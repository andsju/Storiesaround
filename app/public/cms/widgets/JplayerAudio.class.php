<?php

include_once '../cms/includes/inc.core.php';

/**
 * API for class Jplayer
 * extends Widgets class
 */

class JplayerAudio extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'JplayerAudio';
		$a['description'] = 'Play audio files. One of the following audio formats must be supplied: mp3 or m4a. (Additional format: oga) (http://www.jplayer.org/)';
		$a['classname'] = 'JplayerAudio';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'sidebar';
		// external css in filepath as in libraries, '../libraries/?/?.css
		$a['css'] = 'jquery-jplayer/skin/jplayer-audio.css';
		return $a;
    }
	
	public function default_objects() {
		$default = '{"file": "", "title": "", "file2": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"file": "path", "title": "str", "file2": "path"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"file": "Set audio file, if not in page folder use path as -> /content/uploads/...", "title": "Set title if no id3 tags detects", "file2": "Set alternative audio file"}';
		return $help;
   }

   
	private function transl($text) {
		$a = array(
			"english" => array("Open Gallery" => "Open Gallery", "images" => "images"), 
			"swedish" => array("Open Gallery" => "Öppna Galleri", "images" => "bilder"));

		$l = isset($_SESSION['language']) ? $_SESSION['language'] : null;
		if(!$l) {
			$l = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : null;
		} 
		$s = $l ? $a[$l][$text] : $text;
		echo $s;
	}
   
   public function get_id3($file_meta, $filename) {
   
		if(is_file($file_meta)) {
			$data["song"] = $data["artist"] = null;		
			$f = fopen($file_meta, "r");
			fseek($f, -128, SEEK_END); // read some meta
			$tag = fread($f, 3);
			if($tag == "TAG") {
				$data["song"] = trim(fread($f, 30));
				$data["artist"] = trim(fread($f, 30));
			}
			fclose($f);
			
			$s = isset($data["song"]) ? $data["song"] .', '. $data["artist"] : $filename;
			return $s;
		}
   }
  
   
   
	public function run($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);

		//$w = ($width==474) ? 222 : 222;
		$w = $width;

		$file = isset($objects['file']) ? $objects['file'] : $defaults['file'];
		$fileinfo = pathinfo($file);
		$ext = $filename = null;
		if(is_array($fileinfo)) {
			if(isset($fileinfo['extension'])) {
				$ext = $fileinfo['extension'];
				$filename = $fileinfo['filename'];
			}
		}
		
		$file2 = isset($objects['file2']) ? $objects['file2'] : $defaults['file2'];
		$fileinfo2 = pathinfo($file2);
		$ext2 = $filename2 = null;
		if(is_array($fileinfo2)) {
			if(isset($fileinfo2['extension'])) {
				$ext2 = $fileinfo2['extension'];
				$filename2 = $fileinfo2['filename'];
			}
		}
		
		$path = $_SESSION['CMS_DIR'].'/content/uploads/pages/'.$pages_id .'/';
		$file_meta = $path .'/'.$file;

		$title = isset($objects['title']) ? $objects['title'] : $defaults['title'];
		$track = (strlen($title) > 0)  ? $title : $this->get_id3($file_meta, $filename);
		
		?>	


		<script>		
			var token = "<?php echo $_SESSION['token']; ?>";
			var id = <?php echo $pages_widgets_id; ?>;	
			var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
			$(document).ready(function() {

				$("#jquery_jplayer_<?php echo $pages_widgets_id; ?>").jPlayer({
					ready: function(event) {
						$(this).jPlayer("setMedia", {
							<?php
							switch($ext) {
								case 'mp3':
									?>
									mp3: "<?php echo $path . $filename .'.'. $ext; ?>",
									<?php
								break;
								case 'oga':								
									?>
									oga: "<?php echo $path . $filename .'.'. $ext; ?>",
									<?php
								break;
								case 'm4a':
									?>
									m4a: "<?php echo $path . $filename .'.'. $ext; ?>",
									<?php
								break;
							}
							switch($ext2) {
								case 'mp3':
									?>
									mp3: "<?php echo $path . $filename2 .'.'. $ext2; ?>",
									<?php
								break;
								case 'oga':								
									?>
									oga: "<?php echo $path . $filename2 .'.'. $ext2; ?>",
									<?php
								break;
								case 'm4a':
									?>
									m4a: "<?php echo $path . $filename2 .'.'. $ext2; ?>",
									<?php
								break;
							}
							?>
						}); 
					},
					play: function() { 
						$(this).jPlayer("pauseOthers");
					},					
					ended: function (event) {
						$(this).jPlayer("play");
					},				
					swfPath: "<?php echo $_SESSION['CMS_DIR'];?>/cms/libraries/jquery-jplayer/Jplayer.swf",
					supplied: "<?php echo $ext; ?>, <?php echo $ext2; ?>",
					volume: 1,
					wmode:"window",
					cssSelectorAncestor: "#jp_container_<?php echo $pages_widgets_id; ?>",
					solution: "html,flash"
				});
			}); 			
		</script>
		
		<div id="jquery_jplayer_<?php echo $pages_widgets_id; ?>" class="jp-jplayer"></div>

		<div id="jp_container_<?php echo $pages_widgets_id; ?>" class="jp-audio">
			<div class="jp-type-single">
				<div class="jp-gui jp-interface">
					<div class="jp-controls">
						<ul class="jp-controls">
							<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
							<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
							<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
							<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
							<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
						</ul>
					</div>
					
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
					<div class="jp-current-time"></div>					
				
				</div>
				<div class="jp-title">
					<ul>
						<li><?php echo $track ; ?></li>
					</ul>
				</div>
				<div class="jp-no-solution">
					<a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>
		<?php
	}
}
?>