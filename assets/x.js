
const date = new Date();
document.write(date.getFullYear());

function updateTimeElements() {
    const timeElements = document.querySelectorAll('span.time');
    const now = new Date();

    // 获取.notice元素
    const noticeElement = document.querySelector('.notice');

    // 检查是否需要显示通知（当前日期大于20日且推文少于5条）
    const currentDay = now.getDate();
    const tweetElements = document.querySelectorAll('.tweet');

    if (noticeElement) {
        if (currentDay > 20 && tweetElements.length < 5) {
            noticeElement.style.display = 'block'; // 或其他适当的显示方式
        } else {
            noticeElement.style.display = 'none';
        }
    }

    // 原有时间更新逻辑保持不变
    timeElements.forEach((element) => {
        const timeString = element.textContent.trim();
        const date = new Date(timeString.replace(/-/g, '/')); // 兼容 - 和 / 分隔符

        if (isNaN(date.getTime())) {
            return;
        }

        const diffMs = now - date;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);

        let displayTime;
        if (diffSec < 60) {
            displayTime = "Now";
        } else if (diffMin < 60) {
            displayTime = `${diffMin}m`;
        } else if (diffHour < 24) {
            displayTime = `${diffHour}h`;
        } else if (diffDay < 7) {
            displayTime = `${diffDay}d`;
        } else {
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            displayTime = `${year}/${month}/${day}`;
        }

        element.textContent = displayTime;
    });
}
// 页面加载时执行
document.addEventListener('DOMContentLoaded', updateTimeElements);

// 可选：每分钟更新一次（保持时间动态变化）
setInterval(updateTimeElements, 60000);

document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.twi-tabs');
    const underline = document.querySelector('.tab-underline');

    const allContent = document.querySelector('.all');
    const commentContent = document.querySelector('.twi-comment');
    const bottomBanner = document.querySelector('.bottom-banner');
    const tweets = document.querySelectorAll('.all .tweet');

    function moveUnderlineTo(tab) {
        const container = tab.parentElement;
        const tabRect = tab.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();
        const left = tabRect.left - containerRect.left;
        const width = tabRect.width;

        underline.style.transform = `translateX(${left}px)`;
        underline.style.width = `${width}px`;
    }

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', function () {
            // 设置 tab 高亮样式
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // 滑动横线
            moveUnderlineTo(this);

            // 内容控制逻辑
            if (index === 0 && allContent) {
                if (bottomBanner) bottomBanner.innerText = 'tweet';
                allContent.style.display = 'block';
                if (commentContent) commentContent.style.display = 'none';
                tweets.forEach((tweet, i) => {
                    tweet.style.display = i < 3 ? 'block' : 'none';
                });
            } else if (index === 1 && allContent) {
                if (bottomBanner) bottomBanner.innerText = 'monthly';
                allContent.style.display = 'block';
                if (commentContent) commentContent.style.display = 'none';
                tweets.forEach(tweet => {
                    tweet.style.display = 'block';
                });
            }
        });

        // 初始状态（默认点击第一个）

        if (tabs.length > 0 && allContent) {
            tabs[0].click(); // 自动触发
        }




    });

    function formatFollowers(total) {
        if (total < 1000) {
            return total;
        } else if (total < 1000000) {
            return `${(total / 1000).toFixed(1)}K`;
        } else if (total < 1000000000) {
            return `${(total / 1000000).toFixed(1)}M`;
        } else {
            return `${(total / 1000000000).toFixed(2)}B`;
        }
    }
    function calculateCurrentY() {
        const startDate = new Date(Date.UTC(2025, 4, 1, 16, 0, 0)); // CST 5月1日 
        const now = new Date(); const daysPassed = Math.floor((now.getTime() - startDate.getTime()) / (1000 * 60 * 60 * 24)); // 以下是你的增长逻辑 
        const delta = 10, gamma = 0.5;
        const sum_n = (31 - 1) * 31 / 2;
        const sum_n2 = (31 - 1) * 31 * (2 * 31 - 1) / 6;
        const r = (83621 - delta * sum_n - gamma * sum_n2) / 31;
        let total = 0;
        for (let n = 0; n <= daysPassed; n++) {
            const dailyGrowth = r + n * delta + n * n * gamma;
            total += dailyGrowth;

        }
        return Math.floor(total);
    }
    const formatted = formatFollowers(calculateCurrentY());
    document.getElementById('followers').textContent = formatted;
    let mdstatus = true;
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
    const videoExtensions = ['mp4', 'mov', 'webm', 'm3u8'];
    //---get files--- 
    document.querySelectorAll('.loading').forEach(loader => {
        // 确保初始状态下没有src属性，避免意外加载
        if (loader.tagName === 'IMG' || loader.tagName === 'VIDEO') {
            loader.removeAttribute('src');
        }

        loader.addEventListener('click', async function () {
            if (this.classList.contains('loading-in-progress')) return;


            const mediaAttr = this.getAttribute('data-media');
            const mdAttr = this.getAttribute('data-copyright');
            if (!mediaAttr || mediaAttr.length === 0) {
                // 延迟5000毫秒（即5秒）执行错误处理
                setTimeout(() => {
                    this.classList.remove('loading-in-progress');
                    this.classList.remove('loading');
                    this.innerHTML = '<div class="load-insert load-msg"><img class="load-icon" src="images/error.svg"></div>';
                    let loadInsert = this.querySelector('.load-insert');
                    loadInsert.style.setProperty('--load-text', '"Error 500"');
                }, 5000);
                return;
            }
            if (!this.classList.contains('loading')) return;
            if (mdAttr && mdAttr === 'off')
                mdstatus = false;
            this.classList.add('loading-in-progress');
            const currentLoader = this;

            // 解析可能包含多个媒体文件的字符串
            const filenames = mediaAttr.split(',').map(name => name.trim()).filter(name => name);
            async function getUrlStatusCode(url) {
                try {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000);

                    // 发起请求（不使用console.log输出任何信息）
                    const response = await fetch(url, {
                        method: 'HEAD',
                        credentials: 'include',
                        signal: controller.signal
                    });

                    clearTimeout(timeoutId);
                    return response.status; // 返回HTTP状态码（包括401/404等错误码）

                } catch (error) {
                    // 捕获所有错误，但不向控制台输出
                    if (error.name === 'AbortError') {
                        return -1; // 超时错误（静默处理）
                    }
                    return 0; // 其他错误（如网络中断、跨域等，静默处理）
                }
            }
            // 处理单个媒体文件的情况
            if (filenames.length === 1) {
                const filename = filenames[0];
                const ext = filename.split('.').pop().toLowerCase();
                const authUrl = `/images/private/${filename}`;
                const authUrl2 = `/wsfile.php?file=${filename}`;
                // 视频类型直接处理，不发起fetch(authUrl)请求
                if (videoExtensions.includes(ext)) {
                    const httpCode = await getUrlStatusCode(authUrl2);
                    if (httpCode != 200) {
                        currentLoader.innerHTML = `
        <div class="load-insert load-msg">
            <img class="load-icon" src="images/error.svg">
        </div>
    `;
                        currentLoader.classList.remove('loading-in-progress', 'loading');
                        currentLoader.classList.add('error-state'); // 可选：添加错误状态类便于样式控制

                        // 设置错误文本（确保 CSS 变量生效）
                        const loadInsert = currentLoader.querySelector('.load-insert');
                        if (loadInsert) {
                            loadInsert.style.setProperty('--load-text', `"Error ${httpCode}"`);
                        }
                        return;
                    }

                    const video = document.createElement('video');
                    video.className = 'tweet-video video-js vjs-default-skin';
                    video.controls = true;
                    video.preload = 'metadata';
                    video.style.pointerEvents = 'auto';
                    video.style.display = 'none';

                    const isHLS = filename.toLowerCase().endsWith('.m3u8');

                    // 修改HLS视频处理部分的代码
                    if (isHLS) {
                        currentLoader.appendChild(video);

                        const player = videojs(video, {
                            techOrder: ['html5'],
                            sources: [{
                                src: authUrl2,
                                type: 'application/x-mpegURL'
                            }],
                            controlBar: {
                                pictureInPictureToggle: false
                            },
                            errorDisplay: false
                        });

                        player.ready(() => {
                            loadMarkdownIfExists(filename, mdstatus);

                            // 针对iOS的特殊处理
                            const handleVideoDimensions = () => {
                                const videoElement = player.el().querySelector('video');
                                // 尝试获取视频宽高，若获取失败则使用默认值
                                let vw = videoElement.videoWidth || 300;
                                let vh = videoElement.videoHeight || 300;

                                // 确保宽高至少有一个合理的最小值
                                if (vw <= 0 || vh <= 0) {
                                    vw = 300;
                                    vh = 300;
                                }

                                const maxW = 300;
                                const maxH = 300;
                                const aspectRatio = vw / vh;

                                let finalW, finalH;
                                if (aspectRatio > 1) {
                                    finalW = maxW;
                                    finalH = maxW / aspectRatio;
                                } else {
                                    finalH = maxH;
                                    finalW = maxH * aspectRatio;
                                }

                                const container = player.el();
                                container.style.width = `${finalW}px`;
                                container.style.height = `${finalH}px`;
                                container.style.minWidth = '100px'; // 确保不会为0
                                videoElement.style.width = '100%';
                                videoElement.style.height = '100%';

                                currentLoader.classList.remove('loading-in-progress');
                                currentLoader.classList.remove('loading');
                                // 先获取元素
                                const loadInsertElement = currentLoader.querySelector('.load-insert');

                                // 显示视频元素
                                const elementsToShow = currentLoader.querySelectorAll('video, div');
                                elementsToShow.forEach(el => {
                                    if (el.style.display === 'none') {
                                        el.style.display = '';
                                        loadInsertElement.remove();
                                    }
                                });
                            };

                            // 尝试多种方式获取视频尺寸
                            const videoElement = player.el().querySelector('video');
                            // 1. 监听loadedmetadata事件
                            videoElement.addEventListener('loadedmetadata', handleVideoDimensions);
                            // 2. 同时设置超时检查，防止事件不触发
                            setTimeout(handleVideoDimensions, 3000);
                            // 3. 监听loadeddata事件作为备选
                            videoElement.addEventListener('loadeddata', handleVideoDimensions);
                        });

                        // 2. Video.js错误中若包含HTTP状态码（需提前捕获）
                        player.on('error', () => {
                            // 假设已通过其他方式捕获到HTTP状态码并存储在window中
                            if (window.capturedHttpStatus) {
                                fallbackToErrorUI({ status: window.capturedHttpStatus });
                            } else {
                                // 无明确HTTP状态码的播放器错误（可兜底为网络错误）
                                fallbackToErrorUI({ status: null });
                            }
                        });

                    } else {
                        // 普通视频也使用wsfile接口
                        video.src = authUrl2;
                        currentLoader.innerHTML = '';
                        currentLoader.appendChild(video);

                        video.onloadeddata = () => {

                            currentLoader.classList.remove('loading-in-progress');
                            currentLoader.classList.remove('loading');

                            loadMarkdownIfExists(filename, mdstatus);
                        };

                    }
                    // 1. 首先，确保 fallbackToErrorUI 函数增加“彻底清理 DOM”逻辑
                    function fallbackToErrorUI(error) {
                        // 关键步骤1：彻底清理 Video.js 生成的所有残留元素
                        const videoContainer = currentLoader.querySelector('.video-js'); // Video.js 根容器
                        if (videoContainer) {
                            videoContainer.parentNode.removeChild(videoContainer); // 从 DOM 中删除
                        }

                        // 关键步骤2：销毁可能存在的 Video.js 实例（双重保障）
                        if (window.player && window.player.dispose) {
                            window.player.dispose();
                            window.player = null; // 清空全局引用
                        }

                        // 2. 仅处理 HTTP 状态码的错误逻辑（按你的需求保留）
                        const httpStatusMap = {
                            401: "401",
                            403: "403",
                            404: "404",
                            415: "415",
                            500: "500"
                        };

                        // 提取 HTTP 状态码（优先从 error.status 获取，无则按 Video.js 错误兜底）
                        let statusCode = error.status;
                        let errorMsg;

                        // 若未获取到 HTTP 状态码，按 Video.js 错误特征兜底（如错误4对应404/415）
                        if (!statusCode) {
                            // 从错误对象特征判断可能的 HTTP 状态
                            if (error.code === 4) {
                                statusCode = 415; // 媒体不支持 -> 对应 415 状态码
                            } else if (error.code === 2) {
                                statusCode = 404; // 网络错误 -> 优先对应 404
                            }
                        }

                        // 生成错误信息
                        if (statusCode) {
                            errorMsg = httpStatusMap[statusCode] || `Error ${statusCode}`;
                        } else {
                            errorMsg = "网络连接异常：无法加载视频";
                        }

                        // 3. 强制渲染自定义错误 UI（覆盖整个 currentLoader 内容）
                        currentLoader.innerHTML = `
        <div class="load-insert load-msg">
            <img class="load-icon" src="images/error.svg">
        </div>
    `;
                        currentLoader.classList.remove('loading-in-progress', 'loading');
                        currentLoader.classList.add('error-state'); // 可选：添加错误状态类便于样式控制

                        // 设置错误文本（确保 CSS 变量生效）
                        const loadInsert = currentLoader.querySelector('.load-insert');
                        if (loadInsert) {
                            loadInsert.style.setProperty('--load-text', `"${errorMsg.replace(/"/g, '\\"')}"`);
                        }
                    }
                } else {
                    // 非视频类型（图片等）才发起fetch检查
                    fetch(authUrl, {
                        credentials: 'include',
                        headers: {
                            'Cache-Control': 'no-cache'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                currentLoader.classList.remove('loading-in-progress');
                                currentLoader.classList.remove('loading');
                                currentLoader.innerHTML = '<div class="load-insert"><img class="load-icon" src="images/error.svg"></div>';
                                let loadInsert = currentLoader.querySelector('.load-insert');
                                loadInsert.style.setProperty('--load-text', `"Error ${response.status}"`);
                                throw new Error(`${response.status}`);
                            }

                            if (imageExtensions.includes(ext)) {
                                const img = new Image();
                                img.src = authUrl;
                                img.className = 'tweet-image';
                                img.onload = () => {
                                    currentLoader.innerHTML = '';
                                    currentLoader.appendChild(img);
                                    loadMarkdownIfExists(filename, mdstatus);
                                    currentLoader.classList.remove('loading-in-progress');
                                    currentLoader.classList.remove('loading');
                                };

                                img.onerror = () => {
                                    currentLoader.innerHTML = '<div class="load-insert load-msg"><img class="load-icon" src="images/error.svg"></div>';
                                    currentLoader.classList.remove('loading-in-progress');
                                    currentLoader.classList.remove('loading');
                                    let loadInsert = currentLoader.querySelector('.load-insert');
                                    loadInsert.style.setProperty('--load-text', '"Error 404"');
                                };
                            } else {
                                currentLoader.innerHTML = '<div class="load-insert load-msg"><img class="load-icon" src="images/error.svg"></div>';
                                currentLoader.classList.remove('loading-in-progress');
                                currentLoader.classList.remove('loading');
                                let loadInsert = currentLoader.querySelector('.load-insert');
                                loadInsert.style.setProperty('--load-text', '"Error 500"');
                                throw new Error(`500`);
                            }
                        })
                        .catch(error => {
                            currentLoader.classList.remove('loading-in-progress');
                            currentLoader.classList.remove('loading');
                            currentLoader.innerHTML = '<div class="load-insert load-msg"><img class="load-icon" src="images/error.svg"></div>';
                            let loadInsert = currentLoader.querySelector('.load-insert');
                            loadInsert.style.setProperty('--load-text', `"Error ${error.message}"`);
                        });
                }
            }
            // 处理多个媒体文件的情况（只处理图片）
            else if (filenames.length > 1) {
                // 检查所有文件是否都是图片
                const allImages = filenames.every(filename => {
                    const ext = filename.split('.').pop().toLowerCase();
                    return imageExtensions.includes(ext);
                });

                if (!allImages) {
                    currentLoader.classList.remove('loading-in-progress');
                    currentLoader.classList.remove('loading');
                    currentLoader.innerHTML = '<div class="load-insert load-msg"><img class="load-icon" src="images/error.svg"></div>';
                    let loadInsert = currentLoader.querySelector('.load-insert');
                    loadInsert.style.setProperty('--load-text', '"Error 500"');
                    return;
                }

                // 创建容器来存放多张图片
                const container = document.createElement('div');
                container.className = 'multi-image-container';

                // 跟踪已完成加载的图片数量
                let totalImages = filenames.length;
                let processedCount = 0;

                // 加载所有图片
                filenames.forEach(filename => {
                    const authUrl = `/images/private/${filename}`;

                    const img = new Image();
                    img.src = authUrl;
                    img.className = 'tweet-image';
                    img.onload = () => {
                        container.appendChild(img);
                        processedCount++;
                        checkCompletion();
                    };

                    img.onerror = () => {
                        processedCount++;
                        checkCompletion();
                    };
                });

                // 检查是否所有图片都已处理完毕
                function checkCompletion() {
                    if (processedCount === totalImages) {
                        currentLoader.classList.remove('loading-in-progress');
                        currentLoader.classList.remove('loading');
                        currentLoader.innerHTML = '';

                        const imagesLoaded = container.children.length;
                        if (imagesLoaded > 0) {
                            currentLoader.appendChild(container);
                            // 使用第一张图片的名称加载 Markdown 文件
                            if (filenames.length > 0) {
                                loadMarkdownIfExists(filenames[0], mdstatus);
                            }
                        } else {
                            currentLoader.innerHTML = '<div class="load-insert load-msg"><img class="load-icon" src="images/error.svg"></div>';
                            let loadInsert = currentLoader.querySelector('.load-insert');
                            loadInsert.style.setProperty('--load-text', '"Error 401"');
                        }
                    }
                }
            }

            // 尝试加载并显示对应的 Markdown 文件
            function loadMarkdownIfExists(mdfile, status) {
                if (!status)
                    return;
                // 从图片文件名中提取前缀（例如：250513-1.jpg -> 250513）
                const prefixMatch = mdfile.match(/^([^-]+)-/);
                if (!prefixMatch) return; // 如果格式不匹配，则不加载 Markdown

                const markdownPrefix = prefixMatch[1];
                const markdownUrl = `/images/private/${markdownPrefix}.md`;
                // 检查 Markdown 文件是否存在
                fetch(markdownUrl, { method: 'HEAD' })
                    .then(response => {
                        if (response.ok) {
                            // 文件存在，获取内容
                            return fetch(markdownUrl, { credentials: 'include' })
                                .then(mdResponse => mdResponse.text())
                                .then(mdContent => {
                                    // 创建 Markdown 内容容器
                                    const mdContainer = document.createElement('div');
                                    mdContainer.className = 'media-c';
                                    mdContainer.innerHTML = mdContent;
                                    // 将 Markdown 内容添加到当前加载器的末尾
                                    currentLoader.appendChild(mdContainer);
                                });
                        }
                    })
            }
        });

    });
    //---get files--- 

    const buttons = document.querySelectorAll('.trans-button');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const currentTrans = button.nextElementSibling;

            // 隐藏所有翻译，显示所有按钮
            document.querySelectorAll('.tweet-text.trans').forEach(trans => {
                trans.style.display = 'none';
            });
            document.querySelectorAll('.trans-button').forEach(btn => {
                btn.style.display = 'block';
            });

            // 显示当前翻译，隐藏当前按钮
            currentTrans.style.display = 'block';
            button.style.display = 'none';
        });
    });
    // Configuration
    const totalFans = calculateCurrentY();
    //const formatted = formatFollowers(total+past_data-40073);

    // Process all translated tweets
    document.querySelectorAll('.tweet-content').forEach(tweetContent => {
        // Determine insertion point (after translation if exists, otherwise at end)


        // Create tweet actions container
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'tweet-actions';

        // Generate random counts
        const randomLikePercent = 0.005 + Math.random() * 0.845; // 0.5%-85%
        const randomViewPercent = 0.975 + Math.random() * (1.247 - 0.975); // 97.5% - 124.7%

        const likeCount = Math.round(totalFans * randomLikePercent);
        const viewCount = Math.round(totalFans * randomViewPercent);
        const t = Math.random();
        const uShape = t < 0.5 ? t * 2 : (1 - t) * 2; // 0 ~ 1，双峰分布：中间小，两端大
        const commentCount = Math.round(512 * (1 - uShape)); // 映射为 0 ~ 512


        // Create action items
        actionsDiv.innerHTML = `
            <div class="action-item">
            <svg viewBox="0 0 24 24" aria-hidden="true" class="acomment"><g><path d="M12 2.59l5.7 5.7-1.41 1.42L13 6.41V16h-2V6.41l-3.3 3.3-1.41-1.42L12 2.59zM21 15l-.02 3.51c0 1.38-1.12 2.49-2.5 2.49H5.5C4.11 21 3 19.88 3 18.5V15h2v3.5c0 .28.22.5.5.5h12.98c.28 0 .5-.22.5-.5L19 15h2z"></path></g></svg>
                <span class="action-count">${formatFollowers(commentCount)}</span>
            </div>
            <div class="action-item">
                <svg viewBox="0 0 24 24" aria-hidden="true" class="alike">
                    <g><path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path></g>
                </svg>
                <span class="action-count">${formatFollowers(likeCount)}</span>
            </div>
            <div class="action-item">
                <svg viewBox="0 0 24 24" aria-hidden="true" class="avisit">
                    <g><path d="M8.75 21V3h2v18h-2zM18 21V8.5h2V21h-2zM4 21l.004-10h2L6 21H4zm9.248 0v-7h2v7h-2z"></path></g>
                </svg>
                <span class="action-count">${formatFollowers(viewCount)}</span>
            </div>
        `;

        // Insert after the determined point
        tweetContent.appendChild(actionsDiv);
    });
    document.querySelectorAll('.alike').forEach(commentBtn => {
        commentBtn.addEventListener('click', (e) => {
            const plus = document.createElement('div');
            plus.className = 'floating-plus1';
            plus.innerHTML = `
  <svg viewBox="0 0 24 24" aria-hidden="true" class="alike pinkh">
                    <g><path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path></g>
                </svg>
`;


            // 随机水平偏移量（-20 到 +20 像素）
            const offsetX = Math.floor(Math.random() * 41) - 20;
            plus.style.setProperty('--float-x', `${offsetX}px`);

            // 定位到鼠标点击处
            plus.style.left = `${e.pageX}px`;
            plus.style.top = `${e.pageY}px`;

            document.body.appendChild(plus);

            // 1秒后移除元素
            setTimeout(() => {
                plus.remove();
            }, 1000);
        });
    });
    //end of DOMContentLoaded
});


function rotateImage(div) {
    const img = div.querySelector('img');
    img.src = '/images/load2.svg';
}

const lazyImages = document.querySelectorAll('.lazyload');

const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            const imageUrl = img.dataset.src;

            // 使用fetch获取图片数据
            fetch(imageUrl)
                .then(response => response.blob())
                .then(blob => {
                    // 创建Blob URL并设置为图片源
                    const blobUrl = URL.createObjectURL(blob);
                    img.src = blobUrl;

                    // 当图片加载完成后，释放Blob URL以节省内存
                    img.onload = () => {
                        URL.revokeObjectURL(blobUrl);
                        img.classList.remove('lazyload');
                    };
                })
                .catch(error => {
                    console.error('Error loading image:', error);
                });

            observer.unobserve(img);
        }
    });
});

lazyImages.forEach(image => {
    observer.observe(image);
});
let currentPlayer = null;
let currentAudio = null;
let animationId = null;
let minLoadTimer = null;

function stopAnimation() {
    if (animationId) {
        clearTimeout(animationId);
        animationId = null;
    }
    if (minLoadTimer) {
        clearTimeout(minLoadTimer);
        minLoadTimer = null;
    }
    if (currentAudio && currentAudio._onReady) {
        currentAudio.removeEventListener('canplay', currentAudio._onReady);
        delete currentAudio._onReady;
    }
    if (currentPlayer) {
        const barsContainer = currentPlayer.querySelector('.bars');
        const playIcon = currentPlayer.querySelector('.play-icon');

        barsContainer.style.display = 'none';
        playIcon.style.display = 'block';
        playIcon.innerHTML = `<svg viewBox="0 0 100 100" width="60" height="60" xmlns="http://www.w3.org/2000/svg">
                <path class="playsvg" d=" M 20 15 Q 20 10 25 13 L 85 48 Q 90 50 85 52 L 25 87 Q 20 90 20 85 Z"/></svg>`;

        const bars = barsContainer.querySelectorAll('.bar');
        bars.forEach(bar => bar.style.height = '30px');

        if (currentAudio) {
            currentAudio.pause();
            currentAudio.currentTime = 0;
        }

        currentPlayer = null;
        currentAudio = null;
    }
}

function startRandomAnimation(barsContainer, bars, playIcon) {
    barsContainer.style.display = 'flex';
    playIcon.style.display = 'none';

    function animate() {
        bars.forEach(bar => {
            const height = Math.floor(Math.random() * 20) + 5;
            bar.style.height = `${height}px`;
        });
        animationId = setTimeout(animate, 200);
    }

    animate();
}

document.querySelectorAll('.player').forEach(player => {
    const barsContainer = player.querySelector('.bars');
    const bars = barsContainer.querySelectorAll('.bar');
    const audio = player.querySelector('audio');
    const playIcon = player.querySelector('.play-icon');
    const mediaSrc = audio.getAttribute('data-media');

    player.addEventListener('click', () => {
        const isCurrent = currentPlayer === player;

        if (isCurrent) {
            stopAnimation();
        } else {
            stopAnimation();
            currentPlayer = player;
            currentAudio = audio;

            // 显示 loading 图标
            playIcon.innerHTML = '<img class="load-audio-icon" src="/images/load2.svg">';

            let ready = false;
            let minLoadPassed = false;

            // 监听 canplay
            const onReady = () => {
                ready = true;
                attemptPlay();
            };
            audio._onReady = onReady;
            audio.addEventListener('canplay', onReady, { once: true });

            // 保证 loading 至少 5 秒
            minLoadTimer = setTimeout(() => {
                minLoadPassed = true;
                attemptPlay();
            }, 5000);

            function attemptPlay() {
                if (ready && minLoadPassed) {
                    // 尝试播放音频
                    const playPromise = audio.play();
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            startRandomAnimation(barsContainer, bars, playIcon);
                        }).catch(err => {
                            console.warn('音频播放被阻止或出错', err);
                            // 保持 loading 状态，用户可以再次点击
                        });
                    } else {
                        // 老旧浏览器直接播放
                        startRandomAnimation(barsContainer, bars, playIcon);
                    }
                }
            }

            audio.src = mediaSrc;
            audio.load();
        }
    });

    audio.addEventListener('ended', () => {
        stopAnimation();
    });
});

const badge = document.querySelector('.original');
const tooltip = document.getElementById('badge-tooltip');

function isMobile() {
    return window.innerWidth <= 600;
}

function positionTooltip() {
    if (isMobile()) {
        tooltip.classList.add('show');
    } else {
        const rect = badge.getBoundingClientRect();
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const scrollLeft = window.scrollX || document.documentElement.scrollLeft;

        const offsetY = 8;
        const offsetX = -100; // 向左偏移一点，更像 Twitter

        tooltip.style.top = `${rect.bottom + scrollTop + offsetY}px`;
        tooltip.style.left = `${rect.left + scrollLeft + offsetX}px`;
    }
}

function showTooltip() {
    positionTooltip();
    tooltip.style.display = 'block';
}

function hideTooltip() {
    tooltip.style.display = 'none';
    tooltip.classList.remove('show');
}

badge.addEventListener('click', (e) => {
    e.stopPropagation();
    if (tooltip.style.display === 'block') {
        hideTooltip();
    } else {
        showTooltip();
    }
});

document.addEventListener('click', (e) => {
    if (!tooltip.contains(e.target) && e.target !== badge) {
        hideTooltip();
    }
});

window.addEventListener('resize', () => {
    if (tooltip.style.display === 'block') {
        positionTooltip();
    }
});
document.addEventListener('click', async function (e) {

    const icon = e.target.closest('.acomment');
    if (!icon) return;

    const tweet = icon.closest('.tweet');
    if (!tweet) return;
    const tweetId = tweet.dataset.tweetId || '';
    const originalText = tweet.querySelector('.tweet-text')?.innerHTML || '';
    const translatedText = tweet.querySelector('.tweet-text.trans')?.innerHTML || '';
    const pa = document.getElementById('post-actions');
    if (pa)
        pa.style = 'none';
    // 准备截图内容
    const capture = document.getElementById('capture-area');
    capture.innerHTML = `
        <div style="
            max-width: 500px;
            width:100%;
            min-height: 100px;
            padding: 14px;
            background-color:var(--md-background);
            color: var(--md-on-background);
            font-family: 'Noto Serif SC', 'Georgia', 'Times New Roman', serif;
        ">
            <div id="original-text" style="font-size: 12px;padding:8px;line-height:2;text-indent:2em;">${originalText}</div>
            <div style="font-size: 12px;padding:8px;line-height:2;">${translatedText}</div>
           <div style="
    font-size: 5%; /* 父容器宽度的百分比 */
    padding:0.5rem;
    text-align:center;
    position:absolute;
    top:50%;
    left:50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    border:5px solid #f28b82; /* 相对缩放 */
    color:#f28b82;
    z-index:-1;
    white-space: nowrap;
">
    高级不可回收物
</div>
        </div>
    `;
const originalDiv = capture.querySelector('#original-text');
if (originalDiv) {
  const text = originalDiv.innerText.trim();
  const style = window.getComputedStyle(originalDiv);
  const fontSize = parseFloat(style.fontSize);       // 字号
  const containerWidth = originalDiv.clientWidth;    // 容器宽度
  const maxCharsPerLine = Math.floor(containerWidth / fontSize);

  if (text.length <= maxCharsPerLine) {
    originalDiv.style.textIndent = '0';
  }
}

    // 生成图片
    const canvas = await html2canvas(capture.querySelector('div'));
    const imgURL = canvas.toDataURL('image/png');

    // 显示弹窗
    const modal = document.getElementById('preview-modal');
    const container = document.getElementById('preview-image-container');
    const downloadBtn = document.getElementById('download-btn');
    const closeBtn = document.querySelector('.qq-titlebar-button.close');
    const shareImage = document.getElementById('shareimage');
    container.innerHTML = `<img src="${imgURL}" style="width: 100%;display:block;" />`;
    container.style.backgroundPosition = 'center';
    new MutationObserver((o, s) => { let e = document.getElementById('post-actions'); if (e) { e.style.display = 'none'; s.disconnect(); } }).observe(document.body, { childList: true, subtree: true });
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.getElementById('preview-modal').style.display = 'flex';
    shareImage.style.display = 'block';

    // 设置下载功能
    downloadBtn.onclick = () => {
        const link = document.createElement('a');
        link.download = `tweet${tweetId}.png`;
        link.href = imgURL;
        link.click();
        document.body.style.overflow = '';
        document.getElementById('preview-modal').style.display = 'none';

    };
    closeBtn.onclick = () => {
        document.body.style.overflow = '';
        document.getElementById('preview-modal').style.display = 'none';

    };
});
function hasVisibleDynamicMask() {
    // 检查动态生成的.custom-confirm-mask遮罩层
    const confirmMasks = document.querySelectorAll('.custom-confirm-mask');
    for (const mask of confirmMasks) {
        // 动态元素可能通过display:none或visibility:hidden隐藏
        const isDisplayed = window.getComputedStyle(mask).display !== 'none';
        const isVisible = window.getComputedStyle(mask).visibility !== 'hidden';
        // 同时检查元素是否在DOM中且尺寸有效（排除零尺寸元素）
        if (isDisplayed && isVisible && mask.offsetWidth > 0 && mask.offsetHeight > 0) {
            return true;
        }
    }

    // 检查动态生成的#preview-modal遮罩层
    const previewModal = document.getElementById('preview-modal');
    if (previewModal) {
        const isDisplayed = window.getComputedStyle(previewModal).display !== 'none';
        const isVisible = window.getComputedStyle(previewModal).visibility !== 'hidden';
        if (isDisplayed && isVisible && previewModal.offsetWidth > 0 && previewModal.offsetHeight > 0) {
            return true;
        }
    }

    return false;
}
if (window.innerWidth > 767) {
    //fetch('https://api.nasa.gov/planetary/apod?api_key=DEMO_KEY')
    //.then(r => r.json())
    //.then(d => document.documentElement.style.setProperty('--bg-url', `url(${d.url})`));
}
if (window.innerWidth > 767) {
    const targetCount = 10;
    let images = [];
    const today = new Date();
    let end = new Date(Date.UTC(today.getUTCFullYear(), today.getUTCMonth(), today.getUTCDate()));
    const earliestDate = new Date(end);
    earliestDate.setUTCDate(end.getUTCDate() - 30); // 最早只往前一个月

    const format = d => `${d.getUTCFullYear()}-${String(d.getUTCMonth() + 1).padStart(2, '0')}-${String(d.getUTCDate()).padStart(2, '0')}`;

    const fetchAPODBatch = async (start, end) => {
        const res = await fetch(`https://api.nasa.gov/planetary/apod?api_key=DEMO_KEY&start_date=${format(start)}&end_date=${format(end)}`);
        const data = await res.json();
        return data.filter(d => d.media_type === 'image' && new Date(d.date) <= end);
    };

    const loadImages = async () => {
        let batchEnd = end;
        let batchStart = new Date(batchEnd);
        batchStart.setUTCDate(batchEnd.getUTCDate() - 9); // 每次请求 10 天

        while (images.length < targetCount && batchStart >= earliestDate) {
            try {
                const batch = await fetchAPODBatch(batchStart, batchEnd);
                images = [...batch, ...images]; // 新数据放前面
            } catch (e) {
                console.error('APOD fetch error:', e);
                break;
            }

            // 更新下一个时间段，往前推 10 天
            batchEnd = new Date(batchStart);
            batchStart.setUTCDate(batchEnd.getUTCDate() - 9);

            // 防止超过最早日期
            if (batchStart < earliestDate) batchStart = new Date(earliestDate);
        }

        if (images.length) {
            const d = images[Math.floor(Math.random() * images.length)];
            document.documentElement.style.setProperty('--bg-url', `url(${d.url})`);
        }
    }

    loadImages();
}
