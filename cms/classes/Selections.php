<?php

/**
 * Class Selections
 */
class Selections extends Database
{

    /**
     * Selections constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     * @param string $search
     * @return array
     */
    public function getSelectionsSearch($search)
    {
        $sql = "SELECT pages_selections_id, name, description, area, active, position, utc_modified  
		FROM pages_selections 
		WHERE name LIKE :search
		OR description LIKE :search
		LIMIT 1000";

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
    public function getSelectionsSearchWords($search)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $names = implode(' OR name LIKE ', $query_parts);
        $descriptions = implode(' OR description LIKE ', $query_parts);

        $sql = "SELECT pages_selections_id, name, description, area, active, position, utc_modified  
		FROM pages_selections 
		WHERE name LIKE {$names}
		OR description LIKE {$descriptions}
		LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $pages_selections_id
     * @return array
     */
    public function getSelectionsContent($pages_selections_id)
    {

        $sql = "SELECT name, description, area, position, content_html, content_code, external_js, external_css, active  
		FROM pages_selections 
		WHERE pages_selections_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $pages_selections_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $array_pages_selections_id
     * @return array
     */
    public function getMultipleSelectionsContent($array_pages_selections_id)
    {
        $sql = "SELECT name, description, area, position, content_html, content_code, external_js, external_css
		FROM pages_selections 
		WHERE pages_selections_id IN (" . implode(',', array_map('intval', $array_pages_selections_id)) . ")
		AND active = 1
		ORDER BY position ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $pages_selections_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @return array
     */
    public function getSelectionsActive()
    {
        $sql = "SELECT pages_selections_id, name, description, area, position  
		FROM pages_selections 
		WHERE active = 1
		ORDER BY area ASC, position ASC, name ASC
		LIMIT 100";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $name
     * @return string
     */
    public function setSelectionsNew($name)
    {
        try {
            $sql = "INSERT INTO pages_selections 
			(name) VALUES
			(:name)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('pages_selections_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_selections_id
     * @return bool
     */
    public function setSelectionsDelete($pages_selections_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages_selections WHERE pages_selections_id =:pages_selections_id");
            $stmt->bindParam(":pages_selections_id", $pages_selections_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_selections_id
     * @param int $active
     * @param string $name
     * @param string $description
     * @param int $area
     * @param string $content_html
     * @param string $content_code
     * @param string $external_js
     * @param string $external_css
     * @param string $utc_modified
     * @return bool
     */
    public function setSelections($pages_selections_id, $active, $name, $description, $area, $content_html, $content_code, $external_js, $external_css, $utc_modified)
    {
        try {
            $sql = "UPDATE pages_selections
			SET active = :active,
			name = :name,
			description = :description,
			area = :area,
			external_js = :external_js,
			external_css = :external_css,
			content_html = :content_html,
			content_code = :content_code,
			utc_modified = :utc_modified
			WHERE pages_selections_id = :pages_selections_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":pages_selections_id", $pages_selections_id, PDO::PARAM_INT);
            $stmt->bindParam(":active", $active, PDO::PARAM_INT);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":description", $description, PDO::PARAM_STR);
            $stmt->bindParam(":area", $area, PDO::PARAM_INT);
            $stmt->bindParam(":external_js", $external_js, PDO::PARAM_STR);
            $stmt->bindParam(":external_css", $external_css, PDO::PARAM_STR);
            $stmt->bindParam(":content_html", $content_html, PDO::PARAM_STR);
            $stmt->bindParam(":content_code", $content_code, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param array $pages_selections_id_array
     */
    public function updatePagesSelectionsPosition($pages_selections_id_array)
    {
        try {
            // use beginTransaction > commit > rollBack
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("UPDATE pages_selections SET position =:position WHERE pages_selections_id =:pages_selections_id");
            $stmt->bindParam(":pages_selections_id", $pages_selections_id, PDO::PARAM_INT);
            $stmt->bindParam(":position", $position, PDO::PARAM_INT);

            $position = 0;
            foreach ($pages_selections_id_array as $pages_selections_id) {
                $position = $position + 1;
                $stmt->execute();
            }
            $this->db->commit();
            // echo 'page selections positions saved';

        } catch (PDOException $e) {
            $this->db->rollBack();
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }

}

?>