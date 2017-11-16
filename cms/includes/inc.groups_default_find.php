<?php
if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}

require_once 'inc.core.php';

if (!get_role_CMS('administrator') == 1) {
    header('Location: index.php');
    die;
}
?>


<script>
    $(document).ready(function () {

        $("#groups_find").autocomplete({
            delay: 300,
            source: function (request, response) {
                $.ajax({
                    type: "post",
                    url: "groups_ajax.php",
                    dataType: "json",
                    data: {
                        action: "groups_default_search",
                        token: $("#token").val(),
                        s: request.term
                    },
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.groups_default,
                                id: item.groups_default_id
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                $("input#pid").val(ui.item.id)
            }
        });

        $("#toggle_box").click(function () {
            $("#create_box").toggle('fast');
            $("#search_box").toggle('fast');
            return false;
        });

        $(".toolbar button").button({
            icons: {},
            text: true
        });

        $('.table_js').dataTable({
            "iDisplayLength": 25,
            "order": [[0, "asc"]]
        });

        $("[title]").tooltip({
            position: {
                my: "right top",
                at: "right+25 top+25"
            }
        });

    });
</script>


<?php

$groups = new Groups();

// default
$search = null;
$form = false;
$large_table = false;
$class_create = "hide";
$title = $description = false;

// handle form 
if (isset($_POST['btn_create_group'])) {

    // incoming data
    $trimmed = array_map('trim', $_POST);
    if (strlen($trimmed['title']) > 0) {
        $title = filter_var($trimmed['title'], FILTER_SANITIZE_STRING);
    }

    if (strlen($trimmed['description']) > 0) {
        $description = filter_var($trimmed['description'], FILTER_SANITIZE_STRING);
    }

    if ($title) {

        // add new group
        $lastInsertId = $groups->setGroupsDefaultAdd($title, $description);
        if ($lastInsertId > 0) {
            $reply .= '<span class="reply_success">Group created - <a href="groups_edit.php?groups_id=' . $lastInsertId . '">edit</a> </span>';
        }
        // reset values
        $title = $description = false;

    } else {
        $class_create = null;
        $reply .= '<span class="reply_fail"> * Enter title </span>';
    }
}

if (isset($_POST['btn_groups_search'])) {
    if (strlen($_POST['groups_find']) > 0) {
        $search = filter_var($_POST['groups_find'], FILTER_SANITIZE_STRING);
    }
    $form = true;
}

?>


<p>
    <a id="toggle_box"
       style="cursor: pointer;color: #0000FF; margin:0;background:#FAFAFA;padding:10px;border:1px solid #ccc;">Toggle
        search | create group &raquo; </a>
</p>
<div id="create_box" class="<?php echo $class_create; ?>">
    <form id="create" method="post" action="">
        <h4 class="admin-heading">Add default group</h4>
        <p>
            Title
            <input type="text" size="30" name="title" id="title" value="<?php echo $title; ?>"/><?php echo $reply; ?>
            Description
            <input type="text" size="30" name="description" id="description" value=""/>
            <span class="toolbar"><button type="submit" name="btn_create_group" class="input"/>Create</button></span>
        </p>
        <input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token">
    </form>
</div>

<div id="search_box">
    <form id="searchform" method="post" action="">
        <h4 class="admin-heading">Find default group</h4>
        <p>
            <input id="groups_find" name="groups_find" style="width:400px;"
                   value="<?php if (isset($_REQUEST['groups_find'])) {
                       echo $_REQUEST['groups_find'];
                   } ?>"/>
            <input type="hidden" id="pid"/>
            <span class="toolbar"><button type="submit" name="btn_groups_search" class="input"/>Search</button></span>
        </p>
        <input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token">
    </form>
</div>

<?php

$row_groups = $groups->getGroupsDefaultSearchWords($search);

if (!$form) {
    if (count($row_groups) > 100) {
        echo 'More than 100 rows in table (' . count($row_groups) . '). Please use search button to display table.';
        $large_table = true;
    }
}

if (!$large_table) {
    $html = null;
    if ($row_groups) {
        $html .= '<table class="table_js lightgrey">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Title</th>';
        $html .= '<th>Description</th>';
        $html .= '<th style="text-align:right;width:10%;">Members</th>';
        $html .= '<th style="text-align:center;width:10%;">Active</th>';
        $html .= '<th style="text-align:center;width:10%;">Modified</th>';
        $html .= '<th style="width:5%;">View</th>';
        $html .= '<th style="width:5%;">Edit</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach ($row_groups as $r) {
            $members = $groups->getGroupsDefaultMembershipMeta($r['groups_default_id']);
            $html .= '<tr>';
            $html .= '<td>' . $r['title'] . '</td>';
            $html .= '<td>' . $r['description'] . '</td>';
            $html .= '<td style="text-align:right;width:10%;">' . count($members) . '</td>';
            $active = $r['active'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
            $html .= '<td style="text-align:center;">' . $active . '</td>';
            $html .= '<td style="text-align:center;"><span class="ui-icon ui-icon-calendar" style="display:inline-block;" title="' . $r['utc_modified'] . '"></span></td>';
            $html .= '<td><a href="groups_default_view.php?groups_default_id=' . $r['groups_default_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">view</a></td>';
            $html .= '<td><a href="groups_default_edit.php?groups_default_id=' . $r['groups_default_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">edit</a></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
    }
    echo $html;
}

?>
