<?php if (!defined('VALID_INCL')) {die();} ?>

<div id="site-header" class="cycle-slideshow" data-cycle-timeout="<?php echo $arr['header_image_timeout']; ?>" data-cycle-log="false" data-cycle-caption-template="{{alt}}" data-cycle-caption="#site-header-alt-caption">
<?php
$header_image = json_decode($arr['header_image']);
$header_caption = json_decode($arr['header_caption']);

$header_image = array("site_header_vandring.jpg","site_header_winter.jpg");

if (count($header_image)) {

	for ($i = 0; $i < count($header_image); $i++) {
		$class = $i == 0 ? "first" : "";
		$caption = $arr['header_caption_show'] == 1 ? $header_caption[$i] : "";
		echo '<img src="'. CMS_DIR .'/content/uploads/header/'. $header_image[$i] .'" data-info="'.$info.'" alt="'. $caption .'" data-cl="'. $class .'">';
	}

} else {

?>
	<img src="<?php echo CMS_DIR; ?>/content/uploads/header/<?php echo $_SESSION['site_header_image']; ?>" alt="">
<?php
}
?>
</div>
<div id="site-header-alt-caption"></div>