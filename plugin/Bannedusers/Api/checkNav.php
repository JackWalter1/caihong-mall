<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo <<<'EOT'
{"code": 1, "status": {"index": true, "complaints": true, "bannedRecords": true}, "menus": [{"id": 1, "name": "网站首页", "href": "./", "target": 0, "sort": 0}, {"id": 3, "name": "禁售目录", "href": "./?mod=goods-release", "target": 0, "sort": 0}, {"id": 4, "name": "最新公告", "href": "./?mod=notice", "target": 0, "sort": 0}, {"id": 5, "name": "公司介绍", "href": "./?mod=about", "target": 1, "sort": 0}, {"id": 6, "name": "演示店铺", "href": "./?mod=shop", "target": 0, "sort": 0}, {"id": 8, "name": "黑名单查询", "href": "javascript:;", "target": 0, "sort": 0, "subMenus": [{"id": 9, "name": "风控名单", "href": "./?mod=riskcontrol", "target": 0, "sort": 2}, {"id": 10, "name": "商家投诉率", "href": "./?mod=complaints", "target": 0, "sort": 1}, {"id": 11, "name": "买家黑名单", "href": "./?mod=bannedrecords", "target": 0, "sort": 0}]}], "parentMenu": {"id": 8, "name": "黑名单查询", "subMenus": [{"name": "风控名单", "href": "./?mod=riskcontrol", "sort": 2}, {"name": "商家投诉率", "href": "./?mod=complaints", "sort": 1}, {"name": "买家黑名单", "href": "./?mod=bannedrecords", "sort": 0}]}}
EOT;
