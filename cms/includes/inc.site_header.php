<?php if (!defined('VALID_INCL')) {die();} ?>

<div id="site-navigation-identity">
	<a id="site-name-link" href="<?php echo $_SESSION['site_domain_url']; ?>">
		<img id="site-logotype" src="<?php echo CMS_DIR . '/content/uploads/logotype/'. $_SESSION['site_logotype']; ?>" alt="">
		<div id="site-name"><?php echo $_SESSION['site_name']; ?></div>
	</a>
	<?php if(isset($_SESSION['site_slogan'])) { ?>
	<div id="site-slogan-heading"><?php echo $_SESSION['site_slogan']; ?></div>
	<?php } ?>
</div>

<div id="site-navigation-cms">
	<a aria-label="<?php echo translate("Search", "search", $languages); ?>" href="<?php echo CMS_DIR; ?>/pages/sok"><i class="fas fa-search" aria-hidden></i></a>
	<?php if (!isset($_SESSION['users_id'])) { ?>
		<a aria-label="<?php echo translate("Login", "site_login", $languages); ?>" href="<?php echo CMS_DIR; ?>/cms/login.php"><i class="fas fa-sign-in-alt"></i></a>
	<?php } ?>
	<?php if(isset($_SESSION['site_domain_url'])) { ?>
		<a aria-label="<?php echo translate("Start", "site_start_page", $languages); ?>" href="<?php echo $_SESSION['site_domain_url']; ?>"><i class="fas fa-home"></i></a>
	<?php } ?>
</div>

<?php
	if ($arr['search_field_area'] == 1) {
		print_search_field_area_header($languages);
	}
?>
