<?php

/**
 * API for class Bookitems
 * extends Plugins class
 */

class Bookitems extends Plugins {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Bookitems';
		$a['classname'] = 'Bookitems';
		$a['description'] = 'Shows Bookitems plugin i selected area';
		$a['info'] = 'This page uses "Bookitems - plugin".';
		$a['css'] = CMS_DIR.'/content/plugins/bookitems/css/style.css';
		// replace data in area(s) (comma-separated): 'header,left_sidebar,content,right_sidebar,footer,page'  // use blank space pre and post words!
		$a['area'] = ' content ';
		return $a;
    }
	
   // pass parameters to the class file action.php
	public function action($pages_id, $users_id, $token, $areas) {
	
		$info = $this->info();
		$folder = strtolower($info['classname']);
		$file = $folder.'/action.php';
		$plugin_areas = null;
		//if(is_file('')) {}
		include_once $file;
		return $plugin_areas;
	}
	
	public function db_sql() {
		$sqls = array();
		$sqls[] = "CREATE TABLE IF NOT EXISTS `plugin_bookitems` (
		`plugin_bookitems_id` int(11) NOT NULL AUTO_INCREMENT,
		`plugin_bookitems_unit_id` int(11) NOT NULL DEFAULT '0',
		`users_id` int(10) NOT NULL DEFAULT '0',
		`title` varchar(255) DEFAULT '' COMMENT 'title',
		`description` varchar(1000) DEFAULT '' COMMENT 'description',
		`utc_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`utc_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (`plugin_bookitems_id`))";
		$sqls[] = "CREATE TABLE IF NOT EXISTS `plugin_bookitems_unit` (
		`plugin_bookitems_unit_id` int(11) NOT NULL AUTO_INCREMENT,
		`plugin_bookitems_category_id` int(11) NOT NULL DEFAULT '0',
		`title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		`description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
		`image` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
		`position` int(4) NOT NULL DEFAULT '0',
		`active` tinyint(1) NOT NULL DEFAULT '0',
		`utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (`plugin_bookitems_unit_id`))";
		$sqls[] = "CREATE TABLE IF NOT EXISTS `plugin_bookitems_category` (
		`plugin_bookitems_category_id` int(11) NOT NULL AUTO_INCREMENT,
		`title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		`description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`position` int(4) NOT NULL DEFAULT '0',
		`active` tinyint(1) NOT NULL DEFAULT '0',
		`utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (`plugin_bookitems_category_id`))";
		$sqls[] = "CREATE TABLE IF NOT EXISTS `plugin_bookitems_category_rights` (
		`plugin_bookitems_category_rights_id` int(11) NOT NULL AUTO_INCREMENT,
		`plugin_bookitems_category_id` int(11) NOT NULL DEFAULT '0',
		`users_id` int(11) NOT NULL DEFAULT '0',
		`groups_id` int(11) NOT NULL DEFAULT '0',
		`rights_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'read rights',
		`rights_edit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'edit rights',
		`rights_create` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'enable create ...',
		PRIMARY KEY (`plugin_bookitems_category_rights_id`))";
		return $sqls;	
	}

	
	public function db_sql_update() {
		$sqls = array();
		return $sqls;
	}
	

	/**
	*
	* 
	* @return row
	*/
    public function getBookitemsCategory() {
		$sql = "SELECT * 
		FROM plugin_bookitems_category";
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}	

	
	/**
	*
	* 
	* @return row
	*/
    public function getBookitemsCategoryId($plugin_bookitems_category_id) {
		$sql = "SELECT * 
		FROM plugin_bookitems_category
		WHERE
		plugin_bookitems_category_id = :plugin_bookitems_category_id";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}	
	

	
	/**
	*
	* 
	* @return rows
	*/
    public function getBookitemsUnits($plugin_bookitems_category_id) {
		$sql = "SELECT * 
		FROM plugin_bookitems_unit
		WHERE
		plugin_bookitems_category_id = :plugin_bookitems_category_id
		ORDER BY position ASC, title ASC";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}	
	
	

	/**
	*
	* @params
	*/
	public function setBookitemsCategoryiUpdate($title, $description, $active, $position, $plugin_bookitems_category_id, $utc_modified) {
		try {
			$sql = "UPDATE plugin_bookitems_category
			SET title = :title,
			description = :description,
			active = :active,
			position = :position,
			utc_modified = :utc_modified
			WHERE plugin_bookitems_category_id = :plugin_bookitems_category_id";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':description', $description, PDO::PARAM_STR);
			$stmt->bindParam(':active', $active, PDO::PARAM_INT);
			$stmt->bindParam(':position', $position, PDO::PARAM_INT);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}	
	}
		

	/**
	*
	* @return lastInsertId('plugin_bookitems_category_id')
	*/
    public function setBookitemsCategoryInsert($title, $utc_created) {
		try {
			$sql = "INSERT INTO plugin_bookitems_category 
			(title, utc_created) VALUES
			(:title, :utc_created)";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
			$stmt->execute();
			return $this->db->lastInsertId('plugin_bookitems_category_id');
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	


	/**
	*
	* @return lastInsertId('plugin_bookitems_unit_id')
	*/
    public function setBookitemsUnitInsert($title, $plugin_bookitems_category_id, $utc_created) {
		try {
			$sql = "INSERT INTO plugin_bookitems_unit 
			(title, plugin_bookitems_category_id, utc_created) VALUES
			(:title, :plugin_bookitems_category_id, :utc_created)";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
			$stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
			$stmt->execute();
			return $this->db->lastInsertId('plugin_bookitems_unit_id');
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	


	/**
	*
	* 
	* @return rows
	*/
    public function getBookitemsUnit() {
		$sql = "SELECT * 
		FROM plugin_bookitems_unit";
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}	

	/**
	*
	* 
	* @return row
	*/
    public function getBookitemsUnitId($plugin_bookitems_unit_id) {
		$sql = "SELECT * 
		FROM plugin_bookitems_unit
		WHERE
		plugin_bookitems_unit_id = :plugin_bookitems_unit_id";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_unit_id', $plugin_bookitems_unit_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}	

	

	/**
	*
	* @params
	*/
	public function setBookitemsUnitUpdate($title, $description, $image, $active, $position, $plugin_bookitems_unit_id, $plugin_bookitems_category_id, $utc_modified) {
		try {
			$sql = "UPDATE plugin_bookitems_unit
			SET title = :title,
			description = :description,
			image = :image,
			active = :active,
			position = :position,
			plugin_bookitems_category_id = :plugin_bookitems_category_id,
			utc_modified = :utc_modified
			WHERE plugin_bookitems_unit_id = :plugin_bookitems_unit_id";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_bookitems_unit_id', $plugin_bookitems_unit_id, PDO::PARAM_INT);
			$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':description', $description, PDO::PARAM_STR);
			$stmt->bindParam(':image', $image, PDO::PARAM_STR);
			$stmt->bindParam(':active', $active, PDO::PARAM_INT);
			$stmt->bindParam(':position', $position, PDO::PARAM_INT);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}	
	}
	
	
	
	/**
	*
	* 
	* @return row
	*/
    public function getBookitemsId($plugin_bookitems_unit_id) {
		$sql = "SELECT * 
		FROM plugin_bookitems
		WHERE
		plugin_bookitems_unit_id = :plugin_bookitems_unit_id";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_unit_id', $plugin_bookitems_unit_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}	
	

	/**
	*
	* 
	* @return row
	*/
    public function getBookitemsDatesIdNow($plugin_bookitems_unit_id) {
		$sql = "SELECT * 
		FROM plugin_bookitems
		WHERE
		plugin_bookitems_unit_id = :plugin_bookitems_unit_id
		ORDER BY utc_start asc";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_unit_id', $plugin_bookitems_unit_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}	
	
	
	
	/**
	*
	* 
	* @return row
	*/
    public function getBookitemsDatesId($plugin_bookitems_unit_id, $utc_start, $utc_end) {
		$sql = "SELECT * 
		FROM plugin_bookitems
		WHERE
		plugin_bookitems_unit_id = :plugin_bookitems_unit_id
		AND (
		(utc_start BETWEEN :utc_start AND :utc_end)
		OR 
		(utc_end BETWEEN :utc_start AND :utc_end)
		OR 
		(utc_start <= :utc_start AND utc_end >= :utc_end)
		)
		ORDER BY utc_start asc";

		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_unit_id', $plugin_bookitems_unit_id, PDO::PARAM_INT);
		$stmt->bindParam(':utc_start', $utc_start, PDO::PARAM_STR);
		$stmt->bindParam(':utc_end', $utc_end, PDO::PARAM_STR);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}	

	

	/**
	*
	* 
	* @return row
	*/
    public function getBookitemsMine($users_id) {
		$sql = "SELECT plugin_bookitems.utc_start, plugin_bookitems.utc_end, plugin_bookitems.title, plugin_bookitems_unit.title AS enhet_title
		FROM plugin_bookitems
		LEFT JOIN
		plugin_bookitems_unit
		ON plugin_bookitems.plugin_bookitems_unit_id = plugin_bookitems_unit.plugin_bookitems_unit_id 
		WHERE
		plugin_bookitems.users_id = :users_id
		AND utc_end > NOW()		
		ORDER BY plugin_bookitems.utc_start asc
		LIMIT 50 ";

		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}	

	
	
	
	
	/**
	*
	* 
	* @return row
	*/
    public function getBookitemsSingle($plugin_bookitems_id) {
		$sql = "SELECT * 
		FROM plugin_bookitems
		WHERE
		plugin_bookitems_id = :plugin_bookitems_id";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_id', $plugin_bookitems_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}	


	/**
	*
	* getCalendarEventsMultiple
	* @param array of integers calendar_categories_id
	* @return array
	*/
    public function getBookitemsMultiple($ids, $date) {

		$qMarks = str_repeat('?,', count($ids) - 1) . '?';
		
		$date = ($this->_isValidDate($date)) ? $date : date('Y-m-d');
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
		$month = date('m', $timestamp);
	
		$d1 = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
		// overlap months - include previous month...
        $d1 = date('Y-m-d', strtotime($d1.' - 1 months'));
		// get days in month
        // $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		// $d2 = date('Y-m-d', mktime(0, 0, 0, $month, $days_in_month, $year));
		$d2 = date('Y-m-d', strtotime($d1.' + 3 months'));
				
		$sql = "SELECT plugin_bookitems.plugin_bookitems_id, plugin_bookitems.plugin_bookitems_unit_id, plugin_bookitems.title, plugin_bookitems.description, plugin_bookitems.utc_start, plugin_bookitems.utc_end, plugin_bookitems.utc_modified, users.first_name, users.last_name, users.email, users.users_id
		FROM plugin_bookitems
		LEFT JOIN users ON plugin_bookitems.users_id = users.users_id
		WHERE plugin_bookitems.utc_start BETWEEN '$d1' AND '$d2'
		AND plugin_bookitems.utc_end BETWEEN '$d1' AND '$d2'
		AND plugin_bookitems.plugin_bookitems_unit_id IN
		($qMarks) 
		ORDER BY utc_start ASC";
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array_values($ids));
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}	
	

	/**
	*
	* @return lastInsertId('plugin_bookitems_id')
	*/
    public function setBookitemsId($plugin_bookitems_unit_id, $title, $description, $users_id, $utc_start, $utc_end, $utc_created) {
		try {
			$sql = "INSERT INTO plugin_bookitems 
			(plugin_bookitems_unit_id, title, description, users_id, utc_start, utc_end, utc_created) VALUES
			(:plugin_bookitems_unit_id, :title, :description, :users_id, :utc_start, :utc_end, :utc_created)";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_bookitems_unit_id', $plugin_bookitems_unit_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':description', $description, PDO::PARAM_STR);
			$stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
			$stmt->bindParam(':utc_start', $utc_start, PDO::PARAM_STR);
			$stmt->bindParam(':utc_end', $utc_end, PDO::PARAM_STR);
			$stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
			$stmt->execute();
			return $this->db->lastInsertId('plugin_bookitems_id');
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	

	/**
	*
	* @return row
	*/
    public function setBookitemsUpdateId($plugin_bookitems_unit_id, $plugin_bookitems_id, $title, $description, $users_id, $utc_start, $utc_end, $utc_created) {
		try {
			$sql = "UPDATE plugin_bookitems 
			SET
			plugin_bookitems_unit_id = :plugin_bookitems_unit_id,
			title = :title,
			description = :description,
			users_id = :users_id,
			utc_start = :utc_start,
			utc_end = :utc_end,
			utc_modified = :utc_created
			WHERE
			plugin_bookitems_id = :plugin_bookitems_id";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_bookitems_unit_id', $plugin_bookitems_unit_id, PDO::PARAM_INT);
			$stmt->bindParam(':plugin_bookitems_id', $plugin_bookitems_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':description', $description, PDO::PARAM_STR);
			$stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
			$stmt->bindParam(':utc_start', $utc_start, PDO::PARAM_STR);
			$stmt->bindParam(':utc_end', $utc_end, PDO::PARAM_STR);
			$stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
			return $stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}		

	
	/**
	*
	* delete booking
	* @return result
	*/
    public function deleteBookitemsId($plugin_bookitems_id) {

		try { 
		
			$stmt = $this->db->prepare("DELETE FROM plugin_bookitems WHERE plugin_bookitems_id =:plugin_bookitems_id");
			$stmt->bindParam(":plugin_bookitems_id", $plugin_bookitems_id, PDO::PARAM_INT);
			return $stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}




	/**
	*
	* @param integer $calendar_categories_id
	* @param integer $users_id
	* return users meta
	*/
    public function getBookitemsUsersRightsMeta($plugin_bookitems_category_id) {
	
		$sql = "
		SELECT plugin_bookitems_category_rights.plugin_bookitems_category_rights_id, plugin_bookitems_category_rights.rights_read, plugin_bookitems_category_rights.rights_edit, plugin_bookitems_category_rights.rights_create, users.first_name, users.last_name, users.email
		FROM plugin_bookitems_category_rights
		INNER JOIN plugin_bookitems_category
		ON plugin_bookitems_category_rights.plugin_bookitems_category_id = plugin_bookitems_category.plugin_bookitems_category_id
		INNER JOIN users
		ON users.users_id = plugin_bookitems_category_rights.users_id
		WHERE plugin_bookitems_category_rights.plugin_bookitems_category_id = :plugin_bookitems_category_id";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
		
		$stmt->execute();		
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}	


	/**
	*
	* @param integer $calendar_categories_id
	* @param integer $users_id
	* return users meta
	*/
    public function getBookitemsGroupsRightsMeta($plugin_bookitems_category_id) {
	
		$sql = "
		SELECT plugin_bookitems_category_rights.plugin_bookitems_category_rights_id, plugin_bookitems_category_rights.rights_read, plugin_bookitems_category_rights.rights_edit, plugin_bookitems_category_rights.rights_create, groups.title, groups.description 
		FROM plugin_bookitems_category_rights
		INNER JOIN plugin_bookitems_category
		ON plugin_bookitems_category_rights.plugin_bookitems_category_id = plugin_bookitems_category.plugin_bookitems_category_id
		INNER JOIN groups
		ON groups.groups_id = plugin_bookitems_category_rights.groups_id
		WHERE plugin_bookitems_category_rights.plugin_bookitems_category_id = :plugin_bookitems_category_id";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
		
		$stmt->execute();		
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}	


	/**
	*
	* @param integer $calendar_categories_id
	* @param integer $users_id
	*/
    public function getBookitemsUsersRights($plugin_bookitems_category_id, $users_id) {
	
		$sql = "
		SELECT plugin_bookitems_category_rights.rights_read, plugin_bookitems_category_rights.rights_edit, plugin_bookitems_category_rights.rights_create
		FROM plugin_bookitems_category_rights 
		INNER JOIN plugin_bookitems_category
		ON plugin_bookitems_category_rights.plugin_bookitems_category_id = plugin_bookitems_category.plugin_bookitems_category_id
		WHERE plugin_bookitems_category_rights.plugin_bookitems_category_id = :plugin_bookitems_category_id
		AND plugin_bookitems_category_rights.users_id = :users_id";
	
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
		$stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
		$stmt->execute();		
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}	


	/**
	*
	* @param integer $calendar_categories_id
	* @param integer $users_id
	* return lastInsertId
	*/
    public function setBookitemsUsersRightsNew($plugin_bookitems_category_id, $users_id) {
	
		try {
		
			$sql_insert = "INSERT INTO plugin_bookitems_category_rights 
			(plugin_bookitems_category_id, users_id) VALUES
			(:plugin_bookitems_category_id, :users_id)";

			$stmt = $this->db->prepare($sql_insert);
			$stmt->bindParam(":plugin_bookitems_category_id", $plugin_bookitems_category_id, PDO::PARAM_INT);
			$stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
			$stmt->execute();
			return $this->db->lastInsertId("plugins_bookitems_category_rights_id");

		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}
	}	

	

	/**
	*
	* @param integer $calendar_categories_id
	* @param integer $users_id
	* return lastInsertId
	*/
    public function setBookitemsGroupsRightsNew($plugin_bookitems_category_id, $groups_id) {
	
		try {
		
			$sql_insert = "INSERT INTO plugin_bookitems_category_rights 
			(plugin_bookitems_category_id, groups_id) VALUES
			(:plugin_bookitems_category_id, :groups_id)";

			$stmt = $this->db->prepare($sql_insert);
			$stmt->bindParam(":plugin_bookitems_category_id", $plugin_bookitems_category_id, PDO::PARAM_INT);
			$stmt->bindParam(":groups_id", $groups_id, PDO::PARAM_INT);
			$stmt->execute();
			return $this->db->lastInsertId("plugin_bookitems_category_rights_id");

		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}
	}	
	
		
	

	/**
	*
	* @param integer $calendar_categories_id
	* @param integer $users_id
	*/
    public function getBookitemsGroupsRights($plugin_bookitems_category_id, $groups_id) {
	
		$sql = "
		SELECT plugin_bookitems_category_rights.rights_read, plugin_bookitems_category_rights.rights_edit, plugin_bookitems_category_rights.rights_create
		FROM plugin_bookitems_category_rights 
		INNER JOIN plugin_bookitems_category
		ON plugin_bookitems_category_rights.plugin_bookitems_category_id = plugin_bookitems_category.plugin_bookitems_category_id
		INNER JOIN groups
		ON plugin_bookitems_category_rights.groups_id = groups.groups_id
		WHERE plugin_bookitems_category_rights.plugin_bookitems_category_id = :plugin_bookitems_category_id
		AND plugin_bookitems_category_rights.groups_id = :groups_id";

	
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
		$stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
		$stmt->execute();		
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}	


	/*
	* @param integer $calendar_categories_id
	* @param integer $users_id
	* return all rights where groups_id != 0
	*/
    public function getBookitemsGroupsRightsAccess($plugin_bookitems_category_id) {
	
		$sql = "
		SELECT plugin_bookitems_category_rights.rights_read, plugin_bookitems_category_rights.rights_edit, plugin_bookitems_category_rights.rights_create, plugin_bookitems_category_rights.groups_id 
		FROM plugin_bookitems_category_rights 
		INNER JOIN plugin_bookitems_category
		ON plugin_bookitems_category_rights.plugin_bookitems_category_id = plugin_bookitems_category.plugin_bookitems_category_id
		WHERE plugin_bookitems_category_rights.plugin_bookitems_category_id = :plugin_bookitems_category_id
		AND plugin_bookitems_category_rights.groups_id != 0";
	
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_bookitems_category_id', $plugin_bookitems_category_id, PDO::PARAM_INT);
		$stmt->execute();		
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}		
	
	
/**
	*
	* @param integer $plugin_bookitems_category_rights_id
	*/
    public function setBookitemsRightsDelete($plugin_bookitems_category_rights_id) {
	
		try {
		
			$stmt = $this->db->prepare("DELETE FROM plugin_bookitems_category_rights WHERE plugin_bookitems_category_rights_id =:plugin_bookitems_category_rights_id");
			$stmt->bindParam(":plugin_bookitems_category_rights_id", $plugin_bookitems_category_rights_id, PDO::PARAM_INT);
			return $stmt->execute();

		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}
	}	
	
	

	/**
	* @param $plugin_bookitems_category_rights_id
	* @param $rights, $value
	*/
    public function setBookitemsRightsUpdate($plugin_bookitems_category_rights_id, $r, $value) {
		
		try {
		
			$sql_update = "UPDATE plugin_bookitems_category_rights
			SET $r = :value
			WHERE plugin_bookitems_category_rights_id = :plugin_bookitems_category_rights_id";
			
			$stmt = $this->db->prepare($sql_update);
			$stmt->bindParam(":plugin_bookitems_category_rights_id", $plugin_bookitems_category_rights_id, PDO::PARAM_INT);
			$stmt->bindParam(":value", $value, PDO::PARAM_INT);
			return $stmt->execute();

		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}
	}
	

	
	
	

	
	private function _isValidDateTime($dateTime) {
		if(is_string(($dateTime))) {
			if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
				if (checkdate($matches[2], $matches[3], $matches[1])) {
					return true;
				}
			}
		}
	} 
	
	
	private function _isValidDate($date) {
		if(is_string(($date))) {
			if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) {
				if (checkdate($matches[2], $matches[3], $matches[1])) {
					return true;
				}
			}
		}
	}
	
	public function validTags() {
		$tags = '<a><img><b><i><hr><span>';
		return $tags;
	}


	// get calendar - holidays
	public function getBookitemsVertical($date=null, $href=null, $max_width=true, $plugin_bookitems_category_id=null, $period=null) {

		// check requested id
		if(!is_numeric($plugin_bookitems_category_id)) {
			die;
		}

		// get category settings
		$category = $this->getBookitemsCategoryId($plugin_bookitems_category_id);
		
		if(!$category) {
			die;
		}
		
		echo '<h3>'.$category['title'].'</h3>';
		if($category['active'] == 0) {
			die ('Category is not active');
		}		
		
		
		// get units
		$units_meta= $this->getBookitemsUnits($plugin_bookitems_category_id);

		// validate requested category
		if($units_meta == null) {
			die('no units in requested category');
		}
		
		
		//print_r2($units_meta);
		
		// extract unit id:s from $units_meta
		$ids = null;
		foreach($units_meta as $unit_ids) {
			$ids[] = $unit_ids['plugin_bookitems_unit_id'] ;
		}
		
		// get items for categories
		$items = $this->getBookitemsMultiple($ids, $date);
		
		//print_r2($items);
		//$items = getBookitemsId($plugin_bookitems_unit_id);
		//tried to implement access rights before loop - no success


		//default access rights
		$acc_read = $acc_edit = $acc_create = false;
				
		if(isset($_SESSION['users_id'])) {
			if($_SESSION['role_CMS'] >= 5 || $_SESSION['role_LMS'] >= 4) {
				$acc_read = $acc_edit = $acc_create = true;
			} else {
			
				//check role_CMS author & contributor
				//if(get_role_CMS('author') == 1 || get_role_CMS('contributor') == 1 || get_role_CMS('editor') == 1 ) {
				if($_SESSION['role_CMS'] >= 1 || $_SESSION['role_LMS'] >= 1) {

					// user rights 
					$bookitems = new Bookitems();
					$users_id = isset($_SESSION['users_id']) ? $_SESSION['users_id'] : 0;
					$users_rights = $bookitems->getBookitemsUsersRights($plugin_bookitems_category_id, $users_id);
					// groups rights
					$groups_rights = $bookitems->getBookitemsGroupsRightsAccess($plugin_bookitems_category_id);

					// read
					if($users_rights && $users_rights['rights_read'] == 1) {
						$acc_read = true;
					} else {
						if($groups_rights) {
							if(get_membership_rights('rights_read', $_SESSION['membership'], $groups_rights)) {
								$acc_read = true;
							}
						}
					}
					
					// edit
					if($users_rights && $users_rights['rights_edit'] == 1) {
						$acc_edit = true;
					} else {
						if($groups_rights) {
							if(get_membership_rights('rights_edit', $_SESSION['membership'], $groups_rights)) {
								$acc_edit = true;
							}
						}
					}

					// create
					if($users_rights && $users_rights['rights_create'] == 1) {
						$acc_create = true;
					} else {
						if($groups_rights) {												
							if(get_membership_rights('rights_create', $_SESSION['membership'], $groups_rights)) {
								$acc_create = true;
							}
						}
					}
					
				}
			}
		}

		/*
		echo '$acc_read:'.$acc_read .'<br />';
		echo '$acc_edit:'.$acc_edit .'<br />';
		echo '$acc_create:'.$acc_create .'<br />';
		*/

		$view = null;
		
		if(!$acc_read) {
			die();
		}
	
		// check date
		//$date = date('Y-m-d');
		$date = ($this->_isValidDate($date)) ?  $date : date('Y-m-d');
		
	
		// mark today
		$today = date('Y-m-d');
		// get year and month from timestamp
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
		$month = date('m', $timestamp);		
        // get days in month
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		// this month start weekday 
		$timestamp = mktime(0, 0, 0, $month, 1, $year);
        // weeks start on monday
		$start_weekday = (date('w', $timestamp)-1 < 0 ) ? 6 : date('w', $timestamp)-1;
		// calendar month 
		//$month_name = date('F Y', strtotime($date));
		$month_m = date('F', strtotime($date));
		$month_y = date('Y', strtotime($date));
		//$month_name = $month_m .' '.$month_y;
		$month_name = $this->transl($month_m) .' '.$month_y;
				
		$holidays = array();
		$flagdays = array();
		
		$previous = $next = null;
		
		$p = 'month';
		$previous = $href . date('Y-m-d',strtotime("- 1 month", $timestamp));
		$next = $href . date('Y-m-d',strtotime("+ 1 month", $timestamp));
		
		// $period		
		switch ($period) {
			case 'day':
				$p = 'day';
				$previous = $href . date('Y-m-d', strtotime($date . " -1 day"));
				$next = $href . date('Y-m-d', strtotime($date . " +1 day"));
			break;
			
			case '4days':
				$p = '4days';
				$previous = $href . date('Y-m-d', strtotime($date . " -1 day"));
				$next = $href . date('Y-m-d', strtotime($date . " +1 day"));
			break;

			case 'week':
				$p = 'week';
				$previous = $href . date('Y-m-d', strtotime($date . " -7 days"));
				$next = $href . date('Y-m-d', strtotime($date . " +7 days"));
			break;
			
			case '2weeks':
				$p = '2weeks';
				$previous = $href . date('Y-m-d', strtotime($date . " -7 days"));
				$next = $href . date('Y-m-d', strtotime($date . " +7 days"));
			break;
			
			case 'month':
				$p = 'month';
				$previous = $href . date('Y-m-d',strtotime("- 1 month", $timestamp));
				$next = $href . date('Y-m-d',strtotime("+ 1 month", $timestamp));
			break;
				
			default:
				$p = 'week';
				$previous = $href . date('Y-m-d', strtotime($date . " -7 days"));
				$next = $href . date('Y-m-d', strtotime($date . " +7 days"));
			break;
		}
		
		
		
		$periods = array("one day" => "day","four days" => "4days","one week" => "week","two weeks" => "2weeks","one month" => "month");
		$period_select = '<select id="period" class="calendar-bookitems-select">';
		foreach($periods as $key => $value) {
			$period_select .= '<option value="'.$value.'"';
			if($value==$p) {
				$period_select .= ' selected=selected';
			}
			$period_select .= '>'.$this->transl($key).'</option>';
		}
		$period_select .= '</select>';
		
		// calandar markup
		$html = "\n<div style=\"margin:0 auto;\">";
		$html .= "<input type=\"hidden\" id=\"init_date\" value=\"$date\" />";
		$width = ($max_width==true) ? "100%" : null;
		//$width = null;
		$v = $view['name'];
		$html .= "\n$v";
		$html .= "\n<table style=\"width:$width;\" class=\"calendar-bookitems\">";
		$html .= "\n\t<thead>";
		$html .= "\n\t\t<tr class=\"calendar-bookitems-nav\">";
				
			$html .= "\n\t\t\t<th style=\"width:3%;vertical-align:middle;text-align:left;padding-left:5px;\"><input type=\"text\" class=\"calendar-bookitems-input\" style=\"width:100px;\" value=\"$month_name\" id=\"datepicker_events\" /></th>";
			$html .= "\n\t\t\t<th class=\"\" style=\"width:3%;vertical-align:top;text-align:left;\"><button class=\"calendar-bookitems-change-view\"><span class=\"ui-icon ui-icon-refresh\">&nbsp;</span></button></th>";
			$html .= "\n\t\t\t<th class=\"\" style=\"text-align:right;vertical:middle;\">$period_select</th>";
			
			$html .= "\n\t\t\t<th class=\"\" style=\"width:3%;vertical-align:top;text-align:left;\"><button class=\"calendar-bookitems-change-view\" id=\"$date\"><span class=\"ui-icon ui-icon-refresh\">&nbsp;</span></button></th>";
			$html .= "\n\t\t\t<th class=\"\" style=\"width:15%;vertical-align:top;text-align:center;\"><button class=\"calendar_mybookings\"><span class=\"ui-icon ui-icon-clipboard\">&nbsp;</span></button></th>";
			$html .= "\n\t\t\t<th style=\"vertical-align:top;text-align:right;padding-right:5px;\"><button class=\"calendar-bookitems-change-view\" id=\"$previous\"><span class=\"ui-icon ui-icon-triangle-1-w\">&nbsp;</span></button><button class=\"calendar-bookitems-change-view\" id=\"$next\"><span class=\"ui-icon ui-icon-triangle-1-e\">&nbsp;</span></button></th>";
			
		
		$html .= "\n\t\t</tr>";
		$html .= "\n\t</thead>";
		$html .= "\n\t</table>";
		$html .= "\n\t<table style=\"width:$width;\" class=\"calendar-bookitems\">";
		$html .= "\n\t<thead>";
		$html .= "\n\t\t<tr>";
		$html .= "\n\t\t\t";
		$html .= '<th class="calendar-bookitems-thead">&nbsp;<span class="ajax_calendar_load" style="display:none;"><img src="'.CMS_DIR.'/content/plugins/bookitems/css/images/spinner_big.gif" style="width:25px;height:auto;" alt="spinner"></span></th>';
		
		$size = count($units_meta);

		foreach($units_meta as $units) {
			if($units['active'] == 0) {
				$size = $size-1;
			}		
		}

		
		foreach($units_meta as $units) {
			if($units['active'] == 0) {
				continue;
			}		
			$category = $units['title'];
			$descr = '<div class="units_description">'.$units['description'].'</div>';
			$image = is_file(CMS_ABSPATH .'/content/uploads/plugins/bookitems/'.$units['image']) ? CMS_DIR .'/content/uploads/plugins/bookitems/'.$units['image'] : null;
			$width = floor(90/$size);
			$icon = $units['active'] == 1 ? '' : '<span class="ui-icon ui-icon-key" style="display:inline-block;"></span>';
			$html .= "\n\t\t\t<th style=\"width:$width%;\" class=\"calendar-bookitems-thead\">$category $icon<br /><img src=\"$image\" />$descr</th>";
		}
				
		$html .= "\n\t\t</tr>";
		$html .= "\n\t<tbody>";
		

		switch($p) {


			case 'day':
			case '4days':

				$cc = ($p == '4days') ? 4 : 1;

				// current date
				$ts = strtotime($date);
				
				// one or 4 days
				for ($ii = 1; $ii <= $cc; $ii++) {

					if($ii > 1) {
						$ts = strtotime("+1 day", $ts);
					}

					// class style		
					$tr_class = (date('w', $ts)==0 || date('w', $ts)==6) ? ' class="calendar-bookitems-weekend"' : null;
					if(date('Y-m-d', $ts)==$today) {
						$tr_class = ' class="calendar-bookitems-today"';
					}
				
					// tr id = date
					$dateid = date('Y-m-d', $ts);
				
					$html .= "\n\t\t<tr $tr_class id=\"$dateid\">";
						
						$class = null;
						$title_holiday = null;				
						foreach($holidays as $key => $value) {
							if(date('Y-m-d', $ts) == $key) {
								$class = "holiday";
								$title_holiday = '<br /><span style="font-size:0.8em;">'.$value.'<br />';
								break;
							}
						}
						$flagday = null;
						foreach($flagdays as $key => $value) {
							if(date('Y-m-d', $ts) == $key) {
								$flagday = '<img src="css/images/svensk_flagga.gif" title="'.$value.'" />&nbsp;';
								break;
							}
						}

						$wnr = (date('w', $ts)-1) == 0 ? '<span style="font-size:0.8em;">v'.date('W',$ts).'&nbsp;&nbsp;&nbsp;</span>' : null;
						//$day = date('D',$ts);
						$day = $this->transl(date('D',$ts));
						
						$qw = $wnr . $day .'<br />';
						
						$html .=  "\n\t\t\t<td class=\"calendar-bookitems-date\" style=\"max-width:100px;\">&nbsp;<a class=\"$class\">".$qw.$flagday.date('j', $ts).$title_holiday."</a></td>";
						
					
						foreach($units_meta as $units) {
							
							if($units['active'] == 0) {
								continue;
							}
							$unit_id = $units['plugin_bookitems_unit_id'];
							$unit_title = $units['title'];
							$unit_image = $units['image'];
							$unit_description = $units['description'];
							
							$hide_icon = false;
							$box = '';
							for($i = 0; $i < count($items); ++$i) {
								if($units['plugin_bookitems_unit_id'] == $items[$i]['plugin_bookitems_unit_id']) {
								
									$start = strtotime($items[$i]['utc_start']);
									$slut = strtotime($items[$i]['utc_end']);
									$booked = $this->booked($ts, $start, $slut, $unit_id, $unit_title, $unit_image, $items[$i]['plugin_bookitems_id'], $items[$i]['title'], $items[$i]['description'], $items[$i]['first_name'], $items[$i]['last_name'], $items[$i]['email'], $items[$i]['users_id'], $acc_edit);
									$box .= $booked;
									
								}
							}

							$a_title = $acc_read ? $units['title'] : 'no rights';
							$a_class = $acc_read ? 'bookitems' : '';
							
							$a_icon = $acc_create ? '<div><a class="'.$a_class.'" id="'.$unit_id.'" data-title="'.$unit_title.'" data-description="'.$unit_description.'" data-image="'.$unit_image.'" data-edit="0" style="float:none;"><span class="ui-icon ui-icon-document-b" style="display:inline-block;"></span></a></div>' : '';
							$html .=  "\n\t\t\t<td class=\"calendar-bookitems\" title=\"$a_title\"><div>".$a_icon."</div>".$box."</td>";
							
						}
					
						
					$html .= "\n\t\t</tr>";
				}


			break;




			case 'week':
			case '2weeks':
				
				
				$start = date('w', strtotime($date)) == 1 ? strtotime($date) : strtotime("last monday", strtotime($date));
				$one_day = $start;
				
				$end = ($p == 'week') ? strtotime("next sunday", strtotime($date)) : strtotime("+13 days", strtotime($date));
				
				while($one_day <= $end) {
				
					// current date
					$ts = $one_day;
					
					// class style		
					$tr_class = (date('w', $ts)==0 || date('w', $ts)==6) ? ' class="calendar-bookitems-weekend"' : null;
					if(date('Y-m-d', $ts)==$today) {
						$tr_class = ' class="calendar-bookitems-today"';
					}
				
					// tr id = date
					$dateid = date('Y-m-d', $ts);
				
					$html .= "\n\t\t<tr $tr_class id=\"$dateid\">";

					
						$class = null;
						$title_holiday = null;				
						foreach($holidays as $key => $value) {
							if(date('Y-m-d', $ts) == $key) {
								$class = "holiday";
								$title_holiday = '<br /><span style="font-size:0.8em;">'.$value.'<br />';
								break;
							}
						}
						$flagday = null;
						foreach($flagdays as $key => $value) {
							if(date('Y-m-d', $ts) == $key) {
								$flagday = '<img src="css/images/svensk_flagga.gif" title="'.$value.'" />&nbsp;';
								break;
							}
						}

						$wnr = (date('w', $ts)-1) == 0 ? '<span style="font-size:0.8em;">v'.date('W',$ts).'&nbsp;&nbsp;&nbsp;</span>' : null;
						//$day = date('D',$ts);
						$day = $this->transl(date('D',$ts));
						$qw = $wnr . $day .'<br />';
						
						$html .=  "\n\t\t\t<td class=\"calendar-bookitems-date\" style=\"max-width:100px;\">&nbsp;<a class=\"$class\">".$qw.$flagday.date('j', $ts).$title_holiday."</a></td>";
						
						foreach($units_meta as $units) {
							
							if($units['active'] == 0) {
								continue;
							}
							$unit_id = $units['plugin_bookitems_unit_id'];
							$unit_title = $units['title'];
							$unit_image = $units['image'];
							$unit_description = $units['description'];
							
							$hide_icon = false;
							$box = '';
							for($i = 0; $i < count($items); ++$i) {
								if($units['plugin_bookitems_unit_id'] == $items[$i]['plugin_bookitems_unit_id']) {
								
									$start = strtotime($items[$i]['utc_start']);
									$slut = strtotime($items[$i]['utc_end']);
									$booked = $this->booked($ts, $start, $slut, $unit_id, $unit_title, $unit_image, $items[$i]['plugin_bookitems_id'], $items[$i]['title'], $items[$i]['description'], $items[$i]['first_name'], $items[$i]['last_name'], $items[$i]['email'], $items[$i]['users_id'], $acc_edit);
									$box .= $booked;
									
								}
							}
					
							$a_title = $acc_read ? $units['title'] : 'no rights';
							$a_class = $acc_read ? 'bookitems' : '';
							
							$a_icon = $acc_create ? '<div><a class="'.$a_class.'" id="'.$unit_id.'" data-title="'.$unit_title.'" data-description="'.$unit_description.'" data-image="'.$unit_image.'" data-edit="0" style="float:none;"><span class="ui-icon ui-icon-document-b" style="display:inline-block;"></span></a></div>' : '';
							$html .=  "\n\t\t\t<td class=\"calendar-bookitems\" title=\"$a_title\"><div>".$a_icon."</div>".$box."</td>";					

						}
						
						$one_day = strtotime("+1 day", $one_day);
						//$c++;
					$html .= "\n\t\t</tr>";
				}
			break;


			
			
			case 'month':

		
				for($i=1, $c=1, $today, $m=date('m', $timestamp), $y=date('Y', $timestamp); $c <= $days_in_month; ++$i ) {
				
					// current date
					$ts = strtotime($y.'-'.$m.'-'.sprintf("%02d",$c));
					// class style		
					$tr_class = (date('w', $ts)==0 || date('w', $ts)==6) ? ' class="calendar-bookitems-weekend"' : null;
					if(date('Y-m-d', $ts)==$today) {
						$tr_class = ' class="calendar-bookitems-today"';
					}
				
					// tr id = date
					$dateid = date('Y-m-d', $ts);
				
					$html .= "\n\t\t<tr $tr_class id=\"$dateid\">";
						
						$class = null;
						$title_holiday = null;				
						foreach($holidays as $key => $value) {
							if(date('Y-m-d', $ts) == $key) {
								$class = "holiday";
								$title_holiday = '<br /><span style="font-size:0.8em;">'.$value.'<br />';
								break;
							}
						}
						$flagday = null;
						foreach($flagdays as $key => $value) {
							if(date('Y-m-d', $ts) == $key) {
								$flagday = '<img src="css/images/svensk_flagga.gif" title="'.$value.'" />&nbsp;';
								break;
							}
						}

						$wnr = (date('w', $ts)-1) == 0 ? '<span style="font-size:0.8em;">v'.date('W',$ts).'&nbsp;&nbsp;&nbsp;</span>' : null;
						//$day = date('D',$ts);
						$day = $this->transl(date('D',$ts));
						$qw = $wnr . $day .'<br />';
						
						$html .=  "\n\t\t\t<td class=\"calendar-bookitems-date\" style=\"max-width:100px;\">&nbsp;<a class=\"$class\">".$qw.$flagday.date('j', $ts).$title_holiday."</a></td>";
						
						foreach($units_meta as $units) {
							
							if($units['active'] == 0) {
								continue;
							}
							$unit_id = $units['plugin_bookitems_unit_id'];
							$unit_title = $units['title'];
							$unit_image = $units['image'];
							$unit_description = $units['description'];
							
							$hide_icon = false;
							$box = '';
							if($acc_read) {
								for($i = 0; $i < count($items); ++$i) {
									if($units['plugin_bookitems_unit_id'] == $items[$i]['plugin_bookitems_unit_id']) {
									
										$start = strtotime($items[$i]['utc_start']);
										$slut = strtotime($items[$i]['utc_end']);
										$booked = $this->booked($ts, $start, $slut, $unit_id, $unit_title, $unit_image, $items[$i]['plugin_bookitems_id'], $items[$i]['title'], $items[$i]['description'], $items[$i]['first_name'], $items[$i]['last_name'], $items[$i]['email'], $items[$i]['users_id'], $acc_edit);
										$box .= $booked;
										
									}
								}
							}
							
							$a_title = $acc_read ? $units['title'] : 'no rights';
							$a_class = $acc_read ? 'bookitems' : '';
							
							$a_icon = $acc_create ? '<div><a class="'.$a_class.'" id="'.$unit_id.'" data-title="'.$unit_title.'" data-description="'.$unit_description.'" data-image="'.$unit_image.'" data-edit="0" style="float:none;"><span class="ui-icon ui-icon-document-b" style="display:inline-block;"></span></a></div>' : '';
							$html .=  "\n\t\t\t<td class=\"calendar-bookitems\" title=\"$a_title\"><div>".$a_icon."</div>".$box."</td>";
							
						}
						
						$c++;
					$html .= "\n\t\t</tr>";
				}
			
			break;
		
		}
		
		
		$html .= "\n\t</tbody>";
		$html .= "\n\t</table>\n";
		$html .= "\n</div>";
		

		$scr = "";
		$html .= $scr;
		return $html;
	}
	
	
	private function transl($text) {
		$a = array(
			"english" => array("Mon" => "Mon", "Tue" => "Tue", "Wed" => "Wed", "Thu" => "Thu", "Fri" => "Fri", "Sat" => "Sat", "Sun" => "Sun", 
								"Monday" => "Monday", "Tuesday" => "Tuesday", "Wednesday" => "Wednesday", "Thursday" => "Thursday", "Friday" => "Friday", "Saturday" => "Saturday", "Sunday" => "Sunday", 
								"one day" => "one day", "four days" => "four days", "one week" => "one week", "two weeks" => "two weeks", "one month" => "one month",
								"January" => "January", "February" => "February", "March" => "March", "April" => "April", "May" => "May", "June" => "June", "July" => "July", "August" => "August", "September" => "September", "October" => "October", "November" => "November", "December" => "December",
								"w" => "w"), 
			"swedish" => array("Mon" => "må", "Tue" => "ti", "Wed" => "on", "Thu" => "to", "Fri" => "fr", "Sat" => "lö", "Sun" => "sö",
								"Monday" => "Måndag", "Tuesday" => "Tisdag", "Wednesday" => "Onsdag", "Thursday" => "Torsdag", "Friday" => "Fredag", "Saturday" => "Lördag", "Sunday" => "Söndag", 
								"one day" => "en dag", "four days" => "fyra dagar", "one week" => "en vecka", "two weeks" => "två veckor", "one month" => "en månad",
								"January" => "Januari", "February" => "Februari", "March" => "Mars", "April" => "April", "May" => "Maj", "June" => "Juni", "July" => "Juli", "August" => "Augusti", "September" => "September", "October" => "Oktober", "November" => "November", "December" => "December",
								"w" => "v"));

		$l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
		if(!$l) {
			$l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
		} 
		$s = $l ? $a[$l][$text] : $text;
		return $s;
	}
	
	
	
	// $ts timestamp
	private function booked($ts, $start, $slut, $unit_id, $unit_title, $unit_image, $plugin_bookitems_id, $plugin_bookitems_title, $plugin_bookitems_description, $first_name, $last_name, $email, $users_id, $acc_edit) {	

		$start_dag = date('Y-m-d', $start);
		$start_datetime = date('Y-m-d H:i', $start);
		$start_time = date('H:i', $start);
		
		$slut_dag = date('Y-m-d', $slut);
		$slut_datetime = date('Y-m-d H:i', $slut);
		$slut_time = date('H:i', $slut);

		$user = 'Bokad av: '.$first_name.' '.$last_name.', '.$email;

		$edit = ($_SESSION['users_id'] == $users_id || $acc_edit ==1) ? ' <a class="bookitems" id="'.$unit_id.'" data-title="'.$unit_title.'" data-image="'.$unit_image.'" data-edit="'.$plugin_bookitems_id.'" style="float:none;"><span class="ui-icon ui-icon-pencil" style="display:inline-block;margin:-3px 0;"></span></a>' : '';
		
		$html = '';
		
		if(date('Y-m-d', $ts) == $start_dag && date('Y-m-d', $ts) == $slut_dag) {
			$html .= '<div class="box_boka" title="'.$user.'">';
			
			$html .= $start_time . $edit;
			$html .= '<div><span class="box_boka_titel">'.$plugin_bookitems_title.'</span></div><div class="box_boka_beskrivning">'.$plugin_bookitems_description.'</div>';
			$html .= $slut_time;
			$html .= '</div>';
		} elseif ( date('Y-m-d', $ts) == $start_dag && $slut_dag > date('Y-m-d', $ts) ) {
			
			$html .= '<div class="box_boka_open_pre" title="'.$user.'">';
			$html .= $start_time . $edit;
			$html .= '<div><span class="bok_boka_titel">'.$plugin_bookitems_title.'</span></div><div class="box_boka_beskrivning">'.$plugin_bookitems_description.'</div>';
			$html .= '</div>';

			$html .= '<div class="box_boka_open">';
			$html .= $slut_datetime;
			$html .= '</div>';

		} elseif ( date('Y-m-d', $ts) == $slut_dag && $start_dag < date('Y-m-d', $ts) ) {

			$start_datetime = date('j/n', $start);										
			$slut_datetime = date('j/n', $slut);

			$html .= '<div class="box_boka_open" title="'.$user.'">';
			$html .= $start_datetime;
			$html .= ' — ' .$slut_datetime;
			$html .= '</div>';

			$html .= '<div class="box_boka_open_post">';
			$html .= '<div><span class="bok_boka_titel">'.$plugin_bookitems_title.'</span></div><div class="box_boka_beskrivning">'.$plugin_bookitems_description.'</div>';
			$html .= $slut_time . $edit;
			$html .= '</div>';

		} elseif ( date('Y-m-d', $ts) > $start_dag && date('Y-m-d', $ts) < $slut_dag ) {

			$start_datetime = date('j/n', $start);										
			$slut_datetime = date('j/n', $slut);
		
			$html .= '<div class="box_boka_open" title="'.$user.'">';
			$html .= $start_datetime;
			$html .= ' — ' .$slut_datetime . $edit;
			$html .= '</div>';
			//$hide_icon = true;
			
		} else {
		
		}
		return $html;

	}
	
}
?>