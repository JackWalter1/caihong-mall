<?php

include "../includes/common.php";

$title = "秒杀商品列表";
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
<div class="block-title"><h3 class="panel-title">添加秒杀商品</h3></div>
<div class="">
  <form action="./seckill.php?my=add_submit" method="post" class="form" role="form">
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
			秒杀价格
		</span>
		<input type="text" id="price" name="price" value="" class="form-control" placeholder="输入多少元秒杀该商品" required/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			秒杀限制
		</span>
		<input type="number" min="1" id="value" name="value" value="" class="form-control" placeholder="输入输入秒杀商品的限制数量" required/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			下单数量
		</span>
		<input type="number" min="1" id="num" name="num" value="" class="form-control" placeholder="输入秒杀该商品下单多少" required/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			开始时间
		</span>
		<input type="date" class="form-control" name="starttime" value="" required>
		<input type="time" class="form-control" name="starttimes" value="" required>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			结束时间
		</span>
		<input type="date" class="form-control" name="endtime" value="" required>
		<input type="time" class="form-control" name="endtimes" value="" required>
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
  <br/><a href="./seckill.php">>>返回商品列表</a>
</div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/seckilledit.js?ver=<?php echo VERSION;?>"></script>
<?php 
} elseif ($my == "edit") {
	$id = $_GET["id"];
	$row = $DB->getRow("select * from pre_seckillshop where id='" . $id . "' limit 1");
	$toolname = $DB->getColumn("SELECT name FROM pre_tools WHERE tid='" . $row["tid"] . "' LIMIT 1");
	$start_date = explode(" ", $row["starttime"]);
	$start_time = explode(":", $start_date[1]);
	$start_time = $start_time[0] . ":" . $start_time[1];
	$end_date = explode(" ", $row["endtime"]);
	$end_time = explode(":", $end_date[1]);
	$end_time = $end_time[0] . ":" . $end_time[1];
	?><div class="block">
<div class="block-title"><h3 class="panel-title">修改秒杀商品</h3></div>
<div class="">
  <form action="./seckill.php?my=edit_submit&id=<?php echo $id;?>" method="post" class="form" role="form">
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
			秒杀价格
		</span>
		<input type="text" id="price" name="price" value="<?php echo $row["price"];?>" class="form-control" placeholder="输入多少元秒杀该商品" required/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			秒杀限制
		</span>
		<input type="number" min="1" id="value" name="value" value="<?php echo $row["value"];?>" class="form-control" placeholder="输入输入秒杀商品的限制数量" required/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			下单数量
		</span>
		<input type="number" min="1" id="num" name="num" value="<?php echo $row["num"];?>" class="form-control" placeholder="输入秒杀该商品下单多少" required/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			开始时间
		</span>
		<input type="date" class="form-control" name="starttime" value="<?php echo $start_date[0];?>" required>
		<input type="time" class="form-control" name="starttimes" value="<?php echo $start_time;?>" required>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			结束时间
		</span>
		<input type="date" class="form-control" name="endtime" value="<?php echo $end_date[0];?>" required>
		<input type="time" class="form-control" name="endtimes" value="<?php echo $end_time;?>" required>
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
  <br/><a href="./seckill.php">>>返回商品列表</a>
</div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/seckilledit.js?ver=<?php echo VERSION;?>"></script>
<?php 
} elseif ($my == "add_submit") {
	$tid = intval($_POST["tid"]);
	$price = $_POST["price"];
	$value = $_POST["value"];
	$num = $_POST["num"];
	$starttime = $_POST["starttime"];
	$starttimes = $_POST["starttimes"];
	$starttime = $starttime . " " . $starttimes . ":00";
	$endtime = $_POST["endtime"];
	$endtimes = $_POST["endtimes"];
	$endtime = $endtime . " " . $endtimes . ":00";
	$sort = $_POST["sort"];
	if ($price == NULL || $value == NULL || $num == NULL || $starttime == NULL || $endtime == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$sql = "insert into `pre_seckillshop` (`tid`,`price`,`value`,`num`,`sort`,`starttime`,`endtime`,`addtime`,`active`) values ('" . $tid . "','" . $price . "','" . $value . "','" . $num . "','" . $sort . "','" . $starttime . "','" . $endtime . "','" . $date . "','1')";
		if ($DB->exec($sql) !== false) {
			showmsg("添加秒杀商品成功！<br/><br/><a href=\"./seckill.php\">>>返回秒杀商品列表</a>", 1);
		} else {
			showmsg("添加秒杀商品失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = $_GET["id"];
	$rows = $DB->getRow("select * from pre_seckillshop where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$price = $_POST["price"];
	$value = $_POST["value"];
	$num = $_POST["num"];
	$starttime = $_POST["starttime"];
	$starttimes = $_POST["starttimes"];
	$starttime = $starttime . " " . $starttimes . ":00";
	$endtime = $_POST["endtime"];
	$endtimes = $_POST["endtimes"];
	$endtime = $endtime . " " . $endtimes . ":00";
	$sort = $_POST["sort"];
	if ($price == NULL || $value == NULL || $num == NULL || $starttime == NULL || $endtime == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if ($DB->exec("UPDATE `pre_seckillshop` SET `price`='" . $price . "',`value`='" . $value . "',`num`='" . $num . "',`sort`='" . $sort . "',`starttime`='" . $starttime . "',`endtime`='" . $endtime . "' WHERE `id`='" . $id . "'") !== false) {
			showmsg("修改秒杀商品成功！<br/><br/><a href=\"./seckill.php\">>>返回秒杀商品列表</a>", 1);
		} else {
			showmsg("修改秒杀商品失败！" . $DB->error(), 4);
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
  <a href="./seckill.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加秒杀商品</a>
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
<script src="assets/js/seckill.js?ver=<?php echo VERSION;?>"></script>
<?php 
}
?></body>
</html>