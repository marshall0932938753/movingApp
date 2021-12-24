<?php
require 'functional_sql.php';

$result = add_staff($_POST['staff_name'], $_POST['company_id']);
print_r($result);
return $result;

 ?>
