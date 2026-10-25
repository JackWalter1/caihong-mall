<?php
/* *
 * 码支付异步通知页面
 * 安全加固：参数化查询 + 严格金额比较 + 乐观锁 + 0元订单过滤
 */

require_once("./inc.php");
require_once(SYSTEM_ROOT."codepay/codepay_config.php");

// ---------- 安全校验: 只接受POST请求 ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('error: POST only');
}

ksort($_POST); //排序post参数
reset($_POST); //内部指针指向数组中的第一个元素
$sign = '';
foreach ($_POST AS $key => $val) {
    if ($val == '') continue;
    if ($key != 'sign') {
        if ($sign != '') {
            $sign .= "&";
        }
        $sign .= "$key=$val"; //拼接为url参数形式
    }
}

if ($conf['alipay_api']!=5 && $conf['qqpay_api']!=5 && $conf['wxpay_api']!=5) {
    exit('fail');
} elseif (empty($codepay_config['key']) || empty($_POST['pay_no']) || md5($sign . $codepay_config['key']) != $_POST['sign']) { //不合法的数据 KEY密钥为你的密钥
    exit('fail');
} else { //合法的数据

    $out_trade_no = daddslashes($_POST['param']);

    //支付宝交易号
    $trade_no = daddslashes($_POST['pay_no']);
    
    //实付金额
    $total_amount = isset($_POST['money']) ? floatval($_POST['money']) : 0;

    $srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
    if(!$srow) exit('fail');

    // 仅处理未支付订单，并严格校验金额（防止0元订单和金额篡改）
    if($srow['status']==0
        && floatval($srow['money']) > 0
        && round(floatval($srow['money']), 2) === round($total_amount, 2)) {
        // 乐观锁：仅当 status=0 时才更新，防止并发重复发货
        if($DB->exec("UPDATE `pre_pay` SET `status` ='1' WHERE `trade_no`=:trade_no AND `status`='0'", [':trade_no' => $out_trade_no])){
            $DB->exec("UPDATE `pre_pay` SET `endtime` =:date,`api_trade_no` =:trade_no2 WHERE `trade_no`=:trade_no", [':date' => $date, ':trade_no2' => $trade_no, ':trade_no' => $out_trade_no]);
            // 重新读取订单数据，确保获得最新状态
            $srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
            processOrder($srow);
        }
    }
    exit('success');
}

?>
