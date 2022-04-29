<?php
require 'get_data_fun.php';

$result[] = choose_data($_POST['company_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function choose_data($company_id){
  $sql_query = "SELECT * FROM `choose` ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $result = query($sql_query);
  return $result;
}
 ?>