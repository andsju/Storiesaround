<?php

/**
 * Class HistoryEmail
 */
class HistoryEmail extends Database
{

    /**
     * HistoryEmail constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     * @param int $limit
     * @return array
     */
    public function getHistoryEmail($limit)
    {
        $sql = "SELECT * 
		FROM history_email 
		ORDER BY history_email_id DESC
		LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $email_to
     * @param string $email_from
     * @param string $email_subject
     * @param string $email_body
     * @param string $utc_datetime
     * @return string
     */
    public function setHistoryEmail($email_to, $email_from, $email_subject, $email_body, $utc_datetime)
    {
        try {
            $sql = "INSERT INTO history_email 
			(email_to, email_from, email_subject, email_body, utc_datetime) VALUES
			(:email_to, :email_from, :email_subject, :email_body, :utc_datetime)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email_to', $email_to, PDO::PARAM_STR);
            $stmt->bindParam(':email_from', $email_from, PDO::PARAM_STR);
            $stmt->bindParam(':email_subject', $email_subject, PDO::PARAM_STR);
            $stmt->bindParam(':email_body', $email_body, PDO::PARAM_STR);
            $stmt->bindParam(':utc_datetime', $utc_datetime, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('history_email_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return null;
        }
    }

}

?>