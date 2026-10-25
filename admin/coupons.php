<?php
include('../includes/common.php');
$title = '优惠券管理';
include_once('head.php');

if($islogin==1) {
    // 确保 zid 始终有值，管理员默认为 0
    $zid = 0; // 直接设置为0，避免任何可能的未定义问题
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    // 自动创建优惠券表并插入测试数据
    if($action == 'list') {
        try {
            // 创建优惠券表（逐条执行SQL语句）
            $create_tables = array(
                "CREATE TABLE IF NOT EXISTS `shua_coupons` (
                    `cid` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `zid` int(11) unsigned NOT NULL DEFAULT '1',
                    `name` varchar(255) NOT NULL COMMENT '优惠券名称',
                    `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '优惠券类型：0满减券，1折扣券，2固定金额券',
                    `value` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券价值',
                    `min_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用门槛',
                    `max_discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最大优惠金额（仅折扣券）',
                    `total` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发放总量',
                    `used` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已使用数量',
                    `start_time` datetime DEFAULT NULL COMMENT '开始时间',
                    `end_time` datetime DEFAULT NULL COMMENT '结束时间',
                    `valid_days` int(11) NOT NULL DEFAULT '0' COMMENT '有效期天数（0表示固定时间）',
                    `description` text COMMENT '优惠券描述',
                    `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用',
                    `add_time` datetime DEFAULT NULL COMMENT '添加时间',
                    PRIMARY KEY (`cid`),
                    KEY `zid` (`zid`),
                    KEY `active` (`active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券表';",
                
                "CREATE TABLE IF NOT EXISTS `shua_user_coupons` (
                    `ucid` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `zid` int(11) unsigned NOT NULL DEFAULT '1',
                    `cid` int(11) unsigned NOT NULL COMMENT '优惠券ID',
                    `userid` varchar(32) NOT NULL COMMENT '用户ID',
                    `orderid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用订单ID',
                    `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0未使用，1已使用，2已过期',
                    `get_time` datetime DEFAULT NULL COMMENT '获取时间',
                    `use_time` datetime DEFAULT NULL COMMENT '使用时间',
                    `expire_time` datetime DEFAULT NULL COMMENT '过期时间',
                    PRIMARY KEY (`ucid`),
                    KEY `zid` (`zid`),
                    KEY `cid` (`cid`),
                    KEY `userid` (`userid`),
                    KEY `status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户优惠券关联表';",
                
                "CREATE TABLE IF NOT EXISTS `shua_coupon_rules` (
                    `rid` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `zid` int(11) unsigned NOT NULL DEFAULT '1',
                    `scene` tinyint(1) NOT NULL DEFAULT '0' COMMENT '场景：0每日签到，1推广链接，2抽奖商品',
                    `cid` int(11) unsigned NOT NULL COMMENT '优惠券ID',
                    `params` text COMMENT '场景参数（JSON格式）',
                    `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用',
                    `add_time` datetime DEFAULT NULL COMMENT '添加时间',
                    PRIMARY KEY (`rid`),
                    KEY `zid` (`zid`),
                    KEY `scene` (`scene`),
                    KEY `cid` (`cid`),
                    KEY `active` (`active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券发放规则表';",
                
                "CREATE TABLE IF NOT EXISTS `shua_coupon_logs` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `zid` int(11) unsigned NOT NULL DEFAULT '1',
                    `cid` int(11) unsigned NOT NULL COMMENT '优惠券ID',
                    `userid` varchar(32) NOT NULL COMMENT '用户ID',
                    `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型：0发放，1使用，2过期',
                    `reason` varchar(255) DEFAULT NULL COMMENT '原因',
                    `add_time` datetime DEFAULT NULL COMMENT '操作时间',
                    PRIMARY KEY (`id`),
                    KEY `zid` (`zid`),
                    KEY `cid` (`cid`),
                    KEY `userid` (`userid`),
                    KEY `type` (`type`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券发放记录表';"
            );
            
            // 逐条执行SQL语句
            foreach($create_tables as $sql) {
                $DB->exec($sql);
            }
            
            // 检查是否有优惠券数据，如果没有，插入一条测试数据
            // 仅在系统首次安装时执行，通过文件标记判断
            $init_file = __DIR__ . '/coupon_init_flag.txt';
            if(!file_exists($init_file)) {
                $coupon_count = $DB->getColumn("SELECT COUNT(*) FROM `shua_coupons`");
                if($coupon_count == 0) {
                    $DB->exec("INSERT INTO `shua_coupons` (zid, name, type, value, max_discount, min_amount, total, start_time, end_time, valid_days, description, active, add_time) VALUES (0, '测试优惠券', 0, 10, 0, 100, 100, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0, '测试优惠券描述', 1, NOW())");
                }
                // 创建初始化标记文件
                file_put_contents($init_file, '1');
            }
        } catch (Exception $e) {
            // 忽略错误，继续执行
        }
    }
    
    // 优惠券列表
    if($action == 'list') {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $pageSize = 20;
        $start = ($page - 1) * $pageSize;
        
        // 直接使用简单的查询方式，不使用预处理
        $total = $DB->getColumn("SELECT COUNT(*) FROM `shua_coupons`");
        $coupons = $DB->getAll("SELECT * FROM `shua_coupons` ORDER BY cid DESC");
        
        // 先输出状态切换脚本，确保函数在使用前定义
        echo '<script>
        function toggleStatus(cid, status) {
            $.ajax({
                url: "?action=toggle",
                type: "POST",
                data: {
                    cid: cid,
                    status: status
                },
                success: function(data) {
                    location.reload();
                },
                error: function() {
                    alert("状态切换失败，请刷新页面重试");
                }
            });
        }
        </script>';
        
        echo '<div class="col-xs-12">
    <div class="block">
        <div class="block-title">
            <h2><strong>优惠券列表</strong></h2>
            <div class="block-options pull-right">
                <a href="?action=add" class="btn btn-success"><i class="fa fa-plus"></i> 添加优惠券</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>名称</th>
                        <th>类型</th>
                        <th>价值</th>
                        <th>使用门槛</th>
                        <th>发放总量</th>
                        <th>已使用</th>
                        <th>有效期</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>';
        
        // 移除页面底部的重复脚本定义
        $remove_script = 'true';
        
        if(count($coupons) > 0) {
            foreach($coupons as $coupon) {
                $type_text = ['满减券', '折扣券', '固定金额券'][$coupon['type']];
                $status_text = $coupon['active'] ? '<span class="label label-success" onclick="toggleStatus(' . $coupon['cid'] . ', 0)" style="cursor: pointer;">启用</span>' : '<span class="label label-default" onclick="toggleStatus(' . $coupon['cid'] . ', 1)" style="cursor: pointer;">禁用</span>';
                
                // 添加AJAX状态切换脚本
                $status_script = 'true';
                
                // 处理有效期显示，确保日期格式正确
                if($coupon['valid_days'] > 0) {
                    $valid_text = '领取后' . $coupon['valid_days'] . '天有效';
                } else {
                    // 检查日期是否有效
                    $start_date = date('Y-m-d', strtotime($coupon['start_time']));
                    $end_date = date('Y-m-d', strtotime($coupon['end_time']));
                    
                    // 如果日期无效，显示默认值
                    if($start_date == '1970-01-01') $start_date = '';
                    if($end_date == '1970-01-01') $end_date = '';
                    
                    $valid_text = $start_date . ' 至 ' . $end_date;
                }
                
                echo '<tr>
                    <td>' . $coupon['cid'] . '</td>
                    <td>' . $coupon['name'] . '</td>
                    <td>' . $type_text . '</td>
                    <td>' . ($coupon['type'] == 1 ? $coupon['value'].'折' : '¥'.$coupon['value']) . '</td>
                    <td>¥' . $coupon['min_amount'] . '</td>
                    <td>' . ($coupon['total'] == 0 ? '无限' : $coupon['total']) . '</td>
                    <td>' . $coupon['used'] . '</td>
                    <td>' . $valid_text . '</td>
                    <td>' . $status_text . '</td>
                    <td>
                        <a href="?action=edit&cid=' . $coupon['cid'] . '" class="btn btn-xs btn-warning" title="编辑"><i class="fa fa-pencil"></i></a>
                        <a href="?action=delete&cid=' . $coupon['cid'] . '" class="btn btn-xs btn-danger" title="删除"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="10" class="text-center">暂无优惠券</td></tr>';
        }
        
        echo '</tbody>
            </table>
        </div>
        <div class="pull-right">';
        echo page($page, $pageSize, $total, '?page=');
        echo '</div>
    </div>
</div>';
        
        // 删除测试数据脚本
        if(file_exists('test_coupons_db.php')) {
            unlink('test_coupons_db.php');
        }
    }
    
    // 添加优惠券
    elseif($action == 'add') {
        echo '<div class="col-xs-12">
    <div class="block">
        <div class="block-title">
            <h2><strong>添加优惠券</strong></h2>
        </div>
        <form action="?action=save" method="post" class="form-horizontal form-bordered">
            <div class="form-group">
                <label class="col-md-3 control-label" for="name">优惠券名称</label>
                <div class="col-md-6">
                    <input type="text" id="name" name="name" class="form-control" placeholder="请输入优惠券名称" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="type">优惠券类型</label>
                <div class="col-md-6">
                    <select id="type" name="type" class="form-control" required>
                        <option value="0">满减券</option>
                        <option value="1">折扣券</option>
                        <option value="2">固定金额券</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="value">优惠券价值</label>
                <div class="col-md-6">
                    <input type="number" id="value" name="value" class="form-control" placeholder="请输入优惠券价值" step="0.01" min="0" required>
                    <span class="help-block">折扣券请输入小数（如8.5折输入8.5），满减券和固定金额券请输入金额</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="max_discount">最大优惠金额</label>
                <div class="col-md-6">
                    <input type="number" id="max_discount" name="max_discount" class="form-control" placeholder="请输入最大优惠金额（仅折扣券有效）" step="0.01" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="min_amount">使用门槛</label>
                <div class="col-md-6">
                    <input type="number" id="min_amount" name="min_amount" class="form-control" placeholder="请输入使用门槛（0表示无门槛）" step="0.01" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="total">发放总量</label>
                <div class="col-md-6">
                    <input type="number" id="total" name="total" class="form-control" placeholder="请输入发放总量（0表示无限量）" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="valid_type">有效期类型</label>
                <div class="col-md-6">
                    <select id="valid_type" name="valid_type" class="form-control" required>
                        <option value="0">固定时间</option>
                        <option value="1">领取后N天有效</option>
                    </select>
                </div>
            </div>
            <div class="form-group" id="fixed_time">
                <label class="col-md-3 control-label">固定有效期</label>
                <div class="col-md-3">
                    <input type="date" name="start_time" class="form-control" required>
                    <span class="help-block">开始时间</span>
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_time" class="form-control" required>
                    <span class="help-block">结束时间</span>
                </div>
            </div>
            <div class="form-group" id="valid_days" style="display: none;">
                <label class="col-md-3 control-label" for="days">有效期天数</label>
                <div class="col-md-6">
                    <input type="number" id="days" name="days" class="form-control" placeholder="请输入有效期天数" min="1">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="description">优惠券描述</label>
                <div class="col-md-6">
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="请输入优惠券描述"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="active">状态</label>
                <div class="col-md-6">
                    <select id="active" name="active" class="form-control" required>
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>
            <div class="form-group form-actions">
                <div class="col-md-9 col-md-offset-3">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> 保存</button>
                    <button type="button" class="btn btn-default" onclick="window.location.href=\'coupons.php\'">返回</button>
                </div>
            </div>
        </form>
    </div>
</div>';
    
    echo '<script>
$(function() {
    // 有效期类型切换
    $("#valid_type").change(function() {
        if($(this).val() == "0") {
            // 固定时间
            $("#fixed_time").show();
            $("#valid_days").hide();
            // 设置必填属性
            $("#fixed_time input").prop("required", true);
            $("#valid_days input").prop("required", false);
        } else {
            // 领取后N天有效
            $("#fixed_time").hide();
            $("#valid_days").show();
            // 设置必填属性
            $("#fixed_time input").prop("required", false);
            $("#valid_days input").prop("required", true);
        }
    });
    
    // 初始化时设置正确的必填属性
    var initialValidType = $("#valid_type").val();
    if(initialValidType == "0") {
        $("#fixed_time input").prop("required", true);
        $("#valid_days input").prop("required", false);
    } else {
        $("#fixed_time input").prop("required", false);
        $("#valid_days input").prop("required", true);
    }
});
</script>';
    
    // 添加页面脚本修改完成
    }
    
    // 编辑优惠券
    elseif($action == 'edit') {
        $cid = intval($_GET['cid']);
        $coupon = $DB->getRow("SELECT * FROM `shua_coupons` WHERE cid = ? AND zid = ?", [$cid, $zid]);
        
        if(!$coupon) {
            showmsg('优惠券不存在', 'coupons.php');
        }
        
        echo '<div class="col-xs-12">
    <div class="block">
        <div class="block-title">
            <h2><strong>编辑优惠券</strong></h2>
        </div>
        <form action="?action=save" method="post" class="form-horizontal form-bordered">
            <input type="hidden" name="cid" value="' . $coupon['cid'] . '">
            <div class="form-group">
                <label class="col-md-3 control-label" for="name">优惠券名称</label>
                <div class="col-md-6">
                    <input type="text" id="name" name="name" class="form-control" placeholder="请输入优惠券名称" value="' . htmlspecialchars($coupon['name']) . '" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="type">优惠券类型</label>
                <div class="col-md-6">
                    <select id="type" name="type" class="form-control" required>
                        <option value="0"' . ($coupon['type'] == 0 ? ' selected' : '') . '>满减券</option>
                        <option value="1"' . ($coupon['type'] == 1 ? ' selected' : '') . '>折扣券</option>
                        <option value="2"' . ($coupon['type'] == 2 ? ' selected' : '') . '>固定金额券</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="value">优惠券价值</label>
                <div class="col-md-6">
                    <input type="number" id="value" name="value" class="form-control" placeholder="请输入优惠券价值" step="0.01" min="0" value="' . $coupon['value'] . '" required>
                    <span class="help-block">折扣券请输入小数（如8.5折输入8.5），满减券和固定金额券请输入金额</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="max_discount">最大优惠金额</label>
                <div class="col-md-6">
                    <input type="number" id="max_discount" name="max_discount" class="form-control" placeholder="请输入最大优惠金额（仅折扣券有效）" step="0.01" min="0" value="' . $coupon['max_discount'] . '" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="min_amount">使用门槛</label>
                <div class="col-md-6">
                    <input type="number" id="min_amount" name="min_amount" class="form-control" placeholder="请输入使用门槛（0表示无门槛）" step="0.01" min="0" value="' . $coupon['min_amount'] . '" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="total">发放总量</label>
                <div class="col-md-6">
                    <input type="number" id="total" name="total" class="form-control" placeholder="请输入发放总量（0表示无限量）" min="0" value="' . $coupon['total'] . '" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="valid_type">有效期类型</label>
                <div class="col-md-6">
                    <select id="valid_type" name="valid_type" class="form-control" required>
                        <option value="0"' . ($coupon['valid_days'] == 0 ? ' selected' : '') . '>固定时间</option>
                        <option value="1"' . ($coupon['valid_days'] > 0 ? ' selected' : '') . '>领取后N天有效</option>
                    </select>
                </div>
            </div>
            <div class="form-group" id="fixed_time"' . ($coupon['valid_days'] > 0 ? ' style="display: none;"' : '') . '>
                <label class="col-md-3 control-label">固定有效期</label>
                <div class="col-md-3">
                    <input type="date" name="start_time" class="form-control" value="' . (is_numeric($coupon['start_time']) ? date('Y-m-d', $coupon['start_time']) : date('Y-m-d', strtotime($coupon['start_time']))) . '" required>
                    <span class="help-block">开始时间</span>
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_time" class="form-control" value="' . (is_numeric($coupon['end_time']) ? date('Y-m-d', $coupon['end_time']) : date('Y-m-d', strtotime($coupon['end_time']))) . '" required>
                    <span class="help-block">结束时间</span>
                </div>
            </div>
            <div class="form-group" id="valid_days"' . ($coupon['valid_days'] == 0 ? ' style="display: none;"' : '') . '>
                <label class="col-md-3 control-label" for="days">有效期天数</label>
                <div class="col-md-6">
                    <input type="number" id="days" name="days" class="form-control" placeholder="请输入有效期天数" min="1" value="' . $coupon['valid_days'] . '" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="description">优惠券描述</label>
                <div class="col-md-6">
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="请输入优惠券描述">' . htmlspecialchars($coupon['description']) . '</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="active">状态</label>
                <div class="col-md-6">
                    <select id="active" name="active" class="form-control" required>
                        <option value="1"' . ($coupon['active'] == 1 ? ' selected' : '') . '>启用</option>
                        <option value="0"' . ($coupon['active'] == 0 ? ' selected' : '') . '>禁用</option>
                    </select>
                </div>
            </div>
            <div class="form-group form-actions">
                <div class="col-md-9 col-md-offset-3">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> 保存</button>
                    <button type="button" class="btn btn-default" onclick="window.location.href=\'coupons.php\'">返回</button>
                </div>
            </div>
        </form>
    </div>
</div>';
    
    echo '<script>
$(function() {
    // 有效期类型切换
    $("#valid_type").change(function() {
        if($(this).val() == "0") {
            // 固定时间
            $("#fixed_time").show();
            $("#valid_days").hide();
            // 设置必填属性
            $("#fixed_time input").prop("required", true);
            $("#valid_days input").prop("required", false);
        } else {
            // 领取后N天有效
            $("#fixed_time").hide();
            $("#valid_days").show();
            // 设置必填属性
            $("#fixed_time input").prop("required", false);
            $("#valid_days input").prop("required", true);
        }
    });
    
    // 初始化时设置正确的必填属性
    var initialValidType = $("#valid_type").val();
    if(initialValidType == "0") {
        $("#fixed_time input").prop("required", true);
        $("#valid_days input").prop("required", false);
    } else {
        $("#fixed_time input").prop("required", false);
        $("#valid_days input").prop("required", true);
    }
});
</script>';
    
    // 同时确保添加页面也有初始化逻辑
    }
    
    // 保存优惠券
    elseif($action == 'save') {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $type = intval($_POST['type']);
            $value = floatval($_POST['value']);
            $max_discount = floatval($_POST['max_discount']);
            $min_amount = floatval($_POST['min_amount']);
            $total = intval($_POST['total']);
            $valid_type = intval($_POST['valid_type']);
            $description = trim($_POST['description']);
            $active = intval($_POST['active']);
            
            // 处理有效期
            if($valid_type == 0) {
                // 固定时间，使用date函数格式化正确的日期字符串
                $start_time = date('Y-m-d H:i:s', strtotime($_POST['start_time']));
                $end_time = date('Y-m-d H:i:s', strtotime($_POST['end_time']));
                $valid_days = 0;
            } else {
                // 领取后N天有效，使用NULL表示不固定时间
                $start_time = date('Y-m-d H:i:s');
                $end_time = date('Y-m-d H:i:s', strtotime('+365 days'));
                $valid_days = intval($_POST['days']);
            }
            
            if(isset($_POST['cid'])) {
                // 编辑
                $cid = intval($_POST['cid']);
                $DB->exec("UPDATE `shua_coupons` SET name = ?, type = ?, value = ?, max_discount = ?, min_amount = ?, total = ?, start_time = ?, end_time = ?, valid_days = ?, description = ?, active = ?, add_time = ? WHERE cid = ? AND zid = ?", [$name, $type, $value, $max_discount, $min_amount, $total, $start_time, $end_time, $valid_days, $description, $active, date('Y-m-d H:i:s'), $cid, $zid]);
            } else {
                // 添加
                $DB->exec("INSERT INTO `shua_coupons` (zid, name, type, value, max_discount, min_amount, total, start_time, end_time, valid_days, description, active, used, add_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)", [$zid, $name, $type, $value, $max_discount, $min_amount, $total, $start_time, $end_time, $valid_days, $description, $active, date('Y-m-d H:i:s')]);
            }
            
            showmsg('优惠券保存成功', 'coupons.php');
        } else {
            showmsg('非法请求', 'coupons.php');
        }
    }
    
    // 切换优惠券状态
    elseif($action == 'toggle') {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cid = intval($_POST['cid']);
            $status = intval($_POST['status']);
            
            $DB->exec("UPDATE `shua_coupons` SET active = ? WHERE cid = ? AND zid = ?", [$status, $cid, $zid]);
            exit('success');
        } else {
            exit('error');
        }
    }
    
    // 删除优惠券
    elseif($action == 'delete') {
        $cid = intval($_GET['cid']);
        $DB->exec("DELETE FROM `shua_coupons` WHERE cid = ? AND zid = ?", [$cid, $zid]);
        $DB->exec("DELETE FROM `shua_user_coupons` WHERE cid = ? AND zid = ?", [$cid, $zid]);
        $DB->exec("DELETE FROM `shua_coupon_rules` WHERE cid = ? AND zid = ?", [$cid, $zid]);
        showmsg('优惠券删除成功', 'coupons.php');
    }
} else {
    showmsg('登录失败，可能是密码错误或者账号不存在！', 'login.php');
}

?></body>
</html>