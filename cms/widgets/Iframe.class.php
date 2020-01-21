<?php

/**
 * API for class Vimeo
 * extends Widgets class
 */

class Iframe extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Iframe';
		$a['description'] = 'Embed iframe code';
		$a['classname'] = 'Iframe';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = '';
		$a['css'] = '';
		return $a;
    }
	
	public function default_objects() {
		$default = '{"code": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"code": "code"}';
		return $default_validate;
   }
	
	public function help() {
		// help text to show in form
		$help = '{"code": "enter iframe embed code, for responsive video use add class fluid"}';
		return $help;
   }
	
	public function run($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$code = isset($objects['code']) ? $objects['code'] : $defaults['code'];
			
		?>
		
		<div id="iframe_<?php echo $pages_widgets_id; ?>" class="iframe" style="position: relative; padding-bottom: 56.25%; padding-top: 30px; height: 0; overflow: hidden;"><?php echo $code; ?></div>
		
		<?php
	}
}
?>