<?php
// include core
include_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';

$pages = new Pages();

// overall
if (isset($_POST['token']) || isset($_GET['token'])){
	// only accept this $_SESSION['token']
	$req =  isset($_POST['token']) ? $_POST['token'] : $_GET['token'];
	
	if ($req == $_SESSION['token'])  {

		$action = filter_var(trim($_REQUEST['action']), FILTER_SANITIZE_STRING);
		
		switch ($action) {
		
			case 'pages_search':

				$s = $_POST['s'];
				$rows = $pages->getPagesSearch($s);
				echo json_encode($rows);

			break;

			case 'preview_theme':

				$theme = $_POST['theme'];
				$_SESSION['site_theme'] = $theme;
				
			break;
			
			case 'pages_search_extended2':
				$s = isset($_POST['s']) ? trim($_POST['s']) : "";	

				//echo $s;
				//write_debug($s);
				$rows = $pages->getPagesSearchWordsRelevance2($s, 2);
				echo json_encode($rows);
				//echo $rows;
			break;
			
			case 'pages_search_extended':

				
				$s = isset($_POST['pages_s']) ? trim($_POST['pages_s']) : "";	
				if(isset($_POST['again'])) {
					$s = $_POST['again'] == "true" ? trim($_POST['pages_s_again']) : $s;
				}
				if(strlen($s) > 0) {
					$rows = $pages->getPagesSearchWordsRelevance($s, 2);
					
					$a = explode(" ", $s);

					echo '<div class="search-result"><input type="text" id="pages_s_again" class="search" value="'.$s.'"><button id="btn_pages_search_again" class="magnify">'.translate("Search", "site_search", $languages).'</button><span id="ajax_spinner_pages_search" style="display:none;"><img src="/glimnet/cms/css/images/spinner_1.gif" alt="spinner" /></span><p>'.translate("Found", "site_search_found", $languages).' '.count($rows).' '.translate("pages", "site_search_found_pages", $languages).'</p></div>';
					
					if($rows) {
						
						foreach($rows as $row) {
							
							$heading = isValidString($a[0], 'characters') ? highlight($row['title'], $a) : $row['title'];
							$html = '<div>';
							$html .= '<a href="pages.php?id='.$row['pages_id'].'" class="search"><h3>'.$heading.'</h3></a>';
							$html .= '<abbr class="timeago" title="'.get_utc_dtz($row['utc_modified'], $dtz, 'Y-m-d H:i:s').'">'.get_utc_dtz($row['utc_modified'], $dtz, 'Y-m-d H:i:s').'</abbr>';
							
							$text = substr(strip_tags($row['content']),0,300);
							$text = substr($text,0,strrpos($text," ")) . ' ...';
							$text_highlighted = isValidString($a[0], 'characters') ? highlight($text, $a) : $text;
							
							$html .= '<p>'.$text_highlighted.'</p>';
							$html .= '</div>';
							echo $html;
						}
						
					}
					echo '<div class="search-result"></div>';
					
				} 

			break;

			case 'pages_tag':

				$s = $_POST['s'];
				$tags = new Tags();
				$rows = $tags->getTagsSearchLike($s);
				echo json_encode($rows);

			break;
				
			case 'pages_selections_search':

				$s = $_POST['s'];
				
				$selections = new Selections();
				$rows = $selections->getSelectionsSearch($s);
				echo json_encode($rows);

			break;
				
			case 'pages_history':

				$history = new History();
				$field_id = $_POST['pages_id'];
				$field = 'pages_id';
				$action = 'UPDATE';
				$rows = $history->getHistory($field_id, $field, $action, 1);
				if($rows) {
					echo '<span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span> ';
					foreach ($rows as $row) {
						echo get_utc_dtz($row['utc_datetime'], $dtz, 'Y-m-d H:i:s');
						echo ', ';
						echo $row['name'];
						echo '<br />';
					}
				}

			break;
				
			case 'pages_history_extended':

				$history = new History();
				$field_id = $_POST['pages_id'];
				$field = 'pages_id';
				$rows = $history->getHistoryAll($field_id, $field, 50);
				if($rows) {
					echo 'Last updated:<br />';
					foreach ($rows as $row) {
						echo get_utc_dtz($row['utc_datetime'], $dtz, 'Y-m-d H:i:s');
						echo ', by ';
						echo $row['name'];
						echo ' | ';
						echo $row['description'];
						echo '<br />';
					}
				}

			break;
				

			case 'site_feed':
			
				$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
				
				if(isset($pages_id)) {

					$rows = false;
					// randomize
					$input = array("stories", "stories_events", "events");
					shuffle($input);
					
					if(isset($_SESSION['site_feed'])) {
						$method = ($_SESSION['site_feed'] == 'random') ?  $input[0]  : $_SESSION['site_feed'];
					} else {
						die;
					}
					$a = array();
					switch($method) {
						
						case 'stories':

							$rows = $pages->getPagesStoriesFeed();
							if($rows) {
								$i = 0;
								foreach($rows as $row) {
									$end = strlen($row['story_content']) > 75 ? ' ...' : null;
									$part = mb_substr($row['story_content'],0,75);
									$part = strip_tags($part);
									$space_pos = $end ? mb_strrpos($part," ") : 75;
									$a[$i]['link'] = CMS_DIR.'/cms/pages.php?id='.$row['pages_id'];
									$a[$i]['title'] = $row['title'];
									$a[$i]['content'] = mb_substr($part,0,$space_pos).$end;
									$i++;
								}
							}
						break;

						case 'stories_events':

							$rows = $pages->getPagesStoriesEvents();
							if($rows) {
								$i = 0;
								foreach($rows as $row) {
									$end = strlen($row['story_content']) > 75 ? ' ...' : null;
									$part = mb_substr($row['story_content'],0,75);
									$part = strip_tags($part);
									$space_pos = $end ? mb_strrpos($part," ") : 75;
									$a[$i]['link'] = CMS_DIR.'/cms/pages.php?id='.$row['pages_id'];
									$a[$i]['title'] = $row['title'];
									$a[$i]['content'] = mb_substr($part,0,$space_pos).$end;
									$i++;
								}
							}
						break;
 
						case 'events':

							$calendar = new Calendar;
							$rows = $calendar->getCalendarEventsFeed();
							if($rows) {
								$i = 0;
								foreach($rows as $row) {
									$end = strlen($row['event']) > 75 ? ' ...' : '';
									$part = mb_substr($row['event'],0,75);
									$part = strip_tags($part);
									$space_pos = $end ? mb_strrpos($part," ") : 75;
									$a[$i]['link'] = 'javascript:calendar_preview('.$row['calendar_categories_id'].')';
									$a[$i]['title'] = $row['category'] .' | '.$row['event_date'];
									$a[$i]['content'] = mb_substr($part,0,$space_pos).$end;
									$i++;
								}
							}
							
						break;
					
					}
					if($a){
						echo json_encode($a);	
					}
				}
				
			break;
				
				
			case 'calendar_events':
		
				$calendar_categories_id = filter_input(INPUT_POST, 'calendar_categories_id', FILTER_VALIDATE_INT) ? $_POST['calendar_categories_id'] : 0;
				$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : 0;
				$date = filter_var(trim($_POST['date']), FILTER_SANITIZE_STRING);

				$calendar = new Calendar();
				$d = null;
				if(isset($_GET['date'])) {
					$d = $_GET['date'];
					$q = exclude_queries(array('date'));
					echo $calendar->getCalendarNavigation($date, $href="pages.php?".$q."&date=", $max_width=true);
				} else {
					echo $calendar->getCalendarNavigation($date, $href="pages.php?id=".$pages_id."&date=", $max_width=true);
				}
				
				$cal = $calendar->getPagesCalendar($pages_id);
				
				if(is_array($cal)) {
					$calendar_categories_id = $cal['calendar_categories_id'];
					$period = $cal['period_initiate'];
					$calendar_views_id = $cal['calendar_views_id'];
					$calendar_show = $cal['calendar_show'];
				} else {
					die;
				}
				
				echo '<div class="calendar-events-container">';
					echo $calendar->getCalendarEventsList($calendar_categories_id, $date, $href=null, $period);
				echo '</div>';
				
			break;

			
			
			case 'calendar_load':
							
				$cal = new Calendar();

				$date = filter_var(trim($_POST['date']), FILTER_SANITIZE_STRING);
				$date = (isValidDate($date)) ? $date : null;				
				$period = filter_var(trim($_POST['period']), FILTER_SANITIZE_STRING);
				$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : 0;
				$calendar_categories_id = filter_input(INPUT_POST, 'calendar_categories_id', FILTER_VALIDATE_INT) ? $_POST['calendar_categories_id'] : 0;
				$calendar_views_id = filter_input(INPUT_POST, 'calendar_views_id', FILTER_VALIDATE_INT) ? $_POST['calendar_views_id'] : 0;				

				if($calendar_categories_id > 0) {
					echo $cal->getCalendarCategoriesRights($date, $href=null, $calendar_categories_id, $period);
				} else {
					echo $cal->getCalendarViewsRights($date, $href=null, $calendar_views_id, $period);
				}
				
			break;
			
			
			case 'event_stories':
		
				$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : 0;
				$search = filter_var(trim($_POST['search']), FILTER_SANITIZE_STRING);
				$date = filter_var(trim($_POST['datetrigger']), FILTER_SANITIZE_STRING);
				$period = filter_var(trim($_POST['period']), FILTER_SANITIZE_STRING);
				$rows_event_stories = $pages->getPagesStoryContentPublishEvent($search, $date, $period);
				
				$rows_event_stories_sorted = $period == 'previous' ? krsort($rows_event_stories) : $rows_event_stories;				
				get_box_story_events($rows_event_stories, $content_width=474, $stories_wide_teaser_image_align="", $stories_wide_teaser_image_width="", $arr['stories_last_modified']=0, $dtz);
				
			break;

	
			
			
			case 'widget_images':

				if ($pages_widgets_id = filter_input(INPUT_GET, 'pages_widgets_id', FILTER_VALIDATE_INT)) { 
					$tag = isset($_GET['tag']) ? filter_var(trim($_GET['tag']), FILTER_SANITIZE_STRING) : '';
					$width = intval($_GET['width']);
					$rows = $pages->getPagesImagesSlideshowFeed($pages_widgets_id, $tag);
					if($rows) {
											
						$image = new Image();
						foreach($rows as $key => $value) {

							// biggest possible							
							$filename_and_path = CMS_DIR.'/content/uploads/pages/'. $value['pages_id'] .'/'. $value['filename'];							
							$filename = $image->get_optimzed_image($filename_and_path, $width);
							$rows[$key]['filename'] = $filename; 
						}						

						echo json_encode($rows);
					}
				}
			
			break;
			
			case 'getStories':
		
				$number = filter_input(INPUT_GET, 'number', FILTER_VALIDATE_INT) ? $_GET['number'] : 0;
				$tag = filter_var(trim($_GET['tag']), FILTER_SANITIZE_STRING);
				$a = array();
				

				$rows = $pages->getPagesStoryContentPublishPromoted($tag, $number);
				if($rows) {
					$i = 0;
					foreach($rows as $row) {
						$a[$i]['title'] = $row['title'];
						$a[$i]['filename'] = $row['pages_id'] ."/". $row['filename'];
						$i++;
					}
				}					
				echo json_encode($a);
					
			break;			

		}
	}			
}