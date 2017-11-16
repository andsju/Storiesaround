<?php


// include core
//--------------------------------------------------
require_once '../cms/includes/inc.core.php';

if(!isset($_SESSION['users_id'])) {die;}
require_once '../cms/includes/inc.session_access.php';

$users = new Users();


// css files, loaded in inc.header.php
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css', 
	CMS_DIR.'/cms/libraries/jquery-datatables/style.css'
);

// load javascript files, loads before inc.footer.php
//--------------------------------------------------
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js',
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js',	
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/js/functions.js'
);


// include header, open body tag
//--------------------------------------------------
$meta_keywords = $meta_description = $meta_robots = $meta_additional = $meta_author = null;
$page_title = "Users edit";
$body_style = "width:1190px;";
include_once '../cms/includes/inc.header_minimal.php';


?>




<?php 
// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>


<script>
	
	$(document).ready(function() {

		$('.table_js').dataTable({
			"order": [[ 0, "asc" ]],
			"columnDefs": [{ "width": "400px", "targets": 0 }],
		});
	
		$("#back").click(function(event) {
			event.preventDefault();
			//history.back(1);
			window.location.assign("admin.php?t=groups&tgr=group")			
		});
		
		$( ".toolbar_back button" ).button({
			icons: {
				secondary: "ui-icon-arrowreturnthick-1-w"
			},
			text: true
		});

		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});
		
		$('#users_meta_link').click(function(event){
			$("#users_meta").toggle();		
		});
		
		$("#users_form").validate({
			rules: {
				first_name: "required",
				last_name: "required",
				user_name: {
					required: true,
					minlength: 2
				},
				email: {
					required: true,
					email: true
				},
			},
			messages: {
				first_name: "* required",
				last_name: "* required",
				username: {
					required: "Please enter a username",
					minlength: "Your username must consist of at least 2 characters"
				},
				email: "* required",
			}
		});
		
		// propose username by combining first- and lastname
		$("#user_name").focus(function() {
			var first_name = $("#first_name").val();
			var last_name = $("#last_name").val();
			if(first_name && last_name && !this.value) {
				var username = this.value = (first_name.substr(0,3)) + (last_name.substr(0,3));			
				this.value = username.toLowerCase();
			}
		});	
			
		// validate signup form on keyup and submit
		$("#password_form").validate({
			rules: {
				pass: {
					required: true,
					minlength: 8
				},
				pass_new: {
					required: true,
					password_complex: true
				},
				pass_new_confirm: {
					required: true,
					equalTo: "#pass_new"
				},
			},
			messages: {
				pass: {
					required: "* required",
					minlength: " +8 characters"
				},
				pass_new: {
					required: "* required",
					password_complex: "combine letters, numbers & special characters (8+)",
				},
				pass_new_confirm: {
					required: "* required",
					equalTo: "match new password"
				},
			}
		});
		
		jQuery.validator.addMethod("password_complex", function(value, element) { 
			return this.optional(element) || isValidPassword(value); 
			}, "complex!"
		);
		
		$("#tab_it").tabs({
			
		});
		$("#tab_it").show(); 
		
		$('#btn_user_update').click(function(event){
			event.preventDefault();
			save();
		});

		$('.btn_add_to_group').click(function(event){
			event.preventDefault();
			var action = "update_add_to_group";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var groups_id = this.id;
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_user').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_user').hide()",1000)},			
				type: 'POST',
				url: 'users_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&groups_id=" + groups_id,
				success: function(message){	
					ajaxReply(message,'#ajax_status_user');
					window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#groups';
					location.reload(true);
				},
			});

		});

		$('.btn_remove_from_group').click(function(event){
			event.preventDefault();
			var action = "update_remove_from_group";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var groups_members_id = $(this).val();

			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_user').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_user').hide()",1000)},			
				type: 'POST',
				url: 'users_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&groups_members_id=" + groups_members_id,
				success: function(message){	
					ajaxReply(message,'#ajax_status_user');
					window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#groups';
					location.reload(true);
				},
			});
		});

		$('.btn_user_update_roles').click(function(event){
			event.preventDefault();
			var action = "update_roles";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var role_CMS = $("#role_CMS option:selected").val();
			var role_LMS = $("#role_LMS option:selected").val();
					
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_user').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_user').hide()",1000)},			
				type: 'POST',
				url: 'users_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&role_CMS=" + role_CMS + "&role_LMS=" + role_LMS,
				success: function(message){	
					ajaxReply(message,'#ajax_status_user');
				},
			});

		});

		$('#btn_user_send_activation_code').click(function(event){
			event.preventDefault();
			var action = "send_activation_code";
			var token = $("#token").val();
			var email = $("#email").val();
			var activation_code = $("#activation_code").val();
			var full_name = $("#full_name").val();
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_send_activation_code').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_send_activation_code').hide()",1000)},			
				type: 'POST',
				url: 'users_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&email=" + email + "&activation_code=" + activation_code + "&full_name=" + full_name,
				success: function(message){	
					ajaxReply(message,'#ajax_status_send_activation_code');
				},
			});			
		});
		
		$('#btn_user_activate_account').click(function(event){
			event.preventDefault();
			var action = "activate_account";
			var token = $("#token").val();
			var email = $("#email").val();
			var activation_code = $("#activation_code").val();
			var users_id = $("#users_id").val();
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_activate_account').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_activate_account').hide()",1000)},			
				type: 'POST',
				url: 'users_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&email=" + email + "&users_id=" + users_id + "&activation_code=" + activation_code,
				success: function(message){	
					ajaxReply(message,'#ajax_status_activate_account');
					$("#btn_user_activate_account").attr('disabled', 'disabled');
					$("#btn_user_send_activation_code").attr('disabled', 'disabled');
				},
			});			
		});
		
		$('#btn_change_password').click(function(event){
			event.preventDefault();
			// variables
			var test;
			var action = "change_password";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pass = $("#pass").val();
			var pass_new = test = $("#pass_new").val();
			var pass_new_confirm = $("#pass_new_confirm").val();

			// simple encode of passwords
			var number = getRandomNumber(10,99);
			pass = enc(pass, number);
			pass += number;
			pass_new = enc(pass_new, number);
			pass_new += number;
			pass_new_confirm = enc(pass_new_confirm, number);
			pass_new_confirm += number;
			
			// todo already coded so this validation must be done earlier...
			if (pass.length > 7 && test.length > 7){
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_password').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_password').hide()",1000)},			
					type: 'POST',
					url: 'users_edit_ajax.php',
					data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pass=" + pass + "&pass_new=" + pass_new + "&pass_new_confirm=" + pass_new_confirm,
					success: function(message){
						if(message == 'ok') {
							$("#dialog-message-password").dialog("open");
							$("#ajax_icon").html('<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>');
							message = 'Password changed';
							$("#ajax_status_password").empty().append(message);
							$("#password_form").hide();
						} else {
							$("#dialog-message-password").dialog("open");
							$("#ajax_icon").html('<span class="ui-icon ui-icon-info" style="float: left; margin: 0 7px 50px 0;"></span>');
							$("#ajax_status_password").empty().append(message);
						}
					},
				});
			}
		});

		$('#btn_set_new_password').click(function(event){
			event.preventDefault();
			// variables
			var test;
			var action = "set_new_password";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pass_new = test = $("#pass_new").val();
			var pass_new_confirm = $("#pass_new_confirm").val();

			// simple encode of passwords
			var number = getRandomNumber(10,99);
			pass_new = enc(pass_new, number);
			pass_new += number;
			pass_new_confirm = enc(pass_new_confirm, number);
			pass_new_confirm += number;

			
			if (test.length > 7){
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_password').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_password').hide()",1000)},			
					type: 'POST',
					url: 'users_edit_ajax.php',
					data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pass=" + pass + "&pass_new=" + pass_new + "&pass_new_confirm=" + pass_new_confirm,
					success: function(message){
						if(message == 'ok') {
							$("#dialog-message-password").dialog("open");
							$("#ajax_icon").html('<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>');
							message = 'Password changed';
							$("#ajax_status_password").empty().append(message);
							$("#password_form").hide();
						} else {
							$("#dialog-message-password").dialog("open");
							$("#ajax_icon").html('<span class="ui-icon ui-icon-info" style="float: left; margin: 0 7px 50px 0;"></span>');
							$("#ajax_status_password").empty().append(message);
						}
					},
				});
			}
		});
		
		$("#dialog-message-password").dialog({
			autoOpen: false,
			modal: true,
			buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
				}
			}
		});		
		
		$("#users_account_status").dialog({
			autoOpen: false,
			modal: true
		});		

		$('#btn_user_update_status').click(function(event){
			event.preventDefault();
			$("#users_account_status").dialog("open");
			$("#users_account_status").dialog({
				buttons : {
				"Confirm" : function() {
					$(this).dialog("close");
					var action = "users_account_status";
					var token = $("#token").val();
					var users_id = $("#users_id").val();
					var status = $("#status option:selected").val();
							
					$.ajax({
						beforeSend: function() { loading = $('#ajax_spinner_user').show()},
						complete: function(){ loading = setTimeout("$('#ajax_spinner_user').hide()",1000)},			
						type: 'POST',
						url: 'users_edit_ajax.php',
						data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&status=" + status,
						success: function(message){	
							ajaxReply(message,'#ajax_status_user');
						},
					});	
				},
				"Cancel" : function() {
					$(this).dialog("close");
					}
				}
			});
		});

		$('#btn_users_rights').click(function(event){
			event.preventDefault();
			var action = "update_rights";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var profile_edit = $('input:checkbox[name=profile_edit]').is(':checked') ? 1 : 0;
			var debug = $('input:checkbox[name=debug]').is(':checked') ? 1 : 0;
					
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_profile_edit').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_profile_edit').hide()",1000)},			
				type: 'POST',
				url: 'users_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&profile_edit=" + profile_edit + "&debug=" + debug,
				success: function(message){	
					ajaxReply(message,'#ajax_status_user');
				},
			});

		});
		
	})
</script>


<script>

	autosave();
	function autosave() {
		setInterval("save()", <?php if(isset($_SESSION['site_autosave'])) { echo $_SESSION['site_autosave']; } else { echo '15000'; } ?>);
	}		
	
	// autosave
	function save() {
		// variables
		var action = "update";
		var token = $("#token").val();
		var users_id = $("#users_id").val();
		var first_name = $("#first_name").val();
		var last_name = $("#last_name").val();
		var email = $("#email").val();
		var user_name = $("#user_name").val();
		var language = $("#session_language option:selected").val();
		var phone = $("#phone").val();
		var mobile = $("#mobile").val();
		var postal = $("#postal").val();
		var city = $("#city").val();
		var country = $("#country").val();
		var address = $("#address").val();
		var comment = $("#comment").val();
		
		if (first_name.length > 0 && last_name.length > 0){
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_user').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_user').hide()",1000)},			
				type: 'POST',
				url: 'users_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&first_name=" + first_name + "&last_name=" + last_name + 
				"&email=" + email + "&user_name=" + user_name + "&language=" + language + 
				"&phone=" + phone + "&mobile=" + mobile + "&postal=" + postal + "&city=" + city +
				"&country=" + country + "&address=" + address + "&comment=" + comment,
				success: function(message){	
					ajaxReply(message,'#ajax_status_user');
				},
			});
		}
	}
</script>




<?php

// if $_GET method is used for editing
// get saved data, if not just deleted...
if ($users_id = filter_input(INPUT_GET, 'users_id', FILTER_VALIDATE_INT)) { 

	$result = $users->getUsersSettings($users_id);

	if($result) {
		$first_name = $result['first_name'];
		$last_name = $result['last_name'];
		$email = $result['email'];
		$user_name = $result['user_name'];
		$role_CMS = $result['role_CMS'];
		$role_LMS = $result['role_LMS'];
		$default_language = $result['language'];
		$phone = $result['phone'];
		$mobile = $result['mobile'];
		$postal = $result['postal'];
		$address = $result['address'];
		$city = $result['city'];
		$country = $result['country'];
		$comment = $result['comment'];
		$activation_code = $result['activation_code'];
		$utc_lastvisit = $result['utc_lastvisit'];
		$utc_created = $result['utc_created'];
		$utc_modified = $result['utc_modified'];
		$login_count = $result['login_count'];	
		$profile_edit = $result['profile_edit'];
		$debug = $result['debug'];
		
	} else {
		die;
	}
	
}


if($_SESSION['users_id'] == $users_id && $profile_edit == false) { die('Account has no rights to edit user settings.');}

?>

<h4 class="admin-heading">Edit user</h4>

<form id="users_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?users_id='. $users_id; ?>">

<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
<table width="100%">
	<tr>
		<td>
		<?php 
		if(isset($first_name)){
			echo $first_name .' '. $last_name .', '. $email;
			echo '<input type="hidden" id="full_name" value="'.$first_name .' '. $last_name .'" />';
		}
		
		?>
		</td>
		<td align="right">
		<input type="hidden" id="users_id" name="users_id" value="<?php echo $users_id; ?>">
		<span id="ajax_status_user" style="display:none;"></span>
		<span id="ajax_spinner_user" style='display:none'><img src="css/images/spinner.gif"></span>
		<span class="toolbar"><button id="btn_user_update">Save</button></span>
		</td>
	</tr>
</table>



<div id="tab_it" style="display:none;">
	<ul>
		<li><a href="#profile">Profile</a></li>
		<li><a href="#password">Password</a></li>
		<li><a href="#settings">Settings</a></li>
		<li><a href="#groups">Groups</a></li>
		<li><a href="#roles">Roles</a></li>
		<li><a href="#rights">Rights</a></li>
		<li><a href="#account">Account</a></li>
		
	</ul>


	<div id="profile">

		<div class="admin-panel">

			<table width="100%">
				<tr>
					<td width="50%">
					<label for="first_name">First name: </label>
					<br />
					<input type="text" name="first_name" id="first_name" title="Enter first_name" class="text required" minlength="2" maxlength="50" size="50" maxlength="50" value="<?php if(isset($first_name)){echo $first_name;}?>" />
					</td>
					<td>
					<label for="last_name">Last name: </label>
					<br />
					<input type="text" name="last_name" id="last_name" title="Enter last_name" class="text required" minlength="2" maxlength="50" size="50" maxlength="50" value="<?php if(isset($last_name)){echo $last_name;}?>" />
					</td>
				</tr>
				<tr>
					<td>
					<label for="email">Email: </label>
					<br />
					<input type="text" name="email" id="email" title="Enter email" class="required email" size="50" maxlength="50" value="<?php if(isset($email)){echo $email;}?>" />
					</td>
					<td>
					<label for="username">Username: </label>
					<br />
					<input type="text" name="user_name" id="user_name" title="Enter user_name" size="50" maxlength="50" value="<?php if(isset($user_name)){echo $user_name;}?>" />			
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding:20px 0 20px 5px;">
						<a href="#" id="users_meta_link">show more settings &raquo;</a>
					</td>
				</tr>
			</table>
			
			
			<div id="users_meta" style="display:none;">
				<table width="100%">
					<tr>
						<td width="50%">
						<label for="phone">Phone: </label>
						<br />
						<input type="text" name="phone" id="phone" title="Enter phone" size="50" maxlength="50" value="<?php if(isset($phone)){echo $phone;}?>" />
						</td>
						<td>
						<label for="mobile">Mobile: </label>
						<br />
						<input type="text" name="mobile" id="mobile" title="Enter mobile" size="50" maxlength="50" value="<?php if(isset($mobile)){echo $mobile;}?>" />			
						</td>
					</tr>
					<tr>
						<td>
							<p>
								<label for="postal">Postal: </label>
								<br />
								<input type="text" name="postal" id="postal" title="Enter postal" size="10" maxlength="10" value="<?php if(isset($postal)){echo $postal;}?>" />
							</p>
							<p>
								<label for="city">City: </label>
								<br />
								<input type="text" name="city" id="city" title="Enter city" size="50" maxlength="50" value="<?php if(isset($city)){echo $city;}?>" />			
							</p>
							<p>
								<label for="country">Country: </label>
								<br />
								<input type="text" name="country" id="country" title="Enter country" size="50" maxlength="50" value="<?php if(isset($country)){echo $country;}?>" />			
							</p>
							<p>
								<label for="city">Address: </label>
								<br />
								<input type="text" name="address" id="address" title="Enter address" size="50" maxlength="50" value="<?php if(isset($address)){echo $address;}?>" />			
							</p>
						</td>
						<td>
							<p>
								<label for="city">Comment: </label>
								<br />
								<textarea cols="50" rows="10" id="comment">
								<?php if(isset($comment)){echo $comment;}?>
								</textarea>
							</p>
						</td>
					</tr>
				</table>	
				
			</div>
			
		</div>
		
	</div>

	
	
	
	

	<div id="settings">
		
		<div class="admin-panel">

			<?php		
			$folder = 'languages';
			$selects = $selected = null;
			//open directory 
			if ($handle = opendir($folder)) {
				$selects .= '<select id="session_language">';
				/* loop over widget directory */
				while (false !== ($file = readdir($handle))) {

					// list all files in the current directory and strip out . and ..
					if ($file != "." && $file != "..") {

						// get filename from php file >> skip extension .php 
						$language = substr($file, 0, -4);
						
						$selects .= '<option value="'.$language.'"';
						if(isset($_SESSION['language'])) {
							$selected = ($_SESSION['language'] == $language) ? ' selected=selected' : null;
						}
						
						$selects .= $selected;
						$selects .= '>'.$language.'</option>';
					}
				}
				$selects .= '</select>';
			}
			
			echo '<p>';
				echo $selects .'&nbsp;language';
			echo '</p>';
			?>
		
		</div>
		
	</div>


	<div id="groups">
		
		<div class="admin-panel">

			<?php


			$groups = new Groups;
					
			$row = $groups->getUsersGroupsMembership($users_id);
			
			echo '<div style="width:50%;float:left;">';
			
			echo '<h3>Group membership</h3>';
			echo '<table id="membership" class="paging">';		
				echo '<thead>';		
				echo '<tr>';
					echo '<th class="paging">'. $first_name .' '. $last_name .' is member in following groups';
					echo '</th>';
					if(get_role_CMS('administrator') == 1) {
						echo '<th class="paging">action';
						echo '</th>';
					}
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				// css style odd-even rows
				$class = 'even';			
				foreach($row as $r) {
					?>
					<input type="hidden" name="r_id[]" id="r_id[]" value="<?php echo $r['groups_members_id']; ?>" />
					<?php
					// switch odd-even
						$class = ($class=='even') ? 'odd' : 'even';				
						echo '<tr id="row-" class="paging_'. $class .'">';
							echo '<td class="paging">';
							echo $r['title'];
							echo '</td>';
							if(get_role_CMS('administrator') == 1) {
								echo '<td class="paging">';							
								?>
									<span class="toolbar"><button class="btn_remove_from_group" value="<?php echo $r['groups_members_id']; ?>">remove</button></span>
								<?php
								echo '&nbsp;</td>';
							}
					echo '</tr>';
				}

			echo '</tbody></table>';
			
			echo '</div>';
			echo '<div style="width:50%;float:right;">';
			
			
			$rows = $groups->getGroupsAll();
			
			echo '<h3 class="heading">Groups</h3>';
			$html = '';
			if($rows) {
				$html = '<table class="table_js lightgrey">';
					$html .= '<thead>';
						$html .= '<tr>';
							$html .= '<th>Group</th>';
							$html .= '<th style="text-align:center;">Membership</th>';
						$html .= '</tr>';
					$html .= '</thead>';
					$html .= '<tbody>';
						foreach($rows as $row) {
							$html .= '<tr class="">';
								$html .= '<td>'.$row['title'].'</td>';
								$html .= '<td style="text-align:center;"><span class="toolbar"><button class="btn_add_to_group" id="'.$row['groups_id'].'" style="font-size:0.8em;">Add to group</button></span></td>';
							$html .= '</tr>';
						}
					$html .= '</tbody>';
				$html .= '</table>';
			}
			echo $html;
			
			echo '</div>';
			
			
			echo '<div style="clear:both;"></div>';

			?>
			
		</div>
		
	</div>


	
	

	<div id="roles">
	
		<div class="admin-panel">
		
			<table width="100%">
				<?php if(get_role_CMS('administrator') == 1) { ?>
					<tr>
					
						<td width="25%">
						Content management: 
						<p>
							<select name="role_CMS" id="role_CMS" title="Set CMS role">
								<option value="0" <?php if($result['role_CMS'] == 0) {echo 'selected';}?>>- none -</option>
								<option value="1" <?php if($result['role_CMS'] == 1) {echo 'selected';}?>>User</option>
								<option value="2" <?php if($result['role_CMS'] == 2) {echo 'selected';}?>>Contributor</option>
								<option value="3" <?php if($result['role_CMS'] == 3) {echo 'selected';}?>>Author</option>
								<option value="4" <?php if($result['role_CMS'] == 4) {echo 'selected';}?>>Editor</option>
								<option value="5" <?php if($result['role_CMS'] == 5) {echo 'selected';}?>>Administrator</option>
								<option value="6" <?php if($result['role_CMS'] == 6) {echo 'selected';}?>>Superadministrator</option>
							</select>
						</p>
						</td>
						<td>&nbsp;
						</td>
					</tr>
					<tr>
						<td>
						Learning management: 
						<p>
							<select name="role_LMS" id="role_LMS" title="Set LMS role">
								<option value="0" <?php if($result['role_LMS'] == 0) {echo 'selected';}?>>- none -</option>
								<option value="1" <?php if($result['role_LMS'] == 1) {echo 'selected';}?>>Student</option>
								<option value="2" <?php if($result['role_LMS'] == 2) {echo 'selected';}?>>Tutor</option>
								<option value="3" <?php if($result['role_LMS'] == 3) {echo 'selected';}?>>Teacher</option>
								<option value="4" <?php if($result['role_LMS'] == 4) {echo 'selected';}?>>Administrator</option>
							</select>
						</p>
						</td>
						<td>&nbsp;
						</td>
					</tr>
				
				<?php 
				
				} else { 
				
					$a_CMS_roles = array(0 => "none", 1 => "User", 2 => "Contributor", 3 => "Author", 4 => "Editor", 5 => "Administrator", 5 => "Superadministrator");
					$a_LMS_roles = array(0 => "none", 1 => "Student", 2 => "Tutor", 3 => "Teacher", 4 => "Administrator");
					$a_status = array(0 => "none", 1 => "inactive", 2 => "active");
					
					echo '<tr>';
						echo '<td>';
							echo '<i>'.$a_CMS_roles[$result['role_CMS']].'</i>';
						echo '</td>';
					echo '</tr>';
					echo '</tr>';
						echo '<td>';
							echo '<i>'.$a_LMS_roles[$result['role_LMS']].'</i>';
						echo '</td>';
					echo '</tr>';
					
				} 
				?>
				

			</table>
			
			<?php 
			if(get_role_CMS('administrator') == 1) { 
				echo '<div><span class="toolbar"><button class="btn_user_update_roles">Save roles</button></span></div>';
			}
			?>
			
		</div>
	
	
	</div>




	<div id="account">

		
		<?php 
		if(get_role_CMS('administrator') == 1) { 
			
			echo '<div class="admin-panel">';
			?>
				<table style="width:100%;">
					<tr>
						<td>
						User staus: 
						<select name="status" id="status" title="Set user status">
							<option value="0" <?php if($result['status'] == 0) {echo 'selected';}?>>- none (deleted) -</option>
							<option value="1" <?php if($result['status'] == 1) {echo 'selected';}?>>inactive</option>
							<option value="2" <?php if($result['status'] == 2) {echo 'selected';}?>>active</option>
						</select>
						</td>
						<td style="float:right;">
						<span class="toolbar"><button id="btn_user_update_status">Set status</button></span>
						</td>
					</tr>
				</table>
			<?php
			echo '</div>';
			echo '<div class="admin-panel">';
				if($result['activation_code']) {
				echo '<div>';
					echo '<table style="width:100%;"><tr><td>';
						echo 'Account is not activated ';
						echo '</td><td style="float:right;">';
			
						echo '<span class="toolbar"><button id="btn_user_send_activation_code">Send activation code to user</button></span>';
						echo '<span id="ajax_spinner_send_activation_code" style="display:none"><img src="css/images/spinner.gif"></span>';
						echo '<span id="ajax_status_send_activation_code" style="display:none"></span>';
						echo '<span class="toolbar"><button id="btn_user_activate_account">Activate account now</button></span>';
						echo '<span id="ajax_spinner_activate_account" style="display:none"><img src="css/images/spinner.gif"></span>';
						echo '<span id="ajax_status_activate_account" style="display:none"></span>';
					echo '</td></tr></table>';
				echo '</div>';
				echo '<input type="hidden" id="activation_code" value="'.$activation_code.'" />';
				} else {
					echo 'Account is activated';
				}
			echo '</div>';
			echo '<div class="admin-panel">';
				echo '<p>Account created: <span class="pre">'. $utc_created .'</span></p>';
				echo '<p>Account modified: <span class="pre">'. $utc_modified .'</span></p>';
				echo '<p>Last visit: <span class="pre">'. $utc_lastvisit .'</span></p>';
				echo '<p>Login count: <span class="pre">'. $login_count .'</span></p>';
			echo '</div>';
		}
		?>
	
		<div id="users_status" style="display:none;"></div>

		<div id="users_account_status" title="Confirmation required">
		  Change user status?
		</div>
	

	</div>
	
	
	
	</form>

	<div id="password">

		<div class="admin-panel">
			
			<form id="password_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?users_id='. $users_id; ?>">

			<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />			
			


			<table style="margin-top:10px;">
				<tr>
					<td colspan="2">
					<label for="pass">Old password: </label>
					<br />
					<input type="password" name="pass" id="pass" title="Enter old password" size="50" maxlength="25" value="<?php ?>" />
					</td>
				</tr>
				<tr>
					<td>
					<label for="pass_new">New password: </label>
					<br />
					<input onkeyup="update_passwordmeter( this.value );" type="password" name="pass_new" id="pass_new" title="Enter new password" size="50" maxlength="25" value="<?php ?>" />
					</td>
					<td>
					<table>
						<tr>
							<td>
								<div id="passmeterbg"><div id="passmeter"></div></div>
								</td>
							<td>
								<span id="password_score" class="password_status">&nbsp;</span><span class="password_status">%</span> <span id="password_status" class="password_status">&nbsp;</span>
							</td>
						</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<label for="pass_new_confirm">Confirm new password: </label>
					<br />
					<input type="password" name="pass_new_confirm" id="pass_new_confirm" title="Confirm new password" size="50" maxlength="25" value="<?php ?>" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<span class="toolbar"><button id="btn_change_password">Change password</button></span>
					<span id="ajax_spinner_password" style='display:none'><img src="css/images/spinner.gif"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<p>
					<?php 
					if(get_role_CMS('administrator') == 1) { 
						echo 'You have logged in as administrator: <span class="toolbar"><button id="btn_set_new_password">Set new password</button></span> <i>skip old password match...</i>';
					}
					?>
					</p>			
					</td>
				</tr>
			</table>
			</form>	
			
			<div id="dialog-message-password" title="Password">
			  <p>
				<span id="ajax_icon"></span>
				<span id="ajax_status_password"></span>
			  </p>
			</div>		
			
		</div>
		
		
	</div>

	
	<div id="rights">

	<?php if($_SESSION['role_CMS'] >= 5) { ?>

		
		<div class="admin-panel">

			<p>
				<input type="checkbox" name="profile_edit" id="profile_edit" value="1" <?php if($profile_edit == 1) {echo 'checked';}?>>
				enable (default) / disable logged in user to edit they're own profile | password  (Applies not to administrators)
			</p>
			
			<p>
				<input type="checkbox" name="debug" id="debug" value="1" <?php if($debug == 1) {echo 'checked';}?>>
				enable / disable (default) logged in user (administrator) to show debug info
			</p>

			<p>
				<span class="toolbar"><button id="btn_users_rights">Save</button></span>
				<span id="ajax_spinner_profile_edit" style='display:none'><img src="css/images/spinner.gif"></span>
				<span id="ajax_status_profile_edit"></span>
			</p>
		</div>
			
		
		
	<?php } ?>
	
	</div>

</div>







<?php

/*** include footer ***/
//--------------------------------------------------
include_once '../cms/includes/inc.footer_cms.php';

?>

</body>
</html>