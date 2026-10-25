/**
 * Store模板图片优化脚本
 * 作者：教主
 * 博客：zhonguo.ren
 * Q群：915043052
 */

/**
 * 压缩图片URL
 * @param {string} imgUrl - 原始图片URL
 * @returns {string} - 压缩后的图片URL
 */
function compressImage(imgUrl) {
    // 检查是否为空
    if (!imgUrl) {
        return "./assets/store/picture/loadimg.gif";
    }
    
    // 检查是否是本地图片
    var localPatterns = ["./", "../", "/", "assets/"];
    var isLocal = false;
    for (var i = 0; i < localPatterns.length; i++) {
        if (imgUrl.startsWith(localPatterns[i])) {
            isLocal = true;
            break;
        }
    }
    
    // 检查是否是占位符图片
    var placeholderPatterns = ["loadimg.gif", "1562225141902335.jpg", "error_img.png"];
    var isPlaceholder = false;
    for (var j = 0; j < placeholderPatterns.length; j++) {
        if (imgUrl.includes(placeholderPatterns[j])) {
            isPlaceholder = true;
            break;
        }
    }
    
    // 如果是本地图片或占位符，直接返回
    if (isLocal || isPlaceholder) {
        return imgUrl;
    }
    
    // 检查是否已经是压缩链接
    if (imgUrl.includes("image.baidu.com/search/thumbnail") || 
        imgUrl.includes("picsum.photos") || 
        imgUrl.includes("placeholder.com") ||
        imgUrl.includes("qlogo.cn")) {
        return imgUrl;
    }
    
    // 对于外部图片，使用图片代理服务来绕过浏览器的ORB限制
    // ORB (Origin Resource Blocker) 是浏览器的安全机制，会阻止某些跨域资源的加载
    // 使用图片代理服务可以解决这个问题
    var proxyUrl = "https://images.weserv.nl/?url=" + encodeURIComponent(imgUrl);
    return proxyUrl;
}

/**
 * 优化页面所有图片加载
 */
function optimizeAllImages() {
    // 优化商品列表图片
    $('.fui-goods-item img').each(function() {
        var originalSrc = $(this).attr('src');
        if (originalSrc) {
            var compressedSrc = compressImage(originalSrc);
            $(this).attr('src', compressedSrc);
        }
        
        var originalLaySrc = $(this).attr('lay-src');
        if (originalLaySrc) {
            var compressedLaySrc = compressImage(originalLaySrc);
            $(this).attr('lay-src', compressedLaySrc);
        }
    });
    
    // 优化分类图片
    $('.device .content-slide img').each(function() {
        var originalSrc = $(this).attr('src');
        if (originalSrc) {
            var compressedSrc = compressImage(originalSrc);
            $(this).attr('src', compressedSrc);
        }
    });
    
    // 优化轮播图图片
    $('.fui-swipe-wrapper img').each(function() {
        var originalSrc = $(this).attr('src');
        if (originalSrc) {
            var compressedSrc = compressImage(originalSrc);
            $(this).attr('src', compressedSrc);
        }
    });
    
    // 优化商品详情页图片
    $('.layer-photos-demo img').each(function() {
        var originalSrc = $(this).attr('src');
        if (originalSrc) {
            var compressedSrc = compressImage(originalSrc);
            $(this).attr('src', compressedSrc);
        }
        
        var originalLayerSrc = $(this).attr('layer-src');
        if (originalLayerSrc) {
            var compressedLayerSrc = compressImage(originalLayerSrc);
            $(this).attr('layer-src', compressedLayerSrc);
        }
    });
    
    // 优化商品说明中的图片
    $('.hd_intro img').each(function() {
        var originalSrc = $(this).attr('src');
        if (originalSrc) {
            var compressedSrc = compressImage(originalSrc);
            $(this).attr('src', compressedSrc);
        }
    });
    
    // 优化产品宣传图
    $('.135brush img').each(function() {
        var originalSrc = $(this).attr('src');
        if (originalSrc) {
            var compressedSrc = compressImage(originalSrc);
            $(this).attr('src', compressedSrc);
        }
    });
}

/**
 * 延迟加载图片
 */
function lazyLoadImages() {
    // 初始化懒加载
    if (typeof $ !== 'undefined') {
        // 使用layui的懒加载
        if (typeof layui !== 'undefined' && layui.use) {
            layui.use('flow', function() {
                var flow = layui.flow;
                flow.lazyimg({
                    elem: 'img.lazy'
                    ,scroll: true
                });
            });
        }
        
        // 或者使用jquery.lazyload
        if ($.fn.lazyload) {
            $('img.lazy').lazyload({
                effect: 'fadeIn',
                threshold: 200
            });
        }
    }
}

// 页面加载完成后执行图片优化
if (typeof window.addEventListener !== 'undefined') {
    window.addEventListener('DOMContentLoaded', function() {
        // 延迟执行，确保DOM已经完全加载
        setTimeout(function() {
            optimizeAllImages();
            lazyLoadImages();
        }, 100);
    });
} else if (typeof window.attachEvent !== 'undefined') {
    window.attachEvent('onload', function() {
        optimizeAllImages();
        lazyLoadImages();
    });
}

// 导出函数供其他脚本使用
if (typeof window !== 'undefined') {
    window.ImageOptimizer = {
        compressImage: compressImage,
        optimizeAllImages: optimizeAllImages,
        lazyLoadImages: lazyLoadImages
    };
}
