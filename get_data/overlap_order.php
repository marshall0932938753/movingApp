<?php
require 'get_data_fun.php';

$order_num = mysqli_num_rows(order_num());
for($i = 1; $i <= $order_num; $i++){
  $temp_result = overlap_order($_POST['datetime'], $_POST['endtime'], $i, $_POST['company_id'], "vehicle");
  if($temp_result->num_rows > 0) $result[] = $temp_result;
}
for($i = 1; $i <= $order_num; $i++){
  $temp_result = overlap_order($_POST['datetime'], $_POST['endtime'], $i, $_POST['company_id'], "staff");
  if($temp_result->num_rows > 0) $result[] = $temp_result;
}
if(!isset($result)){
  echo "no data";
  return;
}

$row_result = result_to_array($result);
return array_to_json($row_result);

function order_num(){
  $sql_query = "SELECT * ";
  $sql_query .= "FROM `orders`;";
  $result = query($sql_query);
  return $result;
}

function overlap_order($datetime, $endtime, $i, $company_id, $kind){
  if(!strcmp($kind, "staff")) $select_data = "staff.staff_id, staff_name";
  else $select_data = "vehicle.vehicle_id, vehicle_weight, vehicle_type, plate_num";
  $sql_query = "SELECT choose.order_id, moving_date, estimate_worktime, ".$select_data." ";
  $sql_query .= "FROM (((`choose` NATURAL JOIN `orders`) ";
  $sql_query .= "LEFT OUTER JOIN `".$kind."_assignment` ON choose.order_id = ".$kind."_assignment.order_id) ";
  $sql_query .= "LEFT OUTER JOIN `".$kind."` ON ".$kind."_assignment.".$kind."_id = ".$kind.".".$kind."_id) ";
  $sql_query .= "WHERE choose.order_id = ".$i." ";
  $sql_query .= "AND choose.company_id = ".$company_id." ";
  $sql_query .= "AND order_status = 'assigned' ";
  $sql_query .= "AND (('".$datetime."' >= moving_date ";
  $sql_query .=        "AND '".$datetime."' < ";
  $sql_query .=           "(SELECT date_add(moving_date, INTERVAL ";
  $sql_query .=                       "(SELECT estimate_worktime FROM `choose` ";
  $sql_query .=                       "WHERE order_id = ".$i." ";
  $sql_query .=                       "AND company_id = ".$company_id.") HOUR) AS end_moving_time ";
  $sql_query .=            "FROM `choose` ";
  $sql_query .=            "WHERE order_id = ".$i." ";
  $sql_query .=            "AND company_id = ".$company_id."))";
  $sql_query .= "OR ('".$endtime."' >= moving_date ";
  $sql_query .=       "AND '".$endtime."' < ";
  $sql_query .=            "(SELECT date_add(moving_date, INTERVAL ";
  $sql_query .=                      "(SELECT estimate_worktime FROM `choose` ";
  $sql_query .=                       "WHERE order_id = ".$i." ";
  $sql_query .=                       "AND company_id = ".$company_id.") HOUR) AS end_moving_time ";
  $sql_query .=             "FROM `choose` ";
  $sql_query .=             "WHERE order_id = ".$i." ";
  $sql_query .=             "AND company_id = ".$company_id.")) ";
  $sql_query .= "OR ('".$datetime."' < moving_date ";
  $sql_query .=       "AND '".$endtime."' > ";
  $sql_query .=            "(SELECT date_add(moving_date, INTERVAL ";
  $sql_query .=                      "(SELECT estimate_worktime FROM `choose` ";
  $sql_query .=                       "WHERE order_id = ".$i." ";
  $sql_query .=                       "AND company_id = ".$company_id.") HOUR) AS end_moving_time ";
  $sql_query .=             "FROM `choose` ";
  $sql_query .=             "WHERE order_id = ".$i." ";
  $sql_query .=             "AND company_id = ".$company_id.")));";
  $result = query($sql_query);
  return $result;
}

 ?>
