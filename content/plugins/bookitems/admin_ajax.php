<?php
// include core 
//--------------------------------------------------
require_once '../../../cms/includes/inc.core.php';
include_once CMS_ABSPATH . '/cms/includes/inc.functions_pages.php';

if(!get_role_CMS('user') == 1) {die;}


// overall
// --------------------------------------------------
if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {
		
		
		// check client user-agent, prevent session been hijacked
		if($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			die('User agent fail. Please logout and login again.');
		}
		
		$bookitems = new Bookitems();
	
		// $action
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_STRING);
		
		switch($action) {		
		
			case 'action_category_add';
		
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $bookitems->setBookitemsCategoryInsert($title, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;

			case 'action_category_save';
		
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
				$plugin_bookitems_category_id = filter_input(INPUT_POST, 'plugin_bookitems_category_id', FILTER_VALIDATE_INT);
				$description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
				$active = filter_input(INPUT_POST, 'active', FILTER_VALIDATE_INT);
				$position = filter_input(INPUT_POST, 'position', FILTER_VALIDATE_INT);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $bookitems->setBookitemsCategoryiUpdate($title, $description, $active, $position, $plugin_bookitems_category_id, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;
			

			case 'action_unit_add';
		
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
				$plugin_bookitems_category_id = filter_input(INPUT_POST, 'plugin_bookitems_category_id', FILTER_VALIDATE_INT);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $bookitems->setBookitemsUnitInsert($title, $plugin_bookitems_category_id, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;
			
			case 'action_unit_save';
		
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
				$plugin_bookitems_unit_id = filter_input(INPUT_POST, 'plugin_bookitems_unit_id', FILTER_VALIDATE_INT);
				$plugin_bookitems_category_id = filter_input(INPUT_POST, 'plugin_bookitems_category_id', FILTER_VALIDATE_INT);
				$description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
				$image = filter_var(trim($_POST['image']), FILTER_SANITIZE_STRING);
				$active = filter_input(INPUT_POST, 'active', FILTER_VALIDATE_INT);
				$position = filter_input(INPUT_POST, 'position', FILTER_VALIDATE_INT);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $bookitems->setBookitemsUnitUpdate($title, $description, $image, $active, $position, $plugin_bookitems_unit_id, $plugin_bookitems_category_id, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;
			
			case 'action_unit_image_delete';
		
				$img = $_POST['img'];
				echo substr($img, strlen(CMS_DIR)) .'<br />';
				
				$img = CMS_ABSPATH . substr($img, strlen(CMS_DIR));
				
				if (is_file($img)) {
					// remove file
					return unlink($img);
					
				} else {
					return 'no image';
				}
				
			break;

			
			case 'action_unit_booking_delete';
		
				$plugin_bookitems_id = filter_input(INPUT_POST, 'plugin_bookitems_id', FILTER_VALIDATE_INT);
				
				if ($plugin_bookitems_id) {
					$result = $bookitems->deleteBookitemsId($plugin_bookitems_id);
				}
				
			break;
			
			
			case 'action_unit_booking';
			
				
				$plugin_bookitems_unit_id = filter_input(INPUT_POST, 'unit_id', FILTER_VALIDATE_INT);
				$theunit_switch_id = filter_input(INPUT_POST, 'theunit_switch_id', FILTER_VALIDATE_INT);
				// switch unit?
				$plugin_bookitems_unit_id = $theunit_switch_id > 0 ? $theunit_switch_id : $plugin_bookitems_unit_id;
				
				// edit
				$plugin_bookitems_id = filter_input(INPUT_POST, 'plugin_bookitems_id', FILTER_VALIDATE_INT);
				
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
				$description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
				
				$utc_start = isValidDateTime($_POST["datetime_start"]) ? date("Y-m-d H:i:s", strtotime($_POST["datetime_start"])) : null;
				$utc_end = isValidDateTime($_POST["datetime_end"]) ? date("Y-m-d H:i:s", strtotime($_POST["datetime_end"])) : null;
				
				$repeat = filter_input(INPUT_POST, 'repeat', FILTER_VALIDATE_INT);				
				$interval = filter_var(trim($_POST['interval']), FILTER_VALIDATE_INT);
				$occations = filter_var(trim($_POST['occations']), FILTER_VALIDATE_INT);
				
				$dates_include = filter_var(trim($_POST['dates_include']), FILTER_SANITIZE_STRING);
				$dates_exclude = filter_var(trim($_POST['dates_exclude']), FILTER_SANITIZE_STRING);

				$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$users_id = $_SESSION['users_id'];				
				
				if($utc_start > $utc_end) {
					$result = array('success'=>false,'text'=>'Kontrollera datum/tid');
					echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
					die();
				}
				
				if($utc_start && $utc_end && $plugin_bookitems_unit_id) {

					if($plugin_bookitems_id > 0) {
						//update
						$rows = $bookitems->getBookitemsDatesId($plugin_bookitems_unit_id, $utc_start, $utc_end);
						if(!$rows) {
							$result = $bookitems->setBookitemsUpdateId($plugin_bookitems_unit_id, $plugin_bookitems_id, $title, $description, $users_id, $utc_start, $utc_end, $utc_created);
							$reply = $result ? array('success'=>true,'text'=>'Sparat') : array('success'=>false,'text'=>'Uppdatering lyckades inte');
						} else {
							if(count($rows) == 1) {							
								if($rows[0]['plugin_bookitems_id'] == $plugin_bookitems_id) {
									$result = $bookitems->setBookitemsUpdateId($plugin_bookitems_unit_id, $plugin_bookitems_id, $title, $description, $users_id, $utc_start, $utc_end, $utc_created);
									$reply = $result ? array('success'=>true,'text'=>'Sparat') : array('success'=>false,'text'=>'Uppdatering lyckades inte');
								}
							} else {
								$reply = array('success'=>false,'text'=>'Datum intervall kan ej bokas');
							}
						}
					
					} else {
						// insert
						$rows = $bookitems->getBookitemsDatesId($plugin_bookitems_unit_id, $utc_start, $utc_end);
						if(!$rows) {
							$result = $bookitems->setBookitemsId($plugin_bookitems_unit_id, $title, $description, $users_id, $utc_start, $utc_end, $utc_created);
							$reply = $result ? array('success'=>true,'text'=>'Sparat') : array('success'=>false,'text'=>'Uppdatering lyckades inte');
						} else {
							$reply = array('success'=>false,'text'=>'Datum intervall kan ej bokas');
						}
					
					}
					
					echo htmlspecialchars(json_encode($reply), ENT_NOQUOTES);

				}
				
				
				// repeat
				$interval = filter_var(trim($_POST['interval']), FILTER_VALIDATE_INT);
				$occations = filter_var(trim($_POST['occations']), FILTER_VALIDATE_INT);
				
				$dates_include = filter_var(trim($_POST['dates_include']), FILTER_SANITIZE_STRING);
				$dates_exclude = filter_var(trim($_POST['dates_exclude']), FILTER_SANITIZE_STRING);
								
				//remove spaces from string
				$dates_include = preg_replace('/\s+/', '', $dates_include);
				$dates_exclude = preg_replace('/\s+/', '', $dates_exclude);
				
				// to array
				$dates_include = explode(",", $dates_include);
				$dates_exclude = explode(",", $dates_exclude);
				
				if($repeat) {
					
					for($i=1; $i<=$occations; $i++) {
						
						$utc_start = date('Y-m-d H:i:s', strtotime($utc_start. ' + '.$interval.' days'));
						$utc_end = date('Y-m-d H:i:s', strtotime($utc_end. ' + '.$interval.' days'));
						$utc_start_check_date = date('Y-m-d', strtotime($utc_start));
						
						if(!in_array($utc_start_check_date, $dates_exclude)) {
						
							// insert
							$rows = $bookitems->getBookitemsDatesId($plugin_bookitems_unit_id, $utc_start, $utc_end);
							if(!$rows) {
								$result = $bookitems->setBookitemsId($plugin_bookitems_unit_id, $title, $description, $users_id, $utc_start, $utc_end, $utc_created);
								$reply = $result ? array('success'=>true,'text'=>'Sparat') : array('success'=>false,'text'=>'Uppdatering lyckades inte');
							} else {
								$reply = array('success'=>false,'text'=>'Datum intervall kan ej bokas');
							}
						
						}
						
					}
				
				}
				
			break;


			case 'action_get_enhet';
				

				$plugin_bookitems_id = filter_input(INPUT_POST, 'plugin_bookitems_id', FILTER_VALIDATE_INT);
				
				$result = $bookitems->getBookitemsSingle($plugin_bookitems_id);
				if($result) {
					echo json_encode($result);
				}

			break;
			
			
			case 'action_units_show';
				
				$plugin_bookitems_category_id = filter_input(INPUT_POST, 'plugin_bookitems_category_id', FILTER_VALIDATE_INT);
				
				$rows = $bookitems->getBookitemsUnits($plugin_bookitems_category_id);

				$utc_start = isValidDateTime($_POST["datetime_start"]) ? date("Y-m-d H:i:s", strtotime($_POST["datetime_start"])) : null;
				$utc_end = isValidDateTime($_POST["datetime_end"]) ? date("Y-m-d H:i:s", strtotime($_POST["datetime_end"])) : null;
				
				$html = '';
				if($rows) {
					foreach($rows as $row) {					
						$row_unit_dates = $bookitems->getBookitemsDatesId($row['plugin_bookitems_unit_id'], $utc_start, $utc_end);
						$html .= '<div class="units" style="border:2px dashed #EAEAEA;background:#ffffdd">';					
							$html .= '<img src="'.CMS_DIR.'/content/uploads/plugins/bookitems/'.$row['image'].'" style="float:right;padding:0 0 5px 5px;" />';
							$html .= '<h5>'.$row['title'].'</h5>';
							$html .= '<p style="font-size:0.9em;">';
							$html .= $row['description'];
							$html .= '</p>';
							if($row_unit_dates) {
								$html .= '<span style="color:red;">| bokad |</span>';
							} else {
								$html .= '<span class="toolbar"><button class="btn_units_choose" id="'.$row['plugin_bookitems_unit_id'].'" data-title="'.$row['title'].'" data-description="'.$row['description'].'" data-imagesrc="'.CMS_DIR.'/content/uploads/plugins/bookitems/'.$row['image'].'">välj</button></span>';
							}
						$html .= '</div>';
					}
				}
				echo $html;
				
			break;

			case 'cheek';
				$rows = $bookitems->getBookitemsMine($_SESSION["users_id"]);

				foreach($rows as $row) {
					
					$start = strtotime($row['utc_start']);
					$start_dag = date('Y-m-d', $start);
					$start_datetime = date('Y-m-d H:i', $start);
					$start_time = date('H:i', $start);
					
					$slut = strtotime($row['utc_end']);
					$slut_dag = date('Y-m-d', $slut);
					$slut_datetime = date('Y-m-d H:i', $slut);
					$slut_time = date('H:i', $slut);
					
					if($start_dag && $slut_dag) {
						echo '<h5>'.date('j/n Y', $start).'</h5>';
						echo $start_time .'-'. $slut_time;
					} else {
						echo $start_dag .'-'. $slut_dag;
					}
					
					echo ' | '.$row['enhet_title'] . ' [ '. $row['title'] . ' ]<br />';					
				
				}
			break;

			case 'bookitems_category_rights_add_users';
				

				$plugin_bookitems_category_id = filter_input(INPUT_POST, 'plugin_bookitems_category_id', FILTER_VALIDATE_INT);
				echo $plugin_bookitems_category_id;
				
				$pieces = explode(",", $_POST['users_meta']);
				if(is_array($pieces)) {
					$i = count($pieces);
					$email = filter_var(trim($pieces[$i-1]), FILTER_SANITIZE_STRING);	
					$users = new Users();
					$result = $users->getUsersEmail($email);
					if($result) {
						$users_id = intval($result['users_id']);
						
						$rights = new Bookitems();
						$users_id = intval($result['users_id']);
						// check existing post before set new...
						$exists = $rights->getBookitemsUsersRights($plugin_bookitems_category_id, $users_id);
						if(!$exists) {
							// insert into calendar_categories_rights
							
							$row = $rights->setBookitemsUsersRightsNew($plugin_bookitems_category_id, $users_id);
							if($row) {
								//echo 'saved';
							}
							
							
						}
						
					}
				}
				
			break;

		
		
			case 'bookitems_category_rights_add_groups':
				
				$plugin_bookitems_category_id = filter_input(INPUT_POST, 'plugin_bookitems_category_id', FILTER_VALIDATE_INT);
				// get groups_id from POST groups_meta
				// groups_meta contains: title
				// use explode to split
				$pieces = explode(",", $_POST['groups_meta']);
				if(is_array($pieces)) {
					$i = count($pieces);
					$groups_id = filter_var(trim($pieces[$i-1]), FILTER_SANITIZE_STRING);								
					$groups_id = intval($groups_id);
					$rights = new Bookitems();

					// check existing post before set new...
					$exists = $rights->getBookitemsGroupsRights($plugin_bookitems_category_id, $groups_id);					
					if(!$exists) {
						// insert into calendar_categories_rights
						$row = $rights->setBookitemsGroupsRightsNew($plugin_bookitems_category_id, $groups_id);
						echo $row;
						if($row) {
							//echo 'saved';
						}
					}
					
					
				}
			break;
			
			

			case 'plugin_bookitems_category_rights_delete':
				
				$plugin_bookitems_category_rights_id = filter_input(INPUT_POST, 'plugin_bookitems_category_rights_id', FILTER_VALIDATE_INT);

				// delete
				$rights = new Bookitems();
				$row = $rights->setBookitemsRightsDelete($plugin_bookitems_category_rights_id);
				
				if($row) {
					//echo 'saved';
				}
				
			break;
			
			
			case 'plugin_bookitems_category_rights_save':
				// this can be made less database intensive... fix arrays one sunny day
				// pages_rights_id
				$r_id = $_POST['r_id'];
				
				$r_read = $_POST['r_read'];
				$r_edit = $_POST['r_edit'];
				$r_create = $_POST['r_create'];
				
				// check if we have rights to set
				$r_id = explode(",",$r_id);
				
				if(is_array($r_id)) {
					$rights = new Bookitems();
					
					// set read rights to true
					$r_read = explode(",",$r_read);
					if(is_array($r_read)) {
						$r = 'rights_read';
						$value = 1;
						foreach($r_read as $plugin_bookitems_category_rights_id) {
							$plugin_bookitems_category_rights_id = intval($plugin_bookitems_category_rights_id);
							$rights->setBookitemsRightsUpdate($plugin_bookitems_category_rights_id, $r, $value);
						}
					}
					
					// set read rights to false
					if(is_array($r_read)) {
						$r_diff = array_diff($r_id, $r_read);
						if(is_array($r_diff)) {
							$r = 'rights_read';
							$value = 0;
							foreach($r_diff as $plugin_bookitems_category_rights_id) {
								$plugin_bookitems_category_rights_id = intval($plugin_bookitems_category_rights_id);
								$rights->setBookitemsRightsUpdate($plugin_bookitems_category_rights_id, $r, $value);
							}
						}
					}
					
					// set edit rights to true
					$r_edit = explode(",",$r_edit);
					if(is_array($r_edit)) {
						$r = 'rights_edit';
						$value = 1;
						foreach($r_edit as $plugin_bookitems_category_rights_id) {
							$plugin_bookitems_category_rights_id = intval($plugin_bookitems_category_rights_id);
							$rights->setBookitemsRightsUpdate($plugin_bookitems_category_rights_id, $r, $value);
						}
					}
					
					// set edit rights to false
					if(is_array($r_edit)) {
						$r_diff = array_diff($r_id, $r_edit);
						if(is_array($r_diff)) {
							$r = 'rights_edit';
							$value = 0;
							foreach($r_diff as $plugin_bookitems_category_rights_id) {
								$plugin_bookitems_category_rights_id = intval($plugin_bookitems_category_rights_id);
								$rights->setBookitemsRightsUpdate($plugin_bookitems_category_rights_id, $r, $value);
							}
						}
					}
					
					// set create rights to true
					$r_create = explode(",",$r_create);
					if(is_array($r_create)) {
						$r = 'rights_create';
						$value = 1;
						foreach($r_create as $plugin_bookitems_category_rights_id) {
							$plugin_bookitems_category_rights_id = intval($plugin_bookitems_category_rights_id);
							$rights->setBookitemsRightsUpdate($plugin_bookitems_category_rights_id, $r, $value);
						}
					}
					
					// set create rights to false
					if(is_array($r_create)) {
						$r_diff = array_diff($r_id, $r_create);
						if(is_array($r_diff)) {
							$r = 'rights_create';
							$value = 0;
							foreach($r_diff as $plugin_bookitems_category_rights_id) {
								$plugin_bookitems_category_rights_id = intval($plugin_bookitems_category_rights_id);
								$rights->setBookitemsRightsUpdate($plugin_bookitems_category_rights_id, $r, $value);
							}
						}
					}
					
					echo 'saved';
				}
				
				
			break;
			
			
			
			
			case '';
			break;
		}		
		
	}
}

?>