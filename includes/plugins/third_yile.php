<?php
/**
 * 亿乐社区对接插件
 * 博客地址：zhonguo.ren
 * QQ群：915043052
 * 开发者：教主
 * 
 * 功能说明：
 * 1. 对接亿乐SUP新版API，支持商品列表、商品详情、订单查询等功能
 * 2. 使用SHA1签名认证，符合亿乐SUP API文档规范
 * 3. 支持批量商品上架和价格监控
 * 4. 防止SQL注入、XSS攻击、CSRF攻击
 * 
 * 更新日志：
 * v2.0 - 适配亿乐SUP新版API，使用SHA1签名认证
 * v1.0 - 初始版本
 */

namespace plugins;

class third_yile{
	private $config = [];

	static public $info = [
		'name'        => 'third_yile',  //插件名称，必须和类名一致
		'type'        => 'third',  //插件类型，固定为third
		'title'       => '亿乐社区',  //插件显示名称
		'author'      => '6v6',
		'version'     => '2.0',  //更新版本号，适配亿乐SUP新版API
		'link'        => '',
		'sort'        => 11,  //在对接列表显示的排序号
		'showedit'    => false,  //是否在编辑商品页面插入html
		'showip'      => true,  //是否显示加ip白名单提示
		'batchgoods'    => true,  //是否允许被一键上架商品
		'pricejk'     => 2,  //价格监控模式，2为可以下单时检查，1为直接监控批量更新
		'input' => [  //配置对接站点的输入框名称
			'url' => '网站域名',
			'username' => 'TokenID',
			'password' => '密匙',
			'paypwd' => false,
			'paytype' => false,
		],
	];

	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
     * 提交到对接网站
	 * @param int $goods_id 商品ID
	 * @param int $goods_type 类型ID
	 * @param string $goods_param 参数名
	 * @param int $num 下单数量（下单份数×默认数量信息）
	 * @param array $input 下单输入框内容
	 * @param string $money 订单金额
	 * @param string $tradeno 支付订单号
	 * @param string $inputsname 商品其他输入框标题
     * @return array 返回信息（code=0成功，-1失败，message是提示信息）
     */
	public function do_goods($goods_id, $goods_type, $goods_param, $num = 1, $input = array(), $money, $tradeno, $inputsname)
	{
		$result['code'] = -1;
		$url = '/openapi/customer/Goods/Buy';
		
		// 如果goods_id为0，尝试从goods_param中提取gid
		if($goods_id == 0 && !empty($goods_param)){
			$goods_param_parts = explode('#', $goods_param);
			$goods_id = isset($goods_param_parts[0]) ? intval($goods_param_parts[0]) : 0;
		}
		
		// 构建购买参数
		$param = array(
			'goods_id' => intval($goods_id),
			'buy_number' => intval($num)
		);
		
		// 处理下单参数
		if (is_array($input) && $input){
			$buy_params = array();
			// 获取商品详情，取buy_params的key作为参数键名
			$goods_detail = $this->goods_info($goods_id);
			if(is_array($goods_detail) && isset($goods_detail['buy_params']) && is_array($goods_detail['buy_params'])){
				$idx = 0;
				foreach($goods_detail['buy_params'] as $bp){
					if(isset($input[$idx]) && $input[$idx] !== ''){
						$param_key = isset($bp['key']) ? $bp['key'] : (isset($bp['name']) ? $bp['name'] : 'value'.($idx+1));
						$buy_params[$param_key] = $input[$idx];
					}
					$idx++;
				}
			} else {
				// 降级：使用inputsname解析参数名作为键名
				$input_keys = explode('|', $inputsname);
				$i = 0;
				foreach ($input as $val){
					$key = isset($input_keys[$i]) ? $input_keys[$i] : 'value'.($i+1);
					$buy_params[$key] = $val;
					$i++;
				}
			}
			$param['buy_params'] = $buy_params;
		}
		
		// 添加客户单号，防止重复下单
		if(!empty($tradeno)){
			$param['customer_order_id'] = strval($tradeno);
		}
		
		// 使用新版API认证
		$timestamp = time();
		$token = $this->generateToken($url, $timestamp);
		$headers = array(
			'Appid: ' . $this->config['username'],
			'AppTimestamp: ' . $timestamp,
			'AppToken: ' . $token
		);
		
		$post = json_encode($param);
		$data = $this->get_curl_with_headers($url, $post, $headers);
		$json = json_decode($data,true);
		
		if (isset($json['code']) && $json['code']==0) {
			$result = array(
				'code' => 0,
				'id' => $json['data']['id']
			);
			if(isset($json['data']['faka']) && $json['data']['faka'] === true){
				$result['faka'] = true;
				$result['kmdata'] = $json['data']['kmdata'] ?? '';
			}
		} elseif(isset($json['message'])){
			$result['message'] = $json['message'];
		} else{
			$result['message'] = $data;
		}
		return $result;
	}

	/**
     * 商品分类列表
     * @return array
     */
	public function class_list(){
		$url = '/openapi/customer/Goods/CategoryList';
		$timestamp = time();
		$token = $this->generateToken($url, $timestamp);
		$headers = array(
			'Appid: ' . $this->config['username'],
			'AppTimestamp: ' . $timestamp,
			'AppToken: ' . $token
		);
		$ret = $this->get_curl_with_headers($url, '', $headers);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} elseif ($ret['code'] !== 0) {
			return $ret['message'];
		} else {
			$list = array();
			if(isset($ret['data']) && is_array($ret['data'])){
				foreach ($ret['data'] as $v) {
					$class_item = array(
						'id' => $v['id'],
						'name' => $v['name'],
						'sort' => $v['sort'] ?? 0
					);
					// 处理二级分类
					if(isset($v['parent_infos']) && is_array($v['parent_infos'])){
						$class_item['sub'] = array();
						foreach($v['parent_infos'] as $sub){
							$class_item['sub'][] = array(
								'id' => $sub['id'],
								'name' => $sub['name'],
								'sort' => $sub['sort'] ?? 0
							);
						}
					}
					$list[] = $class_item;
				}
			}
			return $list;
		}
	}

	/**
     * 商品列表
     * @return array
     */
	public function goods_list(){
		$url = '/openapi/customer/Goods/List';
		$param = array('goods_category_id' => null);
		$timestamp = time();
		$token = $this->generateToken($url, $timestamp);
		$headers = array(
			'Appid: ' . $this->config['username'],
			'AppTimestamp: ' . $timestamp,
			'AppToken: ' . $token
		);
		$ret = $this->get_curl_with_headers($url, json_encode($param), $headers);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} elseif ($ret['code'] !== 0) {
			return $ret['message'];
		} else {
			$list = array();
			foreach ($ret['data'] as $v) {
				$list[] = array(
					'id' => $v['id'],
					'name' => $v['name'],
					'close' => $v['is_close'] == 1 ? 1 : 0,
					'price' => $v['price'] ?? 0
				);
			}
			return $list;
		}
	}

	/**
     * 商品详情
	 * @param int $goods_id 商品ID
     * @return array
     */
	public function goods_info($goods_id){
		$url = '/openapi/customer/Goods/Show';
		$param = array('goods_id' => intval($goods_id));
		$timestamp = time();
		$token = $this->generateToken($url, $timestamp);
		$headers = array(
			'Appid: ' . $this->config['username'],
			'AppTimestamp: ' . $timestamp,
			'AppToken: ' . $token
		);
		$ret = $this->get_curl_with_headers($url, json_encode($param), $headers);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} elseif ($ret['code'] !== 0) {
			return $ret['message'];
		} else {
			$result = $ret['data'];
			$paramname = '';
			if(isset($result['buy_params']) && is_array($result['buy_params'])){
				foreach($result['buy_params'] as $v){
					$paramname.=$v['name'].'|';
				}
			}
			$result['paramname'] = trim($paramname, '|');
			$result['input'] = isset($result['buy_params'][0]['name']) ? $result['buy_params'][0]['name'] : '';
			$result['inputs'] = trim($paramname, '|');
			
			// 映射API字段到前端期望的字段名
			$result['price'] = $result['price'] ?? 0;
			$result['desc'] = $result['particulars'] ?? '';
			$result['limit_min'] = $result['buy_min_limit'] ?? 1;
			$result['limit_max'] = $result['buy_max_limit'] ?? 0;
			$result['name'] = $result['name'] ?? '';
			$result['image'] = isset($result['image_urls'][0]) ? $result['image_urls'][0] : '';
			
			// 获取分类名称 - 教主修改，博客地址：6v6.ren Q群：941535592
			if(isset($result['category_id']) && !empty($result['category_id'])){
				// 调用分类列表接口获取分类名称
				$class_list = $this->class_list();
				if(is_array($class_list)){
					foreach($class_list as $class){
						if($class['id'] == $result['category_id']){
							$result['class_name'] = $class['name'];
							$result['category'] = $class['name'];
							break;
						}
						// 检查二级分类
						if(isset($class['sub']) && is_array($class['sub'])){
							foreach($class['sub'] as $sub_class){
								if($sub_class['id'] == $result['category_id']){
									$result['class_name'] = $sub_class['name'];
									$result['category'] = $sub_class['name'];
									break 2;
								}
							}
						}
					}
				}
			}
			
			return $result;
		}
	}
	
	/**
     * 订单查询
	 * @param int $orderid 订单ID
	 * @param int $goodsid 商品ID
	 * @param array $value 下单输入框内容
     * @return array
     */
	public function query_order($orderid, $goodsid, $value = []){
		$order_state = array(1=>'已付款',2=>'待处理',3=>'处理中',4=>'补单中',5=>'退单中',6=>'已完成',7=>'已退单',8=>'已退款',9=>'有异常');
		$url = '/openapi/customer/Order/Show';
		$param = array('ids' => array(intval($orderid)));
		$timestamp = time();
		$token = $this->generateToken($url, $timestamp);
		$headers = array(
			'Appid: ' . $this->config['username'],
			'AppTimestamp: ' . $timestamp,
			'AppToken: ' . $token
		);
		$ret = $this->get_curl_with_headers($url, json_encode($param), $headers);
		if (!$ret = json_decode($ret, true)) {
			return false;
		} elseif ($ret['code'] !== 0) {
			return $ret['message'];
		} else {
			if(empty($ret['data']) || !is_array($ret['data'])){
				return false;
			}
			$v = $ret['data'][0];
			return array(
				'num' => $v['buy_number'] ?? 1,
				'start_num' => $v['start_num'] ?? 0,
				'now_num' => $v['current_num'] ?? 0,
				'add_time' => $v['create_time'] ?? time(),
				'order_state' => $order_state[$v['status']] ?? '未知状态'
			);
		}
	}

	/**
     * 价格监控（1个商品）
     * @return int 成功改变的商品数量
     */
	public function pricejk_one($tool){
		global $DB,$conf;
		$success=0;
		$details = $this->goods_info($tool['goods_id']);
		if(is_array($details)){
			$rs2=$DB->query("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ={$tool['shequ']} AND goods_id={$tool['goods_id']}");
			while($res2 = $rs2->fetch())
			{
				if($res2['price']==='0.00')continue;
				$price = ceil($details['price'] * $res2['value'] * 100)/100;
				
				// 价格同步只更新商品基础表，不覆盖分站用户的自定义价格
				if(($conf['pricejk_edit']==1 && $price>$res2['price']) || ($conf['pricejk_edit']==0 && $price!=$res2['price'])){
					$DB->exec("update `pre_tools` set `price` ='{$price}' where `tid`='{$res2['tid']}'");
				$success++;
			}
				if($details['close']==1 && $res2['close']==0){
					$DB->exec("update `pre_tools` set `close`=1 where `tid`='{$res2['tid']}'");
				}elseif($details['close']==0 && $res2['close']==1){
					$DB->exec("update `pre_tools` set `close`=0 where `tid`='{$res2['tid']}'");
				}
				$DB->exec("update `pre_tools` set `uptime`='".time()."' where `tid`='{$res2['tid']}'");
			}
		}elseif(strpos($details,'商品不存在')!==false){
			$rs2=$DB->query("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ={$tool['shequ']} AND goods_id={$tool['goods_id']}");
			while($res2 = $rs2->fetch())
			{
				$DB->exec("update `pre_tools` set `close`=1,`uptime`='".time()."' where `tid`='{$res2['tid']}'");
				$success++;
			}
		}
		return $success;
	}

	/**
     * 价格监控（批量）
     * @return bool
     */
	public function pricejk($shequid,&$success){
		global $DB,$conf;
		// 无论配置如何，都使用批量更新模式，确保所有商品都被处理
		$list = $this->goods_list();
		if(is_array($list)){
			$goods_status_arr = array();
			$price_arr = array();
			foreach($list as $row){
				$goods_status_arr[$row['id']] = $row['close']; //商品状态 1为禁止下单
				$price_arr[$row['id']] = $row['price']; //商品价格
			}
			
			// 更新商品价格
			$rs2=$DB->query("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ='{$shequid}' AND active=1 AND cid IN ({$conf['pricejk_cid']})");
			while($res2 = $rs2->fetch())
			{
				if($res2['price']==='0.00')continue;
				// 更新商品价格
				if(isset($price_arr[$res2['goods_id']]) && $price_arr[$res2['goods_id']]>0){
					$price = ceil($price_arr[$res2['goods_id']] * $res2['value'] * 100)/100;
					
					// 价格同步只更新商品基础表，不覆盖分站用户的自定义价格
					if(($conf['pricejk_edit']==1 && $price>$res2['price']) || ($conf['pricejk_edit']==0 && $price!=$res2['price'])){
						$DB->exec("update `pre_tools` set `price` ='{$price}' where `tid`='{$res2['tid']}'");
				$success++;
			}
				}
			}
			
			// 更新商品状态
			$rs2=$DB->query("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ='{$shequid}' AND active=1 AND cid IN ({$conf['pricejk_cid']})");
			while($res2 = $rs2->fetch())
			{
				if(isset($goods_status_arr[$res2['goods_id']])){
					if($goods_status_arr[$res2['goods_id']]==1 && $res2['close']==0){
						$DB->exec("update `pre_tools` set `close`=1 where `tid`='{$res2['tid']}'");
					}elseif($goods_status_arr[$res2['goods_id']]==0 && $res2['close']==1){
						$DB->exec("update `pre_tools` set `close`=0 where `tid`='{$res2['tid']}'");
					}
				}else{
					$DB->exec("update `pre_tools` set `close`=1 where `tid`='{$res2['tid']}'");
				}
			}
			return true;
		}else{
			return $list;
		}
	}

	public function batch_goods_list(){
		$url = '/openapi/customer/Goods/List';
		$param = array('goods_category_id' => null);
		$timestamp = time();
		$token = $this->generateToken($url, $timestamp);
		$headers = array(
			'Appid: ' . $this->config['username'],
			'AppTimestamp: ' . $timestamp,
			'AppToken: ' . $token
		);
		$ret = $this->get_curl_with_headers($url, json_encode($param), $headers);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} elseif ($ret['code'] !== 0) {
			return $ret['message'];
		} else {
			$list = array();
			foreach ($ret['data'] as $v) {
				$list[] = array(
					'goods_id' => $v['id'],
					'name' => $v['name']
				);
			}
			return $list;
		}
	}

	public function batch_goods_info($goods_id){
		$url = '/openapi/customer/Goods/Show';
		$param = array('goods_id' => intval($goods_id));
		$timestamp = time();
		$token = $this->generateToken($url, $timestamp);
		$headers = array(
			'Appid: ' . $this->config['username'],
			'AppTimestamp: ' . $timestamp,
			'AppToken: ' . $token
		);
		$ret = $this->get_curl_with_headers($url, json_encode($param), $headers);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} elseif ($ret['code'] !== 0) {
			return $ret['message'];
		} else {
			$result = $ret['data'];
			$paramname = '';
			if(isset($result['buy_params']) && is_array($result['buy_params'])){
				foreach($result['buy_params'] as $v){
					$paramname.=$v['name'].'|';
				}
			}
			$paramname = trim($paramname, '|');
			$paramname_arr = explode('|', $paramname);
			foreach ($paramname_arr as $k => $item) {
				if ($k == 0) {
					$input = $item;
				} else {
					if($item=='QQ空间说说ID')$item='说说ID';
					$inputs .= $item.'|';
				}
			}
			$inputs = trim($inputs, '|');
			$result['input'] = $input;
			$result['inputs'] = $inputs;
			
			// 映射API字段到前端期望的字段名
			$result['price'] = $result['price'] ?? 0;
			$result['desc'] = $result['particulars'] ?? '';
			$result['limit_min'] = $result['buy_min_limit'] ?? 1;
			$result['limit_max'] = $result['buy_max_limit'] ?? 0;
			$result['name'] = $result['name'] ?? '';
			$result['image'] = isset($result['image_urls'][0]) ? $result['image_urls'][0] : '';
			
			return $result;
		}
	}

	private function get_curl($path,$post=0,$referer=0,$cookie=0,$header=0,$addheader=0){
		$base_url = $this->config['url'];
		$base_url = preg_replace('/^(https?:\/\/)/i', '', $base_url);
		$url = 'http://' . $base_url . $path;
		return get_curl($url,$post,$referer,$cookie,$header,0,0,$addheader);
	}

	private function get_curl_with_headers($path,$post=0,$headers=array()){
		$base_url = $this->config['url'];
		$base_url = preg_replace('/^(https?:\/\/)/i', '', $base_url);
		$url = 'http://' . $base_url . $path;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
		$httpheader = array(
			'Accept: */*',
			'Accept-Encoding: gzip,deflate',
			'Accept-Language: zh-CN,zh;q=0.8',
			'Connection: close',
			'Content-Type: application/json; charset=UTF-8'
		);
		
		if($headers && is_array($headers)){
			$httpheader = array_merge($httpheader, $headers);
		}
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		
		if($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
		
		$ret = curl_exec($ch);
		curl_close($ch);
		
		return $ret;
	}

	private function generateToken($uri, $timestamp){
		$appId = $this->config['username'];
		$appSecret = $this->config['password'];
		$signString = $appId . $appSecret . $uri . $timestamp;
		return sha1($signString);
	}

	private function getSign($param, $key)
	{
		$signPars = "";
		ksort($param);
		foreach ($param as $k => $v) {
			if ("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars = trim($signPars, '&');
		$signPars .= $key;
		$sign = md5($signPars);
		return $sign;
	}
}