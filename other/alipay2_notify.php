<?php
/* *
 * 功能：支付宝（卡易信）服务器异步通知页面
 * 安全加固：参数化查询 + 严格金额比较 + 乐观锁 + 0元订单过滤
 */

require_once("./inc.php");
require_once(SYSTEM_ROOT."epay/alipay.config.php");
require_once(SYSTEM_ROOT."epay/alipay_notify.class.php");

// ---------- 安全校验: 只接受POST请求 ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('error: POST only');
}

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result && $conf['alipay_api']==7) {//验证成功
	//商户订单号
	$out_trade_no = daddslashes($_POST['out_trade_no']);

	//支付宝交易号
	$trade_no = daddslashes($_POST['trade_no']);

	//交易状态
	$trade_status = $_POST['trade_status'];

	//交易金额
	$total_fee = $_POST['total_fee'];

	$srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);

    if($_POST['trade_status'] == 'TRADE_SUCCESS' && $srow['status']==0
        && floatval($srow['money']) > 0  // 防止0元订单被处理
        && round(floatval($srow['money']), 2) === round(floatval($total_fee), 2)) {  // 严格金额比较
		// 乐观锁：仅当 status=0 时才更新，防止并发重复发货
		if($DB->exec("UPDATE `pre_pay` SET `status` ='1' WHERE `trade_no`=:trade_no AND `status`='0'", [':trade_no' => $out_trade_no])){
			$DB->exec("UPDATE `pre_pay` SET `endtime` =:date,`api_trade_no` =:trade_no2 WHERE `trade_no`=:trade_no", [':date' => $date, ':trade_no2' => $trade_no, ':trade_no' => $out_trade_no]);
			// 重新读取订单数据，确保获得最新状态
			$srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
			processOrder($srow);
		}
    }

	echo "success";
}
else {
    //验证失败
    echo "fail";
}
?>
