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
var handlerEmbed = function (captchaObj) {
	captchaObj.appendTo('#captcha');
	captchaObj.onReady(function () {
		$("#captcha_wait").hide();
	}).onSuccess(function () {
		var result = captchaObj.getValidate();
		if (!result) {
			return alert('请完成验证');
		}
		$("#captchaform").html('<input type="hidden" name="geetest_challenge" value="'+result.geetest_challenge+'" /><input type="hidden" name="geetest_validate" value="'+result.geetest_validate+'" /><input type="hidden" name="geetest_seccode" value="'+result.geetest_seccode+'" />');
	});
};
var handlerEmbed2 = function (token) {
	if (!token) {
		return alert('请完成验证');
	}
	$("#captchaform").html('<input type="hidden" name="token" value="'+token+'" />');
};
var handlerEmbed3 = function (vaptchaObj) {
	vaptchaObj.render();
	$('#captcha_text').hide();
	vaptchaObj.listen('pass', function() {
		var token = vaptchaObj.getToken();
		if (!token) {
			return alert('请完成验证');
		}
		$("#captchaform").html('<input type="hidden" name="token" value="'+token+'" />');
	})
};
$(document).ready(function(){
	var captcha_type = $("input[name='captcha_type']").val();
	$("input[name='user']").blur(function(){
        var user = $(this).val();
        if(user){
            $.get("ajax.php?act=checkuser", { 'user' : user},function(data){
                    if( data == 1 ){
                        layer.alert('你所填写的用户名已存在！');
					//$('input[name="user"]').focus();
                    }
            });
        }
    });
	$("#submit_reg").click(function(){
		var user = $("input[name='user']").val();
		// 兼容两种密码字段名：首先尝试pass，再尝试pwd
		var pwd = $("input[name='pass']").val() || $("input[name='pwd']").val();
		var qq = $("input[name='qq']").val();
		if(qq=='' || user=='' || pwd=='' || qq==undefined || user==undefined || pwd==undefined){layer.alert('请确保每项不能为空！');return false;}
		if(qq.length<5){
			layer.alert('QQ格式不正确！'); return false;
		}else if(user.length<3){
			layer.alert('用户名太短'); return false;
		}else if(user.length>20){
			layer.alert('用户名太长'); return false;
		}else if(pwd.length<6){
			layer.alert('密码不能低于6位'); return false;
		}else if(pwd.length>30){
			layer.alert('密码太长'); return false;
		}
		var invitecode = $("input[name='invitecode']").val();
		// 增强hashsalt获取逻辑，优先使用全局变量，其次使用表单值
		var hashsalt = typeof window.hashsalt !== 'undefined' ? window.hashsalt : $("input[name='hashsalt']").val();
		// 检查hashsalt是否存在
		if(!hashsalt || hashsalt == ''){
			layer.alert('页面验证信息缺失，请刷新页面重试！');
			window.location.reload();
			return false;
		}
		var data = {user:user, pwd:pwd, qq:qq, invitecode:invitecode, hashsalt:hashsalt};
		if(captcha_type == 1){
			var geetest_challenge = $("input[name='geetest_challenge']").val();
			var geetest_validate = $("input[name='geetest_validate']").val();
			var geetest_seccode = $("input[name='geetest_seccode']").val();
			if(geetest_challenge == undefined){
				layer.alert('请先完成滑动验证！'); return false;
			}
			var adddata = {geetest_challenge:geetest_challenge, geetest_validate:geetest_validate, geetest_seccode:geetest_seccode};
		}else if(captcha_type == 2 || captcha_type == 3){
			var token = $("input[name='token']").val();
			if(token == undefined){
				layer.alert('请先完成滑动验证！'); return false;
			}
			var adddata = {token:token};
		}else{
			var code = $("input[name='code']").val();
			if(code==''){
				layer.alert('验证码不能为空！'); return false;
			}
			var adddata = {code:code};
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=reguser",
			data : Object.assign(data, adddata),
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					if($_GET['back']=='cutshop'){
						layer.msg('登录成功，正在跳转到砍价商城页面', {icon: 1,shade: 0.01,time: 15000});
						window.location.href='../?mod=cutshop';
					}else if($_GET['back']=='groupshop'){
						layer.msg('登录成功，正在跳转到团购商城页面', {icon: 1,shade: 0.01,time: 15000});
						window.location.href='../?mod=groupshop';
					}else if($_GET['back']=='seckill'){
						layer.msg('登录成功，正在跳转到秒杀专场页面', {icon: 1,shade: 0.01,time: 15000});
						window.location.href='../?mod=seckill';
					}else if($_GET['back']=='coupon'){
						layer.msg('登录成功，正在跳转到领取优惠券页面', {icon: 1,shade: 0.01,time: 15000});
						window.location.href='../?mod=coupon';
					}else{
						layer.alert('用户注册成功！',{
						  closeBtn: 0,
						    icon: 1
						}, function(){
						  window.location.href='./';
						});
					}
				}else if(data.msg == '验证失败，请刷新页面重试'){
					// 如果是验证失败，提示用户刷新页面
					layer.alert(data.msg, function(){
						window.location.reload();
					});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	if(captcha_type == 1){
		$.getScript("//static.geetest.com/static/tools/gt.js", function() {
			$.ajax({
				url: "../ajax.php?act=captcha&t=" + (new Date()).getTime(),
				type: "get",
				dataType: "json",
				success: function (data) {
					$('#captcha_text').hide();
					$('#captcha_wait').show();
					initGeetest({
						gt: data.gt,
						challenge: data.challenge,
						new_captcha: data.new_captcha,
						product: "popup",
						width: "100%",
						offline: !data.success
					}, handlerEmbed);
				}
			});
		});
	}else if(captcha_type == 2){
		var appid = $("input[name='appid']").val();
		$.getScript("//cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js", function() {
			var myCaptcha = _dx.Captcha(document.getElementById('captcha'), {
				appId: appid,
				type: 'basic',
				style: 'oneclick',
				width: "",
				success: handlerEmbed2
			})
			myCaptcha.on('ready', function () {
				$('#captcha_text').hide();
			})
		});
	}else if(captcha_type == 3){
		var appid = $("input[name='appid']").val();
		$.getScript("//v.vaptcha.com/v3.js", function() {
			vaptcha({
				vid: appid,
				type: 'click',
				container: '#captcha',
				offline_server: 'https://management.vaptcha.com/api/v3/demo/offline'
			}).then(handlerEmbed3);
		});
	}
});