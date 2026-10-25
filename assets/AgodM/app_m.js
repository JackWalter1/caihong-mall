$.get(
  "/api.php?act=siteinfo",
  function (res) {
    if (!$.cookie("open")) {
      if (res.modal != "") {
        layer.open({
          type: 1,
          title: "购卡协议",
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
});

$(document).on("click", ".goods_box", function () {
  $(this).addClass("active");
  $(this).siblings().removeClass("active");
});

function getGoodsPro(Class_ID) {
  $(`div[data-cateid='${Class_ID}']`).addClass("active");
}

const app = new Vue({
  el: "#app",
  data: {
    info: "",
    num_val: 1,
    uPir: 0,
    inputs: [], // 存储商品设置的输入框标题 - 博客地址：zhonguo.ren QQ群：915043052
    inputValues: {}, // 存储输入框的值 - 博客地址：zhonguo.ren QQ群：915043052
  },
  // 计算属性
  computed: {
    // 计算总价
    total() {
      return this.num_val * this.info.price;
    },
  },
  methods: {
    async getGoodsInfo(tid) {
      console.log("1");
      const { data: res } = await axios.get(
        `./ajax.php?act=gettool&tid=${tid}`
      );
      this.info = res.data[0];
      console.log(res.data[0]);
      
      // 处理输入框标题 - 博客地址：zhonguo.ren QQ群：915043052
      if (this.info.inputs) {
        this.inputs = this.info.inputs.split('|').map(item => item.trim()).filter(item => item);
      } else {
        this.inputs = [];
      }
      
      // 初始化输入框值 - 博客地址：zhonguo.ren QQ群：915043052
      this.inputValues = {};
      this.inputs.forEach((input, index) => {
        this.inputValues['inputvalue' + (index + 2)] = '';
      });
    },
    countoper(opr) {
      if (this.info) {
        if (opr == "add") {
          this.num_val++;
        } else if (opr == "reduce") {
          if (this.num_val > 1) {
            this.num_val--;
          }
        }
      }
    },
    PayGoods() {
      var tid = this.info.tid;
      var inputvalue = $("#inputvalue").val();
      console.log(tid, inputvalue);
      
      // 收集所有动态输入框的值 - 博客地址：zhonguo.ren QQ群：915043052
      var submitData = {
        tid: tid,
        num: $("#num").val(),
        inputvalue: $("#inputvalue").val(),
        hashsalt: hashsalt,
      };
      
      // 遍历所有动态输入框，将值添加到提交数据中 - 博客地址：zhonguo.ren QQ群：915043052
      this.inputs.forEach((input, index) => {
        var inputId = 'inputvalue' + (index + 2);
        var inputValue = $("#" + inputId).val();
        submitData[inputId] = inputValue;
      });

      $.ajax({
        type: "POST",
        url: "ajax.php?act=pay",
        data: submitData,
        dataType: "json",
        success: function (data) {
          if (data.code == 0) {
            var paymsg = "";
            if (data.pay_alipay > 0) {
              paymsg +=
                "<button class=\"btn btn-default btn-block\" onclick=\"dopay('alipay','" +
                data.trade_no +
                '\')" style="margin-top:10px;"><img src="assets/img/alipay.png" class="logo">支付宝</button>';
            }
            if (data.pay_qqpay > 0) {
              paymsg +=
                "<button class=\"btn btn-default btn-block\" onclick=\"dopay('qqpay','" +
                data.trade_no +
                '\')" style="margin-top:10px;"><img src="assets/img/qqpay.png" class="logo">QQ钱包</button>';
            }
            if (data.pay_wxpay > 0) {
              paymsg +=
                "<button class=\"btn btn-default btn-block\" onclick=\"dopay('wxpay','" +
                data.trade_no +
                '\')" style="margin-top:10px;"><img src="assets/img/wxpay.png" class="logo">微信支付</button>';
            }
            if (data.pay_usdt > 0) {
              paymsg +=
                "<button class=\"btn btn-default btn-block\" onclick=\"dopay('usdt_pay','" +
                data.trade_no +
                '\')" style="margin-top:10px;"><img src="other/usdt-trc20/static/img/tether.svg" class="logo">USDT-TRC20</button>';
            }
            if (data.pay_rmb > 0) {
              paymsg +=
                "<button class=\"btn btn-default btn-block\" onclick=\"dopay('rmb','" +
                data.trade_no +
                '\')" style="margin-top:10px;"><img src="assets/img/rmb.png" class="logo">余额支付<span class="text-muted">（剩' +
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
