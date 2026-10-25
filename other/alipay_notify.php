<?php
/* *
 * 支付宝异步通知页面
 */

// 确保日志目录存在
$log_dir = __DIR__ . '/log';
if(!file_exists($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// 开启日志记录
$log_file = $log_dir . '/alipay_notify.log';
$log = fopen($log_file, 'a');
fwrite($log, "\n\n" . str_repeat('=', 50) . "\n");
fwrite($log, '通知时间: ' . date('Y-m-d H:i:s') . "\n");
fwrite($log, '请求方法: ' . $_SERVER['REQUEST_METHOD'] . "\n");
fwrite($log, '客户端IP: ' . $_SERVER['REMOTE_ADDR'] . "\n");
fwrite($log, 'POST数据: ' . json_encode($_POST) . "\n");
fwrite($log, 'GET数据: ' . json_encode($_GET) . "\n");
fwrite($log, 'SERVER信息: ' . json_encode($_SERVER) . "\n");

// 加载配置文件
require_once("./inc.php");
fwrite($log, '配置加载成功\n');
fwrite($log, 'alipay_api配置: ' . $conf['alipay_api'] . "\n");
// 脱敏：不记录完整密钥，仅记录是否已配置，防止日志泄露
fwrite($log, '支付宝公钥是否配置: ' . (empty($conf['alipay_publickey']) ? '否' : '是') . "\n");
fwrite($log, '支付宝私钥是否配置: ' . (empty($conf['alipay_privatekey']) ? '否' : '是') . "\n");
fwrite($log, '支付宝APPID是否配置: ' . (empty($conf['alipay_appid']) ? '否' : '是') . "\n");

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
fwrite($log, '配置定义成功\n');

// 检查并引入支付宝SDK文件
if(file_exists(__DIR__ . "/alipay/AlipayTradeService.php")) {
    require_once(__DIR__ . "/alipay/AlipayTradeService.php");
    require_once(__DIR__ . "/alipay/model/builder/AlipayTradePrecreateContentBuilder.php");
    fwrite($log, 'SDK文件加载成功\n');
} else {
    fwrite($log, 'SDK文件缺失\n');
    fclose($log);
    die('支付宝SDK文件缺失！');
}

//计算得出通知验证结果
$alipaySevice = new AlipayTradeService($config); 
fwrite($log, 'AlipayTradeService实例化成功\n');

// 记录详细的验证过程
$verify_result = $alipaySevice->check($_POST);
fwrite($log, '签名验证结果: ' . ($verify_result ? '成功' : '失败') . "\n");
fwrite($log, '验证条件结果: ' . (($verify_result && ($conf['alipay_api']==1||$conf['alipay_api']==3)) ? '满足' : '不满足') . "\n");

if($verify_result && ($conf['alipay_api']==1||$conf['alipay_api']==3)) {//验证成功
	//商户订单号

	$out_trade_no = daddslashes($_POST['out_trade_no']);

	//支付宝交易号

	$trade_no = daddslashes($_POST['trade_no']);

	//交易状态
	$trade_status = $_POST['trade_status'];

	//买家支付宝
	$buyer_id = daddslashes($_POST['buyer_id']);

	//交易金额
	$total_amount = $_POST['total_amount'];

	fwrite($log, '订单处理开始: ' . $out_trade_no . "\n");
	fwrite($log, '支付宝交易号: ' . $trade_no . "\n");
	fwrite($log, '交易状态: ' . $trade_status . "\n");
	fwrite($log, '交易金额: ' . $total_amount . "\n");

	$srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
	fwrite($log, '订单查询结果: ' . ($srow ? '找到' : '未找到') . "\n");
	if($srow) {
	    fwrite($log, '订单当前状态: ' . $srow['status'] . "\n");
	}

    // 根据不同的交易状态进行处理
    switch($_POST['trade_status']) {
        case 'TRADE_SUCCESS':
        case 'TRADE_FINISHED':
            // 付款完成后，支付宝系统发送该交易状态通知
            if($srow && $srow['status']==0
                && floatval($srow['money']) > 0  // 防止0元订单被处理
                && round(floatval($srow['money']), 2) === round(floatval($total_amount), 2)) {  // 严格金额比较
                fwrite($log, '开始更新订单状态为已支付
');
                // 乐观锁：仅当 status=0 时才更新，防止并发重复发货
                if($DB->exec("UPDATE `pre_pay` SET `status` ='1' WHERE `trade_no`=:trade_no AND `status`='0'", [':trade_no' => $out_trade_no])){
                    fwrite($log, '订单状态更新成功
');
                    $DB->exec("UPDATE `pre_pay` SET `endtime` =:date,`api_trade_no` =:trade_no2 WHERE `trade_no`=:trade_no", [':date' => $date, ':trade_no2' => $trade_no, ':trade_no' => $out_trade_no]);
                    fwrite($log, '订单时间和交易号更新成功
');
                    // 重新读取订单数据，确保获得最新状态
                    $srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
                    // 调用processOrder函数处理订单
                    fwrite($log, '开始处理订单
');
                    processOrder($srow);
                    fwrite($log, '订单处理完成
');
                }
            } else {
                fwrite($log, '金额或状态校验失败：订单金额(' . $srow['money'] . ') 实付金额(' . $total_amount . ') 订单状态(' . $srow['status'] . ')\n');
            }
            break;
        case 'TRADE_CLOSED':
            // 交易关闭，包括未付款超时关闭和支付后全额退款
            fwrite($log, '开始处理交易关闭状态
');
            // 更新本地订单状态为已关闭
            $update_sql = "UPDATE `pre_pay` SET `status` ='-1' WHERE `trade_no`=:trade_no";
            fwrite($log, '执行SQL: ' . $update_sql . "\n");
            $update_result = $DB->exec($update_sql, [':trade_no' => $out_trade_no]);
            fwrite($log, '本地订单状态更新为已关闭，结果: ' . $update_result . "\n");
            break;
        default:
            // 其他状态
            fwrite($log, '收到未知交易状态: ' . $_POST['trade_status'] . "\n");
            break;
    }

	echo "success";
    fwrite($log, '返回success\n');
    fclose($log);
}
else {
    //验证失败
    fwrite($log, '验证失败\n');
    fwrite($log, '返回fail\n');
    fclose($log);
    echo "fail";
}
?>