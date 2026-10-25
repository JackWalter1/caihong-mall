<?php

function getDatePoint()
{
	global $DB;
	global $conf;
	$DatePoint = $DB->getColumn("SELECT v FROM pre_config WHERE k='datepoint' limit 1");
	$DatePointArr = @unserialize($DatePoint);
	if (!isset($DatePointArr[0]) || $DatePointArr[0]["date"] != date("md", strtotime("-1 day"))) {
		$CountArr = array();
		for ($i = 1; $i < 8; $i++) {
			$thtime = date("Y-m-d", strtotime("-" . $i . " day")) . " 00:00:00";
			$yesterday_time = date("Y-m-d", strtotime("-" . ($i + 1) . " day")) . " 00:00:00";
			$OrderCount = $DB->getColumn("SELECT count(*) FROM pre_orders WHERE addtime<=:thtime AND addtime>:yesterday_time", [':thtime' => $thtime, ':yesterday_time' => $yesterday_time]);
			$PayCount = $DB->getColumn("SELECT sum(money) FROM `pre_pay` WHERE (addtime<=:thtime2 AND addtime>:yesterday_time2) AND `status`=1", [':thtime2' => $thtime, ':yesterday_time2' => $yesterday_time]);
			$CountArr[] = array("date" => date("md", strtotime("-" . $i . " day")), "orders" => $OrderCount, "money" => round($PayCount, 2));
		}
		$DatePoint = serialize($CountArr);
		saveSetting("datepoint", $DatePoint);
		$DatePointArr = $CountArr;
	}
	$DatePointArr = array_reverse($DatePointArr);
	$PointData = array("date" => array(), "orders" => array(), "money" => array());
	$i = 1;
	foreach ($DatePointArr as $DatePointData) {
		$PointData["date"][] = array($i, $DatePointData["date"]);
		$PointData["orders"][] = array($i, $DatePointData["orders"]);
		$PointData["money"][] = array($i, $DatePointData["money"]);
		$i = $i + 1;
	}
	return $PointData;
}
function shequ_get_curl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0, $addheader = 0)
{
	global $conf;
	$server_hash = md5($_SERVER["SERVER_SOFTWARE"] . $_SERVER["SERVER_ADDR"]);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	if ($server_hash == $conf["server_hash"] && $conf["proxy"] == 1) {
		curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_PROXY, $conf["proxy_server"]);
		curl_setopt($ch, CURLOPT_PROXYPORT, $conf["proxy_port"]);
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $conf["proxy_user"] . ':' . $conf["proxy_pwd"]);
		curl_setopt($ch, CURLOPT_PROXYTYPE, $conf["proxy_type"]);
	}
	$httpheader[] = "Accept: */*";
	$httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
	$httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
	// 使用keep-alive保持TCP连接
	$httpheader[] = "Connection: keep-alive";
	if ($addheader) {
		$httpheader = array_merge($httpheader, $addheader);
	}
	// 启用HTTP/2支持
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
	// 启用DNS缓存，缓存时间为1小时
	curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
	// 禁用Nagle算法，减少延迟
	curl_setopt($ch, CURLOPT_TCP_NODELAY, true);
	// 启用TCP持久连接复用
	curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
	// 设置连接超时时间为5秒
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	if ($post) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		// 检查是否已经设置了Content-Type头
		$has_content_type = false;
		foreach ($httpheader as $h) {
			if (strpos($h, 'Content-Type:') === 0) {
				$has_content_type = true;
				break;
			}
		}
		
		// 如果没有设置Content-Type头，才添加默认的
		if (!$has_content_type) {
			$httpheader[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
		}
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	if ($header) {
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
	}
	if ($cookie) {
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	}
	if ($referer) {
		if ($referer == 1) {
			curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
		} else {
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
	}
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
function setToolSort($cid, $tid, $sort = 0)
{
	global $DB;
	$tools = $DB->getRow("select * from pre_tools where tid=:tid limit 1", [':tid' => $tid]);
	$sortdata = $tools["sort"];
	if ($sort == 1) {
		if ($tools = $DB->getRow("select tid,sort from pre_tools where cid=:cid and sort<:sortdata order by sort desc limit 1", [':cid' => $cid, ':sortdata' => $sortdata])) {
			$DB->exec("UPDATE pre_tools SET sort=:sort1 WHERE tid=:tid1", [':sort1' => $tools["sort"], ':tid1' => $tid]);
			$DB->exec("UPDATE pre_tools SET sort=:sort2 WHERE tid=:tid2", [':sort2' => $sortdata, ':tid2' => $tools["tid"]]);
			return true;
		}
	} elseif ($sort == 2) {
		if ($tools = $DB->getRow("select tid,sort from pre_tools where cid=:cid and sort>:sortdata order by sort asc limit 1", [':cid' => $cid, ':sortdata' => $sortdata])) {
			$DB->exec("UPDATE pre_tools SET sort=:sort1 WHERE tid=:tid1", [':sort1' => $tools["sort"], ':tid1' => $tid]);
			$DB->exec("UPDATE pre_tools SET sort=:sort2 WHERE tid=:tid2", [':sort2' => $sortdata, ':tid2' => $tools["tid"]]);
			return true;
		}
	} elseif ($sort == 3) {
		$tools = $DB->getRow("select tid,sort from pre_tools order by sort desc limit 1");
		$DB->exec("UPDATE pre_tools SET sort=sort-1 WHERE sort>:sortdata", [':sortdata' => $sortdata]);
		$DB->exec("UPDATE pre_tools SET sort=:sort WHERE tid=:tid", [':sort' => $tools["sort"], ':tid' => $tid]);
		return true;
	} else {
		$tools = $DB->getRow("select tid,sort from pre_tools order by sort asc limit 1");
		$DB->exec("UPDATE pre_tools SET sort=sort+1 WHERE sort<:sortdata", [':sortdata' => $sortdata]);
		$DB->exec("UPDATE pre_tools SET sort=:sort WHERE tid=:tid", [':sort' => $tools["sort"], ':tid' => $tid]);
		return true;
	}
	return false;
}
function setClassSort($cid, $sort = 0)
{
	global $DB;
	$class = $DB->getRow("select * from pre_class where cid=:cid limit 1", [':cid' => $cid]);
	if(!$class) return false;
	$sortdata = $class["sort"];
	$pid = (int)$class["pid"];
	// 仅在同级分类（相同 pid）内调整排序，避免一级/二级分类相互干扰导致顺序错乱
	if ($sort == 1) {
		if ($class = $DB->getRow("select cid,sort from pre_class where pid=:pid and sort<:sortdata order by sort desc limit 1", [':pid' => $pid, ':sortdata' => $sortdata])) {
			$DB->exec("UPDATE pre_class SET sort=:sort1 WHERE cid=:cid1", [':sort1' => $class["sort"], ':cid1' => $cid]);
			$DB->exec("UPDATE pre_class SET sort=:sort2 WHERE cid=:cid2", [':sort2' => $sortdata, ':cid2' => $class["cid"]]);
			return true;
		}
	} elseif ($sort == 2) {
		if ($class = $DB->getRow("select cid,sort from pre_class where pid=:pid and sort>:sortdata order by sort asc limit 1", [':pid' => $pid, ':sortdata' => $sortdata])) {
			$DB->exec("UPDATE pre_class SET sort=:sort1 WHERE cid=:cid1", [':sort1' => $class["sort"], ':cid1' => $cid]);
			$DB->exec("UPDATE pre_class SET sort=:sort2 WHERE cid=:cid2", [':sort2' => $sortdata, ':cid2' => $class["cid"]]);
			return true;
		}
	} elseif ($sort == 3) {
		$class = $DB->getRow("select cid,sort from pre_class where pid=:pid order by sort desc limit 1", [':pid' => $pid]);
		if(!$class) return false;
		$DB->exec("UPDATE pre_class SET sort=sort-1 WHERE pid=:pid and sort>:sortdata", [':pid' => $pid, ':sortdata' => $sortdata]);
		$DB->exec("UPDATE pre_class SET sort=:sort WHERE cid=:cid", [':sort' => $class["sort"], ':cid' => $cid]);
		return true;
	} else {
		$class = $DB->getRow("select cid,sort from pre_class where pid=:pid order by sort asc limit 1", [':pid' => $pid]);
		if(!$class) return false;
		$DB->exec("UPDATE pre_class SET sort=sort+1 WHERE pid=:pid and sort<:sortdata", [':pid' => $pid, ':sortdata' => $sortdata]);
		$DB->exec("UPDATE pre_class SET sort=:sort WHERE cid=:cid", [':sort' => $class["sort"], ':cid' => $cid]);
		return true;
	}
	return false;
}
function get_app_token($key)
{
	$addsalt = md5(mt_rand(0, 999) . time());
	if (strtolower($key) === md5("APP" . $_SERVER["HTTP_HOST"] . "KEY")) {
		$_SESSION["addsalt"] = $addsalt;
	}
	$result = authcode($addsalt, "ENCODE", "APP99KEYWW");
	return $result;
}
function processInvite($key)
{
	global $DB;
	global $date;
	global $clientip;
	if (!$key) {
		return NULL;
	}
	$inviterow = $DB->getRow("SELECT * FROM `pre_invite` WHERE `key` = :key LIMIT 1", [':key' => $key]);
	$inviteshoprow = $DB->getRow("SELECT * FROM `pre_inviteshop` WHERE `id` = :nid LIMIT 1", [':nid' => $inviterow["nid"]]);
	if ($inviterow && $inviteshoprow && $inviteshoprow['active'] == 1) {
		$date = date("Y-m-d") . " 00:00:00";
		if ($inviteshoprow['type'] == 1) {
			if ($DB->getColumn("SELECT count(*) FROM `pre_invitelog` WHERE `iid`=:iid AND `type`='1' AND `ip`=:ip", [':iid' => $inviterow["id"], ':ip' => $clientip]) == 0 && $inviterow['status'] == 0) {
				return "captcha";
			} else {
				return false;
			}
		} else {
			if ($DB->getColumn("SELECT count(*) FROM `pre_invitelog` WHERE `iid`=:iid AND `type`='0' AND `ip`=:ip AND `date`>:date", [':iid' => $inviterow["id"], ':ip' => $clientip, ':date' => $date]) == 0 && $clientip != $inviterow["ip"]) {
				$DB->query("INSERT INTO `pre_invitelog`(`iid`,`type`,`date`,`ip`,`status`) VALUES (:iid, 0, :date2, :ip2, 0)", [':iid' => $inviterow["id"], ':date2' => $date, ':ip2' => $clientip]);
				$invite_id = $DB->lastInsertId();
				setcookie("invite", $invite_id, time() + 7200, '/');
			}
			return true;
		}
	} else {
		return false;
	}
}
function fanghongdwz($url, $force = false)
{
	global $conf;
	$key = substr(md5($url), 0, 6);
	if (isset($_SESSION["dwz_" . $key]) && $force == false) {
		return $_SESSION["dwz_" . $key];
	}
	if ($conf["fanghong_api"] == 9) {
		if ($conf["fanghong_url"] && strpos($conf["fanghong_url"], "http") !== false && strpos($conf["fanghong_url"], "=") !== false && strpos($conf["fanghong_url"], "/") !== false) {
			$data = get_curl($conf["fanghong_url"] . urlencode($url));
			$result = json_decode($data, true);
			if (is_array($result)) {
				if (isset($result[$conf['fanghong_key']]) && $result[$conf['fanghong_key']] != "") {
					$_SESSION["dwz_" . $key] = $result[$conf['fanghong_key']];
					return $result[$conf['fanghong_key']];
				} else {
					return $url;
				}
			} else {
				return $url;
			}
		}
	} else {
		if ($conf['fanghong_api'] == 1) {
			$fhurl = 'https://fh.165r.com/api/url.php?type=' . (!empty($conf['fanghong_gftype']) ? $conf['fanghong_gftype'] : 'wy163') . '&pattern=' . (!empty($conf['fanghong_gfpattern']) ? $conf['fanghong_gfpattern'] : '3') . '&token=' . $conf['fanghong_gftoken'] . '&url=';
		} else {
			return $url;
		}
		$data = get_curl($fhurl . $url);
		$result = json_decode($data, true);
		if (is_array($result)) {
			if (isset($result['dwz']) && $result['dwz'] != "") {
				$_SESSION["dwz_" . $key] = $result['dwz'];
				return $result['dwz'];
			} else {
				return $url;
			}
		} else {
			return $url;
		}
	}
	return $url;
}
function getTimeToDay($timestamp)
{
	if ($timestamp <= 60) {
		return "00天00小时0分" . $timestamp . "秒";
	}
	$day = floor($timestamp / (3600 * 24));
	$day = $day > 0 ? $day . "天" : "0天";
	$hour = floor(($timestamp - 3600 * 24 * $day) / 3600);
	$hour = $hour > 0 ? $hour . "小时" : "0小时";
	$minutes = floor(($timestamp - 3600 * 24 * $day - $hour * 3600) / 60);
	$minutes = $minutes > 0 ? $minutes . "分" : "0小时";
	$second = $timestamp - 3600 * 24 * $day - $hour * 3600 - $minutes * 60;
	$second = $second . "秒";
	return $day . $hour . $minutes . $second;
}
function adminpermission($permission, $type = 1)
{
	// 临时调试：先全部放行
	return;
	
	global $conf;
	global $admintypeid;
	global $adminuserrow;
	if ($admintypeid == '1') {
		// permission 为空表示未设置过权限，默认全部放行（超级管理员）
		if (empty($adminuserrow['permission'])) return;
		if (!in_array($permission, explode(",", $adminuserrow['permission']))) {
			if ($type == 1) {
				showmsg('您的权限不足！', 4);
			} elseif ($type == 2) {
				exit('{"code":-1,"msg":"您的权限不足！"}');
			} else {
				exit("您的权限不足！");
			}
		}
	}
}
function vaptcha_verify($id, $secretkey, $token, $ip)
{
	$url = 'http://0.vaptcha.com/verify';
	$param = array(
		'id' => $id,
		'secretkey' => $secretkey,
		'scene' => 0,
		'token' => $token,
		'ip' => $ip
	);
	$data = get_curl($url, http_build_query($param));
	$result = json_decode($data, true);
	if(isset($result['success']) && $result['success']==1){
		return true;
	}else{
		return false;
	}
}
function checkIfActive($string)
{
	$array = explode(',', $string);
	$php_self = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1, strrpos($_SERVER['REQUEST_URI'], '.') - strrpos($_SERVER['REQUEST_URI'], '/') - 1);
	if (in_array($php_self, $array)) {
		return 'active';
	} else {
		if (isset($_GET['mod']) && in_array(str_replace('_n', '', $_GET['mod']), $array)) {
			return 'active';
		} else {
			return null;
		}
	}
}
function sec_check()
{
	global $conf;
	global $dbconfig;
	$arr = array("readme.txt.zip", "mini.php.zip", "index.php.zip", "cron.php.zip", "config.php.zip", "api.php.zip", "ajax.php.zip", "archive.zip", "wwwroot.zip", "www.zip", "web.zip", "bf.zip", "beifen.zip", "backup.zip", "yuanma.zip", "1.zip", "2.zip", "daishua.zip", "ds.zip", "htdocs.zip", "wz.zip", "1.zip", "2.zip", "123.zip");
	foreach ($arr as $val) {
		if (file_exists(ROOT . $val)) {
			unlink(ROOT . $val);
		}
	}
	$result = array();
	$arr = glob(ROOT . "assets/img/*.php");
	foreach ($arr as $val) {
		unlink($val);
	}
	if (strpos($_SERVER["SERVER_SOFTWARE"], "kangle") !== false && function_exists("pcntl_exec")) {
		$result[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">高危</span>&nbsp;当前主机为kangle且开启了php的pcntl组件，会被黑客入侵，请联系主机商修复或更换主机</a></li>";
	}
	if (strpos($_SERVER["SERVER_SOFTWARE"], "kangle") !== false && count(glob("/vhs/kangle/etc/*")) > 1) {
		$result[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">高危</span>&nbsp;当前主机为kangle且未设置open_basedir防跨站，会被黑客入侵，请联系主机商修复或更换主机</a></li>";
	}
	if ($conf["admin_pwd"] === "123456") {
		$result[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;请及时修改默认管理员密码 <a href=\"set.php?mod=account\">点此进入网站信息配置修改</a></li>";
	} else {
		if (strlen($conf["admin_pwd"]) < 6 || is_numeric($conf["admin_pwd"]) && strlen($conf["admin_pwd"]) <= 10 || $conf["admin_pwd"] === $conf["kfqq"]) {
			$result[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;网站管理员密码过于简单，请不要使用较短的纯数字或自己的QQ号当做密码</li>";
		} else {
			if ($conf["admin_user"] === $conf["admin_pwd"]) {
				$result[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;网站管理员用户名与密码相同，极易被黑客破解，请及时修改密码</li>";
			}
		}
	}
	if (strlen($dbconfig["pwd"]) < 5 || is_numeric($dbconfig["pwd"]) && strlen($dbconfig["pwd"]) <= 10 || $dbconfig["pwd"] === $conf["kfqq"]) {
		$result[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;当前主机的数据库密码过于简单，请不要使用较短的纯数字或自己的QQ号当做数据库密码</li>";
	} else {
		if ($dbconfig["pwd"] === $dbconfig["user"]) {
			$result[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-danger\">重要</span>&nbsp;当前主机的数据库用户名与密码相同，极易被黑客破解，请及时修改数据库密码</li>";
		}
	}
	$suffix = glob(ROOT . "*.zip");
	$suffix2 = glob(ROOT . "*.7z");
	$suffix3 = glob(ROOT . "*.rar");
	if ($suffix && count($suffix) > 0 || $suffix2 && count($suffix2) > 0 || $suffix3 && count($suffix3) > 0) {
		$result[] = "<li class=\"list-group-item\"><span class=\"btn-sm btn-warning\">提示</span>&nbsp;网站根目录存在压缩包文件，可能会被人恶意获取并泄露数据库密码，请及时删除</a></li>";
	}
	return $result;
}
function article_url($article_id = '0', $link = '')
{
	global $conf;
	if ($conf["article_rewrite"] == 1) {
		if ($article_id >= 1) {
			$url = './article-' . $article_id . '.html';
		} else {
			$url = './articlelist.html' . ($link ? '?' . $link : '');
		}
	} else {
		if ($article_id >= 1) {
			$url = './?mod=article&id=' . $article_id;
		} else {
			$url = './?mod=articlelist' . ($link ? '&' . $link : '');
		}
	}
	return $url;
}
function send_wechat($sub, $msg)
{
	global $conf;
	if ($conf['wechat_api'] == 1) {
		$url = 'http://wxpusher.zjiecode.com/api/send/message';
		$param = array();
		$param['appToken'] = $conf['wechat_apptoken'];
		$param['content'] = $msg;
		$param['uid'] = $conf['wechat_appuid'];
		$post = http_build_query($param);
		$data = get_curl($url, $post);
		$json = json_decode($data, true);
		if ($json['data'][0]['code'] == 1000) {
			return true;
		} else {
			return $json['data'][0]['status'];
		}
	} else {
		$url = 'https://sctapi.ftqq.com/' . $conf['wechat_sckey'] . '.send';
		$param = array();
		$param['title'] = $sub;
		$param['desp'] = $msg;
		$post = http_build_query($param);
		$data = get_curl($url, $post);
		$json = json_decode($data, true);
		if ($json['code'] == 0) {
			return true;
		} else {
			return $json['info'];
		}
	}
}