<?php
	require 'functional_sql.php';
	
	function smsVerification($mail){
        $email = $mail;
		$randomNumber = rand(1000,9999);
        $text = "驗證碼:".$randomNumber;
		
		$sql_query = "SELECT user_phone FROM `user` WHERE user_email = '".$email."';" ;
		$result = query($sql_query);
		
		$return_result = new stdClass();
		
		if($result == null || $result->num_rows < 1) {
			$return_result->status = "failed";
			$return_result->message = "the user doesn't exist.";
			echo json_encode($return_result);
			return json_encode($return_result);
		}
		else{
			$row_result[] = mysqli_fetch_assoc($result);
			$phone = $row_result[0]["user_phone"];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://api.message.net.tw/send.php?id=0932938753&password=Shady871018&tel=".$phone."&msg=".$text."&mtype=G&encoding=utf8");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。 這參數很重要 因為如果有輸出的話你api 解析json時會有錯誤
			curl_exec($ch);
			curl_close($ch);
		
			$result_arr = array('email'=> $email, 'phone' => $phone, 'verifyCode' => $randomNumber, 'status' => "success");
		
			header('Content-type: application/json; charset=utf-8');
			$result_json = json_encode($result_arr);
			updateVerifySMS($randomNumber);
			echo $result_json;
			return $result_json; 
		}
		 
		
    }
	
	smsVerification($_POST['email']);
	
	
	

    
?>