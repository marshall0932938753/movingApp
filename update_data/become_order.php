<?php
require 'functional_sql.php';

$result = become_order($_POST['company_id'], $_POST['order_id']);
print_r($result);
return $result;

 ?>
