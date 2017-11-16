<script>
	$(document).ready(function() {
		$('#btn_add_toplevel_page').click(function(event){
			event.preventDefault();
			var id = this.id;
			var title_toplevel_page = $("#title_toplevel_page").val();
			var action = "pages_add_toplevel_page";
			var token = $("#token").val();
			if(title_toplevel_page) {
				$.ajax({
					type: 'POST',
					url: 'pages_edit_ajax.php',
					data: "action=" + action + "&token=" + token + "&title_toplevel_page=" + title_toplevel_page,
					success: function(newdata){	
						$("#new_toplevel_page").empty().append('&raquo;<a href=pages_edit.php?id=' +newdata+'>edit new page <b>'+title_toplevel_page+'</b></a>').hide().fadeIn('slow');
						$('#btn_add_toplevel_page').attr('disabled', 'disabled');
					}
				});
			} else {
				alert('Name top level page');
			}
		});

		
		$('#btn_add_child_page').click(function(event){
			event.preventDefault();
			var id = this.id;
			var pages_parent_id = $("#pages_parent_id option:selected").val();
			var title_child_page = $("#title_child_page").val();
			var action = "pages_add_child_page";
			var token = $("#token").val();
			if(pages_parent_id) {
				if(title_child_page) {
					$.ajax({
						type: 'POST',
						url: 'pages_edit_ajax.php',
						data: "action=" + action + "&token=" + token + "&pages_parent_id=" + pages_parent_id + "&title_child_page=" + title_child_page,
						success: function(newdata){	
							$("#new_child_page").empty().append('&raquo;<a href=pages_edit.php?id=' +newdata+'>edit new page <b>'+title_child_page+'</b></a>').hide().fadeIn('slow');
							$('#btn_add_child_page').attr('disabled', 'disabled');				
						}
					});
				}
			} else {
				alert('Select parent page');
			}
		});
	});

</script>

<?php

// include core 
//--------------------------------------------------
require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if(!get_role_CMS('editor') == 1) {die;}

$pages = new Pages();

//--------------------------------------------------
// check $_GET id
$id = array_key_exists('id', $_GET) ? $_GET['id'] : 0;
$id = filter_var($id, FILTER_VALIDATE_INT) ? $id : 0;
$href = 'pages_edit.php';
?>


<h4 class="admin-heading">Pages sitetree</h4>


<div style="padding:20px;" class="ui-black-white">
	<div class="clearfix" style="width:100%;">
	<div style="float:left;width:34%;">
		<b>All</b>
		<?php get_pages_tree_sitemap_all($parent_id=0, $id, $path=get_breadcrumb_path_array($id), $a=true, $a_add_class='colorbox_edit', $seo=false, $href, $open=true, $depth=1, $show_pages_id = true); ?>
	</div>
	<div style="float:left;width:33%;">
		<b>Published</b>
		<?php get_pages_tree_sitemap($parent_id=0, $id, $path=get_breadcrumb_path_array($id), $a=true, $a_add_class='colorbox_edit', $seo=false, $href, $open=true, $depth=1, $show_pages_id = true); ?>
	</div>
	<div style="float:left;width:33%;">
		<b>Not attached to sitetree</b>
		<?php get_pages_outside_sitetree($show_pages_id=true); ?>
	</div>	
	</div>	
</div>

