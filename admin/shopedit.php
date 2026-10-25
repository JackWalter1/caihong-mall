<?php

//请勿删除版权信息，否则出现问题将不再保持售后修复！
include "../includes/common.php";
$title = "商品管理";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?><link rel="stylesheet" href="<?php echo $cdnpublic;?>select2/4.0.10/css/select2.min.css">
<script src="<?php echo $cdnpublic;?>select2/4.0.10/js/select2.min.js"></script>
<style>
	.select2-selection.select2-selection--single {
		height: 32px;
	}

	.select2-container--default.select2-selection--single {
		padding: 5px;
	}
#GoodsInfo img{max-width:100%}
</style>
<div class="modal" align="left" id="inputabout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">输入框标题说明</h4>
      </div>
      <div class="modal-body">
	  使用以下输入框标题可实现特殊的转换功能<br/>
	  自动从链接和文字取出链接：<a href="javascript:changeinput('作品链接')">作品链接</a>、<a href="javascript:changeinput('视频链接')">视频链接</a>、<a href="javascript:changeinput('分享链接')">分享链接</a>、<a href="javascript:changeinput('自定义[shareurl]')">自定义[shareurl]</a><br/>
	  自动获取音乐/视频ID：<a href="javascript:changeinput('作品ID')">作品ID</a>、<a href="javascript:changeinput('帖子ID')">帖子ID</a>、<a href="javascript:changeinput('用户ID')">用户ID</a>、<a href="javascript:changeinput('自定义[shareid]')">自定义[shareid]</a><br/><hr/>
	  注：在输入框名称后面加[shareid]、[shareurl]可以分别有获取ID、获取URL功能
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal" align="left" id="inputsabout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">更多输入框标题说明</h4>
      </div>
      <div class="modal-body">
	  使用以下输入框标题可实现特殊的转换功能<br/>
	  获取空间说说列表：<a href="javascript:changeinputs('说说ID')">说说ID</a>、<a href="javascript:changeinputs('说说ＩＤ')">说说ＩＤ</a>、<a href="javascript:changeinputs('自定义[ssid]')">自定义[ssid]</a><br/>
	  获取空间日志列表：<a href="javascript:changeinputs('日志ID')">日志ID</a>、<a href="javascript:changeinputs('日志ＩＤ')">日志ＩＤ</a>、<a href="javascript:changeinputs('自定义[rzid]')">自定义[rzid]</a><br/>
	  作品地址获取：<a href="javascript:changeinputs('自定义[zpid]')">自定义[zpid]</a><br/>
	  收货地址获取：<a href="javascript:changeinputs('收货地址')">收货地址</a>、<a href="javascript:changeinputs('收货人地址')">收货人地址</a>、<a href="javascript:changeinputs('自定义[address]')">自定义[address]</a><br/><hr/>
	  显示选择框，在名称后面加{选择1,选择2}，例如：<a href="javascript:changeinputs('分类名{普通,音乐,宠物}')">分类名{普通,音乐,宠物}</a>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php 
adminpermission("shop", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
// 获取所有活跃分类，区分一级和二级
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$classes = [];
$subClasses = [];
$shua_class[0] = "未分类";

// 先将分类按层级分组
while ($res = $rs->fetch()) {
    $shua_class[$res["cid"]] = $res["name"];
    if ($res["pid"] == 0) {
        // 一级分类
        $classes[$res["cid"]] = $res;
    } else {
        // 二级分类，按父分类ID分组
        $subClasses[$res["pid"]][] = $res;
    }
}

// 生成带有层级关系的分类下拉选项
$select = "<option value=\"0\">未分类</option>";
foreach ($classes as $cid => $class) {
    // 一级分类
    $select .= "<option value=\"$cid\" style=\"font-weight:bold;color:#337ab7;\">" . $class["name"] . "</option>";
    // 二级分类（如果有）
    if (isset($subClasses[$cid])) {
        foreach ($subClasses[$cid] as $subClass) {
            $select .= "<option value=\"{$subClass['cid']}\" style=\"padding-left:20px;color:#555;\">┗━ " . $subClass["name"] . "</option>";
        }
    }
}
$rs = $DB->query("SELECT * FROM pre_shequ order by id asc");
$shequselect = "";
while ($res = $rs->fetch()) {
	$getInfo = \lib\Plugin::getConfig("third_" . $res["type"]);
	$shequselect .= "<option value=\"" . $res["id"] . "\" type=\"" . $res["type"] . "\" domain=\"" . $res["url"] . "\">[<font color=blue>" . $getInfo["title"] . "</font>] " . $res["url"] . ($res["remark"] ? " (" . $res["remark"] . ")" : "") . "</option>";
}
if ($_SESSION["priceselect"]) {
	$priceselect = $_SESSION["priceselect"];
} else {
	$rs = $DB->query("SELECT * FROM pre_price order by id asc");
	$priceselect = "<option value=\"0\">不使用加价模板</option>";
	while ($res = $rs->fetch()) {
		$kind = $res["kind"] == 1 ? "元" : "倍";
		$priceselect .= "<option value=\"" . $res["id"] . "\" kind=\"" . $res["kind"] . "\" p_2=\"" . $res["p_2"] . "\" p_1=\"" . $res["p_1"] . "\" p_0=\"" . $res["p_0"] . "\" >" . $res["name"] . "(" . $res["p_2"] . $kind . "|" . $res["p_1"] . $kind . "|" . $res["p_0"] . $kind . ")</option>";
	}
}
if ($my == "add") {
	?><form action="./shopedit.php?my=add_submit" method="POST" onsubmit="return checkinput()">
<div class="col-sm-12 col-md-6">
<div class="block">
<div class="block-title"><h3 class="panel-title">商品类型与对接设置</h3></div>
<input type="hidden" name="backurl" value="<?php echo $_SERVER["HTTP_REFERER"];?>"/>
<div class="">
<div class="form-group">
<label>购买成功后的动作:</label><br>
<select class="form-control" name="is_curl"><option value="0">0_手动发货</option><option value="2">自动提交到社区/卡盟</option><option value="1">自定义访问URL/POST</option><option value="4">自动发卡密</option><option value="3">自动发送提醒邮件/微信</option><option value="5">直接显示指定内容</option></select>
</div>
<div id="curl_display1" style="display:none;">
<div class="form-group">
<label>URL:</label><br>
<input type="text" class="form-control" name="curl" id="curl" value="">
</div>
<div class="form-group">
<label>POST:</label><br>
<input type="text" class="form-control" name="curl_post" id="curl_post" value="" placeholder="无POST内容请留空">
</div>
<font color="green">无POST内容请留空，POST格式：a=123&b=456<br/>变量代码：<br/>
<a href="#" onclick="Addstr('curl','[input]');return false">[input]</a>&nbsp;第一个输入框内容<br/>
<a href="#" onclick="Addstr('curl','[input2]');return false">[input2]</a>&nbsp;第二个输入框内容<br/>
<a href="#" onclick="Addstr('curl','[input3]');return false">[input3]</a>&nbsp;第三个输入框内容<br/>
<a href="#" onclick="Addstr('curl','[input4]');return false">[input4]</a>&nbsp;第四个输入框内容<br/>
<a href="#" onclick="Addstr('curl','[name]');return false">[name]</a>&nbsp;商品名称<br/>
<a href="#" onclick="Addstr('curl','[price]');return false">[price]</a>&nbsp;商品价格<br/>
<a href="#" onclick="Addstr('curl','[num]');return false">[num]</a>&nbsp;下单数量<br/>
<a href="#" onclick="Addstr('curl','[time]');return false">[time]</a>&nbsp;当前时间戳<br/></font>
<br/>
</div>
<div id="curl_display3" style="display:none;">
<div class="form-group">
<label>购买后直接显示的内容:</label><br>
<textarea class="form-control" name="showcontent" rows="3" placeholder="用户购买后直接在订单详情显示的内容，支持HTML代码"></textarea>
</div>
<!-- 新增：邮件发送选项 -->
<div class="form-group">
    <label>
        <input type="checkbox" name="send_email" value="1">
        同时通过邮件发送此内容给用户
    </label>
</div>
</div>
<div id="curl_display2" style="display:none;">
<div class="form-group">
<label>选择对接网站:</label>&nbsp;(<a href="shequlist.php" target="_blank">添加</a>)<br>
<select class="form-control" name="shequ"><?php echo $shequselect;?></select>
</div>
<div class="form-group" id="show_goodslist">
<label>选择对接商品:</label><br>
<div class="input-group">
<select class="form-control" id="goodslist"></select>
<span class="input-group-addon btn btn-success" id="getGoods">获取</span>
</div>
</div>
<div class="form-group" id="goods_id">
<label>商品ID（goods_id）:</label><br>
<input type="text" class="form-control" name="goods_id" value="">
</div>
<div class="form-group" id="goods_type">
<label>类型ID（goods_type）:</label><br>
<input type="text" class="form-control" name="goods_type" value="">
</div>
<div id="kyxCommunity" style="display:none;">
	<div class="form-group">
		<label>请选择分类:</label><br>
		<div class="input-group">
			<select class="form-control" id="kyxCategory">
				<option value="null">请选择分类</option>
			</select>
			<span class="input-group-addon btn btn-success"
				  id="kyxGetCategory">获取</span>
		</div>
	</div>
	<div class="form-group">
		<label>请选择对接商品:</label><br>
		<select class="form-control" id="kyxProductList">
			<option value="null">请选择二级分类</option>
		</select>
	</div>
</div>
<div class="form-group" id="goods_type_select_form" style="display:none;">
	<label>商品类型:</label><br>
	<select class="form-control" id="goods_type_select">
		<option value="0">代充</option>
		<option value="1">发卡</option>
	</select>
</div>
<div class="form-group" id="goods_param">
<label id="goods_param_name">参数名:</label><br>
<input type="text" class="form-control" name="goods_param" value="qq">
<pre><font color="green">对应输入框标题，多个参数请用|隔开；如果是对接卡盟，请直接填写下单页面地址</font></pre>
</div>
</div>
<div class="form-group" id="show_value">
<label>默认数量信息:</label><br>
<input type="number" class="form-control" name="value" id="value" value="" placeholder="用于对接社区使用或导出时显示" onkeyup="changeNum()">
<input type="hidden" id="price" value="">
</div>
<div id="GoodsInfo" class="alert alert-info" style="display:none;"></div>
</div></div>
</div>
<div class="col-sm-12 col-md-6">
<div class="block">
<div class="block-title"><h3 class="panel-title">商品基本信息设置</h3></div>
<div class="">
<div class="form-group">
<label>*商品分类:</label><br>
<!-- 添加分类搜索功能 -->
<select name="cid" class="form-control" id="categorySelect" default="<?php echo htmlspecialchars($_GET["cid"]);?>" style="width:100%">
<?php echo $select;?>
</select>
<script>
$(document).ready(function() {
    // 初始化分类选择框为可搜索状态
    $('#categorySelect').select2({
        placeholder: '请选择商品分类或输入关键词搜索',
        allowClear: true,
        width: '100%',
        language: {
            "noResults": function() {
                return "未找到匹配的分类";
            },
            "searching": function() {
                return "搜索中...";
            }
        }
    });
    
    // 如果有默认值，设置选中状态
    var defaultCid = '<?php echo htmlspecialchars($_GET["cid"]);?>';
    if(defaultCid && defaultCid != 0) {
        $('#categorySelect').val(defaultCid).trigger('change');
    }
});
</script>
<style>
/* 优化select2样式，使其更适合我们的页面 */
.select2-container .select2-selection--single {
    height: 34px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 34px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}
.select2-dropdown {
    border-color: #ddd;
    border-radius: 0 0 4px 4px;
}
.select2-search--dropdown .select2-search__field {
    border: 1px solid #ddd;
}
</style>
</div>
<div class="form-group">
<label>*商品名称:</label><br>
<input type="text" class="form-control" name="name" value="" required>
</div>
<div class="form-group">
<label>加价模板:</label>&nbsp;(<a href="./price.php" target="_blank">管理</a>)<br>
<select name="prid" class="form-control"><?php echo $priceselect;?></select>
</div>
<div class="form-group" id="prid1" style="display:none;">
<label>*成本价格:</label><br>
<input type="text" class="form-control" name="price1" value="">
</div>
<table class="table table-striped table-bordered table-condensed" id="prid0" style="display:none;">
<tbody>
<tr align="center"><td>*销售价格</td><td>普及版价格</td><td>专业版价格</td></tr>
<tr>
<td><input type="text" name="price" value="" class="form-control input-sm"/></td>
<td><input type="text" name="cost" value="" class="form-control input-sm" placeholder="不填写则同步销售价格"/></td>
<td><input type="text" name="cost2" value="" class="form-control input-sm" placeholder="不填写则同步普及版价格"/></td>
</tr>
</table>
<div class="form-group">
<label>批发价格优惠设置:</label><br>
<input type="text" class="form-control" name="prices" placeholder="不懂请勿填写">
<pre><font color="green">填写格式：购满x个|减少x元单价,购满x个|减少x元单价  例如10|0.1,20|0.3,30|0.5</font></pre>
</div>
<div class="form-group">
<label>第一个输入框标题:</label><br>
<div class="input-group">
<input type="text" class="form-control" name="input" value="" placeholder="留空默认为“下单账号”"><span class="input-group-btn"><a href="#inputabout" data-toggle="modal" class="btn btn-info" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a></span>
</div>
</div>
<div class="form-group">
<label>更多输入框标题:</label><br>
<div class="input-group">
<input type="text" class="form-control" name="inputs" value="" placeholder="留空则不显示更多输入框"><span class="input-group-btn"><a href="#inputsabout" data-toggle="modal" class="btn btn-info" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a></span>
</div>
<pre><font color="green">多个输入框请用|隔开(不能超过4个)</font></pre>
</div>
<div class="form-group">
<label>商品简介:</label>(没有请留空)<br>
<textarea class="form-control" id="editor_id" name="desc" rows="3" style="width:100%" placeholder="当选择该商品时自动显示，支持HTML代码"></textarea>
</div>
<div class="form-group">
<label>提示内容:</label>(没有请留空)<br>
<input type="text" class="form-control" name="alert" value="" placeholder="当选择该商品时自动弹出提示，不支持HTML代码">
</div>
<div class="form-group">
<label>商品图片:</label><br>
<input type="file" id="file" onchange="fileUpload()" style="display:none;"/>
<div class="input-group">
<input type="text" class="form-control" id="shopimg" name="shopimg" value="" placeholder="填写图片URL，没有请留空"><span class="input-group-btn"><a href="javascript:fileSelect()" class="btn btn-success" title="上传图片"><i class="glyphicon glyphicon-upload"></i></a><a href="javascript:fileView()" class="btn btn-warning" title="查看图片"><i class="glyphicon glyphicon-picture"></i></a></span>
</div>
</div>
<div class="form-group">
<label>*显示数量选择框:</label><br>
<select class="form-control" name="multi"><option value="1">1_是</option><option value="0">0_否</option></select>
</div>
<table class="table table-striped table-bordered table-condensed" id="multi0" style="display:none;">
<tbody>
<tr align="center"><td>最小下单数量</td><td>最大下单数量</td></tr>
<tr>
<td><input type="text" name="min" value="" class="form-control input-sm" placeholder="留空则默认为1"/></td>
<td><input type="text" name="max" value="" class="form-control input-sm" placeholder="留空则不限数量"/></td>
</tr>
</table>
<div class="form-group">
<label>允许重复下单:</label>
<label class="checkbox-inline" style="margin-left: 10px;">
<input type="checkbox" name="repeat_default" value="1"> 默认为开启
</label>
<br>
<div class="input-group">
<select class="form-control" name="repeat"><option value="0">0_否</option><option value="1">1_是</option></select>
<a tabindex="0" class="input-group-addon" role="button" data-toggle="popover" data-trigger="focus" title="" data-placement="bottom" data-content="是指相同下单输入内容（非同一用户）当天只能下单一次，或上一条订单未处理的情况下不能重复下单"><span class="glyphicon glyphicon-info-sign"></span></a>
</div>
</div>
<div class="form-group">
<label>验证操作:</label><br>
<select class="form-control" name="validate"><option value="0">不开启验证</option><option value="1">验证QQ空间是否有访问权限</option><option value="2">验证已开通服务(符合则禁止下单)</option><option value="3">验证已开通服务(符合则不对接社区)</option></select>
</div>
<div class="form-group" id="valiserv" style="display:none;">
<label>需要验证的已开通服务:</label><br>
<select class="form-control" name="valiserv"><option value="vip">QQ会员</option><option value="svip">超级会员</option><option value="red">红钻贵族</option><option value="green">绿钻贵族</option><option value="sgreen">绿钻豪华版</option><option value="yellow">黄钻贵族</option><option value="syellow">豪华黄钻</option><option value="hollywood">腾讯视频VIP</option><option value="qqmsey">付费音乐包</option><option value="qqmstw">豪华付费音乐包</option><option value="weiyun">微云会员</option><option value="sweiyun">微云超级会员</option></select>
</div>
<div class="form-group">
<label>网盘投诉:</label><br>
<select class="form-control" name="ts"><option value="1">开启</option><option value="0">关闭</option></select>
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定添加">
<br/><a href="./shoplist.php?cid=<?php echo $_GET["cid"];?>">>>返回商品列表</a>
</div></div>
</div>
</form>
<?php 
} elseif ($my == "edit") {
	$tid = intval($_GET["tid"]);
	$row = $DB->getRow("select * from pre_tools where tid=:tid limit 1", array(":tid" => $tid));
	?><form action="./shopedit.php?my=edit_submit&tid=<?php echo $tid;?>" method="POST" onsubmit="return checkinput()">
<div class="col-sm-12 col-md-6">
<div class="block">
<div class="block-title"><h3 class="panel-title">商品类型与对接设置</h3></div>
<div class="">
<input type="hidden" name="backurl" value=""/>
<div class="form-group">
<label>购买成功后的动作:</label><br>
<select class="form-control" name="is_curl" default="<?php echo $row["is_curl"];?>"><option value="0">0_手动发货</option><option value="2">自动提交到社区/卡盟</option><option value="1">自定义访问URL/POST</option><option value="4">自动发卡密</option><option value="3">自动发送提醒邮件/微信</option><option value="5">直接显示指定内容</option></select>
</div>
<div id="curl_display1" style="<?php echo $row["is_curl"] != 1 ? "display:none;" : NULL;?>">
<div class="form-group">
<label>URL:</label><br>
<input type="text" class="form-control" name="curl" id="curl" value="<?php echo $row["curl"];?>">
</div>
<div class="form-group">
<label>POST:</label><br>
<input type="text" class="form-control" name="curl_post" id="curl_post" value="<?php echo $row["goods_param"];?>" placeholder="无POST内容请留空">
</div>
<font color="green">无POST内容请留空，POST格式：a=123&b=456<br/>变量代码：<br/>
<a href="#" onclick="Addstr('curl','[input]');return false">[input]</a>&nbsp;第一个输入框内容<br/>
<a href="#" onclick="Addstr('curl','[input2]');return false">[input2]</a>&nbsp;第二个输入框内容<br/>
<a href="#" onclick="Addstr('curl','[input3]');return false">[input3]</a>&nbsp;第三个输入框内容<br/>
<a href="#" onclick="Addstr('curl','[input4]');return false">[input4]</a>&nbsp;第四个输入框内容<br/>
<a href="#" onclick="Addstr('curl','[name]');return false">[name]</a>&nbsp;商品名称<br/>
<a href="#" onclick="Addstr('curl','[price]');return false">[price]</a>&nbsp;商品价格<br/>
<a href="#" onclick="Addstr('curl','[num]');return false">[num]</a>&nbsp;下单数量<br/>
<a href="#" onclick="Addstr('curl','[time]');return false">[time]</a>&nbsp;当前时间戳<br/></font>
<br/>
</div>
<div id="curl_display3" style="<?php echo $row["is_curl"] != 5 ? "display:none;" : NULL;?>">
<div class="form-group">
<label>购买后直接显示的内容:</label><br>
<textarea class="form-control" name="showcontent" rows="3" placeholder="用户购买后直接在订单详情显示的内容，支持HTML代码"><?php echo htmlspecialchars($row["showcontent"]);?></textarea>
</div>
<!-- 新增：邮件发送选项 -->
<div class="form-group">
    <label>
        <input type="checkbox" name="send_email" value="1" <?php echo isset($row['send_email']) && $row['send_email'] ? 'checked' : ''; ?>>
        同时通过邮件发送此内容给用户
    </label>
</div>
</div>
<div id="curl_display2" style="<?php echo $row["is_curl"] != 2 ? "display:none;" : NULL;?>">
<div class="form-group">
<label>选择对接网站:</label><br>
<select class="form-control" name="shequ" default="<?php echo $row["shequ"];?>"><?php echo $shequselect;?></select>
</div>
<div class="form-group" id="show_goodslist">
<label>选择对接商品:</label><br>
<div class="input-group">
<select class="form-control" id="goodslist" default="<?php echo $row["goods_id"];?>"></select>
<span class="input-group-addon btn btn-success" id="getGoods">获取</span>
</div>
</div>
<div class="form-group" id="goods_id">
<label>商品ID（goods_id）:</label><br>
<input type="text" class="form-control" name="goods_id" value="<?php echo $row["goods_id"];?>">
</div>
<div class="form-group" id="goods_type">
<label>类型ID（goods_type）:</label><br>
<input type="text" class="form-control" name="goods_type" value="<?php echo $row["goods_type"];?>">
</div>
<div id="kyxCommunity" style="display:none;">
	<div class="form-group">
		<label>请选择分类:</label><br>
		<div class="input-group">
			<select class="form-control" id="kyxCategory">
				<option value="null">请选择分类</option>
			</select>
			<span class="input-group-addon btn btn-success"
				  id="kyxGetCategory">获取</span>
		</div>
	</div>
	<div class="form-group">
		<label>请选择对接商品:</label><br>
		<select class="form-control" id="kyxProductList">
			<option value="null">请选择二级分类</option>
		</select>
	</div>
</div>
<div class="form-group" id="goods_type_select_form" style="display:none;">
	<label>商品类型:</label><br>
	<select class="form-control" id="goods_type_select" default="<?php echo isset($row['goods_type']) ? $row['goods_type'] : 0; ?>">
		<option value="0">代充</option>
		<option value="1">发卡</option>
	</select>
</div>
<div class="form-group" id="goods_param">
<label id="goods_param_name">参数名:</label><br>
<input type="text" class="form-control" name="goods_param" value="<?php echo $row["goods_param"];?>">
<pre><font color="green">对应输入框标题，多个参数请用|隔开；如果是对接卡盟，请直接填写下单页面地址</font></pre>
</div>
</div>
<div class="form-group" id="show_value">
<label>默认数量信息:</label><br>
<input type="number" class="form-control" name="value" id="value" value="<?php echo $row["value"];?>" placeholder="用于对接社区使用或导出时显示" onkeyup="changeNum()">
<input type="hidden" id="price" value="">
</div>
<div id="GoodsInfo" class="alert alert-info" style="display:none;"></div>
</div></div>
</div>
<div class="col-sm-12 col-md-6">
<div class="block">
<div class="block-title"><h3 class="panel-title">商品基本信息设置</h3></div>
<div class="">
<div class="form-group">
<label>商品分类:</label><br>
<!-- 添加分类搜索功能 -->
<select name="cid" class="form-control" id="categorySelect" default="<?php echo $row["cid"];?>" style="width:100%">
<?php echo $select;?>
</select>
<script>
$(document).ready(function() {
    // 初始化分类选择框为可搜索状态
    $('#categorySelect').select2({
        placeholder: '请选择商品分类或输入关键词搜索',
        allowClear: true,
        width: '100%',
        language: {
            "noResults": function() {
                return "未找到匹配的分类";
            },
            "searching": function() {
                return "搜索中...";
            }
        }
    });
    
    // 设置默认选中状态
    var defaultCid = '<?php echo $row["cid"];?>';
    if(defaultCid && defaultCid != 0) {
        $('#categorySelect').val(defaultCid).trigger('change');
    }
});
</script>
<style>
/* 优化select2样式，使其更适合我们的页面 */
.select2-container .select2-selection--single {
    height: 34px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 34px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}
.select2-dropdown {
    border-color: #ddd;
    border-radius: 0 0 4px 4px;
}
.select2-search--dropdown .select2-search__field {
    border: 1px solid #ddd;
}
</style>
</div>
<div class="form-group">
<label>*商品名称:</label><br>
<input type="text" class="form-control" name="name" value="<?php echo $row["name"];?>" required>
</div>
<div class="form-group">
<label>加价模板:</label>&nbsp;(<a href="./price.php" target="_blank">管理</a>)<br>
<select name="prid" class="form-control" default="<?php echo $row["prid"];?>"><?php echo $priceselect;?></select>
</div>
<div class="form-group" id="prid1" style="display:none;">
<label>*成本价格:</label><br>
<input type="text" class="form-control" name="price1" value="<?php echo $row["price"];?>">
</div>
<table class="table table-striped table-bordered table-condensed" id="prid0" style="display:none;">
<tbody>
<tr align="center"><td>*销售价格</td><td>普及版价格</td><td>专业版价格</td></tr>
<tr>
<td><input type="text" name="price" value="<?php echo $row["price"];?>" class="form-control input-sm"/></td>
<td><input type="text" name="cost" value="<?php echo $row["cost"];?>" class="form-control input-sm" placeholder="不填写则同步销售价格"/></td>
<td><input type="text" name="cost2" value="<?php echo $row["cost2"];?>" class="form-control input-sm" placeholder="不填写则同步普及版价格"/></td>
</tr>
</table>
<div class="form-group">
<label>批发价格优惠设置:</label><br>
<input type="text" class="form-control" name="prices" value="<?php echo $row["prices"];?>">
<pre><font color="green">填写格式：购满x个|减少x元单价,购满x个|减少x元单价  例如10|0.1,20|0.3,30|0.5</font></pre>
</div>
<div class="form-group">
<label>第一个输入框标题:</label><br>
<div class="input-group">
<input type="text" class="form-control" name="input" value="<?php echo $row["input"];?>" placeholder="留空默认为“下单账号”"><span class="input-group-btn"><a href="#inputabout" data-toggle="modal" class="btn btn-info" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a></span>
</div>
</div>
<div class="form-group">
<label>更多输入框标题:</label><br>
<div class="input-group">
<input type="text" class="form-control" name="inputs" value="<?php echo $row["inputs"];?>" placeholder="留空则不显示更多输入框"><span class="input-group-btn"><a href="#inputsabout" data-toggle="modal" class="btn btn-info" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a></span>
</div>
<pre><font color="green">多个输入框请用|隔开(不能超过4个)</font></pre>
</div>
<div class="form-group">
<label>商品简介:</label>(没有请留空)<br>
<textarea class="form-control" id="editor_id" name="desc" rows="3" style="width:100%" placeholder="当选择该商品时自动显示，支持HTML代码"><?php echo htmlspecialchars($row["desc"]);?></textarea>
</div>
<div class="form-group">
<label>提示内容:</label>(没有请留空)<br>
<input type="text" class="form-control" name="alert" value="<?php echo htmlspecialchars($row["alert"]);?>" placeholder="当选择该商品时自动弹出提示，不支持HTML代码">
</div>
<div class="form-group">
<label>商品图片:</label><br>
<input type="file" id="file" onchange="fileUpload()" style="display:none;"/>
<div class="input-group">
<input type="text" class="form-control" id="shopimg" name="shopimg" value="<?php echo $row["shopimg"];?>" placeholder="填写图片URL，没有请留空"><span class="input-group-btn"><a href="javascript:fileSelect()" class="btn btn-success" title="上传图片"><i class="glyphicon glyphicon-upload"></i></a><a href="javascript:fileView()" class="btn btn-warning" title="查看图片"><i class="glyphicon glyphicon-picture"></i></a></span>
</div>
</div>
<div class="form-group">
<label>显示数量选择框:</label><br>
<select class="form-control" name="multi" default="<?php echo $row["multi"];?>"><option value="1">1_是</option><option value="0">0_否</option></select>
</div>
<table class="table table-striped table-bordered table-condensed" id="multi0" style="display:none;">
<tbody>
<tr align="center"><td>最小下单数量</td><td>最大下单数量</td></tr>
<tr>
<td><input type="text" name="min" class="form-control input-sm" value="<?php echo $row["min"];?>" placeholder="留空则默认为1"/></td>
<td><input type="text" name="max" class="form-control input-sm" value="<?php echo $row["max"];?>" placeholder="留空则不限数量"/></td>
</tr>
</table>
<div class="form-group">
<label>允许重复下单:</label>
<label class="checkbox-inline" style="margin-left: 10px;">
<input type="checkbox" name="repeat_default" value="1" <?php echo $row["repeat"] == 1 ? 'checked' : ''; ?>> 默认为开启
</label>
<br>
<div class="input-group">
<select class="form-control" name="repeat" default="<?php echo $row["repeat"];?>"><option value="0">0_否</option><option value="1">1_是</option></select>
<a tabindex="0" class="input-group-addon" role="button" data-toggle="popover" data-trigger="focus" title="" data-placement="bottom" data-content="是指相同下单输入内容（非同一用户）当天只能下单一次，或上一条订单未处理的情况下不能重复下单"><span class="glyphicon glyphicon-info-sign"></span></a>
</div>
</div>
<div class="form-group">
<label>验证操作:</label><br>
<select class="form-control" name="validate" default="<?php echo $row["validate"];?>"><option value="0">不开启验证</option><option value="1">验证QQ空间是否有访问权限</option><option value="2">验证已开通服务(符合则禁止下单)</option><option value="3">验证已开通服务(符合则不对接社区)</option></select>
</div>
<div class="form-group" id="valiserv" style="display:none;">
<label>需要验证的已开通服务:</label><br>
<select class="form-control" name="valiserv" default="<?php echo $row["valiserv"];?>"><option value="vip">QQ会员</option><option value="svip">超级会员</option><option value="red">红钻贵族</option><option value="green">绿钻贵族</option><option value="sgreen">绿钻豪华版</option><option value="yellow">黄钻贵族</option><option value="syellow">豪华黄钻</option><option value="hollywood">腾讯视频VIP</option><option value="qqmsey">付费音乐包</option><option value="qqmstw">豪华付费音乐包</option><option value="weiyun">微云会员</option><option value="sweiyun">微云超级会员</option></select>
</div>
<div class="form-group">
<label>网盘投诉:</label><br>
<select class="form-control" name="ts" default="<?php echo $row["ts"];?>"><option value="1">开启</option><option value="0">关闭</option></select>
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改">
<br/><a href="./shoplist.php?cid=<?php echo $row["cid"];?>">>>返回商品列表</a>
</div></div>
</div>
</form>
<?php 
} elseif ($my == "add_submit") {
	$cid = $_POST["cid"];
	$name = $_POST["name"];
	$prid = $_POST["prid"];
	$price = $prid == "0" ? $_POST["price"] : $_POST["price1"];
	$cost = $_POST["cost"];
	$cost2 = $_POST["cost2"];
	if ($prid == 0) {
		if ($cost2 > $cost) {
			showmsg("专业版加价不能高于普及版加价", 3);
		} elseif ($cost2 > $price) {
			showmsg("专业版加价不能高于普通用户加价", 3);
		} elseif ($cost > $price) {
			showmsg("普及版加价不能高于普通用户加价", 3);
		}
	}
	$prices = $_POST["prices"];
	$input = $_POST["input"];
	$inputs = $_POST["inputs"];
	$desc = $_POST["desc"];
	$alert = $_POST["alert"];
	$shopimg = $_POST["shopimg"];
	$value = $_POST["value"];
	$multi = $_POST["multi"];
	$min = $_POST["min"];
	$max = $_POST["max"];
	$validate = $_POST["validate"];
	$valiserv = $_POST["valiserv"];
	$is_curl = $_POST["is_curl"];
	$curl = $_POST["curl"];
	$showcontent = $_POST["showcontent"];
	$shequ = $_POST["shequ"];
	$goods_id = $_POST["goods_id"];
	$goods_type = $_POST["goods_type"];
	$goods_param = $is_curl == 1 ? $_POST["curl_post"] : $_POST["goods_param"];
	$repeat_default = isset($_POST["repeat_default"]) ? 1 : 0;
	$repeat = $repeat_default == 1 ? 1 : $_POST["repeat"];
	$ts = $_POST["ts"];
	// 设置默认值，防止NULL值插入数据库
	if(empty($cid)) $cid = 0;
	if(empty($shequ)) $shequ = 0;
	if(empty($goods_id)) $goods_id = 0;
	if(empty($goods_type)) $goods_type = 0;
	if ($name == NULL || $price == NULL) {
		showmsg("保存错误，商品名称和价格不能为空！", 3);
	} elseif ($is_curl == 2 && !$shequ) {
		showmsg("请选择对接社区！", 3);
	} else {
		$sort = $DB->getColumn("select sort from pre_tools order by sort desc limit 1");
		$sql = "INSERT INTO `pre_tools` (`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`showcontent`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`sort`,`active`,`ts`,`addtime`) VALUES (:cid,:name,:price,:cost,:cost2,:prid,:prices,:input,:inputs,:desc,:alert,:shopimg,:value,:is_curl,:curl,:showcontent,:shequ,:goods_id,:goods_type,:goods_param,:repeat,:multi,:min,:max,:validate,:valiserv,:sort,:active,:ts,:addtime)";
		$params = array(
			":cid" => $cid, ":name" => $name, ":price" => $price, ":cost" => $cost, ":cost2" => $cost2, ":prid" => $prid, ":prices" => $prices, ":input" => $input, ":inputs" => $inputs, ":desc" => $desc, ":alert" => $alert, ":shopimg" => $shopimg, ":value" => $value, ":is_curl" => $is_curl, ":curl" => $curl, ":showcontent" => $showcontent, ":shequ" => $shequ, ":goods_id" => $goods_id, ":goods_type" => $goods_type, ":goods_param" => $goods_param, ":repeat" => $repeat, ":multi" => $multi, ":min" => $min, ":max" => $max, ":validate" => $validate, ":valiserv" => $valiserv, ":sort" => $sort + 1, ":active" => 1, ":ts" => $ts, ":addtime" => $date
		);
		if ($DB->exec($sql, $params) !== false) {
			$tid = $DB->lastInsertId();
			// 记录上架日志
			$content = "上架：{$name}";
			$DB->exec("INSERT INTO `pre_toollogs` (`content`,`date`,`addtime`,`active`) VALUES ('{$content}', CURDATE(), NOW(), 1)");
			showmsg("添加商品成功！<br/><br/><a href='./shoplist.php?cid=$cid'>>>返回商品列表</a>", 1);
		} else {
			showmsg("添加商品失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$tid = intval($_GET["tid"]);
	$rows = $DB->getRow("select * from pre_tools where tid=:tid limit 1", array(":tid" => $tid));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$cid = $_POST["cid"];
	$name = $_POST["name"];
	$prid = $_POST["prid"];
	$price = $prid == "0" ? $_POST["price"] : $_POST["price1"];
	$cost = $_POST["cost"];
	$cost2 = $_POST["cost2"];
	if ($prid == 0) {
		if ($cost2 > $cost) {
			showmsg("专业版加价不能高于普及版加价", 3);
		} elseif ($cost2 > $price) {
			showmsg("专业版加价不能高于普通用户加价", 3);
		} elseif ($cost > $price) {
			showmsg("普及版加价不能高于普通用户加价", 3);
		}
	}
	$prices = $_POST["prices"];
	$input = $_POST["input"];
	$inputs = $_POST["inputs"];
	$desc = $_POST["desc"];
	$alert = $_POST["alert"];
	$shopimg = $_POST["shopimg"];
	$value = $_POST["value"];
	$multi = $_POST["multi"];
	$min = $_POST["min"];
	$max = $_POST["max"];
	$validate = $_POST["validate"];
	$valiserv = $_POST["valiserv"];
	$is_curl = $_POST["is_curl"];
	$curl = $_POST["curl"];
	$showcontent = $_POST["showcontent"];
	$shequ = $_POST["shequ"];
	$goods_id = $_POST["goods_id"];
	$goods_type = $_POST["goods_type"];
	$goods_param = $is_curl == 1 ? $_POST["curl_post"] : $_POST["goods_param"];
	$repeat_default = isset($_POST["repeat_default"]) ? 1 : 0;
	$repeat = $repeat_default == 1 ? 1 : $_POST["repeat"];
	$ts = $_POST["ts"];
	// 设置默认值，防止NULL值插入数据库
	if(empty($cid)) $cid = 0;
	if(empty($shequ)) $shequ = 0;
	if(empty($goods_id)) $goods_id = 0;
	if(empty($goods_type)) $goods_type = 0;
	if ($name == NULL || $price == NULL) {
		showmsg("保存错误，商品名称和价格不能为空！", 3);
	} elseif ($is_curl == 2 && !$shequ) {
		showmsg("请选择对接社区！", 3);
	} else {
		$sql = "UPDATE `pre_tools` SET `cid`=:cid,`name`=:name,`price`=:price,`cost`=:cost,`cost2`=:cost2,`prid`=:prid,`prices`=:prices,`input`=:input,`inputs`=:inputs,`desc`=:desc,`alert`=:alert,`shopimg`=:shopimg,`value`=:value,`is_curl`=:is_curl,`curl`=:curl,`showcontent`=:showcontent,`shequ`=:shequ,`goods_id`=:goods_id,`goods_type`=:goods_type,`goods_param`=:goods_param,`repeat`=:repeat,`multi`=:multi,`min`=:min,`max`=:max,`validate`=:validate,`valiserv`=:valiserv,`ts`=:ts WHERE `tid`=:tid";
		$params = array(
			":cid" => $cid, ":name" => $name, ":price" => $price, ":cost" => $cost, ":cost2" => $cost2, ":prid" => $prid, ":prices" => $prices, ":input" => $input, ":inputs" => $inputs, ":desc" => $desc, ":alert" => $alert, ":shopimg" => $shopimg, ":value" => $value, ":is_curl" => $is_curl, ":curl" => $curl, ":showcontent" => $showcontent, ":shequ" => $shequ, ":goods_id" => $goods_id, ":goods_type" => $goods_type, ":goods_param" => $goods_param, ":repeat" => $repeat, ":multi" => $multi, ":min" => $min, ":max" => $max, ":validate" => $validate, ":valiserv" => $valiserv, ":ts" => $ts, ":tid" => $tid
		);
		if ($DB->exec($sql, $params) !== false) {
			showmsg("修改商品成功！<br/><br/><a href=\"./shoplist.php?cid=$cid\">>>返回商品列表</a>", 1);
		} else {
			showmsg("修改商品失败！" . $DB->error(), 4);
		}
	}
}
?><script>
var isAdd = true;
</script>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/shopedit.js?ver=<?php echo VERSION;?>"></script>
<?php 
if ($conf["shopdesc_editor"]) {
	?><script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<script>
KindEditor.ready(function(K) {
	window.editor = K.create('#editor_id', {
		resizeType : 1,
		allowUpload : false,
		allowPreviewEmoticons : false,
		uploadJson : './ajax.php?act=article_upload',
		items : [
			'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
			'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
			'insertunorderedlist', '|', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
	});
});
</script>
<?php 
}
?><script>
<?php 
\lib\Plugin::showThirdPluginsEditJs();
?></script>
</body>
</html>