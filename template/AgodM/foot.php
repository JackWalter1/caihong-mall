
<script src="./assets/AgodM/app_m.js"></script>
<script>

      var hashsalt = <?php echo $addsalt_js ?>;
  //默认点亮第N个ID
  var active_ClassID = <?php echo $shop_cid?>;
    getGoodsPro(active_ClassID);
</script>
	<script>
//获取当前页面的url参数
var _URL = new URL(location.href)
var _P1 = _URL.search
if (_P1 == '?buyok=1') {
    // 跳转到?mod=query
    window.location.href = './?mod=query&type=0&qq=&page=1'
}
	</script>
 
