<?php
// 客服工作台
// 博客地址：zhonguo.ren
// Q群：915043052
include "../includes/common.php";
$title = "客服工作台";
include "./head.php";
if ($islogin != 1) {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

// 自动创建聊天相关表结构
if (!$DB->query("SELECT 1 FROM shua_chat_session LIMIT 1")) {
    $DB->exec("CREATE TABLE IF NOT EXISTS shua_chat_session (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        zid INT(11) UNSIGNED DEFAULT '0' COMMENT '账号ID，0表示游客',
        user_ip VARCHAR(50) NOT NULL,
        user_agent TEXT,
        status TINYINT(1) DEFAULT 1,
        create_time DATETIME NOT NULL,
        last_msg_time DATETIME NOT NULL,
        PRIMARY KEY (id),
        INDEX idx_zid (zid),
        INDEX idx_user_ip (user_ip),
        INDEX idx_status (status),
        INDEX idx_last_msg_time (last_msg_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} else {
    // 检查并添加 zid 字段（如果旧表没有）
    $columns = $DB->getAll("SHOW COLUMNS FROM shua_chat_session");
    $has_zid = false;
    foreach($columns as $col) {
        if($col['Field'] == 'zid') {
            $has_zid = true;
            break;
        }
    }
    if(!$has_zid) {
        $DB->exec("ALTER TABLE shua_chat_session ADD COLUMN zid INT(11) UNSIGNED DEFAULT '0' COMMENT '账号ID，0表示游客' AFTER id, ADD INDEX idx_zid (zid)");
    }
}

if (!$DB->query("SELECT 1 FROM shua_chat_message LIMIT 1")) {
    $DB->exec("CREATE TABLE IF NOT EXISTS shua_chat_message (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id INT(11) UNSIGNED NOT NULL,
        type TINYINT(1) DEFAULT 0,
        sender VARCHAR(20) NOT NULL,
        content TEXT NOT NULL,
        create_time DATETIME NOT NULL,
        PRIMARY KEY (id),
        INDEX idx_session_id (session_id),
        INDEX idx_create_time (create_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

$chat_sound = intval($conf['chat_sound'] ?? 1);
$chat_style = intval($conf['chat_style'] ?? 1);
?>
<?php if($chat_style == 2){ ?>
<style>
/* ===== 客服工作台 · 样式二（暗夜黑） ===== */
.panel-primary{border-color:#334155!important;box-shadow:0 8px 24px rgba(0,0,0,.35)!important}
.panel-primary>.panel-heading{background:linear-gradient(135deg,#1e293b,#0f172a)!important;color:#e2e8f0!important;border-color:#334155!important}
#chat-session-list .list-group-item{background:#1e293b;border:none;border-bottom:1px solid #334155;border-radius:8px;margin-bottom:4px;padding:12px 14px;color:#cbd5e1;transition:all .2s}
#chat-session-list .list-group-item:hover{background:#273449;color:#e2e8f0}
#chat-session-list .list-group-item.active{background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;color:#fff}
#chat-session-list .list-group-item small{color:#94a3b8}
#chat-session-list .list-group-item.active small{color:rgba(255,255,255,.85)}
#chat-message-area .panel{border-color:#334155!important;background:#0f172a}
#chat-message-area .panel-heading{background:#1e293b!important;color:#e2e8f0!important;border-bottom:1px solid #334155!important}
#chat-message-list{background:#0f172a!important}
#chat-message-list .label{border-radius:999px!important}
#chat-message-list .text-left .label{background:#1e293b!important;color:#cbd5e1!important;border:1px solid #334155}
#chat-message-list .text-right .label{background:linear-gradient(135deg,#6366f1,#8b5cf6)!important}
#chat-message-list small{color:#64748b}
#chat-reply-box{background:#1e293b!important;border-top:1px solid #334155!important}
#chat-reply-box .form-control{background:#0f172a!important;border-color:#334155!important;color:#e2e8f0!important}
#chat-reply-box .btn-default{background:#273449!important;border-color:#334155!important;color:#cbd5e1!important}
</style>
<?php }elseif($chat_style == 3){ ?>
<style>
/* ===== 客服工作台 · 样式三（渐变科技） ===== */
.panel-primary{border:none!important;box-shadow:0 10px 30px rgba(99,102,241,.18)!important;border-radius:14px!important;overflow:hidden}
.panel-primary>.panel-heading{background:linear-gradient(135deg,#6366f1,#06b6d4)!important;border:none!important}
#chat-session-list .list-group-item{background:linear-gradient(135deg,#ffffff,#f8fafc)!important;border:1px solid #e8ecf1!important;border-radius:10px!important;margin-bottom:6px;padding:12px 14px;box-shadow:0 2px 8px rgba(30,60,110,.06);transition:all .2s}
#chat-session-list .list-group-item:hover{transform:translateX(3px);box-shadow:0 4px 14px rgba(99,102,241,.15)}
#chat-session-list .list-group-item.active{background:linear-gradient(135deg,#6366f1,#06b6d4)!important;border:none!important;color:#fff;box-shadow:0 6px 18px rgba(99,102,241,.35)}
#chat-session-list .list-group-item.active small{color:rgba(255,255,255,.85)}
#chat-message-area .panel{border:none!important;box-shadow:0 8px 24px rgba(30,60,110,.1)!important;border-radius:12px!important;overflow:hidden}
#chat-message-area .panel-heading{background:linear-gradient(135deg,#6366f1,#06b6d4)!important;color:#fff!important;border:none!important}
#chat-message-list{background:linear-gradient(180deg,#f6f7ff,#eef6fd)!important}
#chat-message-list .label{border-radius:999px!important}
#chat-message-list .text-left .label{background:#fff!important;color:#475569!important;border:1px solid #e2e8f0;box-shadow:0 2px 6px rgba(30,60,110,.08)}
#chat-message-list .text-right .label{background:linear-gradient(135deg,#6366f1,#06b6d4)!important}
#chat-reply-box{background:#fff!important;border-top:1px solid #eef2f7!important}
</style>
<?php }else{ ?>
<style>
/* ===== 客服工作台 · 样式一（清新蓝） ===== */
.panel-primary{border-radius:12px!important;overflow:hidden;box-shadow:0 6px 20px rgba(30,60,110,.08)!important}
.panel-primary>.panel-heading{background:linear-gradient(135deg,#3b82f6,#2563eb)!important}
#chat-session-list .list-group-item{background:#fff;border:none;border-bottom:1px solid #eef2f7;border-radius:8px;margin-bottom:4px;padding:12px 14px;transition:all .2s}
#chat-session-list .list-group-item:hover{background:#f0f6ff;color:#1e293b}
#chat-session-list .list-group-item.active{background:linear-gradient(135deg,#3b82f6,#2563eb);border:none;color:#fff}
#chat-session-list .list-group-item.active small{color:rgba(255,255,255,.85)}
#chat-message-list{background:#f7f9fc!important}
#chat-message-list .label{border-radius:999px!important}
#chat-message-list .text-left .label{background:#fff;color:#475569;border:1px solid #e2e8f0}
#chat-message-list .text-right .label{background:linear-gradient(135deg,#3b82f6,#2563eb)}
#chat-reply-box{background:#fff!important;border-top:1px solid #eef2f7!important}
</style>
<?php } ?>
<div class="col-xs-12 col-sm-12 col-lg-12 center-block" style="float: none;">
    <!-- 音频元素用于客服通知 -->
    <audio id="chat-notification-audio" preload="auto" style="display:none;">
        <source src="../template/default/chat/kefu.wav" type="audio/wav">
    </audio>
    <div class="panel panel-primary">
        <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;"><h3 class="panel-title"><i class="fa fa-headphones"></i> 客服工作台</h3><a href="set.php?mod=chat" class="btn btn-sm btn-default"><i class="fa fa-cog"></i> 客服系统配置</a></div>
        <div class="panel-body" style="min-height:600px;">
            <div class="row">
                <div class="col-sm-4" id="chat-session-list">
                    <!-- 会话列表区域 -->
                    <div class="list-group" style="height:550px;overflow-y:auto;">
                        <a href="#" class="list-group-item active">会话列表加载中...</a>
                    </div>
                </div>
                <div class="col-sm-8" id="chat-message-area">
                    <!-- 消息窗口区域 -->
                    <div class="panel panel-default" style="height:550px;display:flex;flex-direction:column;">
                        <div class="panel-heading" id="chat-session-title">请选择会话</div>
                        <div class="panel-body" id="chat-message-list" style="flex:1;overflow-y:auto;background:#f9f9f9;">
                            <div class="text-center text-muted" style="margin-top:200px;">暂无会话</div>
                        </div>
                        <div class="panel-footer" id="chat-reply-box" style="display:none;">
                            <form id="chat-reply-form" class="form-inline" enctype="multipart/form-data">
                                <input type="hidden" name="session_id" value="">
                                <div class="form-group" style="width:60%;">
                                    <input type="text" class="form-control" name="content" placeholder="输入回复内容..." style="width:100%;">
                                </div>
                                <input type="file" name="image" accept="image/*" style="display:none;" id="chat-upload-image">
                                <button type="button" class="btn btn-default" onclick="$('#chat-upload-image').click();"><i class="fa fa-image"></i> 图片</button>
                                <input type="file" name="video" accept="video/mp4" style="display:none;" id="chat-upload-video">
                                <button type="button" class="btn btn-default" onclick="$('#chat-upload-video').click();"><i class="fa fa-video-camera"></i> 视频</button>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> 发送</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var currentSessionId = null;
var pollingTimer = null;
var lastUnreadCount = 0;
var lastMessageIds = {}; // 存储每个会话的最后消息ID，用于检测新消息
var lastMessageListHash = {}; // 存储每个会话的消息列表哈希，用于检测消息变化
var chatSoundEnabled = <?php echo $chat_sound; ?>;
var chatSessionInfo = {}; // sessionId -> {zid, power, username}，用于识别消息来源（游客/用户/分站）
var _lastNotifiedId = 0; // 已播报过的最新消息ID（时间线去重：只对更新的消息响一次）
var _lastSessionSoundTime = 0; // 会话列表播放声音的时间锁，避免与消息列表重复响

// 识别会话来源标签：zid=0 游客，zid>0 且 power>0 分站，zid>0 用户
function getSourceTag(sessionId) {
    var info = chatSessionInfo[sessionId];
    if(info && parseInt(info.zid) > 0) {
        return (parseInt(info.power) > 0)
            ? '<span class="label label-primary">分站</span>'
            : '<span class="label label-success">用户</span>';
    }
    return '<span class="label label-default">游客</span>';
}

// 统一的通知声音播报（按最新时间线去重：仅当传入的最新消息ID比已播报的更晚时才响，绝不重复）
function playChatNotify(latestId) {
    if(!chatSoundEnabled) return;
    if(latestId !== undefined && latestId !== null) {
        if(latestId <= _lastNotifiedId) return; // 时间线未更新，不重复提示
        _lastNotifiedId = latestId;
    }
    var audio = document.getElementById('chat-notification-audio');
    if(audio) {
        try { audio.currentTime = 0; audio.play().catch(function(error) { console.log('音频播放失败:', error); }); }
        catch(e) { console.log('音频播放异常:', e); }
    }
}

function loadSessionList() {
    $.get('ajax.php?act=chat_session_list', function(res) {
        if(res.code === 0) {
            var html = '';
            var totalUnread = 0;
            if(res.data.length === 0) {
                html = '<a href="#" class="list-group-item">暂无会话</a>';
            } else {
                res.data.forEach(function(item) {
                    totalUnread += parseInt(item.unread || 0);
                    // 记录会话来源信息：zid>0 为分站(power>=2)/用户(power=1)，zid==0 为游客
                    chatSessionInfo[item.id] = {zid: parseInt(item.zid) || 0, power: parseInt(item.power) || 0, username: item.username || ''};
                    var srcTag = getSourceTag(item.id);
                    var displayName = item.username ? item.username : item.user_ip;
                    html += '<a href="#" class="list-group-item'+(item.id==currentSessionId?' active':'')+'" data-id="'+item.id+'">'+ 
                        '<span class="badge">'+(item.unread>0?item.unread:'')+'</span>'+srcTag+displayName+'<br><small>'+item.last_msg_time+'</small>'+ 
                        (item.status==0?'<span class="label label-default pull-right">已关闭</span>':'')+ 
                        '</a>';
                });
            }
            $('#chat-session-list .list-group').html(html);
            
            // 检查是否有新的未读消息（播放一次提醒；带时间锁避免与消息列表重复响）
            if(totalUnread > lastUnreadCount) {
                var now = Date.now();
                if(now - _lastSessionSoundTime > 1500) {
                    _lastSessionSoundTime = now;
                    playChatNotify();
                }
            }
            lastUnreadCount = totalUnread;
        }
    },'json');
}

function loadMessageList(sessionId, scrollBottom, playSound) {
    // 保存当前滚动位置，用于检测新消息
    var currentHeight = $('#chat-message-list')[0].scrollHeight;
    var isAtBottom = $('#chat-message-list').scrollTop() + $('#chat-message-list').height() >= currentHeight - 50;
    
    $.get('ajax.php?act=chat_message_list&session_id='+sessionId, function(res) {
        if(res.code === 0) {
            // 保存当前会话的最后消息ID
            var currentLastId = lastMessageIds[sessionId] || 0;
            var newUserMessages = [];
            var hasNewUserMessages = false;
            
            // 计算消息列表的哈希值，用于检测消息是否有变化
            var messageHash = '';
            res.data.forEach(function(msg) {
                messageHash += msg.id + '_' + msg.type + '_' + msg.content + '|';
            });
            
            // 检查消息是否有变化
            if(lastMessageListHash[sessionId] === messageHash) {
                // 消息没有变化，不更新DOM，直接返回
                return;
            }
            
            // 更新消息列表哈希
            lastMessageListHash[sessionId] = messageHash;
            
            var html = '';
            // 识别当前会话来源：zid=0 游客，zid>0 且 power>0 分站，zid>0 用户
            var srcTag = getSourceTag(sessionId);
            var maxNewUserMsgId = 0; // 新用户消息的最新时间线（最大ID）
            res.data.forEach(function(msg) {
                if(msg.type==1) {
                    html += '<div class="'+(msg.sender=='admin'?'text-right':'text-left')+'"><img src="'+msg.content+'" style="max-width:120px;max-height:120px;border-radius:6px;cursor:pointer;" onclick="openImageModal(this.src)"> <small>'+msg.create_time+'</small></div>';
                } else if(msg.type==2) {
                    html += '<div class="'+(msg.sender=='admin'?'text-right':'text-left')+'"><video src="'+msg.content+'" style="max-width:200px;max-height:150px;border-radius:6px;cursor:pointer;" controls onclick="openVideoModal(this.src)"></video> <small>'+msg.create_time+'</small></div>';
                } else {
                    html += '<div class="'+(msg.sender=='admin'?'text-right':'text-left')+'"><span class="label label-'+(msg.sender=='admin'?'primary':'default')+'">'+(msg.sender=='admin'?'客服':srcTag)+'</span> '+msg.content+' <small>'+msg.create_time+'</small></div>';
                }
                
                // 检查是否是新的用户消息，记录最新时间线（最大ID）
                if(msg.id > currentLastId && msg.sender !== 'admin') {
                    newUserMessages.push(msg);
                    hasNewUserMessages = true;
                    if(msg.id > maxNewUserMsgId) maxNewUserMsgId = msg.id;
                }
            });
            
            // 按最新时间线播报一次声音（时间线未更新时绝不重复提示）
            if(maxNewUserMsgId > 0 && currentLastId > 0 && playSound !== false) {
                playChatNotify(maxNewUserMsgId);
            }
            
            // 更新最后消息ID
            if(res.data.length > 0) {
                lastMessageIds[sessionId] = res.data[res.data.length - 1].id;
            }
            
            if(html==='') html = '<div class="text-center text-muted" style="margin-top:200px;">暂无消息</div>';
            $('#chat-message-list').html(html);
            
            // 自动滚动到底部（如果之前就在底部）
            if(scrollBottom!==false || isAtBottom) {
                $('#chat-message-list').scrollTop($('#chat-message-list')[0].scrollHeight);
            }
        }
    },'json');
}

function startPolling() {
    if(pollingTimer) clearInterval(pollingTimer);
    var interval = <?php echo intval($conf['chat_polling']??3); ?> * 1000;
    pollingTimer = setInterval(function(){
        if(currentSessionId) loadMessageList(currentSessionId, true, true);
        loadSessionList();
    }, interval);
}

$(function(){
    loadSessionList();
    startPolling();

    // 点击会话切换
    $('#chat-session-list').on('click', '.list-group-item', function(e){
        e.preventDefault();
        var sid = $(this).data('id');
        if(!sid) return;
        currentSessionId = sid;
        $('#chat-session-list .list-group-item').removeClass('active');
        $(this).addClass('active');
        $('#chat-reply-box').show();
        $('#chat-reply-form [name=session_id]').val(sid);
        
        // 显示会话标题
        var displayName = $(this).text().trim();
        displayName = displayName.split('\n')[0];
        displayName = displayName.replace(/\d+/, '').trim();
        $('#chat-session-title').text('会话：'+displayName);
        
        // 清除该会话的未读计数，防止点击后继续响
        $(this).find('.badge').text('').hide();
        
        // 加载消息列表，不播放声音
        loadMessageList(sid, true, false);
        
        // 更新全局未读计数
        setTimeout(function() {
            $.get('ajax.php?act=chat_session_list', function(res) {
                if(res.code === 0) {
                    var totalUnread = 0;
                    res.data.forEach(function(item) {
                        totalUnread += parseInt(item.unread || 0);
                    });
                    lastUnreadCount = totalUnread;
                }
            },'json');
        }, 500);
    });

    // 发送消息
    $('#chat-reply-form').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'ajax.php?act=chat_send_message',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res){
                if(res.code===0){
                    // 保存当前会话的最新消息ID，防止自己回复时播放声音
                    $.get('ajax.php?act=chat_message_list&session_id='+currentSessionId, function(msgRes) {
                        if(msgRes.code === 0 && msgRes.data.length > 0) {
                            lastMessageIds[currentSessionId] = msgRes.data[msgRes.data.length - 1].id + 1;
                        }
                        // 加载消息列表，但不播放声音
                        loadMessageList(currentSessionId, true, false);
                    },'json');
                    $('#chat-reply-form [name=content]').val('');
                    $('#chat-reply-form [name=image]').val('');
                }else{
                    alert(res.msg);
                }
            }
        });
    });

    // 发送图片
    $('#chat-upload-image').change(function(){
        if(this.files.length>0){
            var formData = new FormData($('#chat-reply-form')[0]);
            $.ajax({
                url: 'ajax.php?act=chat_send_message',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res){
                    if(res.code===0){
                        // 保存当前会话的最新消息ID，防止自己发送图片时播放声音
                        $.get('ajax.php?act=chat_message_list&session_id='+currentSessionId, function(msgRes) {
                            if(msgRes.code === 0 && msgRes.data.length > 0) {
                                lastMessageIds[currentSessionId] = msgRes.data[msgRes.data.length - 1].id + 1;
                            }
                            // 加载消息列表，但不播放声音
                            loadMessageList(currentSessionId, true, false);
                        },'json');
                        $('#chat-reply-form [name=image]').val('');
                    }else{
                        alert(res.msg);
                    }
                }
            });
        }
    });

    // 发送视频
    $('#chat-upload-video').change(function(){
        if(this.files.length>0){
            var formData = new FormData($('#chat-reply-form')[0]);
            $.ajax({
                url: 'ajax.php?act=chat_send_message',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res){
                    if(res.code===0){
                        // 保存当前会话的最新消息ID，防止自己发送视频时播放声音
                        $.get('ajax.php?act=chat_message_list&session_id='+currentSessionId, function(msgRes) {
                            if(msgRes.code === 0 && msgRes.data.length > 0) {
                                lastMessageIds[currentSessionId] = msgRes.data[msgRes.data.length - 1].id + 1;
                            }
                            // 加载消息列表，但不播放声音
                            loadMessageList(currentSessionId, true, false);
                        },'json');
                        $('#chat-reply-form [name=video]').val('');
                    }else{
                        alert(res.msg);
                    }
                }
            });
        }
    });

    // 会话关闭
    $('#chat-session-title').on('dblclick', function(){
        if(currentSessionId && confirm('确定要关闭此会话吗？')){
            $.post('ajax.php?act=chat_close_session', {session_id:currentSessionId}, function(res){
                if(res.code===0){
                    loadSessionList();
                    $('#chat-message-list').html('<div class="text-center text-muted" style="margin-top:200px;">会话已关闭</div>');
                    $('#chat-reply-box').hide();
                }else{
                    alert(res.msg);
                }
            },'json');
        }
    });
});
</script>

<!-- 图片放大样式 -->
<style>
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

/* 聊天图片样式 */
#chat-message-list img {
    cursor: pointer;
    transition: transform 0.2s ease;
}

#chat-message-list img:hover {
    transform: scale(1.05);
}
</style>

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
// 图片放大功能
function openImageModal(src) {
    var modal = document.getElementById('chat-image-modal');
    var modalImg = document.getElementById('chat-image-modal-content');
    var closeBtn = document.getElementById('chat-image-modal-close');
    
    modal.style.display = 'block';
    modalImg.src = src;
    
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
}

// 视频播放功能
function openVideoModal(src) {
    var modal = document.getElementById('chat-video-modal');
    var modalVideo = document.getElementById('chat-video-modal-content');
    var closeBtn = document.getElementById('chat-video-modal-close');
    
    modal.style.display = 'block';
    modalVideo.src = src;
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
</script>
<?php include "./foot.php";?> 