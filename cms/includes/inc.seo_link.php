<?php

// encode strings to utf-8
function encode_to_utf8(&$item, $key) {
	if (mb_detect_encoding($item, 'UTF-8', true) === false) {
		$item = utf8_encode($item);
	}	
}


// scrubs characters and words
function set_seo_title($input, $replace=null, $remove_stopwords=true, $stopwords_array=array()) {
	//lower characters
	$seo = trim(strtolower($input));
	//remove multiple spaces including \t and \n
	$seo = preg_replace('/\s+/', ' ', $seo);
	
	if($remove_stopwords) {
		//make an array of title
		$seo = explode(' ',$seo);
		//utf-8 encode title parts 
		array_walk_recursive($seo, 'encode_to_utf8');
		//just in case - trim stopwords array values
		$stopwords_array = array_map('trim',$stopwords_array);
		//utf-8 encode stopwords to 
		array_walk_recursive($stopwords_array, 'encode_to_utf8');
		
		//new seo title parts
		$seo_result = array();
		// remowe stopwords from title parts
		foreach ($seo as $s) {
			if(!in_array($s,$stopwords_array)) {
				$seo_result[] = $s;
			}
		}
		
		//go from array to string, set words splitter
		$seo = implode($replace, $seo_result);
	} else {
		// set words splitter
		$seo = preg_replace('/\s+/', $replace, $seo);		
	}

	// translate accents and special characters
	$seo = replace_characters($seo);
$seo = trim(strtolower($seo));	
	//keep alfanumerics and dash
	$seo = preg_replace('/[^a-zA-Z0-9-\s]/', '', $seo);
	$seo = preg_replace('/-+/', '-', $seo);

	return $seo;	
}


// scrubs characters and words
function suggest_words($input, $replace=null, $remove_stopwords=true, $stopwords_array=array()) {
	//lower characters
	$seo = trim(strtolower($input));
	//remove multiple spaces including \t and \n
	$seo = preg_replace('/\s+/', ' ', $seo);
	
	//make an array of words
	$seo = explode(' ',$seo);
	//utf-8 encode parts 
	array_walk_recursive($seo, 'encode_to_utf8');
	//just in case - trim stopwords array values
	$stopwords_array = array_map('trim',$stopwords_array);
	//utf-8 encode stopwords to 
	array_walk_recursive($stopwords_array, 'encode_to_utf8');
	
	//new seo parts
	$seo_result = array();
	// remowe stopwords from title parts, remove short words
	foreach ($seo as $s) {
		if(!in_array($s,$stopwords_array) && strlen($s)>2) {
			$seo_result[] = $s;
		}
	}
	
	// remove duplicates
	$seo_result = array_unique($seo_result);
	
	// keep first 20
	$seo_result = array_slice($seo_result, 0, 20);  
	
	//go from array to string, set words splitter
	$seo = implode($replace, $seo_result);

	// keep aplhanumerical
	$seo = preg_replace('/[^a-zA-Z0-9-åäö,\s]/', '', $seo);
	
	return $seo;	
}


//stopwords in string
//$w = "a, a’s, able, about, above";
//stopwords stored in file
$stopwords_file = 'includes/inc.stopwords.php';
ini_set('auto_detect_line_endings',true);

//get stopwords reading file line per line
function get_stopwords($stopwords_file) {
	if(file_exists($stopwords_file)) {
		$lines = file($stopwords_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return $lines;
    } 
}

$stopwords_array = get_stopwords($stopwords_file);
// use ->
//$s = set_seo_title($input, $replace = "-", $remove_stopwords=true, $stopwords_array);

?>