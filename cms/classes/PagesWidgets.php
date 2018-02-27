<?php

/**
 * Class PagesWidgets
 */
class PagesWidgets extends Widgets
{

    /**
     * PagesWidgets constructor
     */
    public function __construct()
    {
        /*
         * Call parent constructor and check for a database object
         */
        parent::__construct();
    }


    /**
     * @param int $pages_widgets_id
     * @return mixed
     */
    public function viewPagesWidgets($pages_widgets_id)
    {
        $sql = "
		SELECT pages_widgets.widgets_action, pages_widgets.widgets_id, widgets.widgets_action AS widgets_default_action, widgets.widgets_title, widgets.widgets_class
		FROM pages_widgets INNER JOIN widgets
		ON pages_widgets.widgets_id=widgets.widgets_id
		WHERE pages_widgets.pages_widgets_id = :pages_widgets_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_widgets_id', $pages_widgets_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_widgets_id
     * @param int $pages_id
     * @param string $width
     */
    public function showPagesWidgets($pages_widgets_id, $pages_id, $width)
    {
		$sql = "
		SELECT pages_widgets.widgets_action, pages_widgets.widgets_header, pages_widgets.widgets_footer, pages_widgets.widgets_id, widgets.widgets_action AS widgets_default_action, widgets.widgets_title, widgets.widgets_class
		FROM pages_widgets INNER JOIN widgets
		ON pages_widgets.widgets_id=widgets.widgets_id
		WHERE pages_widgets.pages_widgets_id = :pages_widgets_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_widgets_id', $pages_widgets_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $html = "";
        if ($row) {
            $w = new $row['widgets_class'];
            $widgets_action = $row['widgets_action'];
            $html .= '<div class="widgets-header">' . $row['widgets_header'] . '</div>';
            $w->{$row['widgets_class']}($widgets_action, $pages_widgets_id, $pages_id, $width);
            $html .= '<div class="widgets-footer">' . $row['widgets_footer'] . '</div>';
        }
        echo $html;
		
    }


    /**
     * @param int $pages_widgets_id
     * @return mixed
     */
    public function getPagesWidgetsId($pages_widgets_id)
    {
        $sql = "
		SELECT pages_widgets.widgets_action, pages_widgets.widgets_header, pages_widgets.widgets_footer, pages_widgets.area, pages_widgets.widgets_id, widgets.widgets_action AS widgets_default_action, widgets.widgets_title, widgets.widgets_class
		FROM pages_widgets INNER JOIN widgets
		ON pages_widgets.widgets_id=widgets.widgets_id
		WHERE pages_widgets.pages_widgets_id = :pages_widgets_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_widgets_id', $pages_widgets_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_id
     * @return array
     */
    public function getPagesWidgets($pages_id)
    {
        $sql = "
		SELECT pages_widgets_id, widgets.widgets_title, widgets.widgets_class, widgets.widgets_css, pages_widgets.widgets_action, pages_widgets.widgets_header, pages_widgets.widgets_footer, pages_widgets.area, pages.pages_id
		FROM pages_widgets
		LEFT JOIN pages ON pages_widgets.pages_id = pages.pages_id
		LEFT JOIN widgets ON pages_widgets.widgets_id = widgets.widgets_id
		WHERE pages_widgets.pages_id = :pages_id
		ORDER BY pages_widgets.position";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_widgets_id
     * @return mixed
     */
    public function getPagesWidgetsFireOff($pages_widgets_id)
    {
        $sql = "
		SELECT widgets_action, widgets_header, widgets_footer, area
		FROM pages_widgets 
		WHERE pages_widgets_id = :pages_widgets_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_widgets_id', $pages_widgets_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }


    /**
     * @param int $pages_widgets_id
     * @param string $widgets_action
     * @param string $widgets_header
     * @param string $widgets_footer
     */
    public function updatePagesWidgets($pages_widgets_id, $widgets_action, $widgets_header, $widgets_footer)
    {
        try {
            $sql = "
			UPDATE pages_widgets 
			SET widgets_action =:widgets_action,  
			widgets_header =:widgets_header, 
			widgets_footer =:widgets_footer 		
			WHERE pages_widgets_id =:pages_widgets_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":pages_widgets_id", $pages_widgets_id, PDO::PARAM_INT);
            $stmt->bindParam(":widgets_action", $widgets_action, PDO::PARAM_STR);
            $stmt->bindParam(":widgets_header", $widgets_header, PDO::PARAM_STR);
            $stmt->bindParam(":widgets_footer", $widgets_footer, PDO::PARAM_STR);
            $stmt->execute();

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }


    /**
     * @param int $pages_widgets_id
     * @param $area
     */
    public function updatePagesWidgetsArea($pages_widgets_id, $area)
    {
        try {
            $sql = "
			UPDATE pages_widgets 
			SET area =:area 
			WHERE pages_widgets_id =:pages_widgets_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":pages_widgets_id", $pages_widgets_id, PDO::PARAM_INT);
            $stmt->bindParam(":area", $area, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }


    /**
     * @param int $pages_id
     * @return string
     */
    public function getPagesWidgetsPending($pages_id)
    {
        $sql = "
		SELECT pages_widgets.pages_widgets_id, widgets.widgets_title AS title 
		FROM pages_widgets INNER JOIN widgets
		ON pages_widgets.widgets_id=widgets.widgets_id
		WHERE pages_widgets.pages_id = :pages_id
		AND pages_widgets.area = ''";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $html = "";
        if ($rows) {
            foreach ($rows as $row) {
                $html .= '<span id="' . $row['pages_widgets_id'] . '" class="toolbar_edit" title="' . $row['title'] . '"><button id="' . $row['pages_widgets_id'] . '" class="btn_widgets_edit">' . $row['title'] . '</button></span>';
            }
        }
        return $html;
    }


    /**
     * @param int $pages_id
     * @param string $area
     * @param int $position
     * @param int $pages_widgets_id
     */
    public function updatePagesWidgetsLayout($pages_id, $area, $position, $pages_widgets_id)
    {
        try {
            $stmt = $this->db->prepare('UPDATE pages_widgets SET area =:area, position =:position WHERE pages_widgets_id =:pages_widgets_id');
            $stmt->bindParam(':pages_widgets_id', $pages_widgets_id, PDO::PARAM_INT);
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':area', $area, PDO::PARAM_STR);
            $stmt->bindParam(':position', $position, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }

}

?>