<?php
  $db_host = "localhost";
  $db_username = "root";
  $db_password = "admin";

  $db_link = @mysqli_connect($db_host, $db_username, $db_password);
  if (!$db_link) die("資料連結失敗！");
  //else echo "資料連結成功";
  mysqli_query($db_link, "SET NAMES 'utf8'");

  $seldb = @mysqli_select_db($db_link, "598_0914");
  if (!$seldb) die("資料庫選擇失敗！");
?>
