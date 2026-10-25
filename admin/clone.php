<?php


include "../includes/common.php";
$title = "克隆站点";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>    <div class="col-sm-12 col-md-10 col-lg-8 center-block" style="float: none;">
<?php 
adminpermission("super", 1);
if (isset($_POST["submit"])) {
	$url = trim($_POST["url"]);
	$key = trim($_POST["key"]);
	$url_arr = parse_url($url);
	if ($url_arr["host"] == $_SERVER["HTTP_HOST"]) {
		showmsg("无法自己克隆自己", 3);
	}
	$data = get_curl($url . "api.php?act=clone&key=" . $key);
	$arr = json_decode($data, true);
	if (array_key_exists("code", $arr) && $arr["code"] == 1) {
		$success = 0;
		if (count($arr["class"]) < 2 || count($arr["tools"]) < 5) {
			showmsg("对方网站数据量过少。", 3);
		}
		if (!isset($arr["price"])) {
			showmsg("对方网站程序版本较低", 3);
		}
		if ($_POST["type"] == 1) {
			if (isset($_POST["tid"])) {
				$classa = array();
				$pricea = array();
				$shequa = array();
				foreach ($arr["tools"] as $row) {
					if (!in_array($row["tid"], $_POST["tid"])) {
						continue;
					}
					if (!array_key_exists($row["cid"], $classa)) {
						$classa[$row["cid"]] = 0;
					}
					if (!array_key_exists($row["prid"], $pricea)) {
						$pricea[$row["prid"]] = 0;
					}
					if (!array_key_exists($row["shequ"], $shequa)) {
						$shequa[$row["shequ"]] = 0;
					}
				}
				foreach ($arr["class"] as $row) {
					if (!array_key_exists($row["cid"], $classa)) {
						continue;
					}
					$srow = $DB->getRow("select cid from pre_class where name='" . $row["name"] . "' limit 1");
					if ($srow) {
						$classa[$row["cid"]] = $srow["cid"];
					} else {
						$id = $DB->exec("INSERT INTO `pre_class` (`name`,`sort`,`active`,`shopimg`) VALUES ('" . $row["name"] . "','" . $row["sort"] . "','" . $row["active"] . "','" . $row["shopimg"] . "')");
						$classa[$row["cid"]] = $DB->lastInsertId();
						$success++;
					}
				}
				foreach ($arr["shequ"] as $row) {
					if (!array_key_exists($row["id"], $shequa)) {
						continue;
					}
					$srow = $DB->getRow("select id from pre_shequ where url='" . $row["url"] . "' limit 1");
					if ($srow) {
						$shequa[$row["id"]] = $srow["id"];
					} else {
						$id = $DB->exec("INSERT INTO `pre_shequ` (`url`,`username`,`password`,`type`) VALUES ('" . $row["url"] . "',NULL,NULL,'" . $row["type"] . "')");
						$shequa[$row["id"]] = $DB->lastInsertId();
						$success++;
					}
				}
				foreach ($arr["price"] as $row) {
					if (!array_key_exists($row["prid"], $pricea)) {
						continue;
					}
					$srow = $DB->getRow("select id from pre_price where kind='" . $row["kind"] . "' and name='" . $row["name"] . "' limit 1");
					if ($srow) {
						$pricea[$row["id"]] = $srow["id"];
					} else {
						$id = $DB->exec("INSERT INTO `pre_price` (`kind`,`name`,`p_0`,`p_1`,`p_2`) VALUES ('" . $row["kind"] . "','" . $row["name"] . "','" . $row["p_0"] . "','" . $row["p_1"] . "','" . $row["p_2"] . "')");
						$pricea[$row["id"]] = $DB->lastInsertId();
						$success++;
					}
				}
				foreach ($arr["tools"] as $row) {
					if (!in_array($row["tid"], $_POST["tid"])) {
						continue;
					}
					if (!$DB->getRow("select tid from pre_tools where name='" . $row["name"] . "' and is_curl='" . $row["is_curl"] . "' limit 1")) {
						$DB->exec("INSERT INTO `pre_tools` (`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`valiserv`,`min`,`max`,`validate`,`sort`,`close`,`active`) VALUES ('" . $classa[$row["cid"]] . "','" . $row["name"] . "','" . $row["price"] . "','" . $row["cost"] . "','" . $row["cost2"] . "','" . $pricea[$row["prid"]] . "','" . $row["prices"] . "','" . $row["input"] . "','" . $row["inputs"] . "','" . addslashes($row["desc"]) . "','" . addslashes($row["alert"]) . "','" . $row["shopimg"] . "','" . $row["value"] . "','" . $row["is_curl"] . "','" . $row["curl"] . "','" . $shequa[$row["shequ"]] . "','" . $row["goods_id"] . "','" . $row["goods_type"] . "','" . $row["goods_param"] . "','" . $row["repeat"] . "','" . $row["multi"] . "','" . $row["min"] . "','" . $row["max"] . "','" . $row["validate"] . "','" . $row["valiserv"] . "','" . $row["sort"] . "','" . $row["close"] . "','" . $row["active"] . "')");
						$success++;
					}
				}
				$CACHE->clear("pricerules");
				showmsg("克隆完成，SQL成功执行" . $success . "句。", 1, "./clone.php");
			} else {
				?>	  <div class="block">
        <div class="block-title"><h3 class="panel-title">请选择要克隆的商品</h3></div>
        <div class="">
          <form action="?" method="POST" role="form">
		  <input type="hidden" name="url" value="<?php echo $url;?>">
		  <input type="hidden" name="key" value="<?php echo $key;?>">
		  <input type="hidden" name="type" value="1">
		  <?php 
				foreach ($arr["class"] as $row) {
					?>		  <a class="panel-title" data-toggle="collapse" data-parent="#class" href="#class<?php echo $row["cid"];?>"><div class="list-group-item list-group-item-success">
		  <span class="pull-right"><i class="fa fa-chevron-down"></i></span><?php echo $row["name"];?></div></a>
		  <div id="class<?php echo $row["cid"];?>" class="panel-collapse collapse">
			<table class="table table-bordered" style="table-layout: fixed;">
			<tbody>
			<tr><td><label class="csscheckbox csscheckbox-primary">全选<input type="checkbox" onclick="SelectAll(<?php echo $row["cid"];?>,this)"><span></span></label>&nbsp;ID</td><td>商品名称</td><td>状态</td></tr>
			<?php 
					foreach ($arr["tools"] as $rows) {
						if ($row["cid"] == $rows["cid"]) {
							echo "<tr><td><label class=\"csscheckbox csscheckbox-primary\"><input name=\"tid[]\" type=\"checkbox\" class=\"class" . $rows["cid"] . "\" id=\"tid\" value=\"" . $rows["tid"] . "\"><span></span>&nbsp;" . $rows["tid"] . "<label></label></label></td><td>" . $rows["name"] . "</td><td>" . ($rows["close"] == 1 ? "<span class=\"btn btn-xs btn-warning\">已下架</span>" : "<span class=\"btn btn-xs btn-success\">上架中</span>") . "&nbsp;" . ($rows["active"] == 1 ? "<span class=\"btn btn-xs btn-success\">显示</span>" : "<span class=\"btn btn-xs btn-warning\">隐藏</span>") . "</td></tr>";
						}
					}
					?>			</tbody>
			</table>
		</div>
		  <?php 
				}
				?>            <p><input type="submit" name="submit" value="确定克隆" class="btn btn-primary form-control"/></p>
          </form>
        </div>
      </div>
<?php 
			}
		} else {
			$DB->exec("TRUNCATE TABLE `pre_class`");
			$success++;
			foreach ($arr["class"] as $row) {
				$DB->exec("INSERT INTO `pre_class` (`cid`,`name`,`sort`,`active`,`shopimg`) VALUES ('" . $row["cid"] . "','" . $row["name"] . "','" . $row["sort"] . "','" . $row["active"] . "','" . $row["shopimg"] . "')");
				$success++;
			}
			$DB->exec("TRUNCATE TABLE `pre_tools`");
			$success++;
			foreach ($arr["tools"] as $row) {
				$DB->exec("INSERT INTO `pre_tools` (`tid`,`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`sort`,`close`,`active`) VALUES ('" . $row["tid"] . "','" . $row["cid"] . "','" . $row["name"] . "','" . $row["price"] . "','" . $row["cost"] . "','" . $row["cost2"] . "','" . $row["prid"] . "','" . $row["prices"] . "','" . $row["input"] . "','" . $row["inputs"] . "','" . addslashes($row["desc"]) . "','" . addslashes($row["alert"]) . "','" . $row["shopimg"] . "','" . $row["value"] . "','" . $row["is_curl"] . "','" . $row["curl"] . "','" . $row["shequ"] . "','" . $row["goods_id"] . "','" . $row["goods_type"] . "','" . $row["goods_param"] . "','" . $row["repeat"] . "','" . $row["multi"] . "','" . $row["min"] . "','" . $row["max"] . "','" . $row["validate"] . "','" . $row["valiserv"] . "','" . $row["sort"] . "','" . $row["close"] . "','" . $row["active"] . "')");
				$success++;
			}
			$DB->exec("TRUNCATE TABLE `pre_shequ`");
			$success++;
			foreach ($arr["shequ"] as $row) {
				$DB->exec("INSERT INTO `pre_shequ` (`id`,`url`,`username`,`password`,`type`) VALUES ('" . $row["id"] . "','" . $row["url"] . "',NULL,NULL,'" . $row["type"] . "')");
				$success++;
			}
			$DB->exec("TRUNCATE TABLE `pre_price`");
			$success++;
			foreach ($arr["price"] as $row) {
				$DB->exec("INSERT INTO `pre_price` (`id`,`kind`,`name`,`p_0`,`p_1`,`p_2`) VALUES ('" . $row["id"] . "','" . $row["kind"] . "','" . $row["name"] . "','" . $row["p_0"] . "','" . $row["p_1"] . "','" . $row["p_2"] . "')");
				$success++;
			}
			$CACHE->clear("pricerules");
			showmsg("克隆完成，SQL成功执行" . $success . "句。", 1);
		}
	} elseif (array_key_exists("code", $arr)) {
		showmsg("克隆失败，原因：" . $arr["msg"], 4);
	} else {
		showmsg("克隆失败，返回数据解析错误。", 4);
	}
} else {
	?>

	  <div class="block">
        <div class="block-title"><h3 class="panel-title">克隆站点</h3></div>
        <div class="">
		<div class="alert alert-info">
            使用此功能可一键克隆目标站点的分类、商品及社区对接数据（除社区账号密码外），方便站长快速丰富网站内容。
        </div>
		<div class="alert alert-danger">
            使用全量克隆将会清空本站所有商品和分类数据，请谨慎操作！
        </div>
          <form action="?" method="POST" role="form">
		    <div class="form-group">
				<div class="input-group"><div class="input-group-addon">站点URL</div>
				<input type="text" name="url" value="" class="form-control" placeholder="http://www.qq.com/" required/>
				<div class="input-group-addon" onclick="checkurl()"><small>检测连通性</small></div>
			</div></div>
			<div class="form-group">
				<div class="input-group"><div class="input-group-addon">克隆密钥</div>
				<input type="text" name="key" value="" class="form-control" placeholder="联系目标站点取得" required/>
			</div></div>
			<div class="form-group">
				<div class="input-group"><div class="input-group-addon">克隆方式</div>
				<select class="form-control" name="type"><option value="1">增量克隆（不会改变原有数据）</option><option value="0">全量克隆（会清空本站原有的商品数据）</option></select>
			</div></div>
            <p><input type="submit" name="submit" value="确定克隆" class="btn btn-primary btn-block"/></p>
          </form>
        </div>
		<div class="panel-footer">
          <span class="glyphicon glyphicon-info-sign"></span> 本站克隆密钥<a href="./set.php?mod=cloneset">点此获取</a>
        </div>
      </div>
<?php 
}
?>    </div>
  </div>
<script src="<?php echo $cdnpublic;?>layer/3.0.1/layer.js"></script>
<script>
function SelectAll(cid,chkAll) {
	var items = $('.class'+cid);
	for (i = 0; i < items.length; i++) {
		if (items[i].id.indexOf("tid") != -1 && items[i].type == "checkbox") {
			items[i].checked = chkAll.checked;
		}
	}
}
function checkurl(){
	var url = $("input[name='url']").val();
	if(url.indexOf('http')>=0 && url.substr(-1) == '/'){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=checkclone",
			data : {url:url},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.msg('连通性良好，可以克隆');
				}else if(data.code == 2){
					layer.alert('无法自己克隆自己');
				}else if(data.code == 3){
					layer.alert('对方网站的源码被用记事本改过，请先在对方网站清理BOM头部');
				}else{
					layer.alert('对方网站无法连接或存在金盾或云锁等防火墙');
				}
			} ,
			error:function(data){
				layer.close(ii);
				layer.msg('目标URL连接超时');
				return false;
			}
		});
	}else{
		layer.alert('URL必须以 http:// 开头，以 / 结尾');
	}
}
</script>