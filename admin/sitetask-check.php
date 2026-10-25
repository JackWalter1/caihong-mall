<?php

include "../includes/common.php";

$title = "站点任务审核";
include "./head.php";
if ($islogin == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("site", 1);

$my = isset($_GET["my"]) ? $_GET["my"] : null;

if ($my == "pass") {
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== md5(session_id() . SYS_KEY)) {
        showmsg("CSRF验证失败，请刷新页面后重试！", 4);
    }
    $id = intval($_GET["id"]);
    $log = $DB->getRow("SELECT * FROM pre_sitetask_log WHERE id=:id AND status=0 LIMIT 1", array(':id' => $id));
    
    if (!$log) {
        showmsg('记录不存在或已处理！', 3);
    }
    
    $DB->beginTransaction();
    try {
        $DB->exec("UPDATE pre_sitetask_log SET status=1 WHERE id=:id", array(':id' => $id));
        $DB->exec("UPDATE pre_user SET money=money+:money WHERE uid=:uid", array(':money' => $log['money'], ':uid' => $log['userid']));
        $DB->commit();
        showmsg('审核通过，奖励已发放！<br/><br/><a href="./sitetask-check.php">>>返回审核列表</a>', 1);
    } catch (Exception $e) {
        $DB->rollBack();
        showmsg('操作失败：' . $e->getMessage(), 4);
    }
} elseif ($my == "reject") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== md5(session_id() . SYS_KEY)) {
        showmsg("CSRF验证失败，请刷新页面后重试！", 4);
    }
    $id = intval($_GET["id"]);
    $remark = isset($_POST["remark"]) ? daddslashes(trim($_POST["remark"])) : '';
    
    $log = $DB->getRow("SELECT * FROM pre_sitetask_log WHERE id=:id AND status=0 LIMIT 1", array(':id' => $id));
    
    if (!$log) {
        showmsg('记录不存在或已处理！', 3);
    }
    
    $result = $DB->exec("UPDATE pre_sitetask_log SET status=2, remark=:remark WHERE id=:id", array(':remark' => $remark, ':id' => $id));
    if ($result !== false) {
        showmsg('已拒绝该申请！<br/><br/><a href="./sitetask-check.php">>>返回审核列表</a>', 1);
    } else {
        showmsg('操作失败！' . $DB->error(), 4);
    }
} elseif ($my == "reject_form") {
    $id = intval($_GET["id"]);
    $log = $DB->getRow("SELECT * FROM pre_sitetask_log WHERE id=:id AND status=0 LIMIT 1", array(':id' => $id));
    
    if (!$log) {
        showmsg('记录不存在或已处理！', 3);
    }
    ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">拒绝申请</h3></div>
        <div class="">
            <form action="./sitetask-check.php?my=reject&id=<?php echo $id;?>" method="post" class="form" role="form">
                <input type="hidden" name="csrf_token" value="<?php echo md5(session_id() . SYS_KEY); ?>">
                <div class="form-group">
                    <label>拒绝理由</label>
                    <textarea class="form-control" name="remark" rows="3" placeholder="请输入拒绝理由（可选）"></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" value="确认拒绝" class="btn btn-danger btn-block"/>
                </div>
            </form>
            <br/><a href="./sitetask-check.php">>>返回审核列表</a>
        </div>
    </div>
    <?php
} else {
    $status = isset($_GET["status"]) ? intval($_GET["status"]) : 0;
    
    if ($status == 0) {
        $where = "status=0";
        $titleText = "待审核任务申请";
    } elseif ($status == 1) {
        $where = "status=1";
        $titleText = "已通过任务申请";
    } elseif ($status == 2) {
        $where = "status=2";
        $titleText = "已拒绝任务申请";
    } else {
        $where = "1";
        $titleText = "全部任务申请";
    }
    
    $numrows = $DB->getColumn("SELECT COUNT(*) FROM pre_sitetask_log WHERE {$where}");
    $pagesize = 30;
    $pages = ceil($numrows / $pagesize);
    $page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
    $offset = $pagesize * ($page - 1);
    ?>
    <div class="col-md-12 center-block" style="float: none;">
        <div class="block">
            <div class="block-title clearfix">
                <h2 id="blocktitle"></h2>
            </div>
            <div class="form-inline">
                <a href="./sitetask-check.php?status=0" class="btn <?php echo $status == 0 ? 'btn-primary' : 'btn-default'; ?>">待审核</a>
                <a href="./sitetask-check.php?status=1" class="btn <?php echo $status == 1 ? 'btn-primary' : 'btn-default'; ?>">已通过</a>
                <a href="./sitetask-check.php?status=2" class="btn <?php echo $status == 2 ? 'btn-primary' : 'btn-default'; ?>">已拒绝</a>
                <a href="./sitetask-check.php?status=-1" class="btn <?php echo $status == -1 ? 'btn-primary' : 'btn-default'; ?>">全部</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>用户</th>
                            <th>任务名称</th>
                            <th>奖励金额</th>
                            <th>提交时间</th>
                            <th>状态</th>
                            <th>备注</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rs = $DB->query("SELECT * FROM pre_sitetask_log WHERE {$where} ORDER BY id DESC LIMIT {$offset},{$pagesize}");
                        while ($res = $rs->fetch()) {
                            $user = $DB->getRow("SELECT user FROM pre_user WHERE uid='{$res['userid']}' LIMIT 1");
                            $username = $user ? $user['user'] : '未知用户';
                            
                            $statusText = '';
                            $statusClass = '';
                            switch ($res['status']) {
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
                                <td>' . $res['id'] . '</td>
                                <td>' . $username . '</td>
                                <td>' . $res['taskname'] . '</td>
                                <td><span class="text-danger">' . $res['money'] . ' 元</span></td>
                                <td>' . $res['addtime'] . '</td>
                                <td><span class="label ' . $statusClass . '">' . $statusText . '</span></td>
                                <td>' . ($res['remark'] ? $res['remark'] : '-') . '</td>
                                <td>';
                            
                            if ($res['status'] == 0) {
                                $csrfToken = md5(session_id() . SYS_KEY);
                                echo '<a href="./sitetask-check.php?my=pass&id=' . $res['id'] . '&csrf_token=' . $csrfToken . '" class="btn btn-success btn-xs" onclick="return confirm(\'确定通过该申请并发放奖励？\')">通过</a>&nbsp;';
                                echo '<a href="./sitetask-check.php?my=reject_form&id=' . $res['id'] . '" class="btn btn-danger btn-xs">拒绝</a>';
                            }
                            
                            echo '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <ul class="pagination">
                <?php
                $first = 1;
                $prev = $page - 1;
                $next = $page + 1;
                $last = $pages;
                if ($page > 1) {
                    echo "<li><a href=\"./sitetask-check.php?status={$status}&page={$first}\">首页</a></li>";
                    echo "<li><a href=\"./sitetask-check.php?status={$status}&page={$prev}\">&laquo;</a></li>";
                } else {
                    echo "<li class=\"disabled\"><a>首页</a></li>";
                    echo "<li class=\"disabled\"><a>&laquo;</a></li>";
                }
                $start = $page - 10 > 1 ? $page - 10 : 1;
                $end = $page + 10 < $pages ? $page + 10 : $pages;
                for ($i = $start; $i < $page; $i++) {
                    echo "<li><a href=\"./sitetask-check.php?status={$status}&page={$i}\">{$i}</a></li>";
                }
                echo "<li class=\"disabled\"><a>{$page}</a></li>";
                for ($i = $page + 1; $i <= $end; $i++) {
                    echo "<li><a href=\"./sitetask-check.php?status={$status}&page={$i}\">{$i}</a></li>";
                }
                if ($page < $pages) {
                    echo "<li><a href=\"./sitetask-check.php?status={$status}&page={$next}\">&raquo;</a></li>";
                    echo "<li><a href=\"./sitetask-check.php?status={$status}&page={$last}\">尾页</a></li>";
                } else {
                    echo "<li class=\"disabled\"><a>&raquo;</a></li>";
                    echo "<li class=\"disabled\"><a>尾页</a></li>";
                }
                ?>
            </ul>
        </div>
    </div>
    <script>
        $("#blocktitle").html('<?php echo $titleText; ?>（共 <?php echo $numrows; ?> 条）');
    </script>
    <?php
}
?></body>
</html>