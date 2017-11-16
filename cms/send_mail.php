
<?php

// include core
//--------------------------------------------------
require_once 'includes/inc.core.php';

// check post
// --------------------------------------------------
if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {
		
		$cc = null;
		
		// send email
		$m_body = "<p>";
		$m_body .= utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
		$m_body .= "</p>";
		
		foreach($_POST as $name => $value) {
		   $m_body .= "<div>";
		   // exclude token, keep cc from hidden input field
		   if($name == "cc" || $name == "token") {
			if($name == "cc") {
				$cc = $value;
			}
		   } else {
			$m_body .= "$name: $value";
		   }
		   $m_body .= "</div>";
		}  		

		$m_body .= "<p>";
		$m_body .= $_SESSION['site_name'] .', '. $_SESSION['site_email'];
		$m_body .= "</p>";
		
		$headers = $_SESSION['site_email'];
		echo '<div class="send-mail-form>"'. $m_body .'</div>';
		// include file
		include_once 'includes/inc.send_a_mail.php';
		
		$subject = $_SESSION['site_name'] .' - info';
		$smtp_email_to = filter_var($_SESSION['site_email'], FILTER_VALIDATE_EMAIL);
		$site_email_from = filter_var($_SESSION['site_email'], FILTER_VALIDATE_EMAIL);
		
		// send mail

		if(send_a_mail($_SESSION['token'], $smtp_email_to, $smtp_email_to, $site_email_from, $site_email_from, $subject, $m_body, $attach=null)) {
			echo '<p>'.translate("Message sent", "message_sent", $languages) .'</p>';
			
			$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
			$history_email = new HistoryEmail();
			$history_email->setHistoryEmail($smtp_email_to, $site_email_from, 'Webform', $m_body, $utc_datetime);
		}

		// cc
		$copies = explode(",",$cc);

		if(is_array($copies)) {
			foreach($copies as $copy) {
				$copy_to = trim(filter_var($copy, FILTER_VALIDATE_EMAIL));
				//echo '<p>'.$copy_to.'</p>';
				if($copy_to) {
					$result = send_a_mail($_SESSION['token'], $copy_to, $copy_to, $site_email_from, $site_email_from, $subject, $m_body, $attach=null);
				}
			}
		}
		
		// copy to mailer		
		if(isset($_POST['epost'])) {
			
			$m_body_copy = "<p>";			
			$m_body_copy .= translate("Thank you, we have received your request. We will get back shortly.", "email_request", $languages);
			$m_body_copy .= "</p>";
			$m_body_copy .= $m_body;

			$subject = $_SESSION['site_name'] .' - info (copy)';
			$smtp_email_to = filter_var($_POST['epost'], FILTER_VALIDATE_EMAIL);
			$site_email_from = filter_var($_SESSION['site_email'], FILTER_VALIDATE_EMAIL);

			if(send_a_mail($_SESSION['token'], $smtp_email_to, $smtp_email_to, $site_email_from, $site_email_from, $subject, $m_body_copy, $attach=null)) {
				$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$history_email = new HistoryEmail();
				$history_email->setHistoryEmail($smtp_email_to, $site_email_from, 'Webform', $m_body, $utc_datetime);
			}
		}

	}
}



?>