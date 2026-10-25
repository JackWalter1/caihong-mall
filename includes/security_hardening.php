<?php
// ============================================================
// 全站国际级加固安全模块 (Security Hardening)
// 功能：多层防护机制，由后台"安全加固"总开关控制
//   L1 防注入：请求参数高危特征拦截（SQL注入/XSS/命令执行/路径穿越）
//   L2 防暴力破解：登录失败次数限制，超限锁定IP
//   L3 防上传/webshell：危险扩展名+文件内容特征拦截，目录扫描
//   L4 防篡改：核心文件hash基线校验，发现变更告警
//   L5 防CC/DDOS：IP黑名单 + Cookie挑战 + 请求频率限制
// 所有拦截事件记录到安全日志(pre_security_log)
// ============================================================

if (defined('SEC_HARDENING_LOADED')) {
    return;
}
define('SEC_HARDENING_LOADED', true);

// ---------- 配置读取（带默认值） ----------
function sec_conf($key, $default = '')
{
    global $conf;
    return (isset($conf[$key]) && $conf[$key] !== '' && $conf[$key] !== null) ? $conf[$key] : $default;
}

// ---------- 安全日志 ----------
function sec_log($type, $detail, $level = 'warn')
{
    global $DB;
    if (!sec_conf('sec_log_enabled', 1)) {
        return;
    }
    try {
        $DB->query("CREATE TABLE IF NOT EXISTS pre_security_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            level VARCHAR(20) NOT NULL DEFAULT 'warn',
            type VARCHAR(50) NOT NULL,
            ip VARCHAR(50) NOT NULL DEFAULT '',
            detail TEXT,
            log_time DATETIME NOT NULL
        )");
        $ip = function_exists('x_real_ip') ? x_real_ip() : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        $detail_cut = function_exists('mb_substr') ? mb_substr($detail, 0, 2000) : substr($detail, 0, 2000);
        $DB->query("INSERT INTO pre_security_log (level,type,ip,detail,log_time) VALUES (:level,:type,:ip,:detail,:time)",
            array(':level' => $level, ':type' => $type, ':ip' => $ip, ':detail' => $detail_cut, ':time' => date('Y-m-d H:i:s')));
    } catch (\Throwable $e) {
        // 日志失败不影响主流程
    }
}

// ---------- 建表（首次使用时） ----------
function setup_security_tables()
{
    global $DB;
    $DB->query("CREATE TABLE IF NOT EXISTS pre_cc_ban (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip VARCHAR(50) NOT NULL,
        block_reason VARCHAR(255) DEFAULT '',
        block_time INT NOT NULL DEFAULT 0,
        expire_time INT NOT NULL DEFAULT 0,
        access_count INT NOT NULL DEFAULT 0,
        UNIQUE KEY uk_ip (ip)
    )");
    $DB->query("CREATE TABLE IF NOT EXISTS pre_cc_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip VARCHAR(50) NOT NULL,
        url VARCHAR(500) DEFAULT '',
        log_time INT NOT NULL DEFAULT 0
    )");
    $DB->query("CREATE TABLE IF NOT EXISTS pre_login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        scope VARCHAR(30) NOT NULL,
        ip VARCHAR(50) NOT NULL,
        account VARCHAR(100) NOT NULL DEFAULT '',
        fail_count INT NOT NULL DEFAULT 0,
        first_time INT NOT NULL DEFAULT 0,
        blocked_until INT NOT NULL DEFAULT 0,
        UNIQUE KEY uk_scope_ip (scope, ip)
    )");
}

// ---------- L1 防注入：请求参数高危特征检测 ----------
function sec_inject_check()
{
    $patterns = array(
        // SQL 注入特征
        '/\b(union\s+all\s+select|union\s+select|select\s+.*\s+from\s+\w+\s+where|insert\s+into.*values|update\s+\w+\s+set|delete\s+from\s+\w+|drop\s+table|truncate\s+table|alter\s+table)\b/i',
        '/\b(sleep|benchmark|load_file|outfile|dumpfile|information_schema)\s*\(/i',
        '/\b(select|union|insert|update|delete|drop|alter|create)\b[^\w]{0,3}.*\b(select|union|insert|update|delete|drop|alter|create)\b/i',
        // 命令执行特征
        '/\b(eval|system|exec|passthru|shell_exec|proc_open|popen|assert)\s*\(/i',
        '/(\$_(GET|POST|REQUEST|COOKIE|SERVER)\[|\$\{(GET|POST|REQUEST|COOKIE|SERVER)\[)/i',
        // XSS / 脚本注入
        '/<\s*script\b|<script[\s>]|javascript\s*:|vbscript\s*:/i',
        '/on(load|error|click|mouseover|focus|blur)\s*=\s*["\']?[^"\']{2,}/i',
        // 路径穿越 / webshell 特征
        '/\.\.(\/|\\\\)/',
        '/(%00|%0a|%0d)/i',
        // 危险文件扩展名（伪装上传）
        '/\.(php|php3|php4|php5|phtml|pht|shtml|asp|aspx|jsp|cgi|pl|sh)\s*$/i',
    );
    $blocks = array('GET' => $_GET, 'POST' => $_POST, 'COOKIE' => $_COOKIE);
    foreach ($blocks as $src => $arr) {
        if (empty($arr)) {
            continue;
        }
        $flat = array();
        sec_flatten($arr, $flat);
        foreach ($flat as $val) {
            if (strlen($val) > 2) {
                foreach ($patterns as $pat) {
                    if (preg_match($pat, $val)) {
                        sec_log('inject_block', '拦截' . $src . '参数: ' . md5($val) . ' 匹配规则: ' . $pat, 'block');
                        http_response_code(403);
                        header('Content-Type: text/html; charset=UTF-8');
                        exit('<meta charset="utf-8"><div style="padding:40px;text-align:center;font-size:16px;color:#c00;">请求包含非法内容，已被安全系统拦截！</div>');
                    }
                }
            }
        }
    }
}

// 数组递归展开（防多维数组绕过）
function sec_flatten($arr, &$out)
{
    foreach ($arr as $v) {
        if (is_array($v)) {
            sec_flatten($v, $out);
        } else {
            $out[] = (string)$v;
        }
    }
}

// ---------- L5 防CC/DDOS：IP黑名单检查 ----------
function sec_check_ip_ban()
{
    global $DB;
    try {
        $ip = function_exists('x_real_ip') ? x_real_ip() : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        if (!$ip) {
            return;
        }
        $row = $DB->getRow("SELECT * FROM pre_cc_ban WHERE ip=:ip AND (expire_time=0 OR expire_time>:now) LIMIT 1", array(':ip' => $ip, ':now' => time()));
        if ($row) {
            sec_log('ip_ban', 'IP被封禁访问: ' . $ip . ' 原因: ' . $row['block_reason'], 'block');
            http_response_code(403);
            header('Content-Type: text/html; charset=UTF-8');
            exit('<meta charset="utf-8"><div style="padding:40px;text-align:center;font-size:16px;color:#c00;">您的访问已被安全系统限制！</div>');
        }
    } catch (\Throwable $e) {
    }
}

// ---------- L5 防CC/DDOS：请求频率限制（文件计数器） ----------
function sec_request_rate_limit()
{
    if (function_exists('x_real_ip') === false) {
        return;
    }
    $ip = x_real_ip();
    if (!$ip) {
        return;
    }
    $max_req = intval(sec_conf('sec_cc_maxreq', 120));
    $window = intval(sec_conf('sec_cc_window', 60));
    $ban_min = intval(sec_conf('sec_cc_ban_min', 30));
    if ($max_req <= 0 || $window <= 0) {
        return;
    }
    $dir = SYSTEM_ROOT . 'runtime/';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $file = $dir . 'sec_rate_' . md5($ip) . '.dat';
    $now = time();
    $data = array();
    if (is_file($file)) {
        $data = @unserialize(@file_get_contents($file));
        if (!is_array($data)) {
            $data = array();
        }
    }
    // 清理窗口外的记录
    $data = array_values(array_filter($data, function ($t) use ($now, $window) {
        return ($now - $t) < $window;
    }));
    $data[] = $now;
    if (count($data) > $max_req) {
        sec_ban_ip($ip, '请求频率超限（CC/DDOS攻击防护）', $ban_min);
        @unlink($file);
        sec_log('cc_block', 'IP触发CC防护: ' . $ip . ' 窗口内请求数: ' . count($data), 'block');
        http_response_code(429);
        header('Content-Type: text/html; charset=UTF-8');
        exit('<meta charset="utf-8"><div style="padding:40px;text-align:center;font-size:16px;color:#c00;">请求过于频繁，请稍后再试！</div>');
    }
    @file_put_contents($file, serialize($data), LOCK_EX);
}

// ---------- 封禁管理 ----------
function sec_ban_ip($ip, $reason = '安全系统封禁', $hours = 24)
{
    global $DB;
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return false;
    }
    setup_security_tables();
    $expire = $hours > 0 ? (time() + $hours * 3600) : 0;
    $exist = $DB->getRow("SELECT id, access_count FROM pre_cc_ban WHERE ip=:ip LIMIT 1", array(':ip' => $ip));
    if ($exist) {
        $DB->query("UPDATE pre_cc_ban SET block_reason=:reason, block_time=:bt, expire_time=:et, access_count=access_count+1 WHERE id=:id",
            array(':reason' => $reason, ':bt' => time(), ':et' => $expire, ':id' => $exist['id']));
    } else {
        $DB->query("INSERT INTO pre_cc_ban (ip,block_reason,block_time,expire_time) VALUES (:ip,:reason,:bt,:et)",
            array(':ip' => $ip, ':reason' => $reason, ':bt' => time(), ':et' => $expire));
    }
    return true;
}

function sec_unban_ip($ip)
{
    global $DB;
    $DB->query("DELETE FROM pre_cc_ban WHERE ip=:ip", array(':ip' => $ip));
}

// ---------- L2 防暴力破解：登录失败锁定 ----------
// $scope: admin / user / sup
function sec_login_lockout($scope = 'admin')
{
    global $DB;
    $max = intval(sec_conf('sec_brute_max', 5));
    $lock_min = intval(sec_conf('sec_brute_ban_min', 30));
    $ip = function_exists('x_real_ip') ? x_real_ip() : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
    if (!$ip) {
        return array('allow' => true, 'msg' => '');
    }
    try {
        setup_security_tables();
        $row = $DB->getRow("SELECT * FROM pre_login_attempts WHERE scope=:scope AND ip=:ip LIMIT 1", array(':scope' => $scope, ':ip' => $ip));
        if ($row && $row['blocked_until'] > time()) {
            $remain = ceil(($row['blocked_until'] - time()) / 60);
            return array('allow' => false, 'msg' => '密码错误次数过多，账号已锁定，请约 ' . $remain . ' 分钟后再试！');
        }
        if ($row && $row['blocked_until'] > 0 && $row['blocked_until'] <= time()) {
            $DB->query("UPDATE pre_login_attempts SET fail_count=0, blocked_until=0, first_time=:now WHERE id=:id", array(':now' => time(), ':id' => $row['id']));
        }
    } catch (Exception $e) {
    }
    return array('allow' => true, 'msg' => '');
}

function sec_login_fail($scope = 'admin', $account = '')
{
    global $DB;
    $max = intval(sec_conf('sec_brute_max', 5));
    $lock_min = intval(sec_conf('sec_brute_ban_min', 30));
    $ip = function_exists('x_real_ip') ? x_real_ip() : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
    if (!$ip) {
        return;
    }
    try {
        setup_security_tables();
        $row = $DB->getRow("SELECT * FROM pre_login_attempts WHERE scope=:scope AND ip=:ip LIMIT 1", array(':scope' => $scope, ':ip' => $ip));
        $now = time();
        if ($row) {
            $count = $row['fail_count'] + 1;
            if ($count >= $max) {
                $DB->query("UPDATE pre_login_attempts SET fail_count=:c, account=:acct, blocked_until=:bu WHERE id=:id",
                    array(':c' => $count, ':acct' => $account, ':bu' => ($now + $lock_min * 60), ':id' => $row['id']));
                sec_log('brute_block', '登录失败次数超限锁定 [' . $scope . '] IP: ' . $ip . ' 账号: ' . $account . ' 次数: ' . $count, 'block');
            } else {
                $DB->query("UPDATE pre_login_attempts SET fail_count=:c, account=:acct WHERE id=:id",
                    array(':c' => $count, ':acct' => $account, ':id' => $row['id']));
            }
        } else {
            $DB->query("INSERT INTO pre_login_attempts (scope,ip,account,fail_count,first_time,blocked_until) VALUES (:scope,:ip,:acct,:c,:now,0)",
                array(':scope' => $scope, ':ip' => $ip, ':acct' => $account, ':c' => 1, ':now' => $now));
        }
    } catch (\Throwable $e) {
    }
}

function sec_login_clear($scope = 'admin')
{
    global $DB;
    $ip = function_exists('x_real_ip') ? x_real_ip() : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
    if (!$ip) {
        return;
    }
    try {
        $DB->query("DELETE FROM pre_login_attempts WHERE scope=:scope AND ip=:ip", array(':scope' => $scope, ':ip' => $ip));
    } catch (\Throwable $e) {
    }
}

// ---------- L3 防上传/webshell ----------
// 返回 true=通过 false=拦截
function sec_scan_upload($tmp_file, $orig_name)
{
    $bad_ext = array('php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'pht', 'phar', 'shtml', 'shtm', 'asp', 'aspx', 'asa', 'cer', 'cgi', 'pl', 'py', 'sh', 'jsp', 'jspx', 'htaccess', 'user.ini');
    $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
    if (in_array($ext, $bad_ext)) {
        sec_log('upload_block', '拦截危险扩展名上传: ' . $orig_name, 'block');
        return false;
    }
    if (is_file($tmp_file) && filesize($tmp_file) > 0) {
        $head = @file_get_contents($tmp_file, false, null, 0, 8192);
        if ($head !== false) {
            $danger = array('<?php', '<?=', '<?PHP', 'eval(', 'system(', 'assert(', 'passthru(', 'shell_exec(', 'base64_decode(', '$_GET[', '$_POST[', '$_REQUEST[');
            foreach ($danger as $sig) {
                if (stripos($head, $sig) !== false) {
                    sec_log('upload_block', '拦截webshell内容特征: ' . $sig . ' 文件: ' . $orig_name, 'block');
                    return false;
                }
            }
        }
    }
    return true;
}

// 扫描目录下可疑PHP文件（防webshell），返回可疑文件列表
function sec_scan_webshell($dir)
{
    $result = array();
    if (!is_dir($dir)) {
        return $result;
    }
    @set_time_limit(300); // 防止大目录扫描超时
    $danger_sigs = array('eval(', 'assert(', 'shell_exec(', 'passthru(', 'system($_', 'base64_decode($_', 'create_function(', 'preg_replace(\'/.*/e', '$_REQUEST[', '$_POST[');
    try {
        $it = new RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($it as $f) {
            if (!$f->isFile()) {
                continue;
            }
            $ext = strtolower($f->getExtension());
            if (!in_array($ext, array('php', 'phtml', 'php3', 'php4', 'php5', 'pht', 'phar'))) {
                continue;
            }
            $size = $f->getSize();
            if ($size > 0 && $size < 1048576) { // 扫描 <1MB 文件
                $content = @file_get_contents($f->getPathname());
                if ($content === false) {
                    continue;
                }
                foreach ($danger_sigs as $sig) {
                    if (stripos($content, $sig) !== false) {
                        $result[] = array('file' => str_replace('\\', '/', $f->getPathname()), 'sig' => $sig, 'size' => $size);
                        break;
                    }
                }
            }
        }
    } catch (\UnexpectedValueException $e) {
        // 跳过不可读目录，不中断整个扫描
    } catch (\Throwable $e) {
        // 其他异常不中断
    }
    return $result;
}

// ---------- L4 防篡改：核心文件hash基线 ----------
function sec_tamper_baseline_file()
{
    return SYSTEM_ROOT . 'runtime/security_tamper_baseline.dat';
}

function sec_tamper_targets()
{
    $targets = array(
        ROOT . 'index.php',
        SYSTEM_ROOT . 'common.php',
        SYSTEM_ROOT . 'function.php',
        SYSTEM_ROOT . 'core.func.php',
        SYSTEM_ROOT . 'ajax.func.php',
        SYSTEM_ROOT . 'member.php',
        SYSTEM_ROOT . 'base.php',
        ROOT . 'config.php',
    );
    return $targets;
}

function sec_tamper_build()
{
    $baseline = array();
    foreach (sec_tamper_targets() as $f) {
        if (is_file($f)) {
            $baseline[str_replace('\\', '/', $f)] = md5_file($f);
        }
    }
    $dir = SYSTEM_ROOT . 'runtime/';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    @file_put_contents(sec_tamper_baseline_file(), serialize($baseline), LOCK_EX);
    return count($baseline);
}

// 返回: array('ok'=>true) 或 array('ok'=>false,'changes'=>array(文件=>'modified/deleted/new'))
function sec_tamper_check()
{
    $file = sec_tamper_baseline_file();
    if (!is_file($file)) {
        return array('ok' => true, 'changes' => array(), 'msg' => '尚未生成基线');
    }
    $baseline = @unserialize(@file_get_contents($file));
    if (!is_array($baseline)) {
        return array('ok' => true, 'changes' => array(), 'msg' => '基线数据损坏');
    }
    $changes = array();
    foreach (sec_tamper_targets() as $f) {
        $key = str_replace('\\', '/', $f);
        if (is_file($f)) {
            $hash = md5_file($f);
            if (!isset($baseline[$key])) {
                $changes[$key] = 'new';
            } elseif ($baseline[$key] !== $hash) {
                $changes[$key] = 'modified';
            }
        } else {
            if (isset($baseline[$key])) {
                $changes[$key] = 'deleted';
            }
        }
    }
    if (!empty($changes)) {
        sec_log('tamper_alert', '核心文件被修改: ' . json_encode(array_keys($changes), JSON_UNESCAPED_UNICODE), 'critical');
    }
    return array('ok' => empty($changes), 'changes' => $changes, 'msg' => '');
}

// ---------- 总入口：全站请求检查 ----------
function security_hardening_init()
{
    if (sec_conf('security_hardening', 0) != 1) {
        return;
    }
    // 检测到未授权直接进入敏感目录/文件时跳过本页安全初始化
    try {
        // 后台脚本豁免请求频率限制（管理员后台操作频繁，且登录已有防暴力破解）
        $is_admin_script = (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);
        // L5 防CC/DDOS：IP黑名单 + 请求频率限制
        // 同时响应"防CC管理"页面配置(cc_protect_enabled)与安全加固开关(sec_cc_enabled)
        if (sec_conf('sec_cc_enabled', 1) == 1 || sec_conf('cc_protect_enabled', 0) == 1) {
            sec_check_ip_ban();
            if (!$is_admin_script && function_exists('check_spider') && check_spider() == false) {
                sec_request_rate_limit();
            }
        }
        // L1 防注入
        if (sec_conf('sec_inject_enabled', 1) == 1) {
            sec_inject_check();
        }
        // 危险UA拦截（扫描器/爬虫工具）
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        if (!empty($ua)) {
            $bad_ua = array('sqlmap', 'nikto', 'wpscan', 'dirbuster', 'nmap', 'acunetix', 'nessus', 'openvas', 'fimap', 'havij', 'pangolin', 'netsparker', 'appscan');
            foreach ($bad_ua as $b) {
                if (strpos($ua, $b) !== false) {
                    sec_log('ua_block', '拦截危险扫描工具UA: ' . $ua, 'block');
                    http_response_code(403);
                    exit('Access Denied');
                }
            }
        }
    } catch (\Throwable $e) {
    }
}
