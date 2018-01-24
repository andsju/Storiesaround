<?php

/**
 * Class Site
 */
class Site extends Database
{

    /**
     * Site constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     * @return mixed
     */
    public function getSiteDatabase()
    {
        $sql = "SELECT DATABASE()";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @return array
     */
    public function getSiteColumns()
    {
        $sql = "SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name='site'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @return array
     */
    public function getSiteDatabaseEmpty()
    {
        $sql = "SELECT * FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema = DATABASE() ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @return mixed
     */
    public function getSiteDatabaseNow()
    {
        $sql = "SELECT NOW() AS dt";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @param string $site_name
     * @param string $site_domain_url
     * @param string $site_domain
     * @param string $site_email
     * @param string $site_copyright
     * @param string $site_theme
     * @param string $site_language
     * @param string $utc_modified
     * @return string
     */
    public function setSiteInstall($site_name, $site_domain_url, $site_domain, $site_email, $site_copyright, $site_theme, $site_language, $utc_modified)
    {
        try {
            $sql = "INSERT INTO site 
			(site_name, site_domain_url, site_domain, site_email, site_copyright, site_theme, site_language, utc_modified) VALUES
			(:site_name, :site_domain_url, :site_domain, :site_email, :site_copyright, :site_theme, :site_language, :utc_modified)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':site_name', $site_name, PDO::PARAM_STR);
            $stmt->bindParam(':site_domain_url', $site_domain_url, PDO::PARAM_STR);
            $stmt->bindParam(':site_domain', $site_domain, PDO::PARAM_STR);
            $stmt->bindParam(':site_email', $site_email, PDO::PARAM_STR);
            $stmt->bindParam(':site_copyright', $site_copyright, PDO::PARAM_STR);
            $stmt->bindParam(':site_theme', $site_theme, PDO::PARAM_STR);
            $stmt->bindParam(':site_language', $site_language, PDO::PARAM_STR);
            $stmt->bindParam(':utc_modified', $utc_modified, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('site_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @return mixed
     */
    public function getSite()
    {
        try {
            $sql = "SELECT * 
			FROM site 
			ORDER BY site_id
			LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getSiteSMTP()
    {
        try {
            $sql = "SELECT site_smtp_server, site_smtp_port, site_smtp_username, site_smtp_password, site_smtp_authentication
			FROM site 
			ORDER BY site_id
			LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param id $site_id
     * @param string $site_name
     * @param string $site_slogan
     * @param string $site_domain_url
     * @param string $site_domain
     * @param string $site_email
     * @param string $site_copyright
     * @param string $utc_modified
     * @return bool
     */
    public function setSiteGeneralSettings($site_id, $site_name, $site_slogan, $site_domain_url, $site_domain, $site_email, $site_copyright, $utc_modified)
    {
        try {
            $sql = "UPDATE site
			SET site_name = :site_name,
			site_slogan = :site_slogan,
			site_domain_url = :site_domain_url,
			site_domain = :site_domain,
			site_email = :site_email,
			site_copyright = :site_copyright,
			utc_modified = :utc_modified
			WHERE site_id = :site_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":site_id", $site_id, PDO::PARAM_INT);
            $stmt->bindParam(":site_name", $site_name, PDO::PARAM_STR);
            $stmt->bindParam(":site_slogan", $site_slogan, PDO::PARAM_STR);
            $stmt->bindParam(":site_domain_url", $site_domain_url, PDO::PARAM_STR);
            $stmt->bindParam(":site_domain", $site_domain, PDO::PARAM_STR);
            $stmt->bindParam(":site_email", $site_email, PDO::PARAM_STR);
            $stmt->bindParam(":site_copyright", $site_copyright, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $site_id
     * @param string $site_theme
     * @param string $site_ui_theme
     * @param string $site_template_default
     * @param string $site_template_sidebar_width
     * @param int $site_template_content_padding
     * @param int $site_navigation_horizontal
     * @param int $site_navigation_vertical
     * @param int $site_navigation_vertical_sidebar
     * @param string $utc_modified
     * @return bool
     */
    public function setSiteDesign($site_id, $site_wrapper_page_width, $site_theme, $site_ui_theme, $site_template_default, $site_template_sidebar_width, $site_template_content_padding, $site_navigation_horizontal, $site_navigation_vertical, $site_navigation_vertical_sidebar, $utc_modified)
    {
        try {
            $sql = "UPDATE site
			SET site_theme = :site_theme,
            site_wrapper_page_width = :site_wrapper_page_width,
			site_ui_theme = :site_ui_theme,
			site_template_default = :site_template_default,
            site_template_sidebar_width = :site_template_sidebar_width,
			site_template_content_padding = :site_template_content_padding,
			site_navigation_horizontal = :site_navigation_horizontal,
			site_navigation_vertical = :site_navigation_vertical,
			site_navigation_vertical_sidebar = :site_navigation_vertical_sidebar,
			utc_modified = :utc_modified
			WHERE site_id = :site_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":site_id", $site_id, PDO::PARAM_INT);
            $stmt->bindParam(":site_wrapper_page_width", $site_wrapper_page_width, PDO::PARAM_INT);
            $stmt->bindParam(":site_theme", $site_theme, PDO::PARAM_STR);
            $stmt->bindParam(":site_ui_theme", $site_ui_theme, PDO::PARAM_STR);            
            $stmt->bindParam(":site_template_default", $site_template_default, PDO::PARAM_INT);
            $stmt->bindParam(":site_template_sidebar_width", $site_template_sidebar_width, PDO::PARAM_INT);
            $stmt->bindParam(":site_template_content_padding", $site_template_content_padding, PDO::PARAM_INT);
            $stmt->bindParam(":site_navigation_horizontal", $site_navigation_horizontal, PDO::PARAM_INT);
            $stmt->bindParam(":site_navigation_vertical", $site_navigation_vertical, PDO::PARAM_INT);
            $stmt->bindParam(":site_navigation_vertical_sidebar", $site_navigation_vertical_sidebar, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $site_id
     * @param int $site_account_registration
     * @param string $site_account_welcome_message
     * @param int $site_groups_default_id
     * @param string $utc_modified
     * @return bool
     */
    public function setSiteAccountSettings($site_id, $site_account_registration, $site_account_welcome_message, $site_groups_default_id, $utc_modified)
    {
        try {
            $sql = "UPDATE site
			SET site_account_registration = :site_account_registration,
			site_account_welcome_message = :site_account_welcome_message,
			site_groups_default_id = :site_groups_default_id,
			utc_modified = :utc_modified
			WHERE site_id = :site_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":site_id", $site_id, PDO::PARAM_INT);
            $stmt->bindParam(":site_account_registration", $site_account_registration, PDO::PARAM_INT);
            $stmt->bindParam(":site_account_welcome_message", $site_account_welcome_message, PDO::PARAM_STR);
            $stmt->bindParam(":site_groups_default_id", $site_groups_default_id, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $site_id
     * @param string $site_rss_description
     * @param string $site_publish_guideline
     * @param string $utc_modified
     * @return bool
     */
    public function setSiteContent($site_id, $site_header_image, $site_404, $site_rss_description, $site_publish_guideline, $utc_modified)
    {
        try {
            $sql = "UPDATE site
			SET site_rss_description = :site_rss_description,
            site_header_image = :site_header_image,
            site_404 = :site_404,
			site_publish_guideline = :site_publish_guideline,
			utc_modified = :utc_modified
			WHERE site_id = :site_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":site_id", $site_id, PDO::PARAM_INT);
            $stmt->bindParam(":site_header_image", $site_header_image, PDO::PARAM_STR);
            $stmt->bindParam(":site_404", $site_404, PDO::PARAM_STR);
            $stmt->bindParam(":site_rss_description", $site_rss_description, PDO::PARAM_STR);
            $stmt->bindParam(":site_publish_guideline", $site_publish_guideline, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $site_id
     * @param string $site_script
     * @param string $utc_modified
     * @return bool
     */
    public function setSiteScript($site_id, $site_script, $utc_modified)
    {
        try {
            $sql = "UPDATE site
			SET site_script = :site_script,
			utc_modified = :utc_modified
			WHERE site_id = :site_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":site_id", $site_id, PDO::PARAM_INT);
            $stmt->bindParam(":site_script", $site_script, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $site_id
     * @param string $site_meta_tags
     * @param string $utc_modified
     * @return bool
     */
    public function setSiteMeta($site_id, $site_meta_tags, $utc_modified)
    {
        try {
            $sql = "UPDATE site
			SET site_meta_tags = :site_meta_tags,
			utc_modified = :utc_modified
			WHERE site_id = :site_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":site_id", $site_id, PDO::PARAM_INT);
            $stmt->bindParam(":site_meta_tags", $site_meta_tags, PDO::PARAM_STR);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $site_id
     * @param int $site_maintenance
     * @param string $site_maintenance_message
     * @param int $site_error_mode
     * @param int $site_history_max
     * @param string $utc_modified
     * @return bool
     */
    public function setSiteMaintenance($site_id, $site_maintenance, $site_maintenance_message, $site_error_mode, $site_history_max, $utc_modified)
    {
        try {
            $sql = "UPDATE site
			SET site_maintenance = :site_maintenance,
			site_maintenance_message = :site_maintenance_message,
			site_error_mode = :site_error_mode,
			site_history_max = :site_history_max,
			utc_modified = :utc_modified
			WHERE site_id = :site_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":site_id", $site_id, PDO::PARAM_INT);
            $stmt->bindParam(":site_maintenance", $site_maintenance, PDO::PARAM_INT);
            $stmt->bindParam(":site_maintenance_message", $site_maintenance_message, PDO::PARAM_STR);
            $stmt->bindParam(":site_error_mode", $site_error_mode, PDO::PARAM_INT);
            $stmt->bindParam(":site_history_max", $site_history_max, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $site_id
     * @param string $site_country
     * @param string $site_language
     * @param string $site_lang
     * @param string $site_timezone
     * @param string $site_dateformat
     * @param string $site_timeformat
     * @param int $site_firstdayofweek
     * @param string $site_wysiwyg
     * @param int $site_seo_url
     * @param int $site_autosave
     * @param int $site_mail_method
     * @param string $site_smtp_server
     * @param int $site_smtp_port
     * @param string $site_smtp_username
     * @param string $site_smtp_password
     * @param int $site_smtp_authentication
     * @param int $site_smtp_debug
     * @param string $utc_modified
     * @return bool
     */
    public function setSiteConfiguration($site_id, $site_country, $site_language, $site_lang, $site_timezone, $site_dateformat, $site_timeformat, $site_firstdayofweek, $site_wysiwyg, $site_seo_url, $site_autosave, $site_mail_method, $site_smtp_server, $site_smtp_port, $site_smtp_username, $site_smtp_password, $site_smtp_authentication, $site_smtp_debug, $utc_modified)
    {
        try {
            $sql = "UPDATE site
			SET site_country = :site_country,
			site_language = :site_language,
			site_lang = :site_lang,
			site_timezone = :site_timezone,
			site_dateformat = :site_dateformat,
			site_timeformat = :site_timeformat,
			site_firstdayofweek = :site_firstdayofweek,
			site_wysiwyg = :site_wysiwyg,
			site_seo_url = :site_seo_url,
			site_autosave = :site_autosave, 
			site_mail_method = :site_mail_method,
			site_smtp_server = :site_smtp_server,
			site_smtp_port = :site_smtp_port,
			site_smtp_username = :site_smtp_username,
			site_smtp_password = :site_smtp_password,
			site_smtp_authentication = :site_smtp_authentication,
			site_smtp_debug = :site_smtp_debug,
			utc_modified = :utc_modified
			WHERE site_id = :site_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":site_id", $site_id, PDO::PARAM_INT);
            $stmt->bindParam(":site_country", $site_country, PDO::PARAM_STR);
            $stmt->bindParam(":site_language", $site_language, PDO::PARAM_STR);
            $stmt->bindParam(":site_lang", $site_lang, PDO::PARAM_STR);
            $stmt->bindParam(":site_timezone", $site_timezone, PDO::PARAM_STR);
            $stmt->bindParam(":site_dateformat", $site_dateformat, PDO::PARAM_STR);
            $stmt->bindParam(":site_timeformat", $site_timeformat, PDO::PARAM_STR);
            $stmt->bindParam(":site_firstdayofweek", $site_firstdayofweek, PDO::PARAM_INT);
            $stmt->bindParam(":site_wysiwyg", $site_wysiwyg, PDO::PARAM_STR);
            $stmt->bindParam(":site_seo_url", $site_seo_url, PDO::PARAM_INT);
            $stmt->bindParam(":site_autosave", $site_autosave, PDO::PARAM_INT);
            $stmt->bindParam(":site_mail_method", $site_mail_method, PDO::PARAM_INT);
            $stmt->bindParam(":site_smtp_server", $site_smtp_server, PDO::PARAM_STR);
            $stmt->bindParam(":site_smtp_port", $site_smtp_port, PDO::PARAM_INT);
            $stmt->bindParam(":site_smtp_username", $site_smtp_username, PDO::PARAM_INT);
            $stmt->bindParam(":site_smtp_password", $site_smtp_password, PDO::PARAM_INT);
            $stmt->bindParam(":site_smtp_authentication", $site_smtp_authentication, PDO::PARAM_INT);
            $stmt->bindParam(":site_smtp_debug", $site_smtp_debug, PDO::PARAM_INT);
            $stmt->bindParam(":utc_modified", $utc_modified, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param int $site_id
     * @param int $site_history_max
     * @param string $utc_modified
     */
    public function setSiteHistory($site_id, $site_history_max, $utc_modified)
    {
        $sql = "SELECT q.history_id
		FROM (SELECT history_id
		FROM history
		ORDER BY utc_datetime DESC LIMIT :site_history_max) q
		ORDER BY q.history_id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":site_history_max", $site_history_max, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $history_id = $result['history_id'];
            try {
                $sql = "DELETE FROM history
				WHERE history_id < $history_id";
                $stmt = $this->db->prepare($sql);

                $stmt->execute();
                $count = $stmt->rowCount();
                echo $count . ' rows deleted ';
            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            }
        }
    }


    /**
     * @param string $sql
     * @return bool
     */
    public function setSiteUpdate($sql)
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":sql", $sql, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }


    /**
     * @param array $sql  -> tbl_name | col_name | column_definition
     * @return bool
     */
    public function setSiteUpdateAlterAddColumn($sql)
    {
        $sql_check = 'SELECT COUNT(*) AS total FROM information_schema.columns WHERE TABLE_SCHEMA = "' . DB_NAME . '" AND COLUMN_NAME = "' . $sql['col_name'] . '" AND TABLE_NAME = "' . $sql['tbl_name'] . '"';
        $sql_alter_add_column = 'ALTER TABLE `' . $sql['tbl_name'] . '` ADD COLUMN `' . $sql['col_name'] . '` ' . $sql['column_definition'] . '';

        $stmt = $this->db->prepare($sql_check);
        $stmt->execute();
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        // run alter cmd if column does´nt exist
        if ($check['total'] <= 0) {
            try {

                $stmt = $this->db->prepare($sql_alter_add_column);
                return $stmt->execute();
            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }

    /**
     * @param array $sql -> tbl_name | col_name | col_name_new | column_definition
     * @return bool
     */
    public function setSiteUpdateAlterChangeColumn($sql)
    {
        $sql_check = 'SELECT COUNT(*) AS total FROM information_schema.columns WHERE TABLE_SCHEMA = "' . DB_NAME . '" AND COLUMN_NAME = "' . $sql['col_name'] . '" AND TABLE_NAME = "' . $sql['tbl_name'] . '"';
        $sql_alter_change_column = 'ALTER TABLE `' . $sql['tbl_name'] . '` CHANGE `' . $sql['col_name'] . '` `' . $sql['col_name_new'] . '` ' . $sql['column_definition'] . '';

        $stmt = $this->db->prepare($sql_check);
        $stmt->execute();
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        // run alter cmd if column exists
        if ($check['total'] > 0) {
            try {
                //echo $sql_alter_change_column;
                $stmt = $this->db->prepare($sql_alter_change_column);
                return $stmt->execute();
            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }


    /**
     * @param array $sql -> tbl_name | col_name | column_definition
     * @return bool
     */
    public function setSiteUpdateAlterModifyColumn($sql)
    {
        $sql_check = 'SELECT COUNT(*) AS total FROM information_schema.columns WHERE TABLE_SCHEMA = "' . DB_NAME . '" AND COLUMN_NAME = "' . $sql['col_name'] . '" AND TABLE_NAME = "' . $sql['tbl_name'] . '"';
        $sql_alter_modify_column = 'ALTER TABLE `' . $sql['tbl_name'] . '` MODIFY `' . $sql['col_name'] . '` ' . $sql['column_definition'] . '';

        $stmt = $this->db->prepare($sql_check);
        $stmt->execute();
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($check['total'] > 0) {
            try {
                $stmt = $this->db->prepare($sql_alter_modify_column);
                return $stmt->execute();
            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }


    /**
     * @param array $sql -> tbl_name | col_name
     * @return bool
     */
    public function setSiteUpdateAlterDropColumn($sql)
    {
        $sql_check = 'SELECT COUNT(*) AS total FROM information_schema.columns WHERE TABLE_SCHEMA = "' . DB_NAME . '" AND COLUMN_NAME = "' . $sql['col_name'] . '" AND TABLE_NAME = "' . $sql['tbl_name'] . '"';
        $sql_alter_drop_column = 'ALTER TABLE `' . $sql['tbl_name'] . '` DROP `' . $sql['col_name'] . '`';

        $stmt = $this->db->prepare($sql_check);
        $stmt->execute();
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($check['total'] > 0) {
            try {
                $stmt = $this->db->prepare($sql_alter_drop_column);
                return $stmt->execute();
            } catch (PDOException $e) {
                handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
                return false;
            }
        }
    }


}


?>