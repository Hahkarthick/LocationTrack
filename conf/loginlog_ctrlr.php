<?php
//include('conf/dbfunctions.php');

//runLoginLog("pakkiyaraj");
//echo "working";


function runLoginLog($username){
	$clientip=get_client_ip();
	$useragent=get_agent_details();
	insertLoginLog($username,$clientip,$useragent);
}

function insertLoginLog($username,$clientip,$useragent){
	$sql=" INSERT INTO `loginlog` " .
	     " (`username`, `clientip`, `useragent`) " .
	     " VALUES " .
	     " (:username, :clientip, :useragent) " ;
	$valueArray=array(':username'=>$username,
					  ':clientip'=>$clientip,
					  ':useragent'=>$useragent);
	     
	dbInsert($sql, $valueArray);
}

// Function to get the client ip address
function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}

function get_agent_details(){
	$agent=$_SERVER['HTTP_USER_AGENT'];
	return $agent;
}

function getLastLoginLog($username){
	$sql=" select dtstamp " .
		  " from loginlog " .
		  " where username=:username " .
		  " order by dtstamp desc ". 
		  " limit 1 " ;
	$valueArray=array(':username'=>$username);
	$lastlogindt=dbSelectRow($sql,$valueArray);
	return $lastlogindt;
}
?>
