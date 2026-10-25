// 全局移动端侧边栏切换函数 - 直接在按钮onclick中调用
function toggleMobileSidebar(event) {
  // 阻止事件冒泡和默认行为
  if (event) {
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
  }
  
  // 只在移动端处理
  if (window.innerWidth > 991.98) {
    return;
  }
  
  // 获取侧边栏元素
  var $aside = $('#aside, .app-aside').first();
  
  // 获取或创建遮罩层
  var $overlay = $('.app-aside-overlay');
  if ($overlay.length === 0) {
    $overlay = $('<div class="app-aside-overlay"></div>');
    $('body').append($overlay);
    
    // 给遮罩层添加点击事件
    $overlay.on('click', function() {
      $aside.removeClass('open off-screen');
      $overlay.removeClass('show');
    });
  }
  
  var isOpen = $aside.hasClass('open') || $aside.hasClass('off-screen');
  
  if (isOpen) {
    // 关闭 - 确保都关闭
    $aside.removeClass('open').removeClass('off-screen');
    $overlay.removeClass('show');
  } else {
    // 打开
    $aside.removeClass('off-screen').addClass('open');
    $overlay.addClass('show');
  }
  
  // 返回false防止其他事件
  return false;
}

// 电脑端侧边栏折叠功能
function initDesktopMenuToggle() {
  $('#desktop-menu-btn').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // 只在桌面端处理
    if (window.innerWidth <= 991.98) {
      return;
    }
    
    // 切换app-aside-folded类
    $('body').toggleClass('app-aside-folded');
  });
}

/* 用户后台现代交互增强 */
$(document).ready(function() {
  // 注释掉JavaScript菜单激活逻辑，使用PHP原生的 checkIfActive 函数
  // initCurrentMenu();
  
  // 自动展开包含激活子菜单的父菜单
  autoExpandActiveSubmenus();
  
  // 初始化移动端菜单
  initMobileMenu();
  
  // 初始化电脑端菜单切换
  initDesktopMenuToggle();
  
  // 初始化工具提示
  initTooltips();
  
  // 初始化表格行点击效果
  initTableClickEffects();
  
  // 初始化按钮加载状态
  initButtonLoadingStates();
  
  // 初始化平滑滚动
  initSmoothScroll();
});

// 移动端菜单功能
function initMobileMenu() {
  var $aside = $('.app-aside');
  var $overlay = $('.app-aside-overlay');
  
  // 点击侧边栏链接后关闭菜单（移动端）
  $aside.find('a').on('click', function(e) {
    if (window.innerWidth <= 991.98) {
      // 检查是否是带有子菜单的父菜单链接
      var $this = $(this);
      var hasSubmenu = $this.hasClass('auto') && $this.next('.nav-sub').length > 0;
      
      // 如果不是父菜单链接，才关闭侧边栏
      if (!hasSubmenu) {
        if ($overlay.length > 0) {
          $overlay.removeClass('show');
        }
        $aside.removeClass('open off-screen');
      }
    }
  });
  
  // 监听窗口大小变化
  $(window).on('resize', function() {
    if (window.innerWidth > 991.98) {
      // 大屏幕，移除遮罩和open状态
      if ($overlay.length > 0) {
        $overlay.removeClass('show');
      }
      $aside.removeClass('open off-screen');
    }
  });
}

// 自动展开激活的子菜单
function autoExpandActiveSubmenus() {
    // 检查每个有子菜单的父菜单项
    $('.navi .nav > li > .nav-sub').each(function() {
        var submenu = $(this);
        var parentLink = submenu.prev('.auto');
        var hasActiveChild = submenu.find('li.active').length > 0;
        
        if (hasActiveChild) {
            // 展开子菜单
            submenu.addClass('open');
            // 旋转箭头图标
            var icon = parentLink.find('.pull-right i');
            icon.removeClass('fa-angle-right').addClass('fa-angle-down');
            // 确保父菜单项也标记为激活
            parentLink.parent('li').addClass('active');
        }
    });
}

// 子菜单切换功能 - 全局可用
function toggleSubmenu(el) {
    var submenu = $(el).next('.nav-sub');
    var icon = $(el).find('.pull-right i');
    
    if (submenu.length > 0) {
        if (submenu.hasClass('open')) {
            submenu.removeClass('open');
            icon.removeClass('fa-angle-down').addClass('fa-angle-right');
        } else {
            // 先关闭其他子菜单
            $('.nav-sub.open').removeClass('open');
            $('.nav-sub').prev('.auto').find('.pull-right i').removeClass('fa-angle-down').addClass('fa-angle-right');
            // 打开当前
            submenu.addClass('open');
            icon.removeClass('fa-angle-right').addClass('fa-angle-down');
        }
    }
}

// 高亮当前菜单项 - 已禁用，使用PHP原生逻辑
/*
function initCurrentMenu() {
}
*/

// 初始化工具提示
function initTooltips() {
    // 如果有 tooltip 库可以在这里初始化
    // 这里用原生实现简单的工具提示
    $('[data-toggle="tooltip"]').on('mouseenter', function() {
        var title = $(this).attr('title') || $(this).data('title');
        if (title) {
            // 简单的工具提示逻辑
        }
    });
}

// 表格行点击效果
function initTableClickEffects() {
    $('.table tbody tr').on('click', function(e) {
        // 如果点击的是按钮或链接，不触发行效果
        if ($(e.target).closest('button, a, input, select, textarea').length) {
            return;
        }
        
        // 简单的点击视觉反馈
        $(this).addClass('table-row-clicked');
        setTimeout(() => {
            $(this).removeClass('table-row-clicked');
        }, 200);
    });
}

// 按钮加载状态
function initButtonLoadingStates() {
    $('form').on('submit', function() {
        var $btn = $(this).find('button[type="submit"], input[type="submit"]');
        $btn.each(function() {
            var $this = $(this);
            if (!$this.data('original-text')) {
                $this.data('original-text', $this.text() || $this.val());
            }
            $this.prop('disabled', true).addClass('disabled');
            if ($this.is('button')) {
                $this.html('<i class="fa fa-spinner fa-spin"></i> 处理中...');
            } else {
                $this.val('处理中...');
            }
        });
    });
}

// 平滑滚动
function initSmoothScroll() {
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 300);
        }
    });
}

// 重置按钮状态
function resetButtonState($btn) {
    var originalText = $btn.data('original-text');
    if (originalText) {
        $btn.prop('disabled', false).removeClass('disabled');
        if ($btn.is('button')) {
            $btn.html(originalText);
        } else {
            $btn.val(originalText);
        }
    }
}

// 全局错误处理 - 静默处理，不干扰用户
$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
    // 静默处理 AJAX 错误，不在控制台显示
    // 如果需要调试，可以取消下面这行的注释
    // console.log('AJAX Error (silenced):', settings.url, thrownError);
});
