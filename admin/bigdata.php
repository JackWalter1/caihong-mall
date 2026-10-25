<?php
/**
 * 数据大屏（深色可视化）
 * 数据全部来自实时库表，30 秒自动刷新
 */
include("../includes/common.php");

if (!defined('IN_CRONLITE')) exit();
if ($islogin != 1) exit("<script language='javascript'>window.location.href='./login.php';</script>");

// ---------- 数据查询 ----------
function bigdata_payload() {
    global $DB;
    $data = array();

    // KPI
    $row = $DB->getRow("SELECT COUNT(*) AS c, IFNULL(SUM(money),0) AS m FROM pre_orders WHERE TO_DAYS(addtime)=TO_DAYS(NOW()) AND status>=1 AND status!=3");
    $data['todayOrders'] = (int)$row['c'];
    $data['todayMoney'] = round((float)$row['m'], 2);

    $row = $DB->getRow("SELECT COUNT(*) AS c, IFNULL(SUM(money),0) AS m FROM pre_orders WHERE status>=1 AND status!=3");
    $data['totalOrders'] = (int)$row['c'];
    $data['totalMoney'] = round((float)$row['m'], 2);

    $row = $DB->getRow("SELECT IFNULL(visits,0) AS v, IFNULL(ip_count,0) AS ip FROM pre_visit_statistics WHERE date=CURDATE()");
    $data['todayVisits'] = (int)$row['v'];
    $data['todayIps'] = (int)$row['ip'];

    $data['goodsCount'] = (int)$DB->getColumn("SELECT COUNT(*) FROM pre_tools");
    $data['siteCount'] = (int)$DB->getColumn("SELECT COUNT(*) FROM pre_site");
    $data['pendingWorkorder'] = (int)$DB->getColumn("SELECT COUNT(*) FROM pre_workorder WHERE status=0 OR status=1");

    // 近 7 日销售趋势（补齐缺失日期）
    $map = array();
    foreach ($DB->getAll("SELECT DATE(addtime) AS d, COUNT(*) AS c, IFNULL(SUM(money),0) AS m FROM pre_orders WHERE addtime>=DATE_SUB(CURDATE(),INTERVAL 6 DAY) AND status>=1 AND status!=3 GROUP BY DATE(addtime)") as $r) {
        $map[$r['d']] = array((int)$r['c'], round((float)$r['m'], 2));
    }
    $days = array(); $dayOrders = array(); $dayMoney = array();
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} day"));
        $days[] = date('m-d', strtotime($d));
        $dayOrders[] = isset($map[$d]) ? $map[$d][0] : 0;
        $dayMoney[] = isset($map[$d]) ? $map[$d][1] : 0;
    }
    $data['days'] = $days;
    $data['dayOrders'] = $dayOrders;
    $data['dayMoney'] = $dayMoney;

    // 订单状态分布
    $statusName = array(0 => '待支付', 1 => '已支付', 2 => '已完成', 3 => '已退款', 4 => '售后中');
    $pie = array();
    foreach ($DB->getAll("SELECT status, COUNT(*) AS c FROM pre_orders GROUP BY status") as $r) {
        $st = (int)$r['status'];
        $pie[] = array('name' => isset($statusName[$st]) ? $statusName[$st] : '状态' . $st, 'value' => (int)$r['c']);
    }
    $data['statusPie'] = $pie;

    // 热销商品 TOP8
    $hot = array();
    foreach ($DB->getAll("SELECT t.name AS name, COUNT(*) AS c FROM pre_orders o LEFT JOIN pre_tools t ON o.tid=t.id WHERE o.status>=1 GROUP BY o.tid ORDER BY c DESC LIMIT 8") as $r) {
        $hot[] = array('name' => $r['name'] ? $r['name'] : '已下架商品', 'value' => (int)$r['c']);
    }
    $data['hotGoods'] = $hot;

    // 近 7 日访问趋势
    $vmap = array();
    foreach ($DB->getAll("SELECT date, visits FROM pre_visit_statistics WHERE date>=DATE_SUB(CURDATE(),INTERVAL 6 DAY) ORDER BY date") as $r) {
        $vmap[substr($r['date'], 5)] = (int)$r['visits'];
    }
    $visitDays = array(); $visitVals = array();
    for ($i = 6; $i >= 0; $i--) {
        $k = date('m-d', strtotime("-{$i} day"));
        $visitDays[] = $k;
        $visitVals[] = isset($vmap[$k]) ? $vmap[$k] : 0;
    }
    $data['visitDays'] = $visitDays;
    $data['visitVals'] = $visitVals;

    // 最新 10 笔订单
    $latest = array();
    $sn = array(0 => '待支付', 1 => '已支付', 2 => '已完成', 3 => '已退款', 4 => '售后中');
    foreach ($DB->getAll("SELECT o.tradeno AS tradeno, IFNULL(t.name,'已下架商品') AS name, o.money AS money, o.status AS status, o.addtime AS addtime FROM pre_orders o LEFT JOIN pre_tools t ON o.tid=t.id ORDER BY o.id DESC LIMIT 10") as $r) {
        $st = (int)$r['status'];
        $latest[] = array(
            'tradeno' => $r['tradeno'],
            'name' => $r['name'],
            'money' => round((float)$r['money'], 2),
            'status' => isset($sn[$st]) ? $sn[$st] : '未知',
            'addtime' => $r['addtime'],
        );
    }
    $data['latest'] = $latest;

    return $data;
}

// 数据接口（供前端 30 秒轮询）
if (isset($_GET['act']) && $_GET['act'] == 'data') {
    @header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(bigdata_payload());
    exit();
}

$D = bigdata_payload();
$title = '数据大屏 - ' . $conf['sitename'];
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($title); ?></title>
<link rel="icon" href="/favicon.ico">
<script src="https://lib.baomitu.com/echarts/5.4.3/echarts.min.js"></script>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }
body {
    background: radial-gradient(1200px 700px at 50% -10%, #0c2a6e 0%, #071a4a 45%, #040d2b 100%);
    color: #d9ecff;
    font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Microsoft YaHei", sans-serif;
    overflow-x: hidden;
}
.wrap { min-height: 100vh; display: flex; flex-direction: column; padding: 14px 18px 18px; }

/* 顶部 */
.hd { position: relative; height: 64px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.hd h1 {
    font-size: 26px; font-weight: 800; letter-spacing: 6px; color: #eaf6ff;
    text-shadow: 0 0 18px rgba(0, 200, 255, .8), 0 0 46px rgba(0, 140, 255, .45);
}
.hd::before, .hd::after {
    content: ""; position: absolute; top: 50%; width: 26%; height: 3px;
    background: linear-gradient(90deg, transparent, #00cfff);
}
.hd::before { left: 4%; }
.hd::after { right: 4%; transform: scaleX(-1); }
.hd .back {
    position: absolute; left: 0; top: 50%; transform: translateY(-50%);
    color: #9fd4ff; font-size: 13px; text-decoration: none; border: 1px solid rgba(0, 207, 255, .4);
    border-radius: 6px; padding: 7px 14px; transition: background .2s, color .2s;
}
.hd .back:hover { background: rgba(0, 207, 255, .15); color: #fff; }
.hd .clock { position: absolute; right: 0; top: 50%; transform: translateY(-50%); font-size: 13px; color: #8fc3f5; letter-spacing: 1px; }

/* KPI */
.kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin: 4px 0 12px; }
.kpi {
    position: relative; padding: 14px 18px; border-radius: 10px;
    background: linear-gradient(135deg, rgba(18, 60, 140, .5), rgba(10, 32, 84, .55));
    border: 1px solid rgba(0, 190, 255, .28); overflow: hidden;
}
.kpi::after { content: ""; position: absolute; left: 0; top: 0; width: 100%; height: 2px;
    background: linear-gradient(90deg, transparent, #00cfff, transparent); opacity: .7; }
.kpi .num { font-size: 28px; font-weight: 800; color: #33e6ff; text-shadow: 0 0 14px rgba(0, 210, 255, .6); font-family: Consolas, monospace; }
.kpi .num small { font-size: 14px; color: #7fd7ff; margin-left: 2px; }
.kpi .lbl { margin-top: 4px; font-size: 12.5px; color: #9cc4ef; letter-spacing: 2px; }
.kpi.gold .num { color: #ffd257; text-shadow: 0 0 14px rgba(255, 190, 60, .55); }
.kpi.green .num { color: #4dffa6; text-shadow: 0 0 14px rgba(60, 255, 160, .5); }
.kpi.pink .num { color: #ff8bd0; text-shadow: 0 0 14px rgba(255, 120, 200, .5); }

/* 面板 */
.grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; flex: 1 1 auto; }
.panel {
    background: rgba(11, 34, 88, .48); border: 1px solid rgba(0, 190, 255, .22);
    border-radius: 10px; padding: 12px 14px 8px; display: flex; flex-direction: column; min-height: 300px;
}
.panel.wide { grid-column: span 2; }
.panel h3 {
    font-size: 14.5px; font-weight: 700; color: #bfe6ff; letter-spacing: 3px;
    padding: 2px 0 8px 10px; border-left: 3px solid #00cfff; margin-bottom: 8px;
    text-shadow: 0 0 10px rgba(0, 180, 255, .5);
}
.chart { flex: 1 1 auto; min-height: 0; }

/* 最新订单表 */
.order-list { flex: 1; overflow: auto; }
.order-list table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
.order-list th { color: #7fa8d9; font-weight: 600; text-align: left; padding: 6px 8px; border-bottom: 1px solid rgba(0, 190, 255, .18); white-space: nowrap; }
.order-list td { padding: 7px 8px; border-bottom: 1px dashed rgba(0, 190, 255, .1); color: #cfe6ff; word-break: break-all; }
.order-list tr:hover td { background: rgba(0, 190, 255, .06); }
.oq-st { display: inline-block; padding: 1px 9px; border-radius: 999px; font-size: 11.5px; white-space: nowrap; }
.st-0 { background: rgba(0, 160, 255, .18); color: #5cb8ff; }
.st-1 { background: rgba(60, 255, 160, .14); color: #4dffa6; }
.st-2 { background: rgba(0, 207, 255, .16); color: #33e6ff; }
.st-3 { background: rgba(255, 90, 90, .16); color: #ff8080; }
.st-4 { background: rgba(255, 190, 60, .16); color: #ffd257; }

.ft { flex-shrink: 0; text-align: center; color: #6f9ad0; font-size: 12px; padding-top: 10px; letter-spacing: 1px; }

@media (max-width: 1100px) {
    .kpis { grid-template-columns: repeat(2, 1fr); }
    .grid { grid-template-columns: 1fr; }
    .panel.wide { grid-column: span 1; }
    .panel { min-height: 280px; }
}
</style>
</head>
<body>
<div class="wrap">
    <div class="hd">
        <a class="back" href="./">← 返回后台</a>
        <h1><?php echo htmlspecialchars($conf['sitename']); ?> · 数据大屏</h1>
        <div class="clock" id="clock"></div>
    </div>

    <div class="kpis">
        <div class="kpi gold"><div class="num" id="kTodayMoney">0<small>元</small></div><div class="lbl">今日销售额</div></div>
        <div class="kpi"><div class="num" id="kTodayOrders">0<small>笔</small></div><div class="lbl">今日订单</div></div>
        <div class="kpi green"><div class="num" id="kTodayVisits">0<small>次</small></div><div class="lbl">今日访问量</div></div>
        <div class="kpi pink"><div class="num" id="kTodayIps">0<small>个</small></div><div class="lbl">今日访客 IP</div></div>
        <div class="kpi gold"><div class="num" id="kTotalMoney">0<small>元</small></div><div class="lbl">累计销售额</div></div>
        <div class="kpi"><div class="num" id="kTotalOrders">0<small>笔</small></div><div class="lbl">累计订单</div></div>
        <div class="kpi green"><div class="num" id="kGoods">0<small>件</small></div><div class="lbl">在售商品 / 分站 <span id="kSites" style="letter-spacing:0"></span></div></div>
        <div class="kpi"><div class="num" id="kWorkorder">0<small>单</small></div><div class="lbl">待处理工单</div></div>
    </div>

    <div class="grid">
        <div class="panel wide">
            <h3>近 7 日销售趋势</h3>
            <div class="chart" id="chTrend"></div>
        </div>
        <div class="panel">
            <h3>订单状态分布</h3>
            <div class="chart" id="chPie"></div>
        </div>
        <div class="panel">
            <h3>热销商品 TOP8</h3>
            <div class="chart" id="chHot"></div>
        </div>
        <div class="panel">
            <h3>近 7 日访问趋势</h3>
            <div class="chart" id="chVisit"></div>
        </div>
        <div class="panel">
            <h3>最新订单</h3>
            <div class="order-list">
                <table>
                    <thead><tr><th>订单号</th><th>商品</th><th>金额</th><th>状态</th><th>时间</th></tr></thead>
                    <tbody id="latestBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ft">数据每 30 秒自动刷新 · <?php echo htmlspecialchars($conf['sitename']); ?></div>
</div>

<script>
var D = <?php echo json_encode($D); ?>;
var STATUS_CLASS = { '待支付': 'st-0', '已支付': 'st-1', '已完成': 'st-2', '已退款': 'st-3', '售后中': 'st-4', '未知': 'st-0' };
var BLUE = '#00cfff', GOLD = '#ffd257';

function fmt(n) {
    n = Number(n) || 0;
    return n.toLocaleString('zh-CN', { maximumFractionDigits: 2 });
}
function renderKpi(d) {
    document.getElementById('kTodayMoney').innerHTML = fmt(d.todayMoney) + '<small>元</small>';
    document.getElementById('kTodayOrders').innerHTML = fmt(d.todayOrders) + '<small>笔</small>';
    document.getElementById('kTodayVisits').innerHTML = fmt(d.todayVisits) + '<small>次</small>';
    document.getElementById('kTodayIps').innerHTML = fmt(d.todayIps) + '<small>个</small>';
    document.getElementById('kTotalMoney').innerHTML = fmt(d.totalMoney) + '<small>元</small>';
    document.getElementById('kTotalOrders').innerHTML = fmt(d.totalOrders) + '<small>笔</small>';
    document.getElementById('kGoods').innerHTML = fmt(d.goodsCount) + '<small>件</small>';
    document.getElementById('kSites').textContent = ' / ' + d.siteCount;
    document.getElementById('kWorkorder').innerHTML = fmt(d.pendingWorkorder) + '<small>单</small>';
}
function renderLatest(d) {
    var html = '';
    (d.latest || []).forEach(function (o) {
        var cls = STATUS_CLASS[o.status] || 'st-0';
        html += '<tr><td>' + o.tradeno + '</td><td>' + o.name + '</td><td>￥' + fmt(o.money) + '</td>'
            + '<td><span class="oq-st ' + cls + '">' + o.status + '</span></td><td>' + o.addtime + '</td></tr>';
    });
    if (!html) html = '<tr><td colspan="5" style="text-align:center;color:#5f83b8;padding:26px 0">暂无订单数据</td></tr>';
    document.getElementById('latestBody').innerHTML = html;
}

var AX = { axis: 'rgba(120,170,230,.35)', split: 'rgba(90,140,210,.14)', label: '#8fb6e8' };
var charts = {};

function initCharts(d) {
    var font = { fontFamily: 'inherit' };
    charts.trend = echarts.init(document.getElementById('chTrend'));
    charts.trend.setOption({
        tooltip: { trigger: 'axis' },
        legend: { data: ['销售额', '订单量'], textStyle: { color: AX.label, fontSize: 11 }, top: 0, right: 8 },
        grid: { left: 8, right: 14, top: 32, bottom: 4, containLabel: true },
        xAxis: { type: 'category', data: d.days, axisLine: { lineStyle: { color: AX.axis } }, axisLabel: { color: AX.label, fontSize: 11 } },
        yAxis: [
            { type: 'value', name: '销售额', splitLine: { lineStyle: { color: AX.split } }, axisLabel: { color: AX.label, fontSize: 11 }, nameTextStyle: { color: AX.label } },
            { type: 'value', name: '订单量', splitLine: { show: false }, axisLabel: { color: AX.label, fontSize: 11 }, nameTextStyle: { color: AX.label } }
        ],
        series: [
            { name: '销售额', type: 'bar', data: d.dayMoney, barWidth: 18,
              itemStyle: { borderRadius: [4, 4, 0, 0], color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ offset: 0, color: '#00cfff' }, { offset: 1, color: 'rgba(0,120,255,.25)' }]) } },
            { name: '订单量', type: 'line', yAxisIndex: 1, data: d.dayOrders, smooth: true, symbolSize: 6,
              lineStyle: { color: GOLD, width: 2.5 }, itemStyle: { color: GOLD } }
        ]
    });

    charts.pie = echarts.init(document.getElementById('chPie'));
    charts.pie.setOption({
        tooltip: { trigger: 'item', formatter: '{b}: {c} ({d}%)' },
        legend: { bottom: 0, textStyle: { color: AX.label, fontSize: 11 }, itemWidth: 12, itemHeight: 8 },
        series: [{
            type: 'pie', radius: ['42%', '68%'], center: ['50%', '44%'],
            label: { color: AX.label, fontSize: 11, formatter: '{b}\n{c}' },
            itemStyle: { borderColor: '#08204f', borderWidth: 2 },
            data: d.statusPie.length ? d.statusPie : [{ name: '暂无订单', value: 1, itemStyle: { color: 'rgba(80,120,180,.25)' }, label: { color: '#5f83b8' } }]
        }],
        color: ['#33e6ff', '#4dffa6', '#ffd257', '#ff8080', '#ff8bd0', '#7f9dff']
    });

    charts.hot = echarts.init(document.getElementById('chHot'));
    var hot = (d.hotGoods || []).slice().reverse();
    charts.hot.setOption({
        tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
        grid: { left: 8, right: 30, top: 10, bottom: 4, containLabel: true },
        xAxis: { type: 'value', splitLine: { lineStyle: { color: AX.split } }, axisLabel: { color: AX.label, fontSize: 11 } },
        yAxis: { type: 'category', data: hot.map(function (h) { return h.name.length > 8 ? h.name.slice(0, 8) + '…' : h.name; }), axisLine: { lineStyle: { color: AX.axis } }, axisLabel: { color: AX.label, fontSize: 11 } },
        series: [{
            type: 'bar', data: hot.map(function (h) { return h.value; }), barWidth: 12,
            itemStyle: { borderRadius: [0, 6, 6, 0], color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [{ offset: 0, color: '#1268ff' }, { offset: 1, color: '#33e6ff' }]) },
            label: { show: true, position: 'right', color: GOLD, fontSize: 11 }
        }]
    });
    if (!hot.length) {
        charts.hot.setOption({ graphic: { type: 'text', left: 'center', top: 'middle', style: { text: '暂无数据', fill: '#5f83b8', fontSize: 13 } } });
    }

    charts.visit = echarts.init(document.getElementById('chVisit'));
    charts.visit.setOption({
        tooltip: { trigger: 'axis' },
        grid: { left: 8, right: 14, top: 24, bottom: 4, containLabel: true },
        xAxis: { type: 'category', data: d.visitDays, boundaryGap: false, axisLine: { lineStyle: { color: AX.axis } }, axisLabel: { color: AX.label, fontSize: 11 } },
        yAxis: { type: 'value', splitLine: { lineStyle: { color: AX.split } }, axisLabel: { color: AX.label, fontSize: 11 } },
        series: [{
            name: '访问量', type: 'line', data: d.visitVals, smooth: true, symbolSize: 6,
            lineStyle: { color: '#4dffa6', width: 2.5 }, itemStyle: { color: '#4dffa6' },
            areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ offset: 0, color: 'rgba(77,255,166,.3)' }, { offset: 1, color: 'rgba(77,255,166,0)' }]) }
        }]
    });
}

function refreshAll() {
    Object.keys(charts).forEach(function (k) { charts[k].resize(); });
}

document.addEventListener('DOMContentLoaded', function () {
    renderKpi(D);
    renderLatest(D);
    initCharts(D);
    window.addEventListener('resize', refreshAll);

    function tick() {
        var n = new Date();
        var p = function (x) { return x < 10 ? '0' + x : x; };
        var w = ['日', '一', '二', '三', '四', '五', '六'][n.getDay()];
        document.getElementById('clock').textContent =
            n.getFullYear() + '-' + p(n.getMonth() + 1) + '-' + p(n.getDate()) + ' 星期' + w + ' ' + p(n.getHours()) + ':' + p(n.getMinutes()) + ':' + p(n.getSeconds());
    }
    tick();
    setInterval(tick, 1000);

    setInterval(function () {
        fetch('./bigdata.php?act=data').then(function (r) { return r.json(); }).then(function (d) {
            D = d;
            renderKpi(D);
            renderLatest(D);
            charts.trend.setOption({ xAxis: { data: D.days }, series: [{ data: D.dayMoney }, { data: D.dayOrders }] });
            charts.pie.setOption({ series: [{ data: D.statusPie.length ? D.statusPie : [{ name: '暂无订单', value: 1 }] }] });
            var hot = (D.hotGoods || []).slice().reverse();
            charts.hot.setOption({
                yAxis: { data: hot.map(function (h) { return h.name.length > 8 ? h.name.slice(0, 8) + '…' : h.name; }) },
                series: [{ data: hot.map(function (h) { return h.value; }) }],
                graphic: hot.length ? [{ style: { text: '' } }] : [{ style: { text: '暂无数据' } }]
            });
            charts.visit.setOption({ xAxis: { data: D.visitDays }, series: [{ data: D.visitVals }] });
        }).catch(function () {});
    }, 30000);
});
</script>
</body>
</html>
