<?php
// 屏蔽 Deprecated 警告
error_reporting(E_ALL & ~E_DEPRECATED);

require "vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

// 你的 API Key / Access Token
$api_key = "X_COM_API_KEY";
$api_secret = "X_COM_API_SECRET";
$access_token = "X_COM_ACCESS_TOKEN";
$access_token_secret = "X_COM_ACCESS_TOKEN_SECRET";

$connection = new TwitterOAuth($api_key, $api_secret, $access_token, $access_token_secret);

// 安全配置
$shared_token = "SHARED_TOKEN$";
$time_window = 300; // 时间误差 5 分钟

// 用于防重放的已使用 nonce 列表（实际可存数据库或缓存）
$used_nonces = []; // 简化示例，生产环境请用持久存储
header('Content-Type: application/json');
// 获取 POST 数据
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['tweet'], $data['hash'], $data['ts'], $data['nonce'])) {
    http_response_code(404);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

// 验证时间戳
$ts = intval($data['ts']);
if (abs(time() - $ts) > $time_window) {
    http_response_code(404);
    echo json_encode(['error' => 'Timestamp expired']);
    exit;
}

// 防重放验证 nonce
$nonce = $data['nonce'];
if (in_array($nonce, $used_nonces)) {
    http_response_code(404);
    echo json_encode(['error' => 'Nonce already used']);
    exit;
}

// 验证 hash
$expected_hash = hash_hmac('sha256', $shared_token.$ts.$nonce, $shared_token);
if (!hash_equals($expected_hash, $data['hash'])) {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid hash']);
    exit;
}

// 标记 nonce 已使用（示例）
$used_nonces[] = $nonce;

// 发帖
$tweet = $data['tweet'];
$result = $connection->post("tweets", ["text" => $tweet]);
$http_code = $connection->getLastHttpCode();

if ($http_code == 201) {
    echo json_encode(['success' => true, 'tweet_id' => $result->data->id]);
} else {
    http_response_code($http_code);
    echo json_encode(['success' => false, 'http_code' => $http_code, 'result' => $result]);
}