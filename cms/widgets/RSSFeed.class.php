<?php

/**
 * API for class RSSFeed
 * extends Widgets class
 */

class RSSFeed extends Widgets {		
	
	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'RSS';
		$a['description'] = 'Load google feed...';
		$a['classname'] = 'RSSFeed';
		// acceptable columns: 'sidebar', 'content' or ''
		$a['column'] = '';
		// external css in filepath in libraries, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"feed": "", "entries": "4", "style": "", "snippet": "true", "content": "false"}';
		return $default;
    }

	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString(), ie url adress "url", digits in range 0-9 "0-9", boolean "true" or "false"
		$default_validate = '{"feed": "url_query", "entries": "0-9", "style": "css", "snippet": "boolean", "content": "boolean"}';
		return $default_validate;
    }

	public function help() {
		// help text to show in form
		$help = '{"feed": "enter RSS url", "entries": "set number of entries -> 0-9", "style": "set inline css", "snippet": "show RSS snippets true or false", "content": "show RSS contetn true or false"}';
		return $help;
   }
	
	public function RSSFeed($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$feed = isset($objects['feed']) ? $objects['feed'] : $defaults['feed'];
		$style = isset($objects['style']) ? $objects['style'] : null;
		$snippet = isset($objects['snippet']) ? $objects['snippet'] : null;
		$content = isset($objects['content']) ? $objects['content'] : null;
		$entries = isset($objects['entries']) ? $objects['entries'] : "3";
		$entries = ($entries < 10) ? $entries : "1";
		?>
	

		<script>
		
			google.load("feeds", "1");

			function feedLoaded_<?php echo $pages_widgets_id; ?>(result) {
				if (!result.error) {
					var container = document.getElementById("feed_<?php echo $pages_widgets_id; ?>");
					container.innerHTML = '';

					for (var i = 0; i < result.feed.entries.length; i++) {
						var entry = result.feed.entries[i];
						var div = document.createElement("div");			  
						div.className = "rss-entry";

						var titleDiv = document.createElement("div");
						titleDiv.className = "rss-entry-title";

						var entryLink = document.createElement("a");				
						entryLink.setAttribute("target","_blank");
						entryLink.href = entry.link;
						entryLink.appendChild(document.createTextNode(entry.title));
						titleDiv.appendChild(entryLink);

						var snippetDiv = document.createElement("div");
						snippetDiv.className = "rss-entry-snippet";
						snippetDiv.innerHTML = entry.contentSnippet;

						var contentDiv = document.createElement("div");
						contentDiv.className = "rss-entry-content";
						contentDiv.innerHTML = entry.content;
						
						div.appendChild(titleDiv);
						<?php if($snippet=="true") {?>
							div.appendChild(snippetDiv);
						<?php }?>
						<?php if($content=="true") {?>
							div.appendChild(contentDiv);
						<?php }?>

						container.appendChild(div);
					}
				}
			}

			function OnLoad() {
				var feed = new google.feeds.Feed("<?php echo $feed; ?>");
				var entries = <?php echo $entries; ?>;
				feed.setNumEntries(entries);
				feed.load(feedLoaded_<?php echo $pages_widgets_id; ?>);
			}

			google.setOnLoadCallback(OnLoad);
		
		</script>
		<div id="feed_<?php echo $pages_widgets_id; ?>" style="<?php echo $style; ?>" class="rss-feed"></div>
		<?php
	}
}
?>