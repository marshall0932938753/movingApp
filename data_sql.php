<?php


  function all_orders_data($company_id){
    $sql_query = "SELECT * FROM `orders` ";
    $sql_query .= "WHERE order_id IN (";
    $sql_query .=     "SELECT DISTINCT order_id FROM `choose` ";
    $sql_query .=     "WHERE company_id = ".$company_id." ";
    $sql_query .= ");";
    $result = query($sql_query);
    return $result;
  }

  function all_company_member($company_id){
    $sql_query = "SELECT * FROM `member` ";
    $sql_query .= "WHERE member_id IN (" ;
    $sql_query .=     "SELECT DISTINCT member_id ";
    $sql_query .=     "FROM `orders` NATURAL JOIN `choose` ";
    $sql_query .=     "WHERE company_id = ".$company_id." ";
    $sql_query .= ");";
    $result = query($sql_query);
    return $result;
  }

  function all_company_comment($company_id){
    $sql_query = "SELECT * FROM `comments` ";
    $sql_query .= "WHERE company_id = ".$company_id.";";
    $result = query($sql_query);
    return $result;
  }

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

  function order_member_today($company_id, $date){
    $sql_query = "SELECT * FROM `member` NATURAL JOIN (`orders` NATURAL JOIN `choose`) ";
    $sql_query .= "WHERE choose.company_id = ".$company_id." ";
    $sql_query .= "AND moving_date < '".$date." 23:59:59' ";
    $sql_query .= "AND order_status = 'assigned' ";
    $sql_query .= "ORDER BY moving_date;";
    $result = query($sql_query);
    return $result;
  }

  function order_member_oneDay($company_id, $date, $isOrder){
    $sql_query = "SELECT * FROM `member` NATURAL JOIN (`orders` NATURAL JOIN `choose`) ";
    $sql_query .= "WHERE choose.company_id = ".$company_id." ";
    $sql_query .= "AND ( ";
    $sql_query .=     "(moving_date >= '".$date."' ";
    $sql_query .=      "AND moving_date < '".$date." 23:59:59' ";
    $sql_query .=      "AND (`orders`.`order_status` = 'scheduled' OR `orders`.`order_status` = 'assigned' OR valuation_status = 'match')) ";
    if($isOrder){
      $sql_query .= ") ";
      $sql_query .= "ORDER BY moving_date;";
    }
    else{
      $sql_query .=    "OR ";
      $sql_query .=    "(valuation_date = '".$date."' ";
      $sql_query .=     "AND valuation_status = 'booking') ";
      $sql_query .=    "OR ";
      $sql_query .=    "(last_update >= '".$date."' ";
      $sql_query .=     "AND last_update < '".$date." 23:59:59' ";
      $sql_query .=     "AND valuation_status = 'self') ";
      $sql_query .= ") ";
      $sql_query .= "ORDER BY moving_date, valuation_time;";
    }
    $result = query($sql_query);
    return $result;
  }

  function order_member_oneMonth($company_id, $monthStr, $month2Str){
    $sql_query = "SELECT orders.order_id AS order_id, valuation_date, moving_date, last_update, valuation_status, order_status, member_name ";
    $sql_query .= "FROM (`member` NATURAL JOIN `orders`) ";
    $sql_query .= "NATURAL JOIN `choose` ";
    $sql_query .= "WHERE choose.company_id = ".$company_id." ";
    $sql_query .= "AND (";
    $sql_query .=     "(moving_date IS NOT NULL ";
    $sql_query .=      "AND moving_date >= '".$monthStr."' ";
    $sql_query .=      "AND moving_date < '".$month2Str."'";
    $sql_query .=      "AND (valuation_status = 'match' ";
    $sql_query .=           "OR valuation_status = 'chosen') ";
    $sql_query .=      ") ";
    $sql_query .= "OR (valuation_date IS NOT NULL ";
    $sql_query .=     "AND valuation_date >= '".$monthStr."' ";
    $sql_query .=     "AND valuation_date < '".$month2Str."' ";
    $sql_query .=     "AND moving_date IS NULL ";
    $sql_query .=     "AND valuation_status = 'booking' ";
    $sql_query .=     "AND order_status = 'evaluating') ";
    $sql_query .= "OR (last_update >= '".$monthStr."' ";
    $sql_query .=     "AND last_update < '".$month2Str."' ";
    $sql_query .=     "AND valuation_status = 'self' ";
    $sql_query .=     "AND order_status = 'evaluating') ";
    $sql_query .= ") ";
    $sql_query .= "ORDER BY moving_date, valuation_date;";
    $result = query($sql_query);
    return $result;
  }

  function order_detail($order_id, $company_id){
    $sql_query = "SELECT * FROM (`member` NATURAL JOIN `orders`) ";
    $sql_query .= "NATURAL JOIN `choose` ";
    $sql_query .= "WHERE choose.company_id = ".$company_id." ";
    $sql_query .= "AND orders.order_id = '".$order_id."';";
    $result = query($sql_query);
    return $result;
  }

  function vehicle_detail($order_id){
    $sql_query = "SELECT vehicle_weight, vehicle_type, COUNT(*) AS num ";
    $sql_query .= "FROM `vehicle_assignment` NATURAL JOIN `vehicle` ";
    $sql_query .= "WHERE order_id = ".$order_id." ";
    $sql_query .= "GROUP BY vehicle_weight, vehicle_type;";
    $result = query($sql_query);
    return $result;
  }

  function vehicle_each_detail($order_id){
    $sql_query = "SELECT * FROM `vehicle_assignment` NATURAL JOIN `vehicle` ";
    $sql_query .= "WHERE order_id = ".$order_id.";";
    $result = query($sql_query);
    return $result;
  }

  function staff_detail($order_id){
    $sql_query = "SELECT * FROM `staff` NATURAL JOIN `staff_assignment` ";
    $sql_query .= "WHERE staff_assignment.order_id = ".$order_id.";";
    $result = query($sql_query);
    return $result;
  }

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

  function valuation_detail($order_id, $company_id){
    $sql_query = "SELECT * FROM `member` NATURAL JOIN ";
    $sql_query .= "(`orders` NATURAL JOIN `choose`) ";
    $sql_query .= "WHERE choose.company_id = ".$company_id." ";
    $sql_query .= "AND orders.order_id = ".$order_id."; ";
    $result = query($sql_query);
    return $result;
  }

  function all_staff_data($company_id){
    $sql_query = "SELECT * FROM `staff` ";
    $sql_query .= "WHERE company_id = ".$company_id."  ";
    $sql_query .= "AND end_time IS NULL;";
    $result = query($sql_query);
    return $result;
  }

  function vehicle_type_data($company_id){
    $sql_query = "SELECT vehicle_weight, vehicle_type FROM `vehicle` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    $sql_query .= "AND end_time IS NULL ";
    $sql_query .= "GROUP BY vehicle_weight, vehicle_type;";
    $result = query($sql_query);
    return $result;
  }

  function all_vehicle_data($company_id){
     $sql_query = "SELECT * FROM `vehicle` ";
     $sql_query .= "WHERE company_id = ".$company_id."  ";
     $sql_query .= "AND end_time IS NULL ;";
     $result = query($sql_query);
     return $result;
   }
  function all_vehicle_verified_data($company_id){
     $sql_query = "SELECT * FROM `vehicle` ";
     $sql_query .= "WHERE company_id = ".$company_id."  ";
	 $sql_query .= "AND verified = '1' ";
     $sql_query .= "AND end_time IS NULL ;";
     $result = query($sql_query);
     return $result;
   }

   function vehicle_demand_data($order_id){
     $sql_query = "SELECT * FROM `vehicle_demand` ";
     $sql_query .= "WHERE order_id = ".$order_id.";";
     $result = query($sql_query);
     return $result;
   }

   function company_detail($company_id){
     $sql_query = "SELECT * FROM `company` ";
     $sql_query.= "WHERE company_id = ".$company_id.";";
     $result = query($sql_query);
     return $result;
   }
   
   function company_fix_discount($company_id){
     $sql_query = "SELECT `fixDiscount`, `isEnable` FROM `company` ";
     $sql_query.= "WHERE company_id = ".$company_id.";";
     $result = query($sql_query);
     return $result;
   }

  function all_company_data(){
    $sql_query = "SELECT company_id, company_name FROM `company`;";
    $result = query($sql_query);
    return $result;
  }

  function all_valuation_date($company_id){
    $sql_query = "SELECT valuation_date AS date FROM `choose` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    $sql_query .= "AND moving_date IS NULL ";
    $sql_query .= "AND valuation_date IS NOT NULL ";
    $sql_query .= "AND valuation_date > '0001-01-01' ";
    $sql_query .= "GROUP BY valuation_date;";
    $result = query($sql_query);
    return $result;
  }

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

  function order_num(){
    $sql_query = "SELECT * ";
    $sql_query .= "FROM `orders`;";
    $result = query($sql_query);
    return $result;
  }

  function all_order_date($company_id){
    $sql_query = "SELECT moving_date AS date FROM `choose` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    $sql_query .= "AND moving_date IS NOT NULL ";
    $sql_query .= "AND moving_date > '0001-01-01' ";
    $sql_query .= "GROUP BY moving_date;";
    $result = query($sql_query);
    return $result;
  }

  function all_order_date_status($company_id, $order_status){
    $sql_query = "SELECT moving_date AS date, orders.order_id FROM `choose` NATURAL JOIN `orders` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    $sql_query .= "AND moving_date IS NOT NULL ";
    $sql_query .= "AND moving_date > '0001-01-01' ";
    $sql_query .= "AND order_status = '".$order_status."' ";
    $sql_query .= "GROUP BY moving_date;";
    $result = query($sql_query);
    return $result;
  }

  function all_member_data(){
    $sql_query = "SELECT * FROM `member`;";
    $result = query($sql_query);
    return $result;
  }

  function member_data($member_id){
    $sql_query = "SELECT * FROM `member` WHERE member_id = '".$member_id."';";
    $result = query($sql_query);
    return $result;
  }

  function order_finish_count($company_id){
    $sql_query = "SELECT COUNT(orders.order_id) AS finish_amount ";
    $sql_query .= "FROM `orders` NATURAL JOIN `choose` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    $sql_query .= "AND (order_status = 'done' ";
    $sql_query .= "     OR order_status ='paid');";
    $result = query($sql_query);
    return $result;
  }

  function order_paid_count($company_id){
    $sql_query = "SELECT COUNT(orders.order_id) AS paid_amount ";
    $sql_query .= "FROM `orders` NATURAL JOIN `choose` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    $sql_query .= "AND order_status = 'paid';";
    $result = query($sql_query);
    return $result;
  }

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

  function serviceItem_data($company_id){
    $sql_query = "SELECT * FROM `service_item` NATURAL JOIN `service_class` ";
    $sql_query.= "WHERE company_id = ".$company_id." ";
    $sql_query.= "AND ((isDelete = FALSE AND end_time IS NOT NULL)";
    $sql_query.= "     OR (end_time IS NULL));";
    $result = query($sql_query);
    return $result;
  }

  function free_discount_data($company_id){
    $sql_query = "SELECT * FROM `discount` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    $sql_query .= "AND update_time = ";
    $sql_query .= "    (SELECT MAX(update_time) FROM `discount` ";
    $sql_query .= "     WHERE company_id = ".$company_id.");";
    $result = query($sql_query);
    return $result;
  }

  function period_discount_data($company_id){
    $sql_query = "SELECT * FROM `period_discount` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    // $sql_query .= "AND isDelete = FALSE;";
    $result = query($sql_query);
    return $result;
  }

  function comment_data($company_id){
    $sql_query = "SELECT * FROM (`comments` NATURAL JOIN `orders`) NATURAL JOIN `member` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    $sql_query .= "ORDER BY comment_date DESC;";
    $result = query($sql_query);
    return $result;
  }

  function comment_detail($comment_id){
    $sql_query = "SELECT * FROM comments NATURAL JOIN orders NATURAL JOIN member ";
    $sql_query .= "WHERE comment_id = ".$comment_id.";";
    $result = query($sql_query);
    return $result;
  }

  function announcement_data($company_id){
    $sql_query = "SELECT * FROM `announcement` NATURAL JOIN `announcement_company` ";
    $sql_query .= "WHERE company_id = ".$company_id." ";
    $sql_query .= "ORDER BY announcement_date DESC;";
    $result = query($sql_query);
    return $result;
  }
  
  function announcement_company_data($company_id){
	$sql_query = "SELECT * FROM `announcement_company` ";
	$sql_query .= "WHERE company_id = ".$company_id." ";
	$result = query($sql_query);
	return $result;
  }
  
  function query($sql_query){
    require './connDB.php';
    $result = mysqli_query($db_link, $sql_query);
    if(!$result) $result = "Error: ".mysqli_error($db_link);
    mysqli_close($db_link);
    return $result;
  }


?>
