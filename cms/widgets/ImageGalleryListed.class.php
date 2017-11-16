<?php

/**
 * API for class Slideshow
 * extends Widgets class
 */

class ImageGalleryListed extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Images gallery listed';
		$a['description'] = 'Show pages images in table, open gallery in colorbox';
		$a['classname'] = 'ImageGalleryListed';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = '';
		// external css in filepath as in libraries, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"limit": "", "background": "#000", "color": "#fff", "tag": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"limit": "numerical", "background": "str", "color": "str", "tag": "alphanumerical"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"limit": "limit number of images", "background": "set background color", "color": "set font color", "tag": "filter images"}';
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

	public function ImageGalleryListed($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);

		$w = ($width>252) ? 474 : 222;
		$limit = isset($objects['limit']) ? $objects['limit'] : $defaults['limit'];
		$limit = filter_var($limit, FILTER_VALIDATE_INT) ? $limit: 100;
		$background = isset($objects['background']) ? $objects['background'] : $defaults['background'];
		$color = isset($objects['color']) ? $objects['color'] : $defaults['color'];
		$tag = isset($objects['tag']) ? $objects['tag'] : $defaults['tag'];
		
		?>
		
		<script>
		
			var token = "<?php echo $_SESSION['token']; ?>";
			var id = <?php echo $pages_widgets_id; ?>;
			var tag = "<?php echo $tag; ?>";
			var cms_dir = "<?php echo $_SESSION['CMS_DIR']; ?>";
			var photo = "<?php echo $this->transl("Photo:"); ?>";

			$.getJSON(cms_dir+"/cms/pages_ajax.php?action=widget_images&pages_widgets_id="+id+"&token="+token+"&tag="+tag+"", function(data){
				
				$.each(data, function(i,item){
					if(i < <?php echo $limit; ?> ) {
					var input = item.filename;
					var input_default = item.filename;
					var page = item.pages_id;
					
					var caption = item.caption;
					var title = item.title.length > 0 ? item.title : '';
					var alt_text = item.alt.length > 0 ? item.alt : item.caption;
					var alt_text = alt_text.length > 0 ? alt_text : '';					
					var alt_rights = item.copyright.length > 0 ? '('+item.copyright+')' : '';
					var alt_rights = item.creator.length > 0 ? photo+' '+item.creator : alt_rights;
					var alt = alt_rights.length > 0 && alt_text.length > 0 ? alt_text +' / '+alt_rights : alt_text;
					
					var $container = "images_<?php echo $pages_widgets_id; ?>";
					input = input.replace("_100.", "_<?php echo $w; ?>.");
					input_full_size = item.filename.replace("_100.", "_726.");
					var img_path = cms_dir+'/content/uploads/pages/'+page+'/' +input;	
					var img_path_full_size = cms_dir+'/content/uploads/pages/'+page+'/' +input_full_size;
					var img_height = Math.round(<?php echo $w; ?>*item.ratio);
					var a_class =  'gallery_<?php echo $pages_widgets_id; ?>';
					

					<?php
					/*
					if ($width <= 326) {
						?>						
						$('<tr><td><a href="'+img_path_full_size+'" class="images_show_<?php echo $pages_widgets_id; ?>" title="'+caption+'"><img src="'+img_path+'" style="display:block;width:100%;height:auto;max-width:100%;max-height:100%;" alt="'+alt+'" title="'+title+'" /></a></td></tr><tr><td style="padding:2px 10px 10px 10px;"><span style="color:<?php echo $color;?>;">'+caption+'</span></td></tr>')
							.appendTo('#images_<?php echo $pages_widgets_id; ?>');
						<?php
					}
					if ($width == 474) {
						?>						
						$('<tr style="vertical-align:middle;"><td style="width:auto;"><a href="'+img_path_full_size+'" class="images_show_<?php echo $pages_widgets_id; ?>" title="'+caption+'"><img src="'+img_path+'" style="display:block;width:100%;height:auto;max-width:100%;max-height:100%;" alt="'+alt+'" title="'+title+'" /></a></td><td style="padding:10px;"><span style="color:<?php echo $color;?>">'+caption+'</span></td></tr>')
							.appendTo('#images_<?php echo $pages_widgets_id; ?>');
						<?php
					}
					*/
					?>
					
					
					$('<div class="gallery_listed"><a href="'+img_path_full_size+'" class="images_show_<?php echo $pages_widgets_id; ?>" title="'+caption+'"><img src="'+img_path+'" style="" alt="'+alt+'" title="'+title+'" /></a><span style="color:<?php echo $color;?>;">'+caption+'</span></div>')
							.appendTo('#images_<?php echo $pages_widgets_id; ?>');

					
					
					}

				});
				
				// images loaded
				jQuery(document).ready(function () {
					$(".images_show_<?php echo $pages_widgets_id; ?>").colorbox({rel:'images_show_<?php echo $pages_widgets_id; ?>'});
				});	
			});			
			
		</script>
		

		<div id="images_<?php echo $pages_widgets_id; ?>" class="gallery_listed_container"></div>
		
		
		<?php	
	}
}
?>