<?php
// 强制刷新缓存 - " . date('Y-m-d H:i:s') . "
// 临时保存原始错误报告设置
$original_error_reporting = error_reporting();
// 先设置显示所有错误，便于调试
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('opcache.revalidate_freq', 0);
// 定义常量
define('IN_CRONLITE', true);
define('IN_OTHER', true);
define('CACHE_FILE', 0);
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
date_default_timezone_set('Asia/Shanghai');
$date = date("Y-m-d H:i:s");

if (function_exists("set_time_limit"))
{
	@set_time_limit(0);
}
if (function_exists("ignore_user_abort"))
{
	@ignore_user_abort(true);
}

include_once(ROOT."includes/autoloader.php");
Autoloader::register();

require ROOT.'config.php';
//连接数据库
$DB = new \lib\PdoHelper($dbconfig);

$CACHE=new \lib\Cache();
$conf=$CACHE->pre_fetch();
if(empty($conf['version']))$conf=$CACHE->update();
define('SYS_KEY', $conf['syskey']);

// authcode函数已在function.php中定义，无需包含authcode.php文件
// 直接定义authcode变量默认值
$authcode = '';
define('authcode', $authcode);
// 修复Undefined variable: distid错误
// 博客地址：zhonguo.ren | q群915043052
// 教主老师修复：添加distid变量默认值处理
define('DIST_ID',hexdec(isset($distid) ? $distid : '0'));
include ROOT.'includes/function.php';

$scriptpath=str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl = (is_https() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$sitepath.'/';
if(!isset($_SERVER['HTTP_USER_AGENT']) || !strpos($_SERVER['HTTP_USER_AGENT'],chr(46)))$_SERVER['HTTP_USER_AGENT']='Mozilla/5.0 (Windows NT 10.0) Safari/537.36';

include ROOT.'includes/core.func.php';
include ROOT.'includes/member.php';

// ============ 全站国际级加固安全模块（总开关：$conf['security_hardening']） ============
include_once ROOT.'includes/security_hardening.php';
include_once ROOT.'includes/cc_protect.php';
security_hardening_init();

// 修复Undefined index: ip_type错误
$clientip = real_ip(isset($conf['ip_type']) ? $conf['ip_type'] : 0);
$micropayapi = micropay_api();

function showalert($msg,$status,$orderid=null,$tid=0){
	if($tid==-1)$link = '../user/';
	elseif($tid==-2)$link = '../user/regok.php?orderid='.$orderid;
	else {
		// 优先使用session中保存的redirect_url
		session_start();
		if(isset($_SESSION['pay_redirect_url']) && !empty($_SESSION['pay_redirect_url'])){
			$link = '../' . $_SESSION['pay_redirect_url'];
			// 使用后清除session
			unset($_SESSION['pay_redirect_url']);
		} else {
			$link = '../?buyok=1';
		}
	}
	// 移除alert弹窗，直接跳转，避免部分浏览器崩溃
	echo '<meta charset="utf-8"/><script>window.location.href="'.$link.'";</script>';
}

// 恢复原始错误报告设置
// error_reporting($original_error_reporting);
// ini_set('display_errors', 0);

// 调试输出，显示inc.php加载完成
// echo "<p style='color: green;'>✓ inc.php加载完成</p>";