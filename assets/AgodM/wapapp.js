$(function () {
    resize();
    $(window).resize(resize);
    $.x_show = function (title, url, width)
    {
        var index = layer.open({
            type: 2,
            fix: false,
            maxmin: true,
            shadeClose: false,
            area: [width, 'auto'],
            shade: 0.4,
            title: title,
            content: url,
            success: function (layero, index) {
                layer.iframeAuto(index);
            }
        });
        return index;
    }
    function resize() {
        if ($(window).width() > 550) {
            $.nyroModalSettings({
                type: "iframe",
                minHeight: 440,
                minWidth: 550,
                titleFromIframe: true,
                modal: true
            });
        } else {
            $.nyroModalSettings({
                type: "iframe",
                minHeight: 500,
                minWidth: 550,
                titleFromIframe: true,
                modal: true
            });
        }
    }

    if (user_popup_message != "") {
        $("#myOnPageContent").html(user_popup_message);
        $("#popup_gg").trigger("click");
    }
    $("#isagree").click(function () {
        $("#agreement").toggle();
    });
    $("[name=isagree]").click(function () {
        $("#agreement").toggle();
    });

    // 获取批发详情
    $("#isdiscount").click(function () {
        var goodid = $("#goodid").val();
        $.post("/ajax/getdiscountdetails", {goodid: goodid}, function (data) {
            $("#discountdetail").html(data);
        });
        $("#discountdetail").toggle();
    });

    $("#isemail").click(function () {
        $("#email").toggle();
        if ($(this).is(":checked")) {
            $("[name=sms_price]").val(0);
            $("#email").toggle();
            $("[name=email]").focus();
            is_contact_limit = "email";
            $('[name="is_contact_limit"]').val("email");
        } else {
            is_contact_limit = is_contact_limit_default;
            $('[name="is_contact_limit"]').val(is_contact_limit_default);
        }
        goodDiscount();
        goodschk();
    });
    $("#is_coupon").click(function () {
        if ($(this).is(":checked")) {
            $("#couponcode").show();
            $("[name=couponcode]").focus();
        } else {
            $("#couponcode").hide();
            $("#checkcoupon").hide();
        }
    });
    $("#is_rev_sms").click(function () {
        $("#email").toggle();
        if ($(this).is(":checked")) {
            $("#is_rev_sms_tip").show();
            $("[name=contact]").focus();
            $("[name=sms_price]").val($(this).attr("data-cost"));
            is_contact_limit = "mobile";
            $('[name="is_contact_limit"]').val("mobile");
        } else {
            $("#is_rev_sms_tip").hide();
            $("[name=sms_price]").val(0);
            is_contact_limit = is_contact_limit_default;
            $('[name="is_contact_limit"]').val(is_contact_limit_default);
        }
        goodDiscount();
        goodschk();
        updateContactLimit();
    });
    $("#is_pwdforsearch").click(function () {
        if ($(this).is(":checked")) {
            $("#pwdforsearchinput").show();
            $("[name=pwdforsearch2]").focus();
        } else {
            $("#pwdforsearchinput").hide();
        }
    });




    $.get("/index/Order/getLastOrder", {}, function (res) {
        if (res.code == 1) {
            layer.open({
                type: 1,
                title: "温馨提示",
                content: '您在' + res.data.success_at + "前已支付完成一笔" + res.data.total_price + "元订单，订单号" + res.data.trade_no + "，是否前往取卡？",
                btn: ["去取卡", "忽略"],
                yes: function () {
                    window.location.href = "/orderquery?orderid=" + res.data.trade_no;
                }
            });
        }
    }, "json");


    $.post("/index/Plugin/chatParams", {userid: userid}, function (res) {
        if (res.code == 1) {
            loadScript(res.data.src, function () {});
        }
    }, "json");
});

var selectcard_fee = 0;

function checkCoupon() {
    var couponcode = $.trim($("[name=couponcode]").val());
    if (cateid == 0) {
        cateid = $("#cateid").val();
    }
    $("#checkcoupon").show();
    $.post("/ajax/checkcoupon",
            {
                couponcode: couponcode,
                userid: userid,
                cateid: cateid,
                t: new Date().getTime()
            },
            function (data) {
                if (data) {
                    data = eval(data);
                    if (data.result == 0) {
                        $("#checkcoupon").html(data.msg);
                    } else if (data.result == 1) {
                        $("[name=coupon_ctype]").val(data.ctype);
                        $("[name=coupon_value]").val(data.coupon);
                        $('[name=is_coupon]').val("1");
                        $("#checkcoupon").html('<span class="blue">此优惠券可用</span>');
                        goodschk();
                    } else {
                        $("#checkcoupon").html("验证失败！");
                    }
                }
            },
            "json"
            );
}



function select_card_quantity() {
    var quantity = $("[name=cardquantity]").val();
    quantity = quantity - 1;
    $(".card_list_add").html("");
    for (var i = 1; i <= quantity; i++) {
        $(".card_list_add").append($(".card_list:first").clone());
    }
}

function selectcateid() {
    var cateid = $("#cateid").val();
    $("[name=couponcode]").val("");

    if (cateid > 0) {
        $.post("/ajax/getgoodlistjson", {cateid: cateid}, function (data) {
            $('.goods_list').empty();

            data.map(function (item, index, array) {

                var stockStr = "";
                if (item.stockStr == "库存不足" || item.stockStr == "库存0张")
                {
                    stockStr = " <div class='goods_remark'><span>" + item.stockStr + "</span></div>";
                } else {
                    stockStr = " <div class='goods_remark' style='color:#0db26a;background:#e1fff2'><span>" + item.stockStr + "</span></div>";
                }

                var html = '<div class="goods_box" style="cursor:pointer" data-goods_id="' + item.id + '">' +
                        '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAwCAMAAABdTmjTAAABU1BMVEUzaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf////9hi//b5f/d5v/z9v81av84bf9Acv9Bc/9Edf9Jef9Me/9NfP9Wg/9Xg/81a/9rkv9tlP9ulf92mv99oP9/of+Gpv+Nq/+Qrf+UsP+fuP+huv+uxP+vxP+yx//B0f/C0v/J2P/M2f83bP8+cf/i6v/t8v/u8//x9P88b//5+v/6+//8/f/9/f/+/v89cf+QnoDYAAAAQHRSTlMAAQIDBgoMDxMVFhwfIiQsLS83PkJDTlFUW2Nma3Z4f4KHiJSWmZykq66xtLy9wcPEyNDS09vg4+rs8PP1+fz+e7c8kwAAATpJREFUeF6V0ldPAlEYhGGRRcQu9t7FrqgIImUO1d672Hv3/1cP0Q3Z8EGG9/q5mkxZCblKsJWzvHV4g7Q1pkBj+xhobBsBjwfA4x7wuDPK47YIaNwcBo3dK6BxfQA0rvGDxi4faOycB40dM6CxMQEa2zzg8RB43Aced4PHHVEet4ZB46YQaNwYBI3rAhaTXi2Cq5dg6TKxVRC7Fq32VKnnWAFc4bXa/S+l1LGMjWmr3U5qm4GI7eOmekS29Sdtb3ZFbBs17cHrHoD0vbbxH4h40LQ7SfV5Alxp+7IJEffC7FbpMmfafsQg4q7cedbu1H9HEHF7BLkerv/sBUTcEoal8+/sECkRu/OOdviu4m+QcMMy8oolNiDhWj+EUpBwlQ9iEnYugCmUtY45cGlrTILG5R7weBg87gffL8pDCUuj0TqxAAAAAElFTkSuQmCC" class="icon">' +
                        '<div></div>' +
                        '<div class="goods_name">' + item.name + '</div>' +
                        '<div class="goods_price">￥' + item.price + '</div>' + stockStr +
                        '</div>';
                $('.goods_list').append(html);
            })

            $('.goods_box:first').click();
        });
    }
    getrate();

}

function selectgoodid() {
    var goodid = $("#goodid").val();
    if ($("[name=couponcode]").val() != "") {
        checkCoupon();
    }
    $.post("/ajax/getgoodinfo", {goodid: goodid}, function (data) {
        if (data) {
            $("[name=price]").val(data.price);
            $("#remark").html(data.remark);

            if (data.is_coupon == 0) {
                //取消掉优惠券码
                $("[name=couponcode]").val("");
                $("#coupon_btn").hide();
            }
            if (data.is_coupon == 1) {
                $("#coupon_btn").show();
            }
            if (data.is_pwdforsearch == 0) {
                $("#pwdforsearch2").hide();
                $("#pwdforsearch1").hide();
            }
            if (data.is_pwdforsearch == 1) {
                $("#pwdforsearch2").hide();
                $("#pwdforsearch1").show();
            }
            if (data.is_pwdforsearch == 2) {
                $("#pwdforsearch1").hide();
                $("#pwdforsearch2").show();
            }
            //显示批发价格
            if (data.is_discount == 1) {
                $("#isdiscount_span").css("display", "inline");
                $.post('/ajax/getdiscountdetails', {goodid: goodid}, function (data) {
                    $('.sale_message span').html(data);
                });

            }
            //隐藏批发价格标签
            if (data.is_discount == 0) {
                $("#isdiscount_span").css("display", "none");
            }
            if (data.limit_quantity > 0) {
                $("[name=quantity]").val(data.limit_quantity);
                $("[name=quantity]").attr({
                    title: "本商品最少购买数量为" + data.limit_quantity + "件！"
                });
                $("#limit_quantity_tip").show();
            } else {
                $("[name=quantity]").val(1);
                $("[name=quantity]").removeAttr("title");
                $("#limit_quantity_tip").hide();
            }

            if (data.sms_payer == 1) {
                $("#is_rev_sms").attr("data-cost", 0);
            } else {
                $("#is_rev_sms").attr("data-cost", data.sms_price);
            }
            $("[name=sms_price]").val($("#is_rev_sms").is(":checked") ? $("#is_rev_sms").attr("data-cost") : 0);

            $("[name=kucun]").val(data.kucun);
            $('[name="limit_quantity"]').val(data.limit_quantity);
            $('[name="is_contact_limit"]').val(data.contact_limit);
            $(".contact_limit_tip").text(data.contact_limit);
            $("[name=danjia]").val(data.price);
            $("#goodInvent").html(data.goodinvent);
            $("[name=is_discount]").val(data.is_discount);
            getrate();
            goodDiscount();
            $(".pinfo1").hide();
            $(".pinfo2").show();
            $(".pinfo3").hide();
            if (data.is_pwdforbuy == 1) {
                getPwdforbuy();
            }
            if (data.card_order == 3) {
                $(".xh_box").show();
                selectForm();
            } else {
                $("[name=select_cards]").val("");
                layer.close(select_card_form);
                $(".xh_box").hide();
                $(".xh_box_content").html("<p style='color: #74788d;padding:10px'>未指定号码，系统将随机发货！</p>");
            }
        }
    }, "json");
}


function selectLable(ids, titles, selectcard_fee_temp)
{
    selectcard_fee = selectcard_fee_temp;
    if (ids.length > 0)
    {
        $("[name=quantity]").val(ids.length);
        $("[name=select_cards]").val(ids);
        var style = "<style>.list-group {display: -webkit-box;display: -ms-flexbox;display: flex;-webkit-box-orient: vertical;-webkit-box-direction: normal;-ms-flex-direction: column;flex-direction: column;padding-left: 0;margin-bottom: 0;border-radius: .15rem;}.list-group-item {position: relative;display: block;padding: .25rem .25rem;background-color: #fff;border: 1px solid #eff2f7;background:#f9f9f9}</style>";
        var content = style + '<ul class="list-group" style="max-height: 200px;overflow: hidden;overflow-y: scroll;width: 100%;">';
        $.each(titles, function (index, value) {
            content += '<li class="list-group-item"><p style="margin-bottom:0px;color: #545454;">' + value + '</p></li>';
        });
        content += "</ul><p style='color: #74788d;padding:10px'>您已指定" + ids.length + "个账号，如下单慢被别人买走或下单数量超出指定数量的将会随机发货。</p>";

    } else {
        var content = "<p style='color: #74788d;padding:10px'>未指定号码，系统将随机发货！</p>";
    }
    changequantity();
    $(".xh_box_content").html(content);
}


var check_password_form;
function getPwdforbuy() {
    check_password_form = $.x_show('购买密码验证', '/ajax/checkpwdforbuy?goods_id=' + $('[name=goodid]').val(), '90%');
}
function closePwdforbuy()
{
    layer.close(check_password_form);
}

var select_card_form;
function selectForm() {
    select_card_form = $.x_show('自助选号', '/ajax/selectcard?goods_id=' + $('[name=goodid]').val(), '90%');
}
function closeSelectForm()
{
    layer.close(select_card_form);
}

function verify_pwdforbuy() {
    var pwdforbuy = $.trim($("[name=pwdforbuy]").val());
    if (pwdforbuy == "") {
        layer.alert("请填写验证码！");
        $("[name=pwdforbuy]").focus();
        return false;
    }
    var reg = /^([a-z0-9A-Z]+){6,20}$/;
    if (!reg.test(pwdforbuy)) {
        layer.alert("验证码格式为6-20个长度，数字、大小写字母或组合！");
        $("[name=pwdforbuy]").focus();
        return false;
    }
    $("#verify_pwdforbuy").attr("disabled", true);
    $("#verify_pwdforbuy_msg").show();
    var goodid = $("#goodid").val();
    $.post(
            "/ajax/checkpwdforbuy",
            {pwdforbuy: pwdforbuy, goodid: goodid, t: new Date().getTime()},
            function (data) {
                if (data == "ok") {
                    $("#verify_pwdforbuy_msg").hide();
                    layer.alert("验证成功，请继续购买！");
                    parent.$.nyroModalRemove();
                } else {
                    $("#verify_pwdforbuy_msg").hide();
                    layer.alert(data);
                    $("#verify_pwdforbuy").attr("disabled", false);
                    return false;
                }
            }
    );
}

function changequantity() {
    var quantity = $.trim($("[name=quantity]").val());
    if (isNaN(quantity) || quantity <= 0 || quantity == "") {
        layer.alert("购买数量填写错误，最小数量为1！");
        $("[name=quantity]").focus();
        $("[name=quantity]").val(1);
    }

    var limit_quantity = $('[name="limit_quantity"]').val();
    if (parseInt(quantity) < parseInt(limit_quantity)) {
        layer.alert("购买数量填写错误，最小数量为" + limit_quantity + "件！");
        $("[name=quantity]").focus();
        $("[name=quantity]").val(limit_quantity);
    }
    goodDiscount();
    goodschk();
}

function goodDiscount() {
    var is_discount = $("[name=is_discount]").val();
    var quantity = parseInt($.trim($("[name=quantity]").val()));
    var goodid = $("#goodid").val();
    if (is_discount == 1) {
        $.post("/ajax/getdiscount", {goodid: goodid, quantity: quantity}, function (data) {
            if (data > 0) {
                $("[name=price]").val(data);
                $("[name=danjia]").val(data);
                goodschk();
            }
        });
    }
}

function getrate() {
    var goodid = $("[name=goodid]").val();
    var cateid = $("[name=cateid]").val();
    var channelid = $("[name=pid]").val();

    if (goodid == "") {
        goodid = 0;
    }
    if (cateid == "") {
        cateid = 0;
    }
    $.post("/ajax/getrate", {userid: userid, cateid: cateid, goodid: goodid, channelid: channelid}, function (data) {
        $("[name=rate]").val(data);
        goodschk();
    });
}



function goodschk() {
    var dprice = parseFloat($("[name=price]").val()) || 0;
    var quantity = parseInt($.trim($("[name=quantity]").val()));
    var rate = parseFloat($("[name=rate]").val());
    var tprice = parseFloat(((dprice * quantity) / rate) * 100);


    //判断是否需要买家承担费率
    var feePayer = parseInt($('[name=feePayer]').val()) || 1;
    var feePrice = 0;
    if (2 === feePayer) {
        //加上买方需要承担的费率
        var feeRate = parseFloat($('[name=fee_rate]').val()) || 0;
        feePrice = parseFloat(feeRate * tprice);
        var minPrice = parseFloat($('[name=min_fee]').val()) || 0;
        if (minPrice > feePrice)
            feePrice = minPrice;
        tprice += feePrice;
    }

    var gprice = parseFloat(dprice * quantity);
    var coupon_ctype = $("[name=coupon_ctype]").val();
    var coupon_value = $("[name=coupon_value]").val();
    if (coupon_ctype == 1) {
        tprice = tprice - coupon_value;
    } else if (coupon_ctype == 100) {
        tprice = parseFloat(tprice - (tprice * coupon_value) / 100);
    }
    var sms_price = $("[name=sms_price]").val();
    tprice = tprice + parseFloat(sms_price);
    tprice = $("#card").attr("checked") ? Math.ceil(tprice.toFixed(2)) : tprice.toFixed(2);
    gprice = $("#card").attr("checked") ? Math.ceil(gprice.toFixed(2)) : gprice.toFixed(2);
    if (2 === feePayer) {
        $('.to_pay > span > p').text('含手续费: ' + parseFloat(feePrice).toFixed(2) + '元');
    } else {
        $('.to_pay > span > p').text('');
    }
    $("[name=paymoney]").val(tprice);

    if (selectcard_fee > 0)
    {
        $('.to_pay > span > p').text($('.to_pay > span > p').text() + " 选号费" + selectcard_fee + "元");
    }
    tprice = Number(tprice) + Number(selectcard_fee);

    $('.to_pay > span > span').text("立即支付￥" + tprice);

}


$(function () {
    $('#check_pay').click(function () {
        var cateid = $("[name=cateid]").val();
        if (cateid == "") {
            layer.alert("请选择商品分类！");
            $("[name=cateid]").focus();
            return false;
        }
        var goodid = $("[name=goodid]").val();
        if (goodid == "") {
            layer.alert("请选择要购买的商品！");
            $("[name=goodid]").focus();
            return false;
        }
        var quantity = $.trim($("[name=quantity]").val());
        if (isNaN(quantity) || quantity <= 0 || quantity == "") {
            layer.alert("购买数量填写错误，最小数量为1！");
            $("[name=quantity]").focus();
            return false;
        }
        var limit_quantity = $('[name="limit_quantity"]').val();
        if (parseInt(quantity) < parseInt(limit_quantity)) {
            layer.alert("购买数量填写错误，最小数量为" + limit_quantity + "件！");
            $("[name=quantity]").focus();
            return false;
        }
        var kucun = $("[name=kucun]").val();
        kucun = kucun == "" ? 0 : parseInt(kucun);
        if (kucun == 0) {
            layer.alert("库存为空，暂无法购买！");
            $("[name=quantity]").focus();
            return false;
        }
        if (kucun > 0 && quantity > kucun) {
            layer.alert("库存不足，请修改购买数量！");
            $("[name=quantity]").focus();
            return false;
        }
        var is_contact_limit = $('[name="is_contact_limit"]').val();
        var contact = $.trim($("[name=contact]").val());
        var email = $.trim($("[name=email]").val());
        if (contact == "" || contact.length < 6) {
            layer.alert("请填写联系方式，联系方式最短为6位哦！");
            $("[name=contact]").focus();
            return false;
        }

        switch (is_contact_limit) {
            case "qq":
                var reg = /^[\d]+$/;
                if (!reg.test(contact)) {
                    layer.alert("联系方式必须QQ！");
                    $("[name=contact]").focus();
                    return false;
                }
                break;
            case "2":
                var reg = /^[a-zA-Z]+$/;
                if (!reg.test(contact)) {
                    layer.alert("联系方式必须全部为英文字母！");
                    $("[name=contact]").focus();
                    return false;
                }
                break;
            case "3":
                var reg = /^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i;
                if (!reg.test(contact)) {
                    layer.alert("联系方式必须为数字和字母的组合！");
                    $("[name=contact]").focus();
                    return false;
                }
                break;
            case "4":
                var reg = /^(([a-z]+)([_])([a-z]+)|([0-9]+)([_])([0-9]+))$/i;
                if (!reg.test(contact)) {
                    layer.alert("联系方式必须有数字和下划红或者字母和下划线组合！");
                    $("[name=contact]").focus();
                    return false;
                }
                break;
            case "5":
                var reg = /[\u4e00-\u9fa5]/;
                if (!reg.test(contact)) {
                    layer.alert("联系方式必须是中文！");
                    $("[name=contact]").focus();
                    return false;
                }
                break;
            case "email":
                var reg = /^([0-9a-zA-Z_-])+@([0-9a-zA-Z_-])+((\.[0-9a-zA-Z_-]{2,3}){1,2})$/;
                if (!reg.test(email)) {
                    layer.alert("联系方式必须是邮箱！");
                    $("[name=contact]").focus();
                    return false;
                }
                break;
            case "mobile":
                var reg = /^(\d){11}$/;
                if (!reg.test(contact)) {
                    layer.alert("联系方式必须为您的手机号码！");
                    $("[name=contact]").focus();
                    return false;
                }
                break;
        }
        if ($("#pwdforsearch1").is(":visible")) {
            var pwdforsearch = $.trim($("[name=pwdforsearch1]").val());
            var reg = /^([0-9A-Za-z]+){6,20}$/;
            if (pwdforsearch == "" || !reg.test(pwdforsearch)) {
                layer.alert("请填写取卡密码！长度为6-20个数字、字母或组合！");
                $("[name=pwdforsearch1]").focus();
                return false;
            }
        }
        if ($("#is_pwdforsearch").is(":checked")) {
            var pwdforsearch = $.trim($("[name=pwdforsearch2]").val());
            var reg = /^([0-9A-Za-z]+){6,20}$/;
            if (pwdforsearch == "" || !reg.test(pwdforsearch)) {
                layer.alert("您选择了使用取卡密码，请填写取卡密码！长度为6-20个数字、字母或组合！");
                $("[name=pwdforsearch2]").focus();
                return false;
            }
        }
        if ($("#isemail").is(":checked")) {
            if ($.trim($("[name=email]").val()) == "") {
                layer.alert("您选择了把卡密信息发送到邮箱，推荐您填写常用的邮箱哦！");
                $("[name=email]").focus();
                return false;
            }
        }
        if ($("#is_coupon").is(":checked")) {
            if ($.trim($("[name=couponcode]").val()) == "") {
                layer.alert("您选择了使用优惠券，但您没有填写优惠券！");
                $("[name=couponcode]").focus();
                return false;
            }
            var coupon_ctype = $("[name=coupon_ctype]").val();
            if (coupon_ctype == 0) {
                layer.alert("您选择了使用优惠券，但所填写的优惠券无效！");
                $("[name=couponcode]").focus();
                return false;
            }
        }

        $("#payform").submit();
        //  return true;
    });

});


function setFeeRate(rate) {
    $('[name=fee_rate]').val(rate);
    goodschk();
}

function updateContactLimit() {
    return;
}

function loadScript(url, callback) {
    var script = document.createElement("script")
    script.type = "text/javascript";
    if (script.readyState) { //IE
        script.onreadystatechange = function () {
            if (script.readyState == "loaded" || script.readyState == "complete") {
                script.onreadystatechange = null;
                callback();
            }
        };
    } else { //Others
        script.onload = function () {
            callback();
        };
    }
    script.src = url;
    document.getElementsByTagName("head")[0].appendChild(script);
}