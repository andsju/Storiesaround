<?php

if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}

require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if (!get_role_CMS('editor') == 1) {
    header('Location: index.php');
    die;
}

?>

    <script>
        $(document).ready(function () {

            $("#banners_find").autocomplete({
                delay: 300,
                source: function (request, response) {
                    $.ajax({
                        type: "post",
                        url: "admin_edit_ajax.php",
                        dataType: "json",
                        data: {
                            action: "banners_search",
                            token: $("#token").val(),
                            s: request.term
                        },
                        success: function (data) {
                            response($.map(data, function (item) {
                                return {
                                    label: item.name,
                                    id: item.banners_id
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

    <h4 class="admin-heading">Find banner</h4>

    <form action="" method="post">
        <div style="padding:10px 0 10px 0;">
            <label for="banners_find">Name: </label><br/>
            <input id="banners_find" name="banners_find" style="width:400px;"
                   value="<?php if (isset($_REQUEST['banners_find'])) {
                       echo $_REQUEST['banners_find'];
                   } ?>"/>
            <input type="hidden" id="psid"/>
            <span class="toolbar"><button name="btn_search">Search</button></span>&nbsp;
            <input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token"/>
        </div>
    </form>

<?php

// default
$search = null;
$form = false;
$large_table = false;

if (isset($_POST['banners_find'])) {
    $search = $_POST['banners_find'];
    $form = true;
}

$banners = new Banners();
$row_banners = $banners->getBannersSearchWords($search);

if (!$form) {
    if (count($row_banners) > 100) {
        echo 'More than 100 rows in table (' . count($row_banners) . '). Please use search button to display table.';
        $large_table = true;
    }
}

if (!$large_table) {

    $html = null;
    if ($row_banners) {
        $html .= '<table class="table_js lightgrey">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Name</th>';
        $html .= '<th>File</th>';
        $html .= '<th>Tag</th>';
        $html .= '<th>Active</th>';
        $html .= '<th>Area</th>';
        $html .= '<th>Start</th>';
        $html .= '<th>End</th>';
        $html .= '<th>View</th>';
        $html .= '<th>Edit</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($row_banners as $r) {
            $html .= '<tr>';
            $html .= '<td>' . $r['name'] . '</td>';
            $html .= '<td>' . $r['file'] . '</td>';
            $html .= '<td>' . $r['tag'] . '</td>';
            $active = $r['active'] == 0 ? '' : '<span class="ui-icon ui-icon-check" style="display:inline-block;"></span>';
            $html .= '<td>' . $active . '</td>';
            $html .= '<td>' . $r['area'] . '</td>';
            $html .= '<td style="text-align:right;">' . $r['utc_start'] . '</td>';
            $html .= '<td style="text-align:right;">' . $r['utc_end'] . '</td>';
            $html .= '<td><a href="banners_preview.php?id=' . $r['banners_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">view</a></td>';
            $html .= '<td><a href="banners_edit.php?id=' . $r['banners_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">edit</a></td>';
            $html .= '</tr>';

        }
        $html .= '</tbody>';
        $html .= '</table>';
    }
    echo $html;

}

?>