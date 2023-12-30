<?php
if (!defined('VALID_INCL')) {
    die;
}

echo '<div style="clear:both;padding-top:10px;">';

echo '<div style="float:left;height:2.2em;vertical-align:bottom;line-height:3.5em; ">';
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

echo '<div style="float:right;">' . CMS . ' ' . $cms_version . '<span class="cms-icon-small"></span></div>';

echo '</div>';

include_once CMS_ABSPATH . '/cms/includes/inc.debug.php';
?>
    <input type="hidden" name="cms_dir" id="cms_dir" value="<?php echo CMS_DIR; ?>"/>

<?php echo '<!-- Last modified: ' . date('Y-m-d H:i:s', getlastmod()) . ' ' . $_SERVER["SCRIPT_NAME"] . ' -->'; ?>

