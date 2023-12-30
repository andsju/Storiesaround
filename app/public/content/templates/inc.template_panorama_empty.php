
    <div id="wrapper-page">
    
        <div id="wrapper-content" class="column" style="width:<?php echo $content_percent_width;?>%">
            <main>
                <?php print_selection("selection-content-above", $selection_area['content_above']); ?>
                <div id="content-top-selections"></div>
                <div id="content-top-grid"><?php print_grid($arr, 1);?></div>
                <div id="content-bottom-grid"><?php print_grid($arr, 2);?></div>
                <aside id="content-bottom-widgets">
                    <?php if($rows_widgets){ show_widgets_content($rows_widgets, "widgets_content", $wrapper_content_width);} ?>
                </aside>
                <aside id="content-bottom-stories">
                    <?php

                    print__story__promoted($rows_promoted, $languages, $cms_dir, $id, $wrapper_content_width, $stories_area = array(3,4,5,6), $arr['stories_promoted_area'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
                    print_story_events($pages, $languages, $cms_dir, $wrapper_content_width, $arr['stories_event_dates'], $arr['stories_event_dates_filter'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_last_modified'], $dtz);
                    $this_width = $arr['stories_columns'] == 0 ? $wrapper_content_width : $wrapper_right_sidebar_width;
                    print__story__selected($rows_selected, $languages, $cms_dir, $id, $content_percent_width, $this_width, "main", $arr['stories_columns'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
                    print__story__child($rows_child, $languages, $cms_dir, $id, $wrapper_content_width, $stories_area = array(5,6,7,8,9), $arr['stories_child_area'], $arr['stories_css_class'], $arr['stories_wide_teaser_image_align'], $arr['stories_wide_teaser_image_width'], $arr['stories_image_copyright'], $arr['stories_last_modified'], $arr['stories_limit'], $arr['stories_filter'], $dtz);
                    ?>
                </aside>
                <?php print_selection("selection-content-below", $selection_area['content_below']); ?>
            </main>
        </div>

    </div>