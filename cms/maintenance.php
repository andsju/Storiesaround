<?php
// include core 
//--------------------------------------------------
require_once 'includes/inc.core.php';

if($_SESSION['site_maintenance'] == 0) {
	header("Location: index.php");
}

// css files, loaded in header.inc.php
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
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

$js_files = array();

// page title
//--------------------------------------------------
$page_title = "Site maintenenace";
$body_style = '';

include 'includes/inc.header_minimal.php';

if($_SESSION['site_maintenance'] == 1) {

	echo "\n".'<div id="site-id">';
		// include site header
		include_once_customfile('includes/inc.site_header.php', $arr=array(), $languages);
	echo "\n".'</div>';

	echo "\n".'<div class="clearfix">&nbsp;</div>';

	echo '<div style="margin:10px;padding:20px;">';
		echo '<h2>'.translate("Site is under maintenance. Functionality is limited, please visit later", "site_maintenance", $languages).'</h2>';

		$z = new Site();
		$site = $z->getSite();
		if($site) {			
			echo '<div style="margin:20px 0;">'.nl2br($site['site_maintenance_message']).'</div>';
		}
	echo '</div>';

}
?>

<div id="footer-wrapper">			
	<?php include_once_customfile('includes/inc.footer.php', $arr=array(), $languages); ?>			
</div>


</body>
</html>

<?php
session_destroy();
session_unset();		
?>