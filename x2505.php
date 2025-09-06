<?php
// 安全配置和Session初始化
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
session_start();
date_default_timezone_set('Asia/Shanghai');
$today = date('Y-m-d');
require_once 'config_auth.php'; // 包含 $allowed_key 和 $open

// 获取参数
$key = trim($_GET['key'] ?? '');
$access = trim($_GET['access'] ?? 'false');

// ==================== Session状态更新 ====================
// 如果access验证通过，设置Session标记
if ($access === 'true') {
    $_SESSION['access_granted'] = true;
    $_SESSION['agree'] = $today;
}

// 如果key验证通过，设置Session标记
if ($key === $allowed_key) {
    $_SESSION['key_verified'] = true;
    $_SESSION['havekey'] = $today;
}

// ==================== IP白名单检查 ====================
$host_ip = preg_replace('/:.*/', '', $_SERVER['HTTP_HOST']);
$ip_whitelisted = in_array($host_ip, [
    '127.0.0.1',
    '172.32.6.33',
    '192.168.194.161',
    'localhost'
]);

// ==================== 验证核心逻辑 ====================
if (!$ip_whitelisted) {
    // access验证：参数或Session
    $access_passed = ($access === 'true') || 
                   (isset($_SESSION['access_granted']) || 
                   (isset($_SESSION['agree']) && $_SESSION['agree'] === $today));
    
    // key验证：如果$open=false才需要
    $key_required = !$open;
    $key_passed = !$key_required ||
             ($key === $allowed_key) ||
             (isset($_SESSION['key_verified'])) ||
             (isset($_SESSION['havekey']) && $_SESSION['havekey'] === $today);
    
    // 决策逻辑
    if (!$access_passed) {
        header('Location: index.php');
        exit;
    } elseif ($key_required && !$key_passed) {
        header('Location: donate.html');
        exit;
    }
}

// ==================== 更新Session ====================
// 无论是否白名单，都更新最后访问时间
$_SESSION['havekey'] = $today;
$_SESSION['agree'] = $today;
?>
<!DOCTYPE html>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <meta name="google" content="notranslate">
        <meta name="color-scheme" content="light dark">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>2025年5月 - 高级不可回收物</title>
        <link rel="stylesheet" href="assets/xym.css?v=2.3.29">
        <link rel="stylesheet" href="assets/dist/file-icon-classic.min.css" />
     </head>
    <body>
        <div class="header"><a href="/x.php"><svg t="1746847106578" viewBox="0 0 1024 1024"
                version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1568"
                width="32" height="32"><path class="icon" d="M446.31
                    135.36c59.14-32.57 133.57-17.46 177.33 32.42 20.22 23.05
                    36.34 49.86 53.08 75.76 54.92 85.02 109.37 170.34 163.78
                    255.69 14.68 23.02 11.74 37.13-10.11 53.81-3.85 2.94-7.82
                    5.8-12.04 8.17-32.65 18.35-40.83
                    16.37-60.85-14.6-71.42-110.44-143.06-220.74-214.09-331.43-20.69-32.24-49.06-54.85-82.48-72.11-4.21-2.17-8.4-4.43-14.62-7.71zM165.01
                    653.32c-20.77-42.48-23.41-82.99-0.47-119.52C213.2 456.32
                    265.31 381 315.98 304.77c28.85-43.4 58.17-86.5 86.11-130.47
                    8.39-13.21 18.19-15.94 29.93-9.09 19.47 11.36 37.87 24.56
                    55.3 36.05-68.12 106.25-134 208.45-199.16 311.1-14.88
                    23.45-27.1 48.58-41 72.66-2.48 4.3-6.62 8.65-11.05
                    10.66-27.47 12.48-52.05 28.44-71.1 57.64zM702.39
                    785.54c-47.62 32.71-93.48 64.21-141.88
                    97.46-0.68-7.98-1.5-13.09-1.5-18.2-0.08-70.43
                    0.39-140.87-0.36-211.3-0.19-17.39 5.33-23.46 22.96-23.11
                    47.95 0.96 95.97-0.91 143.89 0.67 52.88 1.75 95.25-19.04
                    131.85-54.99 3.36-3.3 6.85-6.48 10.48-9.92 14.29 27.24 6.02
                    74.23-20.03 101.21-28.96 29.99-63.04 50.9-107.28
                    49.31-23.04-0.83-46.14 0.14-69.21-0.16-11.27-0.15-17.1
                    3.87-16.39 15.94 0.89 15.09-4.26 34.43 3.27 44.05 6.65 8.49
                    27.13 6.14 44.2 9.04zM468.81
                    880.48c-45.78-32.29-91.55-64.57-141.87-100.06h48.12v-60.93c-62.01-2.91-128.66
                    10.55-183.11-37.54 14.52-26.2 37.09-42.29 60.66-57.17
                    2.63-1.66 6.9-1.38 10.32-1.06 32.99 3.08 65.9 7.84 98.95
                    9.16 32.06 1.28 64.27-0.59 96.39-1.8 11.18-0.42 17.17 2.18
                    17.13 14.68-0.23 77.57-0.12 155.15-0.12 232.72-2.16
                    0.66-4.31 1.33-6.47 2z" fill="#ffffff" p-id="1569"></path></svg><span
                style="display:inline-block;transform: translate(5px,-8px);">高级不可回收物</span></a></div>
                <div class="top-banner"></div>
                 <div style="padding:16px;background-color:var(--md-background);position:
            relative;">
            <div>
                <p><span style="font-weight: bolder;margin-right:5px;" id="tposts">...</span><span
                        style="color:var(--md-on-surface);">Posts</span><span style="font-weight: bolder;margin:0px 5px 0px 20px;" id="followers">...</span><span
                        style="color:var(--md-on-surface);">Followers (MoM)</span></p>
            </div>
        </div>
        <div class="nav">
            <div class="twi-tabs" style="margin-left:2rem;"><div class="twi-tabs-text twi-tabs-cur-text"><a href="/archive.html">May 2025</a></div><div
                    class="twi-tabs-bottom twi-tabs-cur-bottom"></div></div>
        </div>
   
            <!-- INSERT_HERE -->
                    <div class="bottom-banner">may2025</div>
                    <div class="footer">
                        &copy; 高级不可回收物 ,
                        <script> const date = new Date();
             document.write(date.getFullYear());</script><a href="info.html">版权信息</a><a href="archive.html">存档</a>
                    </div><script src="assets/html2canvas.min.js"></script>
<!-- 模态框：图片预览 + 下载按钮 -->
<div id="preview-modal" style="
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  justify-content: center;
  align-items: center;
  z-index: 1999;
  background-color: rgba(0,0,0,0.4);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
">
  <div class="qq-window">
      <!-- 标题栏 -->
        <div class="qq-titlebar">        
            <div class="qq-icon"><img src="images/google-icon.svg"></div><div class="qq-titlebar-text">Share a tweet</div>
            <div class="qq-titlebar-buttons">
                <a onclick="document.getElementById('preview-modal').style.display='none'" class="qq-titlebar-button close">×</a>
            </div>
            <div class="qq-decor-line"></div>
        </div>
        <div class="qq-content">
    <div id="preview-image-container" style="border:1px solid #94b8ce;margin-bottom:1em;max-height: 80vh; overflow: auto;"></div>
<div class="qq-button-container">
                    <a id="download-btn" class="qq-button"><svg t="1753710735193" class="" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5883" width="30" height="30"><path d="M505.7 661c3.2 4.1 9.4 4.1 12.6 0l112-141.7c4.1-5.2 0.4-12.9-6.3-12.9h-74.1V168c0-4.4-3.6-8-8-8h-60c-4.4 0-8 3.6-8 8v338.3H400c-6.7 0-10.4 7.7-6.3 12.9l112 141.8z" p-id="5884" fill="#2d404e"></path><path d="M878 626h-60c-4.4 0-8 3.6-8 8v154H214V634c0-4.4-3.6-8-8-8h-60c-4.4 0-8 3.6-8 8v198c0 17.7 14.3 32 32 32h684c17.7 0 32-14.3 32-32V634c0-4.4-3.6-8-8-8z" p-id="5885" fill="#2d404e"></path></svg><span style="
                   transform:translateY(2px);display:inline-block;user-select:none;">Download</span></a>
                </div>
  </div>
</div>
</div>
<!-- 隐藏截图区域 -->
<div id="capture-area" style="position: absolute; left: -9999px; top: -9999px;"></div>

<script src="assets/common.js?v=2.3.29"></script>
<script>    


                        const total = calculateY('2025-05-31')-calculateY('2025-05-02');
                        const formatted = formatFollowers(total);
                        // 显示到指定的span元素中
                        document.getElementById('followers').textContent = '+' + formatted;
                    </script>
                </body>
            </html>