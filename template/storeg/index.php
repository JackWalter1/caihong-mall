<?php
if (!defined('IN_CRONLITE')) die();

if((isset($_GET['buyok']) && $_GET['buyok']==1) || (isset($_GET['chadan']) && $_GET['chadan']==1)){include_once TEMPLATE_ROOT.'store/query.php';exit;}
if(isset($_GET['tid']) && !empty($_GET['tid']))
{
	$tid=intval($_GET['tid']);
    $tool=$DB->getRow("select tid from pre_tools where tid='$tid' limit 1");
    if($tool)
    {
		exit("<script language='javascript'>window.location.href='./?mod=buy&tid=".$tool['tid']."';</script>");
    }
}

$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
if(!$cid && !empty($conf['defaultcid']) && $conf['defaultcid']!=='0'){
	$cid = intval($conf['defaultcid']);
}
$ar_data = []; // 一级分类
$sub_classes = []; // 二级分类
$classhide = explode(',',$siterow['class']);
$re = $DB->query("SELECT * FROM `pre_class` WHERE `active` = 1 ORDER BY `sort` ASC ");
$qcid = "";
$cat_name = "";
while ($res = $re->fetch()) {
    if($is_fenzhan && in_array($res['cid'], $classhide))continue;
    if($res['cid'] == $cid){
    	$cat_name=$res['name'];
    	$qcid = $cid;
    }
    // 如果有pid字段，使用pid区分一级和二级分类
    if(isset($res['pid']) && $res['pid'] > 0){
        $sub_classes[$res['pid']][] = $res;
    }else{
        $ar_data[] = $res;
    }
}


$class_show_num = isset($conf['index_class_num_style'])&&intval($conf['index_class_num_style'])?intval($conf['index_class_num_style']):2; //分类展示几组
?>
<!DOCTYPE html>
<html lang="zh" style="font-size: 102.4px;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no"/>
    <script> document.documentElement.style.fontSize = document.documentElement.clientWidth / 750 * 40 + "px";</script>
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-param" content="_csrf">
    <title><?php echo $hometitle?></title>
    <meta name="keywords" content="<?php echo $conf['keywords'] ?>">
    <meta name="description" content="<?php echo $conf['description'] ?>">
    <?php if(!empty($conf['favicon'])) { ?>
    <link rel="icon" href="<?php echo $conf['favicon'] ?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $conf['favicon'] ?>" type="image/x-icon" />
    <?php } else { ?>
    <link rel="icon" href="assets/img/favicon/favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="assets/img/favicon/favicon.ico" type="image/x-icon" />
    <?php } ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/foxui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/foxui.diy.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/iconfont.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/index.css">
    
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnpublic ?>layui/2.5.7/css/layui.css">
    <link href="<?php echo $cdnpublic?>Swiper/6.4.5/swiper-bundle.min.css" rel="stylesheet">


    <?php echo str_replace('body','html',$background_css)?>
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
<style type="text/css">
.fui-goods-item{
    padding: .35rem;
}
.fui-goods-item .image {
    width: 3.2rem;
    height: 3.2rem;
}
.msglistbox{
    margin-left: 50px;
    color: #fff;
    padding: 9px 19px;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 10px;
}
    body {
        position: absolute;;

        margin: auto;
    }


    .fui-page.fui-page-from-center-to-left,
    .fui-page-group.fui-page-from-center-to-left,
    .fui-page.fui-page-from-center-to-right,
    .fui-page-group.fui-page-from-center-to-right,
    .fui-page.fui-page-from-right-to-center,
    .fui-page-group.fui-page-from-right-to-center,
    .fui-page.fui-page-from-left-to-center,
    .fui-page-group.fui-page-from-left-to-center {
        -webkit-animation: pageFromCenterToRight 0ms forwards;
        animation: pageFromCenterToRight 0ms forwards;
    }

    .fix-iphonex-bottom {
        padding-bottom: 34px;
    }

    .fui-goods-item .detail .price .buy {
        color: #fff;
        background: #1492fb;
        border-radius: 3px;
        line-height: 1.1rem;
    }

      
      
      
      
      
      
      
      
      
    
    .fui-goods-item .detail .sale {
        height: 1.7rem;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        font-size: 0.65rem;
        line-height: 0.9rem;
    }
    .goods-category {
        display: flex;
        background: #fff;
        flex-wrap: wrap;
    }

    .goods-category li {
        width: 25%;
        list-style: none;
        margin: 0.4rem 0;
        color: #666;
        font-size: 0.65rem;

    }

    .goods-category li.active p {
        background: #1492fb;
        color: #fff;
    }

    body {
        padding-bottom: constant(safe-area-inset-bottom);
        padding-bottom: env(safe-area-inset-bottom);
    }

    .goods-category li p {
        width: 5rem;
        height: 2rem;
        text-align: center;
        line-height: 2rem;
        border: 1px solid #ededed;
        margin: 0 auto;
        -webkit-border-radius: 0.1rem;
        -moz-border-radius: 0.1rem;
        border-radius: 0.1rem;
    }
    .footer ul {
        display: flex;
        width: 100%;
        margin: 0 auto;
    }

    .footer ul li {
        list-style: none;
        flex: 1;
        text-align: center;
        position: relative;
        line-height: 2rem;
    }

    .footer ul li:after {
        content: '';
        position: absolute;
        right: 0;
        top: .8rem;
        height: 10px;
        border-right: 1px solid #999;


    }

    .footer ul li:nth-last-of-type(1):after {
        display: none;
    }

    .footer ul li a {
        color: #999;
        display: block;
        font-size: .6rem;
    }
.fui-goods-group.block .fui-goods-item .image {
     width: 100%; 
     margin: unset; 
     padding-bottom: unset; 
             height:5rem;
          

}
.layui-flow-more{
        width: 100%;
    float: left;
}
.fui-goods-group .fui-goods-item .image img{
    border-radius:5px;    
}
.fui-goods-group .fui-goods-item .detail .minprice {
    font-size: .6rem;
}
.fui-goods-group .fui-goods-item .detail .name{
    height: 1.4rem;
}
.fui-goods-group .fui-goods-item .detail .tag{
    height: 1.4rem;
}
.fui-goods-group .fui-goods-item .detail .tag img{
    transform: translateY(-2px);
}















.swiper-pagination-bullet {
  width: 18px;
  height: 18px;
  text-align: center;
  line-height: 17px;
  font-size: 10px;
  color: #000;
  opacity: 1;
  background: rgba(0, 0, 0, 0.2);
}

.swiper-pagination-bullet-active {
  color: #fff;
  background: #ed414a;
}
.swiper-pagination{
    position: unset;
}

.annc-bar {
    position: relative;
    display: flex;
    align-items: center;
    cursor: pointer;
    height: 35px;
    background: #f9f9f9;
    border: 1px solid #e1e1e1;
    box-shadow: 0 2px 4px 0 rgba(0,0,0,.02);
    border-radius: 8px;
    line-height: 19px;
    font-size: 30px;
    color: red;
    font-weight: 600;
    padding: 0 7px 0 8px;
}
.swiper-container{
    --swiper-theme-color: #ff6600;/* 设置Swiper风格 */
    --swiper-navigation-color: #007aff;/* 单独设置按钮颜色 */
    --swiper-navigation-size: 18px;/* 设置按钮大小 */
}
.goods_sort {
    position: relative;
    width: 100%;

    -webkit-box-align: center;
    padding: .4rem 0;
    background: #fff;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center;
}

.goods_sort:after {
    content: " ";
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    border-bottom: 1px solid #e7e7e7;
}

.goods_sort .item {
    position: relative;
    width: 1%;
    display: table-cell;
    text-align: center;
    font-size: 0.1rem;
    border-left: 1px solid #e7e7e7;
    color: #666;
}
.goods_sort .item .sorting {
    width: .2rem;
    height: .2rem;
    position: relative;
}
.goods_sort .item:first-child {
    border: 0;
}

.goods_sort .item.on .text {
    color: #fd5454;
}
.goods_sort .item .sorting .icon {
    /*font-size: 11px;*/
    position: absolute;
    -webkit-transform: scale(0.6);
    -ms-transform: scale(0.6);
    transform: scale(0.6);
}

.goods_sort .item-price .sorting .icon-sanjiao1 {
    top: .15rem;
    left: 0;
}

.goods_sort .item-price .sorting .icon-sanjiao2 {
    top: -.15rem;
    left: 0;
}

.goods_sort .item-price.DESC .sorting .icon-sanjiao1 {
    color: #ef4f4f
}

.goods_sort .item-price.ASC .sorting .icon-sanjiao2 {
    color: #ef4f4f
}
.content-slide .shop_active .icon-title {
    color: #ff5555;
}
.xz {
    background-color: #3399ff;
    color: white !important;
    border-radius: 5px;
}
.tab_con > ul > li.layui-this{
    background: linear-gradient(to right, #73b891, #53bec5);
    color: #fff;
    border-radius: 6px;
    text-align: center;
}
/*分类组*/
.cate_container {
    overflow-x: scroll;
    background: #fff;
    width: 100%;
}
.cate_list {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    width: max-content;
    padding: 10px;
    
}
.cate_list .item {
    border-radius: 15px;
    background: #494848;
    color: #fff;
    padding: 2px 8px;
    margin-right: 4px;
    margin-top: 4px;
}
.cate_list .item.active {
    background: #ff8000;
}


@keyframes shake-zy {
  0% {
    transform: translateX(0); /* 修改为 translateX */
  }
  25% {
    transform: translateX(-5px) rotate(5deg); /* 修改为 translateX */
  }
  50% {
    transform: translateX(5px) rotate(-5deg); /* 修改为 translateX */
  }
  75% {
    transform: translateX(-5px) rotate(5deg); /* 修改为 translateX */
  }
  100% {
    transform: translateX(0); /* 修改为 translateX */
  }
}

    @-webkit-keyframes rotating {
        from {
            -webkit-transform: rotate(0);
            -moz-transform: rotate(0);
            -ms-transform: rotate(0);
            -o-transform: rotate(0);
            transform: rotate(0)
        }
        to {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg)
        }
    }

    @keyframes rotating {
        from {
            -webkit-transform: rotate(0);
            -moz-transform: rotate(0);
            -ms-transform: rotate(0);
            -o-transform: rotate(0);
            transform: rotate(0)
        }
        to {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg)
        }
    }
 
    .jia span {
    margin-right: 80px;
    background-color: rgba(0, 0, 0, 0.4);
    padding: 5px 8px;
    height: 60px;
    line-height: 4px;
    color: #fff;
    border-radius: 6px;
}
.ser {
    display: flex;
    align-items: center;
    width: 100%;
}

.ser img {
    margin-right: 15px;
    width: 6%;
}
.serbut {
    padding: 0.1525rem 0.825rem;
    background: linear-gradient(180deg,#51a8fd,#3b8cfe);
    border-radius: 1rem;
    color: #fff;
    font-size: 15px;}
.ser {
    display: flex;
    align-items: center;
    justify-content: space-between;
        margin:0 150px 0px 0px;
        height: 1.825rem;
    background: #FFFFFF;
    border-radius: 0rem;
    padding: 0 0.5125rem 0 0.84375rem;
    font-size: .9375rem;
    color: #9b9fa8;
}
#audio-play #audio-btn{width: 44px;height: 44px; background-size: 100% 100%;position:fixed;bottom:5%;right:6px;z-index:111;}
#audio-play .on{background: url('assets/img/music_on.png') no-repeat 0 0;-webkit-animation: rotating 1.2s linear infinite;animation: rotating 1.2s linear infinite;}
#audio-play .off{background:url('assets/img/music_off.png') no-repeat 0 0}
@-webkit-keyframes rotating{from{-webkit-transform:rotate(0);-moz-transform:rotate(0);-ms-transform:rotate(0);-o-transform:rotate(0);transform:rotate(0)}to{-webkit-transform:rotate(360deg);-moz-transform:rotate(360deg);-ms-transform:rotate(360deg);-o-transform:rotate(360deg);transform:rotate(360deg)}}@keyframes rotating{from{-webkit-transform:rotate(0);-moz-transform:rotate(0);-ms-transform:rotate(0);-o-transform:rotate(0);transform:rotate(0)}to{-webkit-transform:rotate(360deg);-moz-transform:rotate(360deg);-ms-transform:rotate(360deg);-o-transform:rotate(360deg);transform:rotate(360deg)}}
</style>
<body ontouchstart="" style="overflow: auto;height: auto !important;max-width: 550px;">
<div id="body">
    <div style="position: fixed;    z-index: 100;    top: 30px;    left: 20px;       color: white;    padding: 2px 8px;      background-color: rgba(0,0,0,0.4);    border-radius: 5px;display: none" id="xn_text">
    </div>
    
    
    
    
    
    
    <div class="fui-page-group " style="height: auto">
        <div class="fui-page  fui-page-current " style="height:auto; overflow: inherit">
            <div class="fui-content navbar" id="container" style="background-color: #fafafc;overflow: inherit">
                <div class="default-items">
                    <div class="fui-swipe">
                        <style>
                            .fui-swipe-page .fui-swipe-bullet {
                                background: #ffffff;
                                opacity: 0.5;
                            }

                            .fui-swipe-page .fui-swipe-bullet.active {
                                opacity: 1;
                            }
                        </style>
                        <div class="fui-swipe-wrapper" style="transition-duration: 500ms;">
                            <?php
                            $banner = explode('|', isset($conf['banner']) ? $conf['banner'] : '');
                            foreach ($banner as $v) {
                                $image_url = explode('*', $v);
                                echo '<a class="fui-swipe-item" href="' . (isset($image_url[1]) ? $image_url[1] : 'javascript:void(0)') . '">
                                <img src="' . (isset($image_url[0]) ? $image_url[0] : '') . '" style="display: block; width: 100%; height: 258px;" />
                            </a>';
                            }
                            ?>
                        </div>
                        <div class="fui-swipe-page right round" style="padding: 0 5px; bottom: 5px; ">
                        </div>
                    </div>
                 <div class="fui-notice">
                        <div class="image">
                            <a href="JavaScript:void(0)" onclick="$('.tzgg').show()"><img src="assets/store/picture/1571065042489353.jpg"></a>
                        </div>
                        <div class="text" style="height: 1.2rem;line-height: 1.2rem">
                            <ul>
                                <li><a href="JavaScript:void(0)" onclick="$('.tzgg').show()">
                                    
                                            <div class="text" style="height: 1.2rem;line-height: 1.2rem">
                            <ul>
                                        <marquee behavior="alternate" class="jia" scrollamount="8">
                                            
                     <span>四川用户135****343购买了本商品 2分钟前</span>
                     <span>湖北用户176****343购买了本商品 8分钟前</span>
                     <span>湖南用户138****343购买了本商品 6分钟前</span>
                     <span>河北用户155****230购买了本商品 10分钟前</span>
                     <span>江苏用户151****443购买了本商品 8分钟前</span>
                     <span>河北用户156****930购买了本商品 13分钟前</span>
                     <span>河南用户155****255购买了本商品 10分钟前</span>
                     <span>广东用户182****781购买了本商品 7分钟前</span>
                     <span>河南用户133****343购买了本商品 1分钟前</span>
                     <span>贵州用户182****343购买了本商品 3分钟前</span>
                     <span>广西用户153****343购买了本商品 9分钟前</span>
                     <span>天津用户189****343购买了本商品 5分钟前</span>
                     <span>四川用户135****343购买了本商品 2分钟前</span>
                     <span>湖北用户176****343购买了本商品 8分钟前</span>
                     <span>湖南用户138****343购买了本商品 6分钟前</span>
                     <span>河北用户155****230购买了本商品 10分钟前</span>
                     <span>江苏用户151****443购买了本商品 58分钟前</span>
                     <span>河北用户156****930购买了本商品 13分钟前</span>
                     <span>河南用户155****255购买了本商品 10分钟前</span>
                     <span>江苏用户151****443购买了本商品 8分钟前</span>
                     <span>河北用户156****930购买了本商品 13分钟前</span>
                     <span>河南用户155****255购买了本商品 10分钟前</span>
                     <span>广东用户182****781购买了本商品 7分钟前</span>
                     <span>河南用户133****343购买了本商品 1分钟前</span>
                     <span>贵州用户182****343购买了本商品 3分钟前</span>
                     <span>广西用户153****343购买了本商品 9分钟前</span>
                     <span>天津用户189****343购买了本商品 5分钟前</span>
                     <span>四川用户135****343购买了本商品 2分钟前</span>
                     <span>湖北用户176****343购买了本商品 8分钟前</span>
                     <span>湖南用户138****343购买了本商品 6分钟前</span>
                     <span>河北用户155****230购买了本商品 10分钟前</span>
                     <span>江苏用户151****443购买了本商品 58分钟前</span>
                     <span>河北用户156****930购买了本商品 13分钟前</span>
                     <span>河南用户155****255购买了本商品 10分钟前</span>
                                        </marquee>
                                    
                            </ul>
           
                        </div>
                                        </marquee>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                  <div class="text" style="height: 1.2rem;line-height: 1.2rem;">
                                
                                <div class="list_item_box shop_tips" id="" style="overflow: hidden;margin-left:10px;margin-top:-25px;top: 150px;height: 25px;min-height:10px;">
                                    <span id="xn_text4" style="padding:0 10px; border-radius: 5px; float: left;line-height:25px;text-overflow: ellipsis;white-space: nowrap;color: white;background-color: rgba(0,0,0,0.4);"></span>
                                </div>
                            </div>
                        </div>


<!--本模板由启程修改，留版权必爆单，感谢支持-->
<!--启程QQ：3882252696-->


                     <style>
                        .urlbox {
                            display: flex;
                            background-color: #F7D100;
                            border-radius: 100px;
                            width: 127px;
                            height: 30px;
                            padding: 0 6px;
                            color: #fff;
                            align-items: center;
                            justify-content: center;
                        }

                        .urlbox2 {
                            display: flex;
                            background-color: #04ab02;
                            border-radius: 100px;
                            width: 127px;
                            height: 30px;
                            padding: 0 6px;
                            color: #fff;
                            align-items: center;
                            justify-content: center;
                        }

                        .urlbox3 {
                            display: flex;
                            background-color: red;
                            border-radius: 100px;
                            width: 127px;
                            height: 30px;
                            padding: 0 6px;
                            color: #fff;
                            align-items: center;
                            justify-content: center;
                        }

                        .urlbox4 {
                            display: flex;
                            background-color: #04AAFF;
                            border-radius: 100px;
                            width: 127px;
                            height: 30px;
                            padding: 0 6px;
                            color: #fff;
                            align-items: center;
                            justify-content: center;
                        }
                    </style>
                        <div style="width:100%;display:flex;    justify-content: space-evenly;padding:10px 0px;">
                           <a href="JavaScript:void(0)" onclick="$('.tzgg').show()" class="urlbox">
                            <span class=" icon icon-notice"></span>
                            <p>平台公告</p>
                        </a>
                            
                            
                            <a href="./?mod=invite" target="_blank" class="urlbox2">
                                <span class=" icon icon-notice"></span>
                                <p>红包福利</p>
                            </a>
                            
                            
                            <a href="/" target="_blank" class="urlbox3">
                                <span class=" icon icon-share"></span>
                                <p>平台Q群</p>
                            </a>
                            <!--本模板由启程修改，留版权必爆单，感谢支持--> <!--启程QQ：3882252696--> <!--定制模板等可以联系--> <!--启程QQ：3882252696-->
<!--启程QQ：3882252696-->


                            
                            <a href="./user/regsite.php" target="_blank" class="urlbox4">
                                <span class=" icon icon-refund"></span>
                                <p>加盟赚钱</p>
                            </a>
                           
                        </div>
                    <div class="fui-notice" style="height: 36px;">
                        <div class="text" style="height: 36px !important;line-height: 36px;">
                            <ul>
                                <li id="serch-icon-container" style="">
                                    <marquee direction="up" behavior="alternate">
                                        <div align="center" style="height: 1.5rem; display: flex;align-items: center;
    justify-content: center;position:relative;"><span style="font-size:13px;">
                                                <font color="#FF0000">-</font>
                                                <font color="#D5002A">-</font>
                                                <font color="#AB0054">↓</font>
                                                <font color="#81007E">请</font>
                                                <font color="#5700A8">选</font>
                                                <font color="#2D00D2">择</font>
                                                <font color="#0000FF">商</font>
                                                <font color="#0000FF">品</font>
                                                <font color="#0000FF">分</font>
                                                <font color="#2D00D2">类</font>
                                                <font color="#81007E">↓</font>
                                                <font color="#81007E">-</font>
                                                <font color="#AB0054">-</font>
                                        </span>
                                       
                                    </marquee>
                                </li>
                                <li id="search-container" style="display: none;">
                            		<div style="display: flex;align-items: center;margin: 0 10px;">
                            			<div class="search-content" style="display: flex;border-radius: 5px;
                            			background-color: rgb(242, 242, 242);padding: 6px 0;flex: 1;margin-right: 8px;">
                            				<div style="margin-right: 6px;">
                            				    <i class="icon icon-search" style="float: none"></i>
                            				</div>
                            				<input placeholder="输入商品关键词...如：腾讯视频" id="kw" name="kw" value="" style="flex:1;
                            			background-color: rgb(242, 242, 242);font-size: 13px;">
                            			</div>
                            			<input type="button" id="goods_search" class="searchbar-cancel searchbtn" style="font-size: 13px;width:3rem;background-color:#ff5555;height: 1.35rem; border-radius:5px 5px 5px 5px;color: #fff;" value="搜索">
                            			<div id="cancel-button" style="margin-left: 8px;">取消</div>
                                        </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="device">
                        <!-- 使用Swiper分页器展示所有一级分类 -->
                        <!-- 添加响应式CSS，确保一级分类在各种屏幕宽度下都能正确显示 -->
                        <style>
                            /* 一级分类基础样式 */
                            .primary-category {
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                box-sizing: border-box;
                                transition: all 0.3s ease;
                                margin: 2px !important;
                                padding: 3px !important;
                                width: calc(20% - 4px) !important; /* 每行5个分类，减去边距 */
                                min-width: 0;
                                text-align: center !important;
                            }
                            
                            .primary-category .mbg {
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                            }
                            
                            .primary-category .ico {
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto !important;
                                width: 100%;
                            }
                            
                            .primary-category .ico img {
                                width: 50px !important;
                                height: 50px !important;
                                max-width: 100%;
                            }
                            
                            /* 确保每行显示5个分类，超出部分在下一行显示 */
                            .content-slide {
                                display: flex;
                                flex-wrap: wrap;
                                justify-content: flex-start;
                                margin: 0 auto !important;
                                align-content: flex-start;
                                width: 100%;
                                height: auto !important;
                                min-height: 160px;
                            }
                            
                            /* 确保2行固定高度，不会向下扩展 */
                            .swiper-slide {
                                display: flex;
                                justify-content: center;
                                align-items: flex-start;
                                height: auto !important;
                                min-height: 160px;
                            }
                            
                            /* 调整文字大小，使分类更紧凑 */
                            .primary-category .icon-title {
                                font-size: 11px !important;
                                margin-top: 3px !important;
                                line-height: 1.2;
                                width: 100%;
                                overflow: hidden;
                                text-overflow: ellipsis;
                                white-space: nowrap;
                            }
                            
                            /* 激活状态样式 */
                            .primary-category.shop_active {
                                transform: scale(1.05);
                            }
                            
                            .primary-category.shop_active .icon-title {
                                color: #ff5555;
                                font-weight: bold;
                            }
                            
                            /* 单行模式：只有5个或更少分类时 */
                            .primary-category-box.single-row .swiper-slide {
                                height: auto !important;
                                min-height: 80px !important;
                            }
                            .primary-category-box.single-row .content-slide {
                                min-height: 80px !important;
                                height: auto !important;
                            }
                        </style>
                        <div class="category-container primary-category-box <?php echo count($ar_data) <= 5 ? 'single-row' : ''; ?>" style="background-color: #fff; padding: 10px; margin-bottom: 10px;">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <?php
                                    $arry = 0;
                                    $au = 0;
                                    $items_per_page = 10; // 每页显示10个分类（2行）
                                    foreach ($ar_data as $v) {
                                        if (($arry / $items_per_page) == $au) { //循环首
                                            echo '<div class="swiper-slide swiper-slide-visible" data-swiper-slide-index="' . $au . '" style="margin: auto;margin-top: 0px;">
                                            <div class="content-slide" style="display: flex; flex-wrap: wrap;">';
                                        }
                                        // 检查当前分类是否被选中
                                        $active_class = ($v['cid'] == $cid) ? 'shop_active' : '';
                                         
                                        echo '<a data-cid="' . $v['cid'] . '" data-name="' . $v['name'] . '" class="get_cat primary-category ' . $active_class . '">
                                               <div class="mbg">
                                                   <p class="ico"><img src="' . (isset($v['shopimg'])?$v['shopimg']:'') . '" onerror="this.src=\'assets/store/picture/1562225141902335.jpg\'"></p>
                                                   <p class="icon-title">' . $v['name'] . '</p>
                                              </div>
                                          </a>';
                                     
                                        if (((($arry + 1) / $items_per_page)) == ($au + 1)) { //循环尾
                                            echo '</div>
                                            </div>';
                                            $au++;
                                        }
                                        $arry++;
                                    }
                                    if (floor((($arry) / $items_per_page)) != (($arry) / $items_per_page)) {
                                        echo '</div></div>';
                                    }
                                    ?>
                                </div>
                                <!-- 添加分页器 -->
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                        
                        <!-- 二级分类盒子 -->
                        <div class="category-container secondary-category-box" style="background-color: #fff; padding: 10px; height: 40px;">
                            <div class="sub-categories" style="display: flex; align-items: center; height: 100%;">
                                <?php
                                // 遍历所有二级分类
                                foreach ($sub_classes as $primary_cid => $secondary_categories) {
                                    // 获取一级分类名称用于显示
                                    $primary_name = '';
                                    foreach ($ar_data as $primary) {
                                        if ($primary['cid'] == $primary_cid) {
                                            $primary_name = $primary['name'];
                                            break;
                                        }
                                    }
                                     
                                    // 如果有二级分类
                                    if (!empty($secondary_categories)) {
                                        // 遍历二级分类
                                        foreach ($secondary_categories as $secondary) {
                                            // 检查当前分类是否被选中
                                            $sub_active_class = ($secondary['cid'] == $cid) ? 'shop_active' : '';
                                             
                                            // 显示二级分类，添加一级分类ID标识
                                            echo '<a data-cid="' . $secondary['cid'] . '" data-name="' . $secondary['name'] . '" data-primary-cid="' . $primary_cid . '" class="get_cat sub-category ' . $sub_active_class . '" style="margin: 5px 10px; padding: 5px 15px; background-color: #f5f5f5; font-size: 14px;">' . $secondary['name'] . '
                                            </a>';
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <script>

                    </script>
                    <div style="height: 3px"></div>




              <div style="height: 3px"></div>
                
                </section>
                </section>
                 <div class="layui-tab tag_name tab_con" style="margin:0;display:none;">
                        <ul class="layui-tab-title" style="margin: 0;background:#fff;overflow: hidden;">
                
                        </ul>
                </div>
                
                <div class="fui-goods-group " style="background: #f3f3f3;" id="goods-list-container">
                    <div class="flow_load"><div id="goods_list"></div></div>
                    <div class="footer" style="width:100%; margin-top:0.5rem;margin-bottom:2.5rem;display: block;">
                        <ul>
                            <li>© <?php echo $conf['sitename'] ?>. All rights reserved.</li>
                        </ul>
                        <p style="text-align: center"><?php echo $conf['footer']?></p>
                    </div>
                </div>

            </div>
        </div>
        
        </div>
        <input type="hidden" name="_cid" value="<?php echo $cid; ?>">
        <input type="hidden" name="_cidname" value="<?php echo $cat_name; ?>">
        <input type="hidden" name="_curr_time" value="<?php echo time(); ?>">
        <input type="hidden" name="_template_virtualdata" value="<?php echo isset($conf['template_virtualdata']) ? $conf['template_virtualdata'] : '0';?>">
		<input type="hidden" name="_template_showsales" value="<?php echo isset($conf['template_showsales']) ? $conf['template_showsales'] : '0';?>">
        <input type="hidden" name="_sort_type" value="">
        <input type="hidden" name="_sort" value="">
        
        <div class="fui-navbar" style="bottom:-34px;background-color: white;max-width: 550px">
        </div>

       <div class="fui-navbar" style="max-width: 550px;z-index: 100;">
            <a href="./" class="nav-item active"> <span class="icon icon-home "></span> <span class="label">首页</span>
            </a>
            <a href="./?mod=query" class="nav-item "> <span class="icon icon-dingdan1"></span> <span class="label">订单</span> </a>
            <?php if(isset($conf['template_cart'])&&$conf['template_cart']==1){?><a href="./?mod=cart" class="nav-item "> <span class="icon icon-cart2"></span> <span class="label">购物车</span><span id="cart_sum" class="cart-count"></span> </a><?php }?>
            
            <a href="/user/regsite.php" class=" nav-item  "> <span class="icon icon-wodetuandui"></span> <span class=" label titlename">加盟</span> </a>
            
                       
            <a href="./?mod=kf" class="nav-item "> <span class=" icon icon-service1"></span> <span class="label">客服</span>
            </a>
            <a href="./user/index.php" class="nav-item "> <span class="icon icon-person2"></span> <span class="label">会员中心</span> </a>
        </div>



        <div style="width: 100%;height: 100vh;position: fixed;top: 0px;left: 0px;opacity: 0.5;background-color: black;display: none;z-index: 10000"
             class="tzgg"></div>
        <div class="tzgg" type="text/html" style="display: none">
            <div class="account-layer" style="z-index: 100000000;">
                <div class="account-main" style="padding:0.8rem;height: auto">

                    <div class="account-title">系 统 公 告</div>

                    <div class="account-verify"
                         style="  display: block;    max-height: 15rem;    overflow: auto;margin-top: -10px">
                        <?php echo $conf['anounce'] ?>
                    </div>
                </div>
                <div class="account-btn" style="display: block" onclick="$('.tzgg').hide()">确认</div>
                
                <!--<div class="account-close">-->
                <!--<i class="icon icon-guanbi1"></i>-->
                <!--</div>-->
            </div>
        </div>

    </div>
</div>
<!--音乐代码-->
<div id="audio-play" <?php if(empty($conf['musicurl'])){?>style="display:none;"<?php }?>>
  <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')">
    <audio loop="loop" src="<?php echo $conf['musicurl']?>" id="media" preload="preload"></audio>
  </div>
</div>
<!--音乐代码-->
<script src="<?php echo $cdnpublic?>jquery/3.4.1/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layui/2.5.7/layui.all.js"></script>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic?>Swiper/6.4.5/swiper-bundle.min.js"></script>
<script src="<?php echo $cdnserver?>assets/store/js/foxui.js"></script>
<script src="<?php echo $cdnserver?>assets/storeg2/js/layui.flow.js"></script>
<script src="<?php echo $cdnserver?>assets/storeg2/js/indexpro.js?ver=<?php echo VERSION ?>"></script>
<!-- 购物车数量样式 -->
  <style>
      #cart_sum {
          position: absolute;
          top: -5px;
          right: -5px;
          background-color: #ff4444;
          color: white;
          border-radius: 50%;
          width: 18px;
          height: 18px;
          font-size: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
      }
  </style>
  <script>
 tags(4);
function tags(cid){
  $.ajax({
    url: 'ajax.php?act=tags',
    method: 'POST',
    data: { cid: cid },
    success: function(response) {
                $('.tags').html('');
                $("#tags").hide();
if(response.code==0){
                    $("#tags").show();
                    var i=0;
      response.data.forEach(function(item) {
          i++;
          if(i==1){
        var html = '<div class="tag1 classavbd" id="tag'+i+'" onclick="ckw(' + cid + ', \'' + item + '\','+i+')">' + item + '</div>';
          }else{
               var html = '<div class="tag1" id="tag'+i+'" onclick="ckw(' + cid + ', \'' + item + '\','+i+')">' + item + '</div>';
          }
        $('.tags').append(html);
      });
}
    },
    error: function(xhr, status, error) {
      console.error(error);
    }
  });
  
        
}


</script>

  <!-- 引入Swiper JavaScript -->
  <script src="<?php echo $cdnpublic?>Swiper/6.4.5/swiper-bundle.min.js"></script>
  
  <!-- 添加响应式CSS，确保一级分类在各种屏幕宽度下都能正确显示 -->
  <style>
      /* 响应式媒体查询，根据屏幕宽度调整一级分类每行显示数量 */
      @media screen and (min-width: 640px) {
          /* 大屏幕：每行显示5个分类 */
          .primary-category {
              width: calc(16% - 8px);
          }
      }
      
      @media screen and (max-width: 639px) and (min-width: 480px) {
          /* 中屏幕：每行显示4个分类 */
          .primary-category {
              width: calc(25% - 10px);
          }
      }
      
      @media screen and (max-width: 479px) and (min-width: 360px) {
          /* 小屏幕：每行显示3个分类 */
          .primary-category {
              width: calc(33.333% - 10px);
          }
      }
      
      @media screen and (max-width: 359px) {
          /* 超小屏幕：每行显示2个分类 */
          .primary-category {
              width: calc(50% - 10px);
          }
      }
  </style>
  
  <!-- 确保Swiper正确初始化 -->
  <script>
      // 页面加载完成后初始化Swiper
      $(document).ready(function() {
          // 初始化一级分类的Swiper
          var swiper = new Swiper('.primary-category-box .swiper-container', {
              slidesPerView: 1,
              spaceBetween: 10,
              pagination: {
                  el: '.primary-category-box .swiper-pagination',
                  clickable: true,
              },
              // 确保在各种屏幕尺寸下都能正常工作
              observer: true,
              observeParents: true,
              observeSlideChildren: true,
              // 允许在移动设备上滑动
              allowTouchMove: true,
              // 禁用自动播放，防止用户体验问题
              autoplay: false
          });
           
          // 当窗口大小改变时，重新初始化Swiper以适应新的屏幕尺寸
          $(window).resize(function() {
              // 稍微延迟一下，确保DOM已经更新
              setTimeout(function() {
                  swiper.update();
              }, 100);
          });
      });
  </script>

</body>
</html>