<?php
// 黑名单查询页（风控公示）—— 彼岸复刻 hsfaka 黑名单模块，数据走 /plugin/Bannedusers/Api/*.php
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="max-age=31536000">
    <title>风控公示</title>
    <link rel="stylesheet" href="/static/others/element-plus/index.css?v=20261024">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <script src="./assets/Agod/jquery.min.js"></script>
    <script>/* anti-debug removed */</script>
    <style>
        /* 加载动画样式优化 */
        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.9);
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.3s ease-out;
        }

        #loading.fade-out {
            opacity: 0;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 0, 0, 0.1);
            border-top-color: #409EFF;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* 确保内容在加载时隐藏 */
        #app {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease-in;
        }

        #app.loaded {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>
    <!-- 加载动画容器 -->
    <div id="loading">
        <div class="spinner"></div>
    </div>

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
            <!-- 风控头部 -->
            <div class="risk-header">
                <div class="header-content">
                    <div class="header-left">
                        <h1 class="title">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                                <!-- 盾牌主体 -->
                                <path d="M12 2L4 6V12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12V6L12 2Z" 
                                      fill="#409EFF" fill-opacity="0.1"/>
                                <path d="M12 2L4 6V12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12V6L12 2Z" 
                                      stroke="#409EFF" stroke-width="2" stroke-linejoin="round"/>
                                <!-- 内部对勾 -->
                                <path d="M9 12L11 14L15 10" 
                                      stroke="#409EFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            风控公示
                            
                        </h1>
                        <p class="notice-content" v-html="noticeContent"></p>
                        <div class="sync-status" id="syncStatus">
                            <span class="sync-dot"></span>
                            <span id="syncText">风控数据实时同步中…</span>
                        </div>
                    </div>
                    <div class="header-right">
                        <div class="shield-container">
                            <!-- 装饰性盾牌 -->
                            <svg width="80" height="80" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="decorative-shield">
                                <!-- 外圈圆点装饰 -->
                                <circle cx="16" cy="4" r="1.5" fill="#409EFF" class="shield-dot"/>
                                <circle cx="28" cy="16" r="1.5" fill="#409EFF" class="shield-dot"/>
                                <circle cx="16" cy="28" r="1.5" fill="#409EFF" class="shield-dot"/>
                                <circle cx="4" cy="16" r="1.5" fill="#409EFF" class="shield-dot"/>
                                
                                <!-- 主体盾牌 -->
                                <path d="M16 6L10 9V14C10 17.3137 12.6863 20 16 20C19.3137 20 22 17.3137 22 14V9L16 6Z" 
                                      fill="#E6F0FF"/>
                                <path d="M16 6L10 9V14C10 17.3137 12.6863 20 16 20C19.3137 20 22 17.3137 22 14V9L16 6Z" 
                                      stroke="#409EFF" stroke-width="1.5"/>
                                
                                <!-- 内部对勾 -->
                                <path d="M13.5 14L15 15.5L18.5 12" 
                                      stroke="#409EFF" stroke-width="1.5" 
                                      stroke-linecap="round" 
                                      stroke-linejoin="round"/>
                                
                                <!-- 连接线 -->
                                <line x1="5.5" y1="16" x2="9" y2="16" stroke="#409EFF" stroke-width="1" opacity="0.5"/>
                                <line x1="23" y1="16" x2="26.5" y2="16" stroke="#409EFF" stroke-width="1" opacity="0.5"/>
                                <line x1="16" y1="5.5" x2="16" y2="9" stroke="#409EFF" stroke-width="1" opacity="0.5"/>
                                <line x1="16" y1="21" x2="16" y2="26.5" stroke="#409EFF" stroke-width="1" opacity="0.5"/>
                            </svg>

                            <!-- 主要图标 -->
                            <svg width="240" height="180" viewBox="0 0 240 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="header-image">
                                <!-- 背景图形 -->
                                <circle cx="120" cy="90" r="80" fill="#f0f7ff" opacity="0.5"/>
                                <circle cx="120" cy="90" r="60" fill="#e6f0ff" opacity="0.3"/>
                                
                                <!-- 盾牌外框 -->
                                <path d="M120 30L170 50V90C170 116.5 148 140 120 150C92 140 70 116.5 70 90V50L120 30Z" 
                                      fill="#409EFF" opacity="0.1"/>
                                <path d="M120 30L170 50V90C170 116.5 148 140 120 150C92 140 70 116.5 70 90V50L120 30Z" 
                                      stroke="#409EFF" stroke-width="3" stroke-linejoin="round"/>
                                
                                <!-- 盾牌内部图案 -->
                                <path d="M110 85L120 95L135 80" 
                                      stroke="#409EFF" stroke-width="3" 
                                      stroke-linecap="round" stroke-linejoin="round"/>
                                
                                <!-- 装饰性连接线和圆点 -->
                                <circle cx="50" cy="60" r="3" fill="#409EFF"/>
                                <circle cx="190" cy="60" r="3" fill="#409EFF"/>
                                <circle cx="40" cy="100" r="3" fill="#409EFF"/>
                                <circle cx="200" cy="100" r="3" fill="#409EFF"/>
                                <line x1="53" y1="60" x2="70" y2="60" stroke="#409EFF" stroke-width="2"/>
                                <line x1="170" y1="60" x2="187" y2="60" stroke="#409EFF" stroke-width="2"/>
                                <line x1="43" y1="100" x2="70" y2="100" stroke="#409EFF" stroke-width="2"/>
                                <line x1="170" y1="100" x2="197" y2="100" stroke="#409EFF" stroke-width="2"/>
                                
                                <!-- 装饰性小圆点 -->
                                <circle cx="85" cy="40" r="2" fill="#409EFF"/>
                                <circle cx="155" cy="40" r="2" fill="#409EFF"/>
                                <circle cx="85" cy="140" r="2" fill="#409EFF"/>
                                <circle cx="155" cy="140" r="2" fill="#409EFF"/>
                                
                                <!-- 波浪动效 -->
                                <path d="M60 120C80 110 100 130 120 120C140 110 160 130 180 120" 
                                      stroke="#409EFF" stroke-width="2" opacity="0.5"
                                      stroke-linecap="round">
                                    <animate attributeName="d" 
                                            dur="3s" 
                                            repeatCount="indefinite" 
                                            values="M60 120C80 110 100 130 120 120C140 110 160 130 180 120;
                                                    M60 115C80 125 100 115 120 125C140 115 160 125 180 115;
                                                    M60 120C80 110 100 130 120 120C140 110 160 130 180 120"/>
                                </path>
                                <path d="M60 130C80 120 100 140 120 130C140 120 160 140 180 130" 
                                      stroke="#409EFF" stroke-width="2" opacity="0.3"
                                      stroke-linecap="round">
                                    <animate attributeName="d" 
                                            dur="3s" 
                                            repeatCount="indefinite" 
                                            values="M60 130C80 120 100 140 120 130C140 120 160 140 180 130;
                                                    M60 125C80 135 100 125 120 135C140 125 160 135 180 125;
                                                    M60 130C80 120 100 140 120 130C140 120 160 140 180 130"/>
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="stats-section">
                    <div class="stats-grid">
                        <!-- 今日风控 -->
                        <div class="stat-item">
                            <div class="stat-content">
                                <div class="stat-icon today">
                                    <svg class="rotate-icon" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 2L19.7942 7V17L12 22L4.20577 17V7L12 2Z" fill="#E6F4FF"/>
                                        <path d="M12 8V16M8 12H16" stroke="#1677FF" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">
                                        <span class="number">{{ stats.today.value }}</span>
                                        <span class="unit">件</span>
                                    </div>
                                    <div class="stat-trend" :class="{'trend-up': stats.today.growth >= 0, 'trend-down': stats.today.growth < 0}">
                                        {{ stats.today.growth >= 0 ? '+' : '' }}{{ stats.today.growth }}% 较昨日
                                    </div>
                                    <div class="stat-label">今日风控</div>
                                </div>
                            </div>
                        </div>

                        <!-- 本周风控 -->
                        <div class="stat-item">
                            <div class="stat-content">
                                <div class="stat-icon week">
                                    <svg class="rotate-icon" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" fill="#E6F4FF"/>
                                        <path d="M12 6V12L16 16" stroke="#1677FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">
                                        <span class="number">{{ stats.week.value }}</span>
                                        <span class="unit">件</span>
                                    </div>
                                    <div class="stat-trend" :class="{'trend-up': stats.week.growth >= 0, 'trend-down': stats.week.growth < 0}">
                                        {{ stats.week.growth >= 0 ? '+' : '' }}{{ stats.week.growth }}% 较上周
                                    </div>
                                    <div class="stat-label">本周风控</div>
                                </div>
                            </div>
                        </div>

                        <!-- 本月风控 -->
                        <div class="stat-item">
                            <div class="stat-content">
                                <div class="stat-icon month">
                                    <svg class="rotate-icon" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3" fill="#E6F4FF"/>
                                        <path d="M12 7L12 17M7 12L17 12" stroke="#1677FF" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">
                                        <span class="number">{{ stats.month.value }}</span>
                                        <span class="unit">件</span>
                                    </div>
                                    <div class="stat-trend" :class="{'trend-up': stats.month.growth >= 0, 'trend-down': stats.month.growth < 0}">
                                        {{ stats.month.growth >= 0 ? '+' : '' }}{{ stats.month.growth }}% 较上月
                                    </div>
                                    <div class="stat-label">本月风控</div>
                                </div>
                            </div>
                        </div>

                        <!-- 累计风控 -->
                        <div class="stat-item">
                            <div class="stat-content">
                                <div class="stat-icon total">
                                    <svg class="rotate-icon" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3V7M12 17V21M21 12H17M7 12H3M18.364 5.636L15.536 8.464M8.464 15.536L5.636 18.364M18.364 18.364L15.536 15.536M8.464 8.464L5.636 5.636" stroke="#1677FF" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">
                                        <span class="number">{{ stats.total.value }}</span>
                                        <span class="unit">件</span>
                                    </div>
                                    <div class="stat-trend" :class="{'trend-up': stats.total.growth >= 0, 'trend-down': stats.total.growth < 0}">
                                        {{ stats.total.growth >= 0 ? '+' : '' }}{{ stats.total.growth }}% 较上月
                                    </div>
                                    <div class="stat-label">累计风控</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 风控列表标题 -->
            <div class="section-header">
                <h2 class="section-title">
                    <span class="title-content">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L4 6V12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12V6L12 2Z" 
                                  fill="#409EFF" fill-opacity="0.1" stroke="#409EFF" stroke-width="2"/>
                            <path d="M9 12L11 14L15 10" stroke="#409EFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>风控记录</span>
                    </span>
                </h2>
                <div class="section-actions">
                    <el-radio-group v-model="timeRange" size="small">
                        <el-radio-button label="today">今日</el-radio-button>
                        <el-radio-button label="week">本周</el-radio-button>
                        <el-radio-button label="month">本月</el-radio-button>
                        <el-radio-button label="all">全部</el-radio-button>
                    </el-radio-group>
                </div>
            </div>

            <!-- 风控用户列表 -->
            <div class="risk-list">
                <el-card v-for="user in users" :key="user.id" class="risk-card" shadow="hover">
                    <div class="user-info-header">
                        <div class="user-info-left">
                            <el-avatar 
                                :size="48" 
                                :src="user.avatar || '/static/images/avatar.png?v=20261024'"
                                class="user-avatar">
                                <el-icon><el-icon-user /></el-icon>
                            </el-avatar>
                            <div class="merchant-info">
                                <div class="merchant-title">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 4px;">
                                        <circle cx="8" cy="5" r="3" fill="#409EFF"/>
                                        <path d="M2 14C2 11.2386 4.23858 9 7 9H9C11.7614 9 14 11.2386 14 14" stroke="#409EFF" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    商户 {{ maskString(user.username) }}
                                    <div class="risk-tag-container">
                                        <el-tag
                                            v-if="user.risk_level"
                                            size="small"
                                            :style="{ backgroundColor: user.risk_level.color, color: '#fff', borderColor: user.risk_level.color }"
                                            effect="plain"
                                            class="risk-tag">
                                            {{ user.risk_level.name }}
                                        </el-tag>
                                    </div>
                                </div>
                                <div class="violation-content">
                                    <span class="label">违规内容：</span>
                                    <span class="content">{{ user.risk_content || '系统检测存在违规风险' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="status-tags">
                            <el-tag
                                :type="user.risk_type === 1 ? 'warning' : 'danger'"
                                effect="dark"
                                class="status-tag">
                                {{ user.risk_type === 1 ? '关闭交易' : '店铺封禁' }}
                            </el-tag>
                        </div>
                    </div>
                    <div class="risk-footer">
                        <div class="risk-info">
                            <div class="punishment-basis" v-if="user.risk_level && user.risk_level.punishment">
                                <el-icon><Warning /></el-icon>
                                处罚依据：{{ user.risk_level.punishment }}
                            </div>
                            <div class="duration-info" v-if="user.risk_level && user.risk_level.duration">
                                <el-icon><Timer /></el-icon>
                                封禁时长：{{ user.risk_level.duration }}
                            </div>
                            <div class="risk-time">
                                <el-icon><Timer /></el-icon>
                                风控时间：{{ formatDate(user.banned_time) }}
                            </div>
                        </div>
                    </div>
                </el-card>
            </div>

            <!-- 分页 -->
            <div class="pagination-container">
                <el-pagination
                    v-model:current-page="currentPage"
                    v-model:page-size="pageSize"
                    :total="total"
                    :page-sizes="[10, 20, 50, 100]"
                    layout="total, sizes, prev, pager, next, jumper"
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                />
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
        const { createApp, ref, watch, onMounted } = Vue;
        
        createApp({
            setup() {
                const users = ref([]);
                const currentPage = ref(1);
                const pageSize = ref(10);
                const total = ref(0);
                const timeRange = ref('all');
                const loading = ref(false);
                const logo = ref('template/XHY-01/assets/img/logo-full.png');
                const siteName = ref('彩虹云商城');
                const icpNumber = ref('<?php echo addslashes(strip_tags(trim((string)($conf['icp_number'] ?? '')))); ?>');
                const gaNumber = ref('<?php echo addslashes(strip_tags(trim((string)($conf['police_number'] ?? '')))); ?>');
                const icpCert = ref('<?php echo addslashes(strip_tags(trim((string)($conf['icp_license'] ?? '')))); ?>');
                const stats = ref({
                    total: { value: 0, growth: 0, trend: 'up' },
                    month: { value: 0, growth: 0, trend: 'up' },
                    week: { value: 0, growth: 0, trend: 'up' },
                    today: { value: 0, growth: 0, trend: 'up' }
                });
                const noticeContent = ref('');
                const templateType = ref('default');
                const riskLevels = ref([]);
                const menuStatus = ref({
                    index: true,
                    complaints: true,
                    bannedRecords: true
                });
                
                const formatDate = (timestamp) => {
                    if (!timestamp) return '未知';
                    if (typeof timestamp === 'string' && timestamp.includes('-')) {
                        return timestamp;
                    }
                    const date = new Date(timestamp * 1000);
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    const seconds = String(date.getSeconds()).padStart(2, '0');
                    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                };

                const loadUsers = async () => {
                    loading.value = true;
                    try {
                        const response = await axios.get('/plugin/Bannedusers/Api/getRiskUsers.php', {
                            params: {
                                page: currentPage.value,
                                limit: pageSize.value,
                                timeRange: timeRange.value
                            }
                        });
                        if (response.data.code === 200) {
                            console.log('获取到的用户数据:', response.data.items);
                            users.value = response.data.items;
                            total.value = response.data.total;
                        } else {
                            ElementPlus.ElMessage.error(response.data.msg || '获取数据失败');
                        }
                    } catch (error) {
                        console.error('获取风控用户列表失败:', error);
                        ElementPlus.ElMessage.error('获取数据失败');
                    } finally {
                        loading.value = false;
                    }
                };
                
                const goToMerchant = () => {
                    window.location.href = './user/login.php';
                };
                
                const maskString = (str) => {
                    if (!str) return '';
                    str = String(str);
                    const len = str.length;
                    if (len <= 2) return str;
                    const showLen = Math.floor(len / 3);
                    const stars = '*'.repeat(len - showLen * 2);
                    return str.substring(0, showLen) + stars + str.substring(len - showLen);
                };

                const navItems = ref(JSON.parse('[{"id":1,"name":"网站首页","href":"./","target":0,"sort":0},{"id":3,"name":"禁售目录","href":"./?mod=goods-release","target":0,"sort":0},{"id":4,"name":"最新公告","href":"./?mod=notice","target":0,"sort":0},{"id":5,"name":"公司介绍","href":"./?mod=about","target":1,"sort":0},{"id":6,"name":"演示店铺","href":"./?mod=shop","target":0,"sort":0},{"id":8,"name":"黑名单查询","href":"javascript:;","target":0,"sort":0,"subMenus":[{"id":9,"name":"风控名单","href":"./?mod=riskcontrol","target":0,"sort":2},{"id":10,"name":"商家投诉率","href":"./?mod=complaints","target":0,"sort":1},{"id":11,"name":"买家黑名单","href":"./?mod=bannedrecords","target":0,"sort":0}]}]'));
                
                const isCurrentPage = (href) => {
                    return window.location.search === href;
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

                const loadStats = async () => {
                    try {
                        const response = await axios.get('/plugin/Bannedusers/Api/getRiskStats.php');
                        if (response.data.code === 200) {
                            stats.value = response.data.data;
                            // 统计数字滚动（与主站 DATA PROOF 同款效果）
                            setTimeout(animateStatNumbers, 80);
                            // 实时同步状态展示
                            updateSyncStatus(response.data.synced_at);
                        } else {
                            ElementPlus.ElMessage.error(response.data.msg || '获取统计数据失败');
                        }
                    } catch (error) {
                        console.error('获取统计数据失败:', error);
                        ElementPlus.ElMessage.error('获取统计数据失败');
                    }
                };

                // 实时同步状态文案：读取后端 synced_at 时间戳
                const updateSyncStatus = (syncedAt) => {
                    const el = document.getElementById('syncText');
                    if (!el) return;
                    if (!syncedAt) { el.textContent = '风控数据实时同步中…'; return; }
                    const diff = Math.max(0, Math.floor(Date.now() / 1000) - syncedAt);
                    const m = Math.floor(diff / 60);
                    el.textContent = m < 1
                        ? '数据源实时同步 · 刚刚更新'
                        : '数据源实时同步 · 最后更新 ' + m + ' 分钟前';
                };

                // 统计数字从 0 滚动到目标值（1200ms ease-out，千分位格式化）
                const animateStatNumbers = () => {
                    document.querySelectorAll('.stat-value .number').forEach(el => {
                        const target = parseInt(el.textContent, 10) || 0;
                        if (!target || parseInt(el.dataset.v || '', 10) === target) {
                            el.dataset.v = target;
                            return;
                        }
                        el.dataset.v = target;
                        const start = performance.now(), dur = 1200;
                        const step = (now) => {
                            const p = Math.min(1, (now - start) / dur);
                            const eased = 1 - Math.pow(1 - p, 3);
                            el.textContent = Math.floor(target * eased).toLocaleString('zh-CN');
                            if (p < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                    });
                };

                const getNoticeContent = async () => {
                    try {
                        const response = await axios.get('/plugin/Bannedusers/Api/getNotice.php');
                        if (response.data.code === 1) {
                            noticeContent.value = response.data.content;
                        }
                    } catch (error) {
                        console.error('获取风控公示内容失败:', error);
                    }
                };

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

                watch(timeRange, () => {
                    currentPage.value = 1;
                    loadUsers();
                });

                const handleSizeChange = (val) => {
                    pageSize.value = val;
                    loadUsers();
                };

                const handleCurrentChange = (val) => {
                    currentPage.value = val;
                    loadUsers();
                };

                const getRiskLevels = async () => {
                    try {
                        const response = await axios.get('/plugin/Bannedusers/Api/getRiskLevels.php');
                        if (response.data.code === 1) {
                            riskLevels.value = response.data.levels;
                        }
                    } catch (error) {
                        console.error('获取风控等级配置失败:', error);
                    }
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

                onMounted(() => {
                    loadUsers();
                    loadStats();
                    getNoticeContent();
                    getTemplateType();
                    getRiskLevels();
                    getMenuStatus();
                });

                return {
                    users,
                    logo,
                    siteName,
                    icpNumber,
                    gaNumber,
                    icpCert,
                    goToMerchant,
                    currentPage,
                    pageSize,
                    total,
                    timeRange,
                    loading,
                    formatDate,
                    maskString,
                    navItems,
                    isCurrentPage,
                    isActiveParent,
                    handleSizeChange,
                    handleCurrentChange,
                    stats,
                    loadStats,
                    noticeContent,
                    getMenuPath,
                    riskLevels,
                    getRiskLevels,
                    menuStatus,
                    getMenuKey,
                    shouldShowSubMenu,
                };
            }
        })
        .use(ElementPlus, {
            locale: ElementPlusLocaleZhCn,
        })
        .mount("#app");

        // 在页面加载完成后平滑过渡
        window.addEventListener('load', function() {
            const loading = document.getElementById('loading');
            const app = document.getElementById('app');
            
            // 确保资源加载完成
            setTimeout(() => {
                // 添加淡出类
                loading.classList.add('fade-out');
                
                // 显示应用内容
                app.classList.add('loaded');
                
                // 等待过渡完成后移除加载动画
                setTimeout(() => {
                    loading.style.display = 'none';
                }, 300);
            }, 100);
        });

        // 在Vue应用初始化之前
        document.addEventListener('DOMContentLoaded', function() {
            // 预加载关键资源
            const preloadLinks = [
                '/static/others/element-plus/index.css?v=20261024',
                // 添加其他需要预加载的资源
            ];
            
            preloadLinks.forEach(link => {
                const preload = document.createElement('link');
                preload.rel = 'preload';
                preload.as = link.endsWith('.css') ? 'style' : 'script';
                preload.href = link;
                document.head.appendChild(preload);
            });
        });
    </script>

    <style>
        body {
            margin: 0;
            background-color: #f5f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .risk-header {
            background: linear-gradient(135deg, #ffffff 0%, #f6f9ff 100%);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 32px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .header-content {
            display: flex;
            gap: 40px;
            align-items: center;
            margin-bottom: 40px;
        }

        .header-left {
            flex: 1;
        }

        .header-right {
            flex-shrink: 0;
        }

        .header-image {
            width: 200px;
            height: auto;
        }

        .title {
            font-size: 32px;
            font-weight: 600;
            color: #303133;
            margin: 0 0 20px;
        }

        .description {
            font-size: 15px;
            line-height: 1.8;
            color: #606266;
            margin: 0;
            max-width: 600px;
        }

        .description a {
            color: #409EFF;
            text-decoration: none;
        }

        .description a:hover {
            text-decoration: underline;
        }

        .stats-section {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 20px;
        }

        .stat-item {
            background: white;
            border-radius: 12px;
            padding: 12px;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            height: 90px;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            border-color: #e5e7eb;
        }

        .stat-content {
            display: flex;
            align-items: flex-start;
            height: 100%;
        }

        .stat-icon {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #F0F7FF;
            transition: all 0.3s ease;
            margin-right: 12px;
            order: -1;
        }

        .stat-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stat-value {
            display: flex;
            align-items: baseline;
            gap: 4px;
            margin-bottom: 2px;
        }

        .stat-value .number {
            font-size: 22px;
            font-weight: 600;
            color: #1f2937;
            line-height: 1;
        }

        .stat-value .unit {
            font-size: 12px;
            color: #6b7280;
            margin-left: 2px;
        }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 12px;
            gap: 4px;
            margin-bottom: 2px;
        }

        .trend-up {
            color: #10B981;
            background-color: #ECFDF5;
        }

        .trend-down {
            color: #EF4444;
            background-color: #FEF2F2;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #303133;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .section-title .el-icon {
            font-size: 24px;
            color: var(--el-color-primary);
        }

        .risk-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .risk-card {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .risk-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .user-info-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .user-info-left {
            display: flex;
            gap: 16px;
        }

        .user-avatar {
            border: 2px solid #f0f2f5;
        }

        .merchant-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .merchant-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            color: #303133;
            font-weight: 500;
        }

        .risk-tag {
            margin-left: 8px;
        }

        .merchant-title .el-icon {
            color: var(--el-color-primary);
            font-size: 16px;
        }

        .violation-content {
            font-size: 14px;
            color: #606266;
        }

        .violation-content .label {
            color: #909399;
        }

        .risk-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f0f2f5;
        }

        .risk-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .punishment-basis {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #666;
            font-size: 13px;
        }

        .punishment-basis .el-icon {
            color: #E6A23C;
        }

        .duration-info {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #409EFF;
            font-size: 13px;
        }

        .duration-info .el-icon {
            color: #409EFF;
        }

        .duration-text {
            font-size: 12px;
            color: #909399;
            margin-left: 4px;
        }

        .risk-time {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #909399;
            font-size: 13px;
            justify-content: flex-end;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 32px;
        }

        @media screen and (max-width: 768px) {
            .risk-header {
                padding: 24px;
            }

            .header-content {
                flex-direction: column;
            }

            .header-image {
                width: 150px;
                margin: 0 auto;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }

            .risk-list {
                grid-template-columns: 1fr;
            }

            .user-info-header {
                flex-direction: column;
                gap: 12px;
            }

            .status-tags {
                align-self: flex-start;
            }
        }

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
            gap: 40px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .logo-img {
            height: 28px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .logo-container:hover .logo-img {
            transform: scale(1.05);
        }

        .nav-menu {
            display: flex;
            gap: 32px;
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
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            color: #606266;
            text-decoration: none;
            font-size: 14px;
            position: relative;
            padding: 4px 0;
            transition: color 0.3s ease;
        }

        .nav-item .el-icon {
            color: #303133;
            font-size: 12px;
            margin-top: 1px;
            margin-left: 4px;
            transition: transform 0.3s;
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

        .risk-tag-container {
            position: relative;
            display: inline-flex;
            align-items: center;
            margin-left: 8px;
        }

        .risk-tag-box {
            position: absolute;
            top: -4px;
            right: -4px;
            bottom: -4px;
            left: -4px;
            border: 1px solid #F56C6C;
            border-radius: 4px;
        }

        .risk-tag {
            position: relative;
            z-index: 1;
        }

        .decorative-shield {
            margin-left: 16px;
            position: relative;
            top: -2px;
        }

        .shield-dot {
            animation: shieldDotPulse 2s infinite ease-in-out;
            transform-origin: center;
        }

        .shield-dot:nth-child(2) {
            animation-delay: 0.5s;
        }

        .shield-dot:nth-child(3) {
            animation-delay: 1s;
        }

        .shield-dot:nth-child(4) {
            animation-delay: 1.5s;
        }

        @keyframes shieldDotPulse {
            0% {
                opacity: 0.4;
                transform: scale(0.8);
            }
            50% {
                opacity: 1;
                transform: scale(1.2);
            }
            100% {
                opacity: 0.4;
                transform: scale(0.8);
            }
        }

        /* 盾牌整体缓慢上下浮动（复刻参考站图片缓缓跳动的效果） */
        @keyframes shieldBob {
            from { transform: translateY(0); }
            to   { transform: translateY(-6px); }
        }
        .decorative-shield {
            animation: shieldBob 3.6s ease-in-out infinite alternate;
        }
        .header-image {
            animation: shieldBob 3.6s ease-in-out infinite alternate;
            animation-delay: 0.9s;
        }

        /* 实时同步状态条 */
        .sync-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            font-size: 13px;
            color: #529b2e;
            background: #f0f9eb;
            border: 1px solid #e1f3d8;
            border-radius: 999px;
            padding: 5px 14px;
            user-select: none;
        }
        .sync-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #67c23a;
            animation: syncPulse 2s ease-out infinite;
            flex-shrink: 0;
        }
        @keyframes syncPulse {
            0%   { box-shadow: 0 0 0 0 rgba(103, 194, 58, 0.45); }
            70%  { box-shadow: 0 0 0 9px rgba(103, 194, 58, 0); }
            100% { box-shadow: 0 0 0 0 rgba(103, 194, 58, 0); }
        }

        /* 尊重系统减少动效设置 */
        @media (prefers-reduced-motion: reduce) {
            .decorative-shield,
            .header-image,
            .shield-dot,
            .sync-dot {
                animation: none !important;
            }
        }

        /* 小屏适配：导航横滚、分页器换行精简，杜绝横向溢出 */
        @media screen and (max-width: 768px) {
            .header {
                padding: 12px 14px;
            }
            .nav-left {
                gap: 16px;
                flex: 1;
                min-width: 0;
            }
            .nav-menu {
                overflow-x: auto;
                min-width: 0;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }
            .nav-menu::-webkit-scrollbar {
                display: none;
            }
            .el-pagination {
                flex-wrap: wrap;
                row-gap: 8px;
                justify-content: center;
                max-width: 100%;
            }
            .el-pagination__sizes,
            .el-pagination__jump {
                display: none !important;
            }
        }

        .header-right {
            flex-shrink: 0;
        }

        .shield-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .decorative-shield {
            flex-shrink: 0;
            margin-top: -40px;
        }

        .header-image {
            flex-shrink: 0;
        }

        .notice-content {
            font-size: 15px;
            line-height: 1.8;
            color: #606266;
            margin: 20px 0;
            max-width: 800px;
        }

        .notice-content a {
            color: #409EFF;
            text-decoration: none;
            transition: color 0.3s;
        }

        .notice-content a:hover {
            color: #66b1ff;
            text-decoration: underline;
        }

        /* 用户信息单元格样式优化 */
        .user-info-cell {
            display: flex;
            align-items: flex-start; /* 改为顶部对齐 */
            gap: 12px;
            padding: 8px 0;
        }

        /* 头像样式固定 */
        .user-avatar {
            width: 48px;
            height: 48px;
            min-width: 48px; /* 添加最小宽度确保不会被压缩 */
            border-radius: 50%;
            object-fit: cover;
        }

        /* 用户信息容器 */
        .user-info {
            flex: 1;
            min-width: 0; /* 防止内容溢出 */
        }

        /* 响应式布局优化 */
        @media screen and (max-width: 768px) {
            .user-info-cell {
                gap: 8px;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                min-width: 40px;
            }

            /* 表格内容自动换行 */
            .el-table .cell {
                white-space: normal;
                line-height: 1.4;
            }

            /* 风险内容区域样式 */
            .risk-content {
                font-size: 13px;
                padding: 8px;
                margin: 4px 0;
            }
        }

        /* 风险等级标签样式优化 */
        .risk-level-tag {
            white-space: nowrap;
            margin: 4px 0;
        }

        /* 风险内容区域样式 */
        .risk-content-wrapper {
            margin-top: 8px;
            background: #f8f8f8;
            border-radius: 4px;
            padding: 12px;
        }

        /* 表格单元格内容溢出处理 */
        .el-table .cell {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-box-orient: vertical;
        }

        /* 移动端适配样式 */
        @media screen and (max-width: 768px) {
            /* 头部内容布局调整 */
            .header-content {
                flex-direction: column;
                padding: 16px;
                gap: 24px;
            }

            /* 左侧内容调整 */
            .header-left {
                width: 100%;
            }

            /* 右侧图片容器调整 */
            .header-right {
                width: 100%;
                justify-content: center;
            }

            /* 盾牌图标容器调整 */
            .shield-container {
                width: 100%;
                max-width: 280px;
                margin: 0 auto;
            }

            /* 统计数据网格调整 */
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 12px;
            }

            /* 统计项目样式调整 */
            .stat-item {
                padding: 12px;
            }

            /* 风险列表卡片调整 */
            .risk-card {
                margin: 12px 0;
            }

            /* 用户信息头部调整 */
            .user-info-header {
                flex-direction: column;
                gap: 12px;
            }

            .user-info-left {
                width: 100%;
            }

            /* 商家信息调整 */
            .merchant-info {
                flex-direction: column;
                gap: 8px;
            }

            /* 风险标签容器调整 */
            .risk-tag-container {
                flex-wrap: wrap;
                gap: 8px;
            }

            /* 风险内容区域调整 */
            .risk-content-wrapper {
                padding: 8px;
                margin-top: 12px;
            }

            /* 头像大小调整 */
            .user-avatar {
                width: 40px;
                height: 40px;
                min-width: 40px;
            }

            /* 标题文字大小调整 */
            .title {
                font-size: 18px;
            }

            /* 描述文字大小调整 */
            .description {
                font-size: 14px;
            }

            /* 统计数值文字大小调整 */
            .stat-value {
                font-size: 20px;
            }

            /* 统计标签文字大小调整 */
            .stat-label {
                font-size: 13px;
            }
        }

        /* 统计卡片网格布局 */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 24px;
            background: white;
            border-radius: 8px;
            margin: 16px 0;
        }

        /* 统计卡片项目样式 */
        .stat-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        /* 移动端适配样式 */
        @media screen and (max-width: 768px) {
            /* 统计网格调整为两列 */
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 12px;
                margin: 12px;
            }

            /* 统计项目样式调整 */
            .stat-item {
                padding: 12px;
                align-items: center;
                text-align: center;
            }

            /* 统计数值文字大小调整 */
            .stat-value {
                font-size: 20px;
                line-height: 1.2;
            }

            /* 统计标签文字大小调整 */
            .stat-label {
                font-size: 13px;
                color: #606266;
            }

            /* 增长率标签调整 */
            .growth-tag {
                font-size: 12px;
                padding: 2px 6px;
            }

            /* 内容区域边距调整 */
            .content-container {
                padding: 0;
            }

            /* 头部内容调整 */
            .header-content {
                padding: 16px;
                margin: 0;
            }

            /* 风控公示内容调整 */
            .notice-content {
                margin: 12px;
                font-size: 14px;
                line-height: 1.6;
            }

            /* 风控记录列表调整 */
            .risk-records {
                margin: 12px;
            }

            /* 表格内容自动换行 */
            .el-table .cell {
                white-space: normal;
                line-height: 1.4;
            }
        }

        /* 确保内容不会溢出容器 */
        .stat-item-content {
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 增加统计卡片的视觉层次 */
        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        }

        /* 优化增长率标签的显示 */
        .growth-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 12px;
            line-height: 1.4;
        }

        .section-title {
            margin: 0;
            display: flex;
            align-items: center;
        }

        .title-content {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .merchant-title {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .merchant-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .violation-content {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .risk-time {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* 确保所有带图标的文本都垂直居中对齐 */
        .risk-card .user-info-header .merchant-title svg,
        .risk-card .risk-footer .risk-time .el-icon {
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
        }

        /* 添加旋转动画 */
        .rotate-icon {
            transition: transform 0.3s ease;
        }

        .stat-item:hover .rotate-icon {
            transform: rotate(45deg);
        }

        /* 为每个图标设置不同的旋转效果 */
        .stat-item:nth-child(1):hover .rotate-icon {
            transform: rotate(45deg);
        }

        .stat-item:nth-child(2):hover .rotate-icon {
            transform: rotate(-90deg);
        }

        .stat-item:nth-child(3):hover .rotate-icon {
            transform: rotate(180deg);
        }

        .stat-item:nth-child(4):hover .rotate-icon {
            transform: rotate(360deg);
        }

        /* 调整旋转动画时间 */
        .stat-icon svg {
            transition: all 0.5s ease;
        }

        /* 图标基础样式 */
        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #F0F7FF;
            transition: all 0.3s ease;
        }

        /* 悬浮时的背景色变化 */
        .stat-item:hover .stat-icon {
            background: #E6F4FF;
        }

        /* 确保图标居中 */
        .stat-icon svg {
            margin: auto;
        }

        /* 风控信息布局 */
        .risk-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        /* 处罚依据样式 */
        .punishment-basis {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #666;
            font-size: 13px;
        }

        .punishment-basis .el-icon {
            color: #E6A23C;
        }

        /* 风控时间样式 */
        .risk-time {
            display: flex;
            align-items: center;
            gap: 4px;
            color: #909399;
            font-size: 13px;
            justify-content: flex-end;
        }

        /* 移动端适配 */
        @media screen and (max-width: 768px) {
            .risk-info {
                gap: 6px;
            }
            
            .punishment-basis {
                font-size: 12px;
            }
        }

        /* 页脚样式 */
        .risk-footer-container {
            width: 100%;
            padding: 20px 0;
            background: #f5f7fa;
            margin-top: 40px;
            border-top: 1px solid #e4e7ed;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
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

        .site-name {
            color: #409EFF;
            font-weight: 500;
        }

        .footer-link {
            color: #606266;
            text-decoration: none;
            transition: color 0.3s ease;
            max-width: 100%;
            overflow-wrap: anywhere;
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
                padding: 16px 0;
                box-sizing: border-box;
            }

            .footer-content {
                padding: 0 12px;
                box-sizing: border-box;
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