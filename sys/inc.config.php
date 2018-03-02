<?php
  
/* site status (development = false)
-------------------------------------------------- */
define('LIVE', true);

/* custom css settings, available in some select lists
-------------------------------------------------- */
$css_custom = array(
	"newsflash" => "Background yellow, black text",
	"white" => "Background white, black text",
	"ivory" => "Background ivory, black text",
	"navyblue" => "Background navy blue, white text",
);


/* editor in textareas
// define js configuration like getContent() in cms/libraries/js/functions.js
// include-file folder in cms/libraries/
-------------------------------------------------- */
$editors = array(	
	"(none)" => array("editor" => null, "css-class" => "", "include_js_file" => "", "include_js_script" => ""),
	"nicedit" => array("editor" => "nicedit", "css-class" => "nicedit", "include_js_file" => "nicedit/nicEdit.js", "include_js_script" => "nicedit/inc.nicedit.js"),
	"tinymce" => array("editor" => "tinymce", "css-class" => "tinymce", "include_js_file" => "tinymce/tinymce.min.js", "include_js_script" => "tinymce/inc.tinymce.js"),
);

?>