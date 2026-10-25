$.get(
  "./api.php?act=siteinfo",
  function (res) {
    if (!$.cookie("open")) {
      if (res.modal != "") {
        layer.open({
          type: 1,
          title: "购买协议",
          content:
            '<div id="buy-protocol">' +
            res.modal +
            "</div><style>#buy-protocol img{width:100%}</style>",
          btn: ["我同意协议"],
          success: function (layero, index) {
            $.cookie("open", "false", { expires: 7 });
            $(layero.selector)
              .find(".layui-layer-close")
              .css("display", "none");
            $(layero.selector)
              .find(".layui-layer-btn a")
              .css("display", "none");
          },
        });
      }
    }
  },
  "json"
);

//默认点亮第N个分类ID
$(document).on("click", ".category_box", function () {
  $(this).addClass("active");
  $(this).siblings().removeClass("active");
  $("[name=cateid]").val($(this).data("cateid"));
  const Class_ID = $("[name=cateid]").val();
  //Class_ID == 分类ID
  //
});

$(document).on("click", ".goods_box", function () {
  $(this).addClass("active");
  $(this).siblings().removeClass("active");
  //   $('[name=goodid]').val($(this).data('goods_id'))
  //     const Goods_ID = $('[name=goodid]').val()
  //     console.log(Goods_ID)
});
function getGoodsPro(Class_ID) {
  $(`div[data-cateid='${Class_ID}']`).addClass("active");
}

const app = new Vue({
  el: "#app",
  data: {
    info: "",
    max: 10000,
    min: 1,
    num_val: 1,
    uPir: 0,
  },
  // 计算属性
  computed: {
    // 计算总价
    total() {
      return (this.num_val*100000000) * (this.info.price*100000000)/10000000000000000;
    },
  },
  methods: {
    async getGoodsInfo(tid) {
      const { data: res } = await axios.get(
        `./ajax.php?act=gettool&tid=${tid}&info=1`
      );
      this.info = res.data[0];
      this.max = res.data[0].max;
      this.min = res.data[0].min>0?res.data[0].min:1;
      getPoint(this.info);
    //   console.log(res.data[0]);
    },
    countoper(opr) {
      if (this.info) {
        if (opr == "add") {
          if(this.num_val>=this.info.max){
              layer.alert("最大购买数量"+this.max);
              return false;
          }
          this.num_val++;
        } else if (opr == "reduce") {
            if(this.num_val<=this.info.min){
              layer.alert("最少购买数量"+this.min);
              return false;
          }
          if (this.num_val > 1) {
            this.num_val--;
          }
        }
      }
    },
    PayGoods() {
      var tid = this.info.tid;
      var inputvalue = $("#inputvalue").val();
    //   console.log(tid, inputvalue);
      if(tid==0||typeof tid === 'undefined'){layer.alert('请选择商品！');return false;}
      $.ajax({
        type: "POST",
        url: "ajax.php?act=pay",
        data: {
          tid: tid,
          num: $("#num").val(),
          inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),
        //   inputvalue: $("#inputvalue").val(),
          hashsalt: hashsalt,
        },
        dataType: "json",
        success: function (data) {
          if (data.code == 0) {
            var paymsg = '';
            // 处理支付方式按钮
            if(data.pay_alipay > 0){
              paymsg+='<button class="btn btn-default btn-block" onclick="dopay(\'alipay\',\''+data.trade_no+'\')" style="margin-top:10px;"><img src="assets/Agod/zfb.png" class="logo" width="20" height="20">支付宝</button>';
            }
            if(data.pay_wxpay > 0){
              paymsg+='<button class="btn btn-default btn-block" onclick="dopay(\'wxpay\',\''+data.trade_no+'\')" style="margin-top:10px;"><img src="assets/Agod/wx.png" class="logo" width="20" height="20">微信支付</button>';
            }
            if(data.pay_qqpay > 0){
              paymsg+='<button class="btn btn-default btn-block" onclick="dopay(\'qqpay\',\''+data.trade_no+'\')" style="margin-top:10px;"><img src="assets/faka/images/qqpaym.png" class="logo" width="20" height="20">QQ支付</button>';
            }
            if(data.pay_usdt > 0){
              paymsg+='<button class="btn btn-default btn-block" onclick="dopay(\'usdt_pay\',\''+data.trade_no+'\')" style="margin-top:10px;"><img src="other/usdt-trc20/static/img/tether.svg" class="logo" width="20" height="20">USDT-TRC20</button>';
            }
            if (data.pay_rmb > 0) {
              paymsg +=
                "<button class=\"btn btn-default btn-block\" onclick=\"dopay('rmb','" +
                data.trade_no +
                '\")" style="margin-top:10px;"><img src="assets/img/rmb.png" class="logo">余额支付<span class="text-muted">（剩' +
                data.user_rmb +
                "元）</span></button>";
            }
            if (data.paymsg != null) paymsg += data.paymsg;
            layer.alert(
              "<center><h2>￥ " +
                data.need +
                "</h2><hr>" +
                paymsg +
                '<hr><a class="btn btn-default btn-block" onclick="cancel(\'' +
                data.trade_no +
                "')\">取消订单</a></center>",
              {
                btn: [],
                title: "提交订单成功",
                closeBtn: false,
              }
            );
          } else if (data.code == 1) {
            alert("领取成功！");
            window.location.href = "?mod=query&type=0&qq=&page=1";
          } else if (data.code == 2) {
            if (data.type == 1) {
              layer.open({
                type: 1,
                title: "完成验证",
                skin: "layui-layer-rim",
                area: ["320px", "100px"],
                content:
                  '<div id="captcha"><div id="captcha_text">正在加载验证码</div><div id="captcha_wait"><div class="loading"><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div></div></div></div>',
                success: function () {
                  $.getScript(
                    "//static.geetest.com/static/tools/gt.js",
                    function () {
                      $.ajax({
                        url: "ajax.php?act=captcha&t=" + new Date().getTime(),
                        type: "get",
                        dataType: "json",
                        success: function (data) {
                          $("#captcha_text").hide();
                          $("#captcha_wait").show();
                          initGeetest(
                            {
                              gt: data.gt,
                              challenge: data.challenge,
                              new_captcha: data.new_captcha,
                              product: "popup",
                              width: "100%",
                              offline: !data.success,
                            },
                            handlerEmbed
                          );
                        },
                      });
                    }
                  );
                },
              });
            } else if (data.type == 2) {
              layer.open({
                type: 1,
                title: "完成验证",
                skin: "layui-layer-rim",
                area: ["320px", "260px"],
                content:
                  '<div id="captcha" style="margin: auto;"><div id="captcha_text">正在加载验证码</div></div>',
                success: function () {
                  $.getScript(
                    "//cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js",
                    function () {
                      var myCaptcha = _dx.Captcha(
                        document.getElementById("captcha"),
                        {
                          appId: data.appid,
                          type: "basic",
                          style: "embed",
                          success: handlerEmbed2,
                        }
                      );
                      myCaptcha.on("ready", function () {
                        $("#captcha_text").hide();
                      });
                    }
                  );
                },
              });
            } else if (data.type == 3) {
              layer.open({
                type: 1,
                title: "完成验证",
                skin: "layui-layer-rim",
                area: ["320px", "231px"],
                content:
                  '<div id="captcha"><div id="captcha_text">正在加载验证码</div></div>',
                success: function () {
                  $.getScript("//v.vaptcha.com/v3.js", function () {
                    vaptcha({
                      vid: data.appid,
                      type: "embed",
                      container: "#captcha",
                      offline_server:
                        "https://management.vaptcha.com/api/v3/demo/offline",
                    }).then(handlerEmbed3);
                  });
                },
              });
            }
          } else if (data.code == 3) {
            layer.alert(
              data.msg,
              {
                closeBtn: false,
              },
              function () {
                window.location.reload();
              }
            );
          } else if (data.code == 4) {
            var confirmobj = layer.confirm(
              "请登录后再购买，是否现在登录？",
              {
                btn: ["登录", "注册", "取消"],
              },
              function () {
                window.location.href = "./user/login.php";
              },
              function () {
                window.location.href = "./user/reg.php";
              },
              function () {
                layer.close(confirmobj);
              }
            );
          } else {
            layer.alert(data.msg);
          }
        },
      });
    },
  },
});
function getPoint(data) {
// 	var multi = data.multi;
// 	var count = data.count;
	var price = data.price;
// 	var shopimg = data.shopimg;
	var close = data.close;
// 	console.log(data.input);
// 	console.log(data.inputs);
	
	if(close == 1){
		$('#check_pay').html('停止下单');
		layer.alert('当前商品维护中，停止下单！');
	}else if(price == 0){
		$('#check_pay').html('立即免费领取');
	}else{
		$('#check_pay').html('立即购买');
	}
	
    $('#num').val(data.min ? data.min:'1');
	$('#num').attr("max",data.max ? data.max:'100000');
	$('#num').attr("min",data.min ? data.min:'1');
	var inputnametype = '';
	$('#inputsname').html("");
	var inputname = data.input;
	if(inputname=='hide'){
		$('#inputsname').append('<input type="hidden" name="inputvalue" id="inputvalue" value="'+$.cookie('mysid')+'"/>');
	}else{
		if(inputname=='')inputname='下单账号';
		if(inputname.indexOf('[')>0 && inputname.indexOf(']')>0){
			inputnametype = inputname.split('[')[1].split(']')[0];
			inputname = inputname.split('[')[0];
		}
		
		$('#inputsname').append('<div class="ure_info_input_box" id="inputname"><div class="ure_info_input_box_hide"><p>*</p><p>'+inputname+'</p></div><div class="input"><input id="inputvalue" name="inputvalue" type="text" value="" required class="phone_num"></div></div>');
	}
	var inputsname = data.inputs;
	if(inputsname!=''){
		$.each(inputsname.split('|'), function(i, value) {
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
					addstr += '<option value="'+i+'">'+v+'</option>';
				});
				$('#inputsname').append('<div class="layui-form-item"><label class="layui-form-label"  style="width: 100%;text-align: left;padding:0" id="inputname'+(i+2)+'">'+selectname+'：</label><div class="layui-input-"><select name="inputvalue'+(i+2)+'" id="inputvalue'+(i+2)+'" class="layui-input">'+addstr+'</select></div></div>');
			}else{
			if(value=='说说ID'||value=='说说ＩＤ'||inputsnametype=='ssid')
				var addstr='<div class="layui-btn layui-btn-sm layui-btn-normal btnee" onclick="get_shuoshuo(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())">自动获取</div>';
			else if(value=='日志ID'||value=='日志ＩＤ'||inputsnametype=='rzid')
				var addstr='<div class="layui-btn layui-btn-sm layui-btn-normal btnee" onclick="get_rizhi(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())">自动获取</div>';
			else if(value=='作品ID'||value=='作品ＩＤ'||inputsnametype=='zpid')
				var addstr='<div class="layui-btn layui-btn-sm layui-btn-normal btnee" onclick="getshareid2(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())">自动获取</div>';
			else
				var addstr='';
			if(addstr!=''){
			    
				$('#inputsname').append('<div class="layui-form-item"><label class="layui-form-label"  style="width: 100%;text-align: left;padding:0" id="inputname'+(i+2)+'" gettype="'+inputsnametype+'">'+value+'：</label><div class="layui-input-" style="padding-right: 80px !important;"><input type="text" name="inputvalue'+(i+2)+'" id="inputvalue'+(i+2)+'" value="" class="layui-input" required/></div>'+addstr+'</div>');
			}else{
				$('#inputsname').append('<div class="ure_info_input_box" id="inputname"><div class="ure_info_input_box_hide" gettype="'+inputsnametype+'"><p>*</p><p>'+value+'</p></div><div class="input"><input id="inputvalue'+(i+2)+'" name="inputvalue'+(i+2)+'" type="text" value="" required class="phone_num"></div></div>');
			}
			}
		});
	}
	if($("#inputname2").html() == '说说ID：'||$("#inputname2").html() == '说说ＩＤ：'||$("#inputname2").attr('gettype')=='ssid'){
		$('#inputvalue2').attr("disabled", true);
		$('#inputvalue2').attr("placeholder", "填写QQ账号后点击→");
	}else if($("#inputname").html() == '作品ID：'||$("#inputname").html() == '作品ＩＤ：'||$("#inputname").html() == '帖子ID：'||$("#inputname").html() == '用户ID：'||$("#inputname").html() == '用户ＩＤ：'||inputnametype=='shareid'){
		$('#inputvalue').attr("placeholder", "在此输入分享链接 可自动获取");
		$('#inputname').attr("gettype", "shareid");
		if($("#inputname2").html() == '作品ID：'||$("#inputname2").html() == '作品ＩＤ：'||$("#inputname2").attr('gettype')=='zpid'){
			$('#inputvalue2').attr("placeholder", "填写作品链接后点击→");
			$("#inputvalue2").attr('disabled', true);
		}
	}else if($("#inputname").html() == '作品链接：'||$("#inputname").html() == '视频链接：'||$("#inputname").html() == '分享链接：'||inputnametype=='shareurl'){
		$('#inputvalue').attr("placeholder", "在此输入复制后的链接 可自动转换");
		$('#inputname').attr("gettype", "shareurl");
	}else{
		$('#inputvalue').removeAttr("placeholder");
		$('#inputvalue2').removeAttr("placeholder");
	}
	if($('#tid').attr('isfaka')==1){
		$('#inputvalue').attr("placeholder", "用于接收卡密和查询订单");
		if($.cookie('email'))$('#inputvalue').val($.cookie('email'));
	}
	var alert = data.alert;
	if(alert!='' && alert!='null'){
		var ii=layer.alert(''+unescape(alert)+'',{
			btn:['我知道了'],
			title:'商品提示'
		},function(){
			layer.close(ii);
		});
	}
}
function dopay(type, orderid) {
  if (type == "rmb") {
    var ii = layer.msg("正在提交订单请稍候...", {
      icon: 16,
      shade: 0.5,
      time: 15000,
    });
    $.ajax({
      type: "POST",
      url: "ajax.php?act=payrmb",
      data: { orderid: orderid },
      dataType: "json",
      success: function (data) {
        layer.close(ii);
        if (data.code == 1) {
          alert(data.msg);
          window.location.href = "?mod=query&type=0&qq=&page=1";
        } else if (data.code == -2) {
          alert(data.msg);
          window.location.href = "?mod=query&type=0&qq=&page=1";
        } else if (data.code == -3) {
          var confirmobj = layer.confirm(
            "你的余额不足，请充值！",
            {
              btn: ["立即充值", "取消"],
            },
            function () {
              window.location.href = "./user/recharge.php";
            },
            function () {
              layer.close(confirmobj);
            }
          );
        } else if (data.code == -4) {
          var confirmobj = layer.confirm(
            "你还未登录，是否现在登录？",
            {
              btn: ["登录", "注册", "取消"],
            },
            function () {
              window.location.href = "./user/login.php";
            },
            function () {
              window.location.href = "./user/reg.php";
            },
            function () {
              layer.close(confirmobj);
            }
          );
        } else {
          layer.alert(data.msg);
        }
      },
    });
  } else {
    window.location.href =
      "other/submit.php?type=" + type + "&orderid=" + orderid;
  }
}

function cancel(orderid) {
  layer.closeAll();
  $.ajax({
    type: "POST",
    url: "ajax.php?act=cancel",
    data: { orderid: orderid, hashsalt: hashsalt },
    dataType: "json",
    async: true,
    success: function (data) {
      if (data.code == 0) {
      } else {
        layer.msg(data.msg);
        window.location.reload();
      }
    },
    error: function (data) {
      window.location.reload();
    },
  });
}
