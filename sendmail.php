<?php
require_once('./PHPMailer/src/PHPMailer.php');
require_once('./PHPMailer/src/SMTP.php');
require_once('./PHPMailer/src/Exception.php'); 
require_once './PHPMailer/src/POP3.php';
require_once './PHPMailer/src/OAuth.php';
//require_once ('./create_pdf.php');   

	function query($sql_query){
    require './connDB.php';
    $result = mysqli_query($db_link, $sql_query);
    if(!$result) $result = "Error: ".mysqli_error($db_link);
    mysqli_close($db_link);
    return $result;
  }
	$eMail = $_POST['email'];
	$order_id = $_POST['order_id'];
	//$company_id = $_POST['company_id'];
	$variable = '';
	$sql_query = "SELECT * FROM `member` NATURAL JOIN (`orders` NATURAL JOIN `choose`) " ;
	$sql_query .= "LEFT JOIN `company` ON ";
	$sql_query .= "choose.company_id = company.company_id WHERE orders.order_id = ".$order_id." ;";
	
	$is_done = 0;
	$not_done = 0;
	
	$results = query($sql_query);
	while ($row = mysqli_fetch_assoc($results)) {
		$variable .= "【".$row['company_name']."】"." 估價狀態: <b><font color=red>".$row['val_done'].'</b><br>' ;
		if(!strcmp($row['val_done'],'未完成')){
			$not_done++;
		}else{
			$is_done++;
		}
	}
	
	$default_message= "
	
   ＊ 此信件為系統發出信件，請勿直接回覆，感謝您的配合 ＊<br><br>
    親愛的會員 您好：<br>
    這封通知信是由598搬家網發出，您的搬家估價單已經完成估價。<br>
	目前您所選擇的搬家公司估價狀態為： <br><br>".$variable."<br>
	
    請點擊下方網址登入查看並選擇您想要的搬家公司進行搬家，
	或是等待其餘搬家公司完成報價後再進行選擇，謝謝！</b><br><br>    
    https://598new.ddns.net/598_new_20211026/ <br><br>
    
    感謝您使用598搬家服務網<br>
    598搬家網 敬祝平安順心 <br>
    598搬家網：https://598new.ddns.net/598_new_20211026<br>
    聯絡我們：service@598mover.com <br>

   ";
   
	$allDone_message = "＊ 此信件為系統發出信件，請勿直接回覆，感謝您的配合 ＊<br><br>
    親愛的會員 您好：<br>
    這封通知信是由598搬家網發出，您的搬家估價單已經完成估價。<br>
	目前您所選擇的搬家公司估價狀態為： <br><br>".$variable."<br>
	
    請點擊下方網址登入查看並選擇您想要的搬家公司進行搬家，謝謝！<br>
 
    https://598new.ddns.net/598_new_20211026/ <br><br>
    
    感謝您使用598搬家服務網<br>
    598搬家網 敬祝平安順心 <br>
    598搬家網：https://598new.ddns.net/598_new_20211026<br>
    聯絡我們：service@598mover.com <br>";
	
	
	$results = query($sql_query);
    $mail= new PHPMailer\PHPMailer\PHPMailer();                          //建立新物件
    $mail->IsSMTP();                                    //設定使用SMTP方式寄信
    $mail->SMTPAuth = true;                        //設定SMTP需要驗證
    $mail->SMTPSecure = "ssl";                    // Gmail的SMTP主機需要使用SSL連線
    $mail->Host = "smtp.gmail.com";             //Gamil的SMTP主機
    $mail->Port = 465;                                 //Gamil的SMTP主機的埠號(Gmail為465)。
    $mail->CharSet = "utf-8";                       //郵件編碼
    $mail->Username = "598moving@gmail.com"; //Gamil帳號
    $mail->Password = "598Moving598";                 //Gmail密碼
    $mail->From = "598moving@gmail.com";        //寄件者信箱
    $mail->FromName = "598搬家網";                  //寄件者姓名
    $mail->Subject ="598搬家網估價完成通知"; //郵件標題
	//$mail->AddAttachment($fileNL, '598Moving.pdf' );

	if($not_done != 0){
		$mail->Body = $default_message; //郵件內容
	}else{
		$mail->Body = $allDone_message; //郵件內容
	}
	
	
	
	
    $mail->IsHTML(true);                             //郵件內容為html
    $mail->AddAddress($eMail);            //收件者郵件及名稱
    if(!$mail->Send()){
		$result = new stdClass();
		$result -> status = "Email sent failed: ".$mail->ErrorInfo."Please check your email again.";
        echo json_encode($result);
    }else{
		$result = new stdClass();
		$result -> status = "Email Sent";
        echo json_encode($result);
    }
	
?>
