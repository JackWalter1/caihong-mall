<?php
/**
 * 商品管理
 **/
include("../includes/common.php");
$title='审核管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");


adminpermission('shop', 1);

$my=isset($_GET['my'])?$_GET['my']:null;

$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select='<option value="0">未分类</option>';
$shua_class[0]='未分类';
while($res = $rs->fetch()){
    $shua_class[$res['cid']]=$res['name'];
    $select.='<option value="'.$res['cid'].'">'.$res['name'].'</option>';
}

if ($_SESSION['priceselect']) {
    $priceselect=$_SESSION['priceselect'];
} else {
    $rs=$DB->query("SELECT * FROM pre_price order by id asc");
    $priceselect='<option value="0">不使用加价模板</option>';
    while($res = $rs->fetch()){
        $kind = $res['kind']==1?'元':'倍';
        $priceselect.='<option value="'.$res['id'].'" kind="'.$res['kind'].'" p_2="'.$res['p_2'].'" p_1="'.$res['p_1'].'" p_0="'.$res['p_0'].'" >'.$res['name'].'('.$res['p_2'].$kind.'|'.$res['p_1'].$kind.'|'.$res['p_0'].$kind.')</option>';
    }
}
if($my=='edit')
{
$tid=$_GET['tid'];
$row=$DB->getRow("select * from pre_tools where tid='$tid' limit 1");
?>
<form action="./supshoplist.php?my=edit_submit&tid=<?php echo $tid?>" method="POST" onsubmit="return checkinput()">
<div class="col-sm-12 col-md-12">
<div class="block">
<div class="block-title"><h3 class="panel-title">商品审核设置</h3></div>
<div class="">
<div class="form-group">
<label>商品分类:</label><br>
<select name="cid" class="form-control" default="<?php echo $row['cid']?>"><?php echo $select?></select>
</div>
<div class="form-group">
<label>*商品名称:</label><br>
<input type="text" class="form-control" name="name" value="<?php echo $row['name']?>" required>
</div>
<div class="form-group">
<label>加价模板:</label>&nbsp;(<a href="./price.php" target="_blank">管理</a>)<br>
<select name="prid" class="form-control" default="<?php echo $row['prid']?>"><?php echo $priceselect?></select>
</div>
<div class="form-group" id="prid1" style="display:none;">
<label>*成本价格:</label><br>
<input type="text" class="form-control" name="price1" value="<?php echo $row['sup_price']?>">
</div>
<table class="table table-striped table-bordered table-condensed" id="prid0">
<tbody>
<tr align="center"><td>*销售价格</td><td>普及版价格</td><td>专业版价格</td></tr>
<tr>
<td><input type="text" name="price" value="<?php echo $row['sup_price']?>" class="form-control input-sm"/></td>
<td><input type="text" name="cost" value="<?php echo $row['sup_price']?>" class="form-control input-sm" placeholder="不填写则同步销售价格"/></td>
<td><input type="text" name="cost2" value="<?php echo $row['sup_price']?>" class="form-control input-sm" placeholder="不填写则同步普及版价格"/></td>
</tr>
</table>
<div class="form-group">
<label>批发价格优惠设置:</label><br>
<input type="text" class="form-control" name="prices" value="<?php echo $row['prices']?>">
<pre><font color="green">填写格式：购满x个|减少x元单价,购满x个|减少x元单价  例如10|0.1,20|0.3,30|0.5</font></pre>
</div>
<div class="form-group">
<label>第一个输入框标题:</label><br>
<div class="input-group">
<input type="text" class="form-control" name="input" value="<?php echo $row['input']?>" placeholder="留空默认为“下单账号”"><span class="input-group-btn"><a href="#inputabout" data-toggle="modal" class="btn btn-info" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a></span>
</div>
</div>
<div class="form-group">
<label>更多输入框标题:</label><br>
<div class="input-group">
<input type="text" class="form-control" name="inputs" value="<?php echo $row['inputs']?>" placeholder="留空则不显示更多输入框"><span class="input-group-btn"><a href="#inputsabout" data-toggle="modal" class="btn btn-info" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a></span>
</div>
<pre><font color="green">多个输入框请用|隔开(不能超过4个)</font></pre>
</div>
<div class="form-group">
<label>商品简介:</label>(没有请留空)<br>
<textarea class="form-control" id="editor_id" name="desc" rows="3" style="width:100%" placeholder="当选择该商品时自动显示，支持HTML代码"><?php echo htmlspecialchars($row['desc'])?></textarea>
</div>
<div class="form-group">
<label>提示内容:</label>(没有请留空)<br>
<input type="text" class="form-control" name="alert" value="<?php echo htmlspecialchars($row['alert'])?>" placeholder="当选择该商品时自动弹出提示，不支持HTML代码">
</div>
<div class="form-group">
<label>商品图片:</label><br>
<input type="file" id="file" onchange="fileUpload()" style="display:none;"/>
<div class="input-group">
<input type="text" class="form-control" id="shopimg" name="shopimg" value="<?php echo $row['shopimg']?>" placeholder="填写图片URL，没有请留空"><span class="input-group-btn"><a href="javascript:fileSelect()" class="btn btn-success" title="上传图片"><i class="glyphicon glyphicon-upload"></i></a><a href="javascript:fileView()" class="btn btn-warning" title="查看图片"><i class="glyphicon glyphicon-picture"></i></a></span>
</div>
</div>
<div class="form-group">
<label>显示数量选择框:</label><br>
<select class="form-control" name="multi" default="<?php echo $row['multi']?>"><option value="1">1_是</option><option value="0">0_否</option></select>
</div>
<table class="table table-striped table-bordered table-condensed" id="multi0" style="display:none;">
<tbody>
<tr align="center"><td>最小下单数量</td><td>最大下单数量</td></tr>
<tr>
<td><input type="text" name="min" class="form-control input-sm" value="<?php echo $row['min']?>" placeholder="留空则默认为1"/></td>
<td><input type="text" name="max" class="form-control input-sm" value="<?php echo $row['max']?>" placeholder="留空则不限数量"/></td>
</tr>
</table>
<div class="form-group">
<label>允许重复下单:</label><br>
<div class="input-group">
<select class="form-control" name="repeat" default="<?php echo $row['repeat']?>"><option value="0">0_否</option><option value="1">1_是</option></select>
<a tabindex="0" class="input-group-addon" role="button" data-toggle="popover" data-trigger="focus" title="" data-placement="bottom" data-content="是指相同下单输入内容（非同一用户）当天只能下单一次，或上一条订单未处理的情况下不能重复下单"><span class="glyphicon glyphicon-info-sign"></span></a>
</div>
</div>
<div class="form-group">
<label>验证操作:</label><br>
<select class="form-control" name="validate" default="<?php echo $row['validate']?>"><option value="0">不开启验证</option><option value="1">验证QQ空间是否有访问权限</option><option value="2">验证已开通服务(符合则禁止下单)</option><option value="3">验证已开通服务(符合则不对接社区)</option></select>
</div>
<div class="form-group" id="valiserv" style="display:none;">
<label>需要验证的已开通服务:</label><br>
<select class="form-control" name="valiserv" default="<?php echo $row['valiserv']?>"><option value="vip">QQ会员</option><option value="svip">超级会员</option><option value="red">红钻贵族</option><option value="green">绿钻贵族</option><option value="sgreen">绿钻豪华版</option><option value="yellow">黄钻贵族</option><option value="syellow">豪华黄钻</option><option value="hollywood">腾讯视频VIP</option><option value="qqmsey">付费音乐包</option><option value="qqmstw">豪华付费音乐包</option><option value="weiyun">微云会员</option><option value="sweiyun">微云超级会员</option></select>
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改">
<br/><a href="shoplist.php">>>返回商品列表</a>
</div></div>
</div>
</form>
<?php
}
elseif($my=='edit_submit')
{
$tid=$_GET['tid'];
$rows=$DB->getRow("select * from pre_tools where tid='$tid' and goods_sid != 0 and audit_status='0' limit 1");
if(!$rows)
    showmsg('当前记录不存在！',3);
$cid=$_POST['cid'];
$name=$_POST['name'];
$prid=$_POST['prid'];
$price=($prid=='0' ? $_POST['price'] : $_POST['price1']);
if($price < $rows['sup_price'])showmsg('销售价格不能低于供货商售卖价格！',3);
$cost=$_POST['cost'];
$cost2=$_POST['cost2'];
$prices=$_POST['prices'];
$input=$_POST['input'];
$inputs=$_POST['inputs'];
$desc=$_POST['desc'];
$alert=$_POST['alert'];
$shopimg=$_POST['shopimg'];
$multi=$_POST['multi'];
$min=$_POST['min'];
$max=$_POST['max'];
$validate=$_POST['validate'];
$valiserv=$_POST['valiserv'];
$repeat=$_POST['repeat'];

if(($name==NULL || $price==NULL)){
    showmsg('保存错误，商品名称和价格不能为空！',3);
}elseif(($is_curl==2 && !$shequ)){
    showmsg('请选择对接社区！',3);
} else {
    if($DB->exec("UPDATE `pre_tools` SET `cid`='".$cid."',`name`='".$name."',`price`='".$price."',`cost`='".$cost."',`cost2`='".$cost2."',`prid`='".$prid."',`prices`='".$prices."',`input`='".$input."',`inputs`='".$inputs."',`desc`='".addslashes($desc)."',`alert`='".addslashes($alert)."',`shopimg`='".$shopimg."',`repeat`='".$repeat."',`multi`='".$multi."',`min`='".$min."',`max`='".$max."',`validate`='".$validate."',`valiserv`='".$valiserv."',`active`='1',`audit_status`='1' WHERE `tid`='".$tid."'")!==false)
        showmsg('商品审核通过成功！<br/><br/><a href="./supshoplist.php">>>返回审核列表</a>',1);
    else
        showmsg('商品审核通过失败！'.$DB->error(),4);
}
}else{
?>
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
            <span class="pull-right"><select id="pagesize" class="form-control" title="每页显示"><option value="30">30</option><option value="50">50</option><option value="60">60</option><option value="80">80</option><option value="100">100</option></select><span>
</span></span>
        </div>
        <form onsubmit="return searchItem()" method="GET" class="form-inline">
            <div class="form-group">
                <input type="text" class="form-control" name="kw" placeholder="请输入商品名称">
            </div>
            <button type="submit" class="btn btn-success">搜索</button>&nbsp;
            <a href="javascript:listTable('start')" class="btn btn-default" title="刷新商品列表"><i class="fa fa-refresh"></i></a>
        </form>

        <div id="listTable"></div>
        <?php }?>
    </div>
    <?php if(!isset($_GET['cid']))echo '<font color="grey">提示：查看单个分类的商品列表可进行商品排序操作';?>
</div>
<?php } ?>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/supshoplist.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>