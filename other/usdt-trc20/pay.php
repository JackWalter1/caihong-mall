<?php

// 开启输出缓冲，防止意外输出
ob_start();

require '../inc.php';
require './inc.php';

@header('Content-Type: text/html; charset=UTF-8');
// 清空输出缓冲
ob_end_clean();
$trade_no = daddslashes($_GET['trade_no']);
if (!is_numeric($trade_no)) {
    exit('订单号不符合要求!');
}
if (empty($conf['codepay_key'])) {
    exit('当前支付接口未开启');
}
$row = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $trade_no]);
if (!$row) {
    exit('该订单号不存在，请返回来源地重新发起请求！');
}

// 确保$USDT_VALID_TIME是分钟
if (!is_numeric($USDT_VALID_TIME)) {
    $USDT_VALID_TIME = 30; // 默认30分钟
}

$valid = (strtotime($row['addtime']) + $USDT_VALID_TIME * 60) * 1000;
$lock  = sys_get_temp_dir() . '/usdt-trc20_pay_' . $trade_no . '.dat';
if (file_exists($lock)) {
    $usdt = file_get_contents($lock);
} else {
    $rate    = getLatestRate();
    $usdt    = round($row['money'] / $rate, 2);
    $addTime = date('Y-m-d H:i:s', strtotime($row['addtime']) - $USDT_VALID_TIME * 60);
    $exist   = $DB->getRow("select * from pre_pay where type = 'usdt_pay' and trade_no != :trade_no and money = :money and status = 0 and addtime >= :addtime order by trade_no desc limit 1", [':trade_no' => $row['trade_no'], ':money' => $row['money'], ':addtime' => $addTime]);
    if ($exist) {
        $dat  = sys_get_temp_dir() . '/usdt-trc20_pay_' . $exist['trade_no'] . '.dat';
        if (file_exists($dat)) {
            $exist_usdt = file_get_contents($dat);
            if (function_exists('bcadd')) {
                $usdt = bcadd($exist_usdt, 0.01, 2);
            } else {
                $usdt = $exist_usdt + 0.01;
                $usdt = round($usdt, 2);
            }
        }
    }

    file_put_contents($lock, $usdt);
}

include_once SYSTEM_ROOT . 'usdt-trc20/themes.php';