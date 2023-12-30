<?php

/**
 * Class PagesCategories
 */
class PagesCategories extends Database
{

    /**
     * PagesCategories constructor
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
    public function getPagesCategories()
    {
        $sql = "SELECT *   
		FROM pages_categories 
		ORDER BY position";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getPagesCategoriesNamed()
    {
        $sql = "SELECT category   
		FROM pages_categories 
		ORDER BY position";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param $pages_categories_id
     * @return mixed
     */
    public function getPagesCategory($pages_categories_id)
    {

        $sql = "SELECT *   
		FROM pages_categories 
		WHERE pages_categories_id = :pages_categories_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_categories_id', $pages_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @return array
     */
    public function getPagesCategoriesSearch($search)
    {
        $sql = "SELECT category  
		FROM pages_categories
		WHERE category = :search
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
    public function getPagesCategoriesSearchLike($search)
    {

        $sql = "SELECT pages_categories_id, category  
		FROM pages_categories
		WHERE category LIKE :search
		ORDER BY category ASC
		LIMIT 100";

        // apply percentages to search string
        $search = "%" . $search . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $category
     * @param int $position
     * @param string $utc_created
     * @return int
     */
    public function setPagesCategoriesNew($category, $position, $utc_created)
    {
        try {
            $sql = "INSERT INTO pages_categories 
			(category, position, utc_created) VALUES
			(:category, :position, :utc_created)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->bindParam(':position', $position, PDO::PARAM_INT);
            $stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('pages_categories_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $pages_categories_id
     * @param int $position
     * @param string $category
     * @param string $utc_modified
     * @return int
     */
    public function setPagesCategoriesUpdate($pages_categories_id, $category, $position, $utc_created)
    {
        try {
            $sql = "UPDATE pages_categories 
            SET category = :category,
            position = :position,
            utc_modified =:utc_modified
            WHERE pages_categories_id =:pages_categories_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pages_categories_id', $pages_categories_id, PDO::PARAM_INT);
            $stmt->bindParam(':position', $position, PDO::PARAM_INT);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $pages_categories_id
     * @return bool
     */
    public function setPagesCategoriesDelete($pages_categories_id)
    {
        try {
            $sql = "DELETE FROM pages_categories 
			WHERE pages_categories_id = :pages_categories_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pages_categories_id', $pages_categories_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

}

?>