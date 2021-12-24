<?php
require 'functional_sql.php';

$result = modify_staff_vehicle($_POST['company_id'], $_POST['order_id'], $_POST['vehicle_assign'], $_POST['staff_assign'], $_POST['staff_transform'], $_POST['vehicle_transform']);
print_r($result);
return $result;

 ?>
