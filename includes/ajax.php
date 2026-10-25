<?php
$islogin=0;
if(isset($_COOKIE["admin_token"])){
    include("../includes/common.php");
}else{
    include("../includes/common.php");
}
header('Content-Type: application/json; charset=UTF-8');

$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

switch($act){
    case 'report_invalid':
        $goods_id = intval($_POST['goods_id']);
        $desc = trim(strip_tags(daddslashes($_POST['desc'])));
        
        if(empty($goods_id)) {
            exit('{"code":-1,"msg":"商品ID不能为空"}');
        }
        if(empty($desc)) {
            exit('{"code":-1,"msg":"补充说明不能为空"}');
        }
        
        // 查询商品是否存在
        $row = $DB->getRow("SELECT * FROM pre_tools WHERE tid='$goods_id' LIMIT 1");
        if(!$row) {
            exit('{"code":-1,"msg":"商品不存在"}');
        }
        
        // 检查是否已经报备过
        $exist = $DB->getRow("SELECT * FROM pre_pan_invalid WHERE goods_id='$goods_id' AND status=0 LIMIT 1");
        if($exist) {
            exit('{"code":-1,"msg":"该商品已有待处理的报备记录，请勿重复提交"}');
        }
        
        // 插入报备记录
        $sql = "INSERT INTO pre_pan_invalid (goods_id, goods_name, `desc`, addtime, status) VALUES (:goods_id, :goods_name, :desc, NOW(), 0)";
        $data = [
            ':goods_id' => $goods_id,
            ':goods_name' => $row['name'],
            ':desc' => $desc
        ];
        
        if($DB->exec($sql, $data)) {
            exit('{"code":0,"msg":"报备成功，我们会尽快处理！"}');
        } else {
            exit('{"code":-1,"msg":"报备失败，请稍后再试"}');
        }
    break;

    case 'check_invalid_progress':
        try {
            // 查询所有报备记录，关联商品表获取商品名称
            $sql = "SELECT r.*, t.name as goods_name FROM pre_netdisk_reports r 
                   LEFT JOIN pre_tools t ON r.tid = t.tid 
                   ORDER BY r.addtime DESC LIMIT 50";
            
            $rs = $DB->query($sql);
            $data = array();
            
            while($row = $rs->fetch(PDO::FETCH_ASSOC)){
                // 格式化时间
                $addtime = date('Y-m-d H:i:s', $row['addtime']);
                
                $data[] = array(
                    'id' => $row['id'],
                    'goods_name' => $row['goods_name'],
                    'addtime' => $addtime,
                    'status' => $row['status']
                );
            }
            
            exit(json_encode(array('code'=>0, 'msg'=>'success', 'data'=>$data)));
            
        } catch (Exception $e) {
            // 检查是否是表不存在的错误
            if(strpos($e->getMessage(), "Table") !== false && strpos($e->getMessage(), "doesn't exist") !== false) {
                // 如果表不存在，返回空数据
                exit(json_encode(array('code'=>0, 'msg'=>'success', 'data'=>array())));
            }
            
            exit(json_encode(array('code'=>-1, 'msg'=>'查询失败')));
        }
    break;

    case 'get_invalid_list':
        if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
        
        $data = [];
        $rs = $DB->query("SELECT * FROM pre_pan_invalid ORDER BY id DESC");
        while($row = $rs->fetch(PDO::FETCH_ASSOC)){
            $row['status_text'] = [
                0 => '待处理',
                1 => '已更新',
                2 => '链接有效',
                3 => '下单后自动显示链接'
            ][$row['status']];
            $data[] = $row;
        }
        exit(json_encode(['code'=>0, 'data'=>$data]));
    break;

    case 'set_invalid_status':
        if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
        
        $id = intval($_POST['id']);
        $status = intval($_POST['status']);
        
        if($status<0 || $status>3)
            exit('{"code":-1,"msg":"无效的状态值"}');
        
        if($DB->exec("UPDATE pre_pan_invalid SET status='$status',process_time=NOW() WHERE id='$id'") !== false){
            exit('{"code":0,"msg":"设置成功"}');
        }else{
            exit('{"code":-1,"msg":"设置失败'.dbErrorMsg($DB->error()).'"}');
        }
    break;

    case 'get_chat_messages':
        try {
            $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
            
            // 基础验证
            if(empty($user_id)) {
                throw new Exception('用户ID不能为空');
            }
            
            // 检查数据库连接
            if(!$DB || !($DB instanceof PDO)) {
                throw new Exception('数据库连接失败');
            }
            
            // 查询消息
            $sql = "SELECT id, type, message, UNIX_TIMESTAMP(addtime) as addtime, status 
                   FROM pre_chat_message 
                   WHERE user_id = :user_id 
                   ORDER BY id ASC LIMIT 50";
            
            $stmt = $DB->prepare($sql);
            if(!$stmt) {
                throw new Exception('SQL准备失败：' . implode(', ', $DB->errorInfo()));
            }
            
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
            
            if(!$stmt->execute()) {
                throw new Exception('SQL执行失败：' . implode(', ', $stmt->errorInfo()));
            }
            
            $messages = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $messages[] = [
                    'id' => intval($row['id']),
                    'type' => intval($row['type']),
                    'message' => $row['message'],
                    'addtime' => intval($row['addtime']),
                    'status' => intval($row['status'])
                ];
            }
            
            // 更新消息状态
            try {
                $update_sql = "UPDATE pre_chat_message SET status=1 
                              WHERE user_id=:user_id AND type=1 AND status=0";
                $update_stmt = $DB->prepare($update_sql);
                $update_stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
                $update_stmt->execute();
            } catch(Exception $e) {
                // 更新状态失败不影响主流程
                error_log('更新消息状态失败: ' . $e->getMessage());
            }
            
            exit(json_encode([
                'code' => 0,
                'msg' => 'success',
                'messages' => $messages
            ]));
            
        } catch (Exception $e) {
            error_log('Chat Error: ' . $e->getMessage());
            exit(json_encode([
                'code' => -1,
                'msg' => '系统错误：' . $e->getMessage()
            ]));
        }
    break;

    case 'add_chat_rule':
        if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
        
        $type = intval($_POST['type']);
        $keyword = trim($_POST['keyword']);
        $reply = trim($_POST['reply']);
        $status = intval($_POST['status']);
        
        if(empty($keyword))exit('{"code":-1,"msg":"关键词不能为空"}');
        if(empty($reply))exit('{"code":-1,"msg":"回复内容不能为空"}');
        
        if($DB->exec("INSERT INTO pre_chat_rules (type,keyword,reply,status) VALUES (:type,:keyword,:reply,:status)", [
            ':type'=>$type,
            ':keyword'=>$keyword,
            ':reply'=>$reply,
            ':status'=>$status
        ]) !== false)
            exit('{"code":0,"msg":"添加成功"}');
        else
            exit('{"code":-1,"msg":"添加失败'.dbErrorMsg($DB->error()).'"}');
    break;
    
    case 'edit_chat_rule':
        if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
        
        $id = intval($_POST['id']);
        $type = intval($_POST['type']);
        $keyword = trim($_POST['keyword']);
        $reply = trim($_POST['reply']);
        $status = intval($_POST['status']);
        
        if(empty($keyword))exit('{"code":-1,"msg":"关键词不能为空"}');
        if(empty($reply))exit('{"code":-1,"msg":"回复内容不能为空"}');
        
        if($DB->exec("UPDATE pre_chat_rules SET type=:type,keyword=:keyword,reply=:reply,status=:status WHERE id=:id", [
            ':type'=>$type,
            ':keyword'=>$keyword,
            ':reply'=>$reply,
            ':status'=>$status,
            ':id'=>$id
        ]) !== false)
            exit('{"code":0,"msg":"修改成功"}');
        else
            exit('{"code":-1,"msg":"修改失败'.dbErrorMsg($DB->error()).'"}');
    break;
    
    case 'get_chat_rule':
        if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
        
        $id = intval($_POST['id']);
        $row = $DB->getRow("SELECT * FROM pre_chat_rules WHERE id=:id LIMIT 1", [':id'=>$id]);
        if($row)
            exit(json_encode(['code'=>0, 'type'=>$row['type'], 'keyword'=>$row['keyword'], 'reply'=>$row['reply'], 'status'=>$row['status']]));
        else
            exit('{"code":-1,"msg":"规则不存在"}');
    break;
    
    case 'del_chat_rule':
        if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
        
        $id = intval($_POST['id']);
        if($DB->exec("DELETE FROM pre_chat_rules WHERE id=:id", [':id'=>$id]) !== false)
            exit('{"code":0,"msg":"删除成功"}');
        else
            exit('{"code":-1,"msg":"删除失败'.dbErrorMsg($DB->error()).'"}');
    break;

    case 'send_chat_message':
        $user_id = trim($_POST['user_id']);
        $message = trim($_POST['message']);
        
        if(empty($user_id)) exit('{"code":-1,"msg":"用户ID不能为空"}');
        if(empty($message)) exit('{"code":-1,"msg":"消息不能为空"}');
        
        // 插入用户消息
        $DB->exec("INSERT INTO pre_chat_message (user_id,type,message,status,addtime) VALUES (:user_id,0,:message,0,NOW())", [
            ':user_id'=>$user_id,
            ':message'=>$message
        ]);
        
        // 检查是否需要自动回复
        if($conf['chat_auto_reply']==1){
            // 检查关键词自动回复
            $rs = $DB->query("SELECT * FROM pre_chat_rules WHERE status=1 ORDER BY type ASC");
            while($row = $rs->fetch(PDO::FETCH_ASSOC)){
                if($row['type']==1){ // 精准匹配
                    if($message == $row['keyword']){
                        $DB->exec("INSERT INTO pre_chat_message (user_id,type,message,status,addtime) VALUES (:user_id,1,:message,0,NOW())", [
                            ':user_id'=>$user_id,
                            ':message'=>$row['reply']
                        ]);
                        break;
                    }
                }else{ // 模糊匹配
                    if(strpos($message, $row['keyword']) !== false){
                        $DB->exec("INSERT INTO pre_chat_message (user_id,type,message,status,addtime) VALUES (:user_id,1,:message,0,NOW())", [
                            ':user_id'=>$user_id,
                            ':message'=>$row['reply']
                        ]);
                        break;
                    }
                }
            }
        }
        
        exit('{"code":0,"msg":"发送成功"}');
    break;

    case 'send_chat_message_admin':
        // 检查是否是发送默认消息
        $user_id = trim($_POST['user_id']);
        $message = trim($_POST['message']);
        $is_default = isset($_POST['is_default']) && $_POST['is_default'] == 1;
        
        if(empty($user_id)) exit('{"code":-1,"msg":"用户ID不能为空"}');
        if(empty($message)) exit('{"code":-1,"msg":"消息不能为空"}');
        
        // 如果不是默认消息，需要验证登录
        if(!$is_default && $islogin!=1) exit('{"code":-1,"msg":"未登录"}');
        
        // 检查是否是系统设置的默认消息
        if($is_default && $message != $conf['chat_first_msg']) {
            exit('{"code":-1,"msg":"非法请求"}');
        }
        
        // 插入管理员消息
        if($DB->exec("INSERT INTO pre_chat_message (user_id,type,message,status,addtime) VALUES (:user_id,1,:message,0,NOW())", [
            ':user_id'=>$user_id,
            ':message'=>$message
        ]) !== false){
            exit('{"code":0,"msg":"发送成功"}');
        }else{
            exit('{"code":-1,"msg":"发送失败'.dbErrorMsg($DB->error()).'"}');
        }
    break;

    case 'set':
        if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
        
        $chat_open = intval($_POST['chat_open']);
        $chat_auto_reply = intval($_POST['chat_auto_reply']);
        $chat_first_msg = trim($_POST['chat_first_msg']);
        $chat_style = trim($_POST['chat_style']);
        $chat_position = trim($_POST['chat_position']);
        
        if(empty($chat_first_msg))exit('{"code":-1,"msg":"首次打开回复内容不能为空"}');
        
        saveSetting('chat_open', $chat_open);
        saveSetting('chat_auto_reply', $chat_auto_reply);
        saveSetting('chat_first_msg', $chat_first_msg);
        saveSetting('chat_style', $chat_style);
        saveSetting('chat_position', $chat_position);
        
        $CACHE->clear();
        exit('{"code":0,"msg":"保存成功"}');
    break;

    case 'clear_chat':
        if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
        
        $user_id = trim($_POST['user_id']);
        if(empty($user_id)) exit('{"code":-1,"msg":"用户ID不能为空"}');
        
        if($DB->exec("DELETE FROM pre_chat_message WHERE user_id=:user_id", [':user_id'=>$user_id]) !== false){
            exit('{"code":0,"msg":"删除成功"}');
        }else{
            exit('{"code":-1,"msg":"删除失败'.dbErrorMsg($DB->error()).'"}');
        }
    break;
    
    case 'clear_all_chat':
        if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
        
        if($DB->exec("DELETE FROM pre_chat_message") !== false){
            exit('{"code":0,"msg":"清空成功"}');
        }else{
            exit('{"code":-1,"msg":"清空失败'.dbErrorMsg($DB->error()).'"}');
        }
    break;

    // 网盘失效报备
    case 'reportNetdiskFail':
        $tid = intval($_POST['tid']);
        $reason = trim(htmlspecialchars($_POST['reason']));
        
        // 检查商品是否存在
        $row = $DB->getRow("SELECT * FROM pre_tools WHERE tid='$tid' LIMIT 1");
        if(!$row) {
            exit('{"code":-1,"msg":"商品不存在"}');
        }
        
        // 检查是否已经报备过
        $count = $DB->getColumn("SELECT count(*) FROM pre_netdisk_reports WHERE tid='$tid' AND status<2");
        if($count > 0) {
            exit('{"code":-1,"msg":"该商品已有人报备，正在处理中"}');
        }
        
        // 检查数据表是否存在
        try {
            $DB->query("SELECT 1 FROM pre_netdisk_reports LIMIT 1");
        } catch (Exception $e) {
            // 如果表不存在，创建表
            $sql = "CREATE TABLE IF NOT EXISTS `pre_netdisk_reports` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `tid` int(11) NOT NULL COMMENT '商品ID',
                `reason` text COMMENT '报备原因',
                `addtime` int(11) NOT NULL COMMENT '添加时间',
                `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0待处理，1处理中，2已解决',
                PRIMARY KEY (`id`),
                KEY `tid` (`tid`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            
            try {
                $DB->exec($sql);
                error_log("创建表成功: pre_netdisk_reports");
            } catch (Exception $e) {
                error_log("创建表失败: " . $e->getMessage());
                // 尝试使用不同的字符集
                $sql = "CREATE TABLE IF NOT EXISTS `pre_netdisk_reports` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `tid` int(11) NOT NULL COMMENT '商品ID',
                    `reason` text COMMENT '报备原因',
                    `addtime` int(11) NOT NULL COMMENT '添加时间',
                    `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0待处理，1处理中，2已解决',
                    PRIMARY KEY (`id`),
                    KEY `tid` (`tid`),
                    KEY `status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                $DB->exec($sql);
            }
        }
        
        // 插入报备记录
        try {
            // 先检查表是否存在
            $tableExists = false;
            try {
                $result = $DB->query("SHOW TABLES LIKE 'pre_netdisk_reports'");
                $tableExists = ($result->rowCount() > 0);
            } catch (Exception $e) {
                error_log("检查表是否存在失败: " . $e->getMessage());
            }
            
            if (!$tableExists) {
                // 如果表不存在，创建表
                $sql = "CREATE TABLE IF NOT EXISTS `pre_netdisk_reports` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `tid` int(11) NOT NULL,
                    `reason` text,
                    `addtime` int(11) NOT NULL,
                    `status` tinyint(1) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`),
                    KEY `tid` (`tid`),
                    KEY `status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                $DB->exec($sql);
                error_log("创建表成功: pre_netdisk_reports");
            }
            
            // 插入数据
            $sql = "INSERT INTO pre_netdisk_reports (tid, reason, addtime, status) VALUES (:tid, :reason, :addtime, 0)";
            $data = [
                ':tid' => $tid,
                ':reason' => $reason,
                ':addtime' => time()
            ];
            
            if($DB->exec($sql, $data)) {
                exit('{"code":0,"msg":"报备成功，感谢您的反馈！"}');
            } else {
                $error = dbErrorMsg($DB->error());
                error_log("插入数据失败: " . $error);
                exit('{"code":-1,"msg":"报备失败，数据库错误：'.$error.'"}');
            }
        } catch (Exception $e) {
            error_log("报备失败异常: " . $e->getMessage());
            exit('{"code":-1,"msg":"报备失败，异常信息：'.$e->getMessage().'"}');
        }
    break;

    default:
        exit('{"code":-4,"msg":"No Act"}');
} 