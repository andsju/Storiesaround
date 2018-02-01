<?php if (!defined('VALID_INCL')) {die();} ?>

<div id="site-navigation-identity">
	<a id="site-name-link" href="<?php echo $_SESSION['site_domain_url']; ?>">
		<h1 id="site-name"><?php echo $_SESSION['site_name']; ?></h1>
		<img src="../content/themes/mountain/images/GF_logotype_1rad.png">
	</a>
	<?php if(isset($_SESSION['site_slogan'])) { ?>
	<div id="site-slogan-heading"><?php echo $_SESSION['site_slogan']; ?></div>
	<?php } ?>
</div>

<div id="site-navigation-cms">
	<?php echo date("Y-m-d"); ?> 
	<?php if (!isset($_SESSION['users_id'])) { ?>
		<a href="<?php echo CMS_DIR; ?>/cms/login.php"><i class="fa fa-sign-in" aria-hidden="true"></i> <span class="cms-link"><?php echo translate("Login", "site_login", $languages); ?></span></a>
	<?php } ?>
	<?php if(isset($_SESSION['site_domain_url'])) { ?>
		<a href="<?php echo $_SESSION['site_domain_url']; ?>"><i class="fa fa-home" aria-hidden="true"></i> <span class="cms-link"><?php echo translate("Start", "site_start_page", $languages); ?></span></a>
	<?php } ?>
</div>

<?php
	if ($arr['search_field_area'] == 1) {
		print_search_field_area_header($languages);
	}
	
?>



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