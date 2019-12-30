
    <div id="wrapper-page">
    
        <div id="wrapper-content" class="column" style="width:<?php echo $content_percent_width;?>%">
            <main>
                <div id="content-top-selections"><?php print_selection("selection-content-above", $selection_area['content_above']); ?></div>
                <div id="content-top-grid"><?php print_grid($arr, 1);?></div>
                <div id="content-breadcrumb"><?php print_breadcrumb($id, $arr['breadcrumb']); ?></div>
                <article>
                    <?php if ($arr['title_hide'] == 0) { ?>
                    <header>
                        <h1 id="content-title" class="editable"><?php echo $page_title_body .' '.$icon; ?></h1>                        
                    </header>
                    <?php } ?>
                    <?php if ($arr['search_field_area'] == 4) { print_search_field_area_page($languages); } ?>
                    <?php if ($arr['search_field_area'] == 4) { print_search_field_result($languages); } ?>
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

    </div>