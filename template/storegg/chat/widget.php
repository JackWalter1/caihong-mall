<?php
if(defined('CHAT_WIDGET_LOADED')) return;
define('CHAT_WIDGET_LOADED', 1);
// 加载配置文件
if(!isset($conf)){
    $conf = include '../../../../config.php';
    // 确保欢迎语有默认值
    if(empty($conf['chat_welcome'])){
        $conf['chat_welcome'] = '如果需要人工可以发送: 人工';
    }
}
// 检查客服系统是否开启，如果未开启则不显示
if(empty($conf['chat_enable']) || $conf['chat_enable'] != 1){
    return;
}
// 前台悬浮客服组件
?>
<!-- 引入Font Awesome图标库 -->
<link rel="stylesheet" href="//lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- 检测Font Awesome是否加载成功 -->
<script>
function checkFontAwesome() {
  const fontTest = document.createElement('span');
  fontTest.className = 'fa fa-check';
  fontTest.style.position = 'absolute';
  fontTest.style.left = '-9999px';
  document.body.appendChild(fontTest);
  
  // 检查图标是否显示（通过宽度判断）
  const width = fontTest.offsetWidth;
  document.body.removeChild(fontTest);
  
  if (width > 0) {
    console.log('Font Awesome加载成功');
  } else {
    console.log('Font Awesome加载失败');
    // 尝试添加备用图标库
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css';
    document.head.appendChild(link);
  }
}

// 页面加载后检查
window.onload = function() {
  checkFontAwesome();
};
</script>

<style>
#chat-float-btn {
    position: fixed;
    right: 30px;
    bottom: 30px;
    left: auto;
    top: auto;
    z-index: 99999 !important;
    background: <?php echo htmlspecialchars($conf['chat_btn_color'] ?? '#2196F3'); ?>;
    color: #fff;
    border-radius: 50px;
    padding: 0 20px;
    height: 50px;
    line-height: 50px;
    font-size: 16px;
    box-shadow: 0 4px 15px rgba(<?php echo hexdec(substr($conf['chat_btn_color'] ?? '#2196F3', 1, 2)); ?>, <?php echo hexdec(substr($conf['chat_btn_color'] ?? '#2196F3', 3, 2)); ?>, <?php echo hexdec(substr($conf['chat_btn_color'] ?? '#2196F3', 5, 2)); ?>, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
    border: none;
    outline: none;
}

/* 手机端只显示图标按钮 */
@media (max-width: 768px) {
    #chat-float-btn {
        right: 15px;
        bottom: 15px;
        z-index: 100;
        padding: 0;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        justify-content: center;
        touch-action: manipulation;
    }
    
    #chat-float-btn .chat-text {
        display: none;
    }
    
    #chat-float-btn .chat-icon {
        width: 28px;
        height: 28px;
    }
}

/* 超小屏幕优化 */
@media (max-width: 480px) {
    #chat-float-btn {
        right: 15px;
        bottom: 25px;
        width: 45px;
        height: 45px;
    }
    
    #chat-float-btn .chat-icon {
        width: 24px;
        height: 24px;
    }
    
    #chat-widget-box {
        width: 95%;
        height: 75vh;
        bottom: 70px;
    }
}

#chat-float-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(<?php echo hexdec(substr($conf['chat_btn_color'] ?? '#2196F3', 1, 2)); ?>, <?php echo hexdec(substr($conf['chat_btn_color'] ?? '#2196F3', 3, 2)); ?>, <?php echo hexdec(substr($conf['chat_btn_color'] ?? '#2196F3', 5, 2)); ?>, 0.6);
    background: <?php 
        $btnColor = $conf['chat_btn_color'] ?? '#2196F3';
        // 计算悬停颜色（稍微深一点）
        $r = max(0, hexdec(substr($btnColor, 1, 2)) - 20);
        $g = max(0, hexdec(substr($btnColor, 3, 2)) - 20);
        $b = max(0, hexdec(substr($btnColor, 5, 2)) - 20);
        echo '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    ?>;
}

#chat-float-btn:active {
    transform: translateY(0);
}

#chat-float-btn .chat-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

#chat-widget-box {
    display: none;
    position: fixed;
    right: 30px;
    bottom: 100px;
    width: 360px;
    height: 500px;
    background: <?php echo htmlspecialchars($conf['chat_window_color'] ?? '#FFFFFF'); ?>;
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    z-index: 10000;
    overflow: hidden;
    flex-direction: column;
    border: 1px solid #e1e5e9;
    animation: slideIn 0.3s ease;
}

/* 手机端窗口居中显示 */
@media (max-width: 768px) {
    #chat-widget-box {
        left: 50%;
        right: auto;
        transform: translateX(-50%);
        width: 90%;
        max-width: 400px;
        height: 70vh;
        max-height: 600px;
        bottom: 80px;
        margin: 0 auto;
    }
    
    #chat-widget-messages {
        min-height: 280px;
        max-height: 300px;
    }
    
    #chat-widget-input {
        padding: 12px;
    }
    
    #chat-widget-input input[type=text] {
        padding: 8px 12px;
        font-size: 14px;
    }
    
    #chat-widget-input button, #chat-widget-input label {
        background: #f0f0f0;
        color: #666;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    #chat-widget-input button:hover, #chat-widget-input label:hover {
        background: #e0e0e0;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    #chat-widget-input button:active, #chat-widget-input label:active {
        transform: translateY(0);
        box-shadow: 0 2px 3px rgba(0,0,0,0.1);
    }

    #chat-widget-input button {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }

    #chat-widget-input button:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    #chat-widget-input label {
        background: #e8f0fe;
        color: #2196f3;
    }

    #chat-widget-input label:hover {
        background: #d4e6fc;
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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
    margin-right: 12px;
    font-size: 20px;
    color: #2196f3;
}

#chat-widget-info {
    flex: 1;
}

#chat-widget-name {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 2px;
}

#chat-widget-subtitle {
    font-size: 12px;
    opacity: 0.9;
}

#chat-widget-controls {
    display: flex;
    gap: 10px;
}

#chat-widget-controls i {
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: background 0.3s ease;
}

#chat-widget-controls i:hover {
    background: rgba(255, 255, 255, 0.2);
}

#chat-widget-close {
    cursor: pointer;
    font-size: 22px;
    color: #fff;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.3s ease;
}

#chat-widget-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

#chat-widget-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #fff;
    min-height: 340px;
    max-height: 360px;
    scroll-behavior: smooth;
}

.chat-quick-questions {
    margin: 15px 0;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 10px;
}

.chat-quick-questions h4 {
    font-size: 14px;
    color: #333;
    margin-bottom: 10px;
    font-weight: bold;
}

.chat-quick-questions .question-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.chat-quick-questions .question-item {
    color: #2196f3;
    text-decoration: none;
    font-size: 13px;
    padding: 5px 0;
    cursor: pointer;
    transition: color 0.3s ease;
}

.chat-quick-questions .question-item:hover {
    color: #1976d2;
    text-decoration: underline;
}

.chat-quick-actions {
    display: flex;
    gap: 8px;
    margin: 15px 0;
    flex-wrap: wrap;
}

.chat-quick-actions .action-btn {
    background: #f0f0f0;
    border: none;
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

.chat-quick-actions .action-btn:hover {
    background: #e0e0e0;
    transform: translateY(-1px);
}

#chat-widget-messages::-webkit-scrollbar {
    width: 6px;
}

#chat-widget-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#chat-widget-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#chat-widget-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
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

#chat-widget-footer {
    padding: 8px 15px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    text-align: center;
    font-size: 11px;
    color: #6c757d;
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

#chat-widget-input button:hover, #chat-widget-input label:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

#chat-widget-input input[type=file] {
    display: none;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.chat-msg-bubble img {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.empty-chat {
    text-align: center;
    color: #6c757d;
    margin-top: 150px;
    font-size: 14px;
}

.empty-chat::before {
    content: "💬";
    font-size: 48px;
    display: block;
    margin-bottom: 10px;
    opacity: 0.5;
}
</style>

<div id="chat-float-btn">
    <div class="chat-icon"><i class="fa fa-user-circle"></i></div>
    <span class="chat-text">在线客服</span>
</div>

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
            <i class="fa fa-chevron-down" id="chat-widget-close" title="关闭"></i>
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
    
    <div id="chat-widget-footer">
    </div>
</div>

<script src="//lib.baomitu.com/jquery/2.1.4/jquery.min.js"></script>
<script>
var chatSessionId = null;
var chatPollingTimer = null;

function renderChatMessages(list) {
    var html = '';
    if(list.length === 0) {
        html = `<div class="chat-msg-admin">
            <span class="chat-msg-bubble">您好，有什么可以帮您？请直接输入问题，人工客服会尽快回复。</span>
            <span class="chat-msg-time">${new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})}</span>
        </div>`;
    } else {
        list.forEach(function(msg){
            var time = '<span class="chat-msg-time">'+msg.create_time+'</span>';
            if(msg.sender==='user'){
                html += '<div class="chat-msg-user">'+
                    time+
                    (msg.type==1?'<span class="chat-msg-bubble"><img src="'+msg.content+'" style="max-width:120px;max-height:120px;"></span>':'<span class="chat-msg-bubble">'+msg.content+'</span>')+
                    '</div>';
            }else{
                html += '<div class="chat-msg-admin">'+
                    (msg.type==1?'<span class="chat-msg-bubble"><img src="'+msg.content+'" style="max-width:120px;max-height:120px;"></span>':'<span class="chat-msg-bubble">'+msg.content+'</span>')+
                    time+'</div>';
            }
        });
    }
    $('#chat-widget-messages').html(html);
    $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);
}

function loadChatMessages(scrollBottom){
    $.get('/user/ajax_chat.php?act=get', function(res){
        if(res.code===0){
            chatSessionId = res.session_id;
            renderChatMessages(res.data);
            if(scrollBottom!==false) $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);
        }
    },'json');
}

function startChatPolling(){
    if(chatPollingTimer) clearInterval(chatPollingTimer);
    var interval = 3000;
    chatPollingTimer = setInterval(function(){
        loadChatMessages(false);
    }, interval);
}

$(function(){
    // 强制重置客服按钮位置
    $('#chat-float-btn').css({
        left: 'auto',
        top: 'auto',
        right: '30px',
        bottom: '30px',
        position: 'fixed'
    });
    // 隐藏客服窗口
    $('#chat-widget-box').hide();

    $('#chat-float-btn').click(function(){
        $('#chat-widget-box').css('display', 'flex').hide().fadeIn(300);
        loadChatMessages(true);
        startChatPolling();
        setTimeout(function() {
            $('#chat-widget-input [name=content]').focus();
        }, 350);
    });
    $('#chat-widget-close').click(function(){
        $('#chat-widget-box').fadeOut(300, function() {
            $(this).css('display', 'none');
        });
        if(chatPollingTimer) clearInterval(chatPollingTimer);
    });
    $(document).click(function(e) {
        if ($(e.target).closest('.fenzhan-jump').length) return;
        if (!$(e.target).closest('#chat-widget-box, #chat-float-btn').length) {
            $('#chat-widget-box').fadeOut(300);
            if(chatPollingTimer) clearInterval(chatPollingTimer);
        }
    });
    // 发送消息
    $('#chat-widget-input').submit(function(e){
        e.preventDefault();
        var content = $(this).find('[name=content]').val().trim();
        if(!content) return;
        var formData = new FormData(this);
        $.ajax({
            url: '/user/ajax_chat.php?act=send',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res){
                if(res.code===0){
                    loadChatMessages(true);
                    $('#chat-widget-input [name=content]').val('').focus();
                    $('#chat-widget-input [name=image]').val('');
                }else{
                    alert(res.msg);
                }
            },
            error: function() {
                alert('发送失败，请重试');
            }
        });
    });
    // 发送图片
    $('#chat-widget-upload').change(function(){
        if(this.files.length>0){
            // 显示上传中提示
            var loadingHtml = '<div class="chat-msg-user">\n                <span class="chat-msg-bubble">图片上传中...</span>\n                <span class="chat-msg-time">'+new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})+'</span>\n            </div>';
            $('#chat-widget-messages').append(loadingHtml);
            $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);

            var formData = new FormData($('#chat-widget-input')[0]);
            $.ajax({
                url: '/user/ajax_chat.php?act=send',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res){
                    if(res.code===0){
                        loadChatMessages(true);
                        $('#chat-widget-input [name=image]').val('');
                    }else{
                        // 替换上传中提示为失败提示
                        var errorHtml = '<div class="chat-msg-user">\n                            <span class="chat-msg-bubble" style="background:#ff5252;">图片上传失败: '+res.msg+'</span>\n                            <span class="chat-msg-time">'+new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})+'</span>\n                        </div>';
                        $('#chat-widget-messages').find('.chat-msg-bubble:contains("图片上传中...")').closest('.chat-msg-user').replaceWith(errorHtml);
                    }
                },
                error: function() {
                    // 替换上传中提示为失败提示
                    var errorHtml = '<div class="chat-msg-user">\n                        <span class="chat-msg-bubble" style="background:#ff5252;">图片上传失败，请重试</span>\n                        <span class="chat-msg-time">'+new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})+'</span>\n                    </div>';
                    $('#chat-widget-messages').find('.chat-msg-bubble:contains("图片上传中...")').closest('.chat-msg-user').replaceWith(errorHtml);
                }
            });
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
});
// 可拖动客服按钮
(function() {
    var dragging = false;
    var offsetX, offsetY;
    var startTime, startX, startY;
    var $btn = $('#chat-float-btn');
    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    $btn.on('mousedown touchstart', function(e) {
        startTime = new Date().getTime();
        var evt = e.type === 'touchstart' ? e.originalEvent.touches[0] : e;
        startX = evt.pageX;
        startY = evt.pageY;
        
        var pos = $btn.offset();
        offsetX = evt.pageX - pos.left;
        offsetY = evt.pageY - pos.top;
        
        if (isMobile) {
            setTimeout(function() {
                if (new Date().getTime() - startTime > 150) {
                    dragging = true;
                }
            }, 150);
        } else {
            dragging = true;
        }
        
        e.preventDefault();
        e.stopPropagation();
    });
    
    $(document).on('mousemove touchmove', function(e) {
        if (!dragging) return;
        var evt = e.type === 'touchmove' ? e.originalEvent.touches[0] : e;
        var moveDistance = Math.sqrt(Math.pow(evt.pageX - startX, 2) + Math.pow(evt.pageY - startY, 2));
        
        if (isMobile && moveDistance < 8) return;
        
        var x = evt.pageX - offsetX;
        var y = evt.pageY - offsetY;
        var maxX = $(window).width() - $btn.outerWidth();
        var maxY = $(window).height() - $btn.outerHeight();
        x = Math.max(0, Math.min(x, maxX));
        y = Math.max(0, Math.min(y, maxY));
        $btn.css({left: x, top: y, right: 'auto', bottom: 'auto', position: 'fixed'});
    });
    
    $(document).on('mouseup touchend', function(e) {
        var endTime = new Date().getTime();
        var evt = e.type === 'touchend' ? e.originalEvent.changedTouches[0] : e;
        var moveDistance = Math.sqrt(Math.pow(evt.pageX - startX, 2) + Math.pow(evt.pageY - startY, 2));
        
        if (endTime - startTime < 200 && moveDistance < 8) {
            // 直接触发点击事件处理函数
            $btn.click();
        }
        
        dragging = false;
    });
})();
</script>

<style>
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
</style>