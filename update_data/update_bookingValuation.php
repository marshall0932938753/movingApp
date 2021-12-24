<?php
require 'functional_sql.php';

$result = update_bookingValuation(
  $_POST['order_id'], $_POST['company_id'],
  $_POST['moving_date'], $_POST['estimate_worktime'], $_POST['fee']);
print_r($result);
return $result;

 ?>
