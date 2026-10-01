<?php
// choose what arguments to pass in order to load plugin

$kursbokning = new Kursbokning();

$courses_table = '';
$rows_forms = $kursbokning->getKursbokning();

//echo '<h3 class="heading">Ansöka till kurs | boka kurs</h3>';
if($rows_forms) {
	$courses_table = '<table id="plugins_args" style="background:#FFF;border-spacing: 10px; border-collapse: separate;">';
			$courses_table .= '<tr>';
				$courses_table .= '<th style="text-align:left;">Titel</th>';
				$courses_table .= '<th style="text-align:left;">Typ</th>';
				$courses_table .= '<th style="text-align:right;">Plugin arguments - copy and paste in text field above</th>';
				
			$courses_table .= '</tr>';		
	
		foreach($rows_forms as $rows_form) {
			$courses_table .= '<tr>';
				$courses_table .= '<td>'.$rows_form['title'].'</td>';
				
				$courses_table .= '<td>'.mb_strtolower($rows_form['type'],'UTF-8').'</td>';
				$courses_table .= '<td><span class="code" style="background:#ffff99;border:1px solid #000;padding:5px;">plugin_kursbokning_id='.$rows_form['plugin_kursbokning_id'].'</span></td>';
			$courses_table .= '</tr>';			
		}
	$courses_table .= '</table>';
}
echo $courses_table;		
?>
