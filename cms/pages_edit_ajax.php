<?php
// include core 
include_once 'includes/inc.core.php';
include_once 'includes/inc.functions_pages.php';

if(!get_role_CMS('user') == 1) {die;}

$pages = new Pages();

// overall
if (isset($_POST['token'])){
	if ($_POST['token'] == $_SESSION['token']) {
		// check client user-agent, prevent session been hijacked
		if($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			die('User agent fail. Please logout and login again.');
		}
	
		$users_id = filter_input(INPUT_POST, 'users_id', FILTER_VALIDATE_INT) ? $_POST['users_id'] : 0;

		if(!get_role_CMS('contributor') == 1) { die; }

		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_STRING);	
		
		if ($pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT)) { 
			
			$history = new History();
			if ($action == 'edit_ownership') { 			
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$history->setHistory($pages_id, 'pages_id', 'ACCESS', 'edit', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
			}
			
			// check active token
			$check_edit = $history->getHistorySession($pages_id, 'pages_id');
			if(is_array($check_edit)) {

				$token_match = $_SESSION['token'] == $check_edit['session'] ? true : false;
				if(!$token_match) {
					die("!token");
				}
			}

			//check pages rights for users with role_CMS author & contributor
			if($_SESSION['role_CMS'] <= 2) {	
				$acc_edit = false;
				$pages_rights = new PagesRights();
				$users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;
				$users_rights = $pages_rights->getPagesUsersRights($pages_id, $users_id);
				$groups_rights = $pages_rights->getPagesGroupsRights($pages_id);
					
				if($users_rights) {
					if($users_rights['rights_edit'] == 1) {
						$acc_edit = true;
					}
				} else {
					if($groups_rights) {												
						if(get_membership_rights('rights_edit', $_SESSION['membership'], $groups_rights)) {
							$acc_edit = true;
						}
					}
				}
				if(!$acc_edit) { die; }	
			}

			// switch action 
			switch ($action) {

				case 'loremipsum':	
				
					$random = rand(3,5);
					echo show_lorem_ipsum($random);
					
				break;
			
				case 'update':

					// check for pages_title value
					if (strlen(trim($_POST['pages_title'])) > 0) {
						$pages_title = filter_var(trim($_POST['pages_title']), FILTER_SANITIZE_STRING);
					}
					
					// required fields validated so far, next step
					if ($pages_title){
					
						// trim content
						$pages_title_alternative = filter_var(trim($_POST['pages_title_alternative']), FILTER_SANITIZE_STRING);
						$content = trim($_POST['content']);
						$title_hide = filter_input(INPUT_POST, 'title_hide', FILTER_VALIDATE_INT) ? $_POST['title_hide'] : 0;
						$content_author = trim($_POST['content_author']);
						$rss_promote = filter_input(INPUT_POST, 'rss_promote', FILTER_VALIDATE_INT) ? $_POST['rss_promote'] : 0;
						$rss_description = trim($_POST['rss_description']);						
						$events = filter_input(INPUT_POST, 'events', FILTER_VALIDATE_INT) ? $_POST['events'] : 0;
						$reservations = filter_input(INPUT_POST, 'reservations', FILTER_VALIDATE_INT) ? $_POST['reservations'] : 0;
						$plugins = filter_input(INPUT_POST, 'plugins', FILTER_VALIDATE_INT) ? $_POST['plugins'] : 0;
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						
						// use class update
						$result = $pages->updatePagesContent($pages_id, $pages_title, $pages_title_alternative, $title_hide, $content, $content_author, $rss_promote, $rss_description, $events, $reservations, $plugins, $utc_modified);
						
						if($result) {
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('content', $content), $users_id, $_SESSION['token'], $utc_modified);							
						}			
						echo reply($result);
					}
					
				break;


				case 'update_content_only':
									
					$content = trim($_POST['content']);
					if (strlen($content)) {
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');						
						$result = $pages->updatePagesContentOnly($pages_id, $content, $utc_modified);
						if($result) {
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('content', $content), $users_id, $_SESSION['token'], $utc_modified);
						}			
						echo reply($result);
					}
					
				break;

				case 'update_title_only':
					
					$title = trim($_POST['title']);
					if (strlen($title)) {
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');						
						$result = $pages->updatePagesTitleOnly($pages_id, $title, $utc_modified);
						if($result) {
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('title', $title), $users_id, $_SESSION['token'], $utc_modified);
						}			
						echo reply($result);
					}
					
				break;
				
				case 'update_author_only':
					
					$author = trim($_POST['author']);
					if (strlen($author)) {
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');			
						$result = $pages->updatePagesAuthorOnly($pages_id, $author, $utc_modified);
						if($result) {
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('author', $author), $users_id, $_SESSION['token'], $utc_modified);
						}	
						echo reply($result);
					}
					
				break;

				case 'save_seo_link':
					
					// sanitize seo link
					$pages_id_link = filter_var(trim($_POST['pages_id_link']), FILTER_SANITIZE_STRING);
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					// use class update
					$result = $pages->updatePagesSeoLink($pages_id, $pages_id_link, $utc_modified);
					
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('seo_link', $pages_id_link), $users_id, $_SESSION['token'], $utc_modified);							
					}
					
					echo reply($result);
				
				break;	


				case 'save_meta':
					
					$meta_keywords = filter_var(trim($_POST['meta_keywords']), FILTER_SANITIZE_STRING);
					$meta_description = filter_var(trim($_POST['meta_description']), FILTER_SANITIZE_STRING);
					$meta_robots = $_POST['meta_robots'];
					$meta_additional = filter_var(trim($_POST['meta_additional']), FILTER_SANITIZE_MAGIC_QUOTES);
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					// use class update
					$result = $pages->updatePagesMeta($pages_id, $meta_keywords, $meta_description, $meta_robots, $meta_additional, $utc_modified);
					
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('meta', $meta_keywords), $users_id, $_SESSION['token'], $utc_modified);							
					}
					
					echo reply($result);
				
				break;	


				case 'update_pages_position':

					// array of pages
					$pages_id_array = $_POST['arr_pages_id'];
					// use class update
					$result = $pages->updatePagesPosition($pages_id_array);
					
					if($result) {
						echo reply($result);
					}
				
				break;

					
				case 'update_parent_id':
					
					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
					$parent_id = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT) ? $_POST['parent_id'] : null;
					$new_parent_id = filter_input(INPUT_POST, 'new_parent_id', FILTER_VALIDATE_INT) ? $_POST['new_parent_id'] : 0;
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					if($new_parent_id !== $pages_id) {
					
						// check if new parent page exists in breadcrumb path
						$a1 = get_breadcrumb_path_array($new_parent_id);
						$ok = in_array($pages_id, $a1) ? false : true;
						if($ok == true) {

							$result = $pages->updatePagesParent($pages_id, $parent_id, $new_parent_id);
							if($result) {
								echo 'Page moved.';
								$history = new History();
								$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'hierarchy', $users_id, $_SESSION['token'], $utc_modified);
							}
						}
					} else {
						echo 'Attach a page to itself is not possible.';
					}
				
				break;


				case 'remove_hierarchy':
					
					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
					$parent_id = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT) ? $_POST['parent_id'] : null;
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$result = $pages->updatePagesRemoveHierarchy($pages_id, $parent_id);
					
					$r = $result ? 'removed' : 'not removed';
					echo $r;

					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'hierarchy', $users_id, $_SESSION['token'], $utc_modified);
					}
					
				break;
					
				
				case 'sitetree_select_list':
					
					echo '<ul id="menu">';
					echo '<li id="0" data-title="root"><a href="#">root</a>';
					$id = $_POST['pages_id'];
					$href = '#';
					get_pages_tree_jqueryui($parent_id=0, $id, $path=get_breadcrumb_path_array($id), $a=true, $href, $open=true, $depth=1);
					echo '</li>';
					echo '</ul>';
					
					?>
					<script>
						$(document).ready(function() {
				
						   $("#menu").menu({
								select: function (e, ui) {
									var id = $(ui.item).attr('id');
									var title = $(ui.item).attr('data-title');
									$("#new_parent_id").empty().val(id);
									$("#sortable_pages").fadeOut().empty();
									$("#sitetree_selected_name").empty().append('<b>'+title+'</b>').hide().fadeIn('fast');
								}
						   });
						   
						});
					</script>
					<?php
					
				break;
					

				case 'browse_directory':
				
					$directory = $_POST['directory'];
					$p = CMS_ABSPATH . '/content/uploads/pages/'. $directory;
					$p1 = CMS_DIR . '/content/uploads/pages/'. $directory;
					$html = '<h4 class="admin-heading">'.$p1.'</h4><p><hr></p>';
					
					if (is_dir($p)) {

						if ($dh = opendir($p)) {
							$images_ext = array('jpg','jpeg','gif','png');

							while (($file = readdir($dh)) !== false) {
								if (!is_dir($p.'/'.$file)) {
									$ext = pathinfo($p.'/'.$file, PATHINFO_EXTENSION);
									if(in_array($ext, $images_ext)) {
										$html .= $file.' ('.round(filesize($p.'/'.$file)/1024,1).' kb)<br /><img src="'.$p1.'/'.$file.'" style="width:100px;height:auto"><br />';
									} else {
										$html .= '<pre><a href="'.$directory.'/'.$file.'" target="_blank">'.$file .'</a></pre>';
									}								
								}
							}
							closedir($dh);
						}
					}
					echo $html;
				
				break;


				case 'get_child_pages':

					$childpages_tree = get_pages_tree_sitemap_all($pages_id, $id=$pages_id, $path=get_breadcrumb_path_array($pages_id), $a=false, $a_add_class=false, $seo=false, $href='', $open=true, $depth=1, $show_pages_id = false);		
					echo $childpages_tree;					

				break;

				case 'load_plugins':
						
					$p = new Plugins();
					$plugins = $p->getPluginsActive();
						
					if($plugins) {
						echo '<select id="plugins_id">';
							echo '<option value="0">(none)</option>';
							foreach($plugins as $plugin) {
								echo '<option value="'.$plugin['plugins_id'].'">'.$plugin['plugins_class'].'</option>';
							}
						echo '</select>';
						echo '<span class="toolbar"><button id="btn_save_plugins">Select</button></span>';
					} else {
						echo '<i>plugins not installed</i>';
					}
					
					?>
					<script>
					$(document).ready(function() {
						$('#btn_save_plugins').click(function(event){
							event.preventDefault();
							var action = "save_plugins";
							var token = $("#token").val();
							var pages_id = $("#pages_id").val();
							var plugins_id = $("#plugins_id option:selected").val();
							$.ajax({
								beforeSend: function() { loading = $('#ajax_spinner_plugins').show()},
								complete: function(){ loading = setTimeout("$('#ajax_spinner_plugins').hide()",1000)},
								type: 'POST',
								url: 'pages_edit_ajax.php',
								data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&plugins_id=" + plugins_id,
								success: function() {
									window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#plugins';
									location.reload(true);
								},
							});
						});
					});
					</script>					
					<?php
				break;


				case 'save_plugins':
					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
					$plugins_id = filter_input(INPUT_POST, 'plugins_id', FILTER_VALIDATE_INT) ? $_POST['plugins_id'] : 0;
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					if($pages_id) {
						
						$pages_plugins = new PagesPlugins();
						$result = 1;
						if($plugins_id > 0) {
							$result = $pages_plugins->setPagesPlugins($pages_id, $plugins_id, $utc_modified);
						} else {
							$result = $pages_plugins->deletePagesPlugins($pages_id);
						}		
						if($result) {
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'INSERT', 'plugins', $plugins_id, $_SESSION['token'], $utc_modified);
						}
					}

				break;

					
				case 'use_plugins':
				
					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
					$plugins = filter_input(INPUT_POST, 'plugins', FILTER_VALIDATE_INT) ? $_POST['plugins'] : 0;
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

					if($pages_id) {
					
						$result = $pages->setPagesUsePlugins($pages_id, $plugins, $utc_modified);
						if($result) {
						
							echo $plugins;
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'plugins', $plugins, $_SESSION['token'], $utc_modified);
						}
					}

				break;
					

				case 'plugin_arguments':
				
					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
					
					//$plugin_arguments = filter_var($_POST['plugin_arguments'], FILTER_VALIDATE_REGEXP, array("options"=>array('regexp'=>'/^(?=^.{1,128}$)([A-Za-z0-9]|[ ":{}])*$/')));
					//$plugin_arguments = isset($_POST['plugin_arguments']) ? json_encode($_POST['plugin_arguments']) : '';
					$plugin_arguments = trim($_POST['plugin_arguments']);
					//$plugin_arguments = json_decode($_POST['plugin_arguments']);
					//$plugin_arguments = filter_var(trim($_POST['plugin_arguments']), FILTER_SANITIZE_STRING);
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

					if($pages_id) {
					
						$result = $pages->setPagesPluginArguments($pages_id, $plugin_arguments, $utc_modified);
						if($result) {
						
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'plugins', $plugin_arguments, $_SESSION['token'], $utc_modified);
						}
					}

				break;


				case 'teaser_image_reset':

					$pages_images_id = filter_input(INPUT_POST, 'pages_images_id', FILTER_VALIDATE_INT) ? $_POST['pages_images_id'] : null;
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					if($pages_images_id) {
						$result = $pages->setPagesImagesTeaserReset($pages_images_id);
						if($result) {
						}
					}

				break;

				
				case 'show_images':
					
					$pages = new Pages();
					$rows = $pages->getPagesImages($pages_id);
					$p = '../content/uploads/pages/'. $pages_id .'/';

					if($rows) {

						echo '<form id="images_form">';
						echo '<input type="hidden" id="token" name="token" value="'. $_SESSION['token'] .'" />';
						echo '<input type="hidden" id="action" name="action" value="save_images" />';
						echo '<input type="hidden" id="pages_id" name="pages_id" value="'. $pages_id .'" />';
						
						echo '<ul id="images_list" class="thumbs">';
						foreach($rows as $row) {						
							$class = ($row['story_teaser'] == 1) ? 'class="highlight"' : null;
							$checked = isset($class) ? ' checked' : null;
							$teaser = isset($class) ? ' <abbr>&nbsp;story teaser</abbr><span class="toolbar_reset_teaser" style="padding:0 20px;"><button class="btn_reset_teaser" value="'. $row['pages_images_id'] .'">&nbsp;</button></span>' : null;
							echo '<li id='. $row['filename'] .' '.$class .'>';
								// trash icon to handle click events, 
								// set button value and id to filename without fileextension (i.e no dot!)
								// in order to remove # id -> $('#'+img).closest('li').remove();
								// jquery id - get filename without extension

								$output = preg_replace("/\\.[^.\\s]{3,4}$/", "", $row['filename']);
								echo '<div style="float:left;width:50px;">';
								echo '<span class="ui-icon ui-icon-triangle-2-n-s" style="display:inline-block;cursor:n-resize;margin:0 10px;" title="Move image"></span>';
								echo '</div>';
								$link = $p . $row['filename'];
								$title = $row['filename'];
								if(strlen($row['copyright'])) {
									$title .=  ' / '.$row['copyright'];
								}
								?>
								<div style="float:left;width:100px;">
								<a href="javascript:;" onclick="image_preview('<?php echo $row['pages_images_id']; ?>', '<?php echo $pages_id; ?>', '<?php echo $_SESSION['token']; ?>')">
								<img src="<?php echo $p . $row['filename']; ?>" title="Edit image settings &raquo; <?php echo $title; ?>" style="max-height:50px;max-width:100px;vertical-align:middle;line-height:50px;" />
								</a>
								</div>
								<?php
								echo '<input type="hidden" name="pages_images_id_array[]" value='. $row['pages_images_id'] .' />';
								echo '<input title="caption" class="thumb_list" type="text" name="caption_array[]" style="width:500px;" maxlength="250" value="'. $row['caption'] .'" />';
								echo '&nbsp;';
								echo '<input type="text" title="tag" class="tag_list" style="width:100px;" value="'. $row['tag'] .'" disabled="disabled" />';
								echo '&nbsp;<span class="toolbar_delete" style="padding:0 20px;"><button class="btn_delete_image" value='. $row['filename'] .' id='. $output.'>Delete image</button></span>';
								echo '&nbsp;';
								echo '<input type="radio" name="story_teaser" value="'. $row['pages_images_id'] .'" '. $checked .' title="story teaser" />';							
								$s = $row['promote'] ? '<span class="ui-icon ui-icon-arrowreturn-1-n" style="display:inline-block;margin:0 8px;" title="Promoted image"></span>' : ''; 
								echo $s;
								echo $teaser;
								
							echo '</li>';
						}
						echo '</ul>';
						echo '</form>';
					}
					
					?>
					<script>
						var token = $("#token").val();
						function image_preview(pages_images_id, pages_id, token) {
							w=window.open('pages_images_preview.php?pages_images_id='+pages_images_id+'&pages_id='+pages_id+'&token='+token,'','menubar=no,location=no,directories=no,toolbar=no,scrollbars=yes');
							w.focus();
						}
						
						$(function() {
							$( ".toolbar_delete button" ).button({
								icons: {
									primary: "ui-icon-trash"
								},
								text: false
							});
							
							$( ".toolbar_view button" ).button({
								icons: {
									primary: "ui-icon-extlink"
								},
								text: false
							});

							$( ".toolbar_reset_teaser button" ).button({
								icons: {
									primary: "ui-icon-close"
								},
								text: false
							});

							$( "#images_list" ).sortable({
								axis: 'y'
							});
														
							$('input:radio').click( function(){	
								$(this).parent('li')
									.siblings().children('abbr').empty();
								$(this).parent('li')
									.children('abbr').empty();
								$(this).parent('li')
									.toggleClass('highlight', this.checked)
									.append("<abbr>&nbsp;story teaser</abbr>")
									.siblings().removeClass('highlight');
							});

							$('.btn_delete_image').click(function(event){
								event.preventDefault();
								var img = $(this).val();
								// get id from jQuery, use event.currentTarget.id instead of event.target.id
								var id = event.currentTarget.id;
								$("#dialog_delete_image").dialog("open");
								$("#dialog_delete_image").dialog({
									buttons : {
									"Confirm" : function() {
										image_delete(img, id);
										$(this).dialog("close");
									},
									"Cancel" : function() {
										$(this).dialog("close");
										}
									}
								});
							});

							$('.btn_reset_teaser').click(function(event){
								event.preventDefault();
								var pages_id = $("#pages_id").val();
								var pages_images_id = $(this).val();
								var action = "teaser_image_reset";
								var token = $("#token").val();
								var users_id = $("#users_id").val();
								
								$.ajax({
									beforeSend: function() { loading = $('#ajax_spinner_images').show()},
									complete: function(){ loading = setTimeout("$('#ajax_spinner_images').hide()",1000)},
									type: 'POST',
									url: 'pages_edit_ajax.php',
									data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_id=" + pages_id + "&pages_images_id=" + pages_images_id,
									success: function(message){	
										load_images();
									}
								});
							});	
						});
					</script>
					<?php

				break;

				case 'image_rotate':				

					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
					$pages_images_id = filter_input(INPUT_POST, 'pages_images_id', FILTER_VALIDATE_INT) ? $_POST['pages_images_id'] : null;

					$pages = new Pages();
					$row = $pages->getPagesImagesMeta($pages_images_id);

					$filename = CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/'. $row['filename'];


					$extension = pathinfo($_SERVER['DOCUMENT_ROOT'] . $filename, PATHINFO_EXTENSION);
					$ext = strlen($extension);
					$width_ext = $ext + 4;
					$pre = substr($filename, 0, - $width_ext);
									
					// biggest possible
					$filename = $pre.'726.'.$extension;	
					
					//echo $filename;
					
					$rotate = filter_input(INPUT_POST, 'rotate', FILTER_VALIDATE_INT) ? $_POST['rotate'] : 0;

					//$path = CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/';
					//$dst_path = CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/';
					
					
					$image = new Image();
					$img = $image->image_rotate($filename, $rotate);
					

					$file = substr($filename, strlen(CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/'));
					//echo $file;
					
					//echo $file;
					echo CMS_DIR.'/content/uploads/pages/'. $pages_id .'/'.$file;
				
				
				break;
				
				case 'image_apply_filter':				

					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
					$pages_images_id = filter_input(INPUT_POST, 'pages_images_id', FILTER_VALIDATE_INT) ? $_POST['pages_images_id'] : null;
					$filter = filter_input(INPUT_POST, 'filter', FILTER_SANITIZE_STRING) ? $_POST['filter'] : '';

					$pages = new Pages();
					$row = $pages->getPagesImagesMeta($pages_images_id);
				
					// biggest possible
					$image = new Image();
					$filename_and_path = CMS_DIR.'/content/uploads/pages/'. $pages_id .'/'. $row['filename'];
					
					$filename = $image->get_max_image($filename_and_path);
					
					$filename = substr($filename, strrpos( $filename, '/')+1);

					// absolute path to open file
					$path = CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/';
					
					// add prefix to new file
					$new_filename_prefix = '__';
						
					// if file exists - begin delete
					if(file_exists($path . $new_filename_prefix . $filename)) {
						unlink($path . $new_filename_prefix . $filename);
					}
					// apply filter
					$img = $image->image_filter($path, $filename, $new_filename_prefix, $filter);

					
					//echo new image file as path and filename;
					if(!$img) {
						echo CMS_DIR.'/content/uploads/pages/'. $pages_id .'/'.$new_filename_prefix . $filename;
					}
					
				break;

				
				
				case 'image_save_new':				

					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
					$pages_images_id = filter_input(INPUT_POST, 'pages_images_id', FILTER_VALIDATE_INT) ? $_POST['pages_images_id'] : null;

					$ratio = filter_input(INPUT_POST, 'ratio', FILTER_VALIDATE_FLOAT) ? $_POST['ratio'] : 0;
					
					if($ratio) {
					
						$ratio = 1/$ratio;
					}
					
					$pages = new Pages();
					$row = $pages->getPagesImagesMeta($pages_images_id);

					// biggest possible
					$image = new Image();
					$filename_and_path = CMS_DIR.'/content/uploads/pages/'. $pages_id .'/'. $row['filename'];
					
					$filename = $image->get_max_image($filename_and_path);
					// just filename
					$filename = substr($filename, strrpos( $filename, '/')+1);
					
					$file = CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/'. $filename;					
					$file_new = CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/__'. $filename;
					
					
					function rename_file($oldfile,$newfile) {
						if(file_exists($oldfile)) {
							if (!rename($oldfile,$newfile)) {
								if (copy ($oldfile,$newfile)) {
									unlink($oldfile);
									return TRUE;
								}
								return FALSE;
							}
							return TRUE;
						}
					}					
					
					$try = rename_file($file_new, $file);

					if($try) {

						$pathinfo = pathinfo($file);
						$ext = $pathinfo['extension'];
						$filename = $pathinfo['filename'];
						$pos_underscore = strrpos($filename, '_') + 1;
						$filename_base = substr($filename, 0, $pos_underscore);

						$f = CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/';					

						$sizes = $image->get_image_sizes();

						//save versions						
						foreach($sizes as $size) {
							$image->image_resize($f . $filename .'.'. $ext, $f . $filename_base . $size .'.'. $ext, $size);
						}
						
						// update db
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');						
						$result = $pages->updatePagesImagesCrop($pages_images_id, $ratio, $utc_modified);
					}
					
					echo CMS_DIR.'/content/uploads/pages/'. $pages_id .'/'.$filename .'.'. $ext;
					
					
				break;
				
				case 'image_crop':
					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : null;
					$pages_images_id = filter_input(INPUT_POST, 'pages_images_id', FILTER_VALIDATE_INT) ? $_POST['pages_images_id'] : null;

					// crop values
					$dst_x = 0;
					$dst_y = 0;
					$src_x = filter_input(INPUT_POST, 'left', FILTER_VALIDATE_INT) ? $_POST['left'] : 0;
					$src_y = filter_input(INPUT_POST, 'top', FILTER_VALIDATE_INT) ? $_POST['top'] : 0;
					$dst_w = filter_input(INPUT_POST, 'width', FILTER_VALIDATE_INT) ? $_POST['width'] : 0;
					$dst_h = filter_input(INPUT_POST, 'height', FILTER_VALIDATE_INT) ? $_POST['height'] : 0;
					$src_w = filter_input(INPUT_POST, 'width', FILTER_VALIDATE_INT) ? $_POST['width'] : 0;
					$src_h = filter_input(INPUT_POST, 'height', FILTER_VALIDATE_INT) ? $_POST['height'] : 0;
					$width_edited_image = filter_input(INPUT_POST, 'width_edited_image', FILTER_VALIDATE_INT) ? $_POST['width_edited_image'] : 0;
					
					// set scale - if window size is smaller than max image 
					$scale = round($width_edited_image/$src_w, 3);

					// ratio 
					// if value == 'transform' image width will resized
					// other values preserve width to 726px
					$ratio = filter_input(INPUT_POST, 'ratio', FILTER_SANITIZE_STRING) ? $_POST['ratio'] : '';

					switch($ratio) {
						
						case '4':
						case '3':
						case '2.76':
						case '2.35':
						case '2.1':
						case '1.77':
						case '1.33':
						case '1':
							$dst_w = $src_w = $width_edited_image;
							$dst_h = $src_h = round($width_edited_image/$ratio);
							$src_x = 0;
							$src_y = round($src_y*$scale);
						break;

						case 'transform':
						break;
						
						case '';
						break;
						
					}
					
					$pages = new Pages();
					$row = $pages->getPagesImagesMeta($pages_images_id);

					// biggest possible
					$image = new Image();
					$filename_and_path = CMS_DIR.'/content/uploads/pages/'. $pages_id .'/'. $row['filename'];
										
					$filename = $image->get_max_image2($filename_and_path, $return='filename');
					// absolute path to open file
					$path = CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/';
					
					// add prefix to new file
					$new_filename_prefix = '__';
						
					// if file exists - begin delete
					if(file_exists($path . $new_filename_prefix . $filename)) {
						unlink($path . $new_filename_prefix . $filename);
					}
					// create new file
					if ($image->image_crop($path, $filename, $new_filename_prefix, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)) {
						echo CMS_DIR.'/content/uploads/pages/'. $pages_id .'/'.$new_filename_prefix.$filename;
					}
				
				break;
				
				
				case 'show_files':
					
					$p = '../content/uploads/pages/'. $pages_id;
					if (is_dir($p)) {
						if ($dh = opendir($p)) {
							$images_ext = array('jpg','jpeg','gif','png');
							echo '<ul id="files_list" class="files">';
							while (($file = readdir($dh)) !== false) {
								if (!is_dir($p.'/'.$file)) {
									$ext = pathinfo($p.'/'.$file, PATHINFO_EXTENSION);
									if(!in_array($ext, $images_ext)) {
										echo '<li style="width:100%;">';
											echo '<span class="toolbar_delete"><button class="btn_delete_file" value="'. $file .'" id="'.$file.'">Delete file</button></span>';
											echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
											echo '<a href='.$p.'/'.$file.' target="_blank">preview</a>';
											echo '&nbsp;&nbsp;|&nbsp;&nbsp;';
											echo $file;						
											echo '&nbsp;&nbsp;|&nbsp;&nbsp;<span class="code">';
											echo $p.'/'.$file;
											echo '</span>';
										echo '</li>';
									}
								}
							}
							closedir($dh);
							echo '</ul>';
						}
					}
					?>
					<script>						
						$(document).ready(function() {
							$( ".toolbar_delete button" ).button({
								icons: {
									primary: "ui-icon-trash"
								},
								text: false
							});							

							$('.btn_delete_file').click(function(event){
								event.preventDefault();
								var file = $(this).val();
								var id = event.currentTarget.id;
								var action = "delete_file";
								var token = $("#token").val();
								var pages_id = $("#pages_id").val();
								$(this).closest("li").remove();
								$.ajax({
									type: 'POST',
									url: 'pages_edit_ajax.php',
									data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&file=" + file,
									success: function(){
										console.log(id)										
									},
								});
							});
						});
					</script>					
					<?php

				break;


				case 'delete_image':
					if ($_POST['image']) {
						$p = '../content/uploads/pages/'. $pages_id .'/';
						$filename = $_POST['image'];
						
						//file named like 'image_222.jpg'
						//keep file extension
						$ext = strrchr($filename,'.');
						// get position of right '_'
						$pos = strrpos($filename, '_');

						// possible image sizes
						$objImage = new Image();
						$sizes = $objImage->get_image_sizes();		
						foreach ($sizes as $size) {
							$f = substr($filename, 0, $pos) .'_'.$size . $ext;
							if (is_file($p . $f)) {
								unlink($p . $f);
							}
						}
						// delete database saved sizes, includes original image if exists 
						$pages = new Pages();
						$rows = $pages->getPagesImagesSizes($filename, $pages_id);
						if ($rows) {
							foreach($rows as $row) {
								$sizes_in_db = $row['sizes'];
							}
						}
						$sizes = explode(",", $sizes_in_db);
						foreach ($sizes as $size) {
							$f = substr($filename, 0, $pos) .'_'.$size . $ext;
							if (is_file($p . $f)) {
								unlink($p . $f);
							}
						}

						// delete from database
						$result = $pages->deletePagesImages($filename, $pages_id);
						if($result) {
							$history = new History();
							$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history->setHistory($pages_id, 'pages_id', 'DELETE', describe('delete image', $filename), $users_id, $_SESSION['token'], $utc_modified);
						}
					}
				break;
					
					
				case 'delete_file':
				
					if ($_POST['file']) {
						$p = '../content/uploads/pages/'. $pages_id .'/';
						$f = $_POST['file'];
												
						if (is_file($p . $f)) {
							// remove file
							unlink($p . $f);
							return true;
						}
					}
				
				break;


				case 'save_images':					
					
					// get arrays from image ul list
					$pages_images_id_array = $_POST['pages_images_id_array'];					
					$caption_array = $_POST['caption_array'];
					$story_teaser = isset($_POST['story_teaser']) ? $_POST['story_teaser'] : 0;
					
					$pages = new Pages();
					$position = 0;
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					if(is_array($pages_images_id_array)) { 
						
						foreach ($pages_images_id_array as $pages_images_id) {							
							$caption = filter_var($caption_array[$position], FILTER_SANITIZE_STRING);
							$story_teaser2 = ($pages_images_id == $story_teaser) ? 1 : 0;
							$result = $pages->updatePagesImages($pages_images_id, $caption, $position, $story_teaser2, $utc_modified);
							$position = $position + 1;
						}
					}
						
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					if($position) {
						echo 'saved';
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'images', $users_id, $_SESSION['token'], $utc_modified);
					}
					
				break;
				
				
				case 'save_site_header_setup_image':
					
					$header_image = json_encode($_POST['header_image']);
					$header_caption = json_encode($_POST['header_caption']);
					//$header_caption_show = filter_input(INPUT_POST, 'header_caption_show', FILTER_VALIDATE_INT) ? $_POST['header_caption_show'] : 0;
					//$header_image_timeout = filter_input(INPUT_POST, 'header_image_timeout', FILTER_VALIDATE_INT) ? $_POST['header_image_timeout'] : 10000;
					$header_caption_show = $_POST['header_caption_show'];
					$header_image_timeout = $_POST['header_image_timeout'];
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					$result = $pages->updatePagesSetupSiteHeaderImage($pages_id, $header_image, $header_caption, $header_caption_show, $header_image_timeout, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('site header image', $header_image), $users_id, $_SESSION['token'], $utc_modified);
					}
					
				break;
	

				case 'save_site_content_setup':

					$comments = $_POST['comments'];
					$stories_columns = $_POST['stories_columns'];

					$result = $pages->updatePagesSetupSiteContent($pages_id, $comments,$stories_columns);
					if($result) {
						$history = new History();
						$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'site content setup', $users_id, $_SESSION['token'], $utc_modified);
					}

				break;


				case 'save_site_templates_setup':

					$template = $_POST['setup_template'];
					$template_custom = $_POST['template_custom'];
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
		
					$result = $pages->updatePagesSetupTemplate($pages_id, $template, $template_custom, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'site template', $users_id, $_SESSION['token'], $utc_modified);
					}
				
				break;


				case 'save_stories_settings':

					$stories_equal_height = $_POST['stories_equal_height'];
					$stories_last_modified = $_POST['stories_last_modified'];
					$stories_image_copyright = $_POST['stories_image_copyright'];
					$stories_wide_teaser_image_width = filter_input(INPUT_POST, 'stories_wide_teaser_image_width', FILTER_VALIDATE_INT);
					$stories_wide_teaser_image_align = filter_input(INPUT_POST, 'stories_wide_teaser_image_align', FILTER_VALIDATE_INT);					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$stories_css_class = $_POST['stories_css_class'];
					$result = $pages->setPagesSetupStoriesSettings($pages_id, $stories_equal_height, $stories_last_modified, $stories_image_copyright, $stories_wide_teaser_image_width, $stories_wide_teaser_image_align, $stories_css_class, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'stories_settings', $users_id, $_SESSION['token'], $utc_modified);						
					}
					
				break;

	
				case 'save_stories_child':

					$stories_child = $_POST['stories_child'];
					$stories_child_area = $_POST['stories_child_area'];
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

					$result = $pages->setPagesSetupStoriesChild($pages_id, $stories_child, $stories_child_area, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'child stories', $users_id, $_SESSION['token'], $utc_modified);
						
						if($stories_child > 0) {
							$rows_childs = $pages->getPagesStoriesChild($pages_id);
							if($rows_childs) {
								get_box_content_child($rows_childs);
							}
						}
					}
					
				break;


				case 'save_stories_promoted':

					$stories_promoted = $_POST['stories_promoted'];	
					$stories_promoted_area = $_POST['stories_promoted_area'];					
					$stories_filter = filter_var(trim($_POST['stories_filter']), FILTER_SANITIZE_STRING);
					$stories_limit = $_POST['stories_limit'];
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					$result = $pages->setPagesSetupStoriesPromoted($pages_id, $stories_promoted, $stories_promoted_area, $stories_filter, $stories_limit, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'promoted stories', $users_id, $_SESSION['token'], $utc_modified);

						if($stories_promoted > 0) {
							$limit = isset($stories_limit) ? $stories_limit : 0;
							$rows_promoted = $pages->getPagesStoriesPromoted($stories_filter, $limit);
							if($rows_promoted) {
								get_box_content_promoted($rows_promoted, $stories_promoted);
							}
						}
					}
					
				break;
					

				case 'save_stories_event_dates':

					$stories_event_dates = filter_input(INPUT_POST, 'stories_event_dates', FILTER_VALIDATE_INT) ? $_POST['stories_event_dates'] : 0;					
					$stories_event_dates_filter = filter_var(trim($_POST['stories_event_dates_filter']), FILTER_SANITIZE_STRING);
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					$result = $pages->setPagesSetupStoriesEventDates($pages_id, $stories_event_dates, $stories_event_dates_filter, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'event stories', $users_id, $_SESSION['token'], $utc_modified);

						if($stories_event_dates > 0) {
							$date = date('Y-m-d');							
							$rows = $pages->getPagesStoryContentPublishEvent($stories_event_dates_filter, $date, $period='next');
							get_box_content_event($rows);
						}
					}
					
				break;


				
				case 'use_widgets':
	
					// check post variables, could be a classname (new widget / string) or an pages_widgets_id (edit widget / integer)
					// edit widget, check for $pages_widgets_id from post
					$pages_widgets_id = (isset($_POST['pages_widgets_id'])) ? $_POST['pages_widgets_id'] : null;					
					
					if(isset($pages_widgets_id)) {
						// get widgets_id, needed to move on...
						$pw = new PagesWidgets();
						$arr = $pw->viewPagesWidgets($pages_widgets_id);
						$widgets_id = $arr['widgets_id'];
						$classname = $arr['widgets_class'];						
					} else {
						// new widget, check for widget class from post
						$classname = (isset($_POST['widget'])) ? $_POST['widget'] : null;
					}	
						
					if(isset($classname)) {
						// get widgets_id from db, use function in class Widgets
						$w = new Widgets();
						$widgets_id = (isset($widgets_id)) ? $widgets_id : $w->getWidgetsId($classname);					
					}
					
					if(isset($widgets_id)) {
						
						// use class						
						$w_class = new $classname();
						// get basic info from this class
						$info = $w_class->info();

						// save new widget to db
						if(!$pages_widgets_id) {
							$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT);
							$lastInsertId = $w_class->savePagesWidgets($widgets_id, $pages_id, $widgets_action='', $area='', $position=1);
							$pages_widgets_id = $lastInsertId;
						}
												
						echo '<table style="width:100%;margin-bottom:10px;"><tr><td>';
						echo '<h4>'.$info['title'].'</h4>';
						echo '</td><td style="width:40%;text-align:left;"><span class="ui-icon ui-icon-info" style="display:inline-block;" title="'.$info['description'].'"></span></td><td style="width:40%;text-align:right;">';
						echo '&raquo;&nbsp;<a style="cursor:pointer;" onclick="widget_preview('. $pages_widgets_id .','. $pages_id.')">Preview <span class="ui-icon ui-icon-newwin" style="display:inline-block;vertical-align:text-bottom;"></span></a>';
						echo '</td></tr></table>';

						// now editing...use in edit / save / add						
						echo '<input type="hidden" id="token" name="token" value="'. $_SESSION['token'] .'" />';
						echo '<input type="hidden" id="action" name="action" value="save_widgets" />';
						echo '<input type="hidden" id="widgets_id" name="widgets_id" value="'. $widgets_id .'" />';
						echo '<input type="hidden" id="pages_widgets_id" name="pages_widgets_id" value="'. $pages_widgets_id .'" />';
						
						$w->wform($w_class, $pages_widgets_id);
						
						?>	
						<script>
						
							function widget_preview(id, pages_id) {
								w=window.open('widgets_preview.php?&token=<?php echo $_SESSION['token']; ?>&pages_id='+pages_id+'&pages_widgets_id='+id,'','width=1280,height=800,scrollbars=1,menubar=0,location=0,directories=0,toolbar=0');
								w.focus();
							}
						
							$(document).ready(function() {
							
								var accordion_active = $( "#accordion_add_content" ).accordion( "option", "active" );
								if(accordion_active != 1) {
									$('#accordion_add_content').accordion( "option", "active", 1  );
								}
								
								$( ".toolbar button" ).button({
								});

								$('#btn_save_widget').click(function(event){
									event.preventDefault();									
									var querystring = $("#widgets_form").serialize();
									
									$.ajax({
										beforeSend: function() { loading = $('.ajax_spinner_widgets_edit').show()},
										complete: function(){ loading = setTimeout("$('.ajax_spinner_widgets_edit').hide()",700)},
										type: 'POST',
										url: 'pages_edit_ajax.php',
										data: querystring,
										success: function(message){
											ajaxReply(message,'.ajax_status_widgets_edit');
										},
									});
								});
							
								$('.btn_delete_widget').click(function(event){
									event.preventDefault();
									var action = "widgets_delete_one";
									var token = $("#token").val();
									var pages_id = $("#pages_id").val();
									var pages_widgets_id = this.id;
									$.ajax({
										beforeSend: function() { loading = $('#ajax_spinner_widgets_edit').show()},
										complete: function(){ loading = setTimeout("$('#ajax_spinner_widgets_edit').hide()",700)},
										type: 'POST',
										url: 'pages_edit_ajax.php',
										data: "action=" + action + "&token=" + token + "&pages_widgets_id=" + pages_widgets_id + "&pages_id=" + pages_id,
										success: function(newdata){	
											$("#widgets_stage").empty().append('deleted').hide().fadeIn('fast');
											$("#"+newdata).hide();
											ajaxReply('','#ajax_status_widgets_edit');
										},
									});
								
								});							
							
								$('#btn_close_widget').click(function(event){
									event.preventDefault();								
									$("#widgets_stage").empty().append().hide().fadeIn('fast');									
								});
							
								$('#btn_save_widget_to_area').click(function(event){
									event.preventDefault();
									var action = "widgets_area";
									var token = $("#token").val();
									var pages_id = $("#pages_id").val();
									var pages_widgets_id = $("#pages_widgets_id").val();
									var widgets_area_target = $("#widgets_area_target option:selected").val();

									$.ajax({
										beforeSend: function() { loading = $('#ajax_spinner_widgets_edit').show()},
										complete: function(){ loading = setTimeout("$('#ajax_spinner_widgets_edit').hide()",700)},									
										type: 'POST',
										url: 'pages_edit_ajax.php',
										data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&widgets_area_target=" + widgets_area_target + "&pages_widgets_id=" + pages_widgets_id,
										success: function(newdata){	
											
											$('div.portlet').remove( ":contains('("+pages_widgets_id+")')" );											
											$("#"+widgets_area_target).append(newdata).hide().fadeIn('fast');
											$("#widgets_stage").empty().hide().fadeIn('fast');
											ajaxReply('','#ajax_status_widgets_edit');
											
											$('.btn_widgets_edit_view').click(function(event){
												event.preventDefault();
												var action = "use_widgets";
												var token = $("#token").val();
												var pages_id = $("#pages_id").val();
												var pages_widgets_id = this.id;
												$.ajax({
													type: 'POST',
													url: 'pages_edit_ajax.php',
													data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&pages_widgets_id=" + pages_widgets_id,
													success: function(newdata){	
														$("#widgets_stage").empty().append(newdata).hide().fadeIn('fast');
													},
												});
											});
											
											$(function() {
												$( ".toolbar_widgets_edit button" ).button({
													icons: {
														primary: "ui-icon-pencil"
													},
													text: false
												});
											});											
										},
									});
								});	
							
								$(".colorbox_preview").colorbox({
									width:"800px", 
									height:"85%", 
									iframe:true,
									onClosed:function(){ 
									}
								});
							});
						</script>						
						<?php
					}
					
				break;

				case 'pages_publish':
					
					$access = filter_input(INPUT_POST, 'access', FILTER_VALIDATE_INT) ? $_POST['access'] : 0;
					$title_tag = filter_var(trim($_POST['title_tag']), FILTER_SANITIZE_STRING);					
					$pages_title = filter_var(trim($_POST['pages_title']), FILTER_SANITIZE_STRING);
					$pages_title_alternative = filter_var(trim($_POST['pages_title_alternative']), FILTER_SANITIZE_STRING);
					$content = trim($_POST['content']);
					$content_author = trim($_POST['content_author']);
					$pages_id_link = filter_var(trim($_POST['pages_id_link']), FILTER_SANITIZE_STRING);
					$datetime_start = isValidDateTime($_POST["datetime_start"]) ? date("Y-m-d H:i:s", strtotime($_POST["datetime_start"])) : null; 
					$datetime_end = isValidDateTime($_POST["datetime_end"]) ? date("Y-m-d H:i:s", strtotime($_POST["datetime_end"])) : null;
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					if($status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT)) {			
						$result = $pages->setPagesPublish($pages_id, $status, $access, $title_tag, $pages_title, $pages_title_alternative, $content, $content_author, $pages_id_link, $datetime_start, $datetime_end, $utc_modified);
						if($result) {
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'publish', $users_id, $_SESSION['token'], $utc_modified);
						}
						echo reply($result);
					}
					
				break;

				case 'pages_status':
					
					$datetime_start = null; 
					$datetime_end = null;
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					if($status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT)) {			
						$result = $pages->setPagesStatus($pages_id, $status, $datetime_start, $datetime_end, $utc_modified);
						if($result) {
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('status', $status), $users_id, $_SESSION['token'], $utc_modified);
						}
						echo reply($result);
					}
					
				break;

				
				case 'pages_delete':
					
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$pages = new Pages();
					$arr = $pages->getPagesEditContent($pages_id);

					if(!$arr) {die;};
					
					$reply = null;

					// status Trash
					if($arr['status'] == 5) {
						$reply[] = '• trash status ok';
					} 
					
					// no children
					if($arr['parent'] == 0) {
						$reply[] = '• parent ok';
					}
					
					$children = $pages->getPagesNode($pages_id);
					if(!$children) {
						$reply[] = '• children check ok';
					} else {
						$reply[] = '• <b>NOT DELETED</b> - this page has one ore more child pages attached';

						if($reply) {
							echo 'Checking settings before delete action:<br />';
							foreach($reply as $replies) {
								echo $replies . '<br />';
							}
						}
						die;						
					}
					
					// delete this pages_calendars
					$calendars = new Calendar();
					$r = $calendars->deletePagesCalendar($pages_id);

					if($r) {
						$reply[] = '• calendar removed';
					} else {
						$reply[] = '• no calendar found';
					}
					
					// delete this pages_images					
					$r = $pages->deletePagesImagesAll($pages_id);
					if($r) {
						$reply[] = '• images in db deleted';
					}
					
					// delete this pages_plugins
					$pp = new PagesPlugins();
					
					$r = $pp->deletePagesPlugins($pages_id);
					if($r) {
						$reply[] = '• plugins deleted';
					}
					
					// delete this pages_stories
					$r = $pages->deleteSelectedPagePagesStories($pages_id);					
					if($r) {
						$reply[] = '• story deleted';
					}
					
					// delete this pages_widgets
					$r = $pages->deleteSelectedPagePagesWidgets($pages_id);					
					if($r) {
						$reply[] = '• widgets deleted';
					}
					
					// remove folder and content
					function rrmdir($dir) { 
						foreach(glob($dir . '/*') as $file) { 
							if(is_dir($file)) {
								rrmdir($file); 
							} else {
								unlink($file);
							}
						} 
						rmdir($dir); 
					}

					$dir = CMS_ABSPATH."/content/uploads/pages/".$pages_id;
					if (is_dir($dir)) {
						
						rrmdir($dir);
						$reply[] = '• folder and files deleted';
					}
					
					// remove page
					$result = $pages->deleteSelectedPage($pages_id);					
					if($result) {
						$reply[] = '• <b>PAGE DELETED</b>';
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'DELETE', 'Page deleted', $users_id, $_SESSION['token'], $utc_modified);
					}
					
					if($reply) {
						echo 'Checking settings before delete action:<br />';
						foreach($reply as $replies) {
							echo $replies . '<br />';
						}
					}
					
				break;

				case 'pages_create_folder':					
					
					if($pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT)) {			

						// create folder
						if (!is_dir(CMS_ABSPATH."/content/uploads/pages/".$pages_id)) {
							mkdir(CMS_ABSPATH."/content/uploads/pages/".$pages_id, 0777);
							$r = 'folder created';
						} else {
							$r = 'folder exists';
						}		
					
						echo reply($r);
					}
					
				break;
				
				
				case 'seo_link':
					
					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : 0;
					$title = filter_var(trim($_POST['pages_title']), FILTER_SANITIZE_STRING);
					$remove_stopwords = filter_input(INPUT_POST, 'stopwords', FILTER_VALIDATE_INT) ? $_POST['stopwords'] : 0;
					
					include_once 'includes/inc.seo_link.php';

					$seo_link = set_seo_title($title, $replace = "-", $remove_stopwords, $stopwords_array);
					
					// check if suggested seo_link is unique, exclude this page if already set
					$result = $pages->checkPagesSeo($seo_link, $pages_id);
					if(!$result) {
						echo $seo_link;
					}
					
				break;
				
				
				case 'suggest_meta_keywords':
					
					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : 0;
					$title = filter_var(trim($_POST['pages_title']), FILTER_SANITIZE_STRING);
					$story = filter_var(trim($_POST['story_content']), FILTER_SANITIZE_STRING);
					$content = filter_var(trim($_POST['content']), FILTER_SANITIZE_STRING);
					// strip_tags, keep leading words from fields, 					
					$story = strip_tags($story);
					$content = strip_tags($content);
					
					
					$remove_stopwords = 1;
					$input = $title .' '.$story. ' '.$content;  
					
					//echo $input;
					include_once 'includes/inc.seo_link.php';

					$str = suggest_words($input, $replace = ",", $remove_stopwords, $stopwords_array);
					
					echo $str;

				break;
				
					
				case 'pages_settings':
					
					$pages_id = filter_input(INPUT_POST, 'pages_id', FILTER_VALIDATE_INT) ? $_POST['pages_id'] : 0;
					$lang = filter_var(trim($_POST['lang']), FILTER_SANITIZE_STRING);
					$search_field_area = $_POST['search_field_area'];
					$category = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
					$category_position = $_POST['category_position'];
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$breadcrumb = filter_input(INPUT_POST, 'breadcrumb', FILTER_VALIDATE_INT) ? $_POST['breadcrumb'] : 0;
					
					$result = $pages->setPagesSettings($pages_id, $breadcrumb, $lang, $search_field_area, $category, $category_position, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'lang', $users_id, $_SESSION['token'], $utc_modified);
					}
					
				break;

				
				case 'pages_story':
					
					$story_content = trim($_POST['story_content']);
					$tag = isset($_POST['tag']) ? implode(",", $_POST['tag']) : "";
					$story_css_class = trim($_POST['story_css_class']);
					$story_custom_title = filter_input(INPUT_POST, 'story_custom_title', FILTER_VALIDATE_INT) ? $_POST['story_custom_title'] : 0;
					$story_custom_title_value = filter_var(trim($_POST['story_custom_title_value']), FILTER_SANITIZE_STRING);
					$story_wide_teaser_image = filter_input(INPUT_POST, 'story_wide_teaser_image', FILTER_VALIDATE_INT) ? $_POST['story_wide_teaser_image'] : 0;
					$story_promote = filter_input(INPUT_POST, 'story_promote', FILTER_VALIDATE_INT) ? $_POST['story_promote'] : 0;	
					$story_link = filter_input(INPUT_POST, 'story_link', FILTER_VALIDATE_INT) ? $_POST['story_link'] : 0;	
					$story_event = filter_input(INPUT_POST, 'story_event', FILTER_VALIDATE_INT) ? $_POST['story_event'] : 0;
					$story_event_date = isValidDateTime($_POST["story_event_datetime"]) ? date("Y-m-d H:i:s", strtotime($_POST["story_event_datetime"])) : null; 
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					
					$result = $pages->setPagesStory($pages_id, $story_content, $tag, $story_promote, $story_link, $story_event, $story_event_date, $story_css_class, $story_custom_title, $story_custom_title_value, $story_wide_teaser_image, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'story', $users_id, $_SESSION['token'], $utc_modified);
					}

					$result = $pages->getPagesStoryContent($pages_id);

					$is_story = isset($result['pages_id']) ? $result['pages_id'] : null;
					
					if($is_story) {
					
						$title = $result['story_custom_title'];
						$title_value = strlen($result['story_custom_title_value']) > 0 ? $result['story_custom_title_value'] : $result['title'];
						$img = str_replace('_100.','_726.',$result['filename']);
						$img ='../content/uploads/pages/'.$pages_id.'/'.$img;
						$pages_id = $result['pages_id'];
						$ratio = $result['ratio'];
						$css_class = strlen($result['story_css_class'])>0 ? $result['story_css_class'] : 'stories-content';
						$story = $result['story_content'];
						$utc = new DateTime($result['utc_start_publish']);
						$date = $utc->format('Y-m-d H:i');
						$caption = isset($result['filename']) ? $result['caption'] : '';
						
						function preview_story($w, $img, $pages_id, $ratio, $title, $title_value, $css_class, $story, $story_wide_teaser_image, $caption, $date, $stories_last_modified) {

							$title_value = isset($title) ? $title_value : null;
							echo '<div style="float:left;margin:20px;"<span style="font-size:0.8em;">'.$w.'px</span>';
							echo '<div style="width:'.$w.'px;border:1px dashed grey;">';
								$height = round($w*$ratio);
								
								switch($w) {
									case 138:
										echo '<a class="stories" href="#">';
										echo '<div class="'.$css_class.'" style="padding:0;">';
										if(isset($img)) {
											echo '<img src="'.$img.'" class="fluid" alt="'.$caption.'" />';
										}
										echo '<div class="'.$css_class.'" style="border:0;">';
										if($title == 0) {

											echo '<h4 class="stories-title" >'.$title_value.'</h4>';
										}
										if($stories_last_modified == 1) {
											echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="'.$date.'">Published: '.$date.'</abbr></span></div>';
										}
										echo $story;
										echo '</div></div></a>';
									break;
									case 222:
										echo '<a class="stories" href="#">';
										echo '<div class="'.$css_class.'" style="padding:0;">';
										if(isset($img)) {
											echo '<img src="'.$img.'" class="fluid" alt="'.$caption.'" />';
										}
										echo '<div class="'.$css_class.'" style="border:0;">';
										if($title == 0) {
											echo '<h3 class="stories-title">'.$title_value.'</h3>';
										}
										if($stories_last_modified == 1) {
											echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="'.$date.'">Published: '.$date.'</abbr></span></div>';
										}
										echo $story;
										echo '</div></div></a>';
									break;
									case 306:
										echo '<a class="stories" href="#">';
										echo '<div class="'.$css_class.'" style="padding:0;">';
										if(isset($img)) {
											echo '<img src="'.$img.'" class="fluid" alt="'.$caption.'" />';
										}
										echo '<div class="'.$css_class.'" style="border:0;">';
										if($title == 0) {
											echo '<h3 class="stories-title">'.$title_value.'</h3>';
										}
										if($stories_last_modified == 1) {
											echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="'.$date.'">Published: '.$date.'</abbr></span></div>';
										}
										echo $story;
										echo '</div></div></a>';
									break;
									case 474:
										
										switch($story_wide_teaser_image) {
											case 0:
												echo '<a class="stories" href="#">';
												echo '<div class="'.$css_class.'">';
												if($title == 0) {
													echo '<h3 class="stories-title">'.$title_value.'</h3>';
												}
												if($stories_last_modified == 1) {
													echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="'.$date.'">Published: '.$date.'</abbr></span></div>';
												}
												echo $story;
												echo '</div></a>';
											break;
											case 1:
												echo '<a class="stories" href="#">';
												echo '<img src="'.$img.'" width="'.$w.'" height="'.$height.'" alt="'.$caption.'" />';
												echo '<div class="'.$css_class.'">';
												if($title == 0) {
													echo '<h3 class="stories-title">'.$title_value.'</h3>';
												}
												if($stories_last_modified == 1) {
													echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="'.$date.'">Published: '.$date.'</abbr></span></div>';
												}
												echo '<div class="stories-content">'.$story.'</div>';
												echo '</div></a>';
											break;
											case 2:
												
												// 80px
												preview_story_image_align($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=80, $teaser_image_align='right');
												// 120px
												preview_story_image_align($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=120, $teaser_image_align='right');
												// 160px
												preview_story_image_align($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=160, $teaser_image_align='right');
												// 200px
												preview_story_image_align($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=200, $teaser_image_align='right');
												
											break;
											case 3:

												// 80px
												preview_story_image_align($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=80, $teaser_image_align='left');
												// 120px
												preview_story_image_align($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=120, $teaser_image_align='left');
												// 160px
												preview_story_image_align($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=160, $teaser_image_align='left');
												// 200px
												preview_story_image_align($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=200, $teaser_image_align='left');
											
											break;
											
										}
										break;
										
									case 726:
										switch($story_wide_teaser_image) {
											case 0:
												echo '<div class="'.$css_class.'">';
												if($title == 0) {
													echo '<h3 class="stories-title">'.$title_value.'</h3>';
												}
												if($stories_last_modified == 1) {
													echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="'.$date.'">Published: '.$date.'</abbr></span></div>';
												}
												echo $story;
												echo '</div>';											
											break;
											case 1:
												echo '<img src="'.$img.'" width="'.$w.'" height="'.$height.'" alt="'.$caption.'" />';
												echo '<div class="'.$css_class.'">';
												if($title == 0) {
													echo '<h2 class="stories-title">'.$title_value.'</h2>';
												}
												if($stories_last_modified == 1) {
													echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="'.$date.'">Published: '.$date.'</abbr></span></div>';
												}
												echo '<div class="stories-content">'.$story.'</div>';
												echo '</div></a>';
											break;
											case 2:

												// 80px
												preview_story_image_align_big($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=80, $teaser_image_align='right');
												// 120px
												preview_story_image_align_big($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=120, $teaser_image_align='right');
												// 160px
												preview_story_image_align_big($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=160, $teaser_image_align='right');
												// 200px
												preview_story_image_align_big($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=200, $teaser_image_align='right');
												
											break;
											case 3:

												// 80px
												preview_story_image_align_big($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=80, $teaser_image_align='left');
												// 120px
												preview_story_image_align_big($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=120, $teaser_image_align='left');
												// 160px
												preview_story_image_align_big($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=160, $teaser_image_align='left');
												// 200px
												preview_story_image_align_big($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story, $teaser_image_width=200, $teaser_image_align='left');
												
											break;
											
										}
									break;
								}
								
							echo '</div></div>';
						}


						function preview_story_image_align($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story_wide, $teaser_image_width, $teaser_image_align) {
							echo '<a class="stories" href="#">';
							echo '<div class="'.$css_class.'">';
							if($title == 0) {
								echo '<h3 class="stories-title">'.$title_value.'</h3>';
							}
							if($stories_last_modified == 1) {
								echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="'.$date.'">Published: '.$date.'</abbr></span></div>';
							}							
							$teaser_image_margins = $teaser_image_align == 'left' ? '0 20px 20px 0;' : '0 0 20px 20px;';
							echo '<div class="stories-content"><img src="'.$img.'"  style="width:'.$teaser_image_width.'px;float:'.$teaser_image_align.';margin:'.$teaser_image_margins.'" alt="'.$caption.'" />'.$story_wide;
							echo '</div>';
							echo '<div style="clear:both"></div>';
							echo '</div></a>';
						}

						function preview_story_image_align_big($css_class, $title, $title_value, $stories_last_modified, $date, $img, $caption, $story_wide, $teaser_image_width, $teaser_image_align) {
							$teaser_image_margins = $teaser_image_align == 'left' ? '0 20px 20px 0;' : '0 0 20px 20px;';
							echo '<div class="'.$css_class.'">';
							echo '<div style="width:'.$teaser_image_width.'px;float:'.$teaser_image_align.';margin:'.$teaser_image_margins.'"><img src="'.$img.'" class="fluid" alt="'.$caption.'" /></div>';
							if($title == 0) {
								echo '<h3 class="stories-title">'.$title_value.'</h3>';
							}
							if($stories_last_modified == 1) {
								echo '<div class="stories-meta"><span class="stories-meta"><abbr class="timeago" title="'.$date.'">Published: '.$date.'</abbr></span></div>';
							}
							echo '<div class="stories-content">'.$story_wide.'</div>';
							echo '<div style="clear:both"></div>';
							echo '</div></a>';

						}

						
						preview_story(138, $img, $pages_id, $ratio, $title, $title_value, $css_class, $story, $story_wide_teaser_image, $caption, $date, $stories_last_modified=0);
						preview_story(222, $img, $pages_id, $ratio, $title, $title_value, $css_class, $story, $story_wide_teaser_image, $caption, $date, $stories_last_modified=0);
						preview_story(306, $img, $pages_id, $ratio, $title, $title_value, $css_class, $story, $story_wide_teaser_image, $caption, $date, $stories_last_modified=0);
						preview_story(474, $img, $pages_id, $ratio, $title, $title_value, $css_class, $story, $story_wide_teaser_image, $caption, $date, $stories_last_modified=0);
						preview_story(726, $img, $pages_id, $ratio, $title, $title_value, $css_class, $story, $story_wide_teaser_image, $caption, $date, $stories_last_modified=0);
						echo '<div style="clear:both"></div>';
					}
					
				break;


				case 'header_images';

					$dir = '/content/uploads/header';
									
					if (is_dir(CMS_ABSPATH . '/'. $dir)) {

						if ($dh = opendir(CMS_ABSPATH .'/'. $dir)) {
							$images_ext = array('jpg','jpeg','gif','png');

							while (($file = readdir($dh)) !== false) {
								if (!is_dir(CMS_ABSPATH .'/'. $dir.'/'.$file)) {
								
									$ext = pathinfo($dir.'/'.$file, PATHINFO_EXTENSION);
									if(in_array($ext, $images_ext)) {
										echo '<div class="code" style="position:relative"><img alt="'.$file.'" src="../content/uploads/header/'. $file .'" data-filename="'.$file.'" width="150px" style="margin-bottom:10px;" /><input type="checkbox" class="image_mark" data-file="'.$file.'" style="position:absolute;top:2px;left:2px;transform:scale(2);"></div>';
									}
								}
							}
							closedir($dh);
						}
					}
					

				break;				
				
				
				
				case 'save_site_selections':

					$selections = $_POST['selections'];
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

					$result = $pages->setPagesSelections($pages_id, $selections, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'selections', $users_id, $_SESSION['token'], $utc_modified);
					}
					
				break;


				case 'stories_new':
					// validate stories_id
					$stories_id = filter_input(INPUT_POST, 'stories_id', FILTER_VALIDATE_INT) ? $_POST['stories_id'] : null;					
					$container = filter_var(trim($_POST['container']), FILTER_SANITIZE_STRING);					
					$sort_id = 1;					
					
					// insert story and return lastInsertId
					if(isset($stories_id)) {
						$pages_stories_id = $pages->updatePagesStories($pages_id, $stories_id, $container, $sort_id);
					}
					
					if(isset($pages_stories_id)) {
						// get story title 
						$row = $pages->getPagesStory($pages_stories_id);						
						$story_title = (strlen($row['title']) > 0) ? $row['title'] : "Added story...";
						// print story
						get_stories_box($pages_stories_id, $story_title, "");
					}

				break;

				case 'stories_save':

					$container = filter_var(trim($_POST['id']), FILTER_SANITIZE_STRING);
					// counter
					$sort_id = 1;
					foreach($_POST['result'] as $key => $value) {
						// delete stories if not in use (side column)
						$pages->updatePagesStoriesLayout($container, $sort_id, $value);
						//echo is_numeric($pages_id);
						//echo $value;
						//echo '<br />';
						//echo $sort_id;
						$sort_id++;
					}
				
				break;


				case 'stories_delete':

					$container = filter_var(trim($_POST['id']), FILTER_SANITIZE_STRING);
					// counter
					$sort_id = 1;
					foreach($_POST['result'] as $key => $value) {
						// delete stories
						if($container == "pending_stories") {
							$pages->deletePagesStories($value);
						} else {
							// save other stories
							$pages->updatePagesStoriesLayout($container, $sort_id, $value);
							echo $value;
							echo '<br />';
							echo $sort_id;
						}
						$sort_id++;
					}

					echo 'stories deleted: '. date('H:i:s');
					
				break;


				case 'stories_change_cols':

					$stories_selected = filter_input(INPUT_POST, 'stories_selected', FILTER_VALIDATE_INT);
					$stories_columns = filter_input(INPUT_POST, 'stories_columns', FILTER_VALIDATE_INT);
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
					$result = $pages->updatePagesStoriesTemplate($pages_id, $stories_selected, $stories_columns, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', 'stories column', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
						echo 'saved template: '. date('H:i:s');
					}
					
					
				break;

				case 'get_stories_promoted':
					//echo 'Aloha';	

					$dynamic_content = $_POST['dynamicContent'];
					$stories_filter = filter_input(INPUT_POST, 'dynamicContentFilter', FILTER_SANITIZE_STRING) ? $_POST['dynamicContentFilter'] : '';
					$limit = filter_input(INPUT_POST, 'dynamicContentLimit', FILTER_VALIDATE_INT) ? $_POST['dynamicContentLimit'] : 0;
					//echo "$stories_filter: ". $stories_filter;
					//echo "$limit:" . $limit;
					$rows_promoted = $pages->getPagesStoryContentPublishPromoted($stories_filter, $limit);
					if($rows_promoted) {
						echo json_encode($rows_promoted);
					}			
					
				break;
				
				case 'get_dynamic_stories':
				//echo 'Aloha';	

				$dynamic_content = $_POST['dynamicContent'];
				$stories_filter = filter_input(INPUT_POST, 'dynamicContentFilter', FILTER_SANITIZE_STRING) ? $_POST['dynamicContentFilter'] : '';
				$limit = filter_input(INPUT_POST, 'dynamicContentLimit', FILTER_VALIDATE_INT) ? $_POST['dynamicContentLimit'] : 0;
				//echo "$stories_filter: ". $stories_filter;
				//echo "$limit:" . $limit;
				$rows = array();
				switch ($dynamic_content) {
					case "stories-promoted":
						$rows = $pages->getPagesStoryContentPublishPromoted($stories_filter, $limit);
					break;
					case "stories-child":
						$rows = $pages->getPagesChildren($pages_id);
					break;
					case "stories-event":
						$date = date('Y-m-d');
						$period = "next";						 
						$rows = $pages->getPagesStoryContentPublishEvent($stories_filter, $date, $period);
					break;
					
				}
				
				if($rows) {
					echo json_encode($rows);
				}
		
				
				break;
			

				case 'save_grid':

					$grid_active = filter_input(INPUT_POST, 'grid_active', FILTER_VALIDATE_INT) ? $_POST['grid_active'] : 0;
					$grid_area = filter_input(INPUT_POST, 'grid_area', FILTER_VALIDATE_INT) ? $_POST['grid_area'] : 0;
					$grid_cell_template = filter_input(INPUT_POST, 'grid_cell_template', FILTER_VALIDATE_INT) ? $_POST['grid_cell_template'] : 0;
					$grid_cell_image_height = filter_input(INPUT_POST, 'grid_cell_image_height', FILTER_VALIDATE_INT) ? $_POST['grid_cell_image_height'] : 0;
					$grid_custom_classes = filter_var(trim($_POST['grid_custom_classes']), FILTER_SANITIZE_STRING);
					$grid_content = trim($_POST['grid_content']);
					$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

					$result = $pages->setPagesGrid($pages_id, $grid_active, $grid_area, $grid_custom_classes, $grid_content, $grid_cell_template, $grid_cell_image_height, $utc_modified);
					if($result) {
						$history = new History();
						$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('grid', $grid_active), $users_id, $_SESSION['token'], $utc_modified);
					}
					echo reply($result);
					
				
				break;




				case 'pages_rights_add_users':
					
					// get users_id from POST users_meta
					// users_meta contains: first_name last_name, email
					// we only need email to get users_id
					// use explode to split
					$pieces = explode(",", $_POST['users_meta']);
					if(is_array($pieces)) {
						$i = count($pieces);
						$email = filter_var(trim($pieces[$i-1]), FILTER_SANITIZE_STRING);	
						$users = new Users();
						$result = $users->getUsersEmail($email);
						if($result) {
							
							$rights = new PagesRights();
							$users_id = intval($result['users_id']);
							// check existing post before set new...
							$exists = $rights->getPagesUsersRights($pages_id, $users_id);
							
							if(!$exists) {
								// insert into pages_rights
								$row = $rights->setPagesUsersRightsNew($pages_id, $users_id);
								echo $row;
								if($row) {
									//echo 'saved';
								}
							}
						}
					}
				break;
					

				case 'pages_rights_add_groups':
					
					// get groups_id from POST groups_meta
					// groups_meta contains: title
					// use explode to split
					$pieces = explode(",", $_POST['groups_meta']);
					if(is_array($pieces)) {
						$i = count($pieces);
						$groups_id = filter_var(trim($pieces[$i-1]), FILTER_SANITIZE_STRING);								
						$groups_id = intval($groups_id);
						$rights = new PagesRights();

						// check existing post before set new...
						$exists = $rights->getPagesGroupsRightsExists($pages_id, $groups_id);
						
						if(!$exists) {
							// insert into pages_rights
							$row = $rights->setPagesGroupsRightsNew($pages_id, $groups_id);
							echo $row;
							if($row) {
								//echo 'saved';
							}
						}
						
					}
				break;

					
				case 'pages_rights_delete':
					$pages_rights_id = $_POST['pages_rights_id'];
					$rights = new PagesRights();
					$row = $rights->setPagesUsersRightsDelete($pages_rights_id);
				break;


				case 'pages_rights_save':
					// this can be made less database intensive... fix arrays one sunny day
					// pages_rights_id
					$r_id = $_POST['r_id'];
					
					$r_read = $_POST['r_read'];
					$r_edit = $_POST['r_edit'];
					$r_create = $_POST['r_create'];
					
					// check if we have rights to set
					//$r_id = explode(",",$r_id);
					
					if(is_array($r_id)) {
						$rights = new PagesRights();
						
						// set read rights to true
						//$r_read = explode(",",$r_read);
						if(is_array($r_read)) {
							$r = 'rights_read';
							$value = 1;
							foreach($r_read as $pages_rights_id) {
								$pages_rights_id = intval($pages_rights_id);
								$rights->setPagesUsersRightsUpdate($pages_rights_id, $r, $value);
							}
						}
						
						// set read rights to false
						if(is_array($r_read)) {
							$r_diff = array_diff($r_id, $r_read);
							if(is_array($r_diff)) {
								$r = 'rights_read';
								$value = 0;
								foreach($r_diff as $pages_rights_id) {
									$pages_rights_id = intval($pages_rights_id);
									$rights->setPagesUsersRightsUpdate($pages_rights_id, $r, $value);
								}
							}
						}
						
						// set edit rights to true
						//$r_edit = explode(",",$r_edit);
						if(is_array($r_edit)) {
							$r = 'rights_edit';
							$value = 1;
							foreach($r_edit as $pages_rights_id) {
								$pages_rights_id = intval($pages_rights_id);
								$rights->setPagesUsersRightsUpdate($pages_rights_id, $r, $value);
							}
						}
						
						// set edit rights to false
						if(is_array($r_edit)) {
							$r_diff = array_diff($r_id, $r_edit);
							if(is_array($r_diff)) {
								$r = 'rights_edit';
								$value = 0;
								foreach($r_diff as $pages_rights_id) {
									$pages_rights_id = intval($pages_rights_id);
									$rights->setPagesUsersRightsUpdate($pages_rights_id, $r, $value);
								}
							}
						}
						
						// set create rights to true
						//$r_create = explode(",",$r_create);
						if(is_array($r_create)) {
							$r = 'rights_create';
							$value = 1;
							foreach($r_create as $pages_rights_id) {
								$pages_rights_id = intval($pages_rights_id);
								$rights->setPagesUsersRightsUpdate($pages_rights_id, $r, $value);
							}
						}
						
						// set create rights to false
						if(is_array($r_create)) {
							$r_diff = array_diff($r_id, $r_create);
							if(is_array($r_diff)) {
								$r = 'rights_create';
								$value = 0;
								foreach($r_diff as $pages_rights_id) {
									$pages_rights_id = intval($pages_rights_id);
									$rights->setPagesUsersRightsUpdate($pages_rights_id, $r, $value);
								}
							}
						}
						
						echo 'saved';
					}
					
					
				break;


				case 'save_widgets':

					$w = new Widgets();
					$w_class = $w->getWidgetsClass($_POST['widgets_id']);
					$keys = $w->wform_keys($w_class);
					// validate against regexp
					$keys_validate = $w->wform_keys_validate($w_class);
					// new action array
					$act = array();
					foreach($_POST as $key => $value) {
						if(in_array($key, $keys)) {							
							foreach($keys_validate as $key2 => $validate_regexp) {
								if($key==$key2) {
									$act[$key] = isValidString($value, $validate_regexp) ? $value : "";
								}
							}
						}
					}

					$widgets_action = json_encode($act);
					$pages_widgets_id = $_POST['pages_widgets_id'];
					
					$widgets_header = filter_var(trim($_POST['widgets_header']), FILTER_SANITIZE_STRING);
					$widgets_footer = filter_var(trim($_POST['widgets_footer']), FILTER_SANITIZE_STRING);

					if(isset($pages_widgets_id)) {
						$pw = new PagesWidgets();
						$result = $pw->updatePagesWidgets($pages_widgets_id, $widgets_action, $widgets_header, $widgets_footer);

						if($result) {
							$history = new History();
							$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
							$history->setHistory($pages_widgets_id, 'pages_widgets_id', 'UPDATE', 'widget', $_SESSION['users_id'], $_SESSION['token'], $utc_modified);
						}

						echo '';
					}
				
				break;

				case 'widgets_delete':

					$column = (isset($_POST['column'])) ? filter_var(trim($_POST['column']), FILTER_SANITIZE_STRING) : null;
					
					if(isset($column)) {
						$sort_id = 1;
						if($_POST['result']) {
							foreach($_POST['result'] as $key => $value) {

								if($column == "pending_widgets") {
									$pages->deletePagesWidgets($value);
								} else {
								}
								$sort_id++;
							}
						}
					}
				
					echo 'widgets deleted: '. date('H:i:s');

				break;
	
				case 'widgets_delete_one':

					$pages_widgets_id = (isset($_POST['pages_widgets_id'])) ? $_POST['pages_widgets_id'] : null;
					
					if(isset($pages_widgets_id)) {
						$result = $pages->deletePagesWidgets($pages_widgets_id);
						
						if($result) {
							$history = new History();
							$history->setHistory($pages_id, 'pages_id', 'UPDATE', describe('delete widget', $pages_widgets_id), $_SESSION['users_id'], $_SESSION['token'], utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s'));
						}						
					}

					echo $pages_widgets_id;
				
				break;	
					
				case 'widgets_area':
					// validate pages_widgets_id
					$pages_widgets_id = filter_input(INPUT_POST, 'pages_widgets_id', FILTER_VALIDATE_INT) ? $_POST['pages_widgets_id'] : null;
					$widgets_area_target = filter_var(trim($_POST['widgets_area_target']), FILTER_SANITIZE_STRING);					
					$sort_id = 1;
					
					if(isset($pages_widgets_id)) {

						$w = new PagesWidgets();
						$w->updatePagesWidgetsArea($pages_widgets_id, $widgets_area_target);
						// prevent duplicates...
						$row = $w->viewPagesWidgets($pages_widgets_id);
						$title = $row['widgets_class'];
						get_widgets_box($pages_widgets_id, $title, $header='', $footer='');
						
					}
					
				break;

				case 'widgets_save':

					$area = filter_var(trim($_POST['id']), FILTER_SANITIZE_STRING);
					$position = 1;
					foreach($_POST['result'] as $key => $value) {
						$w = new PagesWidgets();
						$w->updatePagesWidgetsLayout($pages_id, $area, $position, $value);
						echo $value;
						echo '<br />';
						echo $position;
						$position++;
					}

					echo 'widgets layout saved: '. date('H:i:s');
					
				break;

				

			}
		}
		
		
		if ($action == 'save_pages_images_meta') { 
			
			$pages_images_id = filter_var(trim($_POST['pages_images_id']), FILTER_VALIDATE_INT);
			$caption = filter_var(trim($_POST['caption']), FILTER_SANITIZE_STRING);
			$alt = filter_var(trim($_POST['alt']), FILTER_SANITIZE_STRING);
			$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
			$creator = filter_var(trim($_POST['creator']), FILTER_SANITIZE_STRING);
			$copyright = filter_var(trim($_POST['copyright']), FILTER_SANITIZE_STRING);
			$tag = $_POST['tag'];
			$promote = filter_input(INPUT_POST, 'promote', FILTER_VALIDATE_INT) ? $_POST['promote'] : 0;

			$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
			
			echo "pages_images_id: " . $pages_images_id;
			echo "caption: " . $caption;
			echo "alt: ". $alt;
			echo "title: " . $title;
			echo "creator: ". $creator;
			echo "copyright: " . $copyright;
			echo "tag: ". $tag;
			echo "promote: ". $promote;
			$result = $pages->updatePagesImagesMeta($pages_images_id, $caption, $alt, $title, $creator, $copyright, $tag, $promote, $utc_modified);
			if($result) {
				$history = new History();
				$history->setHistory($pages_images_id, 'pages_images_id', 'UPDATE', describe('image meta', $pages_images_id), $users_id, $_SESSION['token'], $utc_modified);
			}
			
		}

		
		// create new pages
		if ($action == 'pages_add_toplevel_page') { 
			
			// default
			$parent_id = 0;
			$parent = 0;
			$position = 10;
			$category_position = 99;
			$access= 0;
			$status = 1;
			$template = is_numeric($_SESSION['site_template_default']) ? $_SESSION['site_template_default'] : 0;
			$title = filter_var(trim($_POST['title_toplevel_page']), FILTER_SANITIZE_STRING);
			$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
			
			$lastInsertId = $pages->setPagesAddToplevelPage($title, $parent_id, $parent, $position, $category_position, $access, $status, $template, $utc_modified);
			
			// create folder
			if (!is_dir(CMS_ABSPATH."/content/uploads/pages/".$lastInsertId)) {
				mkdir(CMS_ABSPATH."/content/uploads/pages/".$lastInsertId, 0777);
			}			
			
			// set users rights - read, edit, create
			$rights = new PagesRights();
			// insert into pages_rights, skip check existing posts since its just created
			$result = $rights->setPagesUsersRightsNewReadEditCreate($lastInsertId, $users_id);
			
			$history = new History();
			$history->setHistory($lastInsertId, 'pages_id', 'INSERT', 'new page', $users_id, $_SESSION['token'], $utc_modified);	
			echo $lastInsertId;
		}
				
		if ($action == 'pages_add_child_page') { 
		
			if ($parent_id = filter_input(INPUT_POST, 'pages_parent_id', FILTER_VALIDATE_INT)) { 
			
				// default
				$template = is_numeric($_SESSION['site_template_default']) ? $_SESSION['site_template_default'] : 0;
				$position = 10;
				$access = 0;
				$category_position = 99;
				$status = 1;
				$parent = 0;				
				$title = filter_var(trim($_POST['title_child_page']), FILTER_SANITIZE_STRING);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

				// get parent page common settings as template
				$meta_additional = $meta_robots = $tag = $stories_filter = $selections = null;
				$header_caption_show = $template = $stories_columns = 0;
				$header_image = $header_caption = json_encode(array());

				$r = $pages->getPagesAsTemplate($parent_id);
				if($r) {

					$meta_additional = $r['meta_additional'];
					$meta_robots = $r['meta_robots'];
					$tag = $r['tag'];
					$header_image = $r['header_image'];
					$header_caption = $r['header_caption'];
					$header_caption_show = $r['header_caption_show'];					
					$template = $r['template'];
					$stories_columns = $r['stories_columns'];
					$stories_filter = $r['stories_filter'];
					$selections = $r['selections'];
					
					$lastInsertId = $pages->setPagesAddChildPage($title, $parent_id, $parent, $position, $category_position, $access, $status, $utc_modified, $meta_additional, $meta_robots, $tag, $stories_filter, $selections, $header_image, $header_caption, $header_caption_show, $template, $stories_columns);
					
					if (!is_dir(CMS_ABSPATH."/content/uploads/pages/".$lastInsertId)) {
						mkdir(CMS_ABSPATH."/content/uploads/pages/".$lastInsertId, 0777);
					}
					
					$parent = 1;
					$pages_id = $parent_id;
					$pages->updatePagesIsParent($pages_id, $parent);
					$rights = new PagesRights();
					$result = $rights->setPagesUsersRightsNewReadEditCreate($lastInsertId, $users_id);
					
					if($result) {
						$history = new History();
						$history->setHistory($lastInsertId, 'pages_id', 'INSERT', 'new page', $users_id, $_SESSION['token'], $utc_modified);
					}
					
					echo $lastInsertId;	
				}
			}
		}

		
		if ($action == 'pages_category_add') { 
			
			$category = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
			$position = filter_input(INPUT_POST, 'position', FILTER_VALIDATE_INT);
			$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
			$pages_category = new PagesCategories();

			// check existing tags
			$result = $pages_category->getPagesCategoriesSearch($category);
			if($result) { 
				echo 'exists';
				die();
			}

			$lastInsertId = $pages_category->setPagesCategoriesNew($category, $position, $utc_created);
			if($lastInsertId) {
				echo $lastInsertId;
				$history = new History();
				$history->setHistory($lastInsertId, 'pages_categories_id', 'INSERT', $category, $users_id, $_SESSION['token'], $utc_created);
			}
		}
		
		if ($action == 'pages_category_save') { 
			
			$pages_categories_id = filter_input(INPUT_POST, 'pages_categories_id', FILTER_VALIDATE_INT);
			$position = filter_input(INPUT_POST, 'position', FILTER_VALIDATE_INT);
			$category = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
			$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
			$pages_category = new PagesCategories();
			

			$result = $pages_category->setPagesCategoriesUpdate($pages_categories_id, $category, $position, $utc_modified);
			if ($result) {
				$history = new History();
				$history->setHistory($pages_categories_id, 'pages_categories_id', 'UPDATE', $pages_categories_id, $users_id, $_SESSION['token'], $utc_modified);
			}
		}

		if ($action == 'pages_category_delete') { 
			
			$pages_categories_id = filter_input(INPUT_POST, 'pages_categories_id', FILTER_VALIDATE_INT);
			$pages_category = new PagesCategories();

			$result = $pages_category->setPagesCategoriesDelete($pages_categories_id);
			if ($result) {
				echo "ok";
				$history = new History();
				$history->setHistory($pages_categories_id, 'pages_categories_id', 'DELETE', $pages_categories_id, $users_id, $_SESSION['token'], $utc_created);
			}
		}


		if ($action == 'pages_add_tag') { 
			
			$tag = filter_var(trim($_POST['tag']), FILTER_SANITIZE_STRING);
			$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
			$tags = new Tags();

			// check existing tags
			$result = $tags->getTagsSearch($tag);
			if($result) { 
				echo 'exists';
				die();
			}

			$lastInsertId = $tags->setTagsNew($tag, $active=1, $utc_created);
			if($lastInsertId) {
				echo $lastInsertId;
				$history = new History();
				$history->setHistory($lastInsertId, 'tags_id', 'INSERT', $tag, $users_id, $_SESSION['token'], $utc_created);
			}
		}

		if ($action == 'pages_delete_tag') { 

			$tags_id = filter_input(INPUT_POST, 'tags_id', FILTER_VALIDATE_INT) ? $_POST['tags_id'] : 0;
			$tags = new Tags();
			$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

			$result = $tags->setTagsDelete($tags_id);
			if($result) {
				$history = new History();
				$history->setHistory($tags_id, 'tags_id', 'DELETE', $tags_id, $users_id, $_SESSION['token'], $utc_created);
			}
		}

		if ($action == 'selections_new') { 
	
			$name = $_POST['name'];			
			$selections = new Selections();
			$lastInsertId = $selections->setSelectionsNew($name);
			
			if($lastInsertId) {
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$history = new History();
				$history->setHistory($lastInsertId, 'pages_selections_id', 'INSERT', describe('selection', $name), $users_id, $_SESSION['token'], $utc_modified);							
			}
			
			echo $lastInsertId;
		}
		
		if ($action == 'selections_delete') { 
	
			$pages_selections_id = $_POST['pages_selections_id'];			
			$selections = new Selections();
			// use class
			$result = $selections->setSelectionsDelete($pages_selections_id);

			if($result) {
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$history = new History();
				$history->setHistory($pages_selections_id, 'pages_selections_id', 'DELETE', 'selection', $users_id, $_SESSION['token'], $utc_modified);							
			}
					
		}

		if ($action == 'selections_save') { 
	
			$pages_selections_id = $_POST['pages_selections_id'];
			$active = $_POST['active'];
			$name = $_POST['name'];
			$description = $_POST['description'];
			$area = $_POST['area'];
			$external_js = filter_input(INPUT_POST,'external_js',FILTER_SANITIZE_STRING);
			$external_css = filter_input(INPUT_POST,'external_css',FILTER_SANITIZE_STRING);
			$content_html = $_POST['content_html'];
			$content_code = $_POST['content_code'];
			$grid_content = trim($_POST['grid_content']);
			$grid_cell_template = filter_input(INPUT_POST, 'grid_cell_template', FILTER_VALIDATE_INT) ? $_POST['grid_cell_template'] : 0;
			$grid_custom_classes = $_POST['grid_custom_classes'];
			$grid_cell_image_height = filter_input(INPUT_POST, 'grid_cell_image_height', FILTER_VALIDATE_INT);
			$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');

			$selections = new Selections();
			$result = $selections->setSelections($pages_selections_id, $active, $name, $description, $area, $content_html, $content_code, $external_js, $external_css, $grid_content, $grid_cell_template, $grid_custom_classes, $grid_cell_image_height, $utc_modified);

			if($result) {
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$history = new History();
				$history->setHistory($pages_selections_id, 'pages_selections_id', 'UPDATE', describe('name', $name), $users_id, $_SESSION['token'], $utc_modified);
			}
			
			return $result;
		}
		
		
		if ($action == 'calendar_categories') { 
	
			$calendars = new Calendar();
			$result = $calendars->getCalendarCategoriesSelect();
			
			if(!$result) {
				die;
			} else {				
				echo '<select id="calendar_categories_id">';
				foreach($result as $category) {
					echo '<option value="'.$category['calendar_categories_id'].'">'.$category['category'].'</option>';
				}
				echo '</option>';			
			}			
		}
		
		if ($action == 'calendar_views') { 
	
			$calendars = new Calendar();
			$result = $calendars->getCalendarViewsSelect();
			
			if(!$result) {
				die;
			} else {				
				echo '<select id="calendar_views_id">';
				foreach($result as $category) {
					echo '<option value="'.$category['calendar_views_id'].'">'.$category['name'].'</option>';
				}
				echo '</option>';			
			}			
		}
		
		
		if ($action == "pages_bulk_action") {
			if (isset($_POST['pages_ids'])) {
				$pages_ids = filter_var_array($_POST['pages_ids'], FILTER_VALIDATE_INT);					
				$datetime_start = isValidDateTime($_POST["datetime_start"]) ? date("Y-m-d H:i:s", strtotime($_POST["datetime_start"])) : null; 
				$datetime_end = isValidDateTime($_POST["datetime_end"]) ? date("Y-m-d H:i:s", strtotime($_POST["datetime_end"])) : null;
				$access = filter_input(INPUT_POST, 'access', FILTER_VALIDATE_INT) ? $_POST['access'] : 0;
				$status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT) ? $_POST['status'] : 0;
				
				if($pages_ids) {
					
					$i_updated = 0;
					foreach($pages_ids as $pages_id){
						$r = $pages->setPagesBulkStatus($pages_id, $status, $access, $datetime_start, $datetime_end);
						if($r) {
							$i_updated++;
						}
					}					
					if($i_updated > 0 ) {
						echo $i_updated;							
					}
				}
			}		
		}
		
		if ($action == "btn_pages_bulk_site_header") {
			
			if (isset($_POST['pages_ids'])) {
				$pages_ids = filter_var_array($_POST['pages_ids'], FILTER_VALIDATE_INT);					
				$header_image = filter_input(INPUT_POST,'header_image',FILTER_SANITIZE_STRING);
				
				if($pages_ids) {
					$i_updated = 0;
					foreach($pages_ids as $pages_id){
						$r = $pages->setPagesBulkHeaderImage($pages_id, $header_image);
						if($r) {
							$i_updated++;
						}
					}
					if($i_updated > 0 ) {
						echo $i_updated . " pages updated";
					}
				}
			}		
		}
		
	
	}
}

?>