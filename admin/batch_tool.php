<?php
/**
 * 社区/卡盟对接日志
**/
include("../includes/common.php");
$title='批量工具';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$option = '<option value="-1">--请选择对接社区--</option>';
$option2 = '';
$re=$DB->query("SELECT * FROM `pre_shequ`");
while($res = $re->fetch()){
    $option .= '<option value="'.$res['id'].'" type="'.$res['type'].'">'.($res['remark']==''?'':$res['remark'].'_').$res['url'].'</option>';
}

if($_GET['act'] == 'getGoodsLiti'){
    @header('Content-Type: application/json; charset=UTF-8');
    $shequ = daddslashes($_POST['shequ']);
    $prid = daddslashes($_POST['prid']);
    $row=$DB->getRow("SELECT * FROM `pre_shequ` WHERE type='daishua' AND id='{$shequ}' LIMIT 1");

    //exit('{"code":-1,"msg":"'.json_encode($row).'"}');

    if($row){
        $url = ($row['protocol']==1?'https://':'http://') . $row['url'].'/api_tool/api/api.php?act=getGoodsLiti';
        $post = 'user='.$row['username'].'&pass='.$row['password'];

        $list = get_curl($url, $post);

        exit($list);

    }else{
        exit('{"code":-1,"msg":"获取社区失败"}');
    }
}
?>

<div class="col-md-12 col-lg-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title"><h2>图片下载到本地</h2></div>
        <p>网络的商品图片下载到本地，仅下载如http(s)://开头的网络图片，图片过多可能会超时，多次下载即可，不会重复下载。</p>
        <div class="form-group">
            <div class="input-group"><div class="input-group-addon">请选择社区</div>
                <select class="form-control" id="shequ">
                    <?php echo $option?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group"><div class="input-group-addon">类型</div>
                <select class="form-control" id="type">
                    <option value="0">商品图片</option>
                    <option value="1">商品介绍图片</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group"><div class="input-group-addon">下载数量</div>
                <select class="form-control" id="sum">
                    <option value="50">50个</option>
                    <option value="100">100个</option>
                    <option value="200">200个</option>
                    <option value="9999999">全部</option>
                </select>
            </div>
        </div>
        <p><input type="button" name="submit" value="下载" class="btn btn-primary btn-block" onclick="downloadAll()"></p>
    </div>

    <div class="block">
        <div class="block-title"><h2>商品标题批量修改</h2></div>
        <p>批量对接时，是无法判断商品下单信息是否要提取ID、链接。当商品介绍里面有某个关键字如【作品链接、主页链接、作品ID....】可以用本工具来修改本系统的下单标题</p>
        <div class="form-group">
            <div class="input-group"><div class="input-group-addon">请选择社区</div>
                <select class="form-control" id="shequ2">
                    <?php echo $option?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="input-group mt-10"> <span class="input-group-addon" id="nameNumber">商品介绍里的关键字</span>
                <input type="text" class="form-control" id="keyword" placeholder="作品链接">
            </div>
        </div>


        <div class="form-group">
            <div class="input-group mt-10"> <span class="input-group-addon" id="nameNumber">第一个输入框标题：</span>
                <input type="text" class="form-control" id="title" placeholder="作品ID[shareid]">
                <span class="input-group-btn"><a href="#inputabout" data-toggle="modal" class="btn btn-info" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a></span>
            </div>
        </div>
        <p><input type="button" name="submit" value="替换" class="btn btn-primary btn-block" onclick="upInputTitle()"></p>
    </div>

    <div class="block">
        <div class="block-title"><h2>初始化商品</h2></div>
        <p>商品图片、商品介绍、下单参数</p>

        <div class="form-group">
            <div class="input-group"><div class="input-group-addon">初始化商品</div>
                <select class="form-control" id="shequ3">
                    <?php echo $option?>
                </select>
                <span class="input-group-btn"><a onclick="getGoodsParam()" class="btn btn-default">初始化</a></span> 
            </div>
        </div>
    </div>

</div>

<div class="modal" align="left" id="inputabout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">输入框标题说明</h4>
      </div>
      <div class="modal-body">
      使用以下输入框标题可实现特殊的转换功能<br/>
      自动从链接和文字取出链接：<a href="javascript:changeinput('作品链接')">作品链接</a>、<a href="javascript:changeinput('视频链接')">视频链接</a>、<a href="javascript:changeinput('分享链接')">分享链接</a>、<a href="javascript:changeinput('自定义[shareurl]')">自定义[shareurl]</a><br/>
      自动获取音乐/视频ID：<a href="javascript:changeinput('作品ID')">作品ID</a>、<a href="javascript:changeinput('帖子ID')">帖子ID</a>、<a href="javascript:changeinput('用户ID')">用户ID</a>、<a href="javascript:changeinput('自定义[shareid]')">自定义[shareid]</a><br/><hr/>
      注：在输入框名称后面加[shareid]、[shareurl]可以分别有获取ID、获取URL功能
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script>
    function changeinput(str){
        $("#title").val(str);
    }
    function upInputTitle(){
        var shequ = $('#shequ2').val();
        var keyword = $('#keyword').val();
        var title = $('#title').val();
        if(keyword == '' || title == ''){
            layer.msg('不能为空');return false;
        }
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax_batch_docking.php?act=upInputTitle",
            dataType : 'json',
            data : {shequ:shequ,keyword:keyword,title:title},
            success : function(data) {
                layer.close(ii);
                layer.alert(data.msg);
            },
            error:function(data){
                layer.close(ii);
                layer.msg('加载失败，请刷新重试');
                return false;
            }
        });
    }
     function downloadAll(){
        var shequ = $('#shequ').val();
        var type = $('#type').val();
        var sum = $('#sum').val();
        if(shequ == -1){
            return false;
        }
        if(type == 1){
            type= 'downloaddescimg';
        }else{
            type= 'downloadimg2';
        }
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax_batch_docking.php?act="+type,
            dataType : 'json',
            data : {shequ:shequ,sum:sum},
            success : function(data) {
                layer.close(ii);
                if(data.code == 1){
                    layer.alert(data.msg);
                }else{
                    layer.alert(data.msg, {icon:2});
                }
            },
            error:function(data){
                layer.close(ii);
                layer.msg('加载失败，请刷新重试');
                return false;
            }
        });
     }
    
    // 初始化商品功能
    function getGoodsParam(){
        var shequ = $('#shequ3').val();
        if(shequ == -1){
            layer.alert('请选择社区');return false;
        }
        layer.open({
            title: '执行的数量',
            content: '由于每个社区访问速度不一样，如果数量过多会超时的。<br><div class="input-group"> <span class="input-group-addon">执行数量</span><select id="count" class="form-control"><option value="1">1</option><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="50">50</option><option value="100">100</option><option value="200">200</option><option value="1000">全部</option></select></div>',
            btn:['确定','取消'],
            yes:function(index,layero){
                var count = $('#count').val();
                var ii = layer.load(2, {shade:[0.1,'#fff']});
                $.ajax({
                    type : "POST",
                    url : "ajax_batch_docking.php?act=getGoodsParam",
                    dataType : 'json',
                    data : {shequ:shequ,count:count},
                    success : function(data) {
                        layer.close(ii);
                        if(data.code == 0){
                            layer.alert(data.msg);
                        }else{
                            layer.alert(data.msg, {icon:2});
                        }
                    },
                    error:function(data){
                        layer.close(ii);
                        layer.msg('加载失败，请刷新重试');
                        return false;
                    }
                });            
            }
        });
    }
</script>

    </div>
</div>