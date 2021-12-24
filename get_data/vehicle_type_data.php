<?php
require 'get_data_fun.php';

$result[] = vehicle_type_data($_POST['company_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function vehicle_type_data($company_id){
  $sql_query = "SELECT vehicle_weight, vehicle_type FROM `vehicle` ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "AND end_time IS NULL ";
  $sql_query .= "GROUP BY vehicle_weight, vehicle_type;";
  $result = query($sql_query);
  return $result;
}
 ?>
