<?php
// session access right
if (!isset($_SESSION['users_id'])) {
   header("Location: login.php");
}
?>