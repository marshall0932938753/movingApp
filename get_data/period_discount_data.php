<?php
require 'data_sql.php';
require 'get_data_fun.php';

$result[] = period_discount_data($_POST['company_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);
 ?>
