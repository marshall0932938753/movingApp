<?php
require 'get_data_fun.php';

$year = $_POST['year'];
$monthStr = getMonth1($year, $_POST['month']);
$month2Str = getMonth2($year, $_POST['month']);

$result[] = order_member_oneMonth($_POST['company_id'], $monthStr, $month2Str);
if($result[0]->num_rows > 0){
  $row_result = result_to_array($result);
  $row_result2 = orderByDay($row_result);
  return array_to_json($row_result2);
}
else {
  // print_r($result);
  echo "no result";
  return "no result";
}

function order_member_oneMonth($company_id, $monthStr, $month2Str){
  $sql_query = "SELECT orders.order_id AS order_id, valuation_date, moving_date, last_update, valuation_status, order_status, member_name ";
  $sql_query .= "FROM (`member` NATURAL JOIN `orders`) ";
  $sql_query .= "NATURAL JOIN `choose` ";
  $sql_query .= "WHERE choose.company_id = ".$company_id." ";
  $sql_query .= "AND ((moving_date >= '".$monthStr."' ";
  $sql_query .=       "AND moving_date < '".$month2Str."') ";
  $sql_query .= "OR (valuation_date >= '".$monthStr."' ";
  $sql_query .=     "AND valuation_date < '".$month2Str."' ";
  $sql_query .=     "AND moving_date IS NULL ";
  $sql_query .=     "AND valuation_status = 'booking') ";
  $sql_query .= "OR (last_update >= '".$monthStr."' ";
  $sql_query .=     "AND last_update < '".$month2Str."' ";
  $sql_query .=     "AND valuation_status = 'self') ";
  $sql_query .= ") ";
  $sql_query .= "ORDER BY moving_date, valuation_date;";
  $result = query($sql_query);
  return $result;
}
 ?>
