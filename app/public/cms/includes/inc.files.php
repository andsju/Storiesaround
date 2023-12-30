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

        $('#dir_show').click(function (event) {
            event.preventDefault();
            var action = "files";
            var directory_value = $("#dir option:selected").val();
            var directory_text = $("#dir option:selected").text();
            var token = $("#token").val();
            var loading;
            $.ajax({
                beforeSend: function () {
                    loading = $('#ajax_spinner_directory').show()
                },
                complete: function () {
                    loading = setTimeout("$('#ajax_spinner_directory').hide()", 700)
                },
                type: 'POST',
                url: 'admin_edit_ajax.php',
                data: "action=" + action + "&token=" + token + "&directory_value=" + directory_value + "&directory_text=" + directory_text,
                success: function (data) {
                    $("#directory_view").empty().html(data).hide().fadeIn('fast');
                }
            });
        });

    });

</script>

<?php

$folders = array(
    CMS_DIR . "/cms/css/images" => "cms/css/images",
    CMS_DIR . "/content/sample" => "content/sample",
    CMS_DIR . "/content/uploads/files" => "content/uploads/files",
    CMS_DIR . "/content/uploads/header" => "content/uploads/header",
    CMS_DIR . "/content/uploads/html" => "content/uploads/html",
    CMS_DIR . "/content/uploads/images" => "content/uploads/images",
    CMS_DIR . "/content/uploads/media" => "content/uploads/media",
    CMS_DIR . "/content/uploads/media" => "content/uploads/misc"
);

?>
<h4 class="admin-heading">Browse directory</h4>

<?php
/*
print_r2(CMS_ABSPATH);
print_r2(CMS_DIR);
print_r2(strlen(CMS_DIR));
*/
?>

<p>
    <span class="toolbar"><button id="btn_upload">Upload files</button></span>
    <input type="checkbox" id="overwrite" name="overwrite" value="1"> Overwrite file if exists
</p>

<div class="select_items">
    <?php
    echo '<select id="dir">';
    foreach ($folders as $folder => $value) {
        echo '<option value="' . $folder . '">' . $value . '</option>';
    }
    echo '</select>';
    echo '&nbsp;<span class="toolbar"><button id="dir_show">Show</button></span>';
    ?>
    <span id="ajax_spinner_directory" class="hide"><img src="css/images/spinner.gif"></span>&nbsp;
    <span id="ajax_status_directory" class="hide"></span>&nbsp;
</div>

<div id="directory_view" style="border:1px dashed #000;overflow:auto;max-width:100%;padding:10px;display:none"
     class="ui-black-white">
</div>
