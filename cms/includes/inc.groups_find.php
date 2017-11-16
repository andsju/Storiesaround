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
                        action: "groups_search",
                        token: $("#token").val(),
                        s: request.term
                    },
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.groups,
                                id: item.groups_id,
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


        $('#btn_group_save').click(function (event) {
            event.preventDefault();
            var token = $("#token").val();
            var action = 'group_save';
            var groups_id = $('#groups_id').val();
            var title = $("#title").val();
            var description = $("#description").val();
            var active = $('input:checkbox[name="active"]').is(':checked') ? 1 : 0;

            $.ajax({
                beforeSend: function () {
                    loading = $('.ajax_spinner_users').show()
                },
                complete: function () {
                    loading = setTimeout("$('.ajax_spinner_users').hide()", 1000)
                },
                type: 'POST',
                url: 'groups_ajax.php',
                data: {
                    action: action,
                    token: token,
                    groups_id: groups_id,
                    title: title,
                    description: description,
                    active: active
                },
                success: function (message) {
                    if (message > 0) {
                        location.reload(true);
                    }
                },
            });
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
if (isset($_POST['btn_group_create'])) {

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
        $lastInsertId = $groups->setGroupsAdd($title, $description);
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

$groups_id = 0;
$g_meta = null;
if (isset($_GET['groups_id'])) {
    $groups_id = $_GET['groups_id'];
    $g_meta = $groups->getGroups($groups_id);
    if ($g_meta) {
        $class_create = null;
        $title = $g_meta['title'];
        $description = $g_meta['description'];
        $active = $g_meta['active'] == 1 ? ' checked' : '';
    }


}
?>


<p>
    <a id="toggle_box"
       style="cursor: pointer;color: #0000FF; margin:0;background:#FAFAFA;padding:10px;border:1px solid #ccc;">Toggle
        search | create group &raquo; </a>
</p>
<div id="create_box" class="<?php echo $class_create; ?>">
    <form id="create" method="post" action="">
        <h4 class="admin-heading">Add group</h4>
        <p>
            Title
            <input type="text" size="30" name="title" id="title" value="<?php echo $title; ?>"/><?php echo $reply; ?>
            Description
            <input type="text" size="30" name="description" id="description" value="<?php echo $description; ?>"/>
            <?php
            if ($g_meta) {
                echo '<input type="checkbox" value="1" name="active" ' . $active . ' />';
                echo '<span class="toolbar"><button name="btn_group_save" id="btn_group_save" class="input" />Save</button></span>';
                echo '<span class="toolbar"><button type="submit" name="btn_group_delete" class="input" />Delete</button></span>';
            } else {
                echo '<span class="toolbar"><button type="submit" name="btn_group_create" class="input" />Create</button></span>';
            }
            ?>
        </p>
        <input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token">
        <input type="hidden" value="<?php echo $groups_id; ?>" id="groups_id">
    </form>
</div>

<div id="search_box">
    <form id="searchform" method="post" action="">
        <h4 class="admin-heading">Find group</h4>
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

$row_groups = $groups->getGroupsSearchWords($search);

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
        $html .= '<th style="width:5%;">View</th>';
        $html .= '<th style="width:5%;">Membership</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($row_groups as $r) {
            $members = $groups->getGroupsMembershipMeta($r['groups_id']);
            $html .= '<tr>';
            $html .= '<td>' . $r['title'] . '</td>';
            $html .= '<td>' . $r['description'] . '</td>';
            $html .= '<td style="text-align:right;width:10%;">' . count($members) . '</td>';
            $active = $r['active'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
            $html .= '<td style="text-align:center;">' . $active . '</td>';
            $html .= '<td><a href="groups_view.php?groups_id=' . $r['groups_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">view</a></td>';
            $html .= '<td><a href="groupmembers.php?groups_id=' . $r['groups_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">edit</a></td>';
            $html .= '</tr>';

        }
        $html .= '</tbody>';
        $html .= '</table>';
    }
    echo $html;

}

?>
	
