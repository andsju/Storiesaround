<?php

// action php is called from class file and the funtion: 
// action($pages_id, $users_id, $token, $areas)

if (strpos($areas,'header') > 0) {
    $plugin_areas[] = array('header'=> CMS_ABSPATH.'/content/plugins/bookitems/include_bookitems.php');
}


if (strpos($areas,'content') > 0) {
    //$plugin_areas[] = array('content'=>'Template content area');
	$plugin_areas[] = array('content'=> CMS_ABSPATH.'/content/plugins/bookitems/include_bookitems.php');
	
}

if (strpos($areas,'left_sidebar') > 0) {
    $plugin_areas[] = array('left_sidebar'=> CMS_ABSPATH.'/content/plugins/bookitems/include_bookitems.php');
}


if (strpos($areas,'right_sidebar') > 0) {
    $plugin_areas[] = array('right_sidebar'=>CMS_ABSPATH.'/content/plugins/bookitems/include_bookitems.php');
}


if (strpos($areas,'footer') > 0) {
    $plugin_areas[] = array('footer'=> CMS_ABSPATH.'/content/plugins/bookitems/include_bookitems.php');
}

?>