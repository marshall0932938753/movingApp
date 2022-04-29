<?php
require 'functional_sql.php';

$result = add_valuation($_POST['company_id'], $_POST['member_name'], $_POST['gender'], $_POST['contact_address'], $_POST['phone'],
                        $_POST['additional'], $_POST['from_address'], $_POST['to_address'], $_POST['valuation_date'], $_POST['valuation_time']);
print_r($result);
return $result;

 ?>
