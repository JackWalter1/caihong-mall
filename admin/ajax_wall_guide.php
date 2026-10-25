<?php
include("../includes/common.php");
$act=isset($_GET['act'])?$_GET['act']:null;

if($islogin==1){}else exit('{"code":-1,"msg":"未登录"}');

switch($act){
    case 'set':
        adminpermission('set', 2);
        foreach($_POST as $k=>$v){
            if($k=='wall_guide_open' || $k=='wall_guide_title' || $k=='wall_guide_content' || $k=='wall_guide_btn' || $k=='wall_guide_interval' || $k=='wall_guide_interval_unit' || $k=='wall_guide_theme_color'){
                $v = daddslashes($v);
                // 对于间隔时间进行验证
                if($k=='wall_guide_interval'){
                    $v = intval($v);
                    if($v < 1) $v = 1; // 最小1
                    if($v > 720) $v = 720; // 最大720
                }
                // 对于时间单位进行验证
                if($k=='wall_guide_interval_unit'){
                    if(!in_array($v, ['second', 'minute', 'hour'])){
                        $v = 'hour'; // 默认为小时
                    }
                }
                // 对于主题颜色进行验证
                if($k=='wall_guide_theme_color'){
                    if(!preg_match('/^#[a-fA-F0-9]{6}$/', $v)){
                        $v = '#2193b0'; // 默认主题色
                    }
                }
                $DB->exec("INSERT INTO pre_config SET `k`=:k,`v`=:v ON DUPLICATE KEY UPDATE `v`=:v2", [':k' => $k, ':v' => $v, ':v2' => $v]);
                continue;
            }
        }
        $ad=$CACHE->clear();
        if($ad)exit('{"code":0,"msg":"succ"}');
        else exit('{"code":-1,"msg":"修改设置失败['.$DB->error().']"}');
    break;
    
    default:
        exit('{"code":-4,"msg":"No Act"}');
    break;
}