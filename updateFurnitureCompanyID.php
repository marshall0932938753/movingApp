<?php
	header("Content-Type: text/html; charset=utf-8");
	require 'furniture_sql.php';
	
	function setCompanyID($order_id, $company_id){
		$sql_query = "UPDATE `furniture_list` ";
		$sql_query .= "SET company_id = ".$company_id." ";
		$sql_query .= "WHERE order_id = ".$order_id.";";
		$result = query($sql_query);
		$res = new stdClass();
		if(!strcmp($result, "1")){
			return "success";
		}
		else{
		
			return $result;
		}
		
	}
	setCompanyID($_POST['order_id'], $_POST['company_id']);
?>
