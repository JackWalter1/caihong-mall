<?php
if(!defined('IN_CRONLITE'))exit();

// 处理支付成功跳转
if(isset($_GET['buyok']) && $_GET['buyok']==1){
    // 优先使用用户自己的订单号（从cookie中获取）
    $orderid = isset($_COOKIE['user_order']) ? $_COOKIE['user_order'] : null;
    
    // 如果cookie中没有订单号，再获取最新的订单
    if(!$orderid){
        $latest_order = $DB->getRow("SELECT trade_no FROM pre_pay WHERE status=1 ORDER BY addtime DESC LIMIT 1");
        if($latest_order){
            $orderid = $latest_order['trade_no'];
        }
    }
    
    // 如果有订单号，跳转到订单查询页面
    if($orderid){
        header('Location: ./?mod=query&orderid='.$orderid);
        exit;
    }
}

$classhide = explode(',',$siterow['class']);
$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$class=array();
while($res = $rs->fetch()){
    if($is_fenzhan && in_array($res['cid'], $classhide))continue;
    $class[$res['cid']]=$res;
}
$count = array();
foreach($class as $row){
    $count[$row['cid']] = $DB->getColumn("SELECT count(*) FROM pre_tools WHERE cid='{$row['cid']}' AND active=1");
}

// 获取默认分类ID
if(empty($_GET['cid'])){
    $first_row = reset($class);
    $cid = is_array($first_row) && isset($first_row['cid']) ? $first_row['cid'] : 0;
}else{
    $cid = intval($_GET['cid']);
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <title><?php echo $hometitle?></title>
    <meta name="keywords" content="<?php echo $conf['keywords']?>">
    <meta name="description" content="<?php echo $conf['description']?>">
    <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: "Microsoft YaHei", sans-serif;
            padding-bottom: 70px; /* 增加底部内边距 */
            min-height: 100vh; /* 确保页面至少有一个屏幕高度 */
            position: relative; /* 为fixed定位提供参考 */
        }
        .header {
            background: #4e8cff;
            color: white;
            padding: 15px;
            border-radius: 0 0 10px 10px;
        }
        .shop-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .shop-logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .shop-logo i {
            font-size: 35px;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .shop-icon {
            font-size: 24px;
            margin-right: 10px;
            display: none;
        }
        .shop-status {
            margin-left: 10px;
            font-size: 12px;
        }
        .warning {
            color: #ff9800;
        }
        .success {
            color: #fff;
            background: rgba(255, 255, 255, 0.15);
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            margin-bottom: 8px;
            font-weight: 500;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .success i {
            margin-right: 4px;
            color: #2ECC71;
        }
        .guarantee {
            color: #fff;
            background: rgba(255, 87, 34, 0.15);
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            margin-bottom: 8px;
            font-weight: bold;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 87, 34, 0.3);
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .guarantee i {
            margin-right: 4px;
            color: #FF5722;
        }
        .shop-time {
            color: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            margin-top: 5px;
            font-size: 13px;
        }
        .shop-time i {
            margin-right: 6px;
            color: rgba(255,255,255,0.7);
        }
        .function-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding: 0 2px;
            flex-wrap: nowrap; /* 防止按钮换行 */
        }
        .function-btn {
            flex: 1;
            background: rgba(255, 255, 255, 0.15);
            color: #fff !important; /* 确保链接文字为白色 */
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 5px; /* 减小内边距 */
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px; /* 减小字体大小 */
            text-decoration: none; /* 移除下划线 */
            white-space: nowrap; /* 防止文字换行 */
        }
        .function-btn i {
            margin-right: 5px;
            font-size: 16px;
        }
        .function-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #333;
            display: flex;
            align-items: center;
        }
        .section-title:before {
            content: "";
            width: 4px;
            height: 16px;
            background: #4e8cff;
            margin-right: 8px;
            border-radius: 2px;
        }
        .search-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin: 8px 0;
        }
        .search-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .search-btn {
            width: 100%;
            background: #4e8cff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
        }
        .category-box {
            display: flex;
            gap: 10px;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        .category-item {
            flex: 1;
            min-width: 150px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .category-item div {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .category-item.active {
            background: #4e8cff;
            color: white;
        }
        .category-item.active div {
            color: rgba(255,255,255,0.8);
        }
        #goodslist {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0 -4px;
        }
        .product-card {
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            width: 100%;  /* 修改这里，使卡片占满整行 */
            flex-direction: column;
            position: relative;
            border: 2px solid transparent;
            margin-bottom: 8px;  /* 添加底部间距 */
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.12);
        }
        .product-card.active {
            border-color: #4e8cff;
            background: linear-gradient(to bottom right, rgba(78,140,255,0.05), rgba(78,140,255,0.02));
            box-shadow: 0 3px 12px rgba(78,140,255,0.15);
        }
        .product-card.active:before {
            content: "";
            position: absolute;
            top: -2px;
            right: -2px;
            width: 20px;
            height: 20px;
            background: #4e8cff;
            border-radius: 0 8px 0 8px;
        }
        .product-card.active:after {
            content: "✓";
            position: absolute;
            top: 0px;
            right: 4px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .product-card.active .product-title {
            color: #4e8cff;
            font-weight: 500;
        }
        .product-card.active .product-price {
            font-size: 16px;
        }
        .product-card.active .product-stock {
            color: #2E7D32;
        }
        .product-title {
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
            height: 40px;
            word-break: break-all;
            white-space: normal;
        }
        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 10px;
            margin-top: auto;
            flex-wrap: wrap;
        }
        .product-price {
            color: #4e8cff;
            font-size: 15px;
            font-weight: bold;
            white-space: nowrap;
        }
        .product-stock {
            color: #4CAF50;
            font-size: 12px;
            white-space: nowrap;
        }
        .product-sales {
            color: #666;
            font-size: 12px;
            white-space: nowrap;
            margin-right: 10px;
        }
        .bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 10px 15px;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: none;
            z-index: 1000;
        }
        .container {
            max-width: 800px; /* 限制容器最大宽度 */
            margin: 0 auto;
            padding: 15px;
        }
        .pay-btn {
            background: #4e8cff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        .order-flow {
            font-size: 12px;
            color: #666;
            margin-top: 15px;
        }
        .content-wrapper {
            margin-bottom: 80px; /* 为底部按钮预留空间 */
        }
        .product-desc {
            font-size: 14px;
            color: #333;
            margin: 15px 0;
            line-height: 1.8;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            display: none;
            word-break: break-all;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }
        .product-desc-title {
            font-size: 15px;
            color: #4e8cff;
            font-weight: 500;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .product-desc-title:before {
            content: "";
            width: 3px;
            height: 15px;
            background: #4e8cff;
            margin-right: 8px;
            border-radius: 2px;
        }
        .product-desc:after {
            content: "";
            position: absolute;
            top: 0;
            left: -4px;
            right: 0;
            height: 25px;
            background: #fff;
            z-index: 1;
        }
        .product-desc img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin: 10px 0;
            display: block;
        }
        .product-desc > *:first-child {
            margin-top: 0;
        }
        .product-desc > *:last-child {
            margin-bottom: 0;
        }
        .product-desc h1, .product-desc h2, .product-desc h3, 
        .product-desc h4, .product-desc h5, .product-desc h6 {
            margin: 15px 0 10px;
            color: #333;
        }
        .product-desc pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #eee;
            overflow-x: auto;
        }
        .product-desc code {
            background: #f8f9fa;
            padding: 2px 5px;
            border-radius: 3px;
            color: #e83e8c;
        }
        @media (max-width: 768px) {
            #goodslist {
                gap: 6px;
                margin: 0 -3px;
            }
            .product-card {
                min-width: 140px;
            }
            .product-title {
                min-width: 120px;
            }
        }
    </style>
</head>
<body>
    <!-- 弹出公告 -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $conf['sitename']?></h4>
                </div>
                <div class="modal-body">
                    <?php echo $conf['modal']?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">知道了</button>
                </div>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="container">
            <div class="header">
                <div class="shop-info">
                    <div class="shop-logo">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div>
                        <h4><?php echo $conf['sitename']?></h4>
                        <div>客服QQ：<?php echo $conf['kfqq']?></div>
                    </div>
                </div>
                <div class="shop-status">
                    <div class="success"><i class="fa fa-check-circle"></i> 商家已认证</div>
                    <div class="guarantee"><i class="fa fa-shield"></i> 信誉保证金：10000元</div>
                    <div class="shop-time"><i class="fa fa-clock-o"></i> 开店时间：<?php echo $conf['build']?></div>
                </div>
                <div class="function-buttons">
                    <a href="./user/" class="function-btn">
                        <i class="fa fa-user"></i>注册/登录
                    </a>
                    <a href="./?mod=query" class="function-btn">
                        <i class="fa fa-search"></i>订单查询
                    </a>
                    <?php if($conf['fenzhan_buy']==1){?>
                    <a href="./user/regsite.php" class="function-btn">
                        <i class="fa fa-shopping-bag"></i>开通分站
                    </a>
                    <?php }?>
                </div>
            </div>

            <!-- 公告区域 -->
            <div class="announcement" style="background: #fff; padding: 15px; border-radius: 10px; margin: 15px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #4e8cff;">
                <div style="color: #666; line-height: 1.5;">
                    <?php 
                    // 优先使用后台设置的首页公告
                    if(!empty($conf['anounce'])){
                        echo $conf['anounce'];
                    } elseif(!empty($conf['modal'])){
                        // 备选方案：使用弹窗公告内容
                        echo $conf['modal'];
                    } else {
                        echo '暂无公告';
                    }
                    ?>
                </div>
            </div>

            <div class="section-title">
                <span>商品搜索</span>
            </div>
            <div class="search-box">
                <input type="text" class="search-input" placeholder="输入关键词搜索商品">
                <button class="search-btn">商品查询</button>
            </div>

            <div class="section-title">
                <span>选择分类</span>
            </div>
            <div class="category-box">
                <?php foreach($class as $row){?>
                <div class="category-item<?php echo $row['cid']==$cid?' active':''?>" data-cid="<?php echo $row['cid']?>" onclick="window.location.href='./?cid=<?php echo $row['cid']?>'">
                    <?php echo $row['name']?>
                    <div>包含<?php echo $count[$row['cid']]?>种商品</div>
                </div>
                <?php }?>
            </div>

            <div class="section-title">
                <span>选择商品</span>
            </div>
            <div id="goodslist">
            <?php 
            // 初始化价格对象
            if ($islogin2 == 1) {
                $price_obj = new \lib\Price($userrow['zid'], $userrow);
            } elseif ($is_fenzhan == true) {
                $price_obj = new \lib\Price($siterow['zid'], $siterow);
            } else {
                $price_obj = new \lib\Price(1);
            }
            
            $rs=$DB->query("SELECT * FROM pre_tools WHERE cid='{$cid}' AND active=1 ORDER BY sort ASC");
            while($res = $rs->fetch()){
                if($res['is_curl']==4){
                    $count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid='{$res['tid']}' and orderid=0");
                }else{
                    $count = $res['stock'];
                }
                
                // 计算商品价格
                $price_obj->setToolInfo($res['tid'], $res);
                $price = $price_obj->getToolPrice($res['tid']);
                
                // 根据配置的库存显示方式显示库存信息
                        if($conf['agodn_stock_display'] == 1 && $count !== null){
                            // 模糊库存范围显示
                            if($count >= 1000){
                                $stockText = '库存充足';
                            } elseif($count >= 500){
                                $stockText = '库存500+';
                            } elseif($count >= 100){
                                $stockText = '库存100+';
                            } elseif($count >= 50){
                                $stockText = '库存50+';
                            } elseif($count >= 10){
                                $stockText = '库存10+';
                            } else {
                                $stockText = '库存'.$count.'张';
                            }
                        } else {
                            // 准确库存显示
                            $stockText = $count === null ? '无限' : '库存'.$count.'张';
                        }
                // 计算销量 - 通过查询pre_pay表中status=1的订单数量
                $sales = $DB->getColumn("SELECT COUNT(*) FROM pre_pay WHERE tid=:tid AND status=1", array(':tid' => $res['tid']));
                $salesInfo = '';
                if(isset($conf['agodn_show_sales']) && $conf['agodn_show_sales'] == 1) {
                    $salesInfo = '<div class="product-sales">已售'.$sales.'件</div>';
                }
                echo '<div class="product-card" data-tid="'.$res['tid'].'" data-price="'.$price.'">
                    <div class="product-title">'.$res['name'].'</div>
                    <div class="product-info">
                        <div class="product-price">¥'.$price.'</div>
                        '.$salesInfo.'
                        <div class="product-stock">'.$stockText.'</div>
                    </div>
                    <div class="product-desc" style="display:none;">
                        <div class="product-desc-title">商品简介</div>
                        <div class="product-desc-content"></div>
                    </div>
                </div>';
            }
            ?>
            </div>
        </div>
    </div>

    <div class="bottom-bar">
        <button class="pay-btn">立即购买</button>
    </div>

    <script src="<?php echo $cdnpublic?>jquery/3.4.1/jquery.min.js"></script>
    <script src="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
    <script src="//lib.baomitu.com/jquery.pjax/2.0.1/jquery.pjax.min.js"></script>
    <script src="./template/AgodN/js/goods.js?v=<?php echo VERSION ?>"></script>
    <script>
    // 库存显示配置
    var agodn_stock_display = <?php echo isset($conf['agodn_stock_display']) ? $conf['agodn_stock_display'] : 0;?>;
    // 销量显示配置
    var agodn_show_sales = <?php echo isset($conf['agodn_show_sales']) ? $conf['agodn_show_sales'] : 1;?>;
    function hex_md5(s) { 
        var hexcase = 0;  
        var chrsz   = 8;  
        
        function safe_add(x, y) {
            var lsw = (x & 0xFFFF) + (y & 0xFFFF);
            var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
            return (msw << 16) | (lsw & 0xFFFF);
        }
        
        function bit_rol(num, cnt) {
            return (num << cnt) | (num >>> (32 - cnt));
        }
        
        function md5_cmn(q, a, b, x, s, t) {
            return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
        }
        
        function md5_ff(a, b, c, d, x, s, t) {
            return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
        }
        
        function md5_gg(a, b, c, d, x, s, t) {
            return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
        }
        
        function md5_hh(a, b, c, d, x, s, t) {
            return md5_cmn(b ^ c ^ d, a, b, x, s, t);
        }
        
        function md5_ii(a, b, c, d, x, s, t) {
            return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
        }
        
        function core_md5(x, len) {
            x[len >> 5] |= 0x80 << ((len) % 32);
            x[(((len + 64) >>> 9) << 4) + 14] = len;
            
            var a =  1732584193;
            var b = -271733879;
            var c = -1732584194;
            var d =  271733878;
            
            for(var i = 0; i < x.length; i += 16) {
                var olda = a;
                var oldb = b;
                var oldc = c;
                var oldd = d;
                
                a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
                d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
                c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
                b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
                a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
                d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
                c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
                b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
                a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
                d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
                c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
                b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
                a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
                d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
                c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
                b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);
                
                a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
                d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
                c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
                b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
                a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
                d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
                c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
                b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
                a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
                d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
                c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
                b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
                a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
                d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
                c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
                b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);
                
                a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
                d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
                c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
                b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
                a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
                d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
                c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
                b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
                a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
                d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
                c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
                b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
                a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
                d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
                c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
                b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);
                
                a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
                d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
                c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
                b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
                a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
                d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
                c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
                b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
                a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
                d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
                c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
                b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
                a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
                d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
                c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
                b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);
                
                a = safe_add(a, olda);
                b = safe_add(b, oldb);
                c = safe_add(c, oldc);
                d = safe_add(d, oldd);
            }
            return Array(a, b, c, d);
        }
        
        function str2binl(str) {
            var bin = Array();
            var mask = (1 << chrsz) - 1;
            for(var i = 0; i < str.length * chrsz; i += chrsz)
                bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
            return bin;
        }
        
        function binl2hex(binarray) {
            var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
            var str = "";
            for(var i = 0; i < binarray.length * 4; i++) {
                str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
                       hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
            }
            return str;
        }
        
        return binl2hex(core_md5(str2binl(s), s.length * chrsz));
    }
    var sys_key = '<?php echo SYS_KEY?>';
    $(document).ready(function(){
        // 记录滚动位置
        var scrollPosition;
        
        // 初始化pjax
        GoodsHandler.initPjax();

        // 显示弹出公告
        <?php if(!empty($conf['modal'])){?>
        $('#myModal').modal('show');
        <?php }?>
        
        // 分类切换
        $('.category-item').click(function(e){
            e.preventDefault();
            // 保存当前滚动位置
            scrollPosition = $(window).scrollTop();
            
            var cid = $(this).data('cid');
            $('.category-item').removeClass('active');
            $(this).addClass('active');
            
            // 加载商品后恢复滚动位置
            GoodsHandler.loadGoods(cid, false).then(function(){
                $(window).scrollTop(scrollPosition);
            });
        });
        
        // 搜索功能
        $('.search-btn').click(function(e){
            e.preventDefault();
            var kw = $('.search-input').val();
            if(kw==''){
                layer.msg('请输入搜索内容');return;
            }
            GoodsHandler.loadGoods(kw, true);
        });
    });
    </script>
</body>
</html>