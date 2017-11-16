<!DOCTYPE html>
<?php if (!defined('VALID_INCL')) {
    die();
} ?>
<html lang="<?php echo $lang; ?>">

<head>

    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>

    <?php
    //load css files
    foreach ($css_files as $css):
        echo "\n" . '<link rel="stylesheet" type="text/css" href="' . $css . '" />';
    endforeach;

    echo "\n";
    echo "\n\t" . '<script src="' . CMS_DIR . '/cms/libraries/jquery/jquery.min.js"></script>';
    echo "\n\t" . '<script src="https://www.google.com/jsapi"></script>';
    echo "\n";
    ?>

</head>

<body style="<?php echo $body_style; ?>">