<?php

/**
 * Class Users
 */
class Users extends Database
{

    /**
     * Users constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     * @param string $email
     * @param string $pass_hash
     * @param string $first_name
     * @param string $last_name
     * @param string $user_name
     * @param string $utc_created
     * @return int
     */
    public function setUsersAdmin($email, $pass_hash, $first_name, $last_name, $user_name, $utc_created)
    {
        try {
            $sql = "INSERT INTO users 
			(email, pass_hash, first_name, last_name, user_name, activation_code, role_CMS, status, utc_created) VALUES
			(:email, :pass_hash, :first_name, :last_name, :user_name, NULL, 6, 2, :utc_created)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':pass_hash', $pass_hash, PDO::PARAM_STR);
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
            $stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('users_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param string $email
     * @param string $pass_hash
     * @param string $first_name
     * @param string $last_name
     * @param string $activation_code
     * @param int $status
     * @param string $utc_created
     * @return int
     */
    public function setUsersNew($email, $pass_hash, $first_name, $last_name, $activation_code, $status, $utc_created)
    {
        try {
            $sql = "INSERT INTO users 
			(email, pass_hash, first_name, last_name, activation_code, status, utc_created) VALUES
			(:email, :pass_hash, :first_name, :last_name, :activation_code, :status, :utc_created)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':pass_hash', $pass_hash, PDO::PARAM_STR);
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindParam(':activation_code', $activation_code, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':utc_created', $utc_created, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('users_id');
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param string $search
     * @return array
     */
    public function getUsersSearch($search)
    {
        $sql = "SELECT CONCAT(first_name, ' ', last_name, ', ', email) AS user
		FROM users
		WHERE first_name LIKE :search
		OR 
		last_name LIKE :search
		OR 
		email LIKE :search
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
    public function getUsersSearchWords($search)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $first_names = implode(' OR first_name LIKE ', $query_parts);
        $last_names = implode(' OR last_name LIKE ', $query_parts);
        $email = implode(' OR email LIKE ', $query_parts);
        $user_name = implode(' OR user_name LIKE ', $query_parts);

        $sql = "SELECT users_id, first_name, last_name, email, user_name, role_CMS, status, utc_created, utc_lastvisit  
		FROM users 
		WHERE first_name LIKE {$first_names}
		OR last_name LIKE {$last_names}
		OR email LIKE {$email}
		OR user_name LIKE {$user_name}
		LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $search
     * @return array
     */
    public function getUsersSearchWordsRelevance($search)
    {
        $query_parts = array();
        $words = preg_replace('/\s+/', ' ', $search);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $query_parts[] = "'%" . $word . "%'";
        }
        $first_names = implode(' OR first_name LIKE ', $query_parts);
        $last_names = implode(' OR last_name LIKE ', $query_parts);
        $emails = implode(' OR email LIKE ', $query_parts);
        $user_names = implode(' OR user_name LIKE ', $query_parts);

        $ws = implode(' ', $words);
        $ws = trim($ws);

        $sql = "SELECT users_id, first_name, last_name, email, user_name, role_CMS, status, utc_created, utc_lastvisit, 
		MATCH(first_name, last_name, email, user_name) AGAINST('($ws)' IN NATURAL LANGUAGE MODE) AS relevance
		FROM users 
		WHERE 
		( MATCH(first_name, last_name, email, user_name) AGAINST('($ws)' IN NATURAL LANGUAGE MODE)
			OR first_name LIKE {$first_names}
			OR last_name LIKE {$last_names}
			OR email LIKE {$emails} 
			OR user_name LIKE {$user_names} 
		)
		";

        $sql .= " ORDER BY ";
        $sql .= " first_name LIKE {$first_names} DESC, ";
        $sql .= " relevance DESC ";
        $sql .= " LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $role_CMS
     * @return array
     */
    public function getUsersRole($role_CMS)
    {
        $sql = "SELECT users_id, first_name, last_name, email, user_name, role_CMS, status, utc_created, utc_lastvisit  
		FROM users ";
        if ($role_CMS) {
            $sql .= " WHERE role_CMS = :role_CMS ";
        }
        $sql .= " LIMIT 1000";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':role_CMS', $role_CMS, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $email
     * @param string $pass_hash
     * @return bool
     */
    public function setUsersResetPassword($email, $pass_hash)
    {
        try {
            $sql = "UPDATE users
			SET pass_hash = :pass_hash
			WHERE email = :email";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pass_hash', $pass_hash, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $users_id
     * @param string $first_name
     * @param string $last_name
     * @param string $email
     * @param string $user_name
     * @param string $language
     * @param string $phone
     * @param string $mobile
     * @param string $city
     * @param string $postal
     * @param string $address
     * @param string $country
     * @param string $comment
     * @return bool
     */
    public function setUsersStatus($users_id, $first_name, $last_name, $email, $user_name, $language, $phone, $mobile, $city, $postal, $address, $country, $comment)
    {
        try {
            $sql = "UPDATE users
			SET first_name = :first_name, 
			last_name = :last_name, 
			email = :email, 
			user_name = :user_name,  
			language = :language, 
			phone = :phone, 
			mobile = :mobile, 
			city = :city, 
			postal = :postal, 
			address = :address, 
			country = :country, 
			comment = :comment, 
			utc_modified = UTC_TIMESTAMP()
			WHERE users_id = :users_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
            $stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
            $stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
            $stmt->bindParam(":language", $language, PDO::PARAM_STR);
            $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
            $stmt->bindParam(":mobile", $mobile, PDO::PARAM_STR);
            $stmt->bindParam(":city", $city, PDO::PARAM_STR);
            $stmt->bindParam(":postal", $postal, PDO::PARAM_STR);
            $stmt->bindParam(":address", $address, PDO::PARAM_STR);
            $stmt->bindParam(":country", $country, PDO::PARAM_STR);
            $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $users_id
     * @param int $role_CMS
     * @param int $role_LMS
     * @return bool
     */
    public function setUsersRoles($users_id, $role_CMS, $role_LMS)
    {
        try {
            $sql = "UPDATE users
			SET role_CMS = :role_CMS,  
			role_LMS = :role_LMS, 
			utc_modified = UTC_TIMESTAMP()
			WHERE users_id = :users_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
            $stmt->bindParam(":role_CMS", $role_CMS, PDO::PARAM_INT);
            $stmt->bindParam(":role_LMS", $role_LMS, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $users_id
     * @param int $profile_edit
     * @param int $debug
     * @return bool
     */
    public function setUsersRights($users_id, $profile_edit, $debug)
    {
        try {
            $sql = "UPDATE users
			SET profile_edit = :profile_edit,  
			debug = :debug,  
			utc_modified = UTC_TIMESTAMP()
			WHERE users_id = :users_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
            $stmt->bindParam(":profile_edit", $profile_edit, PDO::PARAM_INT);
            $stmt->bindParam(":debug", $debug, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $users_id
     * @param int $status
     * @return bool
     */
    public function setUsersAccountStatus($users_id, $status)
    {
        try {
            $sql = "UPDATE users
			SET status = :status, 
			utc_modified = UTC_TIMESTAMP()
			WHERE users_id = :users_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param string $email
     * @param string $activation_code
     * @return bool
     */
    public function setUsersActivate($email, $activation_code)
    {
        try {
            $sql = "UPDATE users
			SET activation_code = NULL,
			status = 2
			WHERE (email = :email AND activation_code = :activation_code)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":activation_code", $activation_code, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $users_id
     * @param string $pass_new
     * @return bool
     */
    public function setUsersPassword($users_id, $pass_new)
    {
        try {
            $sql = "UPDATE users
			SET pass_hash = :pass_new
			WHERE users_id = :users_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
            $stmt->bindParam(":pass_new", $pass_new, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $users_id
     * @param string $utc_lastvisit
     * @param string $token
     * @return bool
     */
    public function setUsersLastvisit($users_id, $utc_lastvisit, $token)
    {
        try {
            $sql = "UPDATE users
			SET utc_lastvisit = :utc_lastvisit,
			login_count = login_count + 1,
			last_token = :token
			WHERE users_id = :users_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":users_id", $users_id, PDO::PARAM_INT);
            $stmt->bindParam(":utc_lastvisit", $utc_lastvisit, PDO::PARAM_STR);
            $stmt->bindParam(":token", $token, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $users_id
     * @return mixed
     */
    public function getUsersSettings($users_id)
    {
        $sql = "SELECT 
		first_name, last_name, email, user_name, utc_lastvisit, utc_created, utc_modified,
		role_CMS, role_LMS, language, status, phone, mobile, postal, city, country, address, comment, activation_code, login_count, profile_edit, debug
		FROM users
		WHERE users_id = :users_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param int $users_id
     * @param string $user_name
     * @return mixed
     */
    public function getUsersIdUsername($users_id, $user_name)
    {
        $sql = "SELECT 
		user_name, users_id
		FROM users
		WHERE user_name = :user_name
		AND users_id != :users_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $email
     * @return mixed
     */
    public function getUsersEmail($email)
    {
        $sql = "SELECT 
		users_id, email
		FROM users
		WHERE email = :email";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $email
     * @return mixed
     */
    public function getUsersLoginEmail($email)
    {
        $sql = "SELECT 
		pass_hash, users_id, first_name, last_name, email, user_name, activation_code, utc_lastvisit, 
		role_CMS, role_LMS, language, status, debug
		FROM users
		WHERE email = :email";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $user_name
     * @return mixed
     */
    public function getUsersLoginUsername($user_name)
    {
        $sql = "SELECT 
		pass_hash, users_id, first_name, last_name, email, user_name, activation_code, utc_lastvisit, 
		role_CMS, role_LMS, language, status, debug
		FROM users
		WHERE user_name = :user_name";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $utc_lastvisit
     * @return array
     */
    public function getUsersOnline($utc_lastvisit)
    {
        $sql = "SELECT CONCAT( first_name, ' ', last_name, ', ', email ) AS name
		FROM users
		WHERE utc_lastvisit > :utc_lastvisit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":utc_lastvisit", $utc_lastvisit, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $token
     * @return mixed
     */
    public function getUsersToken($token)
    {
        $sql = "SELECT CONCAT( first_name, ' ', last_name, ', ', email ) AS name
		FROM users
		WHERE last_token = :token";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":token", $token, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}

?>