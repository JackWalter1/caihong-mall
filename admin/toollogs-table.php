<?php
include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission('shop', 1);
$sqls = "";
$links = '';
$sql = " 1";
$sql .= $sqls;
$link = $links;
$numrows = $DB->getColumn("SELECT count(*) FROM pre_toollogs WHERE " . $sql);
$con = '系统共有 <b>' . $numrows . '</b> 条';
?>	  <div class="table-responsive">
<form name="form1" id="form1">
        <table class="table table-striped table-bordered table-vcenter orderList">
          <thead><tr><th width="40"><input type="checkbox" name="chkAll" id="chkAll" onClick="checkAll(this)"></th><th>ID</th><th>日志内容</th><th>上架时间</th><th>操作</th></tr></thead>
          <tbody>
<?php 
$pagesize = isset($_GET['num']) ? intval($_GET['num']) : 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM `pre_toollogs` WHERE " . $sql . " ORDER BY id DESC LIMIT " . $offset . "," . $pagesize);
while ($res = $rs->fetch()) {
	echo '<tr><td><input type="checkbox" name="checkbox[]" id="list1" value="' . $res['id'] . '" onClick="unselectall()"></td><td><b>' . $res['id'] . '</b></td><td>' . mb_substr($res['content'], 0, 16, 'utf-8') . '</td><td>' . $res['date'] . '</td><td><a class="btn btn-xs btn-info" href="./toollogsedit.php?my=edit&id=' . $res['id'] . '">编辑</a> <a class="btn btn-xs btn-danger" href="javascript:delOrder(\'' . $res['id'] . '\')">删除</a></td></tr>';
}
?><tr><td colspan="5"><label><input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox">全选</label>&nbsp;
<select name="aid"><option selected>批量操作</option><option value="1">&gt;删除选中</option></select><button type="button" onclick="batchOperation()">执行</button></td></tr>
          </tbody>
        </table>
</form>
<ul class="pagination"><?php 
$first = 1;
$prev = $page - 1;
$next = $page + 1;
$last = $pages;
if ($page > 1) {
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $first . $link . '\')">首页</a></li>';
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $prev . $link . '\')">&laquo;</a></li>';
} else {
	echo '<li class="disabled"><a>首页</a></li>';
	echo '<li class="disabled"><a>&laquo;</a></li>';
}
$start = $page - 10 > 1 ? $page - 10 : 1;
$end = $page + 10 < $pages ? $page + 10 : $pages;
for ($i = $start; $i < $page; $i++) {
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $i . $link . '\')">' . $i . '</a></li>';
}
echo '<li class="disabled"><a>' . $page . '</a></li>';
for ($i = $page + 1; $i <= $end; $i++) {
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $i . $link . '\')">' . $i . '</a></li>';
}
if ($page < $pages) {
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $next . $link . '\')">&raquo;</a></li>';
	echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $last . $link . '\')">尾页</a></li>';
} else {
	echo '<li class="disabled"><a>&raquo;</a></li>';
	echo '<li class="disabled"><a>尾页</a></li>';
}
?></ul><script>
$("#blocktitle").html('<?php echo $con;?>');
</script>