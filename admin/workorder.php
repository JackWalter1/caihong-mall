<?php
include("../includes/common.php");
$title = '工单列表';
include("./head.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
<?php
adminpermission('workorder', 1);
$count1=$DB->getColumn('SELECT count(*) FROM pre_workorder WHERE ts=0');
$count2=$DB->getColumn('SELECT count(*) FROM pre_workorder WHERE ts=0 and status=0');
$count3=$DB->getColumn('SELECT count(*) FROM pre_workorder WHERE ts=0 and status=1');
$count4=$DB->getColumn('SELECT count(*) FROM pre_workorder WHERE ts=0 and status=2');

?>
<div class="block">
<div class="block-title clearfix">
<h2>工单列表&nbsp;&nbsp;<a href="javascript:listTable('start')" class="btn btn-primary btn-xs">全部(<?php echo $count1?>)</a>&nbsp;<a href="javascript:listTable('status=0')" class="btn btn-info btn-xs">待处理(<?php echo $count2?>)</a>&nbsp;<a href="javascript:listTable('status=1')" class="btn btn-warning btn-xs">处理中(<?php echo $count3?>)</a>&nbsp;<a href="javascript:listTable('status=2')" class="btn btn-success btn-xs">已完成(<?php echo $count4?>)</a></h2>
</div>
<div id="listTable"></div>
    </div>
  </div>
</div>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/workorder.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>