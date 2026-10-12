<?php
// include core 
//--------------------------------------------------
include_once '../includes/inc.core.php';


// overall
// --------------------------------------------------
if (isset($_POST['token'])) {
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {

		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		switch ($action) {

			case "db_setup":

				include '../install/inc.db_schema.php';

				$site = new Site();
				$i = 0;

				if (is_array($sql_tables)) {
					foreach ($sql_tables as $sql) {
						$result = $site->setSiteUpdate($sql);
						$reply = $result ? date("H:i:s") . ' | success' : null;
						if ($result) {
							$i++;
							echo $reply . '<br />';
						}
					}
				}

				break;

			case "site_install":

				$s = null;

				$site_name = trim($_POST['site_name']);
				$site_domain_url = filter_var(trim($_POST['site_domain_url']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$site_domain = filter_var(trim($_POST['site_domain']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$user_name = filter_var(trim($_POST['user_name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$password = $_POST['password'];
				$site_seo_url = 1;
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

				// check if email address is available
				$users = new Users;
				$check = $users->getUsersEmail($email);

				if ($check) {
					echo 'You have entered an email address that is already registered: <b>' . $email . '</b>. Please change email or delete current user. Reload browser and try again.';
					die();
				}

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

				$site_id = $site->setSiteInstall($site_name, $site_domain_url, $site_domain, $site_email, $site_copyright, $site_language, $site_timezone, $site_wysiwyg, $site_theme, $site_ui_theme, $site_header_image, $site_logotype, $site_seo_url, $utc_modified);

				if ($site_id) {

					$s .= '<p>' . $icon_check . ' Saved site: <i>' . $site_name . '</i></p>';
					$_SESSION['site_name'] = $site_name;
					$_SESSION['site_domain_url'] = $site_domain_url;
					$_SESSION['site_domain'] = $site_domain;
					$_SESSION['site_theme'] = $site_theme;
					$_SESSION['site_ui_theme'] = $site_ui_theme;
					$_SESSION['site_logotype'] = $site_logotype;
					$_SESSION['site_header_image'] = $site_header_image;
					$_SESSION['site_timezone'] = $site_timezone;
					$_SESSION['site_wysiwyg'] = $site_wysiwyg;
					$_SESSION['site_seo_url'] = $site_seo_url;

					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$history = new History();
					$history->setHistory($site_id, 'site_id', 'INSERT', describe('site install', $site_name), 0, $_SESSION['token'], $utc_modified);
					$pass_hash = password_hash($password, PASSWORD_DEFAULT);
					$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$result = $users->setUsersAdmin($email, $pass_hash, $first_name, $last_name, $user_name, $utc_created);
					if ($result) {

						$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
						$_SESSION['users_id'] = $result;
						$_SESSION['first_name'] = $first_name;
						$_SESSION['last_name'] = $last_name;
						$_SESSION['email'] = $email;
						$_SESSION['user_name'] = $user_name;
						$_SESSION['role_CMS'] = 6;
						$_SESSION['site_maintenance'] = 0;
						$_SESSION['debug'] = 0;

						$s .= '<p>' . $icon_check . ' Saved user name: <b>' . $user_name . ', ' . $email . '</b></p>';
						$s .= '<h3>Done!</h3>';
						$s .= '<p>When you click the link below you will proceed to ' . $site_domain_url . '</p>';
						$s .= '<p>Login with your username and password to start publish content - <a href="' . CMS_DIR . '/cms/pages.php?id=1" class="obvious">take me to ' . $site_domain_url . '</a></b></p>';
						$s .= '<p><strong>Rename file "./cms/install/install.php" to avoid running a new installation setup...</strong></p>';

						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history = new History();
						$history->setHistory($result, 'users_id', 'INSERT', describe('superadministrator', $site_name), 0, $_SESSION['token'], $utc_modified);

						// setup sample page
						$site = new Site();
						$dt = new DateTime();
						$dtStart = $dt->format('Y-m-d H:i:s');

						$sql = "
						INSERT INTO `pages` 
							(
								`parent_id`, 
								`parent`, 
								`access`, 
								`status`, 
								`title`, 
								`content`, 
								`content_author`, 
								`pages_id_link`, 
								`header_image`, 
								`grid_active`, 
								`grid_area`, 
								`grid_content`, 
								`utc_created`, 
								`utc_modified`, 
								`utc_start_publish`)
						VALUES
							(
								0, 
								0,
								2, 
								2, 
								'Sample page', 
								'Storiesaround CMS installed...!', 
								'admin', 
								'sample-page', 
								'[\"site_header_image.jpg\"]', 
								1, 
								0, 
								'[{\"name\":\"grid-image\",\"value\":\"../content/sample/sample1.jpg\"},{\"name\":\"grid-image-y\",\"value\":\"0\"},{\"name\":\"heading\",\"value\":\"\"},{\"name\":\"video\",\"value\":\"\"},{\"name\":\"url\",\"value\":\"\"},{\"name\":\"link\",\"value\":\"\"},{\"name\":\"grid-content\",\"value\":\"Grid content\"},{\"name\":\"css\",\"value\":\"\"},{\"name\":\"label\",\"value\":\"\"},{\"name\":\"grid-dynamic-content\",\"value\":\"none\"},{\"name\":\"grid-dynamic-content-filter\",\"value\":\"\"},{\"name\":\"grid-dynamic-content-limit\",\"value\":\"1\"},{\"name\":\"grid-dynamic-content-default\",\"value\":\"\"},{\"name\":\"grid-image\",\"value\":\"../content/sample/sample2.jpg\"},{\"name\":\"grid-image-y\",\"value\":\"0\"},{\"name\":\"heading\",\"value\":\"\"},{\"name\":\"video\",\"value\":\"\"},{\"name\":\"url\",\"value\":\"\"},{\"name\":\"link\",\"value\":\"\"},{\"name\":\"grid-content\",\"value\":\"Grid content\"},{\"name\":\"css\",\"value\":\"\"},{\"name\":\"label\",\"value\":\"\"},{\"name\":\"grid-dynamic-content\",\"value\":\"none\"},{\"name\":\"grid-dynamic-content-filter\",\"value\":\"\"},{\"name\":\"grid-dynamic-content-limit\",\"value\":\"1\"},{\"name\":\"grid-dynamic-content-default\",\"value\":\"\"},{\"name\":\"grid-image\",\"value\":\"../content/sample/sample3.jpg\"},{\"name\":\"grid-image-y\",\"value\":\"0\"},{\"name\":\"heading\",\"value\":\"\"},{\"name\":\"video\",\"value\":\"\"},{\"name\":\"url\",\"value\":\"\"},{\"name\":\"link\",\"value\":\"\"},{\"name\":\"grid-content\",\"value\":\"Grid content\"},{\"name\":\"css\",\"value\":\"\"},{\"name\":\"label\",\"value\":\"\"},{\"name\":\"grid-dynamic-content\",\"value\":\"none\"},{\"name\":\"grid-dynamic-content-filter\",\"value\":\"\"},{\"name\":\"grid-dynamic-content-limit\",\"value\":\"1\"},{\"name\":\"grid-dynamic-content-default\",\"value\":\"\"}]', 
								'$dtStart', 
								'$dtStart', 
								'$dtStart'
							);";


						$pages_id = $site->setSiteUpdate($sql);

						if ($pages_id) {

							// create folder
							if (!is_dir(CMS_ABSPATH . "/content/uploads/pages/" . $pages_id)) {
								mkdir(CMS_ABSPATH . "/content/uploads/pages/" . $pages_id, 0777);
							}

							// update site domain url
							// https://localhost/cms/pages.php?id='. $pages_id;
							// https://localhost/pages/sample-page

							$new_site_domain_url = $_SESSION['site_domain_url'] . '/pages/sample-page';
							$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

							$site_update = $site->setSiteDomainURL($site_id, $new_site_domain_url, $utc_modified);
							$_SESSION['site_domain_url'] = $new_site_domain_url;
						}

						$reply = $pages_id ? date("H:i:s") . ' | sample page saved' : null;
						if ($pages_id) {
							echo $reply . '<br />';
						}
					}
				}

				echo $s;
				break;
		}
	}
}
