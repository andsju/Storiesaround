<?php

/* enable sessions
-------------------------------------------------- */
session_start();


/* prevent direct access to include files 
-------------------------------------------------- */
define('VALID_INCL', true);


/* generate an anti-CSRF (Cross-Site Request Forgery) token
-------------------------------------------------- */
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = sha1(uniqid(mt_rand(), TRUE));
}


/* 
-------------------------------------------------- */
if (!isset($_SESSION['redirected'])) {
    $_SESSION['redirected'] = 0;
}


/* paths - define constants
-------------------------------------------------- */

// CMS_ABSPATH -> parent directory
// replace any '\' width '/' (windows / linux filesystem
$dirname = str_replace('\\', '/', dirname(__FILE__));
$cms_path = substr($dirname, 0, strripos($dirname, '/cms/'));
define('CMS_ABSPATH', substr($dirname, 0, strripos($dirname, '/cms/')));

// CMS_DIR
define('CMS_DIR', substr(CMS_ABSPATH, strlen($_SERVER['DOCUMENT_ROOT'])));

// set constant
$_SESSION['CMS_DIR'] = CMS_DIR;

// CMS_PATH
define('CMS_PATH', $_SERVER['SERVER_NAME'] . CMS_DIR);

// CMS_URL
define('CMS_URL', "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . CMS_PATH);

// set session same as constant (used in installation and javascript)
$_SESSION['CMS_URL'] = CMS_URL;

// CMS ROOT
define('ROOT', realpath(dirname(__FILE__)) . '/');


/* CMS
-------------------------------------------------- */
define('CMS', 'Storiesaround CMS');
define('CMS_HOME', 'http://storiesaround.com');


/* include version file
-------------------------------------------------- */
require_once CMS_ABSPATH . '/cms/includes/inc.version.php';


/* handle PDOException
-------------------------------------------------- */
function handle_pdo_exception($script, $e)
{
    $file = CMS_ABSPATH . '/log/pdo_exception.txt';

    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $agent = $_SERVER['HTTP_USER_AGENT'];

    $exception = $e->getMessage();

    $contents = $date . ', ' . $ip . ', ' . $agent . ', ' . $script . ', ' . $exception;
    $contents .= PHP_EOL;

    file_put_contents($file, $contents, FILE_APPEND | LOCK_EX);

    if (!LIVE) {
        echo '<p>!LIVE-------------------------</p>';
        print_r($e);
        echo '<p>-------------------------!LIVE</p>';
    } else {
        db_connect();
    }
}


/* handle error
-------------------------------------------------- */
function my_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{

    $file = CMS_ABSPATH . '/log/php_error.txt';
    // new message
    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $message = "[" . $errno . "] An error occurred in script " . $errfile . " on line " . $errline;
    $contents = $date . ', ' . $ip . ', ' . $agent . ', ' . $message .', '. $errstr ."\r\n";

    file_put_contents($file, $contents, FILE_APPEND | LOCK_EX);

    if (!LIVE) {
        echo '<p>' . $message . '</p>';
    } else {
        // only print an error message if the error isn't a notice:
        if ($errno != E_NOTICE) {
            echo '<p>A system error occurred. We apologize for the inconvenience.</p>';
        }
    }
}

// use error handler
set_error_handler('my_error_handler');

/* include default language file
-------------------------------------------------- */
if (isset($_SESSION['language']) && strlen($_SESSION['language']) > 0) {
    $language = ($_SESSION['language']);
} else {
    $language = isset($_SESSION['site_language']) ? $_SESSION['site_language'] : 'english';
}

if (is_file(CMS_ABSPATH . '/cms/languages/' . $language . '.php')) {
    require_once CMS_ABSPATH . '/cms/languages/' . $language . '.php';
} else {
    require_once CMS_ABSPATH . '/cms/languages/english.php';
}

/* include important file 
-------------------------------------------------- */
require_once CMS_ABSPATH . '/sys/inc.config.php';
require_once 'inc.functions.php';
require_once 'inc.functions_pages.php';


/* define autoload function for classes 
-------------------------------------------------- */

// classes
function autoload_default($class_name)
{
    $file = CMS_ABSPATH . '/cms/classes/' . strtolower($class_name) . '.php';
    if (file_exists($file)) {
        include($file);
    }
}

// widgets
function autoload_widgets($class_name)
{
    $file = CMS_ABSPATH . '/cms/widgets/' . $class_name . '.class.php';
    if (file_exists($file)) {
        include($file);
    }
}

// plugins
function autoload_plugins($class_name)
{
    $file = CMS_ABSPATH . '/content/plugins/' . $class_name . '.class.php';
    if (file_exists($file)) {
        include($file);
    }
}

// register classes
spl_autoload_register('autoload_default');
spl_autoload_register('autoload_widgets');
spl_autoload_register('autoload_plugins');

// include database configuration
require_once CMS_ABSPATH . '/sys/inc.db.php';

// detect mobil device - choose mobile or classic layout
if (!isset($_SESSION['layoutType']) || $_SESSION['layoutType'] == "") {
    $detect = new Mobile_Detect();
    $_SESSION['layoutType'] = $detect->isMobile() ? "mobile" : "classic";
}

// load sessions from site class
if (!isset($_SESSION['site_id'])) {

    $z = new Site();
    $site = $z->getSite();
    if ($site) {

        // set site session variables
        // exclude site_smtp_server, site_smtp_port, site_smtp_username, site_smtp_password, site_smtp_authentication, utc_modified
        $excl = array('site_smtp_server', 'site_smtp_port', 'site_smtp_username', 'site_smtp_password', 'site_smtp_authentication', 'utc_modified', 'site_maintenance_message', 'site_error_mode', 'site_history_max');
        foreach ($site as $key => $value) {
            if (!in_array($key, $excl)) {
                if (strlen($value) > 0) {
                    $_SESSION[$key] = $value;
                }
            }
        }
        // $_SESSION['site_domain'] must be set to avoid error logs
        if (!isset($_SESSION['site_domain'])) {
            $_SESSION['site_domain'] = '';
        }

        // default html lang attribute is none
        $_SESSION['site_lang'] = '';
        $site = null;
        // set user agent session variable
        $_SESSION['HTTP_USER_AGENT'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
}


/* initiate variable $lang - can be changed from a page
-------------------------------------------------- */
$lang = isset($_SESSION['site_lang']) ? $_SESSION['site_lang'] : '';


/* datetime zone 
-------------------------------------------------- */
$dtz = isset($_SESSION['site_timezone']) ? $_SESSION['site_timezone'] : 'Europe/Stockholm';


/* function PDO database connection, constants from configuration file
-------------------------------------------------- */
function db_connect()
{
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    try {
        $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ($dbh);
    } catch (PDOException $e) {
        echo '<p>Database connection failed...</p>';
        die;
    }
}

?>