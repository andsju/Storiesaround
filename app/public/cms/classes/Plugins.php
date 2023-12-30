<?php

/**
 * Class Plugins
 */
class Plugins extends Database
{

    /**
     * Plugins constructor
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

        // get plugins in database
        $s = $this->getPluginsInDatabase();

        // get plugins classes kept in folder /plugins
        // echo as floating divs, clickable / drag-drop ...
        $p = '../content/plugins';

        //open directory
        if ($handle = opendir($p)) {
            /* loop over plugin directory */
            while (false !== ($file = readdir($handle))) {

                // list all files in the current directory and strip out . and ..
                if ($file != "." && $file != "..") {
                    // get title from class name >> skip .class.php
                    $classname = substr($file, 0, -10);

                    // match class files with in folder width plugins in database
                    $id = $this->getPluginsDatabaseId($classname);

                    if ($id) {
                        $plugin = new $classname();
                        if ($id['plugins_active'] == 1) {

                            $a = $plugin->info();
                            echo '<div class="toolbar_plugins_new" title="' . $a['description'] . '"><button id="btn_' . $classname . '" class="btn_default" style="font-size:0.8em;">' . $a['title'] . '</button></div>';
                            ?>
                            <script type="text/javascript">

                                $('#btn_<?php echo $classname;?>').click(function (event) {
                                    event.preventDefault();

                                    var btn = this.id;
                                    var plugin = btn.substring(btn.indexOf("_") + 1);
                                    var action = "use_plugins";
                                    var token = $("#token").val();
                                    var pages_id = $("#pages_id").val();
                                    var pages_plugins_in_editor_id = $("#pages_plugins_id").val();

                                    if (!pages_plugins_in_editor_id) {

                                        $.ajax({
                                            type: 'POST',
                                            url: 'pages_edit_ajax.php',
                                            data: "action=" + action + "&token=" + token + "&pages_id=" + pages_id + "&plugin=" + plugin + "&pages_plugins_in_editor_id=" + pages_plugins_in_editor_id,
                                            success: function (newdata) {
                                                $("#plugins_stage").empty().append(newdata).hide().fadeIn('slow');
                                            },
                                        });
                                    } else {
                                        alert('Save and close plugin in editor first');
                                    }
                                });

                            </script>

                            <?php

                        }

                    }
                }
            }
            closedir($handle);
        }
    }

    /**
     *
     */
    public function getPluginsInDatabase()
    {
        $sql = "SELECT plugins_id, plugins_class, plugins_active, utc_created, utc_modified FROM plugins";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':classname', $classname, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * @param string $classname
     * @return mixed
     */
    public function getPluginsDatabaseId($classname)
    {
        $sql = "SELECT plugins_id, plugins_active FROM plugins WHERE plugins_class = :classname";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':classname', $classname, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * @param $plugin_class
     * @param $pages_plugins_id
     */
    public function plugin_form($plugin_class, $pages_plugins_id)
    {

        $class = new $plugin_class();
        $a = json_decode($class->default_objects(), true);
        //var_dump($a);

    }

    /**
     * @param $plugin_class
     * @return array
     */
    public function plugin_form_keys($plugin_class)
    {
        $class = new $plugin_class();
        $a = json_decode($class->default_objects(), true);
        //var_dump($a);
        return array_keys($a);
    }

    /**
     * @param $plugin_class
     * @return mixed
     */
    public function plugin_form_keys_validate($plugin_class)
    {
        $class = new $plugin_class();
        $a = json_decode($class->default_objects_validate(), true);
        //var_dump($a);
        return $a;
    }

    /**
     * @param $plugin_class
     * @return mixed
     */
    public function plugin_form_default($plugin_class)
    {
        $class = new $plugin_class();
        return $class;

    }

    /**
     * @param $classname
     * @return null
     */
    public function getPluginsId($classname)
    {
        $plugins_id = null;
        $sql = "SELECT plugins_id FROM plugins WHERE plugins_class = :classname";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':classname', $classname, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (count($row)) {
            $plugins_id = $row['plugins_id'];
        }
        return $plugins_id;
    }

    /**
     * @param $plugins_id
     * @return mixed
     */
    public function getPluginsClass($plugins_id)
    {
        $class = null;
        $sql = "SELECT plugins_class FROM plugins WHERE plugins_id = :plugins_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':plugins_id', $plugins_id, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (count($row)) {
            $plugins_class = $row['plugins_class'];
        }
        return $plugins_class;
    }

    /**
     * @param $plugins_id
     * @param $pages_id
     * @param $plugins_action
     * @param $area
     * @param $position
     * @return string
     */
    public function savePagesPlugins($plugins_id, $pages_id, $plugins_action, $area, $position)
    {
        try {
            $sql = "INSERT INTO pages_plugins 
			(plugins_id, pages_id, plugins_action, area, position) VALUES
			(:plugins_id, :pages_id, :plugins_action, :area, :position)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':plugins_id', $plugins_id, PDO::PARAM_INT);
            $stmt->bindParam(':pages_id', $pages_id, PDO::PARAM_INT);
            $stmt->bindParam(':plugins_action', $plugins_action, PDO::PARAM_STR);
            $stmt->bindParam(':area', $area, PDO::PARAM_STR);
            $stmt->bindParam(':position', $position, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId('pages_plugins_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }

    /**
     * @return array
     */
    public function getPluginsActive()
    {
        $sql = "SELECT plugins_id, plugins_class FROM plugins WHERE plugins_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * @param $plugins_id
     * @param $plugins_active
     * @return bool
     */
    public function setPluginsActivate($plugins_id, $plugins_active)
    {
        try {
            $sql = "UPDATE plugins 		
			SET plugins_active = :plugins_active, 
			utc_modified = UTC_TIMESTAMP()
			WHERE plugins_id = :plugins_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':plugins_active', $plugins_active, PDO::PARAM_INT);
            $stmt->bindParam(':plugins_id', $plugins_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }

    /**
     * @param $plugins_class
     * @return mixed
     */
    public function getPluginsInstall($plugins_class)
    {
        $sql = "SELECT plugins_id FROM plugins WHERE plugins_class = :plugins_class";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':plugins_class', $plugins_class, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * @param $plugins_class
     * @return string
     */
    public function setPluginsInstall($plugins_class)
    {
        // get title from class
        $w = new $plugins_class();
        $a = $w->info();
        $plugins_title = $a['title'];

        try {
            $sql = "INSERT INTO plugins 
			(plugins_class, plugins_title, plugins_active, utc_created) VALUES
			(:plugins_class,:plugins_title, 1, UTC_TIMESTAMP())";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':plugins_class', $plugins_class, PDO::PARAM_STR);
            $stmt->bindParam(':plugins_title', $plugins_title, PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId('plugins_id');

        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }

    /**
     * @param $sql
     * @return bool
     */
    public function setPluginsDatabaseInstall($sql)
    {
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        } catch (PDOException $e) {
            handle_pdo_exception($_SERVER['REQUEST_URI'], $e);
        }
    }

    public function initiate()
    {
        // plugins in database
        $s = $this->getPluginsInDatabase();

        // get plugins classes kept in folder /plugins
        $p = '../content/plugins';

        //open directory
        if ($handle = opendir($p)) {

            echo '<table class="paging" width="100%">';
            echo '<thead>';
            echo '<tr class="ui-widget ui-widget-header">';
            echo '<th class="paging" width="15%">';
            echo '<b>Plugin</b>';
            echo '</th>';
            echo '<th class="paging" width="5%">';
            echo '<b>Instances</b>';
            echo '</th>';
            echo '<th class="paging" width="25%">';
            echo '<b>Pages</b>';
            echo '</td>';
            echo '<th class="paging" width="5%">';
            echo '<b>Status</b>';
            echo '</th>';
            echo '<th class="paging" width="15%">';
            echo '<b>Date installed</b>';
            echo '</th>';
            echo '<th class="paging" width="10%">';
            echo '<b>Action</b>';
            echo '</th>';
            echo '<th class="paging" width="10%">';
            echo '<b>Update</b> <span class="ui-icon ui-icon-gear" style="display:inline-block;"></span>';
            echo '</th>';
            echo '<th class="paging" width="15%">';
            echo '<b>Edit</b> <span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span>';
            echo '</th>';
            echo '</tr>';
            echo '</thead>';

            /* loop over plugin directory */
            while (false !== ($file = readdir($handle))) {

                echo '<tbody>';
                echo '<tr class="ui-widget ui-widget-content">';

                // exclude directories
                if (is_dir(CMS_ABSPATH . '/content/plugins/' . $file)) continue;

                // list all files in the current directory and strip out . and ..
                if ($file != "." && $file != "..") {

                    //if (is_dir($handle)) continue;
                    echo '<td class="paging">';
                    // get title from class name >> skip .class.php
                    $classname = substr($file, 0, -10);
                    $plugin = new $classname();
                    $a = $plugin->info();
                    echo $classname;
                    echo '</td>';
                    echo '<td class="paging" style="text-align:right;">';
                    $id = $this->getPluginsDatabaseId($classname);
                    $c = $this->getPluginsCount($id['plugins_id']);
                    echo count($c);
                    echo '</td>';
                    echo '<td class="paging">';
                    if (count($c)) {
                        echo '<ul>';
                        foreach ($c as $value) {
                            echo '<li><a href="http://' . $_SESSION['site_domain'] . '/cms/pages.php?id=' . $value['pages_id'] . '" target="_blank">' . $value['title'] . '</a> (id: ' . $value['pages_id'] . ')</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '&nbsp;';
                    }
                    echo '</td>';
                    echo '<td class="paging" style="text-align:center;">';
                    // match plugins in database width class file
                    $i = recursiveArraySearch($s, $classname, $index = null);
                    $status = ($id['plugins_active'] == 1) ? 'ok' : 'not activated';
                    echo $status;
                    echo '</td>';
                    echo '<td class="paging">';
                    if (!is_numeric($i)) {
                        echo '<span class="toolbar"><button id="' . $classname . '" class="btn_plugins_install" style="">install</button></span>';
                    } else {
                        echo $s[$i]['utc_created'];
                    }
                    echo '</td>';
                    echo '<td class="paging" style="text-align:center;">';
                    if (is_numeric($i)) {
                        $btn = ($id['plugins_active'] == 1) ? '<span class="toolbar"><button id="' . $id['plugins_id'] . '" value="0" class="btn_plugins_activate" style="">deactivate</button></span>' : '<span class="toolbar"><button id="' . $id['plugins_id'] . '" value="1" class="btn_plugins_activate" style="">activate</button></span>';
                        echo $btn;
                    }
                    echo '</td>';
                    echo '<td class="paging" style="text-align:center;">';
                    if (is_numeric($i)) {
                        $btn = '<span class="toolbar"><button id="' . $classname . '" value="0" class="btn_plugins_update" style="">update</button></span>';
                        echo $btn;
                    }
                    echo '</td>';
                    echo '<td class="paging">';
                    if (is_numeric($i)) {
                        echo '<a href="' . CMS_DIR . '/content/plugins/' . strtolower($classname) . '/admin.php" target="_blank">Administration <span class="ui-icon ui-icon-pencil" style="display:inline-block;"></span></a>';
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
     * @param $plugins_id
     * @return array
     */
    public function getPluginsCount($plugins_id)
    {
        $sql =
            "SELECT pages_plugins.pages_plugins_id, pages_plugins.pages_id, pages.title
		FROM pages_plugins
		LEFT JOIN pages
		ON pages_plugins.pages_id = pages.pages_id
		WHERE pages_plugins.plugins_id = :plugins_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':plugins_id', $plugins_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
}

?>