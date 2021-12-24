<?php
	header("Content-Type: text/html; charset=utf-8");
	require 'data_sql.php';

	$func = $_POST['function_name'];
	//echo 'func = '.$func.'<br>';
	if (!strcmp("order_member",$func)) {
		if(isset($_POST['startDate'])){
			$result[] = order_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], $_POST['status'], 'TRUE');
			$result[] = order_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], $_POST['status'], 'FALSE');
		}
		else{
			$today = gmdate("Y-m-d", time() + 3600*8);
			$result[] = order_member($_POST['company_id'], '2020-01-01', $today, $_POST['status'], 'TRUE');
			$result[] = order_member($_POST['company_id'], '2020-01-01', $today, $_POST['status'], 'FALSE');
		}
	}
	elseif (!strcmp("order_member_today",$func)) {
		$timezone = 8; //GMT+8
		$date = gmdate("Y-m-d", time() + 3600*($timezone));
		$result[] = order_member_today($_POST['company_id'], $date);
		$row_result = result_to_array($result);
		$row_result2 = orderBytime($row_result);
		return array_to_json($row_result2);
	}
	elseif (!strcmp("order_member_oneDay",$func)) {
		$date = $_POST['date'];
		$isOrder = FALSE;
		if(!strcmp("today",$date)){
			$timezone = 8; //GMT+8
			$date = gmdate("Y-m-d", time() + 3600*($timezone));
			$isOrder = TRUE;
		}
		if(isset($_POST['isOrder'])) $isOrder = TRUE;
		$result[] = order_member_oneDay($_POST['company_id'], $date, $isOrder);
		$row_result = result_to_array($result);
		$row_result2 = orderBytime($row_result);
		return array_to_json($row_result2);
	}
	elseif (!strcmp("order_member_oneMonth",$func)) {
		$year = $_POST['year'];
		$monthStr = getMonth1($year, $_POST['month']);
		$month2Str = getMonth2($year, $_POST['month']);

		$result[] = order_member_oneMonth($_POST['company_id'], $monthStr, $month2Str);
		if($result[0]->num_rows > 0){
			$row_result = result_to_array($result);
			$row_result2 = orderByDay($row_result);
			return array_to_json($row_result2);
		}
		else {
			// print_r($result);
			echo "no result";
			return "no result";
		}
	}
	elseif (!strcmp("order_detail",$func)) {
		$result[] = order_detail($_POST['order_id'], $_POST['company_id']);
		if(!isset($_POST['assign'])){
			$result[] = vehicle_detail($_POST['order_id']);
		}
		else {
			$result[] = vehicle_demand_data($_POST['order_id']);
			$result[] = vehicle_each_detail($_POST['order_id']);
		}
		$result[] = staff_detail($_POST['order_id']);
	}
	elseif (!strcmp("valuation_member",$func)) {
		$result[] = valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], $_POST['status'], 'TRUE');
		$result[] = valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], $_POST['status'], 'FALSE');
	}
	elseif (!strcmp("self_valuation_member",$func)) {
		$result[] = self_valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], 'TRUE', false);
		$result[] = self_valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], 'FALSE', false);
	}
	elseif (!strcmp("cancel_valuation_member",$func)) {
		$result[] = valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'],  "cancel", 'TRUE');
		$result[] = self_valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], 'TRUE', true);
		$result[] = valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'],  "cancel", 'FALSE');
		$result[] = self_valuation_member($_POST['company_id'], $_POST['startDate'], $_POST['endDate'], 'FALSE', true);
	}
	elseif (!strcmp("valuation_detail",$func)) {
		$result[] = valuation_detail($_POST['order_id'], $_POST['company_id']);
	}
	elseif(!strcmp("staff-vehicle_data", $func)){
		$result[] = all_staff_data($_POST['company_id']);
		$result[] = all_vehicle_data($_POST['company_id']);
	}
	elseif (!strcmp("staff_detail",$func)) {
		$result[] = staff_detail($_POST['order_id']);
	}
	elseif(!strcmp("all_staff_data", $func)){
		$result[] = all_staff_data($_POST['company_id']);
	}
	elseif (!strcmp("vehicle_detail",$func)) {
		$result[] = vehicle_detail($_POST['order_id']);
	}
	elseif (!strcmp("vehicle_each_detail",$func)) {
		$result[] = vehicle_each_detail($_POST['order_id']);
	}
	elseif(!strcmp("vehicle_type_data", $func)){
		$result[] = vehicle_type_data($_POST['company_id']);
	}
	elseif(!strcmp("all_vehicle_data", $func)){
		$result[] = all_vehicle_data($_POST['company_id']);
	}
	elseif(!strcmp("all_vehicle_staff_leave", $func)){
		$result[] = vehicle_maintain($_POST['company_id'], $_POST['date']);
		$result[] = staff_leave($_POST['company_id'], $_POST['date']);
	}
	elseif(!strcmp("vehicle_maintain", $func)){
		$result[] = vehicle_maintain($_POST['company_id'], $_POST['date']);
	}
	elseif(!strcmp("staff_leave", $func)){
		$result[] = staff_leave($_POST['company_id'], $_POST['date']);
	}
	elseif (!strcmp("vehicle_demand_data",$func)) {
		$result[] = vehicle_demand_data($_POST['order_id'], $_POST['company_id']);
	}
	elseif(!strcmp("overlap_order", $func)){
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
	}
	elseif(!strcmp("order_num", $func)){
	  $result[] = order_num();
	}
	elseif(!strcmp("company_detail", $func)){
		$result[] = company_detail($_POST['company_id']);
	}
	elseif(!strcmp("all_company_data", $func)){
		$result[] = all_company_data();
	}
	elseif(!strcmp("all_order_date", $func)){
		if(isset($_POST['order_status'])) $result[] = all_order_date_status($_POST['company_id'], $_POST['order_status']);
		else {
			$result[] = all_valuation_date($_POST['company_id']);
			$result[] = all_order_date($_POST['company_id']);
		}
	}
	elseif(!strcmp("all_member_data", $func)){
		$result[] = all_member_data();
	}
	elseif(!strcmp("member_data", $func)){
		$result[] = member_data($_POST['member_id']);
	}
	elseif(!strcmp("done_paid_data", $func)){
    $result[] = order_finish_count($_POST['company_id']);
    $result[] = order_paid_count($_POST['company_id']);
  }
	elseif (!strcmp("pay_oneMonth", $func)) {
		$year = $_POST['year'];
		$monthStr = getMonth1($year, $_POST['month']);
		$month2Str = getMonth2($year, $_POST['month']);

		$result[] = pay_oneMonth($_POST['company_id'], $monthStr, $month2Str);
		$row_result = result_to_array($result);
		if(isset($row_result)) $row_result2 = orderByDay($row_result);
		else $row_result2 = $_POST['month']." month no order";
		return array_to_json($row_result2);
	}
	elseif (!strcmp("pay_daily",$func)) {
		$year = $_POST['year'];
		$monthStr = getMonth1($year, $_POST['month']);
		$month2Str = getMonth2($year, $_POST['month']);

		$result[] = pay_daily($_POST['company_id'], $monthStr, $month2Str);
	}
	elseif (!strcmp("month_order_date",$func)) {
    $year = $_POST['year'];
    $monthStr = getMonth1($year, $_POST['month']);
    $month2Str = getMonth2($year, $_POST['month']);

    $result[] = month_order_date($_POST['company_id'], $monthStr, $month2Str);
  }
	elseif(!strcmp("serviceItem_data", $func)){
		$result[] = serviceItem_data($_POST['company_id']);
	}
	elseif(!strcmp("discount_data", $func)){
	  $result[] = free_discount_data($_POST['company_id']);
		$result[] = period_discount_data($_POST['company_id']);
	}
	elseif(!strcmp("free_discount_data", $func)){
	  $result[] = free_discount_data($_POST['company_id']);
	}
	elseif(!strcmp("period_discount_data", $func)){
		$result[] = period_discount_data($_POST['company_id']);
	}
	elseif(!strcmp("comment_data", $func)){
		$result[] = comment_data($_POST['company_id']);
	}
	elseif(!strcmp("comment_detail", $func)){
		$result[] = comment_detail($_POST['comment_id']);
	}
	elseif(!strcmp("announcement_data", $func)){
		$result[] = announcement_data($_POST['company_id']);
	}
	else{
		echo "function_name not found.";
		return;
	}

	$row_result = result_to_array($result);
	return array_to_json($row_result);

	function result_to_array($result){
		for($i = 0; $i < count($result); $i++)
				for($ii = 0; $ii < $result[$i]->num_rows; $ii++)
					$row_result[] = mysqli_fetch_assoc($result[$i]);
		if(!isset($row_result)) $row_result = mysqli_fetch_assoc($result[0]);
		return $row_result;
	}

	function array_to_json($row_result){
		$result_json = json_encode($row_result);
		echo $result_json;
		return $result_json;
	}

	function orderBytime($row_result){
		if(!isset($row_result)) return null;
		for ($c = 1; $c < sizeof($row_result); $c++) {
			$current = $row_result[$c];
			if(!isset($current['moving_date'])) continue;
			else $time = getMovingStartTime($current['moving_date']);

			for($i = $c-1; $i >= 0; $i--){
				$value = $row_result[$i];
				if(!isset($value['moving_date']))
					$time2 = getValuationStartTime($value['valuation_time']);
				else break;
				$time = getTime($row_result[$i+1]);
				if($time < $time2){
					$temp = $row_result[$i+1];
					$row_result[$i+1] = $row_result[$i];
					$row_result[$i] = $temp;
				}
				else break;
			}
		}
		return $row_result;
	}

	function getTime($value){
		if(!isset($value['moving_date']))
			$time = getValuationStartTime($value['valuation_time']);
		else $time = getMovingStartTime($value['moving_date']);
		return $time;
	}

	function getValuationStartTime($valuation_time){
		$vstime = explode('~', $valuation_time);
		return $vstime[0];
	}

	function getMovingStartTime($moving_date){
		$mtime = explode(' ', $moving_date);
		$mstime = explode(':', $mtime[1]);
		return $mstime[0].":".$mstime[1];
	}

	function orderByDay($row_result){
		for ($c = 1; $c < sizeof($row_result); $c++) {
			$current = $row_result[$c];

			for($i = $c-1; $i >= 0; $i--){
				$value = $row_result[$i];
				$day2 = getDay($value);
				$value2 = $row_result[$i+1];
				$day = getDay($value2);
				if($day < $day2){
					$temp = $row_result[$i+1];
					$row_result[$i+1] = $row_result[$i];
					$row_result[$i] = $temp;
				}
				else break;
			}
		}
		return $row_result;
	}

	function getDay($value){
		if(!isset($value['moving_date'])){
			if(isset($value['valuation_date']) && strcmp($value['valuation_status'], "self")) $day = $value['valuation_date'];
			else{
				$datetime = explode(' ', $value['last_update']);
				$day = $datetime[0];
			}
		}
		else{
			$datetime = explode(' ', $value['moving_date']);
			$day = $datetime[0];
		}
		return $day;
	}

	function getMonth1($year, $month){
		if($month < 10) $monthStr = "0".$month;
		else $monthStr = $month;
		$monthStr = $year."-".$monthStr."-01";

		return $monthStr;
	}

	function getMonth2($year, $month){
		$month2 = $month+1;
		if($month2 == 13){
			$month2 = 1;
			$year = $year+1;
		}
		if($month2 < 10) $month2Str = "0".$month2;
		else $month2Str = $month2;
		$month2Str = $year."-".$month2Str."-01";
		return $month2Str;
	}
?>
