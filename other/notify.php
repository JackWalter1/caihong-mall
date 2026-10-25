<?php
require 'inc.php';

// ============ 兼容多种服务器环境获取回调路径 ============
// 部分环境（如 Nginx/宝塔未开启 PATH_INFO、或开启 cgi.fix_pathinfo 限制）下
// $_SERVER['PATH_INFO'] 为空，导致货源异步回调 /shequ/{id}/{tradeno}/ 无法解析，
// 而支付回调(如 wxpay_notify.php)不依赖 PATH_INFO，故出现"支付回调正常、货源回调失败"。
// 此处依次从 PATH_INFO / ORIG_PATH_INFO / REQUEST_URI 中解析回调路径。
$path_info = '';
if (!empty($_SERVER['PATH_INFO'])) {
    $path_info = $_SERVER['PATH_INFO'];
} elseif (!empty($_SERVER['ORIG_PATH_INFO'])) {
    $path_info = $_SERVER['ORIG_PATH_INFO'];
} elseif (!empty($_SERVER['REQUEST_URI'])) {
    $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($request_uri) {
        $script_name = str_replace('\\', '/', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '');
        if ($script_name && strpos($request_uri, $script_name) !== false) {
            $path_info = substr($request_uri, strpos($request_uri, $script_name) + strlen($script_name));
        } else {
            // 兜底：截取 notify.php 之后的部分
            $pos = stripos($request_uri, 'notify.php');
            if ($pos !== false) {
                $path_info = substr($request_uri, $pos + strlen('notify.php'));
            } else {
                $path_info = $request_uri;
            }
        }
    }
}

$urlarr = explode('/', trim($path_info, '/'));
$act = isset($urlarr[0]) ? $urlarr[0] : '';
$shequid = isset($urlarr[1]) ? intval($urlarr[1]) : 0;
$tradeno = isset($urlarr[2]) ? daddslashes($urlarr[2]) : '';

// 兼容 GET/POST 方式传参回调（?shequ=1&tradeno=xxx）
if (!$shequid && isset($_REQUEST['shequ'])) {
    $shequid = intval($_REQUEST['shequ']);
}
if ($tradeno === '' && isset($_REQUEST['tradeno'])) {
    $tradeno = daddslashes($_REQUEST['tradeno']);
}

@header('Content-Type: text/html; charset=UTF-8');

$is_shequ = ($act == 'shequ');
if (!$is_shequ && $shequid && $tradeno) {
    // 兼容未带 /shequ/ 路径前缀、仅通过 GET/POST 传参的回调
    $is_shequ = true;
}

if ($is_shequ) {
    if (!$shequid) exit('No Shequ ID');
    if (!$tradeno) exit('No tradeno');
    // 使用参数化查询修复SQL注入漏洞
    $shequ = $DB->getRow("SELECT * FROM pre_shequ WHERE id=:id LIMIT 1", [':id' => $shequid]);
    if (!$shequ) exit('Shequ not exists');
    // 使用参数化查询修复SQL注入漏洞
    $order = $DB->getRow("SELECT * FROM pre_orders WHERE tradeno=:tradeno LIMIT 1", [':tradeno' => $tradeno]);
    if (!$order) exit('Order not exists');
    // 部分对接插件不支持异步回调（无 notify 方法），直接返回，避免致命错误
    $third_class = '\\plugins\\third_' . $shequ['type'];
    if (class_exists($third_class) && method_exists($third_class, 'notify')) {
        $list = third_call($shequ['type'], $shequ, 'notify', [$order]);
    } else {
        $list = false;
    }
    if ($list === false) echo 'No support';
} else {
    echo 'No Act';
}
