<?php
namespace plugins;

class third_zhike{

	private $config = [];
	
	// 并发请求最大数量，增加到50以提高首次加载速度
	private $max_concurrent_requests = 50;
	
	// 缓存过期时间（秒）
	private $cache_expire = 1800;
	
	// 商品详情缓存过期时间（秒）
	private $goods_detail_cache_expire = 3600;
	
	// 列表API返回的字段映射
	private $list_field_map = [
		'goodsSN' => 'id',
		'goodsName' => 'name',
		'goodsThumb' => 'shopimg',
		// 如果列表API包含价格等信息，可以添加映射
	];
	
	// 详情API返回的字段映射
	private $detail_field_map = [
		'goodsPrice' => 'price',
		'minOrderNum' => 'minnum',
		'maxOrderNum' => 'maxnum',
		'isClose' => 'close',
	];
	
	// 批量获取商品详情的最大批次大小
	private $max_batch_size = 50;

	static public $info = [
		'name'        => 'third_zhike',
		'type'        => 'third',
		'title'       => '直客SUP',
		'author'      => '6v6',
		'version'     => '1.0',
		'link'        => '',
		'sort'        => 24,
		'showedit'    => false,
		'showip'      => true,
		'batchgoods'    => true,
		'pricejk'     => 2,
		'input' => [
			'url' => '网站域名',
			'username' => '应用ID',
			'password' => '应用密钥',
			'paypwd' => false,
			'paytype' => false,
		],
	];

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function do_goods($goods_id, $goods_type, $goods_param, $num = 1, $input = array(), $money, $tradeno, $inputsname)
	{
		$result['code'] = -1;
		
		// 解析goods_param
		$goodsid = $goods_id;
		$params = [];
		
		if(!empty($goods_param)){
			$array = explode('#', $goods_param);
			if(!empty($array[0])){
				$goodsid = $array[0];
			}
			
			// 如果有alias部分，使用它
			if(isset($array[1]) && !empty($array[1])){
				$aliases = explode('|', $array[1]);
				foreach($aliases as $i => $alias){
					if(isset($input[$i]) && $input[$i] !== ''){
						$params[] = ['alias' => trim($alias), 'value' => $input[$i]];
					}
				}
			}
		}
		
		// 如果没有从goods_param获取到参数，尝试使用inputsname
		if(empty($params) && !empty($inputsname)){
			$input_names = explode('|', $inputsname);
			foreach($input_names as $i => $name){
				if(isset($input[$i]) && $input[$i] !== ''){
					$params[] = ['alias' => trim($name), 'value' => $input[$i]];
				}
			}
		}
		
		// 如果还是没有参数，使用默认的wb
		if(empty($params)){
			$params[] = ['alias' => 'wb', 'value' => isset($input[0]) ? $input[0] : ''];
		}
		
		if(!$goodsid){
			$result['message'] = '商品ID不能为空';
			return $result;
		}

		$path = '/api/client/goods/v2/order';
		$body = ['goodsSN' => $goodsid, 'buyNotify' => -1, 'number' => $num, 'params' => $params];
		$post = json_encode($body);
		
		$data = $this->get_curl($path, $post);
		$json = json_decode($data, true);
		
		if(isset($json['code']) && $json['code'] == 100){
			$result = array(
				'code' => 0,
				'id' => $json['result']['orderSN']
			);
		}elseif(isset($json['msg'])){
			$result['message'] = $json['msg'];
		}else{
			$result['message'] = $data;
		}
		
		return $result;
	}

	public function goods_list(){
		// 生成缓存键名
		$cache_key = 'goods_list_' . $this->config['url'];
		
		// 检查缓存
		$cached_list = $this->get_cache($cache_key);
		if ($cached_list) {
			return $cached_list;
		}
		
		$path = '/api/client/goods/v2/goods/list';
		$ret = $this->get_curl($path);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} else if ($ret['code'] != 100) {
			return $ret['msg'];
		} else {
			$list = array();
			$goodsSN_list = array();
			$goods_temp = array();
			
			// 先收集所有商品基本信息
			foreach ($ret['result']['data'] as $v) {
				// 构建临时商品信息
				$temp_item = array(
					'id' => $v['goodsSN'],
					'name' => $v['goodsName'],
					'shopimg' => $v['goodsThumb'],
				);
				
				// 如果列表API已经包含价格等信息，可以直接使用
				// 这里先初始化需要从详情API获取的字段
				$temp_item['price'] = 0;
				$temp_item['minnum'] = 0;
				$temp_item['maxnum'] = 0;
				$temp_item['close'] = false;
				
				$goods_temp[$v['goodsSN']] = $temp_item;
				$goodsSN_list[] = $v['goodsSN'];
			}
			
			// 并发获取所有商品详情
			$goods_details = $this->get_goods_details_concurrently($goodsSN_list);
			
			// 合并基本信息和详情信息
			foreach ($goodsSN_list as $goodsSN) {
				if (isset($goods_temp[$goodsSN])) {
					$item = $goods_temp[$goodsSN];
					
					// 如果有详情信息，合并到商品信息中
					if (isset($goods_details[$goodsSN])) {
						$goods_info = $goods_details[$goodsSN];
						$item['price'] = $goods_info['goodsPrice'];
						$item['minnum'] = $goods_info['minOrderNum'];
						$item['maxnum'] = $goods_info['maxOrderNum'];
						$item['close'] = $goods_info['isClose'];
						// 检查是否包含分类信息
						if (isset($goods_info['categoryId'])) {
							$item['category'] = $goods_info['categoryId'];
							$item['category_name'] = $goods_info['categoryName'] ?? $goods_info['categoryId'];
						}
					}
					
					$list[] = $item;
				}
			}
			
			// 设置缓存
			$this->set_cache($cache_key, $list);
			
			return $list;
		}
	}

	public function goods_info($goods_id){
		$goodsSN = isset($_POST['goods_param'])?trim($_POST['goods_param']):$goods_id;
		
		// 检查商品详情缓存
		$cache_key = 'goods_detail_full_' . $this->config['url'] . '_' . $goodsSN;
		$cached_info = $this->get_cache($cache_key, $this->goods_detail_cache_expire);
		if ($cached_info) {
			return $cached_info;
		}
		
		$path = '/api/client/goods/v2/goods?goodsSN='.$goodsSN;
		$data = $this->get_curl($path);
		if (!$ret = json_decode($data, true)) {
			return '打开对接网站失败';
		} elseif ($ret['code'] == 100) {
			if($ret['result']['goodsThumb']&&substr($ret['result']['goodsThumb'],0,4)!='http')$ret['result']['goodsThumb'] = 'http://'.$this->config['url'].$ret['result']['goodsThumb'];
			$return = [
				'id' => $ret['result']['goodsSN'],
				'name' => $ret['result']['goodsName'],
				'shopimg' => $ret['result']['goodsThumb'],
				'unit' => $ret['result']['goodsUnit'],
				'desc' => $ret['result']['goodsDetail'],
				'min' => $ret['result']['minOrderNum'],
				'max' => $ret['result']['maxOrderNum'],
				'price' => $ret['result']['goodsPrice'],
				'type' => $ret['result']['goodsType'],
				'unitnum' => $ret['result']['preUnitNum'],
				'close' => $ret['result']['isClose'],
			];
			
			// 添加分类信息
			if (isset($ret['result']['categoryId'])) {
				$return['category'] = $ret['result']['categoryId'];
				$return['category_name'] = $ret['result']['categoryName'] ?? $ret['result']['categoryId'];
			}
			$return['input'] = str_replace('：','',$ret['result']['paramsTemplate'][0]['name']);
			$inputs = '';
			$alias = '';
			foreach($ret['result']['paramsTemplate'] as $row){
				$alias .= $row['alias'].'|';
				if(str_replace('：','',$row['name']) == $return['input'])continue;
				$inputs .= str_replace('：','',$row['name']).'|';
			}
			$alias = trim($alias,'|');
			$return['inputs'] = trim($inputs,'|');
			$return['alias'] = $alias;
			$return['goods_param'] = $ret['result']['goodsSN'].'#'.$alias;
			
			// 将完整的商品信息存入缓存
			$this->set_cache($cache_key, $return);
			
			return $return;
		} else {
			return $ret['msg'];
		}
	}
	
	public function query_order($orderid, $goodsid, $value = []){
		$order_state = array(0=>'待处理',1=>'已付款',2=>'处理中',3=>'待确认',4=>'已完成',5=>'退单中',6=>'已退单',7=>'已退款',8=>'待处理');
		$path = '/api/client/goods/v2/order?orderSN='.$orderid;
		$data = $this->get_curl($path);
		if (!$ret = json_decode($data, true)) {
			return false;
		} elseif ($ret['code'] == 100) {
			$return = ['订单状态'=>$order_state[$ret['result']['orderState']]];
			if($ret['result']['startNum']>0 || $ret['result']['currentNum']>0){
				$return['开始数量'] = $ret['result']['startNum'];
				$return['当前数量'] = $ret['result']['currentNum'];
				$return['完成数量'] = $ret['result']['finishTotal'];
			}
			if(!empty($ret['result']['cardNumber'])){
				$return['卡密信息'] = implode('<br/>',explode(',',$ret['result']['cardNumber']));
			}
			return $return;
		} else{
			return $ret['msg'];
		}
	}

	public function pricejk_one($tool){
		global $DB,$conf;
		$success=0;
		$array = explode('#',$tool['goods_param']);
		$goodsid = $array[0];
		if(!$goodsid)return;
		$details = $this->goods_info($goodsid);
		if(is_array($details)){
			$rs2=$DB->query("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ={$tool['shequ']} AND goods_param='{$tool['goods_param']}'");
			while($res2 = $rs2->fetch())
			{
				if($res2['price']==='0.00')continue;
				$price = ceil($details['price'] * $res2['value'] * 100)/100;
				if($conf['pricejk_edit']==1 && $price>$res2['price'] && $res2['prid']>0){
					$DB->exec("update `pre_tools` set `price` ='{$price}' where `tid`='{$res2['tid']}'");
					$success++;
				}elseif($conf['pricejk_edit']==0 && $price!=$res2['price'] && $res2['prid']>0){
					$DB->exec("update `pre_tools` set `price` ='{$price}' where `tid`='{$res2['tid']}'");
					$success++;
				}
				if($details['close']==true && $res2['close']==0){
					$DB->exec("update `pre_tools` set `close`=1 where `tid`='{$res2['tid']}'");
				}elseif($details['close']==false && $res2['close']==1){
					$DB->exec("update `pre_tools` set `close`=0 where `tid`='{$res2['tid']}'");
				}
				$DB->exec("update `pre_tools` set `uptime`='".time()."' where `tid`='{$res2['tid']}'");
			}
		}elseif(strpos($details,'商品不存在')!==false){
			$rs2=$DB->query("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ={$tool['shequ']} AND goods_param='{$tool['goods_param']}'");
			while($res2 = $rs2->fetch())
			{
				$DB->exec("update `pre_tools` set `close`=1,`uptime`='".time()."' where `tid`='{$res2['tid']}'");
				$success++;
			}
		}
		return $success;
	}

	public function pricejk($shequid,&$success){
		global $DB,$conf;
		$pricejk_time = $conf['pricejk_time']?$conf['pricejk_time']:600;
		for($i=0;$i<10;$i++){
			$tool=$DB->getRow("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ='{$shequid}' AND active=1 AND cid IN ({$conf['pricejk_cid']}) AND uptime<'".(time()-$pricejk_time)."' ORDER BY uptime ASC");
			if(!$tool)break;
			$count = $this->pricejk_one($tool);
			$success+=$count;
		}
		return true;
	}

	public function batch_goods_list(){
		// 生成缓存键名
		$cache_key = 'batch_goods_list_' . $this->config['url'];
		
		// 检查缓存
		$cached_list = $this->get_cache($cache_key);
		if ($cached_list) {
			return $cached_list;
		}
		
		$path = '/api/client/goods/v2/goods/list';
		$ret = $this->get_curl($path);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} else if ($ret['code'] != 100) {
			return $ret['msg'];
		} else {
			$list = array();
			$goodsSN_list = array();
			
			// 收集所有需要获取详情的商品ID
			foreach ($ret['result']['data'] as $v) {
				$goodsSN_list[] = $v['goodsSN'];
			}
			
			// 并发获取所有商品详情
			$goods_details = $this->get_goods_details_concurrently($goodsSN_list);
			
			// 构建结果列表
			foreach ($ret['result']['data'] as $v) {
				if (isset($goods_details[$v['goodsSN']])) {
					$goods_info = $goods_details[$v['goodsSN']];
					$list[] = array(
						'goods_id' => $v['goodsSN'],
						'name' => $v['goodsName'],
						'shopimg' => $v['goodsThumb'],
						'price' => $goods_info['goodsPrice'],
						'minnum' => $goods_info['minOrderNum'],
						'maxnum' => $goods_info['maxOrderNum'],
						'close' => $goods_info['isClose']
					);
				}
			}
			
			// 设置缓存
			$this->set_cache($cache_key, $list);
			
			return $list;
		}
	}
	
	/**
	 * 批量获取商品详情（标准插件系统接口）
	 * 用于自动同步功能
	 */
	public function goods_info_batch($goods_ids) {
		try {
			// 使用现有的并发获取商品详情方法
			$details = $this->get_goods_details_concurrently($goods_ids);
			// 转换键为id，确保与自动同步代码兼容
			$result = array();
			foreach($details as $goodsSN => $detail) {
				if(isset($detail['goodsSN'])) {
					$result[$detail['goodsSN']] = $detail;
				}
			}
			return $result;
		} catch (Exception $e) {
			return array();
		}
	}
	
	/**
	 * 获取分类列表（标准插件系统接口）
	 * 用于批量对接商品功能
	 */
	public function class_list() {
		try {
			// 尝试调用直客API的分类列表接口
			$path = '/api/client/goods/v2/category';
			$ret = $this->get_curl($path);
			
			if (!$ret = json_decode($ret, true)) {
				// API调用失败，尝试从商品列表中提取分类
				return $this->extractCategoriesFromGoods();
			} else if ($ret['code'] != 100) {
				// API返回错误，尝试从商品列表中提取分类
				return $this->extractCategoriesFromGoods();
			} else {
				// 成功获取分类列表
				$list = array();
				if (isset($ret['result']['data']) && is_array($ret['result']['data'])) {
					foreach ($ret['result']['data'] as $v) {
						$list[] = array(
							'cid' => $v['categoryId'] ?? $v['id'],
							'name' => $v['categoryName'] ?? $v['name']
						);
					}
				}
				
				// 如果没有获取到分类，返回默认分类
				if (empty($list)) {
					return array(
						array(
							'cid' => 1,
							'name' => '默认分类'
						)
					);
				}
				
				return $list;
			}
		} catch (Exception $e) {
			// 异常情况下，尝试从商品列表中提取分类
			return $this->extractCategoriesFromGoods();
		}
	}
	
	/**
	 * 从商品列表中提取分类信息
	 * @return array 分类列表
	 */
	private function extractCategoriesFromGoods() {
		try {
			// 获取商品列表
			$goods_list = $this->goods_list();
			if (!is_array($goods_list)) {
				// 如果获取商品列表失败，返回默认分类
				return array(
					array(
						'cid' => 1,
						'name' => '默认分类'
					)
				);
			}
			
			// 从商品信息中提取分类（如果有）
			$categories = array();
			$category_map = array();
			
			foreach ($goods_list as $goods) {
				// 检查商品是否包含分类信息
				if (isset($goods['category']) && !empty($goods['category'])) {
					$category_id = $goods['category'];
					$category_name = $goods['category_name'] ?? $goods['category'];
					
					if (!isset($category_map[$category_id])) {
						$category_map[$category_id] = $category_name;
						$categories[] = array(
							'cid' => $category_id,
							'name' => $category_name
						);
					}
				}
			}
			
			// 如果没有提取到分类，返回默认分类
			if (empty($categories)) {
				return array(
					array(
						'cid' => 1,
						'name' => '默认分类'
					)
				);
			}
			
			return $categories;
		} catch (Exception $e) {
			// 异常情况下，返回默认分类
			return array(
				array(
					'cid' => 1,
					'name' => '默认分类'
				)
			);
		}
	}

	/**
	 * 获取缓存数据
	 * @param string $key 缓存键名
	 * @param int $expire 过期时间（秒）
	 * @return mixed 缓存数据，失败返回false
	 */
	private function get_cache($key, $expire = null) {
		if ($expire === null) {
			$expire = $this->cache_expire;
		}
		
		$cache_dir = '../cache/';
		if (!is_dir($cache_dir)) {
			mkdir($cache_dir, 0777, true);
		}
		
		$cache_file = $cache_dir . 'zhike_' . md5($key) . '.cache';
		
		if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $expire) {
			return json_decode(file_get_contents($cache_file), true);
		}
		
		return false;
	}
	
	/**
	 * 设置缓存数据
	 * @param string $key 缓存键名
	 * @param mixed $data 缓存数据
	 * @return bool 是否成功
	 */
	private function set_cache($key, $data) {
		$cache_dir = '../cache/';
		if (!is_dir($cache_dir)) {
			mkdir($cache_dir, 0777, true);
		}
		
		$cache_file = $cache_dir . 'zhike_' . md5($key) . '.cache';
		
		return file_put_contents($cache_file, json_encode($data));
	}
	
	/**
	 * 清除缓存数据
	 * @param string $key 缓存键名
	 * @return bool 是否成功
	 */
	private function clear_cache($key) {
		$cache_dir = '../cache/';
		$cache_file = $cache_dir . 'zhike_' . md5($key) . '.cache';
		
		if (file_exists($cache_file)) {
			return unlink($cache_file);
		}
		
		return true;
	}
	
	/**
	 * 并发获取商品详情
	 * @param array $goodsSN_list 商品ID列表
	 * @return array 商品详情数组
	 */	
	private function get_goods_details_concurrently($goodsSN_list) {
		// 结果数组
		$results = array();
		// 需要从API获取的商品ID列表
		$need_to_fetch = array();
		
		// 先检查缓存，获取已经缓存的商品详情
		foreach ($goodsSN_list as $goodsSN) {
			$cache_key = 'goods_detail_' . $this->config['url'] . '_' . $goodsSN;
			$cached_detail = $this->get_cache($cache_key, $this->goods_detail_cache_expire);
			if ($cached_detail) {
				$results[$goodsSN] = $cached_detail;
			} else {
				$need_to_fetch[] = $goodsSN;
			}
		}
		
		// 如果所有商品详情都在缓存中，直接返回
		if (empty($need_to_fetch)) {
			return $results;
		}
		
		// 将需要获取的商品ID列表分成多个批次，使用更大的批次大小提高效率
		$batches = array_chunk($need_to_fetch, $this->max_batch_size);
		
		// 逐个批次处理
		foreach ($batches as $batch) {
			$mh = curl_multi_init();
			$curl_handles = array();
			$goodsSN_map = array();
			
			// 预先生成所有需要的curl请求
			foreach ($batch as $goodsSN) {
				$path = '/api/client/goods/v2/goods?goodsSN='.$goodsSN;
				$url = ($this->config['protocol']==1?'https://':'http://') . $this->config['url'] . $path;
				$time = time();
				$token = sha1($this->config['username'].$this->config['password'].$path.$time);
				$header = ['AppId: '.$this->config['username'], 'AppToken: '.$token, 'AppTimestamp: '.$time];
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 15); // 进一步减少超时时间
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0); // 启用HTTP/2
				curl_setopt($ch, CURLOPT_TCP_NODELAY, true); // 禁用Nagle算法
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // 减少连接超时时间到2秒
				curl_setopt($ch, CURLOPT_FORBID_REUSE, false); // 允许连接复用
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, false); // 尝试使用现有连接
				curl_setopt($ch, CURLOPT_NOSIGNAL, true); // 忽略信号，提高多线程性能
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36');
				
				curl_multi_add_handle($mh, $ch);
				$curl_handles[] = $ch; // 保存curl资源
				$goodsSN_map[(string)$ch] = $goodsSN; // 建立资源与商品ID的映射
			}
			
			// 执行并发请求，使用更高效的循环方式
			$running = null;
			do {
				$status = curl_multi_exec($mh, $running);
				if ($status != CURLM_CALL_MULTI_PERFORM) {
					// 等待活动连接，使用超时参数提高性能
					curl_multi_select($mh, 0.05);
				}
			} while ($running > 0 && $status == CURLM_OK);
			
			// 处理结果
			foreach ($curl_handles as $ch) {
				$response = curl_multi_getcontent($ch);
				$goodsSN = $goodsSN_map[(string)$ch];
				
				if ($response) {
					$ret = json_decode($response, true);
					if ($ret && $ret['code'] == 100) {
						// 处理商品详情，确保包含price字段
						$goods_detail = $ret['result'];
						// 添加price字段，与goods_info方法保持一致
						if (isset($goods_detail['goodsPrice'])) {
							$goods_detail['price'] = $goods_detail['goodsPrice'];
						}
						// 添加min和max字段
						if (isset($goods_detail['minOrderNum'])) {
							$goods_detail['min'] = $goods_detail['minOrderNum'];
							$goods_detail['limit_min'] = $goods_detail['minOrderNum'];
						}
						if (isset($goods_detail['maxOrderNum'])) {
							$goods_detail['max'] = $goods_detail['maxOrderNum'];
							$goods_detail['limit_max'] = $goods_detail['maxOrderNum'];
						}
						// 添加value字段（默认值）
						if (!isset($goods_detail['value'])) {
							$goods_detail['value'] = isset($goods_detail['minOrderNum']) ? $goods_detail['minOrderNum'] : 1;
						}
						
						// 处理参数模板，构建goods_param字段
						if (isset($goods_detail['paramsTemplate']) && is_array($goods_detail['paramsTemplate'])) {
							$alias = '';
							$input = '';
							$inputs = '';
							foreach($goods_detail['paramsTemplate'] as $row){
								$alias .= $row['alias'].'|';
								if(empty($input)){
									$input = str_replace('：','',$row['name']);
								} else {
									$inputs .= str_replace('：','',$row['name']).'|';
								}
							}
							$alias = trim($alias,'|');
							$inputs = trim($inputs,'|');
							$goods_detail['alias'] = $alias;
							$goods_detail['input'] = $input;
							$goods_detail['inputs'] = $inputs;
							$goods_detail['goods_param'] = $goods_detail['goodsSN'].'#'.$alias;
						} else {
							// 如果没有参数模板，使用默认的goods_param
							$goods_detail['goods_param'] = $goods_detail['goodsSN'];
							$goods_detail['input'] = '';
							$goods_detail['inputs'] = '';
						}
						
						$results[$goodsSN] = $goods_detail;
						// 将商品详情存入缓存
						$cache_key = 'goods_detail_' . $this->config['url'] . '_' . $goodsSN;
						$this->set_cache($cache_key, $goods_detail);
					}
				}
				
				// 清理资源
				curl_multi_remove_handle($mh, $ch);
				curl_close($ch);
			}
			
			curl_multi_close($mh);
		}
		
		return $results;
	}
	
	private function get_curl($path,$post=0){
		$url = ($this->config['protocol']==1?'https://':'http://') . $this->config['url'] . $path;
		$time = time();
		$token = sha1($this->config['username'].$this->config['password'].$path.$time);
		$header = ['AppId: '.$this->config['username'], 'AppToken: '.$token, 'AppTimestamp: '.$time];
		if($post){
			$header[] = 'Content-Type: application/json; charset=UTF-8';
		}
		// 添加keep-alive头保持连接
		$header[] = 'Connection: keep-alive';
		return \shequ_get_curl($url,$post,0,0,0,$header);
	}
}