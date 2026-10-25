<?php

include "../includes/common.php";
$title = "自定义分站商品密价";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script>window.location.href='./login.php';</script>");
}
?><div class="col-md-12 center-block" style="float: none;">
    <?php 
adminpermission("site", 1);
$zid = isset($_GET["zid"]) ? intval($_GET["zid"]) : showmsg("参数不完整");
?>    <div class="block">
        <div class="block-title clearfix">
            <h2 id="blocktitle"></h2>
            <span class="pull-right"><select id="pagesize" class="form-control"><option value="30">30</option><option value="50">50</option><option value="60">60</option><option value="80">80</option><option value="100">100</option></select><span>
</span></span>
        </div>
        <form onsubmit="return searchItem()" method="GET" class="form-inline">
            <div class="form-group">
                <input type="text" class="form-control" name="kw" placeholder="请输入商品名称">
            </div>
            <button type="submit" class="btn btn-info">搜索</button>&nbsp;
            <a href="javascript:clearPrice()" class="btn btn-warning">恢复原价</a>&nbsp;
            <a href="javascript:changePrice()" class="btn btn-success">一键密价修改</a>&nbsp;
            <a href="javascript:listTable('start')" class="btn btn-default" title="刷新商品列表"><i class="fa fa-refresh"></i></a>
        </form>

        <div id="listTable"></div>
    </div>
</div>
<script type="text/html" id="content">
    <div class="panel-body">
        <div class="form-group">
            <label>批量类型:</label><br>
            <select class="form-control" name="type">
                <option value="0">百分比</option>
                <option value="1">固定值</option>
            </select>
        </div>
        <div class="form-group">
            <label>修改值:</label><br>
            <input type="number" class="form-control" name="value" placeholder="填写百分比或固定值">
        </div>
        <input type="button" id="submit" class="btn btn-primary btn-block" value="确定修改">
    </div>
</script>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/siteprice.js?ver=<?php echo VERSION;?>"></script>
<script>
    function changePrice(){
        layer.open({
            type: 1,
            title: '密价一键修改',
            shade: 0.1,
            content: $("#content").html(),
            success: function(index, layero){
                $("#submit").click(function (){
                    var value = $("input[name='value']").val();
                    var type = $("select[name='type']").val();
                    if(value == ''){
                        layer.msg('请勿留空！');
                        return false;
                    }
                    $.ajax({
                        type:"post",
                        url:"ajax_site.php?act=change_price",
                        data:{type:type, value:value, zid:zid},
                        dataType:"json",
                        success:function(data){
                            layer.closeAll();
                            if(data.code==0){
                                layer.alert('价格修改成功！',{icon:1},function(){
                                    listTable();
                                });
                            }else{
                                layer.alert(data.msg);
                            }
                        }
                    });
                });
            }
        })

        // layer.prompt({title: '价格按降低百分比，无忧支付内部版', formType: 0}, function(text, index){
        //     layer.close(index);
        //     $.ajax({
        //         type:"post",
        //         url:"ajax_site.php?act=change_price",
        //         data:{
        //             rate:text,zid:zid
        //         },
        //         dataType:"json",
        //         success:function(data){
        //             if(data.code==0){
        //                 layer.alert('价格修改成功！',{icon:1},function(){
        //                     listTable();
        //                 });
        //             }else{
        //                 layer.alert(data.msg);
        //             }
        //         }
        //     });
        // });
    }
</script>
</body>
</html>