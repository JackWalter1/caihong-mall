<?php
/**
 * 买家订单查询后端（订单查询页配套）
 * act=code   输出图形验证码（存 session）
 * act=query  校验验证码后按 订单号/预留联系方式 查询订单
 */
include __DIR__ . '/../../includes/common.php';

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
@header('Content-Type: application/json; charset=UTF-8');

if ($act == 'code') {
    header('Content-Type: image/png');
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 4; $i++) $code .= $chars[mt_rand(0, strlen($chars) - 1)];
    $_SESSION['oq_code'] = strtolower($code);
    $_SESSION['oq_time'] = time();
    $w = 250; $h = 60;
    $im = imagecreatetruecolor($w, $h);
    $bg = imagecolorallocate($im, 246, 248, 251);
    imagefill($im, 0, 0, $bg);
    for ($i = 0; $i < 5; $i++) {
        $lc = imagecolorallocate($im, mt_rand(180, 230), mt_rand(180, 230), mt_rand(180, 230));
        imageline($im, mt_rand(0, $w), mt_rand(0, $h), mt_rand(0, $w), mt_rand(0, $h), $lc);
    }
    for ($i = 0; $i < 4; $i++) {
        $x = 28 + $i * 56 + mt_rand(-4, 4);
        $y = mt_rand(34, 44);
        $c = imagecolorallocate($im, mt_rand(20, 90), mt_rand(20, 90), mt_rand(110, 190));
        imagestring($im, 5, $x, $y - 16, $code[$i], $c);
        // 简单扭曲线条干扰
        $lc = imagecolorallocate($im, mt_rand(140, 200), mt_rand(140, 200), mt_rand(140, 200));
        imagearc($im, $x + 10, $y - 6, mt_rand(18, 30), mt_rand(10, 20), mt_rand(0, 180), mt_rand(180, 360), $lc);
    }
    imagepng($im);
    imagedestroy($im);
    exit;
}

if ($act == 'query') {
    if (empty($_SESSION['oq_code'])) exit('{"code":-1,"msg":"验证码已过期，请刷新重试"}');
    if (time() - intval($_SESSION['oq_time'] ?? 0) > 600) { unset($_SESSION['oq_code']); exit('{"code":-1,"msg":"验证码已过期，请刷新重试"}'); }
    $in = strtolower(trim($_POST['code'] ?? ''));
    if ($in === '' || $in !== $_SESSION['oq_code']) {
        unset($_SESSION['oq_code']);
        exit('{"code":-1,"msg":"验证码错误"}');
    }
    unset($_SESSION['oq_code']);

    $kw = trim($_POST['keyword'] ?? '');
    if ($kw === '' || mb_strlen($kw) > 64) exit('{"code":-1,"msg":"请输入正确的订单号/联系方式"}');

    $rs = $DB->query(
        "SELECT o.tid, o.input, o.value, o.status, o.result, o.tradeno, o.money, o.addtime, t.name AS name
         FROM pre_orders o LEFT JOIN pre_tools t ON t.tid = o.tid
         WHERE o.tradeno = :kw1 OR o.input = :kw2
         ORDER BY o.id DESC LIMIT 20",
        array(':kw1' => $kw, ':kw2' => $kw)
    );
    $data = array();
    while ($res = $rs->fetch(PDO::FETCH_ASSOC)) {
        $data[] = array(
            'tradeno' => $res['tradeno'],
            'name'    => $res['name'] !== null ? $res['name'] : '商品已下架',
            'value'   => $res['value'],
            'money'   => $res['money'],
            'status'  => intval($res['status']),
            'result'  => (intval($res['status']) == 2) ? $res['result'] : '',
            'addtime' => $res['addtime'],
        );
    }
    exit(json_encode(array('code' => 0, 'msg' => 'succ', 'data' => $data), JSON_UNESCAPED_UNICODE));
}

exit('{"code":-1,"msg":"参数错误"}');
