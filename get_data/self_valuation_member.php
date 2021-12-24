<?php
require 'get_data_fun.php';

$result[] = self_valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], 'TRUE', false);
$result[] = self_valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], 'FALSE', false);
$row_result = result_to_array($result);
return array_to_json($row_result);

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
