<?php
/**
 * 自助下单
**/
include("../includes/common.php");
$title='自助下单';

// 先进行登录检查
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 再包含head.php
include './head.php';

$usershop = true;
// 确保siterow变量存在
if(!isset($siterow)) {
    $siterow = array('class' => '');
}
// 确保is_fenzhan变量存在
if(!isset($is_fenzhan)) {
    $is_fenzhan = false;
}
$addsalt=md5(mt_rand(0,999).time());
$_SESSION['addsalt']=$addsalt;
$x = new \lib\hieroglyphy();
$addsalt_js = $x->hieroglyphyString($addsalt);
?>
<style>
/* ===== 原有必要样式（保留） ===== */
img.logo{width: 22px;margin: -2px 5px 0 5px;}
.onclick{cursor: pointer;touch-action: manipulation;}
.border-t{border-top: 1px solid #e9e9e9;}
.border-b{border-bottom: 1px solid #e9e9e9;}
.layui-fixbar{position:fixed;right:15px;bottom:15px;z-index:999999;margin:0;padding:0}
.layui-fixbar li{list-style:none;width:50px;height:50px;line-height:50px;margin-bottom:1px;text-align:center;cursor:pointer;font-size:30px;background-color:#9F9F9F;color:#fff;border-radius:2px;opacity:.95}
.nav-counter{position:absolute;font-size:16px;top:-1px;right:1px;height:20px;width:20px;line-height:20px;padding:0 6px;color:#fff;text-align:center;background:#e23442;border-radius:50%;background-image:-webkit-linear-gradient(top,#e8616c,#dd202f);background-image:-moz-linear-gradient(top,#e8616c,#dd202f);background-image:-o-linear-gradient(top,#e8616c,#dd202f);background-image:linear-gradient(to bottom,#e8616c,#dd202f);-webkit-box-shadow:inset 0 0 1px 1px rgba(255,255,255,.1),0 1px rgba(0,0,0,.12);box-shadow:inset 0 0 1px 1px rgba(255,255,255,.1),0 1px rgba(0,0,0,.12)}
#list td{max-width:280px;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;}
#list2 td{max-width:280px;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;}
#orderItem .orderTitle{word-break:keep-all;}
#orderItem .orderContent{word-break:break-all;}
#orderItem .orderContent img{max-width:100%}
#alert_frame img{max-width:100%}

/* ===== 自助下单 高级感美化 ===== */
body {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    min-height: 100vh;
}

/* 外层卡片容器 */
.shop-premium-wrap {
    max-width: 900px;
    margin: 20px auto;
    padding: 0 15px;
    float: none !important;
}

.shop-premium-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    border: none;
}

/* 顶部渐变 Header */
.shop-card-header {
    background: linear-gradient(120deg, #4e6ef2 0%, #6c5ce7 50%, #a55eea 100%);
    padding: 24px 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.shop-card-header::before {
    content: '';
    position: absolute;
    top: -50%; right: -20%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
    pointer-events: none;
}
.shop-card-header::after {
    content: '';
    position: absolute;
    bottom: -60%; left: -10%;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    pointer-events: none;
}
.shop-card-title {
    font-size: 20px;
    font-weight: 600;
    letter-spacing: 1px;
    position: relative;
    z-index: 2;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.shop-card-title i {
    font-size: 22px;
}
.shop-card-balance {
    position: relative;
    z-index: 2;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(10px);
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid rgba(255,255,255,0.25);
}
.shop-card-balance b {
    color: #ffeaa7;
    font-size: 16px;
    margin-left: 4px;
}

/* Tab 导航 */
.shop-tabs {
    display: flex;
    background: #fafbfc;
    border-bottom: 1px solid #eef0f3;
    padding: 0 8px;
    gap: 4px;
}
.shop-tabs > li {
    flex: 1;
    list-style: none;
    margin: 0;
    padding: 0;
}
.shop-tabs > li > a {
    display: block;
    padding: 16px 20px;
    text-align: center;
    color: #8892a4;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: all 0.25s ease;
    border-radius: 0;
}
.shop-tabs > li > a:hover {
    color: #4e6ef2;
    background: rgba(78,110,242,0.04);
}
.shop-tabs > li.active > a {
    color: #4e6ef2;
    border-bottom-color: #4e6ef2;
    background: #fff;
}

/* Tab 内容区域 */
.shop-tab-content {
    padding: 24px 28px 28px;
}

/* 内容区 modal-body 去掉 */
.shop-premium-card .modal-body {
    padding: 0;
}

/* 输入框统一美化 */
.shop-premium-card .form-control {
    border: 1px solid #e4e7ed;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    transition: all 0.2s;
}
.shop-premium-card .form-control:focus {
    border-color: #4e6ef2;
    box-shadow: 0 0 0 3px rgba(78,110,242,0.12);
}
.shop-premium-card .input-group-addon {
    background: #f5f7fa;
    border: 1px solid #e4e7ed;
    color: #606266;
    border-radius: 8px 0 0 8px;
}

/* 按钮美化 */
.shop-premium-card .btn-primary {
    background: linear-gradient(120deg, #4e6ef2, #6c5ce7);
    border: none;
    border-radius: 10px;
    padding: 10px 24px;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(78,110,242,0.25);
    transition: all 0.25s ease;
}
.shop-premium-card .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(78,110,242,0.35);
}
.shop-premium-card .btn-block {
    width: 100%;
}

/* 查询说明 list-group */
.shop-premium-card .list-group-item {
    border: 1px solid #eef0f3;
    border-radius: 10px;
    background: #fafbfc;
    margin-bottom: 16px;
    font-size: 13px;
    color: #606266;
    padding: 14px 18px;
    line-height: 1.7;
}

/* 表格美化 */
.shop-premium-card .table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #fafbfc;
}
.shop-premium-card .table > thead > tr > th {
    background: #f5f7fa;
    border-bottom: 1px solid #e4e7ed;
    color: #606266;
    font-weight: 600;
    font-size: 13px;
    padding: 12px 10px;
}
.shop-premium-card .table > tbody > tr > td {
    padding: 12px 10px;
    border-top: 1px solid #eef0f3;
    font-size: 13px;
    color: #606266;
    vertical-align: middle;
}
.shop-premium-card .table > tbody > tr:hover {
    background: rgba(78,110,242,0.04);
}

/* 购前必看按钮美化（shop.inc.php 内联的） */
.shop-premium-card a.btn.custom-btn,
.shop-premium-card .custom-btn {
    display: flex !important;
    align-items: center;
    gap: 12px;
    background: linear-gradient(120deg, #fff8e1, #fff3cd) !important;
    border: 1px solid #ffeeba !important;
    border-radius: 12px !important;
    padding: 14px 18px !important;
    margin-bottom: 20px !important;
    width: 100% !important;
    text-decoration: none !important;
    transition: all 0.25s ease !important;
    box-shadow: 0 2px 8px rgba(255,193,7,0.08);
}
.shop-premium-card a.btn.custom-btn:hover,
.shop-premium-card .custom-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,193,7,0.15);
}
.shop-premium-card .custom-btn img {
    flex-shrink: 0;
    width: 28px !important;
    height: 28px !important;
    border-radius: 6px;
    object-fit: contain;
}
.shop-premium-card .custom-btn div {
    flex: 1;
}
.shop-premium-card .custom-btn i.fa {
    color: #e6a23c;
    font-size: 16px;
}

/* 商品分类 tab 里的卡片 */
.shop-premium-card .col-lg-4.col-xs-6 {
    margin-bottom: 12px;
}

/* 分类卡片 hover 效果 */
.shop-premium-card [class*="col-xs-6"] a {
    display: block;
    border-radius: 12px;
    transition: all 0.25s;
}
.shop-premium-card [class*="col-xs-6"] a:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

/* 移动端适配 */
@media (max-width: 768px) {
    .shop-premium-wrap {
        margin: 10px auto;
        padding: 0 10px;
    }
    .shop-card-header {
        padding: 18px 20px;
    }
    .shop-tab-content {
        padding: 18px 20px 24px;
    }
    .shop-card-title {
        font-size: 17px;
    }
    .shop-tabs > li > a {
        padding: 12px 10px;
        font-size: 13px;
    }
}
</style>
<div class="wrapper shop-premium-wrap">
    <div class="shop-premium-card">
        <!-- 顶部渐变 Header -->
        <div class="shop-card-header">
            <h3 class="shop-card-title">
                <i class="fa fa-shopping-bag"></i>
                自助下单
            </h3>
            <span class="shop-card-balance pull-right">
                <i class="fa fa-wallet"></i>
                余额：<b><?php echo $userrow['rmb']?></b> 元
            </span>
        </div>

        <!-- Tab 切换 -->
        <div class="modal-body">
            <ul class="nav shop-tabs">
                <li class="active">
                    <a href="#onlinebuy" data-toggle="tab">
                        <i class="fa fa-cart-plus"></i> 在线下单
                    </a>
                </li>
                <li>
                    <a href="#query" data-toggle="tab" id="tab-query">
                        <i class="fa fa-search"></i> 查询订单
                    </a>
                </li>
            </ul>

            <div id="myTabContent" class="tab-content shop-tab-content">
                <div class="tab-pane fade in active" id="onlinebuy">
                    <?php include 'shop.inc.php'; ?>
                </div>
                <div class="tab-pane fade in" id="query">
                    <ul class="list-group animated bounceIn">
                        <li class="list-group-item">
                            <i class="fa fa-info-circle" style="color:#4e6ef2;margin-right:6px;"></i>
                            <?php echo $conf['gg_search']?>
                        </li>
                    </ul>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-search"></i> 查询内容
                            </div>
                            <input type="text" name="qq" id="qq3" value="" class="form-control" placeholder="请输入下单账号（留空则根据浏览器缓存查询）" required="">
                        </div>
                    </div>
                    <input type="submit" id="submit_query" class="btn btn-primary btn-block" value="立即查询">
                    <div id="result2" style="display:none;">
                        <center><small><font color="#ff0000">手机用户可以左右滑动</font></small></center>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>账号</th>
                                        <th>名称</th>
                                        <th>份数</th>
                                        <th class="hidden-xs">时间</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody id="list"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include './foot.php';?>
<script src="<?php echo $cdnpublic?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script type="text/javascript">
var isModal=false;
var homepage=false;
var hashsalt=<?php echo $addsalt_js?>;
$(function() {
	$("img.lazy").lazyload({effect: "fadeIn"});
<?php if($conf['shoppingcart']==1){?>
$.ajax({
	type : "GET",
	url : "../ajax.php?act=cart_info",
	dataType : 'json',
	async: true,
	success : function(data) {
		if(data.count != null && data.count>0){
			$('#cart_count').html(data.count);
			$('#alert_cart').show();
		}
	}
});
<?php }?>
});
</script>
<script src="../assets/js/usershop.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
