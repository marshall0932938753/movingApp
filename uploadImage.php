<?php
require 'connDB.php';
	function query($sql_query){
	require './connDB.php';
	$result = mysqli_query($db_link, $sql_query);
	if(!$result) $result = "Error: ".mysqli_error($db_link);
	mysqli_close($db_link);
	return $result;
	}

   $file_path = "D:/xampp/htdocs/management/uploads/";
   //$file_path = "car_license/";

   $file_path = $file_path . basename( $_FILES['uploaded_file']['name']);

   if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
       echo $file_path;

   } else{
       echo "fail";
   }
?>
