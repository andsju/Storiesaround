<?php
$debug = false;
if (isset($_SESSION['users_id'])) {
    $debug = $_SESSION['debug'] == 1 ? true : false;
}

if (LIVE == false || $debug == true) {
    echo "\n" . '<div style="clear:both;padding-top:50px;">';
    echo "\n" . '<hr />----------  !LIVE || Administrators $debug  ----------';
    echo "\n" . '</div>';
    echo "\n" . '<p><i>footer debug</i></p>';
    echo 'PHP_SELF: ' . $_SERVER['PHP_SELF'] . "<br />";
    echo "\n" . 'SCRIPT_NAME: ' . $_SERVER['SCRIPT_NAME'] . "<br />";
    echo "\n" . 'REQUEST_URI: ' . $_SERVER['REQUEST_URI'] . "<br />";
    echo "\n" . 'QUERY_STRING: '. $_SERVER['QUERY_STRING'] . "<br />";
    if (isset($_GET)) {
        echo "\n" . 'GET: ';
        print_r($_GET);
    }
    
    echo "\n" . 'SERVER_NAME: ' . $_SERVER['SERVER_NAME'] . "<br />";
    echo "\n" . 'HTTP_HOST: ' . $_SERVER['HTTP_HOST'] . "<br />";
    echo "\n" . 'REMOTE_ADDR: ' . $_SERVER['REMOTE_ADDR'] . "<br />";
    echo 'SERVER_ADDR: ' . $_SERVER['SERVER_ADDR'] . "<br />";
    echo "\n" . dirname($_SERVER["REQUEST_URI"]) . "<br />";
    echo "\n" . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT'])) . "<br />";
    echo "\n" . 'CMS_ABSPATH: ' . CMS_ABSPATH . "<br />";
    echo "\n" . 'CMS_DIR: ' . CMS_DIR . "<br />";
    echo "\n" . 'CMS_URL: ' . CMS_URL . "<br />";
    echo "\n" . '<p></p>';
    echo "\n" . 'Sessions<br />';
    print_r2($_SESSION);
    echo "\n" . 'Classes<br />';
    print_r2(get_declared_classes());
    echo "\n" . '<p>----------  !LIVE || Administrators debug  ----------</p>';
}
?>