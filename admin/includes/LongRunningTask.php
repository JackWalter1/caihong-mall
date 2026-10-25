<?php
/*
 * 长时间任务管理类
 * 用于处理自动同步等长时间运行的任务
 * 博客：zhonguo.ren
 * QQ群：915043052
 * 开发者：教主
 */

class LongRunningTask {
    
    // 任务开始时间
    private static $start_time = 0;
    // 最大执行时间
    private static $max_execution_time = 300; // 5分钟
    // 日志文件路径
    private static $log_file = '';
    // 任务ID
    private static $task_id = '';
    
    /**
     * 初始化同步任务
     */
    public static function initSyncTask() {
        global $date;
        
        // 设置开始时间
        self::$start_time = time();
        
        // 设置最大执行时间
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        
        // 设置进程标题
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title('彩虹商城-自动同步');
        }
        
        // 生成任务ID
        self::$task_id = md5(uniqid() . time());
        
        // 设置日志文件路径
        self::$log_file = ROOT . '/cache/task_' . self::$task_id . '.log';
        
        // 创建日志目录
        if (!is_dir(ROOT . '/cache')) {
            mkdir(ROOT . '/cache', 0755, true);
        }
    }
    
    /**
     * 记录日志
     * @param string $message 日志消息
     * @param string $level 日志级别
     */
    public static function log($message, $level = 'INFO') {
        global $date;
        
        $log_line = "[{$date}] [{$level}] {$message}\n";
        
        // 写入日志文件
        file_put_contents(self::$log_file, $log_line, FILE_APPEND);
        
        // 输出到控制台
        echo $log_line;
    }
    
    /**
     * 更新同步进度
     * @param array $progress 进度信息
     */
    public static function updateProgress($progress) {
        $progress_file = ROOT . '/cache/progress_' . self::$task_id . '.json';
        file_put_contents($progress_file, json_encode($progress));
    }
    
    /**
     * 任务完成
     * @param array $result 任务结果
     */
    public static function finish($result) {
        // 清理临时文件
        if (file_exists(self::$log_file)) {
            unlink(self::$log_file);
        }
        
        $progress_file = ROOT . '/cache/progress_' . self::$task_id . '.json';
        if (file_exists($progress_file)) {
            unlink($progress_file);
        }
        
        // 输出结果
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * 检查执行时间
     * @return bool 是否超时
     */
    public static function checkTimeout() {
        $current_time = time();
        return ($current_time - self::$start_time) > self::$max_execution_time;
    }
}
?>