<?php

/**
 * API for class Dewplayer
 * extends Widgets class
 */

class Dewplayer extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Dewplayer';
		$a['description'] = 'Play mp3 files (http://www.alsacreations.fr/dewplayer-en.html)';
		$a['classname'] = 'Dewplayer';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'sidebar';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"file": "mp3"}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"file": "str"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"file": "Set mp3 file in this directory"}';
		return $help;
   }

   
	private function transl($text) {
		$a = array(
			"english" => array("Open Gallery" => "Open Gallery", "images" => "images"), 
			"swedish" => array("Open Gallery" => "Öppna Galleri", "images" => "bilder"));

		$l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
		if(!$l) {
			$l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
		} 
		$s = $l ? $a[$l][$text] : $text;
		echo $s;
	}
   
	public function Dewplayer($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$w = ($width==474) ? 222 : 222;
		$file = isset($objects['file']) ? $objects['file'] : $defaults['file'];

		$path = '../content/uploads/pages/'.$pages_id;
		$file_meta = $path .'/'.$file;
		?>	
		
		<script>google.load("swfobject", "2.2");</script>

		<object type="application/x-shockwave-flash" data="../cms/libraries/dewplayer/dewplayer-rect.swf?mp3=<?php echo $file_meta; ?>" width="222" height="20" id="dewplayer">
			<param name="wmode" value="transparent" />
			<param name="movie" value="../cms/libraries/dewplayer/dewplayer-rect.swf?mp3=<?php echo $file_meta; ?>" />
		</object>
		
		<?php	
	}
}
?>