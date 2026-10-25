var checkflag1 = "false";
function check1(field) {
if (checkflag1 == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag1 = "true";
return "false"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag1 = "false";
return "true"; }
}

function unselectall1()
{
    if(document.form1.chkAll1.checked){
	document.form1.chkAll1.checked = document.form1.chkAll1.checked&0;
	checkflag1 = "false";
    } 	
}

function showkms(obj) {
	$(obj).css("white-space","normal");
	$(obj).css("word-break","break-all");
}

function generateRandomString(length, options) {
	var chars = '';
	if (options.useNum) chars += '0123456789';
	if (options.useLow) chars += 'abcdefghijklmnopqrstuvwxyz';
	if (options.useUp) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if (options.useSpec) chars += '!@#$%^&*()_+-=[]{}|;:,.<>?';
	if (chars === '') chars = '0123456789';
	var result = '';
	for (var i = 0; i < length; i++) {
		result += chars.charAt(Math.floor(Math.random() * chars.length));
	}
	return result;
}

function generatekms() {
	var num = parseInt($("#gen_num").val()) || 10;
	var kmLen = parseInt($("#gen_km_len").val()) || 16;
	var pwLen = parseInt($("#gen_pw_len").val()) || 16;
	var split = $("#gen_split").val() || " ";
	var useNum = $("#gen_use_num").prop("checked");
	var useLow = $("#gen_use_low").prop("checked");
	var useUp = $("#gen_use_up").prop("checked");
	var useSpec = $("#gen_use_spec").prop("checked");
	if (num < 1 || num > 1000) {
		layer.msg('数量必须在1-1000之间', {icon: 2});
		return;
	}
	if (kmLen < 4 || kmLen > 64 || pwLen < 4 || pwLen > 64) {
		layer.msg('卡号和密码长度必须在4-64之间', {icon: 2});
		return;
	}
	var kmOptions = {useNum: useNum, useLow: useLow, useUp: useUp, useSpec: useSpec};
	var kms = [];
	for (var i = 0; i < num; i++) {
		var km = generateRandomString(kmLen, kmOptions);
		var pw = generateRandomString(pwLen, kmOptions);
		kms.push(km + split + pw);
	}
	var existing = $("#kms").val();
	if (existing.trim()) {
		$("#kms").val(existing + "\n" + kms.join("\n"));
	} else {
		$("#kms").val(kms.join("\n"));
	}
	$("input[name='split']").val(split);
	layer.msg('成功生成 ' + num + ' 张卡密', {icon: 1});
}

function clearkms() {
	$("#kms").val('');
}

function checkAdd(){
	if($("#tid").val()==0||$("#tid").val()==null){
		layer.alert('请先选择商品');return false;
	}
	if($("#kms").val()==''){
		layer.alert('卡密列表不能为空');return false;
	}
}
$(document).ready(function(){
	$("#cid").change(function () {
		var cid = $(this).val();
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$("#tid").empty();
		$("#tid").append('<option value="0">请选择商品</option>');
		$.ajax({
			type : "GET",
			url : "./ajax.php?act=getfakatool&cid="+cid,
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
					if(num==0 && cid!=0)$("#tid").html('<option value="0">该分类下没有发卡类商品</option>');
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
	var items = $("select[default]");
	for (i = 0; i < items.length; i++) {
		$(items[i]).val($(items[i]).attr("default")||0);
	}
});