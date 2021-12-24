<?php
require 'functional_sql.php';

$result = modify_staff_vehicle_leave($_POST['staffItems'], $_POST['vehicleItems'], $_POST['date']);
print_r($result);
return $result;

 ?>
