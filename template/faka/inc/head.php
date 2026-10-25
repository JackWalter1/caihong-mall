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
	<link rel="stylesheet" href="<?php echo $cdnserver?>assets/faka/css/css<?php echo isset($conf['template_style'])&&$conf['template_style']?$conf['template_style']:7?>.css"/>
	<link rel="stylesheet" href="<?php echo $cdnpublic?>Buttons/2.0.0/css/buttons.min.css" />
<style>
.info.denglu {
	line-height: 55px;
}
#head .top .logo_img {
	float: left;
	height: 80px;
	width: 400px;
	margin-top: 10px;
}
td.stitle{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width:580px;text-align:left;}
.sinput{text-indent:10px;float:left;width:270px;height:35px;line-height:28px;padding:5px 5px 5px 5px;color:#31302e;border-radius:0;background-color:#fff;font-size:16px}.sbtn{float:left;width:100px;height:47px;cursor:pointer;display:inline-block;font-size:16px;vertical-align:middle;color:#31302e}
</style>
<?php if(isset($conf['template_bgopen']) && $conf['template_bgopen']==0){?><style>.g-body{background-image: none;}</style><?php }?>
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
    font-size: 18px;
    font-weight: bold;
    color: #333;
}
.notice-popup-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
    padding: 0;
    width: 30px;
    height: 30px;
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
    padding: 20px;
    max-height: 400px;
    overflow-y: auto;
}
.notice-popup-image {
    max-width: 100%;
    height: auto;
    margin-bottom: 15px;
    border-radius: 4px;
}
.notice-popup-text {
    line-height: 1.6;
    color: #666;
}
.notice-popup-footer {
    padding: 0 20px 20px;
    text-align: center;
}
.notice-popup-btn {
    background-color: #1E9FFF;
    color: white;
    border: none;
    padding: 10px 30px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
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
<?php if((isset($mod)&&$mod=='index') && (isset($conf['notice_popup'])&&$conf['notice_popup']==1) && !empty($conf['notice_content'])){?>
<div class="notice-popup-overlay" id="noticePopupOverlay">
    <div class="notice-popup">
        <div class="notice-popup-header">
            <h3 class="notice-popup-title"><?php echo !empty($conf['notice_title'])?$conf['notice_title']:'网站公告';?></h3>
            <button class="notice-popup-close" id="noticePopupClose">&times;</button>
        </div>
        <div class="notice-popup-content">
            <?php if(!empty($conf['notice_image']) && trim($conf['notice_image']) !== ''){?>
            <img src="<?php echo trim($conf['notice_image']);?>" class="notice-popup-image" alt="公告图片">
            <?php }?>
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
    <div id="head">

   	  <div class="top">

       	<!--<div class="logo" onClick="location.href=''"></div>-->
        <div class="logo_img"><a href="./"><img src="<?php echo isset($logo)?$logo:'assets/img/logo.png';?>" height="80"></a></div>

        			
              
		  <?php if($islogin2==1){?>
			<div class="info">
				<span class="welcome">亲爱的：<span class="lv"><b><?php echo $userrow['user']?> </b></span>欢迎您！&nbsp;<span></span><br>
            <a href="./user/" class="button button-primary button-rounded button-small">会员中心</a>  
			<a href="./user/login.php?logout" class="button button-giant button-rounded button-small" onclick="return confirm('确定要退出吗？')">安全退出</a>
			</div>
			<?php }else{?>
			<div class="info denglu">
				<a class="button button-3d button-primary button-small" href="./user/login.php">登录</a>    <a class="button button-3d button-caution button-small" href="./user/reg.php"><i class="fa fa-tag"></i>注册</a>&nbsp;&nbsp;
			</div>
			<?php }?>
		</div>
      

      <div class="dh">

        <ul id="nav"> 

            <li><a <?php echo (isset($mod)&&$mod=='index')?'class="a2"':null;?> href="./">商品首页</a></li>
		<li><a <?php echo (isset($mod)&&$mod=='fenlei')?'class="a2"':null;?> href="./?mod=fenlei">商品分类</a></li>
		<li><a <?php echo (isset($mod)&&$mod=='query')?'class="a2"':null;?> href="./?mod=query">订单查询</a></li>
             <?php if(!empty($conf['template_about'])){?><li><a <?php echo (isset($mod)&&$mod=='about')?'class="a2"':null;?> href="./?mod=about">关于我们</a></li><?php }?>
             <?php if(!empty($conf['template_help'])){?><li><a <?php echo (isset($mod)&&$mod=='help')?'class="a2"':null;?> href="./?mod=help">帮助中心</a></li><?php }?>
			  <?php if($conf['articlenum']>0){?>
			   <li><a target="_blank" href="<?php echo article_url()?>">文章列表</a></li>
			   <?php }?>
			   <?php if(!empty(trim($conf['appurl']))){?>
			   <li><a target="_blank" href="<?php echo trim(str_replace(array('﻿', ' '), array('', ''), $conf['appurl'])); ?>">APP下载</a></li>
			   <?php }?>
			   <?php if(!empty($conf['menu1_name']) && !empty(trim($conf['menu1_url']))){?>
			   <li><a target="_blank" href="<?php echo trim(str_replace(array('﻿', ' '), array('', ''), $conf['menu1_url'])); ?>"><?php echo $conf['menu1_name']; ?></a></li>
			   <?php }?>
			   <?php if(!empty($conf['menu2_name']) && !empty(trim($conf['menu2_url']))){?>
			   <li><a target="_blank" href="<?php echo trim(str_replace(array('﻿', ' '), array('', ''), $conf['menu2_url'])); ?>"><?php echo $conf['menu2_name']; ?></a></li>
			   <?php }?>
			   <?php if(!empty($conf['menu3_name']) && !empty(trim($conf['menu3_url']))){?>
			   <li><a target="_blank" href="<?php echo trim(str_replace(array('﻿', ' '), array('', ''), $conf['menu3_url'])); ?>"><?php echo $conf['menu3_name']; ?></a></li>
			   <?php }?>
			   <?php if(isset($conf['search_open'])&&$conf['search_open']==1){?>
				<div style="float:right;width:383px;padding:6px 6px 6px 6px;">
                <div style="border-radius:5px ;width:383px;height: 40px;">
					<form action="?" method="get"><input type="hidden" name="mod" value="so"/>
                    <input type="text" name="kw" value="" class="sinput" placeholder="请输入商品关键词" required>
					<input type="submit" class="sbtn" value="商品搜索">
					</form>
				</div>
            </div>
			<?php }?>
        </ul> 

        </div>

  
    </div>
