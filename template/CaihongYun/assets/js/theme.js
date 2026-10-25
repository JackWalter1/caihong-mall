/* ============================================================
   彩虹云商城 · 极光玻璃主题 主题脚本
   负责：明暗主题切换 / 侧边栏开关 / 移动端首页浮层
   下单、查单、购物车等业务逻辑由程序 assets/js/main.js 负责
   ============================================================ */

$(function () {

  /* ---------------- 明暗主题 ---------------- */
  function applyTheme(mode) {
    if (mode === 'dark') {
      document.documentElement.classList.add('fp-dark');
    } else {
      document.documentElement.classList.remove('fp-dark');
    }
  }

  var urlTheme = null;
  try { urlTheme = new URLSearchParams(location.search).get('theme'); } catch (e) {}
  var savedTheme = null;
  try { savedTheme = localStorage.getItem('fp_theme'); } catch (e) {}
  var initTheme = (urlTheme === 'dark' || urlTheme === 'light') ? urlTheme
    : (savedTheme === 'dark' ? 'dark' : 'light');
  applyTheme(initTheme);
  syncThemeIcon();

  function syncThemeIcon() {
    var dark = document.documentElement.classList.contains('fp-dark');
    $('#themeToggle i')
      .removeClass('fa-moon fa-sun-o')
      .addClass(dark ? 'fa-sun-o' : 'fa-moon');
    $('#themeToggle').attr('title', dark ? '切换到亮色' : '切换到暗色');
  }

  $('#themeToggle').on('click', function () {
    var dark = document.documentElement.classList.contains('fp-dark');
    var next = dark ? 'light' : 'dark';
    applyTheme(next);
    try { localStorage.setItem('fp_theme', next); } catch (e) {}
    syncThemeIcon();
  });

  /* ---------------- 侧边栏开关 ---------------- */
  function sidebarToggle() {
    if (window.innerWidth < 1200) {
      $('body').toggleClass('sidebar-open');
    } else {
      $('#page-container').toggleClass('sidebar-mini');
    }
  }

  $('#menuToggle').on('click', sidebarToggle);
  $('#sidebar-overlay').on('click', function () {
    $('body').removeClass('sidebar-open');
  });

  /* ---------------- 移动端首页浮层（桌面端由程序弹窗公告负责） ---------------- */
  if (window.innerWidth <= 480) {
    var notice = $('#myModal .modal-body').html();
    var hpSections = '';
    if (notice && $.trim(notice)) {
      hpSections = '<div class="hp-section">' + notice + '</div>';
    } else {
      hpSections =
        '<div class="hp-section"><div class="hp-title">下单须知</div>' +
        '选择分类 → 选择商品 → 填写信息 → 下单成功，下单后请保存好卡密。</div>' +
        '<div class="hp-section">凭订单号可随时查询订单状态，如遇问题请联系在线客服。</div>';
    }

    var overlay =
      '<div id="home-popup-overlay">' +
        '<div class="hp-wrap">' +
          '<div class="home-popup">' +
            '<div class="hp-header">' +
              '<div class="hp-badge"><i class="fa fa-shopping-bag"></i></div>' +
              '<h2>彩虹云商城</h2>' +
              '<div class="hp-date">自助下单 · 24小时自动发货</div>' +
            '</div>' +
            '<div class="hp-body">' + hpSections + '</div>' +
            '<div class="hp-footer">' +
              '<button class="hp-btn" id="hpGo">立即选购</button>' +
              '<button class="hp-btn-ghost" id="hpLater">稍后再说</button>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
    $('body').append(overlay);
    $('#home-popup-overlay').addClass('show');
    $(document).on('click', '#hpGo', function () {
      $('#home-popup-overlay').removeClass('show');
      if ($('#tab-shop').length) { $('#tab-shop').click(); }
    });
    $(document).on('click', '#hpLater', function () {
      $('#home-popup-overlay').removeClass('show');
    });
  }

});
