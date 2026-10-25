<?php
if(!defined('IN_CRONLITE'))exit();
$zid = $conf['zid'];
$site_info=$DB->getRow("select appurl from pre_site where zid='$zid' limit 1");
if (empty($site_info['appurl'])) {
  $xr=$conf['appurl'];
} else {
  $xr=$site_info['appurl'];
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
  <title><?php echo $conf['sitename']?><?php echo $conf['title']?></title>
  <meta name="keywords" content="<?php echo $conf['keywords']?>">
  <meta name="description" content="<?php echo $conf['description']?>">
  <?php if(!empty($conf['favicon'])){?>
  <link rel="icon" href="<?php echo $cdnserver?>/<?php echo $conf['favicon']?>" type="image/x-icon" />
  <link rel="shortcut icon" href="<?php echo $cdnserver?>/<?php echo $conf['favicon']?>" type="image/x-icon" />
  <?php }else{?>
  <link rel="icon" href="<?php echo $cdnserver?>assets/img/favicon/favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="<?php echo $cdnserver?>assets/img/favicon/favicon.ico" type="image/x-icon" />
  <?php }?>
  
  <!-- 预加载关键资源 -->
  <!-- 暂时移除预加载，避免跨域问题 -->
  
  <!-- 预加载字体资源 -->
  <link rel="preload" href="//lib.baomitu.com/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2?v=4.7.0" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="//lib.baomitu.com/twitter-bootstrap/3.3.7/fonts/glyphicons-halflings-regular.woff2" as="font" type="font/woff2" crossorigin>
  
  <!-- 关键CSS优先加载 - 使用最快的CDN -->
  <link href="//lib.baomitu.com/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="//lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/simple/css/oneui.css">
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>">
  
  <!-- 非关键JavaScript异步加载 -->
  <script src="//lib.baomitu.com/modernizr/2.8.3/modernizr.min.js" async></script>
  <!--[if lt IE 9]>
    <script src="//lib.baomitu.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//lib.baomitu.com/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <!-- 关键JavaScript立即加载 - 使用最快的CDN -->
  <script src="//lib.baomitu.com/jquery/1.12.4/jquery.min.js"></script>
  <script src="//lib.baomitu.com/layer/2.3/layer.js"></script>
  <script src="assets/js/main.js?ver=<?php echo VERSION ?>"></script>
  
  <!-- 全局错误处理 -->
  <script>
  window.addEventListener('error', function(e) {
      console.error('全局JavaScript错误:', e.error);
      return false;
  });
  </script>
  
  <!-- 分类点击功能优化 -->
  <script>
  $(document).ready(function(){
      // 立即绑定分类点击事件，不等待其他资源加载
      $(document).on('click', '.goodTypeChange', function(e){
          e.preventDefault();
          e.stopPropagation();
          
          var id = $(this).data('id');
          console.log('分类点击事件触发，ID:', id);
          
          // 立即响应，不等待AJAX
          $("#cid").val(id);
          $("#goodType").hide('fast');
          $("#goodTypeContent").show('fast');
          
          // 异步加载商品数据
          setTimeout(function(){
              $("#cid").trigger('change');
          }, 100);
          
          return false;
      });
      
      // 返回按钮
      $(document).on('click', '.backType', function(e){
          e.preventDefault();
          e.stopPropagation();
          $("#goodType").show('fast');
          $("#goodTypeContent").hide('fast');
          return false;
      });
      
      console.log('关键JavaScript已加载完成');
  });
  </script>
  
  <style type="text/css">
#submit_cart_shop {
    background: linear-gradient(to right,#00FFFF,#02C874);
    border-radius: 25px 0 0 25px;
}
#submit_buy {
    background: linear-gradient(to right,#84C1FF,#66B3FF);
    border-radius: 0 25px 25px 0;
}

/* 优化分类点击区域 */
.goodTypeChange {
    pointer-events: auto !important;
    cursor: pointer !important;
    position: relative !important;
    z-index: 10 !important;
}

.goodTypeChange:hover {
    opacity: 0.8;
}

/* 确保分类容器正常显示 */
#goodType {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* 优化加载性能 */
.widget {
    pointer-events: auto !important;
}

.btn {
    pointer-events: auto !important;
}

/* 减少动画时间 */
.animated {
    animation-duration: 0.5s !important;
}

/* 优化图片加载 */
img {
    max-width: 100%;
    height: auto;
}
</style>
<?php echo $background_css?>
</head>
<body>
<?php if($background_image){?>
<img src="<?php echo $background_image;?>" alt="Full Background" class="full-bg full-bg-bottom animated pulse " ondragstart="return false;" oncontextmenu="return false;">
<?php }?>
<img src="https://api.suyanw.cn/api/comic" alt="Full Background" class="full-bg full-bg-bottom animated pulse " ondragstart="return false;" oncontextmenu="return false;">
<div style="padding-top:6px;">
<div class="col-xs-12 col-sm-10 col-md-8 col-lg-4 center-block" style="float: none;">
<!--弹出公告-->
<div class="modal fade" align="left" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $conf['sitename']?></h4>
       </div>
        <div class="modal-body">
         	<?php echo $conf['modal']?>
  	    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">知道啦</button>
      </div>
    </div>
  </div>
</div>
<!--弹出公告-->
<!--公告-->
<div class="modal fade" align="left" id="anounce" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">公告</h4>
      </div>
	  <div class="modal-body">
	  <?php echo $conf['anounce']?>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
 </div>
<!--公告-->
<!--查单说明开始-->
<div class="modal fade" align="left" id="cxsm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">查询内容是什么？该输入什么？</h4>
      </div>
      	<li class="list-group-item">例如您购买的是预留的手机号或者QQ号，输入下单的手机号或者QQ号即可查询订单</li>
        <li class="list-group-item"><font color="red">如果您不知道下单账号是什么，可以不填写，直接点击查询，则会根据浏览器缓存查询</font></li>


      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>
<!--查单说明结束-->

<!--顶部导航-->
          <div class="block block-link-hover3" style="box-shadow:0px 5px 10px 0 rgba(0, 0, 0, 0.26);">
        <?php
// 仅允许本地路径，过滤外部URL
$top_image = $conf['top_image'] ?? '';
// 匹配所有外部URL格式 (http:// https:// ftp:// //)
if (preg_match('/^(https?:\/\/|ftp:\/\/|\/\/)/i', $top_image)) {
    $top_image = 'assets/img/above.png'; // 使用默认本地图片
}
?>
<div class="block-content block-content-full text-center bg-image" style="background-image: url('<?php echo $top_image ?: 'assets/img/above.png'; ?>');background-size: 100% 100%;">
            <div>
                <div>
                    <img class="img-avatar img-avatar80" src="//q4.qlogo.cn/headimg_dl?dst_uin=<?php echo $conf['kfqq']?>&spec=100">
                    </div>
                </div>
            </div>
           
        <center>
            <h2>     <a href="javascript:void(alert('建议收藏到浏览器书签哦！'));"><b>
    <font color="#414324"><?php echo $conf['sitename']?></font></b></a></h2><font color="#414324">
   <h5><div color="wrap"><img src="https://z3.ax1x.com/2021/06/19/RCRVzT.png">低价货源-信誉保证<img src="https://z3.ax1x.com/2021/06/19/RCRVzT.png"> 
    <style>

h5 {
  text-shadow: -1px 1px 0 #FFD180;
  -webkit-animation: 1s infinite rainbowText;
          animation: 1s infinite rainbowText;
}

@-webkit-keyframes rainbowText {
  0% {
    text-shadow: -0.1rem 0.1rem #FFFF8D, -0.2rem 0.2rem #CCFF90, -0.3rem 0.3rem #A7FFEB, -0.4rem 0.4rem #82B1FF, -0.5rem 0.5rem #B388FF, -0.6rem 0.6rem #EA80FC, -0.7rem 0.7rem #FF80AB, -0.8rem 0.8rem #FFD180;
  }
  12.5% {
    text-shadow: -0.1rem 0.1rem #FFD180, -0.2rem 0.2rem #FFFF8D, -0.3rem 0.3rem #CCFF90, -0.4rem 0.4rem #A7FFEB, -0.5rem 0.5rem #82B1FF, -0.6rem 0.6rem #B388FF, -0.7rem 0.7rem #EA80FC, -0.8rem 0.8rem #FF80AB;
  }
  25% {
    text-shadow: -0.1rem 0.1rem #FF80AB, -0.2rem 0.2rem #FFD180, -0.3rem 0.3rem #FFFF8D, -0.4rem 0.4rem #CCFF90, -0.5rem 0.5rem #A7FFEB, -0.6rem 0.6rem #82B1FF, -0.7rem 0.7rem #B388FF, -0.8rem 0.8rem #EA80FC;
  }
  37.5% {
    text-shadow: -0.1rem 0.1rem #EA80FC, -0.2rem 0.2rem #FF80AB, -0.3rem 0.3rem #FFD180, -0.4rem 0.4rem #FFFF8D, -0.5rem 0.5rem #CCFF90, -0.6rem 0.6rem #A7FFEB, -0.7rem 0.7rem #82B1FF, -0.8rem 0.8rem #B388FF;
  }
  50% {
    text-shadow: -0.1rem 0.1rem #B388FF, -0.2rem 0.2rem #EA80FC, -0.3rem 0.3rem #FF80AB, -0.4rem 0.4rem #FFD180, -0.5rem 0.5rem #FFFF8D, -0.6rem 0.6rem #CCFF90, -0.7rem 0.7rem #A7FFEB, -0.8rem 0.8rem #82B1FF;
  }
  62.5% {
    text-shadow: -0.1rem 0.1rem #82B1FF, -0.2rem 0.2rem #B388FF, -0.3rem 0.3rem #EA80FC, -0.4rem 0.4rem #FF80AB, -0.5rem 0.5rem #FFD180, -0.6rem 0.6rem #FFFF8D, -0.7rem 0.7rem #CCFF90, -0.8rem 0.8rem #A7FFEB;
  }
  75% {
    text-shadow: -0.1rem 0.1rem #A7FFEB, -0.2rem 0.2rem #82B1FF, -0.3rem 0.3rem #B388FF, -0.4rem 0.4rem #EA80FC, -0.5rem 0.5rem #FF80AB, -0.6rem 0.6rem #FFD180, -0.7rem 0.7rem #FFFF8D, -0.8rem 0.8rem #CCFF90;
  }
  87.5% {
    text-shadow: -0.1rem 0.1rem #CCFF90, -0.2rem 0.2rem #A7FFEB, -0.3rem 0.3rem #82B1FF, -0.4rem 0.4rem #B388FF, -0.5rem 0.5rem #EA80FC, -0.6rem 0.6rem #FF80AB, -0.7rem 0.7rem #FFD180, -0.8rem 0.8rem #FFFF8D;
  }
  100% {
    text-shadow: -0.1rem 0.1rem #FFFF8D, -0.2rem 0.2rem #CCFF90, -0.3rem 0.3rem #A7FFEB, -0.4rem 0.4rem #82B1FF, -0.5rem 0.5rem #B388FF, -0.6rem 0.6rem #EA80FC, -0.7rem 0.7rem #FF80AB, -0.8rem 0.8rem #FFD180;
  }
}

</style>
</div>
</h5></font></center><font color="#414324">
        <div class="flip-box-1-3"> 
        <div class="block-content block-content-mini block-content-full">
            <div class="btn-group btn-group-justified">
				<div class="btn-group"> 
<a class="btn btn-default fenzhan-jump" href="./user/regsite.php" target="_blank"><img src="https://z3.ax1x.com/2021/06/19/RCRtyD.gif">&nbsp;<font color="#B008B"><span style="font-weight:bold">自助开通分站</span></font></a>
					</div>
					 	<a href="#anounce" target="_blank" data-toggle="modal" class="btn btn-default"><img src="https://z3.ax1x.com/2021/06/19/RCoJN4.jpg">&nbsp;<span style="font-weight:bold"><b><font color="#DC143C">平台公告</font></b></span></a>
						                <div class="btn-group">
                 <a class="btn btn-default" data-toggle="modal" href="user/login.php"><img src="https://z3.ax1x.com/2021/06/19/RCRNOe.gif">&nbsp;<b><font color="#0000CD">注册/登录</font></b></a></div>
				</div>
             <center>
                   <a class="btn btn-default" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes" target="_blank"><font color="#FF0000"><span style="font-weight:bold">♛《售后问题》 9:00-23:00  </span></font><font color="#006400"><span style="font-weight:bold">♛人工客服中心♛</span></font></a>
             
                 <center>
                                                    <!--勉强运行-->
	 <center><li style="font-weight:bold" class="list-group-item">    
<center>
                <font color="#3299CC">当前时间:</font><span id="run_time" style="color:red"></span>
            </center>

            <script>
                function runTime() {
                    var d = new Date(), str = '';
                    BirthDay = new Date("1/1/2023 00:00:00");
                    today = new Date();
                    timeold = (today.getTime() - BirthDay.getTime());
                    sectimeold = timeold / 1000
                    secondsold = Math.floor(sectimeold);
                    msPerDay = 24 * 60 * 60 * 1000
                    msPerYear = 365 * 24 * 60 * 60 * 1000
                    e_daysold = timeold / msPerDay
                    e_yearsold = timeold / msPerYear
                    daysold = Math.floor(e_daysold);
                    yearsold = Math.floor(e_yearsold);
                    //str = yearsold + "年";
                    str += d.getHours() + '时';
                    str += d.getMinutes() + '分';
                    str += d.getSeconds() + '秒';
                    return str;
                }
                setInterval(function () { $('#run_time').html(runTime()) }, 1000);
            </script>
            <!--勉强运行-->
            </li></center></center></div>
            
    </div>
      </font></div><font color="#414324">
            
    	<style>
    #nr{
    	font-size:20px;
    	margin: 0;
        background: -webkit-linear-gradient(left,
            #ffffff,
            #ff0000 6.26%,
            #ff7d00 12.5%,
            #ffff00 18.75%,
            #00ff00 26%,
            #00ffff 31.26%,
            #0000ff 37.5%,
            #ff00ff 43.75%,
            #ffff00 50%,
            #ff0000 56.26%,
            #ff7d00 62.5%,
            #ffff00 68.75%,
            #00ff00 75%,
            #00ffff 81.26%,
            #0000ff 87.5%,
            #ff00ff 93.75%,
            #ffff00 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-size: 200% 100%;
        animation: masked-animation 2s infinite linear;
    }
    @keyframes masked-animation {
        0% {
            background-position: 0 0;
        }
        100% {
            background-position: -100%, 0;
        }
    }
    
  

</style>
<div style="background-color:#333;border-radius: 25px;box-shadow: 0px 0px 5px #f200ff;padding:5px;margin-top: 10px;margin-bottom:0px;">
    <marquee>
    	<b id="nr">最亲爱的站长祝愿：站长祝各位幸福安康，快乐美满，好事成双，生意兴隆，有项目上架联系客服上架，有问题第一时间找客服处理，友友们请第一时间收藏本网址-售后做不到最好,但是一定竭尽全力去解决您遇到的问题-开通分战,密价提卡，诚信邀代理!</b>
    </marquee>
    </div>

<!--顶部导航-->
</font><div class="tab-content"><font color="#414324">

</font><div class="block" style="margin-top:15px;font-size:15px;padding:1px;border-radius:15px;background-color: white;"><font color="#414324">

        <ul class="nav nav-tabs btn btn-block animated zoomInLeft btn-rounded" data-toggle="tabs">
        <style>
        /* 确保分站按钮不会被 tab 系统影响 */
        .fenzhan-jump {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        .fenzhan-jump:hover {
            text-decoration: none !important;
        }
        </style>
            <li style="width: 25%;" align="center" class="active"><a href="#shop" data-toggle="tab"><span style="font-weight:bold"><img border="0" width="22" src="https://pan.suyanw.cn/view.php/a4c308fe41a57c4751b133d9189161b4.gif"><font color="#0000FF">下单</font></span></a></li>
            <li style="width: 25%;" align="center"><a href="#search" data-toggle="tab" id="tab-query"><span style="font-weight:bold"><i class="fa fa-search"></i> <font color="#8B008B">查单</font></span></a></li>
			<li style="width: 25%;" align="center"><a href="./user/regsite.php" target="_blank" class="fenzhan-jump"><font color="#FF4000"><i class="fa fa-location-arrow fa-spin"></i> <b>分站</b></font></a></li>
		
			<li style="width: 25%;" align="center"><a href="#more" data-toggle="tab"><span style="font-weight:bold"><i class="fa fa-folder-open"></i> <font color="#FF8C00">更多</font></span></a></li>
        </ul>
        




<div style="background-color:#333;border-radius: 25px;box-shadow: 0px 0px 5px #f200ff;padding:5px;margin-top: 10px;margin-bottom:0px;">
    <center><span style="color: rgb(194, 79, 74)"><i class="fa fa-check"></i><b><font color="#D2B48C">【平台所有商品全天24小时自动发货】</font><i class="fa fa-check"></i></b></span></center>
    <center><span style="font-size:10px;"><strong><span><span style="color:#E53333;">下单步骤<span style="color:#E53333;"> &gt; <span><span style="color:#E8E8E8;">选择分类</span></span> &gt; <span style="color:#009900;">选择商品<span style="color:#E53333;"> &gt; </span></span><span></span><span style="color:#EE33EE;">填写信息<span style="color:#E53333;"> &gt; </span><span style="color:#F08080;">下单成功</span></span></span></span></strong></span>
</center></div></font><center><font color="#414324"><b><font color="#0000CD"></font></b></font></center>
    
    
    </marquee>

    </marquee>
    <div class="block-content tab-content">
<!--TAB标签-->
<!--在线下单-->

    <div class="tab-pane active" id="shop">	
<?php include ROOT.'user/shop.inc.php'; ?>
	</div>
  	
<!--在线下单-->
<marquee>
    	<b id="nr">诚信经营,价格最低,货源最全,卡密问题质保可退换,放心下单即可!!!</b>
    </marquee>
<!--查询订单-->
				
						<div class="tab-pane fade fade-up" id="search">
							<ul class="list-group animated bounceIn">
      <li class="list-group-item">
        <div class="media">
									<span class="pull-left thumb-sm"><img src="//q4.qlogo.cn/headimg_dl?dst_uin=<?php echo $conf['kfqq']?>&spec=100" class="img-circle img-thumbnail img-avatar"></span>
											<div class="pull-left push-10-t">
									<div class="font-w600 push-5">🚀运营站长 QQ:<?php echo $conf['kfqq']?></div>
									 <div class="text-muted">
              <script>var online = new Array();</script>
									<div class="text-muted"><h8><b>️售后问题请联系网站客服解决！️</b></h8></div>
							</div>
          </div>
        </div>
      </li>
    </ul>
							<div class="col-xs-12 well well-sm animation-pullUp">
			<font color="#0000FF">付款未收到卡密,请自己查单补单<br>
-------------最简单的查单方式--------------</font><br>
<font color="#DC143C">什么浏览器购买的，直接用什么浏览器打开，什么也别填写，直接点立即查询。在手机QQ打开的购买的，用手机QQ打开网址点立即查询~！</font><br>		</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-btn">
										<select class="form-control" id="searchtype" style="padding: 6px 4px;width:90px"><option value="0">下单账号</option><option value="1">17位单号</option></select>
									</div>
									<input type="text" name="qq" id="qq3" value="" class="form-control" placeholder="请输入要查询的内容（留空则显示最新订单）" required="">
									<span class="input-group-btn"><a href="#cxsm" data-toggle="modal" class="btn btn-warning">说明</a></span>
								</div>
							</div>
									<input type="submit" id="submit_query" class="btn btn-default btn-block btn-rounded" style="background-image: url(https://pan.suyanw.cn/view.php/6240ef859a11d3a31a7b3ccb0358dc02.jpg);font-weight:bold" value="立即查询">

								<font color="red">
										<i class="">
										</i>
									</font>
									<font color="red">
				查单号:请输入您购买时候填写的手机号，如果填写的时候忘记手机号请点击立即查询即可！
				
									</font>						
							<br>
							<div id="result2" class="form-group" style="display:none;">
								<center>
									<small>
										<font color="#ff0000">
											手机用户可以左右滑动
										</font>
									</small>
								</center>
								<div class="table-responsive">
									<table class="table table-vcenter table-condensed table-striped">
										<thead>
											<tr>
												<th class="hidden-xs">
													下单账号
												</th>
												<th>
													商品名称
												</th>
												<th>
													数量
												</th>
												<th class="hidden-xs">
													购买时间
												</th>
												<th>
													状态
												</th>
												<th>
													操作
												</th>
											</tr>
										</thead>
										<tbody id="list">
										</tbody>
									</table>
								</div>
							</div>
						</div>
<!--查询订单-->
<!--开通分战-->
    <div class="tab-pane" id="Substation">
	<table class="table table-borderless animated bounceIn" style="text-align: center;">
    <tbody>
      <tr class="active">
        <td>
          <h4>
            <span style="font-weight:bold">
              <font color="#FF8000">搭</font>
              <font color="#EC6D13">建</font>
              <font color="#D95A26">属</font>
              <font color="#C64739"></font>
              <font color="#A0215F">自</font>
              <font color="#8D0E72">己</font>
              <font color="#5400AB">的</font>
              <font color="#4100BE">货</font>
              <font color="#2E00D1">源</font>
              <font color="#1B00E4">站</font></span>
          </h4>
        </td>
      </tr>
      <tr class="active">
        <td>学生/上班族/创业/休闲挣￥必备工具</td></tr>
      <tr class="active">
        <td>
          <strong>
            网站轻轻松松推广日挣上千￥不是梦</strong></td>
      </tr>
            <tr class="active">
        <td><span class="glyphicon glyphicon-magnet"></span>&nbsp;快加入我们成为大家庭中的一员吧<hr> <a href="#userjs" data-toggle="modal" class="btn btn-effect-ripple  btn-info btn-sm" style="float:left;overflow: hidden; position: relative;">
            <span class="glyphicon glyphicon-eye-open"></span>&nbsp;网站详情介绍</a>
          <a href="./user/regsite.php" target="_blank" class="btn btn-effect-ripple  btn-success btn-sm" style="float:right;overflow: hidden; position: relative;">
            <span class="glyphicon glyphicon-share-alt"></span>&nbsp;开通网站</a></td></tr>
      <tr>
    </tbody>
  </table>
	</div>
<!--开通分战-->
<!--抽奖-->
    <div class="tab-pane" id="gift">
		<div class="panel-body text-center">
		<div id="roll">点击下方按钮开始</div>
		<hr>
		<p>
		<a class="btn btn-info" id="start" style="display:block;">开始</a>
		<a class="btn btn-danger" id="stop" style="display:none;">停止</a>
		</p> 
		<div id="result"></div><br/>
		<div class="giftlist" style="display:none;"><strong>最近记录</strong><ul id="pst_1"></ul></div>
		</div>
	</div>
<!--抽奖-->
<!--更多-->
						<div class="tab-pane fade fade-right" id="more">
							<div class="col-xs-6 col-sm-4 col-lg-4">
								<a class="block block-link-hover2 text-center" href="./user/" target="_blank">
									<div class="block-content block-content-full bg-city">
										<i class="fa fa-certificate fa-3x text-white">
										</i>
										<div class="font-w600 text-white-op push-15-t">
											后宫禁地
										</div>
									</div>
								</a>
							</div>
							<div class="col-xs-6 col-sm-4 col-lg-4 hide">
								<a class="block block-link-hover2 text-center btn btn-block animated zoomInLeft btn-rounded"
								data-toggle="modal" href="#lqq">
									<div class="block-content block-content-full bg-primary">
										<i class="fa fa-circle-o fa-3x text-white">
										</i>
										<div class="font-w600 text-white-op push-15-t">
											免.费.卡.密
										</div>
									</div>
								</a>
							</div>
	</div>
	</div>
<!--更多-->
<!--版本介绍-->
<div class="modal fade" align="left" id="userjs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">版本介绍</h4>
		</div>
		<div class="block">
            <div class="table-responsive">
                <table class="table table-borderless table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 100px;">功能</th>
                            <th class="text-center" style="width: 20px;">普及版/股东版</th>
                        </tr>
                    </thead>
					<tbody>
						<tr class="active">
                            <td>专属发卡平台</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
                        <tr class="">
                            <td>三种在线支付接口</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="success">
                            <td>专属网站域名</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="">
                            <td>賺取用户提成</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="info">
                            <td>賺取下级分战提成</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-close"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="">
                            <td>设置商品价格</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="warning">
                            <td>设置下级分战商品价格</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-close"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="">
                            <td>搭建下级分战</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-close"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="danger">
                            <td>赠送专属精致APP</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-close"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
                    </tbody>
                </table>
            </div>
				<center style="color: #b2b2b2;"><small><em>* 自己的能力决定着你的收入！</em></small></center>
        </div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
		</div>
    </div>
  </div>
</div>
<!--版本介绍-->
    </div>
</div>
<!--关我们弹窗-->
<div class="modal fade" align="left" id="customerservice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel">客服与帮助</h4>
		</div>
		<div class="modal-body" id="accordion">
			<div class="panel panel-default" style="margin-bottom: 6px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">为什么订单显示已完成了却一直没到账？</a>
					</h4>
				</div>
				<div id="collapseOne" class="panel-collapse in" style="height: auto;">
					<div class="panel-body">
					订单显示（已完成）就证明已经提交到服务器内！并不是订单已刷完。<br>
					可以立即提交工单，客服会优先给您处理！<br>
					订单长时间显示（待处理）请联系客服！
					</div>
				</div>
			</div>
			<div class="panel panel-default" style="margin-bottom: 6px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed">QQ会员/钻类等什么时候到账？</a>
					</h4>
				</div>
				<div id="collapseTwo" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
					下单后的48小时内到账（会员或钻全部都是一样48小时内到账）！<br>
					如果超过48小时，请联系客服退款或补单，提供QQ号码！或提交工单
					</div>
				</div>
			</div>
			<div class="panel panel-default" style="margin-bottom: 6px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed">卡密/CDK没有发送我的邮箱？</a>
					</h4>
				</div>
				<div id="collapseThree" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">没有收到请检查自己邮箱的垃圾箱！也可以去查单区：输入自己下单时填写的邮箱进行查单。<br>
					查询到订单后点击（详细）就可以看到自己购买的卡密/cdk！
					</div>
				</div>
			</div>
			<div class="panel panel-default" style="margin-bottom: 6px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseFourth" class="collapsed">已付款了没有查询到我订单？</a>
					</h4>
				</div>
				<div id="collapseFourth" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body" style="margin-bottom: 6px;">联系客服处理，请提供（付款详细记录截图）（下单商品名称）（下单账号）<br>直接把三个信息发给客服，然后等待客服回复处理（请不要发抖动窗口或者QQ电话）！
					</div>
				</div>
			</div>
			<ul class="list-group" style="margin-bottom: 0px;">
			<li class="list-group-item">   
			   <div class="media">
					<span class="pull-left thumb-sm"><img src="//q4.qlogo.cn/headimg_dl?dst_uin=<?php echo $conf['kfqq'] ?>&spec=100" alt="..." class="img-circle img-thumbnail img-avatar"></span>
			   <div class="pull-right push-15-t">
					<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes" target="_blank"  class="btn btn-sm btn-info">联系</a>
			   </div>
			   <div class="pull-left push-10-t">
					<div class="font-w600 push-5">售.后.客.服</div>
					<div class="text-muted"><b>QQ：<?php echo $conf['kfqq'] ?></b>
					</div>
			   </div>
			   </div>
			</li>
			<li class="list-group-item">
			想要快速回答你的问题就请把问题描述讲清楚!<br>
			下单账号+业务名称+问题，直奔主题，按顺序回复!<br>
			有问题直接留言，请勿抖动语音否则直接无视。<br>			
			</li>
			</ul>
		</div>
    </div>
  </div>
</div>

<a href="./user/regsite.php"><img src="https://pan.suyanw.cn/view.php/11b003a32f4f431973f24ae6a7494023.png"width="100%"></a><br/>
</style>


<div class="block animated bounceInDown btn-rounded" style="border:1px solid #FFF0F5;margin-top:15px;font-size:15px;padding:15px;border-radius:15px;background-color: white;"><div class="panel-heading"><h3 class="panel-title" types=""><font color="#000000"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;<b>今日订单详细</b><img src="https://z3.ax1x.com/2021/06/19/RCRtyD.gif"/></i></a></span></h3></div>
<div class="btn-group btn-group-justified">
		<a target="_blank" class="btn btn-effect-ripple btn-default collapsed" style="overflow: hidden; position: relative;"><b><font color="modal-title">购买用户</font></b></a>
		<a target="_blank" class="btn btn-effect-ripple btn-default collapsed" style="overflow: hidden; position: relative;"><b><font color="modal-title">下单日期</font></b></a>
		<a target="_blank" class="btn btn-effect-ripple btn-default collapsed" style="overflow: hidden; position: relative;"><b><font color="modal-title">物品名称</font></b></a>
		</div>  
		<marquee class="zmd" behavior="scroll" direction="UP" onmouseover="this.stop()" onmouseout="this.start()" scrollamount="5" style="height:16em">
			<table class="table table-hover table-striped" style="text-align:center;table-layout:fixed;width:100%;">
				<thead style="display:none">
					<tr>
						<th width="30%">购买用户</th>
						<th width="30%">下单日期</th>
						<th width="40%">物品名称</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    $c = 80;
                    // 获取商品列表
                    $rs = $DB->query("SELECT name FROM pre_tools WHERE active=1");
                    $goods_list = array();
                    while($res = $rs->fetch()){
                        $goods_list[] = $res['name'];
                    }
                    $goods_count = count($goods_list);
                    
                    for ($a = 0; $a < $c; $a++) {
                        // 随机获取一个商品名称
                        $rand_index = rand(0, $goods_count-1);
                        $name = $goods_list[$rand_index];
                        
                        $date = date('Y-m-d'); #今日
                        $time = date("Y-m-d", strtotime("-1 day"));
                        if ($a > 50) {
                            $date = $time;
                        } else {
                            if (date('H') == 0 || date('H') == 1 || date('H') == 2) {
                                if ($a > 9) {
                                    $date = $time;
                                }
                            }
                        }
                        echo '<tr>
                            <td width="30%">本站用户' . rand(10, 999) . '**' . rand(100, 999) . '**</td>
                            <td width="30%">于' . $date . '日下单成功</td>
                            <td width="40%"><font color="0000">' . $name . '</font></td>
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </marquee>
        </div>
<?php if($conf['articlenum']>0){
$limit = intval($conf['articlenum']);
$rs=$DB->query("SELECT id,title FROM ".DBQZ."article WHERE active=1 ORDER BY top DESC,id DESC LIMIT {$limit}");
$msgrow=array();
while($res = $rs->fetch()){
	$msgrow[]=$res;
}
$class_arr = ['danger','warning','primary','success','info'];
$i=0;
?>

	<!--文章列表-->
				<div class="block block-themed" style="border-radius: 20px;box-shadow:0 5px 10px 0 rgba(0, 0, 0, 0.09);">
					<div class="block-header bg-amethyst" style="border-top-left-radius: 20px; border-top-right-radius: 20px;background-color: #b3cde3;border-color: #b3cde3; padding: 10px 10px;">
						<h3 class="block-title"><i class="fa fa-newspaper-o"></i> 文章列表</h3>
					</div>
					<?php foreach($msgrow as $row){
					echo '<a target="_blank" class="list-group-item" href="'.article_url($row['id']).'"><span class="btn btn-'.$class_arr[($i++)%5].' btn-xs">'.$i.'</span>&nbsp;'.$row['title'].'</a>';
					}?>
					<a href="<?php echo article_url()?>" title="查看全部文章" class="btn-default btn btn-block" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;font-weight: 100;/* border-radius: 20px; */-webkit-transition: all 0.15s ease-out;transition: all 0.15s ease-out;" target="_blank">查看全部文章</a>
				</div>
				<!--文章列表-->
<?php }?>
<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title"><font color="#000000"><i class="fa fa-bar-chart-o"></i>&nbsp;&nbsp;<b>近30天数据统计</b></font></h3></div>
<table class="table table-bordered">
<tbody>
<tr>
<td align="center"><font size="2"><b><font color=#0000FF>896<span id="count_yxts"></span>关键词</font><b/><br><font color="#65b1c9"><img src="https://z3.ax1x.com/2021/06/19/RC44DU.jpg"/></i></font><br>百度收录</font></td>
<td align="center"><font size="2"><b><font color="#DC143C">999+<span id="cou1nt_yxts"></span>人民币</font><b/><br><font color="#65b1c9"><img src="https://z3.ax1x.com/2021/06/19/RC595d.jpg"/></i></font><br>销售金额</font></td>
<td align="center"><font size="2"><b><font color=#8B4513>999+<span id="co1unt_yxts"></span>次好评</font><b/><br><font color="#65b1c9"><img src="https://z3.ax1x.com/2021/06/19/RC45bF.jpg"/></i></font><br>用户好评</font>

</tbody>
</table>

<div class="block block-content block-content-mini block-content-full" style="box-shadow:0px 5px 10px 0 rgba(0, 0, 0, 0.26);">
	<!--网站日志-->
	<!--<div class="row text-center" >-->
	<!--	<div class="col-xs-4">-->
	<!--		<h5 class="widget-heading"><small>订单总数</small><br><a href="javascript:void(0)" class="themed-color-flat"><span id="count_orders"></span>条</a></h5>-->
	<!--	</div>-->
	<!--	<div class="col-xs-4">-->
	<!--		 <h5 class="widget-heading"><small>今日订单</small><br><a href="javascript:void(0)" class="themed-color-flat"><span id="count_orders2"></span>条</a></h5>-->
	<!--	</div>-->
	<!--	<div class="col-xs-4">-->
	<!--		<h5 class="widget-heading"><small>运营天数</small><br><a href="javascript:void(0)" class="themed-color-flat"><span id="count_yxts"></span>天</a></h5>-->
	<!--	</div>-->
	<!--</div>-->
	<!--底部导航-->
				<div class="block-content text-center border-t">
		<a href="javascript:void(0);" onclick="AddFavorite('QQ代刷网',location.href)">
  <b style="text-shadow: LightSteelBlue 1px 0px 0px;">
  <i class="fa fa-heart text-danger animation-pulse"></i>
  <font color=#CB0034>本</font>
  <font color=#BE0041>站</font>
  <font color=#B1004E>网</font>
  <font color=#A4005B>址</font>
  <font color=#970068>：<?php echo $_SERVER['HTTP_HOST'];?></font>
  <font color=#2F00D0></font>
  <font color=#CB0034>&nbsp;</font>
  <font color=#CB0034>建</font>
  <font color=#BE0041>议</font>
  <font color=#B1004E>收</font>
  <font color=#A4005B>藏</font>
  </b>
</a><br><br>
<?php echo $conf['footer']?>
<!-- 修复LA.init错误 -->
<script>
// 检查LA是否已定义，如果未定义则创建一个空函数
if (typeof LA === 'undefined') {
    window.LA = {
        init: function() {
            console.log('LA.init called but LA library not loaded');
        }
    };
}
// 延迟执行LA.init，确保库加载完成
setTimeout(function() {
    try {
        if (typeof LA !== 'undefined' && LA.init) {
            LA.init({id: "JTj6MWryNtZKd9e5",ck: "JTj6MWryNtZKd9e5"});
        }
    } catch(e) {
        console.warn('LA.init执行失败:', e);
    }
}, 2000);
</script>
<span style="font-size:14px;font-weight:700;color:#E53333;background-color:#FFE500;font-family:&quot;"><span style="color:#FF9900;background-color:#FFFFFF;font-size:12px;"><strong><img src="https://pan.suyanw.cn/view.php/7dbc5423eb3fdf545811e5ea032c84a8.gif" width="8%" height="8%" alt="" />项目/上架/对接/批卡/请联系在线客服<img src="https://pan.suyanw.cn/view.php/7dbc5423eb3fdf545811e5ea032c84a8.gif" width="8%" height="8%" alt="" /></strong></span></span>
			</div><br>
                                            <center>
                                                <img src="https://pan.suyanw.cn/view.php/9511f86349582ca1c605f353d2b72ac2.jpg" style="max-height: 26px; height: auto; width: auto;"></img>
                                                
                                                <img src="https://pan.suyanw.cn/view.php/d1e978792c2b796a04514a277fa72b5c.jpg" style="max-height: 26px; height: auto; width: auto;"></img>
                                                
                                                <img src="https://pan.suyanw.cn/view.php/0c28f568861d37e9e58f2a22bba2506a.jpg" style="max-height: 26px; height: auto; width: auto;"></img>
                                                
                                                <img src="https://pan.suyanw.cn/view.php/dc1f6a276f1f6a05bd7afd504ce182b7.jpg" style="max-height: 26px; height: auto; width: auto;"></img>
                                            </center>
	<!--底部导航-->
</div>
</div>
</font></div><font color="#000000">

<!-- 收藏代码开始-->
<script>
    function AddFavorite(title, url) {
  try {
      window.external.addFavorite(url, title);
  }
catch (e) {
     try {
       window.sidebar.addPanel(title, url, "");
    }
     catch (e) {
         alert("手机用户：点击底部 "≡" 添加书签/收藏网址!\n\n电脑用户：请您按 Ctrl+D 手动收藏本网址! ");
     }
  }
}
</script>
<!-- 收藏代码结束-->



<!--音乐代码-->
<div id="audio-play" <?php if(empty($conf['musicurl'])){?>style="display:none;"<?php }?>>
  <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')">
    <audio loop="loop" src="<?php echo $conf['musicurl']?>" id="media" preload="preload"></audio>
  </div>
</div>
<!--音乐代码-->
<!-- 
  严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
 -->
  <!-- 非关键JavaScript异步加载 - 使用更快的CDN -->
  <script src="//lib.baomitu.com/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
  <script src="//lib.baomitu.com/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
  <script src="//lib.baomitu.com/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="<?php echo $cdnserver?>assets/appui/js/app.js"></script>

  <!-- 延迟加载非关键功能 -->
  <script>
  // 页面完全加载后执行非关键功能
  window.addEventListener('load', function() {
      // 延迟加载懒加载功能
      setTimeout(function() {
          try {
              if(typeof $.fn.lazyload !== 'undefined') {
                  $("img.lazy").lazyload({
                      effect: "fadeIn",
                      threshold: 200
                  });
              }
          } catch(e) {
              console.warn('lazyload初始化失败:', e);
          }
      }, 500);
      
      // 延迟加载其他功能
      setTimeout(function() {
          console.log('页面完全加载完成');
      }, 1000);
  });
  </script>
	  	<script type="text/javascript">
  		var isModal = <?php echo empty($conf['modal']) ? 'false' : 'true'; ?> ;
  		var homepage = true;
  		var hashsalt = <?php echo $addsalt_js ?> ;
  		
  		// 错误处理和调试
  		window.addEventListener('error', function(e) {
  		    console.error('JavaScript错误:', e.error);
  		});
  		
  		$(function() {
  		    // 检查jQuery插件是否加载
  		    try {
  		        if(typeof $.fn.lazyload !== 'undefined') {
  		            $("img.lazy").lazyload({
  		                effect: "fadeIn"
  		            });
  		        } else {
  		            console.warn('lazyload插件未加载');
  		        }
  		        
  		        // 检查Bootstrap tooltip是否可用
  		        if(typeof $.fn.tooltip !== 'undefined') {
  		            $('[data-toggle="tooltip"]').tooltip();
  		        }
  		    } catch(e) {
  		        console.warn('插件初始化失败:', e);
  		    }
  		});
		var ss = 0,
		    mm = 0,
		    hh = 0;
		
		function TimeGo() {
		    ss++;
		    if (ss >= 60) {
		        mm += 1;
		        ss = 0
		    }
		    if (mm >= 60) {
		        hh += 1;
		        mm = 0
		    }
		    ss_str = (ss < 10 ? "0" + ss : ss);
		    mm_str = (mm < 10 ? "0" + mm : mm);
		    tMsg = "" + hh + "小时" + mm_str + "分" + ss_str + "秒";
		    
		    // 检查元素是否存在，避免null错误
		    var stimeElement = document.getElementById("stime");
		    if (stimeElement) {
		        stimeElement.innerHTML = tMsg;
		    }
		    
		    setTimeout("TimeGo()", 1000);
		}
		
		// 只有在页面加载完成后才启动计时器
		$(document).ready(function() {
		    TimeGo();
		  		});
  </script>
<?php /* 暂时注释掉classblock，文件不存在 */ ?>
<?php include_once SYSTEM_ROOT.'sakura.php'; loadSakuraEffect(); /* loadChatWidget(); */ ?>
<?php if(function_exists('loadChatWidget')) loadChatWidget(); ?>

</body>
</html>

<script type="text/javascript">
/* 鼠标特效 */
var a_idx = 0;
jQuery(document).ready(function($) {
    $("body").click(function(e) {
        var a = new Array("❤富强❤","❤民主❤","❤文明❤","❤和谐❤","❤自由❤","❤平等❤","❤公正❤","❤法治❤","❤爱国❤","❤敬业❤","❤诚信❤","❤友善❤");
        var $i = $("<span></span>").text(a[a_idx]);
        a_idx = (a_idx + 1) % a.length;
        var x = e.pageX,
        y = e.pageY;
        $i.css({
            "z-index": 999999999999999999999999999999999999999999999999999999999999999999999,
            "top": y - 20,
            "left": x,
            "position": "absolute",
            "font-weight": "bold",
            "color": "rgb("+~~(255*Math.random())+","+~~(255*Math.random())+","+~~(255*Math.random())+")"
        });
        $("body").append($i);
        $i.animate({
            "top": y - 180,
            "opacity": 0
        },
        1500,
        function() {
            $i.remove();
        });
    });
});
</script>
<div style=" z-index:9999; text-decoration:none; font-weight:bold; position: fixed; z-index: 999; Left: -6px; bottom: 250px; display: inline-block; width: 20px; border-top-left-radius: 10px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 10px; color: white; font-size: 17px; line-height: 17px; box-shadow: rgb(100, 149, 237) 0px 0px 5px; word-wrap: break-word; padding: 8px 13px; border: 2px solid white; background: rgb(242, 12, 12);"><a href="toollogs.php" target="_blank" style="position: relative;left: -7px;top: 2px; color:#fff;">今日上架通知</a></div>



<!--初音未来开始-->
<style>
.cywl {
    position: fixed!important;
    position: absolute;
    width: 70px;
    height: 75px;
    z-index: 9;
    right: 0;
    bottom: 0;
    top: expression(offsetParent.scrollTop+offsetParent.clientHeight-150);
    cursor: pointer;
}
</style>
<div id="audio" class="cywl">
<img src="https://pan.suyanw.cn/view.php/8d17edd6a110994cecfe21688b3e63f0.gif" width="65px" height="65px" id="d" onclick="c();">
</div>
<!--初音未来结束-->

</font></div></aside></div></body>

<script>
$(function(){
  // 阻止分站按钮的默认 tab 切换行为，强制跳转
  $('.fenzhan-jump').off('click').on('click', function(e){
    e.preventDefault();
    e.stopPropagation();
    window.open('./user/regsite.php', '_blank');
    return false;
  });
  
  // 确保分站按钮不会被 Bootstrap tab 系统拦截
  $(document).on('click', '.fenzhan-jump', function(e){
    e.preventDefault();
    e.stopPropagation();
    window.open('./user/regsite.php', '_blank');
    return false;
  });
  
  // 获取URL参数函数 - 博客地址:6v6.ren q群941535592
  function getUrlParam(name) {
      var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
      var r = window.location.search.substr(1).match(reg);
      if (r != null) return decodeURIComponent(r[2]);
      return '';
  }
  
  // 检测buyok参数并显示订单弹窗 - 博客地址:6v6.ren q群941535592
  if(getUrlParam('buyok') == '1'){
      console.log('检测到buyok参数，准备显示订单弹窗');
      
      // 直接从URL获取订单ID和skey参数
      var orderid = getUrlParam('orderid');
      var skey = getUrlParam('skey');
      
      // 如果有订单ID和skey参数，直接显示订单详情
      if(orderid && skey && typeof showOrder === 'function'){
          console.log('使用订单ID和skey显示订单:', orderid, skey);
          showOrder(orderid, skey);
      }
      // 否则，为了保持兼容性，仍然通过查询获取最新订单
      else {
          var searchtype = getUrlParam('searchtype') || 1;
          var qq = getUrlParam('qq') || '';
          
          console.log('通过查询获取最新订单，searchtype:', searchtype, 'qq:', qq);
          
          // 先检查$_GET变量的类型，确保正确处理
          if (typeof window.$_GET === 'function') {
              // 保存原始的$_GET函数
              var original_GET = window.$_GET;
              // 创建一个临时的$_GET函数，移除buyok参数
              window.$_GET = function() {
                  var url = window.document.location.href.toString();
                  var u = url.split("?");
                  if(typeof(u[1]) == "string"){
                      u = u[1].split("&");
                      var get = {};
                      for(var i in u){
                          var j = u[i].split("=");
                          get[j[0]] = j[1];
                      }
                      // 关键修改：将buyok设置为0，禁用showOrder调用
                      get['buyok'] = 0;
                      return get;
                  } else {
                      return {};
                  }
              };
          }
          
          // 临时设置querymode，禁用查询弹窗显示
          var tempQuerymode = window.querymode;
          window.querymode = 'noPopup';
          
          // 执行订单查询
          if(typeof queryOrder === 'function'){
              queryOrder(searchtype, qq, 1);
          } else {
              console.error('queryOrder函数未定义');
          }
          
          // 恢复原始设置
          setTimeout(function() {
              window.querymode = tempQuerymode;
              if (typeof original_GET !== 'undefined') {
                  window.$_GET = original_GET;
              }
          }, 100);
      }
  }
});
</script>

