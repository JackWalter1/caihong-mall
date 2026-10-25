<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("shop", 1);

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

if (isset($_GET["kw"])) {
	$kw = trim(daddslashes($_GET["kw"]));
	$sql = " B.name LIKE '%" . $kw . "%'";
	$numrows = $DB->getColumn("SELECT count(*) FROM pre_recommend A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE" . $sql);
	$con = "包含 <b>" . $kw . "</b> 的共有 <b>" . $numrows . "</b> 个推荐商品";
	$link = "&kw=" . $kw;
} elseif (isset($_GET["id"])) {
	$id = intval($_GET["id"]);
	$numrows = $DB->getColumn("SELECT count(*) from pre_recommend where id='" . $id . "'");
	$sql = " id='" . $id . "'";
	$con = "推荐商品列表";
	$link = "&id=" . $id;
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_recommend");
	$sql = " 1";
	$con = "系统共有 <b>" . $numrows . "</b> 个推荐商品";
}
?>	  <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>商品ID</th><th>商品名称</th><th title="数字越小越靠前">排序</th><th>添加时间</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php 
$pagesize = isset($_GET["num"]) ? intval($_GET["num"]) : 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT A.*,B.name FROM pre_recommend A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE" . $sql . " ORDER BY A.sort ASC, A.id DESC LIMIT " . $offset . "," . $pagesize);
while ($res = $rs->fetch()) {
	echo "<tr><td><b>" . $res["id"] . "</b></td><td><a href=\"./shoplist.php?tid=" . $res["tid"] . "\">" . $res["tid"] . "</a></td><td>" . $res["name"] . "</td><td>" . $res["sort"] . "</td><td>" . $res["addtime"] . "</td><td>" . ($res["active"] == 1 ? "<span class=\"btn btn-xs btn-success\" onclick=\"setActive(" . $res["id"] . ",0)\">显示</span>" : "<span class=\"btn btn-xs btn-warning\" onclick=\"setActive(" . $res["id"] . ",1)\">隐藏</span>") . "</td><td><a href=\"./recommend.php?my=edit&id=" . $res["id"] . "\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<span class=\"btn btn-xs btn-danger\" onclick=\"delTool(" . $res["id"] . ")\">删除</span></td></tr>\r\n";
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