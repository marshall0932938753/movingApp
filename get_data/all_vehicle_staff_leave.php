<?php
require 'get_data_fun.php';

$result[] = vehicle_maintain($_POST['company_id'], $_POST['date']);
$result[] = staff_leave($_POST['company_id'], $_POST['date']);
$row_result = result_to_array($result);
return array_to_json($row_result);

/*顯示請假的人*/
function staff_leave($company_id, $date){
  $sql_query = "SELECT * FROM `staff_leave` ";
  $sql_query .= "NATURAL JOIN staff ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "AND leave_date = '".$date. "';";
  $result = query($sql_query);
  return $result;
}

/*顯示維修的車*/
function vehicle_maintain($company_id, $date){
  $sql_query = "SELECT * FROM `vehicle_maintain` ";
  $sql_query .= "NATURAL JOIN vehicle ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "AND maintain_date = '".$date. "';";
  $result = query($sql_query);
  return $result;
}
 ?>
