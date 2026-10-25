<?php
// 静态数据分页端点（风控/黑名单查询演示数据）
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$page  = max(1, intval(isset($_GET['page'])  ? $_GET['page']  : 1));
$limit = max(1, min(100, intval(isset($_GET['limit']) ? $_GET['limit'] : 10)));
$file = __DIR__ . '/complaints.json';
$all = json_decode(file_get_contents($file), true);
if (!is_array($all)) { echo '{"code":-1,"msg":"数据文件缺失"}'; exit; }
$items = $all['items'];
$sort = isset($_GET['sort_type']) ? $_GET['sort_type'] : 'latest_time';
usort($items, function ($a, $b) use ($sort) {
    if ($sort === 'complaint_rate')   return $b['complaint_rate'] <=> $a['complaint_rate'];
    if ($sort === 'complaint_orders') return $b['complaint_orders'] <=> $a['complaint_orders'];
    return strcmp($b['latest_complaint_time'], $a['latest_complaint_time']);
});
for ($i = 0; $i < count($items); $i++) { $items[$i]['rank'] = $i + 1; }
$total = count($items);
$items = array_slice($items, ($page - 1) * $limit, $limit);
echo json_encode(array(
    'code'  => 200,
    'msg'   => '获取成功',
    'total' => $total,
    'items' => $items,
), JSON_UNESCAPED_UNICODE);
