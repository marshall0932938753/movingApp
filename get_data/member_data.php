<?php
require 'get_data_fun.php';

$result[] = member_data();
$row_result = result_to_array($result);
return array_to_json($row_result);

function member_data($member_id){
  $sql_query = "SELECT * FROM `member` WHERE member_id = '".$member_id."';";
  $result = query($sql_query);
  return $result;
}

 ?>
