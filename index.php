<?php
/* 
首页
QQ群915043052
个人博客blog.6v6.ren
*/
$is_defend=true;
if (version_compare(PHP_VERSION, '7.1.0', '<')) {
    die('PHP版本过低，请使用PHP7.1.0以上版本');
}
if (isset($_SERVER) && $_SERVER['REQUEST_URI'] == '/favicon.ico')exit;

include("./includes/common.php");
eval(base64_decode('aWYoIWRlZmluZWQoJ1NJVEVfQVVUSF9WRVJJRklFRCcpKXtodHRwX3Jlc3BvbnNlX2NvZGUoNDA0KTtoZWFkZXIoIkhUVFAvMS4xIDQwNCBOb3QgRm91bmQiKTtleGl0O30='));

@header('Content-Type: text/html; charset=UTF-8');
if($conf['parapet']==1 && !isset($_SESSION['time'])){
	$_SESSION['time'] = time();
	exit("<script language='javascript'>window.location.href='?key=".$_SESSION['time']."';</script>");
}
if($conf['fenzhan_page']==1 && !empty($conf['fenzhan_remain']) && !in_array($domain,explode(',',$conf['fenzhan_remain'])) && $is_fenzhan==false){
	include ROOT.'template/default/404.html';
	exit;
}

$qq=isset($_GET['qq'])?htmlspecialchars(strip_tags(trim($_GET['qq']))):null;

$addsalt=md5(mt_rand(0,999).time());
$_SESSION['addsalt']=$addsalt;
$x = new \lib\hieroglyphy();
$addsalt_js = $x->hieroglyphyString($addsalt);

if($is_fenzhan==true && file_exists(ROOT.'assets/img/logo_'.$conf['zid'].'.png')){
	$logo = 'assets/img/logo_'.$conf['zid'].'.png';
}else{
	$logo = 'assets/img/logo.png';
}
// 给logo附加文件修改时间戳，更换LOGO后浏览器自动刷新缓存（数据互通）
// 先清除PHP的stat缓存，确保上传新LOGO后能立即读到最新的修改时间
clearstatcache(true, ROOT . $logo);
$logo_mtime = @filemtime(ROOT . $logo);
if ($logo_mtime) $logo .= '?v=' . $logo_mtime;
if($conf['cdnpublic']==1){
	$cdnpublic = '//lib.baomitu.com/';
}elseif($conf['cdnpublic']==2){
	$cdnpublic = 'https://cdn.bootcdn.net/ajax/libs/';
}elseif($conf['cdnpublic']==4){
	$cdnpublic = '//s1.pstatp.com/cdn/expire-1-M/';
}else{
	$cdnpublic = '//lib.baomitu.com/';
}
if(!empty($conf['staticurl'])){
	$cdnserver = '//'.$conf['staticurl'].'/';
}else{
	$cdnserver = null;
}

if(!empty($conf['gg_announce']))$conf['anounce']=$conf['gg_announce'].$conf['anounce'];

if($is_fenzhan == true && $siterow['power']==2){
	if($siterow['ktfz_price']>0)$conf['fenzhan_price']=$siterow['ktfz_price'];
	if($conf['fenzhan_cost2']<=0)$conf['fenzhan_cost2']=$conf['fenzhan_price2'];
	if($siterow['ktfz_price2']>0 && $siterow['ktfz_price2']>=$conf['fenzhan_cost2'])$conf['fenzhan_price2']=$siterow['ktfz_price2'];
}

if($conf['sitename_hide']==1 && !empty($conf['title'])){
	$hometitle = $conf['title'];
}else{
	$hometitle = $conf['sitename'].(!empty($conf['title'])?' - '.$conf['title']:null);
}

list($background_image, $background_css) = \lib\Template::getBackground();

if($conf['invite_tid'] && isset($_GET['i']) && $_GET['i']!=$_COOKIE['invite']){
	$invite_result = processInvite($_GET['i']);
	if($invite_result=='captcha'){
		@header('Content-Type: text/html; charset=UTF-8');
		include TEMPLATE_ROOT.'default/captcha.php';
		exit;
	}
}

if($conf['forceloginhome']==1 && !$islogin2){
	exit("<script language='javascript'>window.location.href='./user/login.php?back=index';</script>");
}

$mod = isset($_GET['mod'])?$_GET['mod']:'index';

if (isset($_GET['template_preview']) && preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-_]+$/u', $_GET['template_preview'])) {
	$conf['template'] = $_GET['template_preview'];
}

$loadfile = \lib\Template::load($mod);
include $loadfile;

$template_layout_config = TEMPLATE_ROOT . $conf['template'] . '/layout_config.json';
if (file_exists($template_layout_config)) {
	$layout_data = json_decode(file_get_contents($template_layout_config), true);
	if (!empty($layout_data['textEdits'])) {
		echo '<script>';
		echo '(function(){';
		echo 'var edits=' . json_encode($layout_data['textEdits'], JSON_UNESCAPED_UNICODE) . ';';
		echo 'var sel="h1,h2,h3,h4,h5,span,p,a,b,strong,small,label,button,td,th,li,.btn,.list-group-item,.widget-heading,.panel-title,.block-title h4";';
		echo 'document.addEventListener("DOMContentLoaded",function(){var els=document.querySelectorAll(sel);var idx=0;for(var i=0;i<els.length;i++){var el=els[i];if(el.closest("script")||el.closest("style")||el.closest("link")||el.closest("meta"))continue;if(el.querySelector("input,textarea,select,button,img,iframe,canvas,svg"))continue;var t=(el.innerText||el.textContent||"").trim();if(t.length>0&&t.length<200&&el.children.length===0){var id=""+idx;if(edits[id]){el.innerText=edits[id];}idx++;}}})';
		echo '})();';
		echo '</script>';
	}
}

include ROOT."template/default/chat/widget.php";