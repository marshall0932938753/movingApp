<?php
require 'functional_sql.php';

$result = update_companyDistribute($_POST['company_id'], $_POST['last_distribution']);
print_r($result);
return $result;

 ?>
