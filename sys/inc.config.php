<?php
  
/* site status (development = false)
-------------------------------------------------- */
define('LIVE', false);

/* custom css settings, available in some select lists
-------------------------------------------------- */
$css_custom = array(
	"gf-red-1" => "gf-red-1",
	"gf-red-2" => "gf-red-2",
	"gf-red-12" => "gf-red-12",
	"gf-yellow-1" => "gf-yellow-1",
	"gf-yellow-2" => "gf-yellow-2",
	"gf-yellow-12" => "gf-yellow-12",
	"gf-green-1" => "gf-green-1",
	"gf-green-2" => "gf-green-2",
	"gf-green-12" => "gf-green-12",
	"gf-warm-grey" => "gf-warm-grey",	
	"gf-grid-yellow-1" => "gf-grid-yellow-1",	
	"gf-grid-red-1" => "gf-grid-red-1",
	"gf-grid-green-1" => "gf-grid-green-1",		
	"gf-grid-grey-1" => "gf-grid-grey-1",	
	"frost" => "frost",
	"snow" => "snow",
	"sky" => "sky",
	"wheat-cornsilk" => "wheat-cornsilk",
	"eggwhite" => "eggwhite",
	"white" => "white",
);

/* editor in textareas
// define js configuration like getContent() in cms/libraries/js/functions.js
// include-file folder in cms/libraries/
-------------------------------------------------- */
$editors = array(	
	"(none)" => array("editor" => null, "css-class" => "", "include_js_file" => "", "include_js_script" => ""),
	"tinymce" => array("editor" => "tinymce", "css-class" => "tinymce", "include_js_file" => "tinymce/tinymce.min.js", "include_js_script" => "tinymce/inc.tinymce.js"),
);

?>