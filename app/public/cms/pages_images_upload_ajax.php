<?php
// include file
include_once 'includes/inc.core.php';

if(isset($_REQUEST['token'])){

	if ($_REQUEST['token'] == $_SESSION['token']) {

		// Handle file uploads via XMLHttpRequest
		class qqUploadedFileXhr {
			/**
			 * Save the file to the specified path
			 * @return boolean TRUE on success
			 */
			function save($path) {    
				$input = fopen("php://input", "r");
				$temp = tmpfile();
				$realSize = stream_copy_to_stream($input, $temp);
				fclose($input);
				
				if ($realSize != $this->getSize()){            
					return false;
				}
				
				$target = fopen($path, "w");        
				fseek($temp, 0, SEEK_SET);
				stream_copy_to_stream($temp, $target);
				fclose($target);
				
				return true;
			}
			function getName() {
				return $_GET['qqfile'];
			}
			function getSize() {
				if (isset($_SERVER["CONTENT_LENGTH"])){
					return (int)$_SERVER["CONTENT_LENGTH"];            
				} else {
					throw new Exception('Getting content length is not supported.');
				}      
			}   
		}

		// Handle file uploads via regular form post (uses the $_FILES array)
		class qqUploadedFileForm {  
			/**
			 * Save the file to the specified path
			 * @return boolean TRUE on success
			 */
			function save($path) {
				if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
					return false;
				}
				return true;
			}
			function getName() {
				return $_FILES['qqfile']['name'];
			}
			function getSize() {
				return $_FILES['qqfile']['size'];
			}
		}

		class qqFileUploader {
			private $allowedExtensions = array();
			private $sizeLimit = 10485760;
			private $file;

			function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
				$allowedExtensions = array_map("strtolower", $allowedExtensions);
					
				$this->allowedExtensions = $allowedExtensions;        
				$this->sizeLimit = $sizeLimit;
				
				$this->checkServerSettings();       

				if (isset($_GET['qqfile'])) {
					$this->file = new qqUploadedFileXhr();
				} elseif (isset($_FILES['qqfile'])) {
					$this->file = new qqUploadedFileForm();
				} else {
					$this->file = false; 
				}
			}
			
			private function checkServerSettings(){        
				$postSize = $this->toBytes(ini_get('post_max_size'));
				$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
				
				if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
					$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
					die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
				}        
			}
			
			private function toBytes($str){
				$val = intval(trim($str));
				$last = strtolower($str[strlen($str)-1]);
				switch($last) {
					case 'g': $val *= 1024*1024*1024;
					case 'm': $val *= 1024*1024;
					case 'k': $val *= 1024;        
				}
				return $val;
			}
			
			// Returns array('success'=>true) or array('error'=>'error message')
			function handleUpload($uploadDirectory, $max_width, $original, $original_random) {
				if (!is_writable($uploadDirectory)){
					return array('error' => "Server error. Upload directory isn't writable.");
				}
				
				if (!$this->file){
					return array('error' => 'No files were uploaded.');
				}
				
				$size = $this->file->getSize();
				
				if ($size == 0) {
					return array('error' => 'File is empty');
				}
				
				if ($size > $this->sizeLimit) {
					return array('error' => 'File is too large');
				}
				
				$pathinfo = pathinfo($this->file->getName());
				$filename_original = $pathinfo['filename'];
				$filename = set_good_filename($pathinfo['filename'], $lowercase=false);

				$ext = $pathinfo['extension'];

				if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
					$these = implode(', ', $this->allowedExtensions);
					return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
				}
	
				// prevent overwrite
				if(file_exists($uploadDirectory . $filename . '.' . $ext)) {
					return array('error' => 'File '. $uploadDirectory . $filename . '.' . $ext .' already exists');
				}
							
				if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){


					$objImg = new Image();
				
					// create thumb
					// get ratio from uploaded file - return in success error for database save
					$ratio = $objImg->image_ratio($uploadDirectory . $filename . '.' . $ext);
					
					//save versions
					$sizes = $objImg->get_image_sizes();

					foreach($sizes as $size) {

						$objImg->image_resize($uploadDirectory . $filename .'.'. $ext, $uploadDirectory . $filename .'_'. $size .'.'. $ext, $size);
						if ($size == $max_width) {
							break;
						}
					}

					$image_description = $image_artist = '';
					
					if (is_file($uploadDirectory . $filename .'.'. $ext)) {
						$f = $uploadDirectory . $filename .'.'. $ext;
						
						
						// read exif IFD0 data
						// ... bug, sometimes an error
						if($ext == "jpg" || $ext == "jpeg") {
							// $exif = exif_read_data($f, 'IFD0');
							// $image_description = is_null($exif['ImageDescription']) ? '' : $exif['ImageDescription'];
							// $image_artist = is_null($exif['Artist']) ? '' : $exif['Artist'];
						}
						
						// read xmpdata						
						$r = $objImg->image_extract_xmpdata($f);
						
						// remove file
						if ($original == 0) {
							unlink($uploadDirectory . $filename .'.'. $ext);
						} else {							
							rename($uploadDirectory . $filename .'.'. $ext, $uploadDirectory . $filename . '_' . $original_random . '.'. $ext);
						}
					}
					
					$a = array('success'=>true,'dir'=>$uploadDirectory,'filename'=>$filename . '_100.' . $ext, 'ratio'=>$ratio, 'image_description'=>$image_description, 'artist'=>$image_artist, 'xmpdata' => $r);					
					return $a;
					
				
				} else {
					return array('error'=> 'Could not save uploaded file.' .
						'The upload was cancelled, or server error encountered');
				}
				
			}    
		}

		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$allowedExtensions = array();
		// max file size in bytes
		$sizeLimit = 10 * 1024 * 1024;

		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
				
		if(isset($_REQUEST['pages_folder'])){

			$original = isset($_REQUEST['original']) ? $_REQUEST['original'] : 'no original';
			$max_width = isset($_REQUEST['max_width']) ? $_REQUEST['max_width'] : 'nop max_width';
			
			$folder = CMS_ABSPATH .'/content/uploads/pages/'. $_REQUEST['pages_folder'] .'/';
			$original_random = $original == 1 ? rand_string(12) : "";
			$result = $uploader->handleUpload($folder, $max_width, $original, $original_random);
			
			$image = new Image();
			$sizes = $image->get_image_sizes();
			$sizes_to_db = "";
			foreach($sizes as $size) {
				$sizes_to_db .= $size . ",";
				if ($size == $max_width) {
					break;
				}
			}

			$sizes_to_db .= $original == 1 ? $original_random : "";
			$sizes_to_db = trim($sizes_to_db, ",");

			if(array_key_exists('success', $result)) {
				$ratio = $result['ratio'];
				$filename = $result['filename'];
				$image_description = $result['image_description'];
				$artist = $result['artist'];
				$xmpdata = json_encode($result['xmpdata']);
				//database save
				$save = new Pages();
				$save->savePagesImages($_REQUEST['pages_folder'], $filename, $ratio, $sizes_to_db, $image_description, $artist, $xmpdata);
			}
			
		}		

		// encode html tags
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

	}
}
