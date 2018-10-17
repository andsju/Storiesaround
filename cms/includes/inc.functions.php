<?php

if (!defined('VALID_INCL')) {
    die();
}

/* This script:
 * - defines common functions
 */

/**
 * @param $default
 * @param $translate
 * @param $languages
 * @return mixed|null|string
 */
function translate($default, $translate, $languages)
{
    if (is_string($default) && is_string($translate) && is_array($languages)) {
        $str = array_key_exists($translate, $languages) ? $languages[$translate] : $default;
    } else {
        $str = is_string($default) ? $default : null;
    }
    return $str;
}

/**
 * translate accents and special characters
 * friendly url, easier filenames
 * @param $string
 * @return mixed
 */
function replace_characters($string)
{
    return preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($string, ENT_COMPAT, 'UTF-8'));
}

/**
 * allow alphanumerics
 * @param $string
 */
function allow_alphanumeric($string)
{
    $string = preg_replace("/[^a-zA-Z0-9-_]/", "", $string);
}

/**
 * remove multiple common
 * replace special characters, spaces, multiple dashes, underscores, keep alphanumeric
 * @param $string
 * @param $lowercase
 * @return mixed|string
 */
function set_good_filename($string, $lowercase)
{
    $string = preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($string, ENT_COMPAT, 'UTF-8'));
    $string = preg_replace('/\s+/', '-', $string);
    $string = trim(preg_replace('/-+/', '-', $string), '-');
    $string = trim(preg_replace('/_+/', '_', $string), '_');
    $string = preg_replace("/[^a-zA-Z0-9-_]/", "", $string);
    $string = $lowercase == true ? strtolower($string) : $string;
    return $string;
}

/**
 * @param $dateTime
 * @return bool
 */
function isValidDateTime($dateTime)
{
    if (is_string(($dateTime))) {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }
    }
}

/**
 * @param $date
 * @return bool
 */
function isValidDate($date)
{
    if (is_string(($date))) {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }
    }
}

/**
 * @param $str
 * @return bool
 */
function isValidPassword($str)
{
    if (is_string(($str))) {
        $reg = '/^[^%\s]{8,}$/';
        $reg2 = '/[A-Z]/';        //upper
        $reg3 = '/[a-z]/';        //lower
        $reg4 = '/[0-9]/';        //numeric
        $reg5 = '/[\@\'\"\!\&\|\<\>\#\?\:\;\$\*\_\+\-\^\.\,\(\)\{\}\[\]\\\/\=\~]/';
        return preg_match($reg, $str) && preg_match($reg2, $str) && preg_match($reg3, $str) && preg_match($reg4, $str) && preg_match($reg5, $str);
    }
}

/**
 * usage -> if(isValidString($string, 'alpha')) {}
 * @param $string
 * @param $reg
 * @return bool
 */
function isValidString($string, $reg)
{
    if ((is_string(($string))) && is_string(($reg))) {

        // allowed string
        $allowed = array('username', 'strong_password', 'str', 'css', 'html', 'code', 'path', 'words', 'name', 'postnummer', 'personnummer', 'personnummerPTF', 'milliseconds', 'alpha', 'numerical', 'alphanumerical', 'ratio', 'email', 'sevendigits', '0-9', '1-20', 'date', 'ip', 'url', 'url_query', 'hex', 'php', 'boolean', 'any-all', 'characters', 'comma_separated_numbers', 'any');
        if (in_array($reg, $allowed)) {
            $regex_string = Array(
                // username; letters, digits. Underscore is allowed if followed by one or more letters / digits. Minimum 4 character. Maximum 20 characters
                'username' => '/^(?=.{4,20}$)[a-zA-Z][a-zA-Z0-9]*(?:_[a-zA-Z0-9]+)*$/',
                // str, matches 1-128 alphanumerical and -+=%_~!@.,#* characters
                'str' => '/^(?=^.{1,128}$)([A-Za-z0-9]|[-+=%_~!@.,#*])*$/',
                // css, matches 1-128 alphanumerical and -+=%_~!@.,#* characters
                'css' => '/^(?=^.{1,128}$)([A-Za-z0-9]|[-+=%_ ~!@.,#*:;()\/\'\"])*$/',
                // html, matches 1-128 alphanumerical and -+=%_~!@<>.,#* characters
                'html' => '/^(?=^.{1,128}$)([A-Za-z0-9]|[-+=%_ ~!@<>.,#*:;()\/\'\"])*$/',
                // code, matches alphanumerical and -+=%_ ~!@<>:;.,#?&'"/\ characters
                'code' => '/^[\w-+=%_ ~!@<>:;.,#?&\/\"\'\/]*$/',
                // path, matches 1-128 alphanumerical and -+=%_~!@.,#* characters
                'path' => '/^(?=^.{1,128}$)([A-Za-z0-9]|[-+=%_~!@.,#*:;()\/])*$/',
                // strong password, must contain 8-50 characters, uppercase, lowercase, special char, number
                'strong_password' => '/^(?=^.{8,50}$)((?=.*\d)(?=.*[A-Z])(?=.*[-+=%_~!@.,#*@])(?=.*[a-z])).*$/',
                // words separated; letters and comma sign allowed if followed by one or more letters
                'words' => '/^(?=^.{2,100}$)[A-Za-z]|[���]+(?:[,][A-Za-z]|[���]+)*$/',
                // name,  \p{L} # Unicode letter, or \p{Mn} # Unicode accents, or \p{Pd} # Unicode hyphens, or \' # single quote, or \x{2019} # single quote (alternative) ]+ # one or more times \s # any kind of space
                'name' => '~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?)+$~u',
                // postnummer 123 45
                'postnummer' => '/^\d\d\d\d\d$/',
                // personnummer
                'personnummer' => '/^\d{2}(0[1-9]|10|11|12)((0[1-9]|1\d|2\d|30|31))-\d{4}$/',
                // personnummer with a "P", "T", or "F" instead of the first of the four last numbers
                'personnummerPTF' => '/^\d{2}(0[1-9]|10|11|12)((0[1-9]|1\d|2\d|30|31))-[0-9PTF][\d]{3}$/',
                // alpha
                'alpha' => '/^[a-zA-Z]+$/',
                // numerical
                'numerical' => '/^[\d]+$/',
                // milliseconds
                'milliseconds' => '/^100|200|300|400|500|600|700|800|900|(1|2|3|4|5|6|7|8|9|10|15|20|25|30|35|40|45|50|55|60|90|120|180|240|300)(000)$/',
                // alphanumerical
                'alphanumerical' => '/^[a-zA-Z0-9]+$/',
                // ratio
                'ratio' => '/^(1:1|4:3|16:9|21:9)$/',
                // email
                'email' => '/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU',
                // seven digits
                'sevendigits' => '/^(\d{7})$/',
                // 0-9
                '0-9' => '/^[0-9]$/',
                // 1-20
                '1-20' => '/^([0-9]|1\d|20)$/',
                // hex value
                'hex' => '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/',
                // date value YYYY-MM-DD
                'date' => '/^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/',
                // ip
                'ip' => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',
                // url
                'url' => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/',
                // url_query
                'url_query' => '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/',
                // php
                'php' => '/^<\?[php]*([^\?>]*)\?>$/',
                // bool
                'boolean' => '/^(true|false|1|0)$/',
                // any or all
                'any-all' => '/^(any|all|1|0)$/',
                // any or all
                'characters' => '/[\p{L}]+/u',
                //comma separatednumbers
                'comma_separated_numbers' => '/^[\d]+(,[\d]+)*$/',
                //any
                'any' => '/^([\s\S])*$/'
            );
            if (filter_var($string, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $regex_string[$reg])))) {
                return true;
            }
        }
    }
}

/**
 * generate a random string using echo rand_string(8);
 * @param $length
 * @return string
 */
function rand_string($length)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * generate random_password(8)
 * @param $length
 * @return string
 */
function random_password($length)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&()_+";
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $special = '!@#$%&()_+';
    $s = substr(str_shuffle($upper), 0, 2) . substr(str_shuffle($lower), 0, 2) . substr(str_shuffle($numbers), 0, 2) . substr(str_shuffle($special), 0, 2);
    // more than 8 characters
    $extra = $length > 8 ? substr(str_shuffle($chars), 0, $length - 8) : '';
    $s .= $extra;
    return str_shuffle($s);
}

/**
 * @param $candidate
 * @param $length
 * @return bool
 */
function valid_password($candidate, $length)
{
    if (strlen($candidate) < $length) return false;

    $r1 = '/[A-Z]/';  // upper
    $r2 = '/[a-z]/';  // lower
    $r3 = '/[0-9]/';  // no alphanumeric
    $r4 = '/\W+/';  //numbers

    $a = preg_match($r1, $candidate);
    $b = preg_match($r2, $candidate);
    $c = preg_match($r3, $candidate);
    $d = preg_match($r4, $candidate);

    if ($a + $b + $c + $d >= 3) {
        return true;
    }
}

/**
 * @param $description
 * @param $data
 * @return string
 */
function describe($description, $data)
{
    if (isset($description) && isset($data)) {
        $d = $description . ': ' . substr(htmlentities($data), 0, 75);
        return $d;
    }
}


/**
 * @param $array
 * @param $editor
 * @return bool|mixed
 */
function get_editor_settings($array, $editor)
{
    if (!is_array($array)) return false;
    foreach ($array as $keys => $values) {
        if ($keys == $editor) {
            return $values;
            break;
        }
    }
}


/**
 * include custom file, fallback -> default
 * @param $inc_file
 * @param $arr
 * @param $languages
 */
function include_once_customfile($inc_file, $arr, $languages)
{
    $f = str_replace('includes', '../content/includes', $inc_file);
    $file = is_file($f) ? $f : $inc_file;
    if (is_file($file)) {
        include_once($file);
    }
}


/**
 * include custom css, declared as array in config
 * @param $array
 * @param $input
 */
function get_css_class($array, $input)
{
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $s = ($key == $input) ? " selected=selected" : null;
            echo '<option value="' . $key . '"' . $s . '>' . $value . '</option>';
        }
    }
}


/**
 * highlight keywords in content
 * @param $content
 * @param array $keywords
 * @return mixed
 */
function highlight($content, array $keywords)
{
    foreach ($keywords as $keyword) {
        $content = preg_replace("/$keyword/i", "<span class='search-word'>\$0</span>", $content);
    }
    return $content;
}


/**
 * set utc datetime from datetime value: such as UTC_TIMESTAMP() (MySQL) and gmdate (PHP)
 * $utc = datetime, $dtz = DateTimeZone (Europe/Stockholm | Europe/London), $format = string like 'Y-m-d H:i:s' (http://www.php.net/manual/en/function.date.php)
 * returns string '----' if date not validates (like MySQL default value 0000-00-00 00:00:00)
 *
 * @param $utc
 * @param $dtz
 * @param $format
 * @return string
 */
function utc_dtz($utc, $dtz, $format)
{
    if (is_string(($utc)) && is_string(($dtz)) && is_string(($format))) {
        if (isValidDateTime($utc)) {
            $dt = new DateTime($utc);
            $dtz = new DateTimeZone($dtz);
            $dt->setTimezone($dtz);
            return $dt->format($format);
        } else {
            return '----';
        }
    }
}


/**
 * get adjusted datetime from datetime value: such as UTC_TIMESTAMP() (MySQL) and gmdate (PHP)
 * $utc = datetime, $dtz = DateTimeZone (Europe/Stockholm | Europe/London), $format = string like 'Y-m-d H:i:s' (http://www.php.net/manual/en/function.date.php)
 * returns string '----' if date not validates (like MySQL default value 0000-00-00 00:00:00)
 *
 * @param $utc
 * @param $dtz
 * @param $format
 * @return string
 */
function get_utc_dtz($utc, $dtz, $format)
{
    if (is_string(($utc)) && is_string(($dtz)) && is_string(($format))) {
        if (isValidDateTime($utc)) {
            $dt = new DateTime($utc, new DateTimeZone($dtz));
            $offset = $dt->getOffset();
            $dt->modify('+' . intval($offset) . 'seconds');
            return $dt->format($format);
        }
    }
}


/**
 * get input explained
 * returns explained value if match in given array $explain
 *
 * @param $input
 * @param $explain
 * @return int|mixed|string
 */
function get_value_explained($input, $explain)
{
    if (is_array($explain)) {
        foreach ($explain as $key => $value) {
            if ($value == $input) {
                return $key;
            }
        }
    } else {
        return null;
    }
}


/**
 * form; toggle and activate submit button
 * arguments - language dependent; $s_toggle = show/hide, $s_toggle_this = toggle what, $btn_value = submit btn value
 * arguments - script; $btn_name = submit name; $i = unique id
 *
 * @param $s_toggle
 * @param $s_toggle_this
 * @param $btn_value
 * @param $btn_name
 * @param $i
 */
function show_submit_button($s_toggle, $s_toggle_this, $btn_value, $btn_name, $i)
{
    echo '<a href=javascript:toggleLayer("id' . $i . '");>' . $s_toggle . '</a> ' . $s_toggle_this;
    echo '<div class="hiddenbuttonlayer" id="id' . $i . '">';
    echo '<input type="submit" name="' . $btn_name . '" id="' . $btn_name . '" title="Delete checked" value="' . $btn_value . '">';
    echo '</div>';
}


/**
 *
 * function get_tab_menu()
 *
 * params
 * $s_tab_url = $_SERVER["PHP_SELF"] .'?a=b // $_SERVER["PHP_SELF"] .'?a=b&tab_tool='. $_GET['tab_tool']
 * $a_tabs = array("tab1","tab2")
 * $a_tab_descriptions = array("tab1 desc","tab2 desc")
 * $s_tab_query = "tab"
 * $s_tab_information = "#raquo;"
 * $s_bookmark anchor, adds url anchor = "#123" -> #123
 *
 * @param $s_tab_url
 * @param $a_tabs
 * @param $a_tab_descriptions
 * @param $s_tab_query
 * @param $s_tab_information
 * @param $s_bookmark
 */
function get_tab_menu($s_tab_url, $a_tabs, $a_tab_descriptions, $s_tab_query, $s_tab_information, $s_bookmark)
{
    echo '<span class="tabline">';
    echo '<ul class="menu">';
    echo '<li class="menu_description"><span>' . $s_tab_information . '</span></li>';
    foreach ($a_tabs as $key => $value) {

        if ($value == "") {
            $li = '<li class="menu_description">&nbsp;&nbsp;&nbsp;</li>';
            echo $li;
        } else {
            if (isset($_GET[$s_tab_query])) {
                if (($_GET[$s_tab_query]) == $a_tabs[$key]) {
                    echo '<li class="menu_active">';
                    echo '<a class="menu_active" ';
                } else {
                    echo '<li class="menu">';
                    echo '<a class="menu" ';
                }
            } else {
                echo '<li class="menu">';
                echo '<a class="menu" ';
            }

            // check for "?" in order to build querystring
            if (strpos($s_tab_url, "?") === false) {
                echo 'href="' . $s_tab_url . '?' . $s_tab_query . '=' . $a_tabs[$key] . $s_bookmark . '">';
            } else {
                echo 'href="' . $s_tab_url . '&' . $s_tab_query . '=' . $a_tabs[$key] . $s_bookmark . '">';
            }
            echo $a_tab_descriptions[$key];
            echo '</a></li>';
        }

    }
    echo '</ul>';
    echo '</span>';
}


/**
 *
 * tab menu jquery_ui_look_alike()
 * function get_tab_menu_jquery_ui_look_alike
 * params;
 * $s_tab_url = $_SERVER["PHP_SELF"] .'?a=b // $_SERVER["PHP_SELF"] .'?a=b&tab_tool='. $_GET['tab_tool']
 * $a_tabs = array("tab1","tab2")
 * $a_tab_descriptions = array("tab1 desc","tab2 desc")
 * $s_tab_query = "tab"
 * $s_tab_information = "#raquo;"
 * $s_bookmark anchor, adds url anchor = "#123" -> #123
 * $ul_css, style ul tag
 * $a_css, style a tag
 *
 * @param $s_tab_url
 * @param $a_tabs
 * @param $a_tab_descriptions
 * @param $s_tab_query
 * @param $s_tab_information
 * @param $s_bookmark
 * @param $ui_ul_add_class
 * @param $ui_a_add_class
 */
function get_tab_menu_jquery_ui_look_alike($s_tab_url, $a_tabs, $a_tab_descriptions, $s_tab_query, $s_tab_information, $s_bookmark, $ui_ul_add_class, $ui_a_add_class)
{

    $str = "\n\t" . '<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all ' . $ui_ul_add_class . '">';
    foreach ($a_tabs as $key => $value) {

        if (isset($_GET[$s_tab_query])) {
            if (($_GET[$s_tab_query]) == $a_tabs[$key]) {
                $str .= "\n\t\t" . '<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active">';
                $str .= '<a class="ui-tabs-anchor" ';
            } else {
                $str .= "\n\t\t" . '<li class="ui-state-default ui-corner-top">';
                $str .= '<a class="ui-tabs-anchor ' . $ui_a_add_class . '"';
            }
        } else {
            $str .= "\n\t\t" . '<li class="ui-state-default ui-corner-top">';
            $str .= '<a class="ui-tabs-anchor ' . $ui_a_add_class . '"';
        }

        // check for "?" in order to build querystring
        if (strpos($s_tab_url, "?") === false) {
            $str .= ' href="' . $s_tab_url . '?' . $s_tab_query . '=' . $a_tabs[$key] . $s_bookmark . '">';
        } else {
            $str .= ' href="' . $s_tab_url . '&amp;' . $s_tab_query . '=' . $a_tabs[$key] . $s_bookmark . '">';
        }
        $str .= $a_tab_descriptions[$key];
        $str .= '</a></li>';

    }
    $str .= "\n\t" . '</ul>' . "\n";
    echo $str;

}


/**
 * gshow lorem ipsum, print paragraphs (up to 10)
 * @param $i_paragraphs
 * @return string
 */
function show_lorem_ipsum($i_paragraphs)
{
    $a[0] = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam laoreet sapien id purus. Pellentesque suscipit imperdiet sapien. </p>';
    $a[1] = '<p>Suspendisse potenti. In hac habitasse platea dictumst. Mauris ullamcorper semper ligula. Morbi purus. Praesent elementum egestas lacus. </p>';
    $a[2] = '<p>Duis vestibulum iaculis turpis. Nunc blandit. Donec laoreet, risus in sodales accumsan, odio risus mollis eros, et dapibus ipsum arcu quis felis. Curabitur iaculis interdum pede. Duis nisi neque, consectetur id, mattis a, euismod vitae, odio. Suspendisse lectus. Etiam dignissim dictum libero. Aenean enim purus, lobortis sed, blandit eget, tincidunt at, quam. Nam id est. Vivamus viverra rutrum lacus. Aenean vulputate, sapien et sodales semper, pede arcu eleifend mauris, ut eleifend elit tellus vitae mauris. Ut sit amet justo quis sapien aliquet elementum. Donec eu risus sed leo egestas fermentum. In aliquet malesuada risus. Mauris vel quam consectetur arcu mattis molestie. Mauris urna.</p>';
    $a[3] = '<p>Nunc vestibulum purus sit amet lectus. Cras iaculis, metus non ullamcorper interdum, erat ante adipiscing lacus, consectetur posuere metus lectus sit amet neque. Nulla sit amet purus. Sed dapibus accumsan felis. Praesent eleifend neque nec nunc. Donec fermentum lorem ac lacus. Nullam tincidunt ipsum a nulla. Phasellus metus lectus, blandit vel, aliquet et, ultrices ut, urna. Sed vel libero in est sagittis egestas. Donec sem. Maecenas libero nibh, porttitor a, tincidunt sit amet, condimentum quis, nulla. Sed condimentum, sapien quis tincidunt mattis, ipsum lorem pellentesque libero, eu viverra lacus dui quis ligula. Nunc dignissim, mi vel molestie congue, lorem erat consequat libero, sit amet luctus turpis mauris et risus. Vivamus fringilla massa et turpis.</p>';
    $a[4] = '<p>Fusce varius, ligula a rutrum consectetur, nibh libero tempus elit, in dictum orci nulla id odio. Mauris leo mi, accumsan at, eleifend non, egestas quis, ante. Curabitur vitae tellus. Duis tincidunt nulla. Phasellus aliquet semper erat. Aenean enim turpis, porta ac, tincidunt at, lacinia sed, arcu. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In odio nisl, sollicitudin sit amet, scelerisque quis, vestibulum eget, est. Nunc imperdiet leo ullamcorper nulla. Nam ligula neque, scelerisque sed, molestie ut, aliquam sed, turpis. Aenean a diam. Etiam lacinia. Donec volutpat tortor in nunc. Morbi nisi.</p>';
    $a[5] = '<p>Proin vitae diam in tellus volutpat pretium. Fusce sapien dolor, pulvinar in, congue non, tristique vitae, lacus. Curabitur molestie augue aliquet libero. Vivamus non urna. Proin at ipsum eu massa vulputate fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin et enim id eros molestie consectetur. Aliquam molestie interdum mauris. Nulla eu libero sit amet enim ullamcorper ultrices. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc vitae mi. Morbi vitae quam in risus dapibus hendrerit. Maecenas sed arcu non velit porta ultrices. Quisque vestibulum aliquam dui. Aliquam et velit ut erat sodales elementum. Duis consequat sodales enim. Donec luctus eleifend neque. Curabitur id justo sit amet lorem tempor aliquam.</p>';
    $a[6] = '<p>Nunc luctus adipiscing pede. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer pellentesque libero in dolor. Donec eleifend ullamcorper nunc. Praesent venenatis. Fusce congue molestie elit. Curabitur eget sem in nibh accumsan vehicula. Etiam a mauris nec est consectetur mollis. Nam sodales. Nulla facilisi.</p>';
    $a[7] = '<p>Donec metus. Nulla erat. Morbi nibh nisi, fringilla sed, eleifend et, tempor ac, risus. Sed sit amet neque. Duis et augue. Nullam pulvinar, libero eget luctus dapibus, turpis diam feugiat neque, vestibulum eleifend justo mi ac mi. Suspendisse potenti. Suspendisse nec turpis quis massa dictum pretium. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse varius, turpis sit amet imperdiet tempor, velit eros malesuada purus, a tincidunt eros tellus ut mauris.</p>';
    $a[8] = '<p>Etiam ligula nisi, iaculis at, mollis non, sodales sit amet, neque. Praesent nulla. Donec lobortis imperdiet ligula. Aenean lorem. Nulla luctus. Aenean ac leo. Proin imperdiet suscipit dolor. Duis dapibus lectus viverra orci. Fusce et dolor. Morbi semper facilisis elit. Fusce sagittis. Integer cursus viverra nibh. Etiam vitae magna vitae nunc condimentum commodo. Fusce congue quam vitae pede dapibus semper. Donec fermentum, leo id faucibus malesuada, tellus metus pellentesque dui, eu congue mi nulla a lectus. Sed tellus nunc, dapibus a, tempor et, mattis quis, augue.</p>';
    $a[9] = '<p>Praesent magna tortor, luctus ut, malesuada ac, consectetur eu, leo. Nunc pede. Sed quis lacus. Proin sit amet nunc. Fusce rutrum venenatis felis. Pellentesque nisi felis, rhoncus ac, molestie ut, vestibulum id, justo. In non arcu. Aliquam at urna eu purus aliquet tempor. Integer aliquam semper lacus. Donec porta. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent sed lacus at justo elementum congue. Morbi ultricies fermentum lorem. Sed vitae neque. Vivamus feugiat odio id diam. Duis magna. Mauris ut diam. Nam nec mauris.</p>';

    $html = '';
    for ($i = 1; $i <= count($a); $i++) {
        $html .= $a[$i - 1];
        if ($i_paragraphs == $i) {
            break;
        }
    }
    return $html;
}

/**
 * @param array $arr_var_to_exclude
 * @return string
 */
function exclude_queries($arr_var_to_exclude = array())
{
    $path = $_GET;
    $newpath = '';
    foreach ($path as $key => $value) {
        if (!in_array($key, $arr_var_to_exclude)) {
            $newpath .= $key . "=" . $value;
            $newpath .= "&amp;";
        }
    }
    // remove last &amp; 5 characters
    return substr($newpath, 0, -5);
}

/**
 * @param $val
 */
function print_r2($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

/**
 * @param $val
 */
function print_r3($text, $val)
{
    echo '<div class="debug">' . $text . '</div>';    
    echo '<pre class="debug">';
    print_r($val);
    echo '</pre>';
    echo '<hr>';
}


/**
 * function to check if a value exists in a multidimensional array, returns key integer
 * use is_int(recursiveArraySearch($array, '$value'))
 *
 * @param $haystack
 * @param $needle
 * @param null $index
 * @return bool|mixed
 */
function recursiveArraySearch($haystack, $needle, $index = null)
{
    $aIt = new RecursiveArrayIterator($haystack);
    $it = new RecursiveIteratorIterator($aIt);
    while ($it->valid()) {
        if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) {
            return $aIt->key();
            break;
        }
        $it->next();
    }
    return false;
}


/**
 * function to check roles access rights in application
 * $s = role in CMS: superadministrator, administrator, editor, author, contributor, user
 *
 * @param $s
 * @return int
 */
function get_role_CMS($s)
{
    // presume no rights
    $int = 0;

    if (isset($_SESSION['role_CMS'])) {
        switch ($s) {
            case 'superadministrator':
                $int = ($_SESSION['role_CMS'] >= 6) ? 1 : 0;
                break;
            case 'administrator':
                $int = ($_SESSION['role_CMS'] >= 5) ? 1 : 0;
                break;
            case 'editor':
                $int = ($_SESSION['role_CMS'] >= 4) ? 1 : 0;
                break;
            case 'author':
                $int = ($_SESSION['role_CMS'] >= 3) ? 1 : 0;
                break;
            case 'contributor':
                $int = ($_SESSION['role_CMS'] >= 2) ? 1 : 0;
                break;
            case 'user':
                $int = ($_SESSION['role_CMS'] >= 1) ? 1 : 0;
                break;
        }
    }
    return $int;
}


/**
 * function to check roles access rights in application
 * $s = role in LMS: administrator, teacher, tutor, student
 *
 * @param $s
 * @return int
 */
function get_role_LMS($s)
{
    // presume no rights
    $int = 0;
    if (isset($_SESSION['role_LMS'])) {
        // check if role match - hierarchy matters
        switch ($s) {
            case 'administrator':
                $int = ($_SESSION['role_CMS'] >= 4) ? 1 : 0;
                break;
            case 'teacher':
                $int = ($_SESSION['role_CMS'] >= 3) ? 1 : 0;
                break;
            case 'tutor':
                $int = ($_SESSION['role_CMS'] >= 2) ? 1 : 0;
                break;
            case 'student':
                $int = ($_SESSION['role_CMS'] >= 1) ? 1 : 0;
                break;
        }
    }
    return $int;
}


/**
 * function to check rights at group level; memberships and required memberships
 * param $rights_to_check as 'read', 'edit', 'create'
 *
 * @param $rights_to_check
 * @param $memberships
 * @param $required_memberships
 * @return bool
 */
function get_membership_rights($rights_to_check, $memberships, $required_memberships)
{
    $r = false;
    if (is_array($memberships) && is_array($required_memberships)) {
        foreach ($memberships as $membership) {
            foreach ($required_memberships as $required_membership) {
                if ($membership['groups_id'] == $required_membership['groups_id']) {
                    if ($required_membership[$rights_to_check] == 1) {
                        $r = true;
                        return $r;
                        break 2;
                    }
                }
            }
        }
    }
}


// function to show select list from given array such as
// array('role_guest' => 'Guest', 'role_user' => 'User')
// array(0 => 'Red', 1 => 'Green', 2 => 'Blue')
// array results from pdo...
// ...convert multidimensional array to single array
// using function pdo2array($result) ->  getSelect(pdo2array($a),...
// $id = id of select
// $select_this = explain what to select
// $request = use $_GET or $_POST
// $session_id = use active session to pre-select value
// $onchange = true/false; true: this.form.submit()
// $multiple = true / false
// $size = integer
// $css = style select list
/**
 * @param $a
 * @param $id
 * @param $select_this
 * @param $request
 * @param $session_id
 * @param $onchange
 * @param $multiple
 * @param $size
 * @param $css
 */
function getSelect($a, $id, $select_this, $request, $session_id, $onchange, $multiple, $size, $css)
{
    if (is_array($a)) {
        echo '<select name="' . $id . '" id="' . $id . '" class="' . $css . '" ';
        if ($size > 1) {
            echo ' size="' . $size . '" ';
        }
        if ($multiple == true) {
            echo 'multiple';
        }
        if ($onchange == true) {
            echo 'onchange="this.form.submit();"';
        }
        echo '>';
        echo '<option value="0">';
        if (isset($select_this)) {
            echo $select_this;
        } else {
            echo 'choose &raquo;';
        }
        echo '</option>';
        foreach ($a as $key => $value) {
            echo '<option value=' . $key;
            $request = strtoupper($request);
            $req = ($request == 'GET') ? $_GET : $_POST;
            if (isset($req[$id])) {
                if ($req[$id] == $key) {
                    echo ' selected';
                }
            } else {
                // check active session_id value
                if (isset($session_id)) {
                    if ($session_id == $key) {
                        echo ' selected';
                    }
                }
            }
            echo '>' . $value . '</option>';
        }
        echo '</select>';
    }
}


// function to show select list from given array such as
// array('role_guest' => 'Guest', 'role_user' => 'User')
// array(0 => 'Red', 1 => 'Green', 2 => 'Blue')
// array results from pdo...
// ...convert multidimensional array to single array
// using function pdo2array($result) ->  getSelect(pdo2array($a),...
// $checkbox = show checkbox
// $id = form checkbox id
// $request = use $_GET or $_POST
/**
 * @param $a
 * @param $checkbox
 * @param $id
 * @param null $request
 */
function getList($a, $checkbox, $id, $request = null)
{
    if (is_array($a)) {
        foreach ($a as $key => $value) {
            echo '<div id="' . $key . '" class="column_items">';
            if ($checkbox == true) {
                echo '<input type="checkbox" name="' . $id . '" id="' . $id . '" value="' . $key . '"';
                echo '>';
            }
            echo $value;
            echo '</div>';
        }
    }
}


/**
 * sort multidimensional array by key
 * http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
 *
 * @param $array
 * @param $key
 */
function aasort(&$array, $key)
{
    $sorter = array();
    $ret = array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii] = $va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii] = $array[$ii];
    }
    $array = $ret;
}


/**
 * convert pdo multidimensional array to single array
 *
 * @param $result
 * @return array
 */
function pdo2array($result)
{
    $new = array();
    foreach ($result as $val) {
        $keys = array_keys($val);
        $new[$val[$keys[0]]] = $val[$keys[1]];
    }
    return $new;
}

/**
 * flatten a multidimensional array to one dimension
 *
 * @param array $a
 * @return array
 */
function flatt_array(array $a)
{
    $ret_array = array();
    foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($a)) as $k => $v) {
        $ret_array[] = $v;
    }
    return $ret_array;
}

/**
 * flatten a multidimensional array to one dimension
 *
 * @param array $a
 * @return array
 */
function array_flatten(array $a)
{
    $ret_array = array();
    foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($a)) as $k => $v) {
        $ret_array[] = $v;
    }
    return $ret_array;
}



/**
 * @param $result
 * @return string
 */
function reply($result)
{
    // show reply message - check if under development define('LIVE', false)...
    $reply = ($result == true) ? 'saved' : '...';
    return $reply;
}

/**
 * @param $option
 */
function get_timezone_identifiers_list_options($option)
{
    $timezone_identifiers = timezone_identifiers_list();
    $str = '';
    for ($i = 0; $i < count($timezone_identifiers); $i++) {
        $str .= '<option value="' . $timezone_identifiers[$i] . '"';
        $str .= $timezone_identifiers[$i] == $option ? ' selected' : null;
        $str .= '>' . $timezone_identifiers[$i] . '</option>';
    }
    echo $str;
}

/**
 * @param $encoded
 * @param $number
 * @return null|string
 */
function enc($encoded, $number)
{
    $decoded = null;
    for ($i = 0; $i < strlen($encoded); $i++) {
        $b = ord($encoded[$i]);
        $a = $b ^ $number;
        $decoded .= chr($a);
    }
    return $decoded;
}

/**
 * @param $string
 * @return array
 */
function getCodedString($string)
{
    $encoded = substr($string, 0, -2);
    $number = substr($string, -2);
    return array($encoded, $number);
}

?>