<?php
/**
 * 商品推荐管理
**/
include("../includes/common.php");
$title='商品推荐';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

?>
    <div class="col-md-12 center-block" style="float: none;">
<?php
adminpermission('shop', 1);

// 直接创建表（IF NOT EXISTS 安全）
$DB->exec("CREATE TABLE IF NOT EXISTS `pre_recommend` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`tid` int(11) unsigned NOT NULL,
	`sort` int(11) unsigned NOT NULL DEFAULT 0,
	`addtime` datetime DEFAULT NULL,
	`active` tinyint(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`),
	KEY `tid` (`tid`),
	KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品推荐表'");

$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select='<option value="0">请选择商品分类</option>';
while($res = $rs->fetch()){
	$select.='<option value="'.$res['cid'].'">'.$res['name'].'</option>';
}

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='add')
{
?>
<div class="block">
<div class="block-title"><h3 class="panel-title">添加推荐商品</h3></div>
<div class="">
  <form action="./recommend.php?my=add_submit" method="post" class="form" role="form">
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			选择商品
		</span>
		<select id="cid" class="form-control"><?php echo $select;?></select>
		<select id="tid" name="tid" class="form-control"></select>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			排序值
		</span>
		<input type="number" min="1" max="1000" name="sort" value="" class="form-control" placeholder="输入排序值，值越小越靠前"/>
	</div>
  </div>
	<div class="form-group">
	  <input type="submit" name="submit" value="添加" class="btn btn-primary btn-block"/>
	</div>
  </form>
  <br/><a href="./recommend.php">>>返回推荐商品列表</a>
</div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/recommendedit.js?ver=<?php echo VERSION;?>"></script>
<?php 
}
elseif($my=='edit')
{
	$id=$_GET['id'];
	$row=$DB->getRow("select * from pre_recommend where id='".$id."' limit 1");
	$toolname=$DB->getColumn("SELECT name FROM pre_tools WHERE tid='".$row['tid']."' LIMIT 1");
?>
<div class="block">
<div class="block-title"><h3 class="panel-title">修改推荐商品</h3></div>
<div class="">
  <form action="./recommend.php?my=edit_submit&id=<?php echo $id;?>" method="post" class="form" role="form">
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			商品名称
		</span>
		<input type="text" id="tid" value="<?php echo $toolname;?>" class="form-control" disabled/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			排序值
		</span>
		<input type="number" min="1" max="1000" name="sort" value="<?php echo $row['sort'];?>" class="form-control" placeholder="输入排序值，值越小越靠前"/>
	</div>
  </div>
	<div class="form-group">
	  <input type="submit" name="submit" value="修改" class="btn btn-primary btn-block"/>
	</div>
  </form>
  <br/><a href="./recommend.php">>>返回推荐商品列表</a>
</div>
</div>
<?php 
}
elseif($my=='add_submit')
{
	$tid=intval($_POST['tid']);
	$sort=intval($_POST['sort']);
	if($tid==NULL || $sort==NULL){
		showmsg('保存错误,请确保每项都不为空!',3);
	} else {
		$exists=$DB->getColumn("SELECT id FROM pre_recommend WHERE tid='{$tid}' LIMIT 1");
		if($exists){
			showmsg('该商品已在推荐列表中！',3);
		}else{
			$sql="insert into `pre_recommend` (`tid`,`sort`,`addtime`,`active`) values ('".$tid."','".$sort."','".$date."','1')";
			if($DB->exec($sql)!==false){
				showmsg('添加推荐商品成功！<br/><br/><a href="./recommend.php">>>返回推荐商品列表</a>',1);
			}else{
				showmsg('添加推荐商品失败！'.$DB->error(),4);
			}
		}
	}
}
elseif($my=='edit_submit')
{
	$id=$_GET['id'];
	$rows=$DB->getRow("select * from pre_recommend where id='".$id."' limit 1");
	if(!$rows){
		showmsg('当前记录不存在！',3);
	}
	$sort=intval($_POST['sort']);
	if($sort==NULL){
		showmsg('保存错误,请确保每项都不为空!',3);
	} else {
		if($DB->exec("UPDATE `pre_recommend` SET `sort`='".$sort."' WHERE `id`='".$id."'")!==false){
			showmsg('修改推荐商品成功！<br/><br/><a href="./recommend.php">>>返回推荐商品列表</a>',1);
		}else{
			showmsg('修改推荐商品失败！'.$DB->error(),4);
		}
	}
}
else
{
?>
<div class="block">
<div class="block-title clearfix">
<h2 id="blocktitle"></h2>
<span class="pull-right">
<select id="pagesize" class="form-control"><option value="30">30</option><option value="50">50</option><option value="60">60</option><option value="80">80</option><option value="100">100</option></select>
<span>
</span></span>
</div>
  <form onsubmit="return searchItem()" method="GET" class="form-inline">
  <a href="./recommend.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加推荐商品</a>
  <div class="form-group">
    <input type="text" class="form-control" name="kw" placeholder="请输入商品名称">
  </div>
  <button type="submit" class="btn btn-info">搜索</button>&nbsp;
  <a href="javascript:listTable('start')" class="btn btn-default" title="刷新商品列表"><i class="fa fa-refresh"></i></a>
</form>
<div id="listTable"></div>
  </div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/recommend.js?ver=<?php echo VERSION;?>"></script>
<?php 
}
?></body>
</html>