function dopay(type,orderid){
	if(type == 'rmb'){
		var ii = layer.msg('正在提交订单请稍候...', {icon: 16,shade: 0.5,time: 15000});
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=payrmb",
			data : {orderid: orderid},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					// 支付成功后，更新购物车计数
					$.ajax({
						type : "GET",
						url : "../ajax.php?act=getcount",
						dataType : 'json',
						async: true,
						success : function(countData) {
							if(countData.cart_count != null){
								$('#cart_count').html(countData.cart_count);
								if(countData.cart_count == 0){
									$('#alert_cart').slideUp();
								}
							}
						}
					});
					alert(data.msg);
					window.location.href='?buyok=1';
				}else if(data.code == -2){
					// 支付成功后，更新购物车计数
					$.ajax({
						type : "GET",
						url : "../ajax.php?act=getcount",
						dataType : 'json',
						async: true,
						success : function(countData) {
							if(countData.cart_count != null){
								$('#cart_count').html(countData.cart_count);
								if(countData.cart_count == 0){
									$('#alert_cart').slideUp();
								}
							}
						}
					});
					alert(data.msg);
					window.location.href='?buyok=1';
				}else if(data.code == -3){
					var confirmobj = layer.confirm('你的余额不足，请充值！', {
					  btn: ['立即充值','取消']
					}, function(){
						window.location.href='./#chongzhi';
					}, function(){
						layer.close(confirmobj);
					});
				}else if(data.code == -4){
					var confirmobj = layer.confirm('你还未登录，是否现在登录？', {
					  btn: ['登录','注册','取消']
					}, function(){
						window.location.href='./login.php';
					}, function(){
						window.location.href='./reg.php';
					}, function(){
						layer.close(confirmobj);
					});
				}else{
					layer.alert(data.msg);
				}
			} 
		});
	}else{
		window.location.href='../other/submit.php?type='+type+'&orderid='+orderid;
	}
}

var handlerEmbed = function (captchaObj) {
	captchaObj.appendTo('#captcha');
	captchaObj.onReady(function () {
		$("#captcha_wait").hide();
	}).onSuccess(function () {
		var result = captchaObj.getValidate();
		if (!result) {
			return alert('请完成验证');
		}
		var kind = $("select[name='kind']").val();
		var qz = $("input[name='qz']").val();
		var domain = $("[name='domain']").val();
		var name = $("input[name='name']").val();
		if($("input[name='user']").length>0){
			var qq = $("input[name='qq']").val();
			var user = $("input[name='user']").val();
			var pwd = $("input[name='pwd']").val();
		}
		var invitecode = $("input[name='invitecode']").val();
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=paysite",
			data : {kind:kind,qz:qz,domain:domain,name:name,qq:qq,invitecode:invitecode,user:user,pwd:pwd,hashsalt:hashsalt,geetest_challenge:result.geetest_challenge,geetest_validate:result.geetest_validate,geetest_seccode:result.geetest_seccode},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code >= 0){
					layer.alert('开通分站成功！',{
						icon: 1,
						closeBtn: false
					}, function(){
					  window.location.href='regok.php?zid='+data.zid;
					});
				}else{
					layer.alert(data.msg);
					captchaObj.reset();
				}
			} 
		});
	});
};
var handlerEmbed2 = function (token) {
	if (!token) {
		return alert('请完成验证');
	}
	var kind = $("select[name='kind']").val();
	var qz = $("input[name='qz']").val();
	var domain = $("[name='domain']").val();
	var name = $("input[name='name']").val();
	if($("input[name='user']").length>0){
		var qq = $("input[name='qq']").val();
		var user = $("input[name='user']").val();
		var pwd = $("input[name='pwd']").val();
	}
	var invitecode = $("input[name='invitecode']").val();
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "ajax.php?act=paysite",
		data : {kind:kind,qz:qz,domain:domain,name:name,qq:qq,invitecode:invitecode,user:user,pwd:pwd,hashsalt:hashsalt,token:token},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code >= 0){
				layer.alert('开通分站成功！',{
					icon: 1,
					closeBtn: false
				}, function(){
				  window.location.href='regok.php?zid='+data.zid;
				});
			}else{
				layer.alert(data.msg);
			}
		} 
	});
};
var handlerEmbed3 = function (vaptchaObj) {
	vaptchaObj.render();
	$('#captcha_text').hide();
	vaptchaObj.listen('pass', function() {
		var token = vaptchaObj.getToken();
		if (!token) {
			return alert('请完成验证');
		}
		var kind = $("select[name='kind']").val();
		var qz = $("input[name='qz']").val();
		var domain = $("[name='domain']").val();
		var name = $("input[name='name']").val();
		if($("input[name='user']").length>0){
			var qq = $("input[name='qq']").val();
			var user = $("input[name='user']").val();
			var pwd = $("input[name='pwd']").val();
		}
		var invitecode = $("input[name='invitecode']").val();
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=paysite",
			data : {kind:kind,qz:qz,domain:domain,name:name,qq:qq,invitecode:invitecode,user:user,pwd:pwd,hashsalt:hashsalt,token:token},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code >= 0){
					layer.alert('开通分站成功！',{
						icon: 1,
						closeBtn: false
					}, function(){
					  window.location.href='regok.php?zid='+data.zid;
					});
				}else{
					layer.alert(data.msg);
					vaptchaObj.reset();
				}
			} 
		});
	});
};
$(document).ready(function(){
    $("input[name='qz']").blur(function(){
        var qz = $(this).val();
        var domain = $("[name='domain']").val();
        if(qz){
            $.get("ajax.php?act=checkdomain", { 'qz' : qz , 'domain' : domain},function(data){
                    if( data == 1 ){
                        layer.alert('你所填写的域名已被使用，请更换一个！');
						//$("input[name='qz']").focus();
                    }
            });
        }
    });
	$("input[name='user']").blur(function(){
        var user = $(this).val();
        if(user){
            $.get("ajax.php?act=checkuser", { 'user' : user},function(data){
                    if( data == 1 ){
                        layer.alert('你所填写的用户名已存在！');
						//$("input[name='user']").focus();
                    }
            });
        }
    });
	$("#submit_buy").click(function(){
		var kind = $("select[name='kind']").val();
		var qz = $("input[name='qz']").val();
		var domain = $("[name='domain']").val();
		var name = $("input[name='name']").val();
		if(qz=='' || name==''){layer.alert('请确保每项不能为空！');return false;}
		if(qz.length<2){
			layer.alert('域名前缀太短！'); return false;
		}else if(qz.length>10){
			layer.alert('域名前缀太长！'); return false;
		}else if(name.length<2){
			layer.alert('网站名称太短！'); return false;
		}
		if($("input[name='user']").length>0){
			var qq = $("input[name='qq']").val();
			var user = $("input[name='user']").val();
			var pwd = $("input[name='pwd']").val();
			if(qq=='' || user=='' || pwd==''){layer.alert('请确保每项不能为空！');return false;}
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
		}
		var invitecode = $("input[name='invitecode']").val();
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=paysite",
			data : {kind:kind,qz:qz,domain:domain,name:name,qq:qq,invitecode:invitecode,user:user,pwd:pwd,hashsalt:hashsalt},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					var paymsg = '';
					if(data.pay_alipay>0){
						paymsg+='<button class="auth-pay-btn auth-pay-alipay" onclick="dopay(\'alipay\',\''+data.trade_no+'\')"><img src="../assets/img/alipay.png" class="auth-pay-logo">支付宝</button>';
					}
					if(data.pay_qqpay>0){
						paymsg+='<button class="auth-pay-btn auth-pay-qq" onclick="dopay(\'qqpay\',\''+data.trade_no+'\')"><img src="../assets/img/qqpay.png" class="auth-pay-logo">QQ钱包</button>';
					}
					if(data.pay_wxpay>0){
						paymsg+='<button class="auth-pay-btn auth-pay-wx" onclick="dopay(\'wxpay\',\''+data.trade_no+'\')"><img src="../assets/img/wxpay.png" class="auth-pay-logo">微信支付</button>';
					}
					if (data.pay_rmb>0) {
						paymsg+='<button class="auth-pay-btn auth-pay-rmb" onclick="dopay(\'rmb\',\''+data.trade_no+'\')"><img src="../assets/img/rmb.png" class="auth-pay-logo">使用余额支付 <span class="auth-pay-sub">(剩'+data.user_rmb+'元)</span></button>';
					}
					var payHtml = '<style>' +
'.auth-pay-modal{padding:0;text-align:center;}' +
'.auth-pay-amount{background:linear-gradient(135deg,#4e6ef2,#6c5ce7);color:#fff;padding:22px 20px;border-radius:12px 12px 0 0;margin:-28px -20px 0 -20px;}' +
'.auth-pay-amount .auth-pay-label{font-size:13px;opacity:.85;font-weight:normal;letter-spacing:1px;}' +
'.auth-pay-amount .auth-pay-num{font-size:34px;font-weight:700;margin-top:4px;letter-spacing:-1px;}' +
'.auth-pay-amount .auth-pay-num .yen{font-size:22px;margin-right:3px;}' +
'.auth-pay-list{padding:18px 20px 10px;}' +
'.auth-pay-btn{display:flex;align-items:center;width:100%;height:48px;margin-bottom:10px;padding:0 16px;border:none;border-radius:10px;color:#fff;font-size:15px;font-weight:500;cursor:pointer;transition:transform .15s,box-shadow .15s;}' +
'.auth-pay-btn:hover{transform:translateY(-1px);box-shadow:0 4px 14px rgba(0,0,0,.18);}' +
'.auth-pay-btn:active{transform:translateY(0);}' +
'.auth-pay-logo{width:24px;height:24px;margin-right:12px;background:rgba(255,255,255,.2);border-radius:5px;}' +
'.auth-pay-sub{opacity:.85;font-weight:normal;font-size:13px;margin-left:4px;}' +
'.auth-pay-alipay{background:linear-gradient(90deg,#1677FF,#0E66D9);}' +
'.auth-pay-qq{background:linear-gradient(90deg,#12B7F5,#0096E6);}' +
'.auth-pay-wx{background:linear-gradient(90deg,#07C160,#06AD56);}' +
'.auth-pay-rmb{background:linear-gradient(90deg,#6c5ce7,#a29bfe);}' +
'.auth-pay-cancel{display:block;width:100%;margin-top:6px;padding:10px;border:1px solid #e0e3eb;border-radius:10px;background:#fff;color:#666;font-size:14px;cursor:pointer;text-align:center;text-decoration:none;transition:all .15s;}' +
'.auth-pay-cancel:hover{border-color:#c9cdd4;color:#333;background:#fafbfc;}' +
'</style>' +
'<div class="auth-pay-modal">' +
'<div class="auth-pay-amount"><div class="auth-pay-label">支付金额</div><div class="auth-pay-num"><span class="yen">¥</span>'+data.need+'</div></div>' +
'<div class="auth-pay-list">'+paymsg+'<a class="auth-pay-cancel" onclick="window.location.reload()">取消订单</a></div>' +
'</div>';
					layer.alert(payHtml,{
						btn:[],
						title:'',
						closeBtn: false,
						area: ['340px','auto']
					});
				}else if(data.code == 1){
					layer.alert('开通分站成功！',{
						icon: 1,
						closeBtn: false
					}, function(){
					  window.location.href='regok.php?zid='+data.zid;
					});
				}else if(data.code == 2){
					if(data.type == 1){
						layer.open({
						  type: 1,
						  title: '完成验证',
						  skin: 'layui-layer-rim',
						  area: ['320px', '100px'],
						  content: '<div id="captcha"><div id="captcha_text">正在加载验证码</div><div id="captcha_wait"><div class="loading"><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div></div></div></div>',
						  success: function(){
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
						  }
						});
					}else if(data.type == 2){
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
									success: handlerEmbed2
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
								}).then(handlerEmbed3);
							});
						  }
						});
					}
				}else{
					layer.alert(data.msg);
				}
			} 
		});
	});
});
