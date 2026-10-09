<?php
  
/* site status (development = false)
-------------------------------------------------- */
define('LIVE', true);


/* editor in textareas
// define js configuration like getContent() in cms/libraries/js/functions.js
// include-file folder in cms/libraries/
-------------------------------------------------- */
$editors = array(	
	"(none)" => array("editor" => null, "css-class" => "", "include_js_file" => "", "include_js_script" => ""),
	"tinymce" => array("editor" => "tinymce", "css-class" => "tinymce", "include_js_file" => "tinymce/tinymce.min.js", "include_js_script" => "tinymce/inc.tinymce.js"),
);

?>