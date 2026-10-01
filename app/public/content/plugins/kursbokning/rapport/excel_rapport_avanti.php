<?php
// include core 
//--------------------------------------------------
require_once '../../../../cms/includes/inc.core.php';

// include session access 
//--------------------------------------------------
require_once '../../../../cms/includes/inc.session_access.php';

// access right, minimum, hierarchy matters
//--------------------------------------------------
if(!get_role_CMS('administrator') == 1) {
	if(!get_role_LMS('administrator') == 1) {
		header('Location: '. $_SESSION['site_domain_url']);	exit;
	}
}

// css files, loaded in inc.header.php 
//--------------------------------------------------
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-datatables/style.css',
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/content/plugins/kursbokning/css/style.css' );

//--------------------------------------------------
$js_files = array(
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.custom.min.js', 
	CMS_DIR.'/cms/libraries/js/functions.js', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/jquery.colorbox-min.js' );
?>
<!DOCTYPE html>
<html lang="sv">

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<?php 
	//load css files
	foreach ( $css_files as $css ) {
		echo "\n".'<link rel="stylesheet" type="text/css" media="screen,projection" href="'.$css.'" />';
	} 
	echo "\n";
	echo "\n\t".'<script src="'.CMS_DIR.'/cms/libraries/jquery/jquery.min.js"></script>';
	echo "\n\t".'<script src="https://www.google.com/jsapi"></script>';
	echo "\n\t".'<script src="'.CMS_DIR.'/content/plugins/kursbokning/rapport/inc.rapport.js"></script>';
	echo "\n";
	?>


</head>

<body style="width:400px;">

<script>
	$(document).ready(function() {

		$( ".toolbar button" ).button({
			icons: {
			},
			text: true
		});

		$('#download').click(function(event){
			event.preventDefault();
			$('#download_link').show();
			setTimeout("$('#download_link').hide()", 20000);
			setTimeout("$('body').empty()", 23000);
		});
		
	});
</script>

<?php

$kursbokning = new Kursbokning();

// check if one unique booking
$plugin_kursbokning_kurs_anmalan_id = isset($_GET['plugin_kursbokning_kurs_anmalan_id']) ? $_GET['plugin_kursbokning_kurs_anmalan_id'] : null;

if(isset($plugin_kursbokning_kurs_anmalan_id)) {

	$rows = $kursbokning->getKursbokningKurserAnmalanIdAll($plugin_kursbokning_kurs_anmalan_id);		

} else {

	$plugin_kursbokning_kurs_id = isset($_GET['plugin_kursbokning_kurs_id']) && is_numeric($_GET['plugin_kursbokning_kurs_id']) ? $_GET['plugin_kursbokning_kurs_id'] : null;
	$result = $kursbokning->getKursbokningKursId($plugin_kursbokning_kurs_id);	

	$utc_date_1 = isset($_GET['d1']) ? filter_var(trim($_GET['d1']), FILTER_SANITIZE_STRING) : date('Y-m-d',strtotime("-1 months", time()));
	$utc_date_2 = isset($_GET['d2']) ? filter_var(trim($_GET['d2']), FILTER_SANITIZE_STRING) : date('Y-m-d',strtotime("+1 day", time()));
	$canceled = isset($_GET['ca']) ? filter_input(INPUT_GET, 'ca', FILTER_VALIDATE_INT) : 0;
	$choosen = isset($_GET['ch']) ? filter_var(trim($_GET['ch']), FILTER_SANITIZE_STRING)  : '';

	$rows = $kursbokning->getKursbokningKurserAnmalan($plugin_kursbokning_kurs_id, $utc_date_1, $utc_date_2, $choosen, $canceled);
	
}

$title =  "Sheet";


/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');


/** Include PHPExcel */
require_once CMS_ABSPATH .'/content/plugins/kursbokning/rapport/classes/PHPExcel.php';




// Create new PHPExcel object
//echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();


// Set document properties
$objPHPExcel->getProperties()->setCreator("PHPExcel")
							 ->setLastModifiedBy("PHPExcel")
							 ->setTitle("Excel")
							 ->setSubject("Excel")
							 ->setDescription("Document generated using PHP classes.")
							 ->setKeywords("")
							 ->setCategory("Temp file");

							 
// Create a first sheet
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "Efternamn");
$objPHPExcel->getActiveSheet()->setCellValue('B1', "Förnamn");
$objPHPExcel->getActiveSheet()->setCellValue('C1', "KursNr");
$objPHPExcel->getActiveSheet()->setCellValue('D1', "Klass");
$objPHPExcel->getActiveSheet()->setCellValue('E1', "Adress");
$objPHPExcel->getActiveSheet()->setCellValue('F1', "Postnr");
$objPHPExcel->getActiveSheet()->setCellValue('G1', "Ort");
$objPHPExcel->getActiveSheet()->setCellValue('H1', "Personnr");
$objPHPExcel->getActiveSheet()->setCellValue('I1', "Telefon");
$objPHPExcel->getActiveSheet()->setCellValue('J1', "Mobil");
$objPHPExcel->getActiveSheet()->setCellValue('K1', "Epost");
$objPHPExcel->getActiveSheet()->setCellValue('L1', "Anhöriguppgift1");
$objPHPExcel->getActiveSheet()->setCellValue('M1', "Anhöriguppgift2");
$objPHPExcel->getActiveSheet()->setCellValue('N1', "AnteckningarBas");
$objPHPExcel->getActiveSheet()->setCellValue('O1', "PMBas");
$objPHPExcel->getActiveSheet()->setCellValue('P1', "Ansökning");
$objPHPExcel->getActiveSheet()->setCellValue('Q1', "Antagning");
$objPHPExcel->getActiveSheet()->setCellValue('R1', "Bekräftelse");
$objPHPExcel->getActiveSheet()->setCellValue('S1', "Hemlän");
$objPHPExcel->getActiveSheet()->setCellValue('T1', "Utbildningsbakgrund");
$objPHPExcel->getActiveSheet()->setCellValue('U1', "AnteckningarKurs");
$objPHPExcel->getActiveSheet()->setCellValue('V1', "PMKurs");
$objPHPExcel->getActiveSheet()->setCellValue('W1', "Specialkost");
// Freeze panes
$objPHPExcel->getActiveSheet()->freezePane('A2');
// Rows to repeat at top
$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
// Add data
$i = 2;
foreach($rows as $row) {

	// reservations
	$s = '';
	$ids = explode(',',$row['plugin_kursbokning_kurser_id']);
	$int = 1;
	foreach($ids as $id) {
		$r = $kursbokning->getKursbokningKursId($id);		
		$s .= $int .': '.$r['title'] .' ';
		$int++;
	}

	// get län char instead of swedish name 
	include_once '../kursbokning_functions.php';
	
	$lan = $row['lan'];
	foreach($swelan_char_name as $key => $value) {
		if ($lan == $value) {
			$lan = $key;
			break;
		}
	}
	
	$enamn = strlen($row['enamn']) ? mb_substr($row['enamn'],0,25,"UTF-8") : '';
	$fnamn = strlen($row['fnamn']) ? mb_substr($row['fnamn'],0,20,"UTF-8") : '';
	$adress = strlen($row['adress']) ? mb_substr($row['adress'],0,30,"UTF-8") : '';
	$postnummer = strlen($row['postnummer']) ? substr($row['postnummer'],0,6) : '';
	$ort = strlen($row['ort']) ? mb_substr($row['ort'],0,20,"UTF-8") : '';
	$personnummer = strlen($row['personnummer_yyyy']) ? substr($row['personnummer_yyyy'],0,13) : '';
	$telefon = strlen($row['telefon']) ? substr($row['telefon'],0,25) : '';
	$mobil = strlen($row['mobil']) ? substr($row['mobil'],0,25) : '';
	$epost = strlen($row['epost']) ? mb_substr($row['epost'],0,50,"UTF-8") : '';	
	$utc_created = strlen($row['utc_created']) > 0 ? substr($row['utc_created'],0,10) : '';
	$utc_created = isValidDate($utc_created) ? $utc_created : ''; 
	$utc_admitted = strlen($row['utc_admitted']) > 0 ? substr($row['utc_admitted'],0,10) : '';
	$utc_admitted = isValidDate($utc_admitted) ? $utc_admitted : ''; 
	$utc_confirmed = strlen($row['utc_confirmed']) > 0 ? substr($row['utc_confirmed'],0,10) : '';
	$utc_confirmed = isValidDate($utc_confirmed) ? $utc_confirmed : ''; 
	
	
	$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $enamn)
	                              ->setCellValue('B' . $i, $fnamn)
								  ->setCellValue('C' . $i, "")
								  ->setCellValue('D' . $i, "")
								  ->setCellValue('E' . $i, $adress)
								  ->setCellValue('F' . $i, $postnummer)
								  ->setCellValue('G' . $i, $ort)
								  ->setCellValue('H' . $i, $personnummer)
								  ->setCellValue('I' . $i, $telefon)
								  ->setCellValue('J' . $i, $mobil)
								  ->setCellValue('K' . $i, $epost)
								  ->setCellValue('L' . $i, "")
								  ->setCellValue('M' . $i, "")
								  ->setCellValue('N' . $i, "")
								  ->setCellValue('O' . $i, str_replace(array("\r","\n","\r\n"), " __ ", $row['questions']))
								  ->setCellValue('P' . $i, $utc_created)
								  ->setCellValue('Q' . $i, $utc_admitted)
								  ->setCellValue('R' . $i, $utc_confirmed)
								  ->setCellValue('S' . $i, $lan)
								  ->setCellValue('T' . $i, "")
								  ->setCellValue('U' . $i, "")
								  ->setCellValue('V' . $i, $s)
								  ->setCellValue('W' . $i, "");
	
	$i++;
}




// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Set sheet title
$objPHPExcel->getActiveSheet()->setTitle($title);

// Save Excel 2007 file
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

$path = CMS_ABSPATH .'/content/plugins/kursbokning/rapport/tmp/';
$file = str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME));
$path_file = $path . $file;
$objWriter->save($path_file);


// output
echo '<h4>Filen är skapad</h4>';
echo '<div style="padding:20px 0;">';
echo '<span class="toolbar"><button id="download">Visa fil</button></span>'; 
echo '<div id="download_link" style="display:none;">&raquo;&raquo;&raquo; <a href="tmp/'.str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)).'">'.str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)).'</a></div>';
echo '</div>';

include_once '../../../../cms/includes/inc.footer_cms.php';

// load javascript files
foreach ( $js_files as $js ) {
	echo "\n".'<script src="'.$js.'"></script>';
}

?>
<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />
</body>
</html>