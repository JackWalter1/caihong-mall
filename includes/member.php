<?php
if(!defined('IN_CRONLITE'))exit();

// 定义password_hash变量，防止undefined variable错误
if(!isset($password_hash)) {
    $password_hash = '9Ks!3p@Wq8zV$x7L';
}

// 增强密码校验辅助函数（兼容明文和哈希）
function _admin_verify_password($password_input, $stored_password) {
    // 如果是bcrypt哈希，使用password_verify()
    if (strpos($stored_password, '$2y$') === 0) {
        return password_verify($password_input, $stored_password);
    }
    // 兼容旧的明文密码（长度小于60，且值相等）
    return (strlen($stored_password) < 60 && $password_input === $stored_password);
}

// 修复Undefined index: ip_type错误
$clientip=real_ip(isset($conf['ip_type']) ? $conf['ip_type'] : 0);

// 首先尝试从SESSION读取token，如果不存在则尝试从Cookie读取备份token
$admin_token = isset($_SESSION['admin_token']) ? $_SESSION['admin_token'] : (isset($_COOKIE['admin_token_backup']) ? $_COOKIE['admin_token_backup'] : null);

if($admin_token)
{
	$token=authcode(daddslashes($admin_token), 'DECODE', SYS_KEY);
	if(!empty($token)){ // 确保token解码成功
		list($admintypeid, $user, $sid, $time) = explode("\t", $token);
		if($admintypeid == '1'){
			if($adminuserrow = $DB->getRow("SELECT * FROM pre_account WHERE id=:id LIMIT 1", [':id' => intval($user)])){
				$session=md5($adminuserrow['username'].$adminuserrow['password'].$password_hash);
				if($session===$sid && $adminuserrow['active']==1 && $time > time()) {
					$islogin=1;
					// 如果是从Cookie恢复的，同时恢复SESSION
					if(!isset($_SESSION['admin_token']) && isset($_COOKIE['admin_token_backup'])) {
						$_SESSION['admin_token'] = $_COOKIE['admin_token_backup'];
					}
					// 延长Cookie有效期
					if(isset($_COOKIE['admin_token_backup'])) {
						setcookie("admin_token_backup", $_COOKIE['admin_token_backup'], time() + 259200, '/', '', false, false);
					}
				}
			}
		}else{
			$session=md5($conf['admin_user'].$conf['admin_pwd'].$password_hash);
			if($session===$sid && $time > time()) {
				$islogin=1;
				// 如果是从Cookie恢复的，同时恢复SESSION
				if(!isset($_SESSION['admin_token']) && isset($_COOKIE['admin_token_backup'])) {
					$_SESSION['admin_token'] = $_COOKIE['admin_token_backup'];
				}
				// 延长Cookie有效期
				if(isset($_COOKIE['admin_token_backup'])) {
					setcookie("admin_token_backup", $_COOKIE['admin_token_backup'], time() + 259200, '/', '', false, false);
				}
			}
		}
	}
}
if(isset($_COOKIE["user_token"]))
{
	$token=authcode(daddslashes($_COOKIE['user_token']), 'DECODE', SYS_KEY);
	list($zid, $sid) = explode("\t", $token);
	if($userrow = $DB->getRow("SELECT * FROM pre_site WHERE zid=:zid LIMIT 1", [':zid' => intval($zid)])){
		$session=md5($userrow['user'].$userrow['pwd'].$password_hash);
		if($session===$sid && $userrow['status']==1) {
			$islogin2=1;
		}
	}
}
if(isset($_COOKIE["sup_token"]))
{
    $token=authcode(daddslashes($_COOKIE['sup_token']), 'DECODE', SYS_KEY);
    list($sid, $ssid) = explode("\t", $token);
    if($suprow = $DB->getRow("SELECT * FROM pre_supplier WHERE sid=:sid LIMIT 1", [':sid' => intval($sid)])){
        $session=md5($suprow['user'].$suprow['pwd'].$password_hash);
        if($session===$ssid && $suprow['status']==1) {
            $islogin3=1;
        }
    }
}
?>