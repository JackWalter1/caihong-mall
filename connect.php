<?php
/**
 * 聚合登录入口：发起登录 / 回调中转
**/
include("./includes/common.php");
if(!oauth_enabled_types($conf))sysmsg('当前站点未开启快捷登录');

if(isset($_GET['act']) && $_GET['act']=='login'){
	$type = isset($_GET['type'])?$_GET['type']:'qq';
	$oauth_enabled = oauth_enabled_types($conf);
	if(!in_array($type, $oauth_enabled))sysmsg('该快捷登录方式未开启');
	$Oauth = new \lib\Oauth($conf['login_apiurl'], $conf['login_appid'], $conf['login_appkey']);
	$arr = $Oauth->login($type);
	if(isset($arr['code']) && $arr['code']==0 && !empty($arr['url'])){
		@header('Location: '.$arr['url']);
	}elseif(isset($arr['code'])){
		sysmsg('<h3>快捷登录跳转失败</h3>错误码：'.$arr['code'].'<br/>'.(isset($arr['msg'])?$arr['msg']:''));
	}else{
		sysmsg('聚合登录接口连接失败，请检查快捷登录API配置');
	}
	exit;
}
if(isset($_GET['code']) && isset($_GET['type'])){
	$q = array();
	foreach(array('code','type','state') as $k){
		if(isset($_GET[$k]))$q[] = $k.'='.urlencode($_GET[$k]);
	}
	@header('Location: ./user/connect.php?'.implode('&', $q));
	exit;
}
sysmsg('参数错误');
