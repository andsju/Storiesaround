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
		$default_validate = '{"code": "any"}';
		return $default_validate;
   }
	
	public function help() {
		// help text to show in form
		$help = '{"code": "paste iframe embed code (https source only), it is scaled responsively (16:9)"}';
		return $help;
   }

	// Returns safe iframe markup built from the first iframe found in $code, or '' if none is valid.
	private function sanitize_iframe($code) {
		if (!is_string($code) || stripos($code, '<iframe') === false || !class_exists('DOMDocument')) {
			return '';
		}

		$allowed = array('src', 'width', 'height', 'title', 'frameborder', 'allow', 'allowfullscreen', 'loading', 'referrerpolicy', 'name', 'scrolling', 'class');
		$boolean = array('allowfullscreen');

		$dom = new DOMDocument();
		$previous = libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="UTF-8"><body>' . $code . '</body>', LIBXML_NOERROR | LIBXML_NOWARNING);
		libxml_clear_errors();
		libxml_use_internal_errors($previous);

		$iframe = $dom->getElementsByTagName('iframe')->item(0);
		if (!$iframe) {
			return '';
		}

		$src = trim($iframe->getAttribute('src'));
		if (!preg_match('~^(https:)?//[^\s/?#]+~i', $src)) {
			return '';
		}

		$html = '<iframe';
		foreach ($iframe->attributes as $attr) {
			$name = strtolower($attr->name);
			if (!in_array($name, $allowed, true)) {
				continue;
			}
			if (in_array($name, $boolean, true)) {
				$html .= ' ' . $name;
			} else {
				$html .= ' ' . $name . '="' . htmlspecialchars($attr->value, ENT_QUOTES, 'UTF-8') . '"';
			}
		}
		return $html . '></iframe>';
	}
	
	public function run($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$code = isset($objects['code']) ? $objects['code'] : $defaults['code'];
		$code = $this->sanitize_iframe($code);
		$id = intval($pages_widgets_id);
			
		?>
		
		<style>#iframe_<?php echo $id; ?> iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }</style>
		<div id="iframe_<?php echo $id; ?>" class="iframe" style="position: relative; padding-bottom: 56.25%; padding-top: 30px; height: 0; overflow: hidden;"><?php echo $code; ?></div>
		
		<?php
	}
}
?>