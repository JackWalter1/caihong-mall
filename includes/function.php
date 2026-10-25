<?php

function curl_get($url)
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        $httpheader = array();
        $httpheader[] = "Accept: */*";
        $httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn; R815T Build/JOP40D) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    } elseif (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create(array(
            'http' => array('timeout' => 30, 'header' => "User-Agent: Mozilla/5.0\r\n"),
            'ssl' => array('verify_peer' => false, 'verify_peer_name' => false),
        ));
        return @file_get_contents($url, false, $ctx);
    }
    return '';
}
function get_curl($url,$post=0,$referer=0,$cookie=0,$header=0,$ua=0,$nobaody=0,$addheader=0){

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

	$httpheader = array();
	$httpheader[] = "Accept: */*";

	$httpheader[] = "Accept-Encoding: gzip,deflate";

	$httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";

	$httpheader[] = "Connection: close";

	// 添加默认 Content-Type 头
	if($post){
		$httpheader[] = "Content-Type: application/x-www-form-urlencoded";
	}

	if($addheader){

		$httpheader = array_merge($httpheader, $addheader);

	}

	curl_setopt($ch, CURLOPT_TIMEOUT, 30);

	if($post){

		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

	}

	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);

	if($header){

		curl_setopt($ch, CURLOPT_HEADER, TRUE);

	}

	if($cookie){

		curl_setopt($ch, CURLOPT_COOKIE, $cookie);

	}

	if($referer){

		if($referer==1){

			curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');

		}else{

			curl_setopt($ch, CURLOPT_REFERER, $referer);

		}

	}

	if($ua){

		curl_setopt($ch, CURLOPT_USERAGENT,$ua);

	}else{

		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');

	}

	if($nobaody){

		curl_setopt($ch, CURLOPT_NOBODY,1);

	}

	curl_setopt($ch, CURLOPT_ENCODING, "gzip");

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

	$ret = curl_exec($ch);
	$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	


	curl_close($ch);

	return $ret;

}





function real_ip($type=0){

$ip = $_SERVER['REMOTE_ADDR'];

if($type<=0 && isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {

	foreach ($matches[0] AS $xip) {

		if (filter_var($xip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {

			$ip = $xip;

			break;

		}

	}

} elseif ($type<=0 && isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {

	$ip = $_SERVER['HTTP_CLIENT_IP'];

} elseif ($type<=1 && isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {

	$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];

} elseif ($type<=1 && isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {

	$ip = $_SERVER['HTTP_X_REAL_IP'];

}

return $ip;

}



function get_ip_city($ip)

{

    $url = 'http://whois.pconline.com.cn/ipJson.jsp?json=true&ip=';

    $city = curl_get($url . $ip);

	$city = mb_convert_encoding($city, "UTF-8", "GB2312");

    $city = json_decode($city, true);

    if ($city['city']) {

        $location = $city['pro'].$city['city'];

    } else {

        $location = $city['pro'];

    }

	if($location){

		return $location;

	}else{

		return false;

	}

}



function daddslashes($string) {

	if(is_array($string)) {

		foreach($string as $key => $val) {

			$string[$key] = daddslashes($val);

		}

	} else {

		$string = addslashes($string);

	}

	return $string;

}



function strexists($string, $find) {

	return !(strpos($string, $find) === FALSE);

}



function dstrpos($string, $arr) {

	if(empty($string)) return false;

	foreach((array)$arr as $v) {

		if(strpos($string, $v) !== false) {

			return true;

		}

	}

	return false;

}



function checkmobile() {

	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

	$ualist = array('android', 'midp', 'nokia', 'mobile', 'iphone', 'ipod', 'blackberry', 'windows phone');

	if((dstrpos($useragent, $ualist) || strexists($_SERVER['HTTP_ACCEPT'], "VND.WAP") || (isset($_SERVER['HTTP_VIA']) && strexists($_SERVER['HTTP_VIA'],'wap')))){

		return true;

	}else{

		return false;

	}

}



function checkEmail($value)

{

	if (preg_match("/^[\w\.\-]+@\w+([\.\-]\w+)*\.\w+$/", $value) && strlen($value) <= 60) {

		return true;

	} else {

		return false;

	}

}



function getSubstr($str, $leftStr, $rightStr)

{

	$left = strpos($str, $leftStr);

	$start = $left+strlen($leftStr);

	$right = strpos($str, $rightStr, $start);

	if($left < 0) return '';

	if($right>0){

		return substr($str, $start, $right-$start);

	}else{

		return substr($str, $start);

	}

}



function send_mail($to, $sub, $msg)

{

	global $conf;
	// 邮件配置默认值兜底（避免配置键未初始化时 Undefined index 报错）
	if(!isset($conf['mail_cloud']))$conf['mail_cloud']=0;
	if(!isset($conf['mail_name']))$conf['mail_name']='';
	if(!isset($conf['mail_name2']))$conf['mail_name2']='';
	if(!isset($conf['mail_recv']))$conf['mail_recv']='';
	if(!isset($conf['mail_port']))$conf['mail_port']='';
	if(!isset($conf['mail_smtp']))$conf['mail_smtp']='';
	if(!isset($conf['mail_pwd']))$conf['mail_pwd']='';
	if(!isset($conf['mail_apiuser']))$conf['mail_apiuser']='';
	if(!isset($conf['mail_apikey']))$conf['mail_apikey']='';
	if(!isset($conf['sitename']))$conf['sitename']='';

	if($conf['mail_cloud']==1){

		$mail = new \lib\mail\Sendcloud($conf['mail_apiuser'], $conf['mail_apikey']);

		return $mail->send($to, $sub, $msg, $conf['mail_name2'], $conf['sitename']);

	}elseif($conf['mail_cloud']==2){

		$mail = new \lib\mail\Aliyun($conf['mail_apiuser'], $conf['mail_apikey']);

		return $mail->send($to, $sub, $msg, $conf['mail_name2'], $conf['sitename']);

	}else{

		if(!$conf['mail_name'] || !$conf['mail_port'] || !$conf['mail_smtp'] || !$conf['mail_pwd'])return false;

		$port = intval($conf['mail_port']);

		$mail = new \lib\mail\PHPMailer\PHPMailer(true);

		try{

			$mail->SMTPDebug = 0;

			$mail->CharSet = 'UTF-8';

			$mail->Timeout = 5;

			$mail->isSMTP();

			$mail->Host = $conf['mail_smtp'];

			$mail->SMTPAuth = true;

			$mail->Username = $conf['mail_name'];

			$mail->Password = $conf['mail_pwd'];

			if($port == 587) $mail->SMTPSecure = 'tls';

			else if($port >= 465) $mail->SMTPSecure = 'ssl';

			else $mail->SMTPAutoTLS = false;

			$mail->Port = intval($conf['mail_port']);

			$mail->setFrom($conf['mail_name'], $conf['sitename']);

			$mail->addAddress($to);

			$mail->addReplyTo($conf['mail_name'], $conf['sitename']);

			$mail->isHTML(true);

			$mail->Subject = $sub;

			$mail->Body = $msg;

			$mail->send();

			return true;

		} catch (Exception $e) {

			return $mail->ErrorInfo;

		}

	}

}



function getSetting($k, $force = false)

{

	global $DB,$CACHE;

	if($force) return $DB->getColumn("SELECT v FROM pre_config WHERE k=:k LIMIT 1", [':k'=>$k]);

	$cache = $CACHE->get($k);

	return $cache[$k];

}



function saveSetting($k, $v)

{

	global $DB;

	return $DB->exec("REPLACE INTO pre_config SET v=:v,k=:k", [':v'=>$v, ':k'=>daddslashes($k)]);

}



// 图片自动压缩（GD）：上传后压缩图片体积，加快前端加载
// $src 源路径；$dst 目标路径（默认覆盖源）；$quality JPEG/WebP 质量(1-100)，PNG 自动转为压缩级别
function compress_image($src, $dst = null, $quality = 90)
{
	if(!function_exists('imagecreatefromstring')) return false;
	if(!is_file($src)) return false;
	$info = @getimagesize($src);
	if(!$info || !in_array($info['mime'], array('image/jpeg','image/png','image/webp'))) return false;
	$img = @imagecreatefromstring(file_get_contents($src));
	if(!$img) return false;
	if(!$dst) $dst = $src;
	$ok = false;
	switch($info['mime']){
		case 'image/png':
			imagealphablending($img, false);
			imagesavealpha($img, true);
			$level = max(0, min(9, intval(round((100 - $quality) / 10))));
			$ok = imagepng($img, $dst, $level);
			break;
		case 'image/webp':
			$ok = imagewebp($img, $dst, $quality);
			break;
		default:
			$ok = imagejpeg($img, $dst, $quality);
	}
	imagedestroy($img);
	return $ok;
}



function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;

	$key = md5($key);

	$keya = md5(substr($key, 0, 16));

	$keyb = md5(substr($key, 16, 16));

	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);

	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;

	$string_length = strlen($string);

	$result = '';

	$box = range(0, 255);

	$rndkey = array();

	for($i = 0; $i <= 255; $i++) {

		$rndkey[$i] = ord($cryptkey[$i % $key_length]);

	}

	for($j = $i = 0; $i < 256; $i++) {

		$j = ($j + $box[$i] + $rndkey[$i]) % 256;

		$tmp = $box[$i];

		$box[$i] = $box[$j];

		$box[$j] = $tmp;

	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {

		$a = ($a + 1) % 256;

		$j = ($j + $box[$a]) % 256;

		$tmp = $box[$a];

		$box[$a] = $box[$j];

		$box[$j] = $tmp;

		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));

	}

	if($operation == 'DECODE') {

		if(((int)substr($result, 0, 10) == 0 || (int)substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {

			return substr($result, 26);

		} else {

			return '';

		}

	} else {

		return $keyc.str_replace('=', '', base64_encode($result));

	}

}



function random($length, $numeric = 0) {

	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);

	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));

	$hash = '';

	$max = strlen($seed) - 1;

	for($i = 0; $i < $length; $i++) {

		$hash .= $seed[mt_rand(0, $max)];

	}

	return $hash;

}



function get_rand($proArr)

{

	$result = "";

	$proSum = array_sum($proArr);

	foreach ($proArr as $key => $proCur) {

		$randNum = mt_rand(1, $proSum);

		if ($randNum <= $proCur) {

			$result = $key;

			break;

		}

		$proSum -= $proCur;

	}

	unset($proArr);

	return $result;

}



function showmsg($content = '未知的异常',$type = 4,$back = false)

{

switch($type)

{

case 1:

	$panel="success";

break;

case 2:

	$panel="info";

break;

case 3:

	$panel="warning";

break;

case 4:

	$panel="danger";

break;

}



echo '<div class="panel panel-'.$panel.'">

      <div class="panel-heading">

        <h3 class="panel-title">提示信息</h3>

        </div>

        <div class="panel-body">';

echo $content;



if ($back) {

	echo '<hr/><a href="'.$back.'"><< 返回上一页</a>';

}

else

    echo '<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a>';



echo '</div>

    </div>';

exit;

}



if(!function_exists("is_https")){

	function is_https() {

		if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){

			return true;

		}elseif(isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')){

			return true;

		}elseif(isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && $_SERVER['HTTP_X_CLIENT_SCHEME'] == 'https'){

			return true;

		}elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){

			return true;

		}elseif(isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https'){

			return true;

		}elseif(isset($_SERVER['HTTP_EWS_CUSTOME_SCHEME']) && $_SERVER['HTTP_EWS_CUSTOME_SCHEME'] == 'https'){

			return true;

		}

		return false;

	}

}



function sysmsg($content = "未知的异常", $is_exit = true)

{

	echo "  \r\n	<!DOCTYPE html>\r\n	<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"zh-CN\">\r\n	<head>\r\n		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n		<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n		<title>站点提示信息</title>\r\n		<style type=\"text/css\">\r\nhtml{background:#eee}body{background:#fff;color:#333;font-family:\"微软雅黑\",\"Microsoft YaHei\",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:10px 10px 10px rgba(0,0,0,.13);box-shadow:10px 10px 10px rgba(0,0,0,.13);opacity:.8}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px \"微软雅黑\",\"Microsoft YaHei\",,sans-serif;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}h3{text-align:center}#error-page p{font-size:9px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:9px}a{color:#21759B;text-decoration:none;margin-top:-10px}a:hover{color:#D54E21}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:9px;line-height:26px;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);box-shadow:inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);vertical-align:top}.button.button-large{height:29px;line-height:28px;padding:0 12px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#222}.button:focus{-webkit-box-shadow:1px 1px 1px rgba(0,0,0,.2);box-shadow:1px 1px 1px rgba(0,0,0,.2)}.button:active{background:#eee;border-color:#999;color:#333;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}table{table-layout:auto;border:1px solid #333;empty-cells:show;border-collapse:collapse}th{padding:4px;border:1px solid #333;overflow:hidden;color:#333;background:#eee}td{padding:4px;border:1px solid #333;overflow:hidden;color:#333}\r\n		</style>\r\n	</head>\r\n	<body id=\"error-page\">\r\n		";

	echo "<h3>站点提示信息</h3>";

	echo $content;

	echo "	</body>\r\n	</html>\r\n	";
	if($is_exit) exit();
	return 0;

}



function changeUserMoney($zid, $money, $add=true, $action=null, $bz=null, $orderid=null)

{

	global $DB;

	if($money<=0)return;

	// 使用参数化查询获取旧余额 - 修复SQL注入漏洞
	$oldmoney = $DB->getColumn("SELECT rmb FROM pre_site WHERE zid=:zid LIMIT 1", [':zid' => $zid]);

	if($add == true){

		

		$newmoney = round($oldmoney+$money, 2);

	}else{

	

		$newmoney = round($oldmoney-$money, 2);

	}

	$action = addslashes($action);

	$bz = addslashes($bz);

	// 使用参数化查询更新余额 - 修复SQL注入漏洞
	$res = $DB->exec("UPDATE pre_site SET rmb=:newmoney WHERE zid=:zid", [':newmoney' => $newmoney, ':zid' => $zid]);

	if ($orderid) {
		$DB->exec("INSERT INTO `pre_points` (`zid`, `action`, `point`, `bz`, `addtime`, `orderid`) VALUES (:zid, :action, :point, :bz, NOW(), :orderid)", [':zid'=>$zid, ':action'=>$action, ':point'=>$money, ':bz'=>$bz, ':orderid'=>$orderid]);
	} else {
		$DB->exec("INSERT INTO `pre_points` (`zid`, `action`, `point`, `bz`, `addtime`) VALUES (:zid, :action, :point, :bz, NOW())", [':zid'=>$zid, ':action'=>$action, ':point'=>$money, ':bz'=>$bz]);
	}

	return $res;

}



function changeSupMoney($sid, $money, $add=true, $action=null, $bz=null)

{

    global $DB;

    if($money<=0)return;

    // 使用参数化查询获取旧余额 - 修复SQL注入漏洞
    $oldmoney = $DB->getColumn("SELECT rmb FROM pre_supplier WHERE sid=:sid LIMIT 1", [':sid' => $sid]);

    if($add == true){

       

        $newmoney = round($oldmoney+$money, 2);

    }else{

      

        $newmoney = round($oldmoney-$money, 2);

    }

    $action = addslashes($action);

    $bz = addslashes($bz);

    // 使用参数化查询更新余额 - 修复SQL注入漏洞
    $res = $DB->exec("UPDATE pre_supplier SET rmb=:newmoney WHERE sid=:sid", [':newmoney' => $newmoney, ':sid' => $sid]);

    $DB->exec("INSERT INTO `pre_suppoints` (`sid`, `action`, `point`, `bz`, `addtime`) VALUES (:sid, :action, :point, :bz, NOW())", [':sid'=>$sid, ':action'=>$action, ':point'=>$money, ':bz'=>$bz]);

    return $res;

}



function check_china(){

	$ip = gethostbyname('check.cccyun.cc');

	if($ip == '192.168.0.1'){

		return true;

	}else{

		return false;

	}

}



function yile_getSign($param, $key)

{

    $signPars = "";

    ksort($param);

    foreach ($param as $k => $v) {

        if ("sign" != $k && "" != $v) {

            $signPars .= $k . "=" . $v . "&";

        }

    }

    $signPars = trim($signPars, '&');

    $signPars .= $key;

    $sign = md5($signPars);

    return $sign;

}



function getServerIp(){

	$url = 'http://members.3322.org/dyndns/getip';

	$url2 = 'https://www.bt.cn/Api/getIpAddress';

	if($data = get_curl($url2)){

		return $data;

	}else{

		$data = get_curl($url);

		return $data;

	}

}



/**
 * 生成分类选项列表，支持提示性分类
 */
function getClassOptionList($selected = 0) {
    global $DB;
    $rs = $DB->query("SELECT * FROM pre_class WHERE active=1 ORDER BY sort ASC");
    $select = '';
    while($res = $rs->fetch()) {
        if($res['is_disabled'] == 1) {
            // 提示性分类，使用disabled属性
            $select .= '<option disabled style="color: blue;">-—'.$res['name'].'—-</option>';
        } else {
            // 普通分类
            $select .= '<option value="'.$res['cid'].'"'.($selected == $res['cid'] ? ' selected' : '').'>'.$res['name'].'</option>';
        }
    }
    return $select;
}

/* ==================== 费率与手续费中心 ==================== */

// 提现结算方式名称（1=微信 2=QQ钱包 3=支付宝）
function tixian_type_name($type)
{
    if ($type == 1) return '微信';
    if ($type == 2) return 'QQ钱包';
    return '支付宝';
}

/**
 * 读取某角色(user/sup)某结算方式的手续费配置
 * 返回：array=配置(含mode/rate/fixed/min_fee/max_fee)
 *       false=该结算方式已被停用（禁止提现）
 *       null=未配置新规则（沿用旧版 xx_tixian_rate 到账比例兜底）
 */
function tixian_fee_config($role = 'user', $pay_type = 0)
{
    global $conf;
    $prefix = ($role === 'sup') ? 'sup_' : '';
    if (!empty($conf[$prefix . 'tixian_fee_bytype']) && $pay_type > 0) {
        $types = json_decode($conf[$prefix . 'tixian_fee_types'], true);
        if (is_array($types) && isset($types[$pay_type])) {
            return (isset($types[$pay_type]['on']) && $types[$pay_type]['on'] == 1) ? $types[$pay_type] : false;
        }
    }
    $def = json_decode($conf[$prefix . 'tixian_fee_default'], true);
    if (is_array($def)) return $def;
    return null;
}

/**
 * 计算提现手续费
 * @return array(手续费, 实际到账金额)，手续费为 null 表示该结算方式已停用
 */
function calc_tixian_fee($money, $pay_type = 0, $role = 'user')
{
    global $conf;
    $cfg = tixian_fee_config($role, $pay_type);
    if ($cfg === false) return array(null, null);
    if ($cfg === null) {
        // 旧版兜底：xx_tixian_rate 为"到账比例"（98 = 扣2%）
        $prefix = ($role === 'sup') ? 'sup_' : '';
        $rate = floatval($conf[$prefix . 'tixian_rate']);
        if ($rate <= 0 || $rate > 100) $rate = 98;
        $fee = round($money * (100 - $rate) / 100, 2);
        return array($fee, round($money - $fee, 2));
    }
    $mode = isset($cfg['mode']) ? $cfg['mode'] : 'percent';
    $rate = isset($cfg['rate']) ? floatval($cfg['rate']) : 0;
    if ($mode === 'fixed') {
        $fee = isset($cfg['fixed']) ? floatval($cfg['fixed']) : 0;
    } elseif ($mode === 'mixed') {
        $fee = max(round($money * $rate / 100, 2), isset($cfg['min_fee']) ? floatval($cfg['min_fee']) : 0);
    } elseif ($mode === 'capped') {
        $max = isset($cfg['max_fee']) && floatval($cfg['max_fee']) > 0 ? floatval($cfg['max_fee']) : 99999999;
        $fee = min(round($money * $rate / 100, 2), $max);
    } else {
        $fee = round($money * $rate / 100, 2);
    }
    if ($fee < 0) $fee = 0;
    if ($fee > $money) $fee = $money;
    return array(round($fee, 2), round($money - $fee, 2));
}

// 单条手续费规则的可读描述
function tixian_fee_desc_one($cfg)
{
    $mode = isset($cfg['mode']) ? $cfg['mode'] : 'percent';
    $rate = isset($cfg['rate']) ? floatval($cfg['rate']) : 0;
    if ($mode === 'fixed') return '每笔固定收 ' . $cfg['fixed'] . ' 元';
    if ($mode === 'mixed') return '收 ' . $rate . '%（最低 ' . (isset($cfg['min_fee']) ? $cfg['min_fee'] : 0) . ' 元）';
    if ($mode === 'capped') return '收 ' . $rate . '%（封顶 ' . (isset($cfg['max_fee']) ? $cfg['max_fee'] : 0) . ' 元）';
    return '收 ' . $rate . '%';
}

/**
 * 手续费规则完整描述（前台展示用），role: user / sup
 */
function tixian_fee_desc($role = 'user')
{
    global $conf;
    $prefix = ($role === 'sup') ? 'sup_' : '';
    if (!empty($conf[$prefix . 'tixian_fee_bytype'])) {
        $types = json_decode($conf[$prefix . 'tixian_fee_types'], true);
        if (is_array($types) && !empty($types)) {
            $parts = array();
            foreach ($types as $t => $c) {
                if (empty($c['on'])) $parts[] = tixian_type_name($t) . '暂不开放';
                else $parts[] = tixian_type_name($t) . tixian_fee_desc_one($c);
            }
            if (!empty($parts)) return '提现手续费（按结算方式）：' . implode('，', $parts);
        }
    }
    $cfg = tixian_fee_config($role, 0);
    if ($cfg === null) {
        $rate = floatval($conf[$prefix . 'tixian_rate']);
        if ($rate <= 0 || $rate > 100) $rate = 98;
        return '提现手续费' . round(100 - $rate, 2) . '%';
    }
    return '提现手续费' . tixian_fee_desc_one($cfg);
}







