var template_virtualdata = $("input[name=_template_virtualdata]").val();
var template_showsales = $("input[name=_template_showsales]").val();
var curr_time = $("input[name=_curr_time]").val();

/**
 * 优化分类图片加载
 */
function optimizeCategoryImages() {
    if (typeof ImageOptimizer !== 'undefined') {
        ImageOptimizer.optimizeAllImages();
    }
}

/**
 * 优化轮播图图片加载
 */
function optimizeBannerImages() {
    if (typeof ImageOptimizer !== 'undefined') {
        ImageOptimizer.optimizeAllImages();
    }
}

$(function() {
    $('.type-right').click(function(){
        $('#zdy_menu').toggleClass('showListType')
    });
    $('#dgncd').click(function(){
        $('#zdy_menu').toggleClass('showListType')
    });
    $('#zdy_menu ul li').click(function(){
        $(this).addClass('active').siblings('li').removeClass('active')
    })
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
        $(".goods_sort div[data-order]").attr("class","item item-price"); // 只重置有data-order属性的元素
        $(this).addClass(sort_type); 
        $(this).data("sort",sort_type_new);
        $('.goods_sort div[data-order]').removeClass('on'); // 只移除有data-order属性的元素的on类
        $(this).addClass("on");
        $("input[name=_sort_type]").val(sort);
        $("input[name=_sort]").val(sort_type);
        get_goods();
    });
    if ($(".swiper-wrapper .content-slide").length > 1) {
        var swiper = new Swiper('.swiper-container', {
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
            renderBullet: function (index, className) {
              return '<span class="' + className + '">' + (index + 1) + '</span>';
            },
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
            mousewheel: true,
            keyboard: true,
            // loop:true,
        });
        $(".swiper-button-next").show();
        $(".swiper-button-prev").show();
    }  
    jQuery(function ($) {
        $(window).resize(function () {
            var width = $('#js-com-header-area').width();
            $('.touchslider-item a').css('width', width);
            $('.touchslider-viewport').css('height', 200 * (width / 640));
        }).resize();
    });

    if(template_virtualdata == 1){
        ka();
    }

    // 优化分类图片加载
    optimizeCategoryImages();
    
    // 优化轮播图图片加载
    optimizeBannerImages();

    get_goods();
    $(".get_cat").on("click",function()
    {
        var cid = $(this).data("cid");
        var name = $(this).data("name");
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
    

    
    /*点击切换风格 - 原生JavaScript实现*/
    console.log('尝试绑定列表切换按钮事件...');
    
    // 首先定义getCookie函数
    function getCookie(name) {
        var cookieValue = null;
        if (document.cookie && document.cookie !== '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i].trim();
                // 检查是否是我们要的cookie
                if (cookie.substring(0, name.length + 1) === (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
    
    // 获取元素
    var listblockBtn = document.getElementById('listblock');
    var goodsListContainer = document.getElementById('goods-list-container');
    
    console.log('按钮元素存在:', listblockBtn ? '是' : '否');
    console.log('列表容器存在:', goodsListContainer ? '是' : '否');
    
    // 初始化列表样式
    // 优先使用cookie设置，其次使用系统默认配置
    var defaultStyle = 'grid'; // 默认网格视图
    var cookieStyle = getCookie('goods_list_style');
    
    // 检查是否有系统默认配置
    if(typeof goods_list_default_style !== 'undefined' && goods_list_default_style) {
        defaultStyle = goods_list_default_style;
    }
    
    console.log('初始化列表样式: 系统默认配置为', defaultStyle, '，cookie设置为', cookieStyle);
    
    // 确定最终使用的样式
    var finalStyle = cookieStyle || defaultStyle;
    
    if(finalStyle == 'list'){
        console.log('初始化列表样式: 使用列表视图');
        if(listblockBtn) {
            listblockBtn.setAttribute('data-state', 'gongge');
            listblockBtn.classList.remove('icon-sort');
            listblockBtn.classList.add('icon-app');
        }
        if(goodsListContainer) {
            goodsListContainer.classList.remove('block', 'three');
        }
    } else {
        console.log('初始化列表样式: 使用网格视图');
        if(listblockBtn) {
            listblockBtn.setAttribute('data-state', 'list');
            listblockBtn.classList.remove('icon-app');
            listblockBtn.classList.add('icon-sort');
        }
        if(goodsListContainer) {
            goodsListContainer.classList.add('block', 'three');
        }
    }
    
    // 直接绑定事件 - 不依赖jQuery
    if (listblockBtn) {
        console.log('按钮元素已找到，尝试绑定点击事件');
        
        // 添加点击事件
        listblockBtn.addEventListener('click', function(e) {
            console.log('列表切换按钮被点击了!');
            e.stopPropagation(); // 阻止事件冒泡
            
            // 获取当前状态
            var currentState = listblockBtn.getAttribute('data-state') || 'list';
            console.log('当前状态:', currentState);
            
            try {
                // 切换状态和样式
                if (currentState === 'gongge') {
                    listblockBtn.setAttribute('data-state', 'list');
                    listblockBtn.classList.remove('icon-app');
                    listblockBtn.classList.add('icon-sort');
                    goodsListContainer.classList.add('block', 'three');
                } else {
                    listblockBtn.setAttribute('data-state', 'gongge');
                    listblockBtn.classList.remove('icon-sort');
                    listblockBtn.classList.add('icon-app');
                    goodsListContainer.classList.remove('block', 'three');
                }
                
                // 设置cookie (使用原生JavaScript) - 保存切换后的实际样式
                var cookietime = new Date();
                cookietime.setTime(cookietime.getTime() + (86400 * 1000));
                // 确定切换后的样式
                var newStyle = currentState === 'gongge' ? 'grid' : 'list';
                document.cookie = 'goods_list_style=' + newStyle + '; expires=' + cookietime.toUTCString() + '; path=/';
                console.log('已保存cookie样式为:', newStyle);
                
                // 如果有layer库，显示消息
                if (window.layer) {
                    var index = layer.msg('加载中', {
                        icon: 16,
                        shade: 0.01
                    });
                    setTimeout(function() { layer.close(index); }, 500);
                }
                
                console.log('列表样式已成功切换');
            } catch (error) {
                console.error('切换列表风格时出错:', error);
            }
        }, false);
        
        console.log('点击事件绑定完成');
    } else {
        console.log('未找到列表切换按钮元素');
    }
        
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
    $(".flow_load").append("<div id=\"goods_list\" ></div>");
    layui.use(['flow'], function(){
        var flow = layui.flow;
        var cid  = $("input[name=_cid]").val();
        var name  = $("input[name=_cidname]").val();
        var kw = $("input[name=kw]").val();
        var sort_type = $("input[name=_sort_type]").val();
        var sort = $("input[name=_sort]").val();
        var mb = testUA('Safari')?180:100;
        var end = kw?"没有更多数据了":" ";
        limit = 9
        if(name != "")
        {
            load();
        }
        //写入数据
        $(".show_class").show();  
        flow.load({
                elem: '#goods_list' //流加载容器
                ,isAuto:true
                ,mb:mb
                ,isLazyimg:true
                ,end:end
                ,done: function(page, next){ //执行下一页的回调
                    var lis = [];
                    //以jQuery的Ajax请求为例，请求下一页数据（注意：page是从2开始返回）
                    $.ajax({
                    type : "post",
                    url : "./ajax.php?act=gettoolnew",
                    data : {page:page,limit:limit,cid:cid,kw:kw,sort_type:sort_type,sort:sort},
                    dataType : 'json',
                    timeout: 10000, // 设置超时时间为10秒
                    success : function(res) {
							$(".tag_name").hide();
							$(".tag_name ul").html("");
                            
                            //假设你的列表返回在data集合中
                            layui.each(res.data, function(index, item){
                                html = '<a class="fui-goods-item" title="'+item.name+'" href="./?mod=buy&tid='+item.tid+'">';
                                html += '<div class="image">';
                                // 如果商品没有图片，使用占位符图片
                                if(!item.shopimg){
                                    item.shopimg="./assets/store/picture/1562225141902335.jpg"
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
                                    kucun = '库存:'+item.stock+'份';
                                }

								if(template_showsales == 1){
									html += '<div style="border-radius: 4px 0 0 4px;text-align:center;padding: 1px;background-color: rgb(57, 61, 73,0.5);color: #FFFFFF;text-align: center;font-size: 10px;position: absolute;right: 1px;bottom: 1px;"><i class="layui-icon layui-icon-fire" style="font-size:10px;"></i>'+item.sales+'</div>';
								}
                                // 图片压缩处理
                                var compressedImgUrl = item.shopimg;
                                if (typeof ImageOptimizer !== 'undefined') {
                                    compressedImgUrl = ImageOptimizer.compressImage(item.shopimg);
                                }
                                html += ''+show_tag_html+'<img class="lazy" lay-src="'+compressedImgUrl+'" src="./assets/store/picture/loadimg.gif" alt="'+item.name+'">'+shoukong+'';
                                html += '</div>';
                                

                                html += '<div class="detail" style="height:unset;">';
                                html += '<div class="name" style="color: #000000;">'+item.name+'</div>';
                                html += '<div style="line-height:0.7rem;height:0.7rem;color:#b2b2b2;font-size:0.6rem;margin-top: .2rem;">'+kucun+'</div>';
                                if(item.price <=0){
                                    buy = '<div style="height: 1rem"><span class="buy" style="background-color: yellowgreen;color:#fff;display: inline-block;height: 1.1rem;line-height: 1rem;color: white;float: right;   padding: 0rem 0.35rem;width: 100%;border-radius: 0.1rem;   border: 1px solid transparent;text-align:center;">领取</span></div>';
                                }else{
                                    buy = '<div style="height: 1rem"><span class="buy" style="background: #1492fb;color:#fff;display: inline-block;height: 1.1rem;line-height: 1rem;float: right;padding: 0rem 0.35rem;width:100%;border-radius: 0.1rem;    border: 1px solid transparent;text-align:center;">购买</span></div>';
                                }

                                if(item.stock == 0){
                                    buy = '<div style="height: 1rem"><span class="buy" style="background: red;color:#fff;display: inline-block;height: 1.1rem;line-height: 1rem;float: right;padding: 0rem 0.35rem;width:100%;border-radius: 0.1rem;    border: 1px solid transparent;text-align:center;">缺货</span></div>';
                                }

								if(item.close == 1){
                                    buy = '<div style="height: 1rem"><span class="buy" style="background: red;color:#fff;display: inline-block;height: 1.1rem;line-height: 1rem;float: right;padding: 0rem 0.35rem;width:100%;border-radius: 0.1rem;    border: 1px solid transparent;text-align:center;">已下架</span></div>';
                                }

                                html += '<div class="price" style="margin-top: 0.2rem;"><span class="text" style="color: #ff5555;"> <p class="minprice">￥'+item.price+'</p> </span>'+buy+'</div>';
                                html += '</div>';
                                html += '</a>';
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
                        error:function(xhr, textStatus, errorThrown){
                            // 优化错误处理
                            if(textStatus == "timeout") {
                                layer.msg("获取数据超时，请刷新页面重试");
                            } else if(textStatus == "error") {
                                layer.msg("服务器错误，请稍后重试");
                            } else {
                                layer.msg("获取数据失败，请刷新页面重试");
                            }
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