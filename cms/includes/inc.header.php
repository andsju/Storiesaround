<!doctype html>
<?php if (!defined('VALID_INCL')) { die(); } ?>
<html lang="<?php echo $language; ?>">

<head>
    <?php echo $_SESSION['site_script']; ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <?php
    if (isset($meta_keywords)) {
        echo "\n\t" . '<meta name="keywords" content="' . $meta_keywords . '" />';
    }
    if (isset($meta_description)) {
        echo "\n\t" . '<meta name="description" content="' . $meta_description . '" />';
    }
    if (isset($meta_robots)) {
        echo "\n\t" . '<meta name="robots" content="' . $meta_robots . '" />';
    }
    if (isset($meta_additional)) {
        echo "\n" . stripcslashes($meta_additional);
    }
    if (isset($_SESSION['site_meta_tags'])) {
        echo "\n" . $_SESSION['site_meta_tags'];
    }
    ?>

    <title><?php echo $page_title; ?></title>
    <?php

    foreach ($css_files as $css) {
        if (isset($_SESSION['site_theme']) && $css == CMS_DIR . '/cms/themes/' . $_SESSION['site_theme'] . '/style.css') {
            echo "\n\t" . '<link rel="stylesheet" type="text/css" href="' . $css . '" id="themes-css" />';
        } else {
            echo "\n\t" . '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
        }
    }
    echo "\n";
    echo "\n\t" . '<script src="' . CMS_DIR . '/cms/libraries/jquery/jquery.min.js"></script>';
    echo "\n\t" . '<script src="https://www.google.com/jsapi"></script>';
    echo "\n\t" . '<link rel="icon" type="image/png" href="' . CMS_DIR . '/content/favicon.png" />';
    echo "\n";

    // custom head tags
    include_once_customfile('includes/inc.head_elements.php', $arr, $languages);
    ?>

</head>