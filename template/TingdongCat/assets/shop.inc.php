<?php
if(!defined('IN_CRONLITE'))exit();
$classhide = explode(',',$siterow['class']);
?>
<?php
if(isset($conf['ui_shop']) && $conf['ui_shop']>0){
//分类图片宫格
?>
	<div id="goodType" <?php if(isset($_GET['cid'])){?>style="display: none"<?php }?>>
<?php if(isset($conf['ui_shop']) && $conf['ui_shop']==1){?>
	<div class="row">
<?php
$rs=$DB->query("select * from pre_class where active=1 order by sort asc");
while($row = $rs->fetch()){
	if($is_fenzhan && in_array($row['cid'], $classhide))continue;
	if(!empty($row["shopimg"])){
		$productimg = $row["shopimg"];
	}else{
		$productimg = '/assets/img/Product/default.png';
	}
	if($usershop)$productimg='../'.$productimg;
	$count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$row['cid']} and active=1");
?>
		<div class="col-lg-4 col-xs-6">
			<a class="widget animation-fadeInQuick goodTypeChange onclick" data-id="<?php echo $row["cid"]?>">
				<img class="lazy" width="100%" data-original="<?php echo $productimg?>">
				<div class="widget-content text-center">
					<strong><?php echo $row["name"]?></strong>
					<p class="text-muted" style="margin-bottom:10px;text-align:center;">分类<?php echo $count?>个商品</p>
					<button type="button" data-id="<?php echo $row["cid"]?>" class="btn btn-rounded btn-info btn-block goodTypeChange">点击进入</button>
				</div>
			</a>
		</div>
<?php }?>
	</div>
<?php }elseif(isset($conf['ui_shop']) && $conf['ui_shop']==2){?>
<style type="text/css">
	.table>tbody>tr>td{vertical-align: baseline;}
</style>
	<table class="table table-striped table-borderless table-vcenter table-hover">
         <tbody>
<?php
$rs=$DB->query("select * from pre_class where active=1 order by sort asc");
while($row = $rs->fetch()){
	if($is_fenzhan && in_array($row['cid'], $classhide))continue;
	if(!empty($row["shopimg"])){
		$productimg = $row["shopimg"];
	}else{
		$productimg = 'assets/img/Product/default.png';
	}
	if($usershop)$productimg='../'.$productimg;
	$count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$row['cid']} and active=1");
?>
			<tr class="widget animation-fadeInQuick onclick goodTypeChange" data-id="<?php echo $row["cid"]?>">
                <td class="text-center" style="width: 100px;">
                    <img data-original="<?php echo $productimg?>" width="50" style="height:50px" alt="avatar" class="lazy img-circle img-thumbnail img-thumbnail-avatar">
                </td>
                <td>
                    <h3 class="widget-heading h4"><strong><?php echo $row["name"]?></strong></h3>
					<span class="text-muted">分类<?php echo $count?>个商品</span>
                </td>
                <td class="text-right">
                    <button type="button" data-id="<?php echo $row["cid"]?>" class="btn btn-rounded btn-info goodTypeChange">点击进入</button>
                </td>
            </tr>
<?php
}
?>
		   </tbody>
        </table>
<?php }elseif(isset($conf['ui_shop']) && $conf['ui_shop']==3){?>
	<div class="row">
<?php
$rs=$DB->query("select * from pre_class where active=1 order by sort asc");
while($row = $rs->fetch()){
	if($is_fenzhan && in_array($row['cid'], $classhide))continue;
	if(!empty($row["shopimg"])){
		$productimg = $row["shopimg"];
	}else{
		$productimg = 'assets/img/Product/default.png';
	}
	if($usershop)$productimg='../'.$productimg;
?>
		<div class="col-lg-3 col-xs-4" style="padding:0px">
		<div class="thumbnail" style="margin-bottom:3px;width:95%;margin: 2px auto;">
			<a class="widget animation-fadeInQuick goodTypeChange onclick" data-id="<?php echo $row["cid"]?>">
			<center style="margin-top:0;">
				<img class="lazy" data-original="<?php echo $productimg?>" style="height: 88px;">
				<strong style="white-space:nowrap"><?php echo $row["name"]?></strong>
				<span type="button" data-id="<?php echo $row["cid"]?>" class="btn btn-sm btn-info btn-block goodTypeChange">点击进入</span>
			</center>
			</a>
		</div>
		</div>
<?php }?>
	</div>
<?php }?>
	</div>
	<div id="goodTypeContent" <?php if(!isset($_GET['cid'])){?>style="display: none"<?php }?>>
		<div style="text-align: center;">
			<h3><span id="className"></span></h3>
			<img src="" id="classImg" width="50%" >
		</div>
		<br>
		<input type="hidden" name="cid" id="cid" value="0"/>
		<div class="form-group">
			<div class="input-group"><div class="input-group-addon">选择商品</div>
			<select name="tid" id="tid" class="form-control" onchange="getPoint();"><option value="0">请选择商品</option></select>
		</div></div>
		<div class="form-group" id="display_price" style="display:none;">
			<div class="input-group"><div class="input-group-addon">商品价格</div>
			<input type="text" name="need" id="need" class="form-control" style="center;color:#4169E1;font-weight:bold" disabled/>
		</div></div>
		<div class="form-group" id="display_left" style="display:none;">
			<div class="input-group"><div class="input-group-addon">库存数量</div>
			<input type="text" name="leftcount" id="leftcount" class="form-control" disabled/>
		</div></div>
		<div class="form-group" id="display_num" style="display:none;">
			<div class="input-group">
			<div class="input-group-addon">下单份数</div>
			<span class="input-group-btn"><input id="num_min" type="button" class="btn btn-info" style="border-radius: 0px;" value="━"></span>
			<input id="num" name="num" class="form-control" type="number" min="1" value="1"/>
			<span class="input-group-btn"><input id="num_add" type="button" class="btn btn-info" style="border-radius: 0px;" value="✚"></span>
		</div></div>
		<div id="inputsname"></div>
		<div id="alert_frame" class="alert alert-success animated rubberBand" style="display:none;background: linear-gradient(to right,#71D7A2,#5ED1D7);font-weight: bold;color:white;"></div>
		<?php if(isset($conf['shoppingcart']) && $conf['shoppingcart']==1){?>
		<div class="btn-group btn-group-justified form-group">
			<a class="btn btn-block" type="button" id="submit_cart_shop" style="background-color: #71D7A2; color: white;">加入购物车</a>
			<a type="submit" id="submit_buy" class="btn btn-block" style="background-color: #9ED3F9; color: white;">立即购买</a>
		</div>
		<?php }else{?>
		<div class="form-group">
			<input type="submit" id="submit_buy" class="btn btn-block" style="background-color: #9ED3F9; color: white;" value="立即购买">
		</div>
		<?php }?>
		<div class="form-group"><button type="button" class="btn btn-default btn-block btn-sm backType">返回重选分类</button></div>
	</div>
	<ul class="layui-fixbar" id="alert_cart" style="display:none;">
	  <li class="layui-icon" style="background-color:#3e4425db" onclick="openCart()"><i class="fa fa-shopping-cart"></i><div class="nav-counter" id="cart_count"></div></li>
	</ul>
<?php
}else{
//经典模式
$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select='<option value="0">请选择分类</option>';
$select_count=0;
$hideclass = false;
while($res = $rs->fetch()){
	if($is_fenzhan && in_array($res['cid'], $classhide))continue;
	$select_count++;
	$select.='<option value="'.$res['cid'].'">'.$res['name'].'</option>';
}
if($select_count==0)$hideclass = true;
?>

<style>
.payment-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin: 20px 0;
}
.buybutton {
    background: white;
    border: 2px solid #9ED3F9;
    border-radius: 12px;
    padding: 11px;
    width: 100%;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #2c3e50;
}
.buybutton:hover {
    background: #9ED3F9;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(158, 211, 249, 0.3);
}
.input-group-addon {
      font-weight: bold;
}
.input-group form-group {
    border-radius: 13px;
    box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.3);
    padding: 11px;
}
/* ===== 微蓝色调系统 ===== */
:root {
  --primary-blue: #7eb1ff; /* 主微蓝色 */
  --light-blue: #e6f0ff; /* 浅蓝背景 */
  --border-blue: rgba(126, 177, 255, 0.4); /* 半透明蓝边框 */
}

/* ===== 输入框系统 ===== */
.form-control, select, #cid, #tid {
  height: 40px;
  padding: 8px 16px;
  
  background: rgba(246, 250, 255, 0.95) !important;
  transition: all 0.2s ease;
}

/* 聚焦状态 */
.form-control:focus {
  border-color: rgba(126, 177, 255, 0.7) !important;
  box-shadow: 0 0 0 3px rgba(126, 177, 255, 0.15) !important;
}

/* ===== 输入组设计 ===== */
.input-group {
  border-radius: 9px;
  background: rgba(230, 240, 255, 0.8);
  border: 1px dashed rgba(126, 177, 255, 0);
}

.input-group-addon {
  background: transparent !important;
  border: none !important;
  color: var(--primary-blue) !important;
  min-width: 100px;
  font-weight: 500;
}

/* ===== 按钮交互 ===== */
#num_min, #num_add {
  background: rgba(126, 177, 255, 0.1) !important;
  border: 1px dashed rgba(126, 177, 255, 0) !important;
  color: var(--primary-blue) !important;
  
}

#num_min:hover, #num_add:hover {
  background: rgba(126, 177, 255, 0.2) !important;
}

/* ===== 下拉菜单优化 ===== */
select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%237eb1ff'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") !important;
  background-position: right 12px center;
  background-size: 16px;
  background-repeat: no-repeat;
  padding-right: 36px !important;
}

/* ===== 提示条 ===== */
.shuaibi-tip {
  background: rgba(230, 240, 255, 0.9);
  border: 1px dashed rgba(126, 177, 255, 0.4);
  border-radius: 8px;
  color: var(--primary-blue);
}

/* ===== 黑暗模式 ===== */
@media (prefers-color-scheme: dark) {
  :root {
    --primary-blue: #5d9cff;
    --light-blue: #1a2a40;
    --border-blue: rgba(93, 156, 255, 0.3);
  }
  
  .form-control, select {
    background: rgba(26, 42, 64, 0.9) !important;
    color: #b3d1ff;
  }
  
  .input-group {
    background: rgba(20, 35, 55, 0.9);
  }
  
  #num_min, #num_add {
    background: rgba(93, 156, 255, 0.1) !important;
  }
}
</style>

		<div id="goodTypeContents">
			<?php echo isset($conf['alert']) ? $conf['alert'] : '';?>
			<?php 
			$hasRecommend = false;
			try {
				$count = $DB->getColumn("SELECT COUNT(*) FROM pre_recommend WHERE active=1");
				if($count > 0) $hasRecommend = true;
			} catch (Exception $e) {
				$hasRecommend = false;
			}
			if($hasRecommend){
			?>
			<!-- 商品推荐按钮 -->
			<div class="form-group" style="margin-bottom: 10px;">
				<button type="button" id="recommendBtn" class="btn btn-block btn-warning" style="background: rgb(158, 211, 249); color: white; font-weight: bold; border-radius: 12px; padding: 12px; box-shadow: 0 4px 15px rgba(158, 211, 249, 0.3);">
					<i class="fa fa-star" style="margin-right: 8px;"></i> 商品推荐
				</button>
			</div>
			<?php } ?>
			<?php if(isset($conf['search_open']) && $conf['search_open']==1){?>
			<div class="form-group" id="display_searchBar">
				<div class="input-group"><div class="input-group-addon">搜索商品</div>
				<input type="text" id="searchkw" class="form-control" placeholder="搜索商品" onkeydown="if(event.keyCode==13){$('#doSearch').click()}"/>
				<div class="input-group-addon"><span class="glyphicon glyphicon-search onclick" title="搜索" id="doSearch"></span></div>
			</div></div>
			<?php }?>
			<div class="form-group" id="display_selectclass"<?php if($hideclass){?> style="display:none;"<?php }?>>
				<div class="input-group"><div class="input-group-addon">选择分类</div>
				<select name="tid" id="cid" class="form-control"><?php echo $select?></select>
			</div></div>
			<div class="form-group">
				<div class="input-group"><div class="input-group-addon">选择商品</div>
				<select name="tid" id="tid" class="form-control" onchange="getPoint();"><option value="0">请选择商品</option></select>
			</div></div>
			<div class="form-group" id="display_price" style="display:none;center;color:#4169E1;font-weight:bold">
				<div class="input-group"><div class="input-group-addon">商品价格</div>
				<input type="text" name="need" id="need" class="form-control" style="center;color:#4169E1;font-weight:bold" disabled/>
			</div></div>
			<div class="form-group" id="display_left" style="display:none;">
				<div class="input-group"><div class="input-group-addon">库存数量</div>
				<input type="text" name="leftcount" id="leftcount" class="form-control" disabled/>
			</div></div>
			<div class="form-group" id="display_num" style="display:none;">
                <div class="input-group">
                <div class="input-group-addon">下单份数</div>
                <span class="input-group-btn"><input id="num_min" type="button" class="btn btn-info" style="border-radius: 1px;background-color:#9ED3F9;" value="━"></span>
				<input id="num" name="num" class="form-control" type="number" min="1" value="1"/>
				<span class="input-group-btn"><input id="num_add" type="button" class="btn btn-info" style="border-radius: 1px;background-color:#9ED3F9;" value="✚"></span>
			</div></div>
			<div id="inputsname"></div>
			<div id="alert_frame" class="alert alert-success animated rubberBand" style="display:none;background: linear-gradient(to right,#70b9eb,#dc87ff);font-weight: bold;color:white;border-radius: 12px;box-shadow: 1px 2px 8px rgba(0, 0, 0, 0.2);"></div>
			<?php if(isset($conf['shoppingcart']) && $conf['shoppingcart']==1){?>
	    
		<div class="btn-group btn-group-justified form-group">
		    <a class="btn btn-block" type="button" id="submit_cart_shop" style="background-color: #71D7A2; color: white;">加入购物车</a>
			<a type="submit" id="submit_buy" class="btn btn-block" style="background-color: #9ED3F9; color: white;">立即购买</a>
            </div>
		<?php }else{?>
		<div class="form-group">
		<div class="payment-buttons">
			<input type="submit" id="submit_buy" class="btn btn-block" style="background-color: #9ED3F9; color: white;" value="立即购买">
			</div>
		</div>
		<?php }?>
			<div class="panel-body border-t" id="alert_cart" style="display:none;"><i class="fa fa-shopping-cart"></i>&nbsp;当前购物车已添加<b id="cart_count">0</b>个商品<a class="btn btn-xs btn-danger pull-right" href="javascript:openCart()">购物车列表</a></div>
		</div>
<?php } ?>