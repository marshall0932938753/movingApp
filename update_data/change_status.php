<?php
require 'functional_sql.php';

$result = change_status($_POST['company_id'], $_POST['table'], $_POST['order_id'], $_POST['status']);
print_r($result);
return $result;

 ?>
