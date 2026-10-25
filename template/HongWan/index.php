<?php
if (!defined('IN_CRONLITE')) exit();
// ============================================================
// 红丸俱乐部主题（HongWan）2026版
// 对标 hack.wetcs.cn 白色营销风：导航 + Hero + 数据 + 下单面板 + 优势 + CTA + 页脚
// 完整保留下单/查询全部元素 ID，兼容 assets/js/main.js
// ============================================================
$tmpl_assets = 'template/HongWan/assets/';
$hw_slogan = !empty($conf['description']) ? $conf['description'] : '专业的全自动发卡销售平台，7×24小时无人值守自动发货，为您的商城之旅保驾护航';
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <link rel="dns-prefetch" href="//cdn.staticfile.org">
  <link rel="preconnect" href="//cdn.staticfile.org">
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
  <title><?php echo $hometitle ?></title>
  <meta name="keywords" content="<?php echo $conf['keywords'] ?>">
  <meta name="description" content="<?php echo $conf['description'] ?>">
  <?php if (!empty($conf['favicon'])) { ?>
  <link rel="icon" href="<?php echo $conf['favicon'] ?>" type="image/x-icon"/>
  <?php } ?>
  <link href="<?php echo $cdnpublic ?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic ?>font-awesome/4.7.0/css/font-awesome.min.css?v=fa-cors-20260706" rel="stylesheet"/>
  <link rel="stylesheet" href="<?php echo $tmpl_assets ?>css/home-2026.css?ver=20261017">
  <!--[if lt IE 9]>
    <script src="//cdn.staticfile.org/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>

<!-- 弹出公告模态框 -->
<div class="modal fade" align="left" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $conf['sitename'] ?></h4>
      </div>
      <div class="modal-body">
        <?php echo isset($conf['modal']) ? $conf['modal'] : '' ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">知道啦</button>
      </div>
    </div>
  </div>
</div>

<!-- ================= 顶部白色导航 ================= -->
<header class="hw-header" id="hwHeader">
  <div class="hw-container">
    <div class="hw-nav">
      <a href="./" class="hw-logo">
        <?php if (!empty($logo)) { ?>
        <img src="<?php echo $logo ?>" alt="<?php echo $conf['sitename'] ?>">
        <?php } else { ?>
        <span class="hw-logo-icon"><i class="fa fa-cube"></i></span>
        <span><?php echo $conf['sitename'] ?></span>
        <?php } ?>
      </a>
      <ul class="hw-menu">
        <li><a href="./" class="active">网站首页</a></li>
        <li><a href="#hwShop" onclick="hwGoBuy()">在线下单</a></li>
        <li><a href="#hwShop" onclick="hwGoQuery()">卡密查询</a></li>
        <?php if (!empty($conf['kfqq'])) { ?>
        <li><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes">联系客服</a></li>
        <?php } ?>
        <li><a href="./user/">用户中心</a></li>
      </ul>
      <div class="hw-nav-actions">
        <?php if ($islogin2 == 1) { ?>
        <a href="./user/" class="hw-login-btn">进入后台</a>
        <?php } else { ?>
        <a href="./user/login.php" class="hw-login-btn">登录 / 注册</a>
        <?php } ?>
        <button type="button" class="hw-burger" id="hwBurger" aria-label="菜单"><i class="fa fa-bars"></i></button>
      </div>
    </div>
  </div>
  <div class="hw-mobile-menu" id="hwMobileMenu">
    <a href="./">网站首页</a>
    <a href="#hwShop" onclick="hwGoBuy()">在线下单</a>
    <a href="#hwShop" onclick="hwGoQuery()">卡密查询</a>
    <?php if (!empty($conf['kfqq'])) { ?>
    <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes">联系客服</a>
    <?php } ?>
    <a href="./user/">用户中心</a>
    <?php if ($islogin2 == 1) { ?>
    <a href="./user/" class="hw-login-btn">进入后台</a>
    <?php } else { ?>
    <a href="./user/login.php" class="hw-login-btn">登录 / 注册</a>
    <?php } ?>
  </div>
</header>

<!-- ================= Hero ================= -->
<section class="hw-hero">
  <div class="hw-container">
    <div class="hw-hero-inner">
      <div class="hw-hero-text">
        <h1><?php echo $conf['sitename'] ?><br><em>打造优质全自动发卡平台</em></h1>
        <p><?php echo $hw_slogan ?></p>
        <div class="hw-hero-btns">
          <a href="#hwShop" onclick="hwGoBuy()" class="hw-btn-primary"><i class="fa fa-shopping-cart"></i>&nbsp; 立即下单</a>
          <a href="./user/login.php" class="hw-btn-outline">商户登录</a>
        </div>
      </div>
      <div class="hw-hero-illu">
        <svg viewBox="0 0 520 420" xmlns="http://www.w3.org/2000/svg">
          <!-- 背景云 -->
          <ellipse cx="260" cy="220" rx="230" ry="170" fill="#E3F0FF"/>
          <!-- 显示器 -->
          <rect x="120" y="80" width="270" height="180" rx="14" fill="#ffffff"/>
          <rect x="136" y="98" width="238" height="132" rx="8" fill="#eef5ff"/>
          <polyline points="152,202 190,168 226,184 262,132 300,152 350,106"
                    fill="none" stroke="#2A7AF3" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="350" cy="106" r="7" fill="#2A7AF3"/>
          <rect x="152" y="208" width="70" height="9" rx="4.5" fill="#bcd2f5"/>
          <rect x="152" y="224" width="104" height="7" rx="3.5" fill="#d8e6fa"/>
          <rect x="236" y="260" width="38" height="26" fill="#c9ddfa"/>
          <rect x="186" y="284" width="138" height="12" rx="6" fill="#9dc3f5"/>
          <!-- 购物袋小卡 -->
          <rect x="372" y="140" width="112" height="100" rx="14" fill="#ffffff"/>
          <circle cx="428" cy="180" r="24" fill="#2A7AF3" opacity=".15"/>
          <path d="M418 172 h20 v24 a10 10 0 0 1 -20 0 z M423 172 v-7 a5 5 0 0 1 10 0 v7"
                fill="none" stroke="#2A7AF3" stroke-width="3.5"/>
          <rect x="392" y="214" width="72" height="8" rx="4" fill="#bcd2f5"/>
          <!-- 金币 -->
          <circle cx="132" cy="330" r="30" fill="#FFD66B"/>
          <circle cx="132" cy="330" r="30" fill="none" stroke="#F5B83D" stroke-width="3"/>
          <text x="132" y="340" text-anchor="middle" font-size="28" fill="#C78800" font-weight="bold">￥</text>
          <!-- 对勾圆 -->
          <circle cx="396" cy="320" r="34" fill="#22C55E"/>
          <path d="M380 320 l11 11 20 -22" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
          <!-- 小装饰 -->
          <circle cx="84" cy="120" r="7" fill="#9dc3f5"/>
          <circle cx="462" cy="84" r="9" fill="#b9d5fa"/>
          <circle cx="72" cy="244" r="6" fill="#b9d5fa"/>
          <path d="M452 300 l5 10 11 2 -8 8 2 11 -10 -5 -10 5 2 -11 -8 -8 11 -2 z" fill="#FFD66B"/>
        </svg>
      </div>
    </div>
  </div>
</section>

<!-- ================= 数据统计 ================= -->
<section class="hw-counter">
  <div class="hw-container">
    <div class="hw-counter-card">
      <div class="hw-counter-item">
        <div class="hw-counter-num"><span id="count_money">0</span><small>元</small></div>
        <div class="hw-counter-label">累计交易金额</div>
      </div>
      <div class="hw-counter-item color-two">
        <div class="hw-counter-num"><span id="count_orders">0</span><small>笔</small></div>
        <div class="hw-counter-label">累计完成订单</div>
      </div>
      <div class="hw-counter-item color-three">
        <div class="hw-counter-num"><span id="count_yxts">0</span><small>天</small></div>
        <div class="hw-counter-label">平台稳定运行</div>
      </div>
    </div>
  </div>
</section>

<!-- ================= 下单 / 查询 ================= -->
<section class="hw-section hw-shop" id="hwShop">
  <div class="hw-container">
    <div class="hw-section-title">
      <span class="hw-sub">SELF-SERVICE ORDER</span>
      <h2>自 助 下 单</h2>
      <p>选择商品，填写信息，自动发货，秒级到账</p>
    </div>
    <div class="hw-shop-grid">

      <!-- 左侧主面板 -->
      <div class="hw-shop-main">
        <div class="hw-card">
          <div class="hw-tabs">
            <a href="#onlinebuy" data-toggle="tab" class="active"><i class="fa fa-shopping-cart"></i>在线下单</a>
            <a href="#query" data-toggle="tab" id="tab-query"><i class="fa fa-search"></i>查询订单</a>
          </div>

          <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade in active" id="onlinebuy">
              <div id="goodTypeContents">
                <?php echo isset($conf['alert']) ? $conf['alert'] : ''; ?>
                <?php if (isset($conf['search_open']) && $conf['search_open'] == 1) { ?>
                <div class="form-group" id="display_searchBar">
                  <div class="input-group">
                    <div class="input-group-addon">搜索商品</div>
                    <input type="text" id="searchkw" class="form-control" placeholder="搜索商品" onkeydown="if(event.keyCode==13){$('#doSearch').click()}"/>
                    <div class="input-group-addon" style="border:1px solid #e6edf6;border-left:none;border-radius:0 8px 8px 0;"><span class="fa fa-search" style="cursor:pointer;color:#2A7AF3;" title="搜索" id="doSearch"></span></div>
                  </div>
                </div>
                <?php } ?>
                <?php
                // 查询分类
                $hideclass = false;
                $classhide = explode(',', $siterow['class']);
                $rs = $DB->query("SELECT * FROM pre_class WHERE active=1 AND pid=0 order by sort asc");
                $primary_select = '<option value="0">请选择分类</option>';
                $primary_count = 0;
                while ($res = $rs->fetch()) {
                    if ($is_fenzhan && in_array($res['cid'], $classhide)) continue;
                    $primary_count++;
                    $primary_select .= '<option value="' . $res['cid'] . '" shopimg="' . htmlspecialchars($res['shopimg'] ?? '') . '">' . $res['name'] . '</option>';
                }
                if ($primary_count == 0) $hideclass = true;
                ?>
                <?php if ($hideclass) { ?>
                <div style="text-align:center;padding:50px 20px;background-color:#f8f9fa;border-radius:8px;margin:20px 0;">
                  <h3 style="color:#6c757d;margin-bottom:20px;">暂无分类</h3>
                  <p style="color:#95a5a6;font-size:16px;">目前还没有添加任何商品分类，请稍后再来或联系管理员</p>
                </div>
                <?php } else { ?>
                <div class="form-group" id="display_selectclass">
                  <div class="input-group">
                    <div class="input-group-addon">选择分类</div>
                    <select name="tid" id="cid" class="form-control"><?php echo $primary_select ?></select>
                  </div>
                </div>
                <?php } ?>
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-addon">选择商品</div>
                    <select name="tid" id="tid" class="form-control" onchange="getPoint();"><option value="0">请选择商品</option></select>
                  </div>
                </div>
                <div class="form-group" id="display_price" style="display:none;">
                  <div class="input-group">
                    <div class="input-group-addon">商品价格</div>
                    <input type="text" name="need" id="need" class="form-control" style="color:#0066FF;font-weight:bold;" disabled/>
                  </div>
                </div>
                <div class="form-group" id="display_left" style="display:none;">
                  <div class="input-group">
                    <div class="input-group-addon">库存数量</div>
                    <input type="text" name="leftcount" id="leftcount" class="form-control" disabled/>
                  </div>
                </div>
                <div class="form-group" id="display_num" style="display:none;">
                  <div class="input-group">
                    <div class="input-group-addon">下单份数</div>
                    <span class="input-group-btn" style="width:auto;"><input id="num_min" type="button" class="btn btn-info" style="border-radius:0;height:46px;width:42px;" value="━"></span>
                    <input id="num" name="num" class="form-control" type="number" min="1" value="1" style="border-radius:0;border-left:none;border-right:none;"/>
                    <span class="input-group-btn" style="width:auto;"><input id="num_add" type="button" class="btn btn-info" style="border-radius:0;height:46px;width:42px;" value="✚"></span>
                  </div>
                </div>
                <div id="inputsname"></div>
                <div id="alert_frame" class="alert alert-success" style="display:none;background:linear-gradient(135deg,#0d6b44,#0b5872);border:1px solid rgba(255,255,255,.18);font-weight:bold;color:#fff;box-shadow:0 6px 18px rgba(13,107,68,.35);border-radius:10px;"></div>
                <style>
                  #alert_frame a{color:#ffd76a !important;font-weight:700;text-decoration:underline;text-underline-offset:2px;}
                  #alert_frame a:hover{color:#fff3c4 !important;}
                  .orderContent a{color:#2A7AF3 !important;font-weight:600;text-decoration:underline;}
                </style>
                <?php if (isset($conf['shoppingcart']) && $conf['shoppingcart'] == 1) { ?>
                <div class="btn-group btn-group-justified form-group" style="display:flex;gap:10px;">
                  <a class="btn btn-default" type="button" id="submit_cart_shop" style="flex:1;">加入购物车</a>
                  <a type="submit" id="submit_buy" class="btn btn-primary" style="flex:1;">立即购买</a>
                </div>
                <?php } else { ?>
                <div class="form-group">
                  <input type="submit" id="submit_buy" class="btn btn-primary btn-block" value="立即购买">
                </div>
                <?php } ?>
                <div class="panel-body" id="alert_cart" style="display:none;background:#f6f9fd;border:1px solid #e6edf6;border-radius:10px;margin-bottom:18px;">
                  <i class="fa fa-shopping-cart" style="color:#2A7AF3;"></i>&nbsp;当前购物车已添加<b id="cart_count">0</b>个商品
                  <a class="btn btn-xs btn-danger pull-right" href="javascript:openCart()" style="border-radius:6px;">购物车列表</a>
                </div>
              </div>
              <script async src="<?php echo $tmpl_assets ?>js/ranger-video-20260909.js"></script>
            </div>

            <div class="tab-pane fade" id="query">
              <div class="alert alert-info" style="display:none;"></div>
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-btn" style="width:100px;">
                    <select class="form-control" id="searchtype" style="width:100px;border-radius:8px 0 0 8px;height:46px;"><option value="0">下单账号</option><option value="1">订单号</option></select>
                  </div>
                  <input type="text" name="qq" id="qq3" value="<?php echo isset($qq) ? $qq : '' ?>" class="form-control" style="border-left:none;border-radius:0 8px 8px 0;" placeholder="请输入要查询的内容（留空则显示最新订单）" onkeydown="if(event.keyCode==13){submit_query.click()}" required/>
                </div>
              </div>
              <div id="user-code2" class="form-group">
                <div class="input-group">
                  <div class="input-group-addon">取卡密码</div>
                  <input type="text" name="keypass" id="keypass" value="" class="form-control" style="border-left:none;border-radius:0 8px 8px 0;" placeholder="请输入取卡密码（没有请留空）">
                </div>
              </div>
              <input type="submit" id="submit_query" class="btn btn-primary btn-block" value="立即查询">
              <br/>
              <div id="result2" class="form-group" style="display:none;">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead><tr><th>下单账号</th><th>商品名称</th><th>数量</th><th class="hidden-xs">购买时间</th><th>状态</th><th>操作</th></tr></thead>
                    <tbody id="list"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 右侧：公告 + 快捷入口 -->
      <div class="hw-shop-side">
        <div class="hw-card">
          <h4 class="hw-card-title"><i class="fa fa-bullhorn"></i>站点公告</h4>
          <div id="ann-body"><?php echo isset($conf['anounce']) ? $conf['anounce'] : '' ?></div>
          <div style="text-align:center;margin-top:8px;"><button type="button" id="ann-toggle" aria-expanded="false" style="color:#0066FF;background:none;border:none;cursor:pointer;padding:4px 8px;font-size:14px;">展开全部</button></div>
          <script>(function(){var box=document.getElementById('ann-body');if(!box)return;var items=box.querySelectorAll('li');var actions=box.querySelector('.btn-group');var tg=document.getElementById('ann-toggle');var LIMIT=3;if(items.length<=LIMIT){if(tg)tg.style.display='none';return;}var ex=false;function apply(){for(var i=0;i<items.length;i++){items[i].style.display=(ex||i<LIMIT)?'':'none';}if(actions)actions.style.display=ex?'':'none';if(tg){tg.textContent=ex?'收起':'展开全部';tg.setAttribute('aria-expanded',ex?'true':'false');box.classList.toggle('expanded',ex);}}apply();if(tg){tg.onclick=function(){ex=!ex;apply();};}})();</script>
        </div>

        <div class="hw-card">
          <h4 class="hw-card-title"><i class="fa fa-th-large"></i>快捷入口</h4>
          <div class="hw-quick-links">
            <a href="./user/"><i class="fa fa-user-circle"></i>用户中心</a>
            <?php if (isset($conf['fenzhan_buy']) && $conf['fenzhan_buy'] == 1) { ?>
            <a href="./user/regsite.php"><i class="fa fa-star"></i>开通分站</a>
            <?php } ?>
            <a href="./user/findpwd.php"><i class="fa fa-unlock-alt"></i>找回密码</a>
            <a href="./user/reg.php"><i class="fa fa-user-plus"></i>注册账号</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ================= 平台优势 ================= -->
<section class="hw-section">
  <div class="hw-container">
    <div class="hw-section-title">
      <span class="hw-sub">PLATFORM ADVANTAGE</span>
      <h2>产 品 特 色</h2>
      <p>专业、稳定、安全的虚拟商品自动销售解决方案</p>
    </div>
    <div class="hw-feature-grid">
      <div class="hw-feature">
        <div class="hw-feature-icon"><i class="fa fa-cubes"></i></div>
        <h5>丰富功能</h5>
        <p>提供多种类型的运营营销方案，优惠券、批发价、限购等功能让运营活动更多样</p>
      </div>
      <div class="hw-feature">
        <div class="hw-feature-icon"><i class="fa fa-mobile"></i></div>
        <h5>多端适用</h5>
        <p>移动端 / PC 端各种端口无障碍适配，让用户随时随地轻松购买</p>
      </div>
      <div class="hw-feature">
        <div class="hw-feature-icon"><i class="fa fa-bolt"></i></div>
        <h5>极速开店</h5>
        <p>一分钟轻松注册成为商家，简单配置即可开始售卖盈利</p>
      </div>
      <div class="hw-feature">
        <div class="hw-feature-icon"><i class="fa fa-line-chart"></i></div>
        <h5>数据分析</h5>
        <p>销售数据流向分析，多种详细报表展示，让你实时掌握店铺经营状况</p>
      </div>
      <div class="hw-feature">
        <div class="hw-feature-icon"><i class="fa fa-shield"></i></div>
        <h5>数据安全</h5>
        <p>统一安全认证访问控制，支持加密传输，确保交易与卡密数据安全</p>
      </div>
      <div class="hw-feature">
        <div class="hw-feature-icon"><i class="fa fa-clock-o"></i></div>
        <h5>7×24自动发货</h5>
        <p>全年无休无人值守，下单付款后系统秒级自动发卡，无需人工干预</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= CTA ================= -->
<section class="hw-cta">
  <div class="hw-container">
    <div class="hw-cta-card">
      <div class="hw-cta-text">
        <div class="sub">MERCHANT PROGRAM</div>
        <h3><?php echo $conf['sitename'] ?>助力业务快速增长，期待与您合作</h3>
      </div>
      <?php if (isset($conf['fenzhan_buy']) && $conf['fenzhan_buy'] == 1) { ?>
      <a href="./user/regsite.php" class="hw-cta-btn">开通分站</a>
      <?php } else { ?>
      <a href="./user/reg.php" class="hw-cta-btn">成为商户</a>
      <?php } ?>
    </div>
  </div>
</section>

<!-- ================= 页脚 ================= -->
<footer class="hw-footer">
  <div class="hw-container">
    <div class="hw-footer-grid">
      <div>
        <div class="hw-footer-logo">
          <?php if (!empty($logo)) { ?>
          <img src="<?php echo $logo ?>" alt="<?php echo $conf['sitename'] ?>">
          <?php } else { ?>
          <span class="hw-logo-icon"><i class="fa fa-cube"></i></span>
          <span><?php echo $conf['sitename'] ?></span>
          <?php } ?>
        </div>
        <p>为客户提供全方位价值服务，为您的发卡之旅保驾护航，如有需要请联系客服</p>
        <div class="hw-footer-social">
          <?php if (!empty($conf['kfqq'])) { ?>
          <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes" title="客服QQ"><i class="fa fa-qq"></i></a>
          <?php } ?>
          <a href="./user/" title="用户中心"><i class="fa fa-user"></i></a>
          <a href="#hwShop" onclick="hwGoQuery()" title="订单查询"><i class="fa fa-search"></i></a>
        </div>
      </div>
      <div>
        <h5>快捷导航</h5>
        <ul>
          <li><a href="./">网站首页</a></li>
          <li><a href="#hwShop" onclick="hwGoBuy()">在线下单</a></li>
          <li><a href="#hwShop" onclick="hwGoQuery()">订单查询</a></li>
          <li><a href="./user/">用户中心</a></li>
        </ul>
      </div>
      <div>
        <h5>帮助中心</h5>
        <ul>
          <li><a href="./user/login.php">商户登录</a></li>
          <li><a href="./user/reg.php">注册账号</a></li>
          <li><a href="./user/findpwd.php">找回密码</a></li>
          <?php if (isset($conf['fenzhan_buy']) && $conf['fenzhan_buy'] == 1) { ?>
          <li><a href="./user/regsite.php">开通分站</a></li>
          <?php } ?>
        </ul>
      </div>
      <div>
        <h5>联系方式</h5>
        <ul>
          <?php if (!empty($conf['kfqq'])) { ?>
          <li>客服QQ <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&Site=">&nbsp;<?php echo $conf['kfqq'] ?></a></li>
          <?php } ?>
          <li>工作时间 <span>7×24小时 全年无休</span></li>
          <li>发货方式 <span>付款后系统自动发货</span></li>
        </ul>
      </div>
    </div>
    <div class="hw-footer-bottom">
      CopyRight <i class="fa fa-heart" style="color:#FF5C7A;"></i> <?php echo date("Y") ?> <?php echo $conf['sitename'] ?>
      <?php echo !empty($conf['footer']) ? strip_tags($conf['footer'], '<a><br><span><b>') : 'All rights reserved.' ?>
    </div>
  </div>
</footer>

<!--音乐代码-->
<div id="audio-play" <?php if (empty($conf['musicurl'])) { ?>style="display:none;"<?php } ?>>
  <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')">
    <audio loop="loop" src="<?php echo isset($conf['musicurl']) ? $conf['musicurl'] : '' ?>" id="media" preload="preload"></audio>
  </div>
</div>
<!--音乐代码-->

<script src="//cdn.staticfile.org/jquery/1.12.4/jquery.min.js"></script>
<script src="//cdn.staticfile.org/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="//cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdn.staticfile.org/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>

<script type="text/javascript">
var isModal=<?php echo empty($conf['modal']) ? 'false' : 'true' ?>;
var homepage=true;
var hashsalt=<?php echo $addsalt_js ?>;
$(function () {
  $("img.lazy").lazyload({effect: "fadeIn"});

  // 吸顶阴影
  var header = document.getElementById('hwHeader');
  window.addEventListener('scroll', function(){
    if (window.pageYOffset > 10) header.classList.add('is-fixed');
    else header.classList.remove('is-fixed');
  });

  // 移动端菜单
  $('#hwBurger').on('click', function(){
    $('#hwMobileMenu').toggleClass('open');
  });

  // 下单 / 查询 tab 联动（同时兼容 bootstrap tab 与自定义高亮）
  $('.hw-tabs a').on('shown.bs.tab', function(){
    $('.hw-tabs a').removeClass('active');
    $(this).addClass('active');
  });

  // 购买成功后自动切到查询标签
  function getUrlParam(name){
    var reg=new RegExp("(^|&)"+name+"=([^&]*)(&|$)");
    var r=window.location.search.substr(1).match(reg);
    return r!=null?decodeURIComponent(r[2]):'';
  }
  if(getUrlParam('buyok')=='1'){
    setTimeout(function(){ $('#tab-query').tab('show'); $('#submit_query').click(); },500);
  }
});

function hwGoBuy(){
  var $a=$('.hw-tabs a[href="#onlinebuy"]');
  $a.tab('show');
  $('.hw-tabs a').removeClass('active'); $a.addClass('active');
}
function hwGoQuery(){
  var $a=$('.hw-tabs a[href="#query"]');
  $a.tab('show');
  $('.hw-tabs a').removeClass('active'); $a.addClass('active');
}
</script>

<script src="<?php echo $cdnserver ?>assets/js/main.js?ver=<?php echo VERSION ?>"></script>
<?php include_once SYSTEM_ROOT.'sakura.php'; loadSakuraEffect(); ?>
<?php if (function_exists('loadChatWidget')) loadChatWidget(); ?>
</body>
</html>
