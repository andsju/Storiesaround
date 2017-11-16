<?php
// include core 
//--------------------------------------------------
require_once 'includes/inc.core.php';

if(!isset($_SESSION['site_id'])) {
	echo 'Site is not set!';
	exit;
}

$pages = new Pages();

// css files, loaded in header.inc.php
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css' );

$page_title = "Sitetree";			

// include header 
$body_style = "width:1260px;";
			
require 'includes/inc.header_minimal.php';

$id = array_key_exists('id', $_GET) ? $_GET['id'] : 0;
$id = filter_var($id, FILTER_VALIDATE_INT) ? $id : 0;
$href = 'pages_edit.php';

get_pages_tree_sitemap_all($parent_id=0, $id, $path=get_breadcrumb_path_array($id), $a=true, $seo=true, $href, $open=true, $depth=1, $show_pages_id = false);

?>
</body>
</html>