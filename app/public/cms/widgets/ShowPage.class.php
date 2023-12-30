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
		$a['column'] = 'content';
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
   
	public function run($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
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
				var width = <?php echo $width; ?>;
				ids = ids.split(",");
				console.log(width);
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
					console.log("scrollPercent", scrollPercent);
					if(scrollPercent > 50) {
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
								
								var title = "<h1>" + item.title + item.ratio +"</h1>";
								var content = "<p>" + item.content + "</p>";

								// no image

								// tiny image

								// smaller image

								// full scal
								
								var img_width = getOptimizedImageWidth(width, item.sizes);

								switch (img_width) {
									case undefined:
									case null:
									case "":

									break;

									case 

								}



								
								var swap_image = getImage(item.filename, img_width);
								// handle ratio and background-posistion-y
								// display image in 2.39:1
								var div_height = 0;
								var position = 0;
								if (item.ratio < 0.418) {
									div_height = item.ratio * width;
									position = 0;
								} else {
									div_height = 0.418 * width;
									position = item.ratio < 0.8 ? 20 : 30;
								}
								var style = "width:100%;height: "+div_height+"px;background-position-y:"+position+"%;background-size:100%;background-repeat:no-repeat";
								var image = "<div style=\"background-image: url("+cms_dir+"/content/uploads/pages/"+item.pages_id+"/"+swap_image+");"+style+"\" class=\"article-image\">bild</div>";

								//$("<article>"+title+image+content+"</article").insertAfter($("#wrapper-content"));
								$("#wrapper-content").append($("<article>"+title+image+content+"</article"));
								
								//$("#wrapper-content").append("<h1>" + item.title + "</h1>");
								//var img_width = getOptimizedImageWidth(width, item.sizes);
								//var swap_image = getImage(item.filename, img_width);
								//if (swap_image.length) {
								//	$("#wrapper-content").append("<img src="+cms_dir+"/content/uploads/pages/"+item.pages_id+"/"+swap_image +">");
								//}
								//$("#wrapper-content").append("<p>" + item.content + "</p>");
							});
						});
					}
				});
			});


			function getOptimizedImageWidth(wrapper_width, sizes) {
				if (!sizes) { return "100";}

				var arr = sizes.split(",");
				for (var i = 0; i < arr.length; i++) {
					if (wrapper_width <= arr[i]) {
						return arr[i];
					}
				}
			}

			function getImageFileExtension(filename) {
				if (!filename) {return ""}
				return filename.split('.').pop();
			}
			function getImageBaseName(filename) {
				if (!filename) {return ""}
				return filename.split('_').shift();
			}

			function getImage(image_default, width) {
				if (!image_default) {return ""}
				var ext = getImageFileExtension(image_default);
				var base = getImageBaseName(image_default);
				return base + "_" + width + "." + ext;
			}


		</script>
				
		<?php	
	}
}
?>