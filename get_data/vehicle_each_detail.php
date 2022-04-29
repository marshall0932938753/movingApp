<?php
require 'get_data_fun.php';

$result[] = vehicle_each_detail($_POST['order_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function vehicle_each_detail($order_id){
  $sql_query = "SELECT * FROM `vehicle_assignment` NATURAL JOIN `vehicle` ";
  $sql_query .= "WHERE order_id = ".$order_id.";";
  $result = query($sql_query);
  return $result;
}
 ?>
