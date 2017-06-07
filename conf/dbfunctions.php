<?php
include_once('dbconfig.php');
include_once('auditlog.php');

function dbInsert($sql, $valueArray, $getInsertID=false)
{

	//debug(" dbInsert entry");

	$db = Database::getInstance()->getConnection();
	$query = $db->prepare($sql);
	$query->execute($valueArray);

	//debug(" dbInsert exit");

	if($getInsertID){
		//debug(" dbInsert returns last ");
		$lastInsertId=$db->lastInsertId();
		//debug (" ID :: " . $lastInsertId);
		return $lastInsertId; 
	}
}


function dbUpdate($sql, $valueArray, $auditLog=false)
{
	//debug(" dbUpdate entry");
	
	if($auditLog===true) {
		auditLog($sql, $valueArray, $_SESSION["username"]);
	}
	
	$db = Database::getInstance()->getConnection();
	$query = $db->prepare($sql);
	if($query->execute($valueArray)){
		return $query->rowCount();			
	}
	else
	{
		return false;
	}

	//return $rowsAffected;
}

function reverse_date_format($pstr)
{
	$pstr=trim($pstr);
	$darr=explode("-",$pstr);
	return $darr[2]. "-" .$darr[1]. "-" . $darr[0];
}

function dbSelectRowAsJSON($sql,$valueArray){
	$row = dbSelectRow($sql,$valueArray);
	return json_encode($row);
}

function dbSelectRow($sql,$valueArray){
	$db = Database::getInstance()->getConnection();
	$pstmt=$db->prepare($sql);
	$pstmt->execute($valueArray);
	$pstmt->setFetchMode(PDO::FETCH_ASSOC);
	$row = $pstmt->fetch();
	return $row;
}


function dbSelectRowsAsJSON($sql,$valueArray){
	$rows = dbSelectRows($sql,$valueArray);
	return json_encode($rows);
}

function dbSelectRows($sql,$valueArray){
	$db = Database::getInstance()->getConnection();
	$pstmt=$db->prepare($sql);
	$pstmt->execute($valueArray);
	$pstmt->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $pstmt->fetchAll();
	return $rows;
}


function dbSelectRowsIndexed($sql,$valueArray){
	$db = Database::getInstance()->getConnection();
	$pstmt=$db->prepare($sql);
	$pstmt->execute($valueArray);
	$pstmt->setFetchMode(PDO::FETCH_NUM);
	$rows = $pstmt->fetchAll();
	return $rows;
}


function getRecordCountTable($tableName,$condition){
	$sql="SELECT COUNT(*) AS RecordCount FROM $tableName $condition";
	$valueArray=array();
	
	$db = Database::getInstance()->getConnection();
	$pstmt=$db->prepare($sql);
	$pstmt->execute($valueArray);
	$pstmt->setFetchMode(PDO::FETCH_ASSOC);
	$row = $pstmt->fetch();
	return $row["RecordCount"];
}

function getCSV($rows){
	//no records from DB
	if(empty($rows)){
	   return "No records for this category";
	}
	//if records returned from DB
	$uniqueIds = array_keys($rows[0]);
	$header=implode(",", $uniqueIds); 	
	$fileContent = $header. "\r\n";
	foreach($rows as $row){
		$fileContent .=  implode(",", $row) . "\r\n";
	}
	return $fileContent;
}

function writeCSV($filename, $csvdata){
	$fp = fopen($filename, 'w');
	fwrite($fp, $csvdata);
	fclose($fp);
}
function reverse_date($pstr)
{
	$pstr=trim($pstr);
	$darr=explode("/",$pstr);
	return $darr[2]. "/" .$darr[1]. "/" . $darr[0]; 
}
function reverse_date_edit($pstr)
{
	$pstr=trim($pstr);
	$darr=explode("-",$pstr);
	return $darr[2]. "/" .$darr[1]. "/" . $darr[0];
}
function reverse_edit($pstr)
{
	$pstr=trim($pstr);
	$darr=explode("/",$pstr);
	return $darr[2]. "/" .$darr[1]. "/" . $darr[0];
}
function money_format_inr($format, $number) 
{ 
    $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'. 
              '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/'; 
    if (setlocale(LC_MONETARY, 0) == 'C') { 
        setlocale(LC_MONETARY, ''); 
    } 
    $locale = localeconv(); 
    preg_match_all($regex, $format, $matches, PREG_SET_ORDER); 
    foreach ($matches as $fmatch) { 
        $value = floatval($number); 
        $flags = array( 
            'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ? 
                           $match[1] : ' ', 
            'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0, 
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? 
                           $match[0] : '+', 
            'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0, 
            'isleft'    => preg_match('/\-/', $fmatch[1]) > 0 
        ); 
        $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0; 
        $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0; 
        $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits']; 
        $conversion = $fmatch[5]; 

        $positive = true; 
        if ($value < 0) { 
            $positive = false; 
            $value  *= -1; 
        } 
        $letter = $positive ? 'p' : 'n'; 

        $prefix = $suffix = $cprefix = $csuffix = $signal = ''; 

        $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign']; 
        switch (true) { 
            case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+': 
                $prefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+': 
                $suffix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+': 
                $cprefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+': 
                $csuffix = $signal; 
                break; 
            case $flags['usesignal'] == '(': 
            case $locale["{$letter}_sign_posn"] == 0: 
                $prefix = '('; 
                $suffix = ')'; 
                break; 
        } 
        if (!$flags['nosimbol']) { 
            $currency = $cprefix . 
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) . 
                        $csuffix; 
        } else { 
            $currency = ''; 
        } 
        $space  = $locale["{$letter}_sep_by_space"] ? ' ' : ''; 

        $value = number_format($value, $right, $locale['mon_decimal_point'], 
                 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']); 
        $value = @explode($locale['mon_decimal_point'], $value); 

        $n = strlen($prefix) + strlen($currency) + strlen($value[0]); 
        if ($left > 0 && $left > $n) { 
            $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0]; 
        } 
        $value = implode($locale['mon_decimal_point'], $value); 
        if ($locale["{$letter}_cs_precedes"]) { 
            $value = $prefix . $currency . $space . $value . $suffix; 
        } else { 
            $value = $prefix . $value . $space . $currency . $suffix; 
        } 
        if ($width > 0) { 
            $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ? 
                     STR_PAD_RIGHT : STR_PAD_LEFT); 
        } 

        $format = str_replace($fmatch[0], $value, $format); 
    } 
    return $format; 
} 
?>
