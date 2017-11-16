<?php
// include file
if(!defined('VALID_INCL')){die('Restricted access');}


 /*** include configuration file  ***/
//--------------------------------------------------
require_once 'inc.core.php';

 
// send mail
//function send_a_mail($to, $from, $subject, $body, array $attachments) {
function send_a_mail($token, $to, $to_name, $from, $from_name, $subject, $body, $attachments) {
		
	if(!filter_var($to, FILTER_VALIDATE_EMAIL)) {die(); }
	if(!is_string($to_name)) { die(); }
	if(!filter_var($from, FILTER_VALIDATE_EMAIL)) { die(); }
	if(!is_string($from_name)) { die(); }
	if(!is_string($subject)) { die(); }
	
	if ($token == $_SESSION['token']) {
						
		// get SMTP settings		
		$z = new Site();
		$smtp = $z->getSiteSMTP();
		if(!$smtp) { die('SMTP settings fail'); };
		
		//include phpmailer class
		require_once(CMS_ABSPATH.'/cms/libraries/phpmailer/PHPMailerAutoload.php');

		$mail = new PHPMailer(true); 								// the true param means it will throw exceptions on errors
		$mail->CharSet = 'UTF-8';
		
		switch($_SESSION['site_mail_method']) {
		
			case '0';
				die();
			break;
			case '1';
				$mail->isMail();
			break;
			case '2';
				$mail->isSendMail();
			break;
			case '3';
				$mail->isQmail();
			break;
			case '4';
				$mail->IsSMTP();
			break;
			
		}


		try {
			
			if($_SESSION['site_mail_method'] == 4) { 		
				
				$debug = $_SESSION['site_smtp_debug'] == 0 ? 0 : 2;
				$mail->SMTPDebug  = $debug;                     		// enables SMTP debug information (for testing)
				$mail->Host       = $smtp['site_smtp_server'];  		// sets the SMTP server
				$mail->SMTPAuth   = $smtp['site_smtp_authentication'];  // enable SMTP authentication
				$mail->Port       = $smtp['site_smtp_port'];            // set the SMTP port
				if ($smtp['site_smtp_authentication'] == 1){
					$mail->Username   = $smtp['site_smtp_username']; 	// SMTP account username
					$mail->Password   = $smtp['site_smtp_password'];    // SMTP account password
				}
				$mail->SMTPSecure = 'tls';
			}
			
			$mail->From = $from;
			$mail->FromName = $from_name;
			$mail->AddAddress($to, $to_name);
			$mail->AddReplyTo($from, $from_name);
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');
			//$mail->AddAttachment(CMS_ABSPATH .'/cms/css/images/site_bg_grey.png'); 
			$mail->WordWrap = 50;                                 	// set word wrap to 50 characters
			if (is_array($attachments)) {
				foreach($attachments as $attachment) {
					if(is_file($attachment)) {
						$mail->AddAttachment($attachment);      		// attachment
					}
				}
			}

			$mail->Subject = $subject;
			$mail->Body = $body;
			// plain text
			$breaks = array("<br />","<br>","<br/>");
			$body_text = str_ireplace($breaks, "\r\n", $body);
			$mail->AltBody = strip_tags($body_text);
			
			$mail->isHTML(true);
			
			if ($mail->send()) {
				return true;
			};
	
		} catch (phpmailerException $e) {
			if($debug == 1) {
				echo $e->errorMessage(); //error messages from PHPMailer
				//print_r($mail);
			}
		}
		
	}

}
?>