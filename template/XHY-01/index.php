<?php
// ================= Rainbow Cloud Mall Homepage (template file, called by root index.php) =================
// 注意：此文件被根目录 index.php 通过 TEMPLATE_ROOT include，不能再 require common.php
$x_logo = 'template/XHY-01/assets/img/logo-full.png?v=20261023_FINAL';
$x_slogan = !empty($conf['description']) ? $conf['description'] : '专业的全自动发卡销售平台，7×24小时无人值守自动发货';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $conf['sitename'] ?>-虚拟商品交易平台-虚拟卡密-知识付费-发卡网平台</title>
<script src="./assets/Agod/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
<meta name="description" content="<?php echo htmlspecialchars($conf['description']) ?>">
<link rel="stylesheet" href="//lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<style>
body { font-family: "Microsoft YaHei", "PingFang SC", Arial, sans-serif; font-size: 14px; line-height: 1.7; margin: 0; padding-top: 68px; color: var(--text, #1a2a48); overflow-x: hidden; }
/* hide old modal overlays conflict */
.modal-backdrop { z-index: 1040 !important; }
</style>
<?php include TEMPLATE_ROOT.'XHY-01/_modal_top.inc.php'; ?>

<!-- ================= 顶部导航 site-header ================= -->
<header class="site-header">
    <div class="site-header__inner">
        <a class="brand" href="./">
            <img src="<?php echo $x_logo ?>" alt="<?php echo $conf['sitename'] ?>">
        </a>
        <nav class="site-nav" aria-label="主导航">
            <ul>
                <li><a href="./" class="active">网站首页</a></li>
                <li><a href="./?mod=goods-release">禁售目录</a></li>
                <li><a href="./?mod=notice">最新公告</a></li>
                <li><a href="./?mod=about">公司介绍</a></li>
                <li><a href="./?mod=shop">演示店铺</a></li>
                <li class="has-submenu">
                    <a href="javascript:;">黑名单查询</a>
                    <ul class="site-nav__submenu">
                        <li><a href="./?mod=riskcontrol">风控名单</a></li>
                        <li><a href="./?mod=complaints">商家投诉率</a></li>
                        <li><a href="./?mod=bannedrecords">买家黑名单</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="header-actions">
            <a class="link-button" href="./?mod=download">商家APP下载</a>
            <a class="link-button" href="./user/login.php">商户登录</a>
            <a class="solid-button solid-button--small" href="./user/reg.php">免费入驻</a>
            <a class="service-action" href="./?mod=order">查询订单/申请售后</a>
        </div>
    </div>
</header>

<?php include TEMPLATE_ROOT.'XHY-01/_hero_hsfaka_v2.inc.php'; ?>

<!-- ================= 下单核心 x-shop（保留原 shop.inc.php 不动） ================= -->
<section class="section x-shop-keep" id="xShop" style="padding-top:60px;">
    <div class="hsf-layout">
        <div class="section-heading">
            <span>SELF-SERVICE ORDER</span>
            <h2>自 助 下 单</h2>
            <p>选择商品，填写信息，自动发货，秒级到账</p>
        </div>
        <div class="x-shop-panel">
            <?php include TEMPLATE_ROOT.'default/shop.inc.php'; ?>
        </div>
    </div>
</section>

<!-- ================= bottom-cta ================= -->
<section class="bottom-cta">
    <div class="hsf-layout bottom-cta__inner">
        <h2>让你的发卡网，从第一眼就显得更专业</h2>
        <p>用企业级首页承接商户入驻、买家信任、公告触达和长期经营。</p>
        <a class="solid-button" href="./user/reg.php">开通小店</a>
    </div>
</section>

<!-- ================= site-footer ================= -->
<footer class="site-footer-new">
    <div class="site-footer-new__inner">
        <div class="footer-brand">
            <img src="<?php echo $x_logo ?>" alt="<?php echo $conf['sitename'] ?>logo">
            <p><?php echo htmlspecialchars($conf['description']) ?></p>
        </div>
        <nav class="footer-links" aria-label="快捷通道">
            <strong>快捷通道</strong>
            <a href="#capabilities">产品能力</a>
            <a href="./?mod=download">APP下载</a>
            <a href="./?mod=notice">服务公告</a>
            <a href="#process">入驻流程</a>
            <a href="./?mod=about">关于我们</a>
        </nav>
        <nav class="footer-links" aria-label="商户服务">
            <strong>商户服务</strong>
            <a href="./user/login.php">商户登录</a>
            <a href="./user/reg.php">商户注册</a>
            <a href="./?mod=server-agreement">帮助协议</a>
            <a href="./?mod=notice">平台公告</a>
        </nav>
        <nav class="footer-links" aria-label="法律条款">
            <strong>法律条款</strong>
            <a href="./?mod=privic-server">隐私政策</a>
            <a href="./?mod=server-agreement">服务协议</a>
            <a href="./?mod=forbid-sale">禁售商品</a>
            <a href="./?mod=riskcontrol">风控名单</a>
        </nav>
        <div class="footer-contact">
            <strong>联系我们</strong>
            <span>客服QQ：1560292</span>
            <span>客服微信：WalTerTeChStuDio</span>
            <span>客服电话：+85256199579</span>
            <span>客服邮箱：jackwalter66@163.com</span>
            <span class="footer-contact__qr-wrap">客服二维码：<button type="button" class="footer-contact__qr-trigger">查看二维码</button><span class="footer-contact__qr"><img class="footer-contact__qr-img" src="template/XHY-01/assets/img/kefu-qr-2.png?v=20261024" alt="客服二维码"><em>客服二维码</em></span></span>
        </div>
        <div class="footer-qr">
            <img class="footer-qr__img" src="template/XHY-01/assets/img/kefu-qr.jpg?v=20261024" alt="公众号二维码">
            <span>微信公众号</span>
        </div>
    </div>
    <div class="site-footer-new__bottom">
        <span>Copyright &copy; <?php echo date('Y') ?> <?php echo $conf['sitename'] ?> All Rights Reserved.</span>
        <?php
$__icp = trim((string)($conf['icp_number'] ?? ''));
$__police = trim((string)($conf['police_number'] ?? ''));
$__license = trim((string)($conf['icp_license'] ?? ''));
$__records = array();
if ($__icp !== '') { $__records[] = '<span class="footer-records__item">' . htmlspecialchars($__icp) . '</span>'; }
if ($__police !== '') { $__records[] = '<span class="footer-records__item">' . htmlspecialchars($__police) . '</span>'; }
if ($__license !== '') { $__records[] = '<span class="footer-records__item">' . htmlspecialchars($__license) . '</span>'; }
if ($__records) {
?>
        <span class="footer-records"><?php echo implode('', $__records); ?></span>
<?php } ?>
    </div>
</footer>

<!-- ================= 右侧悬浮按钮 ================= -->
<aside class="float-rail" aria-label="快捷服务">
    <a class="float-rail__item float-rail__item--login" href="./user/login.php">
        <i class="float-rail__icon" aria-hidden="true"></i>
        <span>登录</span>
    </a>
    <a class="float-rail__item float-rail__item--join" href="./user/reg.php">
        <i class="float-rail__icon" aria-hidden="true"></i>
        <span>入驻</span>
    </a>
    <div class="float-rail__item float-rail__item--qr" tabindex="0" role="button" aria-label="客服二维码">
        <i class="float-rail__icon" aria-hidden="true"></i>
        <span>客服</span>
        <div class="float-rail__qr">
            <img class="float-rail__qr-img" src="template/XHY-01/assets/img/kefu-qr-3.png" alt="客服二维码">
            <strong>扫码联系客服</strong>
        </div>
    </div>
</aside>

<?php include TEMPLATE_ROOT.'XHY-01/_modal_notice.inc.php'; ?>
<?php include TEMPLATE_ROOT.'XHY-01/_modal_cxsm.inc.php'; ?>

<script>var hashsalt = <?php echo $addsalt_js; ?>; var homepage = true;</script>
<script src="assets/js/main.js?ver=<?php echo VERSION ?>"></script>

<script>
/* 商品直达：从店铺页 ./?tid=N#xShop 进入时自动选中商品 */
(function () {
    var m = location.search.match(/[?&]tid=(\d+)/);
    if (!m) return;
    var tid = m[1];
    fetch('./ajax.php?act=gettool&tid=' + tid).then(function (r) { return r.json(); }).then(function (d) {
        if (d.code != 0 || !d.data || !d.data.length) return;
        var t = d.data[0];
        var cidSel = document.getElementById('cid');
        if (cidSel) { cidSel.value = t.cid; }
        if (typeof loadGoodsByCategory === 'function') { try { loadGoodsByCategory(t.cid); } catch (e) {} }
        var tries = 0;
        (function pick() {
            var sel = document.getElementById('tid');
            if (sel && sel.querySelector('option[value="' + tid + '"]')) {
                sel.value = tid;
                if (typeof getPoint === 'function') { try { getPoint(); } catch (e) {} }
                return;
            }
            if (++tries > 50) return;
            setTimeout(pick, 200);
        })();
    }).catch(function () {});
})();
</script>
</body>
</html>
