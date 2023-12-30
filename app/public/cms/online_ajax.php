<?php
// include core 
//--------------------------------------------------
include_once 'includes/inc.core.php';

if(!get_role_CMS('user') == 1) {die;}

// overall
// --------------------------------------------------
if (isset($_POST['token'])){

	if ($_POST['token'] == $_SESSION['token']) {

		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		
		// switch action 
		switch ($action) {
		
			case 'online':
			
				if (isset($_SESSION["users_id"])) {
					$users = new Users();					
					$utc_lastvisit = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$users->setUsersLastvisit($_SESSION["users_id"], $utc_lastvisit, $_SESSION['token']);
				}

			break;
			
			
			case 'whois_online':
				$u = new Users();	
				$utc_lastvisit = new DateTime(gmdate('Y-m-d H:i:s'), new DateTimeZone($dtz));
				$utc_lastvisit->modify('- 60 seconds');
				$utc_lastvisit = $utc_lastvisit->format('Y-m-d H:i:s');
				$users = $u->getUsersOnline($utc_lastvisit); 

				if($users) {
					$s = "<ul>";
						foreach ($users as $user) {
						  $s .= "<li>".$user["name"]."</li>";
						}
					$s .= "</ul>";
					echo $s;
				}
			
			break;
				
		}
	}			
}