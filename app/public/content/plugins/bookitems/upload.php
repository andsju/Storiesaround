<?php
require_once '../../../cms/includes/inc.core.php';


//sleep(1);
//print_r($_FILES);









$allowed_extensions = array("gif", "jpeg", "jpg", "png");
$max_size = 2000000;
$save_file_to_path = CMS_ABSPATH . '/content/uploads/plugins/bookitems/';
$crop_width = 100;
$crop_height = 75;



// folder
if (!is_dir(CMS_ABSPATH."/content/uploads/plugins/bookitems")) {
	mkdir(CMS_ABSPATH."/content/uploads/plugins/bookitems", 0777);
}		



$temp = explode(".", $_FILES["upload_field"]["name"]);
$extension = end($temp);

if(in_array($extension, $allowed_extensions)) {

	if($_FILES["upload_field"]["size"] < $max_size) {

		if ($_FILES["upload_field"]["error"] == 0) {
		
			/*
			$r = "Size: " . ($_FILES["upload_field"]["size"] / 1024) . " kB<br>";
			$r .= "Stored in: " . $_FILES["upload_field"]["tmp_name"];
			*/
			if(save($save_file_to_path, $crop_width, $crop_height)) {
				
			
			}
			
		}

	} else {
		$result = array('success'=>false, 'error' => 'Max file size: 2Mb');
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}
} else {
	$s = null;
	foreach($allowed_extensions as $ext) {
		$s .= $ext . ' ';
	}
	$result = array('success'=>false, 'error' => 'Allowed files: '.$s);
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
}

function save($save_file_to_path, $crop_width, $crop_height) {

	// great filename
	$path_parts = pathinfo($_FILES["upload_field"]["name"]);
	//lower characters
	$n = trim(strtolower($path_parts['filename']));
	// blanks
	$n = preg_replace('/\s+/', '_', $n);
	// translate accents and special characters
	$n = replace_characters($n);
	// add extension
	$filename = $n.'.'.$path_parts['extension'];
	
	
	// prevent overwrite
	if(file_exists($save_file_to_path . $filename)) {
		$result = array('success'=>false, 'error' => 'File '. $save_file_to_path . $filename .' already exists');
		
	} else {

	
		if(move_uploaded_file($_FILES['upload_field']['tmp_name'], $save_file_to_path . $filename)){
			
			// create thumb
			$objImg = new Image();
			// get ratio from uploaded file - return in success error for database save
			$ratio = $objImg->image_ratio($save_file_to_path . $filename);
			
			//save versions
			$objImg->image_resize_crop($save_file_to_path . $filename, $save_file_to_path . $filename, $crop_width, $crop_height);
			
			$size = filesize($save_file_to_path . $filename);

			//return true;
			$result = array('success'=>true,'image'=>$filename,'size'=>format_size($size));
			
		}
		
		
	}
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

	// remove file
	//unlink($_FILES['upload_field']['tmp_name']);

}

function format_size($size) {
      $sizes = array(" bytes", " kB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
      if ($size == 0) { return('n/a'); } else {
      return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); }
}


?>
