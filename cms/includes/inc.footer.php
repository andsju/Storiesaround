<?php if (!defined('VALID_INCL')) { die; } ?>

<div style="float:left; ">
<?php
if (isset($_SESSION['site_name'])) {
    echo $_SESSION['site_name'] . ' | ';
}
if (isset($_SESSION['site_domain'])) {
    echo $_SESSION['site_domain'] . ' | ';
}
if (isset($_SESSION['site_email'])) {
    echo $_SESSION['site_email'] . ' | ';
}
?>
</div>
<?php
if (isset($_SESSION['site_copyright'])) {
    echo '<div style="float:right;"><span id="storiesaround">' . CMS . ' | </span>&#169; ' . $_SESSION['site_copyright'] . '</div>';
}
?>
