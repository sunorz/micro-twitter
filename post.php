<?php

// ==================== IP白名单检查 ====================
$host_ip = preg_replace('/:.*/', '', $_SERVER['HTTP_HOST']);
$ip_whitelisted = in_array($host_ip, [
    '127.0.0.1',
    '172.32.6.33',
    '192.168.194.161',
    'localhost'
]);


// 设置session的过期时间为30分钟（单位：秒）
ini_set('session.gc_maxlifetime', 1800);

// 同时建议设置cookie的过期时间，保持与session一致
session_set_cookie_params(1800);
// 启动会话
session_start();
// 获取并清除会话消息
require_once 'config_auth.php';
$today = date('Y-m-d');
if(!$ip_whitelisted&&(!isset($_SESSION['dashboard'])||$_SESSION['dashboard'] != $fkkey)){
  header('Location: /auth/verify.php');
  die();
}
$_SESSION['havekey'] = $today;
$_SESSION['agree'] = $today;
$_SESSION['dashboard'] = $fkkey;

function randomGibberish() {
    // 乱码池
    $pool = '卝丼厹亍凷圠氶尐顟卂冎乑灮卜讟鞻騳匁屮钃騱朰鰪灪虌夨巜鸜叓支鷼鼺齾忈饐齈溪叒叏月吂纞厵冃氷爩籱饙容忢氺厽厼爨尣龗改匡丮乇鱻冄式未廴吇彡锟斤拷烫卬厸刈仂勼卮圢屴氿仈叐卋';

    // 随机长度 8~20
    $length = mt_rand(8, 20);
    
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPos = mt_rand(0, mb_strlen($pool, 'UTF-8') - 1);
        $result .= mb_substr($pool, $randomPos, 1, 'UTF-8');
    }
    
    // 将结果打乱
    $chars = preg_split('//u', $result, -1, PREG_SPLIT_NO_EMPTY);
    shuffle($chars);
    
    return implode('', $chars);
}

// 完整的 session 清除流程
function completeSessionDestroy() {
    // 1. 清除所有 session 变量
    $_SESSION = [];
    
    // 2. 如果要更彻底地清除，可以使用 session_unset()
    // session_unset(); // 在 PHP 5.6+ 中，$_SESSION = [] 已经足够
    
    // 3. 销毁 session 数据
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    
    // 4. 清除 session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // 5. 重置 session 数组
    $_SESSION = array();
}

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (isset($_POST['clear_session'])) {
ob_start(); // 开启输出缓冲
completeSessionDestroy();
ob_clean(); 
    header('Location: /');
    exit();
}
    // 获取表单数据
    $contentType = $_POST['content_type'] ?? '';
    $textContent = $_POST['text_content'] ?? '';
    $textContent = preg_replace_callback(
      // 仅匹配无属性的<code>、<pre>、<kbd>标签
      '#<(code|pre|kbd)>([\s\S]*?)</\1>#i',
      function ($matches) {
          $tagName = $matches[1]; // 标签名（code、pre或kbd）
          $innerContent = $matches[2]; // 标签内的所有内容（包括其他标签和特殊字符）
          
          // 对内部所有内容进行转义
          $escapedContent = htmlspecialchars($innerContent, ENT_QUOTES, 'UTF-8');
          
          // 保留原始标签结构，替换内部内容
          return "<{$tagName}>{$escapedContent}</{$tagName}>";
      },
      $textContent
  );

    $shortText = randomGibberish().'...';

  

        // 获取当前日期时间
        $curtimestamp = time();
        $salt = 'CUSTOM_SALT';
        $hash_crc32 = sprintf('%08x', crc32($curtimestamp . $salt));
        $currentDate = date("Y/m/d H:i:s",$curtimestamp);
        $shortDate = date("ymd");
        $tweetId = substr(sha1($curtimestamp . $salt), 0, 8); // 8位十六进制
        $curtimestamp = substr($hash_crc32, 0, 4) . '-' . substr($hash_crc32, -4);

        // 根据类型生成不同内容
        switch ($contentType) {
            case "文字":
                $insertContent = <<<HTML
                <div class="tweet" data-tweet-id="{$tweetId}">                    
                        <div class="tweet-content">
                            <div class="tweet-header">                                
                                <span class="time">{$currentDate}</span>
                                <span class="more-options">...</span>
                            </div>
                            <div class="tweet-text">
                                {$textContent}
                            </div><a class="trans-button" href="javascript:void(0)">Translate</a><div class="tweet-text trans"><em>I'll translate it when I have time.</em></div>
                        </div>
                </div>
                HTML;
               
                $insertContent2 = <<<HTML
                     <blockquote><p>{$shortText}</p>
                        <p style="font-size:80%;opacity: 0.5;"><em>Translator not awake.</em></p>
    
                        </blockquote>
                        <div class="quote-source">{$curtimestamp}</div>
                 HTML;
                break;
            case "图片":
                $insertContent = <<<HTML
                <div class="tweet" data-tweet-id="{$tweetId}">                    
                    <div class="tweet-content">
                        <div class="tweet-header">                            
                            <span class="time">{$currentDate}</span>
                            <span class="more-options">...</span>
                        </div>
                        <div class="tweet-text">
                            {$textContent}
                        </div><a class="trans-button" href="javascript:void(0)">Translate</a><div class="tweet-text trans">Share image.</div>
                        <div class="media-container">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="images/timgs/{$shortDate}-1.jpg" alt="untitled" class="tweet-image lazyload">
                            <div class="media-c">-</div>
                        </div>
                    </div>
                </div>
                HTML;
                
                 $insertContent2 = <<<HTML
                    <blockquote><p>{$shortText} <span style="font-style:normal;">[IMG]</span></p>
                     <p style="font-size:80%;opacity: 0.5;"><em>Translator not awake.</em></p>
                    </blockquote>
                    <div class="quote-source">{$curtimestamp}</div>
                HTML;
                break;
            case "音频":
                $insertContent = <<<HTML
                <div class="tweet" data-tweet-id="{$tweetId}">                    
                    <div class="tweet-content">
                        <div class="tweet-header">                            
                            <span class="time">{$currentDate}</span>
                            <span class="more-options">...</span>
                        </div>
                        <div class="tweet-text">
                            {$textContent}
                        </div><a class="trans-button" href="javascript:void(0)">Translate</a><div class="tweet-text trans">Share track.</div>
                          <div class="media-container" style="max-width:150px;">
                <div class="player">
                 <div class="play-icon">
                 <svg viewBox="0 0 100 100" width="60" height="60" xmlns="http://www.w3.org/2000/svg">
                 <path class="playsvg" d=" M 20 15 Q 20 10 25 13 L 85 48 Q 90 50 85 52 L 25 87 Q 20 90 20 85 Z"/></svg></div>
                 <div class="bars">
                 <div class="bar"></div><div class="bar"></div><div class="bar"></div><div class="bar"></div><div class="bar"></div>
                  </div>
                  <audio data-media="/audio/{$shortDate}-1.mp3"></audio>
                  </div><div class="media-c" style="border-top:var(--md-outline) 1px solid;">Track metadata pending...</div>
                         </div>
                         </div>
                      </div>
                HTML;
                
                $insertContent2 = <<<HTML
                    <blockquote><p>{$shortText} <span style="font-style:normal;">[AUDIO]</span></p>
                     <p style="font-size:80%;opacity: 0.5;"><em>Translator not awake.</em></p>
                    </blockquote>
                    <div class="quote-source">{$curtimestamp}</div>
                 HTML;
                break;
            case "视频":
                $insertContent = <<<HTML
                <div class="tweet" data-tweet-id="{$tweetId}">
                    <div class="tweet-content">
                        <div class="tweet-header">                            
                            <span class="time">{$currentDate}</span>
                            <span class="more-options">...</span>
                        </div>
                        <div class="tweet-text">
                            {$textContent}
                        </div><a class="trans-button" href="javascript:void(0)">Translate</a><div class="tweet-text trans">Share video.</div>
                        <div class="media-container">
                            <video controls class="tweet-video" poster="images/timgs/{$shortDate}-1.jpg">
                                <source src="#" type="video/mp4">
                                    Your browser does not support the video tag.
                            </video>
                            <div class="media-c">-</div>
                        </div>
                    </div>
                </div>
                HTML;
                
                $insertContent2 = <<<HTML
                    <blockquote><p>{$shortText} <span style="font-style:normal;">[VIDEO]</span></p>
                     <p style="font-size:80%;opacity: 0.5;"><em>Translator not awake.</em></p>
                    </blockquote>
                    <div class="quote-source">{$curtimestamp}</div>
                 HTML;
                break;
            default:
                break;
        }
        

            // 主HTML文件路径
            $htmlFile = 'x.php';            
            // 处理主文件 x.php
            if (file_exists($htmlFile)) {
                $originalContent = file_get_contents($htmlFile);
            }
            $newContent = str_replace('<!-- INSERT_HERE -->', "<!-- INSERT_HERE -->\n".$insertContent, $originalContent);   
            
             // donate.html路径
            $htmlFile2 = 'donate.html';            
            // 处理文件 donate.html
            if (file_exists($htmlFile2)) {
                $originalContent2 = file_get_contents($htmlFile2);
                // 正则匹配 <!-- INSERT_HERE --> 和 <!-- EOF --> 之间的所有内容（包括换行）
                $pattern = '/<!-- INSERT_HERE -->([\s\S]*?)<!-- EOF -->/';
            }
            // 替换中间内容，但保留标记
            $newContent2 = preg_replace($pattern,"<!-- INSERT_HERE -->\n" . $insertContent2 . "\n<!-- EOF -->",$originalContent2);  
   
            // 写入两个文件
            $writeMain = file_put_contents($htmlFile, $newContent);   
            $writeDonate = file_put_contents($htmlFile2, $newContent2); 
            
            if ($writeMain === false || $writeDonate === false) {
                exit();
            } else {
                header("Location: /post.php");
                exit();
            }
        }
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta name="color-scheme" content="light dark">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take out the trash</title>
    <link rel="stylesheet" href="assets/post.min.css?v=2.3.29">
    <style>
        #result{
            width:100%;text-align:center;display:flex;justify-content:center;gap:10px;line-height:30px;color:var(--qq-text);
        }
        .qq-button:disabled {
    background: #ccc;  /* 灰色背景 */
    color: #999;  /* 灰色文字 */
    cursor: not-allowed;  /* 禁用鼠标指针 */
    opacity: 0.5;  /* 使按钮变得半透明 */
}
 .qq-button:disabled:hover {
    box-shadow : none;
}
.qq-avatar-container{
    position:relative;
}
#status{
  position: absolute;
  display:inline-block;
  top:calc(50% + 12px);
  left:calc(50% + 18px);
  z-index:999;
  width:14px;  
}
#status img{width:100%;}
.copy-target {
    word-break: break-all;
    display: inline-block;
    border-radius: 5px;
    <?php if(!$ip_whitelisted){?>
    background: #484848;
    color: #fff;
    padding: 2px 5px;
    user-select: none;
    text-transform: uppercase;
    box-shadow: 0 3px 6px rgba(0,0,0,0.4); 
    text-shadow: 1px 1px 2px rgba(0,0,0,0.6); 
     cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    <?php 
    }
    else{
    ?>
    color:#111;
    <?php
    }
    ?>
}

.inline{
    display:inline-block !important;
    margin-right:0.5rem;
    line-height:27.33px;
}
    </style>
</head>
<body class="normal-layout">
    <div class="qq-window">
        <!-- 标题栏 -->
        <div class="qq-titlebar">        
            <div class="qq-icon"><img src="images/qq-icon.webp"></div><div class="qq-titlebar-text">Check in trash</div>
            <div class="qq-titlebar-buttons">
                <a href="#" class="qq-titlebar-button minimize">_</a>
                <a href="#" class="qq-titlebar-button maximize"><svg width="13"  id="layout1" data-name="layout1" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 173.34 173.34">
  <rect
    x="5.5"
    y="5.5"
    width="162.34"
    height="162.34"
    fill="none"
    stroke="#22201e"
    stroke-miterlimit="10"
    stroke-width="11"
  />
  <rect
    x="5.5"
    y="10.05"
    width="162.34"
    height="24.68"
    fill="#22201e"
    stroke="#22201e"
    stroke-miterlimit="10"
    stroke-width="11"
  />
</svg>
</a>
                <a href="#" class="qq-titlebar-button close">×</a>
            </div>
            <div class="qq-decor-line"></div>
        </div>
        
        <!-- 内容区域 -->
        <div class="qq-content">
                     <div class="qq-avatar-container">
                <div class="qq-avatar"></div>
                <div id="status">
                <?php if($open)
                  {
                    echo '<img src="images/online.gif" alt="online">';
                  }else{
                    echo '<img src="images/hide.gif" alt="hide">';
                  }
                ?>              
                </div>
            </div>
            <form method="post">
            <div class="label-group">
                <div class="qq-form-group">
                    <span class="qq-label inline">Today key:</span>
                    <span class="copy-target" title="Click to copy.">
                        <?php echo strtotime('today 1:30'); ?>
                    <span style="display:none" data-copy="https://microx.kkii.org/?key=<?php echo $allowed_key?>"></span>
                  </span>
                </div>
                <div class="qq-form-group">
                    <span class="qq-label">Category:</span>
                   <div class="custom-select" tabindex="0" aria-haspopup="listbox" aria-expanded="false" role="combobox" aria-owns="content_type_list" aria-label="内容类型选择">
  <div class="selected" id="selected_value" aria-live="polite"><img src="images/icon-text.png">文字</div>
  <ul id="content_type_list" role="listbox">
    <li data-value="文字" class="selected" role="option" aria-selected="true"><img src="images/icon-text.png">文字</li>
    <li data-value="图片" role="option" aria-selected="false"><img src="images/icon-image.png">图片</li>
    <li data-value="音频" role="option" aria-selected="false"><img src="images/icon-audio.png">音频</li>
    <li data-value="视频" role="option" aria-selected="false"><img src="images/icon-video.png">视频</li>
  </ul>
  <input type="hidden" name="content_type" id="content_type" value="文字" required>
</div>
  
                </div>
                
                <div class="qq-form-group">
                    <label for="text_content" class="qq-label">Trash contents:</label>
                    <div id="result">[TIPS]</div>
                    <textarea id="text_content" name="text_content" class="qq-textarea" required></textarea>                    
                </div>
                </div>
                <div class="qq-button-container">
                 <button type="submit" class="qq-button"><svg t="1746848087309" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3603" width="32" height="32"><path d="M657.3 901H425.4C349.1 901 287 839 287 762.6v-427c0-19.4 15.8-35.2 35.2-35.2 19.4 0 35.2 15.8 35.2 35.2v427.1c0 37.5 30.5 68 68 68h231.9c37.5 0 68-30.5 68-68V335.6c0-19.4 15.8-35.2 35.2-35.2 19.4 0 35.2 15.8 35.2 35.2v427.1c0 76.3-62.1 138.3-138.4 138.3z m0 0" p-id="3604" fill="#2d404e"></path><path d="M625.3 359.1c-19.4 0-35.2-15.8-35.2-35.2v-49.1c0-10.6-8.6-19.2-19.2-19.2h-59.1c-10.6 0-19.2 8.6-19.2 19.2v49.1c0 19.4-15.8 35.2-35.2 35.2-19.4 0-35.2-15.8-35.2-35.2v-49.1c0-49.4 40.2-89.6 89.6-89.6h59.1c49.4 0 89.6 40.2 89.6 89.6v49.1c0 19.5-15.7 35.2-35.2 35.2z m0 0" p-id="3605" fill="#2d404e"></path><path d="M827.6 365.3H255.1c-19.4 0-35.2-15.8-35.2-35.2 0-19.4 15.8-35.2 35.2-35.2h572.5c19.4 0 35.2 15.8 35.2 35.2 0 19.4-15.8 35.2-35.2 35.2zM470 748.1c-19.4 0-35.2-15.8-35.2-35.2V455.8c0-19.4 15.8-35.2 35.2-35.2 19.4 0 35.2 15.8 35.2 35.2v257.1c0 19.4-15.8 35.2-35.2 35.2z m142.7 0c-19.4 0-35.2-15.8-35.2-35.2V455.8c0-19.4 15.8-35.2 35.2-35.2 19.4 0 35.2 15.8 35.2 35.2v257.1c0 19.4-15.8 35.2-35.2 35.2z m0 0" p-id="3606" fill="#2d404e"></path></svg><span style="
                   transform:translateY(2px);display:inline-block;">Take out</span></button>
                </div>                
            </form>
            <form method="post" style="position: absolute;left: 20px;">
                 <button type="submit" class="qq-button" name="clear_session" style="background:#fbbc04;"><svg t="1756100496234" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5041"  width="18" height="18"><path d="M570.107 47.258H453.903V572.78h116.199V47.258z m208.86 86.886l-81.316 80.794c92.872 63.457 162.427 173.143 162.427 300.052 0 190.48-156.544 346.286-348.175 346.286-191.427 0-348.176-155.807-348.176-346.286 0-126.91 69.76-236.595 168.31-294.277l-86.994-86.569C129.055 220.713 47.733 359.188 47.733 514.99c0 254.04 209.076 461.752 464.165 461.752 255.298 0 464.368-207.708 464.368-461.752 0.005-155.802-81.31-294.277-197.299-380.846z" p-id="5042" fill="#2d404e"></path></svg><span style="
                   transform:translateY(2px);display:inline-block;margin-left:0.7rem;">Logout</span></button>
              
      </form>
        </div>
        
        <!-- 底部信息 -->
        <div class="qq-footer">
            &copy; 高级不可回收物 , <script>const date = new Date();
            document.write(date.getFullYear());</script>
        </div>
    </div>
    
    <script>   
    const copyTarget = document.querySelector('.copy-target');
    <?php
    if($ip_whitelisted){
        ?>
        copyTarget.textContent = '<?php echo $allowed_key;?>';
        <?php
    }
    else
    {
    ?>

// 初始化存储原始文字
if (!copyTarget.dataset.originalText) {
  copyTarget.dataset.originalText = copyTarget.firstChild.textContent.trim();
}

copyTarget.addEventListener('click', async function () {
  try {
    const realContent = this.querySelector('[data-copy]').dataset.copy;
    await navigator.clipboard.writeText(realContent);

    // 提供视觉反馈
    this.firstChild.textContent = "Copied!";

    // 恢复成固定的原始文字
    setTimeout(() => {
      this.firstChild.textContent = this.dataset.originalText;
    }, 2000);
  } catch (err) {
    console.error("请在https环境下复制");
  }
});
<?php
}
?>

        // 获取所有需要的元素
const qqWindow = document.querySelector(".qq-window");
const titlebar = document.querySelector(".qq-titlebar-text");
const content = document.querySelector('.qq-content');
const footer = document.querySelector('.qq-footer');
const minimizeBtn = document.querySelector('.qq-titlebar-button.minimize');
const maximizeBtn = document.querySelector('.qq-titlebar-button.maximize');

// 保存原始状态（包括标题文本）
const originalTitleText = titlebar.innerText;
const originalWindowWidth = getComputedStyle(qqWindow).width;
const originalContentDisplay = getComputedStyle(content).display;
const originalFooterDisplay = getComputedStyle(footer).display;

// 最小化功能
minimizeBtn.addEventListener('click', function(e) {
  e.preventDefault();
  
  // 切换到最小化样式类
  document.body.classList.remove('normal-layout');
  document.body.classList.add('minimized-layout');
  
  // 其他最小化操作
  this.style.display = 'none';
  maximizeBtn.style.display = 'flex';
  qqWindow.style.width = 'auto';
  titlebar.innerText = 'Check...';
  titlebar.style.color = '#999';
  content.style.display = 'none';
  footer.style.display = 'none';
});

// 最大化/还原功能
maximizeBtn.addEventListener('click', function(e) {
  e.preventDefault();
  
  // 恢复到正常样式类
  document.body.classList.remove('minimized-layout');
  document.body.classList.add('normal-layout');
  
  // 恢复原始状态
  minimizeBtn.style.display = 'flex';
  this.style.display = 'none';
  qqWindow.style.width = originalWindowWidth;
  titlebar.innerText = originalTitleText;
  content.style.display = originalContentDisplay;
  footer.style.display = originalFooterDisplay;
  titlebar.style.color = '';
});
document.querySelector('.qq-titlebar-button.close')?.addEventListener('click', function (e) {
    e.preventDefault(); // 阻止立即跳转
    qqWindow.style.display = 'none';
    // 1.5 秒后再跳转
    setTimeout(() => {
      window.location.href = '/x.php';
    }, 2000);
  });
   const customSelect = document.querySelector('.custom-select');
  const selected = customSelect.querySelector('.selected');
  const list = customSelect.querySelector('ul');
  const hiddenInput = customSelect.querySelector('input[type=hidden]');
  const options = list.querySelectorAll('li');

  // 切换下拉列表显示/隐藏
  selected.addEventListener('click', () => {
    const expanded = customSelect.getAttribute('aria-expanded') === 'true';
    customSelect.setAttribute('aria-expanded', String(!expanded));
    list.classList.toggle('open');
  });

  // 选中某项
  options.forEach(option => {
    option.addEventListener('click', () => {
      // 取消之前选中
      options.forEach(opt => {
        opt.classList.remove('selected');
        opt.setAttribute('aria-selected', 'false');
      });

      // 选中当前
      option.classList.add('selected');
      option.setAttribute('aria-selected', 'true');

      // 更新显示和隐藏输入
      selected.innerHTML = option.innerHTML;

      hiddenInput.value = option.getAttribute('data-value');

      // 收起列表
      list.classList.remove('open');
      customSelect.setAttribute('aria-expanded', 'false');
    });
  });

  // 点击外部关闭下拉
  document.addEventListener('click', (e) => {
    if (!customSelect.contains(e.target)) {
      list.classList.remove('open');
      customSelect.setAttribute('aria-expanded', 'false');
    }
  });

  // 键盘支持（简单版本）
  customSelect.addEventListener('keydown', (e) => {
    const open = list.classList.contains('open');
    const selectedOption = list.querySelector('li.selected');
    let index = Array.from(options).indexOf(selectedOption);

    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      if (open) {
        options[index].click();
      } else {
        list.classList.add('open');
        customSelect.setAttribute('aria-expanded', 'true');
      }
    } else if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (!open) {
        list.classList.add('open');
        customSelect.setAttribute('aria-expanded', 'true');
      } else {
        index = (index + 1) % options.length;
        options[index].focus();
      }
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (!open) {
        list.classList.add('open');
        customSelect.setAttribute('aria-expanded', 'true');
      } else {
        index = (index - 1 + options.length) % options.length;
        options[index].focus();
      }
    } else if (e.key === 'Escape') {
      list.classList.remove('open');
      customSelect.setAttribute('aria-expanded', 'false');
      selected.focus();
    }
  });

  // 让列表项可聚焦
  options.forEach(option => option.setAttribute('tabindex', '-1'));
function alignButtons() {
  const btn1 = document.querySelector('.qq-button-container button'); // 按钮1
  const form2 = document.querySelectorAll('form')[1]; // 第二个form
  if (!btn1 || !form2) return;

  const rect1 = btn1.getBoundingClientRect();
  const parentRect = btn1.offsetParent.getBoundingClientRect();
  const relativeTop = rect1.top - parentRect.top;

  form2.style.position = 'absolute';
  form2.style.top = relativeTop + 'px';

  // 定位完成后显示
  btn1.style.visibility = 'visible';
  form2.style.visibility = 'visible';
}

window.addEventListener('load', alignButtons);
window.addEventListener('resize', alignButtons);
const textarea = document.querySelector('textarea');

const ro = new ResizeObserver(() => {
  alignButtons();
});

ro.observe(textarea);


// 获取表单和结果容器元素
const form = document.querySelector('form[method="post"]:not([name="clear_session"])');
const resultDiv = document.getElementById('result');

if (form && resultDiv) {
  form.addEventListener('submit', async function(e) {
    
    e.preventDefault(); // 阻止同步提交
    resultDiv.textContent = ''; // 显示加载状态
    resultDiv.style.opacity = '0';
    // 收集表单数据
    const formData = new FormData(this);
    try {
      // 发送异步请求
      const response = await fetch(window.location.href, {
        method: 'POST',
        body: formData
      });
      
      if (response.ok) {
        // 显示成功信息
        resultDiv.style.opacity = '1';
        resultDiv.innerHTML = `<div style="width:100%;text-align:left;display:flex;justify-content:center;gap:10px;line-height:30px;"><svg t="1756550141207" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2356" width="30" height="30"><path d="M512 0C229.004 0 0 229.004 0 512s229.004 512 512 512 512-229.004 512-512S794.996 0 512 0z m260.655 425.425L493.382 704.698c-5.586 5.586-13.033 9.31-21.411 9.31-10.24 1.861-20.48-0.932-27.927-8.379L268.102 528.756a30.906 30.906 0 0 1 0-43.752l14.894-14.895c12.102-12.102 31.651-12.102 43.753 0l141.498 141.498 244.83-244.829c12.101-12.102 31.65-12.102 43.752 0l15.826 14.895c12.101 12.102 12.101 31.65 0 43.752z" fill="#afcd50" p-id="2357"></path></svg>Tweet posted successfully.</div>`;
        document.querySelector('textarea').value = '';
        document.querySelector('textarea').focus();

        const button = document.querySelector('.qq-button');
        setTimeout(() => {
          resultDiv.style.opacity = '0';
          }, 5000);
          button.disabled = true;
          setTimeout(() => {
          button.disabled = false;
          }, 5000);

      } else {
        // 显示错误信息
       document.querySelector('textarea').focus();
       resultDiv.style.opacity = '1';
       resultDiv.innerHTML = `<div style="width:100%;text-align:left;display:flex;justify-content:center;gap:10px;line-height:30px;"><svg t="1756550442373" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3488" width="30" height="30"><path d="M512 0C229.205333 0 0 229.205333 0 512s229.205333 512 512 512 512-229.205333 512-512S794.794667 0 512 0z m0 796.458667A56.917333 56.917333 0 1 1 511.957333 682.666667 56.917333 56.917333 0 0 1 512 796.458667z m54.186667-227.797334h0.128a60.501333 60.501333 0 0 1-53.802667 55.893334c2.048 0.256 3.882667 1.152 5.973333 1.152h-11.818666c2.048 0 3.84-0.981333 5.845333-1.109334a59.093333 59.093333 0 0 1-53.162667-55.893333l-13.056-284.16a54.314667 54.314667 0 0 1 54.613334-57.045333h26.282666a52.992 52.992 0 0 1 54.186667 57.002666l-15.146667 284.16z" fill="#B94343" p-id="3489"></path></svg>Tweet failed.</div>`;
      }
    } catch (error) {
    document.querySelector('textarea').focus();
      resultDiv.style.color = '#ea4335';
      resultDiv.style.opacity = '1';
      console.error('请求错误:', error);
      resultDiv.innerHTML = `<div style="width:100%;text-align:left;display:flex;justify-content:center;gap:10px;line-height:30px;"><svg t="1756550442373" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3488" width="30" height="30"><path d="M512 0C229.205333 0 0 229.205333 0 512s229.205333 512 512 512 512-229.205333 512-512S794.794667 0 512 0z m0 796.458667A56.917333 56.917333 0 1 1 511.957333 682.666667 56.917333 56.917333 0 0 1 512 796.458667z m54.186667-227.797334h0.128a60.501333 60.501333 0 0 1-53.802667 55.893334c2.048 0.256 3.882667 1.152 5.973333 1.152h-11.818666c2.048 0 3.84-0.981333 5.845333-1.109334a59.093333 59.093333 0 0 1-53.162667-55.893333l-13.056-284.16a54.314667 54.314667 0 0 1 54.613334-57.045333h26.282666a52.992 52.992 0 0 1 54.186667 57.002666l-15.146667 284.16z" fill="#B94343" p-id="3489"></path></svg>Other error.</div>`;
    }
  });
}
document.addEventListener('DOMContentLoaded', () => {
    const textareas = document.querySelectorAll('textarea');

    textareas.forEach(textarea => {
        // 点击时确保 focus
        textarea.addEventListener('click', () => {
            setTimeout(() => textarea.focus(), 0);
        });

        // 内容被清空后也保持 focus
        textarea.addEventListener('input', () => {
            if (textarea.value === '') {
                setTimeout(() => textarea.focus(), 0);
            }
        });

        // 移动端部分浏览器对 blur 后点击无效的补救
        textarea.addEventListener('touchend', () => {
            setTimeout(() => textarea.focus(), 0);
        });
    });
});

</script>
</body>
</html>