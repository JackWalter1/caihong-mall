<?php

include './includes/common.php';
?><!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title><?php echo $conf['sitename'];?> - 上架日志</title>
		<link href="//lib.baomitu.com/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="//lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
		<style>
			* {
				margin: 0;
				padding: 0;
				box-sizing: border-box;
			}
			
			body {
				background-color: #f5f5f5;
				font-family: 'Microsoft YaHei', Arial, sans-serif;
				line-height: 1.6;
				color: #333;
			}
			
			.container {
				max-width: 1200px;
				margin: 0 auto;
				padding: 20px;
			}
			
			.page-title {
				font-size: 24px;
				font-weight: bold;
				color: #333;
				margin: 0;
			}
			
			/* 头部容器样式 */
			.header-container {
				display: flex;
				align-items: center;
				justify-content: space-between;
				margin-bottom: 30px;
			}
			
			.panel {
				background: white;
				border-radius: 10px;
				margin-bottom: 25px;
				box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
				overflow: hidden;
				transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
			}
			
			.panel-heading {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 20px 25px;
				background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
				color: white;
				cursor: pointer;
				transition: all 0.3s ease;
			}
			
			.panel-heading:hover {
				background: linear-gradient(135deg, #ff8a8e 0%, #fac0b4 100%);
			}
			
			.panel-heading-left {
				display: flex;
				align-items: center;
			}
			
			.date-text {
				font-size: 16px;
				font-weight: 500;
				margin-right: 20px;
			}
			
			.today-badge {
				font-size: 18px;
				font-weight: 600;
			}
			
			.item-count {
				color: #fff;
				font-weight: normal;
				margin-left: 10px;
				font-size: 14px;
			}
			
			.toggle-btn {
				background: rgba(255, 255, 255, 0.2);
				border: 2px solid rgba(255, 255, 255, 0.3);
				color: white;
				padding: 8px 16px;
				border-radius: 20px;
				font-size: 14px;
				cursor: pointer;
				transition: all 0.3s ease-in-out;
				display: flex;
				align-items: center;
				gap: 5px;
				position: relative;
				overflow: hidden;
			}
			
			.toggle-btn .circle {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				width: 20px;
				height: 20px;
				background-color: white;
				border-radius: 50%;
				opacity: 0;
				transition: all 0.3s ease-in-out;
			}
			
			.toggle-btn .toggle-text {
				position: relative;
				z-index: 1;
				transition: all 0.3s ease-in-out;
			}
			
			.toggle-btn:hover {
				background: rgba(255, 255, 255, 0.3);
				border-color: rgba(255, 255, 255, 0.4);
				box-shadow: 0 0 0 8px transparent;
				border-radius: 24px;
			}
			
			.toggle-btn:hover .circle {
				width: 150px;
				height: 150px;
				opacity: 0.2;
			}
			
			.toggle-btn:hover .toggle-text {
				transform: translateX(8px);
			}
			
			.toggle-btn:active {
				scale: 0.95;
				box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.3);
			}
			
			.toggle-icon {
				font-size: 12px;
				transition: transform 0.3s ease;
				position: relative;
				z-index: 1;
			}
			
			.toggle-icon.rotated {
				transform: rotate(180deg);
			}
			
			.panel-body {
				padding: 0 25px;
				border-top: 1px solid #f0f0f0;
				overflow: hidden;
				max-height: 0;
				opacity: 0;
				transition: padding 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
							max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
							opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
			}
			
			.panel-body.open {
				padding: 25px;
				max-height: 2000px;
				opacity: 1;
			}
			
			.items-container {
				position: relative;
			}
			
			.visible-items {
				margin-bottom: 20px;
			}
			
			.hidden-items {
				display: none;
			}
			
			.product-link {
				display: inline-block;
				background: #f9f9f9;
				border: 1px solid #e0e0e0;
				border-radius: 8px;
				padding: 12px 16px;
				margin: 5px;
				font-size: 14px;
				color: #333;
				text-decoration: none;
				transition: all 0.3s ease;
				max-width: calc(33.333% - 10px);
				min-width: 280px;
			}
			
			.product-link:hover {
				background: #f5f5f5;
				box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
				color: #4d9cf8;
				text-decoration: none;
			}
			
			@media (max-width: 768px) {
				.container {
					padding: 15px;
				}
				
				.panel-heading {
					flex-direction: column;
					align-items: flex-start;
					gap: 10px;
				}
				
				.panel-heading-left {
					flex-direction: column;
					align-items: flex-start;
					gap: 5px;
				}
				
				.date-text {
					margin-right: 0;
				}
				
				.toggle-btn {
					align-self: flex-end;
				}
				
				.product-link {
					max-width: 100%;
					min-width: unset;
					width: 100%;
					margin: 5px 0;
				}
			}
			
			/* 动画按钮样式 */
			.animated-button {
				position: relative;
				display: flex;
				align-items: center;
				gap: 4px;
				padding: 12px 24px;
				border: 4px solid;
				border-color: transparent;
				font-size: 14px;
				background-color: inherit;
				border-radius: 100px;
				font-weight: 600;
				color: greenyellow;
				box-shadow: 0 0 0 2px greenyellow;
				cursor: pointer;
				overflow: hidden;
				transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
				margin: 0;
				width: fit-content;
			}
			
			.animated-button svg {
				position: absolute;
				width: 24px;
				fill: greenyellow;
				z-index: 9;
				transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
			}
			
			.animated-button .arr-1 {
				right: 16px;
			}
			
			.animated-button .arr-2 {
				left: -25%;
			}
			
			.animated-button .circle {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				width: 20px;
				height: 20px;
				background-color: greenyellow;
				border-radius: 50%;
				opacity: 0;
				transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
			}
			
			.animated-button .text {
				position: relative;
				z-index: 1;
				transform: translateX(-12px);
				transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
			}
			
			.animated-button:hover {
				box-shadow: 0 0 0 12px transparent;
				color: #212121;
				border-radius: 12px;
			}
			
			.animated-button:hover .arr-1 {
				right: -25%;
			}
			
			.animated-button:hover .arr-2 {
				left: 16px;
			}
			
			.animated-button:hover .text {
				transform: translateX(12px);
			}
			
			.animated-button:hover svg {
				fill: #212121;
			}
			
			.animated-button:active {
				scale: 0.95;
				box-shadow: 0 0 0 4px greenyellow;
			}
			
			.animated-button:hover .circle {
				width: 220px;
				height: 220px;
				opacity: 1;
			}
			
			/* 响应式设计 */
			@media (max-width: 768px) {
				.header-container {
					flex-direction: row;
					align-items: center;
					gap: 10px;
					flex-wrap: wrap;
				}
				
				.page-title {
					font-size: 20px;
				}
				
				.animated-button {
					font-size: 12px;
					padding: 10px 16px;
				}
				
				.animated-button .arr-1 {
					right: 4px;
				}
				
				.animated-button .arr-2 {
					right: 4px;
				}
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="header-container">
				<!-- 动画返回按钮 -->
				<a href="/" class="animated-button">
				  <svg viewBox="0 0 24 24" class="arr-2" xmlns="http://www.w3.org/2000/svg">
				    <path
				      d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
				    ></path>
				  </svg>
				  <span class="text"><<< 返回首页</span>
				  <span class="circle"></span>
				  <svg viewBox="0 0 24 24" class="arr-1" xmlns="http://www.w3.org/2000/svg">
				    <path
				      d="M16.1716 10.9999L10.8076 5.63589L12.2218 4.22168L20 11.9999L12.2218 19.778L10.8076 18.3638L16.1716 12.9999H4V10.9999H16.1716Z"
				    ></path>
				  </svg>
				</a>
				<h1 class="page-title">上架日志</h1>
			</div>
			
			<?php 
// 按日期分组日志
$logs_by_date = array();
$rs = $DB->query("SELECT * FROM pre_toollogs ORDER BY date DESC");
while ($res = $rs->fetch()) {
	$date = $res['date'];
	if (!isset($logs_by_date[$date])) {
		$logs_by_date[$date] = array();
	}
	$logs_by_date[$date][] = $res['content'];
}

// 遍历分组后的日志
foreach ($logs_by_date as $date => $contents) {
	// 合并同一天的所有商品
	$all_products = array();
	foreach ($contents as $content) {
		$products = explode("\n", trim($content));
		$products = array_filter($products);
		$all_products = array_merge($all_products, $products);
	}
	$count = count($all_products);
	
	// 分离可见和隐藏的商品
	$visible_products = array_slice($all_products, 0, 30); // 显示前30个
	$hidden_products = array_slice($all_products, 30); // 隐藏剩余的
	
	echo '<div class="panel">
						<div class="panel-heading" onclick="togglePanel(this)">
							<div class="panel-heading-left">
								<span class="date-text">' . htmlspecialchars($date) . '</span>
								<span class="today-badge">今日上架</span>
								<span class="item-count">(' . $count . ' 件商品)</span>
							</div>
							<button class="toggle-btn">
							<span class="toggle-text">展开面板</span> <span class="toggle-icon">▼</span>
							<span class="circle"></span>
						</button>
						</div>
						<div class="panel-body">
							<div class="items-container">
								<div class="visible-items">';
									
									foreach ($visible_products as $product) {
									if (trim($product)) {
										$goods_name = str_replace('上架：', '', $product);
										$goods = $DB->getRow("SELECT tid, cid FROM pre_tools WHERE name = :name LIMIT 1", array(':name' => $goods_name));
										if ($goods) {
											echo '<a class="product-link" href="./?cid=' . $goods['cid'] . '&tid=' . $goods['tid'] . '">' . htmlspecialchars($product) . '</a>';
										} else {
											echo '<a class="product-link" href="./">' . htmlspecialchars($product) . '</a>';
										}
									}
								}
									
								echo '</div>';
								
								if (!empty($hidden_products)) {
									echo '<div class="hidden-items">';
										foreach ($hidden_products as $product) {
									if (trim($product)) {
										$goods_name = str_replace('上架：', '', $product);
										$goods = $DB->getRow("SELECT tid, cid FROM pre_tools WHERE name = :name LIMIT 1", array(':name' => $goods_name));
										if ($goods) {
											echo '<a class="product-link" href="./?cid=' . $goods['cid'] . '&tid=' . $goods['tid'] . '">' . htmlspecialchars($product) . '</a>';
										} else {
											echo '<a class="product-link" href="./">' . htmlspecialchars($product) . '</a>';
										}
									}
								}
									echo '</div>';
								}
								
							echo '</div>
							</div>
						</div>';
					
}
?>
		</div>
		
		<script>
			function togglePanel(panelHeading) {
				const panel = panelHeading.closest('.panel');
				const body = panel.querySelector('.panel-body');
				const btn = panelHeading.querySelector('.toggle-btn');
				const icon = btn.querySelector('.toggle-icon');
				const hiddenItems = panel.querySelector('.hidden-items');
				
				if (!body.classList.contains('open')) {
					// 展开面板
					body.classList.add('open');
					btn.innerHTML = '<span class="toggle-text">收起面板</span> <span class="toggle-icon rotated">▼</span><span class="circle"></span>';
					if (hiddenItems) {
						hiddenItems.style.display = 'block';
					}
				} else {
					// 收起面板
					body.classList.remove('open');
					btn.innerHTML = '<span class="toggle-text">展开面板</span> <span class="toggle-icon">▼</span><span class="circle"></span>';
					if (hiddenItems) {
						hiddenItems.style.display = 'none';
					}
				}
			}
			
			// 默认展开第一个面板
			document.addEventListener('DOMContentLoaded', function() {
				const firstPanel = document.querySelector('.panel-heading');
				if (firstPanel) {
					const body = firstPanel.closest('.panel').querySelector('.panel-body');
					body.classList.add('open');
					const btn = firstPanel.querySelector('.toggle-btn');
					btn.innerHTML = '<span class="toggle-text">收起面板</span> <span class="toggle-icon rotated">▼</span><span class="circle"></span>';
					const hiddenItems = firstPanel.closest('.panel').querySelector('.hidden-items');
					if (hiddenItems) {
						hiddenItems.style.display = 'block';
					}
				}
			});
		</script>
	</body>
</html>