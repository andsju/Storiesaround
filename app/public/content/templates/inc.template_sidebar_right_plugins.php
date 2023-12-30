
    <div id="wrapper-page">
    
        <div id="wrapper-content" class="column column-having-right-sidebar" style="width:75%; overflow:auto">
            <main>
                <?php print_selection("selection-content-above", $selection_area['content_above']); ?>
                <div id="content-top-selections"></div>
                <div id="content-top-grid"><?php print_grid($arr, 1);?></div>
                <div id="content-breadcrumb"><?php print_breadcrumb($id, $arr['breadcrumb']); ?></div>
                <article>
                    <header>
                        <h1 id="content-title" class="editable"><?php echo $page_title_body .' '.$icon; ?></h1>
                        <div id="content-meta"><?php  print_meta($arr['utc_modified'], $dtz, $languages) ?></div>
                    </header>
                    <?php if ($arr['search_field_area'] == 3) { print_search_field_area_page($languages); } ?>
                    <?php if ($arr['search_field_area'] > 0) { print_search_field_result($languages); } ?>
                    <?php print_selection("selection-content-inside", $selection_area['content_inside']); ?>
                    <div id="content-html" class="editable"> 
                        <?php echo $arr['content'];?>
                    </div>

                    <?php
                    if($arr['plugins']) {

                        // main class
                        $pages_plugins = new PagesPlugins();					
                    
                        // get this class name
                        $plugin = $pages_plugins->getPagesPlugins($id);
                    
                        if($plugin) {
                            if($plugin['plugins_active'] == 1) {
                                if (file_exists($plugin_settings["content"])) {
                                    $plugin_arguments = $arr['plugin_arguments'];
                                    include $plugin_settings["content"];
                                }                            
                            }
                        }
                    }  
                    ?>

                    <footer>
                        <div id="content-author-leading"><?php print_author($arr['content_author'], $languages)?></div>
                        <div id="content-author" class="editable_row"><?php echo $arr['content_author']; ?></div>
                        <div id="content-social-network"></div>
                    </footer>
                </article>
                <div id="content-bottom-grid"><?php print_grid($arr, 2);?></div>
                <aside id="content-bottom-widgets">
                    <?php if($rows_widgets){ show_widgets_content($rows_widgets, "widgets_content", $wrapper_content_width);} ?>
                </aside>
                <aside id="content-bottom-stories">
                </aside>
                <?php print_selection("selection-content-below", $selection_area['content_below']); ?>
            </main>
        </div>

        <div id="wrapper-right-sidebar" class="column" style="width:25%;min-height:1px">
            <?php print_selection("selection-right-sidebar-top", $selection_area['right_sidebar_top']); ?>
            <?php
            echo '<nav id="nav-site-navigation-vertical">';
            if (!isset($_GET['sample'])) {
                get_pages_tree_sitemap($id, $path=get_breadcrumb_path_array($id), $seo=true, $href, $depth=0, $show_pages_id = false, $parent_id, $a=true, $a_add_class=false, $open);
            }
            echo '</nav>';
            ?>
            <aside id="right-sidebar-widgets"><?php if($rows_widgets){ 
                show_widgets_content($rows_widgets, "widgets_right_sidebar", $wrapper_right_sidebar_width);} ?></aside>
            <aside id="right-sidebar-stories">
            </aside>
            <?php print_selection("selection-right-sidebar-bottom", $selection_area['right_sidebar_bottom']); ?>
        </div>

    </div>