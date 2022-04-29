<?php
	header("Content-Type: text/html; charset=utf-8");
	require 'furniture_sql.php';

	$func = $_POST['function_name'];
	//echo 'func = '.$func.'<br>';
	if (!strcmp("furniture_detail",$func)) {
		$result = furniture_detail($_POST['order_id'], $_POST['company_id']);
	}
	elseif(!strcmp("convert_furniture",$func)){
		$result = convert_furniture($_POST['order_id'], $_POST['company_id']);
	}
	elseif(!strcmp("furniture_fine_detail",$func)){
		$result = furniture_fine_detail($_POST['order_id'], $_POST['company_id']);
	}
	elseif (!strcmp("all_space",$func)) {
		$result = all_space();
	}
	elseif (!strcmp("furniture_space",$func)) {
		$result = furniture_space($_POST['space_type']);
	}
	elseif(!strcmp("get_all_furniture", $func)){
		$result = get_all_furniture();
	}
	elseif (!strcmp("furniture_room_detail",$func)) {
		$result = furniture_room_detail($_POST['order_id'], $_POST['company_id']);
	}
	elseif (!strcmp("furniture_web_room_detail",$func)) {
		$result = furniture_web_room_detail($_POST['order_id'], $_POST['company_id']);
	}
	elseif(!strcmp("modify_furniture", $func)){
		$result = modify_furniture($_POST['order_id'], $_POST['company_id'], $_POST['furniture_data']);
	}
	elseif(!strcmp("modify_web_furniture", $func)){
		$result = modify_web_furniture($_POST['order_id'], $_POST['company_id'], $_POST['furniture_data']);
	}
	elseif(!strcmp("calculate_furniture", $func)){
		$result = calculate_furniture($_POST['duration'], $_POST['distance'], $_POST['mvfopt'], $_POST['mvtopt'], $_POST['furniture_data']);
	}
	elseif(!strcmp("add_furniture", $func)){
		$result = add_furniture($_POST['furniture_id'], $_POST['order_id'], $_POST['company_id'], $_POST['num']);
	}
	elseif(!strcmp("update_furniture", $func)){
		$result = update_furniture($_POST['furniture_id'], $_POST['order_id'], $_POST['company_id'], $_POST['num']);
	}
	elseif(!strcmp("delete_furniture", $func)){
		$result = delete_furniture($_POST['furniture_id'], $_POST['order_id'], $_POST['company_id']);
	}
	else{
		echo "function_name not found.";
		return;
	}

	if(!strcmp(gettype($result), "string")){
		echo $result;
		return $result;
	}

	if(!isset($result) ||  $result->num_rows == 0){
		echo "null";
		return;
	}

	for($i = 0; $i < $result->num_rows; $i++)
		$row_result[] = mysqli_fetch_assoc($result);

	$result_json = json_encode($row_result);
	echo $result_json;

	return $result_json;
?>
