<?php
header("Access-Control-Allow-Origin: *");
$a = array("version" => "2.1.0");
echo json_encode($a);
?>