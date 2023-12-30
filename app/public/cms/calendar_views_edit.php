<?php 
// include core
//--------------------------------------------------
require_once 'includes/inc.core.php';

if(!get_role_CMS('administrator') == 1) {die;}

if(!isset($_SESSION['site_id'])) {
	echo 'Site is not set!';
	exit;
}

// wysiwyg editor
$wysiwyg_editor = isset($_SESSION['site_wysiwyg']) ? get_editor_settings($editors, $_SESSION['site_wysiwyg']) : null;

// css files
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
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

// javascript files
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js',
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js',	
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/js/functions.js'
	//CMS_DIR.'/cms/libraries/tinymce/plugins/moxiemanager/js/moxman.loader.min.js'
);

// javascript files... add wysiwyg file
if (is_array($wysiwyg_editor)) {
	if(file_exists(CMS_ABSPATH .'/cms/libraries/'.$wysiwyg_editor['include_js_file'])) {
		array_push($js_files, CMS_DIR.'/cms/libraries/'.$wysiwyg_editor['include_js_file']);
	}
}

// include header
$page_title = "Calendar events views - edit";
$body_style = "width:1190px;";
include_once 'includes/inc.header_minimal.php';


?>


<script>

	function calendar_views_preview(id) {
		w=window.open('calendar_preview.php?token=<?php echo $_SESSION['token']; ?>&id='+id+'&type=view','','width=1280,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
		w.focus();
	}

	$(document).ready(function() {

		$("#back").click(function(event) {
			event.preventDefault();
			history.back(1);
		});
		$( ".toolbar_back button" ).button({
			icons: { secondary: "ui-icon-arrowreturnthick-1-w" },
			text: true
		});
		$( ".toolbar button" ).button({
			icons: { },
			text: true
		});
		$( ".toolbar_preview button" ).button({
			icons: { secondary: "ui-icon-newwin" },
			text: true
		});
		$( ".toolbar_close button" ).button({
			icons: { secondary: "ui-icon-close" },
			text: true
		});

		$('#btn_calendar_views_save').click(function(event){
			event.preventDefault();
			var action = "calendar_views_save";
			var token = $("#token").val();
			var calendar_views_id = $("#calendar_views_id").val();
			var active = $('input:checkbox[name=active]').is(':checked') ? 1 : 0;
			var public = $('input:checkbox[name=public]').is(':checked') ? 1 : 0;
			var name = $("#name").val();
			var description = $("#description").val();
			$.ajax({
				type: 'POST',
				beforeSend: function() { loading = $('#ajax_spinner_calendar_views_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_calendar_views_edit').hide()",700)},
				url: 'calendar_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&active=" + active + "&public=" + public + "&calendar_views_id=" + calendar_views_id + "&name=" + name + "&description=" + description,
				success: function(newdata){									
					ajaxReply('','#ajax_status_calendar_views_edit');
				},
			});
		});

		$('#btn_selections_delete').click(function(event){
			event.preventDefault();
			var action = "calendar_views_delete";
			var token = $("#token").val();
			var calendar_views_id = $("#calendar_views_id").val();
			$.ajax({
				type: 'POST',
				beforeSend: function() { loading = $('#ajax_spinner_calendar_views_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_calendar_views_edit').hide()",700)},
				url: 'calendar_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&calendar_views_id=" + calendar_views_id,
				success: function(newdata){	
					ajaxReply('','#ajax_status_calendar_views_edit');
				},
			});
		});
		
		$('#btn_calendar_category_add').click(function(event){
			event.preventDefault();
			var action = "calendar_views_category_add";
			var token = $("#token").val();
			var calendar_views_id = $("#calendar_views_id").val();
			var calendar_categories_id = $("#calendar_categories_id option:selected").val();
			var calendar_categories_text = $("#calendar_categories_id option:selected").text();

			$.ajax({
				type: 'POST',
				beforeSend: function() { loading = $('#ajax_spinner_calendar_views_category_add').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_calendar_views_category_add').hide()",700)},
				url: 'calendar_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&calendar_views_id=" + calendar_views_id + "&calendar_categories_id=" + calendar_categories_id + "&calendar_categories_text=" + calendar_categories_text,
				success: function(newdata){	
					ajaxReply('','#ajax_status_calendar_views_category_add');
					if(get_number(newdata)) {
						$("#calendar_categories").prepend('<li style="height:20px;border:1px solid #E8E8E8;padding:2px;margin:2px;" id="calendar_categories_id_'+newdata+'">'+calendar_categories_text+'<input style="float:right;" type="checkbox" name="calendar_views_members_id[]" value="'+newdata+'" /></li>').hide().fadeIn('fast');
					}
				},
			});
		});

		$('#btn_calendar_views_delete').click(function(event){
			event.preventDefault();
			var action = "calendar_views_delete";
			var token = $("#token").val();
			var calendar_views_id = $("#calendar_views_id").val();
			
			$.ajax({
				type: 'POST',
				beforeSend: function() { loading = $('#ajax_spinner_calendar_views_delete').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_calendar_views_delete').hide()",700)},
				url: 'calendar_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&calendar_views_id=" + calendar_views_id,
				success: function(newdata){	
					$("#calendar_views_form").empty();
				},
			});
		});	
		
		
		
		$('#btn_calendar_category_delete').click(function(event){
			event.preventDefault();
			var action = "calendar_views_category_delete";
			var token = $("#token").val();
			var calendar_views_members_id = [];
			
			$("input[name='calendar_views_members_id[]']:checked").each(function (){
				calendar_views_members_id.push(parseInt($(this).val()));
			});

			$.ajax({
				type: 'POST',
				beforeSend: function() { loading = $('#ajax_spinner_calendar_views_category_delete').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_calendar_views_category_delete').hide()",700)},
				url: 'calendar_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&calendar_views_members_id=" + calendar_views_members_id,
				success: function(newdata){	
					ajaxReply('','#ajax_status_calendar_views_category_delete');
					var myArray = newdata.split(',');
					
					for (var i = 0; i < myArray.length; i++) {
						var arrayItem = myArray[i];
						$('#calendar_categories_id_'+arrayItem).remove();
					}				
				},
			});
		});	
		
		$("#calendar_categories").sortable({
			placeholder: "ui-state-highlight",
			axis: 'y',
			opacity: 0.6, 
			cursor: 'move', 
			update: function() {
				var token = $("#token").val();
				var order = $(this).sortable("serialize") + "&action=update_categories_position&token=" + token;
					$.post("calendar_edit_ajax.php", order, function(theResponse){
					$("#ajax_result").html(theResponse);
				});
			}				
		});
		$( "#sortable_calendar_categories" ).disableSelection();
		
	});

</script>

<?php

$calendar = new Calendar();

if(isset($_GET['token'])){
	// only accept $_POST from this token
	if($_GET['token'] == $_SESSION['token']) {
		$id = $_GET['id'];
		$row = $calendar->getCalendarViews($id);
		if(!$row) {
			die;
		}
		$active = ($row['active']==1) ? " checked=checked" : null;
		$public = ($row['public']==1) ? " checked=checked" : null;
		
		// get this view categories - if none saved enable delete button
		$rows = $calendar->getCalendarViewCategories($id);
		$button = (!$rows) ? null : ' disabled=disabled';
		
		echo '<input type="hidden" name="token" id="token" value="'.$_SESSION['token'].'">';
		echo '<input type="hidden" name="calendar_views_id" id="calendar_views_id" value="'.$id.'">';

		echo '<table style="width:100%;">';
			echo '<tr>';
				echo '<td>';
				echo '<h3 class="admin-heading">Edit calendar view "'. $row['name'] .'"</h3>';
				echo '</td>';
				echo '<td style="text-align:right;">';
				echo '<span class="toolbar_close"><button type="submit" onclick="parent.$.colorbox.close(); return false;">Close</button></span>&nbsp;';
				echo '<span class="toolbar_preview"><button type="submit" onclick="calendar_views_preview('.$id.')">Preview</button></span>';
				echo '</td>';
			echo '</tr>';
		echo '</table>';
		echo '<hr />';

		echo '<div class="admin-area-outer  ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';
				
				echo '<div style="width:100%;" id="calendar_views_form">';

					echo '<table style="padding:10px 0 10px 0;width:100%;"><tr><td style="width:30%;">';
					echo 'Name<br /><input type="text" name="name" id="name" value="'. $row['name'] .'" style="width:80%;">';
					echo '</td>';
					echo '<td style="width:70%;">';
					echo 'Description<br /><input type="text" name="description" id="description" value="'. $row['description'] .'" style="width:100%;">';
					echo '</td></tr></table>';
					
					echo '<table style="padding:0;width:100%;"><tr><td style="width:20%;">';
					echo '<input type="checkbox" name="active" id="active" value="1" "'.$active.'> Active';
					echo '</td>';
					echo '<td style="width:20%;">';
					echo '<input type="checkbox" name="public" id="public" value="1" "'.$public.'> Public';
					echo '</td>';
					echo '<td style="width:60%;text-align:right;">';
					
					echo '<span class="toolbar"><button id="btn_calendar_views_save" value="btn_calendar_views_save">Save view</button></span>&nbsp;';
					echo '<span class="toolbar"><button id="btn_calendar_views_delete" value="btn_calendar_views_delete" '.$button.' title="">Delete view</button></span>';
					echo '<br />';
					echo '<span id="ajax_spinner_calendar_views_edit" style="display:none;"></span>';
					echo '<span id="ajax_status_calendar_views_edit" style="display:none;"></span>';
					echo '</td></tr></table>';
				
				echo '</div>';
				echo '<hr />';
				echo '<h5 class="admin-heading">Categories connected to this calendar view</h5>';
				echo '<div style="width:100%; padding: 20px 0 20px 0;">';
		
				$rows_categories = $calendar->getCalendarCategories();
			
				if($rows_categories) {
					echo '<select style="min-width:200px;" id="calendar_categories_id">';
						foreach($rows_categories as $rows_category) {
							echo '<option value="'.$rows_category['calendar_categories_id'] .'">'. $rows_category['category'].'</option>';
						}
					echo '</select>';
					echo ' <span class="toolbar"><button id="btn_calendar_category_add">Add category to view</button></span>';
					echo '<span id="ajax_spinner_calendar_views_category_add" style="display:none;"></span>';
					echo '<span id="ajax_status_calendar_views_category_add" style="display:none;"></span>';
				}
		
				echo '<p>Drag and drop to change position</p>';
				
				echo '<div class="clearfix">';
					echo '<div style="width:400px;padding:10px;float:left;" id="sortable-wrapper" class="sortable_units">';
							echo '<ul id="calendar_categories" class="ui-sortable">';
								if($rows) {
									foreach($rows as $row) {
										echo '<li id="calendar_categories_id_'. $row['calendar_views_members_id'] .'">'. $row['category'];																
											echo '<input style="float:right;" name="calendar_views_members_id[]" type="checkbox" value="'. $row['calendar_views_members_id'] .'" />';
										echo '</li>';
									}
								}
							echo '</ul>';
					echo '</div>';
					echo '<div style="float:right;padding:10px;">';
						echo '<span class="toolbar"><button id="btn_calendar_category_delete">Delete selected categories</button></span>';
						echo '<span id="ajax_spinner_calendar_views_category_delete" style="display:none;"></span>';
						echo '<span id="ajax_status_calendar_views_category_delete" style="display:none;"></span>';
					echo '</div>';
				echo '</div>';
			
			echo '</div>';
		
		echo '</div>';
	echo '</div>';
		
		
	}
}
?>

<?php 
// load javascript files
foreach ( $js_files as $js ): ?>
	<script  src="<?php echo $js; ?>"></script>
<?php endforeach; ?>

<div class="footer-wrapper">
	<?php include_once 'includes/inc.footer_cms.php'; ?>
</div>

</body>
</html>