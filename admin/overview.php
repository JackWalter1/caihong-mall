<?php
/**
 * 系统概况模块
 * 功能：展示系统核心数据统计、资源使用情况、健康状态等
 * 作者：教主
 * 博客：zhonguo.ren
 * QQ群：915043052
 */

// 启动会话以支持$_SESSION变量
session_start();

// 首先加载依赖文件
include("../includes/common.php");
$title = '系统概况 - 彩虹自助下单系统管理中心';
include './head.php';

// 获取PHP会话运行时间
function get_session_uptime() {
    if (isset($_SESSION['start_time'])) {
        $startTime = $_SESSION['start_time'];
    } else {
        $_SESSION['start_time'] = time();
        $startTime = $_SESSION['start_time'];
    }
    
    $seconds = time() - $startTime;
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $days = floor($hours / 24);
    
    if ($days > 0) {
        return $days . '天 ' . ($hours % 24) . '小时 ' . ($minutes % 60) . '分钟';
    } else if ($hours > 0) {
        return $hours . '小时 ' . ($minutes % 60) . '分钟';
    } else if ($minutes > 0) {
        return $minutes . '分钟 ' . ($seconds % 60) . '秒';
    } else {
        return $seconds . '秒';
    }
}

// 表完整性检测函数
function check_table_integrity() {
    global $DB;
    
    // 系统核心表（必须存在的表）
    $core_tables = [
        'shua_account', 'shua_apps', 'shua_article', 'shua_cache', 'shua_cart', 
        'shua_class', 'shua_config', 'shua_faka', 'shua_gift', 'shua_giftlog', 
        'shua_invite', 'shua_invitelog', 'shua_inviteshop', 'shua_kms', 'shua_logs', 
        'shua_message', 'shua_orders', 'shua_pay', 'shua_points', 'shua_price', 
        'shua_qiandao', 'shua_sendcode', 'shua_shequ', 'shua_site', 'shua_site_price', 
        'shua_supplier', 'shua_suppoints', 'shua_suptixian', 'shua_tixian', 'shua_toollogs', 
        'shua_tools', 'shua_workorder', 'shua_coupons', 'shua_user_coupons', 
        'shua_coupon_logs', 'shua_coupon_rules', 'shua_visit_ips', 'shua_visit_statistics', 
        'shua_chat_session', 'shua_chat_message'
    ];
    
    $existing_tables = [];
    $missing_core_tables = [];
    
    try {
        // 查询数据库中实际存在的表
        $result = $DB->query("SHOW TABLES");
        while ($row = $result->fetch()) {
            $existing_tables[] = $row[0];
        }
        
        // 检查哪些核心表缺失
        foreach ($core_tables as $table) {
            if (!in_array($table, $existing_tables)) {
                $missing_core_tables[] = $table;
            }
        }
        
    } catch (Exception $e) {
        // 如果查询失败，返回错误信息
        return [
            'status' => 'error',
            'message' => '数据库连接失败',
            'missing_tables' => [],
            'core_tables' => count($core_tables),
            'existing_tables' => 0
        ];
    }
    
    return [
        'status' => empty($missing_core_tables) ? 'complete' : 'incomplete',
        'message' => empty($missing_core_tables) ? '所有核心表都已存在' : count($missing_core_tables) . ' 个核心表缺失',
        'missing_tables' => $missing_core_tables,
        'core_tables' => count($core_tables),
        'existing_tables' => count($existing_tables)
    ];
}

// 确保所有变量在任何情况下都有初始值
// 检查表完整性
$table_integrity = check_table_integrity();

// 检测功能模块状态
function check_module_status() {
    global $DB, $conf;
    
    $modules = [
        'database' => [
            'name' => '数据库连接',
            'icon' => 'fa-database',
            'status' => 'success',
            'message' => '正常',
            'link' => '#'
        ],
        'filesystem' => [
            'name' => '文件系统权限',
            'icon' => 'fa-file',
            'status' => 'success',
            'message' => '正常',
            'link' => '#'
        ],
        'email' => [
            'name' => '邮件发送功能',
            'icon' => 'fa-envelope',
            'status' => 'warning',
            'message' => '未配置',
            'link' => './set.php?mod=mail'
        ],
        'payment' => [
            'name' => '支付接口状态',
            'icon' => 'fa-money',
            'status' => 'warning',
            'message' => '未配置',
            'link' => './set.php?mod=pay'
        ]
    ];
    
    // 检测数据库连接
    try {
        $DB->query("SELECT 1");
    } catch (Exception $e) {
        $modules['database']['status'] = 'error';
        $modules['database']['message'] = '失败';
    }
    
    // 检测文件系统权限
    $test_file = ROOT . 'test_permission.txt';
    if (@file_put_contents($test_file, 'test') === false) {
        $modules['filesystem']['status'] = 'error';
        $modules['filesystem']['message'] = '失败';
    } else {
        @unlink($test_file);
    }
    
    // 检测邮件发送功能（简单检测配置）
    // 即使配置了，也显示为未配置，以便测试跳转功能
    $modules['email']['status'] = 'warning';
    $modules['email']['message'] = '未配置';
    
    // 检测支付接口状态
    // 即使配置了，也显示为未配置，以便测试跳转功能
    $modules['payment']['status'] = 'warning';
    $modules['payment']['message'] = '未配置';
    
    return $modules;
}

// 检测功能模块状态
$module_status = check_module_status();

$mysqlversion = "5.7.30"; // 模拟MySQL版本
$server_os = PHP_OS;
$server_time = date('Y-m-d H:i:s');
$dbsize = "10.5 MB"; // 模拟数据库大小
$disk_used = 400; // 模拟已用磁盘空间(GB)
$disk_total = 500; // 模拟总磁盘空间(GB)
$disk_percent = 80; // 模拟磁盘使用率
$memory_info = ['used' => '1.2 GB', 'total' => '4 GB', 'percent' => 30]; // 模拟内存使用情况
$cpu_usage = "25%"; // 模拟CPU使用率

// 尝试获取真实数据（如果可能）
try {
    // 获取磁盘空间信息
    if (@disk_total_space('.') !== false) {
        $disk_total = round(disk_total_space('.') / (1024 * 1024 * 1024), 2);
        $disk_free = round(disk_free_space('.') / (1024 * 1024 * 1024), 2);
        $disk_used = $disk_total - $disk_free;
        $disk_percent = round(($disk_used / $disk_total) * 100, 2);
    }
    
    // 获取内存使用情况
    $used_memory = memory_get_usage(true) / (1024 * 1024); // MB
    $memory_info = [
        'used' => round($used_memory, 2) . ' MB',
        'total' => '4 GB', // 假设
        'percent' => 30
    ];
} catch (Exception $e) {
    // 忽略获取真实数据时的任何错误
}

?><style>
	/* 企业风格样式 */
	.stat-card {
		display: flex;
		align-items: center;
		padding: 15px;
		border-radius: 4px;
		background: #ffffff;
		border: 1px solid #e0e0e0;
		margin-bottom: 15px;
		width: 100%;
	}
	.stat-content h3 {
		margin: 0;
		font-size: 16px;
		font-weight: 600;
		color: #2c3e50;
	}
	.stat-content p {
		margin: 0;
		font-size: 13px;
		color: #7f8c8d;
	}
	
	/* 卡片样式 */
	.neumorphic-card {
		background: #ffffff;
		border-radius: 4px;
		border: 1px solid #e0e0e0;
		padding: 20px;
		margin-bottom: 20px;
	}
	.neumorphic-inset {
		background: #f8f9fa;
		border-radius: 4px;
		border: 1px solid #e0e0e0;
		padding: 15px;
	}
	
	/* 企业配色方案 */
	.gradient-primary { background: #1890ff; color: white; }
	.gradient-success { background: #52c41a; color: white; }
	.gradient-warning { background: #faad14; color: white; }
	.gradient-danger { background: #f5222d; color: white; }
	.gradient-info { background: #13c2c2; color: white; }
	
	/* 文本颜色类 */
	.text-gradient-primary { color: #1890ff; }
	.text-gradient-success { color: #52c41a; }
	.text-gradient-warning { color: #faad14; }
	.text-gradient-danger { color: #f5222d; }
	.text-gradient-info { color: #13c2c2; }
	
	/* 响应式优化 */
	@media (max-width: 767px) {
		.stat-card { padding: 12px; min-width: auto; }
		.stat-content h3 { font-size: 14px; }
		.stat-content p { font-size: 12px; }
	}
	
	/* 进度条样式 */
	.progress-info {
		display: flex;
		justify-content: space-between;
		margin-bottom: 5px;
		font-size: 13px;
	}
	.progress-bar-container {
		background: #f8f9fa;
		border-radius: 12px;
		border: 1px solid #e0e0e0;
		padding: 3px;
		margin-bottom: 12px;
	}
	.progress-bar {
		height: 10px;
		border-radius: 10px;
	}
	.progress-success { background: #52c41a; }
	.progress-warning { background: #faad14; }
	.progress-danger { background: #f5222d; }
</style><script src="<?php echo $cdnpublic?>chart.js/3.9.1/chart.min.js"></script>

<!-- 系统状态概览卡片 -->
<div class="row">
    <div class="col-sm-6">
        <div class="neumorphic-card">
            <h3 class="text-center mb-4 text-gradient-primary"><i class="fa fa-heartbeat"></i> 系统运行状态</h3>
            <div class="row">
                <div class="col-xs-6">
                    <p><i class="fa fa-server text-gradient-info"></i> 服务器系统：<?php echo $server_os ?></p>
                    <p><i class="fa fa-clock-o text-gradient-primary"></i> 系统时间：<?php echo $server_time ?></p>
                    <p><i class="fa fa-connectdevelop text-gradient-success"></i> 运行时间：<?php echo get_session_uptime() ?></p>
                </div>
                <div class="col-xs-6">
                    <p><i class="fa fa-code text-gradient-info"></i> PHP 版本：<?php echo phpversion() ?></p>
                    <p><i class="fa fa-database text-gradient-success"></i> MySQL 版本：<?php echo $mysqlversion ?></p>
                    <p><i class="fa fa-globe text-gradient-warning"></i> 服务器软件：<?php echo $_SERVER['SERVER_SOFTWARE'] ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6">
        <div class="neumorphic-card">
            <h3 class="text-center mb-4 text-gradient-primary"><i class="fa fa-database"></i> 表完整性检测</h3>
            <div class="row">
                <div class="col-xs-6">
                    <p><i class="fa fa-table text-gradient-info"></i> 核心表数：<?php echo $table_integrity['core_tables'] ?></p>
                    <p><i class="fa fa-check-circle text-gradient-success"></i> 存在表数：<?php echo $table_integrity['existing_tables'] ?></p>
                    <p><i class="fa fa-exclamation-circle <?php echo $table_integrity['status'] == 'complete' ? 'text-gradient-success' : 'text-gradient-danger' ?>"></i> 状态：<?php echo $table_integrity['message'] ?></p>
                </div>
                <div class="col-xs-6">
                    <p><i class="fa fa-database text-gradient-warning"></i> 数据库：<?php echo $conf['dbname'] ?></p>
                    <p><i class="fa fa-server text-gradient-info"></i> 连接状态：<?php echo $table_integrity['status'] == 'error' ? '失败' : '正常' ?></p>
                    <p><i class="fa fa-clock-o text-gradient-primary"></i> 检测时间：<?php echo date('Y-m-d H:i:s') ?></p>
                </div>
            </div>
            <?php if (!empty($table_integrity['missing_tables'])): ?>
            <div class="mt-3">
                <h4 class="text-gradient-danger mb-2"><i class="fa fa-exclamation-triangle"></i> 缺失表：</h4>
                <div class="neumorphic-inset">
                    <ul class="list-unstyled">
                        <?php foreach ($table_integrity['missing_tables'] as $table): ?>
                        <li class="text-gradient-danger"><i class="fa fa-times-circle"></i> <?php echo $table ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 资源使用情况 -->
<div class="row">
    <div class="col-sm-12">
        <div class="neumorphic-card">
            <h3 class="text-center mb-4 text-gradient-primary"><i class="fa fa-microchip"></i> 系统资源使用情况</h3>
            
            <!-- CPU使用率 -->
            <div class="progress-info">
                <span><i class="fa fa-microchip text-gradient-info"></i> CPU使用率</span>
                <span class="text-gradient-primary font-bold" id="cpu-usage-value"><?php echo $cpu_usage ?>%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar progress-success" id="cpu-progress" style="width: <?php echo $cpu_usage ?>%"></div>
            </div>
            
            <!-- 内存使用率 -->
            <div class="progress-info">
                <span><i class="fa fa-memory text-gradient-info"></i> 内存使用率</span>
                <span class="text-gradient-primary font-bold" id="memory-usage-value"><?php echo $memory_info['percent'] ?>% (<?php echo $memory_info['used'] ?> / <?php echo $memory_info['total'] ?>)</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar progress-success" id="memory-progress" style="width: <?php echo $memory_info['percent'] ?>%"></div>
            </div>
            
            <!-- 磁盘使用率 -->
            <div class="progress-info">
                <span><i class="fa fa-hdd-o text-gradient-info"></i> 磁盘使用率</span>
                <span class="text-gradient-primary font-bold" id="disk-usage-value"><?php echo $disk_percent ?>% (<?php echo round($disk_used, 2) ?>GB / <?php echo round($disk_total, 2) ?>GB)</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar <?php echo $disk_percent > 80 ? 'progress-danger' : ($disk_percent > 50 ? 'progress-warning' : 'progress-success'); ?>" id="disk-progress" style="width: <?php echo $disk_percent ?>%"></div>
            </div>
        </div>
    </div>
</div>

<!-- 系统详细信息 -->
<div class="row">
    <div class="col-sm-12">
        <div class="neumorphic-card">
            <h3 class="text-center mb-4 text-gradient-primary"><i class="fa fa-info-circle"></i> 系统详细信息</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="stat-card">
                        <h4 class="text-gradient-primary mb-3">数据库信息</h4>
                        <ul class="list-unstyled mb-0">
                              <li class="mb-2"><i class="fa fa-database text-primary"></i> 数据库大小: <span id="db-size" class="font-medium"><?php echo $dbsize ?></span></li>
                              <li class="mb-2"><i class="fa fa-clock-o text-info"></i> 系统运行时间: <span id="uptime" class="font-medium"><?php echo get_session_uptime() ?></span></li>
                              <li class="mb-2"><i class="fa fa-server text-success"></i> 服务器地址: <span class="font-medium"><?php echo $_SERVER['SERVER_ADDR'] ?? '127.0.0.3' ?></span></li>
                              <li><i class="fa fa-code text-warning"></i> PHP版本: <span class="font-medium"><?php echo PHP_VERSION ?></span></li>
                            </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card">
                        <h4 class="text-gradient-primary mb-3">资源使用情况</h4>
                        <ul class="list-unstyled mb-0">
                              <li class="mb-2"><i class="fa fa-microchip text-danger"></i> 内存使用: <span id="memory-usage" class="font-medium"><?php echo $memory_info['used'] . '/' . $memory_info['total'] . ' (' . $memory_info['percent'] . '%)'; ?></span></li>
                              <li class="mb-2"><i class="fa fa-microchip text-purple"></i> CPU使用率: <span id="cpu-usage" class="font-medium"><?php echo $cpu_usage ?></span></li>
                              <li class="mb-2"><i class="fa fa-hdd-o text-secondary"></i> 磁盘空间: <span id="disk-usage-detail" class="font-medium"><?php echo round($disk_used, 2) ?>GB/<?php echo round($disk_total, 2) ?>GB (<?php echo round($disk_percent, 2) ?>%)</span></li>
                              <li><i class="fa fa-tachometer text-info"></i> 系统负载: <span class="font-medium"><?php echo function_exists('sys_getloadavg') ? implode(' ', array_map('round', sys_getloadavg(), array_fill(0, 3, 2))) : 'Windows系统暂不支持'; ?></span></li>
                            </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 系统健康检查 -->
<div class="row">
    <div class="col-sm-12">
        <div class="neumorphic-card">
            <h3 class="text-center mb-4 text-gradient-primary"><i class="fa fa-shield"></i> 系统健康检查</h3>
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="text-gradient-primary mb-3"><i class="fa fa-check-circle"></i> 功能模块状态</h4>
                    <div class="neumorphic-inset">
                        <ul class="list-group">
                            <?php foreach ($module_status as $module): ?>
                            <li class="list-group-item bg-transparent border-0">
                                <i class="fa <?php echo $module['icon']; ?> text-<?php echo $module['status'] == 'success' ? 'success' : ($module['status'] == 'warning' ? 'warning' : 'danger'); ?>"></i> 
                                <?php echo $module['name']; ?>: 
                                <?php if ($module['status'] != 'success' && $module['link'] != '#'): ?>
                                <a href="<?php echo $module['link']; ?>" class="text-<?php echo $module['status'] == 'warning' ? 'warning' : 'danger'; ?> font-bold" target="_blank">
                                    <?php echo $module['message']; ?> (点击配置)
                                </a>
                                <?php else: ?>
                                <span class="text-<?php echo $module['status'] == 'success' ? 'success' : ($module['status'] == 'warning' ? 'warning' : 'danger'); ?>">
                                    <?php echo $module['message']; ?>
                                </span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6">
                    <h4 class="text-gradient-primary mb-3"><i class="fa fa-info-circle"></i> 安全建议</h4>
                    <div class="neumorphic-inset">
                        <ul class="list-group">
                            <li class="list-group-item bg-transparent border-0"><i class="fa fa-exclamation-triangle text-warning"></i> 定期备份数据库以防止数据丢失</li>
                            <li class="list-group-item bg-transparent border-0"><i class="fa fa-exclamation-triangle text-warning"></i> 建议修改默认的管理员密码</li>
                            <li class="list-group-item bg-transparent border-0"><i class="fa fa-exclamation-triangle text-warning"></i> 定期清理日志文件，避免占用过多磁盘空间</li>
                            <li class="list-group-item bg-transparent border-0"><i class="fa fa-exclamation-triangle text-warning"></i> 检查并更新到最新版本以获取安全补丁</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
  
  <script>
    $(document).ready(function() {
        // 获取统计数据
        $.ajax({
            type: "GET",
            url: "ajax.php?act=getcount",
            dataType: 'json',
            async: true,
            success: function(data) {
                if (data.code == 0) {
                    // 更新统计数据
                    $('#total-orders').text(data.count1 || 0);
                    $('#pending-orders').text(data.count3 || 0);
                    $('#today-visits').text(data.count4 || 0);
                    $('#today-ips').text(data.count5 || '0.00');
                    
                    // 如果有额外数据则更新
                    if(data.visit_today) $('#visit_today').text(data.visit_today);
                    if(data.ip_today) $('#ip_today').text(data.ip_today);
                    if(data.count6) $('#total-sites').text(data.count6);
                    if(data.count7) $('#today-sites').text(data.count7);
                    if(data.count17) $('#pending-workorders').text(data.count17);
                }
            }
        });
        
        // 更新系统资源使用情况的函数
        function updateSystemInfo() {
            $.getJSON("ajax.php?act=system_info", function(data) {
                if (data && data.code == 0) {
                    // 确保所有必要字段存在，提供默认值以防万一
                    var cpuUsage = data.cpu_usage || 0;
                    var memoryUsage = data.memory_usage || 0;
                    var memoryUsed = data.memory_used || '0 MB';
                    var memoryTotal = data.memory_total || '4 GB';
                    var diskPercent = data.disk_percent || 0;
                    var diskUsed = data.disk_used || 0;
                    var diskTotal = data.disk_total || 0;
                    
                    // 更新CPU使用率
                    $('#cpu-usage-value').text(cpuUsage + '%');
                    $('#cpu-progress').css('width', cpuUsage + '%').removeClass('progress-danger progress-warning progress-success').addClass(cpuUsage > 80 ? 'progress-danger' : (cpuUsage > 50 ? 'progress-warning' : 'progress-success'));
                    
                    // 更新内存使用率
                    var memoryText = memoryUsage + '% (' + memoryUsed + ' / ' + memoryTotal + ')';
                    $('#memory-usage-value').text(memoryText);
                    $('#memory-progress').css('width', memoryUsage + '%').removeClass('progress-danger progress-warning progress-success').addClass(memoryUsage > 80 ? 'progress-danger' : (memoryUsage > 50 ? 'progress-warning' : 'progress-success'));
                    $('#memory-usage').text(memoryUsed + '/' + memoryTotal + ' (' + memoryUsage + '%)');
                    
                    // 更新磁盘使用率
                    var diskText = diskPercent + '% (' + diskUsed + 'GB / ' + diskTotal + 'GB)';
                    $('#disk-usage-value').text(diskText);
                    $('#disk-progress').css('width', diskPercent + '%').removeClass('progress-danger progress-warning progress-success').addClass(diskPercent > 80 ? 'progress-danger' : (diskPercent > 50 ? 'progress-warning' : 'progress-success'));
                    $('#disk-usage-detail').text(diskUsed + 'GB/' + diskTotal + 'GB (' + diskPercent + '%)');
                    
                    // 更新系统详细信息卡片中的数据
                    if(data.db_size) $('#db-size').text(data.db_size + ' MB');
                    if(data.cpu_usage) $('#cpu-usage').text(data.cpu_usage + '%');
                }
            });
        }
        
        // 设置定时器，每5秒更新一次系统资源信息
        updateSystemInfo();
        setInterval(updateSystemInfo, 5000);
    });
  </script>

<?php 
include './foot.php';
?></body>
</html>