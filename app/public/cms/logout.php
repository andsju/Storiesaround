<?php
// logout - destroy session
session_start();
session_destroy();   // destroy session data in storage
session_unset();     // unset $_SESSION variable for the runtime
header('Location: index.php');
exit;
?>