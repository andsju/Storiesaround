
    <div id="wrapper-page">
    
        <div id="wrapper-content" class="column">
        <?php echo $wrapper_content_width; ?>
            <main>
                <?php print_selection("selection-content-above", $selection_area['content_above']); ?>
                <div id="content-top-selections"></div>
                <div id="content-top-grid"><?php show_grid($arr, 1);?></div>
                <div id="content-breadcrumb"><?php print_breadcrumb($id, $arr['breadcrumb']); ?></div>
                <div id="content-edit"></div>
                <article>
                    <header>
                        <h1 id="content-title"><?php echo '<h1>'. $arr['title'] .' '.$icon.'</h1>'; ?></h1>
                        <div id="content-meta"><?php  print_meta($arr['utc_modified'], $dtz, $languages) ?></div>
                    </header>
                    <div id="content-html">        
                        <?php print_selection("selection-content-inside", $selection_area['content_inside']); ?>
                        <?php echo $arr['content'];?>
                    </div>
                    <footer>
                        <div id="content-author"><?php print_author($arr['content_author'], $languages)?></div>
                        <div id="content-social-network"></div>
                    </footer>
                </article>
                <div id="content-bottom-grid"><?php show_grid($arr, 2);?></div>
                <aside id="content-bottom-widgets"></aside>
                <aside id="content-bottom-stories">
                    <?php
                    $cms_dir = CMS_DIR;
                    print_story_events($pages, $languages, $cms_dir, $wrapper_content_width, $arr['stories_event_dates'], $arr['stories_event_dates_filter'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz);
                    ?>

                </aside>
                <?php print_selection("selection-content-below", $selection_area['content_below']); ?>
            </main>
        </div>
        <div id="wrapper-right-sidebar" class="column">
            <?php echo $wrapper_right_sidebar_width; ?>
            <?php print_selection("selection-right-sidebar-top", $selection_area['right_sidebar_top']); ?>
            <?php
            if($_SESSION['site_navigation_vertical_sidebar'] == 0) {
                echo '<nav id="nav-site-navigation-vertical">';
                get_pages_tree_sitemap($parent_id, $id, $path=get_breadcrumb_path_array($id), $a=true, $a_add_class=false, $seo, $href, $open, $depth=0, $show_pages_id = false);									
                echo '</nav>';
            }
            ?>
            <aside id="right-sidebar-widgets"></aside>
            <aside id="right-sidebar-stories"></aside>        
            <img src="css/images/ordmoln_glimakra.png" style="width:100%">
            <?php print_selection("selection-right-sidebar-bottom", $selection_area['right_sidebar_bottom']); ?>
        </div>
    </div>