<?php

/**
 * Class Tags
 */
class Tags extends Database
{

    /**
     * Tags constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     * @return array
     */
    public function getTags()
    {
        $sql = "SELECT *   
		FROM tags 
		ORDER BY tag";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param $tags_id
     * @return mixed
     */
    public function getTag($tags_id)
    {

        $sql = "SELECT *   
		FROM tags 
		WHERE tags_id = :tags_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tags_id', $tags_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @return array
     */
    public function getTagsSearch($search)
    {
        $sql = "SELECT tags_id, tag  
		FROM tags
		WHERE tag = :search
		ORDER BY tag ASC
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
     * @return array
     */
    public function getTagsSearchLike($search)
    {

        $sql = "SELECT tags_id, tag  
		FROM tags
		WHERE tag LIKE :search
		ORDER BY tag ASC
		LIMIT 100";

        // apply percentages to search string
        $search = "%" . $search . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $tag
     * @param int $active
     * @param string $utc_created
     * @return int
     */
    public function setTagsNew($tag, $active, $utc_created)
    {
        try {
            $sql_insert = "INSERT INTO tags 
			(tag, active, utc_created) VALUES
			(:tag, :active, :utc_created)";

            $stmt = $this->db->prepare($sql_insert);
            $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
            $stmt->bindParam(':active', $active, PDO::PARAM_INT);
            $stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('tags_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $tags_id
     * @return bool
     */
    public function setTagsDelete($tags_id)
    {
        try {
            $sql = "DELETE FROM tags 
			WHERE tags_id = :tags_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':tags_id', $tags_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

}

?>