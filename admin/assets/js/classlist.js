// 教主博客 zhonguo.ren QQ群915043052
// 加载分类列表并初始化拖拽功能
function listTable(query){
	var url = window.document.location.href.toString();
	var queryString = url.split("?")[1];
	query = query || queryString;
	layer.closeAll();
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'GET',
		url : 'classlist-table.php?'+query,
		dataType : 'html',
		cache : false,
		success : function(data) {
			layer.close(ii);
			// 检查数据是否为空，防止页面空白
			if(!data || $.trim(data) === '') {
				$("#listTable").html('<div class="alert alert-warning">暂无分类数据</div>');
				return;
			}
			$("#listTable").html(data);
			
			// 初始化拖拽排序功能 - 教主博客 zhonguo.ren 915043052
			initDragSort();
		},
		error:function(data){
			layer.close(ii);
			layer.msg('加载分类列表失败，请刷新页面重试');
			$("#listTable").html('<div class="alert alert-danger">加载失败，请刷新页面重试</div>');
			return false;
		}
	});
}

// 初始化HTML5原生拖拽排序功能 - 教主博客 zhonguo.ren 915043052
function initDragSort() {
	try {
		// 添加拖拽样式 - 教主博客 zhonguo.ren 915043052
		if (!$('style#dragSortStyle').length) {
			$('head').append(`
				<style id="dragSortStyle">
					.sort_drag {
						cursor: grab;
						user-select: none;
						padding: 6px 10px;
						border-radius: 4px;
						background-color: #f8f9fa;
						transition: background-color 0.2s;
					}
					.sort_drag:hover {
						background-color: #e9ecef;
					}
					.sort_drag:active {
						cursor: grabbing;
						background-color: #dee2e6;
					}
					.dragging {
						opacity: 0.5;
						background-color: #e3f2fd !important;
					}
					.drag-over {
						border-top: 2px solid #007bff;
					}
					.drag-placeholder {
						background-color: #e3f2fd;
						border: 2px dashed #007bff;
						min-height: 40px;
					}
				</style>
			`);
		}
		
		// 检查表格和行是否存在，避免空指针错误
		if ($("#classlisttbody").length === 0 || $("#classlisttbody tr").length === 0) {
			console.log('表格不存在或无数据，跳过拖拽初始化');
			return;
		}
		
		// 为一级分类和子分类添加拖拽功能
		$(".primary-class, .sub-class").each(function() {
			var row = $(this);
			var dragHandle = row.find(".sort_drag");
			
			// 设置行可拖拽
			row.attr('draggable', 'true');
			
			// 拖拽开始事件
			row.on('dragstart', function(e) {
				// 存储被拖拽元素的ID
				e.originalEvent.dataTransfer.setData('text/plain', row.attr('data-cid'));
				// 添加拖拽中样式
				row.addClass('dragging');
				// 设置拖拽图像
				e.originalEvent.dataTransfer.effectAllowed = 'move';
			});
			
			// 拖拽结束事件
			row.on('dragend', function() {
				// 移除所有拖拽相关样式
				row.removeClass('dragging');
				$(".primary-class, .sub-class").removeClass('drag-over');
				$(".drag-placeholder").remove();
				
				// 拖拽完成后更新排序
				updateSortNumbers();
			});
			
			// 拖拽经过事件
			row.on('dragover', function(e) {
				e.preventDefault(); // 允许放置
				// 确定是同一类别的元素之间拖拽
				var currentClass = row.hasClass('primary-class') ? 'primary-class' : 'sub-class';
				if (e.originalEvent.dataTransfer.getData('text/plain')) {
					row.addClass('drag-over');
				}
			});
			
			// 拖拽离开事件
			row.on('dragleave', function() {
				row.removeClass('drag-over');
			});
			
			// 拖拽放置事件
			row.on('drop', function(e) {
				e.preventDefault();
				row.removeClass('drag-over');
				
				var draggedId = e.originalEvent.dataTransfer.getData('text/plain');
				var draggedRow = $("tr[data-cid='" + draggedId + "']");
				
				// 确保被拖拽元素存在且不是自身
				if (draggedRow.length > 0 && draggedRow[0] !== row[0]) {
					// 获取当前行和被拖拽行的类型
					var currentIsPrimary = row.hasClass('primary-class');
					var draggedIsPrimary = draggedRow.hasClass('primary-class');
					
					// 确保同类型元素之间才能拖拽交换
					if (currentIsPrimary === draggedIsPrimary) {
						// 执行行交换
						if (draggedRow.index() < row.index()) {
							// 被拖拽行在当前行前面，插入到当前行后面
							row.after(draggedRow);
						} else {
							// 被拖拽行在当前行后面，插入到当前行前面
							row.before(draggedRow);
						}
					}
				}
			});
		});
		
		console.log('HTML5原生拖拽功能初始化完成');
	} catch (e) {
		console.error('拖拽初始化失败:', e);
		layer.msg('拖拽功能初始化失败', {icon: 2});
	}
}

// 更新排序号并保存 - 教主博客 zhonguo.ren 915043052
function updateSortNumbers() {
	try {
		console.log('开始更新排序...');
		// 获取所有一级分类并按当前DOM顺序排序
		var primaryClasses = $("#classlisttbody .primary-class");
		var primarySort = 1;
		
		// 遍历一级分类，更新排序号
		primaryClasses.each(function(index) {
			var cid = $(this).attr('data-cid');
			if (cid) {
				var sortInput = $("input[name='sort["+cid+"]']");
				if (sortInput.length > 0) {
					sortInput.val(primarySort);
					console.log('一级分类排序更新:', cid, '=>', primarySort);
				}
			}
			
			// 为当前一级分类下的子分类排序
			var subSort = 1;
			// 获取当前一级分类之后，下一个一级分类之前的所有子分类
			var nextPrimary = primaryClasses.eq(index + 1);
			var subClasses;
			
			if (nextPrimary.length > 0) {
				// 如果有下一个一级分类，获取它们之间的子分类
				subClasses = $(this).nextUntil(nextPrimary, ".sub-class");
			} else {
				// 如果是最后一个一级分类，获取之后所有子分类
				subClasses = $(this).nextAll(".sub-class");
			}
			
			// 更新子分类排序号
			subClasses.each(function() {
				var subCid = $(this).attr('data-cid');
				if (subCid) {
					var subSortInput = $("input[name='sort["+subCid+"]']");
					if (subSortInput.length > 0) {
						// 使用一级分类排序号 * 100 + 子分类排序号，确保层级关系
						var newSort = (primarySort * 100) + subSort;
						subSortInput.val(newSort);
						console.log('子分类排序更新:', subCid, '=>', newSort);
					}
				}
				subSort++;
			});
			
			primarySort++;
		});
		
		// 显示成功提示并保存
		layer.msg('排序已更新', {icon: 1, time: 1000});
		
		// 延迟保存，让用户看到提示
		setTimeout(function() {
			console.log('保存排序数据...');
			saveAll();
		}, 600);
	} catch (e) {
		console.error('更新排序失败:', e);
		layer.msg('排序更新失败，请重试', {icon: 2});
	}
}

// 显示排序成功提示 - 教主博客 zhonguo.ren 915043052
function showSortSuccess() {
	layer.msg('排序已更新', {
		icon: 1,
		time: 1000
	});
}
function setActive(cid,active) {
	$.ajax({
		type : 'GET',
		url : 'ajax_class.php?act=setClass&cid='+cid+'&active='+active,
		dataType : 'json',
		success : function(data) {
			listTable();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function sort(cid,sort) {
	$.ajax({
		type : 'GET',
		url : 'ajax_class.php?act=setClassSort&cid='+cid+'&sort='+sort,
		dataType : 'json',
		success : function(data) {
			listTable();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function getImage(cid) {
	layer.confirm('是否从该分类下的商品图片获取一张作为分类图片？', {
		btn: ['确定'] //按钮
	}, function(){
	$.ajax({
		type : 'GET',
		url : 'ajax_class.php?act=getClassImage&cid='+cid,
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.msg('获取图片成功');
				$("input[name='img"+cid+"']").val(data.url);
			}else{
				layer.alert('该分类下商品都没有图片');
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
	});
}
function addClass() {
	var name = $("input[name='addname']").val();
	$.ajax({
		type : 'POST',
		url : 'ajax_class.php?act=addClass',
		data : {name:name},
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.msg('添加成功');
				listTable();
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
function editClass(cid) {
	var name = $("input[name='name["+cid+"]']").val();
	$.ajax({
		type : 'POST',
		url : 'ajax_class.php?act=editClass&cid='+cid,
		data : {name:name},
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.msg('修改成功');
				listTable();
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
function delClass(cid) {
	var confirmobj = layer.confirm('你确实要删除此分类和分类下全部商品吗？', {
	  btn: ['确定','取消']
	}, function(){
	  $.ajax({
		type : 'GET',
		url : 'ajax_class.php?act=delClass&cid='+cid,
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.msg('删除成功');
				listTable();
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	  });
	}, function(){
	  layer.close(confirmobj);
	});
}
function saveAll() {
	// 检查表单是否存在
	if ($('#classlist').length === 0) {
		console.error('保存失败：表单不存在');
		return;
	}
	
	// 获取表单数据
	var formData = $('#classlist').serialize();
	console.log('保存数据:', formData);
	
	$.ajax({
		type : 'POST',
		url : 'ajax_class.php?act=editClassAll',
		data : formData,
		dataType : 'json',
		timeout: 10000, // 设置超时时间
		success : function(data) {
			console.log('保存响应:', data);
			if (data && data.code == 0) {
				// 使用layer.msg替代alert，提供更好的用户体验
				layer.msg('保存成功！', {icon: 1});
				// 延时刷新列表，让用户有时间看到成功提示
				setTimeout(function() {
					listTable();
				}, 500);
			} else {
				layer.msg(data && data.msg ? data.msg : '保存失败', {icon: 2});
			}
		},
		error:function(xhr, status, error){
			console.error('保存失败:', status, error);
			layer.msg('服务器错误，请稍后重试', {icon: 2});
			return false;
		}
	});
}
function saveAllImages() {
	$.ajax({
		type : 'POST',
		url : 'ajax_class.php?act=editClassImages',
		data : $('#classlist').serialize(),
		dataType : 'json',
		success : function(data) {
			alert('保存成功！');
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function fileSelect(cid){
	$("#file"+cid).trigger("click");
}
function fileView(cid){
	var shopimg = $("input[name='img["+cid+"]']").val();
	if(shopimg=='') {
		layer.alert("请先上传图片，才能预览");
		return;
	}
	if(shopimg.indexOf('http') == -1)shopimg = '../'+shopimg;
	layer.open({
		type: 1,
		area: ['360px', '400px'],
		title: '分类图片查看',
		shade: 0.3,
		anim: 1,
		shadeClose: true,
		content: '<center><img width="300px" src="'+shopimg+'"></center>'
	});
}
function fileUpload(cid){
	var fileObj = $("#file"+cid)[0].files[0];
	if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
		return;
	}
	var formData = new FormData();
	formData.append("do","upload");
	formData.append("type","class");
	formData.append("file",fileObj);
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		url: "ajax.php?act=uploadimg",
		data: formData,
		type: "POST",
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		success: function (data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg('上传图片成功');
				$("input[name='img["+cid+"]']").val(data.url);
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	})
}
function setClass(cid) {
	$.ajax({
		type : 'POST',
		url : 'ajax_class.php?act=getBlock',
		data : {cid:cid},
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.open({
					area: ['360px'],
					title: '不可售地区设置（多个城市用,分隔）',
					content: '<div class="form-group"><textarea class="form-control" name="blockcontent" placeholder="示例：北京市,广东省深圳市" rows="3">'+data.data+'</textarea></div>',
					yes: function(){
						var content = $("textarea[name='blockcontent']").val();
						$.ajax({
							type : 'POST',
							url : 'ajax_class.php?act=setBlock',
							data : {cid:cid,data: content.replace("，",",")},
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
						var content = $("textarea[name='blockcontent']").val();
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
$(document).ready(function(){
	if($("#listTable").length>0){
		listTable()
	}
})