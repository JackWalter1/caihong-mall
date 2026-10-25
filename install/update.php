<?php
$install = true;
require_once('../includes/common.php');
@header('Content-Type: text/html; charset=UTF-8');
if($conf['lt_version'] < 1011){
	$sqls = file_get_contents('update.sql');
	$version = 1011;
}elseif($conf['lt_version'] < 1012){
	$sqls = file_get_contents('update2.sql');
	$version = 1012;
}elseif($conf['lt_version'] < 1013){
	$sqls = file_get_contents('update3.sql');
	$version = 1013;
}else{
	exit('你的网站已经升级到最新版本了');
}

/**
 * 将SQL文件内容拆分为单条语句。
 * 正确处理多行块注释、单行注释 -- / #、可执行注释 以及字符串/反引号内的分号。
 * @param string $sql
 * @return array
 */
function splitSqlStatements($sql)
{
	$statements = array();
	$buffer = '';
	$len = strlen($sql);
	$inSingle = false;
	$inDouble = false;
	$inBacktick = false;
	$inLineComment = false;
	$inBlockComment = false;

	for ($i = 0; $i < $len; $i++) {
		$c = $sql[$i];
		$next = ($i + 1 < $len) ? $sql[$i + 1] : '';

		if ($inLineComment) {
			if ($c === "\n" || $c === "\r") $inLineComment = false;
			continue;
		}
		if ($inBlockComment) {
			if ($c === '*' && $next === '/') {
				$inBlockComment = false;
				$i++;
			}
			continue;
		}
		if ($inSingle) {
			$buffer .= $c;
			if ($c === '\\' && $next !== '') { $buffer .= $next; $i++; }
			elseif ($c === "'") $inSingle = false;
			continue;
		}
		if ($inDouble) {
			$buffer .= $c;
			if ($c === '\\' && $next !== '') { $buffer .= $next; $i++; }
			elseif ($c === '"') $inDouble = false;
			continue;
		}
		if ($inBacktick) {
			$buffer .= $c;
			if ($c === '`') $inBacktick = false;
			continue;
		}

		if ($c === '-' && $next === '-') { $inLineComment = true; $i++; continue; }
		if ($c === '#') { $inLineComment = true; continue; }
		if ($c === '/' && $next === '*') {
			if ($i + 2 < $len && $sql[$i + 2] === '!') {
				// 可执行注释 /*! ... */ 原样保留（MySQL 会执行其中的语句）
				$end = strpos($sql, '*/', $i + 3);
				if ($end === false) $end = $len - 2;
				$buffer .= substr($sql, $i, $end + 2 - $i);
				$i = $end + 1;
				continue;
			}
			$inBlockComment = true;
			$i++;
			continue;
		}
		if ($c === "'") { $inSingle = true; $buffer .= $c; continue; }
		if ($c === '"') { $inDouble = true; $buffer .= $c; continue; }
		if ($c === '`') { $inBacktick = true; $buffer .= $c; continue; }
		if ($c === ';') {
			$stmt = trim($buffer);
			if ($stmt !== '') $statements[] = $stmt;
			$buffer = '';
			continue;
		}
		$buffer .= $c;
	}
	$stmt = trim($buffer);
	if ($stmt !== '') $statements[] = $stmt;
	return $statements;
}

$explode = splitSqlStatements($sqls);
foreach ($explode as $sql) {
	if ($sql = trim($sql)) {
		$DB->exec($sql);
	}
}
saveSetting('lt_version',$version);
$CACHE->clear();
exit("<script language='javascript'>alert('网站数据库升级完成！');window.location.href='../';</script>");
?>