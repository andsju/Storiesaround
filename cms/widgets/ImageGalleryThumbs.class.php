<?php

/**
 * API for class ImageGalleleryThumbs
 * extends Widgets class
 */

class ImageGalleryThumbs extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'ImageGalleryThumbs';
		$a['description'] = 'Show pages images slideshow';
		$a['classname'] = 'ImageGalleryThumbs';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'content';
		// external css in filepath as in libraries, '../libraries/?/?.css
		$a['css'] = CMS_DIR . '/cms/libraries/jquery-cycle/jquery.cycle2.css';
		return $a;
    }
	
	public function default_objects() {
		$default = '{"timeout": "7000", "speed": "1000", "ratio": "4:3", "control": "true", "thumbs": "true", "caption": "true", "caption_css": "", "copyright": "true", "tag": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"timeout": "milliseconds", "speed": "milliseconds", "ratio": "ratio", "control": "boolean", "thumbs": "boolean", "caption": "boolean", "caption_css": "css", "copyright": "boolean", "tag": "alphanumerical"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"timeout": "slideshow duration in milliseconds (100ms interval 100ms-1 second | 1000 ms interval 1-60 seconds)", "speed": "slideshow transitions speed milliseconds (100ms interval 100ms-1 second | 1000 ms interval 1-60 seconds)", "ratio": "set width:height ratio, allowed -> 1:1, 4:3, 16:9, 21:9 (crop image & adjust vertical)", "control": "show slideshow control", "thumbs": "show navigation thumbs", "caption": "true or false", "caption_css": "inline css styling caption", "copyright": "show copyright, set caption to true", "tag": "filter images"}';
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
   
	public function ImageGalleryThumbs($action, $pages_widgets_id=null, $pages_id=null, $width=null) {

		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$timeout = isset($objects['timeout']) ? $objects['timeout'] : $defaults['timeout'];
		$speed = isset($objects['speed']) ? $objects['speed'] : $defaults['speed'];
		$ratio = isset($objects['ratio']) ? $objects['ratio'] : $defaults['ratio'];
		if(strstr($ratio,':')) {
			$wh = explode(':', $ratio);
			$wish_ratio = $wh[1]/$wh[0];
		} else {
			$wish_ratio = 1;
		}
		$tag = isset($objects['tag']) ? $objects['tag'] : $defaults['tag'];
		$control = isset($objects['control']) ? $objects['control'] : $defaults['control'];
		$thumbs = isset($objects['thumbs']) ? $objects['thumbs'] : $defaults['thumbs'];
		$caption = isset($objects['caption']) ? $objects['caption'] : $defaults['caption'];
		$caption_css = isset($objects['caption_css']) ? $objects['caption_css'] : $defaults['caption_css'];
		$copyright = isset($objects['copyright']) ? $objects['copyright'] : $defaults['copyright'];		
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
						
						var $container = "slideshow_extended_<?php echo $pages_widgets_id; ?>";
						var wish_ratio = "<?php echo $wish_ratio; ?>";
						// adjust img position if wished ratio is less than original ratio, use Golden ratio 0.618...
						if(wish_ratio < item.ratio) {
							var std_height = Math.round(item.ratio*<?php echo $width; ?>);
							var wish_height = Math.round(wish_ratio*<?php echo $width; ?>);
							var offset = Math.round((std_height-wish_height)*(1-0.618));
						}
						
						var style_slideshow_extended = (typeof offset != 'undefined') ? 'margin: -'+offset+'px 0 0 0;width:100%;' : 'margin: 0 0 0 0;width:100%;';	
						// add images not using attr height
						$("<img />").attr("src", input).attr("alt", alt).attr("title", title).attr("data-cycle-caption", caption).attr("style", style_slideshow_extended).appendTo("#slideshow_extended_<?php echo $pages_widgets_id; ?>");
						// add thumbs
						var style_thumbs = 'padding:2px 4px 2px 4px;';
						$("<img />").attr("src", input).attr("width", "50").attr("height", 50*item.ratio).attr("style", style_thumbs).attr("class", "slideshow_extended_thumbs").attr("title", caption).attr("data-caption", caption).appendTo("#slideshow_extended_thumbs_<?php echo $pages_widgets_id; ?>")
						.click(function() { 
							$("#slideshow_extended_<?php echo $pages_widgets_id; ?>").cycle(i); 
							return false;
						});
					});
					
					// images loaded - shoq & cycle...
					$('.slideshow_extended_wrapper').show();
					$('#slideshow_extended_<?php echo $pages_widgets_id; ?>').cycle();
					
					$('#slideshow_extended_<?php echo $pages_widgets_id; ?>').on('cycle-after',function(e, optionHash, outgoingSlideEl, incomingSlideEl, forwardFlag){
						$('#slideshow_extended_descr_<?php echo $pages_widgets_id; ?>').html(incomingSlideEl.data);
					});
					
				});
				
				$(function() {
					$("#slideshow_extended_previous_<?php echo $pages_widgets_id; ?>").button({
						text: false,
						icons: { primary: "ui-icon-carat-1-w" }
					});
					$("#slideshow_extended_btn_play_<?php echo $pages_widgets_id; ?>").button({
						text: false,
						icons: { primary: "ui-icon-play" }
					});
					$("#slideshow_extended_btn_play_<?php echo $pages_widgets_id; ?>").click(function() { 
						$('#slideshow_extended_<?php echo $pages_widgets_id; ?>').cycle('resume'); 
					});				
					$("#slideshow_extended_btn_pause_<?php echo $pages_widgets_id; ?>").button({
						text: false,
						icons: { primary: "ui-icon-pause" }
					});
					$("#slideshow_extended_btn_pause_<?php echo $pages_widgets_id; ?>").click(function() { 
						$('#slideshow_extended_<?php echo $pages_widgets_id; ?>').cycle('pause'); 
					});
					$("#slideshow_extended_next_<?php echo $pages_widgets_id; ?>").button({
						text: false,
						icons: { primary: "ui-icon-carat-1-e" }
					});
				});
				
			});
			
		</script>

		<div class="slideshow_extended_wrapper" style="display:none;">
		
			<?php if($control=="true") { ?>
			
				<div class="slideshow_extended_nav" style="float:left">
					<a id="slideshow_extended_prev<?php echo $pages_widgets_id; ?>" href="#"><button id="slideshow_extended_previous_<?php echo $pages_widgets_id; ?>" class="slideshow_extended_control">previous</button></a>
					<button id="slideshow_extended_btn_pause_<?php echo $pages_widgets_id; ?>" class="slideshow_extended_control">pause</button>
					<button id="slideshow_extended_btn_play_<?php echo $pages_widgets_id; ?>" class="slideshow_extended_control">play</button>
					<a id="slideshow_extended_next<?php echo $pages_widgets_id; ?>" href="#"><button id="slideshow_extended_next_<?php echo $pages_widgets_id; ?>" class="slideshow_extended_control">next</button></a>
				</div>
				<div id="slideshow_extended_info_<?php echo $pages_widgets_id; ?>" class="slideshow_extended_counter" style="vertical-align:bottom;float:right;">&nbsp;</div>
				<div class="clearfix"></div>

			<?php } ?>

			<div id="slideshow_extended_<?php echo $pages_widgets_id; ?>" class="slideshow_extended" 
			data-cycle-caption="#slideshow_extended_descr_<?php echo $pages_widgets_id;?>" 
			data-cycle-caption-template="{{caption}}"
			data-cycle-speed="<?php echo $speed;?>"
			data-cycle-timeout="<?php echo $timeout;?>"
			data-cycle-log="false"
			data-cycle-prev="#slideshow_extended_previous_<?php echo $pages_widgets_id; ?>"
			data-cycle-next="#slideshow_extended_next_<?php echo $pages_widgets_id; ?>"
			>				
			</div>
			
			<?php 
			$styled = (strlen($caption_css)>0) ? $caption_css : '';	
			if($caption=="true") {
				echo '<div class="cycle-caption slideshow_extended_caption" id="slideshow_extended_descr_'.$pages_widgets_id.'" style="'.$styled.'"></div>';
			}
			if($thumbs=="true") {
				echo '<div id="slideshow_extended_thumbs_'. $pages_widgets_id .'" style="background:black;"></div>';
			}
			?>
			
		</div>
		<?php
	}
}
?>