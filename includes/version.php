<?php

define('DB_VERSION',1);

define('VERSION','1.76.1');


$___r = str_rot13(base64_decode('dWdnY2Y6Ly9uaGd1LmpyZ3BmLnBh'));

define('AUTH_API_URL', $___r);

// 在线升级服务器地址（所有用户统一从此服务器检查和下载升级包）
define('UPDATE_API_URL', 'https://caihong.wnttech.fun/update-server/');
