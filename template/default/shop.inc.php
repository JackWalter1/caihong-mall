<?php
if(!defined('IN_CRONLITE'))exit();
$classhide = explode(',',$siterow['class']);

// 模板配置 - 显示/隐藏控制
$show_order_warning = isset($conf['show_order_warning']) ? $conf['show_order_warning'] : '1';
$show_warning_div = isset($conf['show_warning_div']) ? $conf['show_warning_div'] : '1';
$show_guide_link = isset($conf['show_guide_link']) ? $conf['show_guide_link'] : '1';

// 定义空数据提示函数
function showEmptyMessage($title, $message) {
    echo '<div style="text-align: center; padding: 50px 20px; background-color: #f8f9fa; border-radius: 8px; margin: 20px 0;">
        <h3 style="color: #6c757d; margin-bottom: 20px;">'.$title.'</h3>
        <p style="color: #95a5a6; font-size: 16px;">'.$message.'</p>
    </div>';
}
?>
<?php if(isset($conf['ui_shop']) && $conf['ui_shop']>0){
//分类图片宫格
$primary_classes = array();
$secondary_classes = array();
$rs=$DB->query("select * from pre_class where active=1 order by sort asc");
while($row = $rs->fetch()){
    if($is_fenzhan && in_array($row['cid'], $classhide))continue;
    if($row['pid'] == 0){
        $primary_classes[] = $row;
    }else{
        if(!isset($secondary_classes[$row['pid']])){
            $secondary_classes[$row['pid']] = array();
        }
        $secondary_classes[$row['pid']][] = $row;
    }
}
?>
<!-- 警告信息 -->
<?php if($show_order_warning == '1'){ ?>
<div id="orderWarning" style="background-color: rgb(51, 51, 51); border-radius: 25px; box-shadow: rgb(242, 0, 255) 0px 0px 5px; padding: 5px; margin-top: 10px; margin-bottom: 5px; display: block;">
						<center>
							<span style="color:rgb(194, 79, 74)">
								
								<b>
									<font color="#D2B48C">【先确认能（下载/打开/使用）再下单】后果自负</font>
									
								</b>
							</span>
							<br>
							<span style="font-size:10px">
								<strong>
									<span>
										<span style="color:#E53333;">下单步骤</span>
										<span style="color:#E53333;"> &gt; </span>
										<span style="color:#E8E8E8;">选择分类</span>
										<span style="color:#E53333;"> &gt; </span>
										<span style="color:#009900;">选择商品</span>
										<span style="color:#E53333;"> &gt; </span>
										<span style="color:#EE33EE;">填写信息</span>
										<span style="color:#E53333;"> &gt; </span>
										<span style="color:#F08080;">下单成功</span>
									</span>
								</strong>
							</span>
						</center>
					</div>
<?php } ?>
<!-- 购前必看按钮 -->
<?php if($show_guide_link == '1'){ ?>
<a class="btn custom-btn" href="./template/XHY-01/content.html" target="_blank" role="button" style="display: flex; align-items: center; justify-content: space-between; background: linear-gradient(to right, #ffffff, #ffcccc); border: 2px solid #8B0000; border-radius: 25px; padding: 10px 20px; margin-bottom: 15px; width: 100%;">
	<i class="fa fa-lightbulb-o" style="font-size:18px;"></i>
	<div style="flex: 1; text-align: left;">
		<span style="font-weight: 600; color: #8B0000;">购前必看</span>
		<span style="font-size: 0.9em; color: #8B0000; font-weight: 600;">点我查看通用教程与解决方法</span>
	</div>
	<i class="fa fa-external-link" style="color: #8B0000;"></i>
</a>
<?php } ?>
	<div id="goodType" <?php if(isset($_GET['cid'])){?>style="display: none"<?php }?>>
<?php if(isset($conf['ui_shop']) && $conf['ui_shop']==1){?>
	<div class="row">
<?php foreach($primary_classes as $primary){
	if(!empty($primary["shopimg"])){
		$productimg = $primary["shopimg"];
	}else{
		$productimg = '/assets/img/Product/default.png';
	}
	if($usershop)$productimg='../'.$productimg;
	$count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$primary['cid']} and active=1");
	// 计算该一级分类及其二级分类下的商品总数
	$total_count = $count;
	if(isset($secondary_classes[$primary['cid']])){
		foreach($secondary_classes[$primary['cid']] as $secondary){
			$total_count += $DB->getColumn("SELECT count(*) from pre_tools where cid={$secondary['cid']} and active=1");
		}
	}
?>
		<div class="col-lg-4 col-xs-6">
			<div class="widget animation-fadeInQuick">
				<img class="lazy" width="100%" data-original="<?php echo $productimg?>">
				<div class="widget-content text-center">
					<strong><?php echo $primary["name"]?></strong>
					<p class="text-muted" style="margin-bottom:10px;text-align:center;">分类<?php echo $total_count?>个商品</p>
					<button type="button" data-id="<?php echo $primary["cid"]?>" class="btn btn-rounded btn-info btn-block primaryClassChange">点击进入</button>
					<?php if(isset($secondary_classes[$primary['cid']]) && count($secondary_classes[$primary['cid']]) > 0){?>
					<div style="margin-top:10px;">
						<strong style="font-size:12px;">二级分类：</strong><br>
						<?php foreach($secondary_classes[$primary['cid']] as $secondary){
							$sec_count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$secondary['cid']} and active=1");
							$sec_notice = isset($secondary['notice']) ? htmlspecialchars($secondary['notice']) : '';
						?>
						<button type="button" data-id="<?php echo $secondary["cid"]?>" data-notice="<?php echo $sec_notice?>" class="btn btn-xs btn-success btn-block goodTypeChange" style="margin:2px 0;"> <?php echo $secondary["name"]?> (<?php echo $sec_count?>)</button>
						<?php }?>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
<?php }?>
	<?php if(empty($primary_classes)){
		showEmptyMessage('暂无分类', '目前还没有添加任何商品分类，请稍后再来或联系管理员');
	}?>
	</div>
<?php }elseif(isset($conf['ui_shop']) && $conf['ui_shop']==2){?>
<style type="text/css">
	.table>tbody>tr>td{vertical-align: baseline;}
</style>
	<table class="table table-striped table-borderless table-vcenter table-hover">
         <tbody>
<?php foreach($primary_classes as $primary){
	if(!empty($primary["shopimg"])){
		$productimg = $primary["shopimg"];
	}else{
		$productimg = 'assets/img/Product/default.png';
	}
	if($usershop)$productimg='../'.$productimg;
	$count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$primary['cid']} and active=1");
	// 计算该一级分类及其二级分类下的商品总数
	$total_count = $count;
	if(isset($secondary_classes[$primary['cid']])){
		foreach($secondary_classes[$primary['cid']] as $secondary){
			$total_count += $DB->getColumn("SELECT count(*) from pre_tools where cid={$secondary['cid']} and active=1");
		}
	}
?>
		<tr class="widget animation-fadeInQuick">
                <td class="text-center" style="width: 100px;">
                    <img data-original="<?php echo $productimg?>" width="50" style="height:50px" alt="avatar" class="lazy img-circle img-thumbnail img-thumbnail-avatar">
                </td>
                <td>
                    <h3 class="widget-heading h4"><strong><?php echo $primary["name"]?></strong></h3>
			<span class="text-muted">分类<?php echo $total_count?>个商品</span>
			<?php if(isset($secondary_classes[$primary['cid']]) && count($secondary_classes[$primary['cid']]) > 0){?>
			<br>
			<div style="padding-left:20px;">
				<strong style="font-size:12px;">二级分类：</strong><br>
				<?php foreach($secondary_classes[$primary['cid']] as $secondary){
					$sec_count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$secondary['cid']} and active=1");
					$sec_notice = isset($secondary['notice']) ? htmlspecialchars($secondary['notice']) : '';
				?>
				<button type="button" data-id="<?php echo $secondary["cid"]?>" data-notice="<?php echo $sec_notice?>" class="btn btn-xs btn-success goodTypeChange" style="margin:2px;"> <?php echo $secondary["name"]?> (<?php echo $sec_count?>)</button>
				<?php }?>
			</div>
			<?php }?>
                </td>
                <td class="text-right">
                    <button type="button" data-id="<?php echo $primary["cid"]?>" class="btn btn-rounded btn-info primaryClassChange">点击进入</button>
                </td>
            </tr>
<?php }?>
	   </tbody>
        </table>
	<?php if(empty($primary_classes)){
		showEmptyMessage('暂无分类', '目前还没有添加任何商品分类，请稍后再来或联系管理员');
	}?>
<?php }elseif(isset($conf['ui_shop']) && $conf['ui_shop']==3){?>
	<div class="row">
<?php foreach($primary_classes as $primary){
	if(!empty($primary["shopimg"])){
		$productimg = $primary["shopimg"];
	}else{
		$productimg = 'assets/img/Product/default.png';
	}
	if($usershop)$productimg='../'.$productimg;
?>
		<div class="col-lg-3 col-xs-4" style="padding:0px">
		<div class="thumbnail" style="margin-bottom:3px;width:95%;margin: 2px auto;">
			<div class="widget animation-fadeInQuick">
			<center style="margin-top:0;">
				<img class="lazy" data-original="<?php echo $productimg?>" style="height: 88px;">
				<strong style="white-space:nowrap"><?php echo $primary["name"]?></strong>
				<button type="button" data-id="<?php echo $primary["cid"]?>" class="btn btn-sm btn-info btn-block primaryClassChange">一级分类</button>
				<?php if(isset($secondary_classes[$primary['cid']]) && count($secondary_classes[$primary['cid']]) > 0){?>
				<div style="margin-top:5px;">
					<?php foreach($secondary_classes[$primary['cid']] as $secondary){
						$sec_notice = isset($secondary['notice']) ? htmlspecialchars($secondary['notice']) : '';
					?>
					<button type="button" data-id="<?php echo $secondary["cid"]?>" data-notice="<?php echo $sec_notice?>" class="btn btn-xs btn-success btn-block goodTypeChange" style="margin:1px 0;"> <?php echo $secondary["name"]?></button>
					<?php }?>
				</div>
				<?php }?>
			</center>
			</div>
		</div>
		</div>
<?php }?>
	<?php if(empty($primary_classes)){
		showEmptyMessage('暂无分类', '目前还没有添加任何商品分类，请稍后再来或联系管理员');
	}?>
	</div>
<?php }?>
<script>
// 一级分类点击事件
$(document).on('click', '.primaryClassChange', function() {
    var cid = $(this).data('id');
    // 调用原有逻辑
    $(this).addClass('goodTypeChange').removeClass('primaryClassChange');
    $(this).trigger('click');
    $(this).removeClass('goodTypeChange').addClass('primaryClassChange');
});
</script>
	</div>
	<div id="goodTypeContent" <?php if(!isset($_GET['cid'])){?>style="display: none"<?php }?>>
		<div style="text-align: center;">
			<h3><span id="className"></span></h3>
			<img src="" id="classImg" width="50%" >
		</div>
		<br>
		<input type="hidden" name="cid" id="cid" value="0"/>
		<?php if(isset($_GET['cid'])){
			$current_cid = intval($_GET['cid']);
			$current_class = $DB->getRow("SELECT * FROM pre_class WHERE cid='$current_cid'");
			if($current_class && $current_class['pid'] > 0){
				// 当前是二级分类，获取其父分类
				$parent_class = $DB->getRow("SELECT * FROM pre_class WHERE cid='{$current_class['pid']}'");
			}
		}?>
		<div class="form-group">
				<div class="input-group"><div class="input-group-addon">选择商品</div>
				<select name="tid" id="tid" class="form-control" onchange="getPoint();"><option value="0">请选择商品</option></select>
		</div>
			<div id="toolimg_view" style="display:none;margin-top:6px;text-align:center;padding:10px;width:250px;height:250px;box-sizing:border-box;border:1.5px dashed #e2e8f0;border-radius:8px;"><img src="" alt="" style="width:100%;height:100%;object-fit:contain;border-radius:8px;"></div>
		</div>
		<?php if($show_warning_div == '1'){ ?><div class="alert alert-danger" style="border-left: 5px solid #a94442;font-weight: bold;background-color:#f2dede;color:#a94442;"><i class="fa fa-exclamation-triangle"></i> 下单信息：请认真填写，不要填写的过于简单，否则会被不法之人窃取卡密！</div><?php } ?>
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
			<a type="submit" id="submit_buy" class="btn btn-danger btn-block btn-rounded" style="background: linear-gradient(to right, #FFCCCB, #8B0000); color: #fff; border: none;">立即购买</a>
		</div>
		<?php }else{?>
		<div class="form-group">
			<input type="submit" id="submit_buy" class="btn btn-danger btn-block btn-rounded" style="background: linear-gradient(to right, #FFCCCB, #8B0000); color: #fff; border: none;" value="立即购买">
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
?>
<!-- 警告信息 -->
<?php if($show_order_warning == '1'){ ?>
<div id="orderWarning" style="background-color: rgb(51, 51, 51); border-radius: 25px; box-shadow: rgb(242, 0, 255) 0px 0px 5px; padding: 5px; margin-top: 10px; margin-bottom: 5px; display: block;">
						<center>
							<span style="color:rgb(194, 79, 74)">
								
								<b>
									<font color="#D2B48C">【先确认能（下载/打开/使用）再下单】后果自负</font>
									
								</b>
							</span>
							<br>
							<span style="font-size:10px">
								<strong>
									<span>
										<span style="color:#E53333;">下单步骤</span>
										<span style="color:#E53333;"> &gt; </span>
										<span style="color:#E8E8E8;">选择分类</span>
										<span style="color:#E53333;"> &gt; </span>
										<span style="color:#009900;">选择商品</span>
										<span style="color:#E53333;"> &gt; </span>
										<span style="color:#EE33EE;">填写信息</span>
										<span style="color:#E53333;"> &gt; </span>
										<span style="color:#F08080;">下单成功</span>
									</span>
								</strong>
							</span>
						</center>
					</div>
<?php } ?>
<!-- 购前必看按钮 -->
<?php if($show_guide_link == '1'){ ?>
<a class="btn custom-btn" href="./template/XHY-01/content.html" target="_blank" role="button" style="display: flex; align-items: center; justify-content: space-between; background: linear-gradient(to right, #ffffff, #ffcccc); border: 2px solid #8B0000; border-radius: 25px; padding: 10px 20px; margin-bottom: 15px; width: 100%;">
	<i class="fa fa-lightbulb-o" style="font-size:18px;"></i>
	<div style="flex: 1; text-align: left;">
		<span style="font-weight: 600; color: #8B0000;">购前必看</span>
		<span style="font-size: 0.9em; color: #8B0000; font-weight: 600;">点我查看通用教程与解决方法</span>
	</div>
	<i class="fa fa-external-link" style="color: #8B0000;"></i>
</a>
<?php } ?>
<?php
// 查询一级分类
$hideclass = false;
$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 AND pid=0 order by sort asc");
$primary_select='<option value="0">请选择一级分类</option>';
$primary_count=0;
while($res = $rs->fetch()){
	if($is_fenzhan && in_array($res['cid'], $classhide))continue;
	$primary_count++;
	$primary_select.='<option value="'.$res['cid'].'" shopimg="'.htmlspecialchars($res['shopimg']??'').'">'.$res['name'].'</option>';
}
if($primary_count==0)$hideclass = true;
?>
		<div id="goodTypeContents">
			<?php echo isset($conf['alert']) ? $conf['alert'] : '';?>
			<?php if(isset($conf['search_open']) && $conf['search_open']==1){?>
			<div class="form-group" id="display_searchBar">
				<div class="input-group"><div class="input-group-addon">搜索商品</div>
				<input type="text" id="searchkw" class="form-control" placeholder="搜索商品" onkeydown="if(event.keyCode==13){$('#doSearch').click()}"/>
				<div class="input-group-addon"><span class="glyphicon glyphicon-search onclick" title="搜索" id="doSearch"></span></div>
			</div></div>
			<?php }?>
			<?php if($hideclass){?>
				<?php showEmptyMessage('暂无分类', '目前还没有添加任何商品分类，请稍后再来或联系管理员');?>
			<?php }else{?>
			<div class="form-group" id="display_selectclass">
				<div class="input-group"><div class="input-group-addon">一级分类</div>
				<select name="tid" id="cid" class="form-control" onchange="changePrimaryCategory(this.value)"><?php echo $primary_select?></select>
			</div>
				<div id="cid_img" class="sel-img-preview" style="display:none;margin-top:6px;width:250px;height:250px;overflow:hidden;border-radius:8px;"><img src="" alt="" style="width:100%;height:100%;object-fit:contain;border-radius:8px;"></div>
			</div>
			<div class="form-group" id="display_selectsubclass">
				<div class="input-group"><div class="input-group-addon">二级分类</div>
				<select name="subcid" id="subcid" class="form-control" onchange="changeSecondaryCategory(this.value)"><option value="0">请选择二级分类</option></select>
			</div>
				<div id="subcid_img" class="sel-img-preview" style="display:none;margin-top:6px;width:250px;height:250px;overflow:hidden;border-radius:8px;"><img src="" alt="" style="width:100%;height:100%;object-fit:contain;border-radius:8px;"></div>
			</div>
			<div class="form-group">
				<div class="input-group"><div class="input-group-addon">选择商品</div>
				<select name="tid" id="tid" class="form-control" onchange="getPoint();"><option value="0">请选择商品</option></select>
			</div>
				<div id="toolimg_view" style="display:none;margin-top:6px;text-align:center;padding:10px;width:250px;height:250px;box-sizing:border-box;border:1.5px dashed #e2e8f0;border-radius:8px;"><img src="" alt="" style="width:100%;height:100%;object-fit:contain;border-radius:8px;"></div>
			</div>
			<?php }?>
			<?php if($show_warning_div == '1'){ ?><div class="alert alert-danger" style="border-left: 5px solid #a94442;font-weight: bold;background-color:#f2dede;color:#a94442;"><i class="fa fa-exclamation-triangle"></i> 下单信息：请认真填写，不要填写的过于简单，否则会被不法之人窃取卡密！</div><?php } ?>
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
                <span class="input-group-btn"><input id="num_min" type="button" class="btn btn-info" style="border-radius: 0px;" value="━"></span>
				<input id="num" name="num" class="form-control" type="number" min="1" value="1"/>
				<span class="input-group-btn"><input id="num_add" type="button" class="btn btn-info" style="border-radius: 0px;" value="✚"></span>
			</div></div>
			<div id="inputsname"></div>
			<div id="alert_frame" class="alert alert-success animated rubberBand" style="display:none;background: linear-gradient(to right,#71D7A2,#5ED1D7);font-weight: bold;color:white;"></div>
			<?php if(isset($conf['shoppingcart']) && $conf['shoppingcart']==1){?>
			<div class="btn-group btn-group-justified form-group">
				<a type="submit" id="submit_buy" class="btn btn-danger btn-block btn-rounded" style="background: linear-gradient(to right, #FFCCCB, #8B0000); color: #fff; border: none;">立即购买</a>
			</div>
			<?php }else{?>
			<div class="form-group">
				<input type="submit" id="submit_buy" class="btn btn-danger btn-block btn-rounded" style="background: linear-gradient(to right, #FFCCCB, #8B0000); color: #fff; border: none;" value="立即购买">
			</div>
			<?php }?>
			<div class="panel-body border-t" id="alert_cart" style="display:none;"><i class="fa fa-shopping-cart"></i>&nbsp;当前购物车已添加<b id="cart_count">0</b>个商品<a class="btn btn-xs btn-danger pull-right" href="javascript:openCart()">购物车列表</a></div>
		</div>
<?php } ?>
<script>
// 一级分类变化时的处理函数
function changePrimaryCategory(cid) {
    // 显示所选一级分类图片
    var o = $("#cid option:selected").attr('shopimg');
    if (o) { $('#cid_img').css('display','block').find('img').attr('src', o); }
    else { $('#cid_img').hide(); }
    if (cid == 0) {
        $('#display_selectsubclass').hide();
        $('#subcid').html('<option value="0">请选择二级分类</option>');
        $('#tid').html('<option value="0">请选择商品</option>');
        return;
    }
    
    // 加载二级分类
    $.ajax({
        url: './ajax.php?act=getsubclass',
        type: 'GET',
        data: { cid: cid },
        dataType: 'json',
        success: function(data) {
            if (data.code == 0) {
                $('#subcid').html(data.html);
                // 保存二级分类提示语数据 - 教主修改，博客地址：6v6.ren Q群：941535592
                window.subClassNotices = data.notices || {};
                // 检查是否有实际的二级分类（至少有一个非默认选项）
                var optionCount = $('#subcid option').length;
                if (optionCount > 1) {
                    // 有二级分类，显示选择框
                    $('#display_selectsubclass').show();
                } else {
                    // 没有二级分类，隐藏选择框
                    $('#display_selectsubclass').hide();
                }
            } else {
                // 加载失败，隐藏二级分类选择框
                $('#display_selectsubclass').hide();
                $('#subcid').html('<option value="0">暂无二级分类</option>');
            }
            // 加载一级分类下的商品
            loadGoodsByCategory(cid);
        },
        error: function(xhr, status, error) {
            console.error('加载二级分类失败:', error);
            // 加载失败，隐藏二级分类选择框
            $('#display_selectsubclass').hide();
            $('#subcid').html('<option value="0">暂无二级分类</option>');
            // 加载一级分类下的商品
            loadGoodsByCategory(cid);
        }
    });
    
    // 清空商品列表
    $('#tid').html('<option value="0">请选择商品</option>');
}

// 二级分类变化时的处理函数 - 教主修改，博客地址：6v6.ren Q群：941535592
function changeSecondaryCategory(subcid) {
    var cid = $('#cid').val();
    // 显示所选二级分类图片
    var o = $("#subcid option:selected").attr('shopimg');
    if (o) { $('#subcid_img').css('display','block').find('img').attr('src', o); }
    else { $('#subcid_img').hide(); }
    
    // 使用选择的分类ID（二级优先）
    var categoryId = subcid > 0 ? subcid : cid;
    
    // 检查是否有提示语并显示弹窗
    if (subcid > 0 && window.subClassNotices && window.subClassNotices[subcid]) {
        layer.alert(window.subClassNotices[subcid], {
            title: '温馨提示',
            icon: 0,
            btn: ['我知道了']
        });
    }
    
    // 加载商品
    loadGoodsByCategory(categoryId);
}

// 根据分类ID加载商品
function loadGoodsByCategory(categoryId) {
    if (categoryId == 0) {
        $('#tid').html('<option value="0">请选择商品</option>');
        return;
    }
    
    $('#tid').html('<option value="0">加载中...</option>');
    
    $.ajax({
        url: './ajax.php?act=gettool',
        type: 'GET',
        data: { cid: categoryId },
        dataType: 'json',
        success: function(data) {
            if (data.code == 0) {
                var html = '<option value="0">请选择商品</option>';
                if (data.data.length > 0) {
                    for (var i = 0; i < data.data.length; i++) {
                        var res = data.data[i];
                        html += '<option value="' + res.tid + '" cid="' + res.cid + '" shopimg="' + (res.shopimg||'') + '" price="' + res.price + '" desc="' + escape(res.desc) + '" alert="' + escape(res.alert) + '" inputname="' + res.input + '" inputsname="' + res.inputs + '" multi="' + res.multi + '" isfaka="' + res.isfaka + '" count="' + res.value + '" close="' + res.close + '" prices="' + res.prices + '" max="' + res.max + '" min="' + res.min + '" stock="' + res.stock + '">' + res.name + '</option>';
                    }
                } else {
                    html = '<option value="0">该分类下暂无商品</option>';
                }
                $('#tid').html(html);
            } else {
                $('#tid').html('<option value="0">加载失败</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('加载商品失败:', error);
            $('#tid').html('<option value="0">加载失败</option>');
        }
    });
}

// 页面加载完成后初始化
$(document).ready(function() {
    // 初始隐藏二级分类选择框
    // 当用户选择一级分类后会自动显示
});
</script>