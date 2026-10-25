<?php

include '../includes/common.php';
if ($islogin == 1) {
} else {
exit('<script language="javascript">window.location.href="./login.php";</script>');
}
adminpermission('shop', 1);
$numrows = $DB->getColumn('SELECT count(*) from pre_class');

// 获取所有分类，包括一级和二级
$rs = $DB->query('SELECT * FROM pre_class WHERE 1 order by sort asc');
$classes = [];
$subClasses = [];
while ($res = $rs->fetch()) {
	if ($res['pid'] == 0) {
		$classes[] = $res; // 一级分类
	} else {
		$subClasses[$res['pid']][] = $res; // 二级分类，按父分类ID分组
	}
}

// 获取所有一级分类列表，用于添加二级分类时选择父分类
$parentClasses = $DB->query('SELECT cid, name FROM pre_class WHERE pid = 0 order by sort asc');
$parentClassOptions = '';
while ($parent = $parentClasses->fetch()) {
	$parentClassOptions .= "<option value='{$parent['cid']}'>{$parent['name']}</option>";
}

?>
<script>
var checkflag = "false";

function checkAll(field) {
	if (checkflag == "false") {
		$('input[name="checkbox[]"]').each(function() {
			$(this).prop('checked', true);
		});
		checkflag = "true";
	} else {
		$('input[name="checkbox[]"]').each(function() {
			$(this).prop('checked', false);
		});
		checkflag = "false";
	}
}

function check1(field) {
	if (checkflag == "false") {
		for (i = 0; i < field.length; i++) {
			field[i].checked = true;
		}
		checkflag = "true";
		return "false";
	} else {
		for (i = 0; i < field.length; i++) {
			field[i].checked = false;
		}
		checkflag = "false";
		return "true";
	}
}

function unselectall() {
	if(document.classlist.chkAll1.checked){
		document.classlist.chkAll1.checked = document.classlist.chkAll1.checked&0;
		checkflag = "false";
	}
}

function batchOperation() {
	var aid = $("select[name='aid']").val();
	var checkbox = $("input[name='checkbox[]']:checked");
	
	if(checkbox.length == 0){
		layer.msg('请至少选择一个分类', {icon: 2});
		return false;
	}
	
	if(aid == 3){
		layer.confirm('确定要删除选中的 ' + checkbox.length + ' 个分类吗？删除后将无法恢复！', {
			btn: ['确定', '取消'],
			icon: 3
		}, function(index) {
			layer.close(index);
			executeBatchOperation();
		});
	} else {
		executeBatchOperation();
	}
}

function executeBatchOperation() {
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax_class.php?act=batchOperation',
		data : $('#classlist').serialize(),
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg(data.msg, {icon: 1});
				setTimeout(function() {
					location.reload();
				}, 1000);
			} else {
				layer.msg(data.msg, {icon: 2});
			}
		},
		error:function(data){
			layer.close(ii);
			layer.msg('服务器错误', {icon: 2});
			return false;
		}
	});
}

function addSubClass(parentId) {
	layer.prompt({
		formType: 0,
		title: '请输入子分类名称：',
		area: ['400px', '100px'] // 宽高
	}, function(name, index) {
		if (name && name.trim() != '') {
			var loading = layer.load(2);
			$.ajax({
				url: 'ajax_class.php?act=addClass',
				data: {name: name, pid: parentId},
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					layer.close(loading);
					layer.close(index);
					if (data.code == 0) {
						layer.msg(data.msg, {icon: 1});
						setTimeout(function() {
							location.reload();
						}, 1000);
					} else {
						layer.msg(data.msg, {icon: 2});
					}
				},
				error: function() {
					layer.close(loading);
					layer.close(index);
					layer.msg('添加失败，请重试', {icon: 2});
				}
			});
		}
	});
}
function sort(cid, type) {
	var loading = layer.load(2);
	$.ajax({
		type: 'GET',
		url: './ajax_class.php?act=setClassSort&cid=' + cid + '&sort=' + type,
		data: {},
		dataType: 'json',
		success: function(data) {
			layer.close(loading);
			if (data.code == 0) {
				layer.msg(data.msg, {icon: 1});
				setTimeout(function() {
					location.reload();
				}, 1000);
			} else {
				layer.msg(data.msg, {icon: 2});
			}
		},
		error: function() {
			layer.close(loading);
			layer.msg('操作失败，请重试', {icon: 2});
		}
	});
}

function editClass(cid) {
	layer.prompt({
		formType: 0,
		title: '请输入新的分类名称：',
		area: ['400px', '100px'] // 宽高
	}, function(name, index) {
		if (name && name.trim() != '') {
			// 对于子分类，移除前缀
			if (name.indexOf('┗━ ') === 0) {
				name = name.substring(3);
			}
			var loading = layer.load(2);
			$.ajax({
				type: 'POST',
				url: './ajax_class.php?act=editClass&cid=' + cid,
				data: {
					name: name
				},
				dataType: 'json',
				success: function(data) {
					layer.close(loading);
					layer.close(index);
					if (data.code == 0) {
						layer.msg('修改成功', {icon: 1});
						setTimeout(function() {
							location.reload();
						}, 1000);
					} else {
						layer.msg(data.msg, {icon: 2});
					}
				},
				error: function() {
					layer.close(loading);
					layer.close(index);
					layer.msg('操作失败，请重试', {icon: 2});
				}
			});
		}
	});
}

function setActive(cid, type) {
	var loading = layer.load(2);
	$.ajax({
		type: 'GET',
		url: './ajax_class.php?act=setClass',
		data: {
			cid: cid,
			active: type
		},
		dataType: 'json',
		success: function(data) {
			layer.close(loading);
			if (data.code == 0) {
				layer.msg(data.msg, {icon: 1});
				setTimeout(function() {
					location.reload();
				}, 1000);
			} else {
				layer.msg(data.msg, {icon: 2});
			}
		},
		error: function() {
			layer.close(loading);
			layer.msg('操作失败，请重试', {icon: 2});
		}
	});
}

function delClass(cid) {
	layer.confirm('确定要删除该分类吗？删除后将无法恢复！', {
		btn: ['确定', '取消'],
		icon: 3
	}, function(index) {
		layer.close(index);
		var loading = layer.load(2);
		$.ajax({
			type: 'POST',
			url: './ajax_class.php?act=delClass&cid=' + cid,
			data: {},
			dataType: 'json',
			success: function(data) {
				layer.close(loading);
				if (data.code == 0) {
					layer.msg(data.msg, {icon: 1});
					setTimeout(function() {
						location.reload();
					}, 1000);
				} else {
					layer.msg(data.msg, {icon: 2});
				}
			},
			error: function() {
				layer.close(loading);
				layer.msg('操作失败，请重试', {icon: 2});
			}
		});
	});
}

function saveAll() {
	var sortData = {};
	var nameData = {};
	
	$('input[name^="sort"]').each(function() {
		var cid = $(this).attr('name').match(/\d+/)[0];
		sortData[cid] = $(this).val();
	});
	
	$('input[name^="name"]').each(function() {
		var cid = $(this).attr('name').match(/\d+/)[0];
		var name = $(this).val();
		// 对于子分类，移除前缀
		if (name.indexOf('┗━ ') === 0) {
			name = name.substring(3);
		}
		nameData[cid] = name;
	});
	
	var loading = layer.load(2);
	$.ajax({
		type: 'POST',
		url: './ajax_class.php?act=editClassAll',
		data: {
			sort: sortData,
			name: nameData
		},
		dataType: 'json',
		success: function(data) {
			layer.close(loading);
			if (data.code == 0) {
				layer.msg('保存成功', {icon: 1});
				setTimeout(function() {
					location.reload();
				}, 1000);
			} else {
				layer.msg(data.msg, {icon: 2});
			}
		},
		error: function() {
			layer.close(loading);
			layer.msg('操作失败，请重试', {icon: 2});
		}
	});
}

function addClass() {
	var name = $('input[name="addname"]').val();
	if (name && name.trim() != '') {
		var loading = layer.load(2);
		$.ajax({
			type: 'POST',
			url: './ajax_class.php?act=addClass',
			data: {
				name: name,
				pid: 0
			},
			dataType: 'json',
			success: function(data) {
				layer.close(loading);
				if (data.code == 0) {
					layer.msg(data.msg, {icon: 1});
					setTimeout(function() {
						location.reload();
					}, 1000);
				} else {
					layer.msg(data.msg, {icon: 2});
				}
			},
			error: function() {
				layer.close(loading);
				layer.msg('添加失败，请重试', {icon: 2});
			}
		});
	} else {
		layer.msg('分类名称不能为空', {icon: 2});
	}
}

function setBlockPay(cid) {
	$.ajax({
		type : 'POST',
		url : 'ajax_class.php?act=getBlockPay',
		data : {cid:cid},
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.open({
					area: ['360px'],
					title: '设置此分类商品禁用支付方式',
					content: '<div class="form-group"><div class="checkbox"><label><input type="checkbox" name="paytype" value="alipay" '+($.inArray('alipay',data.data)>-1?'checked':null)+'> 禁用支付宝</label></div><div class="checkbox"><label><input type="checkbox" name="paytype" value="qqpay" '+($.inArray('qqpay',data.data)>-1?'checked':null)+'> 禁用QQ钱包</label></div><div class="checkbox"><label><input type="checkbox" name="paytype" value="wxpay" '+($.inArray('wxpay',data.data)>-1?'checked':null)+'> 禁用微信支付</label></div><div class="checkbox"><label><input type="checkbox" name="paytype" value="rmb" '+($.inArray('rmb',data.data)>-1?'checked':null)+'> 禁用余额</label></div></div>',
					yes: function(){
						var paytype = [];
						$.each($("input[name='paytype']:checked"),function(){
							paytype.push($(this).val());
						});
						$.ajax({
							type : 'POST',
							url : 'ajax_class.php?act=setBlockPay',
							data : {cid:cid,paytype: paytype},
							dataType : 'json',
							success : function(data) {
								if(data.code == 0){
									layer.msg(data.msg, {icon:1});
								}else{
									layer.alert(data.msg);
								}
							},
							error:function(data){
								layer.msg('服务器错误');
								return false;
							}
						});
					}
				});
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}

function setClass(cid) {
	// 跳转到设置不可售地区页面或打开模态框
	layer.msg('设置不可售地区功能暂未实现', {icon: 2});
}

// 设置二级分类提示语 - 教主修改，博客地址：6v6.ren Q群：941535592
function setNotice(cid) {
	// 先获取当前提示语
	$.ajax({
		type: 'POST',
		url: './ajax_class.php?act=getNotice',
		data: {cid: cid},
		dataType: 'json',
		success: function(data) {
			var currentNotice = '';
			if(data.code == 0 && data.data) {
				currentNotice = data.data;
			}
			
			// 弹出输入框
			layer.prompt({
				formType: 2,
				title: '设置二级分类提示语（前台选中时弹窗显示）',
				area: ['500px', '250px'],
				value: currentNotice,
				placeholder: '请输入提示语内容，留空则不显示提示'
			}, function(text, index) {
				layer.close(index);
				var loading = layer.load(2);
				$.ajax({
					type: 'POST',
					url: './ajax_class.php?act=setNotice',
					data: {cid: cid, notice: text},
					dataType: 'json',
					success: function(res) {
						layer.close(loading);
						if(res.code == 0) {
							layer.msg('设置成功！', {icon: 1});
						} else {
							layer.msg(res.msg || '设置失败', {icon: 2});
						}
					},
					error: function() {
						layer.close(loading);
						layer.msg('请求失败，请重试', {icon: 2});
					}
				});
			});
		},
		error: function() {
			layer.msg('获取数据失败', {icon: 2});
		}
	});
}
</script>

<form name="classlist" id="classlist">
  <style>
  /* ========== 分类表格简易风格 ========== */
  #classlist thead>tr>th{background:#fafafa!important;color:#333!important;font-weight:600!important;border-bottom:2px solid #ddd!important;white-space:nowrap}
  #classlist>tbody>tr:hover{background:#f5f5f5!important}
  #classlist .primary-class>td{background:#fff!important;border-bottom:1px solid #eee!important;vertical-align:middle}
  #classlist .sub-class>td{background:#fafafa!important;border-bottom:1px dashed #ddd!important;vertical-align:middle}
  #classlist .btn{border-radius:2px!important}
  #classlist .btn-xs,#classlist .btn-sm{border-radius:2px!important}
  #classlist .btn-primary{background:#337ab7!important;border-color:#2e6da4!important;color:#fff!important}
  #classlist .btn-success{background:#5cb85c!important;border-color:#4cae4c!important;color:#fff!important}
  #classlist .btn-info{background:#5bc0de!important;border-color:#46b8da!important;color:#fff!important}
  #classlist .btn-warning{background:#f0ad4e!important;border-color:#eea236!important;color:#fff!important}
  #classlist .btn-danger{background:#d9534f!important;border-color:#d43f3a!important;color:#fff!important}
  #classlist .btn-default{background:#fff!important;border:1px solid #ccc!important;color:#333!important}
  #classlist .label{border-radius:2px!important;font-weight:400!important}
  #classlist .form-control{border-radius:2px!important;border:1px solid #ccc!important;box-shadow:none!important}
  #classlist .form-control:focus{border-color:#66afe9!important;box-shadow:none!important}
  #classlist .dropdown-menu{border-radius:0!important;box-shadow:0 2px 6px rgba(0,0,0,.15)!important;border:1px solid #ccc!important}
  #classlist select{display:inline-block;width:auto;border-radius:2px!important;border:1px solid #ccc!important}
  </style>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead><tr><th width="40"><input type="checkbox" name="chkAll" id="chkAll" onClick="checkAll(this)"></th><th>排序操作</th><th style="min-width:150px">名称（<?php echo $numrows;?>）</th><th>分类类型</th><th>操作</th></tr></thead>
      <tbody id="classlisttbody">
<?php 
// 显示一级分类及其子分类
foreach ($classes as $res) {
	// 一级分类行
	$activeBtn = $res['active'] == 1 ? '<span class="btn btn-sm btn-success" onclick="setActive('.$res['cid'].',0)">显示</span>' : '<span class="btn btn-sm btn-warning" onclick="setActive('.$res['cid'].',1)">隐藏</span>';
	$classBlockBtn = $conf['classblock'] > 0 ? '<li><a href="javascript:setClass('.$res['cid'].')">设置不可售地区</a></li>' : '';
	
	// 输出一级分类
	 echo '<tr data-cid="' . $res['cid'] . '" class="primary-class">
	<td><input type="checkbox" name="checkbox[]" id="list1" value="' . $res['cid'] . '" onClick="unselectall()"></td>
	<td>
		<a class="btn btn-xs sort_btn" title="移到顶部" onclick="sort('.$res['cid'].',0)"><i class="fa fa-long-arrow-up"></i></a>
		<a class="btn btn-xs sort_btn" title="移到上一行" onclick="sort('.$res['cid'].',1)"><i class="fa fa-chevron-circle-up"></i></a>
		<a class="btn btn-xs sort_btn" title="移到下一行" onclick="sort('.$res['cid'].',2)"><i class="fa fa-chevron-circle-down"></i></a>
		<a class="btn btn-xs sort_btn" title="移到底部" onclick="sort('.$res['cid'].',3)"><i class="fa fa-long-arrow-down"></i></a>
		<a class="btn btn-xs sort_drag" title="拖动排序"><i class="fa fa-sort"></i></a>
		<input type="hidden" name="sort['.$res['cid'].']" value="' . $res['sort'] . '">
	</td>
	<td><input type="text" class="form-control input-sm" name="name['.$res['cid'].']" value="' . htmlspecialchars($res['name'], ENT_QUOTES, 'UTF-8') . '" placeholder="分类名称" required></td>
	<td><span class="label label-primary">一级分类</span></td>
	<td>
		<span class="btn btn-primary btn-sm" onclick="editClass('.$res['cid'].')">修改</span>&nbsp;' . $activeBtn . '&nbsp;<a href="./shoplist.php?cid='.$res['cid'].'" class="btn btn-info btn-sm">商品</a>&nbsp;
		<span class="btn btn-sm btn-danger" onclick="delClass('.$res['cid'].')">删除</span>&nbsp;
		<span class="btn btn-sm btn-default" onclick="addSubClass('.$res['cid'].')">添加子分类</span>&nbsp;
		<div class="btn-group">
			<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">更多 <span class="caret"></span></button>
			<ul class="dropdown-menu dropdown-menu-right text-left">
				<li><a href="./list.php?cid='.$res['cid'].'">查看该分类的订单</a></li>
				<li><a href="javascript:setBlockPay('.$res['cid'].')">设置禁用支付方式</a></li>
				' . $classBlockBtn . '
			</ul>
		</div>
	</td>
  </tr>';
  
  // 显示子分类（如果有）
  if (isset($subClasses[$res['cid']])) {
    foreach ($subClasses[$res['cid']] as $subRes) {
	    $subActiveBtn = $subRes['active'] == 1 ? '<span class="btn btn-sm btn-success" onclick="setActive('.$subRes['cid'].',0)">显示</span>' : '<span class="btn btn-sm btn-warning" onclick="setActive('.$subRes['cid'].',1)">隐藏</span>';
	    $subClassBlockBtn = $conf['classblock'] > 0 ? '<li><a href="javascript:setClass('.$subRes['cid'].')">设置不可售地区</a></li>' : '';
	    
	    // 输出子分类
	    echo '<tr data-cid="' . $subRes['cid'] . '" class="sub-class" style="background-color:#f9f9f9;">
	<td><input type="checkbox" name="checkbox[]" id="list1" value="' . $subRes['cid'] . '" onClick="unselectall()"></td>
	<td style="padding-left:40px;">
			<a class="btn btn-xs sort_btn" title="移到顶部" onclick="sort('.$subRes['cid'].',0)"><i class="fa fa-long-arrow-up"></i></a>
			<a class="btn btn-xs sort_btn" title="移到上一行" onclick="sort('.$subRes['cid'].',1)"><i class="fa fa-chevron-circle-up"></i></a>
			<a class="btn btn-xs sort_btn" title="移到下一行" onclick="sort('.$subRes['cid'].',2)"><i class="fa fa-chevron-circle-down"></i></a>
			<a class="btn btn-xs sort_btn" title="移到底部" onclick="sort('.$subRes['cid'].',3)"><i class="fa fa-long-arrow-down"></i></a>
			<a class="btn btn-xs sort_drag" title="拖动排序"><i class="fa fa-sort"></i></a>
			<input type="hidden" name="sort['.$subRes['cid'].']" value="' . $subRes['sort'] . '">
		</td>
		<td style="padding-left:40px;"><input type="text" class="form-control input-sm" name="name['.$subRes['cid'].']" value="┗━ ' . htmlspecialchars($subRes['name'], ENT_QUOTES, 'UTF-8') . '" placeholder="子分类名称" required></td>
		<td><span class="label label-success">二级分类</span></td>
		<td>
			<span class="btn btn-primary btn-sm" onclick="editClass('.$subRes['cid'].')">修改</span>&nbsp;' . $subActiveBtn . '&nbsp;<a href="./shoplist.php?cid='.$subRes['cid'].'" class="btn btn-info btn-sm">商品</a>&nbsp;
			<span class="btn btn-sm btn-danger" onclick="delClass('.$subRes['cid'].')">删除</span>&nbsp;
			<div class="btn-group">
				<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">更多 <span class="caret"></span></button>
				<ul class="dropdown-menu dropdown-menu-right text-left">
					<li><a href="./list.php?cid='.$subRes['cid'].'">查看该分类的订单</a></li>
					<li><a href="javascript:setBlockPay('.$subRes['cid'].')">设置禁用支付方式</a></li>
					<li><a href="javascript:setNotice('.$subRes['cid'].')"><font color="green">设置提示语</font></a></li>
					' . $subClassBlockBtn . '
				</ul>
			</div>
		</td>
      </tr>';
    }
  }
}?><tr><td><label><input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox">全选</label></td><td><span class="btn btn-primary btn-sm" onclick="saveAll()"><i class="fa fa-floppy-o"></i> 保存全部</span></td><td><input type="text" class="form-control input-sm" name="addname" placeholder="分类名称" required></td><td><span class="btn btn-success btn-sm" onclick="addClass()"><span class="glyphicon glyphicon-plus"></span> 添加分类</span>&nbsp;&nbsp;<a href="./classlist.php?my=classimg" class="btn btn-info btn-sm"><i class="fa fa-picture-o"></i> 修改分类图片</a></td><td><select name="aid"><option selected>批量操作</option><option value="1">&gt;改为显示</option><option value="2">&gt;改为隐藏</option><option value="3">&gt;删除选中</option></select><button type="button" class="btn btn-default btn-sm" onclick="batchOperation()">执行</button></td></tr>
          </tbody>
        </table>
      </div>
	</form>