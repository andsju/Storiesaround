<?php
// include core 
//--------------------------------------------------
require_once '../../../cms/includes/inc.core.php';
include_once '../../../cms/includes/inc.functions_pages.php';
include_once 'kursbokning_functions.php';
if(!get_role_CMS('user') == 1) {die;}

// overall
// --------------------------------------------------
if (isset($_POST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_POST['token'] == $_SESSION['token']) {
		
		
		// check client user-agent, prevent session been hijacked
		if($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
			die('User agent fail. Please logout and login again.');
		}
		
		$kursbokning = new Kursbokning();
	
		// $action
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_STRING);
		
		switch($action) {		
				

			case 'action_form_add';
		
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
				$type = filter_var(trim($_POST['type']), FILTER_SANITIZE_STRING);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $kursbokning->setKursbokningFormInsert($title, $type, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;


			case 'reservation_form_edit';
		
				$plugin_reservation_id = filter_input(INPUT_POST, 'plugin_reservation_id', FILTER_VALIDATE_INT);
				
				$result = $kursbokning->getKursbokningForm($plugin_reservation_id);
				if($result) {
					echo $result;
				}
				
			break;
			


			case 'action_form_field_add';
		
				$plugin_kursbokning_id = filter_input(INPUT_POST, 'plugin_kursbokning_id', FILTER_VALIDATE_INT);
				$label = '';
				$field = filter_var(trim($_POST['field']), FILTER_SANITIZE_STRING);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $kursbokning->setFormFieldInsert($plugin_kursbokning_id, $label, $field, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;

			case 'action_form_field_custom_save';
		
				$plugin_kursbokning_fields_id = filter_input(INPUT_POST, 'plugin_kursbokning_fields_id', FILTER_VALIDATE_INT);
				$label = filter_var(trim($_POST['field_label']), FILTER_SANITIZE_STRING);
				$required = filter_input(INPUT_POST, 'required', FILTER_VALIDATE_INT);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$field_values = isset($_POST['field_values']) ? json_encode(array_map("strip_tags", $_POST['field_values'])) : '';
				
				$result = $kursbokning->setFormFieldUpdate($plugin_kursbokning_fields_id, $label, $field_values, $required, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;


			case 'action_form_field_custom_delete';
		
				$plugin_kursbokning_fields_id = filter_input(INPUT_POST, 'plugin_kursbokning_fields_id', FILTER_VALIDATE_INT);
				
				$result = $kursbokning->setFormFieldDelete($plugin_kursbokning_fields_id);
				if($result) {
					echo $result;
				}
				
			break;
			
			case 'action_form_save';
		
				$plugin_kursbokning_id = filter_input(INPUT_POST, 'plugin_kursbokning_id', FILTER_VALIDATE_INT);
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
				$terms = filter_var(trim($_POST['terms']), FILTER_SANITIZE_STRING);
				$status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
				$web_reservation = filter_input(INPUT_POST, 'web_reservation', FILTER_VALIDATE_INT);
				$email_notify = filter_input(INPUT_POST, 'email_notify', FILTER_VALIDATE_EMAIL);
				$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
				$utc_start_publish = filter_var(trim($_POST['datetime_start']), FILTER_SANITIZE_STRING);
				$utc_end_publish = filter_var(trim($_POST['datetime_end']), FILTER_SANITIZE_STRING);

				$file_upload = filter_input(INPUT_POST, 'file_upload', FILTER_VALIDATE_INT);
				$file_instruction = filter_var(trim($_POST['file_instruction']), FILTER_SANITIZE_STRING);
				$file_extensions = filter_var(trim($_POST['file_extensions']), FILTER_SANITIZE_STRING);
				$file_path = filter_var(trim($_POST['file_path']), FILTER_SANITIZE_STRING);
				$file_attach = filter_input(INPUT_POST, 'file_attach', FILTER_VALIDATE_INT);
				$file_action = filter_input(INPUT_POST, 'file_action', FILTER_VALIDATE_INT);
				$file_move_path = filter_var(trim($_POST['file_move_path']), FILTER_SANITIZE_STRING);
				
				$fields = filter_var(trim($_POST['fields']), FILTER_SANITIZE_STRING);
				$fields = explode(",",$fields);
				$fields = json_encode($fields);
				
				//echo $fields;
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $kursbokning->setFormUpdate($plugin_kursbokning_id, $title, $terms, $status, $web_reservation, $email_notify, $type, $utc_start_publish, $utc_end_publish, $file_upload, $file_instruction, $file_extensions, $file_path, $file_attach, $file_action, $file_move_path, $fields, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;

			case 'action_form_delete';
		
				$plugin_kursbokning_id = filter_input(INPUT_POST, 'plugin_kursbokning_id', FILTER_VALIDATE_INT);
				
				$result = $kursbokning->setKursbokningFormDelete($plugin_kursbokning_id);
				if($result) {
					echo $result;
				}
				
			break;

			
			case 'action_field_position_update';
		
				$plugin_kursbokning_fields_id_array = explode(",",$_POST['positions']);
				
				$result = $kursbokning->setFieldPositionUpdate($plugin_kursbokning_fields_id_array);

				if($result) {
					echo reply($result);
				}
				
			break;
			
			
			
			case 'action_course_add';
		
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
				$type = filter_var(trim($_POST['type']), FILTER_SANITIZE_STRING);
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $kursbokning->setKursbokningKursInsert($title, $type, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;
			

			case 'action_form_course_save';
		
				$plugin_kursbokning_id = filter_input(INPUT_POST, 'plugin_kursbokning_id', FILTER_VALIDATE_INT);
				$plugin_kursbokning_kurs_id = filter_input(INPUT_POST, 'plugin_kursbokning_kurs_id', FILTER_VALIDATE_INT);
				
				$result = $kursbokning->setKursbokningKurs($plugin_kursbokning_id, $plugin_kursbokning_kurs_id);
				if($result) {
					echo $result;
				}	
				
			break;
			
			
			case 'action_course_save';
		
				$plugin_kursbokning_kurs_id = filter_input(INPUT_POST, 'plugin_kursbokning_kurs_id', FILTER_VALIDATE_INT);
				$title = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
				$description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
				$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
				$status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
				$web_reservation = filter_input(INPUT_POST, 'web_reservation', FILTER_VALIDATE_INT);				
				$participants = filter_input(INPUT_POST, 'participants', FILTER_VALIDATE_INT);
				$show_participants = filter_input(INPUT_POST, 'show_participants', FILTER_VALIDATE_INT);
				$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
				$cost = filter_input(INPUT_POST, 'cost', FILTER_SANITIZE_STRING);
				$notify = filter_input(INPUT_POST, 'notify', FILTER_SANITIZE_STRING);
				$utc_start = filter_var(trim($_POST['datetime_start']), FILTER_SANITIZE_STRING);
				$utc_end = filter_var(trim($_POST['datetime_end']), FILTER_SANITIZE_STRING);				
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $kursbokning->setKursbokningKursUpdate($plugin_kursbokning_kurs_id, $title, $description, $type, $status, $web_reservation, $notify, $participants, $show_participants, $location, $cost, $utc_start, $utc_end, $utc_modified);
				if($result) {
					echo $result;
				}
				
			break;

			case 'action_course_delete';
		
				$plugin_kursbokning_kurs_id = filter_input(INPUT_POST, 'plugin_kursbokning_kurs_id', FILTER_VALIDATE_INT);
				
				$result = $kursbokning->setKursbokningCourseDelete($plugin_kursbokning_kurs_id);
				if($result) {
					echo $result;
				}
				
			break;

			
		
			case 'action_course_reservation_add';		
			
				$plugin_kursbokning_id = 0;
				
				$plugin_kursbokning_kurser_id = filter_input(INPUT_POST, 'plugin_kursbokning_kurser_id', FILTER_VALIDATE_INT);
				
				$fnamn = filter_var(trim($_POST['fnamn']), FILTER_SANITIZE_STRING);
				$enamn = filter_var(trim($_POST['enamn']), FILTER_SANITIZE_STRING);
				
				$kod = $kod = $kursbokning->create_random_string(6);
				
				// some variables recorded when form used
				$ip = $_SERVER['REMOTE_ADDR'];
				$agent = $_SESSION['HTTP_USER_AGENT'];
				$token = $_SESSION['token'];
				
				$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$log = $utc_created .' | admin bokning';
				
				$result = $kursbokning->setKursbokningKursAnmalanAdminInsert($plugin_kursbokning_id, $plugin_kursbokning_kurser_id, $fnamn, $enamn, $kod, $ip, $agent, $token, $utc_created, $log);
				if($result) {
					echo $result;
				}
		
			break;
		
		
		
		
			case 'action_course_reservation_save';
				
				$plugin_kursbokning_kurs_anmalan_id = filter_input(INPUT_POST, 'plugin_kursbokning_kurs_anmalan_id', FILTER_VALIDATE_INT);
				
				$fnamn = filter_var(trim($_POST['fnamn']), FILTER_SANITIZE_STRING);
				$enamn = filter_var(trim($_POST['enamn']), FILTER_SANITIZE_STRING);
				$epost = filter_var(trim($_POST['epost']), FILTER_SANITIZE_STRING);
				$kod = $kod = $kursbokning->create_random_string(6);

				// optional
				$adress = filter_var(trim($_POST['adress']), FILTER_SANITIZE_STRING);
				$ort = filter_var(trim($_POST['ort']), FILTER_SANITIZE_STRING);
				$postnummer = filter_input(INPUT_POST, 'postnummer', FILTER_VALIDATE_INT);
				$mobil = isset($_POST['mobil']) ? filter_var(trim($_POST['mobil']), FILTER_SANITIZE_STRING) : '';
				$telefon = isset($_POST['telefon']) ? filter_var(trim($_POST['telefon']), FILTER_SANITIZE_STRING) : '';
				$kommun = isset($_POST['kommun']) ? filter_var(trim($_POST['kommun']), FILTER_SANITIZE_STRING) : '';
				$lan = isset($_POST['lan']) ? filter_var(trim($_POST['lan']), FILTER_SANITIZE_STRING) : '';
				// get real name of swedish län instead of numeric representation
				foreach($swelan as $key => $value) {
					if ($lan == $key) {
						$lan = $value;
						break;
					}
				}
				$country = isset($_POST['country']) ? filter_var(trim($_POST['country']), FILTER_SANITIZE_STRING) : '';
				$organisation = isset($_POST['organisation']) ? filter_var(trim($_POST['organisation']), FILTER_SANITIZE_STRING) : '';
				$fakturaadress = isset($_POST['fakturaadress']) ? filter_var(trim($_POST['fakturaadress']), FILTER_SANITIZE_STRING) : '';
				$personnummer = isset($_POST['personnummer']) ? filter_var(trim($_POST['personnummer']), FILTER_SANITIZE_STRING) : '';
				
				$notes = isset($_POST['notes']) ? filter_var(trim($_POST['notes']), FILTER_SANITIZE_STRING) : '';
				// some variables recorded when form used
				$ip = isset($_POST['ip']) ? filter_var(trim($_POST['ip']), FILTER_SANITIZE_STRING) : '';
				$agent = isset($_POST['agent']) ? filter_var(trim($_POST['agent']), FILTER_SANITIZE_STRING) : '';
				$token = isset($_POST['token']) ? filter_var(trim($_POST['token']), FILTER_SANITIZE_STRING) : '';
				
				$utc_admitted = isset($_POST['datetime_utc_admitted']) ? filter_var(trim($_POST['datetime_utc_admitted']), FILTER_SANITIZE_STRING) : '';
				$utc_confirmed = isset($_POST['datetime_utc_confirmed']) ? filter_var(trim($_POST['datetime_utc_confirmed']), FILTER_SANITIZE_STRING) : '';
				$utc_canceled = isset($_POST['datetime_utc_canceled']) ? filter_var(trim($_POST['datetime_utc_canceled']), FILTER_SANITIZE_STRING) : '';
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$notes = isset($_POST['notes']) ? filter_var(trim($_POST['notes']), FILTER_SANITIZE_STRING) : '';
				$oldlog = isset($_POST['log']) ? filter_var(trim($_POST['log']), FILTER_SANITIZE_STRING) : '';
				//$log = "\n".$utc_modified." | ";
				$log = "\n<p>".$utc_modified." | ";
				$log .= $_SESSION['first_name'] .' '.$_SESSION['last_name']." (redigering)\n</p>";
				$log .= $oldlog;
				
				$result = $kursbokning->setKursbokningKursAnmalanUpdate($plugin_kursbokning_kurs_anmalan_id, $fnamn, $enamn, $personnummer, $epost, $adress, $postnummer, $ort, $mobil, $telefon, $kommun, $lan, $country, $organisation, $fakturaadress, $kod, $ip, $agent, $token, $utc_modified, $utc_admitted, $utc_confirmed, $utc_canceled, $notes, $log);
				if($result) {
					//echo $result;
				}
				
			break;
		
		

			case 'action_course_reservation_change_course_save';
				
				$plugin_kursbokning_kurs_anmalan_id = filter_input(INPUT_POST, 'plugin_kursbokning_kurs_anmalan_id', FILTER_VALIDATE_INT);
				$plugin_kursbokning_kurs_id = filter_input(INPUT_POST, 'plugin_kursbokning_kurs_id', FILTER_VALIDATE_INT);
				
				$utc_modified = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$oldlog = isset($_POST['log']) ? filter_var(trim($_POST['log']), FILTER_SANITIZE_STRING) : '';
				$log = "\n".$utc_modified." | ";
				$log .= $_SESSION['first_name'] .' '.$_SESSION['last_name']." (kursbyte)\n";
				$log .= $oldlog;
				
				$result = $kursbokning->setKursbokningKursAnmalanKursbyteUpdate($plugin_kursbokning_kurs_anmalan_id, $plugin_kursbokning_kurs_id, $utc_modified, $log);
				if($result) {
					//echo $result;
				}
				
			break;
		
		
			case 'action_course_reservation_delete';
				
				$plugin_kursbokning_kurs_anmalan_id = filter_input(INPUT_POST, 'plugin_kursbokning_kurs_anmalan_id', FILTER_VALIDATE_INT);
								
				$result = $kursbokning->setKursbokningKursAnmalanDelete($plugin_kursbokning_kurs_anmalan_id);
				if($result) {
					//echo $result;
				}
				
			break;
		

			case 'action_rapport_delete_tmp_folder';
			
				//delete tmp folder
				$dir = CMS_ABSPATH . DIRECTORY_SEPARATOR . 'content'  . DIRECTORY_SEPARATOR . 'plugins'  . DIRECTORY_SEPARATOR . 'kursbokning'  . DIRECTORY_SEPARATOR . 'rapport'  . DIRECTORY_SEPARATOR . 'tmp'  . DIRECTORY_SEPARATOR ;
				echo $dir;
				$iterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
				$files = new RecursiveIteratorIterator($iterator,
							 RecursiveIteratorIterator::CHILD_FIRST);
				foreach($files as $file) {
					echo $file;
					if ($file->getFilename() === '.' || $file->getFilename() === '..') {
						continue;
					}
					if ($file->isDir()){
						rmdir($file->getRealPath());
					} else {
						unlink($file->getRealPath());
					}
				}
				
			break;
		
			case 'action_rapport_delete_tmp_folder_delayed';
			
				sleep(20);
				//delete tmp folder
				$dir = CMS_ABSPATH . DIRECTORY_SEPARATOR . 'content'  . DIRECTORY_SEPARATOR . 'plugins'  . DIRECTORY_SEPARATOR . 'kursbokning'  . DIRECTORY_SEPARATOR . 'rapport'  . DIRECTORY_SEPARATOR . 'tmp'  . DIRECTORY_SEPARATOR ;
				echo $dir;
				$iterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
				$files = new RecursiveIteratorIterator($iterator,
							 RecursiveIteratorIterator::CHILD_FIRST);
				foreach($files as $file) {
					echo $file;
					if ($file->getFilename() === '.' || $file->getFilename() === '..') {
						continue;
					}
					if ($file->isDir()){
						rmdir($file->getRealPath());
					} else {
						unlink($file->getRealPath());
					}
				}
				
			break;


				




		
			
			case '';
			break;
		}		
		
	}
}

?>