<?php
include("../includes/common.php");
$title='防墙引导页设置';
$mod='wall_guide';  // 用于菜单选中
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$mod=isset($_GET['mod'])?$_GET['mod']:null;

?>
<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title"><h3 class="panel-title">功能说明</h3></div>
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <p><strong>功能说明：</strong></p>
                    <ul>
                        <li>开启后访问网站时会先显示一个引导页面</li>
                        <li>用户点击继续访问按钮后24小时内不再显示</li>
                        <li>可用于网站免责声明、防止被墙、防止恶意爬虫等</li>
                        <li>支持自定义主题颜色，自动适配暗黑模式</li>
                        <li>简洁高效的设计，加载速度快</li>
                    </ul>
                    <p><strong>配置项：</strong></p>
                    <ul>
                        <li><b>标题</b>：引导页顶部标题</li>
                        <li><b>内容</b>：主要提示内容（支持换行）</li>
                        <li><b>显示间隔</b>：设置多久显示一次引导页</li>
                        <li><b>主题颜色</b>：设置引导页的主题色调</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 防墙引导页设置 -->
    <div class="block">
        <div class="block-title"><h3 class="panel-title">防墙引导页设置</h3></div>
        <div class="card">
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">防墙引导页开关</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="wall_guide_open" default="<?php echo $conf['wall_guide_open']?>">
                                <option value="0">关闭</option>
                                <option value="1">开启</option>
                            </select>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">引导页标题</label>
                        <div class="col-sm-9">
                            <input type="text" name="wall_guide_title" value="<?php echo $conf['wall_guide_title']; ?>" class="form-control" placeholder="请输入引导页标题">
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">引导页内容</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="wall_guide_content" rows="3" placeholder="请输入引导页显示的内容"><?php echo $conf['wall_guide_content']; ?></textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">按钮文字</label>
                        <div class="col-sm-9">
                            <input type="text" name="wall_guide_btn" value="<?php echo $conf['wall_guide_btn']; ?>" class="form-control" placeholder="请输入按钮显示的文字">
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">显示间隔</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="number" name="wall_guide_interval" value="<?php echo !empty($conf['wall_guide_interval']) ? $conf['wall_guide_interval'] : 24; ?>" class="form-control" placeholder="请输入数值" min="1" max="720">
                                </div>
                                <div class="col-sm-6">
                                    <select class="form-control" name="wall_guide_interval_unit" default="<?php echo !empty($conf['wall_guide_interval_unit']) ? $conf['wall_guide_interval_unit'] : 'hour'; ?>">
                                        <option value="second">秒</option>
                                        <option value="minute">分</option>
                                        <option value="hour">时</option>
                                    </select>
                                </div>
                            </div>
                            <span class="help-block">设置访问者多久会再次看到引导页，支持秒、分、小时单位选择</span>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">主题颜色</label>
                        <div class="col-sm-9">
                            <input type="color" name="wall_guide_theme_color" value="<?php echo !empty($conf['wall_guide_theme_color']) ? $conf['wall_guide_theme_color'] : '#2193b0'; ?>" class="form-control">
                            <span class="help-block">设置引导页的主题颜色，影响按钮、图标等元素的颜色</span>
                        </div>
                    </div>
                    <br/>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel-footer">
            <span class="glyphicon glyphicon-info-sign"></span>
            开启后访问前台会先显示引导页,点击继续访问后在设定的时间内不再显示,时间到期后需再次点击
        </div>
    </div>
</div>
</div>
<?php include './foot.php';?>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
    $(items[i]).val($(items[i]).attr("default")||0);
}
function saveSetting(obj){
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : 'ajax_wall_guide.php?act=set',
        data : $(obj).serialize(),
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.alert('设置保存成功！', {
                    icon: 1,
                    closeBtn: false
                }, function(){
                    window.location.reload()
                });
            }else{
                layer.alert(data.msg, {icon: 2})
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
    return false;
}
</script>