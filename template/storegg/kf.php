<?php if(!defined('IN_CRONLITE')){exit('Access Denied');}
// 使用系统配置
$conf = array();
$conf['chat_open'] = 1;
$conf['chat_title'] = '在线客服';
$conf['chat_welcome'] = '您好，欢迎使用在线客服，有什么可以帮助您的？';
$conf['chat_btn_color'] = '#2196F3';
$conf['chat_window_color'] = '#FFFFFF';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($conf['chat_title'] ?? '在线客服'); ?></title>
    <link rel="stylesheet" href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
            color: #333;
            margin: 0 auto;
            padding: 0;
            max-width: 650px;
            overflow-x: hidden;
        }
        
        .chat-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .chat-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            flex: 1;
        }
        
        #chat-widget-box {
            display: flex;
            flex-direction: column;
            flex: 1;
            background: <?php echo htmlspecialchars($conf['chat_window_color'] ?? '#FFFFFF'); ?>;
            border: none;
        }
        
        #chat-widget-header {
            background: <?php echo htmlspecialchars($conf['chat_btn_color'] ?? '#2196F3'); ?>;
            color: #fff;
            padding: 15px 20px;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(<?php echo hexdec(substr($conf['chat_btn_color'] ?? '#2196F3', 1, 2)); ?>, <?php echo hexdec(substr($conf['chat_btn_color'] ?? '#2196F3', 3, 2)); ?>, <?php echo hexdec(substr($conf['chat_btn_color'] ?? '#2196F3', 5, 2)); ?>, 0.3);
            flex-shrink: 0;
        }
        
        #chat-widget-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        
        #chat-widget-info {
            flex: 1;
        }
        
        #chat-widget-name {
            font-weight: bold;
            font-size: 16px;
        }
        
        #chat-widget-subtitle {
            font-size: 12px;
            opacity: 0.9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        #chat-widget-controls {
            display: flex;
            gap: 10px;
        }
        
        #chat-widget-controls i {
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            border-radius: 50%;
            transition: background 0.3s ease;
        }
        
        #chat-widget-controls i:hover {
            background: rgba(255,255,255,0.2);
        }
        
        #chat-widget-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #f8f9fa;
            min-height: 300px;
        }
        
        .chat-msg-user {
            text-align: right;
            margin-bottom: 12px;
        }
        
        .chat-msg-admin {
            text-align: left;
            margin-bottom: 12px;
        }
        
        .chat-msg-bubble {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 20px;
            color: #fff;
            max-width: 75%;
            word-break: break-all;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
            position: relative;
        }
        
        .chat-msg-admin .chat-msg-bubble {
            background: #fff;
            color: #333;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }
        
        .chat-msg-user .chat-msg-bubble {
            background: #2196f3;
            color: #fff;
            border-radius: 15px;
        }
        
        .chat-msg-time {
            font-size: 11px;
            color: #6c757d;
            margin: 0 6px;
            display: inline-block;
            vertical-align: middle;
            opacity: 0.8;
        }
        
        #chat-widget-input {
            padding: 15px;
            border-top: 1px solid #e9ecef;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        
        #chat-widget-input input[type=text] {
            flex: 1;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            outline: none;
            background: #f8f9fa;
        }
        
        #chat-widget-input input[type=text]:focus {
            border-color: #2196f3;
            background: #fff;
        }
        
        #chat-widget-input button, #chat-widget-input label {
            background: #f0f0f0;
            color: #666;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        #chat-widget-input button:hover, #chat-widget-input label:hover {
            background: #e0e0e0;
            transform: scale(1.05);
        }
        
        #chat-widget-input button:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        #chat-widget-input input[type=file] {
            display: none;
        }
        
        .chat-quick-questions {
            margin: 15px 0;
            padding: 10px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
        
        .chat-quick-questions h4 {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 8px;
        }
        
        .question-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .question-item {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 6px 12px;
            font-size: 12px;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .question-item:hover {
            background: #e9ecef;
            border-color: #dee2e6;
        }
        
        /* 简化的快捷操作按钮 */
        .chat-quick-actions {
            margin: 15px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .action-btn {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 12px;
            color: #333;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 80px;
            text-align: center;
        }
        
        .action-btn:hover {
            background: #e0e0e0;
            transform: translateY(-1px);
        }
        
        /* 确保按钮图标可见的样式 */
        #chat-widget-input label i,
        #chat-widget-input button i {
          font-family: 'FontAwesome' !important;
          font-style: normal;
          display: inline-block;
          text-rendering: auto;
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
        }
        
        /* 导航栏样式 */
        .fui-navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
            max-width: 650px;
            margin: 0 auto;
        }
        
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #666;
            text-decoration: none;
            font-size: 12px;
        }
        
        .nav-item.active {
            color: #2196f3;
        }
        
        .nav-item .icon {
            font-size: 18px;
            margin-bottom: 2px;
        }
        
        /* 响应式设计 */
        @media (max-width: 768px) {
            .chat-container {
                width: 100%;
                height: 100vh;
                border-radius: 0;
                box-shadow: none;
            }
            
            #chat-widget-messages {
                min-height: 300px;
            }
            
            #chat-widget-input {
                padding: 12px;
            }
        }
        
        /* 确保输入框不被底部导航栏遮挡 */
        .chat-wrapper {
            padding-bottom: 60px;
        }
        
        /* 移动端适配优化 */
        @media (max-width: 768px) {
            #chat-widget-input {
                position: relative;
                z-index: 999;
                background-color: white;
            }
            
            .chat-wrapper {
                padding-bottom: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="chat-wrapper">
        <div class="chat-container">
            <div id="chat-widget-box">
                <div id="chat-widget-header">
                    <div id="chat-widget-avatar">
                        <i class="fa fa-user"></i>
                    </div>
                    <div id="chat-widget-info">
                        <div id="chat-widget-name"><?php echo htmlspecialchars($conf['chat_title'] ?? '在线客服'); ?></div>
                        <div id="chat-widget-subtitle"><?php echo htmlspecialchars($conf['chat_welcome']); ?></div>
                    </div>
                    <div id="chat-widget-controls">
                        <i class="fa fa-volume-up" title="静音"></i>
                    </div>
                </div>
                
                <div id="chat-widget-messages">
                    <!-- 欢迎消息 -->
                    <div class="chat-msg-admin">
                        <span class="chat-msg-bubble"><?php echo htmlspecialchars($conf['chat_welcome']); ?></span>
                        <span class="chat-msg-time"><?php echo date('m-d H:i'); ?></span>
                    </div>
                    
                    <!-- 快捷问题 -->
                    <div class="chat-quick-questions">
                        <h4>猜你想问:</h4>
                        <div class="question-list">
                            <div class="question-item" data-question="下单后多久到账?">下单后多久到账?</div>
                            <div class="question-item" data-question="订单号怎么查看?">订单号怎么查看?</div>
                            <div class="question-item" data-question="如何拥有更便宜的商品价格?">如何拥有更便宜的商品价格?</div>
                            <div class="question-item" data-question="怎么加盟网站赚钱?">怎么加盟网站赚钱?</div>
                            <div class="question-item" data-question="人工客服">人工客服</div>
                            <div class="question-item" data-question="不想等了,如何退款?">不想等了,如何退款?</div>
                        </div>
                    </div>
                    
                    <div class="chat-msg-admin">
                        <span class="chat-msg-bubble">请您直接提供订单号哦,提供订单号+问题优先回复</span>
                        <span class="chat-msg-time"><?php echo date('m-d H:i'); ?></span>
                    </div>
                    
                    <!-- 快捷操作按钮 -->
                    <div class="chat-quick-actions">
                        <button class="action-btn" data-action="如何查询订单">如何查询订单</button>
                        <button class="action-btn" data-action="人工客服">人工客服</button>
                        <button class="action-btn" data-action="到账时间">到账时间</button>
                        <button class="action-btn" data-action="申请退款">申请退款</button>
                    </div>
                </div>
                
                <form id="chat-widget-input" enctype="multipart/form-data" autocomplete="off">
                    <input type="text" name="content" placeholder="请输入" autocomplete="off" />
                    <label for="chat-widget-upload" title="发送图片" style="background:#e0e0e0;">
                        <i class="fa fa-image"></i>
                    </label>
                    <input type="file" id="chat-widget-upload" name="image" accept="image/*">
                    <button type="submit" title="发送" style="background:#2196f3;color:#fff;"><i class="fa fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- 底部导航栏 -->
    <div class="fui-navbar">
        <a href="./" class="nav-item">
            <span class="icon icon-home"><i class="fa fa-home"></i></span>
            <span class="label">首页</span>
        </a>
        <a href="./?mod=query" class="nav-item">
            <span class="icon icon-dingdan1"><i class="fa fa-list-alt"></i></span>
            <span class="label">订单</span>
        </a>
        <a href="./?mod=cart" class="nav-item" <?php if($conf['shoppingcart']==0){?>style="display:none"<?php }?>>
            <span class="icon icon-cart2"><i class="fa fa-shopping-cart"></i></span>
            <span class="label">购物车</span>
        </a>
        <a href="./?mod=kf" class="nav-item active">
            <span class="icon icon-service1"><i class="fa fa-comments"></i></span>
            <span class="label">客服</span>
        </a>
        <a href="./user/" class="nav-item">
            <span class="icon icon-person2"><i class="fa fa-user"></i></span>
            <span class="label">会员中心</span>
        </a>
    </div>

    <!-- 使用本地资源 -->
    <script src="../../assets/Agod/jquery-3.6.0.min.js"></script>
    <script src="../../assets/Agod/layer.js"></script>
    <script>
    var chatSessionId = null;
    var chatPollingTimer = null;
    var lastMessageId = 0;
    
    // 初始化会话
    function initChatSession() {
        // 检查是否有已存在的会话ID存储在cookie中
        var savedSessionId = getCookie('chat_session_id');
        
        if (savedSessionId) {
            // 验证会话是否有效
            checkSessionValid(savedSessionId);
        } else {
            // 创建新会话
            createNewSession();
        }
    }
    
    // 检查会话是否有效
    function checkSessionValid(sessionId) {
        // 修正相对路径
        $.get('../../admin/ajax.php?act=chat_message_list&session_id=' + sessionId, function(res) {
            if (res.code === 0) {
                chatSessionId = sessionId;
                loadMessages();
                startPolling();
            } else {
                createNewSession();
            }
        }, 'json').fail(function() {
            createNewSession();
        });
    }
    
    // 创建新会话
    function createNewSession() {
        $.ajax({
            // 修正相对路径
            url: '../../admin/ajax.php?act=create_chat_session',
            type: 'POST',
            data: {
                // 不传递user_ip，让后端自己获取
            },
            dataType: 'json',
            success: function(res) {
                if (res.code === 1) {
                    chatSessionId = res.data.session_id;
                    // 将会话ID保存到cookie，有效期24小时
                    setCookie('chat_session_id', chatSessionId, 24);
                    
                    // 初始化欢迎消息
                    initWelcomeMessage();
                    
                    // 开始轮询
                    startPolling();
                } else {
                    console.error('创建会话失败：', res.msg);
                    // 如果创建失败，使用模拟数据
                    initWithMockData();
                }
            },
            error: function(xhr, status, error) {
                console.error('创建会话请求失败:', status, error);
                console.log('请求URL:', this.url);
                console.log('响应状态:', xhr.status);
                console.log('响应文本:', xhr.responseText);
                // 请求失败，使用模拟数据
                initWithMockData();
            }
        });
    }
    
    // 初始化欢迎消息
    function initWelcomeMessage() {
        var welcomeMsg = {
            sender: 'admin',
            type: 0,
            content: '您好，欢迎使用在线客服，有什么可以帮助您的？',
            create_time: new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})
        };
        
        var html = '<div class="chat-msg-admin">'+
            '<span class="chat-msg-bubble">'+welcomeMsg.content+'</span>'+
            '<span class="chat-msg-time">'+welcomeMsg.create_time+'</span>'+
            '</div>';
        
        $('#chat-widget-messages').html(html);
        $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);
    }
    
    // 加载消息
    function loadMessages() {
        if (!chatSessionId) return;
        
        // 修正相对路径
        $.get('../../admin/ajax.php?act=chat_message_list&session_id=' + chatSessionId, function(res) {
            if (res.code === 0) {
                renderChatMessages(res.data);
                if (res.data.length > 0) {
                    lastMessageId = res.data[res.data.length - 1].id;
                }
            }
        }, 'json').fail(function() {
            console.error('加载消息失败');
        });
    }
    
    // 渲染聊天消息
    function renderChatMessages(list) {
        var html = '';
        if(list.length === 0) {
            initWelcomeMessage();
            return;
        }
        
        list.forEach(function(msg){
            var time = '<span class="chat-msg-time">'+msg.create_time+'</span>';
            if(msg.sender==='user'){
                html += '<div class="chat-msg-user">'+time+
                    (msg.type==1?'<span class="chat-msg-bubble"><img src="'+msg.content+'" style="max-width:120px;max-height:120px;"></span>':'<span class="chat-msg-bubble">'+msg.content+'</span>')+
                    '</div>';
            }else{
                html += '<div class="chat-msg-admin">'+
                    (msg.type==1?'<span class="chat-msg-bubble"><img src="'+msg.content+'" style="max-width:120px;max-height:120px;"></span>':'<span class="chat-msg-bubble">'+msg.content+'</span>')+
                    time+'</div>';
            }
        });
        
        $('#chat-widget-messages').html(html);
        $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);
    }
    
    // 发送消息
    function sendMessage(content, type) {
        if (!chatSessionId || !content) return;
        
        var formData = new FormData();
        formData.append('session_id', chatSessionId);
        formData.append('content', content);
        formData.append('sender', 'user');  // 明确指定发送者为用户
        if (type !== undefined) {
            formData.append('type', type);
        }
        
        $.ajax({
            // 修正相对路径
            url: '../../admin/ajax.php?act=chat_send_message',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if(res.code !== 0) {
                    alert('消息发送失败：' + (res.msg || '未知错误'));
                }
            },
            error: function() {
                alert('消息发送失败，请检查网络连接');
            }
        });
    }
    
    // 发送图片消息
    function sendImage(file) {
        if (!chatSessionId || !file) return;
        
        var formData = new FormData();
        formData.append('session_id', chatSessionId);
        formData.append('image', file);
        formData.append('sender', 'user');  // 明确指定发送者为用户
        
        $.ajax({
            url: '../admin/ajax.php?act=chat_send_message',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if(res.code === 0) {
                    // 确保有res.data和res.data.image_url
                    if(res.data && res.data.image_url) {
                        // 显示用户发送的图片
                        var imgHtml = '<div class="chat-msg-user">'+
                            '<span class="chat-msg-time">'+new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})+'</span>'+
                            '<span class="chat-msg-bubble"><img src="'+res.data.image_url+'" style="max-width:120px;max-height:120px;"></span>'+
                            '</div>';
                        $('#chat-widget-messages').append(imgHtml);
                        $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);
                    }
                } else {
                    alert('图片发送失败：' + (res.msg || '未知错误'));
                }
            },
            error: function() {
                alert('图片发送失败，请检查网络连接');
            }
        });
    }
    
    // 开始轮询接收新消息
    function startPolling() {
        if (chatPollingTimer) clearInterval(chatPollingTimer);
        
        // 每3秒轮询一次
        chatPollingTimer = setInterval(function() {
            if (!chatSessionId) return;
            
            // 修正相对路径
            $.get('../../admin/ajax.php?act=chat_message_list&session_id=' + chatSessionId, function(res) {
                if (res.code === 0 && res.data.length > 0) {
                    var newMessages = res.data.filter(function(msg) {
                        return msg.id > lastMessageId && msg.sender === 'admin';
                    });
                    
                    if (newMessages.length > 0) {
                        // 添加新消息到界面
                        newMessages.forEach(function(msg){
                            var time = '<span class="chat-msg-time">'+msg.create_time+'</span>';
                            var msgHtml = '<div class="chat-msg-admin">'+
                                (msg.type==1?'<span class="chat-msg-bubble"><img src="'+msg.content+'" style="max-width:120px;max-height:120px;"></span>':'<span class="chat-msg-bubble">'+msg.content+'</span>')+
                                time+'</div>';
                            $('#chat-widget-messages').append(msgHtml);
                        });
                        
                        $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);
                        lastMessageId = res.data[res.data.length - 1].id;
                    }
                }
            }, 'json');
        }, 3000);
    }
    
    // 使用模拟数据初始化（当无法连接到后端时）
    function initWithMockData() {
        console.log('使用模拟数据初始化聊天');
        var mockHtml = '<div class="chat-msg-admin">'+
            '<span class="chat-msg-bubble">您好，欢迎使用在线客服，有什么可以帮助您的？</span>'+
            '<span class="chat-msg-time">'+new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})+'</span>'+
            '</div>';
        
        $('#chat-widget-messages').html(mockHtml);
        
        // 添加一个提示，说明当前使用模拟模式
        var tipsHtml = '<div class="text-center text-muted text-xs" style="margin-top:10px;">当前为离线模式，消息不会发送到客服</div>';
        $('#chat-widget-messages').append(tipsHtml);
    }
    
    // Cookie操作辅助函数
    function setCookie(name, value, hours) {
        var expires = '';
        if (hours) {
            var date = new Date();
            date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + (value || '')  + expires + '; path=/';
    }
    
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    
    $(function(){
        // 初始化聊天会话
        initChatSession();
        
        // 聚焦到输入框
        setTimeout(function() {
            $('#chat-widget-input [name=content]').focus();
        }, 350);
        
        // 发送消息
        $('#chat-widget-input').submit(function(e){
            e.preventDefault();
            var content = $(this).find('[name=content]').val().trim();
            if(!content) return;
              
            // 显示用户消息
            var userMsg = {
                sender: 'user',
                type: 0,
                content: content,
                create_time: new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})
            };
              
            var userHtml = '<div class="chat-msg-user">'+
                '<span class="chat-msg-time">'+userMsg.create_time+'</span>'+
                '<span class="chat-msg-bubble">'+userMsg.content+'</span>'+
                '</div>';
            $('#chat-widget-messages').append(userHtml);
            $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);
              
            // 清空输入框
            $('#chat-widget-input [name=content]').val('').focus();
              
            // 发送消息到后端
            if (chatSessionId) {
                sendMessage(content);
            } else {
                // 模拟延迟回复
                setTimeout(function(){
                    var adminMsg = {
                        sender: 'admin',
                        type: 0,
                        content: '感谢您的咨询，我们的人工客服正在处理您的问题，请稍候...',
                        create_time: new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})
                    };
                      
                    var adminHtml = '<div class="chat-msg-admin">'+
                        '<span class="chat-msg-bubble">'+adminMsg.content+'</span>'+
                        '<span class="chat-msg-time">'+adminMsg.create_time+'</span>'+
                        '</div>';
                    $('#chat-widget-messages').append(adminHtml);
                    $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);
                }, 1500);
            }
        });
        
        // 图片上传处理
        $('#chat-widget-upload').change(function(){
            if(this.files.length>0){
                if (chatSessionId) {
                    // 创建一个预览
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var imgHtml = '<div class="chat-msg-user">'+
                            '<span class="chat-msg-time">'+new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})+'</span>'+
                            '<span class="chat-msg-bubble"><img src="'+e.target.result+'" style="max-width:120px;max-height:120px;"></span>'+
                            '</div>';
                        $('#chat-widget-messages').append(imgHtml);
                        $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);
                        
                        // 发送图片到服务器
                        sendImage(this.files[0]);
                    }.bind(this);
                    reader.readAsDataURL(this.files[0]);
                } else {
                    alert('当前为离线模式，无法发送图片');
                }
                $(this).val('');
            }
        });
        
        // 回车发送
        $('#chat-widget-input [name=content]').keypress(function(e) {
            if(e.which == 13 && !e.shiftKey) {
                e.preventDefault();
                $('#chat-widget-input').submit();
            }
        });
        
        // 快捷问题点击，直接填充输入框
        $(document).on('click', '.question-item', function() {
            var question = $(this).data('question');
            $('#chat-widget-input [name=content]').val(question).focus();
        });
        
        // 快捷操作按钮点击，直接填充输入框
        $(document).on('click', '.action-btn', function() {
            var action = $(this).data('action');
            $('#chat-widget-input [name=content]').val(action).focus();
        });
        
        // 静音按钮
        $('#chat-widget-controls .fa-volume-up').click(function() {
            $(this).toggleClass('fa-volume-up fa-volume-off');
        });
        
        // 添加模拟消息函数
        function getMockChatMessages() {
            return [];
        }
        
        // 初始渲染模拟消息
        renderChatMessages(getMockChatMessages());
    });
    </script>
</body>
</html>