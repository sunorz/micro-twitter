<?php
if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(404);
    exit('404 Not Found');
}
$TOTP_SECRET='CUSTOM_TOTP_SECRET';
$USERNAME='CUSTOM_USERNAME'
?>