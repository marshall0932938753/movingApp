<?php
require 'get_data_fun.php';

$result[] = staff_assignment_data();
$row_result = result_to_array($result);
return array_to_json($row_result);

function staff_assignment_data(){
   $sql_query = "SELECT * FROM `staff_assignment` ";
   $result = query($sql_query);
   return $result;
 }

 ?>