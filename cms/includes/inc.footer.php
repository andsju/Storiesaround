<?php
// include file
if (!defined('VALID_INCL')) {
    die;
}

echo '<div style="float:left; ">';
if (isset($_SESSION['site_name'])) {
    echo $_SESSION['site_name'] . ' | ';
}
if (isset($_SESSION['site_domain'])) {
    echo $_SESSION['site_domain'] . ' | ';
}
if (isset($_SESSION['site_email'])) {
    echo $_SESSION['site_email'] . ' | ';
}
echo '</div>';

if (isset($_SESSION['site_copyright'])) {
    echo '<div style="float:right;">' . CMS . ' | &#169; ' . $_SESSION['site_copyright'] . '</div>';
}
?>
