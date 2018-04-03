
    <div id="wrapper-page">

        <div id="wrapper-left-sidebar" class="column" style="width:<?php echo $left_sidebar_percent_width;?>%;min-height:1px">
            <?php print_selection("selection-left-sidebar-top", $selection_area['left_sidebar_top']); ?>
            <?php
            if($_SESSION['site_navigation_vertical_sidebar'] == 1) {
                echo '<nav id="nav-site-navigation-vertical">';
                if (!isset($_GET['sample'])) {
                    get_pages_tree_sitemap($parent_id, $id, $path=get_breadcrumb_path_array($id), $a=true, $a_add_class=false, $seo=true, $href, $open, $depth=0, $show_pages_id = false);
                }
                echo '</nav>';
            }
            ?>
            <aside id="left-sidebar-widgets"><?php if($rows_widgets){ 
                show_widgets_content($rows_widgets, "widgets_left_sidebar", $wrapper_left_sidebar_width);} ?></aside>
            <aside id="left-sidebar-stories">
            <?php
            print__story__promoted($rows_promoted, $languages, $cms_dir, $id, $wrapper_left_sidebar_width, $stories_area = array(2), $arr['stories_promoted_area'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
            print__story__selected($rows_selected, $languages, $cms_dir, $id, $left_sidebar_percent_width, $wrapper_left_sidebar_width, "left_sidebar", $arr['stories_columns'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
            print__story__child($rows_child, $languages, $cms_dir, $id, $wrapper_left_sidebar_width, $stories_area = array(3,4), $arr['stories_child_area'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
            ?>    
            </aside>        
            <?php print_selection("selection-left-sidebar-bottom", $selection_area['left_sidebar_bottom']); ?>
        </div>
    
        <div id="wrapper-content" class="column column-having-left-sidebar column-having-right-sidebar" style="width:<?php echo $content_percent_width;?>%">
            <main>                
                <div id="content-top-selections"><?php print_selection("selection-content-above", $selection_area['content_above']); ?></div>
                <div id="content-top-grid"><?php print_grid($arr, 1);?></div>
                <div id="content-breadcrumb"><?php print_breadcrumb($id, $arr['breadcrumb']); ?></div>
                <article>
                    <header>
                        <h1 id="content-title" class="editable"><?php echo $page_title_body .' '.$icon; ?></h1>                        
                    </header>
                    <?php if ($arr['search_field_area'] == 3) { print_search_field_area_page($languages); } ?>
                    <?php if ($arr['search_field_area'] > 0) { print_search_field_result($languages); } ?>
                    <?php print_selection("selection-content-inside", $selection_area['content_inside']); ?>
                    <div id="content-html" class="editable"> 
                        <?php echo $arr['content'];?>
                    </div>
                    <footer>
                        <div id="content-meta"><?php print_meta($arr['utc_modified'], $dtz, $languages) ?></div>
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
                    <?php
                    print_story_events($pages, $languages, $cms_dir, $wrapper_content_width, $arr['stories_event_dates'], $arr['stories_event_dates_filter'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz);
                    print__story__promoted($rows_promoted, $languages, $cms_dir, $id, $wrapper_content_width, $stories_area = array(5,6,7,8,9), $arr['stories_promoted_area'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
                    $this_width = $arr['stories_columns'] == 0 ? $wrapper_content_width : $wrapper_right_sidebar_width;
                    print__story__selected($rows_selected, $languages, $cms_dir, $id, $content_percent_width, $this_width, "main", $arr['stories_columns'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
                    print__story__child($rows_child, $languages, $cms_dir, $id, $wrapper_content_width, $stories_area = array(5,6,7,8,9), $arr['stories_child_area'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
                    ?>
                </aside>
                <?php print_selection("selection-content-below", $selection_area['content_below']); ?>
            </main>
        </div>

        <div id="wrapper-right-sidebar" class="column" style="width:<?php echo $right_sidebar_percent_width;?>%;min-height:1px">
            <?php print_selection("selection-right-sidebar-top", $selection_area['right_sidebar_top']); ?>
            <?php
            if($_SESSION['site_navigation_vertical_sidebar'] == 0) {
                echo '<nav id="nav-site-navigation-vertical">';
                if (!isset($_GET['sample'])) {
                    get_pages_tree_sitemap($parent_id, $id, $path=get_breadcrumb_path_array($id), $a=true, $a_add_class=false, $seo=true, $href, $open, $depth=0, $show_pages_id = false);
                }
                echo '</nav>';
            }
            ?>
            <aside id="right-sidebar-widgets"><?php if($rows_widgets){ 
                show_widgets_content($rows_widgets, "widgets_right_sidebar", $wrapper_right_sidebar_width);} ?></aside>
            <aside id="right-sidebar-stories">
            <?php
            print__story__promoted($rows_promoted, $languages, $cms_dir, $id, $wrapper_right_sidebar_width, $stories_area = array(3,4), $arr['stories_promoted_area'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
            print__story__selected($rows_selected, $languages, $cms_dir, $id, $right_sidebar_percent_width, $wrapper_right_sidebar_width, "right_sidebar", $arr['stories_columns'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
            print__story__child($rows_child, $languages, $cms_dir, $id, $wrapper_right_sidebar_width, $stories_area = array(3,4), $arr['stories_child_area'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
            ?>
            </aside>
            <?php print_selection("selection-right-sidebar-bottom", $selection_area['right_sidebar_bottom']); ?>
        </div>
        
    </div>