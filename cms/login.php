<?php

// include core 
//--------------------------------------------------
require_once 'includes/inc.core.php';


// handle form actions 
//--------------------------------------------------
// form variables
$prevent_login_tries = false;


// page title 
//--------------------------------------------------

// css files
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css'
	//CMS_DIR.'/cms/libraries/jquery-datatables/style.css
);

// add css theme
$theme = isset($_SESSION['site_theme']) ? $_SESSION['site_theme'] : '';
if(file_exists(CMS_ABSPATH .'/content/themes/'.$theme.'/style.css')) {
	array_push($css_files, CMS_DIR.'/content/themes/'.$theme.'/style.css');
}


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
	CMS_DIR.'/cms/libraries/js/functions.js', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js'
);

// include header
$page_title = translate("Login", "site_login", $languages) .' - '. $_SESSION['site_name'];
$body_style = "width:320px;margin:0 auto;font-size:90%";
include_once 'includes/inc.header_minimal.php';

?>



<?php
if(isset($_SESSION['site_maintenance'])) {
	if($_SESSION['site_maintenance'] == 1) {
		echo '<div style="margin:10px;">';
			echo '<h5>'.translate("Site is under maintenance. Functionality is limited, please visit later", "site_maintenance", $languages).'</h5>';
		echo '</div>';
	}
}

?>
<div id="login_form" style="display:none;">
<form>
<table>
	<tr>
		<td>
			<label><?php echo translate("Användare", "user", $languages);?></label><br />
			<input type="text" id="email_username" title="<?php echo translate("Enter email or username", "login_username", $languages);?>" maxlength="100" value="<?php if (isset($_POST['email_username'])) echo $_POST['email_username']; ?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label><?php echo translate("Password", "password", $languages);?></label><br />
			<input type="password" id="passw" name="passw" maxlength="100" value="<?php if (isset($_POST['passw'])) echo $_POST['passw']; ?>"/>
		</td>
	</tr>
	<tr>
		<td>
			<span class="toolbar"><button id="btn_login" value="login"><?php echo translate("Login", "site_login", $languages);?></button></span>
		</td>
	</tr>
	<tr style="line-height: 25px;">
		<td style="text-align:left;">
			<span id="login_spinner" style='display:none'><img src="css/images/spinner.gif">...</span>
			<span id="ajax_result_login"></span>
			<span id="ajax_result_login2"></span>
		</td>
	</tr>
	<tr>
		<td style="text-align:left;padding:0px;">
			<a href="register.php" target="_blank" title="<?php echo translate("Sign up", "site_sign_up_long", $languages);?>"><?php echo translate("Sign up", "site_sign_up", $languages);?></a> | <a href="login_forgot_password.php" title="<?php echo translate("Forgot password", "site_password_forgot_long", $languages);?>"><?php echo translate("Forgot password", "site_password_forgot", $languages);?></a>
			<span class="cms-icon-small" title="Storiesaround CMS"></span>
		</td>
	</tr>
</table>
<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token']; ?>" />
</form>
</div>


<?php 
// load javascript files
foreach ( $js_files as $js ):
	echo '<script src="'.$js.'"></script>';
endforeach;
?>

<script>

	$(document).ready(function() {
		/*
		console.log(window.location.hostname);
		if (window.location.protocol != "https:") {
			if (window.location.hostname == "localhost") {
				window.location.href = "http:" + window.location.href.substring(window.location.protocol.length);
			}
			
		}
		*/
		
		$("input#email_username").focus();
		
		$('#login_form').css({
			'margin-top' : '20%'
		});
		$('#login_form').show();
		
		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});		
	
		$("#btn_login").click(function(event){
			event.preventDefault();
			var action = "login";
			var token = $("#token").val();
			var email_username = $("#email_username").val();
			var passw = $("#passw").val();
			$("#ajax_result_login").empty();
						
			// simple encode of password
			var number = getRandomNumber(10,99);
			passw = enc(passw, number);
			passw += number;
			
			// check login
			if (email_username.length > 4 && passw.length > 7){
				$.ajax({
					beforeSend: function() {$('#login_spinner').show()},
					complete: function(){ loading = setTimeout("$('#login_spinner').hide()",1000)},
					type: 'POST',
					url: 'login_ajax.php',
					data: { 
						action: action, token: token, email_username: email_username, passw: passw
					},					
					success: function(message){	
						if (message == 'ok') {  						
							var login_response = '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';					
							$("#ajax_result_login").empty().append(login_response);							
							window.parent.jQuery.colorbox.close();
							window.location.href = "index.php";
						} else {
							$("#ajax_result_login").empty().append(message);
						}
					}
				});
			}
		});
		
	});
	
</script>

</body>
</html>