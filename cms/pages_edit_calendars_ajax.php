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
		
		
		// validate pages_id
		if ($pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT)) { 
							
			$cal = new Calendar();
							
			// $action
			$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_STRING);
			//$container = filter_var(trim($_POST['container']), FILTER_SANITIZE_STRING);
			
			// switch action 
			switch ($action) {
			

				case 'calendar_events_set':

					$calendar_categories_id = filter_input(INPUT_POST, 'calendar_categories_id', FILTER_VALIDATE_INT) ? $_POST['calendar_categories_id'] : 0;
					$calendar_views_id = filter_input(INPUT_POST, 'calendar_views_id', FILTER_VALIDATE_INT) ? $_POST['calendar_views_id'] : 0;
					$date_initiate = isValidDate($_POST["date_initiate"]) ? $_POST["date_initiate"] : null;
					$period_initiate = filter_var(trim($_POST['period_initiate']), FILTER_SANITIZE_STRING);
					$calendar_area = filter_var(trim($_POST['calendar_area']), FILTER_SANITIZE_STRING);
					$calendar_show = filter_input(INPUT_POST, 'calendar_show', FILTER_VALIDATE_INT) ? $_POST['calendar_show'] : 1;

					if($calendar_categories_id > 0) {
						$calendar_views_id = 0;
						$result = $cal->setPagesCalendar($pages_id, $calendar_categories_id, $calendar_views_id, $date_initiate, $period_initiate, $calendar_area, $calendar_show);
						
						if($result) {
							$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('set calendar category', $calendar_categories_id), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
						}
						
					}
					if($calendar_views_id > 0) {
						$calendar_categories_id = 0;
						$result = $cal->setPagesCalendar($pages_id, $calendar_categories_id, $calendar_views_id, $date_initiate, $period_initiate, $calendar_area, $calendar_show);

						if($result) {
							$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('set calendar views', $calendar_views_id), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
						}

					}
					
					break;

				case 'calendar_views_select':

					$calendar_views_id = filter_input(INPUT_POST, 'calendar_views_id', FILTER_VALIDATE_INT) ? $_POST['calendar_views_id'] : 0;

					if($calendar_views_id > 0) {
						$result = $cal->getCalendarViews($calendar_views_id);
						if($result) {
							echo $result['name'];
						}
					}
					
					break;

				case 'calendar_categories_select':

					$calendar_categories_id = filter_input(INPUT_POST, 'calendar_categories_id', FILTER_VALIDATE_INT) ? $_POST['calendar_categories_id'] : 0;

					if($calendar_categories_id > 0) {
						$result = $cal->getCalendarCategory($calendar_categories_id);
						if($result) {
							echo $result['category'];
						}
					}
					
					break;

					
				default:
				
					echo 'default...';
					break;
				
			}
		}
	}
}

?>