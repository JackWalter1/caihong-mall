<?php
/* * 
 * 功能：支付宝（卡易信）页面跳转同步通知页面
 * 安全加固：参数化查询 + 同步跳转仅展示，不做业务处理（发货由异步通知完成）
 *           修复：原代码错误使用 $_POST（同步跳转为GET方式），导致同步验证失效
 */

require_once("./inc.php");
require_once(SYSTEM_ROOT."epay/alipay.config.php");
require_once(SYSTEM_ROOT."epay/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result && $conf['alipay_api']==7) {
	//商户订单号 - 同步跳转为GET方式
	$out_trade_no = daddslashes($_GET['out_trade_no']);

	$srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);

    // 同步跳转仅展示支付结果，不做业务处理（不更新状态、不发货）
    // 支付确认和发货由异步通知 alipay2_notify.php 完成
    showalert('您所购买的商品已付款成功，感谢购买！',1,$out_trade_no,$srow['tid']);
}
else {
    //验证失败
	showalert('验证失败！',4);
}
?>
