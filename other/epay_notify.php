<?php
/* *
 * 功能：彩虹易支付服务器异步通知页面
 * 说明：
 *   此页面接收支付平台服务器的POST异步通知。
 *   只有通过签名验证 + 服务器二次确认的请求才会处理订单。
 *   使用严格金额校验、防止0元订单和金额篡改。
 */

// ---------- 日志记录 ----------
$log_dir = __DIR__ . '/log';
if(!file_exists($log_dir)) {
    @mkdir($log_dir, 0755, true);
}
$log_file = $log_dir . '/epay_notify.log';
$log = @fopen($log_file, 'a');
function _epay_log($msg) {
	global $log;
	if($log) fwrite($log, $msg);
}

_epay_log("\n\n" . str_repeat('=', 50) . "\n");
_epay_log('通知时间: ' . date('Y-m-d H:i:s') . "\n");
_epay_log('请求方法: ' . $_SERVER['REQUEST_METHOD'] . "\n");
_epay_log('客户端IP: ' . $_SERVER['REMOTE_ADDR'] . "\n");
_epay_log('POST数据: ' . json_encode($_POST, JSON_UNESCAPED_UNICODE) . "\n");

require_once("./inc.php");

// 安全写日志的辅助函数（inc.php加载后重新打开）
if(!$log) {
    $log = @fopen($log_file, 'a');
}

// ---------- 安全校验: 只接受POST请求 ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    _epay_log('错误: 非POST请求，拒绝' . "\n");
    exit('error: POST only');
}

$out_trade_no = isset($_POST['out_trade_no']) ? daddslashes($_POST['out_trade_no']) : exit('error: no out_trade_no');

// ---------- 获取订单信息和渠道配置 ----------
$srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
if (!$srow) {
    _epay_log('错误: 订单不存在 ' . $out_trade_no . "\n");
    exit('error: order not found');
}
$pay_config = get_pay_api($srow['channel']);

_epay_log('订单号: ' . $out_trade_no . "\n");
_epay_log('订单金额: ' . $srow['money'] . ' 订单状态: ' . $srow['status'] . ' 通道: ' . $srow['channel'] . "\n");

require_once(SYSTEM_ROOT . "epay/epay.config.php");
require_once(SYSTEM_ROOT . "epay/epay_notify.class.php");

// 计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

_epay_log('签名验证结果: ' . ($verify_result ? '成功' : '失败') . "\n");

// 检查支付通道是否启用
$channel_enabled = (
    $conf['alipay_api'] == 2 || $conf['qqpay_api'] == 2 || $conf['wxpay_api'] == 2 ||
    $conf['qqpay_api'] == 8 || $conf['wxpay_api'] == 8 || $conf['wxpay_api'] == 9 ||
    $conf['alipay_api'] == 10 || $conf['qqpay_api'] == 10 || $conf['wxpay_api'] == 10
);

if (
    $verify_result &&
    $channel_enabled &&
    !empty($pay_config['pid']) && !empty($pay_config['key'])
) {
    $trade_no = daddslashes($_POST['trade_no']);
    $trade_status = $_POST['trade_status'];
    $money = $_POST['money'];

    _epay_log('易支付交易号: ' . $trade_no . "\n");
    _epay_log('易支付交易状态: ' . $trade_status . "\n");
    _epay_log('易支付金额: ' . $money . "\n");

    // ---------- 支付成功业务处理 ----------
    if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
        if ($srow['status'] != 0) {
            _epay_log('提示: 订单已处理过，status=' . $srow['status'] . "\n");
            echo "success";
            exit;
        }
        if (floatval($srow['money']) <= 0) {
            _epay_log('错误: 订单金额为0，拒绝处理' . "\n");
            echo "fail";
            exit;
        }
        // 金额校验（使用宽松比较 ==，兼容浮点精度）
        if (round(floatval($srow['money']), 2) == round(floatval($money), 2)) {
            // 乐观锁：仅当 status=0 时才更新，防止并发重复发货
            $updated = $DB->exec("UPDATE `pre_pay` SET `status` ='1' WHERE `trade_no`=:trade_no AND `status`='0'", [':trade_no' => $out_trade_no]);
            if ($updated) {
                $DB->exec("UPDATE `pre_pay` SET `endtime` =:date,`api_trade_no` =:trade_no2 WHERE `trade_no`=:trade_no", [
                    ':date' => $date,
                    ':trade_no2' => $trade_no,
                    ':trade_no' => $out_trade_no
                ]);
                $srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
                _epay_log('订单状态更新成功，开始发货 processOrder()' . "\n");
                processOrder($srow);
                _epay_log('订单处理完成' . "\n");
                echo "success";
            } else {
                _epay_log('错误: 乐观锁更新失败，订单可能已被其他进程处理' . "\n");
                echo "success";
            }
        } else {
            _epay_log('错误: 金额不匹配 订单金额=' . $srow['money'] . ' 易支付金额=' . $money . "\n");
            echo "fail";
        }
    } else {
        _epay_log('错误: 交易状态不是TRADE_SUCCESS: ' . $trade_status . "\n");
        echo "fail";
    }
} else {
    _epay_log('错误: 签名验证失败或通道未启用或配置不完整' . "\n");
    echo "fail";
}

if($log) fclose($log);
