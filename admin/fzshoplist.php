<?php
/**
 * 分站商品管理（后台配套）
 * 管理分站站长在用户中心自主添加的店铺商品：开关、上限、上下架、删除
**/
include "../includes/common.php";
$title = "分站商品管理";
include "./head.php";
if ($islogin == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("shop", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;

// ===== 保存配置 =====
if ($my == "saveconf") {
    $open = intval($_POST["fenzhan_shop_add"]) == 1 ? 1 : 0;
    $max = intval($_POST["fenzhan_shop_max"]);
    if ($max <= 0 || $max > 500) $max = 50;
    saveSetting('fenzhan_shop_add', $open);
    saveSetting('fenzhan_shop_max', $max);
    global $CACHE;
    $CACHE->pre_update(); // 重建配置缓存，立即生效
    showmsg("配置保存成功！<br/><br/><a href='./fzshoplist.php'>>>返回分站商品管理</a>", 1);
}

// ===== 上下架 =====
if ($my == "toggle") {
    $tid = intval($_GET["tid"]);
    $tool = $DB->getRow("SELECT tid,close,name,zid FROM pre_tools WHERE tid=:tid AND zid>1 LIMIT 1", array(':tid' => $tid));
    if (!$tool) showmsg("当前商品不存在！", 3);
    $to = $tool["close"] == 1 ? 0 : 1;
    $DB->exec("UPDATE pre_tools SET close=:c WHERE tid=:tid", array(':c' => $to, ':tid' => $tid));
    showmsg(($to == 1 ? "已强制下架" : "已恢复上架") . "分站【{$tool['zid']}】的商品：{$tool['name']}<br/><br/><a href='./fzshoplist.php'>>>返回分站商品管理</a>", 1);
}

// ===== 删除 =====
if ($my == "del") {
    $tid = intval($_GET["tid"]);
    $tool = $DB->getRow("SELECT tid,name,zid FROM pre_tools WHERE tid=:tid AND zid>1 LIMIT 1", array(':tid' => $tid));
    if (!$tool) showmsg("当前商品不存在！", 3);
    $sold = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE tid=:tid", array(':tid' => $tid));
    if ($sold > 0) {
        $DB->exec("UPDATE pre_tools SET close=1,active=0 WHERE tid=:tid", array(':tid' => $tid));
        showmsg("该商品存在{$sold}笔历史订单，已改为强制下架停售（保留订单数据）。<br/><br/><a href='./fzshoplist.php'>>>返回分站商品管理</a>", 1);
    }
    $DB->exec("DELETE FROM pre_faka WHERE tid=:tid AND orderid=0", array(':tid' => $tid));
    $DB->exec("DELETE FROM pre_tools WHERE tid=:tid", array(':tid' => $tid));
    showmsg("已删除分站【{$tool['zid']}】的商品：{$tool['name']}（含未售卡密）<br/><br/><a href='./fzshoplist.php'>>>返回分站商品管理</a>", 1);
}

// ===== 列表 =====
$fzid = isset($_GET["fzid"]) ? intval($_GET["fzid"]) : 0;
$kw = isset($_GET["kw"]) ? trim(daddslashes($_GET["kw"])) : "";
$page = max(1, intval($_GET["page"]));
$pagesize = 20;
$where = "A.zid>1";
$params = array();
if ($fzid > 0) { $where .= " AND A.zid=:fzid"; $params[':fzid'] = $fzid; }
if ($kw !== "") { $where .= " AND A.name LIKE :kw"; $params[':kw'] = '%' . $kw . '%'; }
$numrows = $DB->getColumn("SELECT COUNT(*) FROM pre_tools A WHERE {$where}", $params);
$offset = ($page - 1) * $pagesize;
$rs = $DB->query("SELECT A.*,B.user,B.sitename FROM pre_tools A LEFT JOIN pre_site B ON A.zid=B.zid WHERE {$where} ORDER BY A.tid DESC LIMIT {$offset},{$pagesize}", $params);
$total_open = isset($conf['fenzhan_shop_add']) ? intval($conf['fenzhan_shop_add']) : 1;
$total_max = isset($conf['fenzhan_shop_max']) && $conf['fenzhan_shop_max'] > 0 ? intval($conf['fenzhan_shop_max']) : 50;
$curlmap = array(4 => '自动发卡', 5 => '自动发内容', 0 => '人工处理');
?>
<div class="row">
<div class="col-sm-12 col-md-5 col-lg-4">
<div class="block">
<div class="block-title"><h3 class="panel-title">分站商品功能配置</h3></div>
<div class="">
<form action="./fzshoplist.php?my=saveconf" method="POST" style="padding:15px">
<div class="form-group">
<label>允许分站自主添加商品：</label>
<select class="form-control" name="fenzhan_shop_add">
<option value="1"<?php echo $total_open == 1 ? " selected" : ""; ?>>开启（分站可在用户中心添加/管理自己的商品）</option>
<option value="0"<?php echo $total_open == 0 ? " selected" : ""; ?>>关闭（关闭后分站将无法添加新商品，已上架商品不受影响）</option>
</select>
</div>
<div class="form-group">
<label>每个分站商品数上限：</label>
<input type="text" class="form-control" name="fenzhan_shop_max" value="<?php echo $total_max; ?>" placeholder="默认50，1-500">
</div>
<input type="submit" class="btn btn-primary btn-block" value="保存配置">
</form>
</div>
</div>
</div>
<div class="col-sm-12 col-md-7 col-lg-8">
<div class="block">
<div class="block-title"><h3 class="panel-title">分站商品列表（共 <?php echo $numrows; ?> 件）</h3></div>
<div class="" style="padding:15px">
<form action="./fzshoplist.php" method="GET" class="form-inline" style="margin-bottom:12px">
<input type="text" class="form-control" name="kw" value="<?php echo htmlspecialchars($kw); ?>" placeholder="商品名称" style="width:150px">
<input type="text" class="form-control" name="fzid" value="<?php echo $fzid > 0 ? $fzid : ''; ?>" placeholder="分站ID" style="width:90px">
<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> 搜索</button>
&nbsp;<span class="text-muted" style="font-size:12px">分站商品由分站站长在用户中心「商品管理-我的商品」中添加并实时上架到其店铺页</span>
</form>
<div class="table-responsive">
<table class="table table-striped table-bordered table-condensed">
<thead><tr><th>ID</th><th>所属分站</th><th>商品名称</th><th>价格</th><th>发货方式</th><th>库存</th><th>状态</th><th>操作</th></tr></thead>
<tbody>
<?php
$found = false;
while ($r = $rs->fetch()) {
    $found = true;
    $stock = $r['is_curl'] == 4 ? $DB->getColumn("SELECT COUNT(*) FROM pre_faka WHERE tid=:tid AND orderid=0", array(':tid' => $r['tid'])) : ($r['is_curl'] == 5 ? '∞' : '—');
    $sitename = $r['sitename'] ? $r['sitename'] : $r['user'];
    echo '<tr>'
        . '<td>' . $r['tid'] . '</td>'
        . '<td><a href="./sitelist.php?kw=' . $r['zid'] . '" title="分站ID：' . $r['zid'] . '">' . ($sitename ? htmlspecialchars($sitename) : '#' . $r['zid']) . '</a></td>'
        . '<td><b>' . htmlspecialchars($r['name']) . '</b></td>'
        . '<td>¥' . $r['price'] . '</td>'
        . '<td>' . (isset($curlmap[$r['is_curl']]) ? $curlmap[$r['is_curl']] : '-') . '</td>'
        . '<td>' . ($stock === 0 ? '<span class="label label-danger">0</span>' : $stock) . '</td>'
        . '<td>' . ($r['close'] == 1 ? '<span class="label label-default">已下架</span>' : '<span class="label label-success">上架中</span>') . '</td>'
        . '<td>'
        . '<a href="./store.php?zid=' . $r['zid'] . '" target="_blank" class="btn btn-info btn-xs">店铺</a>&nbsp;'
        . '<a href="./fzshoplist.php?my=toggle&tid=' . $r['tid'] . '" class="btn btn-warning btn-xs">' . ($r['close'] == 1 ? '上架' : '下架') . '</a>&nbsp;'
        . '<a href="./fzshoplist.php?my=del&tid=' . $r['tid'] . '" class="btn btn-danger btn-xs" onclick="return confirm(\'确定删除该分站商品？未售卡密将一并删除\')">删除</a>'
        . '</td></tr>';
}
if (!$found) echo '<tr><td colspan="8" class="text-center text-muted" style="padding:25px">暂无分站商品</td></tr>';
?>
</tbody>
</table>
</div>
<?php
$pages = ceil($numrows / $pagesize);
if ($pages > 1) {
    $link = './fzshoplist.php?kw=' . urlencode($kw) . '&fzid=' . $fzid . '&page=';
    echo '<ul class="pagination">';
    for ($i = 1; $i <= $pages; $i++) {
        echo '<li' . ($i == $page ? ' class="active"' : '') . '><a href="' . $link . $i . '">' . $i . '</a></li>';
    }
    echo '</ul>';
}
?>
</div>
</div>
</div>
</div>
<?php include "./foot.php"; ?>
