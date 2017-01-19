micro-twitter
=================
##English
###Config
1.config **inc/con.php** to link database

`Database field`
<div>
    <table border="0">
      <tr>
        <th>fieldname</th>
        <th>type</th>
      </tr>
      <tr>
        <td>passwd</td>
        <td>varchar(50)</td>
      </tr>
    </table>
</div>

`data table name`**login**

2.upload a picture per month to **gallery**.Size(150px\*150px,GIF)

3.config the countdown in **index.html** if you want.

\# Before your first post at month you will type a title in this month page.

###How to play music on post?

1.upload your music(**mp3 only**) to    http://*yourdomain*/*postdate,170101*.mp3

2.config **post.php** to display where you post music link and it'll play it with tag `audio`.

exp.If today is Jan. 19th,2017.Rename your audio name to `170119` make sure it's `mp3`.Upload it to cloud where you want to store.
Like `http://abc.com/mymusic`.Then post star with word 'Music'.



any question plz send to [sunplace@live.cn](mailto:sunplace@live.cn).

my website:[卡库伊2.0](http://www.jsunplace.com).

##中文

1.配置**inc/con.php**来链接你的数据库。

建立数据库，表**login**

<div>
    <table border="0">
      <tr>
        <th>字段名称</th>
        <th>类型</th>
      </tr>
      <tr>
        <td>passwd</td>
        <td>varchar(50)</td>
      </tr>
    </table>
</div>

\#如果要修改字段名和表名，需连同**inc/chgpwd.php**、**inc/functions.php**。

2.每个月发帖之前要上传一张150px\*150px的GIF图片到**gallery**。

3.首页有个Jquery倒计时，可以删除或修改，默认秒。

4.引用音频地址在**post.php**里配置。

![sunplace](http://www.jsunplace.com/copyright_by_sunplace.png)
