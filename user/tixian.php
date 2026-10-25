<?php
/**
 * 余额提现 - 现代化卡片式设计
**/
include("../includes/common.php");
$title='余额提现';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

if($conf['fenzhan_tixian']==0)showmsg('当前站点未开放提现功能！');
if($userrow['power']==0){
	showmsg('你没有权限使用此功能！',3);
}

function display_zt($zt,$id){
	if($zt==2)
		return '<a onclick="getResult('.$id.')" style="color:#ef4444;cursor:pointer;">查看失败原因</a>';
	elseif($zt==1)
		return '<span class="mc-tag mc-tag-success">已完成</span>';
	else
		return '<span class="mc-tag mc-tag-warning">处理中</span>';
}
function display_type($type){
	if($type==1) return '<i class="fa fa-weixin" style="color:#07c160"></i> 微信';
	elseif($type==2) return '<i class="fa fa-qq" style="color:#12b7f5"></i> QQ钱包';
	else return '<i class="fa fa-credit-card" style="color:#1677ff"></i> 支付宝';
}

// 处理提现提交
if(isset($_POST['money']))
{
if(!checkRefererHost())exit();
$money=daddslashes(strip_tags($_POST['money']));
if(!is_numeric($money) || !preg_match('/^[0-9.]+$/', $money))showmsg('提现金额输入不规范',3);
list($fee, $realmoney) = calc_tixian_fee($money, $userrow['pay_type'], 'user');
if ($fee === null) exit("<script language='javascript'>alert('该结算方式暂不支持提现，请先在【修改提现方式】中更换结算方式！');history.go(-1);</script>");
if($conf['fenzhan_skimg']==1 && !file_exists(ROOT.'assets/img/skimg/sk_'.$userrow['zid'].'.png')){
	exit("<script language='javascript'>alert('您还未上传收款图！');window.location.href='uset.php?mod=skimg';</script>");
}elseif(empty($userrow['pay_account']) || empty($userrow['pay_name'])){
	exit("<script language='javascript'>alert('您还未设置收款账号！');history.go(-1);</script>");
}

$DB->beginTransaction();
$userrow = $DB->getRow("SELECT * FROM pre_site WHERE zid='{$userrow['zid']}' LIMIT 1 FOR UPDATE");
if($conf['tixian_limit']==1 && $money>$userrow['rmbtc'] || $money>$userrow['rmb'] || $money<=0){
	exit("<script language='javascript'>alert('所输入的提现金额大于你所拥有的余额！');history.go(-1);</script>");
}
if($money<$conf['tixian_min']){
	exit("<script language='javascript'>alert('单笔提现金额不能低于{$conf['tixian_min']}元！');history.go(-1);</script>");
}
$sql = "INSERT INTO `pre_tixian` (`zid`, `money`, `realmoney`, `pay_type`, `pay_account`, `pay_name`, `status`, `addtime`) VALUES (:zid, :money, :realmoney, :pay_type, :pay_account, :pay_name, 0, NOW())";
$data = [':zid'=>$userrow['zid'], ':money'=>$money, ':realmoney'=>$realmoney, ':pay_type'=>$userrow['pay_type'], ':pay_account'=>$userrow['pay_account'], ':pay_name'=>$userrow['pay_name']];
if($DB->exec($sql, $data)){
	if($DB->exec("UPDATE pre_site SET rmb=rmb-:money,rmbtc=rmbtc-:money WHERE zid=:zid", [':money'=>$money, ':zid'=>$userrow['zid']])){
		addPointRecord($userrow['zid'], $money, '提现', '站点余额提现'.$money.'元');
		$DB->commit();
		exit("<script language='javascript'>alert('提现操作成功，本次实际到账金额:{$realmoney}元，请等待管理员人工转账！');window.location.href='tixian.php';</script>");
	}else{
		$DB->rollBack();
		exit("<script>alert('提现操作失败！');location.href='tixian.php';</script>");
	}
}else{
	exit("<script language='javascript'>alert('提现失败！');history.go(-1);</script>");
}
}

// 计算余额
if($conf['tixian_limit']==1){
	$notmoney = $userrow['rmb']-$userrow['rmbtc'];
	if($notmoney<0)$notmoney=0;
	$enmoney = $userrow['rmbtc'];
	if($enmoney>$userrow['rmb'])$enmoney = $userrow['rmb'];
}else{
	$notmoney = 0;
	$enmoney = $userrow['rmb'];
}

// 获取手续费配置供前端实时计算
$fee_cfg = tixian_fee_config('user', $userrow['pay_type']);
$fee_json = $fee_cfg ? json_encode($fee_cfg) : 'null';
// 旧版兜底费率
$old_rate = floatval($conf['tixian_rate']);
if($old_rate <= 0 || $old_rate > 100) $old_rate = 98;

// 累计已提现
$total_withdrawn = $DB->getColumn("SELECT COALESCE(SUM(money),0) FROM pre_tixian WHERE zid='{$userrow['zid']}' AND status!=2");

// 收款图
$skimg_exists = $conf['fenzhan_skimg']==1 && file_exists(ROOT.'assets/img/skimg/sk_'.$userrow['zid'].'.png');
$skimg_url = $skimg_exists ? '../assets/img/skimg/sk_'.$userrow['zid'].'.png' : '';

$numrows=$DB->getColumn("SELECT count(*) FROM pre_tixian WHERE zid='{$userrow['zid']}'");
?>

<!-- 余额统计卡片 -->
<div class="mc-stat-grid" style="margin-bottom:16px;">
  <div class="mc-stat-card">
    <div class="mc-stat-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb)"><i class="fa fa-money"></i></div>
    <div>
      <div class="mc-stat-label">可提现余额</div>
      <div class="mc-stat-value">¥ <?php echo number_format($enmoney,2) ?></div>
    </div>
  </div>
  <?php if($conf['tixian_limit']==1){?>
  <div class="mc-stat-card">
    <div class="mc-stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)"><i class="fa fa-lock"></i></div>
    <div>
      <div class="mc-stat-label">不可提现余额</div>
      <div class="mc-stat-value">¥ <?php echo number_format($notmoney,2) ?></div>
    </div>
  </div>
  <?php }?>
  <div class="mc-stat-card">
    <div class="mc-stat-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fa fa-history"></i></div>
    <div>
      <div class="mc-stat-label">累计已提现</div>
      <div class="mc-stat-value">¥ <?php echo number_format($total_withdrawn,2) ?></div>
    </div>
  </div>
</div>

<div class="mc-row">
  <!-- 左侧：提现表单 -->
  <div class="mc-col-8">
    <div class="mc-card">
      <div class="mc-card-head"><h3><i class="fa fa-credit-card" style="color:var(--mc-primary)"></i> 申请提现</h3></div>
      <div class="mc-card-body">
        <form method="post" class="mc-form" id="tixianForm">
          <!-- 收款信息 -->
          <div class="mc-payinfo-card">
            <div class="mc-payinfo-row">
              <div class="mc-payinfo-label">结算方式</div>
              <div class="mc-payinfo-value"><?php echo display_type($userrow['pay_type']); ?></div>
            </div>
            <?php if(!empty($userrow['pay_account'])){?>
            <div class="mc-payinfo-row">
              <div class="mc-payinfo-label">收款账号</div>
              <div class="mc-payinfo-value"><?php echo htmlspecialchars($userrow['pay_account']) ?></div>
            </div>
            <?php }?>
            <?php if(!empty($userrow['pay_name'])){?>
            <div class="mc-payinfo-row">
              <div class="mc-payinfo-label">真实姓名</div>
              <div class="mc-payinfo-value"><?php echo htmlspecialchars($userrow['pay_name']) ?></div>
            </div>
            <?php }?>
            <?php if($skimg_exists){?>
            <div class="mc-payinfo-row">
              <div class="mc-payinfo-label">收款二维码</div>
              <div class="mc-payinfo-value">
                <img src="<?php echo $skimg_url ?>" onclick="img('<?php echo $userrow['zid'] ?>')" style="width:80px;height:80px;object-fit:cover;border-radius:8px;cursor:pointer;border:1px solid #e5e7eb;">
              </div>
            </div>
            <?php }?>
            <div class="mc-payinfo-actions">
              <a href="uset.php?mod=user" class="mc-btn mc-btn-outline-primary mc-btn-sm"><i class="fa fa-edit"></i> 修改收款信息</a>
              <?php if($conf['fenzhan_skimg']==1){?>
              <a href="uset.php?mod=skimg" class="mc-btn mc-btn-outline-warning mc-btn-sm"><i class="fa fa-qrcode"></i> 修改收款图</a>
              <?php }?>
            </div>
          </div>

          <!-- 提现金额 -->
          <div class="mc-form-group">
            <label class="mc-form-label">提现金额 <span style="color:#ef4444">*</span></label>
            <div class="mc-input-group">
              <span class="mc-input-prefix">¥</span>
              <input type="text" name="money" id="moneyInput" class="mc-form-input" placeholder="请输入提现金额" required oninput="calcFeePreview()">
              <a href="javascript:inputMoney()" class="mc-input-suffix">全部提现</a>
            </div>
            <div class="mc-form-hint">单笔最低 <?php echo $conf['tixian_min'] ?> 元，<?php echo tixian_fee_desc('user')?></div>
          </div>

          <!-- 手续费预览 -->
          <div class="mc-fee-preview" id="feePreview" style="display:none;">
            <div class="mc-fee-row">
              <span>提现金额</span>
              <span class="mc-fee-amount">¥ <span id="pvMoney">0.00</span></span>
            </div>
            <div class="mc-fee-row">
              <span>手续费</span>
              <span class="mc-fee-fee">- ¥ <span id="pvFee">0.00</span></span>
            </div>
            <div class="mc-fee-row mc-fee-total">
              <span>实际到账</span>
              <span class="mc-fee-real">¥ <span id="pvReal">0.00</span></span>
            </div>
          </div>

          <button type="submit" class="mc-btn mc-btn-primary mc-btn-lg mc-btn-block" id="submitBtn">
            <i class="fa fa-check-circle"></i> 确认提现
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- 右侧：温馨提示 -->
  <div class="mc-col-4">
    <div class="mc-card">
      <div class="mc-card-head"><h3><i class="fa fa-info-circle" style="color:#f59e0b"></i> 温馨提示</h3></div>
      <div class="mc-card-body">
        <ul class="mc-tips-list">
          <li><i class="fa fa-check-circle" style="color:#10b981"></i> 提现申请提交后，管理员将在24小时内处理</li>
          <li><i class="fa fa-check-circle" style="color:#10b981"></i> 请确保收款账号和姓名正确无误</li>
          <li><i class="fa fa-check-circle" style="color:#10b981"></i> 到账时间以实际银行/支付平台为准</li>
          <li><i class="fa fa-check-circle" style="color:#10b981"></i> 如遇问题请联系客服处理</li>
          <?php if($conf['tixian_limit']==1){?>
          <li><i class="fa fa-info-circle" style="color:#3b82f6"></i> 充值、签到所得余额不可提现，可用于消费</li>
          <li><i class="fa fa-info-circle" style="color:#3b82f6"></i> 订单<?php echo $conf['tixian_days']>0?$conf['tixian_days'].'天':'即刻'?>后自动加入可提现余额</li>
          <?php }?>
        </ul>
      </div>
    </div>

    <div class="mc-card">
      <div class="mc-card-head"><h3><i class="fa fa-bolt" style="color:#3b82f6"></i> 快捷操作</h3></div>
      <div class="mc-card-body" style="display:flex;flex-direction:column;gap:10px;">
        <a href="uset.php?mod=user" class="mc-btn mc-btn-outline-primary mc-btn-block"><i class="fa fa-user"></i> 设置收款账号</a>
        <?php if($conf['fenzhan_skimg']==1){?>
        <a href="uset.php?mod=skimg" class="mc-btn mc-btn-outline-primary mc-btn-block"><i class="fa fa-qrcode"></i> 上传收款二维码</a>
        <?php }?>
        <a href="record.php" class="mc-btn mc-btn-outline-primary mc-btn-block"><i class="fa fa-list"></i> 查看资金明细</a>
      </div>
    </div>
  </div>
</div>

<!-- 提现记录 -->
<div class="mc-card">
  <div class="mc-card-head">
    <h3><i class="fa fa-list-alt" style="color:var(--mc-primary)"></i> 提现记录</h3>
    <span class="mc-card-badge">共 <?php echo $numrows ?> 条</span>
  </div>
  <div class="mc-card-body" style="padding:0;">
    <div class="mc-table-responsive">
      <table class="mc-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>提现金额</th>
            <th>实际到账</th>
            <th>提现方式</th>
            <th>收款账号</th>
            <th>申请时间</th>
            <th>完成时间</th>
            <th>状态</th>
          </tr>
        </thead>
        <tbody>
<?php
$rs=$DB->query("SELECT * FROM pre_tixian WHERE zid='{$userrow['zid']}' ORDER BY id DESC LIMIT 10");
$has_record = false;
while($res = $rs->fetch())
{
	$has_record = true;
	echo '<tr>';
	echo '<td><b>#'.$res['id'].'</b></td>';
	echo '<td style="color:#ef4444;">¥ '.$res['money'].'</td>';
	echo '<td style="color:#10b981;font-weight:600;">¥ '.$res['realmoney'].'</td>';
	echo '<td>'.display_type($res['pay_type']).'</td>';
	echo '<td>'.htmlspecialchars($res['pay_account']).'</td>';
	echo '<td>'.$res['addtime'].'</td>';
	echo '<td>'.($res['status']==1?$res['endtime']:'-').'</td>';
	echo '<td>'.display_zt($res['status'],$res['id']).'</td>';
	echo '</tr>';
}
if(!$has_record){
	echo '<tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:40px 0;"><i class="fa fa-inbox" style="font-size:36px;opacity:.4"></i><br><br>暂无提现记录</td></tr>';
}
?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include './foot.php';?>
<script>
// 手续费配置
var feeConfig = <?php echo $fee_json ?>;
var oldRate = <?php echo $old_rate ?>;
var payType = <?php echo intval($userrow['pay_type']) ?>;
var minMoney = <?php echo floatval($conf['tixian_min']) ?>;
var maxMoney = <?php echo floatval($enmoney) ?>;

function calcFee(money){
	if(!money || money <= 0) return {fee:0, real:0};
	var fee = 0;
	if(feeConfig){
		var mode = feeConfig.mode || 'percent';
		var rate = parseFloat(feeConfig.rate) || 0;
		if(mode === 'fixed'){
			fee = parseFloat(feeConfig.fixed) || 0;
		} else if(mode === 'mixed'){
			var minFee = parseFloat(feeConfig.min_fee) || 0;
			fee = Math.max(money * rate / 100, minFee);
		} else if(mode === 'capped'){
			var maxFee = parseFloat(feeConfig.max_fee) || 99999999;
			if(maxFee <= 0) maxFee = 99999999;
			fee = Math.min(money * rate / 100, maxFee);
		} else {
			fee = money * rate / 100;
		}
	} else {
		fee = money * (100 - oldRate) / 100;
	}
	if(fee < 0) fee = 0;
	if(fee > money) fee = money;
	fee = Math.round(fee * 100) / 100;
	return {fee: fee, real: Math.round((money - fee) * 100) / 100};
}

function calcFeePreview(){
	var val = parseFloat(document.getElementById('moneyInput').value);
	var box = document.getElementById('feePreview');
	if(!val || val <= 0){
		box.style.display = 'none';
		return;
	}
	var r = calcFee(val);
	box.style.display = 'block';
	document.getElementById('pvMoney').textContent = val.toFixed(2);
	document.getElementById('pvFee').textContent = r.fee.toFixed(2);
	document.getElementById('pvReal').textContent = r.real.toFixed(2);
}

function inputMoney(){
	document.getElementById('moneyInput').value = maxMoney;
	calcFeePreview();
}

// 表单验证
document.getElementById('tixianForm').addEventListener('submit', function(e){
	var val = parseFloat(document.getElementById('moneyInput').value);
	if(!val || val <= 0){
		e.preventDefault();
		layer.msg('请输入提现金额');
		return false;
	}
	if(val < minMoney){
		e.preventDefault();
		layer.msg('单笔提现金额不能低于' + minMoney + '元');
		return false;
	}
	if(val > maxMoney){
		e.preventDefault();
		layer.msg('提现金额不能超过可提现余额');
		return false;
	}
});

function getResult(id) {
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax_user.php?act=tixian_note',
		data : {id:id},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.result!=null)
				layer.alert(data.result);
			else
				layer.alert('提现失败，如有疑问请联系主站站长');
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
</script>
</body>
</html>
