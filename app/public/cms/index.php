<?php

require_once '../cms/includes/inc.core.php';
if(!isset($_SESSION['site_id'])) {
	header('Location: install/install.php');
	die();
}

if(isset($_SESSION['site_domain_url'])) {	
	if($_SESSION['site_domain_url'] != CMS_URL) {
		header('Location: '. $_SESSION['site_domain_url']);
		die();
	} else {
		header('Location: '.CMS_URL.'/cms/pages.php');
	}
}
?>