<?php
// 演示店铺页 shop.php —— 彩虹云商城 XHY-01（1:1 复刻参考站店铺页骨架 + 官网级视觉升级）
$shop_logo = 'template/XHY-01/assets/img/logo-full.png?v=20261023';
$shop_name = $conf['sitename'];
?><!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $shop_name; ?>的小店 - <?php echo $shop_name; ?></title>
    <meta name="description" content="彩虹云商城演示店铺，支持卡密、知识付费、资源下载等虚拟商品，7x24小时自动发货。">
    <link rel="icon" href="/favicon.ico">
    <script src="./assets/Agod/jquery.min.js"></script>
    <style>
      :root{
        --navy:#0A1B3D; --navy-2:#0F2A5C; --brand:#144ECA; --brand-2:#165DFF;
        --gold:#D4AF37; --gold-l:#F3D489;
        --ink:#12275A; --text:#1D2129; --muted:#4E5969; --soft:#7A8699;
        --line:#E8EDF5; --bg:#F4F6FB;
      }
      *{box-sizing:border-box}
      html,body{margin:0;padding:0;background:var(--bg);color:var(--text);
        font-family:-apple-system,BlinkMacSystemFont,"PingFang SC","Microsoft YaHei","Helvetica Neue",Arial,sans-serif;
        -webkit-font-smoothing:antialiased}
      a{text-decoration:none;color:inherit}
      img{display:block}

      /* ============ 顶部导航 ============ */
      .top-nav{position:fixed;top:0;left:0;right:0;z-index:100;height:60px;
        background:rgba(255,255,255,.92);backdrop-filter:saturate(180%) blur(12px);
        -webkit-backdrop-filter:saturate(180%) blur(12px);
        border-bottom:1px solid rgba(13,25,54,.08)}
      .nav-inner{max-width:1200px;height:100%;margin:0 auto;padding:0 16px;display:flex;align-items:center}
      .nav-logo img{height:30px;width:auto}
      .nav-links{display:flex;align-items:center;margin-left:36px;gap:30px;overflow-x:auto;scrollbar-width:none;flex:1;white-space:nowrap}
      .nav-links::-webkit-scrollbar{display:none}
      .nav-links a{font-size:14.5px;color:var(--muted);position:relative;padding:6px 0;transition:color .2s}
      .nav-links a:hover{color:var(--brand)}
      .nav-links a.is-curr{color:var(--brand);font-weight:700}
      .nav-drop{position:relative}
      .nav-drop .drop-menu{position:absolute;top:calc(100% + 8px);left:50%;transform:translateX(-50%) translateY(6px);
        background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 16px 40px rgba(10,31,77,.14);
        padding:7px;min-width:128px;opacity:0;visibility:hidden;transition:.2s;display:flex;flex-direction:column;gap:2px}
      .nav-drop:hover .drop-menu{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0)}
      .nav-drop .drop-menu a{padding:8px 14px;border-radius:8px;font-size:13.5px;white-space:nowrap}
      .nav-drop .drop-menu a:hover{background:#F2F6FF;color:var(--brand)}
      .nav-right{margin-left:auto}
      .btn-order{display:inline-flex;align-items:center;gap:6px;background:var(--brand-2);color:#fff;
        border:none;border-radius:999px;padding:9px 20px;font-size:14px;cursor:pointer;transition:.2s}
      .btn-order:hover{background:#0F43B4;box-shadow:0 8px 20px rgba(22,93,255,.28)}
      .btn-order svg{width:15px;height:15px}

      /* ============ 店铺 Hero（威严感主视觉）============ */
      .shop-hero{margin-top:60px;position:relative;color:#fff;overflow:hidden;
        background:
          radial-gradient(860px 400px at 10% -8%, rgba(72,138,255,.34), transparent 62%),
          radial-gradient(680px 360px at 96% 12%, rgba(212,175,55,.13), transparent 58%),
          linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 54%, #173F8F 100%)}
      .hero-inner{max-width:1200px;margin:0 auto;padding:52px 16px 0;display:flex;gap:26px;align-items:flex-start}
      .hero-avatar{width:88px;height:88px;flex:none;border-radius:50%;border:3px solid rgba(255,255,255,.92);
        background:#fff;padding:2px;box-shadow:0 16px 38px rgba(3,14,36,.5);cursor:pointer;transition:.25s}
      .hero-avatar:hover{transform:translateY(-2px);box-shadow:0 20px 44px rgba(3,14,36,.6)}
      .hero-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover}
      .hero-main{flex:1;min-width:0}
      .hero-eyebrow{display:flex;align-items:center;gap:9px;font-size:12.5px;letter-spacing:2.5px;color:rgba(255,255,255,.55);text-transform:uppercase}
      .hero-eyebrow::before{content:"";width:26px;height:1px;background:var(--gold-l);opacity:.9}
      .hero-shop-name{margin-top:9px;font-size:27px;font-weight:800;letter-spacing:.5px;color:#fff;line-height:1.25}
      .hero-badges{display:flex;flex-wrap:wrap;gap:10px;margin-top:14px}
      .hbadge{display:inline-flex;align-items:center;gap:5px;font-size:12px;padding:4px 12px;border-radius:999px}
      .hbadge svg{width:13px;height:13px;flex:none}
      .hbadge.gold{color:var(--gold-l);border:1px solid rgba(212,175,55,.55);background:rgba(212,175,55,.12)}
      .hbadge.green{color:#7EE2A8;border:1px solid rgba(56,199,133,.45);background:rgba(56,199,133,.12)}
      .hbadge.blue{color:#9DC0FF;border:1px solid rgba(120,170,255,.48);background:rgba(120,170,255,.12)}
      .hero-notice{margin-top:18px;display:flex;gap:12px;align-items:flex-start;max-width:780px;
        padding:13px 16px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.16);border-radius:12px}
      .hero-notice .n-icon{flex:none;margin-top:1px;width:16px;height:16px;color:var(--gold-l)}
      .hero-notice .n-title{font-size:13.5px;font-weight:700;color:rgba(255,255,255,.92)}
      .hero-notice .n-text{margin-top:3px;font-size:12.5px;line-height:1.75;color:rgba(255,255,255,.7)}
      .hero-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:20px}
      .hbtn{display:inline-flex;align-items:center;gap:7px;font-size:13.5px;font-weight:600;
        padding:9px 22px;border-radius:10px;cursor:pointer;transition:.2s;border:none}
      .hbtn svg{width:15px;height:15px}
      .hbtn.solid{background:#fff;color:var(--brand);box-shadow:0 10px 24px rgba(4,16,42,.35)}
      .hbtn.solid:hover{transform:translateY(-1px);box-shadow:0 14px 30px rgba(4,16,42,.45)}
      .hbtn.ghost{background:rgba(255,255,255,.06);color:#fff;border:1px solid rgba(255,255,255,.42)}
      .hbtn.ghost:hover{background:rgba(255,255,255,.14)}

      /* 统计带 */
      .hero-stats{max-width:1200px;margin:36px auto 0;padding:0 16px}
      .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);border-top:1px solid rgba(255,255,255,.15)}
      .stat{position:relative;padding:22px 8px 30px;text-align:center}
      .stat+.stat::before{content:"";position:absolute;left:0;top:50%;transform:translateY(-50%);
        width:1px;height:44px;background:rgba(255,255,255,.15)}
      .stat-num{font-size:30px;font-weight:800;color:#fff;font-variant-numeric:tabular-nums;line-height:1.15}
      .stat-num .suf{font-size:15px;font-weight:700;margin-left:2px;color:rgba(255,255,255,.85)}
      .stat-label{margin-top:7px;font-size:13px;letter-spacing:1.5px;color:rgba(255,255,255,.6)}

      /* ============ 商品区 ============ */
      .goods-section{position:relative;z-index:5;max-width:1200px;margin:-26px auto 0;padding:0 16px}
      .goods-card{background:#fff;border-radius:16px;box-shadow:0 22px 54px rgba(10,31,77,.13);padding:24px 28px 26px}
      .section-title{display:flex;align-items:center;gap:10px;font-size:17px;font-weight:800;color:var(--ink)}
      .section-title::before{content:"";width:4px;height:18px;border-radius:2px;background:var(--brand-2)}
      .goods-toolbar{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-top:14px;flex-wrap:wrap}
      .shop-tabs{display:flex;gap:26px;border-bottom:2px solid #EEF2F8}
      .shop-tab{padding:10px 2px 12px;font-size:14.5px;color:var(--muted);cursor:pointer;
        border-bottom:2px solid transparent;margin-bottom:-2px;font-weight:500;transition:color .2s;user-select:none}
      .shop-tab:hover{color:var(--brand)}
      .shop-tab.is-active{color:var(--brand);font-weight:700;border-bottom-color:var(--brand)}
      .search-box{display:flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:10px;
        padding:8px 13px;width:230px;background:#F8FAFD;transition:.2s}
      .search-box:focus-within{border-color:#9DBDFB;background:#fff;box-shadow:0 0 0 3px rgba(22,93,255,.1)}
      .search-box input{border:none;outline:none;background:transparent;flex:1;font-size:13px;color:var(--text)}
      .search-box svg{width:15px;height:15px;color:#94A3BC;flex:none;cursor:pointer}

      /* 分类筛选药丸 */
      .fl_box_xz{margin:16px 0 4px;max-height:300px;overflow:auto}
      .fl_box_leng{position:relative;display:inline-block;vertical-align:middle;min-width:150px;cursor:pointer;
        background:#F8FAFC;border:1px solid #EEF1F6;border-radius:10px;margin:0 12px 10px 0;padding:12px 20px 0;
        height:67px;user-select:none;transition:.2s}
      .fl_box_leng:hover{border-color:#C9D8F6}
      .fl_box_leng div:first-child{font-size:12px;color:#545454;font-weight:500;display:flex;align-items:center}
      .fl_box_leng div:nth-child(2){opacity:.76;color:#999;font-size:12px;margin-top:7px}
      .fl_box_leng .dui{position:absolute;right:-5px;bottom:-17px;width:17px;height:17px}
      .fl_box_leng_xz{background:linear-gradient(-45deg,#3369FF,#3798F7);border-color:#3580FB;
        box-shadow:0 7px 14px rgba(51,105,255,.28)}
      .fl_box_leng_xz div:first-child,.fl_box_leng_xz div:nth-child(2){color:#fff}

      /* 商品卡片 */
      .goods_list{display:grid;grid-template-columns:repeat(auto-fill,minmax(208px,1fr));gap:14px;margin-top:18px}
      .goods_item{background:#fff;border:1px solid #EBEFF5;border-radius:12px;overflow:hidden;cursor:pointer;transition:.25s}
      .goods_item:hover{transform:translateY(-3px);border-color:#9DBDFB;box-shadow:0 14px 30px rgba(20,78,202,.13)}
      .goods_item .image{height:200px;overflow:hidden;background:#F2F5FA}
      .goods_item .image img{width:100%;height:100%;object-fit:cover;transition:transform .35s}
      .goods_item:hover .image img{transform:scale(1.04)}
      .goods_item .info{padding:13px 15px 15px}
      .goods_item .name{font-size:13px;font-weight:500;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
      .goods_item .subinfo{display:flex;align-items:center;justify-content:space-between;margin-top:11px;gap:8px;flex-wrap:wrap}
      .goods-price{display:flex;align-items:baseline;color:var(--brand);font-weight:700}
      .goods-price .currency{font-size:12px}
      .goods-price .nowPrice{font-size:18px;font-variant-numeric:tabular-nums}
      .discounts .stock{padding:2px 8px;font-size:10.5px;border-radius:4px}
      .stock.rank1{color:#2275FF;background:#F0F7FF}
      .stock.rank0{color:#FF4F11;background:#FFE5DC}
      .empty-goods{text-align:center;color:#86909C;padding:44px 0;grid-column:1/-1;font-size:13.5px}
      .load-more{padding:16px 0 2px;text-align:center}
      .load-more span{display:inline-block;font-size:13px;color:var(--soft);cursor:pointer;padding:8px 26px;
        border:1px solid var(--line);border-radius:999px;transition:.2s}
      .load-more span:hover{color:var(--brand);border-color:#9DBDFB;background:#F6F9FF}
      .toast-x{position:fixed;left:50%;top:18%;transform:translateX(-50%);z-index:2000;
        background:rgba(17,32,51,.9);color:#fff;padding:12px 22px;border-radius:10px;font-size:13.5px;
        box-shadow:0 10px 26px rgba(0,0,0,.2);transition:opacity .3s}

      /* ============ 服务保障 ============ */
      .promise-section{max-width:1200px;margin:22px auto 0;padding:0 16px}
      .promise-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
      .promise{display:flex;align-items:flex-start;gap:13px;background:#fff;border:1px solid #E9EEF6;
        border-radius:14px;padding:20px 18px;transition:.25s}
      .promise:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(10,31,77,.09)}
      .promise-icon{flex:none;width:42px;height:42px;border-radius:12px;display:grid;place-items:center;
        background:linear-gradient(135deg,#EAF2FF,#F5F9FF);color:var(--brand)}
      .promise-icon svg{width:21px;height:21px}
      .promise-title{font-size:14.5px;font-weight:700;color:var(--ink)}
      .promise-desc{margin-top:4px;font-size:12px;color:var(--soft);line-height:1.65}

      /* ============ Footer ============ */
      .site-footer{margin-top:48px;background:var(--navy);color:rgba(255,255,255,.6)}
      .footer-inner{max-width:1200px;margin:0 auto;padding:28px 16px;text-align:center}
      .footer-brand{font-size:13.5px;color:rgba(255,255,255,.78);font-weight:600}
      .footer-brand a:hover{color:#fff}
      .footer-copy{margin-top:8px;font-size:12px;color:rgba(255,255,255,.42);line-height:1.9}

      /* ============ 客服浮窗 & 弹层 ============ */
      .kefu-fab{position:fixed;right:24px;bottom:92px;z-index:120;width:54px;height:54px;border-radius:50%;
        background:#fff;border:1px solid #E1E8F5;box-shadow:0 12px 28px rgba(10,31,77,.18);
        display:grid;place-items:center;cursor:pointer;transition:.25s}
      .kefu-fab:hover{transform:translateY(-3px);box-shadow:0 16px 34px rgba(20,78,202,.24)}
      .kefu-fab svg{width:23px;height:23px;color:var(--brand)}
      .kefu-fab .fab-tip{position:absolute;right:64px;top:50%;transform:translateY(-50%);white-space:nowrap;
        background:rgba(17,32,51,.88);color:#fff;font-size:12px;padding:5px 12px;border-radius:8px;
        opacity:0;visibility:hidden;transition:.2s}
      .kefu-fab:hover .fab-tip{opacity:1;visibility:visible}
      #kefuModal{display:none;position:fixed;inset:0;z-index:1000}
      #kefuModal .mask{position:absolute;inset:0;background:rgba(6,16,36,.5)}
      #kefuModal .panel{position:absolute;left:50%;top:20%;transform:translateX(-50%);background:#fff;
        border-radius:14px;width:min(92vw,360px);padding:24px;box-shadow:0 22px 54px rgba(10,31,77,.25)}
      #kefuModal h3{margin:0 0 14px;font-size:17px;color:var(--text)}
      #kefuModal .rows{line-height:2.15;color:var(--muted);font-size:14px}
      #kefuModal .btn{display:block;margin-top:16px;width:100%;background:var(--brand-2);color:#fff;border:none;
        border-radius:10px;padding:11px;font-size:14px;cursor:pointer}
      #kefuModal .btn:hover{background:#0F43B4}

      /* 7x24 购买提示弹窗 */
      #modalMask{position:fixed;inset:0;background:rgba(6,16,36,.45);z-index:1000;display:none}
      .mark_notice{width:400px;position:absolute;left:50%;top:20%;margin-left:-200px;overflow:hidden;
        border-radius:16px 16px 12px 12px;z-index:1001;display:none;animation:pop_in .35s ease}
      @keyframes pop_in{from{transform:scale(.86);opacity:0}to{transform:scale(1);opacity:1}}
      .mark_notice .mark_notice_top{height:110px;background:linear-gradient(135deg,#165DFF,#0F3BB8);color:#fff;
        padding-top:28px;text-align:center;letter-spacing:1px;position:relative;box-sizing:border-box}
      .mark_notice .mark_notice_top .t{font-size:24px;font-weight:800}
      .mark_notice .mark_notice_top .title{font-size:20px;font-weight:800}
      .mark_notice .mark_notice_top .p{display:inline-block;padding-top:9px;font-size:13px;color:rgba(255,255,255,.85)}
      .mark_notice .mark_notice_top .close{position:absolute;top:14px;right:16px;width:26px;cursor:pointer}
      .mark_notice .mark_notice_bottom{padding:16px 18px 0;background:#fff}
      .mark_notice .sh_notice{color:#343434;font-size:13px;line-height:1.8;max-height:300px;overflow:auto;word-wrap:break-word}
      .mark_notice .exp{display:flex;gap:14px;justify-content:center;align-items:center;padding:15px 0 18px;background:#fff}
      .mark_notice .exp .jump{color:#fff;background:var(--brand-2);padding:8px 18px;border-radius:999px;font-size:13.5px;min-width:88px;text-align:center}
      .mark_notice .exp .close{color:var(--brand);background:#F0F7FF;padding:8px 18px;border-radius:999px;font-size:13.5px;min-width:88px;text-align:center;cursor:pointer}

      /* ============ 响应式 ============ */
      @media(max-width:960px){
        .nav-links{margin-left:20px;gap:20px}
        .hero-inner{flex-direction:column;gap:18px;padding-top:40px}
        .stats-grid{grid-template-columns:repeat(2,1fr)}
        .stat:nth-child(3)::before{display:none}
        .promise-grid{grid-template-columns:repeat(2,1fr)}
        .goods_list{grid-template-columns:repeat(auto-fill,minmax(160px,1fr))}
        .goods_item .image{height:160px}
        .goods-card{padding:20px 18px 22px}
      }
      @media(max-width:560px){
        .hero-shop-name{font-size:21px}
        .stat-num{font-size:24px}
        .stat{padding:16px 4px 20px}
        .promise-grid{grid-template-columns:1fr}
        .goods_list{grid-template-columns:repeat(2,1fr);gap:10px}
        .goods_item .image{height:120px}
        .hero-actions .hbtn{padding:8px 16px;font-size:13px}
        .search-box{width:100%}
        .goods-toolbar{flex-direction:column;align-items:stretch}
        .shop-tabs{gap:16px}
      }
    </style>
</head>
<body>

<!-- 顶部导航 -->
<div class="top-nav">
    <div class="nav-inner">
        <a href="./" class="nav-logo"><img alt="<?php echo $shop_name; ?>" src="<?php echo $shop_logo; ?>"></a>
        <div class="nav-links">
            <a href="./">网站首页</a>
            <a href="./?mod=goods-release">禁售目录</a>
            <a href="./?mod=notice">最新公告</a>
            <a href="./?mod=about">公司介绍</a>
            <a href="./?mod=shop" class="is-curr">演示店铺</a>
            <div class="nav-drop">
                <a href="javascript:;">黑名单查询
                    <svg style="width:11px;height:11px;vertical-align:-1px" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4"><path d="M39.6 17.443 24.043 33 8.487 17.443"/></svg>
                </a>
                <div class="drop-menu">
                    <a href="./?mod=riskcontrol">风控名单</a>
                    <a href="./?mod=complaints">商家投诉率</a>
                    <a href="./?mod=bannedrecords">买家黑名单</a>
                </div>
            </div>
        </div>
        <div class="nav-right">
            <button class="btn-order" type="button" onclick="location.href='./?mod=order'">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="butt"><path d="M33.072 33.071c6.248-6.248 6.248-16.379 0-22.627-6.249-6.249-16.38-6.249-22.628 0-6.248 6.248-6.248 16.379 0 22.627 6.248 6.248 16.38 6.248 22.628 0Zm0 0 8.485 8.485"/></svg>
                订单查询
            </button>
        </div>
    </div>
</div>

<!-- 店铺 Hero -->
<div class="shop-hero">
    <div class="hero-inner">
        <div class="hero-avatar" onclick="xKefu()">
            <img alt="<?php echo $shop_name; ?>" src="<?php echo $shop_logo; ?>">
        </div>
        <div class="hero-main">
            <div class="hero-eyebrow">Official Store</div>
            <div class="hero-shop-name"><?php echo $shop_name; ?></div>
            <div class="hero-badges">
                <span class="hbadge gold">
                    <svg viewBox="0 0 48 48" fill="currentColor"><path d="M24 2 4 12v13c0 11.1 8.5 20.2 20 23 11.5-2.8 20-11.9 20-23V12L24 2zm0 7.5 13 6.3v9.4c0 8.1-5.6 15-13 17.4-7.4-2.4-13-9.3-13-17.4v-9.4l13-6.3z"/><path d="M21.8 29.8 15 23l2.6-2.6 4.2 4.2 8.6-8.6L33 18.6l-11.2 11.2z"/></svg>
                    已认证
                </span>
                <span class="hbadge green">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4"><path d="M6 8h36v26H23l-9 8v-8H6V8z"/></svg>
                    保证金 ¥500
                </span>
                <span class="hbadge blue">
                    <svg viewBox="0 0 48 48" fill="currentColor"><path d="M26 4 12 26h10l-2 18 16-24H26l2-16z"/></svg>
                    7×24 自动发货
                </span>
            </div>
            <div class="hero-notice">
                <svg class="n-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="butt"><path d="M14 20v-6a10 10 0 0 1 20 0v6M8 20h32v18a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V20z"/></svg>
                <div>
                    <div class="n-title">店铺公告</div>
                    <div class="n-text"><?php echo $shop_name; ?>支持卡密、知识付费、资源下载等商品，7×24小时自动发货，费率低至2%-4%，欢迎正规商户入驻！</div>
                </div>
            </div>
            <div class="hero-actions">
                <button class="hbtn solid" type="button" onclick="xKefu()">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="butt"><path d="M15 20h18m-18 9h9M7 41h17.63C33.67 41 41 33.67 41 24.63V24c0-9.389-7.611-17-17-17S7 14.611 7 24v17Z"/></svg>
                    联系TA
                </button>
                <button class="hbtn ghost" type="button" onclick="xShare()">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="butt"><path d="M18 20h-7a1 1 0 0 0-1 1v20a1 1 0 0 0 1 1h26a1 1 0 0 0 1-1V21a1 1 0 0 0-1-1h-7m2.368-5.636L24.004 6l-8.364 8.364M24.003 28V6.604" stroke-miterlimit="16"/></svg>
                    分享店铺
                </button>
            </div>
        </div>
    </div>
    <div class="hero-stats">
        <div class="stats-grid">
            <div class="stat">
                <div class="stat-num"><span data-counter data-count="4">0</span><span class="suf">年</span></div>
                <div class="stat-label">稳定运营</div>
            </div>
            <div class="stat">
                <div class="stat-num"><span data-counter data-count="36820">0</span><span class="suf">+</span></div>
                <div class="stat-label">服务终端用户</div>
            </div>
            <div class="stat">
                <div class="stat-num">7×24<span class="suf">h</span></div>
                <div class="stat-label">自动发货</div>
            </div>
            <div class="stat">
                <div class="stat-num"><span data-counter data-count="24">0</span><span class="suf">h</span></div>
                <div class="stat-label">售后响应</div>
            </div>
        </div>
    </div>
</div>

<!-- 商品区 -->
<div class="goods-section">
    <div class="goods-card">
        <div class="section-title">选择商品</div>
        <div class="goods-toolbar">
            <div class="shop-tabs">
                <div class="shop-tab is-active" data-tab="0" onclick="xTab(0,this)">⚡️ 卡密<span id="tabCnt0">0</span></div>
                <div class="shop-tab" data-tab="1" onclick="xTab(1,this)">☘️ 知识<span id="tabCnt1">0</span></div>
                <div class="shop-tab" data-tab="2" onclick="xTab(2,this)">💎 资源<span id="tabCnt2">0</span></div>
            </div>
            <div class="search-box">
                <input type="text" id="searchKw" placeholder="搜索商品" onkeydown="if(event.key==='Enter'){xSearch()}">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="butt" onclick="xSearch()"><path d="M33.072 33.071c6.248-6.248 6.248-16.379 0-22.627-6.249-6.249-16.38-6.249-22.628 0-6.248 6.248-6.248 16.379 0 22.627 6.248 6.248 16.38 6.248 22.628 0Zm0 0 8.485 8.485"/></svg>
            </div>
        </div>
        <div class="fl_box_xz" id="classPills"></div>
        <div class="goods_list" id="goodsList"></div>
        <div class="load-more" id="loadMoreWrap"><span onclick="xMore()">加载更多</span></div>
    </div>
</div>

<!-- 服务保障 -->
<div class="promise-section">
    <div class="promise-grid">
        <div class="promise">
            <div class="promise-icon">
                <svg viewBox="0 0 48 48" fill="currentColor"><path d="M26 4 12 26h10l-2 18 16-24H26l2-16z"/></svg>
            </div>
            <div>
                <div class="promise-title">秒级自动发货</div>
                <div class="promise-desc">在线支付成功后，系统即时交付，全程无需等待。</div>
            </div>
        </div>
        <div class="promise">
            <div class="promise-icon">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4"><path d="M24 4 8 11v12c0 10 6.8 19.2 16 21 9.2-1.8 16-11 16-21V11L24 4z"/><path d="M16 23l6 6 10-11" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <div class="promise-title">平台担保交易</div>
                <div class="promise-desc">交易资金由平台监管结算，安全保障每一笔订单。</div>
            </div>
        </div>
        <div class="promise">
            <div class="promise-icon">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4"><circle cx="24" cy="24" r="8"/><path d="M24 8v6M24 34v6M8 24h6M34 24h6M12.7 12.7l4.2 4.2M31.1 31.1l4.2 4.2M35.3 12.7l-4.2 4.2M16.9 31.1l-4.2 4.2" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="promise-title">保证金保障</div>
                <div class="promise-desc">商家缴纳经营保证金，违规行为平台先行赔付。</div>
            </div>
        </div>
        <div class="promise">
            <div class="promise-icon">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"><path d="M14 26v-4a10 10 0 0 1 20 0v4"/><rect x="10" y="24" width="7" height="14" rx="3.5"/><rect x="31" y="24" width="7" height="14" rx="3.5"/></svg>
            </div>
            <div>
                <div class="promise-title">全天候售后</div>
                <div class="promise-desc">7×24 小时售后通道开放，最快速度响应处理。</div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand"><a href="./">Powered by <?php echo $shop_name; ?></a></div>
        <?php
$__icp = trim((string)($conf['icp_number'] ?? ''));
$__police = trim((string)($conf['police_number'] ?? ''));
$__license = trim((string)($conf['icp_license'] ?? ''));
$__items = array();
if ($__icp !== '') { $__items[] = '<span>' . htmlspecialchars($__icp) . '</span>'; }
if ($__police !== '') { $__items[] = '<span>' . htmlspecialchars($__police) . '</span>'; }
if ($__license !== '') { $__items[] = '<span>' . htmlspecialchars($__license) . '</span>'; }
if ($__items) {
?>
        <div class="footer-copy"><?php echo implode(' | ', $__items); ?></div>
<?php } ?>
    </div>
</footer>

<!-- 客服浮窗 -->
<div class="kefu-fab" onclick="xKefu()">
    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round"><path d="M14 26v-4a10 10 0 0 1 20 0v4"/><rect x="10" y="24" width="7" height="14" rx="3.5"/><rect x="31" y="24" width="7" height="14" rx="3.5"/></svg>
    <span class="fab-tip">联系商家客服</span>
</div>

<!-- 客服弹层 -->
<div id="kefuModal">
    <div class="mask" onclick="document.getElementById('kefuModal').style.display='none'"></div>
    <div class="panel">
        <h3>联系客服</h3>
        <div class="rows">
            <div>客服QQ：<b>1560292</b></div>
            <div>客服微信：<b>WalTerTeChStuDio</b></div>
            <div>客服电话：<b>+85256199579</b></div>
            <div>客服邮箱：<b>jackwalter66@163.com</b></div>
        </div>
        <button class="btn" type="button" onclick="document.getElementById('kefuModal').style.display='none'">我知道了</button>
    </div>
</div>

<!-- 7x24 自动发货提示弹窗 -->
<div id="modalMask" onclick="closeModal()"></div>
<div class="mark_notice" id="markNotice">
    <div class="mark_notice_top">
        <div class="title"><span class="t">「7×24」</span> 小时自动发货</div>
        <div class="p">选购商品 · 在线付款 · 系统自动发货</div>
        <img src="data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' d='M3 3l10 10M13 3L3 13'/%3E%3C/svg%3E" class="close" onclick="closeModal()">
    </div>
    <div class="mark_notice_bottom">
        <div class="sh_notice">
            <p><font color="#f9963b"><strong>温馨提示：</strong><strong>请务必仔细阅读以下内容！</strong></font></p>
            <p style="line-height:1.7"><font color="#1c487f">1、本平台仅提供寄售服务，非销售商，建议先咨询商家软件使用方法和用途后购买。</font><br><font color="#8baa4a">2、购买后有任何疑问请先联系商家处理，店铺和订单都有商家联系方式，如果联系不上或商家拒绝售后，再申请售后，平台介入处理。</font></p>
            <p style="line-height:1.7">3、虚拟商品具有可复制性，商品没有问题的情况下，不支持无理由退款，请确定需要再下单。</p>
            <p><font color="#c24f4a"><strong>售后须知：</strong>所有订单资金将在平台停留24小时，超过此时间资金将自动结算给商家。如您支付后遇到商品问题，请务必在24小时内申请售后，逾期后平台将不再介入处理。</font></p>
        </div>
    </div>
    <div class="exp">
        <a class="jump" href="./?mod=order">申请售后</a>
        <div class="close" onclick="closeModal()">关闭</div>
    </div>
</div>

<script>
(function () {
    var DUI = "data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23fff' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round' d='M2.5 8.5l3.5 3.5 7-8'/%3E%3C/svg%3E";
    var state = { classes: [], tools: [], filtered: [], tab: 0, cid: -1, kw: '', shown: 24 };

    function toast(msg) {
        var t = document.createElement('div');
        t.className = 'toast-x'; t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 350); }, 2000);
    }
    window.xShare = function () {
        var url = location.href;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function () { toast('店铺链接已复制'); });
        } else { toast('店铺链接：' + url); }
    };
    window.xKefu = function () { document.getElementById('kefuModal').style.display = 'block'; };
    window.closeModal = function () {
        document.getElementById('markNotice').style.display = 'none';
        document.getElementById('modalMask').style.display = 'none';
        try { localStorage.setItem('shop_notice_close_ts', String(Date.now())); } catch (e) {}
    };

    window.xTab = function (i, el) {
        state.tab = i;
        document.querySelectorAll('.shop-tab').forEach(function (t) { t.classList.remove('is-active'); });
        el.classList.add('is-active');
        renderGoods();
    };

    window.pickClass = function (cid, el) {
        state.cid = cid;
        document.querySelectorAll('#classPills .fl_box_leng').forEach(function (p) {
            p.classList.remove('fl_box_leng_xz');
            var d = p.querySelector('.dui'); if (d) d.remove();
        });
        el.classList.add('fl_box_leng_xz');
        el.insertAdjacentHTML('beforeend', '<img class="dui" src="' + DUI + '">');
        state.shown = 24; renderGoods();
    };

    window.xSearch = function () {
        state.kw = document.getElementById('searchKw').value.trim();
        state.shown = 24; renderGoods();
    };
    window.xMore = function () { state.shown += 24; renderGoods(); };
    window.buyGo = function (tid) { location.href = './?tid=' + tid + '#xShop'; };

    function applyFilter() {
        var list = state.tab === 0 ? state.tools.slice() : [];
        if (state.cid > 0) list = list.filter(function (t) { return t.cid == state.cid; });
        if (state.kw) list = list.filter(function (t) { return (t.name || '').indexOf(state.kw) > -1; });
        state.filtered = list;
        document.getElementById('tabCnt0').textContent = state.tools.length;
        document.getElementById('tabCnt1').textContent = 0;
        document.getElementById('tabCnt2').textContent = 0;
    }

    function renderGoods() {
        applyFilter();
        var box = document.getElementById('goodsList');
        var list = state.filtered.slice(0, state.shown);
        if (!list.length) {
            box.innerHTML = '<div class="empty-goods">暂无商品</div>';
            document.getElementById('loadMoreWrap').style.display = 'none';
            return;
        }
        document.getElementById('loadMoreWrap').style.display = state.filtered.length > state.shown ? '' : 'none';
        var html = '';
        list.forEach(function (t) {
            var hasImg = t.shopimg && t.shopimg.indexOf('http') === 0 ? 1 : 0;
            var img = hasImg ? t.shopimg : 'template/XHY-01/assets/img/logo-full.png';
            var close = parseInt(t.close || 0) === 1 || parseInt(t.stock || t.value || 0) <= 0;
            var stock = close ? '<span class="stock rank0">缺货</span>' : '<span class="stock rank1">有货</span>';
            html += '<div class="goods_item" onclick="buyGo(' + t.tid + ')">'
                 +  '<div class="image"><img src="' + img + '" alt="' + (t.name || '') + '" loading="lazy"></div>'
                 +  '<div class="info"><div class="name">' + (t.name || '') + '</div>'
                 +  '<div class="subinfo"><div class="goods-price"><span class="currency">￥</span><span class="nowPrice">' + t.price + '</span></div>'
                 +  '<div class="discounts">' + stock + '</div></div></div></div>';
        });
        box.innerHTML = html;
    }

    function renderPills() {
        var box = document.getElementById('classPills');
        var html = '<div class="fl_box_leng fl_box_leng_xz" onclick="pickClass(-1,this)"><div> 全部</div><div>共' + state.tools.length + '种商品</div><img class="dui" src="' + DUI + '"></div>';
        state.classes.forEach(function (c) {
            html += '<div class="fl_box_leng" onclick="pickClass(' + c.cid + ',this)"><div> ' + c.name + '</div><div id="pcnt_' + c.cid + '">共-种商品</div></div>';
        });
        box.innerHTML = html;
        state.classes.forEach(function (c) {
            fetch('./ajax.php?act=gettool&cid=' + c.cid).then(function (r) { return r.json(); }).then(function (d) {
                var n = (d.code == 0 && d.data) ? d.data.length : 0;
                var el = document.getElementById('pcnt_' + c.cid);
                if (el) el.textContent = '共' + n + '种商品';
            }).catch(function () {});
        });
    }

    function loadAll() {
        var cids = state.classes.map(function (c) { return c.cid; });
        var jobs = cids.length ? cids.map(function (cid) {
            return fetch('./ajax.php?act=gettool&cid=' + cid).then(function (r) { return r.json(); });
        }) : [Promise.resolve({ code: 0, data: [] })];
        Promise.all(jobs).then(function (rs) {
            var seen = {};
            state.tools = [];
            rs.forEach(function (d) {
                if (d.code == 0 && d.data) d.data.forEach(function (t) {
                    if (!seen[t.tid]) { seen[t.tid] = 1; state.tools.push(t); }
                });
            });
            state.tools.sort(function (a, b) { return (a.sort || 0) - (b.sort || 0); });
            renderPills(); renderGoods();
        });
    }

    fetch('./ajax.php?act=getclass').then(function (r) { return r.json(); }).then(function (d) {
        if (d.code == 0 && d.data) state.classes = d.data;
    }).catch(function () {}).then(function () { loadAll(); });

    // Hero 统计数字滚动（与主站一致）
    function formatNumber(v) { return Math.floor(v).toLocaleString('zh-CN'); }
    function animateCounter(el) {
        var target = parseFloat(String(el.getAttribute('data-count')).replace(/,/g, '')) || 0;
        var t0 = null, dur = 1200;
        function tick(ts) {
            if (!t0) t0 = ts;
            var p = Math.min((ts - t0) / dur, 1);
            var e = 1 - Math.pow(1 - p, 3);
            el.textContent = formatNumber(target * e);
            if (p < 1) requestAnimationFrame(tick); else el.textContent = formatNumber(target);
        }
        requestAnimationFrame(tick);
    }
    var counters = document.querySelectorAll('[data-counter]');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { animateCounter(en.target); io.unobserve(en.target); }
            });
        }, { threshold: 0.3 });
        counters.forEach(function (c) { io.observe(c); });
    } else {
        counters.forEach(animateCounter);
    }

    // 页面加载后弹出 7×24 提示（用户关闭后 8 小时内不再弹出）
    var NOTICE_GAP = 8 * 60 * 60 * 1000;
    function showNotice() {
        setTimeout(function () {
            document.getElementById('modalMask').style.display = 'block';
            document.getElementById('markNotice').style.display = 'block';
        }, 600);
    }
    try {
        var lastClose = parseInt(localStorage.getItem('shop_notice_close_ts') || '0', 10);
        if (!lastClose || Date.now() - lastClose >= NOTICE_GAP) showNotice();
    } catch (e) { showNotice(); }
})();
</script>
</body>
</html>
