<?php
/**
 * 供货商管理
**/
include("../includes/common.php");
$title='供货商管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='../sup/login.php';</script>");
?>
    <div class="col-md-12 center-block" style="float: none;">
<div class="modal" align="left" id="search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">搜索供货商</h4>
      </div>
      <div class="modal-body">
<input type="text" class="form-control" name="kw" placeholder="请输入供货商名或QQ或ID"><br/>
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
					<input type="hidden" name="sid" value="">
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

<?php

adminpermission('site', 1);

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='add')
{
echo '<div class="block">
<div class="block-title"><h3 class="panel-title">添加一个供货商</h3></div>';
echo '<div class="">';
echo '<form action="./suplist.php?my=add_submit" method="POST">
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
echo '<br/><a href="./suplist.php">>>返回供货商列表</a>';
echo '</div></div>';
}
elseif($my=='edit')
{
$sid=$_GET['sid'];
$rows=$DB->getRow("select * from pre_supplier where sid='$sid' limit 1");

if(!$rows)showmsg('当前记录不存在！',3);
echo '<div class="block">
<div class="block-title"><h3 class="panel-title">修改供货商信息</h3></div>';
echo '<div class="">';
echo '<form action="./suplist.php?my=edit_submit&sid='.$sid.'" method="POST">
<div class="form-group">
<label>余额:</label><br>
<input type="text" class="form-control" name="rmb" value="'.$rows['rmb'].'" required>
</div>
<div class="form-group">
<label>QQ:</label><br>
<input type="text" class="form-control" name="qq" value="'.$rows['qq'].'">
</div>
<div class="form-group">
<label>重置密码:</label><br>
<input type="text" class="form-control" name="pwd" value="" placeholder="不重置请留空">
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>';
echo '<br/><a href="./suplist.php">>>返回供货商列表</a>';
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
$rows=$DB->getRow("select user from pre_supplier where user='$user' limit 1");
if($rows)
	showmsg('用户名已存在！',3);
$sql="insert into `pre_supplier` (`user`,`pwd`,`rmb`,`qq`,`addtime`,`status`) values (:user, :pwd, :rmb, :qq, :date, 1)";
$data = [':user'=>$user, ':pwd'=>$pwd, ':rmb'=>$rmb, ':qq'=>$qq, ':date'=>$date];
if($DB->exec($sql, $data)!==false){
	showmsg('添加供货商成功！<br/><br/><a href="./suplist.php">>>返回供货商列表</a>',1);
}else
	showmsg('添加供货商失败！'.$DB->error(),4);
}
}
elseif($my=='edit_submit')
{
$sid=intval($_GET['sid']);
$rows=$DB->getRow("select sid from pre_supplier where sid='$sid' limit 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
if(!empty($_POST['pwd']))$sql=",pwd='{$_POST['pwd']}'";
if($rmb==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
if($DB->exec("update pre_supplier set rmb='$rmb',qq='$qq'{$sql} where sid='{$sid}'")!==false)
	showmsg('修改供货商成功！<br/><br/><a href="./suplist.php">>>返回供货商列表</a>',1);
else
	showmsg('修改供货商失败！'.$DB->error(),4);
}
}
elseif($my=='delete')
{
$sid=$_GET['sid'];
$sql="DELETE FROM pre_supplier WHERE sid='$sid'";
if($DB->exec($sql)!==false)
	showmsg('删除成功！<br/><br/><a href="./suplist.php">>>返回供货商列表</a>',1);
else
	showmsg('删除失败！'.$DB->error(),4);
}
else
{

$numrows=$DB->getColumn("SELECT count(*) from pre_supplier WHERE 1");

?>
<div class="block">
<div class="block-title clearfix">
<h2>系统共有 <b><?php echo $numrows?></b> 个供货商</h2>
</div>
<a href="./suplist.php?my=add" class="btn btn-primary">添加供货商</a>&nbsp;<a href="#" data-toggle="modal" data-target="#search" id="search" class="btn btn-success">搜索</a>&nbsp;<a href="javascript:listTable('start')" class="btn btn-default" title="刷新供货商列表"><i class="fa fa-refresh"></i></a>
<div id="listTable"></div>
    </div>
  </div>
</div>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
<script src="assets/js/suplist.js?ver=<?php echo VERSION ?>"></script>
<?php }?>
</body>
</html>