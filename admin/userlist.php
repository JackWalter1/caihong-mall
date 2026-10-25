<?php
/**
 * 用户管理
**/
include("../includes/common.php");
$title='用户管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
    <div class="col-md-12 center-block" style="float: none;">
<div class="modal" align="left" id="search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">搜索用户</h4>
      </div>
      <div class="modal-body">
<input type="text" class="form-control" name="kw" placeholder="请输入用户名或ID或QQ"><br/>
<button type="button" class="btn btn-primary btn-block" id="search_submit">搜索</button>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal" id="modal-rmb">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">余额充值</h4>
			</div>
			<div class="modal-body">
				<form id="form-rmb">
					<input type="hidden" name="zid" value="">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon p-0">
								<select name="do"
										style="-webkit-border-radius: 0;height:20px;border: 0;outline: none !important;border-radius: 5px 0 0 5px;padding: 0 5px 0 5px;">
									<option value="0">充值</option>
									<option value="1">扣除</option>
								</select>
							</span>
							<input type="number" class="form-control" name="rmb" placeholder="输入金额">
							<span class="input-group-addon">元</span>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-info" data-dismiss="modal">取消</button>
				<button type="button" class="btn btn-primary" id="recharge">确定</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="modal-banip">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">封禁IP地址</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="ban_ip_zid" value="">
				<div class="form-group">
					<label>封禁IP地址：</label>
					<input type="text" class="form-control" id="ban_ip_address" readonly>
				</div>
				<div class="form-group">
					<label>封禁时长：</label>
					<select class="form-control" id="ban_duration">
						<option value="1">1小时</option>
						<option value="6">6小时</option>
						<option value="12">12小时</option>
						<option value="24" selected>24小时</option>
						<option value="72">3天</option>
						<option value="168">7天</option>
						<option value="720">30天</option>
						<option value="0">永久封禁</option>
					</select>
				</div>
				<div class="form-group">
					<label>封禁原因：</label>
					<textarea class="form-control" id="ban_reason" rows="3" placeholder="请输入封禁原因（可选）"></textarea>
				</div>
				<div class="form-group">
					<label><input type="checkbox" id="ban_block_user" checked> 同时封禁该用户账号</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-info" data-dismiss="modal">取消</button>
				<button type="button" class="btn btn-danger" id="confirm_ban_ip">确认封禁</button>
			</div>
		</div>
	</div>
</div>

<?php

adminpermission('site', 1);

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='add')
{
echo '<div class="block">
<div class="block-title"><h3 class="panel-title">添加一个用户</h3></div>';
echo '<div class="">';
echo '<form action="./userlist.php?my=add_submit" method="POST">
<div class="form-group">
<label>用户名:</label><br>
<input type="text" class="form-control" name="user" value="" required>
</div>
<div class="form-group">
<label>密码:</label><br>
<input type="text" class="form-control" name="pwd" value="123456" required>
</div>
<div class="form-group">
<label>余额:</label><br>
<input type="text" class="form-control" name="rmb" value="0" required>
</div>
<div class="form-group">
<label>QQ:</label><br>
<input type="text" class="form-control" name="qq" value="">
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form>';
echo '<br/><a href="./userlist.php">>>返回用户列表</a>';
echo '</div></div>';
}
elseif($my=='edit')
{
$zid=$_GET['zid'];
$row=$DB->getRow("select * from pre_site where zid='$zid' limit 1");
echo '<div class="block">
<div class="block-title"><h3 class="panel-title">修改分站信息</h3></div>';
echo '<div class="">';
echo '<form action="./userlist.php?my=edit_submit&zid='.$zid.'" method="POST">
<div class="form-group">
<label>上级站点ID:</label><br>
<input type="text" class="form-control" name="upzid" value="'.$row['upzid'].'" disabled>
</div>
<div class="form-group">
<label>余额:</label><br>
<input type="text" class="form-control" name="rmb" value="'.$row['rmb'].'" required>
</div>
<div class="form-group">
<label>QQ:</label><br>
<input type="text" class="form-control" name="qq" value="'.$row['qq'].'">
</div>
<div class="form-group">
<label>重置密码:</label><br>
<input type="text" class="form-control" name="pwd" value="" placeholder="不重置请留空">
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>';
echo '<br/><a href="./userlist.php">>>返回用户列表</a>';
echo '<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script></div></div>';
}
elseif($my=='add_submit')
{
$user=trim($_POST['user']);
$pwd=trim($_POST['pwd']);
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
if($user==NULL or $pwd==NULL or $qq==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$rows=$DB->getRow("select user from pre_site where user='$user' limit 1");
if($rows)
	showmsg('用户名已存在！',3);
$sql="insert into `pre_site` (`power`,`user`,`pwd`,`rmb`,`qq`,`reg_ip`,`addtime`,`status`) values (0, :user, :pwd, :rmb, :qq, :reg_ip, :date, 1)";
$data = [':user'=>$user, ':pwd'=>$pwd, ':rmb'=>$rmb, ':qq'=>$qq, ':reg_ip'=>$clientip, ':date'=>$date];
if($DB->exec($sql, $data)!==false){
	showmsg('添加用户成功！<br/><br/><a href="./userlist.php">>>返回用户列表</a>',1);
}else
	showmsg('添加用户失败！'.$DB->error(),4);
}
}
elseif($my=='edit_submit')
{
$zid=intval($_GET['zid']);
$rows=$DB->getRow("select zid from pre_site where zid='$zid' limit 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
if(!empty($_POST['pwd']))$sql=",pwd='{$_POST['pwd']}'";
if($rmb==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
if($DB->exec("update pre_site set rmb='$rmb',qq='$qq'{$sql} where zid='{$zid}'")!==false)
	showmsg('修改用户成功！<br/><br/><a href="./userlist.php">>>返回用户列表</a>',1);
else
	showmsg('修改用户失败！'.$DB->error(),4);
}
}
elseif($my=='delete')
{
$zid=$_GET['zid'];
$sql="DELETE FROM pre_site WHERE zid='$zid'";
if($DB->exec($sql)!==false)
	showmsg('删除成功！<br/><br/><a href="./userlist.php">>>返回用户列表</a>',1);
else
	showmsg('删除失败！'.$DB->error(),4);
}
else
{

$numrows=$DB->getColumn("SELECT count(*) from pre_site WHERE power=0");

?>
<div class="block">
<div class="block-title clearfix">
<h2>系统共有 <b><?php echo $numrows?></b> 个用户</h2>
</div>
<a href="./userlist.php?my=add" class="btn btn-primary">添加用户</a>&nbsp;<a href="#" data-toggle="modal" data-target="#search" id="search" class="btn btn-success">搜索</a>&nbsp;<a href="javascript:;" class="btn btn-warning" id="genVirtualBtn"><i class="fa fa-users"></i> 生成虚拟用户</a>&nbsp;<a href="javascript:listTable('start')" class="btn btn-default" title="刷新用户列表"><i class="fa fa-refresh"></i></a>
<div id="listTable"></div>
    </div>
  </div>
</div>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
<script src="assets/js/userlist.js?ver=<?php echo VERSION ?>"></script>
<script>
function showBanIP(ip, zid) {
	$('#ban_ip_zid').val(zid);
	$('#ban_ip_address').val(ip);
	$('#ban_reason').val('');
	$('#ban_block_user').prop('checked', true);
	$('#modal-banip').modal('show');
}
	
	$('#genVirtualBtn').click(function() {
		layer.prompt({
			title: '输入要生成的虚拟用户数量',
			formType: 0,
			value: '10',
			area: ['300px', '200px']
		}, function(count, index) {
			count = parseInt(count);
			if(isNaN(count) || count < 1) {
				layer.msg('请输入有效的数字', {icon: 2});
				return;
			}
			if(count > 100) {
				layer.msg('单次最多生成100个', {icon: 2});
				return;
			}
			
			layer.close(index);
			var loading = layer.load(2, {shade: [0.1, '#fff']});
			
			$.ajax({
				type: 'POST',
				url: 'ajax_site.php?act=genVirtualUsers',
				data: {count: count},
				dataType: 'json',
				success: function(data) {
					layer.close(loading);
					if(data.code === 0) {
						layer.msg(data.msg, {icon: 1, time: 1500}, function() {
							listTable();
						});
					} else {
						layer.msg(data.msg || '生成失败', {icon: 2});
					}
				},
				error: function() {
					layer.close(loading);
					layer.msg('请求失败，请重试', {icon: 2});
				}
			});
		});
	});

$(document).ready(function() {
	$('#confirm_ban_ip').click(function() {
		var ip = $('#ban_ip_address').val();
		var zid = $('#ban_ip_zid').val();
		var duration = $('#ban_duration').val();
		var reason = $('#ban_reason').val();
		var block_user = $('#ban_block_user').prop('checked') ? 1 : 0;
		
		if(!ip) {
			layer.msg('IP地址不能为空', {icon: 2});
			return;
		}
		
		var duration_text = '';
		if(duration == 0) {
			duration_text = '永久';
		} else if(duration == 1) {
			duration_text = '1小时';
		} else if(duration == 24) {
			duration_text = '24小时';
		} else if(duration == 72) {
			duration_text = '3天';
		} else if(duration == 168) {
			duration_text = '7天';
		} else if(duration == 720) {
			duration_text = '30天';
		} else {
			duration_text = duration + '小时';
		}
		
		layer.confirm('确定要封禁IP地址 <b>' + ip + '</b> 吗？<br>封禁时长：' + duration_text + '<br>同时封禁用户：' + (block_user ? '是' : '否'), {
			btn: ['确认封禁', '取消'],
			icon: 3,
			title: '确认封禁'
		}, function(index) {
			layer.close(index);
			
			var loading = layer.load(2, {shade: [0.1, '#fff']});
			
			$.ajax({
				type: 'POST',
				url: 'ajax.php?act=ban_ip',
				data: {
					ip: ip,
					zid: zid,
					duration: duration,
					reason: reason,
					block_user: block_user
				},
				dataType: 'json',
				success: function(data) {
					layer.close(loading);
					if(data.code == 1) {
						layer.msg(data.msg, {icon: 1, time: 1500}, function() {
							$('#modal-banip').modal('hide');
							listTable('start');
						});
					} else {
						layer.msg(data.msg, {icon: 2});
					}
				},
				error: function() {
					layer.close(loading);
					layer.msg('请求失败，请重试', {icon: 2});
				}
			});
		});
	});
});
</script>
<?php }?>
</body>
</html>