<?php
require 'data_sql.php';
require 'get_data_fun.php';

$result[] = all_member_data();
$row_result = result_to_array($result);
return array_to_json($row_result);


 ?>
