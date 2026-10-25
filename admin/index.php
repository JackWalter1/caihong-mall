<?php
/* 
QQ群915043052
个人博客blog.6v6.ren
*/
/**
 * 自助下单系统
 **/
include("../includes/common.php");
eval(base64_decode('aWYoIWRlZmluZWQoJ1NJVEVfQVVUSF9WRVJJRklFRCcpKXtodHRwX3Jlc3BvbnNlX2NvZGUoNDA0KTtoZWFkZXIoIkhUVFAvMS4xIDQwNCBOb3QgRm91bmQiKTtleGl0O30='));
$title = '彩虹自助下单系统管理中心';
include './head.php';
if ($islogin == 1) {
    // 记录访问统计
    $today = date('Y-m-d');
    $ip = x_real_ip();
    
    // 创建访问统计表（如果不存在）
    $DB->query("CREATE TABLE IF NOT EXISTS shua_visit_statistics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date DATE NOT NULL UNIQUE,
        visits INT NOT NULL DEFAULT 0,
        ip_count INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // 创建IP记录表（如果不存在）
    $DB->query("CREATE TABLE IF NOT EXISTS shua_visit_ips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date DATE NOT NULL,
        ip VARCHAR(45) NOT NULL,
        url VARCHAR(255) DEFAULT '-',
        user_agent TEXT,
        region VARCHAR(100) DEFAULT '未知地区',
        visits INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_ip_date (date, ip)
    )");

    $column_exists = $DB->getColumn("SHOW COLUMNS FROM shua_class LIKE 'pid'");
    if(!$column_exists){
        $DB->exec("ALTER TABLE shua_class ADD COLUMN pid int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父分类ID，0表示一级分类' AFTER `zid`");
    }

    $notice_column_exists = $DB->getColumn("SHOW COLUMNS FROM shua_class LIKE 'notice'");
    if(!$notice_column_exists){
        $DB->exec("ALTER TABLE shua_class ADD COLUMN notice text COMMENT '分类提示语，前台选中时弹窗显示' AFTER `blockpay`");
    }

    $result = $DB->query("DESCRIBE pre_site");
    $columns = [];
    while ($row = $result->fetch()) {
        $columns[] = $row['Field'];
    }
    if (!in_array('reg_ip', $columns)) {
        $DB->exec("ALTER TABLE `pre_site` ADD COLUMN `reg_ip` VARCHAR(50) DEFAULT NULL COMMENT '注册IP' AFTER `qq`");
        $DB->exec("ALTER TABLE `pre_site` ADD INDEX `idx_reg_ip` (`reg_ip`)");
    }

    $DB->exec("CREATE TABLE IF NOT EXISTS " . DBQZ . "article (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        keywords VARCHAR(255) DEFAULT '',
        description VARCHAR(255) DEFAULT '',
        content TEXT NOT NULL,
        top TINYINT DEFAULT 0,
        addtime DATE NOT NULL,
        count INT DEFAULT 0,
        active TINYINT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $DB->exec("CREATE TABLE IF NOT EXISTS `pre_region_price_rules` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
        `region_name` varchar(100) NOT NULL COMMENT '地区名称',
        `region_keywords` text NOT NULL COMMENT '地区关键词，逗号分隔',
        `add_price_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '加价类型：1固定金额，2百分比',
        `add_price_value` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '加价数值',
        `min_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最低价格限制',
        `max_price` decimal(10,2) NOT NULL DEFAULT '999999.99' COMMENT '最高价格限制',
        `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1启用，0禁用',
        `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
        `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
        PRIMARY KEY (`id`),
        KEY `idx_status` (`status`),
        KEY `idx_sort_order` (`sort_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地区加价规则表'");

    $DB->exec("CREATE TABLE IF NOT EXISTS `pre_region_price_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
        `order_id` varchar(50) NOT NULL COMMENT '订单号',
        `tid` int(11) NOT NULL COMMENT '商品ID',
        `tool_name` varchar(255) NOT NULL COMMENT '商品名称',
        `original_price` decimal(10,2) NOT NULL COMMENT '原价',
        `final_price` decimal(10,2) NOT NULL COMMENT '最终价格',
        `add_price_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '加价金额',
        `region_id` int(11) DEFAULT NULL COMMENT '匹配的规则ID',
        `region_name` varchar(100) DEFAULT NULL COMMENT '地区名称',
        `add_price_type` tinyint(1) DEFAULT NULL COMMENT '加价类型',
        `add_price_value` decimal(10,2) DEFAULT NULL COMMENT '加价数值',
        `address` text COMMENT '收货地址',
        `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        PRIMARY KEY (`id`),
        KEY `idx_order_id` (`order_id`),
        KEY `idx_tid` (`tid`),
        KEY `idx_region_id` (`region_id`),
        KEY `idx_create_time` (`create_time`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地区加价计算日志表'");

    $check_pay_address = $DB->getColumn("SHOW COLUMNS FROM `pre_pay` LIKE 'address'");
    if(!$check_pay_address){
        $DB->exec("ALTER TABLE `pre_pay` ADD COLUMN `address` VARCHAR(500) DEFAULT '' COMMENT '收货地址' AFTER `input`");
    }

    $check_orders_address = $DB->getColumn("SHOW COLUMNS FROM `pre_orders` LIKE 'address'");
    if(!$check_orders_address){
        $DB->exec("ALTER TABLE `pre_orders` ADD COLUMN `address` VARCHAR(500) DEFAULT '' COMMENT '收货地址' AFTER `input5`");
    }

    $check_cart_address = $DB->getColumn("SHOW COLUMNS FROM `pre_cart` LIKE 'address'");
    if(!$check_cart_address){
        $DB->exec("ALTER TABLE `pre_cart` ADD COLUMN `address` VARCHAR(500) DEFAULT '' COMMENT '收货地址' AFTER `input`");
    }

    $DB->exec("CREATE TABLE IF NOT EXISTS shua_admin_login_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_type TINYINT NOT NULL DEFAULT 0,
        admin_user VARCHAR(255) NOT NULL,
        login_ip VARCHAR(50) NOT NULL,
        login_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_admin (admin_type, admin_user),
        INDEX idx_ip (login_ip),
        INDEX idx_time (login_time)
    )");

    $current_ip = x_real_ip();
    $history_count = $DB->getColumn("SELECT COUNT(*) FROM shua_admin_login_history WHERE admin_type = :type AND admin_user = :user AND login_ip = :ip", array(
        ':type' => $admintypeid,
        ':user' => $user,
        ':ip' => $current_ip
    ));
    $has_other_ip = $DB->getColumn("SELECT COUNT(*) FROM shua_admin_login_history WHERE admin_type = :type AND admin_user = :user AND login_ip != :ip", array(
        ':type' => $admintypeid,
        ':user' => $user,
        ':ip' => $current_ip
    ));
    $abnormal_ip = ($history_count == 0 && $has_other_ip > 0);
    
    // 检查今日是否已有记录
    $today_record = $DB->getRow("SELECT * FROM shua_visit_statistics WHERE date = :date", array(':date' => $today));
    
    if ($today_record) {
        // 更新访问次数
        $DB->query("UPDATE shua_visit_statistics SET visits = visits + 1 WHERE date = :date", array(':date' => $today));
    } else {
        // 创建今日记录
        $DB->query("INSERT INTO shua_visit_statistics (date, visits, ip_count) VALUES (:date, 1, 0)", array(':date' => $today));
    }
    
    // 获取当前URL和User Agent
    $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '-';
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '-';
    
    // 检查当前IP今日是否已有记录
    $ip_record = $DB->getRow("SELECT * FROM shua_visit_ips WHERE date = :date AND ip = :ip", array(':date' => $today, ':ip' => $ip));
    
    if ($ip_record) {
        // 更新IP访问次数和最新URL
        $DB->query("UPDATE shua_visit_ips SET visits = visits + 1, url = :url, user_agent = :user_agent WHERE date = :date AND ip = :ip", array(':date' => $today, ':ip' => $ip, ':url' => $url, ':user_agent' => $user_agent));
    } else {
        // 创建新IP记录
        $DB->query("INSERT INTO shua_visit_ips (date, ip, url, user_agent) VALUES (:date, :ip, :url, :user_agent)", array(':date' => $today, ':ip' => $ip, ':url' => $url, ':user_agent' => $user_agent));
        // 更新今日IP数
        $DB->query("UPDATE shua_visit_statistics SET ip_count = ip_count + 1 WHERE date = :date", array(':date' => $today));
    }
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<?php
$mysqlversion = $DB->getColumn("select VERSION()");
$sec_msg = sec_check();
?>
<style>
	.stat-card {
		display: flex;
		align-items: center;
		padding: 16px 18px;
		border-radius: 8px;
		background: #ffffff;
		border: 1px solid #e8ecf1;
		margin-bottom: 16px;
		width: 100%;
		transition: box-shadow 0.25s ease, transform 0.25s ease, border-color 0.25s ease;
	}
	.stat-card:hover {
		box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
		transform: translateY(-2px);
		border-color: #d0d5dd;
	}
	.stat-icon {
		display: flex;
		align-items: center;
		justify-content: center;
		width: 48px;
		height: 48px;
		border-radius: 8px;
		margin-right: 14px;
		font-size: 20px;
		flex-shrink: 0;
	}
	.stat-content h3 {
		margin: 0 0 2px 0;
		font-size: 18px;
		font-weight: 700;
		color: #1e293b;
		line-height: 1.2;
	}
	.stat-content p {
		margin: 0;
		font-size: 12px;
		color: #94a3b8;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.neumorphic-card {
		background: #ffffff;
		border-radius: 8px;
		border: 1px solid #e8ecf1;
		padding: 24px;
		margin-bottom: 20px;
		transition: box-shadow 0.25s ease;
	}
	.neumorphic-card:hover {
		box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
	}
	.neumorphic-card h3 {
		margin-top: 0;
	}
	.neumorphic-inset {
		background: #f8fafc;
		border-radius: 6px;
		border: 1px solid #e8ecf1;
		padding: 16px;
	}

	.gradient-primary  { background: #3b82f6; color: white; }
	.gradient-success  { background: #22c55e; color: white; }
	.gradient-warning  { background: #f59e0b; color: white; }
	.gradient-danger   { background: #ef4444; color: white; }
	.gradient-info     { background: #06b6d4; color: white; }
	.gradient-amber    { background: #f97316; color: white; }
	.gradient-blue     { background: #6366f1; color: white; }

	.text-gradient-primary  { color: #3b82f6; }
	.text-gradient-success  { color: #22c55e; }
	.text-gradient-warning  { color: #f59e0b; }
	.text-gradient-danger   { color: #ef4444; }
	.text-gradient-info     { color: #06b6d4; }

	.font-bold { font-weight: 700; }

	.chart-container {
		position: relative;
		height: 300px;
		width: 100%;
	}
	.chart-container canvas {
		max-width: 100% !important;
	}

	::-webkit-scrollbar {
		width: 6px;
		height: 6px;
	}
	::-webkit-scrollbar-track {
		background: #f1f5f9;
		border-radius: 3px;
	}
	::-webkit-scrollbar-thumb {
		background: #cbd5e1;
		border-radius: 3px;
	}
	::-webkit-scrollbar-thumb:hover {
		background: #94a3b8;
	}

	@keyframes badgePulse {
		0%, 100% { opacity: 1; }
		50% { opacity: 0.7; }
	}
	.badge-pulse {
		animation: badgePulse 2s infinite;
	}

	@keyframes fadeInUp {
		from { opacity: 0; transform: translateY(12px); }
		to   { opacity: 1; transform: translateY(0); }
	}
	.dashboard-animate {
		animation: fadeInUp 0.5s ease forwards;
		opacity: 0;
	}
	.dashboard-animate:nth-child(1) { animation-delay: 0.05s; }
	.dashboard-animate:nth-child(2) { animation-delay: 0.10s; }
	.dashboard-animate:nth-child(3) { animation-delay: 0.15s; }
	.dashboard-animate:nth-child(4) { animation-delay: 0.20s; }

	/* 站长推荐 — 平滑滑动轮播 */
	.recommend-carousel {
		overflow: hidden;
		width: 100%;
		position: relative;
		border-radius: 4px;
	}
	.recommend-track {
		display: flex;
		gap: 10px;
		width: max-content;
		will-change: transform;
	}
	.recommend-track a {
		flex: 0 0 auto;
		display: block;
		height: 110px;
		border-radius: 4px;
		overflow: hidden;
		background: #fff;
	}
	.recommend-track img {
		display: block;
		height: 100%;
		width: auto;
		max-width: none;
		object-fit: cover;
	}
	@keyframes recommendScroll {
		from { transform: translateX(0); }
		to   { transform: translateX(var(--rc-half)); }
	}
	.recommend-track.rc-scroll {
		animation: recommendScroll 22s linear infinite;
	}
	.recommend-track.rc-scroll:hover {
		animation-play-state: paused;
	}

	@media (max-width: 767px) {
		.stat-card {
			padding: 14px;
		}
		.stat-icon {
			width: 42px;
			height: 42px;
			font-size: 18px;
			margin-right: 12px;
		}
		.stat-content h3 {
			font-size: 15px;
		}
		.stat-content p {
			font-size: 11px;
		}
		.neumorphic-card {
			padding: 16px;
		}
	}

	@media (max-width: 480px) {
		.stat-card {
			flex-direction: column;
			align-items: flex-start;
			padding: 14px;
		}
		.stat-icon {
			margin-right: 0;
			margin-bottom: 10px;
			width: 38px;
			height: 38px;
			font-size: 16px;
		}
	}
</style>

<?php if($abnormal_ip){ ?>
<div id="abnormalAlertBox" style="display:none;">
    <div style="padding:5px 0; line-height:1.8; font-size:14px;">
        <p style="color:#e74c3c; font-size:16px; font-weight:bold; margin-bottom:10px;">
            <i class="fa fa-exclamation-triangle"></i> 异地登录安全提醒
        </p>
        <p>系统检测到您当前的登录IP <strong style="color:#e74c3c;"><?php echo $current_ip; ?></strong> 与历史登录IP不一致，可能存在异地登录风险。</p>
        <p style="margin-top:10px; color:#666; font-size:13px;">如非本人操作，建议立即 <a href="login.php?logout" style="color:#3498db;">退出登录</a> 并修改密码。</p>
    </div>
</div>
<script>
$(function(){
    var tipContent = $('#abnormalAlertBox').html();
    layer.open({
        type: 1,
        title: '<i class="fa fa-shield"></i> 安全提醒',
        skin: 'layui-layer-dialog',
        area: ['480px', 'auto'],
        content: tipContent,
        btn: ['我知道了', '退出登录'],
        yes: function(index){
            layer.close(index);
        },
        btn2: function(index){
            window.location.href = 'login.php?logout';
            return false;
        },
        closeBtn: 1,
        shadeClose: false
    });
});
</script>
<?php } ?>

<!-- 顶部数据统计卡片区域 -->
<div class="row">
	<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 dashboard-animate">
		<div class="stat-card" style="cursor: pointer;" onclick="window.location.href='./list.php'">
			<div class="stat-icon gradient-primary">
				<i class="fa fa-list-ol"></i>
			</div>
			<div class="stat-content">
				<h3 class="text-gradient-primary"><span id="count1">-</span></h3>
				<p>订单总数</p>
			</div>
		</div>
	</div>

	<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 dashboard-animate">
		<div class="stat-card" style="cursor: pointer;" onclick="window.location.href='./list.php?type=0'">
			<div class="stat-icon gradient-warning">
				<i class="fa fa-hourglass-half"></i>
			</div>
			<div class="stat-content">
				<h3 class="text-gradient-warning"><span id="count3">-</span></h3>
				<p>待处理订单</p>
			</div>
		</div>
	</div>

	<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 dashboard-animate">
		<div class="stat-card" style="cursor: pointer;" onclick="window.location.href='./list.php'">
			<div class="stat-icon gradient-success">
				<i class="fa fa-calendar-check-o"></i>
			</div>
			<div class="stat-content">
				<h3 class="text-gradient-success"><span id="count4">-</span></h3>
				<p>今日订单数</p>
			</div>
		</div>
	</div>

	<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 dashboard-animate">
		<div class="stat-card" style="cursor: pointer;" onclick="window.location.href='./list.php'">
			<div class="stat-icon gradient-info">
				<i class="fa fa-line-chart"></i>
			</div>
			<div class="stat-content">
				<h3 class="text-gradient-info">￥<span id="count5">-</span></h3>
				<p>今日交易额</p>
			</div>
		</div>
	</div>

	<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 dashboard-animate">
		<div class="stat-card" style="cursor: pointer;" onclick="window.location.href='./record.php'">
			<div class="stat-icon gradient-amber">
				<i class="fa fa-money"></i>
			</div>
			<div class="stat-content">
				<h3 class="text-gradient-warning">￥<span id="count15">-</span></h3>
				<p>今日收益</p>
			</div>
		</div>
	</div>

	<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 dashboard-animate">
		<div class="stat-card" style="cursor: pointer;" onclick="window.location.href='./record.php'">
			<div class="stat-icon gradient-info">
				<i class="fa fa-history"></i>
			</div>
			<div class="stat-content">
				<h3 class="text-gradient-info">￥<span id="count16">-</span></h3>
				<p>昨日收益</p>
			</div>
		</div>
	</div>

	<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 dashboard-animate">
		<div class="stat-card">
			<div class="stat-icon gradient-info">
				<i class="fa fa-eye"></i>
			</div>
			<div class="stat-content">
				<h3 class="text-gradient-info"><span id="visit_today">0</span></h3>
				<p>今日访问量</p>
			</div>
		</div>
	</div>

	<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 dashboard-animate">
		<div class="stat-card">
			<div class="stat-icon gradient-primary">
				<i class="fa fa-users"></i>
			</div>
			<div class="stat-content">
				<h3 class="text-gradient-primary"><span id="ip_today">0</span></h3>
				<p>今日独立IP</p>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-8">
		<div class="neumorphic-card">
			<h3 class="text-center mb-3 text-gradient-primary"><i class="fa fa-bar-chart"></i> 一周交易数据分析</h3>
			<div class="neumorphic-inset mb-3">
				<div id="chart-classic-dash" class="chart-container"></div>
			</div>
			<div class="row text-center" style="border-top:1px solid #f1f5f9; padding-top:14px; margin-top:4px;">
				<div class="col-xs-4">
					<div class="text-gradient-primary" style="font-size:13px; margin-bottom:4px;"><i class="fa fa-qq"></i> QQ钱包</div>
					<strong style="font-size:16px; color:#1e293b;">￥<span id="count12">-</span></strong>
				</div>
				<div class="col-xs-4">
					<div class="text-gradient-success" style="font-size:13px; margin-bottom:4px;"><i class="fa fa-weixin"></i> 微信</div>
					<strong style="font-size:16px; color:#1e293b;">￥<span id="count13">-</span></strong>
				</div>
				<div class="col-xs-4">
					<div class="text-gradient-warning" style="font-size:13px; margin-bottom:4px;"><i class="fa fa-credit-card"></i> 支付宝</div>
					<strong style="font-size:16px; color:#1e293b;">￥<span id="count14">-</span></strong>
				</div>
			</div>
		</div>

		<div class="row" style="display:flex;align-items:stretch;">
			<div class="col-sm-6" style="display:flex;">
				<div class="neumorphic-card" style="width:100%;">
					<h3 class="text-center mb-3 text-gradient-primary"><i class="fa fa-bullhorn"></i> 官方公告</h3>
					<div class="neumorphic-inset">
						<ul class="list-group" style="margin-bottom:0;">
							<li class="list-group-item bg-transparent border-0" id="notice" style="padding:6px 0;"><p style="margin:0;">请勿将本程序用于任何违规场景，共创和谐网络环境！</p></li>
							<li class="list-group-item bg-transparent border-0" style="padding:6px 0;"><p style="margin:0;">推广QQ群：<a href="https://qm.qq.com/q/cxdp9V2Mla" target="_blank" class="text-gradient-primary">283737974</a></p></li>
							<li class="list-group-item bg-transparent border-0" style="padding:6px 0;"><p style="margin:0;">广告位联系：<a href="https://qm.qq.com/q/cxdp9V2Mla" target="_blank" class="text-gradient-primary">283737974</a></p></li>
						</ul>
					</div>
				</div>
			</div>

			<div class="col-sm-6" style="display:flex;">
				<div class="neumorphic-card" style="width:100%;">
					<h3 class="text-center mb-3 text-gradient-primary"><i class="fa fa-handshake-o"></i> 站长推荐</h3>
					<div class="neumorphic-inset" style="padding:8px; overflow:hidden; border-radius:6px; background:#f1f5f9;">
						<div class="recommend-carousel">
							<div class="recommend-track" id="recommendTrack">
								<a href="https://wetcs.cn" target="_blank"><img src="../assets/img/recommend/1.png" alt="站长推荐" loading="lazy" onerror="this.parentNode.style.display='none'"></a>
								<a href="https://wetcs.cn" target="_blank"><img src="../assets/img/recommend/2.png" alt="站长推荐" loading="lazy" onerror="this.parentNode.style.display='none'"></a>
								<a href="https://wetcs.cn" target="_blank"><img src="../assets/img/recommend/3.png" alt="站长推荐" loading="lazy" onerror="this.parentNode.style.display='none'"></a>
								<a href="https://wetcs.cn" target="_blank"><img src="../assets/img/recommend/4.png" alt="站长推荐" loading="lazy" onerror="this.parentNode.style.display='none'"></a>
								<a href="https://wetcs.cn" target="_blank"><img src="../assets/img/recommend/5.png" alt="站长推荐" loading="lazy" onerror="this.parentNode.style.display='none'"></a>
								<a href="https://wetcs.cn" target="_blank"><img src="../assets/img/recommend/6.png" alt="站长推荐" loading="lazy" onerror="this.parentNode.style.display='none'"></a>
								<a href="https://wetcs.cn" target="_blank"><img src="../assets/img/recommend/7.png" alt="站长推荐" loading="lazy" onerror="this.parentNode.style.display='none'"></a>
								<a href="https://wetcs.cn" target="_blank"><img src="../assets/img/recommend/8.png" alt="站长推荐" loading="lazy" onerror="this.parentNode.style.display='none'"></a>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div class="col-sm-4">
			<div class="neumorphic-card">
				<h3 class="text-center mb-3 text-gradient-primary"><i class="fa fa-comments"></i> 工单提醒</h3>
				<div class="neumorphic-inset text-center">
					<i class="fa fa-bell-o text-gradient-primary" style="font-size:22px; margin-bottom:8px; display:block;"></i>
					<p style="margin:0;">待处理工单：<a id="count17" href="workorder.php" class="text-gradient-primary font-bold">0</a> 个</p>
				</div>
			</div>

			<div class="neumorphic-card">
				<h3 class="text-center mb-3 text-gradient-primary"><i class="fa fa-sitemap"></i> 分站统计</h3>
				<div class="row">
					<div class="col-xs-6" style="margin-bottom:12px;">
						<div class="neumorphic-inset text-center">
							<i class="fa fa-users text-gradient-primary" style="font-size:22px; margin-bottom:8px; display:block;"></i>
							<h4 class="text-gradient-primary font-bold" style="margin:0 0 2px 0; font-size:16px;"><span id="count6">-</span></h4>
							<p style="font-size:11px; color:#94a3b8; margin:0;">分站/用户总数</p>
						</div>
					</div>
					<div class="col-xs-6" style="margin-bottom:12px;">
						<div class="neumorphic-inset text-center">
							<i class="fa fa-plus-circle text-gradient-success" style="font-size:22px; margin-bottom:8px; display:block;"></i>
							<h4 class="text-gradient-success font-bold" style="margin:0 0 2px 0; font-size:16px;"><span id="count7">-</span></h4>
							<p style="font-size:11px; color:#94a3b8; margin:0;">今日新开分站</p>
						</div>
					</div>
					<div class="col-xs-6" style="margin-bottom:12px;">
						<div class="neumorphic-inset text-center">
							<i class="fa fa-percent text-gradient-warning" style="font-size:22px; margin-bottom:8px; display:block;"></i>
							<h4 class="text-gradient-warning font-bold" style="margin:0 0 2px 0; font-size:16px;">￥<span id="count8">-</span></h4>
							<p style="font-size:11px; color:#94a3b8; margin:0;">今日分站提成</p>
						</div>
					</div>
					<div class="col-xs-6" style="margin-bottom:12px;">
						<div class="neumorphic-inset text-center">
							<i class="fa fa-exchange text-gradient-danger" style="font-size:22px; margin-bottom:8px; display:block;"></i>
							<h4 style="margin:0 0 2px 0; font-size:16px;"><a id="count11" href="tixian.php" class="text-gradient-danger font-bold">￥<span id="count11_val">-</span></a></h4>
							<p style="font-size:11px; color:#94a3b8; margin:0;">待处理提现</p>
						</div>
					</div>
				</div>
			</div>

			<div class="neumorphic-card">
				<h3 class="text-center mb-3 text-gradient-primary"><i class="fa fa-shield"></i> 安全中心</h3>
				<div class="neumorphic-inset">
					<ul class="list-group" style="margin-bottom:0;">
						<li class="list-group-item bg-transparent border-0" id="updatemsg" style="padding:6px 0;">正在获取中...</li>
						<?php
						foreach ($sec_msg as $row) {
							echo $row;
						}
						if (count($sec_msg) == 0) echo '<li class="list-group-item bg-transparent border-0" style="padding:6px 0;"><span class="label label-success">正常</span> 暂未发现网站安全问题</li>';
						?>
					</ul>
				</div>
			</div>

			<div class="neumorphic-card">
				<h3 class="text-center mb-3 text-gradient-primary"><i class="fa fa-server"></i> 系统信息</h3>
				<div class="neumorphic-inset">
					<ul class="list-group" style="margin-bottom:0;">
						<li class="list-group-item bg-transparent border-0" style="padding:6px 0;"><i class="fa fa-code text-gradient-info"></i> PHP 版本：<b><?php echo phpversion() ?></b></li>
						<li class="list-group-item bg-transparent border-0" style="padding:6px 0;"><i class="fa fa-database text-gradient-success"></i> MySQL 版本：<b><?php echo $mysqlversion ?></b></li>
						<li class="list-group-item bg-transparent border-0" style="padding:6px 0;"><i class="fa fa-globe text-gradient-warning"></i> 服务器软件：<b><?php echo $_SERVER['SERVER_SOFTWARE'] ?></b></li>
						<li class="list-group-item bg-transparent border-0" style="padding:6px 0;"><i class="fa fa-clock-o text-gradient-primary"></i> 服务器时间：<b><?php echo $date ?></b></li>
					</ul>
				</div>
			</div>

			<!-- 目前最新版本 -->
			<div class="neumorphic-card">
				<h3 class="text-center mb-3 text-gradient-primary"><i class="fa fa-code-fork"></i> 目前最新版本</h3>
				<div class="neumorphic-inset">
					<ul class="list-group" style="margin-bottom:0;">
						<li class="list-group-item bg-transparent border-0" style="padding:6px 0;"><i class="fa fa-tag text-gradient-primary"></i> 当前版本：<b><?php echo VERSION; ?></b></li>
						<li class="list-group-item bg-transparent border-0" id="latestVerItem" style="padding:6px 0;"><i class="fa fa-cloud-download text-gradient-success"></i> 最新版本：<span id="latestVer" style="color:#94a3b8;">检测中...</span></li>
						<li class="list-group-item bg-transparent border-0" id="verStatusItem" style="padding:6px 0;display:none;"><i class="fa fa-info-circle"></i> <span id="verStatus"></span></li>
					</ul>
				</div>
			</div>

</div>
</div>

<script>
// 检测最新版本
(function(){
    var curVer = '<?php echo VERSION; ?>';
    var api = '<?php echo UPDATE_API_URL; ?>';
    if (!api) return;
    try {
        var x = new XMLHttpRequest();
        x.open('GET', api + 'check.php?ver=' + encodeURIComponent(curVer), true);
        x.timeout = 8000;
        x.onreadystatechange = function() {
            if (x.readyState !== 4) return;
            var latest = document.getElementById('latestVer');
            var status = document.getElementById('verStatus');
            var statusItem = document.getElementById('verStatusItem');
            if (x.status !== 200) { if (latest) latest.textContent = '检测失败'; return; }
            try {
                var r = JSON.parse(x.responseText);
                if (r.version) {
                    if (latest) {
                        latest.textContent = r.version;
                        latest.style.color = r.version === curVer ? '#22c55e' : '#ef4444';
                        latest.style.fontWeight = '600';
                    }
                    if (status) {
                        if (r.version === curVer) {
                            status.innerHTML = '<span class="label label-success">已是最新版本</span>';
                        } else {
                            status.innerHTML = '<a href="update.php" class="label label-warning" style="color:#fff;text-decoration:none;">发现新版本，点击更新</a>';
                        }
                        if (statusItem) statusItem.style.display = '';
                    }
                }
            } catch(e) { if (latest) latest.textContent = '解析失败'; }
        };
        x.send();
    } catch(e) {}
})();
</script>

<script>
	// 站长推荐 — 平滑左右循环滑动轮播
	(function() {
		var track = document.getElementById('recommendTrack');
		if (!track) return;
		// 移除加载失败的项
		var items = track.querySelectorAll('a');
		items.forEach(function(a) {
			var img = a.querySelector('img');
			img.onerror = function() { a.style.display = 'none'; };
		});
		function startCarousel() {
			// 等图片加载后测量真实宽度
			var imgs = track.querySelectorAll('img');
			var loaded = 0, total = imgs.length;
			if (total === 0) return;
			imgs.forEach(function(im) {
				if (im.complete) check();
				else { im.onload = check; im.onerror = check; }
			});
			function check() {
				loaded++;
				if (loaded < total) return;
				// 克隆一组，形成无缝循环
				var originals = Array.prototype.map.call(track.children, function(c) { return c.cloneNode(true); });
				originals.forEach(function(c) { track.appendChild(c); });
				// 半宽 = 原始内容宽度 + 一组的间隙总和
				var half = 0;
				for (var i = 0; i < originals.length; i++) {
					half += originals[i].offsetWidth;
				}
				half += originals.length * 10; // gap
				track.style.setProperty('--rc-half', '-' + half + 'px');
				track.classList.add('rc-scroll');
			}
		}
		startCarousel();
	})();
</script>
<script>
	$(document).ready(function() {
		
		$('#title').html('正在加载数据中...');
		
		$.ajax({
			type: "POST",
			url: "index.php",
			data:{'SF_Action':'check'},
			dataType: 'json',
			success: function(data) {
			    if(data.code == 0){
                    // code为1代表需要更新，0则不需要
                    if(data.data.code == 1){
                        $("#updatemsg").html('<b><font color="#FF0000">通知:发现新版本 V'+data.data.data.edition+'</font></b> <a href="update.php">点击更新</a>');
                    }else{
                        $("#updatemsg").html('<font color="#DC143C">您当前已是最新版本</font>');
                    }
                }else{
                    $("#updatemsg").html(data.msg);
                }
			},
		    error :function(data){
			    $("#updatemsg").html('链接服务器成功！');
			}
		});
		$.ajax({
			type: "GET",
			url: "ajax.php?act=getcount",
			dataType: 'json',
			async: true,
			success: function(data) {
				$('#title').html('后台管理首页');
				$('#yxts').html(data.yxts);
				$('#count1').html(data.count1);
				$('#count2').html(data.count2);
				$('#count3').html(data.count3);
				$('#count4').html(data.count4);
				$('#count5').html(data.count5);
				$('#count6').html(data.count6);
				$('#count7').html(data.count7);
				$('#count8').html(data.count8);
				$('#count9').html(data.count9);
				$('#count10').html(data.count10);
				$('#count11').attr('href', 'tixian.php');
				$('#count11_val').html(data.count11);
				$('#count12').html(data.count12);
				$('#count13').html(data.count13);
				$('#count14').html(data.count14);
				$('#count15').html(data.count15);
				$('#count16').html(data.count16);
				$('#count17').html(data.count17);
				
				// 显示访问统计数据
				if(data.visit_today !== undefined) $('#visit_today').text(data.visit_today);
				if(data.ip_today !== undefined) $('#ip_today').text(data.ip_today);
				if(data.realtime_visitors !== undefined) $('#realtimeCount').text(data.realtime_visitors);



				// 绘制图表
				var t = $("#chart-classic-dash");
				
				// 图表配置
				var chartOptions = {
					colors: ['#5BC0DE', '#5CB85C'],
					shadowSize: 0,
					legend: {
						show: !0,
						position: "nw",
						backgroundOpacity: 0
					},
					grid: {
						borderWidth: 0,
						hoverable: !0,
						clickable: !0
					},
					yaxis: {
						show: !0,
						ticks: 5,
						tickColor: "#e2e8f0",
						font: { color: "#94a3b8", size: 11 }
					},
					xaxis: {
						ticks: data.chart.date,
						tickFormatter: function(val, axis) {
							return data.chart.date[val];
						}
					}
				};
				
				// 图表数据
				var chartData = [{
					label: "订单量",
					data: data.chart.orders,
					lines: {
						show: !0,
						fill: !0,
						fillColor: {
							colors: [{
								opacity: .6
							}, {
								opacity: .6
							}]
						}
					},
					points: {
						show: !0,
						radius: 5
					}
				}, {
					label: "交易量",
					data: data.chart.money,
					lines: {
						show: !0,
						fill: !0,
						fillColor: {
							colors: [{
								opacity: .6
							}, {
								opacity: .6
							}]
						}
					},
					points: {
						show: !0,
						radius: 5
					}
				}];
				
				// 初始化图表
				var plot = $.plot(t, chartData, chartOptions);
				
				// 添加窗口大小改变时的重绘逻辑
				$(window).resize(function() {
					// 清除之前的图表
					t.empty();
					// 重绘图表，使用新的容器尺寸
					plot = $.plot(t, chartData, chartOptions);
				});
		
		// 如果有访问数据，绘制访问统计图表
		if(data.visit_chart) {
			// 创建访问统计图表区域
			var visitChartHtml = `
			<div class="row">
				<div class="col-sm-12">
					<div class="neumorphic-card">
						<div class="d-flex justify-content-between align-items-center mb-4">
							<h3 class="text-gradient-primary mb-0"><i class="fa fa-area-chart"></i> 一周访问统计</h3>
							<div class="d-flex align-items-center gap-3">
								<span class="badge-pulse" id="realtimeBadge" style="background:#1890ff; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; white-space:nowrap; display:inline-block;">
									<i class="fa fa-circle"></i> 10分钟内 <strong id="realtimeCount">0</strong> 人在线
								</span>
								<button class="btn btn-primary btn-sm" id="viewVisitDetails"><i class="fa fa-eye"></i> 详细查看</button>
							</div>
						</div>
						<div class="neumorphic-inset mb-4">
							<div id="visit-chart" class="chart-container" style="height:280px;"></div>
						</div>
					</div>
				</div>
			</div>

		<div class="row" style="margin-top:16px;">
			<div class="col-sm-6">
				<div class="neumorphic-card" style="margin-bottom:0;">
					<h4 class="text-gradient-primary mb-3"><i class="fa fa-file-text-o"></i> 热门访问页面 TOP10</h4>
					<div id="top-urls-list"></div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="neumorphic-card" style="margin-bottom:0;">
					<h4 class="text-gradient-primary mb-3"><i class="fa fa-user-o"></i> 访客IP排行 TOP10</h4>
					<div id="top-ips-list"></div>
				</div>
			</div>
		</div>`;
			
			// 添加访问详情模态框
			var visitModalHtml = `
			<div class="modal fade" id="visitDetailModal" tabindex="-1" role="dialog" aria-labelledby="visitDetailModalLabel">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title text-gradient-primary" id="visitDetailModalLabel"><i class="fa fa-list-alt"></i> 访问详情</h4>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
								<tr>
									<th>IP地址</th>
									<th>访问URL</th>
									<th>地区</th>
									<th>访问时间</th>
									<th>访问次数</th>
									<th>浏览器信息</th>
								</tr>
							</thead>
									<tbody id="visitDetailTable"></tbody>
								</table>
							</div>
							<div id="visitDetailEmpty" class="text-center py-10" style="display: none;">
								<i class="fa fa-info-circle text-5xl text-gradient-primary mb-4"></i>
								<p class="text-gray-600">暂无访问记录</p>
							</div>
							<div id="visitDetailLoading" class="text-center py-10">
								<i class="fa fa-spinner fa-spin text-3xl text-gradient-primary"></i>
								<p class="mt-2 text-gray-600">正在加载访问记录...</p>
							</div>
							<div class="modal-footer">
								<nav class="pagination-container">
									<ul class="pagination" id="visitDetailPagination"></ul>
								</nav>
							</div>
						</div>
					</div>
				</div>
			</div>`;
			
			// 将模态框添加到页面
			$('body').append(visitModalHtml);
			
			// 插入到原图表之后
			$("#chart-classic-dash").closest(".neumorphic-card").after(visitChartHtml);
			
			// 绘制访问统计图表
				var v = $("#visit-chart");
				
				// 访问统计图表配置
				var visitChartOptions = {
					colors: ['#3b82f6', '#4cc9f0'], // 蓝色主题配色
					shadowSize: 0,
					legend: {
						show: !0,
						position: "nw",
						backgroundOpacity: 0
					},
					grid: {
						borderWidth: 0,
						hoverable: !0,
						clickable: !0
					},
					yaxis: {
						show: !0,
						ticks: 5,
						tickColor: "#e2e8f0",
						font: { color: "#94a3b8", size: 11 }
					},
					xaxis: {
						ticks: data.visit_chart.date,
						tickFormatter: function(val, axis) {
							return data.visit_chart.date[val];
						}
					}
				};
				
				// 访问统计图表数据
				var visitChartData = [{
					label: "访问量",
					data: data.visit_chart.visits,
					lines: {
						show: !0,
						fill: !0,
						fillColor: {
							colors: [{
								opacity: .6
							}, {
								opacity: .6
							}]
						}
					},
					points: {
						show: !0,
						radius: 5
					}
				}, {
					label: "独立IP",
					data: data.visit_chart.ips,
					lines: {
						show: !0,
						fill: !0,
						fillColor: {
							colors: [{
								opacity: .6
							}, {
								opacity: .6
							}]
						}
					},
					points: {
						show: !0,
						radius: 5
					}
				}];
				
				// 初始化访问统计图表
				var visitPlot = $.plot(v, visitChartData, visitChartOptions);
				
				// 添加窗口大小改变时的重绘逻辑
				$(window).resize(function() {
					// 清除之前的图表
					v.empty();
					// 重绘图表，使用新的容器尺寸
					visitPlot = $.plot(v, visitChartData, visitChartOptions);
				});
			
			var visitPreviousPoint = null;
			v.bind("plothover", function(event, pos, item) {
				if (item) {
					if (visitPreviousPoint != item.dataIndex) {
						visitPreviousPoint = item.dataIndex;
						$("#tooltip").remove();
						var x = item.datapoint[0].toFixed(2),
							y = item.datapoint[1].toFixed(2);
						showTooltip(item.pageX, item.pageY,
							item.series.label + "：" + y);
					}
				} else {
					$("#tooltip").remove();
					visitPreviousPoint = null;
				}
			});

			// 实时人数定时刷新（每10秒）
			setInterval(function() {
				$.ajax({
					type: "GET",
					url: "ajax.php?act=getrealtime",
					dataType: 'json',
					success: function(res) {
						if(res.code === 0 && res.realtime_visitors !== undefined) {
							$('#realtimeCount').text(res.realtime_visitors);
						}
					}
				});
			}, 10000);

			// 渲染热门访问页面
			if(data.top_urls && data.top_urls.length > 0) {
				var maxUrl = Math.max.apply(null, data.top_urls.map(function(u){return u.total;}));
				var urlHtml = "";
				data.top_urls.forEach(function(u, i) {
					var pct = Math.round(u.total / maxUrl * 100);
					var colors = ["#3b82f6","#6366f1","#8b5cf6","#a855f7","#d946ef","#ec4899","#f43f5e","#f97316","#f59e0b","#22c55e"];
					var col = colors[i] || "#94a3b8";
					var displayUrl = u.url.length > 50 ? u.url.substring(0,50)+"..." : u.url;
					urlHtml += '<div style="margin-bottom:8px;">'+
					  '<div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:2px;"><span style="color:#475569;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:70%;" title="'+u.url+'">'+(i+1)+'. '+displayUrl+'</span><b style="color:'+col+';">'+u.total+' 次</b></div>'+
					  '<div style="height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;"><div style="height:100%;width:'+pct+'%;background:'+col+';border-radius:3px;transition:width 0.5s;"></div></div>'+
					  '</div>';
				});
				$("#top-urls-list").html(urlHtml);
			} else {
				$("#top-urls-list").html('<p style="color:#94a3b8;text-align:center;padding:20px 0;">暂无访问页面数据</p>');
			}

			// 渲染访客IP排行
			if(data.top_ips && data.top_ips.length > 0) {
				var ipHtml = '<table class="table table-hover" style="margin-bottom:0;font-size:13px;"><thead><tr style="color:#64748b;font-size:12px;"><th style="border-top:none;padding:4px 8px;">排名</th><th style="border-top:none;padding:4px 8px;">IP地址</th><th style="border-top:none;padding:4px 8px;">地区</th><th style="border-top:none;padding:4px 8px;text-align:right;">访问次数</th></tr></thead><tbody>';
				data.top_ips.forEach(function(u, i) {
					var medal = i < 3 ? ["🥇","🥈","🥉"][i] : (i+1);
					ipHtml += '<tr><td style="padding:6px 8px;">'+medal+'</td><td style="padding:6px 8px;font-family:monospace;color:#3b82f6;">'+u.ip+'</td><td style="padding:6px 8px;color:#64748b;font-size:12px;">'+u.region+'</td><td style="padding:6px 8px;text-align:right;"><b>'+u.total+'</b></td></tr>';
				});
				ipHtml += '</tbody></table>';
				$("#top-ips-list").html(ipHtml);
			} else {
				$("#top-ips-list").html('<p style="color:#94a3b8;text-align:center;padding:20px 0;">暂无访客IP数据</p>');
			}

		}

				var previousPoint = null;
				t.bind("plothover", function(event, pos, item) {
					$("#x").text(pos.x.toFixed(2));
					$("#y").text(pos.y.toFixed(2));
					if (item) {
						if (previousPoint != item.dataIndex) {
							previousPoint = item.dataIndex;
							$("#tooltip").remove();
							var x = item.datapoint[0].toFixed(2),
								y = item.datapoint[1].toFixed(2);
							showTooltip(item.pageX, item.pageY,
								item.series.label + "：" + (item.seriesIndex == 1 ? "￥" + y : y) + (item.seriesIndex == 0 ? " 订单" : " 张"));
						}
					} else {
						$("#tooltip").remove();
						previousPoint = null;
					}
				});

				function showTooltip(x, y, contents) {
				$("<div id='tooltip'>" + contents + "</div>").css({
					position: "absolute",
					display: "none",
					top: y - 30,
					left: x + 5,
					border: "1px solid #fdd",
					padding: "2px",
					"background-color": "#fee",
					opacity: 0.80
				}).appendTo("body").fadeIn(200);
			}

			// 访问详情功能
			let currentPage = 1;
			const pageSize = 20;
			
			// 详细查看按钮点击事件
			$(document).on('click', '#viewVisitDetails', function() {
				currentPage = 1;
				loadVisitDetails(currentPage);
				$('#visitDetailModal').modal('show');
			});
			
			// 加载访问详情数据
			function loadVisitDetails(page) {
				$('#visitDetailLoading').show();
				$('#visitDetailTable').empty();
				$('#visitDetailEmpty').hide();
				$('#visitDetailPagination').empty();
				
				$.ajax({
					type: 'GET',
					url: 'ajax.php?act=get_visit_details',
					data: {
						page: page,
						pageSize: pageSize
					},
					dataType: 'json',
					success: function(data) {
						$('#visitDetailLoading').hide();
						
						if (data.code === 0) {
							// 显示访问记录
							if (data.visits && data.visits.length > 0) {
								$.each(data.visits, function(index, visit) {
									const row = `
										<tr>
											<td>${visit.ip}</td>
											<td>${visit.url}</td>
											<td>${visit.region}</td>
											<td>${visit.visit_time}</td>
											<td>${visit.visits}</td>
											<td class="text-break" style="max-width: 200px;">${visit.user_agent}</td>
										</tr>
									`;
									$('#visitDetailTable').append(row);
								});
								
								// 生成分页
								generatePagination(data.total, data.page, data.pageSize);
							} else {
								$('#visitDetailEmpty').show();
							}
						} else if (data.code === 1) {
							// 表不存在
							$('#visitDetailEmpty').show();
							$('#visitDetailEmpty').html(`
								<i class="fa fa-info-circle text-5xl text-gradient-primary mb-4"></i>
								<p class="text-gray-600">${data.msg}</p>
							`);
						} else {
							// 其他错误
							alert('加载失败：' + data.msg);
						}
					},
					error: function() {
						$('#visitDetailLoading').hide();
						alert('网络错误，无法加载访问记录');
					}
				});
			}
			
			// 生成分页控件
			function generatePagination(total, current, pageSize) {
				const totalPages = Math.ceil(total / pageSize);
				const maxVisible = 5; // 最多显示的页码数
				let startPage = Math.max(1, current - Math.floor(maxVisible / 2));
				let endPage = Math.min(totalPages, startPage + maxVisible - 1);
				
				if (endPage - startPage + 1 < maxVisible) {
					startPage = Math.max(1, endPage - maxVisible + 1);
				}
				
				// 首页
				if (startPage > 1) {
					$('#visitDetailPagination').append(`<li><a href="#" data-page="1">首页</a></li>`);
					$('#visitDetailPagination').append(`<li class="disabled"><a href="#">...</a></li>`);
				}
				
				// 页码
				for (let i = startPage; i <= endPage; i++) {
					const activeClass = i === current ? 'active' : '';
					$('#visitDetailPagination').append(`<li class="${activeClass}"><a href="#" data-page="${i}">${i}</a></li>`);
				}
				
				// 末页
				if (endPage < totalPages) {
					$('#visitDetailPagination').append(`<li class="disabled"><a href="#">...</a></li>`);
					$('#visitDetailPagination').append(`<li><a href="#" data-page="${totalPages}">末页</a></li>`);
				}
				
				// 分页点击事件
				$('#visitDetailPagination a[data-page]').click(function(e) {
					e.preventDefault();
					const page = parseInt($(this).data('page'));
					if (page !== currentPage) {
						currentPage = page;
						loadVisitDetails(currentPage);
					}
				});
			}
			
			// 模态框关闭时重置状态
			$('#visitDetailModal').on('hidden.bs.modal', function() {
				currentPage = 1;
			})

		}
		});
	})
</script>