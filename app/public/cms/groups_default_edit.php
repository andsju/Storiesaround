<?php

/*** include core ***/
//--------------------------------------------------
include_once 'includes/inc.core.php';

if(!get_role_CMS('administrator') == 1) {header('Location: index.php'); die;}


$groups = new Groups();

// $default_id -> handle pre-selected posts and different form actions
if (isset($_POST['groups_default_target_id'])) {
	// new default
	$_SESSION['groups_default_target_id'] = $_POST['groups_default_target_id'];
} else {
	// check pre-selected
	$_SESSION['groups_default_target_id'] = isset($_SESSION['groups_default_target_id']) ? $_SESSION['groups_default_target_id'] : 0; 
}

// set active group if $_GET['groups_default_id'] exists
if (isset($_GET['groups_default_id'])) { 
	$_SESSION['groups_default_target_id'] = $_GET['groups_default_id'];
}

// clear active group if $_GET['clear'] exists
if (isset($_GET['clear'])) { 
	$_SESSION['groups_default_target_id'] = null;
}





/*** handle form actions
-------------------------------------------------- ***/

// form variables
$reply = '';
$groups_default_id = null;
$checkbox_id = '';


// form fail variables
$ff_title = $ff_description = '';

// insert / update
// --------------------------------------------------
// assume invalid values for required fields
$title = $description = false;

if (isset($_POST['btn_save_group'])) {

	// validate groups_default_id
	$groups_default_id = filter_input(INPUT_POST, 'groups_default_id', FILTER_VALIDATE_INT) ? $_POST['groups_default_id'] : null;
	
	// validate active
	$active = filter_input(INPUT_POST, 'checkbox_active', FILTER_VALIDATE_INT) ? '1' : '0';

	// trim incoming data
	$trimmed = array_map('trim', $_POST);
	
	// sanitize $_POST
	// check for title value
	if (strlen($trimmed['title']) > 0) {
		$title = filter_var($trimmed['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	} else {
		$ff_title = 'Please enter title';
	}

	// check for description value
	if (strlen($trimmed['description']) > 0) {
		$description = filter_var($trimmed['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	} else {
		$ff_description = 'Please description';
	}
	
	// required fileds validated so far, next step
	if ($title && $description){
	
		// validate $groups_default_id
		if ($groups_default_id = filter_input(INPUT_POST, 'groups_default_id', FILTER_VALIDATE_INT)) { 
				
			$groups->setGroupsDefault($title, $description, $active, $groups_default_id);

		}
	} else {
		$reply .= '<span class="reply_failure">Please check required fields</span>';
	}
}

// insert new members
// --------------------------------------------------
if (isset($_POST['btn_add'])) {
	// this target group
	$groups_default_target_id = filter_input(INPUT_POST, 'groups_default_target_id', FILTER_VALIDATE_INT) ? $_POST['groups_default_target_id'] : null;

	// filter incomming $_POST['groups_id']
	if (isset($_POST['groups_id'])) {
		$a_groups_id = filter_var_array($_POST['groups_id'], FILTER_VALIDATE_INT);	
	}
	
	// next step, validated group and users
	if (isset($groups_default_target_id) && isset($a_groups_id)) {

		// get selected members in selected group	
		$rows_old = $groups->getGroupsDefaultMembership($groups_default_target_id);
		
		
		// use function flatt_array() to flatten array 
		$a_old = flatt_array($rows_old);
		
		$a_new = flatt_array($a_groups_id);
		
		// members to insert
		$a_insert = array_diff($a_new, $a_old);

		// insert
		$i_inserted = 0;
		try {			
			// new PDO db connection
			$dbh = db_connect();
			
			// sql delete				
			$sql_insert = "INSERT INTO groups_default_members ";
			$sql_insert .= " (groups_default_id, groups_id) ";
			$sql_insert .= " VALUES (:groups_default_id, :groups_id)";
			
			
			// use prepared statement 
			$sth = $dbh->prepare($sql_insert);
			$sth->bindParam(':groups_default_id', $groups_default_target_id, PDO::PARAM_INT);
			$sth->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);

			foreach($a_insert as $groups_id){
				// make sure we use av valid groups_id
				if($groups_id > 0) {
					$sth->execute();
				}
				$i_inserted++;
			}
			// close database connection
			$dbh = null;

		} catch (PDOException $e) {
			//handle PDOException
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e->getMessage());
		}
	}
}


// delete members
// --------------------------------------------------
if (isset($_POST['btn_remove'])) {
	// this target group
	$groups_default_target_id = filter_input(INPUT_POST, 'groups_default_target_id', FILTER_VALIDATE_INT) ? $_POST['groups_default_target_id'] : null;

	// filter incomming $_POST['users_id']
	if (isset($_POST['groups_id'])) {
		$a_groups_id = filter_var_array($_POST['groups_id'], FILTER_VALIDATE_INT);	
	}

	// next step, validated default groups and groups
	if (isset($groups_default_target_id) && isset($a_groups_id)) {
		$i_deleted = 0;
		try {			
			// new PDO db connection
			$dbh = db_connect();
			
			// sql delete				
			$sql_delete = "DELETE FROM groups_default_members";
			$sql_delete .= " WHERE groups_default_id =:groups_default_id";
			$sql_delete .= " AND groups_id =:groups_id";
			
			// use prepared statement 
			$sth = $dbh->prepare($sql_delete);
			$sth->bindParam(':groups_default_id', $groups_default_target_id, PDO::PARAM_INT);
			$sth->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);

			foreach($a_groups_id as $groups_id){
				$sth->execute();
				$i_deleted++;
			}
			$reply = '<span class="reply_success">deleted</span>';
			// close database connection
			$dbh = null;

		} catch (PDOException $e) {
			//handle PDOException
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e->getMessage());
		}
	}
}




// function to show select list from given array such as
// array('role_guest' => 'Guest', 'role_user' => 'User')
$a = array('title' => 'Title', 'descripton' => 'Descripton', 'utc_created' => 'Date registred', 'utc_lastvisit' => 'Date lastvisit');




// if $_GET method is used for editing
// get saved data, if not just deleted...
if (!isset($_POST['btn_delete_group'])) {
	if ($groups_default_id = filter_var($_SESSION['groups_default_target_id'], FILTER_VALIDATE_INT)) {

		$r = $groups->getGroupsDefaultMeta($groups_default_id);
		if($r) {
			$title = $r['title'];
			$description = $r['description'];
			$active = $r['active'];		
		}	
	}
}





// page title
//--------------------------------------------------
$page_title = "Edit default group";


//meta tags
//--------------------------------------------------
$meta_author = $meta_keywords = $meta_description = $meta_robots = $meta_robots = null;


// css files
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css',
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css' );


$body_style = "";
	
// include header
//--------------------------------------------------
include_once 'includes/inc.header_minimal.php';


// load javascript files
//--------------------------------------------------
$js_files = array(
	'libraries/jquery-ui/jquery-ui.custom.min.js', 
	'libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	'libraries/js/functions.js', 
	'libraries/jquery-colorbox/jquery.colorbox-min.js',
	'libraries/jquery-plugin-validation/jquery.validate.js' );

?>


<?php 
// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>


<script type="text/javascript">
	$(document).ready(function() {
		
		$("#back").click(function(event) {
			event.preventDefault();
			//history.back(1);
			window.location.assign("admin.php?t=groups&tgr=group_default")			
		});
		
		$( ".toolbar_back button" ).button({
			icons: {
				secondary: "ui-icon-arrowreturnthick-1-w"
			},
			text: true
		});
		
		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});

		$( ".toolbar_trash button" ).button({
			icons: {
				primary: "ui-icon-trash"
			},
			text: false
		});

		$("#clickMe").click(function() {
			$("#default_users_box").toggle('fast');
			return false;
		});
		
				
		// client side validation
		$("#groups_default_form").validate({
			rules: {
				title: {
					required: true
				},
				description: {
					required: true
				}
			},
			messages: {
				title: {
					required: "* check"
				},
				description: {
					required: "* check"
				}
			}
		});
			
		// drag and drop

		$(".column").sortable({
			opacity: 0.8, 
			cursor: 'move', 			
			connectWith: ['.column'],
			update: function() {
			}
		});
		$('#save_drag_drop').click(function(event){
			event.preventDefault();
			
			$("#col_2").each(function(){
				var action = "savemembers";
				var result = $(this).sortable("toArray");
				var columnId = $(this).attr('id'); 
				var targetId =<?php echo $_SESSION['groups_default_target_id'] ?>;
				var token = $("#token").val();
				
				if (token.length > 0 && targetId > 0 ){
					$.ajax({
						beforeSend: function() { loading = $('#ajax_spinner_drag_drop').show()},
						complete: function(){ loading = setTimeout("$('#ajax_spinner_drag_drop').hide()",700)},
						type: "POST",
						url: "groups_default_ajax.php",
						data: "action=" + action + "&id=" + columnId + "&groups_default_id=" + targetId + "&groups_id=" + result + "&token=" + token,
						cache: false,
						success: function(message){	
							ajaxReply('','#ajax_status_drag_drop');
						}
					});
				}
				
			});
		});
		
	});
</script>





<?php

if($groups_default_id == null) {die;};
// show fieldset only if we have an active group from $_GET['groups_id']
if (isset($_SESSION['groups_default_target_id'])) { 
?>

<p>
	<span class="toolbar_back"><button id="back">Back to default groups</button></span>
</p>

<fieldset>
	<legend align="right">Edit default selection group</legend>
	<form id="groups_default_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
		<input type="hidden" id="groups_default_id" name="groups_default_id" value="<?php echo $groups_default_id; ?>">	
		
		<h4 class="admin-heading">Default group: <?php echo $title; ?> </h4>
		<table width="100%">
			<tr>
				<td width="40%" valign="bottom">
				<label for="title">title: </label>
				<br />
				<input type="text" name="title" id="title" title="Enter title" size="50" maxlength="50" value="<?php if(isset($title)){echo $title;}?>" />
				</td>
				<td width="45%" valign="bottom">
				<label for="description">description: </label>
				<br />
				<input type="text" name="description" id="description" title="Enter description" size="70" maxlength="100" value="<?php if(isset($description)){echo $description;}?>" />
				</td>
				<td width="5%" valign="bottom">
				<label for="checkbox_active">Active: </label>
				<input type="checkbox" name="checkbox_active" value="1" class="input" <?php if($active == 1) {echo 'checked';} ?> />&nbsp;			
				</td>
				<td width="10%" valign="bottom">
				<span class="toolbar"><button type="submit" id="btn_save_group" name="btn_save_group">Save</button></span>
				</td>
			</tr>
		</table>
	</form>	
</fieldset>

<?php } ?>




<fieldset>
<legend align="right">Add / remove group members</legend>

<table width="100%" border="0">
	<tr>
		<td width="46%" valign="top">
			<h5 class="admin-heading">Source</h5>
		</td>
		<td width="8%" valign="top">
		&nbsp;
		</td>
		<td width="46%" valign="top">
			<h5 class="admin-heading">Target</h5>
		</td>
	</tr>
	<tr>
		<td width="46%" valign="top">

			<table width="100%" border="0">
				<tr>
					<td>
						<form id="groups_default_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />

							<?php
							$rows = $groups->getGroupsDefault();
							
							// if btn_search_all is used delete $_SESSION['groups_default_source_id']
							if(isset($_POST['btn_search_all'])) {
								$_SESSION['groups_default_source_id'] = null;
							}
							// 
							if(isset($_POST['groups_default_source_id'])) {
								// new default
								$_SESSION['groups_default_source_id'] = ($_POST['groups_default_source_id'] > 0) ? $_POST['groups_default_source_id'] : null;
							} else {
								// check pre-selected
								$_SESSION['groups_default_source_id'] = isset($_SESSION['groups_default_source_id']) ? $_SESSION['groups_default_source_id'] : null; 
							}
							
							// use function getSelect to show select list, pass $default_id
							getSelect(pdo2array($rows), 'groups_default_source_id', $select_this='default selection groups &raquo;&raquo;&raquo;', 'post', $default_id = $_SESSION['groups_default_source_id'], $onchange=true, $multiple=false, 1, 'css_select');
							?>

							<span class="toolbar"><button type="submit" name="btn_search">&raquo;</button></span>
						</form>
					</td>
					<td align="right">				
					</td>
				</tr>
			</table>
		
		</td>
		<td width="8%" valign="top">
			&nbsp;
		</td>
		<td width="46%" valign="top">
		
		
		
		<?php		
		// use function getSelect to show select list, pass $default_id
		// changable if we have no $_GET['group_edit']  
		if (!isset($_SESSION['groups_default_target_id'])) { 
		?>	
			<form id="groups_default_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
			<?php
			getSelect(pdo2array($rows), 'groups_default_target_id', $select_this='default selection groups &raquo;&raquo;&raquo;', 'post', $default_id = $_SESSION['groups_default_target_id'], $onchange=true, $multiple=false, 1, 'css_select');
			?>
			<span class="toolbar"><button type="submit" name="btn_search">&raquo;</button></span>
			</form>
			
			<?php
			} else {
				if(isset($title)) {
					echo 'Selection group: '. $title;
				}
			}
			?>
		
		</td>
	</tr>
	<tr>
		<td width="46%" valign="top">
		<form id="groups_default_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<?php echo getSelect($a, $id='orderby', $select_this='order by &raquo;&raquo;&raquo;', $request='post', null, $onchange=true, $multiple=false, $size=1, $css=''); ?>
		
			
			<table width="100%">
				<tr>
					<td>
						<input type="text" size="20" name="search_group" value="<?php if(isset($_POST['search_group'])) { echo $_POST['search_group'];} ?>">
					</td>
					<td align="right">
						<input type="hidden" name="q" value="<?php if(isset($_POST['q'])) {echo $_POST['q'];} ?>" />
						<input type="hidden" name="sort" value="<?php if(isset($_REQUEST['sort'])) {if($_REQUEST['sort'] == 'desc') {echo 'asc';} else {echo 'desc';}} ?>" />&nbsp;
						<span class="toolbar"><button type="submit" name="btn_search">Search</button></span>
						<input type="hidden" name="q" id="q" value="all" />
						<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
						<span class="toolbar"><button type="submit" name="btn_search_all">All groups</button></span>					
					</td>
				</tr>
			</table>

		</form>


		
		&nbsp;
		</td>
		<td width="8%" align="center" valign="top">
		&nbsp;
		</td>
		<td width="46%" valign="top">	
			<span class="toolbar"><button id="save_drag_drop" title="save members dont affect source group" type="submit">Save members &laquo;&nbsp;drag and drop&nbsp;&raquo;</button></span>
			<span id="ajax_spinner_drag_drop" style='display:none'><img src="css/images/spinner.gif"></span>
			<span id="ajax_spinner_drag_drop" style='display:none'></span>
			<span id="ajax_status_drag_drop" class="timestamp_autosave"></span>
		</td>
	</tr>

	<?php

	// show selected members in selected group
	if(isset($_SESSION['groups_default_source_id'])) {
		$sql_select = "SELECT groups.groups_id AS groups_id, groups.title AS title 
		FROM groups
		LEFT JOIN groups_default_members ON groups.groups_id = groups_default_members.groups_id
		WHERE groups_default_members.groups_default_id = ".  $_SESSION['groups_default_source_id'];
	} else {	
		$sql_select = "SELECT groups.groups_id, groups.title AS title
		FROM groups";
	}
	
	
	// search words from form input text
	$arr_words = null;	
	if(isset($_POST['search_group'])) {
		if(strlen($_POST['search_group']) > 0) {
			$arr_words = explode(" ", filter_var($_POST['search_group'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
			// search in columns
			$arr_cols = array('groups.title', 'groups.description');
			// function getSQLsearch 	
			$sql_select = getSQLsearch($arr_words, $arr_cols, $bol_match_all_words=false, $sql_select);
		}
	}
	
	
	// ------------------------->
	// sql "ORDER BY ...field ASC|DESC" -> function getSQLdata to query order by
	// set default
	$default_orderby = 'title';
	// allowed fields
	$allowed_orderby = array('title','description','utc_created');
	// default sort
	$default_sort = 'ASC';						
	// table name
	$table = 'groups';
	// function getSQLorder 
	$sql_select = getSQLorder($default_orderby, $allowed_orderby, $default_sort, $table, $sql_select);

	
	// new PDO db connection
	$dbh = db_connect();
	// execute SQL statement
	$sth = $dbh->query($sql_select);
	// return array of rows
	$rows_source = $sth->fetchAll(PDO::FETCH_ASSOC);
	// close db connection
	$dbh = NULL;


	
	if (isset($_SESSION['groups_default_target_id'])) {
		if ($_SESSION['groups_default_target_id'] > 0) {
			$rows_target = $groups->getGroupsDefaultMembershipMeta($groups_id=$_SESSION['groups_default_target_id']);		
		}
	}
	
	?>
	
	<form id="groups_default_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />	
	<input type="hidden" name="groups_default_target_id" id="target_id" value="<?php if(isset($_SESSION['groups_default_target_id'])) {echo $_SESSION['groups_default_target_id'];}?>" />
	
	<tr>
		<td align="left" valign="top">		
			<div id="col_1" class="column" style="max-height:600px;overflow:auto;width:100%;">
				<?php
				if(isset($rows_source)) {
					getList(pdo2array($rows_source), $checkbox=true, $id='groups_id[]', $request='post');
				}
				?>
			</div>
		</td>
		<td align="center" valign="top">
			<div class="column_divider">
				<span class="toolbar"><button type="submit" name="btn_add" title="add selected groups">&raquo;</button></span>
				<br />
				<br />

				<span class="toolbar_trash"><button type="submit" name="btn_remove" title="remove selected groups">&nbsp;</button></span>
				
			</div>		
		</td>
		<td align="left" valign="top">
			<div id="col_2" class="column" style="max-height:600px;overflow:auto;width:100%;">
				<?php
				if(isset($rows_target)) {
					getList(pdo2array($rows_target), $checkbox=true, $id='groups_id[]', $request='post');
				}
				?>
			</div>
		</td>
	</tr>
	</form>
	
</table>
		
</fieldset>		


<?php
// include footer
//--------------------------------------------------
include_once 'includes/inc.footer_cms.php';
?>