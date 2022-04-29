<?php
require 'get_data_fun.php';

$result[] = all_staff_data($_POST['company_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function all_staff_data($company_id){
  $sql_query = "SELECT * FROM `staff` ";
  $sql_query .= "WHERE company_id = ".$company_id."  ";
  $sql_query .= "AND end_time IS NULL;";
  $result = query($sql_query);
  return $result;
}
 ?>
