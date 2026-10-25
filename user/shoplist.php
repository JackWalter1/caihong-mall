<?php
/**
 * 商品管理
**/
include("../includes/common.php");
$title='商品管理';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

unset($islogin2);
?>
<div class="wrapper">
  <div class="col-sm-12">
<?php
if($userrow['power']==0){
	showmsg('你没有权限使用此功能！',3);
}

$my=isset($_GET['my'])?$_GET['my']:null;

$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 ORDER BY sort ASC");
$select='<option value="0">请选择分类</option>';
$shua_class[0]='未分类';
while($res = $rs->fetch()){
	$shua_class[$res['cid']]=$res['name'];
	$select.='<option value="'.$res['cid'].'">'.$res['name'].'</option>';
}
?>
<div class="modal fade" align="left" id="search2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">商品查找</h4>
      </div>
      <div class="modal-body">
      <form action="shoplist.php" method="GET">
      <div class="form-group">
        <label for="cid">商品分类</label>
        <select name="cid" class="form-control">
		<?php echo $select?>
		</select>
      </div>
      <div class="form-group">
        <label for="kw">关键词</label>
        <input type="text" class="form-control" name="kw" placeholder="输入商品名称关键词" value="<?php echo isset($_GET['kw'])?htmlspecialchars($_GET['kw']):''?>">
      </div>
      <input type="submit" class="btn btn-primary btn-block" value="查找"></form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>
<?php
$price_obj = new \lib\Price($userrow['zid'],$userrow);

if($my=='edit')
{
$tid=intval($_GET['tid']);
$row=$DB->getRow("SELECT * FROM pre_tools WHERE tid='$tid' LIMIT 1");
$price_obj->setToolInfo($tid,$row);
// 获取之前的分类和关键词参数
$prev_cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
$prev_kw = isset($_GET['kw']) ? urlencode($_GET['kw']) : '';

// 构建返回链接
$back_url = './shoplist.php';
if($prev_cid > 0 || $prev_kw != '') {
    $back_url .= '?cid=' . $prev_cid;
    if($prev_kw != '') {
        $back_url .= '&kw=' . $prev_kw;
    }
}

echo '<div class="panel panel-default">
<div class="panel-heading font-bold">修改商品价格 <a href="'.$back_url.'" style="color:#fff00f"> 返回 </a></div>';
echo '<div class="panel-body">';
echo '<form action="./shoplist.php?my=edit_submit&tid='.$tid.'&cid='.$prev_cid.'&kw='.$prev_kw.'" method="POST">
<div class="form-group">
<label>商品名称:</label><br>
<input type="text" class="form-control" name="name" value="'.$row['name'].'" disabled>
</div>';
if($userrow['power']==2)echo '
<div class="form-group">
<label>成本价格:</label><br>
<input type="text" class="form-control" name="cost2" value="'.$price_obj->getToolCost2($tid).'" disabled>
</div>
<div class="form-group">
<label>下级分站代理价格:</label><br>
<input type="text" class="form-control" name="cost" value="'.$price_obj->getToolCost($tid).'">
</div>';
else echo '
<div class="form-group">
<label>成本价格:</label><br>
<input type="text" class="form-control" name="cost" value="'.$price_obj->getToolCost($tid).'" disabled>
</div>';
echo '<div class="form-group">
<label>销售价格:</label><br>
<input type="text" class="form-control" name="price" value="'.$price_obj->getToolPrice($tid).'">
</div>
<div class="form-group">
<label>是否上架:</label><br>
<select class="form-control" name="del" default="'.$price_obj->getToolDel($tid).'"><option value="0">1_是</option><option value="1">0_否</option></select>
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>
</div>
</div>';
echo '
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>';
}
elseif($my=='edit_submit')
{
$tid=intval($_GET['tid']);
$rows=$DB->getRow("SELECT * FROM pre_tools WHERE tid='$tid' LIMIT 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$price_obj->setToolInfo($tid,$rows);
$price=round(daddslashes($_POST['price']),2);
$mainprice = $price_obj->getMainPrice();//主站商品售价
$maincost = $price_obj->getMainCost();//主站商品成本价
$del=intval($_POST['del']);
if(!is_numeric($price) || !preg_match('/^[0-9.]+$/', $price))showmsg('价格输入不规范',3);
if($userrow['power']==2){
	$cost=round(daddslashes($_POST['cost']),2);
	if(!is_numeric($cost) || !preg_match('/^[0-9.]+$/', $cost))showmsg('价格输入不规范',3);
	if($cost<$price_obj->getToolCost2($tid)){
		showmsg('下级代理价格不能低于成本价格！',3);
	}
	if($price<$cost){
		showmsg('销售价格不能低于下级代理价格！',3);
	}
	if($conf['fenzhan_pricelimit']==1 && $maincost>0 && ($maincost>1 && $cost>$maincost*2 || $maincost<=1 && $cost>2))
		showmsg('下级代理价格最高不能超过原代理价格的2倍（低于1元的商品，代理价格最高不能超过2元）',3);
}else{
	if($price<$price_obj->getToolCost($tid)){
		showmsg('销售价格不能低于成本价格！',3);
	}
	$cost=0;
}
if($conf['fenzhan_pricelimit']==1 && ($mainprice>1 && $price>$mainprice*2 || $mainprice<=1 && $price>2))
	showmsg('商品售价最高不能超过原售价的2倍（低于1元的商品，售价最高不能超过2元）',3);
// 获取之前的分类和关键词参数
$prev_cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
$prev_kw = isset($_GET['kw']) ? urlencode($_GET['kw']) : '';

// 构建返回链接
$back_url = './shoplist.php';
if($prev_cid > 0 || $prev_kw != '') {
    $back_url .= '?cid=' . $prev_cid;
    if($prev_kw != '') {
        $back_url .= '&kw=' . $prev_kw;
    }
}

if($price_obj->setPriceInfo($tid,$del,$price,$cost))
	showmsg('修改商品成功！<br/><br/><a href="'.$back_url.'">>>返回商品列表</a>',1);
else
	showmsg('修改商品失败！数据库操作异常，请稍后重试',4);
}
elseif($my=='reset')
{
if($DB->exec("UPDATE pre_site SET price=NULL WHERE zid='{$userrow['zid']}'"))
	showmsg('重置成功！<br/><br/><a href="./shoplist.php">>>返回商品列表</a>',1);
else
	showmsg('重置失败！数据库操作异常，请稍后重试',4);
}
else
{
// 处理分类和关键词搜索
$cid = intval($_GET['cid']);
$kw = isset($_GET['kw']) ? trim(daddslashes($_GET['kw'])) : '';

// 构建查询条件
$where = " active=1";
if($cid > 0) {
	$where .= " AND cid='$cid'";
}
if(!empty($kw)) {
	$where .= " AND name LIKE '%$kw%'";
}

// 计算商品数量
$numrows=$DB->getColumn("SELECT count(*) FROM pre_tools WHERE{$where}");

// 构建链接参数
$link = '';
if($cid > 0) {
	$link .= '&cid='.$cid;
}
if(!empty($kw)) {
	$link .= '&kw='.urlencode($kw);
}

// 构建页面内容
if($cid > 0 || !empty($kw)) {
	$title_text = '';
	if($cid > 0) {
		$title_text .= $shua_class[$cid].'分类';
	}
	if(!empty($kw)) {
		$title_text .= (!empty($title_text) ? ' - ' : '') . '关键词: "'.htmlspecialchars($kw).'"';
	}
	$con='
	<div class="panel panel-default"><div class="panel-heading font-bold">'.$title_text.' - [<a href="shoplist.php" style="color:#fff00f">查看全部</a>]</div>
	<div class="well well-sm" style="margin: 0;">共找到 <b>'.$numrows.'</b> 个商品</div>
	<div class="wrapper">
    <a href="#" data-toggle="modal" data-target="#search2" id="search2" class="btn btn-primary"><i class="fa fa-navicon"></i>&nbsp;商品查找</a>&nbsp;<a class="btn btn-info" href="javascript:void(0)" onclick="up_price('.$cid.')"><i class="fa fa-plus-circle"></i>&nbsp;提升售价</a></div>';
} else {
	$con='
	<div class="panel panel-default"><div class="panel-heading font-bold">商品列表</div>
	<div class="well well-sm" style="margin: 0;">系统共有 <b>'.$numrows.'</b> 个商品 - 提升价格赚的更多哦！提高价格最好不要太贵了否则没人买的哦！</div>
    <div class="wrapper">
    <a href="#" data-toggle="modal" data-target="#search2" id="search2" class="btn btn-primary"><i class="fa fa-navicon"></i>&nbsp;商品查找</a>&nbsp;<a class="btn btn-success" href="#" data-toggle="modal" data-target="#resetPriceModal"><i class="fa fa-refresh"></i>&nbsp;恢复价格</a>&nbsp;<a class="btn btn-warning" href="#" data-toggle="modal" data-target="#batchPriceModal"><i class="fa fa-calculator"></i>&nbsp;批量修改</a>&nbsp;<a class="btn btn-info" href="javascript:void(0)" onclick="restoreLastPrice()"><i class="fa fa-undo"></i>&nbsp;恢复到上一次修改</a>&nbsp;<a class="btn btn-danger" href="javascript:void(0)" onclick="showPriceHistory()"><i class="fa fa-history"></i>&nbsp;历史记录</a></div>';
}

// 设置最终的SQL条件
$sql = $where;

echo $con;

?>
      <div class="table-responsive">
        <table class="table table-striped b-t b-light">
          <thead><tr><th>操作</th><th>商品名称</th><th>成本价格</th><?php if($userrow['power']==2){?><th style="font-size:14px">下级价格</th><?php }?><th>销售价格</th><th>状态</th></tr></thead>
          <tbody>
<?php
$pagesize=30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);

$rs=$DB->query("SELECT * FROM pre_tools WHERE{$sql} ORDER BY sort ASC LIMIT $offset,$pagesize");
while($res = $rs->fetch())
{
	$price_obj->setToolInfo($res['tid'],$res);
echo '<tr>
			<td>
						<a href="./shoplist.php?my=edit&tid='.$res['tid'].'&cid='.$cid.'&kw='.urlencode($kw).'" class="btn btn-info btn-xs">编辑</a>
				</td>
			<td><b><a title="点此下单" style="color:#000" href="./shop.php?cid='.$res['cid'].'&tid='.$res['tid'].'">'.$res['name'].'</a></b></td>
			<td><font color="#FF0000">'.($userrow['power']==2?$price_obj->getToolCost2($res['tid']).'元</font> </td>
			<td><font color="#9400D3">'.$price_obj->getToolCost($res['tid']):$price_obj->getToolCost($res['tid'])).'元</font></td><td><font color="#FF0ff0">'.$price_obj->getToolPrice($res['tid']).'元</font> </td>
			<td>'.($price_obj->getToolDel($res['tid'])==1 || $res['close']==1?'<font color=red>已下架</font>':'<font color=green>上架中</font>').'</td>
		</tr>';}
?>
		          
          </tbody>
        </table>
<?php
echo'<ul class="pagination"  style="margin-left:1em">';
$first=1;
$prev=$page-1;
$next=$page+1;
$last=$pages;
if ($page>1)
{
echo '<li><a href="shoplist.php?page='.$first.$link.'">首页</a></li>';
echo '<li><a href="shoplist.php?page='.$prev.$link.'">&laquo;</a></li>';
} else {
echo '<li class="disabled"><a>首页</a></li>';
echo '<li class="disabled"><a>&laquo;</a></li>';
}
$start=$page-10>1?$page-10:1;
$end=$page+10<$pages?$page+10:$pages;
for ($i=$start;$i<$page;$i++)
echo '<li><a href="shoplist.php?page='.$i.$link.'">'.$i .'</a></li>';
echo '<li class="disabled"><a>'.$page.'</a></li>';
for ($i=$page+1;$i<=$end;$i++)
echo '<li><a href="shoplist.php?page='.$i.$link.'">'.$i .'</a></li>';
if ($page<$pages)
{
echo '<li><a href="shoplist.php?page='.$next.$link.'">&raquo;</a></li>';
echo '<li><a href="shoplist.php?page='.$last.$link.'">尾页</a></li>';
} else {
echo '<li class="disabled"><a>&raquo;</a></li>';
echo '<li class="disabled"><a>尾页</a></li>';
}
echo'</ul>';
#分页
}?></div>
<div class="panel-footer">
<span class="glyphicon glyphicon-info-sign"></span>
修改价格之后首页价格没变化？退出当前登录的账号后首页才能看到你设定的售价，否则看到的都是成本价。
</div>
</div>

</div>
<?php include './foot.php';?>
<!-- 批量修改价格模态框 -->
<div class="modal fade" id="batchPriceModal" tabindex="-1" role="dialog" aria-labelledby="batchPriceModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="batchPriceModalLabel">批量加价/降价一键修改</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <strong>提示：</strong>商品价格为0或商品名带"免费"字样不参与运算
        </div>
        
        <div class="form-group">
          <label for="operationType">操作类型：</label>
          <select class="form-control" id="operationType">
            <option value="add">加价</option>
            <option value="subtract">降价</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="priceType">调整价格：</label>
          <select class="form-control" id="priceType">
            <option value="price">销售价格</option>
            <?php if($userrow['power']==2){?><option value="cost">代理价格</option><?php }?>
          </select>
        </div>
        
        <div class="form-group">
          <label for="batchType">批量类型：</label>
          <select class="form-control" id="batchType">
            <option value="fixed">固定值</option>
            <option value="percent">百分比</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="modifyValue">修改值：</label>
          <input type="text" class="form-control" id="modifyValue" placeholder="填写百分比或固定值">
        </div>
        
        <div class="form-group">
          <label for="goodsCategory">所属分类：</label>
          <select class="form-control" id="goodsCategory" multiple="multiple" size="5">
            <option value="0">全部分类</option>
            <?php echo $select; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label for="goodsName">商品名称：</label>
          <input type="text" class="form-control" id="goodsName" placeholder="填写商品名的关键词，留空则为全部商品">
        </div>
        
        <div class="form-group">
          <label for="goodsStatus">商品状态：</label>
          <select class="form-control" id="goodsStatus">
            <option value="0">全部商品</option>
            <option value="1">上架中</option>
            <option value="2">已下架</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" onclick="batchUpdatePrice()">确定修改</button>
      </div>
    </div>
  </div>
</div>

<!-- 恢复价格模态框 -->
<div class="modal fade" id="resetPriceModal" tabindex="-1" role="dialog" aria-labelledby="resetPriceModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resetPriceModalLabel">恢复商品价格</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <strong>提示：</strong>恢复价格将清除所有自定义价格设定，恢复到最初状态
        </div>
        
        <div class="form-group">
          <label for="resetGoodsCategory">所属分类</label>
          <select class="form-control" id="resetGoodsCategory">
            <option value="0">全部分类</option>
            <?php echo $select; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label for="resetGoodsName">商品名称</label>
          <input type="text" class="form-control" id="resetGoodsName" placeholder="输入商品名称关键词，留空则为全部商品">
        </div>
        
        <div class="form-group">
          <label for="resetGoodsStatus">商品状态</label>
          <select class="form-control" id="resetGoodsStatus">
            <option value="0">全部商品</option>
            <option value="1">上架中</option>
            <option value="2">已下架</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" onclick="resetPrice()">确定恢复</button>
      </div>
    </div>
  </div>
</div>

<!-- 价格历史记录模态框 -->
<div class="modal fade" id="priceHistoryModal" tabindex="-1" role="dialog" aria-labelledby="priceHistoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="priceHistoryModalLabel">价格修改历史记录</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <strong>提示：</strong>以下是您的价格修改历史记录，您可以选择特定版本进行恢复
        </div>
        <div id="historyList" class="table-responsive">
          <!-- 历史记录列表将通过AJAX动态加载 -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<script>
function reset_price(cid){
	if(confirm('是否要重置所有商品价格设定，恢复到最初状态？')){
		$.ajax({
			type:"post",
			url:"ajax_user.php?act=reset_price",
			data:{
				cid:cid
			},
			dataType:"json",
			success:function(data){
				if(data.code==0){
					layer.alert('恢复价格成功！',{icon:1},function(){
				      window.location.reload();
				    });
				}else{
					layer.alert(data.msg);
				}
			}
		});
	}
}

// 新的恢复价格函数，支持按条件恢复
function resetPrice() {
    // 获取表单数据
    var goodsCategory = $('#resetGoodsCategory').val();
    var goodsName = $('#resetGoodsName').val();
    var goodsStatus = $('#resetGoodsStatus').val();
    
    // 确认操作
    layer.confirm('确定要恢复符合条件的商品价格吗？', {icon: 3, title: '确认'}, function(index) {
        layer.close(index);
        
        // 显示加载层
        var loadingIndex = layer.load(1, {shade: [0.1,'#fff']});
        
        // 发送AJAX请求
        $.ajax({
            type: "post",
            url: "ajax_user.php?act=reset_price",
            data: {
                cid: goodsCategory,
                name: goodsName,
                status: goodsStatus
            },
            dataType: "json",
            success: function(data) {
                layer.close(loadingIndex);
                
                if (data.code == 0) {
                    layer.alert(data.msg, {icon: 1}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 5});
                }
            },
            error: function() {
                layer.close(loadingIndex);
                layer.alert('操作失败，请稍后重试', {icon: 5});
            }
        });
    });
}
function up_price(cid){
    // 获取当前筛选条件
    var kw = '';
    var urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('kw')){
        kw = urlParams.get('kw');
    }
    
    layer.prompt({title: '价格提升百分比 例如5 最好不要超过10', formType: 0}, function(text, index){
	layer.close(index);
	$.ajax({
		type:"post",
		url:"ajax_user.php?act=up_price",
		data:{
			up:text,cid:cid,kw:kw
		},
		dataType:"json",
		success:function(data){
			if(data.code==0){
				layer.alert('价格提升成功！刷新即可看到效果',{icon:1},function(){
		              window.location.reload();
		            });
			}else{
				layer.alert(data.msg);
			}
		}
	});
	});
}

function batchUpdatePrice() {
    // 获取表单数据
    var operationType = $('#operationType').val();
    var priceType = $('#priceType').val();
    var batchType = $('#batchType').val();
    var modifyValue = $('#modifyValue').val();
    var goodsCategory = $('#goodsCategory').val();
    var goodsName = $('#goodsName').val();
    var goodsStatus = $('#goodsStatus').val();
    
    // 处理多选分类值，转换为逗号分隔的字符串
    if (Array.isArray(goodsCategory)) {
        goodsCategory = goodsCategory.join(',');
    }
    
    // 验证输入
    if (!modifyValue) {
        layer.alert('请填写修改值', {icon: 5});
        return;
    }
    
    if (batchType == 'percent' && (isNaN(modifyValue) || parseFloat(modifyValue) <= 0)) {
        layer.alert('百分比必须为正数', {icon: 5});
        return;
    }
    
    if (batchType == 'fixed' && isNaN(modifyValue)) {
        layer.alert('固定值必须为数字', {icon: 5});
        return;
    }
    
    // 确认修改
    layer.confirm('确定要执行批量价格修改吗？', {icon: 3, title: '确认'}, function(index) {
        layer.close(index);
        
        // 显示加载层
        var loadingIndex = layer.load(1, {shade: [0.1,'#fff']});
        
        // 发送AJAX请求
        $.ajax({
            type: "post",
            url: "ajax_user.php?act=batch_update_price",
            data: {
                operation: operationType,
                price_type: priceType,
                batch_type: batchType,
                value: modifyValue,
                cid: goodsCategory,
                name: goodsName,
                status: goodsStatus
            },
            dataType: "json",
            success: function(data) {
                layer.close(loadingIndex);
                
                if (data.code == 0) {
                    layer.alert(data.msg, {icon: 1}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 5});
                }
            },
            error: function() {
                layer.close(loadingIndex);
                layer.alert('操作失败，请稍后重试', {icon: 5});
            }
        });
    });
}

// 恢复到上一次修改
function restoreLastPrice() {
    layer.confirm('确定要恢复到上一次修改的状态吗？', {
        icon: 3,
        title: '确认恢复',
        btn: ['确定', '取消']
    }, function(index) {
        layer.close(index);
        
        // 显示加载层
        var loadingIndex = layer.load(1, {shade: [0.1,'#fff']});
        
        // 发送AJAX请求
        $.ajax({
            type: "post",
            url: "ajax_user.php?act=restore_last_price",
            dataType: "json",
            success: function(data) {
                layer.close(loadingIndex);
                
                if (data.code == 0) {
                    layer.alert(data.msg, {icon: 1}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 5});
                }
            },
            error: function() {
                layer.close(loadingIndex);
                layer.alert('操作失败，请稍后重试', {icon: 5});
            }
        });
    });
}

// 显示价格修改历史记录
function showPriceHistory() {
    // 显示历史记录模态框
    $('#priceHistoryModal').modal('show');
    
    // 加载历史记录
    loadHistoryList();
}

// 加载历史记录列表
function loadHistoryList(page) {
    page = page || 1;
    
    // 显示加载提示
    $('#historyList').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> 加载中...</div>');
    
    // 发送AJAX请求
    $.ajax({
        type: "post",
        url: "ajax_user.php?act=get_price_history",
        data: {
            page: page
        },
        dataType: "json",
        success: function(data) {
            if (data.code == 0) {
                renderHistoryList(data);
            } else {
                $('#historyList').html('<div class="alert alert-danger text-center">' + data.msg + '</div>');
            }
        },
        error: function() {
            $('#historyList').html('<div class="alert alert-danger text-center">加载失败，请稍后重试</div>');
        }
    });
}

// 渲染历史记录列表
function renderHistoryList(data) {
    var html = '';
    
    if (data.data.length > 0) {
        html += '<table class="table table-striped b-t b-light">';
        html += '<thead><tr><th>操作描述</th><th>修改时间</th><th>涉及商品数量</th><th>操作</th></tr></thead>';
        html += '<tbody>';
        
        $.each(data.data, function(index, item) {
            html += '<tr>';
            html += '<td>' + item.description + '</td>';
            html += '<td>' + item.create_time + '</td>';
            html += '<td>' + item.price_data_count + '</td>';
            html += '<td><button class="btn btn-primary btn-xs" onclick="restoreFromHistory(' + item.id + ')">恢复此版本</button></td>';
            html += '</tr>';
        });
        
        html += '</tbody>';
        html += '</table>';
        
        // 分页
        var totalPages = Math.ceil(data.total / data.pagesize);
        if (totalPages > 1) {
            html += '<nav aria-label="Page navigation" style="margin-top: 15px;">';
            html += '<ul class="pagination pagination-sm">';
            
            // 上一页
            if (data.page > 1) {
                html += '<li><a href="javascript:void(0)" onclick="loadHistoryList(' + (data.page - 1) + ')">&laquo;</a></li>';
            } else {
                html += '<li class="disabled"><a href="javascript:void(0)">&laquo;</a></li>';
            }
            
            // 页码
            for (var i = 1; i <= totalPages; i++) {
                if (i == data.page) {
                    html += '<li class="active"><a href="javascript:void(0)">' + i + '</a></li>';
                } else {
                    html += '<li><a href="javascript:void(0)" onclick="loadHistoryList(' + i + ')">' + i + '</a></li>';
                }
            }
            
            // 下一页
            if (data.page < totalPages) {
                html += '<li><a href="javascript:void(0)" onclick="loadHistoryList(' + (data.page + 1) + ')">&raquo;</a></li>';
            } else {
                html += '<li class="disabled"><a href="javascript:void(0)">&raquo;</a></li>';
            }
            
            html += '</ul>';
            html += '</nav>';
        }
    } else {
        html += '<div class="alert alert-info text-center">暂无价格修改历史记录</div>';
    }
    
    $('#historyList').html(html);
}

// 从指定历史记录恢复
function restoreFromHistory(id) {
    layer.confirm('确定要恢复到该历史版本吗？此操作将覆盖当前的价格设置。', {
        icon: 3,
        title: '确认恢复',
        btn: ['确定', '取消']
    }, function(index) {
        layer.close(index);
        
        // 显示加载层
        var loadingIndex = layer.load(1, {shade: [0.1,'#fff']});
        
        // 发送AJAX请求
        $.ajax({
            type: "post",
            url: "ajax_user.php?act=restore_from_history",
            data: {
                id: id
            },
            dataType: "json",
            success: function(data) {
                layer.close(loadingIndex);
                
                if (data.code == 0) {
                    layer.alert(data.msg, {icon: 1}, function() {
                        // 关闭历史记录模态框
                        $('#priceHistoryModal').modal('hide');
                        // 刷新页面
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 5});
                }
            },
            error: function() {
                layer.close(loadingIndex);
                layer.alert('操作失败，请稍后重试', {icon: 5});
            }
        });
    });
}
</script>
</body>
</html>