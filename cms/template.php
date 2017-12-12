<?php
// include core 
require_once 'includes/inc.core.php';

if(!isset($_SESSION['site_id'])) { die;}

if($_SESSION['site_maintenance'] == 1 && $_SESSION['role_CMS'] < 4) { header('Location: maintenance.php');}

// seo friendly url 
$request = substr($_SERVER['REQUEST_URI'], strlen(CMS_DIR.'/'));
$request_parts = explode('/', $request);

// $request_parts now holds: [0] htaccess rewrite folder, [1] seo title 
$id = array_key_exists('id', $_GET) ? $_GET['id'] : $request_parts[1];

// initiate class
$pages = new Pages();

// try to find id, if alphanumeric seo link -> get id
if(isset($id)) {
	$id = filter_var($id, FILTER_VALIDATE_INT) ? $id : filter_var($id, FILTER_SANITIZE_STRING);
	if(!is_numeric($id)) {
        $result = $pages->getPagesSeo($id);
        $id = $result ? $result['pages_id'] : null;
	}
}
//print_r2($_SERVER['REQUEST_URI']);
//print_r2($request);
//print_r2($request_parts);
//print_r($id);

// get_start_page

//access page as known or unknown user to get page content
$users_id = (isset($_SESSION['users_id'])) ? $_SESSION['users_id'] : null;

// get page content
$arr = $pages->getPagesContent($id);
//print_r2($arr);


$acc_read = get_rights($id, $users_id, $arr['access']);
//print_r2("acc_read: ". $acc_read);

// might be sample

// get this page widgets
$pages_widgets = new PagesWidgets();
$rows_widgets = $pages_widgets->getPagesWidgets($id);
$widgets_css = get_widgets_css($rows_widgets);


// page title
$page_title = strlen($arr['title_tag'])>0 ? $arr['title_tag'] : $arr['title'];

// meta tags
$meta_keywords = (strlen($arr['meta_keywords'])>0) ? $arr['meta_keywords'] : null;
$meta_description = (strlen($arr['meta_description'])>0) ? $arr['meta_description'] : null;
$meta_robots = (strlen($arr['meta_robots'])>0) ? $arr['meta_robots'] : null;
$meta_additional = (strlen($arr['meta_additional'])>0) ? $arr['meta_additional'] : null;

// css files, loaded in header.inc.php
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/cms/libraries/jquery-flexnav/flexnav.css',
    //CMS_DIR.'/cms/css/layout.css',
    CMS_DIR.'/cms/css/style.css'
);

$css_files = add_css_themes($css_files);
$css_files = add_css_widgets($css_files, $widgets_css);
//print_r2($css_files);

// load javascript files, loads before footer.inc.php
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/js/functions.js', 
	CMS_DIR.'/cms/libraries/js/pages.js', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js',
	CMS_DIR.'/cms/libraries/jquery-jplayer/jquery.jplayer.min.js', 
	CMS_DIR.'/cms/libraries/jquery-cycle/jquery.cycle2.min.js', 
	CMS_DIR.'/cms/libraries/masonry/masonry.pkgd.min.js',
	CMS_DIR.'/cms/libraries/masonry/imagesloaded.js',
    CMS_DIR.'/cms/libraries/jquery-flexnav/jquery.flexnav.js'
);


$js_files = add_js_language_files($js_files);
//print_r2($js_files);

// handle plugins, set each area value to null
$plugin_header = $plugin_left_sidebar = $plugin_right_sidebar = $plugin_content = $plugin_footer = $plugin_page = null;
if($arr['plugins']) {
    set_plugin_values($id);
}

// selections
$pages_selections = explode(",",$arr['selections']);
$selection_area = array_fill_keys(array('header_above', 'header', 'header_below', 'left_sidebar_top', 'left_sidebar_bottom', 'right_sidebar_top', 'right_sidebar_bottom', 'content_above', 'content_inside','content_below', 'footer_above', 'outer_sidebar'), null);
$selection_result = set_selection_values($pages_selections, $css_files, $js_files, $pages, $dtz, $selection_area);

$css_files = $selection_result[0];
$js_files = $selection_result[1];
$selection_area = $selection_result[2];
//print_r2($selection_area);


$language = strlen($arr['lang']) > 0 ? $arr['lang'] : $_SESSION['site_lang'];

// plugin uses full page - include header + load js files + footer + exit!

include_once CMS_ABSPATH .'/cms/includes/inc.header.php';

// banners
/* 
<!doctype html>
<html>

<head>

    <title>Template</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
</head>
 */

 // prepare navigation
$root = get_breadcrumb_path_array($id);	
$parent_id = ($_SESSION['site_navigation_vertical'] == 3 || $_SESSION['site_navigation_vertical'] == 4 ) ? 0 : $root[0]; 
$open = ($_SESSION['site_navigation_vertical'] == 2 || $_SESSION['site_navigation_vertical'] == 4) ? true : false;
$href = $_SERVER['SCRIPT_NAME'];
$useragent = $_SERVER['HTTP_USER_AGENT'];
$seo = $_SESSION['site_seo_url'] == 1 ? 1 : 0;
$icon = $arr['access'] == 2 ? '' : '<span class="ui-icon ui-icon-key" style="display:inline-block;"></span>';
if($id == 0) { $icon = '';}


?>
<body>
    <div id="wrapper-user">
        <div id="user-toolbar"><?php include 'includes/inc.site_active_user2.php';?></div>
    </div>

    <?php 
    print_selection("selection-header-above", $selection_area['header_above']); 
    ?>

    <header id="wrapper-site-header">
        <div id="site-name">
            <h1><?php echo $arr['title']?></h1>
            <img src="css/images/GF_logotype_1rad.png" style="width:400px">
        </div>
        <div id="site-custom">Logga in</div>
        <div id="site-search">
            <input type="text" name="search" placeholder="Vad söker du?">
            <button>Sök</button>
        </div>
        <div id="site-identity">
            <?php echo $arr['header'];?>    
            <img src="css/images/1.png">            
        </div>
        <nav id="site-navigation-header">

            <?php
            print_menu($pages, $id, $seo, $href, $open);
            ?>

        </nav>
    </header>
    <div id="wrapper-top">
        <?php 
        print_selection("selection-header-below", $selection_area['header_below']); 
        ?>
        <div id="top-grid"><?php show_grid($arr, 0);?></div>
    </div>
    <!-- run template: sidebar, left-sidebar, right-sidebar, joined sidabars, panorma -->

    <?php
    // handle template, set wrapper-content width
    $content_percent_width = 100;
    
    // remove site_template_content_padding?
    $template_content_padding = is_numeric($_SESSION['site_template_content_padding']) ? $_SESSION['site_template_content_padding'] : 0;
    
    // sidebar width between 20-33%
    $sidebar_percent_width = $_SESSION['site_template_sidebar_width'];

    print_r2("template: ". $arr['template']);    
    print_r2("Aloha" . $arr['stories_wide_teaser_image_width']);
    switch ($arr['template']) {	
        case 0:
        case 1:
            // sidebars
            $content_percent_width = 100 - ($sidebar_percent_width * 2); 
            $left_sidebar_percent_width = $right_sidebar_percent_width = $sidebar_percent_width;
            $wrapper_content_width = round($_SESSION['site_wrapper_page_width'] * $content_percent_width / 100);
            $wrapper_left_sidebar_width = $wrapper_right_sidebar_width = $wrapper_sidebar_width = $_SESSION['site_wrapper_page_width'] - $wrapper_content_width;
        break;
        case 2:
        case 3:
            // left sidebar
            $content_percent_width = 100 - $sidebar_percent_width;
            $left_sidebar_percent_width = $sidebar_percent_width;
            $right_sidebar_percent_width = 0;

        break;
        case 4:
        case 5:
            // right sidebar
            $content_percent_width = 100 - $sidebar_percent_width;
            $right_sidebar_percent_width = $sidebar_percent_width;
            $left_sidebar_percent_width = 0;
            $wrapper_content_width = round($_SESSION['site_wrapper_page_width'] * $content_percent_width / 100);
            $wrapper_right_sidebar_width = $wrapper_sidebar_width = $_SESSION['site_wrapper_page_width'] - $wrapper_content_width;
            $wrapper_left_sidebar_width = 0;
            include 'includes/inc.template_sidebar_content.php';
        break;
        case 6:
            // panorama
            $content_percent_width = 100;
            $left_sidebar_percent_width = $right_sidebar_percent_width = 0;
        break;
        case 7:
            // sidebars right joined
            $content_percent_width = 100 - ($sidebar_percent_width + $sidebar_percent_width * 0.67);
            $right_sidebar_percent_width = $sidebar_percent_width;
            $left_sidebar_percent_width = $sidebar_percent_width * 0.67;
        break;					
    }

    print_r2("template: ". $arr['template']);
    print_r2("content_percent_width: ". $content_percent_width);
    print_r2("left_sidebar_percent_width: ". $left_sidebar_percent_width);
    print_r2("right_sidebar_percent_width: ". $right_sidebar_percent_width);
    

    print_r2($_SESSION);
    ?>



    <div id="wrapper-bottom">
        <div id="bottom-selections"></div>
        <div id="bottom-grid"><?php show_grid($arr, 3);?></div>
    </div>
    <?php print_selection("selection-footer-above", $selection_area['footer_above']); ?>
    <footer id="wrapper-site-footer">
        <div id="site-about"></div>
        <div id="site-contact"></div>
        <div id="site-rss"></div>
    </footer>
</body>

</html>