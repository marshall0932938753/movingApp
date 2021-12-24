<?php
require 'functional_sql.php';

$result = add_vehicleDemands($_POST['order_id'], $_POST['company_id'], $_POST['vehicleItems']);
print_r($result);
return $result;

 ?>
