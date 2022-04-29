<?php
require 'get_data_fun.php';

$result[] = comment_detail($_POST['comment_id']);
$row_result = result_to_array($result);
return array_to_json($row_result);

function comment_detail($comment_id){
  $sql_query = "SELECT * FROM comments NATURAL JOIN orders NATURAL JOIN member ";
  $sql_query .= "WHERE comment_id = ".$comment_id.";";
  $result = query($sql_query);
  return $result;
}
 ?>
