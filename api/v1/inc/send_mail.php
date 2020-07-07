<?php
	
	namespace PHPMailer\PHPMailer;
	require_once('phpmailer/POP3.php');
	require_once('phpmailer/SMTP.php');
	require_once('phpmailer/PHPMailer.php');
 	
 	//email usado para enviar as chaves de ativação das contas
	$your_email = "your_email@gmail.com"
	$your_password = "your_password";


	$mail = new PHPMailer();
	$mail->CharSet =  "utf-8";
	$mail->IsSMTP(); // Set mailer to use SMTP
	$mail->SMTPDebug = 0;  // Enable verbose debug output
	$mail->SMTPAuth = true; // Enable SMTP authentication

	$mail->Username = $your_email;
	$mail->Password = $your_password;
	$mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
	$mail->Host = "smtp.gmail.com"; // SMTP 
	$mail->Port = "587"; // TCP port to connect to
	 

	$mail->setFrom($your_email, 'PWM');
	$mail->AddAddress($email , $name); // Add a recipient
	
	 
	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->ContentType = "text/html";
	 
	if($mail->Send()){
	 $str = "OK"; 
	}else{
	 $str = "ERR"; 
	}

?>