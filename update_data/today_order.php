<?php
require 'functional_sql.php';

$result = today_order($_POST['order_id'], $_POST['company_id']);
print_r($result);
return $result;

 ?>
