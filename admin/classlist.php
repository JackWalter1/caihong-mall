<?php
/**
 * 分类管理
**/
include("../includes/common.php");
$title='分类管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$my=isset($_GET['my'])?$_GET['my']:null;
?>
<style>
#classlist tbody tr>td:nth-child(2){max-width:50px}
/* ========== 简易风格 ========== */
/* 面板：去掉圆角阴影，简单白底灰边 */
.block, .panel{border:1px solid #ddd!important;border-radius:0!important;box-shadow:none!important;overflow:hidden}
.block-title, .panel-heading{background:#f8f8f8!important;border-bottom:1px solid #ddd!important;padding:10px 15px!important}
.block-title h2, .panel-title{color:#333!important;font-weight:600!important;font-size:16px!important;margin:0!important}
.block-title h2 i, .panel-title i{color:#555!important}
.panel-footer{background:#f8f8f8!important;border-top:1px solid #ddd!important}
/* 表格：表头浅灰底，简单边框 */
.table-responsive{overflow-x:auto;border:1px solid #ddd}
.table thead>tr>th{background:#fafafa!important;color:#333!important;font-weight:600!important;border-bottom:2px solid #ddd!important;white-space:nowrap}
.table>tbody>tr:hover{background:#f5f5f5!important}
/* 按钮：Bootstrap 经典纯色 */
.btn{border-radius:2px!important}
.btn-primary{background:#337ab7!important;border-color:#2e6da4!important;color:#fff!important}
.btn-success{background:#5cb85c!important;border-color:#4cae4c!important;color:#fff!important}
.btn-info{background:#5bc0de!important;border-color:#46b8da!important;color:#fff!important}
.btn-warning{background:#f0ad4e!important;border-color:#eea236!important;color:#fff!important}
.btn-danger{background:#d9534f!important;border-color:#d43f3a!important;color:#fff!important}
.btn-default{background:#fff!important;border:1px solid #ccc!important;color:#333!important}
.btn:hover{filter:none!important}
/* 输入框/选择框 */
.form-control{border-radius:2px!important;border:1px solid #ccc!important;box-shadow:none!important}
.form-control:focus{border-color:#66afe9!important;box-shadow:none!important}
/* 标签 */
.label{border-radius:2px!important;font-weight:400!important}
/* 下拉菜单 */
.dropdown-menu{border-radius:0!important;box-shadow:0 2px 6px rgba(0,0,0,.15)!important;border:1px solid #ccc!important}
</style>
<script src="assets/js/jquery.dragsort-0.5.2.min.js"></script>
    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
<?php
adminpermission('shop', 1);

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='qk2')
{
$sql="TRUNCATE TABLE `pre_class`";
if($DB->exec($sql)!==false)
	exit("<script language='javascript'>alert('清空成功！');window.location.href='classlist.php';</script>");
else
	exit("<script language='javascript'>alert('清空失败！".$DB->error()."');history.go(-1);</script>");
}
elseif($my=='classimg'){
$numrows=$DB->getColumn("SELECT count(*) from pre_class");
?>
<div class="block">
	<div class="block-title">
		<h2>修改分类图片&nbsp;[<a href="./classlist.php">返回</a>]</h2>
	</div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>分类名称</th><th style="min-width:220px">图片URL</th></tr></thead>
          <tbody><form id="classlist">
<?php

$rs=$DB->query("SELECT * FROM pre_class WHERE 1 order by sort asc");
while($res = $rs->fetch())
{
	echo '<tr><td>'.$res['name'].'</td><td><div class="input-group"><input type="file" id="file'.$res['cid'].'" onchange="fileUpload('.$res['cid'].')" style="display:none;"/><input type="text" class="form-control input-sm" name="img['.$res['cid'].']" value="'.$res['shopimg'].'" placeholder="填写图片URL" required><span class="input-group-btn"><a href="javascript:fileSelect('.$res['cid'].')" class="btn btn-success btn-sm" title="上传图片"><i class="glyphicon glyphicon-upload"></i></a><a href="javascript:getImage('.$res['cid'].')" class="btn btn-info btn-sm" title="自动获取图片"><i class="glyphicon glyphicon-search"></i></a><a href="javascript:fileView('.$res['cid'].')" class="btn btn-warning btn-sm" title="查看图片"><i class="glyphicon glyphicon-picture"></i></a></span></div></td></tr>';
}
echo '</form><tr><td></td><td><span class="btn btn-primary btn-sm btn-block" onclick="saveAllImages()">保存全部</span></td></tr>';
?>
			</form>
          </tbody>
        </table>
      </div>
	  <div class="panel-footer">
	  <span class="glyphicon glyphicon-info-sign"></span>当前图片仅适用于部分首页模板
	  </div>
	</div>
<?php
}else{
?>
<div class="block">
	<div class="block-title">
	<div class="block-options pull-right"><a href="javascript:listTable()" class="btn btn-default" title="刷新分类列表"><i class="fa fa-refresh"></i></a></div>
		<h2>商品分类</h2>
	</div>
      <div id="listTable"></div>
	</div>
<?php }?>
  </div>
</div>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/classlist.js?ver=<?php echo VERSION?>"></script>
</body>
</html>