<!doctype html>
<?php if (!defined('VALID_INCL')) {
    die();
} ?>
<html lang="<?php echo $language; ?>">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <?php
    //load css files
    foreach ($css_files as $css):
        echo "\n\t" . '<link rel="stylesheet" type="text/css" href="' . $css . '">';
    endforeach;
    ?>

    <script src="<?php echo CMS_DIR; ?>/cms/libraries/jquery/jquery.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
    <!--<script src="https://www.google.com/jsapi"></script>-->
    <link rel="icon" type="image/png" href="<?php echo CMS_DIR; ?>/content/favicon.png">

</head>

<body style="<?php echo $body_style; ?>" class="cms_edit">

