<?php
// include core
//--------------------------------------------------
include_once '../cms/includes/inc.core.php';

if(!isset($_SESSION['users_id']) == 1) {die;}

// only accept $_POST from this §_SESSION['token']
// --------------------------------------------------
if (isset($_POST['token'])){
	if ($_POST['token'] == $_SESSION['token']) {
		
		// check client user-agent, prevent session been hijacked
		if($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			die('User agent fail. Please logout and login again.');
		}
	
		$users = new Users();

		if ($users_id = filter_input(INPUT_POST, 'users_id', FILTER_VALIDATE_INT)) { 

			$trimmed = array_map('trim', $_POST);

			// check action
			if ($action = filter_var($trimmed['action'], FILTER_SANITIZE_STRING)) {
				
				// switch action 
				switch ($action) {
				
					case 'update':
											
						$first_name = $last_name = $email = FALSE;

						$users_id = filter_input(INPUT_POST, 'users_id', FILTER_VALIDATE_INT) ? $_POST['users_id'] : null;
												
						if (strlen($trimmed['first_name']) > 0) {
							$first_name = filter_var($trimmed['first_name'], FILTER_SANITIZE_STRING);
						}

						if (strlen($trimmed['last_name']) > 0) {
							$last_name = filter_var($trimmed['last_name'], FILTER_SANITIZE_STRING);
						}
						
						if(isValidString($trimmed['email'], 'email')) {
							$email = $trimmed['email'];
						}

						// check username value
						if (strlen($trimmed['user_name']) > 0) {
							if(isValidString($trimmed['user_name'], 'username')) {
								$user_name = $trimmed['user_name'];
								
								if($result = $users->getUsersIdUsername($users_id, $user_name)) {
									// if not unique
									if($result['users_id'] != $users_id) {
										$user_name = null;
										echo 'Please pick another username';
									}
								}
								
							} else {
								echo 'Please check username';
								$user_name = '';
							}
						} else {
							$user_name = '';
						}
						
						if ($first_name && $last_name && $email){
						
							$language = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING);
							$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
							$mobile = filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING);
							$postal = filter_input(INPUT_POST, 'postal', FILTER_SANITIZE_STRING);
							$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
							$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
							$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
							$comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

							$result = $users->setUsersStatus($users_id, $first_name, $last_name, $email, $user_name, $language, $phone, $mobile, $city, $postal, $address, $country, $comment);
							
							if($result) {
								$history = new History();
								$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
								$history->setHistory($users_id, 'users_id', 'UPDATE', 'settings', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
								$_SESSION['language'] = $language;
							}
							
							return $result;
						}
				
						break;

					case 'update_roles':
											
						$users_id = filter_input(INPUT_POST, 'users_id', FILTER_VALIDATE_INT) ? $_POST['users_id'] : 0;			
						$role_CMS = filter_input(INPUT_POST, 'role_CMS', FILTER_VALIDATE_INT) ? $_POST['role_CMS'] : 0;
						$role_LMS = filter_input(INPUT_POST, 'role_LMS', FILTER_VALIDATE_INT) ? $_POST['role_LMS'] : 0;
													
						$result = $users->setUsersRoles($users_id, $role_CMS, $role_LMS);
						return $result;

						if($result) {
							$history = new History();
							$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history->setHistory($users_id, 'users_id', 'UPDATE', 'roles', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
						}
						
						break;
						

					case 'update_add_to_group':
											
						$users_id = filter_input(INPUT_POST, 'users_id', FILTER_VALIDATE_INT) ? $_POST['users_id'] : 0;			
						$groups_id = filter_input(INPUT_POST, 'groups_id', FILTER_VALIDATE_INT) ? $_POST['groups_id'] : 0;
						$groups = new Groups();
						
						$row = $groups->getGroupsMembershipUser($groups_id, $users_id);
						
						if(!$row) {
							$result = $groups->setGroupsMembership($groups_id, $users_id);
							
							if($result) {
								$history = new History();
								$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
								$history->setHistory($users_id, 'users_id', 'UPDATE', 'membership', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
							}							
						}
						
					break;
					
					case 'update_remove_from_group':

						$groups_members_id = filter_input(INPUT_POST, 'groups_members_id', FILTER_VALIDATE_INT) ? $_POST['groups_members_id'] : 0;
						$groups = new Groups();
						$result = $groups->setGroupsMembershipDelete($groups_members_id);
						
						if($result) {
							$history = new History();
							$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history->setHistory($users_id, 'users_id', 'UPDATE', 'membership', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
						}
												
					break;

					case 'update_rights':
					
						$users_id = filter_input(INPUT_POST, 'users_id', FILTER_VALIDATE_INT) ? $_POST['users_id'] : 0;			
						$profile_edit = filter_input(INPUT_POST, 'profile_edit', FILTER_VALIDATE_INT) ? $_POST['profile_edit'] : 0;
						$debug = 0;
					
						// make sure administrators always can edit profile... ...and just administrators can show debug
						if($_SESSION['role_CMS'] >= 5) {
							
							$debug = filter_input(INPUT_POST, 'debug', FILTER_VALIDATE_INT) ? $_POST['debug'] : 0;
							
							if($_SESSION['users_id'] == $users_id) {
								$profile_edit = 1;
							}	
						}
					
						$result = $users->setUsersRights($users_id, $profile_edit, $debug);
						
						if($result) {
							$history = new History();
							$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history->setHistory($users_id, 'users_id', 'UPDATE', 'rights', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
						}
												
					break;
												
					case 'users_account_status':
											
						$users_id = filter_input(INPUT_POST, 'users_id', FILTER_VALIDATE_INT) ? $_POST['users_id'] : 0;			
						$status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT) ? $_POST['status'] : 0;

						$result = $users->setUsersAccountStatus($users_id, $status);
						return $result;

						if($result) {
							$history = new History();
							$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history->setHistory($users_id, 'users_id', 'UPDATE', 'status', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
						}
						
						break;

					case 'send_activation_code':
											
						$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
						$full_name = filter_var(trim($_POST['full_name']), FILTER_SANITIZE_STRING);
						$activation_code = filter_var(trim($_POST['activation_code']), FILTER_SANITIZE_STRING);
						
						// send confirmation email
						$m_body = $full_name .',';
						$m_body .= "<br />";
						$m_body .= translate("Your account have successfully been registered. To activate your account, use the link below. Best regards!", "registration_message_body", $languages);
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
						
						include_once '../cms/includes/inc.send_a_mail.php';
						if(send_a_mail($_SESSION['token'], $to, $full_name, $_SESSION['site_email'], $_SESSION['site_copyright'], $subject, $m_body,'')) {
							echo translate("Message sent", "message_sent", $languages);
							
							$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history_email = new HistoryEmail();
							$history_email->setHistoryEmail($to, $_SESSION['site_email'], $subject, $m_body, $utc_datetime);
						}
						
						break;
						
					case 'activate_account':
											
						$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
						$activation_code = filter_var(trim($_POST['activation_code']), FILTER_SANITIZE_STRING);

						if($result = $users->setUsersActivate($email, $activation_code)) {
							echo 'activated';
						}
						
						break;

						
					case 'users_add':
						
						$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
						
						$users = new Users;
						$result = $users->getUsersEmail($email);
						
						if($result) { die ('Email already exists!');}
						
						$first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
						$last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
						$password1 = valid_password($_POST['password1'], 8) ? $_POST['password1'] : null;
	
						if(isset($password1)) {
							$activation_code = md5(uniqid(rand(), true));
							$pass_hash = password_hash($password1, PASSWORD_DEFAULT);
							$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							if($result = $users->setUsersNew($email, $pass_hash, $first_name, $last_name, $activation_code, $status=1, $utc_created)) {
								echo 'Account created';
							}
						}
						break;
						
					case 'change_password':

						$pass_new = null;						
						$pass = $_POST['pass'];
						$pass_new = $_POST['pass_new'];
						$pass_new_confirm = $_POST['pass_new_confirm'];

						// passwords are simple decoded in javascript using javascript function enc() bitwise XOR
						// decode using last 2 numbers in string
						$parts = getCodedString($pass);
						$pass = enc($parts[0], $parts[1]);
						$parts = getCodedString($pass_new);
						$pass_new = enc($parts[0], $parts[1]);
						$parts = getCodedString($pass_new_confirm);
						$pass_new_confirm = enc($parts[0], $parts[1]);
						
						if (strlen($pass) === 0) {
							$pass = null;
						}					
						
						if (valid_password($pass_new, 8)) {
							if ($pass_new != $pass_new_confirm) {
								$reply = '<span class="reply_failure">New password did not match the confirmed password</span>';
							}
						} else {
							$reply = '<span class="reply_failure">Please enter a valid password</span>';
						}

						if ($pass && $pass_new){

							$rows = $users->getUsersLoginEmail($_SESSION['email']);
							
							if (password_verify($pass, $rows['pass_hash'])) {
								$pass_hash = password_hash($pass_new, PASSWORD_DEFAULT);
								$result = $users->setUsersPassword($users_id, $pass_hash);
								if($result) {
									$history = new History();
									$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
									$history->setHistory($users_id, 'users_id', 'UPDATE', 'password', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
								}
								$reply = 'ok';
							} else {
								$reply = 'Old password failed. Password <b>not</b> changed.';
							}
						
						}
						echo $reply;
						
						break;

						
					case 'set_new_password':
						
						$pass_new = null;

						$pass_new = $_POST['pass_new'];
						$pass_new_confirm = $_POST['pass_new_confirm'];

						// passwords are simple decoded in javascript using javascript function enc() bitwise XOR
						// decode using last 2 numbers in string
						$parts = getCodedString($pass_new);
						$pass_new = enc($parts[0], $parts[1]);
						$parts = getCodedString($pass_new_confirm);
						$pass_new_confirm = enc($parts[0], $parts[1]);
												
						// check for a password and match against the confirmed password
						if (valid_password($pass_new, 8)) {
							if ($pass_new != $pass_new_confirm) {
								$reply = '<span class="reply_failure">New password did not match the confirmed password</span>';
							}
						} else {
							$reply = '<span class="reply_failure">Please enter a valid password</span>';
						}

						if ($pass_new){

							
							$pass_hash = password_hash($pass_new, PASSWORD_DEFAULT);
							$result = $users->setUsersPassword($users_id, $pass_hash);
							if($result) {
								echo 'ok';
								$history = new History();
								$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
								$history->setHistory($users_id, 'users_id', 'UPDATE', 'password', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
							}

						}
						break;
				}
			}
		}
	}
}

?>