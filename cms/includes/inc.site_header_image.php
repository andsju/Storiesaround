<?php if (!defined('VALID_INCL')) {die();} ?>
<div id="site-header-image" class="slideshow-cycle-wrapper parallax">
<?php
$header_image = json_decode($arr['header_image']);
$header_caption = json_decode($arr['header_caption']);
$header_caption_align = json_decode($arr['header_caption_align']);
if (count($header_image)) {

	for ($i = 0; $i < count($header_image); $i++) {
		$parts = explode(".", $header_image[$i]);
		$caption = $arr['header_caption_show'] == 1 ? $header_caption[$i] : "";
		$caption_align = $header_caption_align[$i];
		if ($parts[1] == "mp4") {
			// black loading: poster="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII="
			echo '<video class="slideshow-cycle-image" data-caption="'. $caption .'" data-caption-align="'. $caption_align .'" autoplay loop muted poster="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII="><source src="'. CMS_DIR .'/content/uploads/header/'. $header_image[$i] .'"></video>';
		} else {
			echo '<img src="'. CMS_DIR .'/content/uploads/header/'. $header_image[$i] .'" alt="'. $caption .'" data-caption="'. $caption .'" data-caption-align="'. $caption_align .'" class="slideshow-cycle-image">';	
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
<div id="site-header-caption"></div>