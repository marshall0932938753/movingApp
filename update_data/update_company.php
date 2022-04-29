<?php
require 'functional_sql.php';

$result = update_company($_POST['company_id'], $_POST['address'], $_POST['phone'], $_POST['staff_num'], $_POST['url'], $_POST['email'], $_POST['line_id'],$_POST['philosophy']);
print_r($result);
return $result;

 ?>
