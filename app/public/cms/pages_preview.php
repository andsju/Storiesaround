<?php
// include core 
require_once 'includes/inc.core.php';

if(!isset($_SESSION['site_id'])) {
	die;
}

if($_SESSION['site_maintenance'] == 1 && $_SESSION['role_CMS'] < 4) {
	header('Location: maintenance.php');
}

// check $_GET id 
$id = array_key_exists('id', $_GET) ? $_GET['id'] : null;

// seo friendly url 
$request = substr($_SERVER['REQUEST_URI'],strlen(CMS_DIR.'/'));
$path_parts = explode('/', $request);

// array $path_parts now holds: [0] htaccess rewrite folder | [1] seo title 
if(!($id)) {
	$id = $path_parts[1];
}

// initiate class
$pages = new Pages();

// try to find pages_id
if(isset($id)) {

	$id = filter_var($id, FILTER_VALIDATE_INT) ? $id : filter_var($id, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	// alphanumeric seo link, get id	
	if(!is_numeric($id)) {
		$seo_link = $id;
		$result = $pages->getPagesSeo($seo_link);
		if($result) {
			$id = $result['pages_id'];
		}
	}
}

if(!$id) {
	//redirect to site url causes a loop problem if site_domain_url is missing
	if(isset($_SESSION['site_domain_url'])) {
		if($_SESSION['site_domain_url'] != CMS_URL) {
			//header('Location: '. $_SESSION['site_domain_url']);
			exit;
		}
	}
}

//access page as known or unknown user to get page content
$users_id = (isset($_SESSION['users_id'])) ? $_SESSION['users_id'] : null;

// get page content
$arr = $pages->getPagesContentPreview($id);

// check if content can be seen by current user
switch ($arr['access']) {
	case 0: // logged in users must have read rights
		// default
		$acc_read = false;
		if($users_id) {
			if(!get_role_CMS('author') == 1) {
				// user rights to this page
				$pages_rights = new PagesRights();
				$users_rights = $pages_rights->getPagesUsersRights($id, $users_id);
				// groups rights to this page
				$groups_rights = $pages_rights->getPagesGroupsRights($id);		
				
				if($users_rights['rights_read'] == 1) {
					$acc_read = true;
				} else {
					// groups rights membership
					if(get_membership_rights('rights_read', $_SESSION['membership'], $groups_rights)) {
						$acc_read = true;
					}
				}
			} else {
				$acc_read = true;
			}
		}
		if($acc_read==false) {
			$arr = null;
		}
		break;
	case 1: // logged in users
		$arr = isset($users_id) ? $arr : null;
		break;
	
	case 2: // everyone
		break;
}

// handle templates and sample views
$sample = false;

if($arr == null) {

	// get default fields from pages
	$default_arr = $pages->getPagesColumns();

	// fill array width values, replace some
	if(is_array($default_arr)) {
		foreach($default_arr as $columns) {
			foreach($columns as $key => $value) {
			
				switch($value) {
					case 'title':				
						$arr[$value] = translate("Find pages", "site_search_pages", $languages);
						break;
					case 'content':				
						$arr[$value] = '';
							$arr[$value] .= '<input type="text" id="pages_s" style="width:300px;" class="tidy">';
							$arr[$value] .= '<span class="toolbar"><button type="submit" id="btn_pages_search">'.translate("Search", "site_search", $languages).'</button></span>';
							$arr[$value] .= '<span id="ajax_spinner_pages_search" style="display:none;"><img src="'.CMS_DIR.'/cms/css/images/spinner_1.gif"></span>';
							$arr[$value] .= '<span id="ajax_status_pages_search" style="display:none;"></span>';
							$arr[$value] .= '<input type="hidden" name="token" id="token" value="'.$_SESSION['token'].'" />';
							$arr[$value] .= '<div id="pages_search_result">';
							$arr[$value] .= '</div>';
						break;
					
					default:
					$arr[$value] = null;
				}
			}
		}
	}

	// allow templates and sample views if logged in user has CMS role as contributor and above
	if(isset($_SESSION['role_CMS'])) {
		if($_SESSION['role_CMS'] >= 2) {
			if(isset($_GET['sample'])) {
				$sample = true;
				$sample_data_templates = get_sample_links();
				
				$arr['title'] = 'Sample content';
				$arr['content'] = '<img src="../content/sample/sample1.jpg" alt="Sample image" class="fluid" style="width:50%;float:right;margin:0 0 20px 20px;" />';
				$arr['content'] .= "\n".'<p><a href="?sample=2" class="link">Sample content</a> - use querystring <i>pages.php?sample</i></p>';
				$arr['content'] .= "\n".'<p><b>This page is designed using Storiesaround templates and CSS - Cascading Style Sheet. Change window size to discover responsive web design.</b></p>';
				$arr['content'] .= show_lorem_ipsum(5);				
				$arr['content'] .= "\n".'<div id="content-stories" style="float:left;width:100%;min-width:200px;min-height:10px;">';
				$arr['content'] .= "\n".'<div class="stories-column sample" style="width:31%;margin:0 4% 0 0;background:#FFEAAA;">'.show_lorem_ipsum(1).'</div>';
				$arr['content'] .= "\n".'<div class="stories-column sample" style="width:31%;margin:0 4% 0 0;background:#D4BA6A;">'.show_lorem_ipsum(1).'</div>';
				$arr['content'] .= "\n".'<div class="stories-column sample" style="width:30%;margin:0 0 0 0;background:#AA8E39;">'.show_lorem_ipsum(1).'</div>';
				$arr['content'] .= '</div>';
				$arr['template'] = 2;
				switch($_GET['sample']) {
					case 1:
						$arr['template'] = 1;
					break;
					case 2:
						$arr['template'] = 2;
					break;
					case 3:
						$arr['template'] = 4;
					break;
					case 4:
						$arr['template'] = 7;
					break;
					case 5:
						$arr['template'] = 8;
					break;
				}
				
				$arr['stories_promoted'] = 1;
			}
		}
	}
	
	$id = 0;
}

// get this page widgets
$pages_widgets = new PagesWidgets();
$rows_widgets = $pages_widgets->getPagesWidgets($id);

$widgets_css = null;
foreach($rows_widgets as $rows_widget) {
	foreach($rows_widget as $key => $value) {
		if($key == 'widgets_css') {
			if(strpos($value,'css')) {
				$widgets_css[] = $value;
			}
		}
	}
}

// page title
$page_title = "default page";
$page_title = strlen($arr['title_tag'])>0 ? $arr['title_tag'] : $arr['title'];

// meta tags
$meta_keywords = (strlen($arr['meta_keywords'])>0) ? $arr['meta_keywords'] : null;
$meta_description = (strlen($arr['meta_description'])>0) ? $arr['meta_description'] : null;
$meta_robots = (strlen($arr['meta_robots'])>0) ? $arr['meta_robots'] : null;
$meta_additional = (strlen($arr['meta_additional'])>0) ? $arr['meta_additional'] : null;

// css files, loaded in header.inc.php
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css' );


// add css theme
$theme = isset($_SESSION['site_theme']) ? $_SESSION['site_theme'] : '';
if(file_exists(CMS_ABSPATH .'/content/themes/'.$theme.'/style.css')) {
	array_push($css_files, CMS_DIR.'/content/themes/'.$theme.'/style.css');
}

// add css jquery-ui theme
$ui_theme = isset($_SESSION['site_ui_theme']) ? $_SESSION['site_ui_theme'] : '';
if(file_exists(CMS_ABSPATH .'/cms/libraries/jquery-ui/theme/'.$ui_theme.'/jquery-ui.css')) {
	if (($key = array_search(CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', $css_files)) !== false) {
		unset($css_files[$key]);
	}
	array_push($css_files, CMS_DIR.'/cms/libraries/jquery-ui/theme/'.$ui_theme.'/jquery-ui.css');
}

// add required widgets css
if(is_array($widgets_css)) {	
	// unique values
	$widgets_css = array_unique($widgets_css);
	foreach($widgets_css as $widget_css) {
		array_push($css_files, CMS_DIR.'/cms/libraries/'.$widget_css);
	}
}

// load javascript files, loads before footer.inc.php
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/js/functions.js', 
	CMS_DIR.'/cms/libraries/js/pages.js', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js',
	CMS_DIR.'/cms/libraries/jquery-jplayer/jquery.jplayer.min.js', 
	CMS_DIR.'/cms/libraries/jquery-cycle/jquery.cycle2.min.js', 
	CMS_DIR.'/cms/libraries/masonry/masonry.pkgd.min.js' );

// localization js files
$use_language = isset($_SESSION['language']) ? $_SESSION['language'] : $_SESSION['site_language'];

if(isset($use_language)) {
	switch ($use_language) {
		case "swedish":
			$js_files[] = CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.sv.js';
		break;
	}
}


// handle plugins, set each area value to null
$plugin_header = $plugin_left_sidebar = $plugin_right_sidebar = $plugin_content = $plugin_footer = $plugin_page = null;

if($arr['plugins']) {

	// main class
	$pages_plugins = new PagesPlugins();					
	// get this class name
	$plugin = $pages_plugins->getPagesPlugins($id);
	if($plugin) {
		// only use plugin if active
		if($plugin['plugins_active'] == 1) {
			// use class
			$plugin_class = new $plugin['plugins_title']();
			
			// get info
			$plugin_info = $plugin_class->info();

			// get plugin css			
			if(strlen(trim($plugin_info['css']))>0){
				array_push($css_files, trim($plugin_info['css']));
			}
			// returns an array for which areas should be replaced by plugin
			$plugin_action = $plugin_class->action($id, $users_id, $_SESSION['token'], $plugin_info['area']);
			
			if($plugin_action) {
				foreach($plugin_action as $p) {
					foreach($p as $key=>$value) {
						switch ($key) {
							case 'header';
								$plugin_header = $value;
							break;
							case 'left_sidebar';
								$plugin_left_sidebar = $value;
							break;
							case 'content';
								$plugin_content = $value;
							break;
							case 'right_sidebar';
								$plugin_right_sidebar = $value;
							break;
							case 'footer';
								$plugin_footer = $value;
							break;
							case 'page';
								$plugin_page = $value;
							break;
						}
					}
				}
			}
		}
	}				
}

// default selection html
$selections_header_above = $selections_header = $selections_header_below = $selections_left_sidebar_top = $selections_left_sidebar_bottom = $selections_right_sidebar_top = $selections_right_sidebar_bottom = $selections_content_above = $selections_content_inside = $selections_content_below = $selections_footer_above = $selections_outer_sidebar = null;
// selections
$pages_selections = explode(",",$arr['selections']);
if(is_array($pages_selections)) {

	$selections = new Selections();
	$s_rows = $selections->getMultipleSelectionsContent($pages_selections);

	foreach($s_rows as $s_row) {
		
		$external_js_files = explode(" ",$s_row['external_js']);
		foreach($external_js_files as $external_js){
			if(strlen(trim($external_js))>0){
				array_push($js_files, trim(CMS_DIR.$external_js));
			}
		}
		
		$external_css_files = explode(" ",$s_row['external_css']);
		foreach($external_css_files as $external_css){
			if(strlen(trim($external_css))>0){
				array_push($css_files, trim(CMS_DIR.$external_css));
			}
		}
		$content_html = $s_row['content_html'];
		$content_code = implode(parse_storiesaround_coded($pages, $dtz, $s_row['content_code'],0));
		
		switch($s_row['area']) {
		
			case 'header_above':
				$selections_header_above .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;

			case 'header':
				$selections_header .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;

			case 'header_below':
				$selections_header_below .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;

			case 'left_sidebar_top':
				$selections_left_sidebar_top .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;
						
			case 'left_sidebar_bottom':
				$selections_left_sidebar_bottom .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;

			case 'right_sidebar_top':
				$selections_right_sidebar_top .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;

			case 'right_sidebar_bottom':
				$selections_right_sidebar_bottom .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;

			case 'content_above':
				$selections_content_above .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;
			
			case 'content_inside':
				$selections_content_inside .= '<div class="site-selection" style="clear:none;">'.$content_html . $content_code.'</div>';
			break;

			case 'content_below':
				$selections_content_below .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;

			case 'footer_above':
				$selections_footer_above .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;
	
			case 'outer_sidebar':
				$selections_outer_sidebar .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
			break;
		}

	}
}


$body_style = '';
$lang = strlen($arr['lang']) > 0 ? $arr['lang'] : $_SESSION['site_lang'];


// plugin uses full page - include header + load js files + footer + exit!
if($plugin_page) {
	if(file_exists($plugin_page)) {
		$plugin_arguments = $arr['plugin_arguments'];
		include $plugin_page;
		if(is_array($pages_selections)) {

			// include header, open body tag
			include_once CMS_ABSPATH .'/cms/includes/inc.header_minimal.php';
			echo $s_row['content_html'] . $s_row['content_code'];;
			include_once_customfile('includes/inc.footer.php', $arr, $languages);

			// load javascript files
			$js_files = array_unique($js_files);
			foreach ( $js_files as $js ) { 
				echo "\n".'<script type="text/javascript" src="'.$js.'"></script>';
			} 
			
			echo '</body></html>';			
			exit;
		}
	}
	
}


// include header, open body tag
include_once CMS_ABSPATH .'/cms/includes/inc.header.php';


// banners
if($arr['ads']) {
	$banners = new Banners();
	$b = $banners->getBannersActive($tag=$arr['ads_filter'], $dt=gmdate('Y-m-d H:i:s'), $limit=(int)$arr['ads_limit']);
	if($b) {

		echo '<script type="text/javascript">';
			$i = 0;
			foreach($b as $banner) {
				$ext = pathinfo($b[$i]['file'], PATHINFO_EXTENSION);
				if($ext=='swf') {
					?>
					var flashvars = {};
					var params = { allowScriptAccess: "always", wmode : "transparent" };
					var attributes = {};
					swfobject.embedSWF("<?php echo CMS_DIR; ?>/content/uploads/ads/<?php echo $b[$i]['file']; ?>", "<?php echo $b[$i]['name']; ?>", "<?php echo $b[$i]['width']; ?>", "<?php echo $b[$i]['height']; ?>", "<?php echo $_SESSION['site_flash_version']; ?>", "expressInstall.swf", flashvars, params, attributes);					
					<?php
				}
				$i++;
			}
			$i = 0;
			echo '$(document).ready(function() { ';
			foreach($b as $banner) {
				$ext = pathinfo($b[$i]['file'], PATHINFO_EXTENSION);
				
				if($ext=='swf') {
					echo '$("#wrapper-ads-'.$b[$i]['area'].'").append("'.$b[$i]['header'].'<div id='.$b[$i]['name'].'>'.$b[$i]['name'].'</div>"); ';
				}
				if($ext=='gif' || $ext=='jpg' || $ext=='jpg') {
					$target = ($b[$i]['url_target']==1) ? 'target=\"_blank\"' : null;
					echo '$("#wrapper-ads-'.$b[$i]['area'].'").append("'.$b[$i]['header'].'<br /><a href=\"'.$b[$i]['url'].'\" '.$target.'><img id=\"'.$b[$i]['file'].'\" src=\"'.CMS_DIR.'/content/uploads/ads/'.$b[$i]['file'].'\" style=\"width:'.$b[$i]['width'].'px;height:'.$b[$i]['height'].'px;\" /></a><br />"); ';
				}
				$i++;
			}
			echo '}) ;';
		echo '</script>';
	}
}




// preview
//--------------------------------------------------
//--------------------------------------------------
/*

if (isset($_SESSION['first_name'])) {
	echo "\n".'<div id="login-wrapper">';
	include 'includes/inc.site_active_user.php';
	echo "\n".'</div>';
}
*/
//--------------------------------------------------
//--------------------------------------------------





// prepare navigation
$root = get_breadcrumb_path_array($id);	
$href = $_SERVER['SCRIPT_NAME'];


// include calendar
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

// open site wrapper
echo "\n".'<div id="wrapper-site">';

	// open page wrapper
	echo "\n".'<div id="wrapper-page">';

		if($arr['ads']) {
			echo "\n".'<div id="wrapper-ads-site"></div>';
		}

		// selections top (header above)
		if(is_array($pages_selections)) {
			echo "\n".'<div id="wrapper-site-selection-header-above" style="width:auto;top:0;">';
				echo $selections_header_above;
			echo "\n".'</div>';
		}

		echo "\n".'<div id="wrapper-header">';
			
			// plugin
			if($plugin_header) {
				if(file_exists($plugin_header)) {
					$plugin_arguments = $arr['plugin_arguments'];
					include $plugin_header;
				}
			} else {
			
				echo "\n".'<div id="site-navigation-top">';
					// include navigation
					include_once_customfile('includes/inc.site_navigation_top.php', $arr, $languages);
				echo "\n".'</div>';
				
				$header_image = isset($arr['header']) ? $arr['header'] : '';
				$css_site_id = $pages->getPagesHeaderCSS($header_image);
				if($sample == true) {
					$css_site_id = ' style="background: url(../content/sample/site_header_image_0.jpg) no-repeat;"';
					set_theme();
				}
				
				// selections header  - replace header
				if(isset($selections_header)) {
					echo "\n".'<div id="wrapper-site-selection-header" style="width:100%;">';
						echo $selections_header;
					echo "\n".'</div>';
				} else {
					echo "\n".'<div id="site-id" '.$css_site_id.'>';
						include_once_customfile('includes/inc.site_header.php', $arr, $languages);
					echo "\n".'</div>';
				}

				if(!$_SESSION['site_navigation_horizontal'] == 0) {
					echo "\n".'<div id="site-navigation-root">';
						$seo = $_SESSION['site_seo_url'] == 1 ? 1 : 0;
						$html = '';
						if($_SESSION['site_navigation_horizontal'] == 1) {
							$html = $pages->getPagesRoot(get_breadcrumb_path_array($id), $seo, 'pages.php');
						}
						if($sample == true) {							
							$html = get_sample_navigation_root($sample_data_templates);
						}
						echo $html;
					echo "\n".'</div>';
				}
			}
			
		echo '</div>';
		
		// selections header below
		if(is_array($pages_selections)) {
			echo "\n".'<div id="wrapper-site-selection-header-below" style="width:100%;">';
				echo $selections_header_below;
			echo "\n".'</div>';
		}
		?>
		
		<div id="wrapper-main">

			<?php
			$rows_stories = $pages->getPagesStoryContentPublishAllSorted($id);
			
			$limit_stories = isset($_SESSION['site_limit_stories']) ? $_SESSION['site_limit_stories'] : 10;
			
			// handle template, set wrapper-content width
			$content_width = 100;
			$css_content_width = $css_content_width_padding = '';
			
			$template_content_padding = is_numeric($_SESSION['site_template_content_padding']) ? $_SESSION['site_template_content_padding'] : 0;
			
			// sidebar width between 20-33%
			$sidebar_width = $_SESSION['site_template_sidebar_width'];
			
			switch ($arr['template']) {	
				case 0:
				case 1:
					// sidebars
					$content_width = 100 - ($sidebar_width * 2); 
					$left_sidebar_width = $right_sidebar_width = $sidebar_width;
					$css_content_width = $content_width.'%';
					$css_content_width_padding = '';					
				break;
				case 2:
				case 3:
					// left sidebar
					$content_width = 100 - $sidebar_width;
					$left_sidebar_width = $sidebar_width;
					$css_content_width = $content_width - $template_content_padding .'px';
					$css_content_width_padding = 'padding-right:'.$template_content_padding.'px';
				break;
				case 4:
				case 5:
					// right sidebar
					$content_width = 100 - $sidebar_width;
					$right_sidebar_width = $sidebar_width;
					$css_content_width = $content_width - $template_content_padding .'px';
					$css_content_width_padding = 'padding-left:'.$template_content_padding.'px';
				break;
				case 6:
					// panorama
					$content_width = 100; 
					$css_content_width = $content_width - ($template_content_padding * 2) .'px';
					$css_content_width_padding = 'padding-left:'.$template_content_padding.'px;padding-right:'.$template_content_padding.'px';
				break;
				case 7:
					// sidebars right joined
					$content_width = 100 - ($sidebar_width + $sidebar_width * 0.67);
					$right_sidebar_width = $sidebar_width;
					$left_sidebar_width = $sidebar_width * 0.67;
					$css_content_width = $content_width - $template_content_padding .'px';
					$css_content_width_padding = 'padding-left:'.$template_content_padding.'px';
				break;					
			}
			
			// use template settings
			if($arr['template']==0 || $arr['template']==1 || $arr['template']==2 || $arr['template']==3) { ?>
			
				<div id="wrapper-left-sidebar" class="wrapper-sidebar" style="width:<?php echo $left_sidebar_width; ?>%;">
					
					<div id="wrapper-left-sidebar-inner">
				
					<?php								
					// plugin
					if($plugin_left_sidebar) {
						
						if(file_exists($plugin_left_sidebar)) {
							$plugin_arguments = $arr['plugin_arguments'];
							include $plugin_left_sidebar;
						}
						
					} else {
					
						// selections left sidebar top
						if(is_array($pages_selections)) {
							echo '<div id="wrapper-site-selection-left-sidebar-top" style="width:100%;">';
								echo $selections_left_sidebar_top;
							echo '</div>';
						}

						if(!$_SESSION['site_navigation_vertical'] == 0) {
							if($_SESSION['site_navigation_vertical_sidebar'] == 0 || $arr['template'] == 1) {
								echo '<div class="sidebar-navigation">';
									echo '<div class="sidebar-navigation-tree">';
									$open = ($_SESSION['site_navigation_vertical'] == 2 || $_SESSION['site_navigation_vertical'] == 4) ? true : false;
									$parent_id = ($_SESSION['site_navigation_vertical'] == 3 || $_SESSION['site_navigation_vertical'] == 4 ) ? 0 : $root[0]; 
									$seo = $_SESSION['site_seo_url'] == 1 ? 1 : 0;
									get_pages_tree_sitemap($parent_id, $id, $path=get_breadcrumb_path_array($id), $a=true, $a_add_class=false, $seo, $href, $open, $depth=1, $show_pages_id = false);
									echo '</div>';
								echo '</div>';
							}
						}
						
						if($calendar_area == 'left-sidebar') {
							echo '<div id="left-sidebar-calendar" class="calendar-column">';
								get_calendar('left-sidebar', $cal, $id);
							echo '</div>';
						}

						if($sample==true) {
							if($arr['template'] == 1 || $arr['template'] == 2) {
								get_sample_tree_menu($sample_data_templates);
							}
							if($arr['template'] == 2) {
								get_sample_sidebar();
							}
						}						
						?>

						<div id="left-sidebar-widgets" class="sidebar-widgets" style="float:left;width:100%;min-height:10px;">
							<?php if($rows_widgets){ show_widgets_content($rows_widgets, "widgets_left_sidebar", $left_sidebar_width);} ?>
						</div>

						<div id="left-sidebar-promoted-stories" class="stories-column" style="width:100%;">				
							<?php
							if($arr['stories_promoted']) {
							
								$limit_stories = $arr['stories_limit'] > 0 ? $arr['stories_limit'] : $limit_stories;
							
								if($arr['stories_promoted_area'] == 1) {	
									$rows_promoted = $pages->getPagesStoryContentPublishPromoted($arr['stories_filter'], $limit_stories);
									$col_width = $arr['template'] == 1 | $arr['template'] == 3 | $arr['template'] == 5 ? 474 : 222;								
									get_box_story_content_promoted($rows_promoted, $col_width, $arr['stories_last_modified'], $arr['stories_image_copyright'], $dtz, $languages);
								}
							}
							?>
						</div>

						<div id="left-sidebar-stories" class="stories-column" style="width:100%;">
							
							<?php get_box_story_content_selected($rows_stories, "left_sidebar", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?>
							
						</div>

						<?php
						
						// selections left sidebar bottom
						if(is_array($pages_selections)) {
							echo '<div id="wrapper-site-selection-left-sidebar-bottom" style="width:100%;">';
								echo $selections_left_sidebar_bottom;
							echo '</div>';
						}
						
					}
					
					?>
				
					</div>
				</div>
			
			<?php } ?>

			<div id="wrapper-content" style="float:left;width:<?php echo $content_width;?>%;">
				
				<div id="wrapper-content-inner" style="<?php echo $css_content_width_padding; ?>;">
				
				<?php
				
				// plugin
				if($plugin_content) {
					
					if(file_exists($plugin_content)) {
						$plugin_arguments = $arr['plugin_arguments'];
						include $plugin_content;
					}
				
				} else {
					
					echo "\n".'<div id="content-top" style="width:100%;">';
					
					// selections above
					if(is_array($pages_selections)) {
						echo "\n".'<div id="wrapper-site-selection-content-above" style="width:100%;">';
							echo $selections_content_above;
						echo "\n".'</div>';
					}					

						if($id != 0) {
							echo "\n".'<div id="content-breadcrumb" style="float:left;width:90%;">';
							if(filter_var($id, FILTER_VALIDATE_INT)) {
								if($arr['breadcrumb'] > 0) {
									echo get_breadcrumb($id," &raquo; ", 25, $clickable=true);
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
							


							
							// preview
							//--------------------------------------------------
							//--------------------------------------------------
							/*
							
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
									echo '<a href="'.CMS_DIR.'/cms/admin.php?t=pages&amp;tp=add&amp;id='. $id .'" class="colorbox_edit"><span class="ui-icon ui-icon-document" style="display:inline-block;" title="'.translate("Add child page", "pages_add_childpage", $languages).'"></span></a>';
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

							// preview
							//--------------------------------------------------
							//--------------------------------------------------						
							*/
							
							
							
							
							
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
							echo '&nbsp;<img class="ajax_history" id="'.$id.'" src="'.CMS_DIR.'/cms/css/images/icon_info.png" style="vertical-align:top;width:11px;height:11px;" alt="history" />&nbsp;';
							echo '&nbsp;<span id="ajax_spinner_history" style="display:none;"><img src="'.CMS_DIR.'/cms/css/images/spinner_1.gif" alt="spinner" /></span>';
							echo '<div id="ajax_status_history" style="display:none;"></div>';
						}
					echo "\n".'</div>';
			
					if($arr['ads']) {
						echo '<div id="wrapper-ads-column" style="float:right;margin:0 0 10px 10px;">';
						echo '</div>';
					}
					
					// selections inside
					if(is_array($pages_selections)) {
						echo $selections_content_inside;
					}					

					echo "\n".'<div id="content-html">';
						echo $arr['content'];
					echo "\n".'</div>';
					
					echo "\n".'<div id="content-author">';
					if(strlen($arr['content_author']) > 0) {
						echo translate("Author", "pages_content_author", $languages);
						echo ': <span>'.$arr['content_author'].'</span>';
					}
					echo "\n".'</div>';

					// selections below
					if(is_array($pages_selections)) {
						echo "\n".'<div id="wrapper-site-selection-content-below" style="width:100%;">';
							echo $selections_content_below;
						echo "\n".'</div>';
					}					
					
					if($arr['ads']) {
						echo '<div id="wrapper-ads-row" style="margin:10px 0px;">';
						echo '</div>';
					}
					
					if($calendar_area == 'content') {
						get_calendar('content', $cal, $id);
					}
					?>
										
					<div id="content-widgets" style="float:left;width:100%;min-width:326px;min-height:10px;">
						<?php if($rows_widgets){ show_widgets_content($rows_widgets, "widgets_content", $content_width);} ?>
					</div>

					<?php
					// event stories
					if($arr['stories_event_dates']) {
						$search = $arr['stories_event_dates_filter'];
						$date = date('Y-m-d');
						$rows_event_stories = $pages->getPagesStoryContentPublishEvent($search, $date, $period='next');
						
						echo '<input type="hidden" id="stories_event_dates_filter" value="'.$search.'" />';
						echo '<a href="#" id="stories_event_previous" class="stories_events_link">'.translate("previous", "story_previous", $languages).'</a>';
						echo '&nbsp;<span id="ajax_spinner_stories_event_previous" style="display:none;"><img src="'.CMS_DIR.'/cms/css/images/spinner_1.gif" alt="spinner" /></span>';
						echo "\n".'<div id="stories_events" style="width:100%;">';					
						get_box_story_events($rows_event_stories, $content_width, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz);
						echo '</div>'."\n";
						echo '<a href="#" id="stories_event_next" class="stories_events_link">'.translate("coming", "story_coming", $languages).'</a>';
						echo '&nbsp;<span id="ajax_spinner_stories_event_next" style="display:none;"><img src="'.CMS_DIR.'/cms/css/images/spinner_1.gif" alt="spinner" /></span>';
					}					
					
					// promoted stories below content
					if($arr['stories_promoted']) {			
						if($arr['stories_promoted_area'] >= 3) {
							$rows_promoted = $pages->getPagesStoryContentPublishPromoted($arr['stories_filter'], $limit_stories);
							echo '<div id="content-promoted-stories" style="width:100%;">';
								if($arr['stories_promoted_area'] == 3 || $arr['stories_promoted_area'] == 4) {								
									echo '<div id="stories-promoted-masonry" style="margin:0 auto;clear:both;">';
										$layoutbox = $arr['stories_promoted_area'] == 3 ? 1 : 2;
										get_box_story_content_child_floats($rows_promoted, $content_width, $layoutbox, $css_class_uniformed=null, $arr['stories_image_copyright'], $arr['stories_last_modified'], $dtz, $randomize=false);
									echo '</div>';
								}
								if($arr['stories_promoted_area'] == 5) {
									get_box_story_content_child($rows_promoted, 474, $css_class_uniformed=null, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz);
								}
							echo '</div>';
						}
					}
					?>
					
					<div id="content-stories" style="float:left;width:100%;min-width:200px;min-height:10px;">						
							
						<?php
						// template decides stories col width
						switch ($arr['template']) {	
							case 0:
							case 1:
							case 7:
								switch ($arr['stories_columns']) {	
									case 1:
										?>
										<div id="A" class="stories-column" style="width:100%;overflow:hidden;margin:0;"><?php get_box_story_content_selected($rows_stories, "A", 474, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "BCDEF";
									break;
									case 2:
										?>
										<div id="A" class="stories-column" style="width:47%;overflow:hidden;margin:0 8% 0 0;"><?php get_box_story_content_selected($rows_stories, "A", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="B" class="stories-column" style="width:45%;overflow:hidden;margin:0;"><?php get_box_story_content_selected($rows_stories, "B", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "CDEF";
									break;
									case 3:
										?>
										<div id="A" class="stories-column" style="width:31%;overflow:hidden;margin:0 4% 0 0;"><?php get_box_story_content_selected($rows_stories, "A", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="B" class="stories-column" style="width:31%;overflow:hidden;margin:0 4% 0 0;"><?php get_box_story_content_selected($rows_stories, "B", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="C" class="stories-column" style="width:30%;overflow:hidden;margin:0;"><?php get_box_story_content_selected($rows_stories, "C", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "DEF";
									break;
									default:
									break;
								}
							break;

							case 2:
							case 3:
							case 4:
							case 5:
								switch ($arr['stories_columns']) {	
									case 1:
										?>
										<div id="A" class="stories-column" style="float:left;width:100%;width:100%;overflow:hidden;"><?php get_box_story_content_selected($rows_stories, "A", 726, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "BCDEF";
									break;
									case 2:
										?>
										<div id="A" class="stories-column" style="width:49%;overflow:hidden;margin:0 4% 0 0;"><?php get_box_story_content_selected($rows_stories, "A", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="B" class="stories-column" style="width:47%;overflow:hidden;margin:0;"><?php get_box_story_content_selected($rows_stories, "B", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "CDEF";
									break;
									case 3:
										?>
										<div id="A" class="stories-column" style="width:31%;overflow:hidden;margin:0 4% 0 0;"><?php get_box_story_content_selected($rows_stories, "A", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="B" class="stories-column" style="width:31%;overflow:hidden;margin:0 4% 0 0;"><?php get_box_story_content_selected($rows_stories, "B", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="C" class="stories-column" style="width:30%;overflow:hidden;margin:0;"><?php get_box_story_content_selected($rows_stories, "C", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "DEF";
										break;
										
									default:
									break;
								}
								
							break;

							case 6:

								switch ($arr['stories_columns']) {	
									case 1:
										?>
										<div id="A" class="stories-column" style="width:100%;overflow:hidden;"><?php get_box_story_content_selected($rows_stories, "A", 978, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "BCDEF";
									break;
									case 2:
										?>
										<div id="A" class="stories-column" style="width:49%;overflow:hidden;margin:0 4% 0 0;"><?php get_box_story_content_selected($rows_stories, "A", 474, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="B" class="stories-column" style="width:47%;overflow:hidden;margin:0;"><?php get_box_story_content_selected($rows_stories, "B", 474, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "CDEF";
									break;
									case 3:
										?>
										<div id="A" class="stories-column" style="width:32%;overflow:hidden;margin:0 3% 0 0;"><?php get_box_story_content_selected($rows_stories, "A", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="B" class="stories-column" style="width:32%;overflow:hidden;margin:0 3% 0 0;"><?php get_box_story_content_selected($rows_stories, "B", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="C" class="stories-column" style="width:30%;overflow:hidden;margin:0;"><?php get_box_story_content_selected($rows_stories, "C", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "DEF";
									break;
									case 4:
										?>
										<div id="A" class="stories-column" style="width:23%;overflow:hidden;margin:0 2.8% 0 0;"><?php get_box_story_content_selected($rows_stories, "A", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="B" class="stories-column" style="width:23%;overflow:hidden;margin:0 2.8% 0 0;"><?php get_box_story_content_selected($rows_stories, "B", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="C" class="stories-column" style="width:23%;overflow:hidden;margin:0 2.8% 0 0;"><?php get_box_story_content_selected($rows_stories, "C", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="D" class="stories-column" style="width:22.6%;overflow:hidden;margin:0;"><?php get_box_story_content_selected($rows_stories, "D", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "EF";
									break;
									case 6:
										?>
										<div id="A" class="stories-column" style="width:14.8%;overflow:hidden;margin:0 2.5% 0 0;"><?php get_box_story_content_selected($rows_stories, "A", 138, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="B" class="stories-column" style="width:14.8%;overflow:hidden;margin:0 2.5% 0 0;"><?php get_box_story_content_selected($rows_stories, "B", 138, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="C" class="stories-column" style="width:14.8%;overflow:hidden;margin:0 2.5% 0 0;"><?php get_box_story_content_selected($rows_stories, "C", 138, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="D" class="stories-column" style="width:14.8%;overflow:hidden;margin:0 2.5% 0 0;"><?php get_box_story_content_selected($rows_stories, "D", 138, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="E" class="stories-column" style="width:14.8%;overflow:hidden;margin:0 2.5% 0 0;"><?php get_box_story_content_selected($rows_stories, "E", 138, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>

										<div id="F" class="stories-column" style="width:13.5%;overflow:hidden;margin:0;"><?php get_box_story_content_selected($rows_stories, "F", 138, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?></div>
										<?php
										$str_cols_out_of_range = "";
									break;	
									default:
									break;
								}
							break;
							
						}
						
						?>
					</div>
					
					<?php
					// child stories
					if($arr['stories_child']) {

						$rows_child = $pages->getPagesStoryContentPublishChild($id);
						$css_class_uniformed = $arr['stories_css_class'];
						echo '<div id="content-child-stories" style="width:100%;">';
							echo '<div id="stories-child-masonry" style="margin:0 auto;clear:both;">';
							echo '<div class="grid-sizer"></div>';
							
							switch($arr['stories_child_type']) {
								case 1:
									get_box_story_content_child_floats($rows_child, $content_width, $layoutbox=1, $css_class_uniformed, $arr['stories_image_copyright'], $arr['stories_last_modified'], $dtz, $randomize=false);
								break;
								case 2:
									get_box_story_content_child_floats($rows_child, $content_width, $layoutbox=2, $css_class_uniformed, $arr['stories_image_copyright'], $arr['stories_last_modified'], $dtz, $randomize=false);
								break;
								case 3:
									get_box_story_content_child_floats($rows_child, $content_width, $layoutbox=3, $css_class_uniformed, $arr['stories_image_copyright'], $arr['stories_last_modified'], $dtz, $randomize=false);
								break;
								case 4:
									get_box_story_content_child_floats($rows_child, $content_width, $layoutbox=4, $css_class_uniformed, $arr['stories_image_copyright'], $arr['stories_last_modified'], $dtz, $randomize=false);
								break;
								case 5:
									get_box_story_content_child_floats($rows_child, $content_width, $layoutbox=5, $css_class_uniformed, $arr['stories_image_copyright'], $arr['stories_last_modified'], $dtz, $randomize=true);
								break;
								case 6:
									get_box_story_content_child_floats($rows_child, $content_width, $layoutbox=6, $css_class_uniformed, $arr['stories_image_copyright'], $arr['stories_last_modified'], $dtz, $randomize=true);
								break;
								case 7:
									get_box_story_content_child($rows_child, 474, $css_class_uniformed, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz);
								break;
							}
							echo '</div>';
						echo '</div>';
					}					
					?>
					
					<div id="content-bottom" style="float:left;width:100%;overflow:hidden;">
					</div>

					<?php					
					if($arr['template'] != 0) {
					}
				
				// plugin close
				}
				?>

				</div>				
			</div>

			<?php
			// use template settings
			// sidebars right joined
			if($arr['template']==7) { ?>
			
				<div id="wrapper-left-sidebar-joined" class="wrapper-sidebar" style="float:left;width:<?php echo $left_sidebar_width; ?>%;padding-left:30px;box-sizing:border-box;">
					
					<div id="wrapper-left-sidebar-inner">
				
					<?php
								
					// plugin
					if($plugin_left_sidebar) {
						
						if(file_exists($plugin_left_sidebar)) {
							$plugin_arguments = $arr['plugin_arguments'];
							include $plugin_left_sidebar;
						}
						
					} else {

						if($sample==true) {
							if($arr['template'] == 7) {
								get_sample_sidebar();
							}
						}

						// selections left sidebar top
						if(is_array($pages_selections)) {
							echo '<div id="wrapper-site-selection-left-sidebar-top" style="width:100%;">';
								echo $selections_left_sidebar_top;
							echo '</div>';
						}
						
						// site navigation never shown in wrapper-left-sidebar-joined - force in outer sidebar					
						if($calendar_area == 'left-sidebar') {
							echo '<div id="left-sidebar-calendar" class="calendar-column">';
								get_calendar('left-sidebar', $cal, $id);
							echo '</div>';
						}

						?>

						<div id="left-sidebar-widgets" class="sidebar-widgets" style="float:left;width:100%;min-height:10px;">
							<?php if($rows_widgets){ show_widgets_content($rows_widgets, "widgets_left_sidebar", $left_sidebar_width);} ?>
						</div>

						<div id="left-sidebar-promoted-stories" class="stories-column" style="width:100%;">				
							<?php
							if($arr['stories_promoted']) {			
								if($arr['stories_promoted_area'] == 1) {
									$rows_promoted = $pages->getPagesStoryContentPublishPromoted($arr['stories_filter'], $limit_stories);
									$col_width = $arr['template'] == 1 | $arr['template'] == 3 | $arr['template'] == 5 ? 474 : 222;								
									get_box_story_content_promoted($rows_promoted, $col_width, $arr['stories_last_modified'], $arr['stories_image_copyright'], $dtz, $languages);
								}
							}
							?>
						</div>

						<div id="left-sidebar-stories" class="stories-column" style="width:100%;">
							
							<?php get_box_story_content_selected($rows_stories, "left_sidebar", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?>
							
						</div>

						<?php						
						// selections left sidebar bottom
						if(is_array($pages_selections)) {
							echo '<div id="wrapper-site-selection-left-sidebar-bottom" style="width:100%;">';
								echo $selections_left_sidebar_bottom;
							echo '</div>';
						}
						
					}
					
					?>
				
					</div>

				</div>
			
			<?php } ?>

			<?php 
			// use template settings
			if($arr['template'] == 0 || $arr['template'] == 1 || $arr['template'] == 4 || $arr['template'] == 5 || $arr['template'] == 7) { ?>
			
			<div id="wrapper-right-sidebar" class="wrapper-sidebar" style="width:<?php echo $right_sidebar_width; ?>%;">
				
				<div id="wrapper-right-sidebar-inner">
				
				<?php				
				// plugin
				if($plugin_right_sidebar) {
					
					if(file_exists($plugin_right_sidebar)) {
						$plugin_arguments = $arr['plugin_arguments'];
						include $plugin_right_sidebar;
					}
					
				} else {
				
					// selections right sidebar top
					if(is_array($pages_selections)) {
						echo '<div id="wrapper-site-selection-right-sidebar-top" style="width:100%;">';
							echo $selections_right_sidebar_top;
						echo '</div>';
					}

					if(!$_SESSION['site_navigation_vertical'] == 0) {
						if($_SESSION['site_navigation_vertical_sidebar'] == 1 || $arr['template'] == 2 || $arr['template'] == 4 || $arr['template'] == 7) {
							echo '<div class="sidebar-navigation">';
								echo '<div class="sidebar-navigation-tree">';
								$open = ($_SESSION['site_navigation_vertical'] == 2 || $_SESSION['site_navigation_vertical'] == 4) ? true : false;
								$parent_id = ($_SESSION['site_navigation_vertical'] == 3 || $_SESSION['site_navigation_vertical'] == 4 ) ? 0 : $root[0]; 
								$seo = $_SESSION['site_seo_url'] == 1 ? true : false;
								get_pages_tree_sitemap($parent_id, $id, $path=get_breadcrumb_path_array($id), $a=true, $a_add_class=false, $seo, $href, $open, $depth=1, $show_pages_id = false);
								echo '</div>';
							echo '</div>';
						}
					}

					if($calendar_area == 'right-sidebar') {
						echo '<div id="left-sidebar-calendar" class="calendar-column">';
							get_calendar('right-sidebar', $cal, $id);
						echo '</div>';
					}
					
					if($sample==true) {
						if($arr['template'] == 4 || $arr['template'] == 7) {
							get_sample_tree_menu($sample_data_templates);
						}
						if($arr['template'] == 1 || $arr['template'] == 4) {
							get_sample_sidebar();
						}
					}
					?>

					<div id="right-sidebar-widgets" class="sidebar-widgets" style="float:left;width:100%;min-height:10px;">
						<?php if($rows_widgets){ show_widgets_content($rows_widgets, "widgets_right_sidebar", $right_sidebar_width);} ?>
					</div>
			
					<div id="right-sidebar-promoted-stories" class="stories-column" style="width:100%;">				
						<?php
						if($arr['stories_promoted']) {						
							if($arr['stories_promoted_area'] == 2) {
								$rows_promoted = $pages->getPagesStoryContentPublishPromoted($arr['stories_filter'], $limit_stories);
								$col_width = 474;
								get_box_story_content_promoted($rows_promoted, $col_width, $arr['stories_last_modified'], $arr['stories_image_copyright'], $dtz, $languages);
							}
						}
						?>
					</div>
					
					<div id="right-sidebar-stories" class="stories-column" style="width:100%;">
						
						<?php get_box_story_content_selected($rows_stories, "right_sidebar", 222, $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz); ?>
						
					</div>

					<?php
				
					// selections right sidebar bottom
					if(is_array($pages_selections)) {
						echo '<div id="wrapper-site-selection-right-sidebar-bottom" style="width:100%;">';
							echo $selections_right_sidebar_bottom;
						echo '</div>';
					}
				
				// plugin close
				}
				
				?>

				</div>
			</div>
			<?php } ?>
		</div>			

		<div id="wrapper-selections-outside"><?php echo $selections_outer_sidebar; ?></div>
		
		<?php
		
		if($arr['ads']) {
			echo '<div id="wrapper-ads-outside-trigger"><div id="wrapper-ads-outside" style="width:222px;margin:12px 0px;float:right;overflow:hidden;"></div></div>';
		}
		
		// selections bottom (above footer)
		if(is_array($pages_selections)) {
			echo '<div id="wrapper-site-selection-footer-above" style="">';
				echo $selections_footer_above;
			echo '</div>';
		}
		
		// plugin
		if($plugin_footer) {
			echo '<div style="width:100%;clear:both;">';

				if(file_exists($plugin_footer)) {
					$plugin_arguments = $arr['plugin_arguments'];
					include $plugin_footer;
				}
			
			echo '</div>';
		}
		?>
		
		<div id="footer-wrapper">			
			<?php include_once_customfile('includes/inc.footer.php', $arr, $languages); ?>			
		</div>
		
	<!-- close page wrapper -->
	</div>
	
<!-- close site wrapper -->
</div>

<input type="hidden" name="pages_id" id="pages_id" value="<?php echo $id;?>" />


<?php
// load unique javascript files
$js_files = array_unique($js_files);

foreach ( $js_files as $js ) { 
	echo "\n".'<script src="'.$js.'"></script>';
} 
?>

<?php
include_once 'includes/inc.debug.php';
?>



<div id="browser_size_helper" style="position:absolute;top:10px;left:10px;z-index:999;background:#fff;color:#000;width:200px;padding:5px;border:1px solid grey;opacity:0.5;">
	<div id="browser_size" style="">Browser width (px)
	<select id="browser_size_fixed" style="margin:5px 0 5px 5px;">
		<option value="0"></option>
		<option value="320">320</option>
		<option value="480">480</option>
		<option value="640">640</option>
		<option value="768">768</option>
		<option value="1024">1024</option>
		<option value="1280">1280</option>
		<option value="1366">1366</option>
		<option value="1536">1536</option>
		<option value="1680">1680</option>
	</select>
	</div>
</div>




</body>
</html>