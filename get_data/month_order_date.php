<?php
require 'get_data_fun.php';

$year = $_POST['year'];
$monthStr = getMonth1($year, $_POST['month']);
$month2Str = getMonth2($year, $_POST['month']);

$result[] = month_order_date($_POST['company_id'], $monthStr, $month2Str);
$row_result = result_to_array($result);
return array_to_json($row_result);

function month_order_date($company_id, $monthStr, $month2Str){
  $sql_query = "SELECT moving_date ";
  $sql_query .= "FROM (`staff_assignment` NATURAL JOIN `staff`) NATURAL JOIN `choose` ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "AND moving_date >= '".$monthStr."' ";
  $sql_query .= "AND moving_date < '".$month2Str."' ";
  $sql_query .= "ORDER BY moving_date ASC;";
  $result = query($sql_query);
  return $result;
}
 ?>
