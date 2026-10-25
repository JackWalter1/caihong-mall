<?php

include "../includes/common.php";
$title = "批量对接商品";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}?><div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <?php 
adminpermission("shop", 1);
$act = isset($_GET["act"]) ? $_GET["act"] : null;
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$classselect = "<option value=\"0\">未分类</option>";
while ($res = $rs->fetch()) {
	$classselect .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}

// 获取所有支持批量对接的站点，包括玖伍、亿乐、直客等
$rs = $DB->query("SELECT * FROM pre_shequ order by id asc");
$shequselect = "";
while ($res = $rs->fetch()) {
    // 获取插件配置
	$getInfo = \lib\Plugin::getConfig("third_" . $res["type"]);
    
    // 支持标准batchgoods插件和传统的玖伍、亿乐、直客系统
    $isBatchGoodsSupported = false;
    if (isset($getInfo["batchgoods"]) && $getInfo["batchgoods"] == true) {
        $isBatchGoodsSupported = true;
    } elseif (in_array($res["type"], ['jiuwu', 'yile', 'zhike', 'shangzhanwl'])) {
        $isBatchGoodsSupported = true;
    }
    
    if ($isBatchGoodsSupported) {
        // 为传统系统设置标题
        $titleText = $getInfo["title"] ?? "";
        if (in_array($res["type"], ['jiuwu', 'yile', 'zhike', 'shangzhanwl'])) {
            switch ($res["type"]) {
                case 'jiuwu':
                    $titleText = '玖伍';
                    break;
                case 'yile':
                    $titleText = '亿乐';
                    break;
                case 'zhike':
                    $titleText = '直客';
                    break;
                case 'shangzhanwl':
                    $titleText = '商战网';
                    break;
                default:
                    $titleText = '未知';
            }
        }
        
        $shequselect .= "<option value=\"" . $res["id"] . "\" type=\"" . $res["type"] . "\" domain=\"" . $res["url"] . "\">[<font color=blue>" . $titleText . "</font>] " . $res["url"] . ($res["remark"] ? " (" . $res["remark"] . ")" : "") . "</option>";
    }
}

$rs = $DB->query("SELECT * FROM pre_price order by id asc");
$priceselect = "<option value=\"0\">不使用加价模板</option>";
while ($res = $rs->fetch()) {
	$kind = $res["kind"] == 1 ? "元" : "倍";
	$priceselect .= "<option value=\"" . $res["id"] . "\" kind=\"" . $res["kind"] . "\" p_2=\"" . $res["p_2"] . "\" p_1=\"" . $res["p_1"] . "\" p_0=\"" . $res["p_0"] . "\" >" . $res["name"] . "(" . $res["p_2"] . $kind . "|" . $res["p_1"] . $kind . "|" . $res["p_0"] . $kind . ")</option>";
}
if ($act == "data") {
	$shequ = intval($_GET["shequ"]);
	$row = $DB->getRow("select * from pre_shequ where id=:id limit 1", array(":id" => $shequ));
    
    // 处理不同类型的站点
    $isTraditionalSystem = in_array($row["type"], ['jiuwu', 'yile', 'zhike', 'shangzhanwl']);
    
    // 获取商品分类列表
    $third_classlist = "";
    if (!$isTraditionalSystem) {
        // 标准插件系统
        $result = third_call($row["type"], $row, "class_list");
        foreach ($result as $res) {
            $third_classlist .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
        }
    } else {
        // 传统系统，不显示分类选择或显示特殊分类
        $third_classlist .= "<option value=\"-1\">--所有商品--</option>";
    }
	?>	<div class="block">
            <div class="block-title"><h3 class="panel-title">批量对接商品</h3></div>
            <div class="">
                <form action="?" role="form">
                    <input type="hidden" name="shequ" value="<?php echo $shequ;?>"/>
                    <input type="hidden" name="type" value="<?php echo $row["type"];?>"/>
                    <div class="form-group">
                        <div class="input-group"><div class="input-group-addon">当前对接站点</div>
                            <input class="form-control" value="<?php echo $row["url"];?>" disabled><span class="input-group-btn"><a href="./batchgoods.php" class="btn btn-default">重新选择</a></span>
                        </div></div>
                    
                    <!-- 只对非传统系统显示分类选择 -->
                    <?php if (!$isTraditionalSystem): ?>
                    <div class="form-group">
                        <div class="input-group"><div class="input-group-addon">选择对接站点商品分类</div>
                            <select class="form-control" id="cid" multiple="multiple" size="10"><option value="-1">--请选择分类（可多选）--</option><?php echo $third_classlist;?></select>
                        </div></div>
                        <p class="help-block">提示：按住Ctrl或Shift键可以多选分类</p>
                    <?php else: ?>
                    <!-- 传统系统的特殊配置 -->
                    <div class="form-group">
                        <div class="input-group"><div class="input-group-addon">获取方式</div>
                            <select class="form-control" id="is">
                                <option value="0">获取 社区 所有的商品</option>
                                <option value="1">只获取 不存在本系统 的社区商品</option>
                            </select>
                        </div></div>
                    
                    <!-- 传统系统添加商品分类选择 -->
                    <div class="form-group" id="categoryGroup">
                        <div class="input-group"><div class="input-group-addon">选择对接站点商品分类</div>
                            <select class="form-control" id="category_id" multiple="multiple" size="10">
                                <option value="-1">--请选择分类（可多选）--</option>
                            </select>
                        </div></div>
                        <p class="help-block">提示：按住Ctrl或Shift键可以多选分类</p>
                        
                        <!-- 商战网特殊配置 -->
                        <?php if ($row["type"] == 'shangzhanwl'): ?>
                        <div class="form-group" id="showcid">
                            <div class="input-group"><div class="input-group-addon">商品目录</div>
                                <input type="text" class="form-control" id="goodscid" placeholder="输入 全部 就获取全部">
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <table class="table table-bordered table-vcenter table-hover" id="shoptable">
                        <tbody id="shoplist">
                        </tbody>
                    </table>
                    <div class="form-group">
                        <div class="input-group"><div class="input-group-addon">选择保存到本站的分类</div>
                            <select class="form-control" id="mcid"><option value="-1">--请选择分类--</option><option value="new">新建同名分类</option><?php echo $classselect;?></select>
                        </div></div>
                        <div class="form-group" id="parentClassGroup" style="display: none;">
                            <div class="input-group"><div class="input-group-addon">选择父级分类</div>
                                <select class="form-control" id="parent_cid"><option value="0">作为一级分类</option><?php echo $classselect;?></select>
                            </div></div>
                    <div class="form-group">
                        <div class="input-group"><div class="input-group-addon">选择使用的加价模板</div>
                            <select class="form-control" id="prid"><option value="-1">--请选择加价模板--</option><?php echo $priceselect;?></select><span class="input-group-btn"><a href="./price.php" class="btn btn-default">加价模板管理</a></span>
                        </div></div>
                    <!-- 传统系统添加获取商品按钮 -->
                    <?php if ($isTraditionalSystem): ?>
                    <div class="form-group">
                        <button type="button" onclick="getgoods()" class="btn btn-primary btn-block">获取商品列表</button>
                    </div>
                    <?php endif; ?>
                    
                    <p><input type="button" name="submit" value="确定添加/更新选中商品" class="btn btn-primary btn-block" id="add_submit"/></p>
                </form>
            </div>
        </div>
<?php 
} else {
	?>    <div class="block">
        <div class="block-title"><h3 class="panel-title">批量对接商品</h3></div>
        <div class="">
            <div class="alert alert-info">
                支持所有对接系统，包括玖伍、亿乐、直客等传统系统和标准插件系统。使用此功能可以快速添加/更新本站对接的商品。
            </div>
            <form action="?" method="GET" role="form">
                <input type="hidden" name="act" value="data"/>
                <div class="form-group">
                    <div class="input-group"><div class="input-group-addon">选择对接站点</div>
                        <select class="form-control" name="shequ"><?php echo $shequselect;?></select>
                    </div></div>
                <p><input type="submit" name="submit" value="获取商品分类" class="btn btn-primary btn-block"/></p>
            </form>
        </div>
    </div>
    <?php 
}?>	<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
    <script>
        function SelectAll(chkAll) {
            var items = $('.shop');
            for (i = 0; i < items.length; i++) {
                if (items[i].id.indexOf("tid") != -1 && items[i].type == "checkbox") {
                    items[i].checked = chkAll.checked;
                }
            }
        }
        
        // 获取传统系统商品列表
        function getgoods() {
            var shequ = $("input[name='shequ']").val();
            var type = $("input[name='type']").val();
            var goodscid = $("#goodscid").val() || '';
            var is = $("#is").val() || 0;
            
            // 获取选中的分类ID（支持多选）
            var category_ids = [];
            $("#category_id option:selected").each(function() {
                if($(this).val() != '-1') {
                    category_ids.push($(this).val());
                }
            });
            var category_id = category_ids.join(',');
            
            // 验证商战网商品目录
            if (type == 'shangzhanwl' && goodscid == '') {
                layer.msg('请输入商品目录', {icon: 5});
                return false;
            }
            
            var ii = layer.load(2, {shade:[0.1,'#fff']});
            shoplist = new Array();
            $("#shoplist").empty();
            $("#shoplist").append('<tr><td><label class="csscheckbox csscheckbox-primary">全选<input type="checkbox" onclick="SelectAll(this)"><span></span></label>&nbsp;商品名称</td><td>成本价</td><td>计算后的价钱</td><td>最少下单数量</td><td>最大下单数量</td><td style="color:red">默认下单数量</td><td>状态</td><td>对接状态</td><td>上架时间</td></tr>');
            
            $.ajax({
                type : "GET",
                url : "ajax_batch_docking.php?act=getDockedTids&shequ=" + shequ,
                dataType : 'json',
                async: false,
                success : function(data) {
                    if(data.code == 0 && data.data) {
                        window.dockedTids = data.data;
                    } else {
                        window.dockedTids = {};
                    }
                },
                error: function() {
                    window.dockedTids = {};
                }
            });
            
            $.ajax({
                type : "POST",
                url : "./ajax_batch_docking.php?act=getGoodsList",
                data : {shequ:shequ, is:is, type:type, goodscid:goodscid, category_id:category_id},
                dataType : 'json',
                success : function(data) {
                    layer.close(ii);
                    if(data.code == 0) {
                        var num = 0;
                        $.each(data.data, function (i, item) {
                            // 调试：记录原始数据
                            console.log('Yile Item Data:', item);
                            
                            // 设置默认值，处理不同系统返回数据结构不一致的问题
                            var id = item.id || item.gid || 0;
                            var name = item.name || item.title || '未知商品';
                            
                            // 获取原始分类名称
                            var originalCategoryName = item.original_cname || '';
                            if(!originalCategoryName && item.category_name) {
                                originalCategoryName = item.category_name;
                            }
                            if(!originalCategoryName && item.cname) {
                                originalCategoryName = item.cname;
                            }
                            
                            // 修复价格处理：优先使用多个可能的价格字段，如果都为null或undefined则使用0
                            var price = 0;
                            var priceFields = ['price', 'user_unitprice', 'goodsPrice', 'unitPrice'];
                            for(var i = 0; i < priceFields.length; i++) {
                                var field = priceFields[i];
                                if(item[field] !== null && item[field] !== undefined && !isNaN(parseFloat(item[field])) && parseFloat(item[field]) > 0){
                                    price = parseFloat(item[field]);
                                    break;
                                }
                            }
                            
                            // 固定最少下单数量为1
                            var minnum = 1;
                            
                            // 修复最大下单数量处理
                            var maxnum = 0;
                            if(item.maxnum !== null && item.maxnum !== undefined && !isNaN(parseInt(item.maxnum))){
                                maxnum = parseInt(item.maxnum);
                            } else if(item.maxbuynum_0 !== null && item.maxbuynum_0 !== undefined && !isNaN(parseInt(item.maxbuynum_0))){
                                maxnum = parseInt(item.maxbuynum_0);
                            } else if(item.limit_max !== null && item.limit_max !== undefined && !isNaN(parseInt(item.limit_max))){
                                maxnum = parseInt(item.limit_max);
                            }
                            
                            var close = parseInt(item.close || item.goods_status || 0);
                            var shopimg = item.shopimg || item.thumb || '';
                            
                            // 调试：记录处理后的数据
                            console.log('Processed Data:', {id, name, price, minnum, maxnum, close, shopimg, value: item.value});
                            
                            // 优先使用对接站的默认数量信息，尝试多个可能的字段名
                            var defaultNum = 1;
                            // 尝试从多个可能的字段中获取默认数量
                            var valueFields = ['value', 'default_num', 'def_num', 'defaultnum', 'defnum', 'minnum', 'minbuynum_0'];
                            for(var i = 0; i < valueFields.length; i++) {
                                var field = valueFields[i];
                                if(item[field] !== null && item[field] !== undefined && !isNaN(parseInt(item[field])) && parseInt(item[field]) > 0){
                                    defaultNum = parseInt(item[field]);
                                    break;
                                }
                            }
                            // 如果所有字段都无效，使用500作为默认值
                            if(defaultNum <= 0) {
                                defaultNum = 500;
                            }
                            
                            // 计算成本价，确保不会出现NaN
                            var cost = isNaN(price * defaultNum) ? 0 : getFloat(price * defaultNum, 2);
                            
                            // 如果计算后的价钱为0，则自动增加默认下单数量
                            var maxIterations = 5; // 防止无限循环的最大迭代次数
                            var iterations = 0;
                            while (cost <= 0 && iterations < maxIterations) {
                                defaultNum *= 10;
                                cost = isNaN(price * defaultNum) ? 0 : getFloat(price * defaultNum, 2);
                                iterations++;
                            }
                            
                            // 保存到shoplist数组
                            var shopItem = {
                                id: id,
                                name: name,
                                price: price,
                                minnum: minnum,
                                maxnum: maxnum,
                                close: close,
                                shopimg: shopimg,
                                type: type,
                                value: item.value || defaultNum,
                                original_cname: originalCategoryName // 添加原始分类名称
                            };
                            shoplist[id] = JSON.stringify(shopItem);
                            
                            // 渲染表格行
                            var isDocked = window.dockedTids.hasOwnProperty(id);
var dockedTime = isDocked ? window.dockedTids[id] : '-';
$("#shoplist").append('<tr><td><label class="csscheckbox csscheckbox-primary"><input name="tid[]" type="checkbox" class="shop" id="tid" value="'+id+'"><span></span>&nbsp;'+name+'<label></label></label></td><td><span id="price'+id+'">'+price+'</span></td><td><span id="cost'+id+'">'+cost+'</span></td><td>'+minnum+'</td><td>'+(maxnum > 0 ? maxnum : '无限制')+'</td><td><input type="text" class="form-control input-sm" style="width:60px;" id="defaultnum'+id+'" value="'+defaultNum+'" onkeyup="changeNum('+id+')" required=""></td><td>'+(close==1?'<span class="label label-warning">已下架</span>':'<span class="label label-success">上架中</span>')+'</td><td>'+(isDocked ? '<span class="label label-success">已对接</span>' : '<span class="label label-default">未对接</span>')+'</td><td>'+dockedTime+'</td></tr>');
                            num++;
                        });
                        if(num==0) layer.msg('该分类下没有商品', {icon:0, time:800});
                    } else {
                        layer.alert(data.msg, {icon:2});
                    }
                },
                error:function(data){
                    layer.close(ii);
                    layer.alert('请求出错'+JSON.stringify(data));
                }
            });
        }
        
        // 价格计算函数
        function getFloat(number, n) {
            n = n ? parseInt(n) : 0;
            if (n <= 0) return Math.ceil(number);
            number = Math.round(number * Math.pow(10, n)) / Math.pow(10, n);
            return number;
        }
        
        // 修改默认下单数量时重新计算价格
        function changeNum(id) {
            var price = parseFloat($("#price"+id).html());
            var num = parseInt($("#defaultnum"+id).val());
            
            // 确保价格和数量都是有效数字
            if (isNaN(price) || price <= 0) price = 9999;
            if (isNaN(num) || num <= 0) num = 1;
            
            var cost = getFloat(price * num, 2);
            $("#cost"+id).html(cost);
            
            // 确保默认下单数量至少为1
            if (num <= 0) {
                $("#defaultnum"+id).val(1);
            }
        }
        
        var shoplist;
        $(document).ready(function(){
            // 监听分类选择变化，显示/隐藏父级分类选择
            $("#mcid").change(function() {
                if($(this).val() == 'new') {
                    $("#parentClassGroup").show();
                } else {
                    $("#parentClassGroup").hide();
                }
            });
            
            // 传统系统：页面加载时获取商品分类列表
            var type = $("input[name='type']").val();
            var shequ = $("input[name='shequ']").val();
            if (in_array(type, ['jiuwu', 'yile', 'zhike', 'shangzhanwl'])) {
                $.ajax({
                    type : "POST",
                    url : "./ajax_batch_docking.php?act=getCategoryList",
                    data : {shequ:shequ},
                    dataType : 'json',
                    success : function(data) {
                        if(data.code == 0) {
                            var categorySelect = $("#category_id");
                            categorySelect.empty();
                            categorySelect.append('<option value="-1">--请选择分类（可多选）--</option>');
                            $.each(data.data, function (i, item) {
                                categorySelect.append('<option value="' + item.cid + '">' + item.name + '</option>');
                            });
                            // 显示分类选择框
                            $("#categoryGroup").show();
                        } else {
                            layer.msg('获取分类失败：' + data.msg, {icon: 2});
                        }
                    },
                    error:function(data) {
                        layer.msg('获取分类失败，请刷新重试', {icon: 2});
                    }
                });
            }
            
            $("#add_submit").click(function () {
                var shequ = $("input[name='shequ']").val();
                var type = $("input[name='type']").val();
                var mcid = $("#mcid").val();
                var parent_cid = $("#parent_cid").val();
                var prid = $("#prid").val();
                
                if(mcid == -1){
                    layer.alert('请选择保存到本站的分类');return false;
                }
                if(prid == -1){
                    layer.alert('请选择使用的加价模板');return false;
                }
                
                var newshoplist = new Array();
                var defaultnum = new Array();
                var items = $('.shop');
                for (i = 0; i < items.length; i++) {
                    if (items[i].id.indexOf("tid") != -1 && items[i].type == "checkbox" && items[i].checked == true) {
                        var tid = items[i].value;
                        newshoplist.push(shoplist[tid]);
                        
                        // 传统系统需要默认下单数量
                        if (typeof $("#defaultnum"+items[i].value) != 'undefined') {
                            var num = $("#defaultnum"+items[i].value).val();
                            defaultnum.push('{"id":"'+items[i].value+'","value":"'+num+'"}');
                        }
                    }
                }
                if(newshoplist.length <= 0){
                    layer.alert('请至少选中一个商品');return false;
                }
                
                var ii = layer.load(2, {shade:[0.1,'#fff']});
                
                // 根据站点类型选择不同的AJAX URL
                var ajaxUrl = '';
                var ajaxData = {};
                
                if (in_array(type, ['jiuwu', 'yile', 'zhike', 'shangzhanwl'])) {
                    // 传统系统
                    ajaxUrl = "ajax_batch_docking.php?act=batchaddgoods";
                    ajaxData = {shequ:shequ, mcid:mcid, parent_cid:parent_cid, prid:prid, list:newshoplist, numlist:defaultnum};
                } else {
                    // 标准插件系统
                    ajaxUrl = "ajax_shop.php?act=batchaddgoods";
                    // 获取所有选中的分类名称
                    var cnames = [];
                    $("#cid option:selected").each(function() {
                        if($(this).val() != -1) {
                            cnames.push($(this).text());
                        }
                    });
                    ajaxData = {shequ:shequ, mcid:mcid, parent_cid:parent_cid, prid:prid, list:newshoplist, cname:cnames.join(', '), cimg:$("#cid option:selected:first").attr('data-shopimg')};
                }
                
                $.ajax({
                    type : "POST",
                    url : ajaxUrl,
                    dataType : 'json',
                    data : ajaxData,
                    success : function(data) {
                        layer.close(ii);
                        console.log('AJAX Success:', data);
                        if(data.code == 0){
                            layer.alert(data.msg, {icon:1}, function(){window.location.reload()});
                        }else{
                            layer.alert(data.msg, {icon:2});
                        }
                    },
                    error:function(xhr, status, error){
                        layer.close(ii);
                        console.log('AJAX Error:', xhr.status, status, error);
                        console.log('Response Text:', xhr.responseText);
                        layer.msg('加载失败，请刷新重试');
                        return false;
                    }
                });
            });
            
            // 非传统系统监听分类选择变化
            $("#cid").change(function () {
                var cids = $(this).val();
                var shequ = $("input[name='shequ']").val();
                var type = $("input[name='type']").val();
                
                // 传统系统不处理此事件
                if (in_array(type, ['jiuwu', 'yile', 'zhike', 'shangzhanwl'])) return;
                
                if(!cids || cids.length == 0 || (cids.length == 1 && cids[0] == -1))return;
                
                // 过滤掉-1值
                var validCids = $.grep(cids, function(cid) {
                    return cid != -1;
                });
                if(validCids.length == 0)return;
                
                var ii = layer.load(2, {shade:[0.1,'#fff']});
                shoplist = new Array();
                $("#shoplist").empty();
                $("#shoplist").append('<tr><td><label class="csscheckbox csscheckbox-primary">全选<input type="checkbox" onclick="SelectAll(this)"><span></span></label>&nbsp;ID</td><td>商品名称</td><td>成本价</td><td>状态</td><td>对接状态</td><td>上架时间</td></tr>');
                
                $.ajax({
                    type : "GET",
                    url : "ajax_batch_docking.php?act=getDockedTids&shequ=" + shequ,
                    dataType : 'json',
                    async: false,
                    success : function(data) {
                        if(data.code == 0 && data.data) {
                            window.dockedTids = data.data;
                        } else {
                            window.dockedTids = {};
                        }
                    },
                    error: function() {
                        window.dockedTids = {};
                    }
                });
                
                // 为每个选中的分类加载商品
                var loadedCids = 0;
                var totalItems = 0;
                var errorMessages = [];
                
                $.each(validCids, function(index, cid) {
                    $.ajax({
                        type : "POST",
                        url : "ajax_shop.php?act=goodslistbycid",
                        dataType : 'json',
                        data : {shequ:shequ, cid:cid},
                        success : function(data) {
                            loadedCids++;
                            if(data.code == 0){
                                // 为商品添加原始分类信息
                                var originalCid = cid;
                                var originalCname = $("#cid option[value='"+cid+"']").text();
                                
                                $.each(data.data, function (i, item) {
                                    // 添加原始分类信息到商品对象
                                    item.original_cid = originalCid;
                                    item.original_cname = originalCname;
                                    
                                    shoplist[item.tid] = JSON.stringify(item);
                                    var isDocked = window.dockedTids.hasOwnProperty(item.tid);
var dockedTime = isDocked ? window.dockedTids[item.tid] : '-';
$("#shoplist").append('<tr><td><label class="csscheckbox csscheckbox-primary"><input name="tid[]" type="checkbox" class="shop" id="tid" value="'+item.tid+'"><span></span>&nbsp;'+item.tid+'<label></label></label></td><td>'+item.name+'</td><td>'+item.price+'</td><td>'+(item.close==1?'<span class="label label-warning">已下架</span>':'<span class="label label-success">上架中</span>')+'</td><td>'+(isDocked ? '<span class="label label-success">已对接</span>' : '<span class="label label-default">未对接</span>')+'</td><td>'+dockedTime+'</td></tr>');
                                    totalItems++;
                                });
                            } else {
                                errorMessages.push('分类 ' + $("#cid option[value='"+cid+"']").text() + ' 加载失败: ' + data.msg);
                            }
                            
                            // 所有分类都加载完成
                            if(loadedCids == validCids.length) {
                                layer.close(ii);
                                if(totalItems == 0) {
                                    if(errorMessages.length > 0) {
                                        layer.alert('加载商品失败:\n' + errorMessages.join('\n'), {icon:2});
                                    } else {
                                        layer.msg('选中的分类下没有商品', {icon:0, time:800});
                                    }
                                } else if(errorMessages.length > 0) {
                                    layer.alert('部分分类加载失败:\n' + errorMessages.join('\n'), {icon:2});
                                }
                            }
                        },
                        error:function(xhr, status, error) {
                            loadedCids++;
                            errorMessages.push('分类 ' + $("#cid option[value='"+cid+"']").text() + ' 加载失败: ' + status + ' ' + error);
                            console.log('AJAX Error:', xhr.status, status, error);
                            console.log('Response Text:', xhr.responseText);
                            
                            // 所有分类都加载完成
                            if(loadedCids == validCids.length) {
                                layer.close(ii);
                                if(totalItems == 0) {
                                    layer.alert('加载商品失败，请检查网络连接或API配置', {icon:2});
                                } else {
                                    layer.alert('部分分类加载失败，请检查网络连接', {icon:2});
                                }
                            }
                        }
                    });
                });
            });
            
            // 传统系统监听分类选择变化
            $("#category_id").change(function () {
                var type = $("input[name='type']").val();
                
                // 只处理传统系统
                if (!in_array(type, ['jiuwu', 'yile', 'zhike', 'shangzhanwl'])) return;
                
                // 获取选中的分类ID
                var category_ids = [];
                $("#category_id option:selected").each(function() {
                    if($(this).val() != '-1') {
                        category_ids.push($(this).val());
                    }
                });
                
                // 如果没有选择任何分类，不执行操作
                if(category_ids.length == 0) return;
                
                // 自动调用getgoods函数获取商品列表
                getgoods();
            });
        });
        
        // 数组包含判断函数
        function in_array(needle, haystack) {
            for(var i in haystack) {
                if(haystack[i] == needle) return true;
            }
            return false;
        }
    </script>
    </body>
    </html>