<?php
	header("Content-Type: text/html; charset=utf-8");
	require 'functional_sql.php';

	$func = $_POST['function_name'];
	// echo 'func = '.$func.'<br>';
	if(!strcmp("login", $func)){
		$result = login($_POST['user_email'], $_POST['password']);
		if(!strcmp($result->status, "success")) {
		  $result2 = update_user_token($_POST['user_email']);
		  if(strcmp($result2, "success")) {
				echo "update token: ".$result2;
				return;
		  }
		}
		echo json_encode($result);
		return;
	}
	elseif(!strcmp("update_new", $func)){
		$result = update_new($_POST['order_id'], $_POST['company_id'], $_POST['new']);
	}
	elseif(!strcmp("update_selfValuation", $func)){
		$valuation_time = $_POST['valuation_time'];
		$vtimes = explode('~', $valuation_time);
		for($i = 0; $i < 2; $i++){
		  $vtime = explode(':', $vtimes[$i]);
		  $vtime[0] = str_pad($vtime[0], 2, '0', STR_PAD_LEFT);
		  $vtime[1] = str_pad($vtime[1], 2, '0', STR_PAD_LEFT);
		  $vtimes[$i] = implode(':', $vtime);
		}
		$valuation_time = implode('~', $vtimes);
		$result = update_selfValuation($_POST['order_id'], $_POST['company_id'], $_POST['valuation_date'], $valuation_time);
	}
	elseif(!strcmp("update_bookingValuation", $func)){
		$result = update_bookingValuation(
			$_POST['order_id'], $_POST['company_id'],
			$_POST['moving_date'], $_POST['estimate_worktime'], $_POST['fee']);
	}
	elseif(!strcmp("update_Valuation_Done", $func)){
		$result = update_Valuation_Done($_POST['order_id'], $_POST['company_id']);
	}
	elseif(!strcmp("add_vehicleDemands", $func)){
	  $result = add_vehicleDemands($_POST['order_id'], $_POST['company_id'], $_POST['vehicleItems']);
	}
	elseif(!strcmp("update_todayOrder", $func)){
		$result = update_todayOrder($_POST['order_id'], $_POST['company_id'], $_POST['memo'], $_POST['accurate_fee']);
	}
	elseif(!strcmp("modify_staff_vehicle", $func)){
  	$result = modify_staff_vehicle($_POST['company_id'], $_POST['order_id'], $_POST['vehicle_assign'], $_POST['staff_assign'], $_POST['staff_transform'], $_POST['vehicle_transform']);
	}
	elseif(!strcmp("add_valuation", $func)){
		$result = add_valuation($_POST['company_id'], $_POST['member_name'], $_POST['gender'], $_POST['contact_address'],$_POST['contact_time'], $_POST['phone'],
		                        $_POST['additional'], $_POST['outcity'], $_POST['outdistrict'], $_POST['address1'],
								$_POST['incity'], $_POST['indistrict'], $_POST['address2'], 
								$_POST['valuation_date'], $_POST['valuation_time']);
	}
	elseif(!strcmp("add_order", $func)){
		$result = add_order($_POST['company_id'], $_POST['member_name'], 
							$_POST['gender'], $_POST['phone'], $_POST['additional'], $_POST['contact_address'],
							$_POST['outcity'], $_POST['outdistrict'], $_POST['address1'], 
							$_POST['incity'], $_POST['indistrict'], $_POST['address2'], 
							$_POST['moving_date'], $_POST['estimate_fee'], $_POST['worktime'], $_POST['furniture_data']);
	}
	elseif(!strcmp("add_staff", $func)){
		$result = add_staff($_POST['staff_name'], $_POST['company_id']);
	}
	elseif(!strcmp("add_vehicle", $func)){
		$result = add_vehicle($_POST['plate_num'], $_POST['vehicle_weight'], $_POST['vehicle_type'], $_POST['company_id']);
	}
	elseif(!strcmp("delete_staff_vehicle", $func)){
		$result = delete_staff_vehicle($_POST['table'], $_POST['id']);
	}
	elseif(!strcmp("modify_staff_vehicle_leave", $func)) {
    $result = modify_staff_vehicle_leave($_POST['staffItems'], $_POST['vehicleItems'], $_POST['date']);
  }
	elseif(!strcmp("distribute_pay", $func)) {
		$result = distribute_pay($_POST['order_id'], $_POST['staff_id'], $_POST['pay']);
	}
	elseif(!strcmp("update_companyDistribute", $func)) {
		$result = update_companyDistribute($_POST['company_id'], $_POST['last_distribution']);
	}
	elseif(!strcmp("update_company", $func)) {
		$result = update_company($_POST['company_id'], $_POST['address'], $_POST['phone'], $_POST['staff_num'], $_POST['url'], $_POST['email'], $_POST['line_id'],$_POST['philosophy']);
	}
	elseif(!strcmp("modify_serviceItems", $func)) {
		$result = modify_serviceItems($_POST['company_id'], $_POST['enableItems'], $_POST['disableItems'], $_POST['deleteItems']);
	}
	elseif(!strcmp("modify_discount", $func)){
	  $result[] = modify_discount($_POST['company_id'], $_POST['valuate'], $_POST['deposit'], $_POST['cancel'], $_POST['periodItems'], $_POST['deleteItems']);
	}
	elseif(!strcmp("modify_fix_discount", $func)){
	  $result[] = modify_fix_discount($_POST['company_id'], $_POST['fixDiscount'], $_POST['isEnable']);
	}
	elseif(!strcmp("modify_contact_address", $func)){
	  $result[] = modify_contact_address($_POST['member_id']);
	}
	elseif(!strcmp("update_reply", $func)){
		$result = update_reply($_POST['comment_id'], $_POST['reply']);
	}
	elseif(!strcmp("update_announcement_new", $func)){
		$result = update_announcement_new($_POST['announcement_id'], $_POST['company_id']);
	}
	elseif(!strcmp("change_status", $func)){
		$result = change_status($_POST['company_id'], $_POST['table'], $_POST['order_id'], $_POST['status']);
	}
	elseif(!strcmp("today_order", $func)){
		$result = today_order($_POST['order_id'], $_POST['company_id']);
	}
	elseif(!strcmp("become_order", $func)){
		$result = become_order($_POST['company_id'], $_POST['order_id']);
	}
	elseif(!strcmp("update_vehicleLicense", $func)){
		$result = update_vehicleLicense($_POST['company_id'], $_POST['plate_num'], $_POST['license'], $_POST['verified']);
	}
	elseif(!strcmp("update_sugCars", $func)){
		$result = update_sugCars($_POST['company_id'], $_POST['order_id'], $_POST['sugCars']);
	}
	elseif(!strcmp("get_payment_result", $func)){
		$result = get_payment_result($_POST['order_id']);
	}
	else{
		echo "function_name not found.";
		return;
	}

  //echo $result;
	print_r($result);
	return $result;
?>
