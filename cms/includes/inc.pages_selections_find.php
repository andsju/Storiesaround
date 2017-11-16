<?php
if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}
?>

<?php

require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if (!get_role_CMS('editor') == 1) {
    die;
}
?>

    <script>
        $(document).ready(function () {

            $("#pages_selections_find").autocomplete({
                delay: 300,
                source: function (request, response) {
                    $.ajax({
                        type: "post",
                        url: "pages_ajax.php",
                        dataType: "json",
                        data: {
                            action: "pages_selections_search",
                            token: $("#token").val(),
                            s: request.term
                        },
                        success: function (data) {
                            response($.map(data, function (item) {
                                return {
                                    label: item.name,
                                    id: item.pages_selections_id
                                }
                            }));
                        }
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    $("input#psid").val(ui.item.id)
                }
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


    <h4 class="admin-heading">Find selections</h4>

    <form action="" method="post">
        <div style="padding:10px 0;">
            <label for="pages_selections_find">Name: </label><br/>
            <input id="pages_selections_find" name="pages_selections_find" style="width:400px;"
                   value="<?php if (isset($_REQUEST['pages_selections_find'])) {
                       echo $_REQUEST['pages_selections_find'];
                   } ?>"/>
            <input type="hidden" id="psid"/>
            <span class="toolbar"><button name="btn_selections_search" id="btn_selections_search">Search</button></span>&nbsp;
            <input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token"/>
        </div>
    </form>

<?php

// default
$search = null;
$form = false;
$large_table = false;

if (isset($_POST['pages_selections_find'])) {
    if (strlen(trim($_POST['pages_selections_find'])) > 0) {
        $search = trim($_POST['pages_selections_find']);
    }
    $form = true;
}

$pages = new Pages();
$selections = new Selections();
$row_selections = $selections->getSelectionsSearchWords($search);

if (!$form) {
    if (count($row_selections) > 100) {
        echo 'More than 100 rows in table (' . count($row_selections) . '). Please use search button to display table.';
        $large_table = true;
    }
}

if (!$large_table) {

    $html = null;
    if ($row_selections) {
        $html .= '<table class="table_js lightgrey">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Name</th>';
        $html .= '<th>Description</th>';
        $html .= '<th>Area</th>';
        $html .= '<th>Active</th>';
        $html .= '<th>Shown in pages</th>';
        $html .= '<th>Modified</th>';
        $html .= '<th>Order</th>';
        $html .= '<th>View</th>';
        $html .= '<th>Edit</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($row_selections as $r) {
            $pages_appearances = $pages->getPagesSelections($r['pages_selections_id']);
            $html .= '<tr>';
            $html .= '<td>' . $r['name'] . '</td>';
            $html .= '<td>' . $r['description'] . '</td>';
            $html .= '<td>' . $r['area'] . '</td>';
            $active = $r['active'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
            $html .= '<td style="text-align:center;">' . $active . '</td>';
            $html .= '<td style="text-align:right;">';
            $html .= count($pages_appearances);
            if (count($pages_appearances)) {
                $html .= ' <span class="ui-icon ui-icon-info" style="display:inline-block;"';
                $ti = count($pages_appearances) . ' pages: ';
                foreach ($pages_appearances as $pages_appearance) {
                    $ti .= $pages_appearance['title'] . ' | ';
                }
                $html .= 'title="' . substr($ti, 0, 200) . '..."></span>';
            }
            $html .= '</td>';
            $html .= '<td style="text-align:center;">' . $r['utc_modified'] . '</td>';
            $html .= '<td style="text-align:right;">' . $r['position'] . '</td>';
            $html .= '<td><a href="pages_selections_preview.php?id=' . $r['pages_selections_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">view</a></td>';
            $html .= '<td><a href="pages_selections_edit.php?id=' . $r['pages_selections_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">edit</a></td>';
            $html .= '</tr>';

        }
        $html .= '</tbody>';
        $html .= '</table>';
    }
    echo $html;

}
?>