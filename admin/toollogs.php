<?php
/**
 * 
**/
include("../includes/common.php");
$title='上架日志';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<style>
.table thead > tr > th{ font-size:15px}
.orderList tbody tr>td:nth-child(3),.orderList tbody tr>td:nth-child(4){max-width:320px;}
.orderList tbody tr>td:nth-child(5),.orderList tbody tr>td:nth-child(6),.orderList tbody tr>td:nth-child(8){min-width:65px;text-align:center}
.orderList tbody tr>td:nth-child(7){min-width:150px;text-align:center}
</style>
    <div class="col-md-12 center-block" style="float: none;">
<?php

adminpermission('shop', 1);

?>
<div class="block">
<div class="block-title clearfix">
<h2 id="blocktitle">上架记录</h2>
<span class="pull-right"><select id="pagesize" class="form-control"><option value="30">30</option><option value="50">50</option><option value="60">60</option><option value="80">80</option><option value="100">100</option></select><span>
</span></span>
</div>
  <form onsubmit="return searchItem()" method="GET" class="form-inline">
  
  <a href="./toollogsedit.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加日志</a>&nbsp;

  <a href="javascript:clearItem()" class="btn btn-default" title="刷新列表"><i class="fa fa-refresh"></i></a>
  
</form>


<div id="listTable"></div>
<div class="panel-footer">
				<span class="glyphicon glyphicon-info-sign"></span>
				自行调用上架日志：/toollogs.php | <a href="/toollogs.php">前往查看上架日志</a>
			</div>
    </div>
    
  </div>
      
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/toollogs.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>