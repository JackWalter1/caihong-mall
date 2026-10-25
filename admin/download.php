<?php
include("../includes/common.php");

if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

if($_GET['act']=='kms'){
adminpermission('faka', 2);
$params = array();
if(isset($_GET['tid'])){
	$tid=intval($_GET['tid']);
	$sql="tid=:tid";
	$params[':tid'] = $tid;
}elseif(isset($_GET['orderid'])){
	$orderid=intval($_GET['orderid']);
	$sql="orderid=:orderid";
	$params[':orderid'] = $orderid;
}elseif(isset($_GET['kid'])) {
	$kid=intval($_GET['kid']);
	$sql="kid=:kid";
	$params[':kid'] = $kid;
}else{
	$sql="1";
}
if(isset($_GET['use']) && $_GET['use']==1){
	$sql.= " and orderid!=0";
}elseif(isset($_GET['use']) && $_GET['use']==0){
	$sql.= " and orderid=0";
}
$limit = "";
if(isset($_GET['num'])){
    $num = intval($_GET['num']);
    $limit = " limit " . $num;
}
$rs=$DB->query("SELECT * FROM pre_faka WHERE {$sql} order by kid asc{$limit}", $params);
$data='';
while($res = $rs->fetch())
{
	$data.=($res['pw']?$res['km'].' '.$res['pw']:$res['km'])."\r\n";
	if($_GET['isuse']==1&&$_GET['use']==0){
		$DB->exec("update `pre_faka` set orderid=1,usetime=NOW() where `kid`=:kid", array(':kid' => $res['kid']));
	}
}

}else{
adminpermission('order', 2);
$tid=intval($_GET['tid']);
$cid=intval($_GET['cid']);
$status=intval($_GET['status']);
$sign=intval($_GET['sign']);

// 验证排序方式，防止SQL注入
$orderby = ($_GET['orderby']==1) ? "desc" : "asc";
$allowed_orderby = array("asc", "desc");
if(!in_array($orderby, $allowed_orderby)){
	$orderby = "asc";
}

$params = array(':status' => $status);
$values = array();

if($tid>0){
	$tool=$DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid limit 1", array(':tid' => $tid));
	$values[$tid]=$tool['value']>0?$tool['value']:1;
	$sql="tid=:tid";
	$params[':tid'] = $tid;
}else{
	$res_tools = $DB->getAll("SELECT tid,value FROM pre_tools WHERE cid=:cid", array(':cid' => $cid));
	$tids = array();
	foreach($res_tools as $res){
		$values[$res['tid']]=$res['value']>0?$res['value']:1;
		$tids[] = $res['tid'];
	}
	if($tids){
		// 使用IN查询的参数化方式
		$in_params = array();
		foreach($tids as $i => $val) {
		    $in_params[] = ":tid_$i";
		    $params[":tid_$i"] = $val;
		}
		$in_clause = implode(", ", $in_params);
		$sql="tid IN ($in_clause)";
	}else{
		$sql="1";
	}
}

// 处理时间范围查询，使用参数化查询
if(!empty($_GET['starttime'])){
	$starttime = $_GET['starttime'] . ' 00:00:00';
	$sql.=" AND addtime>=:starttime";
	$params[':starttime'] = $starttime;
}
if(!empty($_GET['endtime'])){
	$endtime = $_GET['endtime'] . ' 23:59:59';
	$sql.=" AND addtime<=:endtime";
	$params[':endtime'] = $endtime;
}

$date=date("Y-m-d");
$data='';

$rs=$DB->query("SELECT * FROM pre_orders WHERE {$sql} and status=:status order by id {$orderby} limit 1000", $params);

while($row = $rs->fetch())
{
	$data.=$row['input'] . ($row['input2']?'----'.$row['input2']:null) . ($row['input3']?'----'.$row['input3']:null) . ($row['input4']?'----'.$row['input4']:null) . ($row['input5']?'----'.$row['input5']:null) . '----' . $row['value']*$values[$row['tid']]."\r\n";
	if($sign>0){
		$DB->exec("update `pre_orders` set status=:sign where `id`=:id", array(':sign' => $sign, ':id' => $row['id']));
	}
}
}

$file_name='output_'.$tid.'_'.$date.'__'.time().'.txt';
$file_size=strlen($data);
header("Content-Description: File Transfer");
header("Content-Type:application/force-download");
header("Content-Length: {$file_size}");
header("Content-Disposition:attachment; filename={$file_name}");
echo $data;
?>