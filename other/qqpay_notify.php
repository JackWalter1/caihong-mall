<?php

//---------------------------------------------------------
//QQ钱包支付即时到帐支付后台回调示例，商户按照此文档进行开发即可
//安全加固：参数化查询 + 严格金额比较 + 乐观锁 + 0元订单过滤
//---------------------------------------------------------
require_once("./inc.php");
require_once(SYSTEM_ROOT.'qqpay/qpayNotify.class.php');
@header('Content-Type: text/html; charset=UTF-8');

// ---------- 安全校验: 只接受POST请求 ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("<xml>\n<return_code>FAIL</return_code>\n</xml>");
}

$qpayNotify = new QpayNotify();
$result = $qpayNotify->getParams();
//判断签名
if($qpayNotify->verifySign() && $conf['qqpay_api']==1) {

//判断签名及结果（即时到帐）
	if($result['trade_state'] == "SUCCESS") {
		//商户订单号
		$out_trade_no = daddslashes($result['out_trade_no']);
		//QQ钱包订单号
		$transaction_id = daddslashes($result['transaction_id']);
		//金额,以分为单位
		$total_fee = $result['total_fee'];
		
		//------------------------------
		//处理业务开始
		//------------------------------
		$srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
		
		if($srow['status']==0
			&& floatval($srow['money']) > 0  // 防止0元订单被处理
			// QQ钱包金额单位为分，需要转换为元并严格比较
			&& round(floatval($srow['money']), 2) === round(floatval($total_fee)/100, 2)) {
			// 乐观锁：仅当 status=0 时才更新，防止并发重复发货
			if($DB->exec("UPDATE `pre_pay` SET `status` ='1' WHERE `trade_no`=:trade_no AND `status`='0'", [':trade_no' => $out_trade_no])){
				$DB->exec("UPDATE `pre_pay` SET `endtime` =:date,`api_trade_no` =:transaction_id WHERE `trade_no`=:trade_no", [':date' => $date, ':transaction_id' => $transaction_id, ':trade_no' => $out_trade_no]);
				// 重新读取订单数据，确保获得最新状态
				$srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
				processOrder($srow);
			}
		}
		//------------------------------
		//处理业务完毕
		//------------------------------
		echo "<xml>
<return_code>SUCCESS</return_code>
</xml>";
	} else {
		echo "<xml>
<return_code>FAIL</return_code>
</xml>";
	}

} else {
    //回调签名错误
	echo "<xml>
<return_code>FAIL</return_code>
<return_msg>签名失败</return_msg>
</xml>";
}

 

?>
