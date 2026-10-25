<?php
namespace plugins;

class third_shiduo{

	private $config = [];

	static public $info = [
		'name'        => 'third_shiduo',
		'type'        => 'third',
		'title'       => '视多发卡',
		'author'      => '6v6',
		'version'     => '1.0.3',
		'link'        => 'https://www.showdoc.com.cn/sdfaka?page_id=6260510885022608',
		'sort'        => 33,
		'showedit'    => false,
		'showip'      => false,
		'pricejk'     => 2,
		'batchgoods'  => true,
		'input' => [
			'url' => '网站域名',
			'username' => 'APPID',
			'password' => 'APPKEY',
			'paypwd' => false,
			'paytype' => false,
		],
	];

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function do_goods($goods_id, $goods_type, $goods_param, $num, $input, $money, $tradeno, $inputsname)
	{
		global $siteurl;
		$siteurl = str_replace('/admin/','/',$siteurl);
		$siteurl = str_replace('/other/','/',$siteurl);

		$result['code'] = -1;
		$url = '/api/pay';
		$remote_goods = $this->getRemoteGoodsInfo($goods_id);
		if(is_array($remote_goods)){
			$remote_id = $this->pickValue($remote_goods, ['gid','goods_id','goodsid','good_id','goodid','product_id','productid','id']);
			if($remote_id !== '' && $remote_id !== null)$goods_id = $remote_id;
			if(isset($remote_goods['type'])){
				$goods_type = $remote_goods['type'] == 'k' ? 1 : 0;
			}elseif(isset($remote_goods['typestr'])){
				$goods_type = strpos($remote_goods['typestr'], '卡密') !== false ? 1 : 0;
			}
		}else{
			$goods_type = intval($goods_type);
		}
		$num = intval($num);
		if($num <= 0)$num = 1;
		if($tradeno === null || $tradeno === ''){
			$tradeno = $this->makeTradeNo();
		}
		$param = [
			'appid' => strval($this->config['username']),
			'gid' => strval($goods_id),
			'count' => strval($num),
			'out_trade_no' => strval($tradeno)
		];
		if($goods_type==0){
			$param['code'] = '1';
			$handinfo = $this->buildHandInfo($input, $inputsname);
			if(count($handinfo)==0){
				$result['message'] = '充值商品下单参数不能为空';
				return $result;
			}
			$param['handinfo'] = base64_encode(json_encode($handinfo, JSON_UNESCAPED_UNICODE));
			$param['notify'] = $siteurl.'other/notify.php/shequ/'.$this->config['id'].'/'.$tradeno.'/';
		}
		$param['sign'] = $this->getPaySign($goods_id, $num, $tradeno);
		$data = $this->get_curl($url, http_build_query($param));
		$json = json_decode($data, true);
		if(isset($json['code']) && $json['code']==1){
			$result = array(
				'code' => 0,
				'id' => isset($json['pay_no']) ? $json['pay_no'] : $tradeno
			);
			$kmdata = $this->formatCards(isset($json['kaData']) ? $json['kaData'] : []);
			if($goods_type==1 && count($kmdata)>0){
				$result['faka'] = true;
				$result['kmdata'] = $kmdata;
			}elseif($goods_type==1){
				$query_result = $this->query_order($result['id'], $goods_id);
				if(is_array($query_result) && !empty($query_result['kmdata'])){
					$result['faka'] = true;
					$result['kmdata'] = $query_result['kmdata'];
				}
			}
		}elseif(isset($json['msg'])){
			$result['message'] = $json['msg'];
			if(isset($json['status']))$result['message'] = '['.$json['status'].'] '.$result['message'];
			$result['message'] .= '（gid='.$param['gid'].' count='.$param['count'].' out_trade_no='.$param['out_trade_no'].' type='.$goods_type.' sign='.substr($param['sign'],0,8).'）';
		}else{
			$result['message'] = $data;
		}
		return $result;
	}

	public function goods_info($goods_id){
		$ret = $this->getRemoteGoodsInfo($goods_id, true);
		if(!is_array($ret))return $ret;
		$goods = $this->formatGoods($ret);
		$stock = $this->goods_stock($goods['id']);
		if($stock !== false){
			$goods['stock'] = $stock;
			$goods['max'] = $stock > 0 ? $stock : 9999;
		}
		return $this->publicGoods($goods) + [
			'gid' => $goods['id'],
			'image' => $goods['shopimg'],
			'isfaka' => $goods['isfaka'] ? 1 : 0,
			'minnum' => $goods['min'],
			'maxnum' => $goods['max'],
			'multi' => 1,
			'repeat' => 1
		];
	}

	public function goods_list(){
		$rows = $this->getGoodsRows();
		if(!is_array($rows))return $rows;
		$list = [];
		foreach($rows as $row){
			$goods = $this->formatGoods($row);
			if(!$goods['id'])continue;
			$list[] = $this->publicGoods($goods);
		}
		return $list;
	}

	public function batch_goods_list(){
		$rows = $this->getGoodsRows();
		if(!is_array($rows))return $rows;
		$list = [];
		foreach($rows as $row){
			$goods = $this->formatGoods($row);
			if(!$goods['id'])continue;
			$list[] = $this->batchGoods($goods);
		}
		return $list;
	}

	public function class_list(){
		$rows = $this->getGoodsRows();
		if(!is_array($rows))return $rows;
		$class = [];
		foreach($rows as $row){
			$goods = $this->formatGoods($row);
			if(!$goods['id'])continue;
			$class[$goods['cid']] = [
				'cid' => $goods['cid'],
				'name' => $goods['cname']
			];
		}
		return array_values($class);
	}

	public function goods_list_by_cid($cid){
		$rows = $this->getGoodsRows();
		if(!is_array($rows))return $rows;
		$list = [];
		foreach($rows as $row){
			$goods = $this->formatGoods($row);
			if(!$goods['id'])continue;
			if($goods['cid'] != $cid)continue;
			$list[] = $this->batchGoods($goods);
		}
		return $list;
	}

	public function query_order($orderid, $goodsid = 0, $value = []){
		$url = '/api/get_card';
		$param = [
			'order_id' => $orderid,
			'app_id' => $this->config['username'],
			'sign' => $this->getCardSign($orderid)
		];
		$data = $this->get_curl($url, http_build_query($param));
		if(!$ret = json_decode($data, true)){
			return false;
		}elseif(isset($ret['code']) && $ret['code']==1){
			$kmdata = $this->formatCards(isset($ret['card']) ? $ret['card'] : []);
			$result = ['订单状态'=>'已完成'];
			if(count($kmdata)>0){
				$cards = '';
				foreach($kmdata as $row){
					$cards .= $row['card'].'<br/>';
				}
				$result['卡密信息'] = $cards;
				$result['faka'] = true;
				$result['kmdata'] = $kmdata;
			}
			return $result;
		}elseif(isset($ret['msg'])){
			return $ret['msg'];
		}
		return false;
	}

	public function notify($order){
		global $DB;
		$arr = $_POST;
		if(!isset($arr['order'], $arr['token']))exit('data error');
		if($arr['token'] != md5($this->config['username'].$this->config['password'].$arr['order']))exit('sign error');
		if($arr['order'] == $order['tradeno'] && $order['status']<=2){
			if($arr['type']==2){
				$DB->exec("update pre_orders set status=1 where id='{$order['id']}'");
			}elseif($arr['type']==1){
				$DB->exec("update pre_orders set status=3 where id='{$order['id']}'");
			}
			if(isset($arr['msg']) && $arr['msg']){
				$DB->exec("update pre_orders set result=:result where id=:id", [':result'=>$arr['msg'], ':id'=>$order['id']]);
			}
		}
		echo 'ok';
		return true;
	}

	public function goods_stock($goods_id){
		$url = '/api/getKucun';
		$param = ['gid' => $goods_id];
		$data = $this->get_curl($url, http_build_query($param));
		if($ret = json_decode($data, true)){
			if(isset($ret['code']) && $ret['code']==1){
				return isset($ret['kucun']) ? intval($ret['kucun']) : 0;
			}
		}
		return false;
	}

	public function pre_check($tool, $num){
		global $DB;
		$stock = $this->goods_stock($tool['goods_id']);
		if($stock!==false && $stock!==null){
			if($stock!=$tool['stock']){
				$DB->exec("update `pre_tools` set `stock`=:stock,`uptime`='".time()."' where `tid`='{$tool['tid']}'", [':stock'=>$stock]);
			}
			if($tool['goods_type']==1 && $num>$stock){
				return ['code' => -1, 'msg'=>'当前商品库存不足，无法购买'];
			}
		}else{
			$DB->exec("update `pre_tools` set `uptime`='".time()."' where `tid`='{$tool['tid']}'");
		}
		return ['code' => 0];
	}

	public function pricejk_one($tool){
		global $DB,$conf;
		$success = 0;
		$details = $this->goods_info($tool['goods_id']);
		if(is_array($details)){
			$rs2 = $DB->query("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ={$tool['shequ']} AND goods_id={$tool['goods_id']}");
			while($res2 = $rs2->fetch()){
				if($res2['price']==='0.00')continue;
				$price = ceil($details['price'] * $res2['value'] * 100)/100;
				if($conf['pricejk_edit']==1 && $price>$res2['price'] && $res2['prid']>0){
					$DB->exec("update `pre_tools` set `price`=:price where `tid`='{$res2['tid']}'", [':price'=>$price]);
					$success++;
				}elseif($conf['pricejk_edit']==0 && $price!=$res2['price'] && $res2['prid']>0){
					$DB->exec("update `pre_tools` set `price`=:price where `tid`='{$res2['tid']}'", [':price'=>$price]);
					$success++;
				}
				if($details['close']==1 && $res2['close']==0){
					$DB->exec("update `pre_tools` set `close`=1 where `tid`='{$res2['tid']}'");
				}elseif($details['close']==0 && $res2['close']==1){
					$DB->exec("update `pre_tools` set `close`=0 where `tid`='{$res2['tid']}'");
				}
				if($details['stock']!==null && $res2['stock']!=$details['stock']){
					$DB->exec("update `pre_tools` set `stock`=:stock where `tid`='{$res2['tid']}'", [':stock'=>$details['stock']]);
				}
				$DB->exec("update `pre_tools` set `uptime`='".time()."' where `tid`='{$res2['tid']}'");
			}
		}elseif(is_string($details) && strpos($details,'商品不存在')!==false){
			$rs2 = $DB->query("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ={$tool['shequ']} AND goods_id={$tool['goods_id']}");
			while($res2 = $rs2->fetch()){
				$DB->exec("update `pre_tools` set `close`=1,`uptime`='".time()."' where `tid`='{$res2['tid']}'");
				$success++;
			}
		}
		return $success;
	}

	public function pricejk($shequid,&$success){
		global $DB,$conf;
		$pricejk_time = $conf['pricejk_time']?$conf['pricejk_time']:600;
		for($i=0;$i<30;$i++){
			$tool = $DB->getRow("SELECT * FROM pre_tools WHERE is_curl=2 AND shequ='{$shequid}' AND active=1 AND cid IN ({$conf['pricejk_cid']}) AND uptime<'".(time()-$pricejk_time)."' ORDER BY uptime ASC");
			if(!$tool)break;
			$count = $this->pricejk_one($tool);
			$success += $count;
		}
		return true;
	}

	private function buildHandInfo($input, $inputsname){
		$keys = array();
		if($inputsname){
			$keys = explode('|', $inputsname);
		}
		array_unshift($keys, '');
		$result = array();
		foreach($input as $i=>$value){
			if($value === '' || $value === null)continue;
			$name = isset($keys[$i]) ? $keys[$i] : '';
			$name = preg_replace('/[\{\[].*$/', '', $name);
			if($name==='')$name = '参数'.($i+1);
		$result[$name] = $value;
		}
		return $result;
	}

	private function getRemoteGoodsInfo($goods_id, $return_error = false){
		$url = '/api/good_info';
		$param = [
			'user_id' => $this->config['username'],
			'gid' => $goods_id,
			'sign' => $this->getGoodsSign($goods_id)
		];
		$data = $this->get_curl($url, http_build_query($param));
		if(!$ret = json_decode($data, true)){
			return $return_error ? '打开对接网站失败' : false;
		}else{
			if(isset($ret['code']) && $ret['code']==1){
				return $ret;
			}
			return $return_error ? (isset($ret['msg']) ? $ret['msg'] : $data) : false;
		}
	}

	private function getGoodsRows(){
		$url = '/api/goodList';
		$param = [
			'user_id' => $this->config['username'],
			'sign' => $this->getUserSign()
		];
		$data = $this->get_curl($url, http_build_query($param));
		if(!$ret = json_decode($data, true)){
			return '打开对接网站失败';
		}
		if(isset($ret['code']) && $ret['code'] != 1){
			return isset($ret['msg']) ? $ret['msg'] : $data;
		}
		$rows = $this->extractGoodsRows($ret);
		if(count($rows)>0)return $rows;
		return '商品列表数据格式错误';
	}

	private function extractGoodsRows($data, $class = []){
		$list = [];
		if(!is_array($data))return $list;
		if($this->isAssocArray($data)){
			$class2 = $class;
			$cid = $this->pickValue($data, ['cid','class_id','category_id','cate_id','group_id','id']);
			$cname = $this->pickValue($data, ['class_name','category','category_name','cate_name','groupname','group_name','classname','name']);
			if($cid !== null || $cname !== null){
				$class2 = [
					'cid' => $cid !== null ? $cid : (isset($class['cid']) ? $class['cid'] : 0),
					'name' => $cname !== null ? $cname : (isset($class['name']) ? $class['name'] : '默认分类')
				];
			}
			foreach(['goods','good','products','product','list','lists','rows','items','data'] as $key){
				if(isset($data[$key]) && is_array($data[$key])){
					foreach($this->extractGoodsRows($data[$key], $class2) as $row){
						$list[] = $row;
					}
				}
			}
			if(count($list)>0)return $list;
			if($this->isGoodsRow($data)){
				if($class){
					if(!isset($data['cid']) && isset($class['cid']))$data['cid'] = $class['cid'];
					if(!isset($data['class_name']) && isset($class['name']))$data['class_name'] = $class['name'];
				}
				return [$data];
			}
			return $list;
		}
		foreach($data as $row){
			if(is_array($row)){
				foreach($this->extractGoodsRows($row, $class) as $goods){
					$list[] = $goods;
				}
			}
		}
		return $list;
	}

	private function isGoodsRow($row){
		if(!is_array($row))return false;
		$id = $this->pickValue($row, ['id','gid','goods_id','goodsid','good_id','goodid','product_id','productid','mainId','mainid']);
		if($id === null || $id === '')return false;
		if($this->pickValue($row, ['gid','goods_id','goodsid','good_id','goodid','product_id','productid','mainId','mainid']) !== null)return true;
		if($this->pickValue($row, ['api_price','price','goods_price','goodsprice','good_price','money','cost']) !== null)return true;
		if($this->pickValue($row, ['type','good_type','goods_type','typestr','stock','kucun','dcField','dcfield']) !== null)return true;
		return false;
	}

	private function isAssocArray($arr){
		if(!is_array($arr))return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	private function formatGoods($row){
		$id = $this->pickValue($row, ['gid','goods_id','goodsid','good_id','goodid','product_id','productid','mainId','mainid','id']);
		$name = $this->pickValue($row, ['name','goods_name','goodsname','good_name','goodname','title','product_name','productname']);
		$type = $this->pickValue($row, ['type','good_type','goods_type']);
		$typestr = $this->pickValue($row, ['typestr','type_name','goods_type_name']);
		$isfaka = $type === 'k' || $type === 1 || $type === '1' || $type === 'card' || strpos((string)$typestr, '卡密') !== false;
		$dcField = trim((string)$this->pickValue($row, ['dcField','dcfield','param','params','input','inputs','field','fieldName','fields']));
		$input = '';
		$inputs = '';
		if(!$isfaka && $dcField!=''){
			$field = explode('|', $dcField);
			$input = isset($field[0]) ? $field[0] : '';
			unset($field[0]);
			$inputs = implode('|', $field);
		}
		$status = $this->pickValue($row, ['status','goods_status','good_status','state','goods_state','good_state','shelf_status']);
		$statustr = $this->pickValue($row, ['statustr','statusstr','status_str','state_name','status_name','status_text']);
		$close = 0;
		$is_off_shelf = false;
		$is_on_shelf = false;
		// 优先用文本状态判断，更准确
		if($statustr !== null && $statustr !== ''){
			$off_texts = ['下架','已下架','售罄','已售罄','缺货','无货','停售','暂停','关闭','禁用','无效'];
			$on_texts = ['出售中','上架','正常','在售','有货','正常销售','开启','启用','有效'];
			foreach($off_texts as $t){
				if(strpos($statustr, $t) !== false){ $is_off_shelf = true; break; }
			}
			if(!$is_off_shelf){
				foreach($on_texts as $t){
					if(strpos($statustr, $t) !== false){ $is_on_shelf = true; break; }
				}
			}
		}
		// 文本判断为上架，直接设为上架
		if($is_on_shelf){
			$close = 0;
		}elseif($is_off_shelf){
			$close = 1;
		}else{
			// 文本无法判断时，用数字状态判断
			// 视多发卡：status=0 表示上架，status=1 或其他非0值表示下架
			if($status === null || $status === ''){
				$close = 0;
			}else{
				if(is_numeric($status)){
					$close = intval($status) == 0 ? 0 : 1;
				}else{
					$status_str = strval($status);
					if(in_array($status_str, ['0','on','active','true','yes'], true)){
						$close = 0;
					}elseif(in_array($status_str, ['1','off','inactive','false','no'], true)){
						$close = 1;
					}else{
						$close = 0;
					}
				}
			}
		}
		$stock = $this->pickValue($row, ['stock','kucun','quantity','num','count','qty']);
		// 商品列表API不返回实际库存，stock=0 表示无库存信息，设为null以便后续使用默认值
		$stock = ($stock === null || $stock === '' || intval($stock) === 0) ? null : intval($stock);
		$max = $this->pickValue($row, ['max','maxnum','max_num','buymaxnum','max_count']);
		if($max === null || $max === '')$max = $stock !== null && $stock > 0 ? $stock : 9999;
		$cid = $this->pickValue($row, ['cid','class_id','category_id','cate_id','group_id']);
		if($cid === null || $cid === '')$cid = 0;
		$cname = $this->pickValue($row, ['class_name','category','category_name','cate_name','groupname','group_name','classname']);
		if($cname === null || $cname === '')$cname = '默认分类';
		$price = $this->pickValue($row, ['api_price','price','goods_price','goodsprice','good_price','money','cost']);
		$value = $this->pickValue($row, ['multiple','value']);
		return array(
			'id' => $id,
			'name' => $name ?: '商品'.$id,
			'shopimg' => $this->buildImageUrl($this->pickValue($row, ['images','image','img','imgurl','shopimg','pic','picture','thumb'])),
			'price' => $price === null || $price === '' ? 0 : $price,
			'status' => $status,
			'close' => $close,
			'type' => $isfaka ? 1 : 0,
			'isfaka' => $isfaka,
			'stock' => $stock,
			'min' => $this->pickValue($row, ['min','minnum','min_num','buyminnum','min_count']) ?: 1,
			'max' => $max,
			'value' => $value && $value > 0 ? $value : 1,
			'input' => $input,
			'inputs' => $inputs,
			'desc' => $this->pickValue($row, ['desc','details','content','info','typestr']) ?: '',
			'alert' => $this->pickValue($row, ['alert','tip','tiptext']) ?: '',
			'cid' => $cid,
			'cname' => $cname
		);
	}

	private function publicGoods($goods){
		return [
			'id' => $goods['id'],
			'name' => $goods['name'],
			'shopimg' => $goods['shopimg'],
			'price' => $goods['price'],
			'status' => $goods['status'],
			'type' => $goods['type'],
			'input' => $goods['input'],
			'inputs' => $goods['inputs'],
			'stock' => $goods['stock'],
			'min' => $goods['min'],
			'max' => $goods['max'],
			'close' => $goods['close'],
			'value' => $goods['value'],
			'desc' => $goods['desc'],
			'alert' => $goods['alert']
		];
	}

	private function batchGoods($goods){
		return $this->publicGoods($goods) + [
			'tid' => $goods['id'],
			'minnum' => $goods['min'],
			'maxnum' => $goods['max'],
			'multi' => 1,
			'repeat' => 1,
			'validate' => 0,
			'valiserv' => '',
			'isfaka' => $goods['isfaka'] ? true : false,
			'original_cid' => $goods['cid'],
			'original_cname' => $goods['cname']
		];
	}

	private function pickValue($row, $keys){
		foreach($keys as $key){
			if(isset($row[$key]))return $row[$key];
		}
		return null;
	}

	private function formatCards($cards){
		$kmdata = [];
		if(!is_array($cards))return $kmdata;
		foreach($cards as $card){
			if(is_array($card)){
				if(isset($card['kano'])){
					$kmdata[] = ['card'=>$card['kano']];
				}elseif(isset($card['card'])){
					$kmdata[] = ['card'=>$card['card']];
				}elseif(isset($card['card_no'])){
					$kmdata[] = ['card'=>$card['card_no'], 'pass'=>isset($card['card_password']) ? $card['card_password'] : ''];
				}
			}elseif($card!==''){
				$kmdata[] = ['card'=>$card];
			}
		}
		return $kmdata;
	}

	private function buildImageUrl($image){
		if(!$image)return '';
		if(substr($image,0,4)=='http' || substr($image,0,2)=='//')return $image;
		return ($this->config['protocol']==1?'https://':'http://') . $this->getHost() . '/' . ltrim($image, '/');
	}

	private function get_curl($path,$post=0,$referer=0,$cookie=0,$header=0,$addheader=0){
		$url = ($this->config['protocol']==1?'https://':'http://') . $this->getHost() . $path;
		return \shequ_get_curl($url,$post,$referer,$cookie,$header,$addheader);
	}

	private function getHost(){
		return preg_replace('/^(https?:\/\/)/i', '', rtrim($this->config['url'], '/'));
	}

	private function getPaySign($goods_id, $num, $tradeno){
		return md5($this->config['username'].$goods_id.$num.$tradeno.$this->config['password']);
	}

	private function makeTradeNo(){
		return 'SD'.date('YmdHis').mt_rand(100000, 999999);
	}

	private function getGoodsSign($goods_id){
		return md5($this->config['username'].$goods_id.$this->config['password']);
	}

	private function getUserSign(){
		return md5($this->config['username'].$this->config['password']);
	}

	private function getCardSign($orderid){
		return md5($orderid.$this->config['username'].$this->config['password']);
	}
}
