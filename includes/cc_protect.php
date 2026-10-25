<?php
// ============================================================
// 防CC攻击核心函数库（admin/cc_protect.php 依赖）
// 与 security_hardening.php 共用 pre_cc_ban / pre_cc_log 表
// ============================================================

if (defined('CC_PROTECT_LOADED')) {
    return;
}
define('CC_PROTECT_LOADED', true);

// 建表（初始化）
function setup_cc_protect()
{
    setup_security_tables();
}

// 手动封禁IP
function ban_ip_manually($ip, $reason = '手动封禁', $hours = 24)
{
    return sec_ban_ip($ip, $reason, $hours);
}

// 自动封禁（记录访问日志）
function ban_ip($ip, $reason = 'CC攻击自动封禁', $hours = 24)
{
    global $DB;
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return false;
    }
    $DB->query("INSERT INTO pre_cc_log (ip,url,log_time) VALUES (:ip,:url,:time)", array(':ip' => $ip, ':url' => isset($_SERVER['REQUEST_URI']) ? substr($_SERVER['REQUEST_URI'], 0, 500) : '', ':time' => time()));
    return sec_ban_ip($ip, $reason, $hours);
}

// 解除封禁
function unban_ip($ip)
{
    sec_unban_ip($ip);
}

// 清理过期封禁
function clean_cc_ban_list()
{
    global $DB;
    $DB->query("DELETE FROM pre_cc_ban WHERE expire_time > 0 AND expire_time < :now", array(':now' => time()));
}

// 统计信息
function get_cc_attack_stats()
{
    global $DB;
    $now = time();
    $today_start = strtotime(date('Y-m-d 00:00:00'));
    $current_bans = intval($DB->getColumn("SELECT COUNT(*) FROM pre_cc_ban WHERE (expire_time=0 OR expire_time>:now)", array(':now' => $now)));
    $today_blocks = intval($DB->getColumn("SELECT COUNT(*) FROM pre_cc_log WHERE log_time>:today", array(':today' => $today_start)));
    return array('current_bans' => $current_bans, 'today_blocks' => $today_blocks);
}

// 检查IP是否被封禁（供common.php等入口调用）
function check_ip_banned()
{
    sec_check_ip_ban();
}

// 分页函数（admin/cc_protect.php 依赖）
function page($total, $pageSize = 10, $page = 1, $link = '', $half = 4)
{
    $pages = intval(ceil(intval($total) / intval($pageSize)));
    if ($pages <= 1) {
        return '';
    }
    $page = max(1, min($pages, intval($page)));
    $html = '<ul class="pagination pagination-sm">';
    $html .= '<li><a href="' . $link . '1">&laquo;</a></li>';
    for ($i = max(1, $page - $half); $i <= min($pages, $page + $half); $i++) {
        if ($i == $page) {
            $html .= '<li class="active"><span>' . $i . '</span></li>';
        } else {
            $html .= '<li><a href="' . $link . $i . '">' . $i . '</a></li>';
        }
    }
    $html .= '<li><a href="' . $link . $pages . '">&raquo;</a></li>';
    $html .= '</ul>';
    return $html;
}
