<?php

/**
 * API for class Youtube
 * extends Widgets class
 */

class Youtube extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Youtube';
		$a['description'] = 'Embed Youtube video in an iframe';
		$a['classname'] = 'Youtube';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = '';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"code": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"code": "html"}';
		return $default_validate;
   }
	
	public function help() {
		// help text to show in form
		$help = '{"code": "enter iframe embed code"}';
		return $help;
   }
	
	public function Youtube($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$code = isset($objects['code']) ? $objects['code'] : $defaults['code'];
			
		?>
		
		<div id="youtube_<?php echo $pages_widgets_id; ?>"><?php echo $code; ?></div>
		
		<?php
	}
}
?>