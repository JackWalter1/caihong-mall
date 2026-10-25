<?php
if(!defined('IN_CRONLITE'))exit();

$orderid=trim(daddslashes($_GET['orderid']));
$row=$DB->getRow("select * from pre_pay where trade_no='$orderid' limit 1");
if(!$row)sysmsg('当前订单不存在');
if($row['status']==1)exit("<script language='javascript'>alert('当前订单已完成支付！');window.location.href='./?buyok=1';</script>");

$style_map = [
	'1' => ['primary'=>'#165DFF','primary_hover'=>'#0E42D2','primary_light'=>'#E8F3FF'],
	'2' => ['primary'=>'#00B42A','primary_hover'=>'#009A29','primary_light'=>'#E8FFEA'],
	'3' => ['primary'=>'#FF7D00','primary_hover'=>'#D25F00','primary_light'=>'#FFF3E8'],
	'4' => ['primary'=>'#722ED1','primary_hover'=>'#531DAB','primary_light'=>'#F3E8FF'],
	'5' => ['primary'=>'#F53F3F','primary_hover'=>'#CB2634','primary_light'=>'#FFECE8'],
];
$tpl_style = isset($conf['template_style']) && isset($style_map[$conf['template_style']]) ? $conf['template_style'] : '1';
$theme = $style_map[$tpl_style];

$hometitle = '订单支付 - '.$conf['sitename'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
	<title><?php echo $hometitle?></title>
	<?php if(!empty($conf['favicon'])){?>
	<link rel="icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
	<?php }else{?>
	<link rel="icon" href="assets/img/favicon/favicon.ico" type="image/x-icon" />
	<?php }?>
	<link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	<link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
	<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
	<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
	<style>
	:root{--primary:<?php echo $theme['primary']?>;--primary-hover:<?php echo $theme['primary_hover']?>;--primary-light:<?php echo $theme['primary_light']?>}
	*{margin:0;padding:0;box-sizing:border-box}
	body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Hiragino Sans GB","Microsoft YaHei",sans-serif;background:#F5F6FA;color:#333;line-height:1.6}
	a{text-decoration:none;color:inherit}
	.nova-header{background:#1F2937;color:#fff;position:sticky;top:0;z-index:1000;box-shadow:0 2px 8px rgba(0,0,0,.1)}
	.nova-header-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;padding:0 20px;height:60px}
	.nova-logo{display:flex;align-items:center;gap:10px;font-size:20px;font-weight:700}
	.nova-logo img{height:32px;border-radius:6px}
	.nova-nav{display:flex;gap:8px}
	.nova-nav a{padding:8px 14px;border-radius:6px;font-size:14px}
	.nova-nav a:hover{background:rgba(255,255,255,.1)}
	.nova-container{max-width:700px;margin:0 auto;padding:24px 20px}
	.nova-card{background:#fff;border-radius:16px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.04)}
	.nova-success-icon{text-align:center;margin-bottom:20px}
	.nova-success-icon i{font-size:48px;color:#00B42A}
	.nova-success-title{text-align:center;font-size:22px;font-weight:600;margin-bottom:24px}
	.nova-order-info{background:#F7F8FA;border-radius:12px;padding:20px;margin-bottom:24px}
	.nova-order-row{display:flex;justify-content:space-between;padding:8px 0;font-size:14px}
	.nova-order-row .label{color:#999}
	.nova-order-row .value{color:#333;font-weight:500}
	.nova-order-row .price{color:var(--primary);font-size:20px;font-weight:700}
	.nova-pay-title{font-size:16px;font-weight:600;margin-bottom:14px}
	.nova-pay-methods{display:flex;flex-direction:column;gap:10px;margin-bottom:20px}
	.nova-pay-method{display:flex;align-items:center;gap:12px;padding:14px 16px;border:2px solid #E5E6EB;border-radius:10px;cursor:pointer;transition:all .2s}
	.nova-pay-method:hover{border-color:var(--primary-light)}
	.nova-pay-method.active{border-color:var(--primary);background:var(--primary-light)}
	.nova-pay-method .icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff}
	.nova-pay-method.alipay .icon{background:#1677FF}
	.nova-pay-method.wxpay .icon{background:#07C160}
	.nova-pay-method.qqpay .icon{background:#12B7F5}
	.nova-pay-method.rmb .icon{background:#FF7D00}
	.nova-pay-method .name{font-size:14px;font-weight:500}
	.nova-pay-method .desc{font-size:12px;color:#999}
	.nova-pay-btn{display:block;width:100%;padding:14px;background:var(--primary);color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;transition:all .2s}
	.nova-pay-btn:hover{background:var(--primary-hover)}
	.nova-cancel-btn{display:block;width:100%;padding:12px;background:#fff;color:#666;border:1px solid #E5E6EB;border-radius:10px;font-size:14px;cursor:pointer;text-align:center;margin-top:10px}
	.nova-cancel-btn:hover{background:#F5F6FA}
	.nova-tip{text-align:center;color:#999;font-size:12px;margin-top:12px}
	</style>
</head>
<body>

<header class="nova-header">
	<div class="nova-header-inner">
		<a href="./" class="nova-logo">
			<img src="<?php echo $logo?>" alt="logo" onerror="this.style.display='none'">
			<span><?php echo $conf['sitename']?></span>
		</a>
		<nav class="nova-nav">
			<a href="./">首页</a>
			<a href="./?mod=query">订单查询</a>
		</nav>
	</div>
</header>

<div class="nova-container">
	<div class="nova-card">
		<div class="nova-success-icon"><i class="fa fa-check-circle"></i></div>
		<div class="nova-success-title">订单生成成功，请完成支付</div>

		<div class="nova-order-info">
			<div class="nova-order-row">
				<span class="label">订单编号</span>
				<span class="value" style="font-family:monospace"><?php echo $orderid?></span>
			</div>
			<div class="nova-order-row">
				<span class="label">商品名称</span>
				<span class="value"><a href="./?mod=buy&tid=<?php echo $row['tid']?>" target="_blank" style="color:var(--primary)"><?php echo $row['name']?></a></span>
			</div>
			<div class="nova-order-row">
				<span class="label">订单金额</span>
				<span class="price">¥<?php echo $row['money']?></span>
			</div>
			<?php if($islogin2==1){?>
			<div class="nova-order-row">
				<span class="label">可用余额</span>
				<span class="value">¥<?php echo $userrow['rmb']?> <a href="./user/#chongzhi" style="color:var(--primary);font-size:12px">充值</a></span>
			</div>
			<?php }?>
		</div>

		<div class="nova-pay-title">选择支付方式</div>
		<div class="nova-pay-methods">
			<?php if($islogin2==1){?>
			<div class="nova-pay-method rmb active" data-type="rmb">
				<div class="icon"><i class="fa fa-money"></i></div>
				<div><div class="name">余额支付</div><div class="desc">使用账户余额付款</div></div>
			</div>
			<?php }?>
			<?php if($conf['alipay_api']){?>
			<div class="nova-pay-method alipay <?php echo $islogin2!=1?'active':''?>" data-type="alipay">
				<div class="icon"><i class="fa fa-credit-card"></i></div>
				<div><div class="name">支付宝支付</div><div class="desc">推荐使用支付宝</div></div>
			</div>
			<?php }?>
			<?php if($conf['wxpay_api']){?>
			<div class="nova-pay-method wxpay" data-type="wxpay">
				<div class="icon"><i class="fa fa-weixin"></i></div>
				<div><div class="name">微信支付</div><div class="desc">微信扫码支付</div></div>
			</div>
			<?php }?>
			<?php if($conf['qqpay_api']){?>
			<div class="nova-pay-method qqpay" data-type="qqpay">
				<div class="icon"><i class="fa fa-qq"></i></div>
				<div><div class="name">QQ钱包支付</div><div class="desc">QQ钱包快捷支付</div></div>
			</div>
			<?php }?>
		</div>

		<input type="hidden" id="orderid" value="<?php echo $orderid?>">
		<input type="hidden" id="tid" value="<?php echo $row['tid']?>">
		<button class="nova-pay-btn" id="dopay">确认支付</button>
		<a href="javascript:cancel()" class="nova-cancel-btn">取消订单</a>
		<div class="nova-tip">付款后请不要关闭窗口，等待自动跳转</div>
	</div>
</div>

<script>
var currentType = '';
$('.nova-pay-method').click(function(){
	$('.nova-pay-method').removeClass('active');
	$(this).addClass('active');
	currentType = $(this).data('type');
});
// init
$('.nova-pay-method.active').each(function(){currentType=$(this).data('type');});

$('#dopay').click(function(){
	if(!currentType){layer.alert('请选择支付方式');return}
	if(currentType=='rmb'){
		var ii=layer.msg('正在提交订单请稍候...',{icon:16,shade:0.5,time:15000});
		$.ajax({
			type:'POST',url:'ajax.php?act=payrmb',
			data:{orderid:$('#orderid').val()},
			dataType:'json',
			success:function(data){
				layer.close(ii);
				if(data.code==1||data.code==-2){alert(data.msg);window.location.href='?buyok=1';}
				else if(data.code==-3){layer.confirm('你的余额不足，请充值！',{btn:['立即充值','取消']},function(){window.location.href='./user/#chongzhi';});}
				else if(data.code==-4){layer.confirm('你还未登录，是否现在登录？',{btn:['登录','注册','取消']},function(){window.location.href='./user/login.php';},function(){window.location.href='./user/reg.php';});}
				else{layer.alert(data.msg);}
			}
		});
	}else{
		window.location.href='other/submit.php?type='+currentType+'&orderid='+$('#orderid').val();
	}
});

function cancel(){
	$.ajax({
		type:'POST',url:'ajax.php?act=cancel',
		data:{orderid:$('#orderid').val()},
		dataType:'json',
		success:function(){window.location.href='./?mod=buy&tid='+$('#tid').val();}
	});
}
</script>
</body>
</html>

