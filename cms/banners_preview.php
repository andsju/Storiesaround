<?php 
// include core
//--------------------------------------------------
require_once 'includes/inc.core.php';

if(!get_role_CMS('contributor') == 1) { header('Location: index.php'); die;}

if(!isset($_SESSION['site_id'])) {
	echo 'Site is not set!';
	exit;
}


// css files
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css' );


// include header
//--------------------------------------------------
$page_title = "Banners previes";
$body_style = "width:1190px;";
require 'includes/inc.header_minimal.php';

// js files
//--------------------------------------------------
$js_files = array( 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js',
	CMS_DIR.'/cms/libraries/js/functions.js' 
);


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
		$( ".toolbar button" ).button({
			icons: { secondary: "ui-icon-close" },
			text: true
		});

	});
</script>


<?php
$b = null;

if(isset($_GET['token'])){
	// only accept $_POST from this §_SESSION['token']
	if($_GET['token'] == $_SESSION['token']) {

		// check $_GET id
		$id = array_key_exists('id', $_GET) ? $_GET['id'] : null;
		if($id == null) { die;}

		$banners = new Banners();
		$b = $banners->getBannerId($id);

	}
}
if(!$b) {
	die;
}

echo '<div class="admin-panel-ruler">';
echo '<table style="width:100%;"><tr><td><h3 class="admin-heading">Preview banner: '. $b['name'] .'</h3></td><td style="text-align:right;"><span class="toolbar"><button id="btn_close">Close</button></span></td></tr></table>';
echo '</div>';

if($b) {
	$i = 0;
	echo '<script type="text/javascript">';
		$ext = pathinfo($b['file'], PATHINFO_EXTENSION);
		if($ext=='swf') {
			echo 'swfobject.embedSWF("../content/uploads/ads/'.$b['file'].'", "'.$b['name'].'", "'.$b['width'].'", "'.$b['height'].'", "'.$_SESSION['site_flash_version'].'"); ';
		}
		echo '$(document).ready(function() { ';
		if($ext=='swf') {
			echo '$("#wrapper-ads").append("<div id='.$b['name'].'>'.$b['name'].'</div>"); ';
		} else {
			echo '$("#wrapper-ads").wrap("<a href='.$b['url'].' target='.$b['url_target'].'></a>").append("<img src=../content/uploads/ads/'.$b['file'].' width='.$b['width'].' height='.$b['height'].' />"); ';
		}
		echo '}) ;';
	echo '</script>';

}

?>
<div id="wrapper-ads" style="padding-top:20px;"></div>

</body>
</html>