<?php
include('../includes/common.php');
$title = '用户优惠券管理';
include_once('head.php');

// 格式化时间函数，与coupons.php保持一致
function formatTime($time) {
    if (is_null($time) || $time === '') {
        return '-';
    }
    
    $timestamp = strtotime($time);
    if ($timestamp === false || $timestamp <= 0) {
        return '-';
    }
    
    return date('Y-m-d H:i:s', $timestamp);
}

if($islogin==1) {
    // 设置 zid，管理员默认为 0
    $zid = isset($zid) ? $zid : 0;
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    // 检查用户优惠券表，如果没有数据，插入一些示例数据
    if($action == 'list') {
        try {
            // 修复现有无效时间数据
            $DB->exec("UPDATE shua_user_coupons SET get_time = NOW() WHERE get_time IN ('0000-00-00 00:00:00', '0000-00-00')");
            $DB->exec("UPDATE shua_user_coupons SET expire_time = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE expire_time IN ('0000-00-00 00:00:00', '0000-00-00')");
        } catch (Exception $e) {
            // 忽略错误，继续执行
        }
    }
    
    // 用户优惠券列表
    if($action == 'list') {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $pageSize = 100; // 增加到每页显示100条
        $start = ($page - 1) * $pageSize;
        
        // 使用正常的查询，不使用zid条件，确保所有用户优惠券都能显示
        $total = $DB->getColumn("SELECT COUNT(*) FROM `shua_user_coupons` uc LEFT JOIN `shua_coupons` c ON uc.cid = c.cid");
        $user_coupons = $DB->getAll("SELECT uc.*, c.name, c.type, c.value, c.min_amount FROM `shua_user_coupons` uc LEFT JOIN `shua_coupons` c ON uc.cid = c.cid ORDER BY uc.ucid DESC LIMIT " . intval($start) . ", " . intval($pageSize));
        
        $type_text = ['满减券', '折扣券', '固定金额券'];
        $status_text = ['<span class="label label-success">未使用</span>', '<span class="label label-primary">已使用</span>', '<span class="label label-default">已过期</span>'];
        
        echo '<div class="col-xs-12">
    <div class="block">
        <div class="block-title">
            <h2><strong>用户优惠券列表</strong></h2>
            <div class="block-options pull-right">
                <a href="?action=issue" class="btn btn-success"><i class="fa fa-plus"></i> 手动发放优惠券</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>用户ID</th>
                        <th>优惠券名称</th>
                        <th>类型</th>
                        <th>价值</th>
                        <th>使用门槛</th>
                        <th>获取时间</th>
                        <th>过期时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach($user_coupons as $uc) {
            $value_text = $uc['type'] == 1 ? $uc['value'].'折' : '¥'.$uc['value'];
            echo '<tr>
                <td>' . $uc['ucid'] . '</td>
                <td>' . $uc['userid'] . '</td>
                <td>' . $uc['name'] . '</td>
                <td>' . $type_text[$uc['type']] . '</td>
                <td>' . $value_text . '</td>
                <td>¥' . $uc['min_amount'] . '</td>
                <td>' . formatTime($uc['get_time']) . '</td>
                <td>' . formatTime($uc['expire_time']) . '</td>
                <td>' . $status_text[$uc['status']] . '</td>
                <td>
                    <a href="?action=delete&ucid=' . $uc['ucid'] . '" class="btn btn-xs btn-danger" title="删除"><i class="fa fa-times"></i></a>
                </td>
            </tr>';
        }
        
        if(empty($user_coupons)) {
            echo '<tr><td colspan="10" class="text-center">暂无用户优惠券</td></tr>';
        }
        
        echo '</tbody>
            </table>
        </div>
        <div class="pull-right">';
        echo page($page, $pageSize, $total, '?page=');
        echo '</div>
    </div>
</div>';
    }
    
    // 手动发放优惠券
    elseif($action == 'issue') {
        $coupons = $DB->getAll("SELECT cid, name FROM `shua_coupons` WHERE active = 1 ORDER BY cid DESC");
        echo '<div class="col-xs-12">
    <div class="block">
        <div class="block-title">
            <h2><strong>手动发放优惠券</strong></h2>
        </div>
        <form action="?action=do_issue" method="post" class="form-horizontal form-bordered">
            <div class="form-group">
                <label class="col-md-3 control-label" for="userid">用户ID</label>
                <div class="col-md-6">
                    <input type="text" id="userid" name="userid" class="form-control" placeholder="请输入用户ID" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cid">选择优惠券</label>
                <div class="col-md-6">
                    <select id="cid" name="cid" class="form-control" required>
                        <option value="">请选择优惠券</option>';
                        foreach($coupons as $coupon) {
                            echo '<option value="' . $coupon['cid'] . '">' . htmlspecialchars($coupon['name']) . '</option>';
                        }
                        echo '                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="num">发放数量</label>
                <div class="col-md-6">
                    <input type="number" id="num" name="num" class="form-control" placeholder="请输入发放数量" min="1" max="100" value="1" required>
                </div>
            </div>
            <div class="form-group form-actions">
                <div class="col-md-9 col-md-offset-3">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> 发放</button>
                    <button type="button" class="btn btn-default" onclick="window.location.href=\'user_coupons.php\'">返回</button>
                </div>
            </div>
        </form>
    </div>
</div>';
    }
    
    // 执行发放优惠券
    elseif($action == 'do_issue') {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userid = intval($_POST['userid']);
            $cid = intval($_POST['cid']);
            $num = intval($_POST['num']);
            
            $coupon = $DB->getRow("SELECT * FROM `shua_coupons` WHERE cid = ? AND active = 1", [$cid]);
            if(!$coupon) {
                showmsg('优惠券不存在或已禁用', 'user_coupons.php');
            }
            
            // 检查发放总量
            if($coupon['total'] > 0) {
                $issued = $DB->getColumn("SELECT COUNT(*) FROM `shua_user_coupons` WHERE cid = ?", [$cid]);
                if($issued + $num > $coupon['total']) {
                    showmsg('优惠券发放量已达上限', 'user_coupons.php');
                }
            }
            
            // 计算过期时间
            if($coupon['valid_days'] > 0) {
                $expire_time = strtotime('+' . $coupon['valid_days'] . ' days');
            } else {
                $expire_time = strtotime($coupon['end_time']);
            }
            
            // 发放优惠券
            $success = 0;
            $current_time = date('Y-m-d H:i:s');
            $expire_time_formatted = date('Y-m-d H:i:s', $expire_time);
            for($i = 0; $i < $num; $i++) {
                $result = $DB->exec("INSERT INTO `shua_user_coupons` (zid, userid, cid, get_time, expire_time, status) VALUES (?, ?, ?, ?, ?, ?)", [$zid, $userid, $cid, $current_time, $expire_time_formatted, 0]);
                if($result) {
                    $success++;
                }
            }
            
            if($success > 0) {
                showmsg('优惠券发放成功，共发放 ' . $success . ' 张', 'user_coupons.php');
            } else {
                showmsg('优惠券发放失败', 'user_coupons.php');
            }
        } else {
            showmsg('非法请求', 'user_coupons.php');
        }
    }
    
    // 删除用户优惠券
    elseif($action == 'delete') {
        $ucid = intval($_GET['ucid']);
        $DB->exec("DELETE FROM `shua_user_coupons` WHERE ucid = ?", [$ucid]);
        showmsg('优惠券删除成功', 'user_coupons.php');
    }
} else {
    showmsg('登录失败，可能是密码错误或者账号不存在！', 'login.php');
}

?></body>
</html>