$(document).ready(function(){
	$("#cid").change(function () {
		var cid = $(this).val();
		if (cid == 0) {
			$("#tid").empty().append('<option value="0">请选择商品</option>');
			return;
		}
		$.ajax({
			type: "POST",
			url: "ajax_shop.php",
			data: { act: "getTools", cid: cid },
			dataType: "json",
			success: function (data) {
				if (data.code == 0) {
					var html = '<option value="0">请选择商品</option>';
					$.each(data.data, function (i, item) {
						html += '<option value="' + item.tid + '">' + item.name + '</option>';
					});
					$("#tid").empty().append(html);
				} else {
					layer.msg(data.msg);
				}
			},
			error: function (data) {
				layer.msg('服务器错误');
			}
		});
	});
	$("#cid").trigger("change");
})