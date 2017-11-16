<?php if(!defined('VALID_INCL')){header('Location: index.php'); die;} ?>

<script>
	$(document).ready(function() {
		
		$('#btn_add_tag').click(function(event){
			event.preventDefault();
			var tag = $("#tag").val();
			var action = "pages_add_tag";
			var token = $("#token").val();
			var users_id = $("#users_id").val();

			if(tag.length>0) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_tag').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_tag').hide()",700)},
					type: 'POST',
					url: 'pages_edit_ajax.php',
					data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&tag=" + tag,
					success: function(newdata){	
						if(newdata=='exists') {
							alert('Tag already exists');
						} else {
							$('#tag').val('');
							ajaxReply('','#ajax_result_tag');
							$('<span>&nbsp;tag created</span>').insertAfter("#ajax_result_tag");
							$('#tags').prepend('<li><a href="tags_edit.php?tags_id='+newdata+'" class="edit_tag">'+tag+'&nbsp<span class="ui-icon ui-icon-tag" style="display:inline-block;"></span></a></li>');
						}
					}
				});
			}
		});
		
		$('li.clickable').css('cursor', 'pointer');

		
		$("ul#tags").delegate( "li", "click", function() {
			$(".edit_tag").colorbox({
				width:"100%", 
				height:"100%",
				reposition: false,
				transition:"none",
				iframe:true, 
				onClosed:function(){ 
					// location.reload(false); 
				}
			});

		});		
		
		$(".edit_tag").colorbox({
			width:"100%", 
			height:"100%",
			reposition: false,
			transition:"none",
			iframe:true, 
			onClosed:function(){ 
				// location.reload(false); 
			}
		});

		$( "#tag" ).autocomplete({
			delay: 300,
			source: function( request, response ) {
				$.ajax({
					type: "post",
					url: "pages_ajax.php",
					dataType: "json",
					data: {
						action: "pages_tag",
						token: $("#token").val(),
						s: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.tag,
								id: item.pages_id,
							}
						}));
					}
				});
			},
			minLength: 1
		});


		
	});

</script>

<?php

// include core
//--------------------------------------------------
require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if(!get_role_CMS('editor') == 1) {header('Location: index.php'); die;}

?>


<h4 class="admin-heading">Tags</h4>
<p>
	<label for="tag">Tag: </label><br />
	<input type="text" id="tag" name="tag" style="width:200px;" maxlength="100" />
	<span class="toolbar_add"><button id="btn_add_tag">Add tag</button></span>
	<span id="ajax_spinner_tag" style='display:none'><img src="css/images/spinner.gif"></span>
	&nbsp;<span id="ajax_result_tag"></span>
</p>


<div style="width:96%;overflow:auto;padding-bottom:10px;" class="ui-widget ui-widget-content">

<?php
$tags = new Tags();
$row_tags = $tags->getTags();
if($row_tags) {
	echo '<ul id="tags" style="padding:10px;min-height:20px;">';
	foreach($row_tags as $row_tag) {
		echo '<li class="clickable"><a href="tags_edit.php?tags_id='.$row_tag['tags_id'].'" class="edit_tag">'.$row_tag['tag'];
		echo '&nbsp<span class="ui-icon ui-icon-tag" style="display:inline-block;"></span></a></li>';
	}
	echo '</ul>';
} else {
	echo 'No tags found';
}
?>

</div>
