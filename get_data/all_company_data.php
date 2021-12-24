<?php
require 'get_data_fun.php';

$result[] = all_company_data();
$row_result = result_to_array($result);
return array_to_json($row_result);

function all_company_data(){
  $sql_query = "SELECT company_id, company_name FROM `company`;";
  $result = query($sql_query);
  return $result;
}
 ?>
