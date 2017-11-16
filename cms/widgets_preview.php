<?php

// include core
//--------------------------------------------------
include_once 'includes/inc.core.php';
if(!get_role_CMS('contributor') == 1) {die;}

// include session access
//--------------------------------------------------
require_once 'includes/inc.session_access.php';

$pages_id = filter_input(INPUT_GET, 'pages_id', FILTER_VALIDATE_INT) ? $_GET['pages_id'] : 0;
$pages_widgets_id = filter_input(INPUT_GET, 'pages_widgets_id', FILTER_VALIDATE_INT) ? $_GET['pages_widgets_id'] : null;

$pages_widgets = new PagesWidgets();
$rows_widgets = $pages_widgets->getPagesWidgets($pages_id);
//print_r2($rows_widgets);


//$w = new PagesWidgets();
$widgets_css = null;
foreach($rows_widgets as $rows_widget) {
	foreach($rows_widget as $key => $value) {
		if($key == 'widgets_css') {
			$widgets_css[] = $value;
		}
	}
}




// css files, loaded in header.inc.php
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css' );

// add required widgets css
if(is_array($widgets_css)) {	
	// unique values
	$widgets_css = array_unique($widgets_css);
	foreach($widgets_css as $widget_css) {
		if(file_exists($widget_css)) {
			array_push($css_files, CMS_DIR.$widget_css);
		}
	}
}

// load javascript files
//--------------------------------------------------
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/js/functions.js', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-cycle/jquery.cycle2.min.js' 
);

// include header
$meta_author = $meta_keywords = $meta_description = $meta_robots = null;
$page_title = "Preview";
$body_style = 'width:1190px;';
include_once 'includes/inc.header_minimal.php';


echo '<div class="admin-panel-ruler">';
echo '<table style="width:100%;"><tr><td><h4 class="admin-heading">Preview widget: '. $rows_widgets[0]['widgets_title'] .'</h4></td><td style="text-align:right;"><span class="toolbar"><button id="btn_close">Close</button></span></td></tr></table>';
echo '</div>';
echo '<div style="width:978px;padding-top:20px;">';

if($pages_widgets_id) {
	
	$w = new PagesWidgets();
	$row_widget = $pages_widgets->getPagesWidgetsId($pages_widgets_id);
	if($row_widget){ 
		$width = (strpos($row_widget['area'],'sidebar')) ? 222 : 474;
		$width_percent = (strpos($row_widget['area'],'sidebar')) ? 25 : 100;
		echo '<div style="width:'.$width.'px">';
		$w->showPagesWidgets($pages_widgets_id, $pages_id, $width_percent);
		echo '</div>';
	}
}
echo '</div>';


// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>


<script>
	$(document).ready(function() {
		$("#btn_close").click(function(event) {
			event.preventDefault();
			close();
		});
		$( ".toolbar button" ).button({
			icons: {
				secondary: "ui-icon-close"
			},
			text: true
		});
	});
</script>	


</div>
</body>
</html>