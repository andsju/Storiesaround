<?php if(!defined('VALID_INCL')){header('Location: index.php'); die;} ?>

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

// form action url
//$this_url = $_SERVER['PHP_SELF'] .'?tab='. $_GET['tab'];
$this_url = $_SERVER['REQUEST_URI'];

// sql orderby and sort
$default_orderby = 'history_email_id';
$allowed_orderby = array('history_email_id','email_to','email_subject','email_body','utc_datetime');
$default_sort = 'DESC';

// validate
// btn click - use only $_POST array
if(isset($_POST['btn_search'])) {
	$email_to = filter_input(INPUT_POST, 'email_to', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ? $_POST['email_to'] : null;
} else {
	$email_to = filter_input(INPUT_POST, 'email_to', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ? $_POST['email_to'] : null;
}

?>
	


	<h4 class="admin-heading">Site smtp email history</h4>
	

	<form id="searchform" method="post" action="<?php echo $this_url; ?>">
	
		<fieldset class="search_1">
			<legend align="right">search</legend>
			<div style="float:left;padding:5px;">
				<label for="history_email_find">Find: </label><br />
				<input id="history_email_find" name="history_email_find" style="width:400px;" value="<?php if(isset($_REQUEST['history_email_find'])) {echo $_REQUEST['history_email_find']; } ?>"/>
				<input type="hidden" id="pid" />
			</div>
			<div style="float:left;padding:15px 0 0 10px">
				<input type="submit" name="btn_search" value="Search" class="input" /></span>
			</div>
		</fieldset>	
	</form>

	<?php

	// sql 
	$sql_select = 'SELECT * '; 
	$sql_select .= ' FROM history_email ';
	
	// search in these columns
	$arr_cols = array('email_to', 'email_subject', 'email_body');	
	
	// search form
	// search words from form input text
	$arr_words = null;	
	if (isset($_REQUEST['history_email_find'])) {
		if (strlen($_REQUEST['history_email_find']) > 0) {
			$arr_words = explode(" ", filter_var($_REQUEST['history_email_find'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
		}
	}
	
	
	// handle input checkboxes (not shown)
	$bol_match_all_words = isset($_POST['checkbox_all']) ? true : true;

	$sql_select = getSQLsearch($arr_words, $arr_cols, $bol_match_all_words, $sql_select);
		
	if(isset($_POST['btn_search']) || isset($_GET['history_email_find'])) {
	
		// show result if we have a sql_select question	
		if (isset($sql_select)) {
		
			// get new sql -> ORDER BY | ASC/DESC | LIMIT
			$table = 'history_email';
			$sql_new = getSQLorder($default_orderby, $allowed_orderby, $default_sort, $table, $sql_select);
			
			//create an object of TablePager class -> pass sql query
			$TablePager = new TablePager($sql_new);

			$request_uri = exclude_queries(array('hits','orderby','page','sort','history_email_find'));
			
			//use current page
			$q = isset($_REQUEST['history_email_find']) ? $_REQUEST['history_email_find'] : null;
			
			$TablePager->url = $_SERVER['SCRIPT_NAME'] .'?'. $request_uri .'&history_email_find='. $q;

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
			$table_id = 'history_email_id';

			// table description in table caption
			$table_description = 'History';

			// checkbox in table 0/1
			$table_row_checkbox = null;
			
			// edit link in table 0/1
			$table_row_edit = null;

			// edit url link in table string
			$table_row_edit_link = null;

			// edit url link anchor css class in table, string
			$table_row_edit_link_css_class = '';
			
			// view url link in table, string
			$table_row_view_link = null;

			// view url link anchor css class in table, string
			$table_row_view_link_css_class = null;

			// dialog link in table 0/1
			$table_row_view = null;

			// editable form action in 0/1, enable toggleCheckboxes
			$form = 1;

			// anchor bookmark in edit form
			$bookmark_edit_form_id = 'history_email_form';

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
					array('id','history_email_id','right','top','5%',null,null),
					array('email_to','email_to','left','top','15%',null,null),
					array('email_subject','email_subject','left','top','20%',null,null),
					array('email_body','email_body','left','top','50%',null,null),
					array('datetime','utc_datetime','right','top','15%','Y-m-d H:i:s',null),
				);			
			} else {
				$arrcols = array(
					array('id','history_email_id','right','top','5%',null,null),
					array('email_to','email_to','left','top','35%',null,null),
					array('email_subject','email_subject','left','top','25%',null,null),
					array('email_body','email_body','left','top','15%',null,null),
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
