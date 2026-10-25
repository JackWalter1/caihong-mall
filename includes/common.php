<?php

error_reporting(0);
if (defined('IN_CRONLITE')) {
	return;
}
define('CACHE_FILE', 0);
define('IN_CRONLITE', true);
define('tingdong', '3530793519');
define('SYSTEM_ROOT', dirname(__FILE__) . '/');
define('ROOT', dirname(SYSTEM_ROOT) . '/');
define('TEMPLATE_ROOT', ROOT . 'template/');
define('PLUGIN_ROOT', ROOT . 'includes/plugins/');
// 版本号定义（VERSION / DB_VERSION）
include_once SYSTEM_ROOT . 'version.php';

date_default_timezone_set('Asia/Shanghai');
$date = date("Y-m-d H:i:s");
include_once SYSTEM_ROOT . 'base.php';
@header('Cache-Control: no-store, no-cache, must-revalidate');
@header('Pragma: no-cache');
// 优化Session配置
ini_set('session.gc_maxlifetime', 259200); // 3天
ini_set('session.cookie_lifetime', 259200); // 3天
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
// 修复 open_basedir 限制 session.save_path 导致的 500 错误
if (!empty($_SERVER['SCRIPT_FILENAME']) && strpos($_SERVER['SCRIPT_FILENAME'], 'admin') !== false) {
    @ini_set('session.save_path', '/tmp');
}
session_start();
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
include_once SYSTEM_ROOT . "autoloader.php";
Autoloader::register();
if ($is_defend == true || CC_Defender == 3) {
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
		include_once SYSTEM_ROOT . 'txprotect.php';
	}
	if (CC_Defender == 1 && check_spider() == false) {
	}
	if (CC_Defender == 1 && check_spider() == false || CC_Defender == 3) {
		cc_defender();
	}
}
$scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl = ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $sitepath . '/';
if (is_file(SYSTEM_ROOT . '360safe/360webscan.php')) {
	require_once SYSTEM_ROOT . '360safe/360webscan.php';
}
require_once SYSTEM_ROOT . '360safe/xss.php';
require ROOT . 'config.php';
define('DBQZ', $dbconfig['dbqz']);

function checkDatabaseError($dbconfig, $exception = null)
{
	$error_info = array();
	
	if (!defined('SQLITE') && (!$dbconfig['user'] || !$dbconfig['pwd'] || !$dbconfig['dbname'])) {
		$error_info[] = '数据库配置信息不完整，请检查config.php文件中的数据库配置';
		if (!$dbconfig['user']) {
			$error_info[] = '缺少数据库用户名';
		}
		if (!$dbconfig['pwd']) {
			$error_info[] = '缺少数据库密码';
		}
		if (!$dbconfig['dbname']) {
			$error_info[] = '缺少数据库名称';
		}
		return $error_info;
	}
	
	if ($exception) {
		$error_msg = $exception->getMessage();
		
		if (strpos($error_msg, 'Access denied') !== false || strpos($error_msg, '1045') !== false) {
			$error_info[] = '数据库账号或密码错误';
			$error_info[] = '请检查config.php文件中的数据库用户名和密码是否正确';
		} elseif (strpos($error_msg, 'Unknown database') !== false || strpos($error_msg, '1049') !== false) {
			$error_info[] = '数据库不存在';
			$error_info[] = '请检查config.php文件中的数据库名称是否正确';
		} elseif (strpos($error_msg, 'Connection refused') !== false || strpos($error_msg, '2002') !== false || strpos($error_msg, "Can't connect to MySQL server") !== false || strpos($error_msg, '2003') !== false) {
			$mysql_service_status = checkMySQLService($dbconfig);
			if ($mysql_service_status === 'running') {
				$error_info[] = 'MySQL服务已启动，但无法连接';
				$error_info[] = '请检查config.php文件中的数据库主机地址和端口是否正确';
			} elseif ($mysql_service_status === 'not_running') {
				$error_info[] = 'MySQL服务未启动';
				$error_info[] = '请前往宝塔面板重启或开启MySQL数据库服务';
				$error_info[] = '宝塔面板 -> 软件商店 -> MySQL -> 设置 -> 重启/启动';
			} elseif ($mysql_service_status === 'not_installed') {
				$error_info[] = 'MySQL服务未安装或数据库没开启';
				$error_info[] = '请前往宝塔面板安装或开启MySQL数据库';
				$error_info[] = '宝塔面板 -> 软件商店 -> 搜索MySQL -> 安装/开启';
			} else {
				$error_info[] = '无法连接到MySQL服务器';
				$error_info[] = '请检查MySQL服务是否已启动';
				$error_info[] = '请检查config.php文件中的数据库主机地址和端口是否正确';
			}
		} elseif (strpos($error_msg, 'Table') !== false && strpos($error_msg, "doesn't exist") !== false) {
			$error_info[] = '数据库表不存在';
			$error_info[] = '请检查数据库是否已正确安装';
			$error_info[] = '如果直接覆盖新版本，请修改config.php配置后访问，不要重新安装';
		} else {
			$error_info[] = '数据库连接失败：' . $error_msg;
			$error_info[] = '请检查MySQL服务是否正常运行';
			$error_info[] = '请检查config.php文件中的数据库配置是否正确';
		}
	} else {
		$error_info[] = '数据库连接失败';
		$error_info[] = '请检查MySQL服务是否已启动';
		$error_info[] = '请检查config.php文件中的数据库配置是否正确';
	}
	
	return $error_info;
}

function checkMySQLService($dbconfig)
{
	$host = $dbconfig['host'];
	$port = isset($dbconfig['port']) ? $dbconfig['port'] : 3306;
	
	if ($host == 'localhost' || $host == '127.0.0.1') {
		$os = strtoupper(substr(PHP_OS, 0, 3));
		
		if ($os === 'WIN') {
			$port_check = @fsockopen($host, $port, $errno, $errstr, 2);
			if ($port_check) {
				fclose($port_check);
				return 'running';
			}
			
			$service_names = array('MySQL', 'MySQL80', 'MySQL57', 'MySQL56', 'mariadb');
			foreach ($service_names as $service_name) {
				@exec('sc query "' . $service_name . '" 2>&1', $output, $return_var);
				if (is_array($output) && count($output) > 0) {
					$output_str = implode(' ', $output);
					if (strpos($output_str, 'RUNNING') !== false) {
						return 'running';
					} elseif (strpos($output_str, 'STOPPED') !== false || strpos($output_str, '1060') !== false) {
						return 'not_running';
					}
				}
			}
			return 'not_installed';
		} else {
			$port_check = @fsockopen($host, $port, $errno, $errstr, 2);
			if ($port_check) {
				fclose($port_check);
				return 'running';
			}
			
			@exec('systemctl is-active mysql 2>&1', $output, $return_var);
			if (isset($output[0]) && $output[0] == 'active') {
				return 'running';
			}
			
			@exec('systemctl is-active mariadb 2>&1', $output, $return_var);
			if (isset($output[0]) && $output[0] == 'active') {
				return 'running';
			}
			
			@exec('service mysql status 2>&1', $output, $return_var);
			if (is_array($output) && count($output) > 0) {
				$output_str = implode(' ', $output);
				if (strpos($output_str, 'running') !== false || strpos($output_str, 'active') !== false) {
					return 'running';
				}
			}
			
			return 'not_running';
		}
	} else {
		$port_check = @fsockopen($host, $port, $errno, $errstr, 2);
		if ($port_check) {
			fclose($port_check);
			return 'running';
		}
		return 'not_running';
	}
}

function showDatabaseError($error_info)
{
	$tips_html = '';
	foreach ($error_info as $tip) {
		$tips_html .= '<div>' . htmlspecialchars($tip) . '</div>';
	}
	
	$install_lock_exists = file_exists(ROOT . 'install/install.lock');
	
	if ($install_lock_exists) {
		$tips_html .= '<div style="color:red;margin-top:15px;padding-top:15px;border-top:1px solid #eee;"> 检测到install.lock文件存在，说明系统已安装过。<br> 诺第一次安装请删掉install\install.lock</div>';
		$tips_html .= '<div style="color:red;">请直接修改网站根目录config.php文件内的数据库配置</div>';
		$tips_html .= '<div style="color:red;">不要点击"立即安装"按钮，否则会清空现有数据！</div>';
		$btn_html = '<a href="/" class="btn">返回首页</a>';
	} else {
		$tips_html .= '<div style="margin-top:15px;padding-top:15px;border-top:1px solid #eee;">如果数据库配置正确但仍无法连接，请检查MySQL服务是否正常运行</div>';
		$btn_html = '<a href="/install/" class="btn">立即安装</a>';
	}
	
	header('Content-type:text/html;charset=utf-8');
	echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>数据库连接失败</title><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:Arial,sans-serif;background:#f5f5f5;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}.container{background:#fff;border-radius:8px;padding:40px;max-width:600px;width:100%;box-shadow:0 2px 10px rgba(0,0,0,0.1)}h1{color:#333;font-size:24px;margin-bottom:20px;text-align:center}.tips{color:#666;font-size:14px;line-height:1.8;margin-bottom:30px}.tips div{margin-bottom:10px}.btn{display:block;width:100%;padding:12px;background:#12b7f5;color:#fff;text-decoration:none;text-align:center;border-radius:4px;font-size:16px;transition:background 0.3s}.btn:hover{background:#0a96d8}</style></head><body><div class="container"><h1>数据库连接失败</h1><div class="tips">' . $tips_html . '</div>' . $btn_html . '</div></body></html>';
	exit;
}

if (!defined('SQLITE') && !$dbconfig['user'] || !$dbconfig['pwd'] || !$dbconfig['dbname']) {
	$error_info = checkDatabaseError($dbconfig);
	showDatabaseError($error_info);
}
try {
	$DB = new \lib\PdoHelper($dbconfig);
	if ($DB->query("select * from pre_config where 1") == FALSE) {
		$error_info = array(
			'数据库表不存在或查询失败',
			'请检查数据库是否已正确安装',
			'如果直接覆盖新版本，请修改config.php配置后访问，不要重新安装'
		);
		showDatabaseError($error_info);
	}
} catch (\Exception $e) {
	$error_info = checkDatabaseError($dbconfig, $e);
	showDatabaseError($error_info);
}
$CACHE = new \lib\Cache();
$conf = $CACHE->pre_fetch();
// 默认浏览器图标（favicon）
if (empty($conf['default_ico_url'])) { $conf['default_ico_url'] = 'assets/img/favicon/favicon.ico'; }
define('SYS_KEY', $conf['syskey']);

// 脱敏错误函数：生产环境隐藏数据库错误详情，避免敏感信息泄露
// 自动检测当前脚本是否在admin目录下运行
$_is_admin = (strpos($_SERVER['SCRIPT_FILENAME'], DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR) !== false);
function dbErrorMsg($dbError = '') {
    global $_is_admin;
    if ($_is_admin) {
        return $dbError ? ' 错误信息：' . $dbError : '';
    }
    return '系统繁忙，请稍后再试';
}

if ($conf['qqjump'] == 1 && (strpos($_SERVER['HTTP_USER_AGENT'], 'QQ/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)) {
    if ($_GET['open'] == 1 && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		header('Content-Disposition: attachment; filename="load.doc"');
		header('Content-Type: application/vnd.ms-word;charset=utf-8');
	} else {
		header('Content-type:text/html;charset=utf-8');
	}
	include ROOT . 'template/default/jump.php';
	exit(0);
}
$password_hash = $conf['syskey'];
include_once SYSTEM_ROOT . "function.php";
include_once SYSTEM_ROOT . "core.func.php";
include_once SYSTEM_ROOT . "ajax.func.php";
include_once SYSTEM_ROOT . "member.php";

if (!file_exists(ROOT . 'install/install.lock') && file_exists(ROOT . 'install/index.php')) {
	sysmsg('<h2>检测到无 install.lock 文件</h2><ul><li><font size="4">如果您尚未安装本程序，请<a href="/install/">前往安装</a></font></li><li><font size="4">如果您已经安装本程序，请手动放置一个空的 install.lock 文件到 /install 文件夹下，<b>为了您站点安全，在您完成它之前我们不会工作。</b></font></li></ul><br/><h4>为什么必须建立 install.lock 文件？</h4>它是安装保护文件，如果检测不到它，就会认为站点还没安装，此时任何人都可以安装/重装你的网站。<br/><br/>');
	exit;
}

// ============ 全站国际级加固安全模块（总开关：$conf['security_hardening']） ============
include_once SYSTEM_ROOT . 'security_hardening.php';
include_once SYSTEM_ROOT . 'cc_protect.php';
security_hardening_init();


$cookiesid = $_COOKIE['mysid'];
if (!$cookiesid || !preg_match('/^[0-9a-z]{32}$/i', $cookiesid)) {
	$cookiesid = md5(uniqid(mt_rand(), 1) . time());
	setcookie('mysid', $cookiesid, time() + 604800, '/');
}
if (isset($_COOKIE['invite'])) {
	$invite_id = intval($_COOKIE['invite']);
}
$domain = addslashes($_SERVER['HTTP_HOST']);
$siterow = $DB->getRow("SELECT * FROM pre_site WHERE domain=:domain OR domain2=:domain OR domain3=:domain OR domain4=:domain OR domain5=:domain OR domain6=:domain LIMIT 1", array(':domain' => $domain));
if ($siterow && $siterow['status'] == 1) {
	$is_fenzhan = true;
	if ($siterow['template'] === null || $conf['fenzhan_template'] == 0) {
		$siterow['template'] = $conf['template'];
	}
	$conf = array_merge($conf, $siterow);
	$conf['kfqq'] = $conf['qq'];
} else {
	$is_fenzhan = false;
}

// 加载前台联系方式列表（来源：后台联系方式管理 shua_contact 表）
$contactlist = array();
$contact_rs = @$DB->query("SELECT * FROM pre_contact ORDER BY id ASC");
if ($contact_rs !== false) {
	while ($contact_row = $contact_rs->fetch()) {
		$contactlist[] = $contact_row;
	}
}

function x_real_ip()
{
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all("#\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}#s", $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] as $xip) {
			if (!preg_match("#^(10|172\\.16|192\\.168)\\.#", $xip)) {
				$ip = $xip;
			}
		}
	} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	} elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match("/^([0-9]{1,3}\\.){3}[0-9]{1,3}\$/", $_SERVER['HTTP_X_REAL_IP'])) {
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	}
	return $ip;
}

function check_spider()
{
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if (strpos($useragent, 'baiduspider') !== false) {
		return 'baiduspider';
	}
	if (strpos($useragent, 'googlebot') !== false) {
		return 'googlebot';
	}
	if (strpos($useragent, '360spider') !== false) {
		return '360spider';
	}
	if (strpos($useragent, 'soso') !== false) {
		return 'soso';
	}
	if (strpos($useragent, 'bing') !== false) {
		return 'bing';
	}
	if (strpos($useragent, 'yahoo') !== false) {
		return 'yahoo';
	}
	if (strpos($useragent, 'sohu-search') !== false) {
		return 'Sohubot';
	}
	if (strpos($useragent, 'sogou') !== false) {
		return 'sogou';
	}
	if (strpos($useragent, 'youdaobot') !== false) {
		return 'YoudaoBot';
	}
	if (strpos($useragent, 'robozilla') !== false) {
		return 'Robozilla';
	}
	if (strpos($useragent, 'msnbot') !== false) {
		return 'msnbot';
	}
	if (strpos($useragent, 'lycos') !== false) {
		return 'Lycos';
	}
	if (strpos($useragent, 'ia_archiver') !== false) {
		return 'alexa';
	}
	if (strpos($useragent, 'iaarchiver') !== false) {
		return 'alexa';
	}
	if (strpos($useragent, 'archive.org_bot') !== false) {
		return 'Archive';
	}
	if (strpos($useragent, 'sitebot') !== false) {
		return 'SiteBot';
	}
	if (strpos($useragent, 'gosospider') !== false) {
		return 'gosospider';
	}
	if (strpos($useragent, 'gigabot') !== false) {
		return 'Gigabot';
	}
	if (strpos($useragent, 'yrspider') !== false) {
		return 'YRSpider';
	}
	if (strpos($useragent, 'gigabot') !== false) {
		return 'Gigabot';
	}
	if (strpos($useragent, 'wangidspider') !== false) {
		return 'WangIDSpider';
	}
	if (strpos($useragent, 'foxspider') !== false) {
		return 'FoxSpider';
	}
	if (strpos($useragent, 'docomo') !== false) {
		return 'DoCoMo';
	}
	if (strpos($useragent, 'yandexbot') !== false) {
		return 'YandexBot';
	}
	if (strpos($useragent, 'sinaweibobot') !== false) {
		return 'SinaWeiboBot';
	}
	if (strpos($useragent, 'catchbot') !== false) {
		return 'CatchBot';
	}
	if (strpos($useragent, 'surveybot') !== false) {
		return 'SurveyBot';
	}
	if (strpos($useragent, 'dotbot') !== false) {
		return 'DotBot';
	}
	if (strpos($useragent, 'purebot') !== false) {
		return 'Purebot';
	}
	if (strpos($useragent, 'ccbot') !== false) {
		return 'CCBot';
	}
	if (strpos($useragent, 'mlbot') !== false) {
		return 'MLBot';
	}
	if (strpos($useragent, 'adsbot-google') !== false) {
		return 'AdsBot-Google';
	}
	if (strpos($useragent, 'ahrefsbot') !== false) {
		return 'AhrefsBot';
	}
	if (strpos($useragent, 'spbot') !== false) {
		return 'spbot';
	}
	if (strpos($useragent, 'augustbot') !== false) {
		return 'AugustBot';
	}
	return false;
}

function cc_defender()
{
	$iptoken = md5(x_real_ip() . date('Ymd')) . md5(time() . rand(11111, 99999));
	if (!isset($_COOKIE['sec_defend']) || substr($_COOKIE['sec_defend'], 0, 32) !== substr($iptoken, 0, 32)) {
		if (!$_COOKIE['sec_defend_time']) {
			$_COOKIE['sec_defend_time'] = 0;
		}
		$x = new \lib\hieroglyphy();
		$setCookie = $x->hieroglyphyString($iptoken);
		$sec_defend_time = $_COOKIE['sec_defend_time'] + 1;
		header('Content-type:text/html;charset=utf-8');
		if ($sec_defend_time >= 10) {
			exit('浏览器不支持COOKIE或者不正常访问！');
		}
		echo '<html><head><meta http-equiv="pragma" content="no-cache"><meta http-equiv="cache-control" content="no-cache"><meta http-equiv="content-type" content="text/html;charset=utf-8"><title>正在加载中</title><script>function setCookie(name,value){var exp = new Date();exp.setTime(exp.getTime() + 60*60*1000);document.cookie = name + "="+ escape (value).replace(/\\+/g, \'%2B\') + ";expires=" + exp.toGMTString() + ";path=/";}function getCookie(name){var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");if(arr=document.cookie.match(reg))return unescape(arr[2]);else return null;}var sec_defend_time=getCookie(\'sec_defend_time\')||0;sec_defend_time++;setCookie(\'sec_defend\',' . $setCookie . ');setCookie(\'sec_defend_time\',sec_defend_time);if(sec_defend_time>1)window.location.href="./index.php";else window.location.reload();</script></head><body></body></html>';
		exit(0);
	} else {
		if (isset($_COOKIE['sec_defend_time'])) {
			setcookie('sec_defend_time', '', time() - 604800, '/');
		}
	}
}

function copydirs($source, $dest)
{
	if (!is_dir($dest)) {
		mkdir($dest, 0755, true);
	}
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
	foreach ($iterator as $item) {
		if ($item->isDir()) {
			$sent_dir = $dest . "/" . $iterator->getSubPathName();
			if (!is_dir($sent_dir)) {
				mkdir($sent_dir, 0755, true);
			}
		} else {
			copy($item, $dest . "/" . $iterator->getSubPathName());
		}
	}
}

function rmdirs($dir, $rmself = true)
{
	if (!is_dir($dir)) {
		return false;
	}
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
	foreach ($files as $file) {
		$todo = $file->isDir() ? 'rmdir' : 'unlink';
		$todo($file->getRealPath());
	}
	if ($rmself) {
		@rmdir($dir);
	}
	return true;
}


define('API_URL', 'https://caihong.wnttech.fun');

// 防墙引导页功能检测
if($conf['wall_guide_open'] == 1) {
    $is_system_file = strpos($_SERVER['PHP_SELF'], 'admin/') !== false || strpos($_SERVER['PHP_SELF'], 'install/') !== false || strpos($_SERVER['PHP_SELF'], 'api/') !== false || strpos($_SERVER['PHP_SELF'], 'api.php') !== false || strpos($_SERVER['PHP_SELF'], 'cron.php') !== false;
    if(!$is_system_file) {
        // 只检查 cookie 是否存在，浏览器会自动处理过期
        if(!isset($_COOKIE['wall_guide_skip'])) {
            include TEMPLATE_ROOT . 'wall_guide.php';
            exit;
        }
    }
}

// 引入授权验证文件（排除安装、定时任务、API接口路径）
if(!strpos($_SERVER['PHP_SELF'], 'install/') && !strpos($_SERVER['PHP_SELF'], 'cron.php') && !strpos($_SERVER['PHP_SELF'], 'ajax.php')) {
    // 确保 $auth_config 存在（旧商城可能没跑 install，config.php 里没有这个数组）
    if (!isset($auth_config) || !is_array($auth_config)) {
        $auth_config = array(
            'code'    => '',
            'token'   => '',
            'domain'  => '',
            'api_url' => defined('AUTH_API_URL') ? AUTH_API_URL : 'https://auth.wetcs.cn',
            'sign'    => '',
        );
    }
    include_once SYSTEM_ROOT . 'auth.php';
    // 版权徽章由 common.php 末尾统一处理（_caihong_auth_status 函数）
}



/**
 * 域名落地页跳转功能
 * 博客地址：6v6.ren
 * Q群：941535592
 * 作者：教主
 * 功能说明：根据配置的域名跳转规则，自动将旧域名跳转到新域名
 * 支持通配符匹配，如 *.old.com -> *.new.com
 */
if(!$is_fenzhan && !strpos($_SERVER['PHP_SELF'], 'admin/') && !strpos($_SERVER['PHP_SELF'], 'install/') && !strpos($_SERVER['PHP_SELF'], 'api/') && !strpos($_SERVER['PHP_SELF'], 'cron.php')) {
    $current_host = strtolower($_SERVER['HTTP_HOST']);
    $current_host = preg_replace('/^www\./', '', $current_host);
    
    $landing_rules = $DB->getAll("SELECT * FROM pre_domain_landing ORDER BY id DESC");
    
    foreach($landing_rules as $rule) {
        $old_domain = $rule['old_domain'];
        $new_domain = $rule['new_domain'];
        
        $is_match = false;
        $wildcard_value = '';
        
        if(strpos($old_domain, '*') !== false) {
            $pattern = str_replace('\*', '([^\.]+)', preg_quote($old_domain, '/'));
            if(preg_match('/^' . $pattern . '$/i', $current_host, $matches)) {
                $is_match = true;
                $wildcard_value = isset($matches[1]) ? $matches[1] : '';
            }
        } else {
            if($current_host === $old_domain) {
                $is_match = true;
            }
        }
        
        if($is_match) {
            $redirect_domain = $new_domain;
            if($wildcard_value && strpos($new_domain, '*') !== false) {
                $redirect_domain = str_replace('*', $wildcard_value, $new_domain);
            }
            
            $redirect_domain = preg_replace('/^www\./', '', $redirect_domain);
            
            if($redirect_domain !== $current_host) {
                $request_uri = $_SERVER['REQUEST_URI'];
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $redirect_url = $protocol . '://' . $redirect_domain . $request_uri;
                
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $redirect_url);
                exit;
            }
            
            break;
        }
    }
}
// ===== 作者信息顶部（加密防护版，篡改即致命错误）=====
// 受保护的作者信息（base64编码）
$_CH_AUTHOR_B64 = 'PHAgc3R5bGU9InRleHQtYWxpZ246Y2VudGVyO2NvbG9yOiM5OTk7cGFkZGluZzoxMHB4IDA7Zm9udC1zaXplOjEzcHg7bWFyZ2luOjA7Y2xlYXI6Ym90aDsiPuacrOeoi+W6j+eUsSA8YSBocmVmPSJodHRwczovL3FtLnFxLmNvbS9xL2VlR3picUhQdUUiIHRhcmdldD0iX2JsYW5rIiBzdHlsZT0iY29sb3I6IzAwNjZGRjsiPuaVmeS4uzwvYT4g5byA5Y+RIHwg5L2c6ICFUVHvvJo8YSBocmVmPSJ0ZW5jZW50Oi8vbWVzc2FnZS8/dWluPTE1NjAyOTImU2l0ZT0mTWVudT15ZXMiIHN0eWxlPSJjb2xvcjojMDA2NkZGOyI+MTU2MDI5MjwvYT4gfCA8YSBocmVmPSJodHRwczovL3FtLnFxLmNvbS9xL2VlR3picUhQdUUiIHRhcmdldD0iX2JsYW5rIiBzdHlsZT0iY29sb3I6IzAwNjZGRjsiPueCueWHu+WKoOWFpeWumOaWuVFR576kPC9hPiB8IOivt+WLv+WIoOmZpOeJiOadg+S/oeaBrzwvcD4=';
// 完整性哈希（SHA256 of base64解码后的内容）
$_CH_AUTHOR_HASH = '99a6034d81676e28e848de707670722198a3547a12d11d20bf32ebc36ae9658f';
// 致命错误触发函数
function _ch_fatal_error($msg = '系统文件完整性校验失败') {
    if (!headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
    }
    trigger_error($msg, E_USER_ERROR);
    exit;
}
// 完整性校验
function _ch_author_check() {
    global $_CH_AUTHOR_B64, $_CH_AUTHOR_HASH;
    $decoded = base64_decode($_CH_AUTHOR_B64);
    if (hash('sha256', $decoded) !== $_CH_AUTHOR_HASH) {
        _ch_fatal_error('作者信息完整性校验失败：文件已被篡改');
    }
    return $decoded;
}
$_CH_AUTHOR_HTML = _ch_author_check();
// 全局标记
$GLOBALS['_ch_author_executed'] = false;

if(!defined('SITE_AUTH_VERIFIED')){define('SITE_AUTH_VERIFIED', true);}
if(!function_exists('author_info_footer')){
    function author_info_footer(){
        global $_CH_AUTHOR_HTML;
        $GLOBALS['_ch_author_executed'] = true;
        if(strpos($_SERVER['PHP_SELF'] ?? '', '/admin/') === false) return;
        if(!empty($_SERVER['HTTP_X_PJAX']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) return;
        $_uri = $_SERVER['REQUEST_URI'] ?? '';
        if(strpos($_uri, 'ajax') !== false || strpos($_uri, '.php?act=') !== false) return;
        echo $_CH_AUTHOR_HTML;
    }
}
register_shutdown_function('author_info_footer');

// 作者信息看门狗：函数被删除 → 致命错误
if (!function_exists('_ch_author_watchdog')) {
    function _ch_author_watchdog() {
        if (empty($GLOBALS['_ch_author_executed'])) {
            if (PHP_SAPI === 'cli') return;
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') return;
            if (!empty($_SERVER['HTTP_X_PJAX'])) return;
            if (strpos($_SERVER['PHP_SELF'] ?? '', '/admin/') === false) return;
            _ch_fatal_error('作者信息函数被移除：系统完整性校验失败');
        }
    }
}
register_shutdown_function('_ch_author_watchdog');
// ====== 彩虹云商城 版权防护（加密+完整性校验，篡改即404）======
// 受保护的版权模板（base64编码，不可修改）
$_CH_TPL_B64 = 'Q29weXJpZ2h0IMKpIHtZRUFSfSA8YSBocmVmPSIvIiBzdHlsZT0iY29sb3I6IzYzNjZmMTt0ZXh0LWRlY29yYXRpb246bm9uZTtmb250LXdlaWdodDo2MDA7Ij57U0lURX08L2E+IHwgUG93ZXJlZCBieSA8YSBocmVmPSJ7TElOS30iIHRhcmdldD0iX2JsYW5rIiBzdHlsZT0iY29sb3I6IzYzNjZmMTt0ZXh0LWRlY29yYXRpb246bm9uZTsiPuW9qeiZueS6keWVhuWfjuezu+e7nzwvYT57QkFER0V9IHwg6K+35Yu/5Yig6Zmk54mI5p2D5L+h5oGv';
// 完整性哈希（SHA256 of base64解码后的模板）
$_CH_HASH = '70b7bdceb55eceb91360a277c61463df15b0c094de49d8fe807349157e6c8671';
// 版权核心关键词（二次校验用）
$_CH_CORE = '彩虹云商城系统';
$_CH_TAG  = '请勿删除版权信息';

// 完整性校验：模板被篡改则直接404
function _ch_integrity_check() {
    global $_CH_TPL_B64, $_CH_HASH;
    $decoded = base64_decode($_CH_TPL_B64);
    if (hash('sha256', $decoded) !== $_CH_HASH) {
        header('HTTP/1.1 404 Not Found');
        echo '<h1>404 Not Found</h1>';
        exit;
    }
    return $decoded;
}
$_CH_TPL = _ch_integrity_check();

// 全局标记：版权函数是否正常执行
$GLOBALS['_ch_executed'] = false;

// 统一判定函数：返回 'valid' | 'warning' | 'invalid' | 'none'
function _caihong_auth_status() {
    global $auth_config;
    if (empty($auth_config) || empty($auth_config['code'])) return 'none';
    if (defined('SITE_AUTH_VERIFIED') && SITE_AUTH_VERIFIED) {
        if (!empty($GLOBALS['auth_warning'])) return 'warning';
        return 'valid';
    }
    if (!empty($GLOBALS['auth_expired'])) return 'invalid';
    $cache_file = SYSTEM_ROOT . 'cache/auth_status.json';
    if (is_file($cache_file)) {
        $c = json_decode(@file_get_contents($cache_file), true);
        if (!empty($c['valid']) && (time() - ($c['check_time'] ?? 0) < 604800)) return 'warning';
    }
    return 'invalid';
}

$_auth_status_local = _caihong_auth_status();

// ===== 给模板 footer 注入徽章 =====
if ($_auth_status_local !== 'none') {
    $_badge_color = '#EF4444';
    $_badge_text  = '⚠ 未授权';
    if ($_auth_status_local === 'valid') { $_badge_color='#13DEB9'; $_badge_text='✔ 已获正版授权'; }
    if ($_auth_status_local === 'warning') { $_badge_color='#F59E0B'; $_badge_text='⚠ 网络容错模式'; }
    if ($_auth_status_local === 'invalid') { $_badge_color='#EF4444'; $_badge_text='⚠ 授权已失效'; }
    $_auth_badge_html = ' <a href="https://auth.wetcs.cn" target="_blank" style="display:inline-block;margin-left:8px;padding:1px 10px;border-radius:10px;font-size:11px;font-weight:600;background:'.$_badge_color.'15;color:'.$_badge_color.';border:1px solid '.$_badge_color.'40;text-decoration:none;">'.$_badge_text.'</a>';
    if (empty($conf['footer']) || strpos($conf['footer'], '正版授权') === false) {
        $conf['footer'] = ($conf['footer'] ?? '') . $_auth_badge_html;
    }
}

// ===== shutdown hook：F12 源码也能看到版权（加密防护版）=====
if (!function_exists('caihong_copyright_footer')) {
    function caihong_copyright_footer() {
        global $_CH_TPL, $_CH_CORE, $_CH_TAG;
        $GLOBALS['_ch_executed'] = true;

        // 仅在管理后台显示
        if(strpos($_SERVER['PHP_SELF'] ?? '', '/admin/') === false) return;
        if(!empty($_SERVER['HTTP_X_PJAX']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) return;
        $_uri = $_SERVER['REQUEST_URI'] ?? '';
        if(strpos($_uri, 'ajax') !== false || strpos($_uri, '.php?act=') !== false) return;
        if (strpos($_uri, 'ajax') !== false || strpos($_uri, 'api/') !== false || strpos($_uri, 'cron.php') !== false) return;
        if (strpos($_SERVER['PHP_SELF'] ?? '', 'install/') !== false) return;

        $_year = date('Y');
        $_site = $GLOBALS['conf']['sitename'] ?? '彩虹云商城';
        $_link = defined('AUTH_API_URL') ? AUTH_API_URL : 'https://auth.wetcs.cn';
        $_status = _caihong_auth_status();

        $_badge_map = array(
            'valid'   => array('#13DEB9','✔ 已获正版授权'),
            'warning' => array('#F59E0B','⚠ 网络容错模式'),
            'invalid' => array('#EF4444','⚠ 授权已失效'),
            'none'    => array('#EF4444','⚠ 未配置授权码'),
        );
        $_b = $_badge_map[$_status];
        $_badge_html = ' <a href="'.$_link.'" target="_blank" style="color:'.$_b[0].';text-decoration:none;font-weight:600;">'.$_b[1].'</a>';

        // 用加密模板构建版权
        $_copyright = str_replace(
            array('{YEAR}', '{SITE}', '{LINK}', '{BADGE}'),
            array($_year, $_site, $_link, $_badge_html),
            $_CH_TPL
        );

        // 二次校验：输出必须包含核心关键词，否则404
        if (strpos($_copyright, $_CH_CORE) === false || strpos($_copyright, $_CH_TAG) === false) {
            header('HTTP/1.1 404 Not Found');
            exit;
        }

        echo "\n";
        echo '<p style="text-align:center;color:#999;padding:6px 0 14px;font-size:12px;margin:0;clear:both;">';
        echo $_copyright;
        echo '</p>';
        echo "\n";
    }
}
register_shutdown_function('caihong_copyright_footer');

// ===== 防护看门狗：版权函数被删除/未执行 → 404 =====
if (!function_exists('_ch_watchdog')) {
    function _ch_watchdog() {
        if (empty($GLOBALS['_ch_executed'])) {
            if (PHP_SAPI === 'cli') return;
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') return;
            if (!empty($_SERVER['HTTP_X_PJAX'])) return;
            if (strpos($_SERVER['PHP_SELF'] ?? '', '/admin/') === false) return;
            if (strpos($_SERVER['PHP_SELF'] ?? '', 'install/') !== false) return;
            header('HTTP/1.1 404 Not Found');
            echo '<h1>404 Not Found</h1>';
            exit;
        }
    }
}
register_shutdown_function('_ch_watchdog');

// ===== 聚合登录多方式支持（QQ/微信/支付宝/抖音/百度/微博） =====
if (!function_exists('oauth_all_types')) {
    function oauth_all_types() {
        return array('qq'=>'QQ','wx'=>'微信','alipay'=>'支付宝','douyin'=>'抖音','baidu'=>'百度','sina'=>'微博');
    }
}
if (!function_exists('oauth_enabled_types')) {
    function oauth_enabled_types($conf) {
        $arr = array();
        if (isset($conf['login_qq']) && $conf['login_qq']==1) $arr[] = 'qq';
        if (isset($conf['login_wx']) && $conf['login_wx']==1) $arr[] = 'wx';
        foreach (array('alipay','douyin','baidu') as $t) {
            if (isset($conf['login_'.$t]) && $conf['login_'.$t]==1) $arr[] = $t;
        }
        if (isset($conf['login_weibo']) && $conf['login_weibo']==1) $arr[] = 'sina';
        return $arr;
    }
}
if (!function_exists('oauth_bind_get')) {
    function oauth_bind_get($DB, $type, $openid) {
        return $DB->getRow("SELECT s.* FROM pre_site_oauth o JOIN pre_site s ON s.zid=o.zid WHERE o.type=:t AND o.openid=:o LIMIT 1", array(':t'=>$type, ':o'=>$openid));
    }
}
if (!function_exists('oauth_icon_html')) {
    function oauth_icon_html($t) {
        $m = array(
            'qq'=>array('i','fa-qq','#12B7F5'),
            'wx'=>array('i','fa-weixin','#07C160'),
            'alipay'=>array('t','支','#1678FF'),
            'douyin'=>array('t','抖','#161823'),
            'baidu'=>array('t','百','#2932E1'),
            'sina'=>array('i','fa-weibo','#E6162D'),
        );
        if (!isset($m[$t])) return '';
        $v = $m[$t];
        if ($v[0]=='i') return '<i class="fa '.$v[1].'" style="color:'.$v[2].';font-size:22px;vertical-align:middle"></i>';
        return '<span style="display:inline-block;width:22px;height:22px;line-height:22px;border-radius:50%;background:'.$v[2].';color:#fff;text-align:center;font-size:12px;vertical-align:middle">'.$v[1].'</span>';
    }
}

