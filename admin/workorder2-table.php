<?php
// 显示所有错误
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("workorder", 1);
if (isset($_GET["status"])) {
	$status = intval($_GET["status"]);
	$sql = " status=:status";
	$params = array(':status' => $status);
	$link = "&status=" . $status;
} else {
	$sql = " ts=1";
	$params = array();
	$link = "";
}
$numrows = $DB->getColumn("SELECT count(*) from pre_workorder WHERE" . $sql, $params);
$pagesize = 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
?>	  <form name="form1" id="form1">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>ZID</th><th>商品ID</th><th>订单号</th><th>问题描述</th><th>状态</th><th>提交时间</th><th>操作</th></tr></thead>
          <tbody>
<?php 
// 直接将limit和offset拼接到SQL语句中（MySQL PDO不支持将limit和offset作为参数）	
$rs = $DB->query("SELECT * FROM pre_workorder WHERE" . $sql . " order by id desc limit " . $offset . "," . $pagesize, $params);
while ($res = $rs->fetch()) {
	$content = explode("*", $res["content"]);
	$content = mb_substr($content[0], 0, 16, "utf-8");
	echo "<tr><td><input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["id"] . "\" onClick=\"unselectall1()\">&nbsp;<b>" . $res["id"] . "</b></td><td><a href=\"./sitelist.php?zid=" . $res["zid"] . "\" target=\"_blank\">" . $res["zid"] . "</a></td><td>" . getid($res["orderid"]) . "</td><td><a href=\"./list.php?id=" . $res["orderid"] . "\" target=\"_blank\">" . $res["orderid"] . "</a></td><td><a href=\"javascript:orderItem(" . $res["id"] . ")\">" . htmlspecialchars($content) . "</a></td><td>" . display_status($res["status"]) . "</td><td>" . $res["addtime"] . "</td><td><a href=\"javascript:orderItem(" . $res["id"] . ")\" class=\"btn btn-info btn-xs\">查看</a>&nbsp;<a href=\"javascript:delworkorder(" . $res["id"] . ")\" href=\"./workorder.php?my=delete&id=" . $res["id"] . "\" class=\"btn btn-xs btn-danger\">删除</a></td></tr>";
}
?>          </tbody>
        </table>
<input type="hidden" name="content"/>
<input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox">&nbsp;全选&nbsp;
<select name="aid"><option selected>批量操作</option><option value="1">&gt;改为待处理</option><option value="2">&gt;改为已完成</option><option value="3">&gt;批量回复</option><option value="4">&gt;删除选中</option></select><button type="button" onclick="change()">执行</button>
      </div>
	  </form>
<div class="text-center"><ul class="pagination"><?php 
$first = 1;
$prev = $page - 1;
$next = $page + 1;
$last = $pages;
if ($page > 1) {
	echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $first . $link . "')\">首页</a></li>";
	echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $prev . $link . "')\">&laquo;</a></li>";
} else {
	echo "<li class=\"disabled\"><a>首页</a></li>";
	echo "<li class=\"disabled\"><a>&laquo;</a></li>";
}
$start = $page - 10 > 1 ? $page - 10 : 1;
$end = $page + 10 < $pages ? $page + 10 : $pages;
for ($i = $start; $i < $page; $i++) {
	echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $i . $link . "')\">" . $i . "</a></li>";
}
echo "<li class=\"disabled\"><a>" . $page . "</a></li>";
for ($i = $page + 1; $i <= $end; $i++) {
	echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $i . $link . "')\">" . $i . "</a></li>";
}
if ($page < $pages) {
	echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $next . $link . "')\">&raquo;</a></li>";
	echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $last . $link . "')\">尾页</a></li>";
} else {
	echo "<li class=\"disabled\"><a>&raquo;</a></li>";
	echo "<li class=\"disabled\"><a>尾页</a></li>";
}
?></ul></div><?php 
function getid($id)
{
	global $DB;
	$_var_0 = $DB->getRow("select * from pre_orders where id=:id limit 1", array(':id' => $id));
	// 添加错误处理，防止找不到订单时出错
	if ($_var_0) {
		return $_var_0["tid"];
	} else {
		return "-";
	}
}
function display_status($status)
{
	if ($status == 1) {
		return "<font color=\"red\">处理中</font>";
	} elseif ($status == 2) {
		return "<font color=\"green\">已完成</font>";
	} else {
		return "<font color=\"blue\">待处理</font>";
	}
}