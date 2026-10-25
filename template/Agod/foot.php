<style>
<?php echo $conf['userStyle'];?>
</style>

	<script>
//获取当前页面的url参数
var _URL = new URL(location.href)
var _P1 = _URL.search
if (_P1 == '?buyok=1') {

    window.location.href = './?mod=query&type=0&qq=&page=1'
}

	</script>
</body>
</html>