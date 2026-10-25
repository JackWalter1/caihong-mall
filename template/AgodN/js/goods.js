// 商品相关的AJAX请求处理
const GoodsHandler = {
    // 获取单个商品的库存
    getGoodsStock: function(tid) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: "./ajax.php?act=getleftcount",
                data: {tid: tid},
                dataType: 'json',
                success: function(res) {
                    if(res.code == 0) {
                        // 确保返回的是数字类型的剩余卡密数量
                        const count = res.count;
                        if(count === '' || count === null || count === undefined) {
                            resolve(0); // 如果没有剩余卡密，返回0
                        } else {
                            resolve(parseInt(count)); // 返回剩余卡密数量
                        }
                    } else {
                        reject('获取库存失败');
                    }
                },
                error: function() {
                    reject('网络错误');
                }
            });
        });
    },

    // 批量获取发卡商品库存
    getCardProductsStock: function(tids) {
        if (!tids || tids.length === 0) return Promise.resolve({});
        const promises = tids.map(tid => 
            this.getGoodsStock(tid).catch(err => {
                console.error(`获取商品 ${tid} 库存失败:`, err);
                return undefined; // 获取失败返回undefined
            })
        );
        return Promise.all(promises).then(results => {
            const stockMap = {};
            tids.forEach((tid, index) => {
                stockMap[tid] = results[index];
            });
            return stockMap;
        });
    },

    // 加载商品列表
    loadGoods: function(cid, isSearch = false) {
        return new Promise((resolve, reject) => {
            // 创建一个加载提示的临时容器
            const $loading = $('<div class="text-center"><span class="fa fa-spinner fa-spin"></span> '+(isSearch?'搜索':'加载')+'中...</div>');
            // 将加载提示添加到商品列表后面
            $('#goodslist').after($loading);

            $('.bottom-bar').hide();

            $.ajax({
                type: isSearch ? "POST" : "GET",
                url: "./ajax.php?act=gettool",
                data: isSearch ? {kw:cid} : {cid:cid},
                dataType: 'json',
                success: async function(data) {
                    if(data.code == 0) {
                        if(data.data.length == 0) {
                            const $newContent = $('<div class="alert alert-info">未找到相关商品</div>');
                            $('#goodslist').html($newContent);
                            $loading.remove();
                            resolve();
                            return;
                        }

                        // 收集所有发卡商品ID
                        const cardProducts = data.data.filter(item => item.isfaka == 1);
                        const cardProductIds = cardProducts.map(item => item.tid);
                        
                        try {
                            // 获取所有发卡商品的库存
                            const stockMap = await GoodsHandler.getCardProductsStock(cardProductIds);
                            
                            // 处理商品数据
                            const html = data.data.map(item => {
                                let stockText;
                                if(item.isfaka == 1) {
                                    const count = stockMap[item.tid];
                                    if (count === undefined) {
                                        stockText = '库存获取失败';
                                    } else {
                                        // 根据配置的库存显示方式显示库存信息
                                        if(typeof agodn_stock_display !== 'undefined' && agodn_stock_display == 1){
                                            // 模糊库存范围显示
                                            if(count >= 1000){
                                                stockText = '库存充足';
                                            } else if(count >= 500){
                                                stockText = '库存500+';
                                            } else if(count >= 100){
                                                stockText = '库存100+';
                                            } else if(count >= 50){
                                                stockText = '库存50+';
                                            } else if(count >= 10){
                                                stockText = '库存10+';
                                            } else {
                                                stockText = '库存'+count+'张';
                                            }
                                        } else {
                                            // 准确库存显示
                                            stockText = '库存' + count + '张';
                                        }
                                    }
                                } else {
                                    // 根据配置的库存显示方式显示库存信息
                                    if(typeof agodn_stock_display !== 'undefined' && agodn_stock_display == 1 && item.stock !== null){
                                        // 模糊库存范围显示
                                        if(item.stock >= 1000){
                                            stockText = '库存充足';
                                        } else if(item.stock >= 500){
                                            stockText = '库存500+';
                                        } else if(item.stock >= 100){
                                            stockText = '库存100+';
                                        } else if(item.stock >= 50){
                                            stockText = '库存50+';
                                        } else if(item.stock >= 10){
                                            stockText = '库存10+';
                                        } else {
                                            stockText = '库存'+item.stock+'张';
                                        }
                                    } else {
                                        // 准确库存显示
                                        stockText = item.stock === null ? '无限' : '库存' + item.stock + '张';
                                    }
                                }
                                
                                return GoodsHandler.renderGoodsCard(item, stockText);
                            }).join('');

                            // 创建新内容的容器
                            const $newContent = $(html);
                            
                            // 一次性替换内容
                            $('#goodslist').html($newContent);
                            
                            if(!isSearch) {
                                history.replaceState({}, null, './?cid='+cid);
                            }
                            
                            // 移除加载提示
                            $loading.remove();
                            resolve();
                        } catch(err) {
                            console.error('获取库存失败:', err);
                            // 即使获取库存失败，也继续显示商品列表
                            const html = data.data.map(item => {
                                let stockText = item.isfaka == 1 ? '库存获取失败' : 
                                              (item.stock === null ? '无限' : '库存' + item.stock + '张');
                                return GoodsHandler.renderGoodsCard(item, stockText);
                            }).join('');
                            
                            // 创建新内容的容器
                            const $newContent = $(html);
                            
                            // 一次性替换内容
                            $('#goodslist').html($newContent);
                            
                            // 移除加载提示
                            $loading.remove();
                            resolve();
                        }
                    } else {
                        const $newContent = $('<div class="alert alert-danger">'+data.msg+'</div>');
                        $('#goodslist').html($newContent);
                        $loading.remove();
                        reject(data.msg);
                    }
                },
                error: function(err) {
                    const $newContent = $('<div class="alert alert-danger">'+(isSearch?'搜索':'加载')+'失败，请重试</div>');
                    $('#goodslist').html($newContent);
                    $loading.remove();
                    reject(err);
                }
            });
        });
    },

    // 初始化事件
    initPjax: function() {
        // 使用事件委托绑定商品点击事件
        $(document).on('click', '#goodslist .product-card', function(){
            var $this = $(this);
            var tid = $this.data('tid');
            var price = $this.data('price');
            
            if($this.hasClass('active')) {
                return;
            }
            
            $('.product-card').removeClass('active');
            $this.addClass('active');
            $('.product-desc').hide();
            
            GoodsHandler.getGoodsDetail(tid).then(function(item) {
                if(item.desc){
                    var tempDiv = document.createElement('div');
                    tempDiv.innerHTML = item.desc;
                    
                    var scripts = tempDiv.getElementsByTagName('script');
                    while(scripts[0]) {
                        scripts[0].parentNode.removeChild(scripts[0]);
                    }
                    
                    var elements = tempDiv.getElementsByTagName('*');
                    for(var i = 0; i < elements.length; i++) {
                        elements[i].removeAttribute('onclick');
                        elements[i].removeAttribute('onload');
                        elements[i].removeAttribute('onerror');
                    }
                    
                    $this.find('.product-desc-content').html(tempDiv.innerHTML);
                    $this.find('.product-desc').show();
                }
            }).catch(function(err) {
                console.error('获取商品详情失败:', err);
            });
            
            $('.pay-btn').html('立即购买 ¥' + price);
            $('.bottom-bar').show();
            
            $('.pay-btn').off('click').on('click', function(){
                var skey = hex_md5(tid+sys_key+tid);
                window.location.href = './?mod=shop&tid='+tid+'&skey='+skey;
            });
        });
    },

    // 渲染商品卡片
    renderGoodsCard: function(item, stockText) {
        let salesInfo = '';
        if(typeof agodn_show_sales !== 'undefined' && agodn_show_sales == 1) {
            salesInfo = `<div class="product-sales">已售${item.sales || 0}件</div>`;
        }
        return `<div class="product-card" data-tid="${item.tid}" data-price="${item.price}">
            <div class="product-title">${item.name}</div>
            <div class="product-info">
                <div class="product-price">¥${item.price}</div>
                ${salesInfo}
                <div class="product-stock">${stockText}</div>
            </div>
            <div class="product-desc" style="display:none;">
                <div class="product-desc-title">商品简介</div>
                <div class="product-desc-content"></div>
            </div>
        </div>`;
    },

    // 获取商品详情
    getGoodsDetail: function(tid) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "GET",
                url: "./ajax.php?act=gettool",
                data: {tid: tid},
                dataType: 'json',
                success: function(data) {
                    if(data.code == 0 && data.data[0]) {
                        resolve(data.data[0]);
                    } else {
                        reject('获取商品详情失败');
                    }
                },
                error: function(err) {
                    reject(err);
                }
            });
        });
    }
}; 