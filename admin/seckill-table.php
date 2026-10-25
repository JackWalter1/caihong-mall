<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("shop", 1);
if (isset($_GET["kw"])) {
	$kw = trim(daddslashes($_GET["kw"]));
	$sql = " B.name LIKE '%" . $kw . "%'";
	$numrows = $DB->getColumn("SELECT count(*) FROM pre_seckillshop A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE" . $sql);
	$con = "包含 <b>" . $kw . "</b> 的共有 <b>" . $numrows . "</b> 个商品";
	$link = "&kw=" . $kw;
} elseif (isset($_GET["id"])) {
	$id = intval($_GET["id"]);
	$numrows = $DB->getColumn("SELECT count(*) from pre_seckillshop where id='" . $id . "'");
	$sql = " id='" . $id . "'";
	$con = "秒杀商品列表";
	$link = "&id=" . $id;
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_seckillshop");
	$sql = " 1";
	$con = "系统共有 <b>" . $numrows . "</b> 个秒杀商品";
}
?>	  <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>商品ID</th><th>商品名称</th><th title="数字越小越靠前">排序</th><th>秒杀设置</th><th>剩余数量</th><th>秒杀时间</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php 
$pagesize = isset($_GET["num"]) ? intval($_GET["num"]) : 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT A.*,B.name FROM pre_seckillshop A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE" . $sql . " ORDER BY A.sort ASC LIMIT " . $offset . "," . $pagesize);
while ($res = $rs->fetch()) {
	echo "<tr><td><a href=\"./shoplist.php?tid=" . $res["tid"] . "\">" . $res["tid"] . "</a></td><td>" . $res["name"] . "</td><td>" . $res["sort"] . "</td><td>秒杀价格:<font color=\"red\">" . $res["price"] . "</font>元&nbsp(下单<font color=\"red\">" . $res["num"] . "</font>份)</td><td>剩余<font color=\"red\">" . ($res["value"] - $res["count"]) . "</font>份商品未秒杀</td><td>" . $res["starttime"] . "至" . $res["endtime"] . "</td><td>" . ($res["active"] == 1 ? "<span class=\"btn btn-xs btn-success\" onclick=\"setActive(" . $res["id"] . ",0)\">显示</span>" : "<span class=\"btn btn-xs btn-warning\" onclick=\"setActive(" . $res["id"] . ",1)\">隐藏</span>") . "</td><td><a href=\"./seckill.php?my=edit&id=" . $res["id"] . "\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<span class=\"btn btn-xs btn-danger\" onclick=\"delTool(" . $res["id"] . ")\">删除</span></td></tr>\r\n";
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