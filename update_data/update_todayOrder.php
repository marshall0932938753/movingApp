<?php
require 'functional_sql.php';

$result = update_todayOrder($_POST['order_id'], $_POST['company_id'], $_POST['memo'], $_POST['accurate_fee']);
print_r($result);
return $result;

 ?>
