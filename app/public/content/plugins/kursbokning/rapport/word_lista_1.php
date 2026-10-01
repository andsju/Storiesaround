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

<input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'];?>" />

<?php
$kursbokning = new Kursbokning();
$plugin_kursbokning_kurs_id = isset($_GET['plugin_kursbokning_kurs_id']) && is_numeric($_GET['plugin_kursbokning_kurs_id']) ? $_GET['plugin_kursbokning_kurs_id'] : null;
$result = $kursbokning->getKursbokningKursId($plugin_kursbokning_kurs_id);		
$title = $info = '';

if($result) {
	
	$named = $result['type']=='Uppdragsutbildning' ? 'bokningar' : 'ansökningar';
	// check reservations						
	$row = $kursbokning->getKursbokningKurserAnmalanCount($result['plugin_kursbokning_kurs_id']);								
	$i = $row ? $row['count'] : 0;

	$title = $result['title'];
	$info = 'Antal '.$named.' | platser: '.$i.' | '.$result['participants'];

} else {

	$title = "Ingen specifik kurs vald";
	$info = "";
}


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


/** Include PHPRtfLite */

require_once CMS_ABSPATH .'/content/plugins/kursbokning/rapport/classes/PHPRtfLite.php';


// registers PHPRtfLite autoloader (spl)
PHPRtfLite::registerAutoloader();


// rtf document instance
$rtf = new PHPRtfLite();

// margins
$rtf->setMargins(2.5, 2.5, 2.5, 2.5);

// font face
$font_face = 'Verdana';


// add section
$section = $rtf->addSection();


// add header
$header = $section->addHeader();
$header->writeText($_SESSION['site_name'].' - rapport kursbokning '.date('Y-m-d').' | '. $title, new PHPRtfLite_Font(7, $font_face));


// format
$paragraph_heading_1 = new PHPRtfLite_ParFormat();
$paragraph_heading_1->setSpaceBefore(7);
$paragraph_heading_1->setSpaceAfter(3);
$font_heading_1 = new PHPRtfLite_Font(14, $font_face);
$font_heading_1->setBold();

$paragraph_heading_2 = new PHPRtfLite_ParFormat();
$paragraph_heading_2->setSpaceBefore(10);
$paragraph_heading_2->setSpaceAfter(2);
$font_heading_2 = new PHPRtfLite_Font(10, $font_face);
$font_heading_2->setBold();

$paragraph_heading_3 = new PHPRtfLite_ParFormat();
$paragraph_heading_3->setSpaceBefore(3);
$paragraph_heading_3->setSpaceAfter(1);
$font_heading_3 = new PHPRtfLite_Font(10, $font_face);

$paragraph_heading_4 = new PHPRtfLite_ParFormat();
$paragraph_heading_4->setSpaceBefore(3);
$paragraph_heading_4->setSpaceAfter(1);
$font_heading_4 = new PHPRtfLite_Font(8, $font_face);

$paragraph_body_text = new PHPRtfLite_ParFormat();
$paragraph_body_text->setSpaceBefore(0);
$paragraph_body_text->setSpaceAfter(0);

$paragraph_body_text_start = new PHPRtfLite_ParFormat();
$paragraph_body_text_start->setSpaceBefore(20);
$paragraph_body_text_start->setSpaceAfter(0);

$paragraph_body_text_end = new PHPRtfLite_ParFormat();
$paragraph_body_text_end->setSpaceBefore(0);
$paragraph_body_text_end->setSpaceAfter(5);


$font_body_text = new PHPRtfLite_Font(10, $font_face);

$font_table_head = new PHPRtfLite_Font(10, $font_face);
$font_table_head->setBold();



$section->writeText($title, $font_heading_1, $paragraph_heading_1);
$section->setNoBreak();
$section->writeText($info, $font_heading_3, $paragraph_heading_3);
$section->setNoBreak();


// some bootstraping here
$paragraph_end = new PHPRtfLite_ParFormat();
// spaces in lines after the paragraph
$paragraph_end->setSpaceAfter(16);


$font_style = new PHPRtfLite_Font(11, $font_face);
$font_style_small = new PHPRtfLite_Font(9, $font_face);



$table = $section->addTable();
// add 2 rows with a height of 1cm for each of them
$table->addRows(count($rows)+1, 0.5);
// add 3 columns (first: 1cm, second: 2cm, third: 3cm)
$table->addColumnsList(array(5,5,5));


$table->writeToCell(1, 1, 'Namn', $font_table_head);
$table->writeToCell(1, 2, 'Adress', $font_table_head);
$table->writeToCell(1, 3, 'Postadress', $font_table_head);


$str = '';

$i = 2;
foreach($rows as $row) {
	
	$table->writeToCell($i, 1, $row['fnamn'].' '.$row['enamn'], new PHPRtfLite_Font(10, $font_face));
	$table->writeToCell($i, 2, $row['adress'], new PHPRtfLite_Font(10, $font_face));
	$table->writeToCell($i, 3, $row['postnummer'] .' '.$row['ort'], new PHPRtfLite_Font(10, $font_face));
	$i++;
}


$section->writeText(date('Y-m-d'), $font_heading_4, $paragraph_heading_4);


/*
$imageFile = CMS_ABSPATH .'/cms/css/images/storiesaround_logotype_black.png';
$image = $section->addImage($imageFile);
$image->setWidth(3);
*/


// save
$path = CMS_ABSPATH .'/content/plugins/kursbokning/rapport/tmp/';
$file = str_replace('.php', '.rtf', pathinfo(__FILE__, PATHINFO_BASENAME));
$path_file = $path . $file;
$rtf->save($path_file);


// output
echo '<h4>Filen är skapad</h4>';
echo '<div style="padding:20px 0;">';
echo '<span class="toolbar"><button id="download">Visa fil</button></span>'; 
echo '<div id="download_link" style="display:none;">&raquo;&raquo;&raquo; <a href="tmp/'.str_replace('.php', '.rtf', pathinfo(__FILE__, PATHINFO_BASENAME)).'">'.str_replace('.php', '.rtf', pathinfo(__FILE__, PATHINFO_BASENAME)).'</a></div>';
echo '</div>';

include_once '../../../../cms/includes/inc.footer_cms.php';

// load javascript files
foreach ( $js_files as $js ) {
	echo "\n".'<script src="'.$js.'"></script>';
}

?>

</body>
</html>