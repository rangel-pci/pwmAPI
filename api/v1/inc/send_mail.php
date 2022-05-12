<?php
	
	// namespace PHPMailer\PHPMailer;
	// require_once('phpmailer/POP3.php');
	// require_once('phpmailer/SMTP.php');
	// require_once('phpmailer/PHPMailer.php');

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
 	
 	//email usado para enviar as chaves de ativação das contas

	$mail = new PHPMailer();
	$mail->CharSet =  "utf-8";
	$mail->IsSMTP(); // Set mailer to use SMTP
	$mail->SMTPDebug = 0;  // Enable verbose debug output
	$mail->SMTPAuth = true; // Enable SMTP authentication

	$mail->Username = \Config::$mail_login;
	$mail->Password = \Config::$mail_password;
	$mail->SMTPSecure = \Config::$mail_smtp_secure; // Enable TLS encryption, `ssl` also accepted
	$mail->Host = \Config::$mail_host; // SMTP 
	$mail->Port = \Config::$mail_port; // TCP port to connect to
	 

	$mail->setFrom(\Config::$mail_login, \Config::$app_name);
	$mail->AddAddress($email , $name); // Add a recipient
	
	 
	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->ContentType = "text/html";
	 
	if($mail->Send()){
		$str = "OK";
	}else{
		http_response_code(500);
		return json_encode(array('status' => '500', 'response' => 'internal server error. Error: '.$mail->ErrorInfo));
	}
?>