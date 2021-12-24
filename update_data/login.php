<?php
require 'functional_sql.php';

$result = login($_POST['user_email'], $_POST['password']);
if(!strcmp($result->status, "login success")) {
  $result2 = update_user_token($_POST['user_email']);
  if(strcmp($result2, "success")) {
    echo "update token: ".$result2;
    return;
  }
}
echo json_encode($result);
return;


 ?>
