<?php
/* * 
 * 码支付同步通知页面
 * 安全加固：参数化查询 + 同步跳转仅展示，不做业务处理（发货由异步通知完成）
 */

require_once("./inc.php");
require_once(SYSTEM_ROOT."codepay/codepay_config.php");
ksort($_GET); //排序get参数
reset($_GET); //内部指针指向数组中的第一个元素
$sign = '';
foreach ($_GET AS $key => $val) {
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
} elseif (empty($codepay_config['key']) || empty($_GET['pay_no']) || md5($sign . $codepay_config['key']) != $_GET['sign']) { //不合法的数据 KEY密钥为你的密钥
    showalert('验证失败！',4);
} else { //合法的数据
    $out_trade_no = daddslashes($_GET['param']);
    //支付宝交易号
    $trade_no = daddslashes($_GET['pay_no']);

    $srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);

    // 同步跳转仅展示支付结果，不做业务处理（不更新状态、不发货）
    // 支付确认和发货由异步通知 codepay_notify.php 完成
    showalert('您所购买的商品已付款成功，感谢购买！',1,$out_trade_no,$srow['tid']);
}
?>
