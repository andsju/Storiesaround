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



### Beksrivning av problem

Målet är att uppdatera kod i ett CMS från php version 7.4 till 8.3

4 varianter av buggar har identifierats enligt följande exempel: 

---

#### Bug "strlen"


```php
// bugg 
$title_value = strlen($row['story_custom_title_value']) > 0 ? $row['story_custom_title_value'] : $row['title'];

// fix
$title_value = strlen($row['story_custom_title_value'] ?? '') > 0 ? $row['story_custom_title_value'] : $row['title'];
```


#### Bug "VALIDATE_INT"

```php
// bugg
$story_promote = filter_input(INPUT_POST, 'story_promote', FILTER_VALIDATE_INT) ? $_POST['story_promote'] : 0;	

// fix
$story_promote = (int) filter_input(INPUT_POST, 'story_promote', FILTER_VALIDATE_INT);
```

#### Bug "valid datetime"

```php
// bugg
$story_event_date = isValidDateTime($_POST["story_event_datetime"]) ? date("Y-m-d H:i:s", strtotime($_POST["story_event_datetime"])) : null; 

// fix
$story_event_date = isValidDateTime($_POST["story_event_datetime"]) ? date("Y-m-d H:i:s", strtotime($_POST["story_event_datetime"])) : '1000-01-01 00:00:00';

```

#### Bug "trim null"

```php
// bugg
$story_content = trim($_POST['story_content']);

// fix
$story_content = trim($_POST['story_content'] ?? '');
```


---


Det kan finnas fler kod mönster som senare kan identifieras som bugg. Det ska inte ske nu.

Uppgift: analysera filer och avgör om kod ska justeras baserat på bugg exemplen ovan.

Analysera filen 
./app/public/cms/pages_edit_ajax.php

Skapa en tabell och ange kodrad där någon av de 4 exemplen av mönster ska justeras.  