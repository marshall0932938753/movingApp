<?php
require 'get_data_fun.php';

$date = $_POST['date'];
$isOrder = FALSE;
if(!strcmp("today",$date)){
  $timezone = 8; //GMT+8
  $date = gmdate("Y-m-d", time() + 3600*($timezone));
  $isOrder = TRUE;
}
if(isset($_POST['isOrder'])) $isOrder = TRUE;
$result[] = order_member_oneDay($_POST['company_id'], $date, $isOrder);
$row_result = result_to_array($result);
$row_result2 = orderBytime($row_result);
return array_to_json($row_result2);

function order_member_oneDay($company_id, $date, $isOrder){
  $sql_query = "SELECT * FROM `member` NATURAL JOIN (`orders` NATURAL JOIN `choose`) ";
  $sql_query .= "WHERE choose.company_id = ".$company_id." ";
  $sql_query .= "AND ( ";
  $sql_query .=     "(moving_date >= '".$date."' ";
  $sql_query .=      "AND moving_date < '".$date." 23:59:59' ";
  $sql_query .=      "AND (`orders`.`order_status` = 'scheduled' OR `orders`.`order_status` = 'assigned' OR valuation_status = 'match')) ";
  if($isOrder){
    $sql_query .= ") ";
    $sql_query .= "ORDER BY moving_date;";
  }
  else{
    $sql_query .=    "OR ";
    $sql_query .=    "(valuation_date = '".$date."' ";
    $sql_query .=     "AND valuation_status = 'booking') ";
    $sql_query .=    "OR ";
    $sql_query .=    "(last_update >= '".$date."' ";
    $sql_query .=     "AND last_update < '".$date." 23:59:59' ";
    $sql_query .=     "AND valuation_status = 'self') ";
    $sql_query .= ") ";
    $sql_query .= "ORDER BY moving_date, valuation_time;";
  }
  $result = query($sql_query);
  return $result;
}
 ?>
