<?php
require 'get_data_fun.php';

$result[] = serviceItem_data($_POST['company_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function serviceItem_data($company_id){
  $sql_query = "SELECT * FROM `service_item` NATURAL JOIN `service_class` ";
  $sql_query.= "WHERE company_id = ".$company_id." ";
  $sql_query.= "AND ((isDelete = FALSE AND end_time IS NOT NULL)";
  $sql_query.= "     OR (end_time IS NULL));";
  $result = query($sql_query);
  return $result;
}
 ?>
