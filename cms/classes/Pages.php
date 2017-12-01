<?php

/**
 * Class Pages
 */
class Pages extends Database
{

    /**
     * Pages constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     * @param string $title
     * @param int $parent_id
     * @param int $parent
     * @param int $position
     * @param int $access
     * @param int $status
     * @param int $template
     * @param string $utc_modified
     * @return integer
     */
    public function setPagesAddToplevelPage($title, $parent_id, $parent, $position, $access, $status, $template, $utc_modified)
    {
        try {
            $sql_insert = "INSERT INTO pages 
			(title, parent_id, parent, position, access, status, template, utc_modified) VALUES
			(:title, :parent_id, :parent, :position, :access, :status, :template, :utc_modified)";

            $stmt = $this->db->prepare($sql_insert);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
            $stmt->bindParam(':parent', $parent, PDO::PARAM_INT);
            $stmt->bindParam(':position', $position, PDO::PARAM_INT);
            $stmt->bindParam(':access', $access, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':template', $template, PDO::PARAM_INT);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('pages_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param string $title
     * @param $parent_id
     * @param $parent
     * @param $position
     * @param $access
     * @param $status
     * @param string $utc_modified
     * @param string $meta_additional
     * @param string $meta_robots
     * @param string $tag
     * @param string $ads_filter
     * @param string $stories_filter
     * @param string $selections
     * @param int $header
     * @param int $template
     * @param int $ads
     * @param $ads_limit
     * @param int $stories_columns
     * @return integer
     */
    public function setPagesAddChildPage($title, $parent_id, $parent, $position, $access, $status, $utc_modified, $meta_additional, $meta_robots, $tag, $ads_filter, $stories_filter, $selections, $header, $template, $ads, $ads_limit, $stories_columns)
    {
        try {
            $sql_insert = "INSERT INTO pages 
			(title, parent_id, parent, position, access, status, utc_modified, meta_additional, meta_robots, tag, ads_filter, stories_filter, selections, header, template, ads, ads_limit, stories_columns) VALUES
			(:title, :parent_id, :parent, :position, :access, :status, :utc_modified, :meta_additional, :meta_robots, :tag, :ads_filter, :stories_filter, :selections, :header, :template, :ads, :ads_limit, :stories_columns)";

            $stmt = $this->db->prepare($sql_insert);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
            $stmt->bindParam(':parent', $parent, PDO::PARAM_INT);
            $stmt->bindParam(':position', $position, PDO::PARAM_INT);
            $stmt->bindParam(':access', $access, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            $stmt->bindParam(':meta_additional', $meta_additional, PDO::PARAM_STR);
            $stmt->bindParam(':meta_robots', $meta_robots, PDO::PARAM_STR);
            $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
            $stmt->bindParam(':ads_filter', $ads_filter, PDO::PARAM_STR);
            $stmt->bindParam(':stories_filter', $stories_filter, PDO::PARAM_STR);
            $stmt->bindParam(':selections', $selections, PDO::PARAM_STR);
            $stmt->bindParam(':header', $header, PDO::PARAM_INT);
            $stmt->bindParam(':template', $template, PDO::PARAM_INT);
            $stmt->bindParam(':ads', $ads, PDO::PARAM_INT);
            $stmt->bindParam(':ads_limit', $ads_limit, PDO::PARAM_INT);
            $stmt->bindParam(':stories_columns', $stories_columns, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId('pages_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @return bool
     * check status / parent / folder / related tables before using this
     */
    public function deleteSelectedPage($pages_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages WHERE pages_id =:pages_id");
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $status
     * @param int $access
     * @param string $title_tag
     * @param string $datetime_start
     * @param string $datetime_end
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesPublish($pages_id, $status, $access, $title_tag, $datetime_start, $datetime_end, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET status = :status,
			access = :access,
			title_tag = :title_tag,
			utc_start_publish = :utc_start_publish,
			utc_end_publish = :utc_end_publish,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_INT);
            $stmt->bindParam(":access", $access, PDO::PARAM_INT);
            $stmt->bindParam(":title_tag", $title_tag, PDO::PARAM_STR);
            $stmt->bindParam(":utc_start_publish", $datetime_start, PDO::PARAM_STR);
            $stmt->bindParam(":utc_end_publish", $datetime_end, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $status
     * @param string $datetime_start
     * @param string $datetime_end
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesStatus($pages_id, $status, $datetime_start, $datetime_end, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET status = :status,
			utc_start_publish = :utc_start_publish,
			utc_end_publish = :utc_end_publish,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_INT);
            $stmt->bindParam(":utc_start_publish", $datetime_start, PDO::PARAM_STR);
            $stmt->bindParam(":utc_end_publish", $datetime_end, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $pages_id
     * @param int $status
     * @param int $access
     * @param string $datetime_start
     * @param string $datetime_end
     * @return bool
     */
    public function setPagesBulkStatus($pages_id, $status, $access, $datetime_start, $datetime_end)
    {
        try {
            $sql_update = "UPDATE pages
			SET status = :status,
			access = :access,
			utc_start_publish = :utc_start_publish,
			utc_end_publish = :utc_end_publish
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_INT);
            $stmt->bindParam(":access", $access, PDO::PARAM_INT);
            $stmt->bindParam(":utc_start_publish", $datetime_start, PDO::PARAM_STR);
            $stmt->bindParam(":utc_end_publish", $datetime_end, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param string $header_image
     * @return bool
     */
    public function setPagesBulkHeaderImage($pages_id, $header_image)
    {
        try {
            $sql_update = "UPDATE pages
			SET header = :header_image
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":header_image", $header_image, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $pages_id
     * @param int $parent
     * @return bool
     */
    public function updatePagesIsParent($pages_id, $parent)
    {
        try {
            $stmt = $this->db->prepare("UPDATE pages SET parent =:parent WHERE pages_id =:pages_id");
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":parent", $parent, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }



    /**
     * @param int $pages_id
     * @param int $grid_active
     * @param int $grid_area
     * @param string $grid_custom_classes
     * @param string $grid_content     
     * @param int $grid_cell_template
     * @param int $grid_cell_image_height
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesGrid($pages_id, $grid_active, $grid_area, $grid_custom_classes, $grid_content, $grid_cell_template, $grid_cell_image_height, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET grid_active = :grid_active,
			grid_area = :grid_area,
            grid_custom_classes = :grid_custom_classes,
            grid_content = :grid_content,
            grid_cell_template = :grid_cell_template,
            grid_cell_image_height = :grid_cell_image_height,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":grid_active", $grid_active, PDO::PARAM_INT);
            $stmt->bindParam(":grid_area", $grid_area, PDO::PARAM_INT);
            $stmt->bindParam(":grid_custom_classes", $grid_custom_classes, PDO::PARAM_STR);
            $stmt->bindParam(":grid_content", $grid_content, PDO::PARAM_STR);
            $stmt->bindParam(":grid_cell_template", $grid_cell_template, PDO::PARAM_INT);
            $stmt->bindParam(":grid_cell_image_height", $grid_cell_image_height, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }





    /**
     * @param int $pages_id
     * @param int $ads
     * @param int $ads_limit
     * @param string $ads_filter
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesAds($pages_id, $ads, $ads_limit, $ads_filter, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET ads = :ads,
			ads_limit = :ads_limit,
			ads_filter = :ads_filter,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":ads", $ads, PDO::PARAM_STR);
            $stmt->bindParam(":ads_limit", $ads_limit, PDO::PARAM_INT);
            $stmt->bindParam(":ads_filter", $ads_filter, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param string $story_content
     * @param string $story_wide_content
     * @param string $tag
     * @param int $story_promote
     * @param int $story_link
     * @param int $story_event
     * @param string $story_event_date
     * @param string $story_css_class
     * @param string $story_custom_title
     * @param string $story_custom_title_value
     * @param int $story_wide_teaser_image
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesStory($pages_id, $story_content, $story_wide_content, $tag, $story_promote, $story_link, $story_event, $story_event_date, $story_css_class, $story_custom_title, $story_custom_title_value, $story_wide_teaser_image, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET story_content = :story_content,
			story_wide_content = :story_wide_content,
			tag = :tag,
			story_promote = :story_promote,
			story_link = :story_link,
			story_event = :story_event,
			story_event_date = :story_event_date,
			story_css_class = :story_css_class,
			story_custom_title = :story_custom_title,
			story_custom_title_value = :story_custom_title_value,
			story_wide_teaser_image = :story_wide_teaser_image,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":story_content", $story_content, PDO::PARAM_STR);
            $stmt->bindParam(":story_wide_content", $story_wide_content, PDO::PARAM_STR);
            $stmt->bindParam(":tag", $tag, PDO::PARAM_STR);
            $stmt->bindParam(":story_promote", $story_promote, PDO::PARAM_INT);
            $stmt->bindParam(":story_link", $story_link, PDO::PARAM_INT);
            $stmt->bindParam(":story_event", $story_event, PDO::PARAM_INT);
            $stmt->bindParam(":story_event_date", $story_event_date, PDO::PARAM_STR);
            $stmt->bindParam(":story_css_class", $story_css_class, PDO::PARAM_STR);
            $stmt->bindParam(":story_custom_title", $story_custom_title, PDO::PARAM_INT);
            $stmt->bindParam(":story_custom_title_value", $story_custom_title_value, PDO::PARAM_STR);
            $stmt->bindParam(":story_wide_teaser_image", $story_wide_teaser_image, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function getPagesContent($id)
    {
        $sql = "SELECT * 
		FROM pages 
		WHERE pages_id = :pages_id
		AND status = 2  
		AND (SELECT NOW() BETWEEN utc_start_publish AND utc_end_publish 
		OR NOW() > utc_start_publish AND utc_end_publish IS NULL)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getPagesContentPreview($id)
    {
        $sql = "SELECT * 
		FROM pages 
		WHERE pages_id = :pages_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @return array
     */
    public function getPagesColumns()
    {
        $sql = "SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name='pages'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function getPagesEditContent($id)
    {
        $sql = "SELECT * 
		FROM pages 
		WHERE pages_id = :pages_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $pages_id_link
     * @return mixed
     */
    public function getPagesSeo($pages_id_link)
    {
        $sql = "SELECT pages_id_link, pages_id
		FROM pages 
		WHERE pages_id_link = :pages_id_link";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id_link', $pages_id_link, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $pages_id_link
     * @param int $pages_id
     * @return mixed
     */
    public function checkPagesSeo($pages_id_link, $pages_id)
    {
        $sql = "SELECT pages_id_link
		FROM pages 
		WHERE pages_id_link = :pages_id_link
		AND pages_id != :pages_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->bindParam(':pages_id_link', $pages_id_link, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $parent_id
     * @return array
     */
    public function getPagesNode($parent_id)
    {
        $sql = "SELECT pages_id, title 
		FROM pages 
		WHERE parent_id = :parent_id
		ORDER BY position ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @return array
     */
    public function getPagesSearch($search)
    {
        $sql = "SELECT pages_id, title, status  
		FROM pages 
		WHERE title LIKE :search
		LIMIT 50";

        // apply percentages to search string
        $search = "%" . $search . "%";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @return array
     */
    public function getPagesSearchExtended($search)
    {
        $sql = "SELECT pages_id, title, content, utc_modified  
		FROM pages 
		WHERE 
		( title LIKE :search 
		OR content LIKE :search
		OR story_content LIKE :search
		OR story_wide_content LIKE :search )
		AND status = 2 
		LIMIT 100";

        // apply percentages to search string
        $search = "%" . $search . "%";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @param int $status
     * @return array
     */
    public function getPagesSearchWords($search, $status)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $titles = implode(' OR title LIKE ', $query_parts);
        $contents = implode(' OR content LIKE ', $query_parts);
        $story_contents = implode(' OR story_content LIKE ', $query_parts);
        $story_wide_contents = implode(' OR story_wide_content LIKE ', $query_parts);
        $tags = implode(' OR tag LIKE ', $query_parts);

        $sql = "SELECT pages_id, title, tag, status, access, parent, utc_start_publish, utc_end_publish, utc_modified  
		FROM pages 
		WHERE (
		title LIKE {$titles}
		OR content LIKE {$contents} 
		OR story_content LIKE {$story_contents} 
		OR story_wide_content LIKE {$story_wide_contents} 
		OR tag LIKE {$tags} 
		) ";

        if ($status > 0) {
            $sql .= " AND status = $status ";
        }
        $sql .= " LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @param int $status
     * @return array
     */
    public function getPagesSearchWordsRelevance($search, $status)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $titles = implode(' AND title LIKE ', $query_parts);
        $contents = implode(' AND content LIKE ', $query_parts);
        $story_contents = implode(' AND story_content LIKE ', $query_parts);
        $story_wide_contents = implode(' AND story_wide_content LIKE ', $query_parts);
        $tags = implode(' AND tag LIKE ', $query_parts);

        $ws = implode(' ', $words);
        $ws = trim($ws);

        $sql = "SELECT pages_id, title, content, tag, status, access, parent, utc_start_publish, utc_end_publish, utc_modified,  
		MATCH(title,content,story_content,story_wide_content,tag) AGAINST('($ws)' IN NATURAL LANGUAGE MODE) AS relevance
		FROM pages 
		WHERE 
		( MATCH(title,content,story_content,story_wide_content,tag) AGAINST('($ws)' IN NATURAL LANGUAGE MODE)
			OR title LIKE {$titles}
			OR content LIKE {$contents} 
			OR story_content LIKE {$story_contents} 
			OR story_wide_content LIKE {$story_wide_contents} 
			OR tag LIKE {$tags} 
		)
		";

        if ($status > 0) {
            $sql .= " AND status = $status ";
        }

        $sql .= " ORDER BY ";
        $sql .= " title LIKE {$titles} DESC, ";
        $sql .= " relevance DESC, ";
        $sql .= " tag LIKE {$tags} DESC ";
        $sql .= " LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @return array
     */
    public function getPagesSearchTag($search)
    {
        $sql = "SELECT pages_id, title, tag  
		FROM pages 
		WHERE tag LIKE :search
		LIMIT 50";

        // apply percentages to search string
        $search = "%" . $search . "%";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @return array
     */
    public function getPagesTag($search)
    {
        $query_parts = array();
        $tags = explode(",", $search);
        foreach ($tags as $tag) {
            $query_parts[] = "'%" . $tag . "%'";
        }

        $string = implode(' OR tag LIKE ', $query_parts);

        $sql = "SELECT pages_id, title, tag  
		FROM pages 
		WHERE tag LIKE {$string}
		LIMIT 50";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getPagesPublished()
    {
        $sql = "SELECT pages_id, title, utc_created, utc_modified  
		FROM pages 
		WHERE status = 2 
		AND ( (SELECT NOW() BETWEEN utc_start_publish AND utc_end_publish
		OR NOW() > utc_start_publish AND utc_end_publish IS NULL) )
		LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     *
     * @return array
     */
    public function getPagesPending()
    {
        $sql = "SELECT pages_id, title, utc_created, utc_modified  
		FROM pages 
		WHERE status = 4
		LIMIT 100";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $status
     * @return array
     */
    public function getPagesRecent($status)
    {
        $sql = "SELECT pages_id, title, utc_created, utc_modified  
		FROM pages 
		WHERE utc_modified > DATE_SUB( NOW(), INTERVAL 2 DAY)
		AND status = :status
		ORDER BY utc_modified DESC
		LIMIT 20";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     *
     * @return array
     */
    public function getPagesRecentModified()
    {
        $sql = "SELECT pages_id, title, utc_created, utc_modified  
		FROM pages 
		WHERE utc_modified > DATE_SUB( NOW(), INTERVAL 2 DAY)
		ORDER BY utc_modified DESC
		LIMIT 20";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param array $path
     * @param $seo
     * @param $script
     * @return string
     */
    public function getPagesRoot(array $path, $seo, $script)
    {
        $sql = "SELECT pages_id, pages_id_link, title, access 
		FROM pages 
		WHERE parent_id = 0";
        if (!isset($_SESSION['users_id'])) {
            $sql .= " AND access = 2";
        }
        $sql .= " AND (SELECT NOW() BETWEEN utc_start_publish AND utc_end_publish";
        $sql .= " OR NOW() > utc_start_publish AND utc_end_publish IS NULL) ";
        $sql .= " ORDER BY position asc";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        $html = '';
        if (count($rows)) {

            $html .= "\n<ul id=\"navigation-root\">";
            foreach ($rows as $row) {
                $icon = $row['access'] == 2 ? '' : '';
                // check rights - not in tree view, just page view
                // ...
                // add css classes
                $class = (in_array($row['pages_id'], $path)) ? ' class="node-open"' : '';
                $html .= "\n\t<li" . $class . ">";

                // use seo pages_id_link if set
                if (strlen($row['pages_id_link']) > 0 && $seo == 1) {
                    $html .= '<a href="http://' . $_SESSION['site_domain'] . '/pages/' . $row['pages_id_link'] . '">';
                } else {
                    $html .= '<a href="' . $script . '?id=' . $row['pages_id'] . '">';
                }
                $html .= '<span>';
                $html .= $row['title'];
                $html .= '</span></a>' . $icon . '</li>';
            }
            $html .= "\n</ul>\n";
        }
        return $html;
    }

    /**
     * @param int $id
     * @return void
     */
    public function getPagesRootPreview($id)
    {
        echo "<ul id=\"navigation-root\">\n";
        echo "<li><a>";
        echo $id;
        echo '</a></li>';
        echo "</ul>\n";
    }


    /**
     * @return void
     */
    public function getPagesRootBase()
    {

        $sql = "SELECT pages_id, title 
		FROM pages 
		WHERE parent_id = 0";
        if (!isset($_SESSION['users_id'])) {
            $sql .= " AND access = 1";
        }
        $sql .= " ORDER BY position asc";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        if (count($rows)) {

            echo "<ul id=\"navigation-root\" class=\"clearfixx\">\n";
            foreach ($rows as $row) {
                // check rights - not in tree view, just page view
                // ...
                // add css classes
                echo '<li id="pages_' . $row['pages_id'] . '"  style="padding:0px 20px 10px 0px;">';
                echo $row['title'];
                echo "</li>";
            }
            echo "</ul>\n";
        }
    }


    /**
     * @param int $parent_id
     * @return mixed
     */
    public function getPagesParent($parent_id)
    {
        $sql = "SELECT pages_id, parent_id, title, pages_id_link 
		FROM pages 
		WHERE pages_id = :parent_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_id
     * @return array
     */
    public function getPagesChildren($pages_id)
    {
        $sql = "SELECT pages_id, title, pages_id_link 
		FROM pages 
		WHERE parent_id = :pages_id";
        if (!isset($_SESSION['users_id'])) {
            $sql .= " AND access = 2";
        }
        $sql .= " AND (SELECT NOW() BETWEEN utc_start_publish AND utc_end_publish";
        $sql .= " OR NOW() > utc_start_publish AND utc_end_publish IS NULL) ";
        $sql .= " ORDER BY position asc";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @return array
     */
    public function getPagesParentNull()
    {
        $sql = "SELECT pages_id, title, pages_id_link, access, status 
		FROM pages 
		WHERE parent_id IS NULL
		ORDER BY title ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }


    /**
     *
     * @param int $parent_id
     * @return array
     */
    public function getPagesTreePublished($parent_id)
    {

        $sql = "SELECT pages_id, parent_id, parent, access, title, pages_id_link 
		FROM pages
		WHERE parent_id = :parent_id
		AND (SELECT NOW() BETWEEN utc_start_publish AND utc_end_publish
		OR NOW() > utc_start_publish AND utc_end_publish IS NULL)";
        if (!isset($_SESSION['users_id'])) {
            $sql .= " AND access = 2";
        }
        $sql .= " ORDER BY position asc";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $rows;
    }


    /**
     * @param int $parent_id
     * @return array
     */
    public function getPagesTreeAll($parent_id)
    {
        $sql = "SELECT pages_id, parent_id, parent, access, title, pages_id_link, status 
		FROM pages
		WHERE parent_id = :parent_id
		ORDER BY position ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $rows;
    }


    /**
     * @param int $parent_id
     * @param int $users_id
     * @return array
     */
    public function get_pages_tree($parent_id, $users_id)
    {

        $sql = "SELECT pages_id, parent_id, parent, access, title, pages_id_link 
		FROM pages
		WHERE parent_id = :parent_id";
        if ($users_id == 0) {
            $sql .= " AND access = 1";
        }
        $sql .= " ORDER BY position asc";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $rows;
    }


    /**
     * @param $header
     * @return string
     */
    public function getPagesHeaderCSS($header)
    {
        $str = 'background: url(' . CMS_DIR . '/content/uploads/header/' . $header . ') no-repeat;';
        return 'style="' . $str . '"';
    }


    /**
     * @param int $pages_id
     * @return mixed
     */
    public function getPagesHeader($pages_id)
    {
        $sql = "SELECT header 
		FROM pages
		WHERE pages_id = :pages_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_id
     * @param string $header_image
     * @param string $utc_modified
     * @return bool
     */
    public function updatePagesSetupSiteHeaderImage($pages_id, $header_image, $utc_modified)
    {
        try {
            $stmt = $this->db->prepare("UPDATE pages SET header =:header_image, utc_modified = :utc_modified WHERE pages_id =:pages_id");
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":header_image", $header_image, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int comments
     * @param int $ads
     * @param int $stories_columns
     * @return bool
     */
    public function updatePagesSetupSiteContent($pages_id, $comments, $ads, $stories_columns)
    {
        try {
            $sql_update = "UPDATE pages
			SET comments = :comments,
			stories_columns = :stories_columns,
			ads = :ads
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":comments", $comments, PDO::PARAM_INT);
            $stmt->bindParam(":stories_columns", $stories_columns, PDO::PARAM_INT);
            $stmt->bindParam(":ads", $ads, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $template
     * @param string $utc_modified
     * @return bool
     */
    public function updatePagesSetupTemplate($pages_id, $template, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET template = :template,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":template", $template, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $stories_last_modified
     * @param int $stories_image_copyright
     * @param int $stories_wide_teaser_image_width
     * @param int $stories_wide_teaser_image_align
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesSetupStoriesSettings($pages_id, $stories_last_modified, $stories_image_copyright, $stories_wide_teaser_image_width, $stories_wide_teaser_image_align, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET stories_last_modified = :stories_last_modified,
			stories_image_copyright = :stories_image_copyright,
			stories_wide_teaser_image_width = :stories_wide_teaser_image_width, 
			stories_wide_teaser_image_align = :stories_wide_teaser_image_align,			
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":stories_wide_teaser_image_width", $stories_wide_teaser_image_width, PDO::PARAM_INT);
            $stmt->bindParam(":stories_wide_teaser_image_align", $stories_wide_teaser_image_align, PDO::PARAM_INT);
            $stmt->bindParam(":stories_last_modified", $stories_last_modified, PDO::PARAM_INT);
            $stmt->bindParam(":stories_image_copyright", $stories_image_copyright, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $stories_child
     * @param int $stories_child_type
     * @param string $stories_css_class
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesSetupStoriesChild($pages_id, $stories_child, $stories_child_type, $stories_css_class, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET stories_child = :stories_child,
			stories_child_type = :stories_child_type,
			stories_css_class = :stories_css_class,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":stories_child", $stories_child, PDO::PARAM_INT);
            $stmt->bindParam(":stories_child_type", $stories_child_type, PDO::PARAM_INT);
            $stmt->bindParam(":stories_css_class", $stories_css_class, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $stories_promoted
     * @param int $stories_promoted_area
     * @param string $stories_filter
     * @param int $stories_limit
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesSetupStoriesPromoted($pages_id, $stories_promoted, $stories_promoted_area, $stories_filter, $stories_limit, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET stories_promoted = :stories_promoted,
			stories_promoted_area = :stories_promoted_area,
			stories_filter = :stories_filter,
			stories_limit = :stories_limit,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":stories_promoted", $stories_promoted, PDO::PARAM_INT);
            $stmt->bindParam(":stories_promoted_area", $stories_promoted_area, PDO::PARAM_INT);
            $stmt->bindParam(":stories_filter", $stories_filter, PDO::PARAM_STR);
            $stmt->bindParam(":stories_limit", $stories_limit, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $stories_event_dates
     * @param string $stories_event_dates_filter
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesSetupStoriesEventDates($pages_id, $stories_event_dates, $stories_event_dates_filter, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET stories_event_dates = :stories_event_dates,
			stories_event_dates_filter = :stories_event_dates_filter,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":stories_event_dates", $stories_event_dates, PDO::PARAM_INT);
            $stmt->bindParam(":stories_event_dates_filter", $stories_event_dates_filter, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $breadcrumb
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesBreadcrumb($pages_id, $breadcrumb, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET breadcrumb = :breadcrumb,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":breadcrumb", $breadcrumb, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param string $lang
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesHtmlLang($pages_id, $lang, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET lang = :lang,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":lang", $lang, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param string $pages_title
     * @param string $title_hide
     * @param string $content
     * @param string $content_author
     * @param int $rss_promote
     * @param string $rss_description
     * @param int $events
     * @param int $reservations
     * @param int $plugins
     * @param string $utc_modified
     * @return bool
     */
    public function updatePagesContent($pages_id, $pages_title, $title_hide, $content, $content_author, $rss_promote, $rss_description, $events, $reservations, $plugins, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET title = :pages_title,
			title_hide = :title_hide,
			content = :content,
			content_author = :content_author,
			rss_description = :rss_description,
			rss_promote = :rss_promote,
			events = :events,
			reservations = :reservations,
			plugins = :plugins,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':pages_title', $pages_title, PDO::PARAM_STR);
            $stmt->bindParam(':title_hide', $title_hide, PDO::PARAM_INT);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':content_author', $content_author, PDO::PARAM_STR);
            $stmt->bindParam(':rss_description', $rss_description, PDO::PARAM_STR);
            $stmt->bindParam(':rss_promote', $rss_promote, PDO::PARAM_INT);
            $stmt->bindParam(':events', $events, PDO::PARAM_INT);
            $stmt->bindParam(':reservations', $reservations, PDO::PARAM_INT);
            $stmt->bindParam(':plugins', $plugins, PDO::PARAM_INT);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param string $meta_keywords
     * @param string $meta_description
     * @param string $meta_robots
     * @param string $meta_additional
     * @param string $utc_modified
     * @return bool
     */
    public function updatePagesMeta($pages_id, $meta_keywords, $meta_description, $meta_robots, $meta_additional, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET meta_keywords = :meta_keywords,
			meta_description = :meta_description,
			meta_robots = :meta_robots,
			meta_additional = :meta_additional,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':meta_keywords', $meta_keywords, PDO::PARAM_STR);
            $stmt->bindParam(':meta_description', $meta_description, PDO::PARAM_STR);
            $stmt->bindParam(':meta_robots', $meta_robots, PDO::PARAM_STR);
            $stmt->bindParam(':meta_additional', $meta_additional, PDO::PARAM_STR);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param string $pages_id_link
     * @param string $utc_modified
     * @return bool
     */
    public function updatePagesSeoLink($pages_id, $pages_id_link, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET pages_id_link = :pages_id_link,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':pages_id_link', $pages_id_link, PDO::PARAM_STR);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $plugins
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesUsePlugins($pages_id, $plugins, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET plugins = :plugins,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':plugins', $plugins, PDO::PARAM_INT);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param string $plugin_arguments
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesPluginArguments($pages_id, $plugin_arguments, $utc_modified)
    {
        try {
            $sql_update = "UPDATE pages
			SET plugin_arguments = :plugin_arguments,
			utc_modified = :utc_modified
			WHERE pages_id = :pages_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':plugin_arguments', $plugin_arguments, PDO::PARAM_STR);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $id
     * @param int $parent_id
     * @param int $new_parent_id
     * @return bool
     */
    public function updatePagesParent($id, $parent_id, $new_parent_id)
    {
        try {
            // use beginTransaction > commit > rollBack
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("UPDATE pages SET parent_id = :parent_id WHERE pages_id = :pages_id");
            $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':parent_id', $new_parent_id, PDO::PARAM_INT);
            $q = $stmt->execute();

            if ($parent_id !== null) {
                // check if previous parent page no longer have parent
                if (!$this->db->query("SELECT pages_id FROM pages WHERE parent_id = $parent_id")->fetchALL(PDO::FETCH_ASSOC)) {
                    $this->db->exec("UPDATE pages SET parent = 0 WHERE pages_id = $parent_id");
                }
            }

            // update parent page width existing parent
            $this->db->exec("UPDATE pages SET parent = 1 WHERE pages_id = $new_parent_id");
            $this->db->commit();
            return $q;

        } catch (PDOException $e) {
            $this->db->rollBack();
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $id
     * @param int $parent_id
     * @return bool
     */
    public function updatePagesRemoveHierarchy($id, $parent_id)
    {
        try {
            if (!$this->db->query("SELECT pages_id FROM pages WHERE parent_id = $id")->fetchALL(PDO::FETCH_ASSOC)) {
                // use beginTransaction > commit > rollBack
                $this->db->beginTransaction();
                $stmt = $this->db->prepare("UPDATE pages SET parent_id = NULL WHERE pages_id = :pages_id");
                $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
                $q = $stmt->execute();

                if ($parent_id !== null) {
                    // check if previous parent page no longer have parent
                    if (!$this->db->query("SELECT pages_id FROM pages WHERE parent_id = $parent_id")->fetchALL(PDO::FETCH_ASSOC)) {
                        $this->db->exec("UPDATE pages SET parent = 0 WHERE pages_id = $parent_id");
                    }
                }

                $this->db->commit();
                return $q;
            }

        } catch (PDOException $e) {
            $this->db->rollBack();
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param array $pages_id_array
     */
    public function updatePagesPosition($pages_id_array)
    {
        try {
            // use beginTransaction > commit > rollBack
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("UPDATE pages SET position =:position WHERE pages_id =:pages_id");
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":position", $position, PDO::PARAM_INT);
            // counter
            $position = 0;
            foreach ($pages_id_array as $pages_id) {
                $position = $position + 1;
                $stmt->execute();
            }
            $this->db->commit();
            echo 'saved ';

        } catch (PDOException $e) {
            $this->db->rollBack();
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }


    /**
     * @param int $pages_id
     * @param int $stories_columns
     * @param string $utc_modified
     * @return bool
     */
    public function updatePagesStoriesTemplate($pages_id, $stories_columns, $utc_modified)
    {
        try {
            $stmt = $this->db->prepare('UPDATE pages SET stories_columns =:stories_columns, utc_modified = :utc_modified WHERE pages_id =:pages_id');
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":stories_columns", $stories_columns, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();
            // echo 'stories template saved: ' . date('H:i:s');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param in $pages_stories_id
     * @return mixed
     */
    public function getPagesStory($pages_stories_id)
    {
        $sql = "
		SELECT pages.title, pages.story_content 
		FROM pages INNER JOIN pages_stories
		ON pages.pages_id=pages_stories.stories_id
		WHERE pages_stories.pages_stories_id = $pages_stories_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_stories_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $id
     * @return array
     */
    public function getPagesStories($id)
    {
        $sql = "
		SELECT pages_stories.pages_stories_id, pages.title, pages.template, pages_stories.container 
		FROM pages INNER JOIN pages_stories
		ON pages.pages_id=pages_stories.stories_id
		WHERE pages_stories.pages_id = $id
		ORDER BY
		pages_stories.sort_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $id
     * @return array
     */
    public function getPagesStoriesChild($id)
    {
        $sql = "
		SELECT pages.pages_id, pages.title 
		FROM pages 
		WHERE pages.parent_id = :pages_id
		ORDER BY pages.title ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @param int $limit
     * @return array
     */
    public function getPagesStoriesPromoted($search, $limit)
    {
        $sql = "
		SELECT pages.pages_id, pages.title 
		FROM pages 
		WHERE pages.story_promote = 1
		AND pages.tag LIKE :search		
		AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
		OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 		
		ORDER BY pages.utc_start_publish desc LIMIT $limit";

        // apply percentages to search string
        $search = "%" . $search . "%";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @return array
     */
    public function getPagesStoriesFeed()
    {
        $sql = "
		SELECT pages.pages_id, pages.title, pages.story_content 
		FROM pages 
		WHERE pages.story_promote = 1
		AND CHAR_LENGTH(pages.story_content) > 0
		AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
		OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 		
		ORDER BY RAND() LIMIT 5";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @return array
     */
    public function getPagesStoriesEvents()
    {
        $sql = "
		SELECT pages.pages_id, pages.title, pages.story_content 
		FROM pages 
		WHERE pages.story_event = 1
		AND CHAR_LENGTH(pages.story_content) > 0
		AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
		OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 		
		AND NOW() < pages.story_event_date
		ORDER BY RAND() LIMIT 5";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function getPagesStoryContent($id)
    {

        $sql = "
		SELECT pages.pages_id, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.story_wide_content, pages.story_wide_teaser_image, pages.utc_start_publish, pages.utc_modified, pages_images.story_teaser, pages_images.filename, pages_images.caption, pages_images.ratio
		FROM pages
		INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
		WHERE pages.pages_id = $id
		AND pages_images.story_teaser = 1
		
		UNION

		SELECT pages.pages_id, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.story_wide_content, pages.story_wide_teaser_image, pages.utc_start_publish, pages.utc_modified, null AS story_teaser, null AS filename, null AS caption, null AS ratio
		FROM pages
		WHERE pages.pages_id = $id
		";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $id
     * @return array|null
     */
    public function getPagesStoryContentPublish($id)
    {
        $sql = "SELECT pages.pages_id, pages_stories.pages_stories_id, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.utc_start_publish, pages.utc_modified, pages.template, pages_stories.container, pages_images.filename, pages_images.story_teaser, pages_images.caption, pages_images.ratio
		FROM pages 
		INNER JOIN pages_stories
		ON pages.pages_id=pages_stories.stories_id
		INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
		WHERE pages_stories.pages_id = $id
		AND pages_images.story_teaser = 1
		ORDER BY
		pages_stories.sort_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return null;
        }
    }


    /**
     * @param int $id
     * @return array|null
     */
    public function getPagesStoryContentPublishAll($id)
    {
        $sql =
            "SELECT pages.pages_id, pages_stories.pages_stories_id, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.utc_start_publish, pages.utc_modified, pages.template, pages_stories.container, null AS filename, null AS story_teaser, null AS caption, null AS ratio
		FROM pages, pages_stories
		WHERE pages.pages_id=pages_stories.stories_id
		AND pages_stories.pages_id = $id
		AND pages_stories.stories_id NOT IN 
		( SELECT pages_images.pages_id FROM pages_images )
		UNION
		SELECT pages.pages_id, pages_stories.pages_stories_id, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.utc_start_publish, pages.utc_modified, pages.template, pages_stories.container, pages_images.filename, pages_images.story_teaser, pages_images.caption, pages_images.ratio
		FROM pages 
		INNER JOIN pages_stories
		ON pages.pages_id=pages_stories.stories_id
		INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
		WHERE pages_stories.pages_id = $id
		AND pages_images.story_teaser = 1
		";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return null;
        }
    }


    /**
     * @param int $id
     * @return array|null
     * sql syntax
     * get all stories missing image teaser ->  (creating null values for image instanses)
     * union
     * get all stories having image teaser ->
     * fix correct sort order as 'rank'
     */
    public function getPagesStoryContentPublishAllSorted($id)
    {
        $sql =
            "SELECT * 
		FROM
		(
			SELECT pages_stories.sort_id AS rank, pages.pages_id, pages.access, pages_stories.pages_stories_id, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.story_wide_content, pages.story_wide_teaser_image, pages.utc_start_publish, pages.utc_modified, pages.template, pages_stories.container, pages_images.filename, pages_images.caption, pages_images.copyright, pages_images.story_teaser, pages_images.ratio
			FROM pages 
			INNER JOIN pages_stories
			ON pages.pages_id=pages_stories.stories_id
			INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
			WHERE pages_stories.pages_id = $id
			AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
			OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 
			AND pages_images.story_teaser = 1

			UNION

			SELECT pages_stories.sort_id AS rank, pages.pages_id, pages.access, pages_stories.pages_stories_id, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.story_wide_content, pages.story_wide_teaser_image, pages.utc_start_publish, pages.utc_modified, pages.template, pages_stories.container, null AS filename, null AS caption, null AS copyright, null AS story_teaser, null AS ratio
			FROM pages, pages_stories
			WHERE pages.pages_id=pages_stories.stories_id
			AND pages_stories.pages_id = $id
			AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
			OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 
			
		) a
		GROUP BY pages_id
		ORDER BY rank";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return null;
        }
    }


    /**
     * @param int $ids
     * @return array|null
     */
    public function getPagesStoryContentPublishSelected($ids)
    {
        $sql =
            "
		SELECT * FROM
		(
			SELECT pages.pages_id, pages.title, pages.access, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.utc_start_publish, pages.utc_modified, pages.template, pages_images.filename, pages_images.story_teaser, pages_images.caption, pages_images.copyright, pages_images.ratio
			FROM pages 
			INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
			WHERE pages.pages_id IN ($ids)
			AND pages_images.story_teaser = 1

			UNION
				  
			SELECT pages.pages_id, pages.title, pages.access, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.utc_start_publish, pages.utc_modified, pages.template, null AS filename, null AS story_teaser, null AS caption, null AS copyright, null AS ratio
			FROM pages
			WHERE pages_id IN ($ids)		 
		) AS tmp
		GROUP BY tmp.pages_id
		";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return null;
        }
    }


    /**
     * @param int $id
     * @return array
     */
    public function getPagesStoryContentPublishChild($id)
    {
        $sql =
            "SELECT * 
		FROM
		(
		SELECT pages.title AS rank, pages.pages_id, pages.access, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.story_wide_content, pages.story_wide_teaser_image, pages.utc_start_publish, pages.utc_modified, pages.template, NULL AS filename, NULL AS caption, NULL AS copyright, NULL AS story_teaser, NULL AS ratio
		FROM pages 
		WHERE pages.parent_id = :pages_id
		AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
		OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 
		AND pages.pages_id NOT IN 
		( SELECT pages_images.pages_id FROM pages_images )
		UNION
		SELECT pages.title AS rank, pages.pages_id, pages.access, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.story_wide_content, pages.story_wide_teaser_image, pages.utc_start_publish, pages.utc_modified, pages.template, pages_images.filename, pages_images.caption, pages_images.copyright, pages_images.story_teaser, pages_images.ratio
		FROM pages 
		INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
		WHERE pages.parent_id = :pages_id
		AND pages_images.story_teaser = 1
		AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
		OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 
		) a
		ORDER BY rank ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @param int $limit
     * @return array
     *
     * sql syntax
     * get all promoted pages missing image teaser ->  (creating null values for image instanses)
     * union
     * get all promoted pages having image teaser ->
     * order pages, publish date descending, as 'rank'
     */
    public function getPagesStoryContentPublishPromoted($search, $limit)
    {
        $sql =
            "SELECT * 
		FROM 
		(	
		SELECT pages.utc_start_publish AS rank, pages.pages_id, pages.access, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.utc_start_publish, pages.utc_modified, pages.template, pages.story_wide_content, pages.story_wide_teaser_image, pages_images.filename, pages_images.caption, pages_images.copyright, pages_images.story_teaser, pages_images.ratio
		FROM pages
		INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
		WHERE pages.story_promote = 1
		AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
		OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 
		AND pages.tag LIKE :search
		AND pages_images.story_teaser = 1
		UNION
		SELECT pages.utc_start_publish AS rank, pages.pages_id, pages.access, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.utc_start_publish, pages.utc_modified, pages.template, pages.story_wide_content, pages.story_wide_teaser_image, null AS filename, null AS caption, null AS copyright, null AS story_teaser, null AS ratio
		FROM pages
		WHERE pages.story_promote = 1
		AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
		OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 
		AND pages.tag LIKE :search
		) a
		GROUP BY pages_id
		ORDER BY rank desc LIMIT $limit";

        // apply percentages to search string
        $search = "%" . $search . "%";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @param string $date
     * @param $period
     * @return array
     * sql syntax
     * get all promoted pages missing image teaser ->  (creating null values for image instanses)
     * union
     * get all promoted pages having image teaser ->
     * order pages, publish date descending, as 'rank'
     */
    public function getPagesStoryContentPublishEvent($search, $date, $period)
    {
        $operator = $period == 'next' ? '>' : '<';
        $order = $period == 'next' ? 'ASC' : 'DESC';

        $sql =
            "SELECT * 
		FROM 
		(		
		SELECT pages.story_event_date AS rank, pages.story_event_date, pages.pages_id, pages.access, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.story_wide_content, pages.story_wide_teaser_image, pages.utc_start_publish, pages.utc_modified, pages.template, pages_images.filename, pages_images.caption, pages_images.copyright, pages_images.story_teaser, pages_images.ratio
		FROM pages
		INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
		WHERE pages.story_event = 1
		AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
		OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 
		AND pages.tag LIKE :search
		AND pages_images.story_teaser = 1
		AND pages.story_event_date $operator :date
		UNION
		SELECT pages.story_event_date AS rank, pages.story_event_date, pages.pages_id, pages.access, pages.title, pages.story_link, pages.story_css_class, pages.story_custom_title, pages.story_custom_title_value, pages.story_content, pages.story_wide_content, pages.story_wide_teaser_image, pages.utc_start_publish, pages.utc_modified, pages.template, null AS filename, null AS caption, null AS copyright, null AS story_teaser, null AS ratio
		FROM pages
		WHERE pages.story_event = 1
		AND (SELECT NOW() BETWEEN pages.utc_start_publish AND pages.utc_end_publish
		OR NOW() > pages.utc_start_publish AND pages.utc_end_publish IS NULL) 
		AND pages.tag LIKE :search
		AND pages.story_event_date $operator :date
		) a
		GROUP BY pages_id
		ORDER BY rank $order LIMIT 5";

        // apply percentages to search string
        $search = "%" . $search . "%";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @param int $promoted
     * @param int $limit
     * @return array
     *
     * sql syntax
     * get all promoted pages missing image teaser ->  (creating null values for image instanses)
     * union
     * get all promoted pages having image teaser ->
     */
    public function getPagesStoriesRSS($search, $promoted, $limit)
    {
        $sql =
            "SELECT * 
		FROM 
		(
		SELECT pages.utc_start_publish AS rank, pages.pages_id AS id, pages.title, pages.rss_description AS description, pages.utc_start_publish AS pubdate, NULL AS filename
		FROM pages
		WHERE pages.access = 2 ";
        if ($promoted == 1) {
            $sql .= "AND pages.story_promote = 1 ";
        }
        $sql .= "AND pages.rss_promote = 1
		AND pages.tag LIKE :search
		AND pages.pages_id NOT IN 
		( SELECT pages_images.pages_id FROM pages_images )
		
		UNION
		
		SELECT pages.utc_start_publish AS rank, pages.pages_id AS id, pages.title, pages.rss_description AS description, pages.utc_start_publish AS pubdate, pages_images.filename
		FROM pages
		INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
		WHERE pages.access = 2 ";
        if ($promoted == 1) {
            $sql .= "AND pages.story_promote = 1 ";
        }
        $sql .= "AND pages.rss_promote = 1
		AND pages.tag LIKE :search
		AND pages_images.story_teaser = 1
		) a
		ORDER BY rank desc, title ASC LIMIT $limit";

        // apply percentages to search string
        $search = "%" . $search . "%";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->bindParam(':promoted', $promoted, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $id
     * @return array
     *
     * sql syntax
     * get all promoted pages missing image teaser ->  (creating null values for image instanses)
     * union
     * get all promoted pages having image teaser ->
     */
    public function getPagesStoriesPreviewRSS($id)
    {
        $sql =
            "SELECT DISTINCT pages.pages_id AS id, pages.title, pages.rss_description AS description, pages.utc_start_publish AS pubdate, NULL AS filename
		FROM pages
		WHERE pages.pages_id = :id 
		UNION
		SELECT DISTINCT pages.pages_id AS id, pages.title, pages.rss_description AS description, pages.utc_start_publish AS pubdate, pages_images.filename AS filename
		FROM pages
		INNER JOIN pages_images ON pages.pages_id = pages_images.pages_id
		WHERE pages.pages_id = :id 
		AND pages_images.story_teaser = 1
		ORDER BY filename DESC LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $id
     * @param int $stories_id
     * @param string $container
     * @param int $sort_id
     * @return int
     */
    public function updatePagesStories($id, $stories_id, $container, $sort_id)
    {
        // prevent duplicates
        $stmt = $this->db->prepare("SELECT pages_stories_id FROM pages_stories WHERE pages_id = :pages_id AND stories_id = :stories_id");
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':stories_id', $stories_id, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        if (!count($rows)) {
            try {
                $sql_insert = "INSERT INTO pages_stories 
				(pages_id, stories_id, container, sort_id) VALUES
				(:pages_id, :stories_id, :container, :sort_id)";

                $stmt = $this->db->prepare($sql_insert);
                $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':stories_id', $stories_id, PDO::PARAM_INT);
                $stmt->bindParam(':container', $container, PDO::PARAM_STR);
                $stmt->bindParam(':sort_id', $sort_id, PDO::PARAM_INT);
                $stmt->execute();
                return $this->db->lastInsertId('pages_stories_id');

            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }


    /**
     * @param string $container
     * @param int $sort_id
     * @param int $pages_stories_id
     * @return bool
     */
    public function updatePagesStoriesLayout($container, $sort_id, $pages_stories_id)
    {
        try {
            $stmt = $this->db->prepare('UPDATE pages_stories SET container =:container, sort_id =:sort_id WHERE pages_stories_id =:pages_stories_id');
            $stmt->bindParam(':pages_stories_id', $pages_stories_id, PDO::PARAM_INT);
            $stmt->bindParam(':container', $container, PDO::PARAM_STR);
            $stmt->bindParam(':sort_id', $sort_id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param string $selections
     * @param string $utc_modified
     * @return bool
     */
    public function setPagesSelections($pages_id, $selections, $utc_modified)
    {
        try {
            $stmt = $this->db->prepare('UPDATE pages SET selections =:selections, utc_modified = :utc_modified WHERE pages_id =:pages_id');
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':selections', $selections, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;

        }
    }


    /**
     * @param int $pages_selections_id
     * @return array
     */
    public function getPagesSelections($pages_selections_id)
    {
        $sql = "SELECT pages_id, title, status FROM `pages` WHERE find_in_set(" . $pages_selections_id . ", selections) <> 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_selections_id', $pages_selections_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $pages_stories_id
     * @return bool
     */
    public function deletePagesStories($pages_stories_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages_stories WHERE pages_stories_id =:pages_stories_id");
            $stmt->bindParam(":pages_stories_id", $pages_stories_id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @return bool
     */
    public function deleteSelectedPagePagesStories($pages_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages_stories WHERE pages_id =:pages_id");
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_widgets_id
     * @return bool
     */
    public function deletePagesWidgets($pages_widgets_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages_widgets WHERE pages_widgets_id =:pages_widgets_id");
            $stmt->bindParam(":pages_widgets_id", $pages_widgets_id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @return bool
     */
    public function deleteSelectedPagePagesWidgets($pages_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages_widgets WHERE pages_id =:pages_id");
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $id
     * @return mixed|null
     */
    public function getPagesAsTemplate($id)
    {
        $rows = null;
        $sql = "SELECT meta_additional, meta_robots, tag, header, template, ads, ads_limit, ads_filter, stories_columns, stories_filter, selections
		FROM pages 
		WHERE pages_id = :pages_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        return $rows;
    }


    /**
     * @param int $id
     * @return array|null
     */
    public function getPagesImages($id)
    {
        $rows = null;
        $sql = "SELECT pages_images_id, filename, copyright, caption, alt, title, tag, promote, story_teaser
		FROM pages_images 
		WHERE pages_id = :pages_id
		ORDER BY position, filename";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $rows;
    }


    /**
     * @param string $search
     * @return array
     */
    public function getPagesImagesTag($search)
    {
        $query_parts = array();
        $tags = explode(",", $search);
        foreach ($tags as $tag) {
            $query_parts[] = "'%" . $tag . "%'";
        }

        $string = implode(' OR pages_images.tag LIKE ', $query_parts);

        $sql = "SELECT pages_images.pages_images_id, pages_images.filename, pages_images.caption, pages_images.promote, pages_images.story_teaser, pages.title, pages.pages_id
		FROM pages_images INNER JOIN pages
		ON pages_images.pages_id=pages.pages_id
		WHERE pages_images.tag LIKE {$string}
		LIMIT 100";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $rows;
    }


    /**
     * @param string $tag
     * @return array|null
     */
    public function getPagesImagesSearchTag($tag)
    {
        $rows = null;
        $sql = "SELECT pages_images.pages_images_id, pages_images.filename, pages_images.caption, pages_images.promote, pages_images.story_teaser, pages.title, pages.pages_id
		FROM pages_images INNER JOIN pages
		ON pages_images.pages_id=pages.pages_id
		WHERE pages_images.tag LIKE :tag
		LIMIT 100";

        // apply percentages to search string
        $tag = "%" . $tag . "%";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $rows;
    }


    /**
     * @param int $pages_images_id
     * @return mixed
     */
    public function getPagesImagesMeta($pages_images_id)
    {
        $rows = null;
        $sql = "SELECT filename, creator, copyright, caption, alt, title, tag, xmpdata, promote, ratio, story_teaser, utc_created
		FROM pages_images 
		WHERE pages_images_id = :pages_images_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_images_id', $pages_images_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_widgets_id
     * @param string $tag
     * @return array|null
     */
    public function getPagesImagesSlideshowFeed($pages_widgets_id, $tag)
    {
        $rows = null;
        $sql = "SELECT pages_images.filename, pages_images.creator, pages_images.copyright, pages_images.caption, pages_images.alt, pages_images.title, pages_images.ratio, pages_images.tag, pages_images.pages_id
		FROM pages_images
		LEFT JOIN pages_widgets ON pages_images.pages_id = pages_widgets.pages_id
		LEFT JOIN widgets ON pages_widgets.widgets_id = widgets.widgets_id
		WHERE (
		SELECT pages_widgets.pages_widgets_id = :pages_widgets_id
		AND pages_images.tag LIKE :tag
		)
		ORDER BY pages_images.position ASC";

        // apply percentages to search string
        $tag = "%" . $tag . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_widgets_id', $pages_widgets_id, PDO::PARAM_INT);
        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        return $rows;
    }


    /**
     * @param int $id
     * @param string $filename
     * @param double $ratio
     * @param string $image_description
     * @param string $artist
     * @param string $xmpdata
     * @return int
     */
    public function savePagesImages($id, $filename, $ratio, $image_description, $artist, $xmpdata)
    {
        // prevent duplicates
        $stmt = $this->db->prepare("SELECT filename FROM pages_images WHERE pages_id = :pages_id AND filename = :filename");
        $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':filename', $filename, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);
        if (!count($rows)) {
            try {
                $sql_insert = "INSERT INTO pages_images 
				(pages_id, filename, ratio, caption, copyright, xmpdata) VALUES
				(:pages_id, :filename, :ratio, :image_description, :artist, :xmpdata)";

                $stmt = $this->db->prepare($sql_insert);
                $stmt->bindParam(':pages_id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':filename', $filename, PDO::PARAM_STR);
                $stmt->bindParam(':image_description', $image_description, PDO::PARAM_STR);
                $stmt->bindParam(':artist', $artist, PDO::PARAM_STR);
                $stmt->bindParam(':xmpdata', $xmpdata, PDO::PARAM_STR);
                $stmt->bindParam(':ratio', $ratio, PDO::PARAM_INT);
                $stmt->execute();
                return $this->db->lastInsertId('pages_images_id');

            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }


    /**
     * @param int $pages_images_id
     * @param string $caption
     * @param int $position
     * @param int $story_teaser
     * @param string $utc_modified
     * @return bool
     */
    public function updatePagesImages($pages_images_id, $caption, $position, $story_teaser, $utc_modified)
    {
        try {
            $sql = "UPDATE pages_images
			SET caption = :caption,
			position = :position,
			story_teaser = :story_teaser,
			utc_modified = :utc_modified
			WHERE pages_images_id = :pages_images_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":pages_images_id", $pages_images_id, PDO::PARAM_INT);
            $stmt->bindParam(":caption", $caption, PDO::PARAM_STR);
            $stmt->bindParam(":position", $position, PDO::PARAM_INT);
            $stmt->bindParam(":story_teaser", $story_teaser, PDO::PARAM_INT);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_images_id
     * @return bool
     */
    public function setPagesImagesTeaserReset($pages_images_id)
    {
        try {
            $sql = "UPDATE pages_images
			SET story_teaser = 0
			WHERE pages_images_id = :pages_images_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":pages_images_id", $pages_images_id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_images_id
     * @param string $caption
     * @param string $alt
     * @param string $title
     * @param string $creator
     * @param string $copyright
     * @param string $tag
     * @param int $promote
     * @param string $utc_modified
     * @return bool
     */
    public function updatePagesImagesMeta($pages_images_id, $caption, $alt, $title, $creator, $copyright, $tag, $promote, $utc_modified)
    {
        try {
            $sql = "UPDATE pages_images
			SET caption = :caption,
			alt = :alt,
			title = :title,
			creator = :creator,
			copyright = :copyright,
			tag = :tag,
			promote = :promote
			WHERE pages_images_id = :pages_images_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":pages_images_id", $pages_images_id, PDO::PARAM_INT);
            $stmt->bindParam(":caption", $caption, PDO::PARAM_STR);
            $stmt->bindParam(":alt", $alt, PDO::PARAM_STR);
            $stmt->bindParam(":title", $title, PDO::PARAM_STR);
            $stmt->bindParam(":creator", $creator, PDO::PARAM_STR);
            $stmt->bindParam(":copyright", $copyright, PDO::PARAM_STR);
            $stmt->bindParam(":tag", $tag, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            $stmt->bindParam(":promote", $promote, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $pages_images_id
     * @param double $ratio
     * @param string $utc_modified
     * @return bool
     */
    public function updatePagesImagesCrop($pages_images_id, $ratio, $utc_modified)
    {
        try {
            $sql = "UPDATE pages_images
			SET ratio = :ratio,
			utc_modified = :utc_modified
			WHERE pages_images_id = :pages_images_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":pages_images_id", $pages_images_id, PDO::PARAM_INT);
            $stmt->bindParam(":ratio", $ratio, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param string $filename
     * @param int $pages_id
     * @return bool
     */
    public function deletePagesImages($filename, $pages_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages_images WHERE filename =:filename AND pages_id = :pages_id");
            $stmt->bindParam(":filename", $filename, PDO::PARAM_STR);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @return bool
     */
    public function deletePagesImagesAll($pages_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages_images WHERE pages_id = :pages_id");
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $users_id
     * @param int $groups_id
     * @param int $read
     * @param int $edit
     * @param int $create
     * @param string $utc_time
     * @return int
     */
    public function setPagesUsersRights($pages_id, $users_id, $groups_id, $read, $edit, $create, $utc_time)
    {
        try {
            $sql = "INSERT INTO pages_rights 
			(pages_id, users_id, groups_id, read, edit, create, utc_time) VALUES
			(:pages_id, :users_id, :groups_id, :read, :edit, :create, :utc_time)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
            $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
            $stmt->bindParam(':read', $read, PDO::PARAM_INT);
            $stmt->bindParam(':edit', $edit, PDO::PARAM_INT);
            $stmt->bindParam(':create', $create, PDO::PARAM_INT);
            $stmt->bindParam(':utc_time', $utc_time, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('pages_rights_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

}

?>