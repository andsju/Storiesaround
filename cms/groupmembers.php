<?php
// include core
//--------------------------------------------------
include_once 'includes/inc.core.php';
if(!get_role_CMS('administrator') == 1) {die;}

// include session access
//--------------------------------------------------
require_once 'includes/inc.session_access.php';


// css files
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/cms/libraries/jquery-datatables/style.css'
);

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
$page_title = 'Groupmembers';
$body_style = "width:1190px;";
include_once 'includes/inc.header_minimal.php';

?>


<script>
	
	$(document).ready(function() {

		$('#add').click(function(event){
			event.preventDefault();
			$('#add_table').toggle();
		
		});
	
		$('#btn_users_add').click(function(event){
			event.preventDefault();
			var token = $("#token").val();
			var action = 'users_add_to_group';
			var groups_id = $('#groups_id').val();
			var users = [];
			$('input:checkbox[name="src_users_id"]:checked').each(function(index) { users.push($(this).val());});

			$.ajax({
				beforeSend: function() { loading = $('.ajax_spinner_users').show()},
				complete: function(){ loading = setTimeout("$('.ajax_spinner_users').hide()",1000)},
				type: 'POST',
				url: 'groups_ajax.php',
				data: { 
					action: action, token: token, groups_id: groups_id, users: users
				},
				success: function(message) {
					if(message > 0) {
						location.reload(true);
					} else {
						alert('No new users...');
						
						$("#dialog_users_add").dialog("open").append('No new users...');

					}
				},
			});		
		});	
	
		$('#btn_users_bulk_action').click(function(event){
			event.preventDefault();
			var token = $("#token").val();
			var action = 'bulk';
			var groups_id = $('#groups_id').val();
			var users = [];
			$('input:checkbox[name="users_id"]:checked').each(function(index) { users.push($(this).val());});
			var bulk = $("#bulk").val();

			console.log(users);
			
			$.ajax({
				beforeSend: function() { loading = $('.ajax_spinner_bulk').show()},
				complete: function(){ loading = setTimeout("$('.ajax_spinner_bulk').hide()",1000)},
				type: 'POST',
				url: 'groups_ajax.php',
				data: { 
					action: action, token: token, groups_id: groups_id, bulk: bulk, users: users
				},
				success: function(message){	
					if(message > 0) {
						location.reload(true);
					}
				},
			});
			
		});	

		$( "#users_find" ).autocomplete({
			delay: 300,
			source: function( request, response ) {
				$.ajax({
					type: "post",
					url: "users_ajax.php",
					dataType: "json",
					data: {
						action: "users_search",
						token: $("#token").val(),
						s: request.term
					},
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.user,
								id: item.users_id,
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
		
		$('.table_js').dataTable({
			"iDisplayLength": 10,
			"order": [[ 0, "asc" ]]
		});
		
		$( "[title]" ).tooltip({
			position: {
				my: "right top",
				at: "right+25 top+25"
			}
		});
		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});
		

		$('#members .toggleboxes').click(function(event){
			event.preventDefault();
			var table = $('#members').DataTable();
			var b = $('#members :checkbox').is(':checked');
			
			if(b) {
				$('#members :checkbox').prop('checked', false);
			} else {
				$('#members :checkbox').prop('checked', true);
			}
		});
		$('#members_maybe .toggleboxes').click(function(event){
			event.preventDefault();
			var table = $('#members').DataTable();
			var b = $('#members_maybe :checkbox').is(':checked');
			
			if(b) {
				$('#members_maybe :checkbox').prop('checked', false);
			} else {
				$('#members_maybe :checkbox').prop('checked', true);
			}
		});

		$("#dialog_users_add").dialog({
			autoOpen: false,
			modal: true
		});		

		
		
	});
</script>


<?php

$users = new Users();
$groups = new Groups();

// default
$search = null;
$form = false;
$table = false;

$groups_id = isset($_REQUEST['groups_id']) ? $_REQUEST['groups_id'] : null;

if(!is_numeric($groups_id)) {
	die();
}
$g_meta = $groups->getGroups($groups_id);
if(!$g_meta) {
	die('Not a valid group');
}



$tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : null;
if($tab) {
	$this_url = $_SERVER['PHP_SELF'] .'?groups_id='. $groups_id.'&tab='.$tab;
} else {
	$this_url = $_SERVER['PHP_SELF'] .'?groups_id='. $groups_id;
}


$row_users = null;
$row_users_total = $users->getUsersSearchWords($search=null);
$add_class = isset($_REQUEST['tab']) ? '' : ' hide';

echo '<a href="#a" id="add" class="std"><h4><img src="css/images/plus.gif" /> Add members</h4></a>';

echo '<div class="clearfix '.$add_class.'" id="add_table" style="width:100%;margin-bottom:20px;border:1px dashed grey;padding:10px;">';

	echo '<div class="ui-tabs ui-widget" style="float:none;margin-top:0px;padding: 0em;">';
		get_tab_menu_jquery_ui_look_alike($this_url, array("search","role","group","?"), array("Find user","Find by role","Find by group","?"), "tab", "&raquo;&raquo;&raquo;", null, $ui_ul_add_class="ui-three", $ui_a_add_class="ui-show");
	echo '</div>';	

	echo '<div style="float:right;">'.count($row_users_total).' users (total)</div>';

	if (isset($_REQUEST['tab'])) {

		switch($_REQUEST['tab']) {
		
			case 'search':
			
				if (isset($_POST['users_find'])) {
					if (strlen(trim($_POST['users_find'])) > 0) {
						$search = trim($_POST['users_find']);
						$form = true;
						$table = true;
					}
				}

				?>
				<form id="searchform" method="POST" action="<?php echo $this_url; ?>">
				
					<h4 class="admin-heading" style="margin-bottom:5px;">Find users</h4>
					<input id="users_find" name="users_find" style="width:400px;" value="<?php if(isset($_REQUEST['users_find'])) {echo $_REQUEST['users_find']; } ?>"/>
					<input type="hidden" id="pid" />
					
					<span class="toolbar"><button id="btn_search" name="btn_search" style="margin:0px">Search</button></span>
					<input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token">
					
				</form>

				<?php
			
				$row_users = $users->getUsersSearchWords($search);
				
			break;

			case 'role':

				?>		
				<form id="searchform" method="POST" action="<?php echo $this_url; ?>">
					<h4 class="admin-heading" style="margin-bottom:5px;">Find users by role</h4>
					
					<?php
					// array roles
					$roles = array(0 => '-none-', 1 => 'User', 2 => 'Contributor', 3 => 'Author', 4 => 'Editor', 5 => 'Administrator', 6 => 'Superadministrator');
					
					// use function getSelect for select list
					$session_id = isset($_SESSION['sql_search_role']) ? $_SESSION['sql_search_role'] : null;
					getSelect($roles, 'search_role', $select_this = 'select role &raquo;&raquo;&raquo;', 'post', $session_id, $onchange=true, $multiple=false, 1, 'css_select');
					
					$role_CMS = isset($_POST['search_role']) ? $_POST['search_role'] : null;
					?>
					
					<span class="toolbar"><button id="btn_search_role" name="btn_search_role" style="margin:0px">Search</button></span>
					<input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token">
					
				</form>
				<?php
				
				if(isset($role_CMS)) {
					$form = true;
					$table = true;
					$row_users = $users->getUsersRole($role_CMS);
				}

			break;
			
			
			case 'group':
				
				?>	
				<form id="searchform" method="post" action="<?php echo $this_url; ?>">
					<h4 class="admin-heading" style="margin-bottom:5px;">Find users in group</h4>
					
					<?php

					// default group selection
					if(isset($_REQUEST['groups_default_source_id'])) {
						$gid = $_REQUEST['groups_default_source_id'];
						$_SESSION['groups_default_source_id'] = $_REQUEST['groups_default_source_id'];
					} else {
						//$gid = $_SESSION['groups_default_source_id'];
						$gid = $_SESSION['site_groups_default_id'];
						$_SESSION['groups_default_source_id'] = $_SESSION['site_groups_default_id'];
					}

					$rows = $groups->getGroupsDefault();
					if($rows) {
						echo '<input type="hidden" name="token" id="token" value="'. $_SESSION['token'] .'" />';
						// use function getSelect to show select list, pass $default_id
						getSelect(pdo2array($rows), 'groups_default_source_id', $select_this='Filter - default groups selection &raquo;&raquo;&raquo;', 'post', $default_id = $_SESSION['groups_default_source_id'], $onchange=true, $multiple=false, 1, 'css_select');
					}
									
					$rows = $groups->getGroupsDefaultMembershipMeta($gid);

					if($groups) {
						// use function getSelect for select list
						$session_id = isset($_SESSION['sql_groups_id']) ? $_SESSION['sql_groups_id'] : null;
						getSelect(pdo2array($rows), 'src_groups_id', $select_this = 'select group &raquo;&raquo;&raquo;', 'post', $session_id, $onchange=true, $multiple=false, 1, 'css_select');
					}
					?>
					
					<span class="toolbar"><button id="btn_search_group" name="btn_search_group" style="margin:0px">Search</button></span>
					<input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token">
				</form>
				
				<?php	

				$src_groups_id = isset($_POST['src_groups_id']) ? $_POST['src_groups_id'] : null;
				
				if($src_groups_id) {
					if($src_groups_id > 0) {				
						$row_users = $groups->getGroupsMembershipAll($groups_id);
						$form = true;
						$table = true;
					}
				}
				
			break;
			
		}

		echo '<br />';
		echo '<div style="">';

			$html = '';
			if($row_users) {
				$html .= '<table class="table_js lightgrey" id="members_maybe">';
				$html .= '<thead>';
					$html .= '<tr>';
						$html .= '<th>Name</th>';
						$html .= '<th>Email</th>';
						$html .= '<th>Date registered</th>';
						$html .= '<th>CMS role</th>';
					$html .= '</tr>';
				$html .= '</thead>';
				$html .= '<tfoot>';
					$html .= '<tr>';
						$html .= '<th colspan="3">';
						$html .= '<span class="toggleboxes hover">check | uncheck all</span>';
						$html .= '</th>';
					$html .= '</tr>';
				$html .= '</tfoot>';

				$html .= '<tbody>';

				foreach($row_users as $r) {
					$html .= '<tr>';
						
						$username = strlen($r['user_name']) > 0 ? ' ('.$r['user_name'] .')' : '';
						
						$html .= '<td><input type="checkbox" name="src_users_id" value="'. $r['users_id'] .'" /> '. $r['first_name'] .' '. $r['last_name'] .' '. $username . '</td>';
						$html .= '<td>'. $r['email'] . '</td>';
						
						$utc_stamp = new DateTime($r['utc_created']);
						$utc_stamp = $utc_stamp->format('Y-m-d');
						$utc_stamp = $utc_stamp > '2000-01-01' ? $utc_stamp : '';
						
						$html .= '<td>'. $utc_stamp . '</td>';
						$explain = array('none'=>0,'user'=>1,'contributor'=>2,'author'=>3,'editor'=>4,'administrator'=>5,'admin +'=>6);
						$html .= '<td>'. get_value_explained($r['role_CMS'], $explain) .'</td>';
					$html .= '</tr>';
				
				}
				$html .= '</tbody>';
				$html .= '</table>';
				$html .= '<span class="toolbar"><button id="btn_users_add">Add users to group</button></span>';
			} else {
				$html .= '| no result |';
			}
			echo $html;

		echo '</div>';
		
	}	
	
echo '</div>';

$row_users = $groups->getGroupsMembershipAll($groups_id);
$table = true;

echo '<h2>'.$g_meta['title'].' [group]</h2>';

if($table === true) {

	$html = null;
	if($row_users) {
		$html .= '<table class="table_js lightgrey" id="members">';
		$html .= '<thead>';
			$html .= '<tr>';
				$html .= '<th>First name</th>';
				$html .= '<th>Last name</th>';
				$html .= '<th>Email</th>';
				$html .= '<th>Last visit</th>';
				$html .= '<th>LMS role</th>';
				$html .= '<th>CMS role</th>';
				$html .= '<th style="width:5%;">Status</th>';
				$html .= '<th style="width:5%;">Edit</th>';
			$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tfoot>';
			$html .= '<tr>';
				$html .= '<th colspan="8">';
				$html .= '<span class="toggleboxes hover">check | uncheck all</span>';
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</tfoot>';

		$html .= '<tbody>';

		foreach($row_users as $r) {
			$html .= '<tr>';
				//$html .= '<td><input type="checkbox" name="users_id" value="'. $r['users_id'] .'" /></td>';
				$html .= '<td><input type="checkbox" name="users_id" value="'. $r['users_id'] .'" /> '. $r['first_name'] .'</td>';
				$html .= '<td>'. $r['last_name'] .'</td>';
				$html .= '<td>'. $r['email'] . '</td>';

				$utc_stamp = new DateTime($r['utc_lastvisit']);
				$utc_stamp = $utc_stamp->format('Y-m-d H:i');
				$utc_stamp = $utc_stamp > '2000-01-01' ? $utc_stamp : '';
				
				$html .= '<td>'. $utc_stamp . '</td>';
				$explain = array('none'=>0,'student'=>1,'tutor'=>2,'teacher'=>3,'administrator'=>4);
				$html .= '<td>'. get_value_explained($r['role_LMS'], $explain) .'</td>';
				
				$explain = array('none'=>0,'user'=>1,'contributor'=>2,'author'=>3,'editor'=>4,'administrator'=>5,'admin +'=>6);
				$html .= '<td>'. get_value_explained($r['role_CMS'], $explain) .'</td>';
				$explain = array('deleted'=>0,'inactive'=>1,'active'=>2);
				$html .= '<td>'. get_value_explained($r['status'], $explain) .'</td>';
				$html .= '<td><a href="users_edit.php?users_id=' .$r['users_id'] .'&token='.$_SESSION['token'].'" class="colorbox_edit">edit</a></td>';
			$html .= '</tr>';
		
		}
		$html .= '</tbody>';
		$html .= '</table>';
	} else {
		$html .= '| no result |';
	}
	echo $html;
	
	echo 
	'<select id="bulk" name="bulk">
		<option value="">- choose bulk action -</option>
		<option value="delete">Delete users</option>
		<option value="member">Set group role as member</option>
		<option value="member">Set group role as owner</option>
	</select>
	<span class="toolbar"><button id="btn_users_bulk_action">Run bulk action</button></span>';

}

?>

<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
<input type="hidden" id="groups_id" name="groups_id" value="<?php echo $groups_id; ?>">


<div id="dialog_users_add" title="Info" style="display:none;">
</div>


<?php 
// load javascript files
foreach ( $js_files as $js ): ?>
	<script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>


<?php
// include footer
//--------------------------------------------------
include_once 'includes/inc.footer_cms.php';
?>