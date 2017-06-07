<?php
session_start();
// include ('security.php');
include ('conf/conf.php');

$name=$_REQUEST['name'];

$device=$_REQUEST['device'];	

$statusCode="";	
if (isset($name) && isset($device)) {
	$bResult=addUser($name,$device);
}
	if($bResult==false)
	{
		echo false;
	}
	else
	{
		echo $bResult;

	}

?>