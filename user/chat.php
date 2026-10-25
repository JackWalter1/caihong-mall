<?php
include("../includes/common.php");
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php?back=chat';</script>");
$title = !empty($conf['chat_title']) ? $conf['chat_title'] : '在线客服';
if($conf['chat_enable']!=1)showmsg('当前站点未开启在线客服功能',3);
include './head.php';
$chat_welcome = !empty($conf['chat_welcome']) ? $conf['chat_welcome'] : '您好，欢迎咨询在线客服，请简短描述您的问题，我们会尽快回复您。';
$chat_polling = max(2, intval(!empty($conf['chat_polling']) ? $conf['chat_polling'] : 4));
?>
<style>
.chat-msgbox{height:440px;overflow-y:auto;background:#f5f7fa;border:1px solid #e3e8ef;border-radius:6px;padding:16px 14px}
.chat-msgbox::-webkit-scrollbar{width:6px}
.chat-msgbox::-webkit-scrollbar-thumb{background:#c6d0dd;border-radius:3px}
.chat-row{display:flex;margin-bottom:14px;flex-direction:column}
.chat-row.me{align-items:flex-end}
.chat-row.kefu{align-items:flex-start}
.chat-bubble{max-width:72%;padding:10px 14px;border-radius:10px;font-size:13px;line-height:1.7;word-break:break-all;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.chat-row.kefu .chat-bubble{background:#fff;border:1px solid #e3e8ef;color:#333}
.chat-row.me .chat-bubble{background:#1e90ff;color:#fff}
.chat-bubble img{display:block;max-width:200px;border-radius:6px;cursor:pointer}
.chat-bubble video{display:block;max-width:220px;border-radius:6px}
.chat-meta{font-size:11px;color:#98a6b3;margin-top:3px}
.chat-welcome{padding:10px 14px;background:#eaf4ff;border:1px solid #cfe6ff;color:#1b6bb5;border-radius:6px;margin-bottom:12px;font-size:13px}
.chat-toolbar{display:flex;align-items:center;gap:10px;margin-top:12px;flex-wrap:wrap}
.chat-toolbar .form-control{height:32px;font-size:13px;padding:4px 8px;width:auto;max-width:240px}
.chat-file-btn{position:relative;display:inline-flex;align-items:center;height:32px;padding:0 12px;border:1px solid #d7e3f0;border-radius:4px;background:#fff;color:#4d6b8a;font-size:13px;cursor:pointer}
.chat-file-btn:hover{border-color:#1e90ff;color:#1e90ff}
.chat-file-btn input{position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;cursor:pointer}
.chat-file-name{font-size:12px;color:#1e90ff;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.chat-inputbar{display:flex;gap:10px;margin-top:10px}
.chat-inputbar textarea{flex:1;height:64px;resize:none;border:1px solid #d7e3f0;border-radius:6px;padding:8px 10px;font-size:13px}
.chat-inputbar textarea:focus{border-color:#1e90ff;outline:none;box-shadow:0 0 0 2px rgba(30,144,255,.12)}
.chat-send-btn{height:64px;width:92px;border:none;border-radius:6px;background:#1e90ff;color:#fff;font-size:14px;font-weight:bold;cursor:pointer}
.chat-send-btn:hover{background:#1874cd}
.chat-send-btn:disabled{background:#9ec7ec;cursor:not-allowed}
@media (max-width:768px){.chat-bubble{max-width:88%}.chat-toolbar .form-control{max-width:150px}}
</style>
  <div class="wrapper">
    <div class="col-md-12 center-block" style="float: none;">
      <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-comments-o"></i>&nbsp;&nbsp;<b><?php echo $title ?></b></h3></div>
        <div class="panel-body">
          <div class="chat-welcome"><i class="fa fa-volume-up"></i>&nbsp;<?php echo htmlspecialchars($chat_welcome) ?></div>
          <div class="chat-msgbox" id="chat-msgbox"><div style="text-align:center;color:#98a6b3;padding:40px 0;"><i class="fa fa-spinner fa-spin"></i>&nbsp;正在加载聊天记录...</div></div>
          <div class="chat-toolbar">
            <select class="form-control" id="chat-order" title="附带订单咨询"></select>
            <label class="chat-file-btn"><i class="fa fa-picture-o"></i>&nbsp;发图片<input type="file" id="chat-image" accept="image/*"></label>
            <label class="chat-file-btn"><i class="fa fa-video-camera"></i>&nbsp;发视频<input type="file" id="chat-video" accept="video/mp4"></label>
            <span class="chat-file-name" id="chat-file-name"></span>
          </div>
          <div class="chat-inputbar">
            <textarea id="chat-input" placeholder="请输入您要咨询的内容，Enter 发送，Shift+Enter 换行" maxlength="1000"></textarea>
            <button type="button" class="chat-send-btn" id="chat-send">发 送</button>
          </div>
        </div>
      </div>
    </div>
  </div>
<script>
var chatPolling = <?php echo $chat_polling ?>;
var chatLastHtml = '';
var chatImgFile = null, chatVideoFile = null;

function escapeHtml(s){
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function fmtContent(m){
  var c = m.content || '';
  if(m.type == 1){ return '<img src="'+escapeHtml(c)+'" onclick="window.open(this.src)" alt="图片">'; }
  if(m.type == 2){ return '<video src="'+escapeHtml(c)+'" controls></video>'; }
  return escapeHtml(c).replace(/\n/g,'<br>');
}
function renderMsgs(list){
  var box = $('#chat-msgbox');
  var html = '';
  for(var i=0;i<list.length;i++){
    var m = list[i];
    if(m.sender=='user'){
      html += '<div class="chat-row me"><div class="chat-bubble">'+fmtContent(m)+'</div><div class="chat-meta">'+m.create_time+'</div></div>';
    }else{
      html += '<div class="chat-row kefu"><div class="chat-bubble"><b style="color:#1e90ff">客服</b><br>'+fmtContent(m)+'</div><div class="chat-meta">'+m.create_time+'</div></div>';
    }
  }
  if(!html) html = '<div style="text-align:center;color:#98a6b3;padding:40px 0;">暂无消息，发送消息开始咨询</div>';
  var stick = box.scrollTop() + box.innerHeight() >= box[0].scrollHeight - 60;
  if(html != chatLastHtml){
    chatLastHtml = html;
    box.html(html);
    box.scrollTop(box[0].scrollHeight);
  }else if(stick){
    box.scrollTop(box[0].scrollHeight);
  }
}
function loadMsgs(){
  $.get('./ajax_chat.php?act=get&t='+Date.now(), function(d){
    if(d.code==0){ renderMsgs(d.data||[]); }
  },'json').fail(function(){});
}
function loadOrders(){
  $.get('./ajax_chat.php?act=get_orders&t='+Date.now(), function(d){
    if(d.code==0){
      var opts = '<option value="0">不附带订单</option>';
      var list = d.data||[];
      for(var i=0;i<list.length;i++){
        var o = list[i];
        var st = o.status==1?'已完成':(o.status==0?'处理中':'失败');
        opts += '<option value="'+o.id+'">#'+o.id+' '+escapeHtml(o.name)+' ¥'+o.money+'（'+st+'）</option>';
      }
      $('#chat-order').html(opts);
    }
  },'json');
}
function sendMsg(){
  var input = $('#chat-input');
  var content = $.trim(input.val());
  if(!content && !chatImgFile && !chatVideoFile){ layer.msg('请输入消息内容'); return; }
  var fd = new FormData();
  fd.append('content', content);
  var oid = parseInt($('#chat-order').val()||0);
  if(oid>0) fd.append('order_id', oid);
  if(chatImgFile) fd.append('image', chatImgFile);
  if(chatVideoFile) fd.append('video', chatVideoFile);
  var btn = $('#chat-send');
  btn.prop('disabled',true).text('发送中');
  $.ajax({
    url:'./ajax_chat.php?act=send',
    type:'POST', data:fd, processData:false, contentType:false, dataType:'json',
    success:function(d){
      if(d.code==0){
        input.val('');
        chatImgFile=null; chatVideoFile=null;
        $('#chat-file-name').text('');
        $('#chat-image').val(''); $('#chat-video').val('');
        loadMsgs();
      }else{
        layer.msg(d.msg||'发送失败');
      }
    },
    error:function(){ layer.msg('网络错误，请稍后再试'); },
    complete:function(){ btn.prop('disabled',false).text('发 送'); }
  });
}
$(function(){
  loadMsgs();
  loadOrders();
  setInterval(loadMsgs, chatPolling*1000);
  $('#chat-send').click(sendMsg);
  $('#chat-input').keydown(function(e){
    if(e.keyCode==13 && !e.shiftKey){ e.preventDefault(); sendMsg(); }
  });
  $('#chat-image').change(function(){
    chatImgFile = this.files[0] || null;
    $('#chat-file-name').text(chatImgFile ? chatImgFile.name : '');
  });
  $('#chat-video').change(function(){
    chatVideoFile = this.files[0] || null;
    $('#chat-file-name').text(chatVideoFile ? chatVideoFile.name : '');
  });
});
</script>
<?php include './foot.php';?>
