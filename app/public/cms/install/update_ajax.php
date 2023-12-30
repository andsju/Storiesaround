<?php
// include core 
include_once '../includes/inc.core.php';
if(!get_role_CMS('superadministrator') == 1) {die;}

// overall
if (isset($_POST['token'])){

	if ($_POST['token'] == $_SESSION['token']) {

		ini_set('max_execution_time', 300); 
		
		$action = filter_var(trim($_POST['action']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		
		switch($action) {
		
			case "site_check_version":

				sleep(2);
				
				$dir = CMS_ABSPATH.'/_tmp';
				
				if (is_dir($dir) && is_file($dir.'/storiesaround.zip')) {
					echo 'File exists';
				} else {
					echo 'Missing file';
				}
				
				echo ' Newer version exists. Upgrade?';

			
			break;

			
			case "site_do_it_all":

				sleep(2);
				
				echo 'Checking temporary working folder... ';

				$r = $r2 = '';
				
				$dir = CMS_ABSPATH.'/_tmp';
				
				
				if (!is_dir($dir)) {
					mkdir($dir, 0755);
					$r = ' ... temporary folder created... ';
				} else {
					$r = ' ... temporary folder exists... ';
					
					$iterator = new \FilesystemIterator($dir);
					$isDirEmpty = !$iterator->valid();

				}
				
				echo $r;
			
			break;

			case "site_backup_cms":

				sleep(2);
				
				$result = zip_it($source = CMS_ABSPATH, $destination = CMS_DIR, $zipfile = 'backup.zip', $include_dir = false, $just_count_files=false);				
				$reply = $result ?  'Saved a backup of Storiesaround <b>(not content)</b> to file <p><span class="code code-highlight">'.CMS_ABSPATH.'/_tmp/'. $zipfile .' ('.$result.' files)</span></p>': 'fail';
				
				echo $reply;

			
			break;

			case "site_update":
				
				include_once CMS_ABSPATH .'/cms/install/inc.update.php';
				$site = new Site();
				
				if(!is_array($sqls)) {die;}
				foreach($sqls as $sql) {
					$result = $site->setSiteUpdate($sql);
					$reply = $result ? date("H:i:s") .' | success' : null;
					echo $reply .'<br />';
				}
				
			break;

			case "site_version_check_read_file":

				echo get_version(CMS_ABSPATH.'/cms/includes/inc.version.php');
				
			break;

			
			case "site_update_alter":
				
				include_once CMS_ABSPATH .'/cms/install/inc.update.php';
				$site = new Site();
				
				// execute sqls in following order:
				// 1	ALTER ... CHANGE COLOUMN ...
				// 2	ALTER ... MODIFY COLOUMN ...
				// 3	ALTER ... ADD COLOUMN ...
				// 4	ALTER ... DROP COLOUMN ...
				// 5	SQL cmd
				
				$i = 0;

				// sql cmd in array $sqls_alter_change_column
				if(is_array($sqls_alter_change_column)) {
					foreach($sqls_alter_change_column as $sql_alter_change_column) {
						write_debug($sql_alter_change_column);
						$result = $site->setSiteUpdateAlterChangeColumn($sql_alter_change_column);
						write_debug($result);
						$reply = $result ? date("H:i:s") .' | success' : null;
						if($result) {
							$i++;
							echo $reply .'<br />';
						}						
					}
				}

				// sql cmd in array $sqls_alter_modify_column
				if(is_array($sqls_alter_modify_column)) {
					foreach($sqls_alter_modify_column as $sql_alter_modify_column) {
						$result = $site->setSiteUpdateAlterModifyColumn($sql_alter_modify_column);
						$reply = $result ? date("H:i:s") .' | success' : null;
						if($result) {
							$i++;
							echo $reply .'<br />';
						}						
					}
				}
				
				// sql cmd in array $sqls_alter_add_column
				if(is_array($sqls_alter_add_column)) {
					foreach($sqls_alter_add_column as $sql_alter_add_column) {
						$result = $site->setSiteUpdateAlterAddColumn($sql_alter_add_column);
						$reply = $result ? date("H:i:s") .' | success' : null;
						if($result) {
							$i++;
							echo $reply .'<br />';
						}						
					}
				}
				
				// sql cmd in array $sqls_alter_drop_column
				if(is_array($sqls_alter_drop_column)) {
					foreach($sqls_alter_drop_column as $sql_alter_drop_column) {
						$result = $site->setSiteUpdateAlterDropColumn($sql_alter_drop_column);
						$reply = $result ? date("H:i:s") .' | success' : null;
						if($result) {
							$i++;
							echo $reply .'<br />';
						}						
					}
				}
				
				// sql cmd in array $sqls[]
				if(is_array($sqls)) {
					foreach($sqls as $sql) {
						$result = $site->setSiteUpdate($sql);
						$reply = $result ? date("H:i:s") .' | success' : null;
						if($result) {
							$i++;
							echo $reply .'<br />';
						}
					}
				}
				
				echo '<h5>Update summary</h5>';
				echo $i .' SQL commands executed';
				
				
			break;

			
			case "site_backup_db":
			
				define("BACKUP_PATH", "/");

				$server_name   = DB_HOST;
				$username      = DB_USER;
				$password      = DB_PASS;
				$database_name = DB_NAME;
				$date_string   = '';
				
				define( 'DUMPFILE', CMS_ABSPATH .'/_tmp/backup_'.DB_NAME . '.sql' );
				
				sleep(2);
				 
				try {
					$db = new PDO( 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS );
					$f = fopen( DUMPFILE, 'wt' );
				 
					$tables = $db->query( 'SHOW TABLES' );
					$i = 0;
					foreach ( $tables as $table ) {
						//echo $table[0] . ' ... '; flush();
						$sql = '-- TABLE: ' . $table[0] . PHP_EOL;
						$create = $db->query( 'SHOW CREATE TABLE `' . $table[0] . '`' )->fetch();
						$sql .= $create['Create Table'] . ';' . PHP_EOL;
						fwrite( $f, $sql );
				 
						$rows = $db->query( 'SELECT * FROM `' . $table[0] . '`' );
						$rows->setFetchMode( PDO::FETCH_ASSOC );
						foreach ( $rows as $row ) {
							$row = array_map( array( $db, 'quote' ), $row );
							$sql = 'INSERT INTO `' . $table[0] . '` (`' . implode( '`, `', array_keys( $row ) ) . '`) VALUES (' . implode( ', ', $row ) . ');' . PHP_EOL;
							fwrite( $f, $sql );
						}
				 
						$sql = PHP_EOL;
						$result = fwrite( $f, $sql );
						if ( $result !== FALSE ) {
							// echo 'OK' . PHP_EOL;
							$i++;
						} else {
							echo 'ERROR!!' . PHP_EOL;
						}
						
						flush();
					}
					if($i) {
						echo 'Database saved to <p><span class="code code-highlight">'. DUMPFILE . ' ('.$i.' tables)</span></p>';
					}
					fclose( $f );
				} catch (Exception $e) {
					echo 'fail... ' . $e->getMessage() . PHP_EOL;
				}

				
			break;
			
			case "site_copy_from_zip":

				sleep(2);
				
				$file = CMS_ABSPATH.'/_tmp/storiesaround.zip';
				$extract_path = CMS_ABSPATH.'/_tmp/version/storiesaround';
				$result = unzip_it($file, $extract_path);
				$reply = $result ?  'New version unzipped to <p><span class="code code-highlight">'. $extract_path .'</span></p>': 'Fail unzip files';

				echo '<p>'.$reply.'</p>';

				sleep(2);
				
				$destination = CMS_ABSPATH.'/_tmp/target';
				$result = recurse_copy($extract_path, $destination);
				$reply = $result ?  'Files copied to <p><span class="code code-highlight">'. $destination .'</span></p>' : 'Fail - copy from zip file';

				echo '<p>'.$reply.'</p>';
				
			break;

			case "site_version_swap":

				$new_version = '/cms_new';
				$current = '/cms';
				$previous_version = '/cms_old';

				
				// delete old if exists
				
				$directory = CMS_ABSPATH.$previous_version;
				if(is_dir($directory)){
					$files = new RecursiveIteratorIterator(
						new RecursiveDirectoryIterator($directory,RecursiveDirectoryIterator::SKIP_DOTS),
						RecursiveIteratorIterator::CHILD_FIRST
					);
					foreach($files as $fileinfo){
						$todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
						$todo($fileinfo->getRealPath());
					}

					unset($files);

					rmdir($directory);
					//var_dump(is_dir($directory));
				}else {
					$make = mkdir($directory);
				
					$str = $make? 'create successful' : 'not success';
				}
				
				sleep(2);
				
				if(is_dir(CMS_ABSPATH.$new_version)) {
				
					$result = rename(CMS_ABSPATH.$current, CMS_ABSPATH.$previous_version);
					if($result) {
						sleep(2);
						$result2 = rename(CMS_ABSPATH.$new_version, CMS_ABSPATH.$current);
						sleep(2);
						$str .= $result2 ? "Aloha!" : "Snap...reverse..."; 
						$str .= get_version(CMS_ABSPATH.'/cms/includes/inc.version.php');
						echo $str;
						
					}
				}
			
			break;
			
		}
	}
}




function zip_it($source, $destination, $zipfile, $include_dir = false, $just_count_files=true) {
    $i = 0;
	if (!extension_loaded('zip')) {
        return false;
    }
	
	if (!$just_count_files) {
		if (file_exists(CMS_ABSPATH.'/_tmp/'.$zipfile)) {
			unlink(CMS_ABSPATH.'/_tmp/'.$zipfile);
		}

		$zip = new ZipArchive();
		if (!$zip->open(CMS_ABSPATH.'/_tmp/'.$zipfile, ZIPARCHIVE::CREATE)) {
			return false;
		}
	}

    $source = realpath($source);

    if (is_dir($source) === true) {

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        if ($include_dir) {

            $arr = explode(DIRECTORY_SEPARATOR, $source);
            $maindir = $arr[count($arr)- 1];

            $source = "";
            for ($i=0; $i < count($arr) - 1; $i++) {
                $source .= DIRECTORY_SEPARATOR . $arr[$i];
            }

            $source = substr($source, 1);
			
			if (!$just_count_files) {
				$zip->addEmptyDir($maindir);
			}

        }

        foreach ($files as $file) {
            // ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            $file = realpath($file);

			// skip folders
			if(strpos($file, DIRECTORY_SEPARATOR .'uploads')) { continue; }
			if(strpos($file, DIRECTORY_SEPARATOR .'log')) { continue; }
			if(strpos($file, DIRECTORY_SEPARATOR .'_docs')) { continue; }
			if(strpos($file, DIRECTORY_SEPARATOR .'_tmp')) { continue; }
			
            if (is_dir($file) === true) {

				if(strpos($file, DIRECTORY_SEPARATOR)) { continue; }

				if (!$just_count_files) {
					$zip->addEmptyDir(str_replace($source . DIRECTORY_SEPARATOR, '', $file . DIRECTORY_SEPARATOR));
				}
            } else if (is_file($file) === true) {
				if (!$just_count_files) {
					$zip->addFromString(str_replace($source . DIRECTORY_SEPARATOR, '', $file), file_get_contents($file));
				}
            }
			$i++;
        }
		
    } else if (is_file($source) === true) {
		if (!$just_count_files) {
			$zip->addFromString(basename($source), file_get_contents($source));
		}
    }
	if (!$just_count_files) {
		$zip->close();
		return $i;
	} else {
		return $i;
	}
}


function unzip_it($file, $extract_path) {
	$zip = new ZipArchive;
	$result = $zip->open($file);
	if ($result) {

		/*
	   for ($i = 0; $i < $zip->numFiles; $i++) {
			 echo $zip->getNameIndex($i)  . '<br />'; 
		}
		*/

		$zip->extractTo($extract_path);
		$zip->close();

		return true;
	} else {
		return false;
	}
}


function get_version($file) {
	$search = '$cms_version';
	$matches = array();
	$open_file = fopen($file, 'r');
	if ($open_file) {
		while (!feof($open_file)) {
			$buffer = fgets($open_file);
			if(strpos($buffer, $search) !== FALSE) {
				$matches[] = $buffer;
				break;
			}
		}
		fclose($open_file);
	}
	$string = $matches[0];
	return substr($string, 16, 5);
}

function recurse_copy($src,$dst) { 
	if (!file_exists($dst)) {
		mkdir($dst, 0755);
	}	
    $dir = opendir($src); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
	return true;
} 

?>