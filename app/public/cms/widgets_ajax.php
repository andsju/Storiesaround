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

			case 'widgets_install':

				$widgets_class = filter_var(trim($_POST['widgets_class']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$w = new Widgets();
				// install widget if not installed
				if(!$w->getWidgetsInstall($widgets_class)) {
				
					$result = $w->setWidgetsInstall($widgets_class);
					if($result) {
						echo 'widgets installed: '. date('H:i:s');
						
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history = new History();
						$history->setHistory($result, 'widgets_id', 'INSERT', describe('widget', $widgets_class), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);						
					}
					
				}
				break;
	
			case 'widgets_activate':

				/*
				// get title from class
				$w = new $widgets_class();
				$a = $w->info();
				$widgets_title = $a['title'];
				$widgets_css = $a['css'];
				*/
			
				$widgets_id = filter_input(INPUT_POST, 'widgets_id', FILTER_VALIDATE_INT) ? $_POST['widgets_id'] : null;
				$widgets_active = $_POST['widgets_active'];
				if($widgets_id) {
					$w = new Widgets();	
					
					// get widgets settings from class in order to update settings
					$widgets_class = $w->getWidgetsClass($widgets_id);
					//echo $widgets_class;
					$w_class = new $widgets_class();
					$a = $w_class->info();
					$widgets_title = $a['title'];
					$widgets_css = $a['css'];


					$result = $w->setWidgetsActivate($widgets_id, $widgets_active, $widgets_css);
					
					if ($result) {
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history = new History();
						$history->setHistory($widgets_id, 'widgets_id', 'UPDATE', describe('activate', $widgets_id), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);						
					}
				}
				break;
	




	
				
			default:
			
				echo 'default...';
				break;
			
		}
	}
}

?>