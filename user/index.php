<?php
require '../includes/common.php';
eval(base64_decode('aWYoIWRlZmluZWQoJ1NJVEVfQVVUSF9WRVJJRklFRCcpKXtodHRwX3Jlc3BvbnNlX2NvZGUoNDA0KTtoZWFkZXIoIkhUVFAvMS4xIDQwNCBOb3QgRm91bmQiKTtleGl0O30='));
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

if($_GET['mod']=='faka'){
exit("<script language='javascript'>window.location.href='../?mod=faka&&id={$_GET['id']}&skey={$_GET['skey']}';</script>");
}
$title = '平台首页';
include 'head.php';
?>
<link rel="stylesheet" href="<?php echo $cdnpublic?>toastr.js/latest/css/toastr.min.css">
<style>
/* ============================================================
   用户中心首页 · 玻璃高级感布局（对标目标站代理后台）
   左卡：头像 + 余额 + 充值/提现 + 用户名/UID/今日收益 + 九宫格
   右卡：我的站点信息
   ============================================================ */
.dashboard-container { padding: 0; }

/* 警告提示 */
.alert-warning {
    background: rgba(255,243,205,.85);
    border: 1px solid rgba(255,193,7,.45);
    color: #856404;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 20px;
}

/* 主面板（毛玻璃） */
.main-panel {
    background: rgba(255,255,255,.58);
    -webkit-backdrop-filter: saturate(180%) blur(18px);
    backdrop-filter: saturate(180%) blur(18px);
    border: 1px solid rgba(255,255,255,.65);
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(31,38,135,.10), 0 2px 8px rgba(31,38,135,.05);
    margin-bottom: 20px;
    overflow: hidden;
}
.panel-header {
    background: linear-gradient(135deg, rgba(255,255,255,.55), rgba(244,246,251,.32));
    padding: 14px 22px;
    border-bottom: 1px solid rgba(255,255,255,.5);
}
.panel-title {
    font-size: 16px;
    font-weight: 600;
    color: #1d1d1f;
    margin: 0;
    display: flex;
    align-items: center;
    letter-spacing: -.01em;
}
.panel-title i { margin-right: 10px; color: #0a84ff; }
.panel-body { padding: 22px; }

/* ===== 左卡：用户区 ===== */
.uc-top { display: flex; align-items: center; gap: 18px; }
.uc-avatar img {
    width: 84px; height: 84px; border-radius: 50%;
    border: 3px solid rgba(255,255,255,.9);
    box-shadow: 0 6px 18px rgba(31,38,135,.15);
}
.uc-balance { flex: 1; text-align: right; }
.uc-balance-line { font-size: 15px; color: #6e6e73; }
.uc-balance-line b {
    font-size: 30px; font-weight: 800; color: #1d1d1f;
    letter-spacing: -.02em; margin: 0 4px;
}
.uc-balance-btns { margin-top: 10px; display: flex; gap: 10px; justify-content: flex-end; }
.uc-pill {
    border: none; border-radius: 999px; padding: 9px 22px;
    font-size: 14px; font-weight: 600; color: #fff !important;
    text-decoration: none !important; transition: all .2s;
    display: inline-flex; align-items: center; gap: 6px;
}
.uc-pill:hover { transform: translateY(-2px); }
.uc-pill-green { background: linear-gradient(135deg,#34c759,#28a745); box-shadow: 0 6px 16px rgba(52,199,89,.30); }
.uc-pill-blue { background: linear-gradient(135deg,#0a84ff,#0066d6); box-shadow: 0 6px 16px rgba(10,132,255,.30); }

/* 三列统计：用户名 / UID / 今日收益 */
.uc-stats {
    display: flex; margin-top: 20px; padding: 14px 0;
    border-top: 1px solid rgba(255,255,255,.6);
    border-bottom: 1px solid rgba(255,255,255,.6);
    background: rgba(255,255,255,.28);
    border-radius: 12px;
}
.uc-stat { flex: 1; text-align: center; position: relative; }
.uc-stat + .uc-stat::before {
    content: ''; position: absolute; left: 0; top: 12%;
    height: 76%; width: 1px; background: rgba(0,0,0,.06);
}
.uc-stat-label { font-size: 13px; color: #6e6e73; margin-bottom: 4px; }
.uc-stat-value { font-size: 17px; font-weight: 700; color: #1d1d1f; }

/* 九宫格功能按钮 */
.uc-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 12px; margin-top: 18px; }
.uc-cell {
    position: relative;
    display: flex; align-items: center; justify-content: center;
    min-height: 52px; padding: 14px 8px;
    border-radius: 14px;
    background: rgba(255,255,255,.72);
    border: 1px solid rgba(255,255,255,.8);
    box-shadow: 0 3px 12px rgba(31,38,135,.06);
    color: #1d1d1f !important; font-size: 14.5px; font-weight: 600;
    text-decoration: none !important; transition: all .22s;
    letter-spacing: .02em; text-align: center;
}
.uc-cell:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(31,38,135,.12);
    background: rgba(255,255,255,.92);
    color: #0a84ff !important;
}
.uc-cell.blue {
    background: linear-gradient(135deg,#0a84ff,#2f9bff);
    color: #fff !important; border: none;
    box-shadow: 0 6px 18px rgba(10,132,255,.35);
}
.uc-cell.blue:hover { color: #fff !important; box-shadow: 0 10px 26px rgba(10,132,255,.45); }
.uc-cell.green {
    background: linear-gradient(135deg,#e3f9ee,#d4f4e3);
    color: #14804a !important; border-color: rgba(52,199,89,.25);
}
.uc-cell.green:hover { color: #0e5a23 !important; }
.uc-cell.yellow {
    background: linear-gradient(135deg,#fdf3dc,#fceccb);
    color: #9a6a00 !important; border-color: rgba(255,159,10,.25);
}
.uc-cell.yellow:hover { color: #7a4500 !important; }
.uc-cell.danger:hover { color: #ff3b30 !important; }
.uc-badge {
    position: absolute; top: 6px; right: 10px;
    background: #ff3b30; color: #fff;
    font-size: 11px; font-weight: 700;
    padding: 1px 7px; border-radius: 10px;
    min-width: 18px; text-align: center; display: none;
}

/* ===== 右卡：站点信息列表 ===== */
.site-info-list { border-radius: 14px; overflow: hidden; }
.site-info-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: 13px 4px;
    border-bottom: 1px solid rgba(255,255,255,.55);
}
.site-info-item:last-child { border-bottom: none; }
.site-info-item.warning {
    background: rgba(255,243,205,.6);
    margin: 0 -22px; padding: 14px 22px;
}
.site-info-label { font-weight: 500; color: #6e6e73; font-size: 14px; }
.site-info-value { display: flex; align-items: center; font-size: 14px; color: #1d1d1f; }
.domain-link, .fanghong-link { color: #0a84ff; text-decoration: none; font-weight: 500; }
.domain-link:hover, .fanghong-link:hover { text-decoration: underline; }
.site-status.normal { color: #28a745; font-weight: 600; }
.site-status.expired { color: #ff3b30; font-weight: 600; }
.no-site-message { text-align: center; padding: 20px; color: #6e6e73; }

/* 销售统计（保留原功能） */
.sales-stats-section { margin-top: 4px; }
.sales-stats-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
.sales-stats-title { font-size: 15px; font-weight: 600; color: #1d1d1f; display: flex; align-items: center; gap: 8px; }
.sales-stats-title i { color: #0a84ff; }
.sales-stats-tabs { display: flex; gap: 8px; }
.sales-tab {
    padding: 6px 16px; border-radius: 999px;
    border: 1px solid rgba(0,0,0,.08);
    background: rgba(255,255,255,.6);
    color: #6e6e73; cursor: pointer; transition: all .18s; font-size: 13px;
}
.sales-tab:hover { border-color: #0a84ff; color: #0a84ff; }
.sales-tab.active { background: #0a84ff; color: #fff; border-color: #0a84ff; }
.sales-chart-container {
    background: rgba(255,255,255,.45);
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,.55);
    padding: 18px;
}
.sales-chart-title { font-size: 13px; font-weight: 600; color: #6e6e73; margin-bottom: 12px; }
.sales-chart { width: 100%; height: 280px; position: relative; }
.chart-loading {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    color: #6e6e73; font-size: 14px;
    display: flex; align-items: center; gap: 8px;
}
.chart-loading i { color: #0a84ff; }

/* 客服按钮 */
.service-button { margin-top: 16px; border-radius: 14px; padding: 12px; font-weight: 600; }

/* 公告 */
.announcement-content { padding: 4px 2px; color: #6e6e73; line-height: 1.6; }

/* 响应式 */
@media (max-width: 767px) {
    .panel-body { padding: 16px; }
    .uc-avatar img { width: 64px; height: 64px; }
    .uc-balance-line b { font-size: 24px; }
    .uc-pill { padding: 8px 14px; font-size: 13px; }
    .uc-grid { gap: 8px; }
    .uc-cell { min-height: 46px; font-size: 13px; padding: 10px 4px; border-radius: 11px; }
    .uc-stat-value { font-size: 15px; }
    .site-info-item { flex-direction: column; align-items: flex-start; gap: 6px; }
    .sales-chart { height: 220px; }
    .sales-stats-header { flex-direction: column; gap: 10px; align-items: flex-start; }
}
</style>

<div class="mc-dashboard">
    <!-- 安全提示 -->
    <?php if($userrow['rmb']>4): ?>
        <?php if(strlen($userrow['pwd'])<6 || (is_numeric($userrow['pwd']) && strlen($userrow['pwd'])<=10) || $userrow['pwd']==$userrow['qq']): ?>
            <div class="alert alert-warning" style="border-radius:10px;">
                <i class="fa fa-exclamation-triangle"></i> 你的密码过于简单，请不要使用较短的纯数字或自己的QQ号当做密码，以免造成资金损失！
                <a href="uset.php?mod=user" class="btn btn-primary btn-xs" style="margin-left:10px;">点此修改密码</a>
            </div>
        <?php elseif($userrow['user']==$userrow['pwd']): ?>
            <div class="alert alert-warning" style="border-radius:10px;">
                <i class="fa fa-exclamation-triangle"></i> 你的用户名与密码相同，极易被黑客破解，请及时修改密码
                <a href="uset.php?mod=user" class="btn btn-primary btn-xs" style="margin-left:10px;">点此修改密码</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- 欢迎卡（白色卡片+蓝色装饰条，对标参考站） -->
    <div class="mc-welcome-card">
        <div class="mc-welcome-banner">
            <div class="mc-welcome-banner-text">欢迎回来</div>
            <svg viewBox="0 0 300 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMaxYMax meet">
                <ellipse cx="220" cy="112" rx="70" ry="6" fill="#a5c3f8" opacity=".4"/>
                <rect x="180" y="50" width="70" height="52" rx="5" fill="#fff" opacity=".85"/>
                <rect x="187" y="57" width="56" height="30" rx="3" fill="#d4e4fd"/>
                <rect x="208" y="100" width="14" height="12" fill="#8fb0f5"/>
                <circle cx="255" cy="38" r="15" fill="#fde68a" opacity=".7"/>
                <circle cx="160" cy="30" r="8" fill="#bfdbfe" opacity=".5"/>
                <path d="M150 80 Q160 50 175 55 Q185 58 180 80 Z" fill="#93c5fd" opacity=".5"/>
            </svg>
        </div>
        <div class="mc-welcome-body">
            <div class="mc-welcome-avatar-row">
                <img src="<?php echo $faceimg ?>" class="mc-welcome-avatar" alt="头像">
                <div class="mc-welcome-user">
                    <div class="mc-welcome-greet"><?php echo $nickname ?></div>
                    <div class="mc-welcome-qq"><?php echo !empty($userrow['qq'])?$userrow['qq']:'未绑定QQ' ?></div>
                </div>
            </div>
            <div class="mc-welcome-stats">
                <div class="mc-ws-item">
                    <div class="mc-ws-val"><span id="mc_goods_count">0</span>个</div>
                    <div class="mc-ws-lbl">商品</div>
                </div>
                <div class="mc-ws-item">
                    <div class="mc-ws-val"><?php echo $userrow['rmb'] ?></div>
                    <div class="mc-ws-lbl">账户余额</div>
                </div>
                <div class="mc-ws-item">
                    <div class="mc-ws-val"><span id="mc_pending">0.00</span></div>
                    <div class="mc-ws-lbl">未结余额</div>
                </div>
            </div>
            <div class="mc-welcome-actions">
                <a href="tixian.php" class="mc-btn mc-btn-primary"><i class="fa fa-money"></i> 去结算</a>
                <a href="uset.php?mod=skimg" class="mc-btn mc-btn-primary" style="background:#fff;color:var(--mc-primary) !important;border:1px solid var(--mc-primary);"><i class="fa fa-credit-card"></i> 自定义支付渠道</a>
            </div>
        </div>
    </div>

    <!-- 6 个统计卡（对标参考站） -->
    <div class="mc-stat-grid">
        <div class="mc-stat-card">
            <div class="mc-stat-icon blue"><i class="fa fa-shopping-bag"></i></div>
            <div>
                <div class="mc-stat-label">今日订单</div>
                <div class="mc-stat-value"><span id="mc_today_orders">0</span> 笔</div>
            </div>
        </div>
        <div class="mc-stat-card">
            <div class="mc-stat-icon blue"><i class="fa fa-rmb"></i></div>
            <div>
                <div class="mc-stat-label">今日流水</div>
                <div class="mc-stat-value">￥<span id="mc_today_income">0.00</span></div>
            </div>
        </div>
        <div class="mc-stat-card">
            <div class="mc-stat-icon blue"><i class="fa fa-line-chart"></i></div>
            <div>
                <div class="mc-stat-label">今日利润</div>
                <div class="mc-stat-value">￥<span id="mc_today_profit">0.00</span></div>
            </div>
        </div>
        <div class="mc-stat-card">
            <div class="mc-stat-icon blue"><i class="fa fa-clock-o"></i></div>
            <div>
                <div class="mc-stat-label">待结算</div>
                <div class="mc-stat-value">￥<span id="mc_pending2">0.00</span></div>
            </div>
        </div>
        <div class="mc-stat-card">
            <div class="mc-stat-icon blue"><i class="fa fa-calendar-o"></i></div>
            <div>
                <div class="mc-stat-label">昨日流水</div>
                <div class="mc-stat-value">￥<span id="mc_yest_income">0.00</span></div>
            </div>
        </div>
        <div class="mc-stat-card">
            <div class="mc-stat-icon blue"><i class="fa fa-area-chart"></i></div>
            <div>
                <div class="mc-stat-label">昨日利润</div>
                <div class="mc-stat-value">￥<span id="mc_yest_profit">0.00</span></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 左：生意罗盘 + 交易统计 + 最新订单 -->
        <div class="col-md-8">
            <div class="row">
                <!-- 生意罗盘 -->
                <div class="col-md-4">
                    <div class="mc-card">
                        <div class="mc-card-head"><h3><i class="fa fa-compass"></i> 生意罗盘</h3></div>
                        <div class="mc-card-body mc-traffic">
                            <div class="mc-traffic-num"><span id="mc_uv2">0</span></div>
                            <div class="mc-stat-label">今日店铺访问量(UV)</div>
                            <div class="mc-traffic-change">0% <i class="fa fa-arrow-up"></i> 与昨日同比增长</div>
                            <div class="mc-traffic-circle"><span id="mc_pv">0</span></div>
                            <div class="mc-stat-label" style="margin-top:8px;">今日访问量 PV</div>
                        </div>
                    </div>
                </div>
                <!-- 交易统计 -->
                <div class="col-md-8">
                    <div class="mc-card">
                        <div class="mc-card-head">
                            <h3><i class="fa fa-bar-chart"></i> 交易统计</h3>
                            <div class="mc-chart-tabs">
                                <div class="mc-chart-tab active" data-range="7">七日统计</div>
                                <div class="mc-chart-tab" data-range="30">月度统计</div>
                            </div>
                        </div>
                        <div class="mc-card-body">
                            <div id="sales_trend_chart" style="width:100%;height:260px;">
                                <div style="text-align:center;color:#9ca3af;padding-top:100px;"><i class="fa fa-spinner fa-spin"></i> 加载中...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 最新订单 -->
            <div class="mc-card">
                <div class="mc-card-head">
                    <h3><i class="fa fa-list"></i> 最新订单</h3>
                    <a href="./list.php" class="mc-btn mc-btn-outline mc-btn-sm">查看更多 <i class="fa fa-angle-right"></i></a>
                </div>
                <div class="mc-card-body" style="padding:0;">
                    <table class="mc-table">
                        <thead>
                            <tr><th>订单号</th><th>商品名称</th><th>交易金额</th><th>下单时间</th><th>状态</th><th>操作</th></tr>
                        </thead>
                        <tbody id="mc_latest_orders">
                            <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:30px;"><i class="fa fa-spinner fa-spin"></i> 加载中...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 右：系统公告 -->
        <div class="col-md-4">
            <div class="mc-card">
                <div class="mc-card-head"><h3><i class="fa fa-bullhorn"></i> 系统公告</h3></div>
                <div class="mc-card-body">
                    <ul class="mc-notice-list">
                        <li>
                            <div>🎉 自动发卡平台 · 平台结算时间查看详情</div>
                            <div class="mc-notice-date">06/25</div>
                        </li>
                        <li>
                            <div>💡 最亲爱的站长祝愿：祝各位站长幸福安康，快乐美满，好事成双，生意兴隆</div>
                            <div class="mc-notice-date">06/25</div>
                        </li>
                        <?php if($conf['gg_panel']){ echo '<li><div>'.$conf['gg_panel'].'</div><div class="mc-notice-date">系统</div></li>'; } ?>
                    </ul>
                </div>
            </div>

            <?php if($userrow['power']>0): ?>
            <div class="mc-card">
                <div class="mc-card-head"><h3><i class="fa fa-desktop"></i> 我的站点</h3></div>
                <div class="mc-card-body">
                    <div class="site-info-list">
                        <div class="site-info-item" style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;">
                            <span class="text-muted">网站名称</span><span><?php echo $userrow['sitename'] ?></span>
                        </div>
                        <div class="site-info-item" style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;">
                            <span class="text-muted">代理类型</span>
                            <span style="color:<?php echo $userrow['power']==2?'#ef4444':'#f59e0b' ?>;font-weight:600;"><?php echo $userrow['power']==2?'专业版':'普及版' ?></span>
                        </div>
                        <div class="site-info-item" style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;">
                            <span class="text-muted">主域名</span>
                            <a href="http://<?php echo $userrow['domain'] ?>" target="_blank" style="color:#3b82f6;"><?php echo $userrow['domain'] ?></a>
                        </div>
                        <?php if($conf['fenzhan_expiry']>0): ?>
                        <div class="site-info-item" style="display:flex;justify-content:space-between;padding:8px 0;">
                            <span class="text-muted">到期时间</span><span><?php echo $userrow['endtime'] ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <a href="https://qm.qq.com/q/C25ChPwNuE" target="_blank" class="mc-btn mc-btn-primary" style="width:100%;justify-content:center;margin-bottom:16px;">
                <i class="fa fa-comments"></i> 站长专属客服
            </a>
        </div>
    </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="<?php echo $cdnpublic?>clipboard.js/1.7.1/clipboard.min.js"></script>
<script src="<?php echo $cdnpublic?>toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
$(document).ready(function(){
    // 获取消息通知
    $.ajax({
        type: "GET",
        url: "ajax.php?act=msg",
        dataType: 'json',
        async: true,
        success: function(data) {
            if(data.code == 0){
                if(data.count > 0){
                    $("#mc_msg_dot").show();
                    toastr.info('<a href="message.php">您有<b>'+data.count+'</b>条新消息，请注意查收！</a>', '消息提醒');
                }
                if(data.count2 > 0){
                    toastr.warning('<a href="workorder.php">您有<b>'+data.count2+'</b>个工单已被管理员回复！</a>', '工单提醒');
                }
            }
        }
    });
});

// 子菜单切换
function toggleSubmenu(el) {
    var submenu = $(el).next('.nav-sub');
    var icon = $(el).find('.pull-right i');

    if (submenu.hasClass('open')) {
        submenu.removeClass('open');
        icon.removeClass('fa-angle-down').addClass('fa-angle-right');
    } else {
        $('.nav-sub.open').removeClass('open');
        $('.nav-sub').prev('.auto').find('.pull-right i').removeClass('fa-angle-down').addClass('fa-angle-right');
        submenu.addClass('open');
        icon.removeClass('fa-angle-right').addClass('fa-angle-down');
    }
}

// 销售统计图表初始化
var salesTrendChart = null;

// 检测是否为移动设备
function isMobile() {
    return window.innerWidth <= 767;
}

// 初始化图表
function initSalesCharts() {
    if(typeof echarts === 'undefined') return;

    // 销售趋势图
    var trendDom = document.getElementById('sales_trend_chart');
    if(trendDom) {
        salesTrendChart = echarts.init(trendDom);

        var mobile = isMobile();

        salesTrendChart.setOption({
            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'cross' },
                textStyle: { fontSize: mobile ? 10 : 12 }
            },
            legend: {
                data: ['订单量', '流水金额', '利润金额', '访问量'],
                top: 0,
                textStyle: { fontSize: mobile ? 9 : 11 },
                itemWidth: mobile ? 12 : 16,
                itemHeight: mobile ? 8 : 10,
                itemGap: mobile ? 6 : 12
            },
            grid: {
                left: mobile ? '5%' : '3%',
                right: mobile ? '6%' : '4%',
                bottom: mobile ? '8%' : '3%',
                top: mobile ? '18%' : '12%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: true,
                data: [],
                axisLabel: {
                    fontSize: mobile ? 9 : 11,
                    rotate: mobile ? 45 : 0
                }
            },
            yAxis: [
                {
                    type: 'value',
                    name: mobile ? '' : '数量',
                    position: 'left',
                    axisLabel: { fontSize: mobile ? 9 : 11 },
                    nameTextStyle: { fontSize: 10 }
                },
                {
                    type: 'value',
                    name: mobile ? '' : '金额(元)',
                    position: 'right',
                    axisLabel: { fontSize: mobile ? 9 : 11 },
                    nameTextStyle: { fontSize: 10 }
                }
            ],
            series: [
                {
                    name: '订单量',
                    type: 'bar',
                    data: [],
                    itemStyle: { color: '#3b82f6' },
                    barMaxWidth: mobile ? 12 : 20
                },
                {
                    name: '流水金额',
                    type: 'line',
                    smooth: true,
                    yAxisIndex: 1,
                    data: [],
                    itemStyle: { color: '#10b981' },
                    lineStyle: { width: mobile ? 1.5 : 2 },
                    symbol: mobile ? 'none' : 'circle',
                    symbolSize: mobile ? 0 : 4
                },
                {
                    name: '利润金额',
                    type: 'line',
                    smooth: true,
                    yAxisIndex: 1,
                    data: [],
                    itemStyle: { color: '#f59e0b' },
                    lineStyle: { width: mobile ? 1.5 : 2 },
                    symbol: mobile ? 'none' : 'circle',
                    symbolSize: mobile ? 0 : 4
                },
                {
                    name: '访问量',
                    type: 'line',
                    smooth: true,
                    data: [],
                    itemStyle: { color: '#8b5cf6' },
                    lineStyle: { width: mobile ? 1 : 2, type: 'dashed' },
                    symbol: mobile ? 'none' : 'circle',
                    symbolSize: mobile ? 0 : 4,
                    areaStyle: { opacity: mobile ? 0.02 : 0.05 }
                }
            ]
        });
    }
}

// 加载销售数据
function loadSalesData(days) {
    if(salesTrendChart) {
        salesTrendChart.showLoading({
            text: '加载中...',
            color: '#3b82f6',
            textColor: '#666',
            maskColor: 'rgba(255, 255, 255, 0.8)'
        });
    }

    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=sales_stats&days=' + days,
        dataType: 'json',
        cache: true,
        success: function(data) {
            if(salesTrendChart) salesTrendChart.hideLoading();
            if(data.code == 0) {
                // 更新趋势图
                if(salesTrendChart && data.trend) {
                    salesTrendChart.setOption({
                        xAxis: { data: data.trend.dates },
                        series: [
                            { data: data.trend.orders },
                            { data: data.trend.income },
                            { data: data.trend.profit },
                            { data: data.trend.visits }
                        ]
                    });
                }
                // 填充统计卡
                if(data.today){
                    $('#mc_today_orders').text(data.today.orders || 0);
                    $('#mc_today_income').text((data.today.income || 0).toFixed(2));
                    $('#mc_today_profit').text((data.today.profit || 0).toFixed(2));
                }
                if(data.yesterday){
                    $('#mc_yest_income').text((data.yesterday.income || 0).toFixed(2));
                    $('#mc_yest_profit').text((data.yesterday.profit || 0).toFixed(2));
                }
                $('#mc_pending').text((data.pending || 0).toFixed(2));
                $('#mc_pending2').text((data.pending || 0).toFixed(2));
                $('#mc_goods_count').text(data.goods_count || 0);
                $('#mc_uv').text(data.uv || 0);
                $('#mc_uv2').text(data.uv || 0);
                $('#mc_pv').text(data.pv || 0);

                // 生意罗盘同比
                var yest_pv = data.yest_pv || 0;
                var today_pv = data.pv || 0;
                var changeEl = $('.mc-traffic-change');
                if(yest_pv > 0){
                    var ratio = ((today_pv - yest_pv) / yest_pv * 100).toFixed(1);
                    var arrow = ratio >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                    changeEl.html(Math.abs(ratio) + '% <i class="fa ' + arrow + '"></i> 与昨日同比' + (ratio >= 0 ? '增长' : '下降'));
                } else {
                    changeEl.html('0% <i class="fa fa-arrow-up"></i> 与昨日同比增长');
                }
            }
        },
        error: function() {
            if(salesTrendChart) salesTrendChart.hideLoading();
        }
    });
}

// 窗口大小变化时重绘图表
var resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        if(salesTrendChart) {
            salesTrendChart.resize();
            // 重新应用响应式配置
            var mobile = isMobile();
            salesTrendChart.setOption({
                tooltip: {
                    textStyle: { fontSize: mobile ? 10 : 12 }
                },
                legend: {
                    textStyle: { fontSize: mobile ? 9 : 11 },
                    itemWidth: mobile ? 12 : 16,
                    itemHeight: mobile ? 8 : 10,
                    itemGap: mobile ? 6 : 12
                },
                grid: {
                    left: mobile ? '5%' : '3%',
                    right: mobile ? '6%' : '4%',
                    bottom: mobile ? '8%' : '3%',
                    top: mobile ? '18%' : '12%'
                },
                xAxis: {
                    axisLabel: {
                        fontSize: mobile ? 9 : 11,
                        rotate: mobile ? 45 : 0
                    }
                }
            });
        }
    }, 200);
});

// 图表标签切换
$(document).on('click', '.mc-chart-tab', function() {
    $('.mc-chart-tab').removeClass('active');
    $(this).addClass('active');
    var range = $(this).data('range');
    loadSalesData(range);
});

// 加载最新订单
function loadLatestOrders() {
    $.ajax({
        type: 'GET',
        url: 'ajax.php?act=latest_orders&limit=8',
        dataType: 'json',
        success: function(data) {
            var tbody = $('#mc_latest_orders');
            if(data.code == 0 && data.orders && data.orders.length > 0){
                var html = '';
                data.orders.forEach(function(o){
                    html += '<tr>'
                        + '<td style="font-family:monospace;font-size:12px;">'+o.ddno+'</td>'
                        + '<td>'+o.name+'</td>'
                        + '<td style="color:#ef4444;font-weight:600;">￥'+o.money.toFixed(2)+'</td>'
                        + '<td style="color:#6b7280;font-size:12px;">'+o.addtime+'</td>'
                        + '<td><span class="mc-badge mc-badge-'+o.status_class+'">'+o.status+'</span></td>'
                        + '<td><a href="./list.php" class="mc-link">详情</a></td>'
                        + '</tr>';
                });
                tbody.html(html);
            } else {
                tbody.html('<tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:30px;">暂无订单</td></tr>');
            }
        },
        error: function() {
            $('#mc_latest_orders').html('<tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:30px;">加载失败</td></tr>');
        }
    });
}

// 页面加载完成后初始化销售图表
$(document).ready(function() {
    if($('#sales_trend_chart').length > 0) {
        initSalesCharts();
        loadSalesData(7);
        loadLatestOrders();
    }
});
</script>

<?php include_once SYSTEM_ROOT.'sakura.php'; loadSakuraEffect(); ?>
<?php include 'foot.php'; ?>
