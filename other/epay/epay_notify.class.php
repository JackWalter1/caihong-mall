<?php
/* *
 * 类名：EpayNotify
 * 功能：彩虹易支付通知处理类
 * 详细：处理易支付接口通知返回
 */

require_once(SYSTEM_ROOT."epay/epay_core.function.php");
require_once(SYSTEM_ROOT."epay/epay_md5.function.php");

class AlipayNotify {

	var $alipay_config;

	var $http_verify_url;

	function __construct($alipay_config){
		$this->alipay_config = $alipay_config;
		$this->http_verify_url = $this->alipay_config['apiurl'].'api.php?';
	}
    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	function verifyNotify(){
		if(empty($_POST)) {//判断POST来的数组是否为空
			return false;
		}
		else {
			//生成签名结果
			$isSign = $this->getSignVeryfy($_POST, $_POST["sign"]);
			if(!$isSign) return false;
			
			//获取支付平台远程服务器ATK结果（验证是否是支付平台发来的消息）
			//必须用支付服务器二次确认，防止伪造回调
			//注意：必须用商户订单号 out_trade_no 查询，且需携带 key 参数
			$responseTxt = $this->getResponse($_POST["out_trade_no"]);
			
			//验证结果不匹配，可能是伪造通知
			if (preg_match("/true$/i",$responseTxt)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
    /**
     * 针对return_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	function verifyReturn(){
		if(empty($_GET)) {//判断GET来的数组是否为空
			return false;
		}
		else {
			//生成签名结果
			$isSign = $this->getSignVeryfy($_GET, $_GET["sign"]);
			if(!$isSign) return false;
			
			//获取支付平台远程服务器验证结果（验证是否是支付平台发来的消息）
			//同步跳转也需要二次确认，防止伪造
			$responseTxt = $this->getResponse($_GET["out_trade_no"]);
			
			//验证
			if (preg_match("/true$/i",$responseTxt)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
	function getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);
		
		//对待签名参数数组排序
		$para_sort = argSort($para_filter);
		
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);
		
		$isSgin = false;
		$isSgin = md5Verify($prestr, $sign, $this->alipay_config['key']);
		
		return $isSgin;
	}

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
	function getResponse($out_trade_no) {
		$partner = trim($this->alipay_config['partner']);
		$key = trim($this->alipay_config['key']);
		// 彩虹易支付订单查询接口：api.php?act=order&pid=商户ID&key=商户密钥&out_trade_no=商户订单号
		$veryfy_url = $this->http_verify_url."act=order&pid=" . $partner . "&key=" . $key . "&out_trade_no=" . $out_trade_no;
		$responseTxt = get_curl($veryfy_url);
		$arr = json_decode($responseTxt,true);
		if(isset($arr['status']) && $arr['status']==1){
			return 'true';
		}else{
			return 'false';
		}
	}
}
?>
