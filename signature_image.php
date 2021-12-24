<?php
	require 'functional_sql.php';
	
	function imageBase64($order_id, $encode){
        $image_encoded = $encode;
	
		
		$sql_query = "UPDATE `orders` SET signature = 'data:image/png;base64,".$image_encoded."' ";
		$sql_query .= "WHERE `order_id` = '".$order_id."'; ";
		$result = query($sql_query);
		
		$return_result = new stdClass();
		
		if(!strcmp($result, "1")) {
			$return_result->status = "success";
			$return_result->message = $result;
			echo json_encode($return_result);
			return json_encode($return_result);
		}
		else{
			$return_result->status = "failed";
			$return_result->message = $result;
			echo json_encode($return_result);
			return json_encode($return_result);
		}
		 
		
    }
	
	imageBase64($_POST['order_id'], $_POST['encode']);
	
	
	

    
?>