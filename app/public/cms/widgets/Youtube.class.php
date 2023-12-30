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
		$a['description'] = 'Load Youtube video';
		$a['classname'] = 'Youtube';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = '';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"videoID": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"videoID": "str"}';
		return $default_validate;
   }
	
	public function help() {
		// help text to show in form
		$help = '{"videoID": "enter clip videoID"}';
		return $help;
   }
	
	public function run($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$videoID = isset($objects['videoID']) ? $objects['videoID'] : $defaults['videoID'];
		?>
		<script>
			$(document).ready(function() {
				var w = <?php echo $width; ?>;
				var h = w * 0.5625;			
				var videoID = "<?php echo $videoID; ?>";
				var videoDiv = "videoDiv_<?php echo $pages_widgets_id; ?>"; 
				var iframe = '<iframe src="https://www.youtube.com/embed/' + videoID +'" width="'+w+'" height="'+h+'" frameborder="0" allow="encrypted-media" allowfullscreen></iframe>';
				$("#"+videoDiv).append(iframe);
			});
		</script>
		
		<div id="videoDiv_<?php echo $pages_widgets_id; ?>"></div>
		<?php
	}
}
?>