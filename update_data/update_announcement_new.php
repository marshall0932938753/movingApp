<?php
require 'functional_sql.php';

$result = update_announcement_new($_POST['announcement_id'], $_POST['company_id']);
print_r($result);
return $result;

 ?>
