<?php

/**
 * API for class HtmlContent
 * extends Widgets class
 */

class HtmlContent extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'HtmlContent';
		$a['description'] = 'Show html content. Select file from path CMS_DIR /content/uploads/html/';
		$a['classname'] = 'HtmlContent';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = '';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"file": "lorem.html", "style": "background:#FFF;"}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"file": "str", "style": "css"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"file": "Set file from cms path", "style": "Style html content wrapper"}';
		return $help;
   }
	
	public function HtmlContent($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$w = ($width==474) ? 474 : 726;
		$file = isset($objects['file']) ? $objects['file'] : $defaults['file'];
		$style = isset($objects['style']) ? $objects['style'] : $defaults['style'];
		?>
		
		<script>
		
			var token = "<?php echo $_SESSION['token']; ?>";
			var id = <?php echo $pages_widgets_id; ?>;
			var cms_dir = "<?php echo $_SESSION['CMS_DIR']; ?>";
			var file = "<?php echo $file; ?>";
			var style = "<?php echo $style; ?>";
	
			$.get( "<?php echo $_SESSION['CMS_DIR']; ?>/content/uploads/html/"+file, function(data) {
				$( "#htmlcontent_<?php echo $pages_widgets_id; ?>" ).html(data);
			});
			
		</script>
		
		<div class="htmlcontent_wrapper" id="htmlcontent_<?php echo $pages_widgets_id; ?>" style="<?php echo $style; ?>">
		</div>
		<?php	
	}
}
?>