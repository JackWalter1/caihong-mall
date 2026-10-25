<?php
// 黑名单查询页（买家黑名单）—— 彼岸复刻 hsfaka 黑名单模块，数据走 /plugin/Bannedusers/Api/*.php
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>买家黑名单</title>
    <link rel="stylesheet" href="/static/others/element-plus/index.css?v=20261024">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <style>
        /* 基础样式 */
        :root {
            --primary-color: #409EFF;
            --success-color: #67C23A;
            --warning-color: #E6A23C;
            --danger-color: #F56C6C;
            --info-color: #909399;
            --text-primary: #303133;
            --text-regular: #606266;
            --text-secondary: #909399;
            --border-color: #EBEEF5;
            --bg-color: #F5F7FA;
            --card-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            --transition-standard: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            margin: 0;
            background-color: var(--bg-color);
            font-family: 'PingFang SC', 'Helvetica Neue', Helvetica, 'Hiragino Sans GB', 'Microsoft YaHei', Arial, sans-serif;
            color: var(--text-regular);
            line-height: 1.5;
        }

        /* 头部导航样式 */
        .header {
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            transition: var(--transition-standard);
        }

        .header:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 48px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: var(--transition-standard);
        }
        
        .logo-container:hover {
            transform: scale(1.05);
        }

        .logo-img {
            height: 32px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .nav-menu {
            display: flex;
            gap: 36px;
        }

        .nav-item {
            color: var(--text-regular);
            text-decoration: none;
            font-size: 15px;
            position: relative;
            padding: 6px 2px;
            transition: var(--transition-standard);
            font-weight: 500;
        }

        .nav-item:hover {
            color: var(--primary-color);
        }

        .nav-item.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
            transform: scaleX(0.8);
            transition: var(--transition-standard);
        }
        
        .nav-item.active:hover::after {
            transform: scaleX(1);
        }

        /* 美化卡片样式 */
        .el-card {
            border-radius: 16px !important;
            overflow: hidden;
            transition: var(--transition-standard);
            border: none !important;
            box-shadow: var(--card-shadow) !important;
        }

        .el-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12) !important;
        }

        .el-card__header {
            border-bottom: 1px solid var(--border-color) !important;
            padding: 18px 24px !important;
            background-color: rgba(64, 158, 255, 0.02);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-filter-container {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .type-filter {
            width: 180px;
            transition: var(--transition-standard);
        }

        .type-filter:focus-within {
            width: 200px;
        }

        .card-header span {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            position: relative;
            padding-left: 16px;
        }

        .card-header span::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 5px;
            height: 22px;
            background: var(--primary-color);
            border-radius: 3px;
            opacity: 0.8;
            box-shadow: 0 2px 8px rgba(64, 158, 255, 0.4);
        }

        /* 美化表格样式 */
        .el-table {
            border-radius: 12px;
            overflow: hidden;
            margin-top: 18px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            border: none !important;
        }

        .el-table th {
            background-color: rgba(64, 158, 255, 0.05) !important;
            color: var(--text-primary);
            font-weight: 600;
            padding: 14px 8px !important;
            transition: var(--transition-standard);
        }

        .el-table th:hover {
            background-color: rgba(64, 158, 255, 0.08) !important;
        }

        .el-table--border, .el-table--group {
            border: 1px solid rgba(235, 238, 245, 0.7) !important;
        }

        .el-table--border th, .el-table--border td {
            border-right: 1px solid rgba(235, 238, 245, 0.7) !important;
        }

        .el-table--border::after, .el-table--group::after {
            width: 0 !important;
        }

        .el-table td {
            padding: 12px 8px !important;
            transition: var(--transition-standard);
        }

        .el-table--striped .el-table__body tr.el-table__row--striped td {
            background-color: rgba(250, 250, 250, 0.6) !important;
        }

        .el-table__body tr:hover > td {
            background-color: rgba(64, 158, 255, 0.06) !important;
            transform: scale(1.01);
        }

        /* 美化标签样式 */
        .el-tag {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: var(--transition-standard);
            letter-spacing: 0.3px;
        }
        
        .el-tag:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .el-tag--dark {
            border: none !important;
        }
        
        .el-tag--info {
            background-color: #f4f4f5 !important;
            color: #606266 !important;
            border-color: #e9e9eb !important;
        }
        
        .el-tag--warning {
            background-color: #fdf6ec !important;
            color: #cf9236 !important;
            border-color: #f5dab1 !important;
        }

        .el-tag--small {
            font-size: 12px;
            height: 24px;
            line-height: 22px;
            padding: 0 8px;
        }

        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pagination-container {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        /* 美化警告图标容器 */
        .warning-icon-container {
            position: fixed;
            left: 24px;
            bottom: 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            background: linear-gradient(135deg, rgba(255, 247, 237, 0.95), rgba(255, 243, 224, 0.95));
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 213, 145, 0.8);
            animation: slideIn 0.6s cubic-bezier(0.2, 0.8, 0.2, 1), pulse 3s infinite;
            z-index: 99;
            backdrop-filter: blur(8px);
            transform-origin: center;
        }

        .warning-icon {
            color: #fa8c16;
            font-size: 28px;
            animation: rotate 3s infinite ease-in-out;
            filter: drop-shadow(0 2px 4px rgba(250, 140, 22, 0.3));
        }

        .warning-text {
            color: #ad6800;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }
        
        .count-highlight {
            color: #f5222d;
            font-weight: 700;
            padding: 0 6px;
            position: relative;
            display: inline-block;
        }
        
        .count-highlight::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background-color: rgba(245, 34, 45, 0.2);
            border-radius: 3px;
            z-index: -1;
        }

        @keyframes slideIn {
            0% { transform: translateX(-100%) scale(0.8); opacity: 0; }
            70% { transform: translateX(10%) scale(1.05); opacity: 1; }
            100% { transform: translateX(0) scale(1); opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); }
            50% { transform: scale(1.03); box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18); }
            100% { transform: scale(1); box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); }
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(-12deg); }
            50% { transform: rotate(0deg); }
            75% { transform: rotate(12deg); }
            100% { transform: rotate(0deg); }
        }
        
        @keyframes highlight {
            0% { background-position: -100% 0; }
            100% { background-position: 200% 0; }
        }

        .nav-popover {
            padding: 0;
            min-width: 120px;
        }

        .nav-submenu {
            display: flex;
            flex-direction: column;
            background: white;
        }

        .nav-subitem {
            padding: 8px 16px;
            color: #606266;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .nav-subitem:hover {
            background-color: #f5f7fa;
            color: var(--el-color-primary);
        }

        .nav-subitem.active {
            color: var(--el-color-primary);
            background-color: #ecf5ff;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .nav-item .el-icon {
            font-size: 12px;
            margin-top: 2px;
            color: #303133;
        }

        /* 美化分页控件 */
        .el-pagination {
            margin-top: 32px;
            padding: 0;
            justify-content: flex-end;
        }

        .pagination-container {
            margin-top: 32px;
            display: flex;
            justify-content: flex-end;
        }
        
        .el-pagination.is-background {
            padding: 0;
            border-radius: 30px;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 6px 10px;
            transition: var(--transition-standard);
        }
        
        .el-pagination.is-background:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .el-pagination button, .el-pagination span:not([class*=suffix]) {
            font-size: 14px;
            min-width: 36px;
            height: 32px;
            line-height: 32px;
        }

        .el-pagination .el-select .el-input {
            margin: 0 8px;
            width: 110px;
        }

        .el-pagination .el-select .el-input__inner {
            padding-right: 28px;
            border-radius: 20px;
            height: 32px;
            transition: var(--transition-standard);
            border-color: #dcdfe6;
        }
        
        .el-pagination .el-select .el-input__inner:hover {
            border-color: var(--primary-color);
        }

        .el-pagination .el-input__inner {
            height: 32px;
        }

        .el-pagination button, .el-pagination span:not([class*=suffix]) {
            height: 32px;
            line-height: 32px;
        }

        .el-pagination .btn-next, .el-pagination .btn-prev {
            background: center center no-repeat;
            background-size: 16px;
            min-width: 36px;
            border-radius: 20px;
            transition: var(--transition-standard);
        }

        .el-pagination.is-background .btn-next, 
        .el-pagination.is-background .btn-prev, 
        .el-pagination.is-background .el-pager li {
            background-color: #fff;
            color: var(--text-regular);
            min-width: 32px;
            height: 32px;
            line-height: 32px;
            border-radius: 20px;
            margin: 0 3px;
            border: 1px solid transparent;
            font-weight: 500;
            transition: var(--transition-standard);
        }

        .el-pagination.is-background .el-pager li:not(.disabled).active {
            background-color: var(--primary-color);
            color: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(64, 158, 255, 0.4);
            transform: scale(1.08);
            font-weight: 600;
        }

        .el-pagination.is-background .btn-next:hover, 
        .el-pagination.is-background .btn-prev:hover, 
        .el-pagination.is-background .el-pager li:hover:not(.active) {
            color: var(--primary-color);
            border-color: var(--primary-color);
            transform: scale(1.05);
        }

        /* 添加响应式样式 */
        @media screen and (max-width: 768px) {
            /* 导航栏响应式 */
            .nav-container {
                flex-direction: column;
                padding: 12px;
            }

            .nav-left {
                width: 100%;
                flex-direction: column;
                gap: 16px;
            }

            .nav-menu {
                width: 100%;
                flex-wrap: wrap;
                justify-content: center;
                gap: 16px;
            }

            /* 主容器响应式 */
            .main-container {
                padding: 12px;
            }

            /* 表格响应式 */
            .el-table {
                width: 100%;
                font-size: 13px;
            }

            /* 分页组件响应式 */
            .pagination-container {
                justify-content: center;
            }

            .el-pagination {
                flex-wrap: wrap;
                justify-content: center;
            }

            /* 警告图标容器响应式 */
            .warning-icon-container {
                left: 12px;
                right: 12px;
                bottom: 12px;
                width: auto;
            }
        }

        /* 针对更小屏幕的优化 */
        @media screen and (max-width: 480px) {
            .nav-menu {
                gap: 12px;
            }

            .el-table {
                font-size: 12px;
            }

            .el-button {
                padding: 8px 12px;
            }

            .warning-icon-container {
                font-size: 12px;
                padding: 8px 12px;
            }
        }

        /* 全局样式优化 */
        body {
            margin: 0;
            background-color: #f5f7fa;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* 主容器布局优化 */
        #app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* 内容区域优化 */
        .main-container {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 24px;
            box-sizing: border-box;
        }

        /* 页脚样式优化 */
        .risk-footer-container {
            width: 100%;
            background: linear-gradient(to bottom, var(--bg-color), #fafbfc);
            border-top: 1px solid rgba(228, 231, 237, 0.7);
            margin-top: auto;
            padding: 30px 0;
            box-sizing: border-box;
            backdrop-filter: blur(8px);
        }

        .footer-content {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 30px;
            box-sizing: border-box;
            width: 100%;
        }

        .footer-main {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 16px;
            color: var(--text-regular);
            font-size: 14px;
        }

        .footer-link {
            color: var(--text-regular);
            text-decoration: none;
            transition: var(--transition-standard);
            position: relative;
            padding: 2px 4px;
        }

        .footer-link:hover {
            color: var(--primary-color);
        }
        
        .footer-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: var(--primary-color);
            transform: scaleX(0);
            transition: var(--transition-standard);
            transform-origin: right;
        }
        
        .footer-link:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        .divider {
            color: #dcdfe6;
        }

        .icp-cert {
            color: var(--text-regular);
        }
        
        .site-name {
            font-weight: 500;
            background: linear-gradient(90deg, var(--primary-color), #79bbff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* 按钮样式优化 */
        .el-button--primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(64, 158, 255, 0.3);
            transition: var(--transition-standard);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .el-button--primary:hover {
            background-color: #66b1ff;
            border-color: #66b1ff;
            box-shadow: 0 6px 18px rgba(64, 158, 255, 0.4);
            transform: translateY(-2px);
        }

        /* 移动端适配优化 */
        @media screen and (max-width: 768px) {
            .main-container {
                padding: 16px;
            }

            .risk-footer-container {
                padding: 16px 0;
            }

            .footer-content {
                padding: 0 16px;
            }
            
            .footer-main {
                flex-direction: column;
                gap: 8px;
            }
            
            .divider {
                display: none;
            }
        }
    </style>
    <script>/* anti-debug removed */</script>
</head>

<body>
    <div id="app">
        <!-- 头部导航 -->
        <div class="header">
            <div class="nav-container">
                <div class="nav-left">
                    <div class="logo-container">
                        <img :src="logo" class="logo-img" alt="Logo">
                    </div>
                    <div class="nav-menu">
                        <template v-for="(item, index) in navItems" :key="index">
                            <!-- 有子菜单的导航项目 -->
                            <template v-if="item.subMenus && item.subMenus.length > 0">
                                <el-popover
                                    placement="bottom"
                                    :width="120"
                                    trigger="hover"
                                    popper-class="nav-popover"
                                >
                                    <template #reference>
                                        <span class="nav-item" :class="{ active: isActiveParent(item) }">
                                            {{ item.name }}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" style="fill: currentColor;">
                                                <path d="M12 15.375l-6.375-6.375h12.75z"/>
                                            </svg>
                                        </span>
                                    </template>
                                    <div class="nav-submenu">
                                        <!-- 子菜单已经按sort排序，直接遍历显示 -->
                                        <template v-for="subItem in item.subMenus" :key="subItem.href">
                                            <a :href="subItem.href"
                                               class="nav-subitem"
                                               :class="{ active: isCurrentPage(subItem.href) }"
                                               v-if="shouldShowSubMenu(subItem.href)">
                                                {{ subItem.name }}
                                            </a>
                                        </template>
                                    </div>
                                </el-popover>
                            </template>
                            <!-- 其他顶级菜单项（无子菜单） -->
                            <template v-else>
                                <a :href="item.href"
                                   class="nav-item"
                                   :class="{ active: isCurrentPage(item.href) }">
                                    {{ item.name }}
                                </a>
                            </template>
                        </template>
                    </div>
                </div>
                <el-button type="primary" @click="goToMerchant">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" style="fill: currentColor; margin-right: 4px;">
                        <path d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM6 8a6 6 0 1 1 12 0A6 6 0 0 1 6 8zm2 10a3 3 0 0 0-3 3 1 1 0 1 1-2 0 5 5 0 0 1 5-5h8a5 5 0 0 1 5 5 1 1 0 1 1-2 0 3 3 0 0 0-3-3H8z"/>
                    </svg>
                    商家中心
                </el-button>
            </div>
        </div>

        <!-- 主要内容区域 -->
        <div class="main-container">
            <el-card shadow="never">
                <template #header>
                    <div class="card-header">
                        <span>买家黑名单</span>
                        <div class="search-filter-container">
                            <el-select v-model="typeFilter" placeholder="类型筛选" class="type-filter" @change="handleSearch">
                                <el-option label="全部类型" value=""></el-option>
                                <el-option label="IP" value="IP"></el-option>
                                <el-option label="指纹JUUID" value="指纹JUUID"></el-option>
                                <el-option label="其他" value="其他"></el-option>
                            </el-select>
                        </div>
                    </div>
                </template>

                <el-table 
                    :data="filteredRecords" 
                    border 
                    style="width: 100%" 
                    v-loading="loading"
                    :size="isMobile ? 'small' : 'default'"
                    :cell-style="{ padding: isMobile ? '8px 6px' : '12px 10px' }"
                    highlight-current-row
                    stripe>
                    <!-- IP地址列 -->
                    <el-table-column label="IP地址" prop="buyer_ip" min-width="150">
                        <template #default="scope">
                            <el-tag type="info" effect="plain">
                                <i class="el-icon-location" style="margin-right: 4px;"></i>
                                {{ scope.row.buyer_ip }}
                            </el-tag>
                        </template>
                    </el-table-column>

                    <!-- 订单号列 -->
                    <el-table-column label="关联订单号" prop="order_no" min-width="200">
                        <template #default="scope">
                            <el-tag type="warning" effect="light">
                                <i class="el-icon-document" style="margin-right: 4px;"></i>
                                {{ scope.row.order_no }}
                            </el-tag>
                        </template>
                    </el-table-column>

                    <!-- 黑名单类型列 -->
                    <el-table-column label="黑名单类型" prop="type" width="120" align="center">
                        <template #default="scope">
                            <el-tag :type="getTypeTagType(scope.row.type)" effect="dark" size="small">
                                <i class="el-icon-warning" style="margin-right: 4px;"></i>
                                {{ scope.row.type || '其他' }}
                            </el-tag>
                        </template>
                    </el-table-column>

                    <!-- 拉黑时间列 -->
                    <el-table-column label="拉黑时间" prop="create_time" width="180" align="center">
                        <template #default="scope">
                            <span style="color: var(--text-regular); background: rgba(64, 158, 255, 0.08); padding: 6px 10px; border-radius: 6px; display: inline-block;">
                                <i class="el-icon-time" style="margin-right: 4px; color: var(--primary-color);"></i>
                                {{ scope.row.create_time }}
                            </span>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页组件 -->
                <div class="pagination-container" v-if="total > 0">
                    <el-pagination
                        v-model:current-page="currentPage"
                        v-model:page-size="pageSize"
                        :page-sizes="[10, 20, 30, 50]"
                        :total="total"
                        layout="total, sizes, prev, pager, next, jumper"
                        @size-change="handleSizeChange"
                        @current-change="handleCurrentChange"
                    />
                </div>
            </el-card>

            <!-- 警告图标 -->
            <div class="warning-icon-container">
                <el-icon class="warning-icon">
                    <WarningFilled />
                </el-icon>
                <span class="warning-text">当前共有 <span class="count-highlight">{{ total }}</span> 条违规记录</span>
            </div>
        </div>

        <!-- 添加页脚 -->
        <footer class="risk-footer-container">
            <div class="footer-content">
                <div class="footer-main">
                    <span class="site-name">Powered by {{ siteName }}</span>
                    <span class="divider" v-if="icpNumber">|</span>
                    <span class="icp-cert" v-if="icpNumber">{{ icpNumber }}</span>
                    <span class="divider" v-if="icpCert">|</span>
                    <a v-if="icpCert" :href="'https://beian.miit.gov.cn'" target="_blank" class="footer-link">增值电信经营许可证(ICP/EDI):{{ icpCert }}</a>
                    <span class="divider" v-if="gaNumber">|</span>
                    <a v-if="gaNumber" :href="'http://www.beian.gov.cn'" target="_blank" class="footer-link">
                        <img src="/assets/template/default/assets/wangan.a20583c8.png" alt="公安备案图标" style="vertical-align: -2px; margin-right: 3px; width: 16px; height: 16px;">
                        {{ gaNumber }}
                    </a>
                </div>
            </div>
        </footer>
    </div>

    <script src="/static/others/vue/vue.global.prod.js?v=20261024"></script>
    <script src="/static/others/element-plus/index.full.min.js?v=20261024"></script>
    <script src="/static/others/axios/axios.min.js?v=20261024"></script>
    <script src="/static/others/element-plus/locale/zh-cn.min.js?v=20261024"></script>

    <script>
        const { createApp, ref, onMounted, onUnmounted } = Vue;
        const { ElMessage } = ElementPlus;

        createApp({
            setup() {
                const records = ref([]);
                const filteredRecords = ref([]);
                const currentPage = ref(1);
                const pageSize = ref(10);
                const total = ref(0);
                const loading = ref(false);
                const logo = ref('template/XHY-01/assets/img/logo-full.png');
                const navItems = ref(JSON.parse('[{"id":1,"name":"网站首页","href":"./","target":0,"sort":0},{"id":3,"name":"禁售目录","href":"./?mod=goods-release","target":0,"sort":0},{"id":4,"name":"最新公告","href":"./?mod=notice","target":0,"sort":0},{"id":5,"name":"公司介绍","href":"./?mod=about","target":1,"sort":0},{"id":6,"name":"演示店铺","href":"./?mod=shop","target":0,"sort":0},{"id":8,"name":"黑名单查询","href":"javascript:;","target":0,"sort":0,"subMenus":[{"id":9,"name":"风控名单","href":"./?mod=riskcontrol","target":0,"sort":2},{"id":10,"name":"商家投诉率","href":"./?mod=complaints","target":0,"sort":1},{"id":11,"name":"买家黑名单","href":"./?mod=bannedrecords","target":0,"sort":0}]}]'));
                const isMobile = ref(window.innerWidth <= 768);
                const templateType = ref('default');
                const menuStatus = ref({
                    index: true,
                    complaints: true,
                    bannedRecords: true
                });
                const siteName = ref('彩虹云商城');
                const icpNumber = ref('<?php echo addslashes(strip_tags(trim((string)($conf['icp_number'] ?? '')))); ?>');
                const gaNumber = ref('<?php echo addslashes(strip_tags(trim((string)($conf['police_number'] ?? '')))); ?>');
                const icpCert = ref('<?php echo addslashes(strip_tags(trim((string)($conf['icp_license'] ?? '')))); ?>');
                const typeFilter = ref('');

                const getTemplateType = async () => {
                    try {
                        const response = await axios.get('/plugin/Bannedusers/Api/getTemplate.php');
                        if (response.data.code === 1) {
                            templateType.value = response.data.type;
                        }
                    } catch (error) {
                        console.error('获取模板类型失败:', error);
                    }
                };

                const getMenuPath = (type) => {
                    if (type === 'index') {
                        return templateType.value === 'risk' 
                            ? '?mod=riskcontrol'
                            : '?mod=riskcontrol';
                    }
                    return '?mod=' + ({ complaints: 'complaints', bannedRecords: 'bannedrecords' }[type] || 'riskcontrol');
                };

                const loadRecords = async () => {
                    loading.value = true;
                    try {
                        console.log('开始加载数据...');
                        const response = await axios.get('/plugin/Bannedusers/Api/getBannedRecords.php', {
                            params: {
                                page: currentPage.value,
                                limit: pageSize.value
                            }
                        });
                        console.log('API响应:', response.data);
                        
                        if (response.data.code === 200) {
                            records.value = response.data.items;
                            applyFilters(); // 应用过滤
                            total.value = response.data.total;
                        } else {
                            ElMessage.error(response.data.msg || '获取数据失败');
                        }
                    } catch (error) {
                        console.error('请求错误:', error);
                        ElMessage.error('获取记录失败: ' + error.message);
                    } finally {
                        loading.value = false;
                    }
                };

                const handleSizeChange = (val) => {
                    pageSize.value = val;
                    currentPage.value = 1;
                    loadRecords();
                };

                const handleCurrentChange = (val) => {
                    currentPage.value = val;
                    loadRecords();
                };

                const goToMerchant = () => {
                    window.location.href = './user/login.php';
                };

                const isCurrentPage = (href) => {
                    return window.location.search === href;
                };

                const getTypeTagType = (type) => {
                    switch (type) {
                        case 'IP':
                            return 'info';
                        case 'JUUID':
                        case '指纹JUUID':
                            return 'warning';
                        case '指纹':
                            return 'danger';
                        default:
                            return 'success';
                    }
                };

                const isActiveParent = (item) => {
                    // 检查是否存在子菜单
                    if (item.subMenus && item.subMenus.length > 0) {
                        // 获取子菜单的href数组
                        const subPaths = item.subMenus.map(sub => sub.href);
                        // 检查当前页面路径是否在子菜单中
                        return subPaths.includes(window.location.search);
                    }
                    
                    // 兼容旧代码，如果是名称为"黑名单查询"的菜单
                    if (item.name === '黑名单查询') {
                        const paths = [
                            '?mod=riskcontrol',
                            '?mod=riskcontrol',
                            '?mod=complaints',
                            '?mod=bannedrecords'
                        ];
                        return paths.includes(window.location.search);
                    }
                    
                    return isCurrentPage(item.href);
                };

                const handleResize = () => {
                    isMobile.value = window.innerWidth <= 768;
                };

                const getMenuKey = (href) => {
                    // 处理插件路径
                    if (href && href.indexOf('mod=') > -1) {
                        const path = href.split('mod=')[1] || '';
                        // 特殊处理 index 和 riskControl
                        if (path === 'riskcontrol') {
                            return 'index';
                        }
                        return path;
                    }
                    
                    // 兼容旧代码的固定路径映射
                    const pathMap = {
                        '?mod=riskcontrol': 'index',
                        '?mod=complaints': 'complaints',
                        '?mod=bannedrecords': 'bannedRecords'
                    };
                    return pathMap[href] || '';
                };

                // 判断子菜单是否应该显示
                const shouldShowSubMenu = (href) => {
                    // 如果是插件特定路径，需要检查menuStatus
                    if (href && href.indexOf('mod=') > -1) {
                        const menuKey = getMenuKey(href);
                        return menuStatus.value[menuKey] === true;
                    }
                    // 非插件路径的子菜单，直接显示
                    return true;
                };

                const getMenuStatus = async () => {
                    try {
                        const response = await axios.get('/plugin/Bannedusers/Api/checkNav.php');
                        if (response.data.code === 1) {
                            menuStatus.value = response.data.status;
                            
                            // 更新导航菜单数据，包含已排序的菜单
                            if (response.data.menus) {
                                navItems.value = response.data.menus;
                            }
                            
                            // 更新父菜单及其子菜单
                            if (response.data.parentMenu) {
                                // 查找是否已经存在该菜单
                                const parentIndex = navItems.value.findIndex(
                                    item => item.id === response.data.parentMenu.id
                                );
                                
                                if (parentIndex !== -1) {
                                    // 如果找到，直接更新子菜单
                                    navItems.value[parentIndex].subMenus = response.data.parentMenu.subMenus;
                                } else {
                                    // 如果没找到，查找名称匹配的菜单
                                    const nameIndex = navItems.value.findIndex(
                                        item => item.name === response.data.parentMenu.name
                                    );
                                    
                                    if (nameIndex !== -1) {
                                        navItems.value[nameIndex].subMenus = response.data.parentMenu.subMenus;
                                    } else {
                                        // 如果仍然找不到，则寻找没有 href 的菜单项(通常是 javascript:;)
                                        for (let i = 0; i < navItems.value.length; i++) {
                                            if (navItems.value[i].href === 'javascript:;' || !navItems.value[i].href) {
                                                navItems.value[i].subMenus = response.data.parentMenu.subMenus;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (error) {
                        console.error('获取菜单状态失败:', error);
                    }
                };

                // 处理搜索和过滤
                const handleSearch = () => {
                    applyFilters();
                };
                
                // 应用过滤条件
                const applyFilters = () => {
                    if (!typeFilter.value) {
                        // 如果没有类型过滤，直接显示所有记录
                        filteredRecords.value = records.value;
                        return;
                    }
                    
                    // 应用过滤
                    filteredRecords.value = records.value.filter(item => {
                        let matchesType = true;
                        
                        // 类型过滤
                        if (typeFilter.value) {
                            matchesType = item.type === typeFilter.value;
                        }
                        
                        return matchesType;
                    });
                };

                onMounted(() => {
                    getTemplateType();
                    loadRecords();
                    window.addEventListener('resize', handleResize);
                    getMenuStatus();
                    // 初始化过滤后的记录
                    filteredRecords.value = records.value;
                });

                onUnmounted(() => {
                    window.removeEventListener('resize', handleResize);
                });

                return {
                    records,
                    filteredRecords,
                    currentPage,
                    pageSize,
                    total,
                    loading,
                    logo,
                    navItems,
                    handleSizeChange,
                    handleCurrentChange,
                    goToMerchant,
                    isCurrentPage,
                    isActiveParent,
                    isMobile,
                    getMenuPath,
                    templateType,
                    menuStatus,
                    getMenuKey,
                    shouldShowSubMenu,
                    siteName,
                    icpNumber,
                    gaNumber,
                    icpCert,
                    getTypeTagType,
                    typeFilter,
                    handleSearch,
                };
            }
        }).use(ElementPlus, {
            locale: ElementPlusLocaleZhCn
        }).mount("#app");
    </script>
<script>
function sendHeight() {
  const height = document.body.scrollHeight;
  window.parent.postMessage({ type: 'setIframeHeight', height: height }, '*');
}
window.addEventListener('load', function () {
  sendHeight();
});
window.addEventListener('resize', function () {
  sendHeight();
});
const observer = new MutationObserver(function () {
  sendHeight();
});
observer.observe(document.body, { attributes: true, childList: true, subtree: true });          
</script>
</body>
</html> 