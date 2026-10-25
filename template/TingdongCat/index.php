
    <!--// +----------------------------+-->
    <!--// | Date:<?=date("m月d号")?>-->
    <!--// +----------------------------+-->
   <!--
                   _ooOoo_
                  o8888888o
                  88" . "88
                  (| -_- |)
                  O\  =  /O
               ____/`---'\____
             .'  \\|     |//  `.
            /  \\|||  :  |||//  \
           /  _||||| -:- |||||-  \
           |   | \\\  -  /// |   |
           | \_|  ''\---/''  |   |
           \  .-\__  `-`  ___/-. /
         ___`. .'  /--.--\  `. . __
      ."" '<  `.___\_<|>_/___.'  >'"".
     | | :  `- \`.;`\ _ /`;.`/ - ` : | |
     \  \ `-.   \_ __\ /__ _/   .-` /  /
======`-.____`-.___\_____/___.-`____.-'======
                   `=---='
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    佛祖保佑       永不宕机     永无BUG
-->
<?php
if(!defined('IN_CRONLITE'))exit();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
  <title><?php echo $hometitle?></title>
  <meta name="keywords" content="<?php echo $conf['keywords']?>">
  <meta name="description" content="<?php echo $conf['description']?>">
  <link rel="icon" href="<?php echo $cdnserver?>assets/img/favicon/favicon.ico" type="image/x-icon" />
  <link rel="shortcut icon" href="<?php echo $cdnserver?>assets/img/favicon/favicon.ico" type="image/x-icon" />
  <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/simple/css/plugins.css">
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/simple/css/main.css">
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/simple/css/oneui.css">
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>">
  <script src="<?php echo $cdnpublic?>modernizr/2.8.3/modernizr.min.js"></script>
  <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
<?php echo $background_css?>
<style>
/* 全局主色调 */
:root {
  --primary-color: #9ED3F9;
  --hover-color: #8BC4E9;
}

/* 四个大区块统一样式 */
.widget, .block.full2, .block.block-themed, .block:last-child {
  border-radius: 12px !important;
  box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
  overflow: hidden;
  transition: transform 0.3s ease;
}
.widget:hover, .block.full2:hover {
  transform: translateY(-3px);
}

/* 顶块按钮组 */


/* 文章区块 */
.list-group-item {
  border-radius: 10px !important;
  margin: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
}
.list-group-item:hover {
  transform: translateX(10px);
  background: rgba(158,211,249,0.1);
}
.btn-default[target="_blank"] {
  background: var(--primary-color) !important;
  color: white !important;
  border-radius: 12px;
  margin：0 auto；
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.payment-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin: 20px 0;
}
.buybutton {
    background: white;
    border: 2px solid #9ED3F9;
    border-radius: 12px;
    padding: 11px;
    width: 100%;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #2c3e50;
}
.buybutton:hover {
    background: #9ED3F9;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(158, 211, 249, 0.5);
}
/* 新增公告、登录注册按钮样式 */
.btn-group.themed-background-muted {
    background: white !important;
    border-radius: 15px !important;
    box-shadow: 0 4px 20px rgba(158, 211, 249, 0.2) !important;
    border: none !important;
    overflow: hidden;
    
}
.btn-group.themed-background-muted .btn {
    border-radius: 0 !important;
    border: none !important;
    background: white !important;
    position: relative;
    margin: 0 -1px !important;
    transition: all 0.3s ease;
}
.btn-group.themed-background-muted .btn:not(:last-child)::after {
    content: "";
    position: absolute;
    right: 0;
    top: 25%;
    height: 50%;
    width: 1px;
    background: rgba(158, 211, 249, 0.3);
}
.btn-group.themed-background-muted .btn:hover {
    background: var(--hover-color) !important;
    z-index: 2;
    box-shadow: 0 0 15px rgba(158, 211, 249, 0.3);
}

/* 底部统计数字颜色 */
.widget-heading .themed-color-flat,
#count_orders, #count_orders2, #count_yxts {
    color: #6CB2EB !important;
}
.widget-heading small {
    color: #7F8C8D !important;
}

/* 区块辉光动画 */
.widget, .block.full2, .block.block-themed {
    position: relative;
    will-change: transform, box-shadow;
    animation: glow 3s ease-in-out infinite;
}
@keyframes glow {
    0%, 100% { box-shadow: 0 8px 20px rgba(158, 211, 249, 0.15); }
    50% { box-shadow: 0 8px 30px rgba(158, 211, 249, 0.3); }
}
/* 磨砂质感弹窗 */
#anounce .modal-content {
    background: rgba(255, 255, 255, 0.85) !important;
    backdrop-filter: blur(10px);
    border-radius: 15px !important;
    border: 1px solid rgba(158, 211, 249, 0.3);
    box-shadow: 0 8px 32px rgba(158, 211, 249, 0.2);
}
#anounce .modal-header {
    border-bottom: 1px dashed rgba(158, 211, 249, 0.3) !important;
}
#anounce .modal-footer {
    border-top: 1px dashed rgba(158, 211, 249, 0.3) !important;
}
#anounce .btn-default {
    background: rgba(158, 211, 249, 0.2) !important;
    border: 1px solid var(--primary-color) !important;
}
.cc1 {height: 160px;}
@media (min - width: 768px) {
    .cc1 {height: 370px;
}
}
.wrapper {border: 1.3px dashed #B0E2FF;border-radius: 20px;padding: 16px;width: auto;margin: 0 auto;}
.themed-background1 {background:#6CB2EB;}
.themed-background2 {background:#6CB2EB;}
</style>

</head>
<body>
<?php if($background_image){?>
<img src="<?php echo $background_image;?>" alt="Full Background" class="full-bg full-bg-bottom animated pulse " ondragstart="return false;" oncontextmenu="return false;">
<?php }?>
<br />
<div class="col-xs-12 col-sm-10 col-md-8 col-lg-5 center-block" style="float: none;">
<!--弹出公告-->
<div class="modal fade" align="left" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header-tabs">
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
        <h4 class="modal-title" id="myModalLabel">平台公告</h4>
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
<div class="widget">
<!--logo-->
    <div class="widget-content themed-background-flat text-center cc1" <?php if(isset($conf['background_image_enable']) && $conf['background_image_enable'] == 1 && ((isset($conf['background_style']) && $conf['background_style'] == 'image') || (isset($conf['background_type']) && $conf['background_type'] == 'image'))){ echo 'style="background-image: url(/bj.png);background-size: cover"'; } ?>>
		<img src="https://haowallpaper.com/link/common/file/previewFileImg/18415461775887744" alt="Banner" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; opacity: 0.9;">
    </div>
<!--logo-->
<!--logo下面按钮-->
	<div class="widget-content themed-background-muted text-center" style="Background:#ff0000">
	    <div class="wrapper">
		<div class="btn-group themed-background-muted ">
			<a href="#anounce" data-toggle="modal" class="btn btn-effect-ripple btn-default collapsed "><b><img src="./assets/img/suyan3.png" width="18" height="auto"> <span style="font-weight:bold">必看公告</span></b></a>
			<?php if($islogin2==1){?>
			<a href="./user/" class="btn btn-effect-ripple btn-default"><img src="/assets/img/suyan5.png" width="18" height="auto"> <span style="font-weight:bold">用户后台</span></a>
			<?php }else{?>
			<a href="./user/login.php" class="btn btn-effect-ripple btn-default"><img src="./assets/img/suyan4.png" width="18" height="auto"> <span style="font-weight:bold">客户登录</span></a>
			<a href="./user/reg.php" class="btn btn-effect-ripple btn-default"><img src="./assets/img/suyan9.png" width="18" height="auto"> <span style="font-weight:bold">客户注册</span></a>
			<?php }?>
		</div>
	</div>
      
		<div id="mustsee" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
          <div id="mustsee" class="panel-collapse collapse in" aria-expanded="true" style="">
		
          </div>
		</div>
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
<div style="background-color:#fff;border-radius: 25px;box-shadow: 0px 0px 3px #ffffff;padding:5px;margin-top: 10px;margin-bottom:0px;display:none;">
    <marquee>
    	<b id="nr">公告：本站拥有全网最全精品三角洲辅助！以及其他端游游戏辅助！诚信经营,价格最低,货源最全,卡密问题质保可退换,放心下单即可! 有任何问题在本站客户中心提交工单即可受理！百分百售后！</b>
    </marquee>



</div>
<div class="block full2">
<!--TAB标签-->
	<div class="block-title">

        <ul class="nav nav-tabs" data-toggle="tabs">
  
            <li style="width: 25%;" align="center" class="active"><a href="#shop" data-toggle="tab"><span style="font-weight:bold"><img src="./assets/img/suyan7.png" width="20px" height="auto">下单</span></a></li>
            <li style="width: 25%;" align="center"><a href="#search" data-toggle="tab" id="tab-query"><span style="font-weight:bold"><i class="fa fa-search" style="font-size:16px;color:#9ED3F9;"></i> 查单</span></a></li>
            
            <li style="width: 25%;" align="center"><a href="#more" data-toggle="tab"><span style="font-weight:bold"><img src="./assets/img/suyan10.png" width="20px"> 更多</span></a></li>
            
			
        </ul>
    </div>
    
<!--TAB标签-->
    <div class="tab-content">
 <div class="tab-pane" id="daohang">	
       <?php if(file_exists(TEMPLATE_ROOT.'default/nav.inc.php'))include TEMPLATE_ROOT.'default/nav.inc.php'; ?>
  	</div>
<!--在线下单-->
    <div class="tab-pane active" id="shop">	
       <?php include TEMPLATE_ROOT.'TingdongCat/assets/shop.inc.php'; ?>
  	</div>
  	
<!--在线下单-->
<!--查询订单-->
    <div class="tab-pane" id="search">
		<div class="col-xs-12 well well-sm animation-pullUp" <?php if(empty($conf['gg_search'])){?>style="display:none;"<?php }?><?php echo isset($conf['gg_search']) ? $conf['gg_search'] : '';?></div>
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-btn">
						<select class="form-control" id="searchtype" style="padding: 6px 4px;width:90px"><option value="0">下单账号</option><option value="1">订单号</option></select>
					</div>
					<input type="text" name="qq" id="qq3" value="<?php echo isset($qq) ? $qq : '';?>" class="form-control" placeholder="可不填直接查询" onkeydown="if(event.keyCode==13){submit_query.click()}" required/>
				</div>
			</div>
			<div class="payment-buttons">
			<input type="submit" id="submit_query" class="btn btn-primary btn-block buybutton" value="立即查询">
			</div>
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
<!--查询订单-->
<!--更多-->
<style>
.gengduoicon {
    background: #FFFFFF;
}
</style>
    <div class="tab-pane" id="more">
		<div class="col-sm-6">
            <a  href="./toollogs.php" target="_blank" class="widget">
                <div class="widget-content themed-background2 text-right clearfix" style="color: #fff;">
                    <div class="widget-icon pull-left gengduoicon">
                        <img src="<?php echo $cdnserver?>./assets/img/anquan.png" width="30" height="auto">
                    </div>
                    <h2 class="widget-heading h3">
                        <strong>上架日志</strong>
                    </h2>
                    <span>可以查看商品动态</span>
                </div>
            </a>
            </div>
            <div class="col-sm-6">
            <a  href="/sup/login.php" target="_blank" class="widget">
                <div class="widget-content themed-background1 text-right clearfix" style="color: #fff;">
                    <div class="widget-icon pull-left gengduoicon">
                        <img src="./assets/img/anquan.png" width="30" height="auto">
                    </div>
                    <h2 class="widget-heading h3">
                        <strong>供货中心</strong>
                    </h2>
                    <span>可以自助上架商品售卖</span>
                </div>
            </a>
                       </div>
            <div class="col-sm-6">
            <a  href="/user/reg.php" target="_blank" class="widget">
                <div class="widget-content themed-background1 text-right clearfix" style="color: #fff;">
                    <div class="widget-icon pull-left gengduoicon">
                        <img src="./assets/img/anquan.png" width="30" height="auto">
                    </div>
                    <h2 class="widget-heading h3">
                        <strong>注册账号</strong>
                    </h2>
                    <span>有问题方便售后</span>
                </div>
            </a>
                        </div>
            <div class="col-sm-6">
            <a  href="/user/login.php" target="_blank" class="widget">
                <div class="widget-content themed-background1 text-right clearfix" style="color: #fff;">
                    <div class="widget-icon pull-left gengduoicon">
                        <img src="./assets/img/anquan.png" width="30" height="auto">
                    </div>
                    <h2 class="widget-heading h3">
                        <strong>用户中心</strong>
                    </h2>
                    <span>注册本站账号会员</span>
                </div>
            </a>
        </div>
	</div>
	
<!--更多-->



    </div>
</div>

<?php 
// 无论$conf['articlenum']设置如何，都显示文章列表，且至少显示5个
$limit = intval($conf['articlenum']) > 0 ? intval($conf['articlenum']) : 5;
if($limit < 5) $limit = 5; // 确保至少显示5个文章
$rs=$DB->query("SELECT id,title FROM " . DBQZ . "article WHERE active=1 ORDER BY top DESC,id DESC LIMIT {$limit}");
$msgrow=array();
while($res = $rs->fetch()){
	$msgrow[]=$res;
}
$class_arr = ['danger','warning','primary','success','info'];
$i=0;
?>
<!--文章列表-->
<div class="block block-themed">
	<div class="block-title">
		<h4><img src="<?php echo $cdnserver?>assets/img/dingdan.png" width="25" height="auto"> 本站公告</h4>
	</div>
	<?php foreach($msgrow as $row){
	echo '<a target="_blank" class="list-group-item" href="'.article_url($row['id']).'"><span class="btn btn-'.$class_arr[($i++)%5].' btn-xs">'.$i.'</span>&nbsp;'.$row['title'].'</a>';
	}?>
	<br><a href="<?php echo article_url()?>" title="查看全部文章" class="btn-default btn btn-block" target="_blank">查看全部文章</a><p></p>
</div>
<!--文章列表-->

</div>
<!--音乐代码-->
<div id="audio-play" <?php if(empty($conf['musicurl'])){?>style="display:none;"<?php }?>>
  <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')">
    <audio loop="loop" src="<?php echo $conf['musicurl']?>" id="media" preload="preload"></audio>
  </div>
</div>
<!--音乐代码-->

<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
<script src="<?php echo $cdnpublic?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="<?php echo $cdnserver?>assets/appui/js/app.js"></script>
<script type="text/javascript">
var isModal=<?php echo empty($conf['modal'])?'false':'true';?>;
var homepage=true;
var hashsalt=<?php echo $addsalt_js?>;
// 确保DOM加载完成后初始化lazyload
$(document).ready(function() {
    // 初始化lazyload
    if(typeof $.fn.lazyload !== 'undefined') {
        $("img.lazy").lazyload({effect: "fadeIn"});
    } else {
        // 降级处理：如果lazyload不可用，直接加载所有图片
        $("img.lazy").each(function() {
            $(this).attr('src', $(this).data('original')).removeClass('lazy');
        });
    }
});
</script>
<script src="<?php echo $cdnserver?>assets/js/main.js?ver=<?php echo VERSION ?>"></script>
<?php if($conf['classblock']==1 || $conf['classblock']==2 && checkmobile()==false)include TEMPLATE_ROOT.'default/classblock.inc.php'; ?>
<div class="col-xs-12 col-sm-10 col-md-8 col-lg-5 center-block" style="float: none;margin-top:15px;">
<div class="block animated bounceInDown btn-rounded" style="border:1px solid #FFF0F5;font-size:15px;padding:15px;border-radius:15px;background-color: white;">
<div class="panel-heading"><h3 class="panel-title" types=""><font color="#9ED3F9"><span class="glyphicon glyphicon-stats"></span>&nbsp;&nbsp;<b>今日订单详细</b><img src="https://z3.ax1x.com/2021/06/19/RCRtyD.gif"/></font></h3></div>
<div class="btn-group btn-group-justified">
			<a target="_blank" class="btn btn-effect-ripple btn-default collapsed" style="overflow: hidden; position: relative;"><b><font color="white">购买用户</font></b></a>
			<a target="_blank" class="btn btn-effect-ripple btn-default collapsed" style="overflow: hidden; position: relative;"><b><font color="white">下单日期</font></b></a>
			<a target="_blank" class="btn btn-effect-ripple btn-default collapsed" style="overflow: hidden; position: relative;"><b><font color="white">物品名称</font></b></a>
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
                            <td width="30%" style="color: #7F8C8D;">本站用户' . rand(10, 999) . '**' . rand(100, 999) . '**</td>
                            <td width="30%" style="color: #7F8C8D;">于' . $date . '日下单成功</td>
                            <td width="40%" style="color: #7F8C8D;">' . $name . '</td>
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </marquee>
        </div>
</div>

<!--关于我们弹窗-->
<div class="col-xs-12 col-sm-10 col-md-8 col-lg-5 center-block" style="float: none;">
<div class="block">
	<!--网站日志-->
	<?php if(!isset($conf['hide_tongji']) || !$conf['hide_tongji']){?>
	<div class="row text-center">
		<div class="col-xs-4">
		    
			<h5 class="widget-heading"><small>订单总数</small><br><a href="javascript:void(0)" class="themed-color-flat"><span id="count_orders"></span>条</a></h5>
		</div>
		<div class="col-xs-4">
			 <h5 class="widget-heading"><small>今日订单</small><br><a href="javascript:void(0)" class="themed-color-flat"><span id="count_orders2"></span>条</a></h5>
		</div>
		<div class="col-xs-4">
			<h5 class="widget-heading"><small>运营天数</small><br><a href="javascript:void(0)" class="themed-color-flat"><span id="count_yxts"></span>天</a></h5>
			
		</div>
	</div>
	<?php }?>
	<!--网站日志-->
	<!--底部导航-->
	<div class="block-content text-center border-t">
		<p><span style="font-weight:bold;color:#9ED3F9"><?php echo $conf['sitename'] ?> <i class="fa fa-heart text-danger"><!--ccc-->
			</i> <?php echo date("Y")?></span><br/><?php echo $conf['footer']?></p>
	</div>
	<!--底部导航-->
</div>
</div>
</body>
</html>