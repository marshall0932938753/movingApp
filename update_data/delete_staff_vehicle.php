<?php
require 'functional_sql.php';

$result = delete_staff_vehicle($_POST['table'], $_POST['id']);
print_r($result);
return $result;

 ?>
