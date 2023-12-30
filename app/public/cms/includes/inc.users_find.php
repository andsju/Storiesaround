<?php 
if(!defined('VALID_INCL')){header('Location: index.php'); die;} 

// include core
//--------------------------------------------------
require_once 'inc.core.php';

if(!get_role_CMS('user') == 1) {die;}
?>

<script type="text/javascript">
	$(document).ready(function() {

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
			"iDisplayLength": 25,
			"order": [[ 1, "asc" ]]
		});
				
		$( "[title]" ).tooltip({
			position: {
				my: "right top",
				at: "right+25 top+25"
			}
		});

	});
</script>


<?php

// default
$search = null;
$form = false;
$table = false;

$this_url = $_SERVER['REQUEST_URI'];

$users = new Users();
$row_users = null;


$row_users_total = $users->getUsersSearchWordsRelevance($search);

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
			<form id="searchform" method="post" action="<?php echo $this_url; ?>">
			
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
			<form id="searchform" method="post" action="<?php echo $this_url; ?>">
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
			}
		
			$row_users = $users->getUsersRole($role_CMS);

		break;
		
		
		case 'group':
			
			?>	
			<form id="searchform" method="post" action="<?php echo $this_url; ?>">
				<h4 class="admin-heading" style="margin-bottom:5px;">Find users in group</h4>
				
				<?php

				$groups = new Groups();
				$rows = $groups->getGroupsSearchWords($search=null);

				if($groups) {
					// use function getSelect for select list
					$session_id = isset($_SESSION['sql_groups_id']) ? $_SESSION['sql_groups_id'] : null;
					getSelect(pdo2array($rows), 'groups_id', $select_this = 'select group &raquo;&raquo;&raquo;', 'post', $session_id, $onchange=true, $multiple=false, 1, 'css_select');
				}
				?>
				
				<span class="toolbar"><button id="btn_search_group" name="btn_search_group" style="margin:0px">Search</button></span>
				<input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token">
			</form>
			
			<?php	
			if($groups) {
				$groups_id = isset($_POST['groups_id']) ? $_POST['groups_id'] : null;
				$row_users = $groups->getGroupsMembershipAll($groups_id);
				$form = true;
				$table = true;				
			}
			
		break;
		

	}

	echo '<br />';


	if(count($row_users) > 100) {
		echo 'More than 100 rows in table ('.count($row_users).'). Please use search field to display table.';
		if($form === true) {
			$table = true;
		}
	} else {
		$table = true;
	}
	
	
	if($table === true) {
	
		$html = null;
		if($row_users) {
			$html .= '<table class="table_js lightgrey">';
			$html .= '<thead>';
				$html .= '<tr>';
					$html .= '<th>First name</th>';
					$html .= '<th>Last name</th>';
					$html .= '<th>Email</th>';
					$html .= '<th>Username</th>';
					$html .= '<th>Registered</th>';
					$html .= '<th>Last visit</th>';
					$html .= '<th>CMS role</th>';
					$html .= '<th style="width:5%;">Status</th>';
					$html .= '<th style="width:5%;">Edit</th>';
				$html .= '</tr>';
			$html .= '</thead>';					
			$html .= '<tbody>';

			foreach($row_users as $r) {
				$html .= '<tr>';
					$html .= '<td>'. $r['first_name'] .'</td>';
					$html .= '<td>'. $r['last_name'] .'</td>';
					$html .= '<td>'. $r['email'] . '</td>';
					$html .= '<td>'. $r['user_name'] . '</td>';
					$dt = strlen($r['utc_created']) > 10 ? substr($r['utc_created'], 0, 10) : '<span class="ui-icon ui-icon-calendar" style="display:inline-block;" title="'. $r['utc_created'] .'"></span>';
					$html .= '<td>'. $dt .'</td>';
					$html .= '<td>'. $r['utc_lastvisit'] . '</td>';
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

	}
	
	
	
}

?>
