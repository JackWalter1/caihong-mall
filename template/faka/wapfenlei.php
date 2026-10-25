<?php 
if(!defined('IN_CRONLITE'))exit();

$data=$DB->getAll("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$count = count($data);
include_once TEMPLATE_ROOT.'faka/inc/waphead.php';
?>
<style type="text/css">
/* 重写分类样式 */
.category-container {
    padding: 10px;
    background-color: #f5f5f5;
}
.category-title {
    background-color: #ffffff;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    font-weight: bold;
    text-align: center;
}
.category-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    background-color: #ffffff;
    padding: 10px;
    border-radius: 5px;
}
.category-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    background-color: #ffffff;
    transition: all 0.3s ease;
    box-sizing: border-box;
}
.category-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
.category-item img {
    width: 60px;
    height: 60px;
    margin-bottom: 8px;
    object-fit: contain;
}
.category-item span {
    font-size: 12px;
    color: #333;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    max-height: 31px;
    word-break: break-word;
}
.category-item.all {
    background-color: #f0f8ff;
}
/* 响应式调整 */
@media (max-width: 640px) {
    .category-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 480px) {
    .category-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .category-item img {
        width: 50px;
        height: 50px;
    }
}
</style>

<div style="height: 50px"></div>

<?php if($conf['search_open']==1){?>
<div class="menux" style="background-color: #ffffff;">
  <form action="?" method="get"><input type="hidden" name="mod" value="wapso"/>
    <input name="kw" type="text" class="search_input" placeholder="请输入您要查询的商品名称关键词" required>
    <input type="submit" class="search_submit" style="background-color: #f44530" value="商品搜索">
  </form>
</div>
<?php }?>

<div class="category-container">
  <div class="category-title">商品分类</div>
  <div class="category-grid">
    <!-- 全部商品 -->
    <a href="./" class="category-item all">
      <img src="assets/faka/images/fenleitubiao.png" onerror="this.src='assets/faka/images/fenleitubiao.png'">
      <span>全部商品</span>
    </a>
    <!-- 分类列表 -->
    <?php foreach($data as $row){ ?>
    <a href="./?cid=<?php echo $row['cid'] ?>" class="category-item">
      <img src="<?php echo $row['shopimg'] ?>" onerror="this.src='assets/faka/images/fenleitubiao.png'">
      <span><?php echo $row['name'] ?></span>
    </a>
    <?php }?>
  </div>
</div>

<?php include TEMPLATE_ROOT.'faka/inc/wapfoot.php';?>
</body>
</html>