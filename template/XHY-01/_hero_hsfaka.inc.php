<style>
/* 隐藏旧模板与复刻区块重复的部分，保留 x-shop 下单核心区 */
.x-hero, .x-counter, .x-cta { display: none !important; }
.x-section:not(.x-shop) { display: none !important; }

/* ========== Hero ========== */
.hsf-hero {
    background: linear-gradient(180deg, #f0f7ff 0%, #fff 100%),
                url('template/XHY-01/assets/img/hero-bg.png');
    background-size: cover;
    background-position: center, center;
    padding: 60px 0 40px;
    position: relative;
    overflow: hidden;
}
.hsf-hero::before {
    content: '';
    position: absolute;
    right: -100px;
    top: 30px;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(22,93,255,0.08) 0%, transparent 70%);
    border-radius: 50%;
}
.hsf-container { max-width: 1200px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 1; }
.hsf-hero-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; }
.hsf-eyebrow { display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px; background: #e8f3ff; color: #165dff; border-radius: 20px; font-size: 14px; margin-bottom: 20px; }
.hsf-eyebrow::before { content: ''; width: 24px; height: 2px; background: #165dff; }
.hsf-hero h1 { font-size: 40px; font-weight: 700; line-height: 1.2; margin-bottom: 16px; color: #1d2129; }
.hsf-hero-desc { font-size: 15px; color: #4e5969; margin-bottom: 24px; max-width: 560px; }
.hsf-hero-tags { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 28px; }
.hsf-tag-item { padding: 6px 16px; background: #fff; border: 1px solid #e5e6eb; border-radius: 20px; font-size: 13px; color: #4e5969; }
.hsf-hero-buttons { display: flex; gap: 12px; flex-wrap: wrap; }
.hsf-btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 24px; border-radius: 6px; font-size: 14px; cursor: pointer; border: none; text-decoration: none; transition: all 0.2s; }
.hsf-btn-solid { background: linear-gradient(135deg, #165dff, #4080ff); color: #fff; }
.hsf-btn-solid:hover { opacity: 0.9; color: #fff; }
.hsf-btn-ghost { background: #fff; color: #4e5969; border: 1px solid #e5e6eb; }
.hsf-btn-ghost:hover { border-color: #165dff; color: #165dff; }
.hsf-btn-query { background: #f7f8fa; color: #4e5969; border: 1px solid #e5e6eb; }
.hsf-btn-query:hover { border-color: #165dff; color: #165dff; }
.hsf-hero-visual img { width: 100%; height: auto; border-radius: 12px; }

/* ========== Notice Card ========== */
.hsf-notice-card { background: #fff; border: 1px solid #e5e6eb; border-radius: 12px; padding: 24px 28px; margin: 40px 0; display: flex; gap: 32px; }
.hsf-notice-title { flex-shrink: 0; min-width: 120px; }
.hsf-notice-title strong { display: block; font-size: 18px; color: #1d2129; }
.hsf-notice-title span { font-size: 13px; color: #86909c; }
.hsf-notice-list { flex: 1; }
.hsf-notice-list article { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f2f3f5; }
.hsf-notice-list article:last-child { border-bottom: none; }
.hsf-notice-list i { width: 6px; height: 6px; background: #165dff; border-radius: 50%; margin-top: 10px; flex-shrink: 0; }
.hsf-notice-list h3 { font-size: 14px; font-weight: 600; color: #1d2129; margin-bottom: 4px; }
.hsf-notice-list h3 a { color: #1d2129; text-decoration: none; }
.hsf-notice-list h3 a:hover { color: #165dff; }
.hsf-notice-list p { font-size: 13px; color: #86909c; line-height: 1.6; margin: 0; }

/* ========== Section Common ========== */
.hsf-section { padding: 60px 0; }
.hsf-section-heading { text-align: center; margin-bottom: 40px; }
.hsf-section-eyebrow { font-size: 13px; color: #165dff; font-weight: 600; letter-spacing: 2px; margin-bottom: 8px; }
.hsf-section-title { font-size: 28px; font-weight: 700; color: #1d2129; margin-bottom: 10px; }
.hsf-section-desc { font-size: 14px; color: #86909c; max-width: 600px; margin: 0 auto; }

/* ========== Scenario ========== */
.hsf-scenario-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.hsf-scenario-card { background: #fff; border-radius: 12px; padding: 28px 20px; border: 1px solid #e5e6eb; transition: all 0.3s; }
.hsf-scenario-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); border-color: #165dff; }
.hsf-scenario-num { font-size: 32px; font-weight: 700; color: #e8f3ff; margin-bottom: 10px; }
.hsf-scenario-card img { width: 82px; height: 82px; margin-bottom: 12px; }
.hsf-scenario-card h3 { font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #1d2129; }
.hsf-scenario-card p { font-size: 13px; color: #86909c; line-height: 1.7; }

/* ========== Capability ========== */
.hsf-capability-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.hsf-cap-card { padding: 28px 24px; background: #fff; border: 1px solid #e5e6eb; border-radius: 12px; transition: all 0.3s; }
.hsf-cap-card:hover { border-color: #165dff; box-shadow: 0 4px 16px rgba(22,93,255,0.1); }
.hsf-cap-card img { width: 86px; height: 86px; margin-bottom: 14px; }
.hsf-cap-card h3 { font-size: 17px; font-weight: 600; margin-bottom: 8px; color: #1d2129; }
.hsf-cap-card p { font-size: 13px; color: #86909c; line-height: 1.7; }

/* ========== Cockpit ========== */
.hsf-cockpit { background: #0a1628; color: #fff; padding: 60px 0; position: relative; overflow: hidden; }
.hsf-cockpit::after { content: ''; position: absolute; right: -150px; bottom: -150px; width: 500px; height: 500px; background: radial-gradient(circle, rgba(22,93,255,0.15) 0%, transparent 70%); border-radius: 50%; }
.hsf-cockpit-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; position: relative; z-index: 1; }
.hsf-cockpit .hsf-section-eyebrow { color: #4080ff; }
.hsf-cockpit h2 { font-size: 30px; font-weight: 700; margin-bottom: 14px; color: #fff; }
.hsf-cockpit-desc { font-size: 14px; color: #a9b4c2; margin-bottom: 24px; line-height: 1.7; }
.hsf-cockpit ul { list-style: none; padding: 0; margin-bottom: 28px; }
.hsf-cockpit ul li { padding: 10px 0 10px 28px; position: relative; color: #d4dae3; font-size: 15px; }
.hsf-cockpit ul li::before { content: ''; position: absolute; left: 0; top: 16px; width: 10px; height: 10px; background: #00d26a; border-radius: 50%; }
.hsf-cockpit-art img { width: 100%; height: auto; border-radius: 12px; }

/* ========== Process ========== */
.hsf-process-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 24px; }
.hsf-process-step { text-align: center; padding: 20px 10px; background: #fff; border: 1px solid #e5e6eb; border-radius: 10px; cursor: pointer; transition: all 0.3s; }
.hsf-process-step img { width: 48px; height: 48px; margin-bottom: 8px; }
.hsf-process-step em { display: block; font-size: 14px; font-weight: 700; color: #165dff; margin-bottom: 4px; font-style: normal; }
.hsf-process-step span { font-size: 13px; color: #1d2129; font-weight: 500; }
.hsf-process-step.active { border-color: #165dff; background: #e8f3ff; }
.hsf-process-panel { text-align: center; padding: 20px; background: #fff; border: 1px solid #e5e6eb; border-radius: 10px; }
.hsf-process-panel h3 { font-size: 16px; font-weight: 600; color: #1d2129; margin-bottom: 6px; }
.hsf-process-panel p { font-size: 13px; color: #86909c; }

/* ========== Security ========== */
.hsf-security-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.hsf-security-card { padding: 28px 24px; background: #fff; border: 1px solid #e5e6eb; border-radius: 12px; text-align: center; transition: all 0.3s; }
.hsf-security-card:hover { border-color: #165dff; box-shadow: 0 4px 16px rgba(22,93,255,0.1); }
.hsf-security-card img { width: 86px; height: 86px; margin-bottom: 14px; }
.hsf-security-card h3 { font-size: 16px; font-weight: 600; margin-bottom: 8px; color: #1d2129; }
.hsf-security-card p { font-size: 13px; color: #86909c; line-height: 1.7; }

/* ========== Data ========== */
.hsf-data-section { background: #f7f8fa; }
.hsf-data-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; }
.hsf-data-copy .hsf-section-heading { text-align: left; margin-bottom: 0; }
.hsf-data-copy .hsf-section-desc { margin: 10px 0 0 0; }
.hsf-stats-panel { background: #fff; border-radius: 12px; padding: 12px; border: 1px solid #e5e6eb; }
.hsf-stats-panel article { padding: 20px; text-align: center; border-bottom: 1px solid #f2f3f5; }
.hsf-stats-panel article:last-child { border-bottom: none; }
.hsf-stats-panel strong { display: block; font-size: 36px; font-weight: 700; color: #165dff; margin-bottom: 4px; }
.hsf-stats-panel span { font-size: 14px; color: #86909c; }

/* ========== FAQ ========== */
.hsf-faq-layout { display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px; align-items: start; }
.hsf-company-copy { margin-top: 20px; padding: 20px; background: #f7f8fa; border-radius: 10px; }
.hsf-company-copy h3 { font-size: 16px; font-weight: 600; color: #1d2129; margin-bottom: 10px; }
.hsf-company-copy p { font-size: 13px; color: #86909c; line-height: 1.8; }
.hsf-faq-list { display: flex; flex-direction: column; gap: 10px; }
.hsf-faq-item { background: #fff; border: 1px solid #e5e6eb; border-radius: 10px; overflow: hidden; }
.hsf-faq-item button { width: 100%; padding: 16px 20px; background: none; border: none; text-align: left; font-size: 14px; font-weight: 600; color: #1d2129; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
.hsf-faq-item button::after { content: '+'; font-size: 20px; color: #86909c; font-weight: 300; }
.hsf-faq-item.is-open button::after { content: '-'; }
.hsf-faq-body { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; padding: 0 20px; }
.hsf-faq-item.is-open .hsf-faq-body { max-height: 200px; padding-bottom: 16px; }
.hsf-faq-body p { font-size: 13px; color: #86909c; line-height: 1.7; }

/* ========== Bottom CTA ========== */
.hsf-bottom-cta { background: linear-gradient(135deg, #165dff, #4080ff); padding: 50px 0; text-align: center; color: #fff; }
.hsf-bottom-cta h2 { font-size: 28px; font-weight: 700; margin-bottom: 12px; }
.hsf-bottom-cta p { font-size: 15px; opacity: 0.9; margin-bottom: 24px; }
.hsf-bottom-cta .hsf-btn-solid { background: #fff; color: #165dff; padding: 12px 36px; font-size: 15px; font-weight: 600; }

@media (max-width: 768px) {
    .hsf-hero-inner, .hsf-cockpit-layout, .hsf-data-layout, .hsf-faq-layout { grid-template-columns: 1fr; }
    .hsf-hero h1 { font-size: 28px; }
    .hsf-section-title { font-size: 22px; }
    .hsf-scenario-grid, .hsf-security-grid { grid-template-columns: 1fr 1fr; }
    .hsf-capability-grid { grid-template-columns: 1fr; }
    .hsf-process-grid { grid-template-columns: repeat(3, 1fr); }
    .hsf-notice-card { flex-direction: column; gap: 16px; }
}

.x-nav-btn {
    display: inline-flex;
    align-items: center;
    padding: 6px 16px;
    border-radius: 6px;
    font-size: 13px;
    text-decoration: none;
    margin-left: 8px;
    transition: all 0.2s;
    white-space: nowrap;
}
.x-nav-btn-outline {
    background: #fff;
    color: #4e5969;
    border: 1px solid #e5e6eb;
}
.x-nav-btn-outline:hover {
    border-color: #165dff;
    color: #165dff;
}
.x-nav-btn-primary {
    background: linear-gradient(135deg, #165dff, #4080ff);
    color: #fff;
    border: 1px solid #165dff;
}
.x-nav-btn-primary:hover {
    opacity: 0.9;
    color: #fff;
}
.x-logo-img {
    height: 32px;
    width: auto;
}

/* ========== 右侧悬浮按钮 ========== */
.hsf-float-buttons {
    position: fixed;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 999;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.hsf-float-btn {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    background: #fff;
    border: 1px solid #e5e6eb;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #4e5969;
    font-size: 11px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.2s;
    cursor: pointer;
}
.hsf-float-btn i {
    font-size: 18px;
    margin-bottom: 2px;
}
.hsf-float-btn:hover {
    border-color: #165dff;
    color: #165dff;
    transform: translateY(-2px);
}
@media (max-width: 768px) {
    .hsf-float-buttons { display: none; }
}
</style>

<!-- Hero -->
<section class="hsf-hero">
    <div class="hsf-container">
        <div class="hsf-hero-inner">
            <div>
                <div class="hsf-eyebrow">企业级自动发卡云服务平台</div>
                <h1>彩虹云商城，让发卡生意稳定增长</h1>
                <p class="hsf-hero-desc">为卡密、软件授权、充值权益、会员服务、资料商品等虚拟商品团队提供自动发货、订单查询、资金结算与商户运营一体化能力。</p>
                <div class="hsf-hero-tags">
                    <span class="hsf-tag-item">自动发货</span>
                    <span class="hsf-tag-item">支付回调</span>
                    <span class="hsf-tag-item">卡密库存</span>
                    <span class="hsf-tag-item">商户结算</span>
                </div>
                <div class="hsf-hero-buttons">
                    <a href="./user/reg.php" class="hsf-btn hsf-btn-solid">开通小店</a>
                    <a href="#hsf-capabilities" class="hsf-btn hsf-btn-ghost">查看平台能力</a>
                    <a href="#xShop" onclick="xTab('search')" class="hsf-btn hsf-btn-query">订单查询</a>
                </div>
            </div>
            <div class="hsf-hero-visual">
                <a href="<?php echo !empty($conf["appurl"]) ? $conf["appurl"] : "#" ?>" target="_blank"><img src="assets/plugin/hsmoban/images/caihong-app-download.png" alt="彩虹云商城商家APP下载"></a>
            </div>
        </div>
    </div>
</section>

<!-- 系统公告 -->
<section class="hsf-container">
    <div class="hsf-notice-card" id="hsf-notice">
        <div class="hsf-notice-title">
            <strong>系统公告</strong>
            <span>最新平台公告</span>
        </div>
        <div class="hsf-notice-list">
            <article>
                <i></i>
                <div>
                    <h3><a href="#">彩虹云商城业务模式整改说明</a></h3>
                    <p>为有效防范商户失联、消费者权益受损及投诉率偏高的问题，切实规范平台经营行为，强化风险防控能力，保障交易安全。</p>
                </div>
            </article>
            <article>
                <i></i>
                <div>
                    <h3><a href="#">保证金缴纳与退还规则说明</a></h3>
                    <p>为保障交易安全、提升消费者体验并维护平台经营秩序，根据《平台商家服务协议》及《商品信息发布规范》执行。</p>
                </div>
            </article>
            <article>
                <i></i>
                <div>
                    <h3><a href="#">结算规则与支付渠道说明</a></h3>
                    <p>平台提供多种支付通道，当天交易订单将在24小时后自动签收，结算账单清晰可查。</p>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- 业务场景 -->
<section class="hsf-section" id="hsf-scenarios">
    <div class="hsf-container">
        <div class="hsf-section-heading">
            <div class="hsf-section-eyebrow">BUSINESS SCENE</div>
            <h2 class="hsf-section-title">覆盖主流虚拟商品交易场景</h2>
            <p class="hsf-section-desc">从商品上架到自动交付，从买家查询到商户结算，帮助团队搭建更专业的发卡经营入口。</p>
        </div>
        <div class="hsf-scenario-grid">
            <div class="hsf-scenario-card">
                <div class="hsf-scenario-num">01</div>
                <img src="assets/plugin/hsmoban/images/icon-launch.png" alt="">
                <h3>卡密自动发货</h3>
                <p>适配兑换码、授权码、激活码、账号凭证等库存型虚拟商品。</p>
            </div>
            <div class="hsf-scenario-card">
                <div class="hsf-scenario-num">02</div>
                <img src="assets/plugin/hsmoban/images/icon-dashboard.png" alt="">
                <h3>会员权益售卖</h3>
                <p>面向会员服务、课程资料、权益礼包等即时交付商品。</p>
            </div>
            <div class="hsf-scenario-card">
                <div class="hsf-scenario-num">03</div>
                <img src="assets/plugin/hsmoban/images/icon-security.png" alt="">
                <h3>知识付费</h3>
                <p>支持授权码库存、订单凭证、售后备注和续费记录。</p>
            </div>
            <div class="hsf-scenario-card">
                <div class="hsf-scenario-num">04</div>
                <img src="assets/plugin/hsmoban/images/icon-modules.png" alt="">
                <h3>资源下载</h3>
                <p>适合团队统一管理商品、订单、支付、结算和商户服务。</p>
            </div>
        </div>
    </div>
</section>

<!-- 核心能力 -->
<section class="hsf-section" id="hsf-capabilities" style="background:#f7f8fa;">
    <div class="hsf-container">
        <div class="hsf-section-heading">
            <div class="hsf-section-eyebrow">CORE CAPABILITY</div>
            <h2 class="hsf-section-title">把发卡网该有的核心能力一次配齐</h2>
            <p class="hsf-section-desc">不止是一个展示首页，而是一套围绕转化、信任和运营效率设计的企业发卡网门面。</p>
        </div>
        <div class="hsf-capability-grid">
            <div class="hsf-cap-card">
                <img src="assets/plugin/hsmoban/images/icon-launch.png" alt="">
                <h3>极速开店上线</h3>
                <p>商品、库存、支付、订单流程清晰，适合团队快速搭建发卡业务。</p>
            </div>
            <div class="hsf-cap-card">
                <img src="assets/plugin/hsmoban/images/icon-modules.png" alt="">
                <h3>模块化系统能力</h3>
                <p>覆盖卡密导入、库存预警、订单追踪、售后凭证与商户分账。</p>
            </div>
            <div class="hsf-cap-card">
                <img src="assets/plugin/hsmoban/images/icon-dashboard.png" alt="">
                <h3>移动端商户后台</h3>
                <p>商户可随时查看订单、商品、库存和账单，处理经营事务更高效。</p>
            </div>
            <div class="hsf-cap-card">
                <img src="assets/plugin/hsmoban/images/icon-security.png" alt="">
                <h3>支付风控闭环</h3>
                <p>支付回调、订单状态和风险记录同步留存，异常订单更好核对。</p>
            </div>
            <div class="hsf-cap-card">
                <img src="assets/plugin/hsmoban/images/icon-settlement.png" alt="">
                <h3>资金结算透明</h3>
                <p>订单金额、手续费、结算状态集中展示，方便财务核对。</p>
            </div>
            <div class="hsf-cap-card">
                <img src="assets/plugin/hsmoban/images/icon-support.png" alt="">
                <h3>服务支持体系</h3>
                <p>围绕开通、部署、售后、升级提供持续服务支持。</p>
            </div>
        </div>
    </div>
</section>

<!-- 商户经营驾驶舱 -->
<section class="hsf-cockpit">
    <div class="hsf-container">
        <div class="hsf-cockpit-layout">
            <div class="hsf-cockpit-copy">
                <div class="hsf-section-eyebrow">MERCHANT COCKPIT</div>
                <h2>商户经营驾驶舱，把订单和资金看清楚</h2>
                <p class="hsf-cockpit-desc">用更直观的页面表达平台实力：订单趋势、库存消耗、支付状态、结算概况和售后记录清晰呈现。</p>
                <ul>
                    <li>订单趋势实时追踪</li>
                    <li>库存消耗自动预警</li>
                    <li>结算账单清晰归档</li>
                </ul>
                <a href="./user/reg.php" class="hsf-btn hsf-btn-solid">开通小店</a>
            </div>
            <div class="hsf-cockpit-art">
                <img src="assets/plugin/hsmoban/images/dashboard-cockpit-v3.png" alt="彩虹云商城商户经营驾驶舱">
            </div>
        </div>
    </div>
</section>

<!-- 入驻流程 -->
<section class="hsf-section" id="hsf-process">
    <div class="hsf-container">
        <div class="hsf-section-heading">
            <div class="hsf-section-eyebrow">ONBOARDING</div>
            <h2 class="hsf-section-title">6步完成发卡业务入驻</h2>
            <p class="hsf-section-desc">从商户注册到自动发货，帮助团队快速把虚拟商品交易跑起来。</p>
        </div>
        <div class="hsf-process-grid" id="hsfProcessSteps">
            <div class="hsf-process-step active" data-step="0"><img src="assets/plugin/hsmoban/images/icon-dashboard.png" alt=""><em>01</em><span>注册账户</span></div>
            <div class="hsf-process-step" data-step="1"><img src="assets/plugin/hsmoban/images/icon-security.png" alt=""><em>02</em><span>绑定收款</span></div>
            <div class="hsf-process-step" data-step="2"><img src="assets/plugin/hsmoban/images/icon-modules.png" alt=""><em>03</em><span>发布商品</span></div>
            <div class="hsf-process-step" data-step="3"><img src="assets/plugin/hsmoban/images/icon-launch.png" alt=""><em>04</em><span>买家下单</span></div>
            <div class="hsf-process-step" data-step="4"><img src="assets/plugin/hsmoban/images/icon-support.png" alt=""><em>05</em><span>自动发货</span></div>
            <div class="hsf-process-step" data-step="5"><img src="assets/plugin/hsmoban/images/icon-settlement.png" alt=""><em>06</em><span>资金结算</span></div>
        </div>
        <div class="hsf-process-panel" id="hsfProcessPanel">
            <h3>注册账户</h3>
            <p>提交基础资料，创建商户账号。</p>
        </div>
    </div>
</section>

<!-- 信任系统 -->
<section class="hsf-section" style="background:#f7f8fa;">
    <div class="hsf-container">
        <div class="hsf-section-heading">
            <div class="hsf-section-eyebrow">TRUST SYSTEM</div>
            <h2 class="hsf-section-title">企业级发卡网，信任感必须放在第一屏之后</h2>
            <p class="hsf-section-desc">从支付、数据、售后、资质到服务响应，强化买家和商户对平台的信任。</p>
        </div>
        <div class="hsf-security-grid">
            <div class="hsf-security-card">
                <img src="assets/plugin/hsmoban/images/icon-security.png" alt="">
                <h3>支付风控</h3>
                <p>支付回调与订单状态同步记录，异常交易更易追踪。</p>
            </div>
            <div class="hsf-security-card">
                <img src="assets/plugin/hsmoban/images/icon-settlement.png" alt="">
                <h3>资金结算</h3>
                <p>商户账单、结算状态、订单金额清楚展示，减少对账压力。</p>
            </div>
            <div class="hsf-security-card">
                <img src="assets/plugin/hsmoban/images/icon-support.png" alt="">
                <h3>售后响应</h3>
                <p>围绕订单查询、发货凭证和售后备注建立服务链路。</p>
            </div>
            <div class="hsf-security-card">
                <img src="assets/plugin/hsmoban/images/icon-modules.png" alt="">
                <h3>系统扩展</h3>
                <p>可围绕更多商品类型、商户角色和经营流程持续扩展。</p>
            </div>
        </div>
    </div>
</section>

<!-- 数据证明 -->
<section class="hsf-section hsf-data-section">
    <div class="hsf-container">
        <div class="hsf-data-layout">
            <div class="hsf-data-copy">
                <div class="hsf-section-heading">
                    <div class="hsf-section-eyebrow">DATA PROOF</div>
                    <h2 class="hsf-section-title">专注企业级发卡网服务，沉淀长期经营能力</h2>
                    <p class="hsf-section-desc">用清晰的数据与服务能力展示平台可信度，帮助首页形成更强转化。</p>
                </div>
            </div>
            <div class="hsf-stats-panel">
                <article><strong>4年</strong><span>年稳健运营</span></article>
                <article><strong>5,002,879张</strong><span>成功发卡</span></article>
                <article><strong>36,820户</strong><span>服务终端用户</span></article>
                <article><strong>24h</strong><span>小时售后响应</span></article>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="hsf-section" id="hsf-faq">
    <div class="hsf-container">
        <div class="hsf-faq-layout">
            <div class="hsf-data-copy">
                <div class="hsf-section-heading" style="text-align:left;margin-bottom:0;">
                    <div class="hsf-section-eyebrow">FAQ</div>
                    <h2 class="hsf-section-title">商户常见问题</h2>
                    <p class="hsf-section-desc" style="margin:10px 0 0 0;">围绕开店、商品、支付、发货、售后和结算整理高频问题。</p>
                </div>
                <div class="hsf-company-copy">
                    <h3>关于彩虹云商城</h3>
                    <p>彩虹云商城长期服务发卡网、卡密商城与虚拟商品经营团队，围绕商品上架、库存管理、支付回调、订单查询、自动发货、风控对账和售后凭证持续优化。模板首页强化PC端转化、移动端适配、公告读取、品牌信任和经营数据表达，适合打造更专业的企业级发卡网门面。</p>
                </div>
            </div>
            <div class="hsf-faq-list" id="hsfFaqList">
                <div class="hsf-faq-item is-open">
                    <button type="button"><span>彩虹云商城适合哪些商品？</span></button>
                    <div class="hsf-faq-body"><p>适合卡密、软件授权、充值券、会员权益、资料包、账号凭证等虚拟商品自动发货。</p></div>
                </div>
                <div class="hsf-faq-item">
                    <button type="button"><span>是否支持自动发货？</span></button>
                    <div class="hsf-faq-body"><p>支持。商户导入卡密库存后，用户付款成功即可自动出库并展示发货结果。</p></div>
                </div>
                <div class="hsf-faq-item">
                    <button type="button"><span>订单和售后能否追踪？</span></button>
                    <div class="hsf-faq-body"><p>订单状态、付款记录、发货凭证和售后备注可统一留存，方便客服核对。</p></div>
                </div>
                <div class="hsf-faq-item">
                    <button type="button"><span>手机端可以管理商户后台吗？</span></button>
                    <div class="hsf-faq-body"><p>可以。商户可在移动端查看订单、商品、库存和账单，适合团队随时处理业务。</p></div>
                </div>
                <div class="hsf-faq-item">
                    <button type="button"><span>能否作为企业官网展示？</span></button>
                    <div class="hsf-faq-body"><p>模板已适配企业级官网展示，首屏、优势、公告、流程、保障和页脚内容均可维护。</p></div>
                </div>
                <div class="hsf-faq-item">
                    <button type="button"><span>资金结算是否清晰？</span></button>
                    <div class="hsf-faq-body"><p>平台提供订单金额、手续费、结算状态等数据展示，便于商户对账。</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 底部CTA -->
<section class="hsf-bottom-cta">
    <div class="hsf-container">
        <h2>让你的发卡网，从第一眼就显得更专业</h2>
        <p>用企业级首页承接商户入驻、买家信任、公告触达和长期经营。</p>
        <a href="./user/reg.php" class="hsf-btn hsf-btn-solid">开通小店</a>
    </div>
</section>


<!-- 右侧悬浮按钮 -->
<div class="hsf-float-buttons">
    <a href="./user/login.php" class="hsf-float-btn" title="登录">
        <i class="fa fa-user"></i>
        <span>登录</span>
    </a>
    <a href="./user/reg.php" class="hsf-float-btn" title="入驻">
        <i class="fa fa-user-plus"></i>
        <span>入驻</span>
    </a>
    <a href="javascript:showModal()" class="hsf-float-btn" title="总站号">
        <i class="fa fa-qq"></i>
        <span>总站号</span>
    </a>
</div>
<script>
// 流程步骤切换
(function(){
    var steps = document.querySelectorAll('.hsf-process-step');
    var panel = document.getElementById('hsfProcessPanel');
    var panelData = [
        {h:'注册账户', p:'提交基础资料，创建商户账号。'},
        {h:'绑定收款', p:'接入支付通道，配置结算信息。'},
        {h:'发布商品', p:'创建分类，导入卡密库存。'},
        {h:'买家下单', p:'买家付款后系统自动生成订单。'},
        {h:'自动发货', p:'系统即时出库并保留发货凭证。'},
        {h:'资金结算', p:'账单自动汇总，商户对账更清楚。'}
    ];
    steps.forEach(function(s){
        s.addEventListener('click', function(){
            steps.forEach(function(x){x.classList.remove('active');});
            s.classList.add('active');
            var i = parseInt(s.getAttribute('data-step'));
            if(panelData[i]){
                panel.innerHTML = '<h3>'+panelData[i].h+'</h3><p>'+panelData[i].p+'</p>';
            }
        });
    });
})();
// FAQ 折叠
(function(){
    var items = document.querySelectorAll('.hsf-faq-item');
    items.forEach(function(item){
        var btn = item.querySelector('button');
        btn.addEventListener('click', function(){
            item.classList.toggle('is-open');
        });
    });
})();
</script>