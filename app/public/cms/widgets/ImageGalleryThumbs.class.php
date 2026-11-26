<?php

/**
 * API for class ImageGalleleryThumbs
 * extends Widgets class
 */

class ImageGalleryThumbs extends Widgets {

	public function __construct() {
        parent::__construct();
    }

	public function info() {
		$a = array();
		$a['title'] = 'ImageGalleryThumbs';
		$a['description'] = 'Show pages images slideshow';
		$a['classname'] = 'ImageGalleryThumbs';
		$a['column'] = 'content';
		return $a;
    }
	public function default_objects() {
		return '{"timeout": "7000", "speed": "1000", "ratio": "4:3", "control": "true", "thumbs": "true", "caption": "true", "caption_css": "", "copyright": "true", "tag": ""}';
	}

	public function default_objects_validate() {
		return '{"timeout": "milliseconds", "speed": "milliseconds", "ratio": "ratio", "control": "boolean", "thumbs": "boolean", "caption": "boolean", "caption_css": "css", "copyright": "boolean", "tag": "alphanumerical"}';
	}

	public function help() {
		return '{"timeout": "slideshow duration in milliseconds (0 disables autoplay)", "speed": "slideshow transition speed in milliseconds", "ratio": "image width:height ratio, e.g. 1:1, 4:3, 16:9, 21:9", "control": "show slideshow controls", "thumbs": "show navigation thumbs", "caption": "show image caption", "caption_css": "inline css styling caption", "copyright": "show creator or copyright in the caption", "tag": "filter images"}';
	}

	public function run($action, $pages_widgets_id = null, $pages_id = null, $width = null) {
		$objects = json_decode($action ?? '', true);
		$defaults = json_decode($this->default_objects(), true);
		$objects = is_array($objects) ? array_merge($defaults, $objects) : $defaults;
		$ratio = explode(':', $objects['ratio']);
		$height_ratio = count($ratio) === 2 && is_numeric($ratio[0]) && is_numeric($ratio[1]) && $ratio[0] > 0 && $ratio[1] > 0
			? (float) $ratio[1] / (float) $ratio[0]
			: 0.75;
		$id = (int) $pages_widgets_id;
		$control = filter_var($objects['control'], FILTER_VALIDATE_BOOLEAN);
		$thumbs = filter_var($objects['thumbs'], FILTER_VALIDATE_BOOLEAN);
		$caption = filter_var($objects['caption'], FILTER_VALIDATE_BOOLEAN);
		$language = $_SESSION['language'] ?? $_SESSION['site_language'] ?? 'english';
		$swedish = $language === 'swedish';
		$config = array(
			'id' => $id,
			'token' => $_SESSION['token'],
			'cmsDir' => $_SESSION['CMS_DIR'],
			'tag' => $objects['tag'],
			'timeout' => max(0, (int) $objects['timeout']),
			'speed' => max(0, (int) $objects['speed']),
			'heightRatio' => $height_ratio,
			'copyright' => filter_var($objects['copyright'], FILTER_VALIDATE_BOOLEAN),
			'photo' => $swedish ? 'Foto:' : 'Photo:',
			'loadError' => $swedish ? 'Kunde inte ladda bilderna.' : 'Could not load images.',
			'empty' => $swedish ? 'Inga bilder hittades.' : 'No images found.',
			'imageLabel' => $swedish ? 'Visa bild' : 'Show image'
		);
		?>
		<div id="image_gallery_splide_wrapper_<?php echo $id; ?>" class="slideshow_extended_wrapper">
			<div id="image_gallery_splide_status_<?php echo $id; ?>" role="status"></div>
			<div id="image_gallery_splide_content_<?php echo $id; ?>" style="display:none">
				<?php if ($control) { ?>
					<div class="slideshow_extended_nav">
						<button type="button" class="slideshow_extended_control" data-gallery-action="previous">&ll;</button>
						<button type="button" class="slideshow_extended_control" data-gallery-action="pause"<?php echo $config['timeout'] === 0 ? ' disabled' : ''; ?>>&cir;</button>
						<button type="button" class="slideshow_extended_control" data-gallery-action="play"<?php echo $config['timeout'] === 0 ? ' disabled' : ''; ?>>&RightTriangle;</button>
						<button type="button" class="slideshow_extended_control" data-gallery-action="next">&gg;</button>
						<span data-gallery-counter></span>
					</div>
				<?php } ?>
				<div id="image_gallery_splide_<?php echo $id; ?>" class="splide slideshow_extended" role="region" aria-label="<?php echo $swedish ? 'Bildspel' : 'Image slideshow'; ?>">
					<div class="splide__track"><ul class="splide__list"></ul></div>
				</div>
				<?php if ($caption) { ?>
					<div class="slideshow_extended_caption" data-gallery-caption style="<?php echo htmlspecialchars($objects['caption_css'], ENT_QUOTES, 'UTF-8'); ?>"></div>
				<?php } ?>
				<?php if ($thumbs) { ?>
					<div data-gallery-thumbs style="background:black"></div>
				<?php } ?>
			</div>
		</div>
		<script>
		jQuery(function ($) {
			var config = <?php echo json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
			var wrapper = $("#image_gallery_splide_wrapper_" + config.id);
			var content = wrapper.find("#image_gallery_splide_content_" + config.id);
			var status = wrapper.find("#image_gallery_splide_status_" + config.id);
			var list = content.find(".splide__list");
			var thumbnails = content.find("[data-gallery-thumbs]");
			var caption = content.find("[data-gallery-caption]");
			var counter = content.find("[data-gallery-counter]");

			$.ajax({
				url: config.cmsDir + "/cms/pages_ajax.php",
				dataType: "json",
				data: {
					action: "widget_images",
					pages_widgets_id: config.id,
					token: config.token,
					tag: config.tag,
					width: window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth
				}
			}).done(function (images) {
				if (!Array.isArray(images)) {
					status.text(config.loadError);
					console.error("ImageGallerySplide: unexpected image response", images);
					return;
				}
				if (!images.length) {
					status.text(config.empty);
					return;
				}
				if (typeof Splide !== "function") {
					status.text(config.loadError);
					console.error("ImageGallerySplide: Splide is not loaded");
					return;
				}

				var textDecoder = document.createElement("textarea");
				images.forEach(function (item, index) {
					["caption", "alt", "title", "creator", "copyright"].forEach(function (field) {
						textDecoder.innerHTML = item[field] || "";
						item[field] = textDecoder.value;
					});
					var imageCaption = item.caption || "";
					var rights = item.creator ? config.photo + " " + item.creator : (item.copyright ? "(" + item.copyright + ")" : "");
					var alt = item.alt || imageCaption;
					if (rights && alt) {
						alt += " / " + rights;
					}
					var slide = $("<li>").addClass("splide__slide").appendTo(list);
					$("<img>").attr({ src: item.filename, alt: alt, title: item.title || "" })
						.css({ width: "100%", height: "100%", objectFit: "cover", objectPosition: "center 38.2%" })
						.appendTo(slide);

					if (thumbnails.length) {
						var button = $("<button>").attr({
							type: "button",
							"aria-label": config.imageLabel + " " + (index + 1),
							"aria-pressed": "false"
						}).css({ padding: "2px 4px", border: 0, background: "transparent", cursor: "pointer" }).appendTo(thumbnails);
						$("<img>").attr({ src: item.filename, alt: "", title: imageCaption, width: 80 }).appendTo(button);
						button.on("click", function () { splide.go(index); });
					}
				});

				content.show();
				var splide = new Splide("#image_gallery_splide_" + config.id, {
					type: "fade",
					rewind: true,
					arrows: false,
					pagination: false,
					heightRatio: config.heightRatio,
					speed: config.speed,
					autoplay: config.timeout > 0,
					interval: config.timeout,
					pauseOnHover: false,
					pauseOnFocus: false
				});

				function update(index) {
					var item = images[index];
					var text = item.caption || "";
					var rights = item.creator ? config.photo + " " + item.creator : (item.copyright ? "(" + item.copyright + ")" : "");
					if (config.copyright && rights) {
						text += (text ? " / " : "") + rights;
					}
					caption.text(text);
					counter.text((index + 1) + " / " + images.length);
					thumbnails.children("button").attr("aria-pressed", "false").eq(index).attr("aria-pressed", "true");
				}

				splide.on("mounted move", function () { update(splide.index); });
				splide.mount();
				content.find("[data-gallery-action]").on("click", function () {
					switch ($(this).attr("data-gallery-action")) {
						case "previous": splide.go("<"); break;
						case "next": splide.go(">"); break;
						case "pause": splide.Components.Autoplay.pause(); break;
						case "play": splide.Components.Autoplay.play(); break;
					}
				});
			}).fail(function (xhr, textStatus, error) {
				status.text(config.loadError);
				console.error("ImageGallerySplide: image request failed", textStatus, error);
			});
		});
		</script>
		<?php
	}
}
