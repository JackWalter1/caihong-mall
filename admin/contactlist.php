<?php
include "../includes/common.php";
$title = "联系列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
<?php
adminpermission("contact", 1);

// 确保数据表存在（兼容未执行升级脚本的情况）
$DB->exec("CREATE TABLE IF NOT EXISTS `pre_contact` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(50) NOT NULL DEFAULT '' COMMENT '联系方式类型(QQ/微信/手机/邮箱等)',
	`account` varchar(255) NOT NULL DEFAULT '' COMMENT '账号',
	`url` varchar(255) DEFAULT NULL COMMENT '跳转链接',
	`qrcode` varchar(255) DEFAULT NULL COMMENT '二维码图片路径',
	`remark` varchar(255) DEFAULT NULL COMMENT '备注',
	`addtime` datetime DEFAULT NULL COMMENT '添加时间',
	PRIMARY KEY (`id`),
	KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='联系方式表'");

// 兼容旧版本数据表：自动补充缺失的 url / qrcode 字段
$contact_cols = $DB->getAll("SHOW COLUMNS FROM pre_contact");
if ($contact_cols !== false) {
	$has_url = false;
	$has_qrcode = false;
	foreach ($contact_cols as $col) {
		if ($col['Field'] == 'url') $has_url = true;
		if ($col['Field'] == 'qrcode') $has_qrcode = true;
	}
	if (!$has_url) $DB->exec("ALTER TABLE pre_contact ADD COLUMN `url` varchar(255) DEFAULT NULL COMMENT '跳转链接' AFTER `account`");
	if (!$has_qrcode) $DB->exec("ALTER TABLE pre_contact ADD COLUMN `qrcode` varchar(255) DEFAULT NULL COMMENT '二维码图片路径' AFTER `url`");
}

$my = isset($_GET["my"]) ? $_GET["my"] : null;

// 上传联系方式二维码图片，成功返回相对路径（如 assets/img/contact/xxx.png），失败返回 false
function upload_contact_qrcode($file) {
	global $DB;
	if ($file["error"] != UPLOAD_ERR_OK) {
		return false;
	}
	$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
	$allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
	$file_type = $file["type"];
	$file_ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
	if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
		showmsg("只允许上传JPG、PNG、GIF、WEBP格式的图片文件！", 4);
	}
	$max_size = 2 * 1024 * 1024; // 2MB
	if ($file["size"] > $max_size) {
		showmsg("文件大小不能超过2MB！", 4);
	}
	$image_info = @getimagesize($file["tmp_name"]);
	if (!$image_info) {
		showmsg("请上传真实的图片文件！", 4);
	}
	$dir = ROOT . "assets/img/contact/";
	if (!is_dir($dir)) {
		@mkdir($dir, 0755, true);
	}
	$filename = "contact_" . md5(uniqid(mt_rand(), true)) . "." . $file_ext;
	if (move_uploaded_file($file["tmp_name"], $dir . $filename)) {
		compress_image($dir . $filename);
		return "assets/img/contact/" . $filename;
	}
	return false;
}

if ($my == "add") {
	?><div class="block">
<div class="block-title"><h3 class="panel-title">添加联系方式</h3></div><div class=""><form action="./contactlist.php?my=add_submit" method="POST" enctype="multipart/form-data">
<div class="form-group">
<label>联系方式类型:</label><br>
<select class="form-control" name="type" id="type_select">
<option value="QQ">QQ</option>
<option value="微信">微信</option>
<option value="手机号">手机号</option>
<option value="邮箱">邮箱</option>
<option value="Telegram">Telegram</option>
<option value="自定义">自定义其他联系方式</option>
</select>
</div>
<div class="form-group" id="custom_type_group" style="display:none;">
<label>自定义类型名称:</label><br>
<input type="text" class="form-control" name="custom_type" value="" maxlength="50" placeholder="输入自定义联系方式类型，如：钉钉、Skype等">
</div>
<div class="form-group">
<label>账号:</label><br>
<input type="text" class="form-control" name="account" value="" required placeholder="输入联系方式的账号/号码">
</div>
<div class="form-group">
<label>链接:</label><br>
<input type="text" class="form-control" name="url" value="" placeholder="选填，点击可跳转的链接，如：http://wpa.qq.com/msgrd?v=3&amp;uin=123456">
</div>
<div class="form-group">
<label>二维码图片:</label><br>
<input type="file" class="form-control" name="qrcode" accept="image/*">
</div>
<div class="form-group">
<label>备注:</label><br>
<input type="text" class="form-control" name="remark" value="" placeholder="选填，如：客服、售后等">
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form><br/><a href="./contactlist.php">>>返回联系列表</a></div>
<script>
$("#type_select").change(function(){
	if($(this).val()=="自定义"){
		$("#custom_type_group").show();
	}else{
		$("#custom_type_group").hide();
	}
});
</script>
</div>
<?php
} elseif ($my == "edit") {
	$id = intval($_GET["id"]);
	$row = $DB->getRow("select * from pre_contact where id=:id limit 1", array(':id' => $id));
	if (!$row) {
		showmsg("当前记录不存在！", 3);
	}
	?><div class="block">
<div class="block-title"><h3 class="panel-title">修改联系方式</h3></div>
<div class="">
  <form action="./contactlist.php?my=edit_submit&id=<?php echo $id;?>" method="post" role="form" enctype="multipart/form-data">
  <div class="form-group">
<label>联系方式类型:</label><br>
<select class="form-control" name="type" id="type_select" default="<?php echo $row["type"];?>">
<option value="QQ">QQ</option>
<option value="微信">微信</option>
<option value="手机号">手机号</option>
<option value="邮箱">邮箱</option>
<option value="Telegram">Telegram</option>
<option value="自定义">自定义其他联系方式</option>
</select>
</div>
<div class="form-group" id="custom_type_group" style="display:none;">
<label>自定义类型名称:</label><br>
<input type="text" class="form-control" name="custom_type" id="custom_type_input" value="" maxlength="50" placeholder="输入自定义联系方式类型，如：钉钉、Skype等">
</div>
<div class="form-group">
<label>账号:</label><br>
<input type="text" class="form-control" name="account" value="<?php echo htmlspecialchars($row["account"]);?>" required placeholder="输入联系方式的账号/号码">
</div>
<div class="form-group">
<label>链接:</label><br>
<input type="text" class="form-control" name="url" value="<?php echo htmlspecialchars($row["url"]);?>" placeholder="选填，点击可跳转的链接，如：http://wpa.qq.com/msgrd?v=3&amp;uin=123456">
</div>
<div class="form-group">
<label>二维码图片:</label><br>
<input type="file" class="form-control" name="qrcode" accept="image/*">
<?php if(!empty($row["qrcode"])){?>
<br/><img src="../<?php echo $row["qrcode"];?>" style="max-width:120px;margin-top:8px;"><br/>
<label class="checkbox-inline"><input type="checkbox" name="del_qrcode" value="1"> 删除现有二维码图片</label>
<?php }?>
</div>
<div class="form-group">
<label>备注:</label><br>
<input type="text" class="form-control" name="remark" value="<?php echo htmlspecialchars($row["remark"]);?>" placeholder="选填，如：客服、售后等">
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form><br/><a href="./contactlist.php">>>返回联系列表</a></div>
</div><script>
var preset_types = ["QQ","微信","手机号","邮箱","Telegram"];
var current_type = "<?php echo $row["type"];?>";
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
if($.inArray(current_type, preset_types) == -1){
	$("#type_select").val("自定义");
	$("#custom_type_input").val(current_type);
	$("#custom_type_group").show();
}
$("#type_select").change(function(){
	if($(this).val()=="自定义"){
		$("#custom_type_group").show();
	}else{
		$("#custom_type_group").hide();
	}
});
</script>
<?php
} elseif ($my == "add_submit") {
	$type = trim(strip_tags($_POST["type"]));
	$account = trim(strip_tags($_POST["account"]));
	$url = trim(strip_tags($_POST["url"]));
	$remark = trim(strip_tags($_POST["remark"]));
	if ($type == "自定义") {
		$type = trim(strip_tags($_POST["custom_type"]));
	}
	$qrcode = '';
	if (isset($_FILES["qrcode"]) && $_FILES["qrcode"]["error"] != UPLOAD_ERR_NO_FILE) {
		$qrcode = upload_contact_qrcode($_FILES["qrcode"]);
		if ($qrcode === false) {
			showmsg("二维码图片上传失败！", 4);
		}
	}
	if ($type == NULL || $account == NULL) {
		showmsg("保存错误,请确保类型和账号不为空!", 3);
	} else {
		$sql = "insert into `pre_contact` (`type`,`account`,`url`,`qrcode`,`remark`,`addtime`) values (:type,:account,:url,:qrcode,:remark,:addtime)";
		$data = array(':type' => $type, ':account' => $account, ':url' => $url, ':qrcode' => $qrcode, ':remark' => $remark, ':addtime' => $date);
		if ($DB->exec($sql, $data) !== false) {
			showmsg("添加联系方式成功！<br/><br/><a href=\"./contactlist.php\">>>返回联系列表</a>", 1);
		} else {
			showmsg("添加联系方式失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("select * from pre_contact where id=:id limit 1", array(':id' => $id));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$type = trim(strip_tags($_POST["type"]));
	$account = trim(strip_tags($_POST["account"]));
	$url = trim(strip_tags($_POST["url"]));
	$remark = trim(strip_tags($_POST["remark"]));
	if ($type == "自定义") {
		$type = trim(strip_tags($_POST["custom_type"]));
	}
	// 处理二维码：上传新图则替换，勾选删除则清空，否则保留原图
	$qrcode = $rows["qrcode"];
	if (isset($_FILES["qrcode"]) && $_FILES["qrcode"]["error"] != UPLOAD_ERR_NO_FILE) {
		$qrcode = upload_contact_qrcode($_FILES["qrcode"]);
		if ($qrcode === false) {
			showmsg("二维码图片上传失败！", 4);
		}
		if (!empty($rows["qrcode"])) {
			@unlink(ROOT . $rows["qrcode"]);
		}
	} elseif (isset($_POST["del_qrcode"]) && $_POST["del_qrcode"] == 1 && !empty($rows["qrcode"])) {
		@unlink(ROOT . $rows["qrcode"]);
		$qrcode = '';
	}
	if ($type == NULL || $account == NULL) {
		showmsg("保存错误,请确保类型和账号不为空!", 3);
	} else {
		$sql = "UPDATE `pre_contact` SET `type`=:type,`account`=:account,`url`=:url,`qrcode`=:qrcode,`remark`=:remark WHERE `id`=:id";
		$data = array(':type' => $type, ':account' => $account, ':url' => $url, ':qrcode' => $qrcode, ':remark' => $remark, ':id' => $id);
		if ($DB->exec($sql, $data) !== false) {
			showmsg("修改联系方式成功！<br/><br/><a href=\"./contactlist.php\">>>返回联系列表</a>", 1);
		} else {
			showmsg("修改联系方式失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "delete") {
	$id = intval($_GET["id"]);
	$row = $DB->getRow("select * from pre_contact where id=:id limit 1", array(':id' => $id));
	if ($row && !empty($row["qrcode"])) {
		@unlink(ROOT . $row["qrcode"]);
	}
	$sql = "DELETE FROM pre_contact WHERE id=:id";
	if ($DB->exec($sql, array(':id' => $id)) !== false) {
		showmsg("删除成功！<br/><br/><a href=\"./contactlist.php\">>>返回联系列表</a>", 1);
	} else {
		showmsg("删除失败！" . $DB->error(), 4);
	}
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_contact");
	?><div class="block">
<div class="block-title clearfix">
<h2>系统共有 <b><?php echo $numrows;?></b> 条联系方式</h2>
</div>
<a href="./contactlist.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加联系方式</a>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>类型</th><th>账号</th><th>链接</th><th>二维码</th><th>备注</th><th>添加时间</th><th>操作</th></tr></thead>
          <tbody>
<?php
	$rs = $DB->query("SELECT * FROM pre_contact WHERE 1 order by id desc");
	while ($res = $rs->fetch()) {
		$url_html = !empty($res["url"]) ? '<a href="' . htmlspecialchars($res["url"]) . '" target="_blank" rel="noreferrer" class="btn btn-default btn-xs">访问链接</a>' : '-';
		$qrcode_html = !empty($res["qrcode"]) ? '<img src="../' . htmlspecialchars($res["qrcode"]) . '" style="max-width:50px;max-height:50px;">' : '-';
		echo "<tr><td><b>" . $res["id"] . "</b></td><td><font color=blue>" . htmlspecialchars($res["type"]) . "</font></td><td>" . htmlspecialchars($res["account"]) . "</td><td>" . $url_html . "</td><td>" . $qrcode_html . "</td><td>" . htmlspecialchars($res["remark"]) . "</td><td>" . $res["addtime"] . "</td><td><a href=\"./contactlist.php?my=edit&id=" . $res["id"] . "\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<a href=\"./contactlist.php?my=delete&id=" . $res["id"] . "\" class=\"btn btn-xs btn-danger\" onclick=\"return confirm('你确实要删除此记录吗？');\">删除</a></td></tr>";
	}
	?>          </tbody>
        </table>
      </div>
<?php
}
?>    </div>
  </div>
</div>
