<?php
// include core
//--------------------------------------------------
include_once 'includes/inc.core.php';

// overall
// --------------------------------------------------
if (isset($_POST['token'])){

	if ($_POST['token'] == $_SESSION['token']) {

		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		
		switch ($action) {
		
			case 'register_user':
				$trimmed = array_map('trim', $_POST);
				$first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				
				// check for an email address, php-email-address-validation php class Google Code
				$validator = new EmailAddressValidator();	
				if ($validator->check_email_address($trimmed['email'])) { 
					$email = filter_var($trimmed['email'], FILTER_SANITIZE_EMAIL);
				}

				$password = $_POST['password'];				

				// passwords are simple decoded in javascript using javascript function enc() bitwise XOR
				// decode using last 2 numbers in string
				$parts = getCodedString($password);
				$password = enc($parts[0], $parts[1]);
				
				if (valid_password($password, 8)) {		
												
					if ($first_name && $last_name && $email) { 
						
						// unique email
						$users = new Users;
						$result = $users->getUsersEmail($email);
								
						// new 
						if(!$result) {

							// create activation code
							$activation_code = md5(uniqid(rand(), true));							
							$pass_hash = password_hash($password, PASSWORD_DEFAULT);
							$status = 1;
							$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$result = $users->setUsersNew($email, $pass_hash, $first_name, $last_name, $activation_code, $status, $utc_created);
							
							$reply = array();
							
							if ($result){
								$reply['result'] = "success";
								
								$fullname = $first_name .' '. $last_name;
								
								// send confirmation email
								$m_body = $fullname .',';
								$m_body .= "<br />";
								$m_body .= translate("Your account has successfully been registered. To activate your account, use the link below. Thanks!", "registration_message_body", $languages);
								$m_body .= "<br />";
								$m_body .= "<br />";
								$m_body .= $_SESSION['site_name'] .', '. $_SESSION['site_email'];
								$m_body .= "<br />";
								$m_body .= CMS_URL ;
								$m_body .= "<br />";
								$m_body .= "<br />";
								$m_body .= CMS_URL.'/cms/activate.php?x='. urlencode($email) ."&y=$activation_code";
								
								$to = $email;
								$subject = translate("Registration confirmation", "registration_message_subject", $languages);
								$headers = $_SESSION['site_email'];
							
								include_once 'includes/inc.send_a_mail.php';
								
								if(send_a_mail($_SESSION['token'], $to, $fullname, $_SESSION['site_email'], $_SESSION['site_copyright'], $subject, $m_body,'')) {
									$success = '<h3 class="admin-heading">'.translate("Registration completed", "registration_completed", $languages).'</h3>';
									$success .= '<p>'.translate("Message sent, please use activation code in mail", "registration_message_sent", $languages).'</p>';
									$success .= '<p></p>';
									$success .= '<p>';
									$success .= '<a href="'.$_SESSION['site_domain_url'].'">'.$_SESSION['site_domain'].'</a>';
									$success .= '</p>';
									
									$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
									$history_email = new HistoryEmail();
									$history_email->setHistoryEmail($to, $_SESSION['site_email'], $subject, $m_body, $utc_datetime);
									
								}
								
								//hide form
								$hide_form = true;
								
								// send mail to site admin
								$subject2 = translate("User registration", "registration_message_siteadmin_subject", $languages);
								$m_body2 =  translate("A new user has been registred:", "registration_message_siteadmin_body", $languages);
								$m_body2 .= "<br />";
								$m_body2 .= "<br />";
								$m_body2 .=  $fullname.", ".$to;
								$m_body2 .= "<br />";
								$m_body2 .= "<br />";
								$m_body2 .= $_SESSION['site_name'] .', '. $_SESSION['site_email'];
								$m_body2 .= "<br />";
								$m_body2 .= CMS_URL;
								
								send_a_mail($_SESSION['token'], $_SESSION['site_email'], $_SESSION['site_email'], $_SESSION['site_email'], $_SESSION['site_copyright'], 'no-reply', $m_body2,'');

								$reply['message'] = $success;
								
							} else {	
								$reply['result'] = "fail";
								$reply['message'] = translate("Registration failed", "registration_failed", $languages);
							}
							
							$dbh = null;

						} else {
							$reply['result'] = "fail";
							$reply['message'] = translate("Email address has already been registered.", "registration_email_exists", $languages);
						}
					}
			
				}

				echo json_encode($reply);
				
			break;
		
			case 'register_message_resend':

				$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$full_name = filter_var(trim($_POST['full_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$activation_code = filter_var(trim($_POST['activation_code']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				
				// send confirmation email
				$m_body = $full_name .',';
				$m_body .= "<br />";
				$m_body .= translate("Your account have successfully been registred. To activate your account, use the link below. Best regards!", "registration_message_body", $languages);
				$m_body .= "<br />";
				$m_body .= "<br />";
				$m_body .= $_SESSION['site_name'] .', '. $_SESSION['site_email'];
				$m_body .= "<br />";
				$m_body .= CMS_URL ;
				$m_body .= "<br />";
				$m_body .= "<br />";
				$m_body .= 'http://'.$_SESSION['site_domain'] .'activate.php?x='. urlencode($email) ."&y=$activation_code";
				
				$to = $email;
				$subject = translate("Registration confirmation", "registration_message_subject", $languages);
				$headers = $_SESSION['site_email'];
				
				include_once '../includes/inc.send_a_mail.php';
				
				if(send_a_mail($_SESSION['token'], $to, $full_name, $_SESSION['site_email'], $_SESSION['site_copyright'], $subject, $m_body,'')) {
					echo translate("Message sent", "message_sent", $languages);
					
					$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history_email = new HistoryEmail();
					$history_email->setHistoryEmail($to, $_SESSION['site_email'], $subject, $m_body, $utc_datetime);
				}

			break;

		}
	}			
}