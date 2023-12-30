<?php if(!defined('VALID_INCL')){header('Location: index.php'); die;} ?>

<script type="text/javascript">
	$(document).ready(function() {
	
	
	
		// client side validation
    	$("#register_user_form").validate({
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
     			}    		
			},
			
			submitHandler: function(form) {			
				var action = "users_add";
				var token = $("#token").val();
				var first_name = $("#first_name").val();
				var last_name = $("#last_name").val();
				var email = $("#email").val();
				var password1 = $("#password1").val();
				var users_id = $("#users_id").val();
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_users_add').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_users_add').hide()",700)},
					type: 'POST',
					url: 'users_edit_ajax.php',
					data: "action=" + action + "&token=" + token + "&first_name=" + first_name + "&last_name=" + last_name + "&email=" + email + "&password1=" + password1 + "&users_id=" + users_id,
					success: function(message){	
						ajaxReply(message,'#ajax_status_users_add');
						$("#activation_message").show();
					}
				});
			}
			
    	})

		jQuery.validator.addMethod("password_complex", function(value, element) { 
			return this.optional(element) || isValidPassword(value); 
			}, "*"
		);
	
	});

</script>

<?php

// include core 
//--------------------------------------------------
require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if(!get_role_CMS('administrator') == 1) {die;}

$pages = new Pages();

//--------------------------------------------------
// check $_GET id
$id = array_key_exists('id', $_GET) ? $_GET['id'] : 0;


?>


<h4 class="admin-heading">Add user</h4>
<form id="register_user_form">
<p>
	<table class="std">				
		<tr class="std">
			<td class="right"><?php echo translate("First name:", "first_name", $languages); ?></td>
			<td><input type="text" id="first_name" name="first_name" style="width:300px;" maxlength="100" /></td>
		</tr>
		<tr>
			<td class="right"><?php echo translate("Last name:", "last_name", $languages); ?></td>
			<td><input type="text" id="last_name" name="last_name" style="width:300px;" maxlength="100" /></td>
		</tr>
		<tr>
			<td class="right"><?php echo translate("Email:", "email", $languages); ?></td>
			<td><input type="text" id="email" name="email" style="width:300px;" maxlength="100" /></td>
		</tr>
		<tr>
			<td class="right"><?php echo translate("Password", "password", $languages); ?></td>
			<td><input onkeyup="update_passwordmeter( this.value );" type="password" id="password1" name="password1" style="width:300px;" maxlength="40" /><br />
			<?php echo translate("Password strength:", "password_strength", $languages); ?> <span id="password_score" class="password_status">&nbsp;</span><span class="password_status">%</span> <span id="password_status" class="password_status">&nbsp;</span> <br />
			<div id="passmeterbg"><div id="passmeter"></div></div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php echo translate("Password policy", "password_policy", $languages); ?></td>
		</tr>
		<tr>
			<td style="padding-top:10px;">&nbsp;</td>
			<td style="padding-top:10px;">
			<span class="toolbar"><button id="btn_users_new">Create new user account</button></span>
			<span id="ajax_spinner_users_add" style="display:none;"></span>
			<span id="ajax_status_users_add" style="display:none;"></span>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><span id="activation_message" style="display:none;">Next step: Send activation message or activate account</span></td>
		</tr>
	</table>


	
</p>
</form>

