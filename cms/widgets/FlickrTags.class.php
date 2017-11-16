<?php

/**
 * API for class FlickrTags
 * extends Widgets class
 */

class FlickrTags extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Flickr';
		$a['description'] = 'Load public flickr images by tags...';
		$a['classname'] = 'FlickrTags';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'sidebar';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"tags": "nature", "tagmode": "all", "flickr_user_id": "", "count": "5", "timeout": "10000"}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"tags": "words", "tagmode": "any-all", "flickr_user_id": "str", "count": "1-20", "timeout": "milliseconds"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"tags": "set search words", "tagmode": "any-all, control whether items must have ALL the tags or ANY", "flickr_user_id": "Enter a flickr user_id", "count": "1-20", "timeout": "slideshow duration in milliseconds (5000 ms interval) 5-60 seconds"}';
		return $help;
   }
	
	public function FlickrTags($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$tags = isset($objects['tags']) ? $objects['tags'] : $defaults['tags'];
		$tagmode = isset($objects['tagmode']) ? $objects['tagmode'] : $defaults['tagmode'];
		$flickr_user_id = isset($objects['flickr_user_id']) ? $objects['flickr_user_id'] : $defaults['flickr_user_id'];
		$count = isset($objects['count']) ? $objects['count'] : $defaults['count'];
		$timeout = isset($objects['timeout']) ? $objects['timeout'] : $defaults['timeout'];
		$api_key = 'b377953c8c1c4a5f84452b5ec1793af0';
		?>
		
		<script>
			$(document).ready(function() {
					
				var flickr_div = "widgets-flickr-id-<?php echo $pages_widgets_id; ?>";
				var tags = "<?php echo $tags; ?>";
				var tagmode = "<?php echo $tagmode; ?>";
				var count = "<?php echo $count; ?>";
				var timeout = "<?php echo $timeout; ?>";
				var flickr_user_id = "<?php echo $flickr_user_id; ?>";
				var id = <?php echo $pages_widgets_id; ?>;

				$.getJSON("http://api.flickr.com/services/feeds/photos_public.gne?id="+flickr_user_id+"&tags="+tags+"&tagmode="+tagmode+"&format=json&jsoncallback=?",
				function(data){
					var i = 0;
					$.each(data.items, function(i,item){
						var adjust = 'margin: 0 0 0 -11px;';
						$("<img/>").attr("src", item.media.m).attr("style", adjust).appendTo("#widgets-flickr-id-<?php echo $pages_widgets_id; ?>")
						.wrap("<a href='" + item.link + "' target=_blank></a>");
						if ( i == count-1 ) return false;
					});
					$("#widgets-flickr-id-<?php echo $pages_widgets_id; ?>").cycle({
						fx: 'fade',
						speed: 500,
						next: '#next<?php echo $pages_widgets_id; ?>',
						prev: '#prev<?php echo $pages_widgets_id; ?>',
						timeout: + timeout
					});
				});

			});
		</script>
		
		<div class="widgets-flickr-wrapper" style="width:100%;">
			<div style="float:left">
			<img src="<?php echo $_SESSION['CMS_DIR']; ?>/cms/css/images/flickr.png" title="flickr tag: <?php echo $tags; ?>" />
			</div>
			<div style="float:right">
				<a id="prev<?php echo $pages_widgets_id; ?>" href="#">&laquo;&laquo;</a>
				|
				<a id="next<?php echo $pages_widgets_id; ?>" href="#">&raquo;&raquo;</a>
			</div>
			<div class="clearfix"></div>
			<div id="widgets-flickr-id-<?php echo $pages_widgets_id; ?>" style="background:#000;height:160px;width:218px;border:2px solid #000;padding:0;margin:0;overflow:hidden;"></div>
		</div>
		<?php
	}
}
?>