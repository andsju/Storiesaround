<?php
if(!defined('VALID_INCL')){header('Location: index.php'); die;}

// include core
//--------------------------------------------------
require_once 'inc.core.php';

if(!get_role_CMS('administrator') == 1) {die;}
?>


<script type="text/javascript">

	$(document).ready(function() {
	
		$('a#site_log_php').click(function(event){
			event.preventDefault();
			var action = "get_site_log_php";
			var token = $("#token").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_log').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_log').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: "action=" + action + "&token=" + token,				
					success: function(data){	
					$("#log_view").html(data);
				},
			});
		});	
		
		$('a#site_log_pdo').click(function(event){
			event.preventDefault();
			var action = "get_site_log_pdo";
			var token = $("#token").val();
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_log').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_log').hide()",700)},
				type: 'POST',
				url: 'admin_edit_ajax.php',
				data: "action=" + action + "&token=" + token,				
					success: function(data){	
					$("#log_view").html(data);
				},
			});
		});	


		
	});	

</script>

<h3 class="admin-heading">Log files</h3>

<p>
<div style="height:30px;text-valign:top;">

<?php

$file = is_file(CMS_ABSPATH.'/log/php_error.txt') ? '('. number_format(filesize(CMS_ABSPATH.'/log/php_error.txt'),"0"," "," ") .' bytes)' : 'no error file';

?>
	view logfile &raquo; <span class="code"><a href="#" id="site_log_php">php_error.txt</a> <?php echo $file; ?></span> | <a href="#" id="site_log_pdo">pdo exceptions</a>
	<span id="ajax_spinner_log" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
	
</div>
</p>

<div id="log_view" style="border:1px dashed #000;margin:10px;overflow:auto;max-height:400px;padding:10px;">
</div>
