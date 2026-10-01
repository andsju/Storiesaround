<?php

// include core 
//--------------------------------------------------
require_once '../../../cms/includes/inc.core.php';

include_once CMS_ABSPATH . '/cms/includes/inc.functions_pages.php';


// include session access 
//--------------------------------------------------
require_once CMS_ABSPATH . '/cms/includes/inc.session_access.php';


// access right, minimum, hierarchy matters
//--------------------------------------------------
if(!get_role_CMS('administrator') == 1) {
	if(!get_role_LMS('administrator') == 1) {
		header('Location: '. $_SESSION['site_domain_url']);	exit;
	}
}


// css files, loaded in inc.header.php 
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-datatables/style.css',
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/cms/plugins/bookitems/css/style.css' );

	// add css jquery-ui theme
$ui_theme = isset($_SESSION['site_ui_theme']) ? $_SESSION['site_ui_theme'] : '';
if(file_exists(CMS_ABSPATH .'/cms/libraries/jquery-ui/theme/'.$ui_theme.'/jquery-ui.css')) {
	if (($key = array_search(CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', $css_files)) !== false) {
		unset($css_files[$key]);
	}
	array_push($css_files, CMS_DIR.'/cms/libraries/jquery-ui/theme/'.$ui_theme.'/jquery-ui.css');
}

//--------------------------------------------------
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/js/functions.js', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js', 
	CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js'
);

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Administration - plugin Bookitems</title>

	<?php 
	//load css files
	foreach ( $css_files as $css ) {
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen,projection" href="'.$css.'" />';
	}; 
	echo "\n";
	echo "\n\t".'<script src="'.CMS_DIR.'/cms/libraries/jquery/jquery.min.js"></script>';
	echo "\n\t".'<script src="https://www.google.com/jsapi"></script>';
	echo "\n";
	?>
	
	<style>
	
	.img-wrap .image-name { 
		position: absolute;
		top: 20px;
		left: 120px;
		
		
	}
	.img-wrap {
		position: relative;
		padding:5px;
		vertical-align:middle;

	}
	.img-wrap .close {
		position: absolute;
		top: 2px;
		left: 2px;
		z-index: 100;
		background-color: #FFF;
		padding: 5px;
		color: #000;
		font-weight: bold;
		cursor: pointer;
		opacity: .5;
		text-align: center;
		font-size: 16px;
		line-height: 10px;
		border-radius: 20%;
	}
	.img-wrap:hover .close {
		opacity: 1;
	}	
	</style>
	
	
</head>

<body style="width:1200px;background:#fafafa;">


<script>
	$(document).ready(function() {

		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});
	
		$('.table_js').dataTable({
			"sPaginationType": "full_numbers",
			"aaSorting": [[ 4, "desc" ]]
		});
	
		$('#btn_category_add').click(function(event){
			event.preventDefault();
			var action = "action_category_add";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var title = $("#title").val();
			if(title.length) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_category_add').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_category_add').hide()",700)},
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title,
					success: function(newdata){
						ajaxReply('','#ajax_status_category_add');
						$('<a href=admin.php?t=cat_edit&plugin_bookitems_category_id=' +newdata+'> category skapad &raquo; redigera <b>'+title+'</b> <span class="ui-icon ui-icon-pencil" title="edit" style="display:inline-block;vertical-align:text-bottom;"></span></a>').hide().fadeIn('fast').insertAfter("#ajax_status_category_add");
						$('#title').val('');
					},
				});
			}
		});

		$('#btn_category_save').click(function(event){
			event.preventDefault();
			var action = "action_category_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var title = $("#title").val();
			var description = $("#description").val();
			var position = $("#position").val();
			var active = $('input:checkbox[name=active]').is(':checked') ? 1 : 0;		
			var plugin_bookitems_category_id = $("#plugin_bookitems_category_id").val();
			if(title.length) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_category_edit').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_category_edit').hide()",700)},
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title+"&description="+description+"&active="+active+"&position="+position+"&plugin_bookitems_category_id="+plugin_bookitems_category_id,
					success: function(newdata){
						ajaxReply('','#ajax_status_category_edit');
					},
				});
			}
		});

		
		$('#btn_unit_add').click(function(event){
			event.preventDefault();
			var action = "action_unit_add";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var title = $("#title").val();
			var plugin_bookitems_category_id = $("#plugin_bookitems_category_id").val();
			if(title.length) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_unit_add').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_unit_add').hide()",700)},
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title+"&plugin_bookitems_category_id="+plugin_bookitems_category_id,
					success: function(newdata){
						ajaxReply('','#ajax_status_unit_add');
						$('<a href=admin.php?t=unit_edit&plugin_bookitems_unit_id=' +newdata+'> unit skapad &raquo; redigera <b>'+title+'</b> <span class="ui-icon ui-icon-pencil" title="edit" style="display:inline-block;vertical-align:text-bottom;"></span></a>').hide().fadeIn('fast').insertAfter("#ajax_status_category_add");
						$('#title').val('');
					},
				});
			}
		});

		$('#btn_unit_save').click(function(event){
			event.preventDefault();
			var action = "action_unit_save";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var title = $("#title").val();
			var description = $("#description").val();
			var image = $("#image").val();
			var path = $("#path").val();
			var active = $('input:checkbox[name=active]').is(':checked') ? 1 : 0;
			var position = $("#position").val();
			var plugin_bookitems_unit_id = $("#plugin_bookitems_unit_id").val();
			var plugin_bookitems_category_id = $("#plugin_bookitems_category_id").val();
			if(title.length) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_unit_edit').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_unit_edit').hide()",700)},
					type: 'POST',
					url: 'admin_ajax.php',
					data: "action="+action+"&token="+token+"&users_id="+users_id+"&title="+title+"&description="+description+"&image="+image+"&active="+active+"&position="+position+"&plugin_bookitems_unit_id="+plugin_bookitems_unit_id+"&plugin_bookitems_category_id="+plugin_bookitems_category_id,
					success: function(newdata){
						ajaxReply('','#ajax_status_unit_edit');
						$('#thumb').attr('src',path+'/uploads/plugins/bookitems/'+image);
					},
				});
			}
		});

		
		$('#file_upload_form').submit(function(){
			// show loader [optional line]
			$('#msg').html('uploading <img src="css/images/spinner.gif">').fadeIn();
			if(document.getElementById('upload_frame') == null) {
				// create iframe
				$('body').append('<iframe id="upload_frame" name="upload_frame" width="0" scrolling="no" height="0" frameborder="0"></iframe>');
				$('#upload_frame').on('load',function(){
					if($(this).contents()[0].location.href.match($(this).parent('form').attr('action'))){
						
						
						var response = $(this).contents().find('body').html();
						var data = $.parseJSON(response);
						//console.log(data);
						
						if(data.success==true) {
							var image = data.image;
							var size = data.size;
							$('#msg').hide();
							$('#thumbs').prepend('<div><img src="../../../content/uploads/plugins/bookitems/'+image+'" title="'+image+' '+size+'" class="thumbs" />'+image+'</div>');
							$('#upload_field').val('');
						} else {
							var error = data.error;
							$('#msg').html(error);
						}
						
					}
				});
				$(this).attr('method','post');    
				$(this).attr('enctype','multipart/form-data');
				$(this).attr('target','upload_frame').submit();
			}
		});
		
		$('.img-wrap .close').on('click', function() {
			var img = $(this).closest('.img-wrap').find('img').attr('src');
			var action = "action_unit_image_delete";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			$(this).closest('.img-wrap').remove();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_unit_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_unit_edit').hide()",700)},
				type: 'POST',
				url: 'admin_ajax.php',
				data: "action="+action+"&token="+token+"&users_id="+users_id+"&img="+img,
				success: function(newdata){
				},
			});
			
		});		
		
		
		$( "#users_find" ).autocomplete({
			delay: 300,
			source: function( request, response ) {						
				var cms_dir = $("#cms_dir").val();
				var ajax_dir = cms_dir +'/cms/';
				$.ajax({
					type: "post",
					url: ajax_dir + "users_ajax.php",
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
						var plugin_bookitems_category_id = $("#plugin_bookitems_category_id").val();
						var action = "bookitems_category_rights_add_users";
						var token = $("#token").val();
						var cms_dir = $("#cms_dir").val();
						var ajax_dir = cms_dir +'/content/plugins/bookitems/';
						$.ajax({
							beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
							complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
							type: 'POST',
							url: ajax_dir + 'admin_ajax.php',
							data: "action=" + action + "&token=" + token + "&plugin_bookitems_category_id=" + plugin_bookitems_category_id  + "&users_meta=" + users_meta,
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
				var cms_dir = $("#cms_dir").val();
				var ajax_dir = cms_dir +'/cms/';
				$.ajax({
					type: "post",
					url: ajax_dir + "groups_ajax.php",
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
			minLength: 1,
			select: function( event, ui ) {
				$("input#gid").val(ui.item.id),					
				$(function(){
					$('#btn_add_groups_rights').click(function(){
						var groups_meta = ui.item.value;
						var plugin_bookitems_category_id = $("#plugin_bookitems_category_id").val();
						var action = "bookitems_category_rights_add_groups";
						var token = $("#token").val();
						var cms_dir = $("#cms_dir").val();
						var ajax_dir = cms_dir +'/content/plugins/bookitems/';
						$.ajax({
							beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
							complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
							type: 'POST',
							url: ajax_dir + 'admin_ajax.php',
							data: "action=" + action + "&token=" + token + "&plugin_bookitems_category_id=" + plugin_bookitems_category_id  + "&groups_meta=" + groups_meta,
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
			//var plugin_bookitems_category_rights_id = $("#plugin_bookitems_category_rights_id").val();
			var action = "plugin_bookitems_category_rights_delete";
			var token = $("#token").val();
			var cms_dir = $("#cms_dir").val();
			var ajax_dir = cms_dir +'/content/plugins/bookitems/';
			var plugin_bookitems_category_rights_id = $(this).val();
			console.log(plugin_bookitems_category_rights_id);
			$(this).parent().parent().parent().remove();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_rights').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_rights').hide()",700)},
				type: 'POST',
				url: ajax_dir + 'admin_ajax.php',
				data: "action=" + action + "&token=" + token + "&plugin_bookitems_category_rights_id=" + plugin_bookitems_category_rights_id,
				success: function(message){
					ajaxReplyHistory(message,'#ajax_status_rights');					
				},
			});
		});
		
		
	$("#btn_rights_save").click(function(event) {
		event.preventDefault();
		var plugin_bookitems_category_id = $("#plugin_bookitems_category_id").val();
		var action = "plugin_bookitems_category_rights_save";
		var cms_dir = $("#cms_dir").val();
		var ajax_dir = cms_dir +'/content/plugins/bookitems/';
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
			url: ajax_dir +'admin_ajax.php',
			data: "action=" + action + "&token=" + token + "&plugin_bookitems_category_id=" + plugin_bookitems_category_id + "&r_id=" + r_id + "&r_read=" + r_read + "&r_edit=" + r_edit + "&r_create=" + r_create,
				success: function(message){	
					ajaxReply(message,'#ajax_status_rights');
				},
		});
	});
		
		
	});
</script>


<?php

$bookitems = new Bookitems();


function getKategoriTitel($plugin_bookitems_category_id, $categories) {
	if(!$categories) {die();}
	$str = '';
	foreach($categories as $category) {
		if($category['plugin_bookitems_category_id'] == $plugin_bookitems_category_id) {					
			$str = $category['title'];
			break;
		}
	}
	return $str;
}



$t = (isset($_GET['t'])) ? $_GET['t'] : null;

// url
$this_url = $_SERVER['PHP_SELF'] .'?t='. $t;

echo '<img src="'.CMS_DIR.'/cms/css/images/storiesaround_logotype_black.png" alt="Storiesaround logotype" style="width:120px;float:right;"/>';
echo '<h3 class="plugin-text">plugin</h3>';
echo '<div class="admin-heading">';
	
	echo '<div class="float">';
		echo '<div class="cms-ui-icons cms-ui-plugins"></div>';
		echo '<div class="float">';
			echo '<a href="'.CMS_HOME.'/help/plugins/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Bookitems</h1></a>';
		echo '</div>';
	echo '</div>';
	echo '<div class="ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
		$icon = "<span class='ui-icon ui-icon-pencil' style='display:table-cell;height:15px;'></span>";
		get_tab_menu_jquery_ui_look_alike($_SERVER["PHP_SELF"], array("info","cat_add","cat_find","cat_edit","unit_add","unit_find","unit_edit",), array("Info","New category","Categories",$icon,"New unit","Units",$icon), "t", "", null, $ui_ul_add_class="ui-two", $ui_a_add_class="");
	echo '</div>';	
	
echo '</div>';







echo '<input type="hidden" name="users_id" id="users_id" value="'.$_SESSION['users_id'].'" />';
echo '<input type="hidden" name="cms_dir" id="cms_dir" value="'.CMS_DIR.'" />';


echo '<div class="admin-area-outer ui-widget ui-widget-content">';
	echo '<div class="admin-area-inner">';


		switch($t) {


			case 'cat_add':
			
				?>
					<h3 class="heading">New category</h3>
					
					<table>
						<tr>
							<td>
							<p>
								<label for="title">Titel</label><br />
								<input type="text" id="title" name="title" style="width:300px;" maxlength="75" />
							</p>
							<p>
								<span class="toolbar"><button id="btn_category_add">Skapa</button></span>
								<span id="ajax_spinner_category_add" style='display:none'><img src="css/images/spinner.gif"></span>
								<span id="ajax_status_category_add" style='display:none'></span>			
							</p>
						</tr>
					</table>

				<?php
			
			break;

			
			case 'cat_find':


					$html = '';
					$rows = $bookitems->getBookitemsCategory();
					
					echo '<h3 class="heading">Bookitems</h3>';
					if($rows) {
						$html = '<table class="table_js lightgrey">';
							$html .= '<thead>';
								$html .= '<tr>';
									$html .= '<th>Titel</th>';
									$html .= '<th style="text-align:center;width:5%;">Aktiv</th>';
									$html .= '<th style="text-align:center;width:5%;">Position</th>';
									$html .= '<th>Datum</th>';
									$html .= '<th style="width:5%;text-align:center;">Redigera</th>';
								$html .= '</tr>';
							$html .= '</thead>';					
							$html .= '<tbody>';
								foreach($rows as $row) {
									$html .= '<tr class="">';
										$html .= '<td>'.$row['title'].'</td>';
										$q = $row['active'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
										$html .= '<td style="text-align:center;width:5%;">'.$q.'</td>';
										$html .= '<td style="text-align:right;width:5%;">'.$row['position'].'</td>';
										$html .= '<td>'.$row['utc_created'].'</td>';
										$html .= '<td style="text-align:center;"><a class="link_form_edit" href="admin.php?t=cat_edit&plugin_bookitems_category_id='.$row['plugin_bookitems_category_id'].'"><span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a></td>';
									$html .= '</tr>';
								}
							$html .= '</tbody>';
						$html .= '</table>';
					}
					echo $html;

				
			break;
			

			case 'cat_edit':

				$plugin_bookitems_category_id = isset($_GET['plugin_bookitems_category_id']) && is_numeric($_GET['plugin_bookitems_category_id']) ? $_GET['plugin_bookitems_category_id'] : 0;
				$row = $bookitems->getBookitemsCategoryId($plugin_bookitems_category_id);
				$date_modified = ($row['utc_modified']>'2000-01-01 00:00') ? new DateTime(utc_dtz($row['utc_modified'], $dtz, 'Y-m-d H:i:s')) : null;
				?>
				
				<div style="float:right;text-align:right;">
				<span id="ajax_spinner_category_edit" style='display:none'><img src="css/images/spinner.gif"></span>
				<span id="ajax_status_category_edit" style='display:none'></span>
				<span class="toolbar"><button id="btn_category_save">Spara</button></span>
				</div>
				<input type="hidden" id="plugin_bookitems_category_id" value="<?php echo $plugin_bookitems_category_id;?>">
				<h4 class="admin-heading">Edit category</h4>
				<table>
					<tr>
						<td>
						<label for="title">Titel</label><br />
						<input type="text" name="title" id="title" value="<?php echo $row['title'];?>" style="width:400px;" />
						</td>
					</tr>
					<tr>
						<td>
						<label for="title">Beskrivning</label><br />
						<textarea name="description" id="description" style="width:400px;min-height:75px;"><?php echo $row['description'];?></textarea>
						</td>
					</tr>
					<tr>
						<td>
						<label for="title">Aktiv</label><br />
						<input type="checkbox" name="active" id="active" value="1" <?php if($row['active']) { echo ' checked=checked';} ?> />
						</td>
					</tr>
					<tr>
						<td>
						<label for="title">Position</label><br />						
						<select id="position" name="position">
						<?php						
						for($i=0; $i<=25; $i++) {
							echo '<option value="'.$i.'"';
								if($row['position']==$i) {
									echo ' selected=selected';
								}
							echo '>'.$i;
							echo '</option>';
						}
						?>
						</select>
						</td>
					</tr>
					<tr>
						<td>
						Senast redigerad: 
						<?php if($date_modified) { echo $date_modified->format('Y-m-d H:m:s');} ?>
						</td>
					</tr>
				</table>
				
				
				<?php
				
				
				
				
				echo '<hr />';
				echo '<h4 class="admin-heading">Set category rights</h4>';
				echo '<div style="width:100%; padding: 20px 0 20px 0;">';
				
				
					// user rights to this page
					//$calendar_rights = new CalendarRights();
					$users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;

					// rights to this page
					$users_rights = $bookitems->getBookitemsUsersRightsMeta($plugin_bookitems_category_id);
					$groups_rights = $bookitems->getBookitemsGroupsRightsMeta($plugin_bookitems_category_id);
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
					echo '<table style="margin-top:10px;">';
						echo '<tr><td style="">';

						echo '<div style="max-height:250px;overflow-y:scroll;padding:10px 10px 10px 0;">';
						
						echo '<table id="rights" class="paging" style="min-width:300px;">';		
							echo '<thead>';		
							echo '<tr>';
								echo '<th class="paging" style="width:80%;">users / groups';
								echo '</th>';
								echo '<th class="paging">read';
								echo '</th>';
								echo '<th class="paging">create';
								echo '</th>';
								echo '<th class="paging">edit';
								echo '</th>';
								echo '<th class="paging">delete';
								echo '</th>';
							echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							// css style odd-even rows
							$class = 'even';			
							foreach($users_rights as $r) {
								?><input type="hidden" name="r_id[]" id="r_id[]" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>" /><?php
								// switch odd-even
									$class = ($class=='even') ? 'odd' : 'even';				
									echo '<tr id="row-'. $r['plugin_bookitems_category_rights_id'] .'" class="paging_'. $class .'">';
									echo '<td class="paging">';
									echo $r['first_name'] .' '. $r['last_name'] .', '. $r['email'];
									echo '</td>';
									echo '<td class="paging check">';
									?><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>" <?php if($r['rights_read'] == 1) {echo 'checked';}?> /><?php
									echo '</td>';
									echo '<td class="paging check">';
									?><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>" <?php if($r['rights_create'] == 1) {echo 'checked';}?> /><?php
									echo '</td>';
									echo '<td class="paging check">';
									?><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>" <?php if($r['rights_edit'] == 1) {echo 'checked';}?> /><?php
									echo '</td>';
									echo '<td class="paging">';
									?><span class="toolbar"><button class="btn_delete_rights" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>">delete</button></span><?php
									echo '</td>';
								echo '</tr>';
							}
							

							
							foreach($groups_rights as $r) {
								?><input type="hidden" name="r_id[]" id="r_id[]" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>" /><?php
								// switch odd-even
								$class = ($class=='even') ? 'odd' : 'even';	
								echo '<tr id="row-'. $r['plugin_bookitems_category_rights_id'] .'" class="paging_'. $class .'">';
									echo '<td class="paging">';
									echo $r['title'] .' (group)';
									echo '</td>';
									echo '<td class="paging check">';
									?><input type="checkbox" name="rights_read[]" id="rights_read[]" title="read" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>" <?php if($r['rights_read'] == 1) {echo 'checked';}?> /><?php
									echo '</td>';
									echo '<td class="paging check">';
									?><input type="checkbox" name="rights_create[]" id="rights_create[]" title="create" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>" <?php if($r['rights_create'] == 1) {echo 'checked';}?> /><?php
									echo '</td>';
									echo '<td class="paging check">';
									?><input type="checkbox" name="rights_edit[]" id="rights_edit[]" title="edit" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>" <?php if($r['rights_edit'] == 1) {echo 'checked';}?> /><?php
									echo '</td>';
									echo '<td class="paging">';
									?><span class="toolbar"><button class="btn_delete_rights" value="<?php echo $r['plugin_bookitems_category_rights_id']; ?>">delete</button></span><?php
									echo '</td>';
								echo '</tr>';
								echo '</tbody>';
							}
							
						echo '</table>';	
						echo '</div>';
						
						
						echo '<p>';
							echo '<span class="toolbar"><button id="btn_rights_save">Save rights</button></save>';
						echo '</p>';
				
					echo '</td>';
			
			
			
					echo '<td style="padding:20px;vertical-align:top;">';
					
					echo '<h5>read</h5>';
					echo 'Rights to read/show booked units';
					echo '<h5>create</h5>';
					echo 'Rights to book units (and edit own bookings)';
					echo '<h5>edit</h5>';
					echo 'Rights to edit booked units (all)';
					echo '<div style="color:red;padding:10px 0;">Administrators have (by default) access to all functions in this plugin including create and edit bookings, create and edit categories and units.</div>';
					
					echo '</td></tr></table>';
			
				echo '</div>';
			
			break;
			
			case 'unit_add':
			
				?>
					<h3 class="heading">New unit</h3>
					
					<table>
						<tr>
							<td>
							<p>
								<label for="title">Titel</label><br />
								<input type="text" id="title" name="title" style="width:300px;" maxlength="75" />
							</p>
							<p>
								<label for="plugin_bookitems_category_id">Category</label><br />
								<select id="plugin_bookitems_category_id">
								<option value="0"></option>
								<?php
								$rows = $bookitems->getBookitemsCategory();
								foreach($rows as $row) {			
									echo '<option value="'.$row['plugin_bookitems_category_id'].'"';
									echo '>'.$row['title'].'</option>';						
								}
								?>
								</select>
							</p>
							<p>
								<span class="toolbar"><button id="btn_unit_add">Skapa</button></span>
								<span id="ajax_spinner_unit_add" style='display:none'><img src="css/images/spinner.gif"></span>
								<span id="ajax_status_unit_add" style='display:none'></span>			
							</p>
							</td>
						</tr>
					</table>

				<?php
			
			break;



			case 'unit_find':

				$categories = $bookitems->getBookitemsCategory();
				

					$html = '';
					$rows = $bookitems->getBookitemsUnit();
					
					echo '<h3 class="heading">Bookitems - units</h3>';
					if($rows) {
						$html = '<table class="table_js lightgrey">';
							$html .= '<thead>';
								$html .= '<tr>';
									$html .= '<th>Titel</th>';
									$html .= '<th style="text-align:center;width:5%;">Aktiv</th>';
									$html .= '<th>Kategori</th>';
									$html .= '<th>Datum</th>';
									$html .= '<th style="width:5%;text-align:center;">Redigera</th>';
								$html .= '</tr>';
							$html .= '</thead>';					
							$html .= '<tbody>';
								foreach($rows as $row) {
									$html .= '<tr class="">';
										$html .= '<td>'.$row['title'].'</td>';
										$q = $row['active'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
										$html .= '<td style="text-align:center;width:5%;">'.$q.'</td>';
										
										$kat = getKategoriTitel($row['plugin_bookitems_category_id'], $categories);
										$html .= '<td>'.$kat.'</td>';
										
										$html .= '<td>'.$row['utc_created'].'</td>';
										$html .= '<td style="text-align:center;"><a class="link_form_edit" href="admin.php?t=unit_edit&plugin_bookitems_unit_id='.$row['plugin_bookitems_unit_id'].'"><span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a></td>';
									$html .= '</tr>';
								}
							$html .= '</tbody>';
						$html .= '</table>';
					}
					echo $html;

				
			break;

			
			
			case 'unit_edit':

			
				$plugin_bookitems_unit_id = isset($_GET['plugin_bookitems_unit_id']) && is_numeric($_GET['plugin_bookitems_unit_id']) ? $_GET['plugin_bookitems_unit_id'] : 0;
				
				$row = $bookitems->getBookitemsUnitId($plugin_bookitems_unit_id);
				$date_modified = ($row['utc_modified']>'2000-01-01 00:00') ? new DateTime(utc_dtz($row['utc_modified'], $dtz, 'Y-m-d H:i:s')) : '';
				?>
				
				<input type="hidden" id="plugin_bookitems_unit_id" value="<?php echo $plugin_bookitems_unit_id;?>">
				<input type="hidden" id="path" value="<?php echo CMS_DIR;?>">
				
				<div style="width:50%;float:left;padding:10px;box-sizing:border-box;-moz-box-sizing:border-box;">
					<div style="float:right;text-align:right;">
					<span id="ajax_spinner_unit_edit" style='display:none'><img src="css/images/spinner.gif"></span>
					<span id="ajax_status_unit_edit" style='display:none'></span>
					<span class="toolbar"><button id="btn_unit_save">Spara</button></span>
					</div>
					
					<h3>Redigera unit</h3>
					
					<table class="unit_settings">
						<tr>
							<td>
							<label for="title">Titel</label><br />
							<input type="text" name="title" id="title" value="<?php echo $row['title'];?>" style="width:400px;" />
							</td>
						</tr>
						<tr>
							<td>
							<label for="title">Beskrivning</label><br />
							<textarea name="description" id="description" style="width:400px;min-height:75px;"><?php echo $row['description'];?></textarea>
							</td>
						</tr>
						<tr>
							<td>
							<label for="title">Aktiv</label><br />
							<input type="checkbox" name="active" id="active" value="1" <?php if($row['active']) { echo ' checked=checked';} ?> />
							</td>
						</tr>
						<tr>
							<td>
							<label for="title">Position</label><br />						
							<select id="position" name="position">
							<?php						
							for($i=0; $i<=25; $i++) {
								echo '<option value="'.$i.'"';
									if($row['position']==$i) {
										echo ' selected=selected';
									}
								echo '>'.$i;
								echo '</option>';
							}
							?>
							</select>
							</td>
						</tr>
						<tr>
							<td>
							<label for="type">Kategori</label><br />
							<select id="plugin_bookitems_category_id" name="plugin_bookitems_category_id">
							<option value=""></option>
							<?php
							$categories = $bookitems->getBookitemsCategory();
												
							if(!$categories) {die();}
							$options = '';
							foreach($categories as $category) {
							
								$options .= '<option value="'.$category['plugin_bookitems_category_id'].'"';
								if($category['plugin_bookitems_category_id']==$row['plugin_bookitems_category_id']) {
									$options .= ' selected';
								}
								$options .= '>'.$category['title'].'</option>';
							}
							echo $options;
							?>
							</select>
							</td>
						</tr>
						<tr>
							<td>
							<label for="title">Bild</label><br />
							<input type="text" name="image" id="image" value="<?php echo $row['image'];?>" style="width:400px;" />
							</td>
						</tr>
						<tr>
							<td>
							<img id="thumb" src="<?php echo CMS_DIR . '/content/uploads/plugins/bookitems/'. $row['image'];?>" />
							</td>
						</tr>
						<tr>
							<td style="padding-top:20px;">
							Senast redigerad: 
							<?php if($date_modified) { echo $date_modified->format('Y-m-d H:m:s'); } ?>
							</td>
						</tr>
					</table>
				
				</div>
				
				<div style="width:50%;float:left;border:1px dotted #000;padding:10px;box-sizing:border-box;-moz-box-sizing:border-box;max-height:600px;overflow:scroll;">
					<form id="file_upload_form" action="upload.php" enctype="multipart/form-data">
					   <label for="upload_field">Ladda upp bild</label><br />
					   <input type="file" id="upload_field" name="upload_field" style="border:1px dashed #000;background:#F8F8F8;" />
					   <span class="toolbar"><button id="btn_upload">Ladda upp</button></span>
					</form>
					<div id="msg"></div>
					<pre id="server_response"></pre>
					<div id="thumbs">
					<?php
					$directory_text = CMS_ABSPATH . '/content/uploads/plugins/bookitems/';
					if (is_dir($directory_text)) {
						if ($dh = opendir($directory_text)) {
							$images_ext = array('jpg','jpeg','gif','png');

							while (($file = readdir($dh)) !== false) {
								if (!is_dir($directory_text.'/'.$file)) {
									$ext = pathinfo($directory_text.'/'.$file, PATHINFO_EXTENSION);
									if(in_array($ext, $images_ext)) {
										echo '<div class="img-wrap"><span class="close">&times;</span><img src="'.CMS_DIR.'/content/uploads/plugins/bookitems/'.$file.'" /><span class="image-name">'.$file.'</span></div>';
									}
								}
							}
							closedir($dh);
						}
					}
					?>
					</div>
				</div>
				
				<div style="clear:both;"></div>
				
				
				<?php
			
			break;
			
			
			
			case 'info':
			case '';
			

					$row = $bookitems->info();
					//print_r2($row);
					
					
					?>
					
					
				
				
				<?php
			
			break;

		}


	echo '</div>';
echo '</div>';

?>

<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
<input type="hidden" id="users_id" name="users_id" value="<?php echo $_SESSION['users_id']; ?>">

<?php
// load javascript files
foreach ( $js_files as $js ) {
	echo "\n".'<script src="'.$js.'"></script>';
}
?>


<script>
	// enable log
	jQuery.fn.log = function (msg) {
	  console.log("%s: %o", msg, this);
	  return this;
	};	
</script>

<?php include_once CMS_ABSPATH . '/cms/includes/inc.footer_cms.php'; ?>

</body>
</html>