<?php
require 'functional_sql.php';

$result = distribute_pay($_POST['order_id'], $_POST['staff_id'], $_POST['pay']);
print_r($result);
return $result;

 ?>
