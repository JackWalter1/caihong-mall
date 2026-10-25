<?php
include("../includes/common.php");
$title='域名落地页配置';
$mod='domain_landing';  // 用于菜单选中
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 自动创建数据表
$DB->exec("CREATE TABLE IF NOT EXISTS `pre_domain_landing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_domain` varchar(255) NOT NULL,
  `new_domain` varchar(255) NOT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `old_domain` (`old_domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 添加或修改域名
if(isset($_POST['submit'])) {
    $old_domain = trim(strtolower($_POST['old_domain']));
    $new_domain = trim(strtolower($_POST['new_domain']));
    
    if(empty($old_domain) || empty($new_domain)) {
        showmsg('域名不能为空',3);
    }
    
    // 验证新旧域名不能相同
    if($old_domain === $new_domain) {
        showmsg('新旧域名不能相同',3);
    }
    
    // 去除http(s)://前缀
    $old_domain = preg_replace('/^https?:\/\//', '', $old_domain);
    $new_domain = preg_replace('/^https?:\/\//', '', $new_domain);
    
    // 再次验证去除前缀后的域名不能相同
    if($old_domain === $new_domain) {
        showmsg('新旧域名不能相同',3);
    }
    
    $sds = $DB->exec("INSERT INTO `pre_domain_landing` (`old_domain`, `new_domain`, `addtime`) VALUES (:old_domain, :new_domain, NOW()) ON DUPLICATE KEY UPDATE `new_domain`=:new_domain2", [
        ':old_domain' => $old_domain,
        ':new_domain' => $new_domain,
        ':new_domain2' => $new_domain
    ]);
    
    showmsg('设置保存成功！',1);
}

// 删除域名
if(isset($_GET['act']) && $_GET['act']=='del'){
    $id=intval($_GET['id']);
    $sql="DELETE FROM pre_domain_landing WHERE id='$id' limit 1";
    if($DB->exec($sql)){
        showmsg('删除成功！',1);
    }else{
        showmsg('删除失败！',4);
    }
}

// 获取域名列表
$list = $DB->getAll("SELECT * FROM pre_domain_landing ORDER BY id DESC");
?>

<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title"><h3 class="panel-title">域名落地页配置</h3></div>
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>通配符使用说明：</strong><br/>
                    1. 支持使用*作为通配符，例如：*.example.com 可以匹配所有example.com的子域名<br/>
                    2. 新域名也可以使用*，会自动替换为原域名中*匹配的部分<br/>
                    3. 例如：*.old.com -> *.new.com，访问abc.old.com会跳转到abc.new.com<br/>
                    4. 规则优先级按添加时间倒序排列，即最新添加的规则优先匹配
                </div>
                <form action="./set_domain_landing.php" method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">旧域名</label>
                        <div class="col-sm-10">
                            <input type="text" name="old_domain" class="form-control" placeholder="请输入需要跳转的域名" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">新域名</label>
                        <div class="col-sm-10">
                            <input type="text" name="new_domain" class="form-control" placeholder="请输入跳转到的域名" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" name="submit" value="添加" class="btn btn-primary form-control"/>
                        </div>
                    </div>
                </form>
                <br/>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead><tr><th>ID</th><th>旧域名</th><th>新域名</th><th>添加时间</th><th>操作</th></tr></thead>
                        <tbody>
                        <?php
                        foreach($list as $res){
                            echo '<tr><td>'.$res['id'].'</td><td>'.$res['old_domain'].'</td><td>'.$res['new_domain'].'</td><td>'.$res['addtime'].'</td><td><a href="./set_domain_landing.php?act=del&id='.$res['id'].'" class="btn btn-xs btn-danger" onclick="return confirm(\'你确实要删除此记录吗？\');">删除</a></td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './foot.php';?>