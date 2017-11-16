<?php if(!defined('VALID_INCL')){header('Location: index.php'); die;} ?>

<?php

// include core
//--------------------------------------------------
require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if(!get_role_CMS('administrator') == 1) {
	if(!get_role_LMS('administrator') == 1) {
		die;
	}
}

$plugins = new Plugins();
$plugins->initiate();

$p = $plugins->getPluginsInDatabase();
?>
