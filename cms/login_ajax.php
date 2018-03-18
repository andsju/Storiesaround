<?php
// include core 
//--------------------------------------------------
require_once 'includes/inc.core.php';

if (isset($_SESSION['login_tries'])) {
	if ($_SESSION['login_tries'] >= 7) {
		echo translate("You have tried to login more than 7 times. Consider to reset / re-send your password. You need to restart your browser in order to login in", "login_restart_browser", $languages);
		exit;
	}
}

if ($email_username = filter_input(INPUT_POST, 'email_username', FILTER_SANITIZE_STRING)) { 

	$trimmed = array_map('trim', $_POST);

	if ($action = filter_var($trimmed['action'], FILTER_SANITIZE_STRING)) {

		switch ($action) {
		
			case 'login':
				
				// check for email value, if found assume user login with his/her email. If not found, username...
				if (isValidString($trimmed['email_username'], 'email')) {
					$method = "getUsersLoginEmail";
				} else {
					$method = "getUsersLoginUsername";
				}
				
				$passw = $_POST['passw'];

				// passwords are simple decoded in javascript using javascript function enc() bitwise XOR
				// decode using last 2 numbers in string
				$parts = getCodedString($passw);
				$passw = enc($parts[0], $parts[1]);
				$users = new Users();
				
				if ($result = $users->$method($email_username)) {

					if (password_verify($passw, $result['pass_hash'])) {

						if (isset($result['activation_code'])) {
							// user must activate account
							echo translate("Account must be activated before you can log in. Re-send registration mail", "registration_message_activation", $languages);
							echo '<a href="register_help.php?x='.$email_username.'&token='.$_SESSION['token'].'" target="_blank">&raquo;&raquo;&raquo;</a>';
						} else {

							// if site maintenance mode - only allow superadministrators to login
							if(isset($_SESSION['site_maintenance'])) {
								if($_SESSION['site_maintenance'] == 1) {

									if($result['role_CMS'] < 6) {
										echo translate("Site is under maintenance. Functionality is limited, please visit later", "site_maintenance", $languages);
										die;
									}
								}
							}

							// set site session variables, exclude pass_hash, activation_code, utc_lastvisit, status
							$excl = array('pass_hash','activation_code','utc_lastvisit','status');
							foreach ($result as $key => $value){
								if(!in_array($key,$excl)) {
									if(strlen($key) > 0) {
										$_SESSION[$key] = $value;
									}
								}
							}
							
							// get all groups that user has membership in
							$groups = new Groups();
							$_SESSION['membership'] = $groups->getUsersGroupsIdMembership($_SESSION['users_id']);
							
							// set user-agent session, prevent session been hijacked
							$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

							// add session authentication with plugins
							require_once 'includes/inc.session_authentication.php';
							
							// update last login
							$utc_lastvisit = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$users->setUsersLastvisit($result['users_id'], $utc_lastvisit, $_SESSION['token']);
							
							echo 'ok';		
						}
						
						
					} else {  
						// count login tries
						if (!isset($_SESSION['login_tries'])) {
							$_SESSION['login_tries'] = 1;
						} else {
							$_SESSION['login_tries']++;

							// burning - show login_tries
							echo translate("Wrong password", "login_fail_password", $languages);
							if ($_SESSION['login_tries'] >= 3) {
								echo ' '. $_SESSION['login_tries'];
								echo '(7)';
							}	
						}
					}	
					
				} else {
					// user not found
					echo translate("Wrong password or username", "login_fail", $languages);
				}
	
			break;
		}
	}
}



// validate email_username
if ($email1 = filter_input(INPUT_POST, 'email1', FILTER_SANITIZE_STRING)) { 

	// trim incoming data
	$trimmed = array_map('trim', $_POST);

	// check action
	if ($action = filter_var($trimmed['action'], FILTER_SANITIZE_STRING)) {

		// switch action 
		switch ($action) {
		
			case 'forgot_password':
			
				$users = new Users();
				
				// check if we have a user
				if ($result = $users->getUsersLoginEmail($email1)) {
								
					$password = random_password(8);
					$pass_hash = password_hash($password, PASSWORD_DEFAULT);
					$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					if($r = $users->setUsersResetPassword($email1, $pass_hash)) {
					
						$full_name = $result['first_name'] ." ". $result['last_name'];

						// send email
						$m_body = "";
						$m_body .= $full_name;
						$m_body .= "<br />";
						$m_body .= translate("Your password has been reset. In order to login again, please check your email. Best regards,", "password_reset_message_body", $languages);
						$m_body .= "<br />";					
						$m_body .= $_SESSION['site_name'] .', '. $_SESSION['site_email'];
						$m_body .= "<br />";
						$m_body .= "<br />";
						$m_body .= $password;
						$m_body .= "<br />";
						$m_body .= "<br />";
						$m_body .= "(IP: ". $_SERVER['REMOTE_ADDR'] ."), ". $utc_created;					
						$m_body .= "<br />";
						$m_body .= "<br />";
						$m_body .= CMS_URL ;

						$to = $email1;
						$subject = $_SESSION['site_name'];
						$headers = $_SESSION['site_email'];
						
						// include file
						include_once 'includes/inc.send_a_mail.php';
						
						// send mail
						if(send_a_mail($_SESSION['token'], $to, $full_name, $_SESSION['site_email'], $_SESSION['site_copyright'], $subject, $m_body,'')) {
							echo translate("Please check your email for further instructions", "password_reset_instruction", $languages);							
							
							$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history_email = new HistoryEmail();
							$history_email->setHistoryEmail($to, $_SESSION['site_email'], $subject, $m_body, $utc_datetime);
						}
					}
				}
			
			break;
		}
	}
}

?>