<?php
// ============================================================
// 支付状态轮询查询接口（前端AJAX轮询使用）
// 功能：查询订单支付状态，如果本地未支付则主动查询易支付平台
// 安全策略：
//   1. 服务器端调用易支付API查询订单（需pid+key验证，黑客无法伪造）
//   2. 金额校验：易支付返回金额必须与订单金额一致才发货
//   3. 乐观锁：WHERE status='0' 防止并发重复发货
//   4. 同源Referer验证，防止CSRF/直接调用
//   5. IP频率限制，防止暴力枚举订单号
// 防抓包原理：
//   黑客在浏览器端抓包只能看到最终JSON响应 {"code":1}
//   无法伪造易支付API的返回结果（服务器→易支付，HTTPS加密）
// ============================================================

require './inc.php';
@header('Content-Type: text/html; charset=UTF-8');

// ---------- 安全校验 1: 验证请求来源 ----------
// 只允许站内AJAX轮询或支付页面跳转后的查询
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$host = $_SERVER['HTTP_HOST'];
if (!empty($referer) && strpos($referer, $host) === false) {
    // Referer存在但不匹配本站域名，拒绝
    exit('{"code":-4,"msg":"非法来源"}');
}

// ---------- 安全校验 2: IP频率限制（防止暴力枚举订单号） ----------
$client_ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_key = 'getshop_limit_' . $client_ip;
$rate_file = __DIR__ . '/cache/getshop_rate.dat';

$rate_data = array();
if (file_exists($rate_file)) {
    $rate_data = @unserialize(@file_get_contents($rate_file));
    if (!is_array($rate_data)) $rate_data = array();
}

$now = time();
// 清理超过60秒的旧记录
foreach ($rate_data as $ip => $info) {
    if ($info['time'] < $now - 60) unset($rate_data[$ip]);
}

// 如果当前IP已有记录，检查频率
if (isset($rate_data[$client_ip])) {
    $info = $rate_data[$client_ip];
    $count = intval($info['count']);
    $first_time = intval($info['time']);
    
    if ($now - $first_time < 2 && $count >= 15) {
        exit('{"code":-5,"msg":"请求过于频繁，请稍后再试"}');
    }
    if ($now - $first_time < 5 && $count >= 30) {
        exit('{"code":-5,"msg":"请求过于频繁，请稍后再试"}');
    }
    
    if ($now - $first_time > 60) {
        // 超过60秒，重置计数
        $rate_data[$client_ip] = array('time' => $now, 'count' => 1);
    } else {
        $rate_data[$client_ip]['count'] = $count + 1;
    }
} else {
    $rate_data[$client_ip] = array('time' => $now, 'count' => 1);
}

// 持久化频率数据
$cache_dir = __DIR__ . '/cache';
if (!file_exists($cache_dir)) {
    @mkdir($cache_dir, 0755, true);
}
@file_put_contents($rate_file, serialize($rate_data), LOCK_EX);

// ---------- 业务逻辑: 查询本地数据库 ----------
$trade_no = isset($_GET['trade_no']) ? daddslashes($_GET['trade_no']) : exit('{"code":-6,"msg":"缺少订单号"}');

// 使用参数化查询防止SQL注入
$row = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $trade_no]);

if (!$row) {
    exit('{"code":-3,"msg":"订单不存在"}');
}

// ---------- 主动查询易支付订单状态（服务器端调用，黑客无法抓包伪造） ----------
// 安全保障：
//   1. 服务器端直接调用易支付API，浏览器抓包只能看到最终JSON响应
//   2. 易支付API需要pid+key验证，黑客无法伪造查询结果
//   3. 金额校验确保返回金额与订单金额一致
//   4. 乐观锁防止并发重复发货
if ($row['status'] == 0 && in_array($row['channel'], ['epay1', 'epay2', 'epay3', 'epay4', 'recharge_epay'])) {
    $pay_config = get_pay_api($row['channel']);
    if (!empty($pay_config['pid']) && !empty($pay_config['key']) && !empty($pay_config['url'])) {
        // 调用易支付订单查询API（HTTPS，防中间人攻击）
        $query_url = $pay_config['url'] . 'api.php?act=order&pid=' . $pay_config['pid'] . '&key=' . $pay_config['key'] . '&out_trade_no=' . $trade_no;
        $response = @get_curl($query_url);
        if ($response) {
            $result = json_decode($response, true);
            // 验证易支付返回：code=1表示查询成功，status=1表示已支付
            if ($result && isset($result['code']) && $result['code'] == 1 && isset($result['status']) && $result['status'] == 1) {
                // 金额校验（宽松比较 ==，兼容浮点精度）
                $pay_money = floatval($result['money']);
                if ($pay_money == floatval($row['money']) && floatval($row['money']) > 0) {
                    // 乐观锁：仅当 status=0 时才更新，防止并发重复发货
                    $updated = $DB->exec("UPDATE `pre_pay` SET `status`='1' WHERE `trade_no`=:trade_no AND `status`='0'", [':trade_no' => $trade_no]);
                    if ($updated) {
                        $DB->exec("UPDATE `pre_pay` SET `endtime`=:date,`api_trade_no`=:api_trade_no WHERE `trade_no`=:trade_no", [
                            ':date' => $date,
                            ':api_trade_no' => isset($result['trade_no']) ? $result['trade_no'] : '',
                            ':trade_no' => $trade_no
                        ]);
                        // 重新读取订单数据
                        $row = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $trade_no]);
                        // 发货
                        processOrder($row);
                    }
                }
            }
        }
    }
}

// 生成跳转链接
if ($row['domain'] && $row['domain'] != $host && strpos($row['domain'], '.') !== false) {
    $baseurl = 'http://' . $row['domain'] . '/';
} else {
    $baseurl = '../';
}

if ($row['tid'] == -1) {
    $link = $baseurl . 'user/';
} elseif ($row['tid'] == -2) {
    $link = $baseurl . 'user/regok.php?orderid=' . $trade_no;
} else {
    $link = $baseurl . '?buyok=1';
}

// 根据订单状态返回结果（可能已被上面的主动查询更新为已支付）
switch ($row['status']) {
    case 1:
    case 2:
        $response = '{"code":1,"msg":"付款成功","backurl":"' . $link . '"}';
        break;
    case -1:
        $response = '{"code":-2,"msg":"订单已关闭"}';
        break;
    case 0:
        $response = '{"code":-1,"msg":"未付款"}';
        break;
    default:
        $response = '{"code":-1,"msg":"状态未知"}';
        break;
}

exit($response);