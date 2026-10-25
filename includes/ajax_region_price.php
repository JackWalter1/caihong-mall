<?php

@header('Content-Type: application/json; charset=UTF-8');

if (!defined('IN_CRONLITE')) {
    define('IN_CRONLITE', true);
}

define('SYSTEM_ROOT', dirname(__FILE__) . '/includes/');
define('ROOT', dirname(SYSTEM_ROOT) . '/');

require_once ROOT . 'config.php';
require_once SYSTEM_ROOT . 'common.php';
require_once SYSTEM_ROOT . 'autoloader.php';

Autoloader::register();

use lib\RegionPrice;

$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($act == 'calculate') {
    $tid = isset($_POST['tid']) ? intval($_POST['tid']) : 0;
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';

    if ($tid <= 0) {
        echo json_encode(['code' => -1, 'msg' => '商品ID不能为空']);
        exit;
    }

    if (empty($address)) {
        echo json_encode(['code' => -1, 'msg' => '收货地址不能为空']);
        exit;
    }

    $tool = $DB->getRow("SELECT * FROM pre_tools WHERE tid='{$tid}' LIMIT 1");
    if (!$tool) {
        echo json_encode(['code' => -1, 'msg' => '商品不存在']);
        exit;
    }

    global $userrow;
    $price_obj = new \lib\Price($userrow['zid'], $userrow);
    $price_obj->setToolInfo($tool['tid'], $tool);
    $original_price = $price_obj->getToolPrice($tool['tid']);

    $regionPrice = new RegionPrice();
    $result = $regionPrice->calculatePrice($original_price, $address, $tid, $tool['name']);

    echo json_encode([
        'code' => 0,
        'msg' => 'success',
        'data' => [
            'original_price' => $result['original_price'],
            'final_price' => $result['final_price'],
            'add_price_amount' => $result['add_price_amount'],
            'region_name' => $result['region_name'],
            'add_price_type' => $result['add_price_type'],
            'add_price_value' => $result['add_price_value'],
            'matched' => $result['matched']
        ]
    ]);
    exit;
} elseif ($act == 'get_rules') {
    $regionPrice = new RegionPrice();
    $rules = $regionPrice->getRules(1);

    echo json_encode([
        'code' => 0,
        'msg' => 'success',
        'data' => array_values($rules)
    ]);
    exit;
} else {
    echo json_encode(['code' => -1, 'msg' => '无效的操作']);
    exit;
}
