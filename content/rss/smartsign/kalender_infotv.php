<?php 
require_once '../../../cms/includes/inc.core.php';
if(!isset($_SESSION['site_id'])) {die;}

$limit = (isset($_GET['limit']) === true && intval($_GET['limit']) <= 30 && intval($_GET['limit']) > 0) ? intval($_GET['limit']) : 2;


$date = date('H:m:s');
//echo $date;


// initiate class
//--------------------------------------------------
$cal = new Calendar();
$rows = $cal->getCalendarEventsRSS(8, $limit);


function lux($text) {
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

if($rows) {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">

		<title><?php echo $_SESSION['site_name']; ?> - matsedel</title>
		<link rel="stylesheet" type="text/css" media="screen,projection" href="css/style.css" />

	</head>
	<body style="background:#000;color:#FFF;">
	<div class="date"><?php echo date('Y-m-d'); ?></div>
	<!-- container -->
	<div style="width:100%;height:800px;">
	

	<!-- box -->
	<div style="width:60%;" class="box vertical">
	
	<?php

	// variable $i to count shown events
	$i = 0;
	foreach($rows as $row) {

		// array $rows contains mulitple events		
		// skip event when this condition match
		if($row['event_date'] == date('Y-m-d')) {
			if (date('h:i:s') > '17:00:00') {
				continue;
			}
		}
		// show only one event
		if($i >= 1) { 
			continue;
		}
	
		$link = (isset($row['event_link']) && filter_var($row['event_link'], FILTER_VALIDATE_URL)) ? $row['event_link'] : CMS_URL;
		
		echo '<h1 style="" class="serif">'.  lux(date('l',strtotime($row['event_date']))) .' '. strtolower(date('j',strtotime($row['event_date']))) .' '. strtolower(lux(date('F',strtotime($row['event_date'])))) .' '. $row['title'] . '</h1>';
		
		echo '<div style="" class="serif">'. nl2br($row['description']) .'</div>';
		
		// increase count
		$i++;
	}
	?>
	

	</div>


	<!-- box -->
	<div style="width:40%;" class="box vertical">
	
		<img src="img/ordmoln_glimakra.png" class="right-aligned" />
	
	</div>


	<!-- container -->
	</div>

	<img src="img/gfhsk_logotype_white.png" class="logotype" />
	
	</body>
</html>
<?php
} else {

header('Location: bildspel.php');
exit;
}
?>	