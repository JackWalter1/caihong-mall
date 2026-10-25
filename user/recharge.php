<?php
/**
 * 充值余额
**/
include("../includes/common.php");
$title='充值余额';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php?back=recharge';</script>");
?>
<style>
img.logo{width: 20px;margin: -2px 5px 0 5px;}
</style>
<div class="wrapper">
<div class="col-md-6">
<div class="panel panel-default">
    <div class="panel-heading font-bold">
		充值余额
	</div>
	<div class="panel-body text-center">
<?php 
$rebate_enabled = isset($conf['recharge_rebate_enabled']) ? $conf['recharge_rebate_enabled'] : 1;
$rebate_rate = isset($conf['recharge_rebate_rate']) ? $conf['recharge_rebate_rate'] : 3;
if ($rebate_enabled == 1) {
    $example_rebate = round(100 * ($rebate_rate / 100), 2);
    $example_total = 100 + $example_rebate;
    $example_rebate2 = round(200 * ($rebate_rate / 100), 2);
    $example_total2 = 200 + $example_rebate2;
    echo '<div class="alert alert-success text-left">
<b>充值返利活动：</b>所有用户充值均可获得 <font color="red"><strong>' . $rebate_rate . '%</strong></font> 的额外余额返利！<br/>
例如：充值100元，实际到账' . $example_total . '元；充值200元，实际到账' . $example_total2 . '元<br/>
返利金额将在充值成功后自动发放到您的账户余额中
</div>';
}
?>
			<b>我当前的账户余额：<span style="font-size:16px; color:#FF6133;"><?php echo $userrow['rmb']?></span> 元</b>
			<hr>
			<input type="text" class="form-control" name="value" id="rechargeAmount" autocomplete="off" placeholder="输入要充值的余额" oninput="calculateRebate()"><br>
			<div class="alert alert-info text-left" id="rebateInfo" style="display:none;">
				<b>预计返利金额：</b><span id="rebateAmount" style="font-size:16px; color:#FF6133;">0.00</span> 元<br>
				<b>实际到账金额：</b><span id="totalAmount" style="font-size:16px; color:#FF6133;">0.00</span> 元
			</div>
<?php 
if(!empty($conf['codepay_key']))echo '<button type="button" class="btn btn-default" id="buy_usdt"><img src="../other/usdt-trc20/static/img/tether.svg" class="logo">USDT - TRC20</button>&nbsp;';
if($conf['alipay_api'])echo '<button type="button" class="btn btn-default" id="buy_alipay"><img src="../assets/img/alipay.png" class="logo">支付宝</button>&nbsp;';
if($conf['qqpay_api'])echo '<button type="button" class="btn btn-default" id="buy_qqpay"><img src="../assets/img/qqpay.png" class="logo">QQ钱包</button>&nbsp;';
if($conf['wxpay_api'])echo '<button type="button" class="btn btn-default" id="buy_wxpay"><img src="../assets/img/wxpay.png" class="logo">微信支付</button>&nbsp;';
?>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModa4" id="alink" style="visibility: hidden;"></button>
<hr>
<?php if($conf['tixian_limit']==1){?><small style="color:red;">提示：充值的余额只能用于消费，无法提现</small><?php }?><?php if($conf['recharge_min']>0){?><small style="color:red;">，最低充值<?php echo $conf['recharge_min']?>元</small><?php }?>

	</div>
</div>
<?php if($conf['fenzhan_jiakuanka']){?>
<div class="panel panel-default">
    <div class="panel-heading font-bold">
		使用加款卡充值余额
	</div>
	<div class="panel-body text-center">
			<input type="text" class="form-control" name="km" autocomplete="off" placeholder="请输入加款卡密"><br>
			<button type="submit" class="btn btn-primary" id="usekm">立即使用</button>
							<a href="https://chongzk.xmkj01.cn/" class="btn btn-default"><i class="fa fa-list-alt"></i> 零手续加款卡</a>
	</div>
</div>
<?php }?>
</div>
<div class="col-md-6">
	<div class="panel panel-default">
    <div class="panel-heading font-bold">充值记录</div>
		  <div class="panel-body">

      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>充值金额</th><th>充值时间</th><th>状态</th></tr></thead>
          <tbody>
<?php
$flag=false;
$rs=$DB->query("SELECT * FROM pre_points WHERE zid='{$userrow['zid']}' AND action='充值' ORDER BY id DESC LIMIT 10");
while($res = $rs->fetch())
{
$flag=true;
echo '<tr><td><b>'.$res['point'].'</b></td><td>'.$res['addtime'].'</td><td><font color="green">已完成</font></td></tr>';
}
if(!$flag)echo '<tr class="no-records-found"><td colspan="99">暂无充值记录</td></tr>';
?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
 </div>
</div>
<?php include './foot.php';?>
<script>
function calculateRebate() {
	var value = parseFloat($("input[name='value']").val());
	var rebateInfo = document.getElementById('rebateInfo');
	var rebateAmount = document.getElementById('rebateAmount');
	var totalAmount = document.getElementById('totalAmount');
	var rebateEnabled = <?php echo isset($conf['recharge_rebate_enabled']) ? $conf['recharge_rebate_enabled'] : 1;?>;
	var rebateRate = <?php echo isset($conf['recharge_rebate_rate']) ? $conf['recharge_rebate_rate'] : 3;?>;
	
	if (rebateEnabled == 1 && !isNaN(value) && value > 0) {
		var rebate = value * (rebateRate / 100);
		var total = value + rebate;
		rebateAmount.textContent = rebate.toFixed(2);
		totalAmount.textContent = total.toFixed(2);
		rebateInfo.style.display = 'block';
	} else {
		rebateInfo.style.display = 'none';
	}
}

function dopay(type){
	var value=$("input[name='value']").val();
	if(value=='' || value==0){layer.alert('充值金额不能为空');return false;}
	var ii = layer.msg('正在提交订单请稍候...', {icon: 16, shade: 0.3, time: 3000});
	$.ajax({
		type : 'GET',
		url : "ajax_user.php?act=recharge&type="+type+"&value="+value,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				window.location.href='../other/submit.php?type='+type+'&orderid='+data.trade_no;
			}else{
				layer.alert(data.msg || '提交失败，请稍后重试');
			}
		},
		error : function(xhr, status, error) {
			layer.close(ii);
			layer.alert('请求失败('+status+')，请检查网络后重试');
		}
	});
}
$(document).ready(function(){
$("#buy_usdt").click(function(){
	dopay('usdt_pay')
});
$("#buy_alipay").click(function(){
	var rechargeApi = <?php echo isset($conf['recharge_alipay_api']) ? (int)$conf['recharge_alipay_api'] : 0;?>;
	dopay(rechargeApi > 0 ? 'recharge_alipay' : 'alipay')
});
$("#buy_qqpay").click(function(){
	var rechargeApi = <?php echo isset($conf['recharge_qqpay_api']) ? (int)$conf['recharge_qqpay_api'] : 0;?>;
	dopay(rechargeApi > 0 ? 'recharge_qqpay' : 'qqpay')
});
$("#buy_wxpay").click(function(){
	var rechargeApi = <?php echo isset($conf['recharge_wxpay_api']) ? (int)$conf['recharge_wxpay_api'] : 0;?>;
	dopay(rechargeApi > 0 ? 'recharge_wxpay' : 'wxpay')
});
$("#usekm").click(function(){
	var km = $("input[name='km']").val();
	$.ajax({
		type : "POST",
		url : "ajax_user.php?act=usekm",
		data : {km:km},
		dataType : 'json',
		async: true,
		success : function(data) {
			if(data.code == 0){
				layer.alert(data.msg, {icon:1}, function(){ window.location.reload() });
			}else{
				layer.alert(data.msg, {icon:2});
			}
		}
	});
});
})
</script>