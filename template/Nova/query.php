<?php
if(!defined('IN_CRONLITE'))exit();

function display_zt($zt){
	if($zt==1)return '<span style="color:#00B42A">已完成</span>';
	elseif($zt==2)return '<span style="color:#FF7D00">正在处理</span>';
	elseif($zt==3)return '<span style="color:#F53F3F">异常</span>';
	elseif($zt==4)return '<span style="color:#999">已退单</span>';
	else return '<span style="color:#165DFF">待处理</span>';
}

if($islogin2==1){
	$cookiesid = $userrow['zid'];
}

$style_map = [
	'1' => ['primary'=>'#165DFF','primary_hover'=>'#0E42D2','primary_light'=>'#E8F3FF'],
	'2' => ['primary'=>'#00B42A','primary_hover'=>'#009A29','primary_light'=>'#E8FFEA'],
	'3' => ['primary'=>'#FF7D00','primary_hover'=>'#D25F00','primary_light'=>'#FFF3E8'],
	'4' => ['primary'=>'#722ED1','primary_hover'=>'#531DAB','primary_light'=>'#F3E8FF'],
	'5' => ['primary'=>'#F53F3F','primary_hover'=>'#CB2634','primary_light'=>'#FFECE8'],
];
$tpl_style = isset($conf['template_style']) && isset($style_map[$conf['template_style']]) ? $conf['template_style'] : '1';
$theme = $style_map[$tpl_style];

$data=isset($_GET['data'])?trim(daddslashes($_GET['data'])):'';
$page=isset($_GET['page'])?intval($_GET['page']):1;
if(!empty($data)){
	if(strlen($data)==17 && is_numeric($data))$sql=" A.`tradeno`='{$data}'";
	else $sql=" A.`input`='{$data}'";
	if($conf['queryorderlimit']==1)$sql.=" AND A.`userid`='$cookiesid'";
	$link='&data='.urlencode($data);
}else{
	$sql=" A.`userid`='{$cookiesid}'";
	$link='';
}
$limit = 10;
$start = $limit * ($page-1);
$total = $DB->getColumn("SELECT count(*) FROM `pre_orders` A WHERE{$sql} ");
$total_page = ceil($total/$limit);
$sql = "SELECT A.*,B.`name` FROM `pre_orders` A LEFT JOIN `pre_tools` B ON A.`tid`=B.`tid` WHERE{$sql} ORDER BY A.`id` DESC LIMIT {$start},{$limit}";
$rs=$DB->query($sql);
$record=array();
while($res = $rs->fetch()){
	$record[]=array('id'=>$res['id'],'tid'=>$res['tid'],'input'=>$res['input'],'money'=>$res['money'],'name'=>$res['name'],'value'=>$res['value'],'addtime'=>$res['addtime'],'endtime'=>$res['endtime'],'result'=>$res['result'],'status'=>$res['status'],'djzt'=>$res['djzt'],'tradeno'=>$res['tradeno']);
}

$hometitle = '订单查询 - '.$conf['sitename'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
	<title><?php echo $hometitle?></title>
	<?php if(!empty($conf['favicon'])){?>
	<link rel="icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
	<?php }else{?>
	<link rel="icon" href="assets/img/favicon/favicon.ico" type="image/x-icon" />
	<?php }?>
	<link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	<link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
	<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
	<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
	<style>
	:root{--primary:<?php echo $theme['primary']?>;--primary-hover:<?php echo $theme['primary_hover']?>;--primary-light:<?php echo $theme['primary_light']?>}
	*{margin:0;padding:0;box-sizing:border-box}
	body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Hiragino Sans GB","Microsoft YaHei",sans-serif;background:#F5F6FA;color:#333;line-height:1.6}
	a{text-decoration:none;color:inherit}
	.nova-header{background:#1F2937;color:#fff;position:sticky;top:0;z-index:1000;box-shadow:0 2px 8px rgba(0,0,0,.1)}
	.nova-header-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;padding:0 20px;height:60px}
	.nova-logo{display:flex;align-items:center;gap:10px;font-size:20px;font-weight:700}
	.nova-logo img{height:32px;border-radius:6px}
	.nova-nav{display:flex;gap:8px}
	.nova-nav a{padding:8px 14px;border-radius:6px;font-size:14px}
	.nova-nav a:hover{background:rgba(255,255,255,.1)}
	.nova-container{max-width:1000px;margin:0 auto;padding:24px 20px}
	.nova-card{background:#fff;border-radius:16px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.04)}
	.nova-search{display:flex;gap:10px;margin-bottom:24px}
	.nova-search input{flex:1;padding:12px 16px;border:1px solid #E5E6EB;border-radius:10px;font-size:14px;outline:none}
	.nova-search input:focus{border-color:var(--primary)}
	.nova-search button{padding:12px 28px;background:var(--primary);color:#fff;border:none;border-radius:10px;font-size:14px;cursor:pointer}
	.nova-search button:hover{background:var(--primary-hover)}
	.nova-table{width:100%;border-collapse:collapse}
	.nova-table th{background:#F7F8FA;padding:12px;text-align:left;font-size:13px;color:#666;font-weight:500;border-bottom:1px solid #eee}
	.nova-table td{padding:14px 12px;font-size:14px;border-bottom:1px solid #f0f0f0}
	.nova-table tr:hover td{background:#FAFBFC}
	.nova-empty{text-align:center;padding:50px 20px;color:#999}
	.nova-empty i{font-size:40px;opacity:.4;margin-bottom:10px}
	.nova-pagination{display:flex;justify-content:center;gap:6px;margin-top:24px}
	.nova-pagination a{padding:6px 12px;border:1px solid #E5E6EB;border-radius:6px;font-size:13px;color:#666}
	.nova-pagination a.active{background:var(--primary);color:#fff;border-color:var(--primary)}
	.nova-result-box{background:#F7F8FA;border-radius:8px;padding:12px;font-size:13px;color:#555;white-space:pre-wrap;word-break:break-all;margin-top:8px}
	.nova-detail-btn{color:var(--primary);cursor:pointer;font-size:13px}
	</style>
</head>
<body>

<header class="nova-header">
	<div class="nova-header-inner">
		<a href="./" class="nova-logo">
			<img src="<?php echo $logo?>" alt="logo" onerror="this.style.display='none'">
			<span><?php echo $conf['sitename']?></span>
		</a>
		<nav class="nova-nav">
			<a href="./">首页</a>
			<a href="./?mod=query" style="background:var(--primary)">订单查询</a>
		</nav>
	</div>
</header>

<div class="nova-container">
	<div class="nova-card">
		<h2 style="font-size:20px;font-weight:600;margin-bottom:16px"><i class="fa fa-search" style="color:var(--primary)"></i> 订单查询</h2>
		<form method="get" action="">
			<input type="hidden" name="mod" value="query">
			<div class="nova-search">
				<input type="text" name="data" value="<?php echo htmlspecialchars($data)?>" placeholder="请输入订单号或下单账号（QQ/手机号等）">
				<button type="submit"><i class="fa fa-search"></i> 查询</button>
			</div>
		</form>

		<?php if(!empty($record)){?>
		<div style="overflow-x:auto">
		<table class="nova-table">
			<thead>
				<tr>
					<th>订单号</th>
					<th>商品</th>
					<th>下单账号</th>
					<th>金额</th>
					<th>状态</th>
					<th>下单时间</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($record as $r){?>
				<tr>
					<td style="font-family:monospace;font-size:12px"><?php echo $r['tradeno']?></td>
					<td><?php echo htmlspecialchars($r['name'])?></td>
					<td><?php echo htmlspecialchars($r['input'])?></td>
					<td style="color:var(--primary);font-weight:600">¥<?php echo $r['money']?></td>
					<td><?php echo display_zt($r['status'])?></td>
					<td style="font-size:12px;color:#999"><?php echo $r['addtime']?></td>
					<td>
						<?php if(!empty($r['result'])){?>
						<span class="nova-detail-btn" onclick="showResult(<?php echo $r['id']?>)">查看详情</span>
						<div id="result_<?php echo $r['id']?>" style="display:none"></div>
						<script>document.getElementById('result_<?php echo $r['id']?>').dataset.result=<?php echo json_encode($r['result'])?>;</script>
						<?php }else{?>
						<span style="color:#ccc">-</span>
						<?php }?>
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		</div>

		<?php if($total_page>1){?>
		<div class="nova-pagination">
			<?php for($i=1;$i<=$total_page;$i++){?>
			<a href="./?mod=query<?php echo $link?>&page=<?php echo $i?>" class="<?php echo $i==$page?'active':''?>"><?php echo $i?></a>
			<?php }?>
		</div>
		<?php }?>

		<?php }else{?>
		<div class="nova-empty">
			<i class="fa fa-inbox"></i>
			<p><?php echo !empty($data)?'没有找到相关订单':'请输入订单号或下单账号查询'?></p>
		</div>
		<?php }?>
	</div>
</div>

<script>
function showResult(id){
	var el=document.getElementById('result_'+id);
	layer.open({
		type:1,title:'订单详情',area:['500px','400px'],
		content:'<div style="padding:16px"><div class="nova-result-box">'+(el.dataset.result||'无返回信息')+'</div></div>'
	});
}
</script>
</body>
</html>

