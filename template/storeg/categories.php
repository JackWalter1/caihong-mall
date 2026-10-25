<?php
/* 
分类页面 - 显示所有一级分类和二级分类
*/
if (!defined('IN_CRONLITE')) {
    exit();
}

// 获取所有分类数据
$ar_data = array();
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 AND pid=0 ORDER BY sort ASC");
while ($res = $rs->fetch()) {
    $ar_data[] = $res;
}

// 获取所有二级分类
$sub_classes = array();
foreach ($ar_data as $primary) {
    $primary_cid = $primary['cid'];
    $sub_rs = $DB->query("SELECT * FROM pre_class WHERE active=1 AND pid='$primary_cid' ORDER BY sort ASC");
    $sub_categories = array();
    while ($sub_res = $sub_rs->fetch()) {
        $sub_categories[] = $sub_res;
    }
    if (!empty($sub_categories)) {
        $sub_classes[$primary_cid] = $sub_categories;
    }
}

// 设置页面标题
hook('pageTitle', function () use ($conf) {
    return '全部分类 - ' . $conf['sitename'];
});

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>全部分类 - <?php echo $conf['sitename']; ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/foxui/css/foxui.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/store/css/iconfont.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>assets/storeg2/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $cdnserver; ?>template/storegg/style.css">
    <!-- 引入Swiper CSS -->
    <link rel="stylesheet" href="<?php echo $cdnpublic?>Swiper/6.4.5/swiper-bundle.min.css">
    <style>
        /* 分类页面样式 */
        .category-page-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 2.5rem;
            line-height: 2.5rem;
            background-color: #fff;
            text-align: center;
            font-size: 0.9rem;
            color: #333;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }
        .category-page-content {
            margin-top: 2.5rem;
            padding: 0.5rem;
        }
        
        /* Swiper 轮播样式 */
        .swiper-container {
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            padding: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .swiper-wrapper {
            display: flex;
        }
        .swiper-slide {
            width: 100%;
            height: auto;
            display: flex;
            flex-wrap: wrap;
            margin: -0.5rem;
        }
        .goods-category {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }
        .goods-category a {
            width: 20%;
            padding: 0.5rem;
            box-sizing: border-box;
            text-align: center;
        }
        .goods-category a .shop_cat_img {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 0.5rem;
            border-radius: 50%;
            overflow: hidden;
        }
        .goods-category a .shop_cat_img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .goods-category a .shop_cat_text {
            font-size: 0.75rem;
            color: #333;
            line-height: 1.2;
        }
        .goods-category a.active .shop_cat_img {
            box-shadow: 0 0 0 2px #ff6600;
        }
        .goods-category a.active .shop_cat_text {
            color: #ff6600;
            font-weight: bold;
        }
        
        /* 二级分类样式 */
        .secondary-category-box {
            margin-top: 1rem;
            background-color: #fff;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .secondary-category-title {
            font-size: 0.85rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .secondary-category-list {
            display: flex;
            flex-wrap: wrap;
            margin: -0.35rem;
        }
        .secondary-category-list a {
            width: 25%;
            padding: 0.35rem;
            box-sizing: border-box;
        }
        .secondary-category-list a .item {
            text-align: center;
            padding: 0.35rem 0.5rem;
            background-color: #f5f5f5;
            border-radius: 15px;
            font-size: 0.7rem;
            color: #666;
        }
        .secondary-category-list a .item:hover,
        .secondary-category-list a .item.shop_active {
            background-color: #ff6600;
            color: #fff;
        }
        
        /* 分页器样式 */
        .swiper-pagination {
            margin-top: 0.5rem;
        }
        .swiper-pagination-bullet {
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            font-size: 12px;
            color: #666;
            background: #f5f5f5;
            opacity: 1;
            border-radius: 4px;
        }
        .swiper-pagination-bullet-active {
            color: #fff;
            background: #ff6600;
        }
        
        /* 响应式调整 */
        @media (max-width: 375px) {
            .goods-category a {
                width: 25%;
            }
            .secondary-category-list a {
                width: 33.33%;
            }
        }
    </style>
</head>
<body>
    <!-- 页面头部 -->
    <div class="category-page-header">
        <span>全部分类</span>
    </div>

    <!-- 页面内容 -->
    <div class="category-page-content">
        <!-- 一级分类轮播 -->
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <!-- 分类轮播幻灯片 -->
                <?php 
                $class_show_num = 5; // 每行显示5个
                $class_total = count($ar_data);
                $class_pages = ceil($class_total / ($class_show_num * 5)); // 每页显示5行
                $arry = 0; // 当前行
                $au = 0; // 当前页
                
                for ($i = 0; $i < $class_pages; $i++) {
                    echo '<div class="swiper-slide">';
                    echo '<div class="goods-category">';
                    
                    for ($j = 0; $j < $class_show_num * 5; $j++) {
                        $index = $i * $class_show_num * 5 + $j;
                        if ($index >= $class_total) break;
                        $primary = $ar_data[$index];
                        
                        echo '<a href="javascript:void(0);" data-cid="' . $primary['cid'] . '" data-name="' . $primary['name'] . '" class="get_cat">';
                        echo '<div class="shop_cat_img">';
                        echo '<img src="' . $primary['shopimg'] . '" onerror="this.src=\'assets/store/picture/1562225141902335.jpg\';">';
                        echo '</div>';
                        echo '<div class="shop_cat_text">' . $primary['name'] . '</div>';
                        echo '</a>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            <!-- 分页器 -->
            <div class="swiper-pagination"></div>
        </div>

        <!-- 二级分类区域 -->
        <?php foreach ($ar_data as $primary): ?>
            <?php if (isset($sub_classes[$primary['cid']]) && !empty($sub_classes[$primary['cid']])): ?>
                <div class="secondary-category-box">
                    <div class="secondary-category-title">
                        <?php echo $primary['name']; ?> - 子分类
                    </div>
                    <div class="secondary-category-list">
                        <?php foreach ($sub_classes[$primary['cid']] as $secondary): ?>
                            <a href="javascript:void(0);" data-cid="<?php echo $secondary['cid']; ?>" data-name="<?php echo $secondary['name']; ?>" data-primary-cid="<?php echo $primary['cid']; ?>" class="get_cat">
                                <div class="item">
                                    <?php echo $secondary['name']; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- JavaScript -->
    <script src="<?php echo $cdnpublic; ?>jquery/1.12.4/jquery.min.js"></script>
    <script src="<?php echo $cdnserver; ?>assets/foxui/js/foxui.min.js"></script>
    <script src="<?php echo $cdnserver; ?>assets/store/js/main.js"></script>
    <!-- 引入Swiper JavaScript -->
    <script src="<?php echo $cdnpublic; ?>Swiper/6.4.5/swiper-bundle.min.js"></script>
    <script>
        // 初始化Swiper
        $(document).ready(function() {
            var swiper = new Swiper('.swiper-container', {
                slidesPerView: 1,
                spaceBetween: 10,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
            });
        });
        
        // 分类点击事件处理
        $('.get_cat').click(function() {
            var cid = $(this).data('cid');
            var name = $(this).data('name');
            
            // 调用全局的get_cat函数（在main.js中定义）
            if (typeof get_cat !== 'undefined') {
                get_cat(cid, name);
            } else {
                // 如果没有get_cat函数，默认跳转逻辑
                window.location.href = './?cid=' + cid;
            }
        });
    </script>
</body>
</html>