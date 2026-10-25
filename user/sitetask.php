<?php

include "../includes/common.php";

function createSiteTaskLogTable() {
    global $DB;
    
    $sql = "CREATE TABLE IF NOT EXISTS `pre_sitetask_log` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `userid` int(11) unsigned NOT NULL,
      `taskid` int(11) unsigned NOT NULL,
      `taskname` varchar(255) NOT NULL,
      `money` varchar(32) NOT NULL,
      `addtime` datetime DEFAULT NULL,
      `status` tinyint(1) NOT NULL DEFAULT 0,
      `remark` text,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点任务记录表';";
    
    try {
        $DB->exec($sql);
    } catch (Exception $e) {
    }
}

createSiteTaskLogTable();

$title = "站点任务";
include "./head.php";
if ($islogin2 == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

$my = isset($_GET["my"]) ? $_GET["my"] : null;

if ($my == "claim") {
    $taskid = intval($_GET["id"]);
    $uid = intval($userrow['uid']);
    $today = date('Y-m-d');
    $task = $DB->getRow("SELECT * FROM pre_sitetask WHERE id=:id AND active=1 LIMIT 1", array(':id' => $taskid));
    
    if (!$task) {
        showmsg('任务不存在或已关闭！', 3);
    }
    
    $count = $DB->getColumn("SELECT COUNT(*) FROM pre_sitetask_log WHERE userid=:uid AND taskid=:taskid AND DATE(addtime)=:today", array(':uid' => $uid, ':taskid' => $taskid, ':today' => $today));
    
    if ($count > 0) {
        showmsg('今天已提交过该任务申请！', 3);
    }
    
    $totalCount = $DB->getColumn("SELECT COUNT(*) FROM pre_sitetask_log WHERE taskid=:taskid AND status=1", array(':taskid' => $taskid));
    if ($totalCount >= $task['quantity']) {
        showmsg('该任务奖励已发放完毕！', 3);
    }
    
    $completed = false;
    switch ($task['task']) {
        case 0:
            if ($task['tid'] > 0) {
                if ($task['type'] == 0) {
                    $orderCount = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE zid=:uid AND tid=:tid AND DATE(addtime)=:today", array(':uid' => $uid, ':tid' => $task['tid'], ':today' => $today));
                } else {
                    $orderCount = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE zid=:uid AND tid=:tid", array(':uid' => $uid, ':tid' => $task['tid']));
                }
                $completed = ($orderCount >= $task['value']);
            } else {
                $completed = false;
            }
            break;
        case 1:
            if ($task['type'] == 0) {
                $recharge = $DB->getColumn("SELECT SUM(money) FROM pre_point_record WHERE uid=:uid AND type=1 AND DATE(addtime)=:today", array(':uid' => $uid, ':today' => $today));
            } else {
                $recharge = $DB->getColumn("SELECT SUM(money) FROM pre_point_record WHERE uid=:uid AND type=1", array(':uid' => $uid));
            }
            $completed = ($recharge >= $task['value']);
            break;
        case 2:
            if ($task['type'] == 0) {
                $orderCount = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE uid=:uid AND DATE(addtime)=:today", array(':uid' => $uid, ':today' => $today));
            } else {
                $orderCount = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE uid=:uid", array(':uid' => $uid));
            }
            $completed = ($orderCount >= $task['value']);
            break;
        case 3:
            if ($task['type'] == 0) {
                $salesAmount = $DB->getColumn("SELECT SUM(money) FROM pre_orders WHERE uid=:uid AND DATE(addtime)=:today", array(':uid' => $uid, ':today' => $today));
            } else {
                $salesAmount = $DB->getColumn("SELECT SUM(money) FROM pre_orders WHERE uid=:uid", array(':uid' => $uid));
            }
            $completed = ($salesAmount >= $task['value']);
            break;
        case 4:
            if ($task['type'] == 0) {
                $inviteCount = $DB->getColumn("SELECT COUNT(*) FROM pre_user WHERE zid=:uid AND DATE(regtime)=:today", array(':uid' => $uid, ':today' => $today));
            } else {
                $inviteCount = $DB->getColumn("SELECT COUNT(*) FROM pre_user WHERE zid=:uid", array(':uid' => $uid));
            }
            $completed = ($inviteCount >= $task['value']);
            break;
        case 5:
            if ($userrow['last'] == date('Y-m-d')) {
                $completed = true;
            }
            break;
    }
    
    if (!$completed) {
        showmsg('您还未完成该任务条件！', 3);
    }
    
    $DB->exec("INSERT INTO pre_sitetask_log (userid, taskid, taskname, money, addtime, status) VALUES (:uid, :taskid, :taskname, :money, :addtime, 0)", array(':uid' => $uid, ':taskid' => $taskid, ':taskname' => $task['name'], ':money' => $task['money'], ':addtime' => $date));
    
    showmsg('任务申请已提交，请等待管理员审核！<br/><br/><a href="./sitetask.php">>>返回任务列表</a>', 1);
} else {
?>
<div class="wrapper-md">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">
                    <i class="fa fa-tasks"></i> 站点任务
                </div>
                <div class="panel-body">
                    <p class="text-muted">完成任务提交申请，等待管理员审核后即可获得奖励！</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped b-t b-light">
                        <thead>
                            <tr>
                                <th>任务名称</th>
                                <th>任务类型</th>
                                <th>任务条件</th>
                                <th>奖励金额</th>
                                <th>剩余数量</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $uid = intval($userrow['uid']);
                            $rs = $DB->query("SELECT * FROM pre_sitetask WHERE active=1 ORDER BY sort ASC");
                            $today = date('Y-m-d');
                            while ($res = $rs->fetch()) {
                                $type = $res['type'] == 0 ? '今日' : '总计';
                                $tasktype = sitetask_type($res['task']);
                                if ($res['task'] == 5) {
                                    $tasktype = $tasktype . '任务';
                                } else {
                                    $tasktype = $type . $tasktype . '任务';
                                }
                                
                                $claimed = $DB->getColumn("SELECT COUNT(*) FROM pre_sitetask_log WHERE userid=:uid AND taskid=:taskid AND DATE(addtime)=:today", array(':uid' => $uid, ':taskid' => $res['id'], ':today' => $today));
                                $totalClaimed = $DB->getColumn("SELECT COUNT(*) FROM pre_sitetask_log WHERE taskid=:taskid AND status=1", array(':taskid' => $res['id']));
                                $remain = $res['quantity'] - $totalClaimed;
                                
                                $btnClass = $claimed > 0 ? 'btn-default' : 'btn-success';
                                $btnText = $claimed > 0 ? '今日已提交' : '提交申请';
                                $disabled = $claimed > 0 || $remain <= 0 ? 'disabled' : '';
                                
                                echo '<tr>
                                    <td>' . $res['name'] . '</td>
                                    <td>' . $tasktype . '</td>
                                    <td>' . $res['value'] . '</td>
                                    <td><span class="text-danger">' . $res['money'] . ' 元</span></td>
                                    <td>' . ($remain > 0 ? $remain : '<span class="text-muted">已领完</span>') . '</td>
                                    <td>' . ($res['desc'] ? $res['desc'] : '-') . '</td>
                                    <td>
                                        <a href="./sitetask.php?my=claim&id=' . $res['id'] . '" class="btn ' . $btnClass . ' btn-sm" ' . $disabled . '>
                                            <i class="fa fa-paper-plane"></i> ' . $btnText . '
                                        </a>
                                    </td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="panel panel-default">
                <div class="panel-heading font-bold">
                    <i class="fa fa-history"></i> 我的任务记录
                </div>
                <div class="table-responsive">
                    <table class="table table-striped b-t b-light">
                        <thead>
                            <tr>
                                <th>任务名称</th>
                                <th>奖励金额</th>
                                <th>提交时间</th>
                                <th>审核状态</th>
                                <th>备注</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $logRs = $DB->query("SELECT * FROM pre_sitetask_log WHERE userid=:uid ORDER BY id DESC LIMIT 20", array(':uid' => $uid));
                            while ($log = $logRs->fetch()) {
                                $statusText = '';
                                $statusClass = '';
                                switch ($log['status']) {
                                    case 0:
                                        $statusText = '待审核';
                                        $statusClass = 'label-warning';
                                        break;
                                    case 1:
                                        $statusText = '已通过';
                                        $statusClass = 'label-success';
                                        break;
                                    case 2:
                                        $statusText = '已拒绝';
                                        $statusClass = 'label-danger';
                                        break;
                                }
                                echo '<tr>
                                    <td>' . $log['taskname'] . '</td>
                                    <td><span class="text-success">' . $log['money'] . ' 元</span></td>
                                    <td>' . $log['addtime'] . '</td>
                                    <td><span class="label ' . $statusClass . '">' . $statusText . '</span></td>
                                    <td>' . ($log['remark'] ? $log['remark'] : '-') . '</td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
include "./foot.php";
?>