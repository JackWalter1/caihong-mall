<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("shop", 1);
$classlist = $DB->getAll("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">未分类</option>";
$shua_class[0] = "未分类";
foreach ($classlist as $res) {
	$shua_class[$res["cid"]] = $res["name"];
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
if ($_SESSION["price_class"]) {
	$price_class = $_SESSION["price_class"];
} else {
	$pricelist = $DB->getAll("SELECT * FROM pre_price order by id asc");
	$price_class[0] = "不加价";
	foreach ($pricelist as $res) {
		$price_class[$res["id"]] = $res["name"];
	}
}
$pagesize = isset($_GET["num"]) ? intval($_GET["num"]) : 30;
$orderby = "A.tid desc";
if (isset($_GET["kw"])) {
	$kw = trim(daddslashes($_GET["kw"]));
	$sql = " A.name LIKE '%" . $kw . "%' AND goods_sid != 0 and audit_status='0'";
	if (is_numeric($kw)) {
		$sql .= " OR A.tid='" . $kw . "'";
	}
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools A where" . $sql);
	$con = "包含 <b>" . $kw . "</b> 的共有 <b>" . $numrows . "</b> 个商品";
	$link = "&kw=" . $kw;
} elseif (isset($_GET["cid"])) {
	$cid = intval($_GET["cid"]);
	$sql = " A.cid='" . $cid . "' AND goods_sid != 0 and audit_status='0'";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools A where" . $sql);
	$con = "分类 <a href=\"../?cid=" . $cid . "\" target=\"_blank\">" . $shua_class[$cid] . "</a> 共有 <b>" . $numrows . "</b> 个商品";
	$link = "&cid=" . $cid;
	$orderby = "A.sort asc";
	if ($pagesize < $numrows) {
		$pagesize = $numrows;
	}
} elseif (isset($_GET["prid"])) {
	$prid = intval($_GET["prid"]);
	$sql = " prid='" . $prid . "' AND goods_sid != 0 and audit_status='0'";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where" . $sql);
	$con = "加价模板 " . $price_class[$prid] . " 共有 <b>" . $numrows . "</b> 个商品";
	$link = "&prid=" . $prid;
} elseif (isset($_GET["tid"])) {
	$tid = intval($_GET["tid"]);
	$sql = " tid='" . $tid . "' AND goods_sid != 0 and audit_status='0'";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where" . $sql);
	$con = "商品列表";
	$link = "&tid=" . $tid;
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where goods_sid != 0 and audit_status=0");
	$sql = "  goods_sid != 0 and audit_status=0";
	$con = "系统共有 <b>" . $numrows . "</b> 个审核商品";
}
?><form name="form1" id="form1">
    <div class="table-responsive">
        <table class="table table-striped" id="shoplist">
            <thead><tr><th>商品名称</th><th>售卖价格</th><th>供货商ID</th><th class="<?php echo isset($_GET["cid"]) ? "hide" : "";?>">所属分类</th><th>操作</th></tr></thead>
            <tbody>
            <?php 
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT A.*,B.name classname FROM pre_tools A LEFT JOIN pre_class B ON A.cid=B.cid WHERE" . $sql . " order by " . $orderby . " limit " . $offset . "," . $pagesize);
while ($res = $rs->fetch()) {
	echo "<tr><td><input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["tid"] . "\" onClick=\"unselectall1()\">&nbsp;<a href=\"javascript:show(" . $res["tid"] . ")\" style=\"color:#000\">" . $res["name"] . "</a></td><td><font color=\"blue\">" . $res["sup_price"] . "</font></td><td>" . $res["goods_sid"] . "\r\n</td><td class=\"" . (isset($_GET["cid"]) ? "hide" : "") . "\"><a href=\"./shoplist.php?cid=" . $res["cid"] . "\">" . ($res["classname"] ?: "未分类") . "</a></td><td><a href=\"./supshoplist.php?my=edit&tid=" . $res["tid"] . "\" class=\"btn btn-info btn-xs\">通过</a>&nbsp;<span href=\"./shopedit.php?my=delete&tid=" . $res["tid"] . "\" class=\"btn btn-xs btn-danger\" onclick=\"delTool(" . $res["tid"] . ")\">删除</span></td></tr>\r\n";
}
?>            </tbody>
        </table>
        <input type="hidden" name="prid"/>
        <input type="hidden" name="stock"/>
        <label><input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox">全选</label>&nbsp;
        <select name="aid"><option selected>批量操作</option><option value="10">&gt;改加价模板</option><option value="5">&gt;删除选中</option><option value="6">&gt;复制选中</option></select><button type="button" onclick="change()">执行</button>&nbsp;&nbsp;
        <select name="cid"><option selected>将选定商品移动到分类</option><?php echo $select;?></select><button type="button" onclick="move()">确定移动</button>
    </div>
</form>
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