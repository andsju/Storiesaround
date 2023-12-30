<?php

/**
 * Class Database
 */
class Database
{

    protected $db;

    /**
     * Database constructor
     */
    protected function __construct()
    {
        if (isset($db)) {
            if (is_object($db)) {
                $this->db = $db;
            }
        } else {

            // constants are defined in /sys/inc.db.php
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            try {

                // array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_PERSISTENT => true));
                $this->db = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                die;
            }
        }
    }

}

?>