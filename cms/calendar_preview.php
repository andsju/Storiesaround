<?php 
// include core
//--------------------------------------------------
require_once 'includes/inc.core.php';

if(!get_role_CMS('administrator') == 1) { header('Location: index.php'); die;}

if(!isset($_SESSION['site_id'])) {
	echo 'Site is not set!';
	exit;
}

// css files, loaded in header.inc.php
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/css/layout.css', 
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

// js files
//--------------------------------------------------
$js_files = array( 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js',
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	//CMS_DIR.'/cms/libraries/js/functions.js',
	CMS_DIR.'/cms/libraries/js/pages_calendar.js'
);

// include header
//--------------------------------------------------
$page_title = "Events Calendar";
$body_style = "width:978px;";
require 'includes/inc.header_minimal.php';


// load javascript files
//--------------------------------------------------
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>


<script>
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

$calendar = new Calendar();

echo '<div class="admin-panel-ruler">';
	echo '<table style="width:100%;"><tr><td><h3 class="admin-heading">Preview</h3></td><td style="text-align:right;"><span class="toolbar_close"><button id="btn_close">Close</button></span></td></tr></table>';
echo '</div>';

?>

<div id="content" style="margin-top:20px;">

	<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
	<div id="dialog_calendar" title="Calendar" style="display:none;"></div>

	<?php
	 
	$href = $_SERVER['PHP_SELF'] ."?date=";
	if(isset($_GET['date'])) {
		$date = (isValidDate($_GET['date'])) ? $_GET['date'] . ' 00:00:00' : null;
	} else {
		$date = null;
	}

	$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ? $_GET['id'] : 0;
	
	// requested type
	$type = filter_var(trim($_GET['type']), FILTER_SANITIZE_STRING);

	function removeqsvar($url, $varname) {
		list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
		parse_str($qspart, $qsvars);
		unset($qsvars[$varname]);
		$newqs = http_build_query($qsvars);
		return $urlpart . '?' . $newqs;
	}

	$href = removeqsvar($_SERVER['REQUEST_URI'], 'date');
	$href = $href .'&date=';
	$period = 'week';
	
	echo '<div id="calendar_include" style="width:100%;">';
	if($type == 'category') {
		echo '<h5>'.$type.'</h5>';
		echo $calendar->getCalendarNav($date, 'category', $id, $period);
		echo $calendar->getCalendarCategoriesRights($date, $href=null, $id, $period);
	}
	if($type == 'view') {
		echo '<h5>'.$type.'</h5>';
		echo $calendar->getCalendarNav($date, 'view', $id, $period);
		echo $calendar->getCalendarViewsRights($date, $href=null, $id, $period);
	}	
	
	echo '</div>';	

	

?>
</div>
<input type="hidden" name="cms_dir" id="cms_dir" value="<?php echo CMS_DIR;?>" />
<div class="footer-wrapper">
	<?php include_once 'includes/inc.footer_cms.php'; ?>
</div>


</body>
</html>