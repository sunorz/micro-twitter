<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<?php

//引用
require('inc/functions.php');

//验证权限
if (!session_id()) session_start();
$uid="";
if(isset($_SESSION['uid']))
	{
		$uid=$_SESSION['uid'];
		if(!check_session($uid))
		{
		header("Location:/");	
		}		
	}
	if($uid=="")
	{
		header("Location:/");
	}
	
//根据文件来生成提交界面
if(file_exists(date("ym").'.html')){
		$poster='<center><textarea name="postcon"  required="required"   maxlength="120" placeholder="说点什么吧"></textarea></center><div style="height:20px;"></div>
<center><input type="submit"  value="甩出去"></center>';
		}	
		else
		{
		$poster='<center><input class="title-input" type="text" name="postcon"  required="required"   maxlength="'.(date("y")-15).'" placeholder="标头"></input></center><div style="height:20px;"></div>
<center><input type="submit" value="标"></center>';
		}
?>
<title>发布</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<script>
var checkSubmitFlg = false; 
function checkSubmit(){ 
if(checkSubmitFlg ==true){ return false; //当表单被提交过一次后checkSubmitFlg将变为true,根据判断将无法进行提交。
} 
checkSubmitFlg ==true; 
return true; 
} 
</script> 
<div style="width:100%;text-align:center;">
<form id="form1" name="form1" method="post" autocomplete="off" onSubmit="return checkSubmit();">
<?php echo $poster;?>
</form>
</div>
<?php 

if($_SERVER['REQUEST_METHOD']="POST")
{
	if(isset($_POST['postcon']))
	{
		$cont=trim($_POST['postcon']);	
		if($cont!="")
		{	
//判断日期
	$color='E4F8F5';
	$month=(int)date("n");
	switch($month)
	{
		case $month>=3&&$month<=5:$color='fcf7a7';$color2='cdc31d';break;
		case $month>=6&&$month<=8:$color='E4F8F5';$color2='aeccc7';break;
		case $month>=9&&$month<=11:$color='ffe0bf';$color2='e9a256';break;
		case $month==12||$month<3:$color='fddada';$color2='be4242';break;	
		default:$color='E4F8F5';$color2='aeccc7';break;		
	}
if(file_exists(date("ym").'.html'))
{
	    $filename =date("ym").'.html';	
		$file_content = file_get_contents($filename);
		$front=substr($file_content,0,strrpos($file_content,'<footer>'));
		$after=substr($file_content,strrpos($file_content,'<footer>'));
	if(strpos($cont,"Music") === 0)	
	{
		$complete=$front.'<div class="content" onClick="plays('.date("ymd").')" style="padding-bottom:5px !important; cursor:pointer;">'.$cont.'<div style=" width:100%; text-align:right;"><span>'.date("y/n/j").'</span></div>
<div id="pastime'.date("ymd").'" style="height:2px; width:0; background-color:#'.$color2.'"></div>
</div>
<audio id="music'.date("ymd").'"  src="http://yourdomain/'.date("ymd").'.mp3"></audio>'.$after;
	}
	else
	{
	
$complete=$front.'<div class="content">'.$cont.'<div style="width:100%;text-align:right;"><span>'.date("y/n/j").'</span></div></div>'.$after;
	}
	
countnum();
}
else
{

	$complete='<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<meta charset="utf-8">
<title>'.$cont.'</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
<style type="text/css">
.content{padding:10px; width:90%; border-radius:4px; background-color:#'.$color.'; margin:0 auto; margin-top:20px;line-height:2;word-break:break-all;overflow:auto;color:#000000;}
</style>
</head>
<body>
<script>
function plays(i)
{
var player=document.getElementById(\'music\'+i);
if(player.paused)
{
	player.play();
	var pastime = document.getElementById("pastime"+i);  
    var interval = setInterval(function() {  
        var widthline = Math.round(player.currentTime)/Math.round(player.duration) * 100;        
		pastime.style.width = widthline+"%"; 
             
    },500);

}
else
{
	player.pause();
	player.currentTime = 0.0; 
}

}

</script>
<div class="pic"><img src="gallery/'.date("ym").'.gif" alt=""/></div>
<footer><center>End&nbsp;of&nbsp;'.date("F").'</center></footer>
</body>

</html>';
}
		
$handle = fopen(date("ym").".html", 'w');//打开文件
		fwrite($handle, $complete);
echo "<script>window.location.href='post.php';</script>";
		}
	}
}

function countnum()
{
$filename ='index.html';	
$file_content = file_get_contents($filename);
$start=strrpos($file_content,'<span>')+6;
$end=strrpos($file_content,'</span>');
$len=$end-$start;
$center=substr($file_content,strrpos($file_content,'<span>')+6,$len);
$center++;
$center=number_format($center);
$front=substr($file_content,0,strrpos($file_content,'<span>')+6);
$after=substr($file_content,strrpos($file_content,'</span>')+7);

		$handle = fopen($filename, 'w');//打开文件

$complete=$front.$center.'</span>'.$after;

		fwrite($handle, $complete);
}
?>

</body>
</html>