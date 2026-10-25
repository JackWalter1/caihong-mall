<?php
// ==============================================
// logo/网站名/备案/联系方式 已替换为彩虹云
// ==============================================
$cdn_img = 'template/XHY-01/assets/img';
?>
<style>
:root {
    --primary: #1268ff;
    --primary-dark: #073fbe;
    --primary-soft: #eaf3ff;
    --cyan: #00cfff;
    --aqua: #16d6aa;
    --text: #1a2a48;
    --ink: #07162f;
    --muted: #647391;
    --line: #dfebff;
    --panel: #ffffff;
    --page: #f7faff;
    --dark: #061024;
    --dark-2: #0b1b3d;
    --shadow: 0 18px 46px rgba(31, 83, 168, .12);
}

/* ============ 隐藏旧模板区块 ============ */
.x-hero, .x-counter, .x-cta, .site-footer { display: none !important; }
.x-section:not(.x-shop) { display: none !important; }

/* ============ 容器 ============ */
.hsf-layout { width: min(1220px, calc(100% - 64px)); margin: 0 auto; }

/* ============ Header（site-header 精确值） ============ */
.site-header {
    position: fixed; inset: 0 0 auto 0; z-index: 60;
    height: 68px;
    background: rgba(255, 255, 255, .88);
    border-bottom: 1px solid rgba(211, 226, 255, .75);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    transition: background .25s, box-shadow .25s;
}
.site-header__inner {
    max-width: 1240px; margin: 0 auto; padding: 0 28px; height: 68px;
    display: flex; align-items: center; justify-content: space-between; gap: 24px;
}
.site-header .brand { display: flex; align-items: center; text-decoration: none; flex-shrink: 0; min-width: 180px; }
.site-header .brand img { height: 32px; width: auto; max-width: 260px; object-fit: contain; display: block; }
.site-nav { flex: 1 1 auto; display: flex; align-items: center; justify-content: center; min-width: 0; }
.site-nav > ul { display: flex; align-items: center; justify-content: center; gap: 28px; list-style: none; margin: 0; padding: 0; min-width: 0; }
.site-nav li { position: relative; list-style: none; }
.site-nav a {
    position: relative; display: block; color: #25375d;
    font-size: 14px; font-weight: 700; line-height: 68px; white-space: nowrap;
    text-decoration: none; transition: color .2s;
}
.site-nav .has-submenu > a { padding-right: 16px; }
.site-nav .has-submenu > a::before {
    content: ""; position: absolute; top: 50%; right: 0;
    width: 6px; height: 6px;
    border-right: 2px solid currentColor; border-bottom: 2px solid currentColor;
    transform: translateY(-65%) rotate(45deg); opacity: .58;
}
.site-nav > ul > li > a::after {
    content: ""; position: absolute; left: 50%; bottom: 16px;
    width: 0; height: 3px; border-radius: 3px;
    background: linear-gradient(90deg, var(--primary), var(--cyan));
    transform: translateX(-50%); transition: width .2s ease;
}
.site-nav a:hover { color: var(--primary); }
.site-nav > ul > li > a:hover::after { width: 22px; }
.site-nav a.active { color: var(--primary); }

/* 下拉子菜单 */
.site-nav li ul {
    position: absolute; top: calc(100% - 6px); left: 50%; z-index: 35;
    display: block; min-width: 158px; margin: 0; padding: 10px;
    list-style: none; visibility: hidden; opacity: 0; pointer-events: none;
    border: 1px solid rgba(62, 128, 255, .15); border-radius: 12px;
    background: rgba(255, 255, 255, .98);
    box-shadow: 0 18px 46px rgba(35, 79, 150, .16);
    transform: translate(-50%, 8px);
    transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
}
.site-nav li:hover > ul,
.site-nav li:focus-within > ul {
    visibility: visible; opacity: 1; pointer-events: auto; transform: translate(-50%, 0);
}
.site-nav li ul a {
    min-width: 128px; padding: 0 12px; color: #31517f;
    border-radius: 8px; font-size: 13px; font-weight: 700; line-height: 38px;
}
.site-nav li ul a::before,
.site-nav li ul a::after { display: none; }
.site-nav li ul a:hover {
    color: var(--primary);
    background: linear-gradient(90deg, rgba(61, 126, 255, .1), rgba(37, 205, 240, .08));
}

.header-actions { display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex: 0 0 auto; }
.header-actions a { text-decoration: none; }
.link-button, .solid-button, .ghost-button, .query-button, .service-action {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 0; height: 42px; padding: 0 24px; border-radius: 8px;
    font-size: 14px; font-weight: 800; white-space: nowrap; text-decoration: none;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease, color .2s ease;
}
.link-button { height: 36px; padding: 0 18px; color: var(--primary); background: #eef5ff; border: 1px solid #d9e8ff; }
.solid-button { color: #fff; background: linear-gradient(135deg, #1268ff 0%, #0db9ff 100%); box-shadow: 0 14px 28px rgba(18, 104, 255, .25); }
.solid-button--small { height: 36px; padding: 0 18px; font-size: 13px; }
.ghost-button { color: var(--primary); background: rgba(255, 255, 255, .78); border: 1px solid rgba(18, 104, 255, .22); }
.query-button {
    color: #073f98; border: 1px solid rgba(35, 199, 240, .45);
    background: linear-gradient(135deg, rgba(230, 246, 255, .96), rgba(255, 255, 255, .88));
    box-shadow: 0 12px 28px rgba(18, 104, 255, .12);
}
.service-action {
    height: 36px; padding: 0 16px; color: #075bdc; font-size: 13px;
    border: 1px solid rgba(48, 135, 255, .28);
    background:
        linear-gradient(#fff, #fff) padding-box,
        linear-gradient(135deg, rgba(18, 104, 255, .55), rgba(35, 199, 240, .55)) border-box;
    box-shadow: 0 10px 24px rgba(18, 104, 255, .12);
}
.solid-button:hover, .ghost-button:hover, .link-button:hover, .query-button:hover, .service-action:hover {
    transform: translateY(-2px); box-shadow: 0 18px 34px rgba(18, 104, 255, .2);
}
.service-action:hover { color: #fff; border-color: transparent; background: linear-gradient(135deg, #1268ff, #22c7f0); }

.hero {
    position: relative; overflow: hidden;
    margin-top: 68px; min-height: 680px;
    background:
        linear-gradient(115deg, rgba(238, 247, 255, .96) 0%, rgba(255, 255, 255, .94) 42%, rgba(226, 242, 255, .92) 100%),
        #eef6ff;
}
/* 网格线 overlay */
.hero::before {
    content: ""; position: absolute; inset: 0;
    background-image:
        linear-gradient(rgba(18, 104, 255, .08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(18, 104, 255, .08) 1px, transparent 1px),
        linear-gradient(135deg, transparent 0 42%, rgba(0, 207, 255, .12) 42% 43%, transparent 43% 100%);
    background-size: 48px 48px, 48px 48px, 100% 100%;
    mask-image: linear-gradient(180deg, #000 0%, rgba(0, 0, 0, .62) 72%, transparent 100%);
    -webkit-mask-image: linear-gradient(180deg, #000 0%, rgba(0, 0, 0, .62) 72%, transparent 100%);
    pointer-events: none;
}
/* 右上装饰矩形 */
.hero::after {
    content: ""; position: absolute;
    right: -12%; top: 90px; width: 58%; height: 430px;
    background:
        linear-gradient(128deg, rgba(18, 104, 255, .1), rgba(0, 207, 255, .05)),
        repeating-linear-gradient(135deg, rgba(18, 104, 255, .08) 0 1px, transparent 1px 18px);
    transform: skewX(-12deg);
    border-left: 1px solid rgba(18, 104, 255, .12);
    pointer-events: none;
}
/* 粒子层 */
.hero__particles {
    position: absolute; inset: 0; width: 100%; height: 100%;
    display: block; opacity: .35; pointer-events: none;
}
.hero__inner {
    position: relative; z-index: 2;
    min-height: 680px;
    display: grid; grid-template-columns: minmax(420px, .9fr) minmax(520px, 1.1fr);
    align-items: center; gap: 56px;
    padding: 64px 0 94px;
    width: min(1220px, calc(100% - 64px)); margin: 0 auto;
}
.eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    margin: 0 0 16px; color: var(--primary);
    font-size: 15px; font-weight: 900;
}
.eyebrow::before {
    content: ""; width: 34px; height: 3px; border-radius: 3px;
    background: linear-gradient(90deg, var(--primary), var(--cyan));
}
.hero h1 {
    margin: 0; color: var(--ink);
    font-size: 52px; line-height: 1.18; font-weight: 900;
}
.hero__lead {
    width: 540px; max-width: 100%; margin: 22px 0 0;
    color: #425576; font-size: 18px; line-height: 1.9;
}
.hero-tags { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 26px; }
.hero-tags span {
    display: inline-flex; align-items: center;
    height: 34px; padding: 0 14px;
    color: #0c54d8; border: 1px solid #cfe2ff; border-radius: 8px;
    background: rgba(255, 255, 255, .72);
    box-shadow: 0 8px 20px rgba(18, 104, 255, .08);
    font-size: 13px; font-weight: 800;
}
.hero-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 14px; margin-top: 34px; }
.hero-actions .solid-button,
.hero-actions .ghost-button,
.hero-actions .query-button { cursor: pointer; }

.hero__visual {
    position: relative; justify-self: end;
    width: min(760px, 100%); min-width: 0;
    filter: drop-shadow(0 24px 42px rgba(22, 88, 190, .14));
}
.hero__visual img { width: 100%; height: auto; }
.hero-app-carousel {
    display: block; overflow: hidden;
    border: 1px solid rgba(205, 225, 255, .86); border-radius: 10px;
    background: rgba(255, 255, 255, .72);
    box-shadow: 0 22px 48px rgba(22, 88, 190, .12);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}
.hero-app-carousel:hover {
    transform: translateY(-4px); border-color: rgba(18, 104, 255, .34);
    box-shadow: 0 28px 58px rgba(22, 88, 190, .18);
}
.hero-app-carousel img {
    display: block; width: 100%; height: auto;
    aspect-ratio: 28 / 15; object-fit: cover; object-position: center;
}

.notice-card {
    position: relative; z-index: 4;
    display: grid; grid-template-columns: 230px 1fr; gap: 16px; align-items: stretch;
    margin-top: -72px; padding: 14px;
    border: 1px solid rgba(218, 232, 255, .98); border-radius: 8px;
    background: rgba(255, 255, 255, .96);
    box-shadow: var(--shadow);
    width: min(1220px, calc(100% - 64px));
    margin-left: auto; margin-right: auto;
}
.notice-card__title {
    display: flex; flex-direction: column; justify-content: center; gap: 4px;
    min-height: 112px; padding: 22px 24px; border-radius: 8px;
    text-align: left;
    background:
        linear-gradient(135deg, rgba(18, 104, 255, .98), rgba(0, 207, 255, .88)),
        #1268ff;
}
.notice-card__title strong { color: #fff; font-size: 20px; line-height: 1.25; font-weight: 900; }
.notice-card__title span { color: rgba(255, 255, 255, .82); font-size: 13px; font-weight: 700; }
.notice-list { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
.notice-list article {
    position: relative; display: flex; min-width: 0; gap: 12px;
    padding: 17px 16px;
    border: 1px solid #edf4ff; border-radius: 8px;
    background: linear-gradient(180deg, #fff, #f8fbff);
}
.notice-list article i {
    flex: 0 0 10px; width: 10px; height: 10px; margin-top: 8px;
    border-radius: 50%; background: var(--aqua);
    box-shadow: 0 0 0 5px rgba(22, 214, 170, .12);
}
.notice-list article div { min-width: 0; }
.notice-list article h2 { margin: 0; color: #172849; font-size: 15px; line-height: 1.45; font-weight: 900; }
.notice-list article h2 a { color: inherit; text-decoration: none; }
.notice-list article h2 a:hover { color: var(--primary); }
.notice-list .has-notice-more h2 { padding-right: 70px; }
.notice-list article p { margin: 6px 0 0; color: #70809e; font-size: 13px; line-height: 1.65; }
.notice-list article p a { color: inherit; text-decoration: none; display: block; }
.notice-list article p a:hover { color: var(--primary); }
.notice-more {
    position: absolute; top: 12px; right: 12px;
    display: inline-flex; align-items: center; justify-content: center;
    height: 26px; padding: 0 10px;
    color: #075bdc; border: 1px solid rgba(18, 104, 255, .18); border-radius: 999px;
    background: rgba(238, 246, 255, .94); box-shadow: 0 8px 18px rgba(18, 104, 255, .1);
    font-size: 12px; font-weight: 900; text-decoration: none;
}
.notice-more:hover { color: #fff; border-color: transparent; background: linear-gradient(135deg, #1268ff, #22c7f0); }

.section { position: relative; padding: 94px 0; background: #fff; }
.section-heading { width: 760px; max-width: 100%; margin: 0 auto 46px; text-align: center; }
.section-heading > span,
.cockpit-copy > span,
.data-copy > span,
.qa-copy > span {
    display: inline-flex; align-items: center; gap: 8px;
    margin-bottom: 12px; color: var(--primary);
    font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;
}
.section-heading > span::before,
.section-heading > span::after {
    content: ""; width: 24px; height: 2px; border-radius: 2px;
    background: linear-gradient(90deg, var(--primary), var(--cyan));
}
.section-heading h2 { margin: 0; color: var(--ink); font-size: 32px; line-height: 1.35; font-weight: 900; }
.section-heading p { margin: 14px auto 0; color: var(--muted); font-size: 15px; }
.section-heading a.more { float: right; font-size: 13px; color: var(--primary); text-decoration: none; font-weight: 700; }

/* ============ scenario-section ============ */
.scenario-section { }
.scenario-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 22px; }
.scenario-card {
    position: relative; overflow: hidden;
    padding: 28px 24px 24px;
    border: 1px solid var(--line); border-radius: 8px;
    background: #fff; box-shadow: 0 14px 34px rgba(36, 83, 160, .08);
    transition: all .3s; min-height: 254px;
}
.scenario-card::after {
    content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--cyan), var(--aqua));
}
.scenario-card:hover { transform: translateY(-4px); box-shadow: 0 20px 44px rgba(36, 83, 160, .14); border-color: var(--primary); }
.scenario-card > span { display: inline-flex; color: rgba(18,104,255,.22); font-size: 34px; font-weight: 900; line-height: 1; }
.scenario-card > img { display: block; width: 86px; height: 86px; object-fit: contain; margin: 8px 0 18px; }
.scenario-card h3 { margin: 0; color: #132541; font-size: 18px; line-height: 1.45; font-weight: 900; }
.scenario-card p { margin: 10px 0 0; color: var(--muted); font-size: 14px; line-height: 1.75; }

/* ============ capability-section ============ */
.capability-section { background: linear-gradient(180deg, #f5f9ff 0%, #fff 100%); }
.capability-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 22px; }
.capability-card {
    padding: 32px 28px; border: 1px solid var(--line); border-radius: 8px;
    background: #fff; box-shadow: 0 14px 34px rgba(36, 83, 160, .08);
    transition: all .3s;
}
.capability-card:hover { transform: translateY(-4px); border-color: var(--primary); box-shadow: 0 18px 40px rgba(36, 83, 160, .14); }
.capability-card img { width: 86px; height: 86px; margin-bottom: 16px; object-fit: contain; }
.capability-card h3 { font-size: 17px; font-weight: 900; color: var(--text); margin: 0 0 8px; }
.capability-card p { font-size: 14px; color: var(--muted); line-height: 1.75; margin: 0; }

/* ============ cockpit-section (深色) ============ */
.cockpit-section {
    position: relative; overflow: hidden;
    padding: 104px 0; color: #dce8ff;
    background: linear-gradient(135deg, #061024 0%, #0b1b3d 52%, #0b3286 100%);
}
.cockpit-section::before {
    content: ""; position: absolute; inset: 0;
    background-image:
        repeating-linear-gradient(90deg, rgba(255,255,255,.05) 0 1px, transparent 1px 88px),
        repeating-linear-gradient(0deg, rgba(255,255,255,.04) 0 1px, transparent 1px 72px),
        linear-gradient(125deg, transparent 0 45%, rgba(0,207,255,.08) 45% 46%, transparent 46% 100%);
    pointer-events: none;
}
.cockpit-layout { position: relative; z-index: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
.cockpit-copy > span { color: #7edfff; }
.cockpit-copy h2 { font-size: 32px; font-weight: 900; line-height: 1.35; color: #fff; margin: 0 0 14px; }
.cockpit-copy p { color: #a9b4c2; font-size: 15px; line-height: 1.8; margin: 0 0 22px; }
.cockpit-copy ul { list-style: none; padding: 0; margin: 0 0 28px; }
.cockpit-copy ul li {
    position: relative; padding: 12px 0 12px 42px; margin: 0;
    font-size: 15px; color: #eaf3ff; font-weight: 700;
    border: 1px solid rgba(126,223,255,.18); border-radius: 8px;
    background: rgba(255,255,255,.06);
    margin-bottom: 10px;
}
.cockpit-copy ul li::before {
    content: ""; position: absolute; left: 16px; top: 18px;
    width: 10px; height: 10px; border-radius: 50%;
    background: var(--aqua); box-shadow: 0 0 0 5px rgba(22,214,170,.12);
}
.cockpit-copy .solid-button {
    display: inline-flex; padding: 14px 32px; background: #fff; color: var(--primary-dark);
    font-size: 15px; font-weight: 900; border-radius: 8px; text-decoration: none;
    box-shadow: 0 18px 38px rgba(0,32,92,.18); transition: all .2s;
}
.cockpit-copy .solid-button:hover { transform: translateY(-2px); box-shadow: 0 22px 44px rgba(0,32,92,.28); }
.cockpit-art img { display: block; width: 100%; height: auto; border-radius: 8px;
    border: 1px solid rgba(142,190,255,.24); background: rgba(255,255,255,.08);
    box-shadow: 0 30px 70px rgba(0,0,0,.28); padding: 4px;
}

/* ============ process-section ============ */
.process-section { background: linear-gradient(180deg, #f0f6ff 0%, #f8fbff 100%); }
.process-line { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 22px; }
.process-step {
    position: relative; z-index: 1;
    display: flex; flex-direction: column; align-items: center;
    min-width: 0; min-height: 126px; padding: 0 8px;
    border: 1px solid var(--line); border-radius: 8px;
    background: #fff; box-shadow: 0 10px 28px rgba(36,83,160,.06);
    cursor: pointer; transition: all .3s;
    font-size: 14px; font-weight: 900; color: #23395f;
}
.process-step:hover { border-color: var(--primary); box-shadow: 0 14px 34px rgba(36,83,160,.14); }
.process-step.is-active { border-color: var(--primary); background: var(--primary-soft); }
.process-step span { display: flex; align-items: center; justify-content: center; margin-top: 18px; }
.process-step img { width: 48px; height: 48px; }
.process-step em { font-size: 14px; font-weight: 900; color: var(--primary); margin: 6px 0 4px; font-style: normal; }
.process-panels { display: grid; gap: 0; }
.process-panel {
    padding: 24px 32px; border: 1px solid var(--line); border-radius: 8px;
    background: #fff; text-align: center;
    display: none;
}
.process-panel.is-active { display: block; }
.process-panel h3 { font-size: 16px; font-weight: 900; color: var(--text); margin: 0 0 6px; }
.process-panel p { font-size: 14px; color: var(--muted); margin: 0; line-height: 1.7; }

/* ============ security-section ============ */
.security-section { }
.security-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 22px; }
.security-card {
    padding: 32px 28px; border: 1px solid var(--line); border-radius: 8px;
    background: #fff; box-shadow: 0 14px 34px rgba(36, 83, 160, .08);
    text-align: center; transition: all .3s;
}
.security-card:hover { transform: translateY(-4px); border-color: var(--primary); box-shadow: 0 18px 40px rgba(36, 83, 160, .14); }
.security-card img { width: 86px; height: 86px; margin-bottom: 16px; }
.security-card h3 { font-size: 16px; font-weight: 900; color: var(--text); margin: 0 0 8px; }
.security-card p { font-size: 14px; color: var(--muted); line-height: 1.75; margin: 0; }

/* ============ data-section ============ */
.data-section { background: linear-gradient(135deg, #f7fbff 0%, #fff 58%, #edf5ff 100%); }
.data-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
.data-copy > span { font-size: 14px; color: var(--primary); font-weight: 900; letter-spacing: 2px; }
.data-copy h2 { font-size: 32px; font-weight: 900; line-height: 1.35; color: var(--text); margin: 8px 0 14px; }
.data-copy p { color: var(--muted); font-size: 15px; margin: 0; }
.stats-panel { background: #fff; border-radius: 12px; padding: 16px; border: 1px solid var(--line); box-shadow: var(--shadow); }
.stats-panel article { padding: 22px; text-align: center; border-bottom: 1px solid var(--line); }
.stats-panel article:last-child { border-bottom: none; }
.stats-panel strong { display: block; font-size: 40px; font-weight: 900; color: var(--primary); line-height: 1.1; margin-bottom: 4px; }
.stats-panel span { font-size: 14px; color: var(--muted); }

/* ============ qa-section ============ */
.qa-section { background: #fff; }
.qa-layout { display: grid; grid-template-columns: minmax(330px, 410px) minmax(0, 1fr); gap: 56px; align-items: start; }
.qa-copy > span { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px; color: var(--primary); font-size: 13px; font-weight: 900; text-transform: uppercase; }
.qa-copy h2 { margin: 0; color: #07162f; font-size: 32px; line-height: 1.35; font-weight: 900; }
.qa-copy p { margin: 14px auto 0; color: var(--muted); font-size: 15px; }
.company-copy { margin-top: 28px; padding: 24px 24px 26px; border: 1px solid var(--line); border-radius: 8px; background: #fff; box-shadow: 0 14px 34px rgba(36, 83, 160, .08); }
.company-copy h3 { margin: 0; color: #132541; font-size: 18px; line-height: 1.45; font-weight: 900; }
.company-copy p { margin: 10px 0 0; color: var(--muted); font-size: 14px; line-height: 1.75; }
.faq-list { display: grid; gap: 12px; }
.faq-item { border: 1px solid var(--line); border-radius: 8px; background: #fff; overflow: hidden; box-shadow: none; transition: border-color .22s ease, box-shadow .22s ease; }
.faq-item.is-open { border-color: #b9d5ff; box-shadow: 0 14px 32px rgba(31, 83, 168, .1); }
.faq-item button {
    display: flex; align-items: center; justify-content: space-between; gap: 18px;
    width: 100%; min-height: 62px; padding: 0 20px; border: none; background: transparent; cursor: pointer;
    color: #182a4a; text-align: left; font-family: inherit; font-size: 14px; font-weight: 900;
}
.faq-item button span { min-width: 0; overflow-wrap: anywhere; }
.faq-item button i { position: relative; flex: 0 0 22px; width: 22px; height: 22px; border-radius: 6px; background: #eef5ff; }
.faq-item button i::before,
.faq-item button i::after { content: ""; position: absolute; left: 6px; right: 6px; top: 10px; height: 2px; border-radius: 2px; background: var(--primary); }
.faq-item button i::after { transform: rotate(90deg); }
.faq-item.is-open button i::after { transform: rotate(0); }
.faq-item__body { display: none; padding: 0 20px 20px; }
.faq-item.is-open .faq-item__body { display: block; }
.faq-item__body p { margin: 0; color: var(--muted); font-size: 14px; line-height: 1.7; }

/* ============ bottom-cta ============ */
.bottom-cta {
    position: relative; overflow: hidden; text-align: center;
    padding: 70px 0;
    background: linear-gradient(135deg, #0a4bd6 0%, #1274ff 55%, #00b9ff 100%);
}
.bottom-cta::before {
    content: ""; position: absolute; inset: 0;
    background-image:
        repeating-linear-gradient(90deg, rgba(255,255,255,.14) 0 1px, transparent 1px 84px),
        linear-gradient(135deg, transparent 0 48%, rgba(255,255,255,.16) 48% 49%, transparent 49% 100%);
    pointer-events: none;
}
.bottom-cta__inner { position: relative; z-index: 1; }
.bottom-cta h2 { font-size: 32px; font-weight: 900; color: #fff; margin: 0 0 14px; line-height: 1.35; }
.bottom-cta p { width: 720px; max-width: 100%; margin: 0 auto 28px; color: rgba(255,255,255,.86); font-size: 16px; }
.bottom-cta .solid-button {
    display: inline-flex; padding: 18px 36px; background: #fff; color: var(--primary-dark);
    font-size: 16px; font-weight: 900; border-radius: 10px; text-decoration: none;
    box-shadow: 0 18px 38px rgba(0,32,92,.18); transition: all .2s;
}
.bottom-cta .solid-button:hover { transform: translateY(-2px); box-shadow: 0 24px 44px rgba(0,32,92,.28); }

.site-footer-new { color: #adbbd4; background: var(--dark); }
.site-footer-new__inner {
    display: grid;
    grid-template-columns: minmax(250px, 1.2fr) minmax(120px, .55fr) minmax(120px, .55fr) minmax(120px, .55fr) minmax(250px, 1.12fr) 118px;
    gap: 34px; padding: 58px 0 42px;
    width: min(1220px, calc(100% - 64px)); margin: 0 auto;
}
.footer-brand img { width: 150px; height: auto; margin-bottom: 18px; filter: brightness(0) invert(1); }
.footer-brand p { max-width: 360px; margin: 0 0 16px; color: #b5c2dc; font-size: 13px; line-height: 1.8; }
.footer-brand span { display: block; color: #8898b7; font-size: 13px; }
.footer-links { display: flex; flex-direction: column; gap: 10px; }
.footer-links strong { margin-bottom: 6px; color: #fff; font-size: 15px; font-weight: 800; }
.footer-links a { color: #9aabc8; font-size: 13px; text-decoration: none; transition: color .2s; }
.footer-links a:hover { color: #fff; }
.footer-contact { display: flex; flex-direction: column; gap: 10px; min-width: 0; }
.footer-contact strong { margin-bottom: 6px; color: #fff; font-size: 15px; font-weight: 800; }
.footer-contact span { color: #9aabc8; font-size: 13px; line-height: 1.8; }
.footer-contact a { color: #55d7ff; font-weight: 800; text-decoration: none; }
.footer-contact a:hover { color: #fff; }
.footer-contact__qr-wrap { position: relative; display: inline-flex !important; align-items: center; gap: 6px; width: fit-content; }
.footer-contact__qr-trigger {
    padding: 0; color: #55d7ff; font-size: 13px; font-weight: 800; line-height: 1.8;
    background: none; border: none; cursor: pointer;
}
.footer-contact__qr-trigger:hover,
.footer-contact__qr-trigger:focus-visible { color: #fff; outline: none; }
.footer-contact__qr {
    position: absolute; left: 80px; bottom: calc(100% + 12px); z-index: 5; width: 154px;
    padding: 14px 14px 12px;
    visibility: hidden; opacity: 0; pointer-events: none;
    border: 1px solid rgba(72, 137, 255, .22); border-radius: 18px;
    background: rgba(255, 255, 255, .98);
    box-shadow: 0 20px 48px rgba(0, 0, 0, .32);
    text-align: center; transform: translateY(8px);
    transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
}
.footer-contact__qr::after {
    content: ""; position: absolute; left: 24px; bottom: -7px;
    width: 14px; height: 14px;
    border-right: 1px solid rgba(72, 137, 255, .22);
    border-bottom: 1px solid rgba(72, 137, 255, .22);
    background: #fff; transform: rotate(45deg);
}
.footer-contact__qr em {
    position: relative; z-index: 1; display: block;
    color: #17315b; font-size: 13px; font-style: normal; font-weight: 900;
}
.footer-contact__qr-img {
    position: relative; z-index: 1; display: block;
    width: 118px; height: 118px; margin: 0 auto 8px;
    border-radius: 10px; background: #fff; object-fit: contain;
}
.footer-contact__qr-wrap:hover .footer-contact__qr,
.footer-contact__qr-trigger:focus + .footer-contact__qr,
.footer-contact__qr-trigger:focus-visible + .footer-contact__qr {
    visibility: visible; opacity: 1; pointer-events: auto; transform: translateY(0);
}
.footer-qr { text-align: center; }
.footer-qr span { display: block; color: #fff; font-size: 13px; font-weight: 800; }
.site-footer-new__bottom {
    display: flex; align-items: center; justify-content: center; flex-wrap: wrap;
    gap: 8px 12px; padding: 20px 24px;
    color: #7585a2; border-top: 1px solid rgba(255, 255, 255, .08); font-size: 12px;
    text-align: center; width: min(1220px, calc(100% - 64px)); margin: 0 auto;
}
.site-footer-new__bottom a { color: #8ca0c0; text-decoration: none; }
.site-footer-new__bottom a:hover { color: #fff; }
.footer-records { display: inline-flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 0; }
.footer-records__item { display: inline-flex; align-items: center; color: #8ca0c0; line-height: 1.8; }
.footer-records__item + .footer-records__item::before { content: "|"; margin: 0 8px; color: #50617e; }

.float-rail {
    position: fixed; right: 22px; top: 50%; z-index: 50;
    display: grid; gap: 12px; transform: translateY(-50%);
}
.float-rail__item {
    position: relative; display: grid; place-items: center; gap: 4px;
    width: 62px; min-height: 62px; padding: 8px 0;
    color: #1f5fe9; border: 1px solid rgba(103, 163, 255, .32);
    border-radius: 16px;
    background: linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(244, 249, 255, .94));
    box-shadow: 0 16px 34px rgba(31, 95, 233, .16);
    font-size: 12px; font-weight: 900; text-decoration: none;
    cursor: pointer;
    transition: color .2s ease, border-color .2s ease, background .2s ease, box-shadow .2s ease, transform .2s ease;
}
.float-rail__item:hover,
.float-rail__item:focus-visible {
    color: #fff; border-color: transparent; outline: none;
    background: linear-gradient(135deg, #1f6eff, #23c7f0);
    box-shadow: 0 18px 38px rgba(31, 95, 233, .28);
    transform: translateX(-3px);
}
.float-rail__icon {
    position: relative; width: 24px; height: 24px; border-radius: 10px;
    background: linear-gradient(135deg, rgba(31, 110, 255, .15), rgba(35, 199, 240, .18));
}
.float-rail__item:hover .float-rail__icon,
.float-rail__item:focus-visible .float-rail__icon { background: rgba(255, 255, 255, .2); }
.float-rail__icon::before,
.float-rail__icon::after { content: ""; position: absolute; display: block; }
.float-rail__item--login .float-rail__icon::before {
    top: 5px; left: 8px; width: 8px; height: 8px; border-radius: 50%; background: currentColor;
}
.float-rail__item--login .float-rail__icon::after {
    left: 5px; bottom: 4px; width: 14px; height: 8px;
    border-radius: 8px 8px 4px 4px; background: currentColor;
}
.float-rail__item--join .float-rail__icon::before {
    top: 5px; left: 5px; width: 14px; height: 14px;
    border: 2px solid currentColor; border-radius: 6px;
}
.float-rail__item--join .float-rail__icon::after {
    top: 7px; left: 7px; width: 10px; height: 10px;
    background:
        linear-gradient(currentColor, currentColor) center / 10px 2px no-repeat,
        linear-gradient(currentColor, currentColor) center / 2px 10px no-repeat;
}
.float-rail__item--qr .float-rail__icon::before {
    top: 5px; left: 5px; width: 5px; height: 5px; border-radius: 2px;
    background: currentColor;
    box-shadow: 9px 0 0 currentColor, 0 9px 0 currentColor, 9px 9px 0 currentColor;
}
.float-rail__item--qr .float-rail__icon::after {
    right: 4px; bottom: 4px; width: 5px; height: 5px;
    border: 2px solid currentColor; border-radius: 2px;
}
.float-rail__qr {
    position: absolute; top: 50%; right: 78px; width: 154px;
    padding: 14px 14px 12px;
    visibility: hidden; opacity: 0; pointer-events: none;
    border: 1px solid rgba(72, 137, 255, .18); border-radius: 18px;
    background: rgba(255, 255, 255, .98);
    box-shadow: 0 20px 48px rgba(30, 71, 145, .2);
    text-align: center; transform: translate(8px, -50%);
    transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
}
.float-rail__qr::after {
    content: ""; position: absolute; top: 50%; right: -7px;
    width: 14px; height: 14px;
    border-top: 1px solid rgba(72, 137, 255, .18);
    border-right: 1px solid rgba(72, 137, 255, .18);
    background: #fff; transform: translateY(-50%) rotate(45deg);
}
.float-rail__qr strong {
    position: relative; z-index: 1; color: #17315b; font-size: 13px;
}
.float-rail__item--qr:hover .float-rail__qr,
.float-rail__item--qr:focus-within .float-rail__qr {
    visibility: visible; opacity: 1; pointer-events: auto; transform: translate(0, -50%);
}

/* 二维码占位（待用户提供图片） */
.qr-ph {
    display: flex; align-items: center; justify-content: center;
    color: #6b7f9e; font-size: 11px; text-align: center; line-height: 1.4;
    border: 1px dashed #c3d6f2; border-radius: 12px;
    background: repeating-linear-gradient(45deg, #f7fbff, #f7fbff 6px, #eef5ff 6px, #eef5ff 12px);
}
.qr-ph--pop { position: relative; z-index: 1; width: 118px; height: 118px; margin: 0 auto 8px; }
.float-rail__qr-img {
    display: block; width: 118px; height: 118px; margin: 0 auto 8px;
    padding: 4px; box-sizing: border-box;
    border: 1px solid #e3ecfb; border-radius: 12px; background: #fff;
}
.qr-ph--footer { width: 98px; height: 98px; margin: 0 auto 10px; border-color: rgba(255, 255, 255, .28); color: #8ea7c9; background: rgba(255, 255, 255, .06); }
.footer-qr__img {
    display: block; width: 98px; height: 98px; margin: 0 auto 10px;
    padding: 5px; box-sizing: border-box;
    border: 1px solid rgba(255, 255, 255, .22); border-radius: 12px;
    background: #fff; object-fit: contain;
}

/* ============ x-shop 下单区视觉统一（不动 shop.inc.php，作用域覆盖内联样式） ============ */
.x-shop-keep { background: #fff; }
.x-shop-panel {
    border: 1px solid var(--line); border-radius: 12px; background: #fff;
    box-shadow: 0 14px 34px rgba(36, 83, 160, .08);
    padding: 26px 26px 30px; overflow: hidden;
}
/* 顶部黑色警告横幅 → 品牌蓝信息条 */
.x-shop-keep #orderWarning {
    background: linear-gradient(90deg, #eaf3ff, #e6f9ff) !important;
    border: 1px solid #d3e6ff !important;
    border-radius: 10px !important;
    box-shadow: 0 8px 20px rgba(18, 104, 255, .08) !important;
    padding: 10px 14px !important;
    margin: 0 0 12px !important;
}
.x-shop-keep #orderWarning font[color="#D2B48C"] { color: #073f98 !important; }
.x-shop-keep #orderWarning span[style*="rgb(194, 79, 74)"] { color: #075bdc !important; }
.x-shop-keep #orderWarning span[style*="#E53333"] { color: #9fc0ef !important; }
.x-shop-keep #orderWarning span[style*="#E8E8E8"] { color: #31517f !important; }
.x-shop-keep #orderWarning span[style*="#009900"] { color: #0aa670 !important; }
.x-shop-keep #orderWarning span[style*="#EE33EE"] { color: #075bdc !important; }
.x-shop-keep #orderWarning span[style*="#F08080"] { color: #16d6aa !important; }
/* 购前必看按钮 → 蓝色胶囊 */
.x-shop-keep .custom-btn {
    background: #eef5ff !important;
    border: 1px solid #d9e8ff !important;
    border-radius: 10px !important;
    padding: 10px 18px !important;
    margin-bottom: 14px !important;
    box-shadow: 0 8px 18px rgba(18, 104, 255, .07);
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
}
.x-shop-keep .custom-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(18, 104, 255, .14); background: #e2eeff !important; }
.x-shop-keep .custom-btn span[style*="#8B0000"], .x-shop-keep .custom-btn i[style*="#8B0000"] { color: #075bdc !important; }
.x-shop-keep .custom-btn i { color: #075bdc !important; }
/* 输入组统一蓝色语言 */
.x-shop-keep .form-group { margin-bottom: 16px; }
.x-shop-keep .input-group-addon {
    background: #eaf3ff !important; color: #075bdc !important;
    border: 1px solid #d9e8ff !important; font-weight: 700; font-size: 13px;
    min-width: 92px; text-align: center;
}
.x-shop-keep .input-group .input-group-addon:first-child { border-right: 0 !important; border-radius: 8px 0 0 8px; }
.x-shop-keep .input-group .input-group-addon:last-child { border-left: 0 !important; border-radius: 0 8px 8px 0; }
.x-shop-keep .input-group > .input-group-btn:last-child > .btn { border-radius: 0 8px 8px 0; }
.x-shop-keep .form-control {
    border: 1px solid #d9e8ff !important; height: 42px; font-size: 14px;
    box-shadow: none !important; color: var(--text);
    transition: border-color .2s ease, box-shadow .2s ease;
}
.x-shop-keep .form-control:focus { border-color: #1268ff !important; box-shadow: 0 0 0 3px rgba(18, 104, 255, .12) !important; }
.x-shop-keep select.form-control { -webkit-appearance: menulist; appearance: menulist; }
/* 数量步进按钮 */
.x-shop-keep #num_min, .x-shop-keep #num_add {
    background: #eaf3ff !important; color: #075bdc !important;
    border: 1px solid #d9e8ff !important; border-radius: 0 !important;
    font-weight: 700; width: 44px; transition: background .2s ease;
}
.x-shop-keep #num_min:hover, .x-shop-keep #num_add:hover { background: #d8eaff !important; }
/* 警示条 → 金色提示（弱化恐吓感） */
.x-shop-keep .alert-danger {
    background: #fff8e8 !important; color: #8a6200 !important;
    border: 1px solid #ffe2ad !important; border-left: 4px solid #ffbd4a !important;
    border-radius: 8px !important; font-weight: 700 !important;
}
/* 成功提示 */
.x-shop-keep #alert_frame {
    background: linear-gradient(135deg, #1268ff, #22c7f0) !important;
    border-radius: 8px !important;
}
/* 立即购买 → 品牌渐变按钮 */
.x-shop-keep #submit_buy {
    background: linear-gradient(135deg, #1268ff 0%, #0db9ff 100%) !important;
    color: #fff !important; border: none !important;
    border-radius: 8px !important; height: 46px; line-height: 46px;
    font-size: 15px; font-weight: 800; letter-spacing: 2px;
    box-shadow: 0 14px 28px rgba(18, 104, 255, .25) !important;
    transition: transform .2s ease, box-shadow .2s ease !important;
    padding: 0 !important;
}
.x-shop-keep #submit_buy:hover { transform: translateY(-2px); box-shadow: 0 18px 34px rgba(18, 104, 255, .32) !important; }
/* 返回按钮 */
.x-shop-keep .btn-default {
    background: #fff !important; color: #075bdc !important;
    border: 1px solid #d9e8ff !important; border-radius: 8px !important;
    font-weight: 700; transition: background .2s ease;
}
.x-shop-keep .btn-default:hover { background: #eef5ff !important; }
/* 图片预览虚线框 */
.x-shop-keep #cid_img, .x-shop-keep #subcid_img, .x-shop-keep #toolimg_view { border-color: #b9d4ff !important; }
/* 空状态 → 品牌蓝引导 */
.x-shop-keep div[style*="background-color: #f8f9fa"] {
    background: linear-gradient(180deg, #f7fbff, #eef5ff) !important;
    border: 1px dashed #b9d4ff !important; border-radius: 12px !important;
    padding: 44px 20px !important;
}
.x-shop-keep div[style*="background-color: #f8f9fa"] h3[style] { color: #31517f !important; margin-bottom: 12px !important; font-weight: 800; }
.x-shop-keep div[style*="background-color: #f8f9fa"] p[style] { color: #647391 !important; font-size: 14px !important; }
/* 购物车条 */
.x-shop-keep #alert_cart { background: #eaf3ff; border-radius: 8px; }
.x-shop-keep #alert_cart .btn-danger { background: linear-gradient(135deg, #1268ff, #22c7f0) !important; border: none !important; color: #fff !important; border-radius: 6px !important; }
/* 宫格模式（后台切换 ui_shop 时同样生效） */
.x-shop-keep .btn-info { background: linear-gradient(135deg, #1268ff, #0db9ff) !important; border: none !important; color: #fff !important; border-radius: 8px !important; }
.x-shop-keep .btn-success { background: linear-gradient(135deg, #16d6aa, #0db9ff) !important; border: none !important; color: #fff !important; border-radius: 6px !important; }
.x-shop-keep .widget { border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 10px 26px rgba(36, 83, 160, .08); overflow: hidden; background: #fff; }
.x-shop-keep .widget-content { padding: 16px; }
/* 分类标题 */
.x-shop-keep #goodTypeContent h3 { color: var(--ink); font-weight: 900; }
.x-shop-keep #className { color: var(--primary); }

/* ============ 响应式 ============ */
@media (max-width: 1024px) {
    .hero__inner, .cockpit-layout, .data-layout, .qa-layout { grid-template-columns: 1fr; }
    .scenario-grid, .capability-grid { grid-template-columns: repeat(2, 1fr); }
    .security-grid { grid-template-columns: repeat(2, 1fr); }
    .process-line { grid-template-columns: repeat(3, 1fr); }
    .site-footer-new__inner { grid-template-columns: repeat(2, 1fr); }
    .notice-card, .notice-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .notice-card__title { grid-column: 1 / -1; min-height: 92px; }
    .cockpit-art { order: -1; }
    .hero h1 { font-size: 34px; }
}
@media (max-width: 768px) {
    .hero { min-height: auto; padding-bottom: 20px; }
    .hero__inner { padding: 44px 0 28px; gap: 30px; }
    .scenario-grid, .capability-grid, .security-grid { grid-template-columns: 1fr; }
    .process-line { grid-template-columns: repeat(2, 1fr); }
    .notice-card { margin-top: 16px; grid-template-columns: 1fr; padding: 12px; }
    .notice-list { grid-template-columns: 1fr; }
    .section { padding: 60px 0; }
    .cockpit-section { padding: 70px 0; }
    .header-actions .link-button, .header-actions .service-action { display: none; }
    .site-nav ul { gap: 16px; font-size: 14px; }
    .site-footer-new__inner { grid-template-columns: 1fr; text-align: center; }
    .qa-copy h2 { font-size: 26px; }
    .qa-layout { gap: 28px; }
}
@media (max-width: 420px) {
    .qa-copy h2 { font-size: 24px; }
}
</style>

<!-- ================= Hero ================= -->
<section class="hero" id="home">
    <canvas class="hero__particles" aria-hidden="true"></canvas>
    <div class="hero__inner">
        <div class="hero__copy">
            <p class="eyebrow">企业级自动发卡云服务平台</p>
            <h1>彩虹云商城，让发卡生意稳定增长</h1>
            <p class="hero__lead">为卡密、软件授权、充值权益、会员服务、资料商品等虚拟商品团队提供自动发货、订单查询、资金结算与商户运营一体化能力。</p>
            <div class="hero-tags" aria-label="平台核心能力">
                <span>自动发货</span>
                <span>支付回调</span>
                <span>卡密库存</span>
                <span>商户结算</span>
            </div>
            <div class="hero-actions">
                <a class="solid-button" href="./user/reg.php">开通小店</a>
                <a class="ghost-button" href="#capabilities">查看平台能力</a>
                <a class="query-button" href="#xShop" onclick="xTab('search')">订单查询</a>
            </div>
        </div>
        <div class="hero__visual">
            <a class="hero-app-carousel" id="download" href="./user/login.php" aria-label="下载彩虹云商城商家APP">
                <img src="<?php echo $cdn_img ?>/app-download-carousel.png" width="840" height="450" alt="彩虹云商城商家APP下载">
            </a>
        </div>
    </div>
</section>

<!-- ================= notice-card ================= -->
<section class="notice-card" id="notice">
    <div class="notice-card__title">
        <strong>系统公告</strong>
        <span>最新平台公告</span>
    </div>
    <div class="notice-list">
        <article>
            <i></i>
            <div>
                <h2><a href="./?mod=article">业务模式整改说明</a></h2>
                <p><a href="./?mod=article">为有效防范商户失联、消费者权益受损及投诉率偏高的问题，切实规范平台经营行为，强化风险防控能力。</a></p>
            </div>
        </article>
        <article>
            <i></i>
            <div>
                <h2><a href="./?mod=article">保证金缴纳与退还规则说明</a></h2>
                <p><a href="./?mod=article">为保障交易安全、提升消费者体验并维护平台经营秩序。</a></p>
            </div>
        </article>
        <article class="has-notice-more">
            <a class="notice-more" href="./?mod=article">查看更多</a>
            <i aria-hidden="true"></i>
            <div>
                <h2><a href="./?mod=article">结算规则与支付渠道说明</a></h2>
                <p><a href="./?mod=article">平台提供多种支付通道，当天交易订单在24小时后自动签收。</a></p>
            </div>
        </article>
    </div>
</section>

<!-- ================= scenario-section ================= -->
<section class="section scenario-section" id="scenarios">
    <div class="hsf-layout">
        <div class="section-heading">
            <span>Business Scene</span>
            <h2>覆盖主流虚拟商品交易场景</h2>
            <p>从商品上架到自动交付，从买家查询到商户结算，帮助团队搭建更专业的发卡经营入口。</p>
        </div>
        <div class="scenario-grid">
            <article class="scenario-card">
                <span>01</span>
                <img src="<?php echo $cdn_img ?>/icon-launch.png" alt="">
                <h3>卡密自动发货</h3>
                <p>适配兑换码、授权码、激活码、账号凭证等库存型虚拟商品。</p>
            </article>
            <article class="scenario-card">
                <span>02</span>
                <img src="<?php echo $cdn_img ?>/icon-dashboard.png" alt="">
                <h3>会员权益售卖</h3>
                <p>面向会员服务、课程资料、权益礼包等即时交付商品。</p>
            </article>
            <article class="scenario-card">
                <span>03</span>
                <img src="<?php echo $cdn_img ?>/icon-security.png" alt="">
                <h3>知识付费</h3>
                <p>支持授权码库存、订单凭证、售后备注和续费记录。</p>
            </article>
            <article class="scenario-card">
                <span>04</span>
                <img src="<?php echo $cdn_img ?>/icon-modules.png" alt="">
                <h3>资源下载</h3>
                <p>适合团队统一管理商品、订单、支付、结算和商户服务。</p>
            </article>
        </div>
    </div>
</section>

<!-- ================= capability-section ================= -->
<section class="section capability-section" id="capabilities">
    <div class="hsf-layout">
        <div class="section-heading">
            <span>Core Capability</span>
            <h2>把发卡网该有的核心能力一次配齐</h2>
            <p>不止是一个展示首页，而是一套围绕转化、信任和运营效率设计的企业发卡网门面。</p>
        </div>
        <div class="capability-grid">
            <article class="capability-card">
                <img src="<?php echo $cdn_img ?>/icon-launch.png" alt="">
                <h3>极速开店上线</h3>
                <p>商品、库存、支付、订单流程清晰，适合团队快速搭建发卡业务。</p>
            </article>
            <article class="capability-card">
                <img src="<?php echo $cdn_img ?>/icon-modules.png" alt="">
                <h3>模块化系统能力</h3>
                <p>覆盖卡密导入、库存预警、订单追踪、售后凭证与商户分账。</p>
            </article>
            <article class="capability-card">
                <img src="<?php echo $cdn_img ?>/icon-dashboard.png" alt="">
                <h3>移动端商户后台</h3>
                <p>商户可随时查看订单、商品、库存和账单，处理经营事务更高效。</p>
            </article>
            <article class="capability-card">
                <img src="<?php echo $cdn_img ?>/icon-security.png" alt="">
                <h3>支付风控闭环</h3>
                <p>支付回调、订单状态和风险记录同步留存，异常订单更好核对。</p>
            </article>
            <article class="capability-card">
                <img src="<?php echo $cdn_img ?>/icon-settlement.png" alt="">
                <h3>资金结算透明</h3>
                <p>订单金额、手续费、结算状态集中展示，方便财务核对。</p>
            </article>
            <article class="capability-card">
                <img src="<?php echo $cdn_img ?>/icon-support.png" alt="">
                <h3>服务支持体系</h3>
                <p>围绕开通、部署、售后、升级提供持续服务支持。</p>
            </article>
        </div>
    </div>
</section>

<!-- ================= cockpit-section ================= -->
<section class="cockpit-section">
    <div class="hsf-layout cockpit-layout">
        <div class="cockpit-copy">
            <span>Merchant Cockpit</span>
            <h2>商户经营驾驶舱，把订单和资金看清楚</h2>
            <p>用更直观的页面表达平台实力：订单趋势、库存消耗、支付状态、结算概况和售后记录清晰呈现。</p>
            <ul>
                <li>订单趋势实时追踪</li>
                <li>库存消耗自动预警</li>
                <li>结算账单清晰归档</li>
            </ul>
            <a class="solid-button" href="./user/reg.php">开通小店</a>
        </div>
        <div class="cockpit-art">
            <img src="<?php echo $cdn_img ?>/dashboard-cockpit-v3.png" alt="商户经营驾驶舱">
        </div>
    </div>
</section>

<!-- ================= process-section ================= -->
<section class="section process-section" id="process">
    <div class="hsf-layout">
        <div class="section-heading">
            <span>Onboarding</span>
            <h2>6步完成发卡业务入驻</h2>
            <p>从商户注册到自动发货，帮助团队快速把虚拟商品交易跑起来。</p>
        </div>
        <div class="process-line">
            <button type="button" class="process-step is-active" onclick="showProcess(0)">
                <span><img src="<?php echo $cdn_img ?>/icon-dashboard.png" alt=""></span>
                <em>01</em>注册账户
            </button>
            <button type="button" class="process-step" onclick="showProcess(1)">
                <span><img src="<?php echo $cdn_img ?>/icon-security.png" alt=""></span>
                <em>02</em>绑定收款
            </button>
            <button type="button" class="process-step" onclick="showProcess(2)">
                <span><img src="<?php echo $cdn_img ?>/icon-modules.png" alt=""></span>
                <em>03</em>发布商品
            </button>
            <button type="button" class="process-step" onclick="showProcess(3)">
                <span><img src="<?php echo $cdn_img ?>/icon-launch.png" alt=""></span>
                <em>04</em>买家下单
            </button>
            <button type="button" class="process-step" onclick="showProcess(4)">
                <span><img src="<?php echo $cdn_img ?>/icon-support.png" alt=""></span>
                <em>05</em>自动发货
            </button>
            <button type="button" class="process-step" onclick="showProcess(5)">
                <span><img src="<?php echo $cdn_img ?>/icon-settlement.png" alt=""></span>
                <em>06</em>资金结算
            </button>
        </div>
        <div class="process-panels">
            <article class="process-panel is-active"><h3>注册账户</h3><p>提交基础资料，创建商户账号。</p></article>
            <article class="process-panel"><h3>绑定收款</h3><p>接入支付通道，配置结算信息。</p></article>
            <article class="process-panel"><h3>发布商品</h3><p>创建分类，导入卡密库存。</p></article>
            <article class="process-panel"><h3>买家下单</h3><p>买家付款后系统自动生成订单。</p></article>
            <article class="process-panel"><h3>自动发货</h3><p>系统即时出库并保留发货凭证。</p></article>
            <article class="process-panel"><h3>资金结算</h3><p>账单自动汇总，商户对账更清楚。</p></article>
        </div>
    </div>
</section>

<!-- ================= security-section ================= -->
<section class="section security-section">
    <div class="hsf-layout">
        <div class="section-heading">
            <span>Trust System</span>
            <h2>企业级发卡网，信任感必须放在第一屏之后</h2>
            <p>从支付、数据、售后、资质到服务响应，强化买家和商户对平台的信任。</p>
        </div>
        <div class="security-grid">
            <article class="security-card">
                <img src="<?php echo $cdn_img ?>/icon-security.png" alt="">
                <h3>支付风控</h3>
                <p>支付回调与订单状态同步记录，异常交易更易追踪。</p>
            </article>
            <article class="security-card">
                <img src="<?php echo $cdn_img ?>/icon-settlement.png" alt="">
                <h3>资金结算</h3>
                <p>商户账单、结算状态、订单金额清楚展示，减少对账压力。</p>
            </article>
            <article class="security-card">
                <img src="<?php echo $cdn_img ?>/icon-support.png" alt="">
                <h3>售后响应</h3>
                <p>围绕订单查询、发货凭证和售后备注建立服务链路。</p>
            </article>
            <article class="security-card">
                <img src="<?php echo $cdn_img ?>/icon-modules.png" alt="">
                <h3>系统扩展</h3>
                <p>可围绕更多商品类型、商户角色和经营流程持续扩展。</p>
            </article>
        </div>
    </div>
</section>

<!-- ================= data-section ================= -->
<section class="section data-section" id="company">
    <div class="hsf-layout data-layout">
        <div class="data-copy">
            <span>Data Proof</span>
            <h2>专注企业级发卡网服务，沉淀长期经营能力</h2>
            <p>用清晰的数据与服务能力展示平台可信度，帮助首页形成更强转化。</p>
        </div>
        <div class="stats-panel">
            <article><strong data-counter data-count="4" data-suffix="年">0</strong><span>年稳健运营</span></article>
            <article><strong data-counter data-count="5002879" data-suffix="张">0</strong><span>成功发卡</span></article>
            <article><strong data-counter data-count="36820" data-suffix="户">0</strong><span>服务终端用户</span></article>
            <article><strong data-counter data-count="24" data-suffix="h">0</strong><span>小时售后响应</span></article>
        </div>
    </div>
</section>

<!-- ================= qa-section ================= -->
<section class="section qa-section" id="faq">
    <div class="hsf-layout qa-layout">
        <div class="qa-copy">
            <span>FAQ</span>
            <h2>商户常见问题</h2>
            <p>围绕开店、商品、支付、发货、售后和结算整理高频问题。</p>
            <article class="company-copy">
                <h3>关于<?php echo $conf['sitename'] ?></h3>
                <p><?php echo $conf['description'] ?></p>
            </article>
        </div>
        <div class="faq-list">
            <article class="faq-item is-open">
                <button type="button" onclick="toggleFaq(this)" aria-expanded="true">
                    <span><?php echo $conf['sitename'] ?>适合哪些商品？</span><i></i>
                </button>
                <div class="faq-item__body"><p>适合卡密、软件授权、充值券、会员权益、资料包、账号凭证等虚拟商品自动发货。</p></div>
            </article>
            <article class="faq-item">
                <button type="button" onclick="toggleFaq(this)" aria-expanded="false">
                    <span>是否支持自动发货？</span><i></i>
                </button>
                <div class="faq-item__body"><p>支持。商户导入卡密库存后，用户付款成功即可自动出库并展示发货结果。</p></div>
            </article>
            <article class="faq-item">
                <button type="button" onclick="toggleFaq(this)" aria-expanded="false">
                    <span>订单和售后能否追踪？</span><i></i>
                </button>
                <div class="faq-item__body"><p>订单状态、付款记录、发货凭证和售后备注可统一留存，方便客服核对。</p></div>
            </article>
            <article class="faq-item">
                <button type="button" onclick="toggleFaq(this)" aria-expanded="false">
                    <span>手机端可以管理商户后台吗？</span><i></i>
                </button>
                <div class="faq-item__body"><p>可以。商户可在移动端查看订单、商品、库存和账单，适合团队随时处理业务。</p></div>
            </article>
            <article class="faq-item">
                <button type="button" onclick="toggleFaq(this)" aria-expanded="false">
                    <span>能否作为企业官网展示？</span><i></i>
                </button>
                <div class="faq-item__body"><p>模板已适配企业级官网展示，首屏、优势、公告、流程、保障和页脚内容均可维护。</p></div>
            </article>
            <article class="faq-item">
                <button type="button" onclick="toggleFaq(this)" aria-expanded="false">
                    <span>资金结算是否清晰？</span><i></i>
                </button>
                <div class="faq-item__body"><p>平台提供订单金额、手续费、结算状态等数据展示，便于商户对账。</p></div>
            </article>
        </div>
    </div>
</section>

<script>
// process tabs
function showProcess(i) {
    document.querySelectorAll('.process-step').forEach((el, idx) => el.classList.toggle('is-active', idx === i));
    document.querySelectorAll('.process-panel').forEach((el, idx) => el.classList.toggle('is-active', idx === i));
}
// faq toggle
function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('is-open');
    item.classList.toggle('is-open');
    btn.setAttribute('aria-expanded', !isOpen);
}
// site-header scroll 效果
document.addEventListener('scroll', () => {
    const h = document.querySelector('.site-header');
    if (h) h.classList.toggle('is-scrolled', window.scrollY > 20);
});

// data-counter 数字滚动动画（进入视口触发，与参考站一致）
(function () {
    var countersDone = [];

    function parseCount(value) {
        var number = parseFloat(String(value).replace(/,/g, ''));
        return isNaN(number) ? 0 : number;
    }

    function formatNumber(value) {
        return Math.floor(value).toLocaleString('zh-CN');
    }

    function animateCounter(element) {
        if (countersDone.indexOf(element) !== -1) {
            return;
        }

        countersDone.push(element);
        var target = parseCount(element.getAttribute('data-count'));
        var suffix = element.getAttribute('data-suffix') || '';
        var duration = 1200;
        var start = null;

        function tick(timestamp) {
            if (!start) {
                start = timestamp;
            }

            var progress = Math.min((timestamp - start) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            element.textContent = formatNumber(target * eased) + suffix;

            if (progress < 1) {
                window.requestAnimationFrame(tick);
            } else {
                element.textContent = formatNumber(target) + suffix;
            }
        }

        window.requestAnimationFrame(tick);
    }

    var counterItems = document.querySelectorAll('[data-counter]');
    if (!counterItems.length) return;

    if (!('IntersectionObserver' in window)) {
        for (var i = 0; i < counterItems.length; i += 1) {
            animateCounter(counterItems[i]);
        }
        return;
    }

    var counterObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    counterItems.forEach(function (item) {
        counterObserver.observe(item);
    });
})();

(function () {
    var canvas = document.querySelector('.hero__particles');
    if (!canvas || !canvas.getContext) return;
    var context = canvas.getContext('2d');
    if (!context) return;

    var particles = [];
    var width = 0;
    var height = 0;

    function resize() {
        width = canvas.offsetWidth;
        height = canvas.offsetHeight;
        if (width < 1 || height < 1) return;
        canvas.width = width;
        canvas.height = height;
        particles = [];
        for (var i = 0; i < 46; i += 1) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 0.28,
                vy: (Math.random() - 0.5) * 0.28,
                r: 1 + Math.random() * 2.4
            });
        }
    }

    function draw() {
        if (width < 1 || height < 1) { resize(); }
        context.clearRect(0, 0, width, height);
        context.fillStyle = 'rgba(168, 218, 220, .72)';

        for (var p = 0; p < particles.length; p += 1) {
            var particle = particles[p];
            particle.x += particle.vx;
            particle.y += particle.vy;

            if (particle.x < 0 || particle.x > width) { particle.vx *= -1; }
            if (particle.y < 0 || particle.y > height) { particle.vy *= -1; }

            context.beginPath();
            context.arc(particle.x, particle.y, particle.r, 0, Math.PI * 2);
            context.fill();

            for (var i = p + 1; i < particles.length; i += 1) {
                var other = particles[i];
                var dx = particle.x - other.x;
                var dy = particle.y - other.y;
                var distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 128) {
                    context.strokeStyle = 'rgba(255, 255, 255, ' + ((1 - distance / 128) * 0.16).toFixed(3) + ')';
                    context.lineWidth = 1;
                    context.beginPath();
                    context.moveTo(particle.x, particle.y);
                    context.lineTo(other.x, other.y);
                    context.stroke();
                }
            }
        }

        window.requestAnimationFrame(draw);
    }

    resize();
    window.addEventListener('resize', resize);
    draw();
})();

</script>

<!-- 客服弹窗 -->
<div id="qrcodeModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;" onclick="if(event.target===this)hideQrcode()">
    <div style="background:#fff;border-radius:16px;padding:36px 40px;text-align:center;position:relative;">
        <h3 style="margin:0 0 16px;font-size:18px;color:#1a2a48;">联系在线客服</h3>
        <div style="width:200px;height:200px;margin:0 auto 16px;background:#f0f4ff;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#94a3c2;font-size:14px;">
            <img src="template/XHY-01/assets/img/kefu-qr-3.png" alt="客服二维码" style="width:100%;height:100%;border-radius:12px;object-fit:cover;">
        </div>
        <p style="margin:0;color:#647391;font-size:13px;">扫码添加客服，快速响应您的问题</p>
        <button onclick="hideQrcode()" style="margin-top:16px;padding:8px 24px;background:#1268ff;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:14px;">关闭</button>
    </div>
</div>
<script>
function showQrcode(){document.getElementById('qrcodeModal').style.display='flex';}
function hideQrcode(){document.getElementById('qrcodeModal').style.display='none';}
</script>
