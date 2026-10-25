<?php

include "../includes/common.php";
$title = "推广商品列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-md-12 center-block" style="float: none;">
<?php 
adminpermission("shop", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">请选择商品分类</option>";
while ($res = $rs->fetch()) {
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
if ($my == "add") {
	?><div class="block">
<div class="block-title"><h3 class="panel-title">添加推广商品</h3></div>
<div class="">
  <form action="./invite.php?my=add_submit" method="post" class="form" role="form">
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
			奖励条件
		</span>
		<select id="type" name="type" class="form-control"><option value="0">下单金额</option><option value="1">累计访问</option></select>
		<input type="text" id="value" name="value" value="" class="form-control" placeholder="输入下单金额"/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			奖励次数
		</span>
		<select id="type" name="times" class="form-control"><option value="0">一次性</option><option value="1">可多次</option></select>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			排序数字
		</span>
		<input type="number" min="1" max="1000" name="sort" value="" class="form-control" placeholder="数字越小越靠前"/>
	</div>
  </div>
	<div class="form-group">
	  <input type="submit" name="submit" value="添加" class="btn btn-primary btn-block"/>
	</div>
  </form>
  <br/><a href="./invite.php">>>返回商品列表</a>
</div>
<div class="panel-footer">
<span class="glyphicon glyphicon-info-sign"></span>奖励条件说明：<br/>
【下单金额】被推广用户打开链接下单达到该金额值，推广用户获得该商品奖励<br/>
【累计访问】打开推广链接的IP数量达到该数值，推广用户获得该商品奖励<br/><br/>
奖励次数说明：<br/>
【一次性】一个推广链接只能发放一次奖励（适用于钻和会员等商品）<br/>
【可多次】一个推广链接可发放多次奖励，只要达到条件即可（适用于名片赞等商品）<br/>
注：奖励条件为累计访问的只能是一次性
</div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/inviteedit.js?ver=<?php echo VERSION;?>"></script>
<?php 
} elseif ($my == "edit") {
	$id = $_GET["id"];
	$row = $DB->getRow("select * from pre_inviteshop where id='" . $id . "' limit 1");
	$toolname = $DB->getColumn("SELECT name FROM pre_tools WHERE tid='" . $row["tid"] . "' LIMIT 1");
	?><div class="block">
<div class="block-title"><h3 class="panel-title">修改推广商品</h3></div>
<div class="">
  <form action="./invite.php?my=edit_submit&id=<?php echo $id;?>" method="post" class="form" role="form">
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
			奖励条件
		</span>
		<select id="type" name="type" class="form-control" default="<?php echo $row["type"];?>"><option value="0">下单金额</option><option value="1">累计访问</option></select>
		<input type="text" id="value" name="value" value="<?php echo $row["value"];?>" class="form-control" placeholder="输入下单金额"/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			奖励次数
		</span>
		<select id="type" name="times" class="form-control" default="<?php echo $row["time"];?>"><option value="0">一次性</option><option value="1">可多次</option></select>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			排序数字
		</span>
		<input type="number" min="1" max="1000" name="sort" value="<?php echo $row["sort"];?>" class="form-control" placeholder="数字越小越靠前"/>
	</div>
  </div>
	<div class="form-group">
	  <input type="submit" name="submit" value="修改" class="btn btn-primary btn-block"/>
	</div>
  </form>
  <br/><a href="./invite.php">>>返回商品列表</a>
</div>
<div class="panel-footer">
<span class="glyphicon glyphicon-info-sign"></span>奖励条件说明：<br/>
【下单金额】被推广用户打开链接下单达到该金额值，推广用户获得该商品奖励<br/>
【累计访问】打开推广链接的IP数量达到该数值，推广用户获得该商品奖励<br/><br/>
奖励次数说明：<br/>
【一次性】一个推广链接只能发放一次奖励（适用于钻和会员等商品）<br/>
【可多次】一个推广链接可发放多次奖励，只要达到条件即可（适用于名片赞等商品）<br/>
注：奖励条件为累计访问的只能是一次性
</div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/inviteedit.js?ver=<?php echo VERSION;?>"></script>
<?php 
} elseif ($my == "add_submit") {
	$tid = intval($_POST["tid"]);
	$type = intval($_POST["type"]);
	$value = $_POST["value"];
	$times = intval($_POST["times"]);
	$sort = $_POST["sort"];
	if ($value == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$sql = "insert into `pre_inviteshop` (`tid`,`type`,`value`,`times`,`sort`,`addtime`,`active`) values ('" . $tid . "','" . $type . "','" . $value . "','" . $times . "','" . $sort . "','" . $date . "','1')";
		if ($DB->exec($sql) !== false) {
			showmsg("添加推广商品成功！<br/><br/><a href=\"./invite.php\">>>返回推广商品列表</a>", 1);
		} else {
			showmsg("添加推广商品失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = $_GET["id"];
	$rows = $DB->getRow("select * from pre_inviteshop where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$type = intval($_POST["type"]);
	$value = $_POST["value"];
	$times = intval($_POST["times"]);
	$sort = $_POST["sort"];
	if ($value == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if ($DB->exec("UPDATE `pre_inviteshop` SET `type`='" . $type . "',`value`='" . $value . "',`times`='" . $times . "',`sort`='" . $sort . "' WHERE `id`='" . $id . "'") !== false) {
			showmsg("修改推广商品成功！<br/><br/><a href=\"./invite.php\">>>返回推广商品列表</a>", 1);
		} else {
			showmsg("修改推广商品失败！" . $DB->error(), 4);
		}
	}
} else {
	?><div class="block">
<div class="block-title clearfix">
<h2 id="blocktitle"></h2>
<span class="pull-right"><select id="pagesize" class="form-control"><option value="30">30</option><option value="50">50</option><option value="60">60</option><option value="80">80</option><option value="100">100</option></select><span>
</span></span>
</div>
  <form onsubmit="return searchItem()" method="GET" class="form-inline">
  <a href="./invite.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加推广商品</a>
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
<script src="assets/js/invite.js?ver=<?php echo VERSION;?>"></script>
<?php 
}
?></body>
</html>