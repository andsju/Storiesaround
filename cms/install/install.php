<?php

/*** enable sessions ***/
//--------------------------------------------------
session_start();

/*** prevent direct access to include files ***/
//--------------------------------------------------
define('VALID_INCL', true);
//define('LIVE', false);

/*** generate an anti-CSRF (Cross-Site Request Forgery) token ***/
//--------------------------------------------------
if (!isset($_SESSION['token'])) {
	$_SESSION['token'] = sha1(uniqid(mt_rand(), TRUE));
}

// install is only allowed when site is not set
if(isset($_SESSION['site_id'])) {
	header('Location: ../cms/index.php');
	exit;
}

// css files
//--------------------------------------------------
$css_files = array(
	'../css/layout.css', 
	'../libraries/jquery-ui/jquery-ui.css' );


// javascript files
//--------------------------------------------------
$js_files = array(
	'../libraries/jquery-ui/jquery-ui.custom.min.js', 
	'../libraries/js/functions.js', 
	'../libraries/jquery-plugin-validation/jquery.validate.js' );


?>
<!DOCTYPE html>
<html lang="en">

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Install Storiesaround</title>

	<?php 
	//load css files
	foreach ( $css_files as $css ):
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen,projection" href="'.$css.'" />';
	endforeach; 
	?>

	<script src="../libraries/jquery/jquery.min.js"></script>
	
</head>


<body style="width:1024px;background:#FAFAFA;margin:0 auto;position:relative;font-size:0.9em;">

<?php

echo '<h1>Storiesaround CMS - installation</h1><img src="../css/images/storiesaround_logotype_black.png" alt="Storiesaround logotype" style="right:20px;top:50px; width:120px;position:absolute;"/>';

$error = '<h3>Discovered an error during installation. Please check the following!</h3>';

$db_c =  '../../sys/inc.db.php';

if(!is_file($db_c)) {
	
	$error_message = '<p>Missing important database connection file: <div class="code code-highlight">[Storiesaround CMS root] /sys/inc.db.php</div></p>
	This file must exists, look for a file called "inc.db-sample.php" in the same folder, rename this file to "inc.db.php" (or better - make a copy and replace that filename!)';
	die($error . $error_message);
	
}

include '../../sys/inc.db.php';
include '../includes/inc.functions.php';
include '../languages/english.php';
include '../includes/inc.version.php';
?>



<script>

	$(document).ready(function() {
		
		$("#tabs_edit").tabs({
			disabled: [  2, 3, 4 ]
		});
		
		$("#tabs_edit").show(); 
	
		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});
	
		$("#btn_site_go").click (function() {

			$("#tabs_edit").tabs({
				disabled: [ 2, 3, 4 ],
				active: 1
			});
		
		});

		$("#btn_site_requirements").click (function() {

			$("#tabs_edit").tabs({
				disabled: [ 3, 4 ],
				active: 2
			});
		
		});

		$(".btn_site_db_connect").click (function() {
			window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#connect';
			location.reload(true);
			
			$("#tabs_edit").tabs({
				disabled: [ 3, 4 ],
				active: 2
			});
		
		});
		
		$("#btn_site_db_confirm").click (function() {

			$("#tabs_edit").tabs({
				disabled: [ 4 ],
				active: 3
			});
		
		});

		$("#btn_site_db_setup").click (function(event) {
	
			event.preventDefault();
			var action = "db_setup";
			var token = $("#token").val();
			$.ajax({
				beforeSend: function() { loading = $('.ajax_spinner_setup').show()},
				complete: function(){ loading = setTimeout("$('.ajax_spinner_setup').hide()",700)},
				type: 'POST',
				url: 'install_ajax.php',
				data: { 
					action: action, token: token, 
				},
				success: function(message){	
					
					$("#tabs_edit").tabs({
						disabled: [  ],
						active: 4
					});
				
				},
			});
		
		});

		// client side validation
    	$("#site_form").validate({
    		rules: {
    			site_name: {
					required: true
     			},
    			site_domain_url: {
					required: true
     			},
    			site_domain: {
					required: true
     			},
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
    			user_name: {
					required: true,
					maxlength:25
     			},
    			password1: {
					required: true,
					password_complex: true
     			},
				password2: {
					equalTo: "#password1"
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
    			user_name: {
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
				},
				leave_message: {
					maxlength: "* <?php echo translate("Max 100 characters", "leave_message_maxlength", $languages); ?>"
				}
    		}
    	});
		
		jQuery.validator.addMethod("password_complex", function(value, element) { 
				return this.optional(element) || isValidPassword(value); 
			}, 
			"complex!"
		);
		
		
		$('#btn_site_save').click(function(event){
			event.preventDefault();
			var action = "site_install";
			var token = $("#token").val();
			var site_name = $("#site_name").val();
			var site_domain_url = $("#site_domain_url").val();
			var site_domain = $("#site_domain").val();
			var first_name = $("#first_name").val();
			var last_name = $("#last_name").val();
			var user_name = $("#user_name").val();
			var email = $("#email").val();
			var password = $("#password1").val();
			if($("#site_form").valid()) {
				$.ajax({
					beforeSend: function() { loading = $('.ajax_spinner_setup').show()},
					complete: function(){ loading = setTimeout("$('.ajax_spinner_setup').hide()",700)},
					type: 'POST',
					url: 'install_ajax.php',
					data: "action=" + action + "&token=" + token +
					"&site_name="+site_name+
					"&site_domain_url="+site_domain_url+
					"&site_domain="+site_domain+
					"&first_name="+first_name+
					"&last_name="+last_name+
					"&user_name="+user_name+
					"&email="+email+
					"&password="+password,
					data: { 
						action: action, token: token, 
						site_name: site_name, site_domain_url: site_domain_url, site_domain: site_domain,
						first_name: first_name, last_name: last_name, user_name: user_name, email: email, password: password
					},
					success: function(message){	
						// show ajax reply in span id	
						$("#ajax_status_site_install").empty().append(message).show();					
						$("#btn_site_save").hide();
						$("#site_form").hide();
						
					},
				});
			}
			
		});		
	
	});
</script>


<?php	

// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>



<div id="wrapper-site">

	<div id="wrapper-page" style="float:left;width:100%;">

		<div id="wrapper-main">
		
		
		<div id="tabs_edit" style="display:none;">
			
			<ul>
				<li><a href="#info">&nbsp;</a></li>
				<li><a href="#requirements">Step 1 - system</a></li>
				<li><a href="#connect">Step 2 - connect to database</a></li>
				<li><a href="#setup_db">Step 3 - setup database</a></li>
				<li><a href="#setup_site">Step 4  - setup site</a></li>
			</ul>
			

			<div id="info">
				<div style="width:100%; text-align:center;">
				<h3>Ready to install Storiesaround CMS?</h3>
				<p>
				<span class="toolbar"><button id="btn_site_go">Go!</button></span>
				</p>
				</div>
			</div>
			
			<div id="requirements">

				<?php
				
				$requirements = true;
				
				$icon_check = '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
				$icon_notice = '<span class="ui-icon ui-icon-notice" style="display:inline-block;"></span>';
				
				
				echo '<h3>Checking system requirements</h3>';
				
				if (version_compare(PHP_VERSION, $php_version_required) >= 0) {
					echo '<p>'.$icon_check.' PHP version: '.phpversion() .'</p>';
			
				} else{
					echo '<p>This content management system runs in PHP version '.$php_version_required.' or higher.</p>';
					$requirements = false;
				}
				
				
				if(in_array('mod_rewrite', apache_get_modules())) {
					$mod = "mod_rewrite";
					echo '<p>'.$icon_check.' Apache module "'. $mod .'" available</p>';
				} else {
					echo '<p>Apache module "'. $mod .'" is not available</p>';
					$requirements = false;
				}
				$disabled = $requirements == false? ' disabled="disabled"' : '';
				
				?>
						
				
				<span class="toolbar"><button id="btn_site_requirements" <?php echo $disabled;?>>Click to confirm</button></span>
				<span class="ajax_spinner_setup" style='display:none'><img src="../css/images/spinner.gif"></span>
			
			</div>


			<div id="connect">

		
				<?php

				echo '<h3>Connect to MySQL database</h3>';
				
				echo '<p>Database connection file: <div class="code code-highlight">[cms root]/sys/inc.db.php</div><p>Edit <b>this file</b> manually and set correct credential values.</p>'; 
				
				$db_sample = '../../sys/inc.db-sample.php';
				if(is_file($db_sample)) {
					echo '<p>The file above looks like the sample file "inc.db-sample.php" (in same folder) - <span class="code highlight">DB_HOST, DB_NAME, DB_USER, DB_PASS</span> values</p>'; 
					$f = htmlentities(file_get_contents($db_sample));
					echo '<textarea class="code" style="width:100%;height:150px;">';
						print_r($f);
					echo '</textarea>';
				} else {
					echo '<p><i>Missing sample file "inc.db-sample.php"</i></p>';
				}
				
				// function PDO database connection, constants from configuration file
				function db_try_connect() {
					
					// constants are defined in /sys/inc.db.php
					$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
					try {
					
						$dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
						$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						return ($dbh);
					} catch(PDOException $e) {		

						$error_message = 
						'<p><span class="toolbar"><button class="btn_site_db_connect">Click to (re)connect</button></span><span class="ajax_spinner_setup" style="display:none"><img src="../css/images/spinner.gif"></span></p>
						<h3>Unable to connect to database!</h3>
						<p>Database server: <span class="code highlight">'.DB_HOST.'</span><p>Database name: <span class="code highlight">'.DB_NAME.'</span></p>
						<p>Details:</p>';
						
						die($error_message . $e->getMessage());

					}
					
				}
				
				// new PDO db connection
				$dbh = db_try_connect();

				
				if($dbh==true) {
					
					echo '<p>'.$icon_check.' Successfully connected to database.</p>';
					echo '<p>Database server: <span class="code highlight">'.DB_HOST.'</span><p>Database name: <span class="code highlight">'.DB_NAME.'</span></p></p>';
				
				}
				
				// check database
				$sql = "SELECT * FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema = DATABASE() ";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
				//$dbh = null;
			
				// emtpy database - 
				if(count($tables) < 1) {
				
					echo '<p>'.$icon_check.' This database is empty</p><p><span class="toolbar"><button id="btn_site_db_confirm">Click to confirm database</button></span></p>';
					
				} else {

					// check if database contains some common tables in Storiesaround
					$sql = "SELECT table_name FROM information_schema.tables WHERE table_name IN ('pages','site','users', 'groups', 'history') AND table_schema = DATABASE()";

					$stmt = $dbh->prepare($sql);
					$stmt->execute();
					$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					echo '<h5>This database is not empty</h5>';
					
					//echo count($tables);
						
					if(count($tables) == 5) {
						echo '<p>'.$icon_check.' Some common tables in Storiesaround was found - indicates that you can proceed. '.$icon_notice.' However - backup database and make sure this is the right database before confirm database and continuing this step.</p> 
						<p><span class="toolbar"><button class="btn_site_db_connect">Click to (re)connect</button></span></p><span class="toolbar"><button id="btn_site_db_confirm">Click to confirm database</button></span>';
					} else {
						echo '<p>An empty database is required during installation. You need to configure another database in order to continue...</p>
						<p><span class="toolbar"><button class="btn_site_db_connect">Click to (re)connect</button></span><span class="ajax_spinner_setup" style="display:none"><img src="../css/images/spinner.gif"></span></p>';
					
					}
				}
				
				$dbh = null;
				?>
				
			</div>
			
			
			<div id="setup_db">

				<?php
				echo '<h3>Setup database</h3>';
				echo '<p>'.$icon_check.' This database is empty and ready for installation of Storiesaround</p>';				
				?>
				
				<p>
					<span class="toolbar"><button id="btn_site_db_setup">Click to install</button></span><span class="ajax_spinner_setup" style="display:none"><img src="../css/images/spinner.gif"></span>
				</p>
			
			
			</div>
			
			<div id="setup_site">
		
		
				<form id="site_form">
				
				<?php
						
				if(!isset($_SESSION['site_id'])) {
					
					echo '<h3>Setup site</h3>';
					
					echo '<p>In order to use this site you need to configure basic site information and a user with full privileges</p>';					
					
					function get_input_text($field, $field_name, $field_type, $field_class, $field_style, $maxlength, $field_value, $field_help, $field_disabled=false) {
						echo '<p class="admin-text">';
							echo '<label for="'.$field.'" class="admin-text">'.$field_name.'</label><br />';
							echo '<input type="'.$field_type.'" name="'.$field.'" id="'.$field.'" class="tidy" title="Enter '.$field_name.'" class="'.$field_class.'" style="'.$field_style.'"' ;				
							echo ' value="'.$field_value.'"';
							echo ' maxlength="'.$maxlength.'"';
							if($field_disabled==true) {
								echo ' disabled="disabled"';
							}
							echo ' /> ';
							echo '<i>'.$field_help.'</i>';
						echo '</p>';
					}

					?>
					<div class="admin-panel">
						
						<h5>Basic site settings</h5>
						
						<?php
						get_input_text("site_name", "Site name", "text", "admin-text", "width:600px;", "100", "", "",  false);
						get_input_text("site_domain_url", "Site domain url", "text", "admin-text", "width:600px;", "255", $_SESSION['CMS_URL'], "", true);
						get_input_text("site_domain", "Site domain", "text", "admin-text", "width:600px;", "255", preg_replace('#^https?://#', '', $_SESSION['CMS_URL']), "", true);
						?>
						
					</div>
					
					<?php

				}
				
				?>
				
				
				<div class="admin-panel">

					<h5>User with full privileges</h5>
				
						<?php
						get_input_text("first_name", "First name", "text", "admin-text", "width:600px;", "100", "", "", false);
						get_input_text("last_name", "Last name", "text", "admin-text", "width:600px;", "100", "", "", false);
						get_input_text("user_name", "Username", "text", "admin-text", "width:600px;", "100", "admin", "", false);
						get_input_text("email", "Email", "text", "admin-text", "width:600px;", "100", "", "", false);
						get_input_text("password1", "Password (at least 8 characters, letters & digits)", "password", "admin-text", "width:600px;", "100", "", "", false);
						get_input_text("password2", "Confirm password", "password", "admin-text", "width:600px;", "100", "", "", false);
						?>
					<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
				</div>

				</form>

				
				<div class="admin-panel">
				
						<p>
							<span class="toolbar"><button id="btn_site_save">Save</button><span class="ajax_spinner_setup" style='display:none'><img src="../css/images/spinner.gif"></span></span>
							<span id="ajax_status_site_install" style='display:none'></span>

						</p>
					<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
				</div>

		
			</div>
		
		
		</div>
		
	
	<!-- close page wrapper -->
	</div>


<!-- close site wrapper -->
</div>
		

<div class="footer-wrapper">
	<?php  ?>
</div>