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
      $mail->Charset = 'UTF-8';
      $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
      $mail->SMTPAuth   = true;                  // enable SMTP authentication
      $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
      $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
      $mail->Port       = 465;   // set the SMTP port for the GMAIL server
      $mail->SMTPKeepAlive = true;
      $mail->Mailer = "smtp";
      //$mail->Username = "hguswplus"; // Gmail 계정
      //$mail->Password = "cseeapd!234"; // 패스워드
      $mail->Username   = "kkjunseo@gmail.com";  // GMAIL username
      $mail->Password   = "kimjs0073!";            // GMAIL password
      $mail->AddAddress($to);
      $mail->SetFrom('csee@handong.edu', $title);
      $mail->addReplyTo('csee@handong.edu', $title);
      $mail->Subject = '[CSEE LectureRoom Reservation System]';
      $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
      $mail->MsgHTML($content);
      $mail->Send();
      echo "Message Sent OK</p>\n";
      header("location: ../test.html");
} catch (phpmailerException $e) {
      echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
      echo $e->getMessage(); //Boring error messages from anything else!
}
?>
