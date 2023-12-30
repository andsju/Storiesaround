<?php
// include core
//--------------------------------------------------
include_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';

if(!get_role_CMS('user') == 1) {die;}

// overall
// --------------------------------------------------
if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {
		// validate action
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		
		// switch action 
		switch ($action) {

			case 'plugins_install':

				$plugins_class = filter_var(trim($_POST['plugins_class']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$pl = new Plugins();
				// install widget if not installed
				if(!$pl->getPluginsInstall($plugins_class)) {
				
					$result = $pl->setPluginsInstall($plugins_class);
					if($result) {
						echo 'plugins installed: '. date('H:i:s');
						
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history = new History();
						$history->setHistory($result, 'plugins_id', 'INSERT', describe('plugins', $plugins_class), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);						
					}
					
				}
			break;
	
			case 'plugins_activate':

				$plugins_id = filter_input(INPUT_POST, 'plugins_id', FILTER_VALIDATE_INT) ? $_POST['plugins_id'] : null;
				$plugins_active = $_POST['plugins_active'];
				if($plugins_id) {
					$pl = new Plugins();				
					$result = $pl->setPluginsActivate($plugins_id, $plugins_active);
					
					if ($result) {
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history = new History();
						$history->setHistory($plugins_id, 'plugins_id', 'UPDATE', describe('activate', $plugins_id), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);						
					}
				}
			break;
	
			case 'plugins_update':

				$plugin = filter_var(trim($_POST['plugin']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

				if($plugin) {

					$site = new Site();
					
					$pl = new $plugin;
					$sqls = $pl->db_sql();

					if(!is_array($sqls)) {die;}
					foreach($sqls as $sql) {
						$result = $site->setSiteUpdate($sql);
						$reply = $result ? date("H:i:s") .' | success' : null;
						echo $reply .'<br />';
					}

					$sqls = $pl->db_sql_update();
					foreach($sqls as $sql) {
						$result = $site->setSiteUpdate($sql);
						$reply = $result ? date("H:i:s") .' | success' : null;
						echo $reply .'<br />';
					}
					
					/*
					if ($result) {
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history = new History();
						$history->setHistory($plugins_id, 'plugins_id', 'UPDATE', describe('activate', $plugins_id), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);						
					}
					*/
				}
			break;
					
					
					
					
			default:
			
				echo 'default...';
			break;
		
		}
	}
}

?>