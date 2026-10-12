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