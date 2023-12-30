<?php if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
} ?>

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

        $('#btn_calendar_categories_new').click(function (event) {
            event.preventDefault();
            var action = "calendar_categories_new";
            var token = $("#token").val();
            var category = $("#category").val();
            if (category) {
                $.ajax({
                    beforeSend: function () {
                        loading = $('#ajax_spinner_calendar_categories').show()
                    },
                    complete: function () {
                        loading = setTimeout("$('#ajax_spinner_calendar_categories').hide()", 700)
                    },
                    type: 'POST',
                    url: 'calendar_edit_ajax.php',
                    data: "action=" + action + "&token=" + token + "&category=" + category,
                    success: function (newdata) {
                        ajaxReply('', '#ajax_status_calendar_categories');
                        $('<a href=calendar_categories_edit.php?id=' + newdata + '&token=' + token + ' class="colorbox_edit_reload"> Category created &raquo; click to edit <b>' + category + '</b> <span class="ui-icon ui-icon-pencil" title="edit" style="display:inline-block;vertical-align:text-bottom;"></span></a>').insertAfter("#ajax_status_calendar_categories");
                        $("#category").val('');
                    }
                });
            } else {
                alert('Name calendar category');
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

<h4 class="admin-heading">New category</h4>
<p>
    <label for="category">Name: </label><br/>
    <input type="text" id="category" name="category"/>
    <span class="toolbar_add"><button id="btn_calendar_categories_new">Add calendar category</button></span>
    <span id="ajax_spinner_calendar_categories" style="display:none;"></span>
    <span id="ajax_status_calendar_categories" style="display:none;"></span>
</p>
