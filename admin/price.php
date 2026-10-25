<?php

include "../includes/common.php";
$title = "加价模板";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
<?php 
adminpermission("price", 1);
$numrows = $DB->getColumn("SELECT count(*) from pre_price");
?>

<div class="modal" id="modal-store" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content animated flipInX">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span
							aria-hidden="true">&times;</span><span
							class="sr-only">Close</span></button>
				<h4 class="modal-title" id="modal-title">加价模板修改/添加</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="form-store">
					<input type="hidden" id="action"/>
					<input type="hidden" name="prid" id="prid"/>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right"></label>
						<div class="col-sm-10">
							<div class="alert alert-warning">
								注意：设置的加价规则应满足：用户加价&gt;=普及版加价&gt;=专业版加价
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right">模板名称</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="name" id="name" placeholder="输入模板名称">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">加价方式</label>
						<div class="col-sm-10">
							<select name="kind" id="kind" class="form-control" onchange="changeKind()">
								<option value="0">倍数加价</option>
								<option value="1">固定金额加价</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">假设商品成本价</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" value="100" id="test_price" onkeyup="changeTest()">
							<pre>此价格作为下面加价后价格显示预览，无实际意义！</pre>
						</div>
					</div>
												<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right">专业版加价</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="p_2" id="p_2" onkeyup="changeTest('p_2')">
							<div class="form-control" style="color: red">加价后价格:<span id="test_p_2"></span></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right">普及版加价</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="p_1" id="p_1" onkeyup="changeTest('p_1')">
							<div class="form-control" style="color: red">加价后价格:<span id="test_p_1"></span></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label no-padding-right">普通用户加价</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="p_0" id="p_0" onkeyup="changeTest('p_0')">
							<div class="form-control" style="color: red">加价后价格:<span id="test_p_0"></span></div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
				<button type="button" class="btn btn-primary" id="store" onclick="save()">保存</button>
			</div>
		</div>
	</div>
</div>

<div class="block">
<div class="block-title clearfix">
<h2>加价模板&nbsp;<a class="btn-xs btn-default" href="javascript:addframe()">
                            <i class="fa fa-plus"></i> 新增</a></h2>
</div>
      <div class="table-responsive">
<form name="form1" id="form1">
        <table class="table table-striped">
          <thead><tr><th width="40"><input type="checkbox" name="chkAll" id="chkAll" onClick="checkAll(this)"></th><th>ID</th><th>名称</th><th>类型</th><th>加价规则</th><th>操作</th></tr></thead>
          <tbody>
<?php 
$rs = $DB->query("SELECT * FROM pre_price WHERE 1 order by id desc");
while ($res = $rs->fetch()) {
	echo "<tr><td><input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["id"] . "\" onClick=\"unselectall()\"></td><td><b>" . $res["id"] . "</b></td><td>" . $res["name"] . "</td><td>" . display_kind($res["kind"]) . "</td><td>" . $res["p_2"] . "|" . $res["p_1"] . "|" . $res["p_0"] . "</td><td><a href=\"./shoplist.php?prid=" . $res["id"] . "\" target=\"_blank\" class=\"btn btn-success btn-xs\">商品</a>&nbsp;<a href=\"javascript:editframe(" . $res["id"] . ")\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<a href=\"javascript:delItem(" . $res["id"] . ")\" class=\"btn btn-xs btn-danger\">删除</a>&nbsp;<a href=\"javascript:change(" . $res["id"] . ")\" class=\"btn btn-xs btn-default\">批量更改</a></td></tr>";
}
?><tr><td colspan="6"><label><input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox">全选</label>&nbsp;
<select name="aid"><option selected>批量操作</option><option value="1">&gt;删除选中</option></select><button type="button" onclick="batchOperation()">执行</button></td></tr>
          </tbody>
        </table>
</form>
      </div>
    </div>
  </div>
</div>
<?php 
// 获取所有活跃分类，区分一级和二级
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$classes = [];
$subClasses = [];

// 先将分类按层级分组
while ($res = $rs->fetch()) {
    if ($res["pid"] == 0) {
        // 一级分类
        $classes[$res["cid"]] = $res;
    } else {
        // 二级分类，按父分类ID分组
        $subClasses[$res["pid"]][] = $res;
    }
}

// 生成带有层级关系的分类下拉选项
$select = "<option value=\"0\">未分类商品</option>";
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
?><div id="class-select" style="display:none;">
	<div class="form-group">
	  <label>请选择要应用该加价模板的分类</label><br/>
	  <select name="cids" multiple="multiple" class="form-control" style="height:150px;max-height:200px;overflow-y:auto;"><?php echo $select;?></select>
	  <font color="green">按住Ctrl键可多选，按住Shift键可选择连续范围</font>
	</div>
</div>
<script src="<?php echo $cdnpublic;?>layer/2.3/layer.js"></script>
<script src="assets/js/price.js?ver=<?php echo VERSION;?>"></script>
</body>
</html><?php 
function display_kind($zt)
{
	if ($zt == 1) {
		return "<span class=\"btn-info btn-xs\">固定金额加价</span>";
	} else {
		return "<span class=\"btn-primary btn-xs\">倍数加价</span>";
	}
}