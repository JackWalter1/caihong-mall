<?php
/*
 * 自动同步设置页面
 * 博客地址：zhonguo.ren
 * Q群：915043052
 * 开发者：教主
 */

// 生产环境关闭错误显示
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// 检查PHP版本
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
    die('PHP版本过低，请升级到PHP 7.0或更高版本');
}

// 确保common.php路径正确
if(!file_exists("../includes/common.php")) {
    die('找不到common.php文件，请检查路径');
}

include("../includes/common.php");

// 登录验证
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 检查并修复表结构的函数 - 教主添加 博客地址：zhonguo.ren Q群：915043052
function checkAndFixTableStructure() {
    global $DB;
    
    // 确保$DB是有效的对象
    if(!$DB || !is_object($DB)) {
        error_log('数据库连接对象无效');
        return;
    }
    
    try {
        // 检查表是否存在 - 使用更通用的方法
        $checkTable = 0;
        
        // 首先尝试使用query方法（适用于PDO和大多数数据库类）
        if(method_exists($DB, 'query')) {
            try {
                $result = $DB->query("SHOW TABLES LIKE 'pre_sync_config'");
                
                // 获取结果行数（不同数据库类可能有不同的实现）
                if(method_exists($result, 'rowCount')) {
                    $checkTable = $result->rowCount();
                } else if(method_exists($DB, 'num_rows')) {
                    $checkTable = $DB->num_rows($result);
                } else {
                    // 尝试获取所有行并计算
                    $rows = [];
                    if(method_exists($result, 'fetchAll')) {
                        $rows = $result->fetchAll();
                    } else if(method_exists($result, 'fetch_array')) {
                        while($row = $result->fetch_array()) {
                            $rows[] = $row;
                        }
                    }
                    $checkTable = count($rows);
                }
            } catch (Exception $e) {
                error_log('检查表是否存在时出错: ' . $e->getMessage());
                $checkTable = 0;
            }
        } else if(method_exists($DB, 'getRow')) {
            // 如果有getRow方法，尝试用它来检查表是否存在
            try {
                $row = $DB->getRow("SHOW TABLES LIKE 'pre_sync_config'");
                $checkTable = $row ? 1 : 0;
            } catch (Exception $e) {
                error_log('检查表是否存在时出错: ' . $e->getMessage());
                $checkTable = 0;
            }
        } else {
            // 默认假设表不存在
            $checkTable = 0;
        }
        
        if($checkTable == 0) {
            // 创建表
            $createTableSql = "CREATE TABLE `pre_sync_config` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `shequ_id` int(11) NOT NULL,
                `sync_interval` int(11) NOT NULL DEFAULT '60',
                `sync_limit` int(11) NOT NULL DEFAULT '100',
                `auto_update` tinyint(1) NOT NULL DEFAULT '1',
                `delete_rule` tinyint(1) NOT NULL DEFAULT '0',
                `sync_class` tinyint(1) NOT NULL DEFAULT '1',
                `sync_sort` tinyint(1) NOT NULL DEFAULT '1',
                `sync_goods_sort` tinyint(1) NOT NULL DEFAULT '1',
                `sync_log` tinyint(1) NOT NULL DEFAULT '1',
                `sync_name` tinyint(1) NOT NULL DEFAULT '1',
                `sync_price` tinyint(1) NOT NULL DEFAULT '1',
                `sync_cost` tinyint(1) NOT NULL DEFAULT '1',
                `sync_desc` tinyint(1) NOT NULL DEFAULT '1',
                `sync_image` tinyint(1) NOT NULL DEFAULT '1',
                `sync_workorder` tinyint(1) NOT NULL DEFAULT '1',
                `sync_details` tinyint(1) NOT NULL DEFAULT '0',
                `add_class` tinyint(1) NOT NULL DEFAULT '1',
                `add_goods` tinyint(1) NOT NULL DEFAULT '1',
                `markup_template` tinyint(1) NOT NULL DEFAULT '0',
                `status` tinyint(1) NOT NULL DEFAULT '0',
                `addtime` datetime NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `shequ_id` (`shequ_id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;";
            
            try {
                if(method_exists($DB, 'exec')) {
                    $DB->exec($createTableSql);
                } else if(method_exists($DB, 'query')) {
                    $DB->query($createTableSql);
                }
            } catch (Exception $e) {
                error_log('创建pre_sync_config表失败: ' . $e->getMessage());
            }
            return;
        }
        
        // 检查表结构，添加缺失的字段
        $requiredFields = [
            'markup_template' => "ALTER TABLE `pre_sync_config` ADD COLUMN `markup_template` tinyint(1) NOT NULL DEFAULT '0' AFTER `add_goods`",
            'sync_cost' => "ALTER TABLE `pre_sync_config` ADD COLUMN `sync_cost` tinyint(1) NOT NULL DEFAULT '1' AFTER `sync_price`",
            'sync_workorder' => "ALTER TABLE `pre_sync_config` ADD COLUMN `sync_workorder` tinyint(1) NOT NULL DEFAULT '1' AFTER `sync_image`",
            'add_class' => "ALTER TABLE `pre_sync_config` ADD COLUMN `add_class` tinyint(1) NOT NULL DEFAULT '1' AFTER `sync_workorder`",
            'add_goods' => "ALTER TABLE `pre_sync_config` ADD COLUMN `add_goods` tinyint(1) NOT NULL DEFAULT '1' AFTER `add_class`",
            'sync_details' => "ALTER TABLE `pre_sync_config` ADD COLUMN `sync_details` tinyint(1) NOT NULL DEFAULT '0' AFTER `sync_workorder`"
        ];
        
        // 获取表结构 - 适应不同的数据库连接对象
        $columns = [];
        try {
            if(method_exists($DB, 'query')) {
                $result = $DB->query("SHOW COLUMNS FROM `pre_sync_config`");
                
                // 根据不同的数据库类获取列名
                if(method_exists($result, 'fetchAll')) {
                    if(defined('PDO::FETCH_COLUMN')) {
                        $columns = $result->fetchAll(PDO::FETCH_COLUMN);
                    } else {
                        // 手动提取列名
                        while($row = $result->fetch()) {
                            $columns[] = is_array($row) ? $row[0] : $row->Field;
                        }
                    }
                } else if(method_exists($result, 'fetch_array')) {
                    while($row = $result->fetch_array()) {
                        $columns[] = $row[0];
                    }
                }
            } else if(method_exists($DB, 'queryall')) {
                // 某些自定义数据库类可能使用queryall方法
                $result = $DB->queryall("SHOW COLUMNS FROM `pre_sync_config`");
                foreach($result as $row) {
                    $columns[] = is_array($row) ? $row[0] : $row->Field;
                }
            }
        } catch (Exception $e) {
            error_log('获取表结构失败: ' . $e->getMessage());
            // 如果无法获取表结构，继续执行
        }
        
        // 检查并添加缺失的字段
        foreach($requiredFields as $field => $sql) {
            if(!in_array($field, $columns)) {
                try {
                    if(method_exists($DB, 'exec')) {
                        $DB->exec($sql);
                    } else if(method_exists($DB, 'query')) {
                        $DB->query($sql);
                    }
                } catch (Exception $e) {
                    error_log('添加字段失败: ' . $field . ', 错误: ' . $e->getMessage());
                    // 继续尝试其他字段
                }
            }
        }
    } catch (Exception $e) {
        error_log('表结构检查失败: ' . $e->getMessage());
        // 不抛出异常，避免中断程序
    }
}

// 处理POST请求 - 移到文件开头，确保在任何HTML输出之前处理
if(isset($_POST['submit'])){
    // 全局错误捕获
    try {
        // 记录请求开始
        error_log('开始处理同步配置保存请求');
        
        // 获取当前时间
        $date = date('Y-m-d H:i:s');
        
        // 安全获取POST数据 - 教主修改，添加类型检查
        $shequ_ids = isset($_POST['shequ_ids']) && is_array($_POST['shequ_ids']) ? $_POST['shequ_ids'] : [];
        $sync_interval = isset($_POST['sync_interval']) ? intval($_POST['sync_interval']) : 300;
        $sync_interval_unit = isset($_POST['sync_interval_unit']) ? trim($_POST['sync_interval_unit']) : 'seconds';
        // 如果单位是分钟，转换为秒
        if($sync_interval_unit == 'minutes') {
            $sync_interval = $sync_interval * 60;
        }
        $sync_limit = isset($_POST['sync_limit']) ? intval($_POST['sync_limit']) : 100;
        $monitor_key = isset($_POST['monitor_key']) ? trim($_POST['monitor_key']) : '';
        
        $success = 0;
        $error = '';
        
        // 检查数据库连接 - 教主修改，使用更宽松的检查逻辑
        if(!$DB || !is_object($DB)) {
            error_log('数据库连接无效: $DB不是有效的对象');
            // 不抛出异常，尝试继续执行
        } else {
            // 测试数据库连接是否可用
            try {
                // 使用一个简单的SQL查询来测试连接
                if(method_exists($DB, 'query')) {
                    $testResult = $DB->query("SELECT 1");
                    if(!$testResult) {
                        error_log('数据库连接测试失败');
                    }
                }
            } catch (Exception $e) {
                // 捕获连接测试异常
                error_log('数据库连接测试异常: ' . $e->getMessage());
                // 继续执行，不直接抛出异常，因为可能是其他类型的数据库连接
            }
        }
        
        // 检查并修复表结构 - 函数内部已处理异常
        checkAndFixTableStructure();
        
        // 先禁用所有配置 - 教主修改，支持不同数据库对象类型
        try {
            if($DB && is_object($DB)) {
                if(method_exists($DB, 'exec')) {
                    $result = $DB->exec("UPDATE pre_sync_config SET status=0");
                    if($result === false && method_exists($DB, 'error')) {
                        $error .= '更新状态失败['.$DB->error().']<br/>';
                    }
                } else if(method_exists($DB, 'query')) {
                    $result = $DB->query("UPDATE pre_sync_config SET status=0");
                    if(!$result) {
                        $error .= '更新状态失败<br/>';
                    }
                }
            }
        } catch (Exception $e) {
            $error .= '更新状态时发生异常: ' . $e->getMessage() . '<br/>';
            error_log('更新状态异常: ' . $e->getMessage());
        }
        
        // 处理选中的站点
        if(!empty($shequ_ids)){
            try {
                foreach($shequ_ids as $shequ_id){
                    // 确保shequ_id是有效的整数
                    $shequ_id = intval($shequ_id);
                    if($shequ_id <= 0) continue;
                    
                    // 安全获取POST数据，避免未定义索引错误
                        $auto_update = isset($_POST['auto_update_'.$shequ_id]) ? intval($_POST['auto_update_'.$shequ_id]) : 1;
                        $delete_rule = isset($_POST['delete_rule_'.$shequ_id]) ? intval($_POST['delete_rule_'.$shequ_id]) : 0;
                        $markup_template = isset($_POST['markup_template_'.$shequ_id]) ? intval($_POST['markup_template_'.$shequ_id]) : 0;
                        $sync_details = isset($_POST['sync_details_'.$shequ_id]) ? intval($_POST['sync_details_'.$shequ_id]) : 0;
                    
                        $data = [
                            'shequ_id' => $shequ_id,
                            'sync_interval' => $sync_interval,
                            'sync_limit' => $sync_limit,
                            'auto_update' => $auto_update,
                            'delete_rule' => $delete_rule,
                            'sync_class' => isset($_POST['sync_class_'.$shequ_id])?1:0,
                            'sync_sort' => isset($_POST['sync_sort_'.$shequ_id])?1:0,
                            'sync_goods_sort' => isset($_POST['sync_goods_sort_'.$shequ_id])?1:0,
                            'sync_log' => isset($_POST['sync_log_'.$shequ_id])?1:0,
                            'sync_name' => isset($_POST['sync_name_'.$shequ_id])?1:0,
                            'sync_price' => isset($_POST['sync_price_'.$shequ_id])?1:0,
                            'sync_cost' => isset($_POST['sync_cost_'.$shequ_id])?1:0,
                            'sync_desc' => isset($_POST['sync_desc_'.$shequ_id])?1:0,
                            'sync_image' => isset($_POST['sync_image_'.$shequ_id])?1:0,
                            'sync_workorder' => isset($_POST['sync_workorder_'.$shequ_id])?1:0,
                            'sync_details' => $sync_details,
                            'add_class' => isset($_POST['add_class_'.$shequ_id])?1:0,
                            'add_goods' => isset($_POST['add_goods_'.$shequ_id])?1:0,
                            'markup_template' => $markup_template,
                            'status' => 1,
                            'addtime' => $date
                        ];
                    
                    // 使用参数化查询防止SQL注入 - 教主修改，支持不同数据库对象
                    try {
                        $exists = false;
                        if($DB && is_object($DB)) {
                            if(method_exists($DB, 'prepare') && method_exists($DB, 'execute')) {
                                // PDO方式
                                $stmt = $DB->prepare("SELECT * FROM pre_sync_config WHERE shequ_id=?");
                                $stmt->execute([$shequ_id]);
                                $exists = $stmt->fetch();
                            } else if(method_exists($DB, 'query')) {
                                // 普通查询方式，注意防止SQL注入
                                $sql = "SELECT * FROM pre_sync_config WHERE shequ_id='".intval($shequ_id)."'";
                                $result = $DB->query($sql);
                                if($result) {
                                    if(method_exists($result, 'fetch')) {
                                        $exists = $result->fetch();
                                    } else if(method_exists($result, 'fetch_array')) {
                                        $exists = $result->fetch_array();
                                    }
                                }
                            }
                        }
                        
                        if($exists){
                            // 更新操作 - 教主修改，支持不同数据库对象
                            $result = false;
                            if($DB && is_object($DB)) {
                                if(method_exists($DB, 'prepare') && method_exists($DB, 'execute')) {
                                    // PDO方式
                                    $update_sql = "UPDATE pre_sync_config SET "
                                                . "sync_interval=?, sync_limit=?, auto_update=?, "
                                                . "delete_rule=?, sync_class=?, sync_sort=?, "
                                                . "sync_goods_sort=?, sync_log=?, sync_name=?, "
                                                . "sync_price=?, sync_cost=?, sync_desc=?, "
                                                . "sync_image=?, sync_workorder=?, sync_details=?, add_class=?, "
                                                . "add_goods=?, markup_template=?, status=?, addtime=? "
                                                . "WHERE shequ_id=?";
                                    $update_stmt = $DB->prepare($update_sql);
                                    $result = $update_stmt->execute([
                                        $data['sync_interval'], $data['sync_limit'], $data['auto_update'],
                                        $data['delete_rule'], $data['sync_class'], $data['sync_sort'],
                                        $data['sync_goods_sort'], $data['sync_log'], $data['sync_name'],
                                        $data['sync_price'], $data['sync_cost'], $data['sync_desc'],
                                        $data['sync_image'], $data['sync_workorder'], $data['sync_details'],
                                        $data['add_class'], $data['add_goods'], $data['markup_template'],
                                        $data['status'], $data['addtime'], $data['shequ_id']
                                    ]);
                                } else if(method_exists($DB, 'query')) {
                                    // 普通查询方式，注意防止SQL注入
                                    $sql = "UPDATE pre_sync_config SET "
                                        . "sync_interval='".intval($data['sync_interval'])."', "
                                        . "sync_limit='".intval($data['sync_limit'])."', "
                                        . "auto_update='".intval($data['auto_update'])."', "
                                        . "delete_rule='".intval($data['delete_rule'])."', "
                                        . "sync_class='".intval($data['sync_class'])."', "
                                        . "sync_sort='".intval($data['sync_sort'])."', "
                                        . "sync_goods_sort='".intval($data['sync_goods_sort'])."', "
                                        . "sync_log='".intval($data['sync_log'])."', "
                                        . "sync_name='".intval($data['sync_name'])."', "
                                        . "sync_price='".intval($data['sync_price'])."', "
                                        . "sync_cost='".intval($data['sync_cost'])."', "
                                        . "sync_desc='".intval($data['sync_desc'])."', "
                                        . "sync_image='".intval($data['sync_image'])."', "
                                        . "sync_workorder='".intval($data['sync_workorder'])."', "
                                        . "sync_details='".intval($data['sync_details'])."', "
                                        . "add_class='".intval($data['add_class'])."', "
                                        . "add_goods='".intval($data['add_goods'])."', "
                                        . "markup_template='".intval($data['markup_template'])."', "
                                        . "status='".intval($data['status'])."', "
                                        . "addtime='".addslashes($data['addtime'])."' "
                                        . "WHERE shequ_id='".intval($data['shequ_id'])."'";
                                    $result = $DB->query($sql);
                                }
                            }
                            
                            if($result){
                                $success++;
                            } else {
                                $error .= '站点ID'.$shequ_id.'修改失败<br/>';
                            }
                        } else {
                            // 插入操作 - 教主修改，支持不同数据库对象
                            $result = false;
                            if($DB && is_object($DB)) {
                                if(method_exists($DB, 'prepare') && method_exists($DB, 'execute')) {
                                    // PDO方式
                                    $insert_sql = "INSERT INTO pre_sync_config ("
                                                . "shequ_id, sync_interval, sync_limit, auto_update, "
                                                . "delete_rule, sync_class, sync_sort, sync_goods_sort, "
                                                . "sync_log, sync_name, sync_price, sync_cost, sync_desc, "
                                                . "sync_image, sync_workorder, sync_details, add_class, add_goods, "
                                                . "markup_template, status, addtime) "
                                                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                    $insert_stmt = $DB->prepare($insert_sql);
                                    $result = $insert_stmt->execute([
                                        $data['shequ_id'], $data['sync_interval'], $data['sync_limit'],
                                        $data['auto_update'], $data['delete_rule'], $data['sync_class'],
                                        $data['sync_sort'], $data['sync_goods_sort'], $data['sync_log'],
                                        $data['sync_name'], $data['sync_price'], $data['sync_cost'],
                                        $data['sync_desc'], $data['sync_image'], $data['sync_workorder'],
                                        $data['sync_details'], $data['add_class'], $data['add_goods'],
                                        $data['markup_template'], $data['status'], $data['addtime']
                                    ]);
                                } else if(method_exists($DB, 'query')) {
                                    // 普通查询方式，注意防止SQL注入
                                    $sql = "INSERT INTO pre_sync_config SET "
                                        . "shequ_id='".intval($data['shequ_id'])."', "
                                        . "sync_interval='".intval($data['sync_interval'])."', "
                                        . "sync_limit='".intval($data['sync_limit'])."', "
                                        . "auto_update='".intval($data['auto_update'])."', "
                                        . "delete_rule='".intval($data['delete_rule'])."', "
                                        . "sync_class='".intval($data['sync_class'])."', "
                                        . "sync_sort='".intval($data['sync_sort'])."', "
                                        . "sync_goods_sort='".intval($data['sync_goods_sort'])."', "
                                        . "sync_log='".intval($data['sync_log'])."', "
                                        . "sync_name='".intval($data['sync_name'])."', "
                                        . "sync_price='".intval($data['sync_price'])."', "
                                        . "sync_cost='".intval($data['sync_cost'])."', "
                                        . "sync_desc='".intval($data['sync_desc'])."', "
                                        . "sync_image='".intval($data['sync_image'])."', "
                                        . "sync_workorder='".intval($data['sync_workorder'])."', "
                                        . "sync_details='".intval($data['sync_details'])."', "
                                        . "add_class='".intval($data['add_class'])."', "
                                        . "add_goods='".intval($data['add_goods'])."', "
                                        . "markup_template='".intval($data['markup_template'])."', "
                                        . "status='".intval($data['status'])."', "
                                        . "addtime='".addslashes($data['addtime'])."'";
                                    $result = $DB->query($sql);
                                }
                            }
                            
                            if($result){
                                $success++;
                            } else {
                                $error .= '站点ID'.$shequ_id.'添加失败<br/>';
                            }
                        }
                    } catch (Exception $e) {
                        $error .= '处理站点ID'.$shequ_id.'时异常: ' . $e->getMessage() . '<br/>';
                        error_log('站点ID'.$shequ_id.'处理异常: ' . $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                $error .= '处理站点配置时发生错误: ' . $e->getMessage() . '<br/>';
                error_log('站点配置处理异常: ' . $e->getMessage());
            }
        }
        
        // 保存监控密钥 - 教主修改，支持不同数据库对象
        try {
            if($DB && is_object($DB)) {
                if(method_exists($DB, 'prepare') && method_exists($DB, 'execute')) {
                    // PDO方式
                    $stmt = $DB->prepare("REPLACE INTO pre_config SET k='monitor_key', v=?");
                    $stmt->execute([$monitor_key]);
                } else if(method_exists($DB, 'query')) {
                    // 普通查询方式，注意防止SQL注入
                    $sql = "REPLACE INTO pre_config SET k='monitor_key', v='".addslashes($monitor_key)."'";
                    $DB->query($sql);
                }
            }
        } catch (Exception $e) {
            $error .= '保存监控密钥失败: ' . $e->getMessage() . '<br/>';
            error_log('保存监控密钥异常: ' . $e->getMessage());
        }
        
        // 清除所有输出缓冲
        @ob_clean();
        // 设置正确的内容类型
        header('Content-Type: application/json; charset=utf-8');
        // 确保不缓存
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        
        // 构造响应数据
        $response = [];
        if($error){
            $response = ['code' => 0, 'msg' => $error];
            error_log('保存失败: ' . $error);
        } else {
            if($success > 0){
                $response = ['code' => 1, 'msg' => '成功配置'.$success.'个站点!'];
            } else {
                $response = ['code' => 1, 'msg' => '已成功禁用所有同步配置!'];
            }
        }
        
        // 输出JSON并立即终止脚本
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
        
    } catch (Exception $e) {
        // 捕获所有异常，确保返回有效的JSON
        @ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        $error_msg = '服务器内部错误: ' . $e->getMessage();
        error_log('AJAX处理异常: ' . $e->getMessage());
        echo json_encode(['code' => 0, 'msg' => $error_msg], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// 页面标题设置
$title='自动同步设置';
include './head.php';

echo '<script src="//cdn.bootcdn.net/ajax/libs/layer/3.1.1/layer.js"></script>';


// 检查并修复表结构
checkAndFixTableStructure();

// 获取所有对接站点 - 教主修改，确保所有站点都能显示
$rs = $DB->query("SELECT * FROM pre_shequ order by id asc");
$shequlist = array();
while ($res = $rs->fetch()) {
    // 尝试获取插件配置
    $getInfo = \lib\Plugin::getConfig("third_" . $res["type"]);
    if ($getInfo) {
        $res['title'] = $getInfo['title'];
    } else {
        // 如果没有插件配置，使用默认标题 - 教主修改
        $res['title'] = '站点 ' . $res["type"] . ' - ' . $res["id"];
    }
    // 确保所有站点都添加到列表中
    $shequlist[] = $res;
}

// 获取已启用的配置
$enabled_configs = array();
$rs = $DB->query("SELECT * FROM pre_sync_config WHERE status=1");
while($row = $rs->fetch()){
    $enabled_configs[$row['shequ_id']] = $row;
}

// 获取监控地址
$monitor_key = $DB->getColumn("SELECT v FROM pre_config WHERE k='monitor_key'");
$monitor_url = $siteurl.'admin/cx-api-synchronization.php'.($monitor_key?'?key='.$monitor_key:'');
// 获取最近运行时间
$last_run_time = $DB->getColumn("SELECT v FROM pre_config WHERE k='last_sync_time'");
if(!$last_run_time) $last_run_time = '从未运行';

?>


<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title"><h3 class="panel-title">自动同步设置</h3></div>
        <div class="card">
            <div class="card-body">
                <form onsubmit="return save()" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">监控地址</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="<?php echo $monitor_url?>" readonly>
                            <small class="help-block">全部站点统一同步地址（不推荐，压力集中）</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">计划任务</label>
                        <div class="col-sm-10">
                            <pre>*/5 * * * * curl --silent <?php echo $monitor_url?></pre>
                            <small class="help-block">全部站点统一同步（不推荐）</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">最近运行</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="<?php echo $last_run_time?>" readonly>
                        </div>
                    </div>
                    <div class="alert alert-info" style="margin-top:10px;">
                        <i class="fa fa-info-circle"></i> <strong>推荐方案：分站独立监控</strong>
                        <br>• 勾选下方站点后，每个站点会生成独立的监控地址
                        <br>• 为每个站点单独设置计划任务，分散压力，避免超时
                        <br>• 不同站点可以设置不同的执行频率
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">监控密钥</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" name="monitor_key" value="<?php echo $monitor_key?>" placeholder="请设置监控密钥">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" onclick="generateKey()">生成密钥</button>
                                </span>
                            </div>
                            <small class="help-block">设置后需要在监控地址后面加上 ?key=监控密钥 才能访问，为空则不限制访问</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">选择站点</label>
                        <div class="col-sm-10">
                            <div class="site-nav">
                                <div class="site-nav-header">
                                    <div class="site-nav-title">
                                        <h4>对接站点列表</h4>
                                        <span class="site-count">已选择 <b id="selected_count">0</b> 个站点</span>
                                    </div>
                                    <div class="site-nav-actions">
                                        <label class="select-all">
                                            <input type="checkbox" onclick="checkAll(this)">
                                            <span>全选</span>
                                        </label>
                                        <a href="./shequlist.php" class="btn btn-link">
                                            <i class="fa fa-plus"></i> 添加站点
                                        </a>
                                    </div>
                                </div>
                                <div class="site-list">
                                    <?php foreach($shequlist as $row){
                                        $checked = isset($enabled_configs[$row['id']]) ? ' checked' : '';
                                        $config = isset($enabled_configs[$row['id']]) ? $enabled_configs[$row['id']] : [];
                                        echo '<div class="site-item'.($checked?' active':'').'">';
                                        echo '<div class="site-item-header">';
                                        echo '<label class="site-checkbox">';
                                        echo '<input type="checkbox" name="shequ_ids[]" value="'.$row['id'].'" onclick="toggleSiteConfig(this,'.$row['id'].')"'.$checked.'>';
                                        echo '<span class="site-title">'.$row['title'].'</span>';
                                        echo '</label>';
                                        echo '<div class="site-url">'.$row['url'].'</div>';
                                        if($row['remark']) echo '<div class="site-remark">'.$row['remark'].'</div>';
                                        echo '</div>';
                                        
                                        echo '<div class="site-config'.($checked?' show':'').'" id="config_'.$row['id'].'">';
                        echo '<div class="config-section">';
                        echo '<div class="config-title">基础设置</div>';
                        echo '<div class="config-row">';
                        echo '<div class="config-item">';
                        echo '<label>自动更新</label>';
                        $auto_update_val = isset($config['auto_update']) ? intval($config['auto_update']) : 1;
                        echo '<select name="auto_update_'.$row['id'].'" class="form-control">';
                        echo '<option value="1"'.($auto_update_val == 1 ? ' selected' : '').'>开启</option>';
                        echo '<option value="0"'.($auto_update_val == 0 ? ' selected' : '').'>关闭</option>';
                        echo '</select>';
                        echo '</div>';
                        echo '<div class="config-item">';
                        echo '<label>下架商品处理</label>';
                        $delete_rule_val = isset($config['delete_rule']) ? intval($config['delete_rule']) : 0;
                        echo '<select name="delete_rule_'.$row['id'].'" class="form-control">';
                        echo '<option value="0"'.($delete_rule_val == 0 ? ' selected' : '').'>保留商品</option>';
                        echo '<option value="1"'.($delete_rule_val == 1 ? ' selected' : '').'>下架商品</option>';
                        echo '<option value="2"'.($delete_rule_val == 2 ? ' selected' : '').'>删除商品</option>';
                        echo '</select>';
                        echo '<small class="select-desc">保留商品：不处理下架商品<br>下架商品：将下架商品设为下架状态<br>删除商品：直接删除下架商品</small>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        
                        echo '<div class="config-section">';
                        echo '<div class="config-title">同步选项</div>';
                        echo '<div class="config-row">';
                        echo '<div class="config-item">';
                        echo '<label>获取商品详情</label>';
                        $sync_details_val = isset($config['sync_details']) ? intval($config['sync_details']) : 0;
                        echo '<select name="sync_details_'.$row['id'].'" class="form-control">';
                        echo '<option value="1"'.($sync_details_val == 1 ? ' selected' : '').'>开启（速度较慢，数据完整）</option>';
                        echo '<option value="0"'.($sync_details_val == 0 ? ' selected' : '').'>关闭（速度快，使用列表数据）</option>';
                        echo '</select>';
                        echo '<small class="select-desc">关闭后将不调用goods_info接口，大幅提高同步速度</small>';
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="checkbox-group">';
                                        $sync_class_checked = (isset($config['sync_class']) && $config['sync_class'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="同步商品分类信息，更新商品所属分类"><input type="checkbox" name="sync_class_'.$row['id'].'" value="1"'.$sync_class_checked.'><span>分类</span><small class="option-desc">更新商品所属分类，仅影响已有商品</small></label>';
                                        $sync_sort_checked = (isset($config['sync_sort']) && $config['sync_sort'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="同步分类排序"><input type="checkbox" name="sync_sort_'.$row['id'].'" value="1"'.$sync_sort_checked.'><span>分类排序</span><small class="option-desc">同步分类的排序</small></label>';
                                        $sync_goods_sort_checked = (isset($config['sync_goods_sort']) && $config['sync_goods_sort'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="同步商品排序"><input type="checkbox" name="sync_goods_sort_'.$row['id'].'" value="1"'.$sync_goods_sort_checked.'><span>商品排序</span><small class="option-desc">同步商品的排序</small></label>';
                                        $sync_log_checked = (isset($config['sync_log']) && $config['sync_log'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="记录商品上架日志"><input type="checkbox" name="sync_log_'.$row['id'].'" value="1"'.$sync_log_checked.'><span>上架日志</span><small class="option-desc">新增商品时记录上架日志</small></label>';
                                        $sync_name_checked = (isset($config['sync_name']) && $config['sync_name'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="同步商品名称"><input type="checkbox" name="sync_name_'.$row['id'].'" value="1"'.$sync_name_checked.'><span>名称</span><small class="option-desc">同步商品名称</small></label>';
                                        $sync_price_checked = (isset($config['sync_price']) && $config['sync_price'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="同步商品销售价格"><input type="checkbox" name="sync_price_'.$row['id'].'" value="1"'.$sync_price_checked.'><span>价格</span><small class="option-desc">同步商品销售价格</small></label>';
                                        $sync_cost_checked = (isset($config['sync_cost']) && $config['sync_cost'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="同步商品成本价格"><input type="checkbox" name="sync_cost_'.$row['id'].'" value="1"'.$sync_cost_checked.'><span>成本</span><small class="option-desc">同步商品成本价格</small></label>';
                                        $sync_desc_checked = (isset($config['sync_desc']) && $config['sync_desc'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="同步商品详细描述"><input type="checkbox" name="sync_desc_'.$row['id'].'" value="1"'.$sync_desc_checked.'><span>描述</span><small class="option-desc">同步商品详细描述</small></label>';
                                        $sync_image_checked = (isset($config['sync_image']) && $config['sync_image'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="同步商品图片资源"><input type="checkbox" name="sync_image_'.$row['id'].'" value="1"'.$sync_image_checked.'><span>图片</span><small class="option-desc">同步商品图片资源</small></label>';
                                        $sync_workorder_checked = (isset($config['sync_workorder']) && $config['sync_workorder'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="开启时商品将支持网盘投诉"><input type="checkbox" name="sync_workorder_'.$row['id'].'" value="1"'.$sync_workorder_checked.'><span>网盘投诉</span><small class="option-desc">开启时将添加或更新的商品的网盘投诉设置为开启状态</small></label>';
                                        echo '</div>';
                                        echo '</div>';
                                        
                                        echo '<div class="config-section">';
                                        echo '<div class="config-title">新增选项</div>';
                                        echo '<div class="config-row">';
                                        echo '<div class="config-item">';
                                        echo '<label>加价模板</label>';
                                        $markup_template_val = isset($config['markup_template']) ? intval($config['markup_template']) : 0;
                                        echo '<select name="markup_template_'.$row['id'].'" class="form-control">';
                                        echo '<option value="0"'.($markup_template_val == 0 ? ' selected' : '').'>不使用加价模板</option>';
                                        $rs=$DB->query("SELECT * FROM pre_price ORDER BY id ASC");
                                        while($res = $rs->fetch()){
                                            echo '<option value="'.$res['id'].'"'.($markup_template_val == $res['id'] ? ' selected' : '').'>'.$res['name'].'</option>';
                                        }
                                        echo '</select>';
                                        echo '<small class="select-desc">选择加价模板后，新增商品时将自动应用加价规则</small>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '<div class="checkbox-group">';
                                        $add_class_checked = (isset($config['add_class']) && $config['add_class'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="允许添加新的商品分类，禁用时只能使用已有分类"><input type="checkbox" name="add_class_'.$row['id'].'" value="1"'.$add_class_checked.'><span>允许新增分类</span><small class="option-desc">禁用时，没有对应分类的商品将不会被添加</small></label>';
                                        $add_goods_checked = (isset($config['add_goods']) && $config['add_goods'] == 1) ? ' checked' : '';
                                        echo '<label class="checkbox-item" title="允许添加新的商品"><input type="checkbox" name="add_goods_'.$row['id'].'" value="1"'.$add_goods_checked.'><span>允许新增商品</span><small class="option-desc">允许添加新的商品</small></label>';
                                        echo '</div>';
                                        echo '</div>';
                                        
                                        // 独立监控地址（按类型分离）
                                        $key_param = $monitor_key ? '?key='.$monitor_key : '?';
                                        $base_url = $siteurl.'admin/cx-api-synchronization.php'.$key_param.'&shequ_id='.$row['id'].'&sync_type=';
                                        
                                        // 定义各同步类型
                                        $sync_types = [
                                            ['type' => 'class', 'label' => '分类同步', 'desc' => '同步商品分类信息', 'interval' => '0 */6 * * *', 'interval_desc' => '建议每6小时一次'],
                                            ['type' => 'price', 'label' => '价格同步', 'desc' => '同步价格+成本+默认数量', 'interval' => '*/5 * * * *', 'interval_desc' => '建议每5分钟一次'],
                                            ['type' => 'name', 'label' => '名称同步', 'desc' => '同步商品名称', 'interval' => '*/30 * * * *', 'interval_desc' => '建议每30分钟一次'],
                                            ['type' => 'desc', 'label' => '描述同步', 'desc' => '同步商品详细描述', 'interval' => '0 */6 * * *', 'interval_desc' => '建议每6小时一次'],
                                            ['type' => 'image', 'label' => '图片同步', 'desc' => '同步商品图片', 'interval' => '0 */6 * * *', 'interval_desc' => '建议每6小时一次'],
                                            ['type' => 'goods', 'label' => '新增商品', 'desc' => '只添加新商品，不更新已有', 'interval' => '*/5 * * * *', 'interval_desc' => '建议每5分钟一次'],
                                            ['type' => 'delete', 'label' => '下架处理', 'desc' => '处理对接站已下架的商品', 'interval' => '0 */2 * * *', 'interval_desc' => '建议每2小时一次'],
                                        ];
                                        
                                        echo '<div class="config-section">';
                                        echo '<div class="config-title">独立监控地址（按类型分离）</div>';
                                        echo '<div class="alert alert-success" style="font-size:12px;padding:8px;margin-bottom:10px;">';
                                        echo '<i class="fa fa-lightbulb-o"></i> <strong>推荐：</strong>将不同类型拆分为独立计划任务，高频任务（价格/新增商品）频繁执行，低频任务（分类/描述/图片）降低频率。';
                                        echo '</div>';
                                        
                                        // 全部同步（兼容）
                                        $all_url = $siteurl.'admin/cx-api-synchronization.php'.($monitor_key?'?key='.$monitor_key.'&shequ_id='.$row['id']:'?shequ_id='.$row['id']);
                                        echo '<div class="sync-type-item" style="margin-bottom:8px;border:1px dashed #ddd;border-radius:4px;padding:8px;">';
                                        echo '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">';
                                        echo '<strong style="font-size:12px;color:#999;">全部同步（不推荐，压力集中）</strong>';
                                        echo '<span style="font-size:11px;color:#999;">*/5 * * * *</span>';
                                        echo '</div>';
                                        echo '<input type="text" class="form-control" value="'.$all_url.'" readonly style="font-size:11px;height:28px;">';
                                        echo '</div>';
                                        
                                        // 各类型独立URL
                                        foreach ($sync_types as $st) {
                                            $type_url = $base_url . $st['type'];
                                            echo '<div class="sync-type-item" style="margin-bottom:8px;border:1px solid #e2e8f0;border-radius:4px;padding:8px;background:#fff;">';
                                            echo '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">';
                                            echo '<div>';
                                            echo '<strong style="font-size:13px;">'.$st['label'].'</strong>';
                                            echo '<span style="font-size:11px;color:#718096;margin-left:8px;">'.$st['desc'].'</span>';
                                            echo '</div>';
                                            echo '<div style="text-align:right;">';
                                            echo '<code style="font-size:11px;">'.$st['interval'].'</code><br/>';
                                            echo '<small style="color:#718096;">'.$st['interval_desc'].'</small>';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '<div style="display:flex;gap:4px;">';
                                            echo '<input type="text" class="form-control" value="'.$type_url.'" readonly style="font-size:11px;height:28px;flex:1;">';
                                            echo '<button type="button" class="btn btn-xs btn-default" onclick="copyUrl(this,\''.$type_url.'\')" title="复制URL" style="height:28px;"><i class="fa fa-copy"></i></button>';
                                            echo '</div>';
                                            echo '<pre style="font-size:11px;margin:4px 0 0 0;padding:4px 8px;background:#f7fafc;">curl --silent '.$type_url.'</pre>';
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        
                                        echo '</div>';
                                        echo '</div>';
                                    }?>
                                </div>
                            </div>
                            <small class="help-block">只显示已添加的对接站点，如需添加请前往<a href="./shequlist.php">对接站点管理</a></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">同步间隔</label>
                        <div class="col-sm-10">
                            <div class="input-group" style="display: flex;">
                                <input type="number" name="sync_interval" class="form-control" value="300" min="0" max="86400" required placeholder="请输入同步间隔" style="flex: 1;">
                                <select name="sync_interval_unit" class="form-control" style="width: auto; min-width: 80px;">
                                    <option value="seconds">秒</option>
                                    <option value="minutes">分钟</option>
                                </select>
                            </div>
                            <small class="help-block">设置两次同步之间的最小时间间隔。建议设置300秒（5分钟）以上，以免对接站点压力过大</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">同步数量</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select name="sync_limit" class="form-control" required>
                                    <option value="10">10个/次</option>
                                    <option value="30">30个/次</option>
                                    <option value="50" selected>50个/次</option>
                                    <option value="100">100个/次</option>
                                    <option value="200">200个/次</option>
                                    <option value="500">500个/次</option>
                                    <option value="1000">1000个/次</option>
                                </select>
                                <span class="input-group-addon">建议50个/次</span>
                            </div>
                            <small class="help-block">每次同步的最大商品数量,数量越大同步越快,但也越容易超时,建议根据服务器性能调整</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" name="submit" value="保存" class="btn btn-primary form-control"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div></div>
<script>
function toggleSiteConfig(checkbox, siteId) {
    var $siteItem = $(checkbox).closest('.site-item');
    if($(checkbox).prop('checked')) {
        $siteItem.addClass('active');
        $('#config_' + siteId).addClass('show');
    } else {
        $siteItem.removeClass('active');
        $('#config_' + siteId).removeClass('show');
    }
    updateCount();
}

function checkAll(obj){
    var checked = $(obj).prop('checked');
    $("input[name='shequ_ids[]']").each(function(){
        $(this).prop('checked', checked);
        toggleSiteConfig(this, $(this).val());
    });
}

function copyUrl(btn, url) {
    var $input = $(btn).siblings('input');
    $input.select();
    document.execCommand('copy');
    var $btn = $(btn);
    $btn.html('<i class="fa fa-check"></i>');
    setTimeout(function(){ $btn.html('<i class="fa fa-copy"></i>'); }, 1500);
}

function updateCount(){
    var count = $("input[name='shequ_ids[]']:checked").length;
    $("#selected_count").text(count);
}

function save(){
    console.log('开始保存配置...');
    var submitBtn = $("input[type='submit']");
    submitBtn.attr("disabled",true);
    
    // 验证选中的站点数量 - 教主修改，添加前端验证
    var selectedCount = $("input[name='shequ_ids[]']:checked").length;
    console.log('选中的站点数量:', selectedCount);
    
    // 提取表单数据
    var formData = $("form").serialize() + '&submit=1';
    console.log('提交数据:', formData);
    
    // 安全检查：确保shequ_ids数组存在于表单数据中
    if(selectedCount > 0 && formData.indexOf('shequ_ids%5B%5D=') === -1) {
        console.error('表单数据中缺少站点ID');
        submitBtn.attr("disabled",false);
        layer.alert('表单数据异常，请刷新页面重试！', {icon: 5});
        return false;
    }
    
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    
    $.ajax({
        type : 'POST',
        url : window.location.href,
        data : formData,
        dataType : 'json',
        timeout: 30000, // 设置30秒超时
        success : function(data) {
            console.log('保存成功返回:', data);
            layer.close(ii);
            
            // 验证响应数据格式
            if(data && typeof data.code !== 'undefined') {
                if(data.code == 1){
                    layer.alert(data.msg || '保存成功！', {icon: 1}, function(){
                        window.location.reload();
                    });
                }else{
                    layer.alert(data.msg || '保存失败，请稍后重试！', {icon: 2});
                }
            } else {
                console.error('无效的响应格式:', data);
                layer.alert('服务器返回的数据格式错误，请检查服务器日志！', {icon: 2});
            }
            
            $("input[type='submit']").attr("disabled",false);
        },
        error:function(xhr, status, error){
            console.error('AJAX错误详情:');
            console.error('状态:', status);
            console.error('错误:', error);
            console.error('HTTP状态码:', xhr.status);
            console.error('响应文本长度:', xhr.responseText ? xhr.responseText.length : 0);
            
            // 尝试检查响应文本是否包含HTML
            var isHtmlResponse = xhr.responseText && /<[^>]+>/i.test(xhr.responseText);
            console.error('是否HTML响应:', isHtmlResponse);
            
            // 显示适当的错误信息
            var errorMessage = '保存失败，请稍后重试！';
            if(status === 'parsererror') {
                errorMessage += '\n错误类型: JSON解析错误';
                if(isHtmlResponse) {
                    errorMessage += '\n提示: 服务器可能返回了HTML错误页面而非JSON';
                }
            } else if(status === 'timeout') {
                errorMessage += '\n错误类型: 请求超时';
            } else if(status === 'error') {
                errorMessage += '\n错误类型: AJAX错误';
                if(xhr.status) {
                    errorMessage += '\nHTTP状态码: ' + xhr.status;
                    if(xhr.status === 500) {
                        errorMessage += ' (服务器内部错误)';
                    }
                }
            }
            
            // 记录到控制台的详细错误信息
            if(xhr.responseText && xhr.responseText.length <= 1000) {
                console.error('完整响应文本:', xhr.responseText);
            }
            
            layer.close(ii);
            layer.alert(errorMessage, {icon: 2});
            $("input[type='submit']").attr("disabled",false);
        }
    });
    return false;
}

function generateKey() {
    var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    var key = "";
    for(var i=0; i<32; i++) {
        key += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    $("input[name=\'monitor_key\']").val(key);
}

$(document).ready(function(){  
    console.log('页面加载开始');
    
    // 安全获取站点列表数据 - 教主修改：增强容错性
    var shequListData = <?php echo json_encode($shequlist ?? []); ?> || [];
    var enabledConfigsData = <?php echo json_encode($enabled_configs ?? []); ?> || {};
    
    console.log('站点列表数据:', shequListData);
    console.log('已启用配置:', enabledConfigsData);
    
    // 确保站点列表不为空 - 教主修改：增强容错性
    if (!Array.isArray(shequListData) || shequListData.length === 0) {
        console.warn('站点列表为空，添加默认示例站点');
        // 如果没有站点，添加一个默认站点用于显示
        $('.site-list').append(`
            <div class="site-item">
                <div class="site-item-header">
                    <label class="site-checkbox">
                        <input type="checkbox" name="shequ_ids[]" value="default">
                        <span class="site-title">默认示例站点</span>
                    </label>
                    <div class="site-url">请先添加实际站点</div>
                </div>
                <div class="site-config">
                    <div class="config-section">
                        <div class="config-title">提示</div>
                        <p>请先添加实际站点后再使用同步功能</p>
                    </div>
                </div>
            </div>
        `);
    }
    
    updateCount();
    // 加载已保存的配置 - 教主修改：使用更安全的方式
    var allConfigs = typeof enabledConfigsData === 'object' ? enabledConfigsData : {};
    console.log('加载配置完成');
    
    // 为每个配置执行初始化 - 教主修改：增强容错性
    $.each(allConfigs, function(shequId, config) {
        try {
            // 确保config对象存在
            if (!config) {
                console.warn(`站点 ${shequId} 配置为空`);
                return true;
            }
            
            var siteId = shequId;
            // 显示配置区域
            $("#config_" + siteId).show();
            
            // 选中站点复选框
            $("input[name='shequ_ids[]'][value='" + siteId + "']").prop("checked", true);
            
            // 设置下拉框值
            $("select[name='auto_update_" + siteId + "']").val(config.auto_update || 1);
            $("select[name='delete_rule_" + siteId + "']").val(config.delete_rule || 0);
            $("select[name='markup_template_" + siteId + "']").val(config.markup_template || 0);
            
            // 设置复选框状态 - 添加默认值处理
            $("input[name='sync_class_" + siteId + "']").prop("checked", config.sync_class == 1);
            $("input[name='sync_sort_" + siteId + "']").prop("checked", config.sync_sort == 1);
            $("input[name='sync_goods_sort_" + siteId + "']").prop("checked", config.sync_goods_sort == 1);
            $("input[name='sync_log_" + siteId + "']").prop("checked", config.sync_log == 1);
            $("input[name='sync_name_" + siteId + "']").prop("checked", config.sync_name == 1);
            $("input[name='sync_price_" + siteId + "']").prop("checked", config.sync_price == 1);
            $("input[name='sync_cost_" + siteId + "']").prop("checked", config.sync_cost == 1);
            $("input[name='sync_desc_" + siteId + "']").prop("checked", config.sync_desc == 1);
            $("input[name='sync_image_" + siteId + "']").prop("checked", config.sync_image == 1);
            $("input[name='sync_workorder_" + siteId + "']").prop("checked", config.sync_workorder == 1);
            $("input[name='add_class_" + siteId + "']").prop("checked", config.add_class == 1);
            $("input[name='add_goods_" + siteId + "']").prop("checked", config.add_goods == 1);
        } catch (err) {
            console.error(`初始化站点 ${shequId} 配置失败:`, err);
        }
    });
    
    // 设置公共配置 - 教主修改，使用更简洁的方式，添加默认值处理
    if(Object.keys(allConfigs).length > 0) {
        // 获取第一个配置用于设置公共参数
        var firstSiteId = Object.keys(allConfigs)[0];
        var firstConfig = allConfigs[firstSiteId];
        
        if(firstConfig) {
            // 数据库中保存的是秒数，判断是否显示为分钟
            var intervalSeconds = firstConfig.sync_interval || 300;
            var intervalUnit = 'seconds';
            // 如果是分钟的整数倍且大于等于60秒，显示为分钟
            if(intervalSeconds >= 60 && intervalSeconds % 60 === 0) {
                intervalSeconds = intervalSeconds / 60;
                intervalUnit = 'minutes';
            }
            $("input[name='sync_interval']").val(intervalSeconds);
            $("select[name='sync_interval_unit']").val(intervalUnit);
            $("select[name='sync_limit']").val(firstConfig.sync_limit || 50);
        }
    }
    
    // 更新选中站点数量
    updateCount();
    
    console.log('页面初始化完成');
});
</script>
<style>
/* 导航栏样式 */
.site-nav {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

.site-nav-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.site-nav-title {
    display: flex;
    align-items: center;
    gap: 16px;
}

.site-nav-title h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #1a202c;
}

.site-count {
    font-size: 14px;
    color: #64748b;
}

.site-count b {
    color: #3182ce;
}

.site-nav-actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.select-all {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-weight: normal;
    margin: 0;
}

.site-list {
    padding: 16px;
}

/* 站点项样式 */
.site-item {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 16px;
    transition: all 0.2s;
}

.site-item:hover {
    border-color: #90cdf4;
}

.site-item.active {
    border-color: #4299e1;
}

.site-item-header {
    padding: 16px 20px;
    cursor: pointer;
}

.site-checkbox {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    cursor: pointer;
    font-weight: normal;
}

.site-title {
    font-size: 15px;
    font-weight: 500;
    color: #2d3748;
}

.site-url {
    margin-top: 8px;
    font-size: 13px;
    color: #718096;
}

.site-remark {
    margin-top: 4px;
    font-size: 12px;
    color: #a0aec0;
}

/* 配置区域样式 */
.site-config {
    display: none;
    padding: 0 20px 20px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
}

.site-config.show {
    display: block;
}

.config-section {
    margin-top: 20px;
}

.config-title {
    font-size: 14px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
}

.config-title:before {
    content: "";
    display: inline-block;
    width: 4px;
    height: 16px;
    background: #4299e1;
    margin-right: 8px;
    border-radius: 2px;
}

.config-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.config-item {
    margin-bottom: 16px;
}

.config-item label {
    display: block;
    margin-bottom: 8px;
    color: #4a5568;
    font-size: 13px;
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 12px;
    margin-bottom: 8px;
}

.checkbox-item {
    position: relative;
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
    padding: 12px 16px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    margin: 0;
    font-weight: normal;
    min-height: 48px;
}

.checkbox-item:hover {
    border-color: #90cdf4;
    background: #ebf8ff;
}

.checkbox-item input[type="checkbox"] {
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    margin: 2px 8px 0 0;
}

.checkbox-item span {
    font-size: 13px;
    color: #2d3748;
    font-weight: 500;
    margin-right: 8px;
    white-space: nowrap;
}

.option-desc {
    font-size: 12px;
    color: #718096;
    margin-top: 4px;
    width: 100%;
    display: block;
    word-break: break-word;
}

/* 基础设置下拉框描述 */
.config-item select {
    margin-bottom: 4px;
}

.config-item .select-desc {
    font-size: 12px;
    color: #718096;
    display: block;
    margin-top: 4px;
}

/* 美化表单控件 */
.form-control {
    height: 36px;
    padding: 0 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    box-shadow: none;
    color: #4a5568;
    font-size: 14px;
    background-color: #fff;
    transition: all 0.2s;
}

.form-control:focus {
    border-color: #4299e1;
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
}

.form-control:hover {
    border-color: #cbd5e0;
}

/* 按钮样式 */
.btn-link {
    color: #4299e1;
    text-decoration: none;
    font-size: 14px;
}

.btn-link:hover {
    color: #2b6cb0;
    text-decoration: none;
}

.btn-link i {
    margin-right: 4px;
}

@media (max-width: 768px) {
    .site-nav-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .site-nav-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .config-row {
        grid-template-columns: 1fr;
    }
    
    .checkbox-group {
        grid-template-columns: 1fr;
    }
    
    .checkbox-item {
        padding: 10px 12px;
    }
    
    .site-item-header {
        padding: 12px 16px;
    }
    
    .site-config {
        padding: 0 16px 16px;
    }
    
    .col-sm-10 {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .control-label {
        margin-bottom: 8px;
    }
}
</style>
<?php include './foot.php';?>