<?php
/* *
 * 微信支付异步通知页面
 * 安全策略：签名验证 + 服务器二次查询 + 严格金额校验 + 乐观锁防并发
 */

// ---------- 日志记录 ----------
$log_dir = __DIR__ . '/log';
if(!file_exists($log_dir)) {
    @mkdir($log_dir, 0755, true);
}
$log_file = $log_dir . '/wxpay_notify.log';
$log = @fopen($log_file, 'a');
if($log) {
	fwrite($log, "\n\n" . str_repeat('=', 50) . "\n");
	fwrite($log, '通知时间: ' . date('Y-m-d H:i:s') . "\n");
	fwrite($log, '请求方法: ' . $_SERVER['REQUEST_METHOD'] . "\n");
	fwrite($log, '客户端IP: ' . $_SERVER['REMOTE_ADDR'] . "\n");
	fwrite($log, '原始POST数据: ' . file_get_contents("php://input") . "\n");
}

require_once("./inc.php");

// inc.php加载后重新打开日志（确保error_reporting不会干扰）
if(!$log) {
	$log = @fopen($log_file, 'a');
}

// 安全写日志的辅助函数
function _wxpay_log($msg) {
	global $log;
	if($log) fwrite($log, $msg);
}

require_once SYSTEM_ROOT."wxpay/WxPay.Api.php";
require_once SYSTEM_ROOT."wxpay/WxPay.Notify.php";

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		if(is_array($result) && array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}

	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		global $DB,$date,$conf;

		if(!is_array($data) || !array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			_wxpay_log('错误: 输入参数不正确' . "\n");
			return false;
		}

		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			_wxpay_log('错误: 微信服务器二次查询订单失败 transaction_id=' . $data["transaction_id"] . "\n");
			return false;
		}

		_wxpay_log('微信服务器二次查询成功' . "\n");

		if($data['return_code']=='SUCCESS'){
			if($data['result_code']=='SUCCESS'){
				$out_trade_no = daddslashes($data['out_trade_no']);
				// 微信支付金额单位为分，需要转换为元
				$total_fee = isset($data['total_fee']) ? floatval($data['total_fee'])/100 : 0;
				$srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);

				_wxpay_log('订单号: ' . $out_trade_no . "\n");
				_wxpay_log('微信交易号: ' . $data['transaction_id'] . "\n");
				_wxpay_log('实付金额(元): ' . $total_fee . "\n");
				_wxpay_log('本地订单查询结果: ' . ($srow ? '找到' : '未找到') . "\n");
				if($srow) {
					_wxpay_log('本地订单状态: ' . $srow['status'] . ' 订单金额: ' . $srow['money'] . "\n");
				}

				// 订单不存在
				if(!$srow){
					$msg = '订单不存在';
					_wxpay_log('错误: 订单不存在' . "\n");
					return false;
				}

				// 订单已处理（重复通知）
				if($srow['status'] != 0){
					$msg = '该订单已经处理';
					_wxpay_log('提示: 订单已处理过，status=' . $srow['status'] . "\n");
					return true;
				}

				// 防止0元订单
				if(floatval($srow['money']) <= 0){
					$msg = '订单金额为0';
					_wxpay_log('错误: 订单金额为0，拒绝处理' . "\n");
					return false;
				}

				// 金额校验（使用宽松比较 ==，兼容浮点精度）
				if($total_fee == floatval($srow['money'])) {
					// 乐观锁：仅当 status=0 时才更新，防止并发重复发货
					$updated = $DB->exec("UPDATE `pre_pay` SET `status` ='1' WHERE `trade_no`=:trade_no AND `status`='0'", [':trade_no' => $out_trade_no]);
					if($updated){
						$DB->exec("UPDATE `pre_pay` SET `endtime` =:date,`api_trade_no` =:transaction_id WHERE `trade_no`=:trade_no", [':date' => $date, ':transaction_id' => $data['transaction_id'], ':trade_no' => $out_trade_no]);
						// 重新读取订单数据，确保获得最新状态
						$srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
						_wxpay_log('订单状态更新成功，开始发货 processOrder()' . "\n");
						processOrder($srow);
						_wxpay_log('订单处理完成' . "\n");
						$msg='OK';
						return true;
					} else {
						$msg='订单状态更新失败';
						_wxpay_log('错误: 乐观锁更新失败，订单可能已被其他进程处理' . "\n");
						return true;
					}
				} else {
					$msg='金额验证失败';
					_wxpay_log('错误: 金额不匹配 订单金额=' . $srow['money'] . ' 实付金额=' . $total_fee . "\n");
					return false;
				}
			}else{
				$msg='['.$data['err_code'].']'.$data['err_code_des'];
				_wxpay_log('错误: 微信result_code失败 ' . $msg . "\n");
				return false;
			}
		}else{
			$msg='['.$data['return_code'].']'.$data['return_msg'];
			_wxpay_log('错误: 微信return_code失败 ' . $msg . "\n");
			return false;
		}
		return true;
	}
}

_wxpay_log('开始处理微信支付回调' . "\n");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
_wxpay_log('回调处理结束' . "\n");
if($log) fclose($log);
