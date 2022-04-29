<?php

function login($user_email, $password){
	$sql_query = "SELECT * FROM `user` ";
	$sql_query .= "WHERE user_email = '".$user_email."';";
	$result = query($sql_query);
	$return = new stdClass();
  
	if($result == null || $result->num_rows < 1) {
    $return ->status = "login failed";
    $return ->message = "the user doesn't exist.";
    return ($return);
  }
	else{
		$row_result[] = mysqli_fetch_assoc($result);
		$password_hash = $row_result[0]["user_password"];
	}

	$password_sha = hash("SHA256", $password);
	if(password_verify($password_sha, $password_hash)) {
    $return ->status = "login success";
    $return ->user = $row_result[0];
	return $return;
	}
	else {
    $return ->status = "login failed";
    $return ->message = "password incorrected";
    return ($return);
  }
}

function update_user_token($user_email){
	$token = hash("SHA256", uniqid("", true).sprintf("%02d", rand(0, 99)));
	$sql_query = "UPDATE `user` SET ";
	$sql_query .= "token = '".$token."' ";
	$sql_query .= "WHERE user_email = '".$user_email."';";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function update_new($order_id, $company_id, $new){
	$sql_query = "UPDATE `choose` ";
	$sql_query .= "SET new = ".$new." ";
	$sql_query .= "WHERE order_id = ".$order_id." ";
	$sql_query .= "AND company_id = ".$company_id.";";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function update_selfValuation($order_id, $company_id, $valuation_date, $valuation_time){
	$sql_query = "UPDATE `choose` SET ";
	$sql_query .= "valuation_date = '".$valuation_date."', ";
	$sql_query .= "valuation_time = '".$valuation_time."' ";
	$sql_query .= "WHERE company_id = ".$company_id." ";
	$sql_query .= "AND order_id = ".$order_id.";";
	$result = query($sql_query);
	if(!strcmp($result, "1")){
		update_confirm($order_id, $company_id);
		return change_status($company_id, "choose", $order_id, "booking");
	}
	else return $result;
}

function update_bookingValuation($order_id, $company_id, $moving_date, $estimate_worktime, $fee){
	$sql_query = "UPDATE `choose` SET ";
	$sql_query .= "moving_date = '".$moving_date."', ";
	$sql_query .= "estimate_worktime = '".$estimate_worktime."', ";
	$sql_query .= "estimate_fee = '".$fee."' ";
	$sql_query .= "WHERE company_id = ".$company_id." ";
	$sql_query .= "AND order_id = ".$order_id.";";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return change_status($company_id, "choose", $order_id, "match");
	else return "bookingValuation: ".$result;
}

function add_vehicleDemands($order_id, $company_id, $vehicleItems){
  $ja = json_decode($vehicleItems, true);
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
  $sql_query .= "(".$order_id.", ".$company_id.", '".$weight."', '".$type."', ".$num.");";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
	else if(preg_match("/PRIMARY/", $result)) return update_vehicleDemand($order_id, $company_id, $weight, $type, $num);
  else return $result;
}

function update_vehicleDemand($order_id, $company_id, $weight, $type, $num) {
	$sql_query = "UPDATE `vehicle_demand` SET num = ".$num." ";
	$sql_query .= "WHERE order_id = ".$order_id." ";
	$sql_query .= "AND company_id = ".$company_id." ";
	$sql_query .= "AND vehicle_weight = '".$weight."' ";
	$sql_query .= "AND vehicle_type = '".$type."';";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else $result;
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
		$sql_query .= "AND order_id = ".$order_id.";";
		$result = query($sql_query);
		if(!strcmp($result, "1")){
			$return -> status = "success";
			$return -> message = "update today_order success";
			return json_encode($return);
		}
			
		else return json_encode($result);
	}
	else return json_encode($result);
}

function modify_staff_vehicle($company_id, $order_id, $vehicle_assign, $staff_assign, $staff_transform, $vehicle_transform){
  $check[] = modify_vehicleAssignment($order_id, $vehicle_assign);
  $check[] = modify_staffAssignment($order_id, $staff_assign);
  $check[] = transform_orders($order_id, $staff_transform, "staff");
  $check[] = transform_orders($order_id, $vehicle_transform, "vehicle");
  if(count(array_unique($check))===1 && end($check)==="success"){
		if(!strcmp($vehicle_assign, "[]") || !strcmp($staff_assign, "[]")) return "success";
		return change_status($company_id, "orders", $order_id, "assigned");
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
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function add_vehicleAssignment($order_id, $vehicle_id){;
	$sql_query = "INSERT INTO `vehicle_assignment`(`order_id`, `vehicle_id`) VALUES ";
	$sql_query .= "(".$order_id.", ".$vehicle_id.");";
	$result = query($sql_query);
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
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function add_staffAssignment($order_id, $staff_id){;
	$sql_query = "INSERT INTO `staff_assignment`(`order_id`, `staff_id`) VALUES ";
	$sql_query .= "(".$order_id.", ".$staff_id.");";
	$result = query($sql_query);
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

function add_valuation($company_id, $member_name, $gender, $contact_address, $phone, $additional, $from_address, $to_address, $valuation_date, $valuation_time){
	$member_id = get_memberId($member_name);
	if(!$member_id){
		$check[] = add_member($member_name, $gender, $contact_address, $phone);
		$member_id = get_memberId($member_name);
	}
	else $check[] = update_member($member_id, $gender, $contact_address, $phone);

	$sql_query = "INSERT INTO `orders` (`member_id`, `additional`, `from_address`, `to_address`, `order_status`, `auto`) VALUES ";
	$sql_query .= "(".$member_id.", '".$additional."', '".$from_address."', '".$to_address."', 'evaluating', FALSE);";
	$result = query($sql_query);
	if(!strcmp($result, "1")){
		$order_id = get_orderID();
		$sql_query = "INSERT INTO `choose` (`order_id`, `company_id`, `valuation_date`, `valuation_time`, `valuation_status`) VALUES ";
		$sql_query .= "(".$order_id.", ".$company_id.", '".$valuation_date."', '".$valuation_time."', 'booking');";
		$result2 = query($sql_query);
		if(!strcmp($result, "1")) $check[] = "success";
		else $check[] = "add_choose: ".$result2;
	}
	else $check[] = "add_order: ".$result;
	$return = new stdClass();
	if(count(array_unique($check))===1 && end($check)==="success"){
		$return -> status = "success";
		$return ->message = "add_valuation success";
		return json_encode($return);
	}
		$return -> status = "failed";
		$return ->message = "add_valuation failed".$check;
	return json_encode($return);
}

function add_order($company_id, $member_name, $gender, $contact_address, $phone, $additional, $from_address, $to_address, $moving_date, $estimate_fee, $worktime, $furniture_data){
	$member_id = get_memberId($member_name);
	if(!$member_id){
		$check[] = add_member($member_name, $gender, $contact_address, $phone);
		$member_id = get_memberId($member_name);
	}
	else $check[] = update_member($member_id, $gender, $contact_address, $phone);

	$sql_query = "INSERT INTO `orders` (`member_id`, `additional`, `from_address`, `to_address`, `order_status`, `auto`) VALUES ";
	$sql_query .= "(".$member_id.", '".$additional."', '".$from_address."', '".$to_address."', 'scheduled', FALSE);";
	$result = query($sql_query);
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
	if(count(array_unique($check))===1 && end($check)==="success") return "success";
	else return $check;
}

function add_furniture($order_id, $company_id, $furniture_id, $num){
	$sql_query = "INSERT INTO `furniture_list` (`order_id`, `company_id`, `furniture_id`, `num`) VALUES ";
	$sql_query .= "(".$order_id.", ".$company_id.", ".$furniture_id.", ".$num.");";
	$result = query($sql_query);
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

function add_member($member_name, $gender, $contact_address, $phone){
	$sql_query = "INSERT INTO `member` (`member_name`, `gender`, `contact_address`, `phone`) VALUES ";
	$sql_query .= "('".$member_name."', '".$gender."', '".$contact_address."', '".$phone."');";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function update_member($member_id, $gender, $contact_address, $phone){
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
	 if(!strcmp($result, "1")) return "success";
	 else return $result;
 }

function add_vehicle($plate_num, $vehicle_weight, $vehicle_type, $company_id){
 $sql_query = "INSERT INTO `vehicle`(`plate_num`, `vehicle_weight`, `vehicle_type`, `company_id`) VALUES ";
 $sql_query .= "( '".$plate_num."', '".$vehicle_weight."', '".$vehicle_type."', ".$company_id.");";
 $result = query($sql_query);
 if(!strcmp($result, "1")) return "success";
 else return $result;
}

function delete_staff_vehicle($table, $id){
  $sql_query = "UPDATE ".$table." SET ";
  $sql_query .= "end_time = current_timestamp() ";
  $sql_query .= "WHERE ".$table."_id = ".$id.";";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function modify_staff_vehicle_leave($staffItems, $vehicleItems, $date){
  $check[] = modify_staff_leave($staffItems, $date);
  $check[] = modify_vehicle_leave($vehicleItems, $date);
  if(count(array_unique($check))===1 && end($check)==="success")
    return "success";
  else return $check;
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
	$sql_query .= "(".$staff_id.", '".$date."');";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function modify_vehicle_leave($vehicleItems, $date){
  $ja = json_decode($vehicleItems, true);
  $result_d = delete_vehicle_maintain($vehicleItems, $date);
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
    if(!strcmp($result_d, "success"))
      return "success";
    else return "vehicle delete error: ".$result_d;
  }
  else{
    if(!strcmp($result_d, "success"))
      return $check;
    else return "vehicle delete error: ".$result_d;
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
  $sql_query .= "(".$vehicle_id.", '".$date."');";
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
	if(!strcmp($result, "1")){
	 return "success";
	}
	else return $result;
}

function update_companyDistribute($company_id, $last_distribution){
	$sql_query = "UPDATE `company` SET last_distribution = ".$last_distribution." ";
	$sql_query .= "WHERE company_id = ".$company_id.";";
	$result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
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
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function modify_serviceItems($company_id, $enableItems, $disableItems, $deleteItems){
  if(strcmp($enableItems, "")) $check[] = add_serviceItems($company_id, $enableItems);
	else $check[] = "success";
  if(strcmp($disableItems, "")) $check[] = disable_serviceItems($company_id, $disableItems);
	else $check[] = "success";
  if(strcmp($deleteItems, "")) $check[] = delete_serviceItems($company_id, $deleteItems);
	else $check[] = "success";
  if(count(array_unique($check))===1 && end($check)==="success") return "success";
  else return $check;
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
  if(count(array_unique($check))===1 && end($check)==="success") return "success";
  else return $check;
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
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function update_announcement_new($announcement_id, $company_id){
  $sql_query = "UPDATE `announcement_company` SET ";
  $sql_query .= "new = FALSE ";
  $sql_query .= "WHERE announcement_id = ".$announcement_id." ";
  $sql_query .= "AND company_id = ".$company_id.";";
  $result = query($sql_query);
  if(!strcmp($result, "1")) return "success";
  else return $result;
}

function change_status($company_id, $table, $order_id, $status){
	$sql_query = "UPDATE `".$table."` ";
	if(!strcmp("choose", $table)) $sql_query .= "SET valuation_status = '".$status."' ";
	else $sql_query .= "SET order_status = '".$status."' ";
	$sql_query .= "WHERE order_id = '".$order_id."' ";
	if(!strcmp("choose",$table))
		$sql_query .= "AND company_id = '".$company_id."' ";
	$sql_query .= ";";
	$result = query($sql_query);
	$return = new stdClass();
	if(!strcmp($result, "1")) {
		$result2 = update_new($order_id, $company_id, 1);
		if(!strcmp($result2, "success")){
			$return -> status = "success";
			$return -> message = "change_status success";
			return json_encode($return);
		} 
		else{
			$return -> message = "update_new:".$result2;
			return json_encode($return);
		} 
	}
	else{
			$return -> message = "change_status(".$status."):".$result;
			return json_encode($return);
	} 
}

function change_all_status($company_id, $order_id, $status){
	$sql_query = "UPDATE `choose` SET ";
	$sql_query .= "valuation_status = '".$status."' ";
	$sql_query .= "WHERE order_id = '".$order_id."' ";
	$sql_query .= "AND company_id != '".$company_id."';";
	$result = query($sql_query);
	if(!strcmp($result, "1")) return "success";
	else return "choose:".$result."; orders:".$result2;
}

//存在目的?
function check_companyID($company_id){
	$sql_query = "SELECT company_id FROM `company` ";
	$sql_query .= "WHERE company_id = ".$company_id.";";
	$result = query($sql_query);
  $row_result = mysqli_fetch_assoc($result);
  if(!strcmp($row_result['company_id'], $company_id)) return "success";
  else return "failed";
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
	if(!strcmp($result, "1")) return "success";
	else return $result;
}

function become_order($company_id, $order_id){
	$check[] = change_all_status($company_id, $order_id, "cancel");
	$check[] = change_status($company_id, "choose", $order_id, "chosen");
	$check[] = change_status($company_id, "orders", $order_id, "scheduled");
	if(count(array_unique($check))===1 && end($check)==="success") return "success";
	return $check;
}

function query($sql_query){
  require '../connDB.php';
	$result = mysqli_query($db_link, $sql_query);
	if(!$result) $result = "Error: ".mysqli_error($db_link);
  mysqli_close($db_link);
  return $result;
}
?>
