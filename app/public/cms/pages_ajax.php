<?php
// include core
include_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';

$pages = new Pages();

if (isset($_POST['token']) || isset($_GET['token'])){

	$req =  isset($_POST['token']) ? $_POST['token'] : $_GET['token'];
	
	if ($req == $_SESSION['token'])  {

		$action = filter_var(trim($_REQUEST['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		
		switch ($action) {
		
			case 'accept_cookies':

				$boolean = $_POST['accept_cookies'] == 'true' || $_POST['accept_cookies'] == 1 ? true : false;
				$_SESSION['accept_cookies'] = $boolean;
				echo $boolean;

			break;

			case 'pages_search':

				$s = $_POST['s'];
				$rows = $pages->getPagesSearch($s);
				echo json_encode($rows);

			break;

			case 'preview_theme':

				$theme = $_POST['theme'];
				$_SESSION['site_theme'] = $theme;
				
			break;
			
			case 'pages_search_extended_summary':
				$s = isset($_POST['s']) ? trim($_POST['s']) : "";
				$pages_id = isset($_POST['pages_id']) ? trim($_POST['pages_id']) : 0;
				$limit_tree = isset($_POST['limit_tree']) ? $_POST['limit_tree'] : 0;
				$rows = $pages->getPagesSearchWordsRelevanceSummary($s, $status=2, $pages_id, $limit_tree);
				//write_debug(json_encode($rows));
				echo json_encode($rows);
			break;
			

			case 'pages_search_extended':
				$s = isset($_POST['s']) ? trim($_POST['s']) : "";	
				$pages_id = isset($_POST['pages_id']) ? trim($_POST['pages_id']) : 0;
				$limit_tree = isset($_POST['limit_tree']) ? $_POST['limit_tree'] : 0;
				$limit_start = intval($_POST['limit_start']);
				$limit = 10;
				$rows_total = $pages->getPagesSearchWordsRelevanceCount($s, $status=2, $pages_id, $limit_tree);
				$rows = $pages->getPagesSearchWordsRelevance($s, 2, $pages_id, $limit_tree, $limit_start, $limit);

				$a = explode(" ", $s);
				$html = "";
				$element_last_result = '<span id="search-end"></span>'; 
				if($rows) {
					$html .= '<div class="search-result">'.translate("Show results for ", "site_show_results_for", $languages).' <strong>'.$s.'</strong></p><input type="hidden" id="search_results_total" value="'.$rows_total[0]['total'].'"></div>';
					$counter = $limit_start + 1;
					foreach($rows as $row) {
						//$heading = isValidString($a[0], 'characters') ? highlight($row['title'], $a) : $row['title'];
						$html .= '<div>';
						$html .= '<a href="pages.php?id='.$row['pages_id'].'" class="search"><h3>'.$row['title'].'</h3></a>';
						$html .= '<span>'.$row['category'].'</span>';
						$text = substr(strip_tags($row['content']),0,300);
						$text = substr($text,0,strrpos($text," ")) . ' ...';
						//$text_highlighted = isValidString($a[0], 'characters') ? highlight($text, $a) : $text;
						$html .= '<p>'.$text.'</p>';
						$html .= '</div>';
						$counter++;
					}
				} else {
					$html .= '<div class="search-result">'.translate("No results for ", "site_no_results_for", $languages).' <strong>'.$s.'</strong></p></div>';
				}
				echo $html;


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
				$date = filter_var(trim($_POST['date']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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
					echo $calendar->getCalendarEventsList($calendar_categories_id, $date, $period, $href=null);
				echo '</div>';
				
			break;

			
			
			case 'calendar_load':
							
				$cal = new Calendar();

				$date = filter_var(trim($_POST['date']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$date = (isValidDate($date)) ? $date : null;				
				$period = filter_var(trim($_POST['period']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
				$search = filter_var(trim($_POST['search']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$date = filter_var(trim($_POST['datetrigger']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$period = filter_var(trim($_POST['period']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$rows_event_stories = $pages->getPagesStoryContentPublishEvent($search, $date, $period);
				
				$rows_event_stories_sorted = $period == 'previous' ? krsort($rows_event_stories) : $rows_event_stories;				
				get_box_story_events($rows_event_stories, $content_width=474, $stories_wide_teaser_image_align="", $stories_wide_teaser_image_width="", $arr['stories_last_modified']=0, $dtz);
				
			break;

	
			
			
			case 'widget_images':

				if ($pages_widgets_id = filter_input(INPUT_GET, 'pages_widgets_id', FILTER_VALIDATE_INT)) { 
					$tag = isset($_GET['tag']) ? filter_var(trim($_GET['tag']), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
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
			


			case 'widget_show_page':
			
				$ids = filter_var(trim($_GET['ids']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$tag = filter_var(trim($_GET['tag']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$search = filter_var(trim($_GET['search']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ? $_GET['limit'] : 0;
				$pages_id = filter_input(INPUT_GET, 'pages_id', FILTER_VALIDATE_INT) ? $_GET['pages_id'] : 0;
				
				$ids = explode(",", $ids);

				$ids = is_array($ids) ? $ids : array(); 
				if ($limit > count($ids)) {
					// search
					$row = $pages->getPagesContent($pages_id);
					$words = "";
					if($row) {
						$words .= $row['tag'] . ",";
						$words .= $row['meta_keywords'];
					}
					$words = str_replace(","," ",$words);
					$words = $row['title'] . " " . $words;
					$rows = $pages->getPagesSearchWordsRelevanceSummary($words, $status=2, $pages_id=0, $limit_tree=0);
					
					if ($rows) {
						foreach($rows as $row) {
							array_push($ids, $row['pages_id']);	
						}
					}
				}

				$ids = array_diff($ids, array($pages_id));
				$ids = array_unique($ids);
				$ids = array_slice($ids, 0, $limit);

				$ids = implode(",", $ids);
				$ids = trim($ids,",");
				$rows = $pages->getPagesContentPublishSelected($ids);
				if ($rows) {
					echo json_encode($rows);
				}

			break;



			case 'getStories':
		
				$number = filter_input(INPUT_GET, 'number', FILTER_VALIDATE_INT) ? $_GET['number'] : 0;
				$tag = filter_var(trim($_GET['tag']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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