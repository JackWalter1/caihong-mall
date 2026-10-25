<?php
/**
 * CC攻击防护管理页面
 * 用于配置和管理CC攻击防护功能
 */
// 防CC攻击管理页面
require_once '../includes/common.php';
include_once SYSTEM_ROOT . 'cc_protect.php';

// 检查权限（管理员权限）
if ($islogin != 1) {
    sysmsg('您没有权限访问此页面');
    exit();
}

// 初始化表（首次访问时）
$cc_log_exists = $DB->query("SHOW TABLES LIKE 'pre_cc_log'");
if(!$cc_log_exists || !$cc_log_exists->fetch()) {
    setup_cc_protect();
}

// 处理POST操作
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch($action) {
        case 'save_config':
            // 保存配置
            $cc_protect_enabled = isset($_POST['cc_protect_enabled']) ? 1 : 0;
            $cc_max_requests = intval($_POST['cc_max_requests']);
            $cc_time_window = intval($_POST['cc_time_window']);
            $cc_ban_time = intval($_POST['cc_ban_time']);
            $cc_redirect_url = trim($_POST['cc_redirect_url']);
            
            // 验证输入
            if($cc_max_requests <= 0) $cc_max_requests = 30;
            if($cc_time_window <= 0) $cc_time_window = 60;
            if($cc_ban_time <= 0) $cc_ban_time = 3600;
            if(!$cc_redirect_url) $cc_redirect_url = 'https://www.baidu.com';
            
            // 保存到数据库（配置键可能不存在，先查后插）
            $save_items = array(
                'cc_protect_enabled' => $cc_protect_enabled,
                'cc_max_requests' => $cc_max_requests,
                'cc_time_window' => $cc_time_window,
                'cc_ban_time' => $cc_ban_time,
                'cc_redirect_url' => $cc_redirect_url,
            );
            foreach ($save_items as $k => $v) {
                $exists = $DB->getRow("SELECT k FROM pre_config WHERE k=:k LIMIT 1", array(':k' => $k));
                if ($exists) {
                    $DB->query("UPDATE pre_config SET v=:v WHERE k=:k", array(':v' => $v, ':k' => $k));
                } else {
                    $DB->query("INSERT INTO pre_config (k,v) VALUES (:k,:v)", array(':k' => $k, ':v' => $v));
                }
            }
            
            // 清除缓存
            $CACHE->pre_update();
            
            exit('{"code":1,"msg":"配置保存成功"}');
            break;
            
        case 'ban_ip':
            // 手动封禁IP
            $ip = trim($_POST['ip']);
            $reason = trim($_POST['reason']);
            $ban_hours = intval($_POST['ban_hours']);
            
            if(!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
                exit('{"code":-1,"msg":"IP格式不正确"}');
            }
            
            if(!$reason) $reason = '手动封禁';
            if($ban_hours <= 0) $ban_hours = 24;
            
            ban_ip_manually($ip, $reason, $ban_hours);
            exit('{"code":1,"msg":"IP封禁成功"}');
            break;
            
        case 'unban_ip':
            // 解除IP封禁
            $ip = trim($_POST['ip']);
            
            if(!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
                exit('{"code":-1,"msg":"IP格式不正确"}');
            }
            
            unban_ip($ip);
            exit('{"code":1,"msg":"IP封禁已解除"}');
            break;
            
        case 'clean_logs':
            // 清理访问日志
            $days = intval($_POST['days']);
            $time = time() - ($days * 86400);
            
            $DB->query("DELETE FROM pre_cc_log WHERE log_time < :time", [':time' => $time]);
            exit('{"code":1,"msg":"访问日志清理成功"}');
            break;
            
        case 'clean_expired':
            // 清理过期封禁
            clean_cc_ban_list();
            exit('{"code":1,"msg":"过期封禁清理成功"}');
            break;
    }
    exit();
}

// 获取配置
$conf = $CACHE->pre_fetch();
$cc_protect_enabled = isset($conf['cc_protect_enabled']) ? $conf['cc_protect_enabled'] : 0;
$cc_max_requests = isset($conf['cc_max_requests']) ? $conf['cc_max_requests'] : 30;
$cc_time_window = isset($conf['cc_time_window']) ? $conf['cc_time_window'] : 60;
$cc_ban_time = isset($conf['cc_ban_time']) ? $conf['cc_ban_time'] : 3600;
$cc_redirect_url = isset($conf['cc_redirect_url']) ? $conf['cc_redirect_url'] : 'https://www.baidu.com';

// 获取统计信息
$stats = get_cc_attack_stats();

// 获取当前封禁列表
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pageSize = 20;
$offset = ($page - 1) * $pageSize;
$total = $DB->getColumn("SELECT COUNT(*) FROM pre_cc_ban WHERE (expire_time > :time OR expire_time = 0)", [':time' => time()]);
$ban_list = $DB->getAll("SELECT * FROM pre_cc_ban WHERE (expire_time > :time OR expire_time = 0) ORDER BY block_time DESC LIMIT :offset, :pageSize", [':time' => time(), ':offset' => $offset, ':pageSize' => $pageSize]);

// 生成分页
$pages = page($total, $pageSize, $page, 'cc_protect.php?page=');

$title = '防CC攻击管理';
include './head.php';
?>
<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title"><h3 class="panel-title">防CC攻击管理</h3></div>
        
        <!-- 功能状态和统计 -->
        <div class="card">
            <div class="card-body">
                <div class="alert alert-<?php echo $cc_protect_enabled ? 'success' : 'warning'; ?>">
                    防CC攻击功能：<?php echo $cc_protect_enabled ? '已启用' : '已禁用'; ?><br>
                    当前封禁IP数：<b><?php echo $stats['current_bans']; ?></b><br>
                    今日拦截次数：<b><?php echo $stats['today_blocks']; ?></b>
                </div>
            </div>
        </div>
        <br>
        
        <!-- 配置表单 -->
        <div class="card">
            <div class="card-header"><h4>防CC配置</h4></div>
            <div class="card-body">
                <form id="ccConfigForm" onsubmit="return saveConfig()" method="post" class="form-horizontal" role="form">
                    <input type="hidden" name="action" value="save_config">
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">启用防CC攻击</label>
                        <div class="col-sm-9">
                            <label class="switch">
                                <input type="checkbox" name="cc_protect_enabled" value="1" <?php echo $cc_protect_enabled ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">最大请求次数</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="cc_max_requests" value="<?php echo $cc_max_requests; ?>" placeholder="时间窗口内允许的最大请求次数" min="1">
                            <small class="help-block">在指定时间窗口内，单个IP允许的最大请求次数</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">时间窗口(秒)</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="cc_time_window" value="<?php echo $cc_time_window; ?>" placeholder="检测时间窗口(秒)" min="1">
                            <small class="help-block">统计请求次数的时间窗口，单位秒</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">封禁时间(秒)</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="cc_ban_time" value="<?php echo $cc_ban_time; ?>" placeholder="封禁时间(秒)" min="1">
                            <small class="help-block">检测到攻击后封禁IP的时间，单位秒</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">重定向URL</label>
                        <div class="col-sm-9">
                            <input type="url" class="form-control" name="cc_redirect_url" value="<?php echo $cc_redirect_url; ?>" placeholder="检测到攻击后重定向的URL">
                            <small class="help-block">当检测到CC攻击时，将恶意IP重定向到这个URL</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">保存配置</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
        
        <!-- 手动封禁IP -->
        <div class="card">
            <div class="card-header"><h4>手动封禁IP</h4></div>
            <div class="card-body">
                <form id="ccBanForm" onsubmit="return banIP()" method="post" class="form-horizontal" role="form">
                    <input type="hidden" name="action" value="ban_ip">
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">IP地址</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="ip" placeholder="要封禁的IP地址" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">封禁原因</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="reason" placeholder="封禁原因" value="手动封禁">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">封禁时长(小时)</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="ban_hours" value="24" placeholder="封禁时长(小时)" min="1">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-danger">立即封禁</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>
        
        <!-- 封禁列表 -->
        <div class="card">
            <div class="card-header"><h4>当前封禁列表</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>IP地址</th>
                                <th>封禁原因</th>
                                <th>封禁时间</th>
                                <th>过期时间</th>
                                <th>访问次数</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ban_list as $ban): ?>
                            <tr>
                                <td><?php echo $ban['ip']; ?></td>
                                <td><?php echo $ban['block_reason']; ?></td>
                                <td><?php echo $ban['block_time']; ?></td>
                                <td><?php echo $ban['expire_time'] == 0 ? '永久' : date('Y-m-d H:i:s', $ban['expire_time']); ?></td>
                                <td><?php echo $ban['access_count']; ?></td>
                                <td>
                                    <button class="btn btn-xs btn-primary" onclick="unbanIP('<?php echo $ban['ip']; ?>')">解除封禁</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($ban_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center">暂无封禁记录</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center"><?php echo $pages; ?></div>
            </div>
        </div>
        <br>
        
        <!-- 维护操作 -->
        <div class="card">
            <div class="card-header"><h4>维护操作</h4></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form id="ccLogForm" onsubmit="return cleanLogs()" method="post" class="form-horizontal" role="form">
                            <input type="hidden" name="action" value="clean_logs">
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">清理日志</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="days" value="7" placeholder="保留最近天数" min="1">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-warning">清理</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-md-6">
                        <form id="ccCleanForm" onsubmit="return cleanExpired()" method="post" class="form-horizontal" role="form">
                            <input type="hidden" name="action" value="clean_expired">
                            
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-info">清理过期封禁</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function saveConfig() {
    if(confirm('确定要保存配置吗？')) {
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: $('#ccConfigForm').serialize(),
            dataType: 'json',
            success: function(data) {
                if(data.code == 1) {
                    layer.alert(data.msg, {icon: 1}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 0});
                }
            },
            error: function() {
                layer.alert('网络错误，请重试', {icon: 0});
            }
        });
    }
    return false;
}

function banIP() {
    if(confirm('确定要封禁此IP吗？')) {
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: $('#ccBanForm').serialize(),
            dataType: 'json',
            success: function(data) {
                if(data.code == 1) {
                    layer.alert(data.msg, {icon: 1}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 0});
                }
            },
            error: function() {
                layer.alert('网络错误，请重试', {icon: 0});
            }
        });
    }
    return false;
}

function unbanIP(ip) {
    if(confirm('确定要解除IP封禁吗？')) {
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: {action: 'unban_ip', ip: ip},
            dataType: 'json',
            success: function(data) {
                if(data.code == 1) {
                    layer.alert(data.msg, {icon: 1}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 0});
                }
            },
            error: function() {
                layer.alert('网络错误，请重试', {icon: 0});
            }
        });
    }
}

function cleanLogs() {
    if(confirm('确定要清理旧日志吗？')) {
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: $('#ccLogForm').serialize(),
            dataType: 'json',
            success: function(data) {
                if(data.code == 1) {
                    layer.alert(data.msg, {icon: 1});
                } else {
                    layer.alert(data.msg, {icon: 0});
                }
            },
            error: function() {
                layer.alert('网络错误，请重试', {icon: 0});
            }
        });
    }
    return false;
}

function cleanExpired() {
    if(confirm('确定要清理过期封禁记录吗？')) {
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: $('#ccCleanForm').serialize(),
            dataType: 'json',
            success: function(data) {
                if(data.code == 1) {
                    layer.alert(data.msg, {icon: 1}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 0});
                }
            },
            error: function() {
                layer.alert('网络错误，请重试', {icon: 0});
            }
        });
    }
    return false;
}
</script>

<?php include './foot.php'; ?>