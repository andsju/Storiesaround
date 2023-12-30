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
		$a['description'] = 'Show html content.';
		$a['classname'] = 'HtmlContent';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = '';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"content": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"content": "any"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"content": "Add html content"}';
		return $help;
   }
	
	public function run($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$content = isset($objects['content']) ? $objects['content'] : $defaults['content'];
		?>
		
		<script>
			$(document).ready(function() {

				var token = "<?php echo $_SESSION['token']; ?>";
				var id = <?php echo $pages_widgets_id; ?>;
				var cms_dir = "<?php echo $_SESSION['CMS_DIR']; ?>";
				var content = "<?php echo $content; ?>";

				$("#htmlcontent_<?php echo $pages_widgets_id; ?>").html(content);

			});
		
			
		</script>
		
		<div id="htmlcontent_<?php echo $pages_widgets_id; ?>" class="clearfix">
		</div>
		<?php	
	}
}
?>