<?php
if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}
?>

<script>
    $(document).ready(function () {

        $(document).delegate("a.colorbox_edit_reload", "click", function (event) {
            event.preventDefault();
            $(".colorbox_edit_reload").colorbox({
                open: true,
                width: "1260px",
                height: "96%",
                transition: "none",
                iframe: true,
                onClosed: function () {
                    location.reload(true);
                }
            });
        });

        $('#btn_calendar_views_new').click(function (event) {
            event.preventDefault();
            var action = "calendar_views_new";
            var token = $("#token").val();
            var name = $("#name").val();
            if (name) {
                $.ajax({
                    beforeSend: function () {
                        loading = $('#ajax_spinner_calendar_views').show()
                    },
                    complete: function () {
                        loading = setTimeout("$('#ajax_spinner_calendar_views').hide()", 700)
                    },
                    type: 'POST',
                    url: 'calendar_edit_ajax.php',
                    data: "action=" + action + "&token=" + token + "&name=" + name,
                    success: function (newdata) {
                        ajaxReply('', '#ajax_status_calendar_views');
                        $('<a href=calendar_views_edit.php?id=' + newdata + '&token=' + token + ' class="colorbox_edit_reload"> View created &raquo; click to edit <b>' + name + '</b> <span class="ui-icon ui-icon-pencil" title="edit" style="display:inline-block;vertical-align:text-bottom;"></span></a>').insertAfter("#ajax_status_calendar_views");
                        $("#name").val('');
                    }
                });
            } else {
                alert('Name calendar view');
            }
        });
    });
</script>

<?php
require_once 'includes/inc.core.php';

if (!get_role_CMS('administrator') == 1) {
    header('Location: index.php');
    die;
}
?>

<h4 class="admin-heading">New view</h4>
<p>
    <label for="name">Name: </label><br/>
    <input type="text" id="name" name="name"/>
    <span class="toolbar_add"><button id="btn_calendar_views_new">Add calendar view</button></span>
    <span id="ajax_spinner_calendar_views" style="display:none;"></span>
    <span id="ajax_status_calendar_views" style="display:none;"></span>
</p>
