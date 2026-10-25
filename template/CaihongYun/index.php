<?php
if (!defined('IN_CRONLITE')) exit();
// ============================================================
// 彩虹云商城 · 极光玻璃主题（CaihongYun）
// 版式：玻璃侧边栏 + 玻璃顶栏 + 在线下单/查询订单双选项卡
// 业务逻辑全部复用程序 default/shop.inc.php 与 assets/js/main.js
// ============================================================
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
  <title><?php echo $hometitle ?></title>
  <meta name="keywords" content="<?php echo $conf['keywords'] ?>">
  <meta name="description" content="<?php echo $conf['description'] ?>">
  <?php if (!empty($conf['favicon'])) { ?>
  <link rel="icon" href="<?php echo $conf['favicon'] ?>" type="image/x-icon"/>
  <?php } ?>
  <link href="<?php echo $cdnpublic ?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic ?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?php echo $cdnserver ?>assets/simple/css/plugins.css">
  <link rel="stylesheet" href="<?php echo $cdnserver ?>assets/simple/css/main.css">
  <link rel="stylesheet" href="<?php echo $cdnserver ?>assets/simple/css/oneui.css">
  <link rel="stylesheet" href="<?php echo $cdnserver ?>assets/css/common.css?ver=<?php echo VERSION ?>">
  <?php echo $background_css ?>
  <link rel="stylesheet" href="/template/CaihongYun/assets/css/main.css?v=<?php echo VERSION ?>">
  <script>
  // 主题初始化：URL ?theme= 优先，其次本地记录
  (function () {
    try {
      var t = new URLSearchParams(location.search).get("theme");
      var s = null;
      try { s = localStorage.getItem("fp_theme"); } catch (e) {}
      var m = (t === "dark" || t === "light") ? t : (s === "dark" ? "dark" : "light");
      if (m === "dark") { document.documentElement.classList.add("fp-dark"); }
    } catch (e) {}
  })();
  </script>
  <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic ?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic ?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>

<div id="page-container" class="header-fixed-top sidebar-visible-lg-full">

  <!-- 左侧边栏（玻璃抽屉） -->
  <div id="sidebar">
    <div id="sidebar-brand">
      <a href="./">
        <i class="fa fa-shopping-bag"></i>
        <span class="sidebar-nav-mini-hide">彩虹云商城</span>
      </a>
    </div>
    <div id="sidebar-scroll">
      <div class="sidebar-content">
        <ul class="sidebar-nav">
          <li><a href="./" class="active"><i class="fa fa-home sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">网站首页</span></a></li>
          <?php if (isset($conf['fenzhan_buy']) && $conf['fenzhan_buy'] == 1) { ?>
          <li><a href="./user/regsite.php"><i class="fa fa-star sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">开通分站</span></a></li>
          <?php } ?>
          <li><a href="./user/login.php"><i class="fa fa-lock sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">后台登入</span></a></li>
          <?php if (!empty($conf['kfqq'])) { ?>
          <li><a href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $conf['kfqq'] ?>&amp;site=qq&amp;menu=yes" target="_blank"><i class="fa fa-qq sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">客服QQ</span></a></li>
          <?php } ?>
        </ul>
      </div>
    </div>
    <div id="sidebar-extra-info" class="sidebar-content sidebar-nav-mini-hide">
      <div class="text-center">
        <small><?php echo date("Y") ?> <i class="fa fa-heart text-danger"></i> <a href="./">彩虹云商城</a></small>
      </div>
    </div>
  </div>
  <div id="sidebar-overlay"></div>

  <div id="main-container">

    <!-- 顶部导航（玻璃） -->
    <header class="navbar navbar-fixed-top cy-navbar">
      <ul class="nav navbar-nav-custom">
        <li><a href="javascript:void(0)" id="menuToggle">
          <i class="fa fa-bars fa-fw"></i> 菜单
        </a></li>
      </ul>
      <ul class="nav navbar-nav-custom pull-right">
        <li>
          <button class="theme-toggle-btn" id="themeToggle" title="切换主题"><i class="fa fa-moon"></i></button>
        </li>
        <li class="dropdown">
          <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
            <span class="navbar-avatar"><i class="fa fa-user"></i></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-right">
            <?php if ($islogin2 == 1) { ?>
            <li class="dropdown-header text-center"><strong>欢迎回来</strong></li>
            <li><a href="./user/"><i class="fa fa-user fa-fw pull-right"></i> 用户中心</a></li>
            <li><a href="./user/index.php?logout=yes"><i class="fa fa-power-off fa-fw pull-right"></i> 退出登录</a></li>
            <?php } else { ?>
            <li class="dropdown-header text-center"><strong>未登录</strong></li>
            <li><a href="./user/login.php"><i class="fa fa-user fa-fw pull-right"></i> 登录</a></li>
            <li><a href="./user/reg.php"><i class="fa fa-plus-circle fa-fw pull-right"></i> 注册</a></li>
            <?php } ?>
          </ul>
        </li>
      </ul>
    </header>

    <div id="page-content">
      <div class="row">

        <!-- 在线下单 / 查询订单 -->
        <div class="col-sm-12 col-lg-7 col-lg-push-5">
          <div class="block">
            <div class="block-title tabs-title">
              <ul class="nav nav-tabs" data-toggle="tabs">
                <li class="active" id="tab-shop-li"><a href="#shop" id="tab-shop" data-toggle="tab"><i class="fa fa-check-square-o"></i>&nbsp;在线下单</a></li>
                <li><a href="#search" data-toggle="tab"><i class="fa fa-search"></i>&nbsp;查询订单</a></li>
              </ul>
            </div>

            <div class="tab-content" style="padding: 16px 18px;">
              <!-- 在线下单：复用程序标准下单表单 -->
              <div class="tab-pane fade in active" id="shop">
                <?php include TEMPLATE_ROOT.'default/shop.inc.php'; ?>
              </div>

              <!-- 查询订单 -->
              <div class="tab-pane fade in" id="search">
                <?php if (!empty($conf['gg_search'])) { ?>
                <div class="well well-sm"><?php echo $conf['gg_search'] ?></div>
                <?php } ?>
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-btn">
                      <select class="form-control" id="searchtype" style="padding:6px 4px;width:96px;">
                        <option value="0">下单账号</option>
                        <option value="1">订单号</option>
                      </select>
                    </div>
                    <input type="text" name="qq" id="qq3" value="<?php echo isset($qq) ? $qq : '' ?>" class="form-control" placeholder="可不填直接查询" onkeydown="if(event.keyCode==13){submit_query.click()}" required/>
                  </div>
                </div>
                <input type="submit" id="submit_query" class="btn btn-primary btn-block" value="立即查询"><br/>
                <div id="result2" class="form-group" style="display:none;">
                  <center><small><font color="#ff0000">手机用户请左右滑动</font></small></center>
                  <div class="table-responsive">
                    <table class="table table-vcenter table-condensed table-striped">
                      <thead><tr><th>操作</th><th>状态</th><th>下单账号</th><th>商品名称</th><th>数量</th><th class="hidden-xs">购买时间</th></tr></thead>
                      <tbody id="list">
                      </tbody>
                    </table>
                  </div>
                </div><br/>
              </div>
            </div>
          </div>
        </div>

        <!-- 站点公告 -->
        <div class="col-sm-12 col-lg-5 col-lg-pull-7">
          <div class="block">
            <div class="block-title">
              <h4><i class="fa fa-bullhorn"></i>&nbsp;站点公告</h4>
            </div>
            <div style="padding: 6px 18px 12px;line-height:1.8;">
              <?php if (!empty($conf['anounce'])) {
                echo $conf['anounce'];
              } else { ?>
              <ul style="padding-left:18px;margin:0;">
                <li>欢迎来到彩虹云商城，本站商品 24 小时自动发货。</li>
                <li>下单以后一定要自己保存好卡密，凭订单号可随时查询订单状态。</li>
                <li>支持余额支付与主流在线支付方式，支付后未到账请及时联系客服。</li>
                <li>选择分类 → 选择商品 → 填写信息 → 下单成功。</li>
              </ul>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>

      <!-- 友情链接 -->
      <div class="block">
        <div class="block-title">
          <h2><i class="fa fa-chain"></i>&nbsp;友情链接</h2>
        </div>
        <div class="links-row">
          <a class="jump-btn btn1" href="./user/">用户中心入口</a>
          <a class="jump-btn btn2" href="./sup/login.php">供货中心入口</a>
        </div>
      </div>

      <!-- 运行统计 -->
      <div class="block cy-stats">
        <div class="row text-center">
          <div class="col-xs-4">
            <h5><small>订单总数</small><br><a href="javascript:void(0)"><span id="count_orders"></span> 条</a></h5>
          </div>
          <div class="col-xs-4">
            <h5><small>今日订单</small><br><a href="javascript:void(0)"><span id="count_orders2"></span> 条</a></h5>
          </div>
          <div class="col-xs-4">
            <h5><small>运营天数</small><br><a href="javascript:void(0)"><span id="count_yxts"></span> 天</a></h5>
          </div>
        </div>
      </div>

      <!-- 页脚 -->
      <div class="cy-footer">
        <span class="brand">彩虹云商城 <i class="fa fa-heart text-danger"></i> <?php echo date("Y") ?></span><br/>
        <?php echo $conf['footer'] ?>
      </div>
    </div>
  </div>
</div>

<!-- 弹出公告（桌面端） -->
<div class="modal fade" align="left" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">彩虹云商城</h4>
      </div>
      <div class="modal-body">
        <?php if (!empty($conf['modal'])) { echo $conf['modal']; } else { ?>
        下单以后一定要自己保存好卡密<br>
        凭订单号可随时查询订单状态
        <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">知道啦</button>
      </div>
    </div>
  </div>
</div>

<!-- 平台公告弹窗 -->
<div class="modal fade" align="left" id="anounce" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">平台公告</h4>
      </div>
      <div class="modal-body">
        <?php echo isset($conf['anounce']) ? $conf['anounce'] : '' ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $cdnpublic ?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic ?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo $cdnpublic ?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic ?>layer/2.3/layer.js"></script>
<script src="<?php echo $cdnpublic ?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="<?php echo $cdnserver ?>assets/appui/js/app.js"></script>
<script type="text/javascript">
var isModal=<?php echo empty($conf['modal']) ? 'false' : 'true' ?>;
var homepage=true;
var hashsalt=<?php echo $addsalt_js ?>;
$(document).ready(function() {
    if (typeof $.fn.lazyload !== 'undefined') {
        $("img.lazy").lazyload({effect: "fadeIn"});
    } else {
        $("img.lazy").each(function() {
            $(this).attr('src', $(this).data('original')).removeClass('lazy');
        });
    }
});
</script>
<script src="<?php echo $cdnserver ?>assets/js/main.js?ver=<?php echo VERSION ?>"></script>
<script src="/template/CaihongYun/assets/js/theme.js?v=<?php echo VERSION ?>"></script>
</body>
</html>
