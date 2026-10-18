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