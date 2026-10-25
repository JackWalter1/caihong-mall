<?php
if(!defined('IN_CRONLITE'))exit();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <title>微信的钱转到QQ钱包教程</title>
    <?php if(!empty($conf['favicon'])){?>
    <link rel="icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
    <?php }?>
    <link rel="stylesheet" href="<?php echo $cdnserver?>assets/simple/css/oneui.css">
	<link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	<link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>">
<?php
  // 红丸俱乐部主题（HongWan）：全站苹果玻璃皮肤
  $hw_cur_tpl = !empty($conf['template']) ? $conf['template'] : 'default';
  if(function_exists('checkmobile') && checkmobile() && !empty($conf['template_m']) && $conf['template_m']!='0') $hw_cur_tpl = $conf['template_m'];
  if($hw_cur_tpl === 'HongWan'){
?>
    <link rel="stylesheet" href="template/HongWan/assets/css/apple-core.lossless-20260910.css">
    <link rel="stylesheet" href="template/HongWan/assets/css/appui-liquid.lg15-lossless-20260910.css">
<?php } ?>
    <script src="<?php echo $cdnpublic?>modernizr/2.8.3/modernizr.min.js"></script>
    <!--[if lt IE 9]>
      <script src="<?php echo $cdnpublic?>html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="<?php echo $cdnpublic?>respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<?php echo $background_css?>
</head>
<body>
<br/>
<div class="col-xs-12 col-sm-10 col-md-8 col-lg-5 center-block" style="float: none;">
<div class="block" style="box-shadow:0px 5px 10px 0 rgba(0, 0, 0, 0.25);" id="backHome">
<a class="btn btn-block" href="./"><i class="fa fa-mail-reply-all"></i>  返回网站首页</a>
</div>

<div class="block block-bordered" style="box-shadow:0px 5px 10px 0 rgba(0, 0, 0, 0.25);">
  <div class="block-header bg-gray-lighter">
   <h3 class="block-title"><i class="fa fa-wechat"></i>&nbsp;微信的钱转到QQ钱包教程</h3>
  </div>
  <div class="block-content">
<b style="color:red;"><b style="color:red;">推荐使用支付宝或QQ钱包付款，订单会优先处理！<hr>
          

    
    <font color="red"> 超简单转移方法:</font><br><br><font color="#000000">①&nbsp;在微信公众号搜索<font color="red">QQ钱包官方账号</font><br>
     <br>
②&nbsp;在公众号菜单栏中点击“转账到QQ” 按步骤依次完成即可实现从微信转账资金到QQ钱包余额<br><br>
      ③&nbsp;使用QQ支付下单，就可以啦！</font>
      </b></b><br><br><img src="//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rHehP2ib7coccnFsMEPBrnicqwzEUGkEzc1g/0" style="  width: 100%;"><hr/>
  </div>
</div>
<div class="block" style="box-shadow:0px 5px 10px 0 rgba(0, 0, 0, 0.25);" id="backHome">
<a class="btn btn-block" href="./"><i class="fa fa-mail-reply-all"></i>  返回网站首页</a>
</div>
</div>
</body>
</html>