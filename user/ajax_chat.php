<?php
// 前台客服AJAX接口
include("../includes/common.php");
include("../includes/chat_auto_reply.php");
@header('Content-Type: application/json; charset=UTF-8');

$act = isset($_GET['act']) ? $_GET['act'] : null;
$user_ip = real_ip();
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

function getSessionId($DB, $user_ip, $user_agent) {
    global $islogin2, $userrow;
    $zid = ($islogin2 == 1 && isset($userrow['zid'])) ? $userrow['zid'] : 0;
    if($zid > 0) {
        $row = $DB->getRow("SELECT * FROM shua_chat_session WHERE zid=? AND status=1 ORDER BY id DESC LIMIT 1", [$zid]);
    } else {
        $row = $DB->getRow("SELECT * FROM shua_chat_session WHERE zid=0 AND user_ip=? AND status=1 ORDER BY id DESC LIMIT 1", [$user_ip]);
    }
    if($row) return $row['id'];
    $DB->exec("INSERT INTO shua_chat_session (zid, user_ip, user_agent, status, last_msg_time, create_time) VALUES (?,?,?,?,?,NOW())", [$zid, $user_ip, $user_agent, 1, date('Y-m-d H:i:s')]);
    return $DB->lastInsertId();
}

switch($act){
    case 'get':
        // 获取会话和消息
        $session_id = getSessionId($DB, $user_ip, $user_agent);
        $messages = $DB->getAll("SELECT * FROM shua_chat_message WHERE session_id=? ORDER BY id ASC LIMIT 100", [$session_id]);
        $data = [];
        foreach($messages as $msg){
            $data[] = [
                'id' => $msg['id'],
                'sender' => $msg['sender'],
                'content' => $msg['content'],
                'type' => $msg['type'],
                'create_time' => $msg['create_time']
            ];
        }
        exit(json_encode(['code'=>0,'session_id'=>$session_id,'data'=>$data]));
        break;
    case 'get_orders':
        // 获取用户订单
        $data = [];
        if(isset($islogin2) && $islogin2 === 1){
            // 用户已登录，获取其订单
            $orders = $DB->getAll("SELECT o.id, t.name, o.money, o.status, o.addtime 
                                 FROM shua_orders o 
                                 LEFT JOIN shua_tools t ON o.tid = t.tid 
                                 WHERE o.userid = ? 
                                 ORDER BY o.id DESC LIMIT 20", [$userrow['zid']]);
            
            foreach($orders as $order){
                $data[] = [
                    'id' => $order['id'],
                    'name' => $order['name'],
                    'money' => $order['money'],
                    'status' => $order['status'],
                    'addtime' => $order['addtime']
                ];
            }
        }
        exit(json_encode(['code'=>0,'data'=>$data]));
        break;
    case 'send':
        // 发送消息（支持图片）
        $session_id = getSessionId($DB, $user_ip, $user_agent);
        
        // 获取订单ID
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        
        // 防刷屏功能实现
        $chat_anti_spam_enable = $conf['chat_anti_spam_enable'] ?? 1;
        if($chat_anti_spam_enable == 1){
            // 自动检查表是否存在，不存在则创建
            try {
                $prefix = $conf['prefix'] ?? 'shua_';
                $table_name = "{$prefix}chat_ban";
                
                // 检查表是否存在
                $stmt = $DB->query("SHOW TABLES LIKE '$table_name'");
                $table_exists = $stmt->rowCount() > 0;
                
                if (!$table_exists) {
                    // 创建表
                    $sql = "CREATE TABLE $table_name (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_ip VARCHAR(50) NOT NULL,
                        user_agent VARCHAR(255) NULL,
                        session_id VARCHAR(50) NOT NULL,
                        ban_time INT NOT NULL,
                        ban_end_time DATETIME NOT NULL,
                        create_time DATETIME NOT NULL
                    )";
                    
                    $DB->exec($sql);
                }
            } catch (Exception $e) {
                // 表创建失败不影响程序运行，但可以记录日志
                // error_log('Failed to create chat ban table: ' . $e->getMessage());
            }
            // 检查用户是否被封禁
            $ban_info = $DB->getRow("SELECT * FROM shua_chat_ban WHERE user_ip=? AND ban_end_time>NOW() LIMIT 1", [$user_ip]);
            if($ban_info){
                // 计算剩余封禁时间
                $ban_end = strtotime($ban_info['ban_end_time']);
                $now = time();
                $remaining = $ban_end - $now;
                $remaining_text = '';
                if($remaining < 60){
                    $remaining_text = "{$remaining}秒";
                }elseif($remaining < 3600){
                    $remaining_text = floor($remaining/60)."分钟";
                }else{
                    $remaining_text = floor($remaining/3600)."小时";
                }
                exit(json_encode(['code'=>-7,'msg'=>'您已被禁言，剩余时间：{$remaining_text}']));
            }
            
            // 检查一分钟内发送的消息数量
            $max_messages = $conf['chat_max_messages'] ?? 10;
            $one_minute_ago = date('Y-m-d H:i:s', time() - 60);
            $msg_count = $DB->getColumn("SELECT COUNT(*) FROM shua_chat_message WHERE session_id=? AND sender='user' AND create_time>?", [$session_id, $one_minute_ago]);
            
            if($msg_count >= $max_messages){
                // 获取用户的违规次数
                $violations = $DB->getColumn("SELECT COUNT(*) FROM shua_chat_ban WHERE user_ip=?", [$user_ip]);
                
                // 设置封禁时长
                if($violations >= 1){
                    // 二次违规，封禁一天
                    $ban_time = $conf['chat_second_ban_time'] ?? 24;
                    $ban_end_time = date('Y-m-d H:i:s', time() + ($ban_time * 3600));
                    $ban_msg = "由于您在短时间内发送消息过多（二次违规），已被禁言{$ban_time}小时";
                }else{
                    // 首次违规，封禁3分钟
                    $ban_time = $conf['chat_first_ban_time'] ?? 3;
                    $ban_end_time = date('Y-m-d H:i:s', time() + ($ban_time * 60));
                    $ban_msg = "由于您在短时间内发送消息过多，已被禁言{$ban_time}分钟";
                }
                
                // 记录封禁信息
                $DB->exec("INSERT INTO shua_chat_ban (user_ip, user_agent, session_id, ban_time, ban_end_time, create_time) VALUES (?,?,?,?,?,NOW())", 
                          [$user_ip, $user_agent, $session_id, $ban_time, $ban_end_time]);
                
                // 清除用户的刷屏消息（只保留最早的几条）
                $keep_count = 5; // 保留的消息数量
                $delete_ids = $DB->getCol("SELECT id FROM shua_chat_message WHERE session_id=? AND sender='user' AND create_time>? ORDER BY id DESC LIMIT ?", 
                                         [$session_id, $one_minute_ago, $msg_count - $keep_count]);
                if(!empty($delete_ids)){
                    $DB->exec("DELETE FROM shua_chat_message WHERE id IN (".implode(',', $delete_ids).")");
                }
                
                exit(json_encode(['code'=>-7,'msg'=>$ban_msg]));
            }
        }
        $content = trim($_POST['content'] ?? '');
        $type = 0;
        if(isset($_FILES['image']) && $_FILES['image']['size']>0){
            // 获取图片上传限制配置
            $chat_image_limit_enable = $conf['chat_image_limit_enable'] ?? 1;
            $chat_image_max_size = ($conf['chat_image_max_size'] ?? 2048) * 1024; // 转换为字节
            $chat_image_formats = strtolower($conf['chat_image_formats'] ?? 'jpg,jpeg,png,gif,webp');
            $allowed_formats = array_map('trim', explode(',', $chat_image_formats));
            
            // 获取文件信息
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $file_size = $_FILES['image']['size'];
            $file_type = $_FILES['image']['type'];
            $allowed_mime_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
            
            // 检查文件大小
            if($file_size > $chat_image_max_size){
                exit(json_encode(['code'=>-5,'msg'=>'图片大小超过限制，请上传小于'.($chat_image_max_size/1024).'KB的图片']));
            }
            
            // 检查图片格式
            if(!in_array($ext, $allowed_formats)){
                exit(json_encode(['code'=>-6,'msg'=>'不支持的图片格式，请上传'.implode(',', $allowed_formats).'格式的图片']));
            }
            
            // 检查MIME类型
            if(!in_array($file_type, $allowed_mime_types)){
                exit(json_encode(['code'=>-6,'msg'=>'不支持的文件类型，请上传真实的图片文件']));
            }
            
            // 检查文件是否为真实图片
            $image_info = getimagesize($_FILES['image']['tmp_name']);
            if (!$image_info) {
                exit(json_encode(['code'=>-6,'msg'=>'请上传真实的图片文件']));
            }
            
            $filename = 'chat_'.date('YmdHis').'_'.rand(1000,9999).'.'.$ext;
            $filepath = ROOT.'assets/img/chat/'.$filename;
            if(!is_dir(ROOT.'assets/img/chat/')) mkdir(ROOT.'assets/img/chat/',0755,true);
            if(move_uploaded_file($_FILES['image']['tmp_name'], $filepath)){
                compress_image($filepath); // 自动压缩，加快前端加载
                $content = '/assets/img/chat/'.$filename;
                $type = 1;
            }else{
                exit(json_encode(['code'=>-1,'msg'=>'图片上传失败']));
            }
        }
        // 处理视频上传
        if(isset($_FILES['video']) && $_FILES['video']['size']>0){
            // 检查视频上传是否开启
            $chat_video_enable = $conf['chat_video_enable'] ?? 1;
            if($chat_video_enable != 1){
                exit(json_encode(['code'=>-10,'msg'=>'视频上传功能已关闭']));
            }
            
            // 获取视频上传限制配置
            $chat_video_max_size = ($conf['chat_video_max_size'] ?? 10) * 1024 * 1024; // 转换为字节，默认10MB
            $allowed_video_formats = ['mp4'];
            $allowed_video_mime_types = ['video/mp4'];
            
            // 获取文件信息
            $ext = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
            $file_size = $_FILES['video']['size'];
            $file_type = $_FILES['video']['type'];
            
            // 检查文件大小
            if($file_size > $chat_video_max_size){
                exit(json_encode(['code'=>-8,'msg'=>'视频大小超过限制，请上传小于'.($chat_video_max_size/1024/1024).'MB的视频']));
            }
            
            // 检查视频格式
            if(!in_array($ext, $allowed_video_formats)){
                exit(json_encode(['code'=>-9,'msg'=>'不支持的视频格式，请上传MP4格式的视频']));
            }
            
            // 检查MIME类型
            if(!in_array($file_type, $allowed_video_mime_types)){
                exit(json_encode(['code'=>-9,'msg'=>'不支持的文件类型，请上传真实的MP4视频文件']));
            }
            
            $filename = 'chat_video_'.date('YmdHis').'_'.rand(1000,9999).'.'.$ext;
            $filepath = ROOT.'assets/img/chat/'.$filename;
            if(!is_dir(ROOT.'assets/img/chat/')) mkdir(ROOT.'assets/img/chat/',0755,true);
            if(move_uploaded_file($_FILES['video']['tmp_name'], $filepath)){
                $content = '/assets/img/chat/'.$filename;
                $type = 2; // 2表示视频类型
            }else{
                exit(json_encode(['code'=>-1,'msg'=>'视频上传失败']));
            }
        }
        if(empty($content)) exit(json_encode(['code'=>-1,'msg'=>'消息内容不能为空']));
        
        // 违禁词检查（仅对文本消息进行检查）
        if($type == 0 && checkProhibitedWords($content)) {
            exit(json_encode(['code'=>-3,'msg'=>getProhibitedMessage()]));
        }
        
        // 如果选择了订单，在消息中添加订单信息
        if($order_id > 0 && $type == 0) {
            // 获取订单详情
            $order = $DB->getRow("SELECT o.id, t.name, o.input, o.money, o.status 
                                 FROM shua_orders o 
                                 LEFT JOIN shua_tools t ON o.tid = t.tid 
                                 WHERE o.id = ? AND o.userid = ? LIMIT 1", [$order_id, isset($userrow['zid']) ? $userrow['zid'] : 0]);
            
            if($order) {
                // 添加订单信息到消息内容
                $content .= "\n\n【订单信息】\n";
                $content .= "订单号：#{$order['id']}\n";
                $content .= "商品名称：{$order['name']}\n";
                $content .= "商品链接/账号：{$order['input']}\n";
                $content .= "订单金额：{$order['money']}元\n";
                $content .= "订单状态：" . ($order['status'] == 1 ? '已完成' : ($order['status'] == 0 ? '处理中' : '失败'));
            }
        }
        
        $result = $DB->exec("INSERT INTO shua_chat_message (session_id,sender,content,type,create_time) VALUES (?,?,?,?,NOW())", [$session_id,'user',$content,$type]);
        if($result === false) exit(json_encode(['code'=>-2,'msg'=>'数据库写入失败:'.print_r($DB->error(),true)]));
        $DB->exec("UPDATE shua_chat_session SET last_msg_time=NOW(), status=1 WHERE id=?", [$session_id]);
        
        // 自动回复处理
        $auto_reply = getAutoReply($content);
        if($auto_reply) {
            $DB->exec("INSERT INTO shua_chat_message (session_id,sender,content,type,create_time) VALUES (?,?,?,?,NOW())", [$session_id,'admin',$auto_reply['reply'],0]);
            $DB->exec("UPDATE shua_chat_session SET last_msg_time=NOW() WHERE id=?", [$session_id]);
            exit(json_encode(['code'=>0,'msg'=>'发送成功','auto_reply'=>$auto_reply]));
        }
        
        exit(json_encode(['code'=>0,'msg'=>'发送成功']));
        break;
    default:
        exit(json_encode(['code'=>-4,'msg'=>'No Act']));
}