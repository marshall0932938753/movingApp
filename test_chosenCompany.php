<?php
function query($sql_query){
    require './connDB.php';
    $result = mysqli_query($db_link, $sql_query);
    if(!$result) $result = "Error: ".mysqli_error($db_link);
    mysqli_close($db_link);
    return $result;
  }
	/*$sql_query = "SELECT * FROM `member` NATURAL JOIN (`orders` NATURAL JOIN `choose`) " ;
	$sql_query .= "LEFT JOIN `company` ON ";
	$sql_query .= "choose.company_id = company.company_id WHERE orders.order_id = 1377 ;" ;
	
	$results = query($sql_query);
	

	//尋找相關估價單資訊(傳進company_id, 尋找company_name以及是否寄信)
	while ($row = mysqli_fetch_assoc($results)) {
		printf($row['company_name']."估價狀態:".$row['val_done']);
		echo '<br>';
	}*/
	$mail = new stdClass();
	$variable = '';
	
	$sql_query = "SELECT * FROM `member` NATURAL JOIN (`orders` NATURAL JOIN `choose`) " ;
	$sql_query .= "LEFT JOIN `company` ON ";
	$sql_query .= "choose.company_id = company.company_id WHERE orders.order_id = 1377 ;" ;
	
	$results = query($sql_query);
		while ($row = mysqli_fetch_assoc($results)) {
			$variable .= $row['company_name']."估價狀態: <b>".$row['val_done'].'</b><br>' ;    
		}	
	$mail->Body = "
   ＊ 此信件為系統發出信件，請勿直接回覆，感謝您的配合。＊<br>
    親愛的會員 您好：<br>
    這封通知信是由598搬家網發出，您的搬家估價單已經評估完成<br><br>
	目前各搬家公司估價狀態: <br>".$variable."<br>
	
	請點擊下方網址登入查看並選擇您想要的搬家公司<br>
    <b>亦或是等待其餘搬家公司回覆報價再進行選擇</b><br><br>    
    https://598new.ddns.net/598_new_20211026/ <br><br>
   
	謝謝! <br><br>
    598搬家網 敬上 <br/>
    598搬家網：https://598new.ddns.net/598_new_20211026<br>
    聯絡我們：service@598mover.com <br>" ; 
	
	echo($mail->Body);
	
	
	
		
		

	//尋找相關估價單資訊(傳進company_id, 尋找company_name以及是否寄信)
	
	//郵件內容
   
	
   
  
   
?>
 