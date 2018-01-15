<?php
// include core 
include_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';

if(!get_role_CMS('user') == 1) {die;}

// overall
if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {
				
		// check client user-agent, prevent session been hijacked
		if($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			die('User agent fail. Please logout and login again.');
		}
	
		// $action
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_STRING);
		
		switch($action) {
		
			case 'save_site_general_settings';
		
				$site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
				$site_name = filter_var(trim($_POST['site_name']), FILTER_SANITIZE_STRING);
				$site_slogan = filter_var(trim($_POST['site_slogan']), FILTER_SANITIZE_STRING);
				$site_domain_url = filter_var(trim($_POST['site_domain_url']), FILTER_SANITIZE_STRING);
				$site_domain = filter_var(trim($_POST['site_domain']), FILTER_SANITIZE_STRING);
				$site_email = filter_var(trim($_POST['site_email']), FILTER_VALIDATE_EMAIL);
				$site_copyright = filter_var(trim($_POST['site_copyright']), FILTER_SANITIZE_STRING);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$site = new Site();
				$result = $site->setSiteGeneralSettings($site_id, $site_name, $site_slogan, $site_domain_url, $site_domain, $site_email, $site_copyright, $utc_modified);
				if($result) {
					$_SESSION['site_name'] = $site_name;
					$_SESSION['site_slogan'] = $site_slogan;
					$_SESSION['site_domain_url'] = $site_domain_url;
					$_SESSION['site_domain'] = $site_domain;
					$_SESSION['site_email'] = $site_email;
					$_SESSION['site_copyright'] = $site_copyright;
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($site_id, 'site_id', 'UPDATE', describe('site general settings', $site_name), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);

				}
				
				break;

			case 'save_site_design';

				$site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
				$site_wrapper_page_width = filter_input(INPUT_POST, 'site_wrapper_page_width', FILTER_VALIDATE_INT);
				$site_theme = filter_var(trim($_POST['site_theme']), FILTER_SANITIZE_STRING);
				$site_ui_theme = filter_var(trim($_POST['site_ui_theme']), FILTER_SANITIZE_STRING);
				$site_template_content_padding = filter_input(INPUT_POST, 'site_template_content_padding', FILTER_VALIDATE_INT);
				$site_title_position = filter_input(INPUT_POST, 'site_title_position', FILTER_VALIDATE_INT);				
				$site_template_sidebar_width = filter_input(INPUT_POST, 'site_template_sidebar_width', FILTER_VALIDATE_INT);
				$site_navigation_horizontal = filter_input(INPUT_POST, 'site_navigation_horizontal', FILTER_VALIDATE_INT);
				$site_navigation_vertical = filter_input(INPUT_POST, 'site_navigation_vertical', FILTER_VALIDATE_INT);
				$site_navigation_vertical_sidebar = filter_input(INPUT_POST, 'site_navigation_vertical_sidebar', FILTER_VALIDATE_INT);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$site = new Site();
				$result = $site->setSiteDesign($site_id, $site_wrapper_page_width, $site_theme, $site_ui_theme, $site_template_sidebar_width, $site_template_content_padding, $site_title_position, $site_navigation_horizontal, $site_navigation_vertical, $site_navigation_vertical_sidebar, $utc_modified);
				if($result) {
					$_SESSION['site_theme'] = $site_theme;
					$_SESSION['site_wrapper_page_width'] = $site_wrapper_page_width;
					$_SESSION['site_ui_theme'] = $site_ui_theme;
					$_SESSION['site_template_sidebar_width'] = $site_template_sidebar_width;
					$_SESSION['site_template_content_padding'] = $site_template_content_padding;
					$_SESSION['site_title_position'] = $site_title_position;
					$_SESSION['site_navigation_horizontal'] = $site_navigation_horizontal;
					$_SESSION['site_navigation_vertical'] = $site_navigation_vertical;
					$_SESSION['site_navigation_vertical_sidebar'] = $site_navigation_vertical_sidebar;
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($site_id, 'site_id', 'UPDATE', describe('site general design', $site_id), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);

				} else {
					//echo 'Grrrrrrr';
				}
				
			break;	
				
			case 'save_site_account_settings';
				$site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
				$site_account_registration = ($_POST['site_account_registration'] == 1) ? 1 : 0;
				$site_account_welcome_message = filter_var(trim($_POST['site_account_welcome_message']), FILTER_SANITIZE_STRING);
				$site_groups_default_id = filter_input(INPUT_POST, 'site_groups_default_id', FILTER_VALIDATE_INT);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$site = new Site();
				$result = $site->setSiteAccountSettings($site_id, $site_account_registration, $site_account_welcome_message, $site_groups_default_id, $utc_modified);
				if($result) {
					$_SESSION['site_account_registration'] = $site_account_registration;
					$_SESSION['site_account_welcome_message'] = $site_account_welcome_message;
					$_SESSION['site_groups_default_id'] = $site_groups_default_id;
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($site_id, 'site_id', 'UPDATE', 'site account settings', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
					
				}
				
			break;
				
			case 'save_site_content';
				$site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
				$site_rss_description = filter_var(trim($_POST['site_rss_description']), FILTER_SANITIZE_STRING);
				$site_publish_guideline = filter_var(trim($_POST['site_publish_guideline']), FILTER_SANITIZE_STRING);
				$site_limit_stories = filter_input(INPUT_POST, 'site_limit_stories', FILTER_VALIDATE_INT);
				$site_feed = filter_var(trim($_POST['site_feed']), FILTER_SANITIZE_STRING);
				$site_feed_interval = filter_var(trim($_POST['site_feed_interval']), FILTER_VALIDATE_INT);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$site = new Site();
				$result = $site->setSiteContent($site_id, $site_rss_description, $site_publish_guideline, $site_limit_stories, $site_feed, $site_feed_interval, $utc_modified);
				if($result) {
					$_SESSION['site_rss_description'] = $site_rss_description;
					$_SESSION['site_publish_guideline'] = $site_publish_guideline;
					$_SESSION['site_limit_stories'] = $site_limit_stories;
					$_SESSION['site_feed'] = $site_feed;
					$_SESSION['site_feed_interval'] = $site_feed_interval;
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($site_id, 'site_id', 'UPDATE', 'site content publishing', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
					
				}
				
			break;

		case 'save_site_script';
			$site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
			$site_script = $_POST['site_script'];
			$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
			
			$site = new Site();
			$result = $site->setSiteScript($site_id, $site_script, $utc_modified);
			if($result) {
				$_SESSION['site_script'] = $site_script;
				
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$history = new History();
				$history->setHistory($site_id, 'site_id', 'UPDATE', describe('site script', ''), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
				
			}
			
			break;
			
			case 'save_site_meta';
				$site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
				$site_meta_tags = $_POST['site_meta_tags'];
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$site = new Site();
				$result = $site->setSiteMeta($site_id, $site_meta_tags, $utc_modified);
				if($result) {
					$_SESSION['site_meta_tags'] = $site_meta_tags;
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($site_id, 'site_id', 'UPDATE', describe('site meta', ''), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
					
				}
				
			break;
				
			case 'save_site_maintenance';
				$site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
				$site_maintenance = filter_input(INPUT_POST, 'site_maintenance', FILTER_VALIDATE_INT);
				$site_error_mode = filter_input(INPUT_POST, 'site_error_mode', FILTER_VALIDATE_INT);
				$site_history_max = filter_input(INPUT_POST, 'site_history_max', FILTER_VALIDATE_INT);
				$site_maintenance_message = filter_var(trim($_POST['site_maintenance_message']), FILTER_SANITIZE_STRING);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$site = new Site();
				$result = $site->setSiteMaintenance($site_id, $site_maintenance, $site_maintenance_message, $site_error_mode, $site_history_max, $utc_modified);
				if($result) {
					$_SESSION['site_maintenance'] = $site_maintenance;
					$_SESSION['site_error_mode'] = $site_error_mode;
					$_SESSION['site_history_max'] = $site_history_max;
					$_SESSION['site_maintenance_message'] = $site_maintenance_message;
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($site_id, 'site_id', 'UPDATE', 'site maintenance', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
					
				}
				
			break;
				
			case 'save_site_configuration';
				$site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
				$site_country = filter_var(trim($_POST['site_country']), FILTER_SANITIZE_STRING);
				$site_language = filter_var(trim($_POST['site_language']), FILTER_SANITIZE_STRING);
				$site_lang = filter_var(trim($_POST['site_lang']), FILTER_SANITIZE_STRING);
				$site_timezone = filter_var(trim($_POST['site_timezone']), FILTER_SANITIZE_STRING);
				$site_dateformat = filter_var(trim($_POST['site_dateformat']), FILTER_SANITIZE_STRING);
				$site_timeformat = filter_var(trim($_POST['site_timeformat']), FILTER_SANITIZE_STRING);
				$site_firstdayofweek = filter_input(INPUT_POST, 'site_firstdayofweek', FILTER_VALIDATE_INT);
				$site_wysiwyg = filter_var(trim($_POST['site_wysiwyg']), FILTER_SANITIZE_STRING);
				$site_seo_url = filter_input(INPUT_POST, 'site_seo_url', FILTER_VALIDATE_INT);
				$site_autosave = filter_input(INPUT_POST, 'site_autosave', FILTER_VALIDATE_INT);
				$site_autosave = ($site_autosave >= 30 && $site_autosave <= 600) ? $site_autosave * 1000 : 120000;
				$site_flash_version = filter_input(INPUT_POST, 'site_flash_version', FILTER_SANITIZE_STRING);
				$site_mail_method = filter_input(INPUT_POST, 'site_mail_method', FILTER_VALIDATE_INT);
				$site_smtp_server = filter_var(trim($_POST['site_smtp_server']), FILTER_SANITIZE_STRING);
				$site_smtp_port = filter_input(INPUT_POST, 'site_smtp_port', FILTER_VALIDATE_INT);
				$site_smtp_username = filter_var(trim($_POST['site_smtp_username']), FILTER_SANITIZE_STRING);
				$site_smtp_password = filter_var(trim($_POST['site_smtp_password']), FILTER_SANITIZE_STRING);
				$site_smtp_authentication = filter_input(INPUT_POST, 'site_smtp_authentication', FILTER_VALIDATE_INT);
				$site_smtp_debug = filter_input(INPUT_POST, 'site_smtp_debug', FILTER_VALIDATE_INT);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$site = new Site();
				$result = $site->setSiteConfiguration($site_id, $site_country, $site_language, $site_lang, $site_timezone, $site_dateformat, $site_timeformat, $site_firstdayofweek, $site_wysiwyg, $site_seo_url, $site_autosave, $site_flash_version, $site_mail_method, $site_smtp_server, $site_smtp_port, $site_smtp_username, $site_smtp_password, $site_smtp_authentication, $site_smtp_debug, $utc_modified);
				if($result) {
					$_SESSION['site_country'] = $site_country;
					$_SESSION['site_language'] = $site_language;
					$_SESSION['site_lang'] = $site_lang;
					$_SESSION['site_timezone'] = $site_timezone;
					$_SESSION['site_dateformat'] = $site_dateformat;
					$_SESSION['site_timeformat'] = $site_timeformat;
					$_SESSION['site_firstdayofweek'] = $site_firstdayofweek;
					$_SESSION['site_wysiwyg'] = $site_wysiwyg;
					$_SESSION['site_seo_url'] = $site_seo_url;
					$_SESSION['site_autosave'] = $site_autosave;
					$_SESSION['site_flash_version'] = $site_flash_version;					
					$_SESSION['site_mail_method'] = $site_mail_method;
					$_SESSION['site_smtp_server'] = $site_smtp_server;
					$_SESSION['site_smtp_port'] = $site_smtp_port;
					$_SESSION['site_smtp_username'] = $site_smtp_username;
					$_SESSION['site_smtp_password'] = $site_smtp_password;
					$_SESSION['site_smtp_authentication'] = $site_smtp_authentication;
					$_SESSION['site_smtp_debug'] = $site_smtp_debug;
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($site_id, 'site_id', 'UPDATE', 'site configuration', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
					
				}
				
			break;

			case 'check_smtp_settings';

				// include file
				include_once 'includes/inc.send_a_mail.php';
			
				if(!isset($_SESSION['site_email'])) {
					die('Session site_email not set');
				}
			
				$smtp_email_to = filter_var(trim($_POST['smtp_email_to']), FILTER_VALIDATE_EMAIL);
				$site_email_from = filter_var($_SESSION['site_email'], FILTER_VALIDATE_EMAIL);
				
				if($smtp_email_to && $site_email_from) {				
					$result = send_a_mail($_SESSION['token'], $smtp_email_to, $smtp_email_to, $site_email_from, $site_email_from, 'Check smtp settings', 'Hello world', '');
					if($result) {
						echo 'Message sent';
						
						$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history_email = new HistoryEmail();
						$history_email->setHistoryEmail($smtp_email_to, $site_email_from, 'Check smtp settings', 'Hello world', $utc_datetime);
						
					} else {
						echo 'Mail failure';
					}
				} else {
					echo 'Could not validate email - check email to: '. $smtp_email_to .', from (site email): '. $site_email_from;
				}

			break;
				
				
			case 'update_selections_position':
				// array of pages
				$pages_selections_id_array = $_POST['arr_pages_selections_id'];
				// use class update
				$selections = new Selections();
				$result = $selections->updatePagesSelectionsPosition($pages_selections_id_array);
				
			break;
				

			case 'history_keep';
				$site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$site_history_max = filter_input(INPUT_POST, 'site_history_max', FILTER_VALIDATE_INT);
				$site = new Site();
				$result = $site->setSiteHistory($site_id, $site_history_max, $utc_modified);

				if($result) {
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($site_id, 'site_id', 'UPDATE', 'site configuration', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
				}
				
			break;

	
			case 'banners_save';
				$name = filter_var(trim($_POST['banners_name']), FILTER_SANITIZE_STRING);
				$file = filter_var(trim($_POST['banners_file']), FILTER_SANITIZE_STRING);
				$banners_id = filter_input(INPUT_POST, 'banners_id', FILTER_VALIDATE_INT);
				$area = filter_var(trim($_POST['banners_area']), FILTER_SANITIZE_STRING);
				$file = filter_var(trim($_POST['banners_file']), FILTER_SANITIZE_STRING);
				$tag = filter_var(trim($_POST['banners_tag']), FILTER_SANITIZE_STRING);
				$header = filter_var(trim($_POST['banners_header']), FILTER_SANITIZE_STRING);
				$url = filter_var(trim($_POST['banners_url']), FILTER_SANITIZE_STRING);
				$url_target = filter_input(INPUT_POST, 'banners_target', FILTER_VALIDATE_INT);
				$width	= filter_input(INPUT_POST, 'banners_width', FILTER_VALIDATE_INT);
				$height = filter_input(INPUT_POST, 'banners_height', FILTER_VALIDATE_INT);
				$utc_start = filter_var(trim($_POST['datetime_start']), FILTER_SANITIZE_STRING);
				$utc_end = filter_var(trim($_POST['datetime_end']), FILTER_SANITIZE_STRING);
				$active = filter_input(INPUT_POST, 'banners_active', FILTER_VALIDATE_INT);
				
				
				$banners = new Banners();
				$result = $banners->setBanners($banners_id, $name, $file, $area, $header, $url, $url_target, $width, $height, $tag, $active, $utc_start, $utc_end);

				if ($result) {
				
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($banners_id, 'banners_id', 'UPDATE', describe('file', $file), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
				
				}
				
			break;
				
			case 'banners_delete';
			
				$file = filter_var(trim($_POST['banners_file']), FILTER_SANITIZE_STRING);
				$p = '../uploads/ads/';
				if (is_file($p . $file)) {
					// remove file
					unlink($p . $file);
				
					// delete from database
					$banners_id = filter_input(INPUT_POST, 'banners_id', FILTER_VALIDATE_INT);								
					$banners = new Banners();
					$result = $banners->deleteBanners($banners_id);

					if ($result) {
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history = new History();
						$history->setHistory($banners_id, 'banners_id', 'DELETE', describe('file', $file), $_SESSION['users_id'], $_SESSION['token'], $utc_modified);							
					}
					
				}
				
			break;

				
			case 'history_search';
			
				$s = filter_var(trim($_POST['s']), FILTER_SANITIZE_STRING);
				$banners = new History();
				$rows = $banners->getHistorySearch($s);
				echo json_encode($rows);
				
			break;
				
				
			case 'banners_search';
			
				$s = filter_var(trim($_POST['s']), FILTER_SANITIZE_STRING);
				$banners = new Banners();
				$rows = $banners->getBannersSearch($s);
				echo json_encode($rows);
				
			break;

				
			case 'get_site_log_php';

				$log = null;
				$logfile = '../log/php_error.txt';
				if(is_file($logfile)) {
					$log =  nl2br(file_get_contents($logfile, FILE_USE_INCLUDE_PATH)); 
				}
				echo $log;
				
			break;
				
				
			case 'get_site_log_pdo';

				$log = null;
				$logfile = '../log/pdo_exception.txt';
				if(is_file($logfile)) {
					$log =  nl2br(file_get_contents($logfile, FILE_USE_INCLUDE_PATH)); 
				}
				echo $log; 
				
			break;


			case 'files';

				$directory_value = $_POST['directory_value'];
				$directory_text = $_POST['directory_text'];


				echo '<table><tr>';
					echo '<td><h4 class="admin-heading">'.$directory_text.'</h4></td>';					
					?>
					<td style="padding-left:20px;vertical-align:bottom;">&nbsp;</td>
					<?php
				echo '</tr></table><hr />';

				
				?>
				<p></p>
				<?php
								
				if (is_dir(CMS_ABSPATH . '/'. $directory_text)) {

					if ($dh = opendir(CMS_ABSPATH .'/'. $directory_text)) {
						$images_ext = array('jpg','jpeg','gif','png');

						while (($file = readdir($dh)) !== false) {
							if (!is_dir(CMS_ABSPATH .'/'. $directory_text.'/'.$file)) {
							
								$ext = pathinfo($directory_text.'/'.$file, PATHINFO_EXTENSION);
								if(in_array($ext, $images_ext)) {
									echo $file.' ('.round(filesize(CMS_ABSPATH .'/'. $directory_text.'/'.$file)/1024,1).' kb)'.$ext.'<br /><img src="'.CMS_DIR . '/'. $directory_text.'/'.$file.'" /><br />';
								} else {
									echo '<pre><a href="'.$directory_text.'/'.$file.'" target="_blank">'.$file .'</a></pre>';
								}
							}
						}
						closedir($dh);
					}
				}
				

			break;


			case 'edit_files_css';

				$directory_value = $_POST['directory_value'];
				$directory_text = $_POST['directory_text'];
				
				echo '<table style="width:100%;"><tr>';
					echo '<td style="width:30%;"><h4 class="admin-heading">Theme: '.$directory_text.'</h4></td>';					
					?>
					
					<td style="padding-left:20px;vertical-align:bottom;">&nbsp;</td>
					<?php
					echo '<td style="padding-left:20px;"><i>Saving files overwrites existing file. Always copy content before saving!</i></td>';
					echo '<td style="text-align:right;width:30%;"><span id="ajax_spinner_edit_files_css" style="display:none;"><img src="css/images/spinner.gif"></span><span id="ajax_status_edit_files_css" style="display:none;"></span><span class="toolbar">&nbsp;<button id="save_file_css">Save</button></span></td>';
				echo '</tr></table>';
				
				$str = null;
				$css_file = CMS_ABSPATH .'/content/themes/'.$directory_value.'/style.css';
				echo '<input type="hidden" id="css_file" value="'.$css_file.'" />';
				//echo $css_file;
				if(is_file($css_file)) {
					$str =  file_get_contents($css_file, FILE_USE_INCLUDE_PATH); 
				}

				echo '<p>';
					echo '<textarea id="css" style="width:100%;min-height:300px;" class="code">';
						echo $str;
					echo '</textarea>';
				echo '</p>';

				?>
				<script type="text/javascript">

					$(document).ready(function() {
										
						$( ".toolbar button" ).button({
							icons: {
							},
							text: true
						});
					
						$('#save_file_css').click(function(event){
							event.preventDefault();
							var action = "save_files_css";
							var token = $("#token").val();
							var css_file = $("#css_file").val();
							var css = $("#css").val();
							$.ajax({
								beforeSend: function() { loading = $('#ajax_spinner_edit_files_css').show()},
								complete: function(){ loading = setTimeout("$('#ajax_spinner_edit_files_css').hide()",700)},
								type: 'POST',
								url: 'admin_edit_ajax.php',
								data: "action=" + action + "&token=" + token + "&css_file=" + css_file + "&css=" + css,
									success: function(message){	
									ajaxReply(message,'#ajax_status_edit_files_css');
								},
							});
						});	
						
					});	

				</script>
				
				<?php
				
			break;
				
			case 'edit_files';

				$directory_value = $_POST['directory_value'];
				$directory_text = $_POST['directory_text'];
				
				echo '<table style="width:100%;"><tr>';
					echo '<td style="width:30%;"><h4 class="admin-heading">File: '.$directory_text.'</h4></td>';
					echo '<td style="padding-left:20px;"><i>Saving files overwrites existing file. Always copy content before saving!</i></td>';
					echo '<td style="text-align:right;"><span id="ajax_spinner_edit_files" style="display:none;"><img src="css/images/spinner.gif"></span><span id="ajax_status_edit_files" style="display:none;"></span>&nbsp;<span class="toolbar"><button id="save_file">Save</button></span></td>';
				echo '</tr></table>';
				
				$str = null;
				$file = '../content/includes/'.$directory_value;
				echo '<input type="hidden" id="file" value="'.$file.'" />';
				//echo $css_file;
				if(is_file($file)) {
					$str =  file_get_contents($file, FILE_USE_INCLUDE_PATH); 
				}

				echo '<p>';
					echo '<textarea id="file_content" style="width:100%;min-height:300px;" class="code">';
						echo $str;
					echo '</textarea>';
				echo '</p>';

				?>
				<script type="text/javascript">

					$(document).ready(function() {
										
						$( ".toolbar button" ).button({
							icons: {
							},
							text: true
						});
					
						$('#save_file').click(function(event){
							event.preventDefault();
							var action = "save_file_content";
							var token = $("#token").val();
							var file = $("#file").val();
							var file_content = $("#file_content").val();
							var file_content = encodeURIComponent(file_content);
							$.ajax({
								beforeSend: function() { loading = $('#ajax_spinner_edit_files').show()},
								complete: function(){ loading = setTimeout("$('#ajax_spinner_edit_files').hide()",700)},
								type: 'POST',
								url: 'admin_edit_ajax.php',
								data: "action=" + action + "&token=" + token + "&file=" + file + "&file_content=" + file_content,
									success: function(message){	
									ajaxReply(message,'#ajax_status_edit_files');
								},
							});
						});	
						
					});	

				</script>
				
				<?php
				
			break;

				
			case 'save_files_css';

				$css_file = $_POST['css_file'];
				$css = strip_tags($_POST['css']);
				$css_date = new DateTime();
				$css_date = $css_date->format('Y-m-d H:i:s');
				$css_content = "// ".$css_date . "\n" . $css;
				
				if($result = file_put_contents($css_file, $css_content)) {
					echo 'saved';
				}
			break;
				

			case 'save_file_content';

				$file = $_POST['file'];
				$file_content = $_POST['file_content'];
				$date = new DateTime();
				$date = $date->format('Y-m-d H:i:s');
				$file_content = "<!-- ".$date . " -->\n". $file_content;

				if($result = file_put_contents($file, $file_content)) {
					echo 'saved';
				}
			break;

				
			case '';
			break;
		}		
		
	}
}

?>