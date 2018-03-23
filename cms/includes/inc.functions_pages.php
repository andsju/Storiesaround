<?php
require_once 'inc.core.php';
require_once 'inc.functions.php';


/**
 *
 * get rights to view page
 *
 * @param int $id
 * @param $users_id
 * @param $access
 * @return boolean $acc_read
 */
function get_rights($id, $users_id, $access)
{
    $acc_read = false;
    // check if content can be seen by current user
    switch ($access) {
        case 0: // logged in users must have read rights
            
            if($users_id) {
                if(!get_role_CMS('author') == 1) {
                    
                    // user rights to this page
                    $pages_rights = new PagesRights();
                    $users_rights = $pages_rights->getPagesUsersRights($id, $users_id);
                    
                    // groups rights to this page
                    $groups_rights = $pages_rights->getPagesGroupsRights($id);		
                    
                    if($users_rights['rights_read'] == 1) {
                        $acc_read = true;
                    } else {
                        
                        // groups rights membership
                        if(get_membership_rights('rights_read', $_SESSION['membership'], $groups_rights)) {
                            $acc_read = true;
                        }
                    }
                } else {
                    $acc_read = true;
                }
            }
            break;
        case 1: // logged in users
            
            if (isset($users_id)) {
                $acc_read = true;            
            }
            break;        
        case 2: // everyone
            $acc_read = true;    
        break;
    }
    return $acc_read;
}


/**
 *
 * get rights to view page
 *
 * @param array $rows_widgets
 * @return array $widgets_css
 */
function get_widgets_css($rows_widgets)
{
    $widgets_css = null;
    foreach($rows_widgets as $rows_widget) {
        foreach($rows_widget as $key => $value) {
            if($key == 'widgets_css') {
                if(strpos($value,'css')) {
                    $widgets_css[] = $value;
                }
            }
        }
    }
    return $widgets_css;
}

/**
 *
 * add css themes
 *
 * @param array $css_files
 * @return array $css_files
 */
function add_css_themes($css_files)
{
    // add css_theme
    if(file_exists(CMS_ABSPATH .'/content/themes/'.$_SESSION['site_theme'].'/style.css')) {
        array_push($css_files, CMS_DIR.'/content/themes/'.$_SESSION['site_theme'].'/style.css');
    }

    // add css jquery-ui theme
    if(file_exists(CMS_ABSPATH .'/cms/libraries/jquery-ui/theme/'.$_SESSION['site_ui_theme'].'/jquery-ui.css')) {
        if (($key = array_search(CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', $css_files)) !== false) {
            unset($css_files[$key]);
        }
        array_push($css_files, CMS_DIR.'/cms/libraries/jquery-ui/theme/'.$_SESSION['site_ui_theme'].'/jquery-ui.css');
    }

    return $css_files;
}

/**
 *
 * add widgets css
 *
 * @param array $css_files
 * @param array $widgets_css
 * @return array $css_files
 */
function add_css_widgets($css_files, $widgets_css)
{
    // add required widgets css
    if(is_array($widgets_css)) {
        // unique values
        $widgets_css = array_unique($widgets_css);
        foreach($widgets_css as $widget_css) {
            array_push($css_files, CMS_DIR.'/cms/libraries/'.$widget_css);
        }
    }

    return $css_files;
}


/**
 *
 * add js language files
 *
 * @param array $js_files
 * @return array $js_files
 */
function add_js_language_files($js_files)
{
    $use_language = isset($_SESSION['language']) ? $_SESSION['language'] : $_SESSION['site_language'];
    
    if(isset($use_language)) {
        switch ($use_language) {
            case "swedish":
                $js_files[] = CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.sv.js';
            break;
        }
    }

    return $js_files;    
}

/**
 *
 * set plugin values
 *
 * @param int $id
 */
function set_plugin_values($id, $users_id, $css_files)
{
    //if($arr['plugins']) {
        
        // main class
        $pages_plugins = new PagesPlugins();					
        // get this class name
        $plugin = $pages_plugins->getPagesPlugins($id);
        if($plugin) {
            // only use plugin if active
            if($plugin['plugins_active'] == 1) {
                // use class
                $plugin_class = new $plugin['plugins_title']();
                
                // get info
                $plugin_info = $plugin_class->info();
    
                // get plugin css			
                if(strlen(trim($plugin_info['css']))>0){
                    array_push($css_files, trim($plugin_info['css']));
                }
                // returns an array for which areas should be replaced by plugin
                $plugin_action = $plugin_class->action($id, $users_id, $_SESSION['token'], $plugin_info['area']);
                
                if($plugin_action) {
                    foreach($plugin_action as $p) {
                        foreach($p as $key=>$value) {
                            switch ($key) {
                                case 'header';
                                    $plugin_header = $value;
                                break;
                                case 'left_sidebar';
                                    $plugin_left_sidebar = $value;
                                break;
                                case 'content';
                                    $plugin_content = $value;
                                break;
                                case 'right_sidebar';
                                    $plugin_right_sidebar = $value;
                                break;
                                case 'footer';
                                    $plugin_footer = $value;
                                break;
                                case 'page';
                                    $plugin_page = $value;
                                break;
                            }
                        }
                    }
                }
            }
        }
    //}
}


/**
 *
 * set selection values
 *
 * @param array $pages_selections
 * @param array $css_files
 * @param array $js_files
 * @param array $pages
 * @param string $dtz
 * @param array $selection_area
 */
function set_selection_values($pages_selections, $css_files, $js_files, $id, $pages, $dtz, $selection_area)
{
    if(is_array($pages_selections)) {
        $selections = new Selections();
        $s_rows = $selections->getMultipleSelectionsContent($pages_selections);

        foreach($s_rows as $s_row) {
            $external_js_files = explode(" ",$s_row['external_js']);
            foreach($external_js_files as $external_js){
                if(strlen(trim($external_js))>0){
                    array_push($js_files, trim(CMS_DIR.$external_js));
                }
            }
            
            $external_css_files = explode(" ", $s_row['external_css']);
            
            foreach($external_css_files as $external_css){
                if(strlen(trim($external_css)) > 0){
                    array_push($css_files, trim($external_css));
                }
            }
            $content_html = $s_row['content_html'];
            $content_code = implode(parse_storiesaround_coded($pages, $dtz, $s_row['content_code'],0));
            $grid_cell_template = $s_row['grid_cell_template'];
            $grid_custom_classes = $s_row['grid_custom_classes'];
            $grid_cell_image_height = $s_row['grid_cell_image_height'];
            $grid_content = $s_row['grid_content'];
            $grid_content = get_grid($id, $grid_active=1, $grid_content, $grid_custom_classes, $grid_cell_template, $grid_cell_image_height);

            switch($s_row['area']) {
            
                case 'header_above':
                    $selection_area['header_above'] .= $content_html . $content_code . $grid_content;
                break;
                case 'header':
                    $selection_area['header'] .= $content_html . $content_code . $grid_content;
                break;
                case 'header_below':
                    $selection_area['header_below'] .= $content_html . $content_code . $grid_content;
                break;
                case 'left_sidebar_top':
                    $selection_area['left_sidebar_top'] .= $content_html . $content_code;
                break;      
                case 'left_sidebar_bottom':
                    $selection_area['left_sidebar_bottom'] .= $content_html . $content_code;
                break;
                case 'right_sidebar_top':
                    $selection_area['right_sidebar_top'] .= $content_html . $content_code . $grid_content;
                break;
                case 'right_sidebar_bottom':
                    $selection_area['right_sidebar_bottom'] .= $content_html . $content_code;
                break;
                case 'content_above':
                    $selection_area['content_above'] .= $content_html . $content_code . $grid_content;
                break;
                case 'content_inside':
                    $selection_area['content_inside'] .= $content_html . $content_code;
                break;
                case 'content_below':
                    $selection_area['content_below'] .= $content_html . $content_code . $grid_content;
                break;
                case 'footer_above':
                    $selection_area['footer_above'] .= $content_html . $content_code . $grid_content;
                break;
                case 'outer_sidebar':
                    $selection_area['outer_sidebar'] .= $content_html . $content_code;
                break;
            }
        }    
    }
    return array($css_files, $js_files, $selection_area);
}

/**
 *
 * get selection
 *
 * @param string div_id
 * @param string selection_area
 * @return mixed|string
 */
function get_selection($div_id, $selection_area) 
{
    if (strlen($selection_area)) {
        $clear = $div_id == 'selection-content-inside' ? '' : ' clear';
        return '<div id="'.$div_id.'" class="site-selection'.$clear.'">'.$selection_area.'</div>';
    }
    return;
}


/**
 *
 * print selection
 *
 * @param string div_id
 * @param string selection_area
 * @return mixed|string
 */
function print_selection($div_id, $selection_area) 
{
    $s = get_selection($div_id, $selection_area);
    print($s);
}


/**
 *
 * print menu
 *
 * @param int pages_id
 * @param int id
 * @param boolena seo
 * @param string href
 * @param boolean open
 * @return mixed|string
 */
function print_menu($pages, $id, $seo, $href, $open, $sample)
{
    if(!$_SESSION['site_navigation_horizontal'] == 0) {
        echo "\n".'<div id="site-navigation-root">';
            $html = '';
            if($_SESSION['site_navigation_horizontal'] == 1) {
                $html = $pages->getPagesRoot(get_breadcrumb_path_array($id), $seo, 'pages.php');
            }
            if($_SESSION['site_navigation_horizontal'] == 2) {
    
                if ($_SESSION['layoutType'] == "mobile") {
                    $open = true;
                    $parent_id = 0;
                }
                get_pages_tree_menu($parent_id = 0, $id, $path=get_breadcrumb_path_array($id), $seo, $href,  $depth=0, $counter = 0);
            
            }
            
            
            if($sample == true) {							
                $html .= get_sample_navigation_root($sample_data_templates);
            }
            
            echo $html;
        echo "\n".'</div>';
    }
}


/**
 *
 * print breadcrumb
 *
 * @param int id
 * @param int breadcrumb_settings
 * @return mixed|string
 */
function print_breadcrumb($id, $breadcrumb_settings)
{
    $s = "";
    if(filter_var($id, FILTER_VALIDATE_INT)) {
        if($breadcrumb_settings > 0) {
            $s = get_breadcrumb($id," &raquo; ", 25, $clickable=true);
        }
        if($breadcrumb_settings == 2) {
            $s = get_breadcrumb_children($id, $type="select");
        }
        if($breadcrumb_settings == 3) {
            $s = get_breadcrumb_children($id, $type="ul");
        }
    }
    echo $s;
}


/**
 *
 * print meta
 *
 * @param string utc_modified
 * @param string dtz
 * @param array languages
 * @return mixed|string
 */
function print_meta($utc_modified, $dtz, $languages) 
{
    if(isset($utc_modified)) {	

        echo '<abbr class="timeago">'. translate("Modified", "pages_status_modified", $languages) .'</abbr> <abbr class="timeago" title="'.get_utc_dtz($utc_modified, $dtz, 'Y-m-d H:i:s').'">'.get_utc_dtz($utc_modified, $dtz, 'Y-m-d H:i:s').'</abbr>';
        // echo '&nbsp;<img class="ajax_history" id="'.$id.'" src="'.CMS_DIR.'/cms/css/images/icon_info.png" style="vertical-align:top;width:11px;height:11px;" alt="history" />&nbsp;';
        // echo '&nbsp;<span id="ajax_spinner_history" style="display:none;"><img src="'.CMS_DIR.'/cms/css/images/spinner_1.gif" alt="spinner" /></span>';
        // echo '<div id="ajax_status_history" style="display:none;"></div>';
    }
}


/**
 *
 * print site search field area header
 *
 * @param array languages
 * @return mixed|string
 */
function print_search_field_area_header($languages)
{
    $html = '<div id="site-search-header">';
    $html .= '<label for="search"><i class="fas fa-search" aria-hidden="true"></i></label>'; 
    $html .= '<input type="text" name="search-page" placeholder="'. translate("Search", "site_search_pages", $languages) .'" id="search-page" class="search" value="">';
    $html .= '<span id="ajax_spinner_search" style="display:none"><img src="css/images/spinner.gif"></span>';
    $html .= '<input type="hidden" id="pid" value="0">';
    $html .= '<button id="btn-site-search-page">'. translate("Search", "site_search", $languages) .'</button>';
    $html .= '</div>';
    echo $html;
}

/**
 *
 * print site search field area page
 *
 * @param array languages
 * @return mixed|string
 */
function print_search_field_area_page($languages)
{
    $html = '<div id="site-search-page">';
    $html .= '<label for="search-page"><i class="fas fa-search" aria-hidden="true"></i></label>'; 
    $html .= '<input type="text" name="search-page" placeholder="'. translate("Search", "site_search_pages", $languages) .'" id="search-page" class="search" value="" style="z-index:999">';
    $html .= '<span id="ajax_spinner_search" style="display:none"><img src="'.CMS_DIR.'/cms/css/images/spinner.gif"></span>';
    $html .= '<input type="hidden" id="pid" value="0">';
    $html .= '<button id="btn-site-search-page">'. translate("Search", "site_search", $languages) .'</button>';
    $html .= '<input type="checkbox" name="search-page-limit-tree" id="search-page-limit-tree"> <span id="search-page-limit-tree-helper">'. translate("Search", "site_search_limit", $languages) . '</span>';
    $html .= '</div>';
    echo $html;
}


/**
 *
 * print site search result
 *
 * @param array languages
 * @return mixed|string
 */
function print_search_field_result($languages)
{
    $html = '<div id="pages_search_result" class="hidden"></div>';
    $html .= '<input type="hidden" id="pages_search_result_start" value="0">';
    $html .= '<button id="btn-site-search-page-more" style="display:none" class="btn-link">'. translate("Show more", "site_search_show_more", $languages) .' +</button>';
    echo $html;
}


/**
 *
 * print author
 *
 * @param string $author
 * @param array languages
 * @return mixed|string
 */
function print_author($author, $languages)
{   
    if(strlen($author)) {
        $s = "";
        $s .= translate("Author", "pages_content_author", $languages);
        echo $s;
    }
}


/**
 *
 * get breadcrumb from given id/parent_id
 *
 * @param int $parent_id
 * @param $delimiter config string delimiter
 * @param $trunc_length max length before truncate string
 * @param $clickable
 * @return mixed|string
 */
function get_breadcrumb($parent_id, $delimiter, $trunc_length, $clickable)
{
    $pages = new Pages();
    $row = $pages->getPagesParent($parent_id);
    if (is_array($row)) {
        $r = $row['title'];
        $c = strlen($row['title']);
        if (strlen($row['title']) > $trunc_length) {
            $t = $c - $trunc_length;
            $r = utf8_encode(substr(utf8_decode($row['title']), 0, -$t)) . '...';
        }

        // use seo pages_id_link if set
        if (strlen($row['pages_id_link']) > 0) {            
            $crumb = ($clickable == 1) ? '<a href="' . CMS_PROTOCOL . $_SESSION['site_domain'] . '/pages/' . $row['pages_id_link'] . '">' . $r . '</a>' : $r;
        } else {
            $crumb = ($clickable == 1) ? '<a href="' . CMS_PROTOCOL . $_SESSION['site_domain'] . '/cms/pages.php?id=' . $row['pages_id'] . '">' . $r . '</a>' : $r;
        }

        if ($row['parent_id'] == 0) {
            return $crumb;
        } else {
            return get_breadcrumb($row['parent_id'], $delimiter, $trunc_length, $clickable) . $delimiter . $crumb;
        }
    }
}


/**
 * get breadcrumb from given id/parent_id
 *
 * @param $pages_id
 * @param $type
 */
function get_breadcrumb_children($pages_id, $type)
{
    $pages = new Pages();
    $rows = $pages->getPagesChildren($pages_id);

    if (is_array($rows)) {
        if ($rows) {
            switch ($type) {
                case "select" :
                    echo ' | <select id="navigation-pages-children" title="Select child page">';
                    echo '<option></option>';
                    foreach ($rows as $row) {
                        echo '<option value="' . CMS_DIR . '/cms/pages.php?id=' . $row['pages_id'] . '">';
                        echo $row['title'];
                        echo '</option>';
                    }
                    echo '</select>';
                    break;

                case "ul" :
                    echo '<ul id="navigation-pages-children" title="Select child page">';
                    echo '<li id="0"><a href="#">&nbsp;</a>';
                    echo '<ul>';
                    foreach ($rows as $row) {
                        echo '<li id="' . $row['pages_id'] . '">';
                        echo '<a href="' . CMS_DIR . '/cms/pages.php?id=' . $row['pages_id'] . '">';
                        echo '<span>';
                        echo $row['title'];
                        echo "</span></a>";
                        echo '</li>';
                    }
                    echo '</ul>';
                    echo '</li>';
                    echo '</ul>';
            }
        }
    }
}


/**
 * use function to open nodes in menu
 *
 * @param $parent_id
 * @return string
 */
function get_breadcrumb_path($parent_id)
{
    $pages = new Pages();
    $row = $pages->getPagesParent($parent_id);

    if ($row['parent_id'] == 0) {
        return $row['pages_id'];
    } else {
        return get_breadcrumb_path($row['parent_id']) . ',' . $row['pages_id'];
    }
}


/**
 * @param $id
 * @return array
 */
function get_breadcrumb_path_array($id)
{
    if (filter_var($id, FILTER_VALIDATE_INT)) {

        // get breadcrumb path
        $path = explode(",", get_breadcrumb_path($id));
    } else {
        $path = array();
    }
    return array_key_exists(0, $path) ? $path : array(0 => 0);
}


/**
 * @param $show_pages_id
 */
function get_pages_outside_sitetree($show_pages_id)
{
    $pages = new Pages();
    $rows = $pages->getPagesParentNull();

    if (count($rows)) {
        $class = '';
        echo "<ul class=\"nav-tree\">\n";
        foreach ($rows as $row) {
            $class = $row['status'] == 2 ? '' : ' not-published';
            echo "\t";
            echo '<li class="' . $class . '">';
            echo '<a href="pages_edit.php?id=' . $row['pages_id'] . '" class="colorbox_edit">';
            echo $row['title'];
            echo "</a>";
            if ($show_pages_id == true) {
                echo ' <span class="sitetree_id">(' . $row['pages_id'] . ')</span>';
            }
            echo "</li>\n";
        }
        echo "</ul>\n";
    }
}


/**
 * print html ul li list
 *
 * @param int $parent_id
 * @param int $id
 * @param $array_path
 * @param bool $a
 * @param bool $a_add_class
 * @param $seo true/false use seo links
 * @param $href
 * @param bool $open
 * @param $depth
 * @param $show_pages_id
 */
function get_pages_tree_sitemap($parent_id = 0, $id, $array_path, $a = true, $a_add_class = false, $seo, $href, $open = true, $depth, $show_pages_id)
{

    $pages = new Pages();
    $rows = $pages->getPagesTreePublished($parent_id);
    if (count($rows)) {
        echo str_repeat("\t", $depth);
        echo "<ul class=\"nav-tree\"  data-breakpoint=\"800\" >\n";

        foreach ($rows as $row) {
            $class = "";
            $class = ($row['parent'] == 1) ? "node" : "";
            $class .= (in_array($row['pages_id'], $array_path)) ? " node-open" : "";
            $class .= ($row['pages_id'] == $id) ? " node-selected" : "";
            if (($row['pages_id'] == $id) && ($row['parent'] == 0)) {
            }

            echo str_repeat("\t", $depth);
            echo '<li class="' . $class . '">';
            if ($a) {

                $a_class = strlen($a_add_class) > 0 ? $a_add_class : "";
                if (strlen($row['pages_id_link']) > 0 && $seo == 1) {
                    echo '<a href="' . CMS_PROTOCOL . $_SESSION['site_domain'] . '/pages/' . $row['pages_id_link'] . '" class="' . $class . '">';
                } else {
                    echo '<a href="' . $href . '?id=' . $row['pages_id'] . '" class="' . $class . '">';
                }
                echo $row['title'];
                echo '</a>';
            } else {
                echo $row['title'];
            }
            if ($show_pages_id == true) {
                echo ' <span class="sitetree_id">(' . $row['pages_id'] . ')</span>';
            }

            // use recursion
            if ($open) {
                get_pages_tree_sitemap($row['pages_id'], $id, $array_path, $a, $a_add_class, $seo, $href, $open, $depth + 1, $show_pages_id);
            } else {

                // open nodes from breadcrumb path
                if (in_array($row['pages_id'], $array_path)) {
                    get_pages_tree_sitemap($row['pages_id'], $id, $array_path, $a, $a_add_class, $seo, $href, $open, $depth + 1, $show_pages_id);
                }
            }
            echo "</li>\n";
        }
        echo str_repeat("\t", $depth);
        echo "</ul>\n";
    }
}


/**
 * print html ul li list
 *
 * @param int $parent_id
 * @param $id
 * @param $array_path
 * @param $seo
 * @param $href
 * @param $depth
 * @param int $counter
 */
function get_pages_tree_menu($parent_id = 0, $id, $array_path, $seo, $href, $depth, $counter = 0)
{
   $pages = new Pages();
    $rows = $pages->getPagesTreePublished($parent_id);
    if (count($rows)) {
        echo str_repeat("\t", $depth);
        $ul = $counter == 0 ? "\n<ul id=\"menu\">\n" : "\n<ul>\n";
        echo $ul;
        foreach ($rows as $row) {
            $counter = $counter + 1;
            echo str_repeat("\t", $depth);
            if (strlen($row['pages_id_link']) > 0 && $seo == 1) {
                echo '<li><a href="' . CMS_PROTOCOL . $_SESSION['site_domain'] . '/pages/' . $row['pages_id_link'] . '">';
            } else {
                echo '<li><a href="' . $href . '?id=' . $row['pages_id'] . '">';
            }
            echo $row['title'] . '</a>';

            // use recursion
            get_pages_tree_menu($row['pages_id'], $id, $array_path, $seo, $href, $depth + 1, $counter);
            echo "</li>\n";
        }
        echo str_repeat("\t", $depth);
        echo "\n</ul>\n";
    }
}


/**
 * print html ul li list
 *
 * @param int $parent_id
 * @param $id
 * @param $array_path
 * @param bool $a
 * @param bool $a_add_class
 * @param $seo
 * @param $href
 * @param bool $open
 * @param $depth
 * @param $show_pages_id
 */
function get_pages_tree_sitemap_all($parent_id = 0, $id, $array_path, $a = true, $a_add_class = false, $seo, $href, $open = true, $depth, $show_pages_id)
{
    $pages = new Pages();
    $rows = $pages->getPagesTreeAll($parent_id);

    if (count($rows)) {
        echo "\n";
        echo str_repeat("\t", $depth);
        echo "<ul class=\"nav-tree\">\n";

        foreach ($rows as $row) {
            $class = ($row['parent'] == 1) ? 'node' : '';
            $class .= (in_array($row['pages_id'], $array_path)) ? ' open' : '';
            $class .= ($row['pages_id'] == $id) ? ' selected' : '';
            if (($row['pages_id'] == $id) && ($row['parent'] == 0)) {
                $class .= ' selected_end';
            }

            $class .= $row['status'] == 2 ? '' : ' not-published';
            echo str_repeat("\t", $depth);
            echo "\t";
            echo '<li class="' . $class . '">';
            if ($a) {
                $a_class = strlen($a_add_class) > 0 ? $a_add_class : "";
                if (strlen($row['pages_id_link']) > 0 && $seo == 1) {
                    echo '<a href="' . CMS_PROTOCOL . $_SESSION['site_domain'] . '/pages/' . $row['pages_id_link'] . '" class="' . $a_class . '">';
                } else {
                    echo '<a href="' . $href . '?id=' . $row['pages_id'] . '" class="' . $a_class . '">';
                }
                echo $row['title'];
                echo '</a>';
            } else {
                echo $row['title'];
            }
            if ($show_pages_id == true) {
                echo ' <span class="sitetree_id">(' . $row['pages_id'] . ')</span>';
            }

            // use recursion
            if ($open) {
                get_pages_tree_sitemap_all($row['pages_id'], $id, $array_path, $a, $a_add_class, $seo, $href, $open, $depth + 1, $show_pages_id);
            } else {

                // open nodes from breadcrumb path
                if (in_array($row['pages_id'], $array_path)) {
                    get_pages_tree_sitemap_all($row['pages_id'], $id, $array_path, $a, $a_add_class, $seo, $href, $open, $depth + 1, $show_pages_id);
                }
            }
            echo "</li>\n";
        }
        echo str_repeat("\t", $depth);
        echo "</ul>\n";
    }
}


/**
 * print html option list
 *
 * @param $parent_id
 * @param $get_id
 * @param $depth
 */
function get_pages_tree_option_list2($parent_id, $get_id, $depth)
{
    $users_id = (isset($_SESSION['users_id'])) ? $_SESSION['users_id'] : 0;
    $pages = new Pages();
    $rows = $pages->getPagesTreePublished($parent_id, $users_id);

    if (count($rows)) {
        echo str_repeat("\t", $depth);
        foreach ($rows as $row) {

            // show hierarchy
            $indent = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $depth);
            $class = ' class="tree"';
            $selected = ($row['pages_id'] == $get_id) ? ' selected=selected' : '';
            echo '<option value="' . $row['pages_id'] . '" ' . $class . ' ' . $selected . '>' . $indent . $row['title'] . '</option>';

            // recursive search, go deeper
            get_pages_tree_option_list2($row['pages_id'], $get_id, $depth + 1);
        }
        echo str_repeat("\t", $depth);
    }
}

/**
 * @param int $parent_id
 * @param $id
 * @param $array_path
 * @param bool $a
 * @param $href
 * @param bool $open
 * @param $depth
 */
function get_pages_tree_jqueryui($parent_id = 0, $id, $array_path, $a = true, $href, $open = true, $depth)
{
    $pages = new Pages();
    $rows = $pages->getPagesTreeAll($parent_id);

    if (count($rows)) {
        echo str_repeat("\t", $depth);
        echo "<ul>\n";
        foreach ($rows as $row) {
            $class = '';
            $class = (in_array($row['pages_id'], $array_path)) ? '' : $class;
            $class .= ($row['pages_id'] == $id) ? ' ui-state-disabled' : $class;
            if (($row['pages_id'] == $id) && ($row['parent'] == 0)) {
                $class .= '';
            }

            $class .= $row['status'] != 2 ? ' not-published' : '';

            echo str_repeat("\t", $depth);
            echo '<li class="' . $class . '" id="' . $row['pages_id'] . '" data-title="' . $row['title'] . '">';
            if ($a) {

                // use seo pages_id_link if set
                if (strlen($row['pages_id_link']) > 0) {
                    echo '<a href="#" id=' . $row['pages_id'] . '>';
                } else {
                    echo '<a href="#" id=' . $row['pages_id'] . '>';
                }
                echo $row['title'];
                echo '</a>';
            } else {
                echo $row['title'];
            }

            // use recursion
            if ($open) {
                get_pages_tree_jqueryui($row['pages_id'], $id, $array_path, $a, $href, $open, $depth + 1);
            } else {

                // open nodes from breadcrumb path
                if (in_array($row['pages_id'], $array_path)) {
                    get_pages_tree_jqueryui($row['pages_id'], $id, $array_path, $a, $href, $open, $depth + 1);
                }
            }
            echo "</li>\n";
        }
        echo str_repeat("\t", $depth);
        echo "</ul>\n";
    }
}

/**
 * @param int $parent_id
 * @param $id
 * @param $array_path
 * @param bool $a
 * @param $href
 * @param bool $open
 * @param $depth
 * @return string
 */
function get_pages_tree_jqueryui_published($parent_id = 0, $id, $array_path, $a = true, $href, $open = true, $depth)
{
    $pages = new Pages();
    $html = '';
    $rows = $pages->getPagesTreePublished($parent_id);
    if (count($rows)) {
        $html .= str_repeat("\t", $depth);
        $html .= "<ul>\n";
        foreach ($rows as $row) {
            $class = '';
            $html .= str_repeat("\t", $depth);
            $html .= "\t<li" . $class . " id=" . $row['pages_id'] . ">";
            if ($a) {
                if (strlen($row['pages_id_link']) > 0) {
                    $html .= '<a href="' . $href . '?id=' . $row['pages_id'] . '" id=' . $row['pages_id'] . '>';
                } else {
                    $html .= '<a href="' . $href . '?id=' . $row['pages_id'] . '" id=' . $row['pages_id'] . '>';
                }
                $html .= $row['title'];
                $html .= '</a>';
            } else {
                $html .= $row['title'];
            }

            // use recursion
            if ($open) {
                get_pages_tree_jqueryui_published($row['pages_id'], $id, $array_path, $a, $href, $open, $depth + 1);
            } else {

                // open nodes from breadcrumb path
                if (in_array($row['pages_id'], $array_path)) {
                    get_pages_tree_jqueryui_published($row['pages_id'], $id, $array_path, $a, $href, $open, $depth + 1);
                }
            }
            $html .= "</li>\n";
        }
        $html .= str_repeat("\t", $depth);
        $html .= "</ul>\n";
    }
    return $html;
}


/**
 * @param $id
 * @param $title
 */
function get_stories_box($id, $title, $style)
{
    echo '<div id="' . $id . '" class="portlet" style="'.$style.'">';
    echo $title;
    echo '</div>';
}

/**
 * @param $id
 * @param $title
 * @param $header
 * @param $footer
 */
function get_widgets_box($id, $title, $header, $footer)
{
    echo '<div id="' . $id . '" class="portlet ui-widget ui-widget-content">';
    echo '<span class="toolbar_widgets_edit"><button id="' . $id . '" class="btn_widgets_edit_view">&raquo;</button></span> ';
    echo '<span class="cms_edit">' . $title;
    echo ' (' . $id . ')';
    echo ' | ';
    echo $header;
    echo ' &raquo; ';
    echo $footer;
    echo '</span>';
    echo '</div>';
}

/**
 * @param $rows
 * @param $str
 */
function get_widgets_content($rows, $str)
{
    if (isset($rows)) {
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if ($row['area'] == $str) {
                    get_widgets_box($row['pages_widgets_id'], $row['widgets_title'], $row['widgets_header'], $row['widgets_footer']);
                }
            }
        }
    }
}

/**
 * @param $rows 
 * @param $str
 * @param $content_width
 */
function show_widgets_content($rows, $str, $content_width)
{
    if (isset($rows)) {
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if ($row['area'] == $str) {                    
                    $width = $content_width;
                    echo '<div class="widgets-wrapper" style="width:100%">';
                    $w = new PagesWidgets();
                    $pages_widgets_id = $row['pages_widgets_id'];
					$pages_id = $row['pages_id'];
                    $w->showPagesWidgets($pages_widgets_id, $pages_id, $width);
                    echo '</div>';
                }
            }
        }
    }
}

/**
 * @param $rows
 * @param $str
 * @param $col_width
 * @param $stories_wide_teaser_image_align
 * @param $stories_wide_teaser_image_width
 * @param $stories_last_modified
 * @param $dtz
 */
function get_box_story_content_selected($rows, $languages, $content_percent_width, $wrapper_content_width, $stories_selected_area, $stories_columns, $stories_css_class, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_image_copyright, $stories_last_modified, $stories_limit, $stories_filter, $dtz)
{
        
    if (isset($rows)) {
        $image = new Image();
        $string = "";
        foreach ($rows as $row) {
            if (!isset($_SESSION['users_id'])) {
                if ($row['access'] < 2) {
                    continue;
                }
            }
            if ($row['container'] == $stories_selected_area) {
                $title = $row['story_custom_title'];
                $title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];
                $pages_id = $row['pages_id'];
                $css_class = (strlen($row['story_css_class']) > 0) ? $row['story_css_class'] : '';
                $css_class = strlen($stories_css_class) ? $stories_css_class : $css_class;
                $story = $row['story_content'];
                $story_wide_teaser_image = $row['story_wide_teaser_image'];
                $utc = $row['utc_modified'];
                $date = get_utc_dtz($utc, $dtz, 'Y-m-d H:i');
                $date = $stories_last_modified == 1 ? get_utc_dtz($row['utc_modified'], $dtz, 'Y-m-d H:i') : '';
                $caption = isset($row['filename']) ? $row['caption'] : '';                
                $style_wrapper = $stories_columns == 1 && $stories_selected_area  == "main" ? "" : "width:100%"; 
                $argh = "stories_selected_area: " . $stories_selected_area . ", content_percent_width: " . $content_percent_width . ", wrapper_content_width: " . $wrapper_content_width .", stories_columns: " . $stories_columns;
                $add_class_padding = strlen($css_class) ? "stories-padding" : ""; 
                    
                if ($content_percent_width <= 33 || $stories_columns == 1) {
                        
                    $string .= '<div class="stories-wrapper '.$stories_css_class.'" style="'.$style_wrapper.'">';
                    if ($row['story_link']) {
                        $string .= '<a class="stories" href="pages.php?id=' . $row['pages_id'] . '">';
                    }                    
                    $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $wrapper_content_width) : '';
                    if (isset($row['filename'])) {
                        $string .= '<img src="' . $optimzed_image . '" class="fluid" alt="' . $caption . $stories_selected_area .'" />';
                    }
                    $string .= '<div class="stories-content ' . $css_class . ' '.$add_class_padding.'">';
                    $string .= '<div class=" stories-content' . $css_class . '" style="border:0;">';
                    if ($title == 0) {
                        $string .= '<h4 class="stories-title" >' . $title_value .'</h4>';
                    }
                    if ($stories_last_modified == 1) {
                        $string .= '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                    }
                    $string .= $story;
                    $string .= '</div></div>';
                    if ($row['story_link']) {
                        $string .= '</a>';
                    }
                    $string .= '</div>';
    
                } else {

                    $string .= '<div class="stories-wrapper" style="'.$style_wrapper.'">';
                    if ($row['story_link']) {
                        $string .= '<a class="stories" href="pages.php?id=' . $row['pages_id'] . '">';
                    }
                    switch ($story_wide_teaser_image) {
                        case 0:

                            $string .= '<div class="stories-content ' . $css_class . ' '.$add_class_padding.'">';
                            if ($title == 0) {
                                $string .=  '<h3 class="stories-title">' . $title_value .'</h3>';
                            }
                            if ($stories_last_modified == 1) {
                                $string .=  '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                            }
                            $string .=  $story;
                            $string .= '</div>';
                            break;
                        case 1:
                            $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $wrapper_content_width) : '';
                            if (isset($row['filename'])) {
                                $string .= '<img src="' . $optimzed_image . '" class="fluid" alt="' . $caption . '" />';
                            }

                            $string .= '<div class="stories-content ' . $css_class . ' '.$add_class_padding.'">';
                            if ($title == 0) {
                                $string .= '<h3 class="stories-title">' . $title_value . '</h3>';
                            }
                            if ($stories_last_modified == 1) {
                                $string .= '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                            }
                            $string .= $story;
                            $string .= '</div>';
                            break;
                        case 2:
                            $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $wrapper_content_width) : '';
                            $string .= '<div class="stories-content ' . $css_class . ' '.$add_class_padding.'">';
                            if ($title == 0) {
                                $string .= '<h3 class="stories-title">' . $title_value . '</h3>';
                            }
                            if ($stories_last_modified == 1) {
                                $string .= '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                            }
                            $teaser_image_class = $stories_wide_teaser_image_align == 0 ? 'float-left' : 'float-right';
                            $string .= '<div class="stories-content">';
                            if (isset($row['filename'])) {
                                $string .= '<img src="' . $optimzed_image . '" style="width:' . $stories_wide_teaser_image_width . '%;height:auto;" class="' . $teaser_image_class . '" alt="' . $caption . '" />';
                            }
                            $string .= $story;
                            $string .= '</div>';
                            $string .= '<div style="clear:both"></div>';
                            $string .= '</div>';
                            break;
                    }
                    if ($row['story_link']) {
                        $string .= '</a>';
                    }
                    $string .= '</div>';
                    
                }
                //$string .= $wrapper_content_width < 500 ? '' : '</div>';
            }
        }

        return $string;;
    }
}




function print_story_events($pages, $languages, $cms_dir, $wrapper_content_width, $stories_event_dates, $stories_event_dates_filter, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_last_modified, $dtz)
{
    $html = "";
    if($stories_event_dates) {
        $search = $stories_event_dates_filter;
        $date = date('Y-m-d');
        $rows_event_stories = $pages->getPagesStoryContentPublishEvent($search, $date, $period='next');
        $html = '<input type="hidden" id="stories_event_dates_filter" value="'.$search.'" />';
        $html .= '<a href="#" id="stories_event_previous" class="stories_events_link">'.translate("previous", "story_previous", $languages).'</a>';
        $html .= '&nbsp;<span id="ajax_spinner_stories_event_previous" style="display:none;"><img src="'.CMS_DIR.'/cms/css/images/spinner_1.gif" alt="spinner" /></span>';
        $html .= "\n".'<div id="stories_events" style="width:100%;">';					
        $html .= get_box_story_events($rows_event_stories, $wrapper_content_width, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_last_modified, $dtz);
        $html .= '</div>'."\n";
        $html .= '<a href="#" id="stories_event_next" class="stories_events_link">'.translate("coming", "story_coming", $languages).'</a>';
        $html .= '&nbsp;<span id="ajax_spinner_stories_event_next" style="display:none;"><img src="'.CMS_DIR.'/cms/css/images/spinner_1.gif" alt="spinner" /></span>';
    }
    echo $html;
}






function print_story_child($pages, $languages, $cms_dir, $id, $wrapper_content_width, $stories_child, $stories_child_area, $stories_css_class, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_image_copyright, $stories_last_modified, $dtz)
{
    if($stories_child) {

        $rows_child = $pages->getPagesStoryContentPublishChild($id);
        echo '<div id="content-child-stories" style="width:100%;">';
            echo '<div id="stories-child-masonry" style="margin:0 auto;clear:both;">';
            echo '<div class="grid-sizer"></div>';
            
            switch($stories_child_area) {
                case 1:
                    get_box_story_content_child_floats($rows_child, $wrapper_content_width, $layoutbox=1, $stories_child_area, $stories_image_copyright, $stories_last_modified, $dtz, $randomize=false, $languages);
                break;
                case 2:
                    get_box_story_content_child_floats($rows_child, $wrapper_content_width, $layoutbox=2, $stories_child_area, $stories_image_copyright, $stories_last_modified, $dtz, $randomize=false, $languages);
                break;
                case 3:
                    get_box_story_content_child_floats($rows_child, $wrapper_content_width, $layoutbox=3, $stories_child_area, $stories_image_copyright, $stories_last_modified, $dtz, $randomize=false, $languages);
                break;
                case 4:
                    get_box_story_content_child_floats($rows_child, $wrapper_content_width, $layoutbox=4, $stories_child_area, $stories_image_copyright, $stories_last_modified, $dtz, $randomize=false, $languages);
                break;
                case 5:
                    get_box_story_content_child_floats($rows_child, $wrapper_content_width, $layoutbox=5, $stories_child_area, $stories_image_copyright, $stories_last_modified, $dtz, $randomize=true, $languages);
                break;
                case 6:
                    get_box_story_content_child_floats($rows_child, $wrapper_content_width, $layoutbox=6, $stories_child_area, $stories_image_copyright, $stories_last_modified, $dtz, $randomize=true, $languages);
                break;
                case 7:
                    get_box_story_content_child($rows_child, $wrapper_content_width, $stories_css_class, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_last_modified, $dtz);
                break;
            }
            echo '</div>';
        echo '</div>';
    }
}









/**
 * @param $rows_event_stories
 * @param $column_width
 * @param $stories_wide_teaser_image_align
 * @param $stories_wide_teaser_image_width
 * @param $stories_last_modified
 * @param $dtz
 */
function get_box_story_events($rows_event_stories, $column_width, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_last_modified, $dtz)
{
    $image = new Image();
    $html = "";
    foreach ($rows_event_stories as $row) {
        $title = $row['story_custom_title'];
        $title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];
        $pages_id = $row['pages_id'];
        $ratio = $row['ratio'];
        $css_class = (strlen($row['story_css_class']) > 0) ? $row['story_css_class'] : '';
        $story = $row['story_content'];
        $story_wide_teaser_image = $row['story_wide_teaser_image'];
        $caption = isset($row['filename']) ? $row['caption'] : '';
        $utc = $row['story_event_date'];
        $ts = strtotime($utc);
        $weekday = transl(date('l', $ts));
        $month = transl(date('F', $ts));
        $year = date('Y', $ts) == date('Y') ? '' : date('Y', $ts);
        $time = date('H:i', $ts) == '00:00' ? '' : date('H:i', $ts);
        $date = transl(date('l', $ts)) . ' ' . date('j', $ts) . ' ' . strtolower(transl(date('F', $ts))) . ' ' . $year . ' ' . $time;
        $class = $utc < date('Y-m-d') ? ' story-event-history' : '';
        $a = $a_start = $a_end = '';

        if ($utc > date('Y-m-d')) {
            $a_start = '<a href="' . CMS_DIR . '/cms/pages.php?id=' . $pages_id . '">';
            $a_end = '</a>';
        } else {
            $a = '<a href="' . CMS_DIR . '/cms/pages.php?id=' . $pages_id . '" class="stories_events_link">läs</a>';
        }
        if ($row['story_link'] == 0) {
            $a = $a_start = $a_end = '';
        }

        $html .= "\n" . '<div class="story-event' . $class . '" id="' . $utc . '">';
        $html .= $a_start . '<h3 class="stories-event-date">' . $date . '</h3>' . $a_end;
        $stories_event_meta = '<div class="stories-event-meta"><span class="stories-event-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>'; 
        switch ($story_wide_teaser_image) {
            case 0:
            $html .= '<div class="stories-event-content clear' . $css_class . '">';
                if ($title == 0) {
                    $html .= '<h3 class="stories-event-title">' . $title_value . '</h3>';
                }
                if ($stories_last_modified == 1) {
                    $html .= $stories_event_meta;
                }
                $html .= $story . '</div>';
                break;
            case 1:
                $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $column_width) : '';
                if (isset($row['filename'])) {
                    $html .= '<img src="' . $optimzed_image . '" class="fluid" alt="' . $caption . '" />';
                }
                $html .= '<div class="stories-event-content clear' . $css_class . '">';
                if ($title == 0) {
                    $html .= '<h3 class="stories-event-title">' . $title_value . '</h3>';
                }
                if ($stories_last_modified == 1) {
                    $html .= $stories_event_meta;
                }
                $html .= $story . '</div>';
                break;
            case 2:
                $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $column_width * $stories_wide_teaser_image_width / 100) : '';                
                $html .= '<div class="stories-content clear' . $css_class . '">';
                if (isset($row['filename'])) {
                    $teaser_image_class = $stories_wide_teaser_image_align == 0 ? 'float-left' : 'float-right';
                    $html .= '<img src="' . $optimzed_image . '" class="fluid ' . $teaser_image_class . '" alt="' . $caption . '" style="width:'.$stories_wide_teaser_image_width .'%"/>';
                }
                if ($title == 0) {
                    $html .= '<h3 class="stories-title">' . $title_value . '</h3>';
                }
                $html .= $story . '</div>';
                if ($stories_last_modified == 1) {
                    $html .= $stories_event_meta;
                }
                break;
        }
        $html .= '</div>' . "\n";
        $html .= $a;
    }

    return $html;
}


function print__story__promoted($rows_promoted, $languages, $cms_dir, $id, $wrapper_content_width, $stories_area, $stories_promoted_area, $stories_css_class, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_image_copyright, $stories_last_modified, $stories_limit, $stories_filter, $dtz)
{
    if (!$rows_promoted) {return null;}
    if (in_array($stories_promoted_area, $stories_area)) {
        $html = '<div id="content-promoted-stories" class="clearfix">';
        $html .= get_box_story_content_promoted($rows_promoted, $wrapper_content_width, $stories_last_modified, $stories_image_copyright, $stories_css_class, $dtz, $languages);
        $html .= '</div>';
        echo $html;
    }
}



function print__story__child($rows_child, $languages, $cms_dir, $id, $wrapper_content_width, $stories_area, $stories_child_area, $stories_css_class, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_image_copyright, $stories_last_modified, $stories_limit, $stories_filter, $dtz)
{
    if (!$rows_child) {return null;}
    if (in_array($stories_child_area, $stories_area)) {
        $html = '<div id="content-child-stories" class="clearfix">';
        $html .= get_box_story_content($rows_child, $languages, $wrapper_content_width, $stories_area, $stories_child_area, $stories_css_class, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_image_copyright, $stories_last_modified, $stories_limit, $stories_filter, $dtz);
        $html .= '</div>';
        echo $html;
    }
}


function print__story__selected($rows_selected, $languages, $cms_dir, $id, $content_percent_width, $wrapper_content_width, $stories_selected_area, $stories_columns, $stories_css_class, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_image_copyright, $stories_last_modified, $stories_limit, $stories_filter, $dtz)
{
    if (!$rows_selected) {return null;}

    //if (in_array($stories_child_area, $stories_area)) {
        $html = '<div id="content-selected-stories" class="clearfix">';
        $html .= get_box_story_content_selected($rows_selected, $languages, $content_percent_width, $wrapper_content_width, $stories_selected_area, $stories_columns,$stories_css_class, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_image_copyright, $stories_last_modified, $stories_limit, $stories_filter, $dtz);
        $html .= '</div>';
        echo $html;
    //}
}



function get_box_story_content($rows, $languages, $wrapper_content_width, $stories_area, $stories_child_area, $stories_css_class, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_image_copyright, $stories_last_modified, $stories_limit, $stories_filter, $dtz)
{
    $html = "";
    $image = new Image();
    
    foreach ($rows as $row) {

        if (!isset($_SESSION['users_id'])) {
            if ($row['access'] < 2) {
                continue;
            }
        }

        $title = $row['story_custom_title'];
        $title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];
        $date = get_utc_dtz($row['utc_modified'], $dtz, 'Y-m-d H:i');
        $story = $row['story_content'];
        $story_wide_teaser_image = $row['story_wide_teaser_image'];
        $caption = $row['caption'];
        $alt = $row['alt'];
        $copyright = strlen($row['copyright']) ? translate("Photo:", "site_photo", $languages) . ' ' . $row['copyright'] : '';
        $css_class = strlen($row['story_css_class']) > 0 ? $row['story_css_class'] : '';
        $css_class = strlen($stories_css_class) > 0 ? $stories_css_class : $css_class;
        $a_start = $row['story_link'] ? '<a class="stories" href="?id=' . $row['pages_id'] . '">' : '';  
        $a_end = $row['story_link'] ? '</a>' : '';  
        $stories_meta = '<div class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></div>'; 
        $teaser_image_class = $stories_wide_teaser_image_align == 0 ? 'float-left' : 'float-right';
        $add_class_padding = strlen($css_class) ? "stories-padding" : ""; 

        switch ($stories_child_area) {

            case 1: // left sidebar | top image teaser
            case 3: // right sidebar | top image teaser
                $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $wrapper_content_width) : '';
                $html .= '<div class="column" style="width:100%">';
                $html .= $a_start;
                $html .= isset($row['filename']) ? '<img src="' . $optimzed_image . '" class="fluid" alt="' . $alt . '" title="' . $copyright . '"/>' : '';
                $html .= '<div class="stories-content '. $css_class .' '.$add_class_padding.'">';
                $html .= $title == 0 ? '<h3 class="stories-title">' . $title_value . '</h3>' : '';        
                $html .= $a_end;
                $html .= $stories_last_modified == 1 ? $stories_meta : '';
                $html .= $story . '</div></div>';
                
            break;
            case 2: // left sidebar | align image teaser to story
            case 4: // right sidebar | align image teaser to story
                $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $wrapper_content_width * $stories_wide_teaser_image_width / 100) : '';
                $html .= '<div class="column" style="width:100%;display:grid">';
                $html .= '<div class="stories-content '. $css_class .' '.$add_class_padding.'">';
                $html .= $a_start;
                $html .= $title == 0 ? '<h3 class="stories-title">' . $title_value . '</h3>' : '';                
                $html .= $a_end;
                $html .= $stories_last_modified == 1 ? $stories_meta : '';
                $html .= $a_start;
                $html .= isset($row['filename']) ? '<img src="' . $optimzed_image . '" class="fluid ' . $teaser_image_class . '" alt="' . $alt . '" title="' . $copyright . '"/>' : '';
                $html .= $a_end;
                $html .= $story . '</div></div>';
                
            break;
            
            case 5: // content | columns | top image teaser
                $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $wrapper_content_width * 33 / 100) : '';
                $html .= '<div class="stories-cell ' . $css_class . '">';
                $html .= $a_start;
                $html .= isset($row['filename']) ? '<img src="' . $optimzed_image . '" class="fluid" alt="' . $alt . '" title="' . $copyright . '"/>' : '';
                $html .= $a_end;
                $html .= '<div class="stories-content">';
                $html .= $a_start;
                $html .= $title == 0 ? '<h3 class="stories-title">' . $title_value . '</h3>' : '';        
                $html .= $a_end;
                $html .= $stories_last_modified == 1 ? $stories_meta : '';
                $html .= $story . '</div></div>';

            break;
            case 6: // content | columns | align image teaser
                $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $wrapper_content_width * 20 / 100) : '';
                $html .= '<div class="stories-cell">';
                $html .= '<div class="stories-content ' . $css_class . '">';                
                $html .= $a_start;
                $html .= $title == 0 ? '<h3 class="stories-title">' . $title_value . '</h3>' : '';        
                $html .= $a_end;
                $html .= $stories_last_modified == 1 ? $stories_meta : '';
                $html .= $a_start;
                $html .= isset($row['filename']) ? '<img src="' . $optimzed_image . '" class="fluid ' . $teaser_image_class . '" alt="' . $alt . '" title="' . $copyright . '" style="width:'.$stories_wide_teaser_image_width .'%"/>' : '';
                $html .= $a_end;
                $html .= $story . '</div></div>';
                
        
            break;

            case 7: // content | rows | align image teaser to title
                $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $wrapper_content_width * $stories_wide_teaser_image_width / 100) : '';
                $html .= '<div class="stories-wrapper clear">';                
                $html .= '<div class="stories-content ' . $css_class . '">';
                $html .= $a_start;
                $html .= isset($row['filename']) ? '<img src="' . $optimzed_image . '" class="fluid ' . $teaser_image_class . '" alt="' . $alt . '" title="' . $copyright . '" style="width:'.$stories_wide_teaser_image_width .'%"/>' : '';
                $html .= $title == 0 ? '<h3 class="stories-title">' . $title_value . '</h3>' : '';
                $html .= $a_end;
                $html .= $stories_last_modified == 1 ? $stories_meta : '';
                $html .= $story . '</div></div>';
                

            break;

            case 8: // content | rows | align image teaser to story
                $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $wrapper_content_width * $stories_wide_teaser_image_width / 100) : '';
                $html .= '<div class="stories-wrapper clear">';
                $html .= '<div class="stories-content ' . $css_class . '">';
                $html .= $a_start;
                $html .= $title == 0 ? '<h3 class="stories-title">' . $title_value . '</h3>' : '';        
                $html .= $a_end;
                $html .= $stories_last_modified == 1 ? $stories_meta : '';
                $html .= $a_start;
                $html .= isset($row['filename']) ? '<img src="' . $optimzed_image . '" class="fluid ' . $teaser_image_class . '" alt="' . $alt . '" title="' . $copyright . '" style="width:'.$stories_wide_teaser_image_width .'%"/>' : '';
                $html .= $a_end;
                $html .= $story . '</div></div>';
                

            break;

            case 9: // content | rows | exclude image teaser
                $html .= '<div class="stories-wrapper clear">';
                $html .= $a_start;        
                $html .= '<div class="stories-content ' . $css_class . '">';
                $html .= $title == 0 ? '<h3 class="stories-title">' . $title_value . '</h3>' : '';        
                $html .= $stories_last_modified == 1 ? $stories_meta : '';
                $html .= $story . '</div></div>';
                $html .= $a_end;

            break;

        }







    
    }


    return $html; 
}



/**
 * @param $rows
 * @param $col_width
 * @param $css_class_uniformed
 * @param $stories_wide_teaser_image_align
 * @param $stories_wide_teaser_image_width
 * @param $stories_last_modified
 * @param $dtz
 */
function get_box_story_content_child($rows, $wrapper_content_width, $css_class_uniformed, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_last_modified, $dtz)
{   
    $image = new Image();
    echo "A";
    if (isset($rows)) {
        echo "B";
        foreach ($rows as $row) {
            echo "C";
            if (!isset($_SESSION['users_id'])) {
                if ($row['access'] < 2) {
                    continue;
                }
            }

            $title = $row['story_custom_title'];
            $title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];
            $utc = $row['utc_modified'];
            $date = get_utc_dtz($utc, $dtz, 'Y-m-d H:i');
            $story = $row['story_content'];
            $story_wide_teaser_image = $row['story_wide_teaser_image'];
            $caption = $row['caption'];
            $css_class = (strlen($row['story_css_class']) > 0) ? $row['story_css_class'] : '';
            $css_class = (strlen($css_class_uniformed) > 0) ? $css_class_uniformed : $css_class;

            echo '<div class="stories-wrapper" style="width:100%;">';
            if ($row['story_link']) {
                echo '<a class="stories" href="pages.php?id=' . $row['pages_id'] . '">';
            }
            
            $optimzed_image = isset($row['filename']) ? $image->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $row['filename'], $column_width * $stories_wide_teaser_image_width / 100) : '';
            $html .= '<div class="stories-content clear' . $css_class . '">';
            if (isset($row['filename'])) {
                $teaser_image_class = $stories_wide_teaser_image_align == 0 ? 'float-left' : 'float-right';
                $html .= '<img src="' . $optimzed_image . '" class="fluid ' . $teaser_image_class . '" alt="' . $caption . '" style="width:'.$stories_wide_teaser_image_width .'%"/>';
            }
            if ($title == 0) {
                $html .= '<h3 class="stories-title">' . $title_value . '</h3>';
            }
            $html .= $story . '</div>';
            if ($stories_last_modified == 1) {
                $html .= $stories_event_meta;
            }
            echo $html;

            /*
            switch ($col_width) {
                case 306:
                case 474:
                case 726:

                    switch ($story_wide_teaser_image) {
                        case 0:

                            echo '<div class="stories-content ' . $css_class . '">';
                            if ($title == 0) {
                                echo '<h3 class="stories-title">' . $title_value . '</h3>';
                            }
                            if ($stories_last_modified == 1) {
                                echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                            }
                            echo $story_wide;
                            echo '</div>';
                            break;
                        case 1:

                            $i = 726;
                            $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';

                            echo '<div class="stories-content ' . $css_class . '">';
                            if (!is_null($row['filename'])) {
                                echo '<img src="' . $img . '" class="fluid" alt="' . $caption . '" />';
                            }
                            echo '<div class="stories-content ' . $css_class . '" style="border:0;">';
                            if ($title == 0) {
                                echo '<h3 class="stories-title">' . $title_value . '</h3>';
                            }
                            if ($stories_last_modified == 1) {
                                echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                            }
                            echo $story_wide;
                            echo '</div></div>';
                            break;
                        case 2:

                            $i = 222;
                            $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';

                            echo '<div class="stories-content ' . $css_class . '">';
                            if ($title == 0) {
                                echo '<h3 class="stories-title">' . $title_value . '</h3>';
                            }
                            if ($stories_last_modified == 1) {
                                echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                            }
                            echo '<div class="stories-content">';
                            if (isset($row['filename'])) {
                                $teaser_image_class = $stories_wide_teaser_image_align == 0 ? 'float-left' : 'float-right';
                                echo '<img src="' . $img . '" style="width:' . $stories_wide_teaser_image_width . 'px;height:auto;" class="' . $teaser_image_class . '" alt="' . $caption . '" />';
                            }
                            echo $story_wide;
                            echo '</div>';
                            echo '</div>';
                            break;
                    }

                    break;


                    break;
            }
            */
            if ($row['story_link']) {
                echo '</a>';
            }
            echo '</div>';

        }
    }
}


/**
 * @param $rows
 * @param $content_width
 * @param $box_layout
 * @param $css_class_uniformed
 * @param $show_image_copyright
 * @param $stories_last_modified
 * @param $dtz
 * @param $randomize
 * @param $languages
 */
function get_box_story_content_child_floats($rows, $content_width, $box_layout, $css_class_uniformed, $show_image_copyright, $stories_last_modified, $dtz, $randomize, $languages)
{
    if (isset($rows)) {
        if ($randomize == true) {
            shuffle($rows);
        }
        $number = 0;
        $item = 'item';
        foreach ($rows as $row) {
            $number++;
            if (!isset($_SESSION['users_id'])) {
                if ($row['access'] < 2) {
                    continue;
                }
            }

            $title = $row['story_custom_title'];
            $title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];
            $story = $row['story_content'];
            $story_wide_teaser_image = $row['story_wide_teaser_image'];
            $ratio = $row['ratio'];
            $caption = $row['caption'];
            $date = $stories_last_modified == 1 ? get_utc_dtz($row['utc_modified'], $dtz, 'Y-m-d H:i') : '';
            $css_class = (strlen($row['story_css_class']) > 0) ? $row['story_css_class'] : '';
            $css_class = (strlen($css_class_uniformed) > 0) ? $css_class_uniformed : $css_class;
            $item = ($item == 'item') ? 'item w2' : 'item';

            switch ($box_layout) {
                case 1:
                    $item = 'item w4';
                    break;
                case 2:
                    $item = 'item';
                    break;
                case 3:
                case 5:

                    if (($number % 3) == 0) {
                        $item = 'item w4';
                    }
                    if (($number % 7) == 0) {
                        $item = 'item w2';
                    }
                    $item = ($number % 4 == 0) ? 'item w3' : $item;

                    break;
                case 4:
                case 6:

                    if (($number % 3) == 0) {
                        $item = 'item w3';
                    }
                    if (($number % 4) == 0) {
                        $item = 'item w6';
                    }
                    if (($number % 5) == 0) {
                        $item = 'item w5';
                    }
                    $item = ($number % 3 == 0) ? 'item' : $item;

                    break;
            }

            echo '<div class="' . $item . '">';
            $img = isset($row['filename']) ? str_replace('_100.', '_726.', $row['filename']) : '';
            $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . $img : '';

            if ($row['story_link']) {
                echo '<a class="stories" href="pages.php?id=' . $row['pages_id'] . '">';
            }
            echo '<div class="stories-content ' . $css_class . '">';
            if (!is_null($row['filename'])) {
                echo '<img src="' . $img . '" class="fluid" alt="' . $caption . '" />';
            }

            if (strlen($row['copyright']) && $show_image_copyright == 1) {
                echo '<div class="stories-image-meta">' . translate("Photo:", "site_photo", $languages) . ' ' . $row['copyright'] . '</div>';
            }
            if ($title == 0) {
                echo '<h5 class="stories-title">' . $title_value . '</h5>';
            }
            echo $story;
            if ($stories_last_modified == 1) {
                echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Last modified: ' . $date . '</abbr></span></div>';
            }

            echo '</div>';
            if ($row['story_link']) {
                echo '</a>';
            }
            echo '</div>';

        }
    }
}

function get_sample_data() 
{
    // allow templates and sample views if logged in user has CMS role as contributor and above
    if(isset($_SESSION['role_CMS'])) {
        if($_SESSION['role_CMS'] >= 2) {
            if(isset($_GET['sample'])) {
                $sample = true;                
                $sample_data_templates = get_sample_links();
                $arr = get_sample_array();
                switch($_GET['sample']) {
                    case 1:
                        $arr['template'] = 1;
                    break;
                    case 2:
                        $arr['template'] = 2;
                    break;
                    case 3:
                        $arr['template'] = 4;
                    break;
                    case 4:
                        $arr['template'] = 7;
                    break;
                    case 5:
                        $arr['template'] = 8;
                    break;
                }
                return $arr;
            }
        }
    }    
}


/**
 * template links used to show sample page
 *
 * @return array
 */
function get_sample_links()
{
    $rows = array(0 => array("title" => "Template sidebars", "link" => "1"), 1 => array("title" => "Template left sidebar", "link" => "2"),
        2 => array("title" => "Template right sidebar", "link" => "3"), 3 => array("title" => "Template joined sidebars", "link" => "4"),
        4 => array("title" => "Template panorama", "link" => "5")
    );
    return $rows;
}

/**
 * sample
 *
 * @param $rows
 */
function get_sample_tree_menu($rows)
{
    echo '<div id="sidebar-navigation">';
    echo '<div id="sidebar-navigation-tree">';

    if (count($rows)) {
        echo "<ul class=\"nav-tree\">\n";
        foreach ($rows as $row) {
            echo '<li class="node">';
            echo '<a class="node" href="?sample=' . $row['link'] . '">';
            echo $row['title'];
            echo '</a>';
            echo "</li>\n";
        }
        echo "</ul>\n";
    }
    echo '</div>';
    echo "\n" . '<div class="datepicker" style="margin-top:20px;"></div>';
    echo "\n" . '<button id="button_sample">A button</button>';
    echo "\n" . '<input type="text" value="Input field">';
    echo "\n" . '<textarea>Some more....</textarea>';
    echo "\n" . '<div class="slider" style="width:90%; margin:15px;"></div>';
    echo "\n" . '<div class="tabs"><ul><li><a href="#tabs-1">Carpe</a></li><li><a href="#tabs-2">Diem</a></li></ul>
		<div id="tabs-1"><p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Donec sollicitudin mi sit amet mauris. </p></div>
		<div id="tabs-2"><p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id nunc. </p></div>
		</div>';
    echo '</div>';
}


/**
 * sample...
 *
 * @param $rows
 */
function get_sample_navigation_root($rows)
{
    echo "\n<ul id=\"navigation-root\">";
    foreach ($rows as $row) {
        echo "\n\t<li>";
        echo '<a href="?sample=' . $row['link'] . '">';
        echo '<span>';
        echo $row['title'];
        echo '</span></a></li>';
    }
    echo "\n</ul>\n";
}

/**
 * sample...
 */
function get_sample_sidebar()
{
    echo '<div id="right-sidebar-stories" class="stories-column" style="width:100%;margin-top:10px;">';
    echo '<div class="stories-wrapper" style="width:100%;">';
    echo '<a class="stories" href="pages.php?sample=1">';
    echo '<div><img src="../content/sample/sample1.jpg"  class="fluid" style="width:100%;" alt="Sample story" />';
    echo '<h3 class="stories-title">Hi!</h3>';
    echo 'Template sidebars';
    echo '</div>';
    echo '</a>';
    echo '</div>';
    echo '<div class="stories-wrapper" style="width:100%;">';
    echo '<a class="stories" href="pages.php?sample=2">';
    echo '<div><img src="../content/sample/sample2.jpg"  class="fluid" style="width:100%;" alt="Sample story" />';
    echo '<h3 class="stories-title">Hej!</h3>';
    echo 'Template left sidebar';
    echo '</div>';
    echo '</a>';
    echo '</div>';
    echo '<div class="stories-wrapper" style="width:100%;">';
    echo '<a class="stories" href="pages.php?sample=3">';
    echo '<div><img src="../content/sample/sample3.jpg"  class="fluid" style="width:100%;" alt="Sample story" />';
    echo '<h3 class="stories-title">Aloha!</h3>';
    echo 'Template right sidebar';
    echo '</div>';
    echo '</a>';
    echo '</div>';
    echo '<div class="stories-wrapper" style="width:100%;">';
    echo '<a class="stories" href="pages.php?sample=4">';
    echo '<div>';
    echo '<h3 class="stories-title">Ciao!</h3>';
    echo 'Template joined sidebars';
    echo '</div>';
    echo '</a>';
    echo '</div>';
    echo '<div class="stories-wrapper" style="width:100%;">';
    echo '<a class="stories" href="pages.php?sample=5">';
    echo '<div>';
    echo '<h3 class="stories-title">Salut!</h3>';
    echo 'Template panorama';
    echo '</div>';
    echo '</a>';
    echo '</div>';
    echo '</div>';
}

/**
 * template links used to show sample page
 *
 * @return array
 */
function get_sample_array()
{
    $arr = array();
    $arr['title'] = 'Sample content';
    $arr['content'] = '<img src="../content/sample/sample1.jpg" alt="Sample image" class="fluid" style="width:50%;float:right;margin:0 0 20px 20px;" />';
    $arr['content'] .= "\n" . '<p><a href="?sample=2" class="link">Sample content</a> - use querystring <i>pages.php?sample</i></p>';
    $arr['content'] .= "\n" . '<p><b>This page is designed using Storiesaround templates and CSS - Cascading Style Sheet. Change window size to discover responsive web design.</b></p>';
    $arr['content'] .= show_lorem_ipsum(5);
    $arr['content'] .= "\n" . '<div id="content-stories" style="float:left;width:100%;min-width:200px;min-height:10px;">';
    $arr['content'] .= "\n" . '<div class="stories-column sample" style="width:31%;margin:0 4% 0 0;background:#FFEAAA;">' . show_lorem_ipsum(1) . '</div>';
    $arr['content'] .= "\n" . '<div class="stories-column sample" style="width:31%;margin:0 4% 0 0;background:#D4BA6A;">' . show_lorem_ipsum(1) . '</div>';
    $arr['content'] .= "\n" . '<div class="stories-column sample" style="width:30%;margin:0 0 0 0;background:#AA8E39;">' . show_lorem_ipsum(1) . '</div>';
    $arr['content'] .= '</div>';
    $arr['template'] = 2;
    $arr['stories_promoted'] = 1;
    $arr['title_tag'] = $arr['meta_keywords'] = $arr['meta_description'] = $arr['meta_robots'] = $arr['meta_additional'] = $arr['plugins'] = $arr['selections'] = $arr['lang'] = $arr['ads'] = $arr['events']
        = $arr['ads'] = $arr['stories_limit'] = $arr['stories_promoted_area'] = $arr['stories_wide_teaser_image_align'] = $arr['stories_wide_teaser_image_width'] = $arr['stories_last_modified'] = $arr['access']
        = $arr['content_author'] = $arr['stories_event_dates'] = $arr['stories_columns'] = $arr['stories_child'] 
        = $arr['header_caption']
        = $arr['breadcrumb']
        = $arr['utc_modified']
        = $arr['stories_event_dates_filter']
        = $arr['stories_child_area']
        = $arr['stories_css_class']
        = $arr['stories_image_copyright']
        = $arr['stories_filter']
        = $arr['stories_child_area']
        = $arr['stories_css_class']
        = $arr['stories_image_copyright']
        = $arr['stories_filter']
        = $arr['stories_equal_height'] = "";
    $arr['grid_active'] = null;
    $arr['header_image'] = json_encode(['site_header_image_0.jpg']);
    

    return $arr;
}

/**
 *
 */
function set_theme()
{
    echo '<select id="preview_theme">';
    echo '<option value="">Preview theme: (site default)</option>';
    echo '<option value="">&nbsp;</option>';
    foreach (new DirectoryIterator(CMS_ABSPATH . '/content/themes') as $fileInfo) {
        if ($fileInfo->isDot()) continue;
        $css = $fileInfo->getFilename();
        echo '<option value="' . $css . '"';
        if (isset($_SESSION['site_theme'])) {
            if ($_SESSION['site_theme'] == $css) {
                echo ' selected';
            }
        }
        echo '>Preview theme: "' . $css . '"</option>';
    }
    echo '</select>';
}


//

/**
 * @param $rows
 * @param $col_width
 * @param $stories_last_modified
 * @param $show_image_copyright
 * @param $dtz
 * @param $languages
 */
function get_box_story_content_promoted($rows, $col_width, $stories_last_modified, $show_image_copyright, $stories_css_class, $dtz, $languages)
{
    if (isset($rows)) {
        foreach ($rows as $row) {
            if (!isset($_SESSION['users_id'])) {
                if ($row['access'] < 2) {
                    continue;
                }
            }
            $title = $row['story_custom_title'];
            $title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];
            $utc = $row['utc_modified'];
            $date = get_utc_dtz($utc, $dtz, 'Y-m-d H:i');
            $css_class = (strlen($row['story_css_class']) > 0) ? $row['story_css_class'] : '';
            $css_class = strlen($stories_css_class) ? $stories_css_class : $css_class;
            $caption = $row['caption'];
            $i = $col_width > 222 ? 726 : 222;
            $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';

            if ($row['story_link']) {
                echo '<a class="stories" href="pages.php?id=' . $row['pages_id'] . '">';
            }

            echo '<div class="stories-wrapper">';
            if (!is_null($row['filename'])) {
                echo '<img src="' . $img . '"  class="fluid" alt="' . $caption . '" />';
            }
            $add_class_padding = strlen($css_class) ? "stories-padding" : ""; 
            echo '<div class="stories-content '. $css_class .' '.$add_class_padding.'" style="border:0;">';
            if (strlen($row['copyright']) && $show_image_copyright == 1) {
                echo '<div class="stories-image-meta">' . translate("Photo:", "site_photo", $languages) . ' ' . $row['copyright'] . '</div>';
            }
            if ($title == 0) {
                echo '<h3 class="stories-title" style="margin-top:0">' . $title_value . '</h3>';
            }
            if ($stories_last_modified == 1) {
                echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">' . $date . '</abbr></span></div>';
            }
            echo $row['story_content'];
            echo '</div>';
            echo '</div>';
            if ($row['story_link']) {
                echo '</a>';
            }

        }
    }
}


/**
 * @param $rows
 * @param $col_width
 * @param $stories_last_modified
 * @param $show_image_copyright
 * @param $dtz
 * @return string
 */
function get_box_story_content_bycode($rows, $col_width, $stories_last_modified, $show_image_copyright, $dtz)
{
    $str = "";
    if (isset($rows)) {
        foreach ($rows as $row) {
            if (!isset($_SESSION['users_id'])) {
                if ($row['access'] < 2) {
                    continue;
                }
            }

            $title = $row['story_custom_title'];
            $title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];
            $utc = $row['utc_modified'];
            $date = get_utc_dtz($utc, $dtz, 'Y-m-d H:i');
            $css_class = (strlen($row['story_css_class']) > 0) ? $row['story_css_class'] : '';
            $caption = $row['caption'];
            $i = $col_width > 222 ? 726 : 222;
            $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';

            if (isset($row['story_event_date'])) {

                $utc = $row['story_event_date'];
                $ts = strtotime($utc);
                $weekday = transl(date('l', $ts));
                $month = transl(date('F', $ts));
                $year = date('Y', $ts) == date('Y') ? '' : date('Y', $ts);
                $time = date('H:i', $ts) == '00:00' ? '' : date('H:i', $ts);
                $date = transl(date('l', $ts)) . ' ' . date('j', $ts) . ' ' . strtolower(transl(date('F', $ts))) . ' ' . $year . ' ' . $time;
                $pages_id = $row['pages_id'];

                $str .= '<h3 class="stories-event-date">' . $date . '</h3>';
            }

            $str .= '<div class="stories-wrapper" style="width:100%;">';

            $str .= '<a class="stories" href="pages.php?id=' . $row['pages_id'] . '">';
            $str .= '<div class="stories-content ' . $css_class . '">';
            if (!is_null($row['filename'])) {
                $str .= '<img src="' . $img . '"  class="fluid" alt="' . $caption . '" />';
            }
            $str .= '<div class="stories-content ' . $css_class . '" style="border:0;">';
            if (strlen($row['copyright']) && $show_image_copyright == 1) {
                $str .= '<div class="stories-image-meta">' . translate("Photo:", "site_photo", $languages) . ' ' . $row['copyright'] . '</div>';
            }
            if ($title == 0) {
                $str .= '<h3 class="stories-title">' . $title_value . '</h3>';
            }
            if ($stories_last_modified == 1) {
                $str .= '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">' . $date . '</abbr></span></div>';
            }
            $str .= $row['story_content'];
            $str .= '</div></div>';
            $str .= '</a>';
            $str .= '</div>';
        }
    }
    return $str;
}

/**
 * @param $rows_childs
 */
function get_box_content_child($rows_childs)
{
    if (isset($rows_childs)) {
        foreach ($rows_childs as $row) {
            get_stories_box($row['pages_id'], $row['title'], $style="");
        }
    }
}

/**
 * @param $rows_promoted
 */
function get_box_content_promoted($rows_promoted)
{
    if (isset($rows_promoted)) {
        foreach ($rows_promoted as $row) {
            get_stories_box($row['pages_id'], $row['title'], $style="");
        }
    }
}

/**
 * @param $rows_events
 */
function get_box_content_event($rows_events)
{
    if (isset($rows_events)) {
        foreach ($rows_events as $row) {
            get_stories_box($row['pages_id'], $row['title'], $style="");
        }
    }
}

/**
 * @param $rows
 * @param $str
 */
function get_box_content($rows, $str, $style)
{
    if (isset($rows)) {
        foreach ($rows as $row) {
            if ($row['container'] == $str) {
                get_stories_box($row['pages_stories_id'], $row['title'], $style);
            }
        }
    }
}

/**
 * @param $rows
 * @param $str
 */
function get_box_content_out_of_range($rows, $str)
{
    if (isset($rows)) {
        foreach ($rows as $row) {
            if (strpos($str, $row['container']) !== false) {
                get_stories_box($row['pages_stories_id'], $row['title'], $style="");
            }
        }
    }
}

/**
 * @param $area
 * @param $widget_width
 * @param $css_class
 * @param $div_id
 */
function get_widgetarea($area, $widget_width, $css_class, $div_id)
{
    $str = '<div id="' . $div_id . '" class="' . $css_class . '" style="float:left;width:' . $widget_width . ';background:#ffffcc;border: 1px dashed #cccccc;">';
    $str .= '<div align="right">';
    $str .= '<span style="font-size: 0.8em;">widget area ' . $area . '</span>';
    $str .= '</div>';
    $str .= '</div>';
    echo $str;
}

/**
 * @param $area
 * @param $cal
 * @param $id
 */
function get_calendar($area, $cal, $id)
{
    $calendar = new Calendar();

    if (is_array($cal)) {
        $calendar_categories_id = $cal['calendar_categories_id'];
        $date = $cal['date_initiate'];
        $period = $cal['period_initiate'];
        $calendar_views_id = $cal['calendar_views_id'];
        $calendar_show = $cal['calendar_show'];
    } else {
        die;
    }

    if ($cal['calendar_area'] == $area) {
        switch ($area) {
            case 'content':
                echo '<div id="content-calendars" style="float:left;width:100%;overflow:display;">';
                if ($cal['calendar_categories_id'] > 0) {
                    echo $calendar->getCalendarNav($date, 'category', $calendar_categories_id, $period);
                    echo '<div id="calendar_include">';
                    echo $calendar->getCalendarCategoriesRights($date, $href = null, $calendar_categories_id, $period);
                    echo '</div>';
                }
                if ($cal['calendar_views_id'] > 0) {
                    echo $calendar->getCalendarNav($date, 'view', $calendar_views_id, $period);
                    echo '<div id="calendar_include">';
                    echo $calendar->getCalendarViewsRights($date, $href = null, $calendar_views_id, $period);
                    echo '</div>';
                }
                echo '</div>';
                break;

            case 'left-sidebar':
                if ($calendar_categories_id > 0) {
                    echo '<div class="calendar-sidebar-events" style="width:100%;">';
                    if ($calendar_show == 1 || $calendar_show == 2) {
                        if (isset($_GET['date'])) {
                            $q = exclude_queries(array('date'));
                            echo $calendar->getCalendarNavigation($date = null, $href = "pages.php?" . $q . "&amp;date=", $max_width = true);
                        } else {
                            echo $calendar->getCalendarNavigation($date = null, $href = "pages.php?id=" . $id . "&amp;date=", $max_width = true);
                        }
                    }
                    if ($calendar_show == 1 || $calendar_show == 3) {
                        echo '<div class="calendar-events-container">';
                        echo $calendar->getCalendarEventsList($calendar_categories_id, $date = null, $href = null, $period);
                        echo '</div>';
                    }
                    echo '</div>';
                }
                break;

            case 'right-sidebar':

                if ($calendar_categories_id > 0) {
                    echo '<div class="calendar-sidebar-events" style="width:100%;">';
                    if ($calendar_show == 1 || $calendar_show == 2) {
                        if (isset($_GET['date'])) {
                            $q = exclude_queries(array('date'));
                            echo $calendar->getCalendarNavigation($date = null, $href = "pages.php?" . $q . "&amp;date=", $max_width = true);
                        } else {
                            echo $calendar->getCalendarNavigation($date = null, $href = "pages.php?id=" . $id . "&amp;date=", $max_width = true);
                        }
                    }
                    if ($calendar_show == 1 || $calendar_show == 3) {
                        echo '<div class="ui-widget ui-widget-content calendar-events-container">';
                        echo $calendar->getCalendarEventsList($calendar_categories_id, $date = null, $href = null, $period);
                        echo '</div>';
                    }
                    echo '</div>';
                }
                break;

        }
    }
}


function get_select_number($arrayOfNumbers, $current, $name, $id, $classes) 
{
    if (is_array($arrayOfNumbers)) {
        $name = strlen($name) ? ' name="'.$name.'"' : '';
        $id = strlen($id) ? ' id="'.$id.'"' : '';
        $classes = strlen($classes) ? ' class="'.$classes.'"' : '';

        $html = '<select '. $name . $id . $classes.'>';
        $selected = '';
        for ($i = 0; $i < count($arrayOfNumbers); $i++) {
            $selected = $current === $arrayOfNumbers[$i] ? ' selected' : ''; 
            $html .= '<option value="'.$arrayOfNumbers[$i].'" '.$selected.'>'.$arrayOfNumbers[$i].'</option>';
        }
        $html .= '</select>';
        return $html;
    } else {
        return;
    }
}

function get_select_strings($arrayOfStrings, $current, $name, $id, $classes) 
{
    if (is_array($arrayOfStrings)) {
        $name = strlen($name) ? ' name="'.$name.'"' : '';
        $id = strlen($id) ? ' id="'.$id.'"' : '';
        $classes = strlen($classes) ? ' class="'.$classes.'"' : '';

        $html = '<select '. $name . $id . $classes.'>';
        $selected = '';
        for ($i = 0; $i < count($arrayOfStrings); $i++) {
            $selected = $current === $arrayOfStrings[$i] ? ' selected' : ''; 
            $html .= '<option value="'.$arrayOfStrings[$i][0].'" '.$selected.'>'.$arrayOfStrings[$i][1].'</option>';
        }
        $html .= '</select>';
        return $html;
    } else {
        return;
    }
}


/**
 * @param $grid_active
 * @param $grid_content
 * @param $grid_custom_classes
 * @param $grid_cell_template
 * @param $grid_cell_image_height
 * @return string
 */
function get_grid_edit($pages_id, $grid_active, $grid_content, $grid_custom_classes, $grid_cell_template, $grid_cell_image_height)
{
    $pages = new Pages();
    $imageClass = new Image();
    $grid_content_json = json_decode($grid_content);
    
    // json to a multidimensional array
    $result = array();
    $index = -1;
    if (count($grid_content_json)) {
       foreach($grid_content_json as $key=>$val){
            foreach($val as $k=>$v){
                // first item value 'grid-image'
                if ($v == 'grid-image') {
                    $index++;
                }
                $result[$index][] = $v;
            }
        }
    }

    $result_copy = $result;
    $counter = 0;

    // 0: grid-image, 2: grid-image-y, 4: heading, 6: video, 8: url, 10: link, 12: grid-content, 
    // 14: css, 16: pages_id, 18: grid-dynamic-content, 20: grid-dynamic-content-filter, 22: grid-dynamic-content-limit
    //$html_grid = '<div id="wrapper-grid" class="'.$grid_custom_classes.' clearfix">';
    $html_grid = "";
    foreach($result as $key => $value) {
        $header = "";
        $image = "";
        

        foreach ($value as $key2 => $value2) {
            switch ($key2) {
                case "0":
                $html_grid .= '<div class="grid-cell '.$result_copy[$counter][15].' gridedit">';
                $html_grid .= '<div class="grid-tools"><i class="far fa-save"></i><br><i class="far fa-edit" aria-hidden="true"></i><br><i class="fas fa-arrow-left" aria-hidden="true"></i><br><i class="fas fa-arrow-right" aria-hidden="true"></i><br><i class="far fa-trash-alt"></i></div>';
                break;
                case "1":
                    if(strlen($value2)) {
                        
                        $background_image_y = strlen($result_copy[$counter][3]) > 1 ? 'background-position-y:'. $result_copy[$counter][3] .'%': '';
                        $image = strlen($value2) ? '<div class="grid-image-crop" style="height:'.$grid_cell_image_height.'px;background-image: url('.$value2.');'.$background_image_y.'"></div>' : "";
                    }
                    
                break;
                case "5":
                    if(strlen($value2)) {
                        $a_href = strlen($result_copy[$counter][9]) ? $result_copy[$counter][9] : "";
                        $a_start = ""; 
                        $a_end = ""; 
                        $header .= $a_start . '<h3 class="grid-heading">' . $value2 . '</h3>' . $a_end ;
                    }
                    $html_grid .= $grid_cell_template == 0 ? $image . $header : $header . $image; 
                break;
                case "7":
                        if (strlen($value2)) {
                            $video = getVideoEmbed($value2);
                            $html_grid .= '<div class="grid-video">' .$video . '</div>';
                        }
                    break;
                case "":
                break;

                case "13":
                    if(strlen($value2)) {
                        $html_grid .= '<div class="grid-content">' . $value2 . '</div>';
                    }
                    break;
                case "":
                break;
                case "19":

                    //$html_grid .= '<div class="grid-dynamic hidden">';
                    $html_grid .= '<div class="grid-dynamic">';
                    if ($value2 == "stories-child") {
                        
                        $p_id = (int)$result_copy[$counter][17];
                        $rows_children = $pages->getPagesChildren($p_id);

                        if ($rows_children) {
                            $html_grid .= '<ul>';
                            foreach ($rows_children as $row_child) {
                                $html_grid .= '<li><a href="pages.php?pages_id='.$row_child['pages_id'].'">'.$row_child['title'].'</a></li>';
                            }
                            $html_grid .= '</ul>';								
                        }
                    }
                    if ($value2 == "stories-promoted") {
                        
                        $stories_filter = (string)$result_copy[$counter][21];
                        $limit = (int)$result_copy[$counter][23];
                        $rows_promoted = $pages->getPagesStoryContentPublishPromoted($stories_filter, $limit);

                        if ($rows_promoted) {
                            
                            foreach ($rows_promoted as $row_promoted) {
                                $html_grid .= '<a href="pages.php?pages_id='.$row_promoted['pages_id'].'"><div class="story">';
                                $optimzed_image = isset($row_promoted['filename']) ? $imageClass->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row_promoted['pages_id'] . '/' .  $row_promoted['filename'], 400) : '';
                                $html_grid .= '<h4>'.$row_promoted['title'] . '</h4>';
                                $html_grid .= isset($row_promoted['filename']) ? '<img src="' . $optimzed_image . '" style="float:right;width:33%;margin-left:10px">' : '';
                                $html_grid .= $row_promoted['story_content'];
                                $html_grid .= '</div></a>';
                            }
                        
                        }
                    }
                    $html_grid .= '</div>';
                break;
                case "21":
                    $link = strlen($result_copy[$counter][11]) ? $result_copy[$counter][11] : $result_copy[$counter][9];
                    $html_grid .= '<div class="grid-split"></div>';
                    if(strlen($link)) {
                    $html_grid .= '<div class="grid-link"><a href="'.$result_copy[$counter][9].'">'.$link.'</a></div>';
                    }
                    $grid_image_y = (int)$result_copy[$counter][3];
                    $adjustSelectList = get_select_number(array(0,10,20,30,40,50,60,70,80,90,100), $grid_image_y, "grid-image-y", "", "");
         
                    $dynamic = '<hr><div class="dynamic hidden"><p>Dynamic content</p>';
                    $dynamic .= get_select_strings(array(array("none", "none"),array("stories-child", "Child stories"),array("stories-event", "Event stories"),array("stories-promoted", "Promoted stories")), "", "grid-dynamic-content", "", "");
                    $dynamic .= '<p>Filter promoted stories (tag):</p><input type="text" name="grid-dynamic-content-filter" maxlength="25">'; 
                    $dynamic .= '<p>Limit promoted stories</p>';
                    $dynamic .= get_select_number(array(0,1,2,3,4,5,6,7,8,9), 1, "grid-dynamic-content-limit", "", "");
                    
                    $class_select = '<a class="colorbox_grid_class"  href="pages_css.php?token='. $_SESSION['token'].'&pages_id='.$pages_id.'&return=false">&nbsp;<i class="fas fa-question-circle"></i></a>';
                    $html_grid .= '<div class="grid-form hidden"><p>Image<br><input type="text" name="grid-image" maxlength="255" value="'.$result_copy[$counter][1].'"></p><p>Adjust image (background-position-y %): '.$adjustSelectList.'</p><p>Heading<br><input type="text" name="heading" maxlength="100" value="'.$result_copy[$counter][5].'"></p><p>Video<br><input type="text" name="video" maxlength="100" value="'.$result_copy[$counter][7].'"></p><p>URL<br><input type="text" name="url" maxlength="255" value="'.$result_copy[$counter][9].'"></p><p>Link title<br><input type="text" name="link" maxlength="50" value="'.$result_copy[$counter][11].'"></p><p>Content<br><textarea class="tinymce-grid" name="grid-content">'.$result_copy[$counter][13].'</textarea></p><p>Custom css class<br><input type="text" name="css" maxlength="100" value="'.$result_copy[$counter][15].'">'.$class_select.'<input type="hidden" name="pages_id" value="'.$result_copy[$counter][15].'"></p><p>Toggle <a class="toggle" href="#dynamic">dynamic content</a></p>'.$dynamic.'</div>';

                    $html_grid .= '</div></div>';
                break;


                
            }
            
        }

            
        $counter++;
    }

    return $html_grid;
}


function getVideoEmbed($video) {

    $int = strrpos($video, "/");
    $video_id = substr($video,  $int - strlen($video) + 1);
    $iframe = "";

    
    if (strpos($video, "vimeo")) {
        $iframe = '<iframe src="https://player.vimeo.com/video/'.$video_id.'?title=0&byline=0&autoplay=0" data-ratio="0.5625" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }
    if (strpos($video, "youtu")) {
        $iframe = '<iframe src="https://www.youtube.com/embed/'.$video_id.'" data-ratio="0.5625" frameborder="0" allow="encrypted-media" allowfullscreen></iframe>';
    }

    
    return $iframe;
}


/**
 * @param $grid_active
 * @param $grid_content
 * @param $grid_custom_classes
 * @param $grid_cell_template
 * @param $grid_cell_image_height
 * @return string
 */
function get_grid($pages_id, $grid_active, $grid_content, $grid_custom_classes, $grid_cell_template, $grid_cell_image_height)
{
    $pages = new Pages();
    $imageClass = new Image();
    $grid_content_json = json_decode($grid_content);
    
    // json to a multidimensional array
    $result = array();
    $index = -1;

    if (!is_array($grid_content_json)) {return;}
    foreach($grid_content_json as $key=>$val){
        foreach($val as $k=>$v){
            // first item value 'grid-image'
            if ($v == 'grid-image') {
                $index++;
            }
            $result[$index][] = $v;
        }
    }

    $result_copy = $result;
    $counter = 0;
    // 0: grid-image, 2: grid-image-y, 4: heading, 6: video, 8: url, 10: link, 12: grid-content, 
    // 14: css, 16: pages_id, 18: grid-dynamic-content, 20: grid-dynamic-content-filter, 22: grid-dynamic-content-limit
    $add_wrapper_class = strlen($grid_custom_classes) ? "wrapper-grid-padding" : "";

    $html_grid = '<div id="wrapper-grid" class="'.$grid_custom_classes.' '.$add_wrapper_class.' clearfix">';
    foreach($result as $key => $value) {
        $header = "";
        $image = "";

        foreach ($value as $key2 => $value2) {
            switch ($key2) {
                case "0":
                $html_grid .= '<div class="grid-cell  '.$result_copy[$counter][15].'">';
                break;
                case "1":
                    if(strlen($value2)) {    
                        $background_image_y = strlen($result_copy[$counter][3]) > 1 ? 'background-position-y:'. $result_copy[$counter][3] .'%': '';
                        $image = '<div class="grid-image-crop" style="height:'.$grid_cell_image_height.'px;background-image: url('.$value2.');'.$background_image_y.'"></div>';
                    }
                    
                break;
                case "5":
                    if(strlen($value2)) {
                        $a_href = strlen($result_copy[$counter][9]) ? $result_copy[$counter][9] : "";
                        $a_start = strlen($a_href) ? '<a href="'.$a_href.'">' : ""; 
                        $a_end = strlen($a_href) ? '</a>' : ""; 
                        $header .= $a_start . '<h3 class="grid-heading">' . $value2 . '</h3>' . $a_end ;
                    }
                    $html_grid .= $grid_cell_template == 0 ? $image . $header : $header . $image; 
                break;
                case "7":
                    if(strlen($value2)) {
                        $video = getVideoEmbed($value2);
                        $html_grid .= '<div class="grid-video">' .$video . '</div>';
                    }
                    break;

                case "11":
                    if(strlen($value2)) {
                        //$html_grid .= '<div class="grid-content">' . $value2 . '</div>';
                    }
                    break;
                case "13":
                    if(strlen($value2)) {
                        $html_grid .= '<div class="grid-content">' . $value2 . '</div>';
                    }
                    break;
                case "17":
                    break;
                case "19":
                    if ($value2 == "stories-child") {
                        
                        $p_id = (int)$result_copy[$counter][17];
                        $rows_children = $pages->getPagesChildren($p_id);

                        if ($rows_children) {
                            $html_grid .= '<ul>';
                            foreach ($rows_children as $row_child) {
                                $html_grid .= '<li><a href="pages.php?pages_id='.$row_child['pages_id'].'">'.$row_child['title'].'</a></li>';
                            }
                            $html_grid .= '</ul>';								
                        }
                    }
                    if ($value2 == "stories-promoted") {
                        $stories_filter = (string)$result_copy[$counter][21];
                        $limit = (int)$result_copy[$counter][23];
                        $rows_promoted = $pages->getPagesStoryContentPublishPromoted($stories_filter, $limit);

                        if ($rows_promoted) {
                            foreach ($rows_promoted as $row_promoted) {
                                $html_grid .= '<a href="pages.php?id='.$row_promoted['pages_id'].'"><div class="story">';
                                $optimzed_image = isset($row_promoted['filename']) ? $imageClass->get_optimzed_image(CMS_DIR . '/content/uploads/pages/' . $row_promoted['pages_id'] . '/' .  $row_promoted['filename'], 400) : '';
                                $html_grid .= '<h4>'.$row_promoted['title'] . '</h4>';
                                $html_grid .= isset($row_promoted['filename']) ? '<img src="' . $optimzed_image . '" style="float:right;width:33%;margin-left:10px">' : '';
                                $html_grid .= $row_promoted['story_content'];
                                $html_grid .= '</div></a>';
                            }                            
                        }
                    }
                break;
                case "21":
                    $link = strlen($result_copy[$counter][11]) ? $result_copy[$counter][11] : $result_copy[$counter][9];
                    if (strlen($result_copy[$counter][9])) {
                        $html_grid .= '<div class="grid-split"></div>';
                        $html_grid .= '<div class="grid-link"><a href="'.$result_copy[$counter][9].'">'.$link.'</a></div>';
                    }
                    $html_grid .= '</div>';
                break;
            }
        }
        $counter++;
    }
    $html_grid .= '</div>';
    return $html_grid;
}



/**
 * @param $arr
 * @param $area
 * @return html
 */
function print_grid($arr, $area)
{
    if (!is_array($arr)) {
        return null;
    }
    if ($arr['grid_active'] == 0) {
        return null;
    }

    if ($arr['grid_area'] == $area) {
        $html_grid = get_grid($arr['pages_id'], $arr['grid_active'], $arr['grid_content'], $arr['grid_custom_classes'], $arr['grid_cell_template'], $arr['grid_cell_image_height']);    
        echo $html_grid;
    }
}


function print_mobile_menu($pages, $id, $seo, $href) 
{
    if ($_SESSION['layoutType'] == "mobile") {
        echo '<div id="site-navigation-mobile-wrapper">';
            echo '<div class="mobile-buttons"></div><div class="menu-button"></div>';               
                echo '<nav id="site-navigation-mobile">';
                $seo = $_SESSION['site_seo_url'] == 1 ? 1 : 0;
                $open = true;
                $parent_id = 0;
                get_pages_tree_sitemap($parent_id, $id, $path=get_breadcrumb_path_array($id), $a=true, $a_add_class=false, $seo, $href, $open, $depth=0, $show_pages_id = false);									
        echo '</div>';
    }
}


function print_noscript($languages)
{
    $html = '<noscript>';
    $html .= '<div class="noscript_message">';
    $html .= translate("JavaScript is not enabled, current settings in your browser prevents functionality", "noscript", $languages);
    $html .= '</div>';
    $html .= '</noscript>';
    echo $html;
}


/**
 * @param $text
 * @return mixed
 */
function transl($text)
{
    $a = array(
        "english" => array("Mon" => "Mon", "Tue" => "Tue", "Wed" => "Wed", "Thu" => "Thu", "Fri" => "Fri", "Sat" => "Sat", "Sun" => "Sun",
            "Monday" => "Monday", "Tuesday" => "Tuesday", "Wednesday" => "Wednesday", "Thursday" => "Thursday", "Friday" => "Friday", "Saturday" => "Saturday", "Sunday" => "Sunday",
            "one day" => "one day", "four days" => "four days", "one week" => "one week", "two weeks" => "two weeks", "one month" => "one month",
            "January" => "January", "February" => "February", "March" => "March", "April" => "April", "May" => "May", "June" => "June", "July" => "July", "August" => "August", "September" => "September", "October" => "October", "November" => "November", "December" => "December",
            "Jan" => "Jan", "Feb" => "Feb", "Mar" => "Mar", "Apr" => "Apr", "May" => "May", "Jun" => "Jun", "Jul" => "Jul", "Aug" => "Aug", "Sep" => "Sep", "Oct" => "Oct", "Nov" => "Nov", "Dec" => "Dec",
            "w" => "w"),
        "swedish" => array("Mon" => "må", "Tue" => "ti", "Wed" => "on", "Thu" => "to", "Fri" => "fr", "Sat" => "lö", "Sun" => "sö",
            "Monday" => "Måndag", "Tuesday" => "Tisdag", "Wednesday" => "Onsdag", "Thursday" => "Torsdag", "Friday" => "Fredag", "Saturday" => "Lördag", "Sunday" => "Söndag",
            "one day" => "en dag", "four days" => "fyra dagar", "one week" => "en vecka", "two weeks" => "två veckor", "one month" => "en månad",
            "January" => "Januari", "February" => "Februari", "March" => "Mars", "April" => "April", "May" => "Maj", "June" => "Juni", "July" => "Juli", "August" => "Augusti", "September" => "September", "October" => "Oktober", "November" => "November", "December" => "December",
            "Jan" => "Jan", "Feb" => "Feb", "Mar" => "Mar", "Apr" => "Apr", "May" => "Maj", "Jun" => "Jun", "Jul" => "Jul", "Aug" => "Aug", "Sep" => "Sep", "Oct" => "Okt", "Nov" => "Nov", "Dec" => "Dec",
            "w" => "v"));

    $l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
    if (!$l) {
        $l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
    }
    $s = $l ? $a[$l][$text] : $text;
    return $s;
}


/**
 * recursive function parse_stories()
 *
 * @param $pages object $pages class Pages()
 * @param $dtz
 * @param $html
 * @param $start
 * @return array
 */
function parse_storiesaround_coded($pages, $dtz, $html, $start)
{
    $new = array();

    // find parse start - coded [[
    // find parse end - coded ]]
    // 2 chars used to mark start - recursive function adds 2 to find mulitple stories to parse
    $l = strlen($html);
    if ($l >= 4) {

        // positions in html string
        $p0 = strpos($html, "[[", 0);
        $p1 = strpos($html, "[[", $start);
        $p2 = strpos($html, "]]", $start + 2);

        // $p1 must occur berfore $p2
        if ($p1 && $p2 && $p1 < $p2) {

            // find code to parse between [[ ? ]]
            $parse = substr($html, $p1 + 2, $p2 - 2 - $p1);

            // pre parse htlm
            $pre = substr($html, $start, -$l + $p1);

            // post parse htlm
            $post = substr($html, $p2 + 2, $l - $p2);

            // remove leading 2 characters ]] if not first
            $pre = ($p0 == $p1) ? $pre : substr($pre, 2);
            $new[] = $pre;

            // call function parse_code
            $new[] = parse_code($pages, $dtz, $parse);

            // check for multiple parse strings
            if (strpos($html, "[[", $p2) > 0) {

                // recursive call
                $new = array_merge($new, parse_storiesaround_coded($pages, $dtz, $html, $start = $p2));
            } else {
                $new[] = $post;
            }

        } else {

            // just plain html
            $new[] = $html;
        }
    } else {
        $new[] = $html;
    }
    return $new;
}

/**
 * @param $pages
 * @param $dtz
 * @param $code
 * @return string
 */
function parse_code($pages, $dtz, $code)
{
    $str = "";

    // remove leading | ending whitespaces
    $code = trim($code);

    // remove multiple whitespaces if coded by mistake
    $code = preg_replace('/\s+/', ' ', $code);

    // split code in array
    $a = explode(" ", $code);

    // variable to send parser
    $coded = null;

    // variable do show code to parse (debug)
    $s = null;

    // switch
    /*
    [[story selected "5,7,12"]]
    [[story promoted filter "home" limit "7"]]
    [[story child parent "3"]]
    [[story event filter "music" date "next"]]
    */

    switch ($a[0]) {
        case 'story':
            $coded = "story";
            $s .= 'story ';
            break;
    }

    if ($coded == "story") {
        switch ($a[1]) {
            case 'selected':

                // build debug
                $s .= 'selected ' . $a[2];

                // remove first and last characters ""
                $ids = substr($a[2], 1, -1);
                $rows = $pages->getPagesStoryContentPublishSelected($ids);

                // story content
                $str .= get_box_story_content_bycode($rows, $col_width = 474, "", "", $dtz);
                break;
            case 'promoted':

                // build debug
                $s .= 'promoted ' . $a[3] . ' limit: ' . $a[5];

                // remove first and last characters ""
                $search = substr($a[3], 1, -1);
                $limit = substr($a[5], 1, -1);
                $rows = $pages->getPagesStoryContentPublishPromoted($search, $limit);

                // story content
                $str .= get_box_story_content_bycode($rows, $col_width = 474, "", "", $dtz);
                break;
            case 'child':

                // build debug
                $s .= 'child ' . $a[2] . ' ' . $a[3];

                // remove first and last characters ""
                $id = substr($a[3], 1, -1);
                $rows = $pages->getPagesStoryContentPublishChild($id);
                // story content
                $str .= get_box_story_content_bycode($rows, $col_width = 474, "", "", $dtz);
                break;
            case 'event':

                // build debug
                $s .= 'event ' . $a[3] . ' date: ' . $a[5];

                // remove first and last characters ""
                $search = substr($a[3], 1, -1);
                $period = substr($a[5], 1, -1);
                $rows = $pages->getPagesStoryContentPublishEvent($search, $date = date("Y-m-d"), $period);

                // story content
                $str .= get_box_story_content_bycode($rows, $col_width = 474, "", "", $dtz);
                break;
            default:

                // do nothing...
                break;
        }
    }
    return $str;
}

?>