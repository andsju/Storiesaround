<?php 
require_once '../../../cms/includes/inc.core.php';
if(!isset($_SESSION['site_id'])) {die;}

$limit = (isset($_GET['limit']) === true && (int)$_GET['limit'] <= 30 && (int)$_GET['limit'] > 0) ? $_GET['limit'] : 10;

// initiate class
//--------------------------------------------------
$cal = new Calendar();
$rows1 = $cal->getCalendarEventsRSS(1, $limit);
$rows2 = $cal->getCalendarEventsRSS(2, $limit);
$rows = array_merge($rows1,$rows2);


function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

aasort($rows,"event_date");


if($rows) {
?>
<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>

<rss version="2.0">
<channel>
	<title><?php echo $_SESSION['site_name']; ?></title>
	<description><?php echo 'Stories around'; ?></description>
	<link><?php echo CMS_URL; ?></link>
	<?php 
	foreach($rows as $row) {
	$link = (isset($row['event_link']) && filter_var($row['event_link'], FILTER_VALIDATE_URL)) ? $row['event_link'] : CMS_URL;
	?>
	<item>
		<title><?php echo strtolower(date('j',strtotime($row['event_date']))) . '/'. strtolower(date('n',strtotime($row['event_date']))) .' '. $row['title'];?></title>
		<description><?php echo $row['description'];?></description>
		<link><?php echo $link;?></link>
		<pubDate>Publicerat: <?php echo date('r',strtotime($row['pubdate']));?></pubDate>
		<guid isPermaLink="false">#<?php echo $row['calendar_events_id'];?></guid>
	</item>
	<?php 
	}
	?>
</channel>
</rss>
<?php
}
?>