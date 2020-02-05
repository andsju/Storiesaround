$(document).ready(function() {
	$('#tmp_link').click(function(event){
		var token = $("#token").val();
		// delete tmp folder
		var action = "action_rapport_delete_tmp_folder";
		$.ajax({
			type: 'POST',
			url: '../admin_ajax.php',
			data: "action="+action+"&token="+token,
			success: function(){
			},
		});
	});
});
