<?php
/* 
 * - define settings
 */

  
/* site status (development = false)
-------------------------------------------------*/
define('LIVE', true);


/* custom css settings, available in some select lists
-------------------------------------------------*/
$css_custom = array(
	"stories-content-newsflash" => "Background yellow, black text",
	"stories-content-white" => "Background white, black text",
	"stories-content-ivory" => "Background ivory, black text",
	"stories-content-gold" => "Background gold, black text",
	"stories-content-darkred" => "Background dark red, white text",
	"stories-content-navyblue" => "Background navy blue, white text",
	"stories-content-purple" => "Background purple, white text",
	"stories-content-darkgreen" => "Background dark green, white text",
);


/* editor in textareas
// define js configuration like getContent() in cms/js/functions.js
-------------------------------------------------*/
$editors = array(	
	"(none)" => array("editor" => null, "css-class" => "", "include-file" => ""),
	"nicedit" => array("editor" => "nicedit", "css-class" => "nicedit", "include-file" => "nicedit/inc.nicedit.php"),
	"tinymce" => array("editor" => "tinymce", "css-class" => "tinymce", "include-file" => "tinymce/inc.tinymce.php"),
);


/* ajax online check, in milliseconds
-------------------------------------------------*/
$ajax_online = 60000;


/* delay ajax autosave function in forms, in milliseconds
-------------------------------------------------*/
$ajax_delay_autosave = 120000;


/* ajax autosave function in forms, in milliseconds
-------------------------------------------------*/
$ajax_autosave = 120000;


// moved CMS version to inc.core.php 20140603
// moved handle PDOException to inc.core.php 20140603
?>