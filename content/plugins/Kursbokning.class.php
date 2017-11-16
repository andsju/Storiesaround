<?php

/**
 * API for class Kursbokning
 * extends Plugins class
 */

class Kursbokning extends Plugins {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'Kursbokning';
		$a['classname'] = 'Kursbokning';
		$a['description'] = 'Visa plugin Kursbokning ';
		$a['css'] = CMS_DIR.'/content/plugins/kursbokning/css/style.css';
		// replace data in area(s) (comma-separated): 'header,left_sidebar,content,right_sidebar,footer,page'  // use blank space pre and post words!
		$a['area'] = ' content ';
		$a['info'] = 'This page uses "Kursbokning - plugin". Widgets and stories are not shown in area "content".';
		$a['types'] = 'Folkhögskolekurs,Uppdragsutbildning,Evenemang';

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
	
	// generate a random string using echo create_random_string(8);
	public function create_random_string($length) {
		$chars = "0123456789";
		return substr(str_shuffle($chars),0,$length);
	}
	
	public function db_sql() {
		$sqls = array();
		$sqls[] = "CREATE TABLE IF NOT EXISTS `plugin_kursbokning` (
			`plugin_kursbokning_id` int(11) NOT NULL AUTO_INCREMENT,
			`title` varchar(255) DEFAULT '' COMMENT 'title',
			`description` varchar(1000) DEFAULT '' COMMENT 'description',
			`type` varchar(100) DEFAULT '' COMMENT 'type',
			`terms` varchar(10000) DEFAULT '' COMMENT 'terms',
			`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:not active 1:active',
			`web_reservation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:false 1:true',
			`email_notify` varchar(100) DEFAULT '' COMMENT 'notify',
			`fields` varchar(1000) DEFAULT '' COMMENT 'field includes delimiter separated',
			`file_upload` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Let user upload files',
			`file_action` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'File action after uploads and possible email attach',
			`file_attach` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Attach files to administration email',
			`file_instruction` varchar(1000) DEFAULT '' COMMENT 'File upload instruction',
			`file_move_path` varchar(255) DEFAULT '' COMMENT 'Set file move path to uploaded files (move action must be set)',
			`file_path` varchar(255) DEFAULT '' COMMENT 'Set file path to uploaded files',
			`file_extensions` varchar(255) DEFAULT 'txt, pdf, doc, docx, rtf, jpg, png, gif' COMMENT 'Set allowed upload file extensions, comma separated',
			`utc_start_publish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_end_publish` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`plugin_kursbokning_id`)
		)";

		$sqls[] = "CREATE TABLE IF NOT EXISTS `plugin_kursbokning_fields` (
			`plugin_kursbokning_fields_id` int(11) NOT NULL AUTO_INCREMENT,
			`plugin_kursbokning_id` int(11) NOT NULL DEFAULT '0',
			`label` varchar(1000) DEFAULT '' COMMENT 'label',
			`description` varchar(1000) DEFAULT '' COMMENT 'description',
			`field` varchar(25) DEFAULT '' COMMENT 'field type',
			`field_values` varchar(1000) DEFAULT '' COMMENT 'field values delimiter separated',
			`required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:false 1:true',
			`sort_id` int(3) NOT NULL DEFAULT '0' COMMENT 'sort id order',
			`utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`plugin_kursbokning_fields_id`)
		)";

		$sqls[] = "CREATE TABLE IF NOT EXISTS `plugin_kursbokning_kurs` (
			`plugin_kursbokning_kurs_id` int(11) NOT NULL AUTO_INCREMENT,
			`plugin_kursbokning_id` int(11) NOT NULL DEFAULT '0',
			`title` varchar(255) DEFAULT '' COMMENT 'title',
			`description` varchar(1000) DEFAULT '' COMMENT 'description',
			`type` varchar(100) DEFAULT '' COMMENT 'type',
			`location` varchar(1000) DEFAULT '' COMMENT 'location',
			`cost` varchar(50) DEFAULT '' COMMENT 'cost',
			`participants` int(3) NOT NULL DEFAULT '0' COMMENT 'participants',
			`show_participants` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Show the number participants in form',
			`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:inactive 1:active',
			`web_reservation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:false 1:true',
			`notify` varchar(255) DEFAULT '' COMMENT 'notify mail',
			`utc_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`plugin_kursbokning_kurs_id`)
		)";

		$sqls[] = "CREATE TABLE IF NOT EXISTS `plugin_kursbokning_kurs_anmalan` (
			`plugin_kursbokning_kurs_anmalan_id` int(11) NOT NULL AUTO_INCREMENT,
			`plugin_kursbokning_id` int(11) NOT NULL DEFAULT '0',
			`plugin_kursbokning_kurser_id` varchar(100) DEFAULT '' COMMENT 'kurs_id sökta kurser',
			`fnamn` varchar(100) DEFAULT '' COMMENT 'förnamn',
			`enamn` varchar(100) DEFAULT '' COMMENT 'efternamn',
			`personnummer` varchar(11) DEFAULT '' COMMENT 'personnummer',
			`personnummer_yyyy` varchar(13) DEFAULT '' COMMENT 'personnummer YYYYMMDD-XXXX',
			`epost` varchar(100) DEFAULT '' COMMENT 'epost',
			`adress` varchar(100) DEFAULT '' COMMENT 'adress',
			`postnummer` int(5) DEFAULT '0' COMMENT 'postnummer',
			`ort` varchar(100) DEFAULT '' COMMENT 'ort',
			`mobil` varchar(25) DEFAULT '' COMMENT 'mobil',
			`telefon` varchar(25) DEFAULT '' COMMENT 'telefon',
			`kommun` varchar(25) DEFAULT '' COMMENT 'kommun',
			`lan` varchar(25) DEFAULT '' COMMENT 'län',
			`country` varchar(25) DEFAULT '' COMMENT 'land',
			`organisation` varchar(100) DEFAULT '' COMMENT 'organisation, företag, förening',
			`fakturaadress` varchar(255) DEFAULT '' COMMENT 'fakturaadress',
			`kod` varchar(25) DEFAULT '' COMMENT 'kod för kursanmalan',
			`ip` varchar(25) DEFAULT '' COMMENT 'ip adress',
			`agent` varchar(100) DEFAULT '' COMMENT 'HTTP_USER_AGENT',
			`token` varchar(100) DEFAULT '' COMMENT 'token',
			`questions` text COMMENT 'anpassade frågor med svar',
			`files` varchar(1000) DEFAULT '' COMMENT 'Uploaded filenames, comma separated',
			`notes` text COMMENT 'noteringar för administration',
			`log` text COMMENT 'log förändringar',
			`utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_admitted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_confirmed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_canceled` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_exported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_deleted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',	  
			PRIMARY KEY (`plugin_kursbokning_kurs_anmalan_id`)
		)";

		$sqls[] = "CREATE TABLE IF NOT EXISTS `plugin_kursbokning_bokningskod` (
			`plugin_kursbokning_bokningskod_id` int(11) NOT NULL AUTO_INCREMENT,		  
			`epost` varchar(100) DEFAULT '' COMMENT 'epost',
			`kod` varchar(100) DEFAULT '' COMMENT 'kod',
			`utc_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`utc_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`plugin_kursbokning_bokningskod_id`)
		)";		
		return $sqls;
	}


	public function db_sql_update() {

		$sqls = array();
		//$sqls[] = "ALTER TABLE `plugin_kursbokning_kurs_anmalan` ADD COLUMN `organisation` varchar(100) DEFAULT '' COMMENT 'organisation, företag, förening' AFTER `country`";
		return $sqls;
	}
	
	

	/**
	*
	* @return lastInsertId('plugin_kursbokning_id')
	*/
    public function setKursbokningFormInsert($title, $type, $utc_modified) {
		try {
			$sql = "INSERT INTO plugin_kursbokning 
			(title, type, utc_modified) VALUES
			(:title, :type, :utc_modified)";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->execute();
			return $this->db->lastInsertId('plugin_kursbokning_id');
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	


	/**
	*
	* @return row
	*/
    public function getKursbokningId($plugin_kursbokning_id) {
		try {
			$sql = "SELECT * FROM plugin_kursbokning 
			WHERE
			plugin_kursbokning_id = :plugin_kursbokning_id;";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	


	/**
	*
	* @return lastInsertId('plugin_kursbokning_bokningskod_id')
	*/
    public function setKursbokningBokningskodInsert($epost, $kod, $utc_created) {
		try {
			$sql = "INSERT INTO plugin_kursbokning_bokningskod 
			(epost, kod, utc_created) VALUES
			(:epost, :kod, :utc_created)";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
			$stmt->bindParam(':kod', $kod, PDO::PARAM_STR);
			$stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
			$stmt->execute();
			return $this->db->lastInsertId('plugin_kursbokning_bokningskod_id');
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	

	/**
	*
	* @return row
	*/
    public function getKursbokningBokningskod($epost, $kod) {
		try {
			$sql = "SELECT * FROM plugin_kursbokning_bokningskod 
			WHERE
			epost = :epost
			AND
			kod = :kod
			ORDER BY 
			plugin_kursbokning_bokningskod_id DESC
			LIMIT 1";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
			$stmt->bindParam(':kod', $kod, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	


	/**
	*
	* @return row
	*/
    public function getKursbokningKursAnmalan($epost, $kod) {
		try {
			$sql = "SELECT * FROM plugin_kursbokning_kurs_anmalan 
			WHERE
			epost = :epost
			AND
			kod = :kod
			ORDER BY 
			plugin_kursbokning_kurs_anmalan_id DESC
			LIMIT 1";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
			$stmt->bindParam(':kod', $kod, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	



	/**
	*
	* @return row
	*/
    public function getKursbokningIdKurserWebbPublicering($plugin_kursbokning_id) {
		try {
			$sql = "SELECT * FROM plugin_kursbokning_kurs 
			WHERE
			plugin_kursbokning_id = :plugin_kursbokning_id
			AND status = 1 AND web_reservation = 1
			AND 
			( SELECT NOW() < utc_end OR utc_end IS NULL)
			ORDER BY title ASC";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	

	
	/**
	*
	* @return row
	*/
    public function getKursbokningIdKurser($plugin_kursbokning_id) {
		try {
			$sql = "SELECT * FROM plugin_kursbokning_kurs 
			WHERE
			plugin_kursbokning_id = :plugin_kursbokning_id
			ORDER BY title ASC";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}
	

	/**
	*
	* @return row
	*/
    public function getKursbokningFormId($plugin_kursbokning_kurs_id) {
		try {
			$sql = "SELECT plugin_kursbokning_id FROM plugin_kursbokning_kurs 
			WHERE
			plugin_kursbokning_kurs_id = :plugin_kursbokning_kurs_id;";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_kurs_id', $plugin_kursbokning_kurs_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	

	
	/**
	*
	* @params
	*/
    public function setFormUpdate($plugin_kursbokning_id, $title, $terms, $status, $web_reservation, $email_notify, $type, $utc_start_publish, $utc_end_publish, $file_upload, $file_instruction, $file_extensions, $file_path, $file_attach, $file_action, $file_move_path, $fields, $utc_modified) {

		try {
			$sql = "UPDATE plugin_kursbokning
			SET title = :title,
			terms = :terms,
			status = :status,
			file_upload = :file_upload,
			file_instruction = :file_instruction,
			file_extensions = :file_extensions,
			file_path = :file_path,
			file_attach = :file_attach,
			file_action = :file_action,
			file_move_path = :file_move_path,
			fields = :fields,
			web_reservation = :web_reservation,
			email_notify = :email_notify,
			type = :type,
			utc_start_publish = :utc_start_publish,
			utc_end_publish = :utc_end_publish,
			utc_modified = :utc_modified
			WHERE plugin_kursbokning_id = :plugin_kursbokning_id";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':terms', $terms, PDO::PARAM_STR);
			$stmt->bindParam(':status', $status, PDO::PARAM_INT);
			$stmt->bindParam(':file_upload', $file_upload, PDO::PARAM_INT);
			$stmt->bindParam(':file_instruction', $file_instruction, PDO::PARAM_STR);
			$stmt->bindParam(':file_extensions', $file_extensions, PDO::PARAM_STR);
			$stmt->bindParam(':file_path', $file_path, PDO::PARAM_STR);
			$stmt->bindParam(':file_attach', $file_attach, PDO::PARAM_INT);
			$stmt->bindParam(':file_action', $file_action, PDO::PARAM_INT);
			$stmt->bindParam(':file_move_path', $file_move_path, PDO::PARAM_STR);
			$stmt->bindParam(':web_reservation', $web_reservation, PDO::PARAM_INT);
			$stmt->bindParam(':email_notify', $email_notify, PDO::PARAM_STR);
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);
			$stmt->bindParam(':fields', $fields, PDO::PARAM_STR);
			$stmt->bindParam(':utc_start_publish', $utc_start_publish, PDO::PARAM_STR);
			$stmt->bindParam(':utc_end_publish', $utc_end_publish, PDO::PARAM_STR);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	


	
	/**
	*
	* TODO - delete when no data saved
	*/
	
    public function setKursbokningFormDelete($plugin_kursbokning_id) {
		try {
			$stmt = $this->db->prepare("DELETE FROM plugin_kursbokning WHERE plugin_kursbokning_id = :plugin_kursbokning_id");
			$stmt->bindParam(":plugin_kursbokning_id", $plugin_kursbokning_id, PDO::PARAM_INT);
			return $stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	

	
	/**
	*
	* @return row
	*/
    public function getKursbokningFieldsId($plugin_kursbokning_id) {
		try {
			$sql = "SELECT * FROM plugin_kursbokning_fields 
			WHERE
			plugin_kursbokning_id = :plugin_kursbokning_id
			ORDER BY sort_id ASC";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	


	/**
	*
	* @return row
	*/
    public function getKursbokningFieldId($plugin_kursbokning_fields_id) {
		try {
			$sql = "SELECT * FROM plugin_kursbokning_fields 
			WHERE
			plugin_kursbokning_fields_id = :plugin_kursbokning_fields_id
			ORDER BY sort_id ASC";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_fields_id', $plugin_kursbokning_fields_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	
	
	/**
	*
	* @return lastInsertId plugin_kursbokning_fields_id
	*/
    public function setFormFieldInsert($plugin_kursbokning_id, $label, $field, $utc_modified) {
		try {
			$sql = "INSERT INTO plugin_kursbokning_fields 
			(plugin_kursbokning_id, label, field, utc_modified) VALUES
			(:plugin_kursbokning_id, :label, :field, :utc_modified)";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
			$stmt->bindParam(':label', $label, PDO::PARAM_STR);
			$stmt->bindParam(':field', $field, PDO::PARAM_STR);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->execute();
			return $this->db->lastInsertId('plugin_kursbokning_fields_id');
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	



	/**
	*
	* @param('plugin_kursbokning_fields_id')
	*/
    public function setFormFieldDelete($plugin_kursbokning_fields_id) {
		try {
			
			$stmt = $this->db->prepare("DELETE FROM plugin_kursbokning_fields WHERE plugin_kursbokning_fields_id = :plugin_kursbokning_fields_id");
			$stmt->bindParam(":plugin_kursbokning_fields_id", $plugin_kursbokning_fields_id, PDO::PARAM_INT);
			return $stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	
	
	
	
	/**
	*
	* @params
	*/
    public function setFormFieldUpdate($plugin_kursbokning_fields_id, $label, $field_values, $required, $utc_modified) {
		try {
			$sql = "UPDATE plugin_kursbokning_fields
			SET label = :label,
			field_values = :field_values,
			required = :required,
			utc_modified = :utc_modified
			WHERE plugin_kursbokning_fields_id = :plugin_kursbokning_fields_id";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_fields_id', $plugin_kursbokning_fields_id, PDO::PARAM_INT);
			$stmt->bindParam(':label', $label, PDO::PARAM_STR);
			$stmt->bindParam(':field_values', $field_values, PDO::PARAM_STR);
			$stmt->bindParam(':required', $required, PDO::PARAM_INT);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	

	
	/**
	*
	* @param array $pages_id_array
	* @return result
	*/
    public function setFieldPositionUpdate($plugin_kursbokning_fields_id_array) {
		
		try {		
			// use beginTransaction > commit > rollBack
			$this->db->beginTransaction();
			$stmt = $this->db->prepare("UPDATE plugin_kursbokning_fields SET sort_id =:position WHERE plugin_kursbokning_fields_id =:plugin_kursbokning_fields_id");
			$stmt->bindParam(":plugin_kursbokning_fields_id", $plugin_kursbokning_fields_id, PDO::PARAM_INT);
			$stmt->bindParam(":position", $position, PDO::PARAM_INT);
			// counter
			$position = 0;
			foreach ($plugin_kursbokning_fields_id_array as $plugin_kursbokning_fields_id) {
				$position = $position + 1;
				$stmt->execute();
			}
			$this->db->commit();
		
		} catch(PDOException $e) {
			$this->db->rollBack();
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}
	}
	
	
		
	/**
	*
	* getReservations
	* @return row
	*/
    public function getForm() {
		$s = '';
		return $s;		
	}	

	/**
	*
	* getReservations
	* @return row
	*/
    public function getKursbokning() {
		$sql = "SELECT * 
		FROM plugin_kursbokning";
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}	


	/**
	*
	* 
	* @return row
	*/
    public function getKursbokningWebbPublicering($plugin_kursbokning_id) {
		$sql = "SELECT * 
		FROM plugin_kursbokning
		WHERE status = 1 AND web_reservation = 1";
		if($plugin_kursbokning_id) {
			$sql .= " AND plugin_kursbokning_id = :plugin_kursbokning_id"; 
		}
		$sql .= " AND (SELECT NOW() BETWEEN utc_start_publish AND utc_end_publish 
		OR NOW() > utc_start_publish AND utc_end_publish IS NULL)
		ORDER BY title ASC";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}	
	

	/**
	*
	* @return lastInsertId('plugin_kursbokning_kurs_id')
	*/
    public function setKursbokningKursInsert($title, $type, $utc_modified) {
		try {
			$sql = "INSERT INTO plugin_kursbokning_kurs 
			(title, type, utc_modified) VALUES
			(:title, :type, :utc_modified)";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':type', $type, PDO::PARAM_STR);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->execute();
			return $this->db->lastInsertId('plugin_kursbokning_kurs_id');
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	



	/**
	*
	* TODO - delete when no data saved
	*/
	
    public function setKursbokningCourseDelete($plugin_kursbokning_kurs_id) {
		
		
		$row = $this->getKursbokningKurserAnmalanCount($plugin_kursbokning_kurs_id);
		if($row) {
			if($row['count']==0) {		
				
				try {
					$stmt = $this->db->prepare("DELETE FROM plugin_kursbokning_kurs WHERE plugin_kursbokning_kurs_id = :plugin_kursbokning_kurs_id");
					$stmt->bindParam(":plugin_kursbokning_kurs_id", $plugin_kursbokning_kurs_id, PDO::PARAM_INT);
					return $stmt->execute();
				
				} catch(PDOException $e) {		
					handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
				}
			}
		}
	}	
	

	

	/**
	*
	* getReservations
	* @return row
	*/
    public function getKursbokningKurs() {
		$sql = "SELECT plugin_kursbokning_kurs.title AS title, plugin_kursbokning_kurs.type, plugin_kursbokning_kurs.status, plugin_kursbokning_kurs.web_reservation, plugin_kursbokning_kurs.utc_start, plugin_kursbokning_kurs.utc_end, plugin_kursbokning_kurs.plugin_kursbokning_id, plugin_kursbokning.title AS title_form, plugin_kursbokning_kurs.plugin_kursbokning_kurs_id 
		FROM plugin_kursbokning_kurs
		LEFT JOIN plugin_kursbokning
		ON plugin_kursbokning_kurs.plugin_kursbokning_id=plugin_kursbokning.plugin_kursbokning_id";
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}	
	
	
	/**
	*
	* @param('plugin_kursbokning_id')
	* @param('plugin_kursbokning_kurs_ids')
	*/
    public function setKursbokningKurserUpdate($plugin_kursbokning_id, $plugin_kursbokning_kurs_ids) {
		try {
			$sql = "UPDATE plugin_kursbokning_kurs
			SET plugin_kursbokning_id = :plugin_kursbokning_id
			WHERE plugin_kursbokning_kurs_id IN ($plugin_kursbokning_kurs_ids )";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
			return $stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	
/**
	*
	* @param('plugin_kursbokning_id')
	*/
    public function setKursbokningKurs($plugin_kursbokning_id, $plugin_kursbokning_kurs_id) {
		try {
			$sql = "UPDATE plugin_kursbokning_kurs
			SET plugin_kursbokning_id = $plugin_kursbokning_id
			WHERE plugin_kursbokning_kurs_id = :plugin_kursbokning_kurs_id";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_kurs_id', $plugin_kursbokning_kurs_id, PDO::PARAM_INT);
			return $stmt->execute();
			
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
		
	/**
	*
	* getReservations
	* @return row
	*/
    public function getKursbokningKurser() {
		$sql = "SELECT * 
		FROM plugin_kursbokning_kurs";
		
		$stmt = $this->db->prepare($sql);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}	
		
	/**
	*
	* @return lastInsertId('plugin_reservation_id')
	*/
    public function getKursbokningKursId($plugin_kursbokning_kurs_id) {
		try {
			$sql = "SELECT * FROM plugin_kursbokning_kurs 
			WHERE
			plugin_kursbokning_kurs_id = :plugin_kursbokning_kurs_id;";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_kurs_id', $plugin_kursbokning_kurs_id, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	

	/**
	*
	* @param('plugin_kursbokning_id')
	*/
    public function setKursbokningKursUpdate($plugin_kursbokning_kurs_id, $title, $description, $type, $status, $web_reservation, $notify, $participants, $show_participants, $location, $cost, $utc_start, $utc_end, $utc_modified) {
		try {
			$sql = "UPDATE plugin_kursbokning_kurs
			SET title = :title,
			description = :description,
			type = :type,
			status = :status,
			web_reservation = :web_reservation,
			notify = :notify,
			participants = :participants,
			show_participants = :show_participants,
			location = :location,
			cost = :cost,
			utc_start = :utc_start,
			utc_end = :utc_end,
			utc_modified = :utc_modified
			WHERE plugin_kursbokning_kurs_id = :plugin_kursbokning_kurs_id";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_kurs_id', $plugin_kursbokning_kurs_id, PDO::PARAM_INT);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':description', $description, PDO::PARAM_STR);
			$stmt->bindParam(':type', $type, PDO::PARAM_INT);
			$stmt->bindParam(':status', $status, PDO::PARAM_INT);
			$stmt->bindParam(':web_reservation', $web_reservation, PDO::PARAM_INT);
			$stmt->bindParam(':notify', $notify, PDO::PARAM_STR);
			$stmt->bindParam(':participants', $participants, PDO::PARAM_INT);
			$stmt->bindParam(':show_participants', $show_participants, PDO::PARAM_INT);
			$stmt->bindParam(':location', $location, PDO::PARAM_STR);
			$stmt->bindParam(':cost', $cost, PDO::PARAM_STR);
			$stmt->bindParam(':utc_start', $utc_start, PDO::PARAM_STR);
			$stmt->bindParam(':utc_end', $utc_end, PDO::PARAM_STR);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	
	
	
	
	/**
	*
	* @return lastInsertId('plugin_kursbokning_kurs_anmalan_id')
	*/				
    public function setKursbokningKursAnmalanInsert($plugin_kursbokning_id, $plugin_kursbokning_kurser_id, $fnamn, $enamn, $personnummer, $personnummer_yyyy, $epost, $adress, $postnummer, $ort, $mobil, $telefon, $kommun, $lan, $country, $organisation, $fakturaadress, $kod, $ip, $agent, $token, $questions, $files, $utc_created, $log) {
		try {
			$sql = "INSERT INTO plugin_kursbokning_kurs_anmalan 
			(plugin_kursbokning_id, plugin_kursbokning_kurser_id, fnamn, enamn, personnummer, personnummer_yyyy, epost, adress, postnummer, ort, mobil, telefon, kommun, lan, country, organisation, fakturaadress, kod, log, ip, agent, token, questions, files, utc_created) VALUES
			(:plugin_kursbokning_id, :plugin_kursbokning_kurser_id, :fnamn, :enamn, :personnummer, :personnummer_yyyy, :epost, :adress, :postnummer, :ort, :mobil, :telefon, :kommun, :lan, :country, :organisation, :fakturaadress, :kod, :log, :ip, :agent, :token, :questions, :files, :utc_created)";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
			$stmt->bindParam(':plugin_kursbokning_kurser_id', $plugin_kursbokning_kurser_id, PDO::PARAM_STR);
			$stmt->bindParam(':fnamn', $fnamn, PDO::PARAM_STR);
			$stmt->bindParam(':enamn', $enamn, PDO::PARAM_STR);
			$stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
			$stmt->bindParam(':personnummer', $personnummer, PDO::PARAM_STR);			
			$stmt->bindParam(':personnummer_yyyy', $personnummer_yyyy, PDO::PARAM_STR);			
			$stmt->bindParam(':adress', $adress, PDO::PARAM_STR);
			$stmt->bindParam(':postnummer', $postnummer, PDO::PARAM_INT);
			$stmt->bindParam(':ort', $ort, PDO::PARAM_STR);
			$stmt->bindParam(':mobil', $mobil, PDO::PARAM_STR);
			$stmt->bindParam(':telefon', $telefon, PDO::PARAM_STR);
			$stmt->bindParam(':kommun', $kommun, PDO::PARAM_STR);
			$stmt->bindParam(':lan', $lan, PDO::PARAM_STR);
			$stmt->bindParam(':country', $country, PDO::PARAM_STR);
			$stmt->bindParam(':organisation', $organisation, PDO::PARAM_STR);
			$stmt->bindParam(':fakturaadress', $fakturaadress, PDO::PARAM_STR);
			$stmt->bindParam(':kod', $kod, PDO::PARAM_STR);
			$stmt->bindParam(':log', $log, PDO::PARAM_STR);
			$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
			$stmt->bindParam(':agent', $agent, PDO::PARAM_STR);
			$stmt->bindParam(':token', $token, PDO::PARAM_STR);
			$stmt->bindParam(':questions', $questions, PDO::PARAM_STR);
			$stmt->bindParam(':files', $files, PDO::PARAM_STR);
			$stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
			$stmt->execute();
			return $this->db->lastInsertId('plugin_kursbokning_kurs_anmalan_id');
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	

	/**
	*
	* @params
	*/				
    public function setKursbokningKursAnmalanUpdate($plugin_kursbokning_kurs_anmalan_id, $fnamn, $enamn, $personnummer, $epost, $adress, $postnummer, $ort, $mobil, $telefon, $kommun, $lan, $country, $organisation, $fakturaadress, $kod, $ip, $agent, $token, $utc_modified, $utc_admitted, $utc_confirmed, $utc_canceled, $notes, $log) {
		try {
			$sql = "UPDATE plugin_kursbokning_kurs_anmalan 
			SET fnamn = :fnamn,
			enamn = :enamn,
			personnummer = :personnummer,
			epost = :epost,
			adress = :adress,
			postnummer = :postnummer,
			ort = :ort,
			mobil = :mobil,
			telefon = :telefon,
			kommun = :kommun,
			lan = :lan,
			country = :country,
			organisation = :organisation,
			fakturaadress = :fakturaadress,
			kod = :kod,
			ip = :ip,
			agent = :agent,
			token = :token,
			utc_modified = :utc_modified,
			utc_admitted = :utc_admitted,
			utc_confirmed = :utc_confirmed,
			utc_canceled = :utc_canceled,
			notes = :notes,
			log = :log
			WHERE plugin_kursbokning_kurs_anmalan_id = :plugin_kursbokning_kurs_anmalan_id";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':fnamn', $fnamn, PDO::PARAM_STR);
			$stmt->bindParam(':enamn', $enamn, PDO::PARAM_STR);
			$stmt->bindParam(':epost', $epost, PDO::PARAM_STR);
			$stmt->bindParam(':personnummer', $personnummer, PDO::PARAM_STR);			
			$stmt->bindParam(':adress', $adress, PDO::PARAM_STR);
			$stmt->bindParam(':postnummer', $postnummer, PDO::PARAM_INT);
			$stmt->bindParam(':ort', $ort, PDO::PARAM_STR);
			$stmt->bindParam(':mobil', $mobil, PDO::PARAM_STR);
			$stmt->bindParam(':telefon', $telefon, PDO::PARAM_STR);
			$stmt->bindParam(':kommun', $kommun, PDO::PARAM_STR);
			$stmt->bindParam(':lan', $lan, PDO::PARAM_STR);
			$stmt->bindParam(':country', $country, PDO::PARAM_STR);
			$stmt->bindParam(':organisation', $organisation, PDO::PARAM_STR);
			$stmt->bindParam(':fakturaadress', $fakturaadress, PDO::PARAM_STR);			
			$stmt->bindParam(':kod', $kod, PDO::PARAM_STR);
			$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
			$stmt->bindParam(':agent', $agent, PDO::PARAM_STR);
			$stmt->bindParam(':token', $token, PDO::PARAM_STR);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->bindParam(':utc_admitted', $utc_admitted, PDO::PARAM_STR);
			$stmt->bindParam(':utc_confirmed', $utc_confirmed, PDO::PARAM_STR);
			$stmt->bindParam(':utc_canceled', $utc_canceled, PDO::PARAM_STR);
			$stmt->bindParam(':notes', $notes, PDO::PARAM_STR);
			$stmt->bindParam(':log', $log, PDO::PARAM_STR);
			$stmt->bindParam(':plugin_kursbokning_kurs_anmalan_id', $plugin_kursbokning_kurs_anmalan_id, PDO::PARAM_INT);
			return $stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	

	
	/**
	*
	* @params
	*/				
    public function setKursbokningKursAnmalanKursbyteUpdate($plugin_kursbokning_kurs_anmalan_id, $plugin_kursbokning_kurs_id, $utc_modified, $log)
	 {
		try {
			$sql = "UPDATE plugin_kursbokning_kurs_anmalan 
			SET plugin_kursbokning_kurser_id = :plugin_kursbokning_kurs_id,
			utc_modified = :utc_modified,
			log = :log
			WHERE plugin_kursbokning_kurs_anmalan_id = :plugin_kursbokning_kurs_anmalan_id";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
			$stmt->bindParam(':log', $log, PDO::PARAM_STR);
			$stmt->bindParam(':plugin_kursbokning_kurs_anmalan_id', $plugin_kursbokning_kurs_anmalan_id, PDO::PARAM_INT);
			$stmt->bindParam(':plugin_kursbokning_kurs_id', $plugin_kursbokning_kurs_id, PDO::PARAM_INT);
			return $stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	


	/**
	*
	* $param $plugin_kursbokning_kurs_anmalan_id
	*/
	
    public function setKursbokningKursAnmalanDelete($plugin_kursbokning_kurs_anmalan_id) {
		try {
			$stmt = $this->db->prepare("DELETE FROM plugin_kursbokning_kurs_anmalan WHERE plugin_kursbokning_kurs_anmalan_id = :plugin_kursbokning_kurs_anmalan_id");
			$stmt->bindParam(":plugin_kursbokning_kurs_anmalan_id", $plugin_kursbokning_kurs_anmalan_id, PDO::PARAM_INT);
			return $stmt->execute();
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
	}	
	



	
	/**
	*
	* getReservations
	* @return row
	*/
    public function getKursbokningKurserAnmalan($plugin_kursbokning_kurs_id, $utc_date_1, $utc_date_2, $choosen, $canceled) {
		
		$sql = "SELECT * 
		FROM plugin_kursbokning_kurs_anmalan
		WHERE utc_created BETWEEN '".$utc_date_1."' AND '".$utc_date_2."'";
		
		$sql .= $plugin_kursbokning_kurs_id ? " AND FIND_IN_SET(:plugin_kursbokning_kurs_id, plugin_kursbokning_kurser_id) " : "";
		
		
		switch($choosen) {
			case 'ad':
				$sql .= " AND utc_admitted > '2000-01-01'";
			break;
			case 'co':
				$sql .= " AND utc_confirmed > '2000-01-01'";
			break;
		}
		
		$sql .= $canceled ?  "" : " AND NOT utc_canceled > '2000-01-01'";
		$sql .= " ORDER BY plugin_kursbokning_kurs_anmalan_id DESC";
		
		// apply percentages to search string
		//$plugin_kursbokning_kurs_id = "%".$plugin_kursbokning_kurs_id."%";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_kursbokning_kurs_id', $plugin_kursbokning_kurs_id, PDO::PARAM_INT);
		
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}	


	
	/**
	*
	* getReservations
	* @return count
	*/
    public function getKursbokningKurserAnmalanCount($plugin_kursbokning_kurs_id) {
			
		$sql = "SELECT COUNT(*) as count
		FROM plugin_kursbokning_kurs_anmalan
		WHERE
		FIND_IN_SET(:plugin_kursbokning_kurs_id, plugin_kursbokning_kurser_id) 
		AND NOT utc_canceled > '2000-01-01'";
		
		// apply percentages to search string
		//$plugin_kursbokning_kurs_id = "%".$plugin_kursbokning_kurs_id."%";
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_kursbokning_kurs_id', $plugin_kursbokning_kurs_id, PDO::PARAM_INT);
		
		$stmt->execute();		
		return $stmt->fetch(PDO::FETCH_ASSOC);		
	}	

	

	/**
	*
	* get kurser
	* @return count
	*/
    public function getKursbokningCount($plugin_kursbokning_id) {
			
		$sql = "SELECT COUNT(*) as count
		FROM plugin_kursbokning_kurs
		WHERE
		plugin_kursbokning_id = :plugin_kursbokning_id";
				
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
		
		$stmt->execute();		
		return $stmt->fetch(PDO::FETCH_ASSOC);		
	}	


	
	/**
	*
	* getReservations
	* @return row
	*/
    public function getKursbokningKurserAnmalanId($plugin_kursbokning_kurs_anmalan_id) {
		$sql = "SELECT * 
		FROM plugin_kursbokning_kurs_anmalan
		WHERE
		plugin_kursbokning_kurs_anmalan_id = :plugin_kursbokning_kurs_anmalan_id";
		
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_kursbokning_kurs_anmalan_id', $plugin_kursbokning_kurs_anmalan_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetch(PDO::FETCH_ASSOC);		
	}	
	
	/**
	*
	* getReservations
	* @return row
	*/
    public function getKursbokningKurserAnmalanIdAll($plugin_kursbokning_kurs_anmalan_id) {
		$sql = "SELECT * 
		FROM plugin_kursbokning_kurs_anmalan
		WHERE
		plugin_kursbokning_kurs_anmalan_id = :plugin_kursbokning_kurs_anmalan_id";
		
		
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':plugin_kursbokning_kurs_anmalan_id', $plugin_kursbokning_kurs_anmalan_id, PDO::PARAM_INT);
		$stmt->execute();		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);		
	}	

	
	public function setKursbokningKursAnmalanAdminInsert($plugin_kursbokning_id, $plugin_kursbokning_kurser_id, $fnamn, $enamn, $kod, $ip, $agent, $token, $utc_created, $log) {
	
		try {
			$sql = "INSERT INTO plugin_kursbokning_kurs_anmalan 
			(plugin_kursbokning_id, plugin_kursbokning_kurser_id, fnamn, enamn, kod, log, ip, agent, token, utc_created) VALUES
			(:plugin_kursbokning_id, :plugin_kursbokning_kurser_id, :fnamn, :enamn, :kod, :log, :ip, :agent, :token, :utc_created)";
			
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':plugin_kursbokning_id', $plugin_kursbokning_id, PDO::PARAM_INT);
			$stmt->bindParam(':plugin_kursbokning_kurser_id', $plugin_kursbokning_kurser_id, PDO::PARAM_STR);
			$stmt->bindParam(':fnamn', $fnamn, PDO::PARAM_STR);
			$stmt->bindParam(':enamn', $enamn, PDO::PARAM_STR);
			$stmt->bindParam(':kod', $kod, PDO::PARAM_STR);
			$stmt->bindParam(':log', $log, PDO::PARAM_STR);
			$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
			$stmt->bindParam(':agent', $agent, PDO::PARAM_STR);
			$stmt->bindParam(':token', $token, PDO::PARAM_STR);
			$stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
			$stmt->bindParam(':log', $log, PDO::PARAM_STR);
			$stmt->execute();
			return $this->db->lastInsertId('plugin_kursbokning_kurs_anmalan_id');
		
		} catch(PDOException $e) {		
			handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
		}		
		
	}
	
	
	

	public function transl($text) {
		$a = array(
			"english" => array("Mon" => "Mon", "Tue" => "Tue", "Wed" => "Wed", "Thu" => "Thu", "Fri" => "Fri", "Sat" => "Sat", "Sun" => "Sun", 
								"Monday" => "Monday", "Tuesday" => "Tuesday", "Wednesday" => "Wednesday", "Thursday" => "Thursday", "Friday" => "Friday", "Saturday" => "Saturday", "Sunday" => "Sunday", 
								"one day" => "one day", "four days" => "four days", "one week" => "one week", "two weeks" => "two weeks", "one month" => "one month",
								"January" => "January", "February" => "February", "March" => "March", "April" => "April", "May" => "May", "June" => "June", "July" => "July", "August" => "August", "September" => "September", "October" => "October", "November" => "November", "December" => "December",
								"Jan" => "Jan", "Feb" => "Feb", "Mar" => "Mar", "Apr" => "Apr", "May" => "May", "Jun" => "Jun", "Jul" => "Jul", "Aug" => "Aug", "Sep" => "Sep", "Oct" => "Oct", "Nov" => "Nov", "Dec" => "Dec",
								"w" => "w"), 
			"swedish" => array("Mon" => "må", "Tue" => "ti", "Wed" => "on", "Thu" => "to", "Fri" => "fr", "Sat" => "lö", "Sun" => "sö",
								"Monday" => "Måndag", "Tuesday" => "Tisdag", "Wednesday" => "Onsdag", "Thursday" => "Torsdag", "Friday" => "Fredag", "Saturday" => "Lördag", "Sunday" => "Söndag", 
								"one day" => "en dag", "four days" => "fyra dagar", "one week" => "en vecka", "two weeks" => "två veckor", "one month" => "en månad",
								"January" => "Januari", "February" => "Februari", "March" => "Mars", "April" => "April", "May" => "Maj", "June" => "Juni", "July" => "Juli", "August" => "Augusti", "September" => "September", "October" => "Oktober", "November" => "November", "December" => "December",
								"Jan" => "Jan", "Feb" => "Feb", "Mar" => "Mar", "Apr" => "Apr", "May" => "Maj", "Jun" => "Jun", "Jul" => "Jul", "Aug" => "Aug", "Sep" => "Sep", "Oct" => "Okt", "Nov" => "Nov", "Dec" => "Dec",
								"w" => "v"));

		$l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
		if(!$l) {
			$l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
		} 
		$s = $l ? $a[$l][$text] : $text;
		return $s;
	}


	
}
?>