<?php
include("./includes/common.php");

// 生成订单提交防重放令牌
$addsalt = md5(mt_rand(0, 999) . time());
$_SESSION['addsalt'] = $addsalt;

$zid = isset($_GET['zid']) ? intval($_GET['zid']) : 2;
$store = $DB->getRow("SELECT * FROM pre_site WHERE zid=:zid", [':zid' => $zid]);

if (!$store) {
    exit("<script language='javascript'>alert('店铺不存在！');window.location.href='/index.php';</script>");
}

$sitename = $store['sitename'] ?: $store['user'];
$store_desc = $store['store_desc'] ?: '专业虚拟卡密·知识付费·资源下载交易平台';
$anounce = $store['anounce'] ?: '欢迎来到本店，所有商品自动发货，购买后请保存好订单号！';
$deposit = floatval($store['deposit']);
$verified = intval($store['verified']);
$avatar_char = mb_substr($sitename, 0, 1, 'UTF-8');

// 店铺配置字段
$store_avatar = $store['store_avatar'] ?: $store['faceimg'] ?: '';
$kfqq = $store['kfqq'] ?: '';
$kfwx = $store['kfwx'] ?: '';
$contact_qq = $store['qq'] ?: '';
$contact_phone = $store['phone'] ?: '';
$contact_email = $store['email'] ?: '';
$pay_methods = $store['pay_methods'] ?: 'alipay,qqpay,wxpay';
$pay_methods_arr = explode(',', $pay_methods);
$terms_content = $store['terms_content'] ?: '<p>本商城支持 <strong>7x24小时自动发货</strong>，购买后系统将自动发送卡密到您的订单页面。</p><p><strong>购买须知：</strong></p><p>1. 请确认商品信息无误后再下单；</p><p>2. 虚拟商品一经售出，非质量问题不支持退换；</p><p>3. 请妥善保管您的订单号，凭订单号查询卡密；</p><p>4. 如遇问题请联系商家客服处理。</p>';

// 联系方式拼接
$contact_info = [];
if ($contact_qq) $contact_info[] = 'QQ：' . $contact_qq;
if ($contact_phone) $contact_info[] = '电话：' . $contact_phone;
if ($contact_email) $contact_info[] = '邮箱：' . $contact_email;
$contact_text = implode("\n", $contact_info);
$has_contact = !empty($contact_info);

// 获取顶级分类（pid=0）
$top_cats = $DB->getAll("SELECT * FROM pre_class WHERE zid=:zid AND pid=0 AND active=1 ORDER BY sort ASC", [':zid' => $zid]);
if (empty($top_cats)) {
    // 默认分类
    $top_cats = [
        ['cid' => 0, 'name' => '卡密', 'shopimg' => ''],
    ];
}

// 当前选中的顶级分类
$cur_cid = isset($_GET['cid']) ? intval($_GET['cid']) : (isset($top_cats[0]['cid']) ? $top_cats[0]['cid'] : 0);

// 获取子分类
$sub_cats = $DB->getAll("SELECT * FROM pre_class WHERE zid=:zid AND pid=:pid AND active=1 ORDER BY sort ASC", [':zid' => $zid, ':pid' => $cur_cid]);
$cur_sub = isset($_GET['sub']) ? intval($_GET['sub']) : 0;

// 分类emoji映射
$cat_emoji = [
    '卡密' => '⚡️',
    '知识' => '☘️',
    '资源' => '💎',
    '其他' => '📦',
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($sitename); ?> - 商户店铺</title>
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="shortcut icon" href="/assets/img/favicon/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/assets/plugin/hsmoban/css/style.css?ver=20261022e">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * { box-sizing: border-box; }
        body { background: #f5f6fa; min-height: 100vh; margin: 0; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Hiragino Sans GB","Microsoft YaHei",sans-serif; color: #1d2129; }
        .site-header { position: fixed; top: 0; left: 0; right: 0; z-index: 100; background: #fff; border-bottom: 1px solid #e5e6eb; }
        .store-page { padding-top: 68px; }
        .store-wrap { max-width: 1200px; margin: 0 auto; padding: 16px; }

        /* 店铺信息区 */
        .store-banner { background: #fff; border-radius: 4px; padding: 20px; margin-bottom: 12px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
        .store-avatar { width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg,#667eea,#764ba2); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 26px; font-weight: 700; flex-shrink: 0; overflow: hidden; }
        .store-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .store-info { flex: 1; min-width: 0; }
        .store-name-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap; }
        .store-name { font-size: 18px; font-weight: 700; color: #1d2129; }
        .badge { font-size: 12px; padding: 2px 8px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center; gap: 3px; line-height: 1.6; }
        .badge.verify { background: #e8f7ee; color: #00b42a; }
        .badge.deposit { background: #fff7e6; color: #ff7d00; }
        .badge.nodeposit { background: #fff1f0; color: #f53f3f; }
        .store-desc { color: #86909c; font-size: 13px; margin-bottom: 10px; line-height: 1.5; }
        .store-actions { display: flex; gap: 8px; }
        .store-btn { padding: 6px 16px; border-radius: 4px; font-size: 13px; font-weight: 500; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 5px; transition: all .2s; text-decoration: none; }
        .store-btn.contact { background: #00b42a; color: #fff; }
        .store-btn.contact:hover { background: #009a29; }
        .store-btn.share { background: #f2f3f5; color: #4e5969; }
        .store-btn.share:hover { background: #e5e6eb; }

        /* 店铺公告 */
        .store-notice { background: #fff; border-radius: 4px; padding: 12px 16px; margin-bottom: 12px; display: flex; align-items: flex-start; gap: 10px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
        .notice-icon { font-size: 16px; color: #ff7d00; flex-shrink: 0; margin-top: 2px; }
        .notice-content { flex: 1; }
        .notice-title { font-size: 14px; font-weight: 600; color: #1d2129; margin-bottom: 4px; display: inline; }
        .store-notice p { color: #4e5969; font-size: 13px; line-height: 1.6; margin: 0; display: inline; }

        /* 分类标签（arco风格下划线） */
        .cat-tabs-wrap { background: #fff; border-radius: 4px; padding: 0 16px; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
        .cat-tabs { display: flex; gap: 0; }
        .cat-tab { padding: 14px 16px; font-size: 14px; color: #86909c; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: color .2s; font-weight: 500; border-bottom: 2px solid transparent; text-decoration: none; margin-bottom: -1px; }
        .cat-tab:hover { color: #165dff; }
        .cat-tab.active { color: #165dff; border-bottom-color: #165dff; }
        .cat-tab .count { font-size: 12px; color: #86909c; font-weight: 400; }
        .cat-search { display: flex; align-items: center; gap: 6px; }
        .cat-search input { width: 200px; padding: 6px 12px; border: 1px solid #e5e6eb; border-radius: 4px; font-size: 13px; outline: none; transition: border-color .2s; }
        .cat-search input:focus { border-color: #165dff; }
        .cat-search button { background: none; border: none; color: #86909c; cursor: pointer; font-size: 16px; padding: 4px; }

        /* 子分类筛选 */
        .sub-cat-filter { background: #fff; border-radius: 4px; padding: 12px 16px; margin-bottom: 18px; display: flex; gap: 8px; flex-wrap: wrap; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
        .sub-cat-item { padding: 6px 16px; border-radius: 4px; font-size: 13px; color: #4e5969; cursor: pointer; transition: all .2s; text-decoration: none; background: #f7f8fa; border: 1px solid transparent; }
        .sub-cat-item:hover { background: #e8f3ff; color: #165dff; }
        .sub-cat-item.active { background: #165dff; color: #fff; }
        .sub-cat-item .count { font-size: 12px; opacity: .75; margin-left: 4px; }

        /* 商品标题 */
        .section-title { font-size: 14px; font-weight: 600; color: #1d2129; margin: 18px 0 14px; padding-left: 8px; border-left: 3px solid #165dff; }

        /* 商品网格 */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(218px, 1fr)); gap: 12px; }
        .product-card { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,.03); transition: all .25s; cursor: pointer; border: 1px solid #f0f1f5; }
        .product-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.08); }
        .product-img { width: 100%; height: 208px; background: linear-gradient(135deg,#f0f4ff,#e8f0ff); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
        .product-img img { width: 100%; height: 100%; object-fit: cover; }
        .product-img .fa { font-size: 48px; color: #c9cdd4; }
        .product-tag { position: absolute; top: 8px; right: 8px; background: rgba(22,93,255,.9); color: #fff; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 500; }
        .product-info { padding: 10px 12px; }
        .product-name { font-size: 14px; color: #1d2129; font-weight: 500; margin-bottom: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-price-row { display: flex; align-items: center; justify-content: space-between; }
        .product-price { color: #165dff; font-size: 18px; font-weight: 600; display: flex; align-items: baseline; }
        .product-price small { font-size: 12px; margin-right: 1px; }
        .stock-tag { font-size: 11px; padding: 2px 8px; border-radius: 4px; background: #f2f3f5; color: #86909c; font-weight: 500; }
        .stock-tag.in-stock { background: #e8f7ee; color: #00b42a; }
        .stock-tag.out-stock { background: #fff7e6; color: #ff7d00; }
        .list-end { text-align: center; color: #c9cdd4; font-size: 13px; padding: 32px 0; }

        /* 浮动客服 */
        .float-service { position: fixed; right: 16px; top: 50%; transform: translateY(-50%); z-index: 50; }
        .float-service-btn { background: #fff; border-radius: 12px; padding: 14px 10px; box-shadow: 0 4px 16px rgba(0,0,0,.1); text-align: center; cursor: pointer; transition: all .2s; border: 1px solid #e5e6eb; width: 64px; }
        .float-service-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.14); }
        .float-service-icon { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg,#165dff,#4080ff); display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; font-size: 18px; color: #fff; }
        .float-service-text { font-size: 11px; color: #4e5969; font-weight: 500; line-height: 1.3; }

        /* 页脚 */
        .store-footer { margin-top: 16px; padding: 16px; text-align: center; color: #86909c; font-size: 12px; }
        /* ===== 弹窗容器与动画 ===== */
        .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 9999; display: none; align-items: center; justify-content: center; padding: 20px; }
        .modal-overlay.show { display: flex; animation: modalFadeIn .25s ease; }
        @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes modalPopIn { from { opacity: 0; transform: translateY(20px) scale(.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .modal-box { background: #fff; border-radius: 18px; width: 440px; max-width: 100%; max-height: 92vh; overflow-y: auto; box-shadow: 0 25px 70px rgba(0,0,0,.25), 0 0 0 1px rgba(255,255,255,.5) inset; animation: modalPopIn .3s cubic-bezier(.16,1,.3,1); }
        .modal-box::-webkit-scrollbar { width: 6px; }
        .modal-box::-webkit-scrollbar-thumb { background: #d4d8de; border-radius: 3px; }

        /* ===== 弹窗头部 ===== */
        .modal-header { padding: 20px 24px 12px; display: flex; align-items: center; justify-content: space-between; }
        .modal-title { font-size: 17px; font-weight: 700; color: #1d2129; display: flex; align-items: center; gap: 8px; }
        .modal-title::before { content: ""; display: inline-block; width: 4px; height: 18px; border-radius: 2px; background: linear-gradient(180deg,#165dff,#409eff); }
        .modal-close { cursor: pointer; color: #86909c; font-size: 18px; background: #f2f3f5; border: none; width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all .2s; }
        .modal-close:hover { background: #ffece8; color: #f53f3f; transform: rotate(90deg); }

        /* ===== 弹窗内容区 ===== */
        .modal-body { padding: 8px 24px 16px; }

        /* 商品卡片 */
        .modal-product { display: flex; align-items: center; gap: 14px; padding: 14px; background: linear-gradient(135deg,#f7f9fc,#eef2f7); border-radius: 12px; margin-bottom: 16px; border: 1px solid #eef0f3; }
        .modal-product-img { width: 60px; height: 60px; border-radius: 12px; background: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .modal-product-img img { width: 100%; height: 100%; object-fit: cover; }
        .modal-product-info { flex: 1; min-width: 0; }
        .modal-product-name { font-size: 15px; font-weight: 700; color: #1d2129; margin-bottom: 4px; }
        .modal-product-desc { font-size: 12px; color: #86909c; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* 自动发货标签 */
        .auto-delivery-badge { display: inline-flex; align-items: center; gap: 5px; background: linear-gradient(135deg,#e8f7ee,#d4f5de); color: #00b42a; font-size: 12px; padding: 5px 12px; border-radius: 20px; margin-bottom: 16px; font-weight: 600; border: 1px solid #b7ebc6; }
        .auto-delivery-badge i { animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .5; } }

        /* 数量与优惠券行 */
        .check-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .check-row label { display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; color: #4e5969; }
        .check-row input[type=checkbox] { width: 15px; height: 15px; accent-color: #165dff; cursor: pointer; }
        .qty-control { display: flex; align-items: center; border: 1px solid #e5e6eb; border-radius: 8px; overflow: hidden; }
        .qty-btn { width: 32px; height: 32px; border: none; background: #f7f8fa; cursor: pointer; font-size: 16px; color: #4e5969; display: flex; align-items: center; justify-content: center; transition: all .15s; }
        .qty-btn:hover { background: #165dff; color: #fff; }
        .qty-input { width: 48px; height: 32px; border: none; border-left: 1px solid #e5e6eb; border-right: 1px solid #e5e6eb; text-align: center; font-size: 14px; font-weight: 600; outline: none; color: #1d2129; background: #fff; }

        /* 表单 */
        .form-group { margin-bottom: 14px; }
        .form-label { font-size: 13px; color: #4e5969; margin-bottom: 7px; display: block; font-weight: 600; }
        .form-input { width: 100%; padding: 11px 14px; border: 1.5px solid #e5e6eb; border-radius: 10px; font-size: 14px; outline: none; transition: all .2s; background: #fafbfc; }
        .form-input:focus { border-color: #165dff; background: #fff; box-shadow: 0 0 0 3px rgba(22,93,255,.1); }

        /* 警告提示 */
        .warning-tip { background: linear-gradient(135deg,#fff7e6,#fff1e0); border: 1px solid #ffd591; border-radius: 10px; padding: 10px 14px; font-size: 12px; color: #d46b08; line-height: 1.6; margin-bottom: 16px; display: flex; align-items: flex-start; gap: 8px; }
        .warning-tip i { color: #faad14; margin-top: 2px; flex-shrink: 0; }

        /* 价格行 */
        .price-row { display: flex; align-items: baseline; justify-content: space-between; padding: 14px 16px; background: linear-gradient(135deg,#f0f7ff,#e6f0ff); border-radius: 12px; margin-bottom: 16px; border: 1px solid #d6e4ff; }
        .price-label { font-size: 14px; color: #4e5969; font-weight: 600; }
        .price-total { font-size: 26px; font-weight: 800; color: #165dff; letter-spacing: -.5px; }
        .price-total small { font-size: 15px; font-weight: 700; }

        /* 支付方式 */
        .pay-methods { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 4px; }
        .pay-method { position: relative; border: 2px solid #e5e6eb; border-radius: 12px; padding: 14px 6px; text-align: center; cursor: pointer; transition: all .2s; background: #fff; }
        .pay-method:hover { border-color: #165dff; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(22,93,255,.12); }
        .pay-method.active { border-color: #165dff; background: linear-gradient(135deg,#f0f7ff,#e6f0ff); box-shadow: 0 6px 16px rgba(22,93,255,.15); }
        .pay-method.active::after { content: "\f00c"; font-family: FontAwesome; position: absolute; top: -8px; right: -8px; width: 20px; height: 20px; background: #165dff; color: #fff; border-radius: 50%; font-size: 11px; display: flex; align-items: center; justify-content: center; }
        .pay-method .icon { font-size: 24px; margin-bottom: 6px; }
        .pay-method .name { font-size: 12px; color: #1d2129; font-weight: 600; }
        .pay-method.alipay .icon { color: #1677ff; }
        .pay-method.qqpay .icon { color: #12b7f5; }
        .pay-method.wxpay .icon { color: #07c160; }

        /* ===== 底部按钮 ===== */
        .modal-footer { display: flex; gap: 10px; padding: 0 24px 22px; }
        .btn { flex: 1; padding: 12px; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; border: none; transition: all .2s; }
        .btn-cancel { background: #f2f3f5; color: #4e5969; }
        .btn-cancel:hover { background: #e5e6eb; }
        .btn-pay { background: linear-gradient(135deg,#165dff,#409eff); color: #fff; box-shadow: 0 6px 16px rgba(22,93,255,.3); }
        .btn-pay:hover { transform: translateY(-1px); box-shadow: 0 10px 22px rgba(22,93,255,.38); }
        .btn-pay:disabled { opacity: .7; cursor: not-allowed; transform: none; }

        /* ===== 条款弹窗 ===== */
        .terms-modal .modal-box { width: 380px; }
        .terms-content { font-size: 13px; color: #4e5969; line-height: 1.9; padding: 10px 0; }
        .terms-content strong { color: #1d2129; }
        .terms-modal .modal-footer .btn { flex: 1; }

        /* 头部导航（店铺页） */
        .site-header__inner{height:68px !important;display:flex !important;align-items:center !important;max-width:1200px !important;margin:0 auto !important;padding:0 16px !important;gap:0 !important;}
        .brand{display:flex !important;align-items:center !important;height:44px !important;margin-right:24px !important;flex-shrink:0 !important;}
        .brand img{height:44px !important;width:auto !important;display:block !important;}
        .site-nav{flex:0 1 auto !important;display:flex !important;align-items:center !important;height:68px !important;min-width:0 !important;margin-right:auto !important;}
        .site-nav ul{display:flex !important;align-items:center !important;height:68px !important;margin:0 !important;padding:0 !important;list-style:none !important;gap:2px !important;}
        .site-nav li{display:flex !important;align-items:center !important;height:68px !important;margin:0 !important;padding:0 !important;position:relative !important;flex-shrink:0 !important;}
        .site-nav a{display:inline-flex !important;align-items:center !important;height:36px !important;line-height:1 !important;padding:0 8px !important;margin:0 !important;font-size:14px !important;white-space:nowrap !important;color:#4e5969 !important;text-decoration:none !important;border-radius:4px !important;font-weight:500 !important;}
        .site-nav a:hover{color:#165dff !important;}
        .site-nav .has-dropdown > a::after{content:' ▾';font-size:10px;}
        .site-dropdown{position:absolute !important;top:100% !important;left:0 !important;min-width:140px !important;background:#fff !important;border-radius:8px !important;box-shadow:0 6px 24px rgba(0,0,0,.12) !important;padding:8px !important;display:none !important;z-index:100 !important;border:1px solid #e5e6eb;}
        .site-dropdown a{display:block !important;padding:8px 12px !important;color:#4e5969 !important;font-size:13px !important;border-radius:4px !important;height:auto !important;}
        .site-dropdown a:hover{background:#f7f8fa !important;color:#165dff !important;}
        .site-nav li:hover .site-dropdown{display:block !important;}
        .header-actions{display:flex !important;align-items:center !important;height:36px !important;gap:8px !important;margin-left:auto !important;flex-shrink:0 !important;}
        .ghost-button{display:inline-flex !important;align-items:center !important;height:36px !important;line-height:1 !important;padding:0 16px !important;white-space:nowrap !important;font-size:13px !important;background:#165dff !important;color:#fff !important;border-radius:4px !important;text-decoration:none !important;font-weight:500 !important;}
        .ghost-button:hover{background:#0e42d2 !important;}

        @media (max-width: 768px) {
            .store-banner { flex-direction: column; text-align: center; }
            .store-actions { justify-content: center; }
            .product-grid { grid-template-columns: repeat(2, 1fr); }
            .cat-tabs-wrap { flex-direction: column; align-items: stretch; }
            .cat-search input { width: 100%; }
            .float-service { right: 8px; }
        }

        .load-more-btn { padding: 8px 32px; background: #fff; border: 1px solid #e5e6eb; border-radius: 4px; font-size: 13px; color: #4e5969; cursor: pointer; transition: all .2s; }
        .load-more-btn:hover { border-color: #165dff; color: #165dff; }

        /* 用户信息面板 */
        .user-info-panel { background: #fff; border-radius: 4px; margin-bottom: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.04); overflow: hidden; }
        .user-info-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid #f2f3f5; background: #f8fafc; }
        .user-info-title { font-size: 14px; font-weight: 600; color: #1d2129; display: inline-flex; align-items: center; gap: 6px; }
        .user-info-title i { color: #1677ff; }
        .user-info-close { cursor: pointer; color: #86909c; font-size: 14px; padding: 4px; }
        .user-info-close:hover { color: #1d2129; }
        .user-info-body { padding: 8px 20px; }
        .user-info-row { display: flex; padding: 10px 0; border-bottom: 1px solid #f7f8fa; font-size: 13px; }
        .user-info-row:last-child { border-bottom: none; }
        .user-info-label { width: 100px; color: #86909c; flex-shrink: 0; }
        .user-info-value { color: #1d2129; flex: 1; }
        .user-info-value .badge { font-size: 12px; padding: 2px 8px; border-radius: 4px; font-weight: 500; display: inline-flex; align-items: center; gap: 3px; }
        .user-info-value .badge.verify { background: #e8f7ee; color: #00b42a; }

        /* ===== 高级 Toast 通知 ===== */
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 10000; display: flex; flex-direction: column; gap: 12px; pointer-events: none; }
        .toast { pointer-events: auto; min-width: 280px; max-width: 380px; background: #fff; border-radius: 14px; padding: 14px 18px 14px 14px; box-shadow: 0 12px 40px rgba(0,0,0,.18), 0 2px 8px rgba(0,0,0,.06); display: flex; align-items: flex-start; gap: 12px; animation: toastIn .35s cubic-bezier(.16,1,.3,1); border: 1px solid rgba(0,0,0,.05); position: relative; overflow: hidden; }
        .toast.hide { animation: toastOut .3s ease forwards; }
        @keyframes toastIn { from { opacity: 0; transform: translateX(40px) scale(.95); } to { opacity: 1; transform: translateX(0) scale(1); } }
        @keyframes toastOut { to { opacity: 0; transform: translateX(40px) scale(.95); } }
        .toast::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; }
        .toast-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px; }
        .toast-body { flex: 1; min-width: 0; }
        .toast-title { font-size: 14px; font-weight: 700; color: #1d2129; margin-bottom: 2px; }
        .toast-msg { font-size: 13px; color: #4e5969; line-height: 1.5; word-break: break-word; white-space: pre-line; }
        .toast-close { background: none; border: none; color: #86909c; font-size: 14px; cursor: pointer; padding: 2px; line-height: 1; flex-shrink: 0; }
        .toast-close:hover { color: #f53f3f; }
        /* success */
        .toast.success::before { background: linear-gradient(180deg,#00b42a,#23c343); }
        .toast.success .toast-icon { background: linear-gradient(135deg,#e8f7ee,#d4f5de); color: #00b42a; }
        /* error */
        .toast.error::before { background: linear-gradient(180deg,#f53f3f,#f76560); }
        .toast.error .toast-icon { background: linear-gradient(135deg,#ffece8,#ffd9d4); color: #f53f3f; }
        /* warning */
        .toast.warning::before { background: linear-gradient(180deg,#ff7d00,#ff9a2e); }
        .toast.warning .toast-icon { background: linear-gradient(135deg,#fff7e6,#ffe7ba); color: #ff7d00; }
        /* info */
        .toast.info::before { background: linear-gradient(180deg,#165dff,#409eff); }
        .toast.info .toast-icon { background: linear-gradient(135deg,#e8f3ff,#d4e8ff); color: #165dff; }
    </style>
</head>
<body>

<header class="site-header" data-header>
    <div class="layout site-header__inner">
        <a class="brand" href="/index.php" aria-label="彩虹云商城">
            <img src="/assets/plugin/hsmoban/images/caihong-logo-store.png" width="200" height="44" alt="彩虹云商城">
        </a>
        <nav class="site-nav" aria-label="主导航">
            <ul>
                <li><a href="/index.php">网站首页</a></li>
                <li><a href="/?mod=goods-release">禁售目录</a></li>
                <li><a href="/?mod=notice">最新公告</a></li>
                <li><a href="/?mod=about">公司介绍</a></li>
                <li><a href="/?mod=shop">演示店铺</a></li>
                <li class="has-dropdown">
                    <a href="/?mod=riskcontrol">黑名单查询 ▾</a>
                    <div class="site-dropdown">
                        <a href="/?mod=riskcontrol">风控名单</a>
                        <a href="/?mod=complaints">商家投诉率</a>
                        <a href="/?mod=bannedrecords">买家黑名单</a>
                    </div>
                </li>
                <li><a href="/?mod=download">APP·开放平台</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <a class="ghost-button" href="/?mod=order"><i class="fa fa-search"></i> 订单查询</a>
        </div>
    </div>
</header>

<div class="store-page">
    <div class="store-wrap">
        <!-- 店铺信息 -->
        <div class="store-banner">
            <div class="store-avatar">
                <?php if (!empty($store_avatar)): ?>
                <img src="<?php echo htmlspecialchars($store_avatar); ?>" alt="<?php echo htmlspecialchars($sitename); ?>" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                <?php else: ?>
                <?php echo htmlspecialchars($avatar_char); ?>
                <?php endif; ?>
            </div>
            <div class="store-info">
                <div class="store-name-row">
                    <span class="store-name"><?php echo htmlspecialchars($sitename); ?></span>
                    <?php if ($verified == 1): ?>
                    <span class="badge verify"><i class="fa fa-check-circle"></i> 已认证</span>
                    <?php endif; ?>
                    <?php if ($deposit > 0): ?>
                    <span class="badge deposit" title="商家已向平台缴纳保证金"><i class="fa fa-shield"></i> 已缴保证金 ¥<?php echo number_format($deposit, 2); ?></span>
                    <?php else: ?>
                    <span class="badge nodeposit" title="该商家尚未缴纳保证金，交易前请注意核实"><i class="fa fa-shield"></i> 未缴保证金</span>
                    <?php endif; ?>
                </div>
                <div class="store-desc"><?php echo htmlspecialchars($store_desc); ?></div>
                <div class="store-actions">
                    <a href="javascript:;" class="store-btn contact" onclick="showContact()"><i class="fa fa-comments"></i> 联系TA</a>
                    <a href="javascript:;" class="store-btn share" onclick="showToast(window.location.href, 'info', '分享链接');"><i class="fa fa-share-alt"></i> 分享</a>
                    <a href="javascript:;" class="store-btn share" onclick="toggleUserInfo()"><i class="fa fa-user"></i> 用户信息</a>
                </div>
            </div>
        </div>

        <!-- 店铺公告 -->
        <div class="store-notice">
            <div class="notice-icon"><i class="fa fa-bullhorn"></i></div>
            <div class="notice-content">
                <div class="notice-title">店铺公告</div>
                <p><?php echo nl2br(htmlspecialchars($anounce)); ?></p>
            </div>
        </div>

        <!-- 用户信息面板 -->
        <div class="user-info-panel" id="userInfoPanel" style="display:none;">
            <div class="user-info-header">
                <span class="user-info-title"><i class="fa fa-user-circle"></i> 用户信息</span>
                <span class="user-info-close" onclick="toggleUserInfo()"><i class="fa fa-times"></i></span>
            </div>
            <div class="user-info-body">
                <div class="user-info-row">
                    <span class="user-info-label">店铺名称</span>
                    <span class="user-info-value"><?php echo htmlspecialchars($sitename); ?></span>
                </div>
                <div class="user-info-row">
                    <span class="user-info-label">店铺ID</span>
                    <span class="user-info-value"><?php echo $zid; ?></span>
                </div>
                <div class="user-info-row">
                    <span class="user-info-label">认证状态</span>
                    <span class="user-info-value"><?php echo $verified == 1 ? "<span class=\"badge verify\"><i class=\"fa fa-check-circle\"></i> 已认证</span>" : "<span class=\"badge\" style=\"background:#f2f3f5;color:#86909c;\">未认证</span>"; ?></span>
                </div>
                <div class="user-info-row">
                    <span class="user-info-label">保证金</span>
                    <span class="user-info-value"><?php echo $deposit > 0 ? '<span class="badge deposit"><i class="fa fa-shield"></i> 已缴 ¥' . number_format($deposit, 2) . '</span>' : '<span class="badge nodeposit"><i class="fa fa-shield"></i> 未缴纳</span>'; ?></span>
                </div>
                <div class="user-info-row">
                    <span class="user-info-label">联系QQ</span>
                    <span class="user-info-value"><?php echo $contact_qq ? htmlspecialchars($contact_qq) : "未设置"; ?></span>
                </div>
                <div class="user-info-row">
                    <span class="user-info-label">联系电话</span>
                    <span class="user-info-value"><?php echo $contact_phone ? htmlspecialchars($contact_phone) : "未设置"; ?></span>
                </div>
                <div class="user-info-row">
                    <span class="user-info-label">联系邮箱</span>
                    <span class="user-info-value"><?php echo $contact_email ? htmlspecialchars($contact_email) : "未设置"; ?></span>
                </div>
                <div class="user-info-row">
                    <span class="user-info-label">客服QQ</span>
                    <span class="user-info-value"><?php echo $kfqq ? htmlspecialchars($kfqq) : "未设置"; ?></span>
                </div>
                <div class="user-info-row">
                    <span class="user-info-label">客服微信</span>
                    <span class="user-info-value"><?php echo $kfwx ? htmlspecialchars($kfwx) : "未设置"; ?></span>
                </div>
            </div>
        </div>

                <!-- 分类标签 -->
        <div class="cat-tabs-wrap">
            <div class="cat-tabs">
                <?php
                foreach ($top_cats as $cat) {
                    $cid = $cat['cid'];
                    $count = $DB->getColumn("SELECT COUNT(*) FROM pre_tools WHERE zid=:zid AND cid=:cid AND close=0", [':zid' => $zid, ':cid' => $cid]);
                    $emoji = isset($cat_emoji[$cat['name']]) ? $cat_emoji[$cat['name']] : '📦';
                    $active = ($cid == $cur_cid) ? 'active' : '';
                    echo '<a href="/store.php?zid='.$zid.'&cid='.$cid.'" class="cat-tab '.$active.'">'.$emoji.' '.htmlspecialchars($cat['name']).'</a>';
                }
                ?>
            </div>
            <div class="cat-search">
                <input type="text" placeholder="搜索商品" id="searchInput">
                <button onclick="searchProduct()"><i class="fa fa-search"></i></button>
            </div>
        </div><!-- 子分类筛选 -->
        <?php if (!empty($sub_cats)): ?>
        <div class="sub-cat-filter">
            <a href="/store.php?zid=<?php echo $zid; ?>&cid=<?php echo $cur_cid; ?>" class="sub-cat-item <?php echo $cur_sub == 0 ? 'active' : ''; ?>">
                <div class="name">全部</div>
                <div class="count">共<?php
                    $total = $DB->getColumn("SELECT COUNT(*) FROM pre_tools WHERE zid=:zid AND cid=:cid AND close=0", [':zid' => $zid, ':cid' => $cur_cid]);
                    echo $total;
                ?>种商品</div>
            </a>
            <?php
            foreach ($sub_cats as $sub) {
                $sub_count = $DB->getColumn("SELECT COUNT(*) FROM pre_tools WHERE zid=:zid AND cid=:cid AND close=0", [':zid' => $zid, ':cid' => $sub['cid']]);
                $s_active = ($cur_sub == $sub['cid']) ? 'active' : '';
                echo '<a href="/store.php?zid='.$zid.'&cid='.$cur_cid.'&sub='.$sub['cid'].'" class="sub-cat-item '.$s_active.'">';
                echo '<div class="name">'.htmlspecialchars($sub['name']).'</div>';
                echo '<div class="count">共'.$sub_count.'种商品</div>';
                echo '</a>';
            }
            ?>
        </div>
        <?php endif; ?>

        <!-- 商品列表 -->
        <div class="section-title">选择商品</div>
        <div class="product-grid">
            <?php
            // 构建查询条件
            $where = "zid=:zid AND close=0";
            $params = [':zid' => $zid];
            if ($cur_sub > 0) {
                $where .= " AND cid=:cid";
                $params[':cid'] = $cur_sub;
            } elseif ($cur_cid > 0) {
                // 当前顶级分类下的所有子分类ID
                $sub_ids = $DB->getAll("SELECT cid FROM pre_class WHERE zid=:zid AND pid=:pid", [':zid' => $zid, ':pid' => $cur_cid]);
                $ids = array_column($sub_ids, 'cid');
                $ids[] = $cur_cid;
                $where .= " AND cid IN (".implode(',', array_map('intval', $ids)).")";
            }
            $tools = $DB->query("SELECT * FROM pre_tools WHERE $where ORDER BY sort ASC", $params)->fetchAll();
            if (empty($tools)) {
                echo '<div class="list-end" style="grid-column:1/-1;">暂无商品</div>';
            } else {
                foreach ($tools as $tool) {
                    $tid = $tool['tid'];
                    // 获取价格
                    $price_row = $DB->getRow("SELECT p_0 FROM pre_price WHERE zid=:zid AND kind=1 AND name=:name LIMIT 1", [':zid' => $zid, ':name' => $tool['name']]);
                    $tool_price = $price_row ? $price_row['p_0'] : $tool['price'];
                    // 获取库存：发卡商品取真实未售卡密数（pre_faka），内容商品无限，人工商品不显示库存
                    if ($tool['is_curl'] == 4) {
                        $stock_num = intval($DB->getColumn("SELECT COUNT(*) FROM pre_faka WHERE tid=:tid AND orderid=0", [':tid' => $tid]));
                    } elseif ($tool['is_curl'] == 5) {
                        $stock_num = -1;
                    } else {
                        $stock_num = $tool['stock'] !== null ? intval($tool['stock']) : -1;
                    }
                    ?>
                    <div class="product-card" onclick='openOrderModal(<?php echo json_encode([
                        'tid' => $tid,
                        'name' => $tool['name'],
                        'desc' => $tool['desc'] ?: $tool['name'],
                        'price' => $tool_price,
                        'img' => $tool['shopimg'],
                        'min' => max(1, intval($tool['min'])),
                        'stock' => $stock_num,
                    ]); ?>)'>
                        <div class="product-img">
                            <?php if (!empty($tool['shopimg'])): ?>
                            <img src="<?php echo htmlspecialchars($tool['shopimg']); ?>" alt="<?php echo htmlspecialchars($tool['name']); ?>">
                            <?php else: ?>
                            <i class="fa fa-image" style="font-size:48px;color:#c9cdd4;"></i>
                            <?php endif; ?>
                            <span class="product-tag">v1.0</span>
                        </div>
                        <div class="product-info">
                            <div class="product-name"><?php echo htmlspecialchars($tool['name']); ?></div>
                            <div class="product-price-row">
                                <span class="product-price"><small>¥</small><?php echo $tool_price; ?></span>
                                <?php if ($tool['is_curl'] == 5): ?>
                                <span class="stock-tag in-stock">自动发货</span>
                                <?php elseif ($tool['is_curl'] == 0): ?>
                                <span class="stock-tag">人工发货</span>
                                <?php else: ?>
                                <span class="stock-tag <?php echo $stock_num > 0 ? 'in-stock' : 'out-stock'; ?>"><?php echo $stock_num > 0 ? '现货' : '缺货'; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <div class="list-end" style="padding:14px 0;"><button class="load-more-btn" onclick="showToast('没有更多商品了', 'info')">加载更多</button></div>
    </div>
</div>

<!-- 浮动客服 -->
<div class="float-service">
    <div class="float-service-btn" onclick="showKefu()">
        <div class="float-service-icon"><i class="fa fa-headphones"></i></div>
        <div class="float-service-text">联系商家<br>客服</div>
    </div>
</div>

<!-- 订单确认弹窗 -->
<div class="modal-overlay" id="orderModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">订单确认</div>
            <button class="modal-close" onclick="closeOrderModal()"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-product">
                <div class="modal-product-img" id="mpImg">
                    <i class="fa fa-image" style="font-size:24px;color:#c9cdd4;"></i>
                </div>
                <div class="modal-product-info">
                    <div class="modal-product-name" id="mpName">商品名称</div>
                    <div class="modal-product-desc" id="mpDesc">商品描述</div>
                </div>
            </div>
            <div class="auto-delivery-badge"><i class="fa fa-bolt"></i> 自动发货 · <span id="mpMin">1</span>件起购</div>

            <div class="check-row">
                <label><input type="checkbox" id="useCoupon"> 使用优惠券</label>
                <div class="qty-control">
                    <button class="qty-btn" onclick="changeQty(-1)">−</button>
                    <input type="text" class="qty-input" id="qty" value="1" readonly>
                    <button class="qty-btn" onclick="changeQty(1)">+</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">联系方式</label>
                <input type="text" class="form-input" id="contact" placeholder="请输入联系方式（手机号/邮箱）">
            </div>

            <div class="warning-tip">
                <i class="fa fa-exclamation-circle"></i> 请认真填写联系方式，否则无法收到商品，购买后请保存好订单号。
            </div>

            <div class="price-row">
                <span class="price-label">合计：</span>
                <span class="price-total"><small>¥</small><span id="totalPrice">0.00</span></span>
            </div>

            <div class="pay-methods">
                <?php if (in_array('alipay', $pay_methods_arr)): ?>
                <div class="pay-method alipay active" data-pay="alipay" onclick="selectPay(this)">
                    <div class="icon"><i class="fa fa-credit-card"></i></div>
                    <div class="name">支付宝H5</div>
                </div>
                <?php endif; ?>
                <?php if (in_array('qqpay', $pay_methods_arr)): ?>
                <div class="pay-method qqpay" data-pay="qqpay" onclick="selectPay(this)">
                    <div class="icon"><i class="fa fa-qq"></i></div>
                    <div class="name">QQ支付</div>
                </div>
                <?php endif; ?>
                <?php if (in_array('wxpay', $pay_methods_arr)): ?>
                <div class="pay-method wxpay" data-pay="wxpay" onclick="selectPay(this)">
                    <div class="icon"><i class="fa fa-wechat"></i></div>
                    <div class="name">微信支付</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeOrderModal()">取消</button>
            <button class="btn btn-pay" onclick="submitOrder()">去支付</button>
        </div>
    </div>
</div>

<!-- 7x24自动发货条款弹窗 -->
<div class="modal-overlay terms-modal" id="termsModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">7x24小时自动发货</div>
            <button class="modal-close" onclick="closeTermsModal()"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="terms-content">
                <?php echo $terms_content; ?>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-pay" onclick="closeTermsModal()">我知道了</button>
        </div>
    </div>
</div>

<div class="store-footer">
    <span>Copyright © <?php echo date('Y'); ?> 彩虹云商城 All Rights Reserved.</span>
</div>

<script>
var currentProduct = null;

function openOrderModal(product) {
    currentProduct = product;
    document.getElementById('mpName').textContent = product.name;
    document.getElementById('mpDesc').textContent = product.desc;
    document.getElementById('mpMin').textContent = product.min || 1;
    document.getElementById('qty').value = product.min || 1;
    if (product.img) {
        document.getElementById('mpImg').innerHTML = '<img src="' + product.img + '" alt="">';
    } else {
        document.getElementById('mpImg').innerHTML = '<i class="fa fa-image" style="font-size:24px;color:#c9cdd4;"></i>';
    }
    updateTotal();
    document.getElementById('orderModal').classList.add('show');
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.remove('show');
    currentProduct = null;
}

function changeQty(delta) {
    if (!currentProduct) return;
    var input = document.getElementById('qty');
    var qty = parseInt(input.value) + delta;
    var min = currentProduct.min || 1;
    var max = currentProduct.stock > 0 ? currentProduct.stock : 999;
    if (qty < min) qty = min;
    if (qty > max) qty = max;
    input.value = qty;
    updateTotal();
}

function updateTotal() {
    if (!currentProduct) return;
    var qty = parseInt(document.getElementById('qty').value) || 1;
    var price = parseFloat(currentProduct.price) || 0;
    var total = (qty * price).toFixed(2);
    document.getElementById('totalPrice').textContent = total;
}

function selectPay(el) {
    document.querySelectorAll('.pay-method').forEach(function(m) { m.classList.remove('active'); });
    el.classList.add('active');
}

function submitOrder() {
    if (!currentProduct) return;
    var contact = document.getElementById('contact').value.trim();
    if (!contact) {
        showToast('请输入联系方式', 'warning');
        return;
    }
    var payMethod = document.querySelector('.pay-method.active').dataset.pay;
    var qty = document.getElementById('qty').value;
    var btn = document.querySelector('.btn-pay');
    var oldText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> 提交中...';
    btn.disabled = true;

    var formData = new FormData();
    formData.append('tid', currentProduct.tid);
    formData.append('inputvalue', contact);
    formData.append('num', qty);
    formData.append('hashsalt', '<?php echo $addsalt; ?>');

    fetch('./ajax.php?act=pay', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(function(r) {
        if (!r.ok) {
            return r.text().then(function(t) {
                throw new Error('HTTP ' + r.status + ': ' + t.substring(0, 200));
            });
        }
        return r.text().then(function(t) {
            try { return JSON.parse(t); }
            catch(e) { throw new Error('响应非JSON: ' + t.substring(0, 200)); }
        });
    })
    .then(function(data) {
        btn.innerHTML = oldText;
        btn.disabled = false;
        if (data.code == 0) {
            var redirect = './other/submit.php?type=' + payMethod + '&orderid=' + data.trade_no + '&redirect_url=' + encodeURIComponent('./?mod=query&orderid=' + data.trade_no);
            window.location.href = redirect;
        } else if (data.code == 1) {
            showToast(data.msg || '下单成功！', 'success');
            closeOrderModal();
        } else if (data.code == 4) {
            if (confirm('请登录后再购买，是否现在登录？')) {
                window.location.href = 'user/login.php?back=index';
            }
        } else {
            showToast(data.msg || '下单失败，请重试', 'error');
        }
    })
    .catch(function(err) {
        btn.innerHTML = oldText;
        btn.disabled = false;
        showToast('网络错误，请稍后重试', 'error');
    });
}

// 点击遮罩关闭
document.getElementById('orderModal').addEventListener('click', function(e) {
    if (e.target === this) closeOrderModal();
});
document.getElementById('termsModal').addEventListener('click', function(e) {
    if (e.target === this) closeTermsModal();
});




function toggleUserInfo() {
    var panel = document.getElementById("userInfoPanel");
    if (panel.style.display === "none" || panel.style.display === "") {
        panel.style.display = "block";
    } else {
        panel.style.display = "none";
    }
}
function showContact() {
    var contactText = <?php echo json_encode($contact_text); ?>;
    if (contactText) {
        showToast('商家联系方式：\n' + contactText, 'info', '联系方式');
    } else {
        showToast('商家暂未设置联系方式', 'info');
    }
}

function showKefu() {
    var kfqq = <?php echo json_encode($kfqq); ?>;
    var kfwx = <?php echo json_encode($kfwx); ?>;
    var msg = '商家客服联系方式：\n';
    if (kfqq) msg += 'QQ：' + kfqq + '\n';
    if (kfwx) msg += '微信：' + kfwx + '\n';
    if (!kfqq && !kfwx) msg = '商家暂未设置客服联系方式';
    showToast(msg, 'info', '客服联系方式');
}

// 页面加载显示条款弹窗（关闭后24小时内不再弹出）
window.addEventListener('load', function() {
    var TERMS_KEY = 'caihong_terms_closed';
    var COOLDOWN = 8 * 60 * 60 * 1000; // 8小时
    var lastClosed = parseInt(localStorage.getItem(TERMS_KEY) || '0', 10);
    if (Date.now() - lastClosed < COOLDOWN) return;
    setTimeout(function() {
        document.getElementById('termsModal').classList.add('show');
    }, 800);
});
// 关闭条款弹窗时记录时间
function closeTermsModal() {
    localStorage.setItem('caihong_terms_closed', Date.now().toString());
    document.getElementById('termsModal').classList.remove('show');
}
</script>

<script>
function searchProduct() {
    var q = document.getElementById("searchInput").value.trim();
    if (q) {
        var cards = document.querySelectorAll(".product-card");
        cards.forEach(function(card) {
            var name = card.querySelector(".product-name").textContent.toLowerCase();
            card.style.display = name.indexOf(q.toLowerCase()) >= 0 ? "" : "none";
        });
    } else {
        document.querySelectorAll(".product-card").forEach(function(c) { c.style.display = ""; });
    }
}
document.getElementById("searchInput") && document.getElementById("searchInput").addEventListener("keyup", function(e) {
    if (e.key === "Enter") searchProduct();
});
</script>

<script>
function showToast(msg, type, title) {
    type = type || "info";
    title = title || {success:"操作成功", error:"出错了", warning:"提示", info:"提示"}[type] || "提示";
    var icons = {success:"fa-check-circle", error:"fa-exclamation-circle", warning:"fa-exclamation-triangle", info:"fa-info-circle"};
    var html = '<div class="toast '+type+'"><div class="toast-icon"><i class="fa '+(icons[type]||"fa-info-circle")+'"></i></div><div class="toast-body"><div class="toast-title">'+title+'</div><div class="toast-msg">'+msg+'</div></div><button class="toast-close" onclick="this.parentElement.remove()"><i class="fa fa-times"></i></button></div>';
    var el = document.createElement("div");
    el.innerHTML = html;
    var toast = el.firstChild;
    document.getElementById("toastContainer").appendChild(toast);
    setTimeout(function() {
        toast.classList.add("hide");
        setTimeout(function() { toast.remove(); }, 300);
    }, 3000);
}
</script>

<div class="toast-container" id="toastContainer"></div>
</body>
</html>



