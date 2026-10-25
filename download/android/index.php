<?php
// Android 安装包下载目录：把 .apk 文件放进本文件夹即可自动出现在列表中
$files = array();
foreach (scandir(__DIR__) as $f) {
    if ($f === '.' || $f === '..') continue;
    if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) !== 'apk') continue;
    $path = __DIR__ . '/' . $f;
    if (!is_file($path)) continue;
    $files[] = array('name' => $f, 'size' => filesize($path), 'time' => filemtime($path));
}
usort($files, function ($a, $b) {
    return $b['time'] - $a['time'];
});
$latest = count($files) ? $files[0] : null;

function apk_size($bytes) {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}
function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Android 版下载 - 彩虹云商城商家端</title>
<style>
    * { box-sizing: border-box; }
    body { margin: 0; padding: 0 16px 40px; background: #F5F8FC; color: #1A2A48; font-family: "Microsoft YaHei", "PingFang SC", Arial, sans-serif; font-size: 14px; line-height: 1.65; }
    .wrap { width: min(100%, 760px); margin: 0 auto; }
    .head { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 22px 0 18px; border-bottom: 1px solid #E6EAF0; }
    .head img { height: 30px; width: auto; display: block; }
    .head a { color: #155EEF; text-decoration: none; font-size: 13px; }
    .head a:hover { text-decoration: underline; }
    .card { margin-top: 24px; padding: 28px; border: 1px solid #E6EAF0; border-radius: 14px; background: #fff; box-shadow: 0 10px 28px rgba(18,35,66,.06); }
    .card h1 { margin: 0 0 6px; font-size: 22px; }
    .sub { margin: 0 0 22px; color: #6B7A90; font-size: 13px; }
    .meta { display: flex; flex-wrap: wrap; gap: 8px 20px; margin-bottom: 22px; color: #6B7A90; font-size: 13px; }
    .meta b { color: #1A2A48; font-weight: 700; }
    .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 48px; padding: 0 26px; border-radius: 8px; color: #fff; background: #155EEF; text-decoration: none; font-size: 15px; font-weight: 700; }
    .btn:hover { background: #0F4BD8; }
    .empty { padding: 36px 0; text-align: center; color: #6B7A90; }
    .empty strong { display: block; margin-bottom: 6px; color: #1A2A48; font-size: 16px; }
    .list-title { margin: 26px 0 10px; font-size: 14px; font-weight: 700; }
    .list { margin: 0; padding: 0; list-style: none; }
    .list li { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 13px 0; border-top: 1px solid #F0F3F7; }
    .list li:first-child { border-top: 0; }
    .list .n { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .list .s { flex: 0 0 auto; color: #6B7A90; font-size: 12px; }
    .list .d { flex: 0 0 auto; color: #155EEF; text-decoration: none; font-size: 13px; font-weight: 700; }
    .foot { margin-top: 22px; color: #9AA6B8; font-size: 12px; text-align: center; }
    @media (max-width: 560px) {
        .card { padding: 20px; }
        .list li { flex-wrap: wrap; gap: 4px 12px; }
        .list .n { width: 100%; }
    }
</style>
</head>
<body>
<div class="wrap">
    <div class="head">
        <img src="/template/XHY-01/assets/img/logo-app.png" alt="彩虹云商城">
        <a href="/?mod=download">返回下载页</a>
    </div>

    <div class="card">
        <h1>Android 版安装包</h1>
        <p class="sub">下载后直接在手机上安装，安装前请在系统设置中允许安装未知来源应用。</p>

        <?php if ($latest) { ?>
        <div class="meta">
            <span>最新版本：<b><?php echo h($latest['name']); ?></b></span>
            <span>文件大小：<b><?php echo apk_size($latest['size']); ?></b></span>
            <span>更新时间：<b><?php echo date('Y-m-d H:i', $latest['time']); ?></b></span>
        </div>
        <a class="btn" href="<?php echo rawurlencode($latest['name']); ?>" download>立即下载 APK</a>
        <?php } else { ?>
        <div class="empty">
            <strong>安装包正在上传中</strong>
            目前还没有可下载的 Android 安装包，请稍后再来。
        </div>
        <?php } ?>

        <?php if (count($files) > 1) { ?>
        <div class="list-title">历史版本</div>
        <ul class="list">
            <?php foreach ($files as $i => $file) { if ($i === 0) continue; ?>
            <li>
                <span class="n"><?php echo h($file['name']); ?></span>
                <span class="s"><?php echo apk_size($file['size']); ?> · <?php echo date('Y-m-d', $file['time']); ?></span>
                <a class="d" href="<?php echo rawurlencode($file['name']); ?>" download>下载</a>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>
    </div>

    <div class="foot">彩虹云商城商家端 · Android 安装包由本站服务器直接提供</div>
</div>
</body>
</html>
