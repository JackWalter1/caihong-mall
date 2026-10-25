<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo <<<'EOT'
{"code": 1, "content": "彩虹云商城倡导绿色合规交易，保障买卖绿色、便捷、合法一直是我们的初衷，为积极响应落实《<a href=\"http://www.gov.cn/xinwen/2016-11/07/content_5129723.htm\" target=\"_blank\" rel=\"noopener noreferrer\" style=\"color: #409EFF; text-decoration: none;\">中华人民共和国网络安全法</a>》，彩虹云商城将依据《<a href=\"./?mod=server-agreement\" target=\"_blank\" style=\"color: #409EFF; text-decoration: none;\">平台服务协议</a>》对用户行为进行规范管理，同时为保障风控数据的公正、公开、透明，我们特此将平台风控数据作出公示。"}
EOT;
