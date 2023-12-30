<?php
// include core 
//--------------------------------------------------
include_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';

if(!get_role_CMS('user') == 1) {die;}

// overall
// --------------------------------------------------
if (isset($_GET['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_GET['token'] == $_SESSION['token']) {
		// validate action
		$action = filter_var(trim($_GET['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		
		// switch action 
		switch ($action) {

			case 'calendar_get_events':
			
				$category_date = $_GET['date'];

				$pos = strrpos($category_date,'_');				
				$date = substr($category_date, $pos+1); 
				
				$arr = explode('_',$category_date);
				$category = $arr[0];
				$date = $arr[1];
				
				function convert_line_breaks($string, $line_break=PHP_EOL) {
					$patterns = array(   
										"/(<br>|<br \/>|<br\/>)\s*/i",
										"/(\r\n|\r|\n)/"
					);
					$replacements = array(   
											PHP_EOL,
											$line_break
					);
					$string = preg_replace($patterns, $replacements, $string);
					return $string;
				}
								
				$cal = new Calendar();
				$cal_categories_settings = $cal->getCalendarCategory($calendar_categories_id=$category);
				$cal_rss = $cal_categories_settings['rss'];

				$event = $cal->getCalendarEvent($calendar_categories_id=$category, $date);
				$st = $cal->validTags();
				
				$e = $event_title = $event_rss = $event_link = null;
				
				if(is_array($event)) {
					$e = nl2br($event['event']);
					$event_rss = $event['event_rss'] == 1 ? 'checked=checked' : '';
					$event_title = $event['event_title'];
					$event_link = $event['event_link'];
					//$e =  $event['event'].replace(/\n\r?/g, '<br />');
				}
				
				$html = '<div id="dialog_cal" title="Calendar" style="">';
					$html .= '<div style="font-size:0.8em;padding:10px;">Allowed tags: '.htmlentities($st) .'</div>';
					$html .= '<form id="set_event">';						
						$html .= '<table>';
							$html .= '<tr style="line-height:20px;">';
								$html .= '<td>'.$date.'</td>';
								$html .= '<td><span id="ajax_spinner_events" style="display:none;"><img src="css/images/spinner.gif"></span></td>';
							$html .= '</tr>';
							if($cal_rss == 1) {
								$html .= '<tr style="background-color:#F0F0F0;line-height:20px;border-top:1px dashed #000;">';
									$html .= '<td colspan="2"><div style="float:right;font-size:0.8em;">RSS options</div>Title<br /><input type="text" name="event_title" id="event_title" style="width:350px;" value="'.$event_title.'" /></td>';
								$html .= '</tr>';
								$html .= '<tr style="background-color:#F0F0F0;line-height:20px;border-bottom:1px dashed #000;">';
									$html .= '<td>RSS: <input type="checkbox" id="event_rss" value="1" '.$event_rss.' /></td>';
									$html .= '<td align="right">Link: <input type="text" id="event_link" style="width:200px;" value="'.$event_link.'" /></td>';
								$html .= '</tr>';
							}
							$html .= '<tr>';
								$html .= '<td colspan="2"><textarea name="cal_event" id="cal_event" style="width:350px;height:120px;">'.convert_line_breaks($e).'</textarea></td>';
							$html .= '</tr>';
						$html .= '</table>';
					$html .= '</form>';
				$html .= '</div>';
				
				$sc = '<script type="text/javascript">document.forms["set_event"].elements["cal_event"].focus();</script>';
				
				echo $html.$sc;
				
				break;
				
			default:
			
				echo 'default...';
				break;
			
		}
	}
}


if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {
		// validate action
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);


		$cal = new Calendar();

		
		// switch action 
		switch ($action) {

				
			case 'calendar_set_events':	
			
				$arr = explode('_',$_POST['catcal_id']);
				$calendar_categories_id = $arr[0];
				$event_date = $arr[1];					
				$event = trim($_POST['cal_event']);
				$event_title = trim($_POST['event_title']);
				$event_rss = filter_input(INPUT_POST, 'event_rss', FILTER_VALIDATE_INT) ? $_POST['event_rss'] : 0;
				$event_link = trim($_POST['event_link']);
				$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$utc_modified = $utc_created;
				$result = $cal->setEvents($calendar_categories_id, $event_date, $event, $event_title, $event_rss, $event_link, $utc_created, $utc_modified);
				
				if($result) {
					$history = new History();
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history->setHistory($calendar_categories_id, 'calendar_categories_id', 'INSERT', describe('event', $event_date .' : '.$event), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
				}
			
				break;

			case 'calendar_delete_events':	
			
				$arr = explode('_',$_POST['catcal_id']);
				$calendar_categories_id = $arr[0];
				$event_date = $arr[1];					
				$result = $cal->deleteEvents($calendar_categories_id, $event_date);
			
				break;
				

			case 'calendar_views_new':	
			
				$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$result = $cal->setNewCalendarViews($name);
				if($result) {
					echo $result;
					
					$history = new History();
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history->setHistory($result, 'calendar_views_id', 'INSERT', describe('name', $name), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
				}
			
				break;
				
				
			case 'calendar_categories_new':	
			
				$category = filter_var(trim($_POST['category']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$result = $cal->setNewCalendarCategories($category);
				if($result) {
					echo $result;

					$history = new History();
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history->setHistory($result, 'calendar_categories_id', 'INSERT', describe('category', $category), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
				}
			
				break;


			case 'calendar_views_save':	
			
				$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$description = filter_var(trim($_POST['description']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$public = filter_input(INPUT_POST, 'public', FILTER_VALIDATE_INT) ? $_POST['public'] : 0;
				$active = filter_input(INPUT_POST, 'active', FILTER_VALIDATE_INT) ? $_POST['active'] : 0;
				$calendar_views_id = filter_input(INPUT_POST, 'calendar_views_id', FILTER_VALIDATE_INT) ? $_POST['calendar_views_id'] : 0;
				$result = $cal->setCalendarViews($name, $description, $active, $public, $calendar_views_id);

				if($result) {
					echo $result;
					
					$history = new History();
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history->setHistory($calendar_views_id, 'calendar_views_id', 'UPDATE', describe('name', $name), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
				}
			
				break;
				

			case 'calendar_categories_save':	
			
				$category = filter_var(trim($_POST['category']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$description = filter_var(trim($_POST['description']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$public = filter_input(INPUT_POST, 'public', FILTER_VALIDATE_INT) ? $_POST['public'] : 0;
				$rss = filter_input(INPUT_POST, 'rss', FILTER_VALIDATE_INT) ? $_POST['rss'] : 0;
				$active = filter_input(INPUT_POST, 'active', FILTER_VALIDATE_INT) ? $_POST['active'] : 0;
				$calendar_categories_id = filter_input(INPUT_POST, 'calendar_categories_id', FILTER_VALIDATE_INT) ? $_POST['calendar_categories_id'] : 0;
				$result = $cal->setCalendarCategories($category, $description, $active, $public, $rss, $calendar_categories_id);

				if($result) {
					echo $result;
					
					$history = new History();
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history->setHistory($calendar_categories_id, 'calendar_categories_id', 'UPDATE', describe('category', $category), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);					
				}
			
				break;
				

				
			case 'calendar_views_category_add':	
			
				$calendar_views_id = filter_input(INPUT_POST, 'calendar_views_id', FILTER_VALIDATE_INT) ? $_POST['calendar_views_id'] : null;
				$calendar_categories_id = filter_input(INPUT_POST, 'calendar_categories_id', FILTER_VALIDATE_INT) ? $_POST['calendar_categories_id'] : null;
				$result = null;				
				$result = $cal->setCalendarViewsCategory($calendar_views_id, $calendar_categories_id);
				echo $result;

				break;
				


			case 'calendar_views_category_delete':	
			
				$calendar_views_members = $_POST['calendar_views_members_id'];
				$calendar_views_members = explode(",",$calendar_views_members);
				if(is_array($calendar_views_members)) {
					$response = null;
					foreach($calendar_views_members as $calendar_views_members_id) {
						//echo $calendar_views_members_id .':';
						$result = null;
						$result = $cal->setCalendarViewsCategoryDelete($calendar_views_members_id);
						if($result) {
							$response .= $calendar_views_members_id .',';
						}
					}
					echo $response;
				}
				
				break;
				

				
			case 'calendar_views_delete':	
			
				$calendar_views_id = $_POST['calendar_views_id'];				
				$result = $cal->setCalendarViewsDelete($calendar_views_id);
				echo $result;
				
				if ($result) {
					$history = new History();
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history->setHistory($calendar_views_id, 'calendar_views_id', 'DELETE', describe('delete', $calendar_views_id), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);					
				}
				
				break;
				
				
				
			case 'calendar_categories_search':

				$s = $_POST['s'];
				$rows = $cal->getCalendarCategoriesSearch($s);
				echo json_encode($rows);

				break;
				

				case 'calendar_views_search':

				$s = $_POST['s'];
				$rows = $cal->getCalendarViewsSearch($s);
				echo json_encode($rows);

				break;

			case 'update_categories_position':

				$calendar_views_members_id_array = $_POST['calendar_categories_id'];
				$result = $cal->setCalendarCategoriesPosition($calendar_views_members_id_array);
			
				break;


			default:
			
				echo 'default...';
				break;
			
		}
	}
}


?>