<?php
if(!defined('IN_CRONLITE'))exit();

function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') {
    $return = '';
    if (function_exists('mb_get_info')) {
        for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
            $str = mb_substr ( $string, $x, 1, $in_encoding );
            if (strlen ( $str ) > 1) {
                $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) );
            } else {
                $return .= '%' . strtoupper ( bin2hex ( $str ) );
            }
        }
    }
    return $return;
}

if($islogin2==1){
	$price_obj = new \lib\Price($userrow['zid'],$userrow);
}elseif($is_fenzhan == true){
	$price_obj = new \lib\Price($siterow['zid'],$siterow);
}else{
	$price_obj = new \lib\Price(1);
}

$tid=isset($_GET['tid'])?intval($_GET['tid']):0;
$tool=$DB->getRow("select * from pre_tools where tid='$tid' limit 1");
if(!$tool || $tool['active']!=1)sysmsg('当前商品不存在');
if($tool['close']==1)sysmsg('当前商品维护中，停止下单！');

if(isset($price_obj)){
	$price_obj->setToolInfo($tool['tid'],$tool);
	if($price_obj->getToolDel($tool['tid'])==1)sysmsg('商品已下架');
	$price=$price_obj->getToolPrice($tool['tid']);
}else $price=$tool['price'];

if($tool['is_curl']==4){
	$count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid='{$tool['tid']}' and orderid=0");
	$fakainput = getFakaInput();
	$tool['input']=$fakainput;
	$isfaka = 1;
}elseif($tool['stock']!==null){
	$count = $tool['stock'];
	$isfaka = 1;
}else{
	$isfaka = 0;
}
$isauto = ($tool['is_curl']==1||$tool['is_curl']==2||$tool['is_curl']==4);
$classname = $DB->getColumn("select name from pre_class where cid='{$tool['cid']}' limit 1");
$hometitle = $tool['name'].' - '.$conf['sitename'];

// 主题色
$style_map = [
	'1' => ['primary'=>'#165DFF','primary_hover'=>'#0E42D2','primary_light'=>'#E8F3FF'],
	'2' => ['primary'=>'#00B42A','primary_hover'=>'#009A29','primary_light'=>'#E8FFEA'],
	'3' => ['primary'=>'#FF7D00','primary_hover'=>'#D25F00','primary_light'=>'#FFF3E8'],
	'4' => ['primary'=>'#722ED1','primary_hover'=>'#531DAB','primary_light'=>'#F3E8FF'],
	'5' => ['primary'=>'#F53F3F','primary_hover'=>'#CB2634','primary_light'=>'#FFECE8'],
];
$tpl_style = isset($conf['template_style']) && isset($style_map[$conf['template_style']]) ? $conf['template_style'] : '1';
$theme = $style_map[$tpl_style];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
	<title><?php echo $hometitle?></title>
	<?php if(!empty($conf['favicon'])){?>
	<link rel="icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
	<?php }else{?>
	<link rel="icon" href="assets/img/favicon/favicon.ico" type="image/x-icon" />
	<?php }?>
	<link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	<link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
	<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
	<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
	<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
	<style>
	:root{--primary:<?php echo $theme['primary']?>;--primary-hover:<?php echo $theme['primary_hover']?>;--primary-light:<?php echo $theme['primary_light']?>}
	*{margin:0;padding:0;box-sizing:border-box}
	body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Hiragino Sans GB","Microsoft YaHei",sans-serif;background:#F5F6FA;color:#333;line-height:1.6}
	a{text-decoration:none;color:inherit}
	.nova-header{background:#1F2937;color:#fff;position:sticky;top:0;z-index:1000;box-shadow:0 2px 8px rgba(0,0,0,.1)}
	.nova-header-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;padding:0 20px;height:60px}
	.nova-logo{display:flex;align-items:center;gap:10px;font-size:20px;font-weight:700}
	.nova-logo img{height:32px;border-radius:6px}
	.nova-nav{display:flex;gap:8px}
	.nova-nav a{padding:8px 14px;border-radius:6px;font-size:14px;transition:all .2s}
	.nova-nav a:hover{background:rgba(255,255,255,.1)}
	.nova-container{max-width:1100px;margin:0 auto;padding:24px 20px}
	.nova-breadcrumb{font-size:13px;color:#999;margin-bottom:16px}
	.nova-breadcrumb a{color:var(--primary)}
	.nova-breadcrumb a:hover{text-decoration:underline}
	.nova-buy-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}
	@media(max-width:768px){.nova-buy-grid{grid-template-columns:1fr}}
	.nova-card{background:#fff;border-radius:16px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.04)}
	.nova-product-img{width:100%;aspect-ratio:1;background:linear-gradient(135deg,var(--primary-light),#fff);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:20px;overflow:hidden}
	.nova-product-img img{max-width:100%;max-height:100%;object-fit:contain}
	.nova-product-name{font-size:22px;font-weight:600;margin-bottom:12px}
	.nova-product-desc{color:#666;font-size:14px;margin-bottom:20px;line-height:1.8}
	.nova-meta-row{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
	.nova-meta-tag{padding:4px 12px;border-radius:20px;font-size:12px;font-weight:500}
	.nova-meta-tag.auto{background:var(--primary-light);color:var(--primary)}
	.nova-meta-tag.manual{background:#FFF3E8;color:#FF7D00}
	.nova-meta-tag.stock{background:#F0F0F0;color:#666}
	.nova-price{font-size:32px;font-weight:700;color:var(--primary);margin:16px 0}
	.nova-price small{font-size:14px;font-weight:400}
	.nova-form-group{margin-bottom:16px}
	.nova-form-group label{display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:#555}
	.nova-form-group label .req{color:#F53F3F}
	.nova-form-control{width:100%;padding:10px 14px;border:1px solid #E5E6EB;border-radius:8px;font-size:14px;outline:none;transition:border .2s}
	.nova-form-control:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
	.nova-form-row{display:flex;gap:12px;align-items:center}
	.nova-quantity{display:flex;align-items:center;border:1px solid #E5E6EB;border-radius:8px;overflow:hidden}
	.nova-quantity button{width:36px;height:36px;border:none;background:#F5F6FA;cursor:pointer;font-size:16px;color:#666}
	.nova-quantity button:hover{background:#EAEBED}
	.nova-quantity input{width:60px;height:36px;border:none;text-align:center;font-size:14px;outline:none}
	.nova-submit-btn{display:block;width:100%;padding:14px;background:var(--primary);color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;transition:all .2s;margin-top:8px}
	.nova-submit-btn:hover{background:var(--primary-hover)}
	.nova-submit-btn:disabled{background:#ccc;cursor:not-allowed}
	.nova-back-btn{display:block;width:100%;padding:12px;background:#fff;color:#666;border:1px solid #E5E6EB;border-radius:10px;font-size:14px;cursor:pointer;text-align:center;margin-top:10px;transition:all .2s}
	.nova-back-btn:hover{background:#F5F6FA}
	.nova-alert{background:#FFF3E8;border:1px solid #FFD591;border-radius:8px;padding:12px 16px;font-size:13px;color:#AD6800;margin-bottom:16px}
	.nova-section-title{font-size:16px;font-weight:600;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #eee}
	</style>
</head>
<body>

<header class="nova-header">
	<div class="nova-header-inner">
		<a href="./" class="nova-logo">
			<img src="<?php echo $logo?>" alt="logo" onerror="this.style.display='none'">
			<span><?php echo $conf['sitename']?></span>
		</a>
		<nav class="nova-nav">
			<a href="./">首页</a>
			<a href="./?mod=query">订单查询</a>
		</nav>
	</div>
</header>

<div class="nova-container">
	<div class="nova-breadcrumb">
		<a href="./">首页</a> / <a href="./?cid=<?php echo $tool['cid']?>"><?php echo $classname?></a> / <span><?php echo htmlspecialchars($tool['name'])?></span>
	</div>

	<div class="nova-buy-grid">
		<!-- 左侧：商品信息 -->
		<div class="nova-card">
			<div class="nova-product-img">
				<img src="<?php echo !empty($tool['shopimg'])?$tool['shopimg']:'assets/img/Product/default.png'?>" alt="<?php echo htmlspecialchars($tool['name'])?>" onerror="this.src='assets/img/Product/default.png'">
			</div>
			<h1 class="nova-product-name"><?php echo htmlspecialchars($tool['name'])?></h1>
			<div class="nova-meta-row">
				<span class="nova-meta-tag <?php echo $isauto?'auto':'manual'?>">
					<i class="fa <?php echo $isauto?'fa-bolt':'fa-clock-o'?>"></i> <?php echo $isauto?'自动发货':'手动发货'?>
				</span>
				<?php if($isfaka){?><span class="nova-meta-tag stock">库存：<?php echo intval($count)?></span><?php }?>
				<span class="nova-meta-tag stock">编号：NO.<?php echo $tid?></span>
			</div>
			<div class="nova-price"><small>¥</small><?php echo $price?></div>
			<?php if(!empty($tool['alert'])){?>
			<div class="nova-alert">
				<i class="fa fa-info-circle"></i> <?php echo $tool['alert']?>
			</div>
			<?php }?>
			<?php if(!empty($tool['desc'])){?>
			<div style="margin-top:20px">
				<div class="nova-section-title">商品介绍</div>
				<div class="nova-product-desc"><?php echo $tool['desc']?></div>
			</div>
			<?php }?>
		</div>

		<!-- 右侧：下单表单 -->
		<div class="nova-card">
			<div class="nova-section-title">购买信息</div>
			<form id="buyForm">
				<input type="hidden" id="tid" value="<?php echo $tid?>"
					cid="<?php echo $tool['cid']?>"
					price="<?php echo $price?>"
					alert="<?php echo escape($tool['alert'])?>"
					inputname="<?php echo $tool['input']?>"
					inputsname="<?php echo $tool['inputs']?>"
					multi="<?php echo $tool['multi']?>"
					isfaka="<?php echo $isfaka?>"
					count="<?php echo $tool['value']?>"
					close="<?php echo $tool['close']?>"
					prices="<?php echo $tool['prices']?>"
					max="<?php echo $tool['max']?>"
					min="<?php echo $tool['min']?>"
					shopimg="<?php echo $tool['shopimg']?>"
					desc="<?php echo escape($tool['desc'])?>"
				/>
				<div id="inputsname"></div>

				<?php if(isset($tool['multi'])&&$tool['multi']==1){?>
				<div class="nova-form-group">
					<label>购买数量 <span class="req">*</span></label>
					<div class="nova-form-row">
						<div class="nova-quantity">
							<button type="button" onclick="changeNum(-1)">−</button>
							<input type="number" id="num" value="1" min="<?php echo $tool['min']?1:1?>" <?php echo $isfaka==1?'max="'.$count.'"':''?>>
							<button type="button" onclick="changeNum(1)">+</button>
						</div>
						<?php if($isfaka){?><span style="color:#999;font-size:13px">库存 <?php echo $count?> 件</span><?php }?>
					</div>
				</div>
				<?php }?>

				<button type="button" id="submit_buy" class="nova-submit-btn">
					<?php echo $price==0?'免费领取':'立即购买'?>
				</button>
				<a href="javascript:history.go(-1)" class="nova-back-btn">返回上一页</a>
			</form>
		</div>
	</div>
</div>

<script>
var hashsalt=<?php echo $addsalt_js?>;
var $_GET = (function(){
    var url = window.document.location.href.toString();
    var u = url.split("?");
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");var get={};
        for(var i in u){var j=u[i].split("=");get[j[0]]=j[1];}
        return get;
    } else {return {};}
})();

function changeNum(delta){
	var input=$('#num');var v=parseInt(input.val())||1;
	var min=parseInt(input.attr('min'))||1;var max=parseInt(input.attr('max'))||999999;
	v=Math.max(min,Math.min(max,v+delta));input.val(v);
}

function getPoint(){
	var multi=$('#tid').attr('multi');
	var count=$('#tid').attr('count');
	var inputname=$('#tid').attr('inputname');
	var inputsname=$('#tid').attr('inputsname');
	$('#inputsname').html('');
	if(inputname=='hide'){
		$('#inputsname').append('<input type="hidden" name="inputvalue" id="inputvalue" value="'+$.cookie('mysid')+'"/>');
	}else{
		if(inputname=='')inputname='下单账号';
		var type='';
		if(inputname.indexOf('[')>0&&inputname.indexOf(']')>0){
			type=inputname.split('[')[1].split(']')[0];
			inputname=inputname.split('[')[0];
		}
		$('#inputsname').append('<div class="nova-form-group"><label>'+inputname+' <span class="req">*</span></label><input type="text" name="inputvalue" id="inputvalue" value="'+($_GET['qq']?$_GET['qq']:'')+'" class="nova-form-control" required></div>');
	}
	if(inputsname!=''){
		$.each(inputsname.split('|'),function(i,value){
			if(value.indexOf('{')>0&&value.indexOf('}')>0){
				var addstr='';var selectname=value.split('{')[0];var selectstr=value.split('{')[1].split('}')[0];
				$.each(selectstr.split(','),function(i,v){if(v.indexOf(':')>0){i=v.split(':')[0];v=v.split(':')[1];}addstr+='<option value="'+i+'">'+v+'</option>';});
				$('#inputsname').append('<div class="nova-form-group"><label>'+selectname+' <span class="req">*</span></label><select name="inputvalue'+(i+2)+'" id="inputvalue'+(i+2)+'" class="nova-form-control">'+addstr+'</select></div>');
			}else{
				var vtype='';
				if(value.indexOf('[')>0&&value.indexOf(']')>0){vtype=value.split('[')[1].split(']')[0];value=value.split('[')[0];}
				var inputType = (vtype=='number')?'number':'text';
				$('#inputsname').append('<div class="nova-form-group"><label>'+value+' <span class="req">*</span></label><input type="'+inputType+'" name="inputvalue'+(i+2)+'" id="inputvalue'+(i+2)+'" class="nova-form-control" required></div>');
			}
		});
	}
}
getPoint();

$('#submit_buy').click(function(){
	var tid=$('#tid').val();
	if(tid==0){layer.alert('请选择商品！');return false;}
	var inputvalue=$('#inputvalue').val();
	if(!inputvalue){layer.alert('请填写下单账号！');return false;}
	var iv2=$('#inputvalue2').val()||'',iv3=$('#inputvalue3').val()||'',iv4=$('#inputvalue4').val()||'',iv5=$('#inputvalue5').val()||'';
	var ii=layer.load(2,{shade:[0.1,'#fff']});
	$.ajax({
		type:"POST",url:"ajax.php?act=pay",
		data:{tid:tid,inputvalue:inputvalue,inputvalue2:iv2,inputvalue3:iv3,inputvalue4:iv4,inputvalue5:iv5,num:$('#num').val(),hashsalt:hashsalt},
		dataType:'json',
		success:function(data){
			layer.close(ii);
			if(data.code==0){
				window.location.href='./?mod=order&orderid='+data.trade_no;
			}else if(data.code==1){
				layer.alert('领取成功！',function(){window.location.href='?buyok=1';});
			}else{
				layer.alert(data.msg||'下单失败，请重试');
			}
		},
		error:function(){layer.close(ii);layer.alert('网络错误，请重试');}
	});
});
</script>
</body>
</html>

