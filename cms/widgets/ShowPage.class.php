<?php

/**
 * API for class ShowPage
 * extends Widgets class
 */

class ShowPage extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Show page';
		$a['description'] = 'Show more pages';
		$a['classname'] = 'ShowPage';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'sidebar';
		// external css in filepath as in libraries, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"ids": "", "tag": "", "search": "", "limit": "1"}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"ids": "comma_separated_numbers", "tag": "characters", "search": "boolean", "limit": "0-9"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"ids": "Comma separated pages_id", "tag": "tag", "search": "Search matched pages true|false", "limit": "Limit number of pages"}';
		return $help;
   }

	private function transl($text) {
		$a = array(
			"english" => array("Open Gallery" => "Open Gallery", "images" => "images", "Photo:" => "Photo:"), 
			"swedish" => array("Open Gallery" => "Öppna Galleri", "images" => "bilder", "Photo:" => "Foto:"));

		$l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
		if(!$l) {
			$l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
		} 
		$s = $l ? $a[$l][$text] : $text;
		echo $s;
	}
   
	public function ShowPage($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$ids = isset($objects['ids']) ? $objects['ids'] : $defaults['ids'];
		$tag = isset($objects['tag']) ? $objects['tag'] : $defaults['tag'];
		$search = isset($objects['search']) ? $objects['search'] : $defaults['search'];
		$limit = isset($objects['limit']) ? $objects['limit'] : $defaults['limit'];
		?>
		
		<script>
		
			$(document).ready(function() {
		
				var token = "<?php echo $_SESSION['token']; ?>";
				var id = <?php echo $pages_widgets_id; ?>;
				var pages_id = $("#pages_id").val();
				var ids = "<?php echo $ids; ?>";
				var tag = "<?php echo $tag; ?>";
				var search = "<?php echo $search; ?>";
                var limit = "<?php echo $limit; ?>";
				var cms_dir = "<?php echo $_SESSION['CMS_DIR']; ?>";
				var processing = true;
				
				ids = ids.split(",");

				var ids_checked = [];

				for (var i = 0; i < limit; i++ ) {
					if (ids.length) {
						var ids_ok = ids.shift();	
						ids_checked.push(ids_ok);
					}
				}
				console.log("ids_checked", ids_checked);

				$(document).scroll(function(e){

					// grab the scroll amount and the window height
					var scrollAmount = $(window).scrollTop();
					var documentHeight = $(document).height();

					// calculate the percentage the user has scrolled down the page
					var scrollPercent = (scrollAmount / documentHeight) * 100;

					if(scrollPercent > 70) {
						// run a function called doSomething
						if (processing) {
							doSomething();
						}
						
					}

					function doSomething() { 
						processing = false; //resets the ajax flag once the callback concludes

						// do something when a user gets 50% of the way down my page
						$.getJSON(cms_dir+"/cms/pages_ajax.php?action=widget_show_page&pages_widgets_id="+id+"&pages_id="+pages_id+"&token="+token+"&ids="+ids_checked+"&limit="+limit+"&tag="+tag+"&search="+search+"", function(data){
							console.log(data);
							
							$.each(data, function(i,item){
								$("#wrapper-content").append("<h1>" + item.title + "</h1>");
								$("#wrapper-content").append("<img src="+cms_dir+"/content/uploads/pages/"+item.pages_id+"/"+item.filename +">");
								$("#wrapper-content").append("<p>" + item.content + "</p>");								
							});
						});
					}
				});
			});
		</script>
				
		<?php	
	}
}
?>