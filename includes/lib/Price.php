<?php

namespace lib;



class Price {

	private $zid;

	private $upzid;

	private $power;

	private $user;

	private $price_array = array();

	private $up_price_array = array();

	private $iprice_array = array();

	private $tool = array();

	private $endtime;

	private static $price_rules;



	public function __construct($zid,$siterow=null){

		global $DB;

		if($zid == 1)return;

		if(!$siterow)$siterow=$this->getSiteInfo($zid);

		$this->endtime = $siterow['endtime'];

		// 从新表中获取价格数据
		$this->price_array = [];
		$this->iprice_array = [];
		$this->up_price_array = [];

		if($siterow['power']==2){

			$this->zid = $zid;
			$this->power = $siterow['power'];

			// 从新表中获取价格数据
			$rs = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$zid}'");
			while ($row = $rs->fetch()) {
				$this->price_array[$row['tid']] = [
					'price' => $row['price'],
					'cost' => $row['cost'],
					'cost2' => $row['cost2'],
					'del' => $row['del']
				];
			}

			// 获取iprice数据
			if (!empty($siterow['iprice'])) {
				$this->iprice_array = json_decode($siterow['iprice'], true);
				if (!is_array($this->iprice_array)) {
					$this->iprice_array = @unserialize($siterow['iprice']);
					if (!is_array($this->iprice_array)) {
						$this->iprice_array = [];
					}
				}
			}

		}elseif($siterow['power']==1){

			$this->zid = $zid;
			$this->power = $siterow['power'];

			// 从新表中获取价格数据
			$rs = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$zid}'");
			while ($row = $rs->fetch()) {
				$this->price_array[$row['tid']] = [
					'price' => $row['price'],
					'cost' => $row['cost'],
					'cost2' => $row['cost2'],
					'del' => $row['del']
				];
			}

			// 获取iprice数据
			if (!empty($siterow['iprice'])) {
				$this->iprice_array = json_decode($siterow['iprice'], true);
				if (!is_array($this->iprice_array)) {
					$this->iprice_array = @unserialize($siterow['iprice']);
					if (!is_array($this->iprice_array)) {
						$this->iprice_array = [];
					}
				}
			}

			if($data = $DB->getRow("SELECT zid,price FROM pre_site WHERE zid='{$siterow['upzid']}' AND power=2 LIMIT 1")){
				// 从新表中获取上级站点的价格数据
				$rs = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$data['zid']}'");
				while ($row = $rs->fetch()) {
					$this->up_price_array[$row['tid']] = [
						'price' => $row['price'],
						'cost' => $row['cost'],
						'cost2' => $row['cost2'],
						'del' => $row['del']
					];
				}
				$this->upzid=$data['zid'];
			}

		}elseif($siterow['power']==0){

			$this->user = true;

			if($data = $DB->getRow("SELECT zid,upzid,power,price FROM pre_site WHERE zid='{$siterow['upzid']}' LIMIT 1")){

				$this->zid = $data['zid'];
				$this->power = $data['power'];

				// 从新表中获取价格数据
				$rs = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$this->zid}'");
				while ($row = $rs->fetch()) {
					$this->price_array[$row['tid']] = [
						'price' => $row['price'],
						'cost' => $row['cost'],
						'cost2' => $row['cost2'],
						'del' => $row['del']
					];
				}

				// 获取iprice数据
				if (!empty($data['iprice'])) {
					$this->iprice_array = json_decode($data['iprice'], true);
					if (!is_array($this->iprice_array)) {
						$this->iprice_array = @unserialize($data['iprice']);
						if (!is_array($this->iprice_array)) {
							$this->iprice_array = [];
						}
					}
				}

				if($this->power == 1 && $data['upzid']>1 && $data = $DB->getRow("SELECT zid,price FROM pre_site WHERE zid='{$data['upzid']}' and power=2 limit 1")){
					// 从新表中获取上级站点的价格数据
					$rs = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$data['zid']}'");
					while ($row = $rs->fetch()) {
						$this->up_price_array[$row['tid']] = [
							'price' => $row['price'],
							'cost' => $row['cost'],
							'cost2' => $row['cost2'],
							'del' => $row['del']
						];
					}
					$this->upzid=$data['zid'];
				}

			}

		}

	}

	public function setToolInfo($tid,$row=null){

		global $DB,$CACHE;

		if(!$row)$row=$this->getToolInfo($tid);
		
		// 保存原始价格，用于后续计算
		$original_price = $row['price'];

		if($row['prid']==0){ 
			// 不加价，保持原始价格
		} elseif($price_rules = $this->getPriceRules($row['prid'])){ 
			// 应用加价模板
			if($price_rules['kind']==1){ 
				// 固定金额加价
				$row['price'] = round($original_price + $price_rules['p_0'], 2);
				$row['cost'] = round($original_price + $price_rules['p_1'], 2);
				$row['cost2'] = round($original_price + $price_rules['p_2'], 2);
			} else { 
				// 倍数加价
				$row['price'] = round($original_price * $price_rules['p_0'], 2);
				$row['cost'] = round($original_price * $price_rules['p_1'], 2);
				$row['cost2'] = round($original_price * $price_rules['p_2'], 2);
			}
		} else { 
			// 对应加价模板被删除或不存在
			$row['cost'] = $row['price'];
			$row['cost2'] = $row['price'];
		}

		// 确保价格为正数
		if($row['price'] <= 0) $row['price'] = $original_price;
		if($row['cost'] <= 0) $row['cost'] = $row['price'];
		if($row['cost2'] <= 0) $row['cost2'] = $row['cost'];

		//应用自定义密价
		if($this->power==1 && isset($this->iprice_array[$tid]) && $this->iprice_array[$tid]>0){
			$row['cost'] = $this->iprice_array[$tid];
		}elseif($this->power==2 && isset($this->iprice_array[$tid]) && $this->iprice_array[$tid]>0){
			$row['cost2'] = $this->iprice_array[$tid];
		}

		$this->tool=$row;

	}

	public function getMainPrice(){

		return $this->tool['price'];

	}

	public function getMainCost(){

		return $this->tool['cost'];

	}

	public function getMainCost2(){

		return $this->tool['cost2'];

	}

	public function getToolPrice($tid){

		global $islogin2,$conf,$date,$userrow;

		if($islogin2==1){

		    if($userrow['power'] < 1){

		        $this->user = true;

		    }

			if($this->user==true && $conf['user_level']==1){

				return $this->getToolCost($tid);

			}elseif($this->user==true || $conf['fenzhan_expiry']>0 && $this->endtime<$date){

			}elseif($this->power==1 && !$this->user){

				return $this->getToolCost($tid);

			}elseif($this->power==2 && !$this->user){

				return $this->getToolCost2($tid);

			}else{

			    $row = $this->getToolInfo($tid);

            if($row['prid'] == 0){

                return $row['price'];

            }else{

                $priceTemplate = $this->getPriceRules($row['prid']);

                if($priceTemplate && is_array($priceTemplate)){
                    if($priceTemplate['kind'] == 1){
                        return round($row['price'] + $priceTemplate['p_0'], 2);
                    }else{
                        return round($row['price'] * $priceTemplate['p_0'], 2);
                    }
                }else{
                    return $row['price'];
                }

            }

			}

		}

		$cost = $this->getToolCost($tid);

		if(isset($this->price_array[$tid]['price']) && $this->price_array[$tid]['price'] && $this->price_array[$tid]['price']>=$cost && $cost>0){

			$price=$this->price_array[$tid]['price'];

		}elseif(isset($this->up_price_array[$tid]['price']) && $this->up_price_array[$tid]['price'] && $this->up_price_array[$tid]['price']>=$cost && $cost>0){

			$price = $this->up_price_array[$tid]['price'];

		}elseif($cost>0 && $cost>$this->tool['price']){

			$price=$cost;

		}else{

			$price=$this->tool['price'];

		}

		return $price;

	}

	public function getToolCost($tid){
		$cost2 = $this->getToolCost2($tid);
		if($this->power<2 && isset($this->up_price_array[$tid]['cost']) && $this->up_price_array[$tid]['cost'] && $this->up_price_array[$tid]['cost']>=$cost2){
			$cost = $this->up_price_array[$tid]['cost'];
		}elseif($this->power==2 && isset($this->price_array[$tid]['cost']) && $this->price_array[$tid]['cost'] && $this->price_array[$tid]['cost']>=$cost2){
			$cost = $this->price_array[$tid]['cost'];
		}elseif($this->tool['cost']>0){
			$cost = $this->tool['cost'];
		}else{
			$cost = $this->tool['price'];
		}
		return $cost;
	}

	public function getToolCost2($tid){
		if(isset($this->price_array[$tid]['cost2']) && $this->price_array[$tid]['cost2']>0){
			$cost = $this->price_array[$tid]['cost2'];
		}elseif($this->tool['cost2']>0){
			$cost = $this->tool['cost2'];
		}elseif($this->tool['cost']>0){
			$cost = $this->tool['cost'];
		}else{
			$cost = $this->tool['price'];
		}
		return $cost;
	}

	public function getToolDel($tid){
		return isset($this->price_array[$tid]['del']) ? $this->price_array[$tid]['del'] : 0;
	}

	public function getFinalPrice($price, $num){

		if(!empty($this->tool['prices'])){

			$prices = explode(',',$this->tool['prices']);

			foreach($prices as $item){

				$arrs = explode('|',$item);

				if($num>=$arrs[0])$discount=$arrs[1];

			}

			$price -= $discount;

			if($price<=0)return false;

		}

		return $price;

	}

	public function getTooliPrice($tid){

		if($this->power>0 && $this->iprice_array[$tid]>0){

			return $this->iprice_array[$tid];

		}else{

			return null;

		}

	}

	public function setToolProfit($tid,$num,$name,$money,$orderid,$userid=0){

		global $DB,$islogin2,$conf,$date;

		if(is_numeric($userid) && strlen($userid)!=32)$islogin2=1;

		$toolPrice = $this->getFinalPrice($this->getToolPrice($tid), $num);

		if(round($toolPrice*$num,2) != round($money,2))return false;

		if($this->power==2){

			$profit=$toolPrice - $this->getToolCost2($tid);

			if($profit>0 && $profit<$money){

				$tc_point=round($profit*$num, 2);

				$rs=$this->changeUserMoney($this->zid, $tc_point, '提成', '你网站用户下单 '.$name.' 获得'.$tc_point.'元提成', $orderid);

			}

		}elseif($this->power==1){

			$profit=$toolPrice - $this->getToolCost($tid);

			if($profit>0 && $profit<$money){

				$tc_point=round($profit*$num, 2);

				$rs=$this->changeUserMoney($this->zid, $tc_point, '提成', '你网站用户下单 '.$name.' 获得'.$tc_point.'元提成', $orderid);

			}

			$profit2=$this->getToolCost($tid) - $this->getToolCost2($tid);

			if($profit2>0 && $profit2<$money && $this->upzid>1){

				$tc_point=round($profit2*$num, 2);

				$rs=$this->changeUserMoney($this->upzid, $tc_point, '提成', '你下级网站(ZID:'.$this->zid.')用户下单 '.$name.' 获得'.$tc_point.'元提成', $orderid);

			}

		}

		return $rs;

	}

	public function setPriceInfo($tid,$del,$price,$cost=0){
		global $DB;
		
		// 确保price_array[$tid]存在
		if(!isset($this->price_array[$tid])) {
			$this->price_array[$tid] = array();
		}
		
		// 更新内存中的价格数据
		$this->price_array[$tid]['price'] = $price;
		if($this->power==2) {
			$this->price_array[$tid]['cost'] = $cost;
		}
		$this->price_array[$tid]['del'] = $del;
		
		// 将价格数据保存到新表中
		$cost2 = isset($this->price_array[$tid]['cost2']) ? $this->price_array[$tid]['cost2'] : 0;
		
		// 使用INSERT ON DUPLICATE KEY UPDATE语法，确保数据的唯一性
		$sql = "INSERT INTO pre_site_price (zid, tid, price, cost, cost2, del, create_time, update_time) 
		       VALUES (:zid, :tid, :price, :cost, :cost2, :del, NOW(), NOW()) 
		       ON DUPLICATE KEY UPDATE 
		       price = :price, cost = :cost, cost2 = :cost2, del = :del, update_time = NOW()";
		
		$data = [
			':zid' => $this->zid,
			':tid' => $tid,
			':price' => $price,
			':cost' => $cost,
			':cost2' => $cost2,
			':del' => $del
		];
		
		return $DB->exec($sql, $data);
	}

	public function setiPriceInfo($tid,$price){

		global $DB;

		if($price==0){

			unset($this->iprice_array[$tid]);

		}else{

			$this->iprice_array[$tid] = $price;

		}

		$iprice_data = serialize($this->iprice_array);

		return $DB->exec("UPDATE pre_site SET iprice='$iprice_data' WHERE zid='{$this->zid}'");

	}

	public function getPower(){

		return $this->power;

	}

	private function changeUserMoney($zid, $money, $action=null, $desc = null, $orderid=null){

		global $DB,$conf;

		if($money<=0)return;

		if(!$conf['tixian_limit'] || $conf['tixian_limit']==1 && !$conf['tixian_days']){

			$sqls=",`rmbtc`=`rmbtc`+{$money}";

			$status=1;

		}else{

			$status=0;

		}

		$rs=$DB->exec("UPDATE `pre_site` SET `rmb`=`rmb`+{$money}{$sqls} WHERE `zid`='{$zid}'");

		$DB->exec("INSERT INTO `pre_points` (`zid`, `action`, `point`, `bz`, `addtime`, `orderid`, `status`) VALUES (:zid, :action, :point, :bz, NOW(), :orderid, :status)", [':zid'=>$zid, ':action'=>$action, ':point'=>$money, ':bz'=>$desc, ':orderid'=>$orderid, ':status'=>$status]);

		return $rs;

	}

	private function getSiteInfo($zid){

		global $DB;

		$data = $DB->getRow("SELECT zid,upzid,power,price,iprice,endtime FROM pre_site WHERE zid='$zid' LIMIT 1");

		return $data;

	}

	private function getToolInfo($tid){

		global $DB;

		$row=$DB->getRow("SELECT * FROM pre_tools WHERE tid='$tid' LIMIT 1");

		return $row;

	}

	private function getPriceRules($id){

		global $DB,$CACHE;

		// 先检查缓存是否已加载到内存
		if(self::$price_rules && isset(self::$price_rules[$id])) {
			return self::$price_rules[$id];
		}

		// 尝试从缓存中读取
		$price_rules = unserialize($CACHE->read('pricerules'));

		if(!$price_rules || !is_array($price_rules)){
			// 缓存不存在或无效，重新生成
			$this->updatePriceRules();
			// 检查更新后是否存在该规则
			if(self::$price_rules && isset(self::$price_rules[$id])) {
				return self::$price_rules[$id];
			}
		} else {
			// 缓存有效，保存到内存
			self::$price_rules = $price_rules;
			// 检查是否存在该规则
			if(isset(self::$price_rules[$id])) {
				return self::$price_rules[$id];
			}
		}

		// 如果规则不存在，返回false
		return false;

	}

	private function updatePriceRules(){

		global $DB,$CACHE;

		$array = array();

		$rs=$DB->query("SELECT * FROM pre_price ORDER BY id ASC");

		while($res = $rs->fetch()){

			$array[$res['id']] = array('kind'=>$res['kind'], 'p_2'=>$res['p_2'], 'p_1'=>$res['p_1'], 'p_0'=>$res['p_0']);

		}

		$CACHE->save('pricerules', $array);

		self::$price_rules = $array;

	}

}

