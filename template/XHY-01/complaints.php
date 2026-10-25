<?php
// 黑名单查询页（商家投诉率）—— 彼岸复刻 hsfaka 黑名单模块，数据走 /plugin/Bannedusers/Api/*.php
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商家投诉率</title>
    <link rel="stylesheet" href="/static/others/element-plus/index.css?v=20261024">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <script>/* anti-debug removed */</script>
    <style>
        /* 全局样式优化 */
        body {
            margin: 0;
            background-color: #f5f7fa;
            min-height: 100vh;
            overflow-x: hidden; /* 防止水平滚动条 */
        }

        /* 主容器布局优化 */
        #app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden; /* 防止水平滚动条 */
        }

        /* 内容区域优化 */
        .main-container {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 24px;
            box-sizing: border-box; /* 确保padding计入宽度 */
        }

        /* 页脚样式优化 */
        .risk-footer-container {
            width: 100%;
            background: #f5f7fa;
            border-top: 1px solid #e4e7ed;
            margin-top: auto;
            padding: 20px 0;
            box-sizing: border-box;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            box-sizing: border-box;
            width: 100%;
        }

        .footer-main {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            color: #606266;
            font-size: 14px;
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

        /* 头部导航样式 - 与 index.html 保持一致 */
        .header {
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 40px;  /* 与 index.html 保持一致 */
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .logo-img {
            height: 28px;  /* 与 index.html 保持一致的 logo 大小 */
            width: auto;
            transition: transform 0.3s ease;  /* 添加过渡效果 */
        }

        .logo-container:hover .logo-img {
            transform: scale(1.05);  /* 添加悬停效果 */
        }

        .nav-menu {
            display: flex;
            gap: 32px;  /* 与 index.html 保持一致 */
        }

        .nav-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #606266;
            text-decoration: none;
            font-size: 14px;
            position: relative;
            padding: 4px 0;
            transition: color 0.3s ease;
        }

        .nav-item:hover {
            color: var(--el-color-primary);
        }

        .nav-item.active {
            color: var(--el-color-primary);
            font-weight: 500;
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--el-color-primary);
        }

        /* 响应式处理 */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 16px;
            }

            .nav-left {
                width: 100%;
                justify-content: space-between;
            }

            .nav-menu {
                gap: 24px;
            }
        }

        /* 表格样式优化 */
        .el-table {
            --el-table-border-color: #EBEEF5;
            --el-table-header-bg-color: #F5F7FA;
        }

        /* 确保表格内容垂直居中 */
        .el-table .cell {
            display: flex;
            align-items: center;
        }

        /* 卡片样式 */
        .el-card {
            margin-bottom: 16px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* 标签样式优化 */
        .el-tag {
            max-width: 100%;
            white-space: normal;
            height: auto;
            padding: 4px 8px;
            line-height: 1.4;
        }

        /* 用户信息单元格样式 */
        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .username {
            font-size: 14px;
            color: #303133;
        }

        /* 空状态样式 */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #909399;
        }

        /* 分页容器样式 */
        .pagination-container {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        /* 添加过渡动画效果 */
        .el-table__row {
            transition: all 0.3s ease;
        }

        /* 响应式处理 */
        @media (max-width: 768px) {
            .el-card {
                margin-bottom: 12px;
            }
        }

        /* 警告图标动画样式 */
        .warning-icon-container {
            position: fixed;
            left: 24px;
            bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 247, 237, 0.9);
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #ffd591;
            animation: slideIn 0.5s ease-out, pulse 2s infinite;
            z-index: 99;
        }

        .warning-icon {
            color: #ff9800;
            font-size: 20px;
            animation: rotate 2s infinite;
        }

        .warning-text {
            color: #d48806;
            font-size: 14px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(-15deg);
            }
            75% {
                transform: rotate(15deg);
            }
            100% {
                transform: rotate(0deg);
            }
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

        /* 当菜单展开时旋转倒三角 */
        .el-popover__reference-wrapper:hover .el-icon {
            transform: rotate(180deg);
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

            /* 排序选择器响应式 */
            .card-header {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .el-select {
                width: 100% !important;
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

        /* 导航菜单样式 */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .el-dropdown {
            display: inline-flex;
            align-items: center;
        }

        .el-icon--right {
            margin-left: 4px;
            font-size: 12px;
            transition: transform 0.3s;
        }

        /* 下拉菜单样式 */
        .el-dropdown-menu {
            padding: 4px 0;
        }

        .el-dropdown-menu__item a {
            color: #606266;
            text-decoration: none;
            display: block;
        }

        .el-dropdown-menu__item:hover a {
            color: var(--el-color-primary);
        }

        /* 页脚样式 */
        .risk-footer-container {
            width: 100%;
            padding: 20px 24px;  /* 添加左右内边距 */
            background: #f5f7fa;
            border-top: 1px solid #e4e7ed;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;  /* 与主内容区域保持一致的最大宽度 */
            margin: 0 auto;
            text-align: center;
            width: 100%;
        }

        .footer-main {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #606266;
            font-size: 14px;
        }

        .footer-link {
            color: #606266;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: #409EFF;
        }

        .divider {
            color: #dcdfe6;
        }

        .icp-cert {
            color: #606266;
        }

        /* 移动端适配 */
        @media screen and (max-width: 768px) {
            .risk-footer-container {
                padding: 16px;
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

        <!-- 主要内容 -->
        <div class="main-container">
            <el-card shadow="never">
                <template #header>
                    <div class="card-header">
                        <span>商家投诉率统计</span>
                        <el-select v-model="sortType" style="width: 140px" @change="handleSortChange">
                            <el-option 
                                v-for="option in sortOptions" 
                                :key="option.value" 
                                :label="option.label" 
                                :value="option.value" 
                            />
                        </el-select>
                    </div>
                </template>

                <el-table 
                    :data="users" 
                    border 
                    style="width: 100%" 
                    v-loading="loading"
                    :size="isMobile ? 'small' : 'default'"
                    :cell-style="{ padding: isMobile ? '4px' : '8px' }">
                    <el-table-column label="排名" width="70" align="center">
                        <template #default="scope">
                            <span :style="getRankStyle(scope.$index + 1)">
                                {{ (currentPage - 1) * pageSize + scope.$index + 1 }}
                            </span>
                        </template>
                    </el-table-column>
                    
                    <el-table-column label="商家账户" min-width="200">
                        <template #default="scope">
                            <div class="user-info-cell">
                                <el-avatar 
                                    :size="32" 
                                    :src="scope.row.avatar || '/static/images/avatar.png?v=20261024'"
                                    style="flex-shrink: 0">
                                </el-avatar>
                                <div style="display: flex; flex-direction: column;">
                                    <span>{{ scope.row.username }}</span>
                                    <span style="font-size: 12px; color: #909399;" v-if="scope.row.nickname">
                                        {{ scope.row.nickname }}
                                    </span>
                                </div>
                            </div>
                        </template>
                    </el-table-column>
                    
                    <el-table-column label="投诉率" width="120" align="center">
                        <template #default="scope">
                            <el-tag :type="getComplaintRateType(scope.row.complaint_rate)" effect="light">
                                {{ scope.row.complaint_rate }}%
                            </el-tag>
                        </template>
                    </el-table-column>
                    
                    <el-table-column label="投诉订单" width="100" align="center">
                        <template #default="scope">
                            <span>{{ scope.row.complaint_orders || '0' }}</span>
                        </template>
                    </el-table-column>

                    <el-table-column label="成交订单" width="100" align="center">
                        <template #default="scope">
                            <span>{{ scope.row.total_orders || '0' }}</span>
                        </template>
                    </el-table-column>

                    <el-table-column label="最近投诉时间" min-width="150" align="center">
                        <template #default="scope">
                            <span>{{ formatDate(scope.row.latest_complaint_time) }}</span>
                        </template>
                    </el-table-column>
                </el-table>

                <!-- 分页组件 -->
                <div class="pagination-container" v-if="users.length > 0">
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

            <!-- 在 main-container 内添加警告图标组件 -->
            <div class="warning-icon-container">
                <el-icon class="warning-icon">
                    <WarningFilled />
                </el-icon>
                <span class="warning-text">当前共有 {{ total }} 个投诉率较高的商家</span>
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
                const users = ref([]);
                const currentPage = ref(1);
                const pageSize = ref(10);
                const total = ref(0);
                const loading = ref(false);
                const logo = ref('template/XHY-01/assets/img/logo-full.png');
                const siteName = ref('彩虹云商城');
                const icpNumber = ref('<?php echo addslashes(strip_tags(trim((string)($conf['icp_number'] ?? '')))); ?>');
                const gaNumber = ref('<?php echo addslashes(strip_tags(trim((string)($conf['police_number'] ?? '')))); ?>');
                const icpCert = ref('<?php echo addslashes(strip_tags(trim((string)($conf['icp_license'] ?? '')))); ?>');
                const navItems = ref(JSON.parse('[{"id":1,"name":"网站首页","href":"./","target":0,"sort":0},{"id":3,"name":"禁售目录","href":"./?mod=goods-release","target":0,"sort":0},{"id":4,"name":"最新公告","href":"./?mod=notice","target":0,"sort":0},{"id":5,"name":"公司介绍","href":"./?mod=about","target":1,"sort":0},{"id":6,"name":"演示店铺","href":"./?mod=shop","target":0,"sort":0},{"id":8,"name":"黑名单查询","href":"javascript:;","target":0,"sort":0,"subMenus":[{"id":9,"name":"风控名单","href":"./?mod=riskcontrol","target":0,"sort":2},{"id":10,"name":"商家投诉率","href":"./?mod=complaints","target":0,"sort":1},{"id":11,"name":"买家黑名单","href":"./?mod=bannedrecords","target":0,"sort":0}]}]'));
                const sortType = ref('latest_time');
                const isMobile = ref(window.innerWidth <= 768);
                const templateType = ref('default');  // 添加模板类型响应式变量
                const menuStatus = ref({
                    index: true,
                    complaints: true,
                    bannedRecords: true
                });

                // 添加排序选项数据
                const sortOptions = [
                    { label: '最近投诉时间', value: 'latest_time' },
                    { label: '投诉率', value: 'complaint_rate' },
                    { label: '投诉订单数', value: 'complaint_orders' }
                ];

                console.log('Logo value:', logo.value);

                const getComplaintRateType = (rate) => {
                    if (rate >= 20) return 'danger';
                    if (rate >= 10) return 'warning';
                    if (rate >= 5) return 'info';
                    return 'success';
                };

                const formatDate = (timestamp) => {
                    if (!timestamp) return '-';
                    const date = new Date(timestamp * 1000);
                    return date.toLocaleString('zh-CN', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    }).replace(/\//g, '-');
                };

                const goToMerchant = () => {
                    window.location.href = './user/login.php';
                };

                const handleSortChange = () => {
                    currentPage.value = 1;
                    loadData();
                };

                const loadData = async () => {
                    loading.value = true;
                    try {
                        const response = await axios.get('/plugin/Bannedusers/Api/getComplaintsList.php', {
                            params: {
                                page: currentPage.value,
                                limit: pageSize.value,
                                sort_type: sortType.value
                            }
                        });
                        if (response.data.code === 200) {
                            users.value = response.data.items.map(item => ({
                                ...item,
                                complaint_orders: item.complaint_orders || 0,
                                total_orders: item.total_orders || 0,
                                complaint_rate: item.complaint_rate || 0
                            }));
                            total.value = response.data.total;
                        } else {
                            ElMessage.error(response.data.msg || '获取数据失败');
                        }
                    } catch (error) {
                        console.error('API Error:', error);
                        ElMessage.error('获取商家列表失败');
                    } finally {
                        loading.value = false;
                    }
                };

                const handleSizeChange = (val) => {
                    pageSize.value = val;
                    currentPage.value = 1;
                    loadData();
                };

                const handleCurrentChange = (val) => {
                    currentPage.value = val;
                    loadData();
                };

                const isCurrentPage = (href) => {
                    return window.location.search === href;
                };

                // 添加排名样式方法
                const getRankStyle = (rank) => {
                    if (rank <= 3) {
                        const colors = {
                            1: '#FF4E50',  // 第一名 - 红色
                            2: '#FF8C00',  // 第二名 - 橙色
                            3: '#FFD700'   // 第三名 - 金色
                        };
                        return {
                            color: colors[rank],
                            fontWeight: 'bold',
                            fontSize: '16px'
                        };
                    }
                    return {
                        color: '#606266'  // 默认颜色
                    };
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

                // 获取当前模板类型
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

                // 根据当前模板类型返回对应的路径
                const getMenuPath = (type) => {
                    if (type === 'index') {
                        return templateType.value === 'risk' 
                            ? '?mod=riskcontrol'
                            : '?mod=riskcontrol';
                    }
                    return '?mod=' + ({ complaints: 'complaints', bannedRecords: 'bannedrecords' }[type] || 'riskcontrol');
                };

                // 获取菜单key
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

                // 修改获取菜单状态的方法
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

                onMounted(() => {
                    getTemplateType();
                    loadData();
                    window.addEventListener('resize', handleResize);
                    getMenuStatus();
                });

                onUnmounted(() => {
                    window.removeEventListener('resize', handleResize);
                });

                return {
                    users,
                    currentPage,
                    pageSize,
                    total,
                    loading,
                    logo,
                    siteName,
                    icpNumber,
                    gaNumber,
                    icpCert,
                    handleSizeChange,
                    handleCurrentChange,
                    getComplaintRateType,
                    formatDate,
                    goToMerchant,
                    navItems,
                    isCurrentPage,
                    sortType,
                    sortOptions,
                    handleSortChange,
                    getRankStyle,
                    isActiveParent,
                    isMobile,
                    getMenuPath,
                    menuStatus,
                    getMenuKey,
                    shouldShowSubMenu,
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