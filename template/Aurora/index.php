<?php
if (!defined('IN_CRONLITE')) {
    exit('Access Denied');
}
// ============================================================
// Aurora（晨雾）· 现代轻商务首页模板
// 三端设备识别（PC / 平板 / 手机）· 响应式 · 适配 6v6 云商城
// ============================================================

// ---- 设备多级识别（PC / 平板 / 手机） ----
if (!function_exists('aurora_detect_device')) {
    function aurora_detect_device()
    {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        // 平板：iPad / 安卓平板（安卓且非 mobile，排除 Windows Phone） / 常见平板标识
        $is_tablet = (strpos($ua, 'ipad') !== false) || (strpos($ua, 'tablet') !== false)
            || ((strpos($ua, 'android') !== false) && (strpos($ua, 'mobile') === false) && (strpos($ua, 'windows phone') === false));
        // 手机：iPhone / iPod / 安卓手机 / Windows Phone / 通用 mobile
        $is_mobile = (strpos($ua, 'iphone') !== false) || (strpos($ua, 'ipod') !== false)
            || ((strpos($ua, 'android') !== false) && (strpos($ua, 'mobile') !== false))
            || (strpos($ua, 'windows phone') !== false) || (strpos($ua, 'mobile') !== false)
            || (strpos($ua, 'phone') !== false);
        if ($is_tablet) {
            return 'tablet';
        }
        if ($is_mobile) {
            return 'mobile';
        }
        return 'pc';
    }
}
$aurora_device = aurora_detect_device();

// ---- 模板显示项 ----
$aur_show_hero = isset($conf['aur_show_hero']) ? $conf['aur_show_hero'] : '1';
$aur_show_features = isset($conf['aur_show_features']) ? $conf['aur_show_features'] : '1';
$aur_show_stats = isset($conf['aur_show_stats']) ? $conf['aur_show_stats'] : '1';
$aur_show_announce = isset($conf['aur_show_announce']) ? $conf['aur_show_announce'] : '1';
$aur_show_query = isset($conf['aur_show_query']) ? $conf['aur_show_query'] : '1';
$aur_show_article = isset($conf['aur_show_article']) ? $conf['aur_show_article'] : '1';
$aur_show_bottom = isset($conf['aur_show_bottom']) ? $conf['aur_show_bottom'] : '1';
$aur_show_warning = isset($conf['aur_show_warning']) ? $conf['aur_show_warning'] : '1';
$aur_show_build = isset($conf['aur_build_time']) ? $conf['aur_build_time'] : '1';
// Hero 副标题（固定文案，跑马灯风格下滚动展示）
$aur_slogan = '好物直供 · 即买即发 · 轻松省心';
// UI界面风格（白名单：aurora 晨雾 / tech 科技 / kami 卡密 / faka 发卡 / enterprise 企业 / xuanhuan 玄幻 / minimal 极简 / green 清新绿 / pink 少女粉 / vintage 复古黄 / chinese 中国风 / dark 暗夜黑 / marquee 跑马灯）
$aur_ui_style = isset($conf['aur_ui_style']) && in_array($conf['aur_ui_style'], array('aurora','tech','kami','faka','enterprise','xuanhuan','minimal','green','pink','vintage','chinese','dark','marquee')) ? $conf['aur_ui_style'] : 'aurora';
$show_order_warning = $aur_show_warning;
$show_warning_div = $aur_show_warning;
$show_guide_link = '0';
$usershop = false;

$site_name = !empty($conf['sitename']) ? $conf['sitename'] : '云商城';
$hero_title = !empty($conf['title']) ? $conf['title'] : $site_name;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover"/>
<title><?php echo $hometitle; ?></title>
<meta name="keywords" content="<?php echo $conf['keywords']; ?>">
<meta name="description" content="<?php echo $conf['description']; ?>">
<?php if (!empty($conf['favicon'])) { ?>
<link rel="icon" href="<?php echo $conf['favicon']; ?>" type="image/x-icon"/>
<?php } ?>
<link href="<?php echo $cdnpublic; ?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
<link href="<?php echo $cdnpublic; ?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
<style>
/* ================= Aurora · Modern Light ================= */
:root{
  --sky-1:#eaf2fc;
  --sky-2:#f6f9ff;
  --bg:#f4f7fc;
  --card:#ffffff;
  --ink:#1b2b4b;
  --mut:#6b7a99;
  --line:#e4ebf5;
  --blue:#3b82f6;
  --teal:#14b8a6;
  --orange:#f59e0b;
  --blue-soft:rgba(59,130,246,.08);
  --teal-soft:rgba(20,184,166,.08);
  --orange-soft:rgba(245,158,11,.1);
  --shadow:0 8px 28px rgba(30,60,110,.08);
  --shadow-lg:0 16px 44px rgba(30,60,110,.12);
}
*{box-sizing:border-box}
html,body{margin:0;padding:0;background:var(--bg);color:var(--ink);
  font-family:-apple-system,BlinkMacSystemFont,"PingFang SC","Hiragino Sans GB","Microsoft YaHei","Helvetica Neue",sans-serif;
  -webkit-font-smoothing:antialiased}
body{min-height:100vh;overflow-x:hidden}
body::before{content:"";position:fixed;inset:0;z-index:-2;background:
  linear-gradient(180deg,var(--sky-1) 0%,var(--sky-2) 34%,var(--bg) 100%)}
a{color:var(--blue);text-decoration:none;transition:color .2s}
a:hover{color:var(--teal)}
::selection{background:var(--blue);color:#fff}

/* ---------- 顶部导航 ---------- */
.aur-nav{position:sticky;top:0;z-index:100;background:rgba(255,255,255,.82);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
  border-bottom:1px solid var(--line)}
.aur-nav-inner{max-width:1200px;margin:0 auto;padding:0 20px;height:64px;display:flex;align-items:center;justify-content:space-between}
.aur-brand{display:flex;align-items:center;gap:12px}
.aur-brand img{height:38px;width:auto;border-radius:10px;box-shadow:var(--shadow)}
.aur-nav-links{display:flex;align-items:center;gap:4px}
.aur-nlink{padding:8px 16px;font-size:14px;color:var(--mut);border-radius:8px;transition:all .2s}
.aur-nlink:hover{color:var(--blue);background:var(--blue-soft)}
.aur-nlink.primary{background:linear-gradient(135deg,var(--blue),var(--teal));color:#fff;font-weight:600;box-shadow:0 6px 16px rgba(59,130,246,.3)}
.aur-nlink.primary:hover{color:#fff;filter:brightness(1.05)}
.aur-burger{display:none;width:40px;height:40px;border-radius:10px;border:1px solid var(--line);background:#fff;
  flex-direction:column;align-items:center;justify-content:center;gap:5px;cursor:pointer;pointer-events:auto !important;position:relative;z-index:110;user-select:none;-webkit-user-select:none}
.aur-burger span{display:block;width:20px;height:2px;background:var(--ink);border-radius:2px;transition:all .25s}
.aur-burger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.aur-burger.open span:nth-child(2){opacity:0}
.aur-burger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
.aur-mobile-menu{display:none;background:#fff;border-bottom:1px solid var(--line);padding:8px 16px}
.aur-mobile-menu a{display:block;padding:12px 10px;font-size:15px;color:var(--ink);border-radius:8px}
.aur-mobile-menu a:hover{background:var(--blue-soft);color:var(--blue)}

/* ---------- 布局 ---------- */
.aur-main{max-width:1200px;margin:0 auto;padding:32px 20px 70px}

/* ---------- Hero ---------- */
.aur-hero{background:linear-gradient(140deg,rgba(255,255,255,.9),rgba(234,242,252,.65));border:1px solid var(--line);border-radius:22px;
  box-shadow:var(--shadow);padding:clamp(36px,6vw,64px) clamp(22px,5vw,56px);position:relative;overflow:hidden;text-align:center}
.aur-hero::before{content:"";position:absolute;top:-90px;right:-60px;width:260px;height:260px;border-radius:50%;
  background:radial-gradient(circle,rgba(59,130,246,.16),transparent 65%)}
.aur-hero::after{content:"";position:absolute;bottom:-80px;left:-50px;width:220px;height:220px;border-radius:50%;
  background:radial-gradient(circle,rgba(20,184,166,.14),transparent 65%)}
.aur-hero .badge{display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:999px;font-size:12px;font-weight:600;
  color:var(--teal);background:var(--teal-soft);margin-bottom:20px;letter-spacing:1px}
.aur-hero h1{font-size:clamp(32px,5.4vw,54px);font-weight:800;letter-spacing:1px;line-height:1.2;margin:0 0 14px;color:var(--ink)}
.aur-hero h1 .mark{background:linear-gradient(120deg,var(--blue),var(--teal));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.aur-hero .slogan{font-size:clamp(14px,2vw,17px);color:var(--mut);letter-spacing:.5px;margin:0 0 6px}
.aur-hero .cta{margin-top:26px;display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
.aur-btn{display:inline-flex;align-items:center;gap:8px;padding:13px 34px;border-radius:12px;font-size:15px;font-weight:600;transition:all .2s}
.aur-btn.primary{background:linear-gradient(135deg,var(--blue),var(--teal));color:#fff;box-shadow:0 10px 24px rgba(59,130,246,.32)}
.aur-btn.primary:hover{transform:translateY(-2px);color:#fff}
.aur-btn.ghost{background:#fff;color:var(--ink);border:1.5px solid var(--line)}
.aur-btn.ghost:hover{border-color:var(--blue);color:var(--blue)}

/* ---------- 优势亮点 ---------- */
.aur-features{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin:22px 0 6px}
.aur-feat{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:26px 22px;display:flex;gap:16px;align-items:flex-start;transition:all .25s}
.aur-feat:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg)}
.aur-feat .ic{flex:0 0 46px;width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px}
.aur-feat:nth-child(1) .ic{background:var(--blue-soft);color:var(--blue)}
.aur-feat:nth-child(2) .ic{background:var(--teal-soft);color:var(--teal)}
.aur-feat:nth-child(3) .ic{background:var(--orange-soft);color:var(--orange)}
.aur-feat h4{margin:0 0 6px;font-size:16px;font-weight:700}
.aur-feat p{margin:0;font-size:13px;color:var(--mut);line-height:1.7}

/* ---------- 统计 ---------- */
.aur-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin:22px 0 6px}
.aur-stat{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:24px 16px;text-align:center;transition:all .25s}
.aur-stat:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg)}
.aur-stat .n{font-size:clamp(26px,3.4vw,38px);font-weight:800;line-height:1;color:var(--blue)}
.aur-stat .n i{font-style:normal;font-size:13px;color:var(--mut);margin-left:2px}
.aur-stat:nth-child(2) .n{color:var(--teal)}
.aur-stat:nth-child(3) .n{color:var(--orange)}
.aur-stat:nth-child(4) .n{color:var(--blue)}
.aur-stat .l{margin-top:10px;font-size:12px;color:var(--mut);letter-spacing:1px}

/* ---------- 公告 ---------- */
.aur-announce{display:flex;align-items:center;gap:12px;background:var(--card);border:1px solid var(--line);border-radius:14px;
  padding:13px 18px;margin:22px 0 6px}
.aur-announce .tag{flex:0 0 auto;background:linear-gradient(135deg,var(--blue),var(--teal));color:#fff;padding:4px 14px;border-radius:999px;font-size:12px;font-weight:600}
.aur-announce .txt{flex:1;color:var(--ink);font-size:13.5px;line-height:1.7}
.aur-announce .txt p{margin:0}

/* ---------- 区块标题 ---------- */
.aur-sec-head{display:flex;align-items:center;gap:14px;margin:40px 0 22px}
.aur-sec-head .bar{width:5px;height:26px;border-radius:3px;background:linear-gradient(180deg,var(--blue),var(--teal))}
.aur-sec-head h2{margin:0;font-size:22px;font-weight:800;letter-spacing:1px}
.aur-sec-head .rule{flex:1;border-top:1px solid var(--line)}

/* ---------- 卡片 ---------- */
.aur-card{background:var(--card);border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);padding:26px 28px 30px;margin-bottom:22px}

/* Tab */
.aur-tabs{display:flex;gap:8px;border-bottom:1px solid var(--line);padding-bottom:14px;margin-bottom:20px;flex-wrap:wrap;position:relative;z-index:5}
.aur-tab{padding:9px 22px;border-radius:999px;font-size:14px;color:var(--mut);cursor:pointer;border:1.5px solid transparent;transition:all .2s;pointer-events:auto !important;position:relative;z-index:6;user-select:none;-webkit-user-select:none}
.aur-tab:hover{color:var(--blue);border-color:var(--blue)}
.aur-tab.active{background:linear-gradient(135deg,var(--blue),var(--teal));color:#fff;font-weight:600;border-color:transparent}
.aur-pane{display:none}
.aur-pane.active{display:block;animation:aurIn .3s ease}
@keyframes aurIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}

/* ---------- 分类宫格（覆盖 shop.inc.php） ---------- */
.aur-shop .row{margin:0 -7px}
.aur-shop [class*="col-"]{padding:0 7px}
.widget{background:var(--card)!important;border:1px solid var(--line)!important;border-radius:14px!important;overflow:hidden;
  transition:transform .22s,box-shadow .22s;margin-bottom:14px!important}
.widget:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg)!important}
.widget img{width:100%;height:132px;object-fit:cover;display:block}
.widget-content{padding:14px 14px 16px!important;text-align:center}
.widget-content strong{color:var(--ink);font-size:15px;font-weight:700}
.widget-content .text-muted{color:var(--mut)!important;font-size:12px;margin:6px 0!important}
.thumbnail{background:var(--card)!important;border-color:var(--line)!important;border-radius:14px!important}

/* 表格 */
.table{color:var(--ink)!important}
.table-striped>tbody>tr:nth-of-type(odd){background:rgba(59,130,246,.03)}
.table>tbody>tr:hover{background:rgba(59,130,246,.06)!important}
.table>thead>tr>th{border-color:var(--line)!important;color:var(--blue);font-weight:600}

/* 表单 */
.form-control{background:#f8fafd!important;border:1.5px solid var(--line)!important;color:var(--ink)!important;
  border-radius:10px!important;height:42px;box-shadow:none!important}
.form-control:focus{border-color:var(--blue)!important;box-shadow:0 0 0 3px rgba(59,130,246,.12)!important;outline:none;background:#fff!important}
.form-control[disabled]{background:#f1f4f9!important;color:var(--mut)!important}
.input-group-addon{background:#eef3fa!important;border:1.5px solid var(--line)!important;color:var(--ink)!important;
  border-radius:10px 0 0 10px!important;font-size:13px;font-weight:600}
.input-group .form-control{border-radius:0 10px 10px 0!important}
select.form-control option{background:#fff;color:var(--ink)}
.alert{border-radius:10px!important;border:none!important;font-size:13px}
.alert-danger{background:rgba(239,68,68,.06)!important;color:#b91c1c!important;border-left:4px solid #ef4444!important}
.alert-info{background:var(--blue-soft)!important;color:#1d4ed8!important;border-left:4px solid var(--blue)!important}
.alert-success{background:var(--teal-soft)!important;color:#0f766e!important;border-left:4px solid var(--teal)!important}
.alert-warning{background:var(--orange-soft)!important;color:#b45309!important;border-left:4px solid var(--orange)!important}

/* 按钮 */
.btn{border:none!important;border-radius:10px!important;transition:all .2s!important;font-size:14px}
.btn:hover{filter:brightness(.96);transform:translateY(-1px)}
.btn-danger{background:linear-gradient(135deg,var(--blue),var(--teal))!important;color:#fff!important;font-weight:700}
.btn-info{background:#eef3fa!important;color:var(--ink)!important;border:1.5px solid var(--line)!important}
.btn-success{background:var(--teal-soft)!important;color:#0f766e!important;border:1.5px solid var(--teal)!important}
.btn-warning{background:var(--orange-soft)!important;color:#b45309!important;border:1.5px solid var(--orange)!important}
.btn-primary{background:var(--blue-soft)!important;color:var(--blue)!important;border:1.5px solid var(--blue)!important}
.btn-default{background:#f8fafd!important;color:var(--ink)!important;border:1.5px solid var(--line)!important}

/* 查单 */
.aur-query .form-control{height:44px}
#result2 th{color:var(--blue);font-size:13px}

/* 表格横向滚动（手机端可左右拖拽查看） */
.aur-table-x{overflow-x:auto!important;-webkit-overflow-scrolling:touch;scrollbar-width:thin}
.aur-table-x::-webkit-scrollbar{height:6px}
.aur-table-x::-webkit-scrollbar-track{background:#f1f5f9;border-radius:3px}
.aur-table-x::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
.aur-table-x::-webkit-scrollbar-thumb:hover{background:#94a3b8}

/* 推广链接 */
.aur-invite{margin-top:14px}
.aur-invite-btn{display:flex;align-items:center;gap:14px;padding:15px 20px;border-radius:14px;
  background:linear-gradient(135deg,var(--blue),var(--teal));color:#fff;box-shadow:0 10px 24px rgba(59,130,246,.28);transition:all .25s}
.aur-invite-btn:hover{transform:translateY(-2px);color:#fff;filter:brightness(1.05)}
.aur-invite-btn .ic{flex:0 0 40px;width:40px;height:40px;border-radius:11px;background:rgba(255,255,255,.22);display:flex;align-items:center;justify-content:center;font-size:18px}
.aur-invite-btn .info{flex:1;display:flex;flex-direction:column;gap:2px}
.aur-invite-btn .info strong{font-size:15px;font-weight:700;letter-spacing:1px}
.aur-invite-btn .info span{font-size:12px;opacity:.85}
.aur-invite-btn .go{font-size:16px;opacity:.9}

/* 抽奖 */
.aur-gift{text-align:center;padding:8px 0}
#roll{font-size:16px;color:var(--ink);margin:18px 0;font-weight:600}
.giftlist{display:none;margin-top:16px;border:1.5px solid var(--line);border-radius:10px;padding:12px;text-align:left}
.giftlist ul{list-style:none;margin:0;padding:0;overflow:hidden}
.giftlist li{padding:8px 10px;color:var(--ink);font-size:13px;border-bottom:1px dashed var(--line)}
.giftlist li:last-child{border-bottom:none}

/* 文章 */
.aur-article-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:0 30px}
.aur-article-item{display:flex;align-items:center;gap:12px;padding:13px 4px;border-bottom:1px dashed var(--line)}
.aur-article-item .no{flex:0 0 26px;height:26px;line-height:26px;text-align:center;border-radius:8px;font-size:12px;font-weight:700;
  color:#fff;background:linear-gradient(135deg,var(--blue),var(--teal))}
.aur-article-item a{color:var(--ink);font-size:14px;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.aur-article-item a:hover{color:var(--blue)}
.aur-article-all{display:block;text-align:center;margin-top:16px;padding:11px;border:1.5px dashed var(--blue);color:var(--blue);
  font-size:13px;border-radius:10px}
.aur-article-all:hover{background:var(--blue);color:#fff}

/* 站点助手 */
.aur-bottom{color:var(--ink);font-size:14px;line-height:2}
.aur-bottom a{color:var(--blue)}

/* ---------- 图片溢出保护（内容区大图自适应容器宽度） ---------- */
.aur-announce .txt{min-width:0}
.aur-announce .txt img,
.aur-bottom img,
.aur-footer img,
.modal-body img,
.alert img,
#goodTypeContents img,
#inputsname img{max-width:100%;height:auto}

/* 页脚 */
.aur-footer{border-top:1px solid var(--line);background:rgba(255,255,255,.6);max-width:1200px;margin:0 auto;padding:28px 20px 40px;text-align:center;color:var(--mut);font-size:12.5px;line-height:2.2}
.aur-footer .brand{font-size:16px;font-weight:800;color:var(--ink);letter-spacing:2px}
.aur-footer .brand i{font-style:normal;color:var(--blue)}
.aur-sec-head .aur-build{display:inline-flex;align-items:center;gap:6px;margin-left:14px;font-size:12px;color:var(--mut);white-space:nowrap}
.aur-sec-head .aur-build .fa{color:var(--blue)}

/* 音乐 / 返回顶部 */
#audio-play{position:fixed;right:20px;bottom:20px;z-index:200}
#audio-btn{width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--teal));display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 8px 22px rgba(59,130,246,.35)}
#audio-btn::after{content:"♪";color:#fff;font-size:20px;font-weight:700}
#audio-btn.off::after{content:"♩"}
#aur-top{position:fixed;right:20px;bottom:76px;z-index:200;width:42px;height:42px;border-radius:12px;background:#fff;
  border:1.5px solid var(--line);color:var(--blue);display:none;align-items:center;justify-content:center;cursor:pointer;box-shadow:var(--shadow)}
#aur-top:hover{border-color:var(--blue)}

/* ================= 三端响应 ================= */
/* 平板（801-1024px） */
@media (max-width:1024px){
  .aur-features{grid-template-columns:repeat(2,1fr)}
  .aur-stats{grid-template-columns:repeat(2,1fr)}
}
/* 手机（≤800px） */
@media (max-width:800px){
  .aur-nav-links{display:none}
  .aur-burger{display:flex}
  .aur-mobile-menu.open{display:block}
  .aur-features{grid-template-columns:1fr}
  .aur-article-grid{grid-template-columns:1fr}
  .aur-hero{padding:30px 18px 38px}
  .aur-card{padding:18px 16px 22px;border-radius:14px}
  .aur-sec-head h2{font-size:19px}
  .aur-main{padding:20px 12px 56px}
  .aur-stat{display:flex;align-items:center;justify-content:space-between;text-align:left;padding:16px 18px}
  .aur-stat .l{margin-top:0}
}
/* 小屏手机（≤420px） */
@media (max-width:420px){
  .aur-tab{padding:8px 14px;font-size:12px}
  .widget img{height:104px}
  .aur-cta .aur-btn{padding:11px 24px}
}
<?php if ($aur_ui_style != 'aurora') { ?>
/* ============================================================
   UI界面风格主题（后台模板设置 - UI界面风格 选择）
   ============================================================ */
/* ---------- 科技风 ---------- */
.ui-tech{--sky-1:#0a1226;--sky-2:#0d1730;--bg:#0b1220;--card:#111c33;--ink:#e6edf7;--mut:#8fa2c0;--line:#1e2c47;
  --blue:#00d4ff;--teal:#7c5cff;--orange:#ffb020;--blue-soft:rgba(0,212,255,.10);--teal-soft:rgba(124,92,255,.10);--orange-soft:rgba(255,176,32,.12);
  --shadow:0 8px 28px rgba(0,0,0,.35);--shadow-lg:0 16px 44px rgba(0,0,0,.45)}
.ui-tech .aur-nav{background:rgba(13,23,48,.82);border-bottom-color:var(--line)}
.ui-tech .aur-mobile-menu{background:#0d1730}
.ui-tech .aur-burger{background:#111c33;border-color:var(--line)}
.ui-tech .aur-burger span{background:var(--ink)}
.ui-tech .aur-hero{background:linear-gradient(140deg,rgba(20,30,60,.9),rgba(10,18,38,.7));border-color:var(--line)}
.ui-tech .aur-btn.ghost{background:var(--card);color:var(--ink)}
.ui-tech .form-control{background:#0d1730!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-tech .form-control:focus{background:#0d1730!important}
.ui-tech select.form-control option{background:#0d1730;color:var(--ink)}
.ui-tech .input-group-addon{background:#16224a!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-tech .btn-info{background:#16224a!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-tech .btn-default{background:#0d1730!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-tech .aur-footer{background:rgba(13,23,48,.6)}
.ui-tech #aur-top{background:var(--card)}
.ui-tech .aur-table-x::-webkit-scrollbar-track{background:#0d1730}
.ui-tech .aur-table-x::-webkit-scrollbar-thumb{background:#2a3b63}
/* ---------- 卡密风 ---------- */
.ui-kami{--sky-1:#1c0f0a;--sky-2:#241208;--bg:#150d0a;--card:#221712;--ink:#f6ece2;--mut:#c2a890;--line:#3a2a1e;
  --blue:#ff6b35;--teal:#f7b500;--orange:#ff4d2e;--blue-soft:rgba(255,107,53,.10);--teal-soft:rgba(247,181,0,.10);--orange-soft:rgba(255,77,46,.12);
  --shadow:0 8px 28px rgba(0,0,0,.4);--shadow-lg:0 16px 44px rgba(0,0,0,.5)}
.ui-kami .aur-nav{background:rgba(34,23,18,.82);border-bottom-color:var(--line)}
.ui-kami .aur-mobile-menu{background:#241208}
.ui-kami .aur-burger{background:#221712;border-color:var(--line)}
.ui-kami .aur-burger span{background:var(--ink)}
.ui-kami .aur-hero{background:linear-gradient(140deg,rgba(50,28,15,.9),rgba(21,13,10,.7));border-color:var(--line)}
.ui-kami .aur-btn.ghost{background:var(--card);color:var(--ink)}
.ui-kami .form-control{background:#1a110d!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-kami .form-control:focus{background:#1a110d!important}
.ui-kami select.form-control option{background:#1a110d;color:var(--ink)}
.ui-kami .input-group-addon{background:#33210f!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-kami .btn-info{background:#33210f!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-kami .btn-default{background:#1a110d!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-kami .aur-footer{background:rgba(34,23,18,.6)}
.ui-kami #aur-top{background:var(--card)}
.ui-kami .aur-table-x::-webkit-scrollbar-track{background:#1a110d}
.ui-kami .aur-table-x::-webkit-scrollbar-thumb{background:#4a3a26}
/* ---------- 发卡风 ---------- */
.ui-faka{--sky-1:#e6f4ff;--sky-2:#f0f9ff;--bg:#eef6ff;--card:#ffffff;--ink:#17304d;--mut:#6d8aa8;--line:#d8e8f5;
  --blue:#1677ff;--teal:#00b7a3;--orange:#ff9500;--blue-soft:rgba(22,119,255,.08);--teal-soft:rgba(0,183,163,.08);--orange-soft:rgba(255,149,0,.1);
  --shadow:0 8px 28px rgba(20,80,150,.08);--shadow-lg:0 16px 44px rgba(20,80,150,.12)}
/* ---------- 企业风 ---------- */
.ui-enterprise{--sky-1:#eef2f7;--sky-2:#f5f7fa;--bg:#f2f4f8;--card:#ffffff;--ink:#1f2a3d;--mut:#5f6f85;--line:#dde4ee;
  --blue:#0f4c81;--teal:#2c7fb8;--orange:#c9a227;--blue-soft:rgba(15,76,129,.08);--teal-soft:rgba(44,127,184,.08);--orange-soft:rgba(201,162,39,.1);
  --shadow:0 6px 22px rgba(20,45,80,.07);--shadow-lg:0 12px 34px rgba(20,45,80,.11)}
/* ---------- 玄幻风 ---------- */
.ui-xuanhuan{--sky-1:#151026;--sky-2:#1c1433;--bg:#120d22;--card:#1b1430;--ink:#efe9fb;--mut:#a99bc9;--line:#2c2350;
  --blue:#a78bfa;--teal:#f0c75e;--orange:#ff8ac2;--blue-soft:rgba(167,139,250,.10);--teal-soft:rgba(240,199,94,.10);--orange-soft:rgba(255,138,194,.12);
  --shadow:0 8px 28px rgba(0,0,0,.4);--shadow-lg:0 16px 44px rgba(0,0,0,.5)}
.ui-xuanhuan .aur-nav{background:rgba(28,20,51,.82);border-bottom-color:var(--line)}
.ui-xuanhuan .aur-mobile-menu{background:#1c1433}
.ui-xuanhuan .aur-burger{background:#1b1430;border-color:var(--line)}
.ui-xuanhuan .aur-burger span{background:var(--ink)}
.ui-xuanhuan .aur-hero{background:linear-gradient(140deg,rgba(40,28,70,.9),rgba(18,13,34,.7));border-color:var(--line)}
.ui-xuanhuan .aur-btn.ghost{background:var(--card);color:var(--ink)}
.ui-xuanhuan .form-control{background:#161027!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-xuanhuan .form-control:focus{background:#161027!important}
.ui-xuanhuan select.form-control option{background:#161027;color:var(--ink)}
.ui-xuanhuan .input-group-addon{background:#2a2050!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-xuanhuan .btn-info{background:#2a2050!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-xuanhuan .btn-default{background:#161027!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-xuanhuan .aur-footer{background:rgba(28,20,51,.6)}
.ui-xuanhuan #aur-top{background:var(--card)}
.ui-xuanhuan .aur-table-x::-webkit-scrollbar-track{background:#161027}
.ui-xuanhuan .aur-table-x::-webkit-scrollbar-thumb{background:#3a2f63}
/* ---------- 极简风 ---------- */
.ui-minimal{--sky-1:#f5f5f5;--sky-2:#fafafa;--bg:#f0f0f0;--card:#ffffff;--ink:#111111;--mut:#888888;--line:#e2e2e2;
  --blue:#333333;--teal:#111111;--orange:#999999;--blue-soft:rgba(0,0,0,.04);--teal-soft:rgba(0,0,0,.04);--orange-soft:rgba(0,0,0,.04);
  --shadow:0 4px 16px rgba(0,0,0,.05);--shadow-lg:0 10px 30px rgba(0,0,0,.08)}
/* ---------- 清新绿 ---------- */
.ui-green{--sky-1:#e8f8ee;--sky-2:#f2fbf6;--bg:#eff9f3;--card:#ffffff;--ink:#14382a;--mut:#6b9380;--line:#d8efe2;
  --blue:#16a34a;--teal:#0d9488;--orange:#f59e0b;--blue-soft:rgba(22,163,74,.08);--teal-soft:rgba(13,148,136,.08);--orange-soft:rgba(245,158,11,.1);
  --shadow:0 8px 28px rgba(20,110,70,.08);--shadow-lg:0 16px 44px rgba(20,110,70,.12)}
/* ---------- 少女粉 ---------- */
.ui-pink{--sky-1:#fdeef4;--sky-2:#fef6f9;--bg:#fdf3f7;--card:#ffffff;--ink:#4a2438;--mut:#b78aa0;--line:#f6dfe9;
  --blue:#ec4899;--teal:#f472b6;--orange:#fb923c;--blue-soft:rgba(236,72,153,.08);--teal-soft:rgba(244,114,182,.08);--orange-soft:rgba(251,146,60,.1);
  --shadow:0 8px 28px rgba(200,60,130,.08);--shadow-lg:0 16px 44px rgba(200,60,130,.12)}
/* ---------- 复古黄 ---------- */
.ui-vintage{--sky-1:#f6efe0;--sky-2:#faf5ea;--bg:#f5eddd;--card:#fffdf7;--ink:#4a3b22;--mut:#a08c6a;--line:#eadfc9;
  --blue:#a3762c;--teal:#8a6d3b;--orange:#d9a441;--blue-soft:rgba(163,118,44,.08);--teal-soft:rgba(138,109,59,.08);--orange-soft:rgba(217,164,65,.1);
  --shadow:0 6px 20px rgba(120,90,40,.08);--shadow-lg:0 12px 32px rgba(120,90,40,.12)}
/* ---------- 中国风 ---------- */
.ui-chinese{--sky-1:#1c1010;--sky-2:#241414;--bg:#160c0c;--card:#221616;--ink:#f5e8dd;--mut:#c2a394;--line:#3a2626;
  --blue:#c0392b;--teal:#d4a017;--orange:#e8491d;--blue-soft:rgba(192,57,43,.10);--teal-soft:rgba(212,160,23,.10);--orange-soft:rgba(232,73,29,.12);
  --shadow:0 8px 28px rgba(0,0,0,.4);--shadow-lg:0 16px 44px rgba(0,0,0,.5)}
.ui-chinese .aur-nav{background:rgba(34,22,22,.82);border-bottom-color:var(--line)}
.ui-chinese .aur-mobile-menu{background:#241414}
.ui-chinese .aur-burger{background:#221616;border-color:var(--line)}
.ui-chinese .aur-burger span{background:var(--ink)}
.ui-chinese .aur-hero{background:linear-gradient(140deg,rgba(52,26,20,.9),rgba(22,12,12,.7));border-color:var(--line)}
.ui-chinese .aur-btn.ghost{background:var(--card);color:var(--ink)}
.ui-chinese .form-control{background:#1a0f0f!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-chinese .form-control:focus{background:#1a0f0f!important}
.ui-chinese select.form-control option{background:#1a0f0f;color:var(--ink)}
.ui-chinese .input-group-addon{background:#332020!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-chinese .btn-info{background:#332020!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-chinese .btn-default{background:#1a0f0f!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-chinese .aur-footer{background:rgba(34,22,22,.6)}
.ui-chinese #aur-top{background:var(--card)}
.ui-chinese .aur-table-x::-webkit-scrollbar-track{background:#1a0f0f}
.ui-chinese .aur-table-x::-webkit-scrollbar-thumb{background:#4a3434}
/* ---------- 暗夜黑 ---------- */
.ui-dark{--sky-1:#0d0d0f;--sky-2:#131316;--bg:#0a0a0c;--card:#16161a;--ink:#e8e8ea;--mut:#94949c;--line:#26262c;
  --blue:#4f46e5;--teal:#22d3ee;--orange:#f59e0b;--blue-soft:rgba(79,70,229,.10);--teal-soft:rgba(34,211,238,.10);--orange-soft:rgba(245,158,11,.12);
  --shadow:0 8px 28px rgba(0,0,0,.5);--shadow-lg:0 16px 44px rgba(0,0,0,.6)}
.ui-dark .aur-nav{background:rgba(19,19,22,.82);border-bottom-color:var(--line)}
.ui-dark .aur-mobile-menu{background:#131316}
.ui-dark .aur-burger{background:#16161a;border-color:var(--line)}
.ui-dark .aur-burger span{background:var(--ink)}
.ui-dark .aur-hero{background:linear-gradient(140deg,rgba(24,24,30,.9),rgba(10,10,12,.7));border-color:var(--line)}
.ui-dark .aur-btn.ghost{background:var(--card);color:var(--ink)}
.ui-dark .form-control{background:#0f0f12!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-dark .form-control:focus{background:#0f0f12!important}
.ui-dark select.form-control option{background:#0f0f12;color:var(--ink)}
.ui-dark .input-group-addon{background:#1f1f26!important;border-color:var(--line)!important;color:var(--ink)!important}
.ui-dark .btn-info{background:#1f1f26!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-dark .btn-default{background:#0f0f12!important;color:var(--ink)!important;border-color:var(--line)!important}
.ui-dark .aur-footer{background:rgba(19,19,22,.6)}
.ui-dark #aur-top{background:var(--card)}
.ui-dark .aur-table-x::-webkit-scrollbar-track{background:#0f0f12}
.ui-dark .aur-table-x::-webkit-scrollbar-thumb{background:#33333b}
/* ---------- 跑马灯 ---------- */
.ui-marquee{--sky-1:#eef4ff;--sky-2:#fdf2ff;--bg:#f4f6ff;--card:#ffffff;--ink:#232b4d;--mut:#7481a8;--line:#e3e9f8;
  --blue:#3b82f6;--teal:#10b981;--orange:#f59e0b;--blue-soft:rgba(59,130,246,.08);--teal-soft:rgba(16,185,129,.08);--orange-soft:rgba(245,158,11,.1);
  --shadow:0 8px 28px rgba(50,80,180,.08);--shadow-lg:0 16px 44px rgba(50,80,180,.12)}
.ui-marquee .aur-hero .slogan{display:inline-block;white-space:nowrap;font-weight:600;font-size:clamp(15px,2.2vw,18px);
  background:linear-gradient(90deg,#f43f5e,#f59e0b,#22c55e,#06b6d4,#8b5cf6,#f43f5e);background-size:200% auto;
  -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:transparent;
  animation:aur-marquee 8s linear infinite}
@keyframes aur-marquee{0%{transform:translateX(28%)}50%{transform:translateX(-28%)}100%{transform:translateX(28%)}}
<?php } ?>
</style>
</head>
<body data-device="<?php echo $aurora_device; ?>" class="device-<?php echo $aurora_device; ?> ui-<?php echo $aur_ui_style; ?>">

<!-- 顶部导航 -->
<header class="aur-nav">
  <div class="aur-nav-inner">
    <div class="aur-brand">
      <img src="<?php echo $logo; ?>" alt="<?php echo $site_name; ?>">
    </div>
    <nav class="aur-nav-links">
      <?php if (!empty($conf['daiguaurl'])) { ?><a class="aur-nlink" href="./?mod=daigua"><i class="fa fa-rocket"></i> 代挂</a><?php } ?>
      <?php if ($conf['fenzhan_buy'] == 1) { ?><a class="aur-nlink" href="./user/regsite.php"><i class="fa fa-globe"></i> 开通分站</a><?php } ?>
      <?php if ($islogin2 == 1) { ?>
        <a class="aur-nlink primary" href="./user/"><i class="fa fa-user"></i> 用户中心</a>
      <?php } else { ?>
        <a class="aur-nlink primary" href="./user/login.php"><i class="fa fa-sign-in"></i> 登录</a>
      <?php } ?>
    </nav>
    <div class="aur-burger" id="aurBurger" onclick="aurToggleMenu(this)"><span></span><span></span><span></span></div>
  </div>
</header>
<div class="aur-mobile-menu" id="aurMobileMenu">
  <?php if (!empty($conf['daiguaurl'])) { ?><a href="./?mod=daigua"><i class="fa fa-rocket"></i> 代挂</a><?php } ?>
  <?php if ($conf['fenzhan_buy'] == 1) { ?><a href="./user/regsite.php"><i class="fa fa-globe"></i> 开通分站</a><?php } ?>
  <?php if ($islogin2 == 1) { ?>
    <a href="./user/"><i class="fa fa-user"></i> 用户中心</a>
  <?php } else { ?>
    <a href="./user/login.php"><i class="fa fa-sign-in"></i> 登录</a>
  <?php } ?>
</div>

<main class="aur-main">

  <!-- Hero -->
  <?php if ($aur_show_hero == '1') { ?>
  <section class="aur-hero">
    <div class="badge"><i class="fa fa-check-circle"></i> 在线商城 · 自动发货</div>
    <h1><span class="mark"><?php echo $hero_title; ?></span></h1>
    <p class="slogan"><?php echo $aur_slogan; ?></p>
  </section>
  <?php } ?>

  <!-- 公告：已移至"自助下单"的公告Tab中查看，不在此处直接显示 -->

  <!-- 自助下单 -->
  <div class="aur-sec-head"><span class="bar"></span><h2>自助下单</h2><span class="rule"></span>
    <?php if ($aur_show_build == '1' && !empty($conf['build'])) { ?>
    <span class="aur-build"><i class="fa fa-calendar"></i> 本站创建于 <?php echo htmlspecialchars($conf['build']); ?></span>
    <?php } ?>
  </div>
  <section class="aur-card aur-shop">
    <div class="aur-tabs" id="mainTabs">
      <span class="aur-tab active" data-pane="pane-buy" onclick="aurSwitchPane(this)"><i class="fa fa-shopping-cart"></i> 商品选购</span>
      <?php if ($aur_show_query == '1') { ?><span class="aur-tab" data-pane="pane-query" onclick="aurSwitchPane(this)"><i class="fa fa-search"></i> 订单查询</span><?php } ?>
      <?php if ($conf['gift_open'] == 1) { ?><span class="aur-tab" data-pane="pane-gift" onclick="aurSwitchPane(this)"><i class="fa fa-gift"></i> 幸运抽奖</span><?php } ?>
      <?php if (!empty($conf['anounce'])) { ?><span class="aur-tab" data-pane="pane-announce" onclick="aurSwitchPane(this)"><i class="fa fa-bullhorn"></i> 公告</span><?php } ?>
    </div>

    <div class="aur-pane active" id="pane-buy">
      <?php include TEMPLATE_ROOT . 'default/shop.inc.php'; ?>
      <?php if (!empty($conf['invite_tid'])) { ?>
      <div class="aur-invite">
        <a class="aur-invite-btn" href="./?mod=invite" target="_blank">
          <span class="ic"><i class="fa fa-paper-plane-o"></i></span>
          <span class="info"><strong>推广赚福利</strong><span>生成专属推广链接，邀请好友下单，奖励领不停</span></span>
          <i class="fa fa-chevron-right go"></i>
        </a>
      </div>
      <?php } ?>
    </div>

    <?php if ($aur_show_query == '1') { ?>
    <div class="aur-pane aur-query" id="pane-query">
      <div class="alert alert-info" <?php if (empty($conf['gg_search'])) { ?>style="display:none;"<?php } ?>><?php echo $conf['gg_search']; ?></div>
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-btn">
            <select class="form-control" id="searchtype" style="width:104px;border-radius:10px 0 0 10px;"><option value="0">下单账号</option><option value="1">订单号</option></select>
          </div>
          <input type="text" name="qq" id="qq3" value="<?php echo $qq; ?>" class="form-control" placeholder="请输入要查询的内容（留空显示最新订单）" onkeydown="if(event.keyCode==13){submit_query.click()}"/>
        </div>
      </div>
      <button type="button" id="submit_query" class="btn btn-danger btn-block" style="height:44px;">立即查询</button>
      <div id="result2" class="form-group" style="display:none;margin-top:14px;">
        <div class="table-responsive aur-table-x" style="border:1.5px solid var(--line);border-radius:10px;overflow-x:auto;-webkit-overflow-scrolling:touch;">
          <table class="table table-striped" style="min-width:560px;margin-bottom:0;">
            <thead><tr><th>操作</th><th>状态</th><th>下单信息</th><th class="hidden-xs">商品名称</th><th>数量</th><th>购买时间</th></tr></thead>
            <tbody id="list"></tbody>
          </table>
        </div>
      </div>
    </div>
    <?php } ?>

    <?php if ($conf['gift_open'] == 1) { ?>
    <div class="aur-pane aur-gift" id="pane-gift">
      <div id="roll">点击下方按钮开始抽奖</div>
      <div class="form-group">
        <button type="button" class="btn btn-danger btn-block" id="start" style="height:44px;">开始抽奖</button>
        <button type="button" class="btn btn-info btn-block" id="stop" style="height:44px;display:none;">停止</button>
      </div>
      <div id="result"></div>
      <div class="giftlist" style="display:none;"><strong>最近中奖记录</strong><ul id="pst_1"></ul></div>
    </div>
    <?php } ?>

    <?php if (!empty($conf['anounce'])) { ?>
    <div class="aur-pane" id="pane-announce">
      <div class="aur-announce" style="margin:0;display:flex;"><span class="tag">公告</span><div class="txt"><?php echo $conf['anounce']; ?></div></div>
    </div>
    <?php } ?>
  </section>

  <!-- 优势亮点 -->
  <?php if ($aur_show_features == '1') { ?>
  <section class="aur-features">
    <div class="aur-feat"><div class="ic"><i class="fa fa-bolt"></i></div><div><h4>极速发货</h4><p>付款后系统自动发货，即买即得，全天候在线。</p></div></div>
    <div class="aur-feat"><div class="ic"><i class="fa fa-shield"></i></div><div><h4>安全交易</h4><p>多重安全防护与加密传输，交易放心无忧。</p></div></div>
    <div class="aur-feat"><div class="ic"><i class="fa fa-headphones"></i></div><div><h4>贴心售后</h4><p>工单客服快速响应，为你解决每一个问题。</p></div></div>
  </section>
  <?php } ?>

  <!-- 数据统计 -->
  <?php if ($aur_show_stats == '1' && !$conf['hide_tongji']) { ?>
  <section class="aur-stats">
    <div class="aur-stat"><div class="n"><span id="count_money">0</span><i>元</i></div><div class="l">累计交易金额</div></div>
    <div class="aur-stat"><div class="n"><span id="count_orders">0</span><i>笔</i></div><div class="l">累计订单总数</div></div>
    <div class="aur-stat"><div class="n"><span id="count_money1">0</span><i>元</i></div><div class="l">今日交易金额</div></div>
    <div class="aur-stat"><div class="n"><span id="count_orders2">0</span><i>笔</i></div><div class="l">今日订单总数</div></div>
  </section>
  <?php } ?>

  <!-- 文章 -->
  <?php if ($aur_show_article == '1' && $conf['articlenum'] > 0) {
    $limit = intval($conf['articlenum']);
    $rs = $DB->query("SELECT id,title FROM pre_article WHERE active=1 ORDER BY top DESC,id DESC LIMIT {$limit}");
    $msgrow = array();
    while ($res = $rs->fetch()) {
      $msgrow[] = $res;
    }
    if (!empty($msgrow)) { ?>
  <div class="aur-sec-head"><span class="bar"></span><h2>文章中心</h2><span class="rule"></span></div>
  <section class="aur-card">
    <div class="aur-article-grid">
      <?php $i = 1; foreach ($msgrow as $row) { ?>
      <div class="aur-article-item"><span class="no"><?php echo $i; ?></span><a href="<?php echo article_url($row['id']); ?>" target="_blank"><?php echo $row['title']; ?></a></div>
      <?php $i++; } ?>
    </div>
    <a class="aur-article-all" href="<?php echo article_url(); ?>" target="_blank">查看全部文章</a>
  </section>
  <?php } } ?>

  <!-- 站点助手 -->
  <?php if ($aur_show_bottom == '1' && !empty($conf['bottom'])) { ?>
  <div class="aur-sec-head"><span class="bar"></span><h2>站点助手</h2><span class="rule"></span></div>
  <section class="aur-card">
    <div class="aur-bottom"><?php echo $conf['bottom']; ?></div>
  </section>
  <?php } ?>

  <?php if ($conf['classblock'] == 1 || $conf['classblock'] == 2 && checkmobile() == false) { include TEMPLATE_ROOT . 'default/classblock.inc.php'; } ?>
</main>

<!-- 页脚 -->
<footer class="aur-footer">
  <div class="brand"><?php echo $site_name; ?><i>.</i></div>
  <div>CopyRight <i class="fa fa-heart" style="color:var(--blue)"></i> <?php echo date("Y"); ?> <a href="/"><?php echo $site_name; ?></a> · 保留所有权利</div>
  <div><?php echo $conf['footer']; ?></div>
</footer>

<div id="aur-top"><i class="fa fa-chevron-up"></i></div>

<?php if (!empty($conf['musicurl'])) { ?>
<div id="audio-play"><div id="audio-btn" class="on"><audio loop="loop" src="<?php echo $conf['musicurl']; ?>" id="media" preload="preload"></audio></div></div>
<?php } ?>

<?php if (!empty($conf['modal'])) { ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content" style="background:#fff;color:var(--ink);border:1px solid var(--line);border-radius:16px">
    <div class="modal-header" style="border-bottom:1px solid var(--line)">
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="myModalLabel" style="font-weight:700"><?php echo $site_name; ?></h4>
    </div>
    <div class="modal-body"><?php echo $conf['modal']; ?></div>
    <div class="modal-footer" style="border-top:1px solid var(--line)">
      <button type="button" class="btn btn-danger" data-dismiss="modal">知道了</button>
    </div>
  </div></div>
</div>
<?php } ?>

<script src="<?php echo $cdnpublic; ?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic; ?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic; ?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo $cdnpublic; ?>layer/2.3/layer.js"></script>
<script src="<?php echo $cdnpublic; ?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="assets/js/main.js?ver=<?php echo VERSION; ?>"></script>

<script>
var hashsalt = <?php echo $addsalt_js; ?>;
var homepage = true;
// PC端订单查询结果直接内嵌显示在查询区域，不单独弹出窗口
var querymode = 'noPopup';
var isModal = <?php echo empty($conf['modal']) ? 'false' : 'true'; ?>;
var modalShowType = <?php echo isset($conf['modal_show_type']) ? intval($conf['modal_show_type']) : 0; ?>;
var AURORA_DEVICE = '<?php echo $aurora_device; ?>';

// Tab 切换（原生兜底，兼容任何事件绑定问题）
function aurSwitchPane(el) {
  if (!el) return;
  var paneId = el.getAttribute('data-pane');
  if (!paneId) return;
  var tabs = document.getElementById('mainTabs');
  if (tabs) {
    var all = tabs.querySelectorAll('.aur-tab');
    for (var i = 0; i < all.length; i++) all[i].classList.remove('active');
  }
  el.classList.add('active');
  var panes = document.querySelectorAll('.aur-pane');
  for (var j = 0; j < panes.length; j++) panes[j].classList.remove('active');
  var target = document.getElementById(paneId);
  if (target) target.classList.add('active');
}
document.addEventListener('click', function(e) {
  var t = e.target;
  while (t && t !== document) {
    if (t.classList && t.classList.contains('aur-tab')) { aurSwitchPane(t); return; }
    if (t.id && t.id === 'aurBurger') { aurToggleMenu(t); return; }
    t = t.parentNode;
  }
});

// 汉堡菜单（带防重锁，避免 inline+监听 双触发导致开关复原）
var _aurMenuLock = 0;
function aurToggleMenu(el) {
  if (!el) return;
  var now = Date.now();
  if (now - _aurMenuLock < 60) return;
  _aurMenuLock = now;
  el.classList.toggle('open');
  var menu = document.getElementById('aurMobileMenu');
  if (menu) menu.classList.toggle('open');
}

$(function() {
  if ($('#count_money').length && typeof getcount === 'function') {
    getcount();
  }

  if ($.fn.lazyload) {
    $('img.lazy').lazyload({ effect: 'fadeIn', threshold: 200 });
  }
  // 图片显示兜底：懒加载插件缺失或未生效时，直接加载 data-original 图片（修复分类/商品图片不显示）
  setTimeout(function() {
    $('img.lazy').each(function() {
      var o = $(this).attr('data-original');
      var cur = this.getAttribute('src');
      if (o && (!cur || cur === '' || cur === location.href)) {
        $(this).attr('src', o);
      }
    });
  }, 800);

  if (isModal) {
    if (modalShowType == 1) {
      if (!localStorage.getItem('modal_shown')) {
        $('#myModal').modal('show');
        localStorage.setItem('modal_shown', '1');
      }
    } else {
      $('#myModal').modal('show');
    }
  }

  if (typeof $_GET !== 'undefined' && $_GET.buyok == '1' && $('#pane-query').length) {
    $('#mainTabs .aur-tab[data-pane="pane-query"]').trigger('click');
    setTimeout(function(){ 
      $('#submit_query').click();
      // 清除URL中的buyok参数，避免刷新后订单详情重复弹出
      var u = location.href.replace(/[?&]buyok=1/, '');
      if (u !== location.href) { history.replaceState({}, '', u); }
    }, 500);
  }

  $('#audio-btn').on('click', function() {
    var media = document.getElementById('media');
    if (media) {
      if (media.paused) { media.play(); $(this).removeClass('off'); }
      else { media.pause(); $(this).addClass('off'); }
    }
  });

  var topBtn = $('#aur-top');
  $(window).on('scroll', function() {
    if ($(window).scrollTop() > 400) { topBtn.css('display', 'flex'); }
    else { topBtn.hide(); }
  });
  topBtn.on('click', function() { $('html,body').animate({scrollTop: 0}, 300); });
});

function scrollToTabs() {
  var el = document.getElementById('mainTabs');
  if (el) { el.scrollIntoView({behavior:'smooth', block:'start'}); }
}
</script>
<?php if (function_exists('loadChatWidget')) { loadChatWidget(); } ?>
</body>
</html>
