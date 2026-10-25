<?php

include "../includes/common.php";
$title = "网站对接配置";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
<?php 
adminpermission("shequ", 1);
$ThirdPluginsList = \lib\Plugin::getThirdPluginsList();
$shequlist = "";
$ThirdPluginsArray = array();
foreach ($ThirdPluginsList as $res) {
	$ThirdPluginsArray[] = array($res["code"] => $res);
	$shequlist .= "<option value=\"" . $res["code"] . "\">" . $res["title"] . "</option>";
	$shequ_name[$res["code"]] = $res["title"];
}
$ThirdPluginsJsArray = json_encode($ThirdPluginsArray);
$ThirdPluginsJsArray = str_replace(array("[", "]"), array("", ""), $ThirdPluginsJsArray);
$ThirdPluginsJsArray = str_replace("},{", ",", $ThirdPluginsJsArray);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "add") {
	?><div class="block">
<div class="block-title"><h3 class="panel-title">添加一个社区/卡盟网站对接</h3></div><div class=""><form action="./shequlist.php?my=add_submit" method="POST">
<div class="form-group">
<label>对接网站类型:</label><span class="pull-right">[<a href="?my=refresh">刷新对接插件列表</a>]</span><br>
<div class="input-group">
<select class="form-control" name="type"><?php echo $shequlist;?></select>
<a tabindex="0" class="input-group-addon" role="button" data-toggle="popover" data-trigger="focus" title="" data-placement="bottom" data-content="对接网站类型是指要对接的网站所使用的网站程序类型，并不代表特定的网站！具体是什么类型请咨询对接网站客服"><span class="glyphicon glyphicon-info-sign"></span></a>
</div>
</div>
<div class="form-group">
<label id="url">网站域名:</label><br>
<div class="row">
<div class="col-xs-4 col-md-3" style="padding-right: 0px;">
	<select class="form-control" name="protocol"><option value="0" selected>http://</option><option value="1">https://</option></select>
</div>
<div class="col-xs-8 col-md-9" style="padding-left: 0px;">
	<input type="text" class="form-control" name="url" value="" required placeholder="输入对接网站域名">
</div>
</div>
</div>
<div class="form-group">
<label id="username">登录账号:</label><br>
<input type="text" class="form-control" name="username" value="" required>
</div>
<div class="form-group">
<label id="password">登录密码:</label><br>
<input type="text" class="form-control" name="password" value="" required>
</div>
<div class="form-group" id="paypwd_show" style="display:none;">
<label id="paypwd">支付密码:</label><br>
<input type="text" class="form-control" name="paypwd" value="" placeholder="没有请留空">
</div>
<div class="form-group" id="paytype_show" style="display:none;">
<label id="paytype">支付方式:</label><br>
<select class="form-control" name="paytype"><option value="0">点数</option><option value="1" selected>余额</option></select>
</div>
<div class="form-group">
<label>备注:</label><br>
<input type="text" class="form-control" name="remark" value="" placeholder="选填">
</div>
<input type="button" class="btn btn-default btn-block" onclick="checkurl()" value="检测目标网站连通性">
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form><br/><a href="./shequlist.php">>>返回对接列表</a></div>
<div id="alert-footer"></div>
</div>
<?php 
} elseif ($my == "edit") {
	$id = intval($_GET["id"]);
	$row = $DB->getRow("select * from pre_shequ where id=:id limit 1", array(':id' => $id));
	$permission = explode(",", $row["permission"]);
	?><div class="block">
<div class="block-title"><h3 class="panel-title">修改对接网站信息</h3></div>
<div class="">
  <form action="./shequlist.php?my=edit_submit&id=<?php echo $id;?>" method="post" role="form">
  <div class="form-group">
<label>对接网站类型:</label><span class="pull-right">[<a href="?my=refresh">刷新对接插件列表</a>]</span><br>
<div class="input-group">
<select class="form-control" name="type" default="<?php echo $row["type"];?>"><?php echo $shequlist;?></select>
<a tabindex="0" class="input-group-addon" role="button" data-toggle="popover" data-trigger="focus" title="" data-placement="bottom" data-content="对接网站类型是指要对接的网站所使用的网站程序类型，并不代表特定的网站！具体是什么类型请咨询对接网站客服"><span class="glyphicon glyphicon-info-sign"></span></a>
</div>
</div>
<div class="form-group">
<label id="url">网站域名:</label><br>
<div class="row">
<div class="col-xs-4 col-md-3" style="padding-right: 0px;">
	<select class="form-control" name="protocol" default="<?php echo $row["protocol"];?>"><option value="0">http://</option><option value="1">https://</option></select>
</div>
<div class="col-xs-8 col-md-9" style="padding-left: 0px;">
	<input type="text" class="form-control" name="url" value="<?php echo $row["url"];?>" required placeholder="输入对接网站域名">
</div>
</div>
</div>
<div class="form-group">
<label id="username">登录账号:</label><br>
<input type="text" class="form-control" name="username" value="<?php echo $row["username"];?>" required>
</div>
<div class="form-group">
<label id="password">登录密码:</label><br>
<input type="text" class="form-control" name="password" value="<?php echo $row["password"];?>" required>
</div>
<div class="form-group" id="paypwd_show" style="display:none;">
<label id="paypwd">支付密码:</label><br>
<input type="text" class="form-control" name="paypwd" value="<?php echo $row["paypwd"];?>" placeholder="没有请留空">
</div>
<div class="form-group" id="paytype_show" style="display:none;">
<label id="paytype">支付方式:</label><br>
<select class="form-control" name="paytype" default="<?php echo $row["paytype"];?>"><option value="0">点数</option><option value="1">余额</option></select>
</div>
<div class="form-group">
<label>下单成功后订单状态:</label><br>
<div class="input-group">
<select class="form-control" name="result" default="<?php echo $row["result"];?>"><option value="1">已完成（默认）</option><option value="2">正在处理</option></select>
<a tabindex="0" class="input-group-addon" role="button" data-toggle="popover" data-trigger="focus" title="" data-placement="bottom" data-content="下单成功后如果为正在处理，用户前台查询订单的时候会自动同步订单状态，如果对接订单已完成会自动将本站订单状态也改成已完成。"><span class="glyphicon glyphicon-info-sign"></span></a>
</div>
</div>
<div class="form-group">
<label>备注:</label><br>
<input type="text" class="form-control" name="remark" value="<?php echo $row["remark"];?>" placeholder="选填">
</div>
<input type="button" class="btn btn-default btn-block" onclick="checkurl()" value="检测目标网站连通性">
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form><br/><a href="./shequlist.php">>>返回对接列表</a></div>
<div id="alert-footer"></div>
</div><script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>
<?php 
} elseif ($my == "add_submit") {
	$type = $_POST["type"];
	$protocol = $_POST["protocol"];
	$url = $_POST["url"];
	$username = $_POST["username"];
	$password = $_POST["password"];
	$paypwd = $_POST["paypwd"];
	$paytype = $_POST["paytype"];
	$remark = $_POST["remark"];
	if ($type == NULL || $url == NULL || $username == NULL || $password == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if (strpos($url, "/") !== false) {
			showmsg("仅填写域名，不要带有/等符号", 1);
		}
		$rows = $DB->getRow("select * from pre_shequ where url=:url and username=:username limit 1", array(':url' => $url, ':username' => $username));
		if ($rows) {
			showmsg("你所添加的记录已存在！", 3);
		}
		$sql = "insert into `pre_shequ` (`url`,`username`,`password`,`paypwd`,`paytype`,`type`,`remark`,`protocol`) values (:url,:username,:password,:paypwd,:paytype,:type,:remark,:protocol)";
		$data = array(':url' => $url, ':username' => $username, ':password' => $password, ':paypwd' => $paypwd, ':paytype' => $paytype, ':type' => $type, ':remark' => $remark, ':protocol' => $protocol);
		if ($DB->exec($sql, $data) !== false) {
			showmsg("添加对接网站成功！<br/><br/><a href=\"./shequlist.php\">>>返回对接列表</a>", 1);
		} else {
			showmsg("添加对接网站失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("select * from pre_shequ where id=:id limit 1", array(':id' => $id));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$type = $_POST["type"];
	$protocol = $_POST["protocol"];
	$url = $_POST["url"];
	$username = $_POST["username"];
	$password = $_POST["password"];
	$paypwd = $_POST["paypwd"];
	$paytype = $_POST["paytype"];
	$result = $_POST["result"];
	$remark = $_POST["remark"];
	if ($type == NULL || $url == NULL || $username == NULL || $password == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$sql = "UPDATE `pre_shequ` SET `url`=:url,`username`=:username,`password`=:password,`paypwd`=:paypwd,`paytype`=:paytype,`type`=:type,`result`=:result,`remark`=:remark,`protocol`=:protocol WHERE `id`=:id";
		$data = array(':url' => $url, ':username' => $username, ':password' => $password, ':paypwd' => $paypwd, ':paytype' => $paytype, ':type' => $type, ':result' => $result, ':remark' => $remark, ':protocol' => $protocol, ':id' => $id);
		if ($DB->exec($sql, $data) !== false) {
			showmsg("修改对接网站成功！<br/><br/><a href=\"./shequlist.php\">>>返回用户列表</a>", 1);
		} else {
			showmsg("修改对接网站失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "delete") {
	$id = intval($_GET["id"]);
	$sql = "DELETE FROM pre_shequ WHERE id=:id";
	if ($DB->exec($sql, array(':id' => $id)) !== false) {
		showmsg("删除成功！<br/><br/><a href=\"./shequlist.php\">>>返回对接列表</a>", 1);
	} else {
		showmsg("删除失败！" . $DB->error(), 4);
	}
} elseif ($my == "refresh") {
	\lib\Plugin::refreshThirdPluginsList();
	exit("<script language='javascript'>alert('刷新对接插件列表成功！');history.go(-1);</script>");
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_shequ");
	?><div class="modal" align="left" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
				</button>
				<h4 class="modal-title">对接插件说明</h4>
			</div>
			<div class="modal-body">
			对接插件目录：/includes/plugins/，请将符合要求的对接插件源码上传到该目录，插件名称必须以third_开头，然后点击 <a href="?my=refresh">刷新插件列表</a> 即可显示在对接网站类型列表中。
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>
<div class="block">
<div class="block-title clearfix">
<h2>系统共有 <b><?php echo $numrows;?></b> 个对接网站</h2>
</div>
<a href="./shequlist.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加一个对接站点</a>&nbsp;<a href="javascript:$('#myModal').modal('show');" class="btn btn-default"><i class="fa fa-question-circle-o"></i>&nbsp;对接插件</a>&nbsp;<a href="https://404.6v6.ren/dui-jie/1.html" target="_blank" class="btn btn-success"><i class="fa fa-star"></i>&nbsp;推荐对接网站</a>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>网站域名</th><th>类型</th><th>用户名</th><th>密码</th><th>备注</th><th>操作</th></tr></thead>
          <tbody>
<?php 
	$rs = $DB->query("SELECT * FROM pre_shequ WHERE 1 order by id asc");
	while ($res = $rs->fetch()) {
		echo "<tr><td><b>" . $res["id"] . "</b></td><td><a href=\"" . ($res["protocol"] == 1 ? "https://" : "http://") . $res["url"] . "/\" target=\"_blank\" rel=\"noreferrer\">" . $res["url"] . "</a></td><td><font color=blue>" . $shequ_name[$res["type"]] . "</font></td><td>" . $res["username"] . "</td><td>******</td><td>" . $res["remark"] . "</td><td><a href=\"./shequlist.php?my=edit&id=" . $res["id"] . "\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<a href=\"./shequlist.php?my=delete&id=" . $res["id"] . "\" class=\"btn btn-xs btn-danger\" onclick=\"return confirm('你确实要删除此记录吗？');\">删除</a></td></tr>";
	}
	?>          </tbody>
        </table>
      </div>
<?php 
}
?>    </div>
  </div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.0.1/layer.js"></script>
<script>
var pluginArray = <?php echo $ThirdPluginsJsArray;?>;
function confirmdel(isclone){
	if(isclone == 1){
		return confirm('删除此对接站点会导致克隆的商品对接下单失败，你确实要删除此对接站点吗？');
	}else{
		return confirm('你确实要删除此对接站点吗？');
	}
}
function checkurl(){
	var url = $("input[name='url']").val();
	if(url == ''){layer.alert('请先填写网站域名！');return false;}
	if(url.indexOf('http://')<0 && url.indexOf('https://')<0 && url.substr(-1) != '/'){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=checkshequ",
			data : {url:url},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.msg('连通性良好');
				}else{
					layer.alert('该网站由于防火墙原因国外主机无法连接，请使用国内主机');
				}
			} ,
			error:function(data){
				layer.close(ii);
				layer.msg('目标社区连接超时');
				return false;
			}
		});
	}else{
		layer.alert('网站域名不能带http和/符号，只填写域名');
	}
}
$(document).ready(function(){
$("select[name='type']").change(function(){
	var type = $(this).val();
	var plugin = pluginArray[type];
	if(plugin){
		var input = plugin.input;
		$("#url").text(input.url + ':');
		$("#username").text(input.username + ':');
		$("#password").text(input.password + ':');
		if(input.paypwd){
			$("#paypwd").text(input.paypwd + ':');
			$("#paypwd_show").show();
		}else{
			$("#paypwd_show").hide();
		}
		if(input.paytype){
			$("#paytype").text(input.paytype + ':');
			$("#paytype_show").show();
		}else{
			$("#paytype_show").hide();
		}
		if(plugin.showip){
			$.ajax({
				type : "GET",
				url : "ajax.php?act=getServerIp",
				async: true,
				dataType : 'json',
				success : function(data) {
					$("#alert-footer").html('<font color=red>请设置当前服务器IP为白名单：'+data.ip+'</font>');
				}
			});
		}
	}
});
if($("select[name='type']").length>0){
	$("select[name='type']").change();
}
})
</script>