<?php
/*
 * 自动同步API处理文件
 * 博客：zhonguo.ren
 * QQ群：915043052
 * 开发者：教主
 * 功能：处理商品自动同步的核心逻辑，包括分类同步、商品同步、数据更新等
 */
// 设置脚本执行时间和内存限制 - 教主优化，确保同步任务能完成
ini_set('max_execution_time', 0); // 设置执行时间为无限
ini_set('memory_limit', '1024M'); // 设置内存限制为1G
ini_set('default_socket_timeout', 60); // 设置socket超时为1分钟
ignore_user_abort(true); // 忽略用户中止请求，后台继续执行

header("Content-Type: text/html;charset=utf-8");
set_time_limit(0); // 再次设置时间限制为无限

// 关闭输出缓冲，让输出立即显示
while(ob_get_level() > 0) {
    ob_end_flush();
}
flush();
include("../includes/common.php");

// 检查监控密钥 - 修复空密钥绕过漏洞
$monitor_key = $DB->getColumn("SELECT v FROM pre_config WHERE k='monitor_key'");
if(empty($monitor_key) || !isset($_GET['key']) || $_GET['key'] !== $monitor_key) {
    exit('Access Denied');
}

// 设置同步任务开始时间
$DB->exec("REPLACE INTO pre_config SET k='last_sync_time',v=:date", [':date' => $date]);
echo "同步任务开始时间：{$date}<br/>";

// 模拟任务管理器功能 - 教主优化，提高性能
class SimpleTask {
    private $name;
    private $outputBuffer = [];
    private $bufferSize = 50; // 每50条输出一次，减少I/O操作
    private $lastFlushTime = 0;
    private $flushInterval = 5; // 每5秒至少刷新一次
    
    public function __construct($name) {
        $this->name = $name;
        $this->lastFlushTime = time();
        // 启用输出缓冲
        if(!ob_get_level()) {
            ob_start();
        }
    }
    
    public function updateProgress($message) {
        // 记录关键信息，包括位置更新
        if(strpos($message, '处理第') !== false || strpos($message, '开始') !== false || strpos($message, '完成') !== false || strpos($message, '同步位置') !== false || strpos($message, '获取到') !== false) {
            $this->outputBuffer[] = $message . "<br/>";
            $this->checkFlush();
        }
    }
    
    public function checkTimeout() {
        // 简单实现，不做超时检查
    }
    
    public function cleanup() {
        // 清理时刷新所有缓冲区
        $this->flushOutput();
        // 关闭输出缓冲
        if(ob_get_level()) {
            ob_end_flush();
        }
    }
    
    private function checkFlush() {
        // 检查是否需要刷新缓冲区
        $currentTime = time();
        if(count($this->outputBuffer) >= $this->bufferSize || ($currentTime - $this->lastFlushTime) >= $this->flushInterval) {
            $this->flushOutput();
            $this->lastFlushTime = $currentTime;
        }
    }
    
    private function flushOutput() {
        if(!empty($this->outputBuffer)) {
            echo implode('', $this->outputBuffer);
            $this->outputBuffer = [];
            // 刷新输出缓冲区
            ob_flush();
            flush();
        }
    }
}

// 创建简单任务实例
$task = new SimpleTask('auto_sync');

try {
    // 支持按站点ID单独同步，减轻单次任务压力
    $shequ_id = isset($_GET['shequ_id']) ? intval($_GET['shequ_id']) : 0;
    
    // 支持按同步类型分离执行，分散压力
    // all: 全部 | class: 分类 | price: 价格+成本 | name: 名称 | desc: 描述 | image: 图片 | goods: 新增商品 | delete: 下架处理
    $sync_type = isset($_GET['sync_type']) ? $_GET['sync_type'] : 'all';
    $sync_type_names = [
        'all' => '全部同步',
        'class' => '分类同步',
        'price' => '价格同步',
        'name' => '名称同步',
        'desc' => '描述同步',
        'image' => '图片同步',
        'goods' => '新增商品',
        'delete' => '下架处理',
    ];
    $sync_type_label = isset($sync_type_names[$sync_type]) ? $sync_type_names[$sync_type] : $sync_type;
    
    if ($shequ_id > 0) {
        // 只同步指定站点
        $configs = $DB->query("SELECT * FROM pre_sync_config WHERE status=1 AND shequ_id=?", [$shequ_id]);
        echo "按站点ID同步模式，站点ID: {$shequ_id}，同步类型: {$sync_type_label}<br/>";
    } else {
        // 同步所有启用的站点（兼容旧模式）
        $configs = $DB->query("SELECT * FROM pre_sync_config WHERE status=1");
        echo "全部站点同步模式，同步类型: {$sync_type_label}<br/>";
    }
    
    $total_synced = 0;
    $total_updated = 0;
    $total_deleted = 0;
    $start_time = time();
    
    while($row = $configs->fetch()) {
        $task->updateProgress("开始处理站点配置: ID=" . $row['shequ_id']);
        
        // 获取对接站点信息
        $shequ = $DB->getRow("SELECT * FROM pre_shequ WHERE id=:id", [':id' => $row['shequ_id']]);
        if(!$shequ) {
            $task->updateProgress("跳过: 未找到站点信息 ID=" . $row['shequ_id']);
            continue;
        }
        
        // 检查插件是否存在
        $plugin_file = PLUGIN_ROOT . 'third_' . $shequ['type'] . '.php';
        if(!file_exists($plugin_file)) {
            $task->updateProgress("跳过: 插件文件不存在 {$plugin_file}");
            continue;
        }
        
        // 同步分类（仅 all 或 class 模式执行）
        if (($sync_type == 'all' || $sync_type == 'class') && ($row['sync_class'] || $row['add_class'])) {
            $task->updateProgress("开始同步分类信息");
            syncCategories($shequ, $row, $task);
        }
        
        // 同步商品（根据 sync_type 决定同步内容）
        if ($sync_type != 'class') {
            $synced_count = syncProducts($shequ, $row, $task, $sync_type);
            $total_synced += $synced_count['synced'];
            $total_updated += $synced_count['updated'];
            $total_deleted += $synced_count['deleted'];
        }
        
        $task->updateProgress("站点同步完成: 新增{$synced_count['synced']}, 更新{$synced_count['updated']}, 删除{$synced_count['deleted']}");
    }
    
    $task->updateProgress("同步任务完成: 总计新增{$total_synced}, 更新{$total_updated}, 删除{$total_deleted}");
    echo "同步任务结束时间: " . date('Y-m-d H:i:s') . "<br/>";
    
} catch(Exception $e) {
    $task->updateProgress("同步任务异常: " . $e->getMessage());
} finally {
    // 清理任务
    $task->cleanup();
    
    // 更新同步结束时间
    $DB->exec("REPLACE INTO pre_config SET k='last_sync_end_time',v=:date", [':date' => date('Y-m-d H:i:s')]);
}

/**
 * 同步分类信息
 */
function syncCategories($shequ, $config, $task) {
    global $DB, $date, $conf;
    
    try {
        // 使用 third_call 函数调用插件方法
        $categories = third_call($shequ['type'], $shequ, 'class_list');
        
        // 检查分类列表是否为数组
        if(!is_array($categories)) {
            $task->updateProgress("获取分类列表失败: {$categories}");
            return;
        }
        
        if(empty($categories)) {
            $task->updateProgress("未获取到分类数据");
            return;
        }
        
        $added = 0;
        $updated = 0;
        
        foreach($categories as $cat) {
            // 检查分类是否已存在 - 教主修改，博客地址：6v6.ren Q群：941535592
            $existing_cat = $DB->getRow("SELECT * FROM pre_class WHERE `name`=? LIMIT 1", [$cat['name']]);
            
            if($existing_cat) {
                // 更新分类 - 教主修改，博客地址：6v6.ren Q群：941535592
                if($config['sync_sort'] && $existing_cat['sort'] != ($cat['sort'] ?? 0)) {
                    $sort = intval($cat['sort'] ?? 0);
                    $DB->exec("UPDATE `pre_class` SET `sort`=:sort WHERE `cid`=:cid", [':sort' => $sort, ':cid' => $existing_cat['cid']]);
                    $updated++;
                }
            } elseif($config['add_class']) {
                // 新增分类 - 教主修改，博客地址：6v6.ren Q群：941535592
                $sort = intval($cat['sort'] ?? 0);
                $DB->exec("INSERT INTO `pre_class` (`sort`,`name`,`pid`,`active`) VALUES (:sort,:name,'0','1')", [':sort' => $sort, ':name' => $cat['name']]);
                $added++;
            }
        }
        
        $task->updateProgress("分类同步完成: 新增{$added}, 更新{$updated}");
        
    } catch(Exception $e) {
        $task->updateProgress("分类同步失败: " . $e->getMessage());
    }
}

/**
 * 获取同步位置记录
 */
function getSyncPosition($shequ_id) {
    global $DB;
    $pos = $DB->getColumn("SELECT v FROM pre_config WHERE k='sync_pos_".$shequ_id."'");
    return $pos ? intval($pos) : 0;
}

/**
 * 设置同步位置记录
 */
function setSyncPosition($shequ_id, $position) {
    global $DB;
    $DB->exec("REPLACE INTO pre_config SET k=:k, v=:v", [':k' => 'sync_pos_'.$shequ_id, ':v' => strval($position)]);
}

/**
 * 重置同步位置记录
 */
function resetSyncPosition($shequ_id) {
    global $DB;
    $DB->exec("REPLACE INTO pre_config SET k=:k, v='0'", [':k' => 'sync_pos_'.$shequ_id]);
}

/**
 * 同步商品信息（支持分页同步）
 */
function syncProducts($shequ, $config, $task, $sync_type = 'all') {
    global $DB, $date, $conf;
    
    $synced = 0;
    $updated = 0;
    $deleted = 0;
    
    try {
        // 获取配置的同步数量，默认为50
        $sync_limit = intval($config['sync_limit'] ?? 50);
        if($sync_limit <= 0) $sync_limit = 50;
        
        // 获取当前同步位置
        $current_pos = getSyncPosition($shequ['id']);
        
        // 使用 third_call 函数调用插件方法获取商品列表
        try {
            $products = third_call($shequ['type'], $shequ, 'goods_list');
        } catch(Exception $e) {
            $task->updateProgress("获取商品列表异常: " . $e->getMessage());
            return ['synced' => 0, 'updated' => 0, 'deleted' => 0];
        }
        
        // 检查商品列表是否为数组
        if(!is_array($products)) {
            $task->updateProgress("获取商品列表失败");
            return ['synced' => 0, 'updated' => 0, 'deleted' => 0];
        }
        
        if(empty($products)) {
            $task->updateProgress("未获取到商品数据");
            return ['synced' => 0, 'updated' => 0, 'deleted' => 0];
        }
        
        $total_products = count($products);
        
        // 计算本次需要处理的商品范围
        $start_pos = $current_pos;
        $end_pos = min($current_pos + $sync_limit, $total_products);
        
        // 截取本次需要处理的商品
        $current_batch_products = array_slice($products, $start_pos, $sync_limit);
        
        // 如果已经处理完所有商品，标记完成并重置位置
        if($start_pos >= $total_products) {
            $current_batch_products = $products;
            $start_pos = 0;
            $end_pos = $total_products;
            resetSyncPosition($shequ['id']);
        }
        
        // 输出同步进度日志
        $task->updateProgress("获取到 {$total_products} 个商品，本次处理范围: {$start_pos} - {$end_pos}");
        
        // 预加载分类信息，减少重复查询
        $categories = third_call($shequ['type'], $shequ, 'class_list');
        $category_map = [];
        if(is_array($categories)) {
            foreach($categories as $cat) {
                $category_map[$cat['cid']] = $cat['name'];
            }
        }
        
        // 预加载加价模板，减少重复查询
        $markup_template = null;
        if($config['markup_template']) {
            $markup_template = $DB->getRow("SELECT * FROM pre_price WHERE id=?", [$config['markup_template']]);
        }
        
        // 预加载默认分类，减少重复查询
        $default_class = $DB->getRow("SELECT * FROM pre_class WHERE `name`=? LIMIT 1", ['默认分类']);
        $default_cid = $default_class ? $default_class['cid'] : 0;
        
        // 收集本次处理的商品ID（用于后续删除检查）
        $current_batch_ids = [];
        foreach($current_batch_products as $product) {
            $current_batch_ids[] = $product['id'];
        }
        
        // 分批处理商品，每批20个（内部处理批次，减少内存占用）
        $internal_batch_size = 20;
        $batches = array_chunk($current_batch_products, $internal_batch_size);
        $total_batches = count($batches);
        
        // 收集所有对接站商品ID（用于删除检查）
        $all_shequ_product_ids = [];
        foreach($products as $product) {
            $all_shequ_product_ids[] = $product['id'];
        }
        
        foreach($batches as $batch_index => $batch_products) {
            $batch_number = $batch_index + 1;
            $task->updateProgress("处理第 {$batch_number}/{$total_batches} 批商品，共 " . count($batch_products) . " 个");
            
            // 重置批次相关变量
            $shequ_product_ids = [];
            $new_products = []; // 用于批量插入的商品数据
            $product_ids = []; // 用于批量查询现有商品
            $update_products = []; // 用于批量更新的商品数据
            $update_site_prices = []; // 用于批量更新的站点价格数据
            
            // 收集需要获取详情的商品ID - 教主优化，实现批量API调用
            $need_detail_ids = [];
            $need_price_ids = [];
            $need_desc_ids = [];
            
            foreach($batch_products as $product) {
                $shequ_product_ids[] = $product['id'];
                $product_ids[] = $product['id'];
                $all_shequ_product_ids[] = $product['id'];
                
                // 检查是否需要获取商品详情
                if(empty($product['cid']) || !isset($category_map[$product['cid']])) {
                    $need_detail_ids[] = $product['id'];
                }
                
                // 检查是否需要同步价格
                if($config['sync_price']) {
                    $need_price_ids[] = $product['id'];
                }
                
                // 检查是否需要同步描述
                if($config['sync_desc']) {
                    $need_desc_ids[] = $product['id'];
                }
            }
            
            // 合并需要调用goods_info的商品ID，去重
            $all_need_detail_ids = array_unique(array_merge($need_detail_ids, $need_price_ids, $need_desc_ids));
            $product_details_cache = [];
            
            // 只有当sync_details开启时才获取商品详情
            if(isset($config['sync_details']) && $config['sync_details'] == 1 && !empty($all_need_detail_ids)) {
                $task->updateProgress("开始批量获取商品详情，共 " . count($all_need_detail_ids) . " 个商品");
                
                $api_batch_size = 2;
                $api_batches = array_chunk($all_need_detail_ids, $api_batch_size);
                $total_api_batches = count($api_batches);
                $success_count = 0;
                $fail_count = 0;
                
                foreach($api_batches as $api_batch_index => $api_batch_ids) {
                    $task->updateProgress("处理第 " . ($api_batch_index + 1) . "/" . $total_api_batches . " 批API请求");
                    
                    foreach($api_batch_ids as $product_id) {
                        try {
                            set_time_limit(15);
                            
                            $detail = third_call($shequ['type'], $shequ, 'goods_info', [$product_id]);
                            
                            if(is_array($detail)) {
                                $product_details_cache[$product_id] = $detail;
                                $success_count++;
                            } else {
                                $fail_count++;
                            }
                        } catch(Exception $e) {
                            $fail_count++;
                            continue;
                        }
                    }
                    
                    usleep(100000);
                }
                
                $task->updateProgress("获取商品详情完成！成功: " . $success_count . " 个，失败: " . $fail_count . " 个");
            }
            
            // 批量查询现有商品 - 教主优化，提高性能
            $existing_products = [];
            if(!empty($product_ids)) {
                $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
                $query = "SELECT * FROM pre_tools WHERE is_curl=2 AND shequ=? AND goods_id IN ({$placeholders})";
                $params = array_merge([$shequ['id']], $product_ids);
                $stmt = $DB->query($query, $params);
                while($row = $stmt->fetch()) {
                    $existing_products[$row['goods_id']] = $row;
                }
            }
            
            foreach($batch_products as $product) {
                // 检查任务超时
                $task->checkTimeout();
                
                // 检查商品是否已存在 - 使用批量查询的结果
                $existing_product = isset($existing_products[$product['id']]) ? $existing_products[$product['id']] : null;
                
                // 新增商品逻辑 - 教主修改，博客地址：6v6.ren Q群：941535592
                if(!$existing_product) {
                    // 只在 all 或 goods 模式下新增商品，其他模式跳过
                    if ($sync_type != 'all' && $sync_type != 'goods') {
                        continue;
                    }
                    // 检查是否允许新增商品
                    if($config['add_goods']) {
                        $task->updateProgress("开始处理新增商品: {$product['name']} (ID: {$product['id']})");
                        
                        // 构建商品详情数据
                        $product_detail = [
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'shopimg' => $product['shopimg'],
                            'close' => $product['close'],
                            'stock' => $product['stock'],
                            'class_name' => '',
                            'category' => ''
                        ];
                        
                        // 从商品列表中获取分类名称
                        if(isset($product['cid']) && !empty($product['cid']) && isset($category_map[$product['cid']])) {
                            $product_detail['class_name'] = $category_map[$product['cid']];
                            $product_detail['category'] = $category_map[$product['cid']];
                        }
                        
                        // 从缓存中获取商品详情 - 教主优化，使用缓存数据，没有则使用列表数据
                        $cache_key = $product['id'];
                        if(isset($product_details_cache[$cache_key])) {
                            $cached_detail = $product_details_cache[$cache_key];
                            $product_detail = array_merge($product_detail, $cached_detail);
                        } else {
                            // 使用商品列表中的数据
                            $product_detail = array_merge($product_detail, $product);
                        }
                        
                        // 查找对应的本地分类
                        $cid = 0;
                        $class_name = $product_detail['class_name'] ?? $product_detail['category'] ?? '';
                        
                        if(!empty($class_name)) {
                            $class_info = $DB->getRow("SELECT * FROM pre_class WHERE `name`=? LIMIT 1", [$class_name]);
                            if($class_info) {
                                $cid = $class_info['cid'];
                            } elseif($config['add_class']) {
                                // 如果允许新增分类，则创建新分类
                                $sort = 0;
                                $DB->exec("INSERT INTO `pre_class` (`sort`,`name`,`pid`,`active`) VALUES (:sort,:name,'0','1')", [':sort' => $sort, ':name' => $class_name]);
                                $cid = $DB->lastInsertId();
                            }
                        }
                        
                        // 如果没有找到分类，使用默认分类
                        if($cid == 0) {
                            if($default_cid) {
                                $cid = $default_cid;
                            } elseif($config['add_class']) {
                                // 创建默认分类
                                $sort = 0;
                                $default_class_name = '默认分类';
                                $DB->exec("INSERT INTO `pre_class` (`sort`,`name`,`pid`,`active`) VALUES (:sort,:name,'0','1')", [':sort' => $sort, ':name' => $default_class_name]);
                                $cid = $DB->lastInsertId();
                                $default_cid = $cid; // 更新默认分类ID
                            }
                        }
                        
                        // 计算价格（应用加价模板）
                        $cost_price = $product_detail['price'] ?? $product['price'] ?? 0;
                        // 应用加价模板到成本价
                        if($markup_template) {
                            $cost_price = calculateMarkupPrice($cost_price, $markup_template);
                        }
                        // 获取默认下单数量 - 使用商品自身的默认数量，支持不同字段名
                        $default_num = intval($product_detail['value'] ?? $product_detail['unitnum'] ?? $product_detail['limit_min'] ?? $product_detail['buy_min_limit'] ?? $product_detail['default_num'] ?? $product_detail['default_quantity'] ?? $product_detail['buy_num'] ?? $product_detail['min_buy'] ?? $product_detail['buy_min'] ?? $product_detail['quantity'] ?? $product_detail['default_qty'] ?? $product_detail['qty'] ?? 1);
                        // 确保默认下单数量至少为1
                        if($default_num < 1) {
                            $default_num = 1;
                        }
                        // 对于价格非常小的商品，使用最小下单数量作为默认数量
                        if($default_num == 1 && floatval($product_detail['price'] ?? 0) < 0.01) {
                            $default_num = intval($product_detail['min'] ?? $product_detail['limit_min'] ?? $product_detail['buy_min_limit'] ?? $product_detail['min_buy'] ?? $product_detail['buy_min'] ?? 100);
                            if($default_num < 1) {
                                $default_num = 100;
                            }
                        }
                        // 确保默认下单数量至少为最小下单数量
                        $min_num = intval($product_detail['min'] ?? $product_detail['limit_min'] ?? $product_detail['buy_min_limit'] ?? $product_detail['min_buy'] ?? $product_detail['buy_min'] ?? 1);
                        if($default_num < $min_num) {
                            $default_num = $min_num;
                        }
                        // 计算后的价钱 = 成本价 × 默认下单数量
                        $price = $cost_price * $default_num;
                        
                        // 价格精度处理：当计算后的价格小于0.01时，调整下单数量
                        if($price < 0.01) {
                            // 计算需要的调整倍数
                            $adjustment_factor = ceil(0.01 / $price);
                            // 确保调整倍数是10的倍数
                            $adjustment_factor = ceil($adjustment_factor / 10) * 10;
                            // 调整最少下单数量和默认下单数量
                            $min_num = $min_num * $adjustment_factor;
                            $default_num = $default_num * $adjustment_factor;
                            // 重新计算价格
                            $price = $cost_price * $default_num;
                        }
                        
                        // 处理图片
                        $shopimg = '';
                        if(isset($product_detail['shopimg']) && !empty($product_detail['shopimg'])) {
                            $shopimg = $product_detail['shopimg'];
                        } elseif(isset($product_detail['image']) && !empty($product_detail['image'])) {
                            $shopimg = $product_detail['image'];
                        }
                        
                        // 构建新商品数据
                        $new_product_data = [
                            'sort' => 0,
                            'cid' => $cid,
                            'name' => $product_detail['name'] ?? $product['name'] ?? '未命名商品',
                            'price' => floatval($price),
                            'cost' => floatval($cost_price),
                            'cost2' => 0,
                            'prid' => intval($config['markup_template'] ?? 0),
                            'prices' => '',
                            'input' => $product_detail['input'] ?? '',
                            'inputs' => $product_detail['inputs'] ?? '',
                            'desc' => $product_detail['desc'] ?? $product_detail['content'] ?? $product_detail['info'] ?? '',
                            'alert' => $product_detail['alert'] ?? '',
                            'shopimg' => $shopimg,
                            'value' => $default_num,
                            'is_curl' => 2,
                            'curl' => '',
                            'shequ' => intval($shequ['id']),
                            'goods_id' => strval($product['id']),
                            'goods_type' => 0,
                            'goods_param' => isset($product_detail['goods_param']) ? $product_detail['goods_param'] : (isset($product_detail['id']) ? $product_detail['id'] : ''),
                            'repeat' => intval($product_detail['repeat'] ?? 0),
                            'multi' => intval($product_detail['multi'] ?? 0),
                            'min' => 1,
                        'max' => intval($product_detail['max'] ?? $product_detail['limit_max'] ?? $product_detail['buy_max_limit'] ?? $product_detail['max_buy'] ?? $product_detail['buy_max'] ?? 0),
                            'validate' => $product_detail['validate'] ?? '',
                            'valiserv' => $product_detail['valiserv'] ?? '',
                            'close' => intval($product['close'] ?? 0),
                            'stock' => intval($product['stock'] ?? 9999),
                            'active' => 1,
                            'addtime' => $date,
                            'uptime' => $date
                        ];
                        
                        // 添加到批量插入数组
                        $new_products[] = $new_product_data;
                    } else {
                        $task->updateProgress("不允许新增商品，跳过: {$product['name']}");
                    }
                    continue;
                }
                
                // 构建商品数据（已有商品更新逻辑）
                // 如果 sync_type 是 delete/goods/class 模式，跳过已有商品更新
                if ($sync_type == 'delete' || $sync_type == 'class' || $sync_type == 'goods') {
                    continue;
                }
                
                $cost_price = $product['price'];
                
                // 从缓存中获取商品详情 - 教主优化，使用缓存数据，没有则使用列表数据
                $cache_key = $product['id'];
                $cached_detail = isset($product_details_cache[$cache_key]) ? $product_details_cache[$cache_key] : $product;
                
                // 如果需要同步价格，使用缓存的价格信息或列表价格
                if (($sync_type == 'all' || $sync_type == 'price') && $config['sync_price']) {
                    $cost_price = $cached_detail['price'] ?? $product['price'];
                }
                
                // 处理加价模板
                if($markup_template) {
                    $cost_price = calculateMarkupPrice($cost_price, $markup_template);
                }
                
                // 获取默认下单数量 - 使用商品自身的默认数量，支持不同字段名
                $default_num = intval($cached_detail['value'] ?? $cached_detail['unitnum'] ?? $cached_detail['limit_min'] ?? $cached_detail['buy_min_limit'] ?? $cached_detail['default_num'] ?? $cached_detail['default_quantity'] ?? $cached_detail['buy_num'] ?? $cached_detail['min_buy'] ?? $cached_detail['quantity'] ?? $cached_detail['default_qty'] ?? $cached_detail['qty'] ?? 1);
                // 确保默认下单数量至少为1
                if($default_num < 1) {
                    $default_num = 1;
                }
                // 对于价格非常小的商品，使用最小下单数量作为默认数量
                if($default_num == 1 && floatval($cached_detail['price'] ?? 0) < 0.01) {
                    $default_num = intval($cached_detail['min'] ?? $cached_detail['limit_min'] ?? $cached_detail['buy_min_limit'] ?? $cached_detail['min_buy'] ?? $cached_detail['buy_min'] ?? 100);
                    if($default_num < 1) {
                        $default_num = 100;
                    }
                }
                // 确保默认下单数量至少为最小下单数量
                $min_num = intval($cached_detail['min'] ?? $cached_detail['limit_min'] ?? $cached_detail['buy_min_limit'] ?? $cached_detail['min_buy'] ?? $cached_detail['buy_min'] ?? 1);
                if($default_num < $min_num) {
                    $default_num = $min_num;
                }
                // 计算后的价钱 = 成本价 × 默认下单数量
                $price = $cost_price * $default_num;
                
                // 价格精度处理：当计算后的价格小于0.01时，调整下单数量
                if($price < 0.01) {
                    // 计算需要的调整倍数
                    $adjustment_factor = ceil(0.01 / $price);
                    // 确保调整倍数是10的倍数
                    $adjustment_factor = ceil($adjustment_factor / 10) * 10;
                    // 调整最少下单数量和默认下单数量
                    $min_num = $min_num * $adjustment_factor;
                    $default_num = $default_num * $adjustment_factor;
                    // 重新计算价格
                    $price = $cost_price * $default_num;
                }
                
                // 检查是否有自定义价格记录
                $has_custom_price = $DB->getColumn("SELECT COUNT(*) FROM pre_site_price WHERE tid=?", [$existing_product['tid']]);
                
                // 构建更新数据
                $update_data = [
                    'tid' => $existing_product['tid'],
                    'close' => $product['close'],
                    'stock' => $product['stock'] ?? 9999,
                    'uptime' => $date
                ];
                
                // 同步商品参数
                if(isset($cached_detail['goods_param'])) {
                    $update_data['goods_param'] = $cached_detail['goods_param'];
                }
                
                // 只有当勾选了价格同步选项且 sync_type 匹配时才同步价格
                if (($sync_type == 'all' || $sync_type == 'price') && $config['sync_price']) {
                    $update_data['price'] = $price;
                    $update_data['cost'] = floatval($cost_price);
                }
                
                // 同步商品名称（仅 all 或 name 模式）
                if (($sync_type == 'all' || $sync_type == 'name') && $config['sync_name'] && isset($product['name'])) {
                    $update_data['name'] = $product['name'];
                }
                
                // 同步商品图片（仅 all 或 image 模式）
                if (($sync_type == 'all' || $sync_type == 'image') && $config['sync_image']) {
                    // 优先使用商品详情中的图片，如果没有则使用列表数据
                    $shopimg = '';
                    if(isset($cached_detail['shopimg']) && !empty($cached_detail['shopimg'])) {
                        $shopimg = $cached_detail['shopimg'];
                    } elseif(isset($cached_detail['image']) && !empty($cached_detail['image'])) {
                        $shopimg = $cached_detail['image'];
                    } elseif(isset($product['shopimg']) && !empty($product['shopimg'])) {
                        $shopimg = $product['shopimg'];
                    }
                    if(!empty($shopimg)) {
                        $update_data['shopimg'] = $shopimg;
                    }
                }
                
                // 同步商品简介（仅 all 或 desc 模式）
                if (($sync_type == 'all' || $sync_type == 'desc') && $config['sync_desc'] && !empty($cached_detail)) {
                    // 使用缓存的商品详情，包括简介
                    $desc_content = '';
                    if(isset($cached_detail['desc'])) {
                        $desc_content = $cached_detail['desc'];
                    } elseif(isset($cached_detail['content'])) {
                        $desc_content = $cached_detail['content'];
                    } elseif(isset($cached_detail['info'])) {
                        $desc_content = $cached_detail['info'];
                    } elseif(isset($cached_detail['description'])) {
                        $desc_content = $cached_detail['description'];
                    }
                    
                    if(!empty($desc_content)) {
                        $update_data['desc'] = $desc_content;
                    }
                }
                
                // 同步输入框标题（input字段）
                if(isset($cached_detail['input']) && !empty($cached_detail['input'])) {
                    $update_data['input'] = $cached_detail['input'];
                }
                if(isset($cached_detail['inputs']) && !empty($cached_detail['inputs'])) {
                    $update_data['inputs'] = $cached_detail['inputs'];
                }
                
                // 同步默认数量信息
                $update_data['value'] = $default_num;
                
                // 同步最少下单数量（固定为1）和最大下单数量，支持不同字段名
                $update_data['min'] = 1;
                $update_data['max'] = intval($cached_detail['max'] ?? $cached_detail['limit_max'] ?? $cached_detail['buy_max_limit'] ?? $cached_detail['max_buy'] ?? $cached_detail['buy_max'] ?? 0);
                
                // 收集需要更新的商品数据
                if(!empty($update_data)) {
                    $update_products[] = $update_data;
                }
                
                // 收集需要更新的站点价格数据
                if($config['sync_price'] && $has_custom_price > 0) {
                    $update_site_prices[] = [
                        'tid' => $existing_product['tid'],
                        'price' => $price
                    ];
                }
                
                $task->updateProgress("收集更新商品: {$product['name']}");
            }
            
            // 批量插入新商品 - 教主优化，提高性能
                if(!empty($new_products)) {
                    $task->updateProgress("开始批量插入新商品，共 " . count($new_products) . " 个");
                
                try {
                    // 开始事务
                    $DB->beginTransaction();
                    
                    foreach($new_products as $new_product_data) {
                        // 使用直接SQL语句插入，避免表名前缀问题
                        $fields = array_keys($new_product_data);
                        $values = array_values($new_product_data);
                        $placeholders = implode(',', array_fill(0, count($values), '?'));
                        
                        $sql = "INSERT INTO pre_tools (`" . implode('`,`', $fields) . "`) VALUES ({$placeholders})";
                        $stmt = $DB->query($sql, $values);
                        
                        if($stmt !== false) {
                                $new_tid = $DB->lastInsertId();
                                $synced++;
                                // 记录上架日志
                                if($config['sync_log']) {
                                    $log_content = "自动同步新增商品";
                                    $log_sql = "INSERT INTO pre_tools_log (`tid`,`type`,`content`,`addtime`) VALUES (?,?,?,?)";
                                    $DB->query($log_sql, [$new_tid, 1, $log_content, $date]);
                                }
                            } else {
                                // 商品插入失败，继续处理
                            }
                    }
                    
                    // 提交事务
                    $DB->commit();
                    $task->updateProgress("批量插入完成，成功插入 " . count($new_products) . " 个商品");
                } catch(Exception $e) {
                    // 回滚事务
                    $DB->rollback();
                    $task->updateProgress("批量插入异常: " . $e->getMessage());
                }
            }
            
            // 批量更新商品 - 教主优化，提高性能
            if(!empty($update_products)) {
                $task->updateProgress("开始批量更新商品，共 " . count($update_products) . " 个");
                
                try {
                    // 开始事务
                    $DB->beginTransaction();
                    
                    foreach($update_products as $update_data) {
                        $tid = $update_data['tid'];
                        unset($update_data['tid']); // 移除tid字段，因为它是WHERE条件
                        
                        // 构建SQL更新语句
                        $set_clause = [];
                        $params = [];
                        foreach($update_data as $field => $value) {
                            $set_clause[] = "`{$field}` = ?";
                            $params[] = $value;
                        }
                        $params[] = $tid;
                        
                        $sql = "UPDATE pre_tools SET " . implode(', ', $set_clause) . " WHERE tid = ?";
                        $stmt = $DB->query($sql, $params);
                        
                        if($stmt !== false) {
                            $rowCount = $stmt->rowCount();
                            if($rowCount > 0) {
                                $updated++;
                                // 记录更新日志
                                if($config['sync_log']) {
                                    $log_content = "自动同步更新商品";
                                    $log_sql = "INSERT INTO pre_tools_log (`tid`,`type`,`content`,`addtime`) VALUES (?,?,?,?)";
                                    $DB->query($log_sql, [$tid, 2, $log_content, $date]);
                                }
                            }
                        } else {
                            // 商品更新失败，继续处理
                        }
                    }
                    
                    // 提交事务
                    $DB->commit();
                    $task->updateProgress("批量更新完成，成功更新 " . count($update_products) . " 个商品");
                } catch(Exception $e) {
                    // 回滚事务
                    $DB->rollback();
                    $task->updateProgress("批量更新异常: " . $e->getMessage());
                }
            }
            
            // 注意：不再批量更新 pre_site_price 表，因为那是分站用户的自定义价格，不应被同步覆盖
            // 上游价格变化只更新 pre_tools 商品基础表
            if(!empty($update_site_prices)) {
                $task->updateProgress("已跳过 " . count($update_site_prices) . " 个商品的站点自定义价格更新（保护用户定价）");
            }
            
            // 清理内存
            unset($new_products, $update_products, $update_site_prices, $product_details_cache, $existing_products);
            gc_collect_cycles();
            
            $task->updateProgress("第 {$batch_number}/{$total_batches} 批处理完成");
        }
        
        // 更新同步位置（仅在未处理完所有商品时）
        if($end_pos < $total_products) {
            setSyncPosition($shequ['id'], $end_pos);
        } else {
            // 已处理完所有商品，重置位置并进行完整的删除检查
            resetSyncPosition($shequ['id']);
        }
        
        // 使用去重后的所有商品ID处理下架商品（仅在完整同步时，且 sync_type 为 all 或 delete）
        if (($sync_type == 'all' || $sync_type == 'delete') && ($current_pos == 0 || $end_pos >= $total_products)) {
            $all_shequ_product_ids = array_unique($all_shequ_product_ids);
            
            // 处理下架商品
            if($config['delete_rule'] > 0) {
                $deleted_count = handleDeletedProducts($shequ['id'], $all_shequ_product_ids, $config['delete_rule'], $task);
                $deleted += $deleted_count;
            }
        }
        
    } catch(Exception $e) {
        $task->updateProgress("商品同步失败: " . $e->getMessage());
        echo "商品同步失败: " . $e->getMessage() . "<br/>";
    }
    
    return ['synced' => $synced, 'updated' => $updated, 'deleted' => $deleted];
}

/**
 * 处理下架商品
 */
function handleDeletedProducts($shequ_id, $active_product_ids, $delete_rule, $task) {
    global $DB, $date;
    
    $count = 0;
    // 使用 pre_tools 表而非 pre_goods 表 - 教主修改，博客地址：6v6.ren Q群：941535592
    $query = "SELECT tid, name FROM pre_tools WHERE is_curl=2 AND shequ=? AND active=1";
    $params = [$shequ_id];
    
    if(!empty($active_product_ids)) {
        // 使用参数化查询处理IN子句，防止SQL注入
        $placeholders = implode(',', array_fill(0, count($active_product_ids), '?'));
        $query .= " AND goods_id NOT IN ({$placeholders})";
        $params = array_merge($params, $active_product_ids);
    }
    
    $products = $DB->query($query, $params);
    
    while($product = $products->fetch()) {
        if($delete_rule == 1) {
            // 下架商品 - 教主修改，博客地址：6v6.ren Q群：941535592
            $DB->update('pre_tools', ['close' =>1, 'uptime' => $date], ['tid' => $product['tid']]);
            $task->updateProgress("下架商品: {$product['name']}");
        } elseif($delete_rule == 2) {
            // 删除商品 - 教主修改，博客地址：6v6.ren Q群：941535592
            $DB->delete('pre_tools', ['tid' => $product['tid']]);
            $task->updateProgress("删除商品: {$product['name']}");
        }
        $count++;
    }
    
    return $count;
}

/**
 * 根据加价模板计算价格
 */
function calculateMarkupPrice($base_price, $markup_template) {
    $price = $base_price;
    
    if($markup_template['type'] == 1) {
        // 固定金额加价
        $price += $markup_template['value'];
    } elseif($markup_template['type'] == 2) {
        // 百分比加价
        $price = $price * (1 + $markup_template['value'] / 100);
    }
    
    // 价格精度处理
    $price = round($price, 2);
    
    return $price;
}

/**
 * 保存远程图片到本地
 */
function saveRemoteImage($url) {
    try {
        $img_dir = SYS_ROOT . 'assets/upload/image/';
        $img_url = $conf['local'] . 'assets/upload/image/';
        
        // 创建目录
        if(!is_dir($img_dir)) {
            mkdir($img_dir, 0755, true);
        }
        
        // 获取文件名和扩展名
        $path_info = pathinfo($url);
        $ext = isset($path_info['extension']) ? '.' . $path_info['extension'] : '.jpg';
        $filename = md5($url . time()) . $ext;
        $filepath = $img_dir . $filename;
        $fileurl = $img_url . $filename;
        
        // 下载图片
        $img_data = @file_get_contents($url);
        if($img_data) {
            @file_put_contents($filepath, $img_data);
            if(file_exists($filepath)) {
                return $fileurl;
            }
        }
        
        return false;
    } catch(Exception $e) {
        return false;
    }
}
 
?>