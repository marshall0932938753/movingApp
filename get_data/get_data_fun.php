<?php

  function query($sql_query){
    require '../connDB.php';
    $result = mysqli_query($db_link, $sql_query);
    if(!$result) $result = "Error: ".mysqli_error($db_link);
    mysqli_close($db_link);
    return $result;
  }

  function result_to_array($result){
    for($i = 0; $i < count($result); $i++){
      if(!strcmp(gettype($result[$i]), "string")) return $result[$i];
      for($ii = 0; $ii < $result[$i]->num_rows; $ii++)
        $row_result[] = mysqli_fetch_assoc($result[$i]);
    }
    if(!isset($row_result)) $row_result = mysqli_fetch_assoc($result[0]);
    return $row_result;
  }

function array_to_json($row_result){
  $result_json = json_encode($row_result);
  echo $result_json;
  return $result_json;
}

function orderBytime($row_result){
  if(!isset($row_result)) return null;
  for ($c = 1; $c < sizeof($row_result); $c++) {
    $current = $row_result[$c];
    if(!isset($current['moving_date'])) continue;
    else $time = getMovingStartTime($current['moving_date']);

    for($i = $c-1; $i >= 0; $i--){
      $value = $row_result[$i];
      if(!isset($value['moving_date']))
        $time2 = getValuationStartTime($value['valuation_time']);
      else break;
      $time = getTime($row_result[$i+1]);
      if($time < $time2){
        $temp = $row_result[$i+1];
        $row_result[$i+1] = $row_result[$i];
        $row_result[$i] = $temp;
      }
      else break;
    }
  }
  return $row_result;
}

function getTime($value){
  if(!isset($value['moving_date']))
    $time = getValuationStartTime($value['valuation_time']);
  else $time = getMovingStartTime($value['moving_date']);
  return $time;
}

function getValuationStartTime($valuation_time){
  $vstime = explode('~', $valuation_time);
  return $vstime[0];
}

function getMovingStartTime($moving_date){
  $mtime = explode(' ', $moving_date);
  $mstime = explode(':', $mtime[1]);
  return $mstime[0].":".$mstime[1];
}

function orderByDay($row_result){
  for ($c = 1; $c < sizeof($row_result); $c++) {
    $current = $row_result[$c];

    for($i = $c-1; $i >= 0; $i--){
      $value = $row_result[$i];
      $day2 = getDay($value);
      $value2 = $row_result[$i+1];
      $day = getDay($value2);
      if($day < $day2){
        $temp = $row_result[$i+1];
        $row_result[$i+1] = $row_result[$i];
        $row_result[$i] = $temp;
      }
      else break;
    }
  }
  return $row_result;
}

function getDay($value){
  if(!isset($value['moving_date'])){
    if(isset($value['valuation_date']) && strcmp($value['valuation_status'], "self")) $day = $value['valuation_date'];
    else{
      $datetime = explode(' ', $value['last_update']);
      $day = $datetime[0];
    }
  }
  else{
    $datetime = explode(' ', $value['moving_date']);
    $day = $datetime[0];
  }
  return $day;
}

function getMonth1($year, $month){
  if($month < 10) $monthStr = "0".$month;
  else $monthStr = $month;
  $monthStr = $year."-".$monthStr."-01";

  return $monthStr;
}

function getMonth2($year, $month){
  $month2 = $month+1;
  if($month2 == 13){
    $month2 = 1;
    $year = $year+1;
  }
  if($month2 < 10) $month2Str = "0".$month2;
  else $month2Str = $month2;
  $month2Str = $year."-".$month2Str."-01";
  return $month2Str;
}
 ?>
