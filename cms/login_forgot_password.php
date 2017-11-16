<?php

// include core 
//--------------------------------------------------
require_once 'includes/inc.core.php';

// handle form actions 
$prevent_login_tries = false;



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
$page_title = "Password...";
$body_style = "width:460px;margin:0 auto;";
require 'includes/inc.header_minimal.php';

?>




<?php

if(isset($_SESSION['site_maintenance'])) {
	if($_SESSION['site_maintenance'] == 1) {
		echo '<h4>'.translate("Site is under maintenance. Functionality is limited, please visit later.", "site_maintenance", $languages).'</h4>';
		
		echo '<p>';
			echo nl2br($_SESSION['site_maintenance_message']);
		echo '</p>';;
		die;
	}
}




?>
<form action="login_forgot_password.php" id="password_forgot_form" method="post" class="cms">
<input id="token" type="hidden" value="<?php echo $_SESSION['token']; ?>" />

<table style="margin-left:auto;margin-right:auto;">
	<tr>
		<td width="20%" style="text-align:right;padding:10px">
			<?php echo translate("Email", "email", $languages);?>
		</td>
		<td>
			<input type="text" id="email1" name="email1" title="Enter email" style="width:300px;" maxlength="100" value="<?php if (isset($_POST['email1'])) echo $_POST['email1']; ?>" />
		</td>
	</tr>
	<tr>
		<td width="20%" style="text-align:right;padding:10px">
			<?php echo translate("Confirm email", "email_confirm", $languages);?>
		</td>
		<td>
			<input type="text" id="email2" name="email2" title="Enter email" style="width:300px;" maxlength="100" value="<?php if (isset($_POST['email2'])) echo $_POST['email2']; ?>" />
		</td>
	</tr>
	<tr>
		<td>&nbsp;
		</td>
		<td style="text-align:left;">
			<span class="toolbar"><button id="btn_forgot_password" value="Reset password"><?php echo translate("Reset password", "login_reset_password", $languages);?></button></span>
			
		</td>
	</tr>
	<tr style="line-height: 22px;">
		<td>&nbsp;
		</td>
		<td style="text-align:left;">
			<span id="login_spinner" style='display:none'><img src="css/images/spinner.gif">&nbsp;processing...</span>
			<span id="ajax_result_login"></span>
			<span id="ajax_result_login2"></span>
		</td>
	</tr>
</table>
</form>



<script type="text/javascript">
	$(document).ready(function() {
		
		// client side validation
    	$("#password_forgot_form").validate({
    		rules: {
    			email1: {
					required: true,
     				email: true
				},
				email2: {
					equalTo: "#email1"
				}
    		},
    		messages: {
    			email1: {
    				required: "* <?php echo translate("Required", "required", $languages); ?>"
    			},
				email2: {
					equalTo: "* <?php echo translate("Confirm email", "email_confirm", $languages); ?>"
				}
    		}
    	});
		
		
		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});

		$("input#email1").focus();
	
		$("#btn_forgot_password").click(function(event){
			event.preventDefault();
			var action = "forgot_password";
			var token = $("#token").val();
			var email1 = $("#email1").val();
			var email2 = $("#email2").val();
			
			// check login email
			if (email1 == email2) {
				if (email1.length > 4 && email2.length > 4) {

					$.ajax({						
						beforeSend: function() {$('#login_spinner').show()},
						complete: function(){ loading = setTimeout("$('#login_spinner').hide()",1000)},
						type: 'POST',
						url: 'login_ajax.php',
						data: "action=" + action + "&token=" + token + "&email1=" + email1,
						success: function(message){	
							$("#ajax_result_login").empty().append(message);
						}
					});
				}
			}





			});		
		
	});
	
</script>


<?php 
// load javascript files
foreach ( $js_files as $js ):
	echo '<script type="text/javascript" src="'.$js.'"></script>';
endforeach;
?>


</body>
</html>