<?php
if(!defined('IN_CRONLITE'))exit();

$tid=isset($_GET['tid'])?intval($_GET['tid']):0;
$tool=$DB->getRow("SELECT * FROM pre_tools WHERE tid='$tid' LIMIT 1");
if(!$tool || $tool['active']!=1)sysmsg('当前商品不存在');
if(isset($_GET['skey']) && $_GET['skey']==md5($tid.SYS_KEY.$tid))$skey_check=true;
else $skey_check=false;

// 初始化价格对象
if ($islogin2 == 1) {
    $price_obj = new \lib\Price($userrow['zid'], $userrow);
} elseif ($is_fenzhan == true) {
    $price_obj = new \lib\Price($siterow['zid'], $siterow);
} else {
    $price_obj = new \lib\Price(1);
}

$price_obj->setToolInfo($tool['tid'],$tool);
$price=$price_obj->getToolPrice($tool['tid']);
if($price===false)sysmsg('商品价格信息不存在');

// 获取商品库存
if($tool['is_curl'] == 4){
    $count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid='{$tool['tid']}' and orderid=0");
    $stock = $count;
} else {
    $stock = $tool['stock'];
    $count = $stock;
}

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

?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <title><?php echo $tool['name']?> - <?php echo $conf['sitename']?></title>
    <meta name="keywords" content="<?php echo $conf['keywords']?>">
    <meta name="description" content="<?php echo $conf['description']?>">
    <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: "Microsoft YaHei", sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 15px;
        }
        .product-header {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .product-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .product-price {
            color: #4e8cff;
            font-size: 24px;
            font-weight: bold;
        }
        .product-stock {
            color: #4CAF50;
            font-size: 14px;
        }
        .product-desc {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .desc-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .desc-title:before {
            content: "";
            width: 3px;
            height: 16px;
            background: #4e8cff;
            margin-right: 8px;
            border-radius: 2px;
        }
        .order-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .form-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .form-title:before {
            content: "";
            width: 3px;
            height: 16px;
            background: #4e8cff;
            margin-right: 8px;
            border-radius: 2px;
        }
        .form-control {
            border: 1px solid #ddd;
            box-shadow: none;
            height: 40px;
        }
        .form-control:focus {
            border-color: #4e8cff;
            box-shadow: none;
        }
        .submit-btn {
            background: #4e8cff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            margin-top: 15px;
        }
        .back-btn {
            display: inline-block;
            padding: 8px 15px;
            background: #f8f9fa;
            color: #666;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 15px;
        }
        .back-btn:hover {
            background: #e9ecef;
            text-decoration: none;
            color: #333;
        }
        /* 支付弹窗样式 */
        .pay-modal {
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
        }
        .pay-header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 20px;
        }
        .pay-header:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #4e8cff;
            border-radius: 3px;
        }
        .pay-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .pay-amount {
            font-size: 28px;
            color: #4e8cff;
            font-weight: bold;
            margin: 15px 0;
        }
        .pay-amount small {
            font-size: 14px;
            color: #666;
        }
        .pay-info {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #666;
        }
        .pay-info p {
            margin: 5px 0;
        }
        .pay-channel-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            text-align: center;
            position: relative;
        }
        .pay-channel-title:before,
        .pay-channel-title:after {
            content: "";
            position: absolute;
            top: 50%;
            width: 60px;
            height: 1px;
            background: #eee;
        }
        .pay-channel-title:before {
            left: 20px;
        }
        .pay-channel-title:after {
            right: 20px;
        }
        .pay-channels .btn {
            margin-bottom: 10px;
            padding: 12px;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .pay-channels .btn i {
            margin-right: 8px;
            font-size: 20px;
        }
        .pay-channels .btn-alipay {
            background: #027AFF;
            color: #fff;
            border: none;
        }
        .pay-channels .btn-wxpay {
            background: #09BB07;
            color: #fff;
            border: none;
        }
        .pay-channels .btn-qqpay {
            background: #12B7F5;
            color: #fff;
            border: none;
        }
        .pay-channels .btn-balance {
            background: #FF9800;
            color: #fff;
            border: none;
        }
        .pay-channels .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="./" class="back-btn"><i class="fa fa-angle-left"></i> 返回列表</a>
        
        <div class="product-header">
            <div class="product-title"><?php echo $tool['name']?></div>
            <div class="product-info">
                <div class="product-price">¥<?php echo $price?></div>
                <div class="product-stock"><?php echo $stockText?></div>
            </div>
        </div>

        <?php if($tool['desc']){?>
        <div class="product-desc">
            <div class="desc-title">商品简介</div>
            <div class="desc-content"><?php echo $tool['desc']?></div>
        </div>
        <?php }?>

        <div class="order-form">
            <div class="form-title">填写信息</div>
            <form id="submit-form" onsubmit="return false;">
                <input type="hidden" name="tid" id="tid" value="<?php echo $tid?>">
                <input type="hidden" name="skey" id="skey" value="<?php echo isset($_GET['skey'])?$_GET['skey']:''?>">
                <div class="form-group">
                    <label><?php echo !empty($tool['input']) ? htmlspecialchars($tool['input']) : '联系方式' ?>:</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-user"></i></div>
                        <input type="text" name="inputvalue" id="inputvalue" class="form-control" placeholder="请输入<?php echo !empty($tool['input']) ? htmlspecialchars($tool['input']) : '联系方式' ?>" required>
                    </div>
                </div>
                <?php
                // 处理更多输入框 - 博客地址：zhonguo.ren QQ群：915043052
                if (!empty($tool['inputs'])) {
                    $inputs = explode('|', $tool['inputs']);
                    $input_index = 2;
                    foreach ($inputs as $input_name) {
                        $input_name = trim($input_name);
                        if (!empty($input_name)) {
                            // 判断是否为收货地址字段，如果是则添加oninput事件
                            $is_address = (strpos($input_name, '收货地址') !== false || strpos($input_name, '地址') !== false);
                            $oninput_event = $is_address ? 'oninput="calculateRegionPrice()"' : '';
                            
                            // 根据输入框标题选择合适的图标 - 博客地址：zhonguo.ren QQ群：915043052
                            $icon_class = 'fa-pencil-square-o';
                            if (strpos($input_name, '收货地址') !== false || strpos($input_name, '地址') !== false) {
                                $icon_class = 'fa-map-marker';
                            } elseif (strpos($input_name, '手机号') !== false || strpos($input_name, '电话') !== false || strpos($input_name, '手机') !== false) {
                                $icon_class = 'fa-phone';
                            } elseif (strpos($input_name, '姓名') !== false || strpos($input_name, '名字') !== false || strpos($input_name, '收货人') !== false) {
                                $icon_class = 'fa-user';
                            } elseif (strpos($input_name, 'QQ') !== false || strpos($input_name, 'qq') !== false || strpos($input_name, '账号') !== false) {
                                $icon_class = 'fa-qq';
                            } elseif (strpos($input_name, '邮箱') !== false || strpos($input_name, 'Email') !== false || strpos($input_name, 'email') !== false) {
                                $icon_class = 'fa-envelope';
                            } elseif (strpos($input_name, '微信') !== false || strpos($input_name, 'WeChat') !== false) {
                                $icon_class = 'fa-wechat';
                            }
                ?>
                <div class="form-group">
                    <label><?php echo htmlspecialchars($input_name) ?>:</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa <?php echo $icon_class; ?>"></i></div>
                        <input type="text" name="inputvalue<?php echo $input_index; ?>" id="inputvalue<?php echo $input_index; ?>" class="form-control" placeholder="请输入<?php echo htmlspecialchars($input_name) ?>" required <?php echo $oninput_event; ?>>
                    </div>
                </div>
                <?php
                            $input_index++;
                        }
                    }
                }
                ?>
                <div class="form-group">
                    <label>购买数量:</label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-shopping-cart"></i></div>
                        <input type="number" name="num" id="num" class="form-control" value="1" placeholder="请输入购买数量" required onchange="calculateRegionPrice()">
                    </div>
                </div>
                <div class="price-info" id="price-info" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                    <div style="margin-bottom: 8px;">
                        <span style="color: #666;">商品原价：</span>
                        <span style="color: #999;">¥<span id="original-price"><?php echo $price?></span></span>
                    </div>
                    <div style="margin-bottom: 8px;" id="add-price-row">
                        <span style="color: #666;">地区加价：</span>
                        <span style="color: #ff6b6b;">+¥<span id="add-price-amount">0.00</span></span>
                        <span style="color: #999; font-size: 12px;">（<span id="region-name"></span>）</span>
                    </div>
                    <div style="font-size: 16px; font-weight: bold; color: #4e8cff;">
                        <span>最终价格：</span>
                        <span>¥<span id="final-price"><?php echo $price?></span></span>
                    </div>
                </div>
                <?php if(isset($conf['captcha_open']) && $conf['captcha_open']==1){?>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-shield"></i></div>
                        <input type="text" name="code" id="code" class="form-control" placeholder="请输入验证码" required>
                        <span class="input-group-addon" style="padding: 0">
                            <img src="./code.php?r=<?php echo time();?>" height="43" onclick="this.src='./code.php?r='+Math.random();" title="点击更换验证码">
                        </span>
                    </div>
                </div>
                <?php }?>
                <button type="button" class="submit-btn" id="submit_buy">立即购买</button>
            </form>
        </div>
    </div>

    <script src="<?php echo $cdnpublic?>jquery/3.4.1/jquery.min.js"></script>
    <script src="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
    <script>
    var hashsalt=<?php echo $addsalt_js?>;
    var originalPrice = <?php echo $price?>;
    var tid = <?php echo $tid?>;
    var isCalculating = false;
    var finalPrice = originalPrice;

    function calculateRegionPrice() {
        if (isCalculating) return;

        // 动态查找收货地址字段 - 博客地址：zhonguo.ren QQ群：915043052
        var addressInput = $('input[placeholder*="收货地址"], input[placeholder*="地址"]');
        var address = addressInput.length > 0 ? addressInput.val() : '';
        var num = parseInt($('#num').val()) || 1;

        if (!address || address.trim() === '') {
            $('#price-info').hide();
            finalPrice = originalPrice * num;
            $('#final-price').text((originalPrice * num).toFixed(2));
            return;
        }

        isCalculating = true;

        $.ajax({
            type: 'POST',
            url: '../includes/ajax_region_price.php?act=calculate',
            data: {
                tid: tid,
                address: address
            },
            dataType: 'json',
            success: function(data) {
                isCalculating = false;
                if (data.code === 0) {
                    var result = data.data;
                    var unitPrice = result.final_price;
                    var addPrice = result.add_price_amount;

                    $('#original-price').text((originalPrice * num).toFixed(2));

                    if (result.matched) {
                        $('#add-price-amount').text((addPrice * num).toFixed(2));
                        $('#region-name').text(result.region_name);
                        $('#add-price-row').show();
                        $('#price-info').show();
                    } else {
                        $('#add-price-row').hide();
                        $('#price-info').show();
                    }

                    finalPrice = unitPrice * num;
                    $('#final-price').text(finalPrice.toFixed(2));
                } else {
                    $('#price-info').hide();
                    finalPrice = originalPrice * num;
                    $('#final-price').text(finalPrice.toFixed(2));
                }
            },
            error: function() {
                isCalculating = false;
                $('#price-info').hide();
                finalPrice = originalPrice * num;
                $('#final-price').text(finalPrice.toFixed(2));
            }
        });
    }

    $(document).ready(function(){
        $("#submit_buy").click(function(){
            var tid = $("#tid").val();
            var inputvalue = $("#inputvalue").val();
            var num = $("#num").val();
            // 动态查找收货地址字段 - 博客地址：zhonguo.ren QQ群：915043052
            var addressInput = $('input[placeholder*="收货地址"], input[placeholder*="地址"]');
            var address = addressInput.length > 0 ? addressInput.val() : '';
            <?php if(isset($conf['captcha_open']) && $conf['captcha_open']==1){?>
            var code = $("#code").val();
            if(code==''){layer.alert('验证码不能为空！');return false;}
            <?php }?>
            if(inputvalue=='' || tid=='' || num==''){layer.alert('请确保每项不能为空！');return false;}
            if(num>1000){layer.alert('每次只能下单1000个！');return false;}
            
            // 验证手机号码输入框 - 博客地址：zhonguo.ren QQ群：915043052
            var mainInputLabel = $('.form-group:eq(0) label').text().trim();
            if(mainInputLabel.indexOf('手机号码') !== -1 || mainInputLabel.indexOf('手机号') !== -1 || mainInputLabel.indexOf('手机') !== -1){
                if(!/^1\d{10}$/.test(inputvalue)){
                    layer.alert('请输入正确的11位手机号码！');
                    return false;
                }
            }
            
            // 验证更多输入框中的手机号码 - 博客地址：zhonguo.ren QQ群：915043052
            $('.form-group').each(function(){
                var label = $(this).find('label').text().trim();
                var input = $(this).find('input[type="text"]');
                if((label.indexOf('手机号码') !== -1 || label.indexOf('手机号') !== -1 || label.indexOf('手机') !== -1) && input.length > 0){
                    var value = input.val().trim();
                    if(!/^1\d{10}$/.test(value)){
                        layer.alert('请输入正确的11位手机号码！');
                        return false;
                    }
                }
            });
            
            // 收集所有输入框数据 - 博客地址：zhonguo.ren QQ群：915043052
            var data = {
                type: "buy",
                tid: tid,
                inputvalue: inputvalue,
                num: num,
                hashsalt: hashsalt
            };
            
            // 添加收货地址
            if (address) {
                data['address'] = address;
            }
            
            <?php if(isset($conf['captcha_open']) && $conf['captcha_open']==1){?>
            data['code'] = code;
            <?php }?>
            
            // 添加更多输入框的数据
            <?php
            if (!empty($tool['inputs'])) {
                $inputs = explode('|', $tool['inputs']);
                $input_index = 2;
                foreach ($inputs as $input_name) {
                    $input_name = trim($input_name);
                    if (!empty($input_name)) {
            ?>
            var inputvalue<?php echo $input_index; ?> = $("#inputvalue<?php echo $input_index; ?>").val();
            if(inputvalue<?php echo $input_index; ?> == ''){layer.alert('请确保每项不能为空！');return false;}
            data['inputvalue<?php echo $input_index; ?>'] = inputvalue<?php echo $input_index; ?>;
            <?php
                        $input_index++;
                    }
                }
            }
            ?>
            
            var ii = layer.load(2, {shade:[0.1,'#fff']});
            $.ajax({
                type : "POST",
                url : "ajax.php?act=pay",
                data : data,
                dataType : 'json',
                success : function(data) {
                    layer.close(ii);
                    if(data.code == 0){
                        var paymsg = '';
                        if(data.pay_alipay>0){
                            paymsg+='<button class="btn btn-alipay btn-block" onclick="window.location.href=\'other/submit.php?type=alipay&orderid='+data.trade_no+'&redirect_url='+encodeURIComponent('./?mod=query&orderid='+data.trade_no)+'\'"><i class="fa fa-credit-card"></i>支付宝付款</button>';
                        }
                        if(data.pay_wxpay>0){
                            paymsg+='<button class="btn btn-wxpay btn-block" onclick="window.location.href=\'other/submit.php?type=wxpay&orderid='+data.trade_no+'&redirect_url='+encodeURIComponent('./?mod=query&orderid='+data.trade_no)+'\'"><i class="fa fa-wechat"></i>微信支付</button>';
                        }
                        if(data.pay_qqpay>0){
                            paymsg+='<button class="btn btn-qqpay btn-block" onclick="window.location.href=\'other/submit.php?type=qqpay&orderid='+data.trade_no+'&redirect_url='+encodeURIComponent('./?mod=query&orderid='+data.trade_no)+'\'"><i class="fa fa-qq"></i>QQ钱包付款</button>';
                        }
                        if(data.pay_rmb>0){
                            paymsg+='<button class="btn btn-balance btn-block" onclick="dopay(\'rmb\',\''+data.trade_no+'\')"><i class="fa fa-wallet"></i>余额支付（剩'+data.user_rmb+'元）</button>';
                        }
                        // 设置user_order cookie，用于支付成功后获取订单号
                        document.cookie = 'user_order=' + data.trade_no + '; path=/';
                        
                        layer.open({
                            type: 1,
                            title: false,
                            closeBtn: true,
                            shadeClose: true,
                            skin: 'layui-layer-molv',
                            area: ['420px', 'auto'],
                            content: '<div class="pay-modal">' +
                                '<div class="pay-header">' +
                                '<div class="pay-title">订单提交成功</div>' +
                                '<div class="pay-amount">￥<span>'+data.need+'</span><small>元</small></div>' +
                                '</div>' +
                                '<div class="pay-info">' +
                                '<p><span>订单号：</span>'+data.trade_no+'</p>' +
                                (data.paymsg ? '<p>'+data.paymsg+'</p>' : '') +
                                '</div>' +
                                '<div class="pay-channel-title">请选择支付方式</div>' +
                                '<div class="pay-channels">' +
                                paymsg +
                                '</div>' +
                                '</div>'
                        });
                    }else if(data.code == 1){
                        layer.alert(data.msg,{icon:1},function(){window.location.href='?buyok=1'});
                    }else if(data.code == 4){
                        var confirmobj = layer.confirm('请登录后再购买，是否现在登录？', {
                          btn: ['登录','注册','取消']
                        }, function(){
                            window.location.href='./user/login.php';
                        }, function(){
                            window.location.href='./user/reg.php';
                        }, function(){
                            layer.close(confirmobj);
                        });
                    }else{
                        layer.alert(data.msg);
                    }
                },
                error:function(data){
                    layer.close(ii);
                    layer.msg('服务器错误');
                }
            });
        });
    });

    function dopay(type,orderid){
        if(type == 'rmb'){
            var ii = layer.msg('正在提交订单请稍候...', {icon: 16,shade: 0.5,time: 15000});
            $.ajax({
                type : "POST",
                url : "ajax.php?act=payrmb",
                data : {orderid: orderid},
                dataType : 'json',
                success : function(data) {
                    layer.close(ii);
                    if(data.code == 1){
                        alert(data.msg);
                        window.location.href='./?mod=query&orderid='+orderid;
                    }else if(data.code == -2){
                        alert(data.msg);
                        window.location.href='./?mod=query&orderid='+orderid;
                    }else if(data.code == -3){
                        var confirmobj = layer.confirm('你的余额不足，请充值！', {
                          btn: ['立即充值','取消']
                        }, function(){
                            window.location.href='./user/recharge.php';
                        }, function(){
                            layer.close(confirmobj);
                        });
                    }else if(data.code == -4){
                        var confirmobj = layer.confirm('你还未登录，是否现在登录？', {
                          btn: ['登录','注册','取消']
                        }, function(){
                            window.location.href='./user/login.php';
                        }, function(){
                            window.location.href='./user/reg.php';
                        }, function(){
                            layer.close(confirmobj);
                        });
                    }else{
                        layer.alert(data.msg);
                    }
                } 
            });
        }else{
            window.location.href='other/submit.php?type='+type+'&orderid='+orderid;
        }
    }
    </script>
</body>
</html> 