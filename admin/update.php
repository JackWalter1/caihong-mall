<?php
/**
 * 系统更新模块
 * 功能：检查更新 + 一键在线升级（自动下载升级包、备份被覆盖文件、解压覆盖、执行SQL）
 */
include "../includes/common.php";

// 升级服务器地址回退：兼容旧版本未配置 UPDATE_API_URL 的情况
if (!defined('UPDATE_API_URL') || UPDATE_API_URL === '') {
    define('UPDATE_API_URL', 'https://caihong.wnttech.fun/update-server/');
}

// 当前版本
$currentVersion = defined('VERSION') ? VERSION : '1.60';
$updateApiConfigured = defined('UPDATE_API_URL') && UPDATE_API_URL !== '';

$title = "检查更新";

// 验证用户登录状态
if ($islogin == 1) {
    // 登录成功，继续执行
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

// 引入头部文件
include "./head.php";
?>
<style>
.upd-card { background:#f8f9fa; border:1px solid #e5e9ec; border-radius:8px; padding:20px; margin-bottom:15px; }
.upd-version-big { font-size:36px; font-weight:700; color:#2c3e50; line-height:1.2; }
.upd-sub { color:#888; font-size:13px; margin-top:5px; }
.upd-changelog { background:#fff; border:1px solid #e5e9ec; border-radius:6px; padding:12px 12px 12px 32px; margin:10px 0 0 0; }
.upd-changelog li { margin-bottom:5px; color:#555; }
.upd-log { background:#2b303b; color:#c0dcc0; font-family:Consolas,monospace; font-size:12px; border-radius:6px; padding:12px; max-height:180px; overflow-y:auto; margin-top:12px; display:none; }
.upd-log div { margin-bottom:3px; }
#updProgress { display:none; margin-top:15px; }
.progress { height:20px; margin-bottom:5px; }
.btn-online-upgrade { background:#2ecc71; border-color:#2ecc71; color:#fff; font-weight:600; padding:10px 28px; font-size:15px; border-radius:6px; }
.btn-online-upgrade:hover { background:#27ae60; border-color:#27ae60; color:#fff; }
</style>
<div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
    <div class="block">
        <div class="block-title clearfix">
            <h3 class="panel-title">系统更新</h3>
            <div class="version-info" style="float:right; font-size:14px; color:#666; line-height:38px; margin-right:15px;">
                当前版本: <strong id="curVerText">V<?php echo $currentVersion; ?></strong>
            </div>
        </div>
        <div class="panel-body">

            <div class="row" style="margin-bottom:15px;">
                <div class="col-xs-12 col-sm-5">
                    <div class="upd-card" style="text-align:center;">
                        <div class="upd-sub">当前版本</div>
                        <div class="upd-version-big" id="curVerBig">V<?php echo $currentVersion; ?></div>
                        <div class="upd-sub"><?php echo $updateApiConfigured ? '升级服务器已配置' : '<span style="color:#e67e22">未配置升级服务器</span>'; ?></div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-7">
                    <div class="upd-card" id="latestCard" style="display:none;">
                        <div class="upd-sub">检测到新版本</div>
                        <div style="font-size:22px; font-weight:700; color:#2ecc71;" id="latestVerText"></div>
                        <div class="upd-sub" id="latestTime"></div>
                        <ul class="upd-changelog" id="latestChangelog"></ul>
                    </div>
                    <div class="upd-card" id="noUpdateCard" style="display:none;">
                        <div style="font-size:16px; font-weight:600; color:#2ecc71;"><i class="fa fa-check-circle"></i> 恭喜，你已经是最新版本！</div>
                        <div class="upd-sub">系统会定期发布新版本，可稍后再来检查。</div>
                    </div>
                </div>
            </div>

            <div id="updResult"></div>

            <div id="updProgress">
                <div class="progress">
                    <div class="progress-bar progress-bar-success progress-bar-striped active" id="updBar" style="width:0%;"></div>
                </div>
                <div style="font-size:12px; color:#888;" id="updStepText">准备中...</div>
            </div>
            <div class="upd-log" id="updLog"></div>

            <div style="margin-top:20px;">
                <button class="btn btn-primary" id="btnCheck"><i class="fa fa-refresh"></i> 检查更新</button>
                <button class="btn btn-online-upgrade" id="btnUpgrade" style="display:none;"><i class="fa fa-cloud-download"></i> 一键在线升级</button>
                <button class="btn btn-warning" id="btnForceUpgrade" style="display:none;"><i class="fa fa-cloud-download"></i> 重新升级当前版本</button>
                <a href="donate.php" class="btn btn-default"><i class="fa fa-history"></i> 查看更新日志</a>
            </div>

            <hr>
            <div class="alert alert-warning" style="margin-bottom:10px;">
                <i class="fa fa-exclamation-triangle"></i> <strong>升级须知：</strong>
                <ul style="margin:8px 0 0 18px;">
                    <li>在线升级前请先备份数据库（可在 数据库管理/宝塔面板 中导出）。</li>
                    <li>升级时被覆盖的旧文件会自动备份到 <code>includes/runtime/update_backup_时间/</code> 目录。</li>
                    <li><code>config.php</code>（数据库配置）、<code>includes/runtime/</code> 运行数据不会被覆盖。</li>
                    <li>如果升级包根目录带有 <code>upgrade.sql</code>，会自动执行数据库结构升级。</li>
                    <li>升级完成后建议刷新后台并检查网站前台是否正常。</li>
                </ul>
            </div>

            <h4>手动更新步骤（在线升级失败时使用）：</h4>
            <ol>
                <li>下载最新版本的压缩包</li>
                <li>备份当前网站文件和数据库</li>
                <li>将压缩包上传到网站根目录</li>
                <li>解压压缩包覆盖原有文件（注意不要覆盖 config.php）</li>
                <li>访问网站检查是否正常运行</li>
            </ol>
        </div>
    </div>
</div>
<script>
var pendingVersion = '';

function updLog(msg) {
    var $log = $('#updLog');
    $log.show().append('<div>' + msg + '</div>');
    $log.scrollTop($log[0].scrollHeight);
}

function updSetProgress(pct, text) {
    $('#updBar').css('width', pct + '%');
    $('#updStepText').text(text);
}

function updResult(html, type) {
    $('#updResult').html('<div class="alert alert-' + (type || 'info') + '">' + html + '</div>');
}

$('#btnCheck').click(function () {
    var $btn = $(this);
    $btn.prop('disabled', true).find('i').addClass('fa-spin');
    $('#latestCard, #noUpdateCard').hide();
    $('#btnUpgrade').hide();
    $('#updResult').empty();
    $.post('ajax_update.php?act=check', {}, function (res) {
        $btn.prop('disabled', false).find('i').removeClass('fa-spin');
        if (res.code != 0) { updResult('<i class="fa fa-times-circle"></i> ' + res.msg, 'danger'); return; }
        if (res.update == 1) {
            pendingVersion = res.version;
            $('#latestVerText').text(res.name);
            $('#latestTime').text(res.time ? '发布时间：' + res.time : '');
            var cl = $('#latestChangelog').empty();
            if (res.changelog && res.changelog.length) {
                $.each(res.changelog, function (i, v) { cl.append('<li>' + v + '</li>'); });
                cl.show();
            } else { cl.hide(); }
            $('#latestCard').show();
            $('#btnUpgrade').show();
            $('#btnForceUpgrade').hide();
            updResult('<i class="fa fa-info-circle"></i> 发现新版本 <strong>' + res.name + '</strong>，点击"一键在线升级"自动完成升级。', 'info');
        } else {
            $('#noUpdateCard').show();
            $('#btnForceUpgrade').show();
            updResult('<i class="fa fa-check-circle"></i> 当前已是最新版本 V' + res.current + '。如需重新安装此版本，点击"重新升级当前版本"。', 'success');
        }
    }, 'json').fail(function () {
        $btn.prop('disabled', false).find('i').removeClass('fa-spin');
        updResult('<i class="fa fa-times-circle"></i> 请求失败，请刷新页面重试', 'danger');
    });
});

function doUpgrade(isForce) {
    if (!pendingVersion && !isForce) return;
    var tip = isForce
        ? '确定要重新安装当前版本吗？\n\n此操作会重新下载升级包并覆盖文件，但会自动备份旧文件。'
        : '确定要在线升级到 ' + pendingVersion + ' 吗？\n\n升级过程会自动备份被覆盖的旧文件，但强烈建议先备份数据库！';
    if (!confirm(tip)) return;
    var $btn = isForce ? $('#btnForceUpgrade') : $('#btnUpgrade');
    $btn.prop('disabled', true);
    $('#btnCheck').prop('disabled', true);
    $('#updLog').empty().hide();
    $('#updProgress').show();
    updSetProgress(5, '正在连接升级服务器...');

    var forceParam = isForce ? '&force=1' : '';

    // 第一步：下载升级包
    $.post('ajax_update.php?act=download' + forceParam, {}, function (res) {
        if (res.code != 0) { updSetProgress(100, '失败'); updLog('<span style="color:#ff8080">✘ ' + res.msg + '</span>'); $btn.prop('disabled', false); $('#btnCheck').prop('disabled', false); return; }
        updSetProgress(55, '升级包下载完成（' + res.size + ' MB），正在备份并覆盖文件...');
        updLog('<span style="color:#8fd08f">✔ ' + res.msg + '</span>');

        // 第二步：执行升级（备份->解压覆盖->SQL）
        $.post('ajax_update.php?act=upgrade' + forceParam, {}, function (res2) {
            if (res2.code != 0) {
                updSetProgress(100, '升级失败');
                updLog('<span style="color:#ff8080">✘ ' + res2.msg + '</span>');
                $btn.prop('disabled', false); $('#btnCheck').prop('disabled', false);
                return;
            }
            updSetProgress(100, '升级完成！');
            updLog('<span style="color:#8fd08f">✔ ' + res2.msg + '</span>');
            updLog('<span style="color:#8fd08f">✔ 旧文件备份位置：' + res2.backup + '</span>');
            updResult('<i class="fa fa-check-circle"></i> <strong>升级成功！</strong> 已升级到 <strong>V' + (res2.newversion || pendingVersion) + '</strong>。' +
                '<br><small>旧文件已备份到 ' + res2.backup + '，config.php 未被改动。3 秒后自动刷新页面...</small>', 'success');
            setTimeout(function () { location.reload(); }, 3000);
        }, 'json').fail(function () {
            updSetProgress(100, '请求失败');
            updLog('<span style="color:#ff8080">✘ 升级请求失败，可能是执行超时，请检查升级包是否已部分覆盖，必要时手动处理</span>');
            $btn.prop('disabled', false); $('#btnCheck').prop('disabled', false);
        });
    }, 'json').fail(function () {
        updSetProgress(100, '请求失败');
        updLog('<span style="color:#ff8080">✘ 下载请求失败，请重试</span>');
        $btn.prop('disabled', false); $('#btnCheck').prop('disabled', false);
    });
}

$('#btnUpgrade').click(function () { doUpgrade(false); });
$('#btnForceUpgrade').click(function () { doUpgrade(true); });
</script>
<?php
include './foot.php';
?>
