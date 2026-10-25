<?php
/**
 * 分类管理
**/
include("../includes/common.php");
$title='分类管理';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$zid = intval($userrow['zid']);
$act = isset($_GET['act']) ? $_GET['act'] : null;

if($userrow['power']==0){
        showmsg('你没有权限使用此功能！',3);
}

if($act=='add'){
    if(!checkRefererHost()) showmsg('来源校验失败，请返回重试',3);
    $name = trim(daddslashes(isset($_POST['name']) ? $_POST['name'] : ''));
    if($name=='' || mb_strlen($name)>20) showmsg('分类名称不能为空且不能超过20字',3);
    $count = $DB->getColumn("SELECT COUNT(*) FROM pre_class WHERE zid=:zid", array(':zid'=>$zid));
    if($count>=20) showmsg('分类数量已达上限（20个）',3);
    if($DB->getRow("SELECT cid FROM pre_class WHERE zid=:zid AND name=:name LIMIT 1", array(':zid'=>$zid,':name'=>$name))) showmsg('该分类名称已存在',3);
    $sort = $DB->getColumn("SELECT sort FROM pre_class WHERE zid=:zid ORDER BY sort DESC LIMIT 1");
    if($DB->exec("INSERT INTO pre_class (zid,pid,sort,name,active) VALUES (:zid,0,:sort,:name,1)", array(':zid'=>$zid,':sort'=>intval($sort)+1,':name'=>$name))!==false)
        showmsg('新增分类成功！<br/><br/><a href="./classlist.php">>>返回分类管理</a>',1);
    else
        showmsg('新增分类失败！数据库操作异常',4);
}
elseif($act=='del'){
    $cid = intval($_GET['cid']);
    if(!$DB->getRow("SELECT cid FROM pre_class WHERE cid=:cid AND zid=:zid LIMIT 1", array(':cid'=>$cid,':zid'=>$zid))) showmsg('分类不存在或无权操作！',3);
    $DB->exec("UPDATE pre_tools SET cid=0 WHERE zid=:zid AND cid=:cid", array(':zid'=>$zid,':cid'=>$cid));
    $DB->exec("DELETE FROM pre_class WHERE cid=:cid AND zid=:zid", array(':cid'=>$cid,':zid'=>$zid));
    showmsg('删除分类成功！其下商品已归入未分类<br/><br/><a href="./classlist.php">>>返回分类管理</a>',1);
}

if($userrow['power']==0){
        showmsg('你没有权限使用此功能！',3);
}
$classhide = explode(',',$userrow['class']);
$myclasses = array();
$rs = $DB->query("SELECT * FROM pre_class WHERE zid=:zid AND active=1 ORDER BY sort ASC", array(':zid'=>$zid));
while($res = $rs->fetch()) $myclasses[] = $res;
?>
<div class="wrapper">
  <div class="col-sm-12 col-md-10 col-lg-8 center-block" style="float: none;">

<div class="panel panel-default">
    <div class="panel-heading font-bold">
        <h3 class="panel-title">我的店铺分类</h3>
    </div>
    <div class="panel-body">
        <form action="./classlist.php?act=add" method="POST" class="form-inline" style="margin-bottom:15px">
            <div class="form-group"><input type="text" class="form-control" name="name" maxlength="20" required placeholder="新分类名称"></div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> 新增分类</button>
        </form>
        <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>分类名称</th><th width="90">商品数</th><th width="90">操作</th></tr></thead>
          <tbody>
          <?php
          if(empty($myclasses)){
              echo '<tr><td colspan="3" class="text-center text-muted">暂无分类，添加商品时将归入“未分类”</td></tr>';
          }else{
              foreach($myclasses as $c){
                  $n = $DB->getColumn("SELECT COUNT(*) FROM pre_tools WHERE zid=:zid AND cid=:cid", array(':zid'=>$zid,':cid'=>$c['cid']));
                  echo '<tr><td>'.htmlspecialchars($c['name']).'</td><td>'.$n.'</td><td><a href="./classlist.php?act=del&cid='.$c['cid'].'" class="btn btn-danger btn-xs" onclick="return confirm(\'删除分类后其下商品将归入未分类，确定删除？\')">删除</a></td></tr>';
              }
          }
          ?>
          </tbody>
        </table>
        </div>
    </div>
</div>

<?php if($zid>1){ ?>
<div class="panel panel-default">
    <div class="panel-heading font-bold">
        <h3 class="panel-title">平台分类（控制是否在本站显示）</h3>
    </div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>分类名称</th><th width="90">是否显示</th></tr></thead>
          <tbody>
<?php
$rs=$DB->query("SELECT * FROM pre_class WHERE zid=1 AND active=1 ORDER BY sort ASC");
while($res = $rs->fetch())
{
        echo '<tr><td>'.htmlspecialchars($res['name']).'</td><td>'.(in_array($res['cid'],$classhide)?'<span class="btn btn-sm btn-warning" onclick="setActive('.$res['cid'].',1)">隐藏</span>':'<span class="btn btn-sm btn-success" onclick="setActive('.$res['cid'].',0)">显示</span>').'</td></tr>';
}
?>
          </tbody>
        </table>
      </div>
    </div>
</div>
<?php } ?>

</div>
</div>
<?php include './foot.php';?>
<script>
function setActive(cid,active) {
        $.ajax({
                type : 'GET',
                url : 'ajax_user.php?act=setClass&cid='+cid+'&active='+active,
                dataType : 'json',
                success : function(data) {
                        window.location.reload();
                },
                error:function(data){
                        layer.msg('服务器错误');
                        return false;
                }
        });
}
</script>
</body>
</html>
