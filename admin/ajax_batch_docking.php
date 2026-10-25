<?php
/**
 * 批量对接商品处理文件
 * 博客地址：zhonguo.ren
 * QQ群：915043052
 * 开发者：教主
 * 
 * 功能说明：
 * 1. 处理批量对接商品的各种操作
 * 2. 支持亿乐SUP新版API对接
 * 3. 防止SQL注入、XSS攻击、CSRF攻击
 * 
 * 更新日志：
 * v2.0 - 适配亿乐SUP新版API，使用SHA1签名认证
 */

include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;
@header('Content-Type: application/json; charset=UTF-8');
if(!checkRefererHost())exit('{"code":403}');
@set_time_limit(0);
switch($act){
case 'upInputTitle':	//获取商品介绍里面的网络图片并下载到本地
	$id=$_POST['shequ'];
	$keyword=$_POST['keyword'];
	$title=$_POST['title'];
	$ret = $DB->exec("UPDATE pre_tools SET `input` = :title WHERE shequ=:shequ AND `desc` LIKE :keyword", [':title'=>$title, ':shequ'=>$id, ':keyword'=>'%'.$keyword.'%']);
	exit('{"code":1,"msg":"替换了 '.$ret.' 个"}');
break;	
case 'downloaddescimg':	//获取商品介绍里面的网络图片并下载到本地
	$id=$_POST['shequ'];
	$sum=isset($_POST['sum'])?daddslashes($_POST['sum']):1;
	$kw = 'src="http';
	$ok=0;
    $no=0;
	// 使用参数化查询 - 修复SQL注入漏洞
	$re=$DB->query("SELECT * FROM `pre_tools` WHERE shequ=:id AND `desc` like :kw limit :sum", [':id'=>$id, ':kw'=>'%'.$kw.'%', ':sum'=>$sum]);
	while($res = $re->fetch()){
		preg_match_all ('/src="(.*)"/U', $res['desc'], $pat_array);
		$img1 =array();
		$img2 =array();
		foreach ($pat_array[1] as $i => $value) {
			$file_name = 'assets/img/Product/shop_tid_'.$res['tid'].'_desc'.$i.'.png';
		    $file_name2 = '../'.$file_name;
		    $ret = file_download($value,$file_name2);
		    if($ret){
			    $img1[] = $value;
			    $img2[] = $file_name;
		    }
		}
		if(count($img1) == 0){
			$no++;
			continue;
		}
		$desc = str_replace($img1,$img2,$res['desc']);
		$sql="update pre_tools set `desc`=:desc WHERE tid=:tid";
$data=[':desc'=>$desc, ':tid'=>$res['tid']];
		if($DB->exec($sql,$data)){
			$ok++;
		}else{
			$no++;
		}
	}
	exit('{"code":1,"msg":"成功 '.$ok.' 个，失败 '.$no.' 个"}');
break;	
case 'downloadimg':
	$id=$_POST['shequ'];
	$sum=isset($_POST['sum'])?daddslashes($_POST['sum']):1;
    $ok=0;
    $no=0;
    $is = true;
    $i=0;
    do{
    	// 使用参数化查询 - 修复SQL注入漏洞
	$row=$DB->getRow("SELECT * FROM pre_tools WHERE shequ=:id AND `shopimg` like :pattern limit 1", [':id'=>$id, ':pattern'=>'%http%']);
    	if($row){
    		// 使用参数化查询 - 修复SQL注入漏洞
		$re=$DB->query("SELECT * FROM pre_tools WHERE shequ=:id AND shopimg=:shopimg", [':id'=>$id, ':shopimg'=>$row['shopimg']]);
    		$tidArr =array();
    		while($res = $re->fetch()){   			
				$tidArr[] = $res['tid'];
    		}
	    	$file_name = 'assets/img/Product/shop_tid_'.$row['tid'].'_shequ_'.$id.'.png';
		    $file_name2 = '../'.$file_name;
		    $ret = file_download($row['shopimg'],$file_name2);
		    if($ret){
		    	$placeholders = [];
$params = [':shopimg'=>$file_name];
foreach($tidArr as $ti => $tv){
    $placeholders[] = ':tid_'.$ti;
    $params[':tid_'.$ti] = $tv;
}
$DB->exec("update pre_tools set `shopimg`=:shopimg WHERE tid in(".implode(',',$placeholders).")", $params);
    $ok++;
}else{
    //$DB->exec("update pre_tools set `shopimg`='{$row['shopimg']}?is' WHERE tid in(".implode(',',$tidArr).")");
		    	$no++;
		    }    		
		    if($i>$sum){
		    	break;
		    }
		    $i++;
    	}else{
    		$is = false;
    	}

    }while($is);
	exit('{"code":1,"msg":"成功 '.$ok.' 个，失败 '.$no.' 个"}');
break;
case 'downloadimg2':
	$id=$_POST['shequ'];
	$sum=isset($_POST['sum'])?daddslashes($_POST['sum']):1;
    $ok=0;
    $no=0;
    $i=0;
	// 使用参数化查询 - 修复SQL注入漏洞
	$re=$DB->query("SELECT * FROM pre_tools WHERE shequ=:id AND `shopimg` like :pattern", [':id'=>$id, ':pattern'=>'%http%']);
	while($res = $re->fetch()){   
		if(stripos($res['shopimg'].']','?is=xbc')){continue;}

		// 使用参数化查询 - 修复SQL注入漏洞
	$re2=$DB->query("SELECT * FROM pre_tools WHERE shequ=:id AND shopimg=:shopimg", [':id'=>$id, ':shopimg'=>$res['shopimg']]);
		$tidArr =array();
		while($res2 = $re2->fetch()){   			
			$tidArr[] = $res2['tid'];
		}
		if(count($tidArr)==0){continue;}
    	$file_name = 'assets/img/Product/shop_tid_'.$res['tid'].'_shequ_'.$id.'.png';
	    $file_name2 = '../'.$file_name;
	    $ret = file_download($res['shopimg'],$file_name2);
	    if($ret){
	    	$placeholders = [];
$params = [':shopimg'=>$file_name];
foreach($tidArr as $ti => $tv){
    $placeholders[] = ':tid_'.$ti;
    $params[':tid_'.$ti] = $tv;
}
$DB->exec("update pre_tools set `shopimg`=:shopimg WHERE tid in(".implode(',',$placeholders).")", $params);
    $ok++;
}else{
    $placeholders = [];
$params = [':shopimg'=>$res['shopimg'].'?is=xbc'];
foreach($tidArr as $ti => $tv){
    $placeholders[] = ':tid_'.$ti;
    $params[':tid_'.$ti] = $tv;
}
$DB->exec("update pre_tools set `shopimg`=:shopimg WHERE tid in(".implode(',',$placeholders).")", $params);
	    	$no++;
	    }    		
	    if($i>$sum){
	    	break;
	    }
	    $i++;
	}

	exit('{"code":1,"msg":"成功 '.$ok.' 个，失败 '.$no.' 个"}');
break;
case 'batchUpDesc':	//更新商品介绍
	$shequ = daddslashes($_POST['shequ']);
	// 使用参数化查询 - 修复SQL注入漏洞
	if($DB->getColumn("SELECT count(*) FROM `pre_tools` WHERE shequ=:shequ", [':shequ'=>$shequ]) == 0){
		exit('{"code":-1,"msg":"该社区没有商品"}');
	}
	// 使用参数化查询 - 修复SQL注入漏洞
	$row=$DB->getRow("SELECT * FROM `pre_shequ` WHERE type='daishua' AND id=:shequ LIMIT 1", [':shequ'=>$shequ]);
	if($row){
        $url = ($row['protocol']==1?'https://':'http://') . $row['url'];
        $post = 'user='.$row['username'].'&pass='.$row['password'];
        $list = json_decode(get_curl($url.'/api_tool/api/api.php?act=getGoodsDesc', $post),true);
		if($list['code'] == 1){
			$update_success=0;
			foreach ($list['data'] as $value) {
				if(stripos($value['desc'],'src="assets/')){
					$desc = str_replace('src="assets','src="'.$url.'/assets',$value['desc']);
				}else{
					$desc = $value['desc'];
				}
				// 使用参数化查询 - 修复SQL注入漏洞
						$sql = "UPDATE `pre_tools` SET `desc`=:desc WHERE shequ=:shequ AND `goods_id`=:goods_id";
						$data = [':desc'=>$desc, ':shequ'=>$shequ, ':goods_id'=>$value['tid']];
				$DB->exec($sql, $data);
			$update_success++;
			}	
			$result=['code'=>1, 'msg'=>$DB->error().'成功更新'.$update_success.'个商品'];
			exit(json_encode($result));
		}else{
			exit('{"code":-1,"msg":"获取社区商品失败"}');
		}
	}else{
		exit('{"code":-1,"msg":"社区不存"}');
	}
break;
case 'batchAddGoods_ds2':	//获取彩虹社区版系统商品
    $shequ = daddslashes($_POST['shequ']);
    $prid = daddslashes($_POST['prid']);
    $row=$DB->getRow("SELECT * FROM `pre_shequ` WHERE type='daishua' AND id=:id LIMIT 1", [':id'=>$shequ]);
    if($row){
        $url = ($row['protocol']==1?'https://':'http://') . $row['url'];
        $post = 'user='.$row['username'].'&pass='.$row['password'];
        $list = json_decode(get_curl($url.'/api_tool/api/api.php?act=getGoodsLiti', $post),true);
        $add_success=0;
$add_class_one=0;
$add_class=0;
if($list['code'] == 1){
    $tidArr =array();
// 使用参数化查询 - 修复SQL注入漏洞
$re=$DB->query("SELECT tid,goods_id FROM pre_tools WHERE shequ=:shequ_id", [':shequ_id'=>$row['id']]);
while($res = $re->fetch()){   			
    $tidArr[] = $res['goods_id'];
}
$class_sort = $DB->getRow("SELECT sort FROM `pre_class` order by sort desc limit 1");
$class_sort = $class_sort['sort'] + 1;

$tools_sort = $DB->getRow("SELECT sort FROM `pre_tools` order by sort desc limit 1");
$tools_sort = $tools_sort['sort'] + 1;

$is=$DB->getRow("select*from information_schema.tableS where table_name = 'shua_class_one'");
if($is){//社区版的
$class_one_sort = $DB->getRow("SELECT sort FROM `pre_class_one` order by sort desc limit 1");
				$class_one_sort = $class_one_sort['sort'] + 1;
	        	foreach ($list['data'] as $one) {
	        		
	        		// 使用参数化查询 - 修复SQL注入漏洞
								$class_one_row = $DB->getRow("SELECT * FROM pre_class_one WHERE `name` = :name limit 1", [':name'=>$one['one_name']]);
	        		if($class_one_row){
	        			$oneid=$class_one_row['cid'];
	        		}else{
		        		//添加一级分类
	      				if(stripos('['.$one['shopimg'],'assets/img/')){
	      					$shopimg = $url.'/'.$one['shopimg'];
	      				}else{
	      					$shopimg = $one['shopimg'];
	      				}
						$sql="INSERT INTO `pre_class_one` (`sort`,`name`,`active`,`shopimg`) VALUES (:sort, :name, :active, :shopimg)";
$DB->exec($sql, [':sort'=>$class_one_sort, ':name'=>$one['one_name'], ':active'=>1, ':shopimg'=>$shopimg]);
						$oneid=$DB->lastInsertId();	  
						$add_class_one++;      			
	        		}
					$class_one_sort++;
	        		foreach ($one['two'] as $two) {

	        			// 使用参数化查询 - 修复SQL注入漏洞
										$class_row = $DB->getRow("SELECT * FROM pre_class WHERE `name` = :name limit 1", [':name'=>$two['two_name']]);
	        			if($class_row){
	        				$cid=$class_row['cid'];
	        			}else{
			 				//添加二级分类
		      				if(stripos('['.$two['shopimg'],'assets/img/')){
		      					$shopimg = $url.'/'.$two['shopimg'];
		      				}else{
		      					$shopimg = $two['shopimg'];
		      				}						
							$sql="INSERT INTO `pre_class` (`sort`,`name`,`active`,`shopimg`,`oneid`) VALUES (:sort, :name, :active, :shopimg, :oneid)";
$DB->exec($sql, [':sort'=>$class_sort, ':name'=>$two['two_name'], ':active'=>1, ':shopimg'=>$shopimg, ':oneid'=>$oneid]);
							$cid=$DB->lastInsertId();
							$add_class++;
	        			}
						$class_sort++;
	          			foreach ($two['data'] as $v) {
							if(in_array($v['tid'],$tidArr))continue;
	          				if(stripos('['.$v['shopimg'],'assets/img/')){
	          					$shopimg = $url.'/'.$v['shopimg'];
	          				}else{
	          					$shopimg = $v['shopimg'];
	          				}
							$DB->exec("INSERT INTO `pre_tools` (`sort`,`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`close`,`active`,`addtime`) VALUES (:sort,:cid,:name,:price,:cost,:cost2,:prid,:prices,:input,:inputs,:desc,:alert,:shopimg,:value,:is_curl,:curl,:shequ,:goods_id,:goods_type,:goods_param,:repeat,:multi,:min,:max,:validate,:valiserv,:close,:active,NOW())", [':sort'=>$tools_sort, ':cid'=>$cid, ':name'=>$v['name'], ':price'=>$v['price'], ':cost'=>0, ':cost2'=>0, ':prid'=>$prid, ':prices'=>'', ':input'=>$v['input'], ':inputs'=>$v['inputs'], ':desc'=>'', ':alert'=>$v['alert'], ':shopimg'=>$shopimg, ':value'=>$v['value'], ':is_curl'=>2, ':curl'=>'', ':shequ'=>$row['id'], ':goods_id'=>$v['tid'], ':goods_type'=>($v['isfaka']?'1':'0'), ':goods_param'=>'', ':repeat'=>$v['repeat'], ':multi'=>$v['multi'], ':min'=>$v['min'], ':max'=>$v['max'], ':validate'=>$v['validate'], ':valiserv'=>$v['valiserv'], ':close'=>$v['close'], ':active'=>$v['active']]);
							$tools_sort++;
							$add_success++;
	        			}
	        		}
	        	}

			}else{
	        	foreach ($list['data'] as $one) {
	        		foreach ($one['two'] as $two) {
	        			$class_row = $DB->getRow("SELECT * FROM pre_class WHERE `name` = :name limit 1", [':name'=>$two['two_name']]);
	        			if($class_row){
	        				$cid=$class_row['cid'];
	        			}else{
			 				//添加二级分类
		      				if(stripos('['.$two['shopimg'],'assets/img/')){
		      					$shopimg = $url.'/'.$two['shopimg'];
		      				}else{
		      					$shopimg = $two['shopimg'];
		      				}						
							$sql="INSERT INTO `pre_class` (`sort`,`name`,`active`,`shopimg`) VALUES (:sort, :name, :active, :shopimg)";
$DB->exec($sql, [':sort'=>$class_sort, ':name'=>$two['two_name'], ':active'=>1, ':shopimg'=>$shopimg]);
							$cid=$DB->lastInsertId();
$add_class++;	        				
	        			}
						$class_sort++;
	          			foreach ($two['data'] as $v) {
	          				if(in_array($v['tid'],$tidArr))continue;
	          				if(stripos('['.$v['shopimg'],'assets/img/')){
	          					$shopimg = $url.'/'.$v['shopimg'];
	          				}else{
	          					$shopimg = $v['shopimg'];
	          				}
							$DB->exec("INSERT INTO `pre_tools` (`sort`,`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`close`,`active`,`addtime`) VALUES (:sort,:cid,:name,:price,:cost,:cost2,:prid,:prices,:input,:inputs,:desc,:alert,:shopimg,:value,:is_curl,:curl,:shequ,:goods_id,:goods_type,:goods_param,:repeat,:multi,:min,:max,:validate,:valiserv,:close,:active,NOW())", [':sort'=>$tools_sort, ':cid'=>$cid, ':name'=>$v['name'], ':price'=>$v['price'], ':cost'=>0, ':cost2'=>0, ':prid'=>$prid, ':prices'=>'', ':input'=>$v['input'], ':inputs'=>$v['inputs'], ':desc'=>'', ':alert'=>$v['alert'], ':shopimg'=>$shopimg, ':value'=>$v['min'], ':is_curl'=>2, ':curl'=>'', ':shequ'=>$row['id'], ':goods_id'=>$v['tid'], ':goods_type'=>($v['isfaka']?'1':'0'), ':goods_param'=>'', ':repeat'=>$v['repeat'], ':multi'=>$v['multi'], ':min'=>1, ':max'=>$v['max'], ':validate'=>$v['validate'], ':valiserv'=>$v['valiserv'], ':close'=>$v['close'], ':active'=>$v['active']]);
							$tools_sort++;
							$add_success++;
	        			}
	        		}
	        	}
			}
// 批量记录上架日志
foreach ($list['data'] as $one) {
    foreach ($one['two'] as $two) {
        foreach ($two['data'] as $v) {
            if(in_array($v['tid'],$tidArr))continue;
            $content = "商品上架：{$v['name']}";
            $DB->exec("INSERT INTO `pre_toollogs` (`content`,`date`,`addtime`,`active`) VALUES (:content, CURDATE(), NOW(), 1)", array(':content' => $content));
        }
    }
}
$result=['code'=>1, 'msg'=>$DB->error().'成功添加'.$add_success.'个商品'];
			exit(json_encode($result));
        }else{
        	exit('{"code":-1,"msg":"获取社区失败"}');
    	}

    }else{
        exit('{"code":-1,"msg":"社区不存"}');
    }
break;
case 'batchAddGoods_ds':	//获取彩虹社区版系统商品
    $shequ = daddslashes($_POST['shequ']);
    $prid = daddslashes($_POST['prid']);
    $row=$DB->getRow("SELECT * FROM `pre_shequ` WHERE type='daishua' AND id=:id LIMIT 1", [':id'=>$shequ]);
    if($row){
        $url = ($row['protocol']==1?'https://':'http://') . $row['url'];
        $post = 'user='.$row['username'].'&pass='.$row['password'];
        $list = json_decode(get_curl($url.'/api_tool/api/api.php?act=getGoodsLiti', $post),true);
        $add_success=0;
        if($list['code'] == 1){

			$class_sort = $DB->getRow("SELECT sort FROM `pre_class` order by sort desc limit 1");
			$class_sort = $class_sort['sort'] + 1;

			$tools_sort = $DB->getRow("SELECT sort FROM `pre_tools` order by sort desc limit 1");
			$tools_sort = $tools_sort['sort'] + 1;

			$is=$DB->getRow("select*from information_schema.tableS where table_name = 'shua_class_one'");
if($is){//社区版的
				$class_one_sort = $DB->getRow("SELECT sort FROM `pre_class_one` order by sort desc limit 1");
				$class_one_sort = $class_one_sort['sort'] + 1;
	        	foreach ($list['data'] as $one) {
	        		//添加一级分类
      				if(stripos('['.$one['shopimg'],'assets/img/')){
      					$shopimg = $url.'/'.$one['shopimg'];
      				}else{
      					$shopimg = $one['shopimg'];
      				}
					$sql="INSERT INTO `pre_class_one` (`sort`,`name`,`active`,`shopimg`) VALUES (:sort, :name, :active, :shopimg)";
$DB->exec($sql, [':sort'=>$class_one_sort, ':name'=>$one['one_name'], ':active'=>1, ':shopimg'=>$shopimg]);
					$oneid=$DB->lastInsertId();
					$class_one_sort++;
	        		foreach ($one['two'] as $two) {

		 				//添加二级分类
	      				if(stripos('['.$two['shopimg'],'assets/img/')){
	      					$shopimg = $url.'/'.$two['shopimg'];
	      				}else{
	      					$shopimg = $two['shopimg'];
	      				}						
						$sql="INSERT INTO `pre_class` (`sort`,`name`,`active`,`shopimg`,`oneid`) VALUES (:sort, :name, :active, :shopimg, :oneid)";
$DB->exec($sql, [':sort'=>$class_sort, ':name'=>$two['two_name'], ':active'=>1, ':shopimg'=>$shopimg, ':oneid'=>$oneid]);
						$cid=$DB->lastInsertId();
						$class_sort++;
	          			foreach ($two['data'] as $v) {
	          				if(stripos('['.$v['shopimg'],'assets/img/')){
	          					$shopimg = $url.'/'.$v['shopimg'];
	          				}else{
	          					$shopimg = $v['shopimg'];
	          				}
							$DB->exec("INSERT INTO `pre_tools` (`sort`,`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`close`,`active`,`addtime`) VALUES (:sort,:cid,:name,:price,:cost,:cost2,:prid,:prices,:input,:inputs,:desc,:alert,:shopimg,:value,:is_curl,:curl,:shequ,:goods_id,:goods_type,:goods_param,:repeat,:multi,:min,:max,:validate,:valiserv,:close,:active,NOW())", [':sort'=>$tools_sort, ':cid'=>$cid, ':name'=>$v['name'], ':price'=>$v['price'], ':cost'=>0, ':cost2'=>0, ':prid'=>$prid, ':prices'=>'', ':input'=>$v['input'], ':inputs'=>$v['inputs'], ':desc'=>'', ':alert'=>$v['alert'], ':shopimg'=>$shopimg, ':value'=>$v['value'], ':is_curl'=>2, ':curl'=>'', ':shequ'=>$row['id'], ':goods_id'=>$v['tid'], ':goods_type'=>($v['isfaka']?'1':'0'), ':goods_param'=>'', ':repeat'=>$v['repeat'], ':multi'=>$v['multi'], ':min'=>$v['min'], ':max'=>$v['max'], ':validate'=>$v['validate'], ':valiserv'=>$v['valiserv'], ':close'=>$v['close'], ':active'=>$v['active']]);
$tools_sort++;
$add_success++;
}
}
}
}else{
foreach ($list['data'] as $one) {
    foreach ($one['two'] as $two) {
//添加二级分类
	      				if(stripos('['.$two['shopimg'],'assets/img/')){
	      					$shopimg = $url.'/'.$two['shopimg'];
	      				}else{
	      					$shopimg = $two['shopimg'];
	      				}						
						$sql="INSERT INTO `pre_class` (`sort`,`name`,`active`,`shopimg`) VALUES (:sort, :name, :active, :shopimg)";
$DB->exec($sql, [':sort'=>$class_sort, ':name'=>$two['two_name'], ':active'=>1, ':shopimg'=>$shopimg]);
						$cid=$DB->lastInsertId();
						$class_sort++;
	          			foreach ($two['data'] as $v) {
	          				if(stripos('['.$v['shopimg'],'assets/img/')){
	          					$shopimg = $url.'/'.$v['shopimg'];
	          				}else{
	          					$shopimg = $v['shopimg'];
	          				}
							$DB->exec("INSERT INTO `pre_tools` (`sort`,`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`close`,`active`,`addtime`) VALUES (:sort,:cid,:name,:price,:cost,:cost2,:prid,:prices,:input,:inputs,:desc,:alert,:shopimg,:value,:is_curl,:curl,:shequ,:goods_id,:goods_type,:goods_param,:repeat,:multi,:min,:max,:validate,:valiserv,:close,:active,NOW())", [':sort'=>$tools_sort, ':cid'=>$cid, ':name'=>$v['name'], ':price'=>$v['price'], ':cost'=>0, ':cost2'=>0, ':prid'=>$prid, ':prices'=>'', ':input'=>$v['input'], ':inputs'=>$v['inputs'], ':desc'=>'', ':alert'=>$v['alert'], ':shopimg'=>$shopimg, ':value'=>$v['min'], ':is_curl'=>2, ':curl'=>'', ':shequ'=>$row['id'], ':goods_id'=>$v['tid'], ':goods_type'=>($v['isfaka']?'1':'0'), ':goods_param'=>'', ':repeat'=>$v['repeat'], ':multi'=>$v['multi'], ':min'=>1, ':max'=>$v['max'], ':validate'=>$v['validate'], ':valiserv'=>$v['valiserv'], ':close'=>$v['close'], ':active'=>$v['active']]);
$tools_sort++;
$add_success++;
}
}
}
}

// 批量记录上架日志
        	foreach ($list['data'] as $one) {
        		foreach ($one['two'] as $two) {
        			foreach ($two['data'] as $v) {
        				$content = "商品上架：{$v['name']}";
        				$DB->exec("INSERT INTO `pre_toollogs` (`content`,`date`,`addtime`,`active`) VALUES (:content, CURDATE(), NOW(), 1)", array(':content' => $content));
        			}
        		}
        	}
			$result=['code'=>1, 'msg'=>$DB->error().'成功添加'.$add_success.'个商品'];
			exit(json_encode($result));
        }else{
        	exit('{"code":-1,"msg":"获取社区失败"}');
    	}

    }else{
        exit('{"code":-1,"msg":"社区不存"}');
    }
break;
case 'getdswgoods': //获取商品图片
	$shequ=intval($_POST['shequ']);
    $re=$DB->query("SELECT * FROM `pre_tools` WHERE shequ=:shequ", [':shequ'=>$shequ]);
    while($res = $re->fetch()){
    	if(strpos('['.$res['shopimg'],'http://') || strpos('['.$res['shopimg'],'https://')){
    		$data[] = array('tid' => $res['tid'] , 'cid' => $res['cid'], 'name' => $res['name'], 'shopimg' => $res['shopimg']);
    	}
    	
    }
    if(count($data)==0){
    	$result['code'] = -1;
    	$result['msg'] = '该社区的商品图，都在本地哦';
    }else{
    	$result['code'] = '0';
    }
    $result['data'] = $data;
	exit(json_encode($result));
break;	
case 'getGoodsList': //获取对接商品列表
	$shequ=intval($_POST['shequ']);
	$is=intval($_POST['is']);
	$goodscid=daddslashes($_POST['goodscid']);
	
	// 支持多个分类ID（逗号分隔）
	$category_ids = null;
	if(isset($_POST['category_id']) && !empty($_POST['category_id'])){
		$category_ids = explode(',', $_POST['category_id']);
		// 过滤空值和无效值
		$category_ids = array_filter($category_ids, function($val){
			return is_numeric($val) && $val > 0;
		});
	}
	
	$row=$DB->getRow("select * from pre_shequ where id=:id limit 1", [':id'=>$shequ]);
$url = ($row['protocol']==1?'https://':'http://') . $row['url'];
$idArr = array();
$idArr2 = array();
if($is==1){
    $re=$DB->query("SELECT * FROM pre_tools WHERE shequ=:shequ", [':shequ'=>$row['id']]);
        while($res = $re->fetch()){
            $idArr[$res['goods_id']] = $res['goods_id'];
        }
	}

	if($row['type'] == 'jiuwu'){
		$list = get_jiuwu_goods_list($url,$row['username'],$row['password'],$idArr,$category_ids);
	}elseif($row['type'] == 'yile'){
		$list = get_yile_goods_list($url,$row['username'],$row['password'],$idArr,$category_ids);
	}elseif($row['type'] == 'zhike'){
		$list = third_call($row['type'], $row, 'goods_list');
	}elseif($row['type'] == 'shangzhanwl'){
		// 商战网支持多选分类
		if($category_ids !== null && is_array($category_ids) && count($category_ids) > 0){
			$list = goods_list_shangzhanwl_cate($url, $row['username'], $row['password'], $category_ids, $idArr);
		}elseif($goodscid == '全部'){
			$list = goods_list_shangzhanwl_cate($url, $row['username'], $row['password'], null, $idArr);
		}elseif($goodscid != ''){
			$list = goods_list_shangzhanwl_cate($url, $row['username'], $row['password'], array($goodscid), $idArr);
		}else{
			$list = goods_list_shangzhanwl_cate($url, $row['username'], $row['password'], null, $idArr);
		}
	}elseif($row['type'] == 'yiciyuan'){
		// 异次元系统
		if($category_ids !== null && is_array($category_ids) && count($category_ids) > 0){
			$list = third_call($row['type'], $row, 'goods_list', [$category_ids]);
		}elseif($goodscid != ''){
			$list = third_call($row['type'], $row, 'goods_list', [[$goodscid]]);
		}else{
			$list = third_call($row['type'], $row, 'goods_list');
		}
		// 过滤已存在的商品
		if(is_array($list) && $is == 1){
			$list = array_filter($list, function($goods) use ($idArr){
				return !isset($idArr[$goods['id']]);
			});
			$list = array_values($list);
		}
	}else{
		// 标准插件系统（如卡卡云、易客等）
		// 获取插件配置
		$getInfo = \lib\Plugin::getConfig("third_" . $row['type']);
		
		// 检查是否支持批量对接
		if(isset($getInfo['batchgoods']) && $getInfo['batchgoods'] == true){
			// 如果选择了分类，则获取指定分类的商品
			if($category_ids !== null && is_array($category_ids) && count($category_ids) > 0){
				$list = array();
				foreach($category_ids as $cid){
					$goods_list = third_call($row['type'], $row, 'goods_list_by_cid', [$cid]);
					if(is_array($goods_list)){
						foreach($goods_list as $goods){
							// 如果开启了"只获取不存在本系统的商品"
							if($is == 1 && isset($idArr[$goods['tid']])){
								continue;
							}
							$list[] = $goods;
						}
					}
				}
			}else{
				// 获取所有商品
				$list = third_call($row['type'], $row, 'batch_goods_list');
				if(is_array($list) && $is == 1){
					// 过滤已存在的商品
					$list = array_filter($list, function($goods) use ($idArr){
						return !isset($idArr[$goods['tid']]);
					});
					$list = array_values($list);
				}
			}
		}else{
			exit('{"code":-1,"msg":"该对接系统不支持批量对接"}');
		}
	}

	if($list === false) exit('{"code":-1,"msg":"请直接在参数名处填写下单页面地址"}');
	if(!is_array($list))$result=array('code'=>-1,'msg'=>$list);
	else $result=array('code'=>0,'msg'=>'succ','type'=>$row['type'],'data'=>$list);
	exit(json_encode($result));

break;
case 'getGoodsParam': //获取对接参数名
$shequ=intval($_POST['shequ']);
$count=intval($_POST['count']);
$limit_sql = '';
$limit_params = [];
if($count != 1000){
    $limit_sql = ' LIMIT ' . $count;
}

$row=$DB->getRow("SELECT * FROM pre_shequ WHERE id=:id limit 1", [':id'=>$shequ]);
if($row['type'] =='zhike'){
    $tools=$DB->getRow("SELECT * FROM pre_tools WHERE shequ=:shequ and goods_id=:goods_id limit 1", [':shequ'=>$row['id'], ':goods_id'=>'694319649']);
}else{
    $tools=$DB->getRow("SELECT * FROM pre_tools WHERE shequ=:shequ and goods_param=:goods_param limit 1", [':shequ'=>$row['id'], ':goods_param'=>'694319649']);
}

if($tools){
    $url = ($row['protocol']==1?'https://':'http://') . $row['url'];
    if($row['type'] == 'jiuwu'){
        $cookie = null;
        $get = get_curl($url.'/index.php?m=Home&c=User&a=login', 'username='.urlencode($row['username']).'&username_password='.urlencode($row['password']), 0, 0, 1);
        if (strpos($get, "登录成功")) {
            if (preg_match_all('/Set-Cookie:\s?([A-Za-z0-9\_=\|]+);/is', $get, $arr2)) {
                foreach ($arr2['1'] as $item) {
                    $cookie .= $item . ';';
                }
                $cookie = base64_encode($cookie);
            }else{
                exit('{"code":-1,"msg":"玖伍社区登录失败"}');
            }
            $re=$DB->query("SELECT * FROM `pre_tools` WHERE shequ=:shequ and goods_param=:goods_param".$limit_sql, array_merge([':shequ'=>$row['id'], ':goods_param'=>'694319649'], $limit_params));
        }
    
    }elseif($row['type'] =='zhike'){
        $re=$DB->query("SELECT * FROM `pre_tools` WHERE shequ=:shequ and goods_id=:goods_id".$limit_sql, array_merge([':shequ'=>$row['id'], ':goods_id'=>'694319649'], $limit_params));
    }else{
        $re=$DB->query("SELECT * FROM `pre_tools` WHERE shequ=:shequ and goods_param=:goods_param".$limit_sql, array_merge([':shequ'=>$row['id'], ':goods_param'=>'694319649'], $limit_params));
    }

}else{
    exit('{"code":-1,"msg":"没有商品需要同步的"}');
}

	

	//通过shua_orders里的xbc_start_num字段判断是否是社区版的彩虹系统
	$issq=$DB->getRow("desc `shua_orders` `xbc_start_num`");
    $success = 0;
    $i = 0;
    $n = 0;
    $aaaaa = '';
    $bbbbb = '';
    while($res = $re->fetch()){

    	if($row['type'] == 'jiuwu'){
	    	$urls = $url.'/index.php?m=Home&c=Goods&a=detail&id='.$res['goods_id'];
	    	$param[] = array("url" => $urls ,"post" => "");
    	}elseif($row['type'] == 'zhike'){

    		$result = third_call($row['type'], $row, 'goods_info', [$res['goods_param']]);
    		$input = $result['input'];
    		$inputs = $result['inputs'];
			//通过shua_orders里的xbc_start_num字段判断是否是社区版的彩虹系统
			if($issq['Field'] == 'xbc_start_num'){
				//是社区
				$value = 1;
				$price = $result['price'];
			}else{
				$value = $result['unitnum'];
				$result['limit_min'] = '1';
				$price = $result['unitnum'] * $result['price'];
			}

			if($row['type'] == 'zhike'){
				$goods_param = $result['id'].'#'.$result['alias'];
			}else{
				$goods_param = '';
			}
	    	$close=$result['close']==0?'0':'1';
	    	$aaaaa .= 'id='.$result['gid'].'_'.$close;
    		if($result['code'] == 0){
    			$sql = "UPDATE `pre_tools` SET goods_id='0',shopimg=:shopimg,price=:price,value=:value,input=:input,inputs=:inputs,goods_param=:goods_param,min=:min,max=:max,close=:close,active='1',`desc`=:desc 
    WHERE shequ=:shequ AND tid=:tid";
$desc = [':desc'=>$result['desc'], ':shopimg'=>$result['image'], ':price'=>$price, ':value'=>$value, ':input'=>$input, ':inputs'=>$inputs, ':goods_param'=>$goods_param, ':min'=>$result['min'], ':max'=>$result['max'], ':close'=>$close, ':shequ'=>$row['id'], ':tid'=>$res['tid']];
	    		$DB->exec($sql,$desc);
	    		$bbbbb .= 'id='.$result['gid'].'_'.$DB->error();
	    		$success++;
            }else{
            	$n++;
            }
    	}elseif($row['type'] == 'yile'){
    		$result = third_call($row['type'], $row, 'goods_info', [$res['goods_id']]);
    		$param = explode('|',$result['paramname']);//paramname: "帐号|密码|学校名称|课程名称"
    		$inputs = '';
    		$input = '';
			for ($i=0; $i < count($param); $i++) { 
	    		if($i == 0){
	    			$input = $param[$i];
	    		}else{
	    			$inputs .= $param[$i].'|';
	    		}
	    	}
	    	$inputs = trim($inputs,'|');
			//通过shua_orders里的xbc_start_num字段判断是否是社区版的彩虹系统
			if($issq['Field'] == 'xbc_start_num'){
				//是社区
				$value = 1;
				$price = $result['price'];
			}else{
				$value = $result['limit_min'];
				$result['limit_min'] = '1';
				$price = $result['limit_min'] * $result['price'];
			}	    	
	    	$close=$result['close']==0?'0':'1';
	    	$aaaaa .= 'id='.$result['gid'].'_'.$close;
    		if($result['code'] == 0){
    			$sql = "UPDATE `pre_tools` SET shopimg=:shopimg,price=:price,value=:value,input=:input,inputs=:inputs,goods_param='',min=:min,max=:max,close=:close,active='1',`desc`=:desc WHERE shequ=:shequ AND goods_id=:goods_id AND goods_param='694319649'";
$desc = [':desc'=>$result['desc'], ':shopimg'=>$result['image'], ':price'=>$price, ':value'=>$value, ':input'=>$input, ':inputs'=>$inputs, ':min'=>$result['limit_min'], ':max'=>$result['limit_max'], ':close'=>$close, ':shequ'=>$row['id'], ':goods_id'=>$result['gid']];
	    		$DB->exec($sql,$desc);
	    		$bbbbb .= 'id='.$result['gid'].'_'.$DB->error();
	    		$success++;
            }else{
            	$n++;
            }
    	}elseif($row['type'] == 'shangzhanwl'){
    		$result = third_call($row['type'], $row, 'goods_info', [$res['goods_id']]);
    		if($result['code'] == 0){
				$close = $result['supply_state']==1?'0':'1';	-	//商品状态 1 上架 2 暂停 3 下架
				$goods_type = $result['type']==1?'1':'0';	//1 卡密商品 2 代充商品
    			$inputs = $result['inputs'] == ''?'':$result['inputs'];
    			$info = $result['recharge_url']==''?$result['info']:'卡密激活地址：'.$result['recharge_url'].'<br>'.$result['info'];
    			$sql = "UPDATE `pre_tools` SET shopimg=:shopimg,price=:price,value='1',input=:input,inputs=:inputs,goods_param='',min='1',max=:max,close=:close,active='1',goods_type=:goods_type,`desc`=:desc WHERE shequ=:shequ AND goods_id=:goods_id AND goods_param='694319649'";
$desc = [':desc'=>$info, ':shopimg'=>$result['img'], ':price'=>$result['price'], ':input'=>$result['input'], ':inputs'=>$inputs, ':max'=>$result['quantity'], ':close'=>$close, ':goods_type'=>$goods_type, ':shequ'=>$row['id'], ':goods_id'=>$result['id']];
	    		$DB->exec($sql,$desc);
	    		$success++;
            }else{
            	$n++;
            }

    	}else{
    		exit('{"code":-1,"msg":"目前仅支持玖伍、亿乐、直客"}');
    	}
    	$i++;
    }

    //
    if($row['type'] == 'jiuwu'){
	    $ret = duo_curl_jiuwu($param,$cookie,$shequ,'50000');//100 毫秒=100000 微秒
		exit('{"code":0,"msg":"'.$ret.'"}');
    }elseif ($row['type'] == 'yile') {
    	exit('{"code":0,"msg":"亿乐，本次获取'.$i.'个，成功 '.$success.' 个，失败 '.$n.' 个"}');
    }elseif ($row['type'] == 'zhike') {
    	exit('{"code":0,"msg":"直客，本次获取'.$i.'个，成功 '.$success.' 个，失败 '.$n.' 个"}');
    }elseif ($row['type'] == 'shangzhanwl') {
    	exit('{"code":0,"msg":"商战网，本次获取'.$i.'个，成功 '.$success.' 个，失败 '.$n.' 个"}');
    }else{
    	exit('{"code":-1,"msg":"目前仅支持玖伍、亿乐"}');
    }


break;	
case 'batchaddgoods':
	$shequ=isset($_POST['shequ'])?intval($_POST['shequ']):exit('{"code":-1,"msg":"no shequ"}');
	$mcid=isset($_POST['mcid'])?$_POST['mcid']:exit('{"code":-1,"msg":"no mcid"}');
	$parent_cid=isset($_POST['parent_cid'])?intval($_POST['parent_cid']):0;
	$prid=isset($_POST['prid'])?intval($_POST['prid']):exit('{"code":-1,"msg":"no prid"}');

	$num_arr = array();
	foreach($_POST['numlist'] as $res){
		$row = json_decode($res, true);
		$num_arr[$row['id']] = $row['value'];

	}
	if(count($_POST['list'])==0)exit('{"code":-1,"msg":"请至少选中一个商品"}');
	$row_shequ=$DB->getRow("SELECT * FROM pre_shequ WHERE id=:id limit 1", [':id'=>$shequ]);

	//通过shua_orders里的xbc_start_num字段判断是否是社区版的彩虹系统
	$issq=$DB->getRow("desc `shua_orders` `xbc_start_num`");
	$sort = $DB->getColumn("select sort from pre_tools order by sort desc limit 1");
	$i = 1;
	$msg = '';
	$add_success = 0;
	$update_success = 0;
	$new_category_count = 0;
	$category_map = array();
	
	foreach($_POST['list'] as $res){
		$row = json_decode($res, true);
		if(!$row || !$row['id'])continue;
		
		// 对于传统系统，调用对应的商品详情获取函数
		if($row_shequ['type'] == 'yile'){
			// 亿乐系统需要调用process_yile_goods_data函数获取完整信息
			$url = ($row_shequ['protocol']==1?'https://':'http://') . $row_shequ['url'];
			$goods_id = $row['id'];
			
			// 调用亿乐商品详情接口获取完整信息
			$detail_url = 'http://' . $row_shequ['url'] . '/openapi/customer/Goods/Show';
			$detail_param = array('goods_id' => intval($goods_id));
			$detail_timestamp = time();
			$detail_token = sha1($row_shequ['username'] . $row_shequ['password'] . '/openapi/customer/Goods/Show' . $detail_timestamp);
			$detail_headers = array(
				'Appid: ' . $row_shequ['username'],
				'AppTimestamp: ' . $detail_timestamp,
				'AppToken: ' . $detail_token
			);
			$detail_ret = get_curl_with_headers_yile($detail_url, json_encode($detail_param), $detail_headers);
			if ($detail_ret = json_decode($detail_ret, true)) {
				if ($detail_ret['code'] === 0 && isset($detail_ret['data'])) {
					$detail = $detail_ret['data'];
					
					// 确保价格是有效的数字
					$price = isset($detail['price']) && is_numeric($detail['price']) ? floatval($detail['price']) : 0;
					
					// 确保最小和最大下单数量是有效的数字
					$minnum = isset($detail['buy_min_limit']) && is_numeric($detail['buy_min_limit']) ? intval($detail['buy_min_limit']) : 1;
					$maxnum = isset($detail['buy_max_limit']) && is_numeric($detail['buy_max_limit']) ? intval($detail['buy_max_limit']) : 0;
					
					// 获取商品图片
					$shopimg = '';
					if(isset($detail['image_urls']) && is_array($detail['image_urls']) && count($detail['image_urls']) > 0){
						$shopimg = $detail['image_urls'][0];
					}elseif(isset($detail['thumb']) && !empty($detail['thumb'])){
						$shopimg = $detail['thumb'];
					}
					
					// 获取商品详细信息
					$input = '';
					$inputs = '';
					$desc = isset($detail['particulars']) ? $detail['particulars'] : '';
					$alert = isset($detail['alert']) ? $detail['alert'] : '';
					
					// 尝试从其他字段获取提示内容
					if(empty($alert)){
						$alert = isset($detail['notice']) ? $detail['notice'] : '';
					}
					if(empty($alert)){
						$alert = isset($detail['tip']) ? $detail['tip'] : '';
					}
					if(empty($alert)){
						$alert = isset($detail['hint']) ? $detail['hint'] : '';
					}
					if(empty($alert)){
						$alert = isset($detail['remark']) ? $detail['remark'] : '';
					}
					
					// 从buy_params获取输入框信息
					if(isset($detail['buy_params']) && is_array($detail['buy_params']) && count($detail['buy_params']) > 0){
						// 获取第一个输入框的标题
						$input = isset($detail['buy_params'][0]['name']) ? $detail['buy_params'][0]['name'] : '';
						if(empty($input)){
							$input = isset($detail['buy_params'][0]['key']) ? $detail['buy_params'][0]['key'] : '';
						}
						
						// 如果有多个输入框，获取所有输入框的标题
						if(count($detail['buy_params']) > 1){
							$input_names = array();
							foreach($detail['buy_params'] as $param){
								$param_name = isset($param['name']) ? $param['name'] : (isset($param['key']) ? $param['key'] : '');
								if(!empty($param_name)){
									$input_names[] = $param_name;
								}
							}
							$inputs = implode('|', $input_names);
						}
					}
					
					// 合并商品信息
					$row['price'] = $price;
					$row['minnum'] = $minnum;
					$row['maxnum'] = $maxnum;
					$row['shopimg'] = $shopimg;
					$row['input'] = $input;
					$row['inputs'] = $inputs;
					$row['desc'] = $desc;
					$row['alert'] = $alert;
				}
			}
		}elseif($row_shequ['type'] == 'jiuwu'){
			// 玖伍系统需要调用get_jiuwu_goods_info函数
			$url = ($row_shequ['protocol']==1?'https://':'http://') . $row_shequ['url'];
			$goods_info = get_jiuwu_goods_info($url, $row['id']);
			if(is_array($goods_info) && $goods_info['code'] == 0){
				// 合并商品信息
				$row = array_merge($row, $goods_info);
			}
		}elseif($row_shequ['type'] == 'zhike'){
			// 直客系统需要调用goods_info获取完整信息，包括goods_param
			$goods_info = third_call($row_shequ['type'], $row_shequ, 'goods_info', [$row['id']]);
			if(is_array($goods_info)){
				// 合并商品信息，包括goods_param
				$row = array_merge($row, $goods_info);
			}
		}elseif($row_shequ['type'] == 'shangzhanwl'){
			// 商战网系统商品列表已经返回了完整信息
			// 这里不需要额外调用
		}elseif($row_shequ['type'] == 'yiciyuan'){
			// 异次元系统需要调用goods_info获取完整信息
			$goods_info = third_call($row_shequ['type'], $row_shequ, 'goods_info', [$row['id']]);
			if(is_array($goods_info)){
				// 合并商品信息
				$row = array_merge($row, $goods_info);
			}
		}
		
		// 确定当前商品要使用的分类ID
		$current_mcid = $mcid;
		
		// 如果是"新建同名分类"，则为每个商品创建对应的原始分类
		if($mcid=='new' && !empty($row['original_cname'])){
			$original_cname = $row['original_cname'];
			
			// 检查分类是否已存在于映射中
			if(!isset($category_map[$original_cname])){
				// 检查数据库中是否已存在该分类
				// 考虑父级分类ID，支持二级分类
				$existing_cid = $DB->getColumn("SELECT cid FROM pre_class WHERE name=:name AND pid=:pid LIMIT 1", [':name'=>$original_cname, ':pid'=>$parent_cid]);
				
				if($existing_cid){
					// 使用已存在的分类
					$category_map[$original_cname] = $existing_cid;
				} else {
					// 创建新分类
					$sort = $DB->getColumn("select sort from pre_class WHERE pid=:pid order by sort desc limit 1", [':pid'=>$parent_cid]);
					// 如果是二级分类，获取父级分类下的最大排序值
					if($sort === null) {
						$sort = 0;
					}
					// 插入分类，支持设置pid作为父级分类ID
					$new_sort = $sort + 1;
					$insert_sql = "INSERT INTO `pre_class` (`pid`, `name`, `sort`, `active`) VALUES (:pid, :name, :sort, 1)";
					$insert_data = [':pid'=>$parent_cid, ':name'=>$original_cname, ':sort'=>$new_sort];
					$DB->exec($insert_sql, $insert_data);
					
					// 获取新创建的分类ID
					$new_cid = $DB->lastInsertId();
					$category_map[$original_cname] = $new_cid;
					$new_category_count++;
				}
			}
			
			// 使用映射中的分类ID
			$current_mcid = $category_map[$original_cname];
		} elseif(!is_numeric($mcid)) {
			// 确保mcid是数字
			$current_mcid = 0;
		}
		
		// 通过shua_orders里的xbc_start_num字段判断是否是社区版的彩虹系统
		if($issq['Field'] == 'xbc_start_num'){
			//是社区
			$value = 1;
		}else{
			$value = $num_arr[$row['id']];
		}
		
		// 直客系统使用与自动同步相同的价格计算和精度处理逻辑
		if($row_shequ['type'] == 'zhike'){
			// 获取成本价
			$cost_price = isset($row['price']) ? floatval($row['price']) : 0;
			
			// 获取默认下单数量 - 使用商品自身的默认数量，支持不同字段名
			$default_num = intval($row['value'] ?? $row['unitnum'] ?? $row['min'] ?? $row['limit_min'] ?? $row['minnum'] ?? 1);
			// 确保默认下单数量至少为1
			if($default_num < 1) {
				$default_num = 1;
			}
			// 对于价格非常小的商品，使用最小下单数量作为默认数量
			if($default_num == 1 && floatval($cost_price) < 0.01) {
				$default_num = intval($row['min'] ?? $row['limit_min'] ?? $row['minnum'] ?? 100);
				if($default_num < 1) {
					$default_num = 100;
				}
			}
			// 固定最小下单数量为 1
			$min_num = 1;
			// 确保默认下单数量至少为最小下单数量
			if($default_num < $min_num) {
				$default_num = $min_num;
			}
			
			// 计算后的价钱 = 成本价 × 默认下单数量
			$price = $cost_price * $default_num;
			
			// 价格精度处理：当计算后的价格小于0.01时，调整下单数量
			if($price < 0.01) {
				// 计算需要的调整倍数
				$adjustment_factor = ceil(0.01 / $price);
				// 确保调整倍数是10的倍数
				$adjustment_factor = ceil($adjustment_factor / 10) * 10;
				// 调整最少下单数量和默认下单数量
				$min_num = $min_num * $adjustment_factor;
				$default_num = $default_num * $adjustment_factor;
				// 重新计算价格
				$price = $cost_price * $default_num;
			}
			
			// 更新商品信息
			$row['price'] = $price;
			$row['minnum'] = $min_num;
			$value = $default_num;
		}else{
			// 其他系统保持原有逻辑
			if($issq['Field'] != 'xbc_start_num'){
				$row['price'] = round($value * $row['price'],2);
			}
		}

		$goods_param = '694319649';
		if($row_shequ['type'] == 'yile'){
			// 保留实际价格、图片和下单数量
			// $row['price'] = 9999;
			// $row['shopimg'] = '';
			// $row['type'] = 0;
			// $row['minnum'] = 0;
			// $row['maxnum'] = 0;
			$value = 1;
		}elseif($row_shequ['type'] == 'zhike'){
			// 使用实际价格，不再强制设置为9999
			// $row['price'] = 9999;
			$row['type'] = 0;
			// 保留实际的最小和最大下单数量
			// $row['minnum'] = 0;
			// $row['maxnum'] = 0;
			// 使用从商品详情中获取的goods_param，格式为goodsSN#alias
			$goods_param = isset($row['goods_param']) ? $row['goods_param'] : $row['id'];
			// 不再将ID强制设置为694319649
			// $row['id'] = '694319649';
			// 直客系统也使用用户选择的默认下单数量
			// $value = 1;
		}elseif($row_shequ['type'] == 'jiuwu'){

		}elseif($row_shequ['type'] == 'shangzhanwl'){
			// 保留实际价格、图片和下单数量
			// $row['price'] = 9999;
			// $row['type'] = 0;
			// $row['minnum'] = '';
			// $row['maxnum'] = '';
			$value = 1;
		}elseif($row_shequ['type'] == 'yiciyuan'){
			// 异次元系统使用实际价格和数量
			$value = 1;
		}else{
			exit('{"code":-1,"msg":"目前仅支持玖伍、亿乐、直客、商战网、异次元"}');
		}

		// 检查商品是否已存在 - 只使用goods_id检查，与自动同步保持一致
		$existing_tool = $DB->getRow("SELECT * FROM pre_tools WHERE shequ=:shequ AND goods_id=:goods_id LIMIT 1", 
			[':shequ'=>$shequ, ':goods_id'=>$row['id']]);
		
		if($existing_tool){
			// 更新现有商品
			$sql = "UPDATE `pre_tools` SET `cid`=:cid, `sort`=:sort, `name`=:name, `price`=:price, `prid`=:prid, `value`=:value, `min`=:min, `max`=:max, `close`=:close, `input`=:input, `inputs`=:inputs, `desc`=:desc, `alert`=:alert, `shopimg`=:shopimg WHERE `tid`=:tid";
			// 根据商品状态设置close字段，0表示开启，1表示关闭
			$close = isset($row['close']) && $row['close'] == true ? 1 : 0;
			// 使用从API获取的商品详细信息
			$input = isset($row['input']) ? $row['input'] : '';
			$inputs = isset($row['inputs']) ? $row['inputs'] : '';
			$desc = isset($row['desc']) ? $row['desc'] : '';
			$alert = isset($row['alert']) ? $row['alert'] : '';
			$shopimg = isset($row['shopimg']) ? $row['shopimg'] : '';
			$data = [':cid'=>$current_mcid, ':sort'=>$sort, ':name'=>$row['name'], ':price'=>$row['price'], ':prid'=>$prid, ':value'=>$value, ':min'=>$row['minnum'], ':max'=>$row['maxnum'], ':close'=>$close, ':input'=>$input, ':inputs'=>$inputs, ':desc'=>$desc, ':alert'=>$alert, ':shopimg'=>$shopimg, ':tid'=>$existing_tool['tid']];
			$DB->exec($sql, $data);
			$update_success++;
			// 记录上架日志
			$content = "商品上架：{$row['name']}";
			$DB->exec("INSERT INTO `pre_toollogs` (`content`,`date`,`addtime`,`active`) VALUES (:content, CURDATE(), NOW(), 1)", array(':content' => $content));
		} else {
			$sort++;
			$sql="INSERT INTO `pre_tools` (`cid`,`sort`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`close`,`active`,`addtime`) VALUES (:cid,:sort,:name,:price,:cost,:cost2,:prid,:prices,:input,:inputs,:desc,:alert,:shopimg,:value,:is_curl,:curl,:shequ,:goods_id,:goods_type,:goods_param,:repeat,:multi,:min,:max,:validate,:valiserv,:close,:active,NOW())";
			// 根据商品状态设置close字段，0表示开启，1表示关闭
			$close = isset($row['close']) && $row['close'] == true ? 1 : 0;
			// 设置active字段为1，表示商品激活
			$active = 1;
			// 使用从API获取的商品详细信息
			$input = isset($row['input']) ? $row['input'] : '';
			$inputs = isset($row['inputs']) ? $row['inputs'] : '';
			$desc = isset($row['desc']) ? $row['desc'] : '';
			$alert = isset($row['alert']) ? $row['alert'] : '';
			$data = [':cid'=>$current_mcid, ':sort'=>$sort, ':name'=>$row['name'], ':price'=>$row['price'], ':cost'=>0, ':cost2'=>0, ':prid'=>$prid, ':prices'=>'', ':input'=>$input, ':inputs'=>$inputs, ':desc'=>$desc, ':alert'=>$alert, ':shopimg'=>$row['shopimg'], ':value'=>$value, ':is_curl'=>2, ':curl'=>null, ':shequ'=>$shequ, ':goods_id'=>$row['id'], ':goods_type'=>$row['type'], ':goods_param'=>$goods_param, ':repeat'=>1, ':multi'=>1, ':min'=>$row['minnum'], ':max'=>$row['maxnum'], ':validate'=>0, ':valiserv'=>null, ':close'=>$close, ':active'=>$active];
			$DB->exec($sql, $data);
			$add_success++;
			// 记录上架日志
			$content = "商品上架：{$row['name']}";
			$DB->exec("INSERT INTO `pre_toollogs` (`content`,`date`,`addtime`,`active`) VALUES (:content, CURDATE(), NOW(), 1)", array(':content' => $content));
		}
	}
	
	// 生成结果消息
	$msg = '成功添加'.$add_success.'个商品，更新'.$update_success.'个商品！';
	if($new_category_count > 0){
		if($parent_cid > 0) {
			$parent_name = $DB->getColumn("SELECT name FROM pre_class WHERE cid=:cid LIMIT 1", [':cid'=>$parent_cid]);
			$msg .= ' 新建了'.$new_category_count.'个二级分类到"'.$parent_name.'"下！';
		} else {
			$msg .= ' 新建了'.$new_category_count.'个一级分类！';
		}
	}
	
	// 针对玖伍社区添加特殊提示
	if($row_shequ['type'] == 'jiuwu' && ($add_success > 0 || $update_success > 0)){
		$msg .= '<br>注意：玖伍社区需要下单参数的，请初始化商品，会自动获取对接参数、商品介绍、下单标题>';
	}
	
	$result=['code'=>0, 'msg'=>$msg];
	exit(json_encode($result));

break;
case 'getDockedTids': //获取已对接商品的ID列表和时间
	$shequ=intval($_GET['shequ']);
	$re=$DB->query("SELECT goods_id, addtime FROM pre_tools WHERE shequ=:shequ AND active=1", [':shequ'=>$shequ]);
	$data = array();
	while($res = $re->fetch()){
		$data[$res['goods_id']] = $res['addtime'];
	}
	$result=['code'=>0, 'msg'=>'succ', 'data'=>$data];
	exit(json_encode($result));
break;
case 'getCategoryList': //获取对接站点商品分类
	$shequ=intval($_POST['shequ']);
	$row=$DB->getRow("select * from pre_shequ where id=:id limit 1", [':id'=>$shequ]);
	$url = ($row['protocol']==1?'https://':'http://') . $row['url'];
	
	if($row['type'] == 'yile'){
		$list = get_yile_category_list($url,$row['username'],$row['password']);
	}elseif($row['type'] == 'jiuwu'){
		$list = get_jiuwu_category_list($url,$row['username'],$row['password']);
	}elseif($row['type'] == 'zhike'){
		$list = third_call($row['type'], $row, 'class_list');
	}elseif($row['type'] == 'shangzhanwl'){
		$list = get_shangzhanwl_category_list($url,$row['username'],$row['password']);
	}elseif($row['type'] == 'yiciyuan'){
		$list = third_call($row['type'], $row, 'class_list');
	}else{
		exit('{"code":-1,"msg":"目前仅支持玖伍、亿乐、直客、商战网、异次元"}');
	}

	if($list === false) exit('{"code":-1,"msg":"获取分类失败"}');
	if(!is_array($list))$result=array('code'=>-1,'msg'=>$list);
	else $result=array('code'=>0,'msg'=>'succ','data'=>$list);
	exit(json_encode($result));

break;

default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}

function get_yile_category_list($url,$user,$pwd){
	// 移除URL中的http://或https://前缀，确保正确构建API路径
	$url = preg_replace('/^(https?:\/\/)/i', '', $url);
	$api_url = 'http://' . $url . '/openapi/customer/Goods/CategoryList';
	$timestamp = time();
	$token = sha1($user . $pwd . '/openapi/customer/Goods/CategoryList' . $timestamp);
	$headers = array(
		'Appid: ' . $user,
		'AppTimestamp: ' . $timestamp,
		'AppToken: ' . $token
	);
	$ret = get_curl_with_headers_yile($api_url, '', $headers);
	if (!$ret = json_decode($ret, true)) {
		return '打开对接网站失败';
	} elseif ($ret['code'] !== 0) {
		return $ret['message'];
	} else {
		$list = array();
		foreach ($ret['data'] as $v) {
			// 添加一级分类
			$list[] = array(
				'cid' => $v['id'],
				'name' => $v['name'],
				'parent_id' => $v['parent_id']
			);
			
			// 添加二级分类
			if(isset($v['parent_infos']) && is_array($v['parent_infos']) && count($v['parent_infos']) > 0){
				foreach($v['parent_infos'] as $sub_v){
					$list[] = array(
						'cid' => $sub_v['id'],
						'name' => '&nbsp;&nbsp;&nbsp;&nbsp;' . $sub_v['name'],
						'parent_id' => $sub_v['parent_id']
					);
				}
			}
		}
		return $list;
	}
}

function get_yile_goods_list($url,$user,$pwd,$listid=array(),$category_ids=null){
	// 移除URL中的http://或https://前缀，确保正确构建API路径
	$url = preg_replace('/^(https?:\/\/)/i', '', $url);
	
	// 初始化商品列表
	$list = array();
	
	// 获取所有分类信息，用于后续为商品添加分类名称
	$categories = array();
	$category_url = 'http://' . $url . '/openapi/customer/Goods/CategoryList';
	$category_timestamp = time();
	$category_token = sha1($user . $pwd . '/openapi/customer/Goods/CategoryList' . $category_timestamp);
	$category_headers = array(
		'Appid: ' . $user,
		'AppTimestamp: ' . $category_timestamp,
		'AppToken: ' . $category_token
	);
	$category_ret = get_curl_with_headers_yile($category_url, '', $category_headers);
	if ($category_ret = json_decode($category_ret, true)) {
		if ($category_ret['code'] === 0 && isset($category_ret['data'])) {
			foreach ($category_ret['data'] as $cat) {
				$categories[$cat['id']] = $cat['name'];
				// 处理二级分类
				if(isset($cat['parent_infos']) && is_array($cat['parent_infos'])) {
					foreach($cat['parent_infos'] as $sub_cat) {
						$categories[$sub_cat['id']] = $sub_cat['name'];
					}
				}
			}
		}
	}
	
	// 如果没有指定分类或分类为空，获取所有商品
	if($category_ids === null || !is_array($category_ids) || count($category_ids) == 0){
		$api_url = 'http://' . $url . '/openapi/customer/Goods/List';
		$param = array('goods_category_id' => null);
		$timestamp = time();
		$token = sha1($user . $pwd . '/openapi/customer/Goods/List' . $timestamp);
		$headers = array(
			'Appid: ' . $user,
			'AppTimestamp: ' . $timestamp,
			'AppToken: ' . $token
		);
		$ret = get_curl_with_headers_yile($api_url, json_encode($param), $headers);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} elseif ($ret['code'] !== 0) {
			return $ret['message'];
		} else {
			$list = process_yile_goods_data($ret['data'], $url, $user, $pwd, $listid, $categories);
		}
	}else{
		// 支持按多个分类筛选商品，为每个分类分别调用API
		foreach($category_ids as $category_id){
			$api_url = 'http://' . $url . '/openapi/customer/Goods/List';
			$param = array('goods_category_id' => intval($category_id));
			$timestamp = time();
			$token = sha1($user . $pwd . '/openapi/customer/Goods/List' . $timestamp);
			$headers = array(
				'Appid: ' . $user,
				'AppTimestamp: ' . $timestamp,
				'AppToken: ' . $token
			);
			$ret = get_curl_with_headers_yile($api_url, json_encode($param), $headers);
			if ($ret = json_decode($ret, true)) {
				if ($ret['code'] === 0 && isset($ret['data'])) {
					$category_goods = process_yile_goods_data($ret['data'], $url, $user, $pwd, $listid, $categories, intval($category_id));
					// 合并商品列表
					$list = array_merge($list, $category_goods);
				}
			}
		}
	}
	
	return $list;
}

// 处理亿乐商品数据的辅助函数
function process_yile_goods_data($data, $url, $user, $pwd, $listid, $categories = array(), $current_category_id = null){
	$list = array();
	foreach ($data as $v) {
		if(count($listid)>0){
			if(isset($listid[$v['id']]))continue;
		}
		
		// 商品列表接口返回的字段有限，需要调用详情接口获取完整信息
		$goods_id = $v['id'];
		$detail_url = 'http://' . $url . '/openapi/customer/Goods/Show';
		$detail_param = array('goods_id' => intval($goods_id));
		$detail_timestamp = time();
		$detail_token = sha1($user . $pwd . '/openapi/customer/Goods/Show' . $detail_timestamp);
		$detail_headers = array(
			'Appid: ' . $user,
			'AppTimestamp: ' . $detail_timestamp,
			'AppToken: ' . $detail_token
		);
		$detail_ret = get_curl_with_headers_yile($detail_url, json_encode($detail_param), $detail_headers);
		if ($detail_ret = json_decode($detail_ret, true)) {
			if ($detail_ret['code'] === 0 && isset($detail_ret['data'])) {
				$detail = $detail_ret['data'];
				
				// 确保价格是有效的数字
				$price = isset($detail['price']) && is_numeric($detail['price']) ? floatval($detail['price']) : 0;
				
				// 确保最小和最大下单数量是有效的数字
				$minnum = isset($detail['buy_min_limit']) && is_numeric($detail['buy_min_limit']) ? intval($detail['buy_min_limit']) : 1;
				$maxnum = isset($detail['buy_max_limit']) && is_numeric($detail['buy_max_limit']) ? intval($detail['buy_max_limit']) : 0;
				
				// 获取商品图片
				$shopimg = '';
				if(isset($detail['image_urls']) && is_array($detail['image_urls']) && count($detail['image_urls']) > 0){
					$shopimg = $detail['image_urls'][0];
				}elseif(isset($detail['thumb']) && !empty($detail['thumb'])){
					$shopimg = $detail['thumb'];
				}
				
				// 获取分类名称
				$original_cname = '';
				if(isset($detail['category_id']) && isset($categories[$detail['category_id']])){
					$original_cname = $categories[$detail['category_id']];
				} elseif($current_category_id && isset($categories[$current_category_id])){
					$original_cname = $categories[$current_category_id];
				}
				
				// 获取商品详细信息
				$input = '';
				$inputs = '';
				$desc = isset($detail['particulars']) ? $detail['particulars'] : '';
				$alert = isset($detail['alert']) ? $detail['alert'] : '';
				
				// 尝试从其他字段获取提示内容
				if(empty($alert)){
					$alert = isset($detail['notice']) ? $detail['notice'] : '';
				}
				if(empty($alert)){
					$alert = isset($detail['tip']) ? $detail['tip'] : '';
				}
				if(empty($alert)){
					$alert = isset($detail['hint']) ? $detail['hint'] : '';
				}
				if(empty($alert)){
					$alert = isset($detail['remark']) ? $detail['remark'] : '';
				}
				
				// 从buy_params获取输入框信息
				if(isset($detail['buy_params']) && is_array($detail['buy_params']) && count($detail['buy_params']) > 0){
					// 获取第一个输入框的标题
					$input = isset($detail['buy_params'][0]['name']) ? $detail['buy_params'][0]['name'] : '';
					if(empty($input)){
						$input = isset($detail['buy_params'][0]['key']) ? $detail['buy_params'][0]['key'] : '';
					}
					
					// 如果有多个输入框，获取所有输入框的标题
					if(count($detail['buy_params']) > 1){
						$input_names = array();
						foreach($detail['buy_params'] as $param){
							$param_name = isset($param['name']) ? $param['name'] : (isset($param['key']) ? $param['key'] : '');
							if(!empty($param_name)){
								$input_names[] = $param_name;
							}
						}
						$inputs = implode('|', $input_names);
					}
				}
				
				$list[] = array(
					'id' => $v['id'],
					'name' => $v['name'],
					'close' => isset($v['is_close']) && $v['is_close'] == 1 ? 1 : 0,
					'price' => $price,
					'minnum' => $minnum,
					'maxnum' => $maxnum,
					'shopimg' => $shopimg,
					'original_cname' => $original_cname,
					'input' => $input,
					'inputs' => $inputs,
					'desc' => $desc,
					'alert' => $alert
				);
			}
		}
	}
	return $list;
}

function get_curl_with_headers_yile($url,$post=0,$headers=array()){
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
function getSign_batch($param, $key){
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

function get_jiuwu_category_list($url,$user,$pwd){
	$url = $url.'/index.php?m=home&c=api&a=user_get_goods_category&Api_UserName='.urlencode($user).'&Api_UserMd5Pass='.md5($pwd);
	$ret = get_curl($url);
	if (!$ret = json_decode($ret, true)) {
		return '打开对接网站失败';
	} elseif ($ret['status'] !== true) {
		return $ret['msg'];
	} else {
		$list = array();
		if(isset($ret['user_goods_category']) && is_array($ret['user_goods_category'])){
			foreach ($ret['user_goods_category'] as $v) {
				$list[] = array(
					'cid' => $v['id'],
					'name' => $v['name'],
					'parent_id' => isset($v['parent_id']) ? $v['parent_id'] : 0
				);
			}
		}
		return $list;
	}
}

function get_jiuwu_goods_list($url,$user,$pwd,$listid=array(),$category_ids=null){
	// 初始化商品列表
	$list = array();
	
	// 获取所有分类信息，用于后续为商品添加分类名称
	$categories = array();
	$category_url = $url.'/index.php?m=home&c=api&a=user_get_goods_category&Api_UserName='.urlencode($user).'&Api_UserMd5Pass='.md5($pwd);
	$category_ret = get_curl($category_url);
	if ($category_ret = json_decode($category_ret, true)) {
		if ($category_ret['status'] === true && isset($category_ret['user_goods_category'])) {
			foreach ($category_ret['user_goods_category'] as $cat) {
				$categories[$cat['id']] = $cat['name'];
			}
		}
	}
	
	// 如果没有指定分类或分类为空，获取所有商品
	if($category_ids === null || !is_array($category_ids) || count($category_ids) == 0){
		$api_url = $url.'/index.php?m=home&c=api&a=user_get_goods_lists_details&Api_UserName='.urlencode($user).'&Api_UserMd5Pass='.md5($pwd);
		$ret = get_curl($api_url);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} elseif ($ret['status'] !== true) {
			return $ret['msg'];
		} else {
			foreach ($ret['user_goods_lists_details'] as $v) {
				if(count($listid)>0){
					if(isset($listid[$v['id']]))continue;
				}
				// 获取分类名称
				$original_cname = '';
				if(isset($v['cate_id']) && isset($categories[$v['cate_id']])){
					$original_cname = $categories[$v['cate_id']];
				}
				
				// 获取商品详细信息
				$goods_info = array(
					'id' => $v['id'],
					'type' => $v['goods_type'],
					'name' => $v['title'],
					'shopimg' => $v['thumb'],
					'minnum' => $v['minbuynum_0'],
					'maxnum' => $v['maxbuynum_0'],
					'price' => $v['user_unitprice'],
					'close' => $v['goods_status'],
					'original_cname' => $original_cname,
					'input' => isset($v['input']) ? $v['input'] : '',
					'inputs' => isset($v['inputs']) ? $v['inputs'] : '',
					'desc' => isset($v['desc']) ? $v['desc'] : '',
					'alert' => isset($v['alert']) ? $v['alert'] : ''
				);
				$list[] = $goods_info;
			}
		}
	}else{
		// 支持按多个分类筛选商品，为每个分类分别调用API
		foreach($category_ids as $category_id){
			$api_url = $url.'/index.php?m=home&c=api&a=user_get_goods_lists_details&Api_UserName='.urlencode($user).'&Api_UserMd5Pass='.md5($pwd).'&category_id='.$category_id;
			$ret = get_curl($api_url);
			if ($ret = json_decode($ret, true)) {
				if ($ret['status'] === true && isset($ret['user_goods_lists_details'])) {
					foreach ($ret['user_goods_lists_details'] as $v) {
						if(count($listid)>0){
							if(isset($listid[$v['id']]))continue;
						}
						// 获取分类名称
						$original_cname = '';
						if(isset($v['cate_id']) && isset($categories[$v['cate_id']])){
							$original_cname = $categories[$v['cate_id']];
						} elseif(isset($categories[$category_id])){
							$original_cname = $categories[$category_id];
						}
						
						// 获取商品详细信息
						$goods_info = array(
							'id' => $v['id'],
							'type' => $v['goods_type'],
							'name' => $v['title'],
							'shopimg' => $v['thumb'],
							'minnum' => $v['minbuynum_0'],
							'maxnum' => $v['maxbuynum_0'],
							'price' => $v['user_unitprice'],
							'close' => $v['goods_status'],
							'original_cname' => $original_cname,
							'input' => isset($v['input']) ? $v['input'] : '',
							'inputs' => isset($v['inputs']) ? $v['inputs'] : '',
							'desc' => isset($v['desc']) ? $v['desc'] : '',
							'alert' => isset($v['alert']) ? $v['alert'] : ''
						);
						$list[] = $goods_info;
					}
				}
			}
		}
	}
	
	return $list;
}
function get_jiuwu_goods_info($url,$goods_id=0,$cookie=0){
	$data = $url;
	//$result['code'] = -1;
	//$data = get_curl($url.'/index.php?m=Home&c=Goods&a=detail&id='.$goods_id, 0, 0, base64_decode($cookie));
	$start = strpos($data, 'action="/index.php?m=home&c=order');
	$end = strpos($data, 'name="pay_type');
	if ($start > 1 && $end > 1) {
		$get = substr($data, $start, $end - $start);
		if (preg_match_all('/name="([a-z0-9A-Z\_\-]+)"/is', $get, $arr)) {
			$param = "";
			foreach ($arr[1] as $k => $item) {
				if ($item == 'need_num_0' || $item == 'goods_id' || $item == 'goods_type' || $item == 'ssnr' || $item == 'qmkg_url' || $item == 'kszp_url' || $item == 'kszy_url' || $item == 'kszp_dwz')continue;
				$param .= $item.'|';
			}
			$param = trim($param, '|');
			preg_match('/现金单价：<\/span><span  title=".*?">(.*?)<\/span>/',$data,$match);
			$result = array(
				'code' => 0,
				'message' => 'succ',
				'price' => $match[1],
				'param' => $param
			);

			//获取下单标题
			$start = strpos($data, 'action="/index.php?m=home&c=order');
			$end = strpos($data, '<span class="fixed-width-right-80">支付方式：</span>');
			$get = substr($data, $start, $end - $start);
			unset($arr);
			unset($item);
			preg_match_all('/class="fixed-width-right-80">(.*?)<\/span>/',$get,$arr);
			$param = "";
			foreach ($arr[1] as $k => $item) {
				if ($item == '接口：' || $item == '说说：' || $item == '下单赞：' || $item == '每条数量：' || $item == '下单数量：' || $item == '下单浏览：' || $item == '下单浏览量：' || $item == '下单方式：'|| $item == '下单喜欢：')continue;
				$param .= $item.'|';
			}
			$param = trim($param, '|');
	    	$input = explode('|',$param);
	    	$param = "";
	    	$zh = '';
	    	for ($i=0; $i < count($input); $i++) { 
	    		if($i == 0){
	    			if($input[$i] == '掌心ID：' || $input[$i] == '作品链接：' || $input[$i] == '歌曲Id：'){
	    				$zh = '作品链接';
	    			}else{
	    				$zh = $input[$i];
	    			}
	    			$result['input'] = str_replace('：', '', $zh);
	    		}else{
	    			if($input[$i] == '说说ID：'){
	    				$zh = '说说ID';
	    			}else{
	    				$zh = $input[$i];
	    			}
	    			$param .= str_replace('：', '', $zh).'|';
	    		}
	    	}
	    	$param = trim($param, '|');
	    	$result['inputs'] = $param;
			//获取商品介绍
			$start = strpos($data, '<div class="col-md-12 banner">');
			$end = strpos($data, '<!--内容-->');
			$get = substr($data, $start, $end - $start);
			$start = '<div class="col-md-12 banner">';
			$left = strpos($get, $start);
			$right = strpos($get, '<!--内容-->',$left);
			$get = substr($get, $left + strlen($start),$right-$left-strlen($start));
			$get = trim($get);
			$get = trim($get,'</div>');
			$get = trim($get);
			$get = trim($get,'</div>');
			$get = trim($get);
			$get = '<'.trim($get,'</div>').'>';
			$result['desc'] = $get;
		} else {
			$result['code'] = -1;
			$result['msg'] = '匹配商品POST数据失败';
		}
	} else {
		$result['code'] = -1;
		$result['msg'] = '获取商品POST数据失败';
	}
	return $result;
}
function duo_curl_jiuwu($param,$cookie,$shequid,$delay=0){
	global $DB;
	$cookie = base64_decode($cookie);
    $ua = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36';
    $queue = curl_multi_init();
    $map = [];
    foreach ($param as $value) {
    	$url = $value['url'];
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
		if($value['post']){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $value['post']);
		}
		if($cookie){
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}		
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_multi_add_handle($queue, $ch);
        $map[(string)$ch] = $url;

        if($delay){
        	usleep($delay);
        }
    }
    $i = 0;
    $n = 0;
    $success = 0;
    $string = '';
    do {
        while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;
        if ($code != CURLM_OK) {
            break;
        }
        while ($done = curl_multi_info_read($queue)) {
            $info = curl_getinfo($done['handle']);
            $error = curl_error($done['handle']);
            $results = curl_multi_getcontent($done['handle']);//返回内容
            $urlarr = parse_url($info['url']);

            $data = get_jiuwu_goods_info($results);

            if($data['code'] == 0){
            	$id = getSubstr($urlarr['query'].'&','&id=','&');
            	$string .= $data['desc'].'<br><hr><br>'.$id;
	    		$sql = "UPDATE `pre_tools` SET input=:input,inputs=:inputs,goods_param=:goods_param,close='0',active='1',`desc`=:desc WHERE shequ=:shequ AND goods_id=:goods_id AND goods_param='694319649'";
$data_params = [':desc'=>$data['desc'], ':input'=>$data['input'], ':inputs'=>$data['inputs'], ':goods_param'=>$data['param'], ':shequ'=>$shequid, ':goods_id'=>$id];
$DB->exec($sql, $data_params);
	    		$success++;
            }else{
            	$n++;
            }
           $i++;
            curl_multi_remove_handle($queue, $done['handle']);
            curl_close($done['handle']);
        }
        if ($active > 0) {
            curl_multi_select($queue, 0.5);
        }
    } while ($active);
    curl_multi_close($queue);
    $ret = '本次获取'.$i.'个，成功 '.$success.' 个，失败 '.$n.' 个';
    return $ret;
}
function file_download($url,$path=0){
	$state = @file_get_contents($url,0,null,0,1);//获取网络资源的字符内容
    if($state){
    	if($path){
			$filename = $path;//文件名称生成
    	}else{
    		$filename = date("dMYHis").'.jpg';//文件名称生成
    	}
	    ob_start();//打开输出
	    readfile($url);//输出图片文件
	    $img = ob_get_contents();//得到浏览器输出
	    ob_end_clean();//清除输出并关闭
	    $size = strlen($img);//得到图片大小
	    $fp2 = @fopen($filename, "a");       
	    fwrite($fp2, $img);//向当前目录写入图片文件，并重新命名
	    fclose($fp2);       
	    return true;
    }else{       
       return false;
    }
}

function goods_list_shangzhanwl_cate($url,$user,$pwd,$cid,$listid=array()){
	// 初始化商品列表
	$list = array();
	
	// 获取所有分类信息，用于后续为商品添加分类名称
	$categories = array();
	$category_url = $url.'/api.php/Client/categoryList';
	$category_param = array('customerid'=>$user);
	$category_sign = md5($user . $pwd);
	$category_param['sign'] = $category_sign;
	$category_post = http_build_query($category_param);
	$category_ret = get_curl($category_url, $category_post);
	if ($category_ret = json_decode($category_ret, true) && $category_ret['code'] == 1000) {
		foreach ($category_ret['data'] as $cat) {
			$categories[$cat['id']] = $cat['name'];
		}
	}
	
	// 如果没有指定分类或分类为空，获取所有商品
	if($cid === null || !is_array($cid) || count($cid) == 0){
		$api_url = $url.'/api.php/Client/goodsList';
		$param = array('customerid'=>$user);
		$sign = md5($user . $pwd);
		$param['sign'] = $sign;
		$post = http_build_query($param);
		$ret = get_curl($api_url, $post);
		if (!$ret = json_decode($ret, true)) {
			return '打开对接网站失败';
		} else if ($ret['code'] != 1000) {
			return $ret['info'];
		} else {
			foreach ($ret['data'] as $v) {
				if(count($listid)>0){
					if(isset($listid[$v['id']]))continue;
				}
				// 获取分类名称
				$original_cname = '';
				if(isset($v['cate_id']) && isset($categories[$v['cate_id']])){
					$original_cname = $categories[$v['cate_id']];
				}
				
				// 获取商品详细信息
				$goods_info = array(
					'id' => $v['id'],
					'name' => $v['name'],
					'type' => $v['type'],
					'price' => $v['price'],
					'stock_state' => $v['stock_state'],
					'supply_state' => $v['supply_state'],
					'stock_num' => $v['stock_num'],
					'original_cname' => $original_cname,
					'input' => isset($v['input']) ? $v['input'] : '',
					'inputs' => isset($v['inputs']) ? $v['inputs'] : '',
					'desc' => isset($v['info']) ? $v['info'] : '',
					'alert' => isset($v['alert']) ? $v['alert'] : '',
					'minnum' => isset($v['quantity']) ? 1 : 1,
					'maxnum' => isset($v['quantity']) ? $v['quantity'] : 0,
					'shopimg' => isset($v['img']) ? $v['img'] : ''
				);
				$list[] = $goods_info;
			}
		}
	}else{
		// 支持按多个分类筛选商品，为每个分类分别调用API
		foreach($cid as $category_id){
			$api_url = $url.'/api.php/Client/getCateGoods';
			$param = array('customerid'=>$user, 'cate_id'=>$category_id);
			$sign = md5($user . $category_id. $pwd);
			$param['sign'] = $sign;
			$post = http_build_query($param);
			$ret = get_curl($api_url, $post);
			if ($ret = json_decode($ret, true)) {
				if ($ret['code'] == 1000 && isset($ret['data'])) {
					foreach ($ret['data'] as $v) {
						if(count($listid)>0){
							if(isset($listid[$v['id']]))continue;
						}
						// 获取分类名称
						$original_cname = '';
						if(isset($v['cate_id']) && isset($categories[$v['cate_id']])){
							$original_cname = $categories[$v['cate_id']];
						} elseif(isset($categories[$category_id])) {
							$original_cname = $categories[$category_id];
						}
						
						// 获取商品详细信息
						$goods_info = array(
							'id' => $v['id'],
							'name' => $v['name'],
							'type' => $v['type'],
							'price' => $v['price'],
							'stock_state' => $v['stock_state'],
							'supply_state' => $v['supply_state'],
							'stock_num' => $v['stock_num'],
							'original_cname' => $original_cname,
							'input' => isset($v['input']) ? $v['input'] : '',
							'inputs' => isset($v['inputs']) ? $v['inputs'] : '',
							'desc' => isset($v['info']) ? $v['info'] : '',
							'alert' => isset($v['alert']) ? $v['alert'] : '',
							'minnum' => isset($v['quantity']) ? 1 : 1,
							'maxnum' => isset($v['quantity']) ? $v['quantity'] : 0,
							'shopimg' => isset($v['img']) ? $v['img'] : ''
						);
						$list[] = $goods_info;
					}
				}
			}
		}
	}
	
	return $list;
}

function get_shangzhanwl_category_list($url,$user,$pwd){
	$url = $url.'/api.php/Client/categoryList';
	$param = array('customerid'=>$user);
	$sign = md5($user . $pwd);
	$param['sign'] = $sign;
	$post = http_build_query($param);
	$ret = get_curl($url, $post);
	if (!$ret = json_decode($ret, true)) {
		return '打开对接网站失败';
	} else if ($ret['code'] != 1000) {
		return $ret['info'];
	} else {
		$list = array();
		if(isset($ret['data']) && is_array($ret['data'])){
			foreach ($ret['data'] as $v) {
				$list[] = array(
					'cid' => $v['id'],
					'name' => $v['name'],
					'parent_id' => isset($v['parent_id']) ? $v['parent_id'] : 0
				);
			}
		}
		return $list;
	}
}