<?php
include('dbfunctions.php');
include('loginlog_ctrlr.php');


function addUser($name,$device){
	$sql="INSERT INTO userinfo".
			" (`name`, `device`) ".
			" VALUES(:name,:device) ";
	$valueArray=array(
		':name'=>$name,
		':device'=>$device
		);
	$addusers=dbInsert($sql,$valueArray,true);	
	if($addusers){
		return $addusers;	
	}else{
		return false;
	}
}
function addTrack($clat,$clon,$userid){
	$sql="INSERT INTO userlocation".
			" (`user_id`, `location_type`, `latitude`, `longitude`) ".
			" VALUES(:userid,:loc_type,:clat,:clon) ";
	$valueArray=array(
		':userid'=>$userid,
		':loc_type'=>"Html5",
		':clat'=>$clat,
		':clon'=>$clon
		);
	$addusers=dbInsert($sql,$valueArray,true);
	if($addusers){
		return true;	
	}else{
		return false;
	}
}
?>