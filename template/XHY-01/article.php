<?php
if(!defined('IN_CRONLITE'))exit();

$id=isset($_GET['id'])?intval($_GET['id']):sysmsg('文章ID不存在');
$row=$DB->getRow("select * from " . DBQZ . "article where id='$id' and active=1 limit 1");
if(!$row)
	sysmsg('当前文章不存在！');

// 确保文章存在后再继续执行
if(!$row) {
	exit();
}
$downResult = $DB->getRow("SELECT * FROM " . DBQZ . "article WHERE id<'$id' AND active=1 ORDER BY id DESC LIMIT 1");
$upResult = $DB->getRow("SELECT * FROM " . DBQZ . "article WHERE id>'$id' AND active=1 ORDER BY id DESC LIMIT 1");
$DB->exec("UPDATE `" . DBQZ . "article` SET `count`=`count`+1 WHERE id='$id'");

// 调试信息
echo "<!-- 调试: 数据库前缀=" . DBQZ . ", 文章ID=" . $id . ", 文章标题=" . $row['title'] . " -->";
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <title><?php echo $row['title']?> - <?php echo $conf['sitename']?></title>
	<meta name="description" content="<?php echo $row['description']?>"/>
    <meta name="keywords" content="<?php echo $row['keywords']?>"/>
    <?php if(!empty($conf['favicon'])){?>
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
    <?php }?>
    <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php echo $cdnserver?>assets/simple/css/oneui.css">
	<link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>">
    <script src="<?php echo $cdnpublic?>modernizr/2.8.3/modernizr.min.js"></script>
    <!--[if lt IE 9]>
      <script src="<?php echo $cdnpublic?>html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="<?php echo $cdnpublic?>respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<style type="text/css">
	.form-control {color: #646464;border: 1px solid #f8f8f8;border-radius: 3px;-webkit-box-shadow: none;box-shadow: none;-webkit-transition: all 0.15s ease-out;transition: all 0.15s ease-out;}
	.block{margin-bottom:10px;background-color:#fff;-webkit-box-shadow:0 2px 17px 2px rgb(222,223,241);box-shadow:0 2px 17px 2px rgb(222,223,241);font-weight:400}
	ul.ft-link{margin:0;padding:0}
	ul.ft-link li{border-right:1px solid #E6E7EC;display:inline-block;line-height:30px;margin:8px 0;text-align:center;width:24%}
	ul.ft-link li a{color:#74829c;text-transform:uppercase;font-size:12px}
	ul.ft-link li a:hover,ul.ft-link li.active a{color:#58c9f3}
	ul.ft-link li:last-child{border-right:none}
	ul.ft-link li a i{display:block}
	.well {min-height: 20px;padding: 19px;margin-bottom: 15px;background-color: #f9f9f9;border: 1px solid #e3e3e3;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);}
	.input-group-addon {color: #646464;background-color: #f9f9f9;border-color: #f9f9f9;border-radius: 3px;}
	.panel-primary {border-color: #ffffff;}
	::-webkit-scrollbar-thumb {-webkit-box-shadow: inset 1px 1px 0 rgba(0,0,0,.1), inset 0 -1px 0 rgba(0,0,0,.07);background-clip: padding-box;background-color: #1bc74c;min-height: 40px;padding-top: 100px;border-radius: 4px;}
	.panel-primary {border-color: #ffffff;}
	.block > .nav-tabs > li.active > a, .block > .nav-tabs > li.active > a:hover, .block > .nav-tabs > li.active > a:focus {color: #ffffff !important;background-color: #8B0000;border-color: transparent;border-radius: 15px !important;padding: 10px 15px !important;line-height: 1.5 !important;z-index: 10 !important;overflow: visible !important;}
.block > .nav-tabs > li > a {color: #8B0000 !important;border-radius: 15px !important;padding: 10px 15px !important;line-height: 1.5 !important;z-index: 5 !important;overflow: visible !important;}
.block > .nav-tabs > li:not(.active) > a:hover {background-color: #FFCCCC !important;}
	.btn-info{color:#ffffff;background-color:#4098f2;border-color:#ffffff}
	.btn{font-weight:100;-webkit-transition:all 0.15s ease-out;transition:all 0.15s ease-out}
	.btn-sm,.btn-group-sm > .btn{padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px}
	.btn-primary{color:#ffffff;background-color:rgb(64,152,242);border-color:rgb(64,152,242)}
	.bg-image {background-color: #ffffff;background-position: center center;background-repeat: no-repeat;-webkit-background-size: cover;background-size: cover;}
.nav-btn {color: #8B0000;background: linear-gradient(to right, #ffffff, #ffcccc);border: 2px solid #8B0000;border-radius: 25px !important;font-weight: 600;-webkit-transition: all 0.15s ease-out;transition: all 0.15s ease-out;margin: 0 2px;padding: 8px 16px;display: block !important;float: none !important;width: 100% !important;text-align: center;}
.nav-btn:hover {background: linear-gradient(to right, #fff8f8, #ffb3b3);border-color: #8B0000;}
/* 修复btn-group-justified导致的圆角问题 */
.btn-group-justified > .btn-group .nav-btn {
    border-radius: 25px !important;
    margin: 0 4px;
}
	.article-content img {
            max-width: 100% !important;
        }
	.article-content {
		line-height: 1.8;
		color: #333;
		padding: 20px;
		background: #FFF8F8;
		border: 1px solid #D8A0A0;
		border-radius: 8px;
		margin: 20px 0;
	}
	.article-content h1, .article-content h2, .article-content h3 {
		color: #8B0000;
		margin-top: 20px;
		margin-bottom: 15px;
	}
	.article-content p {
		margin-bottom: 15px;
	}
	.article-content ul, .article-content ol {
		margin-bottom: 15px;
		padding-left: 30px;
	}
	.article-content li {
		margin-bottom: 8px;
	}
	.article-content a {
		color: #8B0000;
		text-decoration: underline;
	}
	.article-content a:hover {
		color: #CD5C5C;
	}
	</style>
</head>
<body>
<br/>
<div class="col-xs-12 col-sm-10 col-md-8 col-lg-6" style="margin: 0 auto; float: none;">
    <div class="block" style="border-radius: 8px; border: 1px solid #D8A0A0;">
        <div class="block-title" style="background: #FFE6E6;
			border-radius: 8px 8px 0 0;
			padding: 15px;
			border-bottom: 2px solid #D8A0A0;">
            <h2><i class="fa fa-list"></i>&nbsp;&nbsp;<b style="color: #8B0000;">文章内容</b></h2>
        </div>
<ol class="breadcrumb" style="margin: 20px;">
	<li>
		<a href="./">首页</a>
	</li>
	<li>
		<a href="<?php echo article_url()?>">文章列表</a>
	</li>
	<li class="active"><?php echo $row['title']?></li>
</ol>
<div class="text-center" style="margin: 20px;">
<h3><strong style="color: #8B0000;"><?php echo $row['title']?></strong></h3>
<span class="text-muted"><i class="glyphicon glyphicon-time"></i>&nbsp;<?php echo $row['addtime']?>&nbsp;&nbsp;&nbsp;<i class="fa fa-mouse-pointer" aria-hidden="true"></i>&nbsp;<?php echo $row['count']?></span>
</div><hr/>
<div class="article-content">
<?php echo $row['content']?>
</div>

		<div style="margin-bottom: 30px;margin-top: 20px;padding: 0 20px;">
            <p style="margin: 0;">
                上一篇：<?php echo empty($upResult) ? '没有了~' : ('<a href="'.article_url($upResult['id']).'">' . $upResult['title'] . '</a>'); ?>
            </p>
            <p style="margin: 0;">
                下一篇：<?php echo empty($downResult) ? '没有了~' : ('<a href="'.article_url($downResult['id']).'">' . $downResult['title'] . '</a>'); ?>
            </p>
        </div>
			<hr>
			<div class="form-group" style="padding: 0 20px 30px;">
			<a href="./" class="btn nav-btn"><i class="fa fa-home"></i>&nbsp;返回首页</a>
			<a href="<?php echo article_url()?>" class="btn nav-btn" style="margin-top: 10px;"><i class="fa fa-list"></i>&nbsp;返回列表</a>
			</div>
        </div>
      </div>
    <script src="https://lib.baomitu.com/jquery/3.5.1/jquery.min.js"></script>
	<script src="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
	<script src="<?php echo $cdnpublic?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
	<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
</body>
</html>