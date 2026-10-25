<?php
/* 
QQ群915043052
个人博客6v6.ren
*/
include("./includes/common.php");
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

@header('Content-Type: application/json; charset=UTF-8');

// 对于system_info动作，允许直接访问，不需要严格的referer检查
// Referer校验：伪造Referer的请求拒绝，空Referer放行（兼容移动端、浏览器配置等场景）
if ($act != 'system_info' && !checkRefererHost()) {
	exit('{"code":403,"msg":"Referer check failed"}');
}

if ($islogin2 == 1) {
	$price_obj = new \lib\Price($userrow['zid'], $userrow);
	$cookiesid = $userrow['zid'];
	if ($userrow['power'] > 0) $siterow = $userrow;
} elseif ($is_fenzhan == true) {
	$price_obj = new \lib\Price($siterow['zid'], $siterow);
} else {
	$price_obj = new \lib\Price(1);
}
if ($conf['cjmsg'] != '') {
	$cjmsg = $conf['cjmsg'];
} else {
	$cjmsg = '您今天的抽奖次数已经达到上限！';
}
switch ($act) {
	case 'getshuoshuo':
		$uin = isset($_GET['uin']) ? daddslashes($_GET['uin']) : exit('{"code":-1,"msg":"参数错误"}');
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		if (empty($conf['sslist_api'])) {
			exit('{"code":-1,"msg":"未配置说说列表接口"}');
		}
		
		$api_url = $conf['sslist_api'] . $uin . '&page=' . $page;
		$result = get_curl($api_url);
		$data = json_decode($result, true);
		
		if ($data && isset($data['code']) && $data['code'] == 0) {
			exit(json_encode($data));
		} else {
			exit('{"code":-1,"msg":"获取说说列表失败"}');
		}
		break;
	case 'getRecommend':
		try {
			$DB->query("SELECT * FROM pre_recommend LIMIT 1");
		} catch (Exception $e) {
			exit('{"code":-1,"msg":"推荐功能未启用"}');
		}
		
		$rs = $DB->query("SELECT r.tid, t.name, t.cid, t.price FROM pre_recommend r LEFT JOIN pre_tools t ON r.tid = t.tid WHERE r.active=1 AND t.active=1 ORDER BY r.sort DESC, r.id DESC");
		$recommend_list = array();
		while ($res = $rs->fetch()) {
			$recommend_list[] = array(
				'tid' => $res['tid'],
				'cid' => $res['cid'],
				'name' => $res['name'],
				'price' => $res['price']
			);
		}
		exit(json_encode(array("code" => 0, "msg" => "success", "data" => $recommend_list)));
		break;
	case 'getrizhi':
		$uin = isset($_GET['uin']) ? daddslashes($_GET['uin']) : exit('{"code":-1,"msg":"参数错误"}');
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		if (empty($conf['rzlist_api'])) {
			exit('{"code":-1,"msg":"未配置日志列表接口"}');
		}
		
		$api_url = $conf['rzlist_api'] . $uin . '&page=' . $page;
		$result = get_curl($api_url);
		$data = json_decode($result, true);
		
		if ($data && isset($data['code']) && $data['code'] == 0) {
			exit(json_encode($data));
		} else {
			exit('{"code":-1,"msg":"获取日志列表失败"}');
		}
		break;
	case 'payrmb':
		if (!$islogin2) exit('{"code":-4,"msg":"你还未登录"}');
		$orderid = isset($_POST['orderid']) ? daddslashes($_POST['orderid']) : exit('{"code":-1,"msg":"订单号未知"}');
		$srow = $DB->getRow("SELECT * FROM pre_pay WHERE trade_no=:orderid LIMIT 1", [':orderid' => $orderid]);
		if (!$srow['trade_no'] || $srow['tid'] == -1) exit('{"code":-1,"msg":"订单号不存在！"}');
		if ($srow['money'] == '0') exit('{"code":-1,"msg":"当前商品为免费商品，不需要支付"}');
		if (!preg_match('/^[0-9.]+$/', $srow['money'])) exit('{"code":-1,"msg":"订单金额不合法"}');
		if ($srow['status'] == 0) {
			if ($srow['money'] > $userrow['rmb']) exit('{"code":-3,"msg":"你的余额不足，请充值！"}');
			
			// 对于批量购买订单(tid=-3)，需要特殊处理
			if ($srow['tid'] == -3) {
				// 先扣费
				if ($DB->exec("UPDATE `pre_site` SET `rmb`=`rmb`-:money WHERE `zid`=:zid", [':money' => $srow['money'], ':zid' => $userrow['zid']])) {
					// 更新订单状态为已支付
				$DB->exec("UPDATE `pre_pay` SET `type`='rmb',`status`='1',`endtime`=NOW() WHERE `trade_no`=:orderid", [':orderid' => $orderid]);
					$srow['type'] = 'rmb';
					$srow['status'] = 1;
					
					// 处理批量订单
					if (processOrder($srow)) {
						addPointRecord($userrow['zid'], $srow['money'], '消费', '批量购买 ' . $srow['name']);
						exit('{"code":1,"msg":"您所购买的批量商品已付款成功，感谢购买！"}');
					} else {
						// 如果处理失败，回滚扣费
					$DB->exec("UPDATE `pre_site` SET `rmb`=`rmb`+:money WHERE `zid`=:zid", [':money' => $srow['money'], ':zid' => $userrow['zid']]);
					$DB->exec("UPDATE `pre_pay` SET `type`='',`status`='0',`endtime`=NULL WHERE `trade_no`=:orderid", [':orderid' => $orderid]);
						exit('{"code":-1,"msg":"批量下单失败！' . dbErrorMsg($DB->error()) . '"}');
					}
				} else {
					exit('{"code":-1,"msg":"扣费失败！' . dbErrorMsg($DB->error()) . '"}');
				}
			} else {
				// 普通订单处理
			if ($DB->exec("UPDATE `pre_site` SET `rmb`=`rmb`-:money WHERE `zid`=:zid", [':money' => $srow['money'], ':zid' => $userrow['zid']]) && $DB->exec("UPDATE `pre_pay` SET `type`='rmb',`status`='1',`endtime`=NOW() WHERE `trade_no`=:orderid", [':orderid' => $orderid])) {
					$srow['type'] = 'rmb';
					if ($orderid = processOrder($srow)) {
						addPointRecord($userrow['zid'], $srow['money'], '消费', '购买 ' . $srow['name'] . ' (' . $orderid . ')', $orderid);
						exit('{"code":1,"msg":"您所购买的商品已付款成功，感谢购买！","orderid":"' . $orderid . '"}');
					} else {
						addPointRecord($userrow['zid'], $srow['money'], '消费', '购买 ' . $srow['name']);
						exit('{"code":-1,"msg":"下单失败！' . dbErrorMsg($DB->error()) . '"}');
					}
				} else {
					exit('{"code":-1,"msg":"下单失败！' . dbErrorMsg($DB->error()) . '"}');
				}
			}
		} else {
			exit('{"code":-2,"msg":"当前订单已付款过，请勿重复提交"}');
		}
		break;
	case 'captcha':
		$GtSdk = new \lib\GeetestLib($conf['captcha_id'], $conf['captcha_key']);
		$data = array(
			'user_id' => $cookiesid, # 网站用户id
			'client_type' => "web", # web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
			'ip_address' => $clientip # 请在此处传输用户请求验证时所携带的IP
		);
		$status = $GtSdk->pre_process($data, 1);
		$_SESSION['gtserver'] = $status;
		echo $GtSdk->get_response_str();
		break;
	case 'getcount':
		$strtotime = strtotime($conf['build']); //获取开始统计的日期的时间戳
		$now = time(); //当前的时间戳
		$yxts = ceil(($now - $strtotime) / 86400); //取相差值然后除于24小时(86400秒)
		if ($conf['hide_tongji'] == 1) {
			$result = array("code" => 0, "yxts" => $yxts, "orders" => 0, "orders1" => 0, "orders2" => 0, "money" => 0, "money1" => 0, "gift" => $gift);
			exit(json_encode($result));
		}
		if ($conf['tongji_time'] > 0) {
			$tongji_cachetime = $DB->getColumn("SELECT v FROM pre_config WHERE k='tongji_cachetime' limit 1");
			$tongji_cache = $CACHE->read('tongji');
			if ($tongji_cachetime + intval($conf['tongji_time']) >= time() && $tongji_cache) {
				if ($conf['shoppingcart'] == 1) {
					$cart_count = $DB->getColumn("SELECT count(*) from pre_cart WHERE userid=:cookiesid AND status<=1", array(':cookiesid' => $cookiesid));
				}
				$array = unserialize($tongji_cache);
				$result = array("code" => 0, "yxts" => $yxts, "orders" => $array['orders'], "orders1" => $array['orders1'], "orders2" => $array['orders2'], "money" => $array['money'], "money1" => $array['money1'], "site" => $array['site'], "gift" => $array['gift'], "cart_count" => $cart_count);
				exit(json_encode($result));
			}
		}
		if ($conf['gift_log'] == 1 && $conf['gift_open'] == 1) {
			$gift = array();
			$list = $DB->query("SELECT a.*,(SELECT b.name FROM pre_gift AS b WHERE a.gid=b.id) AS name FROM pre_giftlog AS a WHERE status>=0 ORDER BY id DESC LIMIT 10");
			while ($cjlist = $list->fetch()) {
				// 显示所有中奖记录，使用IP作为key
				$gift[$cjlist['ip']] = $cjlist['name'];
			}
		}
		$time = date("Y-m-d") . ' 00:00:01';
		$count1 = $DB->getColumn("SELECT count(*) FROM pre_orders");
		$count2 = $DB->getColumn("SELECT count(*) FROM pre_orders WHERE status>=1");
		$count3 = $DB->getColumn("SELECT sum(money) FROM pre_pay WHERE status=1");
		$count4 = round($count3, 2);
		$count5 = $DB->getColumn("SELECT count(*) FROM `pre_orders` WHERE  `addtime` > :time", array(':time' => $time));
		$count6 = $DB->getColumn("SELECT sum(money) FROM `pre_pay` WHERE `addtime` > :time AND `status` = 1", array(':time' => $time));
		$count7 = round($count6, 2);
		$count8 = $DB->getColumn("SELECT count(*) from pre_site");
		if ($conf['tongji_time'] > 0) {
			saveSetting('tongji_cachetime', time());
			$CACHE->save('tongji', serialize(array("orders" => $count1, "orders1" => $count2, "orders2" => $count5, "money" => $count4, "money1" => $count7, "site" => $count8, "gift" => $gift)));
		}
		if ($conf['shoppingcart'] == 1) {
			$cart_count = $DB->getColumn("SELECT count(*) FROM pre_cart WHERE userid=:cookiesid AND status<=1", array(':cookiesid' => $cookiesid));
		}

		$result = array("code" => 0, "yxts" => $yxts, "orders" => $count1, "orders1" => $count2, "orders2" => $count5, "money" => $count4, "money1" => $count7, "site" => $count8, "gift" => $gift, "cart_count" => $cart_count);
		exit(json_encode($result));
		break;
	case 'getclass':
		$classhide = explode(',', $siterow['class']);
		$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 ORDER BY sort ASC");
		$data = array();
		while ($res = $rs->fetch(PDO::FETCH_ASSOC)) {
			if ($is_fenzhan && in_array($res['cid'], $classhide)) continue;
			$data[] = $res;
		}
		$result = array("code" => 0, "msg" => "succ", "data" => $data);
		exit(json_encode($result));
		break;
	case 'getsubclass':
		$cid = intval($_GET['cid']);
		$classhide = explode(',', $siterow['class']);
		$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 AND pid=:cid ORDER BY sort ASC", array(':cid' => $cid));
		$html = '<option value="0">请选择二级分类</option>';
		$notices = array();
		$primary_info = null;
		// 返回当前一级分类信息（含图片），用于前台显示一级分类图片
		$primary = $DB->getRow("SELECT cid, name, shopimg FROM pre_class WHERE cid=:cid AND active=1", array(':cid' => $cid));
		if ($primary) {
			$primary_info = array('cid' => $primary['cid'], 'name' => $primary['name'], 'shopimg' => $primary['shopimg']);
		}
		while ($res = $rs->fetch(PDO::FETCH_ASSOC)) {
			if ($is_fenzhan && in_array($res['cid'], $classhide)) continue;
			$html .= '<option value="' . $res['cid'] . '" shopimg="' . htmlspecialchars($res['shopimg'] ?? '') . '">' . $res['name'] . '</option>';
			// 收集二级分类的提示语 - 教主修改，博客地址：6v6.ren Q群：941535592
			if(!empty($res['notice'])) {
				$notices[$res['cid']] = $res['notice'];
			}
		}
		$result = array("code" => 0, "msg" => "succ", "html" => $html, "notices" => $notices, "primary" => $primary_info);
		exit(json_encode($result));
		break;
	case 'gettool':
		if (isset($_POST['kw'])) {
			$kw = trim(daddslashes($_POST['kw']));
			if ($kw == 'random') {
				$rs = $DB->query("SELECT * FROM pre_tools WHERE active=1 ORDER BY rand() LIMIT 10");
			} else {
				$rs = $DB->query("SELECT * FROM pre_tools WHERE name LIKE :kw AND active=1 ORDER BY sort ASC", array(':kw' => '%' . $kw . '%'));
			}
		} elseif (isset($_GET['cid'])) {
			$cid = intval($_GET['cid']);
			$rs = $DB->query("SELECT * FROM pre_tools WHERE cid=:cid AND active=1 ORDER BY sort ASC", array(':cid' => $cid));
			if (isset($_GET['info']) && $_GET['info'] == 1) {
				$info = $DB->getRow("SELECT * FROM pre_class WHERE cid=:cid", array(':cid' => $cid));
			}
		} elseif (isset($_GET['tid'])) {
			$tid = intval($_GET['tid']);
			$rs = $DB->query("SELECT * FROM pre_tools WHERE tid=:tid AND active=1", array(':tid' => $tid));
		} else {
			exit('{"code":-1,"msg":"参数错误"}');
		}
		$data = array();
		while ($res = $rs->fetch(PDO::FETCH_ASSOC)) {
			if (isset($_SESSION['gift_id']) && isset($_SESSION['gift_tid']) && $_SESSION['gift_tid'] == $res['tid']) {
				$price = $conf["cjmoney"] ? $conf["cjmoney"] : 0;
			} elseif (isset($price_obj)) {
				$price_obj->setToolInfo($res['tid'], $res);
				if ($price_obj->getToolDel($res['tid']) == 1) continue;
				$price = $price_obj->getToolPrice($res['tid']);
			} else $price = $res['price'];
			if ($res['is_curl'] == 4) {
				$isfaka = 1;
				$res['input'] = getFakaInput();
			} else {
				$isfaka = 0;
			}
			$isinvitegift = 0;
			$invitegift_money = 0;
			$invite_gift = 0;
			if ($invite_row = $DB->getRow("SELECT * FROM pre_invitegiftshop WHERE tid=:tid LIMIT 1", array(':tid' => $res['tid']))) {
				$isinvitegift = 1;
				$invitegift_money = $invite_row['money'];
				$invite_gift = $invite_row['gift'];
			}
			// 计算销量 - 通过查询pre_pay表中status=1的订单数量
			$sales = $DB->getColumn("SELECT COUNT(*) FROM pre_pay WHERE tid=:tid AND status=1", array(':tid' => $res['tid']));
			$data[] = array('tid' => $res['tid'], 'cid' => $res['cid'], 'sort' => $res['sort'], 'name' => $res['name'], 'value' => $res['value'], 'price' => $price, 'input' => $res['input'], 'inputs' => $res['inputs'], 'desc' => $res['desc'], 'alert' => $res['alert'], 'shopimg' => $res['shopimg'], 'repeat' => $res['repeat'], 'multi' => $res['multi'], 'close' => $res['close'], 'prices' => $res['prices'], 'min' => $res['min'], 'max' => $res['max'], 'sales' => $sales, 'isfaka' => $isfaka, 'stock' => $res['stock'], 'isinvitegift' => $isinvitegift, 'invitegift_money' => $invitegift_money, 'invite_gift' => $invite_gift, 'goods_sid' => $res['goods_sid']);
		}
		$result = array("code" => 0, "msg" => "succ", "data" => $data, "info" => $info);
		exit(json_encode($result));
		break;
	case 'gettoolnew':
			// 设置脚本执行时间，避免超时 - 作者：教主 博客：zhonguo.ren
			set_time_limit(30);
			$page = $_POST['page'] ? intval(trim(daddslashes($_POST['page']))) : 1;
			$limit = $_POST['limit'] ? intval(trim(daddslashes($_POST['limit']))) : 9;
			if ($limit < 1) $limit = 9;
			if ($limit > 18) $limit = 18;
			$page = ($page - 1) * $limit;
			$kw = trim(daddslashes($_POST['kw']));
			$cid = intval($_POST['cid']);
			$sort_type = $_POST['sort_type'] ? trim(daddslashes($_POST['sort_type'])) : 'sort';
	$sort = $_POST['sort'] ? trim(daddslashes($_POST['sort'])) : 'ASC';
	$is_primary_cat = intval($_POST['is_primary_cat']); // 新增：判断是否是一级分类视图
	if (!$cid && $sort_type == 'sort') $sort_type = 'tid';

			$sort_type_arr = ['sort', 'price', 'sales'];
			$sort_arr = ['DESC', 'ASC'];
			$orderBy = "sort ASC";
			if (in_array($sort_type, $sort_type_arr) && in_array($sort, $sort_arr)) {
				$orderBy = "{$sort_type} {$sort}";
			}

			$where = "active=1";
			$params = array();
			if (!empty($kw)) {
				$where .= " and name LIKE :kw";
				$params[':kw'] = '%' . $kw . '%';
			}
			if ($cid) {
			// 如果是一级分类视图，查询所有属于该一级分类下的二级分类的商品，以及直接属于该一级分类的商品
			if ($is_primary_cat == 1) {
				// 优化查询：直接使用子查询，同时包含直接属于一级分类的商品和属于其子分类的商品
				$where .= " AND (cid=:cid OR cid IN (SELECT cid FROM pre_class WHERE pid=:cid AND active=1))";
				$params[':cid'] = $cid;
			} else {
				// 普通分类视图，只查询指定分类的商品
				$where .= " and cid=:cid";
				$params[':cid'] = $cid;
			}
		}

			// 优化查询性能 - 作者：教主 博客：zhonguo.ren
			$num = $DB->getColumn("SELECT count(tid) FROM pre_tools WHERE $where", $params);
			$rs = $DB->query("SELECT * FROM pre_tools WHERE $where ORDER BY $orderBy LIMIT $page,$limit", $params);

		$data = array();
		$curr_time = time();
		// 预加载所有分类的图片，减少数据库查询次数
		$class_imgs = array();
		$class_res = $DB->query("SELECT cid, shopimg FROM pre_class WHERE active = 1");
		while ($class_row = $class_res->fetch(PDO::FETCH_ASSOC)) {
			$class_imgs[$class_row['cid']] = $class_row['shopimg'];
		}
		while ($res = $rs->fetch(PDO::FETCH_ASSOC)) {
			if (isset($_SESSION['gift_id']) && isset($_SESSION['gift_tid']) && $_SESSION['gift_tid'] == $res['tid']) {
				$price = $conf["cjmoney"] ? $conf["cjmoney"] : 0;
			} elseif (isset($price_obj)) {
				$price_obj->setToolInfo($res['tid'], $res);
				if ($price_obj->getToolDel($res['tid']) == 1) continue;
				$price = $price_obj->getToolPrice($res['tid']);
			} else $price = $res['price'];

			// 检查商品是否有图片，如果没有则使用所属分类的图片
			$shopimg = $res['shopimg'];
			if (empty($shopimg) && isset($class_imgs[$res['cid']])) {
				$shopimg = $class_imgs[$res['cid']];
			}

			$is_stock_err = 0;
			if ($res['is_curl'] == 4) {
				$isfaka = 1;
				$count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid=:tid AND orderid=0", array(':tid' => $res['tid']));
				if ($count == 0) $is_stock_err = 1;
				$res['input'] = getFakaInput();
			} elseif ($res['stock'] !== null) {
				$isfaka = 0;
				$count = $res['stock'];
				if ($count == 0) $is_stock_err = 1;
			} else {
				$isfaka = 0;
				$count = null;
			}

			$data[] = array('tid' => $res['tid'], 'cid' => $res['cid'], 'sort' => $res['sort'], 'name' => $res['name'], 'value' => $res['value'], 'price' => $price, 'input' => $res['input'], 'inputs' => $res['inputs'], 'desc' => $res['desc'], 'alert' => $res['alert'], 'shopimg' => $shopimg, 'repeat' => $res['repeat'], 'multi' => $res['multi'], 'close' => $res['close'], 'prices' => $res['prices'], 'min' => $res['min'], 'max' => $res['max'], 'sales' => $res['sales'], 'stock' => $count, 'isfaka' => $isfaka, 'addtime' => strtotime($res['addtime']), 'is_stock_err' => $is_stock_err);
		}
		$pages = ceil($num / $limit);
		$result = array("code" => 0, "msg" => "succ", "data" => $data, "info" => $info, 'pages' => $pages, 'total' => intval($num));
		exit(json_encode($result));
		break;
	case 'getleftcount':
		$tid = intval($_POST['tid']);
		$count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid=:tid AND orderid=0", array(':tid' => $tid));
		if ($conf['faka_showleft'] == 1) $count = $count > 0 ? '充足' : '缺货';
		$result = array("code" => 0, "count" => $count);
		exit(json_encode($result));
		break;
	case 'pay':
		$method = $_GET['method'];
		$inputvalue = trim(daddslashes($_POST['inputvalue']));
		$inputvalue2 = trim(daddslashes($_POST['inputvalue2']));
		$inputvalue3 = trim(daddslashes($_POST['inputvalue3']));
		$inputvalue4 = trim(daddslashes($_POST['inputvalue4']));
		$inputvalue5 = trim(daddslashes($_POST['inputvalue5']));
		$address = isset($_POST['address']) ? trim(daddslashes($_POST['address'])) : '';
		if (empty($address)) {
			// 如果没有直接提交address，则从inputvalue2-5中查找包含"地址"或"收货地址"的值
			$address_inputs = [$inputvalue2, $inputvalue3, $inputvalue4, $inputvalue5];
			foreach ($address_inputs as $addr) {
				if (!empty($addr)) {
					$address = $addr;
					break;
				}
			}
		}
		$num = isset($_POST['num']) ? intval($_POST['num']) : 1;
		// 防止整数溢出导致的0元购：num 必须是正整数且在合理范围内
		if ($num <= 0 || $num > 100000) {
			exit('{"code":-1,"msg":"购买数量不合法"}');
		}
		$hashsalt = isset($_POST['hashsalt']) ? $_POST['hashsalt'] : null;
		if ($method == 'cart_edit') {
			$shop_id = intval($_POST['shop_id']);
			$cart_item = $DB->getRow("SELECT * FROM `pre_cart` WHERE `id`=:shop_id LIMIT 1", array(':shop_id' => $shop_id));
			if (!$cart_item) exit('{"code":-1,"msg":"商品不存在！"}');
			if ($cart_item['userid'] != $cookiesid || $cart_item['status'] > 1) exit('{"code":-1,"msg":"商品权限校验失败"}');
			$tool = $DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid LIMIT 1", array(':tid' => $cart_item['tid']));
		} else {
			$tid = intval($_POST['tid']);
			$tool = $DB->getRow("SELECT A.*,B.blockpay FROM pre_tools A LEFT JOIN pre_class B ON A.cid=B.cid WHERE tid=:tid LIMIT 1", array(':tid' => $tid));
		}
		if ($tool && $tool['active'] == 1) {
			if ($tool['close'] == 1) exit('{"code":-1,"msg":"当前商品维护中，停止下单！"}');
			if (($conf['forcermb'] == 1 || $conf['forcelogin'] == 1) && !$islogin2) exit('{"code":4,"msg":"你还未登录"}');
			if (!empty($tool['blockpay']) && !$islogin2) {
				$blockpay = explode(',', $tool['blockpay']);
				if (in_array('alipay', $blockpay) && in_array('qqpay', $blockpay) && in_array('wxpay', $blockpay)) exit('{"code":4,"msg":"当前商品需要登录后才能下单"}');
			}
			if (($tool['is_curl'] == 4 || $tool['price'] == 0) && isset($conf['free_goods_login_receive']) && $conf['free_goods_login_receive'] == 1 && !$islogin2) {
				exit('{"code":4,"msg":"免费商品需要登录后才能领取"}');
			}
			// 简化验证逻辑，只在必要时验证
			if ($conf['verify_open'] == 1) {
				// 如果没有提供hashsalt或session中的addsalt不存在，跳过验证
				if (!empty($hashsalt) && !empty($_SESSION['addsalt']) && $hashsalt != $_SESSION['addsalt']) {
					exit('{"code":-1,"msg":"验证失败，请刷新页面重试"}');
				}
				// 验证通过后，清除session中的addsalt，防止重复使用
				unset($_SESSION['addsalt']);
			}
			$inputs = explode('|', $tool['inputs']);
			// 检查哪些输入框是收货地址 - 博客地址：zhonguo.ren QQ群：915043052
			$address_input_indices = [];
			foreach ($inputs as $index => $input_name) {
				if (strpos($input_name, '收货地址') !== false || strpos($input_name, '地址') !== false) {
					$address_input_indices[] = $index;
				}
			}
			
			// 验证必填字段（排除收货地址）
			$validation_failed = false;
			if (empty($inputvalue)) {
				$validation_failed = true;
			} else {
				foreach ($inputs as $index => $input_name) {
					$input_var_name = 'inputvalue' . ($index + 2);
					if (!in_array($index, $address_input_indices) && !empty($input_name)) {
						if ($index == 0 && empty($inputvalue2)) {
							$validation_failed = true;
							break;
						} elseif ($index == 1 && empty($inputvalue3)) {
							$validation_failed = true;
							break;
						} elseif ($index == 2 && empty($inputvalue4)) {
							$validation_failed = true;
							break;
						} elseif ($index == 3 && empty($inputvalue5)) {
							$validation_failed = true;
							break;
						}
					}
				}
			}
			
			if ($validation_failed) {
				exit('{"code":-1,"msg":"请确保各项不能为空"}');
			}
			
			// 验证字段匹配（排除收货地址）
			$match_validation_failed = false;
			foreach ($inputs as $index => $input_name) {
				if (!in_array($index, $address_input_indices)) {
					if ($index == 0 && empty($input_name) && !empty($inputvalue2)) {
						$match_validation_failed = true;
						break;
					} elseif ($index == 1 && empty($input_name) && !empty($inputvalue3)) {
						$match_validation_failed = true;
						break;
					} elseif ($index == 2 && empty($input_name) && !empty($inputvalue4)) {
						$match_validation_failed = true;
						break;
					} elseif ($index == 3 && empty($input_name) && !empty($inputvalue5)) {
						$match_validation_failed = true;
						break;
					}
				}
			}
			
			if ($match_validation_failed) {
				exit('{"code":-1,"msg":"验证失败"}');
			}
			if (in_array($inputvalue, explode("|", $conf['blacklist']))) exit('{"code":-1,"msg":"你的下单账号已被拉黑，无法下单！"}');
			if ($tool['is_curl'] == 4) {
				if (!$islogin2 && $conf['faka_input'] == 0 && !checkEmail($inputvalue)) {
					exit('{"code":-1,"msg":"邮箱格式不正确"}');
				}
				$count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid=:tid AND orderid=0", array(':tid' => $tid));
				$nums = ($tool['value'] > 1 ? $tool['value'] : 1) * $num;
				if ($count == 0) exit('{"code":-1,"msg":"该商品库存卡密不足，请联系站长加卡！"}');
				if ($nums > $count) exit('{"code":-1,"msg":"你所购买的数量超过库存数量！"}');
			} elseif ($tool['stock'] !== null) {
				if ($tool['stock'] == 0) exit('{"code":-1,"msg":"该商品库存不足，请联系站长增加库存！"}');
				if ($num > $tool['stock']) exit('{"code":-1,"msg":"你所购买的数量超过库存数量！"}');
			} elseif ($tool['repeat'] == 0) {
				$thtime = date("Y-m-d") . ' 00:00:00';
				$row = $DB->getRow("SELECT id,input,status,addtime FROM pre_orders WHERE tid=:tid AND input=:input ORDER BY id DESC LIMIT 1", [':tid' => $tid, ':input' => $inputvalue]);
				if ($row['input'] && $row['status'] == 0)
					exit('{"code":-1,"msg":"您今天添加的' . $tool['name'] . '正在排队中，请勿重复提交！"}');
				elseif ($row['addtime'] > $thtime)
					exit('{"code":-1,"msg":"您今天已添加过' . $tool['name'] . '，请勿重复提交！"}');
			}
			if ($tool['validate'] == 1 && is_numeric($inputvalue)) {
				if (validate_qzone($inputvalue) == false)
					exit('{"code":-1,"msg":"你的QQ空间设置了访问权限，无法下单！"}');
			} elseif (($tool['validate'] == 2 || $tool['validate'] == 3) && is_numeric($inputvalue)) {
				$qqservices = ['vip' => 'QQ会员', 'svip' => '超级会员', 'bigqqvip' => '大会员', 'red' => '红钻贵族', 'green' => '绿钻贵族', 'sgreen' => '绿钻豪华版', 'yellow' => '黄钻贵族', 'syellow' => '豪华黄钻', 'hollywood' => '腾讯视频VIP', 'qqmsey' => '付费音乐包', 'qqmstw' => '豪华付费音乐包', 'weiyun' => '微云会员', 'sweiyun' => '微云超级会员'];
				$services = getservices($inputvalue, $qqservices[$tool['valiserv']]);
				if ($services['code'] != 0) exit('{"code":-1,"msg":"' . $services['msg'] . '"}');
				if ($services['code'] == 200) {
					if ($tool['validate'] == 2) {
						exit('{"code":-1,"msg":"您的QQ已经开通了' . $qqservices[$tool['valiserv']] . '，该商品无法购买！"}');
					} else {
						$blockdj = 1;
					}
				}
			}
			if ($tool['multi'] == 0 || $num < 1) $num = 1;
			if ($tool['multi'] == 1 && $tool['min'] > 0 && $num < $tool['min']) exit('{"code":-1,"msg":"当前商品最小下单数量为' . $tool['min'] . '"}');
			if ($tool['multi'] == 1 && $tool['max'] > 0 && $num > $tool['max']) exit('{"code":-1,"msg":"当前商品最大下单数量为' . $tool['max'] . '"}');
			if (isset($_SESSION['gift_id']) && isset($_SESSION['gift_tid']) && $_SESSION['gift_tid'] == $tid) {
				$gift_id = intval($_SESSION['gift_id']);
				$giftlog = $DB->getColumn("SELECT status FROM pre_giftlog WHERE id=:id LIMIT 1", array(':id' => $gift_id));
				if ($giftlog == 1) {
					unset($_SESSION['gift_id']);
					unset($_SESSION['gift_tid']);
					exit('{"code":-1,"msg":"当前奖品已经领取过了！"}');
				}
				$price = $conf["cjmoney"] ? $conf["cjmoney"] : 0;
				$num = 1;
			} elseif (isset($price_obj)) {
				$price_obj->setToolInfo($tid, $tool);
				$price = $price_obj->getToolPrice($tid);
				$price = $price_obj->getFinalPrice($price, $num);
				if (!$price) exit('{"code":-1,"msg":"当前商品批发价格优惠设置不正确"}');
				
				// 地区加价计算 - 博客地址：zhonguo.ren QQ群：915043052
				if (!empty($address)) {
					$regionPrice = new \lib\RegionPrice();
					$region_result = $regionPrice->calculatePrice($price, $address, $tid, $tool['name']);
					if ($region_result['matched']) {
						$price = $region_result['final_price'];
					}
				}
			} else {
				$price = $tool['price'];
			}

			// 验证价格逻辑，防止0元购漏洞，但允许真正的免费商品
			// 区分真正的免费商品（原始价格为0）和异常的0元购（原始价格>0但最终价格<=0）
			if ($price <= 0 && !isset($_SESSION['gift_id'])) {
				// 如果商品原始价格是0，这是真正的免费商品，允许购买
				if ($tool['price'] == 0) {
					// 真正的免费商品，添加IP限制，防止被刷
					if ($method == 'cart_add' || $method == 'cart_edit') exit('{"code":-1,"msg":"免费商品请直接点击领取"}');
					$thtime = date("Y-m-d") . ' 00:00:00';
					if ($_SESSION['blockfree'] == true || $DB->getColumn("SELECT count(*) FROM `pre_pay` WHERE `money`=0 AND `ip`=:clientip AND `status`=1 AND `addtime`>:thtime", [':clientip' => $clientip, ':thtime' => $thtime]) >= 1) {
						exit('{"code":-1,"msg":"您今天已领取过，请明天再来！"}');
					}
					if ($conf['captcha_open_free'] == 1 && $conf['captcha_open'] == 1) {
						if (isset($_POST['geetest_challenge']) && isset($_POST['geetest_validate']) && isset($_POST['geetest_seccode'])) {
							if (!isset($_SESSION['gtserver'])) exit('{"code":-1,"msg":"验证加载失败"}');

							$GtSdk = new \lib\GeetestLib($conf['captcha_id'], $conf['captcha_key']);

							$data = array(
								'user_id' => $cookiesid,
								'client_type' => "web",
								'ip_address' => $clientip
							);

							if ($_SESSION['gtserver'] == 1) {   //服务器正常
								$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
								if ($result) {
									//echo '{"status":"success"}';
								} else {
									exit('{"code":-1,"msg":"验证失败，请重新验证"}');
								}
							} else {  //服务器宕机,走failback模式
								if ($GtSdk->fail_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'])) {
									//echo '{"status":"success"}';
								} else {
									exit('{"code":-1,"msg":"验证失败，请重新验证"}');
								}
							}
						} else {
							exit('{"code":2,"type":1,"msg":"请先完成验证"}');
						}
					} elseif ($conf['captcha_open_free'] == 1 && $conf['captcha_open'] == 2) {
						if (isset($_POST['token'])) {
							$client = new \lib\CaptchaClient($conf['captcha_id'], $conf['captcha_key']);
							$client->setTimeOut(2);
							$response = $client->verifyToken($_POST['token']);
							if ($response->result) {
								/**token验证通过，继续其他流程**/
							} else {
								/**token验证失败**/
								exit('{"code":-1,"msg":"验证失败，请重新验证"}');
							}
						} else {
							exit('{"code":2,"type":2,"appid":"' . $conf['captcha_id'] . '","msg":"请先完成验证"}');
						}
					} elseif ($conf['captcha_open_free'] == 1 && $conf['captcha_open'] == 3) {
						if (isset($_POST['token'])) {
							if (vaptcha_verify($conf['captcha_id'], $conf['captcha_key'], $_POST['token'], $clientip)) {
								/**token验证通过，继续其他流程**/
							} else {
								/**token验证失败**/
								exit('{"code":-1,"msg":"验证失败，请重新验证"}');
							}
						} else {
							exit('{"code":2,"type":3,"appid":"' . $conf['captcha_id'] . '","msg":"请先完成验证"}');
						}
					}
				} else {
					// 商品原始价格>0但最终价格<=0，可能是0元购漏洞，需要验证
					if ($method == 'cart_add' || $method == 'cart_edit') exit('{"code":-1,"msg":"免费商品请直接点击领取"}');
					$thtime = date("Y-m-d") . ' 00:00:00';
					if ($_SESSION['blockfree'] == true || $DB->getColumn("SELECT count(*) FROM `pre_pay` WHERE `money`=0 AND `ip`=:clientip AND `status`=1 AND `addtime`>:thtime", [':clientip' => $clientip, ':thtime' => $thtime]) >= 1) {
						exit('{"code":-1,"msg":"您今天已领取过，请明天再来！"}');
					}
					if ($conf['captcha_open_free'] == 1 && $conf['captcha_open'] == 1) {
						if (isset($_POST['geetest_challenge']) && isset($_POST['geetest_validate']) && isset($_POST['geetest_seccode'])) {
							if (!isset($_SESSION['gtserver'])) exit('{"code":-1,"msg":"验证加载失败"}');

							$GtSdk = new \lib\GeetestLib($conf['captcha_id'], $conf['captcha_key']);

							$data = array(
								'user_id' => $cookiesid,
								'client_type' => "web",
								'ip_address' => $clientip
							);

							if ($_SESSION['gtserver'] == 1) {   //服务器正常
								$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
								if ($result) {
									//echo '{"status":"success"}';
								} else {
									exit('{"code":-1,"msg":"验证失败，请重新验证"}');
								}
							} else {  //服务器宕机,走failback模式
								if ($GtSdk->fail_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'])) {
									//echo '{"status":"success"}';
								} else {
									exit('{"code":-1,"msg":"验证失败，请重新验证"}');
								}
							}
						} else {
							exit('{"code":2,"type":1,"msg":"请先完成验证"}');
						}
					} elseif ($conf['captcha_open_free'] == 1 && $conf['captcha_open'] == 2) {
						if (isset($_POST['token'])) {
							$client = new \lib\CaptchaClient($conf['captcha_id'], $conf['captcha_key']);
							$client->setTimeOut(2);
							$response = $client->verifyToken($_POST['token']);
							if ($response->result) {
								/**token验证通过，继续其他流程**/
							} else {
								/**token验证失败**/
								exit('{"code":-1,"msg":"验证失败，请重新验证"}');
							}
						} else {
							exit('{"code":2,"type":2,"appid":"' . $conf['captcha_id'] . '","msg":"请先完成验证"}');
						}
					} elseif ($conf['captcha_open_free'] == 1 && $conf['captcha_open'] == 3) {
						if (isset($_POST['token'])) {
							if (vaptcha_verify($conf['captcha_id'], $conf['captcha_key'], $_POST['token'], $clientip)) {
								/**token验证通过，继续其他流程**/
							} else {
								/**token验证失败**/
								exit('{"code":-1,"msg":"验证失败，请重新验证"}');
							}
						} else {
							exit('{"code":2,"type":3,"appid":"' . $conf['captcha_id'] . '","msg":"请先完成验证"}');
						}
					}
				}
			}

			$i = 2;
			$neednum = $num;
			foreach ($inputs as $inputname) {
				if (strpos($inputname, '[multi]') !== false && isset(${'inputvalue' . $i}) && is_numeric(${'inputvalue' . $i})) {
					$val = intval(${'inputvalue' . $i});
					if ($val > 0) {
						$neednum = $neednum * $val;
					}
				}
				$i++;
			}

			$need = round($price * $neednum, 2);
			// 再次验证价格，确保不会出现0元购，但允许真正的免费商品
			if ($need <= 0 && !isset($_SESSION['gift_id'])) {
				// 如果商品原始价格是0，这是真正的免费商品，允许购买
				if ($tool['price'] == 0) {
					// 真正的免费商品，继续正常流程
				} else {
					// 商品原始价格>0但最终价格<=0，可能是0元购漏洞
					exit('{"code":-1,"msg":"商品价格异常，请联系站长"}');
				}
			}
			//下单对接预检查
			if ($need > 0 && $tool['shequ'] > 0 && $tool['is_curl'] == 2 && in_array($tool['cid'], explode(",", $conf['pricejk_cid'])) && time() - $tool['uptime'] >= $conf['pricejk_time']) {
				$shequ = $DB->getRow("select * from pre_shequ where id=:id limit 1", array(':id' => $tool['shequ']));
				$allowType = explode(',', $CACHE->read('pricejk_type2'));
				if ($conf['pricejk_yile'] == 0 && in_array($shequ['type'], $allowType) && $tool['prid'] > 0) {
					$num_change = third_call($shequ['type'], $shequ, 'pricejk_one', [$tool]);
					if ($num_change > 0) {
						exit('{"code":3,"msg":"当前商品价格发生变化，请刷新页面重试","change":"' . $num_change . '"}');
					}
				} else {
				//		$apireturn = third_call($shequ['type'], $shequ, 'pre_check', [$tool, $num]);
				//	if ($apireturn && $apireturn['code'] == -1) {
				//		exit('{"code":3,"msg":"' . $apireturn['msg'] . '"}');
				//	}
				}
			}

			$trade_no = date("YmdHis") . mt_rand(111, 999);
			$input = $inputvalue . ($inputvalue2 ? '|' . $inputvalue2 : null) . ($inputvalue3 ? '|' . $inputvalue3 : null) . ($inputvalue4 ? '|' . $inputvalue4 : null) . ($inputvalue5 ? '|' . $inputvalue5 : null);
			// 地区加价计算 - 博客地址：zhonguo.ren QQ群：915043052
			$region_order_id = $trade_no;
			if ($method == 'cart_add') {
				$sql = "INSERT INTO `pre_cart` (`userid`,`zid`,`tid`,`input`,`num`,`money`,`addtime`,`blockdj`,`status`,`address`) VALUES (:userid, :zid, :tid, :input, :num, :money, NOW(), :blockdj, 0, :address)";
				$data = [':userid' => $cookiesid, ':zid' => $siterow['zid'] ? $siterow['zid'] : 1, ':tid' => $tid, ':input' => $input, ':num' => $num, ':money' => $need, ':blockdj' => $blockdj ? $blockdj : 0, ':address' => $address];
				if ($DB->exec($sql, $data)) {
					$cart_count = $DB->getColumn("SELECT count(*) FROM pre_cart WHERE userid='$cookiesid' AND status<=1");
					exit('{"code":0,"msg":"加入购物车成功！","need":"' . $need . '","cart_count":"' . $cart_count . '"}');
				} else {
					exit('{"code":-1,"msg":"加入购物车失败！' . dbErrorMsg($DB->error()) . '"}');
				}
			} elseif ($method == 'cart_edit') {
				$sql = "UPDATE `pre_cart` SET `input`=:input,`num`=:num,`money`=:money,`status`='0',`address`=:address WHERE id=:id";
				$data = [':input' => $input, ':num' => $num, ':money' => $need, ':address' => $address, ':id' => $shop_id];
				if ($DB->exec($sql, $data) !== false) {
					exit('{"code":0,"msg":"编辑订单成功！","need":"' . $need . '"}');
				} else {
					exit('{"code":-1,"msg":"编辑订单失败！' . dbErrorMsg($DB->error()) . '"}');
				}
			} elseif ($need == 0) {
				$trade_no = 'free' . $trade_no;
				$num = 1;
				$sql = "INSERT INTO `pre_pay` (`trade_no`,`tid`,`zid`,`type`,`input`,`num`,`name`,`money`,`ip`,`userid`,`addtime`,`blockdj`,`status`,`address`) VALUES (:trade_no, :tid, :zid, :type, :input, :num, :name, :money, :ip, :userid, NOW(), :blockdj, 1, :address)";
				$data = [':trade_no' => $trade_no, ':tid' => $tid, ':zid' => $siterow['zid'] ? $siterow['zid'] : 1, ':type' => 'free', ':input' => $input, ':num' => $num, ':name' => $tool['name'], ':money' => $need, ':ip' => $clientip, ':userid' => $cookiesid, ':blockdj' => $blockdj ? $blockdj : 0, ':address' => $address];
				if ($DB->exec($sql, $data)) {
					unset($_SESSION['addsalt']);
					if (isset($_SESSION['gift_id'])) {
						$DB->exec("UPDATE `pre_giftlog` SET `status`=1,`tradeno`=:tradeno,`input`=:input WHERE `id`=:id", [':tradeno' => $trade_no, ':input' => $inputvalue, ':id' => $gift_id]);
						unset($_SESSION['gift_id']);
						unset($_SESSION['gift_tid']);
						$_SESSION['blockfree'] = true;
					}
					$srow['tid'] = $tid;
					$srow['input'] = $input;
					$srow['num'] = $num;
					$srow['zid'] = $siterow['zid'] ? $siterow['zid'] : 1;
					$srow['userid'] = $cookiesid;
					$srow['trade_no'] = $trade_no;
					$srow['money'] = 0;
					$srow['address'] = $address;
					if ($orderid = processOrder($srow)) {
						exit('{"code":1,"msg":"下单成功！你可以在进度查询中查看订单进度","orderid":"' . $orderid . '"}');
					} else {
						exit('{"code":-1,"msg":"下单失败！' . dbErrorMsg($DB->error()) . '"}');
					}
				}
			} else {
				$sql = "INSERT INTO `pre_pay` (`trade_no`,`tid`,`zid`,`input`,`num`,`name`,`money`,`ip`,`userid`,`inviteid`,`addtime`,`blockdj`,`status`,`address`) VALUES (:trade_no, :tid, :zid, :input, :num, :name, :money, :ip, :userid, :inviteid, NOW(), :blockdj, 0, :address)";
				$data = [':trade_no' => $trade_no, ':tid' => $tid, ':zid' => $siterow['zid'] ? $siterow['zid'] : 1, ':input' => $input, ':num' => $num, ':name' => $tool['name'], ':money' => $need, ':ip' => $clientip, ':userid' => $cookiesid, ':inviteid' => $invite_id, ':blockdj' => $blockdj ? $blockdj : 0, ':address' => $address];
				if ($DB->exec($sql, $data)) {
					unset($_SESSION['addsalt']);
					if ($conf['forcermb'] == 1) {
						$conf['alipay_api'] = 0;
						$conf['wxpay_api'] = 0;
						$conf['qqpay_api'] = 0;
					}
					$payUsdt = !empty($conf['codepay_key']) ? 1 : 0;
					if (!empty($tool['blockpay'])) {
						$blockpay = explode(',', $tool['blockpay']);
						if (in_array('alipay', $blockpay)) $conf['alipay_api'] = 0;
						if (in_array('qqpay', $blockpay)) $conf['qqpay_api'] = 0;
						if (in_array('wxpay', $blockpay)) $conf['wxpay_api'] = 0;
						if (in_array('rmb', $blockpay)) $islogin2 = 0;
						if (in_array('usdt', $blockpay)) $payUsdt = 0;
					}
					$result = ['code' => 0, 'msg' => '提交订单成功！', 'trade_no' => $trade_no, 'need' => $need, 'pay_alipay' => $conf['alipay_api'], 'pay_wxpay' => $conf['wxpay_api'], 'pay_qqpay' => $conf['qqpay_api'], 'pay_rmb' => $islogin2, 'pay_usdt' => $payUsdt, 'user_rmb' => $userrow['rmb'], 'paymsg' => $conf['paymsg']];
					exit(json_encode($result));
				} else {
					exit('{"code":-1,"msg":"提交订单失败！' . dbErrorMsg($DB->error()) . '"}');
				}
			}
		} else {
			exit('{"code":-2,"msg":"该商品不存在"}');
		}
		break;
	case 'pays':
		if (!$conf['openbatchorder']) exit('{"code":-1,"msg":"未开启批量下单功能"}');
		$inputvalues = $_POST['inputvalues'];
		$hashsalt = isset($_POST['hashsalt']) ? $_POST['hashsalt'] : null;
		$tid = intval($_POST['tid']);
		$tool = $DB->getRow("SELECT A.*,B.blockpay FROM pre_tools A LEFT JOIN pre_class B ON A.cid=B.cid WHERE tid=:tid LIMIT 1", [':tid' => $tid]);
		if ($tool && $tool['active'] == 1) {
			if ($tool['close'] == 1) exit('{"code":-1,"msg":"当前商品维护中，停止下单！"}');
			if (($conf['forcermb'] == 1 || $conf['forcelogin'] == 1) && !$islogin2) exit('{"code":4,"msg":"你还未登录"}');
			if (!empty($tool['blockpay']) && !$islogin2) {
				$blockpay = explode(',', $tool['blockpay']);
				if (in_array('alipay', $blockpay) && in_array('qqpay', $blockpay) && in_array('wxpay', $blockpay)) exit('{"code":4,"msg":"当前商品需要登录后才能下单"}');
			}
			if (($tool['is_curl'] == 4 || $tool['price'] == 0) && isset($conf['free_goods_login_receive']) && $conf['free_goods_login_receive'] == 1 && !$islogin2) {
				exit('{"code":4,"msg":"免费商品需要登录后才能领取"}');
			}
			// 简化验证逻辑，只在必要时验证
				if ($conf['verify_open'] == 1) {
					// 如果没有提供hashsalt或session中的addsalt不存在，跳过验证
					if (!empty($hashsalt) && !empty($_SESSION['addsalt']) && $hashsalt != $_SESSION['addsalt']) {
						exit('{"code":-1,"msg":"验证失败，请刷新页面重试"}');
					}
					// 验证通过后，清除session中的addsalt，防止重复使用
					unset($_SESSION['addsalt']);
				}
			$inputvalues = str_replace(array("\r\n", "\r", "\n"), "[br]", $inputvalues);
			$match = explode("[br]", $inputvalues);
			$num = 0;
			$inputs = [];
			foreach ($match as $val) {
				$inputvalue = htmlspecialchars(trim(strip_tags(daddslashes($val))));
				if ($val == '') continue;
				$inputs[] = $inputvalue;
				$num++;
			}
			if ($num == 0) exit('{"code":-1,"msg":"下单账号不能为空"}');
			// 防止整数溢出：限制批量下单数量上限
			if ($num > 5000) {
				exit('{"code":-1,"msg":"批量下单数量超过上限，请分批提交"}');
			}

			if ($tool['is_curl'] == 4) {
				$count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid=:tid AND orderid=0", [':tid' => $tid]);
				$nums = ($tool['value'] > 1 ? $tool['value'] : 1) * $num;
				if ($count == 0) exit('{"code":-1,"msg":"该商品库存卡密不足，请联系站长加卡！"}');
				if ($nums > $count) exit('{"code":-1,"msg":"你所购买的数量超过库存数量！"}');
			} elseif ($tool['stock'] !== null) {
				if ($tool['stock'] == 0) exit('{"code":-1,"msg":"该商品库存不足，请联系站长增加库存！"}');
				if ($num > $tool['stock']) exit('{"code":-1,"msg":"你所购买的数量超过库存数量！"}');
			}
			if (isset($price_obj)) {
				$price_obj->setToolInfo($tid, $tool);
				$price = $price_obj->getToolPrice($tid);
				$price = $price_obj->getFinalPrice($price, $num);
				if (!$price) exit('{"code":-1,"msg":"当前商品批发价格优惠设置不正确"}');
			} else $price = $tool['price'];

			if ($price == 0) {
				exit('{"code":-1,"msg":"免费商品不支持批量下单"}');
			}
			$need = round($price * $num, 2);

			//下单对接预检查
			if ($need > 0 && $tool['shequ'] > 0 && $tool['is_curl'] == 2 && in_array($tool['cid'], explode(",", $conf['pricejk_cid'])) && time() - $tool['uptime'] >= $conf['pricejk_time']) {
				$shequ = $DB->getRow("select * from pre_shequ where id='{$tool['shequ']}' limit 1");
				$allowType = explode(',', $CACHE->read('pricejk_type2'));
				if ($conf['pricejk_yile'] == 0 && in_array($shequ['type'], $allowType) && $tool['prid'] > 0) {
					$num_change = third_call($shequ['type'], $shequ, 'pricejk_one', [$tool]);
					if ($num_change > 0) {
						exit('{"code":3,"msg":"当前商品价格发生变化，请刷新页面重试","change":"' . $num_change . '"}');
					}
				} else {
					$apireturn = third_call($shequ['type'], $shequ, 'pre_check', [$tool, $num]);
					if ($apireturn && $apireturn['code'] == -1) {
						exit('{"code":3,"msg":"' . $apireturn['msg'] . '"}');
					}
				}
			}

			$ids = array();
			foreach ($inputs as $input) {
				$sql = "INSERT INTO `pre_cart` (`userid`,`zid`,`tid`,`input`,`num`,`money`,`addtime`,`blockdj`,`status`) VALUES (:userid, :zid, :tid, :input, :num, :money, NOW(), :blockdj, 1)";
				$data = [':userid' => $cookiesid, ':zid' => $siterow['zid'] ? $siterow['zid'] : 1, ':tid' => $tid, ':input' => $input, ':num' => 1, ':money' => $price, ':blockdj' => 0];
				$DB->exec($sql, $data);
				$ids[] = $DB->lastInsertId();
			}
			$input = implode('|', $ids);

			$trade_no = date("YmdHis") . rand(111, 999);
			$sql = "INSERT INTO `pre_pay` (`trade_no`,`tid`,`zid`,`input`,`num`,`name`,`money`,`ip`,`userid`,`inviteid`,`addtime`,`status`) VALUES (:trade_no, :tid, :zid, :input, :num, :name, :money, :ip, :userid, :inviteid, NOW(), 0)";
			$data = [':trade_no' => $trade_no, ':tid' => -3, ':zid' => $siterow['zid'] ? $siterow['zid'] : 1, ':input' => $input, ':num' => count($ids), ':name' => $tool['name'], ':money' => $need, ':ip' => $clientip, ':userid' => $cookiesid, ':inviteid' => $invite_id];
			if ($DB->exec($sql, $data)) {
				unset($_SESSION['addsalt']);
				$payUsdt = !empty($conf['codepay_key']) ? 1 : 0;
				if ($conf['forcermb'] == 1) {
					$conf['alipay_api'] = 0;
					$conf['wxpay_api'] = 0;
					$conf['qqpay_api'] = 0;
					$payUsdt = 0;
				}
				$result = ['code' => 0, 'msg' => '提交订单成功！', 'trade_no' => $trade_no, 'need' => $need, 'num' => $num, 'pay_alipay' => $conf['alipay_api'], 'pay_wxpay' => $conf['wxpay_api'], 'pay_qqpay' => $conf['qqpay_api'], 'pay_rmb' => $islogin2, 'pay_usdt' => $payUsdt, 'user_rmb' => $userrow['rmb'], 'paymsg' => $conf['paymsg']];
				exit(json_encode($result));
			} else {
				exit('{"code":-1,"msg":"提交订单失败！' . dbErrorMsg($DB->error()) . '"}');
			}
		} else {
			exit('{"code":-2,"msg":"该商品不存在"}');
		}
		break;
	case 'cancel':
		$orderid = isset($_POST['orderid']) ? trim($_POST['orderid']) : exit('{"code":-1,"msg":"订单号未知"}');
		$hashsalt = isset($_POST['hashsalt']) ? $_POST['hashsalt'] : null;
		$srow = $DB->getRow("SELECT trade_no,userid FROM pre_pay WHERE trade_no=:orderid LIMIT 1", [':orderid' => $orderid]);
		if (!$srow['trade_no'] || $srow['userid'] != $cookiesid) exit('{"code":-1,"msg":"订单号不存在！"}');
		if ($srow['status'] == 0) {
			//$DB->exec("DELETE FROM pre_pay WHERE trade_no=:orderid", [':orderid'=>$orderid]);
			if ($conf['verify_open'] == 1) {
				$_SESSION['addsalt'] = $hashsalt;
			}
		}
		exit('{"code":0,"msg":"ok"}');
		break;
	case 'card_check':
		if ($conf['iskami'] == 0) exit('{"code":-1,"msg":"当前站点未开启卡密下单"}');
		$km = trim(daddslashes($_POST['km']));
		$hashsalt = isset($_POST['hashsalt']) ? $_POST['hashsalt'] : null;
		// 简化验证逻辑，只在必要时验证，且不清除session
		if ($conf['verify_open'] == 1) {
			// 如果没有提供hashsalt或session中的addsalt不存在，跳过验证
			if (!empty($hashsalt) && !empty($_SESSION['addsalt']) && $hashsalt != $_SESSION['addsalt']) {
				exit('{"code":-1,"msg":"验证失败，请刷新页面重试"}');
			}
			// 注意：这里不清除session中的addsalt，因为后续还需要在card_pay中使用
		}
		$myrow = $DB->getRow("SELECT * FROM pre_kms WHERE km=:km AND type=1 LIMIT 1", [':km' => $km]);
		if (!$myrow) exit('{"code":-1,"msg":"此卡密不存在！"}');
		if ($myrow['status'] == 1) exit('{"code":-1,"msg":"此卡密已被使用！"}');
		$res = $DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid AND active=1 LIMIT 1", [':tid' => $myrow['tid']]);
		if (!$res) exit('{"code":-1,"msg":"当前卡密对应的商品不存在"}');
		if ($res['is_curl'] == 4) {
			$isfaka = 1;
			$res['input'] = getFakaInput();
		} else {
			$isfaka = 0;
			// 确保非卡密类型商品也有input参数
			if (empty($res['input']) || $res['input'] == 'hide' || $res['input'] == 'null') {
				$res['input'] = '下单账号';
			}
		}
		$result = array("code" => 0, "num" => $myrow['num'], "data" => array('tid' => $res['tid'], 'cid' => $res['cid'], 'sort' => $res['sort'], 'name' => $res['name'], 'value' => $res['value'], 'price' => $res['price'], 'input' => $res['input'], 'inputs' => $res['inputs'], 'desc' => urlencode($res['desc']), 'alert' => urlencode($res['alert']), 'shopimg' => $res['shopimg'], 'repeat' => $res['repeat'], 'multi' => $res['multi'], 'close' => $res['close'], 'prices' => $res['prices'], 'min' => $res['min'], 'max' => $res['max'], 'sales' => $res['sales'], 'isfaka' => $isfaka, 'stock' => $res['stock']));
		exit(json_encode($result));
		break;
	case 'card_pay':
		if ($conf['iskami'] == 0) exit('{"code":-1,"msg":"当前站点未开启卡密下单"}');
		$km = trim(daddslashes($_POST['km']));
		$inputvalue = trim(daddslashes($_POST['inputvalue']));
		$inputvalue2 = trim(daddslashes($_POST['inputvalue2']));
		$inputvalue3 = trim(daddslashes($_POST['inputvalue3']));
		$inputvalue4 = trim(daddslashes($_POST['inputvalue4']));
		$inputvalue5 = trim(daddslashes($_POST['inputvalue5']));
		$hashsalt = isset($_POST['hashsalt']) ? $_POST['hashsalt'] : null;
		$myrow = $DB->getRow("SELECT * FROM pre_kms WHERE km=:km AND type=1 LIMIT 1", [':km' => $km]);
		if (!$myrow) exit('{"code":-1,"msg":"此卡密不存在！"}');
		if ($myrow['status'] == 1) exit('{"code":-1,"msg":"此卡密已被使用！"}');
		$num = $myrow['num'] ? $myrow['num'] : 1;
		$tid = $myrow['tid'];
		$tool = $DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid LIMIT 1", [':tid' => $tid]);
		if ($tool && $tool['active'] == 1) {
			if ($tool['close'] == 1) exit('{"code":-1,"msg":"当前商品维护中，停止下单！"}');
			if ($conf['forcelogin'] == 1 && !$islogin2) exit('{"code":4,"msg":"你还未登录"}');
			if (($tool['is_curl'] == 4 || $tool['price'] == 0) && isset($conf['free_goods_login_receive']) && $conf['free_goods_login_receive'] == 1 && !$islogin2) {
				exit('{"code":4,"msg":"免费商品需要登录后才能领取"}');
			}
			// 简化验证逻辑，只在必要时验证
				if ($conf['verify_open'] == 1) {
					// 如果没有提供hashsalt或session中的addsalt不存在，跳过验证
					if (!empty($hashsalt) && !empty($_SESSION['addsalt']) && $hashsalt != $_SESSION['addsalt']) {
						exit('{"code":-1,"msg":"验证失败，请刷新页面重试"}');
					}
					// 验证通过后，清除session中的addsalt，防止重复使用
					unset($_SESSION['addsalt']);
				}
			// 验证码处理 - 卡密购买是免费商品，需要验证码
			$code = isset($_POST['code']) ? $_POST['code'] : null;
			if ($conf['captcha_open_free'] == 1) { // 卡密购买是免费商品，需要验证码
				if ($conf['captcha_open'] == 1) {
					if (isset($_POST['geetest_challenge']) && isset($_POST['geetest_validate']) && isset($_POST['geetest_seccode'])) {
						if (!isset($_SESSION['gtserver'])) exit('{"code":-1,"msg":"验证加载失败"}');

						$GtSdk = new \lib\GeetestLib($conf['captcha_id'], $conf['captcha_key']);

						$data = array(
							'user_id' => $cookiesid,
							'client_type' => "web",
							'ip_address' => $clientip
						);

						if ($_SESSION['gtserver'] == 1) {   //服务器正常
							$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
							if ($result) {
								//echo '{"status":"success"}';
							} else {
								exit('{"code":-1,"msg":"验证失败，请重新验证"}');
							}
						} else {  //服务器宕机,走failback模式
							if ($GtSdk->fail_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'])) {
								//echo '{"status":"success"}';
							} else {
								exit('{"code":-1,"msg":"验证失败，请重新验证"}');
							}
						}
					} else {
						exit('{"code":2,"msg":"请完成验证码验证","type":1}');
					}
				} elseif ($conf['captcha_open'] == 2) {
					if (isset($_POST['dx_captcha_token'])) {
						$dx_captcha_token = $_POST['dx_captcha_token'];
						$dx_captcha_app_id = $conf['captcha_id'];
						$dx_captcha_app_secret = $conf['captcha_key'];
						$dx_captcha_verify_url = "https://captcha.dingxiang-inc.com/api/tokenVerify";
						$dx_captcha_post_data = "app_id=" . $dx_captcha_app_id . "&app_secret=" . $dx_captcha_app_secret . "&token=" . $dx_captcha_token;
						$dx_captcha_response = postRequest($dx_captcha_verify_url, $dx_captcha_post_data);
						$dx_captcha_response_data = json_decode($dx_captcha_response, true);
						if ($dx_captcha_response_data['success']) {
							//echo '{"status":"success"}';
						} else {
							exit('{"code":-1,"msg":"验证失败，请重新验证"}');
						}
					} else {
						exit('{"code":2,"msg":"请完成验证码验证","type":2}');
					}
				} elseif ($conf['captcha_open'] == 3) {
					if (isset($_POST['vaptcha_token'])) {
						$vaptcha_token = $_POST['vaptcha_token'];
						$vaptcha_id = $conf['captcha_id'];
						$vaptcha_key = $conf['captcha_key'];
						$vaptcha_verify_url = "http://0.vaptcha.com/verify";
						$vaptcha_post_data = "id=" . $vaptcha_id . "&secretkey=" . $vaptcha_key . "&token=" . $vaptcha_token . "&ip=" . $clientip;
						$vaptcha_response = postRequest($vaptcha_verify_url, $vaptcha_post_data);
						$vaptcha_response_data = json_decode($vaptcha_response, true);
						if ($vaptcha_response_data['success'] == 1) {
							//echo '{"status":"success"}';
						} else {
							exit('{"code":-1,"msg":"验证失败，请重新验证"}');
						}
					} else {
						exit('{"code":2,"msg":"请完成验证码验证","type":3}');
					}
				} else {
				// 简化验证码处理，跳过传统验证码验证
				// 直接通过验证，不再要求输入验证码
			}
			}
			$inputs = explode('|', $tool['inputs']);
			if (empty($inputvalue) || $inputs[0] && empty($inputvalue2) || $inputs[1] && empty($inputvalue3) || $inputs[2] && empty($inputvalue4) || $inputs[3] && empty($inputvalue5)) {
				exit('{"code":-1,"msg":"请确保各项不能为空"}');
			}
			if (!$inputs[0] && !empty($inputvalue2) || !$inputs[1] && !empty($inputvalue3) || !$inputs[2] && !empty($inputvalue4) || !$inputs[3] && !empty($inputvalue5)) {
				exit('{"code":-1,"msg":"验证失败"}');
			}
			if (in_array($inputvalue, explode("|", $conf['blacklist']))) exit('{"code":-1,"msg":"你的下单账号已被拉黑，无法下单！"}');
			if ($tool['is_curl'] == 4) {
				if (!$islogin2 && $conf['faka_input'] == 0 && !checkEmail($inputvalue)) {
					exit('{"code":-1,"msg":"邮箱格式不正确"}');
				}
				$count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid='$tid' AND orderid=0");
				$nums = ($tool['value'] > 1 ? $tool['value'] : 1) * $num;
				if ($count == 0) exit('{"code":-1,"msg":"该商品库存卡密不足，请联系站长加卡！"}');
				if ($nums > $count) exit('{"code":-1,"msg":"你所购买的数量超过库存数量！"}');
			} elseif ($tool['stock'] !== null) {
				if ($tool['stock'] == 0) exit('{"code":-1,"msg":"该商品库存不足，请联系站长增加库存！"}');
				if ($num > $tool['stock']) exit('{"code":-1,"msg":"你所购买的数量超过库存数量！"}');
			} elseif ($tool['repeat'] == 0) {
				$thtime = date("Y-m-d") . ' 00:00:00';
				$row = $DB->getRow("SELECT id,input,status,addtime FROM pre_orders WHERE tid=:tid AND input=:input ORDER BY id DESC LIMIT 1", [':tid' => $tid, ':input' => $inputvalue]);
				if ($row['input'] && $row['status'] == 0)
					exit('{"code":-1,"msg":"您今天添加的' . $tool['name'] . '正在排队中，请勿重复提交！"}');
				elseif ($row['addtime'] > $thtime)
					exit('{"code":-1,"msg":"您今天已添加过' . $tool['name'] . '，请勿重复提交！"}');
			}
			if ($tool['validate'] == 1 && is_numeric($inputvalue)) {
				if (validate_qzone($inputvalue) == false)
					exit('{"code":-1,"msg":"你的QQ空间设置了访问权限，无法下单！"}');
			} elseif (($tool['validate'] == 2 || $tool['validate'] == 3) && is_numeric($inputvalue)) {
				$services = getservices($inputvalue);
				if ($services['code'] != 0) exit('{"code":-1,"msg":"' . $services['msg'] . '"}');
				$qqservices = ['vip' => 'QQ会员', 'svip' => '超级会员', 'bigqqvip' => '大会员', 'red' => '红钻贵族', 'green' => '绿钻贵族', 'sgreen' => '绿钻豪华版', 'yellow' => '黄钻贵族', 'syellow' => '豪华黄钻', 'hollywood' => '腾讯视频VIP', 'qqmsey' => '付费音乐包', 'qqmstw' => '豪华付费音乐包', 'weiyun' => '微云会员', 'sweiyun' => '微云超级会员'];
				if (in_array($tool['valiserv'], $services['data'])) {
					if ($tool['validate'] == 2) {
						exit('{"code":-1,"msg":"您的QQ已经开通了' . $qqservices[$tool['valiserv']] . '，该商品无法购买！"}');
					} else {
						$blockdj = 1;
					}
				}
			}
			if ($tool['multi'] == 0 || $num < 1) $num = 1;
			if ($tool['multi'] == 1 && $tool['min'] > 0 && $num < $tool['min']) exit('{"code":-1,"msg":"当前商品最小下单数量为' . $tool['min'] . '"}');
			if ($tool['multi'] == 1 && $tool['max'] > 0 && $num > $tool['max']) exit('{"code":-1,"msg":"当前商品最大下单数量为' . $tool['max'] . '"}');

			$trade_no = 'kid:' . $myrow['kid'];
			$input = $inputvalue . ($inputvalue2 ? '|' . $inputvalue2 : null) . ($inputvalue3 ? '|' . $inputvalue3 : null) . ($inputvalue4 ? '|' . $inputvalue4 : null) . ($inputvalue5 ? '|' . $inputvalue5 : null);
			$srow['tid'] = $tid;
			$srow['input'] = $input;
			$srow['num'] = $num;
			$srow['zid'] = $siterow['zid'] ? $siterow['zid'] : 1;
			$srow['userid'] = $cookiesid;
			$srow['trade_no'] = $trade_no;
			$srow['money'] = 0;
			if ($orderid = processOrder($srow)) {
				unset($_SESSION['addsalt']);
				$DB->query("UPDATE `pre_kms` SET `status`=1,`orderid`='$orderid',`usetime`=NOW() where `kid`='{$myrow['kid']}'");
				exit('{"code":1,"msg":"下单成功！你可以在进度查询中查看订单进度","orderid":"' . $orderid . '"}');
			} else {
				exit('{"code":-1,"msg":"下单失败！' . dbErrorMsg($DB->error()) . '"}');
			}
		} else {
			exit('{"code":-2,"msg":"该商品不存在"}');
		}
		break;
	case 'query':
		$type = intval($_POST['type']);
		$qq = trim(daddslashes($_POST['qq']));
		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$limit = 10;
		$start = $limit * ($page - 1);
		
		if ($type == 1 && !empty($qq)) {
			if (strlen($qq) == 17 && is_numeric($qq)) {
				$rs = $DB->query("SELECT A.*,B.`name` FROM `pre_orders` A LEFT JOIN `pre_tools` B ON A.`tid`=B.`tid` WHERE A.`tradeno`=:qq ORDER BY A.`id` DESC LIMIT $start,$limit", array(':qq' => $qq));
			} else if (is_numeric($qq)) {
				$rs = $DB->query("SELECT A.*,B.`name` FROM `pre_orders` A LEFT JOIN `pre_tools` B ON A.`tid`=B.`tid` WHERE A.`id`=:qq AND A.`userid`=:cookiesid ORDER BY A.`id` DESC LIMIT $start,$limit", array(':qq' => $qq, ':cookiesid' => $cookiesid));
			} else {
				exit('{"code":-1,"msg":"请输入正确的订单号"}');
			}
		} elseif (!empty($qq)) {
			if ($conf['queryorderlimit'] == 1) {
				$rs = $DB->query("SELECT A.*,B.`name` FROM `pre_orders` A LEFT JOIN `pre_tools` B ON A.`tid`=B.`tid` WHERE A.`input`=:qq AND A.`userid`=:cookiesid ORDER BY A.`id` DESC LIMIT $start,$limit", array(':qq' => $qq, ':cookiesid' => $cookiesid));
			} else {
				$rs = $DB->query("SELECT A.*,B.`name` FROM `pre_orders` A LEFT JOIN `pre_tools` B ON A.`tid`=B.`tid` WHERE A.`input`=:qq ORDER BY A.`id` DESC LIMIT $start,$limit", array(':qq' => $qq));
			}
		} else {
			$rs = $DB->query("SELECT A.*,B.`name` FROM `pre_orders` A LEFT JOIN `pre_tools` B ON A.`tid`=B.`tid` WHERE A.`userid`=:cookiesid ORDER BY A.`id` DESC LIMIT $start,$limit", array(':cookiesid' => $cookiesid));
		}
		
		$data = array();
		$count = 0;
		while ($res = $rs->fetch(PDO::FETCH_ASSOC)) {
			$count++;
			$data[] = array('id' => $res['id'], 'tid' => $res['tid'], 'input' => $res['input'], 'name' => $res['name'], 'value' => $res['value'], 'addtime' => $res['addtime'], 'endtime' => $res['endtime'], 'result' => $res['result'], 'status' => $res['status'], 'skey' => md5($res['id'] . SYS_KEY . $res['id']));
		}
		if ($page > 1 && $count == 0) exit('{"code":-1,"msg":"没有更多订单了"}');
		$result = array("code" => 0, "msg" => "succ", "content" => $qq, "page" => $page, "isnext" => ($count == $limit ? true : false), "islast" => ($page > 1 ? true : false), "data" => $data);
		exit(json_encode($result));
		break;
	case 'apply_refund':
		// 确保所有输出都是JSON格式
		header('Content-Type: application/json; charset=UTF-8');
		
		try {
			// 1. 初始化变量
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$skey = isset($_POST['skey']) ? $_POST['skey'] : '';
			$islogin2 = isset($islogin2) ? $islogin2 : 0;
			$cookiesid = isset($cookiesid) ? $cookiesid : 0;
			$userrow = isset($userrow) ? $userrow : array();
			$conf['selfrefund'] = isset($conf['selfrefund']) ? $conf['selfrefund'] : 0;
			
			// 2. 基本参数验证
			if (empty($id) || empty($skey)) {
				throw new Exception('参数错误');
			}
			
			// 3. 验证skey
			$expected_skey = md5($id . SYS_KEY . $id);
			if ($skey !== $expected_skey) {
				throw new Exception('验证失败');
			}
			
			// 4. 检查登录状态
			if (!$islogin2) {
				throw new Exception('请先登录');
			}
			
			// 5. 检查自助退款功能是否开启
			if ($conf['selfrefund'] != 1) {
				throw new Exception('当前站点未开启自助退款功能');
			}
			
			// 6. 查询订单信息
			$row = $DB->getRow("SELECT * FROM pre_orders WHERE id=:id AND userid=:userid LIMIT 1", array(':id' => $id, ':userid' => $cookiesid));
			if (!$row) {
				throw new Exception('订单不存在或无权限操作');
			}
			
			// 7. 检查订单状态
			if ($row['status'] == 4) {
				throw new Exception('该订单已经退款');
			}
			if ($row['status'] == 1) {
				throw new Exception('已完成的订单无法退款');
			}
			
			// 8. 更新订单状态为已退款
			$update_result = $DB->exec("UPDATE pre_orders SET status=4, result='用户自助申请退款' WHERE id=:id", array(':id' => $id));
			if (!$update_result) {
				throw new Exception('更新订单状态失败');
			}
			
			// 9. 退还金额到用户余额
			$money = $row['money'];
			if (isset($userrow['zid'])) {
				$refund_result = $DB->exec("UPDATE pre_site SET rmb=rmb+:money WHERE zid=:zid", array(':money' => $money, ':zid' => $userrow['zid']));
				if (!$refund_result) {
					throw new Exception('退还金额失败');
				}
			} else {
				throw new Exception('用户信息错误');
			}
			
			// 10. 返回成功结果
			exit(json_encode(array(
				'code' => 0,
				'msg' => '退款成功',
				'money' => $money
			)));
			
		} catch (Exception $e) {
			// 异常处理
			$error_msg = $e->getMessage();
			exit(json_encode(array(
				'code' => -1,
				'msg' => $error_msg
			)));
		}
		break;
	case 'order': //订单进度查询
		// 确保所有输出都是JSON格式
		header('Content-Type: application/json; charset=UTF-8');
		
		try {
			// 1. 初始化变量
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$skey = isset($_POST['skey']) ? $_POST['skey'] : '';
			$islogin2 = isset($islogin2) ? $islogin2 : 0;
			$conf['show_complain'] = isset($conf['show_complain']) ? $conf['show_complain'] : 0;
			$conf['selfrefund'] = isset($conf['selfrefund']) ? $conf['selfrefund'] : 0;
			
			// 2. 基本参数验证
			if (empty($id) || empty($skey)) {
				throw new Exception('参数错误');
			}
			
			// 3. 验证skey
			$expected_skey = md5($id . SYS_KEY . $id);
			if ($skey !== $expected_skey) {
				throw new Exception('验证失败');
			}
			
			// 4. 查询订单基本信息
			$row = $DB->getRow("SELECT * FROM pre_orders WHERE id=:id LIMIT 1", array(':id' => $id));
			if (!$row) {
				throw new Exception('当前订单不存在！');
			}
			
			// 5. 查询工具信息
			$tool = array(
				'name' => '未知商品',
				'input' => '下单账号',
				'alert' => '',
				'desc' => ''
			);
			
			$tool_row = null;
			if (isset($row['tid'])) {
				$tool_row = $DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid LIMIT 1", array(':tid' => $row['tid']));
				if ($tool_row) {
					$tool = array(
						'name' => $tool_row['name'],
						'input' => $tool_row['input'],
						'alert' => $tool_row['alert'],
						'desc' => $tool_row['desc']
					);
				}
			}
			
			// 6. 处理div对接商品的实时状态查询
			$list = false;
			if ($tool_row && $tool_row['is_curl'] == 2 && !empty($row['djorder'])) {
				// 查询社区/卡盟信息
				$shequ = $DB->getRow("SELECT * FROM pre_shequ WHERE id=:shequ_id LIMIT 1", array(':shequ_id' => $tool_row['shequ']));
				if ($shequ) {
					// 调用第三方API获取实时订单状态
					$inputs = array($row['input'], $row['input2'], $row['input3'], $row['input4'], $row['input5']);
					$order_state = third_call($shequ['type'], $shequ, 'query_order', array($row['djorder'], $tool_row['goods_id'], $inputs));
					if ($order_state && is_array($order_state)) {
						$list = $order_state;
					}
				}
			}
			
			// 7. 查询卡密信息
			$kminfo = '';
			if (isset($row['status']) && $row['status'] == 1) {
				// 首先尝试从pre_faka表查询卡密信息
				$km_rs = $DB->query("SELECT * FROM pre_faka WHERE orderid=:orderid ORDER BY kid ASC", array(':orderid' => $id));
				if ($km_rs) {
					$km_data = array();
					while ($km_row = $km_rs->fetch(PDO::FETCH_ASSOC)) {
						$km_data[] = $km_row['km'] . ($km_row['pw'] ? '----' . $km_row['pw'] : '');
					}
					if (!empty($km_data)) {
						$kminfo = '<div style="max-height:200px;overflow-y:auto;">';
						foreach ($km_data as $km) {
							$kminfo .= '<p style="margin:5px 0;">' . $km . '</p>';
						}
						$kminfo .= '</div>';
					}
				}
				
				// 如果没有卡密信息，检查订单的result字段是否包含卡密信息
				if (empty($kminfo) && !empty($row['result'])) {
					// 检查result字段是否包含卡密信息
					if (strpos($row['result'], '----') !== false || strpos($row['result'], '卡密') !== false || strpos($row['result'], '密码') !== false) {
						$kminfo = '<div style="max-height:200px;overflow-y:auto;">';
						$kminfo .= '<p style="margin:5px 0;">' . nl2br(htmlspecialchars($row['result'])) . '</p>';
						$kminfo .= '</div>';
					}
				}
			}
			
			// 8. 构建返回结果
			$return_data = array(
				'code' => 0,
				'msg' => 'succ',
				'name' => $tool['name'],
				'money' => isset($row['money']) ? $row['money'] : 0,
				'date' => isset($row['addtime']) ? $row['addtime'] : '',
				'inputs' => $tool['input'] . '：' . (isset($row['input']) ? $row['input'] : ''),
				'list' => $list,
				'kminfo' => $kminfo,
				'alert' => $tool['alert'],
				'desc' => $tool['desc'],
				'status' => isset($row['status']) ? $row['status'] : 0,
				'result' => isset($row['result']) ? htmlspecialchars($row['result']) : '',
				'complain' => intval($conf['show_complain']),
				'islogin' => $islogin2,
				'selfrefund' => $conf['selfrefund']
			);
			
			exit(json_encode($return_data));
			
		} catch (Exception $e) {
			// 异常处理
			$error_msg = $e->getMessage();
			exit(json_encode(array(
				'code' => -1,
				'msg' => $error_msg
			)));
		}
		break;
	case 'cart_list':
		// 确保所有输出都是JSON格式
		header('Content-Type: application/json; charset=UTF-8');
		
		try {
			// 1. 初始化变量
			$cookiesid = isset($cookiesid) ? $cookiesid : 0;
			
			// 2. 查询购物车数据
			$cart_list = $DB->query("SELECT * FROM pre_cart WHERE userid=:cookiesid AND status<=1 ORDER BY addtime DESC", array(':cookiesid' => $cookiesid));
			
			// 3. 处理购物车数据
			$cart_data = array();
			$total_price = 0;
			$total_count = 0;
			
			while ($row = $cart_list->fetch(PDO::FETCH_ASSOC)) {
				// 查询商品信息
				$tool_row = $DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid LIMIT 1", array(':tid' => $row['tid']));
				if ($tool_row) {
					$cart_item = array(
						'id' => $row['id'],
						'tid' => $row['tid'],
						'name' => $tool_row['name'],
						'input' => $row['input'],
						'num' => $row['num'],
						'money' => $row['money'],
						'addtime' => $row['addtime'],
						'status' => $row['status'],
						'shopimg' => $tool_row['shopimg']
					);
					$cart_data[] = $cart_item;
					$total_price += $row['money'];
					$total_count += $row['num'];
				}
			}
			
			// 4. 返回结果
			$result = array(
				'code' => 0,
				'msg' => 'success',
				'data' => $cart_data,
				'total_price' => $total_price,
				'total_count' => $total_count
			);
			
			exit(json_encode($result));
			
		} catch (Exception $e) {
			// 异常处理
			$error_msg = $e->getMessage();
			exit(json_encode(array(
				'code' => -1,
				'msg' => $error_msg
			)));
		}
		break;
	case 'gift_start':
		// 抽奖功能 - 博客地址：6v6.ren QQ群：941535592
		if ($conf['gift_open'] != 1) {
			exit('{"code":-1,"msg":"抽奖功能未开启"}');
		}
		
		// 检查每日抽奖次数
		$today = date("Y-m-d");
		$today_count = $DB->getColumn("SELECT count(*) FROM pre_giftlog WHERE userid=:cookiesid AND DATE(addtime)=:today", array(':cookiesid' => $cookiesid, ':today' => $today));
		
		if ($today_count >= $conf['cjcishu']) {
			exit('{"code":-1,"msg":"' . $cjmsg . '"}');
		}
		
		// 如果是action=ok，生成token
		if (isset($_GET['action']) && $_GET['action'] == 'ok') {
			$token = md5(SYS_KEY . time() . rand(1000, 9999));
			$_SESSION['gift_token'] = $token;
			exit('{"code":0,"token":"' . $token . '"}');
		}
		
		// 获取所有奖项
		$gift_list = $DB->query("SELECT a.*,b.cid FROM pre_gift AS a LEFT JOIN pre_tools AS b ON a.tid=b.tid WHERE a.ok=0");
		$data = array();
		while ($row = $gift_list->fetch(PDO::FETCH_ASSOC)) {
			$data[] = array(
				'tid' => $row['tid'],
				'cid' => $row['cid'],
				'name' => $row['name'],
				'rate' => $row['rate']
			);
		}
		
		if (empty($data)) {
			exit('{"code":-1,"msg":"暂无可用奖项"}');
		}
		
		exit(json_encode(array("code" => 0, "data" => $data)));
		break;
	case 'gift_stop':
		// 抽奖停止确认 - 博客地址：6v6.ren QQ群：941535592
		if ($conf['gift_open'] != 1) {
			exit('{"code":-1,"msg":"抽奖功能未开启"}');
		}
		
		// 验证token
		$token = isset($_POST['token']) ? $_POST['token'] : '';
		if (!$token || !isset($_SESSION['gift_token']) || $token != $_SESSION['gift_token']) {
			exit('{"code":-1,"msg":"验证失败"}');
		}
		
		// 检查每日抽奖次数
		$today = date("Y-m-d");
		$today_count = $DB->getColumn("SELECT count(*) FROM pre_giftlog WHERE userid=:cookiesid AND DATE(addtime)=:today", array(':cookiesid' => $cookiesid, ':today' => $today));
		
		if ($today_count >= $conf['cjcishu']) {
			exit('{"code":-1,"msg":"' . $cjmsg . '"}');
		}
		
		// 获取所有奖项
		$gift_list = $DB->query("SELECT a.*,b.cid FROM pre_gift AS a LEFT JOIN pre_tools AS b ON a.tid=b.tid WHERE a.ok=0");
		$gifts = array();
		$total_rate = 0;
		
		while ($row = $gift_list->fetch(PDO::FETCH_ASSOC)) {
			$gifts[] = $row;
			$total_rate += $row['rate'];
		}
		
		if (empty($gifts)) {
			exit('{"code":-1,"msg":"暂无可用奖项"}');
		}
		
		// 根据概率计算中奖结果
		$rand_num = mt_rand(1, $total_rate);
		$current_rate = 0;
		$win_gift = null;
		
		foreach ($gifts as $gift) {
			$current_rate += $gift['rate'];
			if ($rand_num <= $current_rate) {
				$win_gift = $gift;
				break;
			}
		}
		
		if (!$win_gift) {
			// 如果没有中奖，随机选择一个
			$win_gift = $gifts[array_rand($gifts)];
		}
		
		// 记录中奖信息
		$insert_data = array(
			':zid' => $siterow['zid'] ? $siterow['zid'] : 1,
			':tid' => $win_gift['tid'],
			':gid' => $win_gift['id'],
			':userid' => $cookiesid,
			':ip' => $clientip,
			':addtime' => date('Y-m-d H:i:s')
		);
		
		$sql = "INSERT INTO pre_giftlog (zid, tid, gid, userid, ip, addtime) VALUES (:zid, :tid, :gid, :userid, :ip, :addtime)";
		
		if ($DB->exec($sql, $insert_data)) {
			$gift_id = $DB->lastInsertId();
			$_SESSION['gift_id'] = $gift_id;
			$_SESSION['gift_tid'] = $win_gift['tid'];
			
			// 清除token
			unset($_SESSION['gift_token']);
			
			exit('{"code":0,"name":"' . $win_gift['name'] . '","tid":"' . $win_gift['tid'] . '","cid":"' . $win_gift['cid'] . '"}');
		} else {
			exit('{"code":-1,"msg":"抽奖失败，请重试"}');
		}
		break;
}

// 关闭数据库连接
$DB = null;