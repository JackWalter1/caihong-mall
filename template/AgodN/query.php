<?php
if(!defined('IN_CRONLITE'))exit();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <title>订单查询 - <?php echo $conf['sitename']?></title>
    <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            font-family: "Microsoft YaHei", sans-serif;
        }
        .query-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .query-header {
            background: #4e8cff;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .query-header h4 {
            margin: 0;
            font-size: 16px;
        }
        .query-input {
            margin-bottom: 15px;
        }
        .query-btn {
            background: #4e8cff;
            color: white;
            border: none;
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            font-size: 15px;
        }
        .query-result {
            margin-top: 20px;
            display: none;
        }
        .table {
            background: white;
            border-radius: 5px;
            overflow: hidden;
        }
        .table>thead>tr>th {
            border-bottom: 2px solid #4e8cff;
            background: #f8f9fa;
        }
        .back-btn {
            margin-bottom: 15px;
            display: inline-block;
            color: #666;
            text-decoration: none;
        }
        .back-btn:hover {
            color: #4e8cff;
            text-decoration: none;
        }
        @media (max-width: 768px) {
            .table-responsive {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="./" class="back-btn"><i class="fa fa-angle-left"></i> 返回首页</a>
        <div class="query-card">
            <div class="query-header">
                <h4><i class="fa fa-search"></i> 订单查询</h4>
            </div>
            <div class="form-group query-input">
                <div class="input-group">
                    <div class="input-group-addon">
                        <select id="searchtype" style="border: none;background: transparent;">
                            <option value="0">下单账号</option>
                            <option value="1">支付订单号</option>
                        </select>
                    </div>
                    <input type="text" id="qq" class="form-control" placeholder="请输入下单账号或支付订单号" required>
                </div>
            </div>
            <button type="button" id="submit_query" class="query-btn">立即查询</button>
            
            <div id="result" class="query-result">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>下单账号</th>
                                <th>商品名称</th>
                                <th>数量</th>
                                <th>购买时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody id="list">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $cdnpublic?>jquery/3.4.1/jquery.min.js"></script>
    <script src="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
    <script src="assets/faka/js/query.js"></script>
    <script>
    $(document).ready(function(){
        function queryOrder(qq, type){
            if(qq==''){layer.alert('请确保每项不能为空！');return false;}
            $('#submit_query').val('Loading');
            var ii = layer.load(2, {shade:[0.1,'#fff']});
            $.ajax({
                type: 'POST',
                url: 'ajax.php?act=query',
                data: {qq:qq,type:type},
                dataType: 'json',
                success: function(data) {
                    layer.close(ii);
                    if(data.code == 0){
                        var item = '';
                        $.each(data.data, function(i, order){
                            var status_class = 'default';
                            var status_text = '';
                            switch(order.status) {
                                case '0':
                                    status_class = 'warning';
                                    status_text = '待处理';
                                    break;
                                case '1':
                                    status_class = 'success';
                                    status_text = '已完成';
                                    break;
                                case '2':
                                    status_class = 'danger';
                                    status_text = '处理失败';
                                    break;
                                case '3':
                                    status_class = 'info';
                                    status_text = '已退款';
                                    break;
                                default:
                                    status_text = '未知';
                            }
                            item += '<tr><td>'+order.input+'</td><td>'+order.name+'</td><td>'+order.value+'</td><td>'+order.addtime+'</td><td><span class="label label-'+status_class+'">'+status_text+'</span></td><td><a href="javascript:;" onclick="showOrder(\''+order.id+'\',\''+order.skey+'\')" class="btn btn-success btn-xs">查看卡密</a></td></tr>';
                        });
                        $('#list').html(item);
                        $('#result').slideDown();
                        
                        // 自动点击第一个订单的查看卡密按钮
                        if(data.data && data.data.length > 0){
                            setTimeout(function(){
                                showOrder(data.data[0].id, data.data[0].skey);
                            }, 100);
                        }
                    }else{
                        layer.alert(data.msg);
                    }
                },
                error: function(){
                    layer.close(ii);
                    layer.alert('加载失败，请刷新重试！');
                }
            });
        }

        // 获取URL中的orderid参数
        var orderid = getQueryString('orderid');
        console.log('orderid:', orderid); // 调试信息
        if(orderid){
            $('#searchtype').val(1);
            $('#qq').val(orderid);
            // 直接调用查询函数
            setTimeout(function(){
                queryOrder(orderid, 1);
            }, 100);
        }

        function getQueryString(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if (r != null) return decodeURI(r[2]);
            return null;
        }

        $('#submit_query').click(function(){
            var type=$('#searchtype').val();
            var qq=$('#qq').val();
            queryOrder(qq, type);
        });
    });
    </script>
</body>
</html> 