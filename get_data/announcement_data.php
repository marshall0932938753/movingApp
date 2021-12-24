<?php
require 'get_data_fun.php';

$result[] = announcement_data($_POST['company_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function announcement_data($company_id){
  $sql_query = "SELECT * FROM `announcement` NATURAL JOIN `announcement_company` ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "ORDER BY announcement_date DESC;";
  $result = query($sql_query);
  return $result;
}
 ?>
