<?php

/**
 * Class Banners
 */
class Banners extends Database
{
    /**
     * Banners constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     * @param string $tag
     * @param string $dt
     * @param int $limit
     * @return array
     */
    public function getBannersActive($tag, $dt, $limit)
    {
        $sql = "SELECT *   
		FROM banners 
		WHERE tag LIKE :tag
		AND active = 1
		AND :dt BETWEEN utc_start AND utc_end
		ORDER BY RAND() LIMIT :limit";
        $tag = "%" . $tag . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
        $stmt->bindParam(':dt', $dt, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @return array
     */
    public function getBannersSearch($search)
    {
        $sql = "SELECT banners_id, name  
		FROM banners 
		WHERE name LIKE :search
		LIMIT 20";
        $search = "%" . $search . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    * @param string $search
    * @return array
    */
    public function getBannersSearchWords($search)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $names = implode(' OR name LIKE ', $query_parts);
        $tags = implode(' OR tag LIKE ', $query_parts);
        $files = implode(' OR file LIKE ', $query_parts);

        $sql = "SELECT banners_id, name, file, tag, area, active, url, utc_start, utc_end 
		FROM banners 
		WHERE name LIKE {$names}
		OR tag LIKE {$tags}
		OR file LIKE {$files}
		LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function getBannerId($id)
    {
        $sql = "SELECT * 
		FROM banners 
		WHERE banners_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $name
     * @param string $file
     * @param int $width
     * @param int $height
     * @param int $active
     * @return int
     */
    public function setBannersNew($name, $file, $width, $height, $active)
    {
        try {
            $sql = "INSERT INTO banners 
			(name, file, width, height, active) VALUES
			(:name, :file, :width, :height, :active)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':file', $file, PDO::PARAM_STR);
            $stmt->bindParam(':width', $width, PDO::PARAM_INT);
            $stmt->bindParam(':height', $height, PDO::PARAM_INT);
            $stmt->bindParam(':active', $active, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId('banners_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param $banners_id
     * @param $name
     * @param $file
     * @param $area
     * @param $header
     * @param $url
     * @param $url_target
     * @param $width
     * @param $height
     * @param $tag
     * @param $active
     * @param $utc_start
     * @param $utc_end
     * @return bool
     */
    public function setBanners($banners_id, $name, $file, $area, $header, $url, $url_target, $width, $height, $tag, $active, $utc_start, $utc_end)
    {
        try {
            $sql = "UPDATE banners
			SET name = :name,
			file  = :file,
			area = :area,
			header = :header,
			url = :url,
			url_target = :url_target,
			width = :width,
			height = :height,
			tag = :tag,
			active = :active,
			utc_start = :utc_start,
			utc_end = :utc_end
			WHERE banners_id = :banners_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':banners_id', $banners_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':file', $file, PDO::PARAM_STR);
            $stmt->bindParam(':area', $area, PDO::PARAM_STR);
            $stmt->bindParam(':header', $header, PDO::PARAM_STR);
            $stmt->bindParam(':url', $url, PDO::PARAM_STR);
            $stmt->bindParam(':url_target', $url_target, PDO::PARAM_STR);
            $stmt->bindParam(':width', $width, PDO::PARAM_INT);
            $stmt->bindParam(':height', $height, PDO::PARAM_INT);
            $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
            $stmt->bindParam(':active', $active, PDO::PARAM_INT);
            $stmt->bindParam(':utc_start', $utc_start, PDO::PARAM_STR);
            $stmt->bindParam(':utc_end', $utc_end, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param $banners_id
     * @return bool
     */
    public function deleteBanners($banners_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM banners WHERE banners_id =:banners_id");
            $stmt->bindParam(":banners_id", $banners_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

}

?>