<?php
if(!defined('IN_CRONLITE'))exit();
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
	<meta http-equiv="Cache-Control" content="no-transform"/>
	<title><?php echo $hometitle?></title>
	<meta name="keywords" content="<?php echo $conf['keywords']?>">
	<meta name="description" content="<?php echo $conf['description']?>">
	<meta name="applicable-device" content="mobile">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<link href="<?php echo $cdnserver?>assets/faka/css/frozen.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $cdnserver?>assets/faka/css/public.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $cdnserver?>assets/faka/css/baoliao.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $cdnserver?>assets/faka/css/iconfont.css" rel="stylesheet"/>
	<link href="<?php echo $cdnserver?>assets/faka/css/component.css" rel="stylesheet" type="text/css">
	<script src="<?php echo $cdnpublic?>modernizr/2.8.3/modernizr.min.js"></script>
</head>
<body>
<style>
.notice-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}
.notice-popup {
    background-color: #fff;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}
.notice-popup-header {
    background-color: #f5f5f5;
    padding: 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.notice-popup-title {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
    color: #333;
}
.notice-popup-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #999;
    padding: 0;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s;
}
.notice-popup-close:hover {
    background-color: #eee;
    color: #333;
}
.notice-popup-content {
    padding: 15px;
    max-height: 300px;
    overflow-y: auto;
}
.notice-popup-text {
    line-height: 1.5;
    color: #666;
    font-size: 14px;
}
.notice-popup-footer {
    padding: 0 15px 15px;
    text-align: center;
}
.notice-popup-btn {
    background-color: #1E9FFF;
    color: white;
    border: none;
    padding: 8px 25px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}
.notice-popup-btn:hover {
    background-color: #0C8CE9;
}
.notice-popup-btn:active {
    transform: translateY(1px);
}
</style>
</head>
<body>
<?php if($mod=='index' && $conf['notice_popup']==1 && !empty($conf['notice_content'])){?>
<div class="notice-popup-overlay" id="noticePopupOverlay">
    <div class="notice-popup">
        <div class="notice-popup-header">
            <h3 class="notice-popup-title"><?php echo !empty($conf['notice_title'])?$conf['notice_title']:'网站公告';?></h3>
            <button class="notice-popup-close" id="noticePopupClose">&times;</button>
        </div>
        <div class="notice-popup-content">
            <div class="notice-popup-text"><?php echo $conf['notice_content'];?></div>
        </div>
        <div class="notice-popup-footer">
            <button class="notice-popup-btn" id="noticePopupBtn">知道了</button>
        </div>
    </div>
</div>
<script type="text/javascript">
// 公告弹窗关闭功能
window.addEventListener('DOMContentLoaded', function() {
    var overlay = document.getElementById('noticePopupOverlay');
    var closeBtn = document.getElementById('noticePopupClose');
    var knowBtn = document.getElementById('noticePopupBtn');
    
    // 关闭弹窗函数
    function closeNoticePopup() {
        if (overlay) {
            overlay.style.display = 'none';
        }
    }
    
    if (overlay && closeBtn) {
        // 点击关闭按钮关闭弹窗
        closeBtn.addEventListener('click', closeNoticePopup);
        
        // 点击遮罩层关闭弹窗
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeNoticePopup();
            }
        });
        
        // 按ESC键关闭弹窗
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeNoticePopup();
            }
        });
        
        // 点击"知道了"按钮关闭弹窗
        if (knowBtn) {
            knowBtn.addEventListener('click', closeNoticePopup);
        }
    }
});
</script>
<?php }?>
<header id="box" class="headerm2">
	<a href="javascript:history.back();" class="logo"><img src="<?php echo $cdnserver?>assets/faka/images/iconfont-fanhui.png"></a>
	<a href="javascript:" class="logo2"><?php echo $conf['sitename']?></a>
	<a href="javascript:" onclick="location.reload();" class="search2"><span>刷新</span></a>
		<div id="dl-menu" class="dl-menuwrapper">
		<button id="dl-menu-button">打开菜单</button>
		<ul class="dl-menu">
			<li><a href="./">网站首页</a></li>
			<li>
				<a href="./?mod=wapfenlei">商品分类</a>
			</li>
			<li>
				<a href="./?mod=wapquery">订单查询</a>
			</li>
			<?php if($islogin2==1){?>
			<li>
				<a href="Line">用户中心</a>
				<ul class="dl-submenu">
					<li class="dl-back"><a href="#">返回上级</a></li>
					<li><a href="./user/">用户中心</a></li>
					<li><a href="./user/recharge.php">充值余额</a></li>
					<li><a href="./user/record.php">消费记录</a></li>
					<li><a href="./user/login.php?logout" onclick="return confirm('确定要退出吗？')">退出登录</a></li>
				</ul>
			</li><?php }else{?><li>
				<a href="Line">登录 或 注册</a>
				<ul class="dl-submenu">
					<li class="dl-back"><a href="#">返回上级</a></li>
					<li><a href="./user/login.php">用户登录</a></li>
					<li><a href="./user/reg.php">注册账号</a></li>
					<li><a href="./user/findpwd.php">找回密码</a></li>
				</ul>
			</li><?php }?>
			<?php if(!empty($conf['template_about'])){?><li><a href="./?mod=wappage&type=0">关于我们</a></li><?php }?>
			<?php if(!empty($conf['template_help'])){?><li><a href="./?mod=wappage&type=1">帮助中心</a></li><?php }?>
			<?php if($conf['articlenum']>0){?><li><a href="<?php echo article_url()?>">文章列表</a></li><?php }?>
		</ul>
	</div>
</header>
