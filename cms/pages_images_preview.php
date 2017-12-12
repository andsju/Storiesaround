<?php

// include core 
//--------------------------------------------------
require_once 'includes/inc.core.php';

// include session access
//--------------------------------------------------
require_once 'includes/inc.session_access.php';

if(!get_role_CMS('contributor') == 1) {
	die;
}

// css files
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css' );

// css files... add css jquery-ui theme
if(isset($_SESSION['site_ui_theme'])) {
	$ui_theme = '/cms/libraries/jquery-ui/theme/'.$_SESSION['site_ui_theme'].'/jquery-ui.css';
	if(file_exists(CMS_ABSPATH .$ui_theme)) {
		if (($key = array_search(CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', $css_files)) !== false) {
			unset($css_files[$key]);
		}
		array_push($css_files, CMS_DIR . $ui_theme);
	}
}
	
// javascript files
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js',
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js',	
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/js/functions.js',
	//CMS_DIR.'/cms/libraries/js/pages_calendar.js'
	//CMS_DIR.'/cms/libraries/tinymce/plugins/moxiemanager/js/moxman.loader.min.js'
);

// include header
$meta_keywords = $meta_description = $meta_robots = $meta_additional = $meta_author = null;
$page_title = 'Image viewer | editor';
$body_style = "width:1190px;max-width:100% !important;margin:0 auto;background:#333;color:#fff";
include_once 'includes/inc.header_minimal.php';


?>

<script>
	
	$(document).ready(function() {
		
		$('button.version').click(function(){
			//console.log(this.id);
			var p = $("#image_path").val();
			//console.log(p);
			var img = p+this.id;
			var t = getTime();
			$('#version2').html('<div>Path: <span class="code" style="padding:10px;border:1px solid #E8E8E8;margin:5px;">'+img+'</span></div><br /><img src="'+img+'?t='+t+'" />');
			$('#version2').show();
			
		});

		
		
		$('#btn_delete').click(function(event){


			event.preventDefault();
			var action = "delete_image";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_id = $("#pages_id").val();
			var images_filename = $("#images_filename").val();
			console.log(token);
			console.log(users_id);
			console.log(pages_id);
			console.log(images_filename);
			
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_image').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_image').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_id=" + pages_id + "&image=" + images_filename,
				success: function(message){
					//ajaxReply(message,'#ajax_status_image');
					console.log(message);
					//$('#li_'+pages_images_id).remove();
					//$('#'+id).closest('li').fadeOut(500, function() { $(this).closest('li').remove(); });
					
					window.location.href = window.location.toString().indexOf("#") != -1 ? window.location.href : window.location.href + '#add_content';
					location.reload(true);
					
				},
			});
			
		});
		

		$("#dialog_delete_image").dialog({
			autoOpen: false,
			modal: true
		});		

		$('#btn_save_image_meta').click(function(event){
			event.preventDefault();
			var action = "save_pages_images_meta";
			var token = $("#token").val();
			var users_id = $("#users_id").val();
			var pages_images_id = $("#pages_images_id").val();
			var caption = $("#caption").val();
			var alt = $("#alt").val();
			var title = $("#title").val();
			var creator = $("#creator").val();
			var copyright = $("#copyright").val();
			var optionTagTexts = [];
			$("ul#tags li").each(function() { optionTagTexts.push($(this).text()) });
			var tag = optionTagTexts;			
			var promote = $('input:checkbox[name=promote]').is(':checked') ? 1 : 0;
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_image').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_image').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_images_id=" + pages_images_id + "&caption=" + caption + "&title=" + title + "&alt=" + alt + "&creator=" + creator + "&copyright=" + copyright + "&tag=" + tag + "&promote=" + promote,
				success: function(message){
					ajaxReply(message,'#ajax_status_image');
				},
			});
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
		
		$('#btn_add_tag').click(function(event) {
			$('#ajax_spinner_tag').show();
			setTimeout("$('#ajax_spinner_tag').hide()",700);
			var tag = $("#tag").val();
			var optionTexts = [];
			$("ul#tags li").each(function() { optionTexts.push($(this).text()) });
			if(optionTexts.indexOf(tag) == -1) {
				$('ul#tags').append('<li>'+tag+'<span class="ui-icon ui-icon-close" style="display:inline-block;"></span></li>');
			}
			$("#tag").val('');
		});
		
		$("ul#tags").delegate( "span", "click", function() {
			$(this).parent().remove();
		});			

		$('#btn_tag').click(function(event) {
			var optionTexts = [];
			$("ul#tags li").each(function() { optionTexts.push($(this).text()) });
			alert(optionTexts);			
		});
	

		$('#btn_close').click(function(event){
			event.preventDefault();
			window.close();
		});
		
		$( ".toolbar button" ).button({
		});
		$( ".toolbar_add button" ).button({
			icons: {
				secondary: "ui-icon-plus"
			},
			text: true
		});	
		$( ".toolbar_rotate_left button" ).button({
			icons: {
				secondary: "ui-icon-arrowreturnthick-1-w"
			}
		});
		$( ".toolbar_rotate_right button" ).button({
			icons: {
				secondary: "ui-icon-arrowreturnthick-1-e"
			}			
		});
		
		$("#filter").change(function(){
			$('#ratio option').eq(0).prop('selected', true);
			$('#box_image').hide();
		});
		
		
		$('#btn_image_apply_filter').click(function(event) {
			event.preventDefault();
						
			var action = "image_apply_filter";
			var token = $("#token").val();
			var pages_id = $("#pages_id").val();
			var pages_images_id = $("#pages_images_id").val();
			var filter = $("#filter").val();
			var t = getTime();
			
			if(filter.length > 0) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_image').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_image').hide()",700)},
					type: 'POST',
					url: 'pages_edit_ajax.php',
					data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&pages_images_id=" + pages_images_id + "&filter=" + filter,
					success: function(respons){
						$('#new_image_container').show();
						$('#edit_image_filter').attr("src", respons+'?t='+t).hide().fadeIn(200);
						$('#btn_save_new_image').show();
					},
				});
			}
		});

		$('#btn_save_new_image').click(function(event) {
			event.preventDefault();
			$('#new_image_container').hide();
			var action = "image_save_new";
			var token = $("#token").val();
			var pages_id = $("#pages_id").val();
			var pages_images_id = $("#pages_images_id").val();
			var t = getTime();
			// pass ratio to update db
			var ratio = $('#ratio').val();
			$.ajax({
				beforeSend: function	() { loading = $('#ajax_spinner_image').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_image').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&pages_images_id=" + pages_images_id,
				
				data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&pages_images_id=" + pages_images_id +"&ratio=" +ratio,
				success: function(respons){
					$('#edit_image').attr("src", respons+'?t='+t);
					$('#btn_save_new_image').hide();
				}
			});

		});
		
		$('.btn_image_rotate').click(function(event) {
			event.preventDefault();
			$('#manip').html('');
			var action = "image_rotate";
			var token = $("#token").val();
			var pages_id = $("#pages_id").val();
			var pages_images_id = $("#pages_images_id").val();
			var t = getTime();
			var rotate = $(this).attr('id') == 'btn_image_rotate_left' ? 90 : 270;
			console.log(rotate);
			$.ajax({
				beforeSend: function() { loading = $('#ajax_spinner_image').show()},
				complete: function(){ loading = setTimeout("$('#ajax_spinner_image').hide()",700)},
				type: 'POST',
				url: 'pages_edit_ajax.php',
				data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&pages_images_id=" + pages_images_id + "&rotate=" + rotate,
				success: function(respons){					
					$('#manip').html('<img src="'+respons+'?dummy='+t+'" />');
				},
			});
		});
		
		
		$('#box_image').css('width', '726px').css('height', '300px').css('left', '0').css('top', '0');
		$('#box_image').draggable({containment: '#container1'});
		$('#box_image').resizable({containment: '#container1', handles: {'ne': '#negrip', 'se': '#segrip', 'sw': '#swgrip', 'nw': '#nwgrip', 'n': '#ngrip', 'e': '#egrip', 's': '#sgrip', 'w': '#wgrip'}, aspectRatio: 16 / 9  });
		
		$("#ratio").change(function(){
		
			$('#btn_save_new_image').hide();
			$('#filter option').eq(0).prop('selected', true);
			
			var ratio = $(this).val();			
			var img = document.getElementById('edit_image'); 
			var w = img.width;			
			var h = Math.round(w/ratio);
			
			$('#box_image').css('top','0px').css('left','0px').css('width',+w+'px');

			switch(ratio) {
				case "4":
					$('#box_image').resizable( "destroy" ).resizable({containment: '#container1', handles: 'n, e, s, w, ne, se, sw, nw', aspectRatio: 4/1 }).css('height', +h+'px');
				break;
				case "3":
					$('#box_image').resizable( "destroy" ).resizable({containment: '#container1', handles: 'n, e, s, w, ne, se, sw, nw', aspectRatio: 3/1 }).css('height', +h+'px');
				break;
				case "2.76":
					$('#box_image').resizable( "destroy" ).resizable({containment: '#container1', handles: 'n, e, s, w, ne, se, sw, nw', aspectRatio: 2.76/1 }).css('height', +h+'px');
				break;
				case "2.35":
					$('#box_image').resizable( "destroy" ).resizable({containment: '#container1', handles: 'n, e, s, w, ne, se, sw, nw', aspectRatio: 2.35/1 }).css('height', +h+'px');
				break;
				case "2.1":
					$('#box_image').resizable( "destroy" ).resizable({containment: '#container1', handles: 'n, e, s, w, ne, se, sw, nw', aspectRatio: 21/10 }).css('height', +h+'px');
				break;
				case "1.77":
					$('#box_image').resizable( "destroy" ).resizable({containment: '#container1', handles: 'n, e, s, w, ne, se, sw, nw', aspectRatio: 16/9 }).css('height', +h+'px');
				break;
				case "1.33":
					$('#box_image').resizable( "destroy" ).resizable({containment: '#container1', handles: 'n, e, s, w, ne, se, sw, nw', aspectRatio: 4/3 }).css('height', +h+'px');
				break;
				case "1":
					$('#box_image').resizable( "destroy" ).resizable({containment: '#container1', handles: 'n, e, s, w, ne, se, sw, nw', aspectRatio: 1/1 }).css('height', +h+'px');
				break;
				default:
					$('#box_image').css('top','20px').css('left','20px').css('width','222px').css('height','222px').css('min-width','222px').css('min-height','222px');
					$('#box_image').resizable( "destroy" ).resizable({containment: '#container1',  handles: 'n, e, s, w, ne, se, sw, nw' });
				break;
				
			}
			
			$('#box_image').show();
		});		
		
		
		$('#btn_crop').click(function(event) {
			event.preventDefault();
			console.log("Apply...");
			var action = "image_crop";
			var token = $("#token").val();
			var pages_id = $("#pages_id").val();
			var pages_images_id = $("#pages_images_id").val();
			var t = getTime();
			var top = $('#box_image').position().top;
			var left = $('#box_image').position().left;
			var width = $('#box_image').width();
			var height = $('#box_image').height();
			var ratio = $('#ratio').val();
			var width_edited_image = $('#width_edited_image').val();
			
			if(ratio.length > 0) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_image').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_image').hide()",700)},
					type: 'POST',
					url: 'pages_edit_ajax.php',
					data: { 
						action: action, token: token, pages_id: pages_id, pages_images_id: pages_images_id, 
						top: top, left: left, width: width, height: height, ratio: ratio, 
						width_edited_image: width_edited_image
						},
					success: function(respons){
						$('#new_image_container').show();
						$('#edit_image_filter').attr("src", respons+'?t='+t).hide().fadeIn(200);
						$('#btn_save_new_image').show();
					},
				});
			}		
		});
		
		
	});
</script>	
<?php






// check $_GET id
$pages_id = array_key_exists('pages_id', $_GET) ? $_GET['pages_id'] : null;
if($pages_id == null) { die;}
$pages_images_id = array_key_exists('pages_images_id', $_GET) ? $_GET['pages_images_id'] : null;
//if($pages_images_id == null) { die;}


$token = array_key_exists('token', $_GET) ? $_GET['token'] : null;
$users_id = $_SESSION['users_id'];


$pages = new Pages();
$row = $pages->getPagesImagesMeta($pages_images_id);
//if(!$row) {die;}

// paths
$p = CMS_DIR.'/content/uploads/pages/'. $pages_id .'/';
$pp = CMS_ABSPATH.'/content/uploads/pages/'. $pages_id .'/';

// prefix used to apply effects / image editing
$new_filename_prefix = '__';

// initiate class Image()
$image = new Image();

// this image
if($row) {
	$img = $p . $row['filename'];
	$preview_img = $image->get_max_image($img);
	$preview_img_filename = substr($preview_img, strrpos( $preview_img, '/')+1);
	$width_edited_image = $image->image_info($pp . $preview_img_filename, 'width');
} else {
	
	$img = $preview_img = $preview_img_filename = $width_edited_image = null;
	
}

// images menu navigation
$rows = $pages->getPagesImages($pages_id);

if($rows) {
	echo '<div style="width:100%;background:#000;overflow:auto;white-space: nowrap; height:80px;color:#fff;">';
		echo '<ul id="imagerow" style="margin:0; padding:0; list-style:none;">';
		foreach($rows as $r) {
			echo '<li id="li_'.$r['pages_images_id'].'" style="float:left;">';
			echo '<a href="pages_images_preview.php?pages_images_id='.$r['pages_images_id'].'&pages_id='.$pages_id.'&token='.$token.'"><img src='.$p . $r['filename'].' style="height:30px;padding:5px;" /></a>';
			// delete tmp images while looping image click menu
			$preview_img2 = $p .'/'. $r['filename'];			
			$preview_img2 = $image->get_max_image($preview_img2);
			// filename
			$fn = substr($preview_img2, strrpos( $preview_img2, '/')+1);
			// delete tmp image - prefix '__'
			if(file_exists($pp . $new_filename_prefix . $fn)) {
				unlink($pp . $new_filename_prefix . $fn);
			}
			echo '</li>';
		}
		echo '</ul>';
	echo '</div>';
}



echo "\n".'<div class="admin-panel" style="margin:10px;">';
	
	if($img) {

		$try = $image->get_optimzed_image($img, $column_width = 1000);
		echo $try; 

	echo "\n".'<div class="wrapper clearfix" style="margin:0px;">';
		
		echo "\n".'<div style="float:left;text-align:right;">';
			echo '<h2>Edit image</h2>';
		echo "\n".'</div>';
		
		echo "\n".'<div style="float:right;text-align:right;padding-top:10px;">';
			echo '<span class="toolbar"><button id="btn_close" type="submit">Close</button></span>';
			echo '<span class="toolbar"><button id="btn_delete" type="submit">Delete</button></span>';
			echo '<span class="toolbar"><button id="btn_save_image_meta">Save</button></span><span id="ajax_spinner_image" style="display:none;"><img src="css/images/spinner.gif"></span><span id="ajax_status_image" style="display:none;"></span>';
		echo "\n".'</div>';
		
	echo "\n".'</div>';

	echo "\n".'<hr />';	
	
	
	echo "\n".'<div class="wrapper clearfix" style="margin:0px;">';

		// image size
		$size = $preview_img ? getimagesize($_SERVER['DOCUMENT_ROOT'] . $preview_img) : "";
		echo "\n".'<div class="float" style="width:60%; max-width:'.$size[0].'px;min-width:474px;">';
			
			//echo '<img src="'.$preview_img.'" style="width:100%;max-width:'.$size[0].'px;" id="edit_image" />';
			echo '<div id="container1"><img src="'.$preview_img.'?t='.date('H:m:s').'" style="width:100%;max-width:'.$size[0].'px;" id="edit_image" title="'.$row['title'].'" alt="'.$row['alt'].'" /><div id="box_image" style="display:none;"></div></div>';

			?>
			<p>
				<select id="filter" name="filter" class="select_lists">
					<option value="">[ image filters ]</option>
					<option value=""></option>
					<option value="IMG_FILTER_BRIGHTNESS_PLUS">Brightness +</option>
					<option value="IMG_FILTER_BRIGHTNESS_MINUS">Brightness -</option>
					<option value="IMG_FILTER_CONTRAST_PLUS">Contrast +</option>
					<option value="IMG_FILTER_CONTRAST_MINUS">Contrast -</option>
					<option value="IMG_FILTER_EMBOSS">Emboss</option>
					<option value="IMG_FILTER_GRAYSCALE">Greyscale</option>
					<option value="IMG_FILTER_EDGEDETECT">Highlight edges</option>
					<option value="IMG_FILTER_NEGATE">Negative</option>
					<option value="IMG_FILTER_SELECTIVE_BLUR">Selective blur</option>
					<option value="IMG_FILTER_GAUSSIAN_BLUR">Gaussian blur</option>
					<option value="IMG_FILTER_MEAN_REMOVAL">Sketchy</option>
					<option value="IMG_FILTER_SMOOTH">Smooth</option>
					<option value="IMG_FILTER_PIXELATE">Pixelate</option>
				</select>
				<span class="toolbar"><button id="btn_image_apply_filter">Apply filter</button></span>
				|
				<select id="ratio" class="select_lists">
					<option value="" selected>[ options ]</option>
					<option value=""></option>
					<option value="4">4:1</option>
					<option value="3">3:1</option>
					<option value="2.76">2.76:1</option>
					<option value="2.35">2.35:1</option>
					<option value="2.1">21:10</option>
					<option value="1.77">16:9</option>
					<option value="1.33">4:3</option>
					<option value="1">1:1</option>
					<option value="transform">free</option>
				</select>
				<span class="toolbar" title="Crop"><button id="btn_crop">Apply crop</button></span>
				
				<!--<span class="toolbar_rotate_left" title="Rotate left"><button class="btn_image_rotate" id="btn_image_rotate_left">rotate</button></span>-->
				<!--<span class="toolbar_rotate_right" title="Rotate right"><button class="btn_image_rotate" id="btn_image_rotate_right">rotate</button></span>-->
				
				
				

			</p>
				<span class="toolbar" id="btn_save_new_image" style="display:none;"><button id="btn_image_apply_filter" class="cms-ui-red">Save image</button></span>
				<div id="new_image_container" style="display:none;"><img src="" style="width:100%;max-width:'<?php echo $size[0]; ?>'px;" id="edit_image_filter" />
			
				</div>
				
			<p>
				Check image width (px):
			</p>
			<?php
			echo '<ul id="image_versions">';

			$sizes = $image->get_image_sizes();
			foreach($sizes as $size) {
				if(get_file($p, $preview_img_filename, $size)) {			
					$version = get_file_version($p, $preview_img_filename, $size);	
					
					echo '<li><span class="toolbar"><button id="'.$version.'" class="version">'.$size .'</button></span></li>';
				} else {
					echo '<li class="missing">'.$size .'</li>';
				};					
			}

			echo '</ul>';
			


			
			
			

			
		echo "\n".'</div>';
		echo "\n".'<div class="float" style="width:40%;padding:20px;">';

			echo '<p>';
				echo 'Filename: <span class="code"> '.$preview_img.'</span>';
			echo '</p>';
			echo '<p>';
				echo 'Ratio: <span class="code">'.$row['ratio'].'</span>';
			echo '</p>';

			echo '<p>';
				echo 'Dimension: <span class="code">'.$size[0]. ' x ' .$size[1].' px</span>';
			echo '</p>';
			echo '<p>';
				echo '<label for="caption">Caption</label><br />';
				echo '<input type="text" style="width:90%;" name="caption" id="caption" value="'.$row['caption'].'" />';
			echo '</p>';
			echo '<p>';
				echo '<label for="caption">Alt attribute</label><br />';
				echo '<input type="text" style="width:90%;" name="alt" id="alt" value="'.$row['alt'].'" maxlength="100" />';
			echo '</p>';
			echo '<p>';
				echo '<label for="title">Title attribute (complement ALT text - shown as tool tip)</label><br />';
				echo '<input type="text" style="width:90%;" name="title" id="title" value="'.$row['title'].'" maxlength="100" />';
			echo '</p>';
			echo '<p>';
				echo '<label for="creator">Creator</label><br />';
				echo '<input type="text" style="width:90%;" name="creator" id="creator" value="'.$row['creator'].'" />';
			echo '</p>';
			echo '<p>';
				echo '<label for="copyright">Copyright</label><br />';
				echo '<input type="text" style="width:90%;" name="copyright" id="copyright" value="'.$row['copyright'].'" />';
			echo '</p>';
			echo '<p>';
				echo '<input type="checkbox" id="promote" name="promote" value="1"';
				if($row['promote'] == 1) {
					echo 'checked';
				}
				echo '/>&nbsp;promote image';
			echo '</p>';
			echo '<p>';
				echo '<label for="tag">Tag</label><br />';
				echo '<input type="text" style="width:50%;" name="tag" id="tag" />';
				echo '<span class="toolbar_add"><button id="btn_add_tag" style="margin:0px" type="submit">Add</button></span>';
				echo '<span id="ajax_spinner_tag" style="display:none;"><img src="css/images/spinner.gif"></span>';
				echo '<span id="ajax_status_tag" style="display:none;"></span>';
			echo '</p>';
			echo '<div style="display:inline-block;">';
				echo '<ul id="tags">';
				if(isset($row['tag']) && strlen($row['tag']) >0){
					$tags = explode(",", $row['tag']);
					foreach ($tags as $tag){
						echo '<li>'.$tag.'<span class="ui-icon ui-icon-close" style="display:inline-block;"></span></li>';
					}
				}
				echo '</ul>';
			echo '</div>';

			
			$xmps = json_decode($row['xmpdata'], true);
			
			echo '<h4>Original image xmpdata</h4>';
			
			if(is_array($xmps)) {
				foreach($xmps as $key => $value) {
					echo '<b>'.$value['item'].'</b> ';				
					echo $value['value'];
					echo '<br />';
				}
			}
		echo "\n".'</div>';
	echo "\n".'</div>';

	} else {
		
		echo "Imaged deleted - select from list";
		
	}
	
	
echo "\n".'</div>';


echo "\n".'<div class="admin-panel" style="margin:10px;">';
echo '<div id="version2" style="padding:5px;margin:5px;display:none;"></div>';
echo '</div>';


echo '<input type="hidden" id="pages_images_id" name="pages_images_id" value="'.$pages_images_id.'" />';
echo '<input type="hidden" id="images_filename" name="images_filename" value="'.$row['filename'].'" />';
echo '<input type="hidden" id="width_edited_image" name="width_edited_image" value="'.$width_edited_image.'" />';
echo '<input type="hidden" id="token" name="token" value="'.$token.'" />';
echo '<input type="hidden" id="image_path" name="image_path" value="'.$p.'" />';
echo '<input type="hidden" id="users_id" name="users_id" value="'.$users_id.'" />';
echo '<input type="hidden" id="pages_id" name="pages_id" value="'.$pages_id.'" />';
?>


<div id="dialog_delete_image" title="Confirmation required">
  Delete this image?
</div>




<?php


function show_image($img, $w) {

	$filename = $img;
	
	if(is_file($_SERVER['DOCUMENT_ROOT'] . $filename)) {
		$extension = pathinfo($_SERVER['DOCUMENT_ROOT'] . $filename, PATHINFO_EXTENSION);
		$ext = strlen($extension);
		$width_ext = $ext + 4;
		$pre = substr($img, 0, - $width_ext);
		
		return $pre.$w.'.'.$extension;
		
	}
}

function get_file($path, $filename, $w) {
	if(is_file($_SERVER['DOCUMENT_ROOT'] .'/'. $path . $filename)) {
		$extension = pathinfo($_SERVER['DOCUMENT_ROOT'] .'/'. $path . $filename, PATHINFO_EXTENSION);
		$ext = strlen($extension);
		$pos_underscore = strrpos($filename, '_') + 1;
		$pos_dot = strrpos($filename, '.') + 1;
		$width_numbers =  $pos_dot - $pos_underscore;
		$width = substr($filename, $pos_underscore, $width_numbers - 1);
		$pre = substr($filename, 0, $pos_underscore);
		if(is_file($_SERVER['DOCUMENT_ROOT'] .'/'. $path . $pre . $w .'.'.$extension)) {
			return true;
		}
	}
}



function get_file_version($path, $filename, $w) {
	if(is_file($_SERVER['DOCUMENT_ROOT'] .'/'. $path . $filename)) {
		$extension = pathinfo($_SERVER['DOCUMENT_ROOT'] .'/'. $path . $filename, PATHINFO_EXTENSION);
		$ext = strlen($extension);
		$pos_underscore = strrpos($filename, '_') + 1;
		$pos_dot = strrpos($filename, '.') + 1;
		$width_numbers =  $pos_dot - $pos_underscore;
		$width = substr($filename, $pos_underscore, $width_numbers - 1);
		$pre = substr($filename, 0, $pos_underscore);
		if(is_file($_SERVER['DOCUMENT_ROOT'] .'/'. $path . $pre . $w .'.'.$extension)) {
			return $pre . $w .'.'.$extension;
		}
	}
}



?>





<?php 
// load javascript files
foreach ( $js_files as $js ) { 
	echo "\n".'<script src="'.$js.'"></script>';
}
?>


</body>
</html>