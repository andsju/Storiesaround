<?php

/**
 * API for class RSS
 * extends Widgets class
 */

class RSS extends Widgets {		
	
	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'RSS';
		$a['description'] = 'Load RSS using Yahoos YUI Library ';
		$a['classname'] = 'RSS';
		// acceptable columns: 'sidebar', 'content' or ''
		$a['column'] = '';
		// external css in filepath in libraries, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"feed": "", "entries": "5", "style": "", "target": "_blank", "content": "true", "date": "true"}';
		return $default;
    }

	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString(), ie url adress "url", digits in range 0-9 "0-9", boolean "true" or "false"
		$default_validate = '{"feed": "url_query", "entries": "0-9", "style": "css", "target": "str", "content": "boolean", "date": "boolean"}';
		return $default_validate;
    }

	public function help() {
		// help text to show in form
		$help = '{"feed": "enter RSS url", "entries": "set number of entries -> 0-9", "style": "set inline css", "target": "use _blank (default) or _self", "content": "show RSS content|description true or false", "date": "show RSS pubDate"}';
		return $help;
   }
	
	public function run($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$feed = isset($objects['feed']) ? $objects['feed'] : $defaults['feed'];
		$style = isset($objects['style']) ? $objects['style'] : null;
		$target = isset($objects['target']) ? $objects['target'] : null;
		$target = "_blank" || "_self" ? $target : "_blank";
		$content = isset($objects['content']) ? $objects['content'] : null;
		$date = isset($objects['date']) ? $objects['date'] : false;
		$date = $date == true || $date == false ? $date : false;
		$entries = isset($objects['entries']) ? $objects['entries'] : 5;
		$entries = ($entries < 10) ? $entries : 1;
		?>
	
		<script src="https://cdnjs.cloudflare.com/ajax/libs/yui/3.18.0/yui/yui-min.js"></script>
		<script>
			var cms_dir = $("#cms_dir").val() != undefined ? $("#cms_dir").val() : '' ;

			YUI().use('yql', function(Y){
				var query = 'select * from rss(0,<?php echo $entries; ?>) where url = "<?php echo $feed; ?>"';
				var q = Y.YQL(query, function(r){
					//r now contains the result of the YQL Query as a JSON
					var date;
					var feedmarkup = '<p>'
					var feed = r.query.results.item // get feed as array of entries
					for (var i=0; i<feed.length; i++){
						feedmarkup += '<div><a href="' + feed[i].link + '" target="<?php echo $target; ?>">' + feed[i].title + '</a></div>';
						if (<?php echo $content; ?>) {
							if (<?php echo $date; ?>) {
								date = new Date(feed[i].pubDate);
								date = date.toISOString().substring(0, 10);
								feedmarkup += '<div><abbr class="timeago" datetime="'+ date +'">'+ date +'</abbr></div>'
							}
							feedmarkup += '<p>' + feed[i].description + '</p>'
						}
					}
					feedmarkup += '</p>';
					document.getElementById('feed_<?php echo $pages_widgets_id; ?>').innerHTML = feedmarkup
				})
			})
			
		</script>
		<div id="feed_<?php echo $pages_widgets_id; ?>" style="<?php echo $style; ?>" class="rss-feed"></div>
		<?php
	}
}
?>