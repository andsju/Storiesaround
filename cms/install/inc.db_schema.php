<?php


$charset_collate = "ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_unicode_ci";

$sql_tables = array();

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `calendar_categories` (
  `calendar_categories_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(4) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `rss` tinyint(1) DEFAULT NULL COMMENT 'rss feed',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`calendar_categories_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `calendar_categories_rights` (
  `calendar_categories_rights_id` int(11) NOT NULL AUTO_INCREMENT,
  `calendar_categories_id` int(11) NOT NULL DEFAULT '0',
  `users_id` int(11) NOT NULL DEFAULT '0',
  `groups_id` int(11) NOT NULL DEFAULT '0',
  `rights_read` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'read rights',
  `rights_edit` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'edit rights',
  `rights_create` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'enable create',
  PRIMARY KEY (`calendar_categories_rights_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `calendar_events` (
  `calendar_events_id` int(11) NOT NULL AUTO_INCREMENT,
  `calendar_categories_id` int(11) NOT NULL DEFAULT '0',
  `event` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `event_date` date NOT NULL DEFAULT '0000-00-00',
  `event_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'rss title',
  `event_rss` tinyint(1) DEFAULT NULL COMMENT 'rss feed',
  `event_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'rss link',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`calendar_events_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `calendar_views` (
  `calendar_views_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(4) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`calendar_views_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `calendar_views_members` (
  `calendar_views_members_id` int(11) NOT NULL AUTO_INCREMENT,
  `calendar_views_id` int(11) DEFAULT NULL,
  `calendar_categories_id` int(11) DEFAULT NULL,
  `position` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`calendar_views_members_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `groups` (
  `groups_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0-false, 1-true',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`groups_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `groups_default` (
  `groups_default_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0-false, 1-true',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`groups_default_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `groups_default_members` (
  `groups_default_members_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `groups_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0-false, 1-true',
  `groups_default_id` tinyint(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0-false, 1-true',
  PRIMARY KEY (`groups_default_members_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `groups_members` (
  `groups_members_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `groups_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0-false, 1-true',
  `users_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0-false, 1-true',
  PRIMARY KEY (`groups_members_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL COMMENT 'auto increment id',
  `field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'auto increment field',
  `action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'sql function INSERT SELECT UPDATE DELETE',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_id` int(11) NOT NULL,
  `session` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utc_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`history_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `history_email` (
  `history_email_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_to` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_from` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_body` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `utc_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`history_email_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `pages` (
  `pages_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `parent` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false, 1:true (used for better performance in select views)',
  `position` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:users width rights defined, 1:users, 2:everyone',
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title_alternative` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'alternative title when default is hidden',
  `title_hide` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:show title, 1:hide title',
  `title_tag` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'title tag in body',
  `pages_id_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'set search engine optimization link',
  `meta_keywords` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_additional` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'additional meta content',
  `meta_robots` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'additional meta content',
  `lang` varchar(2) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'set this page html lang attribute - overrides site settings',
  `category_position` int(2) DEFAULT '99' COMMENT 'page category position',
  `category` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'page category',
  `landing_page` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:none, 1:set CSS class',
  `content` longtext COLLATE utf8_unicode_ci,
  `content_author` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Content author',
  `grid_active` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false, 1:true',
  `grid_area` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:above, 1:above next to, 2:below next to, 3:below',
  `grid_custom_classes` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'custom css',
  `grid_content` longtext COLLATE utf8_unicode_ci COMMENT 'json format',
  `grid_cell_template` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:image top 1:heading top',
  `grid_cell_image_height` int(3) UNSIGNED NOT NULL DEFAULT '140' COMMENT 'height in px',
  `folder` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'uploaded content, (default folder:pages_id)',
  `tag` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'tag keywords to match filter criteria',
  `header_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Set static header image',
  `header_caption` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'header image caption',
  `header_caption_align` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'left, center, right',
  `header_caption_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'show header image caption',
  `header_image_timeout` int(5) UNSIGNED NOT NULL DEFAULT '10000' COMMENT 'header image timeout',
  `header_image_fade` varchar(25) COLLATE utf8_unicode_ci DEFAULT 'normal' COMMENT 'header image fade',
  `parallax_scroll` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0:none, 1:scroll',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1:draft, 2:published, 3:archived, 4:pending, 5:trash',
  `template` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1:sidebars, 2:panorama, 3:blog, 4:left menu',
  `template_custom` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'custom template',
  `selections` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'comma separated id: 1,4,5',
  `breadcrumb` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'show breadcrumb 0:false, 1:show, 2:show add children (select), 4:show add children (ul)',
  `search_field_area` tinyint(1) DEFAULT '0' COMMENT '0: none, 1:header, 2:page',
  `content_links` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false (mail friend, print)',
  `plugins` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false 1:true if set',
  `plugin_arguments` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'array of arguments to load plugin',
  `calendar` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false (include calendar)',
  `events` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false, 1:true',
  `reservations` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false, 1:true',
  `comments` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false, 1:true',
  `functions` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false 1:true (custom functions)',
  `stories_columns` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'stories columns',
  `stories_child_area` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:none, 1:left sidebar, 2:right sidebar, 3-6:content',
  `stories_child` int(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'show child stories',
  `stories_promoted_area` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'show promoted stories in specified area',
  `stories_promoted` int(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'show promoted stories, 0:false, 1:true',
  `stories_limit` int(2) DEFAULT '0' COMMENT 'limit stories',
  `stories_css_class` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'uniformed css stories class',
  `stories_equal_height` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:no, 1:yes',
  `stories_selected` int(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'show selected stories',
  `stories_event_dates` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'show event stories 0:false 1:true',
  `stories_event_dates_filter` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'filter event stories',
  `stories_image_copyright` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'show image copyright 0:false 1:true',
  `stories_wide_teaser_image_align` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'selected stories teaser image align',
  `stories_wide_teaser_image_width` int(3) DEFAULT NULL COMMENT 'selected stories teaser image witdh',
  `stories_last_modified` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'show when stories are last modified, 0:false, 1:true',
  `stories_filter` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'filter stories',
  `story_content` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'story content',
  `story_wide_teaser_image` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:exclude 1:include 2:align right',
  `story_css_class` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'set title css class',
  `story_custom_title` tinyint(1) NOT NULL COMMENT 'use custom title 0:false 1:true',
  `story_custom_title_value` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'custom story title',
  `story_promote` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false, 1:true',
  `story_event` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'story event 0:false 1:true',
  `story_event_date` DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'story event date',
  `story_link` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0:false, 1:true',
  `rss_promote` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'promote rss 0:false, 1:true',
  `rss_description` text COLLATE utf8_unicode_ci COMMENT 'rss description',
  `utc_created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'created',
  `utc_modified` DATETIME NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'modified',
  `utc_start_publish` datetime DEFAULT NULL,
  `utc_end_publish` datetime DEFAULT NULL,
  PRIMARY KEY (`pages_id`)
) $charset_collate;
";


$sql_tables[] = 
"
CREATE TABLE `pages_categories` (
  `pages_categories_id` int(3) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `position` int(3) NOT NULL DEFAULT '0',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pages_categories_id`)
  ) $charset_collate;
";
  


$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `pages_calendars` (
  `pages_calendars_id` int(11) NOT NULL AUTO_INCREMENT,
  `pages_id` int(11) NOT NULL DEFAULT '0',
  `calendar_categories_id` int(11) NOT NULL DEFAULT '0',
  `calendar_views_id` int(11) NOT NULL DEFAULT '0',
  `calendar_area` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'content, right-sidebar. left-sidebar',
  `calendar_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:calendar+events, 2:calendar, 3:events',
  `description` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_initiate` date DEFAULT NULL,
  `period_initiate` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'day, week, month',
  PRIMARY KEY (`pages_calendars_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `pages_images` (
  `pages_images_id` int(11) NOT NULL AUTO_INCREMENT,
  `pages_id` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `creator` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Creator or Author',
  `copyright` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `caption` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `caption_extended` varchar(1000) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'extended caption',
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Title attribute',
  `alt` varchar(100) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Alt attribute',
  `xmpdata` varchar(1000) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'xmp data',
  `promote` tinyint(1) DEFAULT NULL COMMENT 'promote image',
  `tag` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'tag image',
  `ratio` decimal(6,3) NOT NULL DEFAULT '0.000' COMMENT 'width/height ratio',
  `sizes` varchar(50) DEFAULT '' COMMENT 'comma separated width sizes in px',
  `story_teaser` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'story teaser image 0-false, 1-true',
  `position` int(4) NOT NULL DEFAULT '0',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pages_images_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `pages_plugins` (
  `pages_plugins_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugins_id` int(11) NOT NULL,
  `pages_id` int(11) NOT NULL,
  `plugins_action` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'default action to fire off plugin',
  `plugins_header` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'optional header',
  `plugins_footer` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'optional footer',
  `area` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pages_plugins_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `pages_rights` (
  `pages_rights_id` int(11) NOT NULL AUTO_INCREMENT,
  `pages_id` int(11) NOT NULL DEFAULT '0',
  `users_id` int(11) NOT NULL DEFAULT '0',
  `groups_id` int(11) NOT NULL DEFAULT '0',
  `rights_read` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'read rights',
  `rights_edit` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'edit rights',
  `rights_create` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'enable create new child page',
  PRIMARY KEY (`pages_rights_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `pages_selections` (
  `pages_selections_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content_html` longtext COLLATE utf8_unicode_ci,
  `content_code` text COLLATE utf8_unicode_ci,
  `grid_content` longtext COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'json format',
  `grid_custom_classes` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'custom css',
  `grid_cell_template` tinyint(1) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT '0:image top 1:heading top',
  `grid_cell_image_height` int(3) COLLATE utf8_unicode_ci DEFAULT '140' COMMENT 'height in px',
  `area` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `external_js` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'external js dependency file, comma seperated if multiple',
  `external_css` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'external css dependency file, comma seperated if multiple',
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false, 1:true',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `position` int(4) NOT NULL DEFAULT '10',
  PRIMARY KEY (`pages_selections_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `pages_stories` (
  `pages_stories_id` int(11) NOT NULL AUTO_INCREMENT,
  `pages_id` int(11) NOT NULL DEFAULT '0',
  `stories_id` int(11) NOT NULL DEFAULT '0',
  `sort_id` int(11) NOT NULL DEFAULT '0',
  `container` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`pages_stories_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `pages_widgets` (
  `pages_widgets_id` int(11) NOT NULL AUTO_INCREMENT,
  `widgets_id` int(11) NOT NULL,
  `pages_id` int(11) NOT NULL,
  `widgets_action` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'default action to fire off widget',
  `widgets_header` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'optional header',
  `widgets_footer` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'optional footer',
  `area` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pages_widgets_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `plugins` (
  `plugins_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugins_class` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `plugins_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `plugins_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plugins_action` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'default action to fire off widget',
  `plugins_active` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false, 1:true',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`plugins_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `site` (
  `site_id` int(1) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `site_slogan` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `site_domain_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'domain URL',
  `site_domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'friendly domain name',
  `site_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `site_copyright` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `site_theme` varchar(25) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'css theme if set',
  `site_ui_theme` varchar(25) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'set new jquery-ui theme',
  `site_wrapper_page_width` int(4) UNSIGNED NOT NULL DEFAULT '1280' COMMENT 'template wrapper helper width',
  `site_template_default` tinyint(1) UNSIGNED NOT NULL DEFAULT '2' COMMENT 'default page template',
  `site_template_sidebar_width` int(2) UNSIGNED NOT NULL DEFAULT '25' COMMENT 'set sidebar width in percent 20-33%',
  `site_template_content_padding` int(3) UNSIGNED NOT NULL DEFAULT '10' COMMENT 'set content padding in px',
  `site_navigation_horizontal` int(1) DEFAULT '1' COMMENT '0:none, 1:root level, 2:all (menubar)',
  `site_navigation_vertical` int(1) NOT NULL DEFAULT '1' COMMENT '0:none, 1:root childs (tree collapsed), 1:root childs (tree expanded), 3:all (tree collapsed), 4:all (tree expanded)',
  `site_navigation_vertical_sidebar` int(1) NOT NULL DEFAULT '0' COMMENT '0:left sidebar when available, 1:right sidebar when available',
  `site_header_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'default header image',
  `site_404` text COLLATE utf8_unicode_ci COMMENT '404 page not found',
  `site_about_cookies_url` varchar(255) COLLATE utf8_unicode_ci COMMENT 'read about cookies url',
  `site_country` varchar(25) COLLATE utf8_unicode_ci DEFAULT '',
  `site_language` varchar(25) COLLATE utf8_unicode_ci DEFAULT '',
  `site_lang` varchar(2) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'set html lang attribute',
  `site_timezone` varchar(25) COLLATE utf8_unicode_ci DEFAULT '',
  `site_dateformat` varchar(25) COLLATE utf8_unicode_ci DEFAULT '',
  `site_timeformat` varchar(25) COLLATE utf8_unicode_ci DEFAULT '',
  `site_firstdayofweek` int(1) DEFAULT '0',
  `site_mail_method` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'PHPMailer function, default PHP mail()',
  `site_smtp_server` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `site_smtp_port` int(5) DEFAULT '25',
  `site_smtp_username` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `site_smtp_password` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `site_smtp_authentication` int(1) DEFAULT '0',
  `site_smtp_debug` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'PHPMailer debug options',
  `site_account_registration` int(1) DEFAULT '0' COMMENT 'allow user registration',
  `site_account_welcome_message` varchar(1000) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'email message registration',
  `site_groups_default_id` int(11) DEFAULT NULL,
  `site_maintenance` int(1) NOT NULL DEFAULT '0',
  `site_maintenance_message` varchar(1000) COLLATE utf8_unicode_ci DEFAULT '',
  `site_error_mode` int(1) DEFAULT '0',
  `site_history_max` int(6) DEFAULT '1000',
  `site_rss_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `site_publish_guideline` varchar(1000) COLLATE utf8_unicode_ci DEFAULT '',
  `site_wysiwyg` varchar(25) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'use wysiwyg editor',
  `site_seo_url` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'use seo friendly url 0:false, 1:true',
  `site_autosave` int(6) UNSIGNED NOT NULL DEFAULT '120000' COMMENT 'set autosave interval',
  `site_meta_tags` varchar(1000) COLLATE utf8_unicode_ci DEFAULT '',
  `site_script` varchar(1000) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'javascript in head tag',
  `site_meta_robots` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`site_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `tags` (
  `tags_id` int(1) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'tag data',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`tags_id`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `users` (
  `users_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `pass_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'salted & hashed password using crypt()',
  `activation_code` char(32) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user account activated - null, registration results in a md5 hash value',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_lastvisit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'last token',
  `login_count` int(10) NOT NULL DEFAULT '0',
  `login_count_fail` int(3) NOT NULL DEFAULT '0' COMMENT 'counts login failure, > 100 login failure and account is locked out',
  `timezone` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'if set overides default site timezone',
  `language` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'if set overides default site language',
  `phone` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mobile` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `photo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'url link to photo',
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'short comment',
  `role_CMS` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0-none, 1-user, 2-contributor, 3-author, 4-editor, 5-administrator, 6-superadministrator',
  `role_LMS` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0-none, 1-student, 2-tutor, 3-teacher, 4-administrator',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0-deleted, 1-inactive, 2-active',
  `debug` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'let user (administrators) view debug info 0-false, 1-true',
  `profile_edit` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'users can edit profile settings 0-false, 1-true',
  PRIMARY KEY (`users_id`),
  UNIQUE KEY `email` (`email`)
) $charset_collate;
";

$sql_tables[] = 
"
CREATE TABLE IF NOT EXISTS `widgets` (
  `widgets_id` int(11) NOT NULL AUTO_INCREMENT,
  `widgets_class` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `widgets_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `widgets_css` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'external css file',
  `widgets_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `widgets_action` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'default action to fire off widget',
  `widgets_active` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0:false, 1:true',
  `utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`widgets_id`)
) $charset_collate;
";

$sql_tables[] = 
"
ALTER TABLE `pages` ADD FULLTEXT KEY `pages_index` (`title`,`content`,`grid_content`,`story_content`,`tag`,`pages_id_link`);
";

$sql_tables[] = 
"
ALTER TABLE `users` ADD FULLTEXT KEY `users_index` (`first_name`,`last_name`,`email`,`user_name`);
";

?>