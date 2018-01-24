<?php if (!defined('VALID_INCL')) {die();} ?>

<div id="site-navigation-identity">
	<a id="site-name-link" href="<?php echo $_SESSION['site_domain_url']; ?>">
		<h1 id="site-name"><?php echo $_SESSION['site_name']; ?></h1>
	</a>
	<?php if(isset($_SESSION['site_slogan'])) { ?>
	<div id="site-slogan-heading"><?php echo $_SESSION['site_slogan']; ?></div>
	<?php } ?>
</div>

<div id="site-navigation-cms">
	<?php echo date("Y-m-d"); ?> | 
	<?php if(isset($_SESSION['site_domain_url'])) { ?>
		<a href="<?php echo $_SESSION['site_domain_url']; ?>" class="std"><?php echo translate("Start", "site_start_page", $languages); ?></a> | 
	<?php } ?>
	<?php if (!isset($_SESSION['users_id'])) { ?>
		<a href="<?php echo CMS_DIR; ?>/cms/login.php"><?php echo translate("Login", "site_login", $languages); ?></a>
	<?php } ?>
</div>

<div id="site-search">
    <input type="text" name="search" placeholder="Vad söker du?" id="pages_s" class="search" value="" style="z-index:999">
    <button id="btn_pages_search" class="magnify"><?php echo translate("Search", "site_search", $languages); ?></button>
</div>



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
<div id="site-header-alt-caption"></div>;