<?php
require( dirname(__FILE__).'/PHPMailer/src/PHPMailer.php');
require 'D:\xampp\htdocs\598_new_20211026\header.php';
require( dirname(__FILE__).'/PHPMailer/src/SMTP.php');
require( dirname(__FILE__).'/PHPMailer/src/Exception.php'); 
require( dirname(__FILE__).'/PHPMailer/src/POP3.php');
require( dirname(__FILE__).'/PHPMailer/src/OAuth.php');


/*function query($sql_query){
    require './connDB.php';
    $result = mysqli_query($db_link, $sql_query);
    if(!$result) $result = "Error: ".mysqli_error($db_link);
    mysqli_close($db_link);
    return $result;
}*/

function send_receipt(){
	session_start();
	//$eMail = $_SESSION['email'];
	$eMail = 'marshall871018@gmail.com';
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
    $mail->Subject ="598搬家網付款成功通知"; //郵件標題
	require ('D:/xampp/htdocs/598_new_20211026/app/create_pdf.php');
	$mail->AddAttachment($fileNL, '598Moving.pdf' );
	$mail->Body = '598搬家網付款明細';
    $mail->IsHTML(true);                             //郵件內容為html
    $mail->AddAddress($eMail);            //收件者郵件及名稱
    if(!$mail->Send()){
		$result = new stdClass();
		$result -> status = "Email sent failed: ".$mail->ErrorInfo."Please check your email again.";
        return json_encode($result);
    }else{
		$result = new stdClass();
		$result -> status = "Email Sent";
        return json_encode($result);
    }
	
}
	
	//send_receipt();
	
	

   
?>
