<?php
/**
 * 支付宝当面付扫码支付页面
 * 作者：教主 博客：zhonguo.ren Q群：915043052
 */

// 生产环境关闭错误显示，错误仅记录到日志
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/alipay_error.log');

// 实现订单名称替换函数
if (!function_exists('ordername_replace')) {
    /**
     * 订单名称替换函数
     * 替换订单名称模板中的变量
     * @param string $template 订单名称模板
     * @param string $name 商品名称
     * @param string $trade_no 交易订单号
     * @return string 替换后的订单名称
     * 作者：教主 博客：zhonguo.ren Q群：915043052
     */
    function ordername_replace($template, $name, $trade_no) {
        $replacements = array(
            '{name}' => $name,
            '{trade_no}' => $trade_no,
            '{ordername}' => $name,
            '{orderid}' => $trade_no
        );
        return strtr($template, $replacements);
    }
}

echo '<!-- 支付宝支付页面开始加载 -->';

// 尝试加载配置文件
try {
    require 'inc.php';
    echo '<!-- inc.php加载成功 -->';
} catch (Exception $e) {
    die('加载配置文件失败: ' . $e->getMessage());
}

@header('Content-Type: text/html; charset=UTF-8');

// 获取并验证订单号
$trade_no = daddslashes($_GET['trade_no']);
if(empty($trade_no)) {
    die('订单号不能为空！');
}

// 检查支付接口是否开启
if(!isset($conf['alipay_api']) || $conf['alipay_api'] != 3) {
    die('当前支付宝当面付接口未开启！当前设置: ' . (isset($conf['alipay_api']) ? $conf['alipay_api'] : '未设置'));
}

// 查询订单信息
        try {
            // 使用参数化查询修复SQL注入漏洞
            $row = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $trade_no]);
            if(!$row) {
                die('该订单号不存在，请返回来源地重新发起请求！订单号: ' . $trade_no);
            }
            echo '<!-- 订单查询成功 -->';
        } catch (Exception $e) {
            die('查询订单失败: ' . $e->getMessage());
        }

// 构造订单名称
$ordername = !empty($conf['ordername']) ? ordername_replace($conf['ordername'], $row['name'], $trade_no) : $row['name'];

// 检查是否有必要的支付宝配置
if(!isset($conf['alipay_appid']) || empty($conf['alipay_appid'])) {
    die('支付宝APPID未配置！');
}
if(!isset($conf['alipay_privatekey']) || empty($conf['alipay_privatekey'])) {
    die('支付宝私钥未配置！');
}
if(!isset($conf['alipay_publickey']) || empty($conf['alipay_publickey'])) {
    die('支付宝公钥未配置！');
}
echo '<!-- 支付宝配置检查通过 -->';

// 检查支付宝SDK文件是否存在
$precreateFile = __DIR__ . "/alipay/model/builder/AlipayTradePrecreateContentBuilder.php";
$serviceFile = __DIR__ . "/alipay/AlipayTradeService.php";
$configFile = __DIR__ . "/alipay/config.php";

echo '<!-- 文件路径检查 -->';
echo '<!-- precreateFile: ' . $precreateFile . ' -->';
echo '<!-- serviceFile: ' . $serviceFile . ' -->';
echo '<!-- configFile: ' . $configFile . ' -->';

// 检查支付宝SDK目录结构
echo '<!-- 文件存在性检查 -->';
echo '<!-- precreateFile存在: ' . (file_exists($precreateFile) ? '是' : '否') . ' -->';
echo '<!-- serviceFile存在: ' . (file_exists($serviceFile) ? '是' : '否') . ' -->';
echo '<!-- configFile存在: ' . (file_exists($configFile) ? '是' : '否') . ' -->';

if(!file_exists($precreateFile)) {
    die('支付宝SDK文件缺失：' . $precreateFile);
}

if(!file_exists($serviceFile)) {
    die('支付宝SDK文件缺失：' . $serviceFile);
}

// 引入支付宝SDK文件和配置
try {
    // 检查并引入文件
    if(file_exists($precreateFile)) {
        require_once($precreateFile);
        echo '<!-- AlipayTradePrecreateContentBuilder.php加载成功 -->';
    } else {
        die('支付宝SDK文件不存在：' . $precreateFile);
    }
    if(file_exists($serviceFile)) {
        require_once($serviceFile);
        echo '<!-- AlipayTradeService.php加载成功 -->';
    } else {
        die('支付宝SDK文件不存在：' . $serviceFile);
    }
    if(file_exists($configFile)) {
        // 先加载配置模板
        $config = array(
            'sign_type' => 'RSA2',
            'alipay_public_key' => $conf['alipay_publickey'],
            'merchant_private_key' => $conf['alipay_privatekey'],
            'charset' => 'UTF-8',
            'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
            'app_id' => $conf['alipay_appid'],
            'notify_url' => $siteurl . 'alipay_notify.php',
            'return_url' => $siteurl . 'alipay_return.php',
        );
        echo '<!-- 支付宝配置生成成功 -->';
    } else {
        die('支付宝配置文件不存在：' . $configFile);
    }
} catch(Exception $e) {
    die('加载支付宝相关文件失败：' . $e->getMessage());
}

// 检查$config变量是否定义
if(!isset($config) || !is_array($config)) {
    die('支付宝配置加载失败！');
}

// 验证配置的完整性
$requiredConfig = array('sign_type', 'alipay_public_key', 'merchant_private_key', 'charset', 'gatewayUrl', 'app_id');
foreach($requiredConfig as $key) {
    if(!isset($config[$key]) || empty($config[$key])) {
        die('支付宝配置不完整，缺少必要的配置项：' . $key);
    }
}
echo '<!-- 支付宝配置验证通过 -->';

// 创建请求builder，设置请求参数
try {
    $qrPayRequestBuilder = new AlipayTradePrecreateContentBuilder();
    $qrPayRequestBuilder->setOutTradeNo($trade_no);
    $qrPayRequestBuilder->setTotalAmount($row['money']);
    $qrPayRequestBuilder->setSubject($ordername);
    // 设置异步通知URL
    $qrPayRequestBuilder->setNotifyUrl($siteurl . 'alipay_notify.php');
    echo '<!-- 请求参数设置成功 -->';
} catch (Exception $e) {
    die('创建请求参数失败：' . $e->getMessage());
}

// 调用qrPay方法获取当面付应答
try {
    $qrPay = new AlipayTradeService($config);
    echo '<!-- AlipayTradeService实例化成功 -->';
    
    // 记录请求参数以便调试
    $requestParams = array(
        'trade_no' => $trade_no,
        'money' => $row['money'],
        'ordername' => $ordername
    );
    $log_file = __DIR__ . '/alipay_error.log';
    error_log('支付宝请求参数: ' . json_encode($requestParams), 3, $log_file);
    
    $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
    error_log('支付宝qrPay结果: ' . json_encode($qrPayResult), 3, $log_file);
    echo '<!-- 调用qrPay方法成功 -->';
    echo '<!-- qrPayResult: ' . json_encode($qrPayResult) . ' -->';
} catch(Exception $e) {
    error_log('支付宝接口请求失败: ' . $e->getMessage(), 3, $log_file);
    die('支付宝接口请求失败！' . $e->getMessage());
}

// 根据状态值进行业务处理
try {
    $status = $qrPayResult->getTradeStatus();
    $response = $qrPayResult->getResponse();
    echo '<!-- 获取响应结果成功 -->';
    
    error_log('支付宝响应状态: ' . $status, 3, dirname(__FILE__) . '/alipay_error.log');
    error_log('支付宝响应数据: ' . print_r($response, true), 3, dirname(__FILE__) . '/alipay_error.log');
    
    if($status == 'SUCCESS') {
        $code_url = $response->qr_code;
        echo '<!-- 支付二维码生成成功 -->';
    } elseif($status == 'FAILED') {
        die('支付宝创建订单二维码失败！[' . $response->sub_code . ']' . $response->sub_msg);
    } else {
        error_log('支付宝异常状态: ' . $status . ' 响应: ' . print_r($response, true), 3, dirname(__FILE__) . '/alipay_error.log');
        die('系统异常，状态未知！状态值：' . htmlspecialchars($status));
    }
} catch (Exception $e) {
    error_log('处理响应结果失败: ' . $e->getMessage(), 3, dirname(__FILE__) . '/alipay_error.log');
    die('处理支付宝响应失败：' . $e->getMessage());
}

// 验证二维码URL是否生成成功
if(empty($code_url)) {
    die('二维码生成失败，请稍后重试！');
}
echo '<!-- 二维码URL验证通过 -->';
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="zh-cn">
<meta name="renderer" content="webkit">
<title>支付宝扫码支付 - <?php echo $conf['sitename']?></title>
<link href="assets/css/alipay_pay.css?v=2" rel="stylesheet" media="screen">
</head>
<body>
<div class="body">
<h1 class="mod-title">
<span class="ico-wechat"></span><span class="text">支付宝扫码支付</span>
</h1>
<div class="mod-ct">
<div class="order">
</div>
<div class="amount">￥<?php echo $row['money']?></div>
<div class="qr-image" id="qrcode">
</div>
<div class="open_app" style="display: none;">
    <a onclick="openAlipay()" class="btn-open-app">打开支付宝APP继续付款</a><br/><br/><br/>
	<a onclick="checkresult()" class="btn-check">我已付款，返回查看订单</a>
</div>
<div class="detail" id="orderDetail">
<dl class="detail-ct" style="display: none;">
<dt>购买物品</dt>
<dd id="productName"><?php echo $row['name']?></dd>
<dt>商户订单号</dt>
<dd id="billId"><?php echo $row['trade_no']?></dd>
<dt>创建时间</dt>
<dd id="createTime"><?php echo $row['addtime']?></dd>
</dl>
<a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
</div>
<div class="tip">
<span class="dec dec-left"></span>
<span class="dec dec-right"></span>
<div class="ico-scan"></div>
<div class="tip-text">
<p>请使用支付宝扫一扫</p>
<p>扫描二维码完成支付</p>
</div>
</div>
<div class="tip-text">
</div>
</div>
<script src="//lib.baomitu.com/jquery/1.12.4/jquery.min.js"></script>
<script src="//lib.baomitu.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script src="//lib.baomitu.com/layer/3.1.1/layer.min.js"></script>
<script>
	var code_url = '<?php echo $code_url?>';
	var url_scheme = 'alipays://platformapi/startapp?appId=20000067&url=' + encodeURIComponent(code_url);
    $('#qrcode').qrcode({
        text: code_url,
        width: 230,
        height: 230,
        foreground: "#000000",
        background: "#ffffff",
        typeNumber: -1
    });
    // 订单详情
    $('#orderDetail .arrow').click(function (event) {
        if ($('#orderDetail').hasClass('detail-open')) {
            $('#orderDetail .detail-ct').slideUp(500, function () {
                $('#orderDetail').removeClass('detail-open');
            });
        } else {
            $('#orderDetail .detail-ct').slideDown(500, function () {
                $('#orderDetail').addClass('detail-open');
            });
        }
    });
    // 检查是否支付完成
    function loadmsg() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "getshop.php",
            timeout: 10000, //ajax请求超时时间10s
            data: {type: "alipay", trade_no: "<?php echo $row['trade_no']?>"}, //post数据
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == 1) {
					layer.msg('支付成功，正在跳转中...', {icon: 16,shade: 0.1,time: 15000});
					setTimeout(window.location.href=data.backurl, 1000);
                }else{
                    setTimeout("loadmsg()", 3000);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == "timeout") {
                    setTimeout("loadmsg()", 1000);
                } else { //异常
                    setTimeout("loadmsg()", 4000);
                }
            }
        });
    }
	function checkresult() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "getshop.php",
            timeout: 10000, //ajax请求超时时间10s
            data: {type: "alipay", trade_no: "<?php echo $row['trade_no']?>"},
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == 1) {
                    layer.msg('支付成功，正在跳转中...', {icon: 16,shade: 0.1,time: 15000});
					setTimeout(window.location.href=data.backurl, 1000);
                }else{
					layer.msg('您还未完成付款，请继续付款', {shade: 0,time: 1500});
				}
            }
        });
    }
	var isMobile = function (){
		var ua = navigator.userAgent;
		var ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
		isIphone =!ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
		isAndroid = ua.match(/(Android)\s+([\d.]+)/);
		return isIphone || isAndroid;
	}
	function openAlipay(){
		window.location.href = url_scheme;
		layer.msg('正在打开支付宝...', {shade: 0,time: 1000});
	}
	window.onload = function(){
		if(isMobile()){
			$('.open_app').show();
			window.location.href = url_scheme;
		}
		setTimeout("loadmsg()", 2000);
	}
</script>
</body>
</html>