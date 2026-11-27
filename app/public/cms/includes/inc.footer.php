<?php if (!defined('VALID_INCL')) { die; } ?>

<div>
<?php
if (isset($_SESSION['site_name']) && strlen($_SESSION['site_name']) > 0) {
    echo $_SESSION['site_name'] . ' | ';
}
if (isset($_SESSION['site_domain']) && strlen($_SESSION['site_domain']) > 0) {
    echo $_SESSION['site_domain'] . ' | ';
}
if (isset($_SESSION['site_email']) && strlen($_SESSION['site_email']) > 0) {
    echo $_SESSION['site_email'] . ' | ';
}
?>
</div>
<?php
if (isset($_SESSION['site_copyright'])) {
    echo '<div><span id="storiesaround">' . CMS . ' | &#169; ' . $_SESSION['site_copyright'] . '</span></div>';
}
?>
