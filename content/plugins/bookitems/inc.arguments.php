<?php
// choose what arguments to pass in order to load plugin

$bookitems = new Bookitems();

$table = '';
$rows = $bookitems->getBookitemsCategory();

if($rows) {
	$table = '<table id="plugins_args" style="background:#FFF;border-spacing: 10px; border-collapse: separate;">';
			$table .= '<tr>';
				$table .= '<th style="text-align:left;">Titel</th>';
				$table .= '<th style="text-align:right;">Plugin arguments - copy and paste in text field above</th>';
				
			$table .= '</tr>';		
	
		foreach($rows as $row) {
			$table .= '<tr>';
				$table .= '<td>'.$row['title'].'</td>';
				$table .= '<td><span class="code" style="background:#ffff99;border:1px solid #000;padding:5px;">plugin_bookitems_category_id='.$row['plugin_bookitems_category_id'].'</span></td>';
			$table .= '</tr>';			
		}
	$table .= '</table>';
}
echo $table;		
?>
