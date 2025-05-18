micro-twitter
=================
![preview](preview.webp)
## English

1. **post.php** is a backend posting interface that supports text, image, and video content types

2. Private posts and NSFW content are not supported for backend publishing

3. The backend does not support image or video uploads

4. Use nginx configuration to protect files under /private-post with HTTP Basic Authentication

5. Copyright records for private images are stored in /private-post/images and can be automatically identified

6. No database required, but PHP support is needed. If backend posting isn't required, you may delete post.php and switch to static-only

### Media Storage Guide

| Path | Example |
| :---- | :---- |
|*images/post/*| 250517-1.webp |
|*videos/post/*| 250517-1.mp4 |
|*private-post/images/*| 250517-1.webp |
|*private-post/images/*| 250517-1\.md |
|*private-post/videos/*| 250517-1.mp4 |

Your comments in this repo's discussions will show up in my micro-twitter feed :)

My blog: [卡库伊2.0](https://blog.kkii.org).

## 中文

1. **post.php** 是一个后台发布界面，支持文字、图片、视频三种类型

2. 私密帖子和nsfw的帖子不支持后台发表

3. 后台不支持图片和视频上传

4. 请使用 nginx 配置保护/private-post下的文件，如HTTP Basic验证

5. 私密图片的版权记录在/private-post/images下，可以自动识别

6. 不需要数据库，但需要PHP支持，如果不需要后台发布，可以删除 **post.php** ，改为纯静态

### 媒体存放说明

| 路径 | 例子 |
| :---- | :---- |
|*images/post/*| 250517-1.webp |
|*videos/post/*| 250517-1.mp4 |
|*private-post/images/*| 250517-1.webp |
|*private-post/images/*| 250517-1\.md |
|*private-post/videos/*| 250517-1.mp4 |

你在该仓库discussions的留言会显示在我的 micro-twitter 里 :)

我的博客:[卡库伊2.0](https://blog.kkii.org)。