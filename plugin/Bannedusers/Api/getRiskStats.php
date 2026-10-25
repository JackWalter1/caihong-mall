<?php
// 风控统计数据端点：优先读实时同步缓存 riskstats.json（由 sync_risk.php 维护）
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$f  = __DIR__ . '/riskstats.json';
$st = is_file($f) ? json_decode(@file_get_contents($f), true) : null;

if (!$st || !isset($st['data'])) {
    // 首次同步前的兜底（页面不至于空白）
    $st = array(
        'code' => 200, 'msg' => '获取成功', 'synced_at' => 0,
        'data' => array(
            'total' => array('value' => 0, 'growth' => 0, 'trend' => 'up'),
            'month' => array('value' => 0, 'growth' => 0, 'trend' => 'up'),
            'week'  => array('value' => 0, 'growth' => 0, 'trend' => 'up'),
            'today' => array('value' => 0, 'growth' => 0, 'trend' => 'up'),
        ),
    );
}

// 缓存超过 10 分钟未刷新：后台兜底触发一次同步（cron 之外的第二重保险；exec 被禁用时静默跳过）
if (time() - intval($st['synced_at']) > 600) {
    @exec(sprintf('nohup %s %s > /dev/null 2>&1 &', PHP_BINARY, escapeshellarg(__DIR__ . '/sync_risk.php')));
}

echo json_encode(array(
    'code'      => 200,
    'msg'       => '获取成功',
    'data'      => $st['data'],
    'synced_at' => intval($st['synced_at']),
), JSON_UNESCAPED_UNICODE);
