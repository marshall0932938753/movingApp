<?php
require 'get_data_fun.php';

$result[] = order_finish_count($_POST['company_id']);
$result[] = order_paid_count($_POST['company_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function order_finish_count($company_id){
  $sql_query = "SELECT COUNT(orders.order_id) AS finish_amount ";
  $sql_query .= "FROM `orders` NATURAL JOIN `choose` ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "AND (order_status = 'done' ";
  $sql_query .= "     OR order_status ='paid');";
  $result = query($sql_query);
  return $result;
}

function order_paid_count($company_id){
  $sql_query = "SELECT COUNT(orders.order_id) AS paid_amount ";
  $sql_query .= "FROM `orders` NATURAL JOIN `choose` ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "AND order_status = 'paid';";
  $result = query($sql_query);
  return $result;
}
 ?>
