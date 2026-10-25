
var pagesize = 30;

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
	if(document.form1.chkAll1.checked){
		document.form1.chkAll1.checked = document.form1.chkAll1.checked&0;
		checkflag = "false";
	}
}

function batchOperation() {
	var aid = $("select[name='aid']").val();
	var checkbox = $("input[name='checkbox[]']:checked");
	
	if(checkbox.length == 0){
		layer.msg('请至少选择一条记录', {icon: 2});
		return false;
	}
	
	if(aid == 1){
		layer.confirm('确定要删除选中的 ' + checkbox.length + ' 条记录吗？删除后将无法恢复！', {
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
		url : 'ajax.php?act=batchToolLogOperation',
		data : $('input[name="checkbox[]"]:checked').serialize() + '&aid=' + $("select[name='aid']").val(),
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg(data.msg, {icon: 1});
				setTimeout(function() {
					listTable();
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

var dstatus = 0;
function listTable(query){
	var url = window.document.location.href.toString();
	var queryString = url.split("?")[1];
	query = query || queryString;
	if(query == 'start' || query == undefined){
		query = '';
		history.replaceState({}, null, './toollogs.php?'+query);
	}else if(query != undefined){
		history.replaceState({}, null, './toollogs.php?'+query);
	}
	layer.closeAll();
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'GET',
		url : 'toollogs-table.php?num='+pagesize+'&'+query,
		dataType : 'html',
		cache : false,
		success : function(data) {
			layer.close(ii);
			$("#listTable").html(data)
		},
		error:function(data){
			layer.msg('服务器错误');
		}
	});
}
function searchItem(){
	var column=$("select[name='column']").val();
	var kw=$("input[name='kw']").val();
	var type=$("select[name='type']").val();
	if(kw==''){
		listTable('type='+type);
	}else{
		listTable('type='+type+'&column='+column+'&kw='+kw);
	}
	return false;
}
function clearItem(){
	$("input[name='kw']").val('');
	$("select[name='type']").val('all');
	listTable('start')
}
function delOrder(id) {
	var confirmobj = layer.confirm('你确定要删除此记录吗？', {
	  btn: ['确定','取消']
	}, function(){
	  $.ajax({
		type : 'POST',
		url : 'ajax.php?act=delToolLog',
		data : {id: id},
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
		}
	  });
	}, function(){
	  layer.close(confirmobj);
	});
}
$(document).ready(function(){
	listTable();
	$("#pagesize").change(function () {
		var size = $(this).val();
		pagesize = size;
		listTable();
	});
})