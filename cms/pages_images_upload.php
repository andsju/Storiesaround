
<?php

// include core 
//--------------------------------------------------
require_once 'includes/inc.core.php';


// include session access 
//--------------------------------------------------
require_once 'includes/inc.session_access.php';
if(!get_role_CMS('contributor') == 1) {
	die;
}


if (isset($_GET['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_GET['token'] == $_SESSION['token']) {
	
		
		// check client user-agent, prevent session been hijacked
		if($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			die('User agent fail. Please logout and login again.');
		}
		
		if ($pages_id = filter_input(INPUT_GET, 'pages_id', FILTER_VALIDATE_INT)) { 
	
		$css_files = array(
			CMS_DIR.'/cms/css/normalize.css', 
			CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
			CMS_DIR.'/cms/css/layout.css', 
			CMS_DIR.'/cms/css/pages_edit.css', 
			CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
			CMS_DIR.'/cms/libraries/fileuploader/fileuploader.css' 
		);		

		$page_title = "Upload image(s)";
		
		$body_style = "width:100%;margin:20px";

		include_once 'includes/inc.header_minimal.php';

		$js_files = array(
			CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
			CMS_DIR.'/cms/libraries/js/functions.js', 
			CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
			CMS_DIR.'/cms/libraries/fileuploader/fileuploader.js' 
		);

		echo '<div id="main-wrapper">';

		foreach ( $js_files as $js ):
			echo '<script src="'.$js.'"></script>';
		endforeach; 
		?>

		<script>
			$(document).ready(function() {		
				var token = $("#token").val();
				var pages_folder = $("#pages_id").val();
				var original = $("#original").val();
				var max_width = $("#max_width").val();
				
				var running = 0;
				var uploader = new qq.FileUploader({
					multiple: true,
					element: document.getElementById('file-uploader'),
					action: 'pages_images_upload_ajax.php',
					allowedExtensions: ['jpg', 'png', 'gif', 'jpeg'],
					template: 
						'<div class="qq-uploader">' + 
						'<div class="qq-upload-drop-area" style="border:1px dashed black; min-height:200px"><span>Drop files here to upload</span></div>' +
						'<div class="qq-upload-button" style="width:100%;height:50px;font-size:1.2em;padding:20px">Upload images (click or drop)</div>' +
						'<ul class="qq-upload-list"></ul>' + 
						'</div>',
					
					//debug: true,
					params: {token: token, pages_folder: pages_folder, max_width: max_width, original: original},
					sizeLimit: 10520000,

					onSubmit: function(id, fileName){
						running++;
					},
					onComplete: function(id, fileName, responseJSON){
						running--;				

						var filename_server = responseJSON['filename'];							
						$('#filesUploaded').append('<div class=imbox><a href=../content/uploads/pages/' +pages_folder+ '/' +filename_server+ ' class=colorbox_edit><img src=../content/uploads/pages/' +pages_folder+ '/'+filename_server+' title='+filename_server+'></img></a></div>');
						
						if(running==0){
							//$('#file-uploader').fadeOut().hide().html('<h5 style="margin-bottom:10px;">Uploaded - close window</h5>').fadeIn();
						}
					},
				});
			});
		</script>	

		<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>">
		<input type="hidden" name="pages_id" id="pages_id" value="<?php echo $_GET['pages_id'];?>">
		<input type="hidden" name="original" id="original" value="<?php echo $_GET['original'];?>">
		<input type="hidden" name="max_width" id="max_width" value="<?php echo $_GET['max_width'];?>">

		<div id="file-uploader">
			<noscript>          
				<p>Please enable JavaScript to use file uploader.</p>
				<!-- or put a simple form for upload here -->
			</noscript>         
		</div>

		<div id="filesUploaded" class="clearfix">
		</div>
		

		<?php
		}
	}
}
?>