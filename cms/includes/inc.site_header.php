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
	<a href="<?php echo CMS_DIR; ?>/pages/sok"><i class="fa fa-search" aria-hidden="true"></i></a>
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
