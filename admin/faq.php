<?php
include "../includes/common.php";

$title = "常见问题解答";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
    <div class="col-md-12 center-block" style="float: none;">
<?php 
adminpermission("article", 1);
?>
<div class="block">
<div class="block-title clearfix">
<h2>常见问题解答</h2>
</div>
<div class="content">
    <style>
        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .faq-item {
            margin-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
        }
        .faq-question {
            font-weight: bold;
            color: #007bff;
            cursor: pointer;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .faq-question:hover {
            background-color: #e9ecef;
        }
        .faq-answer {
            padding: 15px;
            background-color: #fff;
            border-left: 3px solid #007bff;
            margin-top: 10px;
            display: none;
        }
        .faq-answer.show {
            display: block;
        }
        .category-title {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .code-block {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', Courier, monospace;
            margin: 10px 0;
        }
        .tip-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 10px;
            margin: 10px 0;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include './header.php'; ?>
        <div class="leftside">
            <?php include './menu.php'; ?>
        </div>
        <div class="rightside">
            <div class="content">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">常见问题解答</h3>
                        <p class="card-text">本页面汇总了系统使用和开发过程中的常见问题及解决方案，帮助您快速解决遇到的困难。</p>
                        <p class="text-muted">作者：教主 - <a href="https://www.898fk.com" target="_blank">WalTerTeChStuDio</a> | QQ：1560292</p>
                    </div>
                </div>

                <div class="faq-container mt-4">
                    <!-- 系统安装与配置 -->
                    <div class="category-title">一、系统安装与配置</div>
                    
                    <div class="faq-item">
                        <div class="faq-question">Q: 系统安装要求是什么？</div>
                        <div class="faq-answer">
                            <p>系统安装要求如下：</p>
                            <ul>
                                <li>操作系统：Windows Server 2012+ 或 Linux 系统</li>
                                <li>Web服务器：Nginx 1.22.0+</li>
                                <li>PHP版本：7.4+</li>
                                <li>MySQL版本：5.7+</li>
                                <li>其他：宝塔面板（推荐）</li>
                            </ul>
                            <div class="tip-box">
                                <strong>提示：</strong>建议使用宝塔面板进行环境配置，可大大简化安装过程。
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何配置数据库连接？</div>
                        <div class="faq-answer">
                            <p>数据库连接配置位于系统根目录的<code>config.php</code>文件中，您需要修改以下参数：</p>
                            <div class="code-block">
                                <?php
                                // 数据库配置
                                define('DB_HOST', 'localhost'); // 数据库主机
                                define('DB_USER', 'root'); // 数据库用户名
                                define('DB_PASS', 'password'); // 数据库密码
                                define('DB_NAME', 'shua_system'); // 数据库名
                                define('DB_PORT', '3306'); // 数据库端口
                                ?>
                            </div>
                            <p>修改完成后保存文件，系统将使用新的数据库连接信息。</p>
                            <div class="warning-box">
                                <strong>警告：</strong>请确保数据库用户具有足够的权限，同时不要在生产环境中使用root用户直接连接数据库。
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 安装后无法访问后台怎么办？</div>
                        <div class="faq-answer">
                            <p>安装后无法访问后台可能有以下几种原因及解决方案：</p>
                            <ol>
                                <li><strong>文件权限问题</strong>：确保所有PHP文件具有正确的读写权限。</li>
                                <li><strong>数据库连接错误</strong>：检查<code>config.php</code>中的数据库配置是否正确。</li>
                                <li><strong>Nginx配置问题</strong>：确保Nginx正确配置了伪静态规则。
                                    <div class="code-block">
                                        location / {
                                            if (!-e $request_filename) {
                                                rewrite  ^(.*)$  /index.php?s=$1  last;
                                                break;
                                            }
                                        }
                                    </div>
                                </li>
                                <li><strong>PHP扩展缺失</strong>：检查是否安装了必要的PHP扩展（如pdo_mysql、mbstring等）。</li>
                                <li><strong>浏览器缓存问题</strong>：清除浏览器缓存或尝试使用无痕模式访问。</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何修改系统的基本配置（如网站名称、Logo等）？</div>
                        <div class="faq-answer">
                            <p>您可以通过以下两种方式修改系统基本配置：</p>
                            <ol>
                                <li><strong>通过后台管理界面</strong>：登录后台后，进入"系统设置" -> "基本设置"页面进行修改。</li>
                                <li><strong>直接修改数据库</strong>：可以直接修改<code>shua_config</code>表中的相应配置项。
                                    <div class="code-block">
                                        -- 例如修改网站名称
                                        UPDATE shua_config SET v = '新的网站名称' WHERE k = 'web_name';
                                    </div>
                                </li>
                            </ol>
                            <p>修改完成后，系统会自动应用新的配置，无需重启服务。</p>
                        </div>
                    </div>

                    <!-- 系统使用问题 -->
                    <div class="category-title">二、系统使用问题</div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何添加新的商品？</div>
                        <div class="faq-answer">
                            <p>添加新商品的步骤如下：</p>
                            <ol>
                                <li>登录系统后台</li>
                                <li>点击左侧菜单的"商品管理" -> "商品列表"</li>
                                <li>点击页面顶部的"添加商品"按钮</li>
                                <li>填写商品信息，包括商品名称、价格、库存、描述等</li>
                                <li>选择商品分类和相关配置</li>
                                <li>点击"提交"按钮完成添加</li>
                            </ol>
                            <div class="tip-box">
                                <strong>提示：</strong>添加商品时，请确保填写清晰的商品描述和准确的价格信息，以便用户更好地了解商品。
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何处理用户的订单？</div>
                        <div class="faq-answer">
                            <p>处理用户订单的步骤如下：</p>
                            <ol>
                                <li>登录系统后台</li>
                                <li>点击左侧菜单的"订单管理" -> "订单列表"</li>
                                <li>查看待处理的订单列表</li>
                                <li>点击订单操作列的"处理"按钮进行处理</li>
                                <li>根据订单类型和要求完成相应操作</li>
                                <li>处理完成后，更新订单状态为"已完成"</li>
                            </ol>
                            <p>对于自动处理的订单，系统会根据预设的规则自动完成处理，无需手动干预。</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何设置支付方式？</div>
                        <div class="faq-answer">
                            <p>设置支付方式的步骤如下：</p>
                            <ol>
                                <li>登录系统后台</li>
                                <li>点击左侧菜单的"支付设置" -> "支付方式管理"</li>
                                <li>选择您需要启用的支付方式（如支付宝、微信支付等）</li>
                                <li>填写相应的支付参数（如APPID、商户号、密钥等）</li>
                                <li>点击"保存"按钮，并启用该支付方式</li>
                            </ol>
                            <div class="warning-box">
                                <strong>注意：</strong>支付参数涉及资金安全，请妥善保管，不要泄露给他人。建议定期更换密钥。
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何查看系统日志？</div>
                        <div class="faq-answer">
                            <p>查看系统日志的方式有两种：</p>
                            <ol>
                                <li><strong>通过后台管理界面</strong>：
                                    <ul>
                                        <li>登录系统后台</li>
                                        <li>点击左侧菜单的"系统管理" -> "操作日志"</li>
                                        <li>可以查看系统运行过程中的各种操作记录</li>
                                    </ul>
                                </li>
                                <li><strong>直接查看日志文件</strong>：
                                    <ul>
                                        <li>系统日志通常保存在<code>logs/</code>目录下</li>
                                        <li>可以使用文本编辑器直接查看日志内容</li>
                                    </ul>
                                </li>
                            </ol>
                            <p>系统日志包括操作日志、错误日志、访问日志等，有助于排查问题和监控系统运行状态。</p>
                        </div>
                    </div>

                    <!-- 开发相关问题 -->
                    <div class="category-title">三、开发相关问题</div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何扩展系统功能？</div>
                        <div class="faq-answer">
                            <p>扩展系统功能的方法如下：</p>
                            <ol>
                                <li><strong>了解系统架构</strong>：查看系统的整体架构和模块划分。</li>
                                <li><strong>遵循开发规范</strong>：遵循系统的编码规范和开发流程。</li>
                                <li><strong>添加新模块</strong>：
                                    <ul>
                                        <li>在相应目录下创建新的PHP文件</li>
                                        <li>实现所需功能</li>
                                        <li>在数据库中添加必要的表或字段</li>
                                        <li>在后台菜单中注册新功能</li>
                                    </ul>
                                </li>
                                <li><strong>修改现有功能</strong>：
                                    <ul>
                                        <li>找到相应的功能文件</li>
                                        <li>按照需求进行修改</li>
                                        <li>进行充分测试</li>
                                    </ul>
                                </li>
                            </ol>
                            <div class="tip-box">
                                <strong>提示：</strong>在扩展系统功能时，建议先在测试环境中进行开发和测试，确认无误后再部署到生产环境。
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何调用系统API？</div>
                        <div class="faq-answer">
                            <p>调用系统API的步骤如下：</p>
                            <ol>
                                <li><strong>获取API文档</strong>：了解API接口的详细信息。</li>
                                <li><strong>获取API密钥</strong>：
                                    <ul>
                                        <li>登录系统后台</li>
                                        <li>进入"API设置"页面</li>
                                        <li>生成或查看API密钥</li>
                                    </ul>
                                </li>
                                <li><strong>构建API请求</strong>：
                                    <ul>
                                        <li>根据API文档，准备请求参数</li>
                                        <li>按照要求进行签名验证</li>
                                        <li>发送HTTP请求到API接口地址</li>
                                    </ul>
                                </li>
                                <li><strong>处理API响应</strong>：
                                    <ul>
                                        <li>接收API返回的JSON数据</li>
                                        <li>解析响应内容</li>
                                        <li>根据返回结果进行相应处理</li>
                                    </ul>
                                </li>
                            </ol>
                            <p>以下是一个调用API的示例代码：</p>
                            <div class="code-block">
                                <?php
                                // API调用示例
                                function call_api($url, $params, $api_token) {
                                    $params['token'] = $api_token;
                                    $params['time'] = time();
                                    // 构建签名
                                    ksort($params);
                                    $sign_str = http_build_query($params);
                                    $params['sign'] = md5($sign_str);
                                    
                                    // 发送请求
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_POST, 1);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    $result = curl_exec($ch);
                                    curl_close($ch);
                                    
                                    // 返回解析后的JSON数据
                                    return json_decode($result, true);
                                }
                                
                                // 调用示例
                                $api_url = 'http://127.0.0.3/api/shop.php';
                                $api_token = 'your_api_token';
                                $params = array(
                                    'action' => 'get_goods_list',
                                    'page' => 1,
                                    'page_size' => 10
                                );
                                
                                $result = call_api($api_url, $params, $api_token);
                                if ($result['code'] == 0) {
                                    // 处理成功
                                    print_r($result['data']);
                                } else {
                                    // 处理失败
                                    echo 'Error: ' . $result['msg'];
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何调试系统问题？</div>
                        <div class="faq-answer">
                            <p>调试系统问题的方法如下：</p>
                            <ol>
                                <li><strong>启用调试模式</strong>：
                                    <ul>
                                        <li>在<code>config.php</code>中设置<code>define('DEBUG', true);</code></li>
                                        <li>系统会输出更详细的错误信息和日志</li>
                                    </ul>
                                </li>
                                <li><strong>使用debug函数</strong>：
                                    <div class="code-block">
                                        // 使用debug函数输出变量信息
                                        $data = array('key' => 'value');
                                        debug($data); // 会输出变量的详细信息并终止程序
                                    </div>
                                </li>
                                <li><strong>记录日志</strong>：
                                    <div class="code-block">
                                        // 使用write_log函数记录操作日志
                                        write_log('用户登录', array('user_id' => 123, 'username' => 'test'));
                                    </div>
                                </li>
                                <li><strong>检查数据库</strong>：
                                    <ul>
                                        <li>使用phpMyAdmin或MySQL命令行工具检查数据</li>
                                        <li>执行SQL查询来验证数据是否正确</li>
                                    </ul>
                                </li>
                                <li><strong>检查网络请求</strong>：
                                    <ul>
                                        <li>使用浏览器的开发者工具查看网络请求和响应</li>
                                        <li>检查AJAX请求的状态和返回值</li>
                                    </ul>
                                </li>
                            </ol>
                            <div class="warning-box">
                                <strong>注意：</strong>调试模式会输出详细信息，可能包含敏感数据，在生产环境中请务必关闭调试模式。
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 如何优化系统性能？</div>
                        <div class="faq-answer">
                            <p>优化系统性能的方法如下：</p>
                            <ol>
                                <li><strong>数据库优化</strong>：
                                    <ul>
                                        <li>为常用查询字段添加索引</li>
                                        <li>优化SQL查询语句，避免使用<code>SELECT *</code></li>
                                        <li>定期清理无用数据，优化表结构</li>
                                        <li>考虑使用读写分离和数据库缓存</li>
                                    </ul>
                                </li>
                                <li><strong>代码优化</strong>：
                                    <ul>
                                        <li>减少数据库查询次数，合并重复查询</li>
                                        <li>使用缓存技术（如Redis、Memcached）</li>
                                        <li>优化循环和递归，避免不必要的计算</li>
                                        <li>使用PHP的性能分析工具查找瓶颈</li>
                                    </ul>
                                </li>
                                <li><strong>服务器优化</strong>：
                                    <ul>
                                        <li>配置Nginx缓存静态资源</li>
                                        <li>优化PHP配置参数（如memory_limit、max_execution_time等）</li>
                                        <li>考虑使用CDN加速静态资源访问</li>
                                        <li>升级服务器硬件或使用负载均衡</li>
                                    </ul>
                                </li>
                                <li><strong>前端优化</strong>：
                                    <ul>
                                        <li>压缩CSS、JavaScript和图片文件</li>
                                        <li>减少HTTP请求数量</li>
                                        <li>使用异步加载技术</li>
                                        <li>优化页面渲染性能</li>
                                    </ul>
                                </li>
                            </ol>
                            <div class="tip-box">
                                <strong>提示：</strong>性能优化是一个持续的过程，建议定期监控系统性能，并根据实际情况进行调整和优化。
                            </div>
                        </div>
                    </div>

                    <!-- 故障排除 -->
                    <div class="category-title">四、故障排除</div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 系统出现500错误怎么办？</div>
                        <div class="faq-answer">
                            <p>系统出现500错误（内部服务器错误）可能有以下原因及解决方案：</p>
                            <ol>
                                <li><strong>PHP语法错误</strong>：
                                    <ul>
                                        <li>检查最近修改的PHP文件是否有语法错误</li>
                                        <li>查看PHP错误日志获取详细信息</li>
                                    </ul>
                                </li>
                                <li><strong>权限问题</strong>：
                                    <ul>
                                        <li>确保PHP文件和目录具有正确的读写权限</li>
                                        <li>检查文件所有者和组设置</li>
                                    </ul>
                                </li>
                                <li><strong>内存不足</strong>：
                                    <ul>
                                        <li>增加PHP的memory_limit设置</li>
                                        <li>检查是否有内存泄漏问题</li>
                                    </ul>
                                </li>
                                <li><strong>数据库连接问题</strong>：
                                    <ul>
                                        <li>检查数据库服务器是否正常运行</li>
                                        <li>验证数据库连接配置是否正确</li>
                                    </ul>
                                </li>
                                <li><strong>代码逻辑错误</strong>：
                                    <ul>
                                        <li>使用调试工具逐步排查问题</li>
                                        <li>添加日志记录关键操作和变量值</li>
                                    </ul>
                                </li>
                            </ol>
                            <div class="warning-box">
                                <strong>注意：</strong>在生产环境中，建议关闭PHP的display_errors选项，避免泄露系统信息。
                            </div>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 订单支付成功但状态未更新怎么办？</div>
                        <div class="faq-answer">
                            <p>订单支付成功但状态未更新可能有以下原因及解决方案：</p>
                            <ol>
                                <li><strong>支付回调问题</strong>：
                                    <ul>
                                        <li>检查支付平台的回调地址是否正确设置</li>
                                        <li>验证服务器是否能接收到支付平台的回调请求</li>
                                        <li>查看回调日志，确认是否有错误信息</li>
                                    </ul>
                                </li>
                                <li><strong>签名验证失败</strong>：
                                    <ul>
                                        <li>检查支付密钥是否正确</li>
                                        <li>验证签名算法是否与支付平台一致</li>
                                    </ul>
                                </li>
                                <li><strong>订单处理逻辑错误</strong>：
                                    <ul>
                                        <li>检查订单状态更新的代码逻辑</li>
                                        <li>添加日志记录订单处理过程</li>
                                    </ul>
                                </li>
                                <li><strong>数据库事务问题</strong>：
                                    <ul>
                                        <li>检查事务处理逻辑是否正确</li>
                                        <li>确认事务是否正常提交</li>
                                    </ul>
                                </li>
                            </ol>
                            <p>如果以上方法无法解决问题，您可以手动更新订单状态，并联系支付平台获取详细的支付记录。</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 用户无法登录系统怎么办？</div>
                        <div class="faq-answer">
                            <p>用户无法登录系统可能有以下原因及解决方案：</p>
                            <ol>
                                <li><strong>账号或密码错误</strong>：
                                    <ul>
                                        <li>确认用户输入的账号和密码是否正确</li>
                                        <li>尝试通过"忘记密码"功能重置密码</li>
                                    </ul>
                                </li>
                                <li><strong>账号被冻结</strong>：
                                    <ul>
                                        <li>检查用户账号是否被管理员冻结</li>
                                        <li>如果被冻结，需要管理员解冻账号</li>
                                    </ul>
                                </li>
                                <li><strong>会话问题</strong>：
                                    <ul>
                                        <li>清除浏览器缓存和Cookie</li>
                                        <li>检查PHP的session配置是否正确</li>
                                    </ul>
                                </li>
                                <li><strong>登录限制</strong>：
                                    <ul>
                                        <li>检查是否有登录次数限制或IP限制</li>
                                        <li>查看系统设置中的安全选项</li>
                                    </ul>
                                </li>
                                <li><strong>系统错误</strong>：
                                    <ul>
                                        <li>查看系统日志获取详细错误信息</li>
                                        <li>检查登录相关的代码逻辑</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Q: 系统运行缓慢怎么办？</div>
                        <div class="faq-answer">
                            <p>系统运行缓慢可能有以下原因及解决方案：</p>
                            <ol>
                                <li><strong>服务器资源不足</strong>：
                                    <ul>
                                        <li>检查服务器CPU、内存和磁盘使用情况</li>
                                        <li>考虑升级服务器配置或增加服务器数量</li>
                                    </ul>
                                </li>
                                <li><strong>数据库性能问题</strong>：
                                    <ul>
                                        <li>使用EXPLAIN分析慢查询</li>
                                        <li>为常用查询添加索引</li>
                                        <li>定期优化数据库表</li>
                                    </ul>
                                </li>
                                <li><strong>网络问题</strong>：
                                    <ul>
                                        <li>检查服务器网络连接是否稳定</li>
                                        <li>测试网络延迟和带宽</li>
                                    </ul>
                                </li>
                                <li><strong>代码优化问题</strong>：
                                    <ul>
                                        <li>检查是否有死循环或性能瓶颈</li>
                                        <li>优化频繁执行的代码</li>
                                        <li>使用缓存减少数据库查询</li>
                                    </ul>
                                </li>
                                <li><strong>并发访问过高</strong>：
                                    <ul>
                                        <li>使用负载均衡分散访问压力</li>
                                        <li>优化系统架构，提高并发处理能力</li>
                                    </ul>
                                </li>
                            </ol>
                            <div class="tip-box">
                                <strong>提示：</strong>建议使用性能监控工具（如New Relic、Zabbix等）实时监控系统性能，及时发现和解决性能问题。
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>
</div>
<script src="<?php echo $cdnpublic;?>jquery/2.1.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic;?>twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // 点击问题展开/收起答案
        $('.faq-question').click(function() {
            const answer = $(this).next('.faq-answer');
            answer.toggleClass('show');
            
            // 可以添加一些动画效果
            if (answer.hasClass('show')) {
                answer.slideDown();
            } else {
                answer.slideUp();
            }
        });
    });
</script>
</body>
</html>