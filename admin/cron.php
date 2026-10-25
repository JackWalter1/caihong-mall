<?php
/*
 * 计划任务处理文件
 * 博客：zhonguo.ren
 * QQ群：915043052
 * 开发者：教主
 * 功能：处理各种自动任务，包括自动补单、数据清理等
 */

include("../includes/common.php");

// 检查是否启用了自动补单
$autoreorder = $DB->getColumn("SELECT v FROM pre_config WHERE k='autoreorder'");

// 获取自动补单配置
$autoreorder_config = [];
$config_keys = [
    'autoreorder_interval' => 5,
    'autoreorder_limit' => 50,
    'autoreorder_max_retries' => 3,
    'autoreorder_status' => '0,1,2',
    'autoreorder_after' => 10,
    'autoreorder_timeout' => 30
];

foreach($config_keys as $key => $default) {
    $value = $DB->getColumn("SELECT v FROM pre_config WHERE k='{$key}'");
    $autoreorder_config[$key] = $value !== false ? $value : $default;
}

// 检查上次执行时间
$last_reorder_time = $DB->getColumn("SELECT v FROM pre_config WHERE k='last_reorder_time'");
$current_time = time();

// 判断是否需要执行补单
$should_reorder = false;
if($autoreorder == 1) {
    if(!$last_reorder_time || ($current_time - strtotime($last_reorder_time) >= $autoreorder_config['autoreorder_interval'] * 60)) {
        $should_reorder = true;
    }
}

// 如果是手动触发或满足自动触发条件，则执行补单
if($should_reorder || $_GET['action'] == 'reorder') {
    try {
        // 检查pre_config表是否存在
        $config_check = $DB->query("SELECT 1 FROM pre_config LIMIT 1");
        if ($config_check !== false) {
            // 更新最后执行时间
            $DB->exec("REPLACE INTO pre_config SET k='last_reorder_time',v='{$date}'");
        }
        
        // 执行自动补单
        $result = autoReorder($autoreorder_config);
        
        // 输出结果
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'code' => 1,
            'msg' => "自动补单完成，处理订单{$result['processed']}个，成功{$result['success']}个，失败{$result['failed']}个"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    } catch (Exception $e) {
        // 输出错误信息
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'code' => 0,
            'msg' => '执行错误：' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * 自动补单核心函数
 */
function autoReorder($config) {
    global $DB, $date;
    
    $processed = 0;
    $success = 0;
    $failed = 0;
    
    try {
        // 检查必要的表是否存在
        $tables = ['shua_orders', 'shua_goods', 'shua_kms', 'shua_shequ'];
        foreach ($tables as $table) {
            $check = $DB->query("SELECT 1 FROM {$table} LIMIT 1");
            if ($check === false) {
                throw new Exception("表 {$table} 不存在");
            }
        }
        
        // 解析订单状态
        $status_array = explode(',', $config['autoreorder_status']);
        $status_condition = [];
        $include_djzt2 = false;
        
        foreach ($status_array as $status) {
            if ($status == 'djzt2') {
                $include_djzt2 = true;
            } else {
                $status_condition[] = $status;
            }
        }
        
        // 调试信息
        if ($_GET['action'] == 'reorder' && isset($_GET['debug'])) {
            echo "<pre>";
            echo "自动补单配置: " . print_r($config, true);
            echo "状态数组: " . print_r($status_array, true);
            echo "是否包含对接失败: " . ($include_djzt2 ? '是' : '否');
            echo "</pre>";
        }
        
        $status_condition_str = implode(',', $status_condition);
        
        // 构建查询条件
        $where_condition = '';
        if (!empty($status_condition_str)) {
            $where_condition = "status IN ({$status_condition_str})";
        }
        
        if ($include_djzt2) {
            if (!empty($where_condition)) {
                $where_condition .= " OR djzt = 2";
            } else {
                $where_condition = "djzt = 2";
            }
        } else if (empty($where_condition)) {
            // 如果没有选择任何状态，默认选择未处理和处理中
            $where_condition = "status IN (0,1)";
        }
        
        // 计算时间条件
        $after_time = date('Y-m-d H:i:s', time() - $config['autoreorder_after'] * 60);
        $timeout_time = date('Y-m-d H:i:s', time() - $config['autoreorder_timeout'] * 60);
        
        // 调试信息
        if ($_GET['action'] == 'reorder' && isset($_GET['debug'])) {
            echo "<pre>";
            echo "查询条件: " . $where_condition . "<br>";
            echo "时间条件 - 开始: " . $timeout_time . "<br>";
            echo "时间条件 - 结束: " . $after_time . "<br>";
            echo "</pre>";
        }
        
        // 构建查询语句
        if ($include_djzt2 && empty($status_condition_str)) {
            // 只勾选了对接失败选项，不限制时间范围
            $query = "SELECT * FROM shua_orders WHERE 
                djzt = 2 AND 
                reorder_times < '{$config['autoreorder_max_retries']}' 
                ORDER BY addtime ASC 
                LIMIT {$config['autoreorder_limit']}";
        } else {
            // 其他情况，使用原来的时间条件
            $query = "SELECT * FROM shua_orders WHERE 
                ({$where_condition}) AND 
                addtime >= '{$timeout_time}' AND 
                addtime <= '{$after_time}' AND 
                reorder_times < '{$config['autoreorder_max_retries']}' 
                ORDER BY addtime ASC 
                LIMIT {$config['autoreorder_limit']}";
        }
        
        // 调试信息
        if ($_GET['action'] == 'reorder' && isset($_GET['debug'])) {
            echo "<pre>";
            echo "SQL查询语句: " . $query . "<br>";
            echo "</pre>";
        }
        
        $orders = $DB->query($query);
        
        // 检查 $orders 是否为 false
        if ($orders === false) {
            throw new Exception('查询失败');
        }
        
        // 使用 fetchAll 方法获取所有结果
        $order_list = $orders->fetchAll();
        
        // 处理每个订单
        foreach ($order_list as $order) {
            $processed++;
            
            try {
                // 补单前更新订单状态为处理中
                $DB->update('shua_orders', [
                    'status' => 1,
                    'reorder_times' => $order['reorder_times'] + 1,
                    'last_reorder_time' => $date
                ], ['id' => $order['id']]);
                
                // 检查是否为对接状态失败的订单
                if($order['djzt'] == 2) {
                    // 使用 do_goods 函数重新提交失败的订单
                    $result = do_goods($order['id']);
                    
                    // 处理补单结果
                    if(strpos($result, '成功') !== false) {
                        $DB->update('pre_orders', [
                            'status' => 1,
                            'djzt' => 1,
                            'endtime' => $date,
                            'result' => $result
                        ], ['id' => $order['id']]);
                        $success++;
                    } else {
                        $DB->update('pre_orders', [
                            'result' => $result
                        ], ['id' => $order['id']]);
                        $failed++;
                    }
                } else {
                    // 获取商品信息 - 使用参数化查询修复SQL注入漏洞
                    $goods = $DB->getRow("SELECT * FROM shua_goods WHERE id=:gid", [':gid' => $order['gid']]);
                    if(!$goods) {
                        $failed++;
                        continue;
                    }
                    
                    // 获取商品对应的对接信息
                    $shequ = null;
                    if($goods['shequ_id']) {
                        // 使用参数化查询修复SQL注入漏洞
                        $shequ = $DB->getRow("SELECT * FROM shua_shequ WHERE id=:id", [':id' => $goods['shequ_id']]);
                    }
                    
                    // 根据商品类型执行对应的补单逻辑
                    $result = false;
                    if($shequ) {
                        // 第三方对接商品
                        $result = reorderThirdParty($order, $goods, $shequ);
                    } else {
                        // 本地商品
                        $result = reorderLocal($order, $goods);
                    }
                    
                    // 处理补单结果
                    if($result) {
                        $DB->update('pre_orders', [
                            'status' => 2,
                            'endtime' => $date,
                            'result' => isset($result['result']) ? $result['result'] : '补单成功'
                        ], ['id' => $order['id']]);
                        $success++;
                    } else {
                        $failed++;
                    }
                }
                
            } catch(Exception $e) {
                $failed++;
                // 记录错误信息
                $DB->update('pre_orders', [
                    'result' => '补单失败：' . $e->getMessage()
                ], ['id' => $order['id']]);
            }
        }
    } catch (Exception $e) {
        // 记录错误但继续执行
        $failed = 1;
    }
    
    return [
        'processed' => $processed,
        'success' => $success,
        'failed' => $failed
    ];
}

/**
 * 第三方对接商品补单
 */
function reorderThirdParty($order, $goods, $shequ) {
    global $DB;
    
    try {
        // 加载对应插件
        $plugin_file = SYS_ROOT . 'plugins/third_' . $shequ['type'] . '/index.php';
        if(!file_exists($plugin_file)) {
            throw new Exception("插件不存在 third_{$shequ['type']}");
        }
        
        // 创建插件实例
        $plugin = new \lib\Plugin('third_' . $shequ['type']);
        
        // 构建下单参数
        $params = [
            'order_no' => $order['orderno'],
            'goods_id' => $goods['shequ_goods_id'],
            'goods_name' => $goods['name'],
            'goods_price' => $goods['price'],
            'num' => $order['num'],
            'total' => $order['total'],
            'account' => $order['account'],
            'custom' => $order['custom'],
            'buyer' => $order['buyer']
        ];
        
        // 调用插件下单接口
        $result = $plugin->call('doGoods', $shequ, $params);
        
        if($result && $result['code'] == 1) {
            // 补单成功
            return $result;
        } else {
            throw new Exception($result['msg'] ?? '补单失败，未知错误');
        }
        
    } catch(Exception $e) {
        throw $e;
    }
}

/**
 * 本地商品补单
 */
function reorderLocal($order, $goods) {
    global $DB, $date;
    
    try {
        // 检查库存
        if($goods['stock'] < $order['num']) {
            throw new Exception('库存不足');
        }
        
        // 获取商品卡密 - 使用参数化查询修复SQL注入漏洞（PDO不支持LIMIT参数化，直接使用intval处理后的值）
        $num_limit = intval($order['num']);
        $codes = $DB->query("SELECT * FROM shua_kms WHERE gid=:gid AND used=0 LIMIT {$num_limit}", [':gid' => $goods['id']]);
        
        // 检查 $codes 是否为 false
        if ($codes === false) {
            throw new Exception('查询卡密失败');
        }
        
        // 使用 fetchAll 方法获取所有卡密
        $code_list = [];
        $code_rows = $codes->fetchAll();
        foreach ($code_rows as $code) {
            $code_list[] = $code['km'];
        }
        
        if(count($code_list) < $order['num']) {
            throw new Exception('卡密数量不足');
        }
        
        // 更新卡密状态
        $km_ids = [];
        foreach($code_list as $km) {
            // 使用参数化查询修复SQL注入漏洞
            $km_row = $DB->getRow("SELECT * FROM shua_kms WHERE km=:km", [':km' => $km]);
            $km_ids[] = $km_row['id'];
        }
        $DB->update('shua_kms', ['used' => 1, 'uid' => $order['uid'], 'oid' => $order['id'], 'usetime' => $date], "id IN (" . implode(',', $km_ids) . ")");
        
        // 更新库存
        $DB->update('shua_goods', ['stock' => $goods['stock'] - $order['num'], 'sales' => $goods['sales'] + $order['num']], ['id' => $goods['id']]);
        
        return [
            'code' => 1,
            'msg' => '补单成功',
            'result' => implode('\n', $code_list)
        ];
        
    } catch(Exception $e) {
        throw $e;
    }
}

// 如果没有执行补单操作，输出提示信息
if($autoreorder == 0) {
    echo "自动补单功能已关闭<br/>";
} else {
    echo "未达到补单时间间隔，跳过本次执行<br/>";
    echo "上次执行时间: {$last_reorder_time}<br/>";
    echo "设置的间隔: {$autoreorder_config['autoreorder_interval']}分钟<br/>";
}

// 执行其他计划任务（如果有）
doOtherTasks();

/**
 * 执行其他计划任务
 */
function doOtherTasks() {
    // 这里可以添加其他计划任务的逻辑
    // 例如：数据清理、统计更新等
}
?>