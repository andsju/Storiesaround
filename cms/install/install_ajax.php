<?php
// include core 
//--------------------------------------------------
include_once '../includes/inc.core.php';


// overall
// --------------------------------------------------
if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {
			
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_STRING);
		
		switch($action) {

			case "db_setup":

				include '../install/inc.db_schema.php';

				$site = new Site();
				$i = 0;

				if(is_array($sql_tables)) {
					foreach($sql_tables as $sql) {
						$result = $site->setSiteUpdate($sql);
						$reply = $result ? date("H:i:s") .' | success' : null;
						if($result) {
							$i++;
							echo $reply .'<br />';
						}
					}
				}

			break;			
			
			case "site_install":
			
				$s = null;
			
				$site_name = trim($_POST['site_name']);
				$site_domain_url = filter_var(trim($_POST['site_domain_url']), FILTER_SANITIZE_STRING);
				$site_domain = filter_var(trim($_POST['site_domain']), FILTER_SANITIZE_STRING);
				$first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
				$last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
				$user_name = filter_var(trim($_POST['user_name']), FILTER_SANITIZE_STRING);
				$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
				$password = $_POST['password'];
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

				// check if email address is available
				$users = new Users;
				$check = $users->getUsersEmail($email);

				if($check) { echo 'You have entered an email address that is already registered: <b>'.$email.'</b>. Please change email or delete current user. Reload browser and try again.'; die();}

				$site = new Site();				
				$site_language = 'english';
				$site_email = $email;
				$site_copyright = $site_name;
				$site_theme = "";
				$site_ui_theme = "";
				$site_logotype = "logotype.png"; 
				$site_header_image = 'site_header_image.jpg'; 
				$site_timezone = 'Europe/Stockholm'; 
				$site_wysiwyg = 'tinymce';
				$icon_check = '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
				$icon_notice = '<span class="ui-icon ui-icon-notice" style="display:inline-block;"></span>';
				
				$result = $site->setSiteInstall($site_name, $site_domain_url, $site_domain, $site_email, $site_copyright, $site_language, $site_timezone, $site_wysiwyg, $site_theme, $site_ui_theme, $site_header_image, $utc_modified);
				
				if($result) {

					$s .= '<p>'.$icon_check.' Saved site: <i>'.$site_name.'</i></p>';					
					$_SESSION['site_name'] = $site_name;
					$_SESSION['site_domain_url'] = $site_domain_url;
					$_SESSION['site_domain'] = $site_domain;
					$_SESSION['site_theme'] = $site_theme;
					$_SESSION['site_ui_theme'] = $site_ui_theme;
					$_SESSION['site_logotype'] = $site_logotype;
					$_SESSION['site_header_image'] = $site_header_image;
					$_SESSION['site_timezone'] = $site_timezone;
					$_SESSION['site_wysiwyg'] = $site_wysiwyg;
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($result, 'site_id', 'INSERT', describe('site install', $site_name), 0, $_SESSION['token'], $utc_modified);
					$pass_hash = password_hash($password, PASSWORD_DEFAULT);
					$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');					
					$result2 = $users->setUsersAdmin($email, $pass_hash, $first_name, $last_name, $user_name, $utc_created);
					if($result2) {

						$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
						$_SESSION['users_id'] = $result2;
						$_SESSION['first_name'] = $first_name;
						$_SESSION['last_name'] = $last_name;
						$_SESSION['email'] = $email;
						$_SESSION['user_name'] = $user_name;
						$_SESSION['role_CMS'] = 6;
						$_SESSION['site_maintenance'] = 0;
						$_SESSION['debug'] = 0;

						$s .= '<p>'.$icon_check.' Saved user name: <b>'.$user_name.', '.$email.'</b></p>';
						$s .= '<h3>Done!</h3>';
						$s .= '<p>When you click the link below you will proceed to '.$site_domain_url.'</p>'; 
						$s .= '<p>Login with your username and password to start publish content - <a href="'.CMS_DIR.'/cms/pages.php?sample=2" class="obvious">take me to '.$site_domain_url.'</a></b></p>';
						
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history = new History();
						$history->setHistory($result2, 'users_id', 'INSERT', describe('superadministrator', $site_name), 0, $_SESSION['token'], $utc_modified);						

						// setup sample page
						$site = new Site();
						$dt = new DateTime();
						$dtStart = $dt->format('Y-m-d H:i:s');

						$sql = "
						INSERT INTO `pages` (`parent_id`, `parent`, `position`, `access`, `title`, `title_alternative`, `title_hide`, `title_tag`, `pages_id_link`, `meta_keywords`, `meta_description`, `meta_additional`, `meta_robots`, `lang`, `category_position`, `category`, `landing_page`, `content`, `content_author`, `grid_active`, `grid_area`, `grid_custom_classes`, `grid_content`, `grid_cell_template`, `grid_cell_image_height`, `folder`, `tag`, `header_image`, `header_caption`, `header_caption_align`, `header_caption_vertical_align`, `header_caption_show`, `header_image_timeout`, `parallax_scroll`, `header_image_fade`, `status`, `template`, `template_custom`, `selections`, `breadcrumb`, `search_field_area`, `content_links`, `plugins`, `plugin_arguments`, `calendar`, `events`, `reservations`, `comments`, `functions`, `stories_columns`, `stories_child_area`, `stories_child`, `stories_promoted_area`, `stories_promoted`, `stories_limit`, `stories_css_class`, `stories_equal_height`, `stories_selected`, `stories_event_dates`, `stories_event_dates_filter`, `stories_image_copyright`, `stories_wide_teaser_image_align`, `stories_wide_teaser_image_width`, `stories_last_modified`, `stories_filter`, `story_content`, `story_wide_teaser_image`, `story_css_class`, `story_custom_title`, `story_custom_title_value`, `story_promote`, `story_event`, `story_event_date`, `story_link`, `rss_promote`, `rss_description`, `utc_created`, `utc_modified`, `utc_start_publish`, `utc_end_publish`) VALUES
						(0, 0, 10, 2, 'Sample page', '', 0, '', '', NULL, NULL, NULL, NULL, '', 0, '', 0, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean nunc turpis, aliquam quis quam non, consectetur accumsan tortor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Proin tincidunt a nunc in laoreet. Nulla facilisi. Fusce mauris dolor, ultricies a interdum id, tempor id nulla. Donec sagittis urna ac leo feugiat, ac semper purus rutrum. Suspendisse a luctus sapien. Sed augue dolor, aliquet ut dolor eget, viverra accumsan est. Aliquam et bibendum leo, quis laoreet turpis. Curabitur porttitor ornare eros, non accumsan elit tristique ac. Mauris dictum, enim in vehicula feugiat, turpis purus imperdiet massa, in rhoncus nisl odio non turpis.</p>\n<p>In et neque eu mi rutrum convallis vitae tincidunt magna. Proin ut molestie enim. Donec ut facilisis magna, a convallis tellus. Phasellus condimentum nulla id tincidunt tempus. Proin nibh lacus, laoreet commodo erat et, vestibulum pulvinar nunc. Phasellus hendrerit lectus ligula, non dignissim ligula tincidunt a. Quisque ante lacus, varius vitae ante at, molestie fermentum odio. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut sagittis mi quis arcu ornare, pharetra suscipit elit placerat. Ut et nulla ultricies, porta urna id, luctus lectus.</p>\n<p>Donec nunc libero, porta nec feugiat id, laoreet ut mi. Nunc facilisis, augue nec laoreet dapibus, urna tortor aliquet mi, in imperdiet justo nisi elementum tortor. Sed sit amet purus placerat, ultricies ipsum vel, accumsan quam. Etiam vel nisi in justo consequat volutpat vel sit amet libero. Phasellus commodo elit fringilla nibh pretium tempor. Donec non sollicitudin neque, a dignissim metus. Nam eget elit vel nunc facilisis molestie. Vivamus urna eros, scelerisque id turpis tristique, condimentum aliquet nibh. Duis rhoncus quis mauris nec hendrerit.</p>\n<p>Storiesaround is a&nbsp;<em>Content Management System</em>.&nbsp;</p>', '', 1, 0, '', '[{\"name\":\"grid-image\",\"value\":\"../content/sample/sample1.jpg\"},{\"name\":\"grid-image-y\",\"value\":\"0\"},{\"name\":\"heading\",\"value\":\"\"},{\"name\":\"video\",\"value\":\"\"},{\"name\":\"url\",\"value\":\"\"},{\"name\":\"link\",\"value\":\"\"},{\"name\":\"grid-content\",\"value\":\"Grid content\"},{\"name\":\"css\",\"value\":\"\"},{\"name\":\"label\",\"value\":\"\"},{\"name\":\"grid-dynamic-content\",\"value\":\"none\"},{\"name\":\"grid-dynamic-content-filter\",\"value\":\"\"},{\"name\":\"grid-dynamic-content-limit\",\"value\":\"1\"},{\"name\":\"grid-dynamic-content-default\",\"value\":\"\"},{\"name\":\"grid-image\",\"value\":\"../content/sample/sample2.jpg\"},{\"name\":\"grid-image-y\",\"value\":\"0\"},{\"name\":\"heading\",\"value\":\"\"},{\"name\":\"video\",\"value\":\"\"},{\"name\":\"url\",\"value\":\"\"},{\"name\":\"link\",\"value\":\"\"},{\"name\":\"grid-content\",\"value\":\"Grid content\"},{\"name\":\"css\",\"value\":\"\"},{\"name\":\"label\",\"value\":\"\"},{\"name\":\"grid-dynamic-content\",\"value\":\"none\"},{\"name\":\"grid-dynamic-content-filter\",\"value\":\"\"},{\"name\":\"grid-dynamic-content-limit\",\"value\":\"1\"},{\"name\":\"grid-dynamic-content-default\",\"value\":\"\"},{\"name\":\"grid-image\",\"value\":\"../content/sample/sample3.jpg\"},{\"name\":\"grid-image-y\",\"value\":\"0\"},{\"name\":\"heading\",\"value\":\"\"},{\"name\":\"video\",\"value\":\"\"},{\"name\":\"url\",\"value\":\"\"},{\"name\":\"link\",\"value\":\"\"},{\"name\":\"grid-content\",\"value\":\"Grid content\"},{\"name\":\"css\",\"value\":\"\"},{\"name\":\"label\",\"value\":\"\"},{\"name\":\"grid-dynamic-content\",\"value\":\"none\"},{\"name\":\"grid-dynamic-content-filter\",\"value\":\"\"},{\"name\":\"grid-dynamic-content-limit\",\"value\":\"1\"},{\"name\":\"grid-dynamic-content-default\",\"value\":\"\"}]', 0, 180, NULL, NULL, '[\"site_header_image.jpg\"]', '[\"# Storiesaround\\nA Content Management System\"]', '[\"right\"]', '[\"bottom\"]', 1, 30000, 1, 'normal', 2, 2, '', '', 1, 3, 0, 0, '', 0, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, '', 0, 0, 0, '', 0, 0, NULL, 0, '', NULL, 0, NULL, 0, NULL, 0, 0, '$dtStart', 1, 0, '', '$dtStart', '$dtStart', '$dtStart', NULL);";
		
						$result3 = $site->setSiteUpdate($sql);
						$reply = $result3 ? date("H:i:s") .' | sample page saved' : null;
						if($result3) {
							echo $reply .'<br />';
						}

					}
				}

				 echo $s;			
			break;
		}
	}
}
?>