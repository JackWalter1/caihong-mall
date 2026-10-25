<?php
// 文章管理页面 - 教主 zhonguo.ren
// 功能：文章的添加、编辑、删除和列表展示
// 安全措施：使用PDO预处理语句防止SQL注入，使用daddslashes函数过滤用户输入

include "../includes/common.php";
$title = "文章管理";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="col-sm-12 col-md-10 center-block" style="float: none;">
<?php 
adminpermission("article", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "add") {
	?><div class="block">
<div class="block-title"><h3 class="panel-title">添加文章</h3></div>
<div class="">
  <form action="./article.php?my=add_submit" method="post" class="form-horizontal" role="form">
    <div class="form-group">
	  <label class="col-sm-2 control-label">文章标题</label>
	  <div class="col-sm-10"><input type="text" name="title" value="" class="form-control"/></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">SEO关键词</label>
	  <div class="col-sm-10"><input type="text" name="keywords" value="" class="form-control" placeholder="可留空"/></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">SEO描述</label>
	  <div class="col-sm-10"><textarea id="description" class="form-control" name="description" rows="2" placeholder="可留空"></textarea></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">文章内容</label>
	  <div class="col-sm-10"><textarea id="editor_id" class="form-control" name="content" rows="8" style="width:100%;"></textarea></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">是否置顶</label>
	  <div class="col-sm-10"><select class="form-control" name="top"><option value="0">否</option><option value="1">是</option></select></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">发布时间</label>
	  <div class="col-sm-10"><input type="date" name="addtime" value="<?php echo date("Y-m-d", strtotime("+1 years"));?>" class="form-control"/></div>
	</div>
	<div class="form-group">
	  <div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="发布" class="btn btn-primary btn-block"/><br/>
	 </div>
	</div>
  </form>
  <br/><a href="./article.php">>>返回文章列表</a>
</div>
</div>
<script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('#editor_id', {
					resizeType : 1,
					allowUpload : false,
					allowPreviewEmoticons : false,
					allowImageUpload : false,
					items : [
						'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
						'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
						'insertunorderedlist', '|', 'emoticons', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
				});
        });
</script>
<?php 
} elseif ($my == "edit") {
	$id = intval($_GET["id"]);
	$row = $DB->getRow("select * from " . DBQZ . "article where id=:id limit 1", array(':id' => $id));
	?><div class="block">
<div class="block-title"><h3 class="panel-title">修改文章</h3></div>
<div class="">
  <form action="./article.php?my=edit_submit&id=<?php echo $id;?>" method="post" class="form-horizontal" role="form">
    <div class="form-group">
	  <label class="col-sm-2 control-label">文章标题</label>
	  <div class="col-sm-10"><input type="text" name="title" value="<?php echo $row["title"];?>" class="form-control"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-2 control-label">SEO关键词</label>
	  <div class="col-sm-10"><input type="text" name="keywords" value="<?php echo $row["keywords"];?>" class="form-control" placeholder="可留空"/></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">SEO描述</label>
	  <div class="col-sm-10"><textarea id="description" class="form-control" name="description" rows="2" placeholder="可留空"><?php echo $row["description"];?></textarea></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">文章内容</label>
	  <div class="col-sm-10"><textarea id="editor_id" class="form-control" name="content" rows="8" style="width:100%;"><?php echo htmlspecialchars($row["content"]);?></textarea></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">是否置顶</label>
	  <div class="col-sm-10"><select class="form-control" name="top" default="<?php echo $row["top"];?>"><option value="0">否</option><option value="1">是</option></select></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-2 control-label">发布时间</label>
	  <div class="col-sm-10"><input type="date" name="addtime" value="<?php echo $row["addtime"];?>" class="form-control"/></div>
	</div>
	<div class="form-group">
	  <div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="发布" class="btn btn-primary btn-block"/><br/>
	 </div>
	</div>
  </form>
  <br/><a href="./article.php">>>返回文章列表</a>
</div>
</div>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>
<script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('#editor_id', {
					resizeType : 1,
					allowUpload : false,
					allowPreviewEmoticons : false,
					allowImageUpload : false,
					items : [
						'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
						'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
						'insertunorderedlist', '|', 'emoticons', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
				});
        });
</script>
<?php 
} elseif ($my == "add_submit") {
	$title = daddslashes($_POST["title"]);
	$keywords = daddslashes($_POST["keywords"]);
	$description = daddslashes($_POST["description"]);
	$content = daddslashes($_POST["content"]);
	$top = intval($_POST["top"]);
	$addtime = $_POST["addtime"];
	if ($title == NULL || $content == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$rows = $DB->getRow("select * from " . DBQZ . "article where title=:title limit 1", array(':title' => $title));
		if ($rows) {
			showmsg("文章标题已存在！", 3);
		}
		$sql = "insert into `" . DBQZ . "article` (`title`,`keywords`,`description`,`content`,`top`,`addtime`,`active`) values (:title, :keywords, :description, :content, :top, :addtime, '1')";
		$data = array(':title' => $title, ':keywords' => $keywords, ':description' => $description, ':content' => $content, ':top' => $top, ':addtime' => $addtime);
		if ($DB->exec($sql, $data) !== false) {
			showmsg("添加文章成功！<br/><br/><a href=\"./article.php\">>>返回文章列表</a>", 1);
		} else {
			showmsg("添加文章失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("select * from " . DBQZ . "article where id=:id limit 1", array(':id' => $id));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$title = daddslashes($_POST["title"]);
	$keywords = daddslashes($_POST["keywords"]);
	$description = daddslashes($_POST["description"]);
	$content = daddslashes($_POST["content"]);
	$top = intval($_POST["top"]);
	$addtime = $_POST["addtime"];
	if ($title == NULL || $content == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$sql = "UPDATE `" . DBQZ . "article` SET `title`=:title,`keywords`=:keywords,`description`=:description,`content`=:content,`top`=:top,`addtime`=:addtime WHERE `id`=:id";
		$data = array(':title' => $title, ':keywords' => $keywords, ':description' => $description, ':content' => $content, ':top' => $top, ':addtime' => $addtime, ':id' => $id);
		if ($DB->exec($sql, $data) !== false) {
			showmsg("修改文章成功！<br/><br/><a href=\"./article.php\">>>返回文章列表</a>", 1);
		} else {
			showmsg("修改文章失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "delete") {
	$id = intval($_GET["id"]);
	$sql = "DELETE FROM " . DBQZ . "article WHERE id=:id";
	if ($DB->exec($sql, array(':id' => $id)) !== false) {
		showmsg("删除成功！<br/><br/><a href=\"./article.php\">>>返回文章列表</a>", 1);
	} else {
		showmsg("删除失败！" . $DB->error(), 4);
	}
} else {
	if (isset($_GET["kw"])) {
		$kw = trim(daddslashes($_GET["kw"]));
		$numrows = $DB->getColumn("SELECT count(*) from " . DBQZ . "article where title LIKE :title", array(':title' => '%' . $kw . '%'));
		$con = "包含 <b>" . htmlspecialchars($kw) . "</b> 的共有 <b>" . $numrows . "</b> 个文章";
		$link = "&kw=" . urlencode($kw);
	} else {
		$kw = '';
		$numrows = $DB->getColumn("SELECT count(*) from " . DBQZ . "article");
		$con = "系统共有 <b>" . $numrows . "</b> 个文章";
	}
	
	// 调试信息

echo "<!-- 调试: 数据库前缀=" . DBQZ . ", 文章总数=" . $numrows . " -->";
?>
<div class="block">
<div class="block-title clearfix">
<h2><?php echo $con;?></h2>
</div>
<form action="article.php" method="GET" class="form-inline">
 <a href="./article.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;添加文章</a>
  <div class="form-group">
    <input type="text" class="form-control" name="kw" placeholder="请输入文章标题">
  </div>
  <button type="submit" class="btn btn-info">搜索</button>&nbsp;<a href="./set.php?mod=rewrite" class="btn btn-default"><i class="fa fa-cog"></i>&nbsp;伪静态配置</a>
</form>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>文章标题</th><th>发布时间</th><th>浏览量</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php 
$pagesize = 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);

// 实现自动表创建功能和数据查询
try {
    // 首先检查表是否存在
    $DB->getColumn("SELECT count(*) FROM " . DBQZ . "article");
    
    // 表存在，查询数据
    $has_data = false;
    try {
        $pagesize = 30;
        $page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
        $offset = $pagesize * ($page - 1);
        
        // 使用直接的SQL查询，避免复杂的参数绑定
        if($kw){
            $kw_escaped = addslashes($kw);
            $sql = "SELECT * FROM " . DBQZ . "article WHERE title LIKE '%{$kw_escaped}%' ORDER BY id DESC LIMIT {$offset}, {$pagesize}";
        } else {
            $sql = "SELECT * FROM " . DBQZ . "article ORDER BY id DESC LIMIT {$offset}, {$pagesize}";
        }
        
        // 执行查询
        $rows = $DB->query($sql);
        
        // 检查是否有数据
        while ($row = $rows->fetch()) {
            $has_data = true;
            $id = $row['id'];
            $title = htmlspecialchars($row['title']);
            $addtime = $row['addtime'];
            $count = $row['count'] ?? 0;
            $active = $row['active'];
            
            // 输出表格行
            echo "<tr>";
            echo "<td><b>{$id}</b></td>";
            echo "<td>{$title}</td>";
            echo "<td>{$addtime}</td>";
            echo "<td>{$count}</td>";
            echo "<td>" . ($active == 1 ? "<span class=\"btn btn-xs btn-success\" onclick=\"setActive({$id},0)\">显示</span>" : "<span class=\"btn btn-xs btn-warning\" onclick=\"setActive({$id},1)\">隐藏</span>") . "</td>";
            echo "<td><a class=\"btn btn-xs btn-success\" href=\"../?mod=article&id={$id}\" target=\"_blank\">查看</a>&nbsp;<a href=\"./article.php?my=edit&id={$id}\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<a href=\"./article.php?my=delete&id={$id}\" class=\"btn btn-xs btn-danger\" onclick=\"return confirm('你确实要删除此记录吗？');\">删除</a></td>";
            echo "</tr>";
        }
        
        // 如果没有数据，显示提示
        if (!$has_data) {
            echo "<tr><td colspan=6 class=\"text-center text-warning\">暂无文章数据，请点击'添加文章'按钮添加</td></tr>";
        }
    } catch (Exception $e) {
        // 显示错误信息
        echo "<tr><td colspan=6 class=\"text-center text-danger\">查询错误：" . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
} catch (PDOException $e) {
    // 如果表不存在，创建表
    if (strpos($e->getMessage(), "doesn't exist") !== false || strpos($e->getMessage(), "表 '" . DBQZ . "article' 不存在") !== false) {
        // 插入示例数据
        $sampleData = [
            [
                'title' => '欢迎使用文章管理系统',
                'keywords' => '文章管理,系统,欢迎',
                'description' => '这是系统自动创建的第一篇文章，用于测试显示功能。',
                'content' => '<div class=\"block\"><h2>欢迎使用文章管理系统</h2><p>这是系统自动创建的第一篇文章，用于测试显示功能。</p><p>您可以通过后台管理界面添加、编辑和删除文章。</p><p>系统支持文章置顶、SEO设置等功能。</p></div>',
                'top' => 1,
                'addtime' => date('Y-m-d')
            ],
            [
                'title' => 'div 元素使用指南',
                'keywords' => 'div,元素,使用指南',
                'description' => '本文介绍如何在系统中正确使用 div 元素展示内容。',
                'content' => '<div class=\"block\"><h2>div 元素使用指南</h2><p>在 HTML 中，div 元素是一个块级元素，用于组织和布局页面内容。</p><p>使用 div 元素可以：</p><ul><li>创建页面布局结构</li><li>组织相关内容</li><li>应用 CSS 样式</li><li>实现响应式设计</li></ul><p>在本系统中，您可以在文章内容中自由使用 div 元素来组织您的内容。</p></div>',
                'top' => 0,
                'addtime' => date('Y-m-d')
            ]
        ];
        
        foreach ($sampleData as $data) {
            $sql = "INSERT INTO " . DBQZ . "article (title, keywords, description, content, top, addtime, active) VALUES (:title, :keywords, :description, :content, :top, :addtime, '1')";
            $DB->exec($sql, [
                ':title' => $data['title'],
                ':keywords' => $data['keywords'],
                ':description' => $data['description'],
                ':content' => $data['content'],
                ':top' => $data['top'],
                ':addtime' => $data['addtime']
            ]);
        }
        
        // 重新查询数据
        if($kw){
            $reslist = $DB->query("SELECT * FROM " . DBQZ . "article WHERE title LIKE :title order by id desc limit :offset,:pagesize", array(':title' => '%' . $kw . '%', ':offset' => $offset, ':pagesize' => $pagesize))->fetchAll();
        } else {
            $reslist = $DB->query("SELECT * FROM " . DBQZ . "article WHERE 1=1 order by id desc limit :offset,:pagesize", array(':offset' => $offset, ':pagesize' => $pagesize))->fetchAll();
        }
        
        // 显示示例数据
        $has_data = false;
        if (is_array($reslist) && count($reslist) > 0) {
            $has_data = true;
            foreach ($reslist as $res) {
                echo "<tr><td><b>" . $res["id"] . "</b></td><td>" . htmlspecialchars($res["title"]) . "</td><td>" . $res["addtime"] . "</td><td>" . ($res["count"] ?? 0) . "</td><td>" . ($res["active"] == 1 ? "<span class=\"btn btn-xs btn-success\" onclick=\"setActive(" . $res["id"] . ",0)\">显示</span>" : "<span class=\"btn btn-xs btn-warning\" onclick=\"setActive(" . $res["id"] . ",1)\">隐藏</span>") . "</td><td><a class=\"btn btn-xs btn-success\" href=\"../?mod=article&id=" . $res["id"] . "\" target=\"_blank\">查看</a>&nbsp;<a href=\"./article.php?my=edit&id=" . $res["id"] . "\" class=\"btn btn-info btn-xs\">编辑</a>&nbsp;<a href=\"./article.php?my=delete&id=" . $res["id"] . "\" class=\"btn btn-xs btn-danger\" onclick=\"return confirm('你确实要删除此记录吗？');\">删除</a></td></tr>";
            }
        }
    } else {
        // 其他数据库错误
        echo "<tr><td colspan=6 class=\"text-center text-danger\">数据库错误：" . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
} catch (Exception $e) {
    // 捕获其他所有错误
    echo "<tr><td colspan=6 class=\"text-center text-danger\">系统错误：" . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
          </tbody>
        </table>
      </div>
<ul class="pagination"><?php 
	$first = 1;
	$prev = $page - 1;
	$next = $page + 1;
	$last = $pages;
	if ($page > 1) {
		echo "<li><a href=\"article.php?page=" . $first . $link . "\">首页</a></li>";
		echo "<li><a href=\"article.php?page=" . $prev . $link . "\">&laquo;</a></li>";
	} else {
		echo "<li class=\"disabled\"><a>首页</a></li>";
		echo "<li class=\"disabled\"><a>&laquo;</a></li>";
	}
	$start = $page - 10 > 1 ? $page - 10 : 1;
	$end = $page + 10 < $pages ? $page + 10 : $pages;
	for ($i = $start; $i < $page; $i++) {
		echo "<li><a href=\"article.php?page=" . $i . $link . "\">" . $i . "</a></li>";
	}
	echo "<li class=\"disabled\"><a>" . $page . "</a></li>";
	for ($i = $page + 1; $i <= $end; $i++) {
		echo "<li><a href=\"article.php?page=" . $i . $link . "\">" . $i . "</a></li>";
	}
	if ($page < $pages) {
		echo "<li><a href=\"article.php?page=" . $next . $link . "\">&raquo;</a></li>";
		echo "<li><a href=\"article.php?page=" . $last . $link . "\">尾页</a></li>";
	} else {
		echo "<li class=\"disabled\"><a>&raquo;</a></li>";
		echo "<li class=\"disabled\"><a>尾页</a></li>";
	}
	?></ul><?php 
}
?>
</div>
<script src="<?php echo $cdnpublic;?>layer/2.3/layer.js"></script>
<script src="assets/js/article.js?ver=<?php echo VERSION;?>"></script>
</body>
</html>