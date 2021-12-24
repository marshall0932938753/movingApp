<?php
require 'get_data_fun.php';

$year = $_POST['year'];
$monthStr = getMonth1($year, $_POST['month']);
$month2Str = getMonth2($year, $_POST['month']);

$result[] = pay_oneMonth($_POST['company_id'], $monthStr, $month2Str);
$row_result = result_to_array($result);
if(isset($row_result)) $row_result2 = orderByDay($row_result);
else $row_result2 = $_POST['month']." month no order";
return array_to_json($row_result2);

function pay_oneMonth($company_id, $monthStr, $month2Str){
  $sql_query = "SELECT staff_id, staff_name, SUM(pay) AS total_payment, moving_date ";
  $sql_query .= "FROM (`staff_assignment` NATURAL JOIN `staff`) ";
  $sql_query .= "NATURAL JOIN `choose` ";
  $sql_query .= "WHERE staff.company_id = ".$company_id." ";
  $sql_query .= "AND moving_date > '".$monthStr."' ";
  $sql_query .= "AND moving_date < '".$month2Str."' ";
  $sql_query .= "AND pay <> -1 ";
  $sql_query .= "GROUP BY staff_id;";
  $result = query($sql_query);
  return $result;
}
 ?>
