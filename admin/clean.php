<?php
/**
 * 系统数据清理
 * 博客地址：6v6.ren
 * Q群：941535592
**/
include("../includes/common.php");
$title='系统数据清理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<style>
.clean-section {
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
}
.clean-section h4 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #333;
    font-size: 14px;
    font-weight: bold;
}
.clean-btn {
    margin: 5px 0;
    padding: 8px 15px;
}
.clean-form {
    margin-top: 10px;
    padding: 10px;
    background: #fff;
    border-radius: 4px;
}
.clean-form input {
    display: inline-block;
    width: 80px;
    margin: 0 5px;
}
.clean-form .btn {
    margin-left: 10px;
}
</style>
    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
<?php
adminpermission('super', 1);
$mod=isset($_GET['mod'])?$_GET['mod']:null;
if($mod=='cleancache'){
$CACHE->clear();
if(function_exists("opcache_reset"))@opcache_reset();
showmsg('清理系统设置缓存成功！',1);
}elseif($mod=='cleanlog'){
$DB->exec("TRUNCATE TABLE `pre_logs`");
showmsg('清空社区对接日志成功！',1);
}elseif($mod=='cleanpay'){
$DB->exec("DELETE FROM `pre_pay` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("DELETE FROM `pre_pay` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-3 hours"))."' and status=0");
$DB->exec("DELETE FROM `pre_cart` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("DELETE FROM `pre_cart` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-12 hours"))."' and status<2");
$DB->exec("OPTIMIZE TABLE `pre_pay`");
showmsg('删除30天前支付记录成功！',1);
}elseif($mod=='cleanorders'){
$DB->exec("DELETE FROM `pre_orders` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_orders`");
showmsg('删除30天前订单记录成功！',1);
}elseif($mod=='cleanqiandao'){
$DB->exec("DELETE FROM `pre_qiandao` WHERE time<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_qiandao`");
showmsg('删除30天前签到记录成功！',1);
}elseif($mod=='cleanwork'){
$DB->exec("DELETE FROM `pre_workorder` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_workorder`");
showmsg('删除30天前工单记录成功！',1);
}elseif($mod=='cleanpoints'){
$DB->exec("DELETE FROM `pre_points` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-7 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_points`");
showmsg('删除7天前收支明细成功！',1);
}elseif($mod=='cleangift'){
$DB->exec("DELETE FROM `pre_giftlog` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-1 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_giftlog`");
showmsg('删除1天前中奖记录成功！',1);
}elseif($mod=='cleaninvite'){
$DB->exec("DELETE FROM `pre_invitelog` WHERE date<'".date("Y-m-d H:i:s",strtotime("-1 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_invitelog`");
showmsg('删除1天前推广记录成功！',1);
}elseif($mod=='cleanpayi' && $_POST['do']=='submit'){
$days = intval($_POST['days']);
$money = daddslashes($_POST['money']);
if($days<=0 || $money==null)showmsg('请确保每项不能为空',3);
$DB->exec("DELETE FROM `pre_pay` WHERE money<='$money' and addtime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_pay`");
showmsg('删除支付记录成功！',1);
}elseif($mod=='cleanordersi' && $_POST['do']=='submit'){
if(!checkRefererHost())exit();
$days = intval($_POST['days']);
$money = daddslashes($_POST['money']);
if($days<=0 || $money==null)showmsg('请确保每项不能为空',3);
$DB->exec("DELETE FROM `pre_orders` WHERE money<='$money' and addtime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_orders`");
showmsg('删除订单记录成功！',1);
}elseif($mod=='cleansite' && $_POST['do']=='submit'){
if(!checkRefererHost())exit();
$days = intval($_POST['days']);
$money = daddslashes($_POST['money']);
if($days<=0 || $money==null)showmsg('请确保每项不能为空',3);
$DB->exec("DELETE FROM `pre_site` WHERE rmb<='$money' and addtime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."' and (lasttime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."' or lasttime is null)");
$DB->exec("OPTIMIZE TABLE `pre_pay`");
showmsg('删除分站记录成功！',1);
}else{
?>
<div class="block">
<div class="block-title clearfix">
<h2>系统数据清理</h2>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="clean-section">
            <h4><i class="fa fa-database"></i> 缓存清理</h4>
            <a href="./clean.php?mod=cleancache" class="btn btn-block btn-default clean-btn">
                <i class="fa fa-refresh"></i> 清理设置缓存
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="clean-section">
            <h4><i class="fa fa-eraser"></i> 日志清理</h4>
            <a href="./clean.php?mod=cleanlog" onclick="return confirm('你确实要清空所有社区对接日志吗？');" class="btn btn-block btn-default clean-btn">
                <i class="fa fa-trash"></i> 清空社区对接日志
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="clean-section">
            <h4><i class="fa fa-credit-card"></i> 支付记录清理</h4>
            <a href="./clean.php?mod=cleanpay" onclick="return confirm('你确实要删除30天前的支付记录吗？');" class="btn btn-block btn-default clean-btn">
                <i class="fa fa-trash"></i> 删除30天前支付记录
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="clean-section">
            <h4><i class="fa fa-shopping-cart"></i> 订单记录清理</h4>
            <a href="./clean.php?mod=cleanorders" onclick="return confirm('你确实要删除30天前的订单记录吗？');" class="btn btn-block btn-default clean-btn">
                <i class="fa fa-trash"></i> 删除30天前订单记录
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="clean-section">
            <h4><i class="fa fa-money"></i> 收支明细清理</h4>
            <a href="./clean.php?mod=cleanpoints" onclick="return confirm('你确实要删除7天前收支明细吗？');" class="btn btn-block btn-default clean-btn">
                <i class="fa fa-trash"></i> 删除7天前收支明细
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="clean-section">
            <h4><i class="fa fa-gift"></i> 中奖记录清理</h4>
            <a href="./clean.php?mod=cleangift" onclick="return confirm('你确实要删除1天前的中奖记录吗？');" class="btn btn-block btn-default clean-btn">
                <i class="fa fa-trash"></i> 删除1天前中奖记录
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="clean-section">
            <h4><i class="fa fa-share-alt"></i> 推广记录清理</h4>
            <a href="./clean.php?mod=cleaninvite" onclick="return confirm('你确实要删除1天前的推广记录吗？');" class="btn btn-block btn-default clean-btn">
                <i class="fa fa-trash"></i> 删除1天前推广记录
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="clean-section">
            <h4><i class="fa fa-calendar-check-o"></i> 签到记录清理</h4>
            <a href="./clean.php?mod=cleanqiandao" onclick="return confirm('你确实要删除30天前的签到记录吗？');" class="btn btn-block btn-default clean-btn">
                <i class="fa fa-trash"></i> 删除30天前签到记录
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="clean-section">
            <h4><i class="fa fa-tasks"></i> 工单记录清理</h4>
            <a href="./clean.php?mod=cleanwork" onclick="return confirm('你确实要删除30天前的工单记录吗？');" class="btn btn-block btn-default clean-btn">
                <i class="fa fa-trash"></i> 删除30天前工单记录
            </a>
        </div>
    </div>
</div>

<div class="clean-section" style="margin-top: 20px; background: #e8e8e8;">
    <h4><i class="fa fa-cogs"></i> 自定义清理</h4>
    
    <div class="row">
        <div class="col-md-4">
            <form action="./clean.php?mod=cleanpayi" method="post" role="form" class="clean-form">
                <input type="hidden" name="do" value="submit"/>
                <label>支付记录</label><br/>
                <input type="text" name="days" value="" placeholder="天数" class="form-control input-sm"/> 天前
                <input type="text" name="money" value="" placeholder="金额" class="form-control input-sm"/> 元
                <input type="submit" name="submit" value="立即删除" class="btn btn-sm btn-danger" onclick="return confirm('删除后无法恢复，确定继续吗？');"/>
            </form>
        </div>

        <div class="col-md-4">
            <form action="./clean.php?mod=cleanordersi" method="post" role="form" class="clean-form">
                <input type="hidden" name="do" value="submit"/>
                <label>订单记录</label><br/>
                <input type="text" name="days" value="" placeholder="天数" class="form-control input-sm"/> 天前
                <input type="text" name="money" value="" placeholder="金额" class="form-control input-sm"/> 元
                <input type="submit" name="submit" value="立即删除" class="btn btn-sm btn-danger" onclick="return confirm('删除后无法恢复，确定继续吗？');"/>
            </form>
        </div>

        <div class="col-md-4">
            <form action="./clean.php?mod=cleansite" method="post" role="form" class="clean-form">
                <input type="hidden" name="do" value="submit"/>
                <label>分站记录</label><br/>
                <input type="text" name="days" value="30" placeholder="天数" class="form-control input-sm"/> 天前
                <input type="text" name="money" value="0" placeholder="金额" class="form-control input-sm"/> 元
                <input type="submit" name="submit" value="立即删除" class="btn btn-sm btn-danger" onclick="return confirm('删除后无法恢复，确定继续吗？');"/>
            </form>
        </div>
    </div>
</div>

<div class="panel-footer">
    <span class="glyphicon glyphicon-info-sign"></span>
    定期清理数据有助于提升网站访问速度
</div>
</div>
<?php }?>
 </div>
</div>
</html>
