<?php
require 'get_data_fun.php';

$result[] = valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], $_POST['status'], 'TRUE');
$result[] = valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], $_POST['status'], 'FALSE');
$row_result = result_to_array($result);
if($row_result == null){
  echo "null";
  return "null";
}

$valuation_time = $row_result[0]['valuation_time'];
$vtimes = explode('~', $valuation_time);
for($i = 0; $i < 2; $i++){
  if(!strcmp($vtimes[$i], "null")) continue;
  $vtime = explode(':', $vtimes[$i]);
  if(strcmp($vtime[0], "null")) $vtime[0] = str_pad($vtime[0], 2, '0', STR_PAD_LEFT);
  if(strcmp($vtime[1], "null")) $vtime[1] = str_pad($vtime[1], 2, '0', STR_PAD_LEFT);
  $vtimes[$i] = implode(':', $vtime);
}
if(!strcmp($vtimes[0], "null")) $vtimes[0] = $vtimes[1];
else if(!strcmp($vtimes[1], "null")) $vtimes[1] = $vtimes[0];
$valuation_time = implode('~', $vtimes);
if(!strcmp($vtimes[0], "null") && !strcmp($vtimes[1], "null")) $valuation_time = "null";
$row_result[0]['valuation_time'] = $valuation_time;

$result_json = json_encode($row_result);
echo $result_json;
return $result_json;


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

 ?>
