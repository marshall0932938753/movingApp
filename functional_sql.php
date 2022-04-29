<?php

function updatePassword($email, $password_hash){
	$sql_query = "SELECT * FROM `user` ";
	$sql_query .= "WHERE user_email = '".$email."';";
	$result = query($sql_query);

	$return_result = new stdClass();

	if($result == null || $result->num_rows < 1) {
    $return_result->status = "failed";
    $return_result->message = "the user doesn't exist.";
	echo json_encode($return_result);
    return $return_result;
  }else{
	$sql_query = "UPDATE `user` SET ";
	$sql_query .= "user_password = '".$password_hash."' ";
	$sql_query .= "WHERE user_email = '".$email."';";
	$result = query($sql_query);

	$return_result->status = "success";
    $return_result->message = "password updated.";
	echo json_encode($return_result);
	return json_encode($return_result);
  }

}
function add_account($company_id, $account, $password, $title, $phone){
	$password_sha = hash("SHA256", $password);
	$password_hash = password_hash($password_sha, PASSWORD_BCRYPT);
	$random = rand(1,100);
	if(!strcmp($title, "admin")){
		$name = "管".$random;
	}else{
		$name = "工".$random;
	}
	$sql_query = "INSERT INTO `user` ( `company_id`, `user_name`, `user_email`, `user_phone`, `user_password`, `title` ) VALUES ";
	$sql_query .= "('".$company_id."','".$name."', '".$account."', '".$phone."', '".$password_hash."', '".$title."');";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$return -> status = "success";
		$return -> message = "add account success";
		return json_encode($return);
	}
	else{
		$return -> status = "failed";
		$return -> message = "add account failed";
		return json_encode($return);
	}
}
function updateVerifySMS($number){
	$sql_query = "UPDATE `user` SET `verify_code` = ".$number ;
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$return -> status = "success";
		$return -> message = "updateVerifySMS success";
		return json_encode($return);
	}
	else{
		$return -> status = "failed";
		$return -> message = "updateVerifySMS failed";
		return json_encode($return);
	}
}

function login($user_email, $password){
	$sql_query = "SELECT * FROM `user` ";
	$sql_query .= "WHERE user_email = '".$user_email."';";
	$result = query($sql_query);
  $return_result = new stdClass();
	if($result == null || $result->num_rows < 1) {
    $return_result->status = "failed";
    $return_result->message = "the user doesn't exist.";
    return $return_result;
  }
	else{
		$row_result[] = mysqli_fetch_assoc($result);
		$password_hash = $row_result[0]["user_password"];
	}

	$password_sha = hash("SHA256", $password);
	if(password_verify($password_sha, $password_hash)) {
		$return_result->status = "success";
		$return_result->user = $row_result[0];
		return $return_result;
	}
	else {
    $return_result->status = "failed";
    $return_result->message = "password incorrected";
    return $return_result;
  }
}

function update_user_token($user_email){
	$token = hash("SHA256", uniqid("", true).sprintf("%02d", rand(0, 99)));
	$sql_query = "UPDATE `user` SET ";
	$sql_query .= "token = '".$token."' ";
	$sql_query .= "WHERE user_email = '".$user_email."';";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$return -> status = "success";
		$return -> message = "update_user_token success";
		return json_encode($return);
	}
	else{
		$return -> status = "failed";
		$return -> message = "update_user_token failed";
		return json_encode($return);
	}
}

function update_new($order_id, $company_id, $new){
	$sql_query = "UPDATE `choose` ";
	$sql_query .= "SET new = '".$new."' ";
	$sql_query .= "WHERE order_id = ".$order_id." ";
	$sql_query .= "AND company_id = ".$company_id." ; " ;
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$return -> status = "update_new success";
		return json_encode($return);
	}
	else{
		$return -> status = "update_new failed";
		//$return -> message = "update_new failed";
		return json_encode($return);
	}
}

function update_selfValuation($order_id, $company_id, $valuation_date, $valuation_time, $plan){
	$planned = $plan;
	$sql_query = "UPDATE `choose` SET ";
	$sql_query .= "valuation_date = '".$valuation_date."', ";
	$sql_query .= "valuation_time = '".$valuation_time."' ";
	$sql_query .= "WHERE company_id = ".$company_id." ";
	$sql_query .= "AND order_id = ".$order_id." AND plan = '".$plan."' ;";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		update_confirm($order_id, $company_id);
		$return -> status = "success";
		$return -> message ="update_selfValuation booking: ".change_status($company_id, "choose", $order_id, "booking", $planned);
		return json_encode($return);
	}
	else {
		$return -> status = "failed";
		$return -> message ="update_selfValuation failed";
		return json_encode($return);
	}
}

function update_bookingValuation($order_id, $company_id, $moving_date, $estimate_worktime, $fee, $plan){
	$planned = $plan;
	$sql_query = "UPDATE `choose` SET ";
	$sql_query .= "moving_date = '".$moving_date."', ";
	$sql_query .= "estimate_worktime = '".$estimate_worktime."', ";
	$sql_query .= "estimate_fee = '".$fee."', ";
	$sql_query .= "accurate_fee = '".$fee."' ";
	$sql_query .= "WHERE company_id = ".$company_id." ";
	$sql_query .= "AND order_id = ".$order_id." AND plan = '".$plan."' ;";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$return -> status = "success";
		$return -> message = "update_bookingValuation to match".change_status($company_id, "choose", $order_id, "match", $planned);
		return json_encode($return);
	}
	else {
		$return -> status = "failed";
		$return -> message = "update_bookingValuation failed";
		return json_encode($return);
	}
}
function update_Valuation_Done($order_id, $company_id, $plan){
	$sql_query = "UPDATE `choose` SET ";
	$sql_query .= "val_done = '已完成' " ;
	$sql_query .= "WHERE order_id =".$order_id." AND company_id = ".$company_id." AND plan = '".$plan."' ;";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$return -> status = "success";
		$return -> message = "valuation done";
		return json_encode($return);
	}
	else {
		$return -> status = "failed";
		$return -> message = "valuation not done";
		return json_encode($return);
	}
}

function add_vehicleDemands($order_id, $company_id, $vehicleItems){
  $ja = (array)json_decode($vehicleItems, true);
	if(!strcmp($vehicleItems, "[]")) return "success";
  foreach ($ja as $key => $value) {
    $check[$key] = add_vehicleDemand($order_id, $company_id, $value[0], $value[1], $value[2]);
  }
  $return = new stdClass();
  if(count(array_unique($check))===1 && end($check)==="success"){
  	$return -> status = "success";
  	$return -> message = "add_vehicleDemands success";
  	return json_encode($return);
  }
  else{
  	$return -> status = "failed";
  	$return -> message = "add_vehicleDemands failed: ".$check;
  	return json_encode($return);
  }
}

function add_vehicleDemand($order_id, $company_id, $weight, $type, $num){
  $sql_query = "INSERT INTO `vehicle_demand` (`order_id`, `company_id`, `vehicle_weight`, `vehicle_type`, `num`) VALUES ";
  $sql_query .= "('".$order_id."', '".$company_id."', '".$weight."', '".$type."', '".$num."') ON DUPLICATE KEY UPDATE num =".$num."; ";
  $result = query($sql_query);
  $return = new stdClass();
  if(!strcmp($result, "1")) return "success";
	else if(preg_match("/PRIMARY/", $result)) return update_vehicleDemand($order_id, $company_id, $weight, $type, $num);
  else return $result;
}

function update_vehicleDemand($order_id, $company_id, $weight, $type, $num) {
	$sql_query = "UPDATE `vehicle_demand` SET num = '".$num."' ";
	$sql_query .= "WHERE order_id = '".$order_id."' ";
	$sql_query .= "AND company_id = '".$company_id."' ";
	$sql_query .= "AND vehicle_weight = '".$weight."' ";
	$sql_query .= "AND vehicle_type = '".$type."';";
	$result = query($sql_query);
	$return = new stdClass();

	if(!strcmp($result, "1")){
		$return -> status = "success";
		$return -> message = "update_vehicleDemand success";
  	return json_encode($return);
	}
	else{
		$return -> status = "failed";
		$return -> message = "update_vehicleDemand failed";
  	return json_encode($return);
	}
}
function update_vehicleLicense($company_id, $plate_num, $license, $verified){
	$sql_query = "UPDATE `vehicle` SET license = '".$license."', ";
	$sql_query .= "verified = '".$verified."' ";
	$sql_query .= "WHERE company_id = '".$company_id."' ";
	$sql_query .= "AND plate_num = '".$plate_num."' ";
	$result = query($sql_query);
	$return = new stdClass();

	if(!strcmp($result, "1")){
		$return -> status = "success";
		$return -> message = "update_vehicleLicense success";
  	return json_encode($return);
	}
	else{
		$return -> status = "failed";
		$return -> message = "update_vehicleLicense failed";
  	return json_encode($return);
	}
}
function update_sugCars($company_id, $order_id, $sugCars){
	$sql_query = "UPDATE `orders` SET SugCars = '".$sugCars."' ";
	$sql_query .= "WHERE order_id = ".$order_id.";";
	$result = query($sql_query);
	$return = new stdClass();

	if(!strcmp($result, "1")){
		$return -> status = "success";
		$return -> message = "update_sugCars success";
  	return json_encode($return);
	}
	else{
		$return -> status = "failed";
		$return -> message = "update_sugCars failed";
  	return json_encode($return);
	}
}

function update_todayOrder($order_id, $company_id, $memo, $accurate_fee){
	$sql_query = "UPDATE `orders` SET ";
	$sql_query .= "memo = '".$memo."' ";
	$sql_query .= "WHERE order_id = ".$order_id.";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$sql_query = "UPDATE `choose` SET ";
		$sql_query .= "accurate_fee = ".$accurate_fee." ";
		$sql_query .= "WHERE company_id = ".$company_id." ";
		$sql_query .= "AND order_id = ".$order_id." ;";
		$result = query($sql_query);
		if(!strcmp($result, "1")){
			$return -> status = "success";
  		$return -> message = "update_todayOrder success";
  		return json_encode($return);
		}
		else{
			$return -> status = "update accurate_fee";
  		$return -> message = "accurate_fee: ".$result;;
  		return json_encode($return);
		}
	}
	else{
			$return -> status = "failed";
  		$return -> message = "memo error: ".$result;
  		return json_encode($return);
	}
}

function modify_staff_vehicle($company_id, $order_id, $vehicle_assign, $staff_assign, $staff_transform, $vehicle_transform){
  $check[] = modify_vehicleAssignment($order_id, $vehicle_assign);
  $check[] = modify_staffAssignment($order_id, $staff_assign);
  $check[] = transform_orders($order_id, $staff_transform, "staff");
  $check[] = transform_orders($order_id, $vehicle_transform, "vehicle");
  $return = new stdClass();
  if(count(array_unique($check))===1 && end($check)==="success"){
		if(!strcmp($vehicle_assign, "[]") || !strcmp($staff_assign, "[]")){
			$return -> status = "success";
			$return -> message = "modify_staff_vehicle success";
			return json_encode($return);
		}
		return change_stat($company_id, "orders", $order_id, "assigned");
	}
  else return $check;
}

function modify_vehicleAssignment($order_id, $vehicle_assign){
	$ja = json_decode($vehicle_assign, true);
	$result_d = delete_vehicleAssignment($order_id, $vehicle_assign); //先刪掉沒被分派到的車子
	if(!strcmp($vehicle_assign, "[]")) $check[] = "success";
	else{
		foreach ($ja as $count => $vehicle_id) {
			$result = add_vehicleAssignment($order_id, $vehicle_id);
			if(!strcmp($result, "success") || preg_match("/PRIMARY/", $result))
				$check[$vehicle_id]="success";
			else
				$check[$vehicle_id]=$result;
		}
	}
	if(count(array_unique($check))===1 && end($check)==="success"){
			if(!strcmp($result_d, "success"))
				return "success";
			else return "delete error: ".$result_d;
	}
	else{
		if(!strcmp($result_d, "success"))
			return $check;
	 	else return "delete error: ".$result_d;
	}
}

function delete_vehicleAssignment($order_id, $vehicle_assign){
	$ja = json_decode($vehicle_assign, true);
	$sql_query = "DELETE FROM `vehicle_assignment` ";
	$sql_query .= "WHERE order_id = ".$order_id." ";
	foreach ($ja as $key => $vehicle_id)
		$sql_query .= "AND vehicle_id <> ".$vehicle_id." "; //不等於
	$sql_query .= ";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function add_vehicleAssignment($order_id, $vehicle_id){

	$sql_query = "INSERT INTO `vehicle_assignment`(`order_id`, `vehicle_id`) VALUES ";
	$sql_query .= "(".$order_id.", ".$vehicle_id.") ON DUPLICATE KEY UPDATE `vehicle_id` = ".$vehicle_id." ;";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function modify_staffAssignment($order_id, $staff_assign){
	$ja = json_decode($staff_assign, true);
	$result_d = delete_staffAssignment($order_id, $staff_assign); //先刪掉沒被分派到的員工
	if(!strcmp($staff_assign, "[]")) $check[] = "success";
	else{
		foreach ($ja as $count => $staff_id) {
			$result = add_staffAssignment($order_id, $staff_id);
			if(!strcmp($result, "success") || preg_match("/PRIMARY/", $result))
				$check[$staff_id]="success";
			else
				$check[$staff_id]=$result;
		}
	}
	if(count(array_unique($check))===1 && end($check)==="success"){
			if(!strcmp($result_d, "success"))
				return "success";
			else return "delete error: ".$result_d;
	}
	else{
		if(!strcmp($result_d, "success"))
			return $check;
	 	else return "delete error: ".$result_d;
	}
}

function delete_staffAssignment($order_id, $staff_assign){
	$ja = json_decode($staff_assign, true);
	$sql_query = "DELETE FROM `staff_assignment` ";
	$sql_query .= "WHERE order_id = ".$order_id." ";
	foreach ($ja as $key => $staff_id)
		$sql_query .= "AND staff_id <> ".$staff_id." "; //不等於
	$sql_query .= ";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function add_staffAssignment($order_id, $staff_id){;
	$sql_query = "INSERT INTO `staff_assignment`(`order_id`, `staff_id`) VALUES ";
	$sql_query .= "(".$order_id.", ".$staff_id.") ON DUPLICATE KEY UPDATE `staff_id` = ".$staff_id.";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function transform_orders($order_id, $transformItem, $kind){
	if(!strcmp($transformItem, "[]")) $check[] = "success";
  $ja = json_decode($transformItem, true);
  foreach ($ja as $key => $value) {
    $result = transform_order($order_id, $value[0], $value[1], $kind);
    if(!strcmp($result, "success"))
      $check[$key]="success";
    else
      $check[$key]= $result;
  }
  if(count(array_unique($check))===1 && end($check)==="success") return "success";
  else return $check;
}

function transform_order($order_id, $ori_order_id, $id, $kind){
  $sql_query = "UPDATE ".$kind."_assignment SET ";
  $sql_query .= "order_id = ".$order_id." ";
  $sql_query .= "WHERE order_id = ".$ori_order_id." ";
  $sql_query .= "AND ".$kind."_id = ".$id.";";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function add_valuation($company_id, $member_name, $gender, $contact_address, $contact_time, $phone, $additional,
						$outcity, $outdistrict, $address1,
						$incity, $indistrict, $address2, $valuation_date, $valuation_time){
	$member_id = get_memberId($member_name);
	if(!$member_id){
		$check[] = add_member($member_name, $gender, $contact_address, $contact_time, $phone);
		$member_id = get_memberId($member_name);
	}
	else $check[] = update_member($member_id, $gender, $contact_address, $contact_time, $phone);

	$sql_query = "INSERT INTO `orders` (`member_id`, `additional`, `outcity`, `outdistrict`, `address1`, ";
	$sql_query.= "`incity`, `indistrict`, `address2`, `order_status`, `auto`) VALUES ";
	$sql_query.= "(".$member_id.", '".$additional."', '".$outcity."', '".$outdistrict."', '".$address1."', ";
	$sql_query.= " '".$incity."', '".$indistrict."', '".$address2."', 'evaluating', FALSE);";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$order_id = get_orderID();
		$sql_query = "INSERT INTO `choose` (`order_id`, `company_id`, `valuation_date`, `valuation_time`, `valuation_status`) VALUES ";
		$sql_query .= "(".$order_id.", ".$company_id.", '".$valuation_date."', '".$valuation_time."', 'booking');";
		$result2 = query($sql_query);
		if(!strcmp($result, "1")) $check[] = "success";
		else $check[] = "add_choose: ".$result2;
	}
	else $check[] = "add_order: ".$result;
	if(count(array_unique($check))===1 && end($check)==="success"){
			$return -> status = "success";
  		$return -> message = "add_valuation success";
  		return json_encode($return);
	}

		$return -> status = "failed";
  	$return -> message = "add_valuation failed: ".$check;
  	return json_encode($return);
}

function add_order($company_id, $member_name, $gender, $phone, $additional, $contact_address, $outcity, $outdistrict, $address1, $incity, $indistrict, $address2, $moving_date, $estimate_fee, $worktime, $furniture_data){
	$member_id = get_memberId($member_name);
	if(!$member_id){
		$check[] = add_member_order($member_name, $gender, $contact_address, $phone);
		$member_id = get_memberId($member_name);
	}
	else $check[] = update_member_order($member_id, $gender, $contact_address, $phone);

	$sql_query = "INSERT INTO `orders` (`member_id`, `additional`, `outcity`,`outdistrict`,`address1`,`incity`,`indistrict`,`address2`,`order_status`, `auto`) VALUES ";
	$sql_query .= "(".$member_id.", '".$additional."', '".$outcity."','".$outdistrict."','".$address1."','".$incity."','".$indistrict."','".$address2."', 'scheduled', FALSE);";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$order_id = get_orderID();
		$sql_query = "INSERT INTO `choose` (`order_id`, `company_id`, `moving_date`, `estimate_fee`, `estimate_worktime`, `valuation_status`) VALUES ";
		$sql_query .= "(".$order_id.", ".$company_id.", '".$moving_date."', ".$estimate_fee.", ".$worktime.", 'chosen');";
		$result2 = query($sql_query);
		if(!strcmp($result, "1")) $check[] = "success";
		else $check[] = "add_choose: ".$result2;
	}
	else $check[] = "add_order: ".$result;
	if(count(array_unique($check))===1 && end($check)==="success") {
		$result3 = modify_furniture($order_id, $company_id, $furniture_data);
		if(!strcmp($result3, "1")) return "success";
		else{
			$return -> status = "success";
			$return -> message = "add_order ".$result3;
			return json_encode($return);
		}
	}
	$return -> status = "failed";
  	$return -> message = "add_order failed: ".$check;
  	return json_encode($return);
}

function modify_furniture($order_id, $company_id, $furnitureItems){
	$ja = json_decode($furnitureItems, true);
	foreach ($ja as $key => $furniture_item) {
		$furniture_id = $furniture_item[0];
		$num = $furniture_item[1];
		$check[$key] = add_furniture($order_id, $company_id, $furniture_id, $num);
	}
	$return = new stdClass();
	if(count(array_unique($check))===1 && end($check)==="success"){
		$return -> status = "success";
		$return -> message = "modify_furniture ";
		return json_encode($return);
	}
	else{
		$return -> status = "failed";
		$return -> message = "modify_furniture ";
		return json_encode($return);
	}
}

function add_furniture($order_id, $company_id, $furniture_id, $num){
	$sql_query = "INSERT INTO `furniture_list` (`order_id`, `company_id`, `furniture_id`, `num`) VALUES ";
	$sql_query .= "(".$order_id.", ".$company_id.", ".$furniture_id.", ".$num.");";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")) return "success";
	else return "add_error: ".$result;
}

function get_memberId($member_name){
	$sql_query = "SELECT member_id FROM `member` ";
	$sql_query .= "WHERE member_name = '".$member_name."';";
	$result = query($sql_query);
	$row_result = mysqli_fetch_assoc($result);
	if(isset($row_result['member_id'])) return $row_result['member_id'];
	else return 0;
}

function add_member($member_name, $gender, $contact_address, $contact_time, $phone){
	$sql_query = "INSERT INTO `member` (`member_name`, `gender`, `contact_address`, `contact_time`, `phone`) VALUES ";
	$sql_query .= "('".$member_name."', '".$gender."', '".$contact_address."', '".$contact_time."', '".$phone."');";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function update_member($member_id, $gender, $contact_address, $contact_time, $phone){
	$sql_query = "UPDATE `member` SET ";
	$sql_query .= "gender = '".$gender."', ";
	$sql_query .= "contact_address = '".$contact_address."', ";
	$sql_query .= "contact_time = '".$contact_time."', ";
	$sql_query .= "phone = '".$phone."' ";
	$sql_query .= "WHERE member_id = ".$member_id.";";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}
function add_member_order($member_name, $gender, $contact_address, $phone){
	$sql_query = "INSERT INTO `member` (`member_name`, `gender`, `contact_address`, `phone`) VALUES ";
	$sql_query .= "('".$member_name."', '".$gender."', '".$contact_address."', '".$phone."');";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function update_member_order($member_id, $gender, $contact_address, $phone){
	$sql_query = "UPDATE `member` SET ";
	$sql_query .= "gender = '".$gender."', ";
	$sql_query .= "contact_address = '".$contact_address."', ";
	$sql_query .= "phone = '".$phone."' ";
	$sql_query .= "WHERE member_id = ".$member_id.";";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function add_staff($staff_name, $company_id){
	 $sql_query = "INSERT INTO `staff`(`staff_name`, `company_id`) VALUES ";
	 $sql_query .= "( '".$staff_name."', ".$company_id.");";
	 $result = query($sql_query);
	 $return = new stdClass();
	 if(!strcmp($result, "1")){
	 		$return -> status = "success";
  		$return -> message = "add_staff success";
  		return json_encode($return);
	 }
	 else{
	 		$return -> status = "failed";
  		$return -> message = "add_staff failed";
  		return json_encode($return);
	 }
 }

function add_vehicle($plate_num, $vehicle_weight, $vehicle_type, $company_id){
 $sql_query = "INSERT INTO `vehicle`(`plate_num`, `vehicle_weight`, `vehicle_type`, `company_id`) VALUES ";
 $sql_query .= "( '".$plate_num."', '".$vehicle_weight."', '".$vehicle_type."', '".$company_id."'); ";
 $result = query($sql_query);
 $return = new stdClass();
 if(!strcmp($result, "1")){
	 	$return -> status = "success";
  		$return -> message = "add_vehicle success";
  		return json_encode($return);
	 }
	 else{
	 	$return -> status = "failed";
  		$return -> message = "add_vehicle failed";
  		return json_encode($return);
	 }
}

function delete_staff_vehicle($table, $id){
  if(!strcmp("staff", $table)){
	$sql_query = "UPDATE ".$table." SET ";
	$sql_query .= "end_time = current_timestamp() ";
	$sql_query .= "WHERE ".$table."_id = ".$id.";";
  }else{
	$sql_query = "UPDATE ".$table." SET ";
	$sql_query .= "end_time = current_timestamp(), verified = '3' ";
	$sql_query .= "WHERE ".$table."_id = ".$id.";";
  }

  $result = query($sql_query);
  $return = new stdClass();
  if(!strcmp($result, "1")){
	 	$return -> status = "success";
  		$return -> message = "delete_staff/vehicle success";
  		return json_encode($return);
	 }
	 else{
	 		$return -> status = "failed";
  		$return -> message = "delete_staff/vehicle failed";
  		return json_encode($return);
	 }
}

function modify_staff_vehicle_leave($staffItems, $vehicleItems, $date){
  $check[] = modify_staff_leave($staffItems, $date);
  $check[] = modify_vehicle_leave($vehicleItems, $date);
  $return = new stdClass();

  if(count(array_unique($check))===1 && end($check)==="success"){
  		$return -> status = "failed";
  		$return -> message = "modify_staff_vehicle_leave success";
  		return json_encode($return);
  }
  else{
  		$return -> status = "success";
  		$return -> message = "modify_staff_vehicle_leave success ";
  		return json_encode($return);
  }
}

function modify_staff_leave($staffItems, $date){
	 $ja = json_decode($staffItems, true);
	 $result_d = delete_staff_leave($staffItems, $date);
	 if(!strcmp($staffItems, "[]")) $check[] = "success";
	 else{
		 foreach ($ja as $count => $staff_id) {
			 $result = add_staff_leave($staff_id, $date);
			 if(!strcmp($result, "success") || preg_match("/PRIMARY/", $result))
				 $check[$staff_id]="success";
			 else
				 $check[$staff_id]="add staff error:".$result;
		 }
	 }
	 if(count(array_unique($check))===1 && end($check)==="success"){
		 if(!strcmp($result_d, "success"))
			 return "success";
		 else return "staff delete error: ".$result_d;
	 }
	 else{
		 if(!strcmp($result_d, "success"))
			 return $check;
		 else return "staff delete error: ".$result_d;
	 }
 }

 function delete_staff_leave($staffItems, $date){
   $ja = json_decode($staffItems, true);
   $sql_query = "DELETE FROM `staff_leave` ";
   $sql_query .= "WHERE leave_date = '".$date."' ";
   foreach ($ja as $key => $staff_id)
     $sql_query .= "AND staff_id <> ".$staff_id." ";
   $sql_query .= ";";
   $result = query($sql_query);
   if(!strcmp($result, "1")) return "success";
   else return $result;
 }

function add_staff_leave($staff_id, $date){
	$sql_query = "INSERT INTO `staff_leave` (`staff_id`, `leave_date`) VALUES ";
	$sql_query .= "(".$staff_id.", '".$date."') ON DUPLICATE KEY UPDATE staff_id =".$staff_id.", leave_date = '".$date."' ;";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function modify_vehicle_leave($vehicleItems, $date){
  $ja = json_decode($vehicleItems, true);
  $result_d = delete_vehicle_maintain($vehicleItems, $date);
  $return = new stdClass();
	if(!strcmp($vehicleItems, "[]")) $check[] = "success";
	else{
	  foreach ($ja as $count => $vehicle_id) {
	    $result = add_vehicle_maintain($vehicle_id, $date);
	    if(!strcmp($result, "success") || preg_match("/PRIMARY/", $result))
	      $check[$vehicle_id]="success";
	    else
	      $check[$vehicle_id]="add car error:".$result;
	  }
	}
  if(count(array_unique($check))===1 && end($check)==="success"){
    if(!strcmp($result_d, "success")){
      $return -> status = "success";
  		$return -> message = "modify_vehicle_leave success";
  		return json_encode($return);
    }
    else{
    	$return -> status = "failed";
  		$return -> message = "vehicle delete error: ".$result_d;
  		return json_encode($return);
    }
  }
  else{
    if(!strcmp($result_d, "success")){
    	$return -> status = "success";
  		$return -> message = "modify_vehicle_leave: ".$check;
  		return json_encode($return);
    }
    else{
    	$return -> status = "failed";
  		$return -> message = "modify_vehicle_leave: ".$result_d;
  		return json_encode($return);
    }
  }
}

function delete_vehicle_maintain($vehicleItema, $date){
  $ja = json_decode($vehicleItema, true);
  $sql_query = "DELETE FROM `vehicle_maintain` ";
  $sql_query .= "WHERE maintain_date = '".$date."' ";
  foreach ($ja as $key => $vehicle_id)
    $sql_query .= "AND vehicle_id <> ".$vehicle_id." ";
  $sql_query .= ";";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function add_vehicle_maintain($vehicle_id, $date){
  $sql_query = "INSERT INTO `vehicle_maintain` (`vehicle_id`, `maintain_date`) VALUES ";
  $sql_query .= "(".$vehicle_id.", '".$date."') ON DUPLICATE KEY UPDATE vehicle_id =".$vehicle_id.", maintain_date = '".$date."' ;";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function distribute_pay($order_id, $staff_id, $pay){
	$sql_query = "UPDATE `staff_assignment` SET ";
	$sql_query .= "pay = ".$pay." ";
	$sql_query .= "WHERE order_id = ".$order_id." ";
	$sql_query .= "AND staff_id = ".$staff_id.";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
	 $return -> status = "success";
	 $return -> message = "distribute_pay success";
	 return json_encode($return);
	}
	else{
	 $return -> status = "failed";
	 $return -> message = "distribute_pay failed";
	 return json_encode($return);
	}
}

function update_companyDistribute($company_id, $last_distribution){
	$sql_query = "UPDATE `company` SET last_distribution = ".$last_distribution." ";
	$sql_query .= "WHERE company_id = ".$company_id.";";
	$result = query($sql_query);
	$return = new stdClass();
  if(!strcmp($result, "1"))
  {
  	$return -> status = "success";
	 	$return -> message = "update_companyDistribute success";
	 	return json_encode($return);
  }
  else{
  	$return -> status = "failed";
	 	$return -> message = "update_companyDistribute failed";
	 	return json_encode($return);
  }
}


function update_company($company_id, $address, $phone, $staff_num, $url, $email, $line_id, $philosophy){
	$sql_query = "UPDATE `company` SET ";
	$sql_query .= "address = '".$address."', ";
	$sql_query .= "phone = '".$phone."', ";
	$sql_query .= "staff_num = ".$staff_num.", ";
	$sql_query .= "url = '".$url."', ";
	$sql_query .= "email = '".$email."', ";
	$sql_query .= "line_id = '".$line_id."', ";
	$sql_query .= "philosophy = '".$philosophy."' ";
	$sql_query .= "WHERE company_id = ".$company_id.";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$return -> status = "success";
	 	$return -> message = "update_company success";
	 	return json_encode($return);
	}
	else{
		$return -> status = "failed";
	 	$return -> message = "update_company failed";
	 	return json_encode($return);
	}
}

function modify_serviceItems($company_id, $enableItems, $disableItems, $deleteItems){
  if(strcmp($enableItems, "")) $check[] = "add service items:".add_serviceItems($company_id, $enableItems);
	else $check[] = "success";
  if(strcmp($disableItems, "")) $check[] = "disable service items:".disable_serviceItems($company_id, $disableItems);
	else $check[] = "success";
  if(strcmp($deleteItems, "")) $check[] = "delete service items:".delete_serviceItems($company_id, $deleteItems);
	else $check[] = "success";
	$return = new stdClass();
  if(count(array_unique($check))===1 && end($check)==="success"){
		$return -> status = "failed";
	 	$return -> message = "modify_serviceItems failed";
	 	return json_encode($return);
  }
  else{
		$return -> status = "success";
	 	$return -> message = "modify_serviceItems success ";
	 	return json_encode($return);
  }
}

function add_serviceItems($company_id, $items){
	$ja = json_decode($items, true);
	foreach ($ja as $key => $value) {
		$check[$key] = add_serviceItem($company_id, $value[1], get_serviceId($value[0]));
	}
	if(count(array_unique($check))===1 && end($check)==="success") return "success";
	return $check;
}

function add_serviceItem($company_id, $item_name, $service_id){
	delete_serviceItem($company_id, $item_name, TRUE);
	$sql_query = "INSERT INTO service_item (`company_id`, `item_name`, `service_id`) VALUES ";
	$sql_query .= "(".$company_id.", '".$item_name."', ".$service_id.");";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function disable_serviceItems($company_id, $items){
  $ja = json_decode($items, true);
  foreach ($ja as $key => $value) {
    $check[$key] = disable_serviceItem($company_id, $value[1]);
  }
  if(count(array_unique($check))===1 && end($check)==="success") return "success";
  return $check;
}

function disable_serviceItem($company_id, $item_name){
  $sql_query = "UPDATE service_item SET ";
  $sql_query .= "end_time = current_timestamp() ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "AND item_name = '".$item_name."' ";
  $sql_query .= "AND end_time IS NULL;";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function delete_serviceItems($company_id, $items){
	$ja = json_decode($items, true);
	foreach ($ja as $key => $value) {
	  $check[$key] = delete_serviceItem($company_id, $value[1], FALSE);
	}
	if(count(array_unique($check))===1 && end($check)==="success") return "success";
	return $check;
}

function delete_serviceItem($company_id, $item_name, $addNew){
  $sql_query = "UPDATE service_item SET ";
  $sql_query .= "isDelete = true ";
  if(!$addNew) $sql_query .= ", end_time = current_timestamp() ";
  $sql_query .= "WHERE company_id = ".$company_id." ";
  $sql_query .= "AND item_name = '".$item_name."' ";
  $sql_query .= "AND isDelete = false;";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function get_serviceId($service_name){
	$sql_query = "SELECT * FROM service_class ";
	$sql_query .= "WHERE service_name = '".$service_name."';";
	$result = query($sql_query);
	$row_result = mysqli_fetch_assoc($result);
	return $row_result['service_id'];
}


function modify_discount($company_id, $valuate, $deposit, $cancel, $periodItems, $deleteItems){
	$check[] = modify_free_discount($company_id, $valuate, $deposit, $cancel);
  if(strcmp($deleteItems, "[]")) $check[] = delete_period_discount($deleteItems);
  else $check[] = "success";
  if(strcmp($periodItems, "[]")) $check[] = update_period_discounts($company_id, $periodItems);
  else $check[] = "success";

  $return = new stdClass();
  if(count(array_unique($check))===1 && end($check)==="success"){
		$return -> status = "success";
	 	$return -> message = "modify_discount success";
	 	return json_encode($return);
  }
  else{
		$return -> status = "failed";
		$return -> message = json_encode($check);
		return json_encode($return);
  }
}
function modify_fix_discount($company_id, $fixDiscount, $isEnable){
  $sql_query = "UPDATE company SET fixDiscount = ".$fixDiscount." , isEnable = ".$isEnable." ";
  $sql_query .= "WHERE company_id = ".$company_id." ;";
  $result = query($sql_query);
  $return = new stdClass();
  if(!strcmp($result, "1")){
	$return -> status = "success";
	$return -> message = "modify_fix_discount success";
	return json_encode($return);
  }
  else{
	 $return -> status = "failed";
	$return -> message = "update_address failed";
	return json_encode($return);
  }
}
function modify_contact_address($member_id){
  $sql_query = "UPDATE `member` INNER JOIN orders ON member.member_id = orders.member_id ";
  $sql_query .= "SET member.city = orders.incity, ";
  $sql_query .= "member.district = orders.indistrict, ";
  $sql_query .= "member.contact_address = orders.address2 ";
  $result = query($sql_query);
  $return = new stdClass();
  if(!strcmp($result, "1")){
  	$return -> status = "success";
	$return -> message = "update_address success";
	$result_json = json_encode($return, JSON_FORCE_OBJECT);

	return $result_json;
  }
  else{
  	$return -> status = "failed";
	 	$return -> message = "update_address failed";
	 	return json_encode($return, JSON_FORCE_OBJECT);
  }
}
function modify_free_discount($company_id, $valuate, $deposit, $cancel){
  $sql_query = "INSERT INTO `discount` (`company_id`, `valuate`, `deposit`, `cancel`) VALUES ";
  $sql_query .= "(".$company_id.", ".$valuate.", ".$deposit.", ".$cancel.");";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function delete_period_discount($deleteItems){
  $ja = json_decode($deleteItems, true);
  foreach ($ja as $key => $discount_id) {
    $sql_query = "DELETE FROM `period_discount` ";
    $sql_query .= "WHERE discount_id = ".$discount_id.";";
    $result = query($sql_query);
    if(!strcmp($result, "1")) $check[$key]="success";
    else $check[$key]= $result;
  }
  if(count(array_unique($check))===1 && end($check)==="success") return "success";
  else return $check;
}

function update_period_discounts($company_id, $periodItems){
  $ja = json_decode($periodItems, true);
  foreach ($ja as $key => $value) {
    if($value[0] == -1)
      $check[$key] = add_period_discount($company_id, $value[1], $value[2], $value[3], $value[4], $value[5]);
    else
      $check[$key] = update_period_discount($value[0], $value[2], $value[3], $value[4], $value[5]);
  }
  if(count(array_unique($check))===1 && end($check)==="success") return "success";
  else return $check;
}

function add_period_discount($company_id, $discount_name, $discount, $start_date, $end_date, $enable){
  $sql_query = "INSERT INTO `period_discount` (`company_id`, `discount_name`, `discount`, `start_date`, `end_date`, `enable`) VALUES ";
  $sql_query .= "(".$company_id.", '".$discount_name."', ".$discount.", '".$start_date."', '".$end_date."', ".$enable.");";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function update_period_discount($discount_id, $discount, $start_date, $end_date, $enable){
  $sql_query = "UPDATE `period_discount` SET ";
  $sql_query .= "discount = ".$discount.", ";
  $sql_query .= "start_date = '".$start_date."', ";
  $sql_query .= "end_date = '".$end_date."', ";
  $sql_query .= "enable = ".$enable." ";
  $sql_query .= "WHERE discount_id = ".$discount_id." ";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function get_orderID(){
	$sql_query = "SELECT max(order_id) AS order_id FROM `orders`;";
	$result = query($sql_query);
	$row_result = mysqli_fetch_assoc($result);
	return $row_result['order_id'];
}

function update_reply($comment_id, $reply){
  $sql_query = "UPDATE `comments` SET ";
  $sql_query .= "reply = '".$reply."' ";
  $sql_query .= "WHERE comment_id = ".$comment_id.";";
  $result = query($sql_query);
  $return = new stdClass();
  if(!strcmp($result, "1")){
  	$return -> status = "success";
	 	$return -> message = "update_reply success";
	 	return json_encode($return);
  }
  else{
  	$return -> status = "failed";
	 	$return -> message = "update_reply failed";
	 	return json_encode($return);
  }
}

function update_announcement_new($announcement_id, $company_id){
  $sql_query = "UPDATE `announcement_company` SET ";
  $sql_query .= "new = FALSE ";
  $sql_query .= "WHERE announcement_id = ".$announcement_id." ";
  $sql_query .= "AND company_id = ".$company_id.";";
  $result = query($sql_query);
  $return = new stdClass();
  if(!strcmp($result, "1")){
  	$return -> status = "success";
	 	$return -> message = "update_announcement_new success";
	 	return json_encode($return);
  }
  else{
  	$return -> status = "failed";
	 	$return -> message = "update_announcement_new failed";
	 	return json_encode($return);
  }
}

function change_status($company_id, $table, $order_id, $status, $plan){
	$planned = $plan;
	$sql_query = "UPDATE `".$table."` ";
	if(!strcmp("choose", $table)) $sql_query .= "SET valuation_status = '".$status."' ";
	else $sql_query .= "SET order_status = '".$status."' ";
	$sql_query .= "WHERE order_id = '".$order_id."' ";
	if(!strcmp("choose",$table)) $sql_query .= "AND company_id = '".$company_id."' AND plan = '".$planned."'";
	$sql_query .= ";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")) {
		$result2 = update_new($order_id, $company_id, 1);
		if(!strcmp($result2, "success")){
			$return -> status = "success";
			$return -> message = "change status success";
			 return json_encode($return);
		}
		else return json_encode("update_new:".$result2);
	}
	else return json_encode("change_status(".$status."):".$result);
}

function change_stat($company_id, $table, $order_id, $status){

	$sql_query = "UPDATE `".$table."` ";
	if(!strcmp("choose", $table)) $sql_query .= "SET valuation_status = '".$status."' ";
	else $sql_query .= "SET order_status = '".$status."' ";
	$sql_query .= "WHERE order_id = '".$order_id."' ";
	if(!strcmp("choose",$table))
		$sql_query .= "AND company_id = '".$company_id."'";
	$sql_query .= ";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")) {
		$result2 = update_new($order_id, $company_id, 1);
		if(!strcmp($result2, "success")){
			$return -> status = "success";
			$return -> message = "change status success";
			 return json_encode($return);
		}
		else return json_encode("update_new:".$result2);
	}
	else return json_encode("change_status(".$status."):".$result);
}

function change_all_status($company_id, $order_id, $status, $plan){
	$sql_query = "UPDATE `choose` SET ";
	$sql_query .= "valuation_status = '".$status."' ";
	$sql_query .= "WHERE order_id = '".$order_id."' ";
	$sql_query .= "AND company_id != '".$company_id."' AND plan = '".$plan."' ;";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")) return "success";
	else return "choose:".$result."; orders:".$result2;
}

function check_companyID($company_id){
	$sql_query = "SELECT company_id FROM `company` ";
	$sql_query .= "WHERE company_id = ".$company_id.";";
	$result = query($sql_query);
	$return = new stdClass();
  $row_result = mysqli_fetch_assoc($result);
  if(!strcmp($row_result['company_id'], $company_id)){
  	$return ->status = "success";
  	$return -> message = "check_companyID success";
  	return json_encode($return);
  }
  else{
  	$return ->status = "failed";
  	$return -> message = "check_companyID failed";
  	return json_encode($return);
  }
}

function update_confirm($order_id, $company_id){
	$sql_query = "UPDATE `choose` SET ";
	$sql_query .= "confirm = TRUE ";
	$sql_query .= "WHERE order_id = ".$order_id." ";
	$sql_query .= "AND company_id = ".$company_id.";";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function today_order($order_id, $company_id){
	$sql_query = "UPDATE `choose` SET ";
	$sql_query .= "moving_date = UTC_TIMESTAMP() + INTERVAL 8 HOUR ";
	$sql_query .= "WHERE company_id = ".$company_id." ";
	$sql_query .= "AND order_id = ".$order_id.";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")){
		$return ->status = "success";
  	$return -> message = "today_order success";
  	return json_encode($return);
	}
	else{
		$return ->status = "failed";
  	$return -> message = "today_order failed";
  	return json_encode($return);
	}
}

function become_order($company_id, $order_id, $plan){
	$planned = $plan;
	$check[] = change_all_status($company_id, $order_id, "cancel", $planned);
	$check[] = change_status($company_id, "choose", $order_id, "chosen", $plan);
	$check[] = change_stat($company_id, "orders", $order_id, "scheduled");
	$return = new stdClass();
	if(count(array_unique($check))===1 && end($check)==="success"){
		$return ->status = "failed";
		$return -> message = "become_order failed";
		return json_encode($return);
	}
		$return ->status = "success";
		$return -> message = "become_order success ";
		return json_encode($return);
}

function get_payment_result($order_id){
	$sql_query = "SELECT credit_paid FROM `orders` ";
	$sql_query .= " WHERE order_id = ".$order_id." ; ";
	$result = query($sql_query);
	$return = new stdClass();
	$row_result = mysqli_fetch_assoc($result);

	if(!strcmp($row_result['credit_paid'], "paid")){
		$return ->status = "order_paid";
		return json_encode($return);
	}
	else{
		$return ->status = "order_unpaid";
		return json_encode($return);
	}
}

function query($sql_query){
  require './connDB.php';
	$result = mysqli_query($db_link, $sql_query);
	if(!$result) $result = "Error: ".mysqli_error($db_link);
  mysqli_close($db_link);
  return $result;
}
?>
