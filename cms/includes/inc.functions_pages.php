<?php
require_once 'inc.core.php';


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
function set_plugin_values($id)
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
function set_selection_values($pages_selections, $css_files, $js_files, $pages, $dtz, $selection_area)
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
                    array_push($css_files, trim(CMS_DIR.$external_css));
                }
            }
            $content_html = $s_row['content_html'];
            $content_code = implode(parse_storiesaround_coded($pages, $dtz, $s_row['content_code'],0));
            
            switch($s_row['area']) {
            
                case 'header_above':
                    $selection_area['header_above'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;

                case 'header':
                    $selection_area['header'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;

                case 'header_below':
                    $selection_area['header_below'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;

                case 'left_sidebar_top':
                    $selection_area['left_sidebar_top'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;
                            
                case 'left_sidebar_bottom':
                    $selection_area['left_sidebar_bottom'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;

                case 'right_sidebar_top':
                    $selection_area['right_sidebar_top'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;

                case 'right_sidebar_bottom':
                    $selection_area['right_sidebar_bottom'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;

                case 'content_above':
                    $selection_area['content_above'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;
                
                case 'content_inside':
                    $selection_area['content_inside'] .= '<div class="site-selection" style="clear:none;">'.$content_html . $content_code.'</div>';
                break;

                case 'content_below':
                    $selection_area['content_below'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;

                case 'footer_above':
                    $selection_area['footer_above'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;
        
                case 'outer_sidebar':
                    $selection_area['outer_sidebar'] .= '<div class="site-selection">'.$content_html . $content_code.'</div>';
                break;
            }
        }    
    
    }
    return array($css_files, $js_files, $selection_area);
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
            $crumb = ($clickable == 1) ? '<a href="http://' . $_SESSION['site_domain'] . '/pages/' . $row['pages_id_link'] . '">' . $r . '</a>' : $r;
        } else {
            $crumb = ($clickable == 1) ? '<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $row['pages_id'] . '">' . $r . '</a>' : $r;
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
                    echo '<a href="http://' . $_SESSION['site_domain'] . '/pages/' . $row['pages_id_link'] . '" class="' . $class . '">';
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
                echo '<li><a href="http://' . $_SESSION['site_domain'] . '/pages/' . $row['pages_id_link'] . '">';
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
                    echo '<a href="http://' . $_SESSION['site_domain'] . '/pages/' . $row['pages_id_link'] . '" class="' . $a_class . '">';
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
function get_stories_box($id, $title)
{
    echo '<div id="' . $id . '" class="portlet">';
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
function get_box_story_content_selected($rows, $str, $col_width, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_last_modified, $dtz)
{
    if (isset($rows)) {
        foreach ($rows as $row) {
            if (!isset($_SESSION['users_id'])) {
                if ($row['access'] < 2) {
                    continue;
                }
            }

            if ($row['container'] == $str) {
                $title = $row['story_custom_title'];
                $title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];
                $pages_id = $row['pages_id'];
                $css_class = (strlen($row['story_css_class']) > 0) ? $row['story_css_class'] : '';
                $story = $row['story_content'];
                $story_wide = $row['story_wide_content'];
                $story_wide_teaser_image = $row['story_wide_teaser_image'];
                $utc = $row['utc_modified'];
                $date = get_utc_dtz($utc, $dtz, 'Y-m-d H:i');
                $date = $stories_last_modified == 1 ? get_utc_dtz($row['utc_modified'], $dtz, 'Y-m-d H:i') : '';
                $caption = isset($row['filename']) ? $row['caption'] : '';

                echo '<div class="stories-wrapper">';
                if ($row['story_link']) {
                    echo '<a class="stories" href="pages.php?id=' . $row['pages_id'] . '">';
                }
                $w = $col_width;

                switch ($w) {
                    case 138:
                        $i = 222;
                        $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';
                        echo '<div class="stories-content ' . $css_class . '">';
                        if (isset($row['filename'])) {
                            echo '<img src="' . $img . '" class="fluid" alt="' . $caption . '" />';
                        }
                        echo '<div class=" stories-content' . $css_class . '" style="border:0;">';
                        if ($title == 0) {
                            echo '<h4 class="stories-title" >' . $title_value . '</h4>';
                        }
                        if ($stories_last_modified == 1) {
                            echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                        }
                        echo $story;
                        echo '</div></div>';
                        break;
                    case 222:
                        $i = 222;
                        $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';
                        echo '<div class="stories-content ' . $css_class . '">';
                        if (isset($row['filename'])) {
                            echo '<img src="' . $img . '" class="fluid" alt="' . $caption . '" />';
                        }
                        echo '<div class="stories-content ' . $css_class . '" style="border:0;">';
                        if ($title == 0) {
                            echo '<h3 class="stories-title">' . $title_value . '</h3>';
                        }
                        if ($stories_last_modified == 1) {
                            echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                        }
                        echo $story;
                        echo '</div></div>';
                        break;
                    case 306:
                        $i = 474;
                        $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';

                        echo '<div class="stories-content ' . $css_class . '">';
                        if (isset($row['filename'])) {
                            echo '<img src="' . $img . '" class="fluid" alt="' . $caption . '" />';
                        }
                        echo '<div class="stories-content ' . $css_class . '" style="border:0;">';
                        if ($title == 0) {
                            echo '<h3 class="stories-title">' . $title_value . '</h3>';
                        }
                        if ($stories_last_modified == 1) {
                            echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                        }
                        echo $story;
                        echo '</div></div>';
                        break;
                    case 474:

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
                                $i = 474;
                                $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';
                                if (isset($row['filename'])) {
                                    echo '<img src="' . $img . '" class="fluid" alt="' . $caption . '" />';
                                }
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
                                $teaser_image_class = $stories_wide_teaser_image_align == 0 ? 'float-left' : 'float-right';
                                echo '<div class="stories-content">';
                                if (isset($row['filename'])) {
                                    echo '<img src="' . $img . '" style="width:' . $stories_wide_teaser_image_width . 'px;height:auto;" class="' . $teaser_image_class . '" alt="' . $caption . '" />';
                                }
                                echo $story_wide;
                                echo '</div>';
                                echo '<div style="clear:both"></div>';
                                echo '</div>';
                                break;
                        }
                        break;

                    case 726:
                    case 978:

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
                                if (isset($row['filename'])) {
                                    echo '<img src="' . $img . '" class="fluid" alt="' . $caption . '" />';
                                }
                                if ($title == 0) {
                                    echo '<h3 class="stories-title">' . $title_value . '</h3>';
                                }
                                if ($stories_last_modified == 1) {
                                    echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                                }
                                echo $story_wide;
                                echo '</div>';
                                break;
                            case 2:
                                $i = 222;
                                $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';
                                echo '<div class="stories-content ' . $css_class . '">';
                                if (isset($row['filename'])) {
                                    $teaser_image_class = $stories_wide_teaser_image_align == 0 ? 'float-left' : 'float-right';
                                    echo '<div style="width:' . $stories_wide_teaser_image_width . 'px;"><img src="' . $img . '" class="fluid "' . $teaser_image_class . '" alt="' . $caption . '" /></div>';
                                }
                                if ($title == 0) {
                                    echo '<h3 class="stories-title">' . $title_value . '</h3>';
                                }
                                if ($stories_last_modified == 1) {
                                    echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                                }
                                echo $story_wide;
                                echo '<div style="clear:both"></div>';
                                echo '</div>';

                                break;
                        }
                        break;
                }
                if ($row['story_link']) {
                    echo '</a>';
                }
                echo '</div>';
            }
        }
    }
}

/**
 * @param $rows_event_stories
 * @param $content_width
 * @param $stories_wide_teaser_image_align
 * @param $stories_wide_teaser_image_width
 * @param $stories_last_modified
 * @param $dtz
 */
function get_box_story_events($rows_event_stories, $content_width, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_last_modified, $dtz)
{
    foreach ($rows_event_stories as $row) {
       $title = $row['story_custom_title'];
        $title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];
        $pages_id = $row['pages_id'];
        $ratio = $row['ratio'];
        $css_class = (strlen($row['story_css_class']) > 0) ? $row['story_css_class'] : '';
        $story = $row['story_content'];
        $story_wide = $row['story_wide_content'];
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

        echo "\n" . '<div class="story-event' . $class . '" id="' . $utc . '">';

        echo $a_start . '<h3 class="stories-event-date">' . $date . '</h3>' . $a_end;
        switch ($story_wide_teaser_image) {
            case 0:
                echo '<div class="stories-event-content ' . $css_class . '">';
                if ($title == 0) {
                    echo '<h4 class="stories-event-title">' . $title_value . '</h4>';
                }
                if ($stories_last_modified == 1) {
                    echo '<div class="stories-event-meta"><span class="stories-event-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                }
                echo $story_wide;
                echo '</div>';
                break;
            case 1:

                $i = 726;
                $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';
                if (isset($row['filename'])) {
                    echo '<img src="' . $img . '" class="fluid" alt="' . $caption . '" />';
                }
                echo '<div class="stories-event-content ' . $css_class . '">';
                if ($title == 0) {
                    echo '<h4 class="stories-event-title">' . $title_value . '</h4>';
                }
                if ($stories_last_modified == 1) {
                    echo '<div class="stories-event-meta"><span class="stories-event-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                }
                echo $story_wide;
                echo '</div>';
                break;
            case 2:
                $i = 222;
                $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';
                echo '<div class="stories-event-content ' . $css_class . '">';
                if (isset($row['filename'])) {
                    $teaser_image_class = $stories_wide_teaser_image_align == 0 ? 'float-left' : 'float-right';
                    echo '<div style="width:' . $stories_wide_teaser_image_width . 'px;"><img src="' . $img . '" class="fluid ' . $teaser_image_class . '" alt="' . $caption . '" /></div>';
                }
                if ($title == 0) {
                    echo '<h3 class="stories-title">' . $title_value . '</h3>';
                }
                if ($stories_last_modified == 1) {
                    echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">Published: ' . $date . '</abbr></span></div>';
                }
                echo $story_wide;
                echo '<div style="clear:both"></div>';

                break;
        }

        echo '</div>' . "\n";
        echo $a;
    }
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
function get_box_story_content_child($rows, $col_width, $css_class_uniformed, $stories_wide_teaser_image_align, $stories_wide_teaser_image_width, $stories_last_modified, $dtz)
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
            $story = $row['story_content'];
            $story_wide = $row['story_wide_content'];
            $story_wide_teaser_image = $row['story_wide_teaser_image'];
            $caption = $row['caption'];
            $css_class = (strlen($row['story_css_class']) > 0) ? $row['story_css_class'] : '';
            $css_class = (strlen($css_class_uniformed) > 0) ? $css_class_uniformed : $css_class;

            echo '<div class="stories-wrapper" style="width:100%;">';
            if ($row['story_link']) {
                echo '<a class="stories" href="pages.php?id=' . $row['pages_id'] . '">';
            }

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
            $story_wide = $row['story_wide_content'];
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
        = $arr['content_author'] = $arr['stories_event_dates'] = $arr['stories_columns'] = $arr['stories_child'] = "";

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


/**
 * @param $rows
 * @param $col_width
 * @param $stories_last_modified
 * @param $show_image_copyright
 * @param $dtz
 * @param $languages
 */
function get_box_story_content_promoted($rows, $col_width, $stories_last_modified, $show_image_copyright, $dtz, $languages)
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
            $caption = $row['caption'];
            $i = $col_width > 222 ? 726 : 222;
            $img = isset($row['filename']) ? CMS_DIR . '/content/uploads/pages/' . $row['pages_id'] . '/' . str_replace('_100.', '_' . $i . '.', $row['filename']) : '';

            echo '<div class="stories-wrapper" style="width:100%;">';
            if ($row['story_link']) {
                echo '<a class="stories" href="pages.php?id=' . $row['pages_id'] . '">';
            }
            echo '<div class="stories-content ' . $css_class . '">';
            if (!is_null($row['filename'])) {
                echo '<img src="' . $img . '"  class="fluid" alt="' . $caption . '" />';
            }
            echo '<div class="stories-content ' . $css_class . '" style="border:0;">';
            if (strlen($row['copyright']) && $show_image_copyright == 1) {
                echo '<div class="stories-image-meta">' . translate("Photo:", "site_photo", $languages) . ' ' . $row['copyright'] . '</div>';
            }
            if ($title == 0) {
                echo '<h3 class="stories-title">' . $title_value . '</h3>';
            }
            if ($stories_last_modified == 1) {
                echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="' . $date . '">' . $date . '</abbr></span></div>';
            }
            echo $row['story_content'];
            echo '</div></div>';
            if ($row['story_link']) {
                echo '</a>';
            }
            echo '</div>';

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
            get_stories_box($row['pages_id'], $row['title']);
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
            get_stories_box($row['pages_id'], $row['title']);
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
            get_stories_box($row['pages_id'], $row['title']);
        }
    }
}

/**
 * @param $rows
 * @param $str
 */
function get_box_content($rows, $str)
{
    if (isset($rows)) {
        foreach ($rows as $row) {
            if ($row['container'] == $str) {
                get_stories_box($row['pages_stories_id'], $row['title']);
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
                get_stories_box($row['pages_stories_id'], $row['title']);
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
    $grid_content_json = json_decode($grid_content);
    
    // json to a multidimensional array
    $result = array();
    $index = -1;
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

    //print_r2($result);

    // 0: grid-image, 2: grid-image-y, 4: heading, 6: url, 8: link, 10: grid-content, 
    // 12: css, 42: pages_id, 16: grid-dynamic-content, 18: grid-dynamic-content-filter, 20: grid-dynamic-content-limit
    //$html_grid = '<div id="wrapper-grid" class="'.$grid_custom_classes.' clearfix">';
    $html_grid = "";
    foreach($result as $key => $value) {
        $header = "";
        $image = "";
        
        foreach ($value as $key2 => $value2) {
            switch ($key2) {
                case "0":
                $html_grid .= '<div class="grid-cell '.$result_copy[$counter][13].'">';
                $html_grid .= '<div class="grid-tools"><i class="fa fa-floppy-o" aria-hidden="true"></i><br><i class="fa fa-pencil-square-o" aria-hidden="true"></i><br><i class="fa fa-arrow-left" aria-hidden="true"></i><br><i class="fa fa-arrow-right" aria-hidden="true"></i><br><i class="fa fa-trash-o fa-2x"></i></div>';
                break;
                case "1":
                    //if(strlen($value2)) {
                        
                        $background_image_y = strlen($result_copy[$counter][3]) > 1 ? 'background-position-y:'. $result_copy[$counter][3] .'%': '';
                        $image = '<div class="grid-image-crop" style="height:'.$grid_cell_image_height.'px;background-image: url('.$value2.');'.$background_image_y.'"></div>';
                    //}
                    
                break;
                case "5":
                    //if(strlen($value2)) {
                        $a_href = strlen($result_copy[$counter][7]) ? $result_copy[$counter][7] : "";
                        $a_start = ""; 
                        $a_end = ""; 
                        $header .= $a_start . '<h2 class="grid-heading">' . $value2 . '</h2>' . $a_end ;
                    //}
                    $html_grid .= $grid_cell_template == 0 ? $image . $header : $header . $image; 
                break;

                case "11":
                    //if(strlen($value2)) {
                        $html_grid .= '<div class="grid-content">' . $value2 . '</div>';
                    //}
                    break;
                case "":
                break;
                case "17":

                    $html_grid .= '<div class="grid-dynamic hidden">';
                    if ($value2 == "stories-child") {
                        
                        $p_id = (int)$result_copy[$counter][15];
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
                        
                        $stories_filter = (string)$result_copy[$counter][19];
                        $limit = (int)$result_copy[$counter][21];
                        $rows_promoted = $pages->getPagesStoryContentPublishPromoted($stories_filter, $limit);

                        if ($rows_promoted) {
                            
                            foreach ($rows_promoted as $row_promoted) {
                                $html_grid .= '<a href="pages.php?pages_id='.$row_promoted['pages_id'].'"><div class="story">';
                                $html_grid .= '<h5>'.$row_promoted['title'] . '</h5>';
                                $html_grid .= '<p>'.$row_promoted['story_content'] . '</p>';
                                $html_grid .= '</div></a>';
                            }
                                            
                        }
                    }
                    $html_grid .= '</div>';
                break;
                case "21":
                    $link = strlen($result_copy[$counter][9]) ? $result_copy[$counter][9] : $result_copy[$counter][7];
                    //if (strlen($result_copy[$counter][7])) {
                        $html_grid .= '<div class="grid-split"></div>';
                        $html_grid .= '<div class="grid-link"><a href="'.$result_copy[$counter][7].'">'.$link.'</a></div>';
                    //}


                    $grid_image_y = (int)$result_copy[$counter][3];
                    $adjustSelectList = get_select_number(array(0,10,20,30,40,50,60,70,80,90,100), $grid_image_y, "grid-image-y", "", "");
         
         

/*                     var dynamic = "<hr><div class=\"dynamic hidden\"><p>Dynamic content</p>";			
                    var dynamicSelectList = getSelectStrings([["none", "none"], ["stories-child", "Child stories"], ["stories-event", "Event stories"], ["stories-promoted", "Promoted stories"]], "", "grid-dynamic-content", "", "");
                    dynamic += dynamicSelectList;
                    dynamic += "<p>Filter promoted stories (tag):</p><input type=\"text\" name=\"grid-dynamic-content-filter\" maxlength=\"25\">"; 
                    dynamic += "<p>Limit promoted stories</p>";
                    var limitSelectList = getSelectNumber([0,1,2,3,4,5,6,7,8,9], 1, "grid-dynamic-content-limit", "", "");
                    dynamic += limitSelectList;
 */        

         
                    $dynamic = '<hr><div class="dynamic hidden"><p>Dynamic content</p>';
                    $dynamic .= get_select_strings(array(array("none", "none"),array("stories-child", "Child stories"),array("stories-event", "Event stories"),array("stories-promoted", "Promoted stories")), "", "grid-dynamic-content", "", "");
                    $dynamic .= '<p>Filter promoted stories (tag):</p><input type="text" name="grid-dynamic-content-filter" maxlength="25">'; 
                    $dynamic .= '<p>Limit promoted stories</p>';
                    $dynamic .= get_select_number(array(0,1,2,3,4,5,6,7,8,9), 1, "grid-dynamic-content-limit", "", "");

                    $html_grid .= '<div class="grid-form"><p>Image<br><input type="text" name="grid-image" maxlength="255" value="'.$result_copy[$counter][1].'"></p><p>Adjust image (background-position-y %): '.$adjustSelectList.'</p><p>Heading<br><input type="text" name="heading" maxlength="100" value="'.$result_copy[$counter][5].'"></p><p>URL<br><input type="text" name="url" maxlength="255" value="'.$result_copy[$counter][7].'"></p><p>Link title<br><input type="text" name="link" maxlength="50" value="'.$result_copy[$counter][9].'"></p><p>Content<br><textarea class="tinymce-grid" name="grid-content">'.$result_copy[$counter][11].'</textarea></p><p>Custom css class<br><input type="text" name="css" maxlength="100" value="'.$result_copy[$counter][13].'"><input type="hidden" name="pages_id" value="+pages_id+"></p><p>Toggle <a class="toggle" href="#dynamic">dynamic content</a></p>'.$dynamic.'</div>';


                    $html_grid .= '</div></div>';
                break;


                
            }
            
        }

            
        $counter++;
    }

    return $html_grid;
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
    $grid_content_json = json_decode($grid_content);
    
    // json to a multidimensional array
    $result = array();
    $index = -1;
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

    // 0: grid-image, 2: grid-image-y, 4: heading, 6: url, 8: link, 10: grid-content, 
    // 12: css, 42: pages_id, 16: grid-dynamic-content, 18: grid-dynamic-content-filter, 20: grid-dynamic-content-limit
    $html_grid = '<div id="wrapper-grid" class="'.$grid_custom_classes.' clearfix">';
    foreach($result as $key => $value) {
        $header = "";
        $image = "";

        foreach ($value as $key2 => $value2) {
            switch ($key2) {
                case "0":
                $html_grid .= '<div class="grid-cell '.$result_copy[$counter][13].'">';
                break;
                case "1":
                    if(strlen($value2)) {
                        
                        $background_image_y = strlen($result_copy[$counter][3]) > 1 ? 'background-position-y:'. $result_copy[$counter][3] .'%': '';
                        $image = '<div class="grid-image-crop" style="height:'.$grid_cell_image_height.'px;background-image: url('.$value2.');'.$background_image_y.'"></div>';
                    }
                    
                break;
                case "5":
                    if(strlen($value2)) {
                        $a_href = strlen($result_copy[$counter][7]) ? $result_copy[$counter][7] : "";
                        $a_start = strlen($a_href) ? '<a href="'.$a_href.'">' : ""; 
                        $a_end = strlen($a_href) ? '</a>' : ""; 
                        $header .= $a_start . '<h2 class="grid-heading">' . $value2 . '</h2>' . $a_end ;
                    }
                    $html_grid .= $grid_cell_template == 0 ? $image . $header : $header . $image; 
                break;

                case "11":
                    if(strlen($value2)) {
                        $html_grid .= '<div class="grid-content">' . $value2 . '</div>';
                    }
                    break;
                case "":
                break;
                case "17":

                    if ($value2 == "stories-child") {
                        
                        $p_id = (int)$result_copy[$counter][15];
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
                        
                        $stories_filter = (string)$result_copy[$counter][19];
                        $limit = (int)$result_copy[$counter][21];
                        $rows_promoted = $pages->getPagesStoryContentPublishPromoted($stories_filter, $limit);

                        if ($rows_promoted) {
                            
                            foreach ($rows_promoted as $row_promoted) {
                                $html_grid .= '<a href="pages.php?pages_id='.$row_promoted['pages_id'].'"><div class="story">';
                                $html_grid .= '<h5>'.$row_promoted['title'] . '</h5>';
                                $html_grid .= '<p>'.$row_promoted['story_content'] . '</p>';
                                $html_grid .= '</div></a>';
                            }
                                            
                        }
                    }
                break;
                case "21":
                    $link = strlen($result_copy[$counter][9]) ? $result_copy[$counter][9] : $result_copy[$counter][7];
                    if (strlen($result_copy[$counter][7])) {
                        $html_grid .= '<div class="grid-split"></div>';
                        $html_grid .= '<div class="grid-link"><a href="'.$result_copy[$counter][7].'">'.$link.'</a></div>';
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
function show_grid($arr, $area)
{
    if (!is_array($arr)) {
        return null;
    }
    if ($arr['grid_active'] == 0) {
        return null;
    }

    if ($arr['grid_area'] == $area) {
        $html_grid = get_grid($arr['id'], $arr['grid_active'], $arr['grid_content'], $arr['grid_custom_classes'], $arr['grid_cell_template'], $arr['grid_cell_image_height']);    
        echo $html_grid;
    }
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