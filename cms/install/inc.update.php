<?php

// update cmd - checks alter cmd before try / catch update
// set values in array
$sqls = array();						// just sql cmd in string
$sqls_alter_change_column = array(); 	// array -> tbl_name | col_name | col_name_new | column_definition	
$sqls_alter_modify_column = array();	// array -> tbl_name | col_name | column_definition
$sqls_alter_add_column = array();		// array -> tbl_name | col_name | column_definition
$sqls_alter_drop_column = array();		// array -> tbl_name | col_name

// 1.5.3	20150221
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_template_sidebar_width", "column_definition" => "int(2) unsigned NOT NULL DEFAULT '25' COMMENT 'set sidebar width in percent' AFTER `site_theme`");
// 20150320
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_autosave", "column_definition" => "int(6) unsigned NOT NULL DEFAULT '120000' COMMENT 'set autosave interval' AFTER `site_seo_url`");
//20150728
$sqls[] = "ALTER TABLE pages ADD FULLTEXT INDEX pages_index (title, content, story_content, story_wide_content, tag)";
$sqls[] = "ALTER TABLE users ADD FULLTEXT INDEX users_index (first_name, last_name, email, user_name)";
$sqls_alter_modify_column[] = array("tbl_name" => "pages", "col_name" => "header", "column_definition" => "varchar(100) DEFAULT '' COMMENT 'Set static header image' AFTER `tag`");
// 20150804
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_ui_theme", "column_definition" => "varchar(25) DEFAULT '' COMMENT 'set new jquery-ui theme' AFTER `site_theme`");
// 20151213
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_title_position", "column_definition" => "int(1) DEFAULT '0' COMMENT 'set title position' AFTER `site_template_content_padding`");
// 20160216
// moved folder widgets from /content to /cms
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "title_hide", "column_definition" => "int(1) DEFAULT '0' COMMENT '0:show title, 1:hide title' AFTER `title`");
// 1.6.3
//20160306
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "story_link", "column_definition" => "tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0:false, 1:true' AFTER `story_event_date`");
// 1.8.0
//20171201
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "grid_active", "column_definition" => "tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:false, 1:true' AFTER `content_author`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "grid_area", "column_definition" => "tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:above, 1:above next to, 2:below next to, 3:below' AFTER `grid_active`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "grid_custom_classes", "column_definition" => "varchar(50) DEFAULT '' COMMENT 'custom css' AFTER `grid_area`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "grid_content", "column_definition" => "longtext DEFAULT '' COMMENT 'json format' AFTER `grid_custom_classes`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "grid_cell_template", "column_definition" => "tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:image top 1:heading top' AFTER `grid_content`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "grid_cell_image_height", "column_definition" => "int(3) unsigned NOT NULL DEFAULT '140' COMMENT '0:image above 1:image below' AFTER `grid_cell_template`");
//20171211
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_wrapper_page_width", "column_definition" => "int(4) unsigned NOT NULL DEFAULT '1280' COMMENT 'template wrapper helper width' AFTER `site_ui_theme`");
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_script", "column_definition" => "varchar(1000) DEFAULT '' COMMENT 'javascript in head tag' AFTER `site_meta_tags`");
$sqls_alter_drop_column[] = array("tbl_name" => "pages", "col_name" => "stories_child_type");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "stories_child_area", "column_definition" => "tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:none, 1:left sidebar, 2:right sidebar, 3-6:content' AFTER `stories_columns`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "stories_equal_height", "column_definition" => "tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:no, 1:yes' AFTER `stories_css_class`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "template_custom", "column_definition" => "varchar(100) DEFAULT '' COMMENT 'custom template' AFTER `template`");
$sqls_alter_change_column[] = array("tbl_name" => "pages", "col_name" => "header", "col_name_new" => "header_image", "column_definition" => "varchar(255) DEFAULT '' COMMENT 'Set static header image' AFTER `tag`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "header_caption", "column_definition" => "varchar(255) DEFAULT '' COMMENT 'header image caption' AFTER `header_image`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "header_caption_show", "column_definition" => "tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'show header image caption' AFTER `header_caption`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "header_image_timeout", "column_definition" => "int(5) unsigned NOT NULL DEFAULT '10000' COMMENT 'header image timeout' AFTER `header_caption_show`");
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_template_default", "column_definition" => "tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT 'default page template' AFTER `site_wrapper_page_width`");
$sqls_alter_drop_column[] = array("tbl_name" => "pages", "col_name" => "ads");
$sqls_alter_drop_column[] = array("tbl_name" => "pages", "col_name" => "ads_limit");
$sqls_alter_drop_column[] = array("tbl_name" => "pages", "col_name" => "ads_filter");
//$sqls[] = "DROP INDEX pages_index ON pages";
//$sqls[] = "ALTER TABLE pages ADD FULLTEXT INDEX pages_index (title, content, grid_content, story_content, tag, pages_id_link)";
$sqls_alter_drop_column[] = array("tbl_name" => "site", "col_name" => "site_feed");
$sqls_alter_drop_column[] = array("tbl_name" => "site", "col_name" => "site_feed_interval");
$sqls_alter_drop_column[] = array("tbl_name" => "site", "col_name" => "site_limit_stories");
$sqls_alter_drop_column[] = array("tbl_name" => "site", "col_name" => "site_flash_version");
$sqls_alter_drop_column[] = array("tbl_name" => "site", "col_name" => "site_title_position");
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_header_image", "column_definition" => "varchar(255) DEFAULT '' COMMENT 'default header image' AFTER `site_navigation_vertical_sidebar`");
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_404", "column_definition" => "text DEFAULT '' COMMENT '404 page not found' AFTER `site_header_image`");
// CREATE TABLE IF NOT EXISTS `pages_categories`
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "category", "column_definition" => "varchar(50) DEFAULT '' COMMENT 'page category' AFTER `lang`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "category_position", "column_definition" => "int(2) DEFAULT '99' COMMENT 'page category position' AFTER `lang`");
$sqls_alter_drop_column[] = array("tbl_name" => "pages", "col_name" => "story_wide_content");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "search_field_area", "column_definition" => "tinyint(1) DEFAULT '0' COMMENT '0: none, 1:header, 2:page' AFTER `breadcrumb`");
// 2018-02-14
$sqls_alter_add_column[] = array("tbl_name" => "pages_selections", "col_name" => "grid_content", "column_definition" => "longtext DEFAULT '' COMMENT 'json format' AFTER `content_code`");
$sqls_alter_add_column[] = array("tbl_name" => "pages_selections", "col_name" => "grid_custom_classes", "column_definition" => "varchar(50) DEFAULT '' COMMENT 'custom css' AFTER `grid_content`");
$sqls_alter_add_column[] = array("tbl_name" => "pages_selections", "col_name" => "grid_cell_template", "column_definition" => "tinyint(1) DEFAULT '0' COMMENT '0:image top 1:heading top' AFTER `grid_custom_classes`");
$sqls_alter_add_column[] = array("tbl_name" => "pages_selections", "col_name" => "grid_cell_image_height", "column_definition" => "int(3) DEFAULT '140' COMMENT 'height in px' AFTER `grid_cell_template`");
$sqls_alter_add_column[] = array("tbl_name" => "pages_images", "col_name" => "sizes", "column_definition" => "varchar(50) DEFAULT '' COMMENT 'comma separated width sizes in px' AFTER `ratio`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "title_alternative", "column_definition" => "varchar(100) DEFAULT '' COMMENT 'alternative title when default is hidden' AFTER `title`");
// 2018-05-25
$sqls_alter_add_column[] = array("tbl_name" => "pages_images", "col_name" => "caption_extended", "column_definition" => "varchar(1000) DEFAULT '' COMMENT 'extended caption' AFTER `caption`");
$sqls_alter_add_column[] = array("tbl_name" => "site", "col_name" => "site_about_cookies_url", "column_definition" => "varchar(255) DEFAULT '' COMMENT 'read about cookies url' AFTER `site_404`");
//2019-12-28
$sqls_alter_change_column[] = array("tbl_name" => "pages", "col_name" => "story_event_date", "col_name_new" => "story_event_date", "column_definition" => "DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'story event date'");
$sqls_alter_change_column[] = array("tbl_name" => "pages", "col_name" => "utc_created", "col_name_new" => "utc_created", "column_definition" => "DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created'");
$sqls_alter_change_column[] = array("tbl_name" => "pages", "col_name" => "utc_modified", "col_name_new" => "utc_modified", "column_definition" => "DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'modified'");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "landing_page", "column_definition" => "tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:none, 1:set CSS class' AFTER `category`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "header_image_fade", "column_definition" => "varchar(25) COLLATE utf8_unicode_ci DEFAULT 'normal' COMMENT 'header image fade' AFTER `header_image_timeout`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "parallax_scroll", "column_definition" => "tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0:none, 1:scroll' AFTER `header_image_timeout`");
$sqls_alter_add_column[] = array("tbl_name" => "pages", "col_name" => "header_caption_align", "column_definition" => "varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'left, center, right' AFTER `header_caption`");
?>
