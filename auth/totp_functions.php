<?php
// totp_functions.php
// TOTP 核心逻辑，兼容 PHP 7.4

// Base32 编码
function base32_encode($data) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $bits = 0;
    $value = 0;
    $output = '';
    for ($i = 0, $len = strlen($data); $i < $len; $i++) {
        $value = ($value << 8) | ord($data[$i]);
        $bits += 8;
        while ($bits >= 5) {
            $bits -= 5;
            $output .= $alphabet[($value >> $bits) & 0x1F];
        }
    }
    if ($bits > 0) {
        $output .= $alphabet[($value << (5 - $bits)) & 0x1F];
    }
    return $output;
}

// Base32 解码
function base32_decode($b32) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $b32 = strtoupper($b32);
    $b32 = preg_replace('/[^A-Z2-7]/', '', $b32);
    $l = strlen($b32);
    $bits = 0;
    $value = 0;
    $output = '';
    for ($i = 0; $i < $l; $i++) {
        $value = ($value << 5) | strpos($alphabet, $b32[$i]);
        $bits += 5;
        if ($bits >= 8) {
            $bits -= 8;
            $output .= chr(($value >> $bits) & 0xFF);
        }
    }
    return $output;
}

// 生成随机 secret（16字节 -> Base32）
function generate_secret($length = 16) {
    return base32_encode(random_bytes($length));
}

// 生成 otpauth URI
function get_otpauth_uri($secret, $accountName, $issuer='microx') {
    $label = rawurlencode($issuer . ':' . $accountName);
    return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}&digits=6&period=30";
}

// 生成 TOTP 动态码
function generate_totp($secret, $timeSlice = null, $digits = 6, $period = 30) {
    if ($timeSlice === null) $timeSlice = floor(time() / $period);
    $key = base32_decode($secret);
    $time = pack('N*', 0) . pack('N*', $timeSlice);
    $hash = hash_hmac('sha1', $time, $key, true);
    $offset = ord(substr($hash, -1)) & 0x0F;
    $truncatedHash = substr($hash, $offset, 4);
    $unpack = unpack('N', $truncatedHash); // PHP 7.4 不直接短数组访问
    $value = $unpack[1] & 0x7FFFFFFF;
    return str_pad($value % pow(10, $digits), $digits, '0', STR_PAD_LEFT);
}

// 验证 TOTP 动态码（允许 ±1 个时间片容错）
function verify_totp($secret, $code, $window = 1) {
    $code = preg_replace('/\s+/', '', $code);
    if (!ctype_digit($code)) return false;
    $timeSlice = floor(time() / 30);
    for ($i = -$window; $i <= $window; $i++) {
        if (hash_equals(generate_totp($secret, $timeSlice + $i), $code)) return true;
    }
    return false;
}
