<!doctype html>
<?php if (!defined('VALID_INCL')) { die(); } ?>
<html lang="<?php echo $language; ?>">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <?php echo $_SESSION['site_script']; ?>

    <?php   
    if (isset($meta_keywords)) {
        echo "\n\t" . '<meta name="keywords" content="' . $meta_keywords . '">';
    }
    if (isset($meta_description)) {
        echo "\n\t" . '<meta name="description" content="' . $meta_description . '">';
    }
    if (isset($meta_robots)) {
        echo "\n\t" . '<meta name="robots" content="' . $meta_robots . '">';
    }
    if (isset($meta_additional)) {
        echo "\n" . stripcslashes($meta_additional);
    }
    if (isset($_SESSION['site_meta_tags'])) {
        echo "\n" . $_SESSION['site_meta_tags'];
    }
    ?>

    <title><?php echo $page_title_head; ?></title>
    
    <script src="<?php echo CMS_DIR; ?>/cms/libraries/jquery/jquery.min.js"></script>    
    <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>

    <?php
    foreach ($css_files as $css) {
        if (isset($_SESSION['site_theme']) && $css == CMS_DIR . '/cms/themes/' . $_SESSION['site_theme'] . '/style.css') {
            echo "\n\t" . '<link rel="stylesheet" type="text/css" href="' . $css . '" id="themes-css">';
        } else {
            echo "\n\t" . '<link rel="stylesheet" type="text/css" href="' . $css . '">';
        }
    }
    ?>
    <link rel="icon" type="image/png" href="<?php echo CMS_DIR; ?>/content/favicon.png">
    <?php
    foreach ($og_properties as $key => $value) {
        echo "\n\t" . '<meta property="og:'. $key .'" content="'.$value.'">';
    }
    echo "\n\t";    
    include_once_customfile('includes/inc.head_elements.php', $arr, $languages);
    ?>

</head>