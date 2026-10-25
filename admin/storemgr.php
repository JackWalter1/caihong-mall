<?php
/**
 * 店铺管理
 */
include("../includes/common.php");
$title = '店铺管理';
include './head.php';
if ($islogin == 1) {} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$my = isset($_GET['my']) ? $_GET['my'] : null;

// 保存修改
if ($my == 'save') {
    $zid = intval($_POST['zid']);
    $sitename = daddslashes($_POST['sitename']);
    $store_desc = daddslashes($_POST['store_desc']);
    $anounce = daddslashes($_POST['anounce']);
    $deposit = floatval($_POST['deposit']);
    $verified = intval($_POST['verified']);
    $status = intval($_POST['status']);
    $store_avatar = daddslashes($_POST['store_avatar']);
    $qq = daddslashes($_POST['qq']);
    $phone = daddslashes($_POST['phone']);
    $email = daddslashes($_POST['email']);
    $kfqq = daddslashes($_POST['kfqq']);
    $kfwx = daddslashes($_POST['kfwx']);
    $pay_methods = daddslashes($_POST['pay_methods']);
    $terms_content = $_POST['terms_content'];

    $sql = "UPDATE `pre_site` SET sitename=:sitename, store_desc=:store_desc, anounce=:anounce, deposit=:deposit, verified=:verified, status=:status, store_avatar=:store_avatar, qq=:qq, phone=:phone, email=:email, kfqq=:kfqq, kfwx=:kfwx, pay_methods=:pay_methods, terms_content=:terms_content WHERE zid=:zid";
    $stmt = $DB->prepare($sql);
    if ($stmt->execute([':sitename' => $sitename, ':store_desc' => $store_desc, ':anounce' => $anounce, ':deposit' => $deposit, ':verified' => $verified, ':status' => $status, ':store_avatar' => $store_avatar, ':qq' => $qq, ':phone' => $phone, ':email' => $email, ':kfqq' => $kfqq, ':kfwx' => $kfwx, ':pay_methods' => $pay_methods, ':terms_content' => $terms_content, ':zid' => $zid])) {
        exit("<script language='javascript'>alert('保存成功！');window.location.href='./storemgr.php';</script>");
    } else {
        exit("<script language='javascript'>alert('保存失败！');history.go(-1);</script>");
    }
}

// 编辑表单
if ($my == 'edit') {
    $zid = intval($_GET['zid']);
    $row = $DB->getRow("SELECT * FROM pre_site WHERE zid=:zid", [':zid' => $zid]);
    if (!$row) exit("<script language='javascript'>alert('店铺不存在！');window.location.href='./storemgr.php';</script>");
    ?>
    <div class="col-md-12 center-block" style="float: none;">
        <div class="block">
            <div class="block-title"><h2>编辑店铺 - <?php echo htmlspecialchars($row['sitename'] ?: $row['user']); ?></h2></div>
            <div class="block-content">
                <?php
                $store_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/store.php?zid=' . $row['zid'];
                ?>
                <div class="alert alert-info">
                    <i class="fa fa-link"></i> <strong>店铺链接：</strong>
                    <code id="storeLink" style="background:#fff;padding:4px 8px;border-radius:4px;"><?php echo $store_link; ?></code>
                    <a href="<?php echo $store_link; ?>" target="_blank" class="btn btn-sm btn-success" style="margin-left:10px;"><i class="fa fa-external-link"></i> 访问店铺</a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="copyStoreLink()"><i class="fa fa-copy"></i> 复制链接</button>
                </div>
                <script>
                function copyStoreLink() {
                    var link = document.getElementById('storeLink').innerText;
                    var input = document.createElement('input');
                    input.value = link;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                    alert('店铺链接已复制：' + link);
                }
                </script>
                <form action="./storemgr.php?my=save" method="post" class="form-horizontal">
                    <input type="hidden" name="zid" value="<?php echo $row['zid']; ?>">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺名称</label>
                        <div class="col-sm-8"><input type="text" name="sitename" class="form-control" value="<?php echo htmlspecialchars($row['sitename']); ?>"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺简介</label>
                        <div class="col-sm-8"><input type="text" name="store_desc" class="form-control" value="<?php echo htmlspecialchars($row['store_desc']); ?>" placeholder="如：专业虚拟卡密·知识付费·资源下载交易平台"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺公告</label>
                        <div class="col-sm-8"><textarea name="anounce" class="form-control" rows="4"><?php echo htmlspecialchars($row['anounce']); ?></textarea></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">保证金(元)</label>
                        <div class="col-sm-8"><input type="number" step="0.01" name="deposit" class="form-control" value="<?php echo $row['deposit']; ?>" placeholder="如：500"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">认证状态</label>
                        <div class="col-sm-8">
                            <select name="verified" class="form-control">
                                <option value="0" <?php echo $row['verified'] == 0 ? 'selected' : ''; ?>>未认证</option>
                                <option value="1" <?php echo $row['verified'] == 1 ? 'selected' : ''; ?>>已认证</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺状态</label>
                        <div class="col-sm-8">
                            <select name="status" class="form-control">
                                <option value="1" <?php echo $row['status'] == 1 ? 'selected' : ''; ?>>正常</option>
                                <option value="0" <?php echo $row['status'] == 0 ? 'selected' : ''; ?>>关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">店铺头像URL</label>
                        <div class="col-sm-8"><input type="text" name="store_avatar" class="form-control" value="<?php echo htmlspecialchars($row['store_avatar']); ?>" placeholder="留空则使用首字头像"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">联系QQ</label>
                        <div class="col-sm-8"><input type="text" name="qq" class="form-control" value="<?php echo htmlspecialchars($row['qq']); ?>" placeholder="商家联系QQ"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">联系电话</label>
                        <div class="col-sm-8"><input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($row['phone']); ?>" placeholder="商家联系电话"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">联系邮箱</label>
                        <div class="col-sm-8"><input type="text" name="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>" placeholder="商家联系邮箱"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">客服QQ</label>
                        <div class="col-sm-8"><input type="text" name="kfqq" class="form-control" value="<?php echo htmlspecialchars($row['kfqq']); ?>" placeholder="客服QQ号"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">客服微信</label>
                        <div class="col-sm-8"><input type="text" name="kfwx" class="form-control" value="<?php echo htmlspecialchars($row['kfwx']); ?>" placeholder="客服微信号"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">支付方式</label>
                        <div class="col-sm-8">
                            <label class="checkbox-inline"><input type="checkbox" name="pay_method_alipay" value="alipay" <?php echo strpos($row['pay_methods'], 'alipay') !== false ? 'checked' : ''; ?>> 支付宝H5</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pay_method_qqpay" value="qqpay" <?php echo strpos($row['pay_methods'], 'qqpay') !== false ? 'checked' : ''; ?>> QQ支付</label>
                            <label class="checkbox-inline"><input type="checkbox" name="pay_method_wxpay" value="wxpay" <?php echo strpos($row['pay_methods'], 'wxpay') !== false ? 'checked' : ''; ?>> 微信支付</label>
                            <input type="hidden" name="pay_methods" id="pay_methods" value="<?php echo htmlspecialchars($row['pay_methods']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">自动发货条款</label>
                        <div class="col-sm-8"><textarea name="terms_content" class="form-control" rows="6" placeholder="支持HTML标签"><?php echo htmlspecialchars($row['terms_content']); ?></textarea></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <button type="submit" class="btn btn-primary" onclick="buildPayMethods()"><i class="fa fa-save"></i> 保存</button>
                            <a href="./storemgr.php" class="btn btn-default">返回</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    function buildPayMethods() {
        var methods = [];
        if (document.querySelector('input[name="pay_method_alipay"]').checked) methods.push('alipay');
        if (document.querySelector('input[name="pay_method_qqpay"]').checked) methods.push('qqpay');
        if (document.querySelector('input[name="pay_method_wxpay"]').checked) methods.push('wxpay');
        document.getElementById('pay_methods').value = methods.join(',');
    }
    </script>
    <?php
    include './foot.php';
    exit;
}

// 列表页
?>
<style>
.table>tbody>tr>td { vertical-align: middle; }
.badge-verify { background: #52c41a; color: #fff; }
.badge-noverify { background: #999; color: #fff; }
</style>
<div class="col-md-12 center-block" style="float: none;">
    <div class="block">
        <div class="block-title clearfix">
            <h2>店铺管理</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>用户名</th>
                        <th>店铺名称</th>
                        <th>认证</th>
                        <th>保证金</th>
                        <th>余额</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $rs = $DB->query("SELECT zid, user, sitename, verified, deposit, rmb, status FROM pre_site ORDER BY zid ASC");
                while ($row = $rs->fetch()) {
                    echo '<tr>';
                    echo '<td>' . $row['zid'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['user']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['sitename'] ?: '-') . '</td>';
                    echo '<td>' . ($row['verified'] == 1 ? '<span class="badge badge-verify">已认证</span>' : '<span class="badge badge-noverify">未认证</span>') . '</td>';
                    echo '<td>¥' . $row['deposit'] . '</td>';
                    echo '<td>¥' . $row['rmb'] . '</td>';
                    echo '<td>' . ($row['status'] == 1 ? '<span class="label label-success">正常</span>' : '<span class="label label-danger">关闭</span>') . '</td>';
                    echo '<td><a href="./storemgr.php?my=edit&zid=' . $row['zid'] . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> 编辑</a> <a href="../store.php?zid=' . $row['zid'] . '" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> 查看</a></td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include './foot.php'; ?>




