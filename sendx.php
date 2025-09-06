<?php

// 如果是通过 Web 访问
if (php_sapi_name() !== 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    exit("Direct access not allowed");
}


function sendToX($tweet){

date_default_timezone_set('UTC');

$url = "https://yourdomain/sendtotwitter.php";
$shared_token = "SHARED_TOKEN";

$ts = time();
$nonce = bin2hex(random_bytes(8)); // 16 字节随机 nonce
$hash = hash_hmac('sha256', $shared_token.$ts.$nonce, $shared_token);
header('Content-Type: application/json');
$post_data = [
    'tweet' => $tweet,
    'ts' => $ts,
    'nonce' => $nonce,
    'hash' => $hash
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));

$response = curl_exec($ch);
curl_close($ch);

echo $response;
}
?>