<?php
// include core 
//--------------------------------------------------
include_once '../includes/inc.core.php';


// overall
// --------------------------------------------------
if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {
			
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_STRING);
		
		switch($action) {

			case "db_setup":

				include '../install/inc.db_schema.php';

				$site = new Site();
				$i = 0;

				if(is_array($sql_tables)) {
					foreach($sql_tables as $sql) {
						$result = $site->setSiteUpdate($sql);
						$reply = $result ? date("H:i:s") .' | success' : null;
						if($result) {
							$i++;
							echo $reply .'<br />';
						}
					}
				}

			break;			
			
			case "site_install":
			
				$s = null;
			
				$site_name = trim($_POST['site_name']);
				$site_domain_url = filter_var(trim($_POST['site_domain_url']), FILTER_SANITIZE_STRING);
				$site_domain = filter_var(trim($_POST['site_domain']), FILTER_SANITIZE_STRING);
				$first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
				$last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
				$user_name = filter_var(trim($_POST['user_name']), FILTER_SANITIZE_STRING);
				$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
				$password = $_POST['password'];
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

				// check if email address is available
				$users = new Users;
				$check = $users->getUsersEmail($email);

				if($check) { echo 'You have entered an email address that is already registered: <b>'.$email.'</b>. Please change email or delete current user. Reload browser and try again.'; die();}

				$site = new Site();				
				$site_language = 'english';
				$site_email = $email;
				$site_copyright = $site_name;
				$site_theme = "";
				$site_ui_theme = ""; 
				$site_header_image = 'site_header_image.jpg'; 
				$site_timezone = 'Europe/Stockholm'; 
				$site_wysiwyg = 'tinymce';
				$icon_check = '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
				$icon_notice = '<span class="ui-icon ui-icon-notice" style="display:inline-block;"></span>';
				
				$result = $site->setSiteInstall($site_name, $site_domain_url, $site_domain, $site_email, $site_copyright, $site_language, $site_timezone, $site_wysiwyg, $site_theme, $site_ui_theme, $site_header_image, $utc_modified);
				
				if($result) {

					$s .= '<p>'.$icon_check.' Saved site: <i>'.$site_name.'</i></p>';					
					$_SESSION['site_name'] = $site_name;
					$_SESSION['site_domain_url'] = $site_domain_url;
					$_SESSION['site_domain'] = $site_domain;
					$_SESSION['site_theme'] = $site_theme;
					$_SESSION['site_ui_theme'] = $site_ui_theme;
					$_SESSION['site_header_image'] = $site_header_image;
					$_SESSION['site_timezone'] = $site_timezone;
					$_SESSION['site_wysiwyg'] = $site_wysiwyg;
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($result, 'site_id', 'INSERT', describe('site install', $site_name), 0, $_SESSION['token'], $utc_modified);
				
					$pass_hash = password_hash($password, PASSWORD_DEFAULT);
					$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');					
					
					$result2 = $users->setUsersAdmin($email, $pass_hash, $first_name, $last_name, $user_name, $utc_created);					
					if($result2) {
						

						$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
						$_SESSION['users_id'] = $result2;
						$_SESSION['first_name'] = $first_name;
						$_SESSION['last_name'] = $last_name;
						$_SESSION['email'] = $email;
						$_SESSION['user_name'] = $user_name;
						$_SESSION['role_CMS'] = 6;
						$_SESSION['site_maintenance'] = 0;
						$_SESSION['debug'] = 0;

						$s .= '<p>'.$icon_check.' Saved user name: <b>'.$user_name.', '.$email.'</b></p>';
						$s .= '<h3>Done!</h3>';
						$s .= '<p>When you click the link below you will proceed to '.$site_domain_url.'</p>'; 
						$s .= '<p>Login with your username and password to start publish content - <a href="'.CMS_DIR.'/cms/pages.php?sample=2" class="obvious">take me to '.$site_domain_url.'</a></b></p>';
						
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history = new History();
						$history->setHistory($result2, 'users_id', 'INSERT', describe('superadministrator', $site_name), 0, $_SESSION['token'], $utc_modified);						
					}
				}

				 echo $s;
			
			break;
		}
	}
}
?>