<?php

// include file
include_once '../../../includes/core.inc.php';
//require_once '../../class/Image.class.php';

// if (isset($_FILES['token'])){
if(isset($_REQUEST['token'])){
	// only accept $_POST from this §_SESSION['token']
	//if ($_REQUEST['token'] == $_SESSION['token']) {



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

		function convert_to_filename($string) {
			
			// replace spaces
			$string = preg_replace ("/ +/", "-", $string);
			$string = preg_replace ("/^ +/", "", $string);
			$string = preg_replace ("/ +$/", "", $string);
			
			// lowercase
			// $string = utf8_encode(strtolower(utf8_decode($string)));
			// replace characters
			$characters = array(
			'Å' => 'A',
			'Ä' => 'A',
			'Ö' => 'O', 
			'Ø' => 'O',
			'É' => 'E',
			'Á' => 'A',
			'Æ' => 'A',	
			'å' => 'a',
			'ä' => 'a',
			'ö' => 'o', 
			'ø' => 'o',
			'é' => 'e',
			'á' => 'a',
			'æ' => 'a');
			
			$string = str_replace(flatten_array_keys($characters, $utf8_encode=false),flatten_array_values($characters), $string);
			
			// allow characters
			$string = preg_replace("/[^a-zA-Z0-9-_]/", "", $string); 
			return $string;	
		}




		/**
		 * Handle file uploads via XMLHttpRequest
		 */
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
			
			/**
			 * Returns array('success'=>true) or array('error'=>'error message')
			 */
			function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
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
				// $filename = $pathinfo['filename'];
				$filename = convert_to_filename($pathinfo['filename']);
				//$filename = rawurlencode(str_replace(' ', '_', strtolower($pathinfo['filename'])));
				//$filename = md5(uniqid());
				$ext = $pathinfo['extension'];

				if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
					$these = implode(', ', $this->allowedExtensions);
					return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
				}
				
				/*
				if(!$replaceOldFile){
					/// don't overwrite previous files that were uploaded
					while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
						$filename .= rand(10, 99);
					}
				}
				*/
				
				// prevent overwrite
				if(file_exists($uploadDirectory . $filename . '.' . $ext)) {
					return array('error' => 'File '. $uploadDirectory . $filename . '.' . $ext .' already exists');
				}
				
				
				if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){

					return array('success'=>true,'dir'=>$uploadDirectory,'filename'=>$filename . '.' . $ext);
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
		$result = $uploader->handleUpload('../../upload/');

		// to pass data through iframe you will need to encode all html tags
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

	//}
}
