<?php
session_start();
 
// include ('security.php');
include ('conf/conf.php');

$clat=$_REQUEST['clat'];
$clon=$_REQUEST['clon'];
$userid=$_REQUEST['userid'];

$statusCode="";	
$bResult=addTrack($clat,$clon,$userid);

	if($bResult==false)
	{
		echo false;
	}
	else
	{
		echo true;

	}

?>