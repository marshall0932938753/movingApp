<?php
require 'get_data_fun.php';

$timezone = 8; //GMT+8
$date = gmdate("Y-m-d", time() + 3600*($timezone));
$result[] = order_member_today($_POST['company_id'], $date);
$row_result = result_to_array($result);
$row_result2 = orderBytime($row_result);
return array_to_json($row_result2);

function order_member_today($company_id, $date){
  $sql_query = "SELECT * FROM `member` NATURAL JOIN (`orders` NATURAL JOIN `choose`) ";
  $sql_query .= "WHERE choose.company_id = ".$company_id." ";
  $sql_query .= "AND moving_date < '".$date." 23:59:59' ";
  $sql_query .= "AND order_status = 'assigned' ";
  $sql_query .= "ORDER BY moving_date;";
  $result = query($sql_query);
  return $result;
}


 ?>
