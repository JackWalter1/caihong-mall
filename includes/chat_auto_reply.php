<?php


// 获取关键词自动回复配置
function getKeywordAutoReplies() {
    global $conf;
    $keywords = [];
    // 使用正确的配置键名
    if(!empty($conf['chat_keyword_enable']) && $conf['chat_keyword_enable'] == 1 && !empty($conf['chat_keywords'])) {
        // 按行分割配置文本
        $lines = explode("\n", $conf['chat_keywords']);
        foreach($lines as $line) {
            $line = trim($line);
            if(!empty($line)) {
                // 按照|分割关键词和回复内容
                $parts = explode('|', $line, 2);
                if(count($parts) == 2 && !empty($parts[0]) && !empty($parts[1])) {
                    $keywords[] = [
                        'keyword' => trim($parts[0]),
                        'reply' => trim($parts[1])
                    ];
                }
            }
        }
    }
    return $keywords;
}

// 获取违禁词配置
function getProhibitedWords() {
    global $conf;
    $words = [];
    // 检查违禁词功能是否开启
    if(!empty($conf['chat_prohibited_enable']) && $conf['chat_prohibited_enable'] == 1 && !empty($conf['chat_prohibited_words'])) {
        // 按行分割配置文本
        $lines = explode("\n", $conf['chat_prohibited_words']);
        foreach($lines as $line) {
            $word = trim($line);
            if(!empty($word)) {
                $words[] = $word;
            }
        }
    }
    return $words;
}

// 检查消息是否包含违禁词
function checkProhibitedWords($message) {
    global $conf;
    $prohibitedWords = getProhibitedWords();
    if(empty($prohibitedWords)) {
        return false; // 没有违禁词配置，允许发送
    }
    
    foreach($prohibitedWords as $word) {
        if(stripos($message, $word) !== false) {
            return true; // 包含违禁词
        }
    }
    return false; // 不包含违禁词
}

// 获取违禁提示语
function getProhibitedMessage() {
    global $conf;
    return !empty($conf['chat_prohibited_msg']) ? $conf['chat_prohibited_msg'] : '您的消息包含违禁内容，无法发送！';
}

// 关键词匹配函数
function matchKeyword($question, $keyword) {
    // 简单的包含匹配
    return stripos($question, $keyword) !== false;
}

// 获取自动回复内容（只支持关键词匹配）
function getAutoReply($question) {
    // 检查关键词匹配
    $keywordReplies = getKeywordAutoReplies();
    foreach($keywordReplies as $item) {
        if(matchKeyword($question, $item['keyword'])) {
            return [
                'reply' => $item['reply'],
                'link' => null
            ];
        }
    }
    
    // 如果没有匹配，返回null
    return null;
}
?>