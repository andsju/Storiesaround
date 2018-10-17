<?php

/**
 * API for class ImageGalleryGrid
 * extends Widgets class
 */

class ImageGalleryGrid extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'ImageGalleryGrid';
		$a['description'] = 'Show pages images in a grid';
		$a['classname'] = 'ImageGalleryGrid';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'content';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = null;		
		return $a;
    }
	
	public function default_objects() {
		$default = '{"caption": "true", "tag": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"caption": "boolean", "tag": "alphanumerical"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"caption": "true or false", "tag": "filter images"}';
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
   
	public function ImageGalleryGrid($action, $pages_widgets_id=null, $pages_id=null, $width=null) {

		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$ratio = isset($objects['ratio']) ? $objects['ratio'] : $defaults['ratio'];
		if(strstr($ratio,':')) {
			$wh = explode(':', $ratio);
			$wish_ratio = $wh[1]/$wh[0];
		} else {
			$wish_ratio = 1;
		}
		$tag = isset($objects['tag']) ? $objects['tag'] : $defaults['tag'];
		$caption = isset($objects['caption']) ? $objects['caption'] : $defaults['caption'];
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
						var caption = item.caption_extended;
						var tag = item.tag;
						var title = item.title.length > 0 ? item.title : '';
						title += tag;
						var alt_text = item.alt.length > 0 ? item.alt : item.caption;
						var alt_text = alt_text.length > 0 ? alt_text : '';					
						var alt_rights = item.copyright.length > 0 ? '('+item.copyright+')' : '';
						var alt_rights = item.creator.length > 0 ? photo+' '+item.creator : alt_rights;
						var alt = alt_rights.length > 0 && alt_text.length > 0 ? alt_text +' / '+alt_rights : alt_text;
						
						
						var img = '<img src="'+input+'">';
						$("<div class=\"widgets-grid-cell\"><div class=\"image-holder\">"+img+"</div><div class=\"image-caption\">"+caption+"</div></div></div>").appendTo("#grid_images_<?php echo $pages_widgets_id; ?>");
						// <div class=\"grid-design gf-grid-green-1\">
						//<div class=\"gf-design\" style=\"height:10px\"></div>
					});
										
				});
				
			});
			
		</script>

		<?php
		$html = '<div class="grid_images_wrapper">';
		$html .= '<div id="grid_images_'. $pages_widgets_id .'" class="grid_images">';
		$html .= '</div>';
		$html .= '</div>';
		
		return $html;
		?>
		
		<?php
	}
}
?>
