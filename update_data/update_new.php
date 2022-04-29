<?php
require 'functional_sql.php';

$result = update_new($_POST['order_id'], $_POST['company_id'], $_POST['new']);
print_r($result);
return $result;
 ?>
