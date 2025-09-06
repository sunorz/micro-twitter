document.addEventListener('click', async function (e) {
    const icon = e.target.closest('.more-options');
    if (!icon) return;
    document.body.style.overflow = 'hidden';
    // 移除已有菜单和遮罩
    document.querySelectorAll('.dropdown-menu, .dropdown-mask').forEach(el => el.remove());

    const tweet = icon.closest('.tweet');
    if (!tweet) return;
    // 找到所有 tweet 元素
    const tweets = Array.from(document.querySelectorAll('.tweet'));

    // 取最后一个 tweet 元素
    const lastTweet = tweets[tweets.length - 1];

    // 判断当前 tweet 是否是最后一个 tweet
    const isLastTweet = (tweet === lastTweet);

    const originalText = (tweet.querySelector('.tweet-text')?.innerHTML || '').trim();
    const translatedText = (tweet.querySelector('.tweet-text.trans')?.innerHTML || '').trim();
    const tweetid = (tweet.dataset.tweetId || '').trim();

    const dropdown = document.createElement('div');
    dropdown.className = 'dropdown-menu';

    dropdown.innerHTML = `
   <div class="menu-item" data-action="edit">
  <div class="menu-item-justify">
    <svg class="icon-edit" height="24" width="24" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 64 64">
      <path d="M42.31,11.51l10.18,10.18L16.73,57.45l-13.38,6.42c-1.99.66-3.88-1.23-3.22-3.22l6.42-13.38L42.31,11.51ZM46.42,7.4l6.55-6.55c1.14-1.14,2.98-1.14,4.11,0l6.07,6.07c1.14,1.14,1.14,2.98,0,4.11l-6.55,6.55-10.18-10.18Z"/>
    </svg>
    <span>Edit</span>
  </div>
</div>

${isLastTweet ? `<div class="menu-item" data-action="archive">
  <div class="menu-item-justify">
    <svg class="icon-archive" height="24" width="24" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 64 64">
      <path d="M9.78,0c-1.26,0-2.43.67-3.07,1.76L.49,12.43c-.32.54-.49,1.16-.49,1.79v42.67c0,3.89,3.22,7.11,7.11,7.11h49.78c3.89,0,7.11-3.22,7.11-7.11V14.22c0-.63-.17-1.25-.49-1.79l-6.22-10.67c-.64-1.09-1.81-1.76-3.07-1.76H9.78ZM11.82,7.11h40.36l4.15,7.11H7.67l4.15-7.11ZM7.11,21.33h49.78v35.56H7.11V21.33ZM21.33,28.44v7.11h21.33v-7.11h-21.33Z"/>
    </svg>
    <span>Archive</span>
  </div>
</div>` : ''}

<div class="menu-item" data-action="delete">
  <div class="menu-item-justify">
    <svg class="icon-delete" height="24" width="24" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 64 64">
      <path d="M44.89,12.67v-4.83C44.89,3.39,41.28-.22,36.83-.22h-9.67c-4.48,0-8.06,3.61-8.06,8.06v4.83H3v6.44h3.42l2.61,36.12c.35,5.06,4.54,8.99,9.63,8.99h26.65c5.09,0,9.28-3.93,9.67-8.99l2.58-36.12h3.45v-6.44h-16.11ZM25.56,7.83c0-.9.71-1.61,1.61-1.61h9.67c.87,0,1.61.71,1.61,1.61v4.83h-12.89v-4.83ZM48.53,54.78c-.13,1.68-1.51,3-3.22,3h-26.65c-1.71,0-3.09-1.32-3.22-3l-2.55-35.67h38.18s-2.55,35.67-2.55,35.67ZM22.33,48.11v-19.33h6.44v19.33h-6.44ZM35.22,48.11v-19.33h6.44v19.33h-6.44Z"/>
    </svg>
    <span style="color:var(--md-red);">Delete</span>
  </div>
</div>
    `;

    const isMobile = window.matchMedia("(max-width: 767px)").matches;

    if (isMobile) {
        // 移动端处理 - 底部弹出卡片 + 遮罩层
        dropdown.classList.add('mobile');
        document.body.appendChild(dropdown);

        const mask = document.createElement('div');
        mask.className = 'dropdown-mask';
        document.body.appendChild(mask);

        mask.addEventListener('click', () => {
            dropdown.remove();
            mask.remove();
            document.body.style.overflow = ''; // 或 'auto'
            document.documentElement.style.overflow = ''; // 同时修复根元素
        });
    } else {
        // 桌面端处理 - 右侧悬浮菜单
        dropdown.classList.add('desktop');
        const iconRect = icon.getBoundingClientRect();
        dropdown.style.top = `${iconRect.bottom + window.scrollY}px`;
        dropdown.style.left = `${iconRect.left + window.scrollX - 100}px`;
        document.body.appendChild(dropdown);
    }
    function customConfirm(icon, title, message, confirm) {
        return new Promise((resolve) => {
            const mask = document.createElement('div');
            mask.className = 'custom-confirm-mask';
            mask.innerHTML = `
      <div id="confirm" class="qq-window">
        <div class="qq-titlebar">        
          <div class="qq-icon"><img src="/images/icon-${icon}.svg"></div>
          <div class="qq-titlebar-text">${title}</div>
          <div class="qq-decor-line"></div>
        </div>
        <div class="qq-content">
          <div class="custom-confirm-message">${message}</div>
          <div class="qq-button-container custom-confirm-buttons">
            <button class="custom-confirm-button confirm qq-button">${confirm}</button>
            <button class="custom-confirm-button cancel qq-button">Cancel</button>
          </div>
        </div>
      </div>
    `;

            document.body.appendChild(mask);

            // 获取元素
            const btnConfirm = mask.querySelector('.custom-confirm-button.confirm.qq-button');
            const btnCancel = mask.querySelector('.custom-confirm-button.cancel.qq-button');
            const confirmWindow = mask.querySelector('#confirm');


            btnConfirm.onclick = () => {
                cleanup();
                resolve(true);
            };

            btnCancel.onclick = () => {
                cleanup();
                resolve(false);
            };

            function cleanup() {
                document.body.removeChild(mask);
                // 恢复页面滚动
                document.documentElement.style.overflow = '';
                document.body.style.overflow = '';
            }
        });
    }

    // 事件委托处理菜单点击
    dropdown.addEventListener('click', async (evt) => {
        const menuItem = evt.target.closest('.menu-item');
        if (!menuItem) return;
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';

        const action = menuItem.dataset.action;
        const modal = document.getElementById('preview-modal');
        const postactions = document.getElementById('post-actions');
        document.querySelectorAll('.dropdown-menu, .dropdown-mask').forEach(el => el.remove());
        // HTML实体转文本的函数
        function convertEntitiesToText(text) {
            return text.replace(/&amp;/g, '&')
                .replace(/&lt;/g, '<')
                .replace(/&gt;/g, '>')
                .replace(/&quot;/g, '"')
                .replace(/&#039;/g, "'");
        }
        switch (action) {
            case 'edit':
                document.querySelector('input[name="tweet-id"]').value = tweetid;
                const otext = document.querySelector('textarea[name="otext"]');
                const ttext = document.querySelector('textarea[name="ttext"]');
                otext.value = convertEntitiesToText(originalText);
                ttext.value = convertEntitiesToText(translatedText);
                document.getElementById('shareimage').style.display = 'none';
                modal.style.display = 'flex';
                postactions.style.display = 'block';
                // 弹窗关闭事件绑定
                modal.onclick = (ev) => {
                    if (ev.target.closest('.qq-titlebar-button.close')) {
                        modal.style.display = 'none';
                        document.documentElement.style.overflow = '';
                        document.body.style.overflow = '';
                    }
                };
                break;

            case 'archive':
                if (!await customConfirm('archive', 'ARCHIVE TWEET', 'Are you sure you want to <span style="color:var(--md-blue);">archive</span> this tweet?', 'Archive')) {
                    document.documentElement.style.overflow = '';
                    document.body.style.overflow = '';
                    return;
                }

                const archiveFormData = new FormData();
                archiveFormData.append('tweet-id', tweetid);
                archiveFormData.append('otext', '');
                archiveFormData.append('ttext', '');

                // 发送请求但不处理返回值
                fetch(document.getElementById("tweetForm").action, {
                    method: 'POST',
                    body: archiveFormData
                });

                setTimeout(() => {
                    animateTweetRemoval(tweetid);
                }, 2000);
                // 直接隐藏推文（不等待服务器响应）

                document.documentElement.style.overflow = '';
                document.body.style.overflow = '';
                break;

            case 'delete':
                if (!await customConfirm('delete', 'DELETE TWEET', 'Are you sure you want to <span style="color:var(--md-red);">delete</span> this tweet? This action cannot be undone!', 'Delete')) {
                    document.documentElement.style.overflow = '';
                    document.body.style.overflow = '';
                    return;
                }

                const deleteFormData = new FormData();
                deleteFormData.append('tweet-id', 'q' + tweetid);
                deleteFormData.append('otext', '');
                deleteFormData.append('ttext', '');

                // 发送请求但不处理返回值
                fetch(document.getElementById("tweetForm").action, {
                    method: 'POST',
                    body: deleteFormData
                });

                // 直接隐藏推文（不等待服务器响应）
                setTimeout(() => {
                    animateTweetRemoval(tweetid);
                }, 2000);
                break;
        }

        // 减慢速度的动画移除函数
        function animateTweetRemoval(tweetId) {
            const tweetElement = document.querySelector(`[data-tweet-id="${tweetId}"]`);

            if (tweetElement) {
                // 设置动画过渡效果，时长1.2秒让动画更明显
                tweetElement.style.transition = 'all 1.2s ease-in-out';
                tweetElement.style.overflow = 'hidden';
                tweetElement.style.opacity = '1'; // 确保开始时完全可见

                // 微小延迟后开始动画
                setTimeout(() => {
                    tweetElement.style.opacity = '0'; // 透明度缓慢减到0
                    tweetElement.style.height = '0'; // 高度收缩
                    tweetElement.style.margin = '0'; // 外边距收缩
                    tweetElement.style.padding = '0'; // 内边距收缩
                    tweetElement.style.transform = 'scale(0.9)'; // 轻微缩小增强效果
                }, 50);

                // 动画结束后处理
                setTimeout(() => {
                    tweetElement.style.display = 'none'; // 先隐藏
                    tweetElement.remove(); // 再从DOM中彻底移除
                }, 1250); // 比动画时长多50ms，确保动画完成
            }
        }

        // 清理菜单和遮罩
        dropdown.remove();
        if (isMobile) {
            document.querySelector('.dropdown-mask')?.remove();

        }
    });

    // 桌面端点击页面其他地方关闭菜单
    if (!isMobile) {
        // 获取元素
        const closeMenu = (ev) => {
            if (!dropdown.contains(ev.target)) {
                dropdown.remove();
                if (!hasVisibleDynamicMask()) {
                    document.body.style.overflow = '';
                    document.documentElement.style.overflow = '';
                }
                document.removeEventListener('click', closeMenu);
            }
        };
        document.addEventListener('click', closeMenu);
    }

});
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
const form = document.getElementById("tweetForm");
const resultEl = document.getElementById('result');
const lengthEl = document.getElementById('len');
// 保存元素初始的HTML内容
const originalContent = resultEl.innerHTML;

// 状态变量：标记当前是否为切换后的状态
let isToggled = false;

// 点击事件处理
resultEl.addEventListener('click', function () {
    if (!isToggled) {
        // 第一次点击：切换为"Normal post."
        this.innerHTML = '<img src="/images/localhost.png" width="30" height="30"/>Post tweet in normal mode.';
        lengthEl.style.visibility = 'hidden';
        isToggled = true;
    } else {
        // 再次点击：还原为初始内容（使用innerHTML保留原始HTML结构）
        this.innerHTML = originalContent;
        lengthEl.style.visibility = 'visible';
        isToggled = false;
    }
});
function countXCharacters(text) {
    const urlPattern = /https?:\/\/[^\s]+/gi;
    let length = 0;
    let offset = 0;
    let match;

    while ((match = urlPattern.exec(text)) !== null) {
        const urlStart = match.index;
        const urlEnd = urlPattern.lastIndex;

        // 前面的普通文本
        const before = text.slice(offset, urlStart);
        length += [...before].length; // 兼容 emoji，多码点按 1 计

        // URL 固定 23 字符
        length += 23;

        offset = urlEnd;
    }

    // 余下部分
    const rest = text.slice(offset);
    length += [...rest].length;

    return length;
}

// 示例：监听 textarea 输入并显示长度
const textarea = ttext;


textarea.addEventListener("input", () => {
    const hasSvg = resultEl.querySelector('.twitter') !== null;
    if (hasSvg) {
        lengthEl.style.visibility = 'visible';
    }
    const len = countXCharacters(textarea.value);
    if (len > 265) {
        lengthEl.style.color = '#ea4335';
    }
    else {
        lengthEl.style.color = 'var(--qq-text)';
    }
    lengthEl.textContent = `Length: ${len}`;
});

form.addEventListener('submit', function (e) {
    e.preventDefault(); // 阻止默认刷新
    const editFormData = new FormData();
    const hasSvg = resultEl.querySelector('.twitter') !== null;
    if (hasSvg) {
        editFormData.append('synctox', true);
    }
    const tweetid = document.querySelector('input[name="tweet-id"]').value;
    const otextv = document.querySelector('textarea[name="otext"]').value;
    const ttextv = document.querySelector('textarea[name="ttext"]').value;
    editFormData.append('tweet-id', tweetid);
    editFormData.append('otext', otextv);
    editFormData.append('ttext', ttextv);
    fetch(this.action, {
        method: 'POST',
        body: editFormData
    })
        .then(res => res.json()) // 假设 PHP 返回 JSON
        .then(data => {
            const qqContent = document.querySelector('#post-actions .qq-content');
            const qqTitleBtn = document.querySelector('#post-actions .qq-titlebar-buttons');
            const editMask = document.getElementById('preview-modal');
            if (data.success) {
                qqTitleBtn.remove();
                editMask.style.display = 'flex';
                form.remove();
                const newText = document.createElement('div');
                newText.innerHTML = `<div style="width:100%;text-align:center;display:flex;justify-content:center;gap:10px;line-height:32px;"><svg t="1756550141207" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2356" width="32" height="32"><path d="M512 0C229.004 0 0 229.004 0 512s229.004 512 512 512 512-229.004 512-512S794.996 0 512 0z m260.655 425.425L493.382 704.698c-5.586 5.586-13.033 9.31-21.411 9.31-10.24 1.861-20.48-0.932-27.927-8.379L268.102 528.756a30.906 30.906 0 0 1 0-43.752l14.894-14.895c12.102-12.102 31.651-12.102 43.753 0l141.498 141.498 244.83-244.829c12.101-12.102 31.65-12.102 43.752 0l15.826 14.895c12.101 12.102 12.101 31.65 0 43.752z" fill="#afcd50" p-id="2357"></path></svg>${data.msg}</div>`;  // 创建纯文本节点
                qqContent.append(newText);
                qqContent.style.color = 'var(--qq-text)';// 追加到 .qq-content 中
                setTimeout(() => {
                    location.href = '/x.php';
                }, 3000);
            }
            else {
                qqTitleBtn.remove();
                editMask.style.display = 'flex';
                form.remove();
                const newText = document.createElement('div');
                newText.innerHTML = `<div style="width:100%;text-align:center;display:flex;justify-content:center;gap:10px;line-height:32px;"><svg t="1756550442373" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3488" width="32" height="32"><path d="M512 0C229.205333 0 0 229.205333 0 512s229.205333 512 512 512 512-229.205333 512-512S794.794667 0 512 0z m0 796.458667A56.917333 56.917333 0 1 1 511.957333 682.666667 56.917333 56.917333 0 0 1 512 796.458667z m54.186667-227.797334h0.128a60.501333 60.501333 0 0 1-53.802667 55.893334c2.048 0.256 3.882667 1.152 5.973333 1.152h-11.818666c2.048 0 3.84-0.981333 5.845333-1.109334a59.093333 59.093333 0 0 1-53.162667-55.893333l-13.056-284.16a54.314667 54.314667 0 0 1 54.613334-57.045333h26.282666a52.992 52.992 0 0 1 54.186667 57.002666l-15.146667 284.16z" fill="#B94343" p-id="3489"></path></svg>${data.msg}</div>`;
                qqContent.append(newText);
                qqContent.style.color = 'var(--qq-text)';// 追加到 .qq-content 中
                setTimeout(() => {
                    location.href = '/x.php';
                }, 3000);
            }
        })
        .catch(err => console.error('请求出错', err));
});