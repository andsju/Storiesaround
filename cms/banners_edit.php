<?php 
// include core
//--------------------------------------------------
require_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';

if(!get_role_CMS('editor') == 1) { header('Location: index.php'); die;}

if(!isset($_SESSION['site_id'])) {
	echo 'Site is not set!';
	exit;
}


// css files
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css' );

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

// load javascript files
//--------------------------------------------------
$js_files = array( 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js',
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js',
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/js/functions.js'
);

$wysiwyg_editor = isset($_SESSION['site_wysiwyg']) ? get_editor_settings($editors, $_SESSION['site_wysiwyg']) :  null;

// javascript files... add wysiwyg file
if (is_array($wysiwyg_editor)) {
	if(file_exists(CMS_ABSPATH .'/cms/libraries/'.$wysiwyg_editor['include_js_file'])) {
		array_push($js_files, CMS_DIR.'/cms/libraries/'.$wysiwyg_editor['include_js_file']);
	}
}


// include header
//--------------------------------------------------
$page_title = "Edit banner";
$body_style = "width:1190px;max-width:100% !important;";
require 'includes/inc.header_minimal.php';

	
$row = null;

if(isset($_GET['token'])){

	if($_GET['token'] == $_SESSION['token']) {

		// check $_GET id
		$id = array_key_exists('id', $_GET) ? $_GET['id'] : null;
		if($id == null) { die;}

		$banners = new Banners();
		$row = $banners->getBannerId($id);

	}
}

if(!$row) { die; }
?>


<script>

	function banners_preview(id) {
		w=window.open('banners_preview.php?token=<?php echo $_SESSION['token']; ?>&id='+id,'','width=1280,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
		w.focus();
	}

	$(document).ready(function() {
		var token = $("#token").val();

		$( ".toolbar button" ).button({
		});

		$( ".toolbar_preview button" ).button({
			icons: { secondary: "ui-icon-newwin" },
			text: true
		});
		$( ".toolbar_close button" ).button({
			icons: { secondary: "ui-icon-close" },
			text: true
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
		
		$('#btn_banners_save').click(function(event){
			event.preventDefault();
			var action = "banners_save";
			var banners_area =  $("#banners_area option:selected").val();
			var banners_id = $("#banners_id").val();
			var banners_name = $("#banners_name").val();
			var banners_file = $("#banners_file").val();
			var banners_header = $("#banners_header").val();
			var banners_url = $("#banners_url").val();
			var banners_target = $('input:checkbox[name=banners_target]').is(':checked') ? 1 : 0;
			var banners_active = $('input:checkbox[name=banners_active]').is(':checked') ? 1 : 0;
			var banners_tag = $("#banners_tag").val();
			var banners_width = $("#banners_width").val();
			var banners_height = $("#banners_height").val();
			var date_start = $("#date_start").val();
			var time_start = $("#time_start").val() ? $("#time_start").val() +':00' : '00:00:00';
			var datetime_start = date_start +' '+ time_start
			var date_end = $("#date_end").val();
			var time_end = $("#time_end").val() ? $("#time_end").val() +':00' : '23:59:00';
			if(date_end) {
				var datetime_end = date_end +' '+ time_end;
			} else {
				var datetime_end = null;
			}
			
			var token = $("#token").val();
			// ajax call
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_banners').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_banners').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&banners_area=" + banners_area + "&banners_id=" + banners_id +  "&banners_name=" + banners_name + "&banners_file=" + banners_file + "&banners_header=" + banners_header + "&banners_url=" + banners_url + "&banners_active=" + banners_active + "&banners_target=" + banners_target + "&banners_tag=" + banners_tag + "&banners_width=" + banners_width + "&banners_height=" + banners_height + "&datetime_start=" + datetime_start + "&datetime_end=" + datetime_end,
				success: function(message){	
					// show ajax reply in span id						
					ajaxReply(message,'#ajax_status_banners');
				},
			});
		});
		

		$('#btn_banners_delete').click(function(event){
			event.preventDefault();
			var action = "banners_delete";
			var banners_id = $("#banners_id").val();
			var banners_file = $("#banners_file").val();			
			var token = $("#token").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_banners').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_banners').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&banners_id=" + banners_id + "&banners_file=" + banners_file,
				success: function(message){	
					$(".edit_banner").hide();
					ajaxReply(message,'#ajax_status_banners');
				},
			});
		});
		

		$("#back").click(function(event) {
			event.preventDefault();
			history.back(1);
		});
		$( ".toolbar_back button" ).button({
			icons: {
				secondary: "ui-icon-arrowreturnthick-1-w"
			},
			text: true
		});
				
			
	});
</script>

<?php
if($row) {
	$i = 0;
	echo '<script>';
		$ext = pathinfo($row['file'], PATHINFO_EXTENSION);
		if($ext=='swf') {
			echo 'swfobject.embedSWF("../content/uploads/ads/'.$row['file'].'", "'.$row['name'].'", "'.$row['width'].'", "'.$row['height'].'", "'.$_SESSION['site_flash_version'].'"); ';
		}
		echo '$(document).ready(function() { ';
		if($ext=='swf') {
			echo '$("#wrapper-ads").append("<div id='.$row['name'].'>'.$row['name'].'</div>"); ';
		} else {
			echo '$("#wrapper-ads").wrap("<a href='.$row['url'].' target='.$row['url_target'].'></a>").append("<img src=../content/uploads/ads/'.$row['file'].' width='.$row['width'].' height='.$row['height'].' />"); ';
		}
		echo '}) ;';
	echo '</script>';

}

?>



<table style="width:100%;">
	<tr>
		<td>
			<h3 class="admin-heading">Edit banner</h3>
		</td>
		<td style="text-align:right;">
			<span class="toolbar_close"><button type="submit" onclick="parent.$.colorbox.close(); return false;">Close</button></span>&nbsp;
			<span class="toolbar_preview"><button type="submit" onclick="banners_preview(<?php echo $id; ?>)">Preview</button></span>
		</td>
	</tr>
</table>
<hr />


<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
<input type="hidden" name="banners_id" id="banners_id" value="<?php echo $row['banners_id']; ?>" />
<input type="hidden" name="file" id="banners_file" value="<?php echo $row['file']; ?>" />
<br />

<div class="admin-area-outer ui-widget ui-widget-content">
	<div class="admin-area-inner">

		<div class="edit_banner">
			
			<div id="wrapper-ads" class="edit_banner" style="float:right;width:500px;height:250px;overflow:auto;"></div>
			
			<table style="width:800px;border-spacing:10px;">
				<tr>
					<td cols="2">
					<input type="checkbox" id="banners_active" name="banners_active" value="1" <?php if ($row['active']=="1") {echo ' checked="checked"';}; ?> />
					active (published when checked)
					</td>
				</tr>
				<tr>
					<td cols="2">
					<label for="banners_name">Name: (not shown when published)</label><br />
					<input type="text" id="banners_name" value="<?php echo $row['name']; ?>" style="width:300px;" />
					</td>
				</tr>
				<tr>
					<td cols="2">
					<label for="banners_area">Show banner in area:</label><br />
					<select id="banners_area" class="code">
						<option value=""> --- </option>
						<option value="site" <?php if ($row['area']=="site") {echo ' selected="selected"';}; ?>>site (max width 978px)</option>
						<option value="outside" <?php if ($row['area']=="outside") {echo ' selected="selected"';}; ?>>outside (max width 222px)</option>
						<option value="column" <?php if ($row['area']=="column") {echo ' selected="selected"';}; ?>>column - inside (max width 222px)</option>
						<option value="row" <?php if ($row['area']=="row") {echo ' selected="selected"';}; ?>>row - inside (max width set by template)</option>
						<option value="overlay" <?php if ($row['area']=="overlay") {echo ' selected="selected"';}; ?>>overlay</option>
					</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<label for="banners_header">Header (leading text):</label><br />
					<input type="text" id="banners_header" value="<?php echo $row['header']; ?>" style="width:300px;" />
					</td>
				</tr>
				<tr>
					<td>
					<label for="banners_url">URL:</label><br />
					<input type="text" id="banners_url" value="<?php echo $row['url']; ?>" style="width:300px;" />
					</td>
					<td>
					<input type="checkbox" id="banners_target" name="banners_target" value="1" <?php if ($row['url_target']=="1") {echo ' checked="checked"';}; ?> />
					open link in new window
					</td>
				</tr>
				<tr>
					<td>
					<label for="banners_width">Width:</label><br />
					<input type="text" id="banners_width" style="width:50px" value="<?php echo $row['width']; ?>" /> px
					</td>
					<td>
					<label for="banners_height">Height:</label><br />
					<input type="text" id="banners_height" style="width:50px" value="<?php echo $row['height']; ?>" /> px
					</td>
				</tr>
				<tr>
					<td>
					<label for="date_start">Start publish:</label><br />
					<input type="text" id="date_start" title="yyyy-mm-dd" value="<?php echo $row['utc_start']; ?>" style="width:300px;" />
					</td>
					<td>
					<label for="date_end">End publish:</label><br />
					<input type="text" id="date_end" title="yyyy-mm-dd" value="<?php echo $row['utc_end']; ?>" style="width:300px;" />
					</td>
				</tr>
				<tr>
					<td cols="2">
					<label for="banners_tag">Tag: (set filter in pages)</label><br />
					<input type="text" id="banners_tag" value="<?php echo $row['tag']; ?>" style="width:300px;" />
					</td>
				</tr>
				<tr>
					<td cols="2">
					<span class="toolbar"><button id="btn_banners_save" name="btn_banners_save" class="edit_banner" type="submit">Save</button></span>
					<span class="toolbar"><button id="btn_banners_delete" name="btn_banners_delete" class="edit_banner" type="submit">Delete</button></span>
					<span id="ajax_spinner_banners" style="display:none;"></span>
					<span id="ajax_status_banners" style="display:none;"></span>
					</td>
				</tr>
			</table>


		</div>
	</div>
	
</div>


<?php 
// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>


<div class="footer-wrapper">
<?php include_once 'includes/inc.footer_cms.php'; ?>
</div>


</body>
</html>