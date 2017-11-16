<?php
if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}
?>

<script>
    $(document).ready(function () {

        var loading;
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

        $('#btn_add_toplevel_page').click(function (event) {
            event.preventDefault();
            var id = this.id;
            var title_toplevel_page = $("#title_toplevel_page").val();
            var action = "pages_add_toplevel_page";
            var token = $("#token").val();
            var users_id = $("#users_id").val();

            if (title_toplevel_page) {
                $.ajax({
                    beforeSend: function () {
                        loading = $('#ajax_spinner_add_toplevel_page').show()
                    },
                    complete: function () {
                        loading = setTimeout("$('#ajax_spinner_add_toplevel_page').hide()", 700)
                    },
                    type: 'POST',
                    url: 'pages_edit_ajax.php',
                    data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&title_toplevel_page=" + title_toplevel_page,
                    success: function (newdata) {
                        ajaxReply('', '#new_toplevel_page');
                        $('<a href=pages_edit.php?id=' + newdata + ' class="colorbox_edit_reload"> Page created &raquo; click to edit <b>' + title_toplevel_page + '</b> <span class="ui-icon ui-icon-pencil" title="edit" style="display:inline-block;vertical-align:text-bottom;"></span></a>').hide().fadeIn('fast').insertAfter("#new_toplevel_page");
                        $('#btn_add_toplevel_page').attr('disabled', 'disabled');
                        $('#title_toplevel_page').val('');
                    }
                });
            } else {
                alert('Name top level page');
            }
        });

        $('#btn_add_child_page').click(function (event) {
            event.preventDefault();
            var id = this.id;
            var pages_parent_id = $("#pages_parent_id option:selected").val();
            var title_child_page = $("#title_child_page").val();
            var action = "pages_add_child_page";
            var token = $("#token").val();
            var users_id = $("#users_id").val();

            if (pages_parent_id) {
                if (title_child_page) {
                    $.ajax({
                        beforeSend: function () {
                            loading = $('#ajax_spinner_add_child_page').show()
                        },
                        complete: function () {
                            loading = setTimeout("$('#ajax_spinner_add_child_page').hide()", 700)
                        },
                        type: 'POST',
                        url: 'pages_edit_ajax.php',
                        data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_parent_id=" + pages_parent_id + "&title_child_page=" + title_child_page,
                        success: function (newdata) {
                            ajaxReply('', '#new_child_page');
                            $('<a href=pages_edit.php?id=' + newdata + ' class="colorbox_edit_reload"> Page created &raquo; click to edit <b>' + title_child_page + '</b> <span class="ui-icon ui-icon-pencil" title="edit" style="display:inline-block;vertical-align:text-bottom;"></span></a>').hide().fadeIn('fast').insertAfter("#new_child_page");
                            $('#btn_add_child_page').attr('disabled', 'disabled');
                            $('#title_child_page').val('');
                        }
                    });
                }
            } else {
                alert('Select parent page');
            }
        });
    });

</script>

<?php

require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if (!get_role_CMS('contributor') == 1) {
    header('Location: index.php');
    die;
}

$pages = new Pages();

//--------------------------------------------------
// check $_GET id
$id = array_key_exists('id', $_GET) ? $_GET['id'] : 0;
$id = filter_var($id, FILTER_VALIDATE_INT) ? $id : 0;
?>

<?php

if (get_role_CMS('administrator') == 1) {
    if ($id == 0) {
        ?>
        <h4 class="admin-heading">Add top level page</h4>

        <p>
            <?php echo $pages->getPagesRootBase(); ?>
        <p>
            <label for="title_toplevel_page" style="padding-right:5px;">Title: </label>
            <input type="text" id="title_toplevel_page" name="title_toplevel_page" style="width:100px;"
                   maxlength="100"/>
            <span class="toolbar_add"><button id="btn_add_toplevel_page">Add top level page</button></span>
            <span id="ajax_spinner_add_toplevel_page" class="hide"><img src="css/images/spinner.gif"></span>
            &nbsp;<span id="new_toplevel_page"></span>
        </p>
        <?php
    }
}

// restricted access role_CMS contributors 
// new page sets rights read/edit/create to current user
$disabled = (!get_role_CMS('author') == 1) ? ' disabled=disabled' : null;
?>

<h4 class="admin-heading">Add child page</h4>
<p>
    <label for="city">Select parent page: </label><br/>
    <select name="pages_parent_id" id="pages_parent_id" style="" <?php echo $disabled; ?>>
        <option value="">&raquo;&raquo;&raquo;</option>
        <option value=""></option>

        <?php echo get_pages_tree_option_list2($parent_id = 0, $id, $depth = 0); ?>
    </select>
<p>
    <label for="title_child_page">Title: </label><br/>
    <input type="text" id="title_child_page" name="title_child_page" style="width:300px;" maxlength="100"/>
    <span class="toolbar_add"><button id="btn_add_child_page" value="btn_add_child_page">Add child page</button></span>
    <span id="ajax_spinner_add_child_page" style='display:none'><img src="css/images/spinner.gif"></span>
    &nbsp;<span id="new_child_page"></span>
</p>

