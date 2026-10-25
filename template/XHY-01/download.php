<?php
// APP 下载页 download.php —— 影虹云商城 XHY-01（mock 手机模型展示）
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>APP下载 - <?php echo $conf['sitename']; ?></title>
    <meta name="description" content="订单、商品、售后、资金和经营数据随时掌握，让商户在手机上也能高效处理发卡网业务。">
    <meta name="keywords" content="APP下载,商户APP,发卡网APP,彩虹云商城">
    <style>
        :root {
            --primary: #155EEF;
            --accent: #FF7A1A;
            --green: #08A65A;
            --ink: #172033;
            --text: #526071;
            --muted: #8492A6;
            --line: #E6EAF0;
            --soft: #F6F8FB;
            --white: #FFFFFF;
            --shadow: 0 18px 44px rgba(18, 35, 66, .10);
            --radius: 8px;
            --inner: 1180px;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            color: var(--ink);
            background:
                linear-gradient(180deg, rgba(255,255,255,.98) 0%, rgba(246,248,251,.96) 52%, #F6F8FB 100%),
                repeating-linear-gradient(90deg, rgba(21,94,239,.035) 0 1px, transparent 1px 96px);
            font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Microsoft YaHei", "Helvetica Neue", Arial, sans-serif;
            font-size: 15px;
            line-height: 1.6;
            letter-spacing: 0;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        button { border: 0; background: transparent; color: inherit; font: inherit; cursor: pointer; }
        img { display: block; max-width: 100%; }
        .inner { width: min(100% - 48px, var(--inner)); margin: 0 auto; }
        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            border-bottom: 1px solid rgba(230,234,240,.78);
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }
        .header-row {
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }
        .brand {
            min-width: 0;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }
        .brand-logo {
            width: 156px;
            height: 42px;
            object-fit: contain;
        }
        .brand img, .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 8px 18px rgba(21,94,239,.16);
        }
        .brand img.brand-logo {
            width: 156px;
            height: 42px;
            border-radius: 0;
            box-shadow: none;
            object-fit: contain;
        }
        .brand-mark {
            display: inline-grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), #18A0FB);
        }
        .brand-text {
            min-width: 0;
            display: flex;
            flex-direction: column;
            line-height: 1.16;
        }
        .brand-text strong {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            font-size: 16px;
        }
        .brand-text span { color: var(--muted); font-size: 12px; font-weight: 650; }
        .header-actions { display: flex; align-items: center; gap: 10px; }
        .nav-link, .header-button {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }
        .nav-link { padding: 0 12px; color: var(--text); }
        .nav-link:hover { color: var(--primary); background: rgba(21,94,239,.07); }
        .header-button { padding: 0 16px; color: #fff; background: var(--primary); }
        .hero { padding: 78px 0 64px; overflow: hidden; }
        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(340px, .95fr);
            align-items: center;
            gap: 58px;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 30px;
            padding: 0 12px;
            border: 1px solid rgba(21,94,239,.16);
            border-radius: 999px;
            color: var(--primary);
            background: rgba(21,94,239,.07);
            font-size: 13px;
            font-weight: 800;
        }
        .eyebrow::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 99px;
            background: var(--green);
            box-shadow: 0 0 0 4px rgba(8,166,90,.12);
        }
        h1 {
            margin: 22px 0 14px;
            font-size: 48px;
            line-height: 1.14;
            font-weight: 850;
            letter-spacing: 0;
        }
        .subtitle {
            margin: 0 0 12px;
            color: var(--primary);
            font-size: 22px;
            line-height: 1.35;
            font-weight: 780;
        }
        .description { max-width: 620px; margin: 0; color: var(--text); font-size: 17px; }
        .hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 26px 0 30px;
        }
        .meta-pill {
            min-height: 34px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 12px;
            border: 1px solid var(--line);
            border-radius: 999px;
            color: var(--text);
            background: rgba(255,255,255,.84);
            font-size: 13px;
            font-weight: 650;
        }
        .meta-pill i {
            width: 6px;
            height: 6px;
            border-radius: 99px;
            background: var(--accent);
        }
        .download-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            max-width: 690px;
        }
        .download-button {
            min-height: 66px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border-radius: var(--radius);
            border: 1px solid var(--line);
            background: var(--white);
            box-shadow: 0 6px 16px rgba(18,35,66,.06);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .download-button:hover {
            transform: translateY(-2px);
            border-color: rgba(21,94,239,.28);
            box-shadow: 0 12px 28px rgba(18,35,66,.10);
        }
        .download-button.is-primary {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, var(--primary) 0%, #178BFF 100%);
            box-shadow: 0 14px 30px rgba(21,94,239,.24);
        }
        .download-button.is-primary .button-meta, .download-button.is-primary .platform-icon { color: rgba(255,255,255,.82); }
        .download-button.is-disabled { color: var(--muted); background: rgba(255,255,255,.62); cursor: not-allowed; box-shadow: none; }
        .download-button.is-disabled:hover { transform: none; border-color: var(--line); }
        .platform-icon {
            width: 38px;
            height: 38px;
            flex: 0 0 auto;
            display: inline-grid;
            place-items: center;
            border-radius: var(--radius);
            color: var(--primary);
            background: rgba(21,94,239,.08);
            font-size: 17px;
            font-weight: 850;
        }
        .button-text { min-width: 0; display: flex; flex-direction: column; line-height: 1.2; }
        .button-name { overflow: hidden; font-size: 16px; font-weight: 800; white-space: nowrap; text-overflow: ellipsis; }
        .button-meta { overflow: hidden; margin-top: 4px; color: var(--muted); font-size: 12px; font-weight: 650; white-space: nowrap; text-overflow: ellipsis; }
        .quick-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-top: 16px;
        }
        .device-tip, .text-button {
            min-height: 32px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text);
            font-size: 13px;
        }
        .device-tip strong { color: var(--green); font-weight: 800; }
        .text-button { padding: 0 10px; border-radius: var(--radius); color: var(--primary); background: rgba(21,94,239,.07); font-weight: 800; }
        .download-qr-panel {
            width: min(100%, 360px);
            min-height: 122px;
            display: grid;
            grid-template-columns: 98px minmax(0, 1fr);
            align-items: center;
            gap: 16px;
            margin-top: 18px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: rgba(255,255,255,.92);
            box-shadow: 0 8px 20px rgba(18,35,66,.06);
        }
        .download-qr-code {
            width: 98px;
            height: 98px;
            display: grid;
            place-items: center;
            overflow: hidden;
            border-radius: 6px;
            background: #fff;
        }
        .download-qr-code img {
            width: 98px;
            height: 98px;
        }
        .download-qr-code span {
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }
        .download-qr-copy {
            min-width: 0;
        }
        .download-qr-copy strong {
            display: block;
            font-size: 16px;
            font-weight: 850;
        }
        .download-qr-copy span {
            display: block;
            overflow: hidden;
            margin-top: 5px;
            color: var(--muted);
            font-size: 12px;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .phone-stage { position: relative; width: min(100%, 430px); min-height: 604px; display: grid; place-items: center; }
        .hero-poster {
            width: min(100%, 460px);
            overflow: hidden;
            border: 1px solid rgba(230,234,240,.95);
            border-radius: 18px;
            background: #fff;
            box-shadow: var(--shadow);
        }
        .hero-poster img {
            width: 100%;
            height: auto;
        }
        .hero-visual {
            position: relative;
            min-width: 0;
            display: grid;
            justify-items: center;
        }
        .phone-stage::before {
            content: "";
            position: absolute;
            left: 30px;
            right: 30px;
            bottom: 24px;
            height: 46px;
            border-radius: 50%;
            background: rgba(18,35,66,.16);
            filter: blur(18px);
        }
        .phone-shell {
            position: relative;
            width: 292px;
            height: 590px;
            padding: 12px;
            border-radius: 36px;
            background: #121826;
            box-shadow: var(--shadow);
        }
        .phone-shell::before {
            content: "";
            position: absolute;
            top: 14px;
            left: 50%;
            width: 92px;
            height: 24px;
            transform: translateX(-50%);
            border-radius: 0 0 16px 16px;
            background: #121826;
            z-index: 3;
        }
        .phone-screen { position: relative; width: 100%; height: 100%; overflow: hidden; border-radius: 27px; background: #F7F9FC; }
        .phone-screen img { width: 100%; height: 100%; object-fit: cover; }
        .mock-app { height: 100%; padding: 42px 18px 18px; background: linear-gradient(180deg, rgba(21,94,239,.11), rgba(255,255,255,0) 38%), #F6F8FB; }
        .mock-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .mock-logo { width: auto; height: 28px; max-width: 70%; }
        .mock-logo--sm { height: 24px; }
        .mock-status { flex: 0 0 auto; display: inline-flex; align-items: center; gap: 4px; padding: 3px 7px; border-radius: 999px; color: var(--green); background: rgba(8,166,90,.10); font-size: 10px; font-weight: 750; }
        .mock-status::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: var(--green); }
        .mock-card strong, .mock-list-title { display: block; color: var(--ink); font-weight: 850; }
        .mock-card span, .mock-list-row span { display: block; color: var(--muted); font-size: 11px; font-weight: 650; }
        .mock-total { margin-top: 22px; padding: 18px; border-radius: var(--radius); color: #fff; background: linear-gradient(135deg, var(--primary), #178BFF); }
        .mock-total span { color: rgba(255,255,255,.74); font-size: 12px; font-weight: 650; }
        .mock-total strong { display: block; margin-top: 4px; font-size: 30px; line-height: 1.2; font-weight: 850; }
        .mock-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 12px; }
        .mock-card { min-height: 86px; padding: 14px 12px; border-radius: var(--radius); background: #fff; border: 1px solid #F0F3F7; }
        .mock-card strong { margin-top: 6px; font-size: 21px; }
        .mock-list { margin-top: 12px; padding: 14px 12px; border: 1px solid #F0F3F7; border-radius: var(--radius); background: #fff; }
        .mock-list-row { display: flex; justify-content: space-between; gap: 10px; padding: 10px 0; border-top: 1px solid #F0F3F7; }
        .mock-list-row:first-of-type { margin-top: 8px; }
        .mock-price { color: var(--accent); font-weight: 850; }
        .section { padding: 72px 0; }
        .section-title { max-width: 680px; margin: 0 auto 34px; text-align: center; }
        .section-title h2 { margin: 0; font-size: 32px; line-height: 1.25; font-weight: 850; letter-spacing: 0; }
        .section-title p { margin: 10px 0 0; color: var(--text); font-size: 16px; }
        .feature-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .preview-section {
            background: #101828;
            color: #fff;
        }
        .preview-section .section-title p {
            color: rgba(255,255,255,.68);
        }
        .showcase-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            align-items: start;
        }
        .showcase-card {
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: var(--radius);
            background: rgba(255,255,255,.08);
            box-shadow: 0 10px 28px rgba(0,0,0,.18);
        }
        .showcase-card img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .showcase-card strong {
            display: block;
            padding: 12px 14px 14px;
            font-size: 15px;
            font-weight: 800;
        }
        .feature-card { min-height: 184px; padding: 22px; border: 1px solid var(--line); border-radius: var(--radius); background: rgba(255,255,255,.92); box-shadow: 0 6px 18px rgba(18,35,66,.04); }
        .feature-index { width: 36px; height: 36px; display: grid; place-items: center; border-radius: var(--radius); color: #fff; background: var(--primary); font-weight: 850; }
        .feature-card:nth-child(2) .feature-index { background: var(--green); }
        .feature-card:nth-child(3) .feature-index { background: var(--accent); }
        .feature-card:nth-child(4) .feature-index { background: #222B45; }
        .feature-card h3 { margin: 18px 0 8px; font-size: 18px; line-height: 1.35; font-weight: 850; }
        .feature-card p { margin: 0; color: var(--text); font-size: 14px; }
        .version-wrap { display: grid; grid-template-columns: minmax(0, .88fr) minmax(320px, 1.12fr); gap: 22px; align-items: stretch; }
        .version-panel, .update-panel { padding: 28px; border: 1px solid var(--line); border-radius: var(--radius); background: rgba(255,255,255,.92); box-shadow: 0 6px 18px rgba(18,35,66,.04); }
        .version-panel h3, .update-panel h3 { margin: 0 0 18px; font-size: 20px; font-weight: 850; }
        .version-row { display: flex; justify-content: space-between; gap: 16px; padding: 14px 0; border-top: 1px solid #F0F3F7; }
        .version-row:first-child { border-top: 0; }
        .version-row span { color: var(--muted); }
        .version-row strong { text-align: right; }
        .update-list { margin: 0; padding: 0; list-style: none; }
        .update-list li { position: relative; padding: 11px 0 11px 22px; color: var(--text); border-top: 1px solid #F0F3F7; }
        .update-list li:first-child { border-top: 0; }
        .update-list li::before { content: ""; position: absolute; left: 0; top: 20px; width: 8px; height: 8px; border-radius: 99px; background: var(--green); }
        .trust-row { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-top: 22px; }
        .trust-item { padding: 18px; border: 1px solid var(--line); border-radius: var(--radius); background: rgba(255,255,255,.88); }
        .trust-item strong { display: block; font-size: 16px; font-weight: 850; }
        .trust-item span { display: block; margin-top: 5px; color: var(--muted); font-size: 13px; }
        .footer { padding: 30px 0; border-top: 1px solid var(--line); color: var(--muted); background: rgba(255,255,255,.72); font-size: 13px; }
        .footer-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 10px 14px; text-align: center; }
        .footer a:hover { color: var(--primary); }
        .highlight-platform { outline: 3px solid rgba(255,122,26,.2); border-color: rgba(255,122,26,.5); }
        @media (max-width: 1080px) {
            .hero-grid { grid-template-columns: 1fr; gap: 38px; }
            .hero-copy { text-align: center; }
            .description, .download-actions { margin-left: auto; margin-right: auto; }
            .hero-meta, .quick-row { justify-content: center; }
            .download-qr-panel { margin-left: auto; margin-right: auto; text-align: left; }
            .feature-grid, .showcase-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 760px) {
            .inner { width: min(100% - 28px, var(--inner)); }
            .header-row { height: 62px; }
            .brand-text span, .nav-link { display: none; }
            .header-button { height: 36px; padding: 0 12px; font-size: 13px; }
            .hero { padding: 42px 0 44px; }
            h1 { font-size: 34px; }
            .subtitle { font-size: 18px; }
            .description { font-size: 15px; }
            .download-actions { grid-template-columns: 1fr; max-width: 360px; }
            .phone-stage { min-height: 520px; }
            .phone-shell { width: 246px; height: 498px; border-radius: 32px; }
            .phone-screen { border-radius: 23px; }
            .section { padding: 50px 0; }
            .section-title { margin-bottom: 24px; }
            .section-title h2 { font-size: 26px; }
            .showcase-grid { grid-template-columns: 1fr; max-width: 430px; margin: 0 auto; }
            .feature-grid, .version-wrap, .trust-row { grid-template-columns: 1fr; }
        }

        .showcase-card { padding: 10px; }
        .showcase-card strong { padding: 12px 6px 14px; }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="inner header-row">
            <a class="brand" href="/">
                <img class="brand-logo" src="template/XHY-01/assets/img/logo-full.png" alt="彩虹云商城-虚拟商品交易平台-虚拟卡密-知识付费-发卡网平台">            </a>
            <nav class="header-actions">
                <a class="nav-link" href="#features">功能亮点</a>
                <a class="nav-link" href="#preview">界面预览</a>
                <a class="nav-link" href="#version">版本说明</a>
                <a class="header-button" href="#download">立即下载</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero" id="download">
            <div class="inner hero-grid">
                <div class="hero-copy">
                    <span class="eyebrow">官方正版 · 安全下载 · 持续更新</span>
                    <h1><?php echo $conf['sitename']; ?>商家端 官方下载</h1>
                    <p class="subtitle">移动经营管理平台</p>
                    <p class="description">订单、商品、售后、资金和经营数据随时掌握，让商户在手机上也能高效处理发卡网业务。</p>

                    <div class="hero-meta">
                        <span class="meta-pill"><i></i>最新版本 V1.2.1</span>
                        <span class="meta-pill"><i></i>安装包 19.1M</span>
                        <span class="meta-pill"><i></i>更新于 2026-06-21</span>
                    </div>

                    <div class="download-actions">
                                                    <a class="download-button is-primary" data-platform="android" href="./download/android/">
                                <span class="platform-icon">A</span>
                                <span class="button-text"><span class="button-name">Android 版</span><span class="button-meta">APK / 应用市场</span></span>
                            </a>
                                                                                <a class="download-button" data-platform="ios" href="javascript:void(0)" onclick="appToast('iOS 版 APP 即将上线，敬请期待')">
                                <span class="platform-icon">i</span>
                                <span class="button-text"><span class="button-name">iOS 版</span><span class="button-meta">App Store / 企业签</span></span>
                            </a>
                                                                                <span class="download-button is-disabled" data-platform="harmony">
                                <span class="platform-icon">H</span>
                                <span class="button-text"><span class="button-name">HarmonyOS 版</span><span class="button-meta">暂未开放</span></span>
                            </span>
                                                                        </div>

                    <div class="download-qr-panel">
                        <div class="download-qr-code" data-page-qr data-url="https://caihong.wnttech.fun/?mod=download">
                            <span>二维码生成中</span>
                        </div>
                        <div class="download-qr-copy">
                            <strong>扫码打开下载页</strong>
                            <span>https://caihong.wnttech.fun/?mod=download</span>
                        </div>
                    </div>

                    <div class="quick-row">
                        <span class="device-tip" id="deviceTip">正在识别你的设备</span>
                        <button class="text-button" type="button" data-copy-link>复制下载页链接</button>
                                            </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-poster"><img src="template/XHY-01/assets/img/app/app-hero.webp?v=20261024" alt="彩虹云商城商家端APP经营数据看板"></div>
                </div>
            </div>
        </section>

        <section class="section preview-section" id="preview">
            <div class="inner">
                <div class="section-title">
                    <h2>核心界面预览</h2>
                    <p>首页、登录、经营工具和钱包资金管理页面集中展示，让用户下载前先看到真实体验。</p>
                </div>
                <div class="showcase-grid">
                                        <article class="showcase-card">
                        <img src="template/XHY-01/assets/img/app/app-home.webp?v=20261024" alt="彩虹云商城商家端APP首页">
                        <strong>首页</strong>
                    </article>
                                        <article class="showcase-card">
                        <img src="template/XHY-01/assets/img/app/app-login.webp?v=20261024" alt="彩虹云商城商家端APP登录页">
                        <strong>登录页</strong>
                    </article>
                                        <article class="showcase-card">
                        <img src="template/XHY-01/assets/img/app/app-tools.webp?v=20261024" alt="彩虹云商城商家端APP经营工具页">
                        <strong>工具页</strong>
                    </article>
                                        <article class="showcase-card">
                        <img src="template/XHY-01/assets/img/app/app-wallet.webp?v=20261024" alt="彩虹云商城商家端APP钱包页">
                        <strong>钱包页</strong>
                    </article>
                                    </div>
            </div>
        </section>

        <section class="section" id="features">
            <div class="inner">
                <div class="section-title">
                    <h2>把经营工作装进手机</h2>
                    <p>围绕发卡网商户高频场景设计，信息更集中，处理更轻快。</p>
                </div>
                <div class="feature-grid">
                                        <article class="feature-card">
                        <span class="feature-index">1</span>
                        <h3>订单实时提醒</h3>
                        <p>新订单、付款、发货和售后动态及时触达，关键事项不漏看。</p>
                    </article>
                                        <article class="feature-card">
                        <span class="feature-index">2</span>
                        <h3>经营数据看板</h3>
                        <p>销售额、订单量、库存和结算状态集中呈现，手机上也能看清业务。</p>
                    </article>
                                        <article class="feature-card">
                        <span class="feature-index">3</span>
                        <h3>移动售后处理</h3>
                        <p>订单查询、售后凭证、客服沟通和状态跟进形成闭环。</p>
                    </article>
                                        <article class="feature-card">
                        <span class="feature-index">4</span>
                        <h3>账号安全守护</h3>
                        <p>登录校验、设备提醒和操作痕迹记录，适合高频经营场景。</p>
                    </article>
                                    </div>
            </div>
        </section>

        <section class="section" id="version">
            <div class="inner">
                <div class="section-title">
                    <h2>版本信息与更新说明</h2>
                    <p>给用户清楚的下载判断，也让页面更像正式发布渠道。</p>
                </div>
                <div class="version-wrap">
                    <aside class="version-panel">
                        <h3>当前版本</h3>
                        <div class="version-list">
                            <div class="version-row"><span>版本号</span><strong>V1.2.1</strong></div>
                            <div class="version-row"><span>安装包大小</span><strong>19.1M</strong></div>
                            <div class="version-row"><span>更新时间</span><strong>2026-06-21</strong></div>
                        </div>
                    </aside>
                    <article class="update-panel">
                        <h3>本次更新</h3>
                        <ul class="update-list">
                            <li>优化移动端经营数据看板</li><li>新增订单、售后和资金状态提醒</li><li>提升下载安装页在手机端的访问体验</li>                        </ul>
                    </article>
                </div>
                <div class="trust-row">
                    <div class="trust-item"><strong>官方渠道</strong><span>下载入口统一维护，减少用户跳转疑虑。</span></div>
                    <div class="trust-item"><strong>移动优先</strong><span>适配主流手机屏幕，扫码、点击都顺手。</span></div>
                    <div class="trust-item"><strong>安全可信</strong><span>版本与协议完整展示，下载更安心。</span></div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="inner footer-row">
            <span>Copyright © 2026 彩虹云商城 All Rights Reserved.</span>
            <?php
$__icp = trim((string)($conf['icp_number'] ?? ''));
$__police = trim((string)($conf['police_number'] ?? ''));
$__license = trim((string)($conf['icp_license'] ?? ''));
if ($__icp !== '') { echo '<span>' . htmlspecialchars($__icp) . '</span>'; }
if ($__police !== '') { echo '<span>' . htmlspecialchars($__police) . '</span>'; }
if ($__license !== '') { echo '<span>' . htmlspecialchars($__license) . '</span>'; }
?>        </div>
    </footer>

    <script src="/assets/plugin/hsmoban/js/qrcode.js"></script>
    <script>
        (function () {
            var qrMount = document.querySelector('[data-page-qr]');
            if (qrMount) {
                var qrUrl = qrMount.getAttribute('data-url') || window.location.href;
                if (typeof qrcode === 'function') {
                    try {
                        var qr = qrcode(0, 'M');
                        qr.addData(qrUrl);
                        qr.make();
                        qrMount.innerHTML = qr.createImgTag(4, 6);
                        var qrImg = qrMount.querySelector('img');
                        if (qrImg) {
                            qrImg.alt = '下载页二维码';
                        }
                    } catch (error) {
                        qrMount.innerHTML = '<span>二维码生成失败</span>';
                    }
                } else {
                    qrMount.innerHTML = '<span>二维码组件未加载</span>';
                }
            }

            var ua = navigator.userAgent.toLowerCase();
            var tip = document.getElementById('deviceTip');
            var target = 'android';
            var label = 'Android';
            if (ua.indexOf('iphone') > -1 || ua.indexOf('ipad') > -1 || ua.indexOf('ipod') > -1) {
                target = 'ios';
                label = 'iOS';
            } else if (ua.indexOf('harmony') > -1 || ua.indexOf('arkweb') > -1) {
                target = 'harmony';
                label = 'HarmonyOS';
            }
            var button = document.querySelector('[data-platform="' + target + '"]');
            if (button) {
                button.classList.add('highlight-platform');
            }
            if (tip) {
                tip.innerHTML = '已识别为 <strong>' + label + '</strong> 设备，优先选择对应版本下载';
            }
            var copyBtn = document.querySelector('[data-copy-link]');
            if (!copyBtn) {
                return;
            }
            copyBtn.addEventListener('click', function () {
                var url = window.location.href;
                function done() {
                    copyBtn.textContent = '已复制';
                    window.setTimeout(function () {
                        copyBtn.textContent = '复制下载页链接';
                    }, 1600);
                }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(done).catch(function () {});
                    return;
                }
                var input = document.createElement('input');
                input.value = url;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                done();
            });
        })();
    </script>

    <script>
    function appToast(msg){var t=document.createElement('div');t.textContent=msg;t.style.cssText='position:fixed;left:50%;top:24%;transform:translateX(-50%);background:rgba(17,32,51,.9);color:#fff;padding:12px 22px;border-radius:8px;font-size:14px;z-index:9999;box-shadow:0 10px 26px rgba(0,0,0,.2);transition:opacity .3s';document.body.appendChild(t);setTimeout(function(){t.style.opacity='0';setTimeout(function(){t.remove();},350);},2200);}
    </script>
</body>
</html>
