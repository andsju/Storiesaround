<?php
header("Access-Control-Allow-Origin: *");
$a = array("version" => "1.8.0");
echo json_encode($a);
?>