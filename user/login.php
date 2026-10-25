<?php
/**
 * 登录页面 - 对标 hack.wetcs.cn 左右分栏风格
 * 保留原登录逻辑 / 验证码 / QQ互联接口，仅替换前端模板
**/
$is_defend=true;
include("../includes/common.php");
// CSRF token 存在 session 里，不依赖 session_id 稳定性
if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); }

if(isset($_GET['logout'])){
	if(!checkRefererHost())exit();
	setcookie("user_token", "", time() - 604800, '/');
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已成功注销本次登录！');window.location.href='./login.php';</script>");
}elseif($islogin2==1){
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已登录！');window.location.href='./';</script>");
}
$title='用户登录';

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
$sitename = !empty($conf['sitename']) ? $conf['sitename'] : '用户中心';
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?php echo $title?> - <?php echo $sitename?></title>
  <?php if(!empty($conf['favicon'])){echo '<link rel="icon" href="'.htmlspecialchars($conf['favicon']).'" type="image/x-icon" />';}?>
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/user/css/auth.css?v=20261017">
</head>
<body>
<div class="auth-page">

  <!-- 左侧蓝色插画面板 -->
  <div class="auth-illustration">
    <svg viewBox="0 0 480 420" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <radialGradient id="cloudGlow" cx="50%" cy="50%" r="50%">
          <stop offset="0%" stop-color="#8ec5ff" stop-opacity="0.9"/>
          <stop offset="60%" stop-color="#4d9aff" stop-opacity="0.35"/>
          <stop offset="100%" stop-color="#0066FF" stop-opacity="0"/>
        </radialGradient>
        <linearGradient id="cubeTop" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#ffffff"/>
          <stop offset="100%" stop-color="#d6e8ff"/>
        </linearGradient>
        <linearGradient id="cubeLeft" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0%" stop-color="#9bc4f5"/>
          <stop offset="100%" stop-color="#c5dcf7"/>
        </linearGradient>
        <linearGradient id="cubeRight" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0%" stop-color="#c5dcf7"/>
          <stop offset="100%" stop-color="#7fb0ee"/>
        </linearGradient>
        <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
          <feGaussianBlur stdDeviation="6" result="blur"/>
          <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
      </defs>

      <!-- 透视网格地面 -->
      <g stroke="#5aa0ff" stroke-width="1" opacity="0.35" fill="none">
        <line x1="240" y1="420" x2="60" y2="200"/>
        <line x1="240" y1="420" x2="420" y2="200"/>
        <line x1="240" y1="420" x2="240" y2="180"/>
        <line x1="100" y1="420" x2="380" y2="420"/>
        <line x1="120" y1="380" x2="360" y2="380"/>
        <line x1="140" y1="340" x2="340" y2="340"/>
        <line x1="160" y1="300" x2="320" y2="300"/>
        <line x1="180" y1="260" x2="300" y2="260"/>
        <line x1="200" y1="220" x2="280" y2="220"/>
        <line x1="150" y1="240" x2="330" y2="240"/>
        <line x1="120" y1="290" x2="360" y2="290"/>
        <line x1="90" y1="340" x2="390" y2="340"/>
        <line x1="60" y1="390" x2="420" y2="390"/>
      </g>

      <!-- 顶部发光云脑 -->
      <ellipse cx="240" cy="110" rx="130" ry="90" fill="url(#cloudGlow)" filter="url(#glow)"/>
      <g fill="#bfe0ff" opacity="0.85">
        <circle cx="180" cy="80" r="7"/><circle cx="210" cy="60" r="6"/>
        <circle cx="250" cy="55" r="8"/><circle cx="290" cy="70" r="6"/>
        <circle cx="310" cy="100" r="7"/><circle cx="270" cy="120" r="6"/>
        <circle cx="220" cy="115" r="7"/><circle cx="170" cy="110" r="6"/>
        <circle cx="150" cy="90" r="5"/><circle cx="320" cy="80" r="5"/>
        <circle cx="240" cy="90" r="6"/>
      </g>
      <g stroke="#bfe0ff" stroke-width="1.5" opacity="0.5" fill="none">
        <line x1="180" y1="80" x2="210" y2="60"/><line x1="210" y1="60" x2="250" y2="55"/>
        <line x1="250" y1="55" x2="290" y2="70"/><line x1="290" y1="70" x2="310" y2="100"/>
        <line x1="310" y1="100" x2="270" y2="120"/><line x1="270" y1="120" x2="220" y2="115"/>
        <line x1="220" y1="115" x2="170" y2="110"/><line x1="170" y1="110" x2="150" y2="90"/>
        <line x1="150" y1="90" x2="180" y2="80"/><line x1="180" y1="80" x2="240" y2="90"/>
        <line x1="240" y1="90" x2="290" y2="70"/><line x1="240" y1="90" x2="220" y2="115"/>
        <line x1="250" y1="55" x2="320" y2="80"/><line x1="320" y1="80" x2="310" y2="100"/>
      </g>

      <!-- 立方体 1（左前，大） -->
      <g>
        <polygon points="120,250 180,215 240,250 180,285" fill="url(#cubeTop)" stroke="#ffffff" stroke-width="1.5"/>
        <polygon points="120,250 120,310 180,345 180,285" fill="url(#cubeLeft)" stroke="#8fbef0" stroke-width="1"/>
        <polygon points="240,250 240,310 180,345 180,285" fill="url(#cubeRight)" stroke="#8fbef0" stroke-width="1"/>
        <circle cx="180" cy="250" r="5" fill="#2A7AF3" filter="url(#glow)"/>
      </g>

      <!-- 立方体 2（右前，中） -->
      <g>
        <polygon points="260,280 310,252 360,280 310,308" fill="url(#cubeTop)" stroke="#ffffff" stroke-width="1.5"/>
        <polygon points="260,280 260,330 310,358 310,308" fill="url(#cubeLeft)" stroke="#8fbef0" stroke-width="1"/>
        <polygon points="360,280 360,330 310,358 310,308" fill="url(#cubeRight)" stroke="#8fbef0" stroke-width="1"/>
        <circle cx="310" cy="280" r="4" fill="#2A7AF3" filter="url(#glow)"/>
      </g>

      <!-- 立方体 3（中间后，小） -->
      <g>
        <polygon points="195,205 230,185 265,205 230,225" fill="url(#cubeTop)" stroke="#ffffff" stroke-width="1.2"/>
        <polygon points="195,205 195,245 230,265 230,225" fill="url(#cubeLeft)" stroke="#8fbef0" stroke-width="0.8"/>
        <polygon points="265,205 265,245 230,265 230,225" fill="url(#cubeRight)" stroke="#8fbef0" stroke-width="0.8"/>
      </g>

      <!-- 立方体 4（左后，小） -->
      <g>
        <polygon points="80,220 110,203 140,220 110,237" fill="url(#cubeTop)" stroke="#ffffff" stroke-width="1"/>
        <polygon points="80,220 80,255 110,272 110,237" fill="url(#cubeLeft)" stroke="#8fbef0" stroke-width="0.8"/>
        <polygon points="140,220 140,255 110,272 110,237" fill="url(#cubeRight)" stroke="#8fbef0" stroke-width="0.8"/>
      </g>

      <!-- 立方体 5（最右，小） -->
      <g>
        <polygon points="360,235 385,221 410,235 385,249" fill="url(#cubeTop)" stroke="#ffffff" stroke-width="1"/>
        <polygon points="360,235 360,262 385,276 385,249" fill="url(#cubeLeft)" stroke="#8fbef0" stroke-width="0.8"/>
        <polygon points="410,235 410,262 385,276 385,249" fill="url(#cubeRight)" stroke="#8fbef0" stroke-width="0.8"/>
      </g>

      <!-- 浮动小立方粒子 -->
      <g opacity="0.9">
        <polygon points="150,320 165,312 180,320 165,328" fill="#ffffff" opacity="0.7"/>
        <polygon points="330,320 345,312 360,320 345,328" fill="#ffffff" opacity="0.7"/>
        <polygon points="100,340 112,333 124,340 112,347" fill="#ffffff" opacity="0.5"/>
        <polygon points="370,345 382,338 394,345 382,352" fill="#ffffff" opacity="0.5"/>
      </g>

      <!-- 发光小点 -->
      <g fill="#ffffff">
        <circle cx="70" cy="150" r="3" opacity="0.6"/>
        <circle cx="410" cy="160" r="3" opacity="0.5"/>
        <circle cx="90" cy="280" r="2" opacity="0.5"/>
        <circle cx="400" cy="290" r="2" opacity="0.5"/>
        <circle cx="240" cy="170" r="2" opacity="0.6"/>
      </g>
    </svg>
  </div>

  <!-- 右侧表单面板 -->
  <div class="auth-form-wrapper">
    <div class="auth-topbar">
      <a href="../" class="auth-logo">
        <img src="<?php echo !empty($conf['logo']) ? htmlspecialchars($conf['logo']) : '../assets/img/logo.png' ?>" alt="logo">
        <span class="auth-logo-text"><?php echo $sitename ?></span>
      </a>
      <a href="../" class="auth-go-back">平台首页</a>
    </div>

    <form class="auth-form">
      <h2>商户登录中心</h2>
      <p class="auth-header-info">你还没有账户? <a href="<?php echo $conf['user_open']==1 ? 'reg.php' : 'regsite.php';?>">注册账户</a></p>

      <div class="auth-field">
        <label>用户名</label>
        <input type="text" name="user" value="" required placeholder="请输入用户名"/>
      </div>

      <div class="auth-field">
        <label>密码</label>
        <input type="password" name="pass" id="loginpass" required placeholder="请输入密码"/>
        <button type="button" class="auth-pass-toggle" onclick="togglePass('loginpass',this)" aria-label="显示密码"><i class="fa fa-eye-slash"></i></button>
      </div>

			<?php if($conf['captcha_open_login']==1 && $conf['captcha_open']>=1){?>
				<input type="hidden" name="captcha_type" value="<?php echo $conf['captcha_open']?>"/>
				<?php if($conf['captcha_open']>=2){?><input type="hidden" name="appid" value="<?php echo $conf['captcha_id']?>"><?php }?>
        <div class="auth-field">
          <label>安全验证</label>
          <div id="captcha">
            <div id="captcha_text">正在加载验证码</div>
            <div id="captcha_wait">
              <div class="loading"><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div></div>
            </div>
          </div>
          <div id="captchaform"></div>
        </div>
			<?php }else{ ?>
				<input type="hidden" name="captcha_type" value="0"/>
        <div class="auth-field">
          <label>验证码</label>
          <div class="auth-captcha">
            <input type="text" name="code" maxlength="4" autocomplete="off" required placeholder="输入验证码"/>
            <img id="codeimg" src="./code.php?r=<?php echo time()?>" onclick="this.src='./code.php?r='+Math.random();" title="点击更换验证码" alt="验证码">
          </div>
        </div>
			<?php }?>

      <div class="auth-options">
        <div>
          <input type="checkbox" id="rememberme" name="rememberme" value="1">
          <label for="rememberme">记住密码</label>
        </div>
        <a href="findpwd.php">忘记密码?</a>
      </div>

      <input type="button" value="提交登录" id="submit_login" class="auth-submit"/>

			<?php $oauth_enabled = oauth_enabled_types($conf); if($oauth_enabled){?>
      <div class="auth-third">
        <div class="auth-third-title">其他登录方式</div>
        <?php
        $oauth_btns = array('qq'=>array('i','fa-qq','#12B7F5'),'wx'=>array('i','fa-weixin','#07C160'),'alipay'=>array('t','支','#1678FF'),'douyin'=>array('t','抖','#161823'),'baidu'=>array('t','百','#2932E1'),'sina'=>array('i','fa-weibo','#E6162D'));
        $oauth_all = oauth_all_types();
        foreach($oauth_enabled as $oat){
          $ov = $oauth_btns[$oat];
          $oinner = $ov[0]=='i' ? '<i class="fa '.$ov[1].'"></i>' : '<span style="font-size:14px;font-weight:bold">'.$ov[1].'</span>';
          echo '<a href="javascript:connect(\''.$oat.'\')" title="'.$oauth_all[$oat].'快捷登录" style="display:inline-flex;width:40px;height:40px;border-radius:50%;background:'.$ov[2].';color:#fff;align-items:center;justify-content:center;text-decoration:none;margin:0 5px;vertical-align:top;">'.$oinner.'</a>';
        }?>
      </div>
			<?php }?>

      <div class="auth-alt-links">
        <a href="findpwd.php" class="auth-alt-outline"><i class="fa fa-unlock-alt"></i> 找回密码</a>
        <a href="<?php echo $conf['user_open']==1 ? 'reg.php' : 'regsite.php';?>" class="auth-alt-outline"><i class="fa fa-user-plus"></i> 注册账号</a>
      </div>

      <p class="auth-copyright">Copyright © <?php echo date('Y') ?> <?php echo $sitename ?> All rights reserved.</p>
    </form>
  </div>

</div>
<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
<script>
function togglePass(id,btn){
  var el=document.getElementById(id);
  var icon=btn.querySelector('i');
  if(el.type==='password'){el.type='text';icon.className='fa fa-eye';}
  else{el.type='password';icon.className='fa fa-eye-slash';}
}
function connect(type){
  window.location.href='../connect.php?act=login&type='+type;
}
</script>
<script src="../assets/js/login.js?ver=<?php echo VERSION ?>-login-captcha-1"></script>
</body>
</html>
