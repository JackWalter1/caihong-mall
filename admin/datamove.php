<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/common.php");
$title = '数据库迁移工具';
include './head.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

class DatabaseMigration {
    private $oldDb;
    private $newDb;
    private $oldConfig;
    private $newConfig;
    private $logFile;
    
    public function __construct($oldConfig, $newConfig) {
        $this->oldConfig = $oldConfig;
        $this->newConfig = $newConfig;
        $this->logFile = __DIR__ . '/../migration_log_' . date('YmdHis') . '.log';
    }
    
    private function log($message) {
        $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage . "<br>";
        flush();
        ob_flush();
    }
    
    private function connect($config) {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
            return $pdo;
        } catch (PDOException $e) {
            $this->log("数据库连接失败: " . $e->getMessage());
            die("数据库连接失败: " . $e->getMessage());
        }
    }
    
    public function connectDatabases() {
        $this->log("开始连接数据库...");
        $this->oldDb = $this->connect($this->oldConfig);
        $this->newDb = $this->connect($this->newConfig);
        $this->log("数据库连接成功");
    }
    
    private function getTables($pdo) {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = [];
        while ($row = $stmt->fetch()) {
            $tables[] = array_values($row)[0];
        }
        return $tables;
    }
    
    private function getTableColumns($pdo, $table) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
            $columns = [];
            while ($row = $stmt->fetch()) {
                $columns[] = $row['Field'];
            }
            return $columns;
        } catch (PDOException $e) {
            return [];
        }
    }
    
    private function getTableColumnsInfo($pdo, $table) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
            $columns = [];
            while ($row = $stmt->fetch()) {
                $columns[$row['Field']] = [
                    'field' => $row['Field'],
                    'type' => $row['Type'],
                    'null' => $row['Null'],
                    'default' => $row['Default'],
                    'extra' => $row['Extra']
                ];
            }
            return $columns;
        } catch (PDOException $e) {
            return [];
        }
    }
    
    private function getDefaultValueForType($type, $field) {
        $type = strtolower($type);
        
        if (strpos($type, 'int') !== false || strpos($type, 'bigint') !== false || strpos($type, 'smallint') !== false || strpos($type, 'tinyint') !== false) {
            if (strpos($field, 'sid') !== false || strpos($field, 'id') !== false) {
                return 0;
            }
            return 0;
        }
        
        if (strpos($type, 'decimal') !== false || strpos($type, 'float') !== false || strpos($type, 'double') !== false) {
            return '0.00';
        }
        
        if (strpos($type, 'datetime') !== false || strpos($type, 'timestamp') !== false) {
            return '0000-00-00 00:00:00';
        }
        
        if (strpos($type, 'date') !== false) {
            return '0000-00-00';
        }
        
        return '';
    }
    
    private function getTableData($pdo, $table) {
        $stmt = $pdo->query("SELECT * FROM `{$table}`");
        return $stmt->fetchAll();
    }
    
    private function truncateTable($pdo, $table) {
        try {
            $pdo->exec("TRUNCATE TABLE `{$table}`");
            $this->log("清空表 {$table} 成功");
            return true;
        } catch (PDOException $e) {
            $this->log("清空表 {$table} 失败: " . $e->getMessage());
            return false;
        }
    }
    
    private function insertData($pdo, $table, $data) {
        if (empty($data)) {
            return true;
        }
        
        $columns = array_keys($data[0]);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO `{$table}` (`" . implode('`,`', $columns) . "`) VALUES (" . implode(',', $placeholders) . ")";
        
        try {
            $stmt = $pdo->prepare($sql);
            foreach ($data as $row) {
                $values = array_values($row);
                $stmt->execute($values);
            }
            return true;
        } catch (PDOException $e) {
            $this->log("插入数据到表 {$table} 失败: " . $e->getMessage());
            return false;
        }
    }
    
    private function migrateTable($table) {
        $this->log("开始迁移表: {$table}");
        
        $oldColumns = $this->getTableColumns($this->oldDb, $table);
        $newColumns = $this->getTableColumns($this->newDb, $table);
        $newColumnsInfo = $this->getTableColumnsInfo($this->newDb, $table);
        
        if (empty($newColumns)) {
            $this->log("新数据库中不存在表 {$table}，跳过迁移");
            return false;
        }
        
        $oldData = $this->getTableData($this->oldDb, $table);
        
        if (empty($oldData)) {
            $this->log("表 {$table} 无数据，跳过迁移");
            return true;
        }
        
        $this->log("表 {$table} 共有 " . count($oldData) . " 条数据");
        
        $migratedData = [];
        foreach ($oldData as $row) {
            $newRow = [];
            foreach ($newColumns as $column) {
                if (in_array($column, $oldColumns)) {
                    $value = $row[$column];
                    if ($value === null || $value === '') {
                        $colInfo = $newColumnsInfo[$column];
                        if ($colInfo['null'] === 'NO' && $colInfo['extra'] !== 'auto_increment') {
                            if ($colInfo['default'] !== null) {
                                $value = $colInfo['default'];
                            } else {
                                $value = $this->getDefaultValueForType($colInfo['type'], $column);
                            }
                        }
                    }
                    $newRow[$column] = $value;
                } else {
                    $colInfo = $newColumnsInfo[$column];
                    if ($colInfo['null'] === 'NO' && $colInfo['extra'] !== 'auto_increment') {
                        if ($colInfo['default'] !== null) {
                            $newRow[$column] = $colInfo['default'];
                        } else {
                            $newRow[$column] = $this->getDefaultValueForType($colInfo['type'], $column);
                        }
                    } else {
                        $newRow[$column] = null;
                    }
                }
            }
            $migratedData[] = $newRow;
        }
        
        if ($this->truncateTable($this->newDb, $table)) {
            if ($this->insertData($this->newDb, $table, $migratedData)) {
                $this->log("表 {$table} 迁移成功，共迁移 " . count($migratedData) . " 条数据");
                return true;
            }
        }
        
        return false;
    }
    
    private function createNewTables() {
        $this->log("检查并创建新版本数据库新增的表...");
        
        $newTables = [
            'shua_coupon_logs',
            'shua_coupon_rules', 
            'shua_coupons',
            'shua_site_price',
            'shua_user_coupons',
            'shuaarticle'
        ];
        
        $oldTables = $this->getTables($this->oldDb);
        
        foreach ($newTables as $table) {
            if (!in_array($table, $oldTables)) {
                $this->log("老版本数据库中不存在表 {$table}，需要从新版本数据库获取结构");
            }
        }
    }
    
    private function checkDataIntegrity() {
        $this->log("开始检查数据完整性...");
        
        $oldTables = $this->getTables($this->oldDb);
        $newTables = $this->getTables($this->newDb);
        
        $report = [];
        
        foreach ($oldTables as $table) {
            if (!in_array($table, $newTables)) {
                $report[] = "警告: 新数据库中不存在表 {$table}";
                continue;
            }
            
            $oldCount = $this->oldDb->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            $newCount = $this->newDb->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            
            if ($oldCount == $newCount) {
                $report[] = "表 {$table}: 数据一致 ({$oldCount} 条)";
            } else {
                $report[] = "警告: 表 {$table} 数据不一致 (老: {$oldCount}, 新: {$newCount})";
            }
        }
        
        return $report;
    }
    
    public function migrate() {
        $this->log("========================================");
        $this->log("开始数据库迁移");
        $this->log("========================================");
        
        $this->connectDatabases();
        $this->createNewTables();
        
        $oldTables = $this->getTables($this->oldDb);
        $this->log("老版本数据库共有 " . count($oldTables) . " 个表");
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($oldTables as $table) {
            if ($this->migrateTable($table)) {
                $successCount++;
            } else {
                $failCount++;
            }
        }
        
        $this->log("========================================");
        $this->log("数据迁移完成");
        $this->log("成功: {$successCount} 个表");
        $this->log("失败: {$failCount} 个表");
        $this->log("========================================");
        
        $this->log("开始数据完整性检查...");
        $report = $this->checkDataIntegrity();
        foreach ($report as $item) {
            $this->log($item);
        }
        
        $this->log("========================================");
        $this->log("迁移日志已保存到: {$this->logFile}");
        $this->log("========================================");
        
        return [
            'success' => $successCount,
            'fail' => $failCount,
            'report' => $report,
            'logFile' => $this->logFile
        ];
    }
}

$result = null;
$logContent = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldConfig = [
        'host' => $_POST['old_host'] ?? '127.0.0.1',
        'port' => $_POST['old_port'] ?? '3306',
        'database' => $_POST['old_database'] ?? '',
        'username' => $_POST['old_username'] ?? '',
        'password' => $_POST['old_password'] ?? ''
    ];
    
    $newConfig = [
        'host' => $_POST['new_host'] ?? '127.0.0.1',
        'port' => $_POST['new_port'] ?? '3306',
        'database' => $_POST['new_database'] ?? '',
        'username' => $_POST['new_username'] ?? '',
        'password' => $_POST['new_password'] ?? ''
    ];
    
    $migration = new DatabaseMigration($oldConfig, $newConfig);
    $result = $migration->migrate();
    
    if (file_exists($result['logFile'])) {
        $logContent = file_get_contents($result['logFile']);
    }
}
?>
<style>
.log-container {
    background-color: #272822;
    color: #f8f8f2;
    border: 1px solid #333;
    border-radius: 5px;
    padding: 15px;
    max-height: 400px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.6;
}
.log-container p {
    margin: 3px 0;
    padding: 2px 0;
}
.alert-info-custom {
    background-color: #d9edf7;
    border: 1px solid #bce8f1;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
    color: #31708f;
}
.alert-warning-custom {
    background-color: #fcf8e3;
    border: 1px solid #faebcc;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
    color: #8a6d3b;
}
</style>
<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-header">
            <h3 class="block-title">数据库迁移工具</h3>
        </div>
        <div class="block-content">
            <div class="alert alert-info">
                <strong>功能说明：</strong>
                <ul class="mb-0">
                    <li>首先在宝塔创建一个数据库，随便弄名字就可以，然后导入你的老数据库，或者你已有老版本数据库存在，那就不用创建。</li>
                    <li>新版本就直接输入你目前的数据库配置即可，注意的是你这个新版本不要导入老版本的数据库。</li>
                    <li>自动处理字段不匹配、数据格式差异等兼容性问题</li>
                    <li>确保数据完整性和准确性</li>
                    <li>生成详细的迁移日志</li>
                </ul>
            </div>
            
            <form method="POST">
                <h5 class="text-primary" style="margin-top: 20px; margin-bottom: 15px;">
                    <i class="fa fa-database"></i> 老版本数据库配置
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>主机地址</label>
                            <input type="text" class="form-control" name="old_host" value="127.0.0.1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>端口</label>
                            <input type="text" class="form-control" name="old_port" value="3306" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>数据库名</label>
                    <input type="text" class="form-control" name="old_database" placeholder="请输入老版本数据库名" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>用户名</label>
                            <input type="text" class="form-control" name="old_username" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>密码</label>
                            <input type="password" class="form-control" name="old_password" required>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h5 class="text-success" style="margin-top: 20px; margin-bottom: 15px;">
                    <i class="fa fa-database"></i> 新版本数据库配置
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>主机地址</label>
                            <input type="text" class="form-control" name="new_host" value="127.0.0.1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>端口</label>
                            <input type="text" class="form-control" name="new_port" value="3306" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>数据库名</label>
                    <input type="text" class="form-control" name="new_database" placeholder="请输入新版本数据库名" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>用户名</label>
                            <input type="text" class="form-control" name="new_username" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>密码</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="alert alert-warning">
                    <strong>注意事项：</strong>
                    <ul class="mb-0">
                        <li>迁移前请备份新版本数据库</li>
                        <li>迁移过程会清空新版本数据库中的数据</li>
                        <li>请确保两个数据库的连接信息正确</li>
                        <li>迁移过程中请勿关闭页面</li>
                    </ul>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg btn-block" onclick="return confirm('确定要开始迁移吗？此操作将清空新版本数据库中的所有数据！')">
                    <i class="fa fa-refresh"></i> 开始迁移
                </button>
            </form>
            
            <?php if (isset($result)): ?>
            <div style="margin-top: 30px;">
                <h5 class="text-primary">迁移日志</h5>
                <div class="log-container">
                    <?php
                    $logLines = explode("\n", $logContent);
                    foreach ($logLines as $line) {
                        if (!empty($line)) {
                            echo "<p>{$line}</p>";
                        }
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
