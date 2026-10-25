<?php
/* *
 * 功能：彩虹易支付页面跳转同步通知页面
 * 版本：3.3
 * 说明：
 * 同步跳转仅用于展示支付结果，不做业务处理（发货等）。
 * 业务处理由异步通知(epay_notify.php)完成。
 */

require_once("./inc.php");

$out_trade_no = isset($_GET['out_trade_no']) ? daddslashes($_GET['out_trade_no']) : exit('error');
$srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
$pay_config = get_pay_api($srow['channel']);

require_once(SYSTEM_ROOT . "epay/epay.config.php");
require_once(SYSTEM_ROOT . "epay/epay_notify.class.php");

// 计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();

if (
    $verify_result &&
    (
        $conf['alipay_api'] == 2 || $conf['qqpay_api'] == 2 || $conf['wxpay_api'] == 2 ||
        $conf['qqpay_api'] == 8 || $conf['wxpay_api'] == 8 || $conf['wxpay_api'] == 9 ||
        $conf['alipay_api'] == 10 || $conf['qqpay_api'] == 10 || $conf['wxpay_api'] == 10
    ) &&
    !empty($pay_config['pid']) && !empty($pay_config['key'])
) {
    $trade_status = $_GET['trade_status'];

    // 同步跳转：确认支付状态并发货
    // 异步通知可能无法到达（如本地测试环境），所以在这里也主动查询易支付API确认并发货
    if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
        // 主动查询易支付API确认支付状态并发货（与getshop.php相同的逻辑）
        if ($srow['status'] == 0 && in_array($srow['channel'], ['epay1', 'epay2', 'epay3', 'epay4', 'recharge_epay'])) {
            $query_url = $pay_config['url'] . 'api.php?act=order&pid=' . $pay_config['pid'] . '&key=' . $pay_config['key'] . '&out_trade_no=' . $out_trade_no;
            $response = @get_curl($query_url);
            if ($response) {
                $result = json_decode($response, true);
                if ($result && isset($result['code']) && $result['code'] == 1 && isset($result['status']) && $result['status'] == 1) {
                    $pay_money = floatval($result['money']);
                    if ($pay_money == floatval($srow['money']) && floatval($srow['money']) > 0) {
                        $updated = $DB->exec("UPDATE `pre_pay` SET `status`='1' WHERE `trade_no`=:trade_no AND `status`='0'", [':trade_no' => $out_trade_no]);
                        if ($updated) {
                            $DB->exec("UPDATE `pre_pay` SET `endtime`=:date,`api_trade_no`=:api_trade_no WHERE `trade_no`=:trade_no", [
                                ':date' => $date,
                                ':api_trade_no' => isset($result['trade_no']) ? $result['trade_no'] : '',
                                ':trade_no' => $out_trade_no
                            ]);
                            $srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
                            processOrder($srow);
                        }
                    }
                }
            }
        }
        showalert('您所购买的商品已付款成功，感谢购买！', 1, $out_trade_no, $srow['tid']);
    } else {
        echo "trade_status=" . htmlspecialchars($_GET['trade_status']);
    }
} else {
    // 验证失败
    showalert('验证失败！', 4, 'shop');
}