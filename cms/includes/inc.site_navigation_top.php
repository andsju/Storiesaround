<?php
// include file
if(!defined('VALID_INCL')){die();}
echo '<a id="site-name-heading" href="'.$_SESSION['site_domain_url'].'">';
	echo '<div id="site-navigation-top-box1">';
		if ($_SESSION['site_title_position'] == 0) {
			echo '<div id="site-heading">'. $_SESSION['site_name'] .'</div>';
			if(isset($_SESSION['site_slogan'])) {
				echo '<div id="site-slogan-heading">'. $_SESSION['site_slogan'] .'</div>';
			}			
		}
	echo '</div>';
echo '</a>';

echo '<div id="site-navigation-top-box2">';
	echo date("Y-m-d");
	echo ' | ';
	if(isset($_SESSION['site_domain_url'])) {
		echo '<a href="'.$_SESSION['site_domain_url'].'" class="std">'.translate("Start", "site_start_page", $languages).'</a> | ';
	}

	// display links based upon login status
	if (!isset($_SESSION['users_id'])) {
		echo ' <a href="'.CMS_DIR.'/cms/login.php">'.translate("Login", "site_login", $languages).'</a> ';
	}
		
	echo '<div id="search_site"><input type="text" id="pages_s" class="search" value=""><button id="btn_pages_search" class="magnify">'.translate("Search", "site_search", $languages).'</button></div>';
echo '</div>';

?>