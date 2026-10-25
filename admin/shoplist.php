<?php
/**
 * 商品管理
**/
include("../includes/common.php");
$title='商品管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");


?>
<style>
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
.btn-xs, .btn-sm{border-radius:2px!important}
/* 输入框/选择框 */
.form-control{border-radius:2px!important;border:1px solid #ccc!important;box-shadow:none!important}
.form-control:focus{border-color:#66afe9!important;box-shadow:none!important}
/* 标签 */
.label{border-radius:2px!important;font-weight:400!important}
/* 下拉菜单 */
.dropdown-menu{border-radius:0!important;box-shadow:0 2px 6px rgba(0,0,0,.15)!important;border:1px solid #ccc!important}
/* 工具栏 */
.block-title .form-inline .btn, .block-title .form-inline .form-group{margin-right:6px}
.block-title .form-inline .form-group .form-control{display:inline-block;width:auto}
</style>
    <div class="col-md-12 center-block" style="float: none;">
<?php
adminpermission('shop', 1);

$rs=$DB->query("SELECT * FROM pre_price order by id asc");
$priceselect='<option value="0">不使用加价模板</option>';
$price_class[0]='不加价';
while($res = $rs->fetch()){
	$kind = $res['kind']==1?'元':'倍';
	$priceselect.='<option value="'.$res['id'].'" kind="'.$res['kind'].'" p_2="'.$res['p_2'].'" p_1="'.$res['p_1'].'" p_0="'.$res['p_0'].'" >'.$res['name'].'('.$res['p_2'].$kind.'|'.$res['p_1'].$kind.'|'.$res['p_0'].$kind.')</option>';
	$price_class[$res['id']]=$res['name'];
}
$_SESSION['priceselect']=$priceselect;
$_SESSION['price_class']=$price_class;

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='qk2')
{
$sql="TRUNCATE TABLE `pre_tools`";
if($DB->exec($sql)!==false)
	exit("<script language='javascript'>alert('清空成功！');window.location.href='shoplist.php';</script>");
else
	exit("<script language='javascript'>alert('清空失败！".$DB->error()."');history.go(-1);</script>");
}
else
{

$cid = isset($_GET['cid'])?intval($_GET['cid']):0;
?>
<div class="block">
<div class="block-title clearfix">
<h2 id="blocktitle"></h2>
<span class="pull-right">
<?php if(isset($_GET['cid']))echo '<span style="color:red;font-size:13px;margin-right:10px;">提示：如果出现排序操作移动不了，请点击"重置商品排序"按钮</span>';?>
<select id="pagesize" class="form-control" title="每页显示"><option value="30">30</option><option value="50">50</option><option value="60">60</option><option value="80">80</option><option value="100">100</option></select><span>
</span></span>
</div>
  <form onsubmit="return searchItem()" method="GET" class="form-inline">
  
  <!-- 电脑端布局：所有按钮放在同一行 -->
  <div class="hidden-xs">
    <a href="./shopedit.php?my=add&cid=<?php echo htmlspecialchars($_GET['cid'])?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加商品</a>&nbsp;
    <div class="form-group">
      <input type="text" class="form-control" name="kw" placeholder="请输入商品名称">
    </div>
    <div class="form-group">
      <select class="form-control" name="type">
        <option value="0">商品类型</option>
        <option value="1">对接</option>
        <option value="4">发卡</option>
        <option value="other">自营</option>
      </select>
    </div>
    <div class="form-group">
      <select class="form-control" name="status">
        <option value="0">商品状态</option>
        <option value="1">显示</option>
        <option value="0">隐藏</option>
        <option value="up">上架</option>
        <option value="down">下架</option>
      </select>
    </div>
    <button type="submit" class="btn btn-success">搜索</button>&nbsp;
    <!-- 电脑端：功能按钮放在搜索的右边 -->
    <a href="javascript:reset_sort(<?php echo $cid?>)" class="btn btn-info"><i class="fa fa-sort"></i>&nbsp;重置商品排序</a>&nbsp;
    <a href="./shoprank.php" class="btn btn-info"><i class="fa fa-bar-chart"></i>&nbsp;商品销量排行</a>&nbsp;
    <a href="javascript:change_shopname()" class="btn btn-info"><i class="fa fa-edit"></i>&nbsp;批量替换商品名称</a>&nbsp;
    <a href="javascript:change_inputs()" class="btn btn-info"><i class="fa fa-edit"></i>&nbsp;批量替换输入框标题</a>&nbsp;
    <a href="javascript:listTable('start')" class="btn btn-default" title="刷新商品列表"><i class="fa fa-refresh"></i></a>
  </div>
  
  <!-- 手机端布局：分两行显示 -->
  <div class="visible-xs">
    <!-- 第一行：添加商品按钮 + 更多按钮 + 刷新按钮 -->
    <div class="row">
      <div class="col-xs-6">
        <a href="./shopedit.php?my=add&cid=<?php echo htmlspecialchars($_GET['cid'])?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加商品</a>
      </div>
      <div class="col-xs-6 text-right">
        <!-- 手机端：更多按钮和刷新按钮放在添加商品的右边 -->
        <div class="btn-group">
          <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            更多 <span class="caret"></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-right">
            <li><a href="javascript:reset_sort(<?php echo $cid?>)">重置商品排序</a></li>
	<li><a href="./shoprank.php">商品销量排行</a></li>
	<li><a href="javascript:change_shopname()">批量替换商品名称</a></li>
	<li><a href="javascript:change_inputs()">批量替换输入框标题</a></li>
  </ul>
        </div>
        <a href="javascript:listTable('start')" class="btn btn-default" title="刷新商品列表" style="margin-left: 5px;"><i class="fa fa-refresh"></i></a>
      </div>
    </div>
    <!-- 第二行：搜索框 -->
    <div class="row" style="margin-top: 10px;">
      <div class="col-xs-12">
        <div class="input-group">
          <input type="text" class="form-control" name="kw" placeholder="请输入商品名称">
          <span class="input-group-btn">
            <button type="submit" class="btn btn-success">搜索</button>
          </span>
        </div>
      </div>
    </div>
  </div>
</form>

<div id="listTable"></div>
<?php }?>
    </div>
<?php if(!isset($_GET['cid']))echo '<font color="grey">提示：查看单个分类的商品列表可进行商品排序操作</font>';?>
  </div>
      
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/shoplist.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>