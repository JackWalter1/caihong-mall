<?php
/**
 * 彩虹云商城 授权验证核心（重构版）
 * - 本地硬放行：授权码 CH-4AFF-121C-E9EF-4525-8DF7-6643 绑定 caihong.wnttech.fun
 *   CH_AUTH_LOCAL_OVERRIDE = true 时跳过远程心跳，直接视为已授权
 * - 远程验证：24h 缓存 + 7 天网络容错；域名/授权码状态由授权站 status.php 判定
 * - 如需恢复联网验证：将下方 CH_AUTH_LOCAL_OVERRIDE 改为 false
 */

if (defined('SITE_AUTH_VERIFIED')) return;

define('CH_AUTH_LOCAL_OVERRIDE', true);

if (CH_AUTH_LOCAL_OVERRIDE) {
    define('SITE_AUTH_VERIFIED', true);
    $GLOBALS['auth_local_override'] = true;
    $GLOBALS['auth_status'] = 'local_valid';
    return;
}

// ===== 远程心跳验证（仅本地放行关闭时执行）=====
global $auth_config;

if (empty($auth_config) || empty($auth_config['code'])) {
    define('SITE_AUTH_VERIFIED', false);
    $GLOBALS['auth_no_code'] = true;
    $GLOBALS['auth_expired'] = true;
    $GLOBALS['auth_status'] = 'no_code';
    $GLOBALS['auth_reason'] = '未配置授权码';
    return;
}

$auth_code  = $auth_config['code'];
$auth_token = isset($auth_config['token']) ? $auth_config['token'] : '';
$auth_api   = !empty($auth_config['api_url']) ? $auth_config['api_url'] : (defined('AUTH_API_URL') ? AUTH_API_URL : 'https://auth.wetcs.cn');

$current_domain = preg_replace('/^www\./', '', strtolower(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));

// 本地缓存（目录不存在时自动创建）
$cache_dir  = SYSTEM_ROOT . 'cache';
$cache_file = $cache_dir . '/auth_status.json';
if (!is_dir($cache_dir)) @mkdir($cache_dir, 0755, true);

$cached = null;
if (is_file($cache_file)) {
    $cached = json_decode((string)@file_get_contents($cache_file), true);
}

// 24h 内有效缓存直接放行
if ($cached && !empty($cached['valid']) && (time() - (int)$cached['check_time'] < 86400)) {
    define('SITE_AUTH_VERIFIED', true);
    $GLOBALS['auth_status'] = 'cached_valid';
    return;
}

// 发起心跳
$params = array(
    'code'      => $auth_code,
    'domain'    => $current_domain,
    'token'     => $auth_token,
    'timestamp' => time(),
);

$ch = curl_init($auth_api . '/api/status.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$resp = curl_exec($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode((string)$resp, true);
$is_valid = ($httpCode === 200 && is_array($data) && isset($data['data']['valid']) && $data['data']['valid']);

// 写入缓存（有效/无效都存，无效也存防止每次都打爆授权站）
@file_put_contents($cache_file, json_encode(array(
    'valid'      => $is_valid,
    'check_time' => time(),
    'reason'     => isset($data['data']['reason']) ? $data['data']['reason'] : 'curl_resp_' . substr((string)$resp, 0, 200),
    'http_code'  => $httpCode,
)));

// 心跳通过
if ($is_valid) {
    define('SITE_AUTH_VERIFIED', true);
    $GLOBALS['auth_status'] = 'online_ok';
    return;
}

// 心跳失败：7 天内授权站宕机（网络故障而非业务拒绝）→ 用老缓存放行
$is_network_fail = ($httpCode !== 200 || $resp === false || !is_array($data));
if ($is_network_fail && $cached && !empty($cached['valid']) && (time() - (int)$cached['check_time'] < 604800)) {
    define('SITE_AUTH_VERIFIED', true);
    $GLOBALS['auth_warning'] = true;
    $GLOBALS['auth_status'] = 'network_fallback';
    return;
}

// 真失败：业务拒绝（过期/禁用/域名不匹配/授权码不存在），或网络失败且无有效缓存兜底
define('SITE_AUTH_VERIFIED', false);
$GLOBALS['auth_expired'] = true;
$GLOBALS['auth_status'] = isset($data['data']['reason']) ? $data['data']['reason'] : ($is_network_fail ? 'network_failed' : 'auth_failed');
$GLOBALS['auth_reason'] = $GLOBALS['auth_status'];
