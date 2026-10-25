<?php
// 文章类子页共用骨架（head + header）—— 彩虹云商城 XHY-01
if (!isset($page_title)) $page_title = '平台内容';
if (!isset($page_desc)) $page_desc = '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars($page_title); ?> - <?php echo $conf['sitename']; ?>-虚拟商品交易平台-虚拟卡密-知识付费-发卡网平台</title>
<meta name="description" content="<?php echo htmlspecialchars($page_desc ? $page_desc : $conf['description']); ?>">
<link rel="icon" href="/favicon.ico">
<link rel="stylesheet" href="/assets/plugin/hsmoban/css/style.css?v=202606161020">
</head>
<body class="subpage">
    <header class="site-header subpage-header">
        <div class="layout site-header__inner">
            <a class="brand" href="./" aria-label="<?php echo $conf['sitename']; ?>">
                <img src="template/XHY-01/assets/img/logo-full.png?v=20261023" width="164" height="44" alt="<?php echo $conf['sitename']; ?>logo">
            </a>
            <div class="header-actions">
                <a class="link-button" href="./">返回首页</a>
                <a class="service-action" href="./?mod=order">查询订单/申请售后</a>
            </div>
        </div>
    </header>
    <main class="subpage-main">
