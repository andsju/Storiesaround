<?php

/**
 * API for class Vimeo
 * extends Widgets class
 */

class Vimeo extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Vimeo';
		$a['description'] = 'Embed Vimeo video in an iframe';
		$a['classname'] = 'Vimeo';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = '';
		$a['css'] = '';
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
	
	public function Vimeo($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
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
				var iframe = '<iframe src="https://player.vimeo.com/video/'+videoID+'?title=0&byline=0&autoplay=0" width="'+w+'" height="'+h+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
				$("#"+videoDiv).append(iframe);
			});
		</script>
		
		<div id="videoDiv_<?php echo $pages_widgets_id; ?>"></div>
		
		<?php
	}
}
?>