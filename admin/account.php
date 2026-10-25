<?php

include "../includes/common.php";
$title = "员工管理";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
<?php 
adminpermission("account", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "add") {
	?><div class="block">
<div class="block-title"><h3 class="panel-title">添加一个用户</h3></div>
<div class="">
  <form action="./account.php?my=add_submit" method="post" role="form">
    <div class="form-group">
	  <label>用户名</label><br/>
	  <input type="text" name="username" value="" class="form-control"/>
	</div>
	<div class="form-group">
	  <label>密码</label><br/>
	  <input type="text" name="password" value="" class="form-control"/>
	</div>
	<div class="form-group">
	  <label>选择可用的功能模块</label><br/>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="order"> 订单管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="refund"> 订单退款</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="shop"> 商品管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="price"> 加价模板</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="faka"> 发卡管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="site"> 分站/用户管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="tixian"> 余额提现</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="workorder"> 工单管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="message"> 站内通知</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="article"> 文章管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="shequ"> 对接管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="set"> 系统设置</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="account"> 员工管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="contact"> 联系方式</label>
	</div>
	<div class="form-group">
	  <label>是否激活</label><br/>
	  <select class="form-control" name="active"><option value="1">是</option><option value="0">否</option></select>
	</div>
	<div class="form-group">
	  <input type="submit" name="submit" value="添加" class="btn btn-primary btn-block"/>
	</div>
  </form>
  <br/><a href="./account.php">>>返回员工列表</a>
</div>
</div>
<?php 
} elseif ($my == "edit") {
	$id = $_GET["id"];
	$row = $DB->getRow("select * from pre_account where id='" . $id . "' limit 1");
	$permission = explode(",", $row["permission"]);
	?><div class="block">
<div class="block-title"><h3 class="panel-title">修改用户</h3></div>
<div class="">
  <form action="./account.php?my=edit_submit&id=<?php echo $id;?>" method="post" role="form">
    <div class="form-group">
	  <label>用户名</label><br/>
	  <input type="text" name="username" value="<?php echo $row["username"];?>" class="form-control"/>
	</div>
	<div class="form-group">
	  <label>密码</label><br/>
	  <input type="text" name="password" value="<?php echo $row["password"];?>" class="form-control"/>
	</div>
	<div class="form-group">
	  <label>选择可用的功能模块</label><br/>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="order" <?php echo in_array("order", $permission) ? "checked" : "";?>> 订单管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="refund" <?php echo in_array("refund", $permission) ? "checked" : "";?>> 订单退款</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="shop" <?php echo in_array("shop", $permission) ? "checked" : "";?>> 商品管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="price" <?php echo in_array("price", $permission) ? "checked" : "";?>> 加价模板</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="faka" <?php echo in_array("faka", $permission) ? "checked" : "";?>> 发卡管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="site" <?php echo in_array("site", $permission) ? "checked" : "";?>> 分站/用户管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="tixian" <?php echo in_array("tixian", $permission) ? "checked" : "";?>> 余额提现</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="workorder" <?php echo in_array("workorder", $permission) ? "checked" : "";?>> 工单管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="message" <?php echo in_array("message", $permission) ? "checked" : "";?>> 站内通知</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="article" <?php echo in_array("article", $permission) ? "checked" : "";?>> 文章管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="shequ" <?php echo in_array("shequ", $permission) ? "checked" : "";?>> 对接管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="set" <?php echo in_array("set", $permission) ? "checked" : "";?>> 系统设置</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="account" <?php echo in_array("account", $permission) ? "checked" : "";?>> 员工管理</label>
	<label class="checkbox-inline"><input type="checkbox" name="permission[]" value="contact" <?php echo in_array("contact", $permission) ? "checked" : "";?>> 联系方式</label>
	</div>
	<div class="form-group">
	  <label>是否激活</label><br/>
	  <select class="form-control" name="active" default="<?php echo $row["active"];?>"><option value="1">是</option><option value="0">否</option></select>
	</div>
	<div class="form-group">
	  <input type="submit" name="submit" value="修改" class="btn btn-primary btn-block"/>
	</div>
  </form>
  <br/><a href="./account.php">>>返回员工列表</a>
</div>
</div>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>
<?php 
} elseif ($my == "add_submit") {
	$username = $_POST["username"];
	$password = $_POST["password"];
	$permission = implode(",", $_POST["permission"]);
	$active = intval($_POST["active"]);
	if ($username == NULL || $password == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$rows = $DB->getRow("select * from pre_account where username='" . $username . "' limit 1");
		if ($rows) {
			showmsg("用户名已存在！", 3);
		}
		$sql = "insert into `pre_account` (`username`,`password`,`permission`,`addtime`,`active`) values ('" . $username . "','" . $password . "','" . $permission . "','" . $date . "','" . $active . "')";
		if ($DB->exec($sql) !== false) {
			showmsg("添加用户成功！<br/><br/><a href=\"./account.php\">>>返回用户列表</a>", 1);
		} else {
			showmsg("添加用户失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = $_GET["id"];
	$rows = $DB->getRow("select * from pre_account where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$username = $_POST["username"];
	$password = $_POST["password"];
	$permission = implode(",", $_POST["permission"]);
	$active = intval($_POST["active"]);
	if ($username == NULL || $password == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if ($DB->exec("UPDATE `pre_account` SET `username`='" . $username . "',`password`='" . $password . "',`permission`='" . $permission . "',`active`='" . $active . "' WHERE `id`='" . $id . "'") !== false) {
			showmsg("修改用户成功！<br/><br/><a href=\"./account.php\">>>返回用户列表</a>", 1);
		} else {
			showmsg("修改用户失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "delete") {
	$id = $_GET["id"];
	$sql = "DELETE FROM pre_account WHERE id='" . $id . "'";
	if ($DB->exec($sql) !== false) {
		showmsg("删除成功！<br/><br/><a href=\"./account.php\">>>返回用户列表</a>", 1);
	} else {
		showmsg("删除失败！" . $DB->error(), 4);
	}
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_account");
	?><div class="block">
<div class="block-title clearfix">
<h2>系统共有 <b><?php echo $numrows;?></b> 个员工帐号</h2>
</div>
<a href="./account.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加一个用户</a>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>用户名</th><th>权限</th><th>添加时间/上次登录</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php 
	$pagesize = 30;
	$pages = ceil($numrows / $pagesize);
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$offset = $pagesize * ($page - 1);
	$rs = $DB->query("SELECT * FROM pre_account WHERE 1 order by id desc limit " . $offset . "," . $pagesize);
	while ($res = $rs->fetch()) {
		echo "<tr><td><b>" . $res["id"] . "</b></td><td>" . $res["username"] . "</td><td>" . $res["permission"] . "</td><td>" . $res["addtime"] . "<br/>" . $res["lasttime"] . "</td><td>" . ($res["active"] == 1 ? "<span class=\"btn btn-xs btn-success\">正常</span>" : "<span class=\"btn btn-xs btn-warning\">封禁</span>") . "</td><td><a href=\"./account.php?my=edit&id=" . $res["id"] . "\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<a href=\"./account.php?my=delete&id=" . $res["id"] . "\" class=\"btn btn-xs btn-danger\" onclick=\"return confirm('你确实要删除此记录吗？');\">删除</a></td></tr>";
	}
	?>          </tbody>
        </table>
      </div>
<ul class="pagination"><?php 
	$first = 1;
	$prev = $page - 1;
	$next = $page + 1;
	$last = $pages;
	if ($page > 1) {
		echo "<li><a href=\"account.php?page=" . $first . $link . "\">首页</a></li>";
		echo "<li><a href=\"account.php?page=" . $prev . $link . "\">&laquo;</a></li>";
	} else {
		echo "<li class=\"disabled\"><a>首页</a></li>";
		echo "<li class=\"disabled\"><a>&laquo;</a></li>";
	}
	$start = $page - 10 > 1 ? $page - 10 : 1;
	$end = $page + 10 < $pages ? $page + 10 : $pages;
	for ($i = $start; $i < $page; $i++) {
		echo "<li><a href=\"account.php?page=" . $i . $link . "\">" . $i . "</a></li>";
	}
	echo "<li class=\"disabled\"><a>" . $page . "</a></li>";
	for ($i = $page + 1; $i <= $end; $i++) {
		echo "<li><a href=\"account.php?page=" . $i . $link . "\">" . $i . "</a></li>";
	}
	if ($page < $pages) {
		echo "<li><a href=\"account.php?page=" . $next . $link . "\">&raquo;</a></li>";
		echo "<li><a href=\"account.php?page=" . $last . $link . "\">尾页</a></li>";
	} else {
		echo "<li class=\"disabled\"><a>&raquo;</a></li>";
		echo "<li class=\"disabled\"><a>尾页</a></li>";
	}
	?></ul><?php 
}
?>    </div>
  </div>