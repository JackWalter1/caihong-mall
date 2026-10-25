<?php

include "../includes/common.php";
$title = "订单状态监控";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
<?php 
adminpermission("shequ", 1);
if ($_POST["do"] == "submit") {
	$updatestatus = $_POST["updatestatus"];
	$updatestatus_interval = $_POST["updatestatus_interval"];
	saveSetting("updatestatus", $updatestatus);
	saveSetting("updatestatus_interval", $updatestatus_interval);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
}
?><div class="modal fade" align="left" id="showresult" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">手动同步订单状态</h4>
      </div>
      <div class="modal-body" id="result_content">
	  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="block">
<div class="block-title"><h3 class="panel-title">订单状态监控设置</h3></div>
<div class="alert alert-info">订单状态监控功能可以定时将“正在处理”状态的订单批量检测并更新状态，支持全部类型的对接站点，对接站点需要设置“下单成功后订单状态”是“正在处理”才能生效。</br>订单状态监控不是必须的，用户前台查询订单的时候也会自动更新订单状态，无需监控。</div>
<div class="alert alert-success"><b>监控地址：</b><a style="color:white" href="./set.php?mod=cron">点此查看</a><br/>
<b>上次运行时间：<?php echo $conf["updatestatus_lasttime"];?></b></div>

<div class="">
  <form action="./orderjk.php" method="post" role="form"><input type="hidden" name="do" value="submit"/>
	<div class="form-group">
	  <label>是否开启订单状态监控</label>
	  <select class="form-control" name="updatestatus" default="<?php echo $conf["updatestatus"];?>"><option value="0">关闭</option><option value="1">开启</option></select>
	</div>
	<div class="form-group">
	  <label>订单状态检测时间间隔</label>
	  <div class="input-group"><input type="text" name="updatestatus_interval" value="<?php echo $conf["updatestatus_interval"];?>" class="form-control" placeholder=""/><span class="input-group-addon">分钟</span></div><font color="green">设置的时间间隔是下单之后间隔x分钟去检测，检测订单状态还是未完成的话，再间隔x分钟去检测一次</font>
	</div>
	<div class="form-group">
	  <input type="submit" name="submit" value="修改" class="btn btn-primary btn-block"/><br/>
	  <a href="javascript:showresult()" class="btn btn-default form-control">手动执行一次订单状态监控</a><br/>
	 </div>
	</div>
  </form>
</div>
</div>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
function showresult(){
	var url = '../cron.php?do=updatestatus&key=<?php echo $conf["cronkey"];?>&test=1';
	var content = '<div id="loadiframe" style="text-align:center;"><i class="fa fa-spinner fa-spin"></i>正在加载...</div><iframe src="'+url+'" frameborder="0" scrolling="auto" seamless="seamless" width="100%"  onload="$(\'#loadiframe\').hide();"></iframe>';
	$("#showresult").modal('show');
	$("#result_content").html(content);
}

</script>
    </div>
  </div>