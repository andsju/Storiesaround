<?php
// include file
if(!defined('VALID_INCL')){die('Restricted access');}


?>

<div id="wrapper-user">
	<div id="user-toolbar"><?php include 'includes/inc.site_active_user2.php';?></div>
</div>

<script type="text/javascript">
	$(function() {
	
		$(".dropdown").click(function() {
			$(this).find('ul').fadeToggle(100);
		});
	
		$("#link_user").colorbox({
			width:"1260px", 
			height:"96%", 
			iframe:true, 
			transition:"none",
			onClosed:function(){ 
			}
		});

		setInterval(online, 60000);
		
		$('#themes_preview').change(function() {
			var theme = $('#themes_preview').find(':selected').text();
			var cms_dir = $("#cms_dir").val();
			var cms_dir = $("#cms_dir").val();
			$('#themes-css').attr('href', cms_dir+'/cms/themes/'+theme+'/style.css');
		});		
		
	});

	function online() {
		var action = "online";
		var token = $("#token").val();
		var cms_dir = $("#cms_dir").val();
		$.ajax({				
			type: 'POST',
			url: cms_dir+'/cms/online_ajax.php',
			data: "action=" + action + "&token=" + token,
			success: function(newdata){	
			},
		});
	}	
</script>