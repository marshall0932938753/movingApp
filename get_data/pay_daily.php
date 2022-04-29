<?php
require 'get_data_fun.php';

$year = $_POST['year'];
$monthStr = getMonth1($year, $_POST['month']);
$month2Str = getMonth2($year, $_POST['month']);

$result[] = pay_daily($_POST['company_id'], $monthStr, $month2Str);
$row_result = result_to_array($result);
return array_to_json($row_result);

function pay_daily($company_id, $monthStr, $month2Str){
  $sql_query = "SELECT order_id, staff_id, staff_name, pay, moving_date ";
  $sql_query .= "FROM (`staff_assignment` NATURAL JOIN `staff`) NATURAL JOIN `choose` ";
  $sql_query .= "WHERE staff.company_id = ".$company_id." ";
  $sql_query .= "AND moving_date >= '".$monthStr."' ";
  $sql_query .= "AND moving_date < '".$month2Str."' ";
  $sql_query .= "ORDER BY staff_name, moving_date ASC;";
  $result = query($sql_query);
  return $result;
}
 ?>
