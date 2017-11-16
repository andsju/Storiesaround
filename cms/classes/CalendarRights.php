<?php

/**
 * Class CalendarRights
 */
class CalendarRights extends Calendar
{

    /**
     * CalendarRights constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     * @param int $calendar_categories_id
     * @return array
     */
    public function getCalendarGroupsRights($calendar_categories_id)
    {
        $sql = "
		SELECT calendar_categories_rights.rights_read, calendar_categories_rights.rights_edit, calendar_categories_rights.rights_create, calendar_categories_rights.groups_id 
		FROM calendar_categories_rights 
		INNER JOIN calendar_categories
		ON calendar_categories_rights.calendar_categories_id = calendar_categories.calendar_categories_id
		WHERE calendar_categories_rights.calendar_categories_id = :calendar_categories_id
		AND calendar_categories_rights.groups_id != 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $calendar_categories_id
     * @param int $groups_id
     * @return mixed
     */
    public function getCalendarGroupsRightsExists($calendar_categories_id, $groups_id)
    {
        $sql = "
		SELECT calendar_categories_rights.groups_id 
		FROM calendar_categories_rights 
		INNER JOIN calendar_categories
		ON calendar_categories_rights.calendar_categories_id = calendar_categories.calendar_categories_id
		WHERE calendar_categories_rights.calendar_categories_id = :calendar_categories_id
		AND calendar_categories_rights.groups_id = :groups_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $calendar_categories_id
     * @return array
     */
    public function getCalendarGroupsRightsMeta($calendar_categories_id)
    {
        $sql = "
		SELECT calendar_categories_rights.calendar_categories_rights_id, calendar_categories_rights.rights_read, calendar_categories_rights.rights_edit, calendar_categories_rights.rights_create, calendar_categories_rights.groups_id, groups.title, groups.description  
		FROM calendar_categories_rights 
		INNER JOIN calendar_categories
		ON calendar_categories_rights.calendar_categories_id = calendar_categories.calendar_categories_id
		INNER JOIN groups
		ON calendar_categories_rights.groups_id = groups.groups_id
		WHERE calendar_categories_rights.calendar_categories_id = :calendar_categories_id
		AND calendar_categories_rights.groups_id != 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $calendar_categories_id
     * @param int $users_id
     * @return mixed
     */
    public function getCalendarUsersRights($calendar_categories_id, $users_id)
    {
        $sql = "
		SELECT calendar_categories_rights.rights_read, calendar_categories_rights.rights_edit, calendar_categories_rights.rights_create
		FROM calendar_categories_rights 
		INNER JOIN calendar_categories
		ON calendar_categories_rights.calendar_categories_id = calendar_categories.calendar_categories_id
		WHERE calendar_categories_rights.calendar_categories_id = :calendar_categories_id
		AND calendar_categories_rights.users_id = :users_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $calendar_categories_id
     * @return array
     */
    public function getCalendarUsersRightsMeta($calendar_categories_id)
    {
        $sql = "
		SELECT calendar_categories_rights.calendar_categories_rights_id, calendar_categories_rights.rights_read, calendar_categories_rights.rights_edit, calendar_categories_rights.rights_create, users.first_name, users.last_name, email
		FROM calendar_categories_rights
		INNER JOIN calendar_categories
		ON calendar_categories_rights.calendar_categories_id = calendar_categories.calendar_categories_id
		INNER JOIN users
		ON  users.users_id = calendar_categories_rights.users_id
		WHERE calendar_categories_rights.calendar_categories_id = :calendar_categories_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calendar_categories_id', $calendar_categories_id, PDO::PARAM_INT);

        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $calendar_categories_id
     * @param int $users_id
     * @return string
     */
    public function setcalendarUsersRightsNew($calendar_categories_id, $users_id)
    {
        try {
            $sql_insert = "INSERT INTO calendar_categories_rights 
			(calendar_categories_id, users_id) VALUES
			(:calendar_categories_id, :users_id)";

            $stmt = $this->db->prepare($sql_insert);
            $stmt->bindParam(":calendar_categories_id", $calendar_categories_id, PDO::PARAM_INT);
            $stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId("calendar_categories_rights_id");
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return null;
        }
    }

    /**
     * @param int $calendar_categories_id
     * @param int $groups_id
     * @return string
     */
    public function setcalendarGroupsRightsNew($calendar_categories_id, $groups_id)
    {
        try {
            $sql_insert = "INSERT INTO calendar_categories_rights 
			(calendar_categories_id, groups_id) VALUES
			(:calendar_categories_id, :groups_id)";

            $stmt = $this->db->prepare($sql_insert);
            $stmt->bindParam(":calendar_categories_id", $calendar_categories_id, PDO::PARAM_INT);
            $stmt->bindParam(":groups_id", $groups_id, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId("calendar_categories_rights_id");
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $calendar_categories_rights_id
     * @return bool
     */
    public function setcalendarUsersRightsDelete($calendar_categories_rights_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM calendar_categories_rights WHERE calendar_categories_rights_id =:calendar_categories_rights_id");
            $stmt->bindParam(":calendar_categories_rights_id", $calendar_categories_rights_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $calendar_categories_rights_id
     * @param $r
     * @param int $value
     * @return bool
     */
    public function setcalendarUsersRightsUpdate($calendar_categories_rights_id, $r, $value)
    {
        try {
            $sql_update = "UPDATE calendar_categories_rights
			SET $r = :value
			WHERE calendar_categories_rights_id = :calendar_categories_rights_id";

            $stmt = $this->db->prepare($sql_update);
            $stmt->bindParam(":calendar_categories_rights_id", $calendar_categories_rights_id, PDO::PARAM_INT);
            $stmt->bindParam(":value", $value, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

}

?>