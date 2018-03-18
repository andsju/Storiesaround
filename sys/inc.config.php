<?php
  
/* site status (development = false)
-------------------------------------------------- */
define('LIVE', false);

/* custom css settings, available in some select lists
-------------------------------------------------- */
$css_custom = array(
	"gf-rod-1" => "Röd 1",
	"gf-rod-3" => "Röd 3",
	"darkred" => "Röd 3b",
	"gf-rod-5" => "Röd 5",
	"gf-rod-5b" => "Röd 6",

	"gf-gul-1" => "Gul 1",
	"gf-gul-2" => "Gul 2",
	"gf-gul-12" => "Gul 12",
	"gf-gron-2" => "Grön 2",
	"gf-gron-3" => "Grön 3",
	"sun-on-the-horizon" => "sun-on-the-horizon",
	"the-strain" => "the-strain",
	"frost" => "frost",
	"green-olive" => "green-olive",
	"light-yellow" => "light-yellow",
	"sky" => "sky",
	"snow" => "snow",
	"wheat-cornsilk" => "wheat-cornsilk",
	"gr1" => "gr1",
	"gr2" => "gr2",
	"gr23" => "gr23",
	"wheat-grey" => "wheat-grey",
	"white" => "white",
	//"ivory" => "Background ivory, black text",
	//"navyblue" => "Background navy blue, white text",
);


/*

					{title : 'Röd 1', block : 'div', classes : 'gf-rod-1'},
					{title : 'Röd 2', block : 'div', classes : 'gf-rod-2'},
					{title : 'Röd 3', block : 'div', classes : 'gf-rod-3'},
					{title : 'Gul 1', block : 'div', classes : 'gf-gul-1'},
					{title : 'Gul 2', block : 'div', classes : 'gf-gul-2'},
					{title : 'Grön 1', block : 'div', classes : 'gf-gron-1'},
					{title : 'Grön 2', block : 'div', classes : 'gf-gron-2'},
					{title : 'Grön 3', block : 'div', classes : 'gf-gron-3'},
*/

/* editor in textareas
// define js configuration like getContent() in cms/libraries/js/functions.js
// include-file folder in cms/libraries/
-------------------------------------------------- */
$editors = array(	
	"(none)" => array("editor" => null, "css-class" => "", "include_js_file" => "", "include_js_script" => ""),
	"tinymce" => array("editor" => "tinymce", "css-class" => "tinymce", "include_js_file" => "tinymce/tinymce.min.js", "include_js_script" => "tinymce/inc.tinymce.js"),
);

?>