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

// $selections_header_above = $selections_header = $selections_header_below = $selections_left_sidebar_top = $selections_left_sidebar_bottom = $selections_right_sidebar_top = $selections_right_sidebar_bottom = $selections_content_above = $selections_content_inside = $selections_content_below = $selections_footer_above = $selections_outer_sidebar = null;
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

?>
<body>
    <div id="wrapper-user">
        <div id="user-toolbar"><?php include 'includes/inc.site_active_user.php';?></div>
    </div>
    <header id="wrapper-site-header">
        <div id="site-name">
            <img src="css/images/GF_logotype_1rad.png" style="width:400px">
        </div>
        <div id="site-custom">Logga in</div>
        <div id="site-search">
            <input type="text" name="search" placeholder="Vad söker du?">
            <button>Sök</button>
        </div>
        <div id="site-identity">
            <img src="css/images/1.png">
        </div>
        <nav id="site-navigation-header">
            <!-- print_site_navigation_header($id)-->
            <ul>
                <li>
                    <a href="#">Om Glimåkra folkhögskola</a>
                </li>
                <li>
                    <a href="#">Våra kurser</a>
                </li>
                <li>
                    <a href="#">Ansök till kurs</a>
                </li>
                <li>
                    <a href="#">Kontakt</a>
                </li>
            </ul>
        </nav>
    </header>
    <div id="wrapper-top">
        <div id="top-selections"></div>
        <div id="top-grid"></div>
    </div>
    <!-- run template: sidebar, left-sidebar, right-sidebar, joined sidabars, panorma -->
    <div id="wrapper-page">
        <div id="wrapper-left-sidebar" class="column">
            <div id="left-sidebar-top-selections"></div>
            <nav id="left-site-navigation"></nav>
            <aside id="left-sidebar-widgets"></aside>
            <aside id="left-sidebar-stories"></aside>
            <div id="left-sidebar-bottom-selection"></div>
        </div>
        <div id="wrapper-content" class="column">
            <main>
                <div id="content-top-selections"></div>
                <div id="content-top-grid"></div>
                <div id="content-breadcrumb"></div>
                <div id="content-edit"></div>
                <article>
                    <header>
                        <h1 id="content-title">Aloha</h1>
                        <div id="content-meta"></div>
                    </header>
                    <div id="content-html">
                        <p>
                            <img src="https://lh3.googleusercontent.com/_19QvesXbe05GLnIPohWFHYcSj-F_B0vQpeDR4AGdyU_U-V8tO8AceT7sZmMPSdyEBXV=h900" style="float:left;width:400px;margin:20px;"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. In ligula dolor, auctor non tincidunt
                            at, finibus et sem. Donec eget eros volutpat, condimentum tellus vitae, vestibulum augue. Interdum
                            et malesuada fames ac ante ipsum primis in faucibus. Integer tellus ipsum, hendrerit sed lacus
                            nec, ultrices malesuada sem. Sed placerat vulputate scelerisque.
                        </p>
                        <p>
                            Morbi maximus quam nisi, sit amet mollis massa euismod eget. Aenean consectetur hendrerit tempor. Vestibulum rhoncus justo
                            est, sit amet pellentesque urna ullamcorper vitae. Vivamus ac nibh nec eros ornare mattis. Curabitur
                            vestibulum tellus sed purus commodo, a ornare elit viverra. Curabitur mattis risus justo, quis
                            iaculis lectus venenatis sed. Sed malesuada at est in molestie. Aenean eleifend arcu quis suscipit
                            hendrerit. Curabitur turpis metus, egestas eget convallis eget, euismod posuere dui. Praesent
                            non tempus nulla, a ultricies nibh.
                        </p>
                        <h1>Laterna magica</h1>
                        <p>
                            Mauris euismod quam id lectus iaculis gravida. Vivamus eu erat nunc. Donec sit amet vestibulum leo, non rhoncus enim. Aliquam
                            erat volutpat. Cras commodo euismod est vitae tincidunt. Duis a dictum lacus. Mauris egestas,
                            nisl vel condimentum dapibus, augue sapien condimentum felis, a hendrerit felis nulla eu nunc.
                            Donec a arcu non dui aliquet fermentum in et tellus.
                        </p>
                        <p>
                            In hac habitasse platea dictumst. Nulla facilisi. Morbi at placerat ligula, ac commodo augue. Nam tempor interdum condimentum.
                            Etiam molestie vehicula nulla. Quisque dapibus leo vel libero pretium convallis. Nam eget cursus
                            quam. Nullam varius purus tempus, vestibulum dui at, auctor odio.
                        </p>
                        <p>
                            Fusce blandit scelerisque blandit. Phasellus ante elit, tempor vel porttitor at, rhoncus at mi. Fusce vitae erat at metus
                            vulputate luctus ac eu mi. Proin elit est, efficitur sed laoreet tincidunt, feugiat sit amet
                            turpis. Quisque vel est id sem rutrum tempus.
                        </p>
                    </div>

                    <div id="content-inside-selections"></div>
                    <footer>
                        <div id="content-author"></div>
                        <div id="content-social-network"></div>
                    </footer>
                </article>
                <div id="content-bottom-grid"></div>
                <aside id="content-bottom-widgets"></aside>
                <aside id="content-bottom-stories"></aside>
                <div id="content-bottom-selections"></div>
            </main>
        </div>
        <div id="wrapper-right-sidebar" class="column">
            <!-- left sidebar layout -->
            <img src="css/images/ordmoln_glimakra.png" style="width:100%">
            
            
        </div>
    </div>
    <div id="wrapper-bottom">
        <div id="bottom-selections"></div>
        <div id="bottom-grid"></div>
    </div>
    <footer id="wrapper-site-footer">
        <div id="site-about"></div>
        <div id="site-contact"></div>
        <div id="site-rss"></div>
    </footer>
</body>

</html>