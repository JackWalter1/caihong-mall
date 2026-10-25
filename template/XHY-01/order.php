<?php
$oq_logo = 'template/XHY-01/assets/img/logo-full.png?v=20261023';
?><!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订单查询 - <?php echo htmlspecialchars($conf['sitename']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($conf['sitename']); ?>订单查询，输入预留联系方式或订单号即可查询订单状态与卡密，支持申请售后。">
    <link rel="icon" href="/favicon.ico">
    <script src="./assets/Agod/jquery.min.js"></script>
    <style>
    :root {
        --primary: #1268ff;
        --primary-dark: #073fbe;
        --primary-soft: #eaf3ff;
        --cyan: #00cfff;
        --text: #1a2a48;
        --muted: #647391;
        --line: #dfebff;
        --panel: #ffffff;
        --page: #f7faff;
        --shadow: 0 18px 46px rgba(31, 83, 168, .12);
    }
    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; background: var(--page); color: var(--text);
        font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Microsoft YaHei", Helvetica, Arial, sans-serif; }
    a { text-decoration: none; }

    /* ============ 顶部导航（与首页一致） ============ */
    .oq-header { position: fixed; inset: 0 0 auto 0; z-index: 60; height: 68px;
        background: rgba(255, 255, 255, .92); border-bottom: 1px solid rgba(211, 226, 255, .75);
        backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px); }
    .oq-header__inner { max-width: 1240px; margin: 0 auto; padding: 0 28px; height: 68px;
        display: flex; align-items: center; justify-content: space-between; gap: 24px; }
    .oq-brand { display: flex; align-items: center; flex-shrink: 0; }
    .oq-brand img { height: 32px; width: auto; max-width: 260px; object-fit: contain; display: block; }
    .oq-nav { flex: 1 1 auto; display: flex; align-items: center; justify-content: center; min-width: 0; }
    .oq-nav ul { display: flex; align-items: center; gap: 28px; list-style: none; margin: 0; padding: 0; }
    .oq-nav li { position: relative; }
    .oq-nav a { display: block; color: #25375d; font-size: 14px; font-weight: 700; line-height: 68px;
        white-space: nowrap; transition: color .2s; }
    .oq-nav > ul > li > a::after { content: ""; position: absolute; left: 50%; bottom: 16px; width: 0; height: 3px;
        border-radius: 3px; background: linear-gradient(90deg, var(--primary), var(--cyan));
        transform: translateX(-50%); transition: width .2s ease; }
    .oq-nav a:hover { color: var(--primary); }
    .oq-nav > ul > li > a:hover::after { width: 22px; }
    .oq-header__cta { display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 22px;
        border-radius: 8px; color: #fff; font-size: 14px; font-weight: 800; white-space: nowrap;
        background: linear-gradient(135deg, #1268ff 0%, #0db9ff 100%);
        box-shadow: 0 14px 28px rgba(18, 104, 255, .25); }

    /* ============ Hero 横幅 ============ */
    .oq-hero { margin-top: 68px; padding: 58px 28px 108px; text-align: center; color: #fff;
        background: linear-gradient(135deg, #07265c 0%, #0a3f9e 48%, #1268ff 100%); position: relative; overflow: hidden; }
    .oq-hero::before, .oq-hero::after { content: ""; position: absolute; border-radius: 50%; }
    .oq-hero::before { width: 340px; height: 340px; right: -90px; top: -140px;
        background: radial-gradient(circle, rgba(0, 207, 255, .35), transparent 70%); }
    .oq-hero::after { width: 260px; height: 260px; left: -80px; bottom: -150px;
        background: radial-gradient(circle, rgba(255, 255, 255, .14), transparent 70%); }
    .oq-hero h1 { margin: 0 0 14px; font-size: 34px; font-weight: 800; letter-spacing: 1px; position: relative; z-index: 1; }
    .oq-hero p { margin: 0 auto; max-width: 640px; font-size: 15px; line-height: 1.8;
        color: rgba(255, 255, 255, .82); position: relative; z-index: 1; }

    /* ============ 主体 ============ */
    .oq-main { width: min(1080px, calc(100% - 48px)); margin: -64px auto 0; position: relative; z-index: 2; }
    .oq-card { background: var(--panel); border: 1px solid var(--line); border-radius: 16px;
        box-shadow: var(--shadow); padding: 34px 38px; }
    .oq-card + .oq-card { margin-top: 26px; }

    .oq-query-title { margin: 0 0 6px; font-size: 20px; font-weight: 800; color: var(--text); }
    .oq-query-sub { margin: 0 0 22px; font-size: 14px; color: var(--muted); }
    .oq-search { display: flex; gap: 14px; }
    .oq-search input { flex: 1 1 auto; min-width: 0; height: 52px; padding: 0 18px; border: 1.5px solid var(--line);
        border-radius: 10px; font-size: 15px; color: var(--text); outline: none; background: #fbfdff;
        transition: border-color .2s, box-shadow .2s, background .2s; }
    .oq-search input::placeholder { color: #9fb0cc; }
    .oq-search input:focus { border-color: var(--primary); background: #fff;
        box-shadow: 0 0 0 4px rgba(18, 104, 255, .12); }
    .oq-search button { flex-shrink: 0; height: 52px; padding: 0 34px; border: none; border-radius: 10px;
        color: #fff; font-size: 15px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #1268ff 0%, #0db9ff 100%);
        box-shadow: 0 14px 28px rgba(18, 104, 255, .25); transition: transform .2s, box-shadow .2s, filter .2s; }
    .oq-search button:hover { transform: translateY(-1px); filter: brightness(1.05); }
    .oq-search button:active { transform: translateY(0); }

    .oq-disclaimer { margin-top: 20px; display: flex; align-items: flex-start; gap: 10px; padding: 13px 16px;
        border-radius: 10px; background: #fff8ec; border: 1px solid #ffe3ba; font-size: 13px; line-height: 1.75; color: #a15c00; }
    .oq-disclaimer svg { flex-shrink: 0; width: 17px; height: 17px; margin-top: 3px; color: #ff9a2e; }

    /* ============ 结果表格 ============ */
    .order-result { margin-top: 22px; }
    .oq-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden;
        font-size: 13.5px; border: 1px solid var(--line); }
    .oq-table th { background: #f2f7ff; color: #31517f; font-weight: 700; padding: 12px 14px; text-align: left; white-space: nowrap; }
    .oq-table td { padding: 12px 14px; border-top: 1px solid #eaf1fd; color: var(--text); word-break: break-all; }
    .oq-table tbody tr { transition: background .15s; }
    .oq-table tbody tr:hover { background: #f8fbff; }
    .oq-status { display: inline-block; padding: 3px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
    .oq-status.s0 { background: #e8f3ff; color: #165dff; }
    .oq-status.s1 { background: #e8ffea; color: #00b42a; }
    .oq-status.s2 { background: #e8ffea; color: #00b42a; }
    .oq-status.s3 { background: #ffece8; color: #f53f3f; }
    .oq-status.s4 { background: #fff7e8; color: #ff7d00; }
    .oq-copy { cursor: pointer; color: var(--primary); font-size: 12px; margin-left: 8px;
        border: 1px solid #bedaff; border-radius: 4px; padding: 1px 8px; background: #f2f7ff; }
    .oq-empty { text-align: center; color: #86909c; padding: 34px 0; background: #fbfdff;
        border: 1px dashed var(--line); border-radius: 12px; }

    /* ============ 温馨提示卡 ============ */
    .oq-tips-head { display: flex; align-items: center; gap: 10px; margin: 0 0 18px; }
    .oq-tips-head .badge { width: 34px; height: 34px; border-radius: 10px; display: inline-flex; align-items: center;
        justify-content: center; background: linear-gradient(135deg, #fff3e0, #ffe3ba); color: #ff9a2e; }
    .oq-tips-head .badge svg { width: 19px; height: 19px; }
    .oq-tips-head h2 { margin: 0; font-size: 18px; font-weight: 800; }
    .oq-tips-lead { margin: 0 0 16px; font-size: 14px; line-height: 1.9; color: var(--text); }
    .oq-tips-lead strong { color: #e13c39; }
    .oq-issue-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 14px; margin: 0 0 18px; }
    .oq-issue { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 10px;
        background: #f8fbff; border: 1px solid #eaf1fd; font-size: 13.5px; line-height: 1.7; color: #33456b; }
    .oq-issue i { font-style: normal; flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%; margin-top: 1px;
        display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;
        color: var(--primary); background: var(--primary-soft); }
    .oq-alert { margin: 0; padding: 13px 16px; border-radius: 10px; font-size: 13.5px; line-height: 1.75;
        background: #fff1f0; border: 1px solid #ffccc7; color: #cf1322; font-weight: 600; }

    /* ============ 页脚 ============ */
    .oq-footer { margin-top: 40px; padding: 26px 28px 30px; border-top: 1px solid var(--line);
        background: #fff; text-align: center; }
    .oq-footer p { margin: 0; font-size: 13px; color: var(--muted); line-height: 1.9; }
    .oq-footer a { color: var(--primary); }

    /* ============ 验证码弹窗 ============ */
    .oq-modal { position: fixed; inset: 0; z-index: 1000; display: none; }
    .oq-modal.is-open { display: block; }
    .oq-modal__mask { position: absolute; inset: 0; background: rgba(9, 20, 44, .55); backdrop-filter: blur(2px); }
    .oq-modal__wrap { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .oq-modal__box { width: 360px; max-width: 100%; background: #fff; border-radius: 16px;
        box-shadow: 0 24px 64px rgba(7, 22, 47, .3); overflow: hidden; animation: oqPop .2s ease; }
    @keyframes oqPop { from { transform: translateY(14px) scale(.97); opacity: 0; } to { transform: none; opacity: 1; } }
    .oq-modal__head { position: relative; padding: 18px 20px 0; text-align: center; font-size: 16px; font-weight: 800; color: var(--text); }
    .oq-modal__close { position: absolute; top: 12px; right: 12px; width: 30px; height: 30px; border: none; border-radius: 8px;
        background: #f2f6fd; color: #647391; font-size: 15px; cursor: pointer; line-height: 1; transition: background .2s, color .2s; }
    .oq-modal__close:hover { background: #e5eeff; color: var(--primary); }
    .oq-modal__body { padding: 18px 24px 4px; }
    .oq-modal__body img { display: block; width: 230px; max-width: 100%; margin: 0 auto 16px; border-radius: 8px;
        border: 1px solid var(--line); cursor: pointer; }
    .oq-modal__body input { width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid var(--line); border-radius: 10px;
        font-size: 14px; outline: none; transition: border-color .2s, box-shadow .2s; }
    .oq-modal__body input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(18, 104, 255, .12); }
    .oq-modal__foot { display: flex; gap: 12px; padding: 18px 24px 22px; }
    .oq-modal__foot button { flex: 1; height: 42px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer;
        transition: filter .2s; }
    .oq-btn-plain { border: 1px solid var(--line); background: #fff; color: var(--muted); }
    .oq-btn-plain:hover { filter: brightness(.97); }
    .oq-btn-primary { border: none; color: #fff; background: linear-gradient(135deg, #1268ff 0%, #0db9ff 100%); }
    .oq-btn-primary:hover { filter: brightness(1.05); }

    .toast-x { position: fixed; left: 50%; top: 12%; transform: translateX(-50%); background: rgba(17, 32, 51, .92);
        color: #fff; padding: 12px 24px; border-radius: 10px; font-size: 14px; z-index: 9999;
        box-shadow: 0 10px 26px rgba(0, 0, 0, .2); transition: opacity .3s; }

    /* ============ 响应式 ============ */
    @media (max-width: 900px) {
        .oq-nav { display: none; }
        .oq-hero { padding: 44px 24px 96px; }
        .oq-hero h1 { font-size: 26px; }
        .oq-main { width: calc(100% - 32px); }
        .oq-card { padding: 26px 22px; }
        .oq-search { flex-direction: column; }
        .oq-search button { justify-content: center; }
        .oq-issue-grid { grid-template-columns: 1fr; }
        .oq-table { display: block; overflow-x: auto; }
    }
    </style>
</head>
<body>
<header class="oq-header">
    <div class="oq-header__inner">
        <a class="oq-brand" href="./"><img src="<?php echo $oq_logo; ?>" alt="<?php echo htmlspecialchars($conf['sitename']); ?>"></a>
        <nav class="oq-nav" aria-label="主导航">
            <ul>
                <li><a href="./">网站首页</a></li>
                <li><a href="./?mod=goods-release">禁售目录</a></li>
                <li><a href="./?mod=notice">最新公告</a></li>
                <li><a href="./?mod=about">公司介绍</a></li>
                <li><a href="./?mod=shop">演示店铺</a></li>
                <li><a href="./?mod=riskcontrol">风控名单</a></li>
            </ul>
        </nav>
        <a class="oq-header__cta" href="javascript:;" onclick="var q=document.getElementById('qKeyword'); if(q){ q.focus({preventScroll:true}); q.scrollIntoView({behavior:'smooth',block:'center'}); }">
            <svg width="16" height="16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="4" stroke-linecap="butt" stroke-linejoin="miter"><path d="M33.072 33.071c6.248-6.248 6.248-16.379 0-22.627-6.249-6.249-16.38-6.249-22.628 0-6.248 6.248-6.248 16.379 0 22.627 6.248 6.248 16.38 6.248 22.628 0Zm0 0 8.485 8.485"></path></svg>
            订单查询
        </a>
    </div>
</header>

<section class="oq-hero">
    <h1>订单查询 · 售后申请</h1>
    <p>输入下单时预留的联系方式或订单号，即可查询订单状态与卡密内容，支持在线申请售后。</p>
</section>

<main class="oq-main">
    <div class="oq-card">
        <h2 class="oq-query-title">轻松查询订单，即刻享受自动交易</h2>
        <p class="oq-query-sub">在此输入您的预留联系方式/订单号，如遗忘请联系平台客服找回</p>
        <div class="oq-search">
            <input type="text" id="qKeyword" placeholder="请输入预留联系方式/订单号" onkeydown="if(event.key==='Enter'){startQuery()}">
            <button type="button" onclick="startQuery()">
                <svg width="16" height="16" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="4" stroke-linecap="butt" stroke-linejoin="miter"><path d="M33.072 33.071c6.248-6.248 6.248-16.379 0-22.627-6.249-6.249-16.38-6.249-22.628 0-6.248 6.248-6.248 16.379 0 22.627 6.248 6.248 16.38 6.248 22.628 0Zm0 0 8.485 8.485"></path></svg>
                查询订单
            </button>
        </div>
        <div class="oq-disclaimer">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="4" stroke-linecap="butt" stroke-linejoin="miter"><path d="m14 15 10-7v32l-10-7H6V15h8Z" fill="currentColor" stroke="none"></path><path d="M24.924 6.226A2 2 0 0 1 26 8v32a2 2 0 0 1-3.147 1.639L13.37 35H6a2 2 0 0 1-2-2V15a2 2 0 0 1 2-2h7.37l9.483-6.638a2 2 0 0 1 2.07-.136ZM35.314 35.042c6.248-6.249 6.248-16.38 0-22.628l2.828-2.828c7.81 7.81 7.81 20.474 0 28.284l-2.828-2.828Z" fill="currentColor" stroke="none"></path><path d="M29.657 29.728a8 8 0 0 0 0-11.314l2.828-2.828c4.687 4.686 4.687 12.284 0 16.97l-2.828-2.828Z" fill="currentColor" stroke="none"></path></svg>
            <span>免责声明：卖家次日可在本平台申请提现，因此如果您对订单存在疑惑，请在24小时内向平台客服反馈，逾期请自行与卖家协商解决。</span>
        </div>
        <div class="order-result" id="orderResult"></div>
    </div>

    <div class="oq-card">
        <div class="oq-tips-head">
            <span class="badge">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="4" stroke-linecap="butt" stroke-linejoin="miter"><path fill-rule="evenodd" clip-rule="evenodd" d="M24 44c11.046 0 20-8.954 20-20S35.046 4 24 4 4 12.954 4 24s8.954 20 20 20Zm-2-11a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2Zm4-18a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V15Z" fill="currentColor" stroke="none"></path></svg>
            </span>
            <h2>温馨提示</h2>
        </div>
        <p class="oq-tips-lead"><strong>遇到以下售后问题，请在24小时内在平台上申请售后</strong>（一个订单只能申请一次售后，商家未处理好问题前，不要撤销售后）：</p>
        <div class="oq-issue-grid">
            <div class="oq-issue"><i>1</i><span>承诺充值返现</span></div>
            <div class="oq-issue"><i>2</i><span>虚假卡密、过期卡密</span></div>
            <div class="oq-issue"><i>3</i><span>卡密无意义，仅为提示作用</span></div>
            <div class="oq-issue"><i>4</i><span>卡密或软件有问题，卖家不售后</span></div>
            <div class="oq-issue"><i>5</i><span>购买的商品为实物，需要快递发货</span></div>
            <div class="oq-issue"><i>6</i><span>通过扫描他人给予的二维码进行支付的</span></div>
            <div class="oq-issue"><i>7</i><span>卡密内容为“联系XX取卡/发货/服务”、“付款成功，等待充值”</span></div>
            <div class="oq-issue"><i>8</i><span>以各种理由推脱到第二天</span></div>
        </div>
        <p class="oq-alert">遇到以上售后问题，请在24小时内在平台上申请售后或联系平台客服处理。</p>
    </div>
</main>

<footer class="oq-footer">
    <p>© <?php echo htmlspecialchars($conf['sitename']); ?> · <a href="./">返回首页</a></p>
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
    <p><?php echo implode(' | ', $__items); ?></p>
<?php } ?>
</footer>

<!-- 验证码弹窗 -->
<div class="oq-modal" id="codeModal">
    <div class="oq-modal__mask" onclick="hideCode()"></div>
    <div class="oq-modal__wrap">
        <div class="oq-modal__box">
            <div class="oq-modal__head">请输入验证码
                <button type="button" class="oq-modal__close" aria-label="关闭" onclick="hideCode()">✕</button>
            </div>
            <div class="oq-modal__body">
                <img id="codeImg" src="" alt="验证码" title="看不清？点击更换" onclick="this.src=CODE_URL+'?act=code&t='+Date.now()">
                <input type="text" id="codeInput" placeholder="输入验证码" onkeydown="if(event.key==='Enter'){submitQuery()}">
            </div>
            <div class="oq-modal__foot">
                <button type="button" class="oq-btn-plain" onclick="hideCode()">取消</button>
                <button type="button" class="oq-btn-primary" onclick="submitQuery()">确定</button>
            </div>
        </div>
    </div>
</div>

<script>
var CODE_URL = './template/XHY-01/orderquery.ajax.php';
function toast(msg) {
    var t = document.createElement('div');
    t.className = 'toast-x'; t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function () { t.style.opacity = '0'; setTimeout(function () { t.remove(); }, 350); }, 2000);
}
function showCode() {
    document.getElementById('codeModal').classList.add('is-open');
    document.getElementById('codeImg').src = CODE_URL + '?act=code&t=' + Date.now();
    document.getElementById('codeInput').value = '';
    document.getElementById('codeInput').focus();
}
function hideCode() { document.getElementById('codeModal').classList.remove('is-open'); }
function startQuery() {
    var kw = document.getElementById('qKeyword').value.trim();
    if (!kw) { toast('请输入预留联系方式/订单号'); return; }
    showCode();
}
function submitQuery() {
    var kw = document.getElementById('qKeyword').value.trim();
    var code = document.getElementById('codeInput').value.trim();
    if (!kw) { toast('请输入预留联系方式/订单号'); return; }
    if (!code) { toast('请输入验证码'); return; }
    var fd = new FormData();
    fd.append('act', 'query');
    fd.append('keyword', kw);
    fd.append('code', code);
    fetch(CODE_URL, { method: 'POST', body: fd }).then(function (r) { return r.json(); }).then(function (d) {
        hideCode();
        if (d.code == 0) { renderResult(d.data || []); }
        else { toast(d.msg || '查询失败'); }
    }).catch(function () { hideCode(); toast('网络异常，请稍后再试'); });
}
var STATUS = { 0: ['待支付', 's0'], 1: ['已支付', 's1'], 2: ['交易完成', 's2'], 3: ['已退款', 's3'], 4: ['售后中', 's4'] };
function esc(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
function renderResult(list) {
    var box = document.getElementById('orderResult');
    if (!list.length) { box.innerHTML = '<div class="oq-empty">未查询到相关订单，请核对预留联系方式/订单号</div>'; return; }
    var html = '<div style="overflow-x:auto;"><table class="oq-table"><thead><tr><th>订单号</th><th>商品</th><th>数量</th><th>金额</th><th>状态</th><th>卡密/内容</th><th>下单时间</th></tr></thead><tbody>';
    list.forEach(function (o) {
        var st = STATUS[o.status] || ['未知', 's0'];
        var result = o.status == 2 && o.result ? '<span>' + esc(o.result) + '</span><span class="oq-copy" onclick="copyTxt(this)">复制</span>' : '—';
        html += '<tr><td>' + esc(o.tradeno) + '</td><td>' + esc(o.name) + '</td><td>' + esc(o.value) + '</td><td>￥' + esc(o.money) + '</td><td><span class="oq-status ' + st[1] + '">' + st[0] + '</span></td><td>' + result + '</td><td>' + esc(o.addtime) + '</td></tr>';
    });
    html += '</tbody></table></div>';
    box.innerHTML = html;
    box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function copyTxt(el) {
    var txt = el.parentNode.textContent.replace('复制', '').trim();
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(txt).then(function () { toast('已复制'); });
    } else { toast(txt); }
}
</script>
</body>
</html>
