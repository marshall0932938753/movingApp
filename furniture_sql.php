<?php

  function furniture_detail($order_id, $company_id){
   $sql_query = "SELECT * ";
   $sql_query .= "FROM `furniture` NATURAL JOIN `furniture_list` ";
   $sql_query .= "WHERE order_id = ".$order_id." ";
   $sql_query .= "AND (company_id = ".$company_id." ";
   $sql_query .=      "OR company_id = 999) ";
   $sql_query .= "GROUP BY furniture_id, company_id;";
   $result = query($sql_query);
   return $result;
  }
  
  function convert_furniture($order_id, $company_id){
   $sql_query = "INSERT INTO furniture_list (order_id, company_id, furniture_id, num) ";
   $sql_query .= "SELECT order_id, ".$company_id.", furniture_id, num \n" ;
   $sql_query .= "FROM furniture_list " ;
   $sql_query .= "WHERE order_id = ".$order_id." ";
   $result = query($sql_query);
   var_dump($result);
   return $result;
  }
  
  function furniture_fine_detail($order_id, $company_id){
   $sql_query = "SELECT * ";
   $sql_query .= "FROM `furniture` NATURAL JOIN `furniture_list` ";
   $sql_query .= "WHERE order_id = ".$order_id." ";
   $sql_query .= "AND company_id = ".$company_id." ";
   $sql_query .= "GROUP BY furniture_id; ";
   $result = query($sql_query);
   return $result;
  }

  function get_all_furniture(){
	$sql_query = "SELECT * ";
	$sql_query .= "FROM `furniture";
	$result = query($sql_query);
   return $result;
  }

  function all_space(){
     $sql_query = "SELECT space_type FROM `furniture` ";
     $sql_query .= "GROUP BY space_id;";
     $result = query($sql_query);
     return $result;
   }

   function furniture_space($space_type){
     $sql_query = "SELECT * FROM `furniture` ";
     $sql_query .= "WHERE space_type = '".$space_type."';";
     $result = query($sql_query);
     return $result;
   }

  function furniture_room_detail($order_id, $company_id){
    $sql_query = "SELECT *, furniture_list.furniture_id, furniture_list.order_id, furniture_list.num AS num, furniture_position.num AS p_num ";
    $sql_query .= "FROM (`furniture_list` NATURAL JOIN `furniture`) ";
    $sql_query .= "LEFT OUTER JOIN (`furniture_position` NATURAL JOIN `room`) ";
    $sql_query .= "ON furniture_list.furniture_id = furniture_position.furniture_id ";
    $sql_query .= "WHERE furniture_list.order_id = ".$order_id." ";
    $sql_query .= "AND (company_id = ".$company_id." ";
    $sql_query .=      "OR company_id = 999) ";
    $sql_query .= "GROUP BY furniture_list.furniture_id, company_id ";
    $sql_query .= "ORDER BY floor ASC;";
    $result = query($sql_query);
    return $result;
  }
  function furniture_web_room_detail($order_id, $company_id){
    $sql_query = "SELECT *, furniture_list.furniture_id, furniture_list.order_id, furniture_list.num AS num, furniture_position.num AS p_num ";
    $sql_query .= "FROM (`furniture_list` NATURAL JOIN `furniture`) ";
    $sql_query .= "LEFT OUTER JOIN (`furniture_position` NATURAL JOIN `room`) ";
    $sql_query .= "ON furniture_list.furniture_id = furniture_position.furniture_id ";
    $sql_query .= "WHERE furniture_list.order_id = ".$order_id." ";
    $sql_query .= "AND company_id = ".$company_id." ";
    $sql_query .= "GROUP BY furniture_list.furniture_id, company_id ";
    $sql_query .= "ORDER BY floor ASC;";
    $result = query($sql_query);
    return $result;
  }
  
  function modify_web_furniture($order_id, $company_id, $furnitureItems){
	$ja = json_decode($furnitureItems, true);
	$return = new stdClass();
    foreach ($ja as $key => $furniture_item) {
      $furniture_id = $furniture_item[0];
      $num = $furniture_item[1];

      $original = get_furniture($order_id, 999, $furniture_id);
      $ori_furniture_item = mysqli_fetch_assoc($original);
	  
	  $original_comp = get_furniture($order_id, $company_id, $furniture_id);
      $oricomp_furniture_item = mysqli_fetch_assoc($original_comp);
	  
      if(mysqli_num_rows(get_furniture($order_id, 999, $furniture_id)) != 0){
		if($ori_furniture_item["furniture_id"] == $furniture_id){
			if(mysqli_num_rows($original) == 0 && $num == 0)
				$check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);
			elseif(mysqli_num_rows($original) != 0 && $num == 0)
				$check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);
			elseif(mysqli_num_rows($original) == 0 && $num != 0){
				$check[$key] = add_furniture($order_id, $company_id, $furniture_id, $num);
				$check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);
			}
			elseif(mysqli_num_rows($original) == 1 && $num != 0){
				if(!isset($furniture_item)){
					$check[$key] = add_furniture($order_id, $company_id, $furniture_id, $num);
				}else{
					$check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);
				}	
			}
		}elseif($oricomp_furniture_item["furniture_id"] == $furniture_id){
			if(mysqli_num_rows($original_comp) != 0 && $num == 0)
				$check[$key] = delete_furniture($order_id, $company_id, $furniture_id);
			if(mysqli_num_rows($original_comp) == 0 && $num == 0)
				$check[$key] = delete_furniture($order_id, $company_id, $furniture_id);
			if(mysqli_num_rows($original_comp) == 0 && $num != 0)
				$check[$key] = add_furniture($order_id, $company_id, $furniture_id, $num);
			//elseif(mysqli_num_rows($original) == 1 && $num != $ori_furniture_item["num"])
				//$check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);
			if(mysqli_num_rows($original_comp) != 0 && $num !=0){
				$check[$key] = add_furniture($order_id, $company_id, $furniture_id, $num);
				$check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);	
			}
		}			
        
      }
      else {
        if(mysqli_num_rows($original) == 0 || $num == 0) 
			$check[$key] = delete_furniture($order_id, $company_id, $furniture_id);
		if(mysqli_num_rows($original) != 0 || $num != 0) 
			$check[$key] = add_furniture($order_id, $company_id, $furniture_id, $num);	
		if(mysqli_num_rows($original_comp) != 0 && $num != $oricomp_furniture_item["num"])
			$check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);
		if(mysqli_num_rows($original_comp) != 0 && $num == 0)
			$check[$key] = delete_furniture($order_id, $company_id, $furniture_id);
		//elseif(mysqli_num_rows($original_comp) != 0 && $num != $oricomp_furniture_item["num"])
			//$check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);
		//else $check[$key] = "success";
      }
    }
    if(!isset($check) || (count(array_unique($check))===1 && end($check)==="success")){
		$return -> status = "success";
		$return -> message = "modify_web_furniture success ";
		return json_encode($return);
	}
    else{
		$return -> status = "failed";
		$return -> message = $check;
		return json_encode($return);
	}
  }

  function modify_furniture($order_id, $company_id, $furnitureItems){
    $ja = json_decode($furnitureItems, true);
	$return = new stdClass();
    foreach ($ja as $key => $furniture_item) {
      $furniture_id = $furniture_item[0];
      $num = $furniture_item[1];
	
      $original = get_furniture($order_id, 999, $furniture_id);
      $ori_furniture_item = mysqli_fetch_assoc($original);
      if(mysqli_num_rows(get_furniture($order_id, $company_id, $furniture_id)) == 1){
        if(mysqli_num_rows($original) == 0 && $num == 0)
          $check[$key] = delete_furniture($order_id, $company_id, $furniture_id);
        elseif(mysqli_num_rows($original) == 0 && $num != 0)
          $check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);
        elseif(mysqli_num_rows($original) == 1 && $num == $ori_furniture_item["num"])
          $check[$key] = delete_furniture($order_id, $company_id, $furniture_id);
        elseif(mysqli_num_rows($original) == 1 && $num != $ori_furniture_item["num"])
          $check[$key] = update_furniture($order_id, $company_id, $furniture_id, $num);
      }
      else {
        if(mysqli_num_rows($original) == 0 || ($num != $ori_furniture_item["num"])) $check[$key] = add_furniture($order_id, $company_id, $furniture_id, $num);
        else $check[$key] = "success";
      }
    }
    if(!isset($check) || (count(array_unique($check))===1 && end($check)==="success")){
		$return -> status = "success";
		$return -> message = "modify_furniture success ";
		return json_encode($return);
	}
    else{
		$return -> status = "failed";
		$return -> message = "modify_furniture failed ";
		return json_encode($return);
	} 
  }
  function calculate_furniture($duration, $distance, $movefrom, $moveto, $furnitureItems){
	$ja = json_decode($furnitureItems, true);
    $str="";
    foreach ($ja as $key => $furniture_item) {
      $furniture_id = $furniture_item[0];
	  $num = $furniture_item[1];
	  if(strlen($furniture_id) != 3){
		$furniture_id = substr($furniture_id, 0, 3);
	  }else{
		$furniture_id = $furniture_id ;
	  }
	  //$furniture_array = array("Id:" => $furniture_id, "Count:" => $num);
	  //$result = '{'.'"Id"'.":".'"'.$furniture_id.'"'.',"';
	  //$result .= 'Count"'.':"'.$num.'"}, ';
	  $str .= '{"Id":"'.$furniture_id.'","Count":"'.$num.'"},';
	  //echo $str;
    }
	// API URL
	$url = "https://igprice.com/api/price/GetPrice";
	// Create a new cURL resource
	$ch = curl_init($url);
	
	// Setup request to send json via POST
	$data = '{
    "DURATION":"'.$duration.'",
    "DISTANCE":"'.$distance.'",
    "MVFOPT":"'.$movefrom.'",
    "MVTOPT":"'.$moveto.'",
        "OBJARY": ['.$str.'],
	}'	;
    $data="'".$data."'";
	
	// Attach encoded JSON string to the POST fields
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	
	// Set the content type to application/json
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	
	// Return response instead of outputting
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	// Execute the POST request
	$response_result = curl_exec($ch);
	
	// Close cURL resource
	curl_close($ch);
	
	return ($response_result);
  }
  function get_furniture($order_id, $company_id, $furniture_id){
    $sql_query = "SELECT * FROM `furniture_list` ";
    $sql_query .= "WHERE order_id = ".$order_id." ";
    $sql_query .= "AND furniture_id = ".$furniture_id." ";
    $sql_query .= "AND company_id = ".$company_id.";";
    $result = query($sql_query);
    return $result;
  }
  function add_furniture($order_id, $company_id, $furniture_id, $num){
    $sql_query = "INSERT INTO `furniture_list` (`order_id`, `company_id`, `furniture_id`, `num`) VALUES ";
    $sql_query .= "(".$order_id.", ".$company_id.", ".$furniture_id.", ".$num.");";
    $result = query($sql_query);
    if(!strcmp($result, "1")) return "success";
    else return "add_error: ".$result;
  }
  function delete_furniture($order_id, $company_id, $furniture_id){
	$sql_query = "DELETE FROM `furniture_list` ";
    $sql_query .= "WHERE order_id = ".$order_id." ";
    $sql_query .= "AND furniture_id = ".$furniture_id." ";
    $sql_query .= "AND company_id = ".$company_id.";";
    $result = query($sql_query);
    if(!strcmp($result, "1")) return "success";
    else return "delete_error: ".$result;
    $sql_query = "DELETE FROM `furniture_list` ";
    $sql_query .= "WHERE order_id = ".$order_id;
  }
  function update_furniture($order_id, $company_id, $furniture_id, $num){
    $sql_query = "UPDATE `furniture_list` SET num = ".$num." ";
    $sql_query .= "WHERE furniture_id = ".$furniture_id." ";
    $sql_query .= "AND order_id = ".$order_id." ";
    $sql_query .= "AND company_id = ".$company_id.";";
    $result = query($sql_query);
    if(!strcmp($result, "1")) return "success";
    else return "update_error: ".$result;
	/*$sql_query = "UPDATE `furniture_list` SET num = ".$num.", ";
	$sql_query .= "company_id = ".$company_id." ";
	$sql_query .= "WHERE furniture_id = ".$furniture_id." ";
    $sql_query .= "AND order_id = ".$order_id."; ";
    $result = query($sql_query);
    if(!strcmp($result, "1")) return "success";
    else return "update_error: ".$result;*/
  }


  function query($sql_query){
    require './connDB.php';
    $result = mysqli_query($db_link, $sql_query);
    if(!$result) $result = "Error: ".mysqli_error($db_link);
    mysqli_close($db_link);
    return $result;
  }


?>
