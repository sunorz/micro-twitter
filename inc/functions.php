<?php
require('conn.php');
function check_session($uid)
{
	$query="select * from login where passwd like '$uid'";
	$result=mysql_query($query);
	if(mysql_num_rows($result)>0)
	{
		return true;
	}
	else
	return false;
}
?>