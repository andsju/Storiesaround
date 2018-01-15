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

<div id="site-header" class="cycle-slideshow" data-cycle-timeout="7000">
<?php
$header_image = json_decode($arr['header_image']);
if (count($header_image)) {
	foreach($header_image as $image) {
?>
    <img src="<?php echo CMS_DIR; ?>/content/uploads/header/<?php echo $image;?> ">
<?php
	}
}
?>
</div>