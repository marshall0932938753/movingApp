<?php
require 'get_data_fun.php';

$result[] = valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], "cancel", 'TRUE');
$result[] = self_valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], 'TRUE', true);
$result[] = valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'],  "cancel", 'FALSE');
$result[] = self_valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], 'FALSE', true);
$row_result = result_to_array($result);
return array_to_json($row_result);

function valuation_member($company_id, $startDate, $endDate, $status, $new){
  $sql_query = "SELECT * FROM `member` NATURAL JOIN ";
  $sql_query .= "(`orders` NATURAL JOIN `choose`) ";
  $sql_query .= "WHERE choose.company_id = ".$company_id." ";
  $sql_query .= "AND valuation_date >= '".$startDate."' ";
  $sql_query .= "AND valuation_date < '".$endDate." 23:59:59' ";
  $sql_query .= "AND valuation_status = '".$status."' ";
  $sql_query .= "AND new = ".$new.";";
  $result = query($sql_query);
  return $result;
}

function self_valuation_member($company_id, $startDate, $endDate, $new, $isCancel){
  $sql_query = "SELECT * FROM `member` NATURAL JOIN ";
  $sql_query .= "(`orders` NATURAL JOIN `choose`) ";
  $sql_query .= "WHERE choose.company_id = ".$company_id." ";
  $sql_query .= "AND last_update >= '".$startDate."' ";
  $sql_query .= "AND last_update < '".$endDate." 23:59:59' ";
  if($isCancel) $sql_query .= "AND valuation_status = 'cancel' ";
  else $sql_query .= "AND valuation_status = 'self' ";
  $sql_query .= "AND new = ".$new.";";
  $result = query($sql_query);
  return $result;
}
 ?>
