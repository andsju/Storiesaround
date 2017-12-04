<?php
/* Write an image_list for TinyMCE 4 (output is JSON, not an array!) */
/* Replaces the old external_image_list_url "Example of a PHP-generated image list file" */
/* Instructions */
// 1. Place this file where you want to have it in your project.
// 2. $path: Write the path to your image folder.
// (It's easy when this file is in the folder with the document embedding TinyMCE)
// 3. Make a first test, BEFORE you call it within TinyMCE: 
// - $testmode = "on";
// - Call it with your browser (like you would do it with every "normal" PHP-Script) and NOT through TinyMCE
// - Watch the output: is everything ok? Can you see JSON?
// 4. Switch $testmode = "off";
// 5. Call it with TinyMCE;
// IMPORTANT: Do not change the file extension to ".js", even if this seems weird to you!
$testmode = "off";
$path = "../../../content/uploads/images/";
$images = Array();
$counter = 0;
if ($handle = @opendir($path)) {
    while (false !== ($file=readdir($handle))){
        if (strpos($file, ".") != 0) {
            $images[$counter]['title'] = $file;
            $images[$counter]['value'] = $path."/".$file;
            $counter++;
        }
    }
    closedir($handle);
}
elseif ($testmode == "on") {
    echo "Error: Can't find directory. Please write a valid path.";
    exit;
}
if ($counter == 0 && $testmode == "on") {
    echo "Error: This directory seems to be empty.";
    exit;
}
// Let PHP do the sorting an not the OS
ksort($images);
if ($testmode == "off") {
    // Make output a real JavaScript file!
    // browser will now recognize the file as a valid JS file
    header('Content-type: text/javascript');
    // prevent browser from caching
    header('pragma: no-cache');
    header('expires: 0'); // i.e. contents have already expired
}
if ($testmode == "on") {
    echo "<p><strong>This is the JSON I'll be delivering:</strong></p>";
}
echo json_encode($images);
?>