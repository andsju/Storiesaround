<?php

/**
 * Class PagesRights
 */
class PagesRights extends Pages
{
    /**
     * PagesRights constructor
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
     * @return array
     */
    public function getPagesGroupsRights($pages_id)
    {
        $sql = "
		SELECT pages_rights.rights_read, pages_rights.rights_edit, pages_rights.rights_create, pages_rights.groups_id 
		FROM pages_rights 
		INNER JOIN pages
		ON pages_rights.pages_id = pages.pages_id
		WHERE pages_rights.pages_id = :pages_id
		AND pages_rights.groups_id != 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_id
     * @param int $groups_id
     * @return mixed
     */
    public function getPagesGroupsRightsExists($pages_id, $groups_id)
    {
        $sql = "
		SELECT pages_rights.groups_id 
		FROM pages_rights 
		INNER JOIN pages
		ON pages_rights.pages_id = pages.pages_id
		WHERE pages_rights.pages_id = :pages_id
		AND pages_rights.groups_id = :groups_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_id
     * @return array
     */
    public function getPagesGroupsRightsMeta($pages_id)
    {
        $sql = "
		SELECT pages_rights.pages_rights_id, pages_rights.rights_read, pages_rights.rights_edit, pages_rights.rights_create, pages_rights.groups_id, groups.title, groups.description  
		FROM pages_rights 
		INNER JOIN pages
		ON pages_rights.pages_id = pages.pages_id
		INNER JOIN groups
		ON pages_rights.groups_id = groups.groups_id
		WHERE pages_rights.pages_id = :pages_id
		AND pages_rights.groups_id != 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_id
     * @param int $users_id
     * @return mixed
     */
    public function getPagesUsersRights($pages_id, $users_id)
    {

        $sql = "
		SELECT pages_rights.rights_read, pages_rights.rights_edit, pages_rights.rights_create
		FROM pages_rights 
		INNER JOIN pages
		ON pages_rights.pages_id = pages.pages_id
		WHERE pages_rights.pages_id = :pages_id
		AND pages_rights.users_id = :users_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_id
     * @return array
     */
    public function getPagesUsersRightsMeta($pages_id)
    {
        $sql = "
		SELECT pages_rights.pages_rights_id, pages_rights.rights_read, pages_rights.rights_edit, pages_rights.rights_create, users.first_name, users.last_name, email
		FROM pages_rights
		INNER JOIN pages
		ON pages_rights.pages_id = pages.pages_id
		INNER JOIN users
		ON  users.users_id = pages_rights.users_id
		WHERE pages_rights.pages_id = :pages_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_id
     * @param int $users_id
     * @return string
     */
    public function setPagesUsersRightsNew($pages_id, $users_id)
    {
        try {
            $sql_insert = "INSERT INTO pages_rights 
			(pages_id, users_id) VALUES
			(:pages_id, :users_id)";

            $stmt = $this->db->prepare($sql_insert);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId("pages_rights_id");

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $users_id
     * @return string
     */
    public function setPagesUsersRightsNewReadEditCreate($pages_id, $users_id)
    {
        try {
            $sql_insert = "INSERT INTO pages_rights 
			(pages_id, users_id, rights_read, rights_edit, rights_create) VALUES
			(:pages_id, :users_id, 1, 1, 1)";

            $stmt = $this->db->prepare($sql_insert);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId("pages_rights_id");

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_id
     * @param int $groups_id
     * @return int
     */
    public function setPagesGroupsRightsNew($pages_id, $groups_id)
    {
        try {
            $sql_insert = "INSERT INTO pages_rights 
			(pages_id, groups_id) VALUES
			(:pages_id, :groups_id)";

            $stmt = $this->db->prepare($sql_insert);
            $stmt->bindParam(":pages_id", $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(":groups_id", $groups_id, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId("pages_rights_id");

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $pages_rights_id
     * @return bool
     */
    public function setPagesUsersRightsDelete($pages_rights_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM pages_rights WHERE pages_rights_id =:pages_rights_id");
            $stmt->bindParam(":pages_rights_id", $pages_rights_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param $pages_rights_id
     * @param $r
     * @param $value
     * @return bool
     */
    public function setPagesUsersRightsUpdate($pages_rights_id, $r, $value)
    {
        try {
            $sql_update = "UPDATE pages_rights
			SET $r = :value
			WHERE pages_rights_id = :pages_rights_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":pages_rights_id", $pages_rights_id, PDO::PARAM_INT);
            $stmt->bindParam(":value", $value, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }
}

?>