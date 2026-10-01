<?php

// include file
include_once '../../../cms/includes/inc.core.php';


function FileSizeConvert($bytes) {
    $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    foreach($arBytes as $arItem) {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}



if(isset($_REQUEST['token'])){
	// only accept $_POST from this §_SESSION['token']
	if ($_REQUEST['token'] == $_SESSION['token']) {

		
		// Flatten a multidimensional array to one dimension
		function flatten_array_values(array $a, $utf8_encode=false) {
			$ret_array = array();
			foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($a)) as $k=>$v){
				$ret_array[] = $utf8_encode = true? utf8_encode($v) : ($v);
			}
			return $ret_array;
		}

		// Flatten a multidimensional array to one dimension
		function flatten_array_keys(array $a, $utf8_encode=false) {
			$ret_array = array();
			foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($a)) as $k=>$v){
				$ret_array[] = $utf8_encode = true? utf8_encode($k) : $k;
			}
			return $ret_array;
		}

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

		/**
		 * Handle file uploads via regular form post (uses the $_FILES array)
		 */
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
				$val = trim($str);
				$last = strtolower($str[strlen($str)-1]);
				switch($last) {
					case 'g': $val *= 1024*1024*1024;
					case 'm': $val *= 1024*1024;
					case 'k': $val *= 1024;        
				}
				return $val;
			}
			
			private function set_newfilename($new_filename, $ext, $uploadDirectory){
			
				$a = array(null,null);
				// check if file exists, if so, add number at the end of string
				if (!is_file($uploadDirectory . $new_filename .'.'.$ext)) {						
					$a = array($uploadDirectory . $new_filename .'.'.$ext, null);
				} else {
					// add number and find next
					for($int = 1; $int++; $int <= 9) {
						if (!is_file($uploadDirectory . $new_filename.$int.'.'.$ext)) {						
							$a = array($uploadDirectory . $new_filename.$int.'.'.$ext, $int);
							break;
						} 
					}
				}
				return $a;
			}
			
			// Returns array('success'=>true) or array('error'=>'error message')
			function handleUpload($uploadDirectory){
				
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
				
				// new filename 50 characters
				// following this syntax
				// lets say email; admin@localhost.nu
				// admin_monthdayminutesecond_filename
				// attach to email all files named following syntax above - matching before $filename
				$email_confirmed = $_REQUEST['epost'];
				
				// max 5 characters email
				$e = explode("@",$email_confirmed);
				$part_email = substr($e[0], 0, 5);
				// 5 characters token
				$part_token = substr($_SESSION['token'], 0, 5);
				// 10 characters date
				$part_date = date('Ymd');
				// max 30 characters filename
				$part_filename = strlen($filename) > 30 ? substr($filename, 0, 30) :  $filename;
				
				$new_filename = $part_email.'_'.$part_token.'_'.$part_date.'_'.$part_filename;

				// check if file exists, if so, add number at the end of string				
				$arr = $this->set_newfilename($new_filename, $ext, $uploadDirectory);
				//print_r($arr);
				$f = $arr[0];
				$end = $arr[1];
				
				if ($this->file->save($f)){
					if (is_file($f)) {
						$filesize = FileSizeConvert(filesize($f));
						$filesize_true = filesize($f);
					}
					return array('success'=>true,'filename_original'=>$filename_original .$end.'.' . $ext,'filename'=>$filename . '.' . $ext,'filesize'=>$filesize, 'filesize_true'=>$filesize_true, 'filename2'=>$new_filename.$end.'.'.$ext);
				} else {
					return array('error'=> 'Could not save uploaded file. The upload was cancelled, or server error encountered');
				}
				
			}    
		}

		// list of valid extensions
		$allowedExtensions = array();
		// max file size in bytes
		$sizeLimit = 10 * 1024 * 1024;

		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);	
		$folder = CMS_ABSPATH .'/content/uploads/plugins/kursbokning/';
		$result = $uploader->handleUpload($folder);
		
		if(array_key_exists('success', $result)) {
			$filename = $result['filename'];
		}
	
		// to pass data through iframe you will need to encode all html tags
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

	}
}