<?php
include('../includes/common.php');
$title = '优惠券发放规则管理';
include_once('head.php');

if($islogin==1) {
    // 设置 zid，管理员默认为 0
    $zid = 0;
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    // 发放规则列表
    if($action == 'list') {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $pageSize = 20;
        $start = ($page - 1) * $pageSize;
        
        // 查询所有规则，不区分zid，因为优惠券规则是全局的
        $total = $DB->getColumn("SELECT COUNT(*) FROM `shua_coupon_rules`");
        $rules = $DB->getAll("SELECT r.*, c.name as coupon_name FROM `shua_coupon_rules` r LEFT JOIN `shua_coupons` c ON r.cid = c.cid ORDER BY rid DESC");
        
        $scene_text = ['每日签到', '推广链接', '抽奖商品'];
        
        echo '<div class="col-xs-12">
    <div class="block">
        <div class="block-title">
            <h2><strong>优惠券发放规则列表</strong></h2>
            <div class="block-options pull-right">
                <a href="?action=add" class="btn btn-success"><i class="fa fa-plus"></i> 添加发放规则</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>场景类型</th>
                        <th>优惠券</th>
                        <th>参数配置</th>
                        <th>添加时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach($rules as $rule) {
            $params = json_decode($rule['params'], true);
            $params_text = '';
            if($rule['scene'] == 0) {
                $params_text = '连续签到 ' . $params['continue_days'] . ' 天';
            } elseif($rule['scene'] == 1) {
                $params_text = '推广链接注册';
            } elseif($rule['scene'] == 2) {
                $params_text = '抽奖商品';
            }
            
            // 处理添加时间，确保能正确显示
            $add_time = $rule['add_time'];
            // 如果是字符串，直接显示；如果是时间戳，转换后显示
            if(!is_numeric($add_time)) {
                $add_time_display = $add_time;
            } else {
                $add_time_display = date('Y-m-d H:i:s', $add_time);
            }
            
            echo '<tr>
                <td>' . $rule['rid'] . '</td>
                <td>' . $scene_text[$rule['scene']] . '</td>
                <td>' . $rule['coupon_name'] . '</td>
                <td>' . $params_text . '</td>
                <td>' . $add_time_display . '</td>
                <td>
                    <a href="?action=edit&rid=' . $rule['rid'] . '" class="btn btn-xs btn-primary" title="编辑"><i class="fa fa-pencil"></i></a>
                    <a href="?action=delete&rid=' . $rule['rid'] . '" class="btn btn-xs btn-danger" title="删除"><i class="fa fa-times"></i></a>
                </td>
            </tr>';
        }
        
        if(empty($rules)) {
            echo '<tr><td colspan="7" class="text-center">暂无发放规则</td></tr>';
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
    
    // 添加规则
    elseif($action == 'add') {
        $coupons = $DB->getAll("SELECT cid, name FROM `shua_coupons` WHERE zid = ? AND active = 1 ORDER BY cid DESC", [$zid]);
        echo '<div class="col-xs-12">
    <div class="block">
        <div class="block-title">
            <h2><strong>添加优惠券发放规则</strong></h2>
        </div>
        <form action="?action=save" method="post" class="form-horizontal form-bordered">
            <div class="form-group">
                <label class="col-md-3 control-label" for="scene">场景类型</label>
                <div class="col-md-6">
                    <select id="scene" name="scene" class="form-control" required>
                        <option value="0">每日签到</option>
                        <option value="1">推广链接</option>
                        <option value="2">抽奖商品</option>
                    </select>
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
            <div class="form-group" id="sign_params">
                <label class="col-md-3 control-label" for="continue_days">连续签到天数</label>
                <div class="col-md-6">
                    <input type="number" id="continue_days" name="continue_days" class="form-control" placeholder="请输入连续签到天数" min="1">
                </div>
            </div>
            <div class="form-group" id="invite_params" style="display: none;">
                <label class="col-md-3 control-label">推广链接参数</label>
                <div class="col-md-6">
                    <p class="form-control-static">新用户通过推广链接注册后自动发放</p>
                </div>
            </div>
            <div class="form-group" id="lottery_params" style="display: none;">
                <label class="col-md-3 control-label">抽奖商品参数</label>
                <div class="col-md-6">
                    <p class="form-control-static">作为抽奖活动奖品发放</p>
                </div>
            </div>
            <div class="form-group form-actions">
                <div class="col-md-9 col-md-offset-3">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> 保存</button>
                    <button type="button" class="btn btn-default" onclick="window.location.href=\'coupon_rules.php\'">返回</button>
                </div>
            </div>
        </form>
    </div>
</div>';
    
    echo '<script>
$(function() {
    // 场景类型切换
    $("#scene").change(function() {
        var scene = $(this).val();
        $("#sign_params, #invite_params, #lottery_params").hide();
        if(scene == "0") {
            $("#sign_params").show();
        } else if(scene == "1") {
            $("#invite_params").show();
        } else if(scene == "2") {
            $("#lottery_params").show();
        }
    });
});
</script>';
    }
    
    // 编辑规则
    elseif($action == 'edit') {
        $rid = intval($_GET['rid']);
        $rule = $DB->getRow("SELECT * FROM `shua_coupon_rules` WHERE rid = ?", [$rid]);
        
        if(!$rule) {
            showmsg('规则不存在', 'coupon_rules.php');
        }
        
        $params = json_decode($rule['params'], true);
        $coupons = $DB->getAll("SELECT cid, name FROM `shua_coupons` WHERE active = 1 ORDER BY cid DESC");
        
        echo '<div class="col-xs-12">
    <div class="block">
        <div class="block-title">
            <h2><strong>编辑优惠券发放规则</strong></h2>
        </div>
        <form action="?action=save" method="post" class="form-horizontal form-bordered">
            <input type="hidden" name="rid" value="' . $rule['rid'] . '">
            <div class="form-group">
                <label class="col-md-3 control-label" for="scene">场景类型</label>
                <div class="col-md-6">
                    <select id="scene" name="scene" class="form-control" required>
                        <option value="0"' . ($rule['scene'] == 0 ? ' selected' : '') . '>每日签到</option>
                        <option value="1"' . ($rule['scene'] == 1 ? ' selected' : '') . '>推广链接</option>
                        <option value="2"' . ($rule['scene'] == 2 ? ' selected' : '') . '>抽奖商品</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cid">选择优惠券</label>
                <div class="col-md-6">
                    <select id="cid" name="cid" class="form-control" required>
                        <option value="">请选择优惠券</option>';
                        foreach($coupons as $coupon) {
                            echo '<option value="' . $coupon['cid'] . '"' . ($coupon['cid'] == $rule['cid'] ? ' selected' : '') . '>' . htmlspecialchars($coupon['name']) . '</option>';
                        }
                        echo '                    </select>
                </div>
            </div>
            <div class="form-group" id="sign_params"' . ($rule['scene'] != 0 ? ' style="display: none;"' : '') . '>
                <label class="col-md-3 control-label" for="continue_days">连续签到天数</label>
                <div class="col-md-6">
                    <input type="number" id="continue_days" name="continue_days" class="form-control" placeholder="请输入连续签到天数" min="1" value="' . $params['continue_days'] . '">
                </div>
            </div>
            <div class="form-group" id="invite_params"' . ($rule['scene'] != 1 ? ' style="display: none;"' : '') . '>
                <label class="col-md-3 control-label">推广链接参数</label>
                <div class="col-md-6">
                    <p class="form-control-static">新用户通过推广链接注册后自动发放</p>
                </div>
            </div>
            <div class="form-group" id="lottery_params"' . ($rule['scene'] != 2 ? ' style="display: none;"' : '') . '>
                <label class="col-md-3 control-label">抽奖商品参数</label>
                <div class="col-md-6">
                    <p class="form-control-static">作为抽奖活动奖品发放</p>
                </div>
            </div>
            <div class="form-group form-actions">
                <div class="col-md-9 col-md-offset-3">
                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> 保存</button>
                    <button type="button" class="btn btn-default" onclick="window.location.href=\'coupon_rules.php\'">返回</button>
                </div>
            </div>
        </form>
    </div>
</div>';
    
    echo '<script>
$(function() {
    // 场景类型切换
    $("#scene").change(function() {
        var scene = $(this).val();
        $("#sign_params, #invite_params, #lottery_params").hide();
        if(scene == "0") {
            $("#sign_params").show();
        } else if(scene == "1") {
            $("#invite_params").show();
        } else if(scene == "2") {
            $("#lottery_params").show();
        }
    });
});
</script>';
    }
    
    // 保存规则
    elseif($action == 'save') {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $scene = intval($_POST['scene']);
            $cid = intval($_POST['cid']);
            
            // 构建参数
            $params = [];
            if($scene == 0) {
                $params['continue_days'] = intval($_POST['continue_days']);
            }
            
            $params_json = json_encode($params);
            
            // 使用正确的datetime格式保存时间
            $add_time = date('Y-m-d H:i:s');
            
            if(isset($_POST['rid'])) {
                // 编辑
                $rid = intval($_POST['rid']);
                $DB->exec("UPDATE `shua_coupon_rules` SET scene = ?, cid = ?, params = ?, add_time = ? WHERE rid = ?", [$scene, $cid, $params_json, $add_time, $rid]);
            } else {
                // 添加
                $DB->exec("INSERT INTO `shua_coupon_rules` (zid, scene, cid, params, add_time, active) VALUES (?, ?, ?, ?, ?, ?)", [$zid, $scene, $cid, $params_json, $add_time, 1]);
            }
            
            showmsg('规则保存成功', 'coupon_rules.php');
        } else {
            showmsg('非法请求', 'coupon_rules.php');
        }
    }
    
    // 删除规则
    elseif($action == 'delete') {
        $rid = intval($_GET['rid']);
        $DB->exec("DELETE FROM `shua_coupon_rules` WHERE rid = ?", [$rid]);
        showmsg('规则删除成功', 'coupon_rules.php');
    }
} else {
    showmsg('登录失败，可能是密码错误或者账号不存在！', 'login.php');
}

?></body>
</html>