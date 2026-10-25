<?php
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

@header('Content-Type: application/json; charset=UTF-8');

if(!checkRefererHost())exit('{"code":403}');

switch($act){
case 'addClass':	adminpermission('shop', 2);
	$name=trim($_POST['name']);
	if($name==='')
		exit('{"code":-1,"msg":"分类名不能为空"}');
	// 获取pid参数，默认为0（一级分类）
	$pid=intval($_POST['pid']);
	// 如果是子分类，检查父分类是否存在
	if($pid>0){
		$parent=$DB->getRow("select * from pre_class where cid=:pid limit 1", array(':pid' => $pid));
		if(!$parent)
			exit('{"code":-1,"msg":"父分类不存在"}');
	}
	// 检查分类名称是否已存在
	$rows=$DB->getRow("select * from pre_class where name=:name and pid=:pid limit 1", array(':name' => $name, ':pid' => $pid));
	if($rows)
		exit('{"code":-1,"msg":"当前分类名称已存在"}');
	$sort = $DB->getColumn("select sort from pre_class order by sort desc limit 1");
	$sql="insert into `pre_class` (`name`,`sort`,`active`,`pid`) values (:name,:sort,:active,:pid)";
	if($DB->exec($sql, array(':name' => $name, ':sort' => $sort+1, ':active' => 1, ':pid' => $pid))!==false){
		$cid=$DB->lastInsertId();
		exit('{"code":0,"msg":"添加分类成功！"}');
	}else
		exit('{"code":-1,"msg":"添加分类失败！'.$DB->error().'"}');
break;
case 'editClass':	adminpermission('shop', 2);
	$cid=intval($_GET['cid']);
	$rows=$DB->getRow("select * from pre_class where cid=:cid limit 1", array(':cid' => $cid));
	if(!$rows)
		exit('{"code":-1,"msg":"分类不存在"}');
	$name=trim($_POST['name']);
	if($name==='')
		exit('{"code":-1,"msg":"分类名不能为空"}');
	if($DB->exec("update pre_class set name=:name where cid=:cid", array(':name' => $name, ':cid' => $cid))!==false)
		exit('{"code":0,"msg":"修改分类成功！"}');
	else
		exit('{"code":-1,"msg":"修改分类失败！'.$DB->error().'"}');
break;
case 'delClass':	adminpermission('shop', 2);
	$cid=intval($_GET['cid']);
	// 检查是否有子分类
	$subClasses = $DB->getColumn("select count(*) from pre_class where pid=:pid", array(':pid' => $cid));
	if($subClasses > 0){
		exit('{"code":-1,"msg":"请先删除该分类下的所有子分类"}');
	}
	// 删除分类下的所有商品
	$DB->exec("DELETE FROM pre_tools WHERE cid=:cid", array(':cid' => $cid));
	// 删除分类
	$sql="DELETE FROM pre_class WHERE cid=:cid";
	if($DB->exec($sql, array(':cid' => $cid))!==false)
		exit('{"code":0,"msg":"删除分类成功！"}');
	else
		exit('{"code":-1,"msg":"删除分类失败！'.$DB->error().'"}');
break;
case 'editClassAll':	adminpermission('shop', 2);
	if(!isset($_POST['name']) || !is_array($_POST['name']))
		exit('{"code":-1,"msg":"参数错误"}');
	foreach($_POST['name'] as $cid=>$name){
		if(isset($_POST['sort'][$cid]) && $_POST['sort'][$cid]>0){
			$sort = intval($_POST['sort'][$cid]);
			$DB->exec("update pre_class set name=:name,sort=:sort where cid=:cid", array(':name' => $name, ':sort' => $sort, ':cid' => $cid));
		}else{
			$DB->exec("update pre_class set name=:name where cid=:cid", array(':name' => $name, ':cid' => $cid));
		}
	}
	exit('{"code":0,"msg":"修改分类成功！"}');
break;
case 'editClassImages':	adminpermission('shop', 2);
	foreach($_POST['img'] as $k=>$v){
		$DB->exec("update pre_class set shopimg=:shopimg where cid=:cid", array(':shopimg' => $v, ':cid' => $k));
	}
	exit('{"code":0,"msg":"修改分类成功！"}');
break;
case 'getClassImage':	$cid=intval($_GET['cid']);
	$rows=$DB->getRow("select shopimg from pre_tools where cid=:cid and shopimg is not null limit 1", array(':cid' => $cid));
	if(!$rows)
		exit('{"code":-1,"msg":"分类不存在"}');
	exit('{"code":0,"msg":"succ","url":"'.$rows['shopimg'].'"}');
break;

case 'setBlock':	adminpermission('shop', 2);
	$cid=intval($_POST['cid']);
	$data=trim($_POST['data']);
	if($DB->exec("update pre_class set block=:block where cid=:cid", array(':block' => $data, ':cid' => $cid))!==false)
		exit('{"code":0,"msg":"设置成功"}');
	else
		exit('{"code":-1,"msg":"设置失败！'.$DB->error().'"}');
break;
case 'getBlock':	$cid=intval($_POST['cid']);
	$rows=$DB->getRow("select * from pre_class where cid='$cid' limit 1");
	if(!$rows)
		exit('{"code":-1,"msg":"当前分类不存在！"}');
	$result=array("code"=>0,"msg"=>"succ","data"=>$rows['block']);
	exit(json_encode($result));
break;
case 'setBlockPay':	adminpermission('shop', 2);
	$cid=intval($_POST['cid']);
	$paytype=implode(',',$_POST['paytype']);
	if($DB->exec("update pre_class set blockpay=:blockpay where cid=:cid", array(':blockpay' => $paytype, ':cid' => $cid))!==false)
		exit('{"code":0,"msg":"设置成功"}');
	else
		exit('{"code":-1,"msg":"设置失败！'.$DB->error().'"}');
break;
case 'getBlockPay':	$cid=intval($_POST['cid']);
	$rows=$DB->getRow("select * from pre_class where cid=:cid limit 1", array(':cid' => $cid));
	if(!$rows)
		exit('{"code":-1,"msg":"当前分类不存在！"}');
	$result=array("code"=>0,"msg"=>"succ","data"=>explode(',',$rows['blockpay']));
	exit(json_encode($result));
break;
case 'setClass': //分类上下架
	adminpermission('shop', 2);
	$cid=intval($_GET['cid']);
	$active=intval($_GET['active']);
	if($DB->exec("update pre_class set active=:active where cid=:cid", array(':active' => $active, ':cid' => $cid))!==false)
		exit('{"code":0,"msg":"succ"}');
	else
		exit('{"code":-1,"msg":"设置失败！'.$DB->error().'"}');
break;
case 'setClassSort': //排序操作
	adminpermission('shop', 2);
	$cid=intval($_GET['cid']);
	$sort=intval($_GET['sort']);
	if(setClassSort($cid,$sort)){
		exit('{"code":0,"msg":"succ"}');
	}else{
		exit('{"code":-1,"msg":"失败"}');
	}
break;
case 'setDiscount': //设置商品分类折扣
	adminpermission('shop', 2);
	$cid=intval($_POST['cid']);
	$discount=trim($_POST['discount']);
	if($discount==''){
		$DB->exec("update pre_class set discount=100 where cid=:cid", array(':cid' => $cid));
	}else{
		$discount=round($discount);
		$DB->exec("update pre_class set discount=:discount where cid=:cid", array(':discount' => $discount, ':cid' => $cid));
	}
	exit('{"code":0,"msg":"设置折扣成功"}');
break;
case 'batchOperation': //批量操作分类
	adminpermission('shop', 2);
	$aid=intval($_POST['aid']);
	$checkbox=isset($_POST['checkbox']) && is_array($_POST['checkbox']) ? $_POST['checkbox'] : array();
	if(empty($checkbox))
		exit('{"code":-1,"msg":"请至少选择一个分类"}');
	$i=0;
	$skipped=0;
	foreach($checkbox as $cid){
		$cid=intval($cid);
		if($aid==1){
			if($DB->exec("update pre_class set active=1 where cid=:cid limit 1", array(':cid' => $cid))!==false)$i++;
		}elseif($aid==2){
			if($DB->exec("update pre_class set active=0 where cid=:cid limit 1", array(':cid' => $cid))!==false)$i++;
		}elseif($aid==3){
			// 批量删除时跳过仍有子分类的分类，避免产生孤儿子分类
			$sub=$DB->getColumn("select count(*) from pre_class where pid=:pid", array(':pid' => $cid));
			if($sub>0){
				$skipped++;
				continue;
			}
			$DB->exec("DELETE FROM pre_tools WHERE cid=:cid", array(':cid' => $cid));
			if($DB->exec("DELETE FROM pre_class WHERE cid=:cid limit 1", array(':cid' => $cid))!==false)$i++;
		}
	}
	$msg='成功改变'.$i.'个分类';
	if($skipped>0)$msg.='，'.$skipped.'个分类因存在子分类被跳过';
	exit('{"code":0,"msg":"'.$msg.'"}');
break;

// 设置二级分类提示语 - 教主修改，博客地址：6v6.ren Q群：941535592
case 'setNotice':
	adminpermission('shop', 2);
	$cid=intval($_POST['cid']);
	$notice=trim($_POST['notice']);
	// 防止XSS攻击，对提示语进行转义
	$notice=htmlspecialchars($notice, ENT_QUOTES, 'UTF-8');
	if($DB->exec("update pre_class set notice=:notice where cid=:cid", array(':notice' => $notice, ':cid' => $cid))!==false)
		exit('{"code":0,"msg":"设置提示语成功！"}');
	else
		exit('{"code":-1,"msg":"设置失败！'.$DB->error().'"}');
break;

// 获取二级分类提示语 - 教主修改，博客地址：6v6.ren Q群：941535592
case 'getNotice':
	$cid=intval($_POST['cid']);
	$rows=$DB->getRow("select notice from pre_class where cid=:cid limit 1", array(':cid' => $cid));
	if(!$rows)
		exit('{"code":-1,"msg":"当前分类不存在！"}');
	$result=array("code"=>0,"msg"=>"succ","data"=>$rows['notice']);
	exit(json_encode($result));
break;

default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}