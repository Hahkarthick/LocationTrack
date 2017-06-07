<?php

	
	function auditLog($sql, $valueArray, $username){
		
		$tableName=getTableName($sql);
		//echo "<br/>tablename : $tableName";
				
		$pkColumnName=getPKFromTable($tableName);
		//echo "<br/>PK : $pkColumnName";
		
		$pkValue = $valueArray[":" . $pkColumnName];
		//echo "<br/>PK Value : $pkValue " ;
		
		
		//~ echo "<pre>" . print_r($valueArray,true) . "</pre>";
		
		//~ return;
		//~ $noofcolumns=count($valueArray);
		//~ for($i=0;$i<$noofcolumns;$i++){
		foreach ($valueArray as $key => $value) {
			//~ echo "<br/> $key - $value";
			if($key==(":" .$pkColumnName)) continue;
			$column=str_replace(":", "", $key);
			$columnValue=$value;
			$columnOldValue=getOldValue($tableName, $column, $pkColumnName, $pkValue);
			if($columnValue!=$columnOldValue){
				$id=saveAuditLog($tableName,$column,$columnOldValue,$columnValue,
								$pkColumnName,$pkValue,$username);
			}
			//~ echo "<br/> id : $id ";
			
			//~ echo "<br/>$column - $columnValue - $columnOldValue";
		}
		
	}
	
		
	function getPKFromTable($tableName){
		$sql="SELECT `COLUMN_NAME` " .
				" FROM `information_schema`.`COLUMNS` " .
				" WHERE (`TABLE_SCHEMA` = 'A883743_poshanmarg') " .
				" AND (`TABLE_NAME` = :tablename) " .
				" AND (`COLUMN_KEY` = 'PRI') ";
		$valueArray= array(":tablename"=>$tableName);
		$row=dbSelectRow($sql,$valueArray);
		return $row["COLUMN_NAME"];
	}

	function getTableName($sql){
		$setPos=stripos($sql,"SET",0);
		$updTab=substr($sql,0,$setPos);
		$arr=explode(" ", $updTab);
		return $arr[1];
	}
	
	function getOldValue($tableName, $column, $pkColumnName, $pkValue){
		$sql = " Select $column from $tableName " .
				" where $pkColumnName=:pkValue";
		$valueArray= array(":pkValue"=>$pkValue);
		$row=dbSelectRow($sql,$valueArray);
		return $row[$column];		
	}
	
	
	function saveAuditLog($tablename,$columnname,$oldvalue,$newvalue,$pkeycolumn,$pkeyvalue,$username){
		
		$sql="INSERT INTO audit_log(tablename,columnname,oldvalue,newvalue,pkeycolumn,pkeyvalue,username) 
			  VALUES(:tablename,:columnname,:oldvalue,:newvalue,:pkeycolumn,:pkeyvalue,:username)"; 
		
		$values=array(
				':tablename'=>$tablename,
				':columnname'=>$columnname,
				':oldvalue'=>$oldvalue,
				':newvalue'=>$newvalue,
				':pkeycolumn'=>$pkeycolumn,
				':pkeyvalue'=>$pkeyvalue,
				':username'=>$username);
		
		return dbInsert($sql, $values, true);
	}
?>
