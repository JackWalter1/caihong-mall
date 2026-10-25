var template_virtualdata = $("input[name=_template_virtualdata]").val();
var template_showsales = $("input[name=_template_showsales]").val();
var curr_time = $("input[name=_curr_time]").val();

$(function() {
    //排序点击
    $(".goods_sort .item").on("click",function(){
       var sort = $(this).data("order"); //获取排序类型
       if(!sort){
           return false;
       }
       var sort_type = $(this).data("sort"); //获取类型
       if(sort_type == "DESC")
       {
           var sort_type_new = "ASC";
       }else{
           var sort_type_new = "DESC";
       }

        //移除其他已点击
        $(".goods_sort div").attr("class","item item-price");
        $(this).addClass(sort_type); 
        $(this).data("sort",sort_type_new);
        $('.goods_sort div').removeClass('on');
        $(this).addClass("on");
        $("input[name=_sort_type]").val(sort);
        $("input[name=_sort]").val(sort_type);
        get_goods();
    });
    
    // 添加分类样式
    $("head").append('<style type="text/css">\n        .category-container { border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }\n        .primary-category-box { background-color: #fff; padding: 10px; margin-bottom: 10px; }\n        .secondary-category-box { background-color: #fff; padding: 10px; }\n        .shop_active { color: #ff6600 !important; font-weight: bold; }\n        .sub-category.shop_active { background-color: #ff6600 !important; color: white !important; }\n        .primary-category:hover { opacity: 0.8; }\n        .sub-category:hover { background-color: #e8e8e8 !important; }\n        .sub-category.shop_active:hover { background-color: #ff6600 !important; }\n        /* 二级分类横向滑动样式 */\n        .sub-categories { display: flex; overflow-x: auto; flex-wrap: nowrap; white-space: nowrap; padding-bottom: 5px; -webkit-overflow-scrolling: touch; }\n        \n        /* 默认样式（手机端）：隐藏滚动条但保留滑动功能 */\n        .sub-categories::-webkit-scrollbar { display: none; /* Chrome, Safari, Edge */ }\n        .sub-categories { -ms-overflow-style: none; /* IE and Edge */ scrollbar-width: none; /* Firefox */ }\n        \n        /* 媒体查询：电脑端显示滚动条 */\n        @media (min-width: 768px) {\n            .sub-categories::-webkit-scrollbar { display: block; height: 4px; }\n            .sub-categories::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 2px; }\n            .sub-categories::-webkit-scrollbar-thumb { background: #888; border-radius: 2px; }\n            .sub-categories::-webkit-scrollbar-thumb:hover { background: #555; }\n            .sub-categories { -ms-overflow-style: auto; scrollbar-width: thin; }\n        }\n    </style>');
    
    // 为一级分类添加点击事件，点击后显示该一级分类下所有二级分类的商品 - 作者：教主 博客：zhonguo.ren
    $("a.primary-category").on("click", function() {
        var primary_cid = $(this).data("cid");
        var primary_name = $(this).data("name");
        console.log('点击一级分类:', primary_cid, primary_name); // 调试信息
        
        // 隐藏所有二级分类
        $("a.sub-category").hide();
        
        // 只显示当前一级分类下的二级分类
        $("a.sub-category[data-primary-cid='" + primary_cid + "']").show();
        
        // 移除所有二级分类的选中状态
        $("a.sub-category").removeClass("shop_active");
        
        // 移除所有一级分类的选中状态
        $("a.primary-category").removeClass("shop_active");
        // 添加当前一级分类的选中状态
        $(this).addClass("shop_active");
        
        // 设置表单值 - 使用一级分类ID和名称
        $("input[name=_cid]").val(primary_cid);
        $("input[name=_cidname]").val(primary_name);
        
        // 清空搜索关键词，避免干扰
        $("input[name=kw]").val("");
        
        // 重置排序
        $(".goods_sort .item").removeClass("on");
        $("input[name=_sort_type]").val("sort");
        $("input[name=_sort]").val("ASC");
        
        // 显示加载中状态
        layer.msg("加载中...", {icon: 16, shade: 0.01});
        
        // 强制重新加载商品列表
        setTimeout(function() {
            get_goods();
        }, 100);
        
        // 更新URL
        history.replaceState({}, null, './?cid='+primary_cid);
        
        return false; // 阻止默认行为，避免触发get_cat的点击事件
    });
    
    // 页面加载完成后初始化
    $(document).ready(function() {
        // 获取URL中的cid参数
        var url_cid = parseInt(getQueryString("cid"));
        if(url_cid > 0) {
            // 检查是否是一级分类
            var is_primary = false;
            var primary_element = null;
            
            $(".primary-category").each(function() {
                if(parseInt($(this).data("cid")) === url_cid) {
                    is_primary = true;
                    primary_element = $(this);
                    return false; // 退出循环
                }
            });
            
            if(is_primary && primary_element) {
                // 是一级分类，直接触发点击事件
                primary_element.click();
            } else {
                // 是二级分类，查找对应的一级分类
                var sub_element = $(".sub-category[data-cid='" + url_cid + "']");
                if(sub_element.length > 0) {
                    var primary_cid = sub_element.data("primary-cid");
                    var primary_element = $(".primary-category[data-cid='" + primary_cid + "']");
                    
                    // 先显示对应一级分类的二级分类
                    $(".sub-category").hide();
                    $(".sub-category[data-primary-cid='" + primary_cid + "']").show();
                    
                    // 设置选中状态
                    $(".primary-category").removeClass("shop_active");
                    $(".sub-category").removeClass("shop_active");
                    sub_element.addClass("shop_active");
                }
            }
        } else {
            // 如果没有cid参数，检查是否有默认选中的一级分类
            var active_primary = $(".primary-category.shop_active");
            if (active_primary.length > 0) {
                var active_primary_cid = active_primary.data("cid");
                $(".sub-category").hide();
                $(".sub-category[data-primary-cid='" + active_primary_cid + "']").show();
            }
        }
    });
    
    // 获取URL参数的辅助函数
    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }
    
    if(template_virtualdata == 1){
        ka();
    }

    get_goods();
    $(".get_cat").on("click",function()
    {
        // 如果是一级分类，跳过处理，让 a.primary-category 的处理程序处理
        if($(this).hasClass("primary-category")){
            return false;
        }
        
        var cid = $(this).data("cid");
        var name = $(this).data("name");
        tags(cid);
        if($(this).hasClass("shop_active")){
            //return false;
        }
        $('.device .content-slide a').removeClass('shop_active');
        $("input[name=kw]").val("");
        $("input[name=_cid]").val(cid);
        $("input[name=_cidname]").val(name);
        get_goods();
        $(this).addClass('shop_active');
		history.replaceState({}, null, './?cid='+cid);
    });
    //点击搜索拦截
    $("#goods_search").submit(function(e){
      var km = $("input[name=kw]").val();
      if(km == "")
      {
          layer.msg("请输入关键词进行查询");
          return false;
      }
      $("input[name=_cid]").val("");
      $("input[name=_cidname]").val("");
    $(".catname_show").html("正在获取数据");
    $(".show_class").hide();
    $('.device .content-slide a').removeClass('shop_active');
      get_goods();
     document.activeElement.blur();
      return false;
    });
    
    if($.cookie('goods_list_style') == 'list'){
        $("#listblock").data("state","gongge");
        $("#listblock").removeClass("icon-sort");
        $("#listblock").addClass("icon-app");
        $("#goods-list-container").removeClass("block three");
    }
    
    /*点击切换风格*/
    $("#listblock").on("click",function(){
        var index = layer.msg('加载中', {
          icon: 16
          ,shade: 0.01
        });
        var attr = $(this).data("state");
        if(attr == 'gongge'){
            $(this).data("state","list");
            $(this).removeClass("icon-app");
            $(this).addClass("icon-sort");
            $("#goods-list-container").addClass("block three");
        }else{
            $(this).data("state","gongge");
            $(this).removeClass("icon-sort");
            $(this).addClass("icon-app");
            $("#goods-list-container").removeClass("block three");
        }
        //设置cookie
        var cookietime = new Date(); 
        cookietime.setTime(cookietime.getTime() + (86400));
        $.cookie('goods_list_style', attr, { expires: cookietime });
        layer.close(index);
    });
        
    //弹窗广告
    if( !$.cookie('op')){
        $('.tzgg').show();
        $.cookie('op', false, { expires: 1});
    }
    
        /**
     * 兼容iphone
     * @type {number | boolean | *}
     */
    var isIphoneX = window.devicePixelRatio && window.devicePixelRatio === 3 && window.screen.width === 375 && testUA('iPhone');

    if (isIphoneX && window.history.length <= 2) {
        // document.body.classList.add('fix-iphonex-bottom');
//        $(".fui-navbar,.cart-list,.fui-footer,.fui-content.navbar").addClass('iphonex')
        $(".fui-navbar").css("bottom", "0px");
    } else {
        $(".fui-navbar,.cart-list,.fui-footer,.fui-content.navbar").removeClass('iphonex');
    }
});

function ka() {
	setInterval("get_data()",6000);
}
function get_data() {
	$.ajax({
		type : "get",
		url : "./other/getdatashow.php",
		async: true,
		dataType : 'json',
		timeout: 5000, // 设置5秒超时时间
		success : function(data) {
			if(data.code==1){
				$('#xn_text').text(data.text+" "+data.time+'前');
				$('#xn_text').fadeIn(1000);
				setTimeout("$('#xn_text').fadeOut(1000);",4000);
			}
		}
	});
}



function testUA(str) {
    return navigator.userAgent.indexOf(str) > -1
}

function load(text="加载中")
{
    var index = layer.msg(text, {
        icon: 16
        ,shade: 0.01
    });  
}

//获取商品
function get_goods(){
    $("#goods_list").remove();
    $("div.flow_load").append("<div id=\"goods_list\" ></div>");
    layui.use(['flow'], function(){
        var flow = layui.flow;
        var cid  = $("input[name=_cid]").val();
        var name  = $("input[name=_cidname]").val();
        var kw = $("input[name=kw]").val();
        var sort_type = $("input[name=_sort_type]").val();
        var sort = $("input[name=_sort]").val();
        // 检查是否是一级分类点击 - 作者：教主 博客：zhonguo.ren
        var is_primary_cat = 0;
        var active_primary = $("a.primary-category.shop_active");
        var cid_val = $("input[name=_cid]").val();
        
        // 检查条件：1. 有激活的一级分类 2. cid不为空且为数字 3. 没有激活的二级分类
        if(active_primary.length > 0 && cid_val && !isNaN(parseInt(cid_val))) {
            // 验证该cid确实是一个一级分类的ID
            var is_valid_primary = false;
            $("a.primary-category").each(function() {
                if(parseInt($(this).data("cid")) === parseInt(cid_val)) {
                    is_valid_primary = true;
                    return false;
                }
            });
            
            if(is_valid_primary) {
                is_primary_cat = 1;
                console.log('检测到一级分类视图，cid:', cid_val);
            }
        }
        var mb = testUA('Safari')?180:100;
        var end = kw?"没有更多数据了":" ";
        limit = 9
        if(name != "")
        {
            load();
        }
        //写入数据
        $("div.show_class").show();  
        
        // 先清空列表容器内容，确保每次切换分类都能重新开始 - 作者：教主 博客：zhonguo.ren
        $("#goods_list").html('');
        
        // 重置flow的加载状态 - 作者：教主 博客：zhonguo.ren
        if (window.flowInstance) {
            window.flowInstance.destroy ? window.flowInstance.destroy() : $("#goods_list").data('flow-status', null);
        }
        
        // 重新初始化flow - 作者：教主 博客：zhonguo.ren
        window.flowInstance = flow.load({
                elem: '#goods_list' //流加载容器
                ,isAuto:true
                ,mb:mb
                ,isLazyimg:true
                ,end:end
                ,done: function(page, next){ //执行下一页的回调
                    console.log('加载第', page, '页，cid:', cid, '一级分类:', is_primary_cat);
                    var lis = [];
                    //以jQuery的Ajax请求为例，请求下一页数据（注意：page是从2开始返回）
                    $.ajax({
                    type : "post",
                    url : "./ajax.php?act=gettoolnew",
                    data : {page:page,limit:limit,cid:cid,kw:kw,sort_type:sort_type,sort:sort,is_primary_cat:is_primary_cat},
                    dataType : 'json',
                    timeout: 10000, // 设置10秒超时时间
                    success : function(res) {
							$(".tag_name").hide();
							$(".tag_name ul").html("");
                            
                            //假设你的列表返回在data集合中
                            layui.each(res.data, function(index, item){
                                html = '<a class="fui-goods-item" title="'+item.name+'" href="./?mod=buy&tid='+item.tid+'">';
                                html += '<div class="image">';
                                if(!item.shopimg){
                                    item.shopimg="/assets/img/Product/default.png"
                                }
                                if(item.show_tag){
                                    show_tag = item.show_tag;
                                }else{
                                    if((curr_time-(item.addtime)) <= 259200)
                                    {
                                        show_tag = "新款";
                                    }else{
                                        show_tag = "";
                                    }
                                }


                                //显示商品标签
                                show_tag_html = "";
                                if(show_tag)
                                {
                                    show_tag_html = '<div style="transform: rotate(-45deg);background-color: #FF0000;color:#FFFFFF;width: 100px;text-align: center;margin-top: 15px;margin-left: -27px;font-size: 14px;position: absolute;">'+show_tag+'</div>';
                                }

                                //库存为0的
                                var shoukong = '';
                                var kucun = '';
                                if(item.is_stock_err == 1){
                                    shoukong = '<img  class="lazy" lay-src="./assets/store/picture/ysb.png" alt="" style="width:100%;top: 0;position: absolute;height:100%">';
                                }

                                if(item.stock > 0){
                                    kucun = '售后保障';
                                }

								if(template_showsales == 1){
									html += '<div style="border-radius: 4px 0 0 4px;text-align:center;padding: 1px;background-color: rgb(57, 61, 73,0.5);color: #FFFFFF;text-align: center;font-size: 10px;position: absolute;right: 1px;bottom: 1px;"><i class="layui-icon layui-icon-fire" style="font-size:10px;"></i>'+item.sales+'</div>';
								}
                                html += ''+show_tag_html+'<img class="lazy" lay-src="'+item.shopimg+'" src="./assets/store/picture/loadimg.gif" alt="'+item.name+'">'+shoukong+'';
                                html += '</div>';
                                

                                html += '<div class="detail" style="height:unset;">';
                                html += '<div class="name" style="color: #000000;">'+item.name+'</div>';
                                html += '<div style="line-height:0.7rem;height:0.7rem;color:#b2b2b2;font-size:0.6rem;margin-top: .2rem;">'+kucun+'</div>';
                                if(item.price <=0){
                                    buy = '<div style="height: 1rem"><span class="buy" style="background-color: yellowgreen;color:#fff;display: inline-block;height: 1.1rem;line-height: 1rem;color: white;float: right;   padding: 0rem 0.35rem;width: 100%;border-radius: 0.1rem;   border: 1px solid transparent;text-align:center;">领取</span></div>';
                                }else{
                                    buy = '<div style="height: 1rem;"><span class="buy" style="color: #8A652E;background: #F7D282;position: absolute;right: 0;">立即购买</span></div>';
                                }

                                if(item.stock == 0){
                                    buy = '<div style="height: 1rem"><span class="buy" style="background: red;color:#fff;display: inline-block;height: 1.1rem;line-height: 1rem;float: right;padding: 0rem 0.35rem;width:100%;border-radius: 0.1rem;    border: 1px solid transparent;text-align:center;">缺货</span></div>';
                                }

								if(item.close == 1){
                                    buy = '';
                                }

                                 html += '<div class="price" style="margin-top: -0.1rem;"><span class="text" style="color: #ff5555;"> <p class="minprice">￥' + item.price + '</p> </span><p class="layui-icon layui-icon-wallet" style="font-size: 0.62rem;position:absolute;margin-left:28%;font-weight: 300;color:#000000;">售后保障 </p>' + buy + '</div>';
                            html += '</div>';
                            html += '</a>';
                            html += '<div style="height: 3px"></div>';
                            lis.push(html);
                            });
                            if(name == "")
                            {
                                $(".catname_show").html('系统共有<font style="color:#ed414a;">'+res.total+'</font>个商品');
                            }else{
                                $(".catname_show").html('<font style="color:#ed414a;">'+name+'</font>共有<font style="color:#ed414a;">'+res.total+'</font>个商品');
                            }
                            if(kw != ""){
                                $(".catname_show").html('包含<font style="color:#ed414a;">'+kw+'</font>共有<font style="color:#ed414a;">'+res.total+'</font>个商品');
                            }
                            layer.closeAll();
                            next(lis.join(''), page < res.pages);
                        },
                        error:function(data){
                            layer.msg("获取数据超时");
                            layer.closeAll();
                            return false;
                        }
                });
                }
          });
        
    });
}

var audio_init = {
	changeClass: function (target,id) {
       	var className = $(target).attr('class');
       	var ids = document.getElementById(id);
       	(className == 'on')
           	? $(target).removeClass('on').addClass('off')
           	: $(target).removeClass('off').addClass('on');
       	(className == 'on')
           	? ids.pause()
           	: ids.play();
   	},
	play:function(){
		document.getElementById('media').play();
	}
}
if($('#audio-play').is(':visible')){
	audio_init.play();
}

/*layui.use(['util'], function(){
    var util = layui.util;
    //固定块客服
    util.fixbar({
        bar1: true
        ,bar2: true
        ,css: {right:8,bottom: '25%','z-index':1}
        ,bgcolor: '#393D49'
        ,click: function(type){
          if(type === 'bar1'){
            window.location.href = ("./?mod=kf");
          } else if(type === 'bar2') {
            window.location.href = ("./?mod=articlelist");
          }
        }
    });
});*/