<?php
/*
 * 自动补单设置页面
 * 博客：zhonguo.ren
 * QQ群：915043052
 * 开发者：教主
 * 功能：管理自动补单功能，设置补单参数、补单规则等
 */
include("../includes/common.php");

// 自动创建缺少的表
function createMissingTables() {
    global $DB;
    
    try {
        // 检查pre_goods表
        $goods_check = $DB->query("SELECT 1 FROM pre_goods LIMIT 1");
        if ($goods_check === false) {
            // 创建pre_goods表
            $create_goods = $DB->exec("CREATE TABLE IF NOT EXISTS `pre_goods` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `price` decimal(10,2) NOT NULL,
                `stock` int(11) NOT NULL DEFAULT '0',
                `sales` int(11) NOT NULL DEFAULT '0',
                `shequ_id` int(11) NOT NULL DEFAULT '0',
                `shequ_goods_id` varchar(255) NOT NULL DEFAULT '',
                `type` int(11) NOT NULL DEFAULT '0',
                `cid` int(11) NOT NULL DEFAULT '0',
                `active` int(11) NOT NULL DEFAULT '1',
                `addtime` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1");
        }
        
        // 检查pre_orders表是否有reorder_times和last_reorder_time字段
        $orders_check = $DB->query("SELECT reorder_times, last_reorder_time FROM pre_orders LIMIT 1");
        if ($orders_check === false) {
            // 添加reorder_times字段
            $DB->exec("ALTER TABLE pre_orders ADD COLUMN reorder_times int(11) NOT NULL DEFAULT '0'");
            // 添加last_reorder_time字段
            $DB->exec("ALTER TABLE pre_orders ADD COLUMN last_reorder_time datetime DEFAULT NULL");
        }
        
    } catch (Exception $e) {
        // 忽略错误，继续执行
    }
}

// 检查是否是 AJAX 请求
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$is_ajax = $is_ajax || isset($_POST['ajax']) && $_POST['ajax'] == 1;

// 如果是 AJAX 请求，不输出 HTML 头部
if (!$is_ajax) {
    $title='自动补单设置';
    include './head.php';
}

if($islogin==1){}else {
    if ($is_ajax) {
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['code' => 0, 'msg' => '请先登录']);
        exit;
    } else {
        exit("<script language='javascript'>window.location.href='./login.php';</script>");
    }
}

// 执行表创建
createMissingTables();

// 获取当前配置
$autoreorder_config = [];
$config_keys = [
    'autoreorder' => 0,        // 自动补单开关
    'autoreorder_interval' => 5, // 补单间隔（分钟）
    'autoreorder_limit' => 50,   // 每次补单数
    'autoreorder_max_retries' => 3, // 最大重试次数
    'autoreorder_status' => '0,1,2', // 需要补单的订单状态
    'autoreorder_after' => 10,   // 下单多久后开始补单（分钟）
    'autoreorder_timeout' => 30  // 补单超时时间（分钟）
];

foreach($config_keys as $key => $default) {
    $value = $DB->getColumn("SELECT v FROM pre_config WHERE k=:key", [':key' => $key]);
    $autoreorder_config[$key] = $value !== false ? $value : $default;
}

// 处理保存操作
if(isset($_POST['submit'])) {
    $data = [
        'autoreorder' => intval($_POST['autoreorder']),
        'autoreorder_interval' => intval($_POST['autoreorder_interval']),
        'autoreorder_limit' => intval($_POST['autoreorder_limit']),
        'autoreorder_max_retries' => intval($_POST['autoreorder_max_retries']),
        'autoreorder_after' => intval($_POST['autoreorder_after']),
        'autoreorder_timeout' => intval($_POST['autoreorder_timeout'])
    ];
    
    // 处理订单状态数组
    if(isset($_POST['autoreorder_status'])) {
        $data['autoreorder_status'] = implode(',', $_POST['autoreorder_status']);
    } else {
        $data['autoreorder_status'] = '0,1';
    }
    
    // 保存配置
    $success = 0;
    $error = '';
    
    foreach($data as $key => $value) {
        if($DB->exec("REPLACE INTO pre_config SET k=:key, v=:val", [':key' => $key, ':val' => $value])) {
            $success++;
        } else {
            $error .= "保存{$key}失败<br/>";
        }
    }
    
    // 检查是否是 AJAX 请求（同时检查 POST 参数中的 ajax 标记）
    $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    $is_ajax = $is_ajax || isset($_POST['ajax']) && $_POST['ajax'] == 1;
    
    if ($is_ajax) {
        // 清空所有输出缓冲区
        ob_clean();
        // 返回 JSON 格式响应
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        if($error) {
            echo json_encode(['code' => 0, 'msg' => $error]);
        } else {
            echo json_encode(['code' => 1, 'msg' => "成功更新{$success}项配置"]);
        }
        exit;
    } else {
        // 返回 HTML 格式响应
        if($error) {
            showmsg($error, 3);
        } else {
            showmsg("成功更新{$success}项配置", 1);
        }
    }
}

// 获取最近补单记录
$last_reorder_time = $DB->getColumn("SELECT v FROM pre_config WHERE k='last_reorder_time'");
if(!$last_reorder_time) $last_reorder_time = '从未运行';

// 获取补单统计信息
$total_reordered = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE reorder_times > 0");
$today_reordered = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE reorder_times > 0 AND addtime >= CURDATE()");
?>        

<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title"><h3 class="panel-title">自动补单设置</h3></div>
        <div class="card">
            <div class="card-body">
                <form onsubmit="return saveConfig()" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">最近补单时间</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="<?php echo $last_reorder_time?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">补单统计</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stat-box">
                                        <div class="stat-number"><?php echo $total_reordered?></div>
                                        <div class="stat-label">总补单数</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-box">
                                        <div class="stat-number"><?php echo $today_reordered?></div>
                                        <div class="stat-label">今日补单数</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-box">
                                        <div class="stat-number"><a href="javascript:void(0)" onclick="runReorder()" class="btn btn-primary btn-sm">立即补单</a></div>
                                        <div class="stat-label">手动触发</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr/>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">自动补单</label>
                        <div class="col-sm-10">
                            <label class="radio-inline">
                                <input type="radio" name="autoreorder" value="1" <?php if($autoreorder_config['autoreorder'] == 1) echo 'checked';?>> 开启
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="autoreorder" value="0" <?php if($autoreorder_config['autoreorder'] == 0) echo 'checked';?>> 关闭
                            </label>
                            <small class="help-block">开启后将自动执行补单操作</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">补单间隔</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="number" name="autoreorder_interval" value="<?php echo $autoreorder_config['autoreorder_interval']?>" class="form-control" min="1" max="1440" required>
                                <span class="input-group-addon">分钟</span>
                            </div>
                            <small class="help-block">设置两次补单之间的时间间隔，建议设置为5-15分钟</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">每次补单数</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="number" name="autoreorder_limit" value="<?php echo $autoreorder_config['autoreorder_limit']?>" class="form-control" min="1" max="1000" required>
                                <span class="input-group-addon">个/次</span>
                            </div>
                            <small class="help-block">每次补单的最大订单数量，建议根据服务器性能设置</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">最大重试次数</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="number" name="autoreorder_max_retries" value="<?php echo $autoreorder_config['autoreorder_max_retries']?>" class="form-control" min="1" max="10" required>
                                <span class="input-group-addon">次</span>
                            </div>
                            <small class="help-block">单个订单的最大补单次数，超过此次数将不再尝试补单</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">补单延迟</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="number" name="autoreorder_after" value="<?php echo $autoreorder_config['autoreorder_after']?>" class="form-control" min="0" max="1440" required>
                                <span class="input-group-addon">分钟</span>
                            </div>
                            <small class="help-block">订单下单后多久开始尝试补单，建议设置为10分钟以上</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">补单超时</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="number" name="autoreorder_timeout" value="<?php echo $autoreorder_config['autoreorder_timeout']?>" class="form-control" min="5" max="1440" required>
                                <span class="input-group-addon">分钟</span>
                            </div>
                            <small class="help-block">订单下单后超过此时间将不再补单，建议设置为30-60分钟</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">补单订单状态</label>
                        <div class="col-sm-10">
                            <div class="checkbox-group">
                                <?php 
                                $status_array = explode(',', $autoreorder_config['autoreorder_status']);
                                ?>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="autoreorder_status[]" value="0" <?php echo in_array('0', $status_array) ? 'checked' : ''; ?>>
                                    <span>未处理 (0)</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="autoreorder_status[]" value="1" <?php echo in_array('1', $status_array) ? 'checked' : ''; ?>>
                                    <span>处理中 (1)</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="autoreorder_status[]" value="2" <?php echo in_array('2', $status_array) ? 'checked' : ''; ?>>
                                    <span>已完成 (2)</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="autoreorder_status[]" value="3" <?php echo in_array('3', $status_array) ? 'checked' : ''; ?>>
                                    <span>已取消 (3)</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="autoreorder_status[]" value="4" <?php echo in_array('4', $status_array) ? 'checked' : ''; ?>>
                                    <span>已退款 (4)</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="autoreorder_status[]" value="5" <?php echo in_array('5', $status_array) ? 'checked' : ''; ?>>
                                    <span>退款中 (5)</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="autoreorder_status[]" value="djzt2" <?php echo in_array('djzt2', $status_array) ? 'checked' : ''; ?>>
                                    <span>对接失败</span>
                                </label>
                            </div>
                            <small class="help-block">选择哪些状态的订单需要进行自动补单，建议选择未处理、处理中状态</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">计划任务</label>
                        <div class="col-sm-10">
                            <pre>*/5 * * * * curl --silent <?php echo $siteurl?>admin/cron.php</pre>
                            <p>手动执行补单：<a href="<?php echo $siteurl?>admin/cron.php?action=reorder" target="_blank"><?php echo $siteurl?>admin/cron.php?action=reorder</a></p>
                            <small class="help-block">请确保在服务器中设置了计划任务，执行频率建议为5分钟</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" name="submit" value="保存配置" class="btn btn-primary form-control"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">使用说明</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="fa fa-info-circle"></i> 功能说明</h5>
                    <ul>
                        <li>自动补单功能会定期检查符合条件的订单，并尝试重新提交订单以完成交易</li>
                        <li>主要用于处理因各种原因未能正常完成的订单，提高订单成功率</li>
                    </ul>
                </div>
                <div class="alert alert-warning">
                    <h5><i class="fa fa-exclamation-triangle"></i> 注意事项</h5>
                    <ul>
                        <li>请合理设置补单参数，避免过于频繁的补单操作导致对接站点压力过大</li>
                        <li>补单延迟建议设置为10分钟以上，给系统足够时间处理初始订单</li>
                        <li>最大重试次数建议不超过3次，过多的重试可能导致订单重复处理</li>
                        <li>请确保服务器计划任务正确配置，这是自动补单功能正常运行的前提</li>
                    </ul>
                </div>
                <div class="alert alert-success">
                    <h5><i class="fa fa-check-circle"></i> 最佳实践</h5>
                    <ul>
                        <li>补单间隔：5-15分钟</li>
                        <li>每次补单数：30-100个</li>
                        <li>最大重试次数：2-3次</li>
                        <li>补单延迟：10-30分钟</li>
                        <li>补单超时：30-60分钟</li>
                        <li>建议的订单状态：未处理(0)、处理中(1)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div></div>

<!-- 引入layer.js库 -->
<script src="//lib.baomitu.com/layer/3.1.1/layer.js"></script>
<script>
function saveConfig() {
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : window.location.href,
        data : $("form").serialize() + '&submit=1&ajax=1',
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 1){
                layer.alert(data.msg, {icon: 1}, function(){
                    window.location.reload();
                });
            }else{
                layer.alert(data.msg, {icon: 2});
            }
        },
        error:function(xhr, status, error){            layer.close(ii);            layer.msg('服务器错误: ' + error);            console.log('AJAX错误:', status, error);            console.log('响应内容:', xhr.responseText);            $("input[type='submit']").attr("disabled",false);        }
    });
    return false;
}

function runReorder() {
    layer.confirm('确定要立即执行补单操作吗？', {
        btn: ['确定', '取消']
    }, function() {
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.get("./cron.php?action=reorder", function(data) {
            layer.close(ii);
            try {
                var res = JSON.parse(data);
                if(res.code == 1) {
                    layer.msg(res.msg, {icon: 1});
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    layer.msg(res.msg, {icon: 2});
                }
            } catch(e) {
                layer.msg('执行结果解析失败', {icon: 2});
            }
        }).fail(function() {
            layer.close(ii);
            layer.msg('请求失败，请检查网络连接', {icon: 2});
        });
    });
}
</script>

<style>
.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.checkbox-item {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 5px 10px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    transition: all 0.3s;
}

.checkbox-item:hover {
    border-color: #4285f4;
    background: #f8f9ff;
}

.checkbox-item input {
    margin-right: 5px;
}

.stat-box {
    background: #f8f9ff;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    border: 1px solid #e0e0e0;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #4285f4;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 14px;
}
</style>

<?php
// 如果不是 AJAX 请求，才输出 foot.php
if (!$is_ajax) {
    include './foot.php';
}
?>