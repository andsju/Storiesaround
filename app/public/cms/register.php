<?php

// include core
//--------------------------------------------------
require_once 'includes/inc.core.php';

$z = new Site();
$site = $z->getSite();

if($site['site_account_registration'] == 0) { die;}


// css files
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css'
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
	CMS_DIR.'/cms/libraries/js/functions.js'
);

// include header
$meta_author = $meta_keywords = $meta_description = $meta_robots = null;
$page_title = "Register";
$body_style = "max-width:600px;";
include_once 'includes/inc.header_minimal.php';

echo '<div id="main-wrapper" class="clearfix" style="width:100%;">';
echo '<h2 class="admin-heading">' .$_SESSION['site_name'].' - '.translate("Register", "site_register", $languages). ' </h2>';

?>


<form action="register.php" id="register_form" method="post" class="cms">
	<fieldset>	
	<?php
	
	if(isset($_SESSION['site_account_registration'])) {
		if(!$_SESSION['site_account_registration']) {
			echo translate("User registration temporarily closed", "site_registration_closed", $languages);
			die ;
		}
	}
	?>
		
	<table style="border-spacing:0px;width:100%;">		
		<tr>
			<td>
			<?php echo translate("First name:", "first_name", $languages); ?><br />
			<input title="<?php echo translate("First name:", "first_name", $languages); ?>" type="text" id="first_name" name="first_name" style="width:300px;" maxlength="100" value="<?php if (isset($trimmed['first_name'])) echo $trimmed['first_name'];?>" /></td>
		</tr>
		<tr>
			<td>
			<?php echo translate("Last name:", "last_name", $languages); ?><br />
			<input title="<?php echo translate("Last name:", "last_name", $languages); ?>" type="text" id="last_name" name="last_name" style="width:300px;" maxlength="100" value="<?php if (isset($trimmed['last_name'])) echo $trimmed['last_name'];?>" /></td>
		</tr>
		<tr>
			<td>
			<?php echo translate("Email:", "email", $languages); ?><br />
			<input title="<?php echo translate("Email:", "email", $languages); ?>" type="text" id="email" name="email" style="width:300px;" maxlength="100" value="<?php if (isset($trimmed['email'])) echo $trimmed['email'];?>" /></td>
		</tr>
		<tr>
			<td><div style="width:70%;margin:10px 0;"><?php echo translate("Password policy", "password_policy", $languages); ?></div></td>
		</tr>
		<tr>
			<td>
			<?php echo translate("Password", "password", $languages); ?><br />
			<input title="<?php echo translate("Password", "password", $languages); ?>" onkeyup="update_passwordmeter( this.value );" type="password" id="password1" name="password1" style="width:300px;" maxlength="40" /><br />

			<?php echo translate("Password strength:", "password_strength", $languages); ?> <span id="password_score" class="password_status">&nbsp;</span><span class="password_status">%</span>  <br />
			<div id="passmeterbg" style="float:left;margin:5px 0 7px 0;"><div id="passmeter"></div></div><div style="float:left;"><span id="password_status" class="password_status"></span></div>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo translate("Confirm password:", "password_confirm", $languages); ?><br />
			<input title="<?php echo translate("Confirm password:", "password_confirm", $languages); ?>" type="password" id="password2" name="password2" style="width:300px;" maxlength="40" /></td>
		</tr>
		<tr>
			<td><input type="checkbox" id="agree" name="agree"> <?php echo translate("I agree", "agree_terms_of_use", $languages); ?></td>
		</tr>
		<tr>
			<td><span class="toolbar"><button type="submit" id="btn_register" name="btn_register"><?php echo translate("Create account", "site_register", $languages); ?></button></span></td>
		</tr>
	</table>
	
	</fieldset>
	
	<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
	
</form>

<div id="ajax_status_register"></div>

<script>
	$(document).ready(function() {
	
		// client side validation
    	$("#register_form").validate({
    		rules: {
    			email: {
					required: true,
     				email: true
     			},
    			first_name: {
					required: true
     			},
    			last_name: {
					required: true
     			},
    			password1: {
					required: true,
					password_complex: true
     			},
				password2: {
					equalTo: "#password1"
				},
				agree: {
					required: true
				}
				
    		},
    		messages: {
    			email: {
    				required: "* <?php echo translate("Required", "required", $languages); ?>"
    			},
    			first_name: {
					required: "* <?php echo translate("Required", "required", $languages); ?>"
     			},
    			last_name: {
					required: "* <?php echo translate("Required", "required", $languages); ?>"
     			},
    			password1: {
					required: "* <?php echo translate("Required", "required", $languages); ?>"
     			},
				password2: {
					equalTo: "* <?php echo translate("Confirm password", "password_confirm", $languages); ?>"
				},
				agree: {
					required: "* <?php echo translate("Required", "required", $languages); ?>"
				}
    		}
    	})

		jQuery.validator.addMethod("password_complex", function(value, element) { 
			return this.optional(element) || isValidPassword(value); 
			}, "*"
		);
		
		$( ".toolbar button" ).button({
		});

		$( "[title]" ).tooltip({
			position: {
				my: "right top",
				at: "right+25 top+25"
			}
		});
		
		$('#btn_register').click(function(event){
			event.preventDefault();
			var action = "register_user";
			var token = $("#token").val();
			var first_name = $("#first_name").val();
			var last_name = $("#last_name").val();
			var user_name = $("#user_name").val();
			var email = $("#email").val();
			var password = $("#password1").val();
						
			// simple encode of passwords
			var number = getRandomNumber(10,99);
			password = enc(password, number);
			password += number;
			
			if($("#register_form").valid()) {
				$.ajax({
					beforeSend: function() { loading = $('.ajax_spinner_register').show()},
					complete: function(){ loading = setTimeout("$('.ajax_spinner_register').hide()",700)},
					type: 'POST',
					url: 'register_ajax.php',
					data: { 
						action: action, token: token, 						
						first_name: first_name, last_name: last_name, user_name: user_name, email: email, password: password
					},
					success: function(reply){	
						var jsonified = JSON.parse(reply);
						var result = jsonified.result;
						var message = jsonified.message;
						var css_class = result == "success" ? "reply_success" : "reply_fail";
						
						$("#ajax_status_register").empty().html("<span class="+css_class+">" + message + "</span>").show();
						if (result == "success") {
							$("#register_form").hide();
						}
					}
				});
			}
				
		});		
		
	});
</script>


</div>


<div class="footer-wrapper"></div>
<img style="width:120px;float:right;" alt="Storiesaround logotype" src="css/images/storiesaround_logotype_black.png">

<?php 
// load javascript files
foreach ( $js_files as $js ):
	echo '<script src="'.$js.'"></script>';
endforeach; 
?>

</body>
</html>