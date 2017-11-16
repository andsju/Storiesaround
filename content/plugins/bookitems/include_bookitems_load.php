<?php
// include core
//--------------------------------------------------

require_once '../../../cms/includes/inc.core.php';

$date = (isset($_GET['date'])) ? $_GET['date'] : null;
$date = (isValidDate($date)) ? $date : null;
$period = (isset($_GET['period'])) ? $_GET['period'] : null;
$plugin_bookitems_category_id = (isset($_GET['plugin_bookitems_category_id'])) ? $_GET['plugin_bookitems_category_id'] : null;
$bookitems = new Bookitems();
echo $bookitems->getBookitemsVertical($date, $href=null, $max_width=true, $plugin_bookitems_category_id, $period);
?>