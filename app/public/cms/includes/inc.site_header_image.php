<?php if (!defined('VALID_INCL')) {
	die();
}

$header = $arr ? json_decode($arr['header'], true) : array();

$header_items = isset($header['items']) && is_array($header['items'])
	? $header['items']
	: array();

$header_caption_show = !empty($header['header_caption_show']);
$allowed_extensions = array('jpg', 'jpeg', 'gif', 'png', 'mp4');

echo '<div id="site-header-image" class="slideshow-cycle-wrapper parallax">';

if (count($header_items) > 0) {

	foreach ($header_items as $item) {

		if (!is_array($item) || empty($item['file'])) {
			continue;
		}

		$file = basename($item['file']);
		$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		if (!in_array($extension, $allowed_extensions, true)) {
			continue;
		}

		$caption = isset($item['caption']) ? $item['caption'] : '';
		$caption_align = isset($item['align']) ? $item['align'] : 'center';
		$caption_vertical_align = isset($item['vertical_align'])
			? $item['vertical_align']
			: 'middle';

		$valid_alignments = array('left', 'center', 'right');
		$valid_vertical_alignments = array('top', 'middle', 'bottom');

		if (!in_array($caption_align, $valid_alignments, true)) {
			$caption_align = 'center';
		}

		if (!in_array($caption_vertical_align, $valid_vertical_alignments, true)) {
			$caption_vertical_align = 'middle';
		}

		$file_url = CMS_DIR . '/content/uploads/header/' . rawurlencode($file);

		$caption = $header_caption_show ? $caption : '';

		$caption_html = htmlspecialchars(
			$caption,
			ENT_QUOTES,
			'UTF-8'
		);

		$caption_align_html = htmlspecialchars(
			$caption_align,
			ENT_QUOTES,
			'UTF-8'
		);

		$caption_vertical_align_html = htmlspecialchars(
			$caption_vertical_align,
			ENT_QUOTES,
			'UTF-8'
		);

		$file_url_html = htmlspecialchars(
			$file_url,
			ENT_QUOTES,
			'UTF-8'
		);

		if ($extension === 'mp4') {

			echo '<video'
				. ' class="slideshow-cycle-image"'
				. ' data-caption="' . $caption_html . '"'
				. ' data-caption-align="' . $caption_align_html . '"'
				. ' data-caption-vertical-align="' . $caption_vertical_align_html . '"'
				. ' autoplay'
				. ' loop'
				. ' muted'
				. ' playsinline>'
				. '<source src="' . $file_url_html . '" type="video/mp4">'
				. '</video>';
		} else {

			echo '<img'
				. ' src="' . $file_url_html . '"'
				. ' alt="' . $caption_html . '"'
				. ' data-caption="' . $caption_html . '"'
				. ' data-caption-align="' . $caption_align_html . '"'
				. ' data-caption-vertical-align="' . $caption_vertical_align_html . '"'
				. ' class="slideshow-cycle-image">';
		}
	}
	
} else {

	$fallback_file = isset($_SESSION['site_header_image'])
		? basename($_SESSION['site_header_image'])
		: '';

	if ($fallback_file !== '') {
		$fallback_extension = strtolower(
			pathinfo($fallback_file, PATHINFO_EXTENSION)
		);

		if (in_array($fallback_extension, $allowed_extensions, true)) {
			$fallback_url = CMS_DIR
				. '/content/uploads/header/'
				. rawurlencode($fallback_file);

			echo '<img'
				. ' src="' . htmlspecialchars($fallback_url, ENT_QUOTES, 'UTF-8') . '"'
				. ' alt=""'
				. ' class="slideshow-cycle-image">';
		}
	}
}

?>

</div>

<div id="site-header-caption"></div>