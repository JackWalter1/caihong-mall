<?php
require_once 'includes/common.php';

// 初始化防墙引导页配置项
$configs = [
    'wall_guide_open' => ['type' => 'switch', 'value' => '0', 'name' => '防墙引导页开关', 'sort' => 1],
    'wall_guide_title' => ['type' => 'text', 'value' => '欢迎访问', 'name' => '引导页标题', 'sort' => 2],
    'wall_guide_content' => ['type' => 'textarea', 'value' => '点击下方按钮继续访问网站', 'name' => '引导页内容', 'sort' => 3],
    'wall_guide_btn' => ['type' => 'text', 'value' => '继续访问', 'name' => '按钮文字', 'sort' => 4],
    'wall_guide_interval' => ['type' => 'number', 'value' => '24', 'name' => '显示间隔', 'sort' => 5],
    'wall_guide_interval_unit' => ['type' => 'select', 'value' => 'hour', 'name' => '时间单位', 'sort' => 5.1],
    'wall_guide_theme_color' => ['type' => 'text', 'value' => '#2193b0', 'name' => '主题颜色', 'sort' => 6]
];

foreach($configs as $k => $v) {
    // 检查配置是否已存在
    $row = $DB->find('config', ['name' => $k]);
    if(!$row) {
        // 不存在则插入
        $DB->insert('config', [
            'name' => $k,
            'type' => $v['type'],
            'value' => $v['value'],
            'name_cn' => $v['name'],
            'sort' => $v['sort']
        ]);
        echo "已初始化配置项: $k\n";
    }
}

echo '防墙引导页配置项初始化完成！';
?>