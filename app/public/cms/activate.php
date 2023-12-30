<?php

/*** include core ***/
//--------------------------------------------------
include_once 'includes/inc.core.php';

// activate account
include('includes/inc.activate.php');


// css files, loaded in header.inc.php
//--------------------------------------------------
$css_files = array(
	CMS_DIR . '/cms/css/normalize.css',
	CMS_DIR . '/cms/css/layout.css',
	CMS_DIR . '/cms/libraries/jquery-ui/jquery-ui.css',
	CMS_DIR . '/cms/libraries/jquery-colorbox/colorbox.css'
);

	
// add css theme
$theme = isset($_SESSION['site_theme']) ? $_SESSION['site_theme'] : '';

if (file_exists(CMS_ABSPATH . '/content/themes/' . $theme . '/style.css')) {
	array_push($css_files, CMS_DIR . '/content/themes/' . $theme . '/style.css');
}

$js_files = array();

// load javascript files, loads before footer.inc.php
//--------------------------------------------------
$js_files = array(
	CMS_DIR . '/cms/libraries/jquery-ui/jquery-ui.custom.min.js',
	CMS_DIR . '/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js',
	CMS_DIR . '/cms/libraries/js/functions.js',
	CMS_DIR . '/cms/libraries/jquery-colorbox/jquery.colorbox-min.js'
);


/*** load java script files, loads before include footer.inc.php ***/
//--------------------------------------------------
$js_files = array(
	CMS_DIR . '/libraries/jquery-ui/jquery-ui.custom.min.js',
	CMS_DIR . '/libraries/js/functions.js',
	CMS_DIR . '/libraries/jquery-colorbox/jquery.colorbox-min.js'
);


// page title
//--------------------------------------------------
$page_title = "Account activation";
$body_style = '';

include 'includes/inc.header_minimal.php';

?>

<script type="text/javascript">
	$(document).ready(function() {
	
		$(".colorbox_login").colorbox({
			width:"500px", 
			height:"275px", 
			iframe:true, 
			transition:"none",
			onClosed:function(){ 
				location.reload(true); 
			}
		});
		
	});
</script>

<?php
echo "\n" . '<div id="site-id">';
// include site header
include_once_customfile('includes/inc.site_header.php', $arr = array(), $languages);
echo "\n" . '</div>';

echo "\n" . '<div class="clearfix">&nbsp;</div>';
?>


<div style="margin:10px;padding:20px;">
<h3><?php echo translate("Account activated", "registration_account_activated", $languages); ?></h3> 
<p>
<a class="colorbox_login" href="login.php" style="font-size:1.3em;"><?php echo translate("You can now login", "registration_account_activated_login", $languages); ?></a>
</p>
</div>

<div id="footer-wrapper">			
	<?php include_once_customfile('includes/inc.footer.php', $arr = array(), $languages); ?>			
</div>

<?php
// load javascript files
// unique values
$js_files = array_unique($js_files);

foreach ($js_files as $js) {
	echo "\n" . '<script type="text/javascript" src="' . $js . '"></script>';
}
?>

</body>
</html>