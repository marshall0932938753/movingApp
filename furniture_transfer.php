<?php

  $order_id = $_POST['order_id'];
  $webData = mysqli_fetch_assoc(getWebData($order_id));
  echo "webData(".gettype($webData)."): ";
  print_r($webData);
  echo "<br>";
  echo "1. furniture_num(".gettype($webData['furniture_num'])."): ".$webData['furniture_num'];
  echo "<br>";
  $furniture_num = json_decode($webData['furniture_num'], true);
  echo "2. furniture_num(".gettype($furniture_num)."): ";
  print_r($furniture_num);
  echo "<br>";

  foreach ($furniture_num as $furniture_id => $num) {
    $check[] = add_furniture($furniture_id, $order_id, $num);
  }

  if(count(array_unique($check))===1 && end($check)==="success"){
      echo "success";
      return "success";
  }
  else print_r($check);

  function getWebData($order_id){
    $sql_query = "SELECT * FROM `furniture_list_web` ";
    $sql_query .= "WHERE order_id = ".$order_id.";";
    $result = query($sql_query);
    return $result;
  }

  function add_furniture($furniture_id, $order_id, $num){
    $sql_query = "INSERT INTO `furniture_list_app` ";
    $sql_query .= "(`furniture_id`, `order_id`, `room_id`, `company_id`, `num`, `furniture_memo`) VALUES "; /*room_id跟company_id都先設空值*/
    $sql_query .= "(".$furniture_id.", ".$order_id.", null, null, ".$num.", null);";
    $result = query($sql_query);
    if(!strcmp($result, "1") || preg_match("/PRIMARY/", $result))
      return "success";
    else return $result;
  }

  function query($sql_query){
    require './connDB.php';
  	$result = mysqli_query($db_link, $sql_query);
  	if(!$result) $result = "Error: ".mysqli_error($db_link);
    mysqli_close($db_link);
    return $result;
  }
?>
