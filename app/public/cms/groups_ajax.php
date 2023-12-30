<?php
// include core 
//--------------------------------------------------
include_once 'includes/inc.core.php';

if(!get_role_CMS('user') == 1) {die;}

$users = new Users();
$groups = new Groups();


// search
// prevent usage - check session token
if ((isset($_POST['token']) && (isset($_SESSION['token'])))) {
	if ($_POST['token'] == $_SESSION['token']) {
	
	
	
	
		// $action
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		
		// switch action 
		switch ($action) {
		
			case 'groups_search':

				// search
				$s = $_POST['s'];
				$rows = $groups->getGroupsSearch($s);
				echo json_encode($rows);
				break;


			case 'groups_meta_search':

				// search
				$s = $_POST['s'];
				$rows = $groups->getGroupsMetaSearch($s);
				echo json_encode($rows);
				break;
				

				
			case 'groups_default_search':

				// search
				$s = $_POST['s'];
				$rows = $groups->getGroupsDefaultSearch($s);
				echo json_encode($rows);
				break;
				

			case 'groups_delete':

				// search
				$groups_id = $_POST['groups_id'];
				$result = $groups->deleteGroups($groups_id);				
				return $result;
				
				if ($result) {
					$history = new History();
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history->setHistory($groups_id, 'groups_id', 'DELETE', describe('delete', $groups_id), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);					
				}
								
			break;

			case 'group_save':

				// search
				$groups_id = $_POST['groups_id'];
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$description = filter_var(trim($_POST['description']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$active = filter_input(INPUT_POST, 'active', FILTER_VALIDATE_INT) ? $_POST['active'] : 0;
				$result = $groups->setGroups($title, $description, $active, $groups_id);
				echo $result;
				
				if ($result) {
					$history = new History();
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history->setHistory($groups_id, 'groups_id', 'UPDATE', describe('save', $groups_id), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);					
				}
								
			break;
			
			case 'bulk':

				$groups_id = filter_input(INPUT_POST, 'groups_id', FILTER_VALIDATE_INT) ? $_POST['groups_id'] : null;
				$users = filter_var_array($_POST['users'], FILTER_VALIDATE_INT);					
				$bulk = filter_var(trim($_POST['bulk']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				
				if($groups_id) {
					switch ($bulk) {
					
						case 'delete':
						
							$i_deleted = 0;
							foreach($users as $users_id){
								$r = $groups->setGroupsMembershipDeleteThese($groups_id, $users_id);
								if($r) {
									$i_deleted++;
								}
							}
							
							if($i_deleted > 0 ) {
								echo $i_deleted;							
							}
									
						break;
					
					}
					
				}
				
			break;
				
				
			
			case 'users_add_to_group':

				$groups_id = filter_input(INPUT_POST, 'groups_id', FILTER_VALIDATE_INT) ? $_POST['groups_id'] : null;
				$users = filter_var_array($_POST['users'], FILTER_VALIDATE_INT);					
				
				if($groups_id) {

					$rows_old = $groups->getGroupsMembership($groups_id);

					// flatten array 
					$a_old = flatt_array($rows_old);		
					$a_new = flatt_array($users);
					
					// members to insert
					$a_insert = array_diff($a_new, $a_old);

					$i_inserted = 0;					
					foreach($a_insert as $users_id){
						$r = $groups->setGroupsMembership($groups_id, $users_id);
						if($r) {
							$i_inserted++;
						}
					}
					echo $i_inserted;
				
				}
				
			break;
				
				
				
					
		}
	}
}
?>