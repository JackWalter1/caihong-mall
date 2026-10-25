<?php
namespace plugins;

class third_kakayun_new{

	private $config = [];

	static public $info = [
		'name'        => 'third_kakayun_new',
		'type'        => 'third',
		'title'       => '卡卡云（新）',
		'author'      => '系统',
		'version'     => '1.0',
		'link'        => '',
		'sort'        => 28,
		'showedit'    => false,
		'showip'      => false,
		'pricejk'     => 1,
		'batchgoods'  => true,
		'input' => [
			'url' => 'API地址',
			'username' => '用户ID',
			'password' => '对接密钥',
			'paypwd' => false,
			'paytype' => false,
		],
	];

	public function __construct($config)
	{
		$this->config = $config;
	}

	private function getApiBase() {
		if (!empty($this->config['url'])) {
			$url = $this->config['url'];
			if (!preg_match('/^https?:\/\//', $url)) {
				$url = 'http://' . $url;
			}
			return rtrim($url, '/');
		}
		return 'http://public.kky.v3.api.kakayun.vip';
	}

	private function getSign($param, $token) {
		ksort($param);
		reset($param);
		$signtext = '';
		foreach ($param as $key => $val) {
			if ($val === '' || $key == 'sign' || is_null($val)) {
				continue;
			}
			if (is_bool($val)) {
				$val = $val ? 'true' : 'false';
			}
			if ($signtext) $signtext .= '&';
			if (is_array($val)) {
				$signtext .= "$key=" . json_encode($val, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			} else {
				$signtext .= "$key=$val";
			}
		}
		return md5($signtext . $token);
	}

	private function httpRequest($path, $data) {
		$url = $this->getApiBase() . $path;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json;charset=utf-8'
		]);
		
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}

	public function goods_list() {
		$param = [
			'userid' => $this->config['username'],
			'timestamp' => time(),
			'page' => 1,
			'limit' => 50
		];
		$param['sign'] = $this->getSign($param, $this->config['password']);
		
		$data = $this->httpRequest('/dockapiv3/goods/all', $param);
		$json = json_decode($data, true);
		
		if (isset($json['code']) && $json['code'] == 1 && isset($json['data'])) {
			$list = [];
			if (is_array($json['data'])) {
				foreach ($json['data'] as $row) {
					$list[] = [
						'id' => $row['goodsid'],
						'name' => $row['goodsname'],
						'shopimg' => $row['imgurl'],
						'price' => $row['goodsprice'],
						'status' => $row['goodsstatus'],
						'type' => $row['goodstype'],
						'alert' => '',
						'desc' => '',
						'min' => 1,
						'max' => $row['stock'],
						'stock' => $row['stock']
					];
				}
			}
			return $list;
		} elseif (isset($json['msg'])) {
			return '获取商品列表失败: ' . $json['msg'];
		} else {
			return '获取商品列表失败: 未知错误';
		}
	}

	public function goods_info($goods_id) {
		$param = [
			'userid' => $this->config['username'],
			'timestamp' => time(),
			'goodsid' => (string)$goods_id
		];
		$param['sign'] = $this->getSign($param, $this->config['password']);
		
		$data = $this->httpRequest('/dockapiv3/goods/detail', $param);
		$json = json_decode($data, true);
		
		if (isset($json['code']) && $json['code'] == 1 && isset($json['data'])) {
			$row = $json['data'];
			return [
				'id' => $row['goodsid'],
				'name' => $row['goodsname'],
				'shopimg' => $row['imgurl'],
				'price' => $row['goodsprice'],
				'status' => $row['goodsstatus'],
				'type' => $row['goodstype'],
				'alert' => '',
				'desc' => '',
				'min' => 1,
				'max' => $row['stock'],
				'stock' => $row['stock']
			];
		} else {
			return '获取商品详情失败';
		}
	}

	public function do_goods($goods_id, $goods_type, $goods_param, $buy_num, $buyer, $price = 0, $out_order_no = '', $remark = '') {
		if ($buy_num <= 0) {
			return [
				'code' => -1,
				'message' => '下单失败: 购买数量必须大于0'
			];
		}
		
		$param = [
			'userid' => $this->config['username'],
			'timestamp' => time(),
			'goodsid' => (string)$goods_id,
			'buynum' => (int)$buy_num
		];
		
		if (!empty($out_order_no)) {
			$param['usorderno'] = $out_order_no;
		}
		
		if (is_array($buyer)) {
			$buyer = trim($buyer[0] ?? '');
		}
		$buyer = trim((string)$buyer);
		
		if (!empty($buyer) && $buyer !== '0' && $buyer !== '1') {
			$param['attach'] = $buyer;
		}
		
		$param['sign'] = $this->getSign($param, $this->config['password']);
		
		$data = $this->httpRequest('/dockapiv3/order/create', $param);
		
		if (empty($data)) {
			return [
				'code' => -1,
				'message' => '下单失败: 接口返回为空'
			];
		}
		
		$json = json_decode($data, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			return [
				'code' => -1,
				'message' => '下单失败: 接口返回非JSON格式,原始数据: ' . substr($data, 0, 200)
			];
		}
		
		if (isset($json['code']) && $json['code'] == 1 && isset($json['data'])) {
			$orderno = $json['data']['orderno'];
			$result = [
				'code' => 0,
				'id' => $orderno
			];
			
			if (!empty($json['data']['cardlist']) || !empty($json['data']['cards'])) {
				$result['faka'] = true;
				$result['kmdata'] = [];
				
				if (!empty($json['data']['cards']) && is_array($json['data']['cards'])) {
					foreach ($json['data']['cards'] as $card) {
						$result['kmdata'][] = [
							'card' => $card['card_no'] ?? '',
							'pass' => $card['card_pwd'] ?? ''
						];
					}
				} elseif (!empty($json['data']['cardlist']) && is_array($json['data']['cardlist'])) {
					foreach ($json['data']['cardlist'] as $card) {
						$result['kmdata'][] = [
							'card' => $card,
							'pass' => ''
						];
					}
				}
			} else {
				usleep(500000);
				
				$query_result = $this->query_order($orderno);
				if (is_array($query_result) && (!empty($query_result['faka']) || !empty($query_result['kmdata']))) {
					$result['faka'] = true;
					$result['kmdata'] = $query_result['kmdata'] ?? [];
				}
			}
			
			return $result;
		} else {
			return [
				'code' => -1,
				'message' => '下单失败: ' . ($json['msg'] ?? '未知错误,原始数据: ' . substr($data, 0, 200))
			];
		}
	}

	public function query_order($order_id) {
		$param = [
			'userid' => $this->config['username'],
			'timestamp' => time(),
			'orderno' => $order_id
		];
		$param['sign'] = $this->getSign($param, $this->config['password']);
		
		$data = $this->httpRequest('/dockapiv3/order/get', $param);
		$json = json_decode($data, true);
		
		if (isset($json['code']) && $json['code'] == 1 && isset($json['data'])) {
			$row = $json['data'];
			switch ($row['status']) {
				case 2: $status = '未付款'; break;
				case 3: $status = '进行中'; break;
				case 4: $status = '失败'; break;
				case 5: $status = '成功'; break;
				default: $status = '未知'; break;
			}
			
			$result = '';
			if (!empty($row['receipt'])) {
				$result = $row['receipt'];
			} elseif (!empty($row['cardlist']) && is_array($row['cardlist'])) {
				foreach ($row['cardlist'] as $card) {
					$result .= $card . '<br/>';
				}
			} elseif (!empty($row['cards']) && is_array($row['cards'])) {
				foreach ($row['cards'] as $card) {
					if (!empty($card['card_no'])) {
						$result .= '卡号：' . $card['card_no'];
						if (!empty($card['card_pwd'])) {
							$result .= ' 密码：' . $card['card_pwd'];
						}
						if (!empty($card['card_denomination'])) {
							$result .= ' 面额：' . $card['card_denomination'];
						}
						$result .= '<br/>';
					}
				}
			}
			
			return [
				'order_state' => $status,
				'订单状态' => $status,
				'order' => $row['orderno'],
				'result' => $result,
				'faka' => !empty($row['cardlist']) || !empty($row['cards']),
				'kmdata' => !empty($row['cards']) ? array_map(function($card) {
					return [
						'card' => $card['card_no'] ?? '',
						'pass' => $card['card_pwd'] ?? ''
					];
				}, $row['cards']) : []
			];
		} else {
			return '查询订单失败: ' . ($json['msg'] ?? '未知错误');
		}
	}

	public function batch_goods_list() {
		return $this->goods_list();
	}

	public function pricejk($tool) {
		$param = [
			'userid' => $this->config['username'],
			'timestamp' => time(),
			'page' => 1,
			'limit' => 50
		];
		$param['sign'] = $this->getSign($param, $this->config['password']);
		
		$data = $this->httpRequest('/dockapiv3/goods/all', $param);
		$json = json_decode($data, true);
		
		if (isset($json['code']) && $json['code'] == 1 && isset($json['data'])) {
			$goods = [];
			foreach ($json['data'] as $row) {
				$goods[$row['goodsid']] = [
					'price' => $row['goodsprice'],
					'stock' => $row['stock']
				];
			}
			foreach ($tool as $k => $v) {
				if (isset($goods[$v['goods_id']])) {
					if ($goods[$v['goods_id']]['price'] != $v['price']) {
						// 更新价格
					}
					if ($goods[$v['goods_id']]['stock'] != $v['stock']) {
						// 更新库存
					}
				}
			}
			return true;
		} else {
			return false;
		}
	}

	// 缓存和频率限制
	private static $cache = [];
	private static $lastRequestTime = [];
	
	// 检查请求频率
	private function checkRequestRate($key, $minInterval) {
		$now = time();
		if (isset(self::$lastRequestTime[$key]) && ($now - self::$lastRequestTime[$key]) < $minInterval) {
			return false;
		}
		self::$lastRequestTime[$key] = $now;
		return true;
	}
	
	// 获取缓存数据
	private function getCache($key, $expire = 300) {
		if (isset(self::$cache[$key]) && (time() - self::$cache[$key]['time']) < $expire) {
			return self::$cache[$key]['data'];
		}
		return false;
	}
	
	// 设置缓存数据
	private function setCache($key, $data) {
		self::$cache[$key] = [
			'data' => $data,
			'time' => time()
		];
	}

	public function class_list() {
		// 检查缓存
		$cacheKey = 'class_list_' . $this->config['username'];
		$cachedData = $this->getCache($cacheKey);
		if ($cachedData) {
			return $cachedData;
		}
		
		// 检查请求频率（2秒1次）
		if (!$this->checkRequestRate('group', 2)) {
			return '获取分类列表失败: 请求过于频繁，请稍后重试';
		}
		
		$param = [
			'userid' => $this->config['username'],
			'timestamp' => time()
		];
		$param['sign'] = $this->getSign($param, $this->config['password']);
		
		$data = $this->httpRequest('/dockapiv3/goods/group', $param);
		$json = json_decode($data, true);
		
		if (isset($json['code']) && $json['code'] == 1 && isset($json['data'])) {
			$categories = [];
			foreach ($json['data'] as $row) {
				$categories[] = [
					'cid' => $row['groupid'],
					'name' => $row['groupname']
				];
			}
			
			// 设置缓存
			$this->setCache($cacheKey, $categories);
			return $categories;
		} elseif (isset($json['msg'])) {
			return '获取分类列表失败: ' . $json['msg'];
		} else {
			return '获取分类列表失败: 未知错误';
		}
	}

	public function goods_list_by_cid($cid) {
		// 检查请求频率（5秒1次）
		if (!$this->checkRequestRate('goods', 5)) {
			return '获取商品列表失败: 请求过于频繁，请稍后重试';
		}
		
		$param = [
			'userid' => $this->config['username'],
			'timestamp' => time(),
			'page' => 1,
			'limit' => 50,
			'groupid' => $cid
		];
		$param['sign'] = $this->getSign($param, $this->config['password']);
		
		$data = $this->httpRequest('/dockapiv3/goods/all', $param);
		$json = json_decode($data, true);
		
		if (isset($json['code']) && $json['code'] == 1 && isset($json['data'])) {
			$list = [];
			$categoryName = '';
			
			foreach ($json['data'] as $row) {
				$list[] = [
					'tid' => $row['goodsid'],
					'name' => $row['goodsname'],
					'shopimg' => $row['imgurl'],
					'price' => $row['goodsprice'],
					'close' => $row['goodsstatus'] == 1 ? 0 : 1,
					'minnum' => 1,
					'maxnum' => $row['stock'],
					'min' => 1,
					'max' => $row['stock'],
					'stock' => $row['stock'],
					'alert' => '',
					'desc' => '',
					'input' => '',
					'inputs' => '',
					'multi' => 1,
					'repeat' => 1,
					'validate' => 0,
					'valiserv' => '',
					'isfaka' => $row['goodstype'] == 1 ? true : false,
					'original_cid' => $cid,
					'original_cname' => $row['groupname'] ?? '未分类'
				];
			}
			
			return $list;
		} elseif (isset($json['msg'])) {
			return '获取商品列表失败: ' . $json['msg'];
		} else {
			return '获取商品列表失败: 未知错误';
		}
	}
}
