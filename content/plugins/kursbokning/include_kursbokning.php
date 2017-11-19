<?php

// show title and content before plugin

echo "\n".'<div id="content-top" style="width:100%;">';

// selections above
if(is_array($pages_selections)) {
	echo "\n".'<div id="wrapper-site-selection-content-above" style="width:100%;">';
		echo $selections_content_above;
	echo "\n".'</div>';
}					

	if($id != 0) {
		echo "\n".'<div id="content-breadcrumb" style="float:left;width:90%;padding-bottom:10px;">';
		if(filter_var($id, FILTER_VALIDATE_INT)) {
			if($arr['breadcrumb'] > 0) {
				echo get_breadcrumb($id," &raquo ", 25, $clickable=true);
			}
			if($arr['breadcrumb'] == 2) {
				echo get_breadcrumb_children($id, $type="select");
			}
			if($arr['breadcrumb'] == 3) {
				echo get_breadcrumb_children($id, $type="ul");
			}
		}
		echo "\n".'</div>';
		echo "\n".'<div id="content-links" style="float:right;width:10%;text-align:right;">';
		
		//default access rights
		$acc_create = $acc_edit = $acc_read = false;
		if(isset($_SESSION['users_id'])) {
			if(get_role_CMS('editor') == 1) {
				$acc_create = $acc_edit = true;
			} else {
			
				//check role_CMS author & contributor
				if(get_role_CMS('author') == 1 || get_role_CMS('contributor') == 1 ) {
					// user rights to this page
					$pages_rights = new PagesRights();
					$users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;
					$users_rights = $pages_rights->getPagesUsersRights($id, $users_id);
					// groups rights to this page
					$groups_rights = $pages_rights->getPagesGroupsRights($id);
					
					// create
					if($users_rights) {
						if($users_rights['rights_create'] == 1) {
							$acc_create = true;
						}
					} else {
						if($groups_rights) {												
							if(get_membership_rights('rights_create', $_SESSION['membership'], $groups_rights)) {
								$acc_create = true;
							}
						}
					}
					
					// edit
					if($users_rights) {
						if($users_rights['rights_edit'] == 1) {
							$acc_edit = true;
						}
					} else {
						if($groups_rights) {												
							if(get_membership_rights('rights_edit', $_SESSION['membership'], $groups_rights)) {
								$acc_edit = true;
							}
						}
					}
					
				}
			}
		}
		
		if(isset($id)) {
			if($acc_create) {
				echo '<a href="'.CMS_DIR.'/cms/admin.php?t=pages&tp=add&id='. $id .'" class="colorbox_edit"><span class="ui-icon ui-icon-document" style="display:inline-block;" title="'.translate("Add child page", "pages_add_childpage", $languages).'"></span></a>';
				echo '<div style="display:none;" id="acc_create">inline</div>';
			}
			if($acc_create && $acc_edit) {
				echo ' ';
			}
			if($acc_edit) {
				echo '<a href="'.CMS_DIR.'/cms/pages_edit.php?id='. $id .'" class="colorbox_edit"><span class="ui-icon ui-icon-pencil" style="display:inline-block;" title="'.translate("Edit", "pages_edit", $languages).'"></span></a>';
				echo '<div style="display:none;" id="acc_edit">inline</div>';
			}
		}
		
		echo "\n".'</div>';
	}
	echo '<input type="hidden" name="token" id="token" value="'.$_SESSION['token'].'" />';
echo "\n".'</div>';


echo "\n".'<div id="content-title" style="clear:both;">';
	$icon = $arr['access'] == 2 ? '' : '<span class="ui-icon ui-icon-key" style="display:inline-block;"></span>';
	if($id == 0) { $icon = '';}
	echo '<h1>'. $arr['title'] .' '.$icon.'</h1>';
echo "\n".'</div>';

echo "\n".'<div id="content-meta">';
	if(isset($arr['utc_start_publish'])) {							
		echo '<abbr class="timeago">'. translate("Modified", "pages_status_modified", $languages) .'</abbr> <abbr class="timeago" title="'.get_utc_dtz($arr['utc_modified'], $dtz, 'Y-m-d H:i:s').'">'.get_utc_dtz($arr['utc_modified'], $dtz, 'Y-m-d H:i:s').'</abbr>';							
		echo '&nbsp;<img class="ajax_history" id="'.$id.'" src="'.CMS_DIR.'/cms/css/images/icon_info.png" style="vertical-align:top;" width="11px" height="11px" />&nbsp;';
		echo '&nbsp;<span id="ajax_spinner_history" style=display:none><img src="'.CMS_DIR.'/cms/css/images/spinner_1.gif"></span>';
		echo '<div id="ajax_status_history" style=display:none></div>';
	}
echo "\n".'</div>';


// selections inside
if(is_array($pages_selections)) {
	echo $selections_content_inside;
}					

echo "\n".'<div id="content-html">';
	echo $arr['content'];
echo "\n".'</div>';

echo "\n".'<div id="content-author">';
echo "\n".'</div>';




////////////////////////////////////////////////////////////////////////////////////////////////////
// plugin
////////////////////////////////////////////////////////////////////////////////////////////////////

// prepare id
$plugin_kursbokning_id = null;
// parse arguments
parse_str($plugin_arguments, $plugin_argument);
// check if we have a value
if($plugin_argument) {
	$plugin_kursbokning_id = $plugin_argument['plugin_kursbokning_id'];
}

// open
$kursbokning = new Kursbokning();

$rows_forms = $kursbokning->getKursbokningWebbPublicering($plugin_kursbokning_id);
//print_r2($rows_forms);
$courses_table = '';

if($rows_forms) {
	$courses_table = '<table class="courses_view" style="width:474px;">';
		foreach($rows_forms as $rows_form) {
			$courses_table .= '<tr>';
				
				
				$courses_table .= '<td colspan="2" style="padding:20px 0 10px; 0;vertical-align:top;"><a href="" onclick="open_window('.$rows_form['plugin_kursbokning_id'].')"><span style="font-size:1.5em;cursor:pointer;padding-left:5px;">'.$rows_form['title'].'</span>';
				//$courses_table .= '<td colspan="2" style="padding:20px 0 10px; 0;vertical-align:top;"><a class="colorbox_view" href="'.CMS_DIR.'/cms/plugins/kursbokning/kursbokning.php?id='.$rows_form['plugin_kursbokning_id'].'"><span style="font-size:1.5em;cursor:pointer;padding-left:5px;">'.$rows_form['title'].'</span>';
				$courses_table .= ' ('.mb_strtolower($rows_form['type'],'UTF-8').')';
				$courses_table .= '<span class="ui-icon ui-icon-mail-closed" style="display:inline-block;"></a></td>';
				$courses_table .= '<td style="padding:5px;text-align:right;vertical-align:bottom;width:75px;">Start</td>';
			$courses_table .= '</tr>';

			
			
			$rows_courses = $kursbokning->getKursbokningIdKurserWebbPublicering($rows_form['plugin_kursbokning_id']);				
			//$rows_courses = $kursbokning->getKursbokningIdKurser($rows_form['plugin_kursbokning_id']);				
			//print_r2($rows_courses);
			if($rows_courses) {

				$odd_even = 'even';
				foreach($rows_courses as $rows_course) {
				
					$rows = $kursbokning->getKursbokningKurserAnmalanCount($rows_course['plugin_kursbokning_kurs_id']);
					$utc_date_start = ($rows_course['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($rows_course['utc_start'], $dtz, 'Y-m-d H:i:s')) : null;
					$utc_date_end = ($rows_course['utc_end']>'2000-01-01 00:00') ? new DateTime(utc_dtz($rows_course['utc_end'], $dtz, 'Y-m-d H:i:s')) : null;
					
					$odd_even = ($odd_even=='even') ?  'odd' : 'even';
					
					$courses_table .= '<tr class="'.$odd_even.'">';
						$courses_table .= '<td style="width:10px;"><span class="ui-icon ui-icon-carat-1-e" style="display:inline-block;"></td>';
						$utc_date_start = ($rows_course['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($rows_course['utc_start'], $dtz, 'Y-m-d H:i:s')) : null;
						$courses_table .= '<td><span class="course_title" style="font-family:Georgia, serif;font-size:1.4em;color:navy;cursor:pointer;">'.$rows_course['title'].'</td>';
						$courses_table .= '<td style="text-align:right;vertical-align:top;">';
						if($utc_date_start) { 
							$courses_table .= $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y');
						}
						$courses_table .= '</td>';					
					$courses_table .= '</tr>';
					$courses_table .= '<tr style="display:none;">';

						$courses_table .= '<td colspan="3" style="padding:0 0 0 20px;">';
						
						switch($rows_course['type']) {
							case 'Folkhögskolekurs':
								$courses_table .= '<table style="width:100%;">';
									$courses_table .= '<tr>';
										$courses_table .= '<td style="width:33%;padding:5px;">';
										$courses_table .= '<b>Plats:</b> '.$rows_course['location'];
										$courses_table .= '</td>';
										$colspan = 3;
										if($rows_course['show_participants'] == 1 ) {
											$colspan = 2;
											$courses_table .= '<td style="text-align:right;padding:5px;">';
											$courses_table .= '<b>Deltagare:</b> '.$rows_course['participants'];
											$courses_table .= '</td>';
										}
										$courses_table .= '<td style="text-align:right;padding:5px;">';
										$courses_table .= '<a href="" onclick="open_window('.$rows_form['plugin_kursbokning_id'].')"><b>Ansök till kurs&nbsp;&raquo;</b></a>';
										$courses_table .= '</td>';
									$courses_table .= '</tr>';
									$courses_table .= '<tr>';
										$courses_table .= '<td colspan="'.$colspan.'" style="padding:5px;"><b>Mer info:</b><br />';
										$courses_table .= nl2br($rows_course['description']).'<hr />';
										$courses_table .= '</td>';
									$courses_table .= '</tr>';
								$courses_table .= '</table>';
							break;
							
							case 'Uppdragsutbildning':
								$courses_table .= '<table style="width:100%;">';
									$courses_table .= '<tr>';
										$courses_table .= '<td style="width:33%;padding:5px;">';
										$courses_table .= '<b>Plats:</b> '.$rows_course['location'];
										$courses_table .= '</td>';
										
										$colspan = 3;
										if($rows_course['show_participants'] == 1 ) {
											$colspan = 2;										
											$courses_table .= '<td style="text-align:right;padding:5px;">';
											$courses_table .= '<b>Deltagare:</b> '.$rows_course['participants'];
											$courses_table .= '</td>';
										}
										$courses_table .= '<td style="width:33%;text-align:right;padding:5px;">';
										$courses_table .= '<b>Pris</b>: '.$rows_course['cost'];
										$courses_table .= '</td>';
									$courses_table .= '</tr>';
									$courses_table .= '<tr>';

										$dts = '';
										if($utc_date_end) {
											if($utc_date_start->format('Y')==$utc_date_end->format('Y')) {
												$dts = $utc_date_start->format('Y-m-d')==$utc_date_end->format('Y-m-d') ? $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y') : $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
											} else {
												$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y').' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
											}
										} else {
											$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y');
										}
										
										$courses_table .= '<td style="width:33%;padding:5px;">'.$dts.'</td>';
										
									$courses_table .= '</tr>';
									$courses_table .= '<tr>';


										$courses_table .= '<td colspan="'.$colspan.'" style="padding:5px;">';
										if ($rows_course['participants'] > $rows['count']) {
											$courses_table .= '<a href="" onclick="open_window('.$rows_form['plugin_kursbokning_id'].')"><b>Boka kurs&nbsp;&raquo;</b></a>';
										} else {
											$courses_table .= '<span class="participants_max">Kursen är fullbokad. Kontakta oss för mer information.</span>';
										}
										$courses_table .= '</td>';										
									$courses_table .= '</tr>';
									$courses_table .= '<tr>';
										$courses_table .= '<td colspan="'.$colspan.'" style="padding:5px;"><b>Mer info:</b><br />';
										$courses_table .= nl2br($rows_course['description']).'<hr />';
										$courses_table .= '</td>';
									$courses_table .= '</tr>';
								$courses_table .= '</table>';
							break;	
							
							case 'Evenemang':
								$courses_table .= '<table class="courses_list evenemang" style="width:100%;">';		
									$courses_table .= '<tr>';
										$courses_table .= '<td>'.$rows_course['location'].'</td>';
										$courses_table .= '<td style="width:40%;text-align:right;">';
										
										if ($rows_course['participants'] > $rows['count']) {
											$courses_table .= '<a href="" onclick="open_window('.$rows_form['plugin_kursbokning_id'].')"><b>Boka evenemang&nbsp;&raquo;</b></a>';
										} else {
											$courses_table .= '<span class="participants_max">Evenemanget är fullbokat. Kontakta oss för mer information.</span>';
										}
										
										$courses_table .= '</td>';
	
									$courses_table .= '</tr>';								
									$courses_table .= '<tr>';
										$courses_table .= '<td><span class="course_title events">'.$rows_course['title'].'</span></td>';
										$courses_table .= '<td class="right" style="text-align:right;">'.$rows_course['cost'].'</td>';
									$courses_table .= '</tr>';
									
									$courses_table .= '<tr style="display:;">';
										$courses_table .= '<td colspan="5" class="kurs"><div style="margin-top:10px;padding-bottom:10px;border-bottom:1px dashed grey;"><b>Mer info</b><br /> '.nl2br($rows_course['description']).'</div></td>';
									$courses_table .= '</tr>';
									
								$courses_table .= '</table>';
							break;					
						}
			
						$courses_table .= '</td>';
					$courses_table .= '</tr>';
				}
				
			}
			
		}
	$courses_table .= '</table>';
}
echo $courses_table;		
?>


<script>
	
	var cms_dir = "<?php echo CMS_DIR; ?>";
	function open_window(id) {
		w=window.open(cms_dir+'/content/plugins/kursbokning/kursbokning.php?id='+id,'','width=1280,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
		w.focus();
	}

	$(document).ready(function() {

		$('.course_title').click(function(){
			$(this).closest('tr').next('tr').fadeToggle();
		});
				
		$(".colorbox_view").colorbox({
			width:"1260px", 
			height:"96%", 
			transition:"none",
			iframe:true, 
			onClosed:function(){ 
			}
		});		
		
	});

</script>
	