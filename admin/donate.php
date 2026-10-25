<?php
// +----------------------------------------------------------------------
// | 更新日志页面
// | 博客地址：zhonguo.ren
// | QQ群：915043052
// | 开发者：教主
// +----------------------------------------------------------------------
include("../includes/common.php");
$title='更新日志';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>    
<style>
.timeline {
    position: relative;
    padding: 20px 0;
}
.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 20px;
    height: 100%;
    width: 4px;
    background: #e0e0e0;
    border-radius: 2px;
}
.timeline-item {
    position: relative;
    margin-bottom: 40px;
    padding-left: 60px;
}
.timeline-dot {
    position: absolute;
    left: 6px;
    top: 0;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 4px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    z-index: 2;
}
.timeline-dot.bg-primary { background: #3498db; }
.timeline-dot.bg-success { background: #2ecc71; }
.timeline-dot.bg-warning { background: #f39c12; }
.timeline-dot.bg-info { background: #3498db; }
.timeline-dot.bg-danger { background: #e74c3c; }
.timeline-content {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.timeline-content h4 {
    margin-top: 0;
    color: #2c3e50;
}
.timeline-content ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}
.timeline-content li {
    margin-bottom: 5px;
    color: #555;
}
.block-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.btn-donate {
    background: #85c1e9;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-donate:hover {
    background: #5dade2;
}
.search-box {
    display: flex;
    gap: 10px;
    align-items: center;
}
.search-input {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 14px;
    width: 200px;
}
.search-input:focus {
    outline: none;
    border-color: #85c1e9;
}
.no-results {
    text-align: center;
    padding: 40px;
    color: #888;
    display: none;
}
.files-section {
    margin-top: 15px;
    display: none;
}
.files-section.show {
    display: block;
}
.toggle-btn {
    background: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
    padding: 5px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    margin-top: 10px;
}
.toggle-btn:hover {
    background: #e0e0e0;
}
</style>
<div class="col-md-12">
    <div class="block block-rounded block-themed">
        <div class="block-header">
            <h3 class="block-title">更新日志</h3>
            <div class="search-box">
                <input type="text" class="search-input" id="searchInput" placeholder="搜索日期或内容..." oninput="searchTimeline()">
            </div>
            <button class="btn-donate" onclick="showDonatePopup()">
                <i class="fa fa-heart"></i> 赞助作者
            </button>
            <a href="update.php" class="btn-donate" style="background:#2ecc71;margin-left:10px;">
                <i class="fa fa-cloud-download"></i> 在线更新
            </a>
        </div>
        <div class="block-content">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="timeline" id="timelineContainer">
                        <div id="loading" style="text-align: center; padding: 20px; display: none;">
                            <span class="fa fa-spinner fa-spin"></span> 加载中...
                        </div>
<div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
                        <h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.76.1 版本</h4>
                        <p style="color: #888; margin-bottom: 10px;">2026/10/25</p>
                        <ul>
                            <li><b>聚合登录扩展</b>：快捷登录由 QQ/微信扩展至 6 种，新增支付宝、抖音、百度、微博快捷登录，登录页新增对应品牌色按钮</li>
                            <li><b>绑定体系重构</b>：新增分站第三方登录绑定映射表，6 种方式均支持独立绑定/解绑/快捷登录，自动兼容存量 QQ/微信绑定数据</li>
                            <li><b>后台配置增强</b>：快捷登录配置新增支付宝/抖音/百度/微博 4 个独立开关，保存即时生效</li>
                            <li><b>入口逻辑修复</b>：修复仅开启支付宝等非 QQ/微信登录方式时，快捷登录入口被误拦截的问题</li>
                            <li><b>协议修正</b>：微博快捷登录对接参数按聚合登录协议修正，确保各方式授权跳转正常</li>
                        </ul>
                        </div>
                        </div>
                                        <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
                            <h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.76 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/10/25</p>
                            <ul>
                                <li><b>数据大屏</b>：新增后台数据大屏，销售额、订单量、访问量等 8 项核心指标 + 近7日销售趋势、订单状态分布、热销商品TOP8、访问趋势四大图表 + 最新订单列表，30 秒自动刷新</li>
                                <li><b>菜单交互修复</b>：修复后台侧边栏菜单展开/收起逻辑，展开一个分组自动收起其他分组，点击已展开分组可收起，当前页面所属分组同样支持点击收起</li>
                                <li><b>菜单补齐</b>：后台新增「商品销量排行」「防CC攻击管理」「APP在线生成」三个功能菜单入口</li>
                                <li><b>订单查询页改版</b>：订单查询页全新设计，去除第三方站点信息残留，统一模板风格，优化布局与移动端适配</li>
                                <li><b>安装包净化</b>：清理历史备份文件与调试残留</li>
                            </ul>
                        </div>
                    </div>

<div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
                            <h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.75 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/10/24</p>
                            <ul>
                                <li><b>图标库修复</b>：首页 Font Awesome 图标字体本地缺失导致 404，改为公共 CDN 加载，全站图标恢复正常</li>
                                <li><b>安全清理</b>：移除用户中心遗留的调试日志代码，站点根目录不再生成 debug_site_n.log</li>
                                <li><b>安装包净化</b>：安装包剔除历史运行日志与调试残留文件</li>
                                <li><b>更新日志修复</b>：修复日志页面嵌套结构错误，1.71~1.74 历史版本恢复显示，最新版本稳定置顶</li>
                            </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.74 正式版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/10/24</p>
                            <ul>
                                <li><b>货源对接菜单</b>：后台侧边栏新增「货源对接」分组，含供货大厅、批量对接上架、对接货源列表、价格监控设置、对接日志列表、商品克隆功能、旧版批量上架、网站对接设置 8 个入口</li>
                                <li><b>侧边栏折叠修复</b>：折叠按钮改由 chToggleSidebar 接管，修复点击始终无法收缩的问题；迷你态宽度 40px、主内容区自动让位、悬停临时展开、折叠状态本地记忆</li>
                                <li><b>版本检测跨域修复</b>：升级服务器 check.php 增加 CORS 响应头，修复客户站后台首页显示「最新版本：检测失败」</li>
                                <li><b>模板图片补全</b>：补齐 XHY-01 首页缺失的 8 张图片（商家APP下载大图、经营驾驶舱、6 个功能图标）</li>
                                <li><b>全站备案配置化</b>：后台系统设置新增 ICP备案号/公安备案号/增值电信许可证 三项配置，留空则前台所有页面均不显示备案信息，彻底移除硬编码</li>
                                <li><b>店铺页导航修复</b>：修复店铺页顶部导航全部 404 问题，禁售目录/最新公告/公司介绍/演示店铺/黑名单查询/APP下载/订单查询 全部改为系统路由</li>
                                <li><b>保证金徽标</b>：店铺头部和用户信息面板新增"已缴保证金 ¥xxx"或"未缴保证金"状态显示</li>
                                <li><b>分站商品功能</b>：用户端新增「我的商品」菜单，支持添加商品（自动发卡密/自动发内容/人工处理三种发货方式）、商品列表、分类管理、补卡密、上下架、删除</li>
                                <li><b>后台分站商品管理</b>：管理后台新增「分站商品」管理页面，配套全局开关和单站商品数上限配置，严格按 zid 做归属隔离</li>
                                <li><b>店铺分类管理</b>：user/classlist.php 补齐完整的店铺分类增删功能，支持新增/删除分类，删除后商品自动归入未分类</li>
                                <li><b>库存显示修复</b>：店铺页商品库存改为读取对应商品的真实卡密库存，修复永久显示缺货问题</li>
                                <li><b>店铺弹窗记忆</b>：关闭店铺页「7×24 提示」后写入 localStorage，8 小时内刷新不再弹出</li>
                                <li><b>下载页优化</b>：商家APP下载页替换为实拍海报图，统一 WebP 格式压缩，Android 按钮跳转至 APK 下载目录</li>
                                <li><b>授权系统重构</b>：includes/auth.php 去除 eval 死代码与重复判断，保留本地授权放行与远程心跳验证，自动创建缓存目录</li>
                                <li><b>安装硬拦截</b>：install/index.php 新增 install.lock 检测，已安装时直接提示并跳转首页，禁止重复渲染安装向导</li>
                                <li><b>favicon 统一</b>：全站 13 个页面的 favicon 由横版 logo 改为 /favicon.ico 红色图标</li>
                                <li><b>在线升级功能完善</b>：升级服务器 check.php 标准化、升级包 MD5 校验、SQL 自动迁移、被覆盖文件自动备份、Zip Slip 防护</li>
                            </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.73 测试版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/10/22</p>
                            <ul>
                                <li><b>在线升级功能测试</b>：此版本为测试包，用于验证在线升级流程是否正常工作</li>
                                <li>升级成功后会在根目录生成 update_test_1.73.txt 文件</li>
                            </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.72 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/10/22</p>
                            <ul>
                                <li><b>登录验证码彻底修复</b>：修复后台/用户/供应商登录验证码大小写不一致问题，code.php 统一存小写，用户输入大小写都能通过</li>
                                <li><b>CSRF 验证修复</b>：修复 CSRF token 依赖 session_id 导致偶发验证失败，改为存入 session</li>
                                <li><b>Session 500 错误修复</b>：修复 .user.ini session.save_path 受 open_basedir 限制导致的 session 写入失败，全局 session.save_path=/tmp</li>
                                <li><b>在线升级 500 修复</b>：修复 ajax_update.php 错误处理器调用未定义 debug_log 函数导致升级下载时 HTTP 500</li>
                                <li><b>curl 兼容性修复</b>：修复 curl_get 在无 curl 扩展环境下 fatal error，自动降级为 file_get_contents</li>
                                <li><b>安装授权验证修复</b>：修复 install 授权验证 json_decode 解析 BOM/warning 混入导致"返回有效 JSON 但提示格式错误"的问题</li>
                                <li><b>升级检测接口修复</b>：修复 check.php 返回结构与 upd_fetch_info 不匹配导致"升级服务器返回数据格式不正确"</li>
                                <li><b>升级错误提示优化</b>：失败时显示具体 curl 错误码和信息，方便定位网络/DNS/防火墙问题</li>
                                <li><b>前台路径修复</b>：修复 logo 路径、CSS 响应式布局、AJAX 相对路径等子目录部署问题</li>
                            </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.71 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/10/22</p>
                            <ul>
                                <li><b>F12 控制台错误全修复</b>：修复 kefu.wav 404（绝对路径改相对路径）、Google Fonts 加载失败（googleapis.com 换国内镜像 googleapis.cn）、移除 login.php 无效的 easy-pie-chart 脚本、清理 donate.php 重复加载的 layer.js</li>
                                <li><b>版本检测接口修复</b>：admin/index.php 版本检查 URL 补全 check.php，解决后台版本号一直加载的问题</li>
                                <li><b>后台仪表盘增强</b>：三栏布局对齐，新增热门访问页面 TOP10、访客 IP 排行 TOP10 统计卡，flot 图表启用 Y 轴</li>
                                <li><b>新增 Nova 现代简约模板</b>：全新蓝白主题前端模板，含首页/购买/支付/订单查询完整页面，支持 5 种主题色切换，响应式布局</li>
                                <li><b>全站模板预览图更新</b>：storeg、Agod、default、CaihongYun、AgodN、XHY-00、Aurora、XHY-02、TingdongCat、HongWan、storegg、store、AgodM、faka、CX-NEW1、Nova 共 16 套模板后台预览图全部替换为对应前台截图</li>
                                <li><b>前台聊天组件路径修复</b>：修正 chat_base_path 配置，确保子目录部署下聊天功能正常</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\index.php</li>
                                    <li>admin\ajax.php</li>
                                    <li>admin\login.php</li>
                                    <li>admin\donate.php</li>
                                    <li>admin\head.php</li>
                                    <li>assets\Agod\m3.css</li>
                                    <li>assets\AgodM\m3.css</li>
                                    <li>template\Nova\config.php</li>
                                    <li>template\Nova\index.php</li>
                                    <li>template\Nova\buy.php</li>
                                    <li>template\Nova\order.php</li>
                                    <li>template\Nova\query.php</li>
                                    <li>template\Nova\preview.png</li>
                                    <li>template\storeg\preview.png</li>
                                    <li>template\Agod\preview.png</li>
                                    <li>template\default\preview.png</li>
                                    <li>template\CaihongYun\preview.png</li>
                                    <li>template\AgodN\preview.png</li>
                                    <li>template\XHY-00\preview.png</li>
                                    <li>template\Aurora\preview.png</li>
                                    <li>template\XHY-02\preview.png</li>
                                    <li>template\TingdongCat\preview.png</li>
                                    <li>template\HongWan\preview.png</li>
                                    <li>template\storegg\preview.png</li>
                                    <li>template\store\preview.png</li>
                                    <li>template\AgodM\preview.png</li>
                                    <li>template\faka\preview.png</li>
                                    <li>template\CX-NEW1\preview.png</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.70 版本（WalTerTeChStuDio 定制版 · 大更新）</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/10/17</p>
                            <ul>
                                <li><b>用户后台全面改版</b>：对标 hack.wetcs.cn 白色主题，侧边栏/顶栏/欢迎卡/统计卡全部重写，支持悬停动画</li>
                                <li><b>登录/注册页重写</b>：左右分栏布局，蓝色等距立方插画，红色logo，密码眼睛切换，表单美化</li>
                                <li><b>分站注册页重写</b>：同款左右分栏风格，保留分站版本选择、二级域名、管理账号等全部功能</li>
                                <li><b>前端首页模板 XHY-01 重写</b>：白色营销风，导航/Hero/数据卡/下单面板/优势/文章/CTA/页脚</li>
                                <li><b>提现页面优化</b>：余额统计卡片、收款信息展示、实时手续费预览、温馨提示侧栏、提现记录美化</li>
                                <li><b>收款设置页面美化</b>：拖拽上传+实时预览+左右分栏布局</li>
                                <li><b>费率与手续费系统</b>：后台新增费率管理，4种计费模式（百分比/固定/保底/封顶），按支付宝/微信/QQ分别配置</li>
                                <li><b>一键在线升级系统</b>：后台升级页面+进度条+实时日志，下载/备份/覆盖/SQL执行全流程</li>
                                <li><b>交易统计图修复</b>：修复sales_stats接口非分站用户返回-1导致loading转圈问题</li>
                                <li><b>最新订单加载修复</b>：修复pre_orders表字段不存在导致AJAX失败的问题</li>
                                <li><b>欢迎卡遮挡修复</b>：横幅SVG限宽+pointer-events:none，欢迎文字z-index提升</li>
                                <li><b>全站logo/favicon替换</b>：首页/用户后台/登录/注册/分站/管理后台统一logo，浏览器图标更新</li>
                                <li><b>模板管理优化</b>：XHY-01设为默认模板并改名为"官方默认模板"，预览图更新为首页截图</li>
                                <li><b>作者版权保护</b>：安装页+全局页脚显示作者信息和加群链接，base64加密防篡改，删除即404</li>
                                <li><b>后台标题定制</b>：火星文艺术字体标题+©WalTerTeChStuDio版权，eval加密防修改</li>
                                <li><b>站长推荐轮播优化</b>：CSS无缝循环滑动，5张推荐图统一跳转 wetcs.cn</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>assets\user\css\merchant.css</li>
                                    <li>assets\user\css\auth.css</li>
                                    <li>user\head.php</li>
                                    <li>user\index.php</li>
                                    <li>user\ajax.php</li>
                                    <li>user\login.php</li>
                                    <li>user\reg.php</li>
                                    <li>user\regsite.php</li>
                                    <li>user\tixian.php</li>
                                    <li>user\uset.php</li>
                                    <li>template\XHY-01\index.php</li>
                                    <li>template\XHY-01\home2026.css</li>
                                    <li>template\XHY-01\preview.png</li>
                                    <li>admin\head.php</li>
                                    <li>admin\set.php</li>
                                    <li>admin\rates.php</li>
                                    <li>admin\update.php</li>
                                    <li>admin\ajax_update.php</li>
                                    <li>admin\donate.php</li>
                                    <li>includes\function.php</li>
                                    <li>includes\common.php</li>
                                    <li>includes\version.php</li>
                                    <li>install\database\install.sql</li>
                                    <li>install\view\step4.html</li>
                                    <li>assets\img\logo.png</li>
                                    <li>assets\img\favicon\favicon.png</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.61 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/08/28 01:24</p>
                            <ul>
                                <li>用户后台更新销售统计表</li>
                                <li>优化sql查询速度</li>
                                <li>删除部分内容</li>
                                <li>删除远程日志</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>user\index.php</li>
                                    <li>user\ajax.php</li>
                                    <li>admin\index.php</li>
                                    <li>admin\update.php</li>
                                    <li>admin\donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.50 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/08/05 03:49</p>
                            <ul>
                                <li>把密码类的改成了哈希</li>
                                <li>Cookie加固</li>
                                <li>修复SSO</li>
                                <li>密码基础加固</li>
                                <li>管理员加密</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>includes\member.php</li>
                                    <li>admin\login.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.49 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/08/05 02:40</p>
                            <ul>
                                <li>修复支付不发货的情况，以及再次加固了安全</li>
                                <li>修了对接不了的问题</li>
                                <li>更新日志1.49</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>other\epay_return.php</li>
                                    <li>other/getshop.php</li>
                                    <li>other/epay_notify.php</li>
                                    <li>other/wxpay_notify.php</li>
                                    <li>other/wxpay.php</li>
                                    <li>other/wxpay_final.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.48 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/08/02 07:31</p>
                            <ul>
                                <li>修复如果金额是9999999999999999就会导致0元的情况</li>
                                <li>修了USDT的遗漏问题</li>
                                <li>修了遗漏的风险</li>
                                <li>更新日志1.48</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>ajax.php</li>
                                    <li>api.php</li>
                                    <li>other\usdt-trc20\inc.php</li>
                                    <li>cron.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.47 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/08/02 06:40</p>
                            <ul>
                                <li>安全优化（把能弄的全部堵上了，至于有没有bug，后面再来说）</li>
                                <li>更新日志1.47</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\ajax_order.php</li>
                                    <li>other\epay_notify.php</li>
                                    <li>includes\core.func.php</li>
                                    <li>includes\ajax.func.php</li>
                                    <li>ajax.php</li>
                                    <li>user\ajax.php</li>
                                    <li>admin\cc_protect.php</li>
                                    <li>other\alipay.php</li>
                                    <li>includes\common.php</li>
                                    <li>api.php</li>
                                    <li>other\epay\epay_submit.class.php</li>
                                    <li>other\epay\alipay_submit.class.php</li>
                                    <li>other\codepay_notify.php</li>
                                    <li>other\codepay_return.php</li>
                                    <li>other\alipay_notify.php</li>
                                    <li>other\alipay_return.php</li>
                                    <li>other\alipay2_notify.php</li>
                                    <li>other\alipay2_return.php</li>
                                    <li>other\qqpay_notify.php</li>
                                    <li>other\wxpay_notify.php</li>
                                    <li>other\wxpay_final.php</li>
                                    <li>other\wxjspay.php</li>
                                    <li>other\wxminipay.php</li>
                                    <li>other\qqwappay.php</li>
                                    <li>other\wxwappay.php</li>
                                    <li>other\wxwap_return.php</li>
                                    <li>other\usdt-trc20\pay.php</li>
                                    <li>other\usdt-trc20\status.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.46 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/08/02 06:30</p>
                            <ul>
                                <li>把伪造支付，进行安全完善</li>
                                <li>更新日志1.46版本</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>other\epay\epay_notify.class.php</li>
                                    <li>other\getshop.php</li>
                                    <li>other\epay_return.php</li>
                                    <li>other\epay_notify.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.45 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/08/01 01:04</p>
                            <ul>
                                <li>修复商品添加和编辑的SQL注入漏洞（虽然吧这玩意是在后台）</li>
                                <li>修复SSRF漏洞，然后添加了内网IP黑名单</li>
                                <li>恢复了越权操作权限检查（这玩意之前关闭了）</li>
                                <li>修复卡密添加/查重的SQL注入（一样虽然是在后台里面）</li>
                                <li>修复OAuth SQL注入</li>
                                <li>更新日志1.45</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>sup\shopedit.php</li>
                                    <li>admin\ajax.php</li>
                                    <li>admin\ajax_order.php</li>
                                    <li>sup\fakakms.php</li>
                                    <li>sup\connect.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.44 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/07/31 20:11</p>
                            <ul>
                                <li>首次安装有时候会出现，页面缺少部件的情况（没啥影响，多访问几次首页加载就行了）</li>
                                <li>修复没白名单的情况</li>
                                <li>修复空密钥绕过的情况</li>
                                <li>修复api注入的情况</li>
                                <li>完善一些设计的缺陷</li>
                                <li>修复部分的逻辑问题</li>
                                <li>修复set的sql注入问题</li>
                                <li>更新日志1.44</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\cx-api-synchronization.php</li>
                                    <li>admin\ajax_optimize.php</li>
                                    <li>admin\set.php</li>
                                    <li>admin\set_autoreorder.php</li>
                                    <li>includes\lib\PdoHelper.php</li>
                                    <li>includes\common.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.43 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/07/11 1:27</p>
                            <ul>
                                <li>优化事多云</li>
                                <li>分站可更换首页模板的功能</li>
                                <li>更新日志1.43</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>includes\plugins\third_shiduo.php</li>
                                    <li>includes\common.php</li>
                                    <li>user\uset.php</li>
                                    <li>user\usetmoban.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.42 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/06/29 1:49</p>
                            <ul>
                                <li>修复了本地发货的问题</li>
                                <li>删除了奔溃弹窗</li>
                                <li>添加了订单卡密复制的功能</li>
                                <li>更新日志1.42</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\ajax_batch_docking.php</li>
                                    <li>admin\cron.php</li>
                                    <li>includes\core.func.php</li>
                                    <li>other\inc.php</li>
                                    <li>other\codepay.php</li>
                                    <li>assets\js\cart.js</li>
                                    <li>assets\js\main.js</li>
                                    <li>/admin/donate.php</li>
                                    <li>user\list.php</li>
                                    <li>sup\list.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.41 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/06/28 15:28</p>
                            <ul>
                                <li>添加了视多云商城接口</li>
                                <li>修复部分安全的同时添加了部分bug</li>
                                <li>修复支付回调注入问题</li>
                                <li>把多个文件拼接的问题修改了一下</li>
                                <li>对存在的逻辑缺陷完善了一下</li>
                                <li>删掉压根没有用到的mvc框架</li>
                                <li>把找回密码改了一下</li>
                                <li>更新日志1.41</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>includes\core.func.php</li>
                                    <li>other\alipay_notify.php</li>
                                    <li>other\wxpay_notify.php</li>
                                    <li>admin\ajax.php</li>
                                    <li>includes\common.php</li>
                                    <li>config\app.php</li>
                                    <li>includes\member.php</li>
                                    <li>admin\ajax_site.php</li>
                                    <li>admin\ajax_class.php</li>
                                    <li>admin\ajax_sup.php</li>
                                    <li>admin\ajax_order.php</li>
                                    <li>admin\ajax_shop.php</li>
                                    <li>admin\ajax_wall_guide.php</li>
                                    <li>admin\ajax_batch_docking.php</li>
                                    <li>includes\ajax.func.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.40 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/06/17 12:56</p>
                            <ul>
                                <li>商品管理-商品类型与对接设置-添加手动发货（用户提交订单，在订单列表操作为已完成，就可以手动发货任意信息）</li>
                                <li>修复信息泄露，SQL 注入，XSS 跨站脚本，JSON 注入，状态值未校验，批量操作状态未校验，批量操作 ID 未过滤问题</li>
                                <li>更新日志1.40</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\ajax_order.php</li>
                                    <li>admin\assets\js\list.js</li>
                                    <li>admin\shopedit.php</li>
                                    <li>ajax.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.39 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/06/14 21:16</p>
                            <ul>
                                <li>修复对接商品显示没对接的问题</li>
                                <li>自动同步多监控分压</li>
                                <li>后台登录页修复安全问题</li>
                                <li>更新日志1.39</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\ajax_batch_docking.php</li>
                                    <li>/admin/cx-synchronization.php</li>
                                    <li>/admin/cx-api-synchronization.php</li>
                                    <li>admin\ajax_optimize.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.38 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/06/12</p>
                            <ul>
                                <li>添加用户后台充值单独支付接口</li>
                                <li>完善了用户充值支付的逻辑</li>
                                <li>更新日志1.38</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>/admin/set.php</li>
                                    <li>/other/submit.php</li>
                                    <li>/user/recharge.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.37 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/06/11</p>
                            <ul>
                                <li>删除部分文件</li>
                                <li>修复分站用户价格自动恢复问题</li>
                                <li>弄了权限验证，防止调用</li>
                                <li>修复价格限制，错误变量</li>
                                <li>循环添加上限</li>
                                <li>删除实现支持回滚</li>
                                <li>批量修改添加了代理价格选项</li>
                                <li>后端完善了验证和控制</li>
                                <li>解决部分问题</li>
                                <li>更新日志1.37</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>/admin/donate.php</li>
                                    <li>includes\plugins\third_yile.php</li>
                                    <li>includes\plugins\third_jiuwu.php</li>
                                    <li>includes\plugins\third_daishua.php</li>
                                    <li>admin\cx-api-synchronization.php</li>
                                    <li>user\ajax_user.php</li>
                                    <li>user\shoplist.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.36 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/06/01 4:38</p>
                            <ul>
                                <li>把后台首页弄顺眼了</li>
                                <li>美化网站改名美化/模板设置</li>
                                <li>添加自定义模板（只可以改文字）</li>
                                <li>更新日志1.36</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\index.php</li>
                                    <li>admin\head.php</li>
                                    <li>admin\set.php</li>
                                    <li>admin\ajax.php</li>
                                    <li>index.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.35 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/05/31 2:32</p>
                            <ul>
                                <li>把用户列表创建的表移动到了后台首页</li>
                                <li>把分类列表创建的表移动到了后台首页</li>
                                <li>把文章管理创建的表移动到了后台首页</li>
                                <li>把指定地区加价创建的表移动到了后台首页</li>
                                <li>异常（异地）IP登录，后台弹窗提醒</li>
                                <li>更新日志1.35</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\index.php</li>
                                    <li>admin\classlist-table.php</li>
                                    <li>admin\userlist.php</li>
                                    <li>admin\article.php</li>
                                    <li>admin\region_price.php</li>
                                    <li>admin\login.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.34 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/05/30 6:21</p>
                            <ul>
                                <li>把员工管理移动到了用户/分站管理</li>
                                <li>更新10分钟内实时在线人数</li>
                                <li>虚拟用户生成功能</li>
                                <li>更新日志1.34</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\head.php</li>
                                    <li>admin\ajax.php</li>
                                    <li>admin\index.php</li>
                                    <li>admin\ajax_site.php</li>
                                    <li>admin\userlist.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.33 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/05/29 14:09</p>
                            <ul>
                                <li>修复亿乐部分商品缺少表导致下单错误</li>
                                <li>网站美化更新前景动画+背景样式</li>
                                <li>更新日志1.33</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>includes\plugins\third_yile.php</li>
                                    <li>includes\lib\Template.php</li>
                                    <li>admin\head.php</li>
                                    <li>template\TingdongCat\index.php</li>
                                    <li>admin\set.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.32 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/05/24 22:10</p>
                            <ul>
                                <li>商品名称自定义返回给支付</li>
                                <li>更新日志1.32</li>
                                <li>修复导出未使用卡密显示500错误的问题</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\set.php</li>
                                    <li>other\submit.php</li>
                                    <li>/admin/donate.php</li>
                                    <li>admin\download.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.31 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/05/24 01:52</p>
                            <ul>
                                <li>修复storeg模板分类，第二行空白区域</li>
                                <li>修复storegg模板分类，第二行空白区域</li>
                                <li>store模板订单页面添加时间查询</li>
                                <li>store模板订单页面优化底部切换页面</li>
                                <li>storeg模板订单页面添加时间查询</li>
                                <li>storeg模板订单页面优化底部切换页面</li>
                                <li>storegg模板订单页面添加时间查询</li>
                                <li>storegg模板订单页面优化底部切换页面</li>
                                <li>更新日志1.31</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>template\storeg\index.php</li>
                                    <li>template\storegg\index.php</li>
                                    <li>template\store\query.php</li>
                                    <li>assets\store\js\query.js</li>
                                    <li>template\storeg\query.php</li>
                                    <li>template\storegg\query.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.30 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/05/23 23:30</p>
                            <ul>
                                <li>修复storeg第2行分类字体没显示</li>
                                <li>后台页输入错密码5次封禁1天</li>
                                <li>更新日志1.30</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>template\storeg\index.php</li>
                                    <li>admin\login.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.29 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/05/17 03:53</p>
                            <ul>
                                <li>把分站管理改名用户/分站管理</li>
                                <li>把系统设置-分站相关配置移动：用户/分站管理里</li>
                                <li>修复了磁盘显示错误的问题</li>
                                <li>更新日志1.29</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>admin\head.php</li>
                                    <li>admin\sitelist.php</li>
                                    <li>admin\overview.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.28 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/05/17 00:18</p>
                            <ul>
                                <li>重新加入faka模板，并适配</li>
                                <li>完善迁移系统，适配老版本，部分其他系统，迁移到目前系统</li>
                                <li>更新日志1.28</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>template\faka</li>
                                    <li>admin\datamove.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.27 版本</h4>
                            <p style="color: #888; margin-bottom: 10px;">2026/05/15 1:58</p>
                            <ul>
                                <li>完善了搭建教程</li>
                                <li>防墙引导页显示间隔添加：秒，分，时</li>
                                <li>实现用户管理，注册的用户会显示IP</li>
                                <li>实现用户管理，点击IP可封禁</li>
                                <li>添加商品推荐功能，适配TingdongCat模板</li>
                                <li>添加更新日志</li>
                            </ul>
                            <button class="toggle-btn" onclick="toggleFiles(this)">
                                <span class="toggle-text">展开更新文件</span>
                            </button>
                            <div class="files-section">
                                <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                <ul>
                                    <li>网站搭建教程.md</li>
                                    <li>admin\set_wall_guide.php</li>
                                    <li>admin\ajax_wall_guide.php</li>
                                    <li>template\wall_guide.php</li>
                                    <li>install\init_wall_guide_config.php</li>
                                    <li>includes\common.php</li>
                                    <li>admin\userlist-table.php</li>
                                    <li>admin\userlist.php</li>
                                    <li>user\ajax.php</li>
                                    <li>admin\ajax.php</li>
                                    <li>admin\recommend.php</li>
                                    <li>admin\recommend-table.php</li>
                                    <li>admin\assets\js\recommend.js</li>
                                    <li>admin\assets\js\recommendedit.js</li>
                                    <li>admin\head.php</li>
                                    <li>admin\ajax_shop.php</li>
                                    <li>template\TingdongCat\assets\shop.inc.php</li>
                                    <li>assets\js\main.js</li>
                                    <li>ajax.php</li>
                                    <li>/admin/donate.php</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.26 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/05/14 23:35</p>
                                <ul>
                                    <li>修复SQL 注入+添加CSRF 防护</li>
                                    <li>添加对接状态功能，可以看是否已对接</li>
                                    <li>添加对接功能可查看上架时间</li>
                                    <li>修复对接日志HTML编码&变成amp的情况</li>
                                    <li>更新日志</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\sitetask.php</li>
                                        <li>admin\sitetask-check.php</li>
                                        <li>admin\sitetask-table.php</li>
                                        <li>admin\ajax_site.php</li>
                                        <li>admin\assets\js\sitetask.js</li>
                                        <li>user\sitetask.php</li>
                                        <li>admin\batchgoods.php</li>
                                        <li>admin\ajax_batch_docking.php</li>
                                        <li>ajax.php</li>
                                        <li>admin\list-table.php</li>
                                        <li>admin\ajax_order.php</li>
                                        <li>user\workorder.php</li>
                                        <li>user\list.php</li>
                                        <li>/admin/donate.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.25 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/05/13 23:36</p>
                                <ul>
                                    <li>修复点击站点任务管理，就会回收菜单的问题</li>
                                    <li>分站后台添加了站点任务</li>
                                    <li>在后台添加了站点任务审核</li>
                                    <li>修复一些按钮接口失效</li>
                                    <li>修复分站商品推广（必须分站旗下用户下单）</li>
                                    <li>添加了一些示例防止不知道怎么输入</li>
                                    <li>添加新更新日志</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\head.php</li>
                                        <li>admin\ajax_site.php</li>
                                        <li>admin\sitetask-check.php</li>
                                        <li>user\head.php</li>
                                        <li>user\sitetask.php</li>
                                        <li>admin\sitetask.php</li>
                                        <li>/admin/donate.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.24 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/05/09 21:26</p>
                                <ul>
                                    <li>修复卡卡云对接返回结果是空的情况</li>
                                    <li>修复卡卡云对接没返回对接站的情况</li>
                                    <li>修复卡卡云对接未知错误的情况</li>
                                    <li>修复卡卡云对接卡密商品数量错误的问题</li>
                                    <li>修复卡卡云对接卡密没返回本系统的情况</li>
                                    <li>添加新的更新日志</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>/admin/donate.php</li>
                                        <li>includes\plugins\third_kakayun_new.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.23 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/05/07 21:51</p>
                                <ul>
                                    <li>添加storeg模板</li>
                                    <li>添加获取空间说说列表接口配置</li>
                                    <li>添加获取空间日志列表接口配置</li>
                                    <li>修复获取空间说说功能一直加载的问题</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>assets\store\css\style.css</li>
                                        <li>template\storeg</li>
                                        <li>admin\set.php</li>
                                        <li>ajax.php</li>
                                        <li>admin\donate.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.22 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/05/06 22:05</p>
                                <ul>
                                    <li>修复了同系统自动同步，没同步输入框标题</li>
                                    <li>优化了自动同步菜单加载速度</li>
                                    <li>store模板订单页面，添加更多详情</li>
                                    <li>自动同步的使用方法：生成密钥，配置好自动同步，保存然后复制监控地址，到宝塔计划任务，任务类型选择：访问url-grt，然后把监控地址放进去，执行周期自己设置，User-Agent不用配置</li>
                                    <li>新加1.22更新日志</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\cx-api-synchronization.php</li>
                                        <li>admin\cx-synchronization.php</li>
                                        <li>template\store\query.php</li>
                                        <li>admin\donate.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.21 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/05/05 19:34</p>
                                <ul>
                                    <li>自动同步修复了同步数量错误的问题</li>
                                    <li>自动同步将时间更改成了自定义</li>
                                    <li>修复了自动同步在服务器运行不了的情况</li>
                                    <li>添加了新的日志</li>
                                    <li>修复了自动同步隔间问题</li>
                                    <li>更改了自动同步详情逻辑</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\cx-api-synchronization.php</li>
                                        <li>admin\cx-synchronization.php</li>
                                        <li>includes\cron\cx-cron.php</li>
                                        <li>admin\donate.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.20 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/05/01 08:40</p>
                                <ul>
                                    <li>更改没注册的按钮UI</li>
                                    <li>在源码标注更新版本号</li>
                                    <li>添加1.20更新日志</li>
                                    <li>分站用户的绑定域名再加5个（总7个绑定域名）</li>
                                    <li>自动同步加了个使用提示</li>
                                    <li>修复后台订单退款选项，操作没反应的情况</li>
                                    <li>XHY-02 模板添加显示/隐藏内容功能</li>
                                    <li>XHY-01模板添加显示/隐藏内容功能</li>
                                    <li>XHY-00模板添加显示/隐藏内容功能</li>
                                    <li>修复storegg模板点击分类显示不了商品</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>user\index.php</li>
                                        <li>admin\update.php</li>
                                        <li>admin\donate.php</li>
                                        <li>includes\version.php</li>
                                        <li>user\sitelist.php</li>
                                        <li>admin\sitelist.php</li>
                                        <li>user\ajax.php</li>
                                        <li>user\ndomain.php</li>
                                        <li>user\cdomain.php</li>
                                        <li>includes\common.php</li>
                                        <li>install\add_domains.sql</li>
                                        <li>admin\cx-synchronization.php</li>
                                        <li>admin\ajax_order.php</li>
                                        <li>template\XHY-02\config.php</li>
                                        <li>template\XHY-02\index.php</li>
                                        <li>admin\set.php</li>
                                        <li>template\XHY-01\config.php</li>
                                        <li>user\shop.inc.php</li>
                                        <li>template\XHY-01\index.php</li>
                                        <li>template\XHY-00\config.php</li>
                                        <li>template\XHY-00\index.php</li>
                                        <li>template\default\shop.inc.php</li>
                                        <li>assets\storeg2\js\indexpro.js</li>
                                        <li>ajax.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.19 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/27 06:54</p>
                                <ul>
                                    <li>商品排序提示语</li>
                                    <li>修复在分类进去，添加商品所属分类的问题</li>
                                    <li>自动同步，同系统已测试：10秒内新增8964个商品，新增253个分类（仅限于不获取商品详情）</li>
                                    <li>修复自动同步保存配置，没保存到数据库的问题</li>
                                    <li>修复自动同步全部信息获取，太多会加载很久</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\shoplist.php</li>
                                        <li>admin\shopedit.php</li>
                                        <li>admin\cx-api-synchronization.php</li>
                                        <li>admin\cx-synchronization.php</li>
                                        <li>admin\donate.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.18 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/26 09:38</p>
                                <ul>
                                    <li>后台首页快捷方式添加</li>
                                    <li>解决弹窗窗口有时候有点小的问题</li>
                                    <li>收入明细，添加总收入和记录等</li>
                                    <li>重新修改了一下用户后台部分ui（更简洁了）</li>
                                    <li>自动同步，同系统对接</li>
                                    <li>修复部分人后台菜单卡加载点不动的情况</li>
                                    <li>密码错误限制登录时间</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\index.php</li>
                                        <li>admin\donate.php</li>
                                        <li>admin\record.php</li>
                                        <li>admin\cx-api-synchronization.php</li>
                                        <li>admin\cx-synchronization.php</li>
                                        <li>admin\head.php</li>
                                        <li>assets\appui\js\app2.js</li>
                                        <li>assets\user\css\modern.css</li>
                                        <li>assets\user\css\app.css</li>
                                        <li>user\index.php</li>
                                        <li>user\ajax.php</li>
                                        <li>admin\set.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.17 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/24 18:04</p>
                                <ul>
                                    <li>简洁用户后台侧边栏</li>
                                    <li>简洁侧边栏菜单</li>
                                    <li>简洁用户后台页面内容</li>
                                    <li>用户后台删除大部分文件</li>
                                    <li>用户后台统一了ui文件</li>
                                    <li>修复订单查询报错</li>
                                    <li>修复平板状态布局不正常</li>
                                    <li>修复平板状态侧边栏不正常</li>
                                    <li>改变部分布局</li>
                                    <li>修复已知问题</li>
                                    <li>鱼模板更新显示销量功能</li>
                                    <li>添加更新日志菜单</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\head.php</li>
                                        <li>admin\donate.php</li>
                                        <li>assets\user\css\modern.css</li>
                                        <li>assets\user\css\modern-ultimate.css</li>
                                        <li>assets\user\js\modern.js</li>
                                        <li>user\index.php</li>
                                        <li>user\head.php</li>
                                        <li>user/rank.php</li>
                                        <li>user/workorder.php</li>
                                        <li>user/usetmoban.php</li>
                                        <li>user/workorder2.php</li>
                                        <li>user/recharge.php</li>
                                        <li>user/list.php</li>
                                        <li>user/tixian.php</li>
                                        <li>user/cdomain.php</li>
                                        <li>user/ndomain.php</li>
                                        <li>user/qiandao.php</li>
                                        <li>user/sitelist.php</li>
                                        <li>user/shoplist.php</li>
                                        <li>user/classlist.php</li>
                                        <li>user/faq.php</li>
                                        <li>user/record.php</li>
                                        <li>user/message.php</li>
                                        <li>user/userlist.php</li>
                                        <li>user/uset.php</li>
                                        <li>user/shop.php</li>
                                        <li>assets\js\main.js</li>
                                        <li>template\AgodN\config.php</li>
                                        <li>template\AgodN\index.php</li>
                                        <li>template\AgodN\js\goods.js</li>
                                        <li>ajax.php</li>
                                    </ul>
                                    <p style="color: #666; margin-bottom: 10px; margin-top: 15px;">删除文件：</p>
                                    <ul>
                                        <li>assets/user/css/modern-common.css</li>
                                        <li>assets/user/css/modern-fix.css</li>
                                        <li>user/public/css/blue_theme.css</li>
                                        <li>user/public/js/modern.js</li>
                                        <li>user/public/js/modern-config.js</li>
                                        <li>user/public/js/config-panel.js</li>
                                        <li>user/public/js/modern-config.example.json</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.16 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/20 02:38</p>
                                <ul>
                                    <li>对后台部分菜单加载，太慢进行代码优化（现在速度有点起飞了）</li>
                                    <li>添加新功能，随机卡密生成，随机+规则</li>
                                    <li>修复开启客服系统有报错的情况</li>
                                    <li>客服会话隔离，IP会话和用户会话完全隔离</li>
                                    <li>客服系统数据库表会旧转换新表</li>
                                    <li>后台订单列表，商品名称-弹窗添加一个卡密信息</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\classlist-table.php</li>
                                        <li>admin\assets\js\fakakms.js</li>
                                        <li>admin\fakakms.php</li>
                                        <li>template\default\chat\widget.php</li>
                                        <li>install\database\chat_tables.sql</li>
                                        <li>user\ajax_chat.php</li>
                                        <li>install.sql</li>
                                        <li>admin\chatwork.php</li>
                                        <li>admin\ajax.php</li>
                                        <li>admin/ajax_order.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.15 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/16 04:48</p>
                                <ul>
                                    <li>修复了一些已知问题</li>
                                    <li>修复CX-NEW1没显示文章的问题</li>
                                    <li>完善直客对接（易客）：批量对接修复、自动同步修复、下单对接修复</li>
                                    <li>解决了用户提出的问题</li>
                                    <li>解决了提出问题的用户</li>
                                    <li>更新了最新的版本号</li>
                                    <li>免费商品更改成以ip判断</li>
                                    <li>优化了一下侧边栏</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>\includes\common.php</li>
                                        <li>template\CX-NEW1\index.php</li>
                                        <li>admin\batchgoods.php</li>
                                        <li>admin\ajax_batch_docking.php</li>
                                        <li>admin\cx-api-synchronization.php</li>
                                        <li>ajax.php</li>
                                        <li>admin/head.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.14 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/11 20:04</p>
                                <ul>
                                    <li>加入了最新版本的易支付接口（安全性高）</li>
                                    <li>修改了部分内容</li>
                                    <li>后台全局菜单搜索功能</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\set.php</li>
                                        <li>includes\core.func.php</li>
                                        <li>other\submit.php</li>
                                        <li>other\epay_notify.php</li>
                                        <li>other\epay_return.php</li>
                                        <li>admin\cx-synchronization.php</li>
                                        <li>admin\head.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.13 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/11 04:36</p>
                                <ul>
                                    <li>卡卡云（新）实现批量对接</li>
                                    <li>修复store外链图片出现显示不了的情况</li>
                                    <li>修复卡卡云新建不了分类的情况</li>
                                    <li>对支付安全再次加强修复</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>assets\store\js\image-optimizer.js</li>
                                        <li>includes\plugins\third_kakayun_new.php</li>
                                        <li>template\store\index.php</li>
                                        <li>admin\ajax_shop.php</li>
                                        <li>other\alipay_notify.php</li>
                                        <li>other\alipay_return.php</li>
                                        <li>other\alipay2_notify.php</li>
                                        <li>other\qqpay_notify.php</li>
                                        <li>other\wxpay_notify.php</li>
                                        <li>other\codepay_notify.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.12 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/10 06:59</p>
                                <ul>
                                    <li>更新商品排序问题（重置一下商品排序就行）</li>
                                    <li>更新重置不了商品排序问题</li>
                                    <li>模板没显示部分结构的问题：是因为没点击后台文章管理</li>
                                    <li>更新数据库失败提示信息</li>
                                    <li>安装过程把7.4+改成了7.4PHP</li>
                                    <li>修改了供货商的一些样式</li>
                                    <li>清除了一些垃圾文件</li>
                                    <li>实现了中文名也可以登录后台</li>
                                    <li>修复store模板轮番图没显示</li>
                                    <li>新增加卡卡云新版本插件</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin\shoplist.php</li>
                                        <li>includes\common.php</li>
                                        <li>install\view\step2.html</li>
                                        <li>\sup\login.php</li>
                                        <li>\sup\reg.php</li>
                                        <li>admin\login.php</li>
                                        <li>template\store\index.php</li>
                                        <li>includes\plugins\third_kakayun_new.php</li>
                                        <li>includes\plugins\third_kakayun.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.11 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/06 19:59</p>
                                <ul>
                                    <li>AgodN修复出现2个订单同时下单，出现订单错乱的情况</li>
                                    <li>修复AgodN和部分模板游客购买跳转问题</li>
                                    <li>对叮咚鸡模板进行优化</li>
                                    <li>对叮咚鸡图标，文字进行优化</li>
                                    <li>对叮咚鸡图片继续填充</li>
                                    <li>对叮咚鸡按钮进行添加显示</li>
                                    <li>对叮咚鸡部分进行圆角处理</li>
                                    <li>对叮咚鸡图片部分进行透明处理</li>
                                    <li>对叮咚鸡今日订单详细进行字体优化</li>
                                    <li>首页模板设置进行居中优化</li>
                                    <li>商品列表添加搜索更多选项（可指定搜索状态分类等）</li>
                                    <li>给叮咚鸡模板加上了二级分类提示语</li>
                                    <li>对叮咚鸡模板更多选项进行的颜色调整</li>
                                    <li>商品列表实现点击发卡，会自动跳转到添加卡密并选择对应的商品</li>
                                    <li>实现一级分类列表，设置禁用支付方式</li>
                                    <li>商品列表实现总销量显示</li>
                                    <li>实现点击销量会跳转到今日销量排行</li>
                                    <li>更新对接异次元（异次元插件为定制插件，暂时不会发布）</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>template\AgodN\index.php</li>
                                        <li>\template\AgodN\shop.php</li>
                                        <li>\other\inc.php</li>
                                        <li>\other\submit.php</li>
                                        <li>template\TingdongCat\index.php</li>
                                        <li>template\TingdongCat\assets\shop.inc.php</li>
                                        <li>admin\set.php</li>
                                        <li>admin\shoplist.php</li>
                                        <li>admin\assets\js\shoplist.js</li>
                                        <li>admin\shoplist-table.php</li>
                                        <li>admin\classlist-table.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.10 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/29 21:29</p>
                                <ul>
                                    <li>修复部分模板订单详情</li>
                                    <li>数据迁移功能</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>ajax.php</li>
                                        <li>assets\js\main.js</li>
                                        <li>/admin/datamove.php</li>
                                        <li>admin\head.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.09 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/29 06:36</p>
                                <ul>
                                    <li>更新用户端登录页</li>
                                    <li>更新用户端注册页</li>
                                    <li>更新用户端二维码页</li>
                                    <li>更新免费商品登录领取开关</li>
                                    <li>重新优化后台登录页（更舒适流畅）</li>
                                    <li>修复后台登录页二维码显示问题</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>assets\js\login-new.js</li>
                                        <li>assets\css\login-new.css</li>
                                        <li>user\login.php</li>
                                        <li>user\reg.php</li>
                                        <li>user\findpwd.php</li>
                                        <li>admin\set.php</li>
                                        <li>\ajax.php</li>
                                        <li>admin\login.php</li>
                                        <li>admin\assets\js\qrlogin.js</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.08 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/04/14 19:26</p>
                                <ul>
                                    <li>修复部分商品没显示卡密，显示实时状态的情况</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>ajax.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.07 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/27 23:52</p>
                                <ul>
                                    <li>AgodN模板修支付没弹出弹窗</li>
                                    <li>AgodN模板添加首页公告功能</li>
                                    <li>AgodN模板添加手机号码判断</li>
                                    <li>AgodN模板库存模糊范围显示+准确显示（模板设置）</li>
                                    <li>AgodN模板修分站提升价格后，分站还是原价格的情况</li>
                                    <li>AgodN模板修点击不了商品的情况</li>
                                    <li>删除了大部分没用的文件日志</li>
                                    <li>修复开启防墙引导页会出现别人对接不了你的情况</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>template\AgodN\index.php</li>
                                        <li>template/AgodN/shop.php</li>
                                        <li>template\AgodN\config.php</li>
                                        <li>template\AgodN\js\goods.js</li>
                                        <li>includes\common.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.06 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/27 00:07</p>
                                <ul>
                                    <li>删除自助退款相关代码</li>
                                    <li>修复自动补单功能（修复点击补单不了的问题）</li>
                                    <li>修复直客部分商品没获取到参数名的情况</li>
                                    <li>修复XHY-01模板公告显示一瞬间的问题</li>
                                    <li>自动补单新增对接失败，重新提交的功能（暂时为半成品，不过可以用）</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>user\ajax_user.php</li>
                                        <li>admin\cron.php</li>
                                        <li>admin\set_autoreorder.php</li>
                                        <li>includes\plugins\third_zhike.php</li>
                                        <li>includes\core.func.php</li>
                                        <li>admin\cx-api-synchronization.php</li>
                                        <li>template\XHY-01\index.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.05 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/25 07:31</p>
                                <ul>
                                    <li>重新添加自动同步菜单</li>
                                    <li>这个版本可能是直客的专属版本了</li>
                                    <li>完善了直客的自动同步</li>
                                    <li>完善了直客的批量对接</li>
                                    <li>完善了直客的实时信息</li>
                                    <li>完善了直客商品准确数据</li>
                                    <li>完善了直客商品正常购买</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>admin/head.php</li>
                                        <li>includes\plugins\third_zhike.php</li>
                                        <li>\admin\ajax_batch_docking.php</li>
                                        <li>\admin\cx-api-synchronization.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.04 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/21 18:33</p>
                                <ul>
                                    <li>后台菜单更新：加款卡密</li>
                                    <li>后台菜单更新：秒杀商品</li>
                                    <li>后台菜单更新：支付记录</li>
                                    <li>后台菜单更新：常见问题</li>
                                    <li>后台菜单更新：分站任务</li>
                                    <li>优化store客服页面</li>
                                    <li>优化storegg模板客服页面</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>\admin\head.php</li>
                                        <li>\admin\ajax.php</li>
                                        <li>\install\database\install.sql</li>
                                        <li>\admin\seckill.php</li>
                                        <li>/admin/faq.php</li>
                                        <li>/admin/sitetask.php</li>
                                        <li>\template\store\kf.php</li>
                                        <li>\template\storegg\kf.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.03 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/14 04:49</p>
                                <ul>
                                    <li>修复批量对接默认数量信息是1的问题</li>
                                    <li>修复自动同步默认数量信息也是1的问题</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>\api.php</li>
                                        <li>\includes\plugins\third_daishua.php</li>
                                        <li>\admin\batchgoods.php</li>
                                        <li>\admin\ajax_shop.php</li>
                                        <li>\admin\cx-api-synchronization.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.02 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/13 06:51</p>
                                <ul>
                                    <li>修复同系统对接，默认数量信息，数量一直是1的问题</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>\admin\assets\js\shopedit.js</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.01 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/06 20:09</p>
                                <ul>
                                    <li>修复供货端，手机端侧边栏显示有遮挡的问题</li>
                                </ul>
                                <button class="toggle-btn" onclick="toggleFiles(this)">
                                    <span class="toggle-text">展开更新文件</span>
                                </button>
                                <div class="files-section">
                                    <p style="color: #666; margin-bottom: 10px;">更新文件：</p>
                                    <ul>
                                        <li>\assets\appui\css\main.css</li>
                                        <li>\sup\head.php</li>
                                    </ul>
                                </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio1.00 运营版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/03/03 21:06</p>
                                <ul>
                                    <li>安装系统添加重新安装检测表功能</li>
                                    <li>自动同步实现新增分类+商品同步，商品也正常归属到了分类</li>
                                    <li>优化了后台首页</li>
                                    <li>修复批量易客对接+整体优化速度全部批量对接商品加载</li>
                                    <li>客服系统添加视频上传功能</li>
                                    <li>卡卡云批量对接+分类批量+商品批量</li>
                                    <li>完善修复域名落地页配置</li>
                                    <li>实现分类提示语</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.99 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/20 09:01</p>
                                <ul>
                                    <li>更新自动同步对接站商品：自动对接功能！</li>
                                    <li>修复模板抽奖功能</li>
                                    <li>优化部分页面</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.98 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/18 12:48</p>
                                <ul>
                                    <li>修复XHY-02购买后不显示订单详情弹窗</li>
                                    <li>修复XHY-02部分东西点击不了的问题</li>
                                    <li>修复default模板购买后不显示订单详情弹窗</li>
                                    <li>修复AgodM订单查询显示问题</li>
                                    <li>修复Agod使用vue，目前购买不了</li>
                                    <li>优化了一下后台</li>
                                    <li>修复store模板前台客服页面发信息不了的问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.97 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/15 19:44</p>
                                <ul>
                                    <li>解决CX模板部分人没显示弹窗的bug</li>
                                    <li>修复漏洞：可以给任意用户发消息和查看任意用户跟客服的对话</li>
                                    <li>对于0元商品，以及可能出现0元够进行了价格验证加强（可能导致部分免费领取的商品出现问题）</li>
                                    <li>对于购物车系统这个也可能会有这种情况，也进行了加强</li>
                                    <li>限制恶意用户免费多次领取</li>
                                    <li>对于价格异常进行拦截</li>
                                    <li>加强了最终价格验证</li>
                                    <li>对批量购买折扣进行限制</li>
                                    <li>修复SQL 注入漏洞</li>
                                    <li>自动检测创建表结构</li>
                                    <li>修复了退款时订单ID记录问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.96 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/12 05:37</p>
                                <ul>
                                    <li>修复对接利润问题（需要添加加价模板）</li>
                                    <li>在添加商品/编辑商品加上成本价输入框</li>
                                    <li>后台添加多选删除功能</li>
                                    <li>后台分类管理页</li>
                                    <li>加价模板页面</li>
                                    <li>上架日志页面</li>
                                    <li>后台系统数据清理页面修正</li>
                                    <li>修复storegg模板的一些报错</li>
                                    <li>修复TingdongCat不显示商品</li>
                                    <li>修复default模板的一些问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.95 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/10 03:21</p>
                                <ul>
                                    <li>系统概况添加新功能，一键检测表完全不</li>
                                    <li>不更新时光模板（测试麻烦懒得弄）</li>
                                    <li>更新安装系统功能</li>
                                    <li>对于出现数据库连接失败页面添加检测错误判断</li>
                                    <li>图片加载优化问题</li>
                                    <li>修复批量对接，上架日志没显示的问题</li>
                                    <li>订单管理功能添加价格显示</li>
                                    <li>重修上架日志</li>
                                    <li>解决订单详情显示利润错误问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.94 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/07 23:57</p>
                                <ul>
                                    <li>废除卡卡云（需要卡卡云可先去0.93批量后，更新到0.94）</li>
                                    <li>美化上架日志，添加了一些美感和舒适度</li>
                                    <li>上架日志进行了跳转优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.93 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/06</p>
                                <ul>
                                    <li>实现卡卡云分类商品批量</li>
                                    <li>修改批量对接提示词</li>
                                    <li>压缩了一下donate.php</li>
                                    <li>优化了一下后台首页</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.92 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/05 07:38</p>
                                <ul>
                                    <li>修复亿乐社区对接</li>
                                    <li>修复对接亿乐社区的一些bug</li>
                                    <li>修复亿乐批量</li>
                                    <li>实现亿乐批量分类选择</li>
                                    <li>实现直客批量分类选择</li>
                                    <li>实现易客批量分类选择</li>
                                    <li>修复亿乐自动同步信息错误</li>
                                    <li>修复易支付备用接口支付对接问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.91 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/03 01:30</p>
                                <ul>
                                    <li>把指定地区的表弄到了安装系统里面</li>
                                    <li>实现2级路径显示客服按钮功能</li>
                                    <li>后台添加了自定义选择客服路径</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.90 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/02 04:24</p>
                                <ul>
                                    <li>修复易支付（备用1）配置保存错误问题</li>
                                    <li>修复XHY-01模板发卡商品购买不了的问题</li>
                                    <li>修复商品编辑图片上传（还真是有权限就可以了）</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.89 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27 16:04</p>
                                <p style="color: #666; margin-bottom: 10px;">下载地址：<a href="https://pan.quark.cn/s/40f9f2749845" target="_blank" style="color: #007bff;">https://pan.quark.cn/s/40f9f2749845</a></p>
                                <ul>
                                    <li>新增指定地区价格功能</li>
                                    <li>修复模板相关问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.88 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27</p>
                                <ul>
                                    <li>再次升级客服系统</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.87 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27</p>
                                <ul>
                                    <li>优化系统概况展示</li>
                                    <li>美化网站界面</li>
                                    <li>修复商品添加功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.86 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27</p>
                                <ul>
                                    <li>修复上传图片相关问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.85 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27</p>
                                <ul>
                                    <li>客服系统升级</li>
                                    <li>优化文章居中显示效果</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.84 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27</p>
                                <ul>
                                    <li>客服系统优化</li>
                                    <li>优惠券系统优化</li>
                                    <li>订单详情展示优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.83 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27</p>
                                <ul>
                                    <li>新增实时订单详情功能</li>
                                    <li>模板优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.82 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27</p>
                                <ul>
                                    <li>修复分站保存问题</li>
                                    <li>更新工单提交图片功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.81 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27</p>
                                <ul>
                                    <li>修复模板显示商品异常</li>
                                    <li>修复购物车bug</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.80 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/02/27</p>
                                <ul>
                                    <li>修复模板显示文章异常</li>
                                    <li>优化退款机制</li>
                                    <li>新增可自定义重复下单功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.79 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/31 21:46</p>
                                <p style="color: #666; margin-bottom: 10px;">下载链接：<a href="https://pan.xunlei.com/s/VOjzAK-m8OzZuZq2jgREdvsPA1?pwd=ipje#" target="_blank" style="color: #007bff;">https://pan.xunlei.com/s/VOjzAK-m8OzZuZq2jgREdvsPA1?pwd=ipje#</a></p>
                                <ul>
                                    <li>提高分站后台提现收款码内存限制</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.78 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/31</p>
                                <ul>
                                    <li>修复分站后台提现收款码上传失败问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.77 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/31</p>
                                <ul>
                                    <li>统一批量对接功能，新增基础配套功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.76 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/31</p>
                                <ul>
                                    <li>加固充值系统，修复相关安全漏洞</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.75 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/31</p>
                                <ul>
                                    <li>修复大量SQL注入、XSS、CSRF漏洞（覆盖支付、商品管理、订单查询等核心场景）</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.74 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/31</p>
                                <ul>
                                    <li>优化框架变色拟态效果</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.73 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/31</p>
                                <ul>
                                    <li>支持首页弹窗公告自定义设置</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.72 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/31</p>
                                <ul>
                                    <li>新增易客批量对接功能</li>
                                    <li>删除冗余垃圾文件</li>
                                    <li>新增细节优化，优化模板转链接功能</li>
                                    <li>修复购物车显示问题，优化购物车样式</li>
                                    <li>修复后台/前台文章显示异常</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.71 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27 19:22</p>
                                <ul>
                                    <li>修复自动同步勾选问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.70 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27</p>
                                <ul>
                                    <li>新增用户发订单功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.69 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27</p>
                                <ul>
                                    <li>批量对接玖伍、亿乐、直客平台</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.68 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27</p>
                                <ul>
                                    <li>删除风险冗余文件，修改部分内容</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.67 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27</p>
                                <ul>
                                    <li>修复系统时间规则</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.66 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27</p>
                                <ul>
                                    <li>优化对接编辑内容添加功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.65 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27</p>
                                <ul>
                                    <li>优化商品列表界面，删除开发文档</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.64 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27</p>
                                <ul>
                                    <li>优化微信支付，新增商品简介批量替换功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.63 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27</p>
                                <ul>
                                    <li>修复支付宝、微信官方支付回调问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.62 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/27 18:59</p>
                                <ul>
                                    <li>真正完善修复可运营了卡密兑换系统</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.61 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/22 23:43</p>
                                <ul>
                                    <li>完善了卡密兑换逻辑</li>
                                    <li>修复了部分bug</li>
                                    <li>优化了部分东西</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.60 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/20 05:49</p>
                                <ul>
                                    <li>修复卡密兑换</li>
                                    <li>添加功能提示语</li>
                                    <li>显示部分东西</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.59 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/17 16:33</p>
                                <ul>
                                    <li>添加了卡密兑换功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.58 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/17</p>
                                <ul>
                                    <li>对后台侧边栏进行了动态优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.57 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/14 18:34</p>
                                <ul>
                                    <li>解决了后台点击菜单会关闭大菜单的问题</li>
                                    <li>对后台继续进行了优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.56 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/12 22:11</p>
                                <ul>
                                    <li>性能更强可靠</li>
                                    <li>适配老新数据库</li>
                                    <li>修复老数据库转新版本导致查询功能失效</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.55 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/09 23:32</p>
                                <ul>
                                    <li>更新后台首页 + 换图，优化视觉体验</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.54 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/09</p>
                                <ul>
                                    <li>新增 USDT 赞助充值，拓展支付渠道</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.53 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/05 04:29</p>
                                <ul>
                                    <li>修复重复订单问题 + 固定弹窗异常</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.52 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/05</p>
                                <ul>
                                    <li>优化系统内存占用，提升运行流畅度</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.51 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/05</p>
                                <ul>
                                    <li>支付弹窗适配手机端尺寸，优化移动端操作体验</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.50 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/01 22:15</p>
                                <ul>
                                    <li>修复订单显示问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.49 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/01</p>
                                <ul>
                                    <li>解决后台进入 + 授权漏洞</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.48 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/01</p>
                                <ul>
                                    <li>持续加强上传功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.47 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/01</p>
                                <ul>
                                    <li>修复3个网页服务器错误</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.46 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/01</p>
                                <ul>
                                    <li>修复商品添加问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.45 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2026/01/01</p>
                                <ul>
                                    <li>加强上传限制 + 修复客服漏洞</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.44 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31 17:37</p>
                                <ul>
                                    <li>文件上传添加验证机制</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.43 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>完善XSS，SQL</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.42 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31 16:47</p>
                                <ul>
                                    <li>菜单回收功能</li>
                                    <li>订单详情等修复</li>
                                    <li>字体美化</li>
                                    <li>鼠标美化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.41 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>美化功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.40 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31 16:38</p>
                                <ul>
                                    <li>模板页面+商品图片分类+修复bug</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.39 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>完善了优惠券系统</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.38 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>优惠券系统的实现</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.37 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>实现链接去文字功能+客服系统图片可防大了</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.36 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>批量二级分类商品添加，页面优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.35 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>批量分类创建+批量商品+速度</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.34 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>删除dsw，简洁用户后台优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.33 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>修复用户后台</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.32 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>修复二级分类</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.31 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/31</p>
                                <ul>
                                    <li>适配了二级分类</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.30 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/29 21:37</p>
                                <ul>
                                    <li>美化了支付弹窗</li>
                                    <li>适应了支付弹窗</li>
                                    <li>订单状态操作修复</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.29 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/29 00:35</p>
                                <ul>
                                    <li>充值返利功能</li>
                                    <li>可自定义开启</li>
                                    <li>可自定义设置返利率</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.28 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/26 03:06</p>
                                <ul>
                                    <li>加强了系统安全</li>
                                    <li>加强了防止入侵</li>
                                    <li>优化了客服音频功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.27 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/25 03:42</p>
                                <ul>
                                    <li>修复了加价模板</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.26 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/25</p>
                                <ul>
                                    <li>更新客服音频功能</li>
                                    <li>优化了添加商品的功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.25 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/22 20:15</p>
                                <ul>
                                    <li>顾名思义就是优化了二级分类的规则</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.24 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/21 16:38</p>
                                <ul>
                                    <li>修复了文章顶置失效</li>
                                    <li>再次对模板进行了优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.23 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/20 20:36</p>
                                <ul>
                                    <li>模板添加部分东西</li>
                                    <li>修复异常</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.22 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/18 17:35</p>
                                <ul>
                                    <li>解除了商品编辑种类限制</li>
                                    <li>对部分页面进行的cdn优化</li>
                                    <li>修复了已知问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.21 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/17 20:42</p>
                                <ul>
                                    <li>完善了一个完美的二级分类模板，为后续的二级分类有了基本的基础</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.20 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/16 00:05</p>
                                <ul>
                                    <li>内容优化</li>
                                    <li>修复日志问题</li>
                                    <li>客服补充一些新功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.19 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/15 20:59</p>
                                <ul>
                                    <li>前台弹窗聊天框进行了升级</li>
                                    <li>聊天框有了快捷语言功能</li>
                                    <li>快捷功能可以对应后台的关键词回复进行设置回复</li>
                                    <li>修复了ui小错乱</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.18 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/14 14:13</p>
                                <ul>
                                    <li>半修复了易客功能</li>
                                    <li>易客可以正常对接</li>
                                    <li>但是内容并不完整</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.17 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/13 19:15</p>
                                <ul>
                                    <li>优化了首页</li>
                                    <li>优化了后台部分卡顿页面</li>
                                    <li>优化了用户部分页面卡顿</li>
                                    <li>提升了大部分的速度</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.16 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/13 16:58</p>
                                <ul>
                                    <li>修复订单进程功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.15 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/12 17:49</p>
                                <ul>
                                    <li>修复部分人直客对接后，订单失败，回调不了的问题</li>
                                    <li>进行了少量优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.14 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/10 19:24</p>
                                <ul>
                                    <li>自动同步价格</li>
                                    <li>自动同步库存</li>
                                    <li>自动同步标题</li>
                                    <li>自动同步简介</li>
                                    <li>自动同步图片</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.13 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/10 01:13</p>
                                <ul>
                                    <li>更新了价格自动同步监控</li>
                                    <li>更新了库存自动同步监控</li>
                                    <li>更新了10秒监控的功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.12 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/09 01:07</p>
                                <ul>
                                    <li>更改了部分东西</li>
                                    <li>解除了原本500商品的限制</li>
                                    <li>改成了无限制的商品且不卡顿</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.11 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/08 18:47</p>
                                <ul>
                                    <li>安装系统添加了一个数据库表</li>
                                    <li>修复了其他少了的数据库表</li>
                                    <li>修复分站恢复功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.10 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/12/07 20:20</p>
                                <ul>
                                    <li>更改了一个数据库表</li>
                                    <li>优化了商品限制的问题</li>
                                    <li>实现无限批量秒修改的功能</li>
                                    <li>小优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.09 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/11/29 00:22</p>
                                <ul>
                                    <li>后台首页轻微适应</li>
                                    <li>store和storegg模板列表视图和网格视图自定义选择</li>
                                    <li>后台数据分析/一周访问统计，进行手机端优化</li>
                                    <li>分站支持：批量加价/降价一键修改</li>
                                    <li>供货商后台修复了按钮点击不了的问题</li>
                                    <li>分站有更多的功能：商品搜索，自定义选择恢复</li>
                                    <li>适配7.4php-8.4php</li>
                                    <li>上架日志（蓝色主题）</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.08 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/11/23 23:06</p>
                                <ul>
                                    <li>分类自由拖拽</li>
                                    <li>更新后台系统概况模块（可以实时观看cpu，内存，磁盘使用情况）</li>
                                    <li>替换了部分名称</li>
                                    <li>重新替换了网站公告配置下的内容</li>
                                    <li>重造后台开发文档：改善了原本观感</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.07 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/11/22 14:38</p>
                                <ul>
                                    <li>彩虹退款功能</li>
                                    <li>订单详情弹窗，添加提交工单和退款功能</li>
                                    <li>新添加：易客接口</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.06 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/11/14 17:54</p>
                                <ul>
                                    <li>CX-NEW1顶部图片（后台模板自定义可修改图片）</li>
                                    <li>CX-NEW1顶部默认图片改成别的了</li>
                                    <li>商品指定内容显示，保存不了的问题，购买没显示的问题</li>
                                    <li>删除了部分没用的东西</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.05 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/11/04 06:24</p>
                                <ul>
                                    <li>后台登录页更新：更舒适的跳转动画</li>
                                    <li>安装过程发生安装错误提示</li>
                                    <li>输入错误信息导致安装失败提示</li>
                                    <li>解决store模板切换进不去</li>
                                    <li>后台首页主题更新：手机端视觉优化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.04 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/10/28 02:27</p>
                                <ul>
                                    <li>对于新手的一些小提示页面</li>
                                    <li>小提示页面适配了手机端</li>
                                    <li>更新404主题：起飞了</li>
                                    <li>更新404主题：中间一个返回主页按钮</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.03 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/10/28 01:47</p>
                                <ul>
                                    <li>更新：防cc攻击（位置：后台系统设置）</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.02 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/10/19 20:19</p>
                                <ul>
                                    <li>修复用户供货商后台页面手机端适配问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.01 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/10/19</p>
                                <ul>
                                    <li>更新批量修改价格</li>
                                    <li>全面美化用户后台</li>
                                    <li>全面优化首页</li>
                                    <li>全面优化用户后台</li>
                                    <li>全面优化管理员后台</li>
                                    <li>更新批量历史记录恢复</li>
                                    <li>减少大部分内存</li>
                                    <li>修复大量bug</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0050 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/10/19</p>
                                <ul>
                                    <li>修复了用户后台提款充值的问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0049 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/10/19</p>
                                <ul>
                                    <li>更新点击1级分类，不再需要再点击2级，自动显示2级商品</li>
                                    <li>删除美化鼠标功能（卡）</li>
                                    <li>自动同步功能</li>
                                    <li>自动补单功能</li>
                                    <li>修复监控（可能吧）</li>
                                    <li>开发文档更多细节</li>
                                    <li>稍微优化一点</li>
                                    <li>赞赏作者优化</li>
                                    <li>改名叫彩虹云商城——WalTerTeChStuDio</li>
                                    <li>修复小部分语法问题</li>
                                    <li>优化自动同步</li>
                                    <li>优化自动补单</li>
                                    <li>添加了输出缓冲，防导致JSON解析错误</li>
                                    <li>添加了老版本，自动升级数据库</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0048 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/10/14</p>
                                <ul>
                                    <li>对于原本注册页和登录页进行美化</li>
                                    <li>更新赞赏作者的功能</li>
                                    <li>开发文档（不要的可以删掉devdoc.php）</li>
                                    <li>简单的网站美化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0047 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/10/03</p>
                                <ul>
                                    <li>关键词自动回复设置</li>
                                    <li>违禁词过滤设置</li>
                                    <li>用户发送最大图片大小</li>
                                    <li>用户允许的图片格式自定义</li>
                                    <li>检查用户封禁状态</li>
                                    <li>统计一分钟内消息数量</li>
                                    <li>二次违规封禁处理</li>
                                    <li>记录封禁信息到数据库</li>
                                    <li>违规清理刷屏消息（保留5条）</li>
                                    <li>更新详细查看的记录功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0046 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/10/02</p>
                                <ul>
                                    <li>全新拟态后台</li>
                                    <li>统计系统（IP，访问地址，地区，访问时间，浏览器信息）</li>
                                    <li>更换后台图标</li>
                                    <li>换上了稍微的纸尿裤蓝主题</li>
                                    <li>蓝色的按钮等</li>
                                    <li>更换了部分图标</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0045 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/29</p>
                                <ul>
                                    <li>修复了验证码勾选不了的问题</li>
                                    <li>解决了修复用户注册。供货商就用不了，修复供货商，用户注册就用不了的问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0044 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/29</p>
                                <ul>
                                    <li>新模板（纸尿裤蓝色模板）</li>
                                    <li>修改了用户注册，并考虑不同浏览器的注册方式</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0043 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/27</p>
                                <ul>
                                    <li>修复了供货商功能，和注册功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0042 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/27</p>
                                <ul>
                                    <li>修复config.php数据库配置和服务器数据库配置不一样，彩虹读取数据库连接不一样，自动跳转跳转到安装</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0041 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/26</p>
                                <ul>
                                    <li>修复：点击部分功能会收缩子分类的问题</li>
                                    <li>添加功能：域名落地页，防墙引导站，3秒加载</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0040 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/22 04:40</p>
                                <ul>
                                    <li>16.91更新：修复部分模板问题</li>
                                    <li>16.92更新：修复并添加邮箱功能</li>
                                    <li>16.93更新：修复部分功能</li>
                                    <li>16.94更新：修复模板问题</li>
                                    <li>16.95更新：修复客服系统开关</li>
                                    <li>16.96更新：storegg模板添加网页客服</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0039 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/18 02:10</p>
                                <ul>
                                    <li>更新：把用户注册给全强兼了，现在所有浏览器注册不会出问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0038 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/18 00:23</p>
                                <ul>
                                    <li>更新：又是适配一套鲸鱼模板，速度视乎不错？</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0037 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/17 07:32</p>
                                <ul>
                                    <li>16.5更新：修复了用户注册问题</li>
                                    <li>16.6更新：提高了后台登录的有效时长</li>
                                    <li>16.7更新：2套鲸鱼模板，一个老鲸鱼，一个新鲸鱼 Agod和Magod</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0036 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/15 21:37</p>
                                <ul>
                                    <li>16.2更新：改善一个模板</li>
                                    <li>16.3更新：优化了安装程序，更方便</li>
                                    <li>16.4更新：重点修复了订单对接不了，直客订单对接的问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0035 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/09/02 01:21</p>
                                <ul>
                                    <li>16.1更新：重点优化了安装程序+速度，再也不用手动安装了</li>
                                    <li>16更新：用户供货商后台全局简洁风，再也不是那乱风格了</li>
                                    <li>15.9更新:供货商验证码的更新</li>
                                    <li>15.8更新：初步美化供货商后台</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0034 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/29 13:58</p>
                                <ul>
                                    <li>15.3更新：自定义页面图标</li>
                                    <li>15.4更新：优化了页面标题</li>
                                    <li>15.5更新：后台添加功能说明</li>
                                    <li>15.6更新：修复部分模板的列表显示</li>
                                    <li>15.7更新：适配了一个2级分类</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0033 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/24 16:49</p>
                                <ul>
                                    <li>更新：购买弹窗，目前适配一个模板</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0032 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/24 16:48</p>
                                <ul>
                                    <li>更新：修复了大部分错误语法</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0031 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/21 08:18</p>
                                <ul>
                                    <li>更新：更新二级分类（还没那么完美，不过也可以说完美了）</li>
                                    <li>更新：把一些莫名其妙的bug修复了</li>
                                    <li>预告：我也不知道更新什么</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0030 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/20 10:39</p>
                                <ul>
                                    <li>更新：一个新的模板</li>
                                    <li>预告：更新其他模板以及2级分类</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0029 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/20 06:57</p>
                                <ul>
                                    <li>更新：全新的后台美化，可自定义原本或者美化版本</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0028 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/16 08:17</p>
                                <ul>
                                    <li>更新：修复所有的模板bug，并优化加载速度</li>
                                    <li>预告：之后不用怕不会适配，后面会越来越多适配的！</li>
                                    <li>完美解决了适配的问题（并非所有因为模板构成不一样）</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0027 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/16 07:42</p>
                                <ul>
                                    <li>更新：史诗级优化，毫米加载商品，0.5-1秒无缓存加载</li>
                                    <li>更新：模板全体添加前台商品显示数量美化</li>
                                    <li>更新：新模板XHY-02</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0026 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/13 08:33</p>
                                <ul>
                                    <li>更新：适配了新的模板</li>
                                    <li>更新：修复了模板部分bug</li>
                                    <li>更新：稍微优化了商品显示</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0025 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/11 02:36</p>
                                <ul>
                                    <li>更新：美化手机端后台的登陆页</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0024 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/10 04:26</p>
                                <ul>
                                    <li>更新：网站后台登录美化</li>
                                    <li>更新：优化了后台登录速度</li>
                                    <li>更新：添加了后台登录页忘记密码的提示</li>
                                    <li>更新：前台客服窗口弄了u型角微美化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0023 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/09 07:18</p>
                                <ul>
                                    <li>更新：优化加载效果</li>
                                    <li>更新：自定义颜色按钮</li>
                                    <li>更新：自定义聊天窗口顶部颜色</li>
                                    <li>更新：按钮ui简洁了一些+改了图标</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0022 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/09 06:34</p>
                                <ul>
                                    <li>更新：自定义客服标题</li>
                                    <li>更新：自定义前台客服描述</li>
                                    <li>预告更新：自定义客服头像+默认qq头像</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0021 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/07 06:50</p>
                                <ul>
                                    <li>更新：在前台客服窗口修复了按钮显示不了的问题</li>
                                    <li>更新：再次优化了按钮点击判断</li>
                                    <li>预告更新：客服系统自定义名称/内容</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0020 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/07 06:49</p>
                                <ul>
                                    <li>更新：适配添加了新的热门模板</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0019 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/08/01 20:23</p>
                                <ul>
                                    <li>解决了批量购买不了的问题</li>
                                    <li>添加了购物车功能</li>
                                    <li>解决了奸商批量</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0018 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/31 18:03</p>
                                <ul>
                                    <li>单模板4：美化了供货商后台</li>
                                    <li>单模板5：修复了用户后台购买</li>
                                    <li>单模板6：前台添加了购物单按钮</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0017 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/29 00:28</p>
                                <ul>
                                    <li>彩虹单模板1：就是只有一个模板</li>
                                    <li>彩虹单模板2：修复控制台错误，美化，优化</li>
                                    <li>彩虹单模板3：修复供货商系统，而正常使用</li>
                                    <li>彩虹单模板4预告：美化供货商系统后台</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0016 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/29 00:25</p>
                                <ul>
                                    <li>现在新版本文件夹叫：彩虹单模板</li>
                                    <li>如名：就是只有一个模板</li>
                                    <li>原因：太多模板要更新很难全部适配</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0015 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/28 13:51</p>
                                <ul>
                                    <li>更新：优化了全部模板，但是就弄了一个CX-NEW1的本地加载，你们可以试试CX-NEW1模板，如果CX-NEW1可以那就全部用本地加载，或者全面本地加载</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0014 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/28 08:53</p>
                                <ul>
                                    <li>更新成功解决商品显示问题！</li>
                                    <li>不过目前只适配了CX-NEW1</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0013 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/27 10:54</p>
                                <ul>
                                    <li>更新：修复删除不了上架日志的问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0012 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/27 09:22</p>
                                <ul>
                                    <li>更新：把部分用户手机端的客服按钮按不了的问题解决了</li>
                                    <li>更新：重新优化了客服按钮移动</li>
                                    <li>更新：把弹出窗口弄了更流畅一些</li>
                                    <li>更新：客服按钮现在不能随便移动（其实也可以）</li>
                                    <li>更新：客服按钮现在可以左右移动，上下不了（其实上下也可以）</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0011 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/24 03:12</p>
                                <ul>
                                    <li>更新8-8完美洛天依主题，99%的集全美化</li>
                                    <li>更新8-8洛天依极简美化，适合服务器配置差的人用，外加了登录界面：随机背景图</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0010 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/24 03:09</p>
                                <ul>
                                    <li>更新6-8粉加黑的近完美主题美化</li>
                                    <li>更新7-8粉加高级灰的近完美美化</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0009 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/23 20:11</p>
                                <p style="color: #666; margin-bottom: 10px;">1-1还是1-2等等的意思就是：美化1-彩虹源码2</p>
                                <ul>
                                    <li>更新：1-8原版的基础上再美化</li>
                                    <li>更新：2-8完全的另外一个版本的美化（粉色系列）</li>
                                    <li>更新：3-8这个完美一丢丢不过还是粉色系列</li>
                                    <li>更新：4-8侧边栏完美适应粉色系列</li>
                                    <li>更新：5-8侧边栏完美-部分字体适应</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0008 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/22 20:38</p>
                                <p style="color: #666; margin-bottom: 10px;">网站搭建 - 近完美修完bug的源码</p>
                                <ul>
                                    <li>更新：人工客服基本完善，可用</li>
                                    <li>更新：新型的客服系统</li>
                                    <li>更新：再次优化了前端客服按钮的错误问题</li>
                                    <li>更新：现在客服按钮你只需要开启就可以使用，不再需要手动代码加入</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0007 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/22 20:33</p>
                                <ul>
                                    <li>更新：解决了用户发送不了消息的问题</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0006 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/22 02:23</p>
                                <ul>
                                    <li>更新：解决用户反馈的，出现2个按钮的问题</li>
                                    <li>更新：解决客服按钮按不了的问题</li>
                                    <li>更新：稍微优化了客服按钮随便移动</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-primary"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0005 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/20 15:42</p>
                                <ul>
                                    <li>更新：网站美化：樱花飘落</li>
                                    <li>更新：全新的客服系统</li>
                                    <li>更新：只能按客服系统按钮才会弹出，不会进网站就弹</li>
                                    <li>更新：客服系统更新强制关闭客服窗口</li>
                                    <li>更新：首页客服按钮可以移动（有点卡，后期优化）</li>
                                    <li>更新：客服按钮在电脑端会显示客服2个字，手机端只会显示图标</li>
                                    <li>更新：修复大部分的模板</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-danger"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0004 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/20 15:40</p>
                                <ul>
                                    <li>更新:添加了2套现代化，美化的模板，并适配了一下</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-success"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0003 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/20 15:39</p>
                                <ul>
                                    <li>修复卡密购买问题</li>
                                    <li>适配了部分功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-warning"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0002 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/20 15:39</p>
                                <ul>
                                    <li>优化了客服系统</li>
                                    <li>添加删除聊天记录功能</li>
                                </ul>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot bg-info"></div>
                        <div class="timeline-content">
<h4 class="font-w600">彩虹云商城——WalTerTeChStuDio0.0001 版本</h4>
                                <p style="color: #888; margin-bottom: 10px;">2025/07/20 15:37</p>
                                <ul>
                                    <li>更新：客服系统</li>
                                </ul>
                        </div>
                    </div>
<div class="no-results" id="noResults">
                        <i class="fa fa-search" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <h4>未找到匹配的版本</h4>
                        <p>请尝试其他搜索词</p>
                    </div>
                    </div>
        </div>
    </div>
</div>

</div>
</div>
<script>
// 滚动加载功能
let allTimelineItems = [];
let currentIndex = 0;
const loadBatchSize = 10; // 每次加载10个项目
let isLoading = false;
let isSearching = false;

function showDonatePopup() {
    layer.open({
        type: 1,
        title: '赞赏支持',
        area: ['500px', 'auto'],
        content: '<div style="padding: 20px; text-align: center;">' +
            '<h4 style="margin-bottom: 20px;">感谢您使用我们的系统！</h4>' +
            '<p style="color: #666; margin-bottom: 25px;">' +
            '如果您觉得我们的系统对您有帮助，不妨请开发者喝杯咖啡吧！<br>' +
            '您的支持是我们持续更新和维护的动力！' +
            '</p>' +
            '<img src="../assets/img/wxzsm.jpg" alt="微信赞赏码" style="max-width: 280px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px;">' +
            '<h5 style="margin-bottom: 10px;">微信扫码赞赏</h5>' +
            '<p style="color: #888;">赞赏金额随意，多少都是心意</p>' +
            '</div>',
        btn: ['好的', '关闭'],
        yes: function(index){
            layer.close(index);
        }
    });
}

function searchTimeline() {
    var searchTerm = document.getElementById('searchInput').value.toLowerCase();
    var timelineItems = document.querySelectorAll('.timeline-item');
    var noResults = document.getElementById('noResults');
    var hasResults = false;

    isSearching = searchTerm !== '';

    timelineItems.forEach(function(item) {
        var content = item.textContent.toLowerCase();
        if (searchTerm === '' || content.includes(searchTerm)) {
            item.style.display = 'block';
            hasResults = true;
        } else {
            item.style.display = 'none';
        }
    });

    if (searchTerm === '') {
        noResults.style.display = 'none';
    } else if (!hasResults) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

function toggleFiles(btn) {
    var filesSection = btn.nextElementSibling;
    var toggleText = btn.querySelector('.toggle-text');
    
    if (filesSection.classList.contains('show')) {
        filesSection.classList.remove('show');
        toggleText.textContent = '展开更新文件';
    } else {
        filesSection.classList.add('show');
        toggleText.textContent = '收起更新文件';
    }
}

// 初始化滚动加载
function initInfiniteScroll() {
    // 保存所有时间线项目
    const timelineItems = document.querySelectorAll('.timeline-item');
    allTimelineItems = Array.from(timelineItems);
    
    // 清空容器
    const container = document.getElementById('timelineContainer');
    // 保留加载指示器
    const loading = document.getElementById('loading');
    container.innerHTML = '';
    container.appendChild(loading);
    
    // 初始加载
    loadMoreItems();
    
    // 监听滚动事件
    window.addEventListener('scroll', handleScroll);
}

// 加载更多项目
function loadMoreItems() {
    if (isLoading || isSearching || currentIndex >= allTimelineItems.length) return;
    
    isLoading = true;
    document.getElementById('loading').style.display = 'block';
    
    // 模拟加载延迟
    setTimeout(() => {
        const container = document.getElementById('timelineContainer');
        const endIndex = Math.min(currentIndex + loadBatchSize, allTimelineItems.length);
        
        for (let i = currentIndex; i < endIndex; i++) {
            container.insertBefore(allTimelineItems[i], document.getElementById('loading'));
        }
        
        currentIndex = endIndex;
        isLoading = false;
        document.getElementById('loading').style.display = currentIndex >= allTimelineItems.length ? 'none' : 'block';
    }, 300);
}

// 处理滚动事件
function handleScroll() {
    if (isLoading || isSearching) return;
    
    const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight || document.body.scrollHeight;
    const clientHeight = document.documentElement.clientHeight || window.innerHeight;
    
    // 当滚动到距离底部100px时加载更多
    if (scrollTop + clientHeight >= scrollHeight - 100) {
        loadMoreItems();
    }
}

// 页面加载完成后初始化
window.addEventListener('DOMContentLoaded', initInfiniteScroll);
</script>
</body>
</html>