<?php

include "../includes/common.php";
$title = "库存不足提醒";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-md-12 center-block" style="float: none;">
<?php 
adminpermission("faka", 1);
?><div class="widget">
<ul class="list-group">
	<li class="list-group-item">
		注：商品隐藏、商品下架、对接商品不会显示。
	</li>
<?php 
$stockrows = $DB->getAll("SELECT `tid`,`name`,`active` FROM `pre_tools` WHERE `active` = 1 AND `close` = 0 AND `is_curl` = 4");
$count = 0;
foreach ($stockrows as $stockrow) {
	$stockTotal = $DB->getColumn("SELECT count(`tid`) FROM `pre_faka` WHERE `orderid` = 0 AND `tid` = :tid", array(":tid" => $stockrow["tid"]));
	if ($stockTotal < 1) {
		echo "<li class=\"list-group-item\"><span class=\"btn-sm btn-warning\">告警</span>&nbsp;&nbsp;" . $stockrow["name"] . "→&nbsp;&nbsp;<font color=\"red\">库存为0</font>&nbsp;&nbsp;&nbsp;<a href=\"./fakakms.php?my=add&tid=" . $stockrow["tid"] . "\">进入补卡</a></li>";
		$count++;
	}
}
?></ul>
</div>
    </div>
  </div>