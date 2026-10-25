<?php
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

@header('Content-Type: application/json; charset=UTF-8');

if(!checkRefererHost())exit('{"code":403}');

switch($act){

    case 'delSite': //删除站点
        adminpermission('site', 2);
        $zid=intval($_GET['zid']);
        if($DB->exec("DELETE FROM pre_site WHERE zid=:zid", [':zid' => $zid])!==false)
            exit('{"code":0,"msg":"succ"}');
        else
            exit('{"code":-1,"msg":"删除失败！'.$DB->error().'"}');
        break;
    case 'setSite': //开启关闭站点
        adminpermission('site', 2);
        $zid=intval($_GET['zid']);
        $active=intval($_GET['active']);
        $DB->exec("update pre_site set status=:active where zid=:zid", [':active' => $active, ':zid' => $zid]);
        exit('{"code":0,"msg":"succ"}');
        break;
    case 'setSuper': //切换站点版本
        adminpermission('site', 2);
        $zid=intval($_GET['zid']);
        $row=$DB->getRow("select * from pre_site where zid=:zid limit 1", [':zid' => $zid]);
        if($row['power']==2)$power=1;
        else $power=2;
        $DB->exec("update pre_site set power=:power where zid=:zid", [':power' => $power, ':zid' => $zid]);
        exit('{"code":0,"msg":"succ"}');
        break;
    case 'setEndtime': //分站延时
        adminpermission('site', 2);
        $zid=intval($_POST['zid']);
        $month=intval($_POST['month']);
        $row=$DB->getRow("select * from pre_site where zid=:zid limit 1", [':zid' => $zid]);
        if($row['endtime']>date("Y-m-d")) $endtime = date("Y-m-d", strtotime("+ {$month} months", strtotime($row['endtime'])));
        else $endtime = date("Y-m-d", strtotime("+ {$month} months"));
        $DB->exec("update pre_site set endtime=:endtime where zid=:zid", [':endtime' => $endtime, ':zid' => $zid]);
        exit('{"code":0,"msg":"succ"}');
        break;
    case 'siteRecharge': //分站充值
        adminpermission('site', 2);
        $zid=intval($_POST['zid']);
        $do=intval($_POST['actdo']);
        $rmb=floatval($_POST['rmb']);
        $remark=trim($_POST['remark']);
        $row=$DB->getRow("select zid,rmb from pre_site where zid=:zid limit 1", [':zid' => $zid]);
        if(!$row)
            exit('{"code":-1,"msg":"当前分站不存在！"}');
        if($do==1 && $rmb>$row['rmb'])$rmb=$row['rmb'];
        if($remark)$addstr = '（'.$remark.'）';
        if($do==0){
            changeUserMoney($zid, $rmb, true, '加款', '后台加款'.$rmb.'元'.$addstr);
        }else{
            changeUserMoney($zid, $rmb, false, '扣除', '后台扣款'.$rmb.'元'.$addstr);
        }
        exit('{"code":0,"msg":"succ"}');
        break;

    case 'getTixian': //查看提现信息
        adminpermission('tixian', 2);
        $id=intval($_GET['id']);
        $rows=$DB->getRow("select * from pre_tixian where id=:id limit 1", [':id' => $id]);
        if(!$rows)
            exit('{"code":-1,"msg":"当前提现记录不存在！"}');
        $data = '<div class="form-group"><div class="input-group"><div class="input-group-addon">提现方式</div><select class="form-control" id="pay_type" default="'.$userrow['pay_type'].'"><option value="0">支付宝</option><option value="1">微信</option><option value="2">QQ钱包</option></select></div></div>';
        $data .= '<div class="form-group"><div class="input-group"><div class="input-group-addon">提现账号</div><input type="text" id="pay_account" value="'.$rows['pay_account'].'" class="form-control" required/></div></div>';
        $data .= '<div class="form-group"><div class="input-group"><div class="input-group-addon">提现姓名</div><input type="text" id="pay_name" value="'.$rows['pay_name'].'" class="form-control" required/></div></div>';
        $data .= '<input type="submit" id="save" onclick="saveInfo('.$id.')" class="btn btn-primary btn-block" value="保存">';
        $result=array("code"=>0,"msg"=>"succ","data"=>$data);
        exit(json_encode($result));
        break;
    case 'editTixian': //修改提现信息
        adminpermission('tixian', 2);
        $id=intval($_POST['id']);
        $pay_type=trim(daddslashes($_POST['pay_type']));
        $pay_account=trim(daddslashes($_POST['pay_account']));
        $pay_name=trim(daddslashes($_POST['pay_name']));
        $sds=$DB->exec("update `pre_tixian` set `pay_type`=:pay_type,`pay_account`=:pay_account,`pay_name`=:pay_name where `id`=:id", [':pay_type' => $pay_type, ':pay_account' => $pay_account, ':pay_name' => $pay_name, ':id' => $id]);
        if($sds!==false)
            exit('{"code":0,"msg":"修改记录成功！"}');
        else
            exit('{"code":-1,"msg":"修改记录失败！'.$DB->error().'"}');
        break;
    case 'opTixian': //操作提现
        adminpermission('tixian', 2);
        $id=intval($_POST['id']);
        $op=$_POST['op'];
        if($op == 'delete'){
            $sql="DELETE FROM pre_tixian WHERE id=:id";
            if($DB->exec($sql, [':id' => $id])!==false)
                exit('{"code":0,"msg":"删除成功！"}');
            else
                exit('{"code":-1,"msg":"删除失败！'.$DB->error().'"}');
        }elseif($op == 'complete'){
            if($DB->exec("update pre_tixian set status=1,endtime=NOW() where id=:id", [':id' => $id])!==false)
                exit('{"code":0,"msg":"已变更为已完成状态"}');
            else
                exit('{"code":-1,"msg":"变更失败！'.$DB->error().'"}');
        }elseif($op == 'reset'){
            if($DB->exec("update pre_tixian set status=0 where id=:id", [':id' => $id])!==false)
                exit('{"code":0,"msg":"已变更为未完成状态"}');
            else
                exit('{"code":-1,"msg":"变更失败！'.$DB->error().'"}');
        }elseif($op == 'fail'){
            $rows=$DB->getRow("select * from pre_tixian where id=:id limit 1", [':id' => $id]);
            $reason=$conf['fenzhan_skimg']==1?'提现账号错误或收款图无法识别，请修改后重新提交！':'提现账号错误，请修改后重新提交！';
            if($DB->exec("update pre_tixian set status=2,note=:reason where id=:id", [':reason' => $reason, ':id' => $id])!==false){
                changeUserMoney($rows['zid'], $rows['money'], true, '退回', '提现被退回到分站余额'.$rows['money'].'元');
                exit('{"code":0,"msg":"已成功退回到分站余额"}');
            }else
                exit('{"code":-1,"msg":"退回失败！'.$DB->error().'"}');
        }
        break;
    case 'tixian_note': //提现失败原因
        $id=intval($_POST['id']);
        $rows=$DB->getRow("select * from pre_tixian where id=:id limit 1", [':id' => $id]);
        $result=array("code"=>0,"msg"=>"succ","result"=>$rows['note']);
        exit(json_encode($result));
        break;
    case 'set_tixian_note': //修改提现失败原因
        adminpermission('tixian', 2);
        $id=intval($_POST['id']);
        $result=trim(daddslashes($_POST['result']));
        $sds=$DB->exec("update `pre_tixian` set `note`=:result where `id`=:id", [':result' => $result, ':id' => $id]);
        if($sds!==false)
            exit('{"code":0,"msg":"修改成功！"}');
        else
            exit('{"code":-1,"msg":"修改失败！'.$DB->error().'"}');
        break;

    case 'setiprice':
        adminpermission('site', 2);
        $zid=intval($_POST['zid']);
        $tid=intval($_POST['tid']);
        $price=round(trim($_POST['iprice']),2);
        if(!is_numeric($price) || !preg_match('/^[0-9.]+$/', $price))exit('{"code":-1,"msg":"价格输入不规范"}');

        $price_obj = new \lib\Price($zid);
        if($price_obj->setiPriceInfo($tid,$price))
            exit('{"code":0,"msg":"succ"}');
        else
            exit('{"code":-1,"msg":"修改失败！'.$DB->error().'"}');
        break;
    case 'cleariprice':
        adminpermission('site', 2);
        $zid=intval($_POST['zid']);
        if($DB->exec("update pre_site set iprice=NULL where zid=:zid", [':zid' => $zid])!==false)
            exit('{"code":0,"msg":"succ"}');
        else
            exit('{"code":-1,"msg":"清空失败！'.$DB->error().'"}');
        break;
    case 'change_price':
        $zid=isset($_POST['zid'])?intval($_POST['zid']):exit('{"code":-1,"msg":"zid不能为空"}');
        $price_obj = new \lib\Price($zid);
        $type=intval($_POST['type']);
        $value=round($_POST['value'],2);
        if($value<=0)exit('{"code":-1,"msg":"输入值不正确"}');
        $sql=$DB->query("select * from pre_tools where active=1");
        $data=array();
        while($row=$sql->fetch()){
            if($row['price']==0){
                continue;
            }
            if(strpos($row['name'],'免费')!==false){
                continue;
            }
            $price_obj->setToolInfo($row['tid'],$row);
            if($price_obj->getPower()==2)$price = $price_obj->getToolCost2($row['tid']);
            else $price = $price_obj->getToolCost($row['tid']);

            if($type == 0){
                $sfprice = $price*(1-(float)$value/100);
            }else{
                $sfprice = $price - $value;
            }
            $data[$row['tid']]=round($sfprice,2);
        }
        $array_data=serialize($data);
        $DB->exec("update `pre_site` set `iprice`=:array_data where zid=:zid", [':array_data' => $array_data, ':zid' => $zid]);
        exit('{"code":0}');
        break;

    case 'delTask': //删除站点任务
        adminpermission('site', 2);
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== md5(session_id() . SYS_KEY)) {
            exit('{"code":-1,"msg":"CSRF验证失败！"}');
        }
        $id=intval($_GET['id']);
        if($DB->exec("DELETE FROM pre_sitetask WHERE id=:id", array(':id' => $id))!==false)
            exit('{"code":0,"msg":"删除成功！"}');
        else
            exit('{"code":-1,"msg":"删除失败！'.$DB->error().'"}');
        break;
    case 'setTask': //设置站点任务状态
        adminpermission('site', 2);
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== md5(session_id() . SYS_KEY)) {
            exit('{"code":-1,"msg":"CSRF验证失败！"}');
        }
        $id=intval($_GET['id']);
        $active=intval($_GET['active']);
        $DB->exec("UPDATE pre_sitetask SET active=:active WHERE id=:id", array(':active' => $active, ':id' => $id));
        exit('{"code":0,"msg":"succ"}');
        break;

    case 'genVirtualUsers':
        adminpermission('site', 2);
        $count = intval($_POST['count']);
        if($count < 1) $count = 1;
        if($count > 100) $count = 100;

        $surnames = array('赵','钱','孙','李','周','吴','郑','王','冯','陈','褚','卫','蒋','沈','韩','杨','朱','秦','尤','许','何','吕','施','张','孔','曹','严','华','金','魏','陶','姜','戚','谢','邹','喻','柏','水','窦','章','云','苏','潘','葛','奚','范','彭','郎','鲁','韦','昌','马','苗','凤','花','方','俞','任','袁','柳','酆','鲍','史','唐','费','廉','岑','薛','雷','贺','倪','汤','滕','殷','罗','毕','郝','邬','安','常','乐','于','时','傅','皮','卞','齐','康','伍','余','元','卜','顾','孟','平','黄','和','穆','萧','尹','姚','邵','湛','汪','祁','毛','禹','狄','米','贝','明','臧','计','伏','成','戴','谈','宋','茅','庞','熊','纪','舒','屈','项','祝','董','梁','杜','阮','蓝','闵','席','季','麻','强','贾','路','娄','危','江','童','颜','郭','梅','盛','林','刁','钟','徐','邱','骆','高','夏','蔡','田','樊','胡','凌','霍','虞','万','支','柯','昝','管','卢','莫','经','房','裘','缪','干','解','应','宗','丁','宣','贲','邓','郁','单','杭','洪','包','诸','左','石','崔','吉','钮','龚','程','嵇','邢','滑','裴','陆','荣','翁','荀','羊','於','惠','甄','曲','家','封','芮','羿','储','靳','汲','邴','糜','松','井','段','富','巫','乌','焦','巴','弓','牧','隗','山','谷','车','侯','宓','蓬','全','郗','班','仰','秋','仲','伊','宫','宁','仇','栾','暴','甘','钭','厉','戎','祖','武','符','刘','景','詹','束','龙','叶','幸','司','韶','郜','黎','蓟','薄','印','宿','白','怀','蒲','邰','从','鄂','索','咸','籍','赖','卓','蔺','屠','蒙','池','乔','阴','郁','胥','能','苍','双','闻','莘','党','翟','谭','贡','劳','逄','姬','申','扶','堵','冉','宰','郦','雍','却','璩','桑','桂','濮','牛','寿','通','边','扈','燕','冀','郏','浦','尚','农','温','别','庄','晏','柴','瞿','阎','充','慕','连','茹','习','宦','艾','鱼','容','向','古','易','慎','戈','廖','庾','终','暨','居','衡','步','都','耿','满','弘','匡','国','文','寇','广','禄','阙','东','欧','殳','沃','利','蔚','越','夔','隆','师','巩','厍','聂','晁','勾','敖','融','冷','訾','辛','阚','那','简','饶','空','曾','毋','沙','乜','养','鞠','须','丰','巢','关','蒯','相','查','后','荆','红','游','竺','权','逮','盍','益','桓','公');
        $givenPrefix = array('伟','强','磊','洋','勇','军','杰','涛','明','超','秀','华','丽','艳','芳','敏','静','娜','欣','婷','文','宇','浩','然','博','睿','晨','曦','瑞','鹏','飞','龙','凤','嘉','鑫','毅','恒','亮','平','刚','宁','东','志','宏','峰','建','康','辉','庆','彬','斌','帅','林','成','威','新','国','海','春','秋','冬','雪','梅','兰','菊','竹','松','柏','岩','石','川','江','山','水','云','天','雨','雷','风','光','昌','盛','兴','旺','发','达','祥','和','吉','安','全','利','美','德','仁','义','信','智','勇','忠','孝','良','善','福','寿','喜','乐','佳','怡','悦','欢','思','念','慧','雅','洁','清','柔','婉','丹','青','翠','红','蓝','碧','金','银','玉','珍','珠','宝','珊','瑶','琼','珮','环','玲','珠','玥','珂','琪','琦');

        $success = 0;
        for($i = 0; $i < $count; $i++) {
            $surname = $surnames[array_rand($surnames)];
            $prefix1 = $givenPrefix[array_rand($givenPrefix)];
            $prefix2 = $givenPrefix[array_rand($givenPrefix)];
            $name = $surname . $prefix1 . $prefix2;

            $randNum = str_pad(mt_rand(4, 2000), 3, '0', STR_PAD_LEFT);
            $user = 'user_' . $randNum . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 4);

            $pwd = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $rmb = round(mt_rand(0, 1000) / 100, 2);
            $qq = mt_rand(100000000, 9999999999);

            $sql = "INSERT INTO `pre_site` (`power`,`user`,`pwd`,`rmb`,`qq`,`reg_ip`,`addtime`,`status`) VALUES (0, :user, :pwd, :rmb, :qq, :reg_ip, :date, 1)";
            $data = array(':user' => $user, ':pwd' => $pwd, ':rmb' => $rmb, ':qq' => $qq, ':reg_ip' => '127.0.0.1', ':date' => $date);
            if($DB->exec($sql, $data) !== false) {
                $success++;
            }
        }

        exit(json_encode(array("code" => 0, "msg" => "成功生成 {$success} 个虚拟用户", "count" => $success)));

    default:
        exit('{"code":-4,"msg":"No Act"}');
        break;
}