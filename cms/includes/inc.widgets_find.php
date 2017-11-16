<?php if(!defined('VALID_INCL')){header('Location: index.php'); die;} ?>

<?php

// include core
//--------------------------------------------------
require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if(!get_role_CMS('user') == 1) {die;}

$widgets = new Widgets();
$widgets->initiate();

$w = $widgets->getWidgetsInDatabase();
?>
