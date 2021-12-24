<?php
require 'functional_sql.php';

$result = add_vehicle($_POST['plate_num'], $_POST['vehicle_weight'], $_POST['vehicle_type'], $_POST['company_id']);
print_r($result);
return $result;

 ?>
