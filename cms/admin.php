<?php

// include core
require_once 'includes/inc.core.php';

// initiate class
$z = new Site();
$site = $z->getSite();

// user role access 
if(!get_role_CMS('user') == 1) { header('Location: index.php'); die;}

// wysiwyg editor
$wysiwyg_editor = isset($_SESSION['site_wysiwyg']) ? get_editor_settings($editors, $_SESSION['site_wysiwyg']) : null;

// css files
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css',
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/cms/libraries/jquery-datatables/style.css',
	CMS_DIR.'/cms/libraries/fileuploader/fileuploader.css'
);

// css files... add css jquery-ui theme
if(isset($_SESSION['site_ui_theme'])) {
	$ui_theme = '/cms/libraries/jquery-ui/theme/'.$_SESSION['site_ui_theme'].'/jquery-ui.css';
	if(file_exists(CMS_ABSPATH .$ui_theme)) {
		if (($key = array_search(CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', $css_files)) !== false) {
			unset($css_files[$key]);
		}
		array_push($css_files, CMS_DIR . $ui_theme);
	}
}

// javascript files
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js',
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js',	
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/js/functions.js',
	CMS_DIR.'/cms/libraries/tinymce/plugins/moxiemanager/js/moxman.loader.min.js'
);

// javascript files... add wysiwyg file
if (is_array($wysiwyg_editor)) {
	if(file_exists(CMS_ABSPATH .'/cms/libraries/'.$wysiwyg_editor['include_js_file'])) {
		array_push($js_files, CMS_DIR.'/cms/libraries/'.$wysiwyg_editor['include_js_file']);
	}
	if(file_exists(CMS_ABSPATH .'/cms/libraries/'.$wysiwyg_editor['include_js_script'])) {
		array_push($js_files, CMS_DIR.'/cms/libraries/'.$wysiwyg_editor['include_js_script']);
	}
}


// meta tags
$meta_keywords = $meta_description = $meta_robots = $meta_additional = $meta_author = null;

// include header
$page_title = 'Storiesaround - administration';
$body_style = "width:1250px;max-width:100% !important;margin-top:50px;";
include_once 'includes/inc.header_admin.php';

// add toolbar
include_once 'includes/inc.site_active_user_administration.php';

?>


<script>
	$(document).ready(function() {

		$(function() {
			$("#tab_admin").tabs({
			});
			$("#tab_admin").show();
		});	
		
		$(function() {
			$("#tab_it").tabs({
			});
			$("#tab_it").show(); 
		});	

		$(function() {
			$("#tab_pages").tabs({
			});
			$("#tab_pages").show(); 
		});	

		$(".colorbox_edit").colorbox({
			width:"100%", 
			height:"100%",
			reposition: false,
			transition:"none",
			iframe:true, 
			onClosed:function(){ 
			}
		});

		$(".colorbox_edit_reload").colorbox({
			width:"100%", 
			height:"100%",
			reposition: false,
			transition:"none",
			iframe:true, 
			onClosed:function(){ 
				location.reload(true); 
			}
		});
		
		$(".users_groups_view").colorbox({
			width:"100%", 
			height:"100%",
			reposition: false,
			transition:"none",
			iframe:true, 
			onClosed:function(){ 
			}
		});

		$('#btn_site_general_settings').click(function(event){
			event.preventDefault();
			var action = "save_site_general_settings";
			var token = $("#token").val();
			var site_id = $("#site_id").val();
			var site_name = $("#site_name").val();
			var site_slogan = $("#site_slogan").val();
			var site_domain_url = $("#site_domain_url").val();
			var site_domain = $("#site_domain").val();
			var site_email = $("#site_email").val();
			var site_copyright = $("#site_copyright").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_general_settings').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_general_settings').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: { 
					action: action, token: token, site_id: site_id, site_name: site_name, site_slogan: site_slogan,
					site_domain_url: site_domain_url, site_domain: site_domain, site_email: site_email, site_copyright: site_copyright 
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_site_general_settings');
				},
			});
		});	
		
		$('#btn_site_design').click(function(event){
			event.preventDefault();
			var action = "save_site_design";
			var token = $("#token").val();
			var site_id = $("#site_id").val();
			var site_wrapper_page_width = $("#site_wrapper_page_width").val();
			var site_theme = $("#site_theme option:selected").val();
			var site_ui_theme = $("#site_ui_theme option:selected").val();
			var site_template_default = $('input:radio[name=site_template_default]:checked').val();
			var site_template_content_padding = $("#site_template_content_padding option:selected").val();
			var site_template_sidebar_width = $("#site_template_sidebar_width").val();
			var site_navigation_horizontal = $("#site_navigation_horizontal").val();
			var site_navigation_vertical = $("#site_navigation_vertical").val();
			var site_navigation_vertical_sidebar = $("#site_navigation_vertical_sidebar").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_design').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_design').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',				
				data: { 
					action: action, token: token, site_id: site_id, site_wrapper_page_width: site_wrapper_page_width, site_theme: site_theme, site_ui_theme: site_ui_theme, site_template_content_padding: site_template_content_padding,
					site_template_sidebar_width: site_template_sidebar_width, site_template_default: site_template_default, site_template_sidebar_width: site_template_sidebar_width,  
					site_navigation_horizontal: site_navigation_horizontal, site_navigation_vertical: site_navigation_vertical, site_navigation_vertical_sidebar: site_navigation_vertical_sidebar
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_site_design');
				},
			});
		});	
		
		$('#btn_site_account_settings').click(function(event){
			event.preventDefault();
			var action = "save_site_account_settings";
			var token = $("#token").val();
			var site_id = $("#site_id").val();
			var site_account_registration = $('input:checkbox[name=site_account_registration]').is(':checked') ? 1 : 0;
			var site_account_welcome_message = $("#site_account_welcome_message").val();
			var site_groups_default_id = $("#site_groups_default_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_account_settings').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_account_settings').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',				
				data: { 
					action: action, token: token, site_id: site_id, site_account_registration: site_account_registration,
					site_account_welcome_message: site_account_welcome_message, site_groups_default_id: site_groups_default_id
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_site_account_settings');
				},
			});
		});	

		$('#btn_site_content').click(function(event){
			event.preventDefault();
			var action = "save_site_content";
			var token = $("#token").val();
			var site_id = $("#site_id").val();
			var site_header_image = $("#site_header_image").val();
			var site_404 = get_textarea_editor('<?php echo $wysiwyg_editor['editor']; ?>', 'site_404');
			var site_rss_description = $("#site_rss_description").val();
			var site_publish_guideline = $("#site_publish_guideline").val();
			console.log("site_header_image", site_header_image);
			console.log("site_404", site_404);
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_content').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_content').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: { 
					action: action, token: token, site_id: site_id, site_rss_description: site_rss_description,
					site_header_image: site_header_image, site_404: site_404, site_publish_guideline: site_publish_guideline
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_site_content');
				},
			});
			
		});	

		$('#btn_site_script').click(function(event){
			event.preventDefault();
			var action = "save_site_script";
			var token = $("#token").val();
			var site_id = $("#site_id").val();			
			var site_script = $("#site_script").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_script').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_script').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: { 
					action: action, token: token, site_id: site_id, site_script: site_script
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_site_script');
				},
			});
		});

		$('#btn_site_meta').click(function(event){
			event.preventDefault();
			var action = "save_site_meta";
			var token = $("#token").val();
			var site_id = $("#site_id").val();			
			var site_meta_tags = $("#site_meta_tags").val();
			var site_meta_tags = encodeURIComponent(site_meta_tags);
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_meta').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_meta').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: { 
					action: action, token: token, site_id: site_id, site_meta_tags: site_meta_tags
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_site_meta');
				},
			});
		});
		
		$('#btn_site_maintenance').click(function(event){
			event.preventDefault();
			var action = "save_site_maintenance";
			var token = $("#token").val();
			var site_id = $("#site_id").val();
			var site_maintenance = $('input:checkbox[name=site_maintenance]').is(':checked') ? 1 : 0;
			var site_error_mode = $('input:checkbox[name=site_error_mode]').is(':checked') ? 1 : 0;
			var site_maintenance_message = $("#site_maintenance_message").val();
			var site_history_max = $("#site_history_max").val();		
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_maintenance').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_maintenance').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: { 
					action: action, token: token, site_id: site_id, site_maintenance: site_maintenance,
					site_error_mode: site_error_mode, site_maintenance_message: site_maintenance_message, site_history_max: site_history_max
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_site_maintenance');
				},
			});
		});			
		
		$('#btn_site_history_keep').click(function(event){
			event.preventDefault();
			var action = "history_keep";
			var token = $("#token").val();
			var site_id = $("#site_id").val();
			var site_history_max = $("#site_history_max option:selected").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_history_keep').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_history_keep').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: { 
					action: action, token: token, site_id: site_id, site_history_max: site_history_max
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_site_history_keep');
				},
			});
		});	
		
		$('#btn_site_configuration').click(function(event){
			event.preventDefault();
			var action = "save_site_configuration";
			var token = $("#token").val();
			var site_id = $("#site_id").val();
			var site_country = $("#site_country").val();
			var site_language = $("#site_language option:selected").val();
			var site_lang = $("#site_lang").val();
			var site_timezone = $("#site_timezone").val();
			var site_dateformat = $("#site_dateformat").val();
			var site_timeformat = $("#site_timeformat").val();
			var site_firstdayofweek = $("#site_firstdayofweek").val();
			var site_wysiwyg = $("#site_wysiwyg").val();
			var site_autosave = $("#site_autosave").val();
			var site_seo_url = $('input:checkbox[name=site_seo_url]').is(':checked') ? 1 : 0;
			var site_mail_method = $("#site_mail_method").val();
			var site_smtp_server = $("#site_smtp_server").val();
			var site_smtp_port = $("#site_smtp_port").val();
			var site_smtp_username = $("#site_smtp_username").val();
			var site_smtp_password = $("#site_smtp_password").val();
			var site_smtp_authentication = $('input:checkbox[name=site_smtp_authentication]').is(':checked') ? 1 : 0;
			var site_smtp_debug = $('input:checkbox[name=site_smtp_debug]').is(':checked') ? 1 : 0;
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_configuration').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_configuration').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: { 
					action: action, token: token, site_id: site_id, 
					site_country: site_country, site_language: site_language, site_lang: site_lang, site_timezone: site_timezone, site_dateformat: site_dateformat,
					site_timeformat: site_timeformat, site_firstdayofweek: site_firstdayofweek, site_wysiwyg: site_wysiwyg, site_autosave: site_autosave, site_seo_url: site_seo_url,
					site_mail_method: site_mail_method, site_smtp_server: site_smtp_server, site_smtp_port: site_smtp_port, site_smtp_username: site_smtp_username, 
					site_smtp_password: site_smtp_password, site_smtp_authentication: site_smtp_authentication, site_smtp_debug: site_smtp_debug
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_site_configuration');
				},
			});
		});	
		
		$('#btn_site_mail').click(function(event){
			event.preventDefault();
			var action = "check_smtp_settings";
			var token = $("#token").val();
			var smtp_email_to = $("#smtp_email_to").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_smtp_mail').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_smtp_mail').hide()",1000)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: { 
					action: action, token: token, smtp_email_to: smtp_email_to
				},
				success: function(message){	
					ajaxReplyLetItBe(message,'#ajax_status_site_mail');
				},
			});
		});	

		$( ".toolbar_widgets_instance button" ).button({
			icons: {
				secondary: "ui-icon-extlink"
			},
			text: false
		});

		$( ".toolbar_widgets_status button" ).button({
			icons: {
				secondary: "ui-icon-power"
			},
			text: true
		});
				
		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});
		$( ".toolbar_open button" ).button({
			icons: {
				secondary: "ui-icon-folder-open"
			},
			text: true
		});
		$( ".toolbar_delete button" ).button({
			icons: {
				secondary: "ui-icon-minus"
			},
			text: true
		});
		$( ".toolbar_save button" ).button({
			icons: {
				secondary: "ui-icon-disk"
			},
			text: true
		});
		$( ".toolbar_search button" ).button({
			icons: {
				secondary: "ui-icon-search"
			},
			text: true
		});
		$( ".toolbar_add button" ).button({
			icons: {
				secondary: "ui-icon-plus"
			},
			text: true
		});	
		
		$( "#sidebar_slider" ).slider({
			value: <?php echo $_SESSION['site_template_sidebar_width']; ?>,
			min: 20,
			max: 33,
			step: 1,
			slide: function( event, ui ) {
				$( "#site_template_sidebar_width" ).val( ui.value );
			}
		});

		$( "#site_template_sidebar_width" ).val( $( "#sidebar_slider" ).slider( "value" ) );
		
		$("#dir_header_view").delegate(".header_image", "click", function() {
			var image = $(this).attr("data-image");
			$("#site_header_image").val(image);

		});

		$("#dir_header_show").click(function (event) {
			event.preventDefault();
            var action = "header_files";
            var directory = "/content/uploads/header/";
            var token = $("#token").val();
			
            $.ajax({
                type: 'POST',
                url: 'admin_edit_ajax.php',
                data: "action=" + action + "&token=" + token + "&directory=" + directory,
                success: function (data) {
                    $("#dir_header_view").empty().html(data).hide().fadeIn('fast');
				}
			});
			$("#dir_header_view_info").show();
		});

	});
</script>



<?php

// build tab menu
echo '<div class="admin-tabs-level-1">';
echo '<img src="css/images/storiesaround_logotype_black.png" alt="Storiesaround logotype" style="width:120px;float:right;"/>';
echo '<div class="sub-tabs ui-tabs ui-widget" style="float:left;">';
get_tab_menu_jquery_ui_look_alike($_SERVER["PHP_SELF"], array("dashboard","site","files"), array("Dashboard","Site","Files"), "t", "", null, $ui_ul_add_class="ui-two", $ui_a_add_class="");
echo '</div>';

echo '<div class="sub-tabs ui-tabs ui-widget ui-corner-all" style="float:left;">';
get_tab_menu_jquery_ui_look_alike($_SERVER["PHP_SELF"], array("users","groups"), array("Users","Groups"), "t", "", null, $ui_ul_add_class="ui-one", $ui_a_add_class="");
echo '</div>';

echo '<div class="sub-tabs ui-tabs ui-widget ui-corner-all" style="float:left;">';
get_tab_menu_jquery_ui_look_alike($_SERVER["PHP_SELF"], array("pages","selections","calendars"), array("Pages","Selections","Calendars"), "t", "", null, $ui_ul_add_class="ui-two", $ui_a_add_class="");
echo '</div>';

echo '<div class="sub-tabs ui-tabs ui-widget ui-corner-all" style="float:left;">';
get_tab_menu_jquery_ui_look_alike($_SERVER["PHP_SELF"], array("widgets","plugins"), array("Widgets","Plugins"), "t", "", null, $ui_ul_add_class="ui-one" ,$ui_a_add_class="");
echo '</div>';

echo '<div class="sub-tabs ui-tabs ui-widget ui-corner-all" style="float:left;">';
get_tab_menu_jquery_ui_look_alike($_SERVER["PHP_SELF"], array("updates"), array("Updates"), "t", "", null, $ui_ul_add_class="ui-two" ,$ui_a_add_class="");
echo '</div>';
echo '</div>';

$t = (isset($_GET['t'])) ? $_GET['t'] : null;

// url
$this_url = $_SERVER['PHP_SELF'] .'?t='. $t;

switch($t) {

	case 'site':
		
		echo '<input type="hidden" name="site_id" id="site_id" value="'.$site['site_id'].'" />';		

		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-site"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Site</h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("general","design","content","script","meta","maintenance","configuration","account","history","log","?"), array("General settings","Design","Content","Script","Meta","Maintenance","Configuration","Account registration","History","Log","?"), "tg", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';
		
		if(!get_role_CMS('superadministrator') == 1) {
			die;
		}
		
		$tg = (isset($_GET['tg'])) ? $_GET['tg'] : null;

		function get_input_text($field, $field_name, $field_class, $field_style, $maxlength, $field_value, $field_help) {
			echo '<p class="admin-text">';
				echo '<label for="'.$field.'" class="admin-text">'.$field_name.'</label><br />';
				echo '<input type="text" name="'.$field.'" id="'.$field.'" title="Enter '.$field_name.'" class="'.$field_class.'" style="'.$field_style.'"' ;				
				echo ' value="'.$field_value.'"';
				echo ' maxlength="'.$maxlength.'"';
				echo ' /> ';
				echo '<i>'.$field_help.'</i>';
			echo '</p>';
		}

		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';
				
				switch($tg) {
				
					case 'general':
						?>
						
						<div class="admin-panel">
							
							<div style="float:right">						
								<span id="ajax_spinner_site_general_settings" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
								<span id="ajax_status_site_general_settings" style='display:none'></span>&nbsp;
								<span class="toolbar_save"><button id="btn_site_general_settings" style="float:right;margin:0px;">Save</button></span>
							</div>
							<h3 class="admin_heading">Site settings</h3>
							
							<?php
							get_input_text("site_name", "Site name", "admin-text", "width:300px;", "100", $site['site_name'], "");
							get_input_text("site_slogan", "Site slogan", "admin-text", "width:600px;", "100", $site['site_slogan'], "if set, show site slogan with site name in header");
							get_input_text("site_domain_url", "Site domain url", "admin-text", "width:600px;", "255", $site['site_domain_url'], "full url (or friendly url link) to site start page");
							get_input_text("site_domain", "Site domain", "admin-text", "width:600px;", "255", $site['site_domain'], "friendly domain name");
							get_input_text("site_email", "Site email", "admin-text", "width:600px;", "255", $site['site_email'], "admin email");
							get_input_text("site_copyright", "Site copyright", "admin-text", "width:600px;", "255", $site['site_copyright'], "site copyright");
							?>
						
						</div>
										
						<?php
					break;

						
					case 'design':
						
						?>
						
						

						<div class="admin-panel">

							<h3 class="admin_heading">Site width (helper)</h3>
							<p class="admin-text">
								Set this width to match settings in css (widest). Setting is used in repsonsive webdesign to display images in a proper way
							</p>
							<label for="site_wrapper_page_width" class="admin-text">Set page wrapper width in pixels</label><br>
							<input type="text" id="site_wrapper_page_width" name="site_wrapper_page_width" maxlength="4" size="4" value="<?php echo $_SESSION['site_wrapper_page_width']; ?>">
								
						</div>



						<div class="admin-panel">

							<div style="float:right">				
								<span id="ajax_spinner_site_design" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
								<span id="ajax_status_site_design" style='display:none'></span>&nbsp;
								<span class="toolbar_save"><button id="btn_site_design" style="float:right;margin:0px;">Save</button></span>
							</div>
							<h3 class="admin_heading">Site theme</h3>
							<p class="admin-text">
								A theme will override default css setting 
							</p>
							<label for="site_theme" class="admin-text">Set site theme</label><br />

							<?php							
							echo '<select id="site_theme">';
								echo '<option value="">(site default)</option>';
								echo '<option value=""></option>';
								foreach (new DirectoryIterator(CMS_ABSPATH.'/content/themes') as $fileInfo) {
									if($fileInfo->isDot()) continue;
									$css = $fileInfo->getFilename();
									echo '<option value="'.$css.'"';
										if(isset($_SESSION['site_theme'])) {
											if($_SESSION['site_theme'] == $css) {
												echo ' selected';
											}
										}
									echo '>'.$css.'</option>';
								}
							echo '</select>';
							?>
							<a href="admin.php?t=files&tf=edit_css"> &raquo;&nbsp;edit css theme</a>
							<p class="admin-text">
								
							</p>
							<label for="site_theme" class="admin-text">Set content padding</label><br />

							<?php							
							echo '<select id="site_template_content_padding">';
								echo '<option value="">0</option>';
								for($i=1;$i<=90;$i++) {
									echo '<option value="'.$i.'"';
										if(isset($_SESSION['site_template_content_padding'])) {
											if($_SESSION['site_template_content_padding'] == $i) {
												echo ' selected';
											}
										}
									echo '>'.$i.'</option>';									
								}
							echo '</select>';
							?>
							Page template dependency (no sidebar, one sidebar or joined sidebars). Adjust if background color / image / graphic / borders appears close content.
						</div>



						<div class="admin-panel">

							<h3 class="admin_heading">Jquery-ui theme</h3>
							<p class="admin-text">
								A theme will override default ui-theme
							</p>
							<label for="site_theme" class="admin-text">Set jquery-ui theme</label><br />

							<?php							
							echo '<select id="site_ui_theme">';
								echo '<option value="">(site default)</option>';
								echo '<option value=""></option>';
								foreach (new DirectoryIterator(CMS_ABSPATH.'/cms/libraries/jquery-ui/theme') as $fileInfo) {
									if($fileInfo->isDot()) continue;
									$css = $fileInfo->getFilename();
									echo '<option value="'.$css.'"';
										if(isset($_SESSION['site_ui_theme'])) {
											if($_SESSION['site_ui_theme'] == $css) {
												echo ' selected';
											}
										}
									echo '>'.$css.'</option>';
								}
							echo '</select>';
							?>
								
						</div>

						<div class="admin-panel">
							<h3 class="admin_heading">Navigation</h3>
							<p class="admin-text">
								<label for="site_navigation_horizontal" class="admin-text">Site navigation horizontal</label><br />
								<select name="site_navigation_horizontal" id="site_navigation_horizontal">
									<option value="0" <?php if($_SESSION['site_navigation_horizontal'] == 0) {echo ' selected';} ?>>none</option>
									<option value="1" <?php if($_SESSION['site_navigation_horizontal'] == 1) {echo ' selected';} ?>>root level</option>
									<option value="2" <?php if($_SESSION['site_navigation_horizontal'] == 2) {echo ' selected';} ?>>tree </option>
								</select>
								<i> set site navigation horizontal</i>
							</p>
							<p class="admin-text">
								<label for="site_navigation_vertical" class="admin-text">Site navigation vertical</label><br />
								<select name="site_navigation_vertical" id="site_navigation_vertical">
									<option value="0" <?php if($_SESSION['site_navigation_vertical'] == 0) {echo ' selected';} ?>>none</option>
									<option value="1" <?php if($_SESSION['site_navigation_vertical'] == 1) {echo ' selected';} ?>>root childs tree collapsed)</option>
									<option value="2" <?php if($_SESSION['site_navigation_vertical'] == 2) {echo ' selected';} ?>>root childs (tree expanded)</option>
									<option value="3" <?php if($_SESSION['site_navigation_vertical'] == 3) {echo ' selected';} ?>>all (tree collapsed)</option>
									<option value="4" <?php if($_SESSION['site_navigation_vertical'] == 4) {echo ' selected';} ?>>all (tree expanded)</option>							
								</select>
								<i> set site navigation vertical</i>
							</p>
							<p class="admin-text">
								<label for="site_navigation_vertical_sidebar" class="admin-text">Site navigation vertical sidebar</label><br />
								<select name="site_navigation_vertical_sidebar" id="site_navigation_vertical_sidebar">
									<option value="0" <?php if($_SESSION['site_navigation_vertical_sidebar'] == 0) {echo ' selected';} ?>>left sidebar when available</option>
									<option value="1" <?php if($_SESSION['site_navigation_vertical_sidebar'] == 1) {echo ' selected';} ?>>right sidebar when available</option>
								</select>
								<i> set site navigation vertical sidebar</i>
							</p>
						</div>

						
						<div class="admin-panel">

							<h3 class="admin_heading">Header - Footer</h3>
							<p>
								<table>
									<tr>
										<td style="text-align:left;vertical-align:top;width:50%;">
										<p>
										Customize (listed) files.  Existing file in directory <i>"../includes_replace/"</i> will <strong>override default file.</strong>
										</p>
										<p>
										Copy file from directory <i>"cms/includes/[name of file]"</i>&nbsp;&nbsp;&raquo; and paste into &raquo;&nbsp;&nbsp;<i>"content/includes/[name of file]"</i>
										</p>
										<label for="site_theme" class="admin-text">Customize</label><br />

										<?php							
										echo '<select id="site_header_files">';
											echo '<option value=""></option>';
											foreach (new DirectoryIterator(CMS_ABSPATH.'/content/includes') as $fileInfo) {
												if($fileInfo->isDot()) continue;
												$css = $fileInfo->getFilename();
												echo '<option value="'.$css.'"';
													if(isset($_SESSION['site_theme'])) {
														if($_SESSION['site_theme'] == $css) {
															echo ' selected';
														}
													}
												echo '>'.$css.'</option>';
											}
										echo '</select>';
										?>
										<a href="admin.php?t=files&tf=edit_files"> &raquo;&nbsp;edit file</a>
										</p>
										</td>
										<td style="text-align:right;vertical-align:top;width:20%;">
										<pre>inc.site_header.php<br />inc.head_elements.php<br />inc.body_elements.php<br />inc.footer.php</pre>
										</td>
									</tr>
								</table>								
							<p>
							
						</div>



						<div class="admin-panel">
							
							<table border="0" style="width:100%;">
								<tr>
									<td width="25%" style="vertical-align:top;padding-right:50px;">
										
										<h3 class="admin_heading">Page template settings</h3>
										<p>
											Set default template &raquo;
										</p>

										<p>
											Set value for sidebar width (if sidebar exists in choosen template). Applies to <b>all pages in this site</b>.
										</p>
										<p>Default sidebar width: 25%</p>

										<input type="text" id="site_template_sidebar_width" readonly style="border:0; font-weight:bold;background:#f8f8f8;">%
										<div id="sidebar_slider" style="width:150px;"></div>
										
									</td>
									<td>
										<div style="box-sizing:border-box;width:100%;padding:0 20px;height:280px;overflow-y: hidden;overflow:auto;background:#FCFCFC;border: 1px dashed #D0D0D0;">
											<div class="page_templates"><input type="radio" name="site_template_default" value="0" <?php if($site['site_template_default'] == 0) {echo 'checked';}?>> "Sidebars"<img src="css/images/template_sidebars.png" style="margin-top:10px;height:75px;"></div>
											<div class="page_templates"><input type="radio" name="site_template_default" value="1" <?php if($site['site_template_default'] == 1) {echo 'checked';}?>> "Left sidebar"<img src="css/images/template_sidebar_left.png" style="margin-top:10px;height:75px;"></div>
											<div class="page_templates"><input type="radio" name="site_template_default" value="2" <?php if($site['site_template_default'] == 2) {echo 'checked';}?>> "Right sidebar"<img src="css/images/template_sidebar_right.png" style="margin-top:10px;height:75px;"></div>
											<div class="page_templates"><input type="radio" name="site_template_default" value="3" <?php if($site['site_template_default'] == 3) {echo 'checked';}?>> "Panorama"<img src="css/images/template_panorama.png" style="margin-top:10px;height:75px;"></div>
											<div class="page_templates"><input type="radio" name="site_template_default" value="4" <?php if($site['site_template_default'] == 4) {echo 'checked';}?>> "Sidebars joined"<img src="css/images/template_sidebars_close.png" style="margin-top:10px;height:75px;"></div>
											<div class="page_templates"><input type="radio" name="site_template_default" value="5" <?php if($site['site_template_default'] == 5) {echo 'checked';}?>> Custom "main"<img src="css/images/template_panorama_custom_main.png" style="margin-top:10px;height:75px;"></div>
											<div class="page_templates"><input type="radio" name="site_template_default" value="6" <?php if($site['site_template_default'] == 6) {echo 'checked';}?>> Custom "page"<img src="css/images/template_panorama_custom_page.png" style="margin-top:10px;height:75px;"></div>
										</div>

									<td>
								</tr>
							</table>
							
						</div>

						<div class="admin-panel">
						
							<h3 class="admin_heading">Sample templates</h3>
							<p>
							<a href="<?php echo CMS_URL;?>/cms/pages.php?sample" target="_blank">Show sample templates</a>
							</p>
							
						</div>

						
						
						<?php
						break;

						
					case 'content':
						
					
						?>
						
						<div style="float:right">
								<span id="ajax_spinner_site_content" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
								<span id="ajax_status_site_content" style='display:none'></span>&nbsp;
								<span class="toolbar_save"><button id="btn_site_content" style="float:right;margin:0px;">Save</button></span>
							</div>

						<div class="admin-panel">
							<h3 class="admin_heading">Default header image</h3>
							<span class="toolbar"><button id="dir_header_show">Show selectable images</button></span>
							<p id="dir_header_view_info" class="hidden">	
							Click image to select
							</p>
							<div id="dir_header_view" style="max-height:400px;overflow:auto;display:none"></div>
							<input type="text" id="site_header_image" value="<?php echo $site['site_header_image'] ?>">
						</div>

						<div class="admin-panel">
							<h3 class="admin_heading">404 content</h3>
							<div>
								<textarea name="site_404" id="site_404" class="<?php echo $wysiwyg_editor['css-class']; ?>" style=""><?php echo $site['site_404'] ?></textarea>
							</div>

						</div>

						<div class="admin-panel">
							<h3 class="admin_heading">RSS from this site</h3>
							<?php
							get_input_text("site_rss_description", "Site rss description", "admin-text", "width:80%;", "255", $site['site_rss_description'], "");
							?>
						</div>

						
						<div class="admin-panel">
							<h3 class="admin_heading">Guideline for publishing content</h3>
							<p class="admin-text">
								<label for="site_publish_guideline" class="admin-text">Site publish guideline: </label>
								<br />
								<textarea name="site_publish_guideline" id="site_publish_guideline" title="Enter site publish guideline" class="admin-text" style="width:90%;height:100px;"><?php echo $site['site_publish_guideline'];?></textarea>
							</p>
						
						</div>
					
						<?php
						break;
					
					case 'maintenance':
						?>

						<div class="admin-panel">
							<div style="float:right">						
								<span id="ajax_spinner_site_maintenance" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
								<span id="ajax_status_site_maintenance" style='display:none'></span>&nbsp;
								<span class="toolbar_save"><button id="btn_site_maintenance" style="float:right;margin:0px;">Save</button></span>
							</div>

							<h3 class="admin_heading">Maintenance</h3>
						
							<p class="admin-text">
								<label for="site_maintenance" class="admin-text">Site maintenance mode</label><br />
								<input type="checkbox" name="site_maintenance" id="site_maintenance" title="Put site into maintenance mode" value="1" <?php if($site['site_maintenance']==1){echo ' checked="checked"';}?>" />
								<i> when checked site runs in maintenance mode, noone can login (but...)</i>
							</p>
							<p class="admin-text">
								<label for="site_maintenance_message" class="admin-text">Site maintenance message</label>
								<br />
								<textarea name="site_maintenance_message" id="site_maintenance_message" title="Enter site maintenance message" class="admin-text" style="width:90%;height:100px;"><?php echo $site['site_maintenance_message'];?></textarea>
							</p>
							<p class="admin-text">
								<label for="site_error_mode" class="admin-text">Site error mode</label><br />
								<input type="checkbox" name="site_error_mode" id="site_error_mode" title="Run site in non error mode" value="1" <?php if($site['site_error_mode']==1){echo ' checked="checked"';}?>" />
								<i> save all error logs</i>
							</p>
							
							<hr />

							<p class="admin-text">
								<label for="site_history_max" class="admin-text">Site history max</label><br />
								<select name="site_history_max" id="site_history_max">
									<option value="1">1</option>
									<option value="10">10</option>
									<option value="100">100</option>
									<option value="1000" selected="selected">1000</option>
								</select>
								<i> number of history files to keep </i>
								<span class="toolbar"><button id="btn_site_history_keep">Run</button></span>
								<span id="ajax_spinner_site_history_keep" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
								<span id="ajax_status_site_history_keep" style='display:none'></span>&nbsp;
							</p>						
						</div>
										
						<?php
						break;
					
					case 'script':
						?>
						
						<div class="admin-panel">
							<div style="float:right">						
								<span id="ajax_spinner_site_script" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
								<span id="ajax_status_site_script" style='display:none'></span>&nbsp;
								<span class="toolbar_save"><button id="btn_site_script" style="float:right;margin:0px;">Save</button></span>
							</div>
						
							<h3 class="admin_heading">Script in head element</h3>
							<p class="admin-text">
								Set initial JavaScript in head element
							</p>
							<p>
								<label for="site_script" class="admin-text">Script (max 1000 characters)</label>
							</p>

							<textarea name="site_script" id="site_script" style="width:98%;height:200px;" class="code"><?php echo $site['site_script']; ?></textarea>

							<?php							
							?>
							
						</div>
						

						<?php
						break;



					case 'meta':
						?>
						
						<div class="admin-panel">
							<div style="float:right">						
								<span id="ajax_spinner_site_meta" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
								<span id="ajax_status_site_meta" style='display:none'></span>&nbsp;
								<span class="toolbar_save"><button id="btn_site_meta" style="float:right;margin:0px;">Save</button></span>
							</div>
						
							<h3 class="admin_heading">Meta data</h3>
							<p class="admin-text">
								Define static meta tags. Exclude meta tags defined editing a page; <i>(keywords, description robots)</i>. 
							</p>
							<p>
								<label for="site_meta_tags" class="admin-text">Site meta tags (max 1000 characters)</label>
							</p>

							<textarea name="site_meta_tags" id="site_meta_tags" style="width:98%;height:200px;"><?php echo $site['site_meta_tags']; ?></textarea>

							<?php							
							?>
							
						</div>
						

						<?php
						break;
						
					case 'configuration':
						?>

						<div class="admin-panel">
							<div style="float:right">				
								<span id="ajax_spinner_site_configuration" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
								<span id="ajax_status_site_configuration" style='display:none'></span>&nbsp;
								<span class="toolbar_save"><button id="btn_site_configuration" style="float:right;margin:0px;">Save</button></span>
							</div>

							<h3 class="admin_heading">Datetime and local settings</h3>
							<p class="admin-text">
								
							</p>

							<p class="admin-text">
								<label for="site_country" class="admin-text">Site country</label><br />
								<select name="site_country" id="site_country">
									<option value="Sweden">Sweden</option>
								</select>
								<i> set country</i>
							</p>
							
							
							<?php		
							$folder = 'languages';
							$selects = $selected = null;
							//open directory 
							if ($handle = opendir($folder)) {
								$selects .= '<select id="site_language">';
								/* loop over widget directory */
								while (false !== ($file = readdir($handle))) {

									// list all files in the current directory and strip out . and ..
									if ($file != "." && $file != "..") {

										// get filename from php file >> skip extension .php 
										$language = substr($file, 0, -4);
										
										$selects .= '<option value="'.$language.'"';
										if(isset($_SESSION['site_language'])) {
											$selected = ($_SESSION['site_language'] == $language) ? ' selected=selected' : null;
										}
										
										$selects .= $selected;
										$selects .= '>'.$language.'</option>';
									}
								}
								$selects .= '</select>';
							}
							
							echo '<p class="admin-text">';
								echo '<label for="site_language" class="admin-text">Site language</label><br />';
								echo $selects .'&nbsp;<i>language</i>';
							echo '</p>';

							$lang = isset($_SESSION['site_lang']) ? $_SESSION['site_lang'] : '';
							echo '<p class="admin-text">';
								echo '<label for="site_lang" class="admin-text">Site lang attribute (helps speech synthesis and translation tools) 2-letter</label><br />';
								echo '<input type="text" id="site_lang" name="site_lang" size="2" value="'.$lang.'" />';
							echo '</p>';

							?>
							
							
							<p class="admin-text">
								<label for="site_timezone" class="admin-text">Site timezone</label><br />
								<select name="site_timezone" id="site_timezone">
									<?php									
									get_timezone_identifiers_list_options($option=$_SESSION['site_timezone']);
									?>
								</select>
								<?php
								$dbtime = $z->getSiteDatabaseNow();								
								?>
								<?php echo '<p><b>UTC time</b>: <span class="code time ui-state-highlight">'.utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s').'</span> | <b>Web server time</b>: <span class="code time ui-state-highlight">'.get_utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s').'</span> | <b>Database server time</b>: <span class="code time ui-state-highlight">'.$dbtime['dt'].'</span></p>'; ?>
							</p>
							<p class="admin-text">
								<label for="site_dateformat" class="admin-text">Site date format</label><br />
								<select name="site_dateformat" id="site_dateformat">
									<option value="Y-m-d">Y-m-d</option>
								</select>
								<i> set dateformat</i>
							</p>
							<p class="admin-text">
								<label for="site_timeformat" class="admin-text">Site time format</label><br />
								<select name="site_timeformat" id="site_timeformat">
									<option value="h:m:s">H:i:s</option>
								</select>
								<i> set timeformat</i>
							</p>
							<p class="admin-text">
								<label for="site_firstdayofweek" class="admin-text">First day of week</label><br />
								<select name="site_firstdayofweek" id="site_firstdayofweek">
									<option value="1">monday</option>
								</select>
								<i> set first day of week</i>
							</p>					
						</div>
						

						<div class="admin-panel">
						
							<h3 class="admin_heading">Editor</h3>
							<p class="admin-text">
								Set WYSIWYG editor in textarea
							</p>
						
							<p>
								<label for="site_wysiwyg" class="admin-text">Editor</label><br />
								<select name="site_wysiwyg" id="site_wysiwyg">
									<?php
									
									if(is_array($editors)) {
										foreach($editors as $editor) {
											echo '<option value="'.$editor['editor'].'"';
											if(isset($_SESSION['site_wysiwyg'])) {
												if($_SESSION['site_wysiwyg'] == $editor['editor']) {
													echo ' selected';
												}
											}
											echo '>'.$editor['editor'].'</option>';

										}
									}

									?>
								</select>
								<i> use WYSIWYG editor in textareas when available</i>
							</p>
						</div>
							
						<div class="admin-panel">
						
							<h3 class="admin_heading">Autosave settings</h3>
						
							<p>
								<label for="site_seo_url" class="admin-text">Set autosave in seconds (default 120). Enter value between 30-600 seconds. </label><br />
								<?php
								
								?>
								<input type="text" id="site_autosave" name="site_autosave" style="width:50px;" value="<?php if(isset($_SESSION['site_autosave'])) { echo $_SESSION['site_autosave'] / 1000; }?>" />
							</p>
							
						</div>

						
						<div class="admin-panel">
						
							<h3 class="admin_heading">SEO</h3>
						
							<p>
								<label for="site_seo_url" class="admin-text">Set SEO friendly url's</label><br />
								<input type="checkbox" name="site_seo_url" id="site_seo_url" title="Enable SEO url" value="1" <?php if($site['site_seo_url']==1){echo ' checked="checked"';}?>" />
								<i> use SEO friendly url's</i>
							</p>
							
						</div>
							
						
						<div class="admin-panel">
						
							<h3 class="admin_heading">Mail settings</h3>

							

							<p class="admin-text">
								<label for="site_navigation_vertical" class="admin-text">Set PHPMailer function to send mail. Default isMail() uses PHP mail() function </label><br />
								<select name="site_mail_method" id="site_mail_method">
									<option value="0" <?php if($site['site_mail_method'] == 0) {echo ' selected';} ?>>[ none ]</option>
									<option value="1" <?php if($site['site_mail_method'] == 1) {echo ' selected';} ?>>isMail()</option>
									<option value="2" <?php if($site['site_mail_method'] == 2) {echo ' selected';} ?>>isSendmail()</option>
									<option value="3" <?php if($site['site_mail_method'] == 3) {echo ' selected';} ?>>isQmail()</option>
									<option value="4" <?php if($site['site_mail_method'] == 4) {echo ' selected';} ?>>isSMTP()</option>
								</select>
								<i> set PHPMailer mail method</i>
							</p>


						
							<div class="admin-panel">
						
							<h4 class="admin_heading">SMTP settings</h4>
							These settings only applies if PHPMailer function is set to SMTP
						
							<?php
							get_input_text("site_smtp_server", "SMTP server", "admin-text", "width:300px;", "100", $site['site_smtp_server'], "");
							get_input_text("site_smtp_port", "SMTP port", "admin-text", "width:30px;", "5", $site['site_smtp_port'], "");
							get_input_text("site_smtp_username", "SMTP username", "admin-text", "width:300px;", "100", $site['site_smtp_username'], "");
							get_input_text("site_smtp_password", "SMTP password", "admin-text", "width:300px;", "100", $site['site_smtp_password'], "");
							?>
							<p class="admin-text">
								<label for="site_smtp_authentication" class="admin-text">SMTP authentication</label><br />
								<input type="checkbox" name="site_smtp_authentication" id="site_smtp_authentication" title="Enable SMTP authentication" value="1" <?php if($site['site_smtp_authentication']==1){echo ' checked="checked"';}?>" />
								<i> enable SMTP authentication</i>
							</p>
							<p class="admin-text">
								<label for="site_smtp_debug" class="admin-text">SMTP debug level</label><br />
								<input type="checkbox" name="site_smtp_debug" id="site_smtp_debug" title="Enable SMTP debug" value="1" <?php if($site['site_smtp_debug']==1){echo ' checked="checked"';}?>" />
								<i> enable SMTP debug</i>
							</p>
							
							</div>
							
						</div>
						
						<div class="admin-panel">
							<h3 class="admin_heading">Check mail settings</h3>

							<p class="admin-text">
								<label for="smtp_email" class="admin-text">Send 'Hello World' email to:</label><br />
								<input type="text" name="smtp_email_to" style="width:300px;" id="smtp_email_to">
								<span id="ajax_spinner_site_smtp_mail" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;								
								<div class="clearfix"><span class="toolbar"><button id="btn_site_mail" style="float:left;margin:0px;">Send test mail</button></span></div>
								
								<p><span id="ajax_status_site_mail" style='display:none'></span></p>
							</p>
							
						</div>
						
						
						<?php
					break;
				
				
				
				case 'account':
					?>
					
					<div class="admin-panel">
						<div style="float:right">				
							<span id="ajax_spinner_site_account_settings" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
							<span id="ajax_status_site_account_settings" style='display:none'></span>&nbsp;
							<span class="toolbar_save"><button id="btn_site_account_settings" style="float:right;margin:0px;">Save</button></span>
						</div>
						<h3 class="admin_heading">Handle account registration</h3>

						<p class="admin-text">
							<label for="site_account_registration" class="admin-text">Site account registration</label><br />
							<input type="checkbox" name="site_account_registration" id="site_account_registration" title="Allow anonymous account registration" value="1" <?php if($site['site_account_registration'] == 1){echo ' checked="checked"';}?> />
							<i> allow anonymous account registration</i/>
						</p>
						<p class="admin-text">
							<label for="site_account_welcome_message" class="admin-text">Site account welcome message</label>
							<br />
							<textarea name="site_account_welcome_message" id="site_account_welcome_message" title="Enter site account welcome message" class="admin-text" style="width:90%;height:100px;"><?php echo $site['site_account_welcome_message'];?></textarea>
						</p>
					
						<?php
						get_input_text("site_groups_default_id", "Site default group", "admin-text", "width:100px;", "11", $site['site_groups_default_id'], "default user group");
						?>
						
					</div>
				
					<?php
					break;

				
				case 'history':

					echo '<div class="admin-panel clearfix">';
				
					$this_url = $_SERVER['PHP_SELF'] .'?t='. $t .'&tg='. $_GET['tg'];

					//echo '<div style="float:right;padding:5px;">';
					echo '<div class="sub-tabs ui-tabs ui-widget ui-corner-all" style="float:right;">';
					get_tab_menu_jquery_ui_look_alike($this_url, array("site_edit ","smtp_email"), array("Site edit","Smtp email"), "th", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
					echo '</div>';	
	
					$th = (isset($_GET['th'])) ? $_GET['th'] : null;
					
					switch($th) {	
						case 'site_edit':
							echo '<div class="clearfix">';
							include 'includes/inc.site_history_find.php';
							echo '</div>';
						break;
						
						case 'smtp_email':
							echo '<div class="clearfix">';
							include 'includes/inc.site_history_email_find.php';
							echo '</div>';
						break;
					}
			
					echo '</div>';

					
				break;
				
				case 'log':

					include 'includes/inc.site_log.php';

				break;

				
				default:

					echo '<p>';
						echo 'Site...';
					echo '</p>';
					
				break;
			
				}
				
			echo '</div>';
		echo '</div>';

		break;
	

	case 'users':

		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-users"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Users</h1></a>';
				echo '</div>';
			echo '</div>';

			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("add","search","role","group","?"), array("Add user","Find user","Find by role","Find by group","?"), "tab", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';
		
		if(!get_role_CMS('administrator') == 1) {
			die;
		}

		$tab = (isset($_GET['tab'])) ? $_GET['tab'] : null;

		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';
		
			switch($tab) {
				case 'add':
					include 'includes/inc.users_add.php';			
				break;
				case 'search':
				case 'role':
				case 'group':
					include 'includes/inc.users_find.php';
				break;
				
				default:
					?>
					<p>
						Users...
					</p>
					
					<?php
				
				break;
			}
			echo '</div>';
		echo '</div>';
		
	
		break;


	case 'files':

		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-files"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Files</h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("browse","edit_css","edit_files","filemanager","?"), array("Browse directory","Edit css","Edit files","CMS Filemanager","?"), "tf", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';

		
		if(!get_role_CMS('administrator') == 1) {
			die;
		}

		$tf = (isset($_GET['tf'])) ? $_GET['tf'] : null;

		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';
			
			switch($tf) {		
				case 'browse':
					include 'includes/inc.files.php';		
				break;		

				case 'edit_css':
					include 'includes/inc.edit_files_css.php';
				break;		

				case 'edit_files':
					include 'includes/inc.edit_files.php';
				break;		
				
				case 'filemanager':
					include_once_customfile('includes/inc.filemanager.php', $arr='', $languages);
				break;		

				default:
					?>
					<p>
						View CMS files, edit css theme.
					</p>					
					<?php
				
				break;
			}
			
			echo '</div>';
		echo '</div>';
	
		break;

		
	case 'groups':

		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-groups"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Groups</h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("group","group_default","?"), array("Edit group and user membership","Edit default group selections","?"), "tgr", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';
		
		if(!get_role_CMS('administrator') == 1) {
			die;
		}		
		
		$tgr = (isset($_GET['tgr'])) ? $_GET['tgr'] : null;

		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';
			
			switch($tgr) {		
				case 'group':
					include 'includes/inc.groups_find.php';		
				break;		
				
				case 'group_default':
					include 'includes/inc.groups_default_find.php';
				break;
					

				default:
					?>
					<p>
						Groups...
					</p>
					<?php
				break;
			}

			echo '</div>';
		echo '</div>';
		
		break;


	case 'pages':

		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-pages"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Pages</h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("add","find","sitetree","categories","tags","?"), array("Add page","Find page","Sitetree","Category","Tags","?"), "tp", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';

		
		if(!get_role_CMS('contributor') == 1) {
			die;
		}
		
		
		$tp = (isset($_GET['tp'])) ? $_GET['tp'] : null;

		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';
			
			switch($tp) {
			
				case 'add':
					include 'includes/inc.pages_add.php';
					break;
			
				case 'find':
					include 'includes/inc.pages_find.php';
					break;
				case 'sitetree':
					include 'includes/inc.pages_sitetree.php';
					break;
				case 'categories':
					include 'includes/inc.pages_categories.php';
					break;
				case 'tags':
					include 'includes/inc.tags.php';
					break;
				default:
					?>
					<p>
						Pages are designed through templates and markup language. 
						They are also containers for selections, widgets and more. 
						A minor version of a page is called a story. These stories can be pushed to other pages. 
						A page is connected (default) to a hierarchy tree.
					</p>
					<img src="css/images/template_sidebars.png" style="padding-left:20px;" />
					<?php
				break;
			}

			echo '</div>';
		echo '</div>';

		break;
		

	case 'widgets':
	
		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-widgets"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Widgets</h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("available","?"), array("widgets available","?"), "tw", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';

		
		if(!get_role_CMS('administrator') == 1) {
			die;
		}
		
		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';

			$tw = (isset($_GET['tw'])) ? $_GET['tw'] : null;
			
			switch($tw) {
			
				case 'available':
					echo '<div class="admin-panel">';
					include 'includes/inc.widgets_find.php';		
					echo '</div>';
				break;

				default:
					?>
					<p>
						Widgets are class-based applications with some functionality. One widget can have multiple instances on the same page. Use template class to create new widgets (folder /cms/widgets).
					</p>
					<img src="css/images/template_widgets.png" style="padding-left:20px;" />
					<?php
				break;
				
			}

			
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$('.btn_widgets_handle').click(function(event){
						event.preventDefault();
						// get filename from button value
						var btn = this.id;
						var widgets_class = btn;
						var action = "widgets_install";
						var token = $("#token").val();
						$.ajax({
							type: 'POST',
							url: 'widgets_ajax.php',
							data: "action=" + action + "&token=" + token + "&widgets_class=" + widgets_class,
							success: function(newdata){				
								location.reload(true);
							},
						});
					});
					$('.btn_widgets_activate').click(function(event){
						event.preventDefault();
						// get filename from button value
						var btn = this.id;
						var widgets_id = btn;
						var action = "widgets_activate";
						var widgets_active = $(this).val();
						var token = $("#token").val();
						$.ajax({
							type: 'POST',
							url: 'widgets_ajax.php',
							data: "action=" + action + "&token=" + token + "&widgets_id=" + widgets_id + "&widgets_active=" + widgets_active,
							success: function(newdata){				
								location.reload(true);
							},
						});
					});
				});
			</script>
			
			<?php
			
			echo '</div>';
		echo '</div>';

		break;
	

		
	case 'plugins':

		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-plugins"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Plugins</h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("available","?"), array("plugins available","?"), "tpl", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';

		if(!get_role_CMS('administrator') == 1) {
			if(!get_role_LMS('administrator') == 1) {
				die;
			}
		}
				
		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';

			$tpl = (isset($_GET['tpl'])) ? $_GET['tpl'] : null;
			
			switch($tpl) {
			
				case 'available':
					echo '<div id="info_plugins" title="Plugins" style="max-height:600px;overflow:auto;display:none;"></div>';
					echo '<div id="dialog_plugins" title="Plugins" style="max-height:600px;overflow:auto;"></div>';
					echo '<div class="admin-panel">';
					include 'includes/inc.plugins_find.php';		
					echo '</div>';
				break;

				default:
					?>
					<p>
						Plugins are class-based applications with some functionality. Template replaces areas. Use template class to create new plugins (folder /cms/plugins)
					</p>
					<img src="css/images/template_plugins.png" style="padding-left:20px;" />
					<?php
				break;
				
			}
			
			
			?>
			<script type="text/javascript">
				$(document).ready(function() {
				
					$("#dialog_plugins").dialog({
						autoOpen: false,
						modal: true
					});		
				
					$('.btn_plugins_install').click(function(event){
						event.preventDefault();
						// get filename from button value
						var btn = this.id;
						var plugins_class = btn;
						var action = "plugins_install";
						var token = $("#token").val();
						$.ajax({
							type: 'POST',
							url: 'plugins_ajax.php',
							data: "action=" + action + "&token=" + token + "&plugins_class=" + plugins_class,
							success: function(newdata){	
								location.reload(true);
							},
						});
					});
					$('.btn_plugins_activate').click(function(event){
						event.preventDefault();
						// get filename from button value
						var btn = this.id;
						var plugins_id = btn;
						var action = "plugins_activate";
						var plugins_active = $(this).val();
						var token = $("#token").val();
						$.ajax({
							type: 'POST',
							url: 'plugins_ajax.php',
							data: "action=" + action + "&token=" + token + "&plugins_id=" + plugins_id + "&plugins_active=" + plugins_active,
							success: function(newdata){	
								//location.reload(true);
							},
						});
					});

					$('.btn_plugins_update').click(function(event){
						event.preventDefault();
						// get filename from button value
						var btn = this.id;
						var plugin = btn;
						var action = "plugins_update";
						var plugins_active = $(this).val();
						var token = $("#token").val();
						$.ajax({
							type: 'POST',
							url: 'plugins_ajax.php',
							data: "action=" + action + "&token=" + token + "&plugin=" + plugin,
							success: function(newdata){	
								$( "#info_plugins" ).html(newdata).show();
							},
						});
					});

				});
								
			</script>
			
			<?php
			echo '</div>';	
		echo '</div>';
	
		break;

		
		
	case 'selections':

		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-selections"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Selections</h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("add","find","position","?"), array("Add selection","Find selection","Set selection priority","?"), "ts", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';

		
		if(!get_role_CMS('administrator') == 1) {
			die;
		}

		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';
		
			$ts = (isset($_GET['ts'])) ? $_GET['ts'] : null;
			
			switch($ts) {		
				case 'add':
					include 'includes/inc.pages_selections_add.php';
				break;
				case 'find':
					include 'includes/inc.pages_selections_find.php';
				break;
				case 'position':
					include 'includes/inc.pages_selections_position.php';
				break;		
				default:
					?>
					<p>
						Selections can be set to top, middle or bottom of the page. Use code field to show an offset image (outside main page).
					</p>
					<img src="css/images/template_selection.png" style="padding-left:20px;" />
					<?php
				break;
			}

			echo '</div>';
		echo '</div>';

		break;


	case 'calendars':
	
		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-calendars"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Calendars</h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("add_category","find_category","add_view","find_view","?"), array("Add category","Find category","Add view","Find view","?"), "tc", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';
		
		
		if(!get_role_CMS('administrator') == 1) {
			die;
		}
		
		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';
		
			$tb = (isset($_GET['tc'])) ? $_GET['tc'] : null;
			
			switch($tb) {		
				case 'add_view':
					include 'includes/inc.calendar_views_add.php';
				break;		
				
				case 'find_view':
					include 'includes/inc.calendar_views_find.php';
				break;
				
				case 'add_category':
					include 'includes/inc.calendar_categories_add.php';
				break;
				
				case 'find_category':
					include 'includes/inc.calendar_categories_find.php';
				break;
			
				default:		
					?>
					<p>
						Calendar categories are grouped in views. Each category can have users and groups rights for read and edit.
					</p>
					<div style="padding-left:20px;" />
					<?php
					$cal = new Calendar();
					echo $cal->getCalendarNavigation($date=null, $href="#", $max_width=false);
					?>
					</div>
					<?php
				break;
			}
		
		
			echo '</div>';
		echo '</div>';

		break;

		
	case "dashboard":
	
	default:

		echo '<div class="admin-heading">';
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-dashboard"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Dashboard </h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("?"), array("?"), "", "", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';
		
		if(get_role_CMS('user') == 1) {
			include 'includes/inc.dashboard.php';
		}
		
	break;


	case 'updates':
	
		echo '<div class="admin-heading">';	
			echo '<div class="float">';
				echo '<div class="cms-ui-icons cms-ui-updates"></div>';
				echo '<div class="float">';
					echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Updates</h1></a>';
				echo '</div>';
			echo '</div>';
			echo '<div class="sub-tabs ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
				get_tab_menu_jquery_ui_look_alike($this_url, array("?"), array("?"), "", "", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
			echo '</div>';	
		echo '</div>';

		
		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';

			echo '<a href="'.CMS_DIR.'/cms/install/update.php">Update Storiesaround CMS</a>';
			echo '</div>';	
		echo '</div>';
		

	break;



	
}
?>

<input type="hidden" id="users_id" name="users_id" value="<?php echo $_SESSION['users_id']; ?>">
<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />

<?php 
// load javascript files
foreach ( $js_files as $js ) { 
	echo "\n".'<script src="'.$js.'"></script>';
} 
?>

<div class="footer-wrapper">
	<?php include_once 'includes/inc.footer_cms.php'; ?>
</div>

</body>
</html>