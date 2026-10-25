<?php
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
	$cdnserver = '../';
}

if(substr($userrow['user'],0,3)=='qq_' && !empty($userrow['nickname'])){
	$nickname = htmlspecialchars($userrow['nickname']);
}else{
	$nickname = $userrow['user'];
}
if(empty($userrow['qq']) && !empty($userrow['faceimg'])){
	$faceimg = htmlspecialchars($userrow['faceimg']);
}elseif(!empty($userrow['qq'])){
	$faceimg = '//q4.qlogo.cn/headimg_dl?dst_uin='.$userrow['qq'].'&spec=100';
}else{
	$faceimg = '../assets/img/user.png';
}

$newuserhead=null;
$newuserfoot=null;
$template_route = \lib\Template::loadRoute();
if($template_route){
	$newuserhead = $template_route['userhead'];
	$newuserfoot = $template_route['userfoot'];
	if($template_route['userindex'] && checkIfActive(',index')){
		include($template_route['userindex']);exit;
	}
}
if($newuserhead){
	include($newuserhead);
	return;
}

@header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <title><?php echo $title ?></title>
  <?php if(!empty($conf['favicon'])){echo '<link rel="icon" href="'.htmlspecialchars($conf['favicon']).'" type="image/x-icon" />';}else{echo '<link rel="icon" href="../assets/img/favicon/favicon.ico" type="image/x-icon" /><link rel="shortcut icon" href="../assets/img/favicon/favicon.ico" type="image/x-icon" />';}?>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/user/css/animate.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/user/css/app.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/user/css/modern.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/user/css/modern-ultimate.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/user/css/merchant.css?v=20261023" type="text/css" />
<?php
  // 红丸俱乐部主题（HongWan）：追加 Apple 毛玻璃皮肤包
  $hw_cur_tpl = !empty($conf['template']) ? $conf['template'] : 'default';
  if(checkmobile() && !empty($conf['template_m']) && $conf['template_m']!='0') $hw_cur_tpl = $conf['template_m'];
  if($hw_cur_tpl === 'HongWan'){
?>
  <link rel="stylesheet" href="../template/HongWan/assets/user/css/user-polish.css?ver=2065" type="text/css" />
  <link rel="stylesheet" href="../template/HongWan/assets/user/css/user-polish-ext.css?ver=2065-df4" type="text/css" />
  <link rel="stylesheet" href="../template/HongWan/assets/user/css/user-glass.css?ver=hw1" type="text/css" />
  <link rel="stylesheet" href="../template/HongWan/assets/css/hw-gradient.css?ver=hw3" type="text/css" />
  <link rel="stylesheet" href="../template/HongWan/assets/css/hw-dark.css?ver=hd7" type="text/css" />
<?php } ?>
    <script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
    <script src="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
    <script src="<?php echo $cdnserver?>assets/user/js/app.js"></script>
    <script src="<?php echo $cdnserver?>assets/user/js/modern.js"></script>
  <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<?php if($islogin2==1){
if($userrow['status']==0){
	sysmsg('你的账号已被封禁！',true);exit;
}elseif($userrow['power']>0 && $conf['fenzhan_expiry']>0 && $userrow['endtime']<$date){
	sysmsg('你的账号已到期，请联系管理员续费！',true);exit;
}
?>
<div class="app app-header-fixed">
  <!-- 顶部导航栏 -->
  <header id="header" class="app-header navbar" role="menu">
      <div class="navbar-header">
        <a href="./" class="navbar-brand">
          <img src="../assets/img/logo.png" alt="logo" style="height:30px;width:auto;border-radius:6px;vertical-align:middle;">
          <span><?php echo !empty($conf['sitename'])?$conf['sitename']:'管理中心' ?></span>
        </a>
        <a href="#" class="btn navbar-btn hidden-xs pull-right" id="desktop-menu-btn">
          <i class="fa fa-dedent fa-fw"></i>
        </a>
        <button class="pull-right visible-xs" onclick="return toggleMobileSidebar(event);">
          <i class="glyphicon glyphicon-align-justify"></i>
        </button>
      </div>

      <!-- 顶部搜索 -->
      <div class="mc-top-search hidden-xs">
        <div class="form-group">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="输入订单编号...">
            <span class="input-group-btn">
              <button class="btn" type="button"><i class="fa fa-search"></i></button>
            </span>
          </div>
        </div>
      </div>

      <div class="collapse pos-rlt navbar-collapse">
        <ul class="nav navbar-nav navbar-right mc-top-actions">
          <li><a href="javascript:;" class="mc-icon-btn" title="全屏" onclick="toggleFullScreen()"><i class="fa fa-expand"></i></a></li>
          <li><a href="./message.php" class="mc-icon-btn" title="消息通知"><i class="fa fa-bell-o"></i><span class="badge-dot" id="mc_msg_dot" style="display:none;"></span></a></li>
          <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle clear mc-user-info">
              <img src="<?php echo $faceimg ?>" alt="头像">
              <span class="mc-uname hidden-sm hidden-md"><?php echo $nickname ?></span>
              <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <li><a href="./"><i class="fa fa-home"></i> 用户中心</a></li>
              <li><a href="./uset.php?mod=user"><i class="fa fa-user"></i> 修改资料</a></li>
              <li><a href="../"><i class="fa fa-globe"></i> 返回首页</a></li>
              <li class="divider"></li>
              <li><a href="login.php?logout"><i class="fa fa-sign-out"></i> 退出登录</a></li>
            </ul>
          </li>
        </ul>
      </div>
  </header>

  <!-- 侧边栏导航 -->
  <aside id="aside" class="app-aside hidden-xs">
      <div class="aside-wrap">
        <div class="navi-wrap">
          <nav ui-nav class="navi">
            <ul class="nav">
              <li class="hidden-folded padder m-t m-b-sm text-muted text-xs"><span>菜单</span></li>
              <li class="<?php echo checkIfActive(',index')?>">
                <a href="./"><i class="fa fa-tachometer"></i><span>控制台</span></a>
              </li>

              <li class="hidden-folded padder m-t m-b-sm text-muted text-xs"><span>分站管理</span></li>
              <?php if($conf['fenzhan_buy']==1){?>
              <li class="<?php echo checkIfActive('regsite')?>">
                <a href="./regsite.php"><i class="fa fa-rocket"></i><span>开通分站</span></a>
              </li>
              <?php }?>
              <?php if($userrow['power']>0){?>
              <li class="<?php echo checkIfActive('sitelist,siteinfo')?>">
                <a href="./sitelist.php"><i class="fa fa-sitemap"></i><span>分站列表</span></a>
              </li>
              <li class="<?php echo checkIfActive('tuiguang,userlist')?>">
                <a href="./tuiguang.php"><i class="fa fa-users"></i><span>推广佣金</span></a>
              </li>
              <li class="<?php echo checkIfActive('renew')?>">
                <a href="./renew.php"><i class="fa fa-refresh"></i><span>分站续费</span></a>
              </li>
              <li class="<?php echo checkIfActive('upsite')?>">
                <a href="./upsite.php"><i class="fa fa-arrow-up"></i><span>分站升级</span></a>
              </li>
              <?php }?>

              <?php if($userrow['power']>0){?>
              <li class="<?php echo checkIfActive('uset')?>">
                <a href="javascript:void(0)" class="auto" onclick="toggleSubmenu(this)">
                  <span class="pull-right text-muted"><i class="fa fa-fw fa-angle-right"></i></span>
                  <i class="fa fa-store"></i><span>店铺管理</span>
                </a>
                <ul class="nav nav-sub">
                  <li class="<?php echo checkIfActive('site')?>"><a href="./uset.php?mod=site"><i class="fa fa-info-circle w-4 text-center"></i><span>店铺信息</span></a></li>
                  <li class="<?php echo checkIfActive('skimg')?>"><a href="./uset.php?mod=skimg"><i class="fa fa-qrcode w-4 text-center"></i><span>收款设置</span></a></li>
                  <li class="<?php echo checkIfActive('cdomain,ndomain')?>"><a href="./cdomain.php"><i class="fa fa-link w-4 text-center"></i><span>域名管理</span></a></li>
                  <li class="<?php echo checkIfActive('usetmoban')?>"><a href="./usetmoban.php?mod=site2"><i class="fa fa-paint-brush w-4 text-center"></i><span>模板设置</span></a></li>
                </ul>
              </li>
              <?php }?>

              <li class="hidden-folded padder m-t m-b-sm text-muted text-xs"><span>运营</span></li>
              <?php if($userrow['power']>0){?>
              <li class="<?php echo checkIfActive('classlist,shoplist,myshop')?>">
                <a href="javascript:void(0)" class="auto" onclick="toggleSubmenu(this)">
                  <span class="pull-right text-muted"><i class="fa fa-fw fa-angle-right"></i></span>
                  <i class="fa fa-cube"></i><span>商品管理</span>
                </a>
                <ul class="nav nav-sub">
                  <li class="<?php echo checkIfActive('classlist')?>"><a href="./classlist.php"><i class="fa fa-th-large w-4 text-center"></i><span>分类管理</span></a></li>
                  <li class="<?php echo checkIfActive('shoplist')?>"><a href="./shoplist.php"><i class="fa fa-shopping-bag w-4 text-center"></i><span>商品列表</span></a></li>
                  <li class="<?php echo checkIfActive('myshop')?>"><a href="./myshop.php"><i class="fa fa-cubes w-4 text-center"></i><span>我的商品</span></a></li>
                </ul>
              </li>
              <?php }?>
              <li class="<?php echo checkIfActive('list')?>">
                <a href="./list.php"><i class="fa fa-file-text-o"></i><span>订单管理</span></a>
              </li>
              <li class="<?php echo checkIfActive('shop,shops')?>">
                <a href="./shop.php"><i class="fa fa-shopping-cart"></i><span>自助下单</span></a>
              </li>
              <?php if($conf['workorder_open']==1){?>
              <li class="<?php echo checkIfActive('workorder')?>">
                <a href="./workorder.php"><i class="fa fa-ticket"></i><span>我的工单</span></a>
              </li>
              <?php }?>
              <li class="<?php echo checkIfActive('workorder2')?>">
                <a href="./workorder2.php"><i class="fa fa-comment-o"></i><span>我的投诉</span></a>
              </li>
              <li class="hidden-folded padder m-t m-b-sm text-muted text-xs"><span>资金管理</span></li>
              <li class="<?php echo checkIfActive('recharge')?>">
                <a href="./recharge.php"><i class="fa fa-plus-circle"></i><span>账户充值</span></a>
              </li>
              <li class="<?php echo checkIfActive('record')?>">
                <a href="./record.php"><i class="fa fa-list-alt"></i><span>收支明细</span></a>
              </li>
              <?php if($userrow['power']>0){?>
              <li class="<?php echo checkIfActive('tixian')?>">
                <a href="./tixian.php"><i class="fa fa-credit-card"></i><span>申请提现</span></a>
              </li>
              <?php }?>

              <li class="hidden-folded padder m-t m-b-sm text-muted text-xs"><span>应用</span></li>
              <?php if($userrow['power']>0 && $conf['fenzhan_rank']==1){?>
              <li class="<?php echo checkIfActive('rank')?>">
                <a href="./rank.php"><i class="fa fa-line-chart"></i><span>生意罗盘</span></a>
              </li>
              <?php }?>
              <li class="<?php echo checkIfActive('uset')?>">
                <a href="javascript:void(0)" class="auto" onclick="toggleSubmenu(this)">
                  <span class="pull-right text-muted"><i class="fa fa-fw fa-angle-right"></i></span>
                  <i class="fa fa-cog"></i><span>系统设置</span>
                </a>
                <ul class="nav nav-sub">
                  <li class="<?php echo checkIfActive('user')?>"><a href="./uset.php?mod=user"><i class="fa fa-user-circle w-4 text-center"></i><span>用户资料</span></a></li>
                  <li class="<?php echo checkIfActive('message')?>"><a href="./message.php"><i class="fa fa-bell w-4 text-center"></i><span>消息通知</span></a></li>
                  <?php if(!empty($conf['login_qq']) || !empty($conf['login_wx'])){?>
                  <li class="<?php echo checkIfActive('connect')?>"><a href="./connect.php"><i class="fa fa-link w-4 text-center"></i><span>账号绑定</span></a></li>
                  <?php }?>
                </ul>
              </li>
              <li class="<?php echo checkIfActive('sitetask')?>">
                <a href="./sitetask.php"><i class="fa fa-tasks"></i><span>站点任务</span></a>
              </li>
              <?php if($conf['qiandao_reward']){?>
              <li class="<?php echo checkIfActive('qiandao')?>">
                <a href="./qiandao.php"><i class="fa fa-calendar-check-o"></i><span>每日签到</span></a>
              </li>
              <?php }?>
              <?php if($conf['chat_enable']==1){?>
              <li class="<?php echo checkIfActive('chat')?>">
                <a href="./chat.php"><i class="fa fa-comments-o"></i><span>在线客服</span></a>
              </li>
              <?php }?>
              <li class="<?php echo checkIfActive('faq')?>">
                <a href="./faq.php"><i class="fa fa-question-circle"></i><span>常见问题</span></a>
              </li>
              <li>
                <a href="login.php?logout"><i class="fa fa-sign-out"></i><span>退出登录</span></a>
              </li>
            </ul>
          </nav>
          <!-- 底部店铺卡片 -->
          <div class="mc-store-card">
            <div class="mc-store-card-inner" onclick="location.href='<?php echo $userrow['power']>0?'./uset.php?mod=site':'./uset.php?mod=user'?>'">
              <img src="<?php echo $faceimg ?>" alt="店铺">
              <div class="mc-store-info">
                <div class="mc-store-name"><?php echo $nickname ?></div>
                <div class="mc-store-domain"><?php echo !empty($userrow['domain'])?$userrow['domain']:'未开通分站' ?></div>
              </div>
              <i class="fa fa-chevron-right" style="color:#64748b;font-size:12px;"></i>
            </div>
          </div>
        </div>
      </div>
  </aside>

  <!-- 主内容区 -->
  <div id="content" class="app-content" role="main">
    <div class="app-content-body">
      <div class="mc-breadcrumb"><i class="fa fa-home"></i> 后台管理 <i class="fa fa-angle-right"></i> <?php echo $title ?></div>
<script>
function toggleFullScreen(){
  if(!document.fullscreenElement){ document.documentElement.requestFullscreen && document.documentElement.requestFullscreen(); }
  else{ document.exitFullscreen && document.exitFullscreen(); }
}
// 消息红点
$(function(){
  $.get("ajax.php?act=msg",function(d){
    if(d.code==0 && d.count>0){ $("#mc_msg_dot").show(); }
  },"json");
});
</script>
<?php }?>
