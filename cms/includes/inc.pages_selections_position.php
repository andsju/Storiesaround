<?php
if (!defined('VALID_INCL')) {
    header('Location: index.php');
    die;
}
?>

<script>

    $(document).ready(function () {

        $("#sortable_selections ul").sortable({
            placeholder: "ui-state-highlight",
            axis: 'y',
            opacity: 0.6,
            cursor: 'move',
            update: function () {
                var token = $("#token").val();
                var pages_selections_id = $("#pages_selections_id").val();
                var order = $(this).sortable("serialize") + "&action=update_selections_position&token=" + token + "&pages_selections_id=" + pages_selections_id;
                $.post("admin_edit_ajax.php", order, function (newdata) {
                    ajaxReply(newdata, '#ajax_status_selections');
                });
            }
        });
        $("#sortable_selections").disableSelection();

    });

</script>

<?php

require_once 'inc.core.php';
include_once 'inc.functions_pages.php';

if (!get_role_CMS('administrator') == 1) {
    die;
}

$pages = new Pages();

?>

<h3 class="admin-heading">Set selection priority</h3>

<form method="post">
    <?php

    $values = array("" => "", "Header - above (978px)" => "header_above", "Header (978px replace default)" => "header", "Header - below (978px)" => "header_below", "Left sidebar - top (222px)" => "left_sidebar_top", "Left sidebar - bottom (222px)" => "left_sidebar_bottom", "Right sidebar - top (222px)" => "right_sidebar_top", "Right sidebar - bottom (222px)" => "right_sidebar_bottom", "Content - above (474-978px)" => "content_above", "Content - inside (474-978px)" => "content_inside", "Content - below (474-978px)" => "content_below", "Footer - above (978px)" => "footer_above", "Outer sidebar - top (222px)" => "outer_sidebar");

    echo '<p>';
    echo '<label for="area">Area</label><br />';
    echo '<select id="area" name="area">';
    foreach ($values as $key => $value) {
        echo '<option value="' . $value . '"';
        if (isset($_POST['area'])) {
            if ($_POST['area'] == $value) {
                echo ' selected=selected';
            }
        }
        echo '>' . $key . '</option>';
    }
    echo '</select>';
    echo ' <span class="toolbar"><button id="btn_area">Show</button></span>';
    echo '</p>';
    ?>
</form>

<?php

if (isset($_POST['area'])) {
    $selections = new Selections();
    $row_selections = $selections->getSelectionsActive();
    if ($row_selections) {

        echo '<p>Drag and drop to change position<span id="ajax_spinner_selections" style="display:none;"><img src="css/images/spinner.gif"></span><span id="ajax_status_selections" style="display:none;"></span></p>';
        echo '<div id="sortable_selections" style="padding:10px;width:600px;margin:10px;">';
        echo '<ul style="padding:0px;">';
        foreach ($row_selections as $r) {
            if ($r['area'] == $_POST['area']) {
                echo '<li style="line-height:2em;border:1px solid #DDDDDD;margin:2px;padding:2px;" id="arr_pages_selections_id_' . $r['pages_selections_id'] . '" class="ui-widget ui-widget-content">';
                echo '<span class="ui-icon ui-icon-triangle-2-n-s" title="Move page" style="display:inline-block;cursor:n-resize;margin:0 10px;"></span>' . $r['name'];
                echo '<div style="float:right;">(' . $r['area'] . ') <a class="colorbox_selections" href="pages_selections_preview.php?token=' . $_SESSION['token'] . '&id=' . $r['pages_selections_id'] . '">preview</a></div></li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    }
}

?>
</p>
