<?php
require 'functional_sql.php';

$result = update_reply($_POST['comment_id'], $_POST['reply']);
print_r($result);
return $result;

 ?>
