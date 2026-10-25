<?php
include "../includes/common.php";
$title = '上架日志';
include './head.php';
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission('site', 1);
$my = isset($_GET['my']) ? $_GET['my'] : null;
if ($my == 'add') {
	?><div class="block">
<div class="block-title"><h3 class="panel-title">添加日志</h3></div><div class=""><?php echo '<form action="./toollogsedit.php?my=add_submit" method="POST">
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			日志内容
		</span>
	    <textarea class="form-control" name="content" rows="8" placeholder=""></textarea></div>
	</div>
</div>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			上架日期
		</span>
		<input type="date" name="date" value="' . date('Y-m-d') . '" class="form-control" placeholder="留空则为当天时间"/>
	</div>
  </div>
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form>';?><br/><a href="./toollogs.php">>>返回列表</a></div></div><?php 
} elseif ($my == 'edit') {
	$id = $_GET['id'];
	$row = $DB->getRow("select * from pre_toollogs where id='" . $id . "' limit 1");
	?><div class="block">
<div class="block-title"><h3 class="panel-title">修改日志信息</h3></div><div class=""><?php echo '<form action="./toollogsedit.php?my=edit_submit&id=' . $id . '" method="POST">
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			日志内容
		</span>
		<textarea class="form-control" name="content" rows="8" placeholder="">' . $row['content'] . '</textarea></div>
	</div>
</div>
  <div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			上架日期
		</span>
		<input type="date" name="date" value="' . $row['date'] . '" class="form-control" placeholder="留空获取当天时间"/>
	</div>
  </div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>';?><br/><a href="./toollogs.php">>>返回列表</a><?php 
} elseif ($my == 'add_submit') {
	$date = trim($_POST['date']);
	$content = trim($_POST['content']);
	$rows = $DB->getRow("select * from pre_toollogs where date='" . $date . "' limit 1");
	if ($rows) {
		showmsg($date . '日志已存在！', 3);
	}
	$sql = "insert into `pre_toollogs` (`content`,`date`,`addtime`,`active`) values (:content, :date2,:date, 1)";
	$data = array(':content' => $content, ':date2' => $date, ':date' => $date);
	if ($DB->exec($sql, $data) !== false) {
		showmsg('添加日志成功！<br/><br/><a href="./toollogs.php">>>返回列表</a>', 1);
	} else {
		showmsg('添加日志失败！' . $DB->error(), 4);
	}
} elseif ($my == 'edit_submit') {
	$id = trim($_GET['id']);
	$date = !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d');
	$content = trim($_POST['content']);
	$rows = $DB->getRow("select * from pre_toollogs where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg('不存在此记录！', 3);
	}
	if ($DB->exec("update pre_toollogs set content='" . $content . "',date='" . $date . "' where id='" . $id . "'") !== false) {
		showmsg('修改日志成功！<br/><br/><a href="./toollogs.php">>>返回列表</a>', 1);
	} else {
		showmsg('修改日志失败！' . $DB->error(), 4);
	}
} elseif ($my == 'delete') {
	$id = $_GET['id'];
	$sql = "DELETE FROM pre_toollogs WHERE id='" . $id . "'";
	if ($DB->exec($sql) !== false) {
		showmsg('删除成功！<br/><br/><a href="./toollogs.php">>>返回列表</a>', 1);
	} else {
		showmsg('删除失败！' . $DB->error(), 4);
	}
}
?></body>
</html>