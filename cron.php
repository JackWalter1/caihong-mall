<?php
/* 
QQ群915043052
个人博客blog.6v6.ren
*/
/*支付接口订单监控文件
说明：用于请求支付接口订单列表，同步未通知到本站的订单，防止漏单。
监控频率建议5分钟一次
监控地址：/cron.php?key=监控密钥
注意：千万不要监控太快或使用多节点监控！！！否则会被支付接口自动屏蔽IP地址
*/

if(preg_match('/Baiduspider/', $_SERVER['HTTP_USER_AGENT']))exit;
if(!isset($_SERVER['HTTP_USER_AGENT']))$_SERVER['HTTP_USER_AGENT']='Mozilla/5.0 (Windows NT 10.0) Safari/537.36';
include("./includes/common.php");

if (function_exists("set_time_limit"))
{
	@set_time_limit(0);
}
if (function_exists("ignore_user_abort"))
{
	@ignore_user_abort(true);
}

@header('Content-Type: text/html; charset=UTF-8');

if(empty($conf['cronkey']))exit("请先设置好监控密钥");
if($conf['cronkey']!=$_GET['key'])exit("监控密钥不正确");

if($_GET['do']=='pricejk'){
		// 获取上次更新时间
		$cron_lasttime = $DB->getColumn("SELECT v FROM pre_config WHERE k='pricejk_lasttime' LIMIT 1");
		// 获取设置的时间间隔
		$pricejk_time = $conf['pricejk_time'] ? $conf['pricejk_time'] : 50;
		// 确保时间间隔不会小于1秒
		$pricejk_time = max(1, $pricejk_time);
		
		// 检查是否需要更新
		if(!isset($_GET['test'])) {
			// 如果上次更新时间不存在或时间差大于设置的时间间隔，则执行更新
			if(empty($cron_lasttime) || time() - strtotime($cron_lasttime) >= $pricejk_time) {
				// 保存当前时间作为上次更新时间
				saveSetting('pricejk_lasttime', $date);
			} else {
				// 时间间隔未到，退出
				exit('上次更新时间: ' . $cron_lasttime);
			}
		} else {
			// 测试模式下直接保存当前时间
			saveSetting('pricejk_lasttime', $date);
		}
	$success = 0;
	$is_need = 0;
	// 强制允许 yile 类型的对接站点，确保亿乐社区能被监控
	$allowType = array_merge(explode(',',$CACHE->read('pricejk_type1')), explode(',',$CACHE->read('pricejk_type2')));
	$allowType[] = 'yile';
	$allowType[] = 'third_yile';
	// 去重
	$allowType = array_unique($allowType);
	
	if(count($allowType) == 0)exit('没有支持价格监控的对接网站类型');
	$rs=$DB->query("SELECT * FROM pre_shequ ORDER BY id ASC");
	while($res = $rs->fetch())
	{
		// 调试信息：输出对接站点类型
		if(isset($_GET['test'])) echo '对接站点 '.$res['name'].' 类型: '.$res['type'].'，允许的类型: '.implode(',', $allowType).'<br/>';
		
		if(!in_array($res['type'], $allowType)) continue;
		// 调试信息：输出查询条件
		if(isset($_GET['test'])) echo '查询商品数量：SELECT count(*) FROM pre_tools WHERE is_curl=2 AND shequ='.$res['id'].' AND cid IN ('.$conf['pricejk_cid'].') AND active=1<br/>';
		
		$tcount = $DB->getColumn("SELECT count(*) FROM pre_tools WHERE is_curl=2 AND shequ=:shequ AND cid IN (:cid) AND active=1", [':shequ' => $res['id'], ':cid' => $conf['pricejk_cid']]);
		if($tcount>0 && $res['username'] && $res['password'] && $res['type']){
			$is_need++;
			// 调试信息：输出调用 third_call 前的信息
			if(isset($_GET['test'])) echo '调用 third_call 方法，对接站点ID: '.$res['id'].'，类型: '.$res['type'].'<br/>';
			
			$results = third_call($res['type'], $res, 'pricejk', [$res['id'], &$success]);
			if($results === false) continue;
			if($results===true){
				saveSetting('pricejk_status','ok');
				if(isset($_GET['test'])) echo '对接站点ID'.$res['id'].'：更新成功<br/>';
			}else{
				saveSetting('pricejk_status',$results);
				echo '对接站点ID'.$res['id'].'：'.$results.'<br/>';
			}
		}
	}
	if($is_need==0){
		exit('没有需要监控价格的商品');
	}else{
		exit('成功更新'.$success.'个商品的价格');
	}
}
elseif($_GET['do']=='daily'){ //每天执行一次
	//每日数据库维护
	$maintain = getSetting('maintain', true);
	if($maintain != date("Ymd")){
		saveSetting('maintain',date("Ymd"));
		$sq1 = $DB->exec("DELETE FROM `pre_pay` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-12 hours"))."' AND (status=0 OR money='0')");
		$sq2 = $DB->exec("DELETE FROM `pre_cart` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
		$sq3 = $DB->exec("DELETE FROM `pre_cart` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-24 hours"))."' and status<2");
		$sq4 = $DB->exec("DELETE FROM `pre_giftlog` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-7 days"))."'");
		$sq5 = $DB->exec("DELETE FROM `pre_invitelog` WHERE date<'".date("Y-m-d H:i:s",strtotime("-7 days"))."'");
		$sq6 = $DB->exec("DELETE FROM `pre_cutlog` WHERE date<'".date("Y-m-d H:i:s",strtotime("-7 days"))."'");
		$sq7 = $DB->exec("DELETE FROM `pre_grouplog` WHERE date<'".date("Y-m-d H:i:s",strtotime("-7 days"))."' and status=1");
		$DB->exec("OPTIMIZE TABLE `pre_pay`");
		$DB->exec("OPTIMIZE TABLE `pre_cart`");
		$DB->exec("OPTIMIZE TABLE `pre_giftlog`");
		$DB->exec("OPTIMIZE TABLE `pre_invitelog`");
		$DB->exec("OPTIMIZE TABLE `pre_cutlog`");
		$DB->exec("OPTIMIZE TABLE `pre_grouplog`");
		$count = $sq1+$sq2+$sq3+$sq4+$sq5+$sq6+$sq7;
		exit('日常维护任务已成功执行，本次共清理'.$count.'条数据<br/>');
	}else{
		echo '日常维护任务今天已执行过<br/>';
	}
	//分站排行榜奖励发放
	if($conf['rank_reward']){
		$limit = intval($conf['rank_reward']);
		$cron_lasttime = getSetting('cron_rank_time', true);
		if($cron_lasttime != date("Ymd")){
			$re = $DB->query("SELECT a.zid,SUM(money) AS money FROM pre_orders AS a WHERE (TO_DAYS(NOW()) - TO_DAYS(addtime) = 1) AND zid>1 AND status!=4 GROUP BY zid HAVING money>0 ORDER BY money DESC LIMIT {$limit}");
			$allmoney = 0;
			$count = 0;
			while ($site = $re->fetch()) {
				$reward = round($site['money'] * $conf['rank_percentage'] / 100, 2);
				if($reward>0){
					$allmoney += $reward;
					$count++;
					changeUserMoney($site['zid'], $reward, true, '奖励', '网站昨日销量排行前'.$limit.'名奖励'.$reward.'元');
				}
			}
			saveSetting('cron_rank_time' , date("Ymd"));
			saveSetting('cron_rank_money' , $allmoney);
			echo '分站排行榜奖励发放完成，发放站点数量：'.$count.'&nbsp;总金额：'.$allmoney.'元<br/>';
		}else{
			echo '今日分站排行榜奖励发放任务已完成<br/>';
		}
	}
	//提成延迟到账
	if($conf['tixian_limit']==1 && $conf['tixian_days']>0){
		$cron_lasttime = getSetting('cron_rmb_time', true);
		if($cron_lasttime != date("Ymd")){
			$days = intval($conf['tixian_days']);
			$maxdays = $days+5;
			$rs=$DB->query("SELECT A.id,A.zid,A.point,A.status,B.rmb,B.rmbtc FROM pre_points A LEFT JOIN pre_site B ON A.zid=B.zid WHERE A.action='提成' AND A.status=0 AND TO_DAYS(NOW())-TO_DAYS(A.addtime)>={$days} AND TO_DAYS(NOW())-TO_DAYS(A.addtime)<={$maxdays}");
			$c=0;
			while($row = $rs->fetch()){
				if($row['rmb']-$row['rmbtc']>$row['point']){
					$DB->exec("UPDATE pre_site SET rmbtc=rmbtc+:point WHERE zid=:zid", [':point' => $row['point'], ':zid' => $row['zid']]);
				}else{
					$DB->exec("UPDATE pre_site SET rmbtc=:rmb WHERE zid=:zid", [':rmb' => $row['rmb'], ':zid' => $row['zid']]);
				}
				$DB->exec("UPDATE pre_points SET status=1 WHERE id=:id", [':id' => $row['id']]);
				$c++;
			}
			saveSetting('cron_rmb_time' , date("Ymd"));
			echo '今日提成处理成功('.$c.')<br/>';
		}else{
			echo '今日提成处理已完成<br/>';
		}
	}
	// 自动删除过期聊天记录
	if($conf['chat_auto_delete']==1 && $conf['chat_delete_days']>0){
		$delete_days = intval($conf['chat_delete_days']);
		$delete_time = date('Y-m-d H:i:s', strtotime("-{$delete_days} days"));
		$DB->exec("DELETE FROM shua_chat_message WHERE create_time < ?", [$delete_time]);
		$DB->exec("DELETE FROM shua_chat_session WHERE last_msg_time < ? AND status = 0", [$delete_time]);
	}
	// 每日授权状态检查
	global $auth_config;
	if (!empty($auth_config['code'])) {
		$cache_file = SYSTEM_ROOT . 'cache/auth_status.json';
		@unlink($cache_file);
		echo "授权缓存已清除，下次访问时将重新验证\n";
	}
}
elseif($_GET['do']=='auth_check'){ //授权状态检查
	global $auth_config;
	if (empty($auth_config['code'])) { exit('未配置授权码'); }
	$cache_file = SYSTEM_ROOT . 'cache/auth_status.json';
	@unlink($cache_file);
	include_once SYSTEM_ROOT . 'auth.php';
	if (defined('SITE_AUTH_VERIFIED') && SITE_AUTH_VERIFIED) {
		echo "授权状态正常\n";
	} else {
		echo "授权状态异常\n";
	}
}
elseif($_GET['do']=='updatestatus'){ //订单状态监控
	if($conf['updatestatus']==0)exit('当前站点未开启订单状态监控');
	$updatestatus_interval = $conf['updatestatus_interval']?$conf['updatestatus_interval']:6;
	$times = intval($updatestatus_interval) * 60; //检测间隔时间（单位：分钟）
	$limit = 10; //每次更新订单数量
	$current_time = time();
	$rs=$DB->query("SELECT * FROM pre_orders WHERE status=2 AND djzt=1 AND (uptime IS NULL OR uptime<:uptime) AND addtime<:addtime ORDER BY id DESC LIMIT :limit", [':uptime' => $current_time - $times, ':addtime' => date("Y-m-d H:i:s", $current_time - $times), ':limit' => $limit]);
	$checkcount=0;
	$successcount=0;
	while($row = $rs->fetch()){
		$tool=$DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid LIMIT 1", [':tid' => $row['tid']]);
		if($tool['is_curl']==2){
			$shequ=$DB->getRow("SELECT * FROM pre_shequ WHERE id=:id LIMIT 1", [':id' => $tool['shequ']]);
			if($shequ['result']!=2 || empty($shequ['username']) || empty($shequ['password'])){
				$DB->exec("UPDATE `pre_orders` SET `uptime`=:uptime WHERE id=:id", [':uptime' => time(), ':id' => $row['id']]);
				continue;
			}
			$list = third_call($shequ['type'], $shequ, 'query_order', [$row['djorder'], $tool['goods_id'], [$row['input'], $row['input2'], $row['input3'], $row['input4'], $row['input5']]]);
			$checkcount++;
			// 将返回的所有字段序列化后统一做包含匹配，兼容各对接平台返回的不同状态文案
			// （如卡商网络"充值成功"、易齐达"已发货，物流派送中"、拾多/智客/云宝"已完成"等）
			$list_text = is_array($list) ? json_encode($list, JSON_UNESCAPED_UNICODE) : '';
			$is_success = $list_text !== '' && (
				strpos($list_text, '已完成') !== false ||
				strpos($list_text, '订单已完成') !== false ||
				strpos($list_text, '已发货') !== false ||
				strpos($list_text, '交易成功') !== false ||
				strpos($list_text, '已支付') !== false ||
				strpos($list_text, '充值成功') !== false
			);
			$is_fail = $list_text !== '' && (
				strpos($list_text, '异常') !== false ||
				strpos($list_text, '退单') !== false ||
				strpos($list_text, '退款') !== false ||
				strpos($list_text, '失败') !== false ||
				strpos($list_text, '已取消') !== false
			);
			if ($is_success) {
				$DB->exec("UPDATE `pre_orders` SET `status`=1,`uptime`=:uptime WHERE id=:id", [':uptime' => time(), ':id' => $row['id']]);
				$successcount++;
			} elseif ($is_fail) {
				$DB->exec("UPDATE `pre_orders` SET `status`=3,`uptime`=:uptime WHERE id=:id", [':uptime' => time(), ':id' => $row['id']]);
			} else {
				$DB->exec("UPDATE `pre_orders` SET `uptime`=:uptime WHERE id=:id", [':uptime' => time(), ':id' => $row['id']]);
			}
		}
	}
	echo '成功检测'.$checkcount.'个订单，更新'.$successcount.'个订单状态';
	saveSetting('updatestatus_lasttime',$date);
}elseif($conf['epay_pid'] && $conf['epay_key']){
	$id = isset($_GET['id'])?intval($_GET['id']):1;
	$cron_lasttime = getSetting('cron_lasttime', true);
	if(time()-strtotime($cron_lasttime)<30)exit('ok');
	$trade_no = date("YmdHis",strtotime($cron_lasttime)).'000';
	$cron_lasttime_val = $trade_no;
	$limit = $DB->getColumn("SELECT count(*) FROM pre_pay WHERE trade_no>:trade_no", [':trade_no' => $cron_lasttime_val]);
	if($limit<1)exit('ok');
	if($limit>50)$limit=50;
	saveSetting('cron_lasttime',$date);
	$payapi=pay_api(true, $id);
	if(empty($payapi))exit('未配置易支付信息');
	$data = get_curl($payapi.'api.php?act=orders&limit='.$limit.'&pid='.$conf['epay_pid'].'&key='.$conf['epay_key']);
	$arr = json_decode($data, true);
	if($arr['code']==1){
		foreach($arr['data'] as $row){
			if($row['status']==1){
				$api_trade_no = $row['trade_no'];
				$out_trade_no = $row['out_trade_no'];
				$srow=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
				if($srow && $srow['status']==0){
					// 乐观锁：仅当status=0才更新，防并发
					$updated = $DB->exec("UPDATE `pre_pay` SET `status`='1',`endtime`=:date,`api_trade_no`=:api_trade_no WHERE `trade_no`=:trade_no AND `status`='0'", [':date' => $date, ':api_trade_no' => $api_trade_no, ':trade_no' => $out_trade_no]);
					if($updated){
						// 重读订单获取最新状态
						$srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:trade_no LIMIT 1", [':trade_no' => $out_trade_no]);
						if($srow) processOrder($srow);
						echo '已成功补单:'.$out_trade_no.'<br/>';
					}
				}
			}
		}
		exit('ok');
	}else{
		exit($arr['msg']);
	}
}elseif(!empty($conf['codepay_key'])){ //USDT-TRC20支付状态同步
	// USDT-TRC20支付状态同步
	require_once('./other/usdt-trc20/inc.php');
	checkResult(true);
	exit('USDT-TRC20支付状态同步完成');
}else{
	exit('未配置支付信息');
}