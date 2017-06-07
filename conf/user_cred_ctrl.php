<?php
include('dbfunctions.php');
include('loginlog_ctrlr.php');
	
	function addUserCrediential($username,$password,$staffrole,$staffname){
		//echo $username.$password.$staffrole;
	$sql ="INSERT INTO login_credential(username,password,staff_id,staff_role_id) VALUES (:username,password(:password),:staff_id,:staffrole_id)";

	$valueArray = array(
			':username'=>$username,
			':password'=>$password,
			':staff_id'=>$staffname,			
	        ':staffrole_id'=>$staffrole
		);
	 $userid=dbInsert($sql,  $valueArray, true);

		 if($userid){
	 	return true;
	 }else{
	 	return false;
	 }
}
function updateUser($user_id,$username,$staffrole){
	$sql="UPDATE `login_credential` SET `username`=:username,`staff_role_id`=:staffrole".
			" WHERE `id`=:user_id";
	$valueArray=array(
			':username'=>$username,
			':staffrole'=>$staffrole,
			':user_id'=>$user_id
		);
	$updateUser=dbUpdate($sql, $valueArray);
	if($updateUser){
		return true;	
	}else{
		return false;
	}
}
function updateUserPassword($user_id,$password){
	$sql="UPDATE `login_credential` SET `password`=password(:password)".
			" WHERE `id`=:user_id";
	$valueArray=array(
			':user_id'=>$user_id,
			':password'=>$password
		);
	$updateUserPassword=dbUpdate($sql, $valueArray);
	if($updateUserPassword){
		return true;	
	}else{
		return false;
	}
}
?>