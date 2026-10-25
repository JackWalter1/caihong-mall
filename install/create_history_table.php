<?php
/**
 * 创建价格历史记录表
 */
include('../includes/common.php');

// 创建历史记录表的SQL语句
$sql = "CREATE TABLE IF NOT EXISTS pre_price_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    zid INT UNSIGNED NOT NULL,
    price_data TEXT NOT NULL,
    description VARCHAR(255) NOT NULL,
    create_time DATETIME NOT NULL,
    FOREIGN KEY (zid) REFERENCES pre_site(zid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

try {
    $DB->exec($sql);
    echo "价格历史记录表创建成功！";
} catch (Exception $e) {
    echo "创建表失败：" . $e->getMessage();
}
?>