<?php

include "../includes/common.php";
$title = "卡密列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?><style>
td{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width:360px;}
</style>
    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
<div class="modal" align="left" id="search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">搜索卡密</h4>
      </div>
      <div class="modal-body">
      <form action="fakakms.php" method="GET">
<input type="hidden" name="tid" value="<?php echo @$_GET["tid"];?>"><br/>
<input type="text" class="form-control" name="kw" placeholder="请输入卡号或密码"><br/>
<input type="submit" class="btn btn-primary btn-block" value="搜索"></form>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php 
adminpermission("faka", 1);
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">请选择商品分类</option>";
while ($res = $rs->fetch()) {
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "add") {
	if (isset($_GET["tid"])) {
		$tid = intval($_GET["tid"]);
		$row = $DB->getRow("select cid,name from pre_tools where tid='" . $tid . "' limit 1");
		$shopname = "<option value=\"" . $tid . "\">" . $row["name"] . "</option>";
		$cid = $row["cid"];
	} else {
		$cid = 0;
	}
	?><div class="block">
<div class="block-title"><h3 class="panel-title">添加卡密</h3></div>
<div class="">
<form action="./fakakms.php?my=add_submit" method="POST" onsubmit="return checkAdd()">
<input type="hidden" name="backurl" value="<?php echo $_SERVER["HTTP_REFERER"];?>"/>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			选择商品
		</span>
		<select id="cid" class="form-control" default="<?php echo $cid;?>"><?php echo $select;?></select>
		<select id="tid" name="tid" class="form-control" default="<?php echo $tid;?>"><?php echo $shopname;?></select>
	</div>
</div>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			卡密列表
		</span>
		<textarea class="form-control" id="kms" name="kms" rows="8" placeholder="一行一张卡，或使用下方随机生成功能"></textarea>
	</div>
</div>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			随机生成
		</span>
		<div class="form-control" style="height: auto;padding: 10px;">
			<div class="row">
				<div class="col-xs-6 col-sm-3">
					<div class="input-group input-group-sm">
						<span class="input-group-addon">数量</span>
						<input type="number" id="gen_num" class="form-control" value="10" min="1" max="1000">
					</div>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="input-group input-group-sm">
						<span class="input-group-addon">卡号</span>
						<input type="number" id="gen_km_len" class="form-control" value="16" min="4" max="64">
					</div>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="input-group input-group-sm">
						<span class="input-group-addon">密码</span>
						<input type="number" id="gen_pw_len" class="form-control" value="16" min="4" max="64">
					</div>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="input-group input-group-sm">
						<span class="input-group-addon">分隔符</span>
						<input type="text" id="gen_split" class="form-control" value=" " maxlength="10" placeholder="空格">
					</div>
				</div>
			</div>
			<div class="row" style="margin-top: 8px;">
				<div class="col-xs-12">
					<label style="margin-right: 15px;"><input type="checkbox" id="gen_use_num" checked> 数字(0-9)</label>
					<label style="margin-right: 15px;"><input type="checkbox" id="gen_use_low" checked> 小写字母(a-z)</label>
					<label style="margin-right: 15px;"><input type="checkbox" id="gen_use_up" checked> 大写字母(A-Z)</label>
					<label style="margin-right: 15px;"><input type="checkbox" id="gen_use_spec"> 特殊字符</label>
				</div>
			</div>
			<div class="row" style="margin-top: 8px;">
				<div class="col-xs-12">
					<button type="button" class="btn btn-primary btn-sm" onclick="generatekms()"><i class="fa fa-magic"></i> 生成卡密</button>
					<button type="button" class="btn btn-default btn-sm" onclick="clearkms()"><i class="fa fa-trash-o"></i> 清空</button>
					<span class="help-block" style="margin-bottom:0;font-size:11px;">提示：生成的卡密会自动填入上方卡密列表框</span>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon">
			分隔符
		</span>
		<input type="text" name="split" value="" class="form-control" placeholder="可自定义卡号和密码之间的分隔符，默认留空为空格"/>
	</div>
</div>
<div class="form-group">
	<div class="input-group">
		<span class="input-group-addon"><label><input id="is_check_repeat" name="is_check_repeat" type="checkbox" value="1">检查重复的卡密</label></span>
	</div>
</div>
<div class="form-group">
	<button type="submit" class="btn btn-primary btn-block">确认提交</button>
	<button type="reset" class="btn btn-default btn-block">重新填写</button>
</div>
</form>
</div>
<div class="panel-footer">
<span class="glyphicon glyphicon-info-sign"></span>
注意：卡密格式：卡号+空格+密码，一行一张卡，如：ABCDEFG 123456789<br/>
只有商品设置里面购买成功后的动作选择自动发卡，该商品才会显示在当前列表
</div>
</div>
<a href="<?php echo isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "fakalist.php";?>" class="btn btn-default btn-block">>>返回发卡库存列表</a>
<?php 
} elseif ($my == "add_submit") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_POST["tid"]);
	$kms = $_POST["kms"];
	$split = $_POST["split"];
	$is_check_repeat = $_POST["is_check_repeat"];
	if ($tid == NULL || $kms == NULL) {
		showmsg("请确保各项不能为空！", 3);
	} else {
		$kms = str_replace(array("\r\n", "\r", "\n"), "[br]", $kms);
		$match = explode("[br]", $kms);
		$c = 0;
		foreach ($match as $val) {
			if (empty($split)) {
				$km_arr = explode(" ", $val);
			} else {
				$km_arr = explode($split, $val);
			}
			$km = trim(daddslashes($km_arr[0]));
			$pw = trim(daddslashes($km_arr[1]));
			if ($km == "") {
				continue;
			}
			if ($is_check_repeat == 1) {
				if ($DB->getRow("select * from pre_faka where km='" . $km . "' limit 1")) {
					continue;
				}
			}
			$sql = $DB->exec("INSERT INTO `pre_faka` (`tid`,`km`,`pw`,`addtime`) VALUES ('" . $tid . "','" . $km . "','" . $pw . "',NOW())");
			if ($sql) {
				$c++;
			} else {
				showmsg("添加卡密失败！" . $DB->error());
			}
		}
		showmsg("成功添加<b>" . $c . "</b>张卡密！<br/><br/><a href=\"" . $_POST["backurl"] . "\">>>返回发卡库存列表</a>", 1);
	}
} elseif ($my == "del") {
	if (!checkRefererHost()) {
		exit;
	}
	$id = intval($_GET["id"]);
	$sql = $DB->exec("DELETE FROM pre_faka WHERE kid='" . $id . "'");
	exit("<script language='javascript'>history.go(-1);</script>");
} elseif ($my == "qk") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"]);
	echo "<div class=\"block\">\r\n<div class=\"block-title\"><h3 class=\"panel-title\">清空卡密</h3></div>\r\n<div class=\"box\">\r\n您确认要清空该商品的所有卡密吗？清空后无法恢复！<br><a href=\"./fakakms.php?my=qk2&tid=" . $tid . "\">确认</a> | <a href=\"javascript:history.back();\">返回</a></div></div>";
} elseif ($my == "qk2") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"]);
	?><div class="block">
<div class="block-title"><h3 class="panel-title">清空卡密</h3></div>
<div class="box"><?php 
	if ($DB->exec("DELETE FROM pre_faka WHERE tid='" . $tid . "'") !== false) {
		echo "<div class=\"box\">清空成功.</div>";
	} else {
		echo "<div class=\"box\">清空失败.</div>";
	}
	echo "<hr/><a href=\"./fakakms.php?tid=" . $tid . "\">>>返回卡密列表</a></div></div>";
} elseif ($my == "qkuse") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"]);
	echo "<div class=\"block\">\r\n<div class=\"block-title\"><h3 class=\"panel-title\">清空卡密</h3></div>\r\n<div class=\"box\">\r\n您确认要清空所有卡密吗？清空后无法恢复！<br><a href=\"./fakakms.php?my=qkuse2&tid=" . $tid . "\">确认</a> | <a href=\"javascript:history.back();\">返回</a></div></div>";
} elseif ($my == "qkuse2") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"]);
	?><div class="block">
<div class="block-title"><h3 class="panel-title">清空卡密</h3></div>
<div class="box"><?php 
	if ($DB->exec("DELETE FROM pre_faka WHERE tid='" . $tid . "' and orderid!=0") !== false) {
		echo "<div class=\"box\">清空成功.</div>";
	} else {
		echo "<div class=\"box\">清空失败.</div>";
	}
	echo "<hr/><a href=\"./fakakms.php?tid=" . $tid . "\">>>返回卡密列表</a></div></div>";
} elseif ($my == "del2") {
	if (!checkRefererHost()) {
		exit;
	}
	$checkbox = $_POST["checkbox"];
	$i = 0;
	foreach ($checkbox as $kid) {
		$DB->exec("DELETE FROM pre_faka WHERE kid='" . $kid . "' limit 1");
		$i++;
	}
	exit("<script language='javascript'>alert('成功删除" . $i . "张卡密');history.go(-1);</script>");
} else {
	if (isset($_GET["kw"])) {
		$tid = intval($_GET["tid"]);
		$sql = " `tid`='" . $tid . "' and (`km`='" . $_GET["kw"] . "' or `pw`='" . $_GET["kw"] . "')";
		$link = "&tid=" . $tid . "&kw=" . $_GET["kw"];
	} elseif (isset($_GET["kid"])) {
		$sql = " `kid`='" . $_GET["kid"] . "'";
		$link = "&kid=" . $_GET["kid"];
	} elseif (isset($_GET["orderid"])) {
		$sql = " `orderid`='" . $_GET["orderid"] . "'";
		$link = "&orderid=" . $_GET["orderid"];
	} elseif (isset($_GET["tid"])) {
		$tid = intval($_GET["tid"]);
		$row = $DB->getRow("select * from pre_tools where tid='" . $tid . "' limit 1");
		if (!$row) {
			showmsg("商品不存在", 3);
		}
		$sql = " `tid`='" . $tid . "'";
		$link = "&tid=" . $tid;
	} else {
		showmsg("商品不存在", 3);
	}
	$numrows = $DB->getColumn("SELECT count(*) from pre_faka WHERE" . $sql);
	?><div class="modal" align="left" id="output" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">导出未使用的卡密</h4>
      </div>
      <div class="modal-body">
      <form action="download.php" method="GET">
<input type="hidden" name="act" value="kms">
<input type="hidden" name="tid" value="<?php echo $tid;?>">
<input type="hidden" name="use" value="0">
<div class="form-group">
	<div class="input-group">
		<input type="number" class="form-control" name="num" placeholder="请输入要导出的数量">
		<span class="input-group-btn">
			<select name="isuse" class="form-control" style="width:140px">
				<option value="0">不改为已使用</option>
				<option value="1">同时改为已使用</option>
			</select>
		</span>
		
	</div>
</div>
<input type="submit" class="btn btn-primary btn-block" value="导出"></form>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="block">
	<div class="block-title">
		<h2><?php echo $row["name"];?> - 卡密库存列表</h2>
	</div>
	<div class="">
	<a href="fakakms.php?my=add&tid=<?php echo $tid;?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;加卡</a>
  <a href="fakakms.php?my=qk&tid=<?php echo $tid;?>" class="btn btn-danger">清空</a>
  <a href="fakakms.php?my=qkuse&tid=<?php echo $tid;?>" class="btn btn-danger">清空已使用</a>
  <div class="btn-group">
  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    导出 <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <li><a href="download.php?act=kms<?php echo $link;?>&use=0">未使用</a></li>
    <li><a href="download.php?act=kms<?php echo $link;?>&use=1">已使用</a></li>
    <li><a href="download.php?act=kms<?php echo $link;?>">全部</a></li>
	<li><a href="#" data-toggle="modal" data-target="#output" id="output">指定数量</a></li>
  </ul>
</div>
  <a href="#" data-toggle="modal" data-target="#search" id="search" class="btn btn-primary">搜索</a>
  </div>
	<form name="form1" method="post" action="fakakms.php?my=del2">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>卡号</th><th>密码</th><th>状态</th><th>添加时间</th><th>使用时间</th><th>操作</th></tr></thead>
          <tbody>
<?php 
	$pagesize = 30;
	$pages = ceil($numrows / $pagesize);
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$offset = $pagesize * ($page - 1);
	$rs = $DB->query("SELECT * FROM pre_faka WHERE" . $sql . " order by kid desc limit " . $offset . "," . $pagesize);
	while ($res = $rs->fetch()) {
		if ($res["usetime"] == null) {
			$isuse = "<font color=\"green\">未使用</font>";
		} else {
			$isuse = "<font color=\"red\">已使用</font>(<a href=\"./list.php?id=" . $res["orderid"] . "\" target=\"_blank\">" . $res["orderid"] . "</a>)";
		}
		echo "<tr><td onclick=\"showkms(this)\"><input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["kid"] . "\" onClick=\"unselectall1()\"><b>" . $res["km"] . "</b></td><td>" . $res["pw"] . "</td><td>" . $isuse . "</td><td>" . $res["addtime"] . "</td><td>" . $res["usetime"] . "</td><td><a href=\"./fakakms.php?my=del&id=" . $res["kid"] . "\" class=\"btn btn-xs btn-danger\" onclick=\"return confirm('你确实要删除此卡密吗？');\">删除</a></td></tr>";
	}
	?>          </tbody>
        </table>
<input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox">&nbsp;全选&nbsp;
<input type="submit" name="Submit" value="删除选中">
</div>
</form>
<ul class="pagination"><?php 
	$first = 1;
	$prev = $page - 1;
	$next = $page + 1;
	$last = $pages;
	if ($page > 1) {
		echo "<li><a href=\"fakakms.php?page=" . $first . $link . "\">首页</a></li>";
		echo "<li><a href=\"fakakms.php?page=" . $prev . $link . "\">&laquo;</a></li>";
	} else {
		echo "<li class=\"disabled\"><a>首页</a></li>";
		echo "<li class=\"disabled\"><a>&laquo;</a></li>";
	}
	$start = $page - 10 > 1 ? $page - 10 : 1;
	$end = $page + 10 < $pages ? $page + 10 : $pages;
	for ($i = $start; $i < $page; $i++) {
		echo "<li><a href=\"fakakms.php?page=" . $i . $link . "\">" . $i . "</a></li>";
	}
	echo "<li class=\"disabled\"><a>" . $page . "</a></li>";
	for ($i = $page + 1; $i <= $end; $i++) {
		echo "<li><a href=\"fakakms.php?page=" . $i . $link . "\">" . $i . "</a></li>";
	}
	if ($page < $pages) {
		echo "<li><a href=\"fakakms.php?page=" . $next . $link . "\">&raquo;</a></li>";
		echo "<li><a href=\"fakakms.php?page=" . $last . $link . "\">尾页</a></li>";
	} else {
		echo "<li class=\"disabled\"><a>&raquo;</a></li>";
		echo "<li class=\"disabled\"><a>尾页</a></li>";
	}
	?></ul><?php 
}
?>    </div>
  </div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/fakakms.js?ver=<?php echo VERSION;?>"></script>
<script>
function generateRandomString(length, options) {
	var chars = '';
	if (options.useNum) chars += '0123456789';
	if (options.useLow) chars += 'abcdefghijklmnopqrstuvwxyz';
	if (options.useUp) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if (options.useSpec) chars += '!@#$%^&*()_+-=[]{}|;:,.<>?';
	if (chars === '') chars = '0123456789';
	var result = '';
	for (var i = 0; i < length; i++) {
		result += chars.charAt(Math.floor(Math.random() * chars.length));
	}
	return result;
}
function generatekms() {
	var num = parseInt($("#gen_num").val()) || 10;
	var kmLen = parseInt($("#gen_km_len").val()) || 16;
	var pwLen = parseInt($("#gen_pw_len").val()) || 16;
	var split = $("#gen_split").val() || " ";
	var useNum = $("#gen_use_num").prop("checked");
	var useLow = $("#gen_use_low").prop("checked");
	var useUp = $("#gen_use_up").prop("checked");
	var useSpec = $("#gen_use_spec").prop("checked");
	if (num < 1 || num > 1000) {
		layer.msg('数量必须在1-1000之间', {icon: 2});
		return;
	}
	if (kmLen < 4 || kmLen > 64 || pwLen < 4 || pwLen > 64) {
		layer.msg('卡号和密码长度必须在4-64之间', {icon: 2});
		return;
	}
	var kmOptions = {useNum: useNum, useLow: useLow, useUp: useUp, useSpec: useSpec};
	var kms = [];
	for (var i = 0; i < num; i++) {
		var km = generateRandomString(kmLen, kmOptions);
		var pw = generateRandomString(pwLen, kmOptions);
		kms.push(km + split + pw);
	}
	var existing = $("#kms").val();
	if (existing.trim()) {
		$("#kms").val(existing + "\n" + kms.join("\n"));
	} else {
		$("#kms").val(kms.join("\n"));
	}
	$("input[name='split']").val(split);
	layer.msg('成功生成 ' + num + ' 张卡密', {icon: 1});
}
function clearkms() {
	$("#kms").val('');
}
</script>
</body>
</html>