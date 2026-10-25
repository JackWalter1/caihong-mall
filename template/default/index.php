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
  <?php if(!empty($conf['favicon'])){?>
  <link rel="icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
  <link rel="shortcut icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
  <?php }?>
  
  <!-- 预加载关键资源 -->
  <link rel="preload" href="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js" as="script">
  <link rel="preload" href="assets/js/main.js" as="script">
  
  <!-- 关键CSS优先加载 -->
  <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnserver?>assets/css/nifty.min.css" rel="stylesheet">
  <link href="<?php echo $cdnserver?>assets/css/magic-check.min.css" rel="stylesheet">
  <link href="<?php echo $cdnserver?>assets/css/pace.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>">
  
  <!-- 关键JavaScript立即加载 - 来自6v6.ren -->
  <script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
  <script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
  <script src="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
  <!-- ============================================ -->
  <!-- 🔥 课程查询强制修复 - 纯原生 JavaScript + 终极防护 -->
  <!-- ============================================ -->
  <script>
  console.log('🔥 课程查询强制修复加载...');
  
  // 1. 定义真正的查询函数
  function doQuery() {
    console.log('🚀 执行查询...');
    
    // 纯原生获取值
    var input1 = document.getElementById('inputvalue');
    var input2 = document.getElementById('inputvalue2');
    var input3 = document.getElementById('inputvalue3');
    
    var user = input1 ? input1.value || '' : '';
    var pwd = input2 ? input2.value || '' : '';
    var school = input3 ? input3.value || '' : '';
    
    user = user.trim();
    pwd = pwd.trim();
    school = school.trim();
    
    console.log('获取的值:', {user, pwd, school});
    
    if (!user || !pwd || !school) {
      var msg = '请完整填写信息！';
      if (!user) msg += ' 登录账号';
      if (!pwd) msg += ' 登录密码';
      if (!school) msg += ' 学校名字';
      if (typeof layer !== 'undefined') {
        layer.alert(msg, {icon: 2, title: '提示'});
      } else {
        alert(msg);
      }
      return;
    }
    
    var url = 'cx.php?user=' + encodeURIComponent(user) + '&pwd=' + encodeURIComponent(pwd) + '&school=' + encodeURIComponent(school);
    console.log('跳转URL:', url);
    
    window.open(url, '_blank');
  }
  
  // 2. 立即定义 dzb_ck
  window.dzb_ck = doQuery;
  
  // 3. 持续启用输入框 + 绑定点击事件
  function fixAndBind() {
    // 启用输入框
    ['inputvalue', 'inputvalue2', 'inputvalue3', 'inputvalue4'].forEach(function(id) {
      var el = document.getElementById(id);
      if (el && el.disabled) {
        el.disabled = false;
        el.removeAttribute('disabled');
      }
    });
    
    // 找到查询按钮并强绑定
    var btn = document.getElementById('dzb_ck');
    if (btn) {
      if (btn.disabled) {
        btn.disabled = false;
        btn.removeAttribute('disabled');
      }
      btn.style.opacity = '1';
      btn.style.cursor = 'pointer';
      
      // 终极防护：覆盖 onclick 属性并直接绑定事件
      btn.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('🔥 按钮被点击，执行查询');
        doQuery();
        return false;
      };
      
      // 同时也用 addEventListener
      if (!btn._queryBound) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          console.log('🔥 addEventListener 触发');
          doQuery();
          return false;
        });
        btn._queryBound = true;
      }
    }
    
    // 持续覆盖 dzb_ck 函数，防止被加密代码覆盖
    window.dzb_ck = doQuery;
  }
  
  // 页面加载完成后立即启动
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      console.log('✅ 启用输入框...');
      setInterval(fixAndBind, 100); // 更频繁检查
    });
  } else {
    console.log('✅ 启用输入框...');
    setInterval(fixAndBind, 100); // 更频繁检查
  }
  
  console.log('✅ 强制修复完成！');
  </script>
  <script src="assets/js/main.js?ver=<?php echo VERSION ?>"></script>
  
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
  
  <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <style>
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
  <!-- 鼠标美化效果 -->
  <?php if(isset($conf['mouse_beautify']) && $conf['mouse_beautify'] == 1){
      $mouse_style = $conf['mouse_style'] ?? 'default';
  ?>
  <style>
    /* 自定义鼠标样式 */
    <?php if($mouse_style == 'cute'){?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff69b4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff69b4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'modern'){?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%2300bfff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="%2300bfff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php } elseif($mouse_style == 'game'){?>
    body {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff4500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>'), auto;
    }
    a, button, input, select, textarea {
      cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%23ff4500" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>'), pointer;
    }
    <?php }
    ?>
  </style>
  <?php }
  ?>
</head>
<body>
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
<br/>
<div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 center-block" style="float: none;">
<div class="panel panel-default">
	<div class="panel-body" style="text-align: center;">
		<img src="<?php echo isset($logo) ? $logo : 'assets/img/logo.png';?>" style="max-width: 100%;">
	</div>
</div>
<div class="panel panel-primary">
	<div class="panel-heading"><h3 class="panel-title" ><font color="#FFFFFF"><i class=""></i><b> <script type="text/javascript">
var now=(new Date()).getHours();
if(now>0&&now<=6){
document.write("❤熬夜对身体不好哦 快睡觉！");
}else if(now>6&&now<=11){
document.write("❤早上好 心情好来下一单吧~");
}else if(now>11&&now<=14){
document.write("❤停下手中的工作 去吃饭~");
}else if(now>14&&now<=18){
document.write("❤累了一上午了 休息会吧~");
}else{
document.write("❤晚上好 下一单醒来有惊喜哟~");
}
</script></font> </b></h3></div>
	<div>
<?php echo $conf['anounce']?>
	</div>
</div>

<div class="tab-content">
	<div id="demo-tabs-box-1" class="tab-pane fade active in">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><font color="#fff"><i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;<b>自助下单</b></font><span class="pull-right"><a href="./user/" class="btn btn-warning btn-rounded"><i class="fa fa-user"></i> 用户中心</a></span></h3>
			</div>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#onlinebuy" data-toggle="tab"><i class="fa fa-shopping-cart"></i> 下单</a></li>
		<li><a href="#query" data-toggle="tab" id="tab-query"><i class="fa fa-search"></i> 查单</a></li>
		<?php if(isset($conf['gift_open']) && $conf['gift_open']==1){?><li><a href="#gift" data-toggle="tab"><i class="fa fa-gift"></i> 抽奖</a></li><?php }?>
		<?php if(!empty($conf['daiguaurl'])){?><li><a href="./?mod=daigua"><i class="fa fa-rocket"></i> 代挂</a></li><?php }?>
		<?php if($conf['fenzhan_buy']==1){?><li><a href="./user/regsite.php" style="color:red">开通分站</a></li><?php }?>
	</ul>
	<div class="modal-body">
		<div id="myTabContent" class="tab-content">
		<div class="tab-pane fade in active" id="onlinebuy">
<?php include TEMPLATE_ROOT.'default/shop.inc.php'; ?>
		</div>
		<div class="tab-pane fade in" id="query">
			<div class="alert alert-info" <?php if(empty($conf['gg_search'])){?>style="display:none;"<?php }?>><?php echo isset($conf['gg_search']) ? $conf['gg_search'] : '';?></div>
			<div class="form-group">
				<div class="input-group">
				<div class="input-group-btn">
					<select class="form-control" id="searchtype" style="padding: 6px 4px;width:90px"><option value="0">下单账号</option><option value="1">订单号</option></select>
				</div>
				<input type="text" name="qq" id="qq3" value="<?php echo isset($qq) ? $qq : '';?>" class="form-control" placeholder="请输入要查询的内容（留空则显示最新订单）" onkeydown="if(event.keyCode==13){submit_query.click()}" required/>
				<span class="input-group-btn"><a tabindex="0" class="btn btn-default" role="button" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" title="查询内容是什么？" data-content="请输入您下单时，在第一个输入框内填写的信息。如果您不知道下单账号是什么，可以不填写，直接点击查询，则会根据浏览器缓存查询！"><i class="glyphicon glyphicon-exclamation-sign"></i></a></span>
			</div></div>
			<input type="submit" id="submit_query" class="btn btn-primary btn-block" value="立即查询">
			<div id="result2" class="form-group" style="display:none;">
				<div class="table-responsive">
				<table class="table table-striped">
				<thead><tr><th>下单账号</th><th>商品名称</th><th>数量</th><th class="hidden-xs">购买时间</th><th>状态</th><th>操作</th></tr></thead>
				<tbody id="list">
				</tbody>
				</table>
				</div>
			</div>
		</div>
		<?php if(isset($conf['gift_open']) && $conf['gift_open']==1){?><div class="tab-pane fade in" id="gift">
			<div class="panel-body text-center">
			<div id="roll">点击下方按钮开始抽奖</div>
			<hr>
			<p>
			<a class="btn btn-info" id="start" style="display:block;">开始抽奖</a>
			<a class="btn btn-danger" id="stop" style="display:none;">停止</a>
			</p> 
			<div id="result"></div><br/>
			<div class="giftlist" style="display:none;"><strong>最近中奖记录</strong><ul id="pst_1"></ul></div>
			</div>
		</div>
		</div><?php }?>
	</div>
</div>
</div>
	
</div>

<?php if($conf['articlenum']>0){
$limit = intval($conf['articlenum']);
$rs=$DB->query("SELECT id,title FROM pre_article WHERE active=1 ORDER BY top DESC,id DESC LIMIT {$limit}");
$msgrow=array();
while($res = $rs->fetch()){
	$msgrow[]=$res;
}
$class_arr = ['danger','warning','primary','success','info'];
$i=0;
?>
<!--文章列表-->
<div class="panel panel-primary" <?php if($conf['bottom']==''){?>style="display:none;"<?php }?>>
<div class="panel-heading"><h3 class="panel-title"><font color="#fff"><i class="fa fa-newspaper-o"></i>&nbsp;&nbsp;<b>文章列表</b></font></h3></div>
	<?php foreach($msgrow as $row){
	echo '<a target="_blank" style="color:blue" class="list-group-item" href="'.article_url($row['id']).'"><span class="btn btn-'.$class_arr[($i++)%5].' btn-xs">'.$i.'</span>&nbsp;'.$row['title'].'</a>';
	}?>
	<a href="<?php echo article_url()?>" title="查看全部文章" class="btn-default btn btn-block" target="_blank">查看全部文章</a>
</div>
<!--文章列表-->
<?php }?>

<?php if(!isset($conf['hide_tongji']) || !$conf['hide_tongji']){?>
<div class="row">
	<div class="col-lg-6">
	<div class="panel panel-success panel-colorful">
			<div class="pad-all media">
				<div class="media-left">
					<i class="demo-pli-coin icon-3x icon-fw"></i>
				</div>
				<div class="media-body">
					<p class="h3 text-light mar-no media-heading"><span id="count_money"></span>元</p>
					<span>累计交易金额</span>
				</div>
			</div>
			<div class="progress progress-xs progress-success mar-no">
				<div class="progress-bar progress-bar-light" style="width: 100%"></div>
			</div>
			<div class="pad-all text-sm">
				今天交易金额 <span class="text-semibold" id="count_money1"></span> 元
			</div>
		</div>
	</div>
	<div class="col-lg-6">
	<div class="panel panel-info panel-colorful">
			<div class="pad-all media">
				<div class="media-left">
					<i class="demo-pli-add-cart icon-3x icon-fw"></i>
				</div>
				<div class="media-body">
					<p class="h3 text-light mar-no media-heading"><span id="count_orders"></span>条</p>
					<span>累计订单总数</span>
				</div>
			</div>
			<div class="progress progress-xs progress-dark-base mar-no">
				<div class="progress-bar progress-bar-light" style="width: 100%"></div>
			</div>
			<div class="pad-all text-sm bg-trans-dark">
				今天订单总数 <span class="text-semibold" id="count_orders2"></span> 条
			</div>
		</div>
	</div>
</div>
<?php }?>

<div class="panel panel-primary" <?php if(!isset($conf['bottom']) || $conf['bottom']==''){?>style="display:none;"<?php }?>>
<div class="panel-heading"><h3 class="panel-title"><font color="#fff"><i class="fa fa-skyatlas"></i>&nbsp;&nbsp;<b>站点助手</b></font></h3></div>
<?php echo isset($conf['bottom']) ? $conf['bottom'] : '';?>
</div>
</div>
<p style="text-align:center"><span style="font-weight:bold">CopyRight <i class="fa fa-heart text-danger"></i> <?php echo date("Y")?> <a href="/"><?php echo $conf['sitename']?></a></span><br/><?php echo $conf['footer']?></p>
<!--音乐代码-->
<div id="audio-play" <?php if(empty($conf['musicurl'])){?>style="display:none;"<?php }?>>
  <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')">
    <audio loop="loop" src="<?php echo $conf['musicurl']?>" id="media" preload="preload"></audio>
  </div>
</div>
<!--音乐代码-->

<!-- 非关键JavaScript异步加载 -->
<script src="<?php echo $cdnpublic?>jquery.lazyload/1.9.1/jquery.lazyload.min.js" async></script>
<script src="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/js/bootstrap.min.js" async></script>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js" async></script>
<script src="<?php echo $cdnserver?>assets/js/pace.min.js" async></script>

<!-- 延迟加载非关键功能 -->
<script>
// 页面完全加载后执行非关键功能
window.addEventListener('load', function() {
    // 延迟加载懒加载功能
    if(typeof $.fn.lazyload !== 'undefined') {
        $("img.lazy").lazyload({
            effect: "fadeIn",
            threshold: 200
        });
    }
    
    // 延迟加载其他功能
    setTimeout(function() {
        console.log('页面完全加载完成');
    }, 1000);
});
</script>

<!-- 变量定义 -->
<script type="text/javascript">
var isModal = <?php echo empty($conf['modal']) ? 'false' : 'true'; ?>;
var modalShowType = <?php echo isset($conf['modal_show_type']) ? $conf['modal_show_type'] : '0'; ?>;
var homepage = true;
var hashsalt = <?php echo $addsalt_js; ?>;

// 弹出公告控制 - 来自6v6.ren
$(document).ready(function() {
    if (isModal) {
        // 检查是否需要显示公告
        if (modalShowType == 0) {
            // 每次进网站都弹
            $('#myModal').modal('show');
        } else if (modalShowType == 1) {
            // 只弹一次
            var modalShown = localStorage.getItem('modal_shown');
            if (!modalShown) {
                $('#myModal').modal('show');
                localStorage.setItem('modal_shown', '1');
            }
        }
    }
    
    // 购买成功后自动显示订单详情弹窗
    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return decodeURIComponent(r[2]);
        return '';
    }
    
    if(getUrlParam('buyok') == '1'){
        // 切换到查询标签页
        $("#tab-query").tab("show");
        
        // 延迟执行查询，确保标签页已切换
        setTimeout(function(){
            $("#submit_query").click();
        }, 500);
    }
});
</script>

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
LA.init({id: "JTj6MWryNtZKd9e5",ck: "JTj6MWryNtZKd9e5"});
</script>

<?php if($conf['classblock']==1 || $conf['classblock']==2 && checkmobile()==false)include TEMPLATE_ROOT.'default/classblock.inc.php'; ?>
<?php include_once SYSTEM_ROOT.'sakura.php'; loadSakuraEffect(); /* loadChatWidget(); */ ?>
<?php if(function_exists('loadChatWidget')) loadChatWidget(); ?>

</body>
</html>