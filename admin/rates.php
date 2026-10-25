<?php
/**
 * 费率与手续费中心
 * - 用户（分站站长）提现手续费
 * - 供货商提现手续费
 * - 计费模式：percent=按百分比 / fixed=每笔固定 / mixed=百分比+保底 / capped=百分比+封顶
 * - 支持统一设置，或按结算方式（支付宝/微信/QQ钱包）分别设置并停启
 * - 每次修改自动记录操作日志
 */
include "../includes/common.php";

if ($islogin != 1) exit("<script language='javascript'>window.location.href='./login.php';</script>");
adminpermission('set', 1);

// 初始化手续费修改日志表
$DB->exec("CREATE TABLE IF NOT EXISTS `pre_rate_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(10) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `addtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

// 保存
if ($_SERVER['REQUEST_METHOD'] == 'POST' && checkRefererHost()) {
    $act = isset($_POST['act']) ? $_POST['act'] : '';
    if ($act == 'save_user' || $act == 'save_sup') {
        $prefix = ($act == 'save_sup') ? 'sup_' : '';
        $role = $prefix ? 'sup' : 'user';
        $bytype = isset($_POST['bytype']) ? 1 : 0;
        $def = array(
            'mode' => in_array($_POST['def_mode'], array('percent','fixed','mixed','capped')) ? $_POST['def_mode'] : 'percent',
            'rate' => round(floatval($_POST['def_rate']), 2),
            'fixed' => round(floatval($_POST['def_fixed']), 2),
            'min_fee' => round(floatval($_POST['def_min']), 2),
            'max_fee' => round(floatval($_POST['def_max']), 2),
        );
        $types = array();
        foreach (array('3','1','2') as $t) {
            if (!isset($_POST['t'.$t.'_mode'])) continue;
            $types[$t] = array(
                'on' => isset($_POST['t'.$t.'_on']) ? 1 : 0,
                'mode' => in_array($_POST['t'.$t.'_mode'], array('percent','fixed','mixed','capped')) ? $_POST['t'.$t.'_mode'] : 'percent',
                'rate' => round(floatval($_POST['t'.$t.'_rate']), 2),
                'fixed' => round(floatval($_POST['t'.$t.'_fixed']), 2),
                'min_fee' => round(floatval($_POST['t'.$t.'_min']), 2),
                'max_fee' => round(floatval($_POST['t'.$t.'_max']), 2),
            );
        }
        saveSetting($prefix.'tixian_fee_bytype', $bytype);
        saveSetting($prefix.'tixian_fee_default', json_encode($def, JSON_UNESCAPED_UNICODE));
        saveSetting($prefix.'tixian_fee_types', json_encode($types, JSON_UNESCAPED_UNICODE));
        $DB->exec("INSERT INTO pre_rate_log (role, content, addtime) VALUES (:r, :c, NOW())", array(
            ':r' => $role,
            ':c' => '更新' . ($role == 'sup' ? '供货商' : '用户') . '提现手续费：' . tixian_fee_desc($role) . '；' . ($bytype ? '按结算方式分别设置' : '统一费率'),
        ));
        $CACHE->pre_update();
        exit("<script language='javascript'>alert('保存成功，已立即生效！');window.location.href='./rates.php';</script>");
    }
}

/**
 * 渲染一个角色的手续费设置表单
 */
function rate_section($prefix, $title, $roleName)
{
    global $conf;
    $bytype = !empty($conf[$prefix.'tixian_fee_bytype']);
    $def = json_decode($conf[$prefix.'tixian_fee_default'], true);
    if (!is_array($def)) $def = array('mode'=>'percent','rate'=>round(100-floatval($conf[$prefix.'tixian_rate']),2),'fixed'=>0,'min_fee'=>0,'max_fee'=>0);
    $types = json_decode($conf[$prefix.'tixian_fee_types'], true);
    if (!is_array($types)) $types = array();
    $act = $prefix ? 'save_sup' : 'save_user';
    $modeNames = array('percent'=>'按百分比','fixed'=>'每笔固定金额','mixed'=>'百分比＋保底','capped'=>'百分比＋封顶');
    ?>
    <div class="panel panel-default">
        <div class="panel-heading font-bold"><i class="fa fa-percent"></i> <?php echo $title;?></div>
        <div class="panel-body">
        <form method="post" class="form-horizontal rate-form">
            <input type="hidden" name="act" value="<?php echo $act;?>">
            <div class="form-group">
                <label class="col-sm-3 control-label">设置方式</label>
                <div class="col-sm-8">
                    <label class="radio-inline"><input type="radio" name="bytype" value="0" <?php echo $bytype?'':'checked';?> onchange="toggleBytype('<?php echo $act;?>',false)"> 统一费率（所有结算方式相同）</label>
                    <label class="radio-inline"><input type="radio" name="bytype" value="1" <?php echo $bytype?'checked':'';?> onchange="toggleBytype('<?php echo $act;?>',true)"> 按结算方式分别设置</label>
                </div>
            </div>
            <div class="def-block" <?php echo $bytype?'style="display:none"':'';?>>
                <div class="form-group">
                    <label class="col-sm-3 control-label">计费模式</label>
                    <div class="col-sm-4"><select name="def_mode" class="form-control" onchange="updMode(this)"><?php foreach($modeNames as $m=>$n) echo '<option value="'.$m.'"'.($def['mode']==$m?' selected':'').'>'.$n.'</option>';?></select></div>
                </div>
                <div class="form-group f-rate" <?php echo ($def['mode']!='percent'&&$def['mode']!='mixed'&&$def['mode']!='capped')?'style="display:none"':'';?>><label class="col-sm-3 control-label">手续费费率(%)</label><div class="col-sm-4"><input type="text" name="def_rate" value="<?php echo isset($def['rate'])?$def['rate']:0;?>" class="form-control" placeholder="如 2 表示收2%"></div></div>
                <div class="form-group f-fixed" <?php echo $def['mode']=='fixed'?'':'style="display:none"';?>><label class="col-sm-3 control-label">固定手续费(元)</label><div class="col-sm-4"><input type="text" name="def_fixed" value="<?php echo isset($def['fixed'])?$def['fixed']:0;?>" class="form-control"></div></div>
                <div class="form-group f-min" <?php echo $def['mode']=='mixed'?'':'style="display:none"';?>><label class="col-sm-3 control-label">保底手续费(元)</label><div class="col-sm-4"><input type="text" name="def_min" value="<?php echo isset($def['min_fee'])?$def['min_fee']:0;?>" class="form-control" placeholder="不足此金额按此收"></div></div>
                <div class="form-group f-max" <?php echo $def['mode']=='capped'?'':'style="display:none"';?>><label class="col-sm-3 control-label">封顶手续费(元)</label><div class="col-sm-4"><input type="text" name="def_max" value="<?php echo isset($def['max_fee'])?$def['max_fee']:0;?>" class="form-control" placeholder="0=不封顶"></div></div>
            </div>
            <div class="type-block" <?php echo $bytype?'':'style="display:none"';?>>
                <table class="table table-bordered" style="margin-bottom:10px;">
                    <thead><tr><th style="width:70px;">开放</th><th style="width:100px;">结算方式</th><th>计费模式</th><th>费率(%)</th><th>固定(元)</th><th>保底(元)</th><th>封顶(元)</th></tr></thead>
                    <tbody>
                    <?php foreach(array('3'=>'支付宝','1'=>'微信','2'=>'QQ钱包') as $t=>$name){
                        $c = isset($types[$t]) && is_array($types[$t]) ? $types[$t] : $def;
                        $on = isset($types[$t]['on']) ? $types[$t]['on'] : 1;
                        ?>
                        <tr>
                            <td style="text-align:center;"><input type="checkbox" name="t<?php echo $t;?>_on" value="1" <?php echo $on?'checked':'';?>></td>
                            <td><?php echo $name;?></td>
                            <td><select name="t<?php echo $t;?>_mode" class="form-control input-sm" onchange="updMode(this)"><?php foreach($modeNames as $m=>$n) echo '<option value="'.$m.'"'.(isset($c['mode'])&&$c['mode']==$m?' selected':'').'>'.$n.'</option>';?></select></td>
                            <td><input type="text" name="t<?php echo $t;?>_rate" value="<?php echo isset($c['rate'])?$c['rate']:0;?>" class="form-control input-sm"></td>
                            <td><input type="text" name="t<?php echo $t;?>_fixed" value="<?php echo isset($c['fixed'])?$c['fixed']:0;?>" class="form-control input-sm"></td>
                            <td><input type="text" name="t<?php echo $t;?>_min" value="<?php echo isset($c['min_fee'])?$c['min_fee']:0;?>" class="form-control input-sm"></td>
                            <td><input type="text" name="t<?php echo $t;?>_max" value="<?php echo isset($c['max_fee'])?$c['max_fee']:0;?>" class="form-control input-sm"></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
                <p class="text-muted" style="margin-left:15px;">取消勾选"开放"后，该结算方式的提现申请会被拒绝，提示用户更换结算方式。</p>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <div class="col-sm-offset-3 col-sm-8">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 保存<?php echo $roleName;?>手续费设置</button>
                    <span class="text-muted" style="margin-left:10px;">保存后立即生效，用户提现页/常见问题会同步显示新规则</span>
                </div>
            </div>
        </form>
        </div>
    </div>
<?php
}

$title = '费率与手续费';
include './head.php';
?>
<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">费率与手续费中心</h3>
        </div>
        <div class="alert alert-info">
            <b>手动调整说明：</b>所有手续费在此设置，保存后立即生效；提现时按用户绑定的结算方式实时计算并写入"实际到账"。
        </div>

<?php rate_section('', '用户提现手续费（分站站长余额提现）', '用户'); ?>
<?php rate_section('sup_', '供货商提现手续费', '供货商'); ?>

        <div class="panel panel-default">
            <div class="panel-heading font-bold"><i class="fa fa-calculator"></i> 费率试算器（按当前已保存配置实时计算）</div>
            <div class="panel-body">
                <div class="form-inline">
                    <div class="form-group" style="margin-right:12px;"><label>提现金额</label> <input type="text" id="tryMoney" class="form-control" value="100" style="width:120px;" oninput="tryCalc()"></div>
                    <div class="form-group" style="margin-right:12px;"><label>身份</label> <select id="tryRole" class="form-control" onchange="tryCalc()"><option value="user">用户</option><option value="sup">供货商</option></select></div>
                    <div class="form-group"><label>结算方式</label> <select id="tryType" class="form-control" onchange="tryCalc()"><option value="3">支付宝</option><option value="1">微信</option><option value="2">QQ钱包</option></select></div>
                </div>
                <div id="tryResult" class="alert alert-info" style="margin-top:12px;margin-bottom:0;"></div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading font-bold"><i class="fa fa-history"></i> 修改日志（最近30条）</div>
            <div class="panel-body" style="padding:0;">
                <table class="table table-striped" style="margin-bottom:0;">
                    <thead><tr><th style="width:160px;">时间</th><th style="width:80px;">对象</th><th>内容</th></tr></thead>
                    <tbody>
                    <?php
                    $rs = $DB->query("SELECT * FROM pre_rate_log ORDER BY id DESC LIMIT 30");
                    $rows = $rs ? $rs->fetchAll() : array();
                    if (empty($rows)) echo '<tr><td colspan="3" style="text-align:center;color:#999;">暂无修改记录</td></tr>';
                    foreach ($rows as $r) {
                        echo '<tr><td>'.$r['addtime'].'</td><td>'.($r['role']=='sup'?'供货商':'用户').'</td><td>'.htmlspecialchars($r['content']).'</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="alert alert-warning">
            <i class="fa fa-info-circle"></i> <strong>说明：</strong>
            <ul style="margin:8px 0 0 18px;">
                <li>"百分比＋封顶"填 0 表示不封顶；"百分比＋保底"填 0 表示无保底。</li>
                <li>未保存过新配置前，系统沿用旧版全局费率（用户：系统设置→提现设置；供货商：供货商设置），升级后现有规则不受影响。</li>
            </ul>
        </div>
    </div>
</div>
<script>
var RATE_CONF = {
    user: {bytype:<?php echo !empty($conf['tixian_fee_bytype'])?1:0;?>, def:<?php echo !empty($conf['tixian_fee_default'])?$conf['tixian_fee_default']:'null';?>, types:<?php echo !empty($conf['tixian_fee_types'])?$conf['tixian_fee_types']:'{}';?>},
    sup: {bytype:<?php echo !empty($conf['sup_tixian_fee_bytype'])?1:0;?>, def:<?php echo !empty($conf['sup_tixian_fee_default'])?$conf['sup_tixian_fee_default']:'null';?>, types:<?php echo !empty($conf['sup_tixian_fee_types'])?$conf['sup_tixian_fee_types']:'{}';?>}
};
var RATE_NAMES = {3:'支付宝', 1:'微信', 2:'QQ钱包'};

function updMode(sel) {
    var scope = sel.name.indexOf('def_') === 0 ? sel.closest('.def-block') : sel.closest('tr');
    ['rate','fixed','min','max'].forEach(function(k) {
        var el = scope.querySelector('.f-' + k);
        if (!el) el = scope.querySelector('[name$="_' + k + '"]');
        if (el) el.style.display = 'none';
    });
    var map = {percent:['rate'], fixed:['fixed'], mixed:['rate','min'], capped:['rate','max']};
    (map[sel.value] || ['rate']).forEach(function(k) {
        var el = scope.querySelector('.f-' + k);
        if (!el) el = scope.querySelector('[name$="_' + k + '"]');
        if (el) el.style.display = '';
    });
}

function toggleBytype(act, on) {
    var form = document.querySelector('form input[name=act][value=' + act + ']').closest('form');
    form.querySelector('.type-block').style.display = on ? '' : 'none';
    form.querySelector('.def-block').style.display = on ? 'none' : '';
}

// 初始化：按当前模式显隐对应输入框
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select[name$="_mode"]').forEach(function(s) { updMode(s); });
    tryCalc();
});

function tryCalc() {
    var money = parseFloat(document.getElementById('tryMoney').value) || 0;
    var role = document.getElementById('tryRole').value;
    var type = document.getElementById('tryType').value;
    var c = RATE_CONF[role], cfg = null, note = '';
    if (c.bytype && c.types && typeof c.types[type] !== 'undefined') {
        cfg = c.types[type];
        if (!cfg.on) note = '（该结算方式已停用，用户将无法提现）';
    }
    if (!cfg) cfg = c.def;
    var html;
    if (!cfg) {
        html = '未配置新规则，沿用旧版全局费率';
    } else if (cfg.on === 0) {
        html = '该结算方式已停用，用户无法提现';
    } else {
        var fee = 0;
        if (cfg.mode == 'fixed') fee = parseFloat(cfg.fixed) || 0;
        else if (cfg.mode == 'mixed') fee = Math.max(money * (parseFloat(cfg.rate) || 0) / 100, parseFloat(cfg.min_fee) || 0);
        else if (cfg.mode == 'capped') { fee = money * (parseFloat(cfg.rate) || 0) / 100; if (parseFloat(cfg.max_fee) > 0) fee = Math.min(fee, parseFloat(cfg.max_fee)); }
        else fee = money * (parseFloat(cfg.rate) || 0) / 100;
        if (fee < 0) fee = 0; if (fee > money) fee = money;
        fee = Math.round(fee * 100) / 100;
        html = '手续费 <b style="color:#e74c3c">' + fee.toFixed(2) + '</b> 元，实际到账 <b style="color:#27ae60">' + (money - fee).toFixed(2) + '</b> 元 ' + note;
    }
    document.getElementById('tryResult').innerHTML = html;
}
</script>
<?php include './foot.php'; ?>
