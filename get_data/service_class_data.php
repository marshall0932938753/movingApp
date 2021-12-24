<?php
require 'get_data_fun.php';

$result[] = serviceClass_data();
$row_result = result_to_array($result);
return array_to_json($row_result);

function serviceClass_data(){
  $sql_query = "SELECT * FROM `service_class` ";
  $result = query($sql_query);
  return $result;
}
 ?>