

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

Jag försöker förbättra en sökfunktion i ett CMS. Analysera koden nedan för att avgöra om version 2 är en förbättring av version 1. Målet är att förhindra SQL injections och undvika intensiva SQL bearbetningar.

Analyser metoderna. Förklara och sammanställ 
- förbättringar
- försämringar
- förslag till ny version (endast om det är av stor vikt)


Version 1:

```php

    /**
     * @param string $search
     * @param int $status
     * @param int $pages_id
     * @param int $limit_tree
     * @param int $limit_start
     * @param int $limit
     * @return array
     */
    public function getPagesSearchWordsRelevance($search, $status, $pages_id, $limit_tree, $limit_start, $limit)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        $limit_start = $limit_start;
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $titles = implode(' AND title LIKE ', $query_parts);
        $contents = implode(' AND content LIKE ', $query_parts);
        $story_contents = implode(' AND story_content LIKE ', $query_parts);
        $grid_content = implode(' AND grid_content LIKE ', $query_parts);
        $tags = implode(' AND tag LIKE ', $query_parts);
        $pages_id_links = implode(' AND pages_id_link LIKE ', $query_parts);

        $ws = implode(' ', $words);
        $ws = trim($ws);

        $sql = "SELECT pages_id, title, content, category, tag, status, access, parent, utc_start_publish, utc_end_publish, utc_modified,  
		MATCH(title,content,story_content,grid_content,tag,pages_id_link) AGAINST('($ws)' IN NATURAL LANGUAGE MODE) AS relevance
		FROM pages 
		WHERE 
		( MATCH(title,content,story_content,grid_content,tag,pages_id_link) AGAINST('($ws)' IN NATURAL LANGUAGE MODE)
			OR title LIKE {$titles}
			OR content LIKE {$contents} 
			OR story_content LIKE {$story_contents} 
            OR grid_content LIKE {$grid_content} 
			OR tag LIKE {$tags} 
            OR pages_id_link LIKE {$pages_id_links} 
		)";

        if ($status > 0) {
            $sql .= " AND status = $status ";
        }
        if ($limit_tree > 0) {
            $sql .= " AND parent_id = $pages_id ";
        }

        $sql .= " ORDER BY ";
        $sql .= " category_position ASC, ";
        $sql .= " title LIKE {$titles} DESC, ";
        $sql .= " relevance DESC, ";
        $sql .= " tag LIKE {$tags} DESC ";
        $sql .= " LIMIT $limit_start, $limit";

        $stmt = $this->db->prepare($sql);
        // $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        // $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        // $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        // $stmt->bindParam(':limit_tree', $limit_tree, PDO::PARAM_INT);
        // $stmt->bindParam(':limit_start', $limit_start, PDO::PARAM_INT);
        // $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

```

Version 2:

```php
    /**
     * @param string $search
     * @param int $status
     * @param int $pages_id
     * @param int $limit_tree
     * @param int $limit_start
     * @param int $limit
     * @return array
     */
    public function getPagesSearchWordsRelevance($search, $status, $pages_id, $limit_tree, $limit_start, $limit)
    {
        // enforce numeric types for every value concatenated into the query below
        $status = (int) $status;
        $pages_id = (int) $pages_id;
        $limit_tree = (int) $limit_tree;
        $limit_start = (int) $limit_start;
        $limit = (int) $limit;

        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            // quote() escapes and adds surrounding quotes, protects against SQL injection
            $query_parts[] = $this->db->quote('%' . $word . '%');
        }
        $titles = implode(' AND title LIKE ', $query_parts);
        $contents = implode(' AND content LIKE ', $query_parts);
        $story_contents = implode(' AND story_content LIKE ', $query_parts);
        $grid_content = implode(' AND grid_content LIKE ', $query_parts);
        $tags = implode(' AND tag LIKE ', $query_parts);
        $pages_id_links = implode(' AND pages_id_link LIKE ', $query_parts);

        $ws = implode(' ', $words);
        $ws = trim($ws);
        $ws_quoted = $this->db->quote('(' . $ws . ')');

        $sql = "SELECT pages_id, title, content, category, tag, status, access, parent, utc_start_publish, utc_end_publish, utc_modified,  
		MATCH(title,content,story_content,grid_content,tag,pages_id_link) AGAINST($ws_quoted IN NATURAL LANGUAGE MODE) AS relevance
		FROM pages 
		WHERE 
		( MATCH(title,content,story_content,grid_content,tag,pages_id_link) AGAINST($ws_quoted IN NATURAL LANGUAGE MODE)
			OR title LIKE {$titles}
			OR content LIKE {$contents} 
			OR story_content LIKE {$story_contents} 
            OR grid_content LIKE {$grid_content} 
			OR tag LIKE {$tags} 
            OR pages_id_link LIKE {$pages_id_links} 
		)";

        if ($status > 0) {
            $sql .= " AND status = :status ";
        }
        if ($limit_tree > 0) {
            $sql .= " AND parent_id = :pages_id ";
        }

        $sql .= " ORDER BY ";
        $sql .= " category_position ASC, ";
        $sql .= " title LIKE {$titles} DESC, ";
        $sql .= " relevance DESC, ";
        $sql .= " tag LIKE {$tags} DESC ";
        $sql .= " LIMIT :limit_start, :limit";

        $stmt = $this->db->prepare($sql);
        if ($status > 0) {
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        }
        if ($limit_tree > 0) {
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        }
        $stmt->bindParam(':limit_start', $limit_start, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
```


$pages_id = isset($_POST['pages_id']) ? trim($_POST['pages_id']) : 0;

$pages_id = trim($_POST['pages_id'] ?? '0');
---


Det kan finnas fler kod mönster som kan identifieras som buggar.

Uppgift: analysera filer och avgör om kod ska justeras baserat på bugg exemplen ovan.

Analysera filen 
./app/public/cms/admin_edit_ajax.php

Skapa en tabell och ange kodrad där någon av de 4 exemplen av mönster ska justeras.  

I uppdatering av kod kan följande fil användas för korrigering:
./tools/fix_php_bugs.py


ANalysera filen calendar_edit_ajax.php och kontrollera resik för SQO injections

				$s = $_POST['s'];
				$rows = $cal->getCalendarViewsSearch($s);
				echo json_encode($rows);





```php

Vissa fält ska endast spara text.

Är följande funktion rätt:

function sanitize_text($input)
{
	return striptags(trim($input ?? ''));
}



function sanitize_text(mixed $input): string 
{ 
	if (!is_string($input)) { 
		return ''; 
	} 
		return trim(strip_tags($input) ?? ''); 
	}
```

Föreslå utökning av funktionen så att olika citattecken hanteras korrekt. 

- O'Reilly
- Ingrid Marie "Pippin"


Lägg till return kod i de 2 sista funktionerna 


```php
/**
 * strip tags
 * @param $input
 * @return string
 */
function sanitize_text(mixed $input): string
{
    if (!is_string($input)) {
        return '';
    }
    return trim(strip_tags($input));
}

/**
 * allow tags but not script
 * @param $input
 * @return string
 */
function sanitize_html(mixed $input): string
{
    if (!is_string($input)) {
        return '';
    }

	// kod
    return;
}

/**
 * allow some tags
 * @param $input
 * @param $allow_tags
 * @return string
 */
function sanitize_html_tags(mixed $input, mixed $allow_tags): string
{
    if (!is_string($input)) {
        return '';
    }

	// kod
    return;
}



```


Analysera varför data i databasens tabeller skrivs med ersätningstecken för svenska tecken åäöÅÄÖ

Följande lagras i tabellen:

S&ouml;rml&auml;ndska &auml;pplen

Jag skriver in 

Sörmländska äpplen



Analysera följande sql fråga:

```sql
CREATE TABLE `calendar_categories` (
  `calendar_categories_id` int(11) NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(4) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `public` tinyint(1) NOT NULL DEFAULT 0,
  `rss` tinyint(1) DEFAULT NULL COMMENT 'rss feed',
  `utc_created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=MYISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

Kan jag förenkla genom att ange följande:

```sql
CREATE TABLE `calendar_categories` (
  `calendar_categories_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `position` int(4) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `public` tinyint(1) NOT NULL DEFAULT 0,
  `rss` tinyint(1) DEFAULT NULL COMMENT 'rss feed',
  `utc_created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `utc_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00'
) ENGINE=MYISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```


Visa slutligen generellt hur en tabell kan uppdateras med `ALTER` komando från att vara baserad på:
`ENGINE=MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci`
till:
`ENGINE=MYISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`

Samtliga fält i tabellen ska uppdateras


ALTER TABLE `table_name`
  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


Hur konverteras en hel databas konverteras från MyISAM till InnoDB