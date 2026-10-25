$(document).ready(function(){
	$("#cid").change(function () {
		var cid = $(this).val();
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$("#tid").empty();
		$("#tid").append('<option value="0">请选择商品</option>');
		$.ajax({
			type : "GET",
			url : "./ajax.php?act=gettool&cid="+cid,
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					var num = 0;
					$.each(data.data, function (i, res) {
						$("#tid").append('<option value="'+res.tid+'">'+res.name+'</option>');
						num++;
					});
					$("#tid").val(0);
					if(num==0 && cid!=0)$("#tid").html('<option value="0">该分类下没有商品</option>');
				}else{
					layer.alert(data.msg);
				}
			},
			error:function(data){
				layer.msg('服务器错误');
				return false;
			}
		});
	});
	$("#task").change(function () {
		var task = $(this).val();
		if(task == 1){
			$("#type").show();
			$("#goods").hide();
			$("#value").attr('placeholder', '输入充值金额达到多少金额');
		}else if(task == 2){
			$("#type").show();
			$("#goods").hide();
			$("#value").attr('placeholder', '输入订单数量达到多少个');
		}else if(task == 3){
			$("#type").show();
			$("#goods").hide();
			$("#value").attr('placeholder', '输入销售金额达到多少金额');
		}else if(task == 4){
			$("#type").show();
			$("#goods").hide();
			$("#value").attr('placeholder', '输入邀请人数达到多少人');
		}else if(task == 5){
			$("#type").hide();
			$("#value").attr('placeholder', '输入连续签到达到多少天');
			$("#goods").hide();
		}else{
			$("#type").show();
			$("#goods").show();
			$("#value").attr('placeholder', '输入指定商品TID和该商品需要多少个订单才算完成');
			if($("#cid").length>0){
				$("#cid").change();
			}
		}
	});
	$("#task").change();
	var items = $("select[default]");
	for (i = 0; i < items.length; i++) {
		$(items[i]).val($(items[i]).attr("default")||0);
	}
});