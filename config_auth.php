<?php
if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    exit('404 Not Found');
}
ini_set('display_errors', '0');
error_reporting(0);
$allowed_key_init = 'CUSTOM_KEY_FOR_ENTER';
$open = false;
$salt_init = 'CUSTOM_SALT';
$allowed_key = hash('sha256', $allowed_key_init . $salt_init);
date_default_timezone_set('Asia/Shanghai');
$fkkey_init = date('yyyywmmdd');
$fkkey = hash('sha256', $fkkey_init . $salt_init);
date_default_timezone_set('Asia/Shanghai');
?>