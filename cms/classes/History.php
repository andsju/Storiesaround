<?php

/**
 * Class History
 */
class History extends Database
{

    /**
     * History constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     *
     * @param int $field_id - unique id
     * @param int $field - unique name
     * @param int $action - INSERT SELECT UPDATE DELETE
     * @param int $limit - limit result, 1,2,3....
     * @return array
     */
    public function getHistory($field_id, $field, $action, $limit)
    {
        $sql = "SELECT history.utc_datetime, history.description, history.session, CONCAT(users.first_name, ' ', users.last_name) AS name
		FROM history 
		INNER JOIN users
		ON
		history.users_id = users.users_id
		WHERE field_id = :field_id
		AND field = :field
		AND action = :action
		ORDER BY history.history_id DESC
		LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':field_id', $field_id, PDO::PARAM_INT);
        $stmt->bindParam(':field', $field, PDO::PARAM_STR);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     *
     * @param int $field_id - unique id
     * @param int $field - unique name
     * @return array
     */
    public function getHistorySession($field_id, $field)
    {
        $sql = "SELECT history.utc_datetime, history.session, CONCAT(users.first_name, ' ', users.last_name) AS name
		FROM history 
		INNER JOIN users
		ON
		history.users_id = users.users_id
		WHERE field_id = :field_id
		AND field = :field
		ORDER BY history.history_id DESC
		LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':field_id', $field_id, PDO::PARAM_INT);
        $stmt->bindParam(':field', $field, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     *
     * @param int $field_id - unique id
     * @param int $field - unique name
     * @param int $limit - limit result, 1,2,3....
     * @return array
     */
    public function getHistoryAll($field_id, $field, $limit)
    {
        $sql = "SELECT history.utc_datetime, history.description, history.session, CONCAT(users.first_name, ' ', users.last_name) AS name
		FROM history 
		INNER JOIN users
		ON
		history.users_id = users.users_id
		WHERE field_id = :field_id
		AND field = :field
		ORDER BY history.history_id DESC
		LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':field_id', $field_id, PDO::PARAM_INT);
        $stmt->bindParam(':field', $field, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $field_id
     * @param string $field
     * @param string $action
     * @param string $description
     * @param int $users_id
     * @param string $session
     * @param string $utc_datetime
     * @return string
     */
    public function setHistory($field_id, $field, $action, $description, $users_id, $session, $utc_datetime)
    {
        try {
            $sql = "INSERT INTO history 
			(field_id, field, action, description, users_id, session, utc_datetime) VALUES
			(:field_id, :field, :action, :description, :users_id, :session, :utc_datetime)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':field_id', $field_id, PDO::PARAM_INT);
            $stmt->bindParam(':field', $field, PDO::PARAM_STR);
            $stmt->bindParam(':action', $action, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
            $stmt->bindParam(':session', $session, PDO::PARAM_STR);
            $stmt->bindParam(':utc_datetime', $utc_datetime, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('history_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }

    }


    /**
     * @param string $search
     * @return array
     */
    public function getHistorySearch($search)
    {
        $sql = "SELECT history_id, field, action  
		FROM history 
		WHERE field LIKE :search
		OR action LIKE :search
		OR description LIKE :search
		LIMIT 50";

        $search = "%" . $search . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>