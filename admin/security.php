<?php
/**
 * 全站国际级加固安全设置
 * 总开关 + 多层防护（防注入/防暴力破解/防上传/防篡改/防CC-DDOS）
 */
require_once '../includes/common.php';
include_once SYSTEM_ROOT . 'cc_protect.php';

if ($islogin != 1) {
    exit('<script language="javascript">window.location.href="./login.php";</script>');
}
adminpermission('set', 2);

// 初始化数据表
setup_security_tables();

$act = isset($_POST['act']) ? $_POST['act'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($act) {
        case 'save_config':
            $conf_values = array(
                'security_hardening' => isset($_POST['security_hardening']) ? 1 : 0,
                'sec_brute_enabled' => isset($_POST['sec_brute_enabled']) ? 1 : 0,
                'sec_brute_max' => max(1, intval($_POST['sec_brute_max'])),
                'sec_brute_ban_min' => max(1, intval($_POST['sec_brute_ban_min'])),
                'sec_inject_enabled' => isset($_POST['sec_inject_enabled']) ? 1 : 0,
                'sec_upload_enabled' => isset($_POST['sec_upload_enabled']) ? 1 : 0,
                'sec_cc_enabled' => isset($_POST['sec_cc_enabled']) ? 1 : 0,
                'sec_cc_maxreq' => max(10, intval($_POST['sec_cc_maxreq'])),
                'sec_cc_window' => max(5, intval($_POST['sec_cc_window'])),
                'sec_cc_ban_min' => max(1, intval($_POST['sec_cc_ban_min'])),
                'sec_log_enabled' => isset($_POST['sec_log_enabled']) ? 1 : 0,
            );
            foreach ($conf_values as $k => $v) {
                $exists = $DB->getRow("SELECT k FROM pre_config WHERE k=:k LIMIT 1", array(':k' => $k));
                if ($exists) {
                    $DB->query("UPDATE pre_config SET v=:v WHERE k=:k", array(':v' => $v, ':k' => $k));
                } else {
                    $DB->query("INSERT INTO pre_config (k,v) VALUES (:k,:v)", array(':k' => $k, ':v' => $v));
                }
            }
            $CACHE->pre_update();
            exit('{"code":1,"msg":"安全配置保存成功"}');
            break;

        case 'scan_webshell':
            $dir = isset($_POST['dir']) ? trim($_POST['dir']) : ROOT;
            if (!is_dir($dir)) {
                exit('{"code":-1,"msg":"目录不存在"}');
            }
            $found = sec_scan_webshell($dir);
            if (empty($found)) {
                exit('{"code":1,"msg":"未发现可疑文件","count":0}');
            }
            exit('{"code":1,"msg":"发现可疑文件","count":' . count($found) . ',"list":' . json_encode($found, JSON_UNESCAPED_UNICODE) . '}');
            break;

        case 'tamper_build':
            $n = sec_tamper_build();
            exit('{"code":1,"msg":"已生成核心文件校验基线，共 ' . $n . ' 个文件"}');
            break;

        case 'tamper_check':
            $res = sec_tamper_check();
            if ($res['ok']) {
                exit('{"code":1,"msg":"' . (empty($res['msg']) ? '核心文件校验通过，未被篡改' : $res['msg']) . '","changes":[]}');
            }
            $list = array();
            foreach ($res['changes'] as $file => $st) {
                $list[] = $file . ' [' . $st . ']';
            }
            exit('{"code":-1,"msg":"发现核心文件被修改！","changes":' . json_encode($list, JSON_UNESCAPED_UNICODE) . '}');
            break;

        case 'ban_ip':
            $ip = trim($_POST['ip']);
            $reason = trim($_POST['reason']);
            $hours = max(1, intval($_POST['hours']));
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                exit('{"code":-1,"msg":"IP格式不正确"}');
            }
            sec_ban_ip($ip, $reason ? $reason : '后台手动封禁', $hours);
            exit('{"code":1,"msg":"IP ' . $ip . ' 已封禁 ' . $hours . ' 小时"}');
            break;

        case 'unban_ip':
            $ip = trim($_POST['ip']);
            sec_unban_ip($ip);
            exit('{"code":1,"msg":"IP ' . $ip . ' 已解除封禁"}');
            break;

        case 'clean_logs':
            $DB->query("DELETE FROM pre_security_log WHERE log_time < :time", array(':time' => date('Y-m-d H:i:s', time() - 7 * 86400)));
            exit('{"code":1,"msg":"7天前的安全日志已清理"}');
            break;

        case 'clean_bans':
            clean_cc_ban_list();
            exit('{"code":1,"msg":"过期封禁已清理"}');
            break;
    }
    exit();
}

// 刷新配置
$conf = $CACHE->pre_fetch();

// 封禁列表
$ban_list = $DB->getAll("SELECT * FROM pre_cc_ban ORDER BY id DESC LIMIT 30");
// 安全日志
$sec_logs = $DB->getAll("SELECT * FROM pre_security_log ORDER BY id DESC LIMIT 30");
// 登录尝试记录
$login_attempts = $DB->getAll("SELECT * FROM pre_login_attempts WHERE blocked_until>0 ORDER BY blocked_until DESC LIMIT 20");

$title = '安全加固';
include './head.php';
?>
<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">全站国际级加固安全</h3>
        </div>
        <div class="alert alert-info">
            <b>多层防护机制：</b>
            L1 防注入（SQL注入/XSS/命令执行） &nbsp;|&nbsp;
            L2 防暴力破解（登录失败锁定IP） &nbsp;|&nbsp;
            L3 防上传/webshell（危险文件拦截+扫描） &nbsp;|&nbsp;
            L4 防篡改（核心文件hash校验） &nbsp;|&nbsp;
            L5 防CC/DDOS（IP黑名单+请求频率限制+Cookie挑战）
        </div>

        <form id="secForm" class="form-horizontal" role="form">
            <div class="panel panel-default">
                <div class="panel-heading"><h4><label class="switch" style="margin-right:10px"><input type="checkbox" name="security_hardening" id="master_switch" value="1" <?php echo $conf['security_hardening'] ? 'checked' : ''; ?>><span class="slider round"></span></label>国际级加固安全总开关 <small class="text-muted">关闭后以下所有防护不生效</small></h4></div>
                <div class="panel-body">
                    <!-- L1 防注入 -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">L1 防注入强化</label>
                        <div class="col-sm-9">
                            <label class="switch"><input type="checkbox" name="sec_inject_enabled" value="1" <?php echo $conf['sec_inject_enabled'] ? 'checked' : ''; ?>><span class="slider round"></span></label>
                            <small class="help-block">拦截GET/POST/COOKIE中的SQL注入、XSS、命令执行、路径穿越等特征（叠加360webscan）</small>
                        </div>
                    </div>
                    <!-- L2 防暴力破解 -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">L2 防暴力破解</label>
                        <div class="col-sm-9">
                            <label class="switch"><input type="checkbox" name="sec_brute_enabled" value="1" <?php echo $conf['sec_brute_enabled'] ? 'checked' : ''; ?>><span class="slider round"></span></label>
                            <small class="help-block">后台/用户中心/供货商登录失败次数超限后锁定IP</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">失败次数阈值</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-4"><input type="number" class="form-control" name="sec_brute_max" value="<?php echo $conf['sec_brute_max'] ? $conf['sec_brute_max'] : 5; ?>" min="1"><small class="help-block">连续失败次数</small></div>
                                <div class="col-sm-4"><input type="number" class="form-control" name="sec_brute_ban_min" value="<?php echo $conf['sec_brute_ban_min'] ? $conf['sec_brute_ban_min'] : 30; ?>" min="1"><small class="help-block">锁定时间(分钟)</small></div>
                            </div>
                        </div>
                    </div>
                    <!-- L3 防上传 -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">L3 防上传/webshell</label>
                        <div class="col-sm-9">
                            <label class="switch"><input type="checkbox" name="sec_upload_enabled" value="1" <?php echo $conf['sec_upload_enabled'] ? 'checked' : ''; ?>><span class="slider round"></span></label>
                            <small class="help-block">拦截危险扩展名与文件内容特征（含伪装图片的webshell）</small>
                        </div>
                    </div>
                    <!-- L4 防篡改 -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">L4 防篡改</label>
                        <div class="col-sm-9">
                            <span class="label label-<?php echo $conf['sec_tamper_enabled'] ? 'success' : 'default'; ?>">核心文件hash基线校验</span>
                            <button type="button" class="btn btn-primary btn-sm" onclick="tamperBuild()">生成基线</button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="tamperCheck()">立即校验</button>
                            <small class="help-block">对 index.php / includes核心文件 / config.php 生成hash基线，发现被修改立即告警</small>
                        </div>
                    </div>
                    <!-- L5 防CC/DDOS -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">L5 防CC/DDOS</label>
                        <div class="col-sm-9">
                            <label class="switch"><input type="checkbox" name="sec_cc_enabled" value="1" <?php echo $conf['sec_cc_enabled'] ? 'checked' : ''; ?>><span class="slider round"></span></label>
                            <small class="help-block">IP黑名单 + 单IP请求频率限制 + Cookie挑战（可配合<a href="./cc_protect.php">防CC管理</a>）</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">请求频率限制</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-4"><input type="number" class="form-control" name="sec_cc_maxreq" value="<?php echo $conf['sec_cc_maxreq'] ? $conf['sec_cc_maxreq'] : 120; ?>" min="10"><small class="help-block">窗口内最大请求数</small></div>
                                <div class="col-sm-4"><input type="number" class="form-control" name="sec_cc_window" value="<?php echo $conf['sec_cc_window'] ? $conf['sec_cc_window'] : 60; ?>" min="5"><small class="help-block">时间窗口(秒)</small></div>
                                <div class="col-sm-4"><input type="number" class="form-control" name="sec_cc_ban_min" value="<?php echo $conf['sec_cc_ban_min'] ? $conf['sec_cc_ban_min'] : 30; ?>" min="1"><small class="help-block">超限封禁(分钟)</small></div>
                            </div>
                        </div>
                    </div>
                    <!-- 安全日志 -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">安全日志记录</label>
                        <div class="col-sm-9">
                            <label class="switch"><input type="checkbox" name="sec_log_enabled" value="1" <?php echo $conf['sec_log_enabled'] ? 'checked' : ''; ?>><span class="slider round"></span></label>
                            <small class="help-block">记录所有拦截事件（注入/爆破/CC/篡改等）</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="button" class="btn btn-danger btn-lg" onclick="saveSecConfig()">保存安全配置</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- webshell扫描 -->
        <div class="panel panel-default">
            <div class="panel-heading"><h4>webshell 一键扫描</h4></div>
            <div class="panel-body">
                <div class="input-group">
                    <input type="text" class="form-control" id="scan_dir" value="<?php echo ROOT; ?>" placeholder="要扫描的目录">
                    <span class="input-group-btn"><button type="button" class="btn btn-danger" onclick="scanWebshell()">开始扫描</button></span>
                </div>
                <div id="scan_result" style="margin-top:10px;"></div>
            </div>
        </div>

        <!-- 封禁管理 -->
        <div class="panel panel-default">
            <div class="panel-heading"><h4>IP封禁管理 <button type="button" class="btn btn-info btn-xs" onclick="cleanBans()">清理过期封禁</button></h4></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <input type="text" class="form-control" id="ban_ip_input" placeholder="要封禁的IP">
                            <input type="text" class="form-control" id="ban_reason_input" placeholder="封禁原因" value="后台手动封禁">
                            <input type="number" class="form-control" id="ban_hours_input" placeholder="小时" value="24" style="max-width:80px;">
                            <span class="input-group-btn"><button type="button" class="btn btn-danger" onclick="banIP()">封禁</button></span>
                        </div>
                    </div>
                </div>
                <table class="table table-striped" style="margin-top:10px;">
                    <thead><tr><th>IP</th><th>原因</th><th>封禁时间</th><th>过期时间</th><th>操作</th></tr></thead>
                    <tbody>
                    <?php foreach ($ban_list as $b): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['ip']); ?></td>
                        <td><?php echo htmlspecialchars($b['block_reason']); ?></td>
                        <td><?php echo $b['block_time'] ? date('Y-m-d H:i:s', $b['block_time']) : '-'; ?></td>
                        <td><?php echo $b['expire_time'] == 0 ? '永久' : date('Y-m-d H:i:s', $b['expire_time']); ?></td>
                        <td><button class="btn btn-xs btn-primary" onclick="unbanIP('<?php echo htmlspecialchars($b['ip']); ?>')">解除</button></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ban_list)): ?><tr><td colspan="5" class="text-center">暂无封禁</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 登录锁定记录 -->
        <div class="panel panel-default">
            <div class="panel-heading"><h4>登录锁定记录</h4></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead><tr><th>范围</th><th>IP</th><th>账号</th><th>失败次数</th><th>解封时间</th></tr></thead>
                    <tbody>
                    <?php foreach ($login_attempts as $l): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($l['scope']); ?></td>
                        <td><?php echo htmlspecialchars($l['ip']); ?></td>
                        <td><?php echo htmlspecialchars($l['account']); ?></td>
                        <td><?php echo $l['fail_count']; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $l['blocked_until']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($login_attempts)): ?><tr><td colspan="5" class="text-center">暂无锁定记录</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 安全日志 -->
        <div class="panel panel-default">
            <div class="panel-heading"><h4>安全拦截日志 <button type="button" class="btn btn-warning btn-xs" onclick="cleanLogs()">清理7天前</button></h4></div>
            <div class="panel-body">
                <div class="table-responsive">
                <table class="table table-striped table-condensed">
                    <thead><tr><th>级别</th><th>类型</th><th>IP</th><th>详情</th><th>时间</th></tr></thead>
                    <tbody>
                    <?php foreach ($sec_logs as $l): ?>
                    <tr>
                        <td><span class="label label-<?php echo $l['level'] == 'block' ? 'danger' : ($l['level'] == 'critical' ? 'danger' : 'warning'); ?>"><?php echo htmlspecialchars($l['level']); ?></span></td>
                        <td><?php echo htmlspecialchars($l['type']); ?></td>
                        <td><?php echo htmlspecialchars($l['ip']); ?></td>
                        <td style="word-break:break-all;max-width:400px;"><?php echo htmlspecialchars(function_exists('mb_substr') ? mb_substr($l['detail'], 0, 120) : substr($l['detail'], 0, 120)); ?></td>
                        <td><?php echo $l['log_time']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($sec_logs)): ?><tr><td colspan="5" class="text-center">暂无安全日志</td></tr><?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function saveSecConfig() {
    var loading = layer.load(2);
    $.post('security.php', $('#secForm').serialize() + '&act=save_config', function(res) {
        layer.close(loading);
        var data = typeof res == 'object' ? res : JSON.parse(res);
        if (data.code == 1) {
            layer.msg('保存成功', {icon: 1}, function() { location.reload(); });
        } else {
            layer.alert(data.msg, {icon: 0});
        }
    });
}

function scanWebshell() {
    var dir = $('#scan_dir').val();
    var loading = layer.load(2);
    $.post('security.php', {act: 'scan_webshell', dir: dir}, function(res) {
        layer.close(loading);
        var data = typeof res == 'object' ? res : JSON.parse(res);
        if (data.code == 1) {
            if (data.count > 0) {
                var html = '<div class="alert alert-danger">发现 ' + data.count + ' 个可疑文件：</div><table class="table table-bordered"><tr><th>文件</th><th>特征</th><th>大小</th></tr>';
                $.each(data.list, function(i, item) {
                    html += '<tr><td style="word-break:break-all;">' + item.file + '</td><td>' + item.sig + '</td><td>' + item.size + 'B</td></tr>';
                });
                html += '</table>';
                $('#scan_result').html(html);
            } else {
                $('#scan_result').html('<div class="alert alert-success">扫描完成，未发现可疑文件</div>');
            }
        } else {
            layer.alert(data.msg, {icon: 0});
        }
    });
}

function tamperBuild() {
    var loading = layer.load(2);
    $.post('security.php', {act: 'tamper_build'}, function(res) {
        layer.close(loading);
        var data = typeof res == 'object' ? res : JSON.parse(res);
        layer.alert(data.msg, data.code == 1 ? {icon: 1} : {icon: 0});
    });
}

function tamperCheck() {
    var loading = layer.load(2);
    $.post('security.php', {act: 'tamper_check'}, function(res) {
        layer.close(loading);
        var data = typeof res == 'object' ? res : JSON.parse(res);
        if (data.code == 1) {
            layer.alert(data.msg, {icon: 1});
        } else {
            var msg = data.msg + '\n' + (data.changes || []).join('\n');
            layer.alert(msg, {icon: 2});
        }
    });
}

function banIP() {
    var ip = $('#ban_ip_input').val();
    var reason = $('#ban_reason_input').val();
    var hours = $('#ban_hours_input').val();
    if (!ip) { layer.msg('请输入IP', {icon: 2}); return; }
    var loading = layer.load(2);
    $.post('security.php', {act: 'ban_ip', ip: ip, reason: reason, hours: hours}, function(res) {
        layer.close(loading);
        var data = typeof res == 'object' ? res : JSON.parse(res);
        layer.alert(data.msg, {icon: data.code == 1 ? 1 : 0}, function() { location.reload(); });
    });
}

function unbanIP(ip) {
    layer.confirm('确定解除 ' + ip + ' 的封禁？', function() {
        $.post('security.php', {act: 'unban_ip', ip: ip}, function(res) {
            var data = typeof res == 'object' ? res : JSON.parse(res);
            layer.alert(data.msg, {icon: 1}, function() { location.reload(); });
        });
    });
}

function cleanLogs() {
    layer.confirm('确定清理7天前的安全日志？', function() {
        $.post('security.php', {act: 'clean_logs'}, function(res) {
            var data = typeof res == 'object' ? res : JSON.parse(res);
            layer.msg(data.msg, {icon: 1});
        });
    });
}

function cleanBans() {
    $.post('security.php', {act: 'clean_bans'}, function(res) {
        var data = typeof res == 'object' ? res : JSON.parse(res);
        layer.msg(data.msg, {icon: 1}, function() { location.reload(); });
    });
}
</script>

<?php include './foot.php'; ?>
