<?php if (!defined('VALID_INCL')) {die();} ?>

<div id="site-header" class="cycle-slideshow" data-cycle-timeout="<?php echo $arr['header_image_timeout']; ?>" data-cycle-log="false" data-cycle-caption-template="{{alt}}" data-cycle-caption="#site-header-alt-caption">
<?php
$header_image = json_decode($arr['header_image']);
$header_caption = json_decode($arr['header_caption']);
$n = 0;
if (count($header_image)) {
	foreach($header_image as $image) {
		$caption = $arr['header_caption_show'] == 1 ? $header_caption[$n] : "";
?>
    <img src="<?php echo CMS_DIR; ?>/content/uploads/header/<?php echo $image; ?>" alt="<?php echo $caption; ?>">
<?php
	$n++;
	}
} else {
?>
	<img src="<?php echo CMS_DIR; ?>/content/uploads/header/<?php echo $_SESSION['site_header_image']; ?>" alt="">
<?php
}
?>
</div>
<div id="site-header-alt-caption"></div>