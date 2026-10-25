<?php
// 路由配置文件
return [
    // 基本路由规则
    '/' => 'Index/index',
    '/user' => 'User/index',
    '/user/login' => 'User/login',
    '/user/register' => 'User/register',
    '/user/logout' => 'User/logout',
    '/shop' => 'Shop/index',
    '/admin' => 'Admin/index',
    '/admin/login' => 'Admin/login',
    '/api' => 'Api/index',
    '/ajax' => 'Ajax/index'
];
