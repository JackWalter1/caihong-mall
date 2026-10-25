<?php
/* 
QQ群915043052
个人博客blog.6v6.ren
*/
//文件格式
header("Content-type: text/html; charset=utf-8");
//错误级别
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//初始化
ini_set('display_errors', '1');
//定义web根目录
define('WWW_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
// $runtimePath = str_replace(DIRECTORY_SEPARATOR . 'public', DIRECTORY_SEPARATOR . 'runtime', WWW_ROOT);
//定义后台名称
$config = [
    'siteName' => "WalTerTeChStuDio",
    'siteVersion' => "V4",
    'tablePrefix' => "shua"
];
//错误信息
$msg = '';
//安装文件
$lockFile = "./install.lock";
//数据库
$databaseConfigFile = "../config.php";

session_start();

// 判断文件或目录是否有写的权限
function is_really_writable($file)
{
    if (DIRECTORY_SEPARATOR === '/' and @ ini_get("safe_mode") == false) {
        return is_writable($file);
    }
    if (!is_file($file) or ($fp = @fopen($file, "r+")) === false) {
        return false;
    }
    fclose($fp);
    return true;
}
if (is_file($lockFile)) {
    $msg = "当前已经安装{$config['siteName']}，如果需要重新安装，请手动删除install/install.lock文件";
}
// 同意协议页面
if (@!isset($_GET['s']) || @$_GET['s'] === 'step1') {
    
    require_once './view/step1.html';
}
// 检测环境页面
if (@$_GET['s'] === 'step2') {
    if (version_compare(PHP_VERSION, '7.1.0', '<')) {
        $msg = "当前版本(" . PHP_VERSION . ")过低，请使用PHP7.1.0以上版本";
    } else {
        if (!extension_loaded("PDO")) {
            $msg = "当前未开启PDO，无法进行安装";
        } else {
            if (!is_really_writable($databaseConfigFile)) {
                $open_basedir = ini_get('open_basedir');
                if ($open_basedir) {
                    $dirArr = explode(PATH_SEPARATOR, $open_basedir);
                    if ($dirArr && in_array(__DIR__, $dirArr)) {
                        $msg = '当前服务器因配置了open_basedir，导致无法读取父目录<br>';
                    }
                }
                if (!$msg) {
                    $msg = '当前权限不足，无法写入配置文件config/database.php<br>';
                }
            }
        }
    }
    require_once './view/step2.html';
}
// 安装
if (@$_GET['s'] === 'step3') {
            if ($_GET['s'] === 'step3' && $_SERVER['REQUEST_METHOD'] === 'GET') require_once './view/step3.html';
            if ($_GET['s'] === 'step3' && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
                // 设置纯文本输出格式
                header('Content-Type: text/plain; charset=utf-8');
                if ($msg) {echo $msg;exit;}
                
                // 禁用输出缓冲，确保实时输出
                @ini_set('output_buffering', 'off');
                @ini_set('zlib.output_compression', false);
                @ini_set('implicit_flush', true);
                @ob_end_clean();
                
                //执行安装
                $host = isset($_POST['hostname']) ? $_POST['hostname'] : '127.0.0.1';
                $port = isset($_POST['port']) ? $_POST['port'] : '3306';
                //判断是否在主机头后面加上了端口号
                $hostData = explode(":", $host);
                if (isset($hostData) && $hostData && is_array($hostData) && count($hostData) > 1) {
                    $host = $hostData[0];
                    $port = $hostData[1];
                }
                //mysql的账户相关
                $mysqlUserName = isset($_POST['username']) ? $_POST['username'] : 'root';
                $mysqlPassword = isset($_POST['password']) ? $_POST['password'] : 'root';
                $mysqlDatabase = isset($_POST['database']) ? $_POST['database'] : 'pre';
                $mysqlPreFix = isset($_POST['prefix']) ? $_POST['prefix'] : $config['tablePrefix'];
                $mysqlPreFix = rtrim($mysqlPreFix);
                //php 版本
                if (version_compare(PHP_VERSION, '7.1.0', '<')) {
                    die("当前版本(" . PHP_VERSION . ")过低，请使用PHP7.1.0以上版本");
                }
                if (!extension_loaded("PDO")) {
                    die ("当前未开启PDO，无法进行安装");
                }
                //检测能否读取安装文件
                    $sqlFilePath = './database/install.sql';
                    if (!is_readable($sqlFilePath)) {
                        exit("progress:1|安装失败、因为无database/install.sql文件的读取权限！请前往宝塔面板将install目录权限设置为777|error");
                    }
                    $sql = @file_get_contents($sqlFilePath);
                    if (!$sql) {
                        exit("progress:1|安装失败、无法读取/database/install.sql文件，请检查文件是否存在或有读权限！请前往宝塔面板将install目录权限设置为777|error");
                    }

                /**
                 * 将SQL文件内容拆分为单条语句。
                 * 正确处理：多行块注释（含 SQLyog 导出头注释）、
                 * 单行注释 -- / #、可执行注释 以及字符串/反引号内的分号。
                 * @param string $sql
                 * @return array
                 */
                function splitSqlStatements($sql)
                {
                    $statements = array();
                    $buffer = '';
                    $len = strlen($sql);
                    $inSingle = false;
                    $inDouble = false;
                    $inBacktick = false;
                    $inLineComment = false;
                    $inBlockComment = false;

                    for ($i = 0; $i < $len; $i++) {
                        $c = $sql[$i];
                        $next = ($i + 1 < $len) ? $sql[$i + 1] : '';

                        if ($inLineComment) {
                            if ($c === "\n" || $c === "\r") $inLineComment = false;
                            continue;
                        }
                        if ($inBlockComment) {
                            if ($c === '*' && $next === '/') {
                                $inBlockComment = false;
                                $i++;
                            }
                            continue;
                        }
                        if ($inSingle) {
                            $buffer .= $c;
                            if ($c === '\\' && $next !== '') { $buffer .= $next; $i++; }
                            elseif ($c === "'") $inSingle = false;
                            continue;
                        }
                        if ($inDouble) {
                            $buffer .= $c;
                            if ($c === '\\' && $next !== '') { $buffer .= $next; $i++; }
                            elseif ($c === '"') $inDouble = false;
                            continue;
                        }
                        if ($inBacktick) {
                            $buffer .= $c;
                            if ($c === '`') $inBacktick = false;
                            continue;
                        }

                        if ($c === '-' && $next === '-') { $inLineComment = true; $i++; continue; }
                        if ($c === '#') { $inLineComment = true; continue; }
                        if ($c === '/' && $next === '*') {
                            if ($i + 2 < $len && $sql[$i + 2] === '!') {
                                // 可执行注释 /*! ... */ 原样保留（MySQL 会执行其中的语句）
                                $end = strpos($sql, '*/', $i + 3);
                                if ($end === false) $end = $len - 2;
                                $buffer .= substr($sql, $i, $end + 2 - $i);
                                $i = $end + 1;
                                continue;
                            }
                            $inBlockComment = true;
                            $i++;
                            continue;
                        }
                        if ($c === "'") { $inSingle = true; $buffer .= $c; continue; }
                        if ($c === '"') { $inDouble = true; $buffer .= $c; continue; }
                        if ($c === '`') { $inBacktick = true; $buffer .= $c; continue; }
                        if ($c === ';') {
                            $stmt = trim($buffer);
                            if ($stmt !== '') $statements[] = $stmt;
                            $buffer = '';
                            continue;
                        }
                        $buffer .= $c;
                    }
                    $stmt = trim($buffer);
                    if ($stmt !== '') $statements[] = $stmt;
                    return $statements;
                }

                try {
                    // 1. 连接数据库
                    echo "progress:1|正在连接数据库服务器...\n";
                    @ob_flush();
                    @flush();
                    
                    $link = @new mysqli("{$host}:{$port}", $mysqlUserName, $mysqlPassword);
                    $error = $link->connect_error;
                    if (!is_null($error)) {
                        // 转义防止和alert中的引号冲突
                        $error = addslashes($error);
                        
                        // 根据错误类型提供更友好的提示
                        if (stripos($error, 'Access denied') !== false) {
                            exit("progress:1|数据库连接失败：用户名或密码错误，请检查数据库账号和密码是否正确！\n错误详情：{$error}|error");
                        } elseif (stripos($error, 'Unknown MySQL server') !== false || stripos($error, 'Connection refused') !== false) {
                            exit("progress:1|数据库连接失败：数据库服务器地址或端口错误，请检查数据库主机地址和端口是否正确！\n错误详情：{$error}|error");
                        } elseif (stripos($error, 'Can\'t connect to MySQL server') !== false) {
                            exit("progress:1|数据库连接失败：无法连接到MySQL服务器，请检查MySQL服务是否运行以及网络连接是否正常！\n错误详情：{$error}|error");
                        } else {
                            exit("progress:1|数据库连接失败：请检查数据库配置信息！\n错误详情：{$error}|error");
                        }
                    }
                    if ($link->server_info < 5.5) {
                        exit("progress:1|MySQL数据库版本不能低于5.5,请将您的MySQL升级到5.5及以上|error");
                    }
                    
                    echo "progress:1|数据库连接成功|success\n";
                    @ob_flush();
                    @flush();
                    
                    // 2. 创建数据库并选中
                    echo "progress:2|正在检查并创建数据库...\n";
                    @ob_flush();
                    @flush();
                    
                    // 尝试选择数据库
                    if (!$link->select_db($mysqlDatabase)) {
                        echo "progress:2|数据库不存在，尝试创建数据库...\n";
                        @ob_flush();
                        @flush();
                        
                        // 创建数据库
                        $create_sql = 'CREATE DATABASE IF NOT EXISTS ' . $mysqlDatabase . ' DEFAULT CHARACTER SET utf8;';
                        if (!$link->query($create_sql)) {
                            $createError = $link->error;
                            // 根据错误类型提供具体提示
                            if (stripos($createError, 'Access denied') !== false) {
                                exit("progress:2|创建数据库失败：当前数据库用户没有创建数据库的权限，请使用具有创建数据库权限的账号！\n错误详情：{$createError}|error");
                            } else {
                                exit("progress:2|创建数据库失败：请检查数据库名称是否合法以及用户权限是否足够！\n错误详情：{$createError}|error");
                            }
                        }
                        
                        // 再次尝试选择创建的数据库
                        if (!$link->select_db($mysqlDatabase)) {
                            exit("progress:2|无法选择数据库：虽然数据库已创建成功，但无法切换到该数据库，请检查用户权限！|error");
                        }
                    }
                    $link->query("USE `{$mysqlDatabase}`");//使用数据库
                    
                    echo "progress:2|数据库准备完成|success\n";
                    @ob_flush();
                    @flush();
                    
                    // 3. 检查是否存在表
                    echo "progress:2.5|正在检查数据库中是否存在表...\n";
                    @ob_flush();
                    @flush();
                    
                    $result = $link->query("SHOW TABLES LIKE '{$mysqlPreFix}_%'");
                    $tableCount = $result->num_rows;
                    
                    if ($tableCount > 0 && !isset($_POST['force'])) {
                        exit("progress:2.5|表已存在|exists");
                    }
                    
                    if ($tableCount > 0 && isset($_POST['force'])) {
                        echo "progress:2.5|确认继续安装，开始清除已有数据...\n";
                        @ob_flush();
                        @flush();
                        
                        // 获取所有表名
                        $tables = [];
                        while ($row = $result->fetch_row()) {
                            $tables[] = $row[0];
                        }
                        
                        // 删除所有表
                        foreach ($tables as $table) {
                            $link->query("DROP TABLE IF EXISTS `{$table}`");
                        }
                        
                        echo "progress:2.5|已有数据清除完成，继续安装|success\n";
                        @ob_flush();
                        @flush();
                    } else {
                        echo "progress:2.5|数据库为空，继续安装|success\n";
                        @ob_flush();
                        @flush();
                    }
                    
                    // 写入数据库
                    $date = date("Y-m-d");
                    
                    // 3. 执行主安装SQL
                    echo "progress:3|正在执行主安装SQL文件...\n";
                    @ob_flush();
                    @flush();
                    
                    $sqlArr = splitSqlStatements(file_get_contents('./database/install.sql'));
                    $totalQueries = count($sqlArr);
                    $processedQueries = 0;
                    
                    foreach ($sqlArr as $stmt) {
                        // 替换数据表前缀（与客服表SQL保持一致）
                        $stmt = str_ireplace("`shua_", "`{$mysqlPreFix}_", $stmt);
                        $stmt = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $stmt);
                        try {
                            if (!$link->query($stmt)) {
                                exit("progress:3|执行SQL失败: " . $link->error . "|error");
                            }
                            $processedQueries++;
                            // 每处理10个查询更新一次进度信息
                            if ($processedQueries % 10 == 0) {
                                $progressPercent = min(30 + (($processedQueries / $totalQueries) * 20), 50); // 进度控制在30%-50%
                                $currentStep = 3 + (($processedQueries / $totalQueries) * 1); // 步骤3到4之间的小数进度
                                echo "progress:{$currentStep}|正在创建数据表... ({$processedQueries}/{$totalQueries})\n";
                                @ob_flush();
                                @flush();
                            }
                        } catch (\Exception $e) {
                            exit("progress:3|执行SQL失败: " . $e->getMessage() . "|error");
                        }
                    }
                    
                    echo "progress:4|主数据库结构创建完成|success\n";
                    @ob_flush();
                    @flush();
                    
                    // 4. 执行客服相关表SQL
                    echo "progress:5|正在创建客服相关表...\n";
                    @ob_flush();
                    @flush();
                    
                    $chatTablesFile = './database/chat_tables.sql';
                    if (file_exists($chatTablesFile)) {
                        //检查文件读取权限
                        if (!is_readable($chatTablesFile)) {
                            exit("progress:5|安装失败、因为无database/chat_tables.sql文件的读取权限！请前往宝塔面板将install目录权限设置为777|error");
                        }
                        $chatSqlArr = splitSqlStatements(file_get_contents($chatTablesFile));
                        foreach ($chatSqlArr as $chatSql) {
                            $chatSql = str_ireplace("`shua_", "`{$mysqlPreFix}_", $chatSql);
                            try {
                                $ok = $link->query($chatSql);
                                // 忽略“表已存在”错误
                                if ($ok === false && strpos($link->error, 'already exists') === false) {
                                    exit("progress:5|创建客服表失败: " . $link->error . "|error");
                                }
                            } catch (\Exception $e) {
                                if (strpos($e->getMessage(), 'already exists') === false) {
                                    exit("progress:5|创建客服表失败: " . $e->getMessage() . "|error");
                                }
                            }
                        }
                    }
                    
                    echo "progress:5|客服相关表创建完成|success\n";
                    @ob_flush();
                    @flush();

                    // 5. 插入系统配置
                    echo "progress:6|正在插入系统配置数据...\n";
                    @ob_flush();
                    @flush();
                    
	            $link->query("REPLACE INTO `{$mysqlPreFix}_config` VALUES ('build', '".$date."')");
                    $link->query("REPLACE INTO `{$mysqlPreFix}_config` VALUES ('syskey', '" . md5(uniqid() . time()) . "')");
                    $link->query("REPLACE INTO `{$mysqlPreFix}_config` VALUES ('cronkey', '" . mt_rand(100000,999999) . "')");
                    
                    echo "progress:6|系统配置数据插入完成|success\n";
                    @ob_flush();
                    @flush();
                    
                    // 6. 创建config.php配置文件
                    echo "progress:7|正在生成系统配置文件...\n";
                    @ob_flush();
                    @flush();
                    
                    //检查目录权限
                    $configDir = dirname($databaseConfigFile);
                    if (!is_writable($configDir)) {
                        exit("progress:7|安装失败、因为无config目录的写入权限！请前往宝塔面板将config目录权限设置为777|error");
                    }
                    
                    //替换数据库相关配置
                    $config= "<?php
\$dbconfig=array(
'host' => '{$host}', //数据库服务器
'port' => {$port}, //数据库端口
'user' => '{$mysqlUserName}', //数据库用户名
'pwd' => '{$mysqlPassword}', //数据库密码
'dbname' => '{$mysqlDatabase}', //数据库名
'dbqz' => '{$mysqlPreFix}', //表前缀
);";
                    $putConfig = @file_put_contents($databaseConfigFile, $config);
                    if (!$putConfig) {
                        exit("progress:7|安装失败、因为无config.php文件的写入权限！请前往宝塔面板将config目录权限设置为777|error");
                    }
                    
                    echo "progress:7|系统配置文件生成完成|success\n";
                    @ob_flush();
                    @flush();
                    
                    // 7. 创建install.lock锁文件
                    echo "progress:8|正在创建安装锁文件...\n";
                    @ob_flush();
                    @flush();
                    
                    //检查install目录权限
                    $installDir = dirname($lockFile);
                    if (!is_writable($installDir)) {
                        exit("progress:8|安装失败、因为无install目录的写入权限！请前往宝塔面板将install目录权限设置为777|error");
                    }
                    
                    $adminName = '';
                    $result = @file_put_contents($lockFile, 'ok');
                    if (!$result) {
                        exit("progress:8|安装失败、因为无install.lock文件的写入权限！请前往宝塔面板将install目录权限设置为777|error");
                    }
                    
                    echo "progress:8|安装锁文件创建完成|success\n";
                    @ob_flush();
                    @flush();
                    
                    // 完成安装
                    $_SESSION['admin'] = 'admin';
                    $_SESSION['password'] = '123456';
                    $_SESSION['backend'] = '';
                    echo $msg = 'success|' . $adminName;exit();
                } catch (\Exception $e) {
                    $errMsg = $e->getMessage();
                }
                echo "progress:0|" . $errMsg . "|error\n";
                exit();
    }
}
//完成安装
if (@$_GET['s'] === 'step4') {
    require_once './view/step4.html';
}

?>

