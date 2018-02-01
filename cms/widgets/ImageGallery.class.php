<?php

/**
 * API for class ImageGallery
 * extends Widgets class
 */

class ImageGallery extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Image gallery';
		$a['description'] = 'Show pages images gallery in colorbox';
		$a['classname'] = 'ImageGallery';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'content';
		// external css in filepath as in libraries, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"transition": "elastic", "slideshow": "false", "slideshowspeed": "5000", "tag": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"transition": "str", "slideshow": "boolean", "slideshowspeed": "milliseconds", "tag": "alphanumerical"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"transition": "Set to elastic(default), fade, or none", "slideshow": "Show images in slideshow false or true", "slideshowspeed": "transition in milliseconds (5000 ms interval) 5-60 seconds", "tag": "filter images"}';
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
   
	public function ImageGallery($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$transition = isset($objects['transition']) ? $objects['transition'] : $defaults['transition'];
		$slideshow = isset($objects['slideshow']) ? $objects['slideshow'] : $defaults['slideshow'];
		$slideshowspeed = isset($objects['slideshowspeed']) ? $objects['slideshowspeed'] : $defaults['slideshowspeed'];
		$tag = isset($objects['tag']) ? $objects['tag'] : $defaults['tag'];
		
		?>
		
		<script>
		
			$(document).ready(function() {
				var token = "<?php echo $_SESSION['token']; ?>";
				var id = <?php echo $pages_widgets_id; ?>;
				var tag = "<?php echo $tag; ?>";
				var cms_dir = "<?php echo $_SESSION['CMS_DIR']; ?>";
				var photo = "<?php echo $this->transl("Photo:"); ?>";
				var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
				$.getJSON(cms_dir+"/cms/pages_ajax.php?action=widget_images&pages_widgets_id="+id+"&token="+token+"&tag="+tag+"&width="+width+"", function(data){

					$.each(data, function(i,item){
						var input = item.filename;
						var page = item.pages_id;
						var caption = item.caption;
						var title = item.title.length > 0 ? item.title : '';
						var alt_text = item.alt.length > 0 ? item.alt : item.caption;
						var alt_text = alt_text.length > 0 ? alt_text : '';					
						var alt_rights = item.copyright.length > 0 ? '('+item.copyright+')' : '';
						var alt_rights = item.creator.length > 0 ? photo+' '+item.creator : alt_rights;
						var alt = alt_rights.length > 0 && alt_text.length > 0 ? alt_text +' / '+alt_rights : alt_text;						
						var $container = "slideshow_<?php echo $pages_widgets_id; ?>";
						var a_class =  'gallery_<?php echo $pages_widgets_id; ?>';

						$("<a>"+caption+"</a>").attr("href", input).attr("class", a_class).attr("title", title).attr("alt", alt).appendTo("#gallery_container_<?php echo $pages_widgets_id; ?>").wrap("<p></p>");
						
						// first image - append teaser image
						if(i==0) {
							var a_class =  'gallery_open_<?php echo $pages_widgets_id; ?>';
							$("<img />").attr("src", input).css({"width":"100%","height":"auto"}).attr("alt", alt).attr("title", title).appendTo("#gallery_teaser_<?php echo $pages_widgets_id; ?>").wrap("<a href=# class="+a_class+"></a>");
							if(caption.length > 0) {
								$("<span>"+caption+"</span>").appendTo("#gallery_teaser_<?php echo $pages_widgets_id; ?>");
							}
						}
						// count images
						$("#gallery_count_<?php echo $pages_widgets_id; ?>").empty().append(i+1);
						
					});

					// images loaded
					$('a.gallery_<?php echo $pages_widgets_id; ?>').colorbox({rel:'gallery_<?php echo $pages_widgets_id; ?>'});

					// open gallery
					var $gallery = $('a.gallery_<?php echo $pages_widgets_id; ?>').colorbox({rel:'gallery_<?php echo $pages_widgets_id; ?>', width: width+"px", transition:"<?php echo $transition; ?>", slideshow:<?php echo $slideshow; ?>, slideshowSpeed:<?php echo $slideshowspeed; ?>});
					$("a.gallery_open_<?php echo $pages_widgets_id; ?>").click(function(e){
						e.preventDefault();
						$gallery.eq(0).click();
					});
				
				});
			});
		</script>
		
		<p>
			<a class="gallery_open_<?php echo $pages_widgets_id; ?>" href="#"><?php echo $this->transl("Open Gallery"); ?> (<span id="gallery_count_<?php echo $pages_widgets_id; ?>"></span> <?php echo $this->transl("images"); ?>)</a><span class="ui-icon ui-icon-folder-open" style="display:inline-block;"></span>
		</p>

		<div id="gallery_teaser_<?php echo $pages_widgets_id; ?>">
		</div>

		<div id="gallery_container_<?php echo $pages_widgets_id; ?>" style="display:none;">
		</div>
		
		<?php	
	}
}
?>