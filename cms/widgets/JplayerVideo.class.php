<?php

require_once '../cms/includes/inc.core.php';

/**
 * API for class Jplayer
 * extends Widgets class
 */

class JplayerVideo extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'JplayerVideo';
		$a['description'] = 'Play video files - format m4v. (Additional format: ogv) (http://www.jplayer.org/)';
		$a['classname'] = 'JplayerVideo';
		// acceptable columns: 'sidebar', 'content' or either ''
		$a['column'] = 'content';
		// external css in filepath as in librarise, '../libraries/?/?.css
		$a['css'] = 'jquery-jplayer/skin/jplayer-video.css';
		return $a;
    }
	
	public function default_objects() {
		$default = '{"file": "", "title": "", "file2": ""}';
		return $default;
    }
	
	public function default_objects_validate() {
		// validate objects using FILTER_VALDIDATE_REGEXP and function isValidString()
		$default_validate = '{"file": "path", "title": "words", "file2": "path"}';
		return $default_validate;
   }

	public function help() {
		// help text to show in form
		$help = '{"file": "Set video file, if not in page folder use path as -> /content/uploads/files", "title": "Set title", "file2": "Set alternative video file"}';
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
   
   
	public function JplayerVideo($action, $pages_widgets_id=null, $pages_id=null, $width=null) {
		// return objects in an associative array
		$objects = json_decode($action, true);
		$defaults = json_decode($this->default_objects(), true);


		$w = ($width==474) ? 474 : 474;
		$w = $width;
		
		$file = isset($objects['file']) ? $objects['file'] : $defaults['file'];
		$fileinfo = pathinfo($file);
		$ext = $filename = null;
		if(is_array($fileinfo)) {
			if(isset($fileinfo['extension'])) {
				$ext = $fileinfo['extension'];
				$filename = $fileinfo['filename'];
				$dirname = $fileinfo['dirname'];
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
		$path = strstr($dirname, '/') ? $dirname .'/' : '/content/uploads/pages/'.$pages_id .'/';
		
		$path = 'http://'. CMS_PATH . $path;
		
		// title 
		$title = isset($objects['title']) ? $objects['title'] : $defaults['title'];		
		?>	

		<script>
			var token = "<?php echo $_SESSION['token']; ?>";
			var id = <?php echo $pages_widgets_id; ?>;
			$(document).ready(function() {

				$("#jquery_jplayer_<?php echo $pages_widgets_id; ?>").jPlayer({
					ready: function(event) {
						$(this).jPlayer("setMedia", {							
							<?php
							switch($ext) {
								case 'm4v':
									?>
									m4v: "<?php echo $path . $filename .'.'. $ext; ?>",
									<?php
								break;
								case 'ogv':	
									?>
									ogv: "<?php echo $path . $filename .'.'. $ext; ?>",
									<?php
								break;
							}
							switch($ext2) {
								case 'm4v':
									?>
									m4v: "<?php echo $path . $filename2 .'.'. $ext2; ?>",
									<?php
								break;
								case 'ogv':								
									?>
									ogv: "<?php echo $path . $filename2 .'.'. $ext2; ?>",
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
					supplied: "<?php echo $ext; ?><?php if ($ext2) { echo ','. $ext2; } ?>",
					size: {
						width: "<?php echo $w."px";?>",
						height: "<?php echo $w/1.75."px";?>",
						cssClass: "jp-video-360p"
					},
					wmode: "window",	
					preload: "auto",
					solution: "html,flash",
					cssSelectorAncestor: "#jp_container_<?php echo $pages_widgets_id; ?>"
				});
			}); 
			
		</script>
		
	<?php if($ext != null) { ?>
		<div id="jp_container_<?php echo $pages_widgets_id; ?>" class="jp-video jp-video-360p">		
			<div id="jquery_jplayer_<?php echo $pages_widgets_id; ?>" class="jp-jplayer"></div>
			<div class="jp-interface">
				<div class="jp-controls">
					<ul class="jp-controls">
						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
						<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>
						<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>
					</ul>
				</div>
				
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<div class="jp-current-time"></div>
			
			</div>
			<div class="jp-title-video">
				<ul>
					<li><?php echo $title ; ?></li>
				</ul>
			</div>
				
			<div class="jp-no-solution">
				<span>Update Required</span>
			</div>
		
		</div>
	<?php } ?>
		
	<?php	
	}
}
?>