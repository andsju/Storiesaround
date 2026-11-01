Strukturera stilmallar

Storiesaround CMS har stilmallar enligt följande:

Visa av sidor
./app/public/cms/pages.php

```php
$css_files = array(
    CMS_DIR . '/cms/libraries/jquery-ui/jquery-ui.css',
    CMS_DIR . '/cms/libraries/jquery-colorbox/colorbox.css',
    CMS_DIR . '/cms/libraries/jquery-flexnav/flexnav.css',
    CMS_DIR . '/cms/css/layout.css'
);
```

Redigera sidor
./app/public/cms/pages_edit.php

```php
$css_files = array(
	CMS_DIR . '/cms/css/normalize.css',
	CMS_DIR . '/cms/libraries/jquery-ui/jquery-ui.css',
	CMS_DIR . '/cms/css/layout.css',
	CMS_DIR . '/cms/libraries/jquery-colorbox/colorbox.css'
);
```

Administrera sidor
./app/public/cms/admin.php

```php
$css_files = array(
	CMS_DIR.'/cms/css/normalize.css', 
	CMS_DIR.'/cms/css/layout.css', 
	CMS_DIR.'/cms/css/pages_edit.css',
	CMS_DIR.'/cms/libraries/jquery-ui/jquery-ui.css', 
	CMS_DIR.'/cms/libraries/jquery-colorbox/colorbox.css',
	CMS_DIR.'/cms/libraries/jquery-datatables/style.css',
	CMS_DIR.'/cms/libraries/fileuploader/fileuploader.css'
);
```

Utöver det finns css tema filer implementerade för visning av sidor.

---


Uppgift 

Flytta adekvat innehåll från följande fil: 
/cms/css/layout.css
till
/cms/css/style.css


Det innebär att CSS selektorer och egenskaper som nu finns i filen layout.css som enbart används av pages.php ska flyttas till style.css

Filen pages.php inkluderar flera filer och ska fortsatt göra det.


Det finns en grundläggande struktur i style.css med media queries som ska följas
Mobile first ska vara gällande
Relativa enheter ska prioriteras 


Absoluta mått angivna i px ska justeras till enheten rem enligt följande:

Absoluta mått angivna i enheten px för 
- margin 
- padding

Ska justeras till enheten rem enligt följande:

1px => 1px
2-4px => 0.25rem
5-8px => 0.5rem
9-12px => 0.75rem
13-16px => 1rem

Efter flytt ska filen style.css användas istället för layout.css i filen pages.php 







user-toolbar
user_navigation
toolbar-icons



Bearbeta filen style.css

- ersätt enheten `em` med `rem`
- Använd följande överstättning mellan `px` och `rem`



1px    1px
2px    0.2rem
3px    0.2rem
4px    0.25rem
5px    0.3rem
6px    0.4rem
7px    0.5rem
8px    0.5rem
9px    0.5rem
10px   0.6rem
11px   0.7rem
12px   0.75rem
13px   0.8rem
14px   0.8rem
15px   1rem
16px   1rem

Fler exempel

20px   1.25rem
30px   1.8rem
32px   2rem