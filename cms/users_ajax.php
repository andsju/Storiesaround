<?php
// include core
//--------------------------------------------------
include_once 'includes/inc.core.php';

$users = new Users();

// overall
// --------------------------------------------------
if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {

		// $action
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_STRING);
		
		// switch action 
		switch ($action) {
		
			case 'users_search':

				// search
				$s = $_POST['s'];
				$rows = $users->getUsersSearch($s);											
				echo json_encode($rows);

			break;

			case 'pages_selections_search':

				// search
				$s = $_POST['s'];
				$rows = $selections->getSelectionsSearch($s);
				echo json_encode($rows);

			break;
		}
	}			
}