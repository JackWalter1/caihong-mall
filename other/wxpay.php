<?php
// 最终修复版微信支付页面

// 首先设置错误报告，确保能看到所有错误
ini_set('display_errors', 1);
ini_set('html_errors', 1);
error_reporting(E_ALL);

// 设置字符集
header('Content-Type: text/html; charset=UTF-8');

// inc.php会定义这些常量，所以不需要重复定义

// 加载inc.php，它包含了所有必要的配置和函数
$inc_path = __DIR__ . '/inc.php';
if (file_exists($inc_path)) {
    require $inc_path;
} else {
    die('inc.php not found at ' . $inc_path);
}

// 检查必要参数
$trade_no = isset($_GET['trade_no']) ? daddslashes($_GET['trade_no']) : die('No trade_no!');

// 查询订单 - 使用参数化查询修复SQL注入漏洞
$row = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $trade_no]);
if (!$row) {
    die('Order not found!');
}

// 加载微信支付SDK
require_once SYSTEM_ROOT . 'wxpay/WxPay.Exception.php';
require_once SYSTEM_ROOT . 'wxpay/WxPay.Config.php';
require_once SYSTEM_ROOT . 'wxpay/WxPay.Data.php';
require_once SYSTEM_ROOT . 'wxpay/WxPay.Api.php';

// 订单名称替换函数
function ordername_replace($template, $name, $trade_no) {
    $replacements = array(
        '{name}' => $name,
        '{trade_no}' => $trade_no,
        '{ordername}' => $name,
        '{orderid}' => $trade_no
    );
    return strtr($template, $replacements);
}

// 设置站点URL
if (!isset($siteurl)) {
    $scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    $sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
    $siteurl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $sitepath . '/';
}

// 设置订单参数
$ordername = !empty($conf['ordername']) ? ordername_replace($conf['ordername'], $row['name'], $trade_no) : $row['name'];

// 创建统一订单
$input = new WxPayUnifiedOrder();
$input->SetBody($ordername);
$input->SetOut_trade_no($trade_no);
$input->SetTotal_fee($row['money']*100);
$input->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetNotify_url($siteurl.'wxpay_notify.php');
$input->SetTrade_type("NATIVE");
$input->SetProduct_id("01001");

// 调用统一下单API
$result = WxPayApi::unifiedOrder($input);

// 处理返回结果
$code_url = '';
$error_msg = '';

if ($result["return_code"] == 'SUCCESS') {
    if ($result["result_code"] == 'SUCCESS') {
        $code_url = $result['code_url'];
    } else {
        $error_msg = isset($result["err_code_des"]) ? $result["err_code_des"] : 'Unknown error';
    }
} else {
    $error_msg = isset($result["return_msg"]) ? $result["return_msg"] : 'Unknown error';
}

// 生成HTML输出
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>微信安全支付 - <?php echo $conf['sitename']?></title>
<link href="assets/css/wechat_pay.css" rel="stylesheet" media="screen">
</head>
<body>
<div class="body">
<h1 class="mod-title">
<span class="ico-wechat"></span><span class="text">微信支付</span>
</h1>
<div class="mod-ct">
<div class="order">
</div>
<div class="amount">￥<?php echo $row['money']?></div>

<?php if ($code_url): ?>
<div class="qr-image" id="qrcode"></div>
<?php else: ?>
<div style="color: red; margin: 20px 0; padding: 10px; background: #ffebee;">
    <p>下单失败：<?php echo $error_msg; ?></p>
    <p>请返回重试或联系客服</p>
</div>
<?php endif; ?>

<div class="detail" id="orderDetail">
<dl class="detail-ct" style="display: none;">
<dt>商家</dt>
<dd id="storeName"><?php echo $conf['sitename']?></dd>
<dt>购买物品</dt>
<dd id="productName"><?php echo $row['name']?></dd>
<dt>商户订单号</dt>
<dd id="billId"><?php echo $row['trade_no']?></dd>
<dt>创建时间</dt>
<dd id="createTime"><?php echo $row['addtime']?></dd>
</dl>
<a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
</div>

<?php if ($code_url): ?>
<div class="tip">
<span class="dec dec-left"></span>
<span class="dec dec-right"></span>
<div class="ico-scan"></div>
<div class="tip-text">
<p>请使用微信扫一扫</p>
<p>扫描二维码完成支付</p>
</div>
</div>
<?php endif; ?>

<div class="tip-text">
</div>
</div>
<div class="foot">
<div class="inner">
<?php if ($code_url): ?>
<p>手机用户可保存上方二维码到手机中</p>
<p>在微信扫一扫中选择“相册”即可</p>
<?php else: ?>
<p>如有疑问，请联系客服</p>
<?php endif; ?>
</div>
</div>
</div>

<?php if ($code_url): ?>
<script src="//lib.baomitu.com/jquery/1.12.4/jquery.min.js"></script>
<script src="//lib.baomitu.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script>
$(document).ready(function() {
    $('#qrcode').qrcode({
        text: '<?php echo $code_url?>',
        width: 230,
        height: 230,
        foreground: "#000000",
        background: "#ffffff",
        typeNumber: -1
    });
    
    // 订单详情切换
    $('#orderDetail .arrow').click(function() {
        if ($('#orderDetail').hasClass('detail-open')) {
            $('#orderDetail .detail-ct').slideUp(500, function() {
                $('#orderDetail').removeClass('detail-open');
            });
        } else {
            $('#orderDetail .detail-ct').slideDown(500, function() {
                $('#orderDetail').addClass('detail-open');
            });
        }
    });
    
    // 检查支付状态
    function checkPayment() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "getshop.php",
            data: {type: "wxpay", trade_no: "<?php echo $trade_no?>"},
            timeout: 10000,
            success: function(data) {
                if (data.code == 1) {
                    window.location.href = data.backurl;
                } else {
                    setTimeout(checkPayment, 3000);
                }
            },
            error: function() {
                setTimeout(checkPayment, 3000);
            }
        });
    }
    
    // 开始检查支付状态
    setTimeout(checkPayment, 3000);
});
</script>
<?php endif; ?>
</body>
</html>