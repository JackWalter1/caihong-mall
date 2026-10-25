<?php
if(!defined('IN_CRONLITE'))exit();

// 获取同步间隔配置（从第一个启用的同步配置中获取，数据库中已保存为秒）
$sync_interval_seconds = $DB->getColumn("SELECT sync_interval FROM pre_sync_config WHERE status=1 ORDER BY id ASC LIMIT 1");
if(!$sync_interval_seconds || $sync_interval_seconds <= 0) {
    $sync_interval_seconds = 300; // 默认300秒（5分钟）
}

// 检查是否需要执行
$cron_lastdo = $DB->getColumn("SELECT v FROM pre_config WHERE k='cron_auto_sync_lastdo'");
if($cron_lastdo && time() - strtotime($cron_lastdo) < $sync_interval_seconds) {
    exit('Too frequent, wait ' . ($sync_interval_seconds - (time() - strtotime($cron_lastdo))) . ' seconds');
}

// 更新最后执行时间
$DB->exec("REPLACE INTO pre_config SET k='cron_auto_sync_lastdo',v='".$date."'");

// 调用同步API
$url = $siteurl.'admin/cx-api-synchronization.php';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 增加超时时间
$response = curl_exec($ch);
curl_close($ch);

if($response && strpos($response, '同步任务完成') !== false){
    // 记录成功日志
    $DB->exec("INSERT INTO `pre_log` (`type`,`date`,`ip`,`city`,`data`) VALUES ('cron','".$date."','127.0.0.1','系统','自动同步任务执行成功')");
}else{
    // 记录失败日志
    $DB->exec("INSERT INTO `pre_log` (`type`,`date`,`ip`,`city`,`data`) VALUES ('cron','".$date."','127.0.0.1','系统','自动同步任务执行失败')");
}
?> 