<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
<title>登录</title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div style="margin:0 auto; width:90%;">
<form method="post" action="login.php" autocomplete="off"><center><input  name="code" placeholder="输入创作码" type="password"></center><div style="height:20px;"></div><center><input type="submit" value="登录"></center></form>
</div>
<?php 

if($_SERVER['REQUEST_METHOD']=="POST")

{

	if(isset($_POST['code']))

	{

		include('inc/conn.php');	

		$mmjm=md5("axS09ada1aUH".$_POST['code']."x0IsaCa8");

		

		$query1="select * from login where passwd like '$mmjm'";

		mysql_query("set names 'utf8'");

		$result1=mysql_query($query1);

		if(mysql_num_rows($result1)>0)

		{

			$row1=mysql_fetch_array($result1);			

			$_SESSION['uid']=$row1['passwd'];			

			

			header('Location:post.php');

				

		}

		

	}





}

?>



</body>

</html>