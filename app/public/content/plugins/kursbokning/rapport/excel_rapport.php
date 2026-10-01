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
$objPHPExcel->getActiveSheet()->setCellValue('A1', "Förnamn");
$objPHPExcel->getActiveSheet()->setCellValue('B1', "Efternamn");
$objPHPExcel->getActiveSheet()->setCellValue('C1', "Personnummer");
$objPHPExcel->getActiveSheet()->setCellValue('D1', "Personnummer YYYYMMDD-XXXX");
$objPHPExcel->getActiveSheet()->setCellValue('E1', "Epost");
$objPHPExcel->getActiveSheet()->setCellValue('F1', "Adress");
$objPHPExcel->getActiveSheet()->setCellValue('G1', "Postnummer");
$objPHPExcel->getActiveSheet()->setCellValue('H1', "Ort");
$objPHPExcel->getActiveSheet()->setCellValue('I1', "Mobil");
$objPHPExcel->getActiveSheet()->setCellValue('J1', "Telefon");
$objPHPExcel->getActiveSheet()->setCellValue('K1', "Organisation");
$objPHPExcel->getActiveSheet()->setCellValue('L1', "Kommun");
$objPHPExcel->getActiveSheet()->setCellValue('M1', "Län");
$objPHPExcel->getActiveSheet()->setCellValue('N1', "Land");
$objPHPExcel->getActiveSheet()->setCellValue('O1', "Fakturaadress");
$objPHPExcel->getActiveSheet()->setCellValue('P1', "Frågor");
$objPHPExcel->getActiveSheet()->setCellValue('Q1', "Noteringar");
$objPHPExcel->getActiveSheet()->setCellValue('R1', "Log");
$objPHPExcel->getActiveSheet()->setCellValue('S1', "Sökt kurs");
$objPHPExcel->getActiveSheet()->setCellValue('T1', "Datum anmälan");
$objPHPExcel->getActiveSheet()->setCellValue('U1', "Datum antagen");

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
	foreach($ids as $id) {
		$r = $kursbokning->getKursbokningKursId($id);		
		$s .= $r['title'] .' | ';
	}

	$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $row['fnamn'])
	                              ->setCellValue('B' . $i, $row['enamn'])
								  ->setCellValue('C' . $i, $row['personnummer'])
								  ->setCellValue('D' . $i, $row['personnummer_yyyy'])
								  ->setCellValue('E' . $i, $row['epost'])
								  ->setCellValue('F' . $i, $row['adress'])
								  ->setCellValue('G' . $i, $row['postnummer'])
								  ->setCellValue('H' . $i, $row['ort'])
								  ->setCellValue('I' . $i, $row['mobil'])
								  ->setCellValue('J' . $i, $row['telefon'])
								  ->setCellValue('K' . $i, $row['organisation'])								  
								  ->setCellValue('L' . $i, $row['kommun'])
								  ->setCellValue('M' . $i, $row['lan'])
								  ->setCellValue('N' . $i, $row['country'])
								  ->setCellValue('O' . $i, $row['fakturaadress'])
								  ->setCellValue('P' . $i, $row['questions'])
								  ->setCellValue('Q' . $i, $row['notes'])
								  ->setCellValue('R' . $i, $row['log'])
								  ->setCellValue('S' . $i, $s)
								  ->setCellValue('T' . $i, $row['utc_created'])
								  ->setCellValue('U' . $i, $row['utc_confirmed']);
	
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

</body>
</html>