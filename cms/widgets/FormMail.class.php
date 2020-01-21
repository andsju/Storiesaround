<?php

/**
 * API for class FormMail
 * extends Widgets class
 */

class FormMail extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'FormMail';
		$a['description'] = 'Send SMTP mail';
		$a['classname'] = 'FormMail';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'content';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = null;
		return $a;
    }
	
	public function default_objects() {
		$default = '{"subject": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"subject": "str"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"subject": "Set subject"}';
		return $help;
   }
	
	public function run($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);
		$w = ($width==474) ? 474 : 726;
		$subject = isset($objects['subject']) ? $objects['subject'] : $defaults['subject'];
		
		?>
		
		<script>
		
			
		</script>
		
		<div id="formmail_wrapper">
			<div id="formmail_<?php echo $pages_widgets_id; ?>" style="background:#fcfcfc;padding:10px;border:1px dotted #000;">
				<label for="field_formmail_<?php echo $pages_widgets_id; ?>">Email</label>
				<br />
				<p>
					<input id="field_formmail_<?php echo $pages_widgets_id; ?>" type="text" /
				</p>
				<button id="btn_formmail_<?php echo $pages_widgets_id; ?>">Send</button>
			</div>
		</div>
		<?php	
	}
}
?>