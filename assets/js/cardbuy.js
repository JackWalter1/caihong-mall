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

// 移除全局错误处理函数，避免捕获不必要的错误

// 确保hashsalt变量存在，避免未定义错误
if (typeof hashsalt === 'undefined') {
    hashsalt = '';
}

// 移除cookie依赖，使用直接赋值替代
function getMySid() {
    return '';
}

// 移除cookie依赖，不再保存邮箱到cookie
function saveEmail(email) {
    // 不使用cookie，直接返回
    return;
}

// 移除cookie依赖，直接返回空字符串
function getSavedEmail() {
    return '';
}
// getPoint() function removed as it's no longer used. Input fields are now generated directly in the #submit_card click event.
function get_shuoshuo(id,uin,km,page){
	km = km || 0;
	page = page || 1;
	if(uin==''){
		layer.alert('请先填写QQ号！');return false;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "GET",
		url : "ajax.php?act=getshuoshuo&uin="+uin+"&page="+page+"&hashsalt="+hashsalt,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var addstr='';
				$.each(data.data, function(i, item){
					addstr+='<option value="'+item.tid+'">'+item.content+'</option>';
				});
				var nextpage = page+1;
				var lastpage = page>1?page-1:1;
				if($('#show_shuoshuo').length > 0){
					$('#show_shuoshuo').html('<div class="input-group"><div class="input-group-addon onclick" title="上一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><i class="fa fa-chevron-left"></i></div><select id="shuoid" class="form-control" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select><div class="input-group-addon onclick" title="下一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><i class="fa fa-chevron-right"></i></div></div>');
				}else{
					$('#inputsname').append('<div class="form-group" id="show_shuoshuo"><div class="input-group"><div class="input-group-addon onclick" title="上一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><i class="fa fa-chevron-left"></i></div><select id="shuoid" class="form-control" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select><div class="input-group-addon onclick" title="下一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><i class="fa fa-chevron-right"></i></div></div></div>');
				}
				set_shuoshuo(id);
			}else{
				layer.alert(data.msg);
			}
		},
		error: function () {
			layer.close(ii);
			layer.alert('请重试一遍即可', {icon: 6});
		}
	});
}
function set_shuoshuo(id){
	var shuoid = $('#shuoid').val();
	$('#'+id).val(shuoid);
}
function get_rizhi(id,uin,km,page){
	km = km || 0;
	page = page || 1;
	if(uin==''){
		layer.alert('请先填写QQ号！');return false;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "GET",
		url : "ajax.php?act=getrizhi&uin="+uin+"&page="+page+"&hashsalt="+hashsalt,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var addstr='';
				$.each(data.data, function(i, item){
					addstr+='<option value="'+item.blogId+'">'+item.title+'</option>';
				});
				var nextpage = page+1;
				var lastpage = page>1?page-1:1;
				if($('#show_rizhi').length > 0){
					$('#show_rizhi').html('<div class="input-group"><div class="input-group-addon onclick" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><i class="fa fa-chevron-left"></i></div><select id="blogid" class="form-control" onchange="set_rizhi(\''+id+'\');">'+addstr+'</select><div class="input-group-addon onclick" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><i class="fa fa-chevron-right"></i></div></div>');
				}else{
					$('#inputsname').append('<div class="form-group" id="show_rizhi"><div class="input-group"><div class="input-group-addon onclick" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><i class="fa fa-chevron-left"></i></div><select id="blogid" class="form-control" onchange="set_rizhi(\''+id+'\');">'+addstr+'</select><div class="input-group-addon onclick" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><i class="fa fa-chevron-right"></i></div></div></div>');
				}
				set_rizhi(id);
			}else{
				layer.alert(data.msg);
			}
		},
		error: function () {
			layer.close(ii);
			layer.alert('请重试一遍即可', {icon: 6});
		}
	});
}
function set_rizhi(id){
	var blogid = $('#blogid').val();
	$('#'+id).val(blogid);
}
function getsongid(){
	var songurl=$("#inputvalue").val();
	if(songurl==''){layer.alert('请确保每项不能为空！');return false;}
	if(songurl.indexOf('.qq.com')<0){layer.alert('请输入正确的歌曲的分享链接！');return false;}
	try{
		var songid = songurl.split('s=')[1].split('&')[0];
		layer.msg('ID获取成功！下单即可');
	}catch(e){
		layer.alert('请输入正确的歌曲的分享链接！');return false;
	}
	$('#inputvalue').val(songid);
}
function getsharelink(){
	var songurl=$("#inputvalue").val();
	if(songurl==''){layer.alert('请确保每项不能为空！');return false;}
	if(songurl.indexOf('http')<0){layer.alert('请输入正确的内容！');return false;}
	try{
		if(songurl.indexOf('http://')>=0){
			var songid = 'http://' + songurl.split('http://')[1].split(' ')[0].split('，')[0];
		}else if(songurl.indexOf('https://')>=0){
			var songid = 'https://' + songurl.split('https://')[1].split(' ')[0].split('，')[0];
		}
		if(songid != $("#inputvalue").val())layer.msg('链接转换成功！下单即可');
	}catch(e){
		layer.alert('请输入正确的内容！');return false;
	}
	$('#inputvalue').val(songid);
}
function getshareid(){
	var songurl=$("#inputvalue").val();
	if(songurl==''){layer.alert('请确保每项不能为空！');return false;}
	if(songurl.indexOf('http')<0){layer.alert('请输入正确的内容！');return false;}
	try{
		if(songurl.indexOf('http://')>=0){
			var songurl = 'http://' + songurl.split('http://')[1].split(' ')[0].split('，')[0];
		}else if(songurl.indexOf('https://')>=0){
			var songurl = 'https://' + songurl.split('https://')[1].split(' ')[0].split('，')[0];
		}else{
			throw false;
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=getshareid",
			data : {url:songurl, hashsalt:hashsalt},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					$('#inputvalue').val(data.songid);
					if(typeof data.songid2 != "undefined" && $('#inputvalue2').length>0)$('#inputvalue2').val(data.songid2);
					layer.msg('ID获取成功！下单即可');
				}else{
					layer.alert(data.msg);return false;
				}
			},
			error: function () {
				layer.close(ii);
				layer.alert('请重试一遍即可', {icon: 6});
			}
		});
	}catch(e){
		layer.alert('请输入正确的内容！');return false;
	}
}
function getshareid2(id, songurl){
	if(songurl==''){layer.alert('请确保每项不能为空！');return false;}
	if(songurl.indexOf('http')<0){return false;}
	getshareid();
}
function getpddinput() {
    var result = "";
    var pddinput = $("#inputvalue").val();
    if (pddinput == '') {
        return false;
    }
    if (pddinput.indexOf("PinDuoDuo") != -1 && pddinput.indexOf("http") === -1) {
        pddinput = pddinput.replace("PinDuoDuo", "");
    }
    var pattresult = (/[a-zA-Z0-9=_\&\?\-\/]?[a-zA-Z0-9]{16}[a-zA-Z0-9=_\&\?\-\/]?/).exec(pddinput);
    var patt_str = (/τ[a-zA-Z0-9]{13}τ/).exec(pddinput);
    var pattresult1 = (/(^[a-zA-Z0-9][a-zA-Z0-9]+)点+/).exec(pddinput);
    var pattresult2 = (/(^[0-9]{15})/).exec(pddinput);
    var pattresult3 = (/([0-9]{15}$)/).exec(pddinput);
    var pattresult4 = (/[0-9]{6,20}/).exec(pddinput);
    var pattresult5 = (/(http|https):\/\/[\w\.\=\_\/\-\$\&\!\?\(\)#%+:;]+/).exec(pddinput);
    var pattresult6 = (/([0-9]{8})/).exec(pddinput);
    var pattresult7 = (/[a-zA-Z0-9=_\&\?\-\/]?[a-zA-Z0-9]{15}[a-zA-Z0-9=_\&\?\-\/]?/).exec(pddinput);
    var pattresult12 = (/^[a-zA-Z0-9]{16}/).exec(pddinput);
    
    var pattresult10 = (/[\ud83a-\ud83f][\u0000-\uFFFF]/).exec(pddinput);
    var no_emoji_input = pddinput.replace(/[\ud83a-\ud83f][\u0000-\uFFFF]/g, "");
    no_emoji_input = no_emoji_input.replace(/[\ufe00-\ufe0f]/g, "");
    no_emoji_input = no_emoji_input.replace(/[\u0000-\uffff][\u20aa-\u20ff]/g, "");
    
    var pattresult13 = (/[a-zA-Z0-9]{13}/).exec(no_emoji_input);
    var pattresult14 = (/[a-zA-Z0-9]{14}/).exec(no_emoji_input);
    var status = false;
    if (exec_succ(patt_str)) {
        result = patt_str[0];
    } else if (exec_succ(pattresult1) && pattresult1.length > 1) {
        result = pattresult1[1];
    } else if (exec_succ(pattresult2) && pattresult2.length > 1) {
        result = pattresult2[1];
    } else if (exec_succ(pattresult3) && pattresult3.length > 1) {
        result = pattresult3[1];
    } else if (exec_succ(pattresult) && pattresult[0].length == 16) {
        var a = pattresult[0].length;
        result = pattresult[0];
    } else if (exec_succ(pattresult5) && pattresult5.length > 1) {
        var a = pattresult5[0].length;
        result = pattresult5[0];
    } else if (pddinput.indexOf("⇥") != -1 && pddinput.indexOf("⇤") != -1) {
        result = pddinput.substring(pddinput.indexOf("⇥"), pddinput.indexOf("⇤") + 1);
        layer.msg('ID获取成功！提交下单即可');
    } else if (exec_succ(pattresult4) && (pattresult4[0].length == 9 || pattresult4[0].length == 13 || pattresult4[0].length == 15)) {
        result = pattresult4[0];
        status = true;
    } else if (pddinput.indexOf("口令") != -1 && exec_succ(pattresult6) && pattresult6.length > 1) {
        result = pattresult6[1];
    } else if (!exec_succ(pattresult10) && exec_succ(pattresult7) && pattresult7[0].length == 15) {
        var a = pattresult7[0].length;
        result = pattresult7[0];
    } else if (exec_succ(pattresult12)) {
        result = pattresult12[0];
    } else if (exec_succ(pattresult13) && !exec_succ(pattresult14)) {
        var password = "\ud83d\ude42" + pattresult13[0].slice(0, 6) + "\ud83d\ude42" + pattresult13[0].slice(6) + "\ud83d\ude42";
        result = password;
        $('#inputvalue').prop('readonly', true);
    } else {
        result = pddinput;
    }
    $('#inputvalue').val(result);
    return status;
}
function exec_succ(pattresult) {
    if (typeof(pattresult) == 'object' && pattresult != null && pattresult.length > 0) {
        return true;
    } else {
        return false;
    }
}
function checkInput() {
	try {
		// 确保#inputname和#inputvalue元素存在时才执行检查
		var inputnameElement = $('#inputname');
		var inputvalueElement = $('#inputvalue');
		if (inputnameElement.length && inputvalueElement.length) {
			var gettype = inputnameElement.attr("gettype") || '';
			var inputvalue = inputvalueElement.val() || '';
			
			if(gettype == 'shareid'){
				if(inputvalue != '' && inputvalue.indexOf('http')>=0){
					getshareid();
				}
			}
			else if(gettype == 'shareurl'){
				if(inputvalue != '' && inputvalue.indexOf('http')>=0){
					getsharelink();
				}
			}
			else if(gettype == 'pinduoduo'){
				if(inputvalue != ''){
					getpddinput();
				}
			}
		}
	} catch (e) {
		console.error('checkInput error:', e);
		// 忽略错误，继续执行后续代码
	}
}
function getCity(inputid,fid,i){
	i = i || 0;
	fid = fid || 0;
	if(i == 0){
		var options='<select class="form-control" id="biaozhi_'+(i+1)+'" onchange="getCity(\''+inputid+'\',this.value,'+(i+1)+')">';
		options+='<option>请选择地址</option>';
		$.each("\u5317\u4eac|1|72|1,\u4e0a\u6d77|2|78|1,\u5929\u6d25|3|51035|1,\u91cd\u5e86|4|113|1,\u6cb3\u5317|5|142,\u5c71\u897f|6|303,\u6cb3\u5357|7|412,\u8fbd\u5b81|8|560,\u5409\u6797|9|639,\u9ed1\u9f99\u6c5f|10|698,\u5185\u8499\u53e4|11|799,\u6c5f\u82cf|12|904,\u5c71\u4e1c|13|1000,\u5b89\u5fbd|14|1116,\u6d59\u6c5f|15|1158,\u798f\u5efa|16|1303,\u6e56\u5317|17|1381,\u6e56\u5357|18|1482,\u5e7f\u4e1c|19|1601,\u5e7f\u897f|20|1715,\u6c5f\u897f|21|1827,\u56db\u5ddd|22|1930,\u6d77\u5357|23|2121,\u8d35\u5dde|24|2144,\u4e91\u5357|25|2235,\u897f\u85cf|26|2951,\u9655\u897f|27|2376,\u7518\u8083|28|2487,\u9752\u6d77|29|2580,\u5b81\u590f|30|2628,\u65b0\u7586|31|2652,\u6e2f\u6fb3|52993|52994,\u53f0\u6e7e|32|2768,\u9493\u9c7c\u5c9b|84|84".split(","), function(a, c) {
			c = c.split("|"),
			options+='<option value="'+c[1]+'">'+c[0]+'</option>'
		});
		options+='</select>';
		layer.alert('<div id="layer_button">'+options+'</div>',function(index){
			var con='';
			$("#layer_button select").each(function(){
				con+=$(this.options[this.selectedIndex]).text();
			});
			if($("#more_dizhi").length>0)con+=$("#more_dizhi").val();
			if(con.length<7)return layer.alert('请选择完整的收货地址！');
			$("#"+inputid).val(con).show();
			$("#button_"+inputid).hide();
			layer.close(index);
		});
	}else{
	$.ajax({
		type:"get",
		url:"https://fts.jd.com/area/get?fid="+fid,
		dataType:"jsonp",
		success:function(data){
			if(data.length<1){
				if($("#layer_button").html().indexOf("getCity('"+inputid+"',this.value,"+(i+1)+")")!=-1){
					$("#biaozhi_"+(i+1)).remove();
				}
				if($("#more_dizhi").length>0){}else $("#layer_button").append('<input class="form-control" id="more_dizhi" placeholder="详细地址(村、门牌号)">');
				return false;
			}
			var options='<select class="form-control" id="biaozhi_'+(i+1)+'" onchange="getCity(\''+inputid+'\',this.value,'+(i+1)+')">';
			options+='<option>请选择地址</option>';
			$.each(data,function(index,res){
				options+='<option value="'+res.id+'">'+res.name+'</option>';
			});
			options+='</select>';
			if($("#layer_button").html().indexOf("getCity('"+inputid+"',this.value,"+(i+1)+")")!=-1){
				$("#more_dizhi").remove();
				$("#biaozhi_"+(i+1)).html(options);
			}else{
				$("#layer_button").append(options);
			}
		},
		error:function(){
			layer.alert('地址选择失败，请重试！');
		}
	});
	}
}
function inviteshow(id) {
    var ii = layer.load(1, {shade: [0.1, '#fff']});
    $.ajax({
        type: 'POST',
        url: 'ajax.php?act=invite_content',
        data: {id: id},
        dataType: 'json',
        success: function (data) {
            layer.close(ii);
            if (data.code === 0) {
				layer.open({
				  type: 1,
				  shadeClose: true,
				  shade: false,
				  title: '复制以下内容邀请好友吧',
				  skin: 'layui-layer-lan',
				  content: '<div class="list-group-item well well-sm">' + data.content + '<center><button class="btn btn-warning btn-sm" data-clipboard-text="'+data.url+'" id="copyurl">一键复制链接</button>&nbsp;<button class="btn btn-success btn-sm" data-clipboard-text="'+data.content+'" id="copycontent">一键复制广告语</button></center></div>'
				});
            } else {
                layer.alert(data.msg);
            }
        },
        error: function () {
            layer.close(ii);
            layer.alert('请重试一遍即可', {icon: 6});
        }
    });
}
$(document).ready(function(){
	// 页面加载完成后，添加一个安全检查，确保#inputsname容器存在
	if (!$('#inputsname').length) {
		console.error('DOM ready: #inputsname container not found');
		// 创建一个基本的输入容器
		var kmShowFrame = $('#km_show_frame');
		if (kmShowFrame.length) {
			kmShowFrame.append('<div id="inputsname"></div>');
			console.log('DOM ready: Created #inputsname container in #km_show_frame');
		} else {
			console.error('DOM ready: #km_show_frame not found, could not create #inputsname container');
		}
	} else {
		console.log('DOM ready: #inputsname container found');
	}
	
	$("#submit_card").click(function(){
		var km=$("#km").val();
		if(km==''){layer.alert('卡密不能为空！');return false;}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=card_check",
			data : {km:km,hashsalt:hashsalt},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if (data.code === 0) {
					$("#km_show_frame").show();
					$("#km").prop('disabled', true);
					$("#submit_card_frame").slideUp();
					$("#num").val(data.num);
					var res = data.data;
				$("#shopname").val(res.name);
				console.log('Debug: res.input =', res.input);
				console.log('Debug: res.inputs =', res.inputs);
				
				// 清除并重新生成输入框
				$('#inputsname').html('');
					
					// 生成主输入框
					var inputName = res.input;
					if (!inputName || inputName == 'hide' || inputName == 'null') {
						inputName = '下单账号';
					}
					var inputGroup = $('<div class="form-group"><div class="input-group"></div></div>');
					var addon = $('<div class="input-group-addon" id="inputname">').text(inputName);
					var input = $('<input type="text" name="inputvalue" id="inputvalue" class="form-control" required onblur="checkInput()"/>');
					var qqValue = $_GET['qq'] ? $_GET['qq'] : '';
					input.val(qqValue);
					inputGroup.find('.input-group').append(addon).append(input);
					$('#inputsname').append(inputGroup);
					
					// 处理额外的输入框（如果有）
					if (res.inputs && res.inputs != '') {
						try {
							$.each(res.inputs.split('|'), function(i, value) {
								var inputsnametype = '';
								if(value.indexOf('[')>0 && value.indexOf(']')>0){
									inputsnametype = value.split('[')[1].split(']')[0];
									value = value.split('[')[0];
								}
								if(value.indexOf('{')>0 && value.indexOf('}')>0){
									var addstr = '';
									var selectname = value.split('{')[0];
									var selectstr = value.split('{')[1].split('}')[0];
									$.each(selectstr.split(','), function(i, v) {
										if(v.indexOf(':')>0){
											i = v.split(':')[0];
											v = v.split(':')[1];
										}else{
											i = v;
										}
										addstr += '<option value="'+$("<div>").text(i).html()+'">'+$("<div>").text(v).html()+'</option>';
									});
									var selectGroup = $('<div class="form-group"><div class="input-group"></div></div>');
									var selectAddon = $('<div class="input-group-addon" id="inputname'+(i+2)+'">').text(selectname);
									var select = $('<select name="inputvalue'+(i+2)+'" id="inputvalue'+(i+2)+'" class="form-control">').html(addstr);
									selectGroup.find('.input-group').append(selectAddon).append(select);
									$('#inputsname').append(selectGroup);
								}else{
									var addstr='';
									if(value=='说说ID'||value=='说说ＩＤ'||inputsnametype=='ssid')
										addstr='<div class="input-group-addon onclick" onclick="get_shuoshuo(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())">自动获取</div>';
									else if(value=='日志ID'||value=='日志ＩＤ'||inputsnametype=='rzid')
										addstr='<div class="input-group-addon onclick" onclick="get_rizhi(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())">自动获取</div>';
									else if(value=='作品ID'||value=='作品ＩＤ'||inputsnametype=='zpid')
										addstr='<div class="input-group-addon onclick" onclick="getshareid2(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())">自动获取</div>';
									else if(value=='收货地址'||value=='收货人地址'||inputsnametype=='address')
										addstr='<div class="input-group-addon onclick" onclick="getCity(\'inputvalue'+(i+2)+'\')">点此选择</div>';
									
									var inputGroup = $('<div class="form-group"><div class="input-group"></div></div>');
									var inputAddon = $('<div class="input-group-addon" id="inputname'+(i+2)+'" gettype="'+inputsnametype+'">').text(value);
									var input = $('<input type="text" name="inputvalue'+(i+2)+'" id="inputvalue'+(i+2)+'" value="" class="form-control" required/>');
									inputGroup.find('.input-group').append(inputAddon).append(input);
									if(addstr) {
										inputGroup.find('.input-group').append($(addstr));
									}
									$('#inputsname').append(inputGroup);
								}
							});
						} catch (e) {
							console.error('Error processing inputsname:', e);
						}
					}
					
					// 处理商品描述
					if(res.desc && res.desc != '' && res.desc != 'null'){
						$('#alert_frame').show();
						$('#alert_frame').html(decodeURIComponent(res.desc));
					} else {
						$('#alert_frame').hide();
					}
					
					// 处理商品提示
					if(res.alert && res.alert != '' && res.alert != 'null'){
						var alertIndex=layer.alert(''+decodeURIComponent(res.alert)+'',{
							btn:['我知道了'],
							title:'商品提示'
						},function(){
							layer.close(alertIndex);
						});
					}
					
					console.log('Debug: Input fields generated successfully');
				} else {
					layer.open({
						title: '错误提示'
						, icon: 2
						, content: data.msg
					});
				}
			},
			error: function () {
				layer.close(ii);
				layer.alert('请重试一遍即可', {icon: 6});
			}
		});
	});
	$("#submit_buy").click(function(){
		try {
			var km=$("#km").val();
			if(km==''){layer.alert('卡密不能为空！');return false;}
			
			// 安全检查：确保#inputvalue元素存在
			var inputvalueElement = $('#inputvalue');
			if (!inputvalueElement.length) {
				console.error('submit_buy: #inputvalue element not found');
				layer.alert('系统错误：输入框未正确生成，请刷新页面重试！');
				return false;
			}
			
			// 检查输入值是否为空
			if (inputvalueElement.val()=='') {
				layer.alert('请确保每项不能为空！');
				return false;
			}
			
			// 只检查存在的输入框
			var allFilled = true;
			var emptyFields = [];
			$('input[name^=inputvalue]').each(function(index){
				if($(this).val()==''){
					allFilled = false;
					var fieldName = '输入项 ' + (index + 1);
					// 尝试获取输入框的名称
					var inputGroup = $(this).closest('.input-group');
					var addon = inputGroup.find('.input-group-addon');
					if (addon.length) {
						fieldName = addon.text();
					}
					emptyFields.push(fieldName);
					return false;
				}
			});
			if(!allFilled){
				var errorMsg = '请确保以下项不能为空：' + emptyFields.join('、');
				layer.alert(errorMsg);
				return false;
			}
		// 确保#inputname元素存在且inputvalueElement存在时才执行验证
			var inputnameElement = $('#inputname');
			if (inputnameElement.length && inputvalueElement.length) {
				var inputnameHtml = inputnameElement.html();
				var inputvalue = inputvalueElement.val();
				
				if((inputnameHtml=='下单ＱＱ' || inputnameHtml=='ＱＱ账号' || inputnameHtml == 'QQ账号') && (inputvalue.length<5 || inputvalue.length>11 || isNaN(inputvalue))){layer.alert('请输入正确的QQ号！');return false;}
				var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
				if(inputnameHtml=='你的邮箱' && !reg.test(inputvalue)){layer.alert('邮箱格式不正确！');return false;}
				reg=/^[1][0-9]{10}$/;
				if(inputnameHtml=='手机号码' && !reg.test(inputvalue)){layer.alert('手机号码格式不正确！');return false;}
			}
		// 确保#inputname2和#inputvalue2元素存在时才执行验证
			var inputname2Element = $('#inputname2');
			var inputvalue2Element = $('#inputvalue2');
			if (inputname2Element.length && inputvalue2Element.length) {
				if(inputname2Element.html() == '说说ID'||inputname2Element.html() == '说说ＩＤ'){
					if(inputvalue2Element.val().length != 24){layer.alert('说说必须是原创说说！');return false;}
				}
			}
		checkInput();
		// 确保#inputname和#inputvalue元素存在时才执行验证
			var inputnameElement = $('#inputname');
			var inputvalueElement = $('#inputvalue');
			if (inputnameElement.length && inputvalueElement.length) {
				if(inputnameElement.html() == '抖音作品ID'||inputnameElement.html() == '火山作品ID'||inputnameElement.html() == '火山直播ID'){
					if(inputvalueElement.val().length != 19){layer.alert('您输入的作品ID有误！');return false;}
				}
			}
		// 确保#inputname2和#inputvalue2元素存在时才执行验证
			var inputname2Element = $('#inputname2');
			var inputvalue2Element = $('#inputvalue2');
			if (inputname2Element.length && inputvalue2Element.length) {
				if(inputname2Element.html() == '抖音评论ID'){
					if(inputvalue2Element.val().length != 19){layer.alert('您输入的评论ID有误！请点击自动获取手动选择评论！');return false;}
				}
			}
		if($('#inputname').attr("gettype")=='shareurl'){
			var inputElement = $('#inputvalue');
				if (inputElement.length && inputElement.val().indexOf('http://')==-1 && inputElement.val().indexOf('https://')==-1){
					layer.alert('您输入的链接有误！请重新输入！');return false;
				}
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
			
			// 构建AJAX数据对象，只包含存在的输入值
			var ajaxData = {km: km, hashsalt: hashsalt};
			for (var i = 1; i <= 5; i++) {
				var inputElement = $('#inputvalue' + (i === 1 ? '' : i));
				if (inputElement.length) {
					ajaxData['inputvalue' + (i === 1 ? '' : i)] = inputElement.val();
				}
			}
			
			$.ajax({
				type : "POST",
				url : "ajax.php?act=card_pay",
				data : ajaxData,
				dataType : 'json',
				success : function(data) {
					layer.close(ii);
					if (data.code === 1) {
						if($('#inputname').html()=='你的邮箱'){
							saveEmail(inputvalue);
						}
						layer.alert(data.msg, {icon: 1}, function(){ window.location.href='./?buyok=1';});
					} else if(data.code == 4){
						var confirmobj = layer.confirm('请登录后再购买，是否现在登录？', {
						  btn: ['登录','注册','取消']
						}, function(){
							window.location.href='./user/login.php';
						}, function(){
							window.location.href='./user/reg.php';
						}, function(){
							layer.close(confirmobj);
						});
					} else if(data.code == 2){
						if(data.type == 1){
							layer.open({
						  type: 1,
						  title: '完成验证',
						  skin: 'layui-layer-rim',
						  area: ['320px', '260px'],
						  content: '<div id="captcha" style="margin: auto;"><div id="captcha_text">正在加载验证码</div></div>',
						  success: function(){
							$.getScript("//static.geetest.com/v4/gt4.js", function() {
								initGeetest4({
									product: 'float',
									width: '300px'
								}, function (captchaObj) {
									captchaObj.appendTo('#captcha');
									captchaObj.onReady(function () {
										$("#captcha_text").hide();
									}).onSuccess(function () {
										var result = captchaObj.getValidate();
										if (!result) {
											return alert('请完成验证');
										}
										var ii = layer.load(2, {shade:[0.1,'#fff']});
										$.ajax({
											type : "POST",
											url : "ajax.php?act=card_pay",
											data : {km:km,inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),hashsalt:hashsalt,geetest_challenge:result.geetest_challenge,geetest_validate:result.geetest_validate,geetest_seccode:result.geetest_seccode},
											dataType : 'json',
											success : function(data) {
												layer.close(ii);
												if (data.code === 1) {
													if($('#inputname').html()=='你的邮箱'){
														saveEmail(inputvalue);
													}
													layer.alert(data.msg, {icon: 1}, function(){ window.location.href='./?buyok=1';});
												} else if(data.code == 4){
													var confirmobj = layer.confirm('请登录后再购买，是否现在登录？', {
													  btn: ['登录','注册','取消']
													}, function(){
														window.location.href='./user/login.php';
													}, function(){
														window.location.href='./user/reg.php';
													}, function(){
														layer.close(confirmobj);
													});
												} else {
													layer.alert(data.msg, {icon: 2});
													captchaObj.reset();
												}
											},
											error: function () {
												layer.close(ii);
												layer.alert('请重试一遍即可', {icon: 6});
											}
										});
									});
								});
							});
						  }
						});
					} else if(data.type == 2){
						layer.open({
						  type: 1,
						  title: '完成验证',
						  skin: 'layui-layer-rim',
						  area: ['320px', '260px'],
						  content: '<div id="captcha" style="margin: auto;"><div id="captcha_text">正在加载验证码</div></div>',
						  success: function(){
							$.getScript("//cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js", function() {
								var myCaptcha = _dx.Captcha(document.getElementById('captcha'), {
									appId: data.appid,
									type: 'basic',
									style: 'embed',
									success: function(token) {
										if (!token) {
											return alert('请完成验证');
										}
										var ii = layer.load(2, {shade:[0.1,'#fff']});
										$.ajax({
											type : "POST",
											url : "ajax.php?act=card_pay",
											data : {km:km,inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),hashsalt:hashsalt,token:token},
											dataType : 'json',
											success : function(data) {
												layer.close(ii);
												if (data.code === 1) {
													if($('#inputname').html()=='你的邮箱'){
														saveEmail(inputvalue);
													}
													layer.alert(data.msg, {icon: 1}, function(){ window.location.href='./?buyok=1';});
												} else if(data.code == 4){
													var confirmobj = layer.confirm('请登录后再购买，是否现在登录？', {
													  btn: ['登录','注册','取消']
													}, function(){
														window.location.href='./user/login.php';
													}, function(){
														window.location.href='./user/reg.php';
													}, function(){
														layer.close(confirmobj);
													});
												} else {
													layer.alert(data.msg, {icon: 2});
												}
											},
											error: function () {
												layer.close(ii);
												layer.alert('请重试一遍即可', {icon: 6});
											}
										});
									}
								})
								myCaptcha.on('ready', function () {
									$('#captcha_text').hide();
								})
							});
						  }
						});
					}else if(data.type == 3){
						layer.open({
						  type: 1,
						  title: '完成验证',
						  skin: 'layui-layer-rim',
						  area: ['320px', '231px'],
						  content: '<div id="captcha"><div id="captcha_text">正在加载验证码</div></div>',
						  success: function(){
							$.getScript("//v.vaptcha.com/v3.js", function() {
								vaptcha({
									vid: data.appid,
									type: 'embed',
									container: '#captcha',
									offline_server: 'https://management.vaptcha.com/api/v3/demo/offline'
								}).then(function(token) {
									if (!token) {
										return alert('请完成验证');
									}
									var ii = layer.load(2, {shade:[0.1,'#fff']});
									$.ajax({
										type : "POST",
										url : "ajax.php?act=card_pay",
										data : {km:km,inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),hashsalt:hashsalt,token:token},
										dataType : 'json',
										success : function(data) {
											layer.close(ii);
											if (data.code === 1) {
												if($('#inputname').html()=='你的邮箱'){
													saveEmail(inputvalue);
												}
												layer.alert(data.msg, {icon: 1}, function(){ window.location.href='./?buyok=1';});
											} else if(data.code == 4){
												var confirmobj = layer.confirm('请登录后再购买，是否现在登录？', {
												  btn: ['登录','注册','取消']
												}, function(){
													window.location.href='./user/login.php';
												}, function(){
													window.location.href='./user/reg.php';
												}, function(){
													layer.close(confirmobj);
												});
											} else {
												layer.alert(data.msg, {icon: 2});
											}
										},
										error: function () {
											layer.close(ii);
											layer.alert('请重试一遍即可', {icon: 6});
										}
									});
								});
							});
						  }
						});
					}
				} else {
					layer.alert(data.msg, {icon: 2});
				}
			},
			error: function () {
				layer.close(ii);
				layer.alert('请重试一遍即可', {icon: 6});
			}
		});
		} catch (e) {
			console.error('submit_buy click error:', e);
			layer.alert('提交订单时发生错误：' + e.message, {icon: 2});
			return false;
		}
	});

});

