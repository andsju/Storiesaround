<?php

// include core 
require_once '../includes/inc.core.php';

if(!get_role_CMS('superadministrator') == 1) {header('Location: index.php'); die;}


// css files
$css_files = array(
	CMS_DIR .'/cms/css/layout.css', 
	CMS_DIR .'/cms/libraries/jquery-ui/jquery-ui.css' );

// add css jquery-ui theme
$ui_theme = isset($_SESSION['site_ui_theme']) ? $_SESSION['site_ui_theme'] : '';
if(file_exists(CMS_ABSPATH .'/cms/libraries/jquery-ui/theme/'.$ui_theme.'/jquery-ui.css')) {
	if (($key = array_search(CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', $css_files)) !== false) {
		unset($css_files[$key]);
	}
	array_push($css_files, CMS_DIR.'/cms/libraries/jquery-ui/theme/'.$ui_theme.'/jquery-ui.css');
}


// javascript files
$js_files = array(
	CMS_DIR .'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR .'/cms/libraries/js/functions.js', 
	CMS_DIR .'/cms/libraries/jquery-plugin-validation/jquery.validate.js' );

?>
<!DOCTYPE html>
<html lang="en">

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Update Storiesaround CMS</title>

	<?php 
	foreach ( $css_files as $css ):
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen,projection" href="'.$css.'" />';
	endforeach; 
	?>

	<script type="text/javascript" src="<?php echo CMS_DIR; ?>/cms/libraries/jquery/jquery.min.js"></script>
	
</head>
<body>
	

<?php	
$t = (isset($_GET['t'])) ? $_GET['t'] : null;
// url
$this_url = $_SERVER['PHP_SELF'] .'?t='. $t;

echo '<img src="'.CMS_DIR.'/cms/css/images/storiesaround_logotype_black.png" alt="Storiesaround logotype" style="width:120px;float:right;"/>';

echo '<div class="admin-heading">';
	echo '<div class="float">';
		echo '<div class="cms-ui-icons cms-ui-updates"></div>';
		echo '<div class="float">';
			echo '<a href="'.CMS_HOME.'/help/'.$t.'" target="_blank" title="How to - '.$t.'"><h1 class="admin-heading" style="color:#FFF;">Update</h1></a>';
		echo '</div>';
	echo '</div>';
	echo '<div class="ui-tabs ui-widget" style="float:right;margin-top:22px;padding: 0em;">';
		get_tab_menu_jquery_ui_look_alike($this_url, array("?"), array("?"), "tg", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
	echo '</div>';	
echo '</div>';

foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>


<div style="margin:10px 0 0 0;">

<div class="admin-area-outer ui-widget ui-widget-content">
	<div class="admin-area-inner">


<script type="text/javascript">

	function callBackFunction(data) {
		console.log(data);
	}			

	$(document).ready(function() {

		$( ".toolbar button" ).button({
		});

		$('#site_check_version').click(function(event){
			event.preventDefault();
			var action = "storiesaround_update";
			var token = $("#token").val();
			var version = $("#version").val();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_check_version').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_check_version').hide()",700)},
				type: 'GET',
				url: 'http://storiesaround.com/version.php',
				success: function(json) {
				   var obj = JSON.parse(json);
				   console.log(obj.version);				   
					if(version == obj.version) {
						s = 'Nice - updated!'; 
					} else {
						if (version < obj.version) {
							s = 'Found new version'; 
							s += ' You can download Storiesaround '+obj.version;
						} else {
							s = 'Check if new version exists and download / install it manually'; 
						}
					}
					$("#ajax_status_site_check_version").empty().append(s).show();
				},
				error: function(e) {
				   console.log(e.message);
				}
			});	
		});
			
		
		$('#site_do_it_all').click(function(event){
			event.preventDefault();
			
			$("#dialog_update").dialog('open','option', 'position', 'center');
			$("#dialog_update").dialog({
				buttons : {
				"Update" : function() {
					$(this).dialog("close");
					
					//////////
					var action = "site_do_it_all";
					var token = $("#token").val();

					$.ajax({
						beforeSend: function() { loading = $('#ajax_spinner_site_do_it_all').show()},
						complete: function(){ loading = setTimeout("$('#ajax_spinner_site_do_it_all').hide()",700)},
						type: 'POST',
						url: 'update_ajax.php',
						data: "action=" + action + "&token=" + token,
						success: function(message){	
							$("#ajax_status_site_do_it_all").empty().append(message).show();	
							
							
							//////////
							var action = "site_backup_db";
							var token = $("#token").val();

							$.ajax({
								beforeSend: function() { loading = $('#ajax_spinner_backup_db').show()},
								complete: function(){ loading = setTimeout("$('#ajax_spinner_backup_db').hide()",700)},
								type: 'POST',
								url: 'update_ajax.php',
								data: "action=" + action + "&token=" + token,
								success: function(message){	
									$("#ajax_status_backup_db").empty().append(message).show();					


									//////////
									var action = "site_backup_cms";
									var token = $("#token").val();

									$.ajax({
										beforeSend: function() { loading = $('#ajax_spinner_backup_cms').show()},
										complete: function(){ loading = setTimeout("$('#ajax_spinner_backup_cms').hide()",700)},
										type: 'POST',
										url: 'update_ajax.php',
										data: "action=" + action + "&token=" + token,
										success: function(message){	
											$("#ajax_status_backup_cms").empty().append(message).show();	
																							

											//////////
											var action = "site_copy_from_zip";
											var token = $("#token").val();

											$.ajax({
												beforeSend: function() { loading = $('#ajax_spinner_site_copy_from_zip').show()},
												complete: function(){ loading = setTimeout("$('#ajax_spinner_site_copy_from_zip').hide()",700)},
												type: 'POST',
												url: 'update_ajax.php',
												data: "action=" + action + "&token=" + token,
												success: function(message){	
													$("#ajax_status_site_copy_from_zip").empty().append(message).show();	
													
													$(".site_update_db").show();	
													
												},
											});
											//////////
											
										},
									});
									//////////
									
								},
							});
							//////////
							
						},
					});
							
					//////////
				},
				"Cancel" : function() {
					$(this).dialog("close");
					}
				}
			});
							
			
		});
	
		$('#site_backup_db').click(function(event){
			event.preventDefault();
			var action = "site_backup_db";
			var token = $("#token").val();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_backup_db').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_backup_db').hide()",700)},
				type: 'POST',
				url: 'update_ajax.php',
				data: "action=" + action + "&token=" + token,
				success: function(message){	
					$("#ajax_status_backup_db").empty().append(message).show();					
				},
			});
		});

		$('#site_backup_cms').click(function(event){
			event.preventDefault();
			var action = "site_backup_cms";
			var token = $("#token").val();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_backup_cms').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_backup_cms').hide()",700)},
				type: 'POST',
				url: 'update_ajax.php',
				data: "action=" + action + "&token=" + token,
				success: function(message){	
					$("#ajax_status_backup_cms").empty().append(message).show();					
				},
			});
		});

		$('#site_copy_from_zip').click(function(event){
			event.preventDefault();
			var action = "site_copy_from_zip";
			var token = $("#token").val();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_backup_cms').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_backup_cms').hide()",700)},
				type: 'POST',
				url: 'update_ajax.php',
				data: "action=" + action + "&token=" + token,
				success: function(message){	
					$("#ajax_status_backup_cms").empty().append(message).show();					
				},
			});
		});

	
		$('#site_update_database').click(function(event){
			if(confirm('Run update command?')) {
			
				event.preventDefault();
				var action = "site_update_alter";
				var token = $("#token").val();

				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_site_update').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_site_update').hide()",700)},
					type: 'POST',
					url: 'update_ajax.php',
					data: "action=" + action + "&token=" + token,
					success: function(message){	
						$("#ajax_status_site_update").empty().append(message).show();					
					},
				});
			}
			return false;
		});

		$('#site_version_check_read_file').click(function(event){				
			event.preventDefault();
			var action = "site_version_check_read_file";
			var token = $("#token").val();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_version_check').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_version_check').hide()",700)},
				type: 'POST',
				url: 'update_ajax.php',
				data: "action=" + action + "&token=" + token,
				success: function(message){	
					$("#ajax_status_site_update").empty().append(message).show();					
				},
			});
			return false;
		});

		$('#site_version_swap').click(function(event){				
			event.preventDefault();
			var action = "site_version_swap";
			var token = $("#token").val();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_site_version_swap').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_site_version_swap').hide()",700)},
				type: 'POST',
				url: 'update_ajax.php',
				data: "action=" + action + "&token=" + token,
				success: function(message){	
					$("#ajax_status_site_swap").empty().append(message).show();	
				},
			});
			return false;
		});

		
		$("#dialog_update").dialog({
			autoOpen: false,
			modal: true
		});		
		
		$( document ).tooltip();


	});

</script>


	<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token']; ?>" />
	<input type="hidden" name="version" id="version" value="<?php echo CMS_VERSION; ?>" />
	<h3>Update Storiesaround</h3>
		 
	<div class="admin-panel">

		<div style="padding:10px;background:#fff;border:1px solid black;">
		Important: <b>Always backup</b> database <b>(database admin tool)</b> and folders / files structure <b>(ftp software)</b> before update.
		</div>
		
		<table style="width:100%;">
			<tr>
				<td style="width:250px;">
					Current version:
				</td>
				<td>
				</td>
				<td>
					<pre><?php echo CMS_VERSION;?></pre>
				</td>
			</tr>
			<tr>
				<td>
					<span class="toolbar"><button id="site_check_version">Check newer version</button></span>
				</td>
				<td>
					<span id="ajax_spinner_site_check_version" style="display:none;"><img src="<?php echo CMS_DIR; ?>/cms/css/images/spinner.gif" alt="spinner" /></span>
				</td>
				<td>
					<div><span id="ajax_status_site_check_version"></span></div>
				</td>
			</tr>
			<tr>
				<td>
					<span class="toolbar"><button id="site_do_it_all">Backup</button></span>
				</td>
				<td>
					<span id="ajax_spinner_site_do_it_all" style="display:none;"><img src="<?php echo CMS_DIR; ?>/cms/css/images/spinner.gif" alt="spinner" /></span>
				</td>
				<td>
					<div><span id="ajax_status_site_do_it_all"></span></div>
				</td>
			</tr>
			<tr>
				<td>
					<span class="toolbar hide"><button id="site_backup_db">Backup database</button></span>
				</td>
				<td>
					<span id="ajax_spinner_backup_db" style="display:none;"><img src="<?php echo CMS_DIR; ?>/cms/css/images/spinner.gif" alt="spinner" /></span>
				</td>
				<td>
					<div><span id="ajax_status_backup_db"></span></div>
				</td>
			</tr>
			<tr>
				<td>
					<span class="toolbar hide"><button id="site_backup_cms">Backup cms file</button></span>
				</td>
				<td>
					<span id="ajax_spinner_backup_cms" style="display:none;"><img src="<?php echo CMS_DIR; ?>/cms/css/images/spinner.gif" alt="spinner" /></span>
				</td>
				<td>
					<div><span id="ajax_status_backup_cms"></span></div>
				</td>
			</tr>
			<tr>
				<td>
					<span class="toolbar hide"><button id="site_copy_from_zip">Unzip...</button></span>
				</td>
				<td>
					<span id="ajax_spinner_site_copy_from_zip" style="display:none;"><img src="<?php echo CMS_DIR; ?>/cms/css/images/spinner.gif" alt="spinner" /></span>
				</td>
				<td>
					<div><span id="ajax_status_site_copy_from_zip"></span></div>
				</td>
			</tr>
			<tr>
				<td>
					
					<span class="toolbar site_update_db"><button id="site_update_database" style="background:navy;color:#fff;">Update database</button></span>
				</td>
				<td>
					<span id="ajax_spinner_site_update" style="display:none;"><img src="<?php echo CMS_DIR; ?>/cms/css/images/spinner.gif" alt="spinner" /></span>
				</td>
				<td>
					<div><span id="ajax_status_site_update"></span></div>
				</td>
			</tr>
			<tr>
				<td>
					<span class="toolbar"><button id="site_version_check_read_file">Version check - read file</button></span>
				</td>
				<td>
					<span id="ajax_spinner_site_version_check" style="display:none;"><img src="<?php echo CMS_DIR; ?>/cms/css/images/spinner.gif" alt="spinner" /></span>
				</td>
				<td>
					<div><span id="ajax_status_site_version_check"></span></div>
				</td>
			</tr>
			<tr>
				<td>
					<span class="toolbar"><button id="site_version_swap">Swap...</button></span>
				</td>
				<td>
					<span id="ajax_spinner_site_version_swap" style="display:none;"><img src="<?php echo CMS_DIR; ?>/cms/css/images/spinner.gif" alt="spinner" /></span>
				</td>
				<td>
					<div><span id="ajax_status_site_swap"></span></div>
				</td>
			</tr>
		</table>

	</div>
	
</div>
		
<div id="dialog_update" title="Update CMS">
  <p>
  <b>Always</b> backup database and folders/files structure before CMS update. 
  </p>
  <p>
  <b>Backup files/folders</b> using ftp software or web hosting utility.
  </p>
  <p>
  <b>Backup database</b> using software from web hosting, such as phpMyAdmin or mysqldump command.  
  </p>
  <p>
  That is the only way to "reinstall" after upgrading... 
  </p>
  <p>
  Now...Continue?
  </p>
</div>

</div>
</div>

<div class="footer-wrapper">
	<hr />
	<?php include_once CMS_ABSPATH .'/cms/includes/inc.footer_cms.php'; ?>
</div>

</body>
</html>