<!doctype html>

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">

<?php

if (!session_id()) session_start();

$uid=$urole="";

if(isset($_SESSION['uid']))

	{
		$uid=$_SESSION['uid'];
		require('functions.php');
		if(!check_session($uid))
		{
		header("Location:/");	
		}		

	}

	if($uid=="")

	{

		header("Location:/");

	}

	

?>

<title>修改密码</title>
<link href="../css/main.css" rel="stylesheet" type="text/css">
</head>



<body>

<div style="margin:0 auto; width:90%;">
<form name="my_f1" action="chgpwd.php" method="post" onsubmit="return check(this);" autocomplete="off">

  <center><input placeholder="Enter your old code here." name="old" type="password" required="required"></center>
<p></p>
  <center><input  placeholder="Enter your new code here." maxlength="18" name="newp" type="password" required="required"></center>
<p></p>
  <center><input  placeholder="Confirm your new code." maxlength="18" name="newf" type="password" required="required"></center>
<p></p>
  <center><input onClick="check()" type="submit" value="改吧"></center>

</form>
</div>
<script>

 function check(form) {

            var p1 = form.old;

            var p2 = form.newp;

			var p3 = form.newf;

            if (p2.value != p3.value) {

                p3.oninvalid();

                return false;

            }

            return true;

        }

        onload = function() {

            var p3 = document.forms["my_f1"].newf;

            p3.oninvalid = function() {

                alert("密码不一致，请重新输入");

            }

             

        }

</script>

<?php

if($_SERVER['REQUEST_METHOD']="POST")

{

	if(isset($_POST['old'])&&isset($_POST['new'])&&isset($_POST['newf']))

	{

		$old=$_POST['old'];

		$new=$_POST['new'];

		$newf=md5($_POST['newf']);

		if($old!=$newf)

		{	

		$newf=md5("axS09ada1aUH".$newf."x0IsaCa8");	

		$query2="update login set passwd='$newf'";

		mysql_query("set names 'utf8'");

		//echo $query2;

		mysql_query($query2);

		if(isset($_SESSION['uid'])){unset($_SESSION['uid']);}

		echo '<script> alert("修改密码成功，请重新登录！");document.location.href="../login.php";</script>';

		}

	}

}

?>

</body>

</html>