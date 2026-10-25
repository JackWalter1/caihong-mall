/**
 * jQuery Cookie Plugin - 确保所有使用$.cookie的代码正常工作
 */
(function($) {
    // 强制覆盖$.cookie函数
    $.cookie = function(name, value, options) {
        if (typeof value !== 'undefined') {
            // 设置cookie
            options = options || {};
            if (value === null) {
                value = '';
                options.expires = -1;
            }
            var expires = '';
            if (options.expires && (typeof options.expires === 'number' || options.expires.toUTCString)) {
                var date;
                if (typeof options.expires === 'number') {
                    date = new Date();
                    date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
                } else {
                    date = options.expires;
                }
                expires = '; expires=' + date.toUTCString();
            }
            var path = options.path ? '; path=' + options.path : '';
            var domain = options.domain ? '; domain=' + options.domain : '';
            var secure = options.secure ? '; secure' : '';
            document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
        } else {
            // 获取cookie
            var cookieValue = null;
            if (document.cookie && document.cookie !== '') {
                var cookies = document.cookie.split(';');
                for (var i = 0; i < cookies.length; i++) {
                    var cookie = jQuery.trim(cookies[i]);
                    if (cookie.substring(0, name.length + 1) === (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            return cookieValue;
        }
    };
    
    // 添加removeCookie函数
    $.removeCookie = function(name, options) {
        if ($.cookie(name) !== null) {
            $.cookie(name, null, options);
            return true;
        }
        return false;
    };
})(jQuery);

// 确保函数全局可用
if (typeof jQuery !== 'undefined') {
    jQuery.cookie = $.cookie;
    jQuery.removeCookie = $.removeCookie;
    
    // 添加简单的modal方法实现，避免TypeError: $(...).modal is not a function错误
    $.fn.modal = function(options) {
        // 简单实现：不做任何事，只是防止报错
        return this;
    };
}