`$wrapper_content` anger sidans bredd med desktop - hanterar inte mobil

...ta bort nuvarande funktionalitet.

Möjlig lösning:

1. Ange default `$wrapper_content` width till desktop

`$wrapper_content = 1280`;

2. Sidan laddas första gången i en enhet
3. JavaScript avgör `div#wrapper_content` width
4. Sickas asynkront till backend
5. Backend anger bredd i sessions variabel
6. Ny request baserar html struktur via PHP med korrekt `$wrapper_content` width
7. Om klienten ändrar visning skickas ny asynkron backend `div#wrapper_content` width 

---


Ta bort från table site:
site_template_sidebar_width
site_template_content_padding


sizes:
100, 222, 474, 726, 1024, 1366, 1920




JavaScript client side: check

sidebar width
content width


---

Förenkla *stories* möjligheter då det gäller placering desktop vy -> endast radvis (ej kolumnvis)


show_widgets_content()          ...$wrapper_content_width

print_story_events/()           ...content_width
print__story__promoted()        ...sidebar_width
print__story__selected()        ...sidebar_percent_width   ...$this_width
print__story__child()           ...sidebar_width


hierarki

inc.template_sidebar_left.php
    print__story__promoted()
    
        inc.functions_pages.php
            print__story__promoted()
            get_box_story_content()
    
    print__story__selected()

        inc.functions_pages.php
            print__story__selected()
            get_box_story_content_selected()  =>  removed stories_columns

    print__story__child()

        inc.functions_pages.php
            print__story__child()
            get_box_story_content()

    print_story_events()

        inc.functions_pages.php
            print_story_events()
            get_box_story_events()          $column_width $stories_wide_teaser_image_width



---



Tips - hur TinyMCE lagrar data

```js
tinymce.init({
    selector: 'textarea',
    entity_encoding: 'raw'
});
```



## Uppdatera åäö

table: pages            field: content
table: pages_images     field: caption


```sql
UPDATE pages
SET content = REPLACE(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(content,
                        '&Aring;', 'Å'),
                    '&aring;', 'å'),
                '&Auml;', 'Ä'),
            '&auml;', 'ä'),
        '&Ouml;', 'Ö'),
    '&ouml;', 'ö')
WHERE content LIKE '%&Aring;%'
   OR content LIKE '%&aring;%'
   OR content LIKE '%&Auml;%'
   OR content LIKE '%&auml;%'
   OR content LIKE '%&Ouml;%'
   OR content LIKE '%&ouml;%';

UPDATE pages_images
SET caption = REPLACE(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(caption,
                        '&Aring;', 'Å'),
                    '&aring;', 'å'),
                '&Auml;', 'Ä'),
            '&auml;', 'ä'),
        '&Ouml;', 'Ö'),
    '&ouml;', 'ö')
WHERE caption LIKE '%&Aring;%'
   OR caption LIKE '%&aring;%'
   OR caption LIKE '%&Auml;%'
   OR caption LIKE '%&auml;%'
   OR caption LIKE '%&Ouml;%'
   OR caption LIKE '%&ouml;%';   
```



Problembeskrivning

Projektet dirigerar nu automatiskt till https
Jag vill under utveckling av kod kunna använda http 

Vilka filer påverkar nu vilket protokoll som är aktivt
Hur kan jag på ett enkelt sätt konfigurera vilket protokoll som ska gälla

Analysera koden och svara först vilka alternativ som finns

---


Jag startade om docker projektet och försökte accessa applikationen med http

`http://localhost:8092/`

En redirect i applikationen blev responsen och error connection refused, status 302

`http://localhost/cms`



---


I CMS Storiesaround anges header content enligt följande fält i tabellen `pages`

```php
  `header_image` varchar(255) DEFAULT '[]' COMMENT 'Set static header image',
  `header_caption` varchar(255) DEFAULT '[]' COMMENT 'header image caption',
  `header_caption_align` varchar(255) DEFAULT '[]' COMMENT 'left, center, right',
  `header_caption_vertical_align` varchar(255) DEFAULT '[]' COMMENT 'top, middle, bottom',
  `header_caption_show` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'show header image caption',
  `header_image_timeout` int(5) UNSIGNED NOT NULL DEFAULT 10000 COMMENT 'header image timeout',
  `parallax_scroll` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0:none, 1:scroll',
  `header_image_fade` varchar(25) DEFAULT 'normal' COMMENT 'header image fade',
```

Strukturen ska förenklas och förändras så att data lagras i JSON format enligt följande

```php
    `header` varchar(1000) DEFAULT '[]' COMMENT 'Set header content'
```

Uppgift

Föreslå förändring av php kod:
- hur objekt av header content kan lagras i JSON format
- hur informationen kan visas vid redigering: 
    ./app/public/cms/pages_edit.php  (implementerad rad 2810-2860)
- hur informationen kan visas vid visning: 
    ./app/public/cms/includes/inc.site_header_image.php inkluderad ./app/public/cms/pages.php  (implementerad rad 282)


Förändra INTE nuvarande filer
Skapa kodförslag och presentera i första steget:
- visning i ./app/public/cms/includes/inc.site_header_image.php


Uppgift
Skapa kodförslag och presentera i andra steget:
- hur informationen kan visas vid redigering: 
    ./app/public/cms/pages_edit.php  (implementerad rad 2810-2860)

I UI används jQuery för att välja header content och koppla content till caption. 

./app/public/cms/pages_edit.php  (implementerad ca rad 856-972)







json lagring i databas

```json
{
  "items": [
    {
      "file": "header-1.jpg",
      "caption": "Första bildens text",
      "align": "center",
      "vertical_align": "middle"
    },
    {
      "file": "header-2.mp4",
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



pages.php

```php
<?php if (!defined('VALID_INCL')) { die(); } ?>

<?php
$header = json_decode($arr['header'], true);

if (!is_array($header)) {
    $header = array();
}

$header_items = isset($header['items']) && is_array($header['items'])
    ? $header['items']
    : array();

$caption_show = !empty($header['caption_show']);

$allowed_extensions = array('jpg', 'jpeg', 'gif', 'png', 'mp4');
?>

<div id="site-header-image" class="slideshow-cycle-wrapper parallax">

    <?php if (count($header_items) > 0) { ?>

        <?php foreach ($header_items as $item) { ?>

            <?php
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
            ?>

            <?php if ($extension === 'mp4') { ?>

                <video
                    class="slideshow-cycle-image"
                    data-caption="<?= $caption_html ?>"
                    data-caption-align="<?= $caption_align_html ?>"
                    data-caption-vertical-align="<?= $caption_vertical_align_html ?>"
                    autoplay
                    loop
                    muted
                    playsinline>
                    <source src="<?= $file_url_html ?>" type="video/mp4">
                </video>

            <?php } else { ?>

                <img
                    src="<?= $file_url_html ?>"
                    alt="<?= $caption_html ?>"
                    data-caption="<?= $caption_html ?>"
                    data-caption-align="<?= $caption_align_html ?>"
                    data-caption-vertical-align="<?= $caption_vertical_align_html ?>"
                    class="slideshow-cycle-image">

            <?php } ?>

        <?php } ?>

    <?php } else { ?>

        <?php
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
                ?>

                <img
                    src="<?= htmlspecialchars($fallback_url, ENT_QUOTES, 'UTF-8') ?>"
                    alt=""
                    class="slideshow-cycle-image">

                <?php
            }
        }
        ?>

    <?php } ?>

</div>

<div id="site-header-caption"></div>
```




pages_edit.php

```php

<?php
$header_image = json_decode($arr['header_image']);
$header_caption = json_decode($arr['header_caption']);
$header_caption_align = json_decode($arr['header_caption_align']);
$header_caption_vertical_align = json_decode($arr['header_caption_vertical_align']);

```


```php
<?php
$header = json_decode($arr['header'], true);

if (!is_array($header)) {
    $header = array();
}

$header_items = isset($header['items']) && is_array($header['items'])
    ? $header['items']
    : array();

$selected_files = array();

foreach ($header_items as $header_item) {
    if (is_array($header_item) && !empty($header_item['file'])) {
        $selected_files[] = $header_item['file'];
    }
}
```


```php
<?php
<div id="directory_view_slides">
<?php foreach ($header_items as $header_item) { ?>

    <?php
    if (!is_array($header_item) || empty($header_item['file'])) {
        continue;
    }

    $file = basename($header_item['file']);
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    $caption = isset($header_item['caption'])
        ? $header_item['caption']
        : '';

    $align = isset($header_item['align'])
        ? $header_item['align']
        : 'center';

    $vertical_align = isset($header_item['vertical_align'])
        ? $header_item['vertical_align']
        : 'middle';

    $caption_html = htmlspecialchars(
        $caption,
        ENT_QUOTES,
        'UTF-8'
    );

    $file_html = htmlspecialchars(
        $file,
        ENT_QUOTES,
        'UTF-8'
    );

    $align_value = getCaptionAlignAsInteger($align);
    $vertical_align_value = getCaptionVerticalAlignAsInteger($vertical_align);

    $is_video = $extension === 'mp4';
    ?>

    <?php if ($is_video) { ?>

        <div
            data-image="<?= $file_html ?>"
            class="header-images">

            <video
                style="width:100%; max-height:100px"
                controls
                muted>
                <source
                    src="../content/uploads/header/<?= rawurlencode($file) ?>"
                    type="video/mp4">
            </video>

            <textarea
                name="header_caption[]"
                maxlength="100"><?= $caption_html ?></textarea>

            <input
                type="range"
                name="header_caption_align[]"
                class="header_caption_align"
                value="<?= $align_value ?>"
                min="0"
                max="2">

            <input
                type="range"
                name="header_caption_vertical_align[]"
                class="header_caption_vertical_align"
                value="<?= $vertical_align_value ?>"
                min="0"
                max="2">
        </div>

    <?php } else { ?>

        <div
            data-image="<?= $file_html ?>"
            class="header-images"
            style="background-image:url('../content/uploads/header/<?= rawurlencode($file) ?>');">

            <textarea
                name="header_caption[]"
                maxlength="100"><?= $caption_html ?></textarea>

            <input
                type="range"
                name="header_caption_align[]"
                class="header_caption_align"
                value="<?= $align_value ?>"
                min="0"
                max="2">

            <input
                type="range"
                name="header_caption_vertical_align[]"
                class="header_caption_vertical_align"
                value="<?= $vertical_align_value ?>"
                min="0"
                max="2">
        </div>

    <?php } ?>

<?php } ?>
</div>

```


```js
var headerItems = [];

$("#directory_view_slides .header-images").each(function () {
    var item = $(this);
    var alignValue = item.find(".header_caption_align").val();
    var verticalAlignValue = item.find(".header_caption_vertical_align").val();

    var align = "center";
    if (alignValue == 0) {
        align = "left";
    } else if (alignValue == 2) {
        align = "right";
    }

    var verticalAlign = "middle";
    if (verticalAlignValue == 0) {
        verticalAlign = "top";
    } else if (verticalAlignValue == 2) {
        verticalAlign = "bottom";
    }

    headerItems.push({
        file: item.attr("data-image"),
        caption: item.find("textarea[name='header_caption[]']").val(),
        align: align,
        vertical_align: verticalAlign
    });
});

```

```js
var header = {
    items: headerItems,
    caption_show: $("input:checkbox[name=header_caption_show]").is(":checked"),
    image_timeout: parseInt($("#header_image_timeout").val(), 10),
    parallax_scroll: $("input:checkbox[name=parallax_scroll]").is(":checked"),
    image_fade: $("#header_image_fade").val()
};
```


```js
data: {
    action: action,
    token: token,
    users_id: users_id,
    pages_id: pages_id,
    header: JSON.stringify(header),
    landing_page: $("input:checkbox[name=landing_page]").is(":checked")
        ? 1
        : 0
}
```


```js

function createHeaderItem(filename, isVideo) {
    var item = $("<div>", {
        "class": "header-images",
        "data-image": filename
    });

    if (isVideo) {
        var video = $("<video>", {
            "style": "width:100%;max-height:100px",
            "controls": true,
            "muted": true
        });

        video.append($("<source>", {
            "src": "../content/uploads/header/" + encodeURIComponent(filename),
            "type": "video/mp4"
        }));

        item.append(video);
    } else {
        item.css(
            "background-image",
            "url('../content/uploads/header/" + encodeURIComponent(filename) + "')"
        );
    }

    item.append($("<textarea>", {
        "name": "header_caption[]",
        "maxlength": 100
    }));

    item.append($("<input>", {
        "type": "range",
        "name": "header_caption_align[]",
        "class": "header_caption_align",
        "min": 0,
        "max": 2,
        "value": 1
    }));

    item.append($("<input>", {
        "type": "range",
        "name": "header_caption_vertical_align[]",
        "class": "header_caption_vertical_align",
        "min": 0,
        "max": 2,
        "value": 1
    }));

    return item;
}
```

```js
$("#directory_view").delegate(".image_mark", "click", function () {
    var checkbox = $(this);
    var filename = checkbox.attr("data-file");
    var isVideo = checkbox.attr("data-video") === "true";
    var existingItem = null;

    $("#directory_view_slides .header-images").each(function () {
        if ($(this).attr("data-image") === filename) {
            existingItem = $(this);
        }
    });

    if (!checkbox.is(":checked")) {
        if (existingItem !== null) {
            existingItem.remove();
        }

        return;
    }

    if (existingItem === null) {
        $("#directory_view_slides").append(
            createHeaderItem(filename, isVideo)
        );
    }
});
```


Läs in

```php
<?php
$header = json_decode($arr['header'], true);

if (!is_array($header)) {
    $header = array();
}

$header_items = isset($header['items']) && is_array($header['items'])
    ? $header['items']
    : array();

$selected_files = array();

foreach ($header_items as $header_item) {
    if (is_array($header_item) && !empty($header_item['file'])) {
        $selected_files[] = $header_item['file'];
    }
}


<?php
$header_caption_show = !empty($header['caption_show']) ? 1 : 0;
$header_image_timeout = isset($header['image_timeout'])
    ? (int) $header['image_timeout']
    : 10000;
$header_image_fade = isset($header['image_fade'])
    ? $header['image_fade']
    : 'normal';
$parallax_scroll = !empty($header['parallax_scroll']) ? 1 : 0;
```







		Koden är implementerad backend med PHP
		Det fungerar att klicka och ange header bilder i div#directory_view och visa i div#directory_view_slides

		Felsök varför inte `headerItems` finns korrekt angiven när data skickas backend

		Analysera filerna
		- ./app/public/cms/pages_edit.php 			rad 985-1100
		- ./app/public/cms/pages_edit_ajax.php 		rad 960-975
		- .app/public/cms/classes/Pages.php 		rad 1350-1375


