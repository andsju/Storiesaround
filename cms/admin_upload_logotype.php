<?php

require_once 'includes/inc.core.php';
require_once 'includes/inc.session_access.php';

if(!get_role_CMS('administrator') == 1) {
	die;
}

if (isset($_GET['token'])){

	if ($_GET['token'] == $_SESSION['token']) {
	
		if($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			die('User agent fail. Please logout and login again.');
		}

		$css_files = array(
			CMS_DIR.'/cms/css/normalize.css', 
			CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
			CMS_DIR.'/cms/css/layout.css', 
			CMS_DIR.'/cms/css/pages_edit.css', 
			CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
			CMS_DIR.'/cms/libraries/fileuploader/fileuploader.css'
		);
		
		$page_title = "Upload files";
		$body_style = "width:600px;";

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
                
                $(".qq-upload-drop-area").show();

                var params = window.location.search.substr(1).split("&");
                var tmp;
                tmp = params[0].split("=");
                var token = tmp[1];
                tmp  = params[1].split("=");
                var folder = tmp[1];
                tmp  = params[2].split("=");
                var overwrite = tmp[1];
				var running = 0;
				var uploader = new qq.FileUploader({
					multiple: false,
					element: document.getElementById('file-uploader'),
					action: 'admin_upload_files.php',
					allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
					//debug: true,
					params: {token: token, folder: folder, overwrite: overwrite, logo: true},
					sizeLimit: 10520000,

					onSubmit: function(id, fileName){
						running++;
					},
					onComplete: function(id, fileName, responseJSON){
						running--;				

                        var filename_server = responseJSON['filename'];                        
                        
						if(running==0){
                            $("#upload_response").append("OK!");
						}
					},
				});
			});
		</script>	

		<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />

		<div id="file-uploader">
			<noscript>          
				<p>Please enable JavaScript to use file uploader.</p>
			</noscript>         
		</div>
        
		<div id="upload_response" class="clearfix">
		</div>
		
		<?php
	}
}
?>