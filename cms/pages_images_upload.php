
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
	
	
		// validate pages_id
		if ($pages_id = filter_input(INPUT_GET, 'pages_id', FILTER_VALIDATE_INT)) { 
	

		/*** css files, loaded in inc.header.php ***/
		//--------------------------------------------------

		$css_files = array(
			CMS_DIR.'/cms/css/normalize.css', 
			CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
			CMS_DIR.'/cms/css/layout.css', 
			CMS_DIR.'/cms/css/pages_edit.css', 
			CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
			CMS_DIR.'/cms/libraries/fileuploader/fileuploader.css' );
		

		$page_title = "Upload image(s)";
		
		/*** include header ***/
		$body_style = "width:600px;";

		//--------------------------------------------------
		include_once 'includes/inc.header_minimal.php';



		// load javascript files
		$js_files = array(
			CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
			CMS_DIR.'/cms/libraries/js/functions.js', 
			CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
			CMS_DIR.'/cms/libraries/fileuploader/fileuploader.js' );


		echo '<div id="main-wrapper">';

		// load javascript files
		foreach ( $js_files as $js ):
			echo '<script type="text/javascript" src="'.$js.'"></script>';
		endforeach; 
		?>

		<script type="text/javascript">
			// function to rerun $(document).ready when new elements are added
			// remove this function ... or...
			function document_ready(file) {
				$(document).ready(function() {
					$(".colorbox_edit").colorbox({
						width:"98%", 
						height:"98%", 
						iframe:true, 
						onClosed:function(){ 
							// location.reload(true); 
						}
					});
				});
			}
			document_ready();
		</script>



		<script type="text/javascript">
			$(document).ready(function() {		
				var token = $("#token").val();
				var pages_folder = $("#pages_id").val();
				var running = 0;
				var uploader = new qq.FileUploader({
					multiple: true,
					element: document.getElementById('file-uploader'),
					action: 'pages_images_upload_ajax.php',
					allowedExtensions: ['jpg', 'png', 'gif', 'jpeg'],
					template: '<div class="qq-uploader">' + 
							'<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
							'<div class="qq-upload-button">Upload file(s)</div>' +
							'<ul class="qq-upload-list"></ul>' + 
						 '</div>',
					
					//debug: true,
					params: {token: ''+token+'', pages_folder: ''+pages_folder+''},
					sizeLimit: 10520000,

					onSubmit: function(id, fileName){
						running++;
					},
					onComplete: function(id, fileName, responseJSON){
						running--;				

						var filename_server = responseJSON['filename'];							
						$('#filesUploaded').append('<div class=imbox><a href=../content/uploads/pages/' +pages_folder+ '/' +filename_server+ ' class=colorbox_edit><img src=../content/uploads/pages/' +pages_folder+ '/'+filename_server+' title='+filename_server+'></img></a></div>');						
						
						if(running==0){
							$('#file-uploader').fadeOut().hide().html('<h5 style="margin-bottom:10px;">Uploaded - close window</h5>').fadeIn();
							document_ready();
						}
					},
				});
			});
		</script>	

		
		
		<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
		<input type="hidden" name="pages_id" id="pages_id" value="<?php echo $_GET['pages_id'];?>" />

		<div id="file-uploader">
			<noscript>          
				<p>Please enable JavaScript to use file uploader.</p>
				<!-- or put a simple form for upload here -->
			</noscript>         
		</div>

		<div id="filesUploaded"  class="clearfix">
		</div>
		
		
		<?php
		}
	}
}
?>