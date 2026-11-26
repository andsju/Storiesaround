<?php
/* 
 * - define settings
 */

  
/* site status (development = false)
-------------------------------------------------*/
define('LIVE', true);


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