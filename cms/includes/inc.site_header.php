<?php
if ($_SESSION['site_title_position'] == 1) {
	echo '<a id="site-name-heading" href="'.$_SESSION['site_domain_url'].'">';
	echo '<div id="site-heading">'. $_SESSION['site_name'] .'</div>';
	if(isset($_SESSION['site_slogan'])) {
		echo '<div id="site-slogan-heading">'. $_SESSION['site_slogan'] .'</div>';
	}
	echo '</a>';
}
?>