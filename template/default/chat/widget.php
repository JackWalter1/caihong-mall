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
// 前台联系方式列表（来源：后台联系方式管理）
if(!isset($contactlist)){
    $contactlist = array();
    if(isset($DB)){
        $contact_rs = @$DB->query("SELECT * FROM pre_contact ORDER BY id ASC");
        if($contact_rs !== false){
            while($contact_row = $contact_rs->fetch()){
                $contactlist[] = $contact_row;
            }
        }
    }
}
if(!empty($contactlist)){
?>
<link rel="stylesheet" href="//lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
#contact-float-btn {
    position: fixed;
    right: 30px;
    bottom: 95px;
    z-index: 99999 !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-radius: 50px;
    padding: 0 20px;
    height: 50px;
    line-height: 50px;
    font-size: 16px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 8px;
    border: none;
    outline: none;
}
@media (max-width: 768px) {
    #contact-float-btn {
        right: 15px;
        bottom: 80px;
        padding: 0;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        justify-content: center;
    }
    #contact-float-btn .contact-text {
        display: none;
    }
    #contact-float-btn .contact-icon {
        width: 28px;
        height: 28px;
    }
}
#contact-float-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}
#contact-float-btn .contact-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
#contact-float-box {
    display: none;
    position: fixed;
    right: 30px;
    bottom: 160px;
    width: 320px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    z-index: 10000;
    overflow: hidden;
    border: 1px solid #e1e5e9;
    animation: contactSlideIn 0.3s ease;
}
@media (max-width: 768px) {
    #contact-float-box {
        right: 15px;
        left: 15px;
        width: auto;
        bottom: 145px;
    }
}
@keyframes contactSlideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
#contact-float-box .contact-box-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 15px 20px;
    font-size: 16px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
#contact-float-box .contact-box-header .contact-close {
    cursor: pointer;
    font-size: 22px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.3s ease;
}
#contact-float-box .contact-box-header .contact-close:hover {
    background: rgba(255,255,255,0.2);
}
#contact-float-box .contact-box-body {
    padding: 15px;
    max-height: 400px;
    overflow-y: auto;
}
#contact-float-box .contact-box-body::-webkit-scrollbar {
    width: 6px;
}
#contact-float-box .contact-box-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}
.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 10px;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s ease;
}
.contact-item:last-child {
    border-bottom: none;
}
.contact-item:hover {
    background: #f8f9fa;
}
.contact-item .contact-item-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #eef1ff;
    color: #667eea;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.contact-item .contact-item-info {
    flex: 1;
    min-width: 0;
}
.contact-item .contact-item-type {
    font-size: 13px;
    color: #888;
    margin-bottom: 2px;
}
.contact-item .contact-item-account {
    font-size: 15px;
    color: #333;
    font-weight: bold;
    word-break: break-all;
}
.contact-item .contact-item-link {
    color: #2196f3;
    text-decoration: none;
}
.contact-item .contact-item-link:hover {
    text-decoration: underline;
    color: #1976d2;
}
.contact-item .contact-item-qrcode {
    border: 1px solid #e1e5e9;
}
.contact-item .contact-item-remark {
    font-size: 12px;
    color: #999;
    margin-top: 2px;
}
</style>

<div id="contact-float-btn" onclick="toggleContactBox(event)">
    <div class="contact-icon"><i class="fa fa-phone"></i></div>
    <span class="contact-text">联系方式</span>
</div>

<div id="contact-float-box">
    <div class="contact-box-header">
        <span>联系我们</span>
        <span class="contact-close" onclick="toggleContactBox(event)">&times;</span>
    </div>
    <div class="contact-box-body">
        <?php foreach($contactlist as $contact_row){
            $ctype = htmlspecialchars($contact_row['type']);
            $caccount = htmlspecialchars($contact_row['account']);
            $curl = htmlspecialchars($contact_row['url']);
            $cqrcode = htmlspecialchars($contact_row['qrcode']);
            $cremark = htmlspecialchars($contact_row['remark']);
            $cicon = 'fa-user';
            if(strpos($contact_row['type'],'QQ')!==false || strpos($contact_row['type'],'qq')!==false){
                $cicon = 'fa-qq';
            }elseif(strpos($contact_row['type'],'微信')!==false || strpos($contact_row['type'],'wx')!==false){
                $cicon = 'fa-weixin';
            }elseif(strpos($contact_row['type'],'手机')!==false || strpos($contact_row['type'],'电话')!==false){
                $cicon = 'fa-phone';
            }elseif(strpos($contact_row['type'],'邮箱')!==false || strpos($contact_row['type'],'mail')!==false){
                $cicon = 'fa-envelope';
            }elseif(strpos($contact_row['type'],'Telegram')!==false || strpos($contact_row['type'],'tg')!==false){
                $cicon = 'fa-paper-plane';
            }
            $caccount_html = !empty($curl) ? '<a class="contact-item-account contact-item-link" href="'.$curl.'" target="_blank" rel="noreferrer">'.$caccount.'</a>' : '<div class="contact-item-account">'.$caccount.'</div>';
        ?>
        <div class="contact-item">
            <div class="contact-item-icon"><i class="fa <?php echo $cicon;?>"></i></div>
            <div class="contact-item-info">
                <div class="contact-item-type"><?php echo $ctype;?></div>
                <?php echo $caccount_html;?>
                <?php if(!empty($cqrcode)){?>
                <img class="contact-item-qrcode" src="<?php echo $cqrcode;?>" alt="二维码" onclick="openContactQrcode(this)" style="max-width:90px;max-height:90px;margin-top:6px;cursor:zoom-in;border-radius:6px;">
                <?php }?>
                <?php if(!empty($cremark)){?><div class="contact-item-remark"><?php echo $cremark;?></div><?php }?>
            </div>
        </div>
        <?php }?>
    </div>
</div>
<div id="contact-qrcode-modal" onclick="this.style.display='none'" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.85);z-index:999999;text-align:center;padding:30px;">
    <img id="contact-qrcode-modal-img" src="" alt="二维码" style="max-width:90%;max-height:90%;margin-top:3%;border-radius:8px;">
</div>
<script>
function openContactQrcode(img) {
    var modal = document.getElementById('contact-qrcode-modal');
    var modalImg = document.getElementById('contact-qrcode-modal-img');
    if (!modal || !modalImg) return;
    modalImg.src = img.src;
    modal.style.display = 'block';
}
</script>
<script>
function toggleContactBox(e) {
    if (e) {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
    }
    var box = document.getElementById('contact-float-box');
    if (!box) return;
    var cur = box.style.display;
    if (cur === 'none' || (cur === '' && getComputedStyle(box).display === 'none')) {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}
document.addEventListener('click', function(e) {
    var box = document.getElementById('contact-float-box');
    var btn = document.getElementById('contact-float-btn');
    if (!box) return;
    if (e.target && (box.contains(e.target) || (btn && btn.contains(e.target)))) return;
    box.style.display = 'none';
});
</script>
<?php
}
// 检查客服系统是否开启，如果未开启则不显示
if(empty($conf['chat_enable']) || $conf['chat_enable'] != 1){
    return;
}
// 获取客服系统基础路径
$chat_base_path = isset($conf['chat_base_path']) ? trim($conf['chat_base_path']) : '';
// 确保路径格式正确
if($chat_base_path && substr($chat_base_path, 0, 1) != '/'){
    $chat_base_path = '/' . $chat_base_path;
}
if($chat_base_path && substr($chat_base_path, -1) == '/'){
    $chat_base_path = substr($chat_base_path, 0, -1);
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

// 页面加载后检查（用 addEventListener 避免覆盖页面其它 onload）
if (window.addEventListener) {
    window.addEventListener('load', checkFontAwesome);
} else if (window.attachEvent) {
    window.attachEvent('onload', checkFontAwesome);
} else {
    window.onload = checkFontAwesome;
}
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
        width: 50%;
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
    position: relative; /* 为订单选择器提供相对定位容器 */
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
    min-width: 100px;
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
    flex-shrink: 0;
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

/* 订单选择下拉框样式 */
#chat-widget-order {
    display: none;
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    margin: 0 10px 10px 10px;
    padding: 8px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
    z-index: 10001;
    max-height: 200px;
    overflow-y: auto;
    font-size: 14px;
    width: calc(100% - 20px); /* 减去左右边距 */
}

#chat-widget-order option {
    padding: 8px 12px;
    border-bottom: 1px solid #f5f5f5;
    background: #fff;
}

#chat-widget-order option:last-child {
    border-bottom: none;
}

#chat-widget-order option:hover {
    background-color: #f8f9fa;
}

#chat-widget-order::-webkit-scrollbar {
    width: 4px;
}

#chat-widget-order::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

#chat-widget-order::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
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

/* 图片放大模态框样式 */
#chat-image-modal {
    display: none;
    position: fixed;
    z-index: 999999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    overflow: auto;
}

#chat-image-modal-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90vh;
    animation: zoom 0.3s ease;
}

@keyframes zoom {
    from {transform: scale(0); opacity: 0;}
    to {transform: scale(1); opacity: 1;}
}

#chat-image-modal-close {
    position: absolute;
    top: 20px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
    cursor: pointer;
}

#chat-image-modal-close:hover,
#chat-image-modal-close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* 视频播放模态框样式 */
#chat-video-modal {
    display: none;
    position: fixed;
    z-index: 999999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    overflow: auto;
}

#chat-video-modal-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90vh;
    animation: zoom 0.3s ease;
}

#chat-video-modal-close {
    position: absolute;
    top: 20px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
    cursor: pointer;
}

#chat-video-modal-close:hover,
#chat-video-modal-close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* 手机端适配 */
@media only screen and (max-width: 700px) {
    #chat-image-modal-content {
        width: 100%;
    }
    #chat-video-modal-content {
        width: 100%;
    }
}
</style>

<div id="chat-float-btn" onclick="aurChatToggle(event)">
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
            <i class="fa fa-chevron-down" id="chat-widget-close" title="关闭" onclick="aurChatClose(event)"></i>
        </div>
    </div>
    
    <div id="chat-widget-messages">
        <!-- 静态欢迎消息和快捷选项 -->
        <div id="chat-widget-static-content">
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
        
        <!-- 动态消息容器 -->
        <div id="chat-widget-dynamic-messages"></div>
    </div>
    
    <form id="chat-widget-input" enctype="multipart/form-data" autocomplete="off">
        <input type="text" name="content" placeholder="请输入" autocomplete="off">
        <label for="chat-widget-order" title="选择订单" style="background:#e0e0e0;">
            <i class="fa fa-shopping-cart"></i>
        </label>
        <select id="chat-widget-order" name="order_id">
            <option value="">选择订单</option>
        </select>
        <label for="chat-widget-upload" title="发送图片" style="background:#e0e0e0;">
            <i class="fa fa-image"></i>
        </label>
        <input type="file" id="chat-widget-upload" name="image" accept="image/*">
        <?php if(isset($conf['chat_video_enable']) && $conf['chat_video_enable'] == 1): ?>
        <label for="chat-widget-upload-video" title="发送视频" style="background:#e0e0e0;">
            <i class="fa fa-video-camera"></i>
        </label>
        <input type="file" id="chat-widget-upload-video" name="video" accept="video/mp4">
        <?php endif; ?>
        <button type="submit" title="发送" style="background:#2196f3;color:#fff;"><i class="fa fa-paper-plane"></i></button>
    </form>
    
    <div id="chat-widget-footer">
    </div>
    <!-- 隐藏的音频元素，用于播放回复提示音 -->
    <audio id="chat-notification-audio" preload="auto" style="display:none;">
        <source src="<?php echo $chat_base_path;?>/template/default/chat/kefu.wav" type="audio/wav">
        您的浏览器不支持音频播放功能
     </audio>
</div>

<!-- 图片放大模态框 -->
<div id="chat-image-modal">
    <span id="chat-image-modal-close">&times;</span>
    <img class="modal-content" id="chat-image-modal-content">
</div>

<!-- 视频播放模态框 -->
<div id="chat-video-modal">
    <span id="chat-video-modal-close">&times;</span>
    <video class="modal-content" id="chat-video-modal-content" controls></video>
</div>

<script>
var chatSessionId = null;
var chatPollingTimer = null;
var chatBasePath = '<?php echo $chat_base_path;?>';

// 客服按钮点击：原生兜底（不依赖 jQuery ready 时机与拖动判定，防重锁）
var _aurChatLock = 0;
function aurChatToggle(e) {
    if (e) {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
    }
    var now = new Date().getTime();
    if (now - _aurChatLock < 250) return; // 防重锁
    _aurChatLock = now;
    var box = document.getElementById('chat-widget-box');
    if (!box) return;
    var cur = box.style.display;
    if (cur === 'none' || (cur === '' && getComputedStyle(box).display === 'none')) {
        box.style.display = 'flex';
        if (window.jQuery) {
            try { loadChatMessages(true); startChatPolling(); } catch (err) {}
        }
        setTimeout(function() {
            var inp = box.querySelector('#chat-widget-input [name=content]');
            if (inp) inp.focus();
        }, 350);
    } else {
        box.style.display = 'none';
        if (chatPollingTimer) { clearInterval(chatPollingTimer); chatPollingTimer = null; }
    }
}

// 关闭客服窗口（原生兜底，防重锁）
function aurChatClose(e) {
    if (e) {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
    }
    var now = new Date().getTime();
    if (now - _aurChatLock < 250) return; // 防重锁
    _aurChatLock = now;
    var box = document.getElementById('chat-widget-box');
    if (box) box.style.display = 'none';
    if (chatPollingTimer) { clearInterval(chatPollingTimer); chatPollingTimer = null; }
}

var lastMessageCount = 0; // 记录上次消息数量
var lastMessageHash = ''; // 记录上次消息列表哈希，用于检测消息变化

function renderChatMessages(list) {
    // 计算消息列表的哈希值，用于检测消息是否有变化
    var messageHash = '';
    if(list.length > 0) {
        list.forEach(function(msg) {
            messageHash += msg.id + '_' + msg.type + '_' + msg.content + '|';
        });
    }
    
    // 检查消息是否有变化
    if(lastMessageHash === messageHash) {
        // 消息没有变化，不更新DOM，直接返回
        return;
    }
    
    // 更新消息列表哈希
    lastMessageHash = messageHash;
    
    var html = '';
    if(list.length > 0) {
        list.forEach(function(msg){
            var time = '<span class="chat-msg-time">'+msg.create_time+'</span>';
            var content = msg.content;
            
            // 将换行符转换为HTML换行标签，确保订单信息格式正确
            if(msg.type === 0) {
                content = content.replace(/\n/g, '<br>');
            }
            
            if(msg.sender==='user'){
                html += '<div class="chat-msg-user">'+time+
                    (msg.type==1?'<span class="chat-msg-bubble"><img src="'+content+'" style="max-width:120px;max-height:120px;"></span>':(msg.type==2?'<span class="chat-msg-bubble"><video src="'+content+'" style="max-width:200px;max-height:150px;" controls onclick="openVideoModal(this.src)"></video></span>':'<span class="chat-msg-bubble">'+content+'</span>'))+
                    '</div>';
            }else{
                html += '<div class="chat-msg-admin">'+(msg.type==1?'<span class="chat-msg-bubble"><img src="'+content+'" style="max-width:120px;max-height:120px;"></span>':(msg.type==2?'<span class="chat-msg-bubble"><video src="'+content+'" style="max-width:200px;max-height:150px;" controls onclick="openVideoModal(this.src)"></video></span>':'<span class="chat-msg-bubble">'+content+'</span>'))+
                    time+'</div>';
            }
        });
    }
    $('#chat-widget-dynamic-messages').html(html);
    
    // 检查是否有新消息，有新消息则播放提示音
    if(list.length > lastMessageCount && lastMessageCount > 0) {
        playChatNotification();
    }
    lastMessageCount = list.length;
}

// 播放聊天通知音频
function playChatNotification() {
    var audio = document.getElementById('chat-notification-audio');
    if(audio) {
        try {
            audio.currentTime = 0; // 重置音频到开头
            audio.play().catch(function(e) {
                console.log('音频播放失败:', e);
            });
        } catch(e) {
            console.log('音频播放异常:', e);
        }
    }
}

function loadChatMessages(scrollBottom){
    $.get(chatBasePath + '/user/ajax_chat.php?act=get', function(res){
        if(res.code===0){
            chatSessionId = res.session_id;
            // 获取当前消息数量
            var currentMessageCount = $('#chat-widget-dynamic-messages .chat-msg-admin').length;
            // 渲染新消息
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

// 获取用户订单列表
function loadUserOrders() {
    $.get(chatBasePath + '/user/ajax_chat.php?act=get_orders', function(res) {
        if(res.code === 0 && res.data.length > 0) {
            var select = $('#chat-widget-order');
            select.empty().append('<option value="">选择订单</option>');
            res.data.forEach(function(order) {
                select.append('<option value="' + order.id + '">订单 #' + order.id + ' - ' + order.name + ' - ' + order.money + '元</option>');
            });
        }
    }, 'json');
}

// 订单选择弹窗
function showOrderSelector() {
    var select = $('#chat-widget-order');
    if(select.css('display') === 'none') {
        select.css('display', 'block');
        loadUserOrders();
    } else {
        select.css('display', 'none');
    }
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
    
    // 订单选择按钮点击事件
    $('#chat-widget-order').parent().find('label[for="chat-widget-order"]').click(function(e) {
        e.preventDefault();
        showOrderSelector();
    });
    
    // 点击页面其他地方关闭订单选择器
    $(document).click(function(e) {
        if (!$(e.target).closest('#chat-widget-order, label[for="chat-widget-order"]').length) {
            $('#chat-widget-order').css('display', 'none');
        }
    });
    $('#chat-widget-close').click(function(){
        aurChatClose();
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
        var order_id = $(this).find('[name=order_id]').val();
        
        // 如果没有输入内容但选择了订单，自动生成订单相关的消息
        if(!content && order_id) {
            content = '我想咨询订单 #' + order_id + ' 的情况';
            $(this).find('[name=content]').val(content);
        }
        
        if(!content) return;
        
        var formData = new FormData(this);
        $.ajax({
            url: chatBasePath + '/user/ajax_chat.php?act=send',
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
                    $('#chat-widget-input [name=order_id]').val('');
                    $('#chat-widget-order').css('display', 'none');
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
                url: chatBasePath + '/user/ajax_chat.php?act=send',
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
    // 发送视频
    $('#chat-widget-upload-video').change(function(){
        if(this.files.length>0){
            // 显示上传中提示
            var loadingHtml = '<div class="chat-msg-user">\n                <span class="chat-msg-bubble">视频上传中...</span>\n                <span class="chat-msg-time">'+new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})+'</span>\n            </div>';
            $('#chat-widget-messages').append(loadingHtml);
            $('#chat-widget-messages').scrollTop($('#chat-widget-messages')[0].scrollHeight);

            var formData = new FormData($('#chat-widget-input')[0]);
            $.ajax({
                url: chatBasePath + '/user/ajax_chat.php?act=send',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res){
                    if(res.code===0){
                        loadChatMessages(true);
                        $('#chat-widget-input [name=video]').val('');
                    }else{
                        // 替换上传中提示为失败提示
                        var errorHtml = '<div class="chat-msg-user">\n                            <span class="chat-msg-bubble" style="background:#ff5252;">视频上传失败: '+res.msg+'</span>\n                            <span class="chat-msg-time">'+new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})+'</span>\n                        </div>';
                        $('#chat-widget-messages').find('.chat-msg-bubble:contains("视频上传中...")').closest('.chat-msg-user').replaceWith(errorHtml);
                    }
                },
                error: function() {
                    // 替换上传中提示为失败提示
                    var errorHtml = '<div class="chat-msg-user">\n                        <span class="chat-msg-bubble" style="background:#ff5252;">视频上传失败，请重试</span>\n                        <span class="chat-msg-time">'+new Date().toLocaleString('zh-CN', {month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})+'</span>\n                    </div>';
                    $('#chat-widget-messages').find('.chat-msg-bubble:contains("视频上传中...")').closest('.chat-msg-user').replaceWith(errorHtml);
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
    
    // 图片放大功能
    $(document).on('click', '.chat-msg-bubble img', function() {
        var modal = document.getElementById('chat-image-modal');
        var modalImg = document.getElementById('chat-image-modal-content');
        var closeBtn = document.getElementById('chat-image-modal-close');
        
        modal.style.display = 'block';
        modalImg.src = this.src;
        
        // 点击关闭按钮
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        // 点击模态框外部关闭
        modal.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    });
});

// 打开视频模态框
function openVideoModal(videoSrc) {
    var modal = document.getElementById('chat-video-modal');
    var modalVideo = document.getElementById('chat-video-modal-content');
    var closeBtn = document.getElementById('chat-video-modal-close');
    
    modal.style.display = 'block';
    modalVideo.src = videoSrc;
    modalVideo.play();
    
    // 点击关闭按钮
    closeBtn.onclick = function() {
        modal.style.display = 'none';
        modalVideo.pause();
    }
    
    // 点击模态框外部关闭
    modal.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            modalVideo.pause();
        }
    }
}
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