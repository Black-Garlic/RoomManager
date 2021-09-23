<?php
require_once('./PHPMailer/class.phpmailer.php');
    include("./PHPMailer/class.smtp.php");
    $to = $_POST['to'];
    $content = $_POST['content'];

$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP

//$body .= $nameField;
$title = "=?UTF-8?B?".base64_encode("CSEE 전산전자공학부")."?="."\r\n";
try {
     //$mail->Host       = "mail.gmail.com"; // SMTP server
		$mail->CharSet = "utf-8";
		$mail->Username = "hguswplus"; // Gmail 계정
		$mail->Password = "cseeapd!234"; // 패스워드
		//$mail->From     = "hlkim@handong.edu";
		//$mail->FromName = "SW중심대학";
		//$mail->addReplyTo("hlkim@handong.edu", "Reply Address");

		//$mail->AddAddress($postEmail, $postSname); // 받을 사람 email 주소와 표시될 이름 (표시될 이름은 생략가능)

		$mail->AddAddress($to);
		$mail->SetFrom('csee@handong.edu', $title);
		$mail->addReplyTo('csee@handong.edu', $title);
		$mail->Subject = '[CSEE LectureRoom Reservation System]';
		$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
		$mail->MsgHTML($content);
		$mail->Send();
		echo "Message Sent OK</p>\n";
		header("location: ./room_v1/test.html");

		//$mail->Subject = 'CSEE 마일리지 장학금 사이트 회원 가입'; // 메일 제목
		//$date = date("Y-m-d H:i:s");
		//$hash = password_hash($date, PASSWORD_DEFAULT);  // 현재 일시 암호화

} catch (phpmailerException $e) {
      echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
      echo $e->getMessage(); //Boring error messages from anything else!
}
?>
