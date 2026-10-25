<?php

include "../includes/common.php";

// 检查并创建站点任务表
function createSiteTaskTables() {
    global $DB;
    
    // 创建pre_sitetask表
    $pre_table_sql = "CREATE TABLE IF NOT EXISTS `pre_sitetask` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `task` tinyint(1) NOT NULL DEFAULT 0,
      `type` tinyint(1) NOT NULL DEFAULT 0,
      `tid` int(11) unsigned NOT NULL DEFAULT 0,
      `value` varchar(32) NOT NULL,
      `money` varchar(32) NOT NULL,
      `quantity` int(11) NOT NULL,
      `desc` text,
      `sort` int(11) NOT NULL DEFAULT 10,
      `addtime` datetime DEFAULT NULL,
      `active` tinyint(1) NOT NULL DEFAULT 1,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点任务表';";
    
    // 创建shua_sitetask表
    $shua_table_sql = "CREATE TABLE IF NOT EXISTS `shua_sitetask` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `task` tinyint(1) NOT NULL DEFAULT 0,
      `type` tinyint(1) NOT NULL DEFAULT 0,
      `tid` int(11) unsigned NOT NULL DEFAULT 0,
      `value` varchar(32) NOT NULL,
      `money` varchar(32) NOT NULL,
      `quantity` int(11) NOT NULL,
      `desc` text,
      `sort` int(11) NOT NULL DEFAULT 10,
      `addtime` datetime DEFAULT NULL,
      `active` tinyint(1) NOT NULL DEFAULT 1,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点任务表';";
    
    try {
        $DB->exec($pre_table_sql);
        $DB->exec($shua_table_sql);
    } catch (Exception $e) {
        // 忽略错误，继续执行
    }
}

// 执行表创建
createSiteTaskTables();

$title = "分站任务列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-md-12 center-block" style="float: none;">
<?php 
adminpermission("site", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">请选择商品分类</option>";
while ($res = $rs->fetch()) {
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
if ($my == "add") {
	?><div class="block">
<div class="block-title"><h3 class="panel-title">添加分站任务</h3></div>
<div class="">
  <form action="./sitetask.php?my=add_submit" method="post" class="form" role="form">
  <input type="hidden" name="csrf_token" value="<?php echo md5(session_id() . SYS_KEY); ?>">
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			任务资料
		</span>
		<input type="text" id="name" name="name" value="" class="form-control" placeholder="例如：推广工具A得奖励"/>
		<select id="task" name="task" class="form-control">
			<option value="0">商品推广</option>
			<option value="1">余额充值</option>
			<option value="2">订单数量</option>
			<option value="3">销售金额</option>
			<option value="4">邀新开户</option>
			<option value="5">连续签到</option>
		</select>
		<select id="type" name="type" class="form-control"><option value="0">今日</option><option value="1">总计</option></select>
	</div>
  </div>
  <div class="form-group" id="goods" style="display:none;">
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
			任务条件
		</span>
		<input type="text" id="value" name="value" value="" class="form-control" placeholder="示例：5（表示需要5笔订单/50元充值金额）"/>
		<input type="text" id="money" name="money" value="" class="form-control" placeholder="示例：10.00（表示奖励10元）"/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			任务数量
		</span>
		<input type="number" min="1" name="quantity" value="" class="form-control" placeholder="示例：100（表示该任务可发放100次）"/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			任务介绍
		</span>
		<textarea class="form-control" id="desc" name="desc" rows="3" style="width:100%" placeholder="任务简单介绍，不支持HTML代码，可留空"></textarea>
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
  <br/><a href="./sitetask.php">>>返回任务列表</a>
</div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/sitetaskedit.js?ver=<?php echo VERSION;?>"></script>
<?php 
} elseif ($my == "edit") {
	$id = intval($_GET["id"]);
	$row = $DB->getRow("SELECT * FROM pre_sitetask WHERE id=:id LIMIT 1", array(':id' => $id));
	$toolname = $DB->getColumn("SELECT name FROM pre_tools WHERE tid=:tid LIMIT 1", array(':tid' => $row["tid"]));
	$type = "今日";
	if ($row["task"] == 1) {
		$type = "总计";
	}
	$tasktype = sitetask_type($row["task"]);
	if ($row["task"] == 5) {
		$tasktype = $tasktype . "任务";
	} else {
		$tasktype = $type . $tasktype . "任务";
	}
	?><div class="block">
<div class="block-title"><h3 class="panel-title">修改分站任务</h3></div>
<div class="">
  <form action="./sitetask.php?my=edit_submit&id=<?php echo $id;?>" method="post" class="form" role="form">
  <input type="hidden" name="csrf_token" value="<?php echo md5(session_id() . SYS_KEY); ?>">
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			任务类型
		</span>
		<input type="text" id="taskname" value="<?php echo $tasktype;?>" class="form-control" disabled/>
	</div>
  </div>
  <?php 
	if ($row["task"] == 0) {
		?>  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			商品名称
		</span>
		<input type="text" id="tid" value="<?php echo $toolname;?>" class="form-control" disabled/>
	</div>
  </div>
  <?php 
	}
	?>  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			任务名称
		</span>
		<input type="text" id="name" name="name" value="<?php echo $row["name"];?>" class="form-control" placeholder="例如：推广工具A得奖励"/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			任务条件
		</span>
		<input type="text" id="value" name="value" value="<?php echo $row["value"];?>" class="form-control" placeholder="示例：5（表示需要5笔订单/50元充值金额）"/>
		<input type="text" id="money" name="money" value="<?php echo $row["money"];?>" class="form-control" placeholder="示例：10.00（表示奖励10元）"/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			任务数量
		</span>
		<input type="number" min="1" name="quantity" value="<?php echo $row["quantity"];?>" class="form-control" placeholder="示例：100（表示该任务可发放100次）"/>
	</div>
  </div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			任务介绍
		</span>
		<textarea class="form-control" id="desc" name="desc" rows="3" style="width:100%" placeholder="任务简单介绍，不支持HTML代码，可留空"><?php echo htmlspecialchars($row["desc"]);?></textarea>
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
  <br/><a href="./sitetask.php">>>返回任务列表</a>
</div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/sitetaskedit.js?ver=<?php echo VERSION;?>"></script>
<?php 
} elseif ($my == "add_submit") {
	if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== md5(session_id() . SYS_KEY)) {
		showmsg("CSRF验证失败，请刷新页面后重试！", 4);
	}
	$name = $_POST["name"];
	$task = intval($_POST["task"]);
	$type = intval($_POST["type"]);
	$tid = intval($_POST["tid"]);
	$value = $_POST["value"];
	$money = $_POST["money"];
	$quantity = intval($_POST["quantity"]);
	$sort = intval($_POST["sort"]);
	if ($name == NULL || $value == NULL || $money == NULL || $quantity == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$params = array(
			':name' => $name,
			':task' => $task,
			':type' => $type,
			':tid' => $tid,
			':value' => $value,
			':money' => $money,
			':quantity' => $quantity,
			':desc' => $_POST["desc"],
			':sort' => $sort,
			':addtime' => $date,
		);
		if ($DB->exec("INSERT INTO `pre_sitetask` (`name`,`task`,`type`,`tid`,`value`,`money`,`quantity`,`desc`,`sort`,`addtime`,`active`) VALUES (:name,:task,:type,:tid,:value,:money,:quantity,:desc,:sort,:addtime,'1')", $params) !== false) {
			showmsg("添加分站任务成功！<br/><br/><a href=\"./sitetask.php\">>>返回分站任务列表</a>", 1);
		} else {
			showmsg("添加分站任务失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== md5(session_id() . SYS_KEY)) {
		showmsg("CSRF验证失败，请刷新页面后重试！", 4);
	}
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("SELECT * FROM pre_sitetask WHERE id=:id LIMIT 1", array(':id' => $id));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$name = $_POST["name"];
	$value = $_POST["value"];
	$money = $_POST["money"];
	$quantity = intval($_POST["quantity"]);
	$sort = intval($_POST["sort"]);
	if ($name == NULL || $value == NULL || $money == NULL || $quantity == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$params = array(
			':name' => $name,
			':value' => $value,
			':money' => $money,
			':quantity' => $quantity,
			':desc' => $_POST["desc"],
			':sort' => $sort,
			':id' => $id,
		);
		if ($DB->exec("UPDATE `pre_sitetask` SET `name`=:name,`value`=:value,`money`=:money,`quantity`=:quantity,`desc`=:desc,`sort`=:sort WHERE `id`=:id", $params) !== false) {
			showmsg("修改分站任务成功！<br/><br/><a href=\"./sitetask.php\">>>返回分站任务列表</a>", 1);
		} else {
			showmsg("修改分站任务失败！" . $DB->error(), 4);
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
  <a href="./sitetask.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加分站任务</a>
  <div class="form-group">
    <input type="text" class="form-control" name="kw" placeholder="请输入任务名称">
  </div>
  <button type="submit" class="btn btn-info">搜索</button>&nbsp;
  <a href="javascript:listTable('start')" class="btn btn-default" title="刷新任务列表"><i class="fa fa-refresh"></i></a>
</form>
<div id="listTable"></div>
  </div>
</div>
<script>var csrfToken = '<?php echo md5(session_id() . SYS_KEY); ?>';</script>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/sitetask.js?ver=<?php echo VERSION;?>"></script>
<?php 
}
?></body>
</html>