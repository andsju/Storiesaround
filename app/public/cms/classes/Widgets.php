<?php

/**
 * Class Widgets
 */
class Widgets extends Database
{

    /**
     * Widgets constructor
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
     */
    public function show()
    {

        // get widgets in database
        $s = $this->getWidgetsInDatabase();

        // get widgets classes kept in folder /cms/widgets
        // echo as floating divs, clickable / drag-drop ...
        $p = CMS_ABSPATH . '/cms/widgets';

        //open directory
        if ($handle = opendir($p)) {

            // alphabetic order
            $files = array();
            while ($files[] = readdir($handle)) ;
            sort($files);
            closedir($handle);

            /* loop over widget directory */
            foreach ($files as $file) {

                // list all files in the current directory and strip out . and ..
                if ($file != "." && $file != "..") {

                    // get title from class name >> skip .class.php
                    $classname = substr($file, 0, -10);

                    // match class files with in folder width widgets in database
                    $id = $this->getWidgetsDatabaseId($classname);

                    if ($id) {

                        $widget = new $classname();

                        // echo active widgets
                        if ($id['widgets_active'] == 1) {

                            $a = $widget->info();
                            echo '<div class="toolbar_widgets_new" title="' . $a['description'] . '"><button id="btn_' . $classname . '" class="btn_default" style="font-size:0.8em;">' . $a['title'] . '</button></div>';
                            echo "\n";
                            ?>
                            <script>

                                $('#btn_<?php echo $classname;?>').click(function (event) {
                                    event.preventDefault();
                                    var btn = this.id;
                                    var widget = btn.substring(btn.indexOf("_") + 1);
                                    var action = "use_widgets";
                                    var token = $("#token").val();
                                    var pages_id = $("#pages_id").val();
                                    var pages_widgets_in_editor_id = $("#pages_widgets_id").val();
                                    if (!pages_widgets_in_editor_id) {
                                        $.ajax({
                                            type: 'POST',
                                            url: 'pages_edit_ajax.php',
                                            data: {
                                                action: action,
                                                token: token,
                                                pages_id: pages_id,
                                                widget: widget,
                                                pages_widgets_in_editor_id: pages_widgets_in_editor_id
                                            },
                                            success: function (newdata) {
                                                $("#widgets_stage").empty().append(newdata).hide().fadeIn('slow');
                                            }
                                        });
                                    } else {
                                        alert('Save and close widget in editor first');
                                    }
                                });

                            </script>

                            <?php

                        }

                    }
                }
            }
        }
    }

    /**
     *
     */
    public function getWidgetsInDatabase()
    {
        $sql = "SELECT widgets_id, widgets_class, widgets_active, utc_created FROM widgets";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * @param string $classname
     * @return mixed
     */
    public function getWidgetsDatabaseId($classname)
    {
        $sql = "SELECT widgets_id, widgets_active FROM widgets WHERE widgets_class = :classname";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':classname', $classname, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * @param $w_class
     * @param $pages_widgets_id
     */
    public function wform($w_class, $pages_widgets_id)
    {

        $class = new $w_class();
        $a = json_decode($class->default_objects(), true);
        //var_dump($a);

        // check if widgets action already is saved...
        $w = new PagesWidgets();
        $saved = $w->getPagesWidgetsFireOff($pages_widgets_id);
        // var_dump($a);

        $widgets_action = (strlen($saved['widgets_action']) > 1) ? $saved['widgets_action'] : null;
        $widgets_area = (strlen($saved['area']) > 1) ? $saved['area'] : null;

        if ($widgets_action) {
            $a = json_decode($widgets_action, true);
        }

        // help text, shown when user hover input text fields
        $help = $class->help();
        $arr_help = json_decode($help);

        function show_help($key, $arr_help)
        {
            $s = null;
            foreach ($arr_help as $help => $value) {
                $s = ($key == $help) ? $value : null;
                if ($s) {
                    break;
                }
            }
            return $s;
        }


        echo '<table><tr><td><label for="widgets_header">header</label><br />';
        echo '<input type="text" id="widgets_header" name="widgets_header" style="width:250px" maxlength="100" value="' . $saved['widgets_header'] . '" />';
        echo '</td><td><label for="widgets_footer">footer</label><br />';
        echo '<input type="text" id="widgets_footer" name="widgets_footer" style="width:250px" maxlength="100" value="' . $saved['widgets_footer'] . '" />';
        echo '</td></tr></table>';

        echo '<p style="font-size:0.8em;">widget fire-off (default): <i>' . $class->default_objects() . '</i></p>';
        foreach ($a as $key => $value) {
            $t = show_help($key, $arr_help);
            echo '<p><label for="' . $key . '">' . $key . '</label><br />';
            echo '<input type="text" id="' . $key . '" name="' . $key . '" style="width:90%;" maxlength="255" value="' . htmlentities($value) . '" title="' . $t . '" />';
            echo '</p>';
        }


        echo '<div style="padding:10px 0 10px 0;margin-bottom:20px;">';
        echo '<div style="float:left;margin-right:5px;"><span class="toolbar"><button id="btn_save_widget" name="btn_save_widget" type="submit">Save</button></span></div>';
        echo '<div style="float:left;margin-right:5px;"><span class="toolbar"><button id=' . $pages_widgets_id . ' class="btn_delete_widget" type="submit">Delete</button></span></div>';
        echo '<div style="float:left;margin-right:5px;"><span class="toolbar"><button id="btn_close_widget" name="btn_close_widget" type="submit">Close</button></span></div>';
        echo '<div style="float:left;padding-left:10px;"><span class="ajax_spinner_widgets_edit" style="display:none;"><img src="css/images/spinner.gif"></span><span class="ajax_status_widgets_edit" style="display:none;"></span></div>';

        echo '<div style="float:right;">';
        echo 'location: ';

        function is_selected($value = null, $check = null)
        {
            $s = ($value == $check) ? ' selected' : '';
            return $s;
        }

        $info = $class->info();
        $wcolumn = $info['column'];

        echo '<select name="widgets_area_target" id="widgets_area_target" class="code">';
        if ($wcolumn == 'content' || $wcolumn == '') {
            echo '<option value="widgets_content" ' . is_selected("widgets_content", $widgets_area) . '>content</option>';
        }
        if ($wcolumn == 'sidebar' || $wcolumn == '') {
            echo '<option value="widgets_right_sidebar" ' . is_selected("widgets_right_sidebar", $widgets_area) . '>right sidebar</option>';
        }
        if ($wcolumn == 'sidebar' || $wcolumn == '') {
            echo '<option value="widgets_left_sidebar" ' . is_selected("widgets_left_sidebar", $widgets_area) . '>left sidebar</option>';
        }
        echo '</select>';
        echo '<span class="toolbar" style="margin-left:5px;"><button id="btn_save_widget_to_area" name="btn_save_widget_to_area" type="submit">Add</button></span>';
        echo '</div>';
        echo '</div>';

    }

    public function wform_keys($w_class)
    {
        $class = new $w_class();
        $a = json_decode($class->default_objects(), true);
        //var_dump($a);
        return array_keys($a);
    }

    public function wform_keys_validate($w_class)
    {
        $class = new $w_class();
        $a = json_decode($class->default_objects_validate(), true);
        //var_dump($a);
        return $a;
    }

    public function wform_default($w_class)
    {
        $class = new $w_class();
        return $class;

    }

    /**
     * @param string $classname
     * @return null
     */
    public function getWidgetsId($classname)
    {
        $widgets_id = null;
        $sql = "SELECT widgets_id FROM widgets WHERE widgets_class = :classname";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':classname', $classname, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (count($row)) {
            $widgets_id = $row['widgets_id'];
        }
        return $widgets_id;
    }

    /**
     * @param string $widgets_id
     * @return mixed
     */
    public function getWidgetsClass($widgets_id)
    {
        $class = null;
        $sql = "SELECT widgets_class FROM widgets WHERE widgets_id = :widgets_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':widgets_id', $widgets_id, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (count($row)) {
            $widgets_class = $row['widgets_class'];
        }
        return $widgets_class;
    }

    /**
     * @param int $widgets_id
     * @param int $pages_id
     * @param string $widgets_action
     * @param string $area
     * @param int $position
     * @return string
     */
    public function savePagesWidgets($widgets_id, $pages_id, $widgets_action, $area, $position)
    {
        try {
            $sql = "INSERT INTO pages_widgets 
			(widgets_id, pages_id, widgets_action, area, position) VALUES
			(:widgets_id, :pages_id, :widgets_action, :area, :position)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':widgets_id', $widgets_id, PDO::PARAM_INT);
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':widgets_action', $widgets_action, PDO::PARAM_STR);
            $stmt->bindParam(':area', $area, PDO::PARAM_STR);
            $stmt->bindParam(':position', $position, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId('pages_widgets_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param int $widgets_id
     * @param int $widgets_active
     * @param string $widgets_css
     * @return bool
     */
    public function setWidgetsActivate($widgets_id, $widgets_active, $widgets_css)
    {
        try {
            $sql = "UPDATE widgets 		
			SET widgets_active = :widgets_active, 
			widgets_css = :widgets_css,
			utc_modified = UTC_TIMESTAMP()
			WHERE widgets_id = :widgets_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':widgets_active', $widgets_active, PDO::PARAM_INT);
            $stmt->bindParam(':widgets_id', $widgets_id, PDO::PARAM_INT);
            $stmt->bindParam(':widgets_css', $widgets_css, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     * @param tring $widgets_class
     * @return mixed
     */
    public function getWidgetsInstall($widgets_class)
    {
        $sql = "SELECT widgets_id FROM widgets WHERE widgets_class = :widgets_class";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':widgets_class', $widgets_class, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * @param string $widgets_class
     * @return string
     */
    public function setWidgetsInstall($widgets_class)
    {

        $w = new $widgets_class();
        $a = $w->info();
        $widgets_title = $a['title'];
        $widgets_css = $a['css'];

        try {
            $sql = "INSERT INTO widgets 
			(widgets_class, widgets_title, widgets_css, widgets_active, utc_created) VALUES
			(:widgets_class, :widgets_title, :widgets_css, 1, UTC_TIMESTAMP())";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':widgets_class', $widgets_class, PDO::PARAM_STR);
            $stmt->bindParam(':widgets_title', $widgets_title, PDO::PARAM_STR);
            $stmt->bindParam(':widgets_css', $widgets_css, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('widgets_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
            return false;
        }
    }

    /**
     *
     */
    public function initiate()
    {

        // widgets in database
        $s = $this->getWidgetsInDatabase();

        // get widgets classes kept in folder /cms/widgets
        $p = '../cms/widgets';

        //open directory
        if ($handle = opendir($p)) {

            echo '<table class="paging" width="100%">';
            echo '<thead>';
            echo '<tr class="ui-widget ui-widget-header">';
            echo '<th class="paging" width="35%">';
            echo '<b>widget</b>';
            echo '</td>';
            echo '<th class="paging" width="5%">';
            echo '<b>instances</b>';
            echo '</td>';
            echo '<th class="paging" width="5%">';
            echo '<b>pages</b>';
            echo '</td>';
            echo '<th class="paging" width="15%">';
            echo '<b>status</b>';
            echo '</td>';
            echo '<th class="paging" width="20%">';
            echo '<b>install date</b>';
            echo '</td>';
            echo '<th class="paging" width="20%">';
            echo '<b>action</b>';
            echo '</td>';
            echo '</tr>';
            echo '</thead>';

            /* loop over widget directory */
            while (false !== ($file = readdir($handle))) {
                echo '<tbody>';
                echo '<tr class="ui-widget ui-widget-content">';
                // list all files in the current directory and strip out . and ..
                if ($file != "." && $file != "..") {

                    echo '<td class="paging">';
                    // get title from class name >> skip .class.php

                    $classname = substr($file, 0, -10);
                    $widget = new $classname();
                    $a = $widget->info();
                    echo $classname;
                    echo '</td>';
                    echo '<td class="paging" style="text-align:right;">';
                    $id = $this->getWidgetsDatabaseId($classname);
                    // print_r2($id);
                    $c = $this->getWidgetsCount($id['widgets_id']);
                    echo count($c);
                    echo '</td>';
                    echo '<td class="paging">';

                    echo '<span class="toolbar_widgets_instance"><button id="' . $id['widgets_id'] . '">&nbsp;</button></span>';
                    /*
                    if(count($c)) {
                        echo '&nbsp;<img src="css/images/icon_expand.png" title="';
                        foreach ($c as $value) {
                            echo $value['title'] .' (id:'. $value['pages_id'] .')';
                        }
                        echo '" />';
                    }
                    */
                    echo '</td>';
                    echo '<td class="paging">';
                    // match widgets in database width class file
                    $i = recursiveArraySearch($s, $classname, $index = null);
                    $status = ($id['widgets_active'] == 1) ? 'ok' : 'not activated';
                    echo $status;
                    echo '</td>';
                    echo '<td class="paging">';
                    if (!is_numeric($i)) {
                        echo '<span class="toolbar_widgets_status"><button id="' . $classname . '" class="btn_widgets_handle" style="">install</button></span>';
                    } else {
                        echo $s[$i]['utc_created'];
                    }
                    echo '</td>';
                    echo '<td class="paging">';
                    if (is_numeric($i)) {
                        // match widgets in database width class file
                        $btn = ($id['widgets_active'] == 1) ? '<span class="toolbar_widgets_status"><button id="' . $id['widgets_id'] . '" value="0" class="btn_widgets_activate" style="">deactivate</button></span>' : '<span class="toolbar_widgets_status"><button id="' . $id['widgets_id'] . '" value="1" class="btn_widgets_activate" style="">activate</button></span>';
                        echo $btn;
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';

            closedir($handle);
        }
    }

    /**
     * @param int $widgets_id
     * @return array
     */
    public function getWidgetsCount($widgets_id)
    {
        $sql =
            "SELECT pages_widgets.pages_widgets_id, pages_widgets.pages_id, pages.title
		FROM pages_widgets
		LEFT JOIN pages
		ON pages_widgets.pages_id = pages.pages_id
		WHERE pages_widgets.widgets_id = :widgets_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':widgets_id', $widgets_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

}

?>