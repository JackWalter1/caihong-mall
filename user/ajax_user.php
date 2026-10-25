<?php
include("../includes/common.php");

$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

@header('Content-Type: application/json; charset=UTF-8');

if (!checkRefererHost()) {
    exit('{"code":403}');
}
if (!$islogin2) {
    exit('{"code":-1,"msg":"未登录"}');
}

// 价格相关操作仅限分站用户（power>0），普通用户无权操作
$price_actions = ['up_price', 'reset_price', 'batch_update_price', 'get_price_history', 'restore_last_price', 'restore_from_history'];
if (in_array($act, $price_actions) && $userrow['power'] == 0) {
    exit('{"code":-1,"msg":"你没有权限使用此功能"}');
}

switch ($act) {
    case 'refund':
        // 禁用用户自助退款功能
        exit('{"code":-1,"msg":"当前站点未开启用户自助退款功能"}');
        break;
    case 'setpwd':
        if (substr($userrow['user'], 0, 3) != 'qq_') {
            exit('{"code":-1,"msg":"请勿重复提交"}');
        }
        $user = trim(htmlspecialchars(strip_tags(daddslashes($_POST['user']))));
        $pwd  = trim(htmlspecialchars(strip_tags(daddslashes($_POST['pwd']))));
        if (!preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/', $user)) {
            exit('{"code":-1,"msg":"用户名只能为英文、数字与汉字！"}');
        } elseif ($DB->getRow("SELECT zid FROM pre_site WHERE user=:user LIMIT 1", [':user' => $user])) {
            exit('{"code":-1,"msg":"用户名已存在！"}');
        } elseif (strlen($pwd) < 6) {
            exit('{"code":-1,"msg":"密码不能低于6位"}');
        } elseif ($pwd == $user) {
            exit('{"code":-1,"msg":"用户名和密码不能相同！"}');
        }
        if ($DB->exec("UPDATE pre_site SET user=:user,pwd=:pwd WHERE zid=:zid",
            [':user' => $user, ':pwd' => $pwd, ':zid' => $userrow['zid']])) {
            $session = md5($user . $pwd . $password_hash);
            $token   = authcode("{$userrow['zid']}\t{$session}", 'ENCODE', SYS_KEY);
            ob_clean();
            setcookie("user_token", $token, time() + 604800, '/');
            exit('{"code":0,"msg":"保存成功"}');
        } else {
            exit('{"code":-1,"msg":"保存失败！' . $DB->error() . '"}');
        }
        break;
    case 'up_price':
        unset($islogin2);
        $price_obj = new \lib\Price($userrow['zid'], $userrow);
        $up        = intval($_POST['up']);
        $cid       = intval($_POST['cid']);
        $kw        = trim(daddslashes($_POST['kw']));
        
        if ($up <= 0) {
            exit('{"code":-1,"msg":"输入值不正确"}');
        }
        if ($conf['fenzhan_pricelimit'] == 1 && $up > 100) {
            exit('{"code":-1,"msg":"商品售价最高不能超过原售价的2倍"}');
        }
        
        // 构建查询条件
        $where = "active=1";
        if ($cid > 0) {
            $where .= " AND cid='{$cid}'";
        }
        if (!empty($kw)) {
            $where .= " AND name LIKE '%{$kw}%'";
        }
        
        $sql  = $DB->query("select * from pre_tools where {$where}");
        $data = [];
        while ($row = $sql->fetch()) {
            if ($row['price'] == 0) {
                continue;
            }
            if (strpos($row['name'], '免费') !== false) {
                continue;
            }
            $price_obj->setToolInfo($row['tid'], $row);
            $price                      = $price_obj->getToolPrice($row['tid']);
            $a                          = (float)$up / 100;
            $new_price                  = round($price * ($a + 1), 2);
            
            // 启用价格限制时，跳过会超过上限的商品
            if ($conf['fenzhan_pricelimit'] == 1) {
                $mainprice = $price_obj->getMainPrice();
                if (($mainprice > 1 && $new_price > $mainprice * 2) || ($mainprice <= 1 && $new_price > 2)) {
                    continue;
                }
            }
            
            $data[$row['tid']]['price'] = $new_price;
        }
        
        // 保存历史记录
        $price_history = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$userrow['zid']}'");
        $original_price = [];
        while ($row = $price_history->fetch()) {
            $original_price[$row['tid']] = [
                'price' => $row['price'],
                'cost' => $row['cost'],
                'cost2' => $row['cost2'],
                'del' => $row['del']
            ];
        }
        
        $history_desc = "价格提升：" . $up . "%";
        $history_data = json_encode($original_price);
        
        // 检查并创建历史记录表
        $DB->exec("CREATE TABLE IF NOT EXISTS pre_price_history (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            zid INT UNSIGNED NOT NULL,
            price_data TEXT NOT NULL,
            description VARCHAR(255) NOT NULL,
            create_time DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        
        // 插入历史记录
        $DB->exec("INSERT INTO pre_price_history (zid, price_data, description, create_time) VALUES ('{$userrow['zid']}', '{$history_data}', '{$history_desc}', NOW())");
        
        // 使用事务确保操作的原子性
        $DB->beginTransaction();
        try {
            $updated_count = 0;
            foreach ($data as $tid => $price_info) {
                $price = $price_info['price'];
                
                // 使用INSERT ON DUPLICATE KEY UPDATE语法，确保数据的唯一性
                $sql = "INSERT INTO pre_site_price (zid, tid, price, create_time, update_time) 
                       VALUES (:zid, :tid, :price, NOW(), NOW()) 
                       ON DUPLICATE KEY UPDATE 
                       price = :price, update_time = NOW()";
                
                $data_insert = [
                    ':zid' => $userrow['zid'],
                    ':tid' => $tid,
                    ':price' => $price
                ];
                
                $DB->exec($sql, $data_insert);
                $updated_count++;
            }
            
            $DB->commit();
            exit('{"code":0,"msg":"价格提升成功，共修改了'.count($data).'个商品"}');
        } catch (Exception $e) {
            $DB->rollback();
            exit('{"code":-1,"msg":"价格提升失败：' . $e->getMessage() . '"}');
        }
        break;
    
    case 'reset_price':
        unset($islogin2);
        $cid = intval($_POST['cid']);
        $name = trim(daddslashes($_POST['name']));
        $status = intval($_POST['status']);
        
        // 构建查询条件
        $where = "active=1";
        
        // 分类条件
        if (!empty($cid) && $cid != '0') {
            // 处理多选分类，转换为数组
            $cid_array = explode(',', $cid);
            // 过滤掉无效值
            $cid_array = array_filter($cid_array, function($id) {
                return is_numeric($id) && $id > 0;
            });
            if (!empty($cid_array)) {
                $where .= " AND cid IN ('" . implode("','", $cid_array) . "')";
            }
        }
        
        // 名称条件
        if (!empty($name)) {
            $where .= " AND name LIKE '%$name%'";
        }
        
        // 状态条件
        if ($status == 1) {
            $where .= " AND close=0";
        } elseif ($status == 2) {
            $where .= " AND close=1";
        }
        
        // 查询符合条件的商品
        $sql = $DB->query("SELECT tid FROM pre_tools WHERE {$where}");
        $tids = [];
        while ($row = $sql->fetch()) {
            $tids[] = $row['tid'];
        }
        
        $reset_count = 0;
        if (!empty($tids)) {
            // 删除前保存历史记录
            $price_history = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$userrow['zid']}'");
            $original_price = [];
            while ($row = $price_history->fetch()) {
                $original_price[$row['tid']] = [
                    'price' => $row['price'],
                    'cost' => $row['cost'],
                    'cost2' => $row['cost2'],
                    'del' => $row['del']
                ];
            }
            $history_desc = "恢复价格（按条件重置）";
            $history_data = json_encode($original_price);
            
            // 确保历史记录表存在
            $DB->exec("CREATE TABLE IF NOT EXISTS pre_price_history (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                zid INT UNSIGNED NOT NULL,
                price_data TEXT NOT NULL,
                description VARCHAR(255) NOT NULL,
                create_time DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            
            // 插入历史记录
            $DB->exec("INSERT INTO pre_price_history (zid, price_data, description, create_time) VALUES ('{$userrow['zid']}', '{$history_data}', '{$history_desc}', NOW())");
            
            // 从pre_site_price表中删除这些商品的价格记录
            $tid_list = implode(',', $tids);
            $reset_count = $DB->exec("DELETE FROM pre_site_price WHERE zid='{$userrow['zid']}' AND tid IN ({$tid_list})");
        }
        
        if ($reset_count > 0) {
            exit('{"code":0,"msg":"成功恢复 '.$reset_count.' 个商品的价格"}');
        } else {
            exit('{"code":0,"msg":"没有找到需要恢复价格的商品"}');
        }
        break;
    
    case 'batch_update_price':
        unset($islogin2);
        $price_obj = new \lib\Price($userrow['zid'], $userrow);
        
        // 获取参数
        $operation = $_POST['operation'];
        $price_type = $_POST['price_type'];
        $batch_type = $_POST['batch_type'];
        $value = floatval($_POST['value']);
        $cid = trim($_POST['cid']);
        $name = trim($_POST['name']);
        $status = intval($_POST['status']);
        
        // 验证参数
        if ($operation != 'add' && $operation != 'subtract') {
            exit('{"code":-1,"msg":"操作类型错误"}');
        }
        
        if ($price_type != 'price' && $price_type != 'cost') {
            exit('{"code":-1,"msg":"价格类型错误"}');
        }
        
        // 修改代理价格需要 power==2
        if ($price_type == 'cost' && $userrow['power'] != 2) {
            exit('{"code":-1,"msg":"你没有权限修改代理价格"}');
        }
        
        if ($batch_type != 'fixed' && $batch_type != 'percent') {
            exit('{"code":-1,"msg":"批量类型错误"}');
        }
        
        if ($batch_type == 'percent' && $value <= 0) {
            exit('{"code":-1,"msg":"百分比必须为正数"}');
        }
        
        // 构建查询条件
        $where = "active=1";
        
        // 分类条件
        if (!empty($cid) && $cid != '0') {
            // 处理多选分类，转换为数组
            $cid_array = explode(',', $cid);
            // 过滤掉无效值
            $cid_array = array_filter($cid_array, function($id) {
                return is_numeric($id) && $id > 0;
            });
            if (!empty($cid_array)) {
                $where .= " AND cid IN ('" . implode("','", $cid_array) . "')";
            }
        }
        
        // 名称条件
        if (!empty($name)) {
            $where .= " AND name LIKE '%" . daddslashes($name) . "%'";
        }
        
        // 状态条件
        if ($status == 1) {
            $where .= " AND close=0";
        } elseif ($status == 2) {
            $where .= " AND close=1";
        }
        
        // 查询符合条件的商品数量，移除数量限制
        $total_count = $DB->getColumn("SELECT count(*) FROM pre_tools WHERE {$where}");
        
        // 查询符合条件的商品
        $sql = $DB->query("SELECT * FROM pre_tools WHERE {$where}");
        $data = [];
        $updated_count = 0;
        
        while ($row = $sql->fetch()) {
            // 跳过价格为0或名称包含"免费"的商品
            if ($row['price'] == 0 || strpos($row['name'], '免费') !== false) {
                continue;
            }
            
            // 获取当前价格（根据价格类型）
            $price_obj->setToolInfo($row['tid'], $row);
            if ($price_type == 'cost') {
                $current_price = $price_obj->getToolCost($row['tid']);
            } else {
                $current_price = $price_obj->getToolPrice($row['tid']);
            }
            
            // 计算新价格
            $new_price = 0;
            if ($batch_type == 'fixed') {
                if ($operation == 'add') {
                    $new_price = round($current_price + $value, 2);
                } else {
                    $new_price = round($current_price - $value, 2);
                }
            } else {
                $percentage = $value / 100;
                if ($operation == 'add') {
                    $new_price = round($current_price * (1 + $percentage), 2);
                } else {
                    $new_price = round($current_price * (1 - $percentage), 2);
                }
            }
            
            // 确保价格不低于最低成本价
            if ($price_type == 'cost') {
                $floor_price = $price_obj->getToolCost2($row['tid']);
                if ($new_price < $floor_price) {
                    continue;
                }
            } else {
                $cost_price = $price_obj->getToolCost($row['tid']);
                if ($new_price < $cost_price) {
                    continue;
                }
            }
            
            // 应用价格限制
            if ($conf['fenzhan_pricelimit'] == 1) {
                if ($price_type == 'cost') {
                    $maincost = $price_obj->getMainCost();
                    if (($maincost > 1 && $new_price > $maincost * 2) || ($maincost <= 1 && $new_price > 2)) {
                        continue;
                    }
                } else {
                    $main_price = $price_obj->getMainPrice();
                    if (($main_price > 1 && $new_price > $main_price * 2) || ($main_price <= 1 && $new_price > 2)) {
                        continue;
                    }
                }
            }
            
            // 更新价格
            if ($price_type == 'cost') {
                $data[$row['tid']]['cost'] = $new_price;
            } else {
                $data[$row['tid']]['price'] = $new_price;
            }
            $updated_count++;
        }
        
        // 保存更新
        if ($updated_count > 0) {
            // 添加调试日志
            error_log("[DEBUG] 开始批量修改价格，ZID: {$userrow['zid']}, 待更新商品数量: {$updated_count}");
            
            // 从pre_site_price表中获取当前价格数据作为备份
            $price_history = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$userrow['zid']}'");
            $original_price = [];
            while ($row = $price_history->fetch()) {
                $original_price[$row['tid']] = [
                    'price' => $row['price'],
                    'cost' => $row['cost'],
                    'cost2' => $row['cost2'],
                    'del' => $row['del']
                ];
            }
            
            // 记录原始数据信息
            error_log("[DEBUG] 原始数据数量: " . count($original_price));
            
            // 保存历史记录
            $history_desc = "批量" . ($operation == 'add' ? "加价" : "降价") . "：" . ($batch_type == 'fixed' ? $value . "元" : $value . "%");
            $history_data = json_encode($original_price); // 使用JSON格式保存原始数据作为历史记录
            
            // 检查并创建历史记录表
            $DB->exec("CREATE TABLE IF NOT EXISTS pre_price_history (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                zid INT UNSIGNED NOT NULL,
                price_data TEXT NOT NULL,
                description VARCHAR(255) NOT NULL,
                create_time DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            
            // 插入历史记录
            $DB->exec("INSERT INTO pre_price_history (zid, price_data, description, create_time) VALUES ('{$userrow['zid']}', '{$history_data}', '{$history_desc}', NOW())");
            
            // 使用事务确保操作的原子性
            $DB->beginTransaction();
            try {
                // 将批量修改的价格数据保存到新表中
                foreach ($data as $tid => $price_info) {
                    if (isset($price_info['cost'])) {
                        // 更新代理价格
                        $cost_price = $price_info['cost'];
                        $sql = "INSERT INTO pre_site_price (zid, tid, cost, create_time, update_time) 
                               VALUES (:zid, :tid, :cost, NOW(), NOW()) 
                               ON DUPLICATE KEY UPDATE 
                               cost = :cost, update_time = NOW()";
                        $data_insert = [
                            ':zid' => $userrow['zid'],
                            ':tid' => $tid,
                            ':cost' => $cost_price
                        ];
                    } else {
                        // 更新销售价格
                        $price = $price_info['price'];
                        $sql = "INSERT INTO pre_site_price (zid, tid, price, create_time, update_time) 
                               VALUES (:zid, :tid, :price, NOW(), NOW()) 
                               ON DUPLICATE KEY UPDATE 
                               price = :price, update_time = NOW()";
                        $data_insert = [
                            ':zid' => $userrow['zid'],
                            ':tid' => $tid,
                            ':price' => $price
                        ];
                    }
                    
                    $DB->exec($sql, $data_insert);
                }
                
                $DB->commit();
                error_log("[DEBUG] 批量修改成功，共更新了 {$updated_count} 个商品");
                exit("{\"code\":0,\"msg\":\"批量修改成功，共更新了 {$updated_count} 个商品\"}");
            } catch (Exception $e) {
                $DB->rollback();
                error_log("[DEBUG] 批量修改失败: " . $e->getMessage());
                exit('{"code":-1,"msg":"批量修改失败：' . $e->getMessage() . '"}');
            }
        } else {
            exit('{"code":-1,"msg":"没有找到符合条件的商品或修改后价格不符合要求"}');
        }
        break;
    case 'create_url':
        $force = trim(daddslashes($_GET['force']));
        if (!$userrow['domain']) {
            exit('{"code":-1,"msg":"当前分站还未绑定域名"}');
        }
        $url = 'http://' . $userrow['domain'] . '/';
        if ($force == 1) {
            $turl = fanghongdwz($url, true);
        } else {
            $turl = fanghongdwz($url);
        }
        if ($turl == $url) {
            $result = ['code' => -1, 'msg' => '生成失败，请联系站长更换接口'];
        } elseif (strpos($turl, '/')) {
            $result = ['code' => 0, 'msg' => 'succ', 'url' => $turl];
        } else {
            $result = ['code' => -1, 'msg' => '生成失败：' . $turl];
        }
        exit(json_encode($result));
        break;
    case 'qiandao':
        if (!$conf['qiandao_reward']) {
            exit('{"code":-1,"msg":"当前站点未开启签到功能"}');
        }
        if (!isset($_SESSION['isqiandao']) || $_SESSION['isqiandao'] != $userrow['zid']) {
            exit('{"code":-1,"msg":"校验失败，请刷新页面重试"}');
        }
        $day     = date("Y-m-d");
        $lastday = date("Y-m-d", strtotime("-1 day"));

        if ($DB->getRow("SELECT * FROM pre_qiandao WHERE zid='{$userrow['zid']}' AND date='$day' ORDER BY id DESC LIMIT 1")) {
            exit('{"code":-1,"msg":"今天已经签到过了, 明天在来吧！"}');
        }
        if ($conf['qiandao_limitip'] == 1 && $DB->getRow("SELECT * FROM pre_qiandao WHERE ip='{$clientip}' AND date='$day' ORDER BY id DESC LIMIT 1")) {
            exit('{"code":-1,"msg":"您的IP今天已经签到过了，明天在来吧！"}');
        }
        if ($row = $DB->getRow("SELECT * FROM pre_qiandao WHERE zid='{$userrow['zid']}' AND date='$lastday' ORDER BY id DESC LIMIT 1")) {
            $continue = $row['continue'] + 1;
        } else {
            $continue = 1;
        }
        if ($continue > $conf['qiandao_day']) {
            $continue = $conf['qiandao_day'];
        }
        $reward = $conf['qiandao_reward'];
        if (strpos($reward, '|')) {
            $reward = explode('|', $reward);
            $reward = $reward[$userrow['power']];
            if (!$reward) {
                exit('{"code":-1,"msg":"未配置好签到奖励余额初始值"}');
            }
        }
        if ($conf['qiandao_mult'] > 0) {
            for ($i = 1; $i < $continue; $i++) {
                $reward *= $conf['qiandao_mult'];
            }
        }
        $reward = round($reward, 2);
        $sql    = "INSERT INTO `pre_qiandao` (`zid`,`qq`,`reward`,`date`,`time`,`continue`,`ip`) VALUES ('" . $userrow['zid'] . "','" . $userrow['qq'] . "','" . $reward . "','" . $day . "','" . $date . "','" . $continue . "','" . $clientip . "')";
        if ($DB->exec($sql)) {
            unset($_SESSION['isqiandao']);
            changeUserMoney($userrow['zid'], $reward, true, '赠送', '您今天签到获得了' . $reward . '元奖励');
            $result = ['code' => 0, 'msg' => '签到成功，获得' . $reward . '元现金奖励！'];
        } else {
            $result = ['code' => -1, 'msg' => '签到失败' . $DB->error()];
        }
        exit(json_encode($result));
        break;
    case 'qdcount':
        $day         = date("Y-m-d");
        $lastday     = date("Y-m-d", strtotime("-1 day"));
        $count1      = $DB->getColumn("SELECT count(*) FROM pre_qiandao WHERE date='$day'");
        $count2      = $DB->getColumn("SELECT count(*) FROM pre_qiandao WHERE date='$lastday'");
        $count3      = $DB->getColumn("SELECT count(*) FROM pre_qiandao");
        $rewardcount = $DB->getColumn("SELECT sum(reward) FROM pre_qiandao WHERE zid='{$userrow['zid']}'");
        $result      = [
            "count1"      => $count1,
            "count2"      => $count2,
            "count3"      => $count3,
            "rewardcount" => round($rewardcount, 2)
        ];
        exit(json_encode($result));
        break;
    case 'msg':
        if ($userrow['power'] == 2) {
            $type = '0,2,4';
        } elseif ($userrow['power'] == 1) {
            $type = '0,2,3';
        } else {
            $type = '0,1';
        }
        $msgread = trim($userrow['msgread'], ',');
        if (empty($msgread)) {
            $msgread = '0';
        }
        $count        = $DB->getColumn("SELECT count(*) FROM pre_message WHERE id NOT IN ($msgread) and type IN ($type)");
        $count2       = $DB->getColumn("SELECT count(*) FROM pre_workorder WHERE zid='{$userrow['zid']}' AND status=1");
        $thtime       = date("Y-m-d") . ' 00:00:00';
        $income_today = $DB->getColumn("SELECT sum(point) FROM pre_points WHERE zid='{$userrow['zid']}' AND action='提成' AND addtime>'$thtime'");
        exit('{"code":0,"count":' . $count . ',"count2":' . $count2 . ',"income_today":"' . round($income_today,
                2) . '"}');
        break;
    case 'msginfo':
        if ($userrow['power'] == 2) {
            $type = [0, 2, 4];
        } elseif ($userrow['power'] == 1) {
            $type = [0, 2, 3];
        } else {
            $type = [0, 1];
        }
        $id  = intval($_GET['id']);
        $row = $DB->getRow("SELECT * FROM pre_message WHERE id='$id' AND active=1 LIMIT 1");
        if (!$row) {
            exit('{"code":-1,"msg":"当前消息不存在！"}');
        }
        if (!in_array($row['type'], $type)) {
            exit('{"code":-1,"msg":"你没有权限查看此消息内容"}');
        }
        if (!in_array($id, explode(',', $userrow['msgread']))) {
            $msgread_n = $userrow['msgread'] . $id . ',';
            $DB->exec("UPDATE pre_message SET count=count+1 WHERE id='$id'");
            $DB->exec("UPDATE pre_site SET msgread='" . $msgread_n . "' WHERE zid='{$userrow['zid']}'");
        }
        $result = [
            "code"    => 0,
            "msg"     => "succ",
            "title"   => $row['title'],
            "type"    => $row['type'],
            "content" => $row['content'],
            "date"    => $row['addtime']
        ];
        exit(json_encode($result));
        break;
    case 'msg_read_all':
        if ($userrow['power'] == 2) {
            $type = [0, 2, 4];
        } elseif ($userrow['power'] == 1) {
            $type = [0, 2, 3];
        } else {
            $type = [0, 1];
        }
        $type = implode(',', $type);
        $rs   = $DB->query("SELECT id FROM pre_message WHERE `type` in ({$type})");
        $id   = "";
        foreach ($rs as $key => $value) {
            $id .= $value['id'] . ',';
        }

        if ($id) {
            $DB->exec("UPDATE pre_site SET msgread='" . $id . "' WHERE zid='{$userrow['zid']}'");
        }
        $result = ["code" => 0, "msg" => "succ"];
        exit(json_encode($result));
        break;
    case 'recharge':
        $value    = daddslashes($_GET['value']);
        $trade_no = date("YmdHis") . rand(111, 999);
        if (!is_numeric($value) || !preg_match('/^[0-9.]+$/', $value)) {
            exit('{"code":-1,"msg":"提交参数错误！"}');
        }
        if ($conf['recharge_min'] > 0 && $value < $conf['recharge_min']) {
            exit('{"code":-1,"msg":"最低充值' . $conf['recharge_min'] . '元！"}');
        }
        $sql  = "INSERT INTO `pre_pay` (`trade_no`,`tid`,`input`,`name`,`money`,`ip`,`addtime`,`status`) VALUES (:trade_no, :tid, :input, :name, :money, :ip, NOW(), 0)";
        $data = [
            ':trade_no' => $trade_no,
            ':tid'      => -1,
            ':input'    => (string)$userrow['zid'],
            ':name'     => '在线充值余额',
            ':money'    => $value,
            ':ip'       => $clientip
        ];
        if ($DB->exec($sql, $data)) {
            exit('{"code":0,"msg":"提交订单成功！","trade_no":"' . $trade_no . '","money":"' . $value . '","name":"在线充值余额"}');
        } else {
            exit('{"code":-1,"msg":"提交订单失败！' . $DB->error() . '"}');
        }
        break;
    case 'setClass':
        $cid       = intval($_GET['cid']);
        $active    = intval($_GET['active']);
        $classhide = explode(',', $userrow['class']);
        if ($active == 1 && in_array($cid, $classhide)) {
            $classhide = array_diff($classhide, [$cid]);
        } elseif ($active == 0 && !in_array($cid, $classhide)) {
            $classhide[] = $cid;
        }
        $class = implode(',', $classhide);
        $DB->exec("UPDATE `pre_site` SET `class`='{$class}' WHERE zid='{$userrow['zid']}'");
        exit('{"code":0}');
        break;
    case 'uploadimg':
        if ($_POST['do'] == 'upload') {
            // 检查文件类型和大小
            $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp');
            $file_type = $_FILES['file']['type'];
            $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            $allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp');
            $max_size = 2 * 1024 * 1024; // 2MB
            
            // 检查文件类型和扩展名
            if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
                exit('{"code":-1,"msg":"只允许上传JPG、PNG、GIF、WEBP、BMP格式的图片文件！"}');
            }
            
            // 检查文件大小
            if ($_FILES['file']['size'] > $max_size) {
                exit('{"code":-1,"msg":"文件大小不能超过2MB！"}');
            }
            
            // 检查文件是否为真实图片
            $image_info = getimagesize($_FILES['file']['tmp_name']);
            if (!$image_info) {
                exit('{"code":-1,"msg":"请上传真实的图片文件！"}');
            }
            
            // 生成更安全的文件名：结合时间戳、随机数和文件内容哈希
            $filename = md5(uniqid(mt_rand(), true) . time() . file_get_contents($_FILES['file']['tmp_name'])) . '.' . $file_ext;
            $fileurl  = 'assets/img/workorder/' . $filename;
            
            // 确保上传目录存在
            if (!is_dir(ROOT . 'assets/img/workorder/')) {
                mkdir(ROOT . 'assets/img/workorder/', 0755, true);
            }
            
            // 使用move_uploaded_file更安全
            if (move_uploaded_file($_FILES['file']['tmp_name'], ROOT . $fileurl)) {
                compress_image(ROOT . $fileurl); // 自动压缩，加快前端加载
                exit('{"code":0,"msg":"succ","url":"' . $fileurl . '"}');
            } else {
                exit('{"code":-1,"msg":"上传失败，请确保有本地写入权限"}');
            }
        }
        exit('{"code":-1,"msg":"null"}');
        break;
    case 'usekm':
        if (!$conf['fenzhan_jiakuanka']) {
            exit('{"code":-1,"msg":"未开启使用加款卡功能"}');
        }
        $km    = trim(daddslashes($_POST['km']));
        $myrow = $DB->getRow("SELECT * FROM pre_kms WHERE km='$km' LIMIT 1");
        if (!$myrow) {
            exit('{"code":-1,"msg":"此卡密不存在！"}');
        } elseif ($myrow['status'] == 1) {
            exit('{"code":-1,"msg":"此卡密已被使用！"}');
        }
        $money = $myrow['money'];
        // 计算充值返利
        $rebate_money = 0;
        $rebate_rate = 0;
        if (isset($conf['recharge_rebate_enabled']) && $conf['recharge_rebate_enabled'] == 1) {
            $rebate_rate = isset($conf['recharge_rebate_rate']) ? $conf['recharge_rebate_rate'] : 3;
            $rebate_money = round($money * ($rebate_rate / 100), 2);
        }
        if ($DB->exec("UPDATE `pre_kms` SET `status`=1 WHERE `kid`='{$myrow['kid']}'")) {
            $DB->exec("UPDATE `pre_kms` SET `zid` ='{$userrow['zid']}',`usetime` ='" . $date . "' WHERE `kid`='{$myrow['kid']}'");
            // 充值主金额
            $rs = changeUserMoney($userrow['zid'], $money, true, '充值', '你使用加款卡充值了' . $money . '元余额');
            // 发放返利
            if ($rebate_money > 0) {
                changeUserMoney($userrow['zid'], $rebate_money, true, "充值返利", "你使用加款卡充值了" . $money . "元余额，获得" . $rebate_rate . "%返利" . $rebate_money . "元已到账，感谢充值！");
            }
            if ($rs) {
                if ($rebate_money > 0) {
                    exit('{"code":0,"msg":"成功充值' . $money . '元余额，额外获得' . $rebate_money . '元返利！"}');
                } else {
                    exit('{"code":0,"msg":"成功充值' . $money . '元余额！"}');
                }
            }
        }
        exit('{"code":-1,"msg":"充值失败' . $DB->error() . '"}');
        break;
    case 'app_upload':
        if (!$conf['appcreate_open'] || !$conf['appcreate_key']) {
            exit('{"code":-1,"msg":"未开启分站自助生成APP功能"}');
        }
        if (!$conf['appcreate_diy']) {
            exit('{"code":-1,"msg":"未开启自定义图标和启动图"}');
        }
        $file = $_FILES['file'];
        $type = strtolower(substr($file['name'], strrpos($file['name'], '.') + 1));
        if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
            exit(json_encode(['code' => -1, 'msg' => '上传图片格式错误']));
        }
        $path = sys_get_temp_dir() . '/' . md5_file($file['tmp_name']) . '.' . $type;
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            exit(json_encode(['code' => -1, 'msg' => '上传失败']));
        }
        $app = new \lib\AppCreate($conf['appcreate_key']);
        if ($app->uploadimg($path)) {
            exit(json_encode(['code' => 0, 'msg' => '图片上传成功', 'fileid' => $app->fileid]));
        } else {
            exit(json_encode(['code' => -1, 'msg' => $app->msg]));
        }
        break;
    case 'app_submit':
        if (!$conf['appcreate_open'] || !$conf['appcreate_key']) {
            exit('{"code":-1,"msg":"未开启分站自助生成APP功能"}');
        }
        $price = $userrow['power'] == 2 ? $conf['appcreate_price2'] : $conf['appcreate_price'];
        if ($price > 0 && $userrow['rmb'] < $price) {
            exit('{"code":-1,"msg":"你的余额不足，生成APP需要' . $price . '元"}');
        }
        $app  = new \lib\AppCreate($conf['appcreate_key']);
        $name = trim(daddslashes($_POST['name']));
        $url  = trim(daddslashes($_POST['url']));
        if (empty($name)) {
            exit('{"code":-1,"msg":"应用名称不能为空"}');
        }
        if (!preg_match('/^[a-zA-Z0-9\x7f-\xff\.\-\! ]+$/', $name) || strlen($name) < 3) {
            exit('{"code":-1,"msg":"应用名称不合法"}');
        }
        if (mb_strlen($name, "UTF-8") > 12) {
            exit('{"code":-1,"msg":"应用名称长度不能超过12个字"}');
        }
        if (empty($url)) {
            exit('{"code":-1,"msg":"应用网址不能为空"}');
        }
        if (!strpos($url, '.')) {
            exit('{"code":-1,"msg":"应用网址不正确"}');
        }
        if (isset($_SESSION['appurl']) && $_SESSION['appurl'] == $url) {
            exit(json_encode(['code' => -1, 'msg' => '你已经生成过了，请在"我的生成"中查看。']));
        }
        if ($conf['appcreate_diy'] == 1) {
            $icon       = !empty($_POST['icon']) ? trim($_POST['icon']) : '1';
            $background = !empty($_POST['background']) ? trim($_POST['background']) : '2';
        } else {
            $icon       = '1';
            $background = '2';
        }
        $theme = $conf['appcreate_theme'];
        if ($app->submittask($name, $url, $icon, $background, $theme, $conf['appcreate_nonav'])) {
            $_SESSION['appurl'] = $url;
            if ($price > 0) {
                changeUserMoney($userrow['zid'], $price, false, '消费', '自助生成APP');
            }
            exit(json_encode(['code' => 0, 'msg' => '成功提交生成任务，生成大约需要半分钟，生成成功后请在"我的生成"中查看。', 'taskid' => $app->taskid]));
        } else {
            exit(json_encode(['code' => -1, 'msg' => $app->msg]));
            exit('{"code":-1,"msg":"' . $app->msg . '"}');
        }
        break;
    case 'app_query':
        if (!$conf['appcreate_open'] || !$conf['appcreate_key']) {
            exit('{"code":-1,"msg":"未开启分站自助生成APP功能"}');
        }
        $app    = new \lib\AppCreate($conf['appcreate_key']);
        $url    = 'http://' . $userrow['domain'];
        $url    = isset($_SESSION['appurl']) ? $_SESSION['appurl'] : $url;
        $domain = parse_url($url)['host'];
        $res    = $app->queryurl($url);
        if ($res && is_array($res)) {
            $appurl = "";
            if ($res['status'] == 1) {
                $android_url = $res['lanzou_url'] ? $res['lanzou_url'] : $res['android_url'];
                $ios_url     = $res['ios_url'];
                $approw      = $DB->find('apps', '*', ['domain' => $domain]);
                if ($approw) {
                    $id = $approw['id'];
                    $DB->update('apps', [
                        'taskid'      => $res['id'],
                        'domain'      => $domain,
                        'name'        => $res['name'],
                        'package'     => $res['package'],
                        'android_url' => $android_url,
                        'ios_url'     => $ios_url,
                        'icon'        => $res['icon'],
                        'addtime'     => $res['created_at'],
                        'status'      => 1
                    ], ['id' => $id]);
                } else {
                    $id = $DB->insert('apps', [
                        'taskid'      => $res['id'],
                        'domain'      => $domain,
                        'name'        => $res['name'],
                        'package'     => $res['package'],
                        'android_url' => $android_url,
                        'ios_url'     => $ios_url,
                        'icon'        => $res['icon'],
                        'addtime'     => $res['created_at'],
                        'status'      => 1
                    ]);
                }
                $appurl = '/?mod=app&id=' . $id;
                $DB->exec("UPDATE `pre_site` SET `appurl`=:appurl WHERE `zid`='{$userrow['zid']}'",
                    [':appurl' => $appurl]);
            }
            $result = [
                "code"              => 0,
                "msg"               => "succ",
                "url"               => $url,
                "download_url"      => $appurl,
                "download_url_show" => $url . $appurl,
                "android_url"       => $android_url,
                "ios_url"           => $ios_url,
                "data"              => $res
            ];
            exit(json_encode($result));
        } else {
            exit(json_encode(['code' => -1, 'msg' => $app->msg]));
        }
        break;
    case 'tixian_note':
        $id     = intval($_POST['id']);
        $rows   = $DB->getRow("select * from pre_tixian where id='$id' and zid='{$userrow['zid']}' limit 1");
        $result = ["code" => 0, "msg" => "succ", "result" => $rows['note']];
        exit(json_encode($result));
        break;
    case 'get_price_history':
        // 获取价格修改历史记录
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $pagesize = 10;
        $offset = ($page - 1) * $pagesize;
        
        // 获取总记录数
        $total = $DB->getColumn("SELECT count(*) FROM pre_price_history WHERE zid='{$userrow['zid']}'");
        
        // 获取历史记录
        $sql = $DB->query("SELECT * FROM pre_price_history WHERE zid='{$userrow['zid']}' ORDER BY create_time DESC LIMIT $offset,$pagesize");
        $history = [];
        while ($row = $sql->fetch()) {
            // 尝试使用JSON解析，如果失败则尝试使用序列化解析
            $price_data = json_decode($row['price_data'], true);
            if (!is_array($price_data)) {
                $price_data = @unserialize($row['price_data']);
                if (!is_array($price_data)) {
                    $price_data = [];
                }
            }
            
            $history[] = [
                'id' => $row['id'],
                'description' => $row['description'],
                'create_time' => $row['create_time'],
                'price_data_count' => count($price_data)
            ];
        }
        
        exit(json_encode([
            'code' => 0,
            'msg' => 'success',
            'data' => $history,
            'total' => $total,
            'page' => $page,
            'pagesize' => $pagesize
        ]));
        break;
    case 'restore_last_price':
        // 恢复到上一次修改
        $last_history = $DB->getRow("SELECT * FROM pre_price_history WHERE zid='{$userrow['zid']}' ORDER BY create_time DESC LIMIT 1");
        if (!$last_history) {
            exit('{"code":-1,"msg":"没有找到历史记录"}');
        }
        
        // 保存当前状态到历史记录
        $current_price = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$userrow['zid']}'");
        $current_price_data = [];
        while ($row = $current_price->fetch()) {
            $current_price_data[$row['tid']] = [
                'price' => $row['price'],
                'cost' => $row['cost'],
                'cost2' => $row['cost2'],
                'del' => $row['del']
            ];
        }
        $current_price_json = json_encode($current_price_data);
        $DB->exec("INSERT INTO pre_price_history (zid, price_data, description, create_time) VALUES ('{$userrow['zid']}', '{$current_price_json}', '恢复到上一次修改前的状态', NOW())");
        
        // 使用事务确保操作的原子性
        $DB->beginTransaction();
        try {
            // 清空当前价格数据
            $DB->exec("DELETE FROM pre_site_price WHERE zid='{$userrow['zid']}'");
            
            // 恢复上一次的价格数据
            $last_price_data = json_decode($last_history['price_data'], true);
            if (is_array($last_price_data)) {
                foreach ($last_price_data as $tid => $price_info) {
                    $sql = "INSERT INTO pre_site_price (zid, tid, price, cost, cost2, del, create_time, update_time) 
                           VALUES (:zid, :tid, :price, :cost, :cost2, :del, NOW(), NOW())";
                    
                    $data_insert = [
                        ':zid' => $userrow['zid'],
                        ':tid' => $tid,
                        ':price' => $price_info['price'],
                        ':cost' => isset($price_info['cost']) ? $price_info['cost'] : 0,
                        ':cost2' => isset($price_info['cost2']) ? $price_info['cost2'] : 0,
                        ':del' => isset($price_info['del']) ? $price_info['del'] : 0
                    ];
                    
                    $DB->exec($sql, $data_insert);
                }
            }
            
            $DB->commit();
            exit('{"code":0,"msg":"已成功恢复到上一次修改的状态"}');
        } catch (Exception $e) {
            $DB->rollback();
            exit('{"code":-1,"msg":"恢复失败：' . $e->getMessage() . '"}');
        }
        break;
    case 'restore_from_history':
        // 从指定历史记录恢复
        $history_id = intval($_POST['id']);
        $history = $DB->getRow("SELECT * FROM pre_price_history WHERE id='$history_id' AND zid='{$userrow['zid']}' LIMIT 1");
        if (!$history) {
            exit('{"code":-1,"msg":"历史记录不存在"}');
        }
        
        // 保存当前状态到历史记录
        $current_price = $DB->query("SELECT tid, price, cost, cost2, del FROM pre_site_price WHERE zid='{$userrow['zid']}'");
        $current_price_data = [];
        while ($row = $current_price->fetch()) {
            $current_price_data[$row['tid']] = [
                'price' => $row['price'],
                'cost' => $row['cost'],
                'cost2' => $row['cost2'],
                'del' => $row['del']
            ];
        }
        $current_price_json = json_encode($current_price_data);
        $DB->exec("INSERT INTO pre_price_history (zid, price_data, description, create_time) VALUES ('{$userrow['zid']}', '{$current_price_json}', '恢复到指定历史版本前的状态', NOW())");
        
        // 使用事务确保操作的原子性
        $DB->beginTransaction();
        try {
            // 清空当前价格数据
            $DB->exec("DELETE FROM pre_site_price WHERE zid='{$userrow['zid']}'");
            
            // 恢复指定历史版本的价格数据
            $history_price_data = json_decode($history['price_data'], true);
            if (is_array($history_price_data)) {
                foreach ($history_price_data as $tid => $price_info) {
                    $sql = "INSERT INTO pre_site_price (zid, tid, price, cost, cost2, del, create_time, update_time) 
                           VALUES (:zid, :tid, :price, :cost, :cost2, :del, NOW(), NOW())";
                    
                    $data_insert = [
                        ':zid' => $userrow['zid'],
                        ':tid' => $tid,
                        ':price' => $price_info['price'],
                        ':cost' => isset($price_info['cost']) ? $price_info['cost'] : 0,
                        ':cost2' => isset($price_info['cost2']) ? $price_info['cost2'] : 0,
                        ':del' => isset($price_info['del']) ? $price_info['del'] : 0
                    ];
                    
                    $DB->exec($sql, $data_insert);
                }
            }
            
            $DB->commit();
            exit('{"code":0,"msg":"已成功恢复到指定历史版本"}');
        } catch (Exception $e) {
            $DB->rollback();
            exit('{"code":-1,"msg":"恢复失败：' . $e->getMessage() . '"}');
        }
        break;
    default:
        exit('{"code":-4,"msg":"No Act"}');
        break;
}
