<?php
include("../includes/common.php");

$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

@header('Content-Type: application/json; charset=UTF-8');

if(!checkRefererHost())exit('{"code":403}');

switch($act){
case 'login':
	$user=daddslashes($_POST['user']);
	$pass=daddslashes($_POST['pass']);
	if(!$user || !$pass){
		exit('{"code":-1,"msg":"用户名或密码不能为空"}');
	}
	
	// 登录失败限制检查（国际级安全统一防护）
	$sec_lock = null;
	if (sec_conf('security_hardening', 0) == 1 && sec_conf('sec_brute_enabled', 1) == 1) {
		$sec_lock = sec_login_lockout('user');
		if (!$sec_lock['allow']) {
			exit('{"code":-1,"msg":"' . $sec_lock['msg'] . '"}');
		}
	}
	// 登录失败限制检查
	$login_limit_enable = isset($conf['login_limit_enable']) ? intval($conf['login_limit_enable']) : 0;
	if($login_limit_enable == 1){
		$login_limit_max = intval($conf['login_limit_max']) > 0 ? intval($conf['login_limit_max']) : 5;
		$login_limit_time = intval($conf['login_limit_time']) > 0 ? intval($conf['login_limit_time']) : 30;
		$cache_file = ROOT.'cache/login_fail_'.md5($clientip).'.json';
		$now_time = time();
		
		if(file_exists($cache_file)){
			$cache_data = json_decode(file_get_contents($cache_file), true);
			if($cache_data && isset($cache_data['fail_count']) && $cache_data['fail_count'] >= $login_limit_max){
				$fail_time = $cache_data['last_fail_time'];
				$expire_time = $fail_time + ($login_limit_time * 60);
				if($now_time < $expire_time){
					$remain_minutes = ceil(($expire_time - $now_time) / 60);
					exit('{"code":-1,"msg":"登录失败次数过多，请'.$remain_minutes.'分钟后再试"}');
				}else{
					@unlink($cache_file);
				}
			}
		}
	}
	
	if($conf['captcha_open_login']==1 && $conf['captcha_open']==1){
		if(isset($_POST['geetest_challenge']) && isset($_POST['geetest_validate']) && isset($_POST['geetest_seccode'])){
			$GtSdk = new \lib\GeetestLib($conf['captcha_id'], $conf['captcha_key']);

			$data = array(
				'user_id' => $cookiesid,
				'client_type' => "web",
				'ip_address' => $clientip
			);

			if ($_SESSION['gtserver'] == 1) {   //服务器正常
				$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
				if ($result) {
					//echo '{"status":"success"}';
				} else{
					exit('{"code":-1,"msg":"验证失败，请重新验证"}');
				}
			}else{  //服务器宕机,走failback模式
				if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
					//echo '{"status":"success"}';
				}else{
					exit('{"code":-1,"msg":"验证失败，请重新验证"}');
				}
			}
		}else{
			exit('{"code":2,"type":1,"msg":"请先完成验证"}');
		}
	}elseif($conf['captcha_open_login']==1 && $conf['captcha_open']==2){
		if(isset($_POST['token'])){
			$client = new \lib\CaptchaClient($conf['captcha_id'], $conf['captcha_key']);
			$client->setTimeOut(2);
			$response = $client->verifyToken($_POST['token']);
			if($response->result){
				/**token验证通过，继续其他流程**/
			}else{
				/**token验证失败**/
				exit('{"code":-1,"msg":"验证失败，请重新验证"}');
			}
		}else{
			exit('{"code":2,"type":2,"appid":"'.$conf['captcha_id'].'","msg":"请先完成验证"}');
		}
	}elseif($conf['captcha_open_login']==1 && $conf['captcha_open']==3){
		if(isset($_POST['token'])){
			if(vaptcha_verify($conf['captcha_id'], $conf['captcha_key'], $_POST['token'], $clientip)){
				/**token验证通过，继续其他流程**/
			}else{
				/**token验证失败**/
				exit('{"code":-1,"msg":"验证失败，请重新验证"}');
			}
		}else{
			exit('{"code":2,"type":3,"appid":"'.$conf['captcha_id'].'","msg":"请先完成验证"}');
		}
	}
	$row=$DB->getRow("SELECT zid,user,pwd,status FROM pre_site WHERE user=:user LIMIT 1", [':user'=>$user]);
	if($row && $user===$row['user'] && $pass===$row['pwd']) {
		if($row['status']==0){
			exit('{"code":-1,"msg":"当前账号已被封禁！"}');
		}
		$session=md5($user.$pass.$password_hash);
		$token=authcode("{$row['zid']}\t{$session}", 'ENCODE', SYS_KEY);
		ob_clean();
		setcookie("user_token", $token, time() + 604800, '/');
		log_result('分站登录', 'User:'.$user.' IP:'.$clientip, null, 1);
		
		// 登录成功，清除登录失败记录
		sec_login_clear('user');
		if(isset($login_limit_enable) && $login_limit_enable == 1){
			$cache_file = ROOT.'cache/login_fail_'.md5($clientip).'.json';
			@unlink($cache_file);
		}
		
		if($_SESSION['Oauth_qq_openid'] && $_SESSION['Oauth_qq_token']){
			$DB->exec("UPDATE pre_site SET qq_openid=:qq_openid,lasttime=NOW() WHERE zid=:zid", [':qq_openid'=>$_SESSION['Oauth_qq_openid'], ':zid'=>$row['zid']]);
			unset($_SESSION['Oauth_qq_openid']);
			unset($_SESSION['Oauth_qq_token']);
			unset($_SESSION['Oauth_qq_nickname']);
			unset($_SESSION['Oauth_qq_faceimg']);
			exit('{"code":0,"msg":"绑定QQ快捷登录成功！"}');
		}else{
			$DB->exec("UPDATE pre_site SET lasttime=NOW() WHERE zid=:zid", [':zid'=>$row['zid']]);
			exit('{"code":0,"msg":"登陆用户中心成功！"}');
		}
	}else {
		// 登录失败，记录失败次数
		if(isset($login_limit_enable) && $login_limit_enable == 1){
			$cache_file = ROOT.'cache/login_fail_'.md5($clientip).'.json';
			$now_time = time();
			
			if(file_exists($cache_file)){
				$cache_data = json_decode(file_get_contents($cache_file), true);
				if($cache_data && isset($cache_data['last_fail_time'])){
					// 检查上次失败时间是否在限制时间内（30分钟内）
					if(($now_time - $cache_data['last_fail_time']) < 1800){
						$cache_data['fail_count'] = isset($cache_data['fail_count']) ? $cache_data['fail_count'] + 1 : 1;
					}else{
						// 超过30分钟，重新计数
						$cache_data['fail_count'] = 1;
					}
					$cache_data['last_fail_time'] = $now_time;
				}else{
					$cache_data = ['fail_count' => 1, 'last_fail_time' => $now_time];
				}
			}else{
				$cache_data = ['fail_count' => 1, 'last_fail_time' => $now_time];
			}
			@file_put_contents($cache_file, json_encode($cache_data));
		}
		sec_login_fail('user', $user);
		exit('{"code":-1,"msg":"用户名或密码不正确！"}');
	}
break;
case 'connect':
	$oauth_enabled = oauth_enabled_types($conf);
	if(!$oauth_enabled)exit('{"code":-1,"msg":"当前站点未开启快捷登录"}');
	$type = isset($_POST['type'])?$_POST['type']:exit('{"code":-1,"msg":"no type"}');
	if(!in_array($type, $oauth_enabled))exit('{"code":-1,"msg":"该登录方式未开启"}');
	$back = isset($_POST['back'])?$_POST['back']:null;
	$Oauth = new \lib\Oauth($conf['login_apiurl'], $conf['login_appid'], $conf['login_appkey']);
	$res = $Oauth->login($type);
	if(isset($res['code']) && $res['code']==0){
		$result = ['code'=>0, 'url'=>$res['url']];
		if($back){
			$_SESSION['Oauth_back'] = $back;
		}elseif(isset($_SESSION['Oauth_back'])){
			unset($_SESSION['Oauth_back']);
		}
	}elseif(isset($res['code'])){
		$result = ['code'=>-1, 'msg'=>$res['msg']];
	}else{
		$result = ['code'=>-1, 'msg'=>'快捷登录接口请求失败'];
	}
	exit(json_encode($result));
break;
case 'quickreg':
	if(!oauth_enabled_types($conf))exit('{"code":-1,"msg":"当前站点未开启快捷登录"}');
	if(!$_SESSION['Oauth_qq_openid'] || !$_SESSION['Oauth_qq_token'])exit('{"code":-1,"msg":"请返回重新登录"}');
	if(!$_POST['submit'])exit('{"code":-1,"msg":"access"}');
	$reg_type = $_SESSION['Oauth_qq_type'] ? $_SESSION['Oauth_qq_type'] : 'qq';
	$user = $reg_type.'_'.random(8);
	$pwd = $_SESSION['Oauth_qq_token'];
	$openid = $_SESSION['Oauth_qq_openid'];
	$nickname = $_SESSION['Oauth_qq_nickname'];
	if(strlen($nickname)>32) $nickname = mb_strcut($nickname, 0, 32);
	$faceimg = $_SESSION['Oauth_qq_faceimg'];

	$sql="insert into `pre_site` (`upzid`,`power`,`domain`,`domain2`,`user`,`pwd`,`nickname`,`faceimg`,`rmb`,`qq`,`reg_ip`,`sitename`,`keywords`,`description`,`addtime`,`lasttime`,`status`) values (:upzid,0,NULL,NULL,:user,:pwd,:nickname,:faceimg,'0',NULL,:reg_ip,NULL,NULL,NULL,NOW(),NOW(),'1')";
	$data = [':upzid'=>$siterow['zid']?$siterow['zid']:0, ':user'=>$user, ':pwd'=>$pwd, ':nickname'=>$nickname, ':faceimg'=>$faceimg, ':reg_ip'=>$clientip];
	if($DB->exec($sql, $data)){
		$zid = $DB->lastInsertId();
		$DB->exec("INSERT IGNORE INTO pre_site_oauth (zid,type,openid) VALUES (:z,:t,:o)", [':z'=>$zid, ':t'=>$reg_type, ':o'=>$openid]);
		unset($_SESSION['Oauth_qq_openid']);
		unset($_SESSION['Oauth_qq_token']);
		unset($_SESSION['Oauth_qq_nickname']);
		unset($_SESSION['Oauth_qq_faceimg']);
		$DB->exec("UPDATE `pre_orders` SET `userid`='".$zid."' WHERE `userid`='".$cookiesid."'");
		$session=md5($user.$pwd.$password_hash);
		$token=authcode("{$zid}\t{$session}", 'ENCODE', SYS_KEY);
		ob_clean();
		setcookie("user_token", $token, time() + 604800, '/');
		log_result('分站登录', 'User:'.$user.' IP:'.$clientip, null, 1);
		exit('{"code":0,"msg":"注册用户成功","zid":"'.$zid.'"}');
	}else{
		exit('{"code":-1,"msg":"注册用户失败！'.dbErrorMsg($DB->error()).'"}');
	}
break;
case 'unbind':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	$type = isset($_POST['type'])?$_POST['type']:exit('{"code":-1,"msg":"no type"}');
	$ub_all = oauth_all_types();
	if(!isset($ub_all[$type]))exit('{"code":-1,"msg":"不支持的登录方式"}');
	$ub_name = $ub_all[$type];
	if($DB->exec("DELETE FROM pre_site_oauth WHERE zid=:z AND type=:t", [':z'=>$userrow['zid'], ':t'=>$type])){
		exit('{"code":0,"msg":"您已成功解绑'.$ub_name.'！"}');
	}else{
		exit('{"code":-1,"msg":"解绑'.$ub_name.'失败！'.dbErrorMsg($DB->error()).'"}');
	}
break;
case 'setpwd':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	if(substr($userrow['user'],0,3)!='qq_')exit('{"code":-1,"msg":"请勿重复提交"}');
	$user = trim(htmlspecialchars(strip_tags(daddslashes($_POST['user']))));
	$pwd = trim(htmlspecialchars(strip_tags(daddslashes($_POST['pwd']))));
	if (!preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/',$user)) {
		exit('{"code":-1,"msg":"用户名只能为英文、数字与汉字！"}');
	} elseif ($DB->getRow("SELECT zid FROM pre_site WHERE user=:user LIMIT 1", [':user'=>$user])) {
		exit('{"code":-1,"msg":"用户名已存在！"}');
	} elseif (strlen($pwd) < 6) {
		exit('{"code":-1,"msg":"密码不能低于6位"}');
	} elseif ($pwd == $user) {
		exit('{"code":-1,"msg":"用户名和密码不能相同！"}');
	}
	if($DB->exec("UPDATE pre_site SET user=:user,pwd=:pwd WHERE zid=:zid", [':user'=>$user, ':pwd'=>$pwd, ':zid'=>$userrow['zid']])){
		$session=md5($user.$pwd.$password_hash);
		$token=authcode("{$userrow['zid']}\t{$session}", 'ENCODE', SYS_KEY);
		ob_clean();
		setcookie("user_token", $token, time() + 604800, '/');
		exit('{"code":0,"msg":"保存成功"}');
	}else{
		exit('{"code":-1,"msg":"保存失败！'.dbErrorMsg($DB->error()).'"}');
	}
break;
case 'checkdomain':
	$qz = daddslashes($_GET['qz']);
	$_suffix = preg_replace('/^\*\./', '', daddslashes($_GET['domain']));
	$domain = $qz . '.' . $_suffix;
	$srow=$DB->getRow("SELECT zid FROM pre_site WHERE domain=:domain OR domain2=:domain OR domain3=:domain OR domain4=:domain OR domain5=:domain OR domain6=:domain LIMIT 1", [':domain'=>$domain]);
	if($srow)exit('1');
	else exit('0');
break;
case 'checkuser':
	$user = trim($_GET['user']);
	$srow=$DB->getRow("SELECT zid FROM pre_site WHERE user=:user LIMIT 1", [':user'=>$user]);
	if($srow)exit('1');
	else exit('0');
break;
case 'reguser':
	if($islogin2==1)exit('{"code":-1,"msg":"您已登陆！"}');
	elseif($conf['user_open']==0)exit('{"code":-1,"msg":"当前站点未开启用户注册功能！"}');
	$user = trim(htmlspecialchars(strip_tags(daddslashes($_POST['user']))));
	$pwd = trim(htmlspecialchars(strip_tags(daddslashes($_POST['pwd']))));
	$qq = trim(daddslashes($_POST['qq']));
	$hashsalt = isset($_POST['hashsalt'])?$_POST['hashsalt']:null;
	$code = isset($_POST['code'])?$_POST['code']:null;
	if($conf['verify_open']==1 && (empty($_SESSION['addsalt']) || $hashsalt!=$_SESSION['addsalt'])){
		exit('{"code":-1,"msg":"验证失败，请刷新页面重试"}');
	}
	if (!preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/',$user)) {
		exit('{"code":-1,"msg":"用户名只能为英文、数字与汉字！"}');
	} elseif ($DB->getRow("SELECT zid FROM pre_site WHERE user=:user LIMIT 1", [':user'=>$user])) {
		exit('{"code":-1,"msg":"用户名已存在！"}');
	} elseif (strlen($pwd) < 6) {
		exit('{"code":-1,"msg":"密码不能低于6位"}');
	} elseif (strlen($qq) < 5 || !preg_match('/^[0-9]+$/',$qq)) {
		exit('{"code":-1,"msg":"QQ格式不正确！"}');
	} elseif ($pwd == $user) {
		exit('{"code":-1,"msg":"用户名和密码不能相同！"}');
	}
	if($conf['captcha_open']==1){
		if(isset($_POST['geetest_challenge']) && isset($_POST['geetest_validate']) && isset($_POST['geetest_seccode'])){
			$GtSdk = new \lib\GeetestLib($conf['captcha_id'], $conf['captcha_key']);

			$data = array(
				'user_id' => $cookiesid,
				'client_type' => "web",
				'ip_address' => $clientip
			);

			if ($_SESSION['gtserver'] == 1) {   //服务器正常
				$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
				if ($result) {
					//echo '{"status":"success"}';
				} else{
					exit('{"code":-1,"msg":"验证失败，请重新验证"}');
				}
			}else{  //服务器宕机,走failback模式
				if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
					//echo '{"status":"success"}';
				}else{
					exit('{"code":-1,"msg":"验证失败，请重新验证"}');
				}
			}
		}else{
			exit('{"code":2,"type":1,"msg":"请先完成验证"}');
		}
	}elseif($conf['captcha_open']==2){
		if(isset($_POST['token'])){
			$client = new \lib\CaptchaClient($conf['captcha_id'], $conf['captcha_key']);
			$client->setTimeOut(2);
			$response = $client->verifyToken($_POST['token']);
			if($response->result){
				/**token验证通过，继续其他流程**/
			}else{
				/**token验证失败**/
				exit('{"code":-1,"msg":"验证失败，请重新验证"}');
			}
		}else{
			exit('{"code":2,"type":2,"appid":"'.$conf['captcha_id'].'","msg":"请先完成验证"}');
		}
	}elseif($conf['captcha_open']==3){
		if(isset($_POST['token'])){
			if(vaptcha_verify($conf['captcha_id'], $conf['captcha_key'], $_POST['token'], $clientip)){
				/**token验证通过，继续其他流程**/
			}else{
				/**token验证失败**/
				exit('{"code":-1,"msg":"验证失败，请重新验证"}');
			}
		}else{
			exit('{"code":2,"type":3,"appid":"'.$conf['captcha_id'].'","msg":"请先完成验证"}');
		}
	}elseif (!$code || !isset($_SESSION['vc_code']) || strtolower($code) != $_SESSION['vc_code']) {
		unset($_SESSION['vc_code']);
		exit('{"code":2,"msg":"验证码错误！"}');
	}
	$sql="insert into `pre_site` (`upzid`,`power`,`domain`,`domain2`,`user`,`pwd`,`rmb`,`qq`,`reg_ip`,`sitename`,`keywords`,`description`,`anounce`,`bottom`,`modal`,`addtime`,`lasttime`,`status`) values (:upzid,0,NULL,NULL,:user,:pwd,'0',:qq,:reg_ip,NULL,NULL,NULL,NULL,NULL,NULL,NOW(),NOW(),'1')";
	$data = [':upzid'=>$siterow['zid']?$siterow['zid']:0, ':user'=>$user, ':pwd'=>$pwd, ':qq'=>$qq, ':reg_ip'=>$clientip];
	if($DB->exec($sql, $data)){
		$zid = $DB->lastInsertId();
		unset($_SESSION['addsalt']);
		$DB->exec("UPDATE `pre_orders` SET `userid`='".$zid."' WHERE `userid`='".$cookiesid."'");
		$session=md5($user.$pwd.$password_hash);
		$token=authcode("{$zid}\t{$session}", 'ENCODE', SYS_KEY);
		ob_clean();
		setcookie("user_token", $token, time() + 604800, '/');
		log_result('分站登录', 'User:'.$user.' IP:'.$clientip, null, 1);
		exit('{"code":1,"msg":"注册用户成功","zid":"'.$zid.'"}');
	}else{
		exit('{"code":-1,"msg":"注册用户失败！'.dbErrorMsg($DB->error()).'"}');
	}
break;
case 'paysite':
	if($islogin2==1 && $userrow['power']>0)exit('{"code":-1,"msg":"您已开通过分站！"}');
	elseif($conf['fenzhan_buy']==0)exit('{"code":-1,"msg":"当前站点未开启自助开通分站功能！"}');
	if($is_fenzhan == true && $siterow['power']==2){
		if($siterow['ktfz_price']>0)$conf['fenzhan_price']=$siterow['ktfz_price'];
		if($conf['fenzhan_cost2']<=0)$conf['fenzhan_cost2']=$conf['fenzhan_price2'];
		if($siterow['ktfz_price2']>0 && $siterow['ktfz_price2']>=$conf['fenzhan_cost2'])$conf['fenzhan_price2']=$siterow['ktfz_price2'];
	}
	$kind = intval($_POST['kind']);
	$qz = trim(strtolower(daddslashes($_POST['qz'])));
	$_domain_suffix = preg_replace('/^\*\./', '', trim($_POST['domain'])); // 兼容：去掉 *. 前缀
	$domain = trim(strtolower(htmlspecialchars(strip_tags(daddslashes($_domain_suffix)))));
	$user = trim(htmlspecialchars(strip_tags(daddslashes($_POST['user']))));
	$pwd = trim(htmlspecialchars(strip_tags(daddslashes($_POST['pwd']))));
	$name = trim(htmlspecialchars(strip_tags(daddslashes($_POST['name']))));
	$qq = trim(daddslashes($_POST['qq']));
	$hashsalt = isset($_POST['hashsalt'])?$_POST['hashsalt']:null;
	$domain = $qz . '.' . $domain;
	if($conf['verify_open']==1 && (empty($_SESSION['addsalt']) || $hashsalt!=$_SESSION['addsalt'])){
		exit('{"code":-1,"msg":"验证失败，请刷新页面重试"}');
	}
	if ($kind!=0 && $kind!=1 && $kind!=2) {
		exit('{"code":-1,"msg":"分站类型错误！"}');
	} elseif (empty($_POST['domain'])) {
		exit('{"code":-1,"msg":"域名后缀不能为空，请主站站长在后台设置:分站可用域名"}');
	} elseif (strlen($qz) < 2 || strlen($qz) > 10 || !preg_match('/^[a-z0-9\-]+$/',$qz)) {
		exit('{"code":-1,"msg":"域名前缀不合格！"}');
	} elseif (!preg_match('/^[a-zA-Z0-9\_\-\.]+$/',$domain)) {
		exit('{"code":-1,"msg":"域名格式不正确！"}');
	} elseif ($DB->getRow("SELECT zid FROM pre_site WHERE domain=:domain OR domain2=:domain OR domain3=:domain OR domain4=:domain OR domain5=:domain OR domain6=:domain LIMIT 1", [':domain'=>$domain]) || $qz=='www' || $domain==$_SERVER['HTTP_HOST'] || in_array($domain,explode(',',$conf['fenzhan_remain']))) {
		exit('{"code":-1,"msg":"此前缀已被使用！"}');
	}
	if(!$islogin2){
		if (!preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/',$user)) {
			exit('{"code":-1,"msg":"用户名只能为英文、数字与汉字！"}');
		} elseif ($DB->getRow("SELECT zid FROM pre_site WHERE user=:user LIMIT 1", [':user'=>$user])) {
			exit('{"code":-1,"msg":"用户名已存在！"}');
		} elseif (strlen($pwd) < 6) {
			exit('{"code":-1,"msg":"密码不能低于6位"}');
		} elseif (strlen($name) < 2) {
			exit('{"code":-1,"msg":"网站名称太短！"}');
		} elseif (strlen($qq) < 5 || !preg_match('/^[0-9]+$/',$qq)) {
			exit('{"code":-1,"msg":"QQ格式不正确！"}');
		} elseif ($pwd == $user) {
			exit('{"code":-1,"msg":"用户名和密码不能相同！"}');
		}
	}
	$fenzhan_expiry = $conf['fenzhan_expiry']>0?$conf['fenzhan_expiry']:12;
	$endtime = date("Y-m-d H:i:s", strtotime("+ {$fenzhan_expiry} months", time()));
	$trade_no=date("YmdHis").rand(111,999);
	if($kind==2){
		$need=addslashes($conf['fenzhan_price2']);
	}else{
		$need=addslashes($conf['fenzhan_price']);
	}
	if($need==0){
		if($conf['captcha_open_free']==1 && $conf['captcha_open']==1){
			if(isset($_POST['geetest_challenge']) && isset($_POST['geetest_validate']) && isset($_POST['geetest_seccode'])){
				$GtSdk = new \lib\GeetestLib($conf['captcha_id'], $conf['captcha_key']);

				$data = array(
					'user_id' => $cookiesid,
					'client_type' => "web",
					'ip_address' => $clientip
				);

				if ($_SESSION['gtserver'] == 1) {   //服务器正常
					$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
					if ($result) {
						//echo '{"status":"success"}';
					} else{
						exit('{"code":-1,"msg":"验证失败，请重新验证"}');
					}
				}else{  //服务器宕机,走failback模式
					if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
						//echo '{"status":"success"}';
					}else{
						exit('{"code":-1,"msg":"验证失败，请重新验证"}');
					}
				}
			}else{
				exit('{"code":2,"type":1,"msg":"请先完成验证"}');
			}
		}elseif($conf['captcha_open_free']==1 && $conf['captcha_open']==2){
			if(isset($_POST['token'])){
				$client = new \lib\CaptchaClient($conf['captcha_id'], $conf['captcha_key']);
				$client->setTimeOut(2);
				$response = $client->verifyToken($_POST['token']);
				if($response->result){
					/**token验证通过，继续其他流程**/
				}else{
					/**token验证失败**/
					exit('{"code":-1,"msg":"验证失败，请重新验证"}');
				}
			}else{
				exit('{"code":2,"type":2,"appid":"'.$conf['captcha_id'].'","msg":"请先完成验证"}');
			}
		}elseif($conf['captcha_open_free']==1 && $conf['captcha_open']==3){
			if(isset($_POST['token'])){
				if(vaptcha_verify($conf['captcha_id'], $conf['captcha_key'], $_POST['token'], $clientip)){
					/**token验证通过，继续其他流程**/
				}else{
					/**token验证失败**/
					exit('{"code":-1,"msg":"验证失败，请重新验证"}');
				}
			}else{
				exit('{"code":2,"type":3,"appid":"'.$conf['captcha_id'].'","msg":"请先完成验证"}');
			}
		}
		$keywords=$conf['keywords'];
		$description=$conf['description'];
		if($islogin2==1){
			$sql="UPDATE `pre_site` SET `power`=:power,`domain`=:domain,`sitename`=:sitename,`title`=:title,`keywords`=:keywords,`description`=:description,`endtime`=:endtime WHERE `zid`=:zid";
			$data = [':power'=>$kind, ':domain'=>$domain, ':sitename'=>$name, ':title'=>$conf['title'], ':keywords'=>$keywords, ':description'=>$description, ':endtime'=>$endtime, ':zid'=>$userrow['zid']];
			$DB->exec($sql, $data);
			$zid=$userrow['zid'];
		}else{
			$sql="INSERT INTO `pre_site` (`upzid`,`power`,`domain`,`domain2`,`user`,`pwd`,`rmb`,`qq`,`reg_ip`,`sitename`,`title`,`keywords`,`description`,`addtime`,`endtime`,`status`) VALUES (:upzid, :power, :domain, NULL, :user, :pwd, :rmb, :qq, :reg_ip, :sitename, :title, :keywords, :description, NOW(), :endtime, 1)";
			$data = [':upzid'=>$siterow['zid']?$siterow['zid']:0, ':power'=>$kind, ':domain'=>$domain, ':user'=>$user, ':pwd'=>$pwd, ':rmb'=>'0.00', ':qq'=>$qq, ':reg_ip'=>$clientip, ':sitename'=>$name, ':title'=>$conf['title'], ':keywords'=>$keywords, ':description'=>$description, ':endtime'=>$endtime];
			$DB->exec($sql, $data);
			$zid = $DB->lastInsertId();
		}
		if($zid){
			$_SESSION['newzid']=$zid;
			unset($_SESSION['addsalt']);
			if(!$islogin2)$DB->exec("UPDATE `pre_orders` SET `userid`='".$zid."' WHERE `userid`='".$cookiesid."'");
			$DB->exec("UPDATE `pre_orders` SET `zid`='".$zid."' WHERE `userid`='".$zid."'");
			exit('{"code":1,"msg":"开通分站成功","zid":"'.$zid.'"}');
		}else{
			exit('{"code":-1,"msg":"开通分站失败！'.dbErrorMsg($DB->error()).'"}');
		}
	}else{
		if($islogin2==1){
			$input='update|'.$userrow['zid'].'|'.$kind.'|'.$domain.'|'.$name.'|'.$endtime;
		}else{
			$input='add|'.$kind.'|'.$domain.'|'.$user.'|'.$pwd.'|'.$name.'|'.$qq.'|'.$endtime;
		}
		$sql="INSERT INTO `pre_pay` (`trade_no`,`tid`,`zid`,`input`,`num`,`name`,`money`,`ip`,`userid`,`addtime`,`status`) VALUES (:trade_no, :tid, :zid, :input, :num, :name, :money, :ip, :userid, NOW(), 0)";
		$data = [':trade_no'=>$trade_no, ':tid'=>-2, ':zid'=>$siterow['zid']?$siterow['zid']:1, ':input'=>$input, ':num'=>1, ':name'=>'自助开通分站', ':money'=>$need, ':ip'=>$clientip, ':userid'=>$cookiesid];
		if($DB->exec($sql, $data)){
			unset($_SESSION['addsalt']);
			exit('{"code":0,"msg":"提交订单成功！","trade_no":"'.$trade_no.'","need":"'.$need.'","pay_alipay":"'.$conf['alipay_api'].'","pay_wxpay":"'.$conf['wxpay_api'].'","pay_qqpay":"'.$conf['qqpay_api'].'","pay_rmb":"'.$islogin2.'","user_rmb":"'.$userrow['rmb'].'"}');
		}else{
			exit('{"code":-1,"msg":"提交订单失败！'.dbErrorMsg($DB->error()).'"}');
		}
	}
break;
case 'up_price':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	unset($islogin2);
	$price_obj = new \lib\Price($userrow['zid'],$userrow);
	$up=intval($_POST['up']);
	if($up<=0)exit('{"code":-1,"msg":"输入值不正确"}');
	$sql=$DB->query("select * from pre_tools where active=1");
	$data=array();
	while($row=$sql->fetch()){
		if($row['price']==0){
			continue;
		}
		if(strpos($row['name'],'免费')!==false){
			continue;
		}
		$price_obj->setToolInfo($row['tid'],$row);
		$price = $price_obj->getToolPrice($tid);
		$a=(float)$up/100;
		$data[$row['tid']]['price']=round($price*($a+1),2);
	}
	$array_data=serialize($data);
	$DB->exec("update `pre_site` set `price`='{$array_data}' where zid='{$userrow['zid']}'");
	exit('{"code":0}');
break;
case 'create_url':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	$force = trim(daddslashes($_GET['force']));
	if(!$userrow['domain'])exit('{"code":-1,"msg":"当前分站还未绑定域名"}');
	$url = 'http://'.$userrow['domain'].'/';
	if($conf['fanghong_api']){
    	if($force==1){
    		$turl = fanghongdwz($url,true);
    	}else{
    		$turl = fanghongdwz($url);
    	}
    	if(!empty($userrow['domain2'])){
    		$url2 = 'http://'.$userrow['domain2'].'/';
    		if($force==1){
    			$turl2 = fanghongdwz($url2,true);
    		}else{
    			$turl2 = fanghongdwz($url2);
    		}
    	}
    	if($turl == $url){
    		$result = array('code'=>-1, 'msg'=>'生成失败，请联系站长更换接口');
    	}elseif(strpos($turl,'/') || strpos($turl2,'/')){
    		$result = array('code'=>0, 'msg'=>'succ', 'url'=>$turl, 'url2'=>$turl2);
    	}else{
    		$result = array('code'=>-1, 'msg'=>'生成失败：'.$turl);
    	}
	}else{
	   	$result = array('code'=>-1, 'msg'=>'站长未开启'); 
	}
	exit(json_encode($result));
break;
case 'qiandao':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	if(!$conf['qiandao_reward'])exit('{"code":-1,"msg":"当前站点未开启签到功能"}');
	if(!isset($_SESSION['isqiandao']) || $_SESSION['isqiandao']!=$userrow['zid'])exit('{"code":-1,"msg":"校验失败，请刷新页面重试"}');
	$day = date("Y-m-d");
	$lastday = date("Y-m-d",strtotime("-1 day"));
	
	if ($DB->getRow("SELECT * FROM pre_qiandao WHERE zid='{$userrow['zid']}' AND date='$day' ORDER BY id DESC LIMIT 1")) {
		exit('{"code":-1,"msg":"今天已经签到过了, 明天在来吧！"}');
	}
	if ($conf['qiandao_limitip']==1 && $DB->getRow("SELECT * FROM pre_qiandao WHERE ip='{$clientip}' AND date='$day' ORDER BY id DESC LIMIT 1")) {
		exit('{"code":-1,"msg":"您的IP今天已经签到过了，明天在来吧！"}');
	}
	if ($row = $DB->getRow("SELECT * FROM pre_qiandao WHERE zid='{$userrow['zid']}' AND date='$lastday' ORDER BY id DESC LIMIT 1")) {
		$continue = $row['continue']+1;
	}else{
		$continue = 1;
	}
	if($continue > $conf['qiandao_day']) $continue = $conf['qiandao_day'];
	$reward = $conf['qiandao_reward'];
	if(strpos($reward,'|')){
		$reward = explode('|',$reward);
		$reward = $reward[$userrow['power']];
		if(!$reward)exit('{"code":-1,"msg":"未配置好签到奖励余额初始值"}');
	}
	if($conf['qiandao_mult']>0){
		for($i=1;$i<$continue;$i++){
			$reward *= $conf['qiandao_mult'];
		}
	}
	$reward = round($reward,2);
	$sql="INSERT INTO `pre_qiandao` (`zid`,`qq`,`reward`,`date`,`time`,`continue`,`ip`) VALUES ('".$userrow['zid']."','".$userrow['qq']."','".$reward."','".$day."','".$date."','".$continue."','".$clientip."')";
	if($DB->exec($sql)){
		unset($_SESSION['isqiandao']);
		changeUserMoney($userrow['zid'], $reward, true, '赠送', '您今天签到获得了'.$reward.'元奖励');
		$result = array('code'=>0, 'msg'=>'签到成功，获得'.$reward.'元现金奖励！');
	}else{
		$result = array('code'=>-1, 'msg'=>'签到失败'.dbErrorMsg($DB->error()));
	}
	exit(json_encode($result));
break;
case 'qdcount':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	$day=date("Y-m-d");
	$lastday = date("Y-m-d",strtotime("-1 day"));
	$count1=$DB->getColumn("SELECT count(*) FROM pre_qiandao WHERE date='$day'");
	$count2=$DB->getColumn("SELECT count(*) FROM pre_qiandao WHERE date='$lastday'");
	$count3=$DB->getColumn("SELECT count(*) FROM pre_qiandao");
	$rewardcount=$DB->getColumn("SELECT sum(reward) FROM pre_qiandao WHERE zid='{$userrow['zid']}'");
	$result=array("count1"=>$count1,"count2"=>$count2,"count3"=>$count3,"rewardcount"=>round($rewardcount,2));
	exit(json_encode($result));
break;
case 'msg':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	if($userrow['power']==2){
		$type = '0,2,4';
	}elseif($userrow['power']==1){
		$type = '0,2,3';
	}else{
		$type = '0,1';
	}
	$msgread = trim($userrow['msgread'],',');
	if(empty($msgread))$msgread='0';
	$count=$DB->getColumn("SELECT count(*) FROM pre_message WHERE id NOT IN ($msgread) and type IN ($type)");
	$count2=$DB->getColumn("SELECT count(*) FROM pre_workorder WHERE zid='{$userrow['zid']}' AND status=1");
	$thtime=date("Y-m-d").' 00:00:00';
	$income_today=$DB->getColumn("SELECT sum(point) FROM pre_points WHERE zid='{$userrow['zid']}' AND action='提成' AND addtime>'$thtime'");
	exit('{"code":0,"count":'.$count.',"count2":'.$count2.',"income_today":"'.round($income_today,2).'"}');
break;
case 'msginfo':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	if($userrow['power']==2){
		$type = array(0,2,4);
	}elseif($userrow['power']==1){
		$type = array(0,2,3);
	}else{
		$type = array(0,1);
	}
	$id=intval($_GET['id']);
	$row=$DB->getRow("SELECT * FROM pre_message WHERE id='$id' AND active=1 LIMIT 1");
	if(!$row)
		exit('{"code":-1,"msg":"当前消息不存在！"}');
	if(!in_array($row['type'],$type))
		exit('{"code":-1,"msg":"你没有权限查看此消息内容"}');
	if(!in_array($id,explode(',',$userrow['msgread']))){
		$msgread_n = $userrow['msgread'].$id.',';
		$DB->exec("UPDATE pre_message SET count=count+1 WHERE id='$id'");
		$DB->exec("UPDATE pre_site SET msgread='".$msgread_n."' WHERE zid='{$userrow['zid']}'");
	}
	$result=array("code"=>0,"msg"=>"succ","title"=>$row['title'],"type"=>$row['type'],"content"=>$row['content'],"date"=>$row['addtime']);
	exit(json_encode($result));
break;
case 'recharge':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	$value=daddslashes($_GET['value']);
	$trade_no=date("YmdHis").rand(111,999);
	if(!is_numeric($value) || !preg_match('/^[0-9.]+$/', $value))exit('{"code":-1,"msg":"提交参数错误！"}');
	if($conf['recharge_min']>0 && $value<$conf['recharge_min'])exit('{"code":-1,"msg":"最低充值'.$conf['recharge_min'].'元！"}');
	$sql="INSERT INTO `pre_pay` (`trade_no`,`tid`,`input`,`name`,`money`,`ip`,`addtime`,`status`) VALUES (:trade_no, :tid, :input, :name, :money, :ip, NOW(), 0)";
	$data=[':trade_no'=>$trade_no, ':tid'=>-1, ':input'=>(string)$userrow['zid'], ':name'=>'在线充值余额', ':money'=>$value, ':ip'=>$clientip];
	if($DB->exec($sql, $data)){
		exit('{"code":0,"msg":"提交订单成功！","trade_no":"'.$trade_no.'","money":"'.$value.'","name":"在线充值余额"}');
	}else{
		exit('{"code":-1,"msg":"提交订单失败！'.dbErrorMsg($DB->error()).'"}');
	}
break;
case 'setClass':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	$cid=intval($_GET['cid']);
	$active=intval($_GET['active']);
	$classhide = explode(',',$userrow['class']);
	if($active == 1 && in_array($cid, $classhide)){
		$classhide = array_diff($classhide, array($cid));
	}elseif($active == 0 && !in_array($cid, $classhide)){
		$classhide[] = $cid;
	}
	$class = implode(',',$classhide);
	$DB->exec("UPDATE `pre_site` SET `class`='{$class}' WHERE zid='{$userrow['zid']}'");
	exit('{"code":0}');
break;
case 'uploadimg':
		if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
		if(!$conf['workorder_pic'])exit('{"code":-1,"msg":"未开启上传图片功能"}');
		if($_POST['do']=='upload'){
			// 检查文件类型和大小
			$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp');
			$file_type = $_FILES['file']['type'];
			$file_ext = strtolower(substr($_FILES['file']['name'], strripos($_FILES['file']['name'], '.') + 1));
			$allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp');
			$max_size = 2 * 1024 * 1024; // 2MB
			
			if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
				exit('{"code":-1,"msg":"只允许上传JPG、PNG、GIF、WEBP、BMP格式的图片文件！"}');
			}
			
			if ($_FILES['file']['size'] > $max_size) {
				exit('{"code":-1,"msg":"文件大小不能超过2MB！"}');
			}
			
			// 检查文件是否为真实图片
			$image_info = getimagesize($_FILES['file']['tmp_name']);
			if (!$image_info) {
				exit('{"code":-1,"msg":"请上传真实的图片文件！"}');
			}
			
			$filename = md5_file($_FILES['file']['tmp_name']).'.png';
			$fileurl = 'assets/img/workorder/'.$filename;
			if(copy($_FILES['file']['tmp_name'], ROOT.$fileurl)){
				compress_image(ROOT.$fileurl); // 自动压缩，加快前端加载
				exit('{"code":0,"msg":"succ","url":"'.$fileurl.'"}');
			}else{
				exit('{"code":-1,"msg":"上传失败，请确保有本地写入权限"}');
			}
		}
		exit('{"code":-1,"msg":"null"}');
	break;
case 'usekm':
	if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
	if(!$conf['fenzhan_jiakuanka'])exit('{"code":-1,"msg":"未开启使用加款卡功能"}');
	$km=trim(daddslashes($_POST['km']));
	$myrow=$DB->getRow("SELECT * FROM pre_kms WHERE km=:km LIMIT 1", [':km' => $km]);
	if(!$myrow)
	{
		exit('{"code":-1,"msg":"此卡密不存在！"}');
	}
	elseif($myrow['status']==1){
		exit('{"code":-1,"msg":"此卡密已被使用！"}');
	}
	$money = $myrow['money'];
	if($DB->exec("UPDATE `pre_kms` SET `status`=1 WHERE `kid`='{$myrow['kid']}'")){
		$DB->exec("UPDATE `pre_kms` SET `zid` ='{$userrow['zid']}',`usetime` ='".$date."' WHERE `kid`='{$myrow['kid']}'");
		$rs = changeUserMoney($userrow['zid'], $money, true, '充值', '你使用加款卡充值了'.$money.'元余额');
		if($rs){
			exit('{"code":0,"msg":"成功充值'.$money.'元余额！"}');
		}
	}
	exit('{"code":-1,"msg":"充值失败'.dbErrorMsg($DB->error()).'"}');
break;
case 'gettool':
case 'getsubclass':
// 转发到主目录的ajax.php处理
// 保存当前的act值
$_GET['act'] = $act;
// 直接包含主目录的ajax.php文件
include('../ajax.php');
exit();
break;
case 'latest_orders':
    if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');
    $zid = $userrow['zid'];
    $limit = intval($_GET['limit']);
    if($limit <= 0 || $limit > 50) $limit = 8;

    $orders = [];
    $rs = $DB->query("SELECT o.id, o.money, o.addtime, o.status, t.name FROM pre_orders o LEFT JOIN pre_tools t ON o.tid=t.tid WHERE o.zid='{$zid}' ORDER BY o.id DESC LIMIT {$limit}");
    while($row = $rs->fetch()){
        $status_map = [0=>'待付款',1=>'待处理',2=>'已完成',3=>'已退款',4=>'已撤销'];
        $status_text = isset($status_map[$row['status']]) ? $status_map[$row['status']] : '未知';
        $status_class = $row['status']==2 ? 'success' : ($row['status']==3 ? 'danger' : ($row['status']==0 ? 'warning' : 'info'));
        $orders[] = [
            'ddno' => $row['id'],
            'name' => htmlspecialchars($row['name'] ?: '未知商品'),
            'money' => round(floatval($row['money']), 2),
            'addtime' => $row['addtime'],
            'status' => $status_text,
            'status_class' => $status_class
        ];
    }
    exit(json_encode(['code'=>0, 'orders'=>$orders]));
break;
case 'sales_stats':
    if(!$islogin2)exit('{"code":-1,"msg":"未登录"}');

    $days = intval($_GET['days']);
    if($days <= 0 || $days > 90) $days = 7;

    $zid = $userrow['zid'];
    $start_date = date('Y-m-d 00:00:00', strtotime("-".($days-1)." days"));
    $end_date = date('Y-m-d 23:59:59');

    // 初始化数据数组
    $date_data = [];
    for($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $date_data[$date] = [
            'orders' => 0,
            'income' => 0,
            'profit' => 0,
            'visits' => 0
        ];
    }

    // 单次查询获取订单统计数据
    $order_stats = $DB->query("SELECT 
        DATE(addtime) as date,
        COUNT(*) as orders,
        SUM(CASE WHEN status IN (1,2) THEN money ELSE 0 END) as income,
        SUM(CASE WHEN status IN (1,2) THEN money - cost ELSE 0 END) as profit
        FROM pre_orders 
        WHERE zid='{$zid}' AND addtime >= '{$start_date}' AND addtime <= '{$end_date}'
        GROUP BY DATE(addtime)
    ");

    while($row = $order_stats->fetch()) {
        if(isset($date_data[$row['date']])) {
            $date_data[$row['date']]['orders'] = intval($row['orders']);
            $date_data[$row['date']]['income'] = round(floatval($row['income']), 2);
            $date_data[$row['date']]['profit'] = round(floatval($row['profit']), 2);
        }
    }

    // 单次查询获取访问量数据
    $visit_stats = $DB->query("SELECT date, visits FROM shua_visit_statistics WHERE date >= '".date('Y-m-d', strtotime("-".($days-1)." days"))."' AND date <= '".date('Y-m-d')."'");
    while($row = $visit_stats->fetch()) {
        if(isset($date_data[$row['date']])) {
            $date_data[$row['date']]['visits'] = intval($row['visits']);
        }
    }

    // 构建返回数据
    $trend_dates = [];
    $trend_orders = [];
    $trend_income = [];
    $trend_profit = [];
    $trend_visits = [];

    foreach($date_data as $date => $data) {
        $trend_dates[] = date('m-d', strtotime($date));
        $trend_orders[] = $data['orders'];
        $trend_income[] = $data['income'];
        $trend_profit[] = $data['profit'];
        $trend_visits[] = $data['visits'];
    }

    // 今日 / 昨日汇总（取 trend 数组末尾元素）
    $today_orders = end($trend_orders);
    $today_income = end($trend_income);
    $today_profit = end($trend_profit);
    $today_visits = end($trend_visits);
    $yest_income = count($trend_income) >= 2 ? $trend_income[count($trend_income)-2] : 0;
    $yest_profit = count($trend_profit) >= 2 ? $trend_profit[count($trend_profit)-2] : 0;
    $yest_visits = count($trend_visits) >= 2 ? $trend_visits[count($trend_visits)-2] : 0;

    // 待结算金额：已支付(status=1)但未完成的订单利润
    $pending = $DB->getColumn("SELECT SUM(money - cost) FROM pre_orders WHERE zid='{$zid}' AND status=1");
    $pending = round(floatval($pending), 2);

    // 商品数
    $goods_count = intval($DB->getColumn("SELECT COUNT(*) FROM pre_tools WHERE active=1"));

    // 今日 UV / PV（从访问统计表）
    $today = date('Y-m-d');
    $visit_today = $DB->getRow("SELECT visits, ip_count FROM shua_visit_statistics WHERE date='{$today}' LIMIT 1");
    $pv = $visit_today ? intval($visit_today['visits']) : 0;
    $uv = $visit_today ? intval($visit_today['ip_count']) : 0;
    if($today_visits > $pv) $pv = $today_visits;

    // 昨日 UV/PV 用于同比
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $visit_yest = $DB->getRow("SELECT visits FROM shua_visit_statistics WHERE date='{$yesterday}' LIMIT 1");
    $yest_pv = $visit_yest ? intval($visit_yest['visits']) : 0;

    $result = [
        'code' => 0,
        'trend' => [
            'dates' => $trend_dates,
            'orders' => $trend_orders,
            'income' => $trend_income,
            'profit' => $trend_profit,
            'visits' => $trend_visits
        ],
        'today' => [
            'orders' => $today_orders,
            'income' => round(floatval($today_income), 2),
            'profit' => round(floatval($today_profit), 2),
            'visits' => $today_visits
        ],
        'yesterday' => [
            'income' => round(floatval($yest_income), 2),
            'profit' => round(floatval($yest_profit), 2),
            'visits' => $yest_visits
        ],
        'pending' => $pending,
        'goods_count' => $goods_count,
        'uv' => $uv,
        'pv' => $pv,
        'yest_pv' => $yest_pv
    ];

    exit(json_encode($result));
    break;
default:
exit('{"code":-4,"msg":"No Act"}');
break;
}