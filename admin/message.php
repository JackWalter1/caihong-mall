<?php

include "../includes/common.php";
$title = "站内通知";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
<?php 
adminpermission("message", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "add") {
	?><div class="block">
<div class="block-title"><h3 class="panel-title">发布新通知</h3></div>
<div class="">
  <form action="./message.php?my=add_submit" method="post" class="form-horizontal" role="form">
    <div class="form-group">
	  <label class="col-sm-2 control-label">通知标题</label>
	  <div class="col-sm-10"><input type="text" name="title" value="" class="form-control"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-2 control-label">接收用户类别</label>
	  <div class="col-sm-10"><select class="form-control" name="type"><option value="0">全部用户</option><option value="1">普通用户</option><option value="2">所有分站站长</option><option value="3">普及版站长</option><option value="4">专业版站长</option></select></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-2 control-label">通知内容</label>
	  <div class="col-sm-10"><textarea id="editor_id" class="form-control" name="content" rows="8" style="width:100%;"></textarea></div>
	</div><br/>
	<div class="form-group">
	  <div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="发布" class="btn btn-primary btn-block"/><br/>
	 </div>
	</div>
  </form>
  <br/><a href="./message.php">>>返回通知列表</a>
</div>
</div>
<script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('#editor_id', {
					resizeType : 1,
					allowUpload : false,
					allowPreviewEmoticons : false,
					allowImageUpload : false,
					items : [
						'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
						'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
						'insertunorderedlist', '|', 'emoticons', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
				});
        });
</script>
<?php 
} elseif ($my == "edit") {
	$id = $_GET["id"];
	$row = $DB->getRow("select * from pre_message where id='" . $id . "' limit 1");
	?><div class="block">
<div class="block-title"><h3 class="panel-title">修改通知</h3></div>
<div class="">
  <form action="./message.php?my=edit_submit&id=<?php echo $id;?>" method="post" class="form-horizontal" role="form">
    <div class="form-group">
	  <label class="col-sm-2 control-label">通知标题</label>
	  <div class="col-sm-10"><input type="text" name="title" value="<?php echo $row["title"];?>" class="form-control"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-2 control-label">接收用户类别</label>
	  <div class="col-sm-10"><select class="form-control" name="type" default="<?php echo $row["type"];?>"><option value="0">全部用户</option><option value="1">普通用户</option><option value="2">所有分站站长</option><option value="3">普及版站长</option><option value="4">专业版站长</option></select></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-2 control-label">通知内容</label>
	  <div class="col-sm-10"><textarea id="editor_id" class="form-control" name="content" rows="8" style="width:100%;"><?php echo htmlspecialchars($row["content"]);?></textarea></div>
	</div><br/>
	<div class="form-group">
	  <div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="发布" class="btn btn-primary btn-block"/><br/>
	 </div>
	</div>
  </form>
  <br/><a href="./message.php">>>返回通知列表</a>
</div>
</div>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>
<script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('#editor_id', {
					resizeType : 1,
					allowUpload : false,
					allowPreviewEmoticons : false,
					allowImageUpload : false,
					items : [
						'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
						'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
						'insertunorderedlist', '|', 'emoticons', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
				});
        });
</script>
<?php 
} elseif ($my == "add_submit") {
	$title = daddslashes($_POST["title"]);
	$type = intval($_POST["type"]);
	$content = daddslashes($_POST["content"]);
	if ($title == NULL || $content == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$rows = $DB->getRow("select * from pre_message where type='" . $type . "' and title='" . $title . "' limit 1");
		if ($rows) {
			showmsg("通知标题已存在！", 3);
		}
		$sql = "insert into `pre_message` (`type`,`title`,`content`,`addtime`,`active`) values ('" . $type . "','" . $title . "','" . $content . "','" . $date . "','1')";
		if ($DB->exec($sql) !== false) {
			showmsg("发布站内通知成功！<br/><br/><a href=\"./message.php\">>>返回通知列表</a>", 1);
		} else {
			showmsg("发布站内通知失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = $_GET["id"];
	$rows = $DB->getRow("select * from pre_message where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$title = daddslashes($_POST["title"]);
	$type = intval($_POST["type"]);
	$content = daddslashes($_POST["content"]);
	if ($title == NULL || $content == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if ($DB->exec("UPDATE `pre_message` SET `type`='" . $type . "',`title`='" . $title . "',`content`='" . $content . "' WHERE `id`='" . $id . "'") !== false) {
			showmsg("修改站内通知成功！<br/><br/><a href=\"./message.php\">>>返回通知列表</a>", 1);
		} else {
			showmsg("修改站内通知失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "delete") {
	$id = $_GET["id"];
	$sql = "DELETE FROM pre_message WHERE id='" . $id . "'";
	if ($DB->exec($sql) !== false) {
		showmsg("删除成功！<br/><br/><a href=\"./message.php\">>>返回通知列表</a>", 1);
	} else {
		showmsg("删除失败！" . $DB->error(), 4);
	}
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_message");
	?><div class="block">
<div class="block-title clearfix">
<h2>系统共有 <b><?php echo $numrows;?></b> 个站内通知</h2>
</div>
<a href="./message.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;发布新通知</a>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>通知标题</th><th>发布时间</th><th>已查阅人数</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php 
	$pagesize = 30;
	$pages = ceil($numrows / $pagesize);
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$offset = $pagesize * ($page - 1);
	$rs = $DB->query("SELECT * FROM pre_message WHERE 1 order by id desc limit " . $offset . "," . $pagesize);
	while ($res = $rs->fetch()) {
		echo "<tr><td><b>" . $res["id"] . "</b></td><td>" . $res["title"] . "</td><td>" . $res["addtime"] . "</td><td>" . $res["count"] . "</td><td>" . ($res["active"] == 1 ? "<span class=\"btn btn-xs btn-success\" onclick=\"setActive(" . $res["id"] . ",0)\">显示</span>" : "<span class=\"btn btn-xs btn-warning\" onclick=\"setActive(" . $res["id"] . ",1)\">隐藏</span>") . "</td><td><span class=\"btn btn-xs btn-success\" onclick=\"show(" . $res["id"] . ")\">查看</span>&nbsp;<a href=\"./message.php?my=edit&id=" . $res["id"] . "\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<a href=\"./message.php?my=delete&id=" . $res["id"] . "\" class=\"btn btn-xs btn-danger\" onclick=\"return confirm('你确实要删除此记录吗？');\">删除</a></td></tr>";
	}
	?>          </tbody>
        </table>
      </div>
<ul class="pagination"><?php 
	$first = 1;
	$prev = $page - 1;
	$next = $page + 1;
	$last = $pages;
	if ($page > 1) {
		echo "<li><a href=\"message.php?page=" . $first . $link . "\">首页</a></li>";
		echo "<li><a href=\"message.php?page=" . $prev . $link . "\">&laquo;</a></li>";
	} else {
		echo "<li class=\"disabled\"><a>首页</a></li>";
		echo "<li class=\"disabled\"><a>&laquo;</a></li>";
	}
	$start = $page - 10 > 1 ? $page - 10 : 1;
	$end = $page + 10 < $pages ? $page + 10 : $pages;
	for ($i = $start; $i < $page; $i++) {
		echo "<li><a href=\"message.php?page=" . $i . $link . "\">" . $i . "</a></li>";
	}
	echo "<li class=\"disabled\"><a>" . $page . "</a></li>";
	for ($i = $page + 1; $i <= $end; $i++) {
		echo "<li><a href=\"message.php?page=" . $i . $link . "\">" . $i . "</a></li>";
	}
	if ($page < $pages) {
		echo "<li><a href=\"message.php?page=" . $next . $link . "\">&raquo;</a></li>";
		echo "<li><a href=\"message.php?page=" . $last . $link . "\">尾页</a></li>";
	} else {
		echo "<li class=\"disabled\"><a>&raquo;</a></li>";
		echo "<li class=\"disabled\"><a>尾页</a></li>";
	}
	?></ul><?php 
}
?>    </div>
  </div>
</div>
<script src="<?php echo $cdnpublic;?>layer/2.3/layer.js"></script>
<script src="assets/js/message.js?ver=<?php echo VERSION;?>"></script>
</body>
</html>