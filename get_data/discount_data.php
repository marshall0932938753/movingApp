<?php
require 'get_data_fun.php';

$result[] = free_discount_data($_POST['company_id']);
$result[] = period_discount_data($_POST['company_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function free_discount_data($company_id){
  $sql_query = "SELECT * FROM `discount` ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "AND update_time = ";
  $sql_query .= "    (SELECT MAX(update_time) FROM `discount` ";
  $sql_query .= "     WHERE company_id = ".$company_id.");";
  $result = query($sql_query);
  return $result;
}

function period_discount_data($company_id){
  $sql_query = "SELECT * FROM `period_discount` ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  // $sql_query .= "AND isDelete = FALSE;";
  $result = query($sql_query);
  return $result;
}
 ?>
