<?php

// include core 
//--------------------------------------------------
include_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';
$pages = new Pages();
if(!get_role_CMS('contributor') == 1) {die;}
$pages = new Pages();
$selections = new Selections();


if(isset($_GET['token'])){
	// only accept $_POST from this §_SESSION['token']
	if($_GET['token'] == $_SESSION['token']) {
		$id = $_GET['id'];		
		
		$row = $selections->getSelectionsContent($id);

		$css_files = array(
			CMS_DIR.'/cms/css/normalize.css', 
			CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
			CMS_DIR.'/cms/css/layout.css', 
			CMS_DIR.'/cms/css/pages_edit.css', 
			CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css'
		);

		// css files... add css jquery-ui theme
		if(isset($_SESSION['site_ui_theme'])) {
			$ui_theme = '/cms/libraries/jquery-ui/theme/'.$_SESSION['site_ui_theme'].'/jquery-ui.css';
			if(file_exists(CMS_ABSPATH .$ui_theme)) {
				if (($key = array_search(CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', $css_files)) !== false) {
					unset($css_files[$key]);
				}
				array_push($css_files, CMS_DIR . $ui_theme);
			}
		}

		$js_files = array(
			CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
			CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
			CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js',
			CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
			CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js',	
			CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
			CMS_DIR.'/cms/libraries/js/pages.js',
			CMS_DIR.'/cms/libraries/js/functions.js'
		);
		
		$external_js_files = explode(" ",$row[0]['external_js']);
		foreach($external_js_files as $external_js){
			if(strlen(trim($external_js))>0){
				array_push($js_files, trim($external_js));
			}
		}
		
		$external_css_files = explode(" ",$row[0]['external_css']);
		foreach($external_css_files as $external_css){
			if(strlen(trim($external_css))>0){
				array_push($css_files, trim($external_css));
			}
		}


		// include header
		$page_title = "Preview";
		$body_style = "width:1190px;margin-top:50px;";
		include_once 'includes/inc.header_minimal.php';

		?>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#btn_close").click(function(event) {
					event.preventDefault();
					parent.$.colorbox.close();
				});
				$( ".toolbar_close button" ).button({
					icons: { secondary: "ui-icon-close" },
					text: true
				});
			});
		</script>

		<?php
		echo '<div class="admin-panel-ruler">';
			echo '<table style="width:100%;"><tr><td><h3 class="admin-heading">Preview selection: '. $row[0]['name'] .'</h3></td><td style="text-align:right;"><span class="toolbar_close"><button id="btn_close">Close</button></span></td></tr></table>';
		echo '</div>';
		echo '<div style="width:100%;padding-top:20px;">';

			// content_html
			//parse_storiesaround_coded($pages, $dtz, $row[0]['content_html'],0);
			// content_code
			$a = parse_storiesaround_coded($pages, $dtz, $row[0]['content_code'],0);
			$a = implode($a);
			echo $a;

		echo '</div>';
	}
}
// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>

<div id="browser_size_helper" style="position:absolute;top:10px;left:10px;z-index:999;background:#fff;color:#000;width:200px;padding:5px;border:1px solid grey;opacity:0.5;">
	<div id="browser_size" style="">Browser width (px)
	<select id="browser_size_fixed" style="margin:5px 0 5px 5px;">
		<option value="0"></option>
		<option value="320">320</option>
		<option value="480">480</option>
		<option value="640">640</option>
		<option value="768">768</option>
		<option value="1024">1024</option>
		<option value="1280">1280</option>
		<option value="1366">1366</option>
		<option value="1536">1536</option>
		<option value="1680">1680</option>
	</select>
	</div>
</div>

</body>
</html>