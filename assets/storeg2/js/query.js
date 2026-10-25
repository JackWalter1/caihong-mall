var $_GET = (function(){
    var url = window.document.location.href.toString();
    var u = url.split("?");
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");
        var get = {};
        for(var i in u){
            var j = u[i].split("=");
            get[j[0]] = j[1];
        }
        return get;
    } else {
        return {};
    }
})();
if($_GET['buyok']){
	var id = $("#order_all>.layui-card:first").find(".xiangqing").data("id");
	var skey = $("#order_all>.layui-card:first").find(".xiangqing").data("skey");
	if(id){
		showOrder(id,skey);
	}

}
function OrderQuery(){
	var kw = $('#query').val();
	window.location.href="./?mod=query&data="+kw;
}
function LastPage(){
	var kw = $('#query').val();
	var page = parseInt($('#page').val());
	var status = $('#q_status').val();
	if(page=='1')return;
	page = page-1;
	window.location.href="./?mod=query&status="+status+"&data="+kw+"&page="+page;
}
function NextPage(){
	var kw = $('#query').val();
	var page = parseInt($('#page').val());
	var status = $('#q_status').val();
	page = page+1;
	window.location.href="./?mod=query&status="+status+"&data="+kw+"&page="+page;
}
function changepwd(id,skey) {
	pwdlayer = layer.open({
	  type: 1,
	  title: '不要修改密码',
	  skin: 'layui-layer-rim',
	  content: '<div class="form-group"><div class="bl_view_title"><div class="input-group-addon">密码</div><input type="text" id="pwd" value="" class="search_input2" placeholder="请填写新的密码" required/></div></div><div class="go_buy"><input type="submit" id="save" onclick="saveOrderPwd('+id+',\''+skey+'\')" class="btn btn-primary btn-block" value="保存"></div>'
	});
}
function saveOrderPwd(id,skey) {
	var pwd=$("#pwd").val();
	if(pwd==''){layer.alert('请确保每项不能为空！');return false;}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "ajax.php?act=changepwd",
		data : {id:id,pwd:pwd,skey:skey},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg('保存成功！');
				layer.close(pwdlayer);
			}else{
				layer.alert(data.msg);
			}
		} 
	});
}
function showOrder(id,skey){
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	var status = ['<span class="label label-primary">待处理</span>',
	'<span class="label label-success">已完成</span>',
	'<span class="label label-warning">处理中</span>',
	'<span class="label label-danger">异常</span>',
	'<font color=red>已退款</font>',
	'',
	'',
	'',
	'<span class="label label-success">已补单</span>',
	'<span class="label label-success">已补单</span>'
	];
	$.ajax({
		type : "POST",
		url : "ajax.php?act=order",
		data : {id:id,skey:skey},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var tempItem = '';
				if(data.list && typeof data.list === "object"){
				    // var orderStateText = item.order_state !== null ? item.order_state : "请与实际情况为准";
					if(typeof data.list.order_state !== "undefined" && data.list.order_state && typeof data.list.now_num !== "undefined"){
						tempItem += '<tr><td><span style="color:red;">订单进度</span></td><td>开始数量：' + data.list.start_num + '<br>下单数量：<font color="blue">' + data.list.num + '</font><br>当前数量：' + data.list.now_num + '<br>实时状态：<font color="blue">' + data.list.order_state + '</font></td><td>完成数量：<font style="color:blue">' + (data.list.now_num - data.list.start_num) + '</font></td></tr>';
                    
					}else{
				// 		tempItem += '<tr><td colspan="6" style="text-align:center" class="orderTitle"><b><font color="red">↓↓↓ 订单实时进度看下面 ↓↓↓</font></b></td>';
						$.each(data.list, function(i, v){
				// 			tempItem += '<tr><td class="warning orderTitle">'+i+'</td><td class="orderContent">'+v+'</td></tr>';
				tempItem += '<tr><td class="warning orderTitle"><span style="color:red;">订单进度</span></td><td class="orderContent"><font style="color:blue">'+v+'</font></td><td>无</td></tr>';
						});
					}
					
					if(typeof data.bd == "undefined"){
					    data.bd='无';
					}
					if(typeof data.result !== "undefined" && data.result){
							tempItem += '<tr style="border-bottom: 3px solid black;"><td class="warning orderTitle">异常信息</td><td class="orderContent">'+data.result+'</td><td>'+data.bd+'</td></tr>';
                    
					    if(data.bd.indexOf('异常')!= -1){ tempItem += '<tr style="border-bottom: 3px solid black;"><td class="warning orderTitle">提示</td><td class="orderContent">请再次把订单编号复制发给客服补单</td><td>无</td></tr>'; }98
					}else if(typeof status[data.status] == "string" && (status[data.status].indexOf('<span class="label label-danger">异常</span>')!=-1) || typeof data.list.订单状态 == "string" && (data.list.订单状态.indexOf('补')!=-1)){
                     tempItem += '<tr style="border-bottom: 3px solid black;"><td class="warning orderTitle"><b>异常信息</b></td><td class="orderContent">可以点击申请退款，退不了可联系客服</td><td>无</td></tr>';
                        }
                        
				}else if(data.kminfo){
					tempItem += '<tr style="border-bottom: 3px solid black;"><td class="warning orderTitle"><b><span style="color:red;">卡密信息</span></b></td><td class="orderContent">'+data.kminfo+'</td><td>无</td></tr>';
				}else if(data.result){
					tempItem += '<tr style="border-bottom: 3px solid black;"><td class="warning orderTitle"><b><span style="color:red;">处理结果</span></b></td><td class="orderContent">'+data.result+'</td><td>无</td></tr>';
				}
				var tdstr='<button style="margin:0;height:2em;background-color: red;" class="btn btn-warning btn-xs" onclick="tuikuan('+id+');">申请退款</button>';
				var item = `
                <table class="layui-table layui-text" lay-size="sm" lay-skin="row">
                        <colgroup>
                            <col width="30%">
                            <col width="50%">    <col width="20%">
                            <col>
                          </colgroup>
                          <thead>
                            <tr>
                              <th>类型</th>
                              <th>状态</th>      
                              <th>操作</th>
                            </tr> 
                          </thead>
                          <tbody>
                          <tr>
                              <td>订单编号</td>
                              <td id = "copyinput"><sp style="color:red">` + id + `</b></td>
							  <td><span style="margin:0;height:2em;" id="btncopy" onclick="copy(false,'')"  class="btn btn-warning btn-xs">复制单号</span></td>
                            </tr>
							<tr>
                              <td>商品名称</td>
                              <td >` + data.name + `</td>
                              
                            </tr>
                            <tr style="border-top: 2px solid black;">
                              <td>订单状态</td>
                              <td >` + status[data.status] + `</td><td>无</td>
                            </tr>
							`+tempItem+`
							<tr style="border-top: 2px solid black;">
                              <td>商品金额</td>
                              <td >` + data.money+ `元</td><td>`+tdstr+`</td>
                            </tr>
							<tr>
                              <td>下单信息</td>
                              <td id = "copyinput1" style="word-break: break-all;">` +data.inputs+ `</td><td><span style="margin:0;height:2em;" id="btncopy1" onclick="copy1(false,'')" class="btn btn-warning btn-xs">复制保存</span></td>
                            </tr>
							
							<tr>
                              <td>购买时间</td>
                              <td >` +data.date+ `</td><td>无</td> `;
							  
							item += '<tr><td>订单操作</td><td ><a onclick="return apply_refund('+id+',\''+skey+'\')" class="btn btn-xs btn-danger">申请退款</a>&nbsp <a href="./user/jiameng.php" class="btn btn-xs btn-danger">加盟赚钱</a></tr>'
                
							  item +=`</td></tr>`


				if(data.desc){
					item += '<tr style="border-top: 2px solid black;"><td colspan="6" style="text-align:center" class="orderTitle"><b><font color="#1E9FFF">↓↓↓ 必看商品介绍(可滑动) ↓↓↓</font></b></td><tr><td style="width:100%;height:100%;max-width:initial;" colspan="6" class="orderContent">'+data.desc+'</td></tr>';
				}
				item += '</table>';
				var area = [$(window).width() > 480 ? '480px' : '100%', ';max-height:100%'];
				layer.open({
				  type: 1,
				  area: area,
				  title: '<span style="color: red;">订单详情-（如遇任何问题请咨询平台客服）</span>',
				  skin: 'layui-layer-rim',
				  zIndex: 2001,
				  content: item,
				  btn: ['在线客服', '关闭'],
                  btnAlign: 'r',
                  closeBtn: '<a href="javascript:;" class="layui-layer-close layui-layer-close1" style="font-size: 20px; color: #666;">X</a>',
                  shadeClose: true,
                  yes: function(index, layero) {
                 layer.close(index);
                 },
                success: function(layero, index) {
                var btn = layero.find('.layui-layer-btn0');
                btn.css('background-color', '#1E9FFF');
                btn.attr('href', './?mod=kf');
                  }
				});
			}else{
				layer.alert(data.msg);
			}
		}
	});
}
function copy(shoptip, tipstr) {
    var Url2 = document.getElementById("copyinput").innerText;
    var oInput = document.createElement('input');
    oInput.value = Url2;
    document.body.appendChild(oInput);
    oInput.select(); // 选择对象
    document.execCommand("Copy"); // 执行浏览器复制命令
    oInput.className = 'oInput';
    oInput.style.display = 'none';
    if (shoptip == true) {
        layer.closeAll();
        swal({
            title: '恭喜',
            type: 'success',
            html: tipstr,
            confirmButtonText: '好的',
        });
    } else {
        layer.tips('复制成功', '#btncopy', {
            tips: [1, '#ff8000'],
            time: 800
        });
    }
}
function copy1(shoptip, tipstr) {
    var Url2 = document.getElementById("copyinput1").innerText;
    var oInput = document.createElement('input');
    const result = Url2.replace(/.*：/, '');
    oInput.value = result;
    document.body.appendChild(oInput);
    oInput.select(); // 选择对象
    document.execCommand("Copy"); // 执行浏览器复制命令
    oInput.className = 'oInput';
    oInput.style.display = 'none';
    if (shoptip == true) {
        layer.closeAll();
        swal({
            title: '恭喜',
            type: 'success',
            html: tipstr,
            confirmButtonText: '好的',
        });
    } else {
        layer.tips('复制成功', '#btncopy1', {
            tips: [1, '#ff8000'],
            time: 800
        });
    }
}
function checklogin(islogin){
	if(islogin==1){
		return true;
	}else{
		var confirmobj = layer.confirm('为方便反馈处理结果，投诉订单前请先登录网站！', {
		  btn: ['登录','注册','取消']
		}, function(){
			window.location.href='./user/login.php';
		}, function(){
			window.location.href='./user/reg.php';
		}, function(){
			layer.close(confirmobj);
		});
		return false;
	}
}
function apply_refund(id,skey){
	var confirmobj = layer.confirm('待处理或异常状态订单可以申请退款，退款之后资金会退到用户余额，是否确认退款？', {
	  btn: ['确认退款','取消']
	}, function(){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=apply_refund",
			data : {id:id,skey:skey},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					layer.alert('成功退款'+data.money+'元到余额！', {icon:1}, function(){ window.location.reload(); });
				}else{
					layer.alert(data.msg, {icon:2});
				}
			}
		});
	}, function(){
		layer.close(confirmobj);
	});
}
function tuikuan(id){
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : "GET",
        url : "tuidan2.php?id="+id,
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 8){
                layer.confirm(data.msg, {
                    icon: 2,
                    btnAlign: 'c',
                    btn: ['没有账户？点我注册', '立即登录']
                },
                function(index) {
                    layer.close(index);
                    user_reg();
                },
                function() {
                    user_login();
                });
            }else if(data.code != 0){
                layer.alert(data.msg);
            }else{
                layer.alert(data.msg, {icon:2});
            }
        }
    });
}
function user_login() {
	layer.open({
		title: '在线登录账户 - 登录后即可使用自助退単',
		type: 2,
		maxmin: true,
		shadeClose: true,
		area: ["100%", "100%"],
		skin: 'layui-layer-rim',
		content: ['./user/login.php'],
		btn: '登录完成，关闭窗口',
	});
}
function user_reg() {
	layer.open({
		title: '在线登录账户 - 注册后即可使用自助退単',
		type: 2,
		maxmin: true,
		shadeClose: true,
		area: ["100%", "100%"],
		skin: 'layui-layer-rim',
		content: ['./user/reg.php'],
		btn: '注册完成，关闭窗口',
	});
}

var styleTag = document.createElement('style'); styleTag.innerHTML = '.layui-table img { width: 100%; max-width: none; }'; document.head.appendChild(styleTag);

