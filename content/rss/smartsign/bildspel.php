<?php
require_once '../../../cms/includes/inc.core.php';
if(!isset($_SESSION['site_id'])) {die;}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">

		<title><?php echo $_SESSION['site_name']; ?> - kalender</title>
		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script src="http://malsup.github.com/jquery.cycle2.js"></script>		

		<script>
		</script>
		
	</head>
	<body>
	<div class="cycle-slideshow" data-cycle-speed="1000" data-cycle-timeout="4000">
		<?php
		$images = array('bildspel/2.jpg', 'bildspel/3.jpg', 'bildspel/4.jpg');
		shuffle($images);
		
		foreach($images as $image) {
			echo '<img src="'.$image.'" style="">';
		}
		
		?>
	</div>
	
	</body>
</html>