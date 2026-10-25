<?php
include("../includes/common.php");

if($islogin != 1) {
    exit(json_encode(['success' => false, 'message' => '未登录']));
}

header('Content-Type: application/json; charset=UTF-8');

$action = $_REQUEST['action'] ?? '';

try {
    switch($action) {
        case 'get_ads':
            getAds();
            break;
        case 'get_updates':
            getUpdates();
            break;
        case 'clear_cache':
            clearCache();
            break;
        case 'cache_stats':
            getCacheStats();
            break;
        case 'check_permissions':
            checkUpdatePermissions();
            break;
        case 'download_update':
            downloadUpdate();
            break;
        case 'install_update':
            installUpdate();
            break;
        default:
            exit(json_encode([
                'success' => false, 
                'message' => '未知操作: ' . $action,
                'debug_info' => [
                    'received_action' => $action,
                    'request_method' => $_SERVER['REQUEST_METHOD'],
                    'get_params' => $_GET,
                    'post_params' => array_keys($_POST)
                ]
            ]));
    }
} catch(Exception $e) {
    exit(json_encode([
        'success' => false, 
        'message' => '请求失败: ' . $e->getMessage(),
        'debug_info' => [
            'action' => $action,
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]));
}

function makeApiRequest($url, $timeout = 3) {
    // 优化HTTP上下文配置
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => $timeout,
            'user_agent' => 'AdminPanel/1.0',
            'ignore_errors' => true,
            'follow_location' => 1,
            'max_redirects' => 3,
            'header' => [
                'Connection: close',
                'Accept: application/json,text/plain,*/*',
                'Cache-Control: no-cache'
            ]
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
            'SNI_enabled' => true
        ]
    ]);
    
    // 记录开始时间用于调试
    $start_time = microtime(true);
    $response = @file_get_contents($url, false, $context);
    $end_time = microtime(true);
    
    // 记录请求时间（用于调试）
    $request_time = round(($end_time - $start_time) * 1000, 2);
    
    if($response === false) {
        // 获取更详细的错误信息
        $error = error_get_last();
        $error_msg = $error ? $error['message'] : '网络连接失败';
        throw new Exception("API请求失败: {$error_msg} (耗时: {$request_time}ms)");
    }
    
    return $response;
}

function getAds() {
    // 降低清理频率，只有1%概率执行
    if(rand(1, 100) === 1) {
        cleanupExpiredImages();
    }
    
    // 检查缓存
    $cache_file = ROOT . 'cache/ads_cache.json';
    $cache_time = 300; // 5分钟缓存
    
    if(file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        $cached_data = file_get_contents($cache_file);
        if($cached_data) {
            $ads = json_decode($cached_data, true);
            if(is_array($ads)) {
                // 异步处理图片缓存
                asyncCacheImages($ads);
                exit(json_encode(['success' => true, 'data' => $ads, 'from_cache' => true]));
            }
        }
    }
    
    $api_url = AUTH_API_BASE . 'api/content.php';
    
    try {
        $response = makeApiRequest($api_url, 5); // 增加到5秒超时
        $ads = json_decode($response, true);
        
        if($ads === null) {
            throw new Exception('数据格式错误');
        }
        
        if(!is_array($ads)) {
            throw new Exception('数据类型错误');
        }
        
        // 快速处理图片路径，不同步下载
        foreach($ads as &$ad) {
            if($ad['type'] === 'image' && !empty($ad['image_path'])) {
                // 如果不是完整的URL，则拼接AUTH_API_BASE
                if(!preg_match('/^https?:\/\//', $ad['image_path'])) {
                    $ad['image_path'] = AUTH_API_BASE . ltrim($ad['image_path'], './');
                }
                
                // 检查本地缓存是否存在，不强制下载
                $ad['local_image_path'] = checkLocalImageCache($ad['image_path'], $ad['id']);
            }
        }
        
        // 保存到缓存
        if(!is_dir(dirname($cache_file))) {
            @mkdir(dirname($cache_file), 0755, true);
        }
        file_put_contents($cache_file, json_encode($ads));
        
        // 异步处理图片缓存
        asyncCacheImages($ads);
        
        exit(json_encode(['success' => true, 'data' => $ads, 'from_cache' => false]));
        
    } catch(Exception $e) {
        // 如果API失败，尝试返回过期的缓存数据
        if(file_exists($cache_file)) {
            $cached_data = file_get_contents($cache_file);
            if($cached_data) {
                $ads = json_decode($cached_data, true);
                if(is_array($ads)) {
                    exit(json_encode(['success' => true, 'data' => $ads, 'from_cache' => true, 'cache_expired' => true]));
                }
            }
        }
        
        exit(json_encode(['success' => false, 'message' => '广告数据暂时无法获取，请稍后重试']));
    }
}

function getUpdates() {
    // 检查缓存
    $cache_file = ROOT . 'cache/updates_cache.json';
    $cache_time = 600; // 10分钟缓存，更新不频繁
    
    if(file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        $cached_data = file_get_contents($cache_file);
        if($cached_data) {
            $updates = json_decode($cached_data, true);
            if(is_array($updates)) {
                exit(json_encode(['success' => true, 'data' => $updates, 'from_cache' => true]));
            }
        }
    }
    
    $api_url = AUTH_API_BASE . 'api/update.php';
    
    try {
        $response = makeApiRequest($api_url, 3); // 3秒超时
        $updates = json_decode($response, true);
        
        if($updates === null) {
            throw new Exception('数据格式错误');
        }
        
        if(!is_array($updates)) {
            throw new Exception('数据类型错误');
        }
        
        // 处理文件路径
        foreach($updates as &$update) {
            if(!empty($update['file_path'])) {
                // 如果不是完整的URL，则拼接AUTH_API_BASE
                if(!preg_match('/^https?:\/\//', $update['file_path'])) {
                    $update['file_path'] = AUTH_API_BASE . ltrim($update['file_path'], './');
                }
            }
        }
        
        // 保存到缓存
        if(!is_dir(dirname($cache_file))) {
            @mkdir(dirname($cache_file), 0755, true);
        }
        file_put_contents($cache_file, json_encode($updates));
        
        exit(json_encode(['success' => true, 'data' => $updates, 'from_cache' => false]));
        
    } catch(Exception $e) {
        // 如果API失败，尝试返回过期的缓存数据
        if(file_exists($cache_file)) {
            $cached_data = file_get_contents($cache_file);
            if($cached_data) {
                $updates = json_decode($cached_data, true);
                if(is_array($updates)) {
                    exit(json_encode(['success' => true, 'data' => $updates, 'from_cache' => true, 'cache_expired' => true]));
                }
            }
        }
        
        exit(json_encode(['success' => false, 'message' => '更新数据暂时无法获取，请稍后重试']));
    }
}

// 快速检查本地图片缓存
function checkLocalImageCache($image_url, $ad_id) {
    $cache_dir = ROOT . 'cache/images/';
    $extension = pathinfo(parse_url($image_url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
    $local_filename = 'ad_' . $ad_id . '_' . md5($image_url) . '.' . $extension;
    $local_path = $cache_dir . $local_filename;
    $relative_path = 'cache/images/' . $local_filename;
    
    // 如果本地文件存在且有效（7天内），返回本地路径
    if(file_exists($local_path) && (time() - filemtime($local_path)) < 604800) {
        return $relative_path;
    }
    
    // 否则返回原始URL
    return $image_url;
}

// 异步缓存图片（不阻塞响应）
function asyncCacheImages($ads) {
    // 只在1%的概率下触发图片缓存，避免每次都执行
    if(rand(1, 100) !== 1) {
        return;
    }
    
    foreach($ads as $ad) {
        if($ad['type'] === 'image' && !empty($ad['image_path'])) {
            // 检查是否需要缓存
            $cache_dir = ROOT . 'cache/images/';
            $extension = pathinfo(parse_url($ad['image_path'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $local_filename = 'ad_' . $ad['id'] . '_' . md5($ad['image_path']) . '.' . $extension;
            $local_path = $cache_dir . $local_filename;
            
            // 如果本地文件不存在或已过期，则缓存
            if(!file_exists($local_path) || (time() - filemtime($local_path)) > 604800) {
                cacheImageLocally($ad['image_path'], $ad['id']);
            }
        }
    }
}

function cacheImageLocally($image_url, $ad_id) {
    // 创建图片缓存目录
    $cache_dir = ROOT . 'cache/images/';
    if(!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }
    
    // 生成本地文件名
    $extension = pathinfo(parse_url($image_url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
    $local_filename = 'ad_' . $ad_id . '_' . md5($image_url) . '.' . $extension;
    $local_path = $cache_dir . $local_filename;
    $relative_path = 'cache/images/' . $local_filename;
    
    // 检查本地文件是否存在且有效（7天内）
    if(file_exists($local_path) && (time() - filemtime($local_path)) < 604800) {
        return $relative_path;
    }
    
    // 下载图片，减少超时时间
    try {
        $context = stream_context_create([
            'http' => [
                'timeout' => 3, // 从8秒减少到3秒
                'user_agent' => 'AdminPanel/1.0',
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        
        $image_data = @file_get_contents($image_url, false, $context);
        
        if($image_data && strlen($image_data) > 100) {
            // 验证是有效图片
            if(@getimagesizefromstring($image_data)) {
                file_put_contents($local_path, $image_data);
                return $relative_path;
            }
        }
    } catch(Exception $e) {
        // 静默处理错误
    }
    
    // 下载失败返回原始URL
    return $image_url;
}

function clearCache() {
    $type = $_POST['type'] ?? '';
    $cleared = 0;
    
    if($type === 'ads') {
        $cache_file = ROOT . 'cache/ads_cache.json';
        if(file_exists($cache_file)) {
            unlink($cache_file);
            $cleared++;
        }
        // 清除广告图片缓存
        $cleared += clearImageCache();
    } elseif($type === 'updates') {
        $cache_file = ROOT . 'cache/updates_cache.json';
        if(file_exists($cache_file)) {
            unlink($cache_file);
            $cleared++;
        }
    } else {
        // 清除所有缓存
        $cache_files = [
            ROOT . 'cache/ads_cache.json',
            ROOT . 'cache/updates_cache.json'
        ];
        
        foreach($cache_files as $file) {
            if(file_exists($file)) {
                unlink($file);
                $cleared++;
            }
        }
        
        // 清除图片缓存
        $cleared += clearImageCache();
    }
    
    exit(json_encode(['success' => true, 'message' => '缓存已清除', 'cleared' => $cleared]));
}

function clearImageCache() {
    $cache_dir = ROOT . 'cache/images/';
    $cleared = 0;
    
    if(is_dir($cache_dir)) {
        $files = glob($cache_dir . 'ad_*');
        foreach($files as $file) {
            if(is_file($file)) {
                unlink($file);
                $cleared++;
            }
        }
    }
    
    return $cleared;
}

function cleanupExpiredImages() {
    $cache_dir = ROOT . 'cache/images/';
    
    if(is_dir($cache_dir)) {
        $files = glob($cache_dir . 'ad_*');
        foreach($files as $file) {
            if(is_file($file) && (time() - filemtime($file)) > 604800) {
                @unlink($file);
            }
        }
    }
}

function getCacheStats() {
    $stats = [];
    
    // 广告缓存
    $ads_cache_file = ROOT . 'cache/ads_cache.json';
    $stats['ads_cache'] = file_exists($ads_cache_file);
    
    // 更新缓存
    $updates_cache_file = ROOT . 'cache/updates_cache.json';
    $stats['updates_cache'] = file_exists($updates_cache_file);
    
    // 图片缓存数量
    $cache_dir = ROOT . 'cache/images/';
    $stats['image_count'] = is_dir($cache_dir) ? count(glob($cache_dir . 'ad_*')) : 0;
    
    exit(json_encode(['success' => true, 'data' => $stats]));
}

// 检查更新权限
function checkUpdatePermissions() {
    $current_version = file_exists(ROOT . 'admin/version') ? trim(file_get_contents(ROOT . 'admin/version')) : '未知';
    
    $check_dirs = [
        ROOT,                    // 网站根目录
        ROOT . 'admin/',         // 管理目录
        ROOT . 'includes/',      // 包含文件目录
        ROOT . 'template/',      // 模板目录
        ROOT . 'assets/',        // 资源目录
        ROOT . 'temp/',          // 临时目录
        ROOT . 'cache/',         // 缓存目录
    ];
    
    $permission_results = [];
    $has_permission_issues = false;
    
    foreach($check_dirs as $dir) {
        $dir_name = str_replace(ROOT, '', $dir);
        $dir_name = $dir_name ? $dir_name : '/';
        
        $result = [
            'path' => $dir_name,
            'exists' => is_dir($dir),
            'readable' => false,
            'writable' => false,
            'permissions' => '',
            'owner' => '',
            'group' => ''
        ];
        
        if($result['exists']) {
            $result['readable'] = is_readable($dir);
            $result['writable'] = is_writable($dir);
            
            // 获取权限信息
            $perms = fileperms($dir);
            $result['permissions'] = substr(sprintf('%o', $perms), -4);
            
            // 尝试获取所有者信息
            if(function_exists('posix_getpwuid') && function_exists('fileowner')) {
                $owner_uid = fileowner($dir);
                $owner_info = posix_getpwuid($owner_uid);
                $result['owner'] = $owner_info ? $owner_info['name'] : $owner_uid;
            }
            
            if(function_exists('posix_getgrgid') && function_exists('filegroup')) {
                $group_gid = filegroup($dir);
                $group_info = posix_getgrgid($group_gid);
                $result['group'] = $group_info ? $group_info['name'] : $group_gid;
            }
            
            // 检查是否有权限问题
            if(!$result['writable']) {
                $has_permission_issues = true;
            }
        } else {
            $has_permission_issues = true;
        }
        
        $permission_results[] = $result;
    }
    
    // 检查PHP扩展
    $extensions = [
        'zip' => extension_loaded('zip'),
        'curl' => extension_loaded('curl'),
        'fileinfo' => extension_loaded('fileinfo')
    ];
    
    // 检查可用磁盘空间（至少需要100MB）
    $free_space = disk_free_space(ROOT);
    $space_sufficient = $free_space > 100 * 1024 * 1024;
    
    exit(json_encode([
        'success' => true,
        'data' => [
            'current_version' => $current_version,
            'has_permission_issues' => $has_permission_issues,
            'directories' => $permission_results,
            'extensions' => $extensions,
            'free_space' => $free_space,
            'space_sufficient' => $space_sufficient,
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ]
    ]));
}

// 下载更新包
function downloadUpdate() {
    $update_url = $_POST['update_url'] ?? '';
    $version = $_POST['version'] ?? '';
    
    if(empty($update_url) || empty($version)) {
        exit(json_encode(['success' => false, 'message' => '缺少必要参数']));
    }
    
    // 创建临时目录
    $temp_dir = ROOT . 'temp/updates/';
    if(!is_dir($temp_dir)) {
        if(!@mkdir($temp_dir, 0755, true)) {
            exit(json_encode(['success' => false, 'message' => '无法创建临时目录，请检查权限']));
        }
    }
    
    $temp_file = $temp_dir . 'update_' . $version . '_' . time() . '.zip';
    
    try {
        // 下载文件
        $context = stream_context_create([
            'http' => [
                'timeout' => 300, // 5分钟超时
                'user_agent' => 'AdminPanel/1.0 UpdateDownloader',
                'follow_location' => 1,
                'max_redirects' => 5
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        
        $file_data = @file_get_contents($update_url, false, $context);
        
        if($file_data === false) {
            throw new Exception('下载失败，请检查网络连接');
        }
        
        if(strlen($file_data) < 1024) {
            throw new Exception('下载的文件太小，可能不是有效的更新包');
        }
        
        // 保存文件
        if(!file_put_contents($temp_file, $file_data)) {
            throw new Exception('保存更新包失败，请检查磁盘空间和权限');
        }
        
        // 验证ZIP文件
        if(!extension_loaded('zip')) {
            throw new Exception('服务器不支持ZIP扩展，无法解压更新包');
        }
        
        $zip = new ZipArchive();
        $zip_result = $zip->open($temp_file);
        
        if($zip_result !== TRUE) {
            @unlink($temp_file);
            throw new Exception('更新包格式错误，不是有效的ZIP文件');
        }
        
        $zip->close();
        
        exit(json_encode([
            'success' => true,
            'message' => '更新包下载成功',
            'data' => [
                'temp_file' => basename($temp_file),
                'file_size' => filesize($temp_file),
                'version' => $version
            ]
        ]));
        
    } catch(Exception $e) {
        // 清理临时文件
        if(file_exists($temp_file)) {
            @unlink($temp_file);
        }
        exit(json_encode(['success' => false, 'message' => $e->getMessage()]));
    }
}

// 安装更新
function installUpdate() {
    $temp_filename = $_POST['temp_file'] ?? '';
    $version = $_POST['version'] ?? '';
    
    if(empty($temp_filename) || empty($version)) {
        exit(json_encode(['success' => false, 'message' => '缺少必要参数']));
    }
    
    $temp_file = ROOT . 'temp/updates/' . $temp_filename;
    
    if(!file_exists($temp_file)) {
        exit(json_encode(['success' => false, 'message' => '更新包文件不存在']));
    }
    
    try {
        // 创建备份
        $backup_result = createBackup($version);
        if(!$backup_result['success']) {
            throw new Exception('备份失败：' . $backup_result['message']);
        }
        
        // 解压更新包
        $zip = new ZipArchive();
        if($zip->open($temp_file) !== TRUE) {
            throw new Exception('无法打开更新包');
        }
        
        // 检查ZIP内容
        $file_count = $zip->numFiles;
        if($file_count === 0) {
            $zip->close();
            throw new Exception('更新包为空');
        }
        
        // 创建临时解压目录
        $extract_dir = ROOT . 'temp/extract_' . time() . '/';
        if(!@mkdir($extract_dir, 0755, true)) {
            $zip->close();
            throw new Exception('无法创建解压目录');
        }
        
        // 解压到临时目录
        if(!$zip->extractTo($extract_dir)) {
            $zip->close();
            @rmdir($extract_dir);
            throw new Exception('解压失败');
        }
        $zip->close();
        
        // 复制文件到网站根目录
        $copy_result = copyUpdateFiles($extract_dir, ROOT);
        
        // 清理临时文件
        @unlink($temp_file);
        removeDirectory($extract_dir);
        
        if(!$copy_result['success']) {
            throw new Exception('文件复制失败：' . $copy_result['message']);
        }
        
        // 更新版本号
        file_put_contents(ROOT . 'admin/version', $version);
        
        exit(json_encode([
            'success' => true,
            'message' => '更新安装成功！',
            'data' => [
                'version' => $version,
                'backup_file' => $backup_result['backup_file'],
                'updated_files' => $copy_result['updated_files']
            ]
        ]));
        
    } catch(Exception $e) {
        // 清理临时文件
        @unlink($temp_file);
        if(isset($extract_dir) && is_dir($extract_dir)) {
            removeDirectory($extract_dir);
        }
        
        exit(json_encode(['success' => false, 'message' => $e->getMessage()]));
    }
}

// 创建备份
function createBackup($version) {
    $backup_dir = ROOT . 'backup/';
    if(!is_dir($backup_dir)) {
        if(!@mkdir($backup_dir, 0755, true)) {
            return ['success' => false, 'message' => '无法创建备份目录'];
        }
    }
    
    $backup_file = $backup_dir . 'backup_before_' . $version . '_' . date('Y-m-d_H-i-s') . '.zip';
    
    try {
        $zip = new ZipArchive();
        if($zip->open($backup_file, ZipArchive::CREATE) !== TRUE) {
            return ['success' => false, 'message' => '无法创建备份文件'];
        }
        
        // 备份关键目录
        $backup_dirs = ['admin', 'includes', 'template', 'assets'];
        $backup_files = ['config.php', 'index.php'];
        
        foreach($backup_dirs as $dir) {
            if(is_dir(ROOT . $dir)) {
                addDirectoryToZip($zip, ROOT . $dir, $dir);
            }
        }
        
        foreach($backup_files as $file) {
            if(file_exists(ROOT . $file)) {
                $zip->addFile(ROOT . $file, $file);
            }
        }
        
        $zip->close();
        
        return [
            'success' => true,
            'backup_file' => basename($backup_file),
            'message' => '备份创建成功'
        ];
        
    } catch(Exception $e) {
        return ['success' => false, 'message' => '备份失败：' . $e->getMessage()];
    }
}

// 添加目录到ZIP
function addDirectoryToZip($zip, $dir, $zip_dir) {
    if(!is_dir($dir)) return;
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach($files as $file) {
        if(!$file->isDir()) {
            $file_path = $file->getRealPath();
            $relative_path = $zip_dir . '/' . substr($file_path, strlen($dir) + 1);
            $zip->addFile($file_path, $relative_path);
        }
    }
}

// 复制更新文件
function copyUpdateFiles($source_dir, $target_dir) {
    $updated_files = [];
    
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source_dir),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach($iterator as $file) {
            $source_file = $file->getRealPath();
            $relative_path = substr($source_file, strlen($source_dir));
            $target_file = $target_dir . $relative_path;
            
            if($file->isDir()) {
                if(!is_dir($target_file)) {
                    @mkdir($target_file, 0755, true);
                }
            } else {
                $target_file_dir = dirname($target_file);
                if(!is_dir($target_file_dir)) {
                    @mkdir($target_file_dir, 0755, true);
                }
                
                if(copy($source_file, $target_file)) {
                    $updated_files[] = $relative_path;
                }
            }
        }
        
        return [
            'success' => true,
            'updated_files' => $updated_files,
            'message' => '文件复制完成'
        ];
        
    } catch(Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// 递归删除目录
function removeDirectory($dir) {
    if(!is_dir($dir)) return;
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? removeDirectory($path) : @unlink($path);
    }
    @rmdir($dir);
}
?>
