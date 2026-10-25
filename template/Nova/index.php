<?php
if(!defined('IN_CRONLITE'))exit();

if((isset($_GET['buyok']) && $_GET['buyok']==1) || (isset($_GET['chadan']) && $_GET['chadan']==1)){include_once TEMPLATE_ROOT.'default/query.php';exit;}
if(isset($_GET['tid']) && !empty($_GET['tid']))
{
	$tid=intval($_GET['tid']);
    $tool=$DB->getRow("select tid from pre_tools where tid='$tid' limit 1");
    if($tool)
    {
		exit("<script language='javascript'>window.location.href='./?mod=buy&tid=".$tool['tid']."';</script>");
    }
}

if($islogin2==1){
	$price_obj = new \lib\Price($userrow['zid'],$userrow);
}elseif($is_fenzhan == true){
	$price_obj = new \lib\Price($siterow['zid'],$siterow);
}else{
	$price_obj = new \lib\Price(1);
}

// 主题色
$style_map = [
	'1' => ['primary'=>'#165DFF','primary_hover'=>'#0E42D2','primary_light'=>'#E8F3FF','name'=>'blue'],
	'2' => ['primary'=>'#00B42A','primary_hover'=>'#009A29','primary_light'=>'#E8FFEA','name'=>'green'],
	'3' => ['primary'=>'#FF7D00','primary_hover'=>'#D25F00','primary_light'=>'#FFF3E8','name'=>'orange'],
	'4' => ['primary'=>'#722ED1','primary_hover'=>'#531DAB','primary_light'=>'#F3E8FF','name'=>'purple'],
	'5' => ['primary'=>'#F53F3F','primary_hover'=>'#CB2634','primary_light'=>'#FFECE8','name'=>'red'],
];
$tpl_style = isset($conf['template_style']) && isset($style_map[$conf['template_style']]) ? $conf['template_style'] : '1';
$theme = $style_map[$tpl_style];
$show_sales = isset($conf['template_showsales']) ? $conf['template_showsales'] : '1';

// 分类
$classhide = explode(',',$siterow['class']);
$classes = [];
$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 ORDER BY sort ASC");
while($res = $rs->fetch()){
	if($is_fenzhan && in_array($res['cid'], $classhide))continue;
	$classes[] = $res;
}

$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;

// 商品
$tools = [];
if($cid > 0){
	$rs2 = $DB->query("SELECT * FROM pre_tools WHERE cid='$cid' AND active=1 ORDER BY sort ASC");
}else{
	$rs2 = $DB->query("SELECT * FROM pre_tools WHERE active=1 ORDER BY sort ASC LIMIT 50");
}
while($row = $rs2->fetch()){
	$price_obj->setToolInfo($row['tid'],$row);
	if($price_obj->getToolDel($row['tid'])==1)continue;
	$row['price'] = $price_obj->getToolPrice($row['tid']);
	$row['is_auto'] = ($row['is_curl']==1||$row['is_curl']==2||$row['is_curl']==4);
	if($row['is_curl']==4){
		$row['stock'] = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid='{$row['tid']}' and orderid=0");
	}
	$tools[] = $row;
}

$current_class_name = '';
if($cid > 0){
	$current_class_name = $DB->getColumn("select name from pre_class where cid='$cid' limit 1");
}

$hero_title = !empty($conf['hero_title']) ? $conf['hero_title'] : $conf['sitename'];
$hero_subtitle = !empty($conf['hero_subtitle']) ? $conf['hero_subtitle'] : '专业的数字商品交易平台，安全、快捷、可靠';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
	<title><?php echo $hometitle?></title>
	<meta name="keywords" content="<?php echo $conf['keywords']?>">
	<meta name="description" content="<?php echo $conf['description']?>">
	<?php if(!empty($conf['favicon'])){?>
	<link rel="icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
	<?php }else{?>
	<link rel="icon" href="assets/img/favicon/favicon.ico" type="image/x-icon" />
	<?php }?>
	<link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	<link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
	<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
	<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
	<style>
	:root{
		--primary:<?php echo $theme['primary']?>;
		--primary-hover:<?php echo $theme['primary_hover']?>;
		--primary-light:<?php echo $theme['primary_light']?>;
	}
	*{margin:0;padding:0;box-sizing:border-box}
	body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Hiragino Sans GB","Microsoft YaHei",sans-serif;background:#F5F6FA;color:#333;line-height:1.6}
	a{text-decoration:none;color:inherit}
	img{max-width:100%}

	/* Header */
	.nova-header{background:#1F2937;color:#fff;position:sticky;top:0;z-index:1000;box-shadow:0 2px 8px rgba(0,0,0,.1)}
	.nova-header-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;padding:0 20px;height:60px}
	.nova-logo{display:flex;align-items:center;gap:10px;font-size:20px;font-weight:700}
	.nova-logo img{height:32px;border-radius:6px}
	.nova-nav{display:flex;align-items:center;gap:8px}
	.nova-nav a{padding:8px 14px;border-radius:6px;font-size:14px;transition:all .2s}
	.nova-nav a:hover{background:rgba(255,255,255,.1)}
	.nova-nav a.active{background:var(--primary)}
	.nova-header-right{display:flex;align-items:center;gap:12px}
	.nova-search{display:flex;align-items:center;background:rgba(255,255,255,.1);border-radius:20px;padding:6px 14px}
	.nova-search input{background:transparent;border:none;outline:none;color:#fff;width:180px;font-size:13px}
	.nova-search input::placeholder{color:rgba(255,255,255,.6)}
	.nova-search button{background:none;border:none;color:rgba(255,255,255,.8);cursor:pointer;margin-left:6px}

	/* Notice bar */
	.nova-notice{background:#fff;border-bottom:1px solid #eee;padding:8px 20px;font-size:13px;color:#666;overflow:hidden}
	.nova-notice-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:8px}
	.nova-notice i{color:var(--primary)}

	/* Hero */
	.nova-hero{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-hover) 100%);color:#fff;padding:60px 20px;text-align:center}
	.nova-hero h1{font-size:36px;margin-bottom:12px;font-weight:700}
	.nova-hero p{font-size:16px;opacity:.9;max-width:600px;margin:0 auto}
	.nova-hero-stats{display:flex;justify-content:center;gap:40px;margin-top:30px}
	.nova-hero-stat{text-align:center}
	.nova-hero-stat .num{font-size:28px;font-weight:700}
	.nova-hero-stat .label{font-size:13px;opacity:.85}

	/* Container */
	.nova-container{max-width:1200px;margin:0 auto;padding:24px 20px}

	/* Section title */
	.nova-section-title{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
	.nova-section-title h2{font-size:20px;font-weight:600}
	.nova-section-title h2 i{color:var(--primary);margin-right:6px}
	.nova-section-title a{color:var(--primary);font-size:13px}

	/* Categories */
	.nova-categories{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:30px}
	.nova-cat-card{background:#fff;border-radius:12px;padding:18px;text-align:center;cursor:pointer;transition:all .25s;border:2px solid transparent}
	.nova-cat-card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.08);border-color:var(--primary)}
	.nova-cat-card.active{border-color:var(--primary);background:var(--primary-light)}
	.nova-cat-icon{width:48px;height:48px;margin:0 auto 10px;border-radius:12px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:22px}
	.nova-cat-name{font-size:14px;font-weight:500;margin-bottom:4px}
	.nova-cat-count{font-size:12px;color:#999}

	/* Breadcrumb */
	.nova-breadcrumb{font-size:13px;color:#999;margin-bottom:16px}
	.nova-breadcrumb a{color:var(--primary)}
	.nova-breadcrumb a:hover{text-decoration:underline}

	/* Products */
	.nova-products{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
	.nova-product-card{background:#fff;border-radius:12px;overflow:hidden;transition:all .25s;border:1px solid #eee}
	.nova-product-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.1);transform:translateY(-2px)}
	.nova-product-img{height:160px;background:linear-gradient(135deg,var(--primary-light),#fff);display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
	.nova-product-img img{max-height:100%;object-fit:contain}
	.nova-product-tag{position:absolute;top:10px;left:10px;padding:3px 10px;border-radius:4px;font-size:11px;font-weight:500}
	.nova-product-tag.auto{background:var(--primary);color:#fff}
	.nova-product-tag.manual{background:#FF7D00;color:#fff}
	.nova-product-body{padding:14px}
	.nova-product-name{font-size:14px;font-weight:500;margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
	.nova-product-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
	.nova-product-price{color:var(--primary);font-size:20px;font-weight:700}
	.nova-product-price small{font-size:12px;font-weight:400}
	.nova-product-stock{font-size:12px;color:#999}
	.nova-product-btn{display:block;width:100%;padding:10px;text-align:center;background:var(--primary);color:#fff;border-radius:8px;font-size:14px;font-weight:500;transition:background .2s;border:none;cursor:pointer}
	.nova-product-btn:hover{background:var(--primary-hover);color:#fff}
	.nova-product-btn.disabled{background:#ccc;cursor:not-allowed}

	/* Empty */
	.nova-empty{text-align:center;padding:60px 20px;color:#999}
	.nova-empty i{font-size:48px;margin-bottom:12px;opacity:.4}

	/* Footer */
	.nova-footer{background:#1F2937;color:rgba(255,255,255,.7);padding:40px 20px 20px;margin-top:40px}
	.nova-footer-inner{max-width:1200px;margin:0 auto}
	.nova-footer-top{display:flex;flex-wrap:wrap;gap:40px;margin-bottom:30px}
	.nova-footer-col{flex:1;min-width:200px}
	.nova-footer-col h4{color:#fff;font-size:15px;margin-bottom:14px;font-weight:600}
	.nova-footer-col p,.nova-footer-col a{font-size:13px;line-height:2;color:rgba(255,255,255,.6)}
	.nova-footer-col a:hover{color:var(--primary)}
	.nova-footer-bottom{border-top:1px solid rgba(255,255,255,.1);padding-top:20px;text-align:center;font-size:12px;color:rgba(255,255,255,.5)}

	/* Responsive */
	@media(max-width:768px){
		.nova-nav{display:none}
		.nova-search{display:none}
		.nova-hero h1{font-size:24px}
		.nova-hero-stats{gap:20px}
		.nova-hero-stat .num{font-size:20px}
		.nova-categories{grid-template-columns:repeat(auto-fill,minmax(140px,1fr))}
		.nova-products{grid-template-columns:1fr}
		.nova-footer-top{flex-direction:column;gap:24px}
	}
	</style>
</head>
<body>

<!-- Header -->
<header class="nova-header">
	<div class="nova-header-inner">
		<a href="./" class="nova-logo">
			<img src="<?php echo $logo?>" alt="logo" onerror="this.style.display='none'">
			<span><?php echo $conf['sitename']?></span>
		</a>
		<nav class="nova-nav">
			<a href="./" class="<?php echo $cid==0?'active':''?>">首页</a>
			<a href="./?mod=query">订单查询</a>
			<?php if($islogin2==1){?>
			<a href="./user/">用户中心</a>
			<a href="./user/logout.php">退出</a>
			<?php }else{?>
			<a href="./user/login.php">登录</a>
			<a href="./user/reg.php">注册</a>
			<?php }?>
		</nav>
		<div class="nova-header-right">
			<div class="nova-search">
				<input type="text" id="novaSearch" placeholder="搜索商品..." onkeydown="if(event.key==='Enter')doSearch()">
				<button onclick="doSearch()"><i class="fa fa-search"></i></button>
			</div>
		</div>
	</div>
</header>

<?php if(!empty($conf['header_notice'])){?>
<div class="nova-notice">
	<div class="nova-notice-inner">
		<i class="fa fa-volume-up"></i>
		<span><?php echo $conf['header_notice']?></span>
	</div>
</div>
<?php }?>

<?php if($cid==0){?>
<!-- Hero -->
<section class="nova-hero">
	<h1><?php echo $hero_title?></h1>
	<p><?php echo $hero_subtitle?></p>
	<div class="nova-hero-stats">
		<div class="nova-hero-stat">
			<div class="num"><?php echo count($classes)?>+</div>
			<div class="label">商品分类</div>
		</div>
		<div class="nova-hero-stat">
			<div class="num"><?php echo $DB->getColumn("SELECT count(*) FROM pre_tools WHERE active=1")?>+</div>
			<div class="label">在售商品</div>
		</div>
		<div class="nova-hero-stat">
			<div class="num">24/7</div>
			<div class="label">自动发货</div>
		</div>
	</div>
</section>
<?php }?>

<div class="nova-container">

<?php if($cid > 0){?>
<div class="nova-breadcrumb">
	<a href="./">首页</a> / <span><?php echo $current_class_name?></span>
</div>
<?php }?>

<!-- Categories -->
<?php if(!empty($classes)){?>
<div class="nova-section-title">
	<h2><i class="fa fa-th-large"></i>商品分类</h2>
</div>
<div class="nova-categories">
	<a href="./" class="nova-cat-card <?php echo $cid==0?'active':''?>">
		<div class="nova-cat-icon"><i class="fa fa-th"></i></div>
		<div class="nova-cat-name">全部商品</div>
		<div class="nova-cat-count">共 <?php echo $DB->getColumn("SELECT count(*) FROM pre_tools WHERE active=1")?> 件</div>
	</a>
	<?php foreach($classes as $cls){
		$cnt = $DB->getColumn("SELECT count(*) FROM pre_tools WHERE cid={$cls['cid']} AND active=1");
		$icon = !empty($cls['shopimg']) ? '<img src="'.$cls['shopimg'].'" style="width:100%;height:100%;object-fit:contain;border-radius:12px">' : '<i class="fa fa-folder-o"></i>';
	?>
	<a href="./?cid=<?php echo $cls['cid']?>" class="nova-cat-card <?php echo $cid==$cls['cid']?'active':''?>">
		<div class="nova-cat-icon"><?php echo $icon?></div>
		<div class="nova-cat-name"><?php echo $cls['name']?></div>
		<div class="nova-cat-count">共 <?php echo $cnt?> 件</div>
	</a>
	<?php }?>
</div>
<?php }?>

<!-- Products -->
<div class="nova-section-title">
	<h2><i class="fa fa-shopping-bag"></i><?php echo $cid>0?$current_class_name:'全部商品'?></h2>
	<a href="./?mod=query"><i class="fa fa-search"></i> 订单查询</a>
</div>

<?php if(!empty($tools)){?>
<div class="nova-products">
	<?php foreach($tools as $t){
		$img = !empty($t['shopimg']) ? $t['shopimg'] : 'assets/img/Product/default.png';
		$tag = $t['is_auto'] ? '<span class="nova-product-tag auto">自动发货</span>' : '<span class="nova-product-tag manual">手动发货</span>';
		$stock_text = '';
		if($t['is_curl']==4 || $t['stock']!==null){
			$stock_text = '库存: '.intval($t['stock']);
		}
	?>
	<div class="nova-product-card">
		<div class="nova-product-img">
			<?php echo $tag?>
			<img src="<?php echo $img?>" alt="<?php echo htmlspecialchars($t['name'])?>" onerror="this.src='assets/img/Product/default.png'">
		</div>
		<div class="nova-product-body">
			<div class="nova-product-name"><?php echo htmlspecialchars($t['name'])?></div>
			<div class="nova-product-meta">
				<div class="nova-product-price"><small>¥</small><?php echo $t['price']?></div>
				<?php if($show_sales=='1' && !empty($stock_text)){?><div class="nova-product-stock"><?php echo $stock_text?></div><?php }?>
			</div>
			<?php if($t['close']==1){?>
			<button class="nova-product-btn disabled" disabled>商品维护中</button>
			<?php }else{?>
			<a href="./?mod=buy&tid=<?php echo $t['tid']?>" class="nova-product-btn">立即购买</a>
			<?php }?>
		</div>
	</div>
	<?php }?>
</div>
<?php }else{?>
<div class="nova-empty">
	<i class="fa fa-inbox"></i>
	<p>暂无商品，请稍后再来</p>
</div>
<?php }?>

</div>

<!-- Footer -->
<footer class="nova-footer">
	<div class="nova-footer-inner">
		<div class="nova-footer-top">
			<div class="nova-footer-col">
				<h4><?php echo $conf['sitename']?></h4>
				<p><?php echo $hero_subtitle?></p>
			</div>
			<div class="nova-footer-col">
				<h4>快速链接</h4>
				<p><a href="./">首页</a></p>
				<p><a href="./?mod=query">订单查询</a></p>
				<?php if($islogin2==1){?>
				<p><a href="./user/">用户中心</a></p>
				<?php }?>
			</div>
			<div class="nova-footer-col">
				<h4>联系方式</h4>
				<?php if(!empty($conf['kfqq'])){?><p>客服QQ：<?php echo $conf['kfqq']?></p><?php }?>
				<?php if(!empty($conf['kfwx'])){?><p>客服微信：<?php echo $conf['kfwx']?></p><?php }?>
				<?php if(!empty($conf['kfemail'])){?><p>邮箱：<?php echo $conf['kfemail']?></p><?php }?>
			</div>
		</div>
		<div class="nova-footer-bottom">
			<p>Copyright © <?php echo date('Y')?> <?php echo $conf['sitename']?> All Rights Reserved.</p>
			<?php if(!empty($conf['icp'])){?><p style="margin-top:6px"><a href="https://beian.miit.gov.cn/" target="_blank"><?php echo $conf['icp']?></a></p><?php }?>
		</div>
	</div>
</footer>

<script>
function doSearch(){
	var kw = document.getElementById('novaSearch').value.trim();
	if(!kw){layer.msg('请输入搜索关键词');return}
	window.location.href='./?keyword='+encodeURIComponent(kw);
}
</script>
</body>
</html>

