<?php
/**
 * 分站管理
**/
include("../includes/common.php");

function addDomainColumnsIfNotExists() {
    global $DB;
    $columns = ['domain3', 'domain4', 'domain5', 'domain6'];
    
    try {
        $result = $DB->query("DESCRIBE `pre_site`");
        $existingColumns = [];
        while($row = $result->fetch()) {
            $existingColumns[] = $row['Field'];
        }
        
        foreach($columns as $col) {
            if(!in_array($col, $existingColumns)) {
                $DB->exec("ALTER TABLE `pre_site` ADD COLUMN `{$col}` VARCHAR(255) NULL DEFAULT NULL AFTER `domain2`");
            }
        }
    } catch(Exception $e) {
        // 忽略错误
    }
}
addDomainColumnsIfNotExists();

$title='用户/分站管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
    <div class="col-md-12 center-block" style="float: none;">
<div class="modal" align="left" id="search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">搜索分站</h4>
      </div>
      <div class="modal-body">
<input type="text" class="form-control" name="kw" placeholder="请输入分站ID或用户名或域名或站长QQ"><br/>
<button type="button" class="btn btn-primary btn-block" id="search_submit">搜索</button>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal" align="left" id="search2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">分类查看</h4>
      </div>
      <div class="modal-body">
<select name="power" class="form-control"><option value="1">普及版</option><option value="2">专业版</option></select><br/>
<button type="button" class="btn btn-primary btn-block" id="search2_submit">查看</button>
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
							<span class="input-group-btn">
								<select name="do" class="form-control" style="padding: 6px 4px;width:80px">
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

if($my=='replace')
{
echo '<div class="block">
<div class="block-title"><h3 class="panel-title">分站域名批量修改</h3></div>';
echo '<div class="">';
echo '<form action="./sitelist.php?my=replace_do" method="POST">
<div class="form-group">
	<div class="input-group"><div class="input-group-addon">原域名</div>
	<input type="text" name="olddomain" value="" class="form-control" placeholder="只填写主域名，例如：domain.com" required/>
</div></div>
<div class="form-group">
	<div class="input-group"><div class="input-group-addon">替换为</div>
	<input type="text" name="newdomain" value="" class="form-control" placeholder="只填写主域名，例如：domain.com" required/>
</div></div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>';
echo '<br/><a href="./sitelist.php">>>返回分站列表</a>';
echo '</div></div>';
}
elseif($my=='replace_do')
{
$olddomain=trim($_POST['olddomain']);
$newdomain=trim($_POST['newdomain']);
if($olddomain==NULL or $newdomain==NULL){
	showmsg('请确保每项都不为空！',3);
} elseif($olddomain==$newdomain){
	showmsg('原域名和新域名不能一样！',3);
} elseif(!preg_match('/^[a-zA-Z0-9\.\-\_]+$/',$newdomain) || strpos($newdomain,'.')===false){
	showmsg('新域名不合法',3);
} else {
$sql="update pre_site set domain = replace(domain,'".$olddomain."','".$newdomain."'), domain2 = replace(domain2,'".$olddomain."','".$newdomain."')";
if($DB->exec($sql)!==false){
	showmsg('批量修改域名成功！<br/><br/><a href="./sitelist.php">>>返回分站列表</a>',1);
}else
	showmsg('批量修改域名失败！'.$DB->error(),4);
}
}
elseif($my=='add')
{
echo '<div class="block">
<div class="block-title"><h3 class="panel-title">添加一个分站</h3></div>';
echo '<div class="">';
echo '<form action="./sitelist.php?my=add_submit" method="POST">
<div class="form-group">
<label>分站类型:</label><br>
<select class="form-control" name="power"><option value="1">普及版</option><option value="2">专业版</option></select>
</div>
<div class="form-group">
<label>管理员用户名:</label><br>
<input type="text" class="form-control" name="user" value="" required>
</div>
<div class="form-group">
<label>管理员密码:</label><br>
<input type="text" class="form-control" name="pwd" value="123456" required>
</div>
<div class="form-group">
<label>绑定域名:</label><br>
<input type="text" class="form-control" name="domain" value="" placeholder="分站要用的域名" required>
</div>
<!--div class="form-group">
<label>额外域名:</label><br>
<input type="text" class="form-control" name="domain2" placeholder="不需要填写" value="">
</div-->
<div class="form-group">
<label>站点余额:</label><br>
<input type="text" class="form-control" name="rmb" value="0" required>
</div>
<div class="form-group">
<label>站长QQ:</label><br>
<input type="text" class="form-control" name="qq" value="">
</div>
<div class="form-group">
<label>到期时间:</label><br>
<input type="date" class="form-control" name="endtime" value="'.date("Y-m-d",strtotime("+1 years")).'" required>
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form>';
echo '<br/><a href="./sitelist.php">>>返回分站列表</a>';
echo '</div></div>';
}
elseif($my=='add2')
{
$zid=$_GET['zid'];
$row=$DB->getRow("select * from pre_site where zid='$zid' limit 1");
echo '<div class="block">
<div class="block-title"><h3 class="panel-title">添加一个分站</h3></div>';
echo '<div class="">';
echo '<form action="./sitelist.php?my=add2_submit&zid='.$zid.'" method="POST">
<div class="form-group">
<label>分站类型:</label><br>
<select class="form-control" name="power"><option value="1">普及版</option><option value="2">专业版</option></select>
</div>
<div class="form-group">
<label>绑定域名:</label><br>
<input type="text" class="form-control" name="domain" value="" placeholder="分站要用的域名" required>
</div>
<!--div class="form-group">
<label>额外域名:</label><br>
<input type="text" class="form-control" name="domain2" placeholder="不需要填写" value="">
</div-->
<div class="form-group">
<label>到期时间:</label><br>
<input type="date" class="form-control" name="endtime" value="'.date("Y-m-d",strtotime("+1 years")).'" required>
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form>';
echo '<br/><a href="./sitelist.php">>>返回分站列表</a>';
echo '</div></div>';
}
elseif($my=='edit')
{
$zid=$_GET['zid'];
$row=$DB->getRow("select * from pre_site where zid='$zid' limit 1");
echo '<div class="block">
<div class="block-title"><h3 class="panel-title">修改分站信息</h3><span class="pull-right"><a href="./siteprice.php?zid='.$zid.'" class="btn btn-default">自定义密价</a></div>';
echo '<div class="">';
echo '<form action="./sitelist.php?my=edit_submit&zid='.$zid.'" method="POST">
<div class="form-group">
<label>分站类型:</label><br>
<select class="form-control" name="power" default="'.$row['power'].'"><option value="1">普及版</option><option value="2">专业版</option></select>
</div>'.
($row['power']==1?'<div class="form-group">
<label>上级站点ID:</label><br>
<input type="text" class="form-control" name="upzid" value="'.$row['upzid'].'" disabled>
</div>':null)
.'<div class="form-group">
<label>绑定域名:</label><br>
<input type="text" class="form-control" name="domain" value="'.$row['domain'].'" required>
</div>
<div class="form-group">
<label>额外域名1:</label><br>
<input type="text" class="form-control" name="domain2" value="'.$row['domain2'].'">
</div>
<div class="form-group">
<label>额外域名2:</label><br>
<input type="text" class="form-control" name="domain3" value="'.$row['domain3'].'">
</div>
<div class="form-group">
<label>额外域名3:</label><br>
<input type="text" class="form-control" name="domain4" value="'.$row['domain4'].'">
</div>
<div class="form-group">
<label>额外域名4:</label><br>
<input type="text" class="form-control" name="domain5" value="'.$row['domain5'].'">
</div>
<div class="form-group">
<label>额外域名5:</label><br>
<input type="text" class="form-control" name="domain6" value="'.$row['domain6'].'">
</div>
<div class="form-group">
<label>站点总余额:</label><br>
<input type="text" class="form-control" name="rmb" value="'.$row['rmb'].'" required>
</div>'.($conf['tixian_limit']?'<div class="form-group">
<label>其中可提现余额:</label><br>
<input type="text" class="form-control" value="'.($row['rmbtc']>$row['rmb']?$row['rmb']:$row['rmbtc']).'" disabled>
</div>':null).'
<div class="form-group">
<label>站长QQ:</label><br>
<input type="text" class="form-control" name="qq" value="'.$row['qq'].'">
</div>
<div class="form-group">
<label>站点名称:</label><br>
<input type="text" class="form-control" name="sitename" value="'.$row['sitename'].'">
</div>
<div class="form-group">
<label>结算账号:</label><br>
<input type="text" class="form-control" name="pay_account" value="'.$row['pay_account'].'">
</div>
<div class="form-group">
<label>结算姓名:</label><br>
<input type="text" class="form-control" name="pay_name" value="'.$row['pay_name'].'">
</div>
<div class="form-group">
<label>到期时间:</label><br>
<input type="date" class="form-control" name="endtime" value="'.date("Y-m-d",strtotime($row['endtime'])).'" required>
</div>
<div class="form-group">
<label>重置密码:</label><br>
<input type="text" class="form-control" name="pwd" value="" placeholder="不重置请留空">
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>';
echo '<br/><a href="./sitelist.php">>>返回分站列表</a>';
echo '<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script></div></div>';
}
elseif($my=='add_submit')
{
$power=intval($_POST['power']);
$user=trim($_POST['user']);
$pwd=trim($_POST['pwd']);
$domain=trim(strtolower($_POST['domain']));
$domain2=trim(strtolower($_POST['domain2']));
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
$endtime=$_POST['endtime'];
$sitename=$conf['sitename'];
$keywords=$conf['keywords'];
$description=$conf['description'];
if($user==NULL or $pwd==NULL or $domain==NULL or $endtime==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$rows=$DB->getRow("select user from pre_site where user='$user' limit 1");
if($rows)
	showmsg('用户名已存在！',3);
$rows=$DB->getRow("select * from pre_site where domain='$domain' limit 1");
if($rows || $domain==$_SERVER['HTTP_HOST'] || in_array($domain,explode('|',$conf['fenzhan_remain'])))
	showmsg('域名已存在！',3);
if($conf['fenzhan_html']==1){
	$anounce=$conf['anounce'];
	$alert=$conf['alert'];
}
$sql="insert into `pre_site` (`power`,`domain`,`domain2`,`user`,`pwd`,`rmb`,`qq`,`sitename`,`keywords`,`description`,`anounce`,`alert`,`addtime`,`endtime`,`status`) values ('".$power."','".$domain."','".$domain2."','".$user."','".$pwd."','".$rmb."','".$qq."','".$sitename."','".$keywords."','".$description."','".addslashes($anounce)."','".addslashes($alert)."','".$date."','".$endtime."','1')";
if($DB->exec($sql)!==false){
	showmsg('添加分站成功！<br/><br/><a href="./sitelist.php">>>返回分站列表</a>',1);
}else
	showmsg('添加分站失败！'.$DB->error(),4);
}
}
elseif($my=='add2_submit')
{
$zid=intval($_GET['zid']);
$rows=$DB->getRow("select zid from pre_site where zid='$zid' limit 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$power=intval($_POST['power']);
$domain=trim(strtolower($_POST['domain']));
$domain2=trim(strtolower($_POST['domain2']));
$endtime=$_POST['endtime'];
$sitename=$conf['sitename'];
$keywords=$conf['keywords'];
$description=$conf['description'];
if($domain==NULL || $endtime==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$rows=$DB->getRow("select zid from pre_site where domain='$domain' limit 1");
if($rows || $domain==$_SERVER['HTTP_HOST'] || in_array($domain,explode('|',$conf['fenzhan_remain'])))
	showmsg('域名已存在！',3);
if($DB->exec("update pre_site set power='$power',domain='$domain',domain2='$domain2',qq='$qq',sitename='$sitename',keywords='$keywords',description='$description',endtime='$endtime' where zid='{$zid}'")!==false)
	showmsg('添加分站成功！<br/><br/><a href="./userlist.php">>>返回用户列表</a><br/><a href="./sitelist.php">>>返回分站列表</a>',1);
else
	showmsg('添加分站失败！'.$DB->error(),4);
}
}
elseif($my=='edit_submit')
{
$zid=intval($_GET['zid']);
$rows=$DB->getRow("select zid,rmb,rmbtc from pre_site where zid='$zid' limit 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$power=intval($_POST['power']);
$domain=trim(strtolower($_POST['domain']));
$domain2=trim(strtolower($_POST['domain2']));
$domain3=trim(strtolower($_POST['domain3']));
$domain4=trim(strtolower($_POST['domain4']));
$domain5=trim(strtolower($_POST['domain5']));
$domain6=trim(strtolower($_POST['domain6']));
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
$endtime=$_POST['endtime'];
$sitename=$_POST['sitename'];
$pay_account=$_POST['pay_account'];
$pay_name=$_POST['pay_name'];
if(!empty($_POST['pwd']))$sql=",pwd='{$_POST['pwd']}'";
if($sitename==NULL or $rmb==NULL or $domain==NULL or $endtime==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
if($rmb<$rows['rmbtc'])$rmbtc=$rmb;
else $rmbtc=$rows['rmbtc'];
if($DB->exec("update pre_site set power='$power',domain='$domain',domain2='$domain2',domain3='$domain3',domain4='$domain4',domain5='$domain5',domain6='$domain6',rmb='$rmb',rmbtc='$rmbtc',qq='$qq',sitename='$sitename',pay_account='$pay_account',pay_name='$pay_name',endtime='$endtime'{$sql} where zid='{$zid}'")!==false)
	showmsg('修改分站成功！<br/><br/><a href="./sitelist.php">>>返回分站列表</a>',1);
else
	showmsg('修改分站失败！'.$DB->error(),4);
}
}
elseif($my=='delete')
{
$zid=$_GET['zid'];
$sql="DELETE FROM pre_site WHERE zid='$zid'";
if($DB->exec($sql)!==false)
	showmsg('删除成功！<br/><br/><a href="./sitelist.php">>>返回分站列表</a>',1);
else
	showmsg('删除失败！'.$DB->error(),4);
}
elseif($my=='anounce')
{
if($DB->exec("update pre_site set anounce=NULL")!==false)
	showmsg('清空成功！<br/><br/><a href="./sitelist.php">>>返回分站列表</a>',1);
else
	showmsg('清空失败！'.$DB->error(),4);
}
elseif($my=='price')
{
if($DB->exec("update pre_site set price=NULL")!==false)
	showmsg('清空成功！<br/><br/><a href="./sitelist.php">>>返回分站列表</a>',1);
else
	showmsg('清空失败！'.$DB->error(),4);
}
else
{

$numrows=$DB->getColumn("SELECT count(*) FROM pre_site WHERE power>0");

?>
<div class="block">
<div class="block-title clearfix">
<h2>系统共有 <b><?php echo $numrows?></b> 个分站</h2>
</div>
<a href="./sitelist.php?my=add" class="btn btn-primary">添加分站</a>&nbsp;<a href="#" data-toggle="modal" data-target="#search" id="search" class="btn btn-success">搜索</a>&nbsp;<a href="#" data-toggle="modal" data-target="#search2" id="search2" class="btn btn-warning">分类查看</a>&nbsp;<div class="btn-group">
  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    更多 <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <li><a href="./sitelist.php?my=replace">分站域名批量修改</a></li>
  <li><a href="./sitelist.php?my=anounce" onclick="return confirm('你确实要清空所有分站公告代码吗？清空后默认都显示主站的公告内容');">一键清空所有分站公告代码</a></li>
	<li><a href="./sitelist.php?my=price" onclick="return confirm('你确实要清空所有分站所有分站商品价格设置？清空后默认都恢复到主站设置的商品价格');">一键清空所有分站商品价格设置</a></li>
  </ul>
</div>&nbsp;
<label class="form-inline" for="tabSort" style="font-weight: normal;">按余额&nbsp;<select class="form-control" id="tabSort" style="font-weight: normal;"><option value="">请选择</option><option value="0">正序</option><option value="1">倒序</option></select>
</label>&nbsp;<a href="javascript:listTable('start')" class="btn btn-default" title="刷新分站列表"><i class="fa fa-refresh"></i></a>
<div id="listTable"></div>
    </div>
  </div>
</div>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/sitelist.js?ver=<?php echo VERSION ?>"></script>
<?php }?>
</body>
</html>