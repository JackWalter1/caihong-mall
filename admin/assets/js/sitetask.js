
var pagesize = 30;

function listTable(query){
	var url = window.document.location.href.toString();
	var queryString = url.split("?")[1];
	query = query || queryString;
	if(query == 'start' || query == undefined){
		query = '';
		history.replaceState({}, null, './sitetask.php?'+query);
	}else if(query != undefined){
		history.replaceState({}, null, './sitetask.php?'+query);
	}
	layer.closeAll();
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'GET',
		url : 'sitetask-table.php?num='+pagesize+'&'+query,
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
	var kw=$("input[name='kw']").val();
	if(kw==''){
		listTable('start');
	}else{
		listTable('kw='+kw);
	}
	return false;
}
function setActive(id,active) {
	$.ajax({
		type : 'GET',
		url : 'ajax_site.php?act=setTask&id='+id+'&active='+active+'&csrf_token='+encodeURIComponent(csrfToken),
		dataType : 'json',
		success : function(data) {
			listTable();
		},
		error:function(data){
			layer.msg('服务器错误');
		}
	});
}
function delTask(id) {
	var confirmobj = layer.confirm('你确实要删除此分站任务吗？', {
	  btn: ['确定','取消']
	}, function(){
	  $.ajax({
		type : 'GET',
		url : 'ajax_site.php?act=delTask&id='+id+'&csrf_token='+encodeURIComponent(csrfToken),
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