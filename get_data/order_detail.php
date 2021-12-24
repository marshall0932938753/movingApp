<?php
require 'get_data_fun.php';

$result[] = order_detail($_POST['order_id'], $_POST['company_id']);
if(!isset($_POST['assign'])){
  $result[] = vehicle_detail($_POST['order_id']);
}
else {
  $result[] = vehicle_demand_data($_POST['order_id']);
  $result[] = vehicle_each_detail($_POST['order_id']);
}
$result[] = staff_detail($_POST['order_id']);

$row_result = result_to_array($result);
return array_to_json($row_result);

function order_detail($order_id, $company_id){
  $sql_query = "SELECT * FROM (`member` NATURAL JOIN `orders`) ";
  $sql_query .= "NATURAL JOIN `choose` ";
  $sql_query .= "WHERE choose.company_id = ".$company_id." ";
  $sql_query .= "AND orders.order_id = '".$order_id."';";
  $result = query($sql_query);
  return $result;
}

function vehicle_detail($order_id){
  $sql_query = "SELECT vehicle_weight, vehicle_type, COUNT(*) AS num ";
  $sql_query .= "FROM `vehicle_assignment` NATURAL JOIN `vehicle` ";
  $sql_query .= "WHERE order_id = ".$order_id." ";
  $sql_query .= "GROUP BY vehicle_weight, vehicle_type;";
  $result = query($sql_query);
  return $result;
}

function vehicle_demand_data($order_id){
  $sql_query = "SELECT * FROM `vehicle_demand` ";
  $sql_query .= "WHERE order_id = ".$order_id.";";
  $result = query($sql_query);
  return $result;
}

function vehicle_each_detail($order_id){
  $sql_query = "SELECT * FROM `vehicle_assignment` NATURAL JOIN `vehicle` ";
  $sql_query .= "WHERE order_id = ".$order_id.";";
  $result = query($sql_query);
  return $result;
}

function staff_detail($order_id){
  $sql_query = "SELECT * FROM `staff` NATURAL JOIN `staff_assignment` ";
  $sql_query .= "WHERE staff_assignment.order_id = ".$order_id.";";
  $result = query($sql_query);
  return $result;
}
 ?>
