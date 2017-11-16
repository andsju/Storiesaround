<?php

//Starts Here
//Put here the directory you want to search for. Put / if you want to search your entire domain
$dir='../';

//Put the date you want to compare with in the format of:  YYYY-mm-dd hh:mm:ss
$comparedatestr="2013-05-01 00:00:00";
$comparedate=strtotime($comparedatestr);

//I run the function here to start the search.
directory_tree($dir,$comparedate);

//This is the function which is doing the search...
function directory_tree($address,$comparedate){

 @$dir = opendir($address);

  if(!$dir){ return 0; }
        while($entry = readdir($dir)){
                if(is_dir("$address/$entry") && ($entry != ".." && $entry != ".")){                            
                        directory_tree("$address/$entry",$comparedate);
                }
                 else   {

                  if($entry != ".." && $entry != ".") {
                 
					/*
                    $fulldir=$address.'/'.$entry;
                    $last_modified = filemtime($fulldir);
                    $last_modified_str= date("Y-m-d h:i:s", $last_modified);

					if($comparedate < $last_modified)  {
					  echo $fulldir.'=>'.$last_modified_str;
					  echo "<BR>";
					}
					*/
					$filename=$address.'/'.$entry;
					$date = null;
					if (file_exists($filename)) {
						$date = filemtime($filename);
						clearstatcache();
					}
					echo '<p>';
					if($comparedate < $date)  {
					echo $filename . " " . date('Y-m-d h:i:s', $date);
					}
					echo '</p>';
                 }

            }

      }

}


?>