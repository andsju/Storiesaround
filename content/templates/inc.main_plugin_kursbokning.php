<?php
//print_r2($_SESSION);
//print_r2($arr);
?>

<body>
    <h1>Aloha</h1>

    <?php
    $js_files = array_unique($js_files);
    foreach ( $js_files as $js ) { 
        echo "\n".'<script src="'.$js.'"></script>';
    }

    include_once 'includes/inc.debug.php';
    ?>
</body>

</html>