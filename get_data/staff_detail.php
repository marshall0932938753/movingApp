<?php
require 'data_sql.php';
require 'get_data_fun.php';

$result[] = staff_detail($_POST['order_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

 ?>
