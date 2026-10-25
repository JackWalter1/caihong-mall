<?php
if (!defined('IN_CRONLITE')) die();
$tid=intval($_GET['tid']);
$tool=$DB->getRow("select * from pre_tools where tid='$tid' limit 1");
if(!$tool)sysmsg('没有找到商品熬！');
function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') { 
    $return = ''; 
    if (function_exists('mb_get_info')) { 
        for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) { 
            $str = mb_substr ( $string, $x, 1, $in_encoding ); 
            if (strlen ( $str ) > 1) { // 多字节字符 
                $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) ); 
            } else { 
                $return .= '%' . strtoupper ( bin2hex ( $str ) ); 
            } 
        } 
    } 
    return $return; 
}

$level = '<font color="#48d1cc">普通用户售价</font>';
if($islogin2==1){
	$price_obj = new \lib\Price($userrow['zid'],$userrow);
	if($userrow['power']==2){
		$level = '<font color="orange">高级代理售价</font>';
	}elseif($userrow['power']==1){
		$level = '<font color="red">普通代理售价</font>';
	}
}elseif($is_fenzhan == true){
	$price_obj = new \lib\Price($siterow['zid'],$siterow);
}else{
	$price_obj = new \lib\Price(1);
}

if(isset($price_obj)){
	$price_obj->setToolInfo($tool['tid'],$tool);
	if($price_obj->getToolDel($tool['tid'])==1)sysmsg('商品已下架');
	$price=$price_obj->getToolPrice($tool['tid']);
	$islogin3=$islogin2;
	unset($islogin2);
	$price_pt=$price_obj->getToolPrice($tool['tid']);
	$price_1=$price_obj->getToolCost($tool['tid']);
	$price_2=$price_obj->getToolCost2($tool['tid']);
	$islogin2=$islogin3;
}else{
   $price=$tool['price'];
   $price_pt=$tool['price'];
   $price_1=$tool['cost1'];
   $price_2=$tool['cost2'];
}


if($tool['is_curl']==4){
	$count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid='{$tool['tid']}' and orderid=0");
	$fakainput = getFakaInput();
	$tool['input']=$fakainput;
	$isfaka = 1;
	$stock = '<span class="stock" style="">剩余:<span class="quota">'.$count.'</span>份</span>';
}elseif($tool['stock']!==null){
	$count = $tool['stock'];
	$isfaka = 1;
	$stock = '<span class="stock" style="">剩余:<span class="quota">'.$count.'</span>份</span>';
}else{
	$isfaka = 0;
}

if($tool['prices']){
	$arr = explode(',',$tool['prices']);
	if($arr[0]){
		$arr = explode('|',$tool['prices']);
		$view_mall = '<font color="#bdbdbd" size="2">购买'.$arr[0].'个以上按批发价￥'.($price-$arr[1]).'计算</font>';
	}
}

?>
<!DOCTYPE html>
<html lang="zh" style="font-size: 20px;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover,user-scalable=no">
    <script> document.documentElement.style.fontSize = document.documentElement.clientWidth / 750 * 40 + "px";</script>
    <meta name="format-detection" content="telephone=no">
    <title><?php echo $conf['sitename'] . ($conf['title'] == '' ? '' : ' - ' . $conf['title']) ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords'] ?>">
    <meta name="description" content="<?php echo $conf['description'] ?>">
    <?php if(!empty($conf['favicon'])){?>
    <link rel="icon" href="<?php echo $cdnserver?>/<?php echo $conf['favicon']?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $cdnserver?>/<?php echo $conf['favicon']?>" type="image/x-icon" />
    <?php }else{?>
    <link rel="icon" href="<?php echo $cdnserver?>assets/img/favicon/favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $cdnserver?>assets/img/favicon/favicon.ico" type="image/x-icon" />
    <?php }?>
    <link href="<?php echo $cdnpublic ?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="<?php echo $cdnpublic ?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/foxui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/foxui.diy.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/style(1).css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/iconfont.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/detail.css">
    <link href="<?php echo $cdnpublic ?>limonte-sweetalert2/7.33.1/sweetalert2.min.css" rel="stylesheet">
    <link href="<?php echo $cdnpublic ?>animate.css/3.7.2/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnpublic ?>layui/2.5.7/css/layui.css"/>
    <link href="<?php echo $cdnpublic ?>Swiper/4.5.1/css/swiper.min.css" rel="stylesheet">
	<?php echo str_replace('body','html',$background_css)?>

</head>

<style>
    .fix-iphonex-bottom {
        padding-bottom: 34px;
    }
</style>

<style>
    select {
        /*Chrome和Firefox里面的边框是不一样的，所以复写了一下*/
        border: solid 1px #000;
        /*很关键：将默认的select选择框样式清除*/
        appearance: none;
        -moz-appearance: none;
        -webkit-appearance: none;
        /*将背景改为红色*/
        background: none;
        /*加padding防止文字覆盖*/
        padding-right: 14px;
    }

    /*清除ie的默认选择框样式清除，隐藏下拉箭头*/
    select::-ms-expand {
        display: none;
    }

    .onclick {
        cursor: pointer;
        touch-action: manipulation;
    }

    .fui-page,
    .fui-page-group {
        -webkit-overflow-scrolling: auto;
    }

    .fui-cell-group .fui-cell .fui-input {
        display: inline-block;
        width: 70%;
        height: 32px;
        line-height: 1.5;
        margin: 0 auto;
        padding: 2px 7px;
        font-size: 12px;
        border: 1px solid #dcdee2;
        border-radius: 4px;
        color: #515a6e;
        background-color: #fff;
        background-image: none;
        cursor: text;
        transition: border .2s ease-in-out, background .2s ease-in-out, box-shadow .2s ease-in-out;
    }

    .btnee {
        width: 20%;
        float: right;
        margin-top: -2.8em;
    }

    .btnee_left {
        width: 20%;
        float: lef;
        margin-top: -2.8em;
    }

    .fui-cell-group .fui-cell .fui-cell-label1 {
        padding: 0 0.4rem;
        line-height: 0.7rem;
    }

    .fui-cell-group .fui-cell.must .fui-cell-label:after {
        top: 40%;
    }

    /*支付方式*/
    .payment-method {
        position: fixed;
        bottom: 0;
        background: white;
        width: 100%;
        padding: 0.75rem 0.7rem;
        z-index: 1000 !important;
    }

    .payment-method .title {
        font-size: 0.75rem;
        text-align: center;
        color: #333333;
        line-height: 0.75rem;
        margin-bottom: 1rem;
    }

    .payment-method .title span {
        height: 0.75rem;
        position: absolute;
        right: 0.3rem;
        width: 2rem;
    }

    .payment-method .title .close:before {
        font-family: 'iconfont';
        content: '\e654';
        display: inline-block;
        transform: scale(1.5);
        color: #ccc;

    }

    .payment-method .payment {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        padding: 0.7rem 0;
    }

    .payment-method .payment .icon-weixin1 {
        color: #5ee467;
        font-size: 1.3rem;
        margin-right: 0.4rem;
    }

    .payment-method .payment .icon-zhifubao1 {
        color: #0b9ff5;
        font-size: 1.5rem;
        margin-right: 0.4rem;
    }

    .icon-zhifubao1::before {
        margin-left: 1px;
    }

    .payment-method .payment .paychoose {
        font-size: 1.2rem;
    }

    .payment-method .payment .icon-xuanzhong4 {
        color: #2e8cf0;
    }

    .payment-method .payment .icon-option_off {
        color: #ddd;
    }

    .payment-method .payment .paytext {
        flex: 1;
        font-size: 0.8rem;
        color: #333;
    }

    .payment-method button {
        margin-top: 0.8rem;
        background: #2e8cf0;
        color: white;
        letter-spacing: 1px;
        font-size: 0.7rem;
        border: none;
        outline: none;
        width: 17.25rem;
        height: 1.75rem;
        border-radius: 1.75rem;
    }

    .input_select {
        flex: 1;
        height: 1.5rem;
        border-radius: 2px;
        border: none;
        border-bottom: 1px solid #eee;
        outline: none;
        margin-left: 0.4rem;
    }
</style>
<style>
    html {
        font-size: 14px;
        color: #000;
        font-family: '微软雅黑'
    }

    a, a:hover {
        text-decoration: none;
    }

    pre {
        font-family: '微软雅黑'
    }

    .box {
        padding: 20px;
        background-color: #fff;
        margin: 50px 100px;
        border-radius: 5px;
    }

    .box a {
        padding-right: 15px;
    }

    #about_hide {
        display: none
    }

    .layer_text {
        background-color: #fff;
        padding: 20px;
    }

    .layer_text p {
        margin-bottom: 10px;
        text-indent: 2em;
        line-height: 23px;
    }

    .button {
        display: inline-block;
        *display: inline;
        *zoom: 1;
        line-height: 30px;
        padding: 0 20px;
        background-color: #56B4DC;
        color: #fff;
        font-size: 14px;
        border-radius: 3px;
        cursor: pointer;
        font-weight: normal;
    }

    .photos-demo img {
        width: 200px;
    }

    .layui-layer-content {
        margin: auto;
    }

    * {
        -webkit-overflow-scrolling: touch;
    }

    .pro_content {
        background-image: linear-gradient(130deg, #00F5B2, #1FC3FF, #00dbde);
        height: 120px;
        position: relative;
        margin-bottom: 4rem;
        background-size: 300%;
        animation: bganimation 10s infinite;
    }

    @keyframes bganimation {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    #picture {
        padding-top: 1em;
    }

    #picture div {
        text-align: center;
    }

    #picture img {
        width: auto;
        max-height: 38vh;
        margin: auto;
    }

.hd_intro img{ max-width: 100%; }
	.list-group-item{
	    text-align: left !important;
	}

    .aui-footer-fixed {
        position: fixed;
        bottom: 0;
        z-index: 998;
        width: 100%;
        max-width: 650px;
    }

    .aui-footer-flex {
        display: -webkit-box;
        display: box;
        display: -webkit-flex;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        align-items: center;
    }

    .aui-footer-button {
        border-top: 1px solid #333333;
        background: #ffffff;
        position: fixed;
        bottom: 0;
        height: 105px;
        z-index: 999 !important;
    }

    .aui-footer-wrap {
        width: 30%;
        height: 55px;
    }

    .aui-footer-wrap a {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .aui-footer-flex1 {
        display: block;
        width: 100%;
        flex: 1;
    }

    .aui-footer-group {
        position: relative;
        height: 55px;
    }

    .aui-footer-flex {
        display: flex;
    }

    .aui-btn-red {
        background-image: linear-gradient(to right, rgb(255, 119, 0), rgb(255, 73, 0));
    }

    .aui-btn {
        position: relative;
        z-index: 10;
        line-height: 55px;
        text-align: center;
        color: #fff;
        font-size: 14px;
        -webkit-user-select: none;
        width: 100%;
        font-weight: bold;
    }
    #inputsname .layui-input{
    background-color: #f1f3f7;
        border-radius: 10px;
}

input.no-border {
  border: none;
  outline: none;
}
</style>

<body ontouchstart="" style="overflow: auto;height: auto !important;max-width: 650px;margin: auto;">
<div class="fui-page-group statusbar" style="max-width: 650px;left: auto;">
    <div class="fui-page  fui-page-current " style="overflow: inherit">
        <div id="container" class="fui-content "
             style="background-color: rgb(255, 255, 255); padding-bottom: 60px;">
            <div class="pro_content" style="margin-bottom: 3.5rem;">
                <div class="list_item_box" style="top: 53px;">
                    <div class="bor_detail">
                        <div class="thumb" id="layer-photos-demo" class="layer-photos-demo">
                            <img alt="<?php echo $tool['name']?>" layer-src="<?php echo $tool['shopimg']?$tool['shopimg']:'assets/store/picture/error_img.png';?>"  src="<?php echo $tool['shopimg']?$tool['shopimg']:'assets/store/picture/error_img.png';?>">
                        </div>
                        <div class="pro_right fl">
                            <span id="level">当前级别：<?php echo $level?></span>
                            <a href="./?mod=cart" class="icon icon-cart2 color"
                               style="float: right;margin-right: 1em;background-color: #0079fa;color: white;padding: 0.3rem;border-radius: 3em;box-shadow: 3px 3px 6px #eee;<?php if($conf['shoppingcart']==0){?>display:none;<?php }?>" title="打开购物车"></a>
                               <a href="../" class="icon icon-home color" style="float: right;margin-right: 1em;background-color: #0079fa;color: white;padding: 0.3rem;border-radius: 3em;box-shadow: 3px 3px 6px #eee;"></a>
                            <span class="list_item_title" id="gootsp"><?php echo $tool['name']?></span>
                            <div class="list_tag">
                                <div class="price">
                                    <span class="t_price pay_prices">售价：<span class="pay_price"><?php echo $price?>元</span>
									<?php if($conf['template_showprice']==1){?>
									&nbsp;&nbsp;<button type="button" class="show_daili_price layui-btn layui-btn-warm layui-btn-xs layui-btn-radius "><i class="layui-icon layui-icon-fire"></i>查看等级价格</button>
                                    <?php } ?>
									</span>
                                    <?php echo $stock?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div style="width:100%;text-align:center;font-size:15px;font-weight:550;margin-top: -3%;">填写下单信息</div>
                <div style="padding:10px;">
                
               
                <div class="layui-form-item">
                    
                    
                    
                    
                    
                    
                <div id="inputsname"></div>
                
                
                
                <div style="height: 3px"></div>
                <div class="layui-form-item" <?php echo $tool['multi']==0?'style="display: none"':null;?>>
                    <label class="layui-form-label" style="width: 100%;text-align: left;padding:0">下单份数：<?php if($isfaka == 1){echo "<span style='float:right;'>剩余：<font color='red'>".$count."</font>份</span>";} ?></label>
                    <div class="input-group">
                        <div class="input-group-addon" id="num_min" style="background-color: #ff7100; color: #fff; border-radius: 2px 0 0 2px; cursor: pointer;">
                                减一份
                            </div>
                            
                            <b><input id="num" style="text-align: center; font-weight: bold; border: 1px solid; border-radius: 2px; padding: 5px;background-color: #f1f3f7;" name="num" class="layui-input" type="number" value="1" placeholder="请输入数量" required="" min="1"<?php echo $isfaka==1?' max="'.$count.'"':null?>></b>
                            
                            
                        <div class="input-group-addon" id="num_add" style="background-color: #ff7100; color: #fff; border-radius: 0 2px 2px 0; cursor: pointer;">
                                加一份
                            </div>
                    </div>
             
                </div> <div style="height: 3px"></div>
                   <label class="layui-form-label" style="width: 100%;text-align: left;padding:0">商品价格</label>
                    <div class="layui-input-">
                        
                        <b><input style="text-align: center;color:#4169E1;background-color:#ffffff;" type="text" id="need"disabled="" class="form-control need_price" value="<?php echo $price?> 元"></b>
                        
                        
                        <div style="color: #ff7100;font-size:11px">增加或减少份数时，请注意价格变更哦~</div>
                        
                    </div>
                </div>
              
               
                <br>  <br>  <br> <br> <br>
			
			
			
			
			
			
			
			
			
			
            <div class="content_friends" style="    border: 1px solid #5fb878;margin-bottom:105px;">
                <div class="top_tit">
                    商品说明
                </div>
                <div class="hd_intro" style="word-break: break-all;"><?php echo $tool['desc']?></div>
            </div>
            <br/>
			<?php if($tool['shopimg']){?>
            
                        <section style="border: 0px none #e0dcc6; clear: both; box-sizing: border-box; padding: 0px; color: inherit;">
                            <section style="color: inherit; float: left; width: 10px; margin-top: -10px; border-color: #e0dcc6; height: 10px !important; background-color: #fefefe;"></section>
                            <section style="color: inherit; float: right; width: 10px; margin-top: -10px; border-color: #e0dcc6; height: 10px !important; background-color: #fefefe;"></section>
                            <section style="color: inherit; float: right; width: 10px; border-color: #e0dcc6; margin-right: 10px; height: 10px !important; background-color: #fefefe;"></section>
                            <section style="color: inherit; text-align: left; width: 10px; border-color: #e0dcc6; margin-left: 10px; height: 10px !important; background-color: #fefefe;"></section>
                        </section>
                </section>
                 </section>
            </div>
			<?php }?>

            <div class="swiper-container" id="swiper"
                 style="display: none;width: 94%;max-height: 42vh;box-shadow: 1px 1px 8px #eee;border-radius: 0.3em">
                <div class="swiper-wrapper" id="picture"></div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>

            <div class="assemble-footer footer">
    
          

    <div class="assemble-footer footer" style="max-width: 650px; z-index: 100;background-color: rgba(255,255,255,0.7);">
<style>
    .assemble-footer.footer {
        background-color: rgba(255,255,255,0.3);
        max-width: 650px;
        z-index: 100;
                    }
</style><div class="assemble-footer footer" style="bottom: 3vh;">
        
                    <div class="aui-footer-wrap">
                        <a href="javascript:history.back()" style="color:#333;">
                            <span class="fa fa-mail-reply" aria-hidden="true"></span>
                            <span>返回</span>
                        </a>
                    </div>
                    <div class="aui-footer-wrap" style="">
                        <a id="submit_cart_shop" style="color:#333">
                            <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                            <span>加入购物车<span id="cart_sum" style="display:inline-block;">(0)</span></span>
                        </a>
                    </div>


                    <div class="aui-footer-group aui-footer-flex1">
                        <div class="aui-footer-flex">
                            <div class="aui-btn aui-btn-red" id="submit_buy">立即购买</div>
                        </div>
                    </div>
                </div>

            </div>

<input type="hidden" id="tid" value="<?php echo $tid?>" cid="<?php echo $tool['cid']?>" price="<?php echo $price;?>" alert="<?php echo escape($tool['alert'])?>" inputname="<?php echo $tool['input']?>" inputsname="<?php echo $tool['inputs']?>" multi="<?php echo $tool['multi']?>" isfaka="<?php echo $isfaka?>" count="<?php echo $tool['value']?>" close="<?php echo $tool['close']?>" prices="<?php echo $tool['prices']?>" max="<?php echo $tool['max']?>" min="<?php echo $tool['min']?>">
<input type="hidden" id="leftcount" value="<?php echo $isfaka?$count:100?>">

    <div id="paymentmethod" class="common-mask" style="display:none;max-width: 650px">
        <div class="payment-method" style="position: absolute;max-height:70vh;">
            
            </div>
        </div>
    </div>
</div>
<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic ?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="<?php echo $cdnpublic ?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo $cdnpublic ?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic ?>layer/2.3/layer.js"></script>
<script src="<?php echo $cdnpublic ?>clipboard.js/1.7.1/clipboard.min.js"></script>
<script src="<?php echo $cdnpublic?>layui/2.5.7/layui.all.js"></script>
<script src="<?php echo $cdnpublic ?>Swiper/4.5.1/js/swiper.min.js"></script>
<script src="<?php echo $cdnpublic ?>limonte-sweetalert2/7.33.1/sweetalert2.min.js"></script>
<script>
$(".show_daili_price").on("click",function(){
     layer.open({
          type: 1,
          title: "商品价格对比",
          btnAlign: 'c',
          content: $('#show_daili_price_html'),
          <?php if($islogin2 && $userrow['power'] == 2) {?>
          btn: ['关闭'],
          <?php }else{ ?>
          btn: ['提升级别', '关闭'],
          yes: function(index, layero){
             window.location.href = "./user/regsite.php";
          },
          <?php } ?>
     });
});
</script>
<div id="show_daili_price_html" style="display:none;">
    <div class="price" style="text-align:center;">
        <hr>
        <p class="pay_prices" id="level"><font color="#48d1cc">普通用户价</font>：<span class="pay_price"><?php echo $price_pt?>元</span></p>
        <p class="pay_prices" id="level"><font color="red">普通代理价</font>：<span class="pay_price"><?php echo $price_1?>元</span></p>
        <p class="pay_prices" id="level"><font color="orange">高级代理价</font>：<span class="pay_price"><?php echo $price_2?>元</span></p>
        <hr>
        <p class="pay_prices" id="level"><font color="blue">您当前级别</font>：<span class="pay_price"><?php echo $level?></span></p>
    </div>
</div>
<script type="text/javascript">
layer.photos({
  photos: '#layer-photos-demo'
  ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
});

var hashsalt=<?php echo $addsalt_js?>;
function goback()
{
    document.referrer === '' ?window.location.href = './' :window.history.go(-1);
}
</script>

<script src="assets/store/js/main.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>