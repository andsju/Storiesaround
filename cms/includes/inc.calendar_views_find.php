<?php
if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}

require_once 'includes/inc.core.php';

if (!get_role_CMS('administrator') == 1) {
    header('Location: index.php');
    die;
}
?>

    <script>
        $(document).ready(function () {

            $("#calendar_views_find").autocomplete({
                delay: 300,
                source: function (request, response) {
                    $.ajax({
                        type: "post",
                        url: "calendar_edit_ajax.php",
                        dataType: "json",
                        data: {
                            action: "calendar_views_search",
                            token: $("#token").val(),
                            s: request.term
                        },
                        success: function (data) {
                            response($.map(data, function (item) {
                                return {
                                    label: item.name,
                                    id: item.calendar_views_id
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

    <h4 class="admin-heading">Find view</h4>

    <form id="searchform" method="post" action="">
        <div style="padding:10px 0 10px 0;">
            <label for="pages_selections_find">Name: </label><br/>
            <input id="calendar_views_find" name="calendar_views_find" style="width:400px;"
                   value="<?php if (isset($_REQUEST['calendar_views_find'])) {
                       echo $_REQUEST['calendar_views_find'];
                   } ?>"/>
            <input type="hidden" id="psid"/>
            <span class="toolbar"><button id="btn_search">Search</button></span>
            <input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token"/>
        </div>
    </form>


<?php

// default
$search = null;
$form = false;
$large_table = false;

if (isset($_POST['calendar_views_find'])) {
    $search = $_POST['calendar_views_find'];
    $form = true;
}

$calendar = new Calendar();
$row_calendars = $calendar->getCalendarViewsSearchWords($search);

if (!$form) {
    if (count($row_calendars) > 100) {
        echo 'More than 100 rows in table (' . count($row_calendars) . '). Please use search button to display table.';
        $large_table = true;
    }
}

if (!$large_table) {

    $html = null;
    if ($row_calendars) {
        $html .= '<table class="table_js lightgrey">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Category</th>';
        $html .= '<th>Desrciption</th>';
        $html .= '<th style="width:10%;">Active</th>';
        $html .= '<th style="width:10%;">Public</th>';
        $html .= '<th style="width:10%;">Position</th>';
        $html .= '<th style="width:10%;">View</th>';
        $html .= '<th style="width:10%;">Edit</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($row_calendars as $r) {
            $html .= '<tr>';
            $html .= '<td>' . $r['name'] . '</td>';
            $html .= '<td>' . $r['description'] . '</td>';
            $active = $r['active'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
            $html .= '<td>' . $active . '</td>';
            $public = $r['public'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
            $html .= '<td>' . $public . '</td>';
            $html .= '<td>' . $r['position'] . '</td>';
            $html .= '<td><a href="calendar_preview.php?id=' . $r['calendar_views_id'] . '&type=view&token=' . $_SESSION['token'] . '" class="colorbox_edit">view</a></td>';
            $html .= '<td><a href="calendar_views_edit.php?id=' . $r['calendar_views_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">edit</a></td>';
            $html .= '</tr>';

        }
        $html .= '</tbody>';
        $html .= '</table>';
    }
    echo $html;

}
?>