<?php
$id = intval($_GET['id']);
if (md5($id . SYS_KEY . $id) !== $_GET['skey']) exit("<script language='javascript'>alert('验证失败');history.go(-1);</script>");
$row = $DB->getRow("SELECT * FROM pre_orders WHERE id='$id' LIMIT 1");
if (!$row) exit("<script language='javascript'>alert('当前订单不存在！');history.go(-1);</script>");
$tool = $DB->getRow("SELECT * FROM pre_tools WHERE tid='{$row['tid']}' LIMIT 1");
if ($tool['is_curl'] != 4 && $row['djzt'] != 3) exit("<script language='javascript'>alert('非发卡类商品！');history.go(-1);</script>");
$count = ($tool['value'] > 1 ? $tool['value'] : 1) * $row['value'];
$rs = $DB->query("SELECT * FROM pre_faka WHERE tid='{$row['tid']}' AND orderid='$id' ORDER BY kid ASC LIMIT {$count}");
$rs2 = $DB->query("SELECT * FROM pre_faka WHERE tid='{$row['tid']}' AND orderid='$id' ORDER BY kid ASC LIMIT {$count}");

$kmdata = '';
$kmdata2 = '';
$rcount = 0;

while ($res = $rs->fetch()) {
	$rcount++;
	if (!empty($res['pw'])) {
		$kmdata .= '卡号：' . $res['km'] . ' 密码：' . $res['pw'] . "\r\n";
		$kmdata2 .= '卡号：' . $res['km'] . ' 密码：' . $res['pw'] . "<br>";
	} else {
		$kmdata .= $res['km'] . "\r\n";
		$kmdata2 .= $res['km'] . "<br>";
	}
}



if (strlen($kmdata) > 2) $kmdata = substr($kmdata, 0, -2);


?>

<html lang="zh">
  <head>
    <meta
      http-equiv="Content-Type"
      content="text/html; charset=UTF-8"
    />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1,user-scalable=no"
    />
    <meta
      name="format-detection"
      content="telephone=no"
    />
    <meta
      name="csrf-param"
      content="_csrf"
    />
    <title><?php echo $hometitle?></title>
    <meta
      name="keywords"
      content="<?php echo $conf['keywords'] ?>"
    />
    <meta
      name="description"
      content="<?php echo $conf['description'] ?>"
    />

    <script src="./assets/Agod/jquery-3.6.0.min.js"></script>
      <link href="//lib.baomitu.com/tailwindcss/2.2.9/tailwind.min.css" rel="stylesheet">
    <script src="./assets/Agod/axios.min.js"></script>
    <script src="./assets/Agod/vue.min.js"></script>
      <script src="./assets/Agod/nyro.js"></script>
      <script src="./assets/Agod/layer.js"></script>
  </head>
      <body class="bg-gray-50 font-mono">
    <div id="app">

          <div class="relative mx-auto rounded-md bg-white">
    <div style=" background-image: url('./assets/Agod/img/pc_header.jpg');" class="py-20 text-center text-4xl text-white shadow-md">
      订单查询
      <p class="pt-5 text-lg">轻松查询订单，即刻享受卡密自动交易</p>
    </div>
    <div class="pt-10 pb-5 text-center text-2xl text-gray-700">
      卡密信息 <a class="text-red-400 hover:text-blue-600" href="./"><span>返回首页</span></a>
    </div>
    <!--<div class="py-10 text-center">-->
    <!--  <input placeholder="请输入联系方式进行查询" class="rounded-3xl border py-3 px-4 text-center text-lg text-gray-700 placeholder:text-gray-400 sm:w-96" value="" type="text" />-->
    <!--<a href="?mod=query"> <button  class="text-md rounded-3xl bg-purple-600 py-4 px-8 text-white shadow-md">查询订单</button></a> -->
    <!--</div>-->
    <div class="pb-5 text-center text-xs text-gray-400">免责声明：卖家次日可在平台申请提现，因此如果您对订单存在疑惑，请在购买当天23:59前在网站上投诉订单，逾期请自行与卖家协商解决。</div>
    <div class="py-8 text-center text-xl text-gray-600">订单查询结果</div>
    <div class="mx-auto max-w-4xl p-2 py-10 shadow">
        <div class="mx-auto max-w-3xl rounded-md p-4 shadow">
            <textarea id="txt_0" readonly class="w-full rounded-md border-z-30" wrap="off"><?php echo $kmdata ?></textarea>
        </div>
      <p class="text-center text-gray-400">您的卡密：<?php echo $kmdata ?> <button  data-clipboard-action="copy"
                data-clipboard-target="#txt_0" class="rounded-3xl border bg-gray-900 hover:bg-gray-700 text-white border-orange-300 px-6 py-1 text-orange-300" id="clipboard_btn">复制</button></p>

                 <p v-if="info.desc" class="text-center text-gray-400">使用说明：
                      <div class="text-center text-gray-400" style="word-break: break-all;" v-html='info.desc' >
                </div>
      </p>
      <div class="mx-auto max-w-3xl rounded-md p-4 shadow">
        <div class="flex justify-between">
          <div v-if="info.name">
            <p class="text-lg font-bold text-gray-500">订单号: <span class="text-base font-light text-gray-400"></span>{{_ID}}</p>
            <p class="text-lg font-bold text-gray-500">售后卖家QQ： <span class="text-base font-light text-gray-400"><?php echo $conf['kfqq'];?></span></p>
            <p class="text-lg font-bold text-gray-500">付款时间： <span class="text-base font-light text-gray-400">{{info.date}}</span></p>
            <p class="text-lg font-bold text-gray-500">商品名称： <span class="text-base font-light text-gray-400">{{info.name}}</span></p>
            <p class="text-lg font-bold text-gray-500">已发卡密： <span class="text-base font-light text-gray-400">{{_VAL}}张</span></p>
          </div>
          <div>
            <p class="mt-2"><button onclick="Export()" class="rounded-3xl border border-orange-300 px-6 py-1 text-orange-300">导出</button></p>
            <p class="mt-1"><button  data-clipboard-action="copy"
                data-clipboard-target="#txt_0" id="clipboard_btn" class="rounded-3xl border border-orange-300 px-6 py-1 text-orange-300">复制</button></p>
          </div>
        </div>

        <hr class="mt-4 mb-4" />
        <div class="px-10 text-sm text-gray-400">
          <p class="text-center">平台免责提示：假卡/欺诈/延迟发货等情况必须在当天24点前在网站投诉订单和联系平台客服处理隔天商户体现资金难以追回</p>
          <p class="text-center">（本平台仅提供自助服务，并非销售商，不清楚卡密真实用途！虚拟物品售出后无法二次售卖，非卡密问题本站有权驳回一切投诉）</p>
          <p class="text-center">有疑问请及时联系卖家QQ：<?php echo $conf['kfqq'];?> 联系不上卖家请在当天24点前在网站上方投诉订单</p>
        </div>
      </div>
    </div>
  </div>


<script src="<?php echo $cdnpublic ?>FileSaver.js/2014-11-29/FileSaver.min.js"></script>
<script src="<?php echo $cdnpublic ?>clipboard.js/1.7.1/clipboard.min.js"></script>
<script>
function Export() {
    const data = $('#txt_0').val();
    var _blob = new Blob([data], {
        type: "text/plain;charset=utf-8"
    });
    saveAs(_blob, ('kami') + ".txt");

}

$("#saveas-bt").on("click", function() {
    console.log('app')
    var txt = $("#txt_0").val();
    if (txt.indexOf('\r\n') < 0) {
        txt = txt.replace(/\n/g, "\r\n");
    }
    var fileName = (new Date()).toISOString().substr(0, 10) + ".txt";
    var blob = new Blob([txt], {
        type: "text/plain;charset=utf-8"
    });
    saveAs(blob, fileName);
});

var clipboard = new Clipboard('#clipboard_btn');
clipboard.on('success', function(e) {
    layer.msg('复制成功')
});
clipboard.on('error', function(e) {
    layer.msg('复制失败')
});
</script>

<script>
const app = new Vue({
    el: '#app',
    data: {
        info: '',
        _ID: '',
        _VAL: ''
    },
    created() {
        const _URL = new URL(location.href)
        const _ID = _URL.searchParams.get('id')
        const _SK = _URL.searchParams.get('skey')
        const _VAL = _URL.searchParams.get('value')
        this._ID = _ID
        this._VAL = _VAL
        this.getOrderInfo(_ID, _SK)
        // 得到参数
    },
    methods: {
        //得到订单信息
        getOrderInfo(_ID, _SK) {
            const data = `id=${_ID}&skey=${_SK}`
            $.ajax({
                type: 'POST',
                url: './ajax.php?act=order',
                data: data,
                dataType: 'JSON',
                success: (res) => {
                    this.info = res
                    console.log(this.info)
                }
            })
            // 传入参数

        }
    }
})
</script>
    </div>
  </body>
</html>
