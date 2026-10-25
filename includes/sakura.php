<?php
// 樱花效果加载文件
// 此文件可以在所有模板中使用

if(!defined('IN_CRONLITE'))exit();

// 樱花效果加载函数
function loadSakuraEffect() {
    global $conf, $cdnserver;
    
    if(isset($conf['sakura_enable']) && $conf['sakura_enable'] == 1) {
        echo "<!-- 樱花落叶效果 -->\n";
        echo "<script>\n";
        echo "// 樱花配置\n";
        echo "window.sakuraConfig = {\n";
        echo "    num: " . intval($conf['sakura_num'] ?? 21) . ",\n";
        echo "    limitTimes: " . intval($conf['sakura_limit_times'] ?? 2) . "\n";
        echo "};\n";
        echo "</script>\n";
        echo "<script src=\"" . $cdnserver . "assets/js/sakura.js\"></script>\n";
        echo "<script>\n";
        echo "// 页面加载完成后启动樱花效果\n";
        echo "$(document).ready(function() {\n";
        echo "    if(typeof startSakura === 'function') {\n";
        echo "        startSakura();\n";
        echo "    }\n";
        echo "});\n";
        echo "</script>\n";
    }
}

// 樱花效果加载函数（不依赖jQuery）
function loadSakuraEffectNoJQuery() {
    global $conf, $cdnserver;
    
    if(isset($conf['sakura_enable']) && $conf['sakura_enable'] == 1) {
        echo "<!-- 樱花落叶效果 -->\n";
        echo "<script>\n";
        echo "// 樱花配置\n";
        echo "window.sakuraConfig = {\n";
        echo "    num: " . intval($conf['sakura_num'] ?? 21) . ",\n";
        echo "    limitTimes: " . intval($conf['sakura_limit_times'] ?? 2) . "\n";
        echo "};\n";
        echo "</script>\n";
        echo "<script src=\"" . $cdnserver . "assets/js/sakura.js\"></script>\n";
        echo "<script>\n";
        echo "// 页面加载完成后启动樱花效果\n";
        echo "window.addEventListener('load', function() {\n";
        echo "    if(typeof startSakura === 'function') {\n";
        echo "        startSakura();\n";
        echo "    }\n";
        echo "});\n";
        echo "</script>\n";
    }
}

function loadChatWidget() {
    global $conf;
    if(isset($conf['chat_enable']) && $conf['chat_enable'] == 1) {
        echo "<!-- 客服组件 -->\n";
        include_once ROOT."template/default/chat/widget.php";
    }
}
?> 