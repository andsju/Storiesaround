## Lägg till tabell `header`

```sql
ALTER TABLE `pages` ADD `header` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT 'json' AFTER `tag`;
```

## Lägg till default innehåll

```json

{
  "items": [
    {
      "file": "site-_header_image.jpg",
      "caption": "Första bildens text",
      "align": "center",
      "vertical_align": "middle"
    },
    {
      "file": "site-_header_image-2.jpg",
      "caption": "Andra bildens text",
      "align": "left",
      "vertical_align": "bottom"
    }
  ],
  "caption_show": true,
  "image_timeout": 10000,
  "parallax_scroll": true,
  "image_fade": "normal"
}

```

## Visning pages.php

```php
<?php if (!defined('VALID_INCL')) {
	die();
}
$header = json_decode($arr['header'], true);

if (!is_array($header)) {
	$header = array();
}

$header_items = isset($header['items']) && is_array($header['items'])
	? $header['items']
	: array();

$caption_show = !empty($header['caption_show']);

$allowed_extensions = array('jpg', 'jpeg', 'gif', 'png', 'mp4');

// print_r2($header);

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

		$caption = $caption_show ? $caption : '';

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
```


---



Replace 

```php
						<?php

							$header_image = json_decode($arr['header_image']);
							$header_caption = json_decode($arr['header_caption']);
							$header_caption_align = json_decode($arr['header_caption_align']);
							$header_caption_vertical_align = json_decode($arr['header_caption_vertical_align']);

							$dir = '/content/uploads/header';
																
							if (is_dir(CMS_ABSPATH . '/'. $dir)) {

								if ($dh = opendir(CMS_ABSPATH .'/'. $dir)) {
									$images_ext = array('jpg','jpeg','gif','png', 'mp4');

									while (($file = readdir($dh)) !== false) {
										if (!is_dir(CMS_ABSPATH .'/'. $dir.'/'.$file)) {
										
											$ext = pathinfo($dir.'/'.$file, PATHINFO_EXTENSION);
											if(in_array($ext, $images_ext)) {
												$checked = "";
												if (is_array($header_image)) {
													$checked = in_array($file, $header_image) ? " checked" : "";
												}
												if ($ext != 'mp4') {
													echo '<div class="code" style="position:relative"><img alt="'.$file.'" src="../content/uploads/header/'. $file .'" data-filename="'.$file.'" width="150px" style="margin-bottom:10px;" /><input type="checkbox" '.$checked.' class="image_mark" data-file="'.$file.'" data-video="false" style="position:absolute;top:2px;left:2px;transform:scale(2);"></div>';
												} else {
													echo '<div class="code" style="position:relative"><video id="header_video_url" width="150" muted controls><source src="..'.$dir.'/'.$file.'"></video><input type="checkbox" '.$checked.' class="image_mark" data-file="'.$file.'" data-video="true" style="position:absolute;top:2px;left:2px;transform:scale(2);"></div>';
												}

											}
										}
									}
									closedir($dh);
								}
							}	
						?>

						
```


2

```php
						if (is_array($header_image)) {
							$dir = '/content/uploads/header';
							if (is_dir(CMS_ABSPATH . '/'. $dir)) {
								$countHeader = 0;
								foreach ($header_image as $image) { 
									$ext = getFileExtension($image);
									$align_value = getCaptionAlignAsInteger($header_caption_align[$countHeader]);
									$vertical_align_value = getCaptionVerticalAlignAsInteger($header_caption_vertical_align[$countHeader]);
									if ($ext == "mp4") {

									?>
										<div data-image="<?= $image ?>" style="" class="header-images">
											<video style="width:100%; max-height:100px" class="header-images" controls muted>
												<source src="<?= $dir ?>/<?= $image ?>">
											</video>
											<textarea name="header_caption[]"><?= $header_caption[$countHeader] ?></textarea>
											<input type="range" name="header_caption_align[]" class="header_caption_align" value="<?= $align_value ?>" min="0" max="2">
											<input type="range" name="header_caption_vertical_align[]" class="header_caption_vertical_align" value="<?=  $vertical_align_value ?>" min="0" max="2">
										</div>

									<?php
									} else {
									?>
										
										<div data-image="<?= $image ?>" style="background-image:url(../content/uploads/header/<?= $image ?>);" class="header-images">
											<textarea name="header_caption[]"><?= $header_caption[$countHeader] ?></textarea>
											<input type="range" name="header_caption_align[]" class="header_caption_align" value="<?= $align_value ?>" min="0" max="2">
											<input type="range" name="header_caption_vertical_align[]" class="header_caption_vertical_align" value="<?= $vertical_align_value ?>" min="0" max="2">
										</div>

									<?php
									}
									$countHeader++;
								}
							}
						}

```