<?php
// include core 
require_once 'includes/inc.core.php';

if(!isset($_SESSION['site_id'])) { die;}

if($_SESSION['site_maintenance'] == 1 && $_SESSION['role_CMS'] < 4) { header('Location: maintenance.php');}

// seo friendly url 
$request = substr($_SERVER['REQUEST_URI'], strlen(CMS_DIR.'/'));
$request_parts = explode('/', $request);

// $request_parts now holds: [0] htaccess rewrite folder, [1] seo title (or script name)
$id = array_key_exists('id', $_GET) ? $_GET['id'] : $request_parts[1];

// initiate class
$pages = new Pages();

// try to find id, if alphanumeric seo link -> get id
if(isset($id)) {
	$id = filter_var($id, FILTER_VALIDATE_INT) ? $id : filter_var($id, FILTER_SANITIZE_STRING);    
    if (strlen($id)) {
        if (!is_numeric($id)) {
            $result = $pages->getPagesSeo($id);
            $id = $result ? $result['pages_id'] : null;
        }
    } else {
        $id = null;
    }
}

// no $id and request parts exists ->  show 404 error and default content from site ...
// no $id and no request parts -> get default startpage 
// $id but no published page exists in database - > get default startpage 
// $id and published page exists in database - > show page

// default
$arr = null;

//access page as known or unknown user to get page content
$users_id = (isset($_SESSION['users_id'])) ? $_SESSION['users_id'] : null;

function getStartPage() {
    
    // just installed Storiesaround
    if ($_SESSION['site_domain_url'] == CMS_URL) {
        //print_r2("please set a startpage in site_domain_url");
    } else {
        header('Location: '. $_SESSION['site_domain_url']);
        exit;            
    }
}

function getPagesContentDefault($page404, $acc_read) {

    if (!$page404) {
        return getStartPage();
    }
    $site = new Site();
    $arr = $site->getSiteColumnNames("pages");
    $arr['template'] = $_SESSION['site_template_default']; 
    $arr['content'] = "";
    $arr['content'] .= $acc_read ? "" : getAccessMsg();
    $arr['content'] .= $page404 ? get404() : "";
    $arr['meta_robots'] = "noindex, nofollow";
    return $arr;
}

function get404() {
    return "<div id='page404'><h1>404</h1><p>The page cannot be found!</p></div>" . $_SESSION['site_404'];
}

function getAccessMsg() {
    $msg = "Some information on this page can only be shown if you are logged in as a user";
    return $msg;
}


if ($id == null && count($request_parts)) {

    // pages.php
    // pages.php?iddds
    // pages/lorem-ipsum
    // pages/lorem-ipsum<script>alert()</script>

    $pattern = '/^(?=^.{1,128}$)([a-z-])*$/';
    $page404 = preg_match($pattern, $request_parts[1]) ? true : false;
    $arr = getPagesContentDefault($page404, $acc_read=true);

} elseif ($id == null) {
    header('Location: '. $_SESSION['site_domain_url']);
    exit;
} else {
    $arr = $pages->getPagesContent($id);
    
    // page found
    if ($arr) {
        $acc_read = get_rights($id, $users_id, $arr['access']);
        
        // no access
        if (!$acc_read) {            
            $arr = getPagesContentDefault($page404=true, $acc_read);
        }
    
    } else {

        // not a page -> get start page
        header('Location: '. $_SESSION['site_domain_url']);
        exit;
    }
}

$sample = false;
/*
if ($arr == null && isset($_GET['sample'])) {
    $arr = get_sample_data();
} else if ($arr == null) {
    get404();
} else {
    echo "Sooooo";
}
*/


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
	//CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/cms/libraries/jquery-flexnav/flexnav.css',
    //CMS_DIR.'/cms/css/layout.css',
    CMS_DIR.'/cms/libraries/font-awesome/css/font-awesome.min.css',
    'https://fonts.googleapis.com/css?family=Open+Sans'
    
);

$css_files = add_css_themes($css_files);
$css_files = add_css_widgets($css_files, $widgets_css);
array_push($css_files, CMS_DIR.'/cms/css/style.css');

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

// handle plugins, set each area value to null
$plugin_header = $plugin_left_sidebar = $plugin_right_sidebar = $plugin_content = $plugin_footer = $plugin_page = null;
if($arr['plugins']) {
    set_plugin_values($id, $users_id, $css_files);
}

// selections
$pages_selections = explode(",",$arr['selections']);
$selection_area = array_fill_keys(array('header_above', 'header', 'header_below', 'left_sidebar_top', 'left_sidebar_bottom', 'right_sidebar_top', 'right_sidebar_bottom', 'content_above', 'content_inside','content_below', 'footer_above', 'outer_sidebar'), null);
$selection_result = set_selection_values($pages_selections, $css_files, $js_files, $pages, $dtz, $selection_area);

$css_files = $selection_result[0];
$js_files = $selection_result[1];
$selection_area = $selection_result[2];


$language = strlen($arr['lang']) > 0 ? $arr['lang'] : $_SESSION['site_lang'];

// plugin uses full page - include header + load js files + footer + exit!

include_once CMS_ABSPATH .'/cms/includes/inc.header.php';



 // prepare navigation
$root = get_breadcrumb_path_array($id);	
$parent_id = ($_SESSION['site_navigation_vertical'] == 3 || $_SESSION['site_navigation_vertical'] == 4 ) ? 0 : $root[0]; 
$open = ($_SESSION['site_navigation_vertical'] == 2 || $_SESSION['site_navigation_vertical'] == 4) ? true : false;
$href = $_SERVER['SCRIPT_NAME'];
$useragent = $_SERVER['HTTP_USER_AGENT'];
$seo = $_SESSION['site_seo_url'] == 1 ? 1 : 0;
$icon = $arr['access'] == 2 ? '' : '<span class="ui-icon ui-icon-key" style="display:inline-block;"></span>';
if($id == 0) { $icon = '';}

if ($arr['template'] == 6) {
    if (is_file(CMS_ABSPATH.'/content/templates/' . $arr['template_custom'])) {
        include CMS_ABSPATH.'/content/templates/' . $arr['template_custom'];
    } else {
        print("File missing....");
    }
    die();
}

?>
<body>
    <?php print_noscript($languages) ?>
    <?php if ($users_id) {?>
    <div id="wrapper-user">
        <div id="user-toolbar"><?php include 'includes/inc.site_active_user2.php';?></div>
    </div>
    <?php }?>
    <?php 
    print_selection("selection-header-above", $selection_area['header_above']);
    ?>

    <header id="wrapper-site-header">

        <?php
        if (strlen($selection_area['header'])) {
            print_selection("selection-header-above", $selection_area['header']);
        } else {
            include_once_customfile('includes/inc.site_header.php', $arr, $languages); 
        }
        ?>
        
        <nav id="site-navigation-header">
            <?php
            print_mobile_menu($pages, $id, $seo, $href);
            print_menu($pages, $id, $seo, $href, $open, $sample);
            ?>
        </nav>
        
    </header>

    <div id="wrapper-top">
        <?php
        
        if ($arr == null) {
            $hint[0] = "<h1>Welcome to CMS Storiesaround</h1>";
            $hint[1] = "<p>If this is a new installation, please follow these instructions:</p>";
            $hint[2] = "<ul>";
            $hint[3] = $users_id == null ? "<li><a href='login.php' target='_blank'>Login</a></li>" : "";
            $hint[4] = "<li><a href='admin.php?t=pages&tp=add' target='_blank'>Create</a> a page</li>";
            $hint[5] = "<li>Publish the page</li>"; 
            $hint[6] = "<li>Set the new page as startpage, and save as <a href='admin.php?t=site&tg=settings' target='_blank'>Site domain url</a></li>";
            $hint[7] = "</ul>";
            $hint[8] = "<p><a href='admin.php' target='_blank'>For more settings go to admin.php</a></p>";
            for ($i = 0; $i < count($hint); $i++) {
                echo $hint[$i];
            }
                            
        }
        if ($arr['search_field_area'] == 2) {
            print_search_field_area_page($languages);
        }
        ?>
        <div id="pages_search_result" class="hidden"></div>
        <input type="hidden" id="pages_search_result_start" value="0">
        <button id="btn-site-search-page-more" style="display:none" class="btn-link">Show more +</button>
        <?php
        print_selection("selection-header-below", $selection_area['header_below']); 
        ?>
        <div id="top-grid"><?php print_grid($arr, 0);?></div>
    </div>

    <?php

    // handle template, set wrapper-content width
    $content_percent_width = 100;
    
    // remove site_template_content_padding?
    $template_content_padding = is_numeric($_SESSION['site_template_content_padding']) ? $_SESSION['site_template_content_padding'] : 0;
    
    // sidebar width between 20-33%
    $sidebar_percent_width = $_SESSION['site_template_sidebar_width'];

    // get stories before template renders
    $rows_child = $pages->getPagesStoryContentPublishChild($id); 

    switch ($arr['template']) {	
        case 0:
            // sidebars
            $content_percent_width = 100 - ($sidebar_percent_width * 2); 
            $left_sidebar_percent_width = $right_sidebar_percent_width = $sidebar_percent_width;
            $wrapper_content_width = round($_SESSION['site_wrapper_page_width'] * $content_percent_width / 100);
            $wrapper_left_sidebar_width = $wrapper_right_sidebar_width = $wrapper_sidebar_width = $_SESSION['site_wrapper_page_width'] - $wrapper_content_width;
        break;
        case 1:
            // left sidebar
            $content_percent_width = 100 - $sidebar_percent_width;
            $left_sidebar_percent_width = $sidebar_percent_width;
            $right_sidebar_percent_width = 0;

        break;
        case 2:
            // right sidebar
            $content_percent_width = 100 - $sidebar_percent_width;
            $right_sidebar_percent_width = $sidebar_percent_width;
            $left_sidebar_percent_width = 0;
            $wrapper_content_width = round($_SESSION['site_wrapper_page_width'] * $content_percent_width / 100);
            $wrapper_right_sidebar_width = $wrapper_sidebar_width = $_SESSION['site_wrapper_page_width'] - $wrapper_content_width;
            $wrapper_left_sidebar_width = 0;
            include 'includes/inc.template_sidebar_content.php';
        break;
        case 3:
            // panorama
            $content_percent_width = 100;
            $left_sidebar_percent_width = $right_sidebar_percent_width = 0;
        break;
        case 4:
            // sidebars right joined
            $content_percent_width = 100 - ($sidebar_percent_width + $sidebar_percent_width * 0.67);
            $right_sidebar_percent_width = $sidebar_percent_width;
            $left_sidebar_percent_width = $sidebar_percent_width * 0.67;
        break;					
        case 5:
            // custom
            $content_percent_width = 100;
            $left_sidebar_percent_width = $right_sidebar_percent_width = 0;
            if (is_file(CMS_ABSPATH.'/content/templates/' . $arr['template_custom'])) {
                include CMS_ABSPATH.'/content/templates/' . $arr['template_custom'];
            } else {
                print("File missing....");
            }
        break;
    } 
    ?>
    
    <div id="wrapper-bottom">
        <div id="bottom-selections"></div>
        <div id="bottom-grid"><?php print_grid($arr, 3);?></div>
    </div>

    <?php print_selection("selection-footer-above", $selection_area['footer_above']); ?>

    <footer id="wrapper-site-footer">
        <div id="site-about"></div>
        <div id="site-contact"></div>
        <div id="site-rss"></div>
        <?php include_once_customfile('includes/inc.footer.php', $arr, $languages); ?>			
    </footer>

    <input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token']; ?>">
    <input type="hidden" name="cms_dir" id="cms_dir" value="<?php echo CMS_DIR;?>">
    <input type="hidden" name="pages_id" id="pages_id" value="<?php echo $id;?>">
    <input type="hidden" name="site_language" id="site_language" value="<?php echo $_SESSION['site_language'];?>">
    <input type="hidden" name="stories_equal_height" id="stories_equal_height" value="<?php echo $arr['stories_equal_height'];?>">
    
    <?php
    $js_files = array_unique($js_files);
    foreach ( $js_files as $js ) { 
        echo "\n".'<script src="'.$js.'"></script>';
    }
    ?>

    <?php include_once 'includes/inc.debug.php'; ?>

    <?php
    
    print_r2($request_parts);
    ?>
</body>

</html>