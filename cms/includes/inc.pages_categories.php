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

            $('.table_js').dataTable({
                "iDisplayLength": 25,
                "order": [[1, "asc"]]
            });

            $("#btn_pages_category_add").click(function() {
                var category = $("#category").val();
                var position = $("#position").val();
                position = parseInt(position);
                var action = "pages_category_add";
                var token = $("#token").val();
                var users_id = $("#users_id").val();
                $.ajax({
                    beforeSend: function () { loading = $('#ajax_spinner_category').show()},
                    complete: function () { loading = setTimeout("$('#ajax_spinner_category').hide()", 500)},
                    type: 'POST',
                    url: 'pages_edit_ajax.php',
                    data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&category=" + category + "&position=" + position,
                    success: function (newdata) {
                        location.reload();
                    }
                });
            });

            $("#btn_pages_category_save").click(function() {
                var pages_categories_id = parseInt($("#pages_categories_id").val());
                var category = $("#category").val();
                var position = parseInt($("#position").val());
                var action = "pages_category_save";
                var token = $("#token").val();
                var users_id = $("#users_id").val();
                $.ajax({
                    beforeSend: function () { loading = $('#ajax_spinner_category').show()},
                    complete: function () { loading = setTimeout("$('#ajax_spinner_category').hide()", 500)},
                    type: 'POST',
                    url: 'pages_edit_ajax.php',
                    data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_categories_id=" + pages_categories_id + "&category=" + category + "&position=" + position,
                    success: function (newdata) {
                        location.reload();
                    }
                });
            });            

            $("#btn_pages_category_delete").click(function() {
                var pages_categories_id = $("#pages_categories_id").val();
                var action = "pages_category_delete";
                var token = $("#token").val();
                var users_id = $("#users_id").val();
                $.ajax({
                    type: 'POST',
                    url: 'pages_edit_ajax.php',
                    data: "action=" + action + "&token=" + token + "&users_id=" + users_id + "&pages_categories_id=" + pages_categories_id,
                    success: function (newdata) {
                        location.reload();
                    }
                });
                
            });

        });
    </script>


    <?php
    $btn_save = "";
    $btn_delete = "";
    $edit_class = "";
    $category = "";
    $position = 0;
    $pages_categories_id = 0;
    if (isset($_GET['token']) && isset($_GET['pages_categories_id'])) {
        $pages_categories_id = is_numeric($_GET['pages_categories_id']) ? $_GET['pages_categories_id'] : 0;
        $pages_categories = new PagesCategories();
        $row_category = $pages_categories->getPagesCategory($pages_categories_id);

        print_r2($category);

        if (count($row_category)) {
            $btn_save = '<span class="toolbar"><button id="btn_pages_category_save">Save category</button></span>';
            $btn_delete = '<span class="toolbar"><button id="btn_pages_category_delete">Delete category</button></span>';
            $edit_class = "hidden";
            $category = $row_category['category'];
            $pages_categories_id = $row_category['pages_categories_id'];
            $position = $row_category['position'];
        }

    }
    ?>

    <h4 class="admin-heading">Find category</h4>

    <p>
        <label for="category">Category: </label><br />
        <input type="text" id="category" name="category" style="width:200px;" maxlength="100" value="<?php echo $category; ?>"/>
        <input type="text" id="position" name="position" style="width:30px;" maxlength="3" value="<?php echo $position; ?>"/>
        <input type="hidden" id="pages_categories_id" name="pages_categories_id" value="<?php echo $pages_categories_id; ?>"/>
        <span id="pages_category_add" class="toolbar_add <?php echo $edit_class;?>"><button id="btn_pages_category_add">Add category</button></span>
        <?php echo $btn_save . $btn_delete; ?>
        <span id="ajax_spinner_category" style='display:none'><img src="css/images/spinner.gif"></span>
        &nbsp;<span id="ajax_result_category"></span>
    </p>



<?php

// default
$search = null;
$form = false;
$large_table = false;

if (isset($_POST['calendar_categories_find'])) {
    $search = $_POST['calendar_categories_find'];
    $form = true;
}

$categories = new PagesCategories();
$row_categories = $categories->getPagesCategories();


$html = null;
if ($row_categories) {
    $html .= '<table class="table_js lightgrey">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Category</th>';
    $html .= '<th style="width:10%;">Position</th>';
    $html .= '<th style="width:10%;">View</th>';
    $html .= '<th style="width:10%;">Edit</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';

    foreach ($row_categories as $r) {
        $html .= '<tr>';
        $html .= '<td>' . $r['category'] . '</td>';
        $html .= '<td>' . $r['position'] . '</td>';
        $html .= '<td><a href="pages_categories.php?id=' . $r['pages_categories_id'] . '&type=category&token=' . $_SESSION['token'] . '" class="colorbox_edit">view</a></td>';
        $html .= '<td><a href="admin.php?t=pages&tp=categories&pages_categories_id=' . $r['pages_categories_id'] . '&token=' . $_SESSION['token'] . '">edit</a></td>';
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';
}
echo $html;


?>