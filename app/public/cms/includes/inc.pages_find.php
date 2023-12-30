<?php
if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}
?>


<?php
require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if (!get_role_CMS('author') == 1) {
    die();
}
?>

<script>
    $(document).ready(function () {

        var loading;
        $("#pages_find").autocomplete({
            delay: 300,
            source: function (request, response) {
                $.ajax({
                    type: "post",
                    url: "pages_ajax.php",
                    dataType: "json",
                    data: {
                        action: "pages_search",
                        token: $("#token").val(),
                        s: request.term
                    },
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.title,
                                id: item.pages_id
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

        $('.table_js').dataTable({
            "iDisplayLength": 25,
            "order": [[9, "desc"]],
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "columnDefs": [{"targets": [9], "visible": false}] // hide column
        });

        $("[title]").tooltip({
            position: {
                my: "right top",
                at: "right+25 top+25"
            }
        });

        $("#dialog_confirm").dialog({
            autoOpen: false,
            modal: true
        });

        $('#btn_pages_bulk_status').click(function (event) {
            event.preventDefault();
            $("#dialog_confirm").dialog("open");
            $("#dialog_confirm").dialog({
                buttons: {
                    "Confirm": function () {
                        $(this).dialog("close");
                        var pages_ids = [];
                        $('input:checkbox[name="pages_id"]:checked').each(function (index) {
                            pages_ids.push($(this).val());
                        });
                        var action = "pages_bulk_action";
                        var token = $("#token").val();
                        var access = parseInt($('input:radio[name=bulk_access]:checked').val());
                        var status = $("#bulk_status option:selected").val();
                        var users_id = $("#users_id").val();
                        var date_start = $("#date_start").val() ? $("#date_start").val() : null;
                        var time_start = $("#time_start").val() ? $("#time_start").val() + ':00' : '00:00:00';
                        var datetime_start = (date_start == null) ? null : date_start + ' ' + time_start;
                        var date_end = $("#date_end").val() ? $("#date_end").val() : null;
                        var time_end = $("#time_end").val() ? $("#time_end").val() + ':00' : '00:00:00';
                        var datetime_end = (date_end == null) ? null : date_end + ' ' + time_end;

                        if (parseInt(status) >= 0) {
                            $.ajax({
                                beforeSend: function () {
                                    loading = $('#ajax_spinner_status').show()
                                },
                                complete: function () {
                                    loading = setTimeout("$('#ajax_spinner_status').hide()", 1000)
                                },
                                type: 'POST',
                                url: 'pages_edit_ajax.php',
                                data: {
                                    action: action,
                                    token: token,
                                    users_id: users_id,
                                    pages_ids: pages_ids,
                                    status: status,
                                    access: access,
                                    datetime_start: datetime_start,
                                    datetime_end: datetime_end
                                },
                                success: function (message) {
                                    ajaxReply(message, '#ajax_status_status');
                                }
                            });
                        }
                    },
                    "Cancel": function () {
                        $(this).dialog("close");
                    }
                }
            });
        });

        $('#btn_pages_bulk_site_header').click(function (event) {
            event.preventDefault();
            $("#dialog_confirm").dialog("open");
            $("#dialog_confirm").dialog({
                buttons: {
                    "Confirm": function () {
                        $(this).dialog("close");
                        var pages_ids = [];
                        $('input:checkbox[name="pages_id"]:checked').each(function (index) {
                            pages_ids.push($(this).val());
                        });
                        var action = "btn_pages_bulk_site_header";
                        var token = $("#token").val();
                        var users_id = $("#users_id").val();
                        var header_image = $("#header_image").val();
                        if (pages_ids) {
                            $.ajax({
                                beforeSend: function () {
                                    loading = $('#ajax_spinner_header_image').show()
                                },
                                complete: function () {
                                    loading = setTimeout("$('#ajax_spinner_header_image').hide()", 1000)
                                },
                                type: 'POST',
                                url: 'pages_edit_ajax.php',
                                data: {
                                    action: action, token: token, users_id: users_id, pages_ids: pages_ids,
                                    header_image: header_image
                                },
                                success: function (message) {
                                    ajaxReply(message, '#ajax_status_header_image');
                                }
                            });
                        }
                    },
                    "Cancel": function () {
                        $(this).dialog("close");
                    }
                }
            });
        });


        $('#pages_table .toggleboxes').click(function (event) {
            event.preventDefault();
            var table = $('#pages_table').DataTable();
            var b = $('#pages_table :checkbox').is(':checked');

            if (b) {
                $('#pages_table :checkbox').prop('checked', false);
            } else {
                $('#pages_table :checkbox').prop('checked', true);
            }
        });

        $("#toggle_bulk_actions").click(function () {

            $("#bulk_actions").toggle();
        });


    });
</script>

<?php

$pages = new Pages();

// default
$search = "";
$form = false;
$large_table = false;

if (isset($_POST['btn_pages_search'])) {
    if (strlen($_POST['pages_find']) > 0) {
        $search = filter_var($_POST['pages_find'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    $form = true;

}
$status = isset($_POST['status']) ? $_POST['status'] : 0;
?>


<h4 class="admin-heading">Find page</h4>

<form method="post">
    <input type="hidden" value="<?php echo $_SESSION['token']; ?>" id="token">
    <div style="float:left;padding:5px;">
        <label for="pages_find">Find page: </label><br/>
        <input id="pages_find" name="pages_find" style="width:400px;" value="<?php if (isset($_REQUEST['pages_find'])) {
            echo $_REQUEST['pages_find'];
        } ?>"/>
        <input type="hidden" id="pid"/>
    </div>
    <div style="float:left;padding:5px;">
        <label for="pages_find">Status: </label><br/>
        <select name="status" id="status">
            <option value="" <?php if ($status == null) {
                echo ' selected=selected';
            } ?>>(any)
            </option>
            <option value="1" <?php if ($status == 1) {
                echo ' selected=selected';
            } ?>>draft
            </option>
            <option value="2" <?php if ($status == 2) {
                echo ' selected=selected';
            } ?>>published
            </option>
            <option value="3" <?php if ($status == 3) {
                echo ' selected=selected';
            } ?>>archived
            </option>
            <option value="4" <?php if ($status == 4) {
                echo ' selected=selected';
            } ?>>pending
            </option>
            <option value="5" <?php if ($status == 5) {
                echo ' selected=selected';
            } ?>>trash
            </option>
        </select>
    </div>
    <div style="float:left;padding:15px 0 0 10px">
        <span class="toolbar"><button id="btn_pages_search" name="btn_pages_search">Search</button></span>
    </div>

</form>

<div class="clearfix">

    <?php
    if (get_role_CMS('superadministrator') == 1) {
    ?>
    <h4 id="toggle_bulk_actions" style="cursor: pointer;margin: 5px 0 10px 0;">Bulk actions <img
                src="css/images/icon_expand.png"></h4>

    <div id="bulk_actions" style="display:none;">
        <div style="float:left;padding:0 50px 10px 0; border-right: 1px dotted grey;">
            <span class="toolbar"><button id="btn_pages_bulk_status" name="btn_pages_bulk_status" style="margin:0px">Set page status</button></span>
            <span id="ajax_spinner_status" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
            <span id="ajax_status_status" style='display:none'></span>
            <select name="bulk_status" id="bulk_status">
                <option value="">---</option>
                <option value="2">Publish</option>
                <option value="3">Archive</option>
                <option value="1">Draft</option>
                <option value="5">Trash</option>
            </select>
            <p>
                <?php
                $date_start = $date_end = $access = "";
                if (isset($arr)) {
                    $date_start = ($arr['utc_start_publish'] > '2000-01-01 00:00') ? new DateTime(utc_dtz($arr['utc_start_publish'], $dtz, 'Y-m-d H:i:s')) : new DateTime(get_utc_dtz(gmdate('Y-m-d H:i:s'), $dtz, 'Y-m-d H:i:s'));
                    $date_end = ($arr['utc_end_publish'] > '2000-01-01 00:00') ? new DateTime(utc_dtz($arr['utc_end_publish'], $dtz, 'Y-m-d H:i:s')) : null;

                    $access = $arr['access'];
                }
                ?>
                <label for="date_start">Start publish:</label><br/>
                <input type="text" id="date_start" maxlength="10" title="yyyy-mm-dd" value="<?php if ($date_start) {
                    echo $date_start->format('Y-m-d');
                } ?>">
                <input type="text" id="time_start" size="5" maxlength="5" title="add hours and minutes hh:mm"
                       value="<?php if ($date_start) {
                           echo $date_start->format('H:i');
                       } ?>">
            </p>
            <p>
                <label for="date_end">End publish: (if set)</label><br/>
                <input type="text" id="date_end" maxlength="10" title="yyyy-mm-dd" value="<?php if ($date_end) {
                    echo $date_end->format('Y-m-d');
                } ?>">
                <input type="text" id="time_end" size="5" maxlength="5" title="add hours and minutes hh:mm"
                       value="<?php if ($date_end) {
                           echo $date_end->format('H:i');
                       } ?>">
            </p>

            Page can be viewed by<br/>
            <input type="radio" name="bulk_access" value="0" <?php if ($access == 0) {
                echo 'checked';
            } ?>> logged in users with rights to read
            <br/>
            <input type="radio" name="bulk_access" value="1" <?php if ($access == 1) {
                echo 'checked';
            } ?>> logged in users
            <br/>
            <input type="radio" name="bulk_access" value="2" <?php if ($access == 2) {
                echo 'checked';
            } ?>> everyone (public access)

        </div>

        <div style="float:left;padding:0 50px 10px 50px;">
            <span class="toolbar"><button id="btn_pages_bulk_site_header" name="btn_pages_bulk_site_header"
                                          style="margin:0px">Set header image</button></span>
            <input type="text" id="header_image" title="Site header image" style="width:200px;">
            <span id="ajax_spinner_header_image" style='display:none'><img src="css/images/spinner.gif"></span>&nbsp;
            <span id="ajax_status_header_image" style='display:none'></span>
        </div>
    </div>
</div>

<?php
}
?>

<div id="dialog_confirm" title="Confirmation required" style="display:none;">
    Run bulk action?
</div>

<div style="clear:both;"></div>



<?php
$row_pages = $pages->getPagesSearchWordsRelevance($search, $status=0, $pages_id=0, $limit_tree=0, $limit_start=0, $limit=1000);

if (!$form) {
    if (count($row_pages) > 100) {
        echo 'More than 100 rows in table (' . count($row_pages) . '). Please use search button to display table.';
        $large_table = true;
    }
}

if (!$large_table) {

    $html = null;
    if ($row_pages) {
        $html .= '<table class="table_js lightgrey ui-black-white" id="pages_table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Id</th>';
        $html .= '<th>Title</th>';
        $html .= '<th>Tag</th>';
        $html .= '<th>Status</th>';
        $html .= '<th>Access</th>';
        $html .= '<th>Start</th>';
        $html .= '<th>End</th>';
        $html .= '<th style="text-align:center;">Parent</th>';
        $html .= '<th style="text-align:left;">Modified</th>';
        $html .= '<th>Relevance</th>';
        $html .= '<th><span class="toggleboxes"><img src="css/images/check.png" /></span></th>';
        $html .= '<th>View</th>';
        $html .= '<th>Edit</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($row_pages as $r) {

            $html .= '<tr>';


            $html .= '<td>' . $r['pages_id'] . '</td>';
            $html .= '<td>' . $r['title'] . '</td>';
            $tag = !is_null($r['tag']) && strlen($r['tag']) ? '<span class="ui-icon ui-icon-tag" style="display:inline-block;" title="' . $r['tag'] . '"></span>' : null;
            $html .= '<td>' . $tag . '</td>';
            $explain = array('draft' => 1, 'published' => 2, 'archive' => 3, 'pending' => 4, 'trash' => 5);
            $html .= '<td>' . get_value_explained($r['status'], $explain) . '</td>';
            $explain = array('read rights' => 0, 'users' => 1, 'everyone' => 2);
            $html .= '<td>' . get_value_explained($r['access'], $explain) . '</td>';
            $html .= '<td>' . $r['utc_start_publish'] . '</td>';
            $html .= '<td style="text-align:center;">' . $r['utc_end_publish'] . '</td>';
            $html .= '<td style="text-align:center;">' . $r['parent'] . '</td>';
            $dt = strlen($r['utc_modified']) > 10 ? substr($r['utc_modified'], 0, 10) : '<span class="ui-icon ui-icon-calendar" style="display:inline-block;" title="' . $r['utc_modified'] . '"></span>';
            $html .= '<td>' . $dt . '</td>';
            $html .= '<td>' . $r['relevance'] . '</td>';
            $html .= '<td><input type="checkbox" class="pages_id" name="pages_id" value="' . $r['pages_id'] . '"></td>';
            $html .= '<td><a href="pages_preview.php?id=' . $r['pages_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">view</a></td>';
            $html .= '<td><a href="pages_edit.php?id=' . $r['pages_id'] . '&token=' . $_SESSION['token'] . '" class="colorbox_edit">edit</a></td>';
            $html .= '</tr>';

        }
        $html .= '</tbody>';
        $html .= '</table>';
    }
    echo $html;

}
?>
