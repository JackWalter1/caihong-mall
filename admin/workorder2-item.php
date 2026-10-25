<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>投诉详情</title>
  <link href="//lib.baomitu.com/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="//lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <script src="//lib.baomitu.com/modernizr/2.8.3/modernizr.min.js"></script>
  <script src="//lib.baomitu.com/jquery/2.1.4/jquery.min.js"></script>
  <script src="//lib.baomitu.com/layer/3.1.1/layer.js"></script>
  <!--[if lt IE 9]>
    <script src="//lib.baomitu.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//lib.baomitu.com/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
 <style>
.gdan_gout{width:100%;height:auto;background-color:#fff;padding-bottom:1em}
.gdan_txt{height:3em;line-height:3em;text-indent:1em;font-family:"微软雅黑";font-weight:800;}
.gdan_txt>span{position:absolute;right:4em;}
.gdan_zhugan{width:96%;height:auto;padding-top:1em;margin-left:2%;padding-left:.5em;padding-right:1em;margin-bottom:1em;border-top:dashed 1px #a9a9a9}
.gdan_kjia1{width:auto;margin-left:4em;margin-top:-3em}
.gdan_xiaozhi{width:100%;height:1em;color:#a9a9a9;margin-bottom:1em}
.gdan_xiaozhi>span{position:absolute;right:4em;}
.gdan_huifu{width:100%;height:auto;margin-top:1em;border-top:solid #ccc 1px}
.gdan_srk{width:98%;height:8em;margin-left:1%;margin-top:1em;border-color:#6495ed}
.gdan_huifu1{width:6em;height:2.5em;border:none;background-color:#1e90ff;color:#fff;margin:.5em 0 .5em 1%}
.gdan_jied{width:100%;height:3em;line-height:3em;text-align:center;color:#129DDE}
.speedy-text{max-width: 180px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;display: inline-block;color: #fff!important;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select:none;}
</style>

</head>
<body>
<div class="row">
    <div class="col-sm-12 col-md-10 center-block" style="float: none;">

<?php 
include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("workorder", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "view") {
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("SELECT * FROM pre_workorder WHERE id=:id LIMIT 1", array(":id" => $id));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$contents = explode("*", $rows["content"]);
	$siterow = $DB->getRow("SELECT zid,qq,user FROM pre_site WHERE zid=:zid LIMIT 1", array(":zid" => $rows["zid"]));
	$myimg = $userrow["qq"] ? "//q2.qlogo.cn/headimg_dl?bs=qq&dst_uin=" . $siterow["qq"] . "&src_uin=" . $siterow["qq"] . "&fid=" . $siterow["qq"] . "&spec=100&url_enc=0&referer=bu_interface&term_type=PC" : "../assets/img/user.png";
	$kfimg = "https://imgcache.qq.com/open_proj/proj_qcloud_v2/mc_2014/work-order/css/img/custom-service-avatar.svg";
	?><div class="panel panel-default">
<div class="panel-heading"><h3 class="panel-title"><i class="fa fa-sticky-note-o"></i>&nbsp;&nbsp;<b>投诉详情</b></h3></div>

<div class="gdan_gout">
	<div class="gdan_txt">沟通记录 - <?php echo count($contents);?><span>状态：<?php echo display_status($rows["status"]);?></span></div>
	<!------------------开始沟通------------------------>
	<div class="gdan_zhugan" style="border: none;">
		<a href="./sitelist.php?zid=<?php echo $rows["zid"];?>" target="_blank"><img src="<?php echo $myimg;?>" class="img-circle" width="40"/></a>
		<div class="gdan_kjia1">
			<div class="gdan_xiaozhi">问题描述<span><?php echo $rows["addtime"];?></span></div>
			<?php echo str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars($contents[0], ENT_QUOTES | ENT_HTML5, 'UTF-8'));?><br/><br/>
			用户信息：<?php echo $siterow ? "<a href=\"./sitelist.php?zid=" . $rows["zid"] . " target=\"_blank\">" . htmlspecialchars($siterow["user"], ENT_QUOTES | ENT_HTML5, 'UTF-8') . "</a>" : htmlspecialchars($rows["account"], ENT_QUOTES | ENT_HTML5, 'UTF-8');?><br/>
			订单编号：<?php echo $rows["orderid"] ? "<a href=\"./list.php?id=" . $rows["orderid"] . " target=\"_blank\">" . htmlspecialchars($rows["orderid"], ENT_QUOTES | ENT_HTML5, 'UTF-8') . "</a>" : "无订单号";?><br/>
			问题类型：<?php echo display_type($rows["type"]);?>			<?php echo $rows["picurl"] ? "<p>问题图片：[<a href=\"../" . htmlspecialchars($rows["picurl"]) . "\" target=\"_blank\">点此查看</a>]" : null;?>		</div>
	</div>
<?php 
	for ($i = 1; $i < count($contents); $i++) {
		$content = explode("^", $contents[$i]);
		if (count($content) == 3) {
			echo "<div class=\"gdan_zhugan\">\r\n\t<img src=\"" . ($content[0] == 1 ? $kfimg : $myimg) . "\" class=\"img-circle\" width=\"40\"/>\r\n\t<div class=\"gdan_kjia1\">\r\n\t<div class=\"gdan_xiaozhi\">" . htmlspecialchars(($content[0] == 1 ? "官方客服" : $userrow["user"]), ENT_QUOTES | ENT_HTML5, 'UTF-8') . "<span>" . htmlspecialchars($content[1], ENT_QUOTES | ENT_HTML5, 'UTF-8') . "</span></div>\r\n\t" . str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars($content[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . "\r\n\t</div>\r\n</div>";
		}
	}
	if ($rows["status"] == 2) {
		?><div class="gdan_jied">此投诉已经结单</div>
<?php 
	} else {
		?><div class="gdan_huifu">
<form action="./workorder-item.php?my=reply&id=<?php echo $id;?>" method="POST">
	<textarea class="gdan_srk" name="content" placeholder="回复后投诉状态自动变为已处理 ,分站站点将会收到通知哦！" required></textarea><br/>
	<input type="checkbox" name="email" id="email" value="1" style="margin-left: 1%;"><label for="email">同时发送提醒邮件到用户邮箱</label><br/>
	<input type="submit" name="submit" value="提交回复" class="gdan_huifu1" />
	<input type="button" name="submit" value="完结投诉" class="gdan_huifu1" style="background-color: mediumseagreen;" onclick="window.location.href='./workorder-item.php?my=complete&id=<?php echo $id;?>'"/>
	<button type="button" class="gdan_huifu1" id="addSpeedy" style="width: 8em;background-color: #FFB800;">保存到快捷回复</button>
	<div style="margin: 1%;">
		<span>快捷回复列表</span>
		<button type="button" class="btn btn-xs btn-danger pull-right" data-type="0" id="delSpeedy" title="删除快捷回复列表内容">删除</button>
	</div>
	<div class="form-group" id="speedyList" data-ids="" style="margin: 1%;padding: 5px;border: 1px solid #dedede;min-height: 50px;">
		<?php 
		$speedy_list = explode("^", $conf["speedy_list"]);
		foreach ($speedy_list as $ikey => $icontent) {
			?>		<span class="speedy-text">
			<span class="label label-info" data-id="<?php echo $ikey;?>" title="<?php echo htmlspecialchars($icontent);?>"><?php echo htmlspecialchars($icontent);?></span>
		</span>
		<?php 
		}
		?>	</div>
</form>
</div>
<script>
// 新增快捷回复
$('#addSpeedy').on('click', function () {
	let text = $('.gdan_srk[name=content]').val();
	text = text.trim();
	if (text.length <= 0) {
		layer.msg('请填写回复内容', {icon: 7});
		return false;
	}
	layer.load(2);
	$.ajax({
		url: 'ajax.php?act=add_speedy_text',
		type: 'POST',
		data: {content: text},
		dataType: 'json',
		success: function (res) {
			layer.closeAll('loading');
			if (res.code == 0) {
				let html = '<span class="speedy-text"><span class="label label-info" data-id="'+res.id+'" title="'+res.content+'">'+res.content+'</span></span>';
				$('#speedyList').append(html);
				speedyTextClick();
			} else {
				layer.msg(res['msg'], {icon: 7});
			}
		},
		error: function (data) {
			layer.closeAll('loading');
			layer.msg('操作异常', {icon: 7});
		}
	});
});

const speedyListDom = $('#speedyList');

$(document).ready(function () {
	speedyTextClick();
});

function speedyTextClick() {
	$('.speedy-text').on('click', function () {
		const type = $('#delSpeedy').attr('data-type');
		if (type === '0') { // 默认
			$('.gdan_srk[name=content]').val($(this).children('span').text());
		} else { // 删除
			let ids = speedyListDom.attr('data-ids');
			ids = ids.length > 0 ? ids.split(',') : [];
			ids.push($(this).children('span').attr('data-id'));
			speedyListDom.attr('data-ids', ids.join(','));
			$(this).remove();
		}
	});
}

$('#delSpeedy').on('click', function () {
	const type = $(this).attr('data-type');
	if (type === '0') {
		layer.tips('点击文本即可删除', '#speedyList', {tips: [1, '#FF5722']});
		$(this).text('完成').attr('data-type', '1').removeClass('btn-danger').addClass('btn-success');
		$('.label-info').css({'background-color': '#FF5722'});
	} else {
		$(this).text('删除').attr('data-type', '0').removeClass('btn-success').addClass('btn-danger');
		$('.label-info').css({'background-color': '#01AAED'});
		const ids = speedyListDom.attr('data-ids');
		if (ids.length <= 0) return false;
		layer.load(2);
		$.ajax({
			url: 'ajax.php?act=del_speedy_text',
			type: 'POST',
			data: {ids: ids},
			dataType: 'json',
			success: function (res) {
				layer.closeAll('loading');
				if (res.code == 0) {
					layer.msg(res.msg, {icon: 1});
				} else {
					layer.msg(res.msg, {icon: 7});
				}
			},
			error: function (data) {
				layer.closeAll('loading');
				layer.msg('操作异常', {icon: 7});
			}
		});
	}
});
</script>
<?php 
	}
	?></div>
</div>
<?php 
} elseif ($my == "reply") {
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("SELECT * FROM pre_workorder WHERE id=:id LIMIT 1", array(":id" => $id));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	} elseif ($rows["status"] == 2) {
		showmsg("此投诉已经结单", 3);
	}
	$content = str_replace(array("*", "^", "|"), "", trim(strip_tags(daddslashes($_POST["content"]))));
	if (empty($content)) {
		showmsg("补充信息不能为空！");
	} else {
		$content = $rows["content"] . "*1^" . $date . "^" . $content;
		if ($DB->exec("UPDATE pre_workorder SET content=:content,status=1 WHERE id=:id", array(":content" => $content, ":id" => $id)) !== false) {
			if ($_POST["email"] == 1) {
				send_msg_mail($id);
			}
			exit("<script language='javascript'>alert('回复投诉成功！');history.go(-1);</script>");
		} else {
			showmsg("回复投诉失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "complete") {
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("SELECT * FROM pre_workorder WHERE id=:id LIMIT 1", array(":id" => $id));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	} elseif ($rows["status"] == 2) {
		showmsg("此投诉已经结单", 3);
	}
	if ($DB->exec("UPDATE pre_workorder SET status=2 WHERE id=:id", array(":id" => $id)) !== false) {
		exit("<script language='javascript'>alert('完结投诉成功！');history.go(-1);</script>");
	} else {
		showmsg("完结投诉失败！" . $DB->error(), 4);
	}
}
function send_msg_mail($id)
{
	global $DB, $conf;
	$_var_0 = $DB->getRow("select * from pre_workorder where id=:id limit 1", array(':id' => $id));
	$_var_1 = $DB->getRow("select zid,user,qq from pre_site where zid=:zid limit 1", array(':zid' => $_var_0["zid"]));
	$_var_2 = $_var_1["qq"] . "@qq.com";
	$_var_3 = $conf["sitename"] . "售后支持投诉待反馈提醒";
	$_var_4 = explode("*", $_var_0["content"]);
	$_var_4 = mb_substr($_var_4[0], 0, 16, "utf-8");
	$_var_5 = str_replace("\\", "/", $_SERVER["SCRIPT_NAME"]);
	$_var_5 = substr($_var_5, 0, strrpos($_var_5, "/"));
	$_var_5 = substr($_var_5, 0, strrpos($_var_5, "/"));
	$_var_6 = (is_https() ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . $_var_5 . "/";
	$_var_7 = "尊敬的" . $_var_1["user"] . "：<br/>您于" . $_var_0["addtime"] . "提交的售后支持投诉(ID:" . $id . ") 需要您进一步提供相关信息。请登录网站后台“我的投诉”查看详情并回复。若3天内您仍未回复此投诉，我们会做完成投诉处理。<a href=\"" . $_var_6 . "user/workorder.php?my=view&id=" . $id . "\" target=\"_blank\">点此查看</a><br/><a href=\"" . $_var_6 . "user/workorder.php?my=view&id=" . $id . "\" target=\"_blank\">投诉标题：" . $_var_4 . "</a><br/>----------------<br/>" . $conf["sitename"];
	if (checkEmail($_var_2)) {
		send_mail($_var_2, $_var_3, $_var_7);
	}
}
function display_type($type)
{
	global $conf;
	$_var_8 = explode("|", $conf["workorder_type"]);
	if ($type == 0 || !array_key_exists($type - 1, $_var_8)) {
		return "其它问题";
	} else {
		return $_var_8[$type - 1];
	}
}
function display_status($status)
{
	if ($status == 1) {
		return "<font color=\"red\">处理中</font>";
	} elseif ($status == 2) {
		return "<font color=\"green\">已完成</font>";
	} else {
		return "<font color=\"blue\">待处理</font>";
	}
}