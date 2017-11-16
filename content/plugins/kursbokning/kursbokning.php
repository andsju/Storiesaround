<?php

// include core 
//--------------------------------------------------
require_once '../../../cms/includes/inc.core.php';
include_once '../../../cms/includes/inc.functions_pages.php';

// css files, loaded in inc.header.php 
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-datatables/style.css',
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/content/plugins/kursbokning/css/style.css',
	CMS_DIR.'/cms/libraries/fileuploader/fileuploader.css');
	

//--------------------------------------------------
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery.ui.datepicker-sv.js', 
	CMS_DIR.'/cms/libraries/js/functions.js', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
	CMS_DIR.'/cms/libraries/jquery-timeago/jquery.timeago.js',
	CMS_DIR.'/cms/libraries/jquery-plugin-validation/jquery.validate.js', 
	CMS_DIR.'/cms/libraries/jquery-datatables/jquery.datatables.min.js',
	CMS_DIR.'/cms/libraries/jquery-menubar/jquery.ui.menubar.js',
	CMS_DIR.'/content/plugins/kursbokning/js/lan_kommun.js',
	CMS_DIR.'/content/plugins/kursbokning/js/jquery.autosize.js',
	CMS_DIR.'/cms/libraries/fileuploader/fileuploader.js');

?>
<!DOCTYPE html> 

<html lang="sv">

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<title>Kurs - bokning/ansökan</title>

	<?php 
	//load css files
	foreach ( $css_files as $css ):
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen,projection" href="'.$css.'" />';
	endforeach; 
	echo "\n";
	echo "\n\t".'<script src="'.CMS_DIR.'/cms/libraries/jquery/jquery.min.js"></script>';
	echo "\n";
	?>
	
</head>

<body style="max-width:800px;margin:0 auto;background:#F8F8F8;">


<?php

// initiate class
$kursbokning = new Kursbokning();
	
	$plugin_kursbokning_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;

	if(!$plugin_kursbokning_id) {die;};
	
		if($plugin_kursbokning_id) {

			include 'kursbokning_functions.php';
		
			$result = $kursbokning->getKursbokningId($plugin_kursbokning_id);

			// check if $result....
			if(!$result) {die();}

			$code = $talkcode = $post_confirm = '';
			switch ($result['type']) {
				case 'Uppdragsutbildning':
					$code = 'bokningskod';
					$talkcode = 'boka en kurs';
					$post_confirm = 'Vill du skicka din kursbokning?';
				break;
				case 'Evenemang':
					$code = 'bokningskod';
					$talkcode = 'boka ett evenemang';
					$post_confirm = 'Vill du skicka din evenemangsbokning?';
				break;
				case 'Folkhögskolkurs':
				default:
					$code = 'ansökningskod';
					$talkcode = 'ansöka till en kurs';
					$post_confirm = 'Vill du skicka din ansökan?';					
				break;
			}
			
			// check status and web_reservation
			if($result['status']==0 || $result['web_reservation']==0) { die(); }

			// check if courses can be reserved from this form
			$kurser = $kursbokning->getKursbokningIdKurserWebbPublicering($plugin_kursbokning_id);
			
			if(!$kurser) {die();}

			// check date
			$start = strtotime($result['utc_start_publish']);			
			$end = strtotime($result['utc_end_publish']);
			$now = time();
			
			$show = false;			
			if($end) {
				if($start <= $now && $end >= $now) {
					$show = true;
				}
			} else {
				if($start <= $now) {
					$show = true;
				}
			}
			if(!$show) { die();}

			$fields = json_decode($result['fields']);
			$adress = get_field_setting("adress",$fields);
			$postnummer = get_field_setting("postnummer",$fields);
			$ort = get_field_setting("ort",$fields);			
			$mobil = get_field_setting("mobil",$fields);
			$telefon = get_field_setting("telefon",$fields);
			$personnummer = get_field_setting("personnummer",$fields);
			$lan = get_field_setting("lan",$fields);
			$kommun = get_field_setting("kommun",$fields);
			$country = get_field_setting("country",$fields);
			$organisation = get_field_setting("organisation",$fields);
			$fakturaadress = get_field_setting("fakturaadress",$fields);

			
			// allowed file extensions
			$extensions = array();
			$extensions = explode(",",$result['file_extensions']);
			
			$str_extensions = '';
			$i = 0;
			foreach($extensions as $extension) {
				$str_extensions .= "'".trim($extension)."'";
				if($i < count($extensions)-1) {
					$str_extensions .= ", ";
				}
				 $i++;
			}
			
			function show_required($field) {
				$s = $field == 2 ? '*' : '';
				echo $s;
			}
			function show_field_required($field) {
				$s = $field == 1 ? ' *' : '';
				return $s;
			}
			
			?>
			
			<div id="course_booking" style="background:#FFF;width:100%;box-sizing:border-box;">
			
				<h2><?php echo $result['title'];?></h2>

				<div id="use_code">
					<p>
					För att <?php echo $talkcode;?> behöver du en <a href="#" title="Koden säkerställer att du använder en giltig e-postadress."><?php echo $code;?></a>. 
					</p>

					<div class="row">
						<div class="col">
							E-post<br />
							<input type="text" class="text" id="email_code_request" style="width:300px; "/>
							<input type="hidden" id="code_type" value="<?php echo $code;?>" />
							<span class="toolbar"><button id="btn_code_request">Skicka <?php echo $code;?></button></span>
						</div>
					</div>
					<div class="row" id="code_request_respons"></div>	

					<div class="row" style="padding-top:20px;">
						Har du redan en <?php echo $code;?> anger du e-post samt <?php echo $code;?> här nedan. 
					</div>
				</div>
				
				<div class="row" style="margin-bottom:50px;">
					<div class="col cols-2">
					<div class="quest_label">E-post</div>
					<input type="text" class="text" id="email_code_use">
					</div>
					<div class="col cols-2">
					<div class="quest_label">Kod</div>
					<input type="text" class="text" name="code" id="code" style="width:100px;" maxlength="10">
					<span id="ajax_spinner_use_code" style='display:none'><img src="css/images/spinner.gif" alt="spinner"></span>
					</div>
				</div>
				
				<div class="row" style="padding:5px 0 20px; 0">
					<span class="toolbar"><button id="btn_code_use">Använd <?php echo $code;?></button></span>					
					<div id="code_use_respons"></div>
				</div>
				


				<form id="form_kursbokning" style="display:none;">
				<input type="hidden" name="plugin_kursbokning_id" id="plugin_kursbokning_id" value="<?php echo $plugin_kursbokning_id;?>" />
				<input type="hidden" name="ip" id="ip" value="<?php echo $_SERVER['REMOTE_ADDR'];?>" />
				<input type="hidden" name="agent" id="agent" value="<?php echo $_SESSION['HTTP_USER_AGENT'];?>" />
				<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
			
				<h4>Information och villkor</h4>
				<div class="row" id="terms" style="padding:10px 0;max-height:100px;overflow-y:scroll;">				

					<?php echo nl2br($result['terms']);?>

				</div>
			
				<?php
					
				$result_fields = $kursbokning->getKursbokningFieldsId($plugin_kursbokning_id);
				$_SESSION['site_language'] = 'swedish';


				//print_r2($kurser);
				if($kurser) {
					
					$course = $result['type'];
					echo '<input type="hidden" name="type" id="type" value="'.$result['type'].'" />';
					
					switch ($result['type']) {
						case 'Uppdragsutbildning':
							$html = '<h3>1 Välj kurs</h3>'; 
							$html .= '<table class="courses_list" style="width:100%">';
								$html .= '<thead><tr>';
									$html .= '<th>Kurs';
									$html .= '</th>';
									$html .= '<th>Datum';
									$html .= '</th>';
									$html .= '<th>Plats';
									$html .= '</th>';
									$html .= '<th class="right">Kostnad';
									$html .= '</th>';
									$html .= '<th>Bokning';
									$html .= '</th>';
								$html .= '</tr></thead>';
							foreach($kurser as $kurs) {

								$utc_date_start = ($kurs['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_start'], $dtz, 'Y-m-d H:i:s')) : null;
								$utc_date_end = ($kurs['utc_end']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_end'], $dtz, 'Y-m-d H:i:s')) : null;
							
								// skip course if date is not set or already passed
								if(!isset($utc_date_start) || !isset($utc_date_end)) { continue;}
								if(date('Y-m-d') > $utc_date_start->format('Y-m-d')) { continue;}
								
								
								$html .= '<tr>';
									$html .= '<td><span class="course_title">'.$kurs['title'].'</span></td>';
									
									$dts = '';
									if($utc_date_end) {
										if($utc_date_start->format('Y')==$utc_date_end->format('Y')) {
											$dts = $utc_date_start->format('Y-m-d')==$utc_date_end->format('Y-m-d') ? $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y') : $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
										} else {
											$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y').' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
										}
									} else {
										$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y');
									}
									
									$html .= '<td>'.$dts.'</td>';
									$html .= '<td>'.$kurs['location'].'</td>';
									$html .= '<td class="right">'.$kurs['cost'].'</td>';
									
									// check reservations						
									$row = $kursbokning->getKursbokningKurserAnmalanCount($kurs['plugin_kursbokning_kurs_id']);								
									$i = $row ? $row['count'] : 0;
								
									// booking can be done one day before... 
									$str ='';									
									if($kurs['participants'] > $i) {
										
										if(date('Y-m-d', strtotime("+1 days")) < $utc_date_start->format('Y-m-d')) {
											$str = '<span class="toolbar"><button value="" class="btn_choose" id="'.$kurs['plugin_kursbokning_kurs_id'].'">Boka</button></span>';
										} else {
											$str = 'Kontakta oss för ev bokning (nära kursstart)';
										}										
									} else {
										$str = '<span class="toolbar"><button value="" disabled>Fullbokad</button></span>';									
									}
									
									$html .= '<td style="width:10%;">'.$str.'</td>';
								$html .= '</tr>';
								$html .= '<tr style="display:none;">';
									$html .= '<td colspan="5" class="kurs"><div class="course_info">'.$kurs['description'].'</div></td>';
								$html .= '</tr>';
							}
							$html .= '</table>';
							echo $html;
						break;

						case 'Evenemang':


							$html = '<h3>1 Välj evenemang</h3>'; 
							foreach($kurser as $kurs) {

								$utc_date_start = ($kurs['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_start'], $dtz, 'Y-m-d H:i:s')) : null;
								$utc_date_end = ($kurs['utc_end']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_end'], $dtz, 'Y-m-d H:i:s')) : null;					

								// skip course if date is not set or already passed
								if(!isset($utc_date_start) || !isset($utc_date_end)) { continue;}
								if(date('Y-m-d') > $utc_date_start->format('Y-m-d')) { continue;}

								$html .= '<table class="courses_list"" style="width:500px;">';
							
									$dts = '';
									if($utc_date_end) {
										if($utc_date_start->format('Y')==$utc_date_end->format('Y')) {
											$dts = $utc_date_start->format('Y-m-d')==$utc_date_end->format('Y-m-d') ? $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y') : $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
										} else {
											$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y').' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
										}
									} else {
										$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y');
									}
									
									$html .= '<tr>';
										$html .= '<td>'.$dts.' | '.$kurs['location'].'</td>';

										// check reservations						
										$row = $kursbokning->getKursbokningKurserAnmalanCount($kurs['plugin_kursbokning_kurs_id']);								
										$i = $row ? $row['count'] : 0;
									
										// booking can be done if more than one week... 
										$str ='';									
										if($kurs['participants'] > $i) {
											
											if(date('Y-m-d', strtotime("+5 days")) < $utc_date_start->format('Y-m-d')) {
												$str = '<span class="toolbar"><button value="" class="btn_choose" id="'.$kurs['plugin_kursbokning_kurs_id'].'">Boka</button></span>';
											} else {
												$str = 'Kontakta oss för ev bokning';
											}										
										} else {
											$str = '<span class="toolbar"><button value="" disabled>Fullbokad</button></span>';									
										}
										
										$html .= '<td style="width:10%;">'.$str.'</td>';
									$html .= '</tr>';
									$html .= '<tr>';
										$html .= '<td><span class="course_title events">'.$kurs['title'].'</span></td>';
										$html .= '<td class="right">'.$kurs['cost'].'</td>';						
									$html .= '</tr>';
									$html .= '<tr style="display:none;">';
										$html .= '<td colspan="5" class="kurs"><div class=""><b>Mer info</b><br /> '.$kurs['description'].'</div></td>';
									$html .= '</tr>';
								
								$html .= '</table>';
							}						
							echo $html;

						break;

						case 'Folkhögskolekurs':
							$html = '<h3>1 Välj kurser</h3>'; 
							$html .= '<table class="courses_list" style="width:100%">';
								$html .= '<thead><tr>';
									$html .= '<th style="width:5%;"><span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
									$html .= '</th>';
									$html .= '<th>Kurs';
									$html .= '</th>';
									$html .= '<th style="text-align:right;">Datum kursstart';
									$html .= '</th>';
								$html .= '</tr></thead>';
							foreach($kurser as $kurs) {
								$html .= '<tr>';
									$utc_date_start = ($kurs['utc_start']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_start'], $dtz, 'Y-m-d H:i:s')) : new DateTime(get_utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s'));
									$utc_date_end = ($kurs['utc_end']>'2000-01-01 00:00') ? new DateTime(utc_dtz($kurs['utc_end'], $dtz, 'Y-m-d H:i:s')) : null;
									$html .= '<td><input type="checkbox" id="kurs_id[]" name="kurs_id[]" class="kurs_prio" value="'.$kurs['plugin_kursbokning_kurs_id'].'" style="margin-right:10px;" data-titel="'.$kurs['title'].'" data-id="'.$kurs['plugin_kursbokning_kurs_id'].'"></td>';
									$html .= '<td><span class="course_title">'.$kurs['title'].'</span></td>';
									$dts = '';
									if($utc_date_end) {
										if($utc_date_start->format('Y')==$utc_date_end->format('Y')) {
											$dts = $utc_date_start->format('Y-m-d')==$utc_date_end->format('Y-m-d') ? $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y') : $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
										} else {
											$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y').' — '.$utc_date_end->format('j ').strtolower($kursbokning->transl($utc_date_end->format('M'))).$utc_date_end->format(' Y');
										}
									} else {
										$dts = $utc_date_start->format('j ').strtolower($kursbokning->transl($utc_date_start->format('M'))).$utc_date_start->format(' Y');
									}									
									$html .= '<td style="text-align:right;">'.$dts.'</td>';
								$html .= '</tr>';
								$html .= '<tr style="display:none;">';
									$html .= '<td colspan="5" class="kurs"><div class="course_info">'.$kurs['description'].'</div></td>';
								$html .= '</tr>';
							}
							$html .= '</table>';
							echo $html;
						break;

					}
				
				}
		
			}
			
			
			if($result['type'] == 'Folkhögskolekurs') {

			?>
				<div id="kursval" style="display:none;"></div>
				<div><span class="ui-icon ui-icon-arrowthick-1-n" style="display:inline-block;"></span> Kryssa i de kurser du söker till i listan ovan.</div>
				<h3>Mina kursval</h3>
				<div>
					Du kan söka till max 5 kurser. Kurser som är markerade visas här nedan under rubriken 'Mina kursval'. Söker du till flera kurser måste du rangordna dem; 1 är ditt förstahandsval, 2 ditt andrahandsval etc.
					<div id="prioritera_tips" style="display:none;"></div>
				</div>
				<div class="row">
					<ul id="prioritera">
					</ul>
				</div>
				
			<?php
			
			}
			
			?>

			


				<div class="row" style="margin-top:30px;">
					<h3>2 Fyll i formuläret</h3>
					<p>Obligatoriska fält är markerade med *</p>
				</div>
				<div class="row">
					<div class="col cols-2">
					<div class="quest_label">Förnamn *</div>
					<input type="text" class="text" name="fnamn">
					</div>
					<div class="col cols-2">
					<div class="quest_label">Efternamn *</div>
					<input type="text" class="text" name="enamn">
					</div>
				</div>
				<?php if($personnummer) { ?>
				<div class="row">
					<div class="col">
					<div class="quest_label">Personnummer (ÅÅÅÅMMDD XXXX) <?php show_required($personnummer); ?></div>
					<input type="text" name="personnummer" id="personnummer" size="12" maxlength="13">
					</div>
				</div>
				<?php } ?>
				<?php if($adress>0) {?>
				<div class="row">
					<div class="col">
					<div class="quest_label">Adress <?php show_required($adress); ?></div>
					<input type="text" class="text" name="adress">
					</div>
				</div>
				<?php }?>
				<?php if($postnummer>0 || $ort>0) { ?>
				<div class="row">
					<?php if($postnummer>0) {?>
					<div class="col cols-2">
					<div class="quest_label">Postnummer <?php show_required($postnummer); ?></div>
					<input type="text" class="" name="postnummer" size="5" maxlength="5">
					</div>
					<?php }?>
					<?php if($ort>0) {?>
					<div class="col cols-2">
					<div class="quest_label">Ort <?php show_required($ort); ?></div>
					<input type="text" class="text" name="ort">
					</div>
					<?php }?>
				</div>
				<?php }?>
				<?php if($mobil>0 || $telefon>0) { ?>
				<div class="row">
					<?php if($mobil>0) {?>
					<div class="col cols-2">
					<div class="quest_label">Mobil <?php show_required($mobil); ?></div>
					<input type="text" class="" name="mobil" size="16" maxlength="10">
					</div>
					<?php }?>
					<?php if($telefon>0) {?>
					<div class="col cols-2">
					<div class="quest_label">Telefon <?php show_required($telefon); ?></div>
					<input type="text" class="" name="telefon" size="16" maxlength="16">
					</div>
					<?php }?>
				</div>
				<?php }?>
				<?php if($lan) { ?>
				<div class="row">
					<div class="col cols-2">
					<div class="quest_label">Folkbokförd i län <?php show_required($lan); ?></div>
					<select name="lan" id="lan" onChange="showSubKommun(this.value,this.form.kommun)">
						<option value=""></option>
						<option value="0"></option>
						<option value="10">Blekinge län</option>
						<option value="20">Dalarnas län</option>
						<option value="09">Gotlands län</option>
						<option value="21">Gävleborgs län</option>
						<option value="13">Hallands län</option>
						<option value="23">Jämtlands län</option>
						<option value="06">Jönköpings län</option>
						<option value="08">Kalmar län</option>
						<option value="07">Kronobergs län</option>
						<option value="25">Norrbottens län</option>
						<option value="12">Skåne län</option>
						<option value="01">Stockholms län</option>
						<option value="04">Södermanlands län</option>
						<option value="03">Uppsala län</option>
						<option value="17">Värmlands län</option>
						<option value="24">Västerbottens län</option>
						<option value="22">Västernorrlands län</option>
						<option value="19">Västmanlands län</option>
						<option value="14">Västra Götalands län</option>
						<option value="18">Örebro län</option>
						<option value="05">Östergötlands län</option>
					</select>
					</div>
					<?php if($kommun) { ?>
					<div class="col cols-2">
					<div class="quest_label">Kommun <?php show_required($kommun); ?></div>
					<select name="kommun" id="kommun">
						<option value=""></option>
						<option value="0"></option>
					</select>			
					</div>
					<?php } ?>
				</div>
				<?php } ?>
				<?php if($country) { ?>
				<div class="row">
					<div class="col cols-2">
					<div class="quest_label">Land <?php show_required($country); ?></div>
					<select name="country" id="country">
					<script>
					var states = new Array("", "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burma", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic", "Congo, Republic of the", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Greenland", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Mongolia", "Morocco", "Monaco", "Mozambique", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Norway", "Oman", "Pakistan", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Samoa", "San Marino", " Sao Tome", "Saudi Arabia", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe");
					for(var hi=0; hi<states.length; hi++)
					document.write("<option value=\""+states[hi]+"\">"+states[hi]+"</option>");
					</script>
					</select>				
					</div>
				</div>
				<?php } ?>
				<?php if($organisation>0) {?>
				<div class="row">
					<div class="col">
					<div class="quest_label">Organisation | företag | förening <?php show_required($organisation); ?></div>
					<input type="text" class="text" name="organisation">
					</div>
				</div>
				<?php }?>
				<?php if($fakturaadress>0) {?>
				<div class="row">
					<div class="col">
					<div class="quest_label">Fakturaadress (om annan än ovan) <?php show_required($fakturaadress); ?></div>
					<textarea class="field" name="fakturaadress" id="fakturaadress"></textarea>
					</div>
				</div>
				<?php }?>


				
				<!--upload-->
				<?php if($result['file_upload'] == 1) { ?>
				<div class="row">
					<div class="col" style="border-top:1px solid black;margin:10px 0;">
						<?php
						echo nl2br($result['file_instruction']);
						?>
					</div>
					<div class="col">
						<div id="file-uploader">
							<noscript>          
								<p>Please enable JavaScript to use file uploader.</p>
								<!-- or put a simple form for upload here -->
							</noscript>
						</div>
						<?php
						echo '<span class="code text-bigger">Filtyper => '. $result['file_extensions'] .'</span>';
						?>
					</div>
				</div>
				
				<div id="filesUploaded" class="clearfix"></div>
				<div id="filesUploaded2" class="clearfix"></div>
				
				<div id="sizelimit" style="width:90%;color:red;font-weight:bold;display:none;"></div>

				<div class="row">
					<div class="col" style="border-bottom:1px solid black;margin:10px 0;">
					</div>
				</div>
				<?php } else { echo '<div id="file-uploader" style="display:none;"></div>';}?>
				

				<!--<ul id="custom_fields">-->
				<?php
				$result_fields = $kursbokning->getKursbokningFieldsId($plugin_kursbokning_id);

				function getTypeFields($field, $label, $values, $id, $required) {
					$html = '';
					switch($field) {
						case 'text':
							$html = '<div class="quest_label">'.nl2br($label.show_field_required($required)).'</div><input type="text" class="field" name="q'.$id.'[]" />';
						break;
						case 'checkbox':
							$html = '<input type="checkbox" class="field" name="q'.$id.'[]" value="'.$label.'"/> <span class="quest_label">'.nl2br($label.show_field_required($required)).'</span>';
						break;
						case 'checkboxes':
							$html = '<div class="quest_label"><span>'.nl2br($label.show_field_required($required)).'</span></div>';
							$values = json_decode($values);
							if(is_array($values)) {
								foreach($values as $key=>$value) {
									$html .= '<div><input type="checkbox" name="q'.$id.'[]" value="'.$value.'"> <span class="quest_label">'.$value.'</span></div>';
								}
							}
						break;
						case 'radio':
							$html = '<div class="quest_label">'.nl2br($label.show_field_required($required)).'</div>';
							$values = json_decode($values);
							if(is_array($values)) {
								foreach($values as $key=>$value) {
									$html .= '<div><input type="radio" name="q'.$id.'[]" value="'.$value.'"> <span class="quest_label">'.$value.'</span></div>';
								}
							}
						break;					
						case 'textarea':
							$html = '<div class="quest_label">'.nl2br($label.show_field_required($required)).'</div><textarea class="field" maxlength="5000" name="q'.$id.'[]"></textarea>';
						break;
						case 'select':
							$html = '<div class="quest_label">'.nl2br($label.show_field_required($required)).'</div><select class="field" name="q'.$id.'[]">';
							$html .= '<option value=""></option>';						
							$values = json_decode($values);
							if(is_array($values)) {
								foreach($values as $key=>$value) {
									$html .= '<option value="'.$value.'">'.$value.'</option>';
								}
							}
							$html .= '</select><br />';
						break;
						case 'description':
							$html = '<div class="quest_label"><span>'.nl2br($label).'</span></div>';
						break;
					}
					return $html;
				}

				$validate_field_required = $validate_field_message = '';
				
				if($result_fields) {
					
					foreach($result_fields as $result_field) {
						$label = $result_field['label'];
						$field = $result_field['field'];
						$required = $result_field['required'];
						$values = $result_field['field_values'];
						$id = $result_field['plugin_kursbokning_fields_id'];
					
						echo '<div class="row col">';
						echo getTypeFields($field, $label, $values, $id, $required);
						// hidden input to hold expected fields
						echo '<input type="hidden" name="questions[]" value="'.$id.'" />';						
						echo '</div>';
						if($required) {
							$validate_field_required .= " 'q".$id."[]': 'required',";
							$validate_field_message .= " 'q".$id."[]': '* Obligatorisk uppgift',";
						}
					}
				}
				
				?>
				<div class="row" style="margin-top:30px;">
					<h3>3 Skicka in uppgifterna</h3>
				</div>
				<div class="row">
					<div class="col">
						<input type="checkbox" name="pul" id="pul"> <span class="quest_label">Jag godkänner att mina uppgifter lagras för databearbetning</span>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<span class="toolbar"><button id="btn_save">Skicka</button></span>
					</div>
				</div>
			
				</form>
				
				<div class="row">
					<div id="form_confirm">
					</div>
				</div>
				

			</div>



<?php
// load javascript files
foreach ( $js_files as $js ) {
	echo "\n".'<script src="'.$js.'"></script>';
}
?>
<script>

	// get querystring paramater 
	function getParameterByName(name) {
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
			results = regex.exec(location.search);
		return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}

	function showSubKommun(subCat,selectObj) {
		selectObj.length = 0;
		if (subCat == 0) selectObj[0] = new Option("");
		var j = 1;
		var elm = null;
		selectObj[0] = new Option("");
		for (var k = 0; (elm = kommun[k]); ++k) {	
			if (elm.lankod == subCat) {
				//selectObj[j++] = new Option(elm.kommun, elm.kommunkod);
				selectObj[j++] = new Option(elm.kommun, elm.kommun);
			}
		}
	}	
		
	function personnummer(input) {
		
		// remove '-' and empty space if neccasary 
		input = input.replace(/\-/,'');
		input = input.replace(/\s/,'');
		

		// accept date as ÅÅÅÅMMDDXXXX
		if (input.length != 12) {
			return false;
		}
		
		// check valid given date
        var year = parseInt(input.substr(0, 4), 10);
        var month = parseInt(input.substr(4, 2), 10) - 1;
        var day = parseInt(input.substr(6, 2), 10);
		
        var date = new Date(year, month, day);
		
		if ( isNaN( date ) ) { 
			return false;
		}
		
		// get check number
        var check = parseInt(input.substr(11, 1), 10);
		
        // calculate check number
        input = input.substr(2, 9);
        var result = 0;
        for (var i = 0, len = input.length; i < len; i++) {
            var number = parseInt(input.substr(i, 1), 10);
            if ((i % 2) === 0) {
                number = (number * 2);
            }
            if (number > 9) {
                result += (1 + (number % 10));
            } else {
                result += number;
            }
        }
        return (((check + result) % 10) === 0);
		
	}
	
	function validate_mobil(c) {
		var reg = /^[0]{1}[7]{1}[0-9]{8}$/;
		return reg.test(c)
	};
	
	function validate_password(input) {
		var reg = /^[^%\s]{8,}$/;
		var reg2 = /[A-Z]/;		//upper
		var reg3 = /[a-z]/;		//lower
		var reg4 = /[0-9]/;		//numeric
		return reg.test(input) && reg2.test(input) && reg3.test(input) && reg4.test(input);
	}
	
	function validateEmail(email) { 
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	} 

	function validate_priority(c) {
		var reg = /^[0]{1}[7]{1}[0-9]{8}$/;
		return reg.test(c)
	};

	function validate_uniques(array) {
		//console.log(array.toString());
		var valuesSoFar = [];
		for (var i = 0; i < array.length; ++i) {
			var value = array[i];
			if (valuesSoFar.indexOf(value) !== -1) {
				return false;
			}
			valuesSoFar.push(value);
		}
		return true;
	}	

	
	$(document).ready(function() {

		$('textarea').autosize(); 
	
		var plugin_kursbokning_id = getParameterByName('plugin_kursbokning_id');
		
		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});


		$('#btn_code_request').click(function(event) {
			event.preventDefault();
			var action = "code_request";
			var token = $("#token").val();
			var email_code_request = $("#email_code_request").val();
			var code_type = $("#code_type").val();
			
			if(email_code_request.length && validateEmail(email_code_request)) {
				$.ajax({
					
					beforeSend: function() { loading = $('#ajax_spinner_email').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_email').hide()",500)},
					type: 'POST',
					url: 'kursbokning_ajax.php',
					data: "action="+action+"&token="+token+"&email_code_request="+email_code_request+"&code_type="+code_type,
					success: function(message){
						$('#code_request_respons').empty().append('<span class="ui-icon ui-icon-circle-check" style="display:inline-block;vertical-align:text-bottom;"></span>&nbsp;&nbsp;&nbsp;'+message).attr('style', 'font-size:1em;color:#006400;').show();
						$("#email_code_use").val(email_code_request);
						$('#code').focus();
						$("#code_use_respons").empty().hide();
					},
					
				});
			}
			else {
				$('#code_request_respons').empty().append('* Ange post adress</span>').attr('style', 'background:yellow;border:1px solid #999999;').show();
			}
		});

		
		$('#btn_code_use').click(function(event) {
			event.preventDefault();
			var action = "code_use";
			var token = $("#token").val();
			var email_code_use = $("#email_code_use").val();
			var code = $("#code").val();
			
			if(email_code_use.length && code.length && validateEmail(email_code_use)) {
				$.ajax({
					beforeSend: function() { loading = $('#ajax_spinner_use_code').show()},
					complete: function(){ loading = setTimeout("$('#ajax_spinner_use_code').hide()",500)},
					type: 'POST',
					url: 'kursbokning_ajax.php',
					data: "action="+action+"&token="+token+"&email_code_use="+email_code_use+"&code="+code,
					dataType: "json",
					success: function(response){

						if(response.match == 'match') {
							$("#use_code").fadeOut('normal').hide();
							$("#form_kursbokning").fadeIn('normal').show();
							$('#btn_code_use').fadeOut('normal').hide();
							$("#email_code_use").attr('disabled','disabled').css('background','');
							$("#code").attr('disabled','disabled');
							$("#code_use_respons").empty().append('<span class="ui-icon ui-icon-circle-check" style="display:inline-block;vertical-align:text-bottom;"></span>&nbsp;&nbsp;&nbsp;Ok, din e-post är verifierad. Du kan nu fylla i formuläret.').attr('style', 'font-size:1em;color:#006400;').show();
							
							// upload function
							getready();
							
						} else if (response.match == 'match_used') {
							$("#code_use_respons").empty().append('* Det finns en bokning med motsvarande e-post och kod. Vänligen kontakta oss för mer information. Vill du göra en ny bokning kan du skicka efter en ny bokningskod.</div>').attr('style', 'border:1px solid black;background:yellow;').show();
						} else {
							$("#code_use_respons").empty().append('* Epostadress med kombinerad kod stämmer inte överens</span>').attr('style', 'border:1px solid #999999;background:yellow;').show();
						}
					},
				});
			}
			if(!validateEmail(email_code_use)) {
				$('#email_code_use').css('background','yellow');
				$('<p>Kontrollera epostadress</p>').insertAfter('#email_code_use');
			}
				

		});
		
		$( "#prioritera" ).on( "click", ".kurs_val", function() {
			
			var $this = $(this);
			$this.prev().before($this);
			$( "li" ).each(function( index ) {
				$( this ).find('.prioritet').text(index+1);
			});			
		});		
		
		$('.kurs_prio').change(function(event){

			var kurs_id = $(this).val();
			var kurs = $(this).attr('data-titel');
			
			// check list items
			var ids = [];
			$('#prioritera li').each(function() {
				var a = $(this).attr('id');
				ids.push(a);
			});	
			//console.log(ids.toString());
			
			// find list item
			if(ids.indexOf(kurs_id) != -1) {
				if ($(this).not(':checked')) {
					$('#'+kurs_id).remove();
					$('#kursval').empty().hide();
				}
			} else {
				// add to list
				// check limit 5
				if(ids.length < 5) {
					$('#prioritera').append('<li class="kurs_val" id="'+kurs_id+'"><span class="prioritet">&nbsp;</span><span class="course_title" style="margin-left:10px;">'+kurs+'</span></li>');
					
					if(ids.length >= 0) {
						$('#prioritera').css('background','#FFF');
					}
					if(ids.length >= 1) {	
						$('#prioritera_tips').text('Du kan klicka på en kurs för att flytta upp den i listan').show();
					}
				
				} else {
					$('#kursval').empty().append('Du kan söka till max 5 kurser').attr('style', 'border:1px solid #999999;background:yellow;padding:5px;padding:5px;font-size:1.3em;').show();
					$(this).attr('checked', false)
				}
			}
			
			$( "li" ).each(function( index ) {
				//console.log( index + ": " + $( this ).text() );
				$( this ).find('.prioritet').text(index+1);
			});			
			
		});

		
		$('#btn_save').click(function(event){
			event.preventDefault();
		
			$("#dialog_reservation_send").dialog('open','option', 'position', 'center');
			$("#dialog_reservation_send").dialog({
				buttons : {
				"Skicka" : function() {
					$(this).dialog("close");
					var action = "form_post";
					var token = $("#token").val();
					var epost = $("#email_code_use").val();
					var kod = $("#code").val();
					var kurs_id = $("input:checkbox[name='kurs_id[]']").is(":checked");
					var kurs_vald_id = $("#kurs_vald_id").val();
					
					//console.log('kurs_id: '+kurs_id);
					//console.log('kurs_vald_id: '+kurs_vald_id);

					// check list items
					var kurs_ids = [];
					$('#prioritera li').each(function() {
						var a = $(this).attr('id');
						kurs_ids.push(a);
					});	
					
					var datastring = $("#form_kursbokning").serialize();

					// calculate file size
					var inputs = $('div.upload-success > input[type=checkbox]:checked').map(function(_, el) {
						//return $(el).val();
						return $(el).attr('data-size');
					}).get();			
					//console.log(inputs);
					
					var size = 0;
					for (var i = 0; i < inputs.length; i++) {
						size = +size + +inputs[i];				
					}			
					//console.log(size);
					//console.log(get_prexix_filesize(size));
					
					sizelimit = true;
					if(size > 10520000) {
						$('#sizelimit').text(get_prexix_filesize(size) +' - du kan max skicka med 10 MB. Välj vilka filer som ska skickas. Komplettera genom att maila resterande filer.').show();
						sizelimit = false;
					} else {
						$('#sizelimit').empty();
					}
										
					// validate course priority
					var kurs_prio = false;
					var prios = [];
					$('.prio option:selected').each(function() {
						var a = $(this).text();
						prios.push(a);
					});							
					if(validate_uniques(prios)) {
						kurs_prio = true;
					}

					if($("#form_kursbokning").valid()) {
						
						if(kurs_id && sizelimit && kurs_prio) {
							$.ajax({
								beforeSend: function() { loading = $('#ajax_spinner_email').show()},								
								type: 'POST',
								url: 'kursbokning_ajax.php',
								data: $("#form_kursbokning").serialize() +"&action="+action+"&token="+token+"&epost="+epost+"&kod="+kod+"&kurs_id="+kurs_id+"&kurs_ids="+kurs_ids,
								success: function(newdata){
									// newdata in json format: db & msg
									var obj = JSON && JSON.parse(newdata) || $.parseJSON(newdata);
									loading = setTimeout("$('#ajax_spinner_email').hide()",700);
									if(obj.db == 'true') {
										$('.courses_list').css('background','#FFF');
										$("#form_kursbokning").fadeOut('normal').hide();
										$('#form_confirm').html(obj.msg);
										$("#code_use_respons").empty().hide();
										$('#form_confirm').append('<h3><span class="ui-icon ui-icon-circle-check" style="display:inline-block;vertical-align:text-bottom;"></span>&nbsp;&nbsp;&nbsp;Din ansökan är inskickad</h3>');
									} else {
										$('#form_confirm').html(obj.msg);
									}
									
								},
							});
						} else {

							$('#kursval').empty().append('Välj kurs(er) som du söker till i listan ovan. Prioritera därefter dina kursval här nedanför.').attr('style', 'border:1px solid #999999;background:yellow;padding:5px;padding:5px;font-size:1.3em;').show();
							$('#prioritera').css('background','#FFFF99');
							$('.btn_choose').css('border', '3px solid yellow');							
							$('<div class="plz_check"><label class="error">Välj kurs som du vill boka i listan ovan.</label></div>').insertAfter('.courses_list').attr('style', 'border:1px solid #999999;background:yellow;padding:5px;padding:5px;font-size:1.3em;');
							
						}
						
					}
					
				},
				"Avbryt" : function() {
					$(this).dialog("close");
					}
				}
			});
		});

		// client side validation
    	$("#form_kursbokning").validate({
    		rules: {
				fnamn: "required",
				enamn: "required",
    			epost: {
					required: true,
     				email: true
     			},
				<?php if($mobil==2) { echo 'mobil: {required: true, jq_validate_mobil: true},';} ?>
				<?php if($telefon==2) { echo 'telefon: "required",';} ?>

    			postnummer: {
					<?php if($postnummer==2) { echo 'required: true,';} ?>
					digits: true,
					maxlength:5,
					minlength:5
     			},
				<?php if($adress==2) { echo 'adress: "required",';} ?>
				<?php if($ort==2) { echo 'ort: "required",';} ?>
				<?php if($lan==2) { echo 'lan: "required",';} ?>
				<?php if($kommun==2) { echo 'kommun: "required",';} ?>
				<?php if($country==2) { echo 'country: "required",';} ?>
				<?php if($organisation==2) { echo 'organisation: "required",';} ?>
				<?php if($fakturaadress==2) { echo 'fakturaadress: "required",';} ?>
				<?php if($personnummer==2) { echo 'personnummer: {required: true, jq_validate_personnummer: true},';} ?>
				<?php echo $validate_field_required; ?>
    			pul: "required",
				code: {
					required: true
     			}
    		},
    		messages: {
				fnamn: "Förnamn",
				enamn: "Efternamn",
				epost: "Epost",
				mobil: "Ange mobil (10 siffror)",
				telefon: "Ange telefon",
				adress: "Ange adress",
				postnummer: "Ange 5 siffror",
				ort: "Ange ort",
				lan: "Ange län",
				kommun: "Ange kommun",
				country: "Ange land",
				organisation: "Ange namn på organisation, företag, förening eller liknande",
				fakturaadress: "Ange fakturaadress",
				<?php echo $validate_field_message; ?>
				personnummer: "Kontrollera personnummer",
				pul: "Du måste godkänna för att formuläret ska skickas"
    		}
    	});
		
		
		jQuery.validator.addMethod("jq_validate_personnummer", function(value, element) { 
				return this.optional(element) || personnummer(value); 
			}, 
			"Kontrollera personnummer"
		);
		jQuery.validator.addMethod("jq_validate_mobil", function(value, element) { 
				return this.optional(element) || validate_mobil(value); 
			}, 
			"Kontrollera mobil"
		);
		
		$('.course_title').click(function(){
			$(this).closest('tr').next('tr').fadeToggle();
		});

		$('.btn_choose').click(function(){
			$(this).closest('table').find('tr').fadeOut();
			$(this).closest('tr').fadeIn();
			var id = this.id;
			$(this).replaceWith('<input type="checkbox" id="kurs_id[]" name="kurs_id[]" value="'+id+'" checked="checked" disabled="disabled"><input type="hidden" id="kurs_vald_id" name="kurs_vald_id" value="'+id+'">');
			$('.plz_check').empty().hide();
		});
		
		$("#dialog_reservation_send").dialog({
			autoOpen: false,
			modal: true,
			minWidth: 400,
			minHeight: 300
		});		
		
		$( document ).tooltip();
		
		function getready() {
			var token = $("#token").val();
			//var email_code_request = $("#email_code_request").val();
			var epost = $("#email_code_use").val();
			//console.log('epost:'+epost);
			var running = 0;
						
			var uploader = new qq.FileUploader({
				multiple: true,
				element: document.getElementById('file-uploader'),
				action: 'files_upload.php',
				//allowedExtensions: ['pdf', 'doc', 'jpg'],
				allowedExtensions: [<?php echo $str_extensions; ?>],
				template: '<div class="qq-uploader">' + 
						'<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
						'<div class="qq-upload-button">Ladda upp filer (max 10 MB)</div>' +
						'<ul class="qq-upload-list"></ul>' + 
					 '</div>',
				//debug: true,
				params: {token: token, epost: epost},
				sizeLimit: 10520000,

				onSubmit: function(id, fileName){
					running++;
				},
				onComplete: function(id, fileName, responseJSON){
					running--;
					//console.log(responseJSON);
					var filename = responseJSON['filename'];
					var filename_server = responseJSON['filename2'];
					var filename_original = responseJSON['filename_original'];
					var filesize = responseJSON['filesize'];
					var filesize_true = responseJSON['filesize_true'];
					if(responseJSON['success']) {

						/*
						// check existing files
						var input_files = $('div.upload-success > input[type=checkbox]').map(function(_, el) {
							return $(el).val();
						}).get();

						if(input_files.indexOf(filename_server) > -1) {
							console.log('exists:');
						} else {
							//$('#filesUploaded2').append('<div class="upload-success clearfix"><span class="ui-icons-upload-attachment" style="display:inline-block;vertical-align:text-bottom;float:left;"></span><span class="attachment">'+filename_original+'  |  '+filesize+'</span><span class="ui-icon ui-icon-circle-check" style="display:inline-block;vertical-align:text-bottom;float:left;"></span><input name="attachments[]" id="attachments[]" value="'+filename_server+'" data-size="'+filesize_true+'" type="checkbox" checked="checked" style="margin-left:20px;"/></div>');
						}
						*/
						$('#filesUploaded2').append('<div class="upload-success clearfix"><span class="ui-icons-upload-attachment" style="display:inline-block;vertical-align:text-bottom;float:left;"></span><span class="attachment">'+filename_original+'  |  '+filesize+'</span><span class="ui-icon ui-icon-circle-check" style="display:inline-block;vertical-align:text-bottom;float:left;"></span><input name="attachments[]" id="attachments[]" value="'+filename_server+'" data-size="'+filesize_true+'" type="checkbox" checked="checked" style="margin-left:20px;"/></div>');
					}
					if(running==0){
						$('.qq-upload-list').empty();

					}
				},
			});
		}
		
	});
		
</script>



<div id="dialog_reservation_send" title="Spara och skicka">
  <?php
  echo $post_confirm;
  ?>
  <p>
  Formuläret kommer att kontrolleras. Var vänlig komplettera obligatoriska fält om formuläret inte skickas.
  </p>
</div>

<div class="row" style="padding-bottom:10px;">
</div>

<div id="ajax_spinner_email" class="overlayed">
	<div class="wait">Var vänlig vänta på respons...</div>
	<img src="css/images/spinner_big.gif" class="wait_respons" alt="spinner" />
</div>

<?php include_once '../../../cms/includes/inc.footer_cms.php'; ?>
<?php include_once '../../../cms/includes/inc.debug.php'; ?>

</body>
</html>