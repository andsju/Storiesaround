<?php

// include core 
//--------------------------------------------------
require_once '../../../cms/includes/inc.core.php';

include_once CMS_ABSPATH . '/cms/includes/inc.functions_pages.php';


// include session access 
//--------------------------------------------------
require_once CMS_ABSPATH . '/cms/includes/inc.session_access.php';


// access right, minimum, hierarchy matters
//--------------------------------------------------
if(!get_role_CMS('administrator') == 1) {
	if(!get_role_LMS('administrator') == 1) {
		header('Location: '. $_SESSION['site_domain_url']);	exit;
	}
}


// css files, loaded in inc.header.php 
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-datatables/style.css',
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/cms/plugins/bookitems/css/style.css' );

	
// add css jquery-ui theme
$ui_theme = isset($_SESSION['site_ui_theme']) ? $_SESSION['site_ui_theme'] : '';
if(file_exists(CMS_ABSPATH .'/cms/libraries/jquery-ui/theme/'.$ui_theme.'/jquery-ui.css')) {
	if (($key = array_search(CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', $css_files)) !== false) {
		unset($css_files[$key]);
	}
	array_push($css_files, CMS_DIR.'/cms/libraries/jquery-ui/theme/'.$ui_theme.'/jquery-ui.css');
}

//--------------------------------------------------
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/js/functions.js', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js', 
	CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js'
);

?>
<!DOCTYPE html>
<html lang="">

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Administration - plugin HTMLpage</title>

	<?php 
	//load css files
	foreach ( $css_files as $css ) {
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen,projection" href="'.$css.'" />';
	}; 
	echo "\n";
	echo "\n\t".'<script src="'.CMS_DIR.'/cms/libraries/jquery/jquery.min.js"></script>';
	echo "\n\t".'<script src="'.CMS_DIR.'/cms/libraries/google-code/google.jsapi.js"></script>';
	echo "\n";
	?>
	
	
	
</head>

<body style="width:1200px;background:#fafafa;">


<?php

$htmlpage = new HTMLpage();

$t = (isset($_GET['t'])) ? $_GET['t'] : null;

// url
$this_url = $_SERVER['PHP_SELF'] .'?t='. $t;


echo '<img src="'.CMS_DIR.'/cms/css/images/storiesaround_logotype_black.png" alt="Storiesaround logotype" style="width:120px;float:right;"/>';
echo '<h3 class="plugin-text">plugin</h3>';
echo '<div class="admin-heading">';
	
	echo '<div class="float">';
		echo '<div class="cms-ui-icons cms-ui-plugins"></div>';
		echo '<div class="float">';
			echo '<a href="'.CMS_HOME.'/help/plugins/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">HTMLpage</h1></a>';
		echo '</div>';
	echo '</div>';
	echo '<div class="ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
		get_tab_menu_jquery_ui_look_alike($_SERVER["PHP_SELF"], array("info"), array("Info"), "t", "", null, $ui_ul_add_class="ui-two", $ui_a_add_class="");	
	echo '</div>';	
	
echo '</div>';


echo '<input type="hidden" name="users_id" id="users_id" value="'.$_SESSION['users_id'].'" />';
echo '<input type="hidden" name="cms_dir" id="cms_dir" value="'.CMS_DIR.'" />';


echo '<div class="admin-area-outer">';
	echo '<div class="admin-area-inner">';

		$row = $htmlpage->info();
		print_r2($row);

	echo '</div>';
echo '</div>';

?>

<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
<input type="hidden" id="users_id" name="users_id" value="<?php echo $_SESSION['users_id']; ?>">

<?php
// load javascript files
foreach ( $js_files as $js ) {
	echo "\n".'<script src="'.$js.'"></script>';
}
?>


<script>
	// enable log
	jQuery.fn.log = function (msg) {
	  console.log("%s: %o", msg, this);
	  return this;
	};	
</script>

<?php include_once CMS_ABSPATH . '/cms/includes/inc.footer_cms.php'; ?>

</body>
</html>