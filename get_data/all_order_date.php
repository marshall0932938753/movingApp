<?php
require 'data_sql.php';
require 'get_data_fun.php';

if(isset($_POST['order_status'])) $result[] = all_order_date_status($_POST['company_id'], $_POST['order_status']);
else {
  $result[] = all_valuation_date($_POST['company_id']);
  $result[] = all_order_date($_POST['company_id']);
}
$row_result = result_to_array($result);
return array_to_json($row_result);

 ?>
