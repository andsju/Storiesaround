<?php

// include core 
//--------------------------------------------------
include_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';

if(!get_role_CMS('administrator') == 1) {header('Location: index.php'); die;}

$selections = new Selections();

// wysiwyg editor
$wysiwyg_editor = isset($_SESSION['site_wysiwyg']) ? get_editor_settings($editors, $_SESSION['site_wysiwyg']) : null;

// css files
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css'
	//CMS_DIR.'/cms/libraries/jquery-datatables/style.css
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
	//CMS_DIR.'/cms/libraries/js/pages_calendar.js'
	//CMS_DIR.'/cms/libraries/tinymce/plugins/moxiemanager/js/moxman.loader.min.js'
);

// javascript files... add wysiwyg file
if (is_array($wysiwyg_editor)) {
	if(file_exists(CMS_ABSPATH .'/cms/libraries/'.$wysiwyg_editor['include_js_file'])) {
		array_push($js_files, CMS_DIR.'/cms/libraries/'.$wysiwyg_editor['include_js_file']);
	}
}


// include header 
$page_title = "Edit";
$body_style = "width:96%;margin:0 auto;";
require 'includes/inc.header_minimal.php';


$class_editor = $wysiwyg_editor['css-class'];
?>


<script>

	function selection_preview(id) {
		w=window.open('pages_selections_preview.php?token=<?php echo $_SESSION['token']; ?>&id='+id,'','width=1280,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
		w.focus();
	}

	$(document).ready(function() {

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
		
		$('#btn_selections_save').click(function(event){
			event.preventDefault();
			var action = "selections_save";
			var token = $("#token").val();
			var pages_selections_id = $("#pages_selections_id").val();
			var active = $('input:checkbox[name=active]').is(':checked') ? 1 : 0;
			var name = $("#name").val();
			var description = $("#description").val();
			var external_js = $("#external_js").val();
			var external_css = $("#external_css").val();
			var area = $("#area option:selected").val();
			var position = $("#position option:selected").val();
			var content_html = get_textarea_editor('<?php echo $_SESSION['site_wysiwyg']; ?>', 'content_html');			
			var content_code = $("#content_code").val();
			//var content_code = encodeURIComponent(content_code);
			var grid_content = $("#grid_content").val();
			var grid_cell_template = $('input:checkbox[name=grid_cell_template]').is(':checked') ? 1 : 0;
			var grid_custom_classes = $("#grid_custom_classes").val();
			var grid_cell_image_height = $("#grid_cell_image_height").val();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_selections').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_selections').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: { 
					action: action, token: token, pages_selections_id: pages_selections_id, active: active,
					name: name, description: description, external_js: external_js, external_css: external_css, 
					area: area, position: position, content_html: content_html, content_code: content_code,
					grid_cell_template: grid_cell_template, grid_custom_classes: grid_custom_classes,
					grid_content: grid_content, grid_cell_image_height: grid_cell_image_height
				},
				
				//data: "action=" + action + "&token=" + token + "&active=" + active + "&pages_selections_id=" + pages_selections_id + "&name=" + name + "&description=" + description + "&external_js=" + external_js + "&external_css=" + external_css + "&area=" + area + "&position=" + position + "&content_html=" + content_html + "&content_code=" + content_code,
				success: function(newdata){	
					ajaxReply(newdata,'#ajax_status_selections');
				},
			});
		});
		$('#btn_selections_delete').click(function(event){
			event.preventDefault();
			var action = "selections_delete";
			var token = $("#token").val();
			var pages_selections_id = $("#pages_selections_id").val();
			$.ajax({
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&pages_selections_id=" + pages_selections_id,
				success: function(newdata){	
					$("#selections_form").empty().append(newdata).hide().fadeIn('slow');
				},
			});
		});
		
		// allow tab in textarea
		$("textarea").keydown(function(e) {
			if(e.keyCode === 9) { 
				// get caret position/selection
				var start = this.selectionStart;
				var end = this.selectionEnd;
				var $this = $(this);
				var value = $this.val();

				$this.val(value.substring(0, start)
					+ "\t"
					+ value.substring(end));

				// put caret at right position again (add one for the tab)
				this.selectionStart = this.selectionEnd = start + 1;

				// prevent the focus lose
				e.preventDefault();
			}
			
		});	

		<?php
		// add wysiwyg js file
		if (is_array($wysiwyg_editor)) {
			if(file_exists(CMS_ABSPATH .'/cms/libraries/'.$wysiwyg_editor['include_js_script'])) {
				include CMS_ABSPATH.'/cms/libraries/'.$wysiwyg_editor['include_js_script'];
			}
		}
		?>
		
		
	});

	
</script>


<?php

if(isset($_GET['token'])){
	// only accept $_POST from this token
	if($_GET['token'] == $_SESSION['token']) {
		$id = $_GET['id'];
		$row = $selections->getSelectionsContent($id);
		if(!$row) {
			die;
		}
		
		echo '<table style="width:100%;">';
			echo '<tr>';
				echo '<td>';
				echo '<h3 class="admin-heading">Edit selections</h3>';
				echo '</td>';
				echo '<td style="text-align:right;">';
				echo '<span class="toolbar_close"><button type="submit" onclick="parent.$.colorbox.close(); return false;">Close</button></span>&nbsp;';
				echo '<span class="toolbar_preview"><button type="submit" onclick="selection_preview('.$id.')">Preview</button></span>';
				echo '</td>';
			echo '</tr>';
		echo '</table>';
		echo '<hr />';
		
		echo '<div class="admin-area-outer ui-widget ui-widget-content">';
			echo '<div class="admin-area-inner">';

				$checked = ($row[0]['active']==1) ? " checked=checked" : null;
				echo '<input type="hidden" name="token" id="token" value="'.$_SESSION['token'].'">';
				echo '<input type="hidden" name="pages_selections_id" id="pages_selections_id" value="'.$id.'">';
			
				$pages = new Pages();
				$result = $pages->getPagesSelections($id);
				$button = (!$result) ? null : ' disabled=disabled';

				echo '<div id="selections_form">';
				
					echo '<table style="width:100%;">';
						echo '<tr>';
							echo '<td>';
								echo 'Name<br /><input type="text" name="name" id="name" value="'. $row[0]['name'] .'" style="width:200px;">';
							echo '</td>';
							echo '<td>';
								echo 'Area<br />';
								echo '<select id="area" class="code">';
									$values = array("" => "", "Header - above" => "header_above", "Header (replace default)" => "header", "Header - below" => "header_below", "Left sidebar - top" => "left_sidebar_top", "Left sidebar - bottom" => "left_sidebar_bottom", "Right sidebar - top" => "right_sidebar_top", "Right sidebar - bottom" => "right_sidebar_bottom", "Content - above" => "content_above", "Content - inside" => "content_inside", "Content - below" => "content_below", "Footer - above" => "footer_above");
									foreach($values as $key => $value) {
										echo '<option value="'.$value.'"';
										if($row[0]['area'] == $value) {
											echo ' selected=selected';
										}
										echo '>'. $key .'</option>';
									}
								echo '</select>';								
								echo '<input type="hidden" id="position" value="10" />';
							
							echo '</td>';
							echo '<td width="30%">';
								echo '<input type="checkbox" name="active" id="active" value="1" "'.$checked.'> Active &nbsp;';
								echo '<span id="ajax_spinner_selections" style="display:none;"><img src="css/images/spinner.gif"></span><span id="ajax_status_selections" style="display:none;"></span>';
							echo '</td>';
							echo '<td>';
								echo '<div style="float:right";>';
									echo '<span class="toolbar"><button id="btn_selections_delete" value="btn_selections_delete" '.$button.' title="Selections can be deleted when not in use">Delete</button></span>&nbsp;';
									echo '<span class="toolbar"><button id="btn_selections_save" value="btn_selections_save">Save</button></span>';
								echo '</div>';
							echo '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td colspan="4" style="padding-top:10px;">';
								echo 'Description<br /><input type="text" name="description" id="description" value="'. $row[0]['description'] .'" style="width:100%;">';
							echo '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td colspan="4" style="padding-top:10px;">';
								echo 'External CSS file(s) - seperate width space if more than one<br /><input type="text" name="external_css" id="external_css" value="'. $row[0]['external_css'] .'" style="width:100%;" class="code">';
							echo '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td colspan="4" style="padding-top:10px;">';
								echo 'External Javascript file(s) - seperate width space if more than one (loads at bottom of page)<br /><input type="text" name="external_js" id="external_js" value="'. $row[0]['external_js'] .'" style="width:100%;" class="code">';
							echo '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td style="padding-top:10px;" colspan="4">';
								?>
								<h4>Content</h4>
								<textarea name="content_html" id="content_html" class="<?php echo $class_editor; ?>" style="width:100%;"><?php echo $row[0]['content_html']; ?></textarea>								
								<?php
							echo '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td style="padding-top:10px;" colspan="4">';
								?>
								<h4>Grid json</h4>
								<p>Paste json code (export grid from a page)</p>
								<textarea name="grid_content" id="grid_content" style="width:100%;height:100px;" class="code"><?php echo $row[0]['grid_content']; ?></textarea>
								<p>
									<span>Custom CSS class (grid wrapper):</span>
									<input type="text" name="grid_custom_classes" id="grid_custom_classes" value="<?php echo $row[0]['grid_custom_classes']; ?>">
									<span style="margin-left:30px">Image below heading: </span>
									<?php
									$checked = ($row[0]['grid_cell_template']==1) ? " checked=checked" : null;
									?>
									<input type="checkbox" id="grid_cell_template" name="grid_cell_template" <?php echo $checked; ?>>
									<span style="margin-left:30px"> Grid images height: </span>
									<input type="text" id="grid_cell_image_height" name="grid_cell_image_height" value="<?php echo $row[0]['grid_cell_image_height']; ?>" style="width:50px">
								</p>
								<?php
							echo '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td style="padding-top:10px;" colspan="4">';
								?>
								<h4>Code</h4>
								Code written between double square brackets <span class="code highlight">[[ ? ]]</span> will be parsed. Use storiesaround code syntax below.
								<p>
								<i>Write code inside <b>html element</b></i>, like <span class="code highlight"><b>&lt;div&gt;</b>[[ ? ]]<b>&lt;/div&gt;</b></span>
								</p>
								</p>
								<table class="code-hints">
									<tr>
										<th>Type of stories</th>
										<th>Code syntax</th>
										<th>Code example</th>
									</tr>
									<tr>
										<td>Selected stories</td>
										<td><code>[[story selected "id of pages ( comma-seperated if multiple)"]]</code></td>
										<td><code>[[story selected "5,7,12"]]</code></td>
									</tr>
									<tr>
										<td>Promoted stories</td>
										<td><code>[[story promoted filter "page tag" limit "max number"]]</code></td>
										<td><code>[[story promoted filter "home" limit "7"]]</code></td>
									</tr>
									<tr>
										<td>Child stories</td>
										<td><code>[[story child parent "id of parent page"]]</td>
										<td><code>[[story child parent "3"]]</code></td>
									</tr>
									<tr>
										<td>Event stories</td>
										<td><code>[[story event filter "page tag" date "next|previous"]]</td>
										<td><code>[[story event filter "music" date "next"]]</code></td>
									</tr>
								</table>								
								
								<textarea name="content_code" id="content_code" style="width:100%;height:100px;" class="code"><?php echo htmlspecialchars($row[0]['content_code']); ?></textarea>
								</div>
								<?php
							echo '</td>';
						echo '</tr>';
					echo '</table>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}

// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>

<div class="footer-wrapper">
	<?php include_once 'includes/inc.footer_cms.php'; ?>
</div>

</body>
</html>