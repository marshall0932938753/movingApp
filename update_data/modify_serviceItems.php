<?php
require 'functional_sql.php';

$result = modify_serviceItems($_POST['company_id'], $_POST['enableItems'], $_POST['disableItems'], $_POST['deleteItems']);
print_r($result);
return $result;

 ?>
