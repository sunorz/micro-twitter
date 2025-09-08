<?php

// 获取所有请求头
$headers = getallheaders();

// 取出 X-Auth-Token
$clientToken = $headers['X-Auth-Token'] ?? '';
// 设置时区为 CST
date_default_timezone_set('Asia/Shanghai');

// 获取当前时间戳（精确到小时）
$hourTimestamp = strtotime(date('Y-m-d H:00:00'));

// 你的密钥/盐
$salt = "CUSTOM_SALT";


// 为了防止时间差，可允许前一小时的 token
$validTokens = [
    hash('sha256', $hourTimestamp . $salt),
    hash('sha256', ($hourTimestamp - 3600) . $salt)
];

// 验证 token
if (!in_array($clientToken, $validTokens)) {
    http_response_code(403);
    exit;
}

// token 验证通过，可以处理请求
$file = basename($_GET['file']);
$path = "/pathto/" . $file;

if (!file_exists($path)) {
    http_response_code(404);
    exit;
}

// 输出文件
header('Content-Type: application/octet-stream');
header('Content-Disposition: inline; filename="' . $file . '"');
readfile($path);
?>