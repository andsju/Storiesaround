<?php
require_once 'includes/inc.core.php';
require_once 'includes/inc.session_access.php';

if(!get_role_CMS('contributor') == 1) {
	die;
}

if (isset($_GET['token'])){
	if ($_GET['token'] == $_SESSION['token']) {
	
		if($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			die('User agent fail. Please logout and login again.');
		}
	
		if ($pages_id = filter_input(INPUT_GET, 'pages_id', FILTER_VALIDATE_INT)) { 
	
		$css_files = array(
			CMS_DIR.'/cms/css/normalize.css', 
			CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
			CMS_DIR.'/cms/css/layout.css', 
			CMS_DIR.'/cms/css/pages_edit.css', 
            CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css' 
        );
		
		// add css theme
		$theme = isset($_SESSION['site_theme']) ? $_SESSION['site_theme'] : '';
		if(file_exists(CMS_ABSPATH .'/content/themes/'.$theme.'/style.css')) {
			array_push($css_files, CMS_DIR.'/content/themes/'.$theme.'/style.css');
		}
		
		$page_title = "Set css class";	
		$body_style = "width:100%;margin:20px";

		include_once 'includes/inc.header_minimal.php';

		$js_files = array(
			CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
			CMS_DIR.'/cms/libraries/js/functions.js', 
			CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js'
        );

		echo '<div id="main-wrapper">';

		foreach ( $js_files as $js ):
			echo '<script src="'.$js.'"></script>';
		endforeach; 
		?>

        <div style="width:100%;overflow:auto;height:600px;" id="set_custom_css">
        <?php

        foreach($css_custom as $key => $value) {
            echo '<div class="choose-class space '.$key.'" data-css="'.$key.'" style="float:left; width:100px;height:100px;margin:5px;padding:10px;position:relative" title="'.$key.'">';
				echo '<p>'.$value.'</p>';
				echo '<div class="grid-design '.$key.'"></div>';
            echo '</div>';
        }
        ?>
        </div>

		<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>">
		<input type="hidden" name="pages_id" id="pages_id" value="<?php echo $_GET['pages_id'];?>">

    	<script>
			$(document).ready(function() {		
				var token = $("#token").val();
                $(".choose-class").click(function() {
                    var c = $(this).attr("data-css");
                    var return_class = location.search.split('return=')[1];
                    if (return_class == "true") {
                        window.parent.$(".colorbox_grid_class").prev()[0].value = c;
                        parent.$.colorbox.close();
                    }
                });
			});
		</script>

		<?php
		}
	}
}
?>