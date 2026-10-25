<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo <<<'EOT'
{"code":1,"levels":[{"name":"高风险","color":"#F56C6C","content":"存在严重违规行为，已被系统永久封禁;发布违禁商品，已被系统永久封禁;存在欺诈行为，已被系统永久封禁","punishment":"依据《平台服务协议》第X条，【严重违规行为】相关规定，予以永久封禁处理","min_days":0,"max_days":99999},{"name":"中风险","color":"#E6A23C","content":"存在违规行为，需要进行整改;发布不合规商品，需要整改;商品描述不实，需要整改","punishment":"依据《平台服务协议》第X条，【一般违规行为】相关规定，暂停交易权限，整改后可恢复","min_days":0,"max_days":99999},{"name":"低风险","color":"#67C23A","content":"系统检测到轻微违规行为;商品信息不完整;商品价格异常","punishment":"依据《平台服务协议》第X条，【轻微违规行为】相关规定，限制部分功能，整改后解除限制","min_days":0,"max_days":99999}]}
EOT;
