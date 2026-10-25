<?php
require_once 'includes/common.php';
$conn = $DB;
$sql = "CREATE TABLE IF NOT EXISTS pre_domain_landing (
    id int(11) NOT NULL AUTO_INCREMENT,
    old_domain varchar(100) NOT NULL DEFAULT '',
    new_domain varchar(100) NOT NULL DEFAULT '',
    addtime int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    UNIQUE KEY old_domain (old_domain)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1";
if($conn->query($sql)){
    echo 'pre_domain_landing表创建成功！';
} else {
    echo '创建表失败：' . $conn->error;
}
?>