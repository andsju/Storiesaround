<?php
require_once 'includes/inc.core.php';

if (!isset($_SESSION['site_id'])) {
    echo 'Site is not set!';
    exit;
}

// css files
//--------------------------------------------------
$css_files = array(
    'css/layout.css',
    'libraries/jquery-ui/jquery-ui.css');


// include header
//--------------------------------------------------
$page_title = "Events Calendar";
$body_style = "width:470px;padding:10px;";
require 'includes/inc.header_minimal.php';


// load javascript files
//--------------------------------------------------
$js_files = array(
    'libraries/jquery-ui/jquery-ui.custom.min.js',
    'libraries/jquery-ui/jquery.ui.datepicker-sv.js');

foreach ($js_files as $js): ?>
    <script src="<?php echo $js; ?>"></script>
<?php endforeach; ?>

<?php

$cal = new Calendar();

include 'includes/inc.calendar_js.php';
?>

<div id="content">

    <input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token']; ?>"/>
    <div id="dialog_calendar" title="Calendar" style="display:none;"></div>

    <?php

    $href = $_SERVER['PHP_SELF'] . "?date=";
    if (isset($_GET['date'])) {
        $date = (isValidDate($_GET['date'])) ? $_GET['date'] . ' 00:00:00' : null;
    } else {
        $date = null;
    }


    // requested category
    $calendar_categories_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ? $_GET['id'] : 0;

    echo '<input type="hidden" name="calendar_categories_id" id="calendar_categories_id" value="' . $calendar_categories_id . '" />';

    function removeqsvar($url, $varname)
    {
        list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
        parse_str($qspart, $qsvars);
        unset($qsvars[$varname]);
        $newqs = http_build_query($qsvars);
        return $urlpart . '?' . $newqs;
    }

    $href = removeqsvar($_SERVER['REQUEST_URI'], 'date');
    $href = $href . '&date=';

    echo '<div id="calendar_include" style="width:460px;overflow:auto;height:560px;">';
    echo $cal->getCalendarCategoriesRights($date = null, $href = null, $max_width = true, $calendar_categories_id, $period = "week");
    echo '</div>';

    echo '</div>';

    ?>
</div>

<div class="footer-wrapper">
    <?php include_once 'includes/inc.footer_cms.php'; ?>
</div>


</body>
</html>