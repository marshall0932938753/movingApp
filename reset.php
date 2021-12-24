<?php
	require 'functional_sql.php';
	
	function resetPassword($mail, $pwd){
		$email = $mail;
        $password = $pwd;
		
		$password_sha = hash("SHA256", $password);
		$password_hash = password_hash($password_sha, PASSWORD_BCRYPT);
		
		
		$result_arr = array('pwd_sha' => $password_sha, 'pwd_hash' => $password_hash);
		
		header('Content-type: application/json; charset=utf-8');
		$result_json = json_encode($result_arr);
		
		updatePassword($email, $password_hash);
		
		echo $result_json;
		return $result_json; 
		
    }
	
	resetPassword($_POST['email'], $_POST['password']);
	
	
	

    
?>