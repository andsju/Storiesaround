<?php

/**
 * API for class Slideshow
 * extends Widgets class
 */

class Slideshow extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Slideshow';
		$a['description'] = 'Show pages images slideshow';
		$a['classname'] = 'Slideshow';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'content';
		// external css in filepath as in libraries, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"timeout": "5000", "transition": "1000", "tag": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"timeout": "milliseconds", "transition": "milliseconds", "tag": "alphanumerical"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"timeout": "slideshow duration in milliseconds (5000 ms interval) 5-60 seconds", "transition": "transition time between slides in milliseconds", "tag": "filter images"}';
		return $help;
   }
	
	public function Slideshow($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$w = ($width==474) ? 474 : 726;
		$transition = isset($objects['transition']) ? $objects['transition'] : $defaults['transition'];
		$timeout = isset($objects['timeout']) ? $objects['timeout'] : $defaults['timeout'];
		$tag = isset($objects['tag']) ? $objects['tag'] : $defaults['tag'];
		
		?>		
		<script>
		
			var token = "<?php echo $_SESSION['token']; ?>";
			var id = <?php echo $pages_widgets_id; ?>;
			var tag = "<?php echo $tag; ?>";
			var cms_dir = "<?php echo $_SESSION['CMS_DIR']; ?>";

			$.getJSON(cms_dir+"/cms/pages_ajax.php?action=widget_images&pages_widgets_id="+id+"&token="+token+"&tag="+tag+"", function(data){
				
				$.each(data, function(i,item){
					var input = item.filename;
					var page = item.pages_id;
					var $container = "slideshow_<?php echo $pages_widgets_id; ?>";
					input = input.replace("_100.", "_<?php echo $w; ?>.");
					var img_path = cms_dir+'/content/uploads/pages/'+page+'/' +input;					
					var img_height = Math.round(<?php echo $w; ?>*item.ratio)+'px';
					var caption = item.caption;
					var style_li =  'position:absolute;top:0;left:0;';
					$("<img />").attr("src", img_path).css({"width":"100%","height":"auto"}).attr("alt", caption).appendTo("#slideshow_<?php echo $pages_widgets_id; ?>").wrap("<li></li>").attr("style", style_li);
					$("ul#slideshow_<?php echo $pages_widgets_id; ?>").attr("style", "position:relative;min-height:"+img_height);
				});
				
				// images loaded
				var $slider = $('#slideshow_<?php echo $pages_widgets_id; ?>'); // class or id of carousel slider
				var $slide = 'li'; // could also use 'img' if you're not using a ul
				var $transition_time = parseInt(<?php echo $transition; ?>);
				var $time_between_slides = parseInt(<?php echo $timeout; ?>); 

				function slides(){
					return $slider.find($slide);
				}

				slides().fadeOut();
				slides().first().addClass('active');
				slides().first().fadeIn($transition_time);
				
				// auto scroll 
				$interval = setInterval(
				function(){
					var $i = $slider.find($slide + '.active').index();

					slides().eq($i).removeClass('active');
					slides().eq($i).fadeOut($transition_time);

					if (slides().length == $i + 1) $i = -1; // loop to start

						slides().eq($i + 1).fadeIn($transition_time);
						slides().eq($i + 1).addClass('active');
					}
				, $transition_time +  $time_between_slides 
				);
			});
			
		</script>
		
		<div id="slideshow_wrapper" style="overflow:hidden;">
			<ul id="slideshow_<?php echo $pages_widgets_id; ?>" style="position:relative;"></ul>
		</div>
		<?php	
	}
}
?>