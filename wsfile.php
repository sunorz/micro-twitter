<?php

// ------------------ 参数检查 ------------------
if (!isset($_GET['file']) || strlen($_GET['file']) === 0) {
    http_response_code(404);
    exit;
}

// 安全配置和Session初始化
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
session_start();
if (!isset($_SESSION['dashboard'])) {
    http_response_code(401);
    exit;
}

require_once 'config_auth.php'; // 包含 $allowed_key 和 $open

// 获取参数
$fk = trim($_SESSION['dashboard'] ?? '');

$host_ip = preg_replace('/:.*/', '', $_SERVER['HTTP_HOST']);
$ip_whitelisted = in_array($host_ip, [
    '127.0.0.1',
    '172.32.6.33',
    '192.168.194.161',
    'localhost'
]);

// ==================== 验证核心逻辑 ====================
if (!$ip_whitelisted && $fk !== $fkkey) {
    http_response_code(401);
    exit;
}





$file = basename($_GET['file']); // 防止目录遍历

// ------------------ 生成 token ------------------
date_default_timezone_set('Asia/Shanghai');
$hourTimestamp = strtotime(date('Y-m-d H:00:00'));
$salt = "CUSTOM_SALT";
$token = hash('sha256', $hourTimestamp . $salt);

// ------------------ 构造 b.com API URL ------------------
$bUrl = "https://yourdomain/getfile.php?file=" . urlencode($file);

// ------------------ 初始化 cURL ------------------
$ch = curl_init($bUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Auth-Token: $token"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // 直接输出
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_BUFFERSIZE, 8192); // 每次 8KB 输出
curl_setopt($ch, CURLOPT_HEADER, false);

// 设置流式输出
$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
if ($ext === 'm3u8') {
    header('Content-Type: application/vnd.apple.mpegurl');
    // 先抓取内容处理 TS URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $m3u8Content = curl_exec($ch);

    // 替换 TS URL，指向本代理 PHP
    $m3u8Content = preg_replace_callback('/([^\r\n]+\.ts)/', function($matches) {
        return "/wsfile.php?file=" . urlencode($matches[1]);
    }, $m3u8Content);

    echo $m3u8Content;
    exit;
} else {
    // TS 或普通文件
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: inline; filename="'.$file.'"');

    // 开启输出缓冲关闭
    while (ob_get_level()) ob_end_clean();
    flush();

    // 使用 cURL 流式输出
    curl_exec($ch);
}

curl_close($ch);
?>
