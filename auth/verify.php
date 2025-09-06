<?php
require 'totp_functions.php';
$config_file = __DIR__.'/config.php';

if (!file_exists($config_file)) die('<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="google" content="notranslate"><meta name="color-scheme" content="light dark"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>666 TOTP Error</title><style>body{font-family:sans-serif;background:#fff;color:#222;margin:0;padding:0}main{max-width:600px;margin:100px auto;padding:20px;text-align:center}h1{font-size:2em;margin-bottom:0.5em}p{margin:0.5em 0;color:#555}code{background:#f5f5f5;padding:2px 4px;border-radius:4px;font-size:0.9em}footer{margin-top:2em;font-size:0.85em;color:#888}@media (prefers-color-scheme: dark){body{background:#0d1117;color:#e6edf3}p{color:#9ba4ad}code{background:#161b22}footer{color:#8b949e}}</style></head><body><main><h1>666 TOTP Error</h1><p>Refer to <em>index.php</em> to deploy the <em>config.php</em></p><p><code>GET /verify.php</code></p><footer><p>Caddy Server (Simulated)</p></footer></main></body></html>');
include $config_file;
session_start();
// 只处理AJAX请求，完全禁用同步提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 强制要求必须是AJAX请求
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        http_response_code(403);
        die('Forbidden: Only AJAX requests are allowed');
    }
    
    $code = htmlspecialchars(trim($_POST['code']), ENT_QUOTES, 'UTF-8');
    
    if (preg_match('/^\d{6}$/', $code) && verify_totp($TOTP_SECRET, $code)) {
        // 验证成功，返回成功标识
        require_once('../config_auth_.php');
        $_SESSION['dashboard']=$fkkey;
        die('SUCCESS_REDIRECT:/post.php');
    } else {
        // 验证失败
        die('ERROR:Incorrect code|' . $code);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta name="color-scheme" content="light dark">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOTP 登录 - 高级不可回收物</title>
    <link rel="stylesheet" href="/assets/archive.min.css?v=2.3.29">
    <style>
                .center-all{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .verify-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 300px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .code-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        
        .code {
            display: flex;
            gap: 5px;
            font-size: 32px;
            font-family: 'Consolas', 'Menlo', 'Courier New', monospace;
            cursor: text;
            padding: 15px;
            border: 1px var(--md-outline) solid;
            border-radius: 18px;
            background: var(--md-surface);
        }
        
        .code span {
            width: 30px;
            text-align: center;
        }

        .verify-btn {
            width: 100%;
            height: 52px;
            background-color: #409eff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
            margin-top: 8px;
        }

        .verify-btn:hover:not(:disabled) {
            background-color: #3390e9;
        }

        .verify-btn:active:not(:disabled) {
            background-color: #2d81d5;
        }
        
        .verify-btn:disabled {
            background-color: #666;
            cursor: not-allowed;
        }
        
        .error-tip {
            width: 100%;
            text-align: center;
            display: none;
            margin: 10px 0;
            color: var(--md-red);
        }
        .success-tip{
            width: 100%;
            text-align: center;
            margin: 10px 0;
        }
        .totp {
            width: 84px;
            opacity: 0.5;
            display: inline-block;
            margin-bottom: 2rem;
        }
        .code.error {
    color: var(--md-red);
}
.st0 {
        fill: #1296db;
      }
      .tweet-header{
          display:block;
      }
      .tweet-content{
          min-height:10px;
      }
/* 修复iOS抖动动画 */
@keyframes shake {
    0%, 100% { transform: translate3d(0, 0, 0); }
    10%, 30%, 50%, 70%, 90% { transform: translate3d(-10px, 0, 0); }
    20%, 40%, 60%, 80% { transform: translate3d(10px, 0, 0); }
}

.code.shake {
    animation: shake 0.6s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    /* 强制GPU加速，iOS需要这个 */
    backface-visibility: hidden;
    perspective: 1000px;
}
        @media (prefers-color-scheme: dark) {
            .verify-btn {
                background-color: #5ba2ff;
            }
            .verify-btn:hover:not(:disabled) {
                background-color: #4090ff;
            }
            .verify-btn:active:not(:disabled) {
                background-color: #2580ff;
            }
            .verify-btn:disabled {
                background-color: #444;
            }

        }
        
        .tweet-header:nth-child(n+2) {
            justify-content: center;
        }

        .cooldown-timer {
            font-size: 12px;
            margin-top: 5px;
            color: #999;
            text-align: center;
        }
        .header a:hover{
            text-decoration: none !important;
        }
        .logout{
            display:none;
        }
        .logout a{
            cursor:pointer;
        }
        .bottom-banner{
            filter: none;
            -webkit-filter: none;
        }
    </style>
</head>
<body>
<body>
    <div class="header">
        <a href="/x.php">
            <svg t="1746847106578" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1568" width="32" height="32">
                <path class="icon" d="M446.31 135.36c59.14-32.57 133.57-17.46 177.33 32.42 20.22 23.05 36.34 49.86 53.08 75.76 54.92 85.02 109.37 170.34 163.78 255.69 14.68 23.02 11.74 37.13-10.11 53.81-3.85 2.94-7.82 5.8-12.04 8.17-32.65 18.35-40.83 16.37-60.85-14.6-71.42-110.44-143.06-220.74-214.09-331.43-20.69-32.24-49.06-54.85-82.48-72.11-4.21-2.17-8.4-4.43-14.62-7.71zM165.01 653.32c-20.77-42.48-23.41-82.99-0.47-119.52C213.2 456.32 265.31 381 315.98 304.77c28.85-43.4 58.17-86.5 86.11-130.47 8.39-13.21 18.19-15.94 29.93-9.09 19.47 11.36 37.87 24.56 55.3 36.05-68.12 106.25-134 208.45-199.16 311.1-14.88 23.45-27.1 48.58-41 72.66-2.48 4.3-6.62 8.65-11.05 10.66-27.47 12.48-52.05 28.44-71.1 57.64zM702.39 785.54c-47.62 32.71-93.48 64.21-141.88 97.46-0.68-7.98-1.5-13.09-1.5-18.2-0.08-70.43 0.39-140.87-0.36-211.3-0.19-17.39 5.33-23.46 22.96-23.11 47.95 0.96 95.97-0.91 143.89 0.67 52.88 1.75 95.25-19.04 131.85-54.99 3.36-3.3 6.85-6.48 10.48-9.92 14.29 27.24 6.02 74.23-20.03 101.21-28.96 29.99-63.04 50.9-107.28 49.31-23.04-0.83-46.14 0.14-69.21-0.16-11.27-0.15-17.1 3.87-16.39 15.94 0.89 15.09-4.26 34.43 3.27 44.05 6.65 8.49 27.13 6.14 44.2 9.04zM468.81 880.48c-45.78-32.29-91.55-64.57-141.87-100.06h48.12v-60.93c-62.01-2.91-128.66 10.55-183.11-37.54 14.52-26.2 37.09-42.29 60.66-57.17 2.63-1.66 6.9-1.38 10.32-1.06 32.99 3.08 65.9 7.84 98.95 9.16 32.06 1.28 64.27-0.59 96.39-1.8 11.18-0.42 17.17 2.18 17.13 14.68-0.23 77.57-0.12 155.15-0.12 232.72-2.16 0.66-4.31 1.33-6.47 2z" fill="#ffffff" p-id="1569"></path>
            </svg>
            <span style="display:inline-block;transform: translate(5px,-8px);">高级不可回收物</span>
        </a>
    </div>

    <div class="tweet"> 
        <div class="tweet-content">
            <div class="tweet-header"><h2>TOTP 登录</h2></div>
            <div class="tweet-header"><div style="display:flex;justify-content: center;">
            <svg class="totp" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 124.1 127.67">
               <defs>
  <linearGradient id="gold-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
    <!-- 深金棕 -->
    <stop offset="0%" stop-color="#b8860b"/>
    <!-- 亮金黄 -->
    <stop offset="25%" stop-color="#ffd700"/>
    <!-- 高光（几乎白） -->
    <stop offset="40%" stop-color="#fff8dc"/>
    <!-- 再回到亮金黄 -->
    <stop offset="60%" stop-color="#ffd700"/>
    <!-- 暗一点的金棕 -->
    <stop offset="100%" stop-color="#8b7500"/>
  </linearGradient>
</defs>
  <!-- Generator: Adobe Illustrator 29.5.1, SVG Export Plug-In . SVG Version: 2.1.0 Build 141)  -->
  <path class="st0" d="M111.5,9.74c0,5.38-4.36,9.74-9.74,9.74s-9.74-4.36-9.74-9.74S96.38,0,101.76,0s9.74,4.36,9.74,9.74Z"/>
  <path class="st0" d="M28.44,46.23h-9.79c-.94,0-2.11-.41-2.85-.99l-5.15-4.09c-.61-.48-.99-1.17-1.07-1.94-.08-.77.14-1.53.63-2.13l5.43-6.61c.55-.67,1.36-1.05,2.23-1.05.67,0,1.33.24,1.84.67l1.4,1.17.65-.79c.54-.65,1.33-1.03,2.18-1.03.73,0,1.42.28,1.95.78l1.1,1.05.95-.97c.54-.56,1.31-.88,2.1-.88.72,0,1.41.26,1.95.74l1.98,1.76c-.86-8.6-8.11-15.32-16.94-15.32C7.62,16.58,0,24.21,0,33.61c0,6.92,4.12,12.86,10.04,15.53l-4.12,25.19h22.21l-4.12-25.19c1.63-.74,3.12-1.73,4.43-2.91Z"/>
  <path d="M74.75,54.67c-3.16-.03-5.72-2.61-5.72-5.77s2.56-5.74,5.72-5.77l1.6-.07v-19.92c0-1.86-1.63-3.39-3.63-3.39h-14.51c-2,0-4.56,1.25-5.71,2.78l-8.61,11.51h-9.58c-.5,0-1.21-.27-1.58-.6l-2.05-1.82c-.37-.33-.96-.31-1.31.05l-1.68,1.72c-.35.36-.93.37-1.29.02l-1.86-1.77c-.36-.34-.92-.31-1.24.07l-1.34,1.62c-.32.38-.89.44-1.27.12l-2.24-1.86c-.38-.32-.95-.27-1.27.12l-5.43,6.61c-.32.39-.26.96.13,1.26l5.16,4.09c.39.31,1.12.56,1.62.56h25.56l8.27,11.06c1.14,1.53,3.71,2.78,5.71,2.78h14.51c1.98,0,3.6-1.5,3.63-3.34l-1.59-.07ZM67.5,46.41c0,1-.81,1.81-1.81,1.81h-4.31c-1,0-1.81-.81-1.81-1.81v-14.97c0-1,.81-1.81,1.81-1.81h4.31c1,0,1.81.81,1.81,1.81v14.97Z" fill="<?php 
                $admin = isset($_SESSION['dashboard']) ? 100 : 0; 
                date_default_timezone_set('Asia/Shanghai');
                $currentDate = date('Y-m-d');
                $access = (isset($_SESSION['agree']) && $_SESSION['agree'] === $currentDate) ? 1 : 0;
                $visit = (isset($_SESSION['havekey']) && $_SESSION['havekey'] === $currentDate) ? 10 : 0;
                $perm_total = $admin + $access + $visit;    
                // 定义不同总和对应的字体颜色（可根据需求调整）
$color_map = [
    0 => 'var(--md-red)',   
    1 => 'var(--md-green)',    
    10 => 'var(--md-blue)',   
    11 => 'var(--md-indigo)',
    100 => 'var(--md-orange)',
    111 => 'url(#gold-gradient)'
];

// 获取当前总和对应的颜色（默认灰色）
$perm_color = $color_map[$perm_total] ?? '#1296db'; 
  echo $perm_color;?>"/>
  <path class="st0" d="M115.18,61.29l.38-14.81c0-19.14-4.62-23.29-18.04-23.29l-19.21,1.03v10.85l7.99.64.29,6.91,4.45-.2,6.09-7.78,1.54,1.25-6.62,8.46-17.24.76c-2.09,0-3.79,1.7-3.79,3.79s1.7,3.79,3.79,3.79l19.54.86c1.5,0,2.65-.77,3.68-1.81.06-.06,5.84-6.24,8.7-9.3l1.54,1.25c-8.73,9.34-8.8,9.41-8.83,9.44-1.02,1.03-2.65,2.4-5.08,2.4h-.04l-7.18-.32,2.8,67.52c0,2.73,2.21,4.94,4.94,4.94s4.94-2.21,4.94-4.94l1.36-30.91h1.2l1.36,30.91c0,2.73,2.21,4.94,4.94,4.94s4.94-2.21,4.94-4.94l.79-30.91h9.7s.37-18.36-8.92-30.54Z"/>
</svg>
        </div></div>
            <div class="tweet-header">                
                <div class="success-tip" id="successTip">For <strong>the site administrator</strong> use only. Unauthorized access prohibited.
</div>
<div class="error-tip" id="errorTip"></div>
            </div>
            <div class="tweet-text center-all">
                <div class="verify-container"  <?php if($perm_total==111){echo ' style="display:none;"';}?>>
                    <form id="verifyForm">
                        <div class="code" id="codeBox">
                            <span>·</span><span>·</span><span>·</span><span>·</span><span>·</span><span>·</span>
                            <input 
                                type="text" 
                                name="code"  
                                maxlength="6" 
                                oninput="filterNonNumber(this); updateBtnStatus(this)"
                                inputmode="numeric" 
                                autocomplete="one-time-code" 
                                class="code-input"
                                id="codeInput"
                            >                    
                        </div>
                        <button type="submit" class="verify-btn" id="verifyBtn" disabled>Login</button>
                    </form>
                </div> 
                <span class="logout" <?php if($perm_total==111){echo ' style="display:inline-block;"';}?>>Successfully logged in. <a href="/post.php">Go to dashboard</a></span>   
            </div>
            
            <div class="tweet-text" style="text-align:right;padding-top:4rem;font-size:12px;color:#424242;"><span id="clock"></span><br/>

                <?php 
                $clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
                $_SERVER['HTTP_X_REAL_IP'] ?? 
                $_SERVER['HTTP_CLIENT_IP'] ?? 
                $_SERVER['REMOTE_ADDR'] ?? 
                'Unknown';
                $content = file_get_contents('../assets/version.md');  
  
$perm_total = sprintf("%03d", $perm_total); 

  
echo 'Supports Google<sup>TM</sup> Authenticator, Microsoft<sup>TM</sup> Authenticator, and Synology<sup>TM</sup> Secure SignIn.<br/>';                 
                echo $_SERVER['HTTP_USER_AGENT'].' IP Address: '.htmlspecialchars($clientIP).'<br/>Version: '.$content.' Permission: '.$perm_total; ?>
            </div>
        </div>
    </div>  
    
    <div class="bottom-banner">login</div>
    <div class="footer">
        &copy; 高级不可回收物 , <script>
            const now = new Date();
            const currentYear = now.getFullYear();
            document.write(currentYear); 
        </script>
        <a href="/info.html">版权信息</a><a href="/archive.html">存档</a>
    </div>
    
    <script>
        // 更新CST时间函数
        function updateCSTTime() {
            const now = new Date();
            
            // 计算UTC时间与CST时间的差值（CST是UTC+8）
            const utcTime = now.getTime() + now.getTimezoneOffset() * 60000;
            const cstTime = new Date(utcTime + 8 * 3600000); // 8小时 = 8 * 3600000毫秒
            
            // 获取年、月、日（CST）
            const year = cstTime.getFullYear();
            const month = String(cstTime.getMonth() + 1).padStart(2, '0');
            const day = String(cstTime.getDate()).padStart(2, '0');
            
            // 获取时、分、秒（CST）
            const hours = String(cstTime.getHours()).padStart(2, '0');
            const minutes = String(cstTime.getMinutes()).padStart(2, '0');
            const seconds = String(cstTime.getSeconds()).padStart(2, '0');
            
            // 格式化时间字符串
            const timeString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            
            // 显示在页面上
            document.getElementById('clock').textContent = timeString;
        }
        
        // 立即更新一次时间
        updateCSTTime();
        
        // 每秒更新一次时间
        setInterval(updateCSTTime, 1000);
        // DOM元素
const codeInput = document.getElementById('codeInput');
const verifyBtn = document.getElementById('verifyBtn');
const errorTip = document.getElementById('errorTip');
const successTip = document.getElementById('successTip');
const codeBox = document.getElementById('codeBox');
const spans = codeBox.querySelectorAll('span');
const form = document.getElementById('verifyForm');

// 防重复提交配置
const COOLDOWN_TIME = 30000;
const STORAGE_KEY = 'totp_last_submit_time';

// 1. 获取最后提交时间
function getLastSubmitTime() {
    const storedTime = localStorage.getItem(STORAGE_KEY);
    return storedTime ? parseInt(storedTime, 10) : 0;
}

// 2. 保存提交时间
function saveLastSubmitTime() {
    localStorage.setItem(STORAGE_KEY, Date.now().toString());
}

// 3. 检查冷却时间
function checkCooldown() {
    const lastSubmitTime = getLastSubmitTime();
    const currentTime = Date.now();
    const remainingTime = COOLDOWN_TIME - (currentTime - lastSubmitTime);
    
    if (remainingTime > 0) {
        return Math.ceil(remainingTime / 1000);
    }
    return 0;
}

// 4. 更新冷却时间显示
function updateCooldownDisplay() {
    const remainingSeconds = checkCooldown();
    
    if (remainingSeconds > 0) {
        verifyBtn.disabled = true;
        verifyBtn.textContent = `Login`;
        errorTip.textContent = `Incorrect code. Try again in ${remainingSeconds} sec.`;
        errorTip.style.display = 'block'; // 确保冷却期间错误提示可见
        
        const timer = setInterval(() => {
            const seconds = checkCooldown();
            if (seconds > 0) {
                verifyBtn.textContent = `Login`;
                errorTip.textContent = `Incorrect code. Try again in ${seconds} sec.`;
            } else {
                clearInterval(timer);
                verifyBtn.textContent = 'Login';
                errorTip.style.display = 'none'; // 倒计时结束隐藏错误提示
                updateBtnStatus(codeInput);
            }
        }, 1000);
    } else {
        verifyBtn.textContent = 'Login';
        updateBtnStatus(codeInput);
    }
}

// 5. 过滤非数字字符 - 修改：冷却期间不清除错误状态
function filterNonNumber(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
    const remainingSeconds = checkCooldown();
    
    // 冷却期间不清除错误状态
    if (remainingSeconds <= 0) {
        codeBox.classList.remove('error', 'shake');
        errorTip.style.display = 'none';
    }
    updateBtnStatus(input);
}

// 6. 更新按钮状态
function updateBtnStatus(input) {
    const remainingSeconds = checkCooldown();
    const isCodeValid = input.value.length === 6 && /^\d{6}$/.test(input.value);
    
    if (remainingSeconds > 0) {
        verifyBtn.disabled = true;
    } else {
        verifyBtn.disabled = !isCodeValid;
    }
}

// 7. 显示错误状态 - 修复iOS动画
function showErrorState(errorMessage, userCode) {
    // 显示错误消息
    errorTip.textContent = errorMessage;
    errorTip.style.display = 'block';
    
    // 设置红色错误状态
    codeBox.classList.add('error');
    
    // 先移除可能的动画类，确保可以重新触发
    codeBox.classList.remove('shake');
    
    // 强制重绘以触发动画
    void codeBox.offsetWidth;
    
    // 添加抖动动画
    codeBox.classList.add('shake');
    
    // 动画结束后移除shake类，但保留error类
    codeBox.addEventListener('animationend', function onAnimationEnd() {
        codeBox.classList.remove('shake');
        codeBox.removeEventListener('animationend', onAnimationEnd);
    }, { once: true });
    
    // 更新冷却显示
    updateCooldownDisplay();
}

// 8. 清除错误状态
function clearErrorState() {
    codeBox.classList.remove('error', 'shake');
    errorTip.style.display = 'none';
    
    // 清空输入框
    codeInput.value = '';
    spans.forEach(span => span.textContent = '·');
    
    // 重新聚焦输入框
    codeInput.focus();
}

// 9. 表单提交处理
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    window.scrollTo(0, 0);
    const remainingSeconds = checkCooldown();
    if (remainingSeconds > 0 || verifyBtn.disabled) return;
    
    saveLastSubmitTime();
    
    verifyBtn.disabled = true;
    verifyBtn.textContent = 'Loading...';
    errorTip.style.display = 'none';
    //successTip.style.display = 'none';
    
    // 先移除可能的错误状态
    codeBox.classList.remove('error', 'shake');
    
    try {
        const formData = new FormData();
        formData.append('code', codeInput.value);
        
        const response = await fetch('', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.text();
        console.log('Server response:', result);
        
        if (result.startsWith('SUCCESS_REDIRECT:')) {
            //强制过期
            // 计算当前时间减去60秒的时间戳（毫秒）
            const minuteAgoMs = 60 * 1000; 
            const minuteAgo = Date.now() - minuteAgoMs;
            localStorage.setItem(STORAGE_KEY, minuteAgo.toString());
            const redirectUrl = result.split(':')[1];
            console.log('Redirecting to:', redirectUrl);
            window.location.href = redirectUrl;
            
        } else if (result.startsWith('ERROR:')) {
            const parts = result.split('|');
            const errorMessage = parts[0].split(':')[1];
            const userCode = parts[1] || '';
            
            // 显示错误状态，保留用户输入的验证码
            showErrorState(errorMessage, userCode);
            
        } else {
            throw new Error('Invalid response format');
        }
        
    } catch (error) {
        errorTip.textContent = 'Connection issue. Please refresh.';
        errorTip.style.display = 'block';
        console.error('Error:', error);
        updateCooldownDisplay();
    }
});

// 10. 点击.code时不清除错误状态，只聚焦输入框
codeBox.addEventListener('click', function() {
      setTimeout(() => {
        document.querySelector('.success-tip')?.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
            });
      }, 300);                                                
    
    // 无论是否有错误状态，都只聚焦输入框，不清除错误
    codeInput.focus();
});

// 11. 输入框显示处理
codeInput.addEventListener('input', function() {
    codeBox.classList.remove('error');
    let val = this.value.replace(/\D/g,'').slice(0,6);
    this.value = val;
    spans.forEach((s, i) => s.textContent = val[i] || '·');
    
    if (val.length === 6) {
        updateBtnStatus(this);
    }
});

// 12. 页面加载初始化
window.onload = function() {
    updateCooldownDisplay();
    updateBtnStatus(codeInput);
    if (history.scrollRestoration) {
    history.scrollRestoration = 'manual';
}
         
};

// 13. 阻止同步提交
form.setAttribute('onsubmit', 'return false;');
</script>
</body>
</html>