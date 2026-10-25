<?php
// 应用配置文件
return [
    // 应用名称
    'app_name' => '彩虹云商城系统',
    // 应用调试模式
    'debug' => false,
    // 应用时区
    'timezone' => 'Asia/Shanghai',
    // URL模式
    'url_mode' => 1, // 1:普通模式, 2:PATHINFO模式
    // 默认控制器和操作
    'default_controller' => 'Index',
    'default_action' => 'index',
    // 应用密钥
    'app_key' => 'your_app_key',
    // 缓存配置
    'cache' => [
        'type' => 'file',
        'path' => ROOT_PATH . 'runtime/cache/',
        'expire' => 3600
    ],
    // 会话配置
    'session' => [
        'auto_start' => true,
        'expire' => 86400 * 3, // 3天
        'name' => 'RAINBOW_SESSION'
    ]
];
