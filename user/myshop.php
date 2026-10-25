<?php
/**
 * 我的商品 - 分站自主商品管理
 * 分站用户（power>0）可在此添加/管理自己店铺的商品
**/
include("../includes/common.php");
$title='我的商品';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
if($userrow['power']==0) showmsg('你没有权限使用此功能！请先开通分站',3);
// 平台开关：后台"分站商品管理"页可关闭此功能
if(isset($conf['fenzhan_shop_add']) && $conf['fenzhan_shop_add']==0) showmsg('平台已关闭分站自主添加商品功能，如有疑问请联系平台客服',3);

$my = isset($_GET['my']) ? $_GET['my'] : '';
$zid = intval($userrow['zid']);
// 每个分站可添加的商品数上限（后台可调，默认50）
$shop_max = isset($conf['fenzhan_shop_max']) && $conf['fenzhan_shop_max']>0 ? intval($conf['fenzhan_shop_max']) : 50;

// 我的分类列表（下拉与列表共用）
function myclasses($zid){
	global $DB;
	$rows = $DB->getAll("SELECT cid,name FROM pre_class WHERE zid=:zid AND active=1 ORDER BY sort ASC,cid ASC", array(':zid'=>$zid));
	return $rows ? $rows : array();
}

if($my=='add'){
	// ===== 添加商品表单 =====
	$count = $DB->getColumn("SELECT COUNT(*) FROM pre_tools WHERE zid=:zid", array(':zid'=>$zid));
	if($count >= $shop_max) showmsg('你的店铺商品数量已达上限（'.$shop_max.'个），请先删除不需要的商品',3);
	$classes = myclasses($zid);
?>
<div class="wrapper">
  <div class="col-sm-12 col-md-8 col-lg-7 center-block" style="float:none;">
    <div class="panel panel-default">
      <div class="panel-heading font-bold"><h3 class="panel-title">添加商品</h3></div>
      <div class="panel-body">
        <form action="./myshop.php?my=add_submit" method="POST" onsubmit="return checkAdd()">
          <div class="form-group">
            <label>*商品名称：</label>
            <input type="text" class="form-control" name="name" maxlength="60" required placeholder="例如：XX视频会员月卡">
          </div>
          <div class="form-group">
            <label>商品分类：</label>
            <select class="form-control" name="cid" id="cidSel">
              <option value="0">未分类</option>
              <?php foreach($classes as $c){ echo '<option value="'.$c['cid'].'">'.htmlspecialchars($c['name']).'</option>'; } ?>
            </select>
            <span class="help-block">没有合适的分类？可到 <a href="./myshop.php?my=class">分类管理</a> 先创建一个。</span>
          </div>
          <div class="form-group">
            <label>*销售价格（元）：</label>
            <input type="text" class="form-control" name="price" maxlength="10" required placeholder="0.01 - 99999，支持两位小数">
          </div>
          <div class="form-group">
            <label>*发货方式：</label>
            <select class="form-control" name="is_curl" id="isCurl" onchange="showType()">
              <option value="4">自动发卡密（买家付款后系统自动从卡密库存发货）</option>
              <option value="5">自动发内容（买家付款后直接显示你设置的文字/链接内容）</option>
              <option value="0">人工处理（买家付款后由你手动处理发货）</option>
            </select>
          </div>
          <div class="form-group" id="kmBox">
            <label>*卡密内容（每行一个，支持批量粘贴）：</label>
            <textarea class="form-control" name="kms" id="kms" rows="6" placeholder="每行一个卡密，例如：&#10;abc-123-xyz&#10;def-456-uvw&#10;&#10;相同的行会自动去重，单次最多500个"></textarea>
            <span class="help-block">当前库存 = 提交的卡密行数，之后可随时在商品列表里补充卡密。</span>
          </div>
          <div class="form-group" id="ctBox" style="display:none">
            <label>*购买后显示的内容：</label>
            <textarea class="form-control" name="showcontent" id="showcontent" rows="4" placeholder="买家付款后会直接在订单页看到这里的内容，例如：&#10;网盘链接：https://pan.xxx.com/s/xxxx 提取码：a1b2"></textarea>
            <span class="help-block">支持换行，请勿填入第三方联系方式引导线下交易。</span>
          </div>
          <div class="form-group">
            <label>商品图片：</label>
            <input type="text" class="form-control" name="shopimg" id="shopimg" placeholder="可留空；也可点击右侧按钮上传">
            <div style="margin-top:8px">
              <input type="file" id="imgFile" style="display:none" accept="image/*">
              <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('imgFile').click()"><i class="fa fa-upload"></i> 上传图片</button>
              <span id="upTip" class="text-muted" style="margin-left:8px">支持 JPG/PNG/GIF/WEBP，2MB以内</span>
            </div>
          </div>
          <div class="form-group">
            <label>商品介绍：</label>
            <textarea class="form-control" name="desc" rows="3" maxlength="500" placeholder="展示在店铺商品弹窗中，可留空"></textarea>
          </div>
          <input type="submit" class="btn btn-primary btn-block" value="确定添加">
          <br/><a href="./myshop.php">>>返回我的商品</a>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
function showType(){
  var v = document.getElementById('isCurl').value;
  document.getElementById('kmBox').style.display = (v=='4' ? '' : 'none');
  document.getElementById('ctBox').style.display = (v=='5' ? '' : 'none');
}
function checkAdd(){
  var v = document.getElementById('isCurl').value;
  if(v=='4'){
    var kms = document.getElementById('kms').value.split('\n').map(function(s){return s.trim();}).filter(function(s){return s.length>0;});
    if(kms.length==0){ alert('自动发卡密商品必须至少填写一个卡密'); return false; }
    if(kms.length>500){ alert('单次最多提交500个卡密，多余的请稍后在商品列表中补充'); return false; }
  }
  if(v=='5' && !document.getElementById('showcontent').value.trim()){ alert('请填写购买后显示的内容'); return false; }
  return true;
}
document.getElementById('imgFile').addEventListener('change', function(){
  if(!this.files[0]) return;
  var fd = new FormData();
  fd.append('file', this.files[0]);
  fd.append('do', 'upload');
  document.getElementById('upTip').textContent = '上传中...';
  fetch('./ajax_user.php?act=uploadimg', {method:'POST', body:fd, credentials:'same-origin'})
    .then(function(r){ return r.json(); })
    .then(function(d){
      if(d.code==0){
        document.getElementById('shopimg').value = '/' + d.url.replace(/^\//,'');
        document.getElementById('upTip').textContent = '上传成功';
      } else { document.getElementById('upTip').textContent = d.msg || '上传失败'; }
    })
    .catch(function(){ document.getElementById('upTip').textContent = '网络错误，请重试'; });
});
</script>
<?php
}
elseif($my=='add_submit'){
	// ===== 处理添加 =====
	if(!checkRefererHost()) showmsg('来源校验失败，请返回重试',3);
	$name = trim(daddslashes($_POST['name']));
	$cid = intval($_POST['cid']);
	$price = round(floatval($_POST['price']),2);
	$is_curl = intval($_POST['is_curl']);
	$shopimg = trim(daddslashes($_POST['shopimg']));
	$desc = trim(daddslashes($_POST['desc']));
	$showcontent = trim(daddslashes($_POST['showcontent']));

	if($name=='' || mb_strlen($name)>60) showmsg('商品名称不能为空且不能超过60字',3);
	if($price<=0 || $price>99999) showmsg('销售价格必须是0.01~99999之间的金额',3);
	if(!in_array($is_curl, array(0,4,5))) showmsg('发货方式不正确',3);
	if($cid>0 && !$DB->getRow("SELECT cid FROM pre_class WHERE cid=:cid AND zid=:zid LIMIT 1", array(':cid'=>$cid,':zid'=>$zid))) showmsg('商品分类不存在',3);
	if($shopimg && !preg_match('/^\/assets\/img\/workorder\/[a-z0-9]{32}\.(jpg|jpeg|png|gif|webp|bmp)$/i', $shopimg)) $shopimg='';
	if($is_curl==5 && $showcontent=='') showmsg('请填写购买后显示的内容',3);

	// 卡密处理：去空行、去重、限制长度
	$kms = array();
	if($is_curl==4){
		$lines = explode("\n", str_replace("\r\n", "\n", $_POST['kms']));
		foreach($lines as $line){
			$line = trim($line);
			if($line==='' ) continue;
			if(mb_strlen($line)>255) showmsg('存在超过255字的卡密行，请检查',3);
			$kms[] = $line;
		}
		$kms = array_values(array_unique($kms));
		if(count($kms)==0) showmsg('自动发卡密商品必须至少填写一个卡密',3);
		if(count($kms)>500) showmsg('单次最多提交500个卡密',3);
	}
	$count = $DB->getColumn("SELECT COUNT(*) FROM pre_tools WHERE zid=:zid", array(':zid'=>$zid));
	if($count >= $shop_max) showmsg('你的店铺商品数量已达上限（'.$shop_max.'个）',3);

	$sql = "INSERT INTO `pre_tools` (`zid`,`cid`,`sort`,`name`,`price`,`prid`,`cost`,`cost2`,`input`,`inputs`,`desc`,`shopimg`,`is_curl`,`showcontent`,`multi`,`min`,`max`,`repeat`,`close`,`active`,`ts`,`addtime`) VALUES (:zid,:cid,:sort,:name,:price,:prid,:cost,:cost2,:input,:inputs,:desc,:shopimg,:is_curl,:showcontent,:multi,:min,:max,:repeat,:close,:active,:ts,:addtime)";
	$params = array(
		':zid'=>$zid, ':cid'=>$cid, ':sort'=>50, ':name'=>$name, ':price'=>$price, ':prid'=>0,
		':cost'=>$price, ':cost2'=>$price, ':input'=>'联系方式', ':inputs'=>'', ':desc'=>$desc, ':shopimg'=>$shopimg,
		':is_curl'=>$is_curl, ':showcontent'=>$is_curl==5?$showcontent:'', ':multi'=>1, ':min'=>1, ':max'=>0,
		':repeat'=>1, ':close'=>0, ':active'=>1, ':ts'=>0, ':addtime'=>date("Y-m-d H:i:s")
	);
	if($DB->exec($sql, $params)!==false){
		$tid = $DB->lastInsertId();
		if($is_curl==4 && $kms){
			$stmt = "INSERT INTO `pre_faka` (`tid`,`km`,`addtime`,`orderid`) VALUES (:tid,:km,:addtime,0)";
			foreach($kms as $km){
				$DB->exec($stmt, array(':tid'=>$tid, ':km'=>daddslashes($km), ':addtime'=>date("Y-m-d H:i:s")));
			}
		}
		$DB->exec("INSERT INTO `pre_toollogs` (`content`,`date`,`addtime`,`active`) VALUES (:content, CURDATE(), NOW(), 1)", array(':content'=>'分站'.$zid.'上架：'.$name));
		showmsg('添加商品成功！'.($is_curl==4?'（库存'.count($kms).'个卡密）':'').'<br/><br/><a href="./myshop.php">>>返回我的商品</a>&nbsp;&nbsp;<a href="/store.php?zid='.$zid.'">>>查看我的店铺</a>',1);
	}else{
		showmsg('添加商品失败！数据库操作异常，请稍后重试',4);
	}
}
elseif($my=='edit'){
	// ===== 编辑表单 =====
	$tid = intval($_GET['tid']);
	$tool = $DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid AND zid=:zid LIMIT 1", array(':tid'=>$tid,':zid'=>$zid));
	if(!$tool) showmsg('当前商品不存在或无权操作！',3);
	$classes = myclasses($zid);
?>
<div class="wrapper">
  <div class="col-sm-12 col-md-8 col-lg-7 center-block" style="float:none;">
    <div class="panel panel-default">
      <div class="panel-heading font-bold"><h3 class="panel-title">编辑商品：<?php echo htmlspecialchars($tool['name']);?></h3></div>
      <div class="panel-body">
        <form action="./myshop.php?my=edit_submit&tid=<?php echo $tid;?>" method="POST">
          <div class="form-group">
            <label>*商品名称：</label>
            <input type="text" class="form-control" name="name" maxlength="60" required value="<?php echo htmlspecialchars($tool['name']);?>">
          </div>
          <div class="form-group">
            <label>商品分类：</label>
            <select class="form-control" name="cid">
              <option value="0">未分类</option>
              <?php foreach($classes as $c){ echo '<option value="'.$c['cid'].'"'.($c['cid']==$tool['cid']?' selected':'').'>'.htmlspecialchars($c['name']).'</option>'; } ?>
            </select>
          </div>
          <div class="form-group">
            <label>*销售价格（元）：</label>
            <input type="text" class="form-control" name="price" maxlength="10" required value="<?php echo $tool['price'];?>">
          </div>
          <div class="form-group">
            <label>商品图片：</label>
            <input type="text" class="form-control" name="shopimg" id="shopimg" value="<?php echo htmlspecialchars($tool['shopimg']);?>" placeholder="可留空">
            <div style="margin-top:8px">
              <input type="file" id="imgFile" style="display:none" accept="image/*">
              <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('imgFile').click()"><i class="fa fa-upload"></i> 上传新图片</button>
            </div>
          </div>
          <div class="form-group">
            <label>商品介绍：</label>
            <textarea class="form-control" name="desc" rows="3" maxlength="500"><?php echo htmlspecialchars($tool['desc']);?></textarea>
          </div>
          <input type="submit" class="btn btn-primary btn-block" value="保存修改">
          <br/><a href="./myshop.php">>>返回我的商品</a>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('imgFile').addEventListener('change', function(){
  if(!this.files[0]) return;
  var fd = new FormData();
  fd.append('file', this.files[0]);
  fd.append('do', 'upload');
  fetch('./ajax_user.php?act=uploadimg', {method:'POST', body:fd, credentials:'same-origin'})
    .then(function(r){ return r.json(); })
    .then(function(d){
      if(d.code==0){ document.getElementById('shopimg').value = '/' + d.url.replace(/^\//,''); alert('上传成功'); }
      else alert(d.msg || '上传失败');
    })
    .catch(function(){ alert('网络错误，请重试'); });
});
</script>
<?php
}
elseif($my=='edit_submit'){
	// ===== 处理编辑 =====
	if(!checkRefererHost()) showmsg('来源校验失败，请返回重试',3);
	$tid = intval($_GET['tid']);
	$tool = $DB->getRow("SELECT tid FROM pre_tools WHERE tid=:tid AND zid=:zid LIMIT 1", array(':tid'=>$tid,':zid'=>$zid));
	if(!$tool) showmsg('当前商品不存在或无权操作！',3);
	$name = trim(daddslashes($_POST['name']));
	$cid = intval($_POST['cid']);
	$price = round(floatval($_POST['price']),2);
	$shopimg = trim(daddslashes($_POST['shopimg']));
	$desc = trim(daddslashes($_POST['desc']));
	if($name=='' || mb_strlen($name)>60) showmsg('商品名称不能为空且不能超过60字',3);
	if($price<=0 || $price>99999) showmsg('销售价格必须是0.01~99999之间的金额',3);
	if($cid>0 && !$DB->getRow("SELECT cid FROM pre_class WHERE cid=:cid AND zid=:zid LIMIT 1", array(':cid'=>$cid,':zid'=>$zid))) showmsg('商品分类不存在',3);
	if($shopimg && !preg_match('/^\/assets\/img\/workorder\/[a-z0-9]{32}\.(jpg|jpeg|png|gif|webp|bmp)$/i', $shopimg)) $shopimg='';
	$sql = "UPDATE `pre_tools` SET `name`=:name,`cid`=:cid,`price`=:price,`cost`=:price,`cost2`=:price,`shopimg`=:shopimg,`desc`=:desc WHERE `tid`=:tid AND `zid`=:zid";
	if($DB->exec($sql, array(':name'=>$name,':cid'=>$cid,':price'=>$price,':shopimg'=>$shopimg,':desc'=>$desc,':tid'=>$tid,':zid'=>$zid))!==false)
		showmsg('修改商品成功！<br/><br/><a href="./myshop.php">>>返回我的商品</a>',1);
	else
		showmsg('修改商品失败！数据库操作异常，请稍后重试',4);
}
elseif($my=='km'){
	// ===== 补充卡密 =====
	$tid = intval($_GET['tid']);
	$tool = $DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid AND zid=:zid LIMIT 1", array(':tid'=>$tid,':zid'=>$zid));
	if(!$tool) showmsg('当前商品不存在或无权操作！',3);
	if($tool['is_curl']!=4) showmsg('仅自动发卡密类型的商品可以补充卡密',3);
	$stock = $DB->getColumn("SELECT COUNT(*) FROM pre_faka WHERE tid=:tid AND orderid=0", array(':tid'=>$tid));
?>
<div class="wrapper">
  <div class="col-sm-12 col-md-8 col-lg-7 center-block" style="float:none;">
    <div class="panel panel-default">
      <div class="panel-heading font-bold"><h3 class="panel-title">补充卡密：<?php echo htmlspecialchars($tool['name']);?>（当前库存 <?php echo $stock;?>）</h3></div>
      <div class="panel-body">
        <form action="./myshop.php?my=km_submit&tid=<?php echo $tid;?>" method="POST">
          <div class="form-group">
            <label>*新增卡密（每行一个）：</label>
            <textarea class="form-control" name="kms" rows="8" required placeholder="每行一个卡密，相同的行会自动去重，单次最多500个"></textarea>
          </div>
          <input type="submit" class="btn btn-primary btn-block" value="确定补充">
          <br/><a href="./myshop.php">>>返回我的商品</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
}
elseif($my=='km_submit'){
	// ===== 处理补卡 =====
	if(!checkRefererHost()) showmsg('来源校验失败，请返回重试',3);
	$tid = intval($_GET['tid']);
	$tool = $DB->getRow("SELECT tid FROM pre_tools WHERE tid=:tid AND zid=:zid LIMIT 1", array(':tid'=>$tid,':zid'=>$zid));
	if(!$tool) showmsg('当前商品不存在或无权操作！',3);
	$tool_full = $DB->getRow("SELECT is_curl,name FROM pre_tools WHERE tid=:tid LIMIT 1");
	if($tool_full['is_curl']!=4) showmsg('仅自动发卡密类型的商品可以补充卡密',3);
	$lines = explode("\n", str_replace("\r\n", "\n", $_POST['kms']));
	$kms = array();
	foreach($lines as $line){
		$line = trim($line);
		if($line==='') continue;
		if(mb_strlen($line)>255) showmsg('存在超过255字的卡密行，请检查',3);
		$kms[] = $line;
	}
	$kms = array_values(array_unique($kms));
	if(count($kms)==0) showmsg('请至少填写一个卡密',3);
	if(count($kms)>500) showmsg('单次最多提交500个卡密',3);
	// 与已有未售卡密去重
	$exist = array();
	$rs = $DB->query("SELECT km FROM pre_faka WHERE tid=:tid", array(':tid'=>$tid));
	while($r = $rs->fetch()) $exist[$r['km']] = 1;
	$added = 0;
	$stmt = "INSERT INTO `pre_faka` (`tid`,`km`,`addtime`,`orderid`) VALUES (:tid,:km,:addtime,0)";
	foreach($kms as $km){
		if(isset($exist[$km])) continue;
		$DB->exec($stmt, array(':tid'=>$tid, ':km'=>daddslashes($km), ':addtime'=>date("Y-m-d H:i:s")));
		$added++;
	}
	showmsg('补充完成！新增'.$added.'个卡密'.($added<count($kms)?'（跳过'.(count($kms)-$added).'个重复卡密）':'').'<br/><br/><a href="./myshop.php">>>返回我的商品</a>',1);
}
elseif($my=='del'){
	// ===== 删除商品 =====
	$tid = intval($_GET['tid']);
	$tool = $DB->getRow("SELECT * FROM pre_tools WHERE tid=:tid AND zid=:zid LIMIT 1", array(':tid'=>$tid,':zid'=>$zid));
	if(!$tool) showmsg('当前商品不存在或无权操作！',3);
	// 未售卡密一并清理，已售卡密保留以保证订单可查
	$unsold = $DB->getColumn("SELECT COUNT(*) FROM pre_faka WHERE tid=:tid AND orderid=0", array(':tid'=>$tid));
	$sold = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE tid=:tid", array(':tid'=>$tid));
	if($_GET['confirm']==1){
		if(!checkRefererHost()) showmsg('来源校验失败，请返回重试',3);
		if($sold>0){
			// 有历史订单：下架保留，避免订单详情失效
			$DB->exec("UPDATE pre_tools SET close=1,active=0 WHERE tid=:tid AND zid=:zid", array(':tid'=>$tid,':zid'=>$zid));
			showmsg('该商品存在'.$sold.'笔历史订单，为保障买家查询订单，已为你下架并停售（不再展示）。<br/><br/><a href="./myshop.php">>>返回我的商品</a>',1);
		}else{
			$DB->exec("DELETE FROM pre_faka WHERE tid=:tid AND orderid=0", array(':tid'=>$tid));
			$DB->exec("DELETE FROM pre_tools WHERE tid=:tid AND zid=:zid", array(':tid'=>$tid,':zid'=>$zid));
			showmsg('删除商品成功！<br/><br/><a href="./myshop.php">>>返回我的商品</a>',1);
		}
	}else{
?>
<div class="wrapper">
  <div class="col-sm-12 col-md-6 col-lg-5 center-block" style="float:none;">
    <div class="panel panel-default">
      <div class="panel-heading font-bold"><h3 class="panel-title">删除确认</h3></div>
      <div class="panel-body">
        <p>确定要删除商品 <b><?php echo htmlspecialchars($tool['name']);?></b> 吗？</p>
        <?php if($unsold>0) echo '<p class="text-warning">将同时删除 '.$unsold.' 个未售出卡密。</p>';?>
        <?php if($sold>0) echo '<p class="text-danger">该商品已有 '.$sold.' 笔历史订单，删除后买家将无法查询订单，系统将改为下架停售处理。</p>';?>
        <a href="./myshop.php?my=del&tid=<?php echo $tid;?>&confirm=1" class="btn btn-danger btn-block">确定删除</a>
        <br/><a href="./myshop.php">>>返回我的商品</a>
      </div>
    </div>
  </div>
</div>
<?php
	}
}
elseif($my=='toggle'){
	// ===== 上下架切换 =====
	$tid = intval($_GET['tid']);
	$tool = $DB->getRow("SELECT close,name FROM pre_tools WHERE tid=:tid AND zid=:zid LIMIT 1", array(':tid'=>$tid,':zid'=>$zid));
	if(!$tool) showmsg('当前商品不存在或无权操作！',3);
	$to = $tool['close']==1 ? 0 : 1;
	if($DB->exec("UPDATE pre_tools SET close=:c WHERE tid=:tid AND zid=:zid", array(':c'=>$to,':tid'=>$tid,':zid'=>$zid))!==false)
		showmsg(($to==1?'下架':'上架').'成功！商品“'.htmlspecialchars($tool['name']).'”'.($to==1?'已从店铺隐藏':'已在店铺展示').'<br/><br/><a href="./myshop.php">>>返回我的商品</a>',1);
	else
		showmsg('操作失败！数据库操作异常',4);
}
elseif($my=='class'){
	// ===== 我的分类管理 =====
	$classes = myclasses($zid);
?>
<div class="wrapper">
  <div class="col-sm-12 col-md-8 col-lg-7 center-block" style="float:none;">
    <div class="panel panel-default">
      <div class="panel-heading font-bold"><h3 class="panel-title">店铺分类管理</h3></div>
      <div class="panel-body">
        <form action="./myshop.php?my=class_add" method="POST" class="form-inline" style="margin-bottom:15px">
          <div class="form-group"><input type="text" class="form-control" name="name" maxlength="20" required placeholder="新分类名称"></div>
          <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> 新增分类</button>
        </form>
        <table class="table table-striped">
          <thead><tr><th>分类名称</th><th>商品数</th><th width="90">操作</th></tr></thead>
          <tbody>
          <?php
          if(empty($classes)) echo '<tr><td colspan="3" class="text-center text-muted">暂无分类，添加商品时将归入“未分类”</td></tr>';
          foreach($classes as $c){
            $n = $DB->getColumn("SELECT COUNT(*) FROM pre_tools WHERE zid=:zid AND cid=:cid", array(':zid'=>$zid,':cid'=>$c['cid']));
            echo '<tr><td>'.htmlspecialchars($c['name']).'</td><td>'.$n.'</td><td><a href="./myshop.php?my=class_del&cid='.$c['cid'].'" class="btn btn-danger btn-xs" onclick="return confirm(\'删除分类后其下商品将归入未分类，确定删除？\')">删除</a></td></tr>';
          }
          ?>
          </tbody>
        </table>
        <a href="./myshop.php">>>返回我的商品</a>
      </div>
    </div>
  </div>
</div>
<?php
}
elseif($my=='class_add'){
	if(!checkRefererHost()) showmsg('来源校验失败，请返回重试',3);
	$name = trim(daddslashes($_POST['name']));
	if($name=='' || mb_strlen($name)>20) showmsg('分类名称不能为空且不能超过20字',3);
	$count = $DB->getColumn("SELECT COUNT(*) FROM pre_class WHERE zid=:zid");
	if($count>=20) showmsg('分类数量已达上限（20个）',3);
	if($DB->getRow("SELECT cid FROM pre_class WHERE zid=:zid AND name=:name LIMIT 1", array(':zid'=>$zid,':name'=>$name))) showmsg('该分类名称已存在',3);
	$sort = $DB->getColumn("SELECT sort FROM pre_class WHERE zid=:zid ORDER BY sort DESC LIMIT 1");
	if($DB->exec("INSERT INTO pre_class (zid,pid,sort,name,active) VALUES (:zid,0,:sort,:name,1)", array(':zid'=>$zid,':sort'=>$sort+1,':name'=>$name))!==false)
		showmsg('新增分类成功！<br/><br/><a href="./myshop.php?my=class">>>返回分类管理</a>',1);
	else
		showmsg('新增分类失败！数据库操作异常',4);
}
elseif($my=='class_del'){
	$cid = intval($_GET['cid']);
	if(!$DB->getRow("SELECT cid FROM pre_class WHERE cid=:cid AND zid=:zid LIMIT 1", array(':cid'=>$cid,':zid'=>$zid))) showmsg('分类不存在或无权操作！',3);
	$DB->exec("UPDATE pre_tools SET cid=0 WHERE zid=:zid AND cid=:cid", array(':zid'=>$zid,':cid'=>$cid));
	$DB->exec("DELETE FROM pre_class WHERE cid=:cid AND zid=:zid", array(':cid'=>$cid,':zid'=>$zid));
	showmsg('删除分类成功！其下商品已归入未分类<br/><br/><a href="./myshop.php?my=class">>>返回分类管理</a>',1);
}
else{
	// ===== 商品列表（默认） =====
	$classes = myclasses($zid);
	$kw = isset($_GET['kw']) ? trim(daddslashes($_GET['kw'])) : '';
	$where = "zid=:zid";
	$params = array(':zid'=>$zid);
	if($kw!==''){ $where .= " AND name LIKE :kw"; $params[':kw'] = '%'.$kw.'%'; }
	$rs = $DB->query("SELECT * FROM pre_tools WHERE {$where} ORDER BY tid DESC", $params);
	$list = array();
	while($r = $rs->fetch()) $list[] = $r;
	$mycount = count($list);
?>
<div class="wrapper">
  <div class="col-sm-12 col-md-10 col-lg-9 center-block" style="float:none;">
    <div class="panel panel-default">
      <div class="panel-heading font-bold">
        <h3 class="panel-title">我的商品（<?php echo $mycount;?>/<?php echo $shop_max;?>）</h3>
      </div>
      <div class="panel-body" style="padding-bottom:0">
        <div style="margin-bottom:12px">
          <a href="./myshop.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i> 添加商品</a>&nbsp;
          <a href="./myshop.php?my=class" class="btn btn-success"><i class="fa fa-th-large"></i> 分类管理</a>&nbsp;
          <a href="/store.php?zid=<?php echo $zid;?>" target="_blank" class="btn btn-info"><i class="fa fa-external-link"></i> 查看我的店铺</a>
        </div>
        <form action="./myshop.php" method="GET" class="form-inline" style="margin-bottom:12px">
          <div class="input-group" style="max-width:280px">
            <input type="text" class="form-control" name="kw" value="<?php echo htmlspecialchars($kw);?>" placeholder="搜索我的商品">
            <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead><tr><th>ID</th><th>商品名称</th><th>分类</th><th>价格</th><th>库存</th><th>发货方式</th><th>状态</th><th>操作</th></tr></thead>
            <tbody>
            <?php
            if(empty($list)){
              echo '<tr><td colspan="8" class="text-center text-muted" style="padding:30px">还没有自己的商品，点击上方「添加商品」上架你的第一个商品吧</td></tr>';
            }
            $cname = array();
            foreach($classes as $c) $cname[$c['cid']] = $c['name'];
            $curlmap = array(4=>'自动发卡密',5=>'自动发内容',0=>'人工处理');
            foreach($list as $t){
              if($t['is_curl']==4){
                $stock = $DB->getColumn("SELECT COUNT(*) FROM pre_faka WHERE tid=:tid AND orderid=0", array(':tid'=>$t['tid']));
              }elseif($t['is_curl']==5){
                $stock = '∞';
              }else{
                $stock = '—';
              }
              $cls = $t['cid']>0 && isset($cname[$t['cid']]) ? $cname[$t['cid']] : '未分类';
              echo '<tr>'
                .'<td>'.$t['tid'].'</td>'
                .'<td><b>'.htmlspecialchars($t['name']).'</b></td>'
                .'<td>'.htmlspecialchars($cls).'</td>'
                .'<td><b style="color:#dd4b39">¥'.$t['price'].'</b></td>'
                .'<td>'.($stock===0?'<span class="label label-danger">0</span>':$stock).'</td>'
                .'<td>'.(isset($curlmap[$t['is_curl']])?$curlmap[$t['is_curl']]:'-').'</td>'
                .'<td>'.($t['close']==1?'<span class="label label-default">已下架</span>':'<span class="label label-success">上架中</span>').'</td>'
                .'<td>'
                .'<a href="./myshop.php?my=edit&tid='.$t['tid'].'" class="btn btn-info btn-xs">编辑</a>&nbsp;'
                .($t['is_curl']==4?'<a href="./myshop.php?my=km&tid='.$t['tid'].'" class="btn btn-success btn-xs">补卡密</a>&nbsp;':'')
                .'<a href="./myshop.php?my=toggle&tid='.$t['tid'].'" class="btn btn-warning btn-xs">'.($t['close']==1?'上架':'下架').'</a>&nbsp;'
                .'<a href="./myshop.php?my=del&tid='.$t['tid'].'" class="btn btn-danger btn-xs">删除</a>'
                .'</td></tr>';
            }
            ?>
            </tbody>
          </table>
        </div>
        <p class="text-muted" style="font-size:12px;margin-bottom:15px">提示：上架中的商品会实时展示在你的店铺页（/store.php?zid=<?php echo $zid;?>）；自动发卡密商品库存为0时买家将无法购买，请及时补充卡密。</p>
      </div>
    </div>
  </div>
</div>
<?php
}
include './foot.php';
?>
