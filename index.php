<?
$host_ip = preg_replace('/:.*/', '', $_SERVER['HTTP_HOST']);
$ip_whitelisted = in_array($host_ip, [
    '127.0.0.1',
    '172.32.6.33',
    '192.168.194.161',
    'localhost'
]);
if($ip_whitelisted)
{
  header('Location: /x.php');
  exit;
}
session_start(); 
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8" />
  <meta name="google" content="notranslate">
  <meta name="color-scheme" content="light dark">
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Never negotiate with ransomware!</title>
  <style>
    :root {
        --md-background: #202124;
        --md-surface: #2d2e30;
        --md-surface-variant: #3c4043;
        --md-on-background: #e8eaed;
        --md-on-surface: #bdbfc2;
        --md-on-surface-variant: #9aa0a6;
        --md-outline: #5f6368;
        --md-blue: #8ab4f8;
        --md-green: #81c995;
        --md-red: #f28b82;
        --md-yellow: #fde293;
        --md-purple: #bb86fc;
--md-pink: #ff8a9d;
--md-orange: #ffb74d;
--md-cyan: #4dd0e1;
--md-teal: #26a69a;
--md-indigo: #7986cb;
--md-brown: #a1887f;
--md-gray: #bdbdbd;
--md-lime: #dce775;
--md-deep-orange: #ff8a65;
--md-light-blue: #4fc3f7;
--md-light-green: #aed581;
        --header-bg: rgba(32, 33, 36, 0.9);
    }
    
body, html {
      background: #111;
      color: #eee;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Microsoft YaHei", "Helvetica Neue", 
             "PingFang SC", "Hiragino Sans GB", "Noto Sans", Arial, sans-serif;

      line-height: 1.6;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1024px;
      margin: 0 auto;
      padding: 1rem;
      width: 90%;
    }
    h1 {
      color: #98ff98;
      padding-bottom: 1rem;
      font-size: 1.8rem;
    }
    p {
      margin-bottom: 1rem;
    }
    .highlight {
      background: #222;
      padding: 1rem;
       border-left: 4px solid #98ff98;
      font-style: italic;
      color: #ddd;
    }
    a.button {
      display: inline-block;
      background: #98ff98;
      color: #2c3e50;
      padding: 1rem 1.5rem;
      border-radius: 12px;
      font-weight: bold;
      text-decoration: none;
      transition: background 0.2s ease;
    }
    a.button:hover {
      background: #7be387;
    }
    #mode-indicator {
      display: none;
    }
.disclaimer  {
  padding: 1rem;
  background: #1a2b1a;
  border: 1px solid #7be387;
  border-radius: 6px;
  box-shadow: 0 0 8px #7be38733;
  color: #cceccc;
  font-size: 0.95rem;
  text-align: left;
}
#fade-overlay {
  position: fixed;
  inset: 0;
  background-color: #111;
  z-index: 9999;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.1s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

#fade-overlay.show {
  opacity: 1;
  pointer-events: auto;
  animation: brighten 10s forwards;
  animation-timing-function: cubic-bezier(0.8, 0, 0.2, 1);
}

@keyframes brighten {
  0%   { background-color: #111; }
  100% { background-color: #fff; }
}

.fade-text {
  text-align: center;
  opacity: 0;
  animation: fadein 1s ease-in forwards, textColorChange 10s forwards;
  animation-delay: 0.5s, 0s;
}
@keyframes textColorChange {
  0% {
    color: #eeeeee; /* 浅灰 */
  }
  100% {
    color: #222222; /* 深色 */
  }
}
.fade-text .cn {
  font-size: 1.8rem;
  font-weight: bold;
}

.fade-text .en {
  font-size: 1rem;
  margin-top: 0.5rem;
  font-style: italic;
  opacity: 0.7;
}
@keyframes fadein {
  to { opacity: 1; }
}
/* 进度条容器样式 */
.progress-bar {
  width: 200px;
  height: 6px;
  background: #444;
  border-radius: 3px;
  overflow: hidden;
  margin: 1.5rem auto 0;
}

/* 进度条填充部分 */
.progress-fill {
  width: 0%;
  height: 100%;
  background: #56ccf2; /* 柔和蓝色 */
  animation: loadProgress 10s forwards;
  animation-timing-function: cubic-bezier(0.8, 0, 0.2, 1); /* 前快后慢节奏 */
}

/* 关键帧动画：从 0% 到 100% 宽度 */
@keyframes loadProgress {
  0% {
    width: 0%;
  }
  90% {
    width: 90%;
  }
  100% {
    width: 100%;
  }
}
.svg-key{
    fill: var(--md-yellow);
    
}
.svg-box{
    fill: var(--md-blue);

}
.svg-light{
    fill: var(--md-yellow);
}
@keyframes pulse {
  0%, 20% { opacity: 0.3; }    /* 开始 0.3 并停留 2s（占总时间的20%） */
  50% { opacity: 1; }           /* 过渡到 1 */
  50%, 70% { opacity: 1; }      /* 保持 1 停留 2s */
  100% { opacity: 0.3; }        /* 回到 0.3 */
}
.svg-light-wrap{
    opacity: 0.3;
    animation: pulse 10s infinite;
    cursor: pointer;
}
.enter-quired{
    line-height:2;margin:2rem 0;display: flex; flex-wrap: wrap; align-items: center; gap: 15px;user-select: none;font-style:italic;
}
.enter-quired a{
    display: inline-block;
    outline: none;
}
.enter-quired a svg{
    display: block;
}
<?php
require_once 'config_auth.php'; // 包含 $allowed_key 和 $open
$tips = '';
$linkkey = '';
$key = trim($_GET['key'] ?? '');
if(trim($key) !== '')
$linkkey = '&key='.$key;
if($open || $allowed_key === $key || (isset($_SESSION['havekey'])&&$_SESSION['havekey']==date('Y-m-d'))){
  $tips = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 200 200" width="24">
  <path class="svg-box" d="M163.91,19.79H36.11L12.75,105.37l-.25,74.84h175v-72.93s-23.59-87.5-23.59-87.5ZM47.25,34.38h105.53l19.88,72.91h-40.1l-14.59,21.88h-35.94l-14.59-21.88H27.36l19.88-72.91Z"/>
  <path class="svg-key" d="M140.91,158.91h-9.21v-18.43h-18.43v-18.43h-18.43v-9.21h-18.43v-13.1c-5.81,2.56-12.08,3.88-18.43,3.89-25.44,0-46.06-20.62-46.06-46.06S32.56,11.51,58,11.51s46.06,20.62,46.06,46.06c0,6.56-1.42,12.78-3.89,18.43h13.1v18.43l46.06,36.85v27.64h-18.43ZM48.79,29.93c-10.17,0-18.43,8.25-18.43,18.43s8.25,18.43,18.43,18.43,18.43-8.25,18.43-18.43-8.25-18.43-18.43-18.43Z"/>
  <polygon class="svg-box" points="132.56 107.29 117.97 129.16 104.05 129.16 104.05 161.97 160.9 161.97 160.9 107.29 132.56 107.29"/>
</svg><span>Congrats, enjoy your key!</span>';
  $linkkey = '&key='.$allowed_key;
}
else
{
    echo "p:nth-child(n+3):nth-child(-n+5) {display: none;}
    a.button {display: none;}";
    $tips = '<a href="donate.html"><svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" width="24"><path class="svg-box" d="M839.2 101.3H184.9L65.3 539.5 64 922.7h896V549.3l-120.8-448zM241.9 176h540.3L884 549.3H678.7l-74.7 112H420l-74.7-112H140.1L241.9 176z"></path></svg></a><span style="flex: 1;">Invalid key. You may click the left box to get one.</span>';
}
?>
</style>
</head>
<body>
<div id="fade-overlay">
  <div class="fade-text">
    <div class="cn">阳光正在赶来</div>
    <div class="en">Strike a match, let magic unfold!</div>
    <div class="progress-bar"><div class="progress-fill"></div></div>
  </div>
</div>
  <div class="container">
    <h1>Never negotiate with ransomware!</h1>
    <p class="highlight">Data can be recovered, but memories can't. Evil knows no borders.</p>
    <p style="line-height:2;margin:2rem 0;">
  This website firmly upholds the One-China Principle and recognizes all sovereign territories claimed by China as defined by the position of the People's Republic of China. We resolutely safeguard national sovereignty and territorial integrity, and we operate in full compliance with the laws and regulations of the People's Republic of China.
</p>
<p style="line-height:2;margin:2rem 0;">
  This website strictly protects cybersecurity and opposes any acts of intrusion, tampering, sabotage, or deletion of networks, servers, or files. Implanting trojans, exploiting vulnerabilities, or engaging in any form of unauthorized interference is strictly prohibited. All users shall abide by the laws and regulations of the People's Republic of China when accessing or using this website.
</p>
    <p class="disclaimer">
  By accessing this website or clicking the button below, you affirm your <strong>full agreement</strong> with and 
  <strong>explicit support</strong> for all of the above <em>statements</em>, including 
  <strong>China's territorial integrity and sovereignty</strong>, as well as the commitment 
  <strong>not to compromise network or server security</strong>.
</p>
  <div class="enter-quired"><?php echo $tips;?></div>
  <div class="enter-quired" id="mode-indicator"><svg class="svg-light-wrap" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" width="24"><path  class="svg-light" d="M512 51.2a387.2 387.2 0 0 0-386.752 386.688 386.816 386.816 0 0 0 216.704 347.264v108.224c0 30.08 24.448 54.528 54.528 54.528h34.944a50.176 50.176 0 0 0 43.264 24.896h74.752a50.304 50.304 0 0 0 43.264-24.896h34.944c30.08 0 54.464-24.448 54.464-54.528v-108.224a386.816 386.816 0 0 0 216.704-347.264A387.2 387.2 0 0 0 512.064 51.2z m115.584 845.504h-51.328c-0.576-0.064-1.088-0.384-1.664-0.384s-1.152 0.32-1.792 0.384H396.416a3.328 3.328 0 0 1-3.328-3.328v-17.664h237.696v17.664a3.264 3.264 0 0 1-3.264 3.328z m18.624-151.36a25.6 25.6 0 0 0-15.36 23.424v55.744H537.6v-179.776l143.296-133.184a25.6 25.6 0 0 0-34.816-37.504L512.64 598.08 375.424 461.824a25.6 25.6 0 0 0-36.096 36.352L486.4 644.288v180.288H393.088v-55.744a25.472 25.472 0 0 0-15.36-23.424 335.744 335.744 0 0 1-201.344-307.456c0-185.024 150.528-335.488 335.552-335.488s335.552 150.528 335.552 335.488a335.552 335.552 0 0 1-201.344 307.456z"></path></svg><span>Flashbang ahead!</span></div>
    <div style="margin:2rem auto;text-align:center;">
      <a id="agree-btn" data-key="<?php echo $linkkey;?>" href="x.php?access=true<?php echo $linkkey;?>" class="button">Agree and Access</a>
<p style="font-size:12px;color:#424242;text-align:right;margin-top:64px;"><em>Media playback may occasionally stutter due to service environment limitations.</em> This is a known condition and not related to the website's development.</p>
    </div>
  </div>
  <script>
const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
const indicator = document.getElementById('mode-indicator');

const button = document.querySelector('a.button');
const overlay = document.getElementById('fade-overlay');
if (!isDarkMode) {
  indicator.style.display = 'flex';
} 
button.addEventListener('click', function (e) {
  e.preventDefault();
  const link = this.href;

  if (isDarkMode) {
    // 深色模式，直接跳转
    window.location.href = link;
  } else {
    // 非深色模式：预加载 + 动画过渡
    overlay.classList.add('show');
    setTimeout(() => {
      window.location.href = link;
    }, 10000); // 跳转延迟（同步动画时间）
  }
});
</script>
</body>
</html>
