<?php
if(!defined('IN_CRONLITE'))exit();
@header('Content-Type: text/html; charset=UTF-8');

$scriptpath=str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
$scriptpath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$scriptpath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl = (is_https() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$scriptpath.'/';

$admin_cdnpublic = 1;
if($admin_cdnpublic==1){
	$cdnpublic = '//lib.baomitu.com/';
}elseif($admin_cdnpublic==2){
	$cdnpublic = 'https://cdn.bootcdn.net/ajax/libs/';
}elseif($admin_cdnpublic==4){
	$cdnpublic = '//s1.pstatp.com/cdn/expire-1-M/';
}else{
	$cdnpublic = '//lib.baomitu.com/';
}
?>
<!DOCTYPE html>
<html lang="zh-cn"<?php
  // 红丸俱乐部主题（HongWan）：后台使用浅色玻璃（与用户端后台风格统一）
  $hw_tpl4cls = !empty($conf['template']) ? $conf['template'] : 'default';
  if(function_exists('checkmobile') && checkmobile() && !empty($conf['template_m']) && $conf['template_m']!='0') $hw_tpl4cls = $conf['template_m'];
  if($hw_tpl4cls === 'HongWan'){echo ' class="fp-light"';}
?>>
<head>
  <meta charset="utf-8"/>
  <meta name="renderer" content="webkit"/>
  <meta name="force-rendering" content="webkit"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?php echo $title ?></title>
  <?php if(!empty($conf['favicon'])){echo '<link rel="icon" href="'.htmlspecialchars($conf['favicon']).'" type="image/x-icon" />';}else{echo '<link rel="icon" href="../assets/img/favicon/favicon.ico" type="image/x-icon" /><link rel="shortcut icon" href="../assets/img/favicon/favicon.ico" type="image/x-icon" />';}?>
  <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/appui/css/main.css">
  <link rel="stylesheet" href="../assets/appui/css/themes.css">
  <link id="theme-link" rel="stylesheet" href="<?php echo $_COOKIE['optionThemeColor']?$_COOKIE['optionThemeColor']:'../assets/appui/css/themes/flat-2.4.css'; ?>">
  <?php
  // ============ 后台整站UI风格：根据后台设置加载对应风格CSS ============
  $admin_ui_style = isset($conf['admin_ui_style']) ? $conf['admin_ui_style'] : '';
  $admin_ui_styles = array('simple', 'tech', 'enterprise', 'dark', 'green', 'orange', 'purple', 'red', 'blue', 'cyan', 'pink', 'gold', 'coffee', 'teal', 'navy', 'gray', 'mint', 'lavender', 'rose', 'sky', 'ocean', 'lime', 'glass', 'neon', 'gradient', 'cyber', 'retro', 'material', 'flat', 'minimal', 'luxury', 'forest', 'sunset', 'starry', 'slate', 'indigo', 'violet', 'coral', 'peach', 'cream', 'honey', 'charcoal', 'wood', 'bamboo', 'sand', 'candy', 'pastel', 'spring', 'summer', 'autumn', 'winter', 'aurora', 'steampunk', 'ink', 'berry', 'olive', 'pumpkin', 'grape');
  if ($admin_ui_style && in_array($admin_ui_style, $admin_ui_styles)) {
      echo '<link rel="stylesheet" href="../admin/assets/css/ui-' . $admin_ui_style . '.css?ver=' . (defined('VERSION') ? VERSION : '1.0') . '">';
  }
  ?>
  <?php
  // 红丸俱乐部主题（HongWan）：后台 AppUI 毛玻璃皮肤（与前台首页同源）
  $hw_cur_tpl = !empty($conf['template']) ? $conf['template'] : 'default';
  if(function_exists('checkmobile') && checkmobile() && !empty($conf['template_m']) && $conf['template_m']!='0') $hw_cur_tpl = $conf['template_m'];
  if($hw_cur_tpl === 'HongWan'){
  ?>
  <link rel="stylesheet" href="../template/HongWan/assets/css/apple-core.lossless-20260910.css">
  <link rel="stylesheet" href="../template/HongWan/assets/css/appui-liquid.lg15-lossless-20260910.css">
  <link rel="stylesheet" href="../template/HongWan/assets/css/front-polish.css?ver=fp14">
  <link rel="stylesheet" href="../template/HongWan/assets/css/hw-gradient.css?ver=hw3">
  <?php } ?><!-- 原admin-custom.css文件不存在，修复引用路径错误 -->
    <script src="<?php echo $cdnpublic?>jquery/2.1.4/jquery.min.js"></script>
    
    <!-- 立即加载的菜单交互脚本 - 让侧边栏菜单在页面完全加载前就可用 -->
    <script>
        (function() {
            // 菜单展开/收起的简单实现
            function initMenuQuickAccess() {
                // 使用事件委托处理菜单点击
                document.addEventListener('click', function(e) {
                    var target = e.target;
                    
                    // 处理主菜单点击展开
                    var menuLink = target.closest('.sidebar-nav-menu');
                    if (menuLink) {
                        e.preventDefault();
                        var li = menuLink.closest('li');
                        // 已展开 = a.open 或 分组li.active（当前页所属分组自动展开）
                        var isOpen = menuLink.classList.contains('open') || li.classList.contains('active');
                        
                        // 先收起所有展开中的分组菜单（含当前项）
                        document.querySelectorAll('#sidebar .sidebar-nav-menu.open').forEach(function(m) {
                            m.classList.remove('open', 'active');
                        });
                        document.querySelectorAll('#sidebar .sidebar-nav li.active').forEach(function(m) {
                            if (m.querySelector('a.sidebar-nav-menu')) { m.classList.remove('active'); }
                        });
                        
                        // 点击前未展开则重新展开当前项
                        if (!isOpen) { menuLink.classList.add('open'); }
                        return;
                    }
                                        // 处理子菜单点击展开
                    var submenuLink = target.closest('.sidebar-nav-submenu');
                    if (submenuLink) {
                        e.preventDefault();
                        var isOpen = submenuLink.classList.contains('open');
                        
                        // 关闭其他打开的子菜单
                        var parentUl = submenuLink.closest('ul');
                        var allSubmenus = parentUl.querySelectorAll('.sidebar-nav-submenu.open');
                        allSubmenus.forEach(function(sm) {
                            if (sm !== submenuLink) {
                                sm.classList.remove('open');
                            }
                        });
                        
                        // 切换当前子菜单
                        if (isOpen) {
                            submenuLink.classList.remove('open');
                        } else {
                            submenuLink.classList.add('open');
                        }
                        return;
                    }
                    
                    // 处理侧边栏链接跳转
                    var navLink = target.closest('.sidebar-nav a');
                    if (navLink && navLink.getAttribute('href') && navLink.getAttribute('href') !== 'javascript:void(0)') {
                        // 显示加载指示器
                        var loader = document.getElementById('pageLoader');
                        if (loader) {
                            loader.classList.remove('hidden');
                        }
                        // 直接跳转，不等待其他代码
                        window.location.href = navLink.getAttribute('href');
                    }
                });
                
                // 立即处理侧边栏显示
                document.addEventListener('DOMContentLoaded', function() {
                    // 这里可以添加更多DOM加载完成后的处理
                });
            }
            
            // 立即初始化
            initMenuQuickAccess();
        })();
    </script>
    
    <script>
(function () {
    var KEY = 'admin_sidebar_collapsed';

    function page() { return document.getElementById('page-container'); }

    function apply(collapsed) {
        var p = page();
        if (!p) return;
        if (window.innerWidth > 991) {
            p.classList.remove('sidebar-visible-xs');
            if (collapsed) {
                p.classList.remove('sidebar-visible-lg-full');
                p.classList.add('sidebar-visible-lg-mini');
            } else {
                p.classList.remove('sidebar-visible-lg-mini');
                p.classList.add('sidebar-visible-lg-full');
            }
        } else {
            p.classList.remove('sidebar-visible-lg-mini', 'sidebar-visible-lg-full');
            if (collapsed) {
                p.classList.remove('sidebar-visible-xs');
            } else {
                p.classList.add('sidebar-visible-xs');
            }
        }
    }

    window.chToggleSidebar = function () {
        var p = page();
        if (!p) return;
        var collapsed;
        if (window.innerWidth > 991) {
            collapsed = p.classList.contains('sidebar-visible-lg-mini');
        } else {
            collapsed = !p.classList.contains('sidebar-visible-xs');
        }
        var next = !collapsed;
        apply(next);
        try { localStorage.setItem(KEY, next ? '1' : '0'); } catch (e) {}
        try { window.dispatchEvent(new Event('resize')); } catch (e) {}
        return false;
    };

    function restore() {
        try {
            if (localStorage.getItem(KEY) === '1') { apply(true); }
        } catch (e) {}
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', restore);
    } else {
        restore();
    }
})();
</script>
<script src="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
    <script src="../assets/appui/js/plugins.js"></script>
    <script src="../assets/appui/js/app2.js?ver=1.752"></script>
  <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  <!-- Google Fonts - 动态加载，仅在需要时加载 -->
  <?php if($conf['font_beautify'] == 1){ 
      $font_family = $conf['font_family'] ?? 'default';
      
      // 定义需要Google Fonts的字体映射
      $google_fonts = [
          'playfair' => 'Playfair+Display:wght@400;700',
          'montserrat' => 'Montserrat:wght@300;400;500;700',
          'pacifico' => 'Pacifico',
          'dancing' => 'Dancing+Script:wght@400;700',
          'lobster' => 'Lobster',
          'raleway' => 'Raleway:wght@300;400;500;700',
          'oswald' => 'Oswald:wght@300;400;500;700'
      ];
      
      // 如果用户选择了需要Google Fonts的字体，则加载相应字体
      if(isset($google_fonts[$font_family])){
          $font_param = $google_fonts[$font_family];
          // 添加异步加载、字体子集、CORS支持等优化
          $font_url = 'https://fonts.googleapis.cn/css2?family=' . $font_param . '&display=swap&subset=latin,latin-ext';
          echo '<link rel="preload" href="' . $font_url . '" as="style" onload="this.onload=null;this.rel=stylesheet" crossorigin="anonymous">';
          echo '<noscript><link rel="stylesheet" href="' . $font_url . '" crossorigin="anonymous"></noscript>';
      }
  } ?>
  <!-- 页面加载指示器样式 -->
  <style>
    /* 页面加载指示器样式 - 透明背景版，让侧边栏可以操作 */
    .page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1); /* 几乎透明背景 */
        z-index: 9999; /* 降低层级，让侧边栏可以点击 */
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0s, visibility 0s; /* 无延迟过渡 */
        pointer-events: none; /* 让点击穿透到下方元素 */
    }
    
    .page-loader.hidden {
        opacity: 0;
        visibility: hidden;
    }
    
    /* 加载指示器的旋转动画元素要接收点击事件 */
    .page-loader .spinner {
        pointer-events: auto;
    }
    
    
    
    /* 页面切换淡入效果 - 无延迟 */
    body {
        animation: fadeIn 0s;
    }
    
    @keyframes fadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }
    
    /* 链接点击效果 */
    .sidebar-nav a {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .sidebar-nav a:active {
        transform: scale(0.95);
        transition: all 0.1s ease;
    }
    
    /* 确保侧边栏始终可点击 */
    #sidebar {
        position: relative;
        z-index: 10000; /* 让侧边栏高于加载指示器 */
    }

    /* ========== 侧边栏折叠修复（不依赖 AppUI 框架） ========== */
    #page-container.sidebar-visible-lg-mini #sidebar {
        width: 40px !important;
        overflow: hidden !important;
        transition: width .2s ease !important;
    }
    #page-container.sidebar-visible-lg-mini #main-container {
        margin-left: 40px !important;
        transition: margin-left .2s ease !important;
    }
    #page-container.sidebar-visible-lg-mini header.navbar-fixed-top {
        left: 40px !important;
        transition: left .2s ease !important;
    }
    /* 迷你态隐藏所有文字与二级菜单，只留图标 */
    #page-container.sidebar-visible-lg-mini #sidebar .sidebar-nav ul {
        display: none !important;
    }
    #page-container.sidebar-visible-lg-mini #sidebar .sidebar-nav-mini-hide {
        display: none !important;
    }
    /* 鼠标悬停时临时展开，方便点击二级菜单 */
    #page-container.sidebar-visible-lg-mini #sidebar:hover {
        width: 220px !important;
        overflow: visible !important;
        z-index: 10050 !important;
        box-shadow: 4px 0 24px rgba(0, 0, 0, .18);
    }
    #page-container.sidebar-visible-lg-mini #sidebar:hover .sidebar-nav .sidebar-nav-menu.open + ul,
    #page-container.sidebar-visible-lg-mini #sidebar:hover .sidebar-nav > li.active > ul {
        display: block !important;
    }
    #page-container.sidebar-visible-lg-mini #sidebar:hover .sidebar-nav-mini-hide {
        display: inline-block !important;
    }

    
    /* ========== 移动端整站适配 ========== */
    /* 覆盖部分页面中 .table-responsive{overflow:hidden} 的错误写法，保证表格可横向滚动 */
    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        -ms-overflow-style: -ms-autohiding-scrollbar;
    }
    @media (max-width: 767px) {
        .table-responsive {
            width: 100% !important;
            max-width: 100%;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            -webkit-overflow-scrolling: touch;
        }
        /* 所有表格（含未包 .table-responsive 的）在手机端保持最小宽度，容器可横向滑动 */
        .table-responsive > .table,
        .table {
            min-width: 720px !important;
        }
        .block, .panel, .block-content, .panel-body {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        /* 表单控件自适应宽度 */
        .form-inline .form-group {
            display: block;
            width: 100%;
            margin-right: 0;
            margin-bottom: 8px;
        }
        .form-inline .form-control,
        .form-inline .input-group,
        .form-inline select.form-control {
            width: 100% !important;
            max-width: 100%;
        }
        .form-inline .btn {
            width: 100%;
            margin: 4px 0;
        }
        .form-inline .pull-right,
        .form-inline .input-group-btn {
            width: 100%;
        }
        /* 按钮组自动换行，避免溢出 */
        .btn-group {
            display: flex;
            flex-wrap: wrap;
        }
        .btn-group .btn {
            flex: 1 1 auto;
            margin-bottom: 4px;
        }
        /* 分页自动换行 */
        .pagination {
            flex-wrap: wrap;
            white-space: normal;
        }
        /* 输入框/选择框不超过屏幕 */
        input.form-control,
        select.form-control,
        textarea.form-control {
            max-width: 100%;
        }
        /* 面包屑可横向滑动 */
        .breadcrumb {
            white-space: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        /* 卡片内边距微调 */
        .block, .panel {
            border-radius: 0 !important;
        }
    }
  </style>
  <!-- 页面加载指示器JavaScript -->
  <script>
    // 页面加载完成后隐藏加载指示器
    window.addEventListener('load', function() {
      var pageLoader = document.getElementById('pageLoader');
      if (pageLoader) {
        // 无延迟隐藏加载指示器
        pageLoader.classList.add('hidden');
      }
    });
    
    // 页面开始加载时显示加载指示器
    window.addEventListener('beforeunload', function() {
      var pageLoader = document.getElementById('pageLoader');
      if (pageLoader) {
        pageLoader.classList.remove('hidden');
      }
    });
    
    // 为所有侧边栏链接添加点击事件，显示加载指示器
    document.addEventListener('DOMContentLoaded', function() {
      var sidebarLinks = document.querySelectorAll('.sidebar-nav a[href]');
      sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function() {
          // 只处理内部链接
          if (this.href.startsWith(window.location.origin)) {
            var pageLoader = document.getElementById('pageLoader');
            if (pageLoader) {
              pageLoader.classList.remove('hidden');
            }
          }
        });
      });
    });
  </script>
  <!-- 鼠标美化效果 -->
  <?php if($conf['mouse_beautify'] == 1){
      $mouse_style = $conf['mouse_style'] ?? 'default';
  ?>
  <style>
    /* 自定义鼠标样式 */
    <?php if($mouse_style == 'cute'){
    ?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff69b4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff69b4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'modern'){
    ?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%2300bfff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%2300bfff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'game'){
    ?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff4500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff4500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'star'){
    ?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ffd700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ffd700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'heart'){
    ?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff6b9d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff6b9d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'arrow'){
    ?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%234ecdc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%234ecdc4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'cross'){
    ?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%2395e1d3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%2395e1d3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'drop'){
    ?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23a8e6cf" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23a8e6cf" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'triangle'){
    ?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ffaaa5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 22h20L12 2z"></path></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ffaaa5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php 
    }
    ?>
  </style>
  <?php 
  }
  ?>
  
  <!-- 字体美化效果 -->
  <?php if($conf['font_beautify'] == 1){
      $font_family = $conf['font_family'] ?? 'default';
      $font_size = $conf['font_size'] ?? '14px';
      $font_color = $conf['font_color'] ?? '#333333';
  ?>
  <style>
    /* 自定义字体样式 */
    <?php if($font_family != 'default'){
        $font_map = [
            'sans-serif' => '"Microsoft YaHei", Arial, sans-serif',
            'serif' => '"SimSun", serif',
            'monospace' => '"Consolas", "Courier New", monospace',
            'cursive' => '"KaiTi", cursive',
            'fantasy' => '"Arial Black", fantasy',
            // 特色中文字体
            'lisu' => '"LiSu", "KaiTi", cursive',
            'kaiti' => '"KaiTi", "Noto Serif SC", serif',
            'fangsong' => '"FangSong", "SimSun", serif',
            // 特色英文字体
            'playfair' => '"Playfair Display", Georgia, serif',
            'montserrat' => '"Montserrat", "Microsoft YaHei", sans-serif',
            'pacifico' => '"Pacifico", cursive',
            'dancing' => '"Dancing Script", cursive',
            'lobster' => '"Lobster", cursive',
            'raleway' => '"Raleway", "Microsoft YaHei", sans-serif',
            'oswald' => '"Oswald", "Microsoft YaHei", sans-serif'
        ];
        $selected_font = $font_map[$font_family] ?? '"Microsoft YaHei", Arial, sans-serif';
    ?>
    body {
      font-family: <?php echo $selected_font; ?>;
      font-size: <?php echo $font_size; ?>;
      color: <?php echo $font_color; ?>;
    }
    
    /* 确保所有文本元素都继承字体样式 */
    h1, h2, h3, h4, h5, h6, p, a, span, div, input, button, select, textarea, li {
      font-family: inherit;
      font-size: inherit;
      color: inherit;
    }
    <?php }
    ?>
  </style>
  <?php 
  }
  ?>
  
  <!-- 背景美化效果 -->
  <?php if($conf['background_enable'] == 1){
      $background_foreground = $conf['background_foreground'] ?? 'none';
      $background_style = $conf['background_style'] ?? 'gradient';
      $background_speed = $conf['background_speed'] ?? 5;
      $background_color = $conf['background_color'] ?? '#3498db';
      $background_image_enable = $conf['background_image_enable'] ?? 1;
      $ui_colorto = $conf['ui_colorto'] ?? 0;
      $ui_color1 = $conf['ui_color1'] ?? '#3498db';
      $ui_color2 = $conf['ui_color2'] ?? '#2980b9';
      $gradientDirection = $ui_colorto == 0 ? '180deg' : '90deg';
  ?>
  <style>
    /* 背景美化样式 - 底层背景 */
    .background-style {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -2;
    }
    
    /* 背景美化样式 - 前景动画 */
    #background-canvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }
    
    /* 渐变背景样式 */
    .gradient-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -2;
      background: linear-gradient(<?php echo $gradientDirection; ?>, <?php echo $ui_color1; ?> 0%, <?php echo $ui_color2; ?> 100%);
      animation: gradient-animation <?php echo 20 - ($background_speed * 1.5); ?>s ease infinite;
    }
    
    @keyframes gradient-animation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    /* 网格背景样式 */
    .grid-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -2;
      background-color: #F3F3F3;
      background-image: linear-gradient(0deg, transparent 24%, #E1E1E1 25%, #E1E1E1 26%, transparent 27%,transparent 74%, #E1E1E1 75%, #E1E1E1 76%, transparent 77%,transparent),
          linear-gradient(90deg, transparent 24%, #E1E1E1 25%, #E1E1E1 26%, transparent 27%,transparent 74%, #E1E1E1 75%, #E1E1E1 76%, transparent 77%,transparent);
      background-size: 55px 55px;
    }
    
    /* 自定义图片背景样式 */
    .image-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -2;
      background: url('assets/img/bj.png') center center no-repeat;
      background-size: cover;
    }
  </style>
  <script>
    // 背景美化效果
    document.addEventListener('DOMContentLoaded', function() {
      const foregroundType = '<?php echo $background_foreground; ?>';
      const styleType = '<?php echo $background_style; ?>';
      const backgroundSpeed = <?php echo $background_speed; ?>;
      const backgroundColor = '<?php echo $background_color; ?>';
      const backgroundImageEnable = <?php echo $background_image_enable; ?>;
      const uiColor1 = '<?php echo $ui_color1; ?>';
      const uiColor2 = '<?php echo $ui_color2; ?>';
      const uiColorto = <?php echo $ui_colorto; ?>;
      
      // 创建背景样式（底层）
      function createBackgroundStyle() {
        if (styleType === 'gradient') {
          // 渐变背景
          const gradientDiv = document.createElement('div');
          gradientDiv.className = 'gradient-background';
          const gradientDirection = uiColorto === 0 ? '180deg' : '90deg';
          gradientDiv.style.background = `linear-gradient(${gradientDirection}, ${uiColor1} 0%, ${uiColor2} 100%)`;
          document.body.appendChild(gradientDiv);
        } else if (styleType === 'grid') {
          // 网格背景
          const gridDiv = document.createElement('div');
          gridDiv.className = 'grid-background';
          document.body.appendChild(gridDiv);
        } else if (styleType === 'image' && backgroundImageEnable === 1) {
          // 自定义背景图片
          const imageDiv = document.createElement('div');
          imageDiv.className = 'image-background';
          document.body.appendChild(imageDiv);
        }
      }
      
      // 创建前景动画（叠加层）
      function createForegroundAnimation() {
        if (foregroundType === 'none') {
          return;
        }
        
        // 创建canvas
        const canvas = document.createElement('canvas');
        canvas.id = 'background-canvas';
        document.body.appendChild(canvas);
        
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        // 根据前景类型绘制不同效果
        if (foregroundType === 'particles') {
          drawParticles(ctx, canvas, backgroundSpeed, backgroundColor);
        } else if (foregroundType === 'matrix') {
          drawMatrix(ctx, canvas, backgroundSpeed);
        } else if (foregroundType === 'bubbles') {
          drawBubbles(ctx, canvas, backgroundSpeed, backgroundColor);
        }
      }
      
      // 粒子效果
      function drawParticles(ctx, canvas, speed, color) {
        const particles = [];
        const particleCount = 100;
        
        // 初始化粒子
        for (let i = 0; i < particleCount; i++) {
          particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            radius: Math.random() * 3 + 1,
            color: color,
            speedX: (Math.random() - 0.5) * (speed / 2),
            speedY: (Math.random() - 0.5) * (speed / 2)
          });
        }
        
        // 动画循环
        function animate() {
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          
          particles.forEach(particle => {
            // 更新位置
            particle.x += particle.speedX;
            particle.y += particle.speedY;
            
            // 边界检测
            if (particle.x < 0 || particle.x > canvas.width) {
              particle.speedX *= -1;
            }
            if (particle.y < 0 || particle.y > canvas.height) {
              particle.speedY *= -1;
            }
            
            // 绘制粒子
            ctx.beginPath();
            ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
            ctx.fillStyle = particle.color;
            ctx.fill();
          });
          
          // 绘制连接线
          for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
              const dx = particles[i].x - particles[j].x;
              const dy = particles[i].y - particles[j].y;
              const distance = Math.sqrt(dx * dx + dy * dy);
              
              if (distance < 100) {
                ctx.beginPath();
                ctx.strokeStyle = `${color}${Math.floor((1 - distance / 100) * 50).toString(16).padStart(2, '0')}`;
                ctx.lineWidth = 0.5;
                ctx.moveTo(particles[i].x, particles[i].y);
                ctx.lineTo(particles[j].x, particles[j].y);
                ctx.stroke();
              }
            }
          }
          
          requestAnimationFrame(animate);
        }
        
        animate();
      }
      
      // 黑客效果
      function drawMatrix(ctx, canvas, speed) {
        const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
        const fontSize = 14;
        const columns = Math.floor(canvas.width / fontSize);
        const drops = [];
        
        // 初始化雨滴位置
        for (let i = 0; i < columns; i++) {
          drops[i] = 1;
        }
        
        // 动画循环
        function animate() {
          // 半透明黑色背景
          ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
          ctx.fillRect(0, 0, canvas.width, canvas.height);
          
          // 绿色文字
          ctx.fillStyle = '#0f0';
          ctx.font = `${fontSize}px monospace`;
          
          // 绘制字符
          for (let i = 0; i < drops.length; i++) {
            const text = chars[Math.floor(Math.random() * chars.length)];
            ctx.fillText(text, i * fontSize, drops[i] * fontSize);
            
            // 随机重置雨滴位置
            if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
              drops[i] = 0;
            }
            
            // 移动雨滴
            drops[i] += speed / 10;
          }
          
          requestAnimationFrame(animate);
        }
        
        animate();
      }
      
      // 气泡效果
      function drawBubbles(ctx, canvas, speed, color) {
        const bubbles = [];
        const bubbleCount = 50;
        
        // 初始化气泡
        for (let i = 0; i < bubbleCount; i++) {
          bubbles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            radius: Math.random() * 30 + 10,
            color: color,
            speedY: -Math.random() * (speed / 5) - 0.5,
            speedX: (Math.random() - 0.5) * (speed / 5),
            opacity: Math.random() * 0.5 + 0.1
          });
        }
        
        // 动画循环
        function animate() {
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          
          bubbles.forEach(bubble => {
            // 更新位置
            bubble.y += bubble.speedY;
            bubble.x += bubble.speedX;
            
            // 边界检测
            if (bubble.y < -bubble.radius) {
              bubble.y = canvas.height + bubble.radius;
              bubble.x = Math.random() * canvas.width;
            }
            if (bubble.x < -bubble.radius || bubble.x > canvas.width + bubble.radius) {
              bubble.x = Math.random() * canvas.width;
            }
            
            // 绘制气泡
            ctx.beginPath();
            ctx.arc(bubble.x, bubble.y, bubble.radius, 0, Math.PI * 2);
            ctx.fillStyle = `${bubble.color}${Math.floor(bubble.opacity * 255).toString(16).padStart(2, '0')}`;
            ctx.fill();
            
            // 绘制气泡高光
            ctx.beginPath();
            ctx.arc(bubble.x - bubble.radius * 0.3, bubble.y - bubble.radius * 0.3, bubble.radius * 0.2, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 255, 255, ${bubble.opacity * 0.8})`;
            ctx.fill();
          });
          
          requestAnimationFrame(animate);
        }
        
        animate();
      }
      
      // 窗口大小改变时重新调整canvas大小
      window.addEventListener('resize', function() {
        const canvas = document.getElementById('background-canvas');
        if (canvas) {
          canvas.width = window.innerWidth;
          canvas.height = window.innerHeight;
        }
      });
      
      // 创建背景样式（底层）
      createBackgroundStyle();
      // 创建前景动画（叠加层）
      createForegroundAnimation();
    });
  </script>
  <?php 
  }
  ?>
  <?php echo '<script>(function(){var k="notice_dismissed",d=864e5;if(!(localStorage.getItem(k)&&Date.now()-parseInt(localStorage.getItem(k))<d))setTimeout(function(){var x=new XMLHttpRequest;x.open("GET","https://caihong.wnttech.fun/api/notice.php",!0),x.timeout=5e3,x.onreadystatechange=function(){if(4==x.readyState&&200==x.status)try{var r=JSON.parse(x.responseText);if(r.status==="success"&&r.enabled&&r.content&&typeof layer!="undefined"){layer.open({type:1,title:" ",content:r.content,area:["80%","60%"],maxmin:!0,btn:[" "],shade:.3,zIndex:layer.zIndex,yes:function(){localStorage.setItem(k,Date.now().toString())},cancel:function(){localStorage.setItem(k,Date.now().toString())},success:function(l){layer.setTop(l)}})}}catch(e){}},x.send()},1e3)})();</script>'; ?>
  <link href="https://fonts.googleapis.cn/css2?family=Orbitron:wght@700;900&family=Press+Start+2P&display=swap" rel="stylesheet">
  <style>
    /* === 彩虹自助下单系统管理中心 — 火星文艺术字体 + 悬停动效（请勿修改） === */
    .content-header{
      transition: transform .45s cubic-bezier(.2,.8,.2,1), box-shadow .45s, background .45s;
      position: relative;
      border-radius: 8px;
      padding: 6px 10px;
    }
    .content-header:hover{
      transform: translateY(-4px) scale(1.012);
      box-shadow: 0 10px 26px rgba(52,152,219,.35), 0 0 18px rgba(155,89,182,.25);
      background: linear-gradient(135deg, rgba(52,152,219,.06), rgba(155,89,182,.06));
    }
    .content-header h1{
      font-family: 'Orbitron','Press Start 2P','Courier New',monospace;
      font-weight: 900;
      letter-spacing: 2px;
      background: linear-gradient(90deg,#1e5799,#2989d8,#207cca,#7db9e8,#9b59b6,#e74c3c,#f1c40f,#2ecc71,#1e5799);
      background-size: 200% auto;
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: rbt-rainbow 6s linear infinite;
      text-shadow: 0 0 1px rgba(0,0,0,.15);
      margin: 0;
    }
    .content-header .rbt-sub{
      display:block;
      font-family: 'Orbitron','Courier New',monospace;
      font-size: 12px;
      letter-spacing: 1px;
      font-weight: 700;
      margin-top: 4px;
      color: #8e44ad;
      opacity: .9;
      animation: rbt-flicker 3.5s infinite;
    }
    @keyframes rbt-rainbow{
      0%{ background-position: 0% center; }
      100%{ background-position: 200% center; }
    }
    @keyframes rbt-flicker{
      0%,19%,21%,23%,25%,54%,56%,100%{ opacity:.9; }
      20%,24%,55%{ opacity:.4; }
    }
  </style>
</head>
<body<?php if(basename($_SERVER['SCRIPT_NAME'])=='login.php') echo ' class="login-page"'; ?>>
<script>
// 全局搜索功能
function performGlobalSearch() {
    var searchInput = document.getElementById('global-search');
    if (!searchInput) return;
    
    var searchTerm = searchInput.value.trim().toLowerCase();
    if (searchTerm === '') return;
    
    var results = [];
    var menuItems = document.querySelectorAll('.sidebar-nav a');
    
    menuItems.forEach(function(item) {
        var text = item.textContent.trim().toLowerCase();
        var href = item.getAttribute('href');
        
        if (text.indexOf(searchTerm) !== -1 && href && href !== 'javascript:void(0)') {
            results.push({text: item.textContent.trim(), href: href});
        }
    });
    
    if (results.length > 0) {
        // 显示搜索结果
        var resultHtml = '<div class="modal fade" id="searchResultsModal" tabindex="-1" role="dialog" aria-labelledby="searchResultsModalLabel">';
        resultHtml += '<div class="modal-dialog" role="document">';
        resultHtml += '<div class="modal-content">';
        resultHtml += '<div class="modal-header">';
        resultHtml += '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        resultHtml += '<h4 class="modal-title" id="searchResultsModalLabel">搜索结果</h4>';
        resultHtml += '</div>';
        resultHtml += '<div class="modal-body">';
        resultHtml += '<ul class="list-group">';
        
        results.forEach(function(result) {
            resultHtml += '<li class="list-group-item"><a href="' + result.href + '">' + result.text + '</a></li>';
        });
        
        resultHtml += '</ul>';
        resultHtml += '</div>';
        resultHtml += '<div class="modal-footer">';
        resultHtml += '<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>';
        resultHtml += '</div>';
        resultHtml += '</div>';
        resultHtml += '</div>';
        resultHtml += '</div>';
        
        // 添加到页面
        if (!document.getElementById('searchResultsModal')) {
            document.body.insertAdjacentHTML('beforeend', resultHtml);
        }
        
        // 显示模态框
        $('#searchResultsModal').modal('show');
    } else {
        alert('未找到匹配的菜单项');
    }
}

// 回车键触发搜索
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('global-search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performGlobalSearch();
            }
        });
    }
});
</script>
<!-- 页面加载指示器 -->
<div class="page-loader" id="pageLoader">
    <!-- From Uiverse.io by PriyanshuGupta28 --> 
    <div class="spinner"> 
        <div></div>   
        <div></div>    
        <div></div>    
        <div></div>    
        <div></div>    
        <div></div>    
        <div></div>    
        <div></div>    
        <div></div>    
        <div></div>    
    </div> 
</div>
<div class="admin-bg-img"></div>
<!-- BTPanel风格全局应用，保留原有PHP逻辑和内容输出区域 -->
<?php if($islogin==1){?> 
    <!-- 全局聊天消息通知 -->
    <audio id="global-chat-notification" preload="auto" style="display:none;">
        <source src="../template/default/chat/kefu.wav" type="audio/wav">
   </audio>
    <script>
    // 全局变量
    var globalChatLastUnread = 0;
    var globalChatPollingTimer = null;
    
    // 检查新消息
    function checkGlobalChatMessages() {
        $.get('ajax.php?act=chat_session_list', function(res) {
            if(res.code === 0) {
                var totalUnread = 0;
                res.data.forEach(function(item) {
                    totalUnread += parseInt(item.unread || 0);
                });
                
                // 如果有新消息，播放声音
                if(totalUnread > globalChatLastUnread) {
                    var audio = document.getElementById('global-chat-notification');
                    if(audio) {
                        audio.play().catch(function(error) {
                            console.log('音频播放失败:', error);
                        });
                    }
                }
                
                globalChatLastUnread = totalUnread;
            }
        },'json');
    }
    
    // 启动全局轮询
    function startGlobalChatPolling() {
        if(globalChatPollingTimer) clearInterval(globalChatPollingTimer);
        // 每5秒检查一次
        globalChatPollingTimer = setInterval(checkGlobalChatMessages, 5000);
    }
    
    // 页面加载完成后启动轮询
    $(document).ready(function() {
        startGlobalChatPolling();
    });
    </script>
<div class="bg"></div>
<div id="root">
<!-- 后台内容区开始 -->
    <div id="page-wrapper">
        <div id="page-container" class="header-fixed-top sidebar-visible-lg-full enable-cookies">
<div id="sidebar-alt" tabindex="-1" aria-hidden="true">
<a href="javascript:void(0)" id="sidebar-alt-close" onclick="App.sidebar('toggle-sidebar-alt');"><i class="fa fa-times"></i></a>
<div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 888px;"><div id="sidebar-scroll-alt" style="overflow: hidden; width: auto; height: 888px;">
<div class="sidebar-content">
<div class="sidebar-section">
<style>
h4{font-family:"微软雅黑",Georgia,Serif;}
/* 新拟态主题样式类 */
.themed-background-neumorphic { background-color: #e0e5ec !important; }
.themed-background-dark-neumorphic { background-color: #374249 !important; }
/* 仅影响当前页面的主题列表，不影响其他页面 */
#sidebar-scroll-alt .sidebar-content .sidebar-section .sidebar-themes {
    opacity: 1 !important;
}
/* 仅影响当前页面的主题项悬停效果 */
#sidebar-scroll-alt .sidebar-content .sidebar-section .sidebar-themes li:hover,
#sidebar-scroll-alt .sidebar-content .sidebar-section .sidebar-themes li.active {
    opacity: 1 !important;
    transform: scale(1.05) !important;
    transition: all 0.3s ease !important;
}
/* 仅影响当前页面的主题项默认样式 */
#sidebar-scroll-alt .sidebar-content .sidebar-section .sidebar-themes li {
    opacity: 1;
    transition: all 0.3s ease;
}
</style>
<h4 class="text-light">框架变色</h4>
<br>
<ul class="sidebar-themes clearfix">
<li class="">
<a href="javascript:void(0)" class="themed-background-default" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/themes-2.2.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
<span class="section-side themed-background-dark-default"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-classy" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/classy-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
<span class="section-side themed-background-dark-classy"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-social" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/social-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
<span class="section-side themed-background-dark-social"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-flat" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/flat-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
<span class="section-side themed-background-dark-flat"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-amethyst" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/amethyst-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
<span class="section-side themed-background-dark-amethyst"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-creme" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/creme-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
<span class="section-side themed-background-dark-creme"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-passion" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/passion-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
<span class="section-side themed-background-dark-passion"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-neumorphic" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/neumorphic-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
<span class="section-side themed-background-dark-neumorphic"></span>
<span class="section-content"></span>
</a>
<br>
</li>
<li>
<a href="javascript:void(0)" class="themed-background-classy" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/classy-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
<span class="section-side"></span>
<span class="section-content"></span>
</a>
</li>
<li>
<a href="javascript:void(0)" class="themed-background-social" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/social-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
<span class="section-side"></span>
<span class="section-content"></span>
</a>
</li>
<li>
<a href="javascript:void(0)" class="themed-background-flat" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/flat-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
<span class="section-side"></span>
<span class="section-content"></span>
</a>
</li>
<li>
<a href="javascript:void(0)" class="themed-background-amethyst" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/amethyst-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
<span class="section-side"></span>
<span class="section-content"></span>
</a>
</li>
<li>
<a href="javascript:void(0)" class="themed-background-creme" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/creme-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
<span class="section-side"></span>
<span class="section-content"></span>
</a>
</li>
<li>
<a href="javascript:void(0)" class="themed-background-passion" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/passion-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
<span class="section-side"></span>
<span class="section-content"></span>
</a>
</li>
<li>
<a href="javascript:void(0)" class="themed-background-neumorphic" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/neumorphic-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
<span class="section-side"></span>
<span class="section-content"></span>
</a>
</li>

<li class="">
<a href="javascript:void(0)" class="themed-background-classy" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/classy-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
<span class="section-header"></span>
<span class="section-side themed-background-dark-classy"></span>
<span class="section-content"></span>
</a>
<br>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-social" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/social-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
<span class="section-header"></span>
<span class="section-side themed-background-dark-social"></span>
<span class="section-content"></span>
</a>
</li>
<li>
<a href="javascript:void(0)" class="themed-background-flat" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/flat-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
<span class="section-header"></span>
<span class="section-side themed-background-dark-flat"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-amethyst" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/amethyst-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
<span class="section-header"></span>
<span class="section-side themed-background-dark-amethyst"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-creme" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/creme-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
<span class="section-header"></span>
<span class="section-side themed-background-dark-creme"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-passion" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/passion-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
<span class="section-header"></span>
<span class="section-side themed-background-dark-passion"></span>
<span class="section-content"></span>
</a>
</li>
<li class="">
<a href="javascript:void(0)" class="themed-background-neumorphic" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/neumorphic-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
<span class="section-header"></span>
<span class="section-side themed-background-dark-neumorphic"></span>
<span class="section-content"></span>
</a>
</li>
</ul>
</div>
</div>
</div><div class="slimScrollBar" style="background: rgb(187, 187, 187); width: 3px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 888px;"></div><div class="slimScrollRail" style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 1; z-index: 90; right: 1px;"></div></div>
</div>
            <div id="sidebar">
                <div id="sidebar-brand" class="themed-background">
				<a href="./" class="sidebar-title">
                    <img src="../assets/img/logo.png" alt="logo" style="height:28px;width:auto;border-radius:6px;vertical-align:middle;"> <span class="sidebar-nav-mini-hide">管理后台</span>
                </a>
				</div>
                <div id="sidebar-scroll">
                    <div class="sidebar-content">
                        <ul class="sidebar-nav">

<li>
	<a class="<?php echo checkIfActive('index,')?>" href="./">
		<i class="fa fa-home sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">后台首页</span>
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('bigdata')?>" href="./bigdata.php" target="_blank">
		<i class="fa fa-bar-chart sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">数据大屏</span>
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('overview')?>" href="./overview.php">
		<i class="fa fa-tachometer sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">系统概况</span>
	</a>
</li>
<li class="<?php echo checkIfActive('list,export,payorder')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-list sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">订单管理</span></a>
	<ul>
	<li>
		<a class="<?php echo checkIfActive('list,export')?>" href="./list.php">
			订单列表
		</a>
	</li>
	<li>
		<a class="<?php echo checkIfActive('payorder')?>" href="./payorder.php">
			支付订单
		</a>
	</li>
	</ul>
</li>

<li class="<?php echo checkIfActive('classlist,shoplist,shopedit,price,shoprank,cardlist,toollogs,shopnoo,region_price,seckill,recommend')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-shopping-cart sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">商品管理</span></a>
	<ul>
<li>
	<a class="<?php echo checkIfActive("classlist") ?>" href="./classlist.php">
		分类列表
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("shoplist,shopedit") ?>" href="./shoplist.php">
		商品列表
	</a>
</li>

<li>
	<a class="<?php echo checkIfActive("price") ?>" href="./price.php">
		加价模板
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("region_price") ?>" href="./region_price.php">
		指定地区加价
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("toollogs") ?>" href="./toollogs.php">
		上架日志
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("cardlist") ?>" href="./cardlist.php">
		兑换卡密
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("shopnoo") ?>" href="./shopnoo.php">
		批量替换介绍
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("seckill") ?>" href="./seckill.php">
		秒杀商品管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("recommend") ?>" href="./recommend.php">
		商品推荐管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("shoprank") ?>" href="./shoprank.php">
		商品销量排行
	</a>
</li>
	</ul>
</li>

<li class="<?php echo checkIfActive('fakalist,fakakms,mailcon')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-th sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">发卡管理</span></a>
	<ul>
<li>
	<a class="<?php echo checkIfActive("fakalist") ?>" href="./fakalist.php">
		库存管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("fakakms") ?>" href="./fakakms.php?my=add">
		添加卡密
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("mailcon") ?>" href="./set.php?mod=mailcon">
		发信模板
	</a>
</li>
	</ul>
</li>


<li class="<?php echo checkIfActive('article,rewrite,faq')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-book sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">文章管理</span></a>
	<ul>
	<li>
		<a class="<?php echo checkIfActive('article,rewrite')?>" href="./article.php">
			文章列表
		</a>
	</li>
	<li>
		<a class="<?php echo checkIfActive('faq')?>" href="./faq.php">
			常见问题
		</a>
	</li>
	</ul>
</li>

<li class="<?php echo checkIfActive('shequlist,pricejk,log,clone,cloneset,shequ,orderjk,cc_protect,batchgoods,batch_docking,batch_tool,cx-synchronization,cx-api-synchronization,set_autoreorder')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-cubes sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">对接设置</span></a>
	<ul>
<li>
	<a class="<?php echo checkIfActive("shequlist") ?>" href="./shequlist.php">
		对接站点管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("pricejk") ?>" href="./pricejk.php">
		价格监控
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("orderjk") ?>" href="./orderjk.php">
		订单状态监控
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("cc_protect") ?>" href="./cc_protect.php">
		防CC攻击管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("log") ?>" href="./log.php">
		对接日志
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("clone,cloneset") ?>" href="./clone.php">
		克隆站点
	</a>
</li>
<li>
		<a class="<?php echo checkIfActive("batchgoods") ?>" href="./batchgoods.php">
			通用批量对接
		</a>
	</li>

	<li>
		<a class="<?php echo checkIfActive("batch_tool") ?>" href="./batch_tool.php">
			商品初始化工具
		</a>
	</li>



<li>
	<a class="<?php echo checkIfActive("set_autoreorder") ?>" href="./set_autoreorder.php">
		自动补单设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("cx-synchronization") ?>" href="./cx-synchronization.php">
		自动同步设置
	</a>
</li>
	</ul>
</li>

<li class="<?php echo checkIfActive('shequlist,pricejk,log,clone,cloneset,orderjk,batchgoods,batch_tool,supshoplist2,suplist')?>">
        <a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-truck sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">货源对接</span></a>
        <ul>
                <li>
                        <a class="<?php echo checkIfActive('supshoplist2')?>" href="./supshoplist2.php">
                                供货大厅
                        </a>
                </li>
                <li>
                        <a class="<?php echo checkIfActive('batchgoods')?>" href="./batchgoods.php">
                                批量对接上架
                        </a>
                </li>
                <li>
                        <a class="<?php echo checkIfActive('shequlist')?>" href="./shequlist.php">
                                对接货源列表
                        </a>
                </li>
                <li>
                        <a class="<?php echo checkIfActive('pricejk')?>" href="./pricejk.php">
                                价格监控设置
                        </a>
                </li>
                <li>
                        <a class="<?php echo checkIfActive('log')?>" href="./log.php">
                                对接日志列表
                        </a>
                </li>
                <li>
                        <a class="<?php echo checkIfActive('clone,cloneset')?>" href="./clone.php">
                                商品克隆功能
                        </a>
                </li>
                <li>
                        <a class="<?php echo checkIfActive('batch_tool')?>" href="./batch_tool.php">
                                旧版批量上架
                        </a>
                </li>
                <li>
                        <a class="<?php echo checkIfActive('shequlist')?>" href="./shequlist.php?my=add">
                                网站对接设置
                        </a>
                </li>
        </ul>
</li>

<li class="<?php echo checkIfActive('site,gonggao,mail,pay,upimg,upbgimg,clean,cleanbom,defend,proxy,copygg,mailtest,epay,captcha,cron,oauth,update,chat,set_domain_landing,set_wall_guide,security,rates')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-cog sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">系统设置</span></a>
	<ul>
<li>
	<a class="<?php echo checkIfActive("site") ?>" href="./set.php?mod=site">
		网站信息配置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("gonggao,copygg") ?>" href="./set.php?mod=gonggao">
		网站公告配置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("mail") ?>" href="./set.php?mod=mail">
		邮箱与提醒配置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("pay,epay") ?>" href="./set.php?mod=pay">
		支付接口配置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("oauth") ?>" href="./set.php?mod=oauth">
		快捷登录配置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("captcha") ?>" href="./set.php?mod=captcha">
		验证与IP配置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("defend") ?>" href="./set.php?mod=defend">
		防CC攻击设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("security") ?>" href="./security.php">
		安全加固设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("upimg,upbgimg") ?>" href="./set.php?mod=upimg">
		Logo与背景设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("cron") ?>" href="./set.php?mod=cron">
		计划任务设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("clean") ?>" href="./clean.php">
		系统数据清理
    </a>
</li>
<li>
	<a class="<?php echo checkIfActive("update") ?>" href="./update.php">
		检查版本更新
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("rates") ?>" href="./rates.php">
		费率与手续费
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('chat') ?>" href="./set.php?mod=chat">
		客服系统设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('set_domain_landing') ?>" href="./set_domain_landing.php">
		域名落地页
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('set_wall_guide') ?>" href="./set_wall_guide.php">
		防墙引导页
	</a>
</li>

	</ul>
</li>

<!-- 美化/模板设置菜单 -->
<li class="<?php echo checkIfActive('beautify,beautify_admin,admin_style,beautify_sidebar,beautify_font,beautify_background,template,template2,template_editor')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-paint-brush sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">美化/模板设置</span></a>
	<ul>
<li>
	<a class="<?php echo checkIfActive('beautify') ?>" href="./set.php?mod=beautify">
		前台美化
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('beautify_admin') ?>" href="./set.php?mod=beautify_admin">
		鼠标美化
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('admin_style') ?>" href="./set.php?mod=admin_style">
		后台UI风格
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('beautify_sidebar') ?>" href="./set.php?mod=beautify_sidebar">
		后台侧边栏框架美化
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('beautify_font') ?>" href="./set.php?mod=beautify_font">
		字体美化
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('beautify_background') ?>" href="./set.php?mod=beautify_background">
		背景美化
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive('template,template2,template_editor') ?>" href="./set.php?mod=template">
		首页模板设置
	</a>
</li>

	</ul>
</li>

<li class="<?php echo checkIfActive('qiandao,invite,dwz,choujiang,choujiang_list,invitelog,datamove,appCreate,rebaterecharge,coupons,coupon_rules,user_coupons')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-cogs sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">其它组件</span></a>
	<ul>
<li>
	<a class="<?php echo checkIfActive("qiandao") ?>" href="./set.php?mod=qiandao">
		每日签到设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("invite,invitelog") ?>" href="./set.php?mod=invite">
		推广链接设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("choujiang") ?>" href="./choujiang.php">
		抽奖商品设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("choujiang_list") ?>" href="./choujiang_list.php">
		抽奖商品列表
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("dwz") ?>" href="./set.php?mod=dwz">
		防红接口设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("rebaterecharge") ?>" href="./set.php?mod=rebaterecharge">
		充值返利设置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("coupons") ?>" href="./coupons.php">
		优惠券管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("coupon_rules") ?>" href="./coupon_rules.php">
		优惠券发放规则
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("user_coupons") ?>" href="./user_coupons.php">
		用户优惠券管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("datamove") ?>" href="./datamove.php">
		数据迁移
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("appCreate") ?>" href="./appCreate.php">
		APP在线生成
	</a>
</li>

	</ul>
</li>

<?php
                            $count2 = $DB->getColumn("SELECT count(*) from pre_workorder WHERE status=0 or status=1");
                            $count_cloud = $DB->getColumn("SELECT count(*) from pre_workorder WHERE ts=1 and status=0");
                            ?>
<li class="<?php echo checkIfActive('sitelist,mj,tixian,record,rank,userlist,message,workorder,siteprice,kmlist,sitetask,sitetask-check,fenzhan,account,storemgr,fzshoplist')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-sitemap sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">用户/分站管理</span> <span class="label label-danger"><?php echo $count2?></span></a>
	<ul>
<li>
	<a class="<?php echo checkIfActive("account") ?>" href="./account.php">
		员工管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("storemgr") ?>" href="./storemgr.php">
		<i class="fa fa-store"></i> 店铺管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("fenzhan") ?>" href="./set.php?mod=fenzhan">
		分站相关配置
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("1,siteprice") ?>" href="./sitelist.php?mod=1">
		分站列表
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("fzshoplist") ?>" href="./fzshoplist.php">
		<i class="fa fa-cubes"></i> 分站商品
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("mj") ?>" href="./sitelist.php?mod=mj">
		密价用户
	</a>
</li>
<li>
		<a class="<?php echo checkIfActive("workorder") ?>" href="./workorder.php">
			工单列表
			<span class="label label-danger"><?php echo $count2?></span>
		</a>
	</li>
<li>
	<a class="<?php echo checkIfActive("userlist") ?>" href="./userlist.php">
		用户列表
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("record") ?>" href="./record.php">
		收支明细
	</a>
</li>
<?php if($conf['fenzhan_tixian']==1){?>
<li>
	<a class="<?php echo checkIfActive("tixian") ?>" href="./tixian.php">
		余额提现
	</a>
</li>
<?php }?>
<li>
	<a class="<?php echo checkIfActive("rank") ?>" href="./rank.php">
		分站排行
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("message") ?>" href="./message.php">
		站内通知
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("sitetask") ?>" href="./sitetask.php">
		站点任务管理
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("sitetask-check") ?>" href="./sitetask-check.php">
		站点任务审核
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("kmlist") ?>" href="./kmlist.php">
		加款卡密
	</a>
</li>
	</ul>
</li>
<?php
                            $stockrows = $DB->getAll('SELECT `tid` FROM `pre_tools` WHERE `active` = 1 AND `close` = 0 AND `is_curl` = 4');
                            $count = 0;
                            foreach ($stockrows as $stockrow) {

                                $stockTotal = $DB->getColumn('SELECT count(`tid`) FROM `pre_faka` WHERE `orderid` = 0 AND `tid` = :tid', [
                                    ':tid' => $stockrow['tid'],
                                ]);

                                if ($stockTotal < 1) {
                                    $count++;
                                }
                            }
                            $count3 = $DB->getColumn("SELECT count(*) from pre_tools where goods_sid != 0 and audit_status=0");
                            ?>
                            <li class="<?php echo checkIfActive('suplist,suptixian,suprecord,supshoplist,sup,supshoplist2')?>">
                                <a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-sitemap sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">供货管理</span> <span class="label label-danger"><?php echo $count3?></span></a>
                                <ul>
                                    <li>
                                        <a class="<?php echo checkIfActive("sup") ?>" href="./set.php?mod=sup">
                                            供货商相关配置
                                        </a>
                                    </li>
                                    <li>
                                        <a class="<?php echo checkIfActive("suplist,siteprice") ?>" href="./suplist.php">
                                            供货商列表
                                        </a>
                                    </li>
                                    <li>
                                        <a class="<?php echo checkIfActive("supshoplist2") ?>" href="./supshoplist2.php">
                                            商品列表
                                        </a>
                                    </li>
                                    <li>
                                        <a class="<?php echo checkIfActive("supshoplist") ?>" href="./supshoplist.php">
                                            审核商品 <span class="label label-danger"><?php echo $count3?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="<?php echo checkIfActive("suprecord") ?>" href="./suprecord.php">
                                            收支明细
                                        </a>
                                    </li>

                                    <li>
                                        <a class="<?php echo checkIfActive("suptixian") ?>" href="./suptixian.php">
                                            余额提现
                                        </a>
                                    </li>

                                    <!--                                    <li>-->
                                    <!--                                        <a class="--><?php //echo checkIfActive("workorder") ?><!--" href="./workorder.php">-->
                                    <!--                                            工单列表-->
                                    <!--                                        </a>-->
                                    <!--                                    </li>-->
                                    <!--                                    <li>-->
                                    <!--                                        <a class="--><?php //echo checkIfActive("message") ?><!--" href="./message.php">-->
                                    <!--                                            站内通知-->
                                    <!--                                        </a>-->
                                    <!--                                    </li>-->
                                </ul>
                            </li>
<li class="<?php echo checkIfActive('contactlist')?>">
	<a href="javascript:void(0)" class="sidebar-nav-menu"><i class="fa fa-chevron-left sidebar-nav-indicator sidebar-nav-mini-hide"></i><i class="fa fa-address-book sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">联系方式</span></a>
	<ul>
<li>
	<a class="<?php echo checkIfActive("contactlist") ?>" href="./contactlist.php">
		联系列表
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("contactlist") ?>" href="./contactlist.php?my=add">
		添加联系方式
	</a>
</li>
	</ul>
</li>
<li>
	<a class="<?php echo checkIfActive('stock_notice')?>" href="./stock_notice.php">
		<i class="fa fa-bullhorn sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">库存告急</span>
        <span class="label label-danger"><?php echo $count?></span>
	</a>
</li>
<li>
	<a class="<?php echo checkIfActive("workorder2") ?>" href="./workorder2.php">
		<i class="fa fa-bullhorn sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">网盘失效</span>
        <span class="label label-danger"><?php echo $count_cloud?></span>
	</a>
</li>

<li>
	<a class="<?php echo checkIfActive('chatwork') ?>" href="./chatwork.php">
		<i class="fa fa-headphones sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">客服工作台</span>
	</a>
</li>

<li>
	<a class="" href="donate.php">
		<i class="fa fa-star sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">更新日志</span>
	</a>
</li>


                        </ul>
                    </div>
                </div>
                <div id="sidebar-extra-info" class="sidebar-content sidebar-nav-mini-hide">
<div class="progress progress-mini push-bit">
<div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
</div>
<div class="text-center">
<small><span id="year-copy">2018</span> © <a href="http://www.898fk.com" target="_blank"><?php echo $conf['sitename']?></a></small>
</div>
</div>
            </div>
            <div id="main-container">
                <header class="navbar navbar-inverse navbar-fixed-top">
 
<ul class="nav navbar-nav-custom">
 
<li>
<a href="javascript:void(0)" onclick="chToggleSidebar();this.blur();">
<i class="fa fa-ellipsis-v fa-fw animation-fadeInRight" id="sidebar-toggle-mini"></i>
<i class="fa fa-bars fa-fw animation-fadeInRight" id="sidebar-toggle-full"></i>菜单
</a>
</li>
<li>
<a href="javascript:void(0)" onclick="javascript:history.go(-1);">
<i class="fa fa-reply fa-fw animation-fadeInRight"></i> 返回
</a>
</li>
<li class="hidden-xs" style="margin-left: 20px;">
<div class="navbar-form navbar-form-sm">
<div class="form-group">
<div class="input-group">
<input type="text" id="global-search" class="form-control input-sm" placeholder="全局搜索菜单...">
<span class="input-group-btn">
<button class="btn btn-sm btn-primary" type="button" onclick="performGlobalSearch()">
<i class="fa fa-search"></i>
</button>
</span>
</div>
</div>
</li>
 
</ul>
 
 
<ul class="nav navbar-nav-custom pull-right">
<li>
<a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar-alt');this.blur();">
<i class="fa fa-wrench sidebar-nav-icon"></i>
</a>
</li>
<li class="dropdown">
<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
<img src="<?php echo ($conf['kfqq'])?'//q2.qlogo.cn/headimg_dl?bs=qq&dst_uin='.$conf['kfqq'].'&src_uin='.$conf['kfqq'].'&fid='.$conf['kfqq'].'&spec=100&url_enc=0&referer=bu_interface&term_type=PC':'../assets/img/user.png'?>" alt="avatar">
</a>
<ul class="dropdown-menu dropdown-menu-right">
<li class="dropdown-header text-center">
<strong>管理员用户</strong>
</li>
<li>
<a href="set.php?mod=bind">
<i class="fa fa-qrcode fa-fw pull-right"></i>
扫码登录
</a>
</li>
</li>
<li>
<a href="set.php?mod=account">
<i class="fa fa-pencil-square fa-fw pull-right"></i>
密码修改
</a>
</li>
<li>
<a href="../">
<i class="fa fa-home fa-fw pull-right"></i>
网站首页
</a>
</li>
<li class="divider">
</li>
<li>
<li>
<a href="login.php?logout">
<i class="fa fa-power-off fa-fw pull-right"></i>
退出登录
</a>
</li>
</ul>
</li>
</ul>
</header>
<div id="page-content">
<div class="main-bg-img"></div>
			<div class="main pjaxmain">
				<div class="content-header">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="header-section">
                                    <?php eval(base64_decode('ZWNobyAnPGgxPicuJHRpdGxlLic8L2gxPjxzbWFsbCBjbGFzcz0icmJ0LXN1YiI+Jy5iYXNlNjRfZGVjb2RlKCd3cW52dUk5WFlXeFVaWEpVWlVOb1UzUjFSR2x2JykuJzwvc21hbGw+Jzs=')); ?>
                                </div>
                            </div>
                        </div>
					</div>
<div class="row">
<?php }?>
