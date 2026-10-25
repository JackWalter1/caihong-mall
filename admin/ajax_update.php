<?php

/**
 * 在线升级 AJAX 接口
 * act=check    检查更新（请求升级服务器的 check.php 对比版本号）
 * act=download 下载升级包到 includes/runtime/（下载地址由服务端二次获取，不接收客户端传入，防SSRF）
 * act=upgrade  自动备份被覆盖的文件 -> 解压覆盖 -> 执行 upgrade.sql -> 清理缓存
 */
include "../includes/common.php";

// 升级服务器地址回退：兼容旧版本未配置 UPDATE_API_URL 的情况
if (!defined('UPDATE_API_URL') || UPDATE_API_URL === '') {
    define('UPDATE_API_URL', 'https://caihong.wnttech.fun/update-server/');
}

// 内置 CSRF Referer 检查（防止 ajax.func.php 未加载导致函数未定义）
if (!function_exists('checkRefererHost')) {
    function checkRefererHost()
    {
        if (!isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER'])) {
            return true;
        }
        $url_arr = parse_url($_SERVER['HTTP_REFERER']);
        $http_host = $_SERVER['HTTP_HOST'];
        if (strpos($http_host, ':')) {
            $http_host = substr($http_host, 0, strpos($http_host, ':'));
        }
        return $url_arr['host'] === $http_host;
    }
}



@header('Content-Type: application/json; charset=UTF-8');

if ($islogin != 1) exit('{"code":-100,"msg":"请先登录管理员账号"}');
if (!checkRefererHost()) exit('{"code":403,"msg":"来源校验失败"}');

$act = isset($_GET['act']) ? daddslashes($_GET['act']) : '';

define('UPD_RUNTIME', SYSTEM_ROOT . 'runtime/');
define('UPD_ZIP', UPD_RUNTIME . 'update_pkg.zip');
define('UPD_EXTRACT', UPD_RUNTIME . 'update_extract/');
// 覆盖文件时永远跳过的文件（防止把数据库配置/运行数据冲掉）
define('UPD_SKIP_FILES', 'config.php,install/install.lock,upgrade.sql,includes/auth.php');
define('UPD_SKIP_DIRS', 'includes/runtime');

/**
 * 请求升级服务器获取最新版本信息
 * 升级服务器 check.php 返回 JSON：
 * {"version":"1.61","name":"6v6云倾城1.61","time":"2026-10-17","changelog":["xx","yy"],"url":"https://.../pkg.zip","md5":"..."}
 */
function upd_fetch_info()
{
    if (!defined('UPDATE_API_URL') || UPDATE_API_URL === '' || strpos(UPDATE_API_URL, 'http') !== 0) {
        return array('code' => -1, 'msg' => '尚未配置升级服务器地址，请编辑 includes/version.php，填写 UPDATE_API_URL 后保存');
    }
    $resp = curl_get(UPDATE_API_URL . 'check.php?ver=' . VERSION);
    if (empty($resp)) {
        return array('code' => -1, 'msg' => '连接升级服务器失败（' . upd_get_curl_error() . '），请检查服务器网络/DNS/防火墙是否能访问升级服务器');
    }
    $info = json_decode($resp, true);
    if (!is_array($info) || empty($info['version'])) {
        return array('code' => -1, 'msg' => '升级服务器返回数据格式不正确（前200字符：' . substr($resp, 0, 200) . '）');
    }
    return array('code' => 0, 'info' => $info);
}


function upd_get_curl_error()
{
    if (!function_exists('curl_init')) return '未安装 curl 扩展';
    $ch = @curl_init(UPDATE_API_URL . 'check.php?ver=' . VERSION);
    if (!$ch) return 'curl_init 失败';
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_exec($ch);
    $no = curl_errno($ch);
    $msg = curl_error($ch);
    curl_close($ch);
    return 'curl ' . $no . ': ' . $msg;
}

function upd_has_update($latest)
{
    return version_compare($latest, VERSION, '>');
}

function upd_skip_file($rel)
{
    $skip = explode(',', UPD_SKIP_FILES);
    if (in_array($rel, $skip)) return true;
    $dirs = explode(',', UPD_SKIP_DIRS);
    foreach ($dirs as $d) {
        if (strpos($rel, $d . '/') === 0 || rtrim($d, '/') === $rel) return true;
    }
    return false;
}

// 递归删除目录（清理临时解压目录用）
function upd_del_tree($dir)
{
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            upd_del_tree($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

// 递归创建目录
function upd_mkdir($dir)
{
    if (is_dir($dir)) return true;
    return @mkdir($dir, 0777, true);
}

/**
 * 递归把 $src 覆盖到 $dst
 * 被覆盖前的旧文件会先复制到 $backupDir 下的相同相对路径
 */
function upd_copy_tree($src, $dst, $backupDir, &$copied, &$backed)
{
    $items = scandir($src);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $s = $src . DIRECTORY_SEPARATOR . $item;
        $d = $dst . DIRECTORY_SEPARATOR . $item;
        $rel = str_replace('\\', '/', substr($s, strlen($src)));
        if (is_dir($s)) {
            if (upd_skip_file(trim($rel, '/'))) continue;
            if (!is_dir($d)) upd_mkdir($d);
            upd_copy_tree($s, $d, $backupDir, $copied, $backed);
        } else {
            if (upd_skip_file($rel)) continue;
            if (is_file($d)) { // 覆盖前备份旧文件
                $b = $backupDir . $rel;
                upd_mkdir(dirname($b));
                @copy($d, $b);
                $backed++;
            }
            if (!is_dir(dirname($d))) upd_mkdir(dirname($d));
            if (@copy($s, $d)) $copied++;
        }
    }
}

// 拆分 SQL 语句（处理注释、字符串内分号）
function upd_split_sql($sql)
{
    $statements = array();
    $buffer = '';
    $len = strlen($sql);
    $inSingle = $inDouble = $inBacktick = $inLine = $inBlock = false;
    for ($i = 0; $i < $len; $i++) {
        $c = $sql[$i];
        $next = ($i + 1 < $len) ? $sql[$i + 1] : '';
        if ($inLine) { if ($c === "\n" || $c === "\r") $inLine = false; continue; }
        if ($inBlock) { if ($c === '*' && $next === '/') { $inBlock = false; $i++; } continue; }
        if ($inSingle) { $buffer .= $c; if ($c === '\\') { $buffer .= $next; $i++; } elseif ($c === "'") $inSingle = false; continue; }
        if ($inDouble) { $buffer .= $c; if ($c === '\\') { $buffer .= $next; $i++; } elseif ($c === '"') $inDouble = false; continue; }
        if ($inBacktick) { $buffer .= $c; if ($c === '`') $inBacktick = false; continue; }
        if ($c === '-' && $next === '-') { $inLine = true; $i++; continue; }
        if ($c === '#') { $inLine = true; continue; }
        if ($c === '/' && $next === '*') { $inBlock = true; $i++; continue; }
        if ($c === "'") { $inSingle = true; $buffer .= $c; continue; }
        if ($c === '"') { $inDouble = true; $buffer .= $c; continue; }
        if ($c === '`') { $inBacktick = true; $buffer .= $c; continue; }
        if ($c === ';') {
            $stmt = trim($buffer);
            if ($stmt !== '') $statements[] = $stmt;
            $buffer = '';
            continue;
        }
        $buffer .= $c;
    }
    $stmt = trim($buffer);
    if ($stmt !== '') $statements[] = $stmt;
    return $statements;
}


// 捕获所有 warning/notice 到日志（尊重 @ 抑制，避免未定义函数导致崩溃）
if (!function_exists('debug_log')) {
    function debug_log($msg) {
        @file_put_contents(SYSTEM_ROOT . 'runtime/upd_debug.log', date('[Y-m-d H:i:s] ') . $msg . "\n", FILE_APPEND);
    }
}
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // 被 @ 抑制的错误不处理
    if (!(error_reporting() & $errno)) return;
    debug_log("PHP WARNING [$errno] $errstr in $errfile:$errline");
});
switch ($act) {

    // ==================== 检查更新 ====================
    case 'check':
        $res = upd_fetch_info();
        if ($res['code'] != 0) exit(json_encode($res, JSON_UNESCAPED_UNICODE));
        $info = $res['info'];
        $latest = $info['version'];
        $hasUpdate = upd_has_update($latest) ? 1 : 0;
        exit(json_encode(array(
            'code' => 0,
            'update' => $hasUpdate,
            'current' => VERSION,
            'version' => $latest,
            'name' => isset($info['name']) ? $info['name'] : ('V' . $latest),
            'time' => isset($info['time']) ? $info['time'] : '',
            'changelog' => isset($info['changelog']) && is_array($info['changelog']) ? $info['changelog'] : array(),
        ), JSON_UNESCAPED_UNICODE));
        break;

    // ==================== 下载升级包 ====================
    case 'download':
        if (ini_get('allow_url_fopen') == 0 && !function_exists('curl_init')) {
            exit('{"code":-1,"msg":"服务器既未启用 curl 也未启用 allow_url_fopen，无法下载升级包"}');
        }
        $res = upd_fetch_info();
        if ($res['code'] != 0) exit(json_encode($res, JSON_UNESCAPED_UNICODE));
        $info = $res['info'];
        $force = isset($_GET['force']) && $_GET['force'] == '1';
        if (!$force && !upd_has_update($info['version'])) {
            exit(json_encode(array('code' => -1, 'msg' => '当前已经是最新版本，无需升级')));
        }
        if (empty($info['url']) || !preg_match('#^https?://#i', $info['url'])) {
            exit('{"code":-1,"msg":"升级服务器未提供有效的升级包下载地址"}');
        }
        if (!is_dir(UPD_RUNTIME)) upd_mkdir(UPD_RUNTIME);

        set_time_limit(0);
        @unlink(UPD_ZIP);
        $fp = @fopen(UPD_ZIP, 'wb');
        if (!$fp) exit('{"code":-1,"msg":"无法写入临时目录 includes/runtime/，请检查目录写入权限"}');
        $ch = curl_init($info['url']);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; caihong-updater)');
        $ok = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);
        if (!$ok || $httpCode != 200) {
            @unlink(UPD_ZIP);
            exit(json_encode(array('code' => -1, 'msg' => '下载升级包失败（HTTP ' . $httpCode . '），请手动下载后上传覆盖')));
        }
        $size = filesize(UPD_ZIP);
        if ($size < 1024) {
            @unlink(UPD_ZIP);
            exit('{"code":-1,"msg":"下载的升级包大小异常，可能服务器返回了错误页面"}');
        }
        // md5 校验
        if (!empty($info['md5']) && md5_file(UPD_ZIP) !== strtolower($info['md5'])) {
            @unlink(UPD_ZIP);
            exit('{"code":-1,"msg":"升级包 MD5 校验失败，文件可能被篡改或下载不完整，已自动删除"}');
        }
        exit(json_encode(array(
            'code' => 0,
            'msg' => '升级包下载完成',
            'size' => round($size / 1024 / 1024, 2),
            'version' => $info['version'],
        ), JSON_UNESCAPED_UNICODE));
        break;

    // ==================== 执行升级（备份->解压覆盖->SQL） ====================
    case 'upgrade':
        if (!class_exists('ZipArchive')) {
            exit('{"code":-1,"msg":"PHP 未安装 zip 扩展（ZipArchive），请在宝塔/PHP设置中启用 zip 扩展后重试"}');
        }
        if (!is_file(UPD_ZIP)) {
            exit('{"code":-1,"msg":"未找到已下载的升级包，请先点击下载升级包"}');
        }
        set_time_limit(0);

        $zip = new ZipArchive();
        if ($zip->open(UPD_ZIP) !== TRUE) {
            exit('{"code":-1,"msg":"升级包无法打开，文件可能已损坏"}');
        }
        // 防止 Zip Slip 路径穿越攻击
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (strpos($name, '..') !== false || $name[0] === '/' || preg_match('#^[a-zA-Z]:#', $name)) {
                $zip->close();
                exit('{"code":-1,"msg":"升级包内存在非法路径文件，已终止升级"}');
            }
        }
        // 解压到临时目录
        if (is_dir(UPD_EXTRACT)) upd_del_tree(UPD_EXTRACT);
        if (!upd_mkdir(UPD_EXTRACT)) {
            $zip->close();
            exit('{"code":-1,"msg":"创建临时解压目录失败，请检查 includes/runtime/ 写入权限"}');
        }
        if (!$zip->extractTo(UPD_EXTRACT)) {
            $zip->close();
            upd_del_tree(UPD_EXTRACT);
            exit('{"code":-1,"msg":"升级包解压失败"}');
        }
        $zip->close();

        // 适配压缩包：如果 zip 里套了一层目录，自动找到实际根目录
        $srcRoot = UPD_EXTRACT;
        $subs = glob(UPD_EXTRACT . '*', GLOB_ONLYDIR);
        $topFiles = glob(UPD_EXTRACT . '*');
        if (count($topFiles) === 1 && count($subs) === 1 && is_dir($subs[0])) {
            $srcRoot = $subs[0] . '/';
        }

        // 备份 + 覆盖
        $backupDir = UPD_RUNTIME . 'update_backup_' . date('Ymd_His') . '/';
        $copied = 0;
        $backed = 0;
        upd_copy_tree($srcRoot, ROOT, $backupDir, $copied, $backed);

        // 执行数据库升级 SQL（如果升级包根目录带 upgrade.sql）
        $sqlDone = 0;
        $sqlFile = $srcRoot . 'upgrade.sql';
        if (is_file($sqlFile)) {
            $sqls = upd_split_sql(file_get_contents($sqlFile));
            foreach ($sqls as $sql) {
                if (trim($sql)) {
                    $DB->exec($sql);
                    $sqlDone++;
                }
            }
        }

        // 清理缓存并删除临时文件
        if (isset($CACHE) && is_object($CACHE)) $CACHE->clear();
        upd_del_tree(UPD_EXTRACT);
        @unlink(UPD_ZIP);

        // 读取覆盖后的新版本号
        $newVersion = '';
        $vp = ROOT . 'includes/version.php';
        if (is_file($vp)) {
            $content = file_get_contents($vp);
            if (preg_match("/define\('VERSION'\s*,\s*'([^']+)'\)/", $content, $m)) $newVersion = $m[1];
        }

        exit(json_encode(array(
            'code' => 0,
            'msg' => '升级完成！共更新 ' . $copied . ' 个文件，备份旧文件 ' . $backed . ' 个' . ($sqlDone > 0 ? '，执行SQL ' . $sqlDone . ' 条' : ''),
            'backup' => 'includes/runtime/' . basename(rtrim($backupDir, '/')),
            'newversion' => $newVersion,
        ), JSON_UNESCAPED_UNICODE));
        break;

    default:
        exit('{"code":-1,"msg":"未知操作"}');
}
