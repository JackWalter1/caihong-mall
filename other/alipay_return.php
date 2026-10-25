<?php
/* *
 * 支付宝同步通知页面
 * 安全加固：参数化查询 + 同步跳转仅展示，不做业务处理（发货由异步通知完成）
 */

require_once("./inc.php");

@header('Content-Type: text/html; charset=UTF-8');

// 定义支付宝配置
$config = array(
    'sign_type' => 'RSA2',
    'alipay_public_key' => $conf['alipay_publickey'],
    'merchant_private_key' => $conf['alipay_privatekey'],
    'charset' => 'UTF-8',
    'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
    'app_id' => $conf['alipay_appid'],
    'notify_url' => $siteurl . 'alipay_notify.php',
    'return_url' => $siteurl . 'alipay_return.php',
);

// 检查并引入支付宝SDK文件
if(file_exists(__DIR__ . "/alipay/AlipayTradeService.php")) {
    require_once(__DIR__ . "/alipay/AlipayTradeService.php");
    require_once(__DIR__ . "/alipay/model/builder/AlipayTradePrecreateContentBuilder.php");
} else {
    die('支付宝SDK文件缺失！');
}

//计算得出通知验证结果
$alipaySevice = new AlipayTradeService($config); 
$verify_result = $alipaySevice->check($_GET);

if($verify_result && ($conf['alipay_api']==1||$conf['alipay_api']==3)) {//验证成功
	//商户订单号
	$out_trade_no = daddslashes($_GET['out_trade_no']);

	$srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);

    // 同步跳转仅展示支付结果，不做业务处理（不更新状态、不发货）
    // 支付确认和发货由异步通知 alipay_notify.php 完成
    showalert('您所购买的商品已付款成功，感谢购买！',1,$out_trade_no,$srow['tid']);
}
else {
    //验证失败
	showalert('验证失败！',4,'shop');
}
?>
