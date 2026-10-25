<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("site", 1);
if (isset($_GET["kw"])) {
	$kw = trim(daddslashes($_GET["kw"]));
	$numrows = $DB->getColumn("SELECT count(*) FROM pre_sitetask A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE B.name LIKE :kw", array(':kw' => '%' . $kw . '%'));
	$con = "包含 <b>" . htmlspecialchars($kw) . "</b> 的共有 <b>" . $numrows . "</b> 个商品";
	$link = "&kw=" . urlencode($kw);
} elseif (isset($_GET["id"])) {
	$id = intval($_GET["id"]);
	$numrows = $DB->getColumn("SELECT count(*) from pre_sitetask where id=:id", array(':id' => $id));
	$sql = " id=:id";
	$con = "分站任务列表";
	$link = "&id=" . $id;
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_sitetask");
	$sql = " 1";
	$con = "系统共有 <b>" . $numrows . "</b> 个分站任务";
}
?>	  <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>任务名称</th><th>任务类型</th><th title="数字越小越靠前">排序</th><th>任务条件</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php 
$pagesize = isset($_GET["num"]) ? intval($_GET["num"]) : 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
if (isset($_GET["kw"])) {
	$rs = $DB->query("SELECT A.* FROM pre_sitetask A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE B.name LIKE :kw ORDER BY A.sort ASC LIMIT " . $offset . "," . $pagesize, array(':kw' => '%' . $kw . '%'));
} elseif (isset($_GET["id"])) {
	$rs = $DB->query("SELECT * FROM pre_sitetask WHERE id=:id ORDER BY sort ASC LIMIT " . $offset . "," . $pagesize, array(':id' => $id));
} else {
	$rs = $DB->query("SELECT * FROM pre_sitetask ORDER BY sort ASC LIMIT " . $offset . "," . $pagesize);
}
while ($res = $rs->fetch()) {
	$type = "今日";
	if ($res["task"] == 1) {
		$type = "总计";
	}
	$tasktype = sitetask_type($res["task"]);
	if ($res["task"] == 5) {
		$tasktype = $tasktype . "任务";
	} else {
		$tasktype = $type . $tasktype . "任务";
	}
	$condition = "<font color=\"red\">" . $res["value"] . "|" . $res["money"] . "</font><br>";
	echo "<tr><td>" . $res["id"] . "</td><td>" . $res["name"] . "</td><td>" . $tasktype . "</td><td>" . $res["sort"] . "</td><td>" . $condition . "</td><td>" . ($res["active"] == 1 ? "<span class=\"btn btn-xs btn-success\" onclick=\"setActive(" . $res["id"] . ",0)\">显示</span>" : "<span class=\"btn btn-xs btn-warning\" onclick=\"setActive(" . $res["id"] . ",1)\">隐藏</span>") . "</td><td><a href=\"./sitetask.php?my=edit&id=" . $res["id"] . "\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<span class=\"btn btn-xs btn-danger\" onclick=\"delTask(" . $res["id"] . ")\">删除</span></td></tr>\n";
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
?></ul><script>
$("#blocktitle").html('<?php echo $con;?>');
</script>