<?php 
/* include core 
-------------------------------------------------- */
require_once 'inc.core.php';

if(!defined('VALID_INCL')){die();} 

/* session access
-------------------------------------------------- */
if(get_role_CMS('author') == 1) {
	// MoxieManager SessionAuthenticator
	$_SESSION["MoxieManagerisLoggedIn"] = true;
}
?>