<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(404);
    die();
}
session_start();
require_once 'config_auth.php'; // 包含 $allowed_key 和 $open
$fk = $_SESSION['dashboard'] ?? '';
if($fk!=$fkkey){
    http_response_code(404);
    die();
}
// 接收数据
$tweetId = $_POST['tweet-id'] ?? '';

$otext = $_POST['otext'] ?? '';
$ttext = $_POST['ttext'] ?? '';
$sync = $_POST['synctox'] ?? false;


if(strlen($tweetId) > 8){
    $tweetId=substr($tweetId,1);
    //删除
    $result = deleteTweet($tweetId);
    if($result='success')
    header('Location:x.php');
   
}
elseif ($otext == '' && $ttext == '') {
    // 归档
    $result = archiveTweet($tweetId);
    if($result='success'){
        deleteTweet($tweetId);
        header('Location:x.php');
    }
}

else{
    //修改
    $result = editTweet($tweetId, $otext, $ttext, $sync);
    //if($result='success')      
        //header('Location:x.php');    
}


/**
 * 纯文本深度计数法删除tweet
 * @param string $tweetId 要删除的 tweet 的 data-tweet-id
 * @param string $filePath 目标文件路径
 * @return string 'success' 或 'error'
 */
function deleteTweet($tweetId) {
    $filePath = 'x.php';
    if (empty($tweetId) || !is_readable($filePath) || !is_writable($filePath)) {
        return 'error';
    }
    $content = file_get_contents($filePath);
    if ($content === false) return 'error';

    $len = strlen($content);
    $pos = 0;

    // 找到第一个匹配的 <div class=... tweet ... data-tweet-id="xxx" ...> 起始位置
    while (($divPos = stripos($content, '<div', $pos)) !== false) {
        $tagEnd = findTagEnd($content, $divPos);
        if ($tagEnd === false) break;

        $tagStr = substr($content, $divPos, $tagEnd - $divPos + 1);
        if (preg_match('/\bclass\s*=\s*(["\'])(.*?)\1/i', $tagStr, $mClass)
            && preg_match('/\btweet\b/i', $mClass[2])
            && preg_match('/\bdata-tweet-id\s*=\s*(["\'])' . preg_quote($tweetId, '/') . '\1/i', $tagStr)) {

            // 找到了起始标签
            $startPos = $divPos;
            $depth = 1;
            $cursor = $tagEnd + 1;

            // 深度计数找闭合
            while ($cursor < $len) {
                // 查找下一个 <div 或 </div>
                $nextOpen = stripos($content, '<div', $cursor);
                $nextClose = stripos($content, '</div>', $cursor);

                if ($nextOpen === false && $nextClose === false) {
                    // 没有闭合标签了，结构异常，返回错误
                    return 'error';
                }

                if ($nextOpen !== false && $nextOpen < $nextClose) {
                    // 下一个标签是新的 <div>
                    $depth++;
                    $cursor = $nextOpen + 4;
                } else {
                    // 下一个标签是 </div>
                    $depth--;
                    $cursor = $nextClose + 6;
                    if ($depth === 0) {
                        // 找到闭合位置
                        $endPos = $cursor;
                        // 删除范围是 [startPos, endPos)
                        $newContent = substr($content, 0, $startPos) . substr($content, $endPos);
                        if (file_put_contents($filePath, $newContent) === false) {
                            return 'error';
                        }
                        return 'success';
                    }
                }
            }
            // 如果循环结束都没找到闭合，返回 error
            return 'error';
        } else {
            $pos = $tagEnd + 1;
        }
    }
    return 'error'; // 没找到匹配的tweet
}

function archiveTweet($tweetId) {
    date_default_timezone_set('Asia/Shanghai');
    $fromFile = 'x.php';

    if (empty($tweetId) || !is_readable($fromFile)) {
        return 'error';
    }

    $fromContent = file_get_contents($fromFile);
    if ($fromContent === false) return 'error';

    $tweetBlock = extractTweetBlock($fromContent, $tweetId);
    if ($tweetBlock === false) return 'error';

    // 提取 tweet 时间
    if (!preg_match('/<span\s+class="time">\s*(\d{4})\/(\d{2})\/(\d{2})/', $tweetBlock, $m)) {
        return 'error';
    }

    $year  = substr($m[1], -2);
    $month = $m[2];
    $toFile = 'x' . $year . $month . '.php';

    // 如果归档文件不存在，不创建，直接退出
    if (!file_exists($toFile)) {
        return 'error';
    }

    $toContent = file_get_contents($toFile);
    if ($toContent === false) return 'error';

    $insertPos = strpos($toContent, '<!-- INSERT_HERE -->');
    if ($insertPos === false) return 'error';

    $insertPosEnd = $insertPos + strlen('<!-- INSERT_HERE -->');
    $before = substr($toContent, 0, $insertPosEnd);
    $after = substr($toContent, $insertPosEnd);

    $newContent = $before . "\n" . $tweetBlock . "\n" . $after;

    if (file_put_contents($toFile, $newContent) === false) {
        return 'error';
    }

    return 'success';
}


/**
 * 从内容中提取第一个匹配的tweet div块（带嵌套）
 * 返回完整div字符串，失败返回 false
 */
function extractTweetBlock($content, $tweetId) {
    $len = strlen($content);
    $pos = 0;

    while (($divPos = stripos($content, '<div', $pos)) !== false) {
        $tagEnd = findTagEnd($content, $divPos);
        if ($tagEnd === false) break;

        $tagStr = substr($content, $divPos, $tagEnd - $divPos + 1);

        if (preg_match('/\bclass\s*=\s*(["\'])(.*?)\1/i', $tagStr, $mClass)
            && preg_match('/\btweet\b/i', $mClass[2])
            && preg_match('/\bdata-tweet-id\s*=\s*(["\'])' . preg_quote($tweetId, '/') . '\1/i', $tagStr)) {

            $startPos = $divPos;
            $depth = 1;
            $cursor = $tagEnd + 1;

            while ($cursor < $len) {
                $nextOpen = stripos($content, '<div', $cursor);
                $nextClose = stripos($content, '</div>', $cursor);

                if ($nextOpen === false && $nextClose === false) {
                    return false;
                }

                if ($nextOpen !== false && ($nextOpen < $nextClose || $nextClose === false)) {
                    $depth++;
                    $cursor = $nextOpen + 4;
                } else {
                    $depth--;
                    $cursor = $nextClose + 6;
                    if ($depth === 0) {
                        $endPos = $cursor;
                        return substr($content, $startPos, $endPos - $startPos);
                    }
                }
            }
            return false;
        }
        $pos = $tagEnd + 1;
    }
    return false;
}


/**
 * 找标签结束（考虑引号）
 */
function findTagEnd($s, $start) {
    $len = strlen($s);
    $inQuote = false;
    $quoteChar = '';
    for ($i = $start; $i < $len; $i++) {
        $ch = $s[$i];
        if (($ch === '"' || $ch === "'")) {
            if (!$inQuote) {
                $inQuote = true;
                $quoteChar = $ch;
            } elseif ($quoteChar === $ch) {
                $inQuote = false;
                $quoteChar = '';
            }
            continue;
        }
        if ($ch === '>' && !$inQuote) {
            return $i;
        }
    }
    return false;
}

function editTweet($tweetId, $otext, $ttext, $sync) {
    // 初始化变量，避免未定义错误
    $textTwitter = '';
    $dResponse = '';
    $success = false;
    $msg = '';
    $tweetSent = '';
    $ttext = normalize_text($ttext);
    // 1. 同步到X.com的操作（仅当sync为true时执行）
    if ($sync) {
        // 验证同步文本
        if($textTwitter = sanitizeForX($ttext)){
            require_once('sendx.php');
            // 捕获同步输出
            ob_start();
            sendToX($textTwitter.' #microXBot');
            $dResponse = ob_get_clean();
            
            $success = true;
            $msg = 'Successfully synced and posted to X.com';
            $tweetSent = $textTwitter.' #microXBot';
        } else {
            $success = false;
            $msg = 'Failed to sync to X.com: Tweet format restricted.';
            // 同步失败时仍继续执行本地修改（如果需要同步失败也终止，这里可以return）
            // return; 
        }
    } else {
        // sync为false时，直接标记同步步骤跳过
        $success = true;
        $msg = 'Normal tweet mode enabled.';
    }

    // 2. 本地文件修改操作（无论sync是否为true，都执行）
    $filePath = 'x.php';
    // 检查文件操作可行性
    if (empty($tweetId) || !is_readable($filePath) || !is_writable($filePath)) {
        $success = false;
        $msg = 'Tweet failed in normal mode: Invalid tweet ID or file access error.';
    } else {
        // 读取文件内容
        $content = file_get_contents($filePath);
        if ($content === false) {
            $success = false;
            $msg = 'Tweet failed in normal mode: Could not read file.';
        } else {
            // 提取tweet块
            $tweetBlock = extractTweetBlock($content, $tweetId);
            if ($tweetBlock === false) {
                $success = false;
                $msg = 'Tweet failed in normal mode: Tweet not found.';
            } else {
                // 处理文本转义
                $otext = preg_replace_callback(
                    '#<(code|pre|kbd)>([\s\S]*?)</\1>#i',
                    function ($matches) {
                        $tagName = $matches[1];
                        $innerContent = $matches[2];
                        $escapedContent = htmlspecialchars($innerContent, ENT_QUOTES, 'UTF-8');
                        return "<{$tagName}>{$escapedContent}</{$tagName}>";
                    },
                    $otext
                );

                $ttext = preg_replace_callback(
                    '#<(code|pre|kbd)>([\s\S]*?)</\1>#i',
                    function ($matches) {
                        $tagName = $matches[1];
                        $innerContent = $matches[2];
                        $escapedContent = htmlspecialchars($innerContent, ENT_QUOTES, 'UTF-8');
                        return "<{$tagName}>{$escapedContent}</{$tagName}>";
                    },
                    $ttext
                );

                // 修改tweet内容
                $modifiedBlock = replaceTweetText($tweetBlock, $otext, $ttext);
                if ($modifiedBlock === false) {
                    $success = false;
                    $msg = 'Tweet failed in normal mode: Could not modify content.';
                } else {
                    // 保存修改
                    if (file_put_contents($filePath, str_replace($tweetBlock, $modifiedBlock, $content)) === false) {
                        $success = false;
                        $msg = 'Tweet failed in normal mode: Could not save file.';
                    } else {
                        // 本地修改成功（如果之前同步步骤没问题，则整体成功）
                        if ($success) {
                            $msg = $sync ? 'Tweet posted to X.com and in normal mode.' : 'Tweet posted successfully in normal mode.';
                        }
                        $success = true;
                    }
                }
            }
        }
    }

    // 3. 统一输出JSON（确保只输出一次）
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'msg' => $msg,
        'tweetSent' => $tweetSent, // 仅sync为true时有值
        'dResponse' => $dResponse  // 仅sync为true时有值
    ]);
}

/**
 * 替换tweet块中两个tweet-text div的全部内容（允许HTML）
 */
function replaceTweetText($tweetBlock, $otext, $ttext) {
    $pattern1 = '#(<div[^>]*class=["\'][^"\']*\btweet-text\b[^"\']*["\'][^>]*>)(.*?)(</div>)#is';
    if (!preg_match($pattern1, $tweetBlock, $m1)) {
        return false;
    }
    $newFirst = $m1[1] . $otext . $m1[3];

    $afterFirstPos = strpos($tweetBlock, $m1[0]) + strlen($m1[0]);
    $afterFirst = substr($tweetBlock, $afterFirstPos);

    $pattern2 = '#(<div[^>]*class=["\'][^"\']*\btweet-text\b[^"\']*\btrans\b[^"\']*["\'][^>]*>)(.*?)(</div>)#is';
    if (!preg_match($pattern2, $afterFirst, $m2)) {
        return false;
    }
    $newSecond = $m2[1] . $ttext . $m2[3];

    $afterFirstReplaced = substr_replace($afterFirst, $newSecond, strpos($afterFirst, $m2[0]), strlen($m2[0]));

    $newTweetBlock = substr($tweetBlock, 0, strpos($tweetBlock, $m1[0])) . $newFirst . $afterFirstReplaced;

    return $newTweetBlock;
}

function sanitizeForX($text) {
    // 固定最大长度
    $maxLength = 265; // 280 - 12

    // 1. 标签转换为换行
    // 在 <ol> 或 <ul> 前面补一个 /
    $text = preg_replace('/(?<!◆)\s*<(?:ol|ul)[^>]*>/i', '◆$0', $text);
    $text = str_ireplace(["</p>", "</li>"], "◆", $text);
    $text = preg_replace('/<br\s*\/?>/i', "◆", $text);

    // 2. 去掉所有 HTML 标签
    $text = strip_tags($text);

    // 3. 去掉多余空格和 Unicode 空白
    $text = trim(preg_replace('/[\p{Z}\t ]+/u', ' ', $text));

    // 4. 合并连续换行为一个换行
    $text = preg_replace("/\/+/", "/", $text);

    // 5. 去掉不可打印字符
    $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text);

    // 6. 补全不成对的引号
    $text = fixUnpairedQuotes($text);

    // 7. 按 X 规则截断，不截断单词
    $text = truncateForX($text, $maxLength);

    return $text;
}

function fixUnpairedQuotes($text) {
    $single = substr_count($text, "'");
    $double = substr_count($text, '"');

    if ($single % 2 !== 0) $text .= "'";
    if ($double % 2 !== 0) $text .= '"';

    return $text;
}

function truncateForX($text, $maxLength) {
    $urlPattern = '/\b(?:[a-z0-9-]+\.)+[a-z]{2,}(?:\/\S*)?\b/i';
    $offset = 0;
    $length = 0;
    $result = '';

    while (preg_match($urlPattern, $text, $match, PREG_OFFSET_CAPTURE, $offset)) {
        $url = $match[0][0];
        $urlPos = $match[0][1];

        // 前面非链接文本
        $before = mb_substr($text, $offset, $urlPos - $offset, 'UTF-8');
        $result = appendWords($result, $before, $maxLength, $length);

        if ($length >= $maxLength) return $result;

        // 链接固定 23 字符
        if ($length + 23 > $maxLength) {
            $result .= '...';
            return $result;
        }
        $result .= $url;
        $length += 23;

        $offset = $urlPos + strlen($url);
    }

    // 余下部分
    $rest = mb_substr($text, $offset, null, 'UTF-8');
    $result = appendWords($result, $rest, $maxLength, $length);

    return $result;
}

function appendWords($result, $text, $maxLength, &$length) {
    // 按空格分割单词，同时保留空格
    $words = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($words as $word) {
        $wordLen = mb_strlen($word, 'UTF-8');

        // 如果整词放不下就停止，不截断
        if ($length + $wordLen > $maxLength) {
            if ($length < $maxLength) {
                $result .= '...';
            }
            $length = $maxLength;
            break;
        }

        $result .= $word;
        $length += $wordLen;
    }
    return $result;
}

function normalize_text($str) {
    // 1. 全角转半角（a-zA-Z0-9、空格、标点）
    // mb_convert_kana 选项：
    // a:全角英文字母 → 半角
    // n:全角数字 → 半角
    // s:全角空格 → 半角
    // p:全角标点 → 半角
    $str = mb_convert_kana($str, 'ansp', 'UTF-8');

    // 2. 中文标点转英文标点
    $punctMap = [
    // 逗号、句号
    '，' => ',',
    '。' => '.',
    '、' => ',',
    
    // 叹号、问号、冒号、分号
    '！' => '!',
    '？' => '?',
    '：' => ':',
    '；' => ';',
    
    // 单引号
    '‘' => "'",
    '’' => "'",
    '＇' => "'",   // 全角撇号
    '′' => "'",   // 单引号变体
    
    // 双引号
    '“' => '"',
    '”' => '"',
    '＂' => '"',  // 全角双引号
    '″' => '"',   // 双引号变体
    
    // 括号
    '（' => '(',
    '）' => ')',
    '【' => '[',
    '】' => ']',
    '《' => '<',
    '》' => '>',
    
    // 破折号、连字符
    '—' => '-',   // 长破折号
    '–' => '-',   // 短破折号
    '——' => '-',  // 中文破折号
    
    // 省略号
    '…' => '...', // 中文省略号
    
    // 其他常用符号
    '·' => '.',   // 中文间隔点
    '〜' => '~',  // 波浪号
];

    return str_replace(array_keys($punctMap), array_values($punctMap), $str);
}
?>