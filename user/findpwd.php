<?php
/**
 * 找回密码 - 彩虹云商城主题
 * 保留原 QQ 扫码找回逻辑（act=qrlogin），仅替换前端模板
**/
$is_defend=true;
include("../includes/common.php");
if(isset($_GET['act']) && $_GET['act']=='qrlogin'){
	if(isset($_SESSION['findpwd_qq']) && $qq=$_SESSION['findpwd_qq']){
		$row=$DB->getRow("SELECT zid,user,pwd,status FROM pre_site WHERE qq=:qq LIMIT 1", [':qq'=>$qq]);
		unset($_SESSION['findpwd_qq']);
		if($row['user']){
			if($row['status']==0){
				exit('{"code":-1,"msg":"当前账号已被封禁！"}');
			}
			$session=md5($row['user'].$row['pwd'].$password_hash);
			$token=authcode("{$row['zid']}\t{$session}", 'ENCODE', SYS_KEY);
			setcookie("user_token", $token, time() + 604800, '/');
			log_result('分站找回密码', 'User:'.$row['user'].' IP:'.$clientip, null, 1);
			$DB->exec("UPDATE pre_site SET lasttime='$date' WHERE zid='{$row['zid']}'");
			exit('{"code":1,"msg":"登录成功，请在用户资料设置里重置密码","url":"./"}');
		}else{
			@header('Content-Type: application/json; charset=UTF-8');
			exit('{"code":-1,"msg":"当前QQ不存在，请确认你已注册过账号或开通过分站"}');
		}
	}else{
		@header('Content-Type: application/json; charset=UTF-8');
			exit('{"code":-2,"msg":"验证失败，请重新扫码"}');
	}
}elseif($islogin2==1){
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
}
$title='找回密码';

// 设置CDN路径
if($conf['cdnpublic']==1){
	$cdnpublic = '//lib.baomitu.com/';
}elseif($conf['cdnpublic']==2){
	$cdnpublic = 'https://cdn.bootcdn.net/ajax/libs/';
}elseif($conf['cdnpublic']==4){
	$cdnpublic = '//s1.pstatp.com/cdn/expire-1-M/';
}else{
	$cdnpublic = '//lib.baomitu.com/';
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?php echo $title?> - 彩虹云商城</title>
  <?php if(!empty($conf['favicon'])){echo '<link rel="icon" href="'.htmlspecialchars($conf['favicon']).'" type="image/x-icon" />';}?>
  <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css?v=fa-cors-20260706" rel="stylesheet"/>
  <script src="<?php echo $cdnpublic?>modernizr/2.8.3/modernizr.min.js"></script>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:"PingFang SC","Microsoft YaHei",sans-serif; }
body {
  min-height:100vh;
  background:linear-gradient(135deg,#eaf3ff 0%,#f5f8fc 50%,#eef4ff 100%);
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
}
.findpwd-wrap {
  width:100%;
  max-width:460px;
}
.findpwd-card {
  background:#fff;
  border-radius:20px;
  box-shadow:0 20px 60px rgba(46,134,222,.15);
  overflow:hidden;
  border:1px solid rgba(46,134,222,.08);
}
.findpwd-header {
  background:linear-gradient(135deg,#2e86de 0%,#54a0ff 100%);
  padding:28px 30px;
  text-align:center;
  position:relative;
  overflow:hidden;
}
.findpwd-header::after {
  content:"";
  position:absolute;
  inset:0;
  background:radial-gradient(circle at 80% 20%,rgba(255,255,255,.18),transparent 50%);
  pointer-events:none;
}
.findpwd-header .logo {
  display:inline-flex;
  align-items:center;
  gap:10px;
  color:#fff;
  font-size:20px;
  font-weight:700;
  letter-spacing:1px;
  margin-bottom:6px;
}
.findpwd-header .logo i { font-size:22px; }
.findpwd-header .subtitle {
  color:rgba(255,255,255,.9);
  font-size:13px;
  letter-spacing:.5px;
}
.findpwd-header .back-home {
  position:absolute;
  top:18px;
  right:20px;
  color:rgba(255,255,255,.85);
  text-decoration:none;
  font-size:13px;
  background:rgba(255,255,255,.15);
  padding:6px 14px;
  border-radius:20px;
  transition:all .2s;
}
.findpwd-header .back-home:hover {
  background:rgba(255,255,255,.28);
  color:#fff;
}
.findpwd-body {
  padding:36px 34px 30px;
}
.findpwd-tip {
  text-align:center;
  margin-bottom:24px;
}
.findpwd-tip i {
  color:#2e86de;
  font-size:16px;
  margin-right:6px;
}
.findpwd-tip span {
  color:#3d4654;
  font-size:15px;
  font-weight:500;
}
.qr-box {
  width:220px;
  height:220px;
  margin:0 auto 28px;
  border:3px solid #eaf3ff;
  border-radius:16px;
  display:flex;
  align-items:center;
  justify-content:center;
  background:#fafcff;
  box-shadow:inset 0 2px 8px rgba(46,134,222,.06);
  position:relative;
  overflow:hidden;
}
.qr-box img { max-width:100%; max-height:100%; }
.qr-box #qrimg { width:100%; height:100%; display:flex; align-items:center; justify-content:center; }
.qr-loading {
  color:#8a96a8;
  font-size:14px;
}
.btn-row {
  display:flex;
  gap:14px;
  margin-top:8px;
}
.btn-row .btn {
  flex:1;
  height:46px;
  border:none;
  border-radius:12px;
  font-size:15px;
  font-weight:600;
  cursor:pointer;
  transition:all .2s;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap:8px;
  text-decoration:none;
}
.btn-row .btn-login {
  background:linear-gradient(135deg,#2e86de 0%,#54a0ff 100%);
  color:#fff;
  box-shadow:0 4px 14px rgba(46,134,222,.28);
}
.btn-row .btn-login:hover {
  transform:translateY(-2px);
  box-shadow:0 8px 22px rgba(46,134,222,.38);
  color:#fff;
}
.btn-row .btn-reg {
  background:#fff;
  color:#2e86de;
  border:1.5px solid #d6e4f5;
}
.btn-row .btn-reg:hover {
  border-color:#54a0ff;
  background:#f0f6ff;
  color:#2e86de;
  transform:translateY(-2px);
}
.findpwd-footer {
  text-align:center;
  margin-top:22px;
  color:#8a96a8;
  font-size:12px;
}
</style>
</head>
<body>
<div class="findpwd-wrap">
  <div class="findpwd-card">
    <div class="findpwd-header">
      <a href="../" class="back-home"><i class="fa fa-home"></i> 返回首页</a>
      <div class="logo"><i class="fa fa-cloud"></i> 彩虹云商城</div>
      <div class="subtitle">Rainbow Cloud Mall</div>
    </div>
    <div class="findpwd-body">
      <div class="findpwd-tip">
        <i class="fa fa-qrcode"></i>
        <span id="loginmsg">请使用QQ手机版扫描二维码</span>
      </div>
      <div class="qr-box">
        <div id="qrimg"><span class="qr-loading">二维码加载中...</span></div>
      </div>
      <div id="mobile" style="display:none; text-align:center; margin-bottom:16px;">
        <button type="button" id="mlogin" onclick="mloginurl()" class="btn btn-warning" style="margin-bottom:10px;">跳转QQ快捷登录</button><br/>
        <button type="button" onclick="qrlogin()" class="btn btn-success">我已完成登录</button>
      </div>
      <div class="btn-row">
        <a href="login.php" class="btn btn-login"><i class="fa fa-sign-in"></i> 返回登录</a>
        <a href="reg.php" class="btn btn-reg"><i class="fa fa-user-plus"></i> 注册账户</a>
      </div>
    </div>
  </div>
  <div class="findpwd-footer">Copyright © <?php echo date("Y") ?> 彩虹云商城 All Rights Reserved.</div>
</div>
<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="../assets/js/qrlogin.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>

