<?php
require 'functional_sql.php';

$valuation_time = $_POST['valuation_time'];
$vtimes = explode('~', $valuation_time);
for($i = 0; $i < 2; $i++){
  $vtime = explode(':', $vtimes[$i]);
  $vtime[0] = str_pad($vtime[0], 2, '0', STR_PAD_LEFT);
  $vtime[1] = str_pad($vtime[1], 2, '0', STR_PAD_LEFT);
  $vtimes[$i] = implode(':', $vtime);
}
$valuation_time = implode('~', $vtimes);


$result = update_selfValuation($_POST['order_id'], $_POST['company_id'], $_POST['valuation_date'], $valuation_time);
print_r($result);
return $result;
 ?>
