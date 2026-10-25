var shoplist;
function selectAll(checkbox) {
	$('input[type=checkbox]').prop('checked', $(checkbox).prop('checked'));
}
$(document).ready(function(){
	$("#getGoods").click(function(){
		var shequ=$("select[name='shequ']").val();
		if(shequ==''){layer.alert('请先选择一个对接网站');return false;}
		$('#goodslist').empty();
		shoplist = new Array();
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax_shop.php?act=getBatchGoodsList",
			data : {shequ:shequ},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					$('#getGoods').attr('type',data.type);
					var num = 0;
					$.each(data.data, function(i, item){
						shoplist[item.goods_id] = item;
						$('#goodslist').append('<label class="checkbox-inline"><input type="checkbox" name="batchgoods[]" value="'+item.goods_id+'"> '+item.name+'</label>');
						num++;
					});
					layer.msg('共获取到'+num+'个可一键上架的商品');
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
});
function BatchGoods(obj){
	var shequ=$("select[name='shequ']").val();
	var prid=$("select[name='prid']").val();
	if(shequ==''){layer.alert('请先选择一个对接网站');return false;}
	if(prid==0){layer.alert('请先选择一个加价模板');return false;}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax_shop.php?act=batch_goodsid',
		data : $(obj).serialize(),
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var index = layer.msg('开始上架商品,本次共需上架商品数量为：' + data.num + '个！', {
					icon: 16,
					time: 999999,
					shade: [0.5, '#000']
				});
				batch_conversion(data.goodsid_list, data.num, 0);
			}else{
				layer.alert(data.msg, {icon: 2})
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
	return false;
}
function batch_conversion(goodsid_list, num, sum){
	if (num <= sum) {
		layer.close();
		var cid=$("select[name='cid']").val();
		layer.alert('恭喜你,商品已经全部上架完！<br>本次共成功上架' + num + '个商品！', {
			anim: 4,
			icon: 1,
			title: '商品上架成功提醒！',
			btn: ['确定', '查看上架的商品'],
			shade: [0.5, '#000'],
			btn2: function (layero, index) {
				window.open('shoplist.php?cid=' + cid);
			}
		});
		return false;
	}

	var goods_id = goodsid_list.split('|')[sum];

	setTimeout(function () {
		batch_merchandise(goods_id, goodsid_list, shoplist[goods_id], num, (sum + 1));
	}, 1100);
}
function batch_merchandise(goods_id, goodsid_list, data, num, sum){
	var shequ=$("select[name='shequ']").val();
	var cid=$("select[name='cid']").val();
	var prid=$("select[name='prid']").val();
	var delay=$("input[name='delay']").val();
	$.ajax({
		type : 'POST',
		url : 'ajax_shop.php?act=batch_merchandise',
		data: {
			shequ: shequ,
			goods_id: goods_id,
			goods_type: data['goods_type'],
			goods_param: data['goods_param'],
			cid: cid,
			name: data['name'],
			prid: prid,
			price: data['price'],
			input: data['input'],
			inputs: data['inputs'],
			desc: data['desc'],
			alert: data['alert'],
			shopimg: data['shopimg'],
			min: data['min'],
			max: data['max']
		},
		dataType : 'json',
		success : function(res) {
			layer.close();
			if(res.code == 0){
				setTimeout(function () {
					var index = layer.msg('正在上架第' + (sum) + '个商品<font color=#228b22>[' + res.name + ']</font><br>共需上架：' + num + '个商品！', {
						icon: 16,
						time: 999999,
						shade: [0.5, '#000'],
						anim: Math.ceil(Math.random() * 6)
					});
					batch_conversion(goodsid_list, num, sum);
				}, delay);
			} else {
				layer.alert(res.msg + '<br>可能可以在网站维护界面修复,前两个按钮！', {icon: 2})
			}
		},
		error:function(res){
			layer.msg('失败！');
			return false;
		}
	});
	return false;
}