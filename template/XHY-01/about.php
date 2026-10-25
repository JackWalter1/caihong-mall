<?php
// 文章类子页共用骨架（head + header）—— 彩虹云商城 XHY-01
if (!isset($page_title)) $page_title = '平台内容';
if (!isset($page_desc)) $page_desc = '';
?>
<?php $page_title = '关于我们'; $page_desc = '彩虹云商城是专注于虚拟商品交易的电子商务平台，核心业务涵盖虚拟卡密、数字权益、知识付费等虚拟产品的电商交易服务。'; ?>

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

<section class="subpage-hero">
    <div class="layout">
        <span>平台内容</span>
        <h1>关于我们</h1>
        <p>2023-05-15</p>                </div>
</section>
<section class="layout subpage-card">
    <article class="article-body">
        <p style="text-indent: 2em;">彩虹云商城是彩虹云商城运营团队旗下专注于虚拟商品交易的电子商务平台，母公司彩虹云商城运营团队成立于 2023 年 06 月 13 日。平台核心业务涵盖虚拟卡密、数字权益、知识付费等虚拟产品的电商交易服务，为商户提供虚拟商品的寄售、分销及资金结算解决方案。</p><p style="text-indent: 2em;">作为新一代虚拟商品交易基础设施，彩虹云商城采用资金直清模式，通过对接支付宝直付通与微信收付通系统，实现买家付款直接进入商户账户，无中间托管环节，且支持 0 门槛实时提现，有效解决传统平台提现难、手续费高的行业痛点。平台功能体系包含商户自定义支付模式、自动 / 手动提现切换、代理分销管理等核心模块，支持商品价格动态更新、批量对接及分销价格管控等精细化运营需求。</p><p style="text-indent: 2em;">在服务生态方面，彩虹云商城依托母公司具备的互联网信息服务、在线数据处理与交易处理业务（经营类电子商务）及增值电信业务资质，面向游戏点卡、软件 CDK、注册码、会员权益等合规虚拟商品商户开放入驻，提供店铺专属链接推广、SEO 优化指导及营销工具支持（如优惠券、限时折扣设置）。据 2025 年行业评测，其因安全合规的交易环境和灵活的技术架构，成为企业级虚拟交易领域的推荐平台之一。</p><p style="text-indent: 2em;">平台技术迭代持续聚焦商户体验优化，近期更新包括修复支付接口 BUG、优化商品展示分类及完善代理邀请机制等，逐步构建起涵盖交易、结算、推广的全链路虚拟商品服务能力。</p>
    </article>
</section>
<?php include TEMPLATE_ROOT.'XHY-01/_subpage_foot.inc.php'; ?>
