<?php 
if(!defined('VALID_INCL')){header('Location: index.php'); die;} 
// include core 
//--------------------------------------------------
require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if(!get_role_CMS('administrator') == 1) {die;}

$pages = new Pages();
?>

<script type="text/javascript">
	$(document).ready(function() {

		$(document).delegate("a.colorbox_edit_reload", "click", function(event){
			event.preventDefault();
			$(".colorbox_edit_reload").colorbox({
				open: true,
				width:"1260px", 
				height:"96%", 
				transition:"none",
				iframe:true, 
				onClosed:function(){ 
					location.reload(true); 
				}
			});
		});		
	
		$('#btn_selections_new').click(function(event){
			event.preventDefault();
			var action = "selections_new";
			var token = $("#token").val();
			var name = $("#name").val();
			if(name) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_selections').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_selections').hide()",700)},
					type: 'POST',
					url: 'pages_edit_ajax.php',
					data: "action=" + action + "&token=" + token + "&name=" + name,
					success: function(newdata){	
						ajaxReply('','#ajax_status_selections');
						$('<a href=pages_selections_edit.php?id='+newdata+'&token='+token+' class="colorbox_edit_reload"> selection created &raquo; click to edit <b>'+name+'</b> <span class="ui-icon ui-icon-pencil" title="edit" style="display:inline-block;vertical-align:text-bottom;"></span></a>').insertAfter("#ajax_status_selections");
						$("#name").val('');
					}
				});
			} else {
				alert('Name selection');
			}
		});		
	});

</script>


<h4 class="admin-heading">New selection</h4>

<p>
	<label for="name">Name: </label><br />
	<input type="text" id="name" name="name" />
	<span class="toolbar_add"><button id="btn_selections_new">Add selection</button></span>
	<span id="ajax_spinner_selections" style="display:none;"></span>
	<span id="ajax_status_selections" style="display:none;"></span>
</p>

