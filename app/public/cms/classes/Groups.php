<?php

/**
 * Class Groups
 */
class Groups extends Users
{

    /**
     * Groups constructor
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
    public function getGroupsSearch($search)
    {
        $sql = "SELECT CONCAT(title, ', ', description) AS groups
		FROM groups
		WHERE title LIKE :search
		OR 
		description LIKE :search
		LIMIT 50";

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
    public function getGroupsMetaSearch($search)
    {
        $sql = "SELECT CONCAT(title, ', ', description, ', ', groups_id) AS groups, groups_id
		FROM groups
		WHERE title LIKE :search
		OR 
		description LIKE :search
		LIMIT 50";

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
    public function getGroupsSearchWords($search)
    {
        $query_parts = array();
        $words = is_null($search) ? "" : preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $titles = implode(' OR title LIKE ', $query_parts);
        $descriptions = implode(' OR description LIKE ', $query_parts);

        $sql = "SELECT groups_id, title, description, active, utc_modified  
		FROM groups 
		WHERE title LIKE {$titles}
		OR description LIKE {$descriptions}
		LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $search
     * @return array
     */
    public function getGroupsDefaultSearchWords($search)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $titles = implode(' OR title LIKE ', $query_parts);
        $descriptions = implode(' OR description LIKE ', $query_parts);

        $sql = "SELECT groups_default_id, title, description, active, utc_modified  
		FROM groups_default 
		WHERE title LIKE {$titles}
		OR description LIKE {$descriptions}
		LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getGroupsAll()
    {
        $sql = "SELECT title, description, groups_id
		FROM groups
		LIMIT 100";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $groups_id
     * @return mixed
     */
    public function getGroups($groups_id)
    {
        $sql = "SELECT title, description, active
		FROM groups
		WHERE groups_id = :groups_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $groups_id
     * @return bool
     */
    public function deleteGroups($groups_id)
    {
        $members = $this->getUsersGroupsMembership($groups_id);
        if ($members == null) {
            try {

                $sql = "DELETE
				FROM groups
				WHERE groups_id = :groups_id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
                return $stmt->execute();

            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }


    /**
     * @param string $search
     * @return array
     */
    public function getGroupsDefaultSearch($search)
    {
        $sql = "SELECT CONCAT(title, ', ', description) AS groups_default
		FROM groups_default
		WHERE title LIKE :search
		OR 
		description LIKE :search
		LIMIT 50";

        $search = "%" . $search . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $users_id
     * @return array
     */
    public function getUsersGroupsMembership($users_id)
    {
        $sql = "SELECT groups_members.groups_members_id, groups.title, groups.description
				FROM groups
				LEFT JOIN groups_members ON groups.groups_id = groups_members.groups_id
				LEFT JOIN users ON users.users_id = groups_members.users_id
				WHERE groups_members.users_id = :users_id
				ORDER BY groups.title, groups.description";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $users_id
     * @return array
     */
    public function getUsersGroupsIdMembership($users_id)
    {
        $sql = "SELECT groups.groups_id
				FROM groups
				LEFT JOIN groups_members ON groups.groups_id = groups_members.groups_id
				LEFT JOIN users ON users.users_id = groups_members.users_id
				WHERE groups_members.users_id = :users_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $groups_id
     * @return array
     */
    public function getGroupsMembership($groups_id)
    {
        $sql = "SELECT users.users_id AS users_id
			FROM users
			LEFT JOIN groups_members ON users.users_id = groups_members.users_id
			WHERE groups_members.groups_id = :groups_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $groups_id
     * @return array
     */
    public function getGroupsMembershipAll($groups_id)
    {
        $sql = "SELECT users.users_id, users.first_name, users.last_name, users.email, users.user_name, users.email, users.utc_created, users.utc_lastvisit, users.role_CMS, users.role_LMS, users.status
			FROM users
			LEFT JOIN groups_members ON users.users_id = groups_members.users_id
			WHERE groups_members.groups_id = :groups_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $groups_id
     * @return array
     */
    public function getGroupsMembershipMeta($groups_id)
    {
        $sql = "SELECT users.users_id AS users_id, CONCAT(users.first_name, ' ', users.last_name, ', ', users.email ) AS name 
		FROM users
		LEFT JOIN groups_members ON users.users_id = groups_members.users_id
		WHERE groups_members.groups_id = :groups_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $groups_id
     * @param int $role
     * @param string $orderby
     * @param string $search
     * @return array
     */
    public function getGroupsPossibleMembershipMeta($groups_id, $role, $orderby, $search)
    {
        $s = explode(" ", $search);

        $sql = "SELECT users.users_id AS users_id, CONCAT(users.first_name, ' ', users.last_name, ', ', users.email ) AS name 
		FROM users";
        if ($groups_id) {
            $sql .= " LEFT JOIN groups_members ON users.users_id = groups_members.users_id
				WHERE groups_members.groups_id = :groups_id";
        } else {
            $sql .= " WHERE users.users_id > 0";
        }
        if ($role > 0) {
            $sql .= " AND users.login_count = :role";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':groups_id', $groups_id, PDO::PARAM_INT);
        $stmt->bindValue(':role', $role, PDO::PARAM_INT);
        $stmt->bindValue(':orderby', $orderby, PDO::PARAM_STR);
        $stmt->bindValue(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $groups_id
     * @return array
     */
    public function getGroupsDefaultMembershipMeta($groups_id)
    {
        $sql = "SELECT groups.groups_id, groups.title, groups_default.title AS groups_default_title
				FROM groups
				LEFT JOIN groups_default_members ON groups.groups_id = groups_default_members.groups_id
				LEFT JOIN groups_default ON groups_default_members.groups_default_id = groups_default.groups_default_id
				WHERE groups_default_members.groups_default_id = :groups_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $groups_default_id
     * @return array
     */
    public function getGroupsDefaultMembership($groups_default_id)
    {
        $sql = "SELECT groups.groups_id AS groups_id
				FROM groups
				LEFT JOIN groups_default_members ON groups.groups_id = groups_default_members.groups_id
				WHERE groups_default_members.groups_default_id = :groups_default_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':groups_default_id', $groups_default_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $groups_id
     * @param int $users_id
     * @return mixed
     */
    public function getGroupsMembershipUser($groups_id, $users_id)
    {
        $sql = "SELECT groups_members_id 
				FROM groups_members
				WHERE groups_id = :groups_id AND users_id = :users_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }


    /**
     * @param int $groups_id
     * @param int $users_id
     * @return string
     */
    public function setGroupsMembership($groups_id, $users_id)
    {
        try {
            $sql = "INSERT INTO groups_members
					(groups_id, users_id) 
					VALUES (:groups_id, :users_id)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
            $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId('groups_members_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $groups_members_id
     * @return bool
     */
    public function setGroupsMembershipDelete($groups_members_id)
    {
        try {
            $sql = "DELETE FROM groups_members WHERE groups_members_id =:groups_members_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':groups_members_id', $groups_members_id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $groups_id
     * @param int $users_id
     * @return bool
     */
    public function setGroupsMembershipDeleteThese($groups_id, $users_id)
    {
        try {
            $sql = "DELETE FROM groups_members WHERE groups_id =:groups_id AND users_id = :users_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
            $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @return array
     */
    public function getGroupsDefault()
    {
        try {
            $sql = "SELECT groups_default_id, title
			FROM groups_default";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return null;
        }
    }


    /**
     * @param int $groups_default_id
     * @return mixed
     */
    public function getGroupsDefaultMeta($groups_default_id)
    {
        try {
            $sql = "SELECT 
			title, description, active
			FROM groups_default
			WHERE groups_default_id = :groups_default_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':groups_default_id', $groups_default_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param string $title
     * @param string$description
     * @return string
     */
    public function setGroupsAdd($title, $description)
    {
        try {
            $sql = "INSERT INTO groups
			(title, description, utc_created)
			VALUES (:title, :description, UTC_TIMESTAMP())";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('groups_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param string $title
     * @param string$description
     * @param int $active
     * @param int $groups_id
     * @return bool
     */
    public function setGroups($title, $description, $active, $groups_id)
    {
        try {
            $sql = "UPDATE groups
			SET title = :title, 
			description = :description, 
			active = :active, 
			utc_modified = UTC_TIMESTAMP()
			WHERE groups_id = :groups_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':active', $active, PDO::PARAM_INT);
            $stmt->bindParam(':groups_id', $groups_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param string $title
     * @param string $description
     * @return string
     */
    public function setGroupsDefaultAdd($title, $description)
    {
        try {
            $sql = "INSERT INTO groups_default
			(title, description, utc_created)
			VALUES (:title, :description, UTC_TIMESTAMP())";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('groups_default_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return null;
        }
    }


    /**
     * @param string $title
     * @param string $description
     * @param int $active
     * @param int $groups_default_id
     * @return bool
     */
    public function setGroupsDefault($title, $description, $active, $groups_default_id)
    {
        try {
            $sql = "UPDATE groups_default
			SET title = :title, 
			description = :description, 
			active = :active, 
			utc_modified = UTC_TIMESTAMP()
			WHERE groups_default_id = :groups_default_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':active', $active, PDO::PARAM_INT);
            $stmt->bindParam(':groups_default_id', $groups_default_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }
}

?>