<?php
require 'functional_sql.php';

$result[] = modify_discount($_POST['company_id'], $_POST['valuate'], $_POST['deposit'], $_POST['cancel'], $_POST['periodItems'], $_POST['deleteItems']);
print_r($result);
return $result;

 ?>
