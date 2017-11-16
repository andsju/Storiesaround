<?php

/**
 * API for class YoutubeSimple
 * extends Widgets class
 */

class YoutubeSimple extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'YoutubeSimple';
		$a['description'] = 'Load Youtube video';
		$a['classname'] = 'YoutubeSimple';
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
	
	public function YoutubeSimple($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$videoID = isset($objects['videoID']) ? $objects['videoID'] : $defaults['videoID'];
		
		?>
		
		<script>google.load("swfobject", "2.2");</script>
		<script>

			function _run() {
				// load
				var videoID = "<?php echo $videoID; ?>";
				var videoDiv = "videoDiv_<?php echo $pages_widgets_id; ?>"; 
				var w = Math.round(document.body.clientWidth*<?php echo $width; ?>/100-30);
				var h = Math.round(w/1.6);
				
				// params scale: "exactFit", 
				var params = { allowScriptAccess: "always", wmode : "transparent", allowFullScreen: "true" };
				// The element id of the Flash embed
				var atts = { id: "ytPlayer" };
				// SWFObject (http://code.google.com/p/swfobject/)
				swfobject.embedSWF("http://www.youtube.com/v/" + videoID + "?version=3&enablejsapi=1&playerapiid=player1", videoDiv, w, h, "<?php echo $_SESSION['site_flash_version']; ?>", null, null, params, atts);
			}
			google.setOnLoadCallback(_run);
		
		</script>
		
		<div id="videoDiv_<?php echo $pages_widgets_id; ?>"></div>
		
		<?php
	}
}
?>