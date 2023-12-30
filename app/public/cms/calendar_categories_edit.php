<?php 
// include core
//--------------------------------------------------
require_once 'includes/inc.core.php';

if(!get_role_CMS('administrator') == 1) { header('Location: index.php'); die;}

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
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/cms/libraries/fileuploader/fileuploader.js'
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
$page_title = "Calendar events categories - edit";
$body_style = "width:1190px;";
include_once 'includes/inc.header_minimal.php';


// load javascript files
//--------------------------------------------------
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>


<script>

	function calendar_categories_preview(id) {
		w=window.open('calendar_preview.php?token=<?php echo $_SESSION['token']; ?>&id='+id+'&type=category','','width=1280,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
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
		$( ".toolbar_preview button" ).button({
			icons: { secondary: "ui-icon-newwin" },
			text: true
		});
		$( ".toolbar_close button" ).button({
			icons: { secondary: "ui-icon-close" },
			text: true
		});

		$( ".toolbar button" ).button({
		});

		$('#btn_calendar_categories_save').click(function(event){
			event.preventDefault();
			var action = "calendar_categories_save";
			var token = $("#token").val();
			var calendar_categories_id = $("#calendar_categories_id").val();
			var active = $('input:checkbox[name=active]').is(':checked') ? 1 : 0;
			var public = $('input:checkbox[name=public]').is(':checked') ? 1 : 0;
			var rss = $('input:checkbox[name=rss]').is(':checked') ? 1 : 0;
			var category = $("#category").val();
			var description = $("#description").val();
			$.ajax({
				type: 'POST',
				beforeSend: function() { loading = $('#ajax_spinner_calendar_categories_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_calendar_categories_edit').hide()",700)},
				url: 'calendar_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&active=" + active + "&public=" + public + "&rss=" + rss + "&calendar_categories_id=" + calendar_categories_id + "&category=" + category + "&description=" + description,
				success: function(newdata){									
					ajaxReply('','#ajax_status_calendar_categories_edit');
				},
			});
		});

		$('#btn_calendar_categories_delete').click(function(event){
			event.preventDefault();
			var action = "calendar_categories_delete";
			var token = $("#token").val();
			var calendar_categories_id = $("#calendar_categories_id").val();
			$.ajax({
				type: 'POST',
				beforeSend: function() { loading = $('#ajax_spinner_calendar_categories_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_calendar_categories_edit').hide()",700)},
				url: 'calendar_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&calendar_categories_id=" + calendar_categories_id,
				success: function(newdata){	
					ajaxReply('','#ajax_status_calendar_categories_edit');
				},
			});
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
						var calendar_categories_id = $("#calendar_categories_id").val();
						var action = "calendar_categories_rights_add_users";
						var token = $("#token").val();
						$.ajax({
							beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
							complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
							type: 'POST',
							url: 'calendar_categories_rights_ajax.php',
							data: "action=" + action + "&token=" + token + "&calendar_categories_id=" + calendar_categories_id  + "&users_meta=" + users_meta,
							success: function(message){
								ajaxReplyHistory(message,'#ajax_status_rights');
								if(message) {
									var newRow = $('<tr class="paging"><td class="paging">'+users_meta+'</td><td class="paging"><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value='+message+' /></td><td class="paging"><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value='+message+' /></td><td class="paging"><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value='+message+' /></td><td class="paging"><button class="btn_delete_rights" value='+message+'>delete</button></td></tr>');
									$('#rights > tbody:last').append(newRow);
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
						var calendar_categories_id = $("#calendar_categories_id").val();
						var action = "calendar_categories_rights_add_groups";
						var token = $("#token").val();
						$.ajax({
							beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
							complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
							type: 'POST',
							url: 'calendar_categories_rights_ajax.php',
							data: "action=" + action + "&token=" + token + "&calendar_categories_id=" + calendar_categories_id  + "&groups_meta=" + groups_meta,
							success: function(message){
								ajaxReplyHistory(message,'#ajax_status_rights');
								if(message) {
									var newRow = $('<tr class="paging"><td class="paging">'+groups_meta+'</td><td class="paging"><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value='+message+' /></td><td class="paging"><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value='+message+' /></td><td class="paging"><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value='+message+' /></td><td class="paging"><button class="btn_delete_rights" value='+message+'>delete</button></td></tr>');
									$('#rights > tbody:last').append(newRow);
								}
							},
						});
					
					});
				});
			}
		});


		$(document).on("click", "button.btn_delete_rights", function() {
			var calendar_categories_id = $("#calendar_categories_id").val();
			var action = "calendar_categories_rights_delete";
			var token = $("#token").val();
			var calendar_categories_rights_id = $(this).val();
			$(this).parent().parent().parent().remove();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
				type: 'POST',
				url: 'calendar_categories_rights_ajax.php',
				data: "action=" + action + "&token=" + token + "&calendar_categories_id=" + calendar_categories_id  + "&calendar_categories_rights_id=" + calendar_categories_rights_id,
				success: function(message){
					ajaxReplyHistory(message,'#ajax_status_rights');					
				},
			});
		});


		$("#btn_rights_save").click(function(event) {
			event.preventDefault();
			var calendar_categories_id = $("#calendar_categories_id").val();
			var action = "calendar_categories_rights_save";
			var token = $("#token").val();
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
				url: 'calendar_categories_rights_ajax.php',
				data: "action=" + action + "&token=" + token + "&calendar_categories_id=" + calendar_categories_id + "&r_id=" + r_id + "&r_read=" + r_read + "&r_edit=" + r_edit + "&r_create=" + r_create,
					success: function(message){	
						ajaxReply(message,'#ajax_status_rights');
					},
			});
		});
		
	});

</script>

<?php

$calendar = new Calendar();

if(isset($_GET['token'])){
	// only accept $_POST from this §_SESSION['token']
	if($_GET['token'] == $_SESSION['token']) {
		$id = $_GET['id'];
		$row = $calendar->getCalendarCategory($id);
		if(!$row) {
			die;
		}
		$active = ($row['active']==1) ? " checked=checked" : null;
		$public = ($row['public']==1) ? " checked=checked" : null;
		$rss = ($row['rss']==1) ? " checked=checked" : null;
		
		// get this view categories - if none saved enable delete button
		$rows = $calendar->getCalendarViewCategories($id);
		$button = (!$rows) ? null : ' disabled=disabled';
		
		echo '<input type="hidden" name="token" id="token" value="'.$_SESSION['token'].'">';
		echo '<input type="hidden" name="calendar_categories_id" id="calendar_categories_id" value="'.$id.'">';

		echo '<table style="width:100%;">';
			echo '<tr>';
				echo '<td>';
				echo '<h3 class="admin-heading">Edit calendar category "'. $row['category'] .'"</h3>';
				echo '</td>';
				echo '<td style="text-align:right;">';
				echo '<span class="toolbar_close"><button type="submit" onclick="parent.$.colorbox.close(); return false;">Close</button></span>&nbsp;';
				echo '<span class="toolbar_preview"><button type="submit" onclick="calendar_categories_preview('.$id.')">Preview</button></span>';
				echo '</td>';
			echo '</tr>';
		echo '</table>';
		echo '<hr />';

		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';
		
				echo '<div style="width:100%;" id="calendar_categories_form">';

					echo '<table style="padding:10px 0 10px 0;width:100%;"><tr><td style="width:30%;">';
					echo 'Name<br /><input type="text" name="category" id="category" value="'. $row['category'] .'" style="width:80%;">';
					echo '</td>';
					echo '<td style="width:70%;">';
					echo 'Description<br /><input type="text" name="description" id="description" value="'. $row['description'] .'" style="width:100%;">';
					echo '</td></tr></table>';
					
					echo '<table style="padding:0;width:100%;"><tr><td style="width:10%;">';
					echo '<input type="checkbox" name="active" id="active" value="1" "'.$active.'> Active';
					echo '</td>';
					echo '<td style="width:10%;">';
					echo '<input type="checkbox" name="public" id="public" value="1" "'.$public.'> Public';
					echo '</td>';
					echo '<td style="width:10%;">';
					echo '<input type="checkbox" name="rss" id="rss" value="1" "'.$rss.'> RSS';
					echo '</td>';
					echo '<td style="width:70%;text-align:right;">';
					
					echo '<span class="toolbar"><button id="btn_calendar_categories_save" value="btn_calendar_categories_save">Save category</button></span>&nbsp;';
					echo '<span class="toolbar"><button id="btn_calendar_categories_delete" value="btn_calendar_categories_delete" '.$button.' title="">Delete category</button></span>';
					echo '<br />';
					echo '<span id="ajax_spinner_calendar_categories_edit" style="display:none;"></span>';
					echo '<span id="ajax_status_calendar_categories_edit" style="display:none;"></span>';
					echo '</td></tr></table>';
				
				echo '</div>';

				echo '<hr />';
				echo '<h5 class="admin-heading">Category rights</h5>';
				echo '<div style="width:100%; padding: 20px 0 20px 0;">';
				
					// user rights to this page
					$calendar_rights = new CalendarRights();
					$users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;

					// rights to this page
					$users_rights = $calendar_rights->getCalendarUsersRightsMeta($id, $users_id);
					$groups_rights = $calendar_rights->getCalendarGroupsRightsMeta($id);
					?>
					
					<table>
						<tr>
							<td>
								<input id="users_find" name="users_find" value="<?php if(isset($_REQUEST['users_find'])) {echo $_REQUEST['users_find']; } ?>"  style="min-width:300px;" />
								<input type="hidden" id="pid" /><span class="toolbar"><button id="btn_add_users_rights">Add user</button></span>
							</td>
							<td>
								<input id="groups_find" name="groups_find" value="<?php if(isset($_REQUEST['groups_find'])) {echo $_REQUEST['groups_find']; } ?>" style="min-width:300px;" />
								<input type="hidden" id="gid" /><span class="toolbar"><button id="btn_add_groups_rights">Add group</button></span>
							</td>
							<td>						
								<span id="ajax_spinner_rights" style=display:none><img src="css/images/spinner_1.gif"></span>
								<span id="ajax_status_rights" style="display:none;"></span>
							</td>
						</tr>
					</table>
					
					<?php
					echo '<p>';
					echo '<table id="rights" class="paging" style="min-width:600px;">';		
						echo '<thead>';		
						echo '<tr>';
							echo '<th class="paging" style="width:80%;">users / groups';
							echo '</th>';
							echo '<th class="paging">read';
							echo '</th>';
							echo '<th class="paging">edit';
							echo '</th>';
							echo '<th class="paging">create';
							echo '</th>';
							echo '<th class="paging">delete';
							echo '</th>';
						echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						// css style odd-even rows
						$class = 'even';			
						foreach($users_rights as $r) {
							?><input type="hidden" name="r_id[]" id="r_id[]" value="<?php echo $r['calendar_categories_rights_id']; ?>" /><?php
							// switch odd-even
								$class = ($class=='even') ? 'odd' : 'even';				
								echo '<tr id="row-'. $r['calendar_categories_rights_id'] .'" class="paging_'. $class .'">';
								echo '<td class="paging">';
								echo $r['first_name'] .' '. $r['last_name'] .', '. $r['email'];
								echo '</td>';
								echo '<td class="paging">';
								?><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value="<?php echo $r['calendar_categories_rights_id']; ?>" <?php if($r['rights_read'] == 1) {echo 'checked';}?> /><?php
								echo '</td>';
								echo '<td class="paging">';
								?><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value="<?php echo $r['calendar_categories_rights_id']; ?>" <?php if($r['rights_edit'] == 1) {echo 'checked';}?> /><?php
								echo '</td>';
								echo '<td class="paging">';
								?><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value="<?php echo $r['calendar_categories_rights_id']; ?>" <?php if($r['rights_create'] == 1) {echo 'checked';}?> /><?php
								echo '</td>';
								echo '<td class="paging">';
								?><span class="toolbar"><button class="btn_delete_rights" value="<?php echo $r['calendar_categories_rights_id']; ?>">delete</button></span><?php
								echo '</td>';
							echo '</tr>';
						}
						foreach($groups_rights as $r) {
							?><input type="hidden" name="r_id[]" id="r_id[]" value="<?php echo $r['calendar_categories_rights_id']; ?>" /><?php
							// switch odd-even
							$class = ($class=='even') ? 'odd' : 'even';				
							echo '<tr id="row-'. $r['calendar_categories_rights_id'] .'" class="paging_'. $class .'">';
								echo '<td class="paging">';
								echo $r['title'] .' (group)';
								echo '</td>';
								echo '<td class="paging">';
								?><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value="<?php echo $r['calendar_categories_rights_id']; ?>" <?php if($r['rights_read'] == 1) {echo 'checked';}?> /><?php
								echo '</td>';
								echo '<td class="paging">';
								?><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value="<?php echo $r['calendar_categories_rights_id']; ?>" <?php if($r['rights_edit'] == 1) {echo 'checked';}?> /><?php
								echo '</td>';
								echo '<td class="paging">';
								?><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value="<?php echo $r['calendar_categories_rights_id']; ?>" <?php if($r['rights_create'] == 1) {echo 'checked';}?> /><?php
								echo '</td>';
								echo '<td class="paging">';
								?><span class="toolbar"><button class="btn_delete_rights" value="<?php echo $r['calendar_categories_rights_id']; ?>">delete</button></span><?php
								echo '</td>';
							echo '</tr>';
							echo '</tbody>';
						}
					echo '</table>';	
					echo '</p>';
					
					echo '<p>';
						echo '<span class="toolbar"><button id="btn_rights_save">Save rights</button></save>';
					echo '</p>';
				
				
				echo '</div>';
				echo '<hr />';
				echo '<h5 class="admin-heading">Views containing this calendar category</h5>';
				echo '<div style="width:100%; padding: 20px 0 20px 0;">';
				
					$views = $calendar->getCalendarViewsUsingCategory($id);
					//print_r($views);
					echo '<ul>';
					
					foreach($views as $view) {
						echo '<li>'.$view['name'].'</li>';
					}			
					
					echo '</ul>';
					
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
	}
}
?>


<div class="footer-wrapper">
	<?php include_once 'includes/inc.footer_cms.php'; ?>
</div>


</body>
</html>