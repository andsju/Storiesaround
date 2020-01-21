<?php
header("Access-Control-Allow-Origin: *");
$a = array("version" => "2.0.0");
echo json_encode($a);
?>