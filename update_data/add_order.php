<?php
require 'functional_sql.php';

$result = add_order($_POST['company_id'], $_POST['member_name'], 
							$_POST['gender'], $_POST['phone'],$_POST['additional'], $_POST['contact_address'],
							$_POST['outcity'], $_POST['outdistrict'], $_POST['address1'], 
							$_POST['incity'], $_POST['indistrict'] $_POST['address2'], 
							$_POST['moving_date'], $_POST['estimate_fee'], $_POST['worktime'], $_POST['furniture_data']);
print_r($result);
return $result;

 ?>
