<?php

$calendar_area = null;
if($arr['events']) {

	$calendar = new Calendar();
	$cal = $calendar->getPagesCalendar($id);
	if($cal) {
		$calendar_area = $cal['calendar_area'];
		echo '<input type="hidden" name="calendar_categories_id" id="calendar_categories_id" value="'.$cal['calendar_categories_id'].'" />';
		echo '<input type="hidden" name="calendar_views_id" id="calendar_views_id" value="'.$cal['calendar_views_id'].'" />';		
		echo '<div id="dialog_calendar" title="Calendar" style="display:none;"></div>';	
		$js_files[] = CMS_DIR.'/cms/libraries/js/pages_calendar.js';
	}
}

function lux($text) {
	$a = array(
		"english" => array("Mon" => "Mon", "Tue" => "Tue", "Wed" => "Wed", "Thu" => "Thu", "Fri" => "Fri", "Sat" => "Sat", "Sun" => "Sun", 
							"Monday" => "Monday", "Tuesday" => "Tuesday", "Wednesday" => "Wednesday", "Thursday" => "Thursday", "Friday" => "Friday", "Saturday" => "Saturday", "Sunday" => "Sunday", 
							"one day" => "one day", "four days" => "four days", "one week" => "one week", "two weeks" => "two weeks", "one month" => "one month",
							"January" => "January", "February" => "February", "March" => "March", "April" => "April", "May" => "May", "June" => "June", "July" => "July", "August" => "August", "September" => "September", "October" => "October", "November" => "November", "December" => "December",
							"w" => "w"), 
		"swedish" => array("Mon" => "må", "Tue" => "ti", "Wed" => "on", "Thu" => "to", "Fri" => "fr", "Sat" => "lö", "Sun" => "sö",
							"Monday" => "Måndag", "Tuesday" => "Tisdag", "Wednesday" => "Onsdag", "Thursday" => "Torsdag", "Friday" => "Fredag", "Saturday" => "Lördag", "Sunday" => "Söndag", 
							"one day" => "en dag", "four days" => "fyra dagar", "one week" => "en vecka", "two weeks" => "två veckor", "one month" => "en månad",
							"January" => "Januari", "February" => "Februari", "March" => "Mars", "April" => "April", "May" => "Maj", "June" => "Juni", "July" => "Juli", "August" => "Augusti", "September" => "September", "October" => "Oktober", "November" => "November", "December" => "December",
							"w" => "v"));

	$l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
	if(!$l) {
		$l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
	} 
	$s = $l ? $a[$l][$text] : $text;
	return $s;
}




?>

    <div id="wrapper-page">
    
        <div id="wrapper-content" class="column column-having-right-sidebar" style="width:75%">
            <main>
                <?php print_selection("selection-content-above", $selection_area['content_above']); ?>
                <div id="content-top-selections"></div>
                <div id="content-top-grid"><?php print_grid($arr, 1);?></div>
                <div id="content-breadcrumb"><?php print_breadcrumb($id, $arr['breadcrumb']); ?></div>
                <article>
                    <header>
                        <h1 id="content-title" class="editable"><?php echo $page_title_body .' '.$icon; ?></h1>
                        <div id="content-meta"><?php  print_meta($arr['utc_modified'], $dtz, $languages) ?></div>
                    </header>
                    <?php if ($arr['search_field_area'] == 3) { print_search_field_area_page($languages); } ?>
                    <?php if ($arr['search_field_area'] > 0) { print_search_field_result($languages); } ?>
                    <?php print_selection("selection-content-inside", $selection_area['content_inside']); ?>
                    <div id="content-html" class="editable"> 
                        <?php echo $arr['content'];?>
                    </div>

                    <div id="caledar-events">

                    <?php

                    $rowsMultiple = $calendar->getCalendarEventsMultiple([3,4], date('Y-m-d'), "2weeks");

                    if ($rowsMultiple) {

                        // variable $i to count shown events
                        $i = 0;
                        $tmpDate = "";
                        foreach($rowsMultiple as $r) {
                                        
                            if($r['event_date'] != date('Y-m-d')) {
                                
                            }
            
                            $link = (isset($r['event_link']) && filter_var($r['event_link'], FILTER_VALIDATE_URL)) ? $r['event_link'] : CMS_URL;
                            
                            if ($tmpDate != $r['event_date']) {
                                echo '<div style="width:100%;padding:0 10px 5px 10px" class="gf-green-1 border">';
                                echo '<h5>'.  lux(date('l',strtotime($r['event_date']))) .' '. strtolower(date('j',strtotime($r['event_date']))) .' '. strtolower(lux(date('F',strtotime($r['event_date'])))) .' '. $r['title'] . '</h5>';
                                echo '</div>';
                            }
                            
                            echo '<div style="width:100%;padding:5px;">';
                            echo '<h5>'. $r['category'] .'</h5  >';
                            echo '<p>'. nl2br($r['event']) .'</p>';
                            echo '</div>';
                            
                            $tmpDate = $r['event_date'];
                            
                            // increase count
                            $i++;
                            
                        }
                    }    
        
                    ?>
                    </div>
                    <footer>
                        <div id="content-author-leading"><?php print_author($arr['content_author'], $languages)?></div>
                        <div id="content-author" class="editable_row"><?php echo $arr['content_author']; ?></div>
                        <div id="content-social-network"></div>
                    </footer>
                </article>
                <div id="content-bottom-grid"><?php print_grid($arr, 2);?></div>
                <aside id="content-bottom-widgets">
                    <?php if($rows_widgets){ show_widgets_content($rows_widgets, "widgets_content", $wrapper_content_width);} ?>
                </aside>
                <aside id="content-bottom-stories">
                </aside>
                <?php print_selection("selection-content-below", $selection_area['content_below']); ?>
            </main>
        </div>

        <div id="wrapper-right-sidebar" class="column" style="width:25%;min-height:1px">
            <?php print_selection("selection-right-sidebar-top", $selection_area['right_sidebar_top']); ?>
            <?php
            echo '<nav id="nav-site-navigation-vertical">';
            if (!isset($_GET['sample'])) {
                get_pages_tree_sitemap($id, $path=get_breadcrumb_path_array($id), $seo=true, $href, $open, $show_pages_id = false, $parent_id, $a=true, $a_add_class=false, $depth=0);
            }
            echo '</nav>';

            if($calendar_area == 'right-sidebar') {
                echo '<div id="left-sidebar-calendar" class="calendar-column">';
                    get_calendar('right-sidebar', $cal, $id);
                echo '</div>';
            }
            
            ?>
            <aside id="right-sidebar-widgets"><?php if($rows_widgets){ 
                show_widgets_content($rows_widgets, "widgets_right_sidebar", $wrapper_right_sidebar_width=300);} ?></aside>
            <aside id="right-sidebar-stories">
            </aside>
            <?php print_selection("selection-right-sidebar-bottom", $selection_area['right_sidebar_bottom']); ?>
        </div>

    </div>