<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("site", 2);

// 检查是否是供货商登录
if (isset($_GET["sid"])) {
	$sid = intval($_GET["sid"]);
	$userrow = $DB->getRow("select * from pre_supplier where sid='" . $sid . "' limit 1");
	if (!$userrow) {
		sysmsg("当前供货商不存在！");
	}
	// 跳转到供货商登录页面，让用户输入账号密码
	exit("<script language='javascript'>window.location.href='../sup/login.php';</script>");
}

// 分站登录（保持原有功能）
if (isset($_GET["zid"])) {
	$zid = intval($_GET["zid"]);
	$userrow = $DB->getRow("select * from pre_site where zid='" . $zid . "' limit 1");
	if (!$userrow) {
		sysmsg("当前用户不存在！");
	}
	$session = md5($userrow["user"] . $userrow["pwd"] . $password_hash);
	$token = authcode($zid . "\t" . $session, "ENCODE", SYS_KEY);
	setcookie("user_token", $token, time() + 604800, "/");
	exit("<script language='javascript'>window.location.href='../user/';</script>");
}

sysmsg("参数错误！");
?>