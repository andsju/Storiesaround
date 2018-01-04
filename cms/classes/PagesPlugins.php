<?php

/**
 * Class PagesPlugins
 */
class PagesPlugins extends Plugins
{

    /**
     * PagesPlugins constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }

    /**
     * @param int $pages_id
     * @param int $plugins_id
     * @param string $utc_modified
     * @return string
     */
    public function setPagesPlugins($pages_id, $plugins_id, $utc_modified)
    {
        $stmt = $this->db->prepare("DELETE FROM pages_plugins WHERE pages_id = :pages_id");
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            try {
                $sql_insert = "INSERT INTO pages_plugins 
				(pages_id, plugins_id, utc_modified) VALUES
				(:pages_id, :plugins_id, :utc_modified)";

                $stmt = $this->db->prepare($sql_insert);
                $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
                $stmt->bindParam(':plugins_id', $plugins_id, PDO::PARAM_INT);
                $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
                $stmt->execute();
                return $this->db->lastInsertId('pages_plugins_id');

            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }


    /**
     * @param int $pages_id
     * @return bool
     */
    public function deletePagesPlugins($pages_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages_plugins WHERE pages_id = :pages_id");
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @return mixed
     */
    public function getPagesPlugins($pages_id)
    {
        $sql = "
		SELECT plugins.plugins_title, plugins.plugins_active
		FROM plugins INNER JOIN pages_plugins
		ON plugins.plugins_id=pages_plugins.plugins_id
		WHERE pages_plugins.pages_id = :pages_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
}

?>

