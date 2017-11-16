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

        $('#dir_open').click(function (event) {
            event.preventDefault();
            var action = "edit_files_css";
            var directory_value = $("#dir option:selected").val();
            var directory_text = $("#dir option:selected").text();
            var token = $("#token").val();
            var loading;
            $.ajax({
                beforeSend: function () {
                    loading = $('#ajax_spinner_css').show()
                },
                complete: function () {
                    loading = setTimeout("$('#ajax_spinner_css').hide()", 700)
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

<h4 class="admin-heading">Edit css file</h4>

<div class="select_items">
    <?php

    echo '<select id="dir">';
    foreach (new DirectoryIterator(CMS_ABSPATH . '/content/themes') as $fileInfo) {
        if ($fileInfo->isDot()) continue;
        //echo $fileInfo->getFilename() . "<br>\n";
        $css = $fileInfo->getFilename();
        echo '<option value="' . $css . '">' . $css . '</option>';
    }
    echo '</select>';
    echo '&nbsp;<span class="toolbar_open"><button id="dir_open">Open</button></span>';
    ?>
    <span id="ajax_spinner_css" class="hide"><img src="css/images/spinner.gif"></span>
    <span id="ajax_status_css" class="hide"></span>
</div>

<div id="directory_view" class="ui-black-white">
</div>
