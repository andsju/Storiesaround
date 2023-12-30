<?php
require_once 'includes/inc.core.php';

if (!get_role_CMS('user') == 1) {
    die;
}

if (!isset($_SESSION['site_id'])) {
    echo 'Site is not set!';
    exit;
}

// css files, loaded in header.inc.php
//--------------------------------------------------
$css_files = array(
    CMS_DIR . '/cms/css/layout.css',
    CMS_DIR . '/cms/css/pages_edit.css',
    CMS_DIR . '/cms/libraries/jquery-ui/jquery-ui.css',
    CMS_DIR . '/cms/libraries/jquery-ui/jquery-ui.css',
    CMS_DIR . '/cms/libraries/jquery-colorbox/colorbox.css',
    CMS_DIR . '/cms/libraries/jquery-datatables/style.css'
);

// include header
//--------------------------------------------------
$page_title = "Group preview";
$body_style = "width:600px;";
require 'includes/inc.header_minimal.php';

// load javascript files, loads before inc.footer.php
//--------------------------------------------------
$js_files = array(
    CMS_DIR . '/cms/libraries/jquery-ui/jquery-ui.custom.min.js',
    CMS_DIR . '/cms/libraries/js/functions.js',
    CMS_DIR . '/cms/libraries/jquery-colorbox/jquery.colorbox-min.js',
    CMS_DIR . '/cms/libraries/jquery-datatables/jquery.datatables.min.js'
);

// load javascript files
//--------------------------------------------------
foreach ($js_files as $js): ?>
    <script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>

<script>
    $(document).ready(function () {
        $('.table_js').dataTable({
            "iDisplayLength": 25,
            "order": [[0, "asc"]]
        });
    });
</script>


<?php

echo '<h5 class="admin-heading">Group members</h5>';

if (isset($_GET['token'])) {
    // only accept $_POST from this §_SESSION['token']
    if ($_GET['token'] == $_SESSION['token']) {


        // check $_GET id
        $groups_default_id = array_key_exists('groups_default_id', $_GET) ? $_GET['groups_default_id'] : null;

        if($groups_default_id == null) { die;}

        $groups = new Groups();
        $g = $groups->getGroupsDefaultMeta($groups_default_id);

        $title = null;
        if ($g) {
            $title = $g['title'];
        }

        echo '<h4 class="admin-heading">' . $title . '</h4>';

        $row_groups = $groups->getGroupsDefaultMembership($groups_default_id);

        $html = null;
        if ($row_groups) {
            $html .= '<table class="table_js lightgrey">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>First name</th>';
            $html .= '<th>Last name</th>';
            $html .= '<th>Email</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($row_groups as $r) {
                $html .= '<tr>';
                $html .= '<td>' . $r['first_name'] . '</td>';
                $html .= '<td>' . $r['last_name'] . '</td>';
                $html .= '<td>' . $r['email'] . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
        } else {
            $html .= '| no result |';
        }
        echo $html;
    }
}

?>

</body>
</html>