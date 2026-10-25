<?php
//请勿删除版权信息，否则出现问题将不再保持售后修复！
include "../includes/common.php";
$title = "商品介绍批量替换";
include "./head.php";

if($islogin != 1){
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

//权限验证
adminpermission("shop", 1);

$my = isset($_GET["my"]) ? $_GET["my"] : "index";

//核心替换功能
function performReplace($search, $replace, $preview = true){
    global $DB;
    
    if(empty($search)){
        return ["status"=>0, "msg"=>"查找内容不能为空"];
    }
    
    //查找匹配记录
    $rows = $DB->getAll("SELECT tid, name, `desc` FROM pre_tools WHERE `desc` LIKE :pattern", 
        [':pattern' => "%{$search}%"]
    );
    
    if(!$rows){
        return ["status"=>0, "msg"=>"未找到匹配的商品介绍"];
    }
    
    $results = [];
    $affected = 0;
    
    foreach($rows as $row){
        $newDesc = str_replace($search, $replace, $row['desc']);
        $isChanged = ($newDesc !== $row['desc']);
        
        $results[] = [
            "tid" => $row['tid'],
            "name" => $row['name'],
            "old_desc" => $row['desc'],
            "new_desc" => $newDesc,
            "changed" => $isChanged
        ];
        
        if($isChanged && !$preview){
            //执行更新
            $DB->exec("UPDATE pre_tools SET `desc`=:newDesc, `uptime`=NOW() WHERE tid=:tid",
                [':newDesc'=>$newDesc, ':tid'=>$row['tid']]
            );
            $affected++;
        }
    }
    
    return [
        "status" => 1,
        "msg" => $preview ? "找到 ".count($rows)." 条匹配记录" : "成功替换 {$affected} 条记录",
        "data" => $results,
        "affected" => $affected
    ];
}

//显示操作结果
function showResult($result){
    if($result['status']){
        showmsg($result['msg'] . "<br/><br/><a href='./shopnoo.php'>>>返回继续操作</a><br/><a href='./shoplist.php'>>>返回商品列表</a>", 1);
    }else{
        showmsg($result['msg'], 3);
    }
}

if($my == "index"){
    ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="block">
                <div class="block-title">
                    <h3 class="panel-title">插件贡献者：小杰云商城</h3>
                </div>
                
                <form action="./shopnoo.php?my=preview" method="POST">
                    <div class="form-group">
                        <label for="search"><font color="red">*</font> 查找内容（替换前）:</label>
                        <textarea class="form-control" id="search" name="search" rows="5" 
                            placeholder="请输入要查找的文本内容，支持任意字符"><?php echo htmlspecialchars($_POST['search'] ?? '');?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="replace">替换为（替换后）:</label>
                        <textarea class="form-control" id="replace" name="replace" rows="5" 
                            placeholder="请输入替换后的内容，留空则删除查找的内容"><?php echo htmlspecialchars($_POST['replace'] ?? '');?></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>提示：</strong><br>
                        1. 请先使用"预览替换"查看匹配结果<br>
                        2. 支持替换HTML代码和文本内容<br>
                        3. 操作不可撤销，请谨慎操作
                    </div>
                    
                    <button type="submit" class="btn btn-info btn-block">
                        <i class="glyphicon glyphicon-search"></i> 预览替换结果
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php
    
}elseif($my == "preview"){
    $search = $_POST['search'] ?? '';
    $replace = $_POST['replace'] ?? '';
    
    if(empty($search)){
        showmsg('查找内容不能为空！', 3);
        exit;
    }
    
    $result = performReplace($search, $replace, true);
    
    if(!$result['status']){
        showmsg($result['msg'], 3);
        exit;
    }
    
    $changedItems = array_filter($result['data'], function($item){
        return $item['changed'];
    });
    ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="block">
                <div class="block-title">
                    <h3 class="panel-title">替换预览确认</h3>
                </div>
                
                <div class="alert alert-info">
                    共找到 <?php echo count($result['data']); ?> 条记录，其中 <?php echo count($changedItems); ?> 条将被修改
                </div>
                
                <?php if(!empty($changedItems)): ?>
                    <form action="./shopnoo.php?my=execute" method="POST" onsubmit="return confirmReplace()">
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search);?>" />
                        <input type="hidden" name="replace" value="<?php echo htmlspecialchars($replace);?>" />
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th width="60">商品ID</th>
                                        <th width="150">商品名称</th>
                                        <th>替换前内容</th>
                                        <th>替换后预览</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($changedItems as $item): ?>
                                        <tr>
                                            <td><?php echo $item['tid'];?></td>
                                            <td><?php echo htmlspecialchars($item['name']);?></td>
                                            <td><div style="max-height:80px;overflow:auto;"><?php echo htmlspecialchars($item['old_desc']);?></div></td>
                                            <td><div style="max-height:80px;overflow:auto;"><?php echo htmlspecialchars($item['new_desc']);?></div></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="glyphicon glyphicon-ok"></i> 确认执行替换
                        </button>
                        
                        <br/><a href="./shopnoo.php" class="btn btn-default btn-block">
                            <i class="glyphicon glyphicon-chevron-left"></i> 返回重新编辑
                        </a>
                    </form>
                    
                    <script>
                    function confirmReplace(){
                        return confirm("确定要执行批量替换操作吗？此操作将直接修改数据库中的数据，不可撤销！");
                    }
                    </script>
                <?php else: ?>
                    <div class="alert alert-warning">
                        没有找到需要替换的内容，请检查查找条件是否正确
                    </div>
                    <a href="./shopnoo.php" class="btn btn-default btn-block">
                        <i class="glyphicon glyphicon-chevron-left"></i> 返回重新编辑
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    
}elseif($my == "execute"){
    $search = $_POST['search'] ?? '';
    $replace = $_POST['replace'] ?? '';
    
    if(empty($search)){
        showmsg('查找内容不能为空！', 3);
        exit;
    }
    
    //执行替换
    $result = performReplace($search, $replace, false);
    
    //记录操作日志
    if($result['affected'] > 0){
        $content = "批量替换商品介绍：{$search} → {$replace}，影响 {$result['affected']} 条记录";
        $DB->exec("INSERT INTO `pre_toollogs` (`content`, `date`, `addtime`, `active`) VALUES (:content, CURDATE(), NOW(), 1)",
            [':content'=>$content]
        );
    }
    
    showResult($result);
}

include "foot.php";
?>
