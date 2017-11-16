<?php
// include file
if (!defined('VALID_INCL')) {
    die();
}

// trim incoming data
$trimmed = array_map('trim', $_GET);

// assume false values
$x = $y = false;

// email
if (isset($trimmed['x']) && preg_match('/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/', $trimmed['x'])) {
    $x = filter_var($trimmed['x'], FILTER_SANITIZE_STRING);
}

// activation code
if (isset($trimmed['y']) && (strlen($trimmed['y']) == 32)) {
    $y = filter_var($trimmed['y'], FILTER_SANITIZE_STRING);
}

// check values
if ($x && $y) {

    $email = $x;
    $activation_code = $y;

    $users = new Users();
    $result = $users->setUsersActivate($email, $activation_code);

    if (!$result) {
        // link not active, redirect user to default page
        $url = CMS_URL;
        header("Location: $url");
        exit();
    }

} else {

    // link not active, redirect user to default page
    $url = CMS_URL;
    header("Location: $url");
    exit();
}
?>