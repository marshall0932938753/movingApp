<?php
require 'get_data_fun.php';

$result[] = comment_data($_POST['company_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function comment_data($company_id){
  $sql_query = "SELECT * FROM (`comments` NATURAL JOIN `orders`) NATURAL JOIN `member` ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "ORDER BY comment_date DESC;";
  $result = query($sql_query);
  return $result;
}
 ?>
