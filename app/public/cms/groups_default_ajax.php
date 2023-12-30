<?php
/*** include core ***/
//--------------------------------------------------
include_once '../includes/inc.core.php';

if(!get_role_CMS('user') == 1) {die;}

// search
// prevent usage - check session token
if ((isset($_POST['token']) && (isset($_SESSION['token'])))) {
	if ($_POST['token'] == $_SESSION['token']) {

		// check $_POST suggestion_search		
		if (isset($_POST['suggestion_search'])) {
			
			// sanitize url string
			$url = filter_var($_POST['url'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			
			// check string length greater
			if (strlen($_POST['suggestion_search']) > 1) {
		
				// sanitize search string
				$query_search = filter_var($_POST['suggestion_search'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		
				//try...catch block
				try {
					// sql 					
					$sql_select = 
					"SELECT title, description, groups_default_id
					FROM groups_default
					WHERE title LIKE :search
					OR 
					description LIKE :search
					LIMIT 20";
					
					// apply percentages to search string
					$search = "%".$query_search."%";

					// new PDO db connection
					$dbh = db_connect();
					
					// use prepared statement 
					$sth = $dbh->prepare($sql_select);
					$sth->bindParam(':search', $search, PDO::PARAM_STR);
					$sth->execute();
					// return array of rows
					$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
					
					// close db connection
					$dbh = NULL;

				} catch (PDOException $e) {
					handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
				}

				if (is_array($rows)) {
					if (count($rows) > 0) {
						echo '<div id="searchresults">';
						$even_odd = '';
						foreach ($rows as $row) {
							$even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
							echo '<div class="'. $even_odd .'">';
							echo '<a href="'. $url .'&groups_default_id='. $row['groups_default_id'] .'">';
							echo $row['title'];
							echo '</a>';
							echo '</div>';
						}
						echo '</p>';
					}
				}			
			}
		}
		
		// check $_POST suggestion_search		
		if (isset($_POST['action'])) {

			// get existing members in this group, array $a_old
			// new members from post, $a_new 
			// compare arrays; use array_diff to delete old members, and insert new
			// use array_intersect to keep existing members

			// show selected members in selected group
			try {	
				// sql "SELECT...FROM..."
				$sql_select = "SELECT groups.groups_id AS groups_id
				FROM groups
				LEFT JOIN groups_default_members ON groups.groups_id = groups_default_members.groups_id
				WHERE groups_default_members.groups_default_id = ".  $_POST['groups_default_id'];
				

				// new PDO db connection
				$dbh = db_connect();
				// execute SQL statement
				$sth = $dbh->query($sql_select);
				// return array of rows
				$rows_old = $sth->fetchAll(PDO::FETCH_ASSOC);
				// close db connection
				$dbh = NULL;

			} catch (PDOException $e) {
				handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
			}

			// use function flatt_array() to flatten array 
			$a_old = flatt_array($rows_old);			
			
			// make array of $_POST['users_id']
			$a_new = explode(",", $_POST['groups_id']);

			// existing members that still will be group members
			$a_keep = array_intersect($a_new, $a_old);
			
			// members to delete
			$a_delete = array_diff($a_old, $a_new);
			
			// members to insert
			$a_insert = array_diff($a_new, $a_old);
			
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
				$sth->bindParam(':groups_default_id', $_POST['groups_default_id'], PDO::PARAM_INT);
				$sth->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
	
				foreach($a_delete as $groups_id){
					$sth->execute();
					$i_deleted++;
				}
				// close database connection
				$dbh = null;

			} catch (PDOException $e) {
				handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
			}

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
				$sth->bindParam(':groups_default_id', $_POST['groups_default_id'], PDO::PARAM_INT);
				$sth->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
	
				foreach ($a_insert as $groups_id) {
					// make sure we use av valid groups_id
					if($groups_id > 0) {
						$sth->execute();
					}
					$i_inserted++;
				}
				// close database connection
				$dbh = null;

			} catch (PDOException $e) {
				handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
			}

			echo 'saved';			
		}
	}
}
?>