<?php
// include core 
//--------------------------------------------------
require_once '../../../cms/includes/inc.core.php';
include_once CMS_ABSPATH .'/cms/includes/inc.functions_pages.php';
include_once 'kursbokning_functions.php';

//if(!get_role_CMS('user') == 1) {die;}

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
		

			case 'code_request';
		
				$epost = filter_var(trim($_POST['email_code_request']), FILTER_VALIDATE_EMAIL);
				$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				$kod = $kursbokning->create_random_string(3);
				$type = strtolower($_POST['code_type']);

				$result = $kursbokning->setKursbokningBokningskodInsert($epost, $kod, $utc_created);

				if($result) {
				
					$code = $talkcode = '';
					switch ($type) {
						case 'bokningskod':
							$code = 'bokningskod';
							$talkcode = 'boka en kurs';
						break;
						case 'ansökningskod':
							$code = 'ansökningskod';
							$talkcode = 'ansöka till en kurs';
						break;
						default:
							$code = 'kod';
							$talkcode = 'boka via webben';
						break;
					}
				
					// send confirmation email
					$m_body = 'Hej!';
					$m_body .= "<br />";
					$m_body .= 'Vi skickar här den '.$code.' som du kan använda för att '.$talkcode.'. Koden är giltig tillsammans med din e-postadress.';
					$m_body .= "<br />";
					$m_body .= "<br />";
					$m_body .= 'Din '.$code.' är: ';
					$m_body .= "\n".$kod."\n";
					$m_body .= "<br /><br />";
					$m_body .= $_SESSION['site_name'];
					$m_body .= " | ";
					$m_body .= $_SESSION['site_email'];
					$m_body .= " | ";
					$m_body .= CMS_URL ;
					
					$to = $epost;
					$subject = 'Efterfrågad kod';
					$headers = $_SESSION['site_email'];
					
					// include file
					include_once CMS_ABSPATH .'/cms/includes/inc.send_a_mail.php';
					// send mail
					if(send_a_mail($_SESSION['token'], $to, $to, $_SESSION['site_email'], $_SESSION['site_copyright'], $subject, $m_body,'')) {
						$reply = 'Nu skickas '.$code.' till e-postadress: <i>'.$to. '</i> ';
						$reply .= 'Koden använder du i fältet nedan. <i>Har inte koden kommit inom några minuter vänligen kontrollera din skräppost.</i> Om du av ngn anledning inte får koden så vänligen kontakta oss så hjälper vi dig!';
						echo $reply;
						
						$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history_email = new HistoryEmail();
						$history_email->setHistoryEmail($to, $_SESSION['site_email'], $subject, $m_body, $utc_datetime);
						
					}
					
				}
				
			break;



			case 'code_use';
				$epost = filter_var(trim($_POST['email_code_use']), FILTER_VALIDATE_EMAIL);
				$kod = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);								
				$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
				
				$result = $kursbokning->getKursbokningBokningskod($epost, $kod);

				if($result) {
					// check if code is used
					$row = $kursbokning->getKursbokningKursAnmalan($epost, $kod);
					
					//print_r($row);
					// set new session if match in order to handle functions in cms/plugins
					if($row == 1) {
						//echo 'oki';
						//$_SESSION['email_confirmed'] = $epost;
					}
					$str = $row ? 'match_used' : 'match';
					
					//return array('match' => 'true', 'email_confirmed' => $epost);					
					$return = array('match' => $str, 'email_confirmed' => $epost);
					
					echo json_encode($return);
					//echo $str;
				} else {
					$return = array('match' => 'false', 'email_confirmed' => '');
					echo json_encode($return);
				}
				
			break;
			
				
			case 'form_post';

				// default use $_POST['kurs_id'] - used in all types of courses
				$plugin_kursbokning_kurser_id = isset($_POST['kurs_vald_id']) ? explode(",",$_POST['kurs_vald_id']) : array();
				// if $_POST['kurs_ids'] - use this (priority check), fall back to $_POST['kurs_vald_id']
				$plugin_kursbokning_kurser_id = isset($_POST['kurs_ids']) && strlen($_POST['kurs_ids']) > 0 ? explode(",",$_POST['kurs_ids']) : $plugin_kursbokning_kurser_id;
				
				//print_r($plugin_kursbokning_kurser_id);
				
				$s_kurser = "\n".'<ul>';
				// notify addresses
				$notifies = array();
				// get name of cources
				// priority
				
				$ii = 1;
				foreach($plugin_kursbokning_kurser_id as $plugin_kursbokning_kurs_id) {
					$kurs_meta = $kursbokning->getKursbokningKursId($plugin_kursbokning_kurs_id);
					$s_kurser .= "\n".'<li>'.$ii.'  '.$kurs_meta['title'] .'</li>';
					
					// new notify addresses
					$results = explode(',',$kurs_meta['notify']);
					foreach($results as $result) {
						$notifies[] = trim($result);
					}
					$ii++;
				}
				$s_kurser .= "\n".'</ul>';
				
				// keep unique address
				$notifies = array_unique($notifies);
				
				$plugin_kursbokning_kurser_id = implode(',',$plugin_kursbokning_kurser_id);
				$plugin_kursbokning_id = filter_input(INPUT_POST, 'plugin_kursbokning_id', FILTER_VALIDATE_INT);
				$fnamn = filter_var(trim($_POST['fnamn']), FILTER_SANITIZE_STRING);
				$enamn = filter_var(trim($_POST['enamn']), FILTER_SANITIZE_STRING);
				$adress = isset($_POST['adress']) ? filter_var(trim($_POST['adress']), FILTER_SANITIZE_STRING) : '';
				$ort = isset($_POST['ort']) ? filter_var(trim($_POST['ort']), FILTER_SANITIZE_STRING) : '';
				$postnummer = filter_input(INPUT_POST, 'postnummer', FILTER_VALIDATE_INT);
				$epost = filter_var(trim($_POST['epost']), FILTER_SANITIZE_STRING);
				$kod = filter_var(trim($_POST['kod']), FILTER_SANITIZE_STRING);

				// optional
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
							
				// assume $personnummer not given
				$personnummer = null;
				$personnummer_yyyy = isset($_POST['personnummer']) ? filter_var(trim($_POST['personnummer']),  FILTER_SANITIZE_STRING) : null;
				
				// if $personnummer_yyyy seems to be ok
				if ($personnummer_yyyy ) {
					// clear all but numbers, remove '-' and empty space 
					$personnummer_yyyy = preg_replace('/\s+/', '', $personnummer_yyyy);
					$personnummer_yyyy = preg_replace('/\-/', '', $personnummer_yyyy);
					// add '-' sign
					$personnummer_yyyy = isset($personnummer_yyyy) ? substr_replace($personnummer_yyyy,'-',-4,0) : null;
					// leave century...
					$personnummer = substr($personnummer_yyyy, 2);
				}
				
				
				// some variables recorded when form used
				$ip = isset($_POST['ip']) ? filter_var(trim($_POST['ip']), FILTER_SANITIZE_STRING) : '';
				$agent = isset($_POST['agent']) ? filter_var(trim($_POST['agent']), FILTER_SANITIZE_STRING) : '';
				$token = isset($_POST['token']) ? filter_var(trim($_POST['token']), FILTER_SANITIZE_STRING) : '';
				
				// custom fields - as expected list in following loop
				$questions = isset($_POST['questions']) ? $_POST['questions'] : array();
				$questions_remains = $questions;
				
				// attachments
				$attachments = isset($_POST['attachments']) ? $_POST['attachments'] : array();
				// save to database
				// convert array $attachments to comma separated string
				$files = implode(',',$attachments);

				// confirm uploaded files - simplified filenames last 15 characters
				$attachments_files_uploaded = '';
				
				foreach($attachments as $attachment) {
					// show last 15 characters
					$filename = '...'. substr($attachment, -15); 
					$attachments_files_uploaded .= "\n".$filename .'<br />';
				}
				
				// uploaded files to attach email
				$attachments_files_and_path = array();
				
				foreach($attachments as $attachment) { 	
					$attachments_files_and_path[] = CMS_ABSPATH .'/content/uploads/plugins/kursbokning/'. $attachment;
				}
				
				//print_r($attachments_files_and_path);
				
				// check custom fields, loop $_POST
				$s = '';
				foreach ($_POST as $varname => $values) {
					
					// variable starts width q and following integer
					if (preg_match("/^[q](\d{1})/", $varname)) {
						
						// id
						$plugin_kursbokning_fields_id = substr($varname, 1);
						
						// get label from database
						$field_meta = $kursbokning->getKursbokningFieldId($plugin_kursbokning_fields_id);
						
						// check for match
						if($field_meta) {
							
							// output label
							$s .= "\n".$field_meta['label'] ." (#id: ".$plugin_kursbokning_fields_id.")\n";
							// check $_POST in $questions
							if (in_array($plugin_kursbokning_fields_id, $questions)) {

								if(is_array($values)) {
									foreach ($values as $key => $value) {
										// if answer is given...
										$r = strlen($value) > 0 ? substr($value,0,5000) : '(ej svar)';
										// if checkbox checked - print 'ok' instead of repeating label
										$r = $field_meta['field'] == 'checkbox' && strlen($r) > 0 ? 'ok (kryssad checkbox)' : $r ;
										$s .=  strip_tags($r) ."\n";
									}
								}
								// remove question from array $questions_remains
								$questions_remains = array_diff($questions_remains, array($plugin_kursbokning_fields_id));
							}
							
						} 
						
					}
					
				}
				
				$s1 = "\nObesvarade frågor:\n";
				
				foreach($questions_remains as $question_remains) {
					$plugin_kursbokning_fields_id = $question_remains;
					// get label from database
					$field_meta = $kursbokning->getKursbokningFieldId($plugin_kursbokning_fields_id);
					
					// check for match
					if($field_meta) {
						if($field_meta['field'] != 'description') {
							//$s1 .= $field_meta['label'] .' (#id: '.$plugin_kursbokning_fields_id.')<br />';
							$s1 .= $field_meta['label'] ." (#id: ".$plugin_kursbokning_fields_id.")\n";
						}
					}				
				}
				
				
				// convert array $questions to comma separated string
				$questions = implode(',',$questions);
				
				$utc_created = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');				
				$s2 = $s . $s1;
				$log = $utc_created .' | '. $s_kurser;

				// reply default
				$return = array('db' => 'false', 'msg' => 'Informationen sparades inte. Vänligen kontakta oss för manuell hantering.');

				
				
				
				
				
				
				
				
				
				///// 20150709 ////////
				// get expected parameters - check expected personnummer...
				$course_meta = $kursbokning->getKursbokningId($plugin_kursbokning_id);
				$fields = json_decode($course_meta['fields']);
				$expected_personnummer = get_field_setting("personnummer",$fields);
				if ($expected_personnummer == 2 && $personnummer == null) {
					$return = array('db' => 'false', 'msg' => 'Personnummer saknas och du kan därför inte använda formuläret. Prova att uppdatera sidan och fyll i formläret igen. Om problemet kvarstår så vänligen kontakta oss för manuell hantering.');
					echo json_encode($return);
					die();
				}
				///// 20150709 ////////
				
				
				
				
				
				
				
				
				
				
				
				$result = $kursbokning->setKursbokningKursAnmalanInsert($plugin_kursbokning_id, $plugin_kursbokning_kurser_id, $fnamn, $enamn, $personnummer, $personnummer_yyyy, $epost, $adress, $postnummer, $ort, $mobil, $telefon, $kommun, $lan, $country, $organisation, $fakturaadress, $kod, $ip, $agent, $token, $s2, $files, $utc_created, $log);
				if(!$result) {
					$return = array('db' => 'false', 'msg' => 'Informationen sparades inte. Vänligen kontakta oss för manuell hantering.');
					echo json_encode($return);
				} else {
					
					// send confirmation email
					// get terms and type of this reservation
					// moved before database insert in order to doubblecheck ...
					// $course_meta = $kursbokning->getKursbokningId($plugin_kursbokning_id);
					
					//if(!$course_meta) {die();}
					
					$subject = $postinfo = '';
					$m_body = "Hej!<br /><br />";
					$m_body .=  $fnamn.' '.$enamn.',';
					$m_body .= "<br />";					
					switch($course_meta['type']) {
						case 'Folkhögskolekurs':
							$m_body .= 'Vi har tagit emot din ansökan till kurs:';
							$subject = 'Ansökan till kurs';
							$postinfo = "För mer information ber vi dig kontakta oss!<br /><br />Vänligen,"; 
						break;
						case 'Evenemang':
							$m_body .= 'Vi har tagit emot din bokning av evenemang:';
							$subject = 'Bokning av evenemang';
							$postinfo = "För mer information ber vi dig kontakta oss!<br /><br />Vänligen,"; 
						break;					
						case 'Uppdragsutbildning':
						default:
							$m_body .= 'Vi har tagit emot din bokning av kurs:';
							$subject = 'Bokning av kurs';
							$postinfo = "För mer information ber vi dig kontakta oss!<br /><br />Vänligen,"; 
						break;					
					}
					$m_body .= "<br /><br />";
					$m_body .= $s_kurser;					
					$m_body .= "<br /><br />";
					$m_body .= "Det här meddelandet skickas automatiskt. ";
					$m_body .= $postinfo;
					$m_body .= "<br /><br />";
					$m_body .= $_SESSION['site_name'];
					$m_body .= " | ";
					$m_body .= $_SESSION['site_email'];
					$m_body .= " | ";
					$m_body .= CMS_URL;
					
					$m_body_add = "<br /><br />";
					$m_body_add .= "Information och villkor";
					$m_body_add .= "<br /><br />";
					$m_body_add .= nl2br($course_meta['terms']);

					$m_body_add .= "<br /><br />";
					$m_body_add .= "Förnamn efternamn, epost:";
					$m_body_add .= "<br />";
					$m_body_add .=  $fnamn.' '.$enamn.', '.$epost;
					$m_body_add .= "<br /><br />";
					$m_body_add .= "Adress:";
					$m_body_add .= "<br />";
					$m_body_add .=  $adress;
					$m_body_add .= "<br /><br />";
					$m_body_add .= "Postnummer ort:";
					$m_body_add .= "<br />";
					$m_body_add .=  $postnummer.' '.$ort;
					$m_body_add .= "<br /><br />";
					$m_body_add .= "Mobil | Telefon:";
					$m_body_add .= "<br />";
					$m_body_add .=  $mobil.' | '.$telefon;
					$m_body_add .= "<br /><br />";
					$m_body_add .= "Kommun | Län:";
					$m_body_add .= "<br />";
					$m_body_add .=  $kommun.' | '.$lan;
					$m_body_add .= "<br /><br />";
					$m_body_add .= "Land:";
					$m_body_add .= "<br />";
					$m_body_add .=  $country;
					// om organisation är noterad
					if(strlen($organisation) > 0) {
						$m_body_add .= "<br /><br />";					
						$m_body_add .= "Organisation:";
						$m_body_add .= "<br />";
						$m_body_add .=  $organisation;
					}
					// om fakturaadress är noterad
					if(strlen($fakturaadress) > 0) {
						$m_body_add .= "<br /><br />";
						$m_body_add .= "Fakturaadress:";
						$m_body_add .= "<br />";
						$m_body_add .=  $fakturaadress;
					}
					$m_body_add .= "<br /><br />";
					$m_body_add .= "Frågor i formulär:";
					$m_body_add .= "<br />";
					$m_body_add .=  nl2br($s2);
					if(strlen($attachments_files_uploaded) > 0) {
						$m_body_add .= "<br /><br />";
						$m_body_add .= "Filer som bifogas (observera att endast del av filnamn visas):";
						$m_body_add .= "<br />";
						$m_body_add .= $attachments_files_uploaded;
						$m_body_add .= "<br />";
					}
					$m_body_add .= "<br />....................";
					$m_body_reply = $m_body . $m_body_add;

					$to = $epost;
					$to_name = $fnamn.' '.$enamn;
					if(isValidString($to_name, 'name')) {
						//
					} else {
						$to_name = $epost;
					}
					
					$headers = $_SESSION['site_email'];
					
					// attachments
					
					// include file
					include_once CMS_ABSPATH .'/cms/includes/inc.send_a_mail.php';
					
					// confirm message
					$msg = '<h3>Tack för '.mb_strtolower($subject,'UTF-8').'!</h3>';
					$msg .= $m_body;
					$msg .= '<br /><br />';
					
					// send mail
					if(send_a_mail($_SESSION['token'], $to, $to_name, $_SESSION['site_email'], $_SESSION['site_copyright'], $subject, $m_body_reply,'')) {	
						$msg .= 'En bekräftelse skickas nu till angiven e-postadress.';
						$return = array('db' => 'true', 'msg' => $msg);
						
						$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history_email = new HistoryEmail();
						$history_email->setHistoryEmail($to, $_SESSION['site_email'], $subject, $m_body, $utc_datetime);						
					} else {
						$msg .= 'En bekräftelse kunde inte skickas till angiven e-postadress.';
						$return = array('db' => 'true', 'msg' => $msg);
					}
					echo json_encode($return);
					
					// notifera	(per formulär)
					$subject = $postinfo = '';
					$m_body = "Hej,<br /><br />";
					
					switch($course_meta['type']) {
						case 'Folkhögskolekurs':
							$m_body .= 'Ny ansökan till kurs:';
							$subject = $_SESSION['site_name'].' - kursansökan';
							
						break;
						case 'Evenemang':
							$m_body .= 'Ny bokning av evenemang:';
							$subject = $_SESSION['site_name'].' - evenemangsbokning';
						break;					
						case 'Uppdragsutbildning':
						default:
							$m_body .= 'Ny bokning av kurs:';
							$subject = $_SESSION['site_name'].' - kursbokning';
						break;					
					}
					$m_body .= "<br />";
					$m_body .= $s_kurser;
					$m_body .= "<br /><br />";
					$m_body .= "Förnamn efternamn, epost:";
					$m_body .= "<br />";
					$m_body .=  $fnamn.' '.$enamn.', '.$epost;
					$m_body .= "<br /><br />";
					$m_body .= "Adress:";
					$m_body .= "<br />";
					$m_body .=  $adress;
					$m_body .= "<br /><br />";
					$m_body .= "Postnummer ort:";
					$m_body .= "<br />";
					$m_body .=  $postnummer.' '.$ort;
					$m_body .= "<br /><br />";
					$m_body .= "Mobil | Telefon:";
					$m_body .= "<br />";
					$m_body .=  $mobil.' | '.$telefon;
					$m_body .= "<br /><br />";
					$m_body .= "Kommun | Län:";
					$m_body .= "<br />";
					$m_body .=  $kommun.' | '.$lan;
					$m_body .= "<br /><br />";
					$m_body .= "Land:";
					$m_body .= "<br />";
					$m_body .=  $country;
					$m_body .= "<br /><br />";
					$m_body .= "Organisation:";
					$m_body .= "<br />";
					$m_body .=  $organisation;
					$m_body .= "<br /><br />";
					$m_body .= "Fakturaadress:";
					$m_body .= "<br />";
					$m_body .=  $fakturaadress;
					$m_body .= "<br /><br />";
					$m_body .= "Frågor:";
					$m_body .= "<br />";
					$m_body .=  nl2br($s2);
					if(strlen($attachments_files_uploaded) > 0) {
						$m_body_add .= "<br /><br />";
						$m_body_add .= "Filer som bifogas (observera att endast del av filnamn visas):";
						$m_body_add .= "<br />";
						$m_body_add .= $attachments_files_uploaded;
						$m_body_add .= "<br />";
					}
					$m_body .= "<br /><br />";
					$m_body .= "Det här meddelandet skickas automatiskt när formuläret för kursbokning används";
					$m_body .= $postinfo;
					$m_body .= "<br /><br />";
					$m_body .= $_SESSION['site_name'] .', '. $_SESSION['site_email'];
					$m_body .= " | ";
					$m_body .= CMS_URL ;
										
					$to = $course_meta['email_notify'];

					// get settings for file handling
					$form = $kursbokning->getKursbokningId($plugin_kursbokning_id);
					$action = $form ? $form['file_action'] : 1;
					$file_move_path = $form ? $form['file_move_path'] : CMS_ABSPATH .'/content/uploads/plugins/kursbokning/';
					
					// attach uploaded files to admin mail, default true
					$file_attach = $form ? $form['file_attach'] : 1;
					
					$attachments_files_and_path2 = $file_attach == 1 ? $attachments_files_and_path : array();
					//echo '<br />$attachments_files_and_path2:<br />';
					//print_r($attachments_files_and_path2);
					// send mail
					if(send_a_mail($_SESSION['token'], $to, $to, $_SESSION['site_email'], $_SESSION['site_copyright'], $subject, $m_body, $attachments_files_and_path2)) {
						$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history_email = new HistoryEmail();
						$history_email->setHistoryEmail($to, $_SESSION['site_email'], $subject, $m_body, $utc_datetime);
						
						// admin email sent - handle uploaded files
						// start delete eventually uploaded files not to be sent
						// read directory "/content/uploads/plugins/kursbokning"

						function in_array_r($needle, $haystack, $strict = false) {
							foreach ($haystack as $item) {
								if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
									return true;
								}
							}
							return false;
						}						
						
						
						if ($handle = opendir(CMS_ABSPATH .'/content/uploads/plugins/kursbokning')) {
							while (false !== ($entry = readdir($handle))) {
								if ($entry != "." && $entry != "..") {

									// check just files pattern: underscore + 5 first characters in token + underscore									
									$part_token = '_'.substr($_SESSION['token'], 0, 5).'_';
									
									if(strpos($entry, $part_token) > 0) {
										$file = CMS_ABSPATH .'/content/uploads/plugins/kursbokning/'.$entry;
										if (in_array_r($file, $attachments_files_and_path)) {
											// most likely delete...
											// action to files attached
											switch($action) {
												
												// delete
												case 1:
													unlink($file);
												break;
												
												// leave
												case 2:
												break;
												
												// move
												case 3:
													rename($file, $_SERVER['DOCUMENT_ROOT'] . $file_move_path . $entry);
												break;
											
											}
											
										} else {
										
											//echo '<br />file to delete:'. $file;
											unlink($file);
										} 
										
									}
								}
							}
							closedir($handle);
						}							
						
						
					}
					
					// notifera	(per kurs)
					$subject = $postinfo = '';
					$m_body = "Hej,<br /><br />";
					
					switch($course_meta['type']) {
						case 'Folkhögskolekurs':
							$m_body .= 'Ny ansökan till kurs:';
							$subject = $_SESSION['site_name'].'- kursansökan';
							
						break;
						case 'Evenemang':
							$m_body .= 'Ny bokning av evenemang:';
							$subject = $_SESSION['site_name'].'- evenemangsbokning';
						break;					
						case 'Uppdragsutbildning':
						default:
							$m_body .= 'Ny bokning av kurs:';
							$subject = $_SESSION['site_name'].'- kursbokning';
						break;					
					}
					$m_body .= "<br /><br />";
					$m_body .= 'Det här meddelandet skickas automatiskt. E-postadressen '.$epost.' är registrerad för notifiering.';
					$m_body .= "<br /><br />";					
					$m_body .= $s_kurser;
					$m_body .= "<br /><br />";
					$m_body .= "Förnamn efternamn, epost:";
					$m_body .= "<br />";
					$m_body .=  $fnamn.' '.$enamn.', '.$epost;
					$m_body .= "<br /><br />";
					$m_body .= "Adress:";
					$m_body .= "<br />";
					$m_body .=  $adress;
					$m_body .= "<br /><br />";
					$m_body .= "Postnummer ort:";
					$m_body .= "<br />";
					$m_body .=  $postnummer.' '.$ort;
					$m_body .= "<br /><br />";
					$m_body .= "Mobil | Telefon:";
					$m_body .= "<br />";
					$m_body .=  $mobil.' | '.$telefon;
					$m_body .= "<br /><br />";
					$m_body .= "Kommun | Län:";
					$m_body .= "<br />";
					$m_body .=  $kommun.' | '.$lan;
					$m_body .= "<br /><br />";
					$m_body .= "Land:";
					$m_body .= "<br />";
					$m_body .=  $country;
					// om organisation är noterad
					if(strlen($organisation) > 0) {
						$m_body .= "<br /><br />";
						$m_body .= "Organisation:";
						$m_body .= "<br />";
						$m_body .=  $organisation;
					}
					// om fakturaadress är noterad
					if(strlen($fakturaadress) > 0) {
						$m_body .= "<br /><br />";
						$m_body .= "Fakturaadress:";
						$m_body .= "<br />";
						$m_body .=  $fakturaadress;
					}
					$m_body .= "<br /><br />";
					$m_body .= "Frågor:";
					$m_body .= "<br />";
					$m_body .=  nl2br($s2);
					$m_body .= "<br /><br />";
					if(strlen($attachments_files_uploaded) > 0) {
						$m_body_add .= "<br /><br />";
						$m_body_add .= "Filer som bifogas (observera att endast del av filnamn visas):";
						$m_body_add .= "<br />";
						$m_body_add .= $attachments_files_uploaded;
						$m_body_add .= "<br />";
					}
					$m_body_add .= "<br />....................";
					$m_body .= "<br /><br />";
					$m_body .= $postinfo;
					$m_body .= "<br /><br />";
					$m_body .= $_SESSION['site_name'];
					$m_body .= " | ";
					$m_body .= $_SESSION['site_email'];
					$m_body .= " | ";
					$m_body .= CMS_URL ;
										
					if($notifies) {
						foreach($notifies as $notify) {
							$to = $notify;
							
							// send mail
							if(send_a_mail($_SESSION['token'], $to, $to, $_SESSION['site_email'], $_SESSION['site_copyright'], $subject, $m_body,'')) {
								$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
								$history_email = new HistoryEmail();
								$history_email->setHistoryEmail($to, $_SESSION['site_email'], $subject, $m_body, $utc_datetime);						
							}							
						}
					}
					
					

				}

				
			break;

			
			case 'course_confirm';
				
				$plugin_kursbokning_kurs_anmalan_id = isset($_POST['plugin_kursbokning_kurs_anmalan_id']) && is_numeric($_POST['plugin_kursbokning_kurs_anmalan_id']) ? $_POST['plugin_kursbokning_kurs_anmalan_id'] : null;
				if(!$plugin_kursbokning_kurs_anmalan_id) {die;};		

				$result = $kursbokning->getKursbokningKurserAnmalanId($plugin_kursbokning_kurs_anmalan_id);		
				if(!$result) { die();}
				
				$fnamn = $result['fnamn'];
				$enamn = $result['enamn'];
				$epost = $result['epost'];
				$plugin_kursbokning_kurser_id = $result['plugin_kursbokning_kurser_id'];
				$plugin_kursbokning_id = $result['plugin_kursbokning_id'];

				$s_kurser = '';
				$ids = null;
				
				if(strpos($plugin_kursbokning_kurser_id, ',')) {
					$ids = array();
					$ids = explode(',',$plugin_kursbokning_kurser_id);
				}
				// get name of cources
				if($ids) {
					foreach($ids as $id) {
						$kurs_meta = $kursbokning->getKursbokningKursId($id);
						$s_kurser .= $kurs_meta['title'] .'<br />';
					}
				} else {
					$kurs_meta = $kursbokning->getKursbokningKursId($plugin_kursbokning_kurser_id);
					$s_kurser .= $kurs_meta['title'] .'<br />';	
				}
				
				if($result) {
					
					$course_meta = $kursbokning->getKursbokningId($kurs_meta['plugin_kursbokning_id']);

					// send confirmation email					
					if(!$course_meta) {die();}

					$subject = $postinfo = '';
					$m_body = "Hej,<br /><br />";
					$m_body .=  $fnamn.' '.$enamn.', '.$epost;
					$m_body .= "<br />";					
					switch($course_meta['type']) {
						case 'Folkhögskolekurs':
							$m_body .= 'Vi har tagit emot din ansökan till kurs:';
							$subject = 'Ansökan till kurs';
							$postinfo = "För mer information ber vi dig kontakta oss!<br /><br />Vänligen,"; 
						break;
						case 'Evenemang':
							$m_body .= 'Vi har tagit emot din bokning av evenemang:';
							$subject = 'Bokning av evenemang';
							$postinfo = "För mer information ber vi dig kontakta oss!<br /><br />Vänligen,"; 
						break;					
						case 'Uppdragsutbildning':
						default:
							$m_body .= 'Vi har tagit emot din bokning av kurs:';
							$subject = 'Bokning av kurs';
							$postinfo = "För mer information ber vi dig kontakta oss!<br /><br />Vänligen,"; 
						break;					
					}
					$m_body .= "<br />";
					$m_body .= $s_kurser;					
					$m_body .= "<br /><br />";
					$m_body .= "Det här meddelandet skickas automatiskt. ";
					$m_body .= $postinfo;
					$m_body .= "<br /><br />";
					$m_body .= $_SESSION['site_name'];
					$m_body .= " | ";
					$m_body .= $_SESSION['site_email'];
					$m_body .= " | ";
					$m_body .= CMS_URL;
					$m_body .= "<br /><br />- - - - - - - - - - - - - - - - - - - -<br /><br />";
					$m_body .= "Information och villkor";
					$m_body .= "<br />";
					$m_body .= $course_meta['terms'];
					$m_body .= "<br /><br />- - - - - - - - - - - - - - - - - - - -";
					
					$to = $epost;
					$headers = $_SESSION['site_email'];
					
					// include file
					include_once CMS_ABSPATH .'/cms/includes/inc.send_a_mail.php';
					// send mail
					if(send_a_mail($_SESSION['token'], $to, $to, $_SESSION['site_email'], $_SESSION['site_copyright'], $subject, $m_body,'')) {
						echo 'En bekräftelse skickas nu till angiven e-postadress.';
						$utc_datetime = utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s');
						$history_email = new HistoryEmail();
						$history_email->setHistoryEmail($to, $_SESSION['site_email'], $subject, $m_body, $utc_datetime);						
					}
					
				}
				
			break;

			
			
			
		}		
		
	}
}

?>