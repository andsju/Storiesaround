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


// include header
//--------------------------------------------------
$page_title = "Edit tag";
$body_style = "width:50%;margin:0 auto;";
require 'includes/inc.header_minimal.php';


// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>



<script>
	$(document).ready(function() {

		$('body').css({
			'position' : 'absolute',
			'left' : '50%',
			'top' : '33%',
			'margin-left' : -$('body').outerWidth()/2,
			'margin-top' : -$('body').outerHeight()/3
		});
	
		$( ".toolbar button" ).button({
			icons: {
				secondary: "ui-icon-trash"
			},
			text: true
		});
	
		$('#btn_delete_tag').click(function(event){
			event.preventDefault();
			var deletable = true;
			var tags_id = $("#tags_id").val();
			var action = "pages_delete_tag";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var tags_pages = $("#tags_pages").val();
			var tags_images = $("#tags_images").val();
			var tags_banners = $("#tags_banners").val();
			if(tags_pages!=0 || tags_images!=0 || tags_banners!=0) {
				alert('Please remove this tag from content below');
				var deletable = false;
			}
			if(deletable) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_tag').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_tag').hide()",700)},
					type: 'POST',
					url: 'pages_edit_ajax.php',
					data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&tags_id=" + tags_id,
					success: function(newdata){	
						$('#handle_tags').empty();
						$('#handle_tags').html('tag deleted');
					}
				});
			}
		});
	});

</script>

<?php

if(!get_role_CMS('editor') == 1) {header('Location: index.php'); die;}

?>


<div style="width:100%;clear:both;padding:20px;" class="ui-widget ui-widget-content">
	<div id="handle_tags">
	<div style="width:96%;overflow:auto;">

		<?php
		
		$tags_id = isset($_GET['tags_id']) ? $_GET['tags_id'] : null;
		
		if(!$tags_id) {die;}
		
		$tags = new Tags();
		$row = $tags->getTag($tags_id);
		
		if(isset($row)) {
			$tag = $row['tag'];

			if(strlen($tag)==0) {die;}
			
			?>
			<div style="width:96%;">

				<h3 class="admin-heading">Tag '<?php echo $tag; ?>' <span class="ui-icon ui-icon-tag" style="display:inline-block;"></span></h3>
				<p>
					<input type="hidden" id="tags_id" name="tags_id" style="width:200px;" value="<?php echo $row['tags_id']; ?>" />
					<span class="toolbar"><button id="btn_delete_tag">Delete tag</button></span>
					<span id="ajax_spinner_tag" style='display:none'><img src="css/images/spinner.gif"></span>
					&nbsp;<span id="ajax_result_tag"></span>
				</p>

			</div>
			<p>
			Searching for tag '<?php echo $tag; ?>' in database:
			</p>
			<?php
			//print_r2($row);
			
			// check use of this tag
			
			$pages = new Pages();
			
			$rows = $pages->getPagesTag($tag);
			echo '<h4 class="admin-heading">Pages</h4>';
			
			if(isset($rows)) {
				echo count($rows);
				echo '<input type="hidden" id="tags_pages" value="'.count($rows).'">';
				echo '<ul class="tags_match">';
				foreach($rows as $row) {
					echo '<li><span class="ui-icon ui-icon-tag" style="display:inline-block;"></span> '.$row['title'].' (id: '.$row['pages_id'].') <a href="pages_edit.php?id=' .$row['pages_id'] .'&token='.$_SESSION['token'].'"><span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a></li>';
				}
				echo '</ul>';
				//print_r2($rows);
			}
			if(!count($rows)) { echo 'not found';}
			
			$rows = null;
			
			$rows = $pages->getPagesImagesTag($tag);
			echo '<h4 class="admin-heading">Pages | Images</h4>';
			
			if(isset($rows)) {
				echo count($rows);
				echo '<input type="hidden" id="tags_images" value="'.count($rows).'">';
				echo '<ul class="tags_match">';
				$id = 0;
				$s = null;
				foreach($rows as $row) {					
					if($id!=$row['pages_id']) {
						if($s) {
							echo '<li class="pre" style="padding:0 0 6px 20px;">&raquo; '.$s.'<span class="ui-icon ui-icon-tag" style="display:inline-block;"></li>';
							$s = null;
						}
						echo '<li>'.$row['title'].' (id: '.$row['pages_id'].')</li>';
						 $s .= strlen($row['filename'])>10 ? substr($row['filename'],0,7).'...'.substr($row['filename'],-3,3).' | ' : $row['filename'].'| ';
					} else {
						$s .= strlen($row['filename'])>10 ? substr($row['filename'],0,7).'...'.substr($row['filename'],-3,3).' | ' : $row['filename'].' | ';
					}
					$id = $row['pages_id'];
				}
				if($s) {
					echo '<li class="pre" style="padding:0 0 6px 20px;">&raquo; '.$s.'<span class="ui-icon ui-icon-tag" style="display:inline-block;"></li>';
					$s = null;
				}				
				echo '</ul>';
				//print_r2($rows);
			}
			if(!count($rows)) { echo 'not found';}
						
			$rows = null;
			
		}
		?>

	</div>
	</div>
</div>

<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
<input type="hidden" id="users_id" name="users_id" value="<?php echo $_SESSION['users_id']; ?>">

</body>
</html>