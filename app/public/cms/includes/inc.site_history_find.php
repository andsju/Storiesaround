<?php if(!defined('VALID_INCL')){header('Location: index.php'); die;} ?>
<script type="text/javascript">
	$(document).ready(function() {

		$( "#history_find" ).autocomplete({
			delay: 300,
			source: function( request, response ) {
				$.ajax({
					type: "post",
					url: "admin_edit_ajax.php",
					dataType: "json",
					data: {
						action: "history_search",
						token: $("#token").val(),
						s: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.field,
								id: item.history_id,
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				$("input#pid").val(ui.item.id)
			}
		});
		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});

	});
</script>

<?php

// include core
//--------------------------------------------------
require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if(!get_role_CMS('administrator') == 1) {die;}

$pages = new Pages();

//--------------------------------------------------
// check $_GET id
$id = array_key_exists('id', $_GET) ? $_GET['id'] : 0;


?>




<?php

	// form action url
	//$this_url = $_SERVER['PHP_SELF'] .'?tab='. $_GET['tab'];
	$this_url = $_SERVER['REQUEST_URI'];

	// sql orderby and sort
	$default_orderby = 'history_id';
	$allowed_orderby = array('history_id','field_id','field','action','description','users_id','utc_datetime');
	$default_sort = 'DESC';

	// validate
	// btn click - use only $_POST array
	if(isset($_POST['btn_search'])) {
		$field_id = filter_input(INPUT_POST, 'field_id', FILTER_VALIDATE_INT) ? $_POST['field_id'] : null;
		//$users_id = filter_input(INPUT_POST, 'users_id', FILTER_VALIDATE_INT) ? $_POST['users_id'] : null; // caused a problem on production server php version 5.2.17 - null value changed $_SESSION['users_id'].....
		$users_id = filter_input(INPUT_POST, 'users_id', FILTER_VALIDATE_INT) ? $_POST['users_id'] : $_SESSION['users_id'];
		$module = isset($_POST['module']) ? filter_var(trim($_POST['module']), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
		$action = isset($_POST['action']) ? filter_var(trim($_POST['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
	} else {
		$field_id = filter_input(INPUT_GET, 'field_id', FILTER_VALIDATE_INT) ? $_GET['field_id'] : null;
		//$users_id = filter_input(INPUT_GET, 'users_id', FILTER_VALIDATE_INT) ? $_GET['users_id'] : null; // caused a problem on production server php version 5.2.17 - null value changed $_SESSION['users_id'].....
		$users_id = filter_input(INPUT_GET, 'users_id', FILTER_VALIDATE_INT) ? $_GET['users_id'] : $_SESSION['users_id'];
		$module = isset($_GET['module']) ? filter_var(trim($_GET['module']), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
		$action = isset($_GET['action']) ? filter_var(trim($_GET['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS) : null;
	}


	?>
	


	<h4 class="admin-heading">Site history</h4>
	

	<form id="searchform" method="post" action="<?php echo $this_url; ?>">
	
		<fieldset class="search_1">
			<legend align="right">search</legend>
			<div style="float:left;padding:5px;">
				<label for="history_find">Find: </label><br />
				<input id="history_find" name="history_find" style="width:400px;" value="<?php if(isset($_REQUEST['history_find'])) {echo $_REQUEST['history_find']; } ?>"/>
				<input type="hidden" id="pid" />
			</div>
			<div style="float:left;padding:5px;">
				<label for="history">Module: </label><br />
				<select name="module" id="module" style="">
					<option value="" <?php if($module==null) { echo ' selected=selected';} ?>>(any)</option>
					<option value="site_id" <?php if($module=='site_id') { echo ' selected=selected';} ?>>Site</option>
					<option value="pages_id" <?php if($module=='pages_id') { echo ' selected=selected';} ?>>Pages</option>
					<option value="users_id" <?php if($module=='users_id') { echo ' selected=selected';} ?>>Users</option>
					<option value="groups_id" <?php if($module=='groups_id') { echo ' selected=selected';} ?>>Groups</option>
					<option value="widgets_id" <?php if($module=='widgets_id') { echo ' selected=selected';} ?>>Widgets</option>
					<option value="pages_selections_id" <?php if($module=='pages_selections_id') { echo ' selected=selected';} ?>>Selections</option>
					<option value="banners_id" <?php if($module=='banners_id') { echo ' selected=selected';} ?>>Banners</option>
					<option value="calendar_categories_id" <?php if($module=='calendar_categories_id') { echo ' selected=selected';} ?>>Calendar categories</option>
					<option value="calendar_views_id" <?php if($module=='views_id') { echo ' selected=selected';} ?>>Calendar views</option>
				</select>			
			</div>
			<div style="float:left;padding:5px;">
				<label for="history">Action: </label><br />
				<select name="action" id="action" style="">
					<option value="" <?php if($action==null) { echo ' selected=selected';} ?>>(any)</option>
					<option value="select" <?php if($action=='select') { echo ' selected=selected';} ?>>SELECT</option>
					<option value="insert" <?php if($action=='insert') { echo ' selected=selected';} ?>>INSERT</option>
					<option value="update" <?php if($action=='update') { echo ' selected=selected';} ?>>UPDATE</option>
					<option value="delete" <?php if($action=='delete') { echo ' selected=selected';} ?>>DELETE</option>
				</select>			
			</div>
			<div style="float:left;padding:15px 0 0 10px">
				<input type="submit" name="btn_search" value="Search" class="input" /></span>
			</div>
		</fieldset>	
		<fieldset class="search_1">
			<legend align="right">filter</legend>
			Filter options:
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="text" name="field_id" value="<?php echo $field_id;?>" />&nbsp;field_id
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="text" name="users_id" value="<?php echo $users_id;?>" />&nbsp;users_id
						
			<input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token">
		
		</fieldset>
	</form>

	<?php


	// sql 
	$sql_select = 'SELECT * '; 
	$sql_select .= ' FROM history ';
	
	// search in these columns
	$arr_cols = array('field', 'action', 'description');

	
	// search form
	// search words from form input text
	$arr_words = null;	
	if (isset($_REQUEST['history_find'])) {
		if (strlen($_REQUEST['history_find']) > 0) {
			$arr_words = explode(" ", filter_var($_REQUEST['history_find'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
		}
	}
	
	// handle input checkboxes (not shown)
	$bol_match_all_words = isset($_POST['checkbox_all']) ? true : true;

	$sql_select = getSQLsearch($arr_words, $arr_cols, $bol_match_all_words, $sql_select);
	
		
	if(strlen($module) > 0) {
		$module = $_REQUEST['module'];
		if(strrpos($sql_select, "WHERE") == false) {
			$sql_select .= " WHERE ";
		} else {
			$sql_select .= " AND ";
		}
		$sql_select .= " field = '$module'";
	}

	if(strlen($action) > 0) {
		if(strrpos($sql_select, "WHERE") == false) {
			$sql_select .= " WHERE ";
		} else {
			$sql_select .= " AND ";
		}
		$sql_select .= " action = '$action'";
	}
	
	if(isset($_REQUEST['field_id'])) {
		$field_id = $_REQUEST['field_id'];
		if(is_numeric($field_id)) {
			if(strrpos($sql_select, "WHERE") == false) {
				$sql_select .= " WHERE ";
			} else {
				$sql_select .= " AND ";
			}
			$sql_select .= " field_id = $field_id";
		}
	}
	
	if(isset($_REQUEST['users_id'])) {
		$users_id = $_REQUEST['users_id'];
		if(is_numeric($users_id)) {
			if(strrpos($sql_select, "WHERE") == false) {
				$sql_select .= " WHERE ";
			} else {
				$sql_select .= " AND ";
			}
			$sql_select .= " users_id = $users_id";
		}
	}
	
	if(isset($_POST['btn_search']) || isset($_GET['history_find'])) {
	
		// show result if we have a sql_select question	
		if (isset($sql_select)) {
		
			// get new sql -> ORDER BY | ASC/DESC | LIMIT
			$table = 'history';
			$sql_new = getSQLorder($default_orderby, $allowed_orderby, $default_sort, $table, $sql_select);
			
			//echo $sql_new .'<br />';

			//create an object of TablePager class -> pass sql query
			$TablePager = new TablePager($sql_new);

			$request_uri = exclude_queries(array('hits','orderby','page','sort','history_find'));
			
			//use current page
			$q = isset($_REQUEST['history_find']) ? $_REQUEST['history_find'] : null;
			$TablePager->url = $_SERVER['SCRIPT_NAME'] .'?'. $request_uri .'&history_find='. $q .'&module='. $module .'&field_id='. $field_id .'&users_id='. $users_id;

			//set number of rows, (default in TablePager class 10)
			$TablePager->rowPerPage = 20;

			//build TablePager
			$TablePager->build();

			//get paged data
			$rows = $TablePager->getPagedData();
			
			// $_SESSION['token']
			$token = $_SESSION['token'];		

			// anchor bookmark in TablePager links
			$bookmark = '#qq';

			// edit id -> MySQL table id
			$table_id = 'history_id';

			// table description in table caption
			$table_description = 'History';

			// checkbox in table 0/1
			$table_row_checkbox = 0;
			
			// edit link in table 0/1
			$table_row_edit = 0;

			// edit url link in table string
			$table_row_edit_link = 0;

			// edit url link anchor css class in table, string
			$table_row_edit_link_css_class = '';
			
			// view url link in table, string
			$table_row_view_link = 0;

			// view url link anchor css class in table, string
			$table_row_view_link_css_class = '';

			// dialog link in table 0/1
			$table_row_view = 0;

			// editable form action in 0/1, enable toggleCheckboxes
			$form = 0;

			// anchor bookmark in edit form
			$bookmark_edit_form_id = 'history_form';

			// css class
			$css = 'paging';

			// array; 
			// 1 column friendly name, 
			// 2 column MySQL name, 
			// 3 align (left/center/right), 
			// 4 valign (top/bottom), 
			// 5 width, 
			// 6 handle date, string value 'timeago' activates jquery timeago script 
			// 6 string value 'Y-m-d' / 'Y-m-d H:i:s' / 'H:i:s' / 'D, j M Y, H:i:s' etc to format datetime (http://php.net/manual/en/function.date.php)
			// 6 use null for all other content (no datetime)
			// 7 replace value from match array (if not null): array('one'=>1,'two'=>2)
			
			if (get_role_CMS('administrator') == 1) {
				$arrcols = array(
					array('id','history_id','right','top','5%',null,null),
					array('field_id','field_id','right','top','5%',null,null),
					array('field','field','left','top','5%',null,null),
					array('action','action','left','top','5%',null,null),
					array('description','description','right','top','60%',null,null),
					array('users_id','users_id','right','top','5%',null,null),
					array('datetime','utc_datetime','right','top','15%','Y-m-d H:i:s',null),
				);			
			} else {
				$arrcols = array(
					array('id','history_id','right','top','5%',null,null),
					array('field_id','field_id','right','top','5%',null,null),
					array('field','field','left','top','5%',null,null),
					array('action','action','left','top','5%',null,null),
					array('description','description','right','top','60%',null,null),
					array('users_id','users_id','right','top','5%',null,null),
					array('datetime','utc_datetime','right','top','15%','Y-m-d H:i:s',null),
				);			
			}

			// build table 
			// paging
			// editable
			// config
			getSQLtable(
				$TablePager, 
				$rows, 
				$bookmark, 
				$token,
				$table_description, 
				$table_row_checkbox, 
				$table_row_view,
				$table_row_edit, 
				$table_row_edit_link, 
				$table_row_edit_link_css_class, 
				$table_row_view_link, 
				$table_row_view_link_css_class, 
				$bookmark_edit_form_id,	
				$table_id,
				$css,
				$dtz,
				$form,
				$arrcols);	
		}

	}


?>
