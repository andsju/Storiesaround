<?php 
require_once 'includes/inc.core.php';
if(!isset($_SESSION['site_id'])) {die;}

$id = (isset($_GET['id']) === true && (int)$_GET['id']) ? $_GET['id'] : 0;
$search = '';
// initiate class
//--------------------------------------------------
$pages = new Pages();
$rows = $pages->getPagesStoriesPreviewRSS($id);
//print_r2($rows);

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
	$link = CMS_URL .'/cms/pages.php?id='.$row['id'];
	$img = isset($row['filename']) ? str_replace('_100.','_222.',$row['filename']) : '';
	?>
	<item>
		<title><?php echo $row['title'];?></title>
		<link><?php echo $link;?></link>
		<pubDate><?php echo date('r',strtotime($row['pubdate']));?></pubDate>
		<guid isPermaLink="false">#<?php echo $row['id'];?></guid>
		<?php if (isset($row['filename'])) { ?>
		<description><![CDATA[<img src="<?php echo CMS_URL .'/content/uploads/pages/'.$row['id'].'/'.$img; ?>" /><p><?php echo $row['description'];?></p>]]></description>
		<?php } else { ?>
		<description><?php echo $row['description'];?></description>
		<?php } ?>
	</item>
	<?php 
	}
	?>
</channel>
</rss>
<?php
}
?>