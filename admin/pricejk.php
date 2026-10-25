<?php

include "../includes/common.php";
$title = "对接价格监控";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
<?php 
adminpermission("shequ", 1);
if ($_POST["do"] == "submit") {
	$pricejk_cid = implode(",", $_POST["pricejk_cid"]);
	$pricejk_edit = $_POST["pricejk_edit"];
	$pricejk_time = $_POST["pricejk_time"];
	$pricejk_yile = $_POST["pricejk_yile"];
	saveSetting("pricejk_cid", $pricejk_cid);
	saveSetting("pricejk_edit", $pricejk_edit);
	saveSetting("pricejk_time", $pricejk_time);
	saveSetting("pricejk_yile", $pricejk_yile);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} else {
	$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
	$pricejk_cid = explode(",", $conf["pricejk_cid"]);
	$select = "<option value=\"0\" " . (in_array("0", $pricejk_cid) ? "selected" : "NULL") . ">未分类商品</option>";
	while ($res = $rs->fetch()) {
		$select .= "<option value=\"" . $res["cid"] . "\" " . (in_array($res["cid"], $pricejk_cid) ? "selected" : "NULL") . ">" . $res["name"] . "</option>";
	}
}
$pricejk_lasttime = $DB->getColumn("SELECT v FROM pre_config WHERE k='pricejk_lasttime' limit 1");
?><div class="modal fade" align="left" id="showresult" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">手动同步价格</h4>
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
<div class="block-title"><h3 class="panel-title">对接价格监控设置</h3></div>
<div class="alert alert-info">价格监控可以实现自动修改商品成本价格和商品上下架，部分对接类型还支持库存同步。<b>仅支持使用加价模板的商品！成本价是0的不会同步！</b></div>
<div class="alert alert-success">监控地址：<a style="color:white" href="./set.php?mod=cron">点此查看</a></div>
<table class="table table-condensed table-bordered">
<div class="">
  <form action="./pricejk.php" method="post" role="form"><input type="hidden" name="do" value="submit"/>
	<div class="form-group">
	  <label>选择要监控的商品类别</label><br/>
	  <select name="pricejk_cid[]" multiple="multiple" class="form-control" style="height:180px;"><?php echo $select;?></select>
	  <font color="green">按住Ctrl键可多选</font>
	</div>
	<div class="form-group">
	  <label>在以下情况修改价格</label><br/>
	  <select class="form-control" name="pricejk_edit" default="<?php echo $conf["pricejk_edit"];?>"><option value="0">成本价格与社区价格不符时</option><option value="1">成本价格低于社区价格时</option></select>
	</div>
	<div class="form-group">
	  <label>价格同步频率</label><br/>
	  <select class="form-control" name="pricejk_time" default="<?php echo $conf["pricejk_time"];?>"><option value="10">10秒</option><option value="600">10分钟</option><option value="1200">20分钟</option><option value="1800">30分钟</option><option value="3600">60分钟</option><option value="7200">120分钟</option></select>
	</div>
	<div class="form-group">
	  <label>亿乐/卡商网/卡易信/直客等同步价格方式</label>(未对接的可以无视此选项)<br/>
	  <select class="form-control" name="pricejk_yile" default="<?php echo $conf["pricejk_yile"];?>"><option value="0">下单同时同步价格（无需监控）</option><option value="1">监控批量同步价格（可能会超时）</option></select>
	  <font color="green">此类对接网站无法批量获取商品价格，只能一个一个商品获取，因此改用下单同时同步价格，可以避免监控批量同步价格导致超时，但是用户提交订单的时候如果价格变化会出现提示框。</font>
	</div>
	<div class="form-group">
	  <input type="submit" name="submit" value="修改" class="btn btn-primary btn-block"/><br/>
	  <a href="javascript:showresult()" class="btn btn-default form-control">立即同步一次价格</a><br/>
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
	var url = '../cron.php?do=pricejk&key=<?php echo $conf["cronkey"];?>&test=1';
	var content = '<div id="loadiframe" style="text-align:center;"><i class="fa fa-spinner fa-spin"></i>正在加载...</div><iframe src="'+url+'" frameborder="0" scrolling="auto" seamless="seamless" width="100%"  onload="$(\'#loadiframe\').hide();"></iframe>';
	$("#showresult").modal('show');
	$("#result_content").html(content);
}

</script>
    </div>
  </div>