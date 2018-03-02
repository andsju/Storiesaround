<!DOCTYPE html>
<?php if (!defined('VALID_INCL')) {
    die();
} ?>
<html lang="<?php echo $lang; ?>">

<head>

    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow"/>
    <title><?php echo $page_title; ?></title>

    <?php
    //load css files
    foreach ($css_files as $css):
        echo '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
    endforeach;
    ?>
    
    <script src="<?php echo CMS_DIR; ?>/cms/libraries/jquery/jquery.min.js"></script>
    <script src="https://www.google.com/jsapi"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
    <link rel="icon" type="image/png" href="<?php echo CMS_DIR; ?>/content/favicon.png" />

</head>

<body style="<?php echo $body_style; ?>" class="cms-edit">