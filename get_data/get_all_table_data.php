<?php

$sql_query = "SELECT * FROM `".$_POST["table"]."`;";
$result = query($sql_query);
$row_result = result_to_array($result);
$result_json = json_encode($row_result);
echo $result_json;
return $result_json;


function result_to_array($result){
  for($i = 0; $i < count($result); $i++){
    if(!strcmp(gettype($result[$i]), "string")) return $result[$i];
    for($ii = 0; $ii < $result[$i]->num_rows; $ii++)
      $row_result[] = mysqli_fetch_assoc($result[$i]);
  }
  if(!isset($row_result)) $row_result = mysqli_fetch_assoc($result[0]);
  return $row_result;
}



function query($sql_query){
  require '../connDB.php';
  $result = mysqli_query($db_link, $sql_query);
  if(!$result) $result = "Error: ".mysqli_error($db_link);
  mysqli_close($db_link);
  return $result;
}
 ?>
