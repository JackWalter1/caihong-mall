<?php
/**
 * 风控名单实时同步器（sync_risk.php）
 * 数据源：https://hsfaka.cn/plugin/Bannedusers/Api/
 * 调度：crontab 每 5 分钟一次；前台 getRiskStats.php 检测缓存超 10 分钟时兜底触发
 * 策略：抓取失败不动旧数据；临时文件 + rename 原子写入；flock 防并发；日志滚动
 */
error_reporting(0);

const SRC = 'https://hsfaka.cn/plugin/Bannedusers/Api/';
const UA  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36';

function http_post($url, $timeout = 10) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => '{}',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_HTTPHEADER     => array('Content-Type: application/json', 'User-Agent: ' . UA),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ));
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($body === false || $code != 200) return null;
    return json_decode($body, true);
}

function local_avatar($url) {
    if (!is_string($url) || $url === '') return '/static/images/avatar.png';
    // 源站默认头像 -> 本地文件，减少外链依赖
    if (strpos($url, 'hsfaka.cn/static/images/avatar.png') !== false) return '/static/images/avatar.png';
    // 商家自定义头像（源站公开图床）保留原链，可直接加载
    return $url;
}

function atomic_write($file, $arr) {
    $tmp = $file . '.tmp' . getmypid();
    $ok = file_put_contents($tmp, json_encode($arr, JSON_UNESCAPED_UNICODE));
    if ($ok === false) return false;
    return rename($tmp, $file);
}

function log_line($file, $msg) {
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    clearstatcache(true, $file);
    if (is_file($file) && filesize($file) > 2097152) {
        $all = file($file);
        file_put_contents($file, implode('', array_slice($all, -500)));
    }
}

$dir = __DIR__;
$log = $dir . '/sync.log';

$lock = fopen($dir . '/sync.lock', 'w');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    exit(0); // 已有同步进程在跑，安静退出
}

// 1) 名单全量（100 条/页循环翻页抓全）
$items = array();
$total = 0;
$page  = 1;
while ($page <= 30) {
    $resp = http_post(SRC . 'getRiskUsers?page=' . $page . '&limit=100&timeRange=all');
    if (!$resp || !isset($resp['items'])) {
        log_line($log, 'FAIL users page ' . $page . '（网络或接口异常，保留旧数据）');
        exit(1);
    }
    $total = intval($resp['total']);
    foreach ($resp['items'] as $it) {
        if (isset($it['avatar'])) $it['avatar'] = local_avatar($it['avatar']);
        $items[] = $it;
    }
    if ($page * 100 >= $total) break;
    $page++;
}
if (count($items) === 0) {
    log_line($log, 'FAIL empty items（保留旧数据）');
    exit(1);
}

// 2) 统计数字
$stats = http_post(SRC . 'getRiskStats');
if (!$stats || !isset($stats['data'])) {
    log_line($log, 'FAIL stats（保留旧数据）');
    exit(1);
}

// 3) 原子落盘：先写名单、后写统计（前台读到新统计时名单必已就绪）
$old = is_file($dir . '/riskusers.json') ? json_decode(@file_get_contents($dir . '/riskusers.json'), true) : null;
$old_total = $old ? intval($old['total']) : 0;

atomic_write($dir . '/riskusers.json', array('code' => 200, 'msg' => '获取成功', 'total' => $total, 'items' => $items));
atomic_write($dir . '/riskstats.json', array('code' => 200, 'msg' => '获取成功', 'data' => $stats['data'], 'synced_at' => time()));

log_line($log, 'OK items=' . count($items) . ' total=' . $total . ($total != $old_total ? '（变更 ' . $old_total . ' -> ' . $total . '）' : ''));

flock($lock, LOCK_UN);
fclose($lock);
