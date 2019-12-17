<?php if (!defined('VALID_INCL')) {die();} ?>
<div id="site-header" class="slideshow-cycle-wrapper parallax">
<?php
$header_image = json_decode($arr['header_image']);
$header_caption = json_decode($arr['header_caption']);
//print_r($header_image);
if (count($header_image)) {

	for ($i = 0; $i < count($header_image); $i++) {
		$parts = explode(".", $header_image[$i]);
		//print_r($parts);

		if ($parts[1] == "mp4") {
			echo '<video class="slideshow-cycle-image" autoplay loop muted><source src="'. CMS_DIR .'/content/uploads/header/'. $header_image[$i] .'"></video>';	
		} else {
			$class = $i == 0 ? "first" : "";
			$caption = $arr['header_caption_show'] == 1 ? $header_captionc : "";
			echo '<img src="'. CMS_DIR .'/content/uploads/header/'. $header_image[$i] .'" alt="'. $caption .'" data-cl="'. $class .'" class="slideshow-cycle-image">';	
		}

	}

} else {

?>
	<img src="<?php echo CMS_DIR; ?>/content/uploads/header/<?php echo $_SESSION['site_header_image']; ?>" alt="">
<?php
}
if ($arr['search_field_area'] == 2) {
	print_search_field_area_page($languages);
}
?>
</div>
<div id="site-header-alt-caption"></div>