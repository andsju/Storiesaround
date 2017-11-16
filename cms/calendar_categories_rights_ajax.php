<?php
// include core 
//--------------------------------------------------
include_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';

if(!get_role_CMS('user') == 1) {die;}

$pages = new Pages();

// overall
// --------------------------------------------------
if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {
		// check client user-agent, prevent session been hijacked
		if($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			die('User agent fail. Please logout and login again.');
		}
	
		// $action
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_STRING);	
		
		// validate pages_id
		if ($calendar_categories_id = filter_input(INPUT_POST, 'calendar_categories_id', FILTER_VALIDATE_INT)) { 
			// switch action 
			switch ($action) {
			
				case 'calendar_categories_rights_add_users':
					// get users_id from POST users_meta
					// users_meta contains: first_name last_name, email
					// we only need email to get users_id
					// use explode to split
					$pieces = explode(",", $_POST['users_meta']);
					if(is_array($pieces)) {
						$i = count($pieces);
						$email = filter_var(trim($pieces[$i-1]), FILTER_SANITIZE_STRING);	
						$users = new Users();
						$result = $users->getUsersEmail($email);
						if($result) {
							
							$rights = new CalendarRights();
							$users_id = intval($result['users_id']);
							// check existing post before set new...
							$exists = $rights->getCalendarUsersRights($calendar_categories_id, $users_id);
							if(!$exists) {
								// insert into calendar_categories_rights
								$row = $rights->setCalendarUsersRightsNew($calendar_categories_id, $users_id);
								echo $row;
								if($row) {
									//echo 'saved';
								}
							}
						}
					}
					break;
					

				case 'calendar_categories_rights_add_groups':
					
					// get groups_id from POST groups_meta
					// groups_meta contains: title
					// use explode to split
					$pieces = explode(",", $_POST['groups_meta']);
					if(is_array($pieces)) {
						$i = count($pieces);
						$groups_id = filter_var(trim($pieces[$i-1]), FILTER_SANITIZE_STRING);								
						$groups_id = intval($groups_id);
						$rights = new CalendarRights();

						// check existing post before set new...
						$exists = $rights->getCalendarGroupsRightsExists($calendar_categories_id, $groups_id);
						
						if(!$exists) {
							// insert into calendar_categories_rights
							$row = $rights->setCalendarGroupsRightsNew($calendar_categories_id, $groups_id);
							echo $row;
							if($row) {
								//echo 'saved';
							}
						}
						
					}
					break;

					
				case 'calendar_categories_rights_delete':
					$calendar_categories_rights_id = $_POST['calendar_categories_rights_id'];
					$rights = new CalendarRights();
					$row = $rights->setCalendarUsersRightsDelete($calendar_categories_rights_id);
					break;


				case 'calendar_categories_rights_save':
					// this can be made less database intensive... fix arrays one sunny day
					// pages_rights_id
					$r_id = $_POST['r_id'];
					
					$r_read = $_POST['r_read'];
					$r_edit = $_POST['r_edit'];
					$r_create = $_POST['r_create'];
					
					// check if we have rights to set
					$r_id = explode(",",$r_id);
					
					if(is_array($r_id)) {
						$rights = new CalendarRights();
						
						// set read rights to true
						$r_read = explode(",",$r_read);
						if(is_array($r_read)) {
							$r = 'rights_read';
							$value = 1;
							foreach($r_read as $calendar_categories_rights_id) {
								$calendar_categories_rights_id = intval($calendar_categories_rights_id);
								$rights->setCalendarUsersRightsUpdate($calendar_categories_rights_id, $r, $value);
							}
						}
						
						// set read rights to false
						if(is_array($r_read)) {
							$r_diff = array_diff($r_id, $r_read);
							if(is_array($r_diff)) {
								$r = 'rights_read';
								$value = 0;
								foreach($r_diff as $calendar_categories_rights_id) {
									$calendar_categories_rights_id = intval($calendar_categories_rights_id);
									$rights->setCalendarUsersRightsUpdate($calendar_categories_rights_id, $r, $value);
								}
							}
						}
						
						// set edit rights to true
						$r_edit = explode(",",$r_edit);
						if(is_array($r_edit)) {
							$r = 'rights_edit';
							$value = 1;
							foreach($r_edit as $calendar_categories_rights_id) {
								$calendar_categories_rights_id = intval($calendar_categories_rights_id);
								$rights->setCalendarUsersRightsUpdate($calendar_categories_rights_id, $r, $value);
							}
						}
						
						// set edit rights to false
						if(is_array($r_edit)) {
							$r_diff = array_diff($r_id, $r_edit);
							if(is_array($r_diff)) {
								$r = 'rights_edit';
								$value = 0;
								foreach($r_diff as $calendar_categories_rights_id) {
									$calendar_categories_rights_id = intval($calendar_categories_rights_id);
									$rights->setCalendarUsersRightsUpdate($calendar_categories_rights_id, $r, $value);
								}
							}
						}
						
						// set create rights to true
						$r_create = explode(",",$r_create);
						if(is_array($r_create)) {
							$r = 'rights_create';
							$value = 1;
							foreach($r_create as $calendar_categories_rights_id) {
								$calendar_categories_rights_id = intval($calendar_categories_rights_id);
								$rights->setCalendarUsersRightsUpdate($calendar_categories_rights_id, $r, $value);
							}
						}
						
						// set create rights to false
						if(is_array($r_create)) {
							$r_diff = array_diff($r_id, $r_create);
							if(is_array($r_diff)) {
								$r = 'rights_create';
								$value = 0;
								foreach($r_diff as $calendar_categories_rights_id) {
									$calendar_categories_rights_id = intval($calendar_categories_rights_id);
									$rights->setCalendarUsersRightsUpdate($calendar_categories_rights_id, $r, $value);
								}
							}
						}
						
						echo 'saved';
					}
					
					
					break;


					
			}
		}
	}
}

?>