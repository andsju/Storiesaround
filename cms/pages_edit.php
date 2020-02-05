<?php

// includes
require_once 'includes/inc.core.php';
require_once 'includes/inc.functions_pages.php';
require_once 'includes/inc.session_access.php';

// access right, minimum, hierarchy matters
if(!get_role_CMS('contributor') == 1) {
	header('Location: '. $_SESSION['site_domain_url']);	exit;
}

//print_r2($_SESSION);
// check $_GET id
$id = array_key_exists('id', $_GET) ? $_GET['id'] : null;
if($id == null) { die;}
$_SESSION['pages_id'] = $id;

//check pages rights for users with role_CMS author & contributor
if($_SESSION['role_CMS'] <= 2) {	
	$acc_edit = false;
	$pages_rights = new PagesRights();
	$users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;
	$users_rights = $pages_rights->getPagesUsersRights($id, $users_id);
	$groups_rights = $pages_rights->getPagesGroupsRights($id);
		
	if($users_rights) {
		if($users_rights['rights_edit'] == 1) {
			$acc_edit = true;
		}
	} else {
		if($groups_rights) {												
			if(get_membership_rights('rights_edit', $_SESSION['membership'], $groups_rights)) {
				$acc_edit = true;
			}
		}
	}
	if(!$acc_edit) { die; }	
}

// initiate class
$pages = new Pages();
$arr = $pages->getPagesEditContent($id);

// wysiwyg editor
$wysiwyg_editor = isset($_SESSION['site_wysiwyg']) ? get_editor_settings($editors, $_SESSION['site_wysiwyg']) :  null;

// css files
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css'
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

// add css theme
$theme = isset($_SESSION['site_theme']) ? $_SESSION['site_theme'] : '';
if(file_exists(CMS_ABSPATH .'/content/themes/'.$theme.'/style.css')) {
	array_push($css_files, CMS_DIR.'/content/themes/'.$theme.'/style.css');
}
// apply edit style
array_push($css_files, CMS_DIR.'/cms/css/pages_edit.css');
 

// javascript files
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js',
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-cycle/jquery.cycle2.min.js', 
	CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js',	
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/js/functions.js'
);

// javascript files... add wysiwyg file
if (is_array($wysiwyg_editor)) {
	if(file_exists(CMS_ABSPATH .'/cms/libraries/'.$wysiwyg_editor['include_js_file'])) {
		array_push($js_files, CMS_DIR.'/cms/libraries/'.$wysiwyg_editor['include_js_file']);
	}
}

// include header
$page_title = (isset($arr['title'])) ? $arr['title'] : "default page";
$page_title = 'Editing: ' .$page_title;
$body_style = "max-width:100% !important;background-color: #e8e8e8; ";
include_once 'includes/inc.header_minimal.php';

// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>


<script>

	set_jquery_ui_touch_punch();
	
	jQuery.fn.log = function (msg) {
	  console.log("%s: %o", msg, this);
	  return this;
	};
	
	$(document).ready(function() {
			
		$(".colorbox_edit").colorbox({
			width:"100%", 
			height:"100%", 
			iframe:true,
			onClosed:function(){ 
				load_images();
			}
		});
		
		$(".colorbox_images").colorbox({
			width:"640px", 
			height:"300px", 
			iframe:true,
			onClosed:function(){ 
				load_images();
			}
		});
		
		$(".colorbox_edit_story").colorbox({
			width:"50%", 
			height:"50%", 
			iframe:true, 
			onClosed:function(){ 
			}
		});

		$(".colorbox_grid_class").colorbox({
			width:"50%", 
			height:"50%", 
			iframe:true, 
			onClosed:function(){
			}
		});

		$("#tabs_edit").show(); 		
		
		$( ".toolbar_reload button" ).button({			
			icons: {
				secondary: "ui-icon-refresh"
			},
			text: true
		});

		$( ".toolbar_gear button" ).button({
			icons: {
				secondary: "ui-icon-gear"
			},
			text: true
		});

		$( ".toolbar_preview button" ).button({
			icons: {
				secondary: "ui-icon-newwin"
			},
			text: true
		});
		
		$( ".toolbar_close button" ).button({
			icons: {
				secondary: "ui-icon-close"
			},
			text: true
		});

		$( ".toolbar_save_images button" ).button({
			icons: {
			},
			text: true
		});
	
		$( ".toolbar_widgets_new button" ).button({
			icons: {
				primary: "ui-icon-carat-1-w",
				secondary: "ui-icon-carat-1-e"
			},
			text: true
		});
		
		$( ".toolbar_edit button" ).button({
			icons: {
				primary: "ui-icon-grip-dotted-vertical",
				secondary: "ui-icon-pencil"
			},
			text: true
		});

		$( ".toolbar_widgets_edit button" ).button({
			icons: {
				secondary: "ui-icon-pencil"
			},
			text: false
		});
		
		$( ".toolbar button" ).button({
		});

		$( ".toolbar_publish button" ).button({
		  icons: {
				secondary: "ui-icon-power"
			},
		});

		$( ".toolbar_help button" ).button({
		  icons: {
				secondary: "ui-icon-help"
			},
			text: false
		});

		$( ".toolbar_trash button" ).button({
		  icons: {
				secondary: "ui-icon-trash"
			},
		});

		$( ".toolbar_add button" ).button({
			icons: {
				secondary: "ui-icon-plus"
			},
			text: true
		});	
		
		$( ".toolbar_refresh button" ).button({
		  icons: {
				secondary: "ui-icon-refresh"
			},
			text: true
		});
				
		$( ".column_selected_stories" ).sortable({
			connectWith: ".column_selected_stories",
			items: "div:not(.ui-state-disabled)"
		});

		$( ".column_selected_stories" ).disableSelection();
				
		$( ".column_widgets_sidebar" ).sortable({
			connectWith: ".column_widgets_sidebar",
			items: "div:not(.ui-state-disabled)"
		});
		
		$( ".column_widgets_content" ).sortable({
			connectWith: ".column_widgets_content",
			items: "div:not(.ui-state-disabled)"
		});
		
		$( "#pending_widgets" ).sortable({
		});

		$( "#directory_view_slides" ).sortable({axis: 'y', forceHelperSize: true, forcePlaceholderSize: true, opacity: 0.1});
		$( "#directory_view_slides" ).disableSelection();

		$( ".toolbar_delete button" ).button({
			icons: {
				primary: "ui-icon-trash"
			},
			text: false
		});
		
		$('#link_title_alternative').click(function(){
			$('#title_settings').toggle();
		});

		$('#link_stories_settings').click(function(){
			$('#stories_settings').toggle();
		});

		$('#link_stories_childpages').click(function(){
			$('#stories_childpages').toggle();
		});

		$('#link_stories_promoted').click(function(){
			$('#stories_promoted').toggle();
		});

		$('#link_stories_event_dates').click(function(){
			$('#stories_event_dates').toggle();
		});

		$('#link_stories_select').click(function(){
			$('#stories_select').toggle();
		});

		$( "#accordion_add_content" ).accordion({
			heightStyle: "content",
			collapsible: true,
			animate: 50,
			active: false
		});
		
		$("#dialog_delete_page").dialog({
			autoOpen: false,
			modal: true
		});		
		
		$("#dialog_token").dialog({
			autoOpen: false,
			modal: true
		});		

		$("#dialog_delete_image").dialog({
			autoOpen: false,
			modal: true
		});		

		$("#dialog_delete_item").dialog({
			autoOpen: false,
			modal: true
		});		

		$( "#search_story" ).autocomplete({
			delay: 300,
			source: function( request, response ) {
				$.ajax({
					type: "post",
					url: "pages_ajax.php",
					dataType: "json",
					data: {
						action: "pages_search",
						token: $("#token").val(),
						s: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
								
								var status = '';
								if(item.status==1) {
									status = ' (draft)';
								} else if (item.status==2) {
									status = ' (published)';
								} else {
									status = '';
								}
							
							return {
								label: item.title + ' (id: '+item.pages_id+')' + status,
								id: item.pages_id,
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				$("input#pid").val(ui.item.id)
			}
		});
				
		$("#sortable_pages ul").sortable({
			placeholder: "ui-state-highlight",
			axis: 'y',
			opacity: 0.6, 
			cursor: 'move', 
			update: function() {
				var token = $("#token").val();
				var pages_id = $("#pages_id").val();
				var order = $(this).sortable("serialize") + "&action=update_pages_position&token=" + token + "&pages_id=" + pages_id;
					$.post("pages_edit_ajax.php", order, function(message){
					ajaxReply(message,'#ajax_result_move');
				});
			}				
		});
		$( "#sortable_pages" ).disableSelection();
		
		$('.content_save').click(function(event){
			event.preventDefault();
			save();
		});

		// grid
		$('.add-grid-item').click(function(event){
			event.preventDefault();
			var pages_id = $("#pages_id").val();			
			var cell = "";
			var form = "";
			var html = "";
			
			var dynamic = "<div class=\"grid-settings dynamic\"><p>Dynamic content</p>";
			var dynamicSelectList = getSelectStrings([["none", "none"], ["story-selected", "Selected story"], ["story-promoted", "Promoted story"], ["calendar-event", "Calendar event"]], "", "grid-dynamic-content", "", "");
			dynamic += dynamicSelectList;
			dynamic += "<div class=\"grid-settings\"><p>Dynamic filter: string or id</p><input type=\"text\" name=\"grid-dynamic-content-filter\" maxlength=\"25\"></div>"; 
			dynamic += "<div class=\"grid-settings\"><p>Dynamic match: get position (single) get number (mulitple)</p>";
			var limitSelectList = getSelectNumber([0,1,2,3,4,5], 1, "grid-dynamic-content-limit", "", "");
			dynamic += limitSelectList + "</div>";
			dynamic += "<div class=\"grid-settings\"><p>Dynamic default content (no match)</p><textarea class=\"tinymce-grid\" name=\"grid-dynamic-content-default\"></textarea></div>";

			cell += "<div class=\"grid-cell gridedit\" style=\"position:relative\">";
			var tool = "<div class=\"grid-tools\">\
			<i class=\"far fa-save\" aria-hidden=\"true\"></i><br>\
			<i class=\"far fa-edit\" aria-hidden=\"true\"></i><br>\
			<i class=\"fas fa-arrow-left\" aria-hidden=\"true\"></i>\
			<br><i class=\"fas fa-arrow-right\" aria-hidden=\"true\"></i>\
			<br><i class=\"far fa-trash-alt\"></i>\
			</div>";
			cell += tool;

			cell += "<span class=\"grid-label\"></span>";

			var adjustSelectList = getSelectNumber([0,10,20,30,40,50,60,70,80,90,100], 0, "grid-image-y", "", "");
			cell += "<div class=\"grid-image-crop hidden\"></div>\
			<div class=\"grid-video hidden\"></div>\
			<h3 class=\"grid-heading hidden\"></h3>\
			<div class=\"grid-content hidden\"></div>\
			<div class=\"grid-dynamic hidden\"></div>\
			<div class=\"grid-link hidden\"></div>";
			
			form += "<div class=\"grid-form hidden\">\
			<div class=\"grid-settings\"><p>Image<br><input type=\"text\" name=\"grid-image\" maxlength=\"255\"></p></div>\
			<div class=\"grid-settings\"><p>Adjust image (background-position-y %): "+adjustSelectList+"</p></div>\
			<div class=\"grid-settings\"><p>Heading<br><input type=\"text\" name=\"heading\" maxlength=\"100\"></p></div>\
			<div class=\"grid-settings\"><p>Video<br><input type=\"text\" name=\"video\" maxlength=\"100\"></p></div>\
			<div class=\"grid-settings\"><p>URL<br><input type=\"text\" name=\"url\" maxlength=\"255\"></p></div>\
			<div class=\"grid-settings\"><p>Link title<br><input type=\"text\" name=\"link\" maxlength=\"50\"></p></div>\
			<div class=\"grid-settings\"><p>Content<br><textarea class=\"tinymce-grid\" name=\"grid-content\"></textarea></p></div>\
			<div class=\"grid-settings\"><p>Custom css class<br><input type=\"text\" name=\"css\" maxlength=\"100\"></p></div>\
			<div class=\"grid-settings\"><p>Label<br><input type=\"text\" name=\"label\" maxlength=\"25\"></p></div>\
			<div class=\"grid-settings\"><p>Dynamic content</p>"+dynamic+"</div>";
			html += cell + form + "</div>";

			$("div#wrapper-grid").append(html);

			activateEditor("tinymce");
		});
		
		$("div#wrapper-grid").delegate( "div.grid-tools .fa-trash-alt", "click", function(event) {
			event.preventDefault();
			var that = $(this); 
			$("#dialog_delete_item").dialog("open");
			$("#dialog_delete_item").dialog({
				buttons : {
				"Confirm" : function() {
					$(this).dialog("close");
					that.parent().parent().remove();
					equalheight('div.grid-cell');
				},
				"Cancel" : function() {
					$(this).dialog("close");
					}
				}
			});
		});

		$("div#wrapper-grid").delegate( "div.grid-tools .fa-edit", "click", function(event) {
			event.preventDefault();
			var el = $(this).parent().parent().children();
			$(this).parent().parent().children("div.grid-form").show();
			activateEditor("tinymce");
			equalheight('div.grid-cell');
			setTimeout(function() {
				equalheight('div.grid-cell');
			}, 1000);
		});

		$("div#wrapper-grid").delegate( "div.grid-tools .fa-arrow-left", "click", function(event) {
			event.preventDefault();
			var item = $(this).parent().parent();
			var prev_item = item.prev();
			item.insertBefore(item.prev());
			equalheight('div.grid-cell');
		});

		$("div#wrapper-grid").delegate( "div.grid-tools .fa-arrow-right", "click", function(event) {
			event.preventDefault();
			var item = $(this).parent().parent();
			var next_item = item.next();
			item.insertAfter(item.next());
			equalheight('div.grid-cell');
		});
		
		$("div#wrapper-grid").delegate( "a.toggle", "click", function(event) {
			event.preventDefault();
			$(this).parent().parent().find("div.dynamic").toggle();			
			equalheight('div.grid-cell');
		});
		
		$("div#wrapper-grid").delegate( ".fa-save", "click", function(event) {
			event.preventDefault();
			var form = $(this).parent().parent().children("div.grid-form");
			var image, heading, video, url, url1, url2, link, css, pages_id;
			image = form.find("input[name=grid-image]")[0].value;
			heading = form.find("input[name=heading]")[0].value;
			video = form.find("input[name=video]")[0].value;
			url = form.find("input[name=url]")[0].value;
			link = form.find("input[name=link]")[0].value;
			css = form.find("input[name=css]")[0].value;
			label = form.find("input[name=label]")[0].value;
			pages_id = $("#pages_id").val();
			image_position_y = form.find("select[name=grid-image-y]").val();

			$(form.find("span.reply_fail")).each( function(i) {
				$(this).remove();
			});
			
			tinyMCE.triggerSave();

			var content = form.find("textarea").val();
			if (content.length > 5000) {
				var words = $(content).text().split(' ').length;
				content = "";
			}

			var cell = $(this).parent().parent();
			cell.attr("class", "grid-cell");
			cell.addClass(css);
			var design = cell.find("div.grid-design");
			design.addClass(css);

			var imagePreview = $(this).parent().parent().find("div.grid-image-crop");
			imagePreview.css("background-image", "url("+image+")");
			if(image.length) {
				imagePreview.removeClass("hidden");			
			}
			var headingPreview = $(this).parent().parent().find("h3.grid-heading");
			headingPreview.text(heading);
			if(heading.length) {
				headingPreview.removeClass("hidden");	
			}
			var labelPreview = $(this).parent().parent().find("span.grid-label");
			labelPreview.text(label);
			if(label.length) {
				labelPreview.removeClass("hidden");	
			}
			var videoPreview = $(this).parent().parent().find("div.grid-video");
			videoPreview.text("Click 'Save grid' button and reload page to preview video: " +video);
			if(video.length) {
				videoPreview.removeClass("hidden");		
			}
			var contentPreview = $(this).parent().parent().find("div.grid-content");
			contentPreview.html(content);
			if(content.length) {
				contentPreview.removeClass("hidden");
			}
			var urlPreview = $(this).parent().parent().find("div.grid-link");
			urlPreview.html("<a href=\""+url+"\">"+link+"</a>");
			if(link.length) {
				urlPreview.removeClass("hidden");		
			}
			// dynamic
			var dynamicContent, dynamicContentFilter, dynamicContentLimit;
			dynamicContent = form.children("div.grid-settings").find("select[name=grid-dynamic-content]").val();
			dynamicContentFilter = form.children("div.grid-settings").find("input[name='grid-dynamic-content-filter']")[0].value;
			dynamicContentLimit = form.children("div.grid-settings").find("select[name=grid-dynamic-content-limit]").val();
			dynamicContentDefault = form.children("div.grid-settings").find("select[name=grid-dynamic-content-default]").val();

			var action = "get_dynamic_stories";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var gridForm = $(this).closest("div.grid-cell");

			$.ajax({	
				beforeSend: function() { loading = $('#ajax_spinner_grid').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_grid').hide()",500)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					dynamicContent: dynamicContent, dynamicContentFilter: dynamicContentFilter, dynamicContentLimit: dynamicContentLimit,
				},
				success: function(result){
					gridForm.append("<div>Save grid and reload</div>");
					console.log(result);
					
					if (result.length) {
						$.each(JSON.parse(result), function(index, object) {
							console.log(index, object);
						});
					}
				}
			});
			
			$(this).parent().parent().children("div.grid-form").hide();
			equalheight('div.grid-cell');

			$("#btnSaveGrid").addClass("button-attention");
		});

		$("#btnGridExport").click(function(event) {
			event.preventDefault();
			var grid_content = JSON.stringify($("#gridform").serializeArray());
			grid_content = stringEscape(grid_content);
			$("#grid_json").val(grid_content).fadeIn(300);
			$("#grid_json_clipboard").fadeIn(300);
		});

		$("#grid_json_clipboard").click(function(){
			$("#grid_json").select();
			document.execCommand('copy');
		});

		$("#btnGridImport").click(function(event) {
			event.preventDefault();
			$("#grid_json").fadeIn(700);
			$("#btnGridImportSave").fadeIn(300);
		});

		$("#btnGridImportSave").click(function(event) {
			event.preventDefault();
			var grid_content = $("#grid_json").val();
			grid_content = stringEscape(grid_content);
			var action = "save_grid";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var grid_active = $('input:checkbox[name=grid_active]').is(':checked') ? 1 : 0;
			var grid_custom_classes = $("#grid_custom_classes").val();
			var grid_cell_image_height = $("#grid_cell_image_height").val();
			var grid_area = $('input:radio[name=grid_area]:checked').val();
			var grid_cell_template = $('input:radio[name=grid_cell_template]:checked').val();
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_grid').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_grid').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					grid_content: grid_content, grid_active: grid_active, grid_area: grid_area, 
					grid_cell_template: grid_cell_template, grid_custom_classes: grid_custom_classes,
					grid_cell_image_height: grid_cell_image_height
				},
				success: function(message){
					window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#setup';
					location.reload(true);
				}
			});
		});

		$("#btnSaveGrid").click(function(event) {
			event.preventDefault();
			var grid_content = JSON.stringify($("#gridform").serializeArray());
			console.log(grid_content);
			//grid_content = stringEscape(grid_content);
			var action = "save_grid";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var grid_active = $('input:checkbox[name=grid_active]').is(':checked') ? 1 : 0;
			var grid_custom_classes = $("#grid_custom_classes").val();
			var grid_cell_image_height = $("#grid_cell_image_height").val();
			var grid_area = $('input:radio[name=grid_area]:checked').val();
			var grid_cell_template = $('input:radio[name=grid_cell_template]:checked').val();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_grid').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_grid').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					grid_content: grid_content, grid_active: grid_active, grid_area: grid_area, 
					grid_cell_template: grid_cell_template, grid_custom_classes: grid_custom_classes,
					grid_cell_image_height: grid_cell_image_height
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_grid');
					$("#btnSaveGrid").removeClass("button-attention");
				},
			});
		});

		function stringEscape(s) {
			return s ? s.replace(/\\/g,'\\\\').replace(/'/g,"\\'") : s;
		}

		$("div#wrapper-grid").delegate( "select[name='grid-image-y']", "change", function(event) {
			event.preventDefault();
			var value = $(this).val();
			$(this).parent().parent().parent().find("div.grid-image-crop").css("background-position-y", value + "%");
			equalheight('div.grid-cell');
		});

		$( "#grid_image_slider_height" ).slider({
			orientation: "vertical",
			value: 100,
			min: 100,
			max: 400,
			step: 20,
			slide: function( event, ui ) {
				$( "#grid_cell_image_height" ).val( ui.value );

				$("div.grid-image-crop").each(function() {  
					$(this).css("min-height", ui.value + "px");
				});
				equalheight('div.grid_cell_image_height');
			}
    	});

		// window resize
		$(window).resize(function() {

			var $videos = $(".grid-video iframe");
			var $fluidEl = $(".grid-video");
			var newWidth = $fluidEl.width();
			$videos.each(function() {
				$(this)
				.width(newWidth)
				.height(newWidth * $(this).data('ratio'));
			});
			equalheight('div.grid-cell');
		}).resize();
		
		$('#content_loremipsum').click(function(event){
			event.preventDefault();
			var action = "loremipsum";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id
				},
				success: function(loremipsum){
					var txt = $("textarea#content");
					txt.val( txt.val() + "\n" +loremipsum);					
				},
			});
		});

		$('#btn_edit_ownership').click(function(event){
			event.preventDefault();
			var action = "edit_ownership";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id
				},
				success: function(message){	
					window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#setup';
					location.reload(true);
				},
			});
		});

		$('#link_page_template_setup').click(function(event){
			event.preventDefault();
			var action = "save_site_templates_setup";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var setup_template = $('input:radio[name=setup_template]:checked').val();
			var template_custom = $("#template_custom option:selected").val();
			$.ajax({
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					setup_template: setup_template, template_custom: template_custom 
				},
				success: function(){	
					window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#setup';
					location.reload(true);
				},
			});
		});
		
		$('#seo_link').click(function(event){
			event.preventDefault();
			var action = "seo_link";
			var pages_title = $("#pages_title").val();
			var pages_title_alternative = $("#pages_title_alternative").val();
			var stopwords = $('input:checkbox[name=stopwords]').is(':checked') ? 1 : 0;
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_seo_link').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_seo_link').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					pages_title: pages_title, pages_title_alternative: pages_title_alternative, stopwords: stopwords
				},
				success: function(data) {
					var data = /[a-z]/.test(data) == true ? data : "link-" + data;
					$('#pages_id_link').val(data);
				}
			});
		});

		$('#btn_save_seo_link').click(function(event){
			event.preventDefault();
			var action = "save_seo_link";
			var pages_id_link = $("#pages_id_link").val();
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_seo_link').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_seo_link').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					pages_id_link: pages_id_link
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_seo_link');
				},
			});
		});
		
		$('#suggest_meta_keywords').click(function(event){
			event.preventDefault();
			var action = "suggest_meta_keywords";
			var pages_title = $("#pages_title").val();
			var pages_title_alternative = $("#pages_title_alternative").val();
			var story_content = $("#story_content").val();
			var content = $("#content").val();
			var stopwords = 1;
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_meta_keywords').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_meta_keywords').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					pages_title: pages_title, pages_title_alternative: pages_title_alternative, stopwords: stopwords, story_content: story_content, content: content
				},
				success: function(message){	
					$('#meta_keywords').val(message);
				},
			});
		});
		
		$('#btn_site_header_setup').click(function(event){
			event.preventDefault();
			var action = "save_site_header_setup_image";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();

			var header_image = [];
			$("#directory_view_slides div").each(function() {
				var media = $(this).attr("data-image");
				if (media.length > 0) {
					header_image.push($(this).attr("data-image"));
				}			
				
			});

			var header_caption = [];
			$("textarea[name='header_caption[]'").each(function (){
				let captionText = $(this).val();
				captionText = captionText.replace(/</g, "&lt;").replace(/>/g, "&gt;");
				header_caption.push(captionText);
			});
			var header_caption_align = [];
			$("input[name='header_caption_align[]'").each(function (){
				let value = $(this).val();
				let cssValue;
				if (value == 0) {
					cssValue = "left";
				} else if (value == 1) {
					cssValue = "center";
				} else {
					cssValue = "right";
				}
				header_caption_align.push(cssValue);
			});

			var header_caption_vertical_align = [];
			$("input[name='header_caption_vertical_align[]'").each(function (){
				let value = $(this).val();
				let cssValue;
				if (value == 0) {
					cssValue = "top";
				} else if (value == 1) {
					cssValue = "middle";
				} else {
					cssValue = "bottom";
				}
				header_caption_vertical_align.push(cssValue);
			});
			var header_caption_show = $('input:checkbox[name=header_caption_show]').is(':checked') ? 1 : 0;
			var header_image_timeout = $("#header_image_timeout").val();
			var header_image_fade = $("#header_image_fade").val();
			var landing_page = $('input:checkbox[name=landing_page]').is(':checked') ? 1 : 0;
			var parallax_scroll = $('input:checkbox[name=parallax_scroll]').is(':checked') ? 1 : 0;

			if (header_image.length === 0) {
				return;
			}
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_header_image').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_header_image').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					header_image: header_image, header_caption: header_caption, 
					header_caption_align: header_caption_align, header_caption_vertical_align: header_caption_vertical_align, 
					header_caption_show: header_caption_show, 
					header_image_timeout: header_image_timeout, header_image_fade: header_image_fade,
					landing_page: landing_page, parallax_scroll: parallax_scroll 
				},
				success: function(newdata){
					ajaxReply('','#ajax_status_header_image');
					window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#setup';
					//location.reload(true);
				}
			});
			
		});

		$("#directory_view").delegate( ".image_mark", "click", function() {
			var filename = $(this).attr("data-file");
			var image = "";
			var video = $(this).attr("data-video");
			if (video == "false") {
				image = '<div data-image=\"'+filename+'\" style=\"background-image:url(../content/uploads/header/'+ filename + ');\" class="header-images"><textarea name="header_caption[]"></textarea><input type=\"range\" name=\"header_caption_align[]\" class=\"header_caption_align\" min=\"0\" max=\"2\" value=\"1\"><input type=\"range\" name=\"header_caption_vertical_align[]\" class=\"header_caption_vertical_align\" min=\"0\" max=\"2\" value=\"1\"></div>';
			} else {
				image = '<div data-image=\"'+filename+'\" class="header-images"><video style=\"width:100%;max-height:100px\" class="header-images" controls muted><source src="../content/uploads/header/'+ filename + '"></video><textarea name="header_caption[]"></textarea><input type=\"range\" name=\"header_caption_align[]\" class=\"header_caption_align\" min=\"0\" max=\"2\" value=\"1\"><input type=\"range\" name=\"header_caption_vertical_align[]\" class=\"header_caption_vertical_align\" min=\"0\" max=\"2\" value=\"1\"></div>';
			}
			var isFile = false;
			$("#directory_view_slides div, #directory_view_slides video").each(function(){
				img = $(this).attr("data-image");
				if(img == filename) {
					isFile = true;
				}
			});

			$("#directory_view input").each(function(){
				var checked = $(this).is(':checked') ? 1 : 0;
				var filename = $(this).attr("data-file");
				
				if(checked == 0) {
					console.log("input", filename);
					$("#directory_view_slides div, #directory_view_slides video").each(function(){
						if (filename == $(this).attr("data-image")) {
							$(this).remove();
						}
					})
				} 
			});

			if (!isFile) {
				$("#directory_view_slides").append(image);
			}

		});		

		$('#btn_site_selections').click(function(event){
			event.preventDefault();
			var action = "save_site_selections";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();					
			var selections = [];
			$('input:checkbox[name="selections[]"]:checked').each(function(index) { selections.push($(this).val());});
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_selections').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_selections').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_id=" + pages_id + "&selections=" + selections,
				/*
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					selections: selections
				},
				*/
				success: function(message){
					ajaxReply(message,'#ajax_status_selections');
				},
			});
		});

		$('#selections_filter').keyup(function(event){
			event.preventDefault();
			var filter = $("#selections_filter").val();
			filter = filter.toLowerCase();
			$("#choose_selections li").each(function(){
				var text = $(this).text(); 
				text = text.toLowerCase();
				if(text.indexOf(filter) > 0) {					
					$(this).show(); 
				} else if (filter.length == 0) {
					$(this).show(); 
				} else {
					$(this).hide(); 
				}
			});
		});
				
		$('#selections_checked').on('change', function(event) {
			event.preventDefault();			
			if (this.checked) {
				$("#choose_selections li").each(function(){
					var check = $(this).find('input:checkbox').is(':checked');
					if(check == true) {					
						$(this).show(); 
					} else {
						$(this).hide(); 
					}
				});
			} else {
				$("#choose_selections li").each(function(){
					$(this).show(); 
				});
			}
		});

		
		$('#btn_load_images').click(function(event){
			event.preventDefault();
			load_images();
		});

		$('#btn_new_images').click(function(event){
			event.preventDefault();
			var token = $("#token").val();
			var pages_id = $("#pages_id").val();
			var original = $('input:checkbox[name=original]').is(':checked') ? 1 : 0;
			var max_width = $("#max_width option:selected").val();
			$.colorbox({width:"80%", height:"80%", iframe:true, href:"pages_images_upload.php?token="+token+"&pages_id="+pages_id+"&original="+original+"&max_width="+max_width+""});
		});

		$('#btn_load_files').click(function(event){
			event.preventDefault();
			load_files();
		});
		
		$('#btn_load_plugins').click(function(event){
			event.preventDefault();
			var action = "load_plugins";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_plugins').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_plugins').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id
				},
				success: function(newdata) {
					$("#page_plugins").empty().append(newdata).hide().fadeIn('fast');
				},
			});
		});
		
		$('#btn_use_plugins').click(function(event){
			event.preventDefault();
			var action = "use_plugins";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var plugins = $('input:checkbox[name=plugins]').is(':checked') ? 1 : 0;
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_plugins').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_plugins').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id, plugins: plugins
				},
				success: function(data) {
					if(data == 1) {
						$("#show_load_plugins").show();
						$("#page_plugins").show();						
					} else {
						$("#show_load_plugins").hide();
						$("#page_plugins").hide();
					}
					ajaxReply('','#ajax_status_plugins');
				},
			});
		});

		$('#btn_plugin_arguments_save').click(function(event){
			event.preventDefault();
			var action = "plugin_arguments";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var plugin_arguments = $("#plugin_arguments").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_plugins').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_plugins').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id, plugin_arguments: plugin_arguments
				},
				success: function(data) {
					ajaxReply('','#ajax_status_plugins');
				},
			});
		});
		
		$('#btn_settings').click(function(event){
			event.preventDefault();
			var pages_id = $("#pages_id").val();
			var action = "pages_settings";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var lang = $("#lang").val();
			var search_field_area = $("#search_field_area option:selected").val();
			var breadcrumb = $('input:radio[name=breadcrumb]:checked').val();
			var category = $("#category option:selected").text();
			var category_position = $("#category option:selected").val();
			console.log("category", category);
			console.log("category_position", category_position);
			console.log("search_field_area", search_field_area);
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_settings').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_settings').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id, 
					lang: lang, search_field_area: search_field_area, category: category, category_position: category_position, breadcrumb: breadcrumb
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_html_lang');
				}
			});
		});

		$('#btn_save_images').click(function(event){
			event.preventDefault();
			
			$("#images_list").each(function(){
				var action = "save_images";
				var token = $("#token").val();
				var users_id = $("#users_id").val();
				var pages_id = $("#pages_id").val();
				var querystring = $("#images_form").serialize();
				var result = $(this).sortable("toArray");
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_images').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_images').hide()",700)},
					type: 'POST',
					url: 'pages_edit_ajax.php',
					data: querystring,
					success: function(message) {
						ajaxReply(message,'#ajax_status_images');
					},
				});
			});	
		});
		
		$(document).on('click', '#set_custom_css div', function (e) {
			var css = $( this ).data("css");
			$('#story_css_class').val(css).hide().fadeIn(500);			
		});

		$('#btn_save_stories').click(function(event){
			event.preventDefault();
			var stories_column_saved = false;
			$(".column_selected_stories").each(function(){
				var result = $(this).sortable("toArray");
				var token = $("#token").val();
				var users_id = $("#users_id").val();
				var action = "stories_save";
				var columnId = $(this).attr('id'); 
				var pages_id = $("#pages_id").val();
				var items = {  
				id: columnId,
				token: token,
				users_id: users_id,
				action: action,
				pages_id: pages_id,
				result: result  
				}; 
				if (result.length > 0){
					$.post("pages_edit_ajax.php", items, function(){
						var stories_column_saved = true;
					});
				}
			});
			if(stories_column_saved = true) {
				$('#ajax_spinner_stories').show();
				setTimeout("$('#ajax_spinner_stories').hide()",700);					
				setTimeout("$('#ajax_status_stories').hide()",3000);
				ajaxReply('saved','#ajax_status_stories');
			}
		});

		$('#btn_save_widgets').click(function(event){
			event.preventDefault();
			var widgets_saved = false;
			$(".column_widgets_sidebar").each(function(){
				var result = $(this).sortable("toArray");
				var token = $("#token").val();
				var users_id = $("#users_id").val();
				var action = "widgets_save";
				var columnId = $(this).attr('id'); 
				var pages_id = $("#pages_id").val();
				var items = {  
				id: columnId,
				token: token,
				users_id: users_id,
				action: action,
				pages_id: pages_id,
				result: result  
				}; 
				if (result.length > 0){
					$.post("pages_edit_ajax.php", items, function(){
						var widgets_saved = true;
					});
				}
			});

			$(".column_widgets_content").each(function(){
				var result = $(this).sortable("toArray");
				var token = $("#token").val();
				var users_id = $("#users_id").val();
				var action = "widgets_save";
				var columnId = $(this).attr('id'); 
				var pages_id = $("#pages_id").val();
				var items = {  
				id: columnId,
				token: token,
				users_id: users_id,
				action: action,
				pages_id: pages_id,
				result: result  
				}; 
				console.log("result", result);
				if (result.length > 0){
					$.post("pages_edit_ajax.php", items, function(){
						var widgets_saved = true;
					});
				}
			});
		
			if(widgets_saved = true) {
				
				$('#ajax_spinner_widgets').show();
				setTimeout("$('#ajax_spinner_widgets').hide()",700);
				ajaxReply('saved','#ajax_status_widgets');
				
			}
		});
				
		$('#btn_delete_stories').click(function(event){
			event.preventDefault();
			$(".column_selected_stories").each(function(){
				var result = $(this).sortable("toArray");
				var token = $("#token").val();
				var users_id = $("#users_id").val();
				var action = "stories_delete";
				var columnId = $(this).attr('id'); 
				var pages_id = $("#pages_id").val();
				var items = {  
				id: columnId,
				token: token,
				users_id: users_id,
				action: action,
				pages_id: pages_id,
				result: result  
				}; 
				if (result.length > 0){
					$.post("pages_edit_ajax.php", items, function(theResponse){
						$("#pending_stories").empty().hide().fadeIn('fast');
					});
				}
			});
		});

		$('#btn_delete_widgets').click(function(event){
			event.preventDefault();
			$("#pending_widgets").each(function(){
				var result = $(this).sortable("toArray");
				var token = $("#token").val();
				var users_id = $("#users_id").val();
				var action = "widgets_delete";
				var columnId = $(this).attr('id'); 
				var pages_id = $("#pages_id").val();
				var items = {  
				column: columnId,
				token: token,
				users_id: users_id,
				action: action,
				pages_id: pages_id,
				result: result  
				}; 
				if (result.length > 0){
					$.post("pages_edit_ajax.php", items, function(theResponse){
						$("#ajax_result").html(theResponse);
						$("#pending_widgets").empty().hide().fadeIn('fast');				
					});
				}
			});
		});
		
		$('#btn_stories_child').click(function(event){
			event.preventDefault();
			var action = "save_stories_child";
			var stories_child = $('input:checkbox[name=stories_child]').is(':checked') ? 1 : 0;
			var stories_child_area = $('#stories_child_area').val();
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_stories_child').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_stories_child').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					stories_child: stories_child, stories_child_area: stories_child_area
				},
				success: function(message){
					ajaxReply('','#ajax_status_stories_child');
					$("#container_stories_child").empty().append(message).hide().fadeIn('fast');											
				},
			});
		});
		
		$('#btn_stories_settings').click(function(event){
			event.preventDefault();
			var action = "save_stories_settings";
			var stories_equal_height = $('input:checkbox[name=stories_equal_height]').is(':checked') ? 1 : 0;
			var stories_last_modified = $('input:checkbox[name=stories_last_modified]').is(':checked') ? 1 : 0;
			var stories_image_copyright = $('input:checkbox[name=stories_image_copyright]').is(':checked') ? 1 : 0;
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var stories_wide_teaser_image_width = $("#stories_wide_teaser_image_width").val();
			var stories_wide_teaser_image_align = $('input:radio[name=stories_wide_teaser_image_align]:checked').val();
			var stories_css_class = $("#stories_css_class").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_stories_modified').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_stories_modified').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id, stories_equal_height: stories_equal_height,
					stories_wide_teaser_image_width: stories_wide_teaser_image_width, stories_wide_teaser_image_align: stories_wide_teaser_image_align,
					stories_last_modified: stories_last_modified, stories_image_copyright: stories_image_copyright, stories_css_class: stories_css_class
				},
				success: function(message){
					ajaxReply('','#ajax_status_stories_settings');
				},
			});
		});
		
		$('#btn_stories_promoted').click(function(event){
			event.preventDefault();
			var action = "save_stories_promoted";
			var stories_promoted = $('input:checkbox[name=stories_promoted]').is(':checked') ? 1 : 0;
			var stories_promoted_area = $("#stories_promoted_area option:selected").val();
			var stories_filter = $("#stories_filter").val();
			var stories_limit = $("#stories_limit").val();
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_stories_promoted').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_stories_promoted').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					stories_promoted: stories_promoted, stories_promoted_area: stories_promoted_area, stories_filter: stories_filter, stories_limit: stories_limit
				},
				success: function(message){	
					ajaxReply('','#ajax_status_stories_promoted');
					$("#container_stories_promoted_left_sidebar").empty();
					$("#container_stories_promoted_right_sidebar").empty();
					$("#container_stories_promoted_content").empty();
					
					if(stories_promoted == 1 && stories_promoted_area == 1) {
						$("#container_stories_promoted_left_sidebar").empty().append(message).hide().fadeIn('fast');
					}
					if(stories_promoted == 1 && stories_promoted_area == 2) {
						$("#container_stories_promoted_right_sidebar").empty().append(message).hide().fadeIn('fast');
					}
					if(stories_promoted == 1 && stories_promoted_area == 3) {
						$("#container_stories_promoted_content").empty().append(message).hide().fadeIn('fast');
					}
					if(stories_promoted == 1 && stories_promoted_area == 4) {
						$("#container_stories_promoted_content").empty().append(message).hide().fadeIn('fast');
					}
					if(stories_promoted == 1 && stories_promoted_area == 5) {
						$("#container_stories_promoted_content").empty().append(message).hide().fadeIn('fast');
					}
				},
			});
		});

		$('#btn_stories_event_dates').click(function(event){
			event.preventDefault();
			var action = "save_stories_event_dates";
			var stories_event_dates = $('input:checkbox[name=stories_event_dates]').is(':checked') ? 1 : 0;
			var stories_event_dates_filter = $("#stories_event_dates_filter").val();
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_stories_event_dates').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_stories_event_dates').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					stories_event_dates: stories_event_dates, stories_event_dates_filter: stories_event_dates_filter
				},
				success: function(data){	
					ajaxReply('','#ajax_status_stories_event_dates');
					
					$("#container_stories_events").empty().append(data).hide().fadeIn('fast');					
				},
			});
		});
		
		$('#btn_new_pages_search_stories_id').click(function(event){
			event.preventDefault();
			var action = "stories_new";
			var container = "main";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var stories_id = $("input#pid").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_stories_select').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_stories_select').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					stories_id: stories_id, container: container
				},
				success: function(newdata){	
					$("#main").prepend(newdata).hide().fadeIn('fast');
					$("#search_story").val('');
					ajaxReply('','#ajax_status_stories_select');
				},
			});
		});		

		$('#btn_change_cols').click(function(event){
			event.preventDefault();			
			var action = "stories_change_cols";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var stories_selected = $('input:checkbox[name=stories_selected]').is(':checked') ? 1 : 0;
			var stories_columns = $('input:checkbox[name=stories_columns]').is(':checked') ? 1 : 0;
			$.ajax({
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					stories_selected: stories_selected, stories_columns: stories_columns
				},
				success: function(newdata){	
					window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#add_content';
					location.reload(true);
				},
			});
		});
		
		$( "#dialog_pages_sitetree" ).dialog({
			autoOpen: false,
			closeOnEscape: false,
			position: { my: "right", at: "top", of: window },
			close: function(e, i) { 
				$(this).remove(); 
				$("#btn_dialog_pages_sitetree").fadeOut('fast');
			}
		});
		
		$( "#btn_dialog_pages_sitetree" ).click(function() {
			$( "#dialog_pages_sitetree" ).load('pages_sitetree_dialog.php').dialog('open');
		});	

		$('.btn_widgets_edit').click(function(event){
			event.preventDefault();
			var action = "use_widgets";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var pages_widgets_id = this.id;
			$.ajax({
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					pages_widgets_id: pages_widgets_id
				},
				success: function(newdata){	
					$("#widgets_stage").empty().append(newdata).hide().fadeIn('fast');
				},
			});
		});
		
		$('.btn_widgets_edit_view').click(function(event){
			event.preventDefault();
			var action = "use_widgets";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var pages_widgets_id = this.id;
			$.ajax({
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					pages_widgets_id: pages_widgets_id
				},
				success: function(newdata){	
					$("#widgets_stage").empty().append(newdata).hide().fadeIn('fast');
				},
			});
		});

		$('#btn_story_save').click(function(event){
			event.preventDefault();
			var story_content = get_textarea_editor('<?php echo $wysiwyg_editor['editor']; ?>', 'story_content');
			var action = "pages_story";
			var token = $("#token").val();
			var users_id = $("#users_id").val();			
			
			var optionTagTexts = [];
			$("ul#tags li").each(function() { optionTagTexts.push($(this).text()) });
			var tag = optionTagTexts;
			
			var story_custom_title = $('input:checkbox[name=story_custom_title]').is(':checked') ? 1 : 0;
			var story_custom_title_value = $("#story_custom_title_value").val();
			var story_css_class = $("#story_css_class").val();
			var story_wide_teaser_image = $('input:radio[name=story_wide_teaser_image]:checked').val();
			var story_promote = $('input:checkbox[name=story_promote]').is(':checked') ? 1 : 0;
			var story_link = $('input:checkbox[name=story_link]').is(':checked') ? 1 : 0;
			
			var story_event = $('input:checkbox[name=story_event]').is(':checked') ? 1 : 0;
			var story_event_date = $("#story_event_date").val() ? $("#story_event_date").val() : null;
			var story_event_time = $("#story_event_time").val() ? $("#story_event_time").val() +':00' : '00:00:00';
			var story_event_datetime = (story_event_date==null) ?  null : story_event_date +' '+ story_event_time;

			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_story').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_story').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id, story_content: story_content, 
					tag: tag, story_custom_title: story_custom_title, 
					story_custom_title_value: story_custom_title_value, story_css_class: story_css_class, 
					story_wide_teaser_image: story_wide_teaser_image, story_promote: story_promote, story_link: story_link, 
					story_event: story_event, story_event_datetime: story_event_datetime 
				},
				success: function(newdata){	
					ajaxReply('','#ajax_status_story');
					$("#story_samples").empty().append('<h4>Samples:</h4>'+newdata).hide().fadeIn('fast');
				},
			});
		});
		
		$('#btn_save_meta').click(function(event){
			event.preventDefault();
			var action = "save_meta";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var meta_keywords = $("#meta_keywords").val();
			var meta_description = $("#meta_description").val();
			var meta_robots = $("#meta_robots option:selected").val();
			var meta_additional = $("#meta_additional").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_meta').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_meta').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: {
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					meta_keywords: meta_keywords, meta_description: meta_description, meta_robots: meta_robots, meta_additional: meta_additional
				},
				success: function(message){	
					ajaxReply('','#ajax_status_meta');
				},
			});
		});

		$('#customize').click(function(){
			$('#customize_story').toggle();
		});
		
		$( "#users_find" ).autocomplete({
			delay: 300,
			source: function( request, response ) {
				$.ajax({
					type: "post",
					url: "users_ajax.php",
					dataType: "json",
					data: {
						action: "users_search",
						token: $("#token").val(),
						s: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.user,
								id: item.users_id,
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				$("input#pid").val(ui.item.id),
				$(function(){
					$('#btn_add_users_rights').click(function(){
						var users_meta = ui.item.value;
						var pages_id = $("#pages_id").val();
						var action = "pages_rights_add_users";
						var token = $("#token").val();
						var users_id = $("#users_id").val();
						$.ajax({
							beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
							complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
							type: 'POST',
							url: 'pages_edit_ajax.php',
							data: { 
								action: action, token: token, users_id: users_id, pages_id: pages_id,
								users_meta: users_meta
							},
							success: function(message){
								ajaxReplyHistory(message,'#ajax_status_rights');
								if(message) {
									var newRow = $('<tr class="paging"><td class="paging">'+users_meta+'</td><td class="paging"><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value='+message+' /></td><td class="paging"><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value='+message+' /></td><td class="paging"><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value='+message+' /></td><td class="paging"><button class="btn_delete_rights" value='+message+'>delete</button></td></tr>');
									$('#rights > tbody:last').append(newRow);
									$("#users_find").val('');
								}
							},
						});
					
					});
				});
			}
		});
			
		$( "#groups_find" ).autocomplete({
			delay: 300,
			source: function( request, response ) {
				$.ajax({
					type: "post",
					url: "groups_ajax.php",
					dataType: "json",
					data: {
						action: "groups_meta_search",
						token: $("#token").val(),
						s: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.groups,
								id: item.groups_id,
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				$("input#gid").val(ui.item.id),					
				$(function(){
					$('#btn_add_groups_rights').click(function(){
						var groups_meta = ui.item.value;
						var pages_id = $("#pages_id").val();
						var action = "pages_rights_add_groups";
						var token = $("#token").val();
						var users_id = $("#users_id").val();
						$.ajax({
							beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
							complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
							type: 'POST',
							url: 'pages_edit_ajax.php',
							data: { 
								action: action, token: token, users_id: users_id, pages_id: pages_id,
								groups_meta: groups_meta
							},
							success: function(message){
								ajaxReplyHistory(message,'#ajax_status_rights');
								if(message) {
									var newRow = $('<tr class="paging"><td class="paging">'+groups_meta+'</td><td class="paging"><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value='+message+' /></td><td class="paging"><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value='+message+' /></td><td class="paging"><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value='+message+' /></td><td class="paging"><button class="btn_delete_rights" value='+message+'>delete</button></td></tr>');
									$('#rights > tbody:last').append(newRow);
									$("#groups_find").val('');
								}
							},
						});
					
					});
				});
			}
		});

		$( "#stories_filter" ).autocomplete({
			delay: 300,
			source: function( request, response ) {
				$.ajax({
					type: "post",
					url: "pages_ajax.php",
					dataType: "json",
					data: {
						action: "pages_tag",
						token: $("#token").val(),
						s: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.tag,
								id: item.pages_id,
							}
						}));
					}
				});
			},
			minLength: 1
		});

		$( "#tag" ).autocomplete({
			delay: 300,
			source: function( request, response ) {
				$.ajax({
					type: "post",
					url: "pages_ajax.php",
					dataType: "json",
					data: {
						action: "pages_tag",
						token: $("#token").val(),
						s: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.tag,
								id: item.pages_id,
							}
						}));
					}
				});
			},
			minLength: 1
		});
		
		$(document).on("click", "button.btn_delete_rights", function() {
			var pages_id = $("#pages_id").val();
			var action = "pages_rights_delete";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_rights_id = $(this).val();
			$(this).parent().parent().parent().remove();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					pages_rights_id: pages_rights_id
				},
				success: function(message){
					ajaxReplyHistory(message,'#ajax_status_rights');					
				},
			});
		});

		$("#btn_rights_save").click(function(event) {
			event.preventDefault();
			var pages_id = $("#pages_id").val();
			var action = "pages_rights_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var r_id = [];
			var r_read = [];
			var r_edit = [];
			var r_create = [];
			$("input[name='r_id[]']").each(function (){
				r_id.push(parseInt($(this).val()));
			});
			$("input[name='rights_read[]']:checked").each(function (){
				r_read.push(parseInt($(this).val()));
			});
			$("input[name='rights_edit[]']:checked").each(function (){
				r_edit.push(parseInt($(this).val()));
			});
			$("input[name='rights_create[]']:checked").each(function (){
				r_create.push(parseInt($(this).val()));
			});
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					r_id: r_id, r_read: r_read, r_edit: r_edit, r_create: r_create
				},
				//data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_id=" + pages_id + "&r_id=" + r_id + "&r_read=" + r_read + "&r_edit=" + r_edit + "&r_create=" + r_create,
					success: function(message){	
						ajaxReply(message,'#ajax_status_rights');
					},
			});
		});
		
		$('#btn_child_pages').click(function(event){
			event.preventDefault();
			var action = "get_child_pages";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_child_pages').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_child_pages').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id
				},
				success: function(newdata){	
					$("#child_pages").empty().append(newdata).hide().fadeIn('fast');
				},
			});
		});
		
		$('#btn_pages_sitetree').click(function(event){
			event.preventDefault();
			$("#sitetree_selected_name").empty();
			var action = "sitetree_select_list";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			$.ajax({
				beforeSend: function() { loading = $('.ajax_spinner_hierarchy').show()},
				complete: function(){ loading = setTimeout("$('.ajax_spinner_hierarchy').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id
				},
				success: function(newdata){	
					console.log(newdata);
					//$("#sitetree_select").html(newdata);
					$("#sitetree_select").empty().html(newdata).hide().fadeIn('fast');
				},
			});
		});
	
		$('#btn_new_parent_id').click(function(event){
			event.preventDefault();
			var action = "update_parent_id";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var parent_id = $("#parent_id").val();
			var new_parent_id = $("#new_parent_id").val();
			$.ajax({				
				beforeSend: function() { loading = $('.ajax_spinner_hierarchy').show()},
				complete: function(){ loading = setTimeout("$('.ajax_spinner_hierarchy').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					parent_id: parent_id, new_parent_id: new_parent_id
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_hierarchy');
				},
			});
		});
		
		$('#btn_pages_remove_hierarchy').click(function(event){
			event.preventDefault();
			var action = "remove_hierarchy";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var parent_id = $("#parent_id").val();
			$.ajax({				
				beforeSend: function() { loading = $('#ajax_spinner_remove_hierarchy').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_remove_hierarchy').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					parent_id: parent_id
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_remove_hierarchy');
				},
			});
		});
		
		$('.ajax_history').click(function(event){
			event.preventDefault();
			var action = "pages_history_extended";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $(this).attr('id'); 
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_history').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_history').hide()",700)},
				type: 'POST',
				url: 'pages_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id
				},
				success: function(message){
					$("#pages_history").empty().append(message).hide().fadeIn('fast');
				},
			});
		});
		
		$('.btn_pages_publish').click(function(event){
			event.preventDefault();
			var pages_id = this.id;
			var status = 2;
			var action = "pages_publish";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var access = $('input:radio[name=access]:checked').val();
			var title_tag = $("#title_tag").val();

			var content = get_textarea_editor('<?php echo $wysiwyg_editor['editor']; ?>', 'content');
			var pages_title = $("#pages_title").val();
			var pages_title_alternative = $("#pages_title_alternative").val();
			var pages_id_link = $("#pages_id_link").val();
			var content_author = $("#content_author").val();

			var date_start = $("#date_start").val() ? $("#date_start").val() : null;
			var time_start = $("#time_start").val() ? $("#time_start").val() +':00' : '00:00:00';
			var datetime_start = (date_start==null) ?  null : date_start +' '+ time_start;
			var date_end = $("#date_end").val() ? $("#date_end").val() : null;
			var time_end = $("#time_end").val() ? $("#time_end").val() +':00' : '00:00:00';
			var datetime_end = (date_end==null) ? null : date_end +' '+ time_end;
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_publish').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_publish').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id, status: status, title_tag: title_tag, access: access, 
					content: content, content_author: content_author, pages_title: pages_title, pages_title_alternative: pages_title_alternative, pages_id_link: pages_id_link, 
					datetime_start: datetime_start, datetime_end: datetime_end
				},
				success: function(message){	
					$("#status").empty().append("<option>Published</option>").removeClass("ui-state-error").addClass("ui-state-highlight").hide().fadeIn('fast');
					$(".btn_pages_delete").hide();
					ajaxReply(message,'#ajax_status_publish');
					$('#tabs-publish > a > span').removeClass("ui-icon ui-icon-notice").addClass("ui-icon ui-icon-check");
				}
			});
		});

		$('.btn_pages_pending').click(function(event){
			event.preventDefault();
			var pages_id = this.id;
			var status = 4;
			var action = "pages_status";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var datetime_start = null;
			var datetime_end = null;
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_change_status').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_change_status').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					status: status, datetime_start: datetime_start, datetime_end: datetime_end, 
				},
				success: function(message){	
					$("#status").empty().append("<option>Pending</option>").hide().removeClass("ui-state-highlight").addClass("ui-state-error").fadeIn('fast');
					$(".btn_pages_delete").hide();
					ajaxReply(message,'#ajax_status_change_status');
				}
			});
		});
		
		$('.btn_pages_trash').click(function(event){
			event.preventDefault();
			var pages_id = this.id;
			var status = 5;
			var action = "pages_status";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var datetime_start = null;
			var datetime_end = null;
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_change_status').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_change_status').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					status: status, datetime_start: datetime_start, datetime_end: datetime_end, 
				},
				success: function(message){	
					$("#status").empty().append("<option>Trash</option>").removeClass("ui-state-highlight").addClass("ui-state-error").hide().fadeIn('fast');
					$(".btn_pages_delete").show();
					ajaxReply(message,'#ajax_status_change_status');		
					$('#tabs-publish > a > span').removeClass("ui-icon ui-icon-check").addClass("ui-icon ui-icon-notice");
				}
			});
		});

		$('.btn_pages_delete').click(function(event){
			event.preventDefault();
			$("#dialog_delete_page").dialog("open");
			$("#dialog_delete_page").dialog({
				buttons : {
				"Confirm" : function() {
					$(this).dialog("close");
					var pages_id = $("#pages_id").val();
					var action = "pages_delete";
					var token = $("#token").val();
					var users_id = $("#users_id").val();

					$.ajax({
						beforeSend: function() { loading = $('#ajax_spinner_change_status').show()},
						complete: function(){ loading = setTimeout("$('#ajax_spinner_change_status').hide()",1000)},
						type: 'POST',
						url: 'pages_edit_ajax.php',
						data: { 
							action: action, token: token, users_id: users_id, pages_id: pages_id
						},
						success: function(message){	
							$("#tabs_edit").fadeOut('fast');
							$("#page_delete").empty().append("<div style='width:400px;margin:40px 0 40px 0;'>"+message+"</div>").hide().fadeIn('fast');
							ajaxReply(message,'#ajax_status_change_status');
						}
					});
				},
				"Cancel" : function() {
					$(this).dialog("close");
					}
				}
			});
		});		
		
		$('.btn_pages_archive').click(function(event){
			event.preventDefault();
			var pages_id = this.id;
			var status = 3;
			var action = "pages_status";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var datetime_start = null;
			var datetime_end = null;
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_change_status').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_change_status').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id,
					status: status, datetime_start: datetime_start, datetime_end: datetime_end
				},
				success: function(message){	
					$("#status").empty().append("<option>Archive</option>").removeClass("ui-state-highlight").addClass("ui-state-error").hide().fadeIn('fast');
					ajaxReply(message,'#ajax_status_change_status');
					$('#tabs-publish > a > span').removeClass("ui-icon ui-icon-check").addClass("ui-icon ui-icon-notice");
				}
			});
		});

		$('.btn_create_upload_folder').click(function(event){
			event.preventDefault();
			var pages_id = this.id;
			var action = "pages_create_folder";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_create_folder').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_create_folder').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id
				},
				success: function(message){	
					ajaxReply(message,'#ajax_status_create_folder');
				}
			});
		});		


        $('#btn_browse_directory').click(function (event) {
            event.preventDefault();
			var pages_id = $(this).attr("data-dir");
            var action = "browse_directory";
			var users_id = $("#users_id").val();
            var directory = $(this).attr("data-dir");
            var token = $("#token").val();
            var loading;
            $.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_create_folder').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_create_folder').hide()",1000)},
                type: 'POST',
                url: 'pages_edit_ajax.php',
				data: {
					action: action, token: token, users_id: users_id, pages_id: pages_id, directory: directory
				 },				
                success: function (data) {
                    $("#folder_view").empty().html(data).hide().fadeIn('fast');
                }
            });
        });

		

		$.datepicker.setDefaults($.datepicker.regional['sv']);

		$("#date_start").datepicker({
			showWeek: true, firstDay: 1,
			minDate: -2, maxDate: '+1M +10D',
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});
		$("#date_end").datepicker({
			showWeek: true, firstDay: 1,
			minDate: 0,
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});
		$("#story_event_date").datepicker({
			showWeek: true, firstDay: 1,
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true, 
			selectOtherMonths: true,
			showAnim: ''
		});
		
		$("#date_initiate").datepicker({
			showWeek: true, firstDay: 1
		});
	
		$('#btn_get_calendar_categories').click(function(event){
			event.preventDefault();
			var action = "calendar_categories";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_calendar').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_calendar').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id
				},
				success: function(data){	
					$("#calendar_categories").empty().append(data).hide().fadeIn('fast');
				}
			});
		});
		
		$('#btn_set_calendar_categories').click(function(event){
			event.preventDefault();			
			var action = "calendar_categories_select";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var calendar_categories_id = $("#calendar_categories_id option:selected").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_calendars_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id, 
					calendar_categories_id: calendar_categories_id
				},
				success: function(data){
					$("#calendar_events").val('category: '+data);
					$("#calendar_categories_saved_id").val(calendar_categories_id);
					$("#calendar_views_saved_id").val('0');
				}
			});
		});
		
		$('#btn_get_calendar_views').click(function(event){
			event.preventDefault();
			var action = "calendar_views";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_calendar').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id
				},
				success: function(data){	
					$("#calendar_views").empty().append(data).hide().fadeIn('fast');
				}
			});
		});
		
		$('#btn_set_calendar_views').click(function(event){
			event.preventDefault();			
			var action = "calendar_views_select";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var calendar_views_id = $("#calendar_views_id option:selected").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_calendars_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id, 
					calendar_views_id: calendar_views_id
				},
				success: function(data){
					$("#calendar_events").val('view: '+data);
					$("#calendar_views_saved_id").val(calendar_views_id);
					$("#calendar_categories_saved_id").val('0');
				}
			});
		});

		$('#btn_set_calendar_events').click(function(event){
			event.preventDefault();			
			var action = "calendar_events_set";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var calendar_categories_id = $("#calendar_categories_saved_id").val();
			var calendar_views_id = $("#calendar_views_saved_id").val();
			var period_initiate = $("#period_select option:selected").val();
			var calendar_area = $("#calendar_area option:selected").val();
			var calendar_show = $("#calendar_show option:selected").val();
			var date_initiate = $("#date_initiate").val() ? $("#date_initiate").val() : null;
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_calendar').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_calendar').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_calendars_ajax.php',
				data: { 
					action: action, token: token, users_id: users_id, pages_id: pages_id, 
					calendar_categories_id: calendar_categories_id, calendar_views_id: calendar_views_id, 
					date_initiate: date_initiate, calendar_area: calendar_area, calendar_show: calendar_show, period_initiate: period_initiate
				},
				success: function(message){
					ajaxReply(message,'#ajax_status_calendar');
				}
			});
		});
		
		$('#btn_set_calendar_reload').click(function(event){
			event.preventDefault();
			window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#settings';
			location.reload(true);	
		});
		
		$("#tabs_edit").tabs({ active: window.location.hash.substr(1)
		});

		$('.btn_reload').click(function(event){
			event.preventDefault();
            curTabIndex = $('.ui-tabs-active').index();
			window.location.hash = curTabIndex;
			window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href += window.location.hash;
			window.location.reload(true);
		});
		
		$('#btn_add_tag').click(function(event) {
			$('#ajax_spinner_tag').show();
			setTimeout("$('#ajax_spinner_tag').hide()",700);
			var tag = $("#tag").val();
			var optionTexts = [];
			$("ul#tags li").each(function() { optionTexts.push($(this).text()) });
			if(optionTexts.indexOf(tag) == -1) {
				$('ul#tags').append('<li>'+tag+'<i class="far fa-trash-alt" aria-hidden="true"></i></li>');
			}
			$("#tag").val('');
		});
		
		$("ul#tags").delegate( ".fa-trash-alt", "click", function() {
			$(this).parent().remove();
		});		

		$('#btn_tag').click(function(event) {
			var optionTexts = [];
			$("ul#tags li").each(function() { optionTexts.push($(this).text()) });
			alert(optionTexts);			
		});

		$('#btn_help').click(function(event){
			event.preventDefault();			
			var activeTabIndex = $( "#tabs_edit" ).tabs( "option", "active" );
			var text = $("#tabs_edit ul.ui-tabs-nav li.ui-tabs-active").text();
			var link = text.toLowerCase();
			
			window.open('http://storiesaround.com/help/'+link, '_blank');
		});
		
		var slider_width_value = $("#stories_wide_teaser_image_width_slider_value").val();
		
		$("#stories_wide_teaser_image_width_slider").slider({
			value: slider_width_value,
			min: 10,
			max: 50,
			step: 1,
			slide: function( event, ui ) {
				$( "#stories_wide_teaser_image_width" ).val( ui.value );
			}
		});
		$("#stories_wide_teaser_image_width").val($("#stories_wide_teaser_image_width_slider").slider("value"));
		
		var slider_align_value = $("#stories_wide_teaser_image_align_slider_value").val();
		$("#stories_wide_teaser_image_align_slider" ).slider({
			value: slider_align_value,
			min: 0,
			max: 1,
			step: 1,
			slide: function( event, ui ) {
				$( "#stories_wide_teaser_image_align" ).val( ui.value );
			}
		});
		$("#stories_wide_teaser_image_align").val($("#stories_wide_teaser_image_align_slider").slider("value"));

		autosave();
		
	});
	
	<?php
	// add wysiwyg js file
	if (is_array($wysiwyg_editor)) {
		if(file_exists(CMS_ABSPATH .'/cms/libraries/'.$wysiwyg_editor['include_js_script'])) {
			include CMS_ABSPATH.'/cms/libraries/'.$wysiwyg_editor['include_js_script'];
		}
	}
	?>

	function activateEditor(editor) {
		if (editor == "tinymce") {
			tinymce.init({
				forced_root_block : "", 
				mode : "specific_textareas",
				editor_selector : "tinymce-grid",
				menubar: "",
				image_advtab: true,
				plugins: [
					"autolink lists link hr anchor pagebreak code image paste"
				],
				toolbar: "undo redo bold italic link code image removeformat",
				paste_remove_styles: true,
				extended_valid_elements:'script'
			});
		}
	}
	
	function page_preview(id) {
		var w = screen.width-50;
		var h = screen.height-50;
		//w=window.open('pages_preview.php?id='+id,'','width='+w+',height='+h+',scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
		//w.focus();
	}
	
	function selection_preview(id) {
		w=window.open('pages_selections_preview.php?token=<?php echo $_SESSION['token']; ?>&id='+id,'','width=1280,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
		w.focus();
	}
	
	function image_delete(img, id) {
		var action = "delete_image";
		var token = $("#token").val();
		var users_id = $("#users_id").val();
		var pages_id = $("#pages_id").val();

		$.ajax({
			type: 'POST',
			url: 'pages_edit_ajax.php',
			data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_id=" + pages_id + "&image=" + img,
			success: function(){	
				$('#'+id).closest('li').fadeOut(100, function() { $(this).closest('li').remove(); });
			},
		});
	}

	function autosave() {
		setInterval("save()", <?php if(isset($_SESSION['site_autosave'])) { echo $_SESSION['site_autosave']; } else { echo '15000'; } ?>);
	}		
		
	function save() {			
		var content = get_textarea_editor('<?php echo $wysiwyg_editor['editor']; ?>', 'content');
		var action = "update";
		var token = $("#token").val();
		var pages_id = $("#pages_id").val();
		var users_id = $("#users_id").val();
		var pages_title = $("#pages_title").val();
		var pages_title_alternative = $("#pages_title_alternative").val();
		var title_hide = $('input:checkbox[name=title_hide]').is(':checked') ? 1 : 0;
		var breadcrumb_hide = $('input:checkbox[name=breadcrumb_hide]').is(':checked') ? 1 : 0;
		var content_author = $("#content_author").val();
		var rss_description = $("#rss_description").val();
		var rss_promote = $('input:checkbox[name=rss_promote]').is(':checked') ? 1 : 0;
		var events = $('input:checkbox[name=events]').is(':checked') ? 1 : 0;
		var reservations = 0;
		var plugins = $('input:checkbox[name=plugins]').is(':checked') ? 1 : 0;
		var role_superadministrator = $('input:checkbox[name=role_superadministrator]').is(':checked') ? 1 : 0;
		if (pages_title.length > 0){
			$.ajax({				
				beforeSend: function() { loading = $('.ajax_spinner_content').show()},
				complete: function(){ loading = setTimeout("$('.ajax_spinner_content').hide()",1000)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, pages_id: pages_id, users_id: users_id, pages_title: pages_title, pages_title_alternative: pages_title_alternative, title_hide: title_hide, breadcrumb_hide: breadcrumb_hide, content: content, content_author: content_author, 
					rss_promote: rss_promote, rss_description: rss_description, events: events, reservations: reservations, plugins: plugins 
				},
				success: function(message){	
					ajaxReply(message,'.ajax_status_content');
					$('#show_pages_title').html(pages_title);
				},
			});
		}
	}


	function ciao(m) {
		console.log(m);
	}


	function load_images() {
		var action = "show_images";
		var token = $("#token").val();
		var users_id = $("#users_id").val();
		var pages_id = $("#pages_id").val();
		$.ajax({
			beforeSend: function() { loading = $('#ajax_spinner_images').show()},
			complete: function(){ loading = setTimeout("$('#ajax_spinner_images').hide()",1000)},
			type: 'POST',
			url: 'pages_edit_ajax.php',
			data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_id=" + pages_id,
			success: function(newdata) {
				$("#page_images").empty().append(newdata).hide().fadeIn('fast');
			},
		});
	}

	function load_files() {
		var action = "show_files";
		var token = $("#token").val();
		var users_id = $("#users_id").val();
		var pages_id = $("#pages_id").val();
		$.ajax({
			beforeSend: function() { loading = $('#ajax_spinner_files').show()},
			complete: function(){ loading = setTimeout("$('#ajax_spinner_files').hide()",1000)},
			type: 'POST',
			url: 'pages_edit_ajax.php',
			data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_id=" + pages_id,
			success: function(newdata) {
				$("#page_files").empty().append(newdata).hide().fadeIn('fast');
			},
		});
	}
	
</script>


<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
<input type="hidden" id="wysiwyg" value="<?php echo strtolower($wysiwyg_editor['editor']) ;?>" />
<input type="hidden" name="pages_id" id="pages_id" value="<?php echo $id;?>" />
<input type="hidden" id="users_id" name="users_id" value="<?php echo $_SESSION['users_id']; ?>">
<input type="hidden" id="theme" name="theme" value="<?php echo $_SESSION['site_theme']; ?>">
<input type="hidden" id="parent_id" name="parent_id" value="<?php echo $arr['parent_id']; ?>">
<input type="hidden" id="site_header_image" name="site_header_image" value="<?php echo $arr['header_image']; ?>">

<table style="width:100%;">
	<tr>
		<td style="vertical-align:bottom;width:180px;">
		<img src="css/images/storiesaround_logotype_black.png" style="width:140px;padding-left:5px;float:left;" alt="Storiesaround logotype" />
		</td>
		<td>
		Editing page:
			<?php echo '<b>"<span class="text-bigger" id="show_pages_title">'.$arr['title'].'</span>"</b>'; ?>
		</td>
		<td valign="bottom" align="right" style="width:400px;">
			
			<span id="ajax_spinner_autosave" style='display:none'><img src="css/images/spinner.gif"></span>
			<span id="ajax_status_autosave" style='display:none'></span>
			<span class="toolbar_help small"><button type="submit" id="btn_help">&nbsp;</button></span>
			<span class="toolbar_close small"><button type="submit" onclick="parent.$.colorbox.close(); return false;">Close</button></span>
			<span class="toolbar_reload small"><button type="submit" class="btn_reload">Reload</button></span>
			<span class="toolbar_preview small"><button type="submit" onclick="page_preview(<?php echo $id; ?>)">Preview</button></span>
			
		</td>
	</tr>
</table>

<div id="page_delete" style="display:none;"></div>

<div id="dialog_delete_page" title="Confirmation required" style="display:none;">
  Delete this page?
</div>
<div id="dialog_delete_item" title="Confirmation required" style="display:none;">
  Delete?
</div>

<div id="dialog_token" title="Confirmation required" style="display:none;">
  <h3>Ouch!</h3>
  <div id="dialog_token_text"></div>
</div>

<?php
// check active edit token
$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
$utc_datetime_dtz = get_utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
$history = new History();

// check edit access
$check_edit = $history->getHistorySession($id, 'pages_id');
$acc = false;
if(is_array($check_edit)) {
	$utc_last_modified = $check_edit['utc_datetime'];
	$last_token = $check_edit['session'];
	$last_name = $check_edit['name'];
	$utc_last_modified_dtz = get_utc_dtz($utc_last_modified, $dtz, 'Y-m-d H:i:s');
	
	// check tokens
	$token_match = $_SESSION['token'] == $last_token ? true : false;
	if($token_match) {
		$history->setHistory($id, 'pages_id', 'ACCESS', 'edit', $_SESSION['users_id'], $_SESSION['token'], $utc_datetime);
		$acc = true;
	}
	// check last edit access
	if(!$acc) {
		$utc_stamp = new DateTime($utc_last_modified_dtz, new DateTimeZone($dtz));
		$utc_stamp->modify('+ 300 seconds');
		$utc_editable = $utc_stamp->format('Y-m-d H:i:s');
		if($utc_datetime_dtz > $utc_editable) {
			$history->setHistory($id, 'pages_id', 'ACCESS', 'edit', $_SESSION['users_id'], $_SESSION['token'], $utc_datetime);
			$acc = true;
		} else {
			echo '<div style="padding:20px;">';
			echo '<h4 class="admin-heading">This page was recently edited ('.$utc_last_modified_dtz .') by '.$last_name.'</h4>';
			echo '<br /><span class="toolbar"><button id="btn_edit_ownership">Take ownership - edit page</button></span>';
			echo '</div>';
			die;
		}
	}	
}

?>

<div id="tabs_edit" style="display:none;">
	
	<ul>
		<li><a href="#setup">Setup</a></li>
		<li><a href="#settings">Settings</a></li>
		<li><a href="#calendar">Calendar</a></li>
		<li><a href="#plugins">Plugin</a></li>		
		<li><a href="#files">Files</a></li>
		<li><a href="#images">Images</a></li>
		<li><a href="#content_editor">Content</a></li>		
		<li><a href="#add_content">Additional content</a></li>		
		<li><a href="#grid">Grid</a></li>
		<li><a href="#story">Story</a></li>
		<li><a href="#rss">RSS</a></li>
		<li><a href="#meta">Meta</a></li>
		<li><a href="#rights">Rights</a></li>		
		
		<?php
		$page_status_icon = '';
		switch ($arr['status']) {
			case 0:
			case 1:
				$page_status_icon = "ui-icon ui-icon-notice";
			break;
			case 2:
				$page_status_icon = "ui-icon ui-icon-check";
			break;
			default:
				$page_status_icon = "ui-icon ui-icon-notice";
			break;
		}
		?>
		<li id="tabs-publish"><a href="#publish">Publish <span class="<?php echo $page_status_icon; ?>"></span></a></li>
	</ul>
	
	<div id="setup">	
	
	
		<div class="admin-panel">
			
			<table border="0" style="width:100%;">
				<tr>
					<td width="25%" style="vertical-align:top;padding-top:10px;">
					<h4><i class="far fa-newspaper" aria-hidden="true"></i> Page template</h4>
						<p>
						<span class="toolbar"><button id="link_page_template_setup">Save template</button></span>
						</p>
					</td>
					<td style="padding-bottom:10px;">
						<div style="float:right;width:100%;height:260px;overflow-y: hidden;overflow:auto;">
							<div class="page_templates"><input type="radio" name="setup_template" value="0" <?php if($arr['template'] == 0) {echo 'checked';}?>>"Sidebars"<br><img src="css/images/template_sidebars.png" style="margin-top:10px;height:75px;"></div>
							<div class="page_templates"><input type="radio" name="setup_template" value="1" <?php if($arr['template'] == 1) {echo 'checked';}?>>"Left sidebar"<br><img src="css/images/template_sidebar_left.png" style="margin-top:10px;height:75px;"></div>
							<div class="page_templates"><input type="radio" name="setup_template" value="2" <?php if($arr['template'] == 2) {echo 'checked';}?>>"Right sidebar"<br><img src="css/images/template_sidebar_right.png" style="margin-top:10px;height:75px;"></div>
							<div class="page_templates"><input type="radio" name="setup_template" value="3" <?php if($arr['template'] == 3) {echo 'checked';}?>>"Panorama"<br><img src="css/images/template_panorama.png" style="margin-top:10px;height:75px;"></div>
							<div class="page_templates"><input type="radio" name="setup_template" value="4" <?php if($arr['template'] == 4) {echo 'checked';}?>>"Sidebars joined"<br><img src="css/images/template_sidebars_close.png" style="margin-top:10px;height:75px;"></div>
							<div class="page_templates"><input type="radio" name="setup_template" value="5" <?php if($arr['template'] == 5) {echo 'checked';}?>>Custom "main"<br><img src="css/images/template_panorama_custom_main.png" style="margin-top:10px;height:75px;"></div>
							<div class="page_templates"><input type="radio" name="setup_template" value="6" <?php if($arr['template'] == 6) {echo 'checked';}?>>Custom "page"<br><img src="css/images/template_panorama_custom_page.png" style="margin-top:10px;height:75px;"></div>
												
							<?php
							$str = "";	
							foreach (new DirectoryIterator(CMS_ABSPATH.'/content/templates') as $fileInfo) {
								if($fileInfo->isDot()) continue;
								$template = $fileInfo->getFilename();
								$str .= '<option value="'.$template.'"';
								if(isset($arr['template_custom'])) {
									if($arr['template_custom'] == $template) {
										$str .= ' selected';
									}
								}
								$str .= '>'.$template.'</option>';
							}
							?>
							<div class="page_templates">
								Custom file 
								<p>
									<code><?php echo CMS_DIR.'/content/templates/'; ?></code>
								</p>
								<p>
									<select id="template_custom"><option></option><?php echo $str;?></select>
								</p>
							</div>
						</div>
						<div class="page_templates_info">
							Custom page is required in order to show content from <b>Calendar and Plugin<b>
						</div>
						
					<td>
				</tr>
			</table>
			
		</div>

		
		<div class="">
			
			<table border="0" style="width:100%;">
				<tr>
					<td width="25%" style="vertical-align:top;">
						<h4><i class="fas fa-eye" aria-hidden="true"></i> Header settings</h4>
						<p>
							<span class="toolbar"><button id="btn_site_header_setup" value="btn_site_header_setup">Save</button></span>
							<span id="ajax_spinner_header_image" style="display:none;"><img src="css/images/spinner.gif"></span>
							<span id="ajax_status_header_image" style="display:none;"></span>
						</p>
					</td>
					<td style="vertical-align:top">&nbsp;
						<div style="margin:5px 0;">
							<input type="checkbox" name="landing_page" id="landing_page" value="1" <?php if ($arr['landing_page'] == 1) { echo ' checked';}?>> Apply CSS class 'landing-page" to body element (use full size header image)
						</div>
						<div style="margin:5px 0;">
							<input type="checkbox" name="parallax_scroll" id="parallax_scroll" value="1" <?php if ($arr['parallax_scroll'] == 1) { echo ' checked';}?>> Apply parallax scrolling effect to header content
 						</div>
						<div style="margin:5px 0;">
							<input type="checkbox" name="header_caption_show" <?php if ($arr['header_caption_show'] == 1) { echo ' checked';}?> value="1"> Show media caption
						</div>
						<div style="margin:5px 0;">
							<select id="header_image_timeout">
							<?php 
							$timeouts = array(5000, 6000, 7000, 8000, 9000, 10000, 12000, 14000, 16000, 18000, 20000, 25000, 30000);						
							foreach ($timeouts as $timeout) {
								$sec = $timeout / 1000;
								echo '<option value="'.$timeout.'"';
								if ($arr['header_image_timeout'] == $timeout) {
									echo ' selected';
								}
								echo '>'.$sec.' sec</option>';
							}
							?>
							</select> Swap media 
						</div>
						<div style="margin:5px 0;">
							<select id="header_image_fade">
							<?php 
							$speeds = array("super slow", "slow", "normal", "fast", "super fast");						
							foreach ($speeds as $speed) {	
								echo '<option value="'.$speed.'"';
								if ($arr['header_image_fade'] == $speed) {
									echo ' selected';
								}
								echo '>'.$speed.'</option>';
							}
							?>
							</select> Fade duration
						</div>

					</td>
					<td width="25%" align="right">
						<?php 			
						echo '<div id="box_site_preview_header" style="width:180px;height:120px;margin:10px 0">';
						?>
						<img src="css/images/template-landing-page.png">
						<?php
						echo '</div>';				
						?>				
					</td>

				</tr>
			</table>
			
		</div>


		<div class="admin-panel">
			
			<table border="0" style="width:100%;">
				<tr>
					<td width="25%" style="vertical-align:top;" rowspan="2">
						<h4><i class="fas fa-image" aria-hidden="true"></i> Header media</h4>
							
						<p>
							Choose matching height (equal) 
						</p>

						<div id="directory_view" style="max-height:400px;overflow:auto;">					
						<?php

							$header_image = json_decode($arr['header_image']);
							$header_caption = json_decode($arr['header_caption']);
							$header_caption_align = json_decode($arr['header_caption_align']);
							$header_caption_vertical_align = json_decode($arr['header_caption_vertical_align']);

							$dir = '/content/uploads/header';
																
							if (is_dir(CMS_ABSPATH . '/'. $dir)) {

								if ($dh = opendir(CMS_ABSPATH .'/'. $dir)) {
									$images_ext = array('jpg','jpeg','gif','png', 'mp4');

									while (($file = readdir($dh)) !== false) {
										if (!is_dir(CMS_ABSPATH .'/'. $dir.'/'.$file)) {
										
											$ext = pathinfo($dir.'/'.$file, PATHINFO_EXTENSION);
											if(in_array($ext, $images_ext)) {
												$checked = "";
												if (is_array($header_image)) {
													$checked = in_array($file, $header_image) ? " checked" : "";
												}
												if ($ext != 'mp4') {
													echo '<div class="code" style="position:relative"><img alt="'.$file.'" src="../content/uploads/header/'. $file .'" data-filename="'.$file.'" width="150px" style="margin-bottom:10px;" /><input type="checkbox" '.$checked.' class="image_mark" data-file="'.$file.'" data-video="false" style="position:absolute;top:2px;left:2px;transform:scale(2);"></div>';
												} else {
													echo '<div class="code" style="position:relative"><video id="header_video_url" width="150" muted controls><source src="..'.$dir.'/'.$file.'"></video><input type="checkbox" '.$checked.' class="image_mark" data-file="'.$file.'" data-video="true" style="position:absolute;top:2px;left:2px;transform:scale(2);"></div>';
												}

											}
										}
									}
									closedir($dh);
								}
							}	
						?>
						</div>


					</td>
					<td style="vertical-align:top;padding:20px 0">
						<table width="100%">
							<tr>
								<td width="50%">
									<b>Selected media</b>
								</td>
								<td>
									<b>Caption (max 100 char) | text-align</b>
									<p>Supports h1 and p tags as markdown code</p>
								</td>
							</tr>
						</table>
							<p>
							<div id="directory_view_slides">
							<?php
							if (is_array($header_image)) {
								$dir = '/content/uploads/header';
								if (is_dir(CMS_ABSPATH . '/'. $dir)) {
									$countHeader = 0;
									foreach ($header_image as $image) { 
										$ext = getFileExtension($image);
										$align_value = getCaptionAlignAsInteger($header_caption_align[$countHeader]);
										$vertical_align_value = getCaptionVerticalAlignAsInteger($header_caption_vertical_align[$countHeader]);
										if ($ext == "mp4") {
											echo '<div data-image="'.$image.'" style="" class="header-images"><video style="width:100%; max-height:100px" class="header-images" controls muted><source src="..'.$dir.'/'.$image.'"></video><textarea name="header_caption[]">'.$header_caption[$countHeader].'</textarea><input type="range" name="header_caption_align[]" class="header_caption_align" value="'.$align_value.'" min="0" max="2"><input type="range" name="header_caption_vertical_align[]" class="header_caption_vertical_align" value="'.$vertical_align_value.'" min="0" max="2"></div>';
										} else {
											echo '<div data-image="'.$image.'" style="background-image:url(../content/uploads/header/'.$image.');" class="header-images"><textarea name="header_caption[]">'.$header_caption[$countHeader].'</textarea><input type="range" name="header_caption_align[]" class="header_caption_align" value="'.$align_value.'" min="0" max="2"><input type="range" name="header_caption_vertical_align[]" class="header_caption_vertical_align" value="'.$vertical_align_value.'" min="0" max="2"></div>';
										}
										$countHeader++;
									}
								}
							}
							?>
							</div>
						</p>

					</td>
					<td width="25%" align="right" style="vertical-align:top;">
						<?php 			
						echo '<div id="box_site_preview_header" style="width:180px;height:120px;margin:20px 0">';
						echo '<img id="site_preview" src="css/images/template_header.png">';
						echo '</div>';				
						?>				
					</td>
				</tr>

			</table>
			
		</div>
		
		<div class="admin-panel">
			
			<table border="0" style="width:100%;">
				<tr>
					<td width="25%" style="vertical-align:top;">
						<h4><i class="fas fa-tasks" aria-hidden="true"></i> Selections</h4>
						<p style="margin-top:10px;">
							<span class="toolbar"><button id="btn_site_selections" value="btn_site_selections">Save selections</button></span>
							<span id="ajax_spinner_selections" style='display:none'><img src="css/images/spinner.gif"></span>
							<br /><span id="ajax_status_selections" style='display:none'></span>
						</p>
						<p>
							<label for="selections_filter">Filter</label><br />
							<input type="text" id="selections_filter" />
						</p>
						<p>
							<input type="checkbox" id="selections_checked" value="1"> <label for="selections_checked" style="float:inherit;">Toggle checked | all</label>
						</p>
					</td>
					<td style="vertical-align:top;">					
						<div style="border:1px dashed #D0D0D0;padding:5px;max-height:300px;overflow:auto;background-color:lightgrey">
						<?php
						$s = explode(",", $arr['selections']);
						$selections = new Selections();
						$row_selections = $selections->getSelectionsActive();
						if($row_selections) {
							echo '<ul id="choose_selections" class="code">';
							foreach($row_selections as $r) {
								$checked = in_array($r['pages_selections_id'],$s) ? ' checked=checked' : null;
								echo '<li class="ui-widget ui-widget-content"><input name="selections[]" type="checkbox" value="'.$r['pages_selections_id'].'"'.$checked.'>&nbsp;';
								echo $r['name'];
								echo '<div style="float:right;">('.$r['area'].') <a style="cursor:pointer;" onclick="selection_preview('. $r['pages_selections_id'] .')">preview<span class="ui-icon ui-icon-newwin" style="display:inline-block;vertical-align:text-bottom;"></span></a></div></li>';
							}
							echo '</ul>';
						}
						
						?>
						</div>
					</td>
					<td width="25%" align="right">
					<?php 	
					echo '<div id="box_site_preview_selections" style="width:180px;height:180px;">';
						echo '<img id="site_preview" src="css/images/template_selection.png">';
					echo '</div>';
					?>
					</td>
				</tr>
			</table>
			
		</div>
		
	
	</div>

	
	
	<div id="settings">	
		
		<div class="admin-panel">
			
			<table style="width:100%">
				<tr>
					<td style="width:70%">

						<h4><i class="fas fa-paw" aria-hidden="true"></i> Breadcrumb</h4>
						
						<p>
							<input type="radio" name="breadcrumb" value="0" <?php if($arr['breadcrumb'] == 0) {echo 'checked';}?>> hide  | <input type="radio" name="breadcrumb" value="1" <?php if($arr['breadcrumb'] == 1) {echo 'checked';}?>> show (default) | <input type="radio" name="breadcrumb" value="2" <?php if($arr['breadcrumb'] == 2) {echo 'checked';}?>> show + children (select) | <input type="radio" name="breadcrumb" value="3" <?php if($arr['breadcrumb'] == 3) {echo 'checked';}?>> show + children (ul)
						</p>
					
						<h4><i class="fas fa-globe" aria-hidden="true"></i> Language</h4>
						<p>
							Set this page html lang attribute - overrides site settings. 2-letter (ISO 639-1 codes)
						</p>
						<p>
							<input type="text" size="2" name="lang" id="lang" value="<?php echo $arr['lang']; ?>" />
						</p>

						<h4><i class="fas fa-search" aria-hidden="true"></i> Search field</h4>
						<p>
							Specify search field area
						</p>

						<p>
							<select id="search_field_area">
							<?php
							$search_fields = [0 => "none", 1 => "above header", 2 => "inside header", 3 => "page top", 4 => "page content"];						
							if ($search_fields) {
								foreach ($search_fields as $key => $value) {
									$selected = $key == $arr['search_field_area'] ? " selected" : "";
									echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
								}
							}
							?>
							</select>
						</p>

						<h4><i class="fas fa-tag" aria-hidden="true"></i> Category</h4>
						<p>
							Specify a page category  
						</p>
						<p>
							<select id="category">
								<option value=""></option>
							<?php
							$categories = new PagesCategories();
							$row_categories = $categories->getPagesCategoriesNamed();
							$row_categories = flatt_array($row_categories);
							
							if ($row_categories) {
								foreach ($row_categories as $key => $value) {
									$selected = $key == $arr['category_position'] ? " selected" : "";
									echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
								}
							}
							?>
								<option value="99"></option>
							</select>
							<?php
							//echo ' (weight: '. $arr['category_position'] .')';
							?>
						</p>
												
					</td>
					<td style="vertical-align:bottom;text-align:right;">
						<p>
							<span class="toolbar"><button id="btn_settings">Save</button></span>
							<span id="ajax_spinner_settings" style='display:none'><img src="css/images/spinner.gif"></span>
							<span id="ajax_status_settings" style='display:none'></span>
						</p>
					</td>
				</tr>
			</table>

		</div>

		<?php if(get_role_CMS('superadministrator') == 1) { ?>
		
			<div class="admin-panel">

				<h4><i class="fas fa-folder" aria-hidden="true"></i> Folder</h4>

				<?php if (!is_dir(CMS_ABSPATH . '/content/uploads/pages/'. $id)) { ?>
				<p>
					Missing upload folder &raquo; &nbsp;
					<span class="toolbar"><button class="btn_create_upload_folder" id="<?php echo $id; ?>">Create folder</button></span>
					<span id="ajax_spinner_create_folder" style='display:none'><img src="css/images/spinner.gif"></span>
					<span id="ajax_status_create_folder" style='display:none'></span>					
				</p>

				<?php } else { ?>

				<p>
				<span class="toolbar"><button id="btn_browse_directory" data-dir="<?php echo $id;?>">Browse folder</button></span>
					<div id="folder_view" style="border:1px dashed #000;margin:10px;overflow:auto;max-height:600px;padding:10px;" class="ui-black-white">
					</div>
				</p>

				<?php } ?>
			</div>
	
		<?php } ?>


		<div class="admin-panel">
		
			<?php if(get_role_CMS('administrator') == 1) { ?>
	
			<table style="width:100%";>
				<tr>
					<td style="width:48%; vertical-align:top;">
					<div>
						<h4><i class="fas fa-sitemap" aria-hidden="true"></i> Page hierarchy</h4>
						<p>
							Attach this page to parent page:&nbsp;<span id="sitetree_selected_name" style="" /></span>
						</p>
						<p>
							<input type="hidden" id="new_parent_id" />
							<span class="toolbar"><button id="btn_pages_sitetree">Load pages tree</button></span>
							<span class="toolbar"><button id="btn_new_parent_id" value="btn_new_parent_id">Save</button></span>
							<span class="ajax_spinner_hierarchy" style="display:none;"><img src="css/images/spinner.gif"></span>
							<span id="ajax_status_hierarchy" style="display:none;"></span>
						</p>											
						<p>
							<div id="sitetree_select"></div>

						</p>
						<p>
							<span class="toolbar"><button id="btn_child_pages">Show child pages</button></span>
						</p>
						<div style="border:1px dashed #D0D0D0;background:#FCFCFC;padding:5px;height:100px;overflow:auto;">
							<div id="child_pages">
							<span id="ajax_spinner_child_pages" style="display:none;"><img src="css/images/spinner.gif"></span>
							</div>
						</div>
						<br /><br />
						<p>
							Removing page from hierarchy can only be done if no children pages are attached.<br /><br />
							<span class="toolbar"><button id="btn_pages_remove_hierarchy">Remove this page from pages hierarchy</button></span>
							<span id="ajax_spinner_remove_hierarchy" style="display:none;"><img src="css/images/spinner.gif"></span>
							<span id="ajax_status_remove_hierarchy" style="display:none;"></span>

						</p>
						
					</div>
					</td>
					<td style="width:4%; vertical-align:top;">
					&nbsp;
					</td>
					<td style="width:48%; vertical-align:top;">
					<p>
					Drag and drop to change position <span id="ajax_result_move"></span>
					</p>
					<div id="sortable-wrapper">
						<div id="sortable_pages">
							<ul style="padding:0;" class="pages">
							
							<?php							
							// get nodes with same parent_id
							$nodes = $pages->getPagesNode($arr['parent_id']);

							if(isset($nodes)){
								foreach($nodes as $node){							
									$class = ($node['pages_id'] == $arr['pages_id']) ? ' class="active ui-widget-content"' : ' class="ui-widget-content"';
									echo '<li '.$class.' id="arr_pages_id_'. $node['pages_id'] .'"><span class="ui-icon ui-icon-triangle-2-n-s" style="display:inline-block;cursor:n-resize;margin:0 10px;" title="Move page"></span>'. $node['title'] .'</li>';
								}
							}
							?>
								
							</ul>
						</div>
					</div>

					</td>
				</tr>
			</table>
			
			<?php } else { echo 'Administrators can move pages'; } ?>
		
		</div>
	

		
		
	
	</div>

	<div id="calendar">
	


		<div class="admin-panel">

			<table style="width:100%">
				<tr>
					<td style="width:40%">
						<h4><i class="fas fa-calendar-alt" aria-hidden="true"></i> Calendar</h4>
						<p>
							<input type="checkbox" name="events" id="events" value="1" <?php if($arr['events'] == 1) {echo 'checked';}?>>
							include calendar events (if changed save and reload page)
						</p>
					</td>
					<td style="vertical-align:bottom;text-align:right;">
						<span class="toolbar"><button class="content_save">Save</button></span>&nbsp;&nbsp;<span class="toolbar"><button id="btn_set_calendar_reload">Reload</button></span>
						<span class="ajax_spinner_content" style='display:none'><img src="css/images/spinner.gif"></span>
						<span class="ajax_status_content" style='display:none'></span>
					</td>
				</tr>
			</table>

			
			<?php
			if($arr['events'] == 1) {
			
				$name = $date_initiate = $view = $text = $period_initiate = $calendar_categories_id = $calendar_views_id = null;
				$cal = new Calendar();
				$calendar = $cal->getPagesCalendar($id);
				if($calendar) {
					$name = $calendar['name'];
					$date_initiate = $calendar['date_initiate'];
					$period_initiate = $calendar['period_initiate'];
					$calendar_categories_id = $calendar['calendar_categories_id'];
					$calendar_views_id = $calendar['calendar_views_id'];
					$calendar_area = $calendar['calendar_area'];
					$calendar_show = $calendar['calendar_show'];
					$text = $calendar_views_id > 0 ? 'view: ' : 'category: ';
				}
				
				echo '<div style="margin:20px;padding:10px;border:1px dotted #000;background:#FCFCFC;">';
				
				?>
				
				<table>
					<tr>
						<td><span class="toolbar_refresh"><button id="btn_get_calendar_categories" name="btn_get_calendar_categories" type="submit">Get categories</button></span></td>
						<td style="text-align:right;"><span id="calendar_categories"></span></td>
						<td><span class="toolbar"><button id="btn_set_calendar_categories" name="btn_set_calendar_categories" type="submit">Select category</button></span></td>
						<td style="padding:0 10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;or&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td><span class="toolbar_refresh"><button id="btn_get_calendar_views" name="btn_get_calendar_views" type="submit">Get views</button></span></td>
						<td style="text-align:right;"><span id="calendar_views"></span></td>
						<td><span class="toolbar"><button id="btn_set_calendar_views" name="btn_set_calendar_views" type="submit">Select view</button></span></td>
					</tr>
				</table>
				<input type="hidden" id="calendar_categories_saved_id" value="<?php echo $calendar_categories_id;?>" /><input type="hidden" id="calendar_views_saved_id" value="<?php echo $calendar_views_id; ?>" />
				<table>
					<tr>
						<td style="padding:10px;vertical-align:top;" colspan="4"><label for="calendar_events">selected</label><br /><input type="text" id="calendar_events" style="width:300px;background:#FCFCFC;padding:2px;" disabled="disabled" value="<?php echo $text . $name; ?>"></td>
					</tr>
					<tr>
						<td style="padding:10px;vertical-align:top;" colspan="3">						
						<p>
							<label for="calendar_area">show calendar in area:</label><br />
							<select id="calendar_area">
								<option value="content" <?php if ($calendar){ if($calendar_area == "content") { echo ' selected=selected'; }} ?>>content (events are shown inside calendar)</option>
								<option value="left-sidebar" <?php if ($calendar){ if($calendar_area == "left-sidebar") { echo ' selected=selected'; }} ?>>left sidebar (if set - events are shown below calendar) - only if category is selected</option>
								<option value="right-sidebar" <?php if ($calendar){ if($calendar_area == "right-sidebar") { echo ' selected=selected'; }} ?>>right sidebar (if set - events are shown below calendar) - only if category is selected</option>
							</select>
						</p>
						</td>
						<td style="padding:10px;vertical-align:top;">
						<p>
							<label for="calendar_area">show (only if category is selected):</label><br />
							<select id="calendar_show">
								<option value="1" <?php if ($calendar){ if($calendar_show == "1") { echo ' selected=selected'; }} ?>>calendar+events</option>
								<option value="2" <?php if ($calendar){ if($calendar_show == "2") { echo ' selected=selected'; }} ?>>calendar</option>
								<option value="3" <?php if ($calendar){ if($calendar_show == "3") { echo ' selected=selected'; }} ?>>events</option>
							</select>
						</p>
						</td>
						
					</tr>
					<tr>
						<td style="padding:10px;vertical-align:top;"><label for="date_initiate">default date</label><br /><input type="text" id="date_initiate" value="<?php echo $date_initiate; ?>" /></td>
						<td style="padding:10px;vertical-align:top;">
						<?php						
						$periods = array("one day" => "day","four days" => "4days","one week" => "week","two weeks" => "2weeks","one month" => "month");
						$period_list = '<label for="period_select">default period</label><br /><select id="period_select">';
						foreach($periods as $key => $value) {
							$period_list .= '<option value="'.$value.'"';
							if($value==$period_initiate) {
								$period_list .= ' selected=selected';
							}
							$period_list .= '>'.$key.'</option>';
						}
						$period_list .= '</select>';						
						echo $period_list;
						?>
						
						</td>
						<td>&nbsp;</td>
						<td style="padding:10px;vertical-align:bottom;">
							<span class="toolbar"><button id="btn_set_calendar_events" name="btn_set_calendar_events" type="submit">Save events settings</button></span>
							<span id="ajax_spinner_calendar" style='display:none'><img src="css/images/spinner.gif"></span>
							<span id="ajax_status_calendar" style='display:none'></span>						
						</td>
					</tr>
				</table>
				</div>
			
			<?php } ?>
		</div>

	</div>

	
	<div id="plugins">
		
		<div class="admin-panel">
			<?php if(get_role_CMS('administrator') == 1) { ?>

			<h4><i class="fas fa-puzzle-piece" aria-hidden="true"></i> Plugin</h4>
			<p>
				<input type="checkbox" name="plugins" id="plugins" value="1" <?php if($arr['plugins'] == 1) {echo 'checked';}?>>
				include plugins 
				
				<span class="toolbar"><button id="btn_use_plugins" value="btn_use_plugins">Save</button></span>
				<span id="ajax_spinner_plugins" style='display:none'><img src="css/images/spinner.gif"></span>
				<span id="ajax_status_plugins" style='display:none'></span>
				
				<?php $plugins_style = $arr['plugins'] == 1 ? "" : "display:none;";	?>
	
				<span id="show_load_plugins" style="<?php echo $plugins_style;?>">
					<span class="toolbar"><button id="btn_load_plugins">Load plugins</button></span>
				</span>
			</p>
		</div>
		
		<div class="admin-panel">
			<p>
				<div id="page_plugins">
				<?php

				if($arr['plugins']) {
					
					$pages_plugins = new PagesPlugins();					
					$plugin = $pages_plugins->getPagesPlugins($id);
					
					if($plugin) {
					
						if($plugin['plugins_active'] == 0) {
							echo '<p><i><b>Plugin is not active and will not take any action</b></i></p>';
						}
						echo 'This page uses plugin: ';
						echo '<p>';
							echo '<b>'.$plugin['plugins_title'].'</b>';
						echo '</p>';
						echo '<p>';
							$plugin_meta = new $plugin['plugins_title']();
							$plugin_details = $plugin_meta->info();								
							echo 'Replace: <i>'.$plugin_details['area'].'</i>';
						echo '</p>';

						echo '<p>';
						echo '<label for="plugin_arguments">Plugin arguments (blank field or defined arguments listed)</label>';
						echo '<input type="text" class="code" style="width:100%;" id="plugin_arguments" name="plugin_arguments" value="'.$arr['plugin_arguments'].'">';
						echo '</p>';
						echo '<p>';
						echo '<span class="toolbar"><button id="btn_plugin_arguments_save" value="btn_plugin_arguments_save">Save plugin arguments</button></span>';
						echo '</p>';
						if(file_exists(CMS_ABSPATH.'/content/plugins/'.strtolower($plugin['plugins_title']).'/inc.arguments.php')) { 
							include CMS_ABSPATH.'/content/plugins/'.strtolower($plugin['plugins_title']).'/inc.arguments.php';
						}
						
					}
				}
				?>
				</div>
				
			</p>
			
		<?php } else { echo 'Administrators can handle plugins'; } ?>
		</div>

	</div>


	
	<div id="images">
		<?php
		$image = new Image();
		$sizes = $image->get_image_sizes();
		?>

		<div class="admin-panel">
			<h4><i class="far fa-file-image" aria-hidden="true"></i> Images</h4>
			<table style="width:100%">
				<tr>
					<td style="vertical-align:bottom">
						<ul>
							<li>Uploaded images are saved (if original size permits) in pixel widths:<br> <?php echo implode(", ", $sizes); ?></li>
							<li>Image originals are deleted by default</li>
						</ul>
						<p>
							<span class="toolbar_reload"><button id="btn_load_images">Show images</button></span>
							&nbsp;|&nbsp;
							<span class="toolbar_save_images"><button id="btn_save_images">Save</button></span>
						</p>
					</td>
					<td style="vertical-align:bottom; text-align:right">
						<p>
							New images settings - max width:
							<select id="max_width">
							<?php
							foreach($sizes as $size) {
								$selected = $size == 1366 ? " selected" : "";
								echo '<option value="'.$size.'" '.$selected.'>'.$size.' px</option>';
							}
							?>
							</select>
						</p>
						<p>
							<input type="checkbox" id="original" name="original" value="1"> Keep original image 
						</p>
						<p>
							<span class="toolbar_save_images"><button id="btn_new_images">Upload images</button></span>
						</p>
					</td>
				</tr>
			</table>
			<div class="clearfix">
			<span id="ajax_spinner_images" style='display:none'><img src="css/images/spinner.gif"></span>
			<span id="ajax_status_images" style='display:none'></span>
			</div>
			
		</div>
		
		<p>
			<div id="page_images"></div>
		</p>
		
		<div id="dialog_delete_image" title="Confirmation required">
		  Delete this image?
		</div>
		
		
	</div>


	<div id="files">
	
		<div class="admin-panel">
			<h4><i class="far fa-file" aria-hidden="true"></i> Files</h4>
			<p>
				<a class="colorbox_images" href="pages_edit_files.php?token=<?php echo $_SESSION['token'];?>&pages_id=<?php echo $_GET['id'];?>"><span class="toolbar_save_images"><button id="btn_new_files">New files</button></span></a>
				&nbsp;|&nbsp;
				<span class="toolbar_reload"><button id="btn_load_files">Refresh files</button></span>
				<span id="ajax_spinner_files" style='display:none'><img src="css/images/spinner.gif"></span>
				<span id="ajax_status_files" style='display:none'></span>
			</p>
		</div>
		
		<p>
			<div id="page_files"></div>
		</p>

	</div>


	
	<div id="content_editor">
	
		<div class="admin-panel">
	
			<p>
				<table style="width:100%">
					<tr>
						<td width="20px"><span class="toolbar"><button class="content_save">Save</button></span></td>
						<td width="20px"><span class="ajax_spinner_content" style='display:none'><img src="css/images/spinner.gif"></span></td>
						<td width="300px"><span class="ajax_status_content" style='display:none'></span></td>
						<td>&nbsp;</td>
						<?php
						if($arr['plugins']) {
							if($plugin) {
								echo '<td style="text-align:right;"><span style="background:#ffff99;border:1px solid #000;padding:10px;">'.$plugin_details['info'].'</span></td>';
							}
						}
						?>
					</tr>
				</table>
			</p>
			<?php

			$content_percent_width = 100;						
			// sidebar width between 20-33%
			$sidebar_percent_width = $_SESSION['site_template_sidebar_width'];
			$class_editor = $wysiwyg_editor['css-class'];

			switch ($arr['template']) {
				case 0:
					// sidebars
					$content_percent_width = 100 - ($sidebar_percent_width * 2); 
					$css_content_width = 'width:'.$content_percent_width.'%;';
					break;
				case 1:
					// left sidebar
					$content_percent_width = 100 - $sidebar_percent_width;
					$css_content_width = 'width:'.$content_percent_width.'%;';
				case 2:
					// right sidebar
					$content_percent_width = 100 - $sidebar_percent_width;
					$css_content_width = 'width:'.$content_percent_width.'%;';
					break;
				
				case 3:
					// panorama
					$content_percent_width = 100; 
					$css_content_width = 'width:'.$content_percent_width.'%;';
					break;
				case 4:
		            // sidebars right joined
        		    $content_percent_width = 100 - ($sidebar_percent_width + $sidebar_percent_width * 0.67);
					$css_content_width = 'width:'.$content_percent_width.'%;';
					break;
				case 5:
				case 6:
					// custom
					$content_percent_width = 100;
					$css_content_width = 'width:'.$content_percent_width.'%;';
					break;
			}
			
			if(!isset($_SESSION['site_wysiwyg'])) {
				$class_editor = null;
				$_SESSION['site_wysiwyg'] = "";
			}
			
			?>
			<p>
				<label for="pages_title">Title: </label>
				<br />
				<input type="text" name="pages_title" id="pages_title" title="Enter title" style="font-size:2.14em;<?php echo $css_content_width; ?>" maxlength="100" value="<?php if(isset($arr['title'])){echo $arr['title'];}?>" />
			</p>

			<a href="#" id="link_title_alternative">Alternative title</a>
			<div id="title_settings" style="display:none">
				<p>
					<input type="checkbox" id="title_hide" name="title_hide" <?php if($arr['title_hide'] == 1) {echo 'checked="checked"';}?>> Hide page title (title can manually be added inside content)
				</p>
				<p>
					<label for="pages_title_alternative">Alternativt title: </label>
					<br />
					<input type="text" name="pages_title_alternative" id="pages_title_alternative" title="Enter alternative title" style="font-size:2.14em;<?php echo $css_content_width; ?>" maxlength="100" value="<?php if(isset($arr['title_alternative'])){echo $arr['title_alternative'];}?>" />
				</p>
			</div>

			<p>
				<label for="content">Content: </label>
				<br />
				<div>
					<textarea name="content" id="content" class="<?php echo $class_editor; ?>" style="<?php echo $css_content_width; ?>"><?php echo $arr['content'];?></textarea>
				</div>
			</p>
			<p>
				<label for="content_author">Content author and contact:</label>
				<br />
				<input type="text" name="content_author" id="content_author" title="Enter content author and contact" style="width:460px;" maxlength="100" value="<?php if(isset($arr['content_author'])){echo $arr['content_author'];}?>" />
			</p>

			<p>
				<table>
					<tr>
						<td width="20px"><span class="toolbar"><button class="content_save">Save</button></span></td>
						<td width="20px"><span class="ajax_spinner_content" style='display:none'><img src="css/images/spinner.gif"></span></td>
						<td width="300px"><span class="ajax_status_content" style='display:none'></span></td>
					</tr>
				</table>
			</p>
			<p>
				<?php echo '<span style="font-style:italic;font-size:0.8em;">Calculated width: '. round($content_percent_width * $_SESSION['site_wrapper_page_width'] / 100) .'px (approximately)</span>'; ?>
			</p>
		
		</div>
	
	</div>
	
	
	<div id="add_content">
	
		<?php
		// get saved stories
		$rows = $pages->getPagesStories($id);
		// variable to hold stories position / areas
		$str_cols_out_of_range = "";

		// get page widgets 
		$pages_widgets = new PagesWidgets();
		$rows_widgets = $pages_widgets->getPagesWidgets($id);
		
		// get page content
		$arr = $pages->getPagesEditContent($id);
		?>
		
		<div class="admin-panel">
		
			<div id="accordion_add_content">
			
				<h3>Stories <span class="ui-icon ui-icon-tag" style="display:inline-block;"></span></h3>
				
				<div>
					
					<p>
						<a href="#add_content" id="link_stories_settings">Settings <span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a>
					</p>				
	

					<div id="stories_settings" class="edit_joins">

					<p>
						<input type="checkbox" id="stories_equal_height" name="stories_equal_height" <?php if($arr['stories_equal_height'] == 1) {echo 'checked';}?>> 
						Display stories in a row at same height 
					</p>
					<p>
						<input type="checkbox" id="stories_last_modified" name="stories_last_modified" <?php if($arr['stories_last_modified'] == 1) {echo 'checked';}?>> 
						Show when stories are last modified
					</p>
					<p>
						<input type="checkbox" id="stories_image_copyright" name="stories_image_copyright" <?php if($arr['stories_image_copyright'] == 1) {echo 'checked';}?>> 
						Show image copyright
					</p>
					<p>
						<table style="width:100%;" class="edit_pages_storiess">
							<tr>
								<td style="width:20%;">
									Story image teaser (when applicable)
								</td>
								<td style="width:10%">
									align image:
								</td>
								<td style="width: 20%">
									<input type="radio" name="stories_wide_teaser_image_align" value="0" <?php if($arr['stories_wide_teaser_image_align'] == 0) {echo 'checked';}?> style="margin-right:10px;"> left  |  right <input type="radio" name="stories_wide_teaser_image_align" value="1" <?php if($arr['stories_wide_teaser_image_align'] == 1) {echo 'checked';}?> style="margin-left:10px;"> 
								</td>
								<td style="width:10%;text-align:right">
									Width %:
								</td>
								<td style="width: 10%">
									<div id="stories_wide_teaser_image_width_slider" style="width:100%;"></div>
								</td>
								<td style="width:25%;padding-left:20px;">
									<input type="text" id="stories_wide_teaser_image_width" style="width:25px;" disabled>
								</td>
							</tr>
						</table>						
					</p>
					<p>
						<label for="stories_css_class">Set stories css class (override):</label><br />
						<select id="stories_css_class" name="stories_css_class" class="code">
							<option value=""></option>
							<?php 
							get_css_class($css_custom, $input=$arr['stories_css_class']);
							?>
						</select>
					</p>
					<p>
						<input type="hidden" id="stories_wide_teaser_image_width_slider_value" value="<?php echo $arr['stories_wide_teaser_image_width']; ?>" />
						<input type="hidden" id="stories_wide_teaser_image_align_slider_value" value="<?php echo $arr['stories_wide_teaser_image_align']; ?>" />
						

						<span class="toolbar"><button id="btn_stories_settings" name="btn_stories_settings" style="margin-left:10px;">Save</button><span class="toolbar">
						<span id="ajax_spinner_stories_modified" style='display:none'><img src="css/images/spinner.gif"></span>
						<span id="ajax_status_stories_settings" style='display:none'></span>								
					</p>
					
					</div>
				
					<p>
						<a href="#add_content" id="link_stories_childpages">Child stories <span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a>
					</p>				
					
					<div id="stories_childpages" class="edit_joins">
						<table width="100%" class="edit_pages_stories">
							<tr>
								<td style="width:20%;">
									<input type="checkbox" id="stories_child" name="stories_child" <?php if($arr['stories_child'] > 0) {echo 'checked';}?>> 
									Show child pages as stories
								</td>
								<td>
									
									<label for="stories_child_area">Show stories as:</label><br />
									<select id="stories_child_area" name="stories_child_area">
									<option value="0" <?php if($arr['stories_child_area'] == 0) {echo 'selected';}?>>(none)</option>
										<option value="0"></option>
										<option value="1" <?php if($arr['stories_child_area'] == 1) {echo 'selected';}?>>left sidebar | top image teaser</option>
										<option value="2" <?php if($arr['stories_child_area'] == 2) {echo 'selected';}?>>left sidebar | align image teaser to story</option>
										<option value="0"></option>
										<option value="3" <?php if($arr['stories_child_area'] == 3) {echo 'selected';}?>>right sidebar | top image teaser</option>
										<option value="4" <?php if($arr['stories_child_area'] == 4) {echo 'selected';}?>>right sidebar | align image teaser to story</option>
										<option value="0"></option>
										<option value="5" <?php if($arr['stories_child_area'] == 5) {echo 'selected';}?>>content | columns | top image teaser</option>
										<option value="6" <?php if($arr['stories_child_area'] == 6) {echo 'selected';}?>>content | columns | align image teaser</option>
										<option value="0"></option>
										<option value="7" <?php if($arr['stories_child_area'] == 7) {echo 'selected';}?>>content | rows | align image teaser to title</option>
										<option value="8" <?php if($arr['stories_child_area'] == 8) {echo 'selected';}?>>content | rows | align image teaser to story</option>										
										<option value="9" <?php if($arr['stories_child_area'] == 9) {echo 'selected';}?>>content | rows | exclude image teaser</option>
									</select>
									
								</td>
								<td style="width:15%;" align="right">
									
									<span class="toolbar"><button id="btn_stories_child" name="btn_stories_child" style="margin-left:10px;">Save</button><span class="toolbar">
									<br />
									<span id="ajax_spinner_stories_child" style='display:none'><img src="css/images/spinner.gif"></span>
									<span id="ajax_status_stories_child" style='display:none'></span>
									
								</td>
								<td width="15%" align="right">
									<div class="site_layout" class="clearfix">
										<div class="site_header"></div>
										<div class="site_sidebar_target_left">?</div>
										<div class="site_content_target">?</div>
										<div class="site_sidebar_target_right">?</div>
										<div class="site_footer"></div>
									</div>
								</td>
							</tr>
						</table>
					</div>

					<p>
						<a href="#add_content" id="link_stories_promoted">Promoted stories <span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a>
					</p>
				
					<div id="stories_promoted" class="edit_joins">

						<table width="100%" class="edit_pages_stories">
							<tr>
								<td style="width:20%;">
									<input type="checkbox" id="stories_promoted" name="stories_promoted" <?php if($arr['stories_promoted'] > 0) {echo 'checked';}?>> 
									Show promoted pages as stories
								</td>
								<td>
									<label for="stories_promoted">Show promoted pages in area:</label><br />
									<select id="stories_promoted_area" name="stories_promoted_area">

										<option value="0"></option>
										<option value="1" <?php if($arr['stories_promoted_area'] == 1) {echo 'selected';}?>>left sidebar | top image teaser</option>
										<option value="2" <?php if($arr['stories_promoted_area'] == 2) {echo 'selected';}?>>left sidebar | align image teaser to story</option>
										<option value="0"></option>
										<option value="3" <?php if($arr['stories_promoted_area'] == 3) {echo 'selected';}?>>right sidebar | top image teaser</option>
										<option value="4" <?php if($arr['stories_promoted_area'] == 4) {echo 'selected';}?>>right sidebar | align image teaser to story</option>
										<option value="0"></option>
										<option value="5" <?php if($arr['stories_promoted_area'] == 5) {echo 'selected';}?>>content | columns | top image teaser</option>
										<option value="6" <?php if($arr['stories_promoted_area'] == 6) {echo 'selected';}?>>content | columns | align image teaser</option>
										<option value="0"></option>
										<option value="7" <?php if($arr['stories_promoted_area'] == 7) {echo 'selected';}?>>content | rows | align image teaser to title</option>
										<option value="8" <?php if($arr['stories_promoted_area'] == 8) {echo 'selected';}?>>content | rows | align image teaser to story</option>										
										<option value="9" <?php if($arr['stories_promoted_area'] == 9) {echo 'selected';}?>>content | rows | exclude image teaser</option>

									</select>
									<p>
									<label for="stories_filter">Set filter: (tag):</label><br />
									<input id="stories_filter" / value="<?php echo $arr['stories_filter']; ?>">
									<input type="hidden" id="filter_id" />
									</p>
									
									<p>
									<label for="stories_limit">Limit promoted stories</label><br />
									<select name="stories_limit" id="stories_limit">
										<?php
										for ($i = 0; $i <= 25; $i++) {
											echo '<option value="'.$i.'"';
											$limit = $arr['stories_limit'] > 0 ? $arr['stories_limit'] : 0; 
											if($limit == $i) {
												echo ' selected';
											}
											echo '>'.$i.'</option>';
										}
										?>
									</select>
									</p>
								</td>
								<td style="width:15%;" align="right">
									<span class="toolbar"><button id="btn_stories_promoted" name="btn_stories_promoted" style="margin-left:10px;">Save</button><span class="toolbar">
									<br /><span id="ajax_spinner_stories_promoted" style='display:none'><img src="css/images/spinner.gif"></span>
									<span id="ajax_status_stories_promoted" style='display:none'></span>
								</td>
								<td style="width:15%;" align="right">
									<div class="site_layout" class="clearfix">
										<div class="site_header"></div>
										<div class="site_sidebar_target_left">?</div>
										<div class="site_content_target">?</div>
										<div class="site_sidebar_target_right">?</div>
										<div class="site_footer"></div>
									</div>
								</td>
							</tr>
						</table>

					</div>

					<p>
						<a href="#add_content" id="link_stories_event_dates">Event stories <span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a>
					</p>
				
					<div id="stories_event_dates" class="edit_joins">

						<table width="100%" class="edit_pages_stories">
							<tr>
								<td style="width:25%;">
									
									<input type="checkbox" id="stories_event_dates" name="stories_event_dates" <?php if($arr['stories_event_dates'] == 1) {echo 'checked';}?>> 
									Show event pages as stories
								</td>
								<td>
									<label for="stories_event_dates_filter">Set filter: (tag):</label><br />
									<input id="stories_event_dates_filter" / value="<?php echo $arr['stories_event_dates_filter']; ?>">
								</td>
								<td style="width:15%;" align="right">
									<span class="toolbar"><button id="btn_stories_event_dates" name="btn_stories_event_dates" style="margin-left:10px;">Save</button><span class="toolbar">
									<span id="ajax_spinner_stories_event_dates" style='display:none'><img src="css/images/spinner.gif"></span>
									<span id="ajax_status_stories_event_dates" style='display:none'></span>
								</td>
								<td width="15%" align="right">
									<div class="site_layout" class="clearfix">
										<div class="site_header"></div>
										<div class="site_sidebar_left"></div>
										<div class="site_content_target">?</div>
										<div class="site_sidebar_right"></div>
										<div class="site_footer"></div>
									</div>
								</td>
							</tr>
						</table>

					</div>
					
					<p>
						<a href="#add_content" id="link_stories_select">Selected stories <span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a>
					</p>
				
					<div id="stories_select" class="edit_joins">

						<?php
						$get_id = $id;
						$array_tree_node = null;
						?>

						<table width="100%" class="edit_pages_stories">
							<tr>
								<td style="width:20%;">
									<input type="checkbox" id="stories_selected" name="stories_selected" value="1" <?php if($arr['stories_selected'] > 0) {echo 'checked';}?>> 
									Show selected pages as stories
								</td>
								<td>
								
								<input type="checkbox" id="stories_columns" name="stories_columns" value="1" <?php if($arr['stories_columns'] > 0) {echo 'checked';}?>> 								
								Enable columns below content
								<p>
									<label for="search_story">Search story: </label><br />
									<input id="search_story" style="width:300px;" />
									<input type="hidden" id="pid" />				
									<span class="toolbar_add"><button id="btn_new_pages_search_stories_id" value="btn_new_pages_search_stories_id">Add</button></span>
								</p>
								<p>
									<span class="toolbar"><button id="btn_dialog_pages_sitetree">Show sitetree</button></span>
								</p>
								<span id="ajax_spinner_stories_select" style='display:none'><img src="css/images/spinner.gif"></span>
								<span id="ajax_status_stories_select" style='display:none'></span>								
								<div id="dialog_pages_sitetree" title="Sitetree" style="max-height:600px;overflow:auto;"></div>

								<td width="15%" align="right">
								<span class="toolbar"><button id="btn_change_cols" value="btn_change_cols">Save</button></span>
								</td>
								<td width="15%" align="right">

								<div class="site_layout" class="clearfix">
									<div class="site_header"></div>
									<div class="site_sidebar_target_left">?</div>
									<div class="site_content_target">?</div>
									<div class="site_sidebar_target_right">?</div>
									<div class="site_footer"></div>
								</div>
								</p>
								</td>
							</tr>
						</table>
					
					</div>
					
				</div>
				
				
				<h3>Widgets <span class="ui-icon ui-icon-image" style="display:inline-block;"></span><span class="ui-icon ui-icon-video" style="display:inline-block;"></span><span class="ui-icon ui-icon-mail-closed" style="display:inline-block;"></span><span class="ui-icon ui-icon-volume-on" style="display:inline-block;"></span><span class="ui-icon ui-icon-help" style="display:inline-block;"></span></h3>
				<div>
									
					<div style="float:left;width:350px;">
						<div id="widgets_select">
							<?php
							$widgets_in_dir = new Widgets();
							$widgets_in_dir->show();
							?>
						</div>
						<br />
						<div class="trash" style="width:340px;padding:10px;margin-top:10px;background:#D0D0D0;float:right;border: 1px dashed grey;float:left;">
							<span class="toolbar_delete"><button id="btn_delete_widgets">Delete pending widgets</button></span>
							<div id="pending_widgets" style="width:222px;min-height:40px;background:#D0D0D0;border:0px;overflow:auto;">
								<?php
								$pages_widgets = new PagesWidgets();
								$pages_widgets->getPagesWidgetsPending($id);
								?>
							</div>
						</div>
					</div>
					
					<div style="float:right">
						<form id="widgets_form">
							<div id="widgets_editor">
							
								<input type="hidden" id="pages_id" name="pages_id" value="<?php echo $id; ?>" />
								<!-- hide tollbar if no widget is selected! -->
								<div style="float:left;">
									Widgets editor
								</div>
								<div style="float:right;width:200px;text-align:right;">
									<span class="ajax_spinner_widgets_edit" style="display:none;"><img src="css/images/spinner.gif"></span>
									<span class="ajax_status_widgets_edit" style="display:none;"></span>
								</div>
								
								
								<div id="widgets_stage">
								</div>

							</div>
						</form>
					</div>
				
				</div>

				<!-- -->


			</div>
			
		</div>

		
		
		<?php
		if($arr['stories_promoted'] > 0) {
			$limit_stories = $arr['stories_limit'] > 0 ? $arr['stories_limit'] : 0;
			$rows_promoted = $pages->getPagesStoriesPromoted($arr['stories_filter'], $limit_stories);
		} else {
			$rows_promoted = null;
		}
		?>

		<!-- outer div -->
		<div style="width:100%;">

			<!-- inner div -->
			<div style="width:100%;margin:0px auto; background-color:yellow;display:none">
				selections
				<?php  
				//print_r2($row_selections);
				//print_r2($arr['selections']);
				?>
			</div>		
		
			<!-- inner div -->
			<div style="width:100%;margin:0px auto;">
	
				<?php
				
				if($arr['plugins']) {
					if($plugin) {			
						echo '<div style="background:#ffff99;border:1px solid #000;padding:10px;margin:10px;">'.$plugin_details['info'].'</div>';
					}
				}
				
				$column_space_edit = 'width:2%;';
				
				switch ($arr['template']) {	
					case 0:
						// sidebars 
						$sidebar_percent_width  = $sidebar_percent_width - 2;
						$css_left_sidebar_width = $css_right_sidebar_width = "width:" . $sidebar_percent_width  . "%";						
						break;
					case 1:
						// left sidebar
					case 2:
						// right sidebar
						$sidebar_percent_width  = $sidebar_percent_width - 2;
						$css_left_sidebar_width = $css_right_sidebar_width = 'width:'.$sidebar_percent_width.'%;';
						break;			
					case 3:
						// panorama
						break;
					case 4:
						// joined sidebars
						$content_percent_width = 100 - ($sidebar_percent_width + $sidebar_percent_width * 0.67);
						$right_sidebar_width = $sidebar_percent_width;
						$left_sidebar_width = $right_sidebar_width * 0.67;
						$right_sidebar_width = $right_sidebar_width - 2;
						$left_sidebar_width = $left_sidebar_width - 2;
						$css_left_sidebar_width = 'width:'.$left_sidebar_width.'%;';
						$css_right_sidebar_width = 'width:'.$right_sidebar_width.'%;';
						break;
						case 5:
						case 6:
							// custom
							break;
				}


				if($arr['template'] == 0 || $arr['template'] == 1) { ?>
			
					<div class="sidebar_wrapper_edit" style="<?php echo $css_left_sidebar_width;?>">

						<div class="column_description ui-state-disabled">left sidebar - calendar</div>

						<div id="widgets_left_sidebar" class="column_widgets_sidebar" style="float:left;width:100%;min-height:20px;border: 1px dashed #cccccc;">
							<div class="column_description ui-state-disabled">left sidebar - widgets</div>
							<?php get_widgets_content($rows_widgets, "widgets_left_sidebar"); ?>
						</div>
						
						<div class="area_space_edit"></div>

						<div class="promoted_stories_left_sidebar" class="column" style="width:100%;">
							<div class="column_description">left sidebar - promoted stories</div>
							<div id="container_stories_promoted_left_sidebar" class="container_stories_promoted" style="background:#fffbd0;"><?php if($arr['stories_promoted'] == 1 && $arr['stories_promoted_area'] == 1) { get_box_content_promoted($rows_promoted); } ?></div>
						</div>

						<div class="area_space_edit"></div>

						<div id="left_sidebar" class="column_selected_stories" style="width:100%;">
							<div class="column_description ui-state-disabled">left sidebar - stories</div>
							<?php get_box_content($rows, "left_sidebar", ""); ?>
						</div>
						
					</div>
					<div class="column_space_edit" style="<?php echo $column_space_edit;?>"></div>
				
				<?php } ?>
				
				<div class="content_wrapper_edit" style="float:left;<?php echo $css_content_width;?>;">
					<div class="column_description ui-state-disabled" style="height:20px;border:1px dashed grey;">content</div>
					<div class="area_space_edit"></div>
						
					<div id="widgets_content" class="column_widgets_content" style="float:left;width:100%;min-height:20px;border: 1px dashed #cccccc;">
						<div class="column_description ui-state-disabled">content - widgets</div>
						<?php get_widgets_content($rows_widgets, "widgets_content"); ?>
					</div>
			
					<div style="float:left;width:100%;min-height:20px;background:#FFF8DC;border: 1px dashed #cccccc;">
						<div class="column_description ui-state-disabled">content - event stories</div>
						<div id="container_stories_events" style="background:#FFF8DC"></div>
					</div>

					<div class="area_space_edit"></div>

					<div class="promoted_stories_content" class="column" style="width:100%;">
						<div class="column_description">content - promoted stories</div>
						<div id="container_stories_promoted_content" class="container_stories_promoted" style="background:#fffbd0;"><?php if($arr['stories_promoted'] == 1 && $arr['stories_promoted_area'] >= 3) { get_box_content_promoted($rows_promoted); } ?></div>
					</div>
					
					<div class="area_space_edit"></div>
					
					<div style="float:left;width:100%;min-height:20px;background:#FFF;">
						<div class="column_description ui-state-disabled">content - selected stories</div>
					</div>
					
					<div class="area_space_edit"></div>
					<?php
					$main_column_style = $arr['stories_columns'] == 1 ? "" : "width:100%";
					?>
					<div id="main" class="column_selected_stories" style="width:100%;"><?php get_box_content($rows, "main", $main_column_style); ?></div>

					<div style="clear:both;"></div>

					<div class="area_space_edit"></div>
					
					<div style="float:left;width:100%;min-height:20px;border: 1px dashed #cccccc;">
						<div class="column_description ui-state-disabled">content - child stories</div>
					</div>

					<div class="area_space_edit"></div>
					
					<?php
					if($arr['stories_child'] > 1) {
						$rows_childs = $pages->getPagesStoriesChild($id);
					} else {
						$rows_childs = null;
					}
					?>
					<div id="container_stories_child" style="background:#ffeebb;"><?php get_box_content_child($rows_childs); ?></div>
				
				</div>
				


				<?php
				if($arr['template'] == 4 ) { ?>
			
					<div class="column_space_edit" style="<?php echo $column_space_edit;?>"></div>
					
					<div class="sidebar_wrapper_edit" style="<?php echo $css_left_sidebar_width;?>">

						<div class="column_description ui-state-disabled">left sidebar - calendar</div>

						<div id="widgets_left_sidebar" class="column_widgets_sidebar" style="float:left;width:100%;min-height:20px;background:#ffffcc;border: 1px dashed #cccccc;">
							<div class="column_description ui-state-disabled">left sidebar - widgets</div>
							<?php get_widgets_content($rows_widgets, "widgets_left_sidebar"); ?>
						</div>
						
						<div class="area_space_edit"></div>

						<div class="promoted_stories_left_sidebar" class="column" style="width:100%;">
							<div class="column_description">left sidebar - promoted stories</div>
							<div id="container_stories_promoted_left_sidebar" class="container_stories_promoted" style="background:#fffbd0;"><?php if($arr['stories_promoted'] == 1 && $arr['stories_promoted_area'] == 2) { get_box_content_promoted($rows_promoted); } ?></div>
						</div>

						<div class="area_space_edit"></div>					

						<div id="left_sidebar" class="column_selected_stories" style="width:100%;">
							<div class="column_description ui-state-disabled">left sidebar - stories</div>
							<?php get_box_content($rows, "left_sidebar", ""); ?>
						</div>
						
					</div>
					
				<?php } ?>


				<?php 
				// use template settings
				if($arr['template'] == 0 || $arr['template'] == 2 || $arr['template'] == 4 ) { ?>

					<div class="column_space_edit" style="<?php echo $column_space_edit;?>"></div>
					<div class="sidebar_wrapper_edit" style="<?php echo $css_right_sidebar_width;?>">
				
						<div class="column_description ui-state-disabled">right sidebar - calendar</div>
						
						<div id="widgets_right_sidebar" class="column_widgets_sidebar" style="float:left;width:100%;min-height:20px;border: 1px dashed #cccccc;">
							<div class="column_description ui-state-disabled">right sidebar - widgets</div>
							<?php get_widgets_content($rows_widgets, "widgets_right_sidebar"); ?>					
						</div>
							
						<div class="area_space_edit"></div>

						<div class="promoted_stories_right_sidebar" class="column" style="width:100%;">
							<div class="column_description">right sidebar - promoted stories</div>
							<div id="container_stories_promoted_right_sidebar" class="container_stories_promoted" style="background:#fffbd0;"><?php if($arr['stories_promoted'] == 1) { get_box_content_promoted($rows_promoted); } ?></div>
						</div>

						<div class="area_space_edit"></div>
						
						<div id="right_sidebar" class="column_selected_stories" style="width:100%;">
							<div class="column_description ui-state-disabled">right sidebar - stories</div>
							<?php get_box_content($rows, "right_sidebar", ""); ?>
						</div>
					
					</div>
				
				<?php } ?>

			<!-- inner div -->
			</div>
		
		<!-- outer div -->
		</div>
		
		
		<div style="clear:both;">

			<div class="admin-panel">
		
				<span class="toolbar"><button id="btn_save_stories" name="btn_save_stories" type="submit">Save stories position</button></span>
				<span id="ajax_spinner_stories" style='display:none'><img src="css/images/spinner.gif"></span>
				<span id="ajax_status_stories" style='display:none'></span>
				
				<span class="toolbar"><button id="btn_save_widgets" name="btn_save_widgets" type="submit">Save widgets position</button></span>
				<span id="ajax_spinner_widgets" style='display:none'><img src="css/images/spinner.gif"></span>
				<span id="ajax_status_widgets" style='display:none'></span>
				
				
				<div style="float:right;min-height:70px;background:#F8F8F8;margin:10px;padding:10px;border:1px solid #ccc;">

					<div class="trash" title="Stories out of range" style="width:160px;background:#D0D0D0;float:left;border: 1px dashed grey;padding:5px;float:right;overflow:auto;">
						<div class="column_description ui-state-disabled" style="float:left;padding-bottom:4px;">Stories out of range</div>
						<div style="float:right;padding-bottom:4px;"><span class="toolbar_delete"><button id="btn_delete_stories">Trash</button></span></div>
						<div id="pending_stories" class="column_selected_stories" style="width:100%;min-height:30px;background:#D0D0D0;border:0px;overflow:auto;">
						<?php get_box_content_out_of_range($rows, $str_cols_out_of_range); ?>
						<?php get_box_content_out_of_range($rows, 'pending_stories'); ?>
						</div>
					</div>
				
				</div>

			</div>
		
		</div>

		
	</div>
	


	
	<div id="grid">
	
		<div class="admin-panel clearfix">
			<p>

				<div style="float:left;width:50%" class="grid-settings">

					<h4><i class="fas fa-th" aria-hidden="true"></i> Grid settings</h4>
					<p>
						<input type="checkbox" name="grid_active" id="grid_active" value="1" <?php if($arr['grid_active'] == 1) {echo 'checked="checked"';}?>> Active
					</p>
					<p>
						Grid area relative to content | article
						<br>
						<input type="radio" value="0" name="grid_area" <?php if($arr['grid_area'] == 0) {echo 'checked="checked"';}?>> Above 
						<br>
						<input type="radio" value="1" name="grid_area" <?php if($arr['grid_area'] == 1) {echo 'checked="checked"';}?>> Above content and next to any sidebar
						<br>
						<input type="radio" value="2" name="grid_area" <?php if($arr['grid_area'] == 2) {echo 'checked="checked"';}?>> Below content and next to any sidebar
						<br>
						<input type="radio" value="3" name="grid_area" <?php if($arr['grid_area'] == 3) {echo 'checked="checked"';}?>> Below
					</p>
					<p style="margin-top:40px">
						<span class="toolbar"><button class="add-grid-item">Add grid item <i class="far fa-plus-square" aria-hidden="true"></i></button><span>
						<span class="toolbar"><button name="btnSaveGrid" id="btnSaveGrid">Save grid</button></span>
						<span id="ajax_spinner_grid" style='display:none'><img src="css/images/spinner.gif"></span>
						<span id="ajax_status_grid" style='display:none'></span>
					</p>
					<div style="background-color:rgb(200,200,200);padding:10px;border:1px dashed black">
						<span class="toolbar"><button name="btnGridExport" id="btnGridExport">Export content</button></span>
						<span class="toolbar"><button name="btnGridImport" id="btnGridImport">Import content</button></span>
						<span class="toolbar"><button name="btnGridImportSave" id="btnGridImportSave" style="display:none">Save import (reload page)</button></span>

						<textarea id="grid_json" style="width:95%;display:none"></textarea>
						<div id="grid_json_clipboard_link">
							<a href="#" id="grid_json_clipboard" class="hidden">Copy to clipboard</a>
						</div>
					</div>
					
				</div>

				<div style="float:left;width:20%;margin-left:5%" class="grid-cell-settings">
				
					<h4>Grid cell settings</h4>
					<div>
						Custom CSS class (grid wrapper)<br>
						<input type="text" name="grid_custom_classes" id="grid_custom_classes" size="30" id="grid-class" value="<?php echo $arr['grid_custom_classes']?>">
						<a class="colorbox_grid_class" href="pages_css.php?token=<?php echo $_SESSION['token'];?>&pages_id=<?php echo $_GET['id'];?>&return=true"><i class="far fa-question-circle"></i></a>
					</div>
					
					<div style="margin:20px 0;">
						Grid cell template
						<br>
						<input type="radio" value="0" name="grid_cell_template" disabled <?php if($arr['grid_cell_template'] == 0) {echo 'checked="checked"';}?>> Image above heading
						<br>
						<input type="radio" value="1" name="grid_cell_template" disabled <?php if($arr['grid_cell_template'] == 1) {echo 'checked="checked"';}?>> Heading above image
					</div>

				</div>

				<div style="float:left;width:15%;margin-left:5%;padding-left:20px" class="grid-cell-settings">
					<h4>&nbsp;</h4>
					Grid images height
					<br>
					<input id="grid_cell_image_height" type="text" value="<?php echo $arr['grid_cell_image_height']?>" readonly style="border:1px dotted grey;width:3em">
					<br>
					<div style="float:left;margin:20px">
						400px
						<div id="grid_image_slider_height" style="height:100px;margin:10px 0"></div>
						100px
					</div>

				</div>

			</p>
		</div>
		
		<p>
			<form id="gridform"  method="post">
				
				<div id="wrapper-grid" class="grid-edit clearfix">
				<?php
				$html_grid = get_grid_edit($id, $arr['grid_active'], $arr['grid_content'], $arr['grid_custom_classes'], $arr['grid_cell_template'], $arr['grid_cell_image_height']);
				echo $html_grid;				
				?>
				</div>
				
			</form>
		</p>
		
		
	</div>

	
	
	<div id="story">

		<div class="admin-panel">

			<h4><i class="fas fa-leaf" aria-hidden="true"></i> Story</h4>
			<p>
				How this page may be shown as a story  
			</p>
			<table style="width:100%;" class="page-settings">
				<tr>
					<td style="width:25%;">
					<label for="tag"><i class="fas fa-tags" aria-hidden="true"></i> Tag page:</label>
					<br />
					<input type="text" name="tag" id="tag" title="Tag page" style="width:150px;" maxlength="25" />		
					<span class="toolbar_add"><button id="btn_add_tag" style="margin:0px" type="submit">Add</button></span>
					<span id="ajax_spinner_tag" style="display:none;"><img src="css/images/spinner.gif"></span>
					<span id="ajax_status_tag" style="display:none;"></span>										
					</td>
					<td style="width:50%;">
					<ul id="tags">
					<?php 
					if(isset($arr['tag']) && strlen($arr['tag']) >0) {
						$tags = explode(",", $arr['tag']);
						foreach ($tags as $tag){
							echo '<li>'.$tag.'<i class="far fa-trash-alt" aria-hidden="true"></i></li>';
						}
					}
					?>
					</ul>
					</td>
					<td style="float:right;">
					<span class="toolbar"><button id="btn_story_save" style="margin:0px" type="submit">Save & preview</button></span>
					<span id="ajax_spinner_story" style="display:none;"><img src="css/images/spinner.gif"></span>
					<span id="ajax_status_story" style="display:none;"></span>										
					</td>
				</tr>
			</table>
			
		</div>

		<div class="admin-panel">

			<table style="width:100%;" class="page-settings">
				<tr>
					<td style="width:33%;">
					<input type="checkbox" name="story_promote" id="story_promote" value="1" <?php if($arr['story_promote'] == 1) {echo 'checked';}?>> promote story to front page
					</td>
					<td>
					<input type="checkbox" name="story_link" id="story_link" value="1" <?php if($arr['story_link'] == 1) {echo 'checked';}?>> link story (default)
					</td>
				</tr>
			</table>
			
		</div>

		
		<div class="admin-panel">

			<?php
			$story_event_date = ($arr['story_event_date']>'2000-01-01 00:00') ? new DateTime(utc_dtz($arr['story_event_date'], $dtz, 'Y-m-d H:i')) : '';
			?>
		
			<table style="width:100%;" class="page-settings">
				<tr>
					<td style="width:33%;">
					<input type="checkbox" name="story_event" id="story_event" value="1" <?php if($arr['story_event'] == 1) {echo 'checked';}?>> set story as event
					</td>
					<td>
					<label for="story_event_date">Event date:</label><br />
					<input type="text" name="story_event_date" id="story_event_date" value="<?php if($story_event_date) {echo $story_event_date->format('Y-m-d');} ?>" title="yyyy-mm-dd" />
					</td>
					<td>
					<label for="story_event_time">Time:</label><br />
					<input type="text" id="story_event_time" size="5" maxlength="5" title="add hours and minutes hh:mm" value="<?php if($story_event_date) {echo $story_event_date->format('H:i');} ?>">
					</td>
					<td>&nbsp;
					</td>
				</tr>
			</table>
			
		</div>
		
		<div class="admin-panel">

			<table style="width:100%;" class="page-settings">
				<tr>
					<td style="width:33%;">
					<input type="checkbox" name="story_custom_title" id="story_custom_title" value="1" <?php if($arr['story_custom_title'] == 1) {echo 'checked';}?>>
					remove title from story
					</td>
					<td style="width:33%;">
					<label for="story_custom_title">Custom title (if set):</label><br />
					<input type="text" size="25" id="story_custom_title_value" title="use default page title or set a custom story title" value="<?php echo $arr['story_custom_title_value']; ?>">
					</td>
					<td>
					</td>
			</table>
			
		</div>


		<div class="admin-panel">

			<table style="width:100%;" class="page-settings">
				<tr>
					<td style="width:33%;">
						<label for="story_css_class">Background css class:</label><br />
						<input type="text" id="story_css_class" name="story_css_class" class="code" value="<?php echo $arr['story_css_class']; ?>">
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<div style="width:100%;overflow:auto;" id="set_custom_css">
					<?php

					foreach($css_custom as $key => $value) {
						echo '<div class="'.$key.'" data-css="'.$key.'" style="float:left; width:100px;height:100px;margin:5px;padding:10px" title="'.$value.'">';
							echo '<p>'.$value.'</p>';
						echo '</div>';
					}
					?>
					</div>
					
					</td>
			</table>
			
		</div>
		
		<div class="admin-panel">
		
			<table class="page-settings">
				<tr>
					<td style="vertical-align:top;width:35%;">
						<h4>Story</h4> 
						<p>
							<i>&lt= 33% of page width<i>
						</p>
						<div>
							<textarea name="story_content" id="story_content" class="<?php echo $class_editor; ?>" style=""><?php echo $arr['story_content'];?></textarea>
						</div>
					</td>
					<td style="width:5%;">
					</td>
					<td style="vertical-align:top;width:60%;">
						<h4>Story wide:</h4>
						<p>
							<i>&gt;  33% of page width<i>
						</p>
						<div>
						<div style="float:left;">
							Teaser image settings -> story wide  
							<br /><input type="radio" name="story_wide_teaser_image" id="story_wide_teaser_image" value="0" <?php if($arr['story_wide_teaser_image'] == 0) {echo 'checked';}?>> exclude
							<br /><input type="radio" name="story_wide_teaser_image" id="story_wide_teaser_image" value="1" <?php if($arr['story_wide_teaser_image'] == 1) {echo 'checked';}?>> include full size above story
							<br /><input type="radio" name="story_wide_teaser_image" id="story_wide_teaser_image" value="2" <?php if($arr['story_wide_teaser_image'] == 2) {echo 'checked';}?>> align image left|right (set size and alignment in the page that shows stories)
						</div>
						</div>
					</td>
				</tr>
			</table>

		</div>
		
		<p>
			<div id="story_samples" style="padding:10px;"></div>
		</p>
	
	</div>

	
	
	<div id="rss">

		<div class="admin-panel">

			<h4><i class="fas fa-leaf" aria-hidden="true"></i> RSS</h4>

			<p>
				<span style="font-size:2.14em;padding:5px;"><?php if(isset($arr['title'])){echo $arr['title'];}?></span>
			</p>
			<div style="padding:5px;">
				RSS description</br />
				<textarea name="rss_description" id="rss_description" style="width:90%;min-height:100px;"><?php echo $arr['rss_description'];?></textarea>
			</div>

			<div style="padding:5px;">
				<input type="checkbox" name="rss_promote" id="rss_promote" value="1" <?php if($arr['rss_promote'] == 1) {echo 'checked';}?>>
				include in RSS feeds | <img src="../cms/css/images/feed-icon-14x14.png"> <i>published pages, public access</i>
				<p>
					<span class="toolbar"><button class="content_save">Save</button></span>
					<span class="ajax_spinner_content" style='display:none'><img src="css/images/spinner.gif"></span>
					<span class="ajax_status_content" style='display:none'></span>
				</p>
				<a href="rss_preview.php?id=<?php echo $arr['pages_id']; ?>" target="_blank">preview</a>
			</div>

		</div>
	
	</div>
	
	
	<div id="move">
	
		
	
	</div>

	
	
	<div id="rights">

		<div class="admin-panel">
		
			<?php
			// user rights to this page
			$pages_rights = new PagesRights();
			$users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;

			// rights to this page
			$users_rights = $pages_rights->getPagesUsersRightsMeta($id, $users_id);
			$groups_rights = $pages_rights->getPagesGroupsRightsMeta($id);
			?>
			
			<table style="width:100%">
				<tr>
					<td>
						<input id="users_find" name="users_find" value="<?php if(isset($_REQUEST['users_find'])) {echo $_REQUEST['users_find']; } ?>"  style="width:300px;" />
						<input type="hidden" id="pid" /><span class="toolbar_add"><button id="btn_add_users_rights">Add user</button></span>
					</td>
					<td>
						<input id="groups_find" name="groups_find" value="<?php if(isset($_REQUEST['groups_find'])) {echo $_REQUEST['groups_find']; } ?>" style="min-width:300px;" />
						<input type="hidden" id="gid" /><span class="toolbar_add"><button id="btn_add_groups_rights">Add group</button></span>
					</td>
					<td>					
						<span id="ajax_spinner_rights" style=display:none><img src="css/images/spinner.gif"></span>
						<span id="ajax_status_rights" style="display:none;"></span>
					</td>
				</tr>
			</table>
			
			<p>
			
				<?php
				echo '<table id="rights" class="paging" style="min-width:600px;">';
					echo '<thead class="ui-widget-header">';
					echo '<tr>';
						echo '<th style="width:80%;">users / groups';
						echo '</th>';
						echo '<th>read';
						echo '</th>';
						echo '<th>edit';
						echo '</th>';
						echo '<th>create';
						echo '</th>';
						echo '<th>delete';
						echo '</th>';
					echo '</tr>';
					echo '</thead>';
					echo '<tbody class="ui-widget-content">';
					// css style odd-even rows
					$class = 'even';			
					foreach($users_rights as $r) {
						?><input type="hidden" name="r_id[]" id="r_id[]" value="<?php echo $r['pages_rights_id']; ?>" /><?php
						// switch odd-even
							$class = ($class=='even') ? 'odd' : 'even';				
							echo '<tr id="row-'. $r['pages_rights_id'] .'" class="paging_'. $class .'">';
							echo '<td class="paging">';
							echo $r['first_name'] .' '. $r['last_name'] .', '. $r['email'];
							echo '</td>';
							echo '<td class="paging">';
							?><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value="<?php echo $r['pages_rights_id']; ?>" <?php if($r['rights_read'] == 1) {echo 'checked';}?> /><?php
							echo '</td>';
							echo '<td class="paging">';
							?><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value="<?php echo $r['pages_rights_id']; ?>" <?php if($r['rights_edit'] == 1) {echo 'checked';}?> /><?php
							echo '</td>';
							echo '<td class="paging">';
							?><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value="<?php echo $r['pages_rights_id']; ?>" <?php if($r['rights_create'] == 1) {echo 'checked';}?> /><?php
							echo '</td>';
							echo '<td class="paging">';
							?><span class="toolbar"><button class="btn_delete_rights" value="<?php echo $r['pages_rights_id']; ?>">Delete</button></span><?php
							echo '</td>';
						echo '</tr>';
					}
					foreach($groups_rights as $r) {
						?><input type="hidden" name="r_id[]" id="r_id[]" value="<?php echo $r['pages_rights_id']; ?>" /><?php
						// switch odd-even
						$class = ($class=='even') ? 'odd' : 'even';				
						echo '<tr id="row-'. $r['pages_rights_id'] .'" class="paging_'. $class .'">';
							echo '<td class="paging">';
							echo $r['title'] .' (group)';
							echo '</td>';
							echo '<td class="paging">';
							?><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value="<?php echo $r['pages_rights_id']; ?>" <?php if($r['rights_read'] == 1) {echo 'checked';}?> /><?php
							echo '</td>';
							echo '<td class="paging">';
							?><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value="<?php echo $r['pages_rights_id']; ?>" <?php if($r['rights_edit'] == 1) {echo 'checked';}?> /><?php
							echo '</td>';
							echo '<td class="paging">';
							?><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value="<?php echo $r['pages_rights_id']; ?>" <?php if($r['rights_create'] == 1) {echo 'checked';}?> /><?php
							echo '</td>';
							echo '<td class="paging">';
							?><span class="toolbar"><button class="btn_delete_rights" value="<?php echo $r['pages_rights_id']; ?>">Delete</button></span><?php
							echo '</td>';
						echo '</tr>';
						echo '</tbody>';
					}
				echo '</table>';
				
				?>
			
			</p>
			
			<p>
				<span class="toolbar"><button id="btn_rights_save">Save rights</button></save>
			</p>
		
		</div>
	
	</div>


	
	<div id="meta">

		<div class="admin-panel">
			<p>
				<span class="toolbar"><button id="btn_save_meta">Save</button></span>
				<span id="ajax_spinner_meta" style='display:none'><img src="css/images/spinner.gif"></span>
				<span id="ajax_status_meta" style='display:none'></span>
			</p>
			<p>
				<label for="meta_description">Meta descripton: </label>
				<br />
				<input type="text" name="meta_description" id="meta_description" title="Enter meta description" style="width:90%;" maxlength="200" value="<?php if(isset($arr['meta_description'])){echo $arr['meta_description'];}?>" />
			</p>
			<p>
				<label for="meta_keywords">Meta keywords: </label>
				<br />
				<input type="text" name="meta_keywords" id="meta_keywords" title="Enter meta keywords" style="width:90%;" maxlength="200" value="<?php if(isset($arr['meta_keywords'])){echo $arr['meta_keywords'];}?>" />
				<span class="toolbar_gear"><button id="suggest_meta_keywords" title="Get words from title and story, reload page if words from story doesn't show.">Suggest</button></span>
				<span id="ajax_spinner_meta_keywords" style='display:none'><img src="css/images/spinner.gif"></span>
				<span id="ajax_status_meta_keywords" style='display:none'></span>			
			</p>
			<p>
				<label for="meta_additional">Meta additional tags (max 255 characters)</label>
				<br />
				<textarea id="meta_additional" id="meta_additional" style="width:90%;height:100px;"><?php if(isset($arr['meta_additional'])){echo htmlspecialchars(stripcslashes($arr['meta_additional']));}?></textarea>
			</p>	
			<p>
			<label for="meta_robots">Meta robots (if not set default action taken: index, follow): </label>
			<br />
			<select name="meta_robots" id="meta_robots" class="code">
				<option value=""></option>
				<option value="index, follow" <?php if($arr['meta_robots']=="index, follow"){echo 'selected';}?>>index, follow (default)</option>
				<option value="noindex, follow" <?php if($arr['meta_robots']=="noindex, follow"){echo 'selected';}?>>noindex, follow</option>
				<option value="index, nofollow" <?php if($arr['meta_robots']=="index, nofollow"){echo 'selected';}?>>index, nofollow</option>
				<option value="noindex, nofollow" <?php if($arr['meta_robots']=="noindex, nofollow"){echo 'selected';}?>>noindex, nofollow</option>
			</select>
			</p>
		</div>

	</div>
		
		
		
	<div id="publish">	
	
		<div class="admin-panel">

			<span style="">Page status:</span><br>
			<select name="status" id="status" style="font-weight:bold;padding:5px;border:1px dotted #000;width:150px;" disabled="disabled">
				<option value="1" <?php if($arr['status']==1) { echo ' selected=selected';} ?>>draft</option>
				<option value="2" <?php if($arr['status']==2) { echo ' selected=selected';} ?>>published</option>
				<option value="3" <?php if($arr['status']==3) { echo ' selected=selected';} ?>>archived</option>
				<option value="4" <?php if($arr['status']==4) { echo ' selected=selected';} ?>>pending</option>
				<option value="5" <?php if($arr['status']==5) { echo ' selected=selected';} ?>>trash</option>
			</select>
			
		</div>

		<div class="admin-panel">
			<div class="clearfix">
				<div class="publish-step">
					<h4>Step 1</h4>
				</div>
				<div class="publish-step">
					<h4><i class="fas fa-user-secret" aria-hidden="true"></i> Page visibilty</h4>
					
					<input type="radio" name="access" id="access" value="0" <?php if($arr['access'] == 0) {echo 'checked';}?>> logged in users with rights to read
					<br />
					<input type="radio" name="access" id="access" value="1" <?php if($arr['access'] == 1) {echo 'checked';}?>> logged in users
					<br />
					<input type="radio" name="access" id="access" value="2" <?php if($arr['access'] == 2) {echo 'checked';}?>> everyone (public access)
				</div>
			</div>
		</div>

		
		<div class="admin-panel">
			
			<div class="clearfix">
				<div class="publish-step">
					<h4>Step 2</h4>
				</div>
				<div class="publish-step">
					<h4><i class="far fa-check-square" aria-hidden="true"></i> Friendly URL</h4>

					<label for="pages_id_link">Friendly URL, use following title based link: </label>
					<br />
					<input type="text" name="pages_id_link" id="pages_id_link" title="Enter id link" size="50" maxlength="100" value="<?php if(isset($arr['pages_id_link'])){echo $arr['pages_id_link'];}?>" />
					
					<span class="toolbar_gear"><button id="seo_link">Suggest</button></span>
					<span class="toolbar"><button id="btn_save_seo_link">Save friendly URL</button></span>
					<span id="ajax_spinner_seo_link" style='display:none'><img src="css/images/spinner.gif"></span>
					<span id="ajax_status_seo_link" style='display:none'></span>
					<p>
					<input type="checkbox" id="stopwords" name="stopwords" checked="checked"> Reduce common words 
					</p>
				</div>
			</div>
		</div>

		<div class="admin-panel">
		
			<div class="clearfix">
				<div class="publish-step">
					<h4>Step 3</h4>
				</div>
				<div class="publish-step">

					<h4><i class="fas fa-play" aria-hidden="true"></i> Publish</h4>

					<p>
						<?php
						$date_start = ($arr['utc_start_publish']>'2000-01-01 00:00') ? new DateTime(utc_dtz($arr['utc_start_publish'], $dtz, 'Y-m-d H:i:s')) : new DateTime(get_utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s'));
						$date_end = ($arr['utc_end_publish']>'2000-01-01 00:00') ? new DateTime(utc_dtz($arr['utc_end_publish'], $dtz, 'Y-m-d H:i:s')) : null;
						?>
						
						<label for="date_start">Start publish:</label><br />
						<input type="text" id="date_start" title="yyyy-mm-dd" value="<?php if($date_start) {echo $date_start->format('Y-m-d');} ?>">
						<input type="text" id="time_start" size="5" maxlength="5" title="add hours and minutes hh:mm" value="<?php if($date_start) {echo $date_start->format('H:i');} ?>">
					</p>
					<p>
						<label for="date_end">End publish: (if set)</label><br />
						<input type="text" id="date_end" value="<?php if($date_end) {echo $date_end->format('Y-m-d');} ?>">
						<input type="text" id="time_end" size="5" maxlength="5" title="add hours and minutes hh:mm" value="<?php if($date_end) {echo $date_end->format('H:i');} ?>">
					</p>


					<?php if(get_role_CMS('author') == 1) { ?>
						<div style="padding:10px 0;">
							<span class="toolbar_publish"><button class="btn_pages_publish" id="<?php echo $id;?>" style="border:3px solid black">Publish</button></span>
							<span id="ajax_spinner_publish" style="display:none;"><img src="css/images/spinner.gif"></span>
							<span id="ajax_status_publish" style="display:none;"></span>
						</div>
					<?php } else { ?>
						<p>
							<i>Contact someone with rights to publish this page: </i><span class="toolbar"><button class="btn_pages_pending" id="<?php echo $id;?>">Ready to publish (pending)</button></span>
						</p>
					<?php } ?>
				
				</div>

			</div>
			
		</div>
		<div class="admin-panel">
			<p>
				<label for="pages_id_link">Title tag in head (leave field empty to use default -> page title): </label>
				<br />
				<input type="text" name="title_tag" id="title_tag" title="Override default title tag" size="50" maxlength="100" value="<?php if(isset($arr['title_tag'])){echo $arr['title_tag'];}?>" />
			</p>
		</div>

		

		<div class="admin-panel">
			Site publish guideline:
			<p>
			<div style="border:3px dotted #FFF;padding:20px;">
				<i>
				<?php 
				if(isset($_SESSION['site_publish_guideline'])) {
					echo nl2br($_SESSION['site_publish_guideline']);
				}					
				?>
				</i>
				</div>
			</p>
		</div>



		<div class="admin-panel">

			<div style="float:right;overflow:auto;height:400px;">
				<div style="float:right;">
				<span class="toolbar"><button class="ajax_history" id="<?php echo $id;?>">Display page history</button></span>
				&nbsp;<span id="ajax_spinner_history" style=display:none><img src="css/images/spinner_1.gif"></span>
				</div>
				<div id="pages_history" style="float:right;display:none;padding:10px;width:400px;border:1px solid #000;" class="ui-widget ui-widget-content"></div>
			</div>

			<p>
				<span class="toolbar" style="padding:10px;"><button class="btn_pages_archive" id="<?php echo $id;?>">Move page to Archive</button></span>
				<span class="toolbar_trash" style="padding:10px;"><button class="btn_pages_trash" id="<?php echo $id;?>">Move page to Trash</button></span>
				<span class="toolbar" style="padding:10px;"><button class="btn_pages_delete" id="<?php echo $id;?>" style="display:none;">Delete page</button></span>
				<span id="ajax_spinner_change_status" style="display:none;"><img src="css/images/spinner.gif"></span>
				<span id="ajax_status_change_status" style="display:none;"></span>
			</p>
		
		</div>


	</div>
	
	
</div>

<?php include_once 'includes/inc.footer_cms.php'; ?>
</body>
</html>