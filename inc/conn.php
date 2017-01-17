<?php if (!session_id()) session_start();
date_default_timezone_set('Asia/Shanghai'); 
$con = mysql_connect("localhost","","");//连接数据库服务器
if (!$con)
  {
  die('无法连接数据库: ' . mysql_error());
  }
 
        mysql_select_db("jonsun23_ei", $con);//连接指定的数据库
		return $con;
		?>