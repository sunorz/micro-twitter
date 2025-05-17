<?php
// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $contentType = $_POST['content_type'] ?? '';
    $textContent = $_POST['text_content'] ?? '';
    
    // 验证输入
    if (empty($contentType) || empty($textContent)) {
        $_SESSION['error'] = "请选择内容类型并输入内容";
    } elseif (mb_strlen($textContent, 'UTF-8') > 200) {
        $_SESSION['error'] = "内容不能超过200个汉字";
    } else {
        // 获取当前日期时间
        $currentDate = date("Y/m/d H:i:s");
        $shortDate = date("ymd");
        
        // 根据类型生成不同内容
        switch ($contentType) {
            case "文字":
                $insertContent = <<<HTML
                <div class="tweet">                    
                        <div class="tweet-content">
                            <div class="tweet-header">                                
                                <span class="time">{$currentDate}</span>
                                <span class="more-options">...</span>
                            </div>
                            <div class="tweet-text">
                                {$textContent}
                            </div>
                        </div>
                </div>
                HTML;
                break;
            case "图片":
                $insertContent = <<<HTML
                <div class="tweet">                    
                    <div class="tweet-content">
                        <div class="tweet-header">                            
                            <span class="time">{$currentDate}</span>
                            <span class="more-options">...</span>
                        </div>
                        <div class="tweet-text">
                            {$textContent}
                        </div>
                        <div class="media-container">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="images/post/{$shortDate}-1.jpg" alt="untitled" class="tweet-image lazyload">
                            <div class="media-c">&copy;</div>
                        </div>
                    </div>
                </div>
                HTML;
                break;
            case "视频":
                $insertContent = <<<HTML
                <div class="tweet">
                    <div class="tweet-content">
                        <div class="tweet-header">                            
                            <span class="time">{$currentDate}</span>
                            <span class="more-options">...</span>
                        </div>
                        <div class="tweet-text">
                            {$textContent}
                        </div>
                        <div class="media-container">
                            <video controls class="tweet-video" poster="images/post/{$shortDate}-1.jpg">
                                <source src="videos/post/{$shortDate}-1.mp4" type="video/mp4">
                                    Your browser does not support the video tag.
                            </video>
                            <div class="media-c">none</div>
                        </div>
                    </div>
                </div>
                HTML;
                break;
                break;
            default:
                $_SESSION['error'] = "无效的内容类型";
                break;
        }
        
        if (!isset($_SESSION['error'])) {
            // HTML文件路径
            $htmlFile = 'index.html';
            
            // 读取原始内容（如果文件不存在则创建）
            if (!file_exists($htmlFile)) {
                $originalContent = '';
                file_put_contents($htmlFile, $originalContent);
            } else {
                $originalContent = file_get_contents($htmlFile);
            }
            
            // 替换标记位置
            $newContent = str_replace('<!-- INSERT_HERE -->', "<!-- INSERT_HERE -->\n".$insertContent , $originalContent);
            
            // 写回文件
            if (file_put_contents($htmlFile, $newContent) !== false) {
                $_SESSION['success'] = "内容已成功添加到HTML文件";
                // 重定向到当前页面，防止刷新重复提交
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } else {
                $_SESSION['error'] = "写入HTML文件失败";
            }
        }
    }
}

// 启动会话
session_start();
// 获取并清除会话消息
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take out the trash</title>
    <style>
        :root {
            --qq-blue: #0052D9;
            --qq-light-blue: #E6F3FF;
            --qq-title-blue: #0052D9;
            --qq-button-blue: #3684FF;
            --qq-button-hover: #5CACEE;
            --qq-border: #95B8E7;
            --qq-inner-border: #C9D8F2;
            --qq-text: #333333;
            --qq-title-text: white;
            --qq-button-text: white;
            --qq-bg-light: #F0F7FF;
            --qq-bg-dark: #2C3E50;
            --qq-border-dark: #4E6E8E;
            --qq-text-dark: #ECF0F1;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background-color: var(--qq-bg-light);
            color: var(--qq-text);
            transition: background-color 0.3s, color 0.3s;
        }
        
        /* 深色模式 */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1E293B;
                color: var(--qq-text-dark);
            }
            
            .qq-window {
                background-color: var(--qq-bg-dark);
                border-color: var(--qq-border-dark);
            }
            
            .qq-titlebar {
                background: linear-gradient(to right, #1A365D, #3B82F6);
            }
            
            .qq-content {
                background: linear-gradient(to bottom, #334155, #1E293B);
            }
            
            .qq-welcome {
                color: #93C5FD;
                border-bottom-color: var(--qq-border-dark);
            }
            
            .qq-input, .qq-textarea, .qq-select {
                background-color: #334155;
                border-color: var(--qq-border-dark);
                color: var(--qq-text-dark);
            }
            
            .qq-select {
                background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8"><path fill="%2394A3B8" d="M11.354.646a.5.5 0 0 1 0 .708L6.707 6.5a.5.5 0 0 1-.708 0L.646 1.354a.5.5 0 1 1 .708-.708L6 5.293 10.646.646a.5.5 0 0 1 .708 0z"/></svg>');
            }
            
            .qq-footer {
                background-color: #334155;
                border-top-color: var(--qq-border-dark);
                color: #94A3B8;
            }
            
            .qq-button {
                background: linear-gradient(to bottom, #3B82F6, #1D4ED8);
                border-color: #1E40AF;
            }
            
            .qq-button:hover {
                background: linear-gradient(to bottom, #60A5FA, #3B82F6);
            }
            
            .qq-button:active {
                background: linear-gradient(to bottom, #1D4ED8, #3B82F6);
            }
            
            .qq-error {
                background-color: #5B21B6;
                border-color: #8B5CF6;
                color: #C7D2FE;
            }
            
            .qq-success {
                background-color: #065F46;
                border-color: #10B981;
                color: #A7F3D0;
            }
        }
        
        /* QQ2008窗口样式 */
        .qq-window {
            max-width: 1024px;
            width:100%;
            background-color: white;
            border: 1px solid var(--qq-border);
            border-radius: 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.25);
            position: relative;
            overflow: hidden;
            transition: background-color 0.3s;
        }
        
        /* 标题栏样式 */
        .qq-titlebar {
            height: 36px;
            background: linear-gradient(to right, var(--qq-title-blue), #4190FF);
            color: var(--qq-title-text);
            display: flex;
            align-items: center;
            padding: 0 8px;
            user-select: none;
            position: relative;
        }     
              
        .qq-titlebar-text {
            font-size: 15px;
            font-weight: bold;
            margin-left:0.3em;
            text-transform: uppercase;
        }
        
        .qq-titlebar-buttons {
            margin-left: auto;
            display: flex;
        }
        
        .qq-titlebar-button {
            width: 28px;
            height: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 11px;
            color: var(--qq-title-text);
            text-decoration: none;
            margin-left: 1px;
            transition: background-color 0.2s;
            cursor: pointer;
        }
        
        .qq-titlebar-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .qq-titlebar-button.close:hover {
            background-color: #E81123;
        }
        
        /* QQ2008风格装饰线 */
        .qq-decor-line {
            height: 1px;
            background-color: #FFFFFF;
            opacity: 0.3;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        
        /* 内容区域 */
        .qq-content {
            padding: 20px;
            background: var(--qq-bg-gradient);
            transition: background 0.3s;
        }
        
        .qq-avatar-container {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .qq-avatar {
            width: 64px;
            height: 64px;
            border: 2px solid #FFFFFF;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            background-image: url('images/avatar.gif');
            background-size: cover;
            background-position: center;
            display: inline-block;
        }
        
        .qq-form-group {
            margin-bottom: 15px;
        }
        
        .qq-label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: var(--qq-text);
            transition: color 0.3s;
        }
        
        .qq-input, .qq-textarea, .qq-select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid var(--qq-inner-border);
            border-radius: 2px;
            font-size: 14px;
            color: var(--qq-text);
            background-color: white;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
            appearance: none;
        }
        
        .qq-input:focus, .qq-textarea:focus, .qq-select:focus {
            outline: none;
            border-color: var(--qq-button-blue);
            box-shadow: 0 0 3px rgba(54, 132, 255, 0.5);
        }
        
        .qq-select {
            background-image: url('data:image/svg+xml;charset=utf-8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8"><path fill="%23666666" d="M11.354.646a.5.5 0 0 1 0 .708L6.707 6.5a.5.5 0 0 1-.708 0L.646 1.354a.5.5 0 1 1 .708-.708L6 5.293 10.646.646a.5.5 0 0 1 .708 0z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding-right: 28px;
        }
        
        .qq-textarea {
            min-height: 120px;
            resize: vertical;
        }
        .qq-icon{
            display: inline-block;
    transform: translateY(4px);
        }
        .qq-button {
            background: linear-gradient(to bottom, #5CACEE, #3684FF);
            color: var(--qq-button-text);
            border: 1px solid var(--qq-blue);
            border-radius: 2px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.2);
            transition: all 0.2s;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
          }
        .qq-button svg{
            height:23px;
            vertical-align: middle;
        }
        .qq-button:hover {
            background: linear-gradient(to bottom, #79B8F5, #5CACEE);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        .qq-button:active {
            background: linear-gradient(to bottom, #3684FF, #5CACEE);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        .qq-button-container {
            margin-top: 20px;
            text-align:right;
        }
        
        .qq-error {
            color: #D8000C;
            background-color: #FFBABA;
            padding: 10px;
            border: 1px solid #FFD2D2;
            border-radius: 2px;
            font-size: 14px;
            margin-bottom: 15px;
            transition: all 0.3s;
            display: none;
        }
        
        .qq-success {
            color: #270;
            background-color: #DFF2BF;
            padding: 10px;
            border: 1px solid #BBDF8D;
            border-radius: 2px;
            font-size: 14px;
            margin-bottom: 15px;
            transition: all 0.3s;
            display: none;
        }
        
        /* 底部信息 */
        .qq-footer {
            padding: 8px 15px;
            background-color: #F0F7FF;
            border-top: 1px solid var(--qq-inner-border);
            font-size: 12px;
            color: #666;
            text-align: right;
            transition: all 0.3s;
        }
        
        /* 响应式设计 */
        @media (max-width: 480px) {
            .qq-window {
                width: 100%;
            }
            
            .qq-content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="qq-window">
        <!-- 标题栏 -->
        <div class="qq-titlebar">        
            <div class="qq-icon"><svg t="1746847106578" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1568" width="32" height="32"><path d="M446.31 135.36c59.14-32.57 133.57-17.46 177.33 32.42 20.22 23.05 36.34 49.86 53.08 75.76 54.92 85.02 109.37 170.34 163.78 255.69 14.68 23.02 11.74 37.13-10.11 53.81-3.85 2.94-7.82 5.8-12.04 8.17-32.65 18.35-40.83 16.37-60.85-14.6-71.42-110.44-143.06-220.74-214.09-331.43-20.69-32.24-49.06-54.85-82.48-72.11-4.21-2.17-8.4-4.43-14.62-7.71zM165.01 653.32c-20.77-42.48-23.41-82.99-0.47-119.52C213.2 456.32 265.31 381 315.98 304.77c28.85-43.4 58.17-86.5 86.11-130.47 8.39-13.21 18.19-15.94 29.93-9.09 19.47 11.36 37.87 24.56 55.3 36.05-68.12 106.25-134 208.45-199.16 311.1-14.88 23.45-27.1 48.58-41 72.66-2.48 4.3-6.62 8.65-11.05 10.66-27.47 12.48-52.05 28.44-71.1 57.64zM702.39 785.54c-47.62 32.71-93.48 64.21-141.88 97.46-0.68-7.98-1.5-13.09-1.5-18.2-0.08-70.43 0.39-140.87-0.36-211.3-0.19-17.39 5.33-23.46 22.96-23.11 47.95 0.96 95.97-0.91 143.89 0.67 52.88 1.75 95.25-19.04 131.85-54.99 3.36-3.3 6.85-6.48 10.48-9.92 14.29 27.24 6.02 74.23-20.03 101.21-28.96 29.99-63.04 50.9-107.28 49.31-23.04-0.83-46.14 0.14-69.21-0.16-11.27-0.15-17.1 3.87-16.39 15.94 0.89 15.09-4.26 34.43 3.27 44.05 6.65 8.49 27.13 6.14 44.2 9.04zM468.81 880.48c-45.78-32.29-91.55-64.57-141.87-100.06h48.12v-60.93c-62.01-2.91-128.66 10.55-183.11-37.54 14.52-26.2 37.09-42.29 60.66-57.17 2.63-1.66 6.9-1.38 10.32-1.06 32.99 3.08 65.9 7.84 98.95 9.16 32.06 1.28 64.27-0.59 96.39-1.8 11.18-0.42 17.17 2.18 17.13 14.68-0.23 77.57-0.12 155.15-0.12 232.72-2.16 0.66-4.31 1.33-6.47 2z" fill="#ffffff" p-id="1569"></path></svg></div><div class="qq-titlebar-text">Check in trash</div>
            <div class="qq-titlebar-buttons">
                <a href="#" class="qq-titlebar-button minimize">_</a>
                <a href="#" class="qq-titlebar-button maximize">□</a>
                <a href="#" class="qq-titlebar-button close">×</a>
            </div>
            <div class="qq-decor-line"></div>
        </div>
        
        <!-- 内容区域 -->
        <div class="qq-content">
            <div class="qq-avatar-container">
                <div class="qq-avatar"></div>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="qq-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="qq-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="qq-form-group">
                    <label for="content_type" class="qq-label">Category:</label>
                    <select id="content_type" name="content_type" class="qq-select" required>
                        <option value="文字" selected>文字</option>
                        <option value="图片">图片</option>
                        <option value="视频">视频</option>
                    </select>
                </div>
                
                <div class="qq-form-group">
                    <label for="text_content" class="qq-label">Trash contents:</label>
                    <textarea id="text_content" name="text_content" maxlength="200" class="qq-textarea" required></textarea>                    
                </div>
                
                <div class="qq-button-container">
                    <button type="submit" class="qq-button"><svg t="1746848087309" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3603" width="32" height="32"><path d="M657.3 901H425.4C349.1 901 287 839 287 762.6v-427c0-19.4 15.8-35.2 35.2-35.2 19.4 0 35.2 15.8 35.2 35.2v427.1c0 37.5 30.5 68 68 68h231.9c37.5 0 68-30.5 68-68V335.6c0-19.4 15.8-35.2 35.2-35.2 19.4 0 35.2 15.8 35.2 35.2v427.1c0 76.3-62.1 138.3-138.4 138.3z m0 0" p-id="3604" fill="#ffffff"></path><path d="M625.3 359.1c-19.4 0-35.2-15.8-35.2-35.2v-49.1c0-10.6-8.6-19.2-19.2-19.2h-59.1c-10.6 0-19.2 8.6-19.2 19.2v49.1c0 19.4-15.8 35.2-35.2 35.2-19.4 0-35.2-15.8-35.2-35.2v-49.1c0-49.4 40.2-89.6 89.6-89.6h59.1c49.4 0 89.6 40.2 89.6 89.6v49.1c0 19.5-15.7 35.2-35.2 35.2z m0 0" p-id="3605" fill="#ffffff"></path><path d="M827.6 365.3H255.1c-19.4 0-35.2-15.8-35.2-35.2 0-19.4 15.8-35.2 35.2-35.2h572.5c19.4 0 35.2 15.8 35.2 35.2 0 19.4-15.8 35.2-35.2 35.2zM470 748.1c-19.4 0-35.2-15.8-35.2-35.2V455.8c0-19.4 15.8-35.2 35.2-35.2 19.4 0 35.2 15.8 35.2 35.2v257.1c0 19.4-15.8 35.2-35.2 35.2z m142.7 0c-19.4 0-35.2-15.8-35.2-35.2V455.8c0-19.4 15.8-35.2 35.2-35.2 19.4 0 35.2 15.8 35.2 35.2v257.1c0 19.4-15.8 35.2-35.2 35.2z m0 0" p-id="3606" fill="#ffffff"></path></svg><span style="
                   transform:translateY(2px);display:inline-block;">Take out</span></button>
                </div>
            </form>
        </div>
        
        <!-- 底部信息 -->
        <div class="qq-footer">
            &copy; 高级不可回收物 , <script>const date = new Date();
            document.write(date.getFullYear());</script>
        </div>
    </div>
    
    <script>   
        // 深色模式切换效果
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            // 添加深色模式过渡动画
            document.documentElement.style.transition = 'background-color 0.3s, color 0.3s';
        }
    </script>
</body>
</html>