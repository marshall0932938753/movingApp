<?php
require 'get_data_fun.php';

if(isset($_POST['startDate'])){
  $result[] = order_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], $_POST['status'], 'TRUE');
  $result[] = order_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], $_POST['status'], 'FALSE');
}
else{
  $today = gmdate("Y-m-d", time() + 3600*8);
  $result[] = order_member($_POST['company_id'], '2020-01-01', $today, $_POST['status'], 'TRUE');
  $result[] = order_member($_POST['company_id'], '2020-01-01', $today, $_POST['status'], 'FALSE');
}

$row_result = result_to_array($result);
return array_to_json($row_result);


function order_member($company_id, $startDate, $endDate, $status, $new){
  $sql_query = "SELECT * FROM (`member` NATURAL JOIN `orders`) ";
  $sql_query .= "NATURAL JOIN `choose` ";
  $sql_query .= "WHERE choose.company_id = ".$company_id." ";
  $sql_query .= "AND moving_date > '".$startDate."' ";
  $sql_query .= "AND moving_date < '".$endDate." 23:59:59' ";
  $sql_query .= "AND order_status = '".$status."'";
  $sql_query .= "AND new = ".$new.";";
  $result = query($sql_query);
  return $result;
}
?>
